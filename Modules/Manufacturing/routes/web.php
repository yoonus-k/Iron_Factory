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
use Modules\Manufacturing\Http\Controllers\ShiftHandoverController;

/*
|--------------------------------------------------------------------------
| Manufacturing Routes - Protected by Authentication
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::resource('manufacturings', ManufacturingController::class)
        ->middleware('permission:MANUFACTURING_READ')
        ->names('manufacturing');

    // Warehouse Routes
    Route::resource('warehouses', WarehouseController::class)
        ->middleware('permission:WAREHOUSE_STORES_READ')
        ->names('manufacturing.warehouses');
    Route::get('warehouses/statistics', [WarehouseController::class, 'statistics'])
        ->middleware('permission:WAREHOUSE_STORES_READ')
        ->name('manufacturing.warehouses.statistics');
    Route::get('warehouses/active', [WarehouseController::class, 'getActive'])
        ->middleware('permission:WAREHOUSE_STORES_READ')
        ->name('manufacturing.warehouses.active');
    Route::put('warehouses/{id}/toggle-status', [WarehouseController::class, 'toggleStatus'])
        ->middleware('permission:WAREHOUSE_STORES_UPDATE')
        ->name('manufacturing.warehouses.toggle-status');
    Route::resource('warehouse-products', WarehouseProductController::class)
        ->middleware('permission:WAREHOUSE_MATERIALS_READ')
        ->names('manufacturing.warehouse-products');
    Route::get('warehouse-products/{id}/transactions', [WarehouseProductController::class, 'transactions'])
        ->middleware('permission:WAREHOUSE_MATERIALS_READ')
        ->name('manufacturing.warehouse-products.transactions');
    Route::post('warehouse-products/{id}/add-quantity', [WarehouseProductController::class, 'addQuantity'])
        ->middleware('permission:WAREHOUSE_MATERIALS_UPDATE')
        ->name('manufacturing.warehouse-products.add-quantity');
    Route::post('warehouse-products/{id}/change-status', [WarehouseProductController::class, 'changeStatus'])
        ->middleware('permission:WAREHOUSE_MATERIALS_UPDATE')
        ->name('manufacturing.warehouse-products.change-status');
    Route::get('materials/export', [WarehouseProductController::class, 'export'])
        ->middleware('permission:WAREHOUSE_MATERIALS_READ')
        ->name('manufacturing.materials.export');
    Route::post('materials/import', [WarehouseProductController::class, 'import'])
        ->middleware('permission:WAREHOUSE_MATERIALS_CREATE')
        ->name('manufacturing.materials.import');

    // Material Routes
    Route::get('warehouses/material/{id}', [WarehouseProductController::class, 'showMaterial'])
        ->middleware('permission:WAREHOUSE_MATERIALS_READ')
        ->name('manufacturing.warehouses.material.show');
    Route::post('warehouses/material/{id}/add-quantity', [WarehouseProductController::class, 'addMaterialQuantity'])
        ->middleware('permission:WAREHOUSE_MATERIALS_UPDATE')
        ->name('manufacturing.warehouses.material.add-quantity');
    Route::resource('delivery-notes', DeliveryNoteController::class)
        ->middleware('permission:WAREHOUSE_DELIVERY_NOTES_READ')
        ->names('manufacturing.delivery-notes');
    Route::put('delivery-notes/{id}/toggle-status', [DeliveryNoteController::class, 'toggleStatus'])
        ->middleware('permission:WAREHOUSE_DELIVERY_NOTES_UPDATE')
        ->name('manufacturing.delivery-notes.toggle-status');
    Route::put('delivery-notes/{id}/change-status', [DeliveryNoteController::class, 'changeStatus'])
        ->middleware('permission:WAREHOUSE_DELIVERY_NOTES_UPDATE')
        ->name('manufacturing.delivery-notes.change-status');
    Route::put('delivery-notes/{id}/update-status', [DeliveryNoteController::class, 'updateStatus'])
        ->middleware('permission:WAREHOUSE_DELIVERY_NOTES_UPDATE')
        ->name('manufacturing.delivery-notes.update-status');
    Route::resource('purchase-invoices', PurchaseInvoiceController::class)
        ->middleware('permission:WAREHOUSE_PURCHASE_INVOICES_READ')
        ->names('manufacturing.purchase-invoices');
    Route::put('purchase-invoices/{id}/update-status', [PurchaseInvoiceController::class, 'updateStatus'])
        ->middleware('permission:WAREHOUSE_PURCHASE_INVOICES_UPDATE')
        ->name('manufacturing.purchase-invoices.update-status');
    Route::resource('suppliers', SupplierController::class)
        ->middleware('permission:WAREHOUSE_SUPPLIERS_READ')
        ->names('manufacturing.suppliers');
    Route::put('suppliers/{id}/toggle-status', [SupplierController::class, 'toggleStatus'])
        ->middleware('permission:WAREHOUSE_SUPPLIERS_UPDATE')
        ->name('manufacturing.suppliers.toggle-status');
    Route::get('suppliers/{id}/invoices', [SupplierController::class, 'getInvoices'])
        ->middleware('permission:WAREHOUSE_SUPPLIERS_READ')
        ->name('manufacturing.suppliers.invoices');
    Route::get('suppliers/{id}/delivery-notes', [SupplierController::class, 'getDeliveryNotes'])
        ->middleware('permission:WAREHOUSE_SUPPLIERS_READ')
        ->name('manufacturing.suppliers.delivery-notes');
    Route::resource('additives', AdditiveController::class)
        ->middleware('permission:WAREHOUSE_MATERIALS_READ')
        ->names('manufacturing.additives');

    // Warehouse Settings Routes
    Route::get('warehouse-settings', [WarehouseSettingsController::class, 'index'])
        ->middleware('permission:MENU_WAREHOUSE_SETTINGS')
        ->name('manufacturing.warehouse-settings.index');

    // Categories Manual Routes
    Route::get('warehouse-settings/categories', [WarehouseSettingsController::class, 'categoriesIndex'])
        ->middleware('permission:MENU_WAREHOUSE_SETTINGS')
        ->name('manufacturing.warehouse-settings.categories.index');
    Route::get('warehouse-settings/categories/create', [WarehouseSettingsController::class, 'categoriesCreate'])
        ->middleware('permission:MENU_WAREHOUSE_SETTINGS')
        ->name('manufacturing.warehouse-settings.categories.create');
    Route::post('warehouse-settings/categories', [WarehouseSettingsController::class, 'categoriesStore'])
        ->middleware('permission:MENU_WAREHOUSE_SETTINGS')
        ->name('manufacturing.warehouse-settings.categories.store');
    Route::get('warehouse-settings/categories/{id}', [WarehouseSettingsController::class, 'categoriesShow'])
        ->middleware('permission:MENU_WAREHOUSE_SETTINGS')
        ->name('manufacturing.warehouse-settings.categories.show');
    Route::get('warehouse-settings/categories/{id}/edit', [WarehouseSettingsController::class, 'categoriesEdit'])
        ->middleware('permission:MENU_WAREHOUSE_SETTINGS')
        ->name('manufacturing.warehouse-settings.categories.edit');
    Route::put('warehouse-settings/categories/{id}', [WarehouseSettingsController::class, 'categoriesUpdate'])
        ->middleware('permission:MENU_WAREHOUSE_SETTINGS')
        ->name('manufacturing.warehouse-settings.categories.update');
    Route::delete('warehouse-settings/categories/{id}', [WarehouseSettingsController::class, 'categoriesDestroy'])
        ->middleware('permission:MENU_WAREHOUSE_SETTINGS')
        ->name('manufacturing.warehouse-settings.categories.destroy');

    // Units Resource Routes
    Route::resource('warehouse-settings/units', UnitController::class)
        ->middleware('permission:WAREHOUSE_UNITS_READ')
        ->names('manufacturing.warehouse-settings.units');
    Route::put('warehouse-settings/units/{id}/toggle-status', [UnitController::class, 'toggleStatus'])
        ->middleware('permission:WAREHOUSE_UNITS_UPDATE')
        ->name('manufacturing.warehouse-settings.units.toggle-status');

    // Material Types Resource Routes
    Route::resource('warehouse-settings/material-types', MaterialTypeController::class)
        ->middleware('permission:WAREHOUSE_MATERIAL_TYPES_READ')
        ->names('manufacturing.warehouse-settings.material-types');
    Route::put('warehouse-settings/material-types/{id}/toggle-status', [MaterialTypeController::class, 'toggleStatus'])
        ->middleware('permission:WAREHOUSE_MATERIAL_TYPES_UPDATE')
        ->name('manufacturing.warehouse-settings.material-types.toggle-status');

    // ========== تسجيل البضاعة ==========
    Route::prefix('warehouse/registration')->middleware('permission:WAREHOUSE_REGISTRATION_READ')->group(function () {
        Route::get('/', [WarehouseRegistrationController::class, 'pending'])->name('manufacturing.warehouse.registration.pending');
        Route::get('create/{deliveryNote}', [WarehouseRegistrationController::class, 'create'])
            ->middleware('permission:WAREHOUSE_REGISTRATION_CREATE')
            ->name('manufacturing.warehouse.registration.create');
        Route::post('store/{deliveryNote}', [WarehouseRegistrationController::class, 'store'])
            ->middleware('permission:WAREHOUSE_REGISTRATION_CREATE')
            ->name('manufacturing.warehouse.registration.store');
        Route::get('show/{deliveryNote}', [WarehouseRegistrationController::class, 'show'])->name('manufacturing.warehouse.registration.show');

        // النقل للإنتاج مع اختيار الكمية
        Route::get('transfer/{deliveryNote}', [WarehouseRegistrationController::class, 'showTransferForm'])
            ->middleware('permission:WAREHOUSE_REGISTRATION_TRANSFER')
            ->name('manufacturing.warehouse.registration.transfer-form');
        Route::post('transfer-to-production/{deliveryNote}', [WarehouseRegistrationController::class, 'transferToProduction'])
            ->middleware('permission:WAREHOUSE_REGISTRATION_TRANSFER')
            ->name('manufacturing.warehouse.registration.transfer-to-production');

        // صفحة باركود الإنتاج
        Route::get('production-barcode/{deliveryNote}', [WarehouseRegistrationController::class, 'showProductionBarcode'])->name('manufacturing.warehouse.registration.production-barcode');

        // النقل الفوري (لاحتفاظ الرجعية)
        Route::post('move-production/{deliveryNote}', [WarehouseRegistrationController::class, 'moveToProduction'])
            ->middleware('permission:WAREHOUSE_REGISTRATION_TRANSFER')
            ->name('manufacturing.warehouse.registration.move-production');

        Route::post('lock/{deliveryNote}', [WarehouseRegistrationController::class, 'lock'])
            ->middleware('permission:WAREHOUSE_REGISTRATION_LOCK')
            ->name('manufacturing.warehouse.registration.lock');
        Route::post('unlock/{deliveryNote}', [WarehouseRegistrationController::class, 'unlock'])
            ->middleware('permission:WAREHOUSE_REGISTRATION_UNLOCK')
            ->name('manufacturing.warehouse.registration.unlock');
    });

    // ========== التسوية ==========
    Route::prefix('warehouse/reconciliation')->middleware('permission:WAREHOUSE_RECONCILIATION_READ')->group(function () {
        Route::get('/', [ReconciliationController::class, 'index'])->name('manufacturing.warehouses.reconciliation.index');
        Route::get('show/{deliveryNote}', [ReconciliationController::class, 'show'])->name('manufacturing.warehouses.reconciliation.show');
        Route::post('link/{deliveryNote}', [ReconciliationController::class, 'link'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_UPDATE')
            ->name('manufacturing.warehouses.reconciliation.link');
        Route::post('decide/{deliveryNote}', [ReconciliationController::class, 'decide'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_UPDATE')
            ->name('manufacturing.warehouses.reconciliation.decide');
        Route::get('history', [ReconciliationController::class, 'history'])->name('manufacturing.warehouses.reconciliation.history');
        Route::get('supplier-report', [ReconciliationController::class, 'supplierReport'])->name('manufacturing.warehouses.reconciliation.supplier-report');

        // ربط الفاتورة المتأخرة
        Route::get('link-invoice', [ReconciliationController::class, 'showLinkInvoice'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_LINK_INVOICE')
            ->name('manufacturing.warehouses.reconciliation.link-invoice');
        Route::post('link-invoice', [ReconciliationController::class, 'storeLinkInvoice'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_LINK_INVOICE')
            ->name('manufacturing.warehouses.reconciliation.link-invoice.store');
        Route::get('link-invoice/{reconciliation}/edit', [ReconciliationController::class, 'editLinkInvoice'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_LINK_INVOICE')
            ->name('manufacturing.warehouses.reconciliation.link-invoice.edit');
        Route::put('link-invoice/{reconciliation}', [ReconciliationController::class, 'updateLinkInvoice'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_LINK_INVOICE')
            ->name('manufacturing.warehouses.reconciliation.link-invoice.update');
        Route::delete('link-invoice/{reconciliation}', [ReconciliationController::class, 'deleteLinkInvoice'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_LINK_INVOICE')
            ->name('manufacturing.warehouses.reconciliation.link-invoice.delete');

        // ✅ لوحة التحكم الإدارية
        Route::get('management', [ReconciliationController::class, 'showManagement'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_MANAGEMENT')
            ->name('manufacturing.warehouses.reconciliation.management');

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
        Route::patch('update-delivery-note/{deliveryNote}', [ReconciliationController::class, 'updateDeliveryNote'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_UPDATE')
            ->name('manufacturing.warehouses.reconciliation.update-delivery-note');
        Route::patch('update-invoice/{purchaseInvoice}', [ReconciliationController::class, 'updateInvoice'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_UPDATE')
            ->name('manufacturing.warehouses.reconciliation.update-invoice');
        Route::patch('update-reconciliation/{reconciliationLog}', [ReconciliationController::class, 'updateReconciliation'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_UPDATE')
            ->name('manufacturing.warehouses.reconciliation.update-reconciliation');
        Route::delete('delete-delivery-note/{deliveryNote}', [ReconciliationController::class, 'deleteDeliveryNote'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_DELETE')
            ->name('manufacturing.warehouses.reconciliation.delete-delivery-note');
        Route::delete('delete-invoice/{purchaseInvoice}', [ReconciliationController::class, 'deleteInvoice'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_DELETE')
            ->name('manufacturing.warehouses.reconciliation.delete-invoice');
        Route::delete('delete-reconciliation/{reconciliationLog}', [ReconciliationController::class, 'deleteReconciliation'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_DELETE')
            ->name('manufacturing.warehouses.reconciliation.delete-reconciliation');
        Route::delete('delete-movement/{materialMovement}', [ReconciliationController::class, 'deleteMovement'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_DELETE')
            ->name('manufacturing.warehouses.reconciliation.delete-movement');

        // ✅ صفحات التعديل (Edit Pages)
        Route::get('edit-delivery-note/{deliveryNote}', [ReconciliationController::class, 'editDeliveryNote'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_UPDATE')
            ->name('manufacturing.warehouses.reconciliation.edit-delivery-note');
        Route::get('edit-invoice/{purchaseInvoice}', [ReconciliationController::class, 'editInvoice'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_UPDATE')
            ->name('manufacturing.warehouses.reconciliation.edit-invoice');
        Route::get('edit-reconciliation/{reconciliationLog}', [ReconciliationController::class, 'editReconciliation'])
            ->middleware('permission:WAREHOUSE_RECONCILIATION_UPDATE')
            ->name('manufacturing.warehouses.reconciliation.edit-reconciliation');
    });

    // ========== سجل حركات المواد ==========
    Route::prefix('warehouse/movements')->middleware('permission:WAREHOUSE_MOVEMENTS_READ')->group(function () {
        Route::get('/', [MaterialMovementController::class, 'index'])->name('manufacturing.warehouse.movements.index');
        Route::get('show/{movement}', [MaterialMovementController::class, 'show'])
            ->middleware('permission:WAREHOUSE_MOVEMENTS_DETAILS')
            ->name('manufacturing.warehouse.movements.show');
    });

    // API endpoint for movement details
    Route::get('material-movements/{id}', [MaterialMovementController::class, 'getDetails'])
        ->middleware('permission:WAREHOUSE_MOVEMENTS_DETAILS')
        ->name('manufacturing.material-movements.details');

    // ========== تأكيدات الإنتاج ==========
    Route::prefix('manufacturing/production/confirmations')->name('manufacturing.production.confirmations.')
        ->middleware('permission:PRODUCTION_CONFIRMATIONS_READ')->group(function () {
        Route::get('pending', [ProductionConfirmationController::class, 'pendingConfirmations'])->name('pending');
        Route::get('/', [ProductionConfirmationController::class, 'index'])->name('index');
        Route::get('{id}', [ProductionConfirmationController::class, 'show'])->name('show');
        Route::post('{id}/confirm', [ProductionConfirmationController::class, 'confirm'])
            ->middleware('permission:PRODUCTION_CONFIRMATIONS_CONFIRM')
            ->name('confirm');
        Route::post('{id}/reject', [ProductionConfirmationController::class, 'reject'])
            ->middleware('permission:PRODUCTION_CONFIRMATIONS_REJECT')
            ->name('reject');
        Route::get('{id}/details', [ProductionConfirmationController::class, 'getDetails'])
            ->middleware('permission:PRODUCTION_CONFIRMATIONS_VIEW_DETAILS')
            ->name('details');
    });

    // ========== تقارير وإحصائيات المستودع ==========
    Route::prefix('warehouse/reports')->middleware('permission:MENU_WAREHOUSE_REPORTS')->group(function () {
        Route::get('/', [WarehouseReportsController::class, 'index'])
            ->middleware('permission:MENU_WAREHOUSE_REPORTS')
            ->name('manufacturing.warehouse-reports.index');
        Route::get('comprehensive', [WarehouseReportsController::class, 'comprehensiveReport'])
            ->middleware('permission:MENU_WAREHOUSE_REPORTS')
            ->name('manufacturing.warehouse-reports.comprehensive');
        Route::get('warehouses-statistics', [WarehouseReportsController::class, 'warehousesStatistics'])
            ->middleware('permission:MENU_WAREHOUSE_REPORTS')
            ->name('manufacturing.warehouse-reports.warehouses-statistics');
        Route::get('materials', [WarehouseReportsController::class, 'materialsReport'])
            ->middleware('permission:MENU_WAREHOUSE_REPORTS')
            ->name('manufacturing.warehouse-reports.materials');
        Route::get('delivery-notes', [WarehouseReportsController::class, 'deliveryNotesReport'])
            ->middleware('permission:MENU_WAREHOUSE_REPORTS')
            ->name('manufacturing.warehouse-reports.delivery-notes');
        Route::get('purchase-invoices', [WarehouseReportsController::class, 'purchaseInvoicesReport'])
            ->middleware('permission:MENU_WAREHOUSE_REPORTS')
            ->name('manufacturing.warehouse-reports.purchase-invoices');
        Route::get('additives', [WarehouseReportsController::class, 'additivesReport'])
            ->middleware('permission:MENU_WAREHOUSE_REPORTS')
            ->name('manufacturing.warehouse-reports.additives');
        Route::get('suppliers', [WarehouseReportsController::class, 'suppliersReport'])
            ->middleware('permission:MENU_WAREHOUSE_REPORTS')
            ->name('manufacturing.warehouse-reports.suppliers');
        Route::get('movements', [WarehouseReportsController::class, 'movementsReport'])
            ->middleware('permission:MENU_WAREHOUSE_REPORTS')
            ->name('manufacturing.warehouse-reports.movements');

        // ========== تقرير القطع قيد التشغيل (WIP) ==========
        Route::get('reports/wip', [\Modules\Manufacturing\Http\Controllers\WorkInProgressController::class, 'index'])
            ->middleware('permission:MENU_WAREHOUSE_REPORTS')
            ->name('manufacturing.reports.wip');
        Route::get('reports/wip/stats', [\Modules\Manufacturing\Http\Controllers\WorkInProgressController::class, 'stats'])
            ->middleware('permission:MENU_WAREHOUSE_REPORTS')
            ->name('manufacturing.reports.wip.stats');
    });

    // Production Stages Routes
    Route::resource('stage1', Stage1Controller::class)
        ->middleware('permission:STAGE1_STANDS_READ')
        ->names('manufacturing.stage1');
    Route::resource('stage2', Stage2Controller::class)
        ->middleware('permission:STAGE2_PROCESSING_READ')
        ->names('manufacturing.stage2');
    Route::resource('stage3', Stage3Controller::class)
        ->middleware('permission:STAGE3_COILS_READ')
        ->names('manufacturing.stage3');
    Route::resource('stage4', Stage4Controller::class)
        ->middleware('permission:STAGE4_PACKAGING_READ')
        ->names('manufacturing.stage4');

    // Stage 1 Additional Routes
    Route::get('stage1/barcode/scan', [Stage1Controller::class, 'barcodeScan'])
        ->middleware('permission:STAGE1_BARCODE_SCAN')
        ->name('manufacturing.stage1.barcode-scan');
    Route::post('stage1/barcode/process', [Stage1Controller::class, 'processBarcodeAction'])
        ->middleware('permission:STAGE1_STANDS_UPDATE')
        ->name('manufacturing.stage1.process-barcode');
    Route::get('stage1/waste/tracking', [Stage1Controller::class, 'wasteTracking'])
        ->middleware('permission:STAGE1_WASTE_TRACKING')
        ->name('manufacturing.stage1.waste-tracking');
    Route::post('stage1/store-single', [Stage1Controller::class, 'storeSingle'])
        ->middleware('permission:STAGE1_STANDS_CREATE')
        ->name('manufacturing.stage1.store-single');
    Route::get('material-batches/get-by-barcode/{barcode}', [Stage1Controller::class, 'getMaterialByBarcode'])
        ->middleware('permission:STAGE1_STANDS_READ')
        ->name('manufacturing.material-batch.get-by-barcode');

    // Stage 2 Additional Routes
    Route::post('stage2/store-single', [Stage2Controller::class, 'storeSingle'])
        ->middleware('permission:STAGE2_PROCESSING_CREATE')
        ->name('manufacturing.stage2.store-single');
    Route::get('stage2/complete/processing', [Stage2Controller::class, 'completeProcessing'])
        ->middleware('permission:STAGE2_PROCESSING_READ')
        ->name('manufacturing.stage2.complete-processing');
    Route::put('stage2/complete', [Stage2Controller::class, 'completeAction'])
        ->middleware('permission:STAGE2_PROCESSING_UPDATE')
        ->name('manufacturing.stage2.complete');
    Route::get('stage2/waste/statistics', [Stage2Controller::class, 'wasteStatistics'])
        ->middleware('permission:STAGE2_PROCESSING_READ')
        ->name('manufacturing.stage2.waste-statistics');

    // Stage 3 Additional Routes
    Route::post('stage3/store-single', [Stage3Controller::class, 'storeSingle'])
        ->middleware('permission:STAGE3_COILS_CREATE')
        ->name('manufacturing.stage3.store-single');
    Route::get('stage3/get-stage2-by-barcode/{barcode}', [Stage3Controller::class, 'getByBarcode'])
        ->middleware('permission:STAGE3_COILS_READ')
        ->name('manufacturing.stage3.get-stage2-by-barcode');
    Route::get('stage3/add-dye-plastic', [Stage3Controller::class, 'addDyePlastic'])
        ->middleware('permission:STAGE3_COILS_READ')
        ->name('manufacturing.stage3.add-dye-plastic');
    Route::post('stage3/add-dye', [Stage3Controller::class, 'addDyeAction'])
        ->middleware('permission:STAGE3_COILS_UPDATE')
        ->name('manufacturing.stage3.add-dye');
    Route::get('stage3/completed-coils', [Stage3Controller::class, 'completedCoils'])
        ->middleware('permission:STAGE3_COILS_READ')
        ->name('manufacturing.stage3.completed-coils');

    // Stage 4 Additional Routes
    Route::post('stage4/store-single', [Stage4Controller::class, 'storeSingle'])
        ->middleware('permission:STAGE4_PACKAGING_CREATE')
        ->name('manufacturing.stage4.store-single');
    Route::get('stage4/get-lafaf-by-barcode/{barcode}', [Stage4Controller::class, 'getByBarcode'])
        ->middleware('permission:STAGE4_PACKAGING_READ')
        ->name('manufacturing.stage4.get-lafaf-by-barcode');

    // Shift Handover Routes
    Route::resource('shift-handovers', ShiftHandoverController::class)
        ->middleware('permission:SHIFT_HANDOVERS_READ')
        ->names('manufacturing.shift-handovers');
    Route::post('shift-handovers/{id}/approve', [ShiftHandoverController::class, 'approve'])
        ->middleware('permission:SHIFT_HANDOVERS_APPROVE')
        ->name('manufacturing.shift-handovers.approve');
    Route::post('shift-handovers/{id}/reject', [ShiftHandoverController::class, 'reject'])
        ->middleware('permission:SHIFT_HANDOVERS_REJECT')
        ->name('manufacturing.shift-handovers.reject');
    Route::get('shift-handovers/api/user-shifts', [ShiftHandoverController::class, 'getUserActiveShifts'])
        ->middleware('permission:SHIFT_HANDOVERS_READ')
        ->name('manufacturing.shift-handovers.api.user-shifts');
    Route::get('shift-handovers/api/available-workers', [ShiftHandoverController::class, 'getAvailableWorkers'])
        ->middleware('permission:SHIFT_HANDOVERS_READ')
        ->name('manufacturing.shift-handovers.api.available-workers');
    Route::get('shift-handovers/api/history', [ShiftHandoverController::class, 'getHandoverHistory'])
        ->middleware('permission:SHIFT_HANDOVERS_READ')
        ->name('manufacturing.shift-handovers.api.history');

    // Quality Management Routes
    Route::get('quality', [QualityController::class, 'index'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.quality.index');
    Route::get('quality/waste-report', [QualityController::class, 'wasteReport'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.quality.waste-report');
    Route::get('quality/quality-monitoring', [QualityController::class, 'qualityMonitoring'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.quality.quality-monitoring');
    Route::get('quality/downtime-tracking', [QualityController::class, 'downtimeTracking'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.quality.downtime-tracking');
    Route::get('quality/waste-limits', [QualityController::class, 'wasteLimits'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.quality.waste-limits');

    // Quality Check Routes
    Route::get('quality/quality-create', [QualityController::class, 'qualityCreate'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.quality.quality-create');
    Route::get('quality/quality-show/{id}', [QualityController::class, 'qualityShow'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.quality.quality-show');
    Route::get('quality/quality-edit/{id}', [QualityController::class, 'qualityEdit'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.quality.quality-edit');

    // Downtime Tracking Routes
    Route::get('quality/downtime-create', [QualityController::class, 'downtimeCreate'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.quality.downtime-create');
    Route::get('quality/downtime-show/{id}', [QualityController::class, 'downtimeShow'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.quality.downtime-show');
    Route::get('quality/downtime-edit/{id}', [QualityController::class, 'downtimeEdit'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.quality.downtime-edit');

    // Production Tracking Routes
    Route::get('production-tracking/scan', [QualityController::class, 'productionTrackingScan'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.production-tracking.scan');
    Route::post('production-tracking/process', [QualityController::class, 'processProductionTracking'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.production-tracking.process');
    Route::get('production-tracking/report', [QualityController::class, 'productionTrackingReport'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.production-tracking.report');

    // Iron Journey Tracking Routes
    Route::get('iron-journey', [QualityController::class, 'ironJourney'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.iron-journey');
    Route::get('iron-journey/show', [QualityController::class, 'showIronJourney'])
        ->middleware('permission:MENU_QUALITY')
        ->name('manufacturing.iron-journey.show');

}); // End of Authentication Middleware
