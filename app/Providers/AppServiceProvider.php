<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // تعيين اللغة من الجلسة أو الحفظ المحلي
        $this->app->singleton('setLanguage', function () {
            $locale = session('locale', config('app.locale'));
            app()->setLocale($locale);

            // حفظ اللغة الحالية في الجلسة
            session(['locale' => $locale]);

            return $locale;
        });

        // استدعاء خدمة اللغة في كل طلب
        $this->app->make('setLanguage');

        // مشاركة الإشعارات مع جميع views - محسّنة للأداء
        View::composer(['layout.topbar', 'master'], function ($view) {
            try {
                // جلب فقط الإشعارات غير المقروءة + 5 إشعارات مقروءة حديثة
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

                $notificationModels = $unreadNotifications->merge($readNotifications);

                // تخزين مؤقت للمستخدمين لتجنب استعلامات متكررة
                $usersCache = [];

                $notifications = $notificationModels->map(function ($notif) use (&$usersCache) {
                    // الحصول على اسم المستخدم من الكاش أو من قاعدة البيانات
                    $createdByName = 'النظام';
                    if ($notif->created_by) {
                        if (!isset($usersCache[$notif->created_by])) {
                            $user = \App\Models\User::select('id', 'name')->find($notif->created_by);
                            $usersCache[$notif->created_by] = $user ? $user->name : 'النظام';
                        }
                        $createdByName = $usersCache[$notif->created_by];
                    }

                    return [
                        'id' => $notif->id,
                        'title' => $notif->title,
                        'message' => $notif->message,
                        'type' => $notif->type,
                        'color' => $notif->color,
                        'icon' => $notif->icon,
                        'is_read' => (bool)$notif->is_read,
                        'action_url' => $notif->action_url,
                        'created_at' => $notif->created_at->diffForHumans(),
                        'created_by_name' => $createdByName,
                    ];
                })->toArray();

                $view->with('notifications', $notifications);
            } catch (\Exception $e) {
                // في حالة الخطأ، تمرير array فارغ
                $view->with('notifications', []);
            }
        });
    }
}
