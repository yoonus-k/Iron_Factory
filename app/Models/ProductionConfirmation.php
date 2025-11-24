<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\DeliveryNote;
use Modules\Manufacturing\Entities\MaterialBatch;

class ProductionConfirmation extends Model
{
    use HasFactory;

    protected $table = 'production_confirmations';

    protected $fillable = [
        'delivery_note_id',
        'batch_id',
        'stage_code',
        'assigned_to',
        'status',
        'confirmed_by',
        'confirmed_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'actual_received_quantity',
        'notes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'actual_received_quantity' => 'decimal:2',
    ];

    /**
     * العلاقة مع أذن التسليم
     */
    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    /**
     * العلاقة مع الدفعة
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(MaterialBatch::class);
    }

    /**
     * العلاقة مع الموظف المكلف
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * العلاقة مع الموظف الذي أكد
     */
    public function confirmedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * العلاقة مع الموظف الذي رفض
     */
    public function rejectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * العلاقة مع المرحلة
     */
    public function stage()
    {
        return ProductionStage::where('stage_code', $this->stage_code)->first();
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * الحصول على الطلبات المعلقة لموظف معين
     */
    public static function getPendingForUser($userId)
    {
        return self::with(['deliveryNote', 'batch', 'assignedUser'])
            ->pending()
            ->forUser($userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * تأكيد الاستلام
     */
    public function confirm($userId, $actualQuantity = null, $notes = null)
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_by' => $userId,
            'confirmed_at' => now(),
            'actual_received_quantity' => $actualQuantity ?? $this->deliveryNote->quantity,
            'notes' => $notes,
        ]);

        // تحديث حالة أذن التسليم
        $this->deliveryNote->update([
            'transfer_status' => 'confirmed',
            'confirmed_by' => $userId,
            'confirmed_at' => now(),
            'actual_received_quantity' => $actualQuantity ?? $this->deliveryNote->quantity,
        ]);

        // تحديث حالة الدفعة
        if ($this->batch) {
            $this->batch->update([
                'status' => 'in_production',
            ]);
        }
    }

    /**
     * رفض الاستلام
     */
    public function reject($userId, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_by' => $userId,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        // تحديث حالة أذن التسليم
        $this->deliveryNote->update([
            'transfer_status' => 'rejected',
            'rejected_by' => $userId,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        // إرجاع الكمية للمستودع
        if ($this->batch) {
            $this->batch->increment('available_quantity', $this->deliveryNote->quantity);
            $this->batch->update(['status' => 'available']);
        }
    }
}
