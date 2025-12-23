<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\ShiftAssignment;

echo "=== بنية جدول shift_assignments ===\n\n";

$columns = DB::select('DESCRIBE shift_assignments');

echo "الأعمدة:\n";
foreach($columns as $col) {
    $null = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
    $default = $col->Default ? "DEFAULT: {$col->Default}" : '';
    echo sprintf("  %-25s %-20s %-10s %s\n", $col->Field, $col->Type, $null, $default);
}

echo "\n=== مثال على بيانات وردية ===\n\n";

$shift = ShiftAssignment::latest()->first();

if ($shift) {
    echo "آخر وردية (ID: {$shift->id}):\n";
    echo "  - shift_code: {$shift->shift_code}\n";
    echo "  - user_id: {$shift->user_id}\n";
    echo "  - supervisor_id: {$shift->supervisor_id}\n";
    echo "  - worker_ids: " . json_encode($shift->worker_ids) . "\n";
    echo "  - total_workers: {$shift->total_workers}\n\n";
    
    // شرح الفرق
    echo "📋 شرح الحقول:\n\n";
    echo "1️⃣ user_id و supervisor_id:\n";
    echo "   - هذان الحقلان يحفظان المشرف فقط (نفس القيمة)\n";
    echo "   - المشرف: " . ($shift->supervisor ? $shift->supervisor->name : 'غير محدد') . "\n\n";
    
    echo "2️⃣ worker_ids (JSON):\n";
    echo "   - هذا الحقل يحفظ العمال المسندين للوردية\n";
    echo "   - نوع البيانات: JSON array of integers\n";
    echo "   - القيمة الحالية: " . json_encode($shift->worker_ids) . "\n";
    
    if (!empty($shift->worker_ids)) {
        echo "   - عدد العمال: " . count($shift->worker_ids) . "\n";
        echo "   - العمال:\n";
        
        $workers = \App\Models\User::whereIn('id', $shift->worker_ids)->get();
        foreach ($workers as $worker) {
            echo "      * [{$worker->id}] {$worker->name}\n";
        }
    } else {
        echo "   - لا يوجد عمال مسندون\n";
    }
} else {
    echo "لا توجد ورديات\n";
}

echo "\n=== انتهى ===\n";
