<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class CleanOldNotifications extends Command
{
    protected $signature = 'notifications:clean {--days=30 : عدد الأيام}';
    protected $description = 'حذف الإشعارات القديمة';

    public function handle(NotificationService $notificationService)
    {
        $days = $this->option('days');
        $deleted = $notificationService->deleteOldNotifications($days);

        $this->info("تم حذف {$deleted} إشعار قديم (أقدم من {$days} يوم)");
    }
}
