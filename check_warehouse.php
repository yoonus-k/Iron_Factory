<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== فحص جدول warehouse_transactions ===\n\n";

$count = DB::table('warehouse_transactions')->count();
echo "إجمالي عدد المعاملات: {$count}\n\n";

if ($count > 0) {
    echo "أول 10 معاملات:\n";
    echo "=================\n\n";
    
    $transactions = DB::table('warehouse_transactions')->take(10)->get();
    
    foreach ($transactions as $trans) {
        echo "ID: {$trans->id}\n";
        echo "رقم المعاملة (الباركود): {$trans->transaction_number}\n";
        echo "نوع المعاملة: {$trans->transaction_type}\n";
        echo "الكمية: {$trans->quantity}\n";
        echo "الوحدة: " . ($trans->unit ?? 'N/A') . "\n";
        echo "ملاحظات: " . ($trans->notes ?? 'N/A') . "\n";
        echo "--------------------\n\n";
    }
} else {
    echo "⚠️ لا توجد بيانات في جدول warehouse_transactions!\n";
    echo "يجب إضافة معاملات مخزنية أولاً.\n";
}
