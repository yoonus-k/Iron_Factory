<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // ============================================
            // لوحة التحكم - Dashboard
            // ============================================
            ['name' => 'MENU_DASHBOARD', 'display_name' => 'لوحة التحكم', 'display_name_en' => 'Dashboard', 'group_name' => 'لوحة التحكم', 'group_name_en' => 'Dashboard', 'description' => 'إظهار قائمة لوحة التحكم', 'description_en' => 'Show Dashboard Menu'],

            // ============================================
            // المستودع - Warehouse (Parent Group)
            // ============================================
            ['name' => 'MENU_WAREHOUSE', 'display_name' => 'المستودع', 'display_name_en' => 'Warehouse', 'group_name' => 'المستودع', 'group_name_en' => 'Warehouse', 'description' => 'إظهار قائمة المستودع الرئيسية', 'description_en' => 'Show Main Warehouse Menu'],

            // ============================================
            // المواد الخام - Materials
            // ============================================
            ['name' => 'MENU_WAREHOUSE_MATERIALS', 'display_name' => 'المواد الخام', 'display_name_en' => 'Raw Materials', 'group_name' => 'المستودع - المواد الخام', 'group_name_en' => 'Warehouse - Raw Materials', 'description' => 'إظهار قائمة المواد الخام', 'description_en' => 'Show Raw Materials Menu'],
            ['name' => 'WAREHOUSE_MATERIALS_READ', 'display_name' => 'عرض المواد الخام', 'display_name_en' => 'View Raw Materials', 'group_name' => 'المستودع - المواد الخام', 'group_name_en' => 'Warehouse - Raw Materials', 'description' => 'عرض المواد الخام', 'description_en' => 'View Raw Materials'],
            ['name' => 'WAREHOUSE_MATERIALS_CREATE', 'display_name' => 'إضافة مواد خام', 'display_name_en' => 'Add Raw Materials', 'group_name' => 'المستودع - المواد الخام', 'group_name_en' => 'Warehouse - Raw Materials', 'description' => 'إضافة مواد خام جديدة', 'description_en' => 'Add New Raw Materials'],
            ['name' => 'WAREHOUSE_MATERIALS_UPDATE', 'display_name' => 'تعديل المواد الخام', 'display_name_en' => 'Edit Raw Materials', 'group_name' => 'المستودع - المواد الخام', 'group_name_en' => 'Warehouse - Raw Materials', 'description' => 'تعديل المواد الخام', 'description_en' => 'Edit Raw Materials'],
            ['name' => 'WAREHOUSE_MATERIALS_DELETE', 'display_name' => 'حذف المواد الخام', 'display_name_en' => 'Delete Raw Materials', 'group_name' => 'المستودع - المواد الخام', 'group_name_en' => 'Warehouse - Raw Materials', 'description' => 'حذف المواد الخام', 'description_en' => 'Delete Raw Materials'],

            // ============================================
            // المخازن - Stores
            // ============================================
            ['name' => 'MENU_WAREHOUSE_STORES', 'display_name' => 'المخازن', 'display_name_en' => 'Stores', 'group_name' => 'المستودع - المخازن', 'group_name_en' => 'Warehouse - Stores', 'description' => 'إظهار قائمة المخازن', 'description_en' => 'Show Stores Menu'],
            ['name' => 'WAREHOUSE_STORES_READ', 'display_name' => 'عرض المخازن', 'display_name_en' => 'View Stores', 'group_name' => 'المستودع - المخازن', 'group_name_en' => 'Warehouse - Stores', 'description' => 'عرض المخازن', 'description_en' => 'View Stores'],
            ['name' => 'WAREHOUSE_STORES_CREATE', 'display_name' => 'إضافة مخزن', 'display_name_en' => 'Add Store', 'group_name' => 'المستودع - المخازن', 'group_name_en' => 'Warehouse - Stores', 'description' => 'إضافة مخزن جديد', 'description_en' => 'Add New Store'],
            ['name' => 'WAREHOUSE_STORES_UPDATE', 'display_name' => 'تعديل المخزن', 'display_name_en' => 'Edit Store', 'group_name' => 'المستودع - المخازن', 'group_name_en' => 'Warehouse - Stores', 'description' => 'تعديل بيانات المخزن', 'description_en' => 'Edit Store Data'],
            ['name' => 'WAREHOUSE_STORES_DELETE', 'display_name' => 'حذف المخزن', 'display_name_en' => 'Delete Store', 'group_name' => 'المستودع - المخازن', 'group_name_en' => 'Warehouse - Stores', 'description' => 'حذف المخزن', 'description_en' => 'Delete Store'],

            // ============================================
            // مذكرات التسليم - Delivery Notes
            // ============================================
            ['name' => 'MENU_WAREHOUSE_DELIVERY_NOTES', 'display_name' => 'مذكرات التسليم', 'display_name_en' => 'Delivery Notes', 'group_name' => 'المستودع - مذكرات التسليم', 'group_name_en' => 'Warehouse - Delivery Notes', 'description' => 'إظهار قائمة مذكرات التسليم', 'description_en' => 'Show Delivery Notes Menu'],
            ['name' => 'WAREHOUSE_DELIVERY_NOTES_READ', 'display_name' => 'عرض مذكرات التسليم', 'display_name_en' => 'View Delivery Notes', 'group_name' => 'المستودع - مذكرات التسليم', 'group_name_en' => 'Warehouse - Delivery Notes', 'description' => 'عرض مذكرات التسليم', 'description_en' => 'View Delivery Notes'],
            ['name' => 'WAREHOUSE_DELIVERY_NOTES_CREATE', 'display_name' => 'إنشاء مذكرة تسليم', 'display_name_en' => 'Create Delivery Note', 'group_name' => 'المستودع - مذكرات التسليم', 'group_name_en' => 'Warehouse - Delivery Notes', 'description' => 'إنشاء مذكرة تسليم جديدة', 'description_en' => 'Create New Delivery Note'],
            ['name' => 'WAREHOUSE_DELIVERY_NOTES_UPDATE', 'display_name' => 'تعديل مذكرة التسليم', 'display_name_en' => 'Edit Delivery Note', 'group_name' => 'المستودع - مذكرات التسليم', 'group_name_en' => 'Warehouse - Delivery Notes', 'description' => 'تعديل مذكرات التسليم', 'description_en' => 'Edit Delivery Notes'],
            ['name' => 'WAREHOUSE_DELIVERY_NOTES_DELETE', 'display_name' => 'حذف مذكرة التسليم', 'display_name_en' => 'Delete Delivery Note', 'group_name' => 'المستودع - مذكرات التسليم', 'group_name_en' => 'Warehouse - Delivery Notes', 'description' => 'حذف مذكرات التسليم', 'description_en' => 'Delete Delivery Notes'],

            // ============================================
            // فواتير الشراء - Purchase Invoices
            // ============================================

            ['name' => 'WAREHOUSE_PURCHASE_INVOICES_READ', 'display_name' => 'عرض فواتير الشراء', 'display_name_en' => 'View Purchase Invoices', 'group_name' => 'المستودع - فواتير الشراء', 'group_name_en' => 'Warehouse - Purchase Invoices', 'description' => 'عرض فواتير الشراء', 'description_en' => 'View Purchase Invoices'],
            ['name' => 'WAREHOUSE_PURCHASE_INVOICES_CREATE', 'display_name' => 'إنشاء فاتورة شراء', 'display_name_en' => 'Create Purchase Invoice', 'group_name' => 'المستودع - فواتير الشراء', 'group_name_en' => 'Warehouse - Purchase Invoices', 'description' => 'إنشاء فاتورة شراء جديدة', 'description_en' => 'Create New Purchase Invoice'],
            ['name' => 'WAREHOUSE_PURCHASE_INVOICES_UPDATE', 'display_name' => 'تعديل فاتورة الشراء', 'display_name_en' => 'Edit Purchase Invoice', 'group_name' => 'المستودع - فواتير الشراء', 'group_name_en' => 'Warehouse - Purchase Invoices', 'description' => 'تعديل فواتير الشراء', 'description_en' => 'Edit Purchase Invoices'],
            ['name' => 'WAREHOUSE_PURCHASE_INVOICES_DELETE', 'display_name' => 'حذف فاتورة الشراء', 'display_name_en' => 'Delete Purchase Invoice', 'group_name' => 'المستودع - فواتير الشراء', 'group_name_en' => 'Warehouse - Purchase Invoices', 'description' => 'حذف فواتير الشراء', 'description_en' => 'Delete Purchase Invoices'],

            // ============================================
            // الموردين - Suppliers
            // ============================================
            ['name' => 'MENU_WAREHOUSE_SUPPLIERS', 'display_name' => 'الموردين', 'display_name_en' => 'Suppliers', 'group_name' => 'المستودع - الموردين', 'group_name_en' => 'Warehouse - Suppliers', 'description' => 'إظهار قائمة الموردين', 'description_en' => 'Show Suppliers Menu'],
            ['name' => 'WAREHOUSE_SUPPLIERS_READ', 'display_name' => 'عرض الموردين', 'display_name_en' => 'View Suppliers', 'group_name' => 'المستودع - الموردين', 'group_name_en' => 'Warehouse - Suppliers', 'description' => 'عرض الموردين', 'description_en' => 'View Suppliers'],
            ['name' => 'WAREHOUSE_SUPPLIERS_CREATE', 'display_name' => 'إضافة مورد', 'display_name_en' => 'Add Supplier', 'group_name' => 'المستودع - الموردين', 'group_name_en' => 'Warehouse - Suppliers', 'description' => 'إضافة مورد جديد', 'description_en' => 'Add New Supplier'],
            ['name' => 'WAREHOUSE_SUPPLIERS_UPDATE', 'display_name' => 'تعديل بيانات المورد', 'display_name_en' => 'Edit Supplier Data', 'group_name' => 'المستودع - الموردين', 'group_name_en' => 'Warehouse - Suppliers', 'description' => 'تعديل بيانات المورد', 'description_en' => 'Edit Supplier Data'],
            ['name' => 'WAREHOUSE_SUPPLIERS_DELETE', 'display_name' => 'حذف المورد', 'display_name_en' => 'Delete Supplier', 'group_name' => 'المستودع - الموردين', 'group_name_en' => 'Warehouse - Suppliers', 'description' => 'حذف المورد', 'description_en' => 'Delete Supplier'],

            // ============================================
            // أنواع المواد - Material Types
            // ============================================
            ['name' => 'MENU_WAREHOUSE_MATERIAL_TYPES', 'display_name' => 'أنواع المواد', 'display_name_en' => 'Material Types', 'group_name' => 'المستودع - أنواع المواد', 'group_name_en' => 'Warehouse - Material Types', 'description' => 'إظهار قائمة أنواع المواد', 'description_en' => 'Show Material Types Menu'],
            ['name' => 'WAREHOUSE_MATERIAL_TYPES_READ', 'display_name' => 'عرض أنواع المواد', 'display_name_en' => 'View Material Types', 'group_name' => 'المستودع - أنواع المواد', 'group_name_en' => 'Warehouse - Material Types', 'description' => 'عرض أنواع المواد', 'description_en' => 'View Material Types'],
            ['name' => 'WAREHOUSE_MATERIAL_TYPES_CREATE', 'display_name' => 'إضافة نوع مادة', 'display_name_en' => 'Add Material Type', 'group_name' => 'المستودع - أنواع المواد', 'group_name_en' => 'Warehouse - Material Types', 'description' => 'إضافة نوع مادة جديد', 'description_en' => 'Add New Material Type'],
            ['name' => 'WAREHOUSE_MATERIAL_TYPES_UPDATE', 'display_name' => 'تعديل نوع المادة', 'display_name_en' => 'Edit Material Type', 'group_name' => 'المستودع - أنواع المواد', 'group_name_en' => 'Warehouse - Material Types', 'description' => 'تعديل أنواع المواد', 'description_en' => 'Edit Material Types'],
            ['name' => 'WAREHOUSE_MATERIAL_TYPES_DELETE', 'display_name' => 'حذف نوع المادة', 'display_name_en' => 'Delete Material Type', 'group_name' => 'المستودع - أنواع المواد', 'group_name_en' => 'Warehouse - Material Types', 'description' => 'حذف أنواع المواد', 'description_en' => 'Delete Material Types'],

            // ============================================
            // وحدات القياس - Units
            // ============================================
            ['name' => 'MENU_WAREHOUSE_UNITS', 'display_name' => 'وحدات القياس', 'display_name_en' => 'Units of Measurement', 'group_name' => 'المستودع - وحدات القياس', 'group_name_en' => 'Warehouse - Units', 'description' => 'إظهار قائمة وحدات القياس', 'description_en' => 'Show Units Menu'],
            ['name' => 'WAREHOUSE_UNITS_READ', 'display_name' => 'عرض الوحدات', 'display_name_en' => 'View Units', 'group_name' => 'المستودع - وحدات القياس', 'group_name_en' => 'Warehouse - Units', 'description' => 'عرض وحدات القياس', 'description_en' => 'View Units'],
            ['name' => 'WAREHOUSE_UNITS_CREATE', 'display_name' => 'إضافة وحدة', 'display_name_en' => 'Add Unit', 'group_name' => 'المستودع - وحدات القياس', 'group_name_en' => 'Warehouse - Units', 'description' => 'إضافة وحدة قياس جديدة', 'description_en' => 'Add New Unit'],
            ['name' => 'WAREHOUSE_UNITS_UPDATE', 'display_name' => 'تعديل الوحدة', 'display_name_en' => 'Edit Unit', 'group_name' => 'المستودع - وحدات القياس', 'group_name_en' => 'Warehouse - Units', 'description' => 'تعديل وحدات القياس', 'description_en' => 'Edit Units'],
            ['name' => 'WAREHOUSE_UNITS_DELETE', 'display_name' => 'حذف الوحدة', 'display_name_en' => 'Delete Unit', 'group_name' => 'المستودع - وحدات القياس', 'group_name_en' => 'Warehouse - Units', 'description' => 'حذف وحدات القياس', 'description_en' => 'Delete Units'],

            // ============================================
            // تسجيل البضاعة - Warehouse Registration
            // ============================================
            ['name' => 'MENU_WAREHOUSE_REGISTRATION', 'display_name' => 'تسجيل البضاعة', 'display_name_en' => 'Goods Registration', 'group_name' => 'المستودع - تسجيل البضاعة', 'group_name_en' => 'Warehouse - Registration', 'description' => 'إظهار قائمة تسجيل البضاعة', 'description_en' => 'Show Registration Menu'],
            ['name' => 'WAREHOUSE_REGISTRATION_READ', 'display_name' => 'عرض تسجيلات البضاعة', 'display_name_en' => 'View Registrations', 'group_name' => 'المستودع - تسجيل البضاعة', 'group_name_en' => 'Warehouse - Registration', 'description' => 'عرض قائمة تسجيلات البضاعة', 'description_en' => 'View Registrations List'],
            ['name' => 'WAREHOUSE_REGISTRATION_CREATE', 'display_name' => 'إضافة تسجيل بضاعة', 'display_name_en' => 'Add Registration', 'group_name' => 'المستودع - تسجيل البضاعة', 'group_name_en' => 'Warehouse - Registration', 'description' => 'إنشاء تسجيل بضاعة جديد', 'description_en' => 'Create New Registration'],
            ['name' => 'WAREHOUSE_REGISTRATION_UPDATE', 'display_name' => 'تعديل تسجيل البضاعة', 'display_name_en' => 'Edit Registration', 'group_name' => 'المستودع - تسجيل البضاعة', 'group_name_en' => 'Warehouse - Registration', 'description' => 'تعديل تسجيلات البضاعة', 'description_en' => 'Edit Registrations'],
            ['name' => 'WAREHOUSE_REGISTRATION_LOCK', 'display_name' => 'قفل التسجيل', 'display_name_en' => 'Lock Registration', 'group_name' => 'المستودع - تسجيل البضاعة', 'group_name_en' => 'Warehouse - Registration', 'description' => 'قفل تسجيلات البضاعة', 'description_en' => 'Lock Registrations'],
            ['name' => 'WAREHOUSE_REGISTRATION_UNLOCK', 'display_name' => 'فتح القفل', 'display_name_en' => 'Unlock Registration', 'group_name' => 'المستودع - تسجيل البضاعة', 'group_name_en' => 'Warehouse - Registration', 'description' => 'فتح تسجيلات البضاعة المقفلة', 'description_en' => 'Unlock Locked Registrations'],
            ['name' => 'WAREHOUSE_REGISTRATION_TRANSFER', 'display_name' => 'نقل البضاعة للإنتاج', 'display_name_en' => 'Transfer to Production', 'group_name' => 'المستودع - تسجيل البضاعة', 'group_name_en' => 'Warehouse - Registration', 'description' => 'نقل البضاعة إلى المراحل الإنتاجية', 'description_en' => 'Transfer Goods to Production'],

            // ============================================
            // تسوية المستودع - Warehouse Reconciliation
            // ============================================
            ['name' => 'MENU_WAREHOUSE_RECONCILIATION', 'display_name' => 'تسوية المستودع', 'display_name_en' => 'Reconciliation', 'group_name' => 'المستودع - تسوية المستودع', 'group_name_en' => 'Warehouse - Reconciliation', 'description' => 'إظهار قائمة التسوية', 'description_en' => 'Show Reconciliation Menu'],
            ['name' => 'WAREHOUSE_RECONCILIATION_READ', 'display_name' => 'عرض التسويات', 'display_name_en' => 'View Reconciliation', 'group_name' => 'المستودع - تسوية المستودع', 'group_name_en' => 'Warehouse - Reconciliation', 'description' => 'عرض قائمة التسويات', 'description_en' => 'View Reconciliation Records'],
            ['name' => 'WAREHOUSE_RECONCILIATION_CREATE', 'display_name' => 'إنشاء تسوية', 'display_name_en' => 'Create Reconciliation', 'group_name' => 'المستودع - تسوية المستودع', 'group_name_en' => 'Warehouse - Reconciliation', 'description' => 'إنشاء تسوية جديدة', 'description_en' => 'Create New Reconciliation Record'],
            ['name' => 'WAREHOUSE_RECONCILIATION_UPDATE', 'display_name' => 'تعديل التسوية', 'display_name_en' => 'Edit Reconciliation', 'group_name' => 'المستودع - تسوية المستودع', 'group_name_en' => 'Warehouse - Reconciliation', 'description' => 'تعديل التسويات', 'description_en' => 'Edit Reconciliation Record'],
            ['name' => 'WAREHOUSE_RECONCILIATION_DELETE', 'display_name' => 'حذف التسوية', 'display_name_en' => 'Delete Reconciliation', 'group_name' => 'المستودع - تسوية المستودع', 'group_name_en' => 'Warehouse - Reconciliation', 'description' => 'حذف التسويات', 'description_en' => 'Delete Reconciliation Record'],
            ['name' => 'WAREHOUSE_RECONCILIATION_MANAGEMENT', 'display_name' => 'إدارة التسويات', 'display_name_en' => 'Reconciliation Management', 'group_name' => 'المستودع - تسوية المستودع', 'group_name_en' => 'Warehouse - Reconciliation', 'description' => 'لوحة التحكم الإدارية للتسويات', 'description_en' => 'Reconciliation Management Dashboard'],
            ['name' => 'WAREHOUSE_RECONCILIATION_LINK_INVOICE', 'display_name' => 'ربط الفواتير', 'display_name_en' => 'Link Invoices', 'group_name' => 'المستودع - تسوية المستودع', 'group_name_en' => 'Warehouse - Reconciliation', 'description' => 'ربط الفواتير بمذكرات التسليم', 'description_en' => 'Link Invoices with Delivery Notes'],

            // ============================================
            // حركات المواد - Material Movements
            // ============================================
            ['name' => 'MENU_WAREHOUSE_MOVEMENTS', 'display_name' => 'حركات المواد', 'display_name_en' => 'Material Movements', 'group_name' => 'المستودع - حركات المواد', 'group_name_en' => 'Warehouse - Movements', 'description' => 'إظهار قائمة حركات المواد', 'description_en' => 'Show Material Movements Menu'],
            ['name' => 'WAREHOUSE_MOVEMENTS_READ', 'display_name' => 'عرض حركات المواد', 'display_name_en' => 'View Movements', 'group_name' => 'المستودع - حركات المواد', 'group_name_en' => 'Warehouse - Movements', 'description' => 'عرض سجل حركات المواد', 'description_en' => 'View Material Movements'],
            ['name' => 'WAREHOUSE_MOVEMENTS_DETAILS', 'display_name' => 'عرض تفاصيل الحركة', 'display_name_en' => 'View Movement Details', 'group_name' => 'المستودع - حركات المواد', 'group_name_en' => 'Warehouse - Movements', 'description' => 'عرض تفاصيل حركة المادة', 'description_en' => 'View Material Movement Details'],

            // ============================================
            // إعدادات المستودع - Warehouse Settings
            // ============================================
            ['name' => 'MENU_WAREHOUSE_SETTINGS', 'display_name' => 'إعدادات المستودع', 'display_name_en' => 'Warehouse Settings', 'group_name' => 'المستودع - إعدادات', 'group_name_en' => 'Warehouse - Settings', 'description' => 'إظهار قائمة إعدادات المستودع', 'description_en' => 'Show Warehouse Settings Menu'],

            // ============================================
            // تقارير المستودع - Warehouse Reports
            // ============================================
            ['name' => 'MENU_WAREHOUSE_REPORTS', 'display_name' => 'تقارير المستودع', 'display_name_en' => 'Warehouse Reports', 'group_name' => 'المستودع - تقارير', 'group_name_en' => 'Warehouse - Reports', 'description' => 'إظهار قائمة تقارير المستودع', 'description_en' => 'Show Warehouse Reports Menu'],

            // ============================================
            // تأكيدات التسليم - Production Confirmations
            // ============================================
            ['name' => 'MENU_PRODUCTION_CONFIRMATIONS', 'display_name' => 'تأكيدات التسليم', 'display_name_en' => 'Production Confirmations', 'group_name' => 'تأكيدات التسليم', 'group_name_en' => 'Production - Confirmations', 'description' => 'إظهار قائمة تأكيدات التسليم', 'description_en' => 'Show Confirmations Menu'],
            ['name' => 'PRODUCTION_CONFIRMATIONS_READ', 'display_name' => 'عرض تأكيدات التسليم', 'display_name_en' => 'View Confirmations', 'group_name' => 'تأكيدات التسليم', 'group_name_en' => 'Production - Confirmations', 'description' => 'عرض جميع تأكيدات التسليم', 'description_en' => 'View All Production Confirmations'],
            ['name' => 'PRODUCTION_CONFIRMATIONS_CONFIRM', 'display_name' => 'تأكيد الاستلام', 'display_name_en' => 'Confirm Receipt', 'group_name' => 'تأكيدات التسليم', 'group_name_en' => 'Production - Confirmations', 'description' => 'تأكيد استلام الدفعة', 'description_en' => 'Confirm Batch Receipt'],
            ['name' => 'PRODUCTION_CONFIRMATIONS_REJECT', 'display_name' => 'رفض الاستلام', 'display_name_en' => 'Reject Receipt', 'group_name' => 'تأكيدات التسليم', 'group_name_en' => 'Production - Confirmations', 'description' => 'رفض استلام الدفعة', 'description_en' => 'Reject Batch Receipt'],
            ['name' => 'PRODUCTION_CONFIRMATIONS_VIEW_DETAILS', 'display_name' => 'عرض تفاصيل التأكيد', 'display_name_en' => 'View Confirmation Details', 'group_name' => 'تأكيدات التسليم', 'group_name_en' => 'Production - Confirmations', 'description' => 'عرض تفاصيل تأكيد التسليم', 'description_en' => 'View Confirmation Details'],

            // ============================================
            // المرحلة الأولى - الاستاندات - Stage 1: Stands
            // ============================================
            ['name' => 'MENU_STAGE1_STANDS', 'display_name' => 'المرحلة الأولى - الاستاندات', 'display_name_en' => 'Stage 1: Stands', 'group_name' => 'المرحلة الأولى - الاستاندات', 'group_name_en' => 'Production - Stage 1', 'description' => 'إظهار قائمة المرحلة الأولى', 'description_en' => 'Show Stage 1 Menu'],
            ['name' => 'STAGE1_STANDS_READ', 'display_name' => 'عرض الاستاندات', 'display_name_en' => 'View Stands', 'group_name' => 'المرحلة الأولى - الاستاندات', 'group_name_en' => 'Production - Stage 1', 'description' => 'عرض الاستاندات', 'description_en' => 'View Stands'],
            ['name' => 'STAGE1_STANDS_CREATE', 'display_name' => 'إضافة استاند', 'display_name_en' => 'Create Stand', 'group_name' => 'المرحلة الأولى - الاستاندات', 'group_name_en' => 'Production - Stage 1', 'description' => 'إنشاء استاند جديد', 'description_en' => 'Create New Stand'],
            ['name' => 'STAGE1_STANDS_UPDATE', 'display_name' => 'تعديل الاستاند', 'display_name_en' => 'Edit Stand', 'group_name' => 'المرحلة الأولى - الاستاندات', 'group_name_en' => 'Production - Stage 1', 'description' => 'تعديل الاستاندات', 'description_en' => 'Edit Stand Data'],
            ['name' => 'STAGE1_STANDS_DELETE', 'display_name' => 'حذف الاستاند', 'display_name_en' => 'Delete Stand', 'group_name' => 'المرحلة الأولى - الاستاندات', 'group_name_en' => 'Production - Stage 1', 'description' => 'حذف الاستاندات', 'description_en' => 'Delete Stand'],
            ['name' => 'STAGE1_BARCODE_SCAN', 'display_name' => 'مسح الباركود - المرحلة الأولى', 'display_name_en' => 'Barcode Scan - Stage 1', 'group_name' => 'المرحلة الأولى - الاستاندات', 'group_name_en' => 'Production - Stage 1', 'description' => 'الوصول لصفحة مسح الباركود', 'description_en' => 'Access Barcode Scanning Page'],
            ['name' => 'STAGE1_WASTE_TRACKING', 'display_name' => 'تتبع الهدر - المرحلة الأولى', 'display_name_en' => 'Waste Tracking - Stage 1', 'group_name' => 'المرحلة الأولى - الاستاندات', 'group_name_en' => 'Production - Stage 1', 'description' => 'تتبع الهدر في المرحلة الأولى', 'description_en' => 'Track Waste in Stage 1'],
            ['name' => 'VIEW_ALL_STAGE1_OPERATIONS', 'display_name' => 'عرض جميع عمليات المرحلة الأولى', 'display_name_en' => 'View All Stage 1 Operations', 'group_name' => 'المرحلة الأولى - الاستاندات', 'group_name_en' => 'Production - Stage 1', 'description' => 'السماح بعرض جميع عمليات المرحلة الأولى لجميع العمال', 'description_en' => 'Allow Viewing All Stage 1 Operations'],

            // ============================================
            // المرحلة الثانية - المعالجة - Stage 2: Processing
            // ============================================
            ['name' => 'MENU_STAGE2_PROCESSING', 'display_name' => 'المرحلة الثانية - المعالجة', 'display_name_en' => 'Stage 2: Processing', 'group_name' => 'المرحلة الثانية - المعالجة', 'group_name_en' => 'Production - Stage 2', 'description' => 'إظهار قائمة المرحلة الثانية', 'description_en' => 'Show Stage 2 Menu'],
            ['name' => 'STAGE2_PROCESSING_READ', 'display_name' => 'عرض المعالجة', 'display_name_en' => 'View Processing', 'group_name' => 'المرحلة الثانية - المعالجة', 'group_name_en' => 'Production - Stage 2', 'description' => 'عرض عمليات المعالجة', 'description_en' => 'View Processing Records'],
            ['name' => 'STAGE2_PROCESSING_CREATE', 'display_name' => 'إضافة معالجة', 'display_name_en' => 'Create Processing', 'group_name' => 'المرحلة الثانية - المعالجة', 'group_name_en' => 'Production - Stage 2', 'description' => 'إنشاء عملية معالجة جديدة', 'description_en' => 'Create New Processing Record'],
            ['name' => 'STAGE2_PROCESSING_UPDATE', 'display_name' => 'تعديل المعالجة', 'display_name_en' => 'Edit Processing', 'group_name' => 'المرحلة الثانية - المعالجة', 'group_name_en' => 'Production - Stage 2', 'description' => 'تعديل عمليات المعالجة', 'description_en' => 'Edit Processing Record'],
            ['name' => 'STAGE2_PROCESSING_DELETE', 'display_name' => 'حذف المعالجة', 'display_name_en' => 'Delete Processing', 'group_name' => 'المرحلة الثانية - المعالجة', 'group_name_en' => 'Production - Stage 2', 'description' => 'حذف عمليات المعالجة', 'description_en' => 'Delete Processing Record'],
            ['name' => 'STAGE2_COMPLETE_PROCESSING', 'display_name' => 'إتمام المعالجة', 'display_name_en' => 'Complete Processing', 'group_name' => 'المرحلة الثانية - المعالجة', 'group_name_en' => 'Production - Stage 2', 'description' => 'إتمام عمليات المعالجة', 'description_en' => 'Complete Processing Operations'],
            ['name' => 'STAGE2_WASTE_STATISTICS', 'display_name' => 'إحصائيات الهدر - المرحلة الثانية', 'display_name_en' => 'Waste Statistics - Stage 2', 'group_name' => 'المرحلة الثانية - المعالجة', 'group_name_en' => 'Production - Stage 2', 'description' => 'عرض إحصائيات الهدر', 'description_en' => 'View Waste Statistics'],
            ['name' => 'VIEW_ALL_STAGE2_OPERATIONS', 'display_name' => 'عرض جميع عمليات المرحلة الثانية', 'display_name_en' => 'View All Stage 2 Operations', 'group_name' => 'المرحلة الثانية - المعالجة', 'group_name_en' => 'Production - Stage 2', 'description' => 'السماح بعرض جميع عمليات المرحلة الثانية لجميع العمال', 'description_en' => 'Allow Viewing All Stage 2 Operations'],

            // ============================================
            // المرحلة الثالثة - اللفائف - Stage 3: Coils
            // ============================================
            ['name' => 'MENU_STAGE3_COILS', 'display_name' => 'المرحلة الثالثة - اللفائف', 'display_name_en' => 'Stage 3: Coils', 'group_name' => 'المرحلة الثالثة - اللفائف', 'group_name_en' => 'Production - Stage 3', 'description' => 'إظهار قائمة المرحلة الثالثة', 'description_en' => 'Show Stage 3 Menu'],
            ['name' => 'STAGE3_COILS_READ', 'display_name' => 'عرض اللفائف', 'display_name_en' => 'View Coils', 'group_name' => 'المرحلة الثالثة - اللفائف', 'group_name_en' => 'Production - Stage 3', 'description' => 'عرض اللفائف', 'description_en' => 'View Coil Records'],
            ['name' => 'STAGE3_COILS_CREATE', 'display_name' => 'إضافة لفافة', 'display_name_en' => 'Create Coil', 'group_name' => 'المرحلة الثالثة - اللفائف', 'group_name_en' => 'Production - Stage 3', 'description' => 'إنشاء لفافة جديدة', 'description_en' => 'Create New Coil Record'],
            ['name' => 'STAGE3_COILS_UPDATE', 'display_name' => 'تعديل اللفافة', 'display_name_en' => 'Edit Coil', 'group_name' => 'المرحلة الثالثة - اللفائف', 'group_name_en' => 'Production - Stage 3', 'description' => 'تعديل اللفائف', 'description_en' => 'Edit Coil Data'],
            ['name' => 'STAGE3_COILS_DELETE', 'display_name' => 'حذف اللفافة', 'display_name_en' => 'Delete Coil', 'group_name' => 'المرحلة الثالثة - اللفائف', 'group_name_en' => 'Production - Stage 3', 'description' => 'حذف اللفائف', 'description_en' => 'Delete Coil Record'],
            ['name' => 'VIEW_ALL_STAGE3_OPERATIONS', 'display_name' => 'عرض جميع عمليات المرحلة الثالثة', 'display_name_en' => 'View All Stage 3 Operations', 'group_name' => 'المرحلة الثالثة - اللفائف', 'group_name_en' => 'Production - Stage 3', 'description' => 'السماح بعرض جميع عمليات المرحلة الثالثة لجميع العمال', 'description_en' => 'Allow Viewing All Stage 3 Operations'],

            // ============================================
            // المرحلة الرابعة - التعبئة - Stage 4: Packaging
            // ============================================
            ['name' => 'MENU_STAGE4_PACKAGING', 'display_name' => 'المرحلة الرابعة - التعبئة', 'display_name_en' => 'Stage 4: Packaging', 'group_name' => 'المرحلة الرابعة - التعبئة', 'group_name_en' => 'Production - Stage 4', 'description' => 'إظهار قائمة المرحلة الرابعة', 'description_en' => 'Show Stage 4 Menu'],
            ['name' => 'STAGE4_PACKAGING_READ', 'display_name' => 'عرض التعبئة', 'display_name_en' => 'View Packaging', 'group_name' => 'المرحلة الرابعة - التعبئة', 'group_name_en' => 'Production - Stage 4', 'description' => 'عرض عمليات التعبئة', 'description_en' => 'View Packaging Records'],
            ['name' => 'STAGE4_PACKAGING_CREATE', 'display_name' => 'إضافة تعبئة', 'display_name_en' => 'Create Packaging', 'group_name' => 'المرحلة الرابعة - التعبئة', 'group_name_en' => 'Production - Stage 4', 'description' => 'إنشاء عملية تعبئة جديدة', 'description_en' => 'Create New Packaging Record'],
            ['name' => 'STAGE4_PACKAGING_UPDATE', 'display_name' => 'تعديل التعبئة', 'display_name_en' => 'Edit Packaging', 'group_name' => 'المرحلة الرابعة - التعبئة', 'group_name_en' => 'Production - Stage 4', 'description' => 'تعديل عمليات التعبئة', 'description_en' => 'Edit Packaging Data'],
            ['name' => 'STAGE4_PACKAGING_DELETE', 'display_name' => 'حذف التعبئة', 'display_name_en' => 'Delete Packaging', 'group_name' => 'المرحلة الرابعة - التعبئة', 'group_name_en' => 'Production - Stage 4', 'description' => 'حذف عمليات التعبئة', 'description_en' => 'Delete Packaging Record'],
            ['name' => 'VIEW_ALL_STAGE4_OPERATIONS', 'display_name' => 'عرض جميع عمليات المرحلة الرابعة', 'display_name_en' => 'View All Stage 4 Operations', 'group_name' => 'المرحلة الرابعة - التعبئة', 'group_name_en' => 'Production - Stage 4', 'description' => 'السماح بعرض جميع عمليات المرحلة الرابعة لجميع العمال', 'description_en' => 'Allow Viewing All Stage 4 Operations'],

            // ============================================
            // تتبع الإنتاج - Production Tracking
            // ============================================
            ['name' => 'MENU_PRODUCTION_TRACKING', 'display_name' => 'تتبع الإنتاج', 'display_name_en' => 'Production Tracking', 'group_name' => 'تتبع الإنتاج', 'group_name_en' => 'Production - Tracking', 'description' => 'إظهار قائمة تتبع الإنتاج', 'description_en' => 'Show Production Tracking Menu'],
            ['name' => 'PRODUCTION_TRACKING_SCAN', 'display_name' => 'مسح الباركود - تتبع الإنتاج', 'display_name_en' => 'Barcode Scan - Tracking', 'group_name' => 'تتبع الإنتاج', 'group_name_en' => 'Production - Tracking', 'description' => 'الوصول لصفحة مسح الباركود في تتبع الإنتاج', 'description_en' => 'Access Barcode Scanning in Production Tracking'],
            ['name' => 'PRODUCTION_IRON_JOURNEY', 'display_name' => 'رحلة الحديد', 'display_name_en' => 'Iron Journey', 'group_name' => 'تتبع الإنتاج', 'group_name_en' => 'Production - Tracking', 'description' => 'الوصول لصفحة رحلة الحديد', 'description_en' => 'Access Iron Journey Page'],

            // ============================================
            // الورديات والعمال - Shifts & Workers
            // ============================================
            ['name' => 'MENU_SHIFTS_WORKERS', 'display_name' => 'الورديات والعمال', 'display_name_en' => 'Shifts & Workers', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'إظهار قائمة الورديات والعمال', 'description_en' => 'Show Shifts & Workers Menu'],
            ['name' => 'SHIFTS_READ', 'display_name' => 'عرض الورديات', 'display_name_en' => 'View Shifts', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'عرض قائمة الورديات', 'description_en' => 'View Shifts List'],
            ['name' => 'SHIFTS_CREATE', 'display_name' => 'إضافة وردية', 'display_name_en' => 'Add Shift', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'إضافة وردية جديدة', 'description_en' => 'Add New Shift'],
            ['name' => 'SHIFTS_UPDATE', 'display_name' => 'تعديل الوردية', 'display_name_en' => 'Edit Shift', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'تعديل بيانات الوردية', 'description_en' => 'Edit Shift Data'],
            ['name' => 'SHIFTS_DELETE', 'display_name' => 'حذف الوردية', 'display_name_en' => 'Delete Shift', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'حذف الوردية', 'description_en' => 'Delete Shift'],
            ['name' => 'SHIFTS_ACTIVATE', 'display_name' => 'تفعيل الوردية', 'display_name_en' => 'Activate Shift', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'تفعيل وردية مجدولة', 'description_en' => 'Activate Scheduled Shift'],
            ['name' => 'SHIFTS_COMPLETE', 'display_name' => 'إكمال الوردية', 'display_name_en' => 'Complete Shift', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'إكمال وردية نشطة', 'description_en' => 'Complete Active Shift'],
            ['name' => 'SHIFTS_SUSPEND', 'display_name' => 'تعليق الوردية', 'display_name_en' => 'Suspend Shift', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'تعليق وردية مؤقتاً', 'description_en' => 'Suspend Shift Temporarily'],
            ['name' => 'SHIFTS_RESUME', 'display_name' => 'استئناف الوردية', 'display_name_en' => 'Resume Shift', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'استئناف وردية معلقة', 'description_en' => 'Resume Suspended Shift'],
            ['name' => 'SHIFTS_CURRENT', 'display_name' => 'الورديات الحالية', 'display_name_en' => 'Current Shifts', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'عرض الورديات الحالية', 'description_en' => 'View Current Shifts'],
            ['name' => 'SHIFTS_ATTENDANCE', 'display_name' => 'الحضور والغياب', 'display_name_en' => 'Attendance', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'إدارة حضور وغياب العمال', 'description_en' => 'Manage Worker Attendance'],
            ['name' => 'WORKERS_READ', 'display_name' => 'عرض العمال', 'display_name_en' => 'View Workers', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'عرض قائمة العمال', 'description_en' => 'View Workers List'],
              ['name' => 'WORKERS_DELETE', 'display_name' => 'حذف العمال', 'display_name_en' => 'Delete Worker', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'حذف عامل', 'description_en' => 'Delete Worker'],
            ['name' => 'WORKERS_CREATE', 'display_name' => 'إضافة عامل', 'display_name_en' => 'Add Worker', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'إضافة عامل جديد', 'description_en' => 'Add New Worker'],
            ['name' => 'WORKERS_UPDATE', 'display_name' => 'تعديل بيانات العامل', 'display_name_en' => 'Edit Worker Data', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'تعديل بيانات العامل', 'description_en' => 'Edit Worker Data'],

            // ============================================
            // نقل الورديات - Shift Handovers
            // ============================================
            ['name' => 'MENU_SHIFT_HANDOVERS', 'display_name' => 'نقل الورديات', 'display_name_en' => 'Shift Handovers', 'group_name' => 'نقل الورديات', 'group_name_en' => 'Shift Handovers', 'description' => 'إظهار قائمة نقل الورديات', 'description_en' => 'Show Shift Handovers Menu'],
            ['name' => 'SHIFT_HANDOVERS_READ', 'display_name' => 'عرض نقل الورديات', 'display_name_en' => 'View Handovers', 'group_name' => 'نقل الورديات', 'group_name_en' => 'Shift Handovers', 'description' => 'عرض قائمة نقل الورديات', 'description_en' => 'View Shift Handovers List'],
            ['name' => 'SHIFT_HANDOVERS_CREATE', 'display_name' => 'إنشاء نقل وردية', 'display_name_en' => 'Create Handover', 'group_name' => 'نقل الورديات', 'group_name_en' => 'Shift Handovers', 'description' => 'إنشاء نقل وردية جديد', 'description_en' => 'Create New Shift Handover'],
            ['name' => 'SHIFT_HANDOVERS_VIEW', 'display_name' => 'عرض تفاصيل النقل', 'display_name_en' => 'View Handover Details', 'group_name' => 'نقل الورديات', 'group_name_en' => 'Shift Handovers', 'description' => 'عرض تفاصيل نقل الوردية', 'description_en' => 'View Shift Handover Details'],
            ['name' => 'SHIFT_HANDOVERS_APPROVE', 'display_name' => 'الموافقة على النقل', 'display_name_en' => 'Approve Handover', 'group_name' => 'نقل الورديات', 'group_name_en' => 'Shift Handovers', 'description' => 'الموافقة على نقل الوردية', 'description_en' => 'Approve Shift Handover'],
            ['name' => 'SHIFT_HANDOVERS_REJECT', 'display_name' => 'رفض النقل', 'display_name_en' => 'Reject Handover', 'group_name' => 'نقل الورديات', 'group_name_en' => 'Shift Handovers', 'description' => 'رفض نقل الوردية', 'description_en' => 'Reject Shift Handover'],
            ['name' => 'SHIFT_HANDOVERS_DELETE', 'display_name' => 'حذف النقل', 'display_name_en' => 'Delete Handover', 'group_name' => 'نقل الورديات', 'group_name_en' => 'Shift Handovers', 'description' => 'حذف نقل الوردية', 'description_en' => 'Delete Shift Handover'],
            ['name' => 'SHIFT_HANDOVERS_FROM_INDEX', 'display_name' => 'نقل من الانديكس', 'display_name_en' => 'Handover from Index', 'group_name' => 'نقل الورديات', 'group_name_en' => 'Shift Handovers', 'description' => 'السماح بنقل الوردية من صفحة الانديكس مباشرة', 'description_en' => 'Allow Shift Handover from Index Page'],
            ['name' => 'WORKER_TEAMS_READ', 'display_name' => 'عرض مجموعات العمال', 'display_name_en' => 'View Worker Teams', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'عرض مجموعات العمال', 'description_en' => 'View Worker Teams'],
            ['name' => 'WORKER_TEAMS_CREATE', 'display_name' => 'إضافة مجموعة عمال', 'display_name_en' => 'Create Worker Team', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'إضافة مجموعة عمال جديدة', 'description_en' => 'Create New Worker Team'],
            ['name' => 'WORKER_TEAMS_UPDATE', 'display_name' => 'تعديل مجموعة العمال', 'display_name_en' => 'Edit Worker Team', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'تعديل مجموعة العمال', 'description_en' => 'Edit Worker Team'],
            ['name' => 'WORKER_TEAMS_DELETE', 'display_name' => 'حذف مجموعة العمال', 'display_name_en' => 'Delete Worker Team', 'group_name' => 'الورديات والعمال', 'group_name_en' => 'Shifts & Workers', 'description' => 'حذف مجموعة العمال', 'description_en' => 'Delete Worker Team'],

            // ============================================
            // الجودة والهدر - Quality & Waste
            // ============================================
            ['name' => 'MENU_QUALITY_WASTE', 'display_name' => 'الجودة والهدر', 'display_name_en' => 'Quality & Waste', 'group_name' => 'الجودة والهدر', 'group_name_en' => 'Quality & Waste', 'description' => 'إظهار قائمة الجودة والهدر', 'description_en' => 'Show Quality & Waste Menu'],
            ['name' => 'QUALITY_WASTE_REPORT', 'display_name' => 'تقرير الهدر', 'display_name_en' => 'Waste Report', 'group_name' => 'الجودة والهدر', 'group_name_en' => 'Quality & Waste', 'description' => 'عرض تقرير الهدر', 'description_en' => 'View Waste Report'],
            ['name' => 'QUALITY_MONITORING', 'display_name' => 'مراقبة الجودة', 'display_name_en' => 'Quality Monitoring', 'group_name' => 'الجودة والهدر', 'group_name_en' => 'Quality & Waste', 'description' => 'الوصول لصفحة مراقبة الجودة', 'description_en' => 'Access Quality Monitoring Page'],
            ['name' => 'QUALITY_DOWNTIME_TRACKING', 'display_name' => 'تتبع التوقفات', 'display_name_en' => 'Downtime Tracking', 'group_name' => 'الجودة والهدر', 'group_name_en' => 'Quality & Waste', 'description' => 'تتبع توقفات الإنتاج', 'description_en' => 'Track Production Downtime'],
            ['name' => 'QUALITY_WASTE_LIMITS', 'display_name' => 'حدود الهدر', 'display_name_en' => 'Waste Limits', 'group_name' => 'الجودة والهدر', 'group_name_en' => 'Quality & Waste', 'description' => 'إدارة حدود الهدر المسموح', 'description_en' => 'Manage Acceptable Waste Limits'],

            // ============================================
            // التقارير الإنتاجية - Production Reports
            // ============================================
            ['name' => 'MENU_PRODUCTION_REPORTS', 'display_name' => 'التقارير الإنتاجية', 'display_name_en' => 'Production Reports', 'group_name' => 'التقارير الإنتاجية', 'group_name_en' => 'Production Reports', 'description' => 'إظهار قائمة التقارير الإنتاجية', 'description_en' => 'Show Production Reports Menu'],
            ['name' => 'REPORTS_WIP', 'display_name' => 'تقرير الأعمال غير المنتهية', 'display_name_en' => 'WIP Report', 'group_name' => 'التقارير الإنتاجية', 'group_name_en' => 'Production Reports', 'description' => 'عرض تقرير الأعمال غير المنتهية', 'description_en' => 'View Work In Progress Report'],
            ['name' => 'REPORTS_SHIFT_DASHBOARD', 'display_name' => 'ملخص الوردية', 'display_name_en' => 'Shift Summary', 'group_name' => 'التقارير الإنتاجية', 'group_name_en' => 'Production Reports', 'description' => 'عرض ملخص الوردية', 'description_en' => 'View Shift Summary'],
            ['name' => 'REPORTS_STANDS_USAGE', 'display_name' => 'تاريخ استخدام الستاندات', 'display_name_en' => 'Stands Usage History', 'group_name' => 'التقارير الإنتاجية', 'group_name_en' => 'Production Reports', 'description' => 'عرض تاريخ استخدام الستاندات', 'description_en' => 'View Stands Usage History'],
            ['name' => 'REPORTS_WORKER_PERFORMANCE', 'display_name' => 'تقرير أداء العمال', 'display_name_en' => 'Worker Performance Report', 'group_name' => 'التقارير الإنتاجية', 'group_name_en' => 'Production Reports', 'description' => 'عرض تقرير أداء العمال', 'description_en' => 'View Worker Performance Report'],

            // ============================================
            // التقارير العامة - General Reports
            // ============================================
            ['name' => 'VIEW_REPORTS', 'display_name' => 'التقارير والإحصائيات', 'display_name_en' => 'Reports & Statistics', 'group_name' => 'التقارير العامة', 'group_name_en' => 'General Reports', 'description' => 'عرض التقارير والإحصائيات', 'description_en' => 'View Reports & Statistics'],
            ['name' => 'PRODUCTION_REPORTS', 'display_name' => 'تقارير الإنتاج', 'display_name_en' => 'Production Reports', 'group_name' => 'التقارير العامة', 'group_name_en' => 'General Reports', 'description' => 'تقارير الإنتاج التفصيلية', 'description_en' => 'Detailed Production Reports'],
            ['name' => 'INVENTORY_REPORTS', 'display_name' => 'تقارير المخزون', 'display_name_en' => 'Inventory Reports', 'group_name' => 'التقارير العامة', 'group_name_en' => 'General Reports', 'description' => 'تقارير المخزون والحركات', 'description_en' => 'Inventory & Movements Reports'],

            // ============================================
            // لوحة التحكم - Dashboard (Additional)
            // ============================================
            ['name' => 'VIEW_DASHBOARD', 'display_name' => 'لوحة التحكم', 'display_name_en' => 'Dashboard', 'group_name' => 'لوحة التحكم', 'group_name_en' => 'Dashboard', 'description' => 'عرض لوحة التحكم الرئيسية', 'description_en' => 'View Main Dashboard'],
            ['name' => 'STAGE_WORKER_DASHBOARD', 'display_name' => 'لوحة تحكم عمال المراحل', 'display_name_en' => 'Stage Worker Dashboard', 'group_name' => 'لوحة التحكم', 'group_name_en' => 'Dashboard', 'description' => 'الوصول للوحة التحكم الخاصة بعمال المراحل', 'description_en' => 'Access Stage Workers Dashboard'],

            // ============================================
            // الإدارة - Management
            // ============================================
            ['name' => 'MENU_MANAGEMENT', 'display_name' => 'الإدارة', 'display_name_en' => 'Management', 'group_name' => 'الإدارة', 'group_name_en' => 'Management', 'description' => 'إظهار قائمة الإدارة', 'description_en' => 'Show Management Menu'],

            // ============================================
            // إدارة المستخدمين - Manage Users
            // ============================================
            ['name' => 'MENU_MANAGE_USERS', 'display_name' => 'إدارة المستخدمين', 'display_name_en' => 'Manage Users', 'group_name' => 'إدارة المستخدمين', 'group_name_en' => 'User Management', 'description' => 'إظهار قائمة إدارة المستخدمين', 'description_en' => 'Show User Management Menu'],
            ['name' => 'MANAGE_USERS_READ', 'display_name' => 'عرض المستخدمين', 'display_name_en' => 'View Users', 'group_name' => 'إدارة المستخدمين', 'group_name_en' => 'User Management', 'description' => 'عرض المستخدمين', 'description_en' => 'View Users'],
            ['name' => 'MANAGE_USERS_CREATE', 'display_name' => 'إضافة مستخدم', 'display_name_en' => 'Add User', 'group_name' => 'إدارة المستخدمين', 'group_name_en' => 'User Management', 'description' => 'إضافة مستخدم جديد', 'description_en' => 'Add New User'],
            ['name' => 'MANAGE_USERS_UPDATE', 'display_name' => 'تعديل المستخدم', 'display_name_en' => 'Edit User', 'group_name' => 'إدارة المستخدمين', 'group_name_en' => 'User Management', 'description' => 'تعديل بيانات المستخدم', 'description_en' => 'Edit User Data'],
            ['name' => 'MANAGE_USERS_DELETE', 'display_name' => 'حذف المستخدم', 'display_name_en' => 'Delete User', 'group_name' => 'إدارة المستخدمين', 'group_name_en' => 'User Management', 'description' => 'حذف المستخدم', 'description_en' => 'Delete User'],

            // ============================================
            // إدارة الأدوار - Manage Roles
            // ============================================
            ['name' => 'MENU_MANAGE_ROLES', 'display_name' => 'إدارة الأدوار', 'display_name_en' => 'Manage Roles', 'group_name' => 'إدارة الأدوار', 'group_name_en' => 'Role Management', 'description' => 'إظهار قائمة إدارة الأدوار', 'description_en' => 'Show Role Management Menu'],
            ['name' => 'MANAGE_ROLES_READ', 'display_name' => 'عرض الأدوار', 'display_name_en' => 'View Roles', 'group_name' => 'إدارة الأدوار', 'group_name_en' => 'Role Management', 'description' => 'عرض الأدوار', 'description_en' => 'View Roles'],
            ['name' => 'MANAGE_ROLES_CREATE', 'display_name' => 'إضافة دور', 'display_name_en' => 'Add Role', 'group_name' => 'إدارة الأدوار', 'group_name_en' => 'Role Management', 'description' => 'إضافة دور جديد', 'description_en' => 'Add New Role'],
            ['name' => 'MANAGE_ROLES_UPDATE', 'display_name' => 'تعديل الدور', 'display_name_en' => 'Edit Role', 'group_name' => 'إدارة الأدوار', 'group_name_en' => 'Role Management', 'description' => 'تعديل الدور', 'description_en' => 'Edit Role'],
            ['name' => 'MANAGE_ROLES_DELETE', 'display_name' => 'حذف الدور', 'display_name_en' => 'Delete Role', 'group_name' => 'إدارة الأدوار', 'group_name_en' => 'Role Management', 'description' => 'حذف الدور', 'description_en' => 'Delete Role'],

            // ============================================
            // إدارة الصلاحيات - Manage Permissions
            // ============================================
            ['name' => 'MENU_MANAGE_PERMISSIONS', 'display_name' => 'إدارة الصلاحيات', 'display_name_en' => 'Manage Permissions', 'group_name' => 'إدارة الصلاحيات', 'group_name_en' => 'Permission Management', 'description' => 'إظهار قائمة إدارة الصلاحيات', 'description_en' => 'Show Permission Management Menu'],
            ['name' => 'MANAGE_PERMISSIONS_READ', 'display_name' => 'عرض الصلاحيات', 'display_name_en' => 'View Permissions', 'group_name' => 'إدارة الصلاحيات', 'group_name_en' => 'Permission Management', 'description' => 'عرض الصلاحيات', 'description_en' => 'View Permissions'],
            ['name' => 'MANAGE_PERMISSIONS_CREATE', 'display_name' => 'إضافة صلاحية', 'display_name_en' => 'Add Permission', 'group_name' => 'إدارة الصلاحيات', 'group_name_en' => 'Permission Management', 'description' => 'إضافة صلاحية جديدة', 'description_en' => 'Add New Permission'],
            ['name' => 'MANAGE_PERMISSIONS_UPDATE', 'display_name' => 'تعديل الصلاحية', 'display_name_en' => 'Edit Permission', 'group_name' => 'إدارة الصلاحيات', 'group_name_en' => 'Permission Management', 'description' => 'تعديل الصلاحية', 'description_en' => 'Edit Permission'],
            ['name' => 'MANAGE_PERMISSIONS_DELETE', 'display_name' => 'حذف الصلاحية', 'display_name_en' => 'Delete Permission', 'group_name' => 'إدارة الصلاحيات', 'group_name_en' => 'Permission Management', 'description' => 'حذف الصلاحية', 'description_en' => 'Delete Permission'],

            // ============================================
            // الإعدادات - Settings
            // ============================================
            ['name' => 'MENU_SETTINGS', 'display_name' => 'الإعدادات', 'display_name_en' => 'Settings', 'group_name' => 'الإعدادات', 'group_name_en' => 'Settings', 'description' => 'إظهار قائمة الإعدادات', 'description_en' => 'Show Settings Menu'],
            ['name' => 'SETTINGS_GENERAL', 'display_name' => 'الإعدادات العامة', 'display_name_en' => 'General Settings', 'group_name' => 'الإعدادات', 'group_name_en' => 'Settings', 'description' => 'الوصول للإعدادات العامة', 'description_en' => 'Access General Settings'],
            ['name' => 'SETTINGS_CALCULATIONS', 'display_name' => 'إعدادات الحسابات', 'display_name_en' => 'Calculation Settings', 'group_name' => 'الإعدادات', 'group_name_en' => 'Settings', 'description' => 'إعدادات الحسابات والمعادلات', 'description_en' => 'Calculation Formulas Settings'],
            ['name' => 'SETTINGS_BARCODE', 'display_name' => 'إعدادات الباركود', 'display_name_en' => 'Barcode Settings', 'group_name' => 'الإعدادات', 'group_name_en' => 'Settings', 'description' => 'إعدادات الباركود', 'description_en' => 'Barcode Configuration'],
            ['name' => 'SETTINGS_NOTIFICATIONS', 'display_name' => 'إعدادات الإشعارات', 'display_name_en' => 'Notification Settings', 'group_name' => 'الإعدادات', 'group_name_en' => 'Settings', 'description' => 'إعدادات الإشعارات', 'description_en' => 'Notification Configuration'],

            // ============================================
            // العملاء - Customers
            // ============================================
            ['name' => 'MENU_CUSTOMERS', 'display_name' => 'العملاء', 'group_name' => 'العملاء', 'description' => 'إظهار قائمة العملاء'],
            ['name' => 'CUSTOMERS_READ', 'display_name' => 'عرض العملاء', 'group_name' => 'العملاء', 'description' => 'عرض قائمة العملاء'],
            ['name' => 'CUSTOMERS_CREATE', 'display_name' => 'إضافة عميل', 'group_name' => 'العملاء', 'description' => 'إضافة عميل جديد'],
            ['name' => 'CUSTOMERS_UPDATE', 'display_name' => 'تعديل بيانات العميل', 'group_name' => 'العملاء', 'description' => 'تعديل بيانات العميل'],
            ['name' => 'CUSTOMERS_DELETE', 'display_name' => 'حذف العميل', 'group_name' => 'العملاء', 'description' => 'حذف العميل (soft delete)'],
            ['name' => 'CUSTOMERS_ACTIVATE', 'display_name' => 'تفعيل/تعطيل العميل', 'group_name' => 'العملاء', 'description' => 'تفعيل أو تعطيل العميل'],

            // ============================================
            // طلبات إدخال المستودع - Warehouse Intake Requests
            // ============================================
            ['name' => 'WAREHOUSE_INTAKE_READ', 'display_name' => 'عرض طلبات الإدخال', 'group_name' => 'إدخال المستودع', 'description' => 'عرض طلبات إدخال المستودع'],
            ['name' => 'WAREHOUSE_INTAKE_CREATE', 'display_name' => 'إنشاء طلب إدخال', 'group_name' => 'إدخال المستودع', 'description' => 'إنشاء طلب إدخال مستودع جديد'],
            ['name' => 'WAREHOUSE_INTAKE_APPROVE', 'display_name' => 'اعتماد طلب الإدخال', 'group_name' => 'إدخال المستودع', 'description' => 'اعتماد طلبات إدخال المستودع'],
            ['name' => 'WAREHOUSE_INTAKE_REJECT', 'display_name' => 'رفض طلب الإدخال', 'group_name' => 'إدخال المستودع', 'description' => 'رفض طلبات إدخال المستودع'],
            ['name' => 'WAREHOUSE_INTAKE_PRINT', 'display_name' => 'طباعة إذن الإدخال', 'group_name' => 'إدخال المستودع', 'description' => 'طباعة إذن إدخال المستودع'],

            // ============================================
            // المراحل الموقوفة - تجاوز نسبة الهدر
            // ============================================
            ['name' => 'STAGE_SUSPENSION_VIEW', 'display_name' => 'عرض المراحل الموقوفة', 'group_name' => 'مراقبة الهدر', 'description' => 'عرض قائمة المراحل التي تم إيقافها بسبب تجاوز نسبة الهدر'],
            ['name' => 'STAGE_SUSPENSION_APPROVE', 'display_name' => 'الموافقة على استئناف المرحلة', 'group_name' => 'مراقبة الهدر', 'description' => 'الموافقة على استئناف المرحلة الموقوفة'],
            ['name' => 'STAGE_SUSPENSION_REJECT', 'display_name' => 'رفض استئناف المرحلة', 'group_name' => 'مراقبة الهدر', 'description' => 'رفض طلب استئناف المرحلة الموقوفة'],

            // ============================================
            // الإذونات الصادرة - منتجات نهائية
            // ============================================
            ['name' => 'MENU_FINISHED_PRODUCT_DELIVERIES', 'display_name' => 'الإذونات الصادرة', 'group_name' => 'المنتجات النهائية', 'description' => 'إظهار قائمة الإذونات الصادرة'],
            ['name' => 'FINISHED_PRODUCT_DELIVERIES_READ', 'display_name' => 'عرض الإذونات الصادرة', 'group_name' => 'المنتجات النهائية', 'description' => 'عرض قائمة الإذونات الصادرة للمنتجات النهائية'],
            ['name' => 'FINISHED_PRODUCT_DELIVERIES_CREATE', 'display_name' => 'إنشاء إذن صادر', 'group_name' => 'المنتجات النهائية', 'description' => 'إنشاء إذن صادر جديد للمنتجات النهائية'],
            ['name' => 'FINISHED_PRODUCT_DELIVERIES_UPDATE', 'display_name' => 'تعديل الإذن الصادر', 'group_name' => 'المنتجات النهائية', 'description' => 'تعديل الإذن الصادر (قبل الاعتماد فقط)'],
            ['name' => 'FINISHED_PRODUCT_DELIVERIES_DELETE', 'display_name' => 'حذف الإذن الصادر', 'group_name' => 'المنتجات النهائية', 'description' => 'حذف الإذن الصادر (قبل الاعتماد فقط)'],
            ['name' => 'FINISHED_PRODUCT_DELIVERIES_APPROVE', 'display_name' => 'اعتماد الإذن الصادر', 'group_name' => 'المنتجات النهائية', 'description' => 'اعتماد الإذن الصادر (للإدارة العليا)'],
            ['name' => 'FINISHED_PRODUCT_DELIVERIES_REJECT', 'display_name' => 'رفض الإذن الصادر', 'group_name' => 'المنتجات النهائية', 'description' => 'رفض الإذن الصادر'],
            ['name' => 'FINISHED_PRODUCT_DELIVERIES_PRINT', 'display_name' => 'طباعة الإذن الصادر', 'group_name' => 'المنتجات النهائية', 'description' => 'طباعة الإذن الصادر (بعد الاعتماد فقط)'],
            ['name' => 'FINISHED_PRODUCT_DELIVERIES_VIEW_ALL', 'display_name' => 'عرض جميع الإذونات', 'group_name' => 'المنتجات النهائية', 'description' => 'عرض جميع الإذونات (للإدارة والمشرفين)'],

            // ============================================
            // صلاحيات قديمة - Backward Compatibility
            // ============================================
            ['name' => 'MANAGE_USERS', 'display_name' => 'إدارة المستخدمين', 'display_name_en' => 'Manage Users', 'group_name' => 'إدارة المستخدمين', 'group_name_en' => 'User Management', 'description' => 'إدارة المستخدمين والموظفين', 'description_en' => 'Manage Users and Employees'],
            ['name' => 'MANAGE_ROLES', 'display_name' => 'إدارة الأدوار', 'display_name_en' => 'Manage Roles', 'group_name' => 'إدارة الأدوار', 'group_name_en' => 'Role Management', 'description' => 'إدارة أدوار المستخدمين', 'description_en' => 'Manage User Roles'],
            ['name' => 'MANAGE_PERMISSIONS', 'display_name' => 'إدارة الصلاحيات', 'display_name_en' => 'Manage Permissions', 'group_name' => 'إدارة الصلاحيات', 'group_name_en' => 'Permission Management', 'description' => 'إدارة صلاحيات النظام', 'description_en' => 'Manage System Permissions'],
            ['name' => 'MANAGE_MATERIALS', 'display_name' => 'إدارة المواد الخام', 'display_name_en' => 'Manage Materials', 'group_name' => 'المستودع - المواد الخام', 'group_name_en' => 'Warehouse - Raw Materials', 'description' => 'إدارة المواد الخام والمخزون', 'description_en' => 'Manage Raw Materials & Inventory'],
            ['name' => 'MANAGE_SUPPLIERS', 'display_name' => 'إدارة الموردين', 'display_name_en' => 'Manage Suppliers', 'group_name' => 'المستودع - الموردين', 'group_name_en' => 'Warehouse - Suppliers', 'description' => 'إدارة الموردين والموزعين', 'description_en' => 'Manage Suppliers & Distributors'],
            ['name' => 'MANAGE_WAREHOUSES', 'display_name' => 'إدارة المخازن', 'display_name_en' => 'Manage Warehouses', 'group_name' => 'المستودع - المخازن', 'group_name_en' => 'Warehouse - Stores', 'description' => 'إدارة المخازن والمواقع', 'description_en' => 'Manage Warehouses & Locations'],
            ['name' => 'WAREHOUSE_TRANSFERS', 'display_name' => 'تحويلات المخازن', 'display_name_en' => 'Warehouse Transfers', 'group_name' => 'المستودع - المخازن', 'group_name_en' => 'Warehouse - Stores', 'description' => 'إدارة تحويلات المخازن', 'description_en' => 'Manage Warehouse Transfers'],
            ['name' => 'STAGE1_STANDS', 'display_name' => 'المرحلة الأولى - الاستاندات', 'display_name_en' => 'Stage 1 - Stands', 'group_name' => 'المرحلة الأولى - الاستاندات', 'group_name_en' => 'Production - Stage 1', 'description' => 'إدارة المرحلة الأولى من الإنتاج', 'description_en' => 'Manage Stage 1 Production'],
            ['name' => 'STAGE2_PROCESSING', 'display_name' => 'المرحلة الثانية - المعالجة', 'display_name_en' => 'Stage 2 - Processing', 'group_name' => 'المرحلة الثانية - المعالجة', 'group_name_en' => 'Production - Stage 2', 'description' => 'إدارة المرحلة الثانية من الإنتاج', 'description_en' => 'Manage Stage 2 Production'],
            ['name' => 'STAGE3_COILS', 'display_name' => 'المرحلة الثالثة - اللفائف', 'display_name_en' => 'Stage 3 - Coils', 'group_name' => 'المرحلة الثالثة - اللفائف', 'group_name_en' => 'Production - Stage 3', 'description' => 'إدارة المرحلة الثالثة من الإنتاج', 'description_en' => 'Manage Stage 3 Production'],
            ['name' => 'STAGE4_PACKAGING', 'display_name' => 'المرحلة الرابعة - التعبئة', 'display_name_en' => 'Stage 4 - Packaging', 'group_name' => 'المرحلة الرابعة - التعبئة', 'group_name_en' => 'Production - Stage 4', 'description' => 'إدارة المرحلة الرابعة من الإنتاج', 'description_en' => 'Manage Stage 4 Production'],
            ['name' => 'PURCHASE_INVOICES', 'display_name' => 'فواتير الشراء', 'display_name_en' => 'Purchase Invoices', 'group_name' => 'المستودع - فواتير الشراء', 'group_name_en' => 'Warehouse - Purchase Invoices', 'description' => 'إدارة فواتير الشراء', 'description_en' => 'Manage Purchase Invoices'],
            ['name' => 'SALES_INVOICES', 'display_name' => 'فواتير المبيعات', 'display_name_en' => 'Sales Invoices', 'group_name' => 'التقارير العامة', 'group_name_en' => 'General Reports', 'description' => 'إدارة فواتير المبيعات', 'description_en' => 'Manage Sales Invoices'],
            ['name' => 'MANAGE_MOVEMENTS', 'display_name' => 'إدارة الحركات', 'display_name_en' => 'Manage Movements', 'group_name' => 'المستودع - حركات المواد', 'group_name_en' => 'Warehouse - Movements', 'description' => 'إدارة حركات المخزون', 'description_en' => 'Manage Inventory Movements'],
        ];

        foreach ($permissions as &$permission) {
            $permission['created_at'] = now();
            $permission['updated_at'] = now();
        }

        // Insert permissions using updateOrInsert to avoid duplicates
        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
