<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$barcode = 'RW-2025-050';

echo "=== محاكاة الكود الجديد ===\n\n";

// البحث عن batch_code في material_batches
$batch = DB::table('material_batches')
    ->where('batch_code', $barcode)
    ->first();

if (!$batch) {
    echo "❌ الباركود غير موجود في النظام\n";
    exit;
}

echo "✅ الباركود موجود: Batch ID = {$batch->id}\n\n";

// البحث عن التأكيد المرتبط بهذا الـ batch
$confirmation = DB::table('production_confirmations')
    ->where('batch_id', $batch->id)
    ->where('stage_code', 'stage_1')
    ->first();

if (!$confirmation) {
    echo "❌ هذا الباركود غير مسجل في نظام الموافقات للمرحلة الأولى\n";
    exit;
}

echo "✅ التأكيد موجود: Confirmation ID = {$confirmation->id}\n";
echo "حالة التأكيد: {$confirmation->status}\n\n";

// التحقق من حالة الموافقة
if ($confirmation->status === 'pending') {
    echo "⏳ هذا الباركود في انتظار الموافقة\n";
    exit;
}

if ($confirmation->status === 'rejected') {
    echo "❌ تم رفض هذا الباركود\n";
    exit;
}

if ($confirmation->status !== 'confirmed') {
    echo "❌ حالة الباركود غير صالحة: {$confirmation->status}\n";
    exit;
}

echo "✅ الباركود مؤكد! يمكن المتابعة\n";
