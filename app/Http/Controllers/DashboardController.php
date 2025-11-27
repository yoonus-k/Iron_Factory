<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $roleCode = $user->role->role_code ?? null;

        // إذا كان المستخدم عامل مرحلة، توجيهه إلى لوحة التحكم الخاصة به
        if (in_array($roleCode, ['STAGE1_WORKER', 'STAGE2_WORKER', 'STAGE3_WORKER', 'STAGE4_WORKER'])) {
            return redirect()->route('stage-worker.dashboard.index');
        }

        // Get ALL notifications without any conditions or restrictions
        $notifications = Notification::with('creator')
            ->latest()
            ->get();

        // Get unread count for all notifications
        $unreadCount = Notification::unread()->count();

        return view('dashboard', compact('notifications', 'unreadCount'));
    }
}
