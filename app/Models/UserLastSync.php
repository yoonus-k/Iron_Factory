<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLastSync extends Model
{
    protected $table = 'user_last_sync';

    protected $fillable = [
        'user_id',
        'last_pull_at',
        'last_push_at',
        'pending_count',
        'failed_count',
        'sync_stats',
    ];

    protected $casts = [
        'last_pull_at' => 'datetime',
        'last_push_at' => 'datetime',
        'sync_stats' => 'json',
        'pending_count' => 'integer',
        'failed_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * علاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * تحديث وقت آخر سحب
     */
    public function updateLastPull()
    {
        $this->update([
            'last_pull_at' => now(),
        ]);
    }

    /**
     * تحديث وقت آخر رفع
     */
    public function updateLastPush()
    {
        $this->update([
            'last_push_at' => now(),
        ]);
    }

    /**
     * تحديث إحصائيات المزامنة
     */
    public function updateStats($pendingCount, $failedCount)
    {
        $this->update([
            'pending_count' => $pendingCount,
            'failed_count' => $failedCount,
        ]);
    }

    /**
     * الحصول على أو إنشاء سجل للمستخدم
     */
    public static function getOrCreateForUser($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'pending_count' => 0,
                'failed_count' => 0,
                'sync_stats' => [],
            ]
        );
    }
}
