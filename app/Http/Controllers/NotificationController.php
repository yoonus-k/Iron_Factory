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
        $unreadFilter = $request->input('unread', null);

        // جلب جميع الإشعارات بدون قيود على المستخدم
        $query = Notification::with(['creator', 'user']);

        // Filter by type only if provided and not empty
        if ($type && $type !== '') {
            $query->where('type', $type);
        }

        // Handle unread filter - only apply if explicitly set to 0 or 1
        if ($unreadFilter !== null && $unreadFilter !== '') {
            if ($unreadFilter == '1' || $unreadFilter === 'true') {
                $query->where('is_read', false);
            } elseif ($unreadFilter == '0' || $unreadFilter === 'false') {
                $query->where('is_read', true);
            }
        }

        // Order by created_at descending (newest first)
        $notifications = $query->orderBy('created_at', 'desc')->paginate($limit);

        // Get statistics - لجميع الإشعارات
        $stats = [
            'total' => Notification::count(),
            'unread' => Notification::where('is_read', false)->count(),
            'read' => Notification::where('is_read', true)->count(),
            'by_color' => Notification::selectRaw('color, count(*) as count')
                ->groupBy('color')
                ->get()
        ];

        return view('notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Get notifications as JSON (for AJAX)
     */
    public function getNotifications(Request $request)
    {
        $user = Auth::user();
        $limit = $request->input('limit', 20);
        $unreadFilter = $request->input('unread', null);

        // جلب جميع الإشعارات بدون قيود
        $query = Notification::query();

        // Only filter by read status if explicitly requested
        if ($unreadFilter !== null && $unreadFilter !== '') {
            if ($unreadFilter == '1' || $unreadFilter === true || $unreadFilter === 'true') {
                $query->where('is_read', false);
            } elseif ($unreadFilter == '0' || $unreadFilter === false || $unreadFilter === 'false') {
                $query->where('is_read', true);
            }
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
            'unread_count' => Notification::where('is_read', false)->count(),
            'notifications' => $result,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        // حذف شرط المستخدم
        $notification = Notification::findOrFail($id);

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
        // وضع علامة قراءة على جميع الإشعارات
        Notification::where('is_read', false)->update(['is_read' => true]);

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
        // حذف بدون شرط المستخدم
        $notification = Notification::findOrFail($id);

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
        // حذف جميع الإشعارات
        Notification::query()->delete();

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
        // عدد الإشعارات غير المقروءة لجميع المستخدمين
        $unread_count = Notification::where('is_read', false)->count();

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
        // عرض أي إشعار بدون شرط المستخدم
        $notification = Notification::findOrFail($id);

        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }
}
