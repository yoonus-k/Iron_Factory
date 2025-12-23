<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== فحص حالة الباركود RW-2025-050 ===\n\n";

// البحث في production_confirmations
$confirmations = DB::table('production_confirmations')
    ->join('material_batches', 'production_confirmations.batch_id', '=', 'material_batches.id')
    ->where('material_batches.production_barcode', 'RW-2025-050')
    ->orWhere('material_batches.batch_code', 'RW-2025-050')
    ->select(
        'production_confirmations.id',
        'production_confirmations.status',
        'production_confirmations.confirmed_by',
        'production_confirmations.confirmed_at',
        'production_confirmations.created_at',
        'material_batches.production_barcode',
        'material_batches.batch_code',
        'material_batches.status as batch_status'
    )
    ->get();

echo "عدد التأكيدات الموجودة: " . $confirmations->count() . "\n\n";

foreach ($confirmations as $confirmation) {
    echo "--- تأكيد رقم: {$confirmation->id} ---\n";
    echo "حالة التأكيد: {$confirmation->status}\n";
    echo "تم التأكيد بواسطة: " . ($confirmation->confirmed_by ?? 'لم يتم') . "\n";
    echo "وقت التأكيد: " . ($confirmation->confirmed_at ?? 'لم يتم') . "\n";
    echo "وقت الإنشاء: {$confirmation->created_at}\n";
    echo "باركود الإنتاج: {$confirmation->production_barcode}\n";
    echo "كود الدفعة: {$confirmation->batch_code}\n";
    echo "حالة الدفعة: {$confirmation->batch_status}\n";
    echo "\n";
}

// البحث في material_batches أيضاً
$batches = DB::table('material_batches')
    ->where('production_barcode', 'RW-2025-050')
    ->orWhere('batch_code', 'RW-2025-050')
    ->select('id', 'production_barcode', 'batch_code', 'status', 'stage', 'created_at')
    ->get();

echo "\n=== معلومات الدفعة ===\n";
foreach ($batches as $batch) {
    echo "--- دفعة رقم: {$batch->id} ---\n";
    echo "باركود الإنتاج: {$batch->production_barcode}\n";
    echo "كود الدفعة: {$batch->batch_code}\n";
    echo "الحالة: {$batch->status}\n";
    echo "المرحلة: {$batch->stage}\n";
    echo "تاريخ الإنشاء: {$batch->created_at}\n";
    echo "\n";
}
