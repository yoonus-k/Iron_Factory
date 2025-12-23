<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n╔══════════════════════════════════════════════════════════╗\n";
echo "║   🔧 إصلاح سجل CO3-2025-012                            ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

$barcode = 'CO3-2025-012';

// إغلاق السجل النشط في المرحلة الثالثة
$updated = DB::table('worker_stage_history')
    ->where('barcode', $barcode)
    ->where('stage_type', 'stage3_coils')
    ->where('is_active', true)
    ->update([
        'is_active' => false,
        'ended_at' => now(),
        'duration_minutes' => DB::raw('TIMESTAMPDIFF(MINUTE, started_at, NOW())'),
        'status_after' => 'completed',
        'updated_at' => now()
    ]);

if ($updated > 0) {
    echo "✅ تم إغلاق السجل النشط للباركود {$barcode}\n";
    echo "   عدد السجلات المغلقة: {$updated}\n\n";
    echo "🎉 يمكنك الآن فتح صفحة 'العمليات غير المكتملة' والتحقق!\n";
} else {
    echo "⚠️  لم يتم العثور على سجلات نشطة لإغلاقها\n";
}

echo "\n";
