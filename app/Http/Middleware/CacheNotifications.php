<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheNotifications
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // تخزين مؤقت للإشعارات لمدة 5 دقائق
        // يتم تنظيفه عند إنشاء إشعار جديد أو تغيير حالة إشعار
        Cache::remember('notifications_cache', 300, function () {
            return true;
        });

        return $next($request);
    }
}
