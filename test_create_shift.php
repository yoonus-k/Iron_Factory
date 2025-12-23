<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ShiftAssignment;
use Illuminate\Support\Facades\DB;

echo "=== اختبار حفظ وردية مع عمال ===\n\n";

// محاكاة البيانات المُرسلة من الـ form
$formData = [
    'shift_code' => 'TEST-' . time(),
    'shift_type' => 'morning',
    'supervisor_id' => 7, // عبدالمنعم
    'workers' => [5, 8], // مشرف الإنتاج + محمد
    'shift_date' => now()->format('Y-m-d'),
    'start_time' => '06:00',
    'end_time' => '18:00',
    'stage_number' => 1,
];

echo "📝 البيانات المُرسلة:\n";
echo "   - المشرف: {$formData['supervisor_id']}\n";
echo "   - العمال: " . json_encode($formData['workers']) . "\n\n";

try {
    DB::beginTransaction();
    
    $workerIds = $formData['workers'];
    
    // تنظيف البيانات كما في الكود
    if (!is_array($workerIds)) {
        $workerIds = [];
    }
    $workerIds = array_filter($workerIds);
    $workerIds = array_map('intval', $workerIds);
    $workerIds = array_values($workerIds);
    
    echo "🔄 بعد المعالجة:\n";
    echo "   - worker_ids: " . json_encode($workerIds) . "\n";
    echo "   - total_workers: " . count($workerIds) . "\n\n";
    
    $shift = ShiftAssignment::create([
        'shift_code' => $formData['shift_code'],
        'shift_type' => $formData['shift_type'],
        'user_id' => $formData['supervisor_id'],
        'supervisor_id' => $formData['supervisor_id'],
        'stage_number' => $formData['stage_number'],
        'shift_date' => $formData['shift_date'],
        'start_time' => $formData['start_time'],
        'end_time' => $formData['end_time'],
        'status' => 'scheduled',
        'total_workers' => count($workerIds),
        'worker_ids' => $workerIds,
    ]);
    
    DB::commit();
    
    echo "✅ تم إنشاء الوردية بنجاح!\n\n";
    
    // قراءة البيانات المحفوظة
    $shift->refresh();
    
    echo "📋 البيانات المحفوظة:\n";
    echo "   - ID: {$shift->id}\n";
    echo "   - shift_code: {$shift->shift_code}\n";
    echo "   - user_id: {$shift->user_id}\n";
    echo "   - supervisor_id: {$shift->supervisor_id}\n";
    echo "   - worker_ids: " . json_encode($shift->worker_ids) . "\n";
    echo "   - total_workers: {$shift->total_workers}\n\n";
    
    if (!empty($shift->worker_ids)) {
        echo "👷 العمال المحفوظون:\n";
        $workers = \App\Models\User::whereIn('id', $shift->worker_ids)->get();
        foreach ($workers as $worker) {
            echo "   - [{$worker->id}] {$worker->name}\n";
        }
    }
    
    // حذف الوردية التجريبية
    $shift->delete();
    echo "\n🗑️  تم حذف الوردية التجريبية\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}

echo "\n=== انتهى الاختبار ===\n";
