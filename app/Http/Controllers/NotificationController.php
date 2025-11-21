<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get user notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $limit = $request->input('limit', 50);
        $type = $request->input('type', null);
        $unread = $request->input('unread', null); // Changed from boolean to allow null

        // Use with() to eager load relationships
        $query = Notification::with(['creator', 'user'])->where('user_id', $user->id);

        if ($type) {
            $query->where('type', $type);
        }

        // Handle unread filter properly
        if ($unread !== null) {
            if ($unread == '1' || $unread === true || $unread === 'true') {
                $query->unread();
            } elseif ($unread == '0' || $unread === false || $unread === 'false') {
                $query->read();
            }
        }

        // Order by created_at descending (newest first)
        $notifications = $query->orderBy('created_at', 'desc')->paginate($limit);
        $stats = Notification::getStatistics($user->id);

        return view('notifications.index', compact('notifications', 'stats', 'type', 'unread'));
    }

    /**
     * Get notifications as JSON (for AJAX)
     */
    public function getNotifications(Request $request)
    {
        $user = Auth::user();
        $limit = $request->input('limit', 20);
        $unread = $request->boolean('unread', true);

        $query = Notification::where('user_id', $user->id);

        if ($unread) {
            $query->unread();
        }

        $notifications = $query->latest()->take($limit)->get();
        $result = [];

        foreach ($notifications as $notif) {
            $result[] = [
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
        }

        return response()->json([
            'success' => true,
            'count' => count($result),
            'unread_count' => Notification::where('user_id', $user->id)->unread()->count(),
            'notifications' => $result,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = Notification::where('user_id', $user->id)->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'تم وضع علامة القراءة على الإشعار',
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $this->notificationService->markAllAsRead($user);

        return response()->json([
            'success' => true,
            'message' => 'تم وضع علامة القراءة على جميع الإشعارات',
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = Notification::where('user_id', $user->id)->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الإشعار بنجاح',
        ]);
    }

    /**
     * Delete all notifications
     */
    public function destroyAll()
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف جميع الإشعارات بنجاح',
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $unread_count = Notification::where('user_id', $user->id)->unread()->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unread_count,
        ]);
    }

    /**
     * Get notification details
     */
    public function show($id)
    {
        $user = Auth::user();
        $notification = Notification::where('user_id', $user->id)->findOrFail($id);

        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }
}