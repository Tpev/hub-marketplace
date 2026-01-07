<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngestionRun extends Model
{
    protected $fillable = [
        'source',
        'run_id',
        'status',
        'started_at',
        'finished_at',
        'max_source_lastmod',
        'upserted_count',
        'deactivated_count',
        'error',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'max_source_lastmod' => 'datetime',
    ];
}
