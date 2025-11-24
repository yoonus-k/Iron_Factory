<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class FixSidebarPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $this->command->info('🔄 جاري إضافة صلاحيات الـ Sidebar...');

            // الصلاحيات المطلوبة للـ Sidebar
            $sidebarPermissions = [
                [
                    'permission_name' => 'لوحة التحكم الرئيسية',
                    'permission_name_en' => 'Main Dashboard',
                    'permission_code' => 'VIEW_MAIN_DASHBOARD',
                    'module' => 'Dashboard',
                    'description' => 'عرض لوحة التحكم الرئيسية',
                    'is_system' => true,
                    'is_active' => true,
                ],
                [
                    'permission_name' => 'إدارة المخازن',
                    'permission_name_en' => 'Manage Warehouses',
                    'permission_code' => 'MANAGE_WAREHOUSES',
                    'module' => 'Manufacturing',
                    'description' => 'إدارة المخازن',
                    'is_system' => true,
                    'is_active' => true,
                ],
                [
                    'permission_name' => 'المرحلة الأولى - الأستندات',
                    'permission_name_en' => 'Stage 1 - Stands',
                    'permission_code' => 'STAGE1_STANDS',
                    'module' => 'Manufacturing',
                    'description' => 'إدارة المرحلة الأولى',
                    'is_system' => true,
                    'is_active' => true,
                ],
                [
                    'permission_name' => 'المرحلة الثانية - المعالجة',
                    'permission_name_en' => 'Stage 2 - Processing',
                    'permission_code' => 'STAGE2_PROCESSING',
                    'module' => 'Manufacturing',
                    'description' => 'إدارة المرحلة الثانية',
                    'is_system' => true,
                    'is_active' => true,
                ],
                [
                    'permission_name' => 'المرحلة الثالثة - الفافات',
                    'permission_name_en' => 'Stage 3 - Coils',
                    'permission_code' => 'STAGE3_COILS',
                    'module' => 'Manufacturing',
                    'description' => 'إدارة المرحلة الثالثة',
                    'is_system' => true,
                    'is_active' => true,
                ],
                [
                    'permission_name' => 'المرحلة الرابعة - التعبئة',
                    'permission_name_en' => 'Stage 4 - Packaging',
                    'permission_code' => 'STAGE4_PACKAGING',
                    'module' => 'Manufacturing',
                    'description' => 'إدارة المرحلة الرابعة',
                    'is_system' => true,
                    'is_active' => true,
                ],
                [
                    'permission_name' => 'إدارة الحركات والتتبع',
                    'permission_name_en' => 'Manage Movements',
                    'permission_code' => 'MANAGE_MOVEMENTS',
                    'module' => 'Manufacturing',
                    'description' => 'إدارة حركات المخزون والتتبع',
                    'is_system' => true,
                    'is_active' => true,
                ],
                [
                    'permission_name' => 'عرض التكاليف والهدر',
                    'permission_name_en' => 'View Costs',
                    'permission_code' => 'VIEW_COSTS',
                    'module' => 'Manufacturing',
                    'description' => 'عرض التكاليف والهدر',
                    'is_system' => true,
                    'is_active' => true,
                ],
                [
                    'permission_name' => 'عرض التقارير',
                    'permission_name_en' => 'View Reports',
                    'permission_code' => 'VIEW_REPORTS',
                    'module' => 'Reports',
                    'description' => 'عرض التقارير والإحصائيات',
                    'is_system' => true,
                    'is_active' => true,
                ],
                [
                    'permission_name' => 'إدارة المستخدمين',
                    'permission_name_en' => 'Manage Users',
                    'permission_code' => 'MANAGE_USERS',
                    'module' => 'Users',
                    'description' => 'إدارة المستخدمين',
                    'is_system' => true,
                    'is_active' => true,
                ],
                [
                    'permission_name' => 'إدارة الأدوار',
                    'permission_name_en' => 'Manage Roles',
                    'permission_code' => 'MANAGE_ROLES',
                    'module' => 'Users',
                    'description' => 'إدارة الأدوار',
                    'is_system' => true,
                    'is_active' => true,
                ],
                [
                    'permission_name' => 'إدارة الصلاحيات',
                    'permission_name_en' => 'Manage Permissions',
                    'permission_code' => 'MANAGE_PERMISSIONS',
                    'module' => 'Users',
                    'description' => 'إدارة الصلاحيات',
                    'is_system' => true,
                    'is_active' => true,
                ],
            ];

            // إضافة أو تحديث الصلاحيات
            foreach ($sidebarPermissions as $permission) {
                Permission::updateOrCreate(
                    ['permission_code' => $permission['permission_code']],
                    $permission
                );
                $this->command->info("✅ {$permission['permission_name']}");
            }

            // ربط الصلاحيات بالأدوار
            $this->assignPermissionsToRoles();

            DB::commit();
            $this->command->info('✅ تم إضافة صلاحيات الـ Sidebar بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ خطأ: ' . $e->getMessage());
        }
    }

    private function assignPermissionsToRoles()
    {
        $this->command->info('🔄 جاري ربط الصلاحيات بالأدوار...');

        $admin = Role::where('role_code', 'ADMIN')->first();
        $manager = Role::where('role_code', 'MANAGER')->first();
        $supervisor = Role::where('role_code', 'SUPERVISOR')->first();
        $worker = Role::where('role_code', 'WORKER')->first();

        // ============================================
        // ADMIN - جميع الصلاحيات
        // ============================================
        if ($admin) {
            $this->command->info('⚙️  ربط صلاحيات Admin...');
            $allPermissions = Permission::whereIn('permission_code', [
                'VIEW_MAIN_DASHBOARD',
                'MANAGE_WAREHOUSES',
                'STAGE1_STANDS',
                'STAGE2_PROCESSING',
                'STAGE3_COILS',
                'STAGE4_PACKAGING',
                'MANAGE_MOVEMENTS',
                'VIEW_COSTS',
                'VIEW_REPORTS',
                'MANAGE_USERS',
                'MANAGE_ROLES',
                'MANAGE_PERMISSIONS',
            ])->get();

            $admin->permissions()->detach();
            foreach ($allPermissions as $permission) {
                $admin->permissions()->attach($permission->id, [
                    'can_create' => true,
                    'can_read' => true,
                    'can_update' => true,
                    'can_delete' => true,
                    'can_approve' => true,
                    'can_export' => true,
                ]);
            }
            $this->command->info("   ✅ تم ربط " . $allPermissions->count() . " صلاحية للـ Admin");
        }

        // ============================================
        // MANAGER - معظم الصلاحيات ما عدا الإدارة الكاملة
        // ============================================
        if ($manager) {
            $this->command->info('⚙️  ربط صلاحيات Manager...');
            $managerPermissions = Permission::whereIn('permission_code', [
                'VIEW_MAIN_DASHBOARD',
                'MANAGE_WAREHOUSES',
                'STAGE1_STANDS',
                'STAGE2_PROCESSING',
                'STAGE3_COILS',
                'STAGE4_PACKAGING',
                'MANAGE_MOVEMENTS',
                'VIEW_COSTS',
                'VIEW_REPORTS',
                'MANAGE_USERS',
            ])->get();

            $manager->permissions()->detach();
            foreach ($managerPermissions as $permission) {
                $manager->permissions()->attach($permission->id, [
                    'can_create' => true,
                    'can_read' => true,
                    'can_update' => true,
                    'can_delete' => false,
                    'can_approve' => true,
                    'can_export' => true,
                ]);
            }
            $this->command->info("   ✅ تم ربط " . $managerPermissions->count() . " صلاحية للـ Manager");
        }

        // ============================================
        // SUPERVISOR - صلاحيات الإشراف
        // ============================================
        if ($supervisor) {
            $this->command->info('⚙️  ربط صلاحيات Supervisor...');
            $supervisorPermissions = Permission::whereIn('permission_code', [
                'VIEW_MAIN_DASHBOARD',
                'STAGE1_STANDS',
                'STAGE2_PROCESSING',
                'STAGE3_COILS',
                'STAGE4_PACKAGING',
                'MANAGE_MOVEMENTS',
                'VIEW_COSTS',
                'VIEW_REPORTS',
            ])->get();

            $supervisor->permissions()->detach();
            foreach ($supervisorPermissions as $permission) {
                $supervisor->permissions()->attach($permission->id, [
                    'can_create' => true,
                    'can_read' => true,
                    'can_update' => true,
                    'can_delete' => false,
                    'can_approve' => false,
                    'can_export' => false,
                ]);
            }
            $this->command->info("   ✅ تم ربط " . $supervisorPermissions->count() . " صلاحية للـ Supervisor");
        }

        // ============================================
        // WORKER - صلاحيات محدودة
        // ============================================
        if ($worker) {
            $this->command->info('⚙️  ربط صلاحيات Worker...');
            $workerPermissions = Permission::whereIn('permission_code', [
                'VIEW_MAIN_DASHBOARD',
                'STAGE1_STANDS',
                'STAGE2_PROCESSING',
                'STAGE3_COILS',
                'STAGE4_PACKAGING',
            ])->get();

            $worker->permissions()->detach();
            foreach ($workerPermissions as $permission) {
                $worker->permissions()->attach($permission->id, [
                    'can_create' => true,
                    'can_read' => true,
                    'can_update' => false,
                    'can_delete' => false,
                    'can_approve' => false,
                    'can_export' => false,
                ]);
            }
            $this->command->info("   ✅ تم ربط " . $workerPermissions->count() . " صلاحية للـ Worker");
        }
    }
}
