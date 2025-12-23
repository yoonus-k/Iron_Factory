<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== هيكل جدول production_confirmations ===\n\n";
$columns = DB::select("DESCRIBE production_confirmations");
foreach ($columns as $column) {
    echo "{$column->Field} - {$column->Type}\n";
}

echo "\n=== البحث عن الباركود RW-2025-050 في جميع الجداول المحتملة ===\n\n";

// البحث في delivery_notes
echo "--- delivery_notes ---\n";
$deliveryNotes = DB::table('delivery_notes')
    ->where('production_barcode', 'RW-2025-050')
    ->select('id', 'production_barcode', 'transfer_status', 'created_at')
    ->get();
if ($deliveryNotes->count() > 0) {
    foreach ($deliveryNotes as $dn) {
        print_r($dn);
    }
} else {
    echo "لا توجد نتائج\n";
}

// البحث في production_confirmations بدون join
echo "\n--- production_confirmations ---\n";
$confirmations = DB::table('production_confirmations')
    ->select('*')
    ->get();
echo "عدد التأكيدات: " . $confirmations->count() . "\n";
if ($confirmations->count() > 0) {
    foreach ($confirmations as $conf) {
        echo "ID: {$conf->id}, Status: {$conf->status}, Batch ID: " . ($conf->batch_id ?? 'null') . ", DeliveryNote ID: {$conf->delivery_note_id}\n";
    }
}
