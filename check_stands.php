<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Stand;

echo "=== فحص جدول الاستاندات ===\n\n";

$count = Stand::count();
echo "إجمالي عدد الاستاندات: {$count}\n\n";

$unusedCount = Stand::where('status', 'unused')->count();
echo "عدد الاستاندات غير المستخدمة: {$unusedCount}\n\n";

if ($count > 0) {
    echo "أول 5 استاندات:\n";
    echo "================\n\n";
    
    $stands = Stand::take(5)->get();
    
    foreach ($stands as $stand) {
        echo "ID: {$stand->id}\n";
        echo "رقم الاستاند: {$stand->stand_number}\n";
        echo "الوزن: {$stand->weight} كجم\n";
        echo "الحالة: {$stand->status}\n";
        echo "نشط: " . ($stand->is_active ? 'نعم' : 'لا') . "\n";
        echo "عدد الاستخدامات: {$stand->usage_count}\n";
        echo "--------------------\n\n";
    }
} else {
    echo "⚠️ لا توجد بيانات في جدول stands!\n";
    echo "يجب إضافة استاندات أولاً من صفحة إدارة الاستاندات.\n";
}
