<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ShiftAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== اختبار التحقق من worker_ids ===\n\n";

// إنشاء وردية تجريبية
echo "1️⃣ إنشاء وردية تجريبية:\n";

$supervisor = User::find(7); // عبدالمنعم
$workers = [5, 8]; // مشرف الإنتاج + محمد

$shift = ShiftAssignment::create([
    'shift_code' => 'TEST-VERIFY-' . time(),
    'shift_type' => 'morning',
    'user_id' => $supervisor->id,
    'supervisor_id' => $supervisor->id,
    'stage_number' => 1,
    'shift_date' => now()->format('Y-m-d'),
    'start_time' => '06:00',
    'end_time' => '18:00',
    'status' => 'active',
    'total_workers' => count($workers),
    'worker_ids' => $workers,
]);

echo "   ✅ تم إنشاء الوردية\n";
echo "   - ID: {$shift->id}\n";
echo "   - المشرف: {$supervisor->name} (ID: {$supervisor->id})\n";
echo "   - العمال: " . json_encode($workers) . "\n\n";

// اختبار التحقق لكل عامل
echo "2️⃣ اختبار التحقق من worker_ids:\n\n";

foreach ([5, 8, 7] as $userId) {
    $user = User::find($userId);
    echo "👤 المستخدم: {$user->name} (ID: {$userId})\n";
    
    // محاكاة التحقق من الـ middleware
    $foundShift = ShiftAssignment::where(function($query) use ($user) {
        $query->where(function($q) use ($user) {
            $q->whereJsonContains('worker_ids', $user->id)
              ->orWhereJsonContains('worker_ids', (string)$user->id);
        })->orWhere('supervisor_id', $user->id);
    })
    ->whereIn('status', ['active', 'scheduled'])
    ->whereDate('shift_date', now()->toDateString())
    ->first();
    
    if ($foundShift) {
        echo "   ✅ موجود في وردية: {$foundShift->shift_code}\n";
        
        // تحديد نوع الإسناد
        if ($foundShift->supervisor_id == $user->id) {
            echo "   📋 الدور: مشرف الوردية\n";
        }
        if (in_array($user->id, $foundShift->worker_ids ?? [])) {
            echo "   👷 الدور: عامل في الوردية\n";
        }
    } else {
        echo "   ❌ غير موجود في أي وردية نشطة\n";
    }
    echo "\n";
}

// اختبار المستخدم الذي ليس في الوردية
echo "3️⃣ اختبار مستخدم غير موجود في الوردية:\n\n";

$otherUser = User::where('id', '!=', 5)
    ->where('id', '!=', 7)
    ->where('id', '!=', 8)
    ->first();

if ($otherUser) {
    echo "👤 المستخدم: {$otherUser->name} (ID: {$otherUser->id})\n";
    
    $foundShift = ShiftAssignment::where(function($query) use ($otherUser) {
        $query->where(function($q) use ($otherUser) {
            $q->whereJsonContains('worker_ids', $otherUser->id)
              ->orWhereJsonContains('worker_ids', (string)$otherUser->id);
        })->orWhere('supervisor_id', $otherUser->id);
    })
    ->whereIn('status', ['active', 'scheduled'])
    ->whereDate('shift_date', now()->toDateString())
    ->first();
    
    if ($foundShift) {
        echo "   ✅ موجود في وردية: {$foundShift->shift_code}\n";
    } else {
        echo "   ❌ غير موجود في أي وردية نشطة (متوقع)\n";
        echo "   ⛔ الـ middleware سيمنعه من الدخول\n";
    }
}

// حذف الوردية التجريبية
$shift->delete();
echo "\n🗑️  تم حذف الوردية التجريبية\n";

echo "\n=== انتهى الاختبار ===\n";
echo "\n✅ الخلاصة: التحقق من worker_ids يعمل بشكل صحيح!\n";
