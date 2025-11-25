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
            ['name' => 'MENU_DASHBOARD', 'display_name' => 'لوحة التحكم', 'group_name' => 'لوحة التحكم', 'description' => 'إظهار قائمة لوحة التحكم'],

            // ============================================
            // المستودع - Warehouse (Parent Group)
            // ============================================
            ['name' => 'MENU_WAREHOUSE', 'display_name' => 'المستودع', 'group_name' => 'المستودع', 'description' => 'إظهار قائمة المستودع الرئيسية'],

            // ============================================
            // المواد الخام - Materials
            // ============================================
            ['name' => 'MENU_WAREHOUSE_MATERIALS', 'display_name' => 'المواد الخام', 'group_name' => 'المواد الخام', 'description' => 'إظهار قائمة المواد الخام'],
            ['name' => 'WAREHOUSE_MATERIALS_READ', 'display_name' => 'عرض المواد الخام', 'group_name' => 'المواد الخام', 'description' => 'عرض المواد الخام'],
            ['name' => 'WAREHOUSE_MATERIALS_CREATE', 'display_name' => 'إضافة مواد خام', 'group_name' => 'المواد الخام', 'description' => 'إضافة مواد خام جديدة'],
            ['name' => 'WAREHOUSE_MATERIALS_UPDATE', 'display_name' => 'تعديل المواد الخام', 'group_name' => 'المواد الخام', 'description' => 'تعديل المواد الخام'],
            ['name' => 'WAREHOUSE_MATERIALS_DELETE', 'display_name' => 'حذف المواد الخام', 'group_name' => 'المواد الخام', 'description' => 'حذف المواد الخام'],

            // ============================================
            // المخازن - Stores
            // ============================================
            ['name' => 'MENU_WAREHOUSE_STORES', 'display_name' => 'المخازن', 'group_name' => 'المخازن', 'description' => 'إظهار قائمة المخازن'],
            ['name' => 'WAREHOUSE_STORES_READ', 'display_name' => 'عرض المخازن', 'group_name' => 'المخازن', 'description' => 'عرض المخازن'],
            ['name' => 'WAREHOUSE_STORES_CREATE', 'display_name' => 'إضافة مخزن', 'group_name' => 'المخازن', 'description' => 'إضافة مخزن جديد'],
            ['name' => 'WAREHOUSE_STORES_UPDATE', 'display_name' => 'تعديل المخزن', 'group_name' => 'المخازن', 'description' => 'تعديل بيانات المخزن'],
            ['name' => 'WAREHOUSE_STORES_DELETE', 'display_name' => 'حذف المخزن', 'group_name' => 'المخازن', 'description' => 'حذف المخزن'],

            // ============================================
            // مذكرات التسليم - Delivery Notes
            // ============================================
            ['name' => 'MENU_WAREHOUSE_DELIVERY_NOTES', 'display_name' => 'مذكرات التسليم', 'group_name' => 'مذكرات التسليم', 'description' => 'إظهار قائمة مذكرات التسليم'],
            ['name' => 'WAREHOUSE_DELIVERY_NOTES_READ', 'display_name' => 'عرض مذكرات التسليم', 'group_name' => 'مذكرات التسليم', 'description' => 'عرض مذكرات التسليم'],
            ['name' => 'WAREHOUSE_DELIVERY_NOTES_CREATE', 'display_name' => 'إنشاء مذكرة تسليم', 'group_name' => 'مذكرات التسليم', 'description' => 'إنشاء مذكرة تسليم جديدة'],
            ['name' => 'WAREHOUSE_DELIVERY_NOTES_UPDATE', 'display_name' => 'تعديل مذكرة التسليم', 'group_name' => 'مذكرات التسليم', 'description' => 'تعديل مذكرات التسليم'],
            ['name' => 'WAREHOUSE_DELIVERY_NOTES_DELETE', 'display_name' => 'حذف مذكرة التسليم', 'group_name' => 'مذكرات التسليم', 'description' => 'حذف مذكرات التسليم'],

            // ============================================
            // فواتير الشراء - Purchase Invoices
            // ============================================
            ['name' => 'MENU_WAREHOUSE_PURCHASE_INVOICES', 'display_name' => 'فواتير الشراء', 'group_name' => 'فواتير الشراء', 'description' => 'إظهار قائمة فواتير الشراء'],
            ['name' => 'WAREHOUSE_PURCHASE_INVOICES_READ', 'display_name' => 'عرض فواتير الشراء', 'group_name' => 'فواتير الشراء', 'description' => 'عرض فواتير الشراء'],
            ['name' => 'WAREHOUSE_PURCHASE_INVOICES_CREATE', 'display_name' => 'إنشاء فاتورة شراء', 'group_name' => 'فواتير الشراء', 'description' => 'إنشاء فاتورة شراء جديدة'],
            ['name' => 'WAREHOUSE_PURCHASE_INVOICES_UPDATE', 'display_name' => 'تعديل فاتورة الشراء', 'group_name' => 'فواتير الشراء', 'description' => 'تعديل فواتير الشراء'],
            ['name' => 'WAREHOUSE_PURCHASE_INVOICES_DELETE', 'display_name' => 'حذف فاتورة الشراء', 'group_name' => 'فواتير الشراء', 'description' => 'حذف فواتير الشراء'],

            // ============================================
            // الموردين - Suppliers
            // ============================================
            ['name' => 'MENU_WAREHOUSE_SUPPLIERS', 'display_name' => 'الموردين', 'group_name' => 'الموردين', 'description' => 'إظهار قائمة الموردين'],
            ['name' => 'WAREHOUSE_SUPPLIERS_READ', 'display_name' => 'عرض الموردين', 'group_name' => 'الموردين', 'description' => 'عرض الموردين'],
            ['name' => 'WAREHOUSE_SUPPLIERS_CREATE', 'display_name' => 'إضافة مورد', 'group_name' => 'الموردين', 'description' => 'إضافة مورد جديد'],
            ['name' => 'WAREHOUSE_SUPPLIERS_UPDATE', 'display_name' => 'تعديل بيانات المورد', 'group_name' => 'الموردين', 'description' => 'تعديل بيانات المورد'],
            ['name' => 'WAREHOUSE_SUPPLIERS_DELETE', 'display_name' => 'حذف المورد', 'group_name' => 'الموردين', 'description' => 'حذف المورد'],

            // ============================================
            // أنواع المواد - Material Types
            // ============================================
            ['name' => 'MENU_WAREHOUSE_MATERIAL_TYPES', 'display_name' => 'أنواع المواد', 'group_name' => 'أنواع المواد', 'description' => 'إظهار قائمة أنواع المواد'],
            ['name' => 'WAREHOUSE_MATERIAL_TYPES_READ', 'display_name' => 'عرض أنواع المواد', 'group_name' => 'أنواع المواد', 'description' => 'عرض أنواع المواد'],
            ['name' => 'WAREHOUSE_MATERIAL_TYPES_CREATE', 'display_name' => 'إضافة نوع مادة', 'group_name' => 'أنواع المواد', 'description' => 'إضافة نوع مادة جديد'],
            ['name' => 'WAREHOUSE_MATERIAL_TYPES_UPDATE', 'display_name' => 'تعديل نوع المادة', 'group_name' => 'أنواع المواد', 'description' => 'تعديل أنواع المواد'],
            ['name' => 'WAREHOUSE_MATERIAL_TYPES_DELETE', 'display_name' => 'حذف نوع المادة', 'group_name' => 'أنواع المواد', 'description' => 'حذف أنواع المواد'],

            // ============================================
            // وحدات القياس - Units
            // ============================================
            ['name' => 'MENU_WAREHOUSE_UNITS', 'display_name' => 'وحدات القياس', 'group_name' => 'وحدات القياس', 'description' => 'إظهار قائمة وحدات القياس'],
            ['name' => 'WAREHOUSE_UNITS_READ', 'display_name' => 'عرض الوحدات', 'group_name' => 'وحدات القياس', 'description' => 'عرض وحدات القياس'],
            ['name' => 'WAREHOUSE_UNITS_CREATE', 'display_name' => 'إضافة وحدة', 'group_name' => 'وحدات القياس', 'description' => 'إضافة وحدة قياس جديدة'],
            ['name' => 'WAREHOUSE_UNITS_UPDATE', 'display_name' => 'تعديل الوحدة', 'group_name' => 'وحدات القياس', 'description' => 'تعديل وحدات القياس'],
            ['name' => 'WAREHOUSE_UNITS_DELETE', 'display_name' => 'حذف الوحدة', 'group_name' => 'وحدات القياس', 'description' => 'حذف وحدات القياس'],

            // ============================================
            // تسجيل البضاعة - Warehouse Registration
            // ============================================
            ['name' => 'MENU_WAREHOUSE_REGISTRATION', 'display_name' => 'تسجيل البضاعة', 'group_name' => 'تسجيل البضاعة', 'description' => 'إظهار قائمة تسجيل البضاعة'],
            ['name' => 'WAREHOUSE_REGISTRATION_READ', 'display_name' => 'عرض تسجيلات البضاعة', 'group_name' => 'تسجيل البضاعة', 'description' => 'عرض قائمة تسجيلات البضاعة'],
            ['name' => 'WAREHOUSE_REGISTRATION_CREATE', 'display_name' => 'إضافة تسجيل بضاعة', 'group_name' => 'تسجيل البضاعة', 'description' => 'إنشاء تسجيل بضاعة جديد'],
            ['name' => 'WAREHOUSE_REGISTRATION_UPDATE', 'display_name' => 'تعديل تسجيل البضاعة', 'group_name' => 'تسجيل البضاعة', 'description' => 'تعديل تسجيلات البضاعة'],
            ['name' => 'WAREHOUSE_REGISTRATION_LOCK', 'display_name' => 'قفل التسجيل', 'group_name' => 'تسجيل البضاعة', 'description' => 'قفل تسجيلات البضاعة'],
            ['name' => 'WAREHOUSE_REGISTRATION_UNLOCK', 'display_name' => 'فتح القفل', 'group_name' => 'تسجيل البضاعة', 'description' => 'فتح تسجيلات البضاعة المقفلة'],
            ['name' => 'WAREHOUSE_REGISTRATION_TRANSFER', 'display_name' => 'نقل البضاعة للإنتاج', 'group_name' => 'تسجيل البضاعة', 'description' => 'نقل البضاعة إلى المراحل الإنتاجية'],

            // ============================================
            // تسوية المستودع - Warehouse Reconciliation
            // ============================================
            ['name' => 'MENU_WAREHOUSE_RECONCILIATION', 'display_name' => 'تسوية المستودع', 'group_name' => 'تسوية المستودع', 'description' => 'إظهار قائمة التسوية'],
            ['name' => 'WAREHOUSE_RECONCILIATION_READ', 'display_name' => 'عرض التسويات', 'group_name' => 'تسوية المستودع', 'description' => 'عرض قائمة التسويات'],
            ['name' => 'WAREHOUSE_RECONCILIATION_CREATE', 'display_name' => 'إنشاء تسوية', 'group_name' => 'تسوية المستودع', 'description' => 'إنشاء تسوية جديدة'],
            ['name' => 'WAREHOUSE_RECONCILIATION_UPDATE', 'display_name' => 'تعديل التسوية', 'group_name' => 'تسوية المستودع', 'description' => 'تعديل التسويات'],
            ['name' => 'WAREHOUSE_RECONCILIATION_DELETE', 'display_name' => 'حذف التسوية', 'group_name' => 'تسوية المستودع', 'description' => 'حذف التسويات'],
            ['name' => 'WAREHOUSE_RECONCILIATION_MANAGEMENT', 'display_name' => 'إدارة التسويات', 'group_name' => 'تسوية المستودع', 'description' => 'لوحة التحكم الإدارية للتسويات'],
            ['name' => 'WAREHOUSE_RECONCILIATION_LINK_INVOICE', 'display_name' => 'ربط الفواتير', 'group_name' => 'تسوية المستودع', 'description' => 'ربط الفواتير بمذكرات التسليم'],

            // ============================================
            // حركات المواد - Material Movements
            // ============================================
            ['name' => 'MENU_WAREHOUSE_MOVEMENTS', 'display_name' => 'حركات المواد', 'group_name' => 'حركات المواد', 'description' => 'إظهار قائمة حركات المواد'],
            ['name' => 'WAREHOUSE_MOVEMENTS_READ', 'display_name' => 'عرض حركات المواد', 'group_name' => 'حركات المواد', 'description' => 'عرض سجل حركات المواد'],
            ['name' => 'WAREHOUSE_MOVEMENTS_DETAILS', 'display_name' => 'عرض تفاصيل الحركة', 'group_name' => 'حركات المواد', 'description' => 'عرض تفاصيل حركة المادة'],

            // ============================================
            // إعدادات المستودع - Warehouse Settings
            // ============================================
            ['name' => 'MENU_WAREHOUSE_SETTINGS', 'display_name' => 'إعدادات المستودع', 'group_name' => 'إعدادات المستودع', 'description' => 'إظهار قائمة إعدادات المستودع'],

            // ============================================
            // تقارير المستودع - Warehouse Reports
            // ============================================
            ['name' => 'MENU_WAREHOUSE_REPORTS', 'display_name' => 'تقارير المستودع', 'group_name' => 'تقارير المستودع', 'description' => 'إظهار قائمة تقارير المستودع'],

            // ============================================
            // المرحلة الأولى - الاستاندات - Stage 1: Stands
            // ============================================
            ['name' => 'MENU_STAGE1_STANDS', 'display_name' => 'المرحلة الأولى - الاستاندات', 'group_name' => 'المرحلة الأولى - الاستاندات', 'description' => 'إظهار قائمة المرحلة الأولى'],
            ['name' => 'STAGE1_STANDS_READ', 'display_name' => 'عرض الاستاندات', 'group_name' => 'المرحلة الأولى - الاستاندات', 'description' => 'عرض الاستاندات'],
            ['name' => 'STAGE1_STANDS_CREATE', 'display_name' => 'إضافة استاند', 'group_name' => 'المرحلة الأولى - الاستاندات', 'description' => 'إنشاء استاند جديد'],
            ['name' => 'STAGE1_STANDS_UPDATE', 'display_name' => 'تعديل الاستاند', 'group_name' => 'المرحلة الأولى - الاستاندات', 'description' => 'تعديل الاستاندات'],
            ['name' => 'STAGE1_STANDS_DELETE', 'display_name' => 'حذف الاستاند', 'group_name' => 'المرحلة الأولى - الاستاندات', 'description' => 'حذف الاستاندات'],
            ['name' => 'STAGE1_BARCODE_SCAN', 'display_name' => 'مسح الباركود - المرحلة الأولى', 'group_name' => 'المرحلة الأولى - الاستاندات', 'description' => 'الوصول لصفحة مسح الباركود'],
            ['name' => 'STAGE1_WASTE_TRACKING', 'display_name' => 'تتبع الهدر - المرحلة الأولى', 'group_name' => 'المرحلة الأولى - الاستاندات', 'description' => 'تتبع الهدر في المرحلة الأولى'],

            // ============================================
            // المرحلة الثانية - المعالجة - Stage 2: Processing
            // ============================================
            ['name' => 'MENU_STAGE2_PROCESSING', 'display_name' => 'المرحلة الثانية - المعالجة', 'group_name' => 'المرحلة الثانية - المعالجة', 'description' => 'إظهار قائمة المرحلة الثانية'],
            ['name' => 'STAGE2_PROCESSING_READ', 'display_name' => 'عرض المعالجة', 'group_name' => 'المرحلة الثانية - المعالجة', 'description' => 'عرض عمليات المعالجة'],
            ['name' => 'STAGE2_PROCESSING_CREATE', 'display_name' => 'إضافة معالجة', 'group_name' => 'المرحلة الثانية - المعالجة', 'description' => 'إنشاء عملية معالجة جديدة'],
            ['name' => 'STAGE2_PROCESSING_UPDATE', 'display_name' => 'تعديل المعالجة', 'group_name' => 'المرحلة الثانية - المعالجة', 'description' => 'تعديل عمليات المعالجة'],
            ['name' => 'STAGE2_PROCESSING_DELETE', 'display_name' => 'حذف المعالجة', 'group_name' => 'المرحلة الثانية - المعالجة', 'description' => 'حذف عمليات المعالجة'],
            ['name' => 'STAGE2_COMPLETE_PROCESSING', 'display_name' => 'إتمام المعالجة', 'group_name' => 'المرحلة الثانية - المعالجة', 'description' => 'إتمام عمليات المعالجة'],
            ['name' => 'STAGE2_WASTE_STATISTICS', 'display_name' => 'إحصائيات الهدر - المرحلة الثانية', 'group_name' => 'المرحلة الثانية - المعالجة', 'description' => 'عرض إحصائيات الهدر'],

            // ============================================
            // المرحلة الثالثة - اللفائف - Stage 3: Coils
            // ============================================
            ['name' => 'MENU_STAGE3_COILS', 'display_name' => 'المرحلة الثالثة - اللفائف', 'group_name' => 'المرحلة الثالثة - اللفائف', 'description' => 'إظهار قائمة المرحلة الثالثة'],
            ['name' => 'STAGE3_COILS_READ', 'display_name' => 'عرض اللفائف', 'group_name' => 'المرحلة الثالثة - اللفائف', 'description' => 'عرض اللفائف'],
            ['name' => 'STAGE3_COILS_CREATE', 'display_name' => 'إضافة لفافة', 'group_name' => 'المرحلة الثالثة - اللفائف', 'description' => 'إنشاء لفافة جديدة'],
            ['name' => 'STAGE3_COILS_UPDATE', 'display_name' => 'تعديل اللفافة', 'group_name' => 'المرحلة الثالثة - اللفائف', 'description' => 'تعديل اللفائف'],
            ['name' => 'STAGE3_COILS_DELETE', 'display_name' => 'حذف اللفافة', 'group_name' => 'المرحلة الثالثة - اللفائف', 'description' => 'حذف اللفائف'],

            // ============================================
            // المرحلة الرابعة - التعبئة - Stage 4: Packaging
            // ============================================
            ['name' => 'MENU_STAGE4_PACKAGING', 'display_name' => 'المرحلة الرابعة - التعبئة', 'group_name' => 'المرحلة الرابعة - التعبئة', 'description' => 'إظهار قائمة المرحلة الرابعة'],
            ['name' => 'STAGE4_PACKAGING_READ', 'display_name' => 'عرض التعبئة', 'group_name' => 'المرحلة الرابعة - التعبئة', 'description' => 'عرض عمليات التعبئة'],
            ['name' => 'STAGE4_PACKAGING_CREATE', 'display_name' => 'إضافة تعبئة', 'group_name' => 'المرحلة الرابعة - التعبئة', 'description' => 'إنشاء عملية تعبئة جديدة'],
            ['name' => 'STAGE4_PACKAGING_UPDATE', 'display_name' => 'تعديل التعبئة', 'group_name' => 'المرحلة الرابعة - التعبئة', 'description' => 'تعديل عمليات التعبئة'],
            ['name' => 'STAGE4_PACKAGING_DELETE', 'display_name' => 'حذف التعبئة', 'group_name' => 'المرحلة الرابعة - التعبئة', 'description' => 'حذف عمليات التعبئة'],

            // ============================================
            // تتبع الإنتاج - Production Tracking
            // ============================================
            ['name' => 'MENU_PRODUCTION_TRACKING', 'display_name' => 'تتبع الإنتاج', 'group_name' => 'تتبع الإنتاج', 'description' => 'إظهار قائمة تتبع الإنتاج'],
            ['name' => 'PRODUCTION_TRACKING_SCAN', 'display_name' => 'مسح الباركود - تتبع الإنتاج', 'group_name' => 'تتبع الإنتاج', 'description' => 'الوصول لصفحة مسح الباركود في تتبع الإنتاج'],
            ['name' => 'PRODUCTION_IRON_JOURNEY', 'display_name' => 'رحلة الحديد', 'group_name' => 'تتبع الإنتاج', 'description' => 'الوصول لصفحة رحلة الحديد'],

            // ============================================
            // الورديات والعمال - Shifts & Workers
            // ============================================
            ['name' => 'MENU_SHIFTS_WORKERS', 'display_name' => 'الورديات والعمال', 'group_name' => 'الورديات والعمال', 'description' => 'إظهار قائمة الورديات والعمال'],
            ['name' => 'SHIFTS_READ', 'display_name' => 'عرض الورديات', 'group_name' => 'الورديات والعمال', 'description' => 'عرض قائمة الورديات'],
            ['name' => 'SHIFTS_CREATE', 'display_name' => 'إضافة وردية', 'group_name' => 'الورديات والعمال', 'description' => 'إضافة وردية جديدة'],
            ['name' => 'SHIFTS_UPDATE', 'display_name' => 'تعديل الوردية', 'group_name' => 'الورديات والعمال', 'description' => 'تعديل بيانات الوردية'],
            ['name' => 'SHIFTS_DELETE', 'display_name' => 'حذف الوردية', 'group_name' => 'الورديات والعمال', 'description' => 'حذف الوردية'],
            ['name' => 'SHIFTS_CURRENT', 'display_name' => 'الورديات الحالية', 'group_name' => 'الورديات والعمال', 'description' => 'عرض الورديات الحالية'],
            ['name' => 'SHIFTS_ATTENDANCE', 'display_name' => 'الحضور والغياب', 'group_name' => 'الورديات والعمال', 'description' => 'إدارة حضور وغياب العمال'],
            ['name' => 'WORKERS_READ', 'display_name' => 'عرض العمال', 'group_name' => 'الورديات والعمال', 'description' => 'عرض قائمة العمال'],
            ['name' => 'WORKERS_CREATE', 'display_name' => 'إضافة عامل', 'group_name' => 'الورديات والعمال', 'description' => 'إضافة عامل جديد'],
            ['name' => 'WORKERS_UPDATE', 'display_name' => 'تعديل بيانات العامل', 'group_name' => 'الورديات والعمال', 'description' => 'تعديل بيانات العامل'],
            ['name' => 'WORKERS_DELETE', 'display_name' => 'حذف العامل', 'group_name' => 'الورديات والعمال', 'description' => 'حذف العامل'],
            ['name' => 'WORKER_TEAMS_READ', 'display_name' => 'عرض مجموعات العمال', 'group_name' => 'الورديات والعمال', 'description' => 'عرض مجموعات العمال'],
            ['name' => 'WORKER_TEAMS_CREATE', 'display_name' => 'إضافة مجموعة عمال', 'group_name' => 'الورديات والعمال', 'description' => 'إضافة مجموعة عمال جديدة'],
            ['name' => 'WORKER_TEAMS_UPDATE', 'display_name' => 'تعديل مجموعة العمال', 'group_name' => 'الورديات والعمال', 'description' => 'تعديل مجموعة العمال'],
            ['name' => 'WORKER_TEAMS_DELETE', 'display_name' => 'حذف مجموعة العمال', 'group_name' => 'الورديات والعمال', 'description' => 'حذف مجموعة العمال'],

            // ============================================
            // الجودة والهدر - Quality & Waste
            // ============================================
            ['name' => 'MENU_QUALITY_WASTE', 'display_name' => 'الجودة والهدر', 'group_name' => 'الجودة والهدر', 'description' => 'إظهار قائمة الجودة والهدر'],
            ['name' => 'QUALITY_WASTE_REPORT', 'display_name' => 'تقرير الهدر', 'group_name' => 'الجودة والهدر', 'description' => 'عرض تقرير الهدر'],
            ['name' => 'QUALITY_MONITORING', 'display_name' => 'مراقبة الجودة', 'group_name' => 'الجودة والهدر', 'description' => 'الوصول لصفحة مراقبة الجودة'],
            ['name' => 'QUALITY_DOWNTIME_TRACKING', 'display_name' => 'تتبع التوقفات', 'group_name' => 'الجودة والهدر', 'description' => 'تتبع توقفات الإنتاج'],
            ['name' => 'QUALITY_WASTE_LIMITS', 'display_name' => 'حدود الهدر', 'group_name' => 'الجودة والهدر', 'description' => 'إدارة حدود الهدر المسموح'],

            // ============================================
            // التقارير الإنتاجية - Production Reports
            // ============================================
            ['name' => 'MENU_PRODUCTION_REPORTS', 'display_name' => 'التقارير الإنتاجية', 'group_name' => 'التقارير الإنتاجية', 'description' => 'إظهار قائمة التقارير الإنتاجية'],
            ['name' => 'REPORTS_WIP', 'display_name' => 'تقرير الأعمال غير المنتهية', 'group_name' => 'التقارير الإنتاجية', 'description' => 'عرض تقرير الأعمال غير المنتهية'],
            ['name' => 'REPORTS_SHIFT_DASHBOARD', 'display_name' => 'ملخص الوردية', 'group_name' => 'التقارير الإنتاجية', 'description' => 'عرض ملخص الوردية'],
            ['name' => 'REPORTS_STANDS_USAGE', 'display_name' => 'تاريخ استخدام الستاندات', 'group_name' => 'التقارير الإنتاجية', 'description' => 'عرض تاريخ استخدام الستاندات'],
            ['name' => 'REPORTS_WORKER_PERFORMANCE', 'display_name' => 'تقرير أداء العمال', 'group_name' => 'التقارير الإنتاجية', 'description' => 'عرض تقرير أداء العمال'],

            // ============================================
            // التقارير العامة - General Reports
            // ============================================
            ['name' => 'VIEW_REPORTS', 'display_name' => 'التقارير والإحصائيات', 'group_name' => 'التقارير العامة', 'description' => 'عرض التقارير والإحصائيات'],
            ['name' => 'PRODUCTION_REPORTS', 'display_name' => 'تقارير الإنتاج', 'group_name' => 'التقارير العامة', 'description' => 'تقارير الإنتاج التفصيلية'],
            ['name' => 'INVENTORY_REPORTS', 'display_name' => 'تقارير المخزون', 'group_name' => 'التقارير العامة', 'description' => 'تقارير المخزون والحركات'],

            // ============================================
            // لوحة التحكم - Dashboard (Additional)
            // ============================================
            ['name' => 'VIEW_DASHBOARD', 'display_name' => 'لوحة التحكم', 'group_name' => 'لوحة التحكم', 'description' => 'عرض لوحة التحكم الرئيسية'],

            // ============================================
            // الإدارة - Management
            // ============================================
            ['name' => 'MENU_MANAGEMENT', 'display_name' => 'الإدارة', 'group_name' => 'الإدارة', 'description' => 'إظهار قائمة الإدارة'],

            // ============================================
            // إدارة المستخدمين - Manage Users
            // ============================================
            ['name' => 'MENU_MANAGE_USERS', 'display_name' => 'إدارة المستخدمين', 'group_name' => 'إدارة المستخدمين', 'description' => 'إظهار قائمة إدارة المستخدمين'],
            ['name' => 'MANAGE_USERS_READ', 'display_name' => 'عرض المستخدمين', 'group_name' => 'إدارة المستخدمين', 'description' => 'عرض المستخدمين'],
            ['name' => 'MANAGE_USERS_CREATE', 'display_name' => 'إضافة مستخدم', 'group_name' => 'إدارة المستخدمين', 'description' => 'إضافة مستخدم جديد'],
            ['name' => 'MANAGE_USERS_UPDATE', 'display_name' => 'تعديل المستخدم', 'group_name' => 'إدارة المستخدمين', 'description' => 'تعديل بيانات المستخدم'],
            ['name' => 'MANAGE_USERS_DELETE', 'display_name' => 'حذف المستخدم', 'group_name' => 'إدارة المستخدمين', 'description' => 'حذف المستخدم'],

            // ============================================
            // إدارة الأدوار - Manage Roles
            // ============================================
            ['name' => 'MENU_MANAGE_ROLES', 'display_name' => 'إدارة الأدوار', 'group_name' => 'إدارة الأدوار', 'description' => 'إظهار قائمة إدارة الأدوار'],
            ['name' => 'MANAGE_ROLES_READ', 'display_name' => 'عرض الأدوار', 'group_name' => 'إدارة الأدوار', 'description' => 'عرض الأدوار'],
            ['name' => 'MANAGE_ROLES_CREATE', 'display_name' => 'إضافة دور', 'group_name' => 'إدارة الأدوار', 'description' => 'إضافة دور جديد'],
            ['name' => 'MANAGE_ROLES_UPDATE', 'display_name' => 'تعديل الدور', 'group_name' => 'إدارة الأدوار', 'description' => 'تعديل الدور'],
            ['name' => 'MANAGE_ROLES_DELETE', 'display_name' => 'حذف الدور', 'group_name' => 'إدارة الأدوار', 'description' => 'حذف الدور'],

            // ============================================
            // إدارة الصلاحيات - Manage Permissions
            // ============================================
            ['name' => 'MENU_MANAGE_PERMISSIONS', 'display_name' => 'إدارة الصلاحيات', 'group_name' => 'إدارة الصلاحيات', 'description' => 'إظهار قائمة إدارة الصلاحيات'],
            ['name' => 'MANAGE_PERMISSIONS_READ', 'display_name' => 'عرض الصلاحيات', 'group_name' => 'إدارة الصلاحيات', 'description' => 'عرض الصلاحيات'],
            ['name' => 'MANAGE_PERMISSIONS_CREATE', 'display_name' => 'إضافة صلاحية', 'group_name' => 'إدارة الصلاحيات', 'description' => 'إضافة صلاحية جديدة'],
            ['name' => 'MANAGE_PERMISSIONS_UPDATE', 'display_name' => 'تعديل الصلاحية', 'group_name' => 'إدارة الصلاحيات', 'description' => 'تعديل الصلاحية'],
            ['name' => 'MANAGE_PERMISSIONS_DELETE', 'display_name' => 'حذف الصلاحية', 'group_name' => 'إدارة الصلاحيات', 'description' => 'حذف الصلاحية'],

            // ============================================
            // الإعدادات - Settings
            // ============================================
            ['name' => 'MENU_SETTINGS', 'display_name' => 'الإعدادات', 'group_name' => 'الإعدادات', 'description' => 'إظهار قائمة الإعدادات'],
            ['name' => 'SETTINGS_GENERAL', 'display_name' => 'الإعدادات العامة', 'group_name' => 'الإعدادات', 'description' => 'الوصول للإعدادات العامة'],
            ['name' => 'SETTINGS_CALCULATIONS', 'display_name' => 'إعدادات الحسابات', 'group_name' => 'الإعدادات', 'description' => 'إعدادات الحسابات والمعادلات'],
            ['name' => 'SETTINGS_BARCODE', 'display_name' => 'إعدادات الباركود', 'group_name' => 'الإعدادات', 'description' => 'إعدادات الباركود'],
            ['name' => 'SETTINGS_NOTIFICATIONS', 'display_name' => 'إعدادات الإشعارات', 'group_name' => 'الإعدادات', 'description' => 'إعدادات الإشعارات'],

            // ============================================
            // صلاحيات قديمة - Backward Compatibility
            // ============================================
            ['name' => 'MANAGE_USERS', 'display_name' => 'إدارة المستخدمين', 'group_name' => 'إدارة المستخدمين', 'description' => 'إدارة المستخدمين والموظفين'],
            ['name' => 'MANAGE_ROLES', 'display_name' => 'إدارة الأدوار', 'group_name' => 'إدارة الأدوار', 'description' => 'إدارة أدوار المستخدمين'],
            ['name' => 'MANAGE_PERMISSIONS', 'display_name' => 'إدارة الصلاحيات', 'group_name' => 'إدارة الصلاحيات', 'description' => 'إدارة صلاحيات النظام'],
            ['name' => 'MANAGE_MATERIALS', 'display_name' => 'إدارة المواد الخام', 'group_name' => 'المواد الخام', 'description' => 'إدارة المواد الخام والمخزون'],
            ['name' => 'MANAGE_SUPPLIERS', 'display_name' => 'إدارة الموردين', 'group_name' => 'الموردين', 'description' => 'إدارة الموردين والموزعين'],
            ['name' => 'MANAGE_WAREHOUSES', 'display_name' => 'إدارة المخازن', 'group_name' => 'المخازن', 'description' => 'إدارة المخازن والمواقع'],
            ['name' => 'WAREHOUSE_TRANSFERS', 'display_name' => 'تحويلات المخازن', 'group_name' => 'المخازن', 'description' => 'إدارة تحويلات المخازن'],
            ['name' => 'STAGE1_STANDS', 'display_name' => 'المرحلة الأولى - الاستاندات', 'group_name' => 'المرحلة الأولى - الاستاندات', 'description' => 'إدارة المرحلة الأولى من الإنتاج'],
            ['name' => 'STAGE2_PROCESSING', 'display_name' => 'المرحلة الثانية - المعالجة', 'group_name' => 'المرحلة الثانية - المعالجة', 'description' => 'إدارة المرحلة الثانية من الإنتاج'],
            ['name' => 'STAGE3_COILS', 'display_name' => 'المرحلة الثالثة - اللفائف', 'group_name' => 'المرحلة الثالثة - اللفائف', 'description' => 'إدارة المرحلة الثالثة من الإنتاج'],
            ['name' => 'STAGE4_PACKAGING', 'display_name' => 'المرحلة الرابعة - التعبئة', 'group_name' => 'المرحلة الرابعة - التعبئة', 'description' => 'إدارة المرحلة الرابعة من الإنتاج'],
            ['name' => 'PURCHASE_INVOICES', 'display_name' => 'فواتير الشراء', 'group_name' => 'فواتير الشراء', 'description' => 'إدارة فواتير الشراء'],
            ['name' => 'SALES_INVOICES', 'display_name' => 'فواتير المبيعات', 'group_name' => 'التقارير العامة', 'description' => 'إدارة فواتير المبيعات'],
            ['name' => 'MANAGE_MOVEMENTS', 'display_name' => 'إدارة الحركات', 'group_name' => 'حركات المواد', 'description' => 'إدارة حركات المخزون'],
        ];

        foreach ($permissions as &$permission) {
            $permission['created_at'] = now();
            $permission['updated_at'] = now();
        }

        DB::table('permissions')->insert($permissions);
    }
}
