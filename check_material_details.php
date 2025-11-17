<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== فحص جدول material_details ===\n\n";

$count = DB::table('material_details')->count();
echo "إجمالي عدد السجلات: {$count}\n\n";

if ($count > 0) {
    echo "أول 10 سجلات:\n";
    echo "=================\n\n";
    
    $materials = DB::table('material_details')->take(10)->get();
    
    foreach ($materials as $mat) {
        echo "ID: {$mat->id}\n";
        echo "Material ID: " . ($mat->material_id ?? 'N/A') . "\n";
        echo "Transaction Number: " . ($mat->transaction_number ?? 'N/A') . "\n";
        echo "الكمية: " . ($mat->quantity ?? 'N/A') . "\n";
        echo "الوحدة: " . ($mat->unit ?? 'N/A') . "\n";
        echo "ملاحظات: " . ($mat->notes ?? 'N/A') . "\n";
        
        // عرض جميع الأعمدة المتاحة
        echo "الأعمدة المتاحة: ";
        foreach ($mat as $key => $value) {
            echo "$key, ";
        }
        echo "\n--------------------\n\n";
        break; // عرض سجل واحد فقط لمعرفة البنية
    }
} else {
    echo "⚠️ لا توجد بيانات في جدول material_details!\n";
}
