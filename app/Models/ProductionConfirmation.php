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
        'stage_record_id',
        'stage_type',
        'worker_stage_history_id',
        'barcode',
        'assigned_to',
        'assigned_by',
        'status',
        'confirmation_type',
        'confirmed_by',
        'confirmed_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'actual_received_quantity',
        'notes',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'actual_received_quantity' => 'decimal:2',
        'metadata' => 'array',
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
     * الموظف الذي قام بالإسناد
     */
    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * سجل worker_stage_history المرتبط (لعمليات إعادة الإسناد)
     */
    public function workerStageHistory(): BelongsTo
    {
        return $this->belongsTo(WorkerStageHistory::class, 'worker_stage_history_id');
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
     * تحميل سجل المرحلة بناءً على stage_type
     */
    public function loadStageRecord()
    {
        if (!$this->stage_record_id || !$this->stage_type) {
            return null;
        }

        $table = null;
        $weightField = null;

        switch ($this->stage_type) {
            case 'stage1_stands':
                $table = 'stage1_stands';
                $weightField = 'remaining_weight';
                break;
            case 'stage2_processed':
                $table = 'stage2_processed';
                $weightField = 'remaining_weight';
                break;
            case 'stage3_coils':
                $table = 'stage3_coils';
                $weightField = 'remaining_weight';
                break;
            case 'stage4_boxes':
                $table = 'stage4_boxes';
                $weightField = 'final_weight';
                break;
        }

        if ($table) {
            $record = \DB::table($table)->find($this->stage_record_id);
            if ($record && $weightField && isset($record->$weightField)) {
                // إضافة الوزن إلى metadata
                $metadata = $this->metadata ?? [];
                $metadata['stage_weight'] = $record->$weightField;
                $metadata['stage_barcode'] = $record->barcode ?? null;
                $this->metadata = $metadata;
                $this->save();
            }
            return $record;
        }

        return null;
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
            'actual_received_quantity' => $actualQuantity ?? $this->actual_received_quantity,
            'notes' => $notes,
        ]);

        // تحديث حالة الدفعة
        if ($this->batch) {
            $this->batch->update([
                'status' => 'in_production',
            ]);
        }
        
        // تحديث حالة أذن التسليم فقط إذا كان موجوداً (لا يوجد في حالة إعادة الإسناد)
        if ($this->delivery_note_id) {
            $this->updateDeliveryNoteStatusIfAllConfirmed();
        }
    }
    
    /**
     * تحديث حالة أذن التسليم إذا تم تأكيد جميع الكويلات
     */
    private function updateDeliveryNoteStatusIfAllConfirmed()
    {
        // التحقق من جميع التأكيدات المرتبطة بنفس أذن التسليم
        $allConfirmations = self::where('delivery_note_id', $this->delivery_note_id)->get();
        $allConfirmed = $allConfirmations->every(fn($c) => $c->status === 'confirmed');
        
        if ($allConfirmed) {
            $this->deliveryNote->update([
                'transfer_status' => 'confirmed',
                'confirmed_by' => $this->confirmed_by,
                'confirmed_at' => $this->confirmed_at,
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

        // تحديث حالة أذن التسليم (فقط إذا كان موجوداً - لا يوجد في حالة إعادة الإسناد)
        if ($this->deliveryNote) {
            $this->deliveryNote->update([
                'transfer_status' => 'rejected',
                'rejected_by' => $userId,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]);
        }

        // ✅ إرجاع الكمية للمستودع (MaterialDetail) - فقط إذا كان من المخزن
        if ($this->deliveryNote && $this->deliveryNote->materialDetail) {
            $materialDetail = $this->deliveryNote->materialDetail;
            
            // إضافة الكمية المرفوضة مرة أخرى للمستودع
            $rejectedQuantity = $this->actual_received_quantity ?? $this->deliveryNote->quantity;
            $materialDetail->quantity += $rejectedQuantity;
            $materialDetail->save();
            
            \Log::info("تم إرجاع {$rejectedQuantity} كجم للمستودع بعد رفض الكويل", [
                'confirmation_id' => $this->id,
                'material_detail_id' => $materialDetail->id,
                'previous_quantity' => $materialDetail->quantity - $rejectedQuantity,
                'new_quantity' => $materialDetail->quantity,
            ]);
        }

        // ✅ إرجاع الكمية للـ batch وتغيير حالته
        if ($this->batch) {
            $rejectedQuantity = $this->actual_received_quantity ?? $this->deliveryNote->quantity;
            $this->batch->increment('available_quantity', $rejectedQuantity);
            $this->batch->update(['status' => 'available']);
            
            \Log::info("تم إرجاع {$rejectedQuantity} كجم للـ batch بعد رفض الكويل", [
                'confirmation_id' => $this->id,
                'batch_id' => $this->batch->id,
                'batch_code' => $this->batch->batch_code,
            ]);
        }
    }
}
