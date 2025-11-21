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

        // Get latest 10 notifications for current user
        $notifications = Notification::with('creator')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        // Get unread count
        $unreadCount = Notification::where('user_id', $user->id)
            ->unread()
            ->count();

        return view('dashboard', compact('notifications', 'unreadCount'));
    }
}
