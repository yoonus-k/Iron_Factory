<?php

// هذا الملف يجب رفعه على السيرفر الأون لاين وتشغيله هناك
// https://hitstest.sehoool.com/create_online_token.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// البحث عن أول مستخدم أو إنشاء واحد
$user = \App\Models\User::first();

if (!$user) {
    echo "⚠️  لا يوجد مستخدمين! سيتم إنشاء مستخدم جديد...\n";
    $user = \App\Models\User::create([
        'name' => 'Central Server Admin',
        'username' => 'central_admin',
        'email' => 'admin@central.com',
        'password' => bcrypt('admin123456'),
    ]);
    echo "✅ تم إنشاء المستخدم: {$user->name}\n\n";
} else {
    echo "✅ تم العثور على المستخدم: {$user->name}\n\n";
}

// حذف جميع الـ tokens القديمة لهذا المستخدم
$user->tokens()->delete();
echo "🗑️  تم حذف جميع الـ tokens القديمة\n\n";

// إنشاء token جديد
$token = $user->createToken('local-server-sync', ['sync:*'])->plainTextToken;

echo "═══════════════════════════════════════════════════════════\n";
echo "✅ تم إنشاء Token جديد بنجاح!\n";
echo "═══════════════════════════════════════════════════════════\n\n";
echo "Token:\n";
echo $token . "\n\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "الخطوات التالية:\n";
echo "1. انسخ الـ Token أعلاه\n";
echo "2. افتح ملف .env على السيرفر المحلي\n";
echo "3. حدّث قيمة CENTRAL_SERVER_TOKEN\n";
echo "4. احذف هذا الملف من السيرفر الأون لاين لأسباب أمنية\n";
echo "═══════════════════════════════════════════════════════════\n";
