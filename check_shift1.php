<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== فحص الوردية رقم 1 ===\n\n";

$shift = \App\Models\ShiftAssignment::find(1);

if (!$shift) {
    echo "❌ الوردية غير موجودة!\n";
    exit;
}

echo "ID: " . $shift->id . "\n";
echo "رقم الوردية: " . $shift->shift_number . "\n";
echo "التاريخ: " . $shift->shift_date . "\n";
echo "البداية: " . $shift->start_time . "\n";
echo "النهاية: " . $shift->end_time . "\n";
echo "الحالة: " . $shift->status . "\n";
echo "المشرف ID: " . $shift->supervisor_id . "\n";
echo "العمال: " . json_encode($shift->worker_ids) . "\n";

echo "\n=== الوقت الحالي ===\n";
echo "الآن: " . now()->format('Y-m-d H:i:s') . "\n";

// استخدام toDateString() لأخذ التاريخ فقط
$shiftDate = \Carbon\Carbon::parse($shift->shift_date)->toDateString();
$shiftEnd = \Carbon\Carbon::parse($shiftDate . ' ' . $shift->end_time);
echo "نهاية الوردية: " . $shiftEnd->format('Y-m-d H:i:s') . "\n";

$isPast = now()->greaterThan($shiftEnd);
echo "هل الوقت تجاوز النهاية؟ " . ($isPast ? "نعم ✅" : "لا ❌") . "\n";

if ($isPast) {
    echo "\n🔴 الوردية انتهت! يجب منع الدخول\n";
} else {
    echo "\n🟢 الوردية لا تزال نشطة\n";
}

echo "\n=== فحص العامل رقم 8 ===\n";
$user = \App\Models\User::find(8);
echo "الاسم: " . $user->name . "\n";
echo "هل موجود في worker_ids؟ " . (in_array(8, $shift->worker_ids ?? []) ? "نعم ✅" : "لا ❌") . "\n";
