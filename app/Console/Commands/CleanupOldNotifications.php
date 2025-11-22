<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class CleanupOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup {--days=30 : عدد الأيام للاحتفاظ بالإشعارات}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'تنظيف الإشعارات المقروءة والقديمة جداً لتحسين الأداء';

    /**
     * Execute the command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $date = now()->subDays($days);

        // حذف الإشعارات المقروءة القديمة جداً
        $deletedCount = Notification::where('is_read', true)
            ->where('created_at', '<', $date)
            ->delete();

        $this->info("تم حذف {$deletedCount} إشعار قديم.");

        // إحصائيات
        $totalNotifications = Notification::count();
        $unreadCount = Notification::where('is_read', false)->count();

        $this->info("إجمالي الإشعارات: {$totalNotifications}");
        $this->info("الإشعارات غير المقروءة: {$unreadCount}");
    }
}
