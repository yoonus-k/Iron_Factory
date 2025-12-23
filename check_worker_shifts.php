<?php
// التحقق من بيانات worker_id=1 و user_id=9
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== التحقق من بيانات Worker ===\n";
$worker = \App\Models\Worker::find(1);
if ($worker) {
    echo "Worker ID: {$worker->id}\n";
    echo "Worker Code: {$worker->worker_code}\n";
    echo "Name: {$worker->name}\n";
    echo "User ID: {$worker->user_id}\n";
    echo "Shift Preference: {$worker->shift_preference}\n";
} else {
    echo "Worker not found!\n";
}

echo "\n=== التحقق من بيانات User ===\n";
$user = \App\Models\User::find(9);
if ($user) {
    echo "User ID: {$user->id}\n";
    echo "Username: {$user->username}\n";
    echo "Name: {$user->name}\n";
} else {
    echo "User not found!\n";
}

echo "\n=== التحقق من ShiftAssignments لـ user_id=9 ===\n";
$shifts = \App\Models\ShiftAssignment::where('user_id', 9)
    ->whereIn('status', ['active', 'scheduled'])
    ->where('shift_date', '>=', now()->toDateString())
    ->orderBy('shift_date', 'asc')
    ->get();

echo "عدد الورديات: " . $shifts->count() . "\n";
foreach ($shifts as $shift) {
    echo "- Shift ID: {$shift->id}, Type: {$shift->shift_type}, Date: {$shift->shift_date}, Status: {$shift->status}\n";
}

echo "\n=== جميع ShiftAssignments لـ user_id=9 ===\n";
$allShifts = \App\Models\ShiftAssignment::where('user_id', 9)->get();
echo "عدد جميع الورديات: " . $allShifts->count() . "\n";
foreach ($allShifts as $shift) {
    echo "- Shift ID: {$shift->id}, Type: {$shift->shift_type}, Date: {$shift->shift_date}, Status: {$shift->status}\n";
}

echo "\n=== اختبار العلاقة من Worker Model ===\n";
if ($worker && $worker->user_id) {
    $workerShifts = $worker->shiftAssignments;
    echo "عدد الورديات من العلاقة: " . $workerShifts->count() . "\n";
    foreach ($workerShifts as $shift) {
        echo "- Shift ID: {$shift->id}, Type: {$shift->shift_type}, Date: {$shift->shift_date}, Status: {$shift->status}\n";
    }
}
