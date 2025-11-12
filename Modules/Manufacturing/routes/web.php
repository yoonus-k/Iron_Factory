<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\ManufacturingController;
use Modules\Manufacturing\Http\Controllers\WarehouseProductController;
use Modules\Manufacturing\Http\Controllers\DeliveryNoteController;
use Modules\Manufacturing\Http\Controllers\PurchaseInvoiceController;
use Modules\Manufacturing\Http\Controllers\SupplierController;
use Modules\Manufacturing\Http\Controllers\AdditiveController;
use Modules\Manufacturing\Http\Controllers\ShiftsWorkersController;
use Modules\Manufacturing\Http\Controllers\Stage1Controller;
use Modules\Manufacturing\Http\Controllers\Stage2Controller;
use Modules\Manufacturing\Http\Controllers\Stage3Controller;
use Modules\Manufacturing\Http\Controllers\Stage4Controller;
use Modules\Manufacturing\Http\Controllers\QualityController;

    Route::resource('manufacturings', ManufacturingController::class)->names('manufacturing');

    // Warehouse Routes
    Route::resource('warehouse-products', WarehouseProductController::class)->names('manufacturing.warehouse-products');
    Route::resource('delivery-notes', DeliveryNoteController::class)->names('manufacturing.delivery-notes');
    Route::resource('purchase-invoices', PurchaseInvoiceController::class)->names('manufacturing.purchase-invoices');
    Route::resource('suppliers', SupplierController::class)->names('manufacturing.suppliers');
    Route::resource('additives', AdditiveController::class)->names('manufacturing.additives');

    // Production Stages Routes
    Route::resource('stage1', Stage1Controller::class)->names('manufacturing.stage1');
    Route::resource('stage2', Stage2Controller::class)->names('manufacturing.stage2');
    Route::resource('stage3', Stage3Controller::class)->names('manufacturing.stage3');
    Route::resource('stage4', Stage4Controller::class)->names('manufacturing.stage4');
    Route::resource('shifts-workers', ShiftsWorkersController::class)->names('manufacturing.shifts-workers');

    // Shifts and Workers Additional Routes
    Route::get('shifts-workers/current/view', [ShiftsWorkersController::class, 'current'])->name('manufacturing.shifts-workers.current');
    Route::get('shifts-workers/attendance/log', [ShiftsWorkersController::class, 'attendance'])->name('manufacturing.shifts-workers.attendance');

    // Stage 1 Additional Routes
    Route::get('stage1/barcode/scan', [Stage1Controller::class, 'barcodeScan'])->name('manufacturing.stage1.barcode-scan');
    Route::post('stage1/barcode/process', [Stage1Controller::class, 'processBarcodeAction'])->name('manufacturing.stage1.process-barcode');
    Route::get('stage1/waste/tracking', [Stage1Controller::class, 'wasteTracking'])->name('manufacturing.stage1.waste-tracking');

    // Stage 2 Additional Routes
    Route::get('stage2/complete/processing', [Stage2Controller::class, 'completeProcessing'])->name('manufacturing.stage2.complete-processing');
    Route::put('stage2/complete', [Stage2Controller::class, 'completeAction'])->name('manufacturing.stage2.complete');
    Route::get('stage2/waste/statistics', [Stage2Controller::class, 'wasteStatistics'])->name('manufacturing.stage2.waste-statistics');

    // Stage 3 Additional Routes
    Route::get('stage3/add-dye-plastic', [Stage3Controller::class, 'addDyePlastic'])->name('manufacturing.stage3.add-dye-plastic');
    Route::post('stage3/add-dye', [Stage3Controller::class, 'addDyeAction'])->name('manufacturing.stage3.add-dye');
    Route::get('stage3/completed-coils', [Stage3Controller::class, 'completedCoils'])->name('manufacturing.stage3.completed-coils');

    // Quality Management Routes
    Route::get('quality', [QualityController::class, 'index'])->name('manufacturing.quality.index');
    Route::get('quality/waste-report', [QualityController::class, 'wasteReport'])->name('manufacturing.quality.waste-report');
    Route::get('quality/quality-monitoring', [QualityController::class, 'qualityMonitoring'])->name('manufacturing.quality.quality-monitoring');
    Route::get('quality/downtime-tracking', [QualityController::class, 'downtimeTracking'])->name('manufacturing.quality.downtime-tracking');
    Route::get('quality/waste-limits', [QualityController::class, 'wasteLimits'])->name('manufacturing.quality.waste-limits');
    
    // Quality Check Routes
    Route::get('quality/quality-create', [QualityController::class, 'qualityCreate'])->name('manufacturing.quality.quality-create');
    Route::get('quality/quality-show/{id}', [QualityController::class, 'qualityShow'])->name('manufacturing.quality.quality-show');
    Route::get('quality/quality-edit/{id}', [QualityController::class, 'qualityEdit'])->name('manufacturing.quality.quality-edit');
    
    // Downtime Tracking Routes
    Route::get('quality/downtime-create', [QualityController::class, 'downtimeCreate'])->name('manufacturing.quality.downtime-create');
    Route::get('quality/downtime-show/{id}', [QualityController::class, 'downtimeShow'])->name('manufacturing.quality.downtime-show');
    Route::get('quality/downtime-edit/{id}', [QualityController::class, 'downtimeEdit'])->name('manufacturing.quality.downtime-edit');