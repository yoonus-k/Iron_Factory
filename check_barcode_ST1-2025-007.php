<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\ProductionConfirmation;

echo "=== فحص الباركود ST1-2025-007 ===\n\n";

// 1. البحث في stage1_stands
echo "1️⃣ البحث في stage1_stands:\n";
$stage1 = DB::table('stage1_stands')->where('barcode', 'ST1-2025-007')->first();
if ($stage1) {
    echo "   ✅ موجود في stage1_stands\n";
    echo "   - ID: {$stage1->id}\n";
    echo "   - الوزن المتبقي: {$stage1->remaining_weight} كجم\n";
    echo "   - الحالة: {$stage1->status}\n";
} else {
    echo "   ❌ غير موجود في stage1_stands\n";
}
echo "\n";

// 2. البحث في production_confirmations
echo "2️⃣ البحث في production_confirmations:\n";
$confirmations = DB::table('production_confirmations')
    ->where('barcode', 'ST1-2025-007')
    ->get();

if ($confirmations->isEmpty()) {
    echo "   ❌ لا توجد تأكيدات لهذا الباركود\n";
} else {
    foreach ($confirmations as $conf) {
        echo "   ✅ تأكيد رقم {$conf->id}:\n";
        echo "      - الحالة: {$conf->status}\n";
        echo "      - المسند إليه: User #{$conf->assigned_to}\n";
        echo "      - النوع: {$conf->confirmation_type}\n";
        echo "      - stage_type: {$conf->stage_type}\n";
        echo "      - stage_record_id: {$conf->stage_record_id}\n";
        
        $metadata = json_decode($conf->metadata, true);
        if ($metadata) {
            echo "      - metadata:\n";
            echo "         * stage_name: " . ($metadata['stage_name'] ?? 'N/A') . "\n";
            echo "         * stage_weight: " . ($metadata['stage_weight'] ?? 'N/A') . "\n";
            echo "         * barcode: " . ($metadata['barcode'] ?? 'N/A') . "\n";
        }
        echo "\n";
    }
}
echo "\n";

// 3. البحث في stage2_processed
echo "3️⃣ البحث في stage2_processed:\n";
$stage2 = DB::table('stage2_processed')
    ->where('parent_barcode', 'ST1-2025-007')
    ->orWhere('barcode', 'LIKE', 'ST1-2025-007%')
    ->get();

if ($stage2->isEmpty()) {
    echo "   ⚠️ لم يتم استخدام هذا الباركود في المرحلة الثانية بعد\n";
} else {
    echo "   ✅ تم استخدامه في المرحلة الثانية:\n";
    foreach ($stage2 as $s2) {
        echo "      - باركود المرحلة 2: {$s2->barcode}\n";
        echo "      - الوزن: {$s2->remaining_weight} كجم\n";
    }
}
echo "\n";

// 4. تجربة loadStageRecord
if (!$confirmations->isEmpty()) {
    echo "4️⃣ اختبار loadStageRecord():\n";
    $testConf = ProductionConfirmation::find($confirmations->first()->id);
    if ($testConf) {
        echo "   - قبل loadStageRecord:\n";
        echo "     * metadata: " . json_encode($testConf->metadata) . "\n";
        
        $result = $testConf->loadStageRecord();
        
        echo "   - بعد loadStageRecord:\n";
        echo "     * metadata: " . json_encode($testConf->metadata) . "\n";
        echo "     * النتيجة: " . ($result ? "✅ نجح" : "❌ فشل") . "\n";
    }
}
