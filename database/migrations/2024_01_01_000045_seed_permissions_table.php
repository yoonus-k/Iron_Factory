<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إدراج الصلاحيات الأساسية
        $permissions = [
            // صلاحيات المستخدمين
            ['permission_name' => 'إدارة المستخدمين', 'permission_name_en' => 'Manage Users', 'permission_code' => 'users.manage', 'module' => 'users', 'description' => 'إضافة وتعديل وحذف المستخدمين', 'is_system' => true],
            ['permission_name' => 'عرض المستخدمين', 'permission_name_en' => 'View Users', 'permission_code' => 'users.view', 'module' => 'users', 'description' => 'عرض قائمة المستخدمين', 'is_system' => true],

            // صلاحيات الأدوار والصلاحيات
            ['permission_name' => 'إدارة الأدوار', 'permission_name_en' => 'Manage Roles', 'permission_code' => 'roles.manage', 'module' => 'roles', 'description' => 'إضافة وتعديل وحذف الأدوار', 'is_system' => true],
            ['permission_name' => 'تعيين الصلاحيات', 'permission_name_en' => 'Assign Permissions', 'permission_code' => 'permissions.assign', 'module' => 'roles', 'description' => 'تعيين الصلاحيات للأدوار', 'is_system' => true],

            // صلاحيات المستودعات
            ['permission_name' => 'إدارة المستودعات', 'permission_name_en' => 'Manage Warehouses', 'permission_code' => 'warehouses.manage', 'module' => 'warehouses', 'description' => 'إضافة وتعديل المستودعات'],
            ['permission_name' => 'عرض المستودعات', 'permission_name_en' => 'View Warehouses', 'permission_code' => 'warehouses.view', 'module' => 'warehouses', 'description' => 'عرض المستودعات والمخزون'],
            ['permission_name' => 'حركات المستودع', 'permission_name_en' => 'Warehouse Transactions', 'permission_code' => 'warehouses.transactions', 'module' => 'warehouses', 'description' => 'تسجيل حركات المستودع'],

            // صلاحيات المواد
            ['permission_name' => 'إدارة المواد', 'permission_name_en' => 'Manage Materials', 'permission_code' => 'materials.manage', 'module' => 'materials', 'description' => 'إضافة وتعديل المواد الخام'],
            ['permission_name' => 'عرض المواد', 'permission_name_en' => 'View Materials', 'permission_code' => 'materials.view', 'module' => 'materials', 'description' => 'عرض المواد والمخزون'],
            ['permission_name' => 'استلام المواد', 'permission_name_en' => 'Receive Materials', 'permission_code' => 'materials.receive', 'module' => 'materials', 'description' => 'استلام المواد من الموردين'],

            // صلاحيات المراحل الإنتاجية
            ['permission_name' => 'المرحلة الأولى', 'permission_name_en' => 'Stage 1', 'permission_code' => 'production.stage1', 'module' => 'production', 'description' => 'التقسيم والاستاندات'],
            ['permission_name' => 'المرحلة الثانية', 'permission_name_en' => 'Stage 2', 'permission_code' => 'production.stage2', 'module' => 'production', 'description' => 'المعالجة'],
            ['permission_name' => 'المرحلة الثالثة', 'permission_name_en' => 'Stage 3', 'permission_code' => 'production.stage3', 'module' => 'production', 'description' => 'تصنيع الكويلات'],
            ['permission_name' => 'المرحلة الرابعة', 'permission_name_en' => 'Stage 4', 'permission_code' => 'production.stage4', 'module' => 'production', 'description' => 'التعبئة والتغليف'],
            ['permission_name' => 'عرض الإنتاج', 'permission_name_en' => 'View Production', 'permission_code' => 'production.view', 'module' => 'production', 'description' => 'عرض المراحل الإنتاجية'],

            // صلاحيات الهدر
            ['permission_name' => 'تسجيل الهدر', 'permission_name_en' => 'Report Waste', 'permission_code' => 'waste.report', 'module' => 'waste', 'description' => 'تسجيل الهدر في المراحل'],
            ['permission_name' => 'الموافقة على الهدر', 'permission_name_en' => 'Approve Waste', 'permission_code' => 'waste.approve', 'module' => 'waste', 'description' => 'الموافقة على الهدر الاستثنائي'],
            ['permission_name' => 'عرض تقارير الهدر', 'permission_name_en' => 'View Waste Reports', 'permission_code' => 'waste.view', 'module' => 'waste', 'description' => 'عرض تقارير الهدر'],

            // صلاحيات الورديات
            ['permission_name' => 'إدارة الورديات', 'permission_name_en' => 'Manage Shifts', 'permission_code' => 'shifts.manage', 'module' => 'shifts', 'description' => 'تعيين الورديات للموظفين'],
            ['permission_name' => 'تسليم الوردية', 'permission_name_en' => 'Shift Handover', 'permission_code' => 'shifts.handover', 'module' => 'shifts', 'description' => 'تسليم واستلام الوردية'],

            // صلاحيات التقارير
            ['permission_name' => 'عرض التقارير', 'permission_name_en' => 'View Reports', 'permission_code' => 'reports.view', 'module' => 'reports', 'description' => 'عرض جميع التقارير'],
            ['permission_name' => 'تصدير التقارير', 'permission_name_en' => 'Export Reports', 'permission_code' => 'reports.export', 'module' => 'reports', 'description' => 'تصدير التقارير'],
            ['permission_name' => 'التقارير المتقدمة', 'permission_name_en' => 'Advanced Reports', 'permission_code' => 'reports.advanced', 'module' => 'reports', 'description' => 'الوصول للتقارير المتقدمة'],

            // صلاحيات الموردين والفواتير
            ['permission_name' => 'إدارة الموردين', 'permission_name_en' => 'Manage Suppliers', 'permission_code' => 'suppliers.manage', 'module' => 'suppliers', 'description' => 'إضافة وتعديل الموردين'],
            ['permission_name' => 'إدارة الفواتير', 'permission_name_en' => 'Manage Invoices', 'permission_code' => 'invoices.manage', 'module' => 'accounting', 'description' => 'إدارة فواتير المشتريات'],

            // صلاحيات الإعدادات
            ['permission_name' => 'إعدادات النظام', 'permission_name_en' => 'System Settings', 'permission_code' => 'settings.manage', 'module' => 'settings', 'description' => 'تعديل إعدادات النظام', 'is_system' => true],
            ['permission_name' => 'إدارة المعادلات', 'permission_name_en' => 'Manage Formulas', 'permission_code' => 'formulas.manage', 'module' => 'settings', 'description' => 'تعديل المعادلات الحسابية'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(array_merge($permission, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->truncate();
    }
};
