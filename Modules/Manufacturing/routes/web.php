<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\ManufacturingController;
use Modules\Manufacturing\Http\Controllers\WarehouseProductController;
use Modules\Manufacturing\Http\Controllers\DeliveryNoteController;
use Modules\Manufacturing\Http\Controllers\PurchaseInvoiceController;
use Modules\Manufacturing\Http\Controllers\SupplierController;
use Modules\Manufacturing\Http\Controllers\AdditiveController;
use Modules\Manufacturing\Http\Controllers\WarehouseController;
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
    Route::put('warehouses/{id}/toggle-status', [WarehouseController::class, 'toggleStatus'])->name('manufacturing.warehouses.toggle-status');
    Route::resource('warehouse-products', WarehouseProductController::class)->names('manufacturing.warehouse-products');
    Route::get('warehouse-products/{id}/transactions', [WarehouseProductController::class, 'transactions'])->name('manufacturing.warehouse-products.transactions');
    Route::post('warehouse-products/{id}/add-quantity', [WarehouseProductController::class, 'addQuantity'])->name('manufacturing.warehouse-products.add-quantity');
    Route::post('warehouse-products/{id}/change-status', [WarehouseProductController::class, 'changeStatus'])->name('manufacturing.warehouse-products.change-status');
    Route::get('materials/export', [WarehouseProductController::class, 'export'])->name('manufacturing.materials.export');
    Route::post('materials/import', [WarehouseProductController::class, 'import'])->name('manufacturing.materials.import');
    Route::resource('delivery-notes', DeliveryNoteController::class)->names('manufacturing.delivery-notes');
    Route::put('delivery-notes/{id}/toggle-status', [DeliveryNoteController::class, 'toggleStatus'])->name('manufacturing.delivery-notes.toggle-status');
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
    Route::put('warehouse-settings/units/{id}/toggle-status', [UnitController::class, 'toggleStatus'])->name('manufacturing.warehouse-settings.units.toggle-status');

    // Material Types Resource Routes
    Route::resource('warehouse-settings/material-types', MaterialTypeController::class)->names('manufacturing.warehouse-settings.material-types');
    Route::put('warehouse-settings/material-types/{id}/toggle-status', [MaterialTypeController::class, 'toggleStatus'])->name('manufacturing.warehouse-settings.material-types.toggle-status');

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

    // Iron Journey Tracking Routes
    Route::get('iron-journey', [QualityController::class, 'ironJourney'])->name('manufacturing.iron-journey');
    Route::get('iron-journey/show', [QualityController::class, 'showIronJourney'])->name('manufacturing.iron-journey.show');

}); // End of Authentication Middleware
