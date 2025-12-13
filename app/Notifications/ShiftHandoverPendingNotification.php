<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\ShiftHandover;

class ShiftHandoverPendingNotification extends Notification
{
    use Queueable;

    protected $handover;
    protected $shift;

    public function __construct(ShiftHandover $handover)
    {
        $this->handover = $handover;
        $this->shift = $handover->shiftAssignment;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $fromSupervisor = $this->handover->fromUser->name ?? 'لا يوجد';
        $shiftCode = $this->shift->shift_code ?? 'N/A';
        $workersCount = count($this->handover->handover_items ?? []);

        return (new MailMessage)
            ->subject("طلب نقل وردية - {$shiftCode}")
            ->greeting("مرحبا {$notifiable->name},")
            ->line("تم إرسال طلب نقل وردية جديد بانتظار موافقتك")
            ->line("**من:** {$fromSupervisor}")
            ->line("**الوردية:** {$shiftCode}")
            ->line("**عدد العمال:** {$workersCount}")
            ->action('عرض الطلب', route('manufacturing.shift-handovers.index'))
            ->line('شكراً لك.');
    }

    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'type' => 'shift_handover_pending',
            'handover_id' => $this->handover->id,
            'shift_code' => $this->shift->shift_code,
            'from_user' => $this->handover->fromUser->name ?? 'لا يوجد',
            'workers_count' => count($this->handover->handover_items ?? []),
            'action_url' => route('manufacturing.shift-handovers.index'),
            'message' => "طلب نقل وردية {$this->shift->shift_code} من {$this->handover->fromUser->name}",
        ]);
    }
}
