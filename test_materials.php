<?php
// تحميل Laravel
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// حل الـ Container
$db = $app['db'];

// اختبر البيانات
$count = $db->table('material_details')->count();
echo "Total Material Details: $count\n";

$withQuantity = $db->table('material_details')->where('quantity', '>', 0)->count();
echo "Material Details with Quantity > 0: $withQuantity\n";

// اعرض أول 5 صفوف
$data = $db->table('material_details')
    ->where('quantity', '>', 0)
    ->limit(5)
    ->select('id', 'warehouse_id', 'material_id', 'quantity')
    ->get();

echo "\nFirst 5 Materials:\n";
foreach ($data as $item) {
    echo "ID: {$item->id}, Warehouse: {$item->warehouse_id}, Material: {$item->material_id}, Qty: {$item->quantity}\n";
}

// اختبر الـ groupBy
$grouped = $db->table('material_details')
    ->where('quantity', '>', 0)
    ->select('warehouse_id')
    ->distinct()
    ->count();

echo "\nWarehouse Groups with Materials: $grouped\n";
