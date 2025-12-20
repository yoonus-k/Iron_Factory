<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== فحص صلاحيات المستخدم رقم 1 ===\n\n";

$user = App\Models\User::with('roleRelation.permissions')->find(1);

if (!$user) {
    echo "❌ المستخدم غير موجود!\n";
    exit;
}

echo "👤 المستخدم: {$user->name}\n";
echo "🎭 Role ID: {$user->role_id}\n";

if (!$user->role_id) {
    echo "❌ المستخدم ليس لديه role_id!\n";
    exit;
}

$role = App\Models\Role::with('permissions')->find($user->role_id);

if (!$role) {
    echo "❌ الدور غير موجود!\n";
    exit;
}

echo "🎭 الدور: {$role->name}\n";
echo "📋 عدد الصلاحيات: " . $role->permissions->count() . "\n\n";

// Check specific permissions
$permissions = [
    'WAREHOUSE_INTAKE_READ',
    'WAREHOUSE_INTAKE_CREATE',
    'WAREHOUSE_INTAKE_APPROVE',
    'WAREHOUSE_INTAKE_REJECT',
    'WAREHOUSE_INTAKE_PRINT'
];

echo "=== صلاحيات WAREHOUSE_INTAKE ===\n";
foreach ($permissions as $permission) {
    $has = $user->hasPermission($permission);
    $icon = $has ? '✅' : '❌';
    echo "$icon $permission: " . ($has ? 'موجودة' : 'غير موجودة') . "\n";
}

echo "\n=== جميع صلاحيات الدور ===\n";
$allPermissions = $role->permissions->pluck('name')->toArray();
$warehouseIntakePerms = array_filter($allPermissions, function($p) {
    return strpos($p, 'WAREHOUSE_INTAKE') !== false;
});

if (empty($warehouseIntakePerms)) {
    echo "❌ لا توجد صلاحيات WAREHOUSE_INTAKE في هذا الدور!\n";
} else {
    echo "✅ صلاحيات WAREHOUSE_INTAKE الموجودة:\n";
    foreach ($warehouseIntakePerms as $perm) {
        echo "   - $perm\n";
    }
}
