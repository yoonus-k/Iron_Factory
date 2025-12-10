<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test MaterialDetail->reduceOutgoingQuantity() ===\n\n";

$md = \App\Models\MaterialDetail::find(3);

if ($md) {
    $beforeQty = $md->quantity;
    echo sprintf("Quantity before: %s كجم\n", $beforeQty);
    
    // اختبار الخصم بـ 1 كجم
    try {
        $md->reduceOutgoingQuantity(1);
        echo "✅ تم الخصم بنجاح!\n";
        
        $md->refresh();
        $afterQty = $md->quantity;
        echo sprintf("Quantity after: %s كجم\n", $afterQty);
        echo sprintf("Difference: %s كجم\n", $beforeQty - $afterQty);
        
        // إرجاع الكمية
        $md->quantity += 1;
        $md->save();
        echo "\n✅ تم إرجاع الكمية للاختبار\n";
        
    } catch (\Exception $e) {
        echo "❌ خطأ: " . $e->getMessage() . "\n";
    }
} else {
    echo "MaterialDetail ID:3 not found!\n";
}
