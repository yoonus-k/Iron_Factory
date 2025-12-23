<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$barcode = 'RW-2025-050';

echo "=== فحص البيانات للباركود $barcode ===\n\n";

// البحث عن batch في material_batches
echo "--- material_batches ---\n";
$batch = DB::table('material_batches')
    ->where('batch_code', $barcode)
    ->first();

if ($batch) {
    print_r($batch);
    
    echo "\n--- production_confirmations for batch {$batch->id} ---\n";
    $confirmation = DB::table('production_confirmations')
        ->where('batch_id', $batch->id)
        ->where('stage_code', 'stage_1')
        ->first();
    
    if ($confirmation) {
        print_r($confirmation);
    } else {
        echo "لا يوجد تأكيد\n";
    }
} else {
    echo "لا يوجد batch\n";
}

// البحث في coil_transfers
echo "\n--- coil_transfers ---\n";
$coilTransfer = DB::table('coil_transfers')
    ->where('production_barcode', $barcode)
    ->first();

if ($coilTransfer) {
    print_r($coilTransfer);
} else {
    echo "لا يوجد coil_transfer\n";
}
