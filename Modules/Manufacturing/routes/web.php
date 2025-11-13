<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\ManufacturingController;
use Modules\Manufacturing\Http\Controllers\WarehouseProductController;
use Modules\Manufacturing\Http\Controllers\DeliveryNoteController;
use Modules\Manufacturing\Http\Controllers\PurchaseInvoiceController;
use Modules\Manufacturing\Http\Controllers\SupplierController;
use Modules\Manufacturing\Http\Controllers\AdditiveController;
use Modules\Manufacturing\Http\Controllers\WarehouseController;
use Modules\Manufacturing\Http\Controllers\ShiftsWorkersController;
use Modules\Manufacturing\Http\Controllers\Stage1Controller;
use Modules\Manufacturing\Http\Controllers\Stage2Controller;
use Modules\Manufacturing\Http\Controllers\Stage3Controller;
use Modules\Manufacturing\Http\Controllers\Stage4Controller;
use Modules\Manufacturing\Http\Controllers\QualityController;
use Modules\Manufacturing\Http\Controllers\WarehouseSettingsController;
use Modules\Manufacturing\Http\Controllers\UnitController;
use Modules\Manufacturing\Http\Controllers\MaterialTypeController;

/*
|--------------------------------------------------------------------------
| Manufacturing Routes - Protected by Authentication
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::resource('manufacturings', ManufacturingController::class)->names('manufacturing');

    // Warehouse Routes
    Route::resource('warehouses', WarehouseController::class)->names('manufacturing.warehouses');
    Route::get('warehouses/statistics', [WarehouseController::class, 'statistics'])->name('manufacturing.warehouses.statistics');
    Route::get('warehouses/active', [WarehouseController::class, 'getActive'])->name('manufacturing.warehouses.active');
    Route::resource('warehouse-products', WarehouseProductController::class)->names('manufacturing.warehouse-products');
    Route::get('warehouse-products/{id}/transactions', [WarehouseProductController::class, 'transactions'])->name('manufacturing.warehouse-products.transactions');
    Route::post('warehouse-products/{id}/add-quantity', [WarehouseProductController::class, 'addQuantity'])->name('manufacturing.warehouse-products.add-quantity');
    Route::get('materials/export', [WarehouseProductController::class, 'export'])->name('manufacturing.materials.export');
    Route::post('materials/import', [WarehouseProductController::class, 'import'])->name('manufacturing.materials.import');
    Route::resource('delivery-notes', DeliveryNoteController::class)->names('manufacturing.delivery-notes');
    Route::resource('purchase-invoices', PurchaseInvoiceController::class)->names('manufacturing.purchase-invoices');
    Route::resource('suppliers', SupplierController::class)->names('manufacturing.suppliers');
    Route::put('suppliers/{id}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('manufacturing.suppliers.toggle-status');
    Route::resource('additives', AdditiveController::class)->names('manufacturing.additives');

    // Warehouse Settings Routes
    Route::get('warehouse-settings', [WarehouseSettingsController::class, 'index'])->name('manufacturing.warehouse-settings.index');

    // Categories Manual Routes
    Route::get('warehouse-settings/categories', [WarehouseSettingsController::class, 'categoriesIndex'])->name('manufacturing.warehouse-settings.categories.index');
    Route::get('warehouse-settings/categories/create', [WarehouseSettingsController::class, 'categoriesCreate'])->name('manufacturing.warehouse-settings.categories.create');
    Route::post('warehouse-settings/categories', [WarehouseSettingsController::class, 'categoriesStore'])->name('manufacturing.warehouse-settings.categories.store');
    Route::get('warehouse-settings/categories/{id}', [WarehouseSettingsController::class, 'categoriesShow'])->name('manufacturing.warehouse-settings.categories.show');
    Route::get('warehouse-settings/categories/{id}/edit', [WarehouseSettingsController::class, 'categoriesEdit'])->name('manufacturing.warehouse-settings.categories.edit');
    Route::put('warehouse-settings/categories/{id}', [WarehouseSettingsController::class, 'categoriesUpdate'])->name('manufacturing.warehouse-settings.categories.update');
    Route::delete('warehouse-settings/categories/{id}', [WarehouseSettingsController::class, 'categoriesDestroy'])->name('manufacturing.warehouse-settings.categories.destroy');

    // Units Resource Routes
    Route::resource('warehouse-settings/units', UnitController::class)->names('manufacturing.warehouse-settings.units');

    // Material Types Resource Routes
    Route::resource('warehouse-settings/material-types', MaterialTypeController::class)->names('manufacturing.warehouse-settings.material-types');

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

    // Production Tracking Routes
    Route::get('production-tracking/scan', [QualityController::class, 'productionTrackingScan'])->name('manufacturing.production-tracking.scan');
    Route::post('production-tracking/process', [QualityController::class, 'processProductionTracking'])->name('manufacturing.production-tracking.process');
    Route::get('production-tracking/report', [QualityController::class, 'productionTrackingReport'])->name('manufacturing.production-tracking.report');

    // Iron Journey Tracking Routes
    Route::get('iron-journey', [QualityController::class, 'ironJourney'])->name('manufacturing.iron-journey');
    Route::get('iron-journey/show', [QualityController::class, 'showIronJourney'])->name('manufacturing.iron-journey.show');

}); // End of Authentication Middleware
