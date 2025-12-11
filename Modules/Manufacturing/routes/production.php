<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\Stage1Controller;
use Modules\Manufacturing\Http\Controllers\Stage2Controller;
use Modules\Manufacturing\Http\Controllers\Stage3Controller;
use Modules\Manufacturing\Http\Controllers\Stage4Controller;
use Modules\Manufacturing\Http\Controllers\StandsController;
use Modules\Manufacturing\Http\Controllers\ProductTrackingController;
use Modules\Manufacturing\Http\Controllers\ProductTrackingReportController;
use Modules\Manufacturing\Http\Controllers\WorkInProgressController;
use Modules\Manufacturing\Http\Controllers\WorkerPerformanceController;
use Modules\Manufacturing\Http\Controllers\ShiftDashboardController;

/*
|--------------------------------------------------------------------------
| Production Routes - Protected by Authentication
|--------------------------------------------------------------------------
| Routes for production stages, stands management, and tracking
*/

Route::middleware(['auth'])->group(function () {

    // Production Stages Routes
    // Route::resource('stage1', Stage1Controller::class)->names('manufacturing.stage1');
    // Route::resource('stage2', Stage2Controller::class)->names('manufacturing.stage2');
    // Route::resource('stage3', Stage3Controller::class)->names('manufacturing.stage3');
    // Route::resource('stage4', Stage4Controller::class)->names('manufacturing.stage4');

    // Stands Management Routes
    Route::get('stands/generate-number', [StandsController::class, 'generateStandNumber'])->name('manufacturing.stands.generate-number');
    Route::get('stands/usage-history', [StandsController::class, 'usageHistory'])->name('manufacturing.stands.usage-history');
    Route::patch('stands/{id}/toggle-status', [StandsController::class, 'toggleStatus'])->name('manufacturing.stands.toggle-status');
    Route::resource('stands', StandsController::class)->names('manufacturing.stands');

    // Stage 1 Additional Routes
    Route::get('stage1/barcode/scan', [Stage1Controller::class, 'barcodeScan'])->name('manufacturing.stage1.barcode-scan');
    Route::post('stage1/barcode/process', [Stage1Controller::class, 'processBarcodeAction'])->name('manufacturing.stage1.process-barcode');
    Route::get('stage1/waste/tracking', [Stage1Controller::class, 'wasteTracking'])->name('manufacturing.stage1.waste-tracking');

    // Stage 2 Additional Routes
    Route::get('stage1/get-by-barcode/{barcode}', [Stage2Controller::class, 'getByBarcode'])->name('manufacturing.stage1.get-by-barcode');
    Route::get('stage2/get-by-barcode/{barcode}', [Stage2Controller::class, 'getByBarcode'])->name('manufacturing.stage2.get-by-barcode'); // يدعم مصدرين
    Route::get('stage2/complete/processing', [Stage2Controller::class, 'completeProcessing'])->name('manufacturing.stage2.complete-processing');
    Route::put('stage2/complete', [Stage2Controller::class, 'completeAction'])->name('manufacturing.stage2.complete');
    Route::get('stage2/waste/statistics', [Stage2Controller::class, 'wasteStatistics'])->name('manufacturing.stage2.waste-statistics');

    // Stage 3 Additional Routes
    Route::get('stage3/get-stage2-by-barcode/{barcode}', [Stage3Controller::class, 'getByBarcode'])->name('manufacturing.stage3.get-stage2-barcode');
    Route::get('stage3/add-dye-plastic', [Stage3Controller::class, 'addDyePlastic'])->name('manufacturing.stage3.add-dye-plastic');
    Route::post('stage3/add-dye', [Stage3Controller::class, 'addDyeAction'])->name('manufacturing.stage3.add-dye');
    Route::get('stage3/completed-coils', [Stage3Controller::class, 'completedCoils'])->name('manufacturing.stage3.completed-coils');

    // Stage 4 Additional Routes
    Route::get('stage4/get-lafaf-by-barcode/{barcode}', [Stage4Controller::class, 'getByBarcode'])->name('manufacturing.stage4.get-lafaf-barcode');

    // Production Tracking Routes (Real Data)
    Route::get('production-tracking/scan', [ProductTrackingController::class, 'scan'])->name('manufacturing.production-tracking.scan');
    Route::post('production-tracking/process', [ProductTrackingController::class, 'process'])->name('manufacturing.production-tracking.process');
    Route::get('production-tracking/report', [ProductTrackingController::class, 'report'])->name('manufacturing.production-tracking.report');
    Route::get('production-tracking/chart/{barcode}', [ProductTrackingController::class, 'getChartData'])->name('manufacturing.production-tracking.chart');

    // ============================================
    // Production Reports Routes
    // ============================================

    // Work In Progress Report (الأعمال غير المنتهية)
    Route::get('reports/wip', [WorkInProgressController::class, 'index'])->name('manufacturing.reports.wip');
    Route::get('reports/wip/stats', [WorkInProgressController::class, 'stats'])->name('manufacturing.reports.wip.stats');
    Route::get('reports/wip/export', [WorkInProgressController::class, 'export'])->name('manufacturing.reports.wip.export');

    // Worker Performance Report (مؤشرات أداء العمال)
    Route::get('reports/worker-performance', [WorkerPerformanceController::class, 'index'])->name('manufacturing.reports.worker-performance');
    Route::get('reports/worker-performance/{workerId}', [WorkerPerformanceController::class, 'show'])->name('manufacturing.reports.worker-performance.show');
    Route::get('reports/worker-performance/compare', [WorkerPerformanceController::class, 'compare'])->name('manufacturing.reports.worker-performance.compare');

    // Shift Dashboard (ملخص الوردية)
    Route::get('reports/shift-dashboard', [ShiftDashboardController::class, 'index'])->name('manufacturing.reports.shift-dashboard');
    Route::get('reports/shift-dashboard/night-summary', [ShiftDashboardController::class, 'nightShiftSummary'])->name('manufacturing.reports.night-shift-summary');
    Route::get('reports/shift-dashboard/live-stats', [ShiftDashboardController::class, 'liveStats'])->name('manufacturing.reports.live-stats');

    // Product Tracking Report (تقرير التتبع الشامل)
    Route::get('reports/product-tracking', [ProductTrackingReportController::class, 'index'])->name('manufacturing.reports.product-tracking');

}); // End of Authentication Middleware
