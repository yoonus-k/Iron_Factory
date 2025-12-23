<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixPendingWorkerRecords extends Command
{
    protected $signature = 'workers:fix-pending-records';
    protected $description = 'إصلاح سجلات العمال النشطة للعمليات المكتملة';

    public function handle()
    {
        $this->info('🔍 البحث عن سجلات المرحلة الثالثة النشطة التي تم تعبئتها...');
        $this->newLine();

        // جلب جميع الباركودات من stage3_coils التي status = 'packed'
        $packedCoils = DB::table('stage3_coils')
            ->where('status', 'packed')
            ->select('id', 'barcode')
            ->get();

        $this->info("📦 عدد اللفائف المعبأة: " . $packedCoils->count());
        $this->newLine();

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
                $this->warn("⚠️  وجدنا {$activeRecords->count()} سجل نشط للباركود: {$coil->barcode}");
                
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
                
                $this->info("   ✅ تم إنهاء {$updated} سجل");
                $this->newLine();
                $fixedCount += $updated;
            }
        }

        // التحقق من المرحلة الرابعة أيضاً
        $this->newLine();
        $this->info('🔍 التحقق من سجلات المرحلة الرابعة...');
        $this->newLine();

        $completedBoxes = DB::table('stage4_boxes')
            ->whereIn('status', ['in_warehouse', 'shipped', 'delivered'])
            ->select('id', 'barcode')
            ->get();

        $this->info("📦 عدد الصناديق المكتملة: " . $completedBoxes->count());
        $this->newLine();

        foreach ($completedBoxes as $box) {
            $activeRecords = DB::table('worker_stage_history')
                ->where('stage_type', 'stage4_boxes')
                ->where('stage_record_id', $box->id)
                ->where('is_active', true)
                ->get();

            if ($activeRecords->count() > 0) {
                $this->warn("⚠️  وجدنا {$activeRecords->count()} سجل نشط للصندوق: {$box->barcode}");
                
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
                
                $this->info("   ✅ تم إنهاء {$updated} سجل");
                $this->newLine();
                $fixedCount += $updated;
            }
        }

        $this->newLine();
        $this->info(str_repeat("=", 50));
        $this->info("📊 النتيجة النهائية:");
        $this->info("   ✅ تم إصلاح {$fixedCount} سجل");
        $this->info(str_repeat("=", 50));

        if ($fixedCount > 0) {
            $this->newLine();
            $this->info("✨ تم إصلاح السجلات بنجاح! يمكنك الآن فتح صفحة 'العمليات غير المكتملة' والتحقق.");
        } else {
            $this->newLine();
            $this->info("✅ لا توجد سجلات تحتاج إصلاح. جميع السجلات صحيحة!");
        }

        return 0;
    }
}
