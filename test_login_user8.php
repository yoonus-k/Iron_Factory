<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== اختبار تسجيل دخول المستخدم 8 ===\n\n";

$user = \App\Models\User::find(8);
if (!$user) {
    echo "❌ المستخدم غير موجود!\n";
    exit;
}

echo "المستخدم: " . $user->name . " (ID: " . $user->id . ")\n";
echo "البريد: " . $user->email . "\n\n";

// التحقق من صلاحياته
echo "=== الصلاحيات ===\n";
if ($user->role) {
    echo "الدور: " . $user->role->role_code . "\n";
    
    if (in_array($user->role->role_code, ['ADMIN', 'SUPER_ADMIN', 'SUPERVISOR', 'MANAGER'])) {
        echo "🟢 هذا المستخدم لديه دور إداري - سيُسمح له بالدخول دائماً!\n";
        exit;
    }
}

// التحقق من صلاحيات إدارة الورديات
if ($user->can('manage-shifts') || $user->can('edit-shifts')) {
    echo "🟢 لديه صلاحية إدارة الورديات - سيُسمح له بالدخول دائماً!\n";
    exit;
}

echo "🔴 مستخدم عادي (عامل)\n\n";

// التحقق من أنه عامل (من خلال دوره)
if (!$user->role || !str_contains($user->role->role_code, 'WORKER')) {
    echo "✅ ليس عامل - يمكنه الدخول بدون قيود!\n";
    exit;
}

echo "✅ هو عامل (دوره: " . $user->role->role_code . ")\n\n";

// البحث عن الوردية الحالية
$currentShift = \App\Models\ShiftAssignment::where(function($query) use ($user) {
    $query->where(function($q) use ($user) {
        $q->whereJsonContains('worker_ids', $user->id)
          ->orWhereJsonContains('worker_ids', (string)$user->id);
    })->orWhere('supervisor_id', $user->id);
})
->whereIn('status', ['active', 'scheduled'])
->whereDate('shift_date', now()->toDateString())
->first();

echo "=== نتيجة البحث عن الوردية ===\n";
if (!$currentShift) {
    echo "❌ لا توجد وردية نشطة اليوم\n";
    echo "🔴 يجب منع الدخول!\n";
    exit;
}

echo "✅ وجدت وردية:\n";
echo "   - ID: " . $currentShift->id . "\n";
echo "   - الحالة: " . $currentShift->status . "\n";
echo "   - التاريخ: " . $currentShift->shift_date . "\n";
echo "   - من: " . $currentShift->start_time . "\n";
echo "   - إلى: " . $currentShift->end_time . "\n\n";

// التحقق من الوقت
$now = now();
$shiftDate = \Carbon\Carbon::parse($currentShift->shift_date)->toDateString();
$shiftEnd = \Carbon\Carbon::parse($shiftDate . ' ' . $currentShift->end_time);

echo "=== التحقق من الوقت ===\n";
echo "الوقت الحالي: " . $now->format('Y-m-d H:i:s') . "\n";
echo "نهاية الوردية: " . $shiftEnd->format('Y-m-d H:i:s') . "\n";

if ($now->greaterThan($shiftEnd)) {
    echo "🔴 الوقت تجاوز نهاية الوردية - يجب منع الدخول!\n";
} else {
    echo "🟢 الوردية لا تزال نشطة - يمكنه الدخول\n";
}
