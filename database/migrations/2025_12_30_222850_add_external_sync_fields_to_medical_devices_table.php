<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_devices', function (Blueprint $table) {
            // External sync metadata
            $table->string('source')->nullable()->index();                 // e.g. "relink"
            $table->string('source_external_id')->nullable();              // e.g. Shopify product id
            $table->string('source_url')->nullable();
            $table->timestamp('source_lastmod')->nullable();
            $table->timestamp('synced_at')->nullable();

            // Track whether listing still exists in source sitemap
            $table->boolean('is_active')->default(true)->index();

            // IMPORTANT: Unique key for upsert
            $table->unique(['source', 'source_external_id'], 'md_source_external_unique');
        });
    }

    public function down(): void
    {
        Schema::table('medical_devices', function (Blueprint $table) {
            $table->dropUnique('md_source_external_unique');

            $table->dropColumn([
                'source',
                'source_external_id',
                'source_url',
                'source_lastmod',
                'synced_at',
                'is_active',
            ]);
        });
    }
};
