<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== اختبار الحل الجديد ===\n\n";

// فحص آخر DeliveryNote
$dn = \App\Models\DeliveryNote::latest()->first();

if ($dn) {
    echo "📦 أذن التسليم الأخير:\n";
    echo "   ID: {$dn->id}\n";
    echo "   material_id: {$dn->material_id}\n";
    echo "   warehouse_id: {$dn->warehouse_id}\n";
    echo "   material_detail_id: " . ($dn->material_detail_id ?? 'NULL') . "\n\n";
    
    // محاولة البحث عن MaterialDetail
    $materialDetail = $dn->materialDetail;
    
    if (!$materialDetail && $dn->material_id && $dn->warehouse_id) {
        echo "🔍 البحث عن MaterialDetail عبر material_id و warehouse_id...\n";
        $materialDetail = \App\Models\MaterialDetail::where('material_id', $dn->material_id)
            ->where('warehouse_id', $dn->warehouse_id)
            ->first();
        
        if ($materialDetail) {
            echo "✅ تم العثور على MaterialDetail!\n";
            echo "   ID: {$materialDetail->id}\n";
            echo "   Quantity: {$materialDetail->quantity} كجم\n";
            echo "   Warehouse: {$materialDetail->warehouse_id}\n";
        } else {
            echo "❌ لم يتم العثور على MaterialDetail!\n";
        }
    } elseif ($materialDetail) {
        echo "✅ MaterialDetail موجود بالفعل:\n";
        echo "   ID: {$materialDetail->id}\n";
        echo "   Quantity: {$materialDetail->quantity} كجم\n";
    }
} else {
    echo "❌ لا توجد أذونات تسليم!\n";
}
