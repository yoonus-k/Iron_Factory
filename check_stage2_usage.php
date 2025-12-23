<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$stage2 = \Illuminate\Support\Facades\DB::table('stage2_processed')
    ->where('parent_barcode', 'ST1-2025-007')
    ->orWhere('barcode', 'LIKE', 'ST1-2025-007%')
    ->get();

echo "\n=== نتائج البحث في stage2_processed ===\n";
echo "عدد النتائج: " . $stage2->count() . "\n";

if ($stage2->isNotEmpty()) {
    echo "\n⚠️ تم استخدام الباركود بالفعل في المرحلة الثانية:\n";
    foreach ($stage2 as $s) {
        echo "  - Barcode: {$s->barcode}\n";
        echo "    Parent: {$s->parent_barcode}\n";
        echo "    الوزن: {$s->remaining_weight} كجم\n";
        echo "    Created: {$s->created_at}\n\n";
    }
} else {
    echo "\n✅ لم يتم استخدام الباركود في المرحلة الثانية بعد\n";
    echo "✅ الvalidation سيعمل عند المحاولة التالية\n";
}
