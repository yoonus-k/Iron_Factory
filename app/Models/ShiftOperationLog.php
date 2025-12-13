<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftOperationLog extends Model
{
    use HasFactory;

    protected $table = 'shift_operation_logs';

    protected $fillable = [
        'shift_id',
        'user_id',
        'operation_type',
        'stage_number',
        'old_data',
        'new_data',
        'description',
        'notes',
        'status',
        'error_message',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العمليات المتاحة
     */
    const OPERATION_CREATE = 'create';
    const OPERATION_UPDATE = 'update';
    const OPERATION_TRANSFER = 'transfer';
    const OPERATION_ASSIGN_STAGE = 'assign_stage';
    const OPERATION_COMPLETE = 'complete';
    const OPERATION_SUSPEND = 'suspend';
    const OPERATION_RESUME = 'resume';

    /**
     * الحالات المتاحة
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * العلاقات
     */
    
    /**
     * Get the shift that this log belongs to
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(ShiftAssignment::class, 'shift_id');
    }

    /**
     * Get the user who performed this operation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper method to create a log entry
     */
    public static function logOperation(
        ShiftAssignment $shift,
        string $operationType,
        array $oldData = [],
        array $newData = [],
        string $description = '',
        string $notes = '',
        string $stageNumber = null
    ) {
        try {
            $user = auth()->user();
            
            return self::create([
                'shift_id' => $shift->id,
                'user_id' => $user?->id,
                'operation_type' => $operationType,
                'stage_number' => $stageNumber,
                'old_data' => $oldData ?: null,
                'new_data' => $newData ?: null,
                'description' => $description,
                'notes' => $notes,
                'status' => self::STATUS_COMPLETED,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create operation log', [
                'error' => $e->getMessage(),
                'shift_id' => $shift->id,
            ]);
            return null;
        }
    }

    /**
     * Get operation type label in Arabic
     */
    public function getOperationTypeLabel(): string
    {
        return match($this->operation_type) {
            self::OPERATION_CREATE => 'إنشاء وردية',
            self::OPERATION_UPDATE => 'تحديث الوردية',
            self::OPERATION_TRANSFER => 'نقل الوردية',
            self::OPERATION_ASSIGN_STAGE => 'تحديد المرحلة',
            self::OPERATION_COMPLETE => 'إكمال الوردية',
            self::OPERATION_SUSPEND => 'إيقاف الوردية',
            self::OPERATION_RESUME => 'استئناف الوردية',
            default => $this->operation_type,
        };
    }

    /**
     * Get status label in Arabic
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_FAILED => 'فشل',
            default => $this->status,
        };
    }
}
