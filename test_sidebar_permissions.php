#!/usr/bin/env php
<?php
/**
 * اختبار سريع لصلاحيات الـ Sidebar
 * يمكن تشغيله من سطر الأوامر:
 * php test_sidebar_permissions.php
 */

echo "\n╔══════════════════════════════════════════════╗\n";
echo "║     اختبار صلاحيات القائمة الجانبية        ║\n";
echo "╚══════════════════════════════════════════════╝\n\n";

// تحميل Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;

// الأدوار المختلفة
$roles = [
    'ADMIN' => 'مدير النظام',
    'MANAGER' => 'المدير',
    'SUPERVISOR' => 'المشرف',
    'WORKER' => 'العامل',
];

// الصلاحيات في الـ Sidebar
$permissions = [
    'VIEW_MAIN_DASHBOARD' => 'لوحة التحكم',
    'MANAGE_WAREHOUSES' => 'المستودع',
    'STAGE1_STANDS' => 'المرحلة الأولى',
    'STAGE2_PROCESSING' => 'المرحلة الثانية',
    'STAGE3_COILS' => 'المرحلة الثالثة',
    'STAGE4_PACKAGING' => 'المرحلة الرابعة',
    'MANAGE_MOVEMENTS' => 'تتبع الإنتاج والورديات',
    'VIEW_COSTS' => 'الهدر والجودة',
    'VIEW_REPORTS' => 'التقارير',
    'MANAGE_USERS' => 'الإدارة',
];

echo "📋 الصلاحيات المطلوبة في الـ Sidebar:\n";
echo "─────────────────────────────────────────────\n";
foreach ($permissions as $code => $name) {
    printf("  • %-30s (%s)\n", $name, $code);
}

echo "\n\n📊 توزيع الصلاحيات على الأدوار:\n";
echo "─────────────────────────────────────────────\n";

foreach ($roles as $roleCode => $roleName) {
    $role = Role::where('role_code', $roleCode)->first();

    if (!$role) {
        echo "❌ الدور {$roleName} ({$roleCode}) غير موجود!\n";
        continue;
    }

    echo "\n✅ {$roleName} ({$roleCode}):\n";

    $rolePermissions = $role->permissions()
        ->whereIn('permission_code', array_keys($permissions))
        ->pluck('permission_code')
        ->toArray();

    foreach ($permissions as $code => $name) {
        $hasPermission = in_array($code, $rolePermissions);
        $status = $hasPermission ? '✅' : '❌';
        echo "   {$status} {$name}\n";
    }
}

echo "\n\n🧪 اختبار الـ Helper Functions:\n";
echo "─────────────────────────────────────────────\n";

// اختبار مع مستخدم بدور مختلف
$testUser = User::first();

if (!$testUser) {
    echo "⚠️  لا يوجد مستخدمين في النظام للاختبار!\n";
} else {
    echo "المستخدم: {$testUser->name}\n";
    echo "الدور: " . ($testUser->role ? $testUser->role->role_name : 'بدون دور') . "\n\n";

    // اختبار بعض الصلاحيات
    echo "نتائج الاختبار:\n";
    echo "  canRead('MANAGE_USERS'): " . (canRead('MANAGE_USERS') ? '✅ صحيح' : '❌ خطأ') . "\n";
    echo "  canCreate('MANAGE_USERS'): " . (canCreate('MANAGE_USERS') ? '✅ صحيح' : '❌ خطأ') . "\n";
    echo "  hasRole('ADMIN'): " . (hasRole('ADMIN') ? '✅ صحيح' : '❌ خطأ') . "\n";
    echo "  isAdmin(): " . (isAdmin() ? '✅ صحيح' : '❌ خطأ') . "\n";
}

echo "\n\n✨ تم الاختبار بنجاح!\n\n";
?>
