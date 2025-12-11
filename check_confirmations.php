<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== آخر 10 تأكيدات إنتاج ===\n\n";

$confirmations = \App\Models\ProductionConfirmation::with(['deliveryNote', 'batch'])
    ->latest()
    ->take(10)
    ->get();

foreach ($confirmations as $c) {
    echo sprintf(
        "ID: %d | actual_received_quantity: %s | DeliveryNote Qty: %s | Batch Initial: %s | Created: %s\n",
        $c->id,
        $c->actual_received_quantity ?? 'NULL',
        $c->deliveryNote?->quantity ?? 'NULL',
        $c->batch?->initial_quantity ?? 'NULL',
        $c->created_at->format('Y-m-d H:i')
    );
}

echo "\n=== فحص MaterialDetails ===\n\n";

$materialDetails = \App\Models\MaterialDetail::latest()->take(5)->get();

foreach ($materialDetails as $md) {
    echo sprintf(
        "ID: %d | Material: %s | Warehouse: %d | Quantity: %s | Updated: %s\n",
        $md->id,
        $md->material?->name ?? 'N/A',
        $md->warehouse_id,
        $md->quantity,
        $md->updated_at->format('Y-m-d H:i')
    );
}
