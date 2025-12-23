<?php
// عرض جميع الورديات في قاعدة البيانات
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== جميع ShiftAssignments في قاعدة البيانات ===\n";
$allShifts = \App\Models\ShiftAssignment::with(['user'])
    ->orderBy('shift_date', 'desc')
    ->limit(20)
    ->get();

echo "عدد الورديات: " . $allShifts->count() . "\n\n";

foreach ($allShifts as $shift) {
    $userName = $shift->user ? $shift->user->name : 'غير معروف';
    echo "ID: {$shift->id} | User: {$shift->user_id} ({$userName}) | Type: {$shift->shift_type} | Date: {$shift->shift_date} | Status: {$shift->status}\n";
}

echo "\n=== جميع Workers مع user_id ===\n";
$workers = \App\Models\Worker::whereNotNull('user_id')->get();
echo "عدد العمال: " . $workers->count() . "\n\n";

foreach ($workers as $worker) {
    echo "Worker ID: {$worker->id} | Code: {$worker->worker_code} | Name: {$worker->name} | User ID: {$worker->user_id}\n";
}
