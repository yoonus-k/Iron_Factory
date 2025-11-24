<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "========== تشخيص مشكلة الصلاحيات ==========\n\n";

// جرب مع كل مستخدم نشط
$users = User::where('is_active', true)->get();

foreach ($users as $user) {
    echo "👤 المستخدم: {$user->name} ({$user->username})\n";
    echo "   البريد: {$user->email}\n";
    echo "   ID الدور: {$user->role_id}\n";

    // حمّل الدور
    if ($user->roleRelation) {
        echo "   ✅ الدور (roleRelation): {$user->roleRelation->role_name} ({$user->roleRelation->role_code})\n";
        echo "      مستوى الدور: {$user->roleRelation->level}\n";
        echo "      عدد الصلاحيات: {$user->roleRelation->permissions()->count()}\n";

        // اختبر بعض الصلاحيات
        echo "      \n      الصلاحيات:\n";

        $permissions = $user->roleRelation->permissions()->take(5)->get();
        foreach ($permissions as $perm) {
            $can_create = $perm->pivot->can_create ? '✓' : '✗';
            $can_read = $perm->pivot->can_read ? '✓' : '✗';
            $can_update = $perm->pivot->can_update ? '✓' : '✗';
            $can_delete = $perm->pivot->can_delete ? '✓' : '✗';

            echo "        - {$perm->permission_code}: C:{$can_create} R:{$can_read} U:{$can_update} D:{$can_delete}\n";
        }
    } else {
        echo "   ❌ لا يوجد دور محمّل!\n";

        if ($user->role) {
            echo "      لكن $user->role يرجع: " . get_class($user->role) . "\n";
        }
    }

    echo "\n";
}

echo "========== نهاية التشخيص ==========\n";
