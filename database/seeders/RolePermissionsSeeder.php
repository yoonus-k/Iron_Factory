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
                    $admin->permissions()->attach($permission->id);
                }
            }

            // Manager - معظم الصلاحيات
            if ($manager) {
                $this->command->info('⚙️  ربط صلاحيات Manager...');
                $manager->permissions()->detach();

                $managerPermissions = Permission::whereIn('name', [
                    'MANAGE_USERS', 'MANAGE_MATERIALS', 'MANAGE_SUPPLIERS', 'MANAGE_WAREHOUSES',
                    'WAREHOUSE_TRANSFERS', 'STAGE1_STANDS', 'STAGE2_PROCESSING', 'STAGE3_COILS',
                    'STAGE4_PACKAGING', 'PURCHASE_INVOICES', 'SALES_INVOICES', 'MANAGE_MOVEMENTS',
                    'VIEW_REPORTS', 'PRODUCTION_REPORTS', 'INVENTORY_REPORTS', 'VIEW_DASHBOARD',
                    'MENU_WAREHOUSE_REGISTRATION', 'WAREHOUSE_REGISTRATION_READ', 'WAREHOUSE_REGISTRATION_CREATE',
                    'WAREHOUSE_REGISTRATION_UPDATE', 'WAREHOUSE_REGISTRATION_LOCK', 'WAREHOUSE_REGISTRATION_UNLOCK', 'WAREHOUSE_REGISTRATION_TRANSFER',
                    'MENU_WAREHOUSE_RECONCILIATION', 'WAREHOUSE_RECONCILIATION_READ', 'WAREHOUSE_RECONCILIATION_CREATE',
                    'WAREHOUSE_RECONCILIATION_UPDATE', 'WAREHOUSE_RECONCILIATION_MANAGEMENT', 'WAREHOUSE_RECONCILIATION_LINK_INVOICE',
                    'MENU_WAREHOUSE_MOVEMENTS', 'WAREHOUSE_MOVEMENTS_READ', 'WAREHOUSE_MOVEMENTS_DETAILS',
                    'MENU_WAREHOUSE_PURCHASE_INVOICES', 'WAREHOUSE_PURCHASE_INVOICES_READ', 'WAREHOUSE_PURCHASE_INVOICES_CREATE',
                    'WAREHOUSE_PURCHASE_INVOICES_UPDATE', 'WAREHOUSE_PURCHASE_INVOICES_DELETE'
                ])->get();

                foreach ($managerPermissions as $permission) {
                    $manager->permissions()->attach($permission->id);
                }
            }

            // Supervisor - صلاحيات الإشراف
            if ($supervisor) {
                $this->command->info('⚙️  ربط صلاحيات Supervisor...');
                $supervisor->permissions()->detach();

                $supervisorPermissions = Permission::whereIn('name', [
                    'STAGE1_STANDS', 'STAGE2_PROCESSING', 'STAGE3_COILS', 'STAGE4_PACKAGING',
                    'MANAGE_MOVEMENTS', 'VIEW_REPORTS', 'PRODUCTION_REPORTS', 'VIEW_DASHBOARD',
                    'WAREHOUSE_REGISTRATION_READ', 'WAREHOUSE_RECONCILIATION_READ', 'WAREHOUSE_MOVEMENTS_READ'
                ])->get();

                foreach ($supervisorPermissions as $permission) {
                    $supervisor->permissions()->attach($permission->id);
                }
            }

            // Accountant - صلاحيات المحاسبة
            if ($accountant) {
                $this->command->info('⚙️  ربط صلاحيات Accountant...');
                $accountant->permissions()->detach();

                $accountantPermissions = Permission::whereIn('name', [
                    'PURCHASE_INVOICES', 'SALES_INVOICES', 'VIEW_REPORTS', 'INVENTORY_REPORTS', 'VIEW_DASHBOARD',
                    'MENU_WAREHOUSE_PURCHASE_INVOICES', 'WAREHOUSE_PURCHASE_INVOICES_READ', 'WAREHOUSE_PURCHASE_INVOICES_CREATE',
                    'WAREHOUSE_PURCHASE_INVOICES_UPDATE', 'WAREHOUSE_PURCHASE_INVOICES_DELETE'
                ])->get();

                foreach ($accountantPermissions as $permission) {
                    $accountant->permissions()->attach($permission->id);
                }
            }            // Warehouse Keeper - صلاحيات المخازن
            if ($warehouseKeeper) {
                $this->command->info('⚙️  ربط صلاحيات Warehouse Keeper...');
                $warehouseKeeper->permissions()->detach();

                $warehousePermissions = Permission::whereIn('name', [
                    'MANAGE_WAREHOUSES', 'WAREHOUSE_TRANSFERS', 'MANAGE_MOVEMENTS',
                    'VIEW_REPORTS', 'INVENTORY_REPORTS', 'VIEW_DASHBOARD',
                    'MENU_WAREHOUSE_REGISTRATION', 'WAREHOUSE_REGISTRATION_READ', 'WAREHOUSE_REGISTRATION_CREATE',
                    'WAREHOUSE_REGISTRATION_UPDATE', 'WAREHOUSE_REGISTRATION_LOCK', 'WAREHOUSE_REGISTRATION_UNLOCK', 'WAREHOUSE_REGISTRATION_TRANSFER',
                    'MENU_WAREHOUSE_RECONCILIATION', 'WAREHOUSE_RECONCILIATION_READ', 'WAREHOUSE_RECONCILIATION_CREATE',
                    'WAREHOUSE_RECONCILIATION_UPDATE', 'WAREHOUSE_RECONCILIATION_LINK_INVOICE',
                    'MENU_WAREHOUSE_MOVEMENTS', 'WAREHOUSE_MOVEMENTS_READ', 'WAREHOUSE_MOVEMENTS_DETAILS'
                ])->get();

                foreach ($warehousePermissions as $permission) {
                    $warehouseKeeper->permissions()->attach($permission->id);
                }
            }

            // Worker - صلاحيات محدودة
            if ($worker) {
                $this->command->info('⚙️  ربط صلاحيات Worker...');
                $worker->permissions()->detach();

                $workerPermissions = Permission::whereIn('name', [
                    'STAGE1_STANDS', 'STAGE2_PROCESSING', 'STAGE3_COILS', 'STAGE4_PACKAGING', 'VIEW_DASHBOARD'
                ])->get();

                foreach ($workerPermissions as $permission) {
                    $worker->permissions()->attach($permission->id);
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
