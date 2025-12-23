<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Worker;
use App\Models\ShiftAssignment;
use Illuminate\Support\Facades\DB;

echo "=== فحص بيانات المستخدم والوردية ===\n\n";

$userId = 5;

// 1. معلومات المستخدم
$user = User::with('roleRelation')->find($userId);
if (!$user) {
    echo "❌ المستخدم غير موجود!\n";
    exit;
}

echo "📋 معلومات المستخدم:\n";
echo "   - الاسم: {$user->name}\n";
echo "   - البريد: {$user->email}\n";
echo "   - اسم المستخدم: {$user->username}\n";
echo "   - الدور: " . ($user->roleRelation ? $user->roleRelation->role_name : 'بدون دور') . "\n";
echo "   - كود الدور: " . ($user->roleRelation ? $user->roleRelation->role_code : 'N/A') . "\n\n";

// 2. معلومات العامل
$worker = Worker::where('user_id', $userId)->first();
if ($worker) {
    echo "👷 معلومات العامل:\n";
    echo "   - كود العامل: {$worker->worker_code}\n";
    echo "   - الوظيفة: {$worker->position}\n";
    echo "   - تفضيل الوردية: {$worker->shift_preference}\n";
    echo "   - نشط: " . ($worker->is_active ? 'نعم' : 'لا') . "\n\n";
} else {
    echo "ℹ️  المستخدم ليس له ملف عامل\n\n";
}

// 3. الورديات المسندة للمستخدم
echo "📅 الورديات المسندة:\n\n";

// البحث في worker_ids
$shiftsWithWorker = ShiftAssignment::whereJsonContains('worker_ids', $userId)
    ->orderBy('shift_date', 'desc')
    ->get();

// البحث كمشرف
$shiftsAsSupervisor = ShiftAssignment::where('supervisor_id', $userId)
    ->orderBy('shift_date', 'desc')
    ->get();

if ($shiftsWithWorker->isEmpty() && $shiftsAsSupervisor->isEmpty()) {
    echo "   ❌ لا توجد ورديات مسندة لهذا المستخدم\n\n";
} else {
    if ($shiftsWithWorker->isNotEmpty()) {
        echo "   🔹 كعامل في الوردية:\n";
        foreach ($shiftsWithWorker as $shift) {
            $statusIcon = match($shift->status) {
                'active' => '🟢',
                'scheduled' => '🔵',
                'completed' => '⚫',
                'cancelled' => '🔴',
                default => '⚪'
            };
            
            $shiftType = $shift->shift_type === 'morning' ? 'صباحية' : 'مسائية';
            echo "      {$statusIcon} [{$shift->shift_code}] {$shift->shift_date->format('Y-m-d')} - {$shiftType}\n";
            echo "         الحالة: {$shift->status}\n";
            echo "         الوقت: {$shift->start_time} - {$shift->end_time}\n";
            
            // عرض جميع العمال في الوردية
            $workerIds = $shift->worker_ids ?? [];
            echo "         عدد العمال: " . count($workerIds) . "\n";
            
            if (count($workerIds) > 0) {
                $workers = User::whereIn('id', $workerIds)->get();
                echo "         العمال: ";
                echo $workers->pluck('name')->implode(', ') . "\n";
            }
            echo "\n";
        }
    }
    
    if ($shiftsAsSupervisor->isNotEmpty()) {
        echo "   🔹 كمشرف:\n";
        foreach ($shiftsAsSupervisor as $shift) {
            $statusIcon = match($shift->status) {
                'active' => '🟢',
                'scheduled' => '🔵',
                'completed' => '⚫',
                'cancelled' => '🔴',
                default => '⚪'
            };
            
            $shiftType = $shift->shift_type === 'morning' ? 'صباحية' : 'مسائية';
            echo "      {$statusIcon} [{$shift->shift_code}] {$shift->shift_date->format('Y-m-d')} - {$shiftType}\n";
            echo "         الحالة: {$shift->status}\n\n";
        }
    }
}

// 4. الوردية الحالية لليوم
echo "🎯 الوردية الحالية لليوم (" . now()->format('Y-m-d') . "):\n";

$todayShift = ShiftAssignment::where(function($query) use ($userId) {
    $query->whereJsonContains('worker_ids', $userId)
          ->orWhere('supervisor_id', $userId);
})
->whereIn('status', ['active', 'scheduled'])
->whereDate('shift_date', now()->toDateString())
->first();

if ($todayShift) {
    $shiftType = $todayShift->shift_type === 'morning' ? 'صباحية' : 'مسائية';
    echo "   ✅ لديه وردية اليوم\n";
    echo "   - الكود: {$todayShift->shift_code}\n";
    echo "   - النوع: {$shiftType}\n";
    echo "   - الحالة: {$todayShift->status}\n";
    echo "   - الوقت: {$todayShift->start_time} - {$todayShift->end_time}\n";
} else {
    echo "   ❌ لا توجد وردية نشطة لليوم\n";
}

echo "\n=== انتهى الفحص ===\n";
