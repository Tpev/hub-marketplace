<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ingestion_runs', function (Blueprint $table) {
            $table->id();
            $table->string('source')->index();          // 'relink'
            $table->string('run_id')->index();          // uuid
            $table->string('status')->index();          // 'running'|'success'|'failed'
            $table->dateTimeTz('started_at');
            $table->dateTimeTz('finished_at')->nullable();
            $table->dateTimeTz('max_source_lastmod')->nullable();
            $table->unsignedInteger('upserted_count')->default(0);
            $table->unsignedInteger('deactivated_count')->default(0);
            $table->text('error')->nullable();
            $table->timestamps();

            $table->unique(['source', 'run_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingestion_runs');
    }
};
