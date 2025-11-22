<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class UpdateAdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $adminRole = Role::where('role_code', 'ADMIN')->first();
            
            if (!$adminRole) {
                $this->command->error('❌ دور Admin غير موجود!');
                return;
            }

            // Update admin user
            $adminUser = User::where('username', 'admin')->first();
            if ($adminUser) {
                $adminUser->update([
                    'role_id' => $adminRole->id,
                ]);
                $this->command->info('✅ تم تحديث مستخدم admin');
            }

            // Update any other users if needed
            $users = User::whereNull('role_id')->get();
            foreach ($users as $user) {
                // Assign Worker role as default
                $workerRole = Role::where('role_code', 'WORKER')->first();
                if ($workerRole) {
                    $user->update([
                        'role_id' => $workerRole->id,
                    ]);
                    $this->command->info("✅ تم تحديث مستخدم: {$user->username}");
                }
            }

            DB::commit();
            $this->command->info('✅ تم تحديث جميع المستخدمين بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ خطأ: ' . $e->getMessage());
        }
    }
}
