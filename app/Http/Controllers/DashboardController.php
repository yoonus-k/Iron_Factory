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

        // Get ALL notifications without any conditions or restrictions
        $notifications = Notification::with('creator')
            ->latest()
            ->get();

        // Get unread count for all notifications
        $unreadCount = Notification::unread()->count();

        return view('dashboard', compact('notifications', 'unreadCount'));
    }
}
