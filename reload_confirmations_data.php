<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\ProductionConfirmation;

echo "=== إعادة تحميل البيانات للConfirmations المعلقة ===\n\n";

// الحصول على جميع الconfirmations المعلقة التي لها stage_record_id
$confirmations = ProductionConfirmation::where('status', 'pending')
    ->whereNotNull('stage_record_id')
    ->whereNotNull('stage_type')
    ->get();

echo "عدد الconfirmations المعلقة مع stage_record: {$confirmations->count()}\n\n";

foreach ($confirmations as $confirmation) {
    echo "📋 Confirmation ID: {$confirmation->id}\n";
    echo "   - Barcode: {$confirmation->barcode}\n";
    echo "   - Stage Type: {$confirmation->stage_type}\n";
    echo "   - Stage Record ID: {$confirmation->stage_record_id}\n";
    
    $metadata = $confirmation->metadata ?? [];
    echo "   - الوزن الحالي في metadata: " . ($metadata['stage_weight'] ?? 'غير محدد') . "\n";
    
    // إعادة تحميل البيانات
    $result = $confirmation->loadStageRecord();
    
    if ($result) {
        $newMetadata = $confirmation->metadata ?? [];
        echo "   ✅ تم تحديث الوزن إلى: " . ($newMetadata['stage_weight'] ?? 'غير محدد') . " كجم\n";
    } else {
        echo "   ⚠️ فشل تحميل البيانات\n";
    }
    
    echo "\n";
}

echo "✅ اكتمل التحديث!\n";
echo "\nالآن اعمل Ctrl+F5 على لوحة التحكم لرؤية التحديثات.\n";
