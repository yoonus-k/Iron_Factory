<?php

namespace App\Notifications;

use App\Models\ShiftHandover;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PendingWorkHandoverNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $handover;
    protected $pendingItemsCount;

    /**
     * Create a new notification instance.
     */
    public function __construct(ShiftHandover $handover, int $pendingItemsCount)
    {
        $this->handover = $handover;
        $this->pendingItemsCount = $pendingItemsCount;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $locale = app()->getLocale();

        if ($locale === 'ar') {
            return (new MailMessage)
                ->subject('تسليم وردية - أشغال معلقة')
                ->greeting('مرحباً ' . $notifiable->name)
                ->line('لديك تسليم وردية جديد من: ' . $this->handover->fromUser->name)
                ->line('المرحلة: ' . $this->handover->stage_number)
                ->line('عدد الأشغال المعلقة: ' . $this->pendingItemsCount)
                ->action('عرض التفاصيل', url('/manufacturing/shift-handovers/' . $this->handover->id))
                ->line('يرجى مراجعة الأشغال المعلقة والبدء في إكمالها.');
        }

        return (new MailMessage)
            ->subject('Shift Handover - Pending Work')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have a new shift handover from: ' . $this->handover->fromUser->name)
            ->line('Stage: ' . $this->handover->stage_number)
            ->line('Pending items count: ' . $this->pendingItemsCount)
            ->action('View Details', url('/manufacturing/shift-handovers/' . $this->handover->id))
            ->line('Please review the pending work and continue completing it.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $locale = app()->getLocale();

        return [
            'type' => 'shift_handover',
            'handover_id' => $this->handover->id,
            'from_user_id' => $this->handover->from_user_id,
            'from_user_name' => $this->handover->fromUser->name,
            'stage_number' => $this->handover->stage_number,
            'pending_items_count' => $this->pendingItemsCount,
            'handover_time' => $this->handover->handover_time,
            'title' => $locale === 'ar' ? 'تسليم وردية جديد' : 'New Shift Handover',
            'message' => $locale === 'ar'
                ? "لديك تسليم وردية من {$this->handover->fromUser->name} - المرحلة {$this->handover->stage_number}"
                : "You have a shift handover from {$this->handover->fromUser->name} - Stage {$this->handover->stage_number}",
            'action_url' => url('/manufacturing/shift-handovers/' . $this->handover->id),
        ];
    }
}
