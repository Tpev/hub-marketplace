<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MedicalDevice;
use App\Models\DeviceInquiry;

class AdminController extends Controller
{
    private function ensureAdmin(): void
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->ensureAdmin();

        $users = User::withCount([
                'medicalDevices',
                'deviceInquiries as contact_requests_count'
            ])
            ->orderByDesc('created_at')
            ->get();

        $inquiries = \App\Models\BuyerInquiry::latest()->get();

        $kpis = [
            'total_users'     => User::count(),
            'buyers'          => User::where('intent', 'Buyer')->count(),
            'sellers'         => User::where('intent', 'Seller')->count(),
            'both'            => User::where('intent', 'Both')->count(),
            'new_devices'     => MedicalDevice::where('condition', 'new')->count(),
            'used_devices'    => MedicalDevice::where('condition', 'used')->count(),
            'refurb_devices'  => MedicalDevice::where('condition', 'refurbished')->count(),
            'total_devices'   => MedicalDevice::count(),
            'total_inquiries' => DeviceInquiry::count(),
            'total_value'     => MedicalDevice::sum('price'),
            'average_price'   => round(MedicalDevice::avg('price'), 2),
        ];

        // Allowed tiers (for Blade UI + validation)
        $licenseTiers = [
            'basic'      => 'Basic',
            'pro'        => 'Pro',
            'enterprise' => 'Enterprise',
        ];

        return view('admin.dashboard', compact('users', 'kpis', 'inquiries', 'licenseTiers'));
    }

    public function updateUserLicense(Request $request, User $user)
    {
        $this->ensureAdmin();

        // Optional safety: prevent admin from disabling themselves
        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'is_subscribed' => "You can't change your own license from here.",
            ]);
        }

        $allowedTiers = ['basic', 'pro', 'enterprise'];

        $data = $request->validate([
            // Tamper check: must match route user
            'user_id'       => ['required', 'integer'],
            'is_subscribed' => ['required', 'in:0,1'],
            'license_tier'  => ['nullable', 'in:' . implode(',', $allowedTiers)],
        ]);

        if ((int) $data['user_id'] !== (int) $user->id) {
            abort(403, 'Invalid target user.');
        }

        $isSubscribed = (int) $data['is_subscribed'] === 1;

        // If active, tier is required (guardrail)
        if ($isSubscribed && empty($data['license_tier'])) {
            return back()->withErrors([
                'license_tier' => 'Tier is required when license is Active.',
            ]);
        }

        $user->is_subscribed = $isSubscribed;
        $user->license_tier  = $isSubscribed ? $data['license_tier'] : null; // clear tier if inactive
        $user->save();

        return back()->with('success', "✅ License updated for {$user->name} (User #{$user->id}).");
    }
}
