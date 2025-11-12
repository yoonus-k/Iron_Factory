<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
    }
}
