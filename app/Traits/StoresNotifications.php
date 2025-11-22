<?php

namespace App\Traits;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

trait StoresNotifications
{
    /**
     * Store a notification for an operation
     *
     * @param string $type Notification type (e.g., 'material_created', 'warehouse_updated')
     * @param string $title Notification title
     * @param string $message Notification message
     * @param string $color Notification color (success, danger, warning, info)
     * @param string $icon Notification icon (Font Awesome class)
     * @param string|null $actionUrl URL to navigate to when notification is clicked
     * @return Notification
     */
    public function storeNotification($type, $title, $message, $color = 'info', $icon = 'fas fa-bell', $actionUrl = null)
    {
        try {
            return Notification::create([
                'user_id' =>null , // عام لجميع المستخدمين
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'color' => $color,
                'icon' => $icon,
                'action_url' => $actionUrl,
                'created_by' => Auth::id() ?? 1,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to store notification: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Store notification for create operation
     */
    public function notifyCreate($entityType, $entityNumber = null, $actionUrl = null)
    {
        $message = 'تم إنشاء ' . $entityType;
        if ($entityNumber) {
            $message .= ' جديد برقم: ' . $entityNumber;
        }

        return $this->storeNotification(
            strtolower(str_replace(' ', '_', $entityType)) . '_created',
            'إضافة ' . $entityType,
            $message,
            'success',
            'fas fa-plus-circle',
            $actionUrl
        );
    }

    /**
     * Store notification for update operation
     */
    public function notifyUpdate($entityType, $entityNumber = null, $actionUrl = null)
    {
        $message = 'تم تحديث ' . $entityType;
        if ($entityNumber) {
            $message .= ' برقم: ' . $entityNumber;
        }

        return $this->storeNotification(
            strtolower(str_replace(' ', '_', $entityType)) . '_updated',
            'تحديث ' . $entityType,
            $message,
            'warning',
            'fas fa-edit',
            $actionUrl
        );
    }

    /**
     * Store notification for delete operation
     */
    public function notifyDelete($entityType, $entityNumber = null, $actionUrl = null)
    {
        $message = 'تم حذف ' . $entityType;
        if ($entityNumber) {
            $message .= ' برقم: ' . $entityNumber;
        }

        return $this->storeNotification(
            strtolower(str_replace(' ', '_', $entityType)) . '_deleted',
            'حذف ' . $entityType,
            $message,
            'danger',
            'fas fa-trash',
            $actionUrl
        );
    }

    /**
     * Store notification for status change operation
     */
    public function notifyStatusChange($entityType, $oldStatus, $newStatus, $entityNumber = null, $actionUrl = null)
    {
        $message = 'تم تغيير حالة ' . $entityType;
        if ($entityNumber) {
            $message .= ' رقم ' . $entityNumber;
        }
        $message .= ' من "' . $oldStatus . '" إلى "' . $newStatus . '"';

        return $this->storeNotification(
            strtolower(str_replace(' ', '_', $entityType)) . '_status_changed',
            'تغيير حالة ' . $entityType,
            $message,
            'info',
            'fas fa-exchange-alt',
            $actionUrl
        );
    }

    /**
     * Store notification for custom operation
     */
    public function notifyCustomOperation($type, $title, $message, $color = 'info', $icon = 'fas fa-bell', $actionUrl = null)
    {
        return $this->storeNotification($type, $title, $message, $color, $icon, $actionUrl);
    }
}
