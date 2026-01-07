<?php

return [
    'relink' => [
        'token' => env('RELINK_INGEST_TOKEN'),
        'source' => env('RELINK_INGEST_SOURCE', 'relink'),
        'importer_email' => env('RELINK_IMPORTER_EMAIL', 'relink-importer@yourdomain.com'),
    ],
];
