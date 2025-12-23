<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ShiftAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== فحص الورديات وكيفية التحقق من العمال ===\n\n";

// 1. عرض آخر 5 ورديات مع العمال
echo "📋 آخر 5 ورديات:\n\n";

$shifts = ShiftAssignment::latest('id')->take(5)->get();

foreach ($shifts as $shift) {
    $shiftType = $shift->shift_type === 'morning' ? 'صباحية' : 'مسائية';
    
    echo "🔹 [{$shift->id}] {$shift->shift_code} - {$shift->shift_date->format('Y-m-d')}\n";
    echo "   النوع: {$shiftType} | الحالة: {$shift->status}\n";
    echo "   المشرف: " . ($shift->supervisor ? $shift->supervisor->name : 'غير محدد') . "\n";
    
    // عرض worker_ids
    $workerIds = $shift->worker_ids;
    echo "   worker_ids: " . json_encode($workerIds) . "\n";
    echo "   نوع البيانات: " . gettype($workerIds) . "\n";
    
    if (is_array($workerIds) && count($workerIds) > 0) {
        echo "   عدد العمال: " . count($workerIds) . "\n";
        echo "   العمال:\n";
        
        $workers = User::whereIn('id', $workerIds)->get();
        foreach ($workers as $worker) {
            echo "      - [{$worker->id}] {$worker->name}\n";
        }
    } else {
        echo "   ⚠️  لا يوجد عمال مسندون\n";
    }
    echo "\n";
}

echo "\n=== كيفية التحقق من وجود عامل في وردية ===\n\n";

$userId = 5;
$user = User::find($userId);

if (!$user) {
    echo "❌ المستخدم غير موجود\n";
} else {
    echo "👤 التحقق من المستخدم: {$user->name} (ID: {$userId})\n\n";
    
    // الطريقة 1: whereJsonContains
    echo "1️⃣ استخدام whereJsonContains:\n";
    $shiftsMethod1 = ShiftAssignment::whereJsonContains('worker_ids', $userId)
        ->whereDate('shift_date', now()->toDateString())
        ->get();
    
    echo "   النتيجة: " . ($shiftsMethod1->count() > 0 ? "✅ موجود في {$shiftsMethod1->count()} وردية" : "❌ غير موجود") . "\n\n";
    
    // الطريقة 2: whereRaw with JSON_CONTAINS
    echo "2️⃣ استخدام JSON_CONTAINS:\n";
    $shiftsMethod2 = ShiftAssignment::whereRaw('JSON_CONTAINS(worker_ids, ?)', [json_encode($userId)])
        ->whereDate('shift_date', now()->toDateString())
        ->get();
    
    echo "   النتيجة: " . ($shiftsMethod2->count() > 0 ? "✅ موجود في {$shiftsMethod2->count()} وردية" : "❌ غير موجود") . "\n\n";
    
    // الطريقة 3: get all and check in PHP
    echo "3️⃣ الفحص في PHP:\n";
    $allShifts = ShiftAssignment::whereDate('shift_date', now()->toDateString())->get();
    $foundShifts = $allShifts->filter(function($shift) use ($userId) {
        $workerIds = $shift->worker_ids ?? [];
        return in_array($userId, $workerIds);
    });
    
    echo "   النتيجة: " . ($foundShifts->count() > 0 ? "✅ موجود في {$foundShifts->count()} وردية" : "❌ غير موجود") . "\n\n";
    
    // عرض الورديات الموجودة
    if ($shiftsMethod1->count() > 0) {
        echo "📅 الورديات التي يوجد فيها المستخدم:\n";
        foreach ($shiftsMethod1 as $shift) {
            echo "   - [{$shift->id}] {$shift->shift_code} - {$shift->shift_date->format('Y-m-d')}\n";
        }
    }
}

echo "\n=== اختبار middleware CheckActiveShift ===\n\n";

if ($user) {
    // محاكاة فحص الـ middleware
    $currentShift = ShiftAssignment::where(function($query) use ($userId) {
        $query->whereJsonContains('worker_ids', $userId)
              ->orWhere('supervisor_id', $userId);
    })
    ->whereIn('status', ['active', 'scheduled'])
    ->whereDate('shift_date', now()->toDateString())
    ->first();
    
    if ($currentShift) {
        echo "✅ الـ middleware سيسمح بالدخول\n";
        echo "   الوردية: {$currentShift->shift_code}\n";
        echo "   الحالة: {$currentShift->status}\n";
    } else {
        // التحقق من الدور
        if ($user->roleRelation && in_array($user->roleRelation->role_code, ['ADMIN', 'SUPER_ADMIN', 'SUPERVISOR', 'MANAGER'])) {
            echo "✅ الـ middleware سيسمح بالدخول (مشرف/إداري)\n";
            echo "   الدور: {$user->roleRelation->role_name}\n";
        } else {
            echo "❌ الـ middleware سيمنع الدخول\n";
            echo "   السبب: لا توجد وردية نشطة لليوم\n";
        }
    }
}

echo "\n=== انتهى الفحص ===\n";
