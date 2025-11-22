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

        // مشاركة الإشعارات مع جميع views
        View::composer(['layout.topbar', 'master'], function ($view) {
            try {
                $notificationModels = Notification::with('creator')
                    ->latest()
                    ->take(20)
                    ->get();

                $notifications = $notificationModels->map(function ($notif) {
                    return [
                        'id' => $notif->id,
                        'title' => $notif->title,
                        'message' => $notif->message,
                        'type' => $notif->type,
                        'color' => $notif->color,
                        'icon' => $notif->icon,
                        'is_read' => $notif->is_read,
                        'action_url' => $notif->action_url,
                        'created_at' => $notif->created_at->diffForHumans(),
                        'created_by_name' => $notif->creator ? $notif->creator->name : 'النظام',
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
