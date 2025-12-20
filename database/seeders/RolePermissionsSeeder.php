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
                    'WAREHOUSE_PURCHASE_INVOICES_UPDATE', 'WAREHOUSE_PURCHASE_INVOICES_DELETE',
                    'MENU_PRODUCTION_CONFIRMATIONS', 'PRODUCTION_CONFIRMATIONS_READ', 'PRODUCTION_CONFIRMATIONS_CONFIRM',
                    'PRODUCTION_CONFIRMATIONS_REJECT', 'PRODUCTION_CONFIRMATIONS_VIEW_DETAILS',
                    'WAREHOUSE_INTAKE_READ', 'WAREHOUSE_INTAKE_CREATE', 'WAREHOUSE_INTAKE_APPROVE', 'WAREHOUSE_INTAKE_REJECT', 'WAREHOUSE_INTAKE_PRINT'
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
                    'WAREHOUSE_REGISTRATION_READ', 'WAREHOUSE_RECONCILIATION_READ', 'WAREHOUSE_MOVEMENTS_READ',
                    'MENU_PRODUCTION_CONFIRMATIONS', 'PRODUCTION_CONFIRMATIONS_READ', 'PRODUCTION_CONFIRMATIONS_VIEW_DETAILS'
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
                    'MENU_WAREHOUSE_MOVEMENTS', 'WAREHOUSE_MOVEMENTS_READ', 'WAREHOUSE_MOVEMENTS_DETAILS',
                    'MENU_PRODUCTION_CONFIRMATIONS', 'PRODUCTION_CONFIRMATIONS_READ', 'PRODUCTION_CONFIRMATIONS_VIEW_DETAILS'
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
                    'STAGE1_STANDS', 'STAGE2_PROCESSING', 'STAGE3_COILS', 'STAGE4_PACKAGING', 'VIEW_DASHBOARD',
                    'MENU_PRODUCTION_CONFIRMATIONS', 'PRODUCTION_CONFIRMATIONS_CONFIRM', 'PRODUCTION_CONFIRMATIONS_REJECT'
                ])->get();

                foreach ($workerPermissions as $permission) {
                    $worker->permissions()->attach($permission->id);
                }
            }

            // Stage 1 Worker - صلاحيات المرحلة الأولى فقط
            $stage1Worker = Role::where('role_code', 'STAGE1_WORKER')->first();
            if ($stage1Worker) {
                $this->command->info('⚙️  ربط صلاحيات Stage 1 Worker...');
                $stage1Worker->permissions()->detach();

                $stage1Permissions = Permission::whereIn('name', [
                    'MENU_STAGE1_STANDS', 'STAGE1_STANDS_READ', 'STAGE1_STANDS_CREATE', 
                    'STAGE1_STANDS_UPDATE', 'STAGE1_BARCODE_SCAN', 'STAGE1_WASTE_TRACKING',
                    'VIEW_DASHBOARD', 'STAGE_WORKER_DASHBOARD'
                ])->get();

                foreach ($stage1Permissions as $permission) {
                    $stage1Worker->permissions()->attach($permission->id);
                }
            }

            // Stage 2 Worker - صلاحيات المرحلة الثانية فقط
            $stage2Worker = Role::where('role_code', 'STAGE2_WORKER')->first();
            if ($stage2Worker) {
                $this->command->info('⚙️  ربط صلاحيات Stage 2 Worker...');
                $stage2Worker->permissions()->detach();

                $stage2Permissions = Permission::whereIn('name', [
                    'MENU_STAGE2_PROCESSING', 'STAGE2_PROCESSING_READ', 'STAGE2_PROCESSING_CREATE', 
                    'STAGE2_PROCESSING_UPDATE', 'STAGE2_COMPLETE_PROCESSING', 'STAGE2_WASTE_STATISTICS',
                    'VIEW_DASHBOARD', 'STAGE_WORKER_DASHBOARD', 'MENU_PRODUCTION_CONFIRMATIONS', 
                    'PRODUCTION_CONFIRMATIONS_CONFIRM', 'PRODUCTION_CONFIRMATIONS_REJECT'
                ])->get();                foreach ($stage2Permissions as $permission) {
                    $stage2Worker->permissions()->attach($permission->id);
                }
            }

            // Stage 3 Worker - صلاحيات المرحلة الثالثة فقط
            $stage3Worker = Role::where('role_code', 'STAGE3_WORKER')->first();
            if ($stage3Worker) {
                $this->command->info('⚙️  ربط صلاحيات Stage 3 Worker...');
                $stage3Worker->permissions()->detach();

                $stage3Permissions = Permission::whereIn('name', [
                    'MENU_STAGE3_COILS', 'STAGE3_COILS_READ', 'STAGE3_COILS_CREATE',
                    'STAGE3_COILS_UPDATE',
                    'VIEW_DASHBOARD', 'STAGE_WORKER_DASHBOARD', 'MENU_PRODUCTION_CONFIRMATIONS',
                    'PRODUCTION_CONFIRMATIONS_CONFIRM', 'PRODUCTION_CONFIRMATIONS_REJECT'
                ])->get();

                foreach ($stage3Permissions as $permission) {
                    $stage3Worker->permissions()->attach($permission->id);
                }
            }

            // Stage 4 Worker - صلاحيات المرحلة الرابعة فقط
            $stage4Worker = Role::where('role_code', 'STAGE4_WORKER')->first();
            if ($stage4Worker) {
                $this->command->info('⚙️  ربط صلاحيات Stage 4 Worker...');
                $stage4Worker->permissions()->detach();

                $stage4Permissions = Permission::whereIn('name', [
                    'MENU_STAGE4_PACKAGING', 'STAGE4_PACKAGING_READ', 'STAGE4_PACKAGING_CREATE',
                    'STAGE4_PACKAGING_UPDATE',
                    'VIEW_DASHBOARD', 'STAGE_WORKER_DASHBOARD', 'MENU_PRODUCTION_CONFIRMATIONS',
                    'PRODUCTION_CONFIRMATIONS_CONFIRM', 'PRODUCTION_CONFIRMATIONS_REJECT'
                ])->get();

                foreach ($stage4Permissions as $permission) {
                    $stage4Worker->permissions()->attach($permission->id);
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
