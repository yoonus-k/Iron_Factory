<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\BarcodeController;

/*
|--------------------------------------------------------------------------
| Barcode Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Barcode Management Routes
    Route::get('barcode', [BarcodeController::class, 'index'])->name('manufacturing.barcode.index');
    Route::post('barcode/reset-year', [BarcodeController::class, 'resetYear'])->name('manufacturing.barcode.reset-year');
    Route::put('barcode/settings/{id}', [BarcodeController::class, 'updateSettings'])->name('manufacturing.barcode.settings.update');
    Route::post('barcode/settings/store', [BarcodeController::class, 'storeSettings'])->name('manufacturing.barcode.settings.store');

    // Barcode API Routes
    Route::prefix('api/barcode')->group(function () {
        Route::get('scan/{barcode}', [BarcodeController::class, 'scan'])->name('manufacturing.api.barcode.scan');
        Route::post('generate', [BarcodeController::class, 'generate'])->name('manufacturing.api.barcode.generate');
        Route::get('history/{barcode}', [BarcodeController::class, 'history'])->name('manufacturing.api.barcode.history');
        Route::get('trace/{barcode}', [BarcodeController::class, 'trace'])->name('manufacturing.api.barcode.trace');
        Route::get('report/{barcode}', [BarcodeController::class, 'report'])->name('manufacturing.api.barcode.report');
        Route::put('status/{barcode}', [BarcodeController::class, 'updateStatus'])->name('manufacturing.api.barcode.status');
        Route::get('statistics', [BarcodeController::class, 'statistics'])->name('manufacturing.api.barcode.statistics');
        Route::get('print/{barcode}', [BarcodeController::class, 'print'])->name('manufacturing.api.barcode.print');
    });

}); // End of Authentication Middleware
