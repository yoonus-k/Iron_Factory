<?php
// إنشاء وردية تجريبية للعامل user_id=9
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== إنشاء وردية تجريبية للعامل ===\n";

// إنشاء وردية للعامل خالد علي (user_id=9)
$shift = \App\Models\ShiftAssignment::create([
    'shift_code' => 'TEST-' . now()->format('YmdHis'),
    'shift_type' => 'evening', // وردية مسائية
    'user_id' => 9,
    'supervisor_id' => 1,
    'stage_number' => 1,
    'shift_date' => now()->toDateString(),
    'start_time' => '14:00:00',
    'end_time' => '22:00:00',
    'status' => 'scheduled',
    'is_active' => true,
    'worker_ids' => [9],
    'total_workers' => 1,
]);

echo "✅ تم إنشاء الوردية بنجاح!\n";
echo "Shift ID: {$shift->id}\n";
echo "Type: {$shift->shift_type}\n";
echo "Date: {$shift->shift_date}\n";
echo "User ID: {$shift->user_id}\n";

echo "\n=== التحقق من الوردية ===\n";
$worker = \App\Models\Worker::find(1);
$workerShifts = $worker->shiftAssignments()
    ->whereIn('status', ['active', 'scheduled'])
    ->where('shift_date', '>=', now()->toDateString())
    ->get();

echo "عدد الورديات المسندة: " . $workerShifts->count() . "\n";
foreach ($workerShifts as $s) {
    echo "- Type: {$s->shift_type}, Date: {$s->shift_date}\n";
}
