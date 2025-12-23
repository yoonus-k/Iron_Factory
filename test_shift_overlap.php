<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== اختبار: إضافة عامل لورديتين متداخلتين ===\n\n";

// محاكاة: لو أضفنا المستخدم 8 للوردية #1 أيضاً
echo "السيناريو: المستخدم 8 في الوردية #1 (06:00-18:00) والوردية #4 (18:00-06:00)\n\n";

$shifts = \App\Models\ShiftAssignment::whereDate('shift_date', '2025-12-21')
    ->orderBy('start_time')
    ->get();

echo "الورديات اليوم:\n";
foreach ($shifts as $shift) {
    $hasUser8 = in_array(8, $shift->worker_ids ?? []) || in_array("8", $shift->worker_ids ?? []);
    echo "- وردية #" . $shift->id . ": " . $shift->start_time . " → " . $shift->end_time;
    echo " | المستخدم 8: " . ($hasUser8 ? "✅ موجود" : "❌ غير موجود") . "\n";
}

echo "\n=== فحص التداخل ===\n\n";

// فحص لو أضفنا المستخدم 8 للوردية #1
$shift1 = \App\Models\ShiftAssignment::find(1);
$shift4 = \App\Models\ShiftAssignment::find(4);

if ($shift1 && $shift4) {
    echo "الوردية #1: " . $shift1->start_time . " → " . $shift1->end_time . "\n";
    echo "الوردية #4: " . $shift4->start_time . " → " . $shift4->end_time . "\n\n";
    
    // حساب الأوقات
    $shiftDate = \Carbon\Carbon::parse($shift1->shift_date)->toDateString();
    
    $shift1Start = \Carbon\Carbon::parse($shiftDate . ' ' . $shift1->start_time);
    $shift1End = \Carbon\Carbon::parse($shiftDate . ' ' . $shift1->end_time);
    
    $shift4Start = \Carbon\Carbon::parse($shiftDate . ' ' . $shift4->start_time);
    $shift4End = \Carbon\Carbon::parse($shiftDate . ' ' . $shift4->end_time);
    
    // إذا كان وقت النهاية أصغر من وقت البداية، الوردية تمتد لليوم التالي
    if ($shift1End->lessThanOrEqualTo($shift1Start)) {
        $shift1End->addDay();
    }
    if ($shift4End->lessThanOrEqualTo($shift4Start)) {
        $shift4End->addDay();
    }
    
    echo "الوردية #1: " . $shift1Start->format('Y-m-d H:i') . " → " . $shift1End->format('Y-m-d H:i') . "\n";
    echo "الوردية #4: " . $shift4Start->format('Y-m-d H:i') . " → " . $shift4End->format('Y-m-d H:i') . "\n\n";
    
    // فحص التداخل
    $hasOverlap = (
        ($shift1Start->between($shift4Start, $shift4End, false)) ||
        ($shift1End->between($shift4Start, $shift4End, false)) ||
        ($shift4Start->between($shift1Start, $shift1End, false)) ||
        ($shift4End->between($shift1Start, $shift1End, false)) ||
        ($shift1Start->equalTo($shift4Start)) ||
        ($shift1End->equalTo($shift4End))
    );
    
    if ($hasOverlap) {
        echo "🔴 هناك تداخل! لا يمكن للعامل أن يكون في الورديتين!\n";
    } else {
        echo "🟢 لا يوجد تداخل - يمكن للعامل أن يكون في الورديتين\n";
    }
}

echo "\n=== التوصية ===\n";
echo "يجب إضافة validation في ShiftsWorkersController:\n";
echo "1. عند إضافة عامل لوردية، فحص الورديات الأخرى في نفس اليوم\n";
echo "2. رفض الإضافة إذا كان هناك تداخل في الأوقات\n";
echo "3. عرض رسالة خطأ واضحة للمستخدم\n";
