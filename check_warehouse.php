<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== فحص MaterialDetail ID:3 (Warehouse 1) ===\n\n";

$md = \App\Models\MaterialDetail::find(3);

if ($md) {
    echo sprintf("ID: %d\n", $md->id);
    echo sprintf("Material ID: %d\n", $md->material_id);
    echo sprintf("Warehouse ID: %d\n", $md->warehouse_id);
    echo sprintf("Quantity: %s كجم\n", $md->quantity);
    echo sprintf("Last Updated: %s\n", $md->updated_at->format('Y-m-d H:i:s'));
    
    echo "\n=== آخر 10 تحديثات على هذا السجل ===\n\n";
    
    // فحص آخر DeliveryNotes المرتبطة
    $deliveryNotes = \App\Models\DeliveryNote::where('material_detail_id', 3)
        ->latest()
        ->take(5)
        ->get();
    
    foreach ($deliveryNotes as $dn) {
        echo sprintf(
            "DeliveryNote ID: %d | Quantity: %s | quantity_used: %s | Type: %s | Created: %s\n",
            $dn->id,
            $dn->quantity,
            $dn->quantity_used ?? '0',
            $dn->type,
            $dn->created_at->format('Y-m-d H:i')
        );
    }
} else {
    echo "MaterialDetail ID:3 not found!\n";
}
