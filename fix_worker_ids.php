<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ShiftAssignment;
use Illuminate\Support\Facades\DB;

echo "=== تحديث worker_ids من strings إلى integers ===\n\n";

$shifts = ShiftAssignment::all();
$updated = 0;
$skipped = 0;

foreach ($shifts as $shift) {
    $workerIds = $shift->worker_ids;
    
    if (is_array($workerIds) && count($workerIds) > 0) {
        // Check if any value is a string
        $hasStrings = false;
        foreach ($workerIds as $id) {
            if (is_string($id)) {
                $hasStrings = true;
                break;
            }
        }
        
        if ($hasStrings) {
            // Convert all to integers
            $newWorkerIds = array_map('intval', $workerIds);
            $shift->worker_ids = $newWorkerIds;
            $shift->save();
            
            echo "✅ [{$shift->id}] {$shift->shift_code}\n";
            echo "   قبل: " . json_encode($workerIds) . "\n";
            echo "   بعد: " . json_encode($newWorkerIds) . "\n\n";
            $updated++;
        } else {
            $skipped++;
        }
    } else {
        $skipped++;
    }
}

echo "\n=== النتيجة ===\n";
echo "✅ تم التحديث: {$updated} وردية\n";
echo "⏭️  تم التخطي: {$skipped} وردية (بالفعل integers أو فارغة)\n";
echo "\n=== انتهى ===\n";
