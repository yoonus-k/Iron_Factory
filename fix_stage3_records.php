<?php

/**
 * سكريبت لإصلاح سجلات المرحلة الثالثة التي لم تُنهى
 * يبحث عن الباركودات من المرحلة الثالثة التي تم تعبئتها في المرحلة الرابعة
 * ولكن سجلاتها ما زالت نشطة
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 البحث عن سجلات المرحلة الثالثة النشطة التي تم تعبئتها...\n\n";

// جلب جميع الباركودات من stage3_coils التي status = 'packed'
$packedCoils = DB::table('stage3_coils')
    ->where('status', 'packed')
    ->select('id', 'barcode')
    ->get();

echo "📦 عدد اللفائف المعبأة: " . $packedCoils->count() . "\n\n";

$fixedCount = 0;

foreach ($packedCoils as $coil) {
    // البحث عن سجلات نشطة لهذا اللفاف
    $activeRecords = DB::table('worker_stage_history')
        ->where('stage_type', 'stage3_coils')
        ->where(function($q) use ($coil) {
            $q->where('stage_record_id', $coil->id)
              ->orWhere('barcode', $coil->barcode);
        })
        ->where('is_active', true)
        ->get();

    if ($activeRecords->count() > 0) {
        echo "⚠️  وجدنا {$activeRecords->count()} سجل نشط للباركود: {$coil->barcode}\n";
        
        // إنهاء هذه السجلات
        $updated = DB::table('worker_stage_history')
            ->where('stage_type', 'stage3_coils')
            ->where(function($q) use ($coil) {
                $q->where('stage_record_id', $coil->id)
                  ->orWhere('barcode', $coil->barcode);
            })
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'ended_at' => now(),
                'duration_minutes' => DB::raw('TIMESTAMPDIFF(MINUTE, started_at, NOW())'),
                'status_after' => 'completed',
                'updated_at' => now()
            ]);
        
        echo "   ✅ تم إنهاء {$updated} سجل\n\n";
        $fixedCount += $updated;
    }
}

// التحقق من المرحلة الرابعة أيضاً
echo "\n🔍 التحقق من سجلات المرحلة الرابعة...\n\n";

$completedBoxes = DB::table('stage4_boxes')
    ->whereIn('status', ['in_warehouse', 'shipped', 'delivered'])
    ->select('id', 'barcode')
    ->get();

echo "📦 عدد الصناديق المكتملة: " . $completedBoxes->count() . "\n\n";

foreach ($completedBoxes as $box) {
    $activeRecords = DB::table('worker_stage_history')
        ->where('stage_type', 'stage4_boxes')
        ->where('stage_record_id', $box->id)
        ->where('is_active', true)
        ->get();

    if ($activeRecords->count() > 0) {
        echo "⚠️  وجدنا {$activeRecords->count()} سجل نشط للصندوق: {$box->barcode}\n";
        
        $updated = DB::table('worker_stage_history')
            ->where('stage_type', 'stage4_boxes')
            ->where('stage_record_id', $box->id)
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'ended_at' => now(),
                'duration_minutes' => DB::raw('TIMESTAMPDIFF(MINUTE, started_at, NOW())'),
                'status_after' => 'completed',
                'updated_at' => now()
            ]);
        
        echo "   ✅ تم إنهاء {$updated} سجل\n\n";
        $fixedCount += $updated;
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 النتيجة النهائية:\n";
echo "   ✅ تم إصلاح {$fixedCount} سجل\n";
echo str_repeat("=", 50) . "\n";

if ($fixedCount > 0) {
    echo "\n✨ تم إصلاح السجلات بنجاح! يمكنك الآن فتح صفحة 'العمليات غير المكتملة' والتحقق.\n";
} else {
    echo "\n✅ لا توجد سجلات تحتاج إصلاح. جميع السجلات صحيحة!\n";
}
