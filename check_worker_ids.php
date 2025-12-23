<?php
// التحقق من worker_ids في shift_assignments
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== التحقق من worker_ids في shift_assignments ===\n";

$shifts = \App\Models\ShiftAssignment::all();
echo "عدد الورديات: " . $shifts->count() . "\n\n";

foreach ($shifts as $shift) {
    echo "Shift ID: {$shift->id}\n";
    echo "  user_id: {$shift->user_id}\n";
    echo "  shift_type: {$shift->shift_type}\n";
    echo "  shift_date: {$shift->shift_date}\n";
    echo "  status: {$shift->status}\n";
    echo "  worker_ids: " . json_encode($shift->worker_ids) . "\n";
    
    // التحقق إذا كان user_id=9 موجود في worker_ids
    if (is_array($shift->worker_ids) && in_array(9, $shift->worker_ids)) {
        echo "  ✅ يحتوي على user_id=9\n";
    }
    echo "\n";
}

echo "=== البحث عن ورديات تحتوي على user_id=9 في worker_ids ===\n";
$shiftsWithUser9 = \App\Models\ShiftAssignment::whereJsonContains('worker_ids', 9)
    ->get();
echo "عدد الورديات: " . $shiftsWithUser9->count() . "\n";
foreach ($shiftsWithUser9 as $shift) {
    echo "- Shift ID: {$shift->id}, Type: {$shift->shift_type}, Date: {$shift->shift_date}\n";
}
