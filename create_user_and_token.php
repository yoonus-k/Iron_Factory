<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// إنشاء مستخدم
$user = \App\Models\User::firstOrCreate(
    ['email' => 'admin@system.local'],
    [
        'name' => 'System Admin',
        'username' => 'admin',
        'password' => bcrypt('password123'),
    ]
);

echo "✅ User created/found: {$user->name} (ID: {$user->id})\n";

// حذف جميع الـ tokens القديمة
$user->tokens()->delete();

// إنشاء token جديد
$token = $user->createToken('sync-token', ['sync:*'])->plainTextToken;

echo "✅ New token created:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo $token . "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "⚠️  Update your .env file:\n";
echo "CENTRAL_SERVER_TOKEN=" . $token . "\n";
