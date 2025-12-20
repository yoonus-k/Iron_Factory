<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'مدير النظام',
                'username' => 'admin',
                'email' => 'admin@system.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ],
            [
                'name' => 'مدير المصنع',
                'username' => 'manager',
                'email' => 'manager@factory.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
            ],
            [
                'name' => 'مشرف الإنتاج',
                'username' => 'supervisor',
                'email' => 'supervisor@factory.com',
                'password' => Hash::make('password'),
                'role' => 'supervisor',
            ],
            [
                'name' => 'موظف عادي',
                'username' => 'employee',
                'email' => 'employee@factory.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            // الحصول على الدور
            $roleModel = Role::where('role_code', strtoupper($role))->first();
            if ($roleModel) {
                $userData['role_id'] = $roleModel->id;
            }

            // التحقق من وجود المستخدم بالـ username أيضاً
            $user = User::where('email', $userData['email'])
                ->orWhere('username', $userData['username'])
                ->first();
            
            if (!$user) {
                $user = User::create($userData);
                $this->command->info("✅ تم إنشاء المستخدم: {$userData['username']}");
            } else {
                $this->command->info("⚠️ المستخدم موجود بالفعل: {$userData['username']}");
            }
        }

        $this->command->info('✅ تم إنشاء المستخدمين بنجاح');
        $this->command->info('📧 Email: admin@system.com | Password: password');
    }
}
