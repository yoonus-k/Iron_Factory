<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ProductionConfirmation;

echo "=== اختبار confirm() للباركود المعاد إسناده ===\n\n";

// الحصول على confirmation معلق للباركود ST1-2025-007
$confirmation = ProductionConfirmation::where('barcode', 'ST1-2025-007')
    ->where('status', 'pending')
    ->first();

if (!$confirmation) {
    echo "❌ لم يتم العثور على confirmation معلق للباركود ST1-2025-007\n";
    exit;
}

echo "✅ وجدنا confirmation:\n";
echo "   - ID: {$confirmation->id}\n";
echo "   - Barcode: {$confirmation->barcode}\n";
echo "   - Type: {$confirmation->confirmation_type}\n";
echo "   - Status: {$confirmation->status}\n";
echo "   - delivery_note_id: " . ($confirmation->delivery_note_id ?? 'NULL') . "\n";
echo "   - batch_id: " . ($confirmation->batch_id ?? 'NULL') . "\n";
echo "\n";

// محاكاة الموافقة
echo "محاولة الموافقة على التأكيد...\n";

try {
    $confirmation->confirm(
        userId: 9, // User #9
        actualQuantity: null,
        notes: 'تم التأكيد من الاختبار'
    );
    
    echo "✅ تمت الموافقة بنجاح!\n\n";
    
    // إعادة تحميل البيانات
    $confirmation->refresh();
    
    echo "الحالة بعد الموافقة:\n";
    echo "   - Status: {$confirmation->status}\n";
    echo "   - Confirmed By: User #{$confirmation->confirmed_by}\n";
    echo "   - Confirmed At: {$confirmation->confirmed_at}\n";
    
} catch (\Exception $e) {
    echo "❌ فشلت الموافقة!\n";
    echo "   الخطأ: " . $e->getMessage() . "\n";
    echo "   الملف: " . $e->getFile() . "\n";
    echo "   السطر: " . $e->getLine() . "\n";
}
