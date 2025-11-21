<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'color',
        'action_type',
        'model_type',
        'model_id',
        'created_by',
        'is_read',
        'read_at',
        'action_url',
        'metadata',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'metadata' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * المستخدم الذي سيتلقى الاشعار
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * المستخدم الذي قام بالعملية
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * وضع علامة قراءة على الاشعار
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * وضع علامة عدم قراءة على الاشعار
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * الاشعارات غير المقروءة
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * الاشعارات المقروءة
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * فلترة حسب النوع
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * فلترة حسب اللون
     */
    public function scopeByColor($query, $color)
    {
        return $query->where('color', $color);
    }

    /**
     * آخر الاشعارات
     */
    public function scopeLatest($query, $column = 'created_at')
    {
        return $query->orderBy($column, 'desc');
    }

    /**
     * الاشعارات اليوم
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * الاشعارات هذا الأسبوع
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * الاشعارات هذا الشهر
     */
    public function scopeThisMonth($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ]);
    }

    /**
     * احصائيات الاشعارات
     */
    public static function getStatistics($userId = null)
    {
        $baseQuery = self::query();

        if ($userId) {
            $baseQuery->where('user_id', $userId);
        }

        return [
            'total' => (clone $baseQuery)->count(),
            'unread' => (clone $baseQuery)->unread()->count(),
            'read' => (clone $baseQuery)->read()->count(),
            'by_type' => (clone $baseQuery)->groupBy('type')->selectRaw('type, count(*) as count')->get(),
            'by_color' => (clone $baseQuery)->groupBy('color')->selectRaw('color, count(*) as count')->get(),
        ];
    }
}
