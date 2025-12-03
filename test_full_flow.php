<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Login as user 1
Auth::loginUsingId(1);

echo "=== Test Full Flow ===\n\n";

// Get a test material batch
$batch = DB::table('material_batches')->first();

if (!$batch) {
    echo "No material batch found\n";
    exit;
}

echo "Using batch: {$batch->batch_code}\n";

// Simulate waste check
$inputWeight = 100; // Material weight
$outputWeight = 70;  // Output = 30% waste

echo "Input: {$inputWeight}kg, Output: {$outputWeight}kg\n";
echo "Waste: " . ($inputWeight - $outputWeight) . "kg (" . (($inputWeight - $outputWeight)/$inputWeight*100) . "%)\n\n";

$wasteCheck = \App\Services\WasteCheckService::checkAndSuspend(
    stageNumber: 1,
    batchBarcode: $batch->batch_code,
    batchId: $batch->id,
    inputWeight: $inputWeight,
    outputWeight: $outputWeight
);

echo "Waste Check Result:\n";
echo "  suspended: " . ($wasteCheck['suspended'] ? 'true' : 'false') . "\n";
echo "  suspension_id: " . ($wasteCheck['suspension_id'] ?? 'null') . "\n";

if (isset($wasteCheck['data'])) {
    echo "  waste_percentage: " . $wasteCheck['data']['waste_percentage'] . "%\n";
    echo "  exceeded: " . ($wasteCheck['data']['exceeded'] ? 'true' : 'false') . "\n";
    echo "  should_suspend: " . ($wasteCheck['data']['should_suspend'] ? 'true' : 'false') . "\n";
}

// Check if recordStatus would be pending_approval
$recordStatus = $wasteCheck['suspended'] ? 'pending_approval' : 'created';
echo "\nRecord Status would be: {$recordStatus}\n";

if ($recordStatus === 'pending_approval') {
    echo "✅ Would return pending_approval response\n";
} else {
    echo "❌ Would NOT return pending_approval response\n";
}

echo "\n";
