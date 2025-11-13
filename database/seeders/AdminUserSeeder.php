<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin exists
        if (!User::where('email', 'admin@factory.com')->exists()) {
            User::create([
                'name' => 'مدير المصنع',
                'username' => 'admin',
                'email' => 'admin@factory.com',
                'password' => Hash::make('123456'),
            ]);

            echo "✅ Admin user created successfully!\n";
            echo "📧 Email: admin@factory.com\n";
            echo "🔑 Password: 123456\n";
        } else {
            echo "⚠️ Admin user already exists!\n";
        }

        // Create additional test user
        if (!User::where('email', 'user@factory.com')->exists()) {
            User::create([
                'name' => 'مستخدم تجريبي',
                'username' => 'user',
                'email' => 'user@factory.com',
                'password' => Hash::make('123456'),
            ]);

            echo "✅ Test user created successfully!\n";
            echo "📧 Email: user@factory.com\n";
            echo "🔑 Password: 123456\n";
        }
    }
}
