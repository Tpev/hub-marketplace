<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medical_devices', function (Blueprint $table) {
            // si ta colonne peut être null
            $table->text('image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('medical_devices', function (Blueprint $table) {
            // remet une taille "classique" si besoin (à adapter à ton ancien schema)
            $table->string('image', 255)->nullable()->change();
        });
    }
};
