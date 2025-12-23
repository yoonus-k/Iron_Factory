<?php
// اختبار الكود المعدل
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== اختبار عرض الوردية للعامل ===\n";

$worker = \App\Models\Worker::find(1);
echo "Worker: {$worker->name} (ID: {$worker->id}, User ID: {$worker->user_id})\n";
echo "Shift Preference: {$worker->shift_preference}\n";

// البحث عن الوردية كما في Controller
if ($worker->user_id) {
    $currentShift = \App\Models\ShiftAssignment::whereJsonContains('worker_ids', $worker->user_id)
        ->whereIn('status', ['active', 'scheduled'])
        ->where('shift_date', '>=', now()->toDateString())
        ->orderBy('shift_date', 'asc')
        ->first();
    
    if ($currentShift) {
        echo "✅ وجدت وردية!\n";
        echo "  Shift Type: {$currentShift->shift_type}\n";
        echo "  Shift Date: {$currentShift->shift_date}\n";
        echo "  Status: {$currentShift->status}\n";
        
        $shiftTypeName = match($currentShift->shift_type) {
            'morning' => 'الفترة الصباحية',
            'evening' => 'الفترة المسائية',
            'night' => 'الفترة الليلية',
            default => 'غير محدد'
        };
        
        echo "  الاسم بالعربي: {$shiftTypeName}\n";
        echo "\n";
        echo "سيظهر في الصفحة:\n";
        echo "✅ {$shiftTypeName} ({$currentShift->shift_date->format('Y-m-d')})\n";
    } else {
        echo "⚪ لم توجد وردية - سيظهر 'أي وردية'\n";
    }
}
