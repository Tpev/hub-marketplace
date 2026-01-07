<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medical_devices', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_devices', 'source')) {
                $table->string('source')->nullable()->index(); // e.g. 'relink'
            }
            if (!Schema::hasColumn('medical_devices', 'source_external_id')) {
                $table->string('source_external_id')->nullable()->index(); // Shopify product id
            }
            if (!Schema::hasColumn('medical_devices', 'source_url')) {
                $table->text('source_url')->nullable();
            }
            if (!Schema::hasColumn('medical_devices', 'source_lastmod')) {
                $table->dateTimeTz('source_lastmod')->nullable()->index();
            }
            if (!Schema::hasColumn('medical_devices', 'last_seen_run_id')) {
                $table->string('last_seen_run_id')->nullable()->index();
            }
            if (!Schema::hasColumn('medical_devices', 'is_active')) {
                $table->boolean('is_active')->default(true)->index();
            }
            if (!Schema::hasColumn('medical_devices', 'synced_at')) {
                $table->dateTimeTz('synced_at')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_devices', function (Blueprint $table) {
            foreach ([
                'source',
                'source_external_id',
                'source_url',
                'source_lastmod',
                'last_seen_run_id',
                'is_active',
                'synced_at',
            ] as $col) {
                if (Schema::hasColumn('medical_devices', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
