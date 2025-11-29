<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseIntakeRequest extends Model
{
    protected $fillable = [
        'request_number',
        'status',
        'warehouse_id',
        'requested_by',
        'approved_by',
        'approved_at',
        'notes',
        'rejection_reason',
        'boxes_count',
        'total_weight',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'total_weight' => 'decimal:2',
    ];

    /**
     * العلاقات
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(WarehouseIntakeItem::class, 'intake_request_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * التحقق من إمكانية الموافقة
     */
    public function canApprove(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * الموافقة على الطلب
     */
    public function approve(User $approver, int $warehouseId): bool
    {
        if (!$this->canApprove()) {
            return false;
        }

        \DB::beginTransaction();
        try {
            // تحديث حالة الطلب والمستودع
            $this->status = 'approved';
            $this->warehouse_id = $warehouseId;
            $this->approved_by = $approver->id;
            $this->approved_at = now();
            $this->save();

            // تحديث حالة الصناديق إلى "في المستودع" وتحديد المستودع
            foreach ($this->items as $item) {
                $item->stage4Box->update([
                    'status' => 'in_warehouse',
                    'warehouse_id' => $warehouseId
                ]);
            }

            // تسجيل الحركة في material_movements
            MaterialMovement::create([
                'movement_number' => MaterialMovement::generateMovementNumber(),
                'movement_type' => 'incoming',
                'source' => 'production',
                'to_warehouse_id' => $warehouseId,
                'quantity' => $this->boxes_count,
                'description' => 'إدخال منتجات تامة من المرحلة الرابعة',
                'reference_number' => $this->request_number,
                'notes' => "إذن إدخال رقم {$this->request_number} - عدد الصناديق: {$this->boxes_count} - الوزن الإجمالي: {$this->total_weight} كجم",
                'created_by' => $approver->id,
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'movement_date' => now(),
                'status' => 'completed',
                'material_id' => 1, // يمكن تعديله حسب الحاجة
                'unit_id' => 1, // يمكن تعديله حسب الحاجة
            ]);

            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error approving intake request: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * رفض الطلب
     */
    public function reject(User $user, string $reason): bool
    {
        if (!$this->canApprove()) {
            return false;
        }

        $this->status = 'rejected';
        $this->approved_by = $user->id;
        $this->rejection_reason = $reason;
        $this->approved_at = now();

        return $this->save();
    }

    /**
     * التحقق من إمكانية الطباعة
     */
    public function canPrint(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
