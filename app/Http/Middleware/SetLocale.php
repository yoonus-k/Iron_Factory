<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // الحصول على اللغة من الجلسة أو من رأس الطلب أو تعيين الافتراضية
        $locale = session('locale', 'ar');

        // التحقق من أن اللغة مدعومة
        if (!in_array($locale, config('app.available_locales', ['ar', 'en']))) {
            $locale = 'ar';
        }

        // تعيين اللغة للتطبيق
        app()->setLocale($locale);

        // تخزين اللغة في الجلسة
        session(['locale' => $locale]);

        return $next($request);
    }
}
