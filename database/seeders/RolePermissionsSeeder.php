<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RolePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $this->command->info('🔄 جاري ربط الصلاحيات بالأدوار...');

            $admin = Role::where('role_code', 'ADMIN')->first();
            $manager = Role::where('role_code', 'MANAGER')->first();
            $supervisor = Role::where('role_code', 'SUPERVISOR')->first();
            $accountant = Role::where('role_code', 'ACCOUNTANT')->first();
            $warehouseKeeper = Role::where('role_code', 'WAREHOUSE_KEEPER')->first();
            $worker = Role::where('role_code', 'WORKER')->first();

            // Admin - كل الصلاحيات
            if ($admin) {
                $this->command->info('⚙️  ربط صلاحيات Admin...');
                $allPermissions = Permission::all();
                $admin->permissions()->detach(); // مسح القديم
                
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
            }

            // Manager - معظم الصلاحيات
            if ($manager) {
                $this->command->info('⚙️  ربط صلاحيات Manager...');
                $manager->permissions()->detach();
                
                $managerPermissions = Permission::whereIn('permission_code', [
                    'MANAGE_USERS', 'MANAGE_MATERIALS', 'MANAGE_SUPPLIERS', 'MANAGE_WAREHOUSES',
                    'WAREHOUSE_TRANSFERS', 'STAGE1_STANDS', 'STAGE2_PROCESSING', 'STAGE3_COILS',
                    'STAGE4_PACKAGING', 'PURCHASE_INVOICES', 'SALES_INVOICES', 'MANAGE_MOVEMENTS',
                    'VIEW_REPORTS', 'PRODUCTION_REPORTS', 'INVENTORY_REPORTS', 'VIEW_DASHBOARD'
                ])->get();
                
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
            }

            // Supervisor - صلاحيات الإشراف
            if ($supervisor) {
                $this->command->info('⚙️  ربط صلاحيات Supervisor...');
                $supervisor->permissions()->detach();
                
                $supervisorPermissions = Permission::whereIn('permission_code', [
                    'STAGE1_STANDS', 'STAGE2_PROCESSING', 'STAGE3_COILS', 'STAGE4_PACKAGING',
                    'MANAGE_MOVEMENTS', 'VIEW_REPORTS', 'PRODUCTION_REPORTS', 'VIEW_DASHBOARD'
                ])->get();
                
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
            }

            // Accountant - صلاحيات المحاسبة
            if ($accountant) {
                $this->command->info('⚙️  ربط صلاحيات Accountant...');
                $accountant->permissions()->detach();
                
                $accountantPermissions = Permission::whereIn('permission_code', [
                    'PURCHASE_INVOICES', 'SALES_INVOICES', 'VIEW_REPORTS', 'INVENTORY_REPORTS', 'VIEW_DASHBOARD'
                ])->get();
                
                foreach ($accountantPermissions as $permission) {
                    $accountant->permissions()->attach($permission->id, [
                        'can_create' => true,
                        'can_read' => true,
                        'can_update' => true,
                        'can_delete' => false,
                        'can_approve' => true,
                        'can_export' => true,
                    ]);
                }
            }

            // Warehouse Keeper - صلاحيات المخازن
            if ($warehouseKeeper) {
                $this->command->info('⚙️  ربط صلاحيات Warehouse Keeper...');
                $warehouseKeeper->permissions()->detach();
                
                $warehousePermissions = Permission::whereIn('permission_code', [
                    'MANAGE_WAREHOUSES', 'WAREHOUSE_TRANSFERS', 'MANAGE_MOVEMENTS', 
                    'VIEW_REPORTS', 'INVENTORY_REPORTS', 'VIEW_DASHBOARD'
                ])->get();
                
                foreach ($warehousePermissions as $permission) {
                    $warehouseKeeper->permissions()->attach($permission->id, [
                        'can_create' => true,
                        'can_read' => true,
                        'can_update' => true,
                        'can_delete' => false,
                        'can_approve' => false,
                        'can_export' => false,
                    ]);
                }
            }

            // Worker - صلاحيات محدودة
            if ($worker) {
                $this->command->info('⚙️  ربط صلاحيات Worker...');
                $worker->permissions()->detach();
                
                $workerPermissions = Permission::whereIn('permission_code', [
                    'STAGE1_STANDS', 'STAGE2_PROCESSING', 'STAGE3_COILS', 'STAGE4_PACKAGING', 'VIEW_DASHBOARD'
                ])->get();
                
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
            }

            DB::commit();
            $this->command->info('✅ تم ربط الصلاحيات بالأدوار بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ خطأ: ' . $e->getMessage());
        }
    }
}
