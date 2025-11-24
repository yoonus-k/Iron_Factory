<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class UpdatePermissionsStructureSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $this->command->info('🔄 جاري تحديث هيكل الصلاحيات...');

            // صلاحيات جديدة بدون CRUD كامل
            $newPermissions = [
                // لوحة التحكم - عرض فقط
                [
                    'permission_name' => 'لوحة التحكم الرئيسية',
                    'permission_name_en' => 'Main Dashboard',
                    'permission_code' => 'VIEW_MAIN_DASHBOARD',
                    'module' => 'Dashboard',
                    'description' => 'عرض لوحة التحكم الرئيسية والإحصائيات',
                ],
                
                // التقارير - عرض وتصدير فقط
                [
                    'permission_name' => 'تقارير الإنتاج اليومية',
                    'permission_name_en' => 'Daily Production Reports',
                    'permission_code' => 'VIEW_DAILY_REPORTS',
                    'module' => 'Reports',
                    'description' => 'عرض تقارير الإنتاج اليومية',
                ],
                [
                    'permission_name' => 'تقارير الهدر',
                    'permission_name_en' => 'Waste Reports',
                    'permission_code' => 'VIEW_WASTE_REPORTS',
                    'module' => 'Reports',
                    'description' => 'عرض تقارير الهدر والخسائر',
                ],
                [
                    'permission_name' => 'تقارير الورديات',
                    'permission_name_en' => 'Shift Reports',
                    'permission_code' => 'VIEW_SHIFT_REPORTS',
                    'module' => 'Reports',
                    'description' => 'عرض تقارير الورديات والعمال',
                ],
                
                // الإشعارات - عرض وقراءة فقط
                [
                    'permission_name' => 'الإشعارات',
                    'permission_name_en' => 'Notifications',
                    'permission_code' => 'VIEW_NOTIFICATIONS',
                    'module' => 'General',
                    'description' => 'عرض وإدارة الإشعارات',
                ],
                
                // سجل النشاطات - عرض فقط
                [
                    'permission_name' => 'سجل النشاطات',
                    'permission_name_en' => 'Activity Log',
                    'permission_code' => 'VIEW_ACTIVITY_LOG',
                    'module' => 'General',
                    'description' => 'عرض سجل نشاطات المستخدمين',
                ],
                
                // طباعة الباركود - إجراء فقط
                [
                    'permission_name' => 'طباعة الباركود',
                    'permission_name_en' => 'Print Barcode',
                    'permission_code' => 'PRINT_BARCODE',
                    'module' => 'General',
                    'description' => 'طباعة باركود المنتجات',
                ],
                
                // إعدادات الباركود - تعديل فقط
                [
                    'permission_name' => 'إعدادات الباركود',
                    'permission_name_en' => 'Barcode Settings',
                    'permission_code' => 'MANAGE_BARCODE_SETTINGS',
                    'module' => 'Settings',
                    'description' => 'تعديل إعدادات الباركود',
                ],
                
                // إعدادات النظام - تعديل فقط
                [
                    'permission_name' => 'إعدادات النظام العامة',
                    'permission_name_en' => 'General System Settings',
                    'permission_code' => 'MANAGE_SYSTEM_SETTINGS',
                    'module' => 'Settings',
                    'description' => 'تعديل إعدادات النظام العامة',
                ],
                
                // النسخ الاحتياطي
                [
                    'permission_name' => 'النسخ الاحتياطي',
                    'permission_name_en' => 'Database Backup',
                    'permission_code' => 'MANAGE_BACKUP',
                    'module' => 'System',
                    'description' => 'إنشاء واستعادة النسخ الاحتياطية',
                ],
            ];

            foreach ($newPermissions as $permission) {
                Permission::updateOrCreate(
                    ['permission_code' => $permission['permission_code']],
                    array_merge($permission, [
                        'is_system' => true,
                        'is_active' => true,
                        'created_by' => 1,
                    ])
                );
                $this->command->info("✅ {$permission['permission_name']}");
            }

            // ربط الصلاحيات الجديدة بالأدوار
            $this->assignToRoles();

            DB::commit();
            $this->command->info('✅ تم تحديث هيكل الصلاحيات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ خطأ: ' . $e->getMessage());
        }
    }

    private function assignToRoles()
    {
        $admin = Role::where('role_code', 'ADMIN')->first();
        $manager = Role::where('role_code', 'MANAGER')->first();
        $supervisor = Role::where('role_code', 'SUPERVISOR')->first();
        $accountant = Role::where('role_code', 'ACCOUNTANT')->first();
        $worker = Role::where('role_code', 'WORKER')->first();

        // Admin - كل الصلاحيات الجديدة
        if ($admin) {
            $permissions = Permission::whereIn('permission_code', [
                'VIEW_MAIN_DASHBOARD', 'VIEW_DAILY_REPORTS', 'VIEW_WASTE_REPORTS',
                'VIEW_SHIFT_REPORTS', 'VIEW_NOTIFICATIONS', 'VIEW_ACTIVITY_LOG',
                'PRINT_BARCODE', 'MANAGE_BARCODE_SETTINGS', 'MANAGE_SYSTEM_SETTINGS',
                'MANAGE_BACKUP',
            ])->get();

            foreach ($permissions as $permission) {
                $admin->permissions()->syncWithoutDetaching([
                    $permission->id => [
                        'can_create' => true,
                        'can_read' => true,
                        'can_update' => true,
                        'can_delete' => true,
                        'can_approve' => true,
                        'can_export' => true,
                    ]
                ]);
            }
        }

        // Manager - معظم الصلاحيات
        if ($manager) {
            $permissions = Permission::whereIn('permission_code', [
                'VIEW_MAIN_DASHBOARD', 'VIEW_DAILY_REPORTS', 'VIEW_WASTE_REPORTS',
                'VIEW_SHIFT_REPORTS', 'VIEW_NOTIFICATIONS', 'VIEW_ACTIVITY_LOG',
                'PRINT_BARCODE',
            ])->get();

            foreach ($permissions as $permission) {
                $manager->permissions()->syncWithoutDetaching([
                    $permission->id => [
                        'can_create' => false,
                        'can_read' => true,
                        'can_update' => true,
                        'can_delete' => false,
                        'can_approve' => false,
                        'can_export' => true,
                    ]
                ]);
            }
        }

        // Supervisor - التقارير والإشعارات
        if ($supervisor) {
            $permissions = Permission::whereIn('permission_code', [
                'VIEW_MAIN_DASHBOARD', 'VIEW_DAILY_REPORTS', 'VIEW_WASTE_REPORTS',
                'VIEW_SHIFT_REPORTS', 'VIEW_NOTIFICATIONS', 'PRINT_BARCODE',
            ])->get();

            foreach ($permissions as $permission) {
                $supervisor->permissions()->syncWithoutDetaching([
                    $permission->id => [
                        'can_create' => false,
                        'can_read' => true,
                        'can_update' => false,
                        'can_delete' => false,
                        'can_approve' => false,
                        'can_export' => false,
                    ]
                ]);
            }
        }

        // Worker - لوحة التحكم والطباعة فقط
        if ($worker) {
            $permissions = Permission::whereIn('permission_code', [
                'VIEW_MAIN_DASHBOARD', 'PRINT_BARCODE',
            ])->get();

            foreach ($permissions as $permission) {
                $worker->permissions()->syncWithoutDetaching([
                    $permission->id => [
                        'can_create' => false,
                        'can_read' => true,
                        'can_update' => false,
                        'can_delete' => false,
                        'can_approve' => false,
                        'can_export' => false,
                    ]
                ]);
            }
        }
    }
}
