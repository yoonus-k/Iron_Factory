<?php

namespace App\Console\Commands;

use App\Services\SyncService;
use Illuminate\Console\Command;

class ProcessPendingSyncs extends Command
{
    /**
     * اسم الأمر
     *
     * @var string
     */
    protected $signature = 'sync:process-pending 
                            {--user= : معالجة عمليات مستخدم محدد}
                            {--limit=100 : عدد العمليات المعلقة للمعالجة}
                            {--force : معالجة حتى لو كانت هناك أخطاء}';

    /**
     * وصف الأمر
     *
     * @var string
     */
    protected $description = 'معالجة العمليات المعلقة للمزامنة (Offline Sync)';

    protected $syncService;

    /**
     * Constructor
     */
    public function __construct(SyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    /**
     * تنفيذ الأمر
     */
    public function handle()
    {
        $this->info('🔄 بدء معالجة العمليات المعلقة...');
        $this->newLine();

        $userId = $this->option('user');
        $limit = (int) $this->option('limit');

        try {
            // الحصول على عدد العمليات المعلقة
            $pendingCount = \App\Models\PendingSync::pending()
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->count();

            if ($pendingCount === 0) {
                $this->info('✅ لا توجد عمليات معلقة للمعالجة');
                return Command::SUCCESS;
            }

            $this->info("📊 عدد العمليات المعلقة: {$pendingCount}");
            $this->newLine();

            // معالجة العمليات المعلقة
            $progressBar = $this->output->createProgressBar(min($limit, $pendingCount));
            $progressBar->start();

            $result = $this->syncService->processPendingSyncs($userId, $limit);

            $progressBar->finish();
            $this->newLine(2);

            // عرض النتائج
            $this->displayResults($result);

            // التحقق من الأخطاء
            if ($result['failed'] > 0 && !$this->option('force')) {
                return Command::FAILURE;
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ حدث خطأ أثناء المعالجة');
            $this->error($e->getMessage());
            
            if ($this->option('verbose')) {
                $this->error($e->getTraceAsString());
            }

            return Command::FAILURE;
        }
    }

    /**
     * عرض نتائج المعالجة
     */
    protected function displayResults(array $result)
    {
        $this->table(
            ['المؤشر', 'القيمة'],
            [
                ['✅ تمت المعالجة بنجاح', $result['processed']],
                ['❌ فشلت', $result['failed']],
                ['📊 الإجمالي', $result['total']],
            ]
        );

        // حساب النسبة
        if ($result['total'] > 0) {
            $successRate = round(($result['processed'] / $result['total']) * 100, 2);
            
            if ($successRate >= 90) {
                $this->info("✨ نسبة النجاح: {$successRate}%");
            } elseif ($successRate >= 70) {
                $this->warn("⚠️  نسبة النجاح: {$successRate}%");
            } else {
                $this->error("❌ نسبة النجاح: {$successRate}%");
            }
        }

        $this->newLine();

        // إحصائيات إضافية
        $remainingPending = \App\Models\PendingSync::pending()->count();
        $totalFailed = \App\Models\PendingSync::failed()->count();

        $this->info("📋 العمليات المتبقية المعلقة: {$remainingPending}");
        $this->info("🔴 إجمالي العمليات الفاشلة: {$totalFailed}");
    }
}
