<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medical_devices', function (Blueprint $table) {
            // If it exists and is varchar, convert to TEXT
            $table->text('source_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('medical_devices', function (Blueprint $table) {
            $table->string('source_url', 2048)->nullable()->change();
        });
    }
};
