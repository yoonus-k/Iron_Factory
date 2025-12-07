<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Stage 1 Waste Check System ===\n\n";

// Simulate waste check
$batchBarcode = 'TEST-2025-999';
$batchId = 999;
$inputWeight = 100; // 100 kg input
$outputWeight = 70; // 70 kg output = 30% waste (exceeds 3% limit)

echo "Test Parameters:\n";
echo "  Batch: $batchBarcode\n";
echo "  Input: {$inputWeight} kg\n";
echo "  Output: {$outputWeight} kg\n";
echo "  Waste: " . ($inputWeight - $outputWeight) . " kg\n";
echo "  Waste %: " . (($inputWeight - $outputWeight) / $inputWeight * 100) . "%\n\n";

// Call WasteCheckService
try {
    $result = \App\Services\WasteCheckService::checkAndSuspend(
        stageNumber: 1,
        batchBarcode: $batchBarcode,
        batchId: $batchId,
        inputWeight: $inputWeight,
        outputWeight: $outputWeight
    );
    
    echo "Result:\n";
    echo "  Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
    echo "  Suspended: " . ($result['suspended'] ? 'Yes' : 'No') . "\n";
    
    if (isset($result['suspension_id'])) {
        echo "  Suspension ID: " . $result['suspension_id'] . "\n";
        
        // Check the created suspension
        $suspension = \App\Models\StageSuspension::find($result['suspension_id']);
        if ($suspension) {
            echo "\nCreated Suspension:\n";
            echo "  ID: " . $suspension->id . "\n";
            echo "  Stage: " . $suspension->stage_number . "\n";
            echo "  Batch: " . $suspension->batch_barcode . "\n";
            echo "  Waste: " . $suspension->waste_percentage . "%\n";
            echo "  Status: " . $suspension->status . "\n";
        }
    }
    
    if (isset($result['data'])) {
        echo "\nWaste Data:\n";
        echo "  Exceeded: " . ($result['data']['exceeded'] ? 'Yes' : 'No') . "\n";
        echo "  Waste %: " . $result['data']['waste_percentage'] . "%\n";
        echo "  Allowed %: " . $result['data']['allowed_percentage'] . "%\n";
        echo "  Should Alert: " . ($result['data']['should_alert'] ? 'Yes' : 'No') . "\n";
        echo "  Should Suspend: " . ($result['data']['should_suspend'] ? 'Yes' : 'No') . "\n";
    }
    
    echo "\nMessage: " . $result['message'] . "\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
