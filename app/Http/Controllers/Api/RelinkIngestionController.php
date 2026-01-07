<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalDevice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RelinkIngestionController extends Controller
{
    private function assertAuthorized(Request $request): void
    {
        $token = config('ingestion.relink.token');

        $authHeader = $request->header('Authorization', '');
        $given = null;

        // Expect: "Bearer <token>"
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $m)) {
            $given = trim($m[1]);
        }

        if (!$token || !$given || !hash_equals($token, $given)) {
            abort(401, 'Unauthorized');
        }
    }

    private function importerUserId(): int
    {
        $email = config('ingestion.relink.importer_email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            abort(500, "Importer user not found: {$email}. Run: php artisan relink:create-importer");
        }

        return $user->id;
    }

    /**
     * POST /import/relink/medical-devices
     * Body:
     * {
     *   "run_id": "uuid-or-date-string",
     *   "items": [ ... ]
     * }
     */
    public function upsertBatch(Request $request)
    {
        $this->assertAuthorized($request);

        $validated = $request->validate([
            'run_id' => 'required|string|max:64',

            'items' => 'required|array|min:1|max:500',
            'items.*.external_id' => 'required|string|max:255',
            'items.*.source_url' => 'required|url|max:2048',
            'items.*.lastmod' => 'nullable|string|max:64',

            'items.*.name' => 'required|string|max:255',
            'items.*.brand' => 'nullable|string|max:255',
            'items.*.description_html' => 'required|string',
            'items.*.condition' => 'required|in:new,used,refurbished',

            'items.*.price' => 'required|numeric|min:0',
            'items.*.price_new' => 'nullable|numeric|min:0',

            'items.*.quantity' => 'required|integer|min:0',
            'items.*.shipping_available' => 'required|boolean',

            'items.*.main_category' => 'nullable|string|max:255',
            'items.*.aux_category' => 'nullable|string|max:255',

            'items.*.city' => 'nullable|string|max:255',
            'items.*.state' => 'nullable|string|max:255',
            'items.*.country' => 'nullable|string|max:255',

            'items.*.image_url' => 'nullable|string|max:2048',
        ]);

        $runId = $validated['run_id'];

        $source = config('ingestion.relink.source', 'relink');
        $importerId = $this->importerUserId();

        $now = now();
        $items = $validated['items'];

        $created = 0;
        $updated = 0;

        DB::transaction(function () use (
            $items,
            $source,
            $importerId,
            $now,
            $runId,
            &$created,
            &$updated
        ) {
            foreach ($items as $item) {
                $externalId = (string) $item['external_id'];

                $lastmod = null;
                if (!empty($item['lastmod'])) {
                    try {
                        $lastmod = Carbon::parse($item['lastmod']);
                    } catch (\Throwable $e) {
                        $lastmod = null;
                    }
                }

                // Backward compatible "location"
                $locationLegacy = trim(collect([
                    $item['city'] ?? null,
                    $item['state'] ?? null,
                    $item['country'] ?? null,
                ])->filter()->implode(' '));

                // NOTE: image column can store an external URL; your blade now supports it.
                $image = $item['image_url'] ?? null;

                $payload = [
                    'user_id' => $importerId,

                    'name' => $item['name'],
                    'brand' => $item['brand'] ?? null,
                    'description' => $item['description_html'],

                    'price' => $item['price'],
                    'price_new' => $item['price_new'] ?? null,

                    'condition' => $item['condition'],
                    'quantity' => (int) $item['quantity'],

                    'shipping_available' => (bool) $item['shipping_available'],

                    'main_category' => $item['main_category'] ?? null,
                    'aux_category' => $item['aux_category'] ?? null,

                    'city' => $item['city'] ?? null,
                    'state' => $item['state'] ?? null,
                    'country' => $item['country'] ?? null,
                    'location' => $locationLegacy,

                    'image' => $image,

                    // External sync fields
                    'source' => $source,
                    'source_external_id' => $externalId,
                    'source_url' => $item['source_url'],
                    'source_lastmod' => $lastmod,
                    'synced_at' => $now,
                    'is_active' => true,

                    // ✅ critical for "finalize run" strategy
                    'last_seen_run_id' => $runId,
                ];

                $existing = MedicalDevice::where('source', $source)
                    ->where('source_external_id', $externalId)
                    ->first();

                if ($existing) {
                    $existing->update($payload);
                    $updated++;
                } else {
                    MedicalDevice::create($payload);
                    $created++;
                }
            }
        });

        return response()->json([
            'ok' => true,
            'run_id' => $runId,
            'created' => $created,
            'updated' => $updated,
        ]);
    }

    /**
     * POST /import/relink/finalize
     * Body:
     * {
     *   "run_id": "uuid-or-date-string"
     * }
     *
     * Deactivate any relink items not seen in this run.
     */
    public function finalize(Request $request)
    {
        $this->assertAuthorized($request);

        $validated = $request->validate([
            'run_id' => 'required|string|max:64',
        ]);

        $runId = $validated['run_id'];
        $source = config('ingestion.relink.source', 'relink');

        $deactivated = MedicalDevice::where('source', $source)
            ->where('is_active', true)
            ->where(function ($q) use ($runId) {
                $q->whereNull('last_seen_run_id')
                  ->orWhere('last_seen_run_id', '!=', $runId);
            })
            ->update([
                'is_active' => false,
                'synced_at' => now(),
            ]);

        // Optional: mark those seen in this run as active (in case some were inactive)
        $activated = MedicalDevice::where('source', $source)
            ->where('last_seen_run_id', $runId)
            ->where('is_active', false)
            ->update([
                'is_active' => true,
                'synced_at' => now(),
            ]);

        return response()->json([
            'ok' => true,
            'run_id' => $runId,
            'activated' => $activated,
            'deactivated' => $deactivated,
        ]);
    }

    /**
     * GET /import/relink/status
     * Returns the last max source_lastmod we have, to drive incremental runs.
     */
    public function status(Request $request)
    {
        $this->assertAuthorized($request);

        $source = config('ingestion.relink.source', 'relink');

        $maxLastmod = MedicalDevice::where('source', $source)
            ->whereNotNull('source_lastmod')
            ->max('source_lastmod');

        return response()->json([
            'ok' => true,
            'max_source_lastmod' => $maxLastmod ? Carbon::parse($maxLastmod)->toIso8601String() : null,
        ]);
    }

    /**
     * (Keep your old sync endpoint if you want it as a backup.)
     * But with finalize(run_id), you no longer need to send external_ids.
     */
    public function syncActiveSet(Request $request)
    {
        $this->assertAuthorized($request);

        $validated = $request->validate([
            'external_ids' => 'required|array|min:1',
            'external_ids.*' => 'required|string|max:255',
        ]);

        $source = config('ingestion.relink.source', 'relink');
        $ids = array_values(array_unique(array_map('strval', $validated['external_ids'])));

        $deactivated = MedicalDevice::where('source', $source)
            ->whereNotIn('source_external_id', $ids)
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'synced_at' => now(),
            ]);

        $activated = MedicalDevice::where('source', $source)
            ->whereIn('source_external_id', $ids)
            ->where('is_active', false)
            ->update([
                'is_active' => true,
                'synced_at' => now(),
            ]);

        return response()->json([
            'ok' => true,
            'activated' => $activated,
            'deactivated' => $deactivated,
            'count_sent' => count($ids),
        ]);
    }
}
