<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasMultilingualContent;

class Material extends Model
{
    use HasMultilingualContent;
    protected $fillable = [
        'warehouse_id',
        'material_type_id',
        'unit_id',
        'barcode',
        'batch_number',
        'name_ar',
        'name_en',
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

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', self::class);
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
     * ======================================
     * 🌍 نظام إدارة اللغات / Language Management
     * ======================================
     */

    /**
     * علاقة الترجمات - استخدام morphMany بدلاً من hasMany
     */
    public function getTranslations_relation()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * الحصول على ترجمة معينة
     * @param string $key - مفتاح الحقل (name, notes, shelf_location)
     * @param string|null $locale - اللغة (ar, en)
     */
    public function getTranslation($key, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        return Translation::getTranslation(
            self::class,
            $this->id,
            $key,
            $locale
        );
    }

    /**
     * حفظ/تحديث ترجمة
     * @param string $key - مفتاح الحقل
     * @param string $value - القيمة
     * @param string|null $locale - اللغة
     */
    public function setTranslation($key, $value, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        Translation::saveTranslation(
            self::class,
            $this->id,
            $key,
            $value,
            $locale
        );

        return $this;
    }

    /**
     * الحصول على كل الترجمات
     * @param string|null $locale - اللغة
     */
    public function getAllTranslations($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        return Translation::getTranslations(
            self::class,
            $this->id,
            $locale
        );
    }

    /**
     * ========== Helper Methods للحقول الرئيسية ==========
     */

    /**
     * الحصول على اسم المادة بلغة معينة
     */
    public function getDisplayName($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        // جرب الترجمة أولاً
        $translated = $this->getTranslation('name', $locale);
        if ($translated) return $translated;
        
        // أو اعتمد على الحقول المباشرة
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    /**
     * الحصول على الملاحظات بلغة معينة
     */
    public function getDisplayNotes($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        $translated = $this->getTranslation('notes', $locale);
        if ($translated) return $translated;
        
        return $locale === 'ar' ? $this->notes : $this->notes_en;
    }

    /**
     * الحصول على موقع الرف بلغة معينة
     */
    public function getDisplayShelfLocation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        $translated = $this->getTranslation('shelf_location', $locale);
        if ($translated) return $translated;
        
        return $locale === 'ar' ? $this->shelf_location : $this->shelf_location_en;
    }

    /**
     * تعيين الاسم بلغات متعددة
     */
    public function setMultilingualName($nameAr, $nameEn)
    {
        $this->name_ar = $nameAr;
        $this->name_en = $nameEn;
        
        // حفظ في جدول الترجمات أيضاً للمرجعية
        $this->setTranslation('name', $nameAr, 'ar');
        $this->setTranslation('name', $nameEn, 'en');
        
        return $this;
    }

    /**
     * تعيين الملاحظات بلغات متعددة
     */
    public function setMultilingualNotes($notesAr, $notesEn)
    {
        $this->notes = $notesAr;
        $this->notes_en = $notesEn;
        
        $this->setTranslation('notes', $notesAr, 'ar');
        $this->setTranslation('notes', $notesEn, 'en');
        
        return $this;
    }

    /**
     * تعيين موقع الرف بلغات متعددة
     */
    public function setMultilingualShelfLocation($locationAr, $locationEn)
    {
        $this->shelf_location = $locationAr;
        $this->shelf_location_en = $locationEn;
        
        $this->setTranslation('shelf_location', $locationAr, 'ar');
        $this->setTranslation('shelf_location', $locationEn, 'en');
        
        return $this;
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
}
