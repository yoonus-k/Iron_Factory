<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== اختبار WasteCheckService ===\n\n";

// محاكاة بيانات من المرحلة 1
$stageNumber = 1;
$batchBarcode = 'RW-2025-030';
$batchId = 1;
$inputWeight = 100; // الوزن المادي (بدون الاستاند)
$outputWeight = 75; // الوزن الصافي
$waste = $inputWeight - $outputWeight; // 25 كجم

echo "البيانات المدخلة:\n";
echo "المرحلة: {$stageNumber}\n";
echo "باركود المادة: {$batchBarcode}\n";
echo "الوزن المادي: {$inputWeight} كجم\n";
echo "الوزن الصافي: {$outputWeight} كجم\n";
echo "الهدر: {$waste} كجم\n";
echo "نسبة الهدر: " . round(($waste / $inputWeight) * 100, 2) . "%\n\n";

echo "استدعاء WasteCheckService::checkAndSuspend...\n\n";

$result = App\Services\WasteCheckService::checkAndSuspend(
    stageNumber: $stageNumber,
    batchBarcode: $batchBarcode,
    batchId: $batchId,
    inputWeight: $inputWeight,
    outputWeight: $outputWeight
);

echo "النتيجة:\n";
echo "Success: " . ($result['success'] ? 'true' : 'false') . "\n";
echo "Suspended: " . ($result['suspended'] ? 'true ✅' : 'false ❌') . "\n";
echo "Message: {$result['message']}\n\n";

if (isset($result['data'])) {
    echo "تفاصيل البيانات:\n";
    foreach ($result['data'] as $key => $value) {
        if (is_numeric($value)) {
            echo "  {$key}: {$value}\n";
        } else {
            echo "  {$key}: " . json_encode($value) . "\n";
        }
    }
}

echo "\n=== انتهى الاختبار ===\n";
