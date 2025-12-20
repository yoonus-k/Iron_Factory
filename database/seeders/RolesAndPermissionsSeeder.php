<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء الأدوار
        $roles = [
            [
                'role_code' => 'SUPER_ADMIN',
                'role_name' => 'مدير النظام',
                'role_name_en' => 'Super Admin',
                'description' => 'صلاحيات كاملة على النظام',
                'level' => 100,
                'is_system' => true,
            ],
            [
                'role_code' => 'ADMIN',
                'role_name' => 'مدير',
                'role_name_en' => 'Admin',
                'description' => 'صلاحيات إدارية',
                'level' => 90,
                'is_system' => false,
            ],
            [
                'role_code' => 'MANAGER',
                'role_name' => 'مدير عام',
                'role_name_en' => 'Manager',
                'description' => 'إدارة العمليات اليومية',
                'level' => 70,
                'is_system' => false,
            ],
            [
                'role_code' => 'SUPERVISOR',
                'role_name' => 'مشرف',
                'role_name_en' => 'Supervisor',
                'description' => 'إشراف على العمليات',
                'level' => 50,
                'is_system' => false,
            ],
            [
                'role_code' => 'EMPLOYEE',
                'role_name' => 'موظف',
                'role_name_en' => 'Employee',
                'description' => 'موظف عادي',
                'level' => 10,
                'is_system' => false,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['role_code' => $roleData['role_code']],
                $roleData
            );
        }

        // إنشاء الصلاحيات
        $permissions = [
            // Users
            ['name' => 'view_users', 'display_name' => 'عرض المستخدمين', 'group_name' => 'users', 'description' => 'عرض قائمة المستخدمين'],
            ['name' => 'create_users', 'display_name' => 'إنشاء مستخدمين', 'group_name' => 'users', 'description' => 'إضافة مستخدمين جدد'],
            ['name' => 'edit_users', 'display_name' => 'تعديل المستخدمين', 'group_name' => 'users', 'description' => 'تعديل بيانات المستخدمين'],
            ['name' => 'delete_users', 'display_name' => 'حذف المستخدمين', 'group_name' => 'users', 'description' => 'حذف المستخدمين'],
            
            // Materials
            ['name' => 'view_materials', 'display_name' => 'عرض المواد', 'group_name' => 'materials', 'description' => 'عرض قائمة المواد'],
            ['name' => 'create_materials', 'display_name' => 'إنشاء مواد', 'group_name' => 'materials', 'description' => 'إضافة مواد جديدة'],
            ['name' => 'edit_materials', 'display_name' => 'تعديل المواد', 'group_name' => 'materials', 'description' => 'تعديل بيانات المواد'],
            ['name' => 'delete_materials', 'display_name' => 'حذف المواد', 'group_name' => 'materials', 'description' => 'حذف المواد'],
            
            // Delivery Notes
            ['name' => 'view_delivery_notes', 'display_name' => 'عرض إذن الصرف', 'group_name' => 'delivery_notes', 'description' => 'عرض أذونات الصرف'],
            ['name' => 'create_delivery_notes', 'display_name' => 'إنشاء إذن صرف', 'group_name' => 'delivery_notes', 'description' => 'إنشاء أذونات صرف جديدة'],
            ['name' => 'edit_delivery_notes', 'display_name' => 'تعديل إذن الصرف', 'group_name' => 'delivery_notes', 'description' => 'تعديل أذونات الصرف'],
            ['name' => 'delete_delivery_notes', 'display_name' => 'حذف إذن الصرف', 'group_name' => 'delivery_notes', 'description' => 'حذف أذونات الصرف'],
            
            // Production Stages
            ['name' => 'view_stages', 'display_name' => 'عرض المراحل', 'group_name' => 'production', 'description' => 'عرض مراحل الإنتاج'],
            ['name' => 'manage_stages', 'display_name' => 'إدارة المراحل', 'group_name' => 'production', 'description' => 'إدارة مراحل الإنتاج'],
            
            // Reports
            ['name' => 'view_reports', 'display_name' => 'عرض التقارير', 'group_name' => 'reports', 'description' => 'عرض التقارير'],
            ['name' => 'export_reports', 'display_name' => 'تصدير التقارير', 'group_name' => 'reports', 'description' => 'تصدير التقارير'],
            
            // Settings
            ['name' => 'manage_settings', 'display_name' => 'إدارة الإعدادات', 'group_name' => 'settings', 'description' => 'إدارة إعدادات النظام'],
            
            // Sync
            ['name' => 'view_sync_dashboard', 'display_name' => 'عرض لوحة المزامنة', 'group_name' => 'sync', 'description' => 'عرض لوحة المزامنة'],
            ['name' => 'manage_sync', 'display_name' => 'إدارة المزامنة', 'group_name' => 'sync', 'description' => 'إدارة عمليات المزامنة'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );
        }

        // ربط الصلاحيات بالأدوار
        $superAdmin = Role::where('role_code', 'SUPER_ADMIN')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->sync(Permission::all()->pluck('id'));
        }

        $this->command->info('✅ تم إنشاء الأدوار والصلاحيات بنجاح');
    }
}
