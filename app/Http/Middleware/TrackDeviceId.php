<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackDeviceId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // الحصول على Device ID من الـ Header أو إنشاء واحد جديد
        $deviceId = $request->header('X-Device-ID');

        if (!$deviceId) {
            // محاولة الحصول عليه من Cookie
            $deviceId = $request->cookie('device_id');
        }

        if (!$deviceId) {
            // إنشاء device_id جديد بناءً على معلومات الجهاز
            $deviceId = $this->generateDeviceId($request);
        }

        // إضافة Device ID للـ Request
        $request->attributes->set('device_id', $deviceId);
        
        // إضافة Device ID للـ Session (للاستخدام لاحقاً)
        if ($request->hasSession()) {
            $request->session()->put('device_id', $deviceId);
        }

        $response = $next($request);

        // إضافة Device ID كـ Cookie في الـ Response
        return $response->cookie('device_id', $deviceId, 60 * 24 * 365); // سنة واحدة
    }

    /**
     * توليد Device ID فريد
     */
    protected function generateDeviceId(Request $request): string
    {
        $userAgent = $request->userAgent() ?? 'unknown';
        $ip = $request->ip();
        $timestamp = microtime(true);
        
        // إنشاء hash فريد
        $hash = hash('sha256', $userAgent . $ip . $timestamp);
        
        // استخدام أول 32 حرف من الـ hash
        return substr($hash, 0, 32);
    }
}
