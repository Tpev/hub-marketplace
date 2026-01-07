<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RelinkIngestionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are automatically prefixed with /api
| and use the 'api' middleware group (no CSRF).
|--------------------------------------------------------------------------
*/

Route::prefix('import/relink')->group(function () {
    Route::post('/medical-devices', [RelinkIngestionController::class, 'upsertBatch']);
    Route::post('/finalize', [RelinkIngestionController::class, 'finalize']);
    Route::get('/status', [RelinkIngestionController::class, 'status']);
});
