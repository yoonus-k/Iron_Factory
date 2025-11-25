<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\ManufacturingController;
use Modules\Manufacturing\Http\Controllers\WarehouseProductController;
use Modules\Manufacturing\Http\Controllers\DeliveryNoteController;
use Modules\Manufacturing\Http\Controllers\PurchaseInvoiceController;
use Modules\Manufacturing\Http\Controllers\SupplierController;
use Modules\Manufacturing\Http\Controllers\AdditiveController;
use Modules\Manufacturing\Http\Controllers\WarehouseController;
use Modules\Manufacturing\Http\Controllers\Stage1Controller;
use Modules\Manufacturing\Http\Controllers\Stage2Controller;
use Modules\Manufacturing\Http\Controllers\Stage3Controller;
use Modules\Manufacturing\Http\Controllers\Stage4Controller;
use Modules\Manufacturing\Http\Controllers\QualityController;
use Modules\Manufacturing\Http\Controllers\WarehouseSettingsController;
use Modules\Manufacturing\Http\Controllers\UnitController;
use Modules\Manufacturing\Http\Controllers\MaterialTypeController;
use Modules\Manufacturing\Http\Controllers\WarehouseRegistrationController;
use Modules\Manufacturing\Http\Controllers\ReconciliationController;
use Modules\Manufacturing\Http\Controllers\MaterialMovementController;
use Modules\Manufacturing\Http\Controllers\WarehouseReportsController;
use Modules\Manufacturing\Http\Controllers\ProductionConfirmationController;

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

    // Material Routes
    Route::get('warehouses/material/{id}', [WarehouseProductController::class, 'showMaterial'])->name('manufacturing.warehouses.material.show');
    Route::post('warehouses/material/{id}/add-quantity', [WarehouseProductController::class, 'addMaterialQuantity'])->name('manufacturing.warehouses.material.add-quantity');
    Route::resource('delivery-notes', DeliveryNoteController::class)->names('manufacturing.delivery-notes');
    Route::put('delivery-notes/{id}/toggle-status', [DeliveryNoteController::class, 'toggleStatus'])->name('manufacturing.delivery-notes.toggle-status');
    Route::put('delivery-notes/{id}/change-status', [DeliveryNoteController::class, 'changeStatus'])->name('manufacturing.delivery-notes.change-status');
    Route::put('delivery-notes/{id}/update-status', [DeliveryNoteController::class, 'updateStatus'])->name('manufacturing.delivery-notes.update-status');
    Route::resource('purchase-invoices', PurchaseInvoiceController::class)->names('manufacturing.purchase-invoices');
    Route::put('purchase-invoices/{id}/update-status', [PurchaseInvoiceController::class, 'updateStatus'])->name('manufacturing.purchase-invoices.update-status');
    Route::resource('suppliers', SupplierController::class)->names('manufacturing.suppliers');
    Route::put('suppliers/{id}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('manufacturing.suppliers.toggle-status');
    Route::get('suppliers/{id}/invoices', [SupplierController::class, 'getInvoices'])->name('manufacturing.suppliers.invoices');
    Route::get('suppliers/{id}/delivery-notes', [SupplierController::class, 'getDeliveryNotes'])->name('manufacturing.suppliers.delivery-notes');
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

    // ========== تسجيل البضاعة ==========
    Route::prefix('warehouse/registration')->group(function () {
        Route::get('/', [WarehouseRegistrationController::class, 'pending'])->name('manufacturing.warehouse.registration.pending');
        Route::get('create/{deliveryNote}', [WarehouseRegistrationController::class, 'create'])->name('manufacturing.warehouse.registration.create');
        Route::post('store/{deliveryNote}', [WarehouseRegistrationController::class, 'store'])->name('manufacturing.warehouse.registration.store');
        Route::get('show/{deliveryNote}', [WarehouseRegistrationController::class, 'show'])->name('manufacturing.warehouse.registration.show');

        // النقل للإنتاج مع اختيار الكمية
        Route::get('transfer/{deliveryNote}', [WarehouseRegistrationController::class, 'showTransferForm'])->name('manufacturing.warehouse.registration.transfer-form');
        Route::post('transfer-to-production/{deliveryNote}', [WarehouseRegistrationController::class, 'transferToProduction'])->name('manufacturing.warehouse.registration.transfer-to-production');
        
        // صفحة باركود الإنتاج
        Route::get('production-barcode/{deliveryNote}', [WarehouseRegistrationController::class, 'showProductionBarcode'])->name('manufacturing.warehouse.registration.production-barcode');

        // النقل الفوري (لاحتفاظ الرجعية)
        Route::post('move-production/{deliveryNote}', [WarehouseRegistrationController::class, 'moveToProduction'])->name('manufacturing.warehouse.registration.move-production');

        Route::post('lock/{deliveryNote}', [WarehouseRegistrationController::class, 'lock'])->name('manufacturing.warehouse.registration.lock');
        Route::post('unlock/{deliveryNote}', [WarehouseRegistrationController::class, 'unlock'])->name('manufacturing.warehouse.registration.unlock');
    });

    // ========== التسوية ==========
    Route::prefix('warehouse/reconciliation')->group(function () {
        Route::get('/', [ReconciliationController::class, 'index'])->name('manufacturing.warehouses.reconciliation.index');
        Route::get('show/{deliveryNote}', [ReconciliationController::class, 'show'])->name('manufacturing.warehouses.reconciliation.show');
        Route::post('link/{deliveryNote}', [ReconciliationController::class, 'link'])->name('manufacturing.warehouses.reconciliation.link');
        Route::post('decide/{deliveryNote}', [ReconciliationController::class, 'decide'])->name('manufacturing.warehouses.reconciliation.decide');
        Route::get('history', [ReconciliationController::class, 'history'])->name('manufacturing.warehouses.reconciliation.history');
        Route::get('supplier-report', [ReconciliationController::class, 'supplierReport'])->name('manufacturing.warehouses.reconciliation.supplier-report');

        // ربط الفاتورة المتأخرة
        Route::get('link-invoice', [ReconciliationController::class, 'showLinkInvoice'])->name('manufacturing.warehouses.reconciliation.link-invoice');
        Route::post('link-invoice', [ReconciliationController::class, 'storeLinkInvoice'])->name('manufacturing.warehouses.reconciliation.link-invoice.store');
        Route::get('link-invoice/{reconciliation}/edit', [ReconciliationController::class, 'editLinkInvoice'])->name('manufacturing.warehouses.reconciliation.link-invoice.edit');
        Route::put('link-invoice/{reconciliation}', [ReconciliationController::class, 'updateLinkInvoice'])->name('manufacturing.warehouses.reconciliation.link-invoice.update');
        Route::delete('link-invoice/{reconciliation}', [ReconciliationController::class, 'deleteLinkInvoice'])->name('manufacturing.warehouses.reconciliation.link-invoice.delete');

        // ✅ لوحة التحكم الإدارية
        Route::get('management', [ReconciliationController::class, 'showManagement'])->name('manufacturing.warehouses.reconciliation.management');

        // ✅ APIs للبحث والـ Auto-complete
        Route::get('api/search-delivery-notes', [ReconciliationController::class, 'searchDeliveryNotes'])->name('manufacturing.warehouses.reconciliation.api.search-delivery-notes');
        Route::get('api/search-invoices', [ReconciliationController::class, 'searchInvoices'])->name('manufacturing.warehouses.reconciliation.api.search-invoices');
        Route::get('api/delivery-note/{id}', [ReconciliationController::class, 'getDeliveryNoteDetails'])->name('manufacturing.warehouses.reconciliation.api.delivery-note-details');
        Route::get('api/invoice/{id}', [ReconciliationController::class, 'getInvoiceDetails'])->name('manufacturing.warehouses.reconciliation.api.invoice-details');
        Route::post('api/create-delivery-note-from-invoice', [ReconciliationController::class, 'createDeliveryNoteFromInvoice'])->name('manufacturing.warehouses.reconciliation.api.create-delivery-note-from-invoice');

        // ✅ APIs لـ CRUD لوحة التحكم
        Route::get('api/get-delivery-notes', [ReconciliationController::class, 'getDeliveryNotes'])->name('manufacturing.warehouses.reconciliation.api.get-delivery-notes');
        Route::get('api/get-invoices', [ReconciliationController::class, 'getInvoices'])->name('manufacturing.warehouses.reconciliation.api.get-invoices');
        Route::get('api/get-reconciliation-logs', [ReconciliationController::class, 'getReconciliationLogs'])->name('manufacturing.warehouses.reconciliation.api.get-reconciliation-logs');
        Route::get('api/get-movements', [ReconciliationController::class, 'getMovements'])->name('manufacturing.warehouses.reconciliation.api.get-movements');
        Route::get('api/reconciliation/{id}', [ReconciliationController::class, 'getReconciliationDetails'])->name('manufacturing.warehouses.reconciliation.api.reconciliation-details');

        // ✅ Routes التعديل والحذف
        Route::patch('update-delivery-note/{deliveryNote}', [ReconciliationController::class, 'updateDeliveryNote'])->name('manufacturing.warehouses.reconciliation.update-delivery-note');
        Route::patch('update-invoice/{purchaseInvoice}', [ReconciliationController::class, 'updateInvoice'])->name('manufacturing.warehouses.reconciliation.update-invoice');
        Route::patch('update-reconciliation/{reconciliationLog}', [ReconciliationController::class, 'updateReconciliation'])->name('manufacturing.warehouses.reconciliation.update-reconciliation');
        Route::delete('delete-delivery-note/{deliveryNote}', [ReconciliationController::class, 'deleteDeliveryNote'])->name('manufacturing.warehouses.reconciliation.delete-delivery-note');
        Route::delete('delete-invoice/{purchaseInvoice}', [ReconciliationController::class, 'deleteInvoice'])->name('manufacturing.warehouses.reconciliation.delete-invoice');
        Route::delete('delete-reconciliation/{reconciliationLog}', [ReconciliationController::class, 'deleteReconciliation'])->name('manufacturing.warehouses.reconciliation.delete-reconciliation');
        Route::delete('delete-movement/{materialMovement}', [ReconciliationController::class, 'deleteMovement'])->name('manufacturing.warehouses.reconciliation.delete-movement');

        // ✅ صفحات التعديل (Edit Pages)
        Route::get('edit-delivery-note/{deliveryNote}', [ReconciliationController::class, 'editDeliveryNote'])->name('manufacturing.warehouses.reconciliation.edit-delivery-note');
        Route::get('edit-invoice/{purchaseInvoice}', [ReconciliationController::class, 'editInvoice'])->name('manufacturing.warehouses.reconciliation.edit-invoice');
        Route::get('edit-reconciliation/{reconciliationLog}', [ReconciliationController::class, 'editReconciliation'])->name('manufacturing.warehouses.reconciliation.edit-reconciliation');
    });

    // ========== سجل حركات المواد ==========
    Route::prefix('warehouse/movements')->group(function () {
        Route::get('/', [MaterialMovementController::class, 'index'])->name('manufacturing.warehouse.movements.index');
        Route::get('show/{movement}', [MaterialMovementController::class, 'show'])->name('manufacturing.warehouse.movements.show');
    });

    // API endpoint for movement details
    Route::get('material-movements/{id}', [MaterialMovementController::class, 'getDetails'])->name('manufacturing.material-movements.details');

    // ========== تأكيدات الإنتاج ==========
    Route::prefix('manufacturing/production/confirmations')->name('manufacturing.production.confirmations.')->group(function () {
        Route::get('pending', [ProductionConfirmationController::class, 'pendingConfirmations'])->name('pending');
        Route::get('/', [ProductionConfirmationController::class, 'index'])->name('index');
        Route::get('{id}', [ProductionConfirmationController::class, 'show'])->name('show');
        Route::post('{id}/confirm', [ProductionConfirmationController::class, 'confirm'])->name('confirm');
        Route::post('{id}/reject', [ProductionConfirmationController::class, 'reject'])->name('reject');
        Route::get('{id}/details', [ProductionConfirmationController::class, 'getDetails'])->name('details');
    });

    // ========== تقارير وإحصائيات المستودع ==========
    Route::prefix('warehouse/reports')->group(function () {
        Route::get('/', [WarehouseReportsController::class, 'index'])->name('manufacturing.warehouse-reports.index');
        Route::get('comprehensive', [WarehouseReportsController::class, 'comprehensiveReport'])->name('manufacturing.warehouse-reports.comprehensive');
        Route::get('warehouses-statistics', [WarehouseReportsController::class, 'warehousesStatistics'])->name('manufacturing.warehouse-reports.warehouses-statistics');
        Route::get('materials', [WarehouseReportsController::class, 'materialsReport'])->name('manufacturing.warehouse-reports.materials');
        Route::get('delivery-notes', [WarehouseReportsController::class, 'deliveryNotesReport'])->name('manufacturing.warehouse-reports.delivery-notes');
        Route::get('purchase-invoices', [WarehouseReportsController::class, 'purchaseInvoicesReport'])->name('manufacturing.warehouse-reports.purchase-invoices');
        Route::get('additives', [WarehouseReportsController::class, 'additivesReport'])->name('manufacturing.warehouse-reports.additives');
        Route::get('suppliers', [WarehouseReportsController::class, 'suppliersReport'])->name('manufacturing.warehouse-reports.suppliers');
        Route::get('movements', [WarehouseReportsController::class, 'movementsReport'])->name('manufacturing.warehouse-reports.movements');

        // ========== تقرير القطع قيد التشغيل (WIP) ==========
        Route::get('reports/wip', [\Modules\Manufacturing\Http\Controllers\WorkInProgressController::class, 'index'])->name('manufacturing.reports.wip');
        Route::get('reports/wip/stats', [\Modules\Manufacturing\Http\Controllers\WorkInProgressController::class, 'stats'])->name('manufacturing.reports.wip.stats');
    });

    // Production Stages Routes
    Route::resource('stage1', Stage1Controller::class)->names('manufacturing.stage1');
    Route::resource('stage2', Stage2Controller::class)->names('manufacturing.stage2');
    Route::resource('stage3', Stage3Controller::class)->names('manufacturing.stage3');
    Route::resource('stage4', Stage4Controller::class)->names('manufacturing.stage4');

    // Stage 1 Additional Routes
    Route::get('stage1/barcode/scan', [Stage1Controller::class, 'barcodeScan'])->name('manufacturing.stage1.barcode-scan');
    Route::post('stage1/barcode/process', [Stage1Controller::class, 'processBarcodeAction'])->name('manufacturing.stage1.process-barcode');
    Route::get('stage1/waste/tracking', [Stage1Controller::class, 'wasteTracking'])->name('manufacturing.stage1.waste-tracking');
    Route::post('stage1/store-single', [Stage1Controller::class, 'storeSingle'])->name('manufacturing.stage1.store-single');
    Route::get('material-batches/get-by-barcode/{barcode}', [Stage1Controller::class, 'getMaterialByBarcode'])->name('manufacturing.material-batch.get-by-barcode');

    // Stage 2 Additional Routes
    Route::post('stage2/store-single', [Stage2Controller::class, 'storeSingle'])->name('manufacturing.stage2.store-single');
    Route::get('stage2/complete/processing', [Stage2Controller::class, 'completeProcessing'])->name('manufacturing.stage2.complete-processing');
    Route::put('stage2/complete', [Stage2Controller::class, 'completeAction'])->name('manufacturing.stage2.complete');
    Route::get('stage2/waste/statistics', [Stage2Controller::class, 'wasteStatistics'])->name('manufacturing.stage2.waste-statistics');

    // Stage 3 Additional Routes
    Route::post('stage3/store-single', [Stage3Controller::class, 'storeSingle'])->name('manufacturing.stage3.store-single');
    Route::get('stage3/get-stage2-by-barcode/{barcode}', [Stage3Controller::class, 'getByBarcode'])->name('manufacturing.stage3.get-stage2-by-barcode');
    Route::get('stage3/add-dye-plastic', [Stage3Controller::class, 'addDyePlastic'])->name('manufacturing.stage3.add-dye-plastic');
    Route::post('stage3/add-dye', [Stage3Controller::class, 'addDyeAction'])->name('manufacturing.stage3.add-dye');
    Route::get('stage3/completed-coils', [Stage3Controller::class, 'completedCoils'])->name('manufacturing.stage3.completed-coils');

    // Stage 4 Additional Routes
    Route::post('stage4/store-single', [Stage4Controller::class, 'storeSingle'])->name('manufacturing.stage4.store-single');
    Route::get('stage4/get-lafaf-by-barcode/{barcode}', [Stage4Controller::class, 'getByBarcode'])->name('manufacturing.stage4.get-lafaf-by-barcode');

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
