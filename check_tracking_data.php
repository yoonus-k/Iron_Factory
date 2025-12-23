<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$barcode = 'BOX4-2025-025'; // باركود كرتون من المرحلة الرابعة

echo "=== التحقق من بيانات التتبع للباركود: {$barcode} ===\n\n";

// 1. البحث عن الباركود في جدول product_tracking
echo "1. البحث في جدول product_tracking:\n";
$trackingRecords = DB::table('product_tracking')
    ->where('barcode', $barcode)
    ->orWhere('input_barcode', $barcode)
    ->orWhere('output_barcode', $barcode)
    ->orderBy('created_at', 'asc')
    ->get();

if ($trackingRecords->isEmpty()) {
    echo "   ❌ لا توجد بيانات في جدول product_tracking لهذا الباركود!\n\n";
} else {
    echo "   ✅ تم العثور على " . $trackingRecords->count() . " سجل\n\n";
    foreach ($trackingRecords as $record) {
        echo "   - المرحلة: {$record->stage}\n";
        echo "     الإجراء: {$record->action}\n";
        echo "     الباركود: {$record->barcode}\n";
        echo "     الدخول: {$record->input_barcode}\n";
        echo "     الخروج: {$record->output_barcode}\n";
        echo "     الوزن: {$record->output_weight} كجم\n";
        echo "     التاريخ: {$record->created_at}\n\n";
    }
}

// 2. البحث في جداول المراحل
echo "2. البحث في جداول المراحل:\n";

// Stage 1
$stage1 = DB::table('stage1_stands')->where('barcode', $barcode)->first();
if ($stage1) {
    echo "   ✅ موجود في stage1_stands\n";
    echo "      الوزن: {$stage1->net_weight} كجم\n\n";
}

// Stage 2
$stage2 = DB::table('stage2_processed')->where('barcode', $barcode)->first();
if ($stage2) {
    echo "   ✅ موجود في stage2_processed\n";
    echo "      الوزن: {$stage2->output_weight} كجم\n\n";
}

// Stage 3
$stage3 = DB::table('stage3_coils')->where('barcode', $barcode)->first();
if ($stage3) {
    echo "   ✅ موجود في stage3_coils\n";
    echo "      الوزن: {$stage3->net_weight} كجم\n\n";
}

// Stage 4
$stage4 = DB::table('stage4_boxes')->where('barcode', $barcode)->orWhere('parent_barcode', $barcode)->get();
if ($stage4->isNotEmpty()) {
    echo "   ✅ موجود في stage4_boxes (" . $stage4->count() . " كرتون)\n";
    foreach ($stage4 as $box) {
        echo "      باركود الكرتون: {$box->barcode}\n";
        echo "      باركود اللفاف: {$box->parent_barcode}\n";
        echo "      الوزن: {$box->total_weight} كجم\n\n";
    }
}

// 3. فحص هيكل جدول product_tracking
echo "3. هيكل جدول product_tracking:\n";
$columns = DB::select("SHOW COLUMNS FROM product_tracking");
foreach ($columns as $column) {
    echo "   - {$column->Field} ({$column->Type})\n";
}

echo "\n=== انتهى الفحص ===\n";
