<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== فحص ورديات المستخدم 8 ===\n\n";

$user = \App\Models\User::find(8);
echo "المستخدم: " . $user->name . " (ID: " . $user->id . ")\n\n";

echo "=== جميع الورديات اليوم (2025-12-21) ===\n\n";

$allShifts = \App\Models\ShiftAssignment::whereDate('shift_date', '2025-12-21')
    ->orderBy('start_time')
    ->get();

foreach ($allShifts as $shift) {
    echo "الوردية #" . $shift->id . ":\n";
    echo "  - رقم الوردية: " . $shift->shift_number . "\n";
    echo "  - الحالة: " . $shift->status . "\n";
    echo "  - التاريخ: " . $shift->shift_date . "\n";
    echo "  - من: " . $shift->start_time . "\n";
    echo "  - إلى: " . $shift->end_time . "\n";
    echo "  - المشرف ID: " . $shift->supervisor_id . "\n";
    echo "  - العمال: " . json_encode($shift->worker_ids) . "\n";
    
    // هل المستخدم 8 موجود في هذه الوردية؟
    $isInShift = in_array(8, $shift->worker_ids ?? []) || in_array("8", $shift->worker_ids ?? []);
    echo "  - المستخدم 8 موجود: " . ($isInShift ? "✅ نعم" : "❌ لا") . "\n\n";
}

echo "=== الورديات التي يمكن للمستخدم 8 الدخول بها ===\n\n";

$currentShift = \App\Models\ShiftAssignment::where(function($query) use ($user) {
    $query->where(function($q) use ($user) {
        $q->whereJsonContains('worker_ids', $user->id)
          ->orWhereJsonContains('worker_ids', (string)$user->id);
    })->orWhere('supervisor_id', $user->id);
})
->whereIn('status', ['active', 'scheduled'])
->whereDate('shift_date', now()->toDateString())
->get();

if ($currentShift->isEmpty()) {
    echo "❌ لا توجد وردية نشطة للمستخدم 8 اليوم!\n\n";
} else {
    foreach ($currentShift as $shift) {
        echo "وردية #" . $shift->id . ":\n";
        echo "  - من: " . $shift->start_time . " (" . \Carbon\Carbon::parse($shift->start_time)->format('h:i A') . ")\n";
        echo "  - إلى: " . $shift->end_time . " (" . \Carbon\Carbon::parse($shift->end_time)->format('h:i A') . ")\n";
        
        $now = now();
        $shiftDate = \Carbon\Carbon::parse($shift->shift_date)->toDateString();
        $shiftStart = \Carbon\Carbon::parse($shiftDate . ' ' . $shift->start_time);
        $shiftEnd = \Carbon\Carbon::parse($shiftDate . ' ' . $shift->end_time);
        
        // إذا كان وقت النهاية أصغر من وقت البداية، معناها الوردية تمتد إلى اليوم التالي
        if ($shiftEnd->lessThanOrEqualTo($shiftStart)) {
            $shiftEnd->addDay();
            echo "  - الوردية تمتد إلى اليوم التالي!\n";
        }
        
        echo "  - الآن: " . $now->format('Y-m-d h:i A') . "\n";
        echo "  - نهاية الوردية: " . $shiftEnd->format('Y-m-d h:i A') . "\n";
        
        if ($now->greaterThan($shiftEnd)) {
            echo "  🔴 الوردية انتهت!\n";
        } elseif ($now->lessThan($shiftStart)) {
            echo "  🟡 الوردية لم تبدأ بعد\n";
        } else {
            echo "  🟢 الوردية نشطة الآن\n";
        }
        echo "\n";
    }
}
