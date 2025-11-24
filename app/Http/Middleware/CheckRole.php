<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        // Check if user has one of the required roles
        // Support both role relationship and string role attribute
        $userRoleCode = null;

        if ($user->roleRelation) {
            $userRoleCode = $user->roleRelation->role_code;
        } elseif (is_string($user->role)) {
            $userRoleCode = $user->role;
        }

        if (!$userRoleCode || !in_array($userRoleCode, $roles)) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
        }

        return $next($request);
    }
}
