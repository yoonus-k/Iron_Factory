<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ShiftAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== اختبار تحديث الوردية ===\n\n";

$shiftId = 1; // غير هذا الرقم حسب الوردية التي تريد اختبارها

$shift = ShiftAssignment::find($shiftId);

if (!$shift) {
    echo "❌ الوردية غير موجودة!\n";
    exit;
}

echo "📋 الوردية الحالية:\n";
echo "   - الكود: {$shift->shift_code}\n";
echo "   - التاريخ: {$shift->shift_date->format('Y-m-d')}\n";
echo "   - الحالة: {$shift->status}\n";
echo "   - العمال الحاليين: " . json_encode($shift->worker_ids) . "\n";
echo "   - عدد العمال: " . count($shift->worker_ids ?? []) . "\n\n";

// عرض العمال الحاليين
if (!empty($shift->worker_ids)) {
    echo "👷 العمال المسندون حالياً:\n";
    $currentWorkers = User::whereIn('id', $shift->worker_ids)->get();
    foreach ($currentWorkers as $worker) {
        echo "   - [{$worker->id}] {$worker->name}\n";
    }
    echo "\n";
}

// محاكاة تحديث الوردية بإضافة عامل
echo "🔄 اختبار التحديث - إضافة عامل جديد:\n\n";

$newWorkerIds = [5, 7]; // مثال: إضافة المستخدمين 5 و 7

echo "العمال الجدد: " . json_encode($newWorkerIds) . "\n";

try {
    $shift->update([
        'worker_ids' => $newWorkerIds,
        'total_workers' => count($newWorkerIds),
    ]);
    
    echo "✅ تم التحديث بنجاح!\n\n";
    
    // إعادة قراءة البيانات
    $shift->refresh();
    
    echo "📋 بعد التحديث:\n";
    echo "   - worker_ids: " . json_encode($shift->worker_ids) . "\n";
    echo "   - total_workers: {$shift->total_workers}\n\n";
    
    if (!empty($shift->worker_ids)) {
        echo "👷 العمال بعد التحديث:\n";
        $updatedWorkers = User::whereIn('id', $shift->worker_ids)->get();
        foreach ($updatedWorkers as $worker) {
            echo "   - [{$worker->id}] {$worker->name}\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ فشل التحديث: " . $e->getMessage() . "\n";
}

echo "\n=== انتهى الاختبار ===\n";
