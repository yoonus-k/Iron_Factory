<?php

namespace App\Helpers;

use App\Models\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class NotificationHelper
{
    /**
     * الحصول على الإشعارات المهمة بكفاءة
     * - 10 إشعارات غير مقروءة
     * - 5 إشعارات مقروءة حديثة
     */
    public static function getTopNotifications($limit = 15)
    {
        // محاولة الحصول من الكاش أولاً
        $userId = Auth::check() ? Auth::id() : 'guest';
        $cacheKey = 'top_notifications_' . $userId;

        return Cache::remember($cacheKey, 300, function () use ($limit) {
            $unreadNotifications = Notification::where('is_read', false)
                ->select('id', 'title', 'message', 'type', 'color', 'icon', 'is_read', 'action_url', 'created_at', 'created_by')
                ->latest()
                ->limit(10)
                ->get();

            $readNotifications = Notification::where('is_read', true)
                ->select('id', 'title', 'message', 'type', 'color', 'icon', 'is_read', 'action_url', 'created_at', 'created_by')
                ->latest()
                ->limit(5)
                ->get();

            return $unreadNotifications->merge($readNotifications)->take($limit);
        });
    }

    /**
     * تنظيف الكاش عند إنشاء إشعار جديد
     */
    public static function flushCache()
    {
        $userId = Auth::check() ? Auth::id() : 'guest';
        Cache::forget('top_notifications_' . $userId);
        Cache::forget('unread_count_' . $userId);
        Cache::forget('notification_stats_' . $userId);
    }

    /**
     * الحصول على عدد الإشعارات غير المقروءة
     */
    public static function getUnreadCount()
    {
        $userId = Auth::check() ? Auth::id() : 'guest';
        $cacheKey = 'unread_count_' . $userId;

        return Cache::remember($cacheKey, 60, function () {
            return Notification::where('is_read', false)->count();
        });
    }

    /**
     * الحصول على إحصائيات سريعة للإشعارات
     */
    public static function getQuickStats()
    {
        $userId = Auth::check() ? Auth::id() : 'guest';
        $cacheKey = 'notification_stats_' . $userId;

        return Cache::remember($cacheKey, 120, function () {
            $totalCount = Notification::count();
            $unreadCount = Notification::where('is_read', false)->count();

            return [
                'total' => $totalCount,
                'unread' => $unreadCount,
                'read' => $totalCount - $unreadCount,
            ];
        });
    }
}
