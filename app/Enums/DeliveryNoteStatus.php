<?php

namespace App\Enums;

enum DeliveryNoteStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';

    /**
     * الحصول على اسم الحالة بالعربية
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'قيد الانتظار',
            self::APPROVED => 'معتمد',
            self::REJECTED => 'مرفوض',
            self::COMPLETED => 'مكتمل',
        };
    }

    /**
     * الحصول على لون الحالة
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::COMPLETED => 'info',
        };
    }

    /**
     * الحصول على أيقونة الحالة
     */
    public function icon(): string
    {
        return match($this) {
            self::PENDING => 'clock',
            self::APPROVED => 'check-circle',
            self::REJECTED => 'x-circle',
            self::COMPLETED => 'check-square',
        };
    }

    /**
     * الحصول على جميع الحالات كمصفوفة
     */
    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'color' => $case->color(),
            'icon' => $case->icon(),
        ], self::cases());
    }
}
