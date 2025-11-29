<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdatePermissionsTranslations extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            // Dashboard
            'MENU_DASHBOARD' => ['display_name_en' => 'Dashboard', 'group_name_en' => 'Dashboard', 'description_en' => 'Show Dashboard Menu'],

            // Warehouse Main
            'MENU_WAREHOUSE' => ['display_name_en' => 'Warehouse', 'group_name_en' => 'Warehouse', 'description_en' => 'Show Main Warehouse Menu'],

            // Materials
            'MENU_WAREHOUSE_MATERIALS' => ['display_name_en' => 'Raw Materials', 'group_name_en' => 'Warehouse - Raw Materials', 'description_en' => 'Show Raw Materials Menu'],
            'WAREHOUSE_MATERIALS_READ' => ['display_name_en' => 'View Raw Materials', 'group_name_en' => 'Warehouse - Raw Materials', 'description_en' => 'View Raw Materials'],
            'WAREHOUSE_MATERIALS_CREATE' => ['display_name_en' => 'Add Raw Materials', 'group_name_en' => 'Warehouse - Raw Materials', 'description_en' => 'Add New Raw Materials'],
            'WAREHOUSE_MATERIALS_UPDATE' => ['display_name_en' => 'Edit Raw Materials', 'group_name_en' => 'Warehouse - Raw Materials', 'description_en' => 'Edit Raw Materials'],
            'WAREHOUSE_MATERIALS_DELETE' => ['display_name_en' => 'Delete Raw Materials', 'group_name_en' => 'Warehouse - Raw Materials', 'description_en' => 'Delete Raw Materials'],

            // Stores
            'MENU_WAREHOUSE_STORES' => ['display_name_en' => 'Stores', 'group_name_en' => 'Warehouse - Stores', 'description_en' => 'Show Stores Menu'],
            'WAREHOUSE_STORES_READ' => ['display_name_en' => 'View Stores', 'group_name_en' => 'Warehouse - Stores', 'description_en' => 'View Stores'],
            'WAREHOUSE_STORES_CREATE' => ['display_name_en' => 'Add Store', 'group_name_en' => 'Warehouse - Stores', 'description_en' => 'Add New Store'],
            'WAREHOUSE_STORES_UPDATE' => ['display_name_en' => 'Edit Store', 'group_name_en' => 'Warehouse - Stores', 'description_en' => 'Edit Store Data'],
            'WAREHOUSE_STORES_DELETE' => ['display_name_en' => 'Delete Store', 'group_name_en' => 'Warehouse - Stores', 'description_en' => 'Delete Store'],

            // Delivery Notes
            'MENU_WAREHOUSE_DELIVERY_NOTES' => ['display_name_en' => 'Delivery Notes', 'group_name_en' => 'Warehouse - Delivery Notes', 'description_en' => 'Show Delivery Notes Menu'],
            'WAREHOUSE_DELIVERY_NOTES_READ' => ['display_name_en' => 'View Delivery Notes', 'group_name_en' => 'Warehouse - Delivery Notes', 'description_en' => 'View Delivery Notes'],
            'WAREHOUSE_DELIVERY_NOTES_CREATE' => ['display_name_en' => 'Create Delivery Note', 'group_name_en' => 'Warehouse - Delivery Notes', 'description_en' => 'Create New Delivery Note'],
            'WAREHOUSE_DELIVERY_NOTES_UPDATE' => ['display_name_en' => 'Edit Delivery Note', 'group_name_en' => 'Warehouse - Delivery Notes', 'description_en' => 'Edit Delivery Notes'],
            'WAREHOUSE_DELIVERY_NOTES_DELETE' => ['display_name_en' => 'Delete Delivery Note', 'group_name_en' => 'Warehouse - Delivery Notes', 'description_en' => 'Delete Delivery Notes'],

            // Purchase Invoices
            'WAREHOUSE_PURCHASE_INVOICES_READ' => ['display_name_en' => 'View Purchase Invoices', 'group_name_en' => 'Warehouse - Purchase Invoices', 'description_en' => 'View Purchase Invoices'],
            'WAREHOUSE_PURCHASE_INVOICES_CREATE' => ['display_name_en' => 'Create Purchase Invoice', 'group_name_en' => 'Warehouse - Purchase Invoices', 'description_en' => 'Create New Purchase Invoice'],
            'WAREHOUSE_PURCHASE_INVOICES_UPDATE' => ['display_name_en' => 'Edit Purchase Invoice', 'group_name_en' => 'Warehouse - Purchase Invoices', 'description_en' => 'Edit Purchase Invoices'],
            'WAREHOUSE_PURCHASE_INVOICES_DELETE' => ['display_name_en' => 'Delete Purchase Invoice', 'group_name_en' => 'Warehouse - Purchase Invoices', 'description_en' => 'Delete Purchase Invoices'],

            // Suppliers
            'MENU_WAREHOUSE_SUPPLIERS' => ['display_name_en' => 'Suppliers', 'group_name_en' => 'Warehouse - Suppliers', 'description_en' => 'Show Suppliers Menu'],
            'WAREHOUSE_SUPPLIERS_READ' => ['display_name_en' => 'View Suppliers', 'group_name_en' => 'Warehouse - Suppliers', 'description_en' => 'View Suppliers'],
            'WAREHOUSE_SUPPLIERS_CREATE' => ['display_name_en' => 'Add Supplier', 'group_name_en' => 'Warehouse - Suppliers', 'description_en' => 'Add New Supplier'],
            'WAREHOUSE_SUPPLIERS_UPDATE' => ['display_name_en' => 'Edit Supplier Data', 'group_name_en' => 'Warehouse - Suppliers', 'description_en' => 'Edit Supplier Data'],
            'WAREHOUSE_SUPPLIERS_DELETE' => ['display_name_en' => 'Delete Supplier', 'group_name_en' => 'Warehouse - Suppliers', 'description_en' => 'Delete Supplier'],

            // Material Types
            'MENU_WAREHOUSE_MATERIAL_TYPES' => ['display_name_en' => 'Material Types', 'group_name_en' => 'Warehouse - Material Types', 'description_en' => 'Show Material Types Menu'],
            'WAREHOUSE_MATERIAL_TYPES_READ' => ['display_name_en' => 'View Material Types', 'group_name_en' => 'Warehouse - Material Types', 'description_en' => 'View Material Types'],
            'WAREHOUSE_MATERIAL_TYPES_CREATE' => ['display_name_en' => 'Add Material Type', 'group_name_en' => 'Warehouse - Material Types', 'description_en' => 'Add New Material Type'],
            'WAREHOUSE_MATERIAL_TYPES_UPDATE' => ['display_name_en' => 'Edit Material Type', 'group_name_en' => 'Warehouse - Material Types', 'description_en' => 'Edit Material Types'],
            'WAREHOUSE_MATERIAL_TYPES_DELETE' => ['display_name_en' => 'Delete Material Type', 'group_name_en' => 'Warehouse - Material Types', 'description_en' => 'Delete Material Types'],

            // Units
            'MENU_WAREHOUSE_UNITS' => ['display_name_en' => 'Units of Measurement', 'group_name_en' => 'Warehouse - Units', 'description_en' => 'Show Units Menu'],
            'WAREHOUSE_UNITS_READ' => ['display_name_en' => 'View Units', 'group_name_en' => 'Warehouse - Units', 'description_en' => 'View Units'],
            'WAREHOUSE_UNITS_CREATE' => ['display_name_en' => 'Add Unit', 'group_name_en' => 'Warehouse - Units', 'description_en' => 'Add New Unit'],
            'WAREHOUSE_UNITS_UPDATE' => ['display_name_en' => 'Edit Unit', 'group_name_en' => 'Warehouse - Units', 'description_en' => 'Edit Units'],
            'WAREHOUSE_UNITS_DELETE' => ['display_name_en' => 'Delete Unit', 'group_name_en' => 'Warehouse - Units', 'description_en' => 'Delete Units'],

            // Registration
            'MENU_WAREHOUSE_REGISTRATION' => ['display_name_en' => 'Goods Registration', 'group_name_en' => 'Warehouse - Registration', 'description_en' => 'Show Registration Menu'],
            'WAREHOUSE_REGISTRATION_READ' => ['display_name_en' => 'View Registrations', 'group_name_en' => 'Warehouse - Registration', 'description_en' => 'View Registrations List'],
            'WAREHOUSE_REGISTRATION_CREATE' => ['display_name_en' => 'Add Registration', 'group_name_en' => 'Warehouse - Registration', 'description_en' => 'Create New Registration'],
            'WAREHOUSE_REGISTRATION_UPDATE' => ['display_name_en' => 'Edit Registration', 'group_name_en' => 'Warehouse - Registration', 'description_en' => 'Edit Registrations'],
            'WAREHOUSE_REGISTRATION_LOCK' => ['display_name_en' => 'Lock Registration', 'group_name_en' => 'Warehouse - Registration', 'description_en' => 'Lock Registrations'],
            'WAREHOUSE_REGISTRATION_UNLOCK' => ['display_name_en' => 'Unlock Registration', 'group_name_en' => 'Warehouse - Registration', 'description_en' => 'Unlock Locked Registrations'],
            'WAREHOUSE_REGISTRATION_TRANSFER' => ['display_name_en' => 'Transfer to Production', 'group_name_en' => 'Warehouse - Registration', 'description_en' => 'Transfer Goods to Production'],

            // Reconciliation
            'MENU_WAREHOUSE_RECONCILIATION' => ['display_name_en' => 'Reconciliation', 'group_name_en' => 'Warehouse - Reconciliation', 'description_en' => 'Show Reconciliation Menu'],
            'WAREHOUSE_RECONCILIATION_READ' => ['display_name_en' => 'View Reconciliation', 'group_name_en' => 'Warehouse - Reconciliation', 'description_en' => 'View Reconciliation Records'],
            'WAREHOUSE_RECONCILIATION_CREATE' => ['display_name_en' => 'Create Reconciliation', 'group_name_en' => 'Warehouse - Reconciliation', 'description_en' => 'Create New Reconciliation Record'],
            'WAREHOUSE_RECONCILIATION_UPDATE' => ['display_name_en' => 'Edit Reconciliation', 'group_name_en' => 'Warehouse - Reconciliation', 'description_en' => 'Edit Reconciliation Record'],
            'WAREHOUSE_RECONCILIATION_DELETE' => ['display_name_en' => 'Delete Reconciliation', 'group_name_en' => 'Warehouse - Reconciliation', 'description_en' => 'Delete Reconciliation Record'],
            'WAREHOUSE_RECONCILIATION_PRINT' => ['display_name_en' => 'Print Reconciliation', 'group_name_en' => 'Warehouse - Reconciliation', 'description_en' => 'Print Reconciliation Report'],
            'WAREHOUSE_RECONCILIATION_TRANSFER' => ['display_name_en' => 'Transfer Reconciliation', 'group_name_en' => 'Warehouse - Reconciliation', 'description_en' => 'Transfer Reconciliation to Production'],

            // Movements
            'MENU_WAREHOUSE_MOVEMENTS' => ['display_name_en' => 'Material Movements', 'group_name_en' => 'Warehouse - Movements', 'description_en' => 'Show Material Movements Menu'],
            'WAREHOUSE_MOVEMENTS_READ' => ['display_name_en' => 'View Movements', 'group_name_en' => 'Warehouse - Movements', 'description_en' => 'View Material Movements'],
            'WAREHOUSE_MOVEMENTS_CREATE' => ['display_name_en' => 'Create Movement', 'group_name_en' => 'Warehouse - Movements', 'description_en' => 'Create New Material Movement'],

            // Settings & Reports
            'MENU_WAREHOUSE_SETTINGS' => ['display_name_en' => 'Warehouse Settings', 'group_name_en' => 'Warehouse - Settings', 'description_en' => 'Show Warehouse Settings Menu'],
            'MENU_WAREHOUSE_REPORTS' => ['display_name_en' => 'Warehouse Reports', 'group_name_en' => 'Warehouse - Reports', 'description_en' => 'Show Warehouse Reports Menu'],

            // Production
            'MENU_PRODUCTION' => ['display_name_en' => 'Production', 'group_name_en' => 'Production', 'description_en' => 'Show Production Main Menu'],

            // Confirmations
            'MENU_PRODUCTION_CONFIRMATIONS' => ['display_name_en' => 'Production Confirmations', 'group_name_en' => 'Production - Confirmations', 'description_en' => 'Show Confirmations Menu'],
            'PRODUCTION_CONFIRMATIONS_READ' => ['display_name_en' => 'View Confirmations', 'group_name_en' => 'Production - Confirmations', 'description_en' => 'View Production Confirmations'],
            'PRODUCTION_CONFIRMATIONS_CREATE' => ['display_name_en' => 'Create Confirmation', 'group_name_en' => 'Production - Confirmations', 'description_en' => 'Create New Production Confirmation'],
            'PRODUCTION_CONFIRMATIONS_UPDATE' => ['display_name_en' => 'Edit Confirmation', 'group_name_en' => 'Production - Confirmations', 'description_en' => 'Edit Production Confirmation'],
            'PRODUCTION_CONFIRMATIONS_DELETE' => ['display_name_en' => 'Delete Confirmation', 'group_name_en' => 'Production - Confirmations', 'description_en' => 'Delete Production Confirmation'],

            // Stage 1 - Stands
            'MENU_PRODUCTION_STAGE1_STANDS' => ['display_name_en' => 'Stage 1: Stands', 'group_name_en' => 'Production - Stage 1', 'description_en' => 'Show Stands Menu'],
            'PRODUCTION_STAGE1_STANDS_READ' => ['display_name_en' => 'View Stands', 'group_name_en' => 'Production - Stage 1', 'description_en' => 'View Stands'],
            'PRODUCTION_STAGE1_STANDS_CREATE' => ['display_name_en' => 'Create Stand', 'group_name_en' => 'Production - Stage 1', 'description_en' => 'Create New Stand'],
            'PRODUCTION_STAGE1_STANDS_UPDATE' => ['display_name_en' => 'Edit Stand', 'group_name_en' => 'Production - Stage 1', 'description_en' => 'Edit Stand Data'],
            'PRODUCTION_STAGE1_STANDS_DELETE' => ['display_name_en' => 'Delete Stand', 'group_name_en' => 'Production - Stage 1', 'description_en' => 'Delete Stand'],
            'PRODUCTION_STAGE1_STANDS_ACTIVATE' => ['display_name_en' => 'Activate Stand', 'group_name_en' => 'Production - Stage 1', 'description_en' => 'Activate Stand'],
            'PRODUCTION_STAGE1_STANDS_DEACTIVATE' => ['display_name_en' => 'Deactivate Stand', 'group_name_en' => 'Production - Stage 1', 'description_en' => 'Deactivate Stand'],
            'PRODUCTION_STAGE1_STANDS_PRINT' => ['display_name_en' => 'Print Stand Details', 'group_name_en' => 'Production - Stage 1', 'description_en' => 'Print Stand Details'],

            // Stage 2 - Processing
            'MENU_PRODUCTION_STAGE2_PROCESSING' => ['display_name_en' => 'Stage 2: Processing', 'group_name_en' => 'Production - Stage 2', 'description_en' => 'Show Processing Menu'],
            'PRODUCTION_STAGE2_PROCESSING_READ' => ['display_name_en' => 'View Processing', 'group_name_en' => 'Production - Stage 2', 'description_en' => 'View Processing Records'],
            'PRODUCTION_STAGE2_PROCESSING_CREATE' => ['display_name_en' => 'Create Processing', 'group_name_en' => 'Production - Stage 2', 'description_en' => 'Create New Processing Record'],
            'PRODUCTION_STAGE2_PROCESSING_UPDATE' => ['display_name_en' => 'Edit Processing', 'group_name_en' => 'Production - Stage 2', 'description_en' => 'Edit Processing Record'],
            'PRODUCTION_STAGE2_PROCESSING_DELETE' => ['display_name_en' => 'Delete Processing', 'group_name_en' => 'Production - Stage 2', 'description_en' => 'Delete Processing Record'],
            'PRODUCTION_STAGE2_PROCESSING_APPROVE' => ['display_name_en' => 'Approve Processing', 'group_name_en' => 'Production - Stage 2', 'description_en' => 'Approve Processing Record'],
            'PRODUCTION_STAGE2_PROCESSING_PRINT' => ['display_name_en' => 'Print Processing', 'group_name_en' => 'Production - Stage 2', 'description_en' => 'Print Processing Details'],

            // Stage 3 - Coils
            'MENU_PRODUCTION_STAGE3_COILS' => ['display_name_en' => 'Stage 3: Coils', 'group_name_en' => 'Production - Stage 3', 'description_en' => 'Show Coils Menu'],
            'PRODUCTION_STAGE3_COILS_READ' => ['display_name_en' => 'View Coils', 'group_name_en' => 'Production - Stage 3', 'description_en' => 'View Coil Records'],
            'PRODUCTION_STAGE3_COILS_CREATE' => ['display_name_en' => 'Create Coil', 'group_name_en' => 'Production - Stage 3', 'description_en' => 'Create New Coil Record'],
            'PRODUCTION_STAGE3_COILS_UPDATE' => ['display_name_en' => 'Edit Coil', 'group_name_en' => 'Production - Stage 3', 'description_en' => 'Edit Coil Data'],
            'PRODUCTION_STAGE3_COILS_DELETE' => ['display_name_en' => 'Delete Coil', 'group_name_en' => 'Production - Stage 3', 'description_en' => 'Delete Coil Record'],

            // Stage 4 - Packaging
            'MENU_PRODUCTION_STAGE4_PACKAGING' => ['display_name_en' => 'Stage 4: Packaging', 'group_name_en' => 'Production - Stage 4', 'description_en' => 'Show Packaging Menu'],
            'PRODUCTION_STAGE4_PACKAGING_READ' => ['display_name_en' => 'View Packaging', 'group_name_en' => 'Production - Stage 4', 'description_en' => 'View Packaging Records'],
            'PRODUCTION_STAGE4_PACKAGING_CREATE' => ['display_name_en' => 'Create Packaging', 'group_name_en' => 'Production - Stage 4', 'description_en' => 'Create New Packaging Record'],
            'PRODUCTION_STAGE4_PACKAGING_UPDATE' => ['display_name_en' => 'Edit Packaging', 'group_name_en' => 'Production - Stage 4', 'description_en' => 'Edit Packaging Data'],
            'PRODUCTION_STAGE4_PACKAGING_DELETE' => ['display_name_en' => 'Delete Packaging', 'group_name_en' => 'Production - Stage 4', 'description_en' => 'Delete Packaging Record'],

            // Tracking
            'MENU_PRODUCTION_TRACKING' => ['display_name_en' => 'Production Tracking', 'group_name_en' => 'Production - Tracking', 'description_en' => 'Show Production Tracking Menu'],
            'PRODUCTION_TRACKING_READ' => ['display_name_en' => 'View Tracking', 'group_name_en' => 'Production - Tracking', 'description_en' => 'View Production Tracking'],
            'PRODUCTION_TRACKING_DETAILS' => ['display_name_en' => 'Tracking Details', 'group_name_en' => 'Production - Tracking', 'description_en' => 'View Detailed Tracking Information'],

            // Shifts
            'MENU_SHIFTS' => ['display_name_en' => 'Shifts Management', 'group_name_en' => 'Shifts', 'description_en' => 'Show Shifts Main Menu'],
            'MENU_SHIFTS_WORKERS' => ['display_name_en' => 'Shift Workers', 'group_name_en' => 'Shifts - Workers', 'description_en' => 'Show Shift Workers Menu'],
            'SHIFTS_WORKERS_READ' => ['display_name_en' => 'View Shift Workers', 'group_name_en' => 'Shifts - Workers', 'description_en' => 'View Shift Workers Data'],
            'SHIFTS_WORKERS_CREATE' => ['display_name_en' => 'Add Worker to Shift', 'group_name_en' => 'Shifts - Workers', 'description_en' => 'Add Worker to Shift'],
            'SHIFTS_WORKERS_UPDATE' => ['display_name_en' => 'Edit Shift Worker', 'group_name_en' => 'Shifts - Workers', 'description_en' => 'Edit Shift Worker Data'],
            'SHIFTS_WORKERS_DELETE' => ['display_name_en' => 'Remove Worker from Shift', 'group_name_en' => 'Shifts - Workers', 'description_en' => 'Remove Worker from Shift'],
            'SHIFTS_WORKERS_CURRENT' => ['display_name_en' => 'Current Shift Workers', 'group_name_en' => 'Shifts - Workers', 'description_en' => 'View Current Shift Workers'],
            'SHIFTS_WORKERS_HISTORY' => ['display_name_en' => 'Shift Workers History', 'group_name_en' => 'Shifts - Workers', 'description_en' => 'View Shift Workers History'],

            // Worker Teams
            'MENU_SHIFTS_TEAMS' => ['display_name_en' => 'Worker Teams', 'group_name_en' => 'Shifts - Teams', 'description_en' => 'Show Worker Teams Menu'],
            'SHIFTS_TEAMS_READ' => ['display_name_en' => 'View Worker Teams', 'group_name_en' => 'Shifts - Teams', 'description_en' => 'View Worker Teams'],
            'SHIFTS_TEAMS_CREATE' => ['display_name_en' => 'Create Team', 'group_name_en' => 'Shifts - Teams', 'description_en' => 'Create New Worker Team'],
            'SHIFTS_TEAMS_UPDATE' => ['display_name_en' => 'Edit Team', 'group_name_en' => 'Shifts - Teams', 'description_en' => 'Edit Worker Team'],

            // Handovers
            'MENU_SHIFTS_HANDOVERS' => ['display_name_en' => 'Shift Handovers', 'group_name_en' => 'Shifts - Handovers', 'description_en' => 'Show Shift Handovers Menu'],
            'SHIFTS_HANDOVERS_READ' => ['display_name_en' => 'View Handovers', 'group_name_en' => 'Shifts - Handovers', 'description_en' => 'View Shift Handovers'],
            'SHIFTS_HANDOVERS_CREATE' => ['display_name_en' => 'Create Handover', 'group_name_en' => 'Shifts - Handovers', 'description_en' => 'Create Shift Handover'],
            'SHIFTS_HANDOVERS_UPDATE' => ['display_name_en' => 'Edit Handover', 'group_name_en' => 'Shifts - Handovers', 'description_en' => 'Edit Shift Handover'],
            'SHIFTS_HANDOVERS_DELETE' => ['display_name_en' => 'Delete Handover', 'group_name_en' => 'Shifts - Handovers', 'description_en' => 'Delete Shift Handover'],
            'SHIFTS_HANDOVERS_APPROVE' => ['display_name_en' => 'Approve Handover', 'group_name_en' => 'Shifts - Handovers', 'description_en' => 'Approve Shift Handover'],
            'SHIFTS_HANDOVERS_PRINT' => ['display_name_en' => 'Print Handover', 'group_name_en' => 'Shifts - Handovers', 'description_en' => 'Print Handover Report'],

            // Quality & Waste
            'MENU_QUALITY_WASTE' => ['display_name_en' => 'Quality & Waste', 'group_name_en' => 'Quality', 'description_en' => 'Show Quality & Waste Menu'],
            'QUALITY_WASTE_READ' => ['display_name_en' => 'View Waste Records', 'group_name_en' => 'Quality', 'description_en' => 'View Quality Waste Records'],
            'QUALITY_WASTE_CREATE' => ['display_name_en' => 'Record Waste', 'group_name_en' => 'Quality', 'description_en' => 'Record New Waste Entry'],
            'QUALITY_WASTE_UPDATE' => ['display_name_en' => 'Edit Waste Record', 'group_name_en' => 'Quality', 'description_en' => 'Edit Waste Record'],
            'QUALITY_WASTE_DELETE' => ['display_name_en' => 'Delete Waste Record', 'group_name_en' => 'Quality', 'description_en' => 'Delete Waste Record'],

            // Reports
            'MENU_REPORTS' => ['display_name_en' => 'Reports', 'group_name_en' => 'Reports', 'description_en' => 'Show Reports Main Menu'],
            'MENU_REPORTS_PRODUCTION' => ['display_name_en' => 'Production Reports', 'group_name_en' => 'Reports - Production', 'description_en' => 'Show Production Reports Menu'],
            'REPORTS_PRODUCTION_DAILY' => ['display_name_en' => 'Daily Production Report', 'group_name_en' => 'Reports - Production', 'description_en' => 'View Daily Production Report'],
            'REPORTS_PRODUCTION_MONTHLY' => ['display_name_en' => 'Monthly Production Report', 'group_name_en' => 'Reports - Production', 'description_en' => 'View Monthly Production Report'],
            'REPORTS_PRODUCTION_CUSTOM' => ['display_name_en' => 'Custom Production Report', 'group_name_en' => 'Reports - Production', 'description_en' => 'Generate Custom Production Report'],

            'MENU_REPORTS_GENERAL' => ['display_name_en' => 'General Reports', 'group_name_en' => 'Reports - General', 'description_en' => 'Show General Reports Menu'],
            'REPORTS_INVENTORY' => ['display_name_en' => 'Inventory Report', 'group_name_en' => 'Reports - General', 'description_en' => 'View Inventory Report'],
            'REPORTS_MOVEMENTS' => ['display_name_en' => 'Movements Report', 'group_name_en' => 'Reports - General', 'description_en' => 'View Material Movements Report'],

            // Management
            'MENU_MANAGEMENT' => ['display_name_en' => 'System Management', 'group_name_en' => 'Management', 'description_en' => 'Show System Management Menu'],
            'MENU_MANAGE_USERS' => ['display_name_en' => 'Manage Users', 'group_name_en' => 'User Management', 'description_en' => 'Show User Management Menu'],
            'MANAGE_USERS_READ' => ['display_name_en' => 'View Users', 'group_name_en' => 'User Management', 'description_en' => 'View Users'],
            'MANAGE_USERS_CREATE' => ['display_name_en' => 'Add User', 'group_name_en' => 'User Management', 'description_en' => 'Add New User'],
            'MANAGE_USERS_UPDATE' => ['display_name_en' => 'Edit User', 'group_name_en' => 'User Management', 'description_en' => 'Edit User Data'],
            'MANAGE_USERS_DELETE' => ['display_name_en' => 'Delete User', 'group_name_en' => 'User Management', 'description_en' => 'Delete User'],

            'MENU_MANAGE_ROLES' => ['display_name_en' => 'Manage Roles', 'group_name_en' => 'Role Management', 'description_en' => 'Show Role Management Menu'],
            'MANAGE_ROLES_READ' => ['display_name_en' => 'View Roles', 'group_name_en' => 'Role Management', 'description_en' => 'View Roles'],
            'MANAGE_ROLES_CREATE' => ['display_name_en' => 'Add Role', 'group_name_en' => 'Role Management', 'description_en' => 'Add New Role'],
            'MANAGE_ROLES_UPDATE' => ['display_name_en' => 'Edit Role', 'group_name_en' => 'Role Management', 'description_en' => 'Edit Role'],
            'MANAGE_ROLES_DELETE' => ['display_name_en' => 'Delete Role', 'group_name_en' => 'Role Management', 'description_en' => 'Delete Role'],

            // Settings
            'MENU_SETTINGS' => ['display_name_en' => 'System Settings', 'group_name_en' => 'Settings', 'description_en' => 'Show System Settings Menu'],
            'SETTINGS_GENERAL' => ['display_name_en' => 'General Settings', 'group_name_en' => 'Settings', 'description_en' => 'Manage General Settings'],
            'SETTINGS_BACKUP' => ['display_name_en' => 'Backup & Restore', 'group_name_en' => 'Settings', 'description_en' => 'Manage System Backup'],
            'SETTINGS_LOGS' => ['display_name_en' => 'System Logs', 'group_name_en' => 'Settings', 'description_en' => 'View System Logs'],
        ];

        $updatedCount = 0;
        $notFoundCount = 0;

        foreach ($translations as $name => $data) {
            $affected = DB::table('permissions')
                ->where('name', $name)
                ->update($data);

            if ($affected > 0) {
                $updatedCount++;
            } else {
                $notFoundCount++;
                $this->command->warn("Permission not found: {$name}");
            }
        }

        $this->command->info("Successfully updated {$updatedCount} permissions with English translations!");
        if ($notFoundCount > 0) {
            $this->command->warn("{$notFoundCount} permissions were not found in the database.");
        }
    }
}
