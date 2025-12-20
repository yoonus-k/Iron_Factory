<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\WorkerTrackingController;

Route::middleware(['auth'])->prefix('worker-tracking')->name('worker-tracking.')->group(function () {

    // Dashboard
    Route::get('dashboard', [WorkerTrackingController::class, 'dashboard'])
        ->name('dashboard');

    // Stage History
    Route::get('stage/{stageType}/{stageRecordId}', [WorkerTrackingController::class, 'stageHistory'])
        ->name('stage-history');

    // Worker History
    Route::get('worker/{workerId}', [WorkerTrackingController::class, 'workerHistory'])
        ->name('worker-history');

    // Search by Barcode
    Route::get('search', [WorkerTrackingController::class, 'searchByBarcode'])
        ->name('search');

    // Transfer Work
    Route::post('transfer', [WorkerTrackingController::class, 'transferWork'])
        ->name('transfer');

    // End Session
    Route::post('end-session/{historyId}', [WorkerTrackingController::class, 'endSession'])
        ->name('end-session');

    // Available Workers (AJAX)
    Route::get('available-workers', [WorkerTrackingController::class, 'availableWorkers'])
        ->name('available-workers');
});
