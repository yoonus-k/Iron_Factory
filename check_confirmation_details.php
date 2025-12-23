<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== تفاصيل التأكيدات للباركود RW-2025-050 ===\n\n";

$deliveryNote = DB::table('delivery_notes')->where('production_barcode', 'RW-2025-050')->first();

if ($deliveryNote) {
    echo "DeliveryNote ID: {$deliveryNote->id}\n";
    echo "Production Barcode: {$deliveryNote->production_barcode}\n";
    echo "Transfer Status: " . ($deliveryNote->transfer_status ?? 'null') . "\n\n";
    
    $confirmations = DB::table('production_confirmations')
        ->where('delivery_note_id', $deliveryNote->id)
        ->get();
    
    echo "عدد التأكيدات: " . $confirmations->count() . "\n\n";
    
    foreach ($confirmations as $conf) {
        echo "--- تأكيد رقم {$conf->id} ---\n";
        echo "Batch ID: {$conf->batch_id}\n";
        echo "Stage Code: {$conf->stage_code}\n";
        echo "Status: {$conf->status}\n";
        echo "Assigned To: " . ($conf->assigned_to ?? 'null') . "\n";
        echo "Confirmed By: " . ($conf->confirmed_by ?? 'null') . "\n";
        echo "Confirmed At: " . ($conf->confirmed_at ?? 'null') . "\n";
        echo "Created At: {$conf->created_at}\n";
        
        // البحث عن batch_id
        if ($conf->batch_id) {
            $batch = DB::table('material_batches')->where('id', $conf->batch_id)->first();
            if ($batch) {
                echo "Batch Code: " . ($batch->batch_code ?? 'null') . "\n";
                echo "Batch Status: {$batch->status}\n";
                echo "Batch Stage: " . ($batch->stage ?? 'null') . "\n";
            }
        }
        echo "\n";
    }
}
