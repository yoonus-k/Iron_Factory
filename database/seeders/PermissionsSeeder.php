<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // إنشاء الصلاحيات الأساسية
            $permissions = [
                // إدارة المستخدمين
                [
                    'permission_name' => 'إدارة المستخدمين',
                    'permission_name_en' => 'Manage Users',
                    'permission_code' => 'MANAGE_USERS',
                    'module' => 'Users',
                    'description' => 'إدارة المستخدمين والموظفين',
                ],
                
                // إدارة الأدوار والصلاحيات
                [
                    'permission_name' => 'إدارة الأدوار',
                    'permission_name_en' => 'Manage Roles',
                    'permission_code' => 'MANAGE_ROLES',
                    'module' => 'Roles',
                    'description' => 'إدارة أدوار المستخدمين',
                ],
                [
                    'permission_name' => 'إدارة الصلاحيات',
                    'permission_name_en' => 'Manage Permissions',
                    'permission_code' => 'MANAGE_PERMISSIONS',
                    'module' => 'Permissions',
                    'description' => 'إدارة صلاحيات النظام',
                ],

                // إدارة المواد الخام
                [
                    'permission_name' => 'إدارة المواد الخام',
                    'permission_name_en' => 'Manage Materials',
                    'permission_code' => 'MANAGE_MATERIALS',
                    'module' => 'Materials',
                    'description' => 'إدارة المواد الخام والمخزون',
                ],

                // إدارة الموردين
                [
                    'permission_name' => 'إدارة الموردين',
                    'permission_name_en' => 'Manage Suppliers',
                    'permission_code' => 'MANAGE_SUPPLIERS',
                    'module' => 'Suppliers',
                    'description' => 'إدارة الموردين والموزعين',
                ],

                // إدارة المخازن
                [
                    'permission_name' => 'إدارة المخازن',
                    'permission_name_en' => 'Manage Warehouses',
                    'permission_code' => 'MANAGE_WAREHOUSES',
                    'module' => 'Warehouses',
                    'description' => 'إدارة المخازن والمواقع',
                ],
                [
                    'permission_name' => 'تحويلات المخازن',
                    'permission_name_en' => 'Warehouse Transfers',
                    'permission_code' => 'WAREHOUSE_TRANSFERS',
                    'module' => 'Warehouses',
                    'description' => 'إدارة تحويلات المخازن',
                ],

                // إدارة الإنتاج
                [
                    'permission_name' => 'المرحلة الأولى - الأستندات',
                    'permission_name_en' => 'Stage 1 - Stands',
                    'permission_code' => 'STAGE1_STANDS',
                    'module' => 'Manufacturing',
                    'description' => 'إدارة المرحلة الأولى من الإنتاج',
                ],
                [
                    'permission_name' => 'المرحلة الثانية - المعالجة',
                    'permission_name_en' => 'Stage 2 - Processing',
                    'permission_code' => 'STAGE2_PROCESSING',
                    'module' => 'Manufacturing',
                    'description' => 'إدارة المرحلة الثانية من الإنتاج',
                ],
                [
                    'permission_name' => 'المرحلة الثالثة - اللفائف',
                    'permission_name_en' => 'Stage 3 - Coils',
                    'permission_code' => 'STAGE3_COILS',
                    'module' => 'Manufacturing',
                    'description' => 'إدارة المرحلة الثالثة من الإنتاج',
                ],
                [
                    'permission_name' => 'المرحلة الرابعة - التعبئة',
                    'permission_name_en' => 'Stage 4 - Packaging',
                    'permission_code' => 'STAGE4_PACKAGING',
                    'module' => 'Manufacturing',
                    'description' => 'إدارة المرحلة الرابعة من الإنتاج',
                ],

                // إدارة الفواتير
                [
                    'permission_name' => 'فواتير الشراء',
                    'permission_name_en' => 'Purchase Invoices',
                    'permission_code' => 'PURCHASE_INVOICES',
                    'module' => 'Invoices',
                    'description' => 'إدارة فواتير الشراء',
                ],
                [
                    'permission_name' => 'فواتير المبيعات',
                    'permission_name_en' => 'Sales Invoices',
                    'permission_code' => 'SALES_INVOICES',
                    'module' => 'Invoices',
                    'description' => 'إدارة فواتير المبيعات',
                ],

                // إدارة الحركات
                [
                    'permission_name' => 'إدارة الحركات',
                    'permission_name_en' => 'Manage Movements',
                    'permission_code' => 'MANAGE_MOVEMENTS',
                    'module' => 'Movements',
                    'description' => 'إدارة حركات المخزون',
                ],

                // التقارير
                [
                    'permission_name' => 'التقارير',
                    'permission_name_en' => 'Reports',
                    'permission_code' => 'VIEW_REPORTS',
                    'module' => 'Reports',
                    'description' => 'عرض التقارير والإحصائيات',
                ],
                [
                    'permission_name' => 'تقارير الإنتاج',
                    'permission_name_en' => 'Production Reports',
                    'permission_code' => 'PRODUCTION_REPORTS',
                    'module' => 'Reports',
                    'description' => 'تقارير الإنتاج التفصيلية',
                ],
                [
                    'permission_name' => 'تقارير المخزون',
                    'permission_name_en' => 'Inventory Reports',
                    'permission_code' => 'INVENTORY_REPORTS',
                    'module' => 'Reports',
                    'description' => 'تقارير المخزون والحركات',
                ],

                // لوحة التحكم
                [
                    'permission_name' => 'لوحة التحكم',
                    'permission_name_en' => 'Dashboard',
                    'permission_code' => 'VIEW_DASHBOARD',
                    'module' => 'Dashboard',
                    'description' => 'عرض لوحة التحكم الرئيسية',
                ],
            ];

            foreach ($permissions as $permission) {
                Permission::firstOrCreate(
                    ['permission_code' => $permission['permission_code']],
                    array_merge($permission, [
                        'is_system' => true,
                        'is_active' => true,
                        'created_by' => 1,
                    ])
                );
            }

            // ربط الصلاحيات بالأدوار
            $this->assignPermissionsToRoles();

            DB::commit();
            $this->command->info('✅ تم إنشاء الصلاحيات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ خطأ: ' . $e->getMessage());
        }
    }

    private function assignPermissionsToRoles()
    {
        $admin = Role::where('role_code', 'ADMIN')->first();
        $manager = Role::where('role_code', 'MANAGER')->first();
        $supervisor = Role::where('role_code', 'SUPERVISOR')->first();
        $accountant = Role::where('role_code', 'ACCOUNTANT')->first();
        $warehouseKeeper = Role::where('role_code', 'WAREHOUSE_KEEPER')->first();
        $worker = Role::where('role_code', 'WORKER')->first();

        // Admin - كل الصلاحيات
        if ($admin) {
            $allPermissions = Permission::all();
            foreach ($allPermissions as $permission) {
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
            $managerPermissions = Permission::whereIn('permission_code', [
                'MANAGE_USERS', 'MANAGE_MATERIALS', 'MANAGE_SUPPLIERS', 'MANAGE_WAREHOUSES',
                'WAREHOUSE_TRANSFERS', 'STAGE1_STANDS', 'STAGE2_PROCESSING', 'STAGE3_COILS',
                'STAGE4_PACKAGING', 'PURCHASE_INVOICES', 'SALES_INVOICES', 'MANAGE_MOVEMENTS',
                'VIEW_REPORTS', 'PRODUCTION_REPORTS', 'INVENTORY_REPORTS', 'VIEW_DASHBOARD'
            ])->get();
            
            foreach ($managerPermissions as $permission) {
                $manager->permissions()->syncWithoutDetaching([
                    $permission->id => [
                        'can_create' => true,
                        'can_read' => true,
                        'can_update' => true,
                        'can_delete' => false,
                        'can_approve' => true,
                        'can_export' => true,
                    ]
                ]);
            }
        }

        // Supervisor - صلاحيات الإشراف
        if ($supervisor) {
            $supervisorPermissions = Permission::whereIn('permission_code', [
                'STAGE1_STANDS', 'STAGE2_PROCESSING', 'STAGE3_COILS', 'STAGE4_PACKAGING',
                'MANAGE_MOVEMENTS', 'VIEW_REPORTS', 'PRODUCTION_REPORTS', 'VIEW_DASHBOARD'
            ])->get();
            
            foreach ($supervisorPermissions as $permission) {
                $supervisor->permissions()->syncWithoutDetaching([
                    $permission->id => [
                        'can_create' => true,
                        'can_read' => true,
                        'can_update' => true,
                        'can_delete' => false,
                        'can_approve' => false,
                        'can_export' => false,
                    ]
                ]);
            }
        }

        // Accountant - صلاحيات المحاسبة
        if ($accountant) {
            $accountantPermissions = Permission::whereIn('permission_code', [
                'PURCHASE_INVOICES', 'SALES_INVOICES', 'VIEW_REPORTS', 'INVENTORY_REPORTS', 'VIEW_DASHBOARD'
            ])->get();
            
            foreach ($accountantPermissions as $permission) {
                $accountant->permissions()->syncWithoutDetaching([
                    $permission->id => [
                        'can_create' => true,
                        'can_read' => true,
                        'can_update' => true,
                        'can_delete' => false,
                        'can_approve' => true,
                        'can_export' => true,
                    ]
                ]);
            }
        }

        // Warehouse Keeper - صلاحيات المخازن
        if ($warehouseKeeper) {
            $warehousePermissions = Permission::whereIn('permission_code', [
                'MANAGE_WAREHOUSES', 'WAREHOUSE_TRANSFERS', 'MANAGE_MOVEMENTS', 
                'VIEW_REPORTS', 'INVENTORY_REPORTS', 'VIEW_DASHBOARD'
            ])->get();
            
            foreach ($warehousePermissions as $permission) {
                $warehouseKeeper->permissions()->syncWithoutDetaching([
                    $permission->id => [
                        'can_create' => true,
                        'can_read' => true,
                        'can_update' => true,
                        'can_delete' => false,
                        'can_approve' => false,
                        'can_export' => false,
                    ]
                ]);
            }
        }

        // Worker - صلاحيات محدودة
        if ($worker) {
            $workerPermissions = Permission::whereIn('permission_code', [
                'STAGE1_STANDS', 'STAGE2_PROCESSING', 'STAGE3_COILS', 'STAGE4_PACKAGING', 'VIEW_DASHBOARD'
            ])->get();
            
            foreach ($workerPermissions as $permission) {
                $worker->permissions()->syncWithoutDetaching([
                    $permission->id => [
                        'can_create' => true,
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
