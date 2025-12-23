<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Worker;
use App\Models\ShiftAssignment;
use Illuminate\Support\Facades\DB;

echo "=== إدارة الورديات - إضافة/تعديل/حذف ===\n\n";

// يمكنك تغيير هذه القيم
$userId = 5; // معرف المستخدم
$action = 'add'; // add, remove, list

if ($argc > 1) {
    $action = $argv[1];
}
if ($argc > 2) {
    $userId = (int)$argv[2];
}

$user = User::find($userId);
if (!$user) {
    echo "❌ المستخدم غير موجود!\n";
    exit;
}

echo "👤 المستخدم: {$user->name} (ID: {$userId})\n\n";

switch ($action) {
    case 'list':
        // عرض جميع الورديات
        echo "📋 جميع الورديات المتاحة:\n\n";
        
        $shifts = ShiftAssignment::with('supervisor')
            ->whereDate('shift_date', '>=', now()->toDateString())
            ->orderBy('shift_date')
            ->orderBy('shift_type')
            ->get();
        
        if ($shifts->isEmpty()) {
            echo "   ❌ لا توجد ورديات متاحة\n";
        } else {
            foreach ($shifts as $shift) {
                $shiftType = $shift->shift_type === 'morning' ? 'صباحية' : 'مسائية';
                $status = match($shift->status) {
                    'active' => '🟢 نشطة',
                    'scheduled' => '🔵 مجدولة',
                    'completed' => '⚫ منتهية',
                    'cancelled' => '🔴 ملغاة',
                    default => '⚪ ' . $shift->status
                };
                
                $workerCount = count($shift->worker_ids ?? []);
                $hasUser = in_array($userId, $shift->worker_ids ?? []);
                $marker = $hasUser ? '✓ ' : '  ';
                
                echo "{$marker}[{$shift->id}] {$shift->shift_code} - {$shift->shift_date->format('Y-m-d')} - {$shiftType}\n";
                echo "      {$status} | العمال: {$workerCount} | {$shift->start_time}-{$shift->end_time}\n";
                if ($hasUser) {
                    echo "      ✅ المستخدم موجود في هذه الوردية\n";
                }
                echo "\n";
            }
        }
        break;
        
    case 'add':
        // إضافة المستخدم لوردية
        if ($argc < 4) {
            echo "❌ يجب تحديد معرف الوردية!\n";
            echo "الاستخدام: php manage_shift.php add {user_id} {shift_id}\n";
            echo "\nمثال: php manage_shift.php add 5 1\n\n";
            echo "لعرض الورديات المتاحة: php manage_shift.php list\n";
            exit;
        }
        
        $shiftId = (int)$argv[3];
        $shift = ShiftAssignment::find($shiftId);
        
        if (!$shift) {
            echo "❌ الوردية غير موجودة!\n";
            exit;
        }
        
        $workerIds = $shift->worker_ids ?? [];
        
        if (in_array($userId, $workerIds)) {
            echo "ℹ️  المستخدم موجود بالفعل في هذه الوردية\n";
        } else {
            $workerIds[] = $userId;
            $shift->update([
                'worker_ids' => $workerIds,
                'total_workers' => count($workerIds)
            ]);
            
            echo "✅ تم إضافة المستخدم للوردية بنجاح!\n";
            echo "   - الوردية: {$shift->shift_code}\n";
            echo "   - التاريخ: {$shift->shift_date->format('Y-m-d')}\n";
            echo "   - النوع: " . ($shift->shift_type === 'morning' ? 'صباحية' : 'مسائية') . "\n";
        }
        break;
        
    case 'remove':
        // إزالة المستخدم من وردية
        if ($argc < 4) {
            echo "❌ يجب تحديد معرف الوردية!\n";
            echo "الاستخدام: php manage_shift.php remove {user_id} {shift_id}\n";
            exit;
        }
        
        $shiftId = (int)$argv[3];
        $shift = ShiftAssignment::find($shiftId);
        
        if (!$shift) {
            echo "❌ الوردية غير موجودة!\n";
            exit;
        }
        
        $workerIds = $shift->worker_ids ?? [];
        
        if (!in_array($userId, $workerIds)) {
            echo "ℹ️  المستخدم غير موجود في هذه الوردية\n";
        } else {
            $workerIds = array_values(array_filter($workerIds, fn($id) => $id != $userId));
            $shift->update([
                'worker_ids' => $workerIds,
                'total_workers' => count($workerIds)
            ]);
            
            echo "✅ تم إزالة المستخدم من الوردية بنجاح!\n";
        }
        break;
        
    case 'create':
        // إنشاء وردية جديدة
        echo "📝 إنشاء وردية جديدة:\n\n";
        
        $shiftDate = $argc > 3 ? $argv[3] : now()->format('Y-m-d');
        $shiftType = $argc > 4 ? $argv[4] : 'morning';
        
        $lastShift = ShiftAssignment::latest('id')->first();
        $nextNumber = $lastShift ? ((int)substr($lastShift->shift_code, 3) + 1) : 1;
        $shiftCode = 'SH-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        
        $startTime = $shiftType === 'morning' ? '06:00' : '18:00';
        $endTime = $shiftType === 'morning' ? '18:00' : '06:00';
        
        $shift = ShiftAssignment::create([
            'shift_code' => $shiftCode,
            'shift_type' => $shiftType,
            'user_id' => $userId,
            'supervisor_id' => $userId,
            'shift_date' => $shiftDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'scheduled',
            'worker_ids' => [$userId],
            'total_workers' => 1,
        ]);
        
        echo "✅ تم إنشاء الوردية بنجاح!\n";
        echo "   - الكود: {$shift->shift_code}\n";
        echo "   - التاريخ: {$shiftDate}\n";
        echo "   - النوع: " . ($shiftType === 'morning' ? 'صباحية' : 'مسائية') . "\n";
        echo "   - المشرف: {$user->name}\n";
        break;
        
    default:
        echo "❌ أمر غير معروف!\n\n";
        echo "الأوامر المتاحة:\n";
        echo "  list              - عرض جميع الورديات\n";
        echo "  add {user} {shift}    - إضافة مستخدم لوردية\n";
        echo "  remove {user} {shift} - إزالة مستخدم من وردية\n";
        echo "  create {date} {type}  - إنشاء وردية جديدة\n\n";
        echo "أمثلة:\n";
        echo "  php manage_shift.php list\n";
        echo "  php manage_shift.php add 5 1\n";
        echo "  php manage_shift.php create 2025-12-21 morning\n";
}

echo "\n=== انتهى ===\n";
