<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    protected $fillable = [
        'warehouse_id',
        'material_type_id',
        'unit_id',
        'barcode',
        'batch_number',
        'name_ar',
        'name_en',
        'material_category',

        'delivery_note_number',
     
        'shelf_location',
        'shelf_location_en',
        'purchase_invoice_id',
        'status',
        'notes',
        'notes_en',
        'created_by',
    ];

    protected $casts = [
        // ❌ تم نقل هذه الحقول إلى material_details:
        // 'original_weight' => 'float',
        // 'remaining_weight' => 'float',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constants
    const MATERIAL_CATEGORY_RAW = 'raw';
    const MATERIAL_CATEGORY_MANUFACTURED = 'manufactured';
    const MATERIAL_CATEGORY_FINISHED = 'finished';

    const STATUS_AVAILABLE = 'available';
    const STATUS_IN_USE = 'in_use';
    const STATUS_CONSUMED = 'consumed';
    const STATUS_EXPIRED = 'expired';

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialType::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deliveryNotes(): HasMany
    {
        return $this->hasMany(DeliveryNote::class);
    }

    public function warehouseTransactions(): HasMany
    {
        return $this->hasMany(WarehouseTransaction::class);
    }

    public function materialDetails()
    {
        return $this->hasMany(MaterialDetail::class);
    }

    public function operationLogs(): HasMany
    {
        return $this->hasMany(OperationLog::class, 'record_id')->where('table_name', 'materials');
    }

    public function stage1Stands(): HasMany
    {
        return $this->hasMany(Stage1Stand::class);
    }

    /**
     * الترجمة
     */
    public function getTypeName($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $locale === 'ar' ? $this->material_type : $this->material_type_en ?? $this->material_type;
    }

    public function getCategoryLabel($locale = null)
    {
        $categories = [
            'raw' => ['ar' => 'خام', 'en' => 'Raw'],
            'manufactured' => ['ar' => 'مصنع', 'en' => 'Manufactured'],
            'finished' => ['ar' => 'جاهز', 'en' => 'Finished'],
        ];

        $locale = $locale ?? app()->getLocale();
        return $categories[$this->material_category][$locale] ?? $this->material_category;
    }

    /**
     * التحقق من انتهاء الصلاحية
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && now()->isAfter($this->expiry_date);
    }

    public function isExpiringSoon($days = 7): bool
    {
        return $this->expiry_date &&
               now()->addDays($days)->isAfter($this->expiry_date) &&
               now()->isBefore($this->expiry_date);
    }

    /**
     * Get shelf location based on locale
     */
    public function getShelfLocation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $locale === 'ar' ? $this->shelf_location : $this->shelf_location_en ?? $this->shelf_location;
    }

    /**
     * Get notes based on locale
     */
    public function getNotes($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $locale === 'ar' ? $this->notes : $this->notes_en ?? $this->notes;
    }
}
