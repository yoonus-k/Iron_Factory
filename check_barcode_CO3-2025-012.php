<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$barcode = 'CO3-2025-012';

echo "\n╔══════════════════════════════════════════════════════════╗\n";
echo "║   🔍 فحص السجلات للباركود: {$barcode}           ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

// فحص السجلات النشطة
$activeRecords = DB::table('worker_stage_history')
    ->where('barcode', $barcode)
    ->where('is_active', true)
    ->get();

echo "📊 عدد السجلات النشطة: " . $activeRecords->count() . "\n\n";

if ($activeRecords->count() > 0) {
    foreach ($activeRecords as $record) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "ID: {$record->id}\n";
        echo "المرحلة: {$record->stage_type}\n";
        echo "Stage Record ID: {$record->stage_record_id}\n";
        echo "Worker ID: {$record->worker_id}\n";
        echo "تاريخ البدء: {$record->started_at}\n";
        echo "الحالة قبل: {$record->status_before}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}

// فحص جميع السجلات (نشطة وغير نشطة)
$allRecords = DB::table('worker_stage_history')
    ->where('barcode', $barcode)
    ->orderBy('created_at', 'desc')
    ->get();

echo "\n📋 إجمالي السجلات: " . $allRecords->count() . "\n\n";

// فحص المرحلة الثالثة
$stage3 = DB::table('stage3_coils')->where('barcode', $barcode)->first();
if ($stage3) {
    echo "🎯 المرحلة الثالثة (stage3_coils):\n";
    echo "   ID: {$stage3->id}\n";
    echo "   الحالة: {$stage3->status}\n";
    echo "   تاريخ الإنشاء: {$stage3->created_at}\n\n";
}

// فحص المرحلة الرابعة
$stage4 = DB::table('stage4_boxes')
    ->where('barcode', $barcode)
    ->orWhere('parent_barcode', $barcode)
    ->get();

if ($stage4->count() > 0) {
    echo "📦 المرحلة الرابعة (stage4_boxes): " . $stage4->count() . " صندوق\n";
    foreach ($stage4 as $box) {
        echo "   • الباركود: {$box->barcode}\n";
        echo "     الحالة: {$box->status}\n";
        echo "     الوزن: {$box->total_weight} كجم\n\n";
    }
}

echo "\n";
