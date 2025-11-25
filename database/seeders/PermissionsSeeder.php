<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'MENU_DASHBOARD', 'display_name' => 'لوحة التحكم', 'group_name' => 'لوحة التحكم', 'description' => 'إظهار قائمة لوحة التحكم'],

            // Warehouse Menu & Operations
            ['name' => 'MENU_WAREHOUSE', 'display_name' => 'المستودع', 'group_name' => 'المستودع', 'description' => 'إظهار قائمة المستودع الرئيسية'],
            ['name' => 'MENU_WAREHOUSE_MATERIALS', 'display_name' => 'المواد الخام', 'group_name' => 'المستودع', 'description' => 'إظهار قائمة المواد الخام'],
            ['name' => 'MENU_WAREHOUSE_STORES', 'display_name' => 'المخازن', 'group_name' => 'المستودع', 'description' => 'إظهار قائمة المخازن'],
            ['name' => 'MENU_WAREHOUSE_DELIVERY_NOTES', 'display_name' => 'مذكرات التسليم', 'group_name' => 'المستودع', 'description' => 'إظهار قائمة مذكرات التسليم'],
            ['name' => 'MENU_WAREHOUSE_PURCHASE_INVOICES', 'display_name' => 'فواتير الشراء', 'group_name' => 'المستودع', 'description' => 'إظهار قائمة فواتير الشراء'],
            ['name' => 'MENU_WAREHOUSE_SUPPLIERS', 'display_name' => 'الموردين', 'group_name' => 'المستودع', 'description' => 'إظهار قائمة الموردين'],
            ['name' => 'MENU_WAREHOUSE_SETTINGS', 'display_name' => 'إعدادات المستودع', 'group_name' => 'المستودع', 'description' => 'إظهار قائمة إعدادات المستودع'],
            ['name' => 'MENU_WAREHOUSE_REPORTS', 'display_name' => 'تقارير المستودع', 'group_name' => 'المستودع', 'description' => 'إظهار قائمة تقارير المستودع'],

            // Warehouse Operations
            ['name' => 'WAREHOUSE_MATERIALS_READ', 'display_name' => 'عرض المواد الخام', 'group_name' => 'المستودع', 'description' => 'عرض المواد الخام'],
            ['name' => 'WAREHOUSE_MATERIALS_CREATE', 'display_name' => 'إضافة مواد خام', 'group_name' => 'المستودع', 'description' => 'إضافة مواد خام جديدة'],
            ['name' => 'WAREHOUSE_MATERIALS_UPDATE', 'display_name' => 'تعديل المواد الخام', 'group_name' => 'المستودع', 'description' => 'تعديل المواد الخام'],
            ['name' => 'WAREHOUSE_MATERIALS_DELETE', 'display_name' => 'حذف المواد الخام', 'group_name' => 'المستودع', 'description' => 'حذف المواد الخام'],

            ['name' => 'WAREHOUSE_STORES_READ', 'display_name' => 'عرض المخازن', 'group_name' => 'المستودع', 'description' => 'عرض المخازن'],
            ['name' => 'WAREHOUSE_STORES_CREATE', 'display_name' => 'إضافة مخزن', 'group_name' => 'المستودع', 'description' => 'إضافة مخزن جديد'],
            ['name' => 'WAREHOUSE_STORES_UPDATE', 'display_name' => 'تعديل المخزن', 'group_name' => 'المستودع', 'description' => 'تعديل بيانات المخزن'],
            ['name' => 'WAREHOUSE_STORES_DELETE', 'display_name' => 'حذف المخزن', 'group_name' => 'المستودع', 'description' => 'حذف المخزن'],

            ['name' => 'WAREHOUSE_DELIVERY_NOTES_READ', 'display_name' => 'عرض مذكرات التسليم', 'group_name' => 'المستودع', 'description' => 'عرض مذكرات التسليم'],
            ['name' => 'WAREHOUSE_DELIVERY_NOTES_CREATE', 'display_name' => 'إنشاء مذكرة تسليم', 'group_name' => 'المستودع', 'description' => 'إنشاء مذكرة تسليم جديدة'],
            ['name' => 'WAREHOUSE_DELIVERY_NOTES_UPDATE', 'display_name' => 'تعديل مذكرة التسليم', 'group_name' => 'المستودع', 'description' => 'تعديل مذكرات التسليم'],
            ['name' => 'WAREHOUSE_DELIVERY_NOTES_DELETE', 'display_name' => 'حذف مذكرة التسليم', 'group_name' => 'المستودع', 'description' => 'حذف مذكرات التسليم'],

            ['name' => 'WAREHOUSE_PURCHASE_INVOICES_READ', 'display_name' => 'عرض فواتير الشراء', 'group_name' => 'المستودع', 'description' => 'عرض فواتير الشراء'],
            ['name' => 'WAREHOUSE_PURCHASE_INVOICES_CREATE', 'display_name' => 'إنشاء فاتورة شراء', 'group_name' => 'المستودع', 'description' => 'إنشاء فاتورة شراء جديدة'],
            ['name' => 'WAREHOUSE_PURCHASE_INVOICES_UPDATE', 'display_name' => 'تعديل فاتورة الشراء', 'group_name' => 'المستودع', 'description' => 'تعديل فواتير الشراء'],
            ['name' => 'WAREHOUSE_PURCHASE_INVOICES_DELETE', 'display_name' => 'حذف فاتورة الشراء', 'group_name' => 'المستودع', 'description' => 'حذف فواتير الشراء'],

            ['name' => 'WAREHOUSE_SUPPLIERS_READ', 'display_name' => 'عرض الموردين', 'group_name' => 'المستودع', 'description' => 'عرض الموردين'],
            ['name' => 'WAREHOUSE_SUPPLIERS_CREATE', 'display_name' => 'إضافة مورد', 'group_name' => 'المستودع', 'description' => 'إضافة مورد جديد'],
            ['name' => 'WAREHOUSE_SUPPLIERS_UPDATE', 'display_name' => 'تعديل بيانات المورد', 'group_name' => 'المستودع', 'description' => 'تعديل بيانات المورد'],
            ['name' => 'WAREHOUSE_SUPPLIERS_DELETE', 'display_name' => 'حذف المورد', 'group_name' => 'المستودع', 'description' => 'حذف المورد'],

            // Material Types & Units
            ['name' => 'WAREHOUSE_MATERIAL_TYPES_READ', 'display_name' => 'عرض أنواع المواد', 'group_name' => 'المستودع', 'description' => 'عرض أنواع المواد'],
            ['name' => 'WAREHOUSE_MATERIAL_TYPES_CREATE', 'display_name' => 'إضافة نوع مادة', 'group_name' => 'المستودع', 'description' => 'إضافة نوع مادة جديد'],
            ['name' => 'WAREHOUSE_MATERIAL_TYPES_UPDATE', 'display_name' => 'تعديل نوع المادة', 'group_name' => 'المستودع', 'description' => 'تعديل أنواع المواد'],
            ['name' => 'WAREHOUSE_MATERIAL_TYPES_DELETE', 'display_name' => 'حذف نوع المادة', 'group_name' => 'المستودع', 'description' => 'حذف أنواع المواد'],

            ['name' => 'WAREHOUSE_UNITS_READ', 'display_name' => 'عرض الوحدات', 'group_name' => 'المستودع', 'description' => 'عرض وحدات القياس'],
            ['name' => 'WAREHOUSE_UNITS_CREATE', 'display_name' => 'إضافة وحدة', 'group_name' => 'المستودع', 'description' => 'إضافة وحدة قياس جديدة'],
            ['name' => 'WAREHOUSE_UNITS_UPDATE', 'display_name' => 'تعديل الوحدة', 'group_name' => 'المستودع', 'description' => 'تعديل وحدات القياس'],
            ['name' => 'WAREHOUSE_UNITS_DELETE', 'display_name' => 'حذف الوحدة', 'group_name' => 'المستودع', 'description' => 'حذف وحدات القياس'],

            // Production Stages
            ['name' => 'MENU_STAGE1_STANDS', 'display_name' => 'المرحلة الأولى - الاستاندات', 'group_name' => 'الإنتاج', 'description' => 'إظهار قائمة المرحلة الأولى'],
            ['name' => 'MENU_STAGE2_PROCESSING', 'display_name' => 'المرحلة الثانية - المعالجة', 'group_name' => 'الإنتاج', 'description' => 'إظهار قائمة المرحلة الثانية'],
            ['name' => 'MENU_STAGE3_COILS', 'display_name' => 'المرحلة الثالثة - اللفائف', 'group_name' => 'الإنتاج', 'description' => 'إظهار قائمة المرحلة الثالثة'],
            ['name' => 'MENU_STAGE4_PACKAGING', 'display_name' => 'المرحلة الرابعة - التعبئة', 'group_name' => 'الإنتاج', 'description' => 'إظهار قائمة المرحلة الرابعة'],

            // Production Details
            ['name' => 'STAGE1_STANDS_READ', 'display_name' => 'عرض الاستاندات', 'group_name' => 'الإنتاج', 'description' => 'عرض الاستاندات'],
            ['name' => 'STAGE1_STANDS_CREATE', 'display_name' => 'إضافة استاند', 'group_name' => 'الإنتاج', 'description' => 'إنشاء استاند جديد'],
            ['name' => 'STAGE1_STANDS_UPDATE', 'display_name' => 'تعديل الاستاند', 'group_name' => 'الإنتاج', 'description' => 'تعديل الاستاندات'],
            ['name' => 'STAGE1_STANDS_DELETE', 'display_name' => 'حذف الاستاند', 'group_name' => 'الإنتاج', 'description' => 'حذف الاستاندات'],

            ['name' => 'STAGE2_PROCESSING_READ', 'display_name' => 'عرض المعالجة', 'group_name' => 'الإنتاج', 'description' => 'عرض عمليات المعالجة'],
            ['name' => 'STAGE2_PROCESSING_CREATE', 'display_name' => 'إضافة معالجة', 'group_name' => 'الإنتاج', 'description' => 'إنشاء عملية معالجة جديدة'],
            ['name' => 'STAGE2_PROCESSING_UPDATE', 'display_name' => 'تعديل المعالجة', 'group_name' => 'الإنتاج', 'description' => 'تعديل عمليات المعالجة'],
            ['name' => 'STAGE2_PROCESSING_DELETE', 'display_name' => 'حذف المعالجة', 'group_name' => 'الإنتاج', 'description' => 'حذف عمليات المعالجة'],

            ['name' => 'STAGE3_COILS_READ', 'display_name' => 'عرض اللفائف', 'group_name' => 'الإنتاج', 'description' => 'عرض اللفائف'],
            ['name' => 'STAGE3_COILS_CREATE', 'display_name' => 'إضافة لفافة', 'group_name' => 'الإنتاج', 'description' => 'إنشاء لفافة جديدة'],
            ['name' => 'STAGE3_COILS_UPDATE', 'display_name' => 'تعديل اللفافة', 'group_name' => 'الإنتاج', 'description' => 'تعديل اللفائف'],
            ['name' => 'STAGE3_COILS_DELETE', 'display_name' => 'حذف اللفافة', 'group_name' => 'الإنتاج', 'description' => 'حذف اللفائف'],

            ['name' => 'STAGE4_PACKAGING_READ', 'display_name' => 'عرض التعبئة', 'group_name' => 'الإنتاج', 'description' => 'عرض عمليات التعبئة'],
            ['name' => 'STAGE4_PACKAGING_CREATE', 'display_name' => 'إضافة تعبئة', 'group_name' => 'الإنتاج', 'description' => 'إنشاء عملية تعبئة جديدة'],
            ['name' => 'STAGE4_PACKAGING_UPDATE', 'display_name' => 'تعديل التعبئة', 'group_name' => 'الإنتاج', 'description' => 'تعديل عمليات التعبئة'],
            ['name' => 'STAGE4_PACKAGING_DELETE', 'display_name' => 'حذف التعبئة', 'group_name' => 'الإنتاج', 'description' => 'حذف عمليات التعبئة'],

            // Production & Quality
            ['name' => 'MENU_PRODUCTION_TRACKING', 'display_name' => 'تتبع الإنتاج', 'group_name' => 'الإنتاج', 'description' => 'إظهار قائمة تتبع الإنتاج'],
            ['name' => 'MENU_SHIFTS_WORKERS', 'display_name' => 'الورديات والعمال', 'group_name' => 'الإنتاج', 'description' => 'إظهار قائمة الورديات والعمال'],
            ['name' => 'MENU_QUALITY_WASTE', 'display_name' => 'الجودة والهدر', 'group_name' => 'الإنتاج', 'description' => 'إظهار قائمة الجودة والهدر'],
            ['name' => 'MENU_PRODUCTION_REPORTS', 'display_name' => 'التقارير الإنتاجية', 'group_name' => 'الإنتاج', 'description' => 'إظهار قائمة التقارير الإنتاجية'],

            // Management & Settings
            ['name' => 'MENU_MANAGEMENT', 'display_name' => 'الإدارة', 'group_name' => 'الإدارة والإعدادات', 'description' => 'إظهار قائمة الإدارة'],
            ['name' => 'MENU_MANAGE_USERS', 'display_name' => 'إدارة المستخدمين', 'group_name' => 'الإدارة والإعدادات', 'description' => 'إظهار قائمة إدارة المستخدمين'],
            ['name' => 'MENU_MANAGE_ROLES', 'display_name' => 'إدارة الأدوار', 'group_name' => 'الإدارة والإعدادات', 'description' => 'إظهار قائمة إدارة الأدوار'],
            ['name' => 'MENU_MANAGE_PERMISSIONS', 'display_name' => 'إدارة الصلاحيات', 'group_name' => 'الإدارة والإعدادات', 'description' => 'إظهار قائمة إدارة الصلاحيات'],
            ['name' => 'MENU_SETTINGS', 'display_name' => 'الإعدادات', 'group_name' => 'الإدارة والإعدادات', 'description' => 'إظهار قائمة الإعدادات'],

            // Management operations
            ['name' => 'MANAGE_USERS_READ', 'display_name' => 'عرض المستخدمين', 'group_name' => 'الإدارة والإعدادات', 'description' => 'عرض المستخدمين'],
            ['name' => 'MANAGE_USERS_CREATE', 'display_name' => 'إضافة مستخدم', 'group_name' => 'الإدارة والإعدادات', 'description' => 'إضافة مستخدم جديد'],
            ['name' => 'MANAGE_USERS_UPDATE', 'display_name' => 'تعديل المستخدم', 'group_name' => 'الإدارة والإعدادات', 'description' => 'تعديل بيانات المستخدم'],
            ['name' => 'MANAGE_USERS_DELETE', 'display_name' => 'حذف المستخدم', 'group_name' => 'الإدارة والإعدادات', 'description' => 'حذف المستخدم'],

            // Old permissions (backward compatibility)
            ['name' => 'MANAGE_USERS', 'display_name' => 'إدارة المستخدمين', 'group_name' => 'الإدارة والإعدادات', 'description' => 'إدارة المستخدمين والموظفين'],
            ['name' => 'MANAGE_ROLES', 'display_name' => 'إدارة الأدوار', 'group_name' => 'الإدارة والإعدادات', 'description' => 'إدارة أدوار المستخدمين'],
            ['name' => 'MANAGE_PERMISSIONS', 'display_name' => 'إدارة الصلاحيات', 'group_name' => 'الإدارة والإعدادات', 'description' => 'إدارة صلاحيات النظام'],
            ['name' => 'MANAGE_MATERIALS', 'display_name' => 'إدارة المواد الخام', 'group_name' => 'المستودع', 'description' => 'إدارة المواد الخام والمخزون'],
            ['name' => 'MANAGE_SUPPLIERS', 'display_name' => 'إدارة الموردين', 'group_name' => 'المستودع', 'description' => 'إدارة الموردين والموزعين'],
            ['name' => 'MANAGE_WAREHOUSES', 'display_name' => 'إدارة المخازن', 'group_name' => 'المستودع', 'description' => 'إدارة المخازن والمواقع'],
            ['name' => 'WAREHOUSE_TRANSFERS', 'display_name' => 'تحويلات المخازن', 'group_name' => 'المستودع', 'description' => 'إدارة تحويلات المخازن'],
            ['name' => 'STAGE1_STANDS', 'display_name' => 'المرحلة الأولى - الاستاندات', 'group_name' => 'الإنتاج', 'description' => 'إدارة المرحلة الأولى من الإنتاج'],
            ['name' => 'STAGE2_PROCESSING', 'display_name' => 'المرحلة الثانية - المعالجة', 'group_name' => 'الإنتاج', 'description' => 'إدارة المرحلة الثانية من الإنتاج'],
            ['name' => 'STAGE3_COILS', 'display_name' => 'المرحلة الثالثة - اللفائف', 'group_name' => 'الإنتاج', 'description' => 'إدارة المرحلة الثالثة من الإنتاج'],
            ['name' => 'STAGE4_PACKAGING', 'display_name' => 'المرحلة الرابعة - التعبئة', 'group_name' => 'الإنتاج', 'description' => 'إدارة المرحلة الرابعة من الإنتاج'],
            ['name' => 'PURCHASE_INVOICES', 'display_name' => 'فواتير الشراء', 'group_name' => 'المستودع', 'description' => 'إدارة فواتير الشراء'],
            ['name' => 'SALES_INVOICES', 'display_name' => 'فواتير المبيعات', 'group_name' => 'المستودع', 'description' => 'إدارة فواتير المبيعات'],
            ['name' => 'MANAGE_MOVEMENTS', 'display_name' => 'إدارة الحركات', 'group_name' => 'المستودع', 'description' => 'إدارة حركات المخزون'],

            // Reports
            ['name' => 'VIEW_REPORTS', 'display_name' => 'التقارير والإحصائيات', 'group_name' => 'التقارير', 'description' => 'عرض التقارير والإحصائيات'],
            ['name' => 'PRODUCTION_REPORTS', 'display_name' => 'تقارير الإنتاج', 'group_name' => 'التقارير', 'description' => 'تقارير الإنتاج التفصيلية'],
            ['name' => 'INVENTORY_REPORTS', 'display_name' => 'تقارير المخزون', 'group_name' => 'التقارير', 'description' => 'تقارير المخزون والحركات'],

            // Dashboard
            ['name' => 'VIEW_DASHBOARD', 'display_name' => 'لوحة التحكم', 'group_name' => 'لوحة التحكم', 'description' => 'عرض لوحة التحكم الرئيسية'],
        ];

        foreach ($permissions as &$permission) {
            $permission['created_at'] = now();
            $permission['updated_at'] = now();
        }

        DB::table('permissions')->insert($permissions);
    }
}
