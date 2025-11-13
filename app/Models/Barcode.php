<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Barcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'type',
        'reference_id',
        'reference_table',
        'status',
        'scan_count',
        'last_scanned_at',
        'metadata',
    ];

    protected $casts = [
        'scan_count' => 'integer',
        'last_scanned_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * العلاقة Polymorphic للمنتج المرتبط
     */
    public function reference(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'reference_table', 'reference_id');
    }

    /**
     * زيادة عداد المسح
     */
    public function incrementScan(): void
    {
        $this->increment('scan_count');
        $this->update(['last_scanned_at' => now()]);
    }

    /**
     * تحديث حالة الباركود
     */
    public function updateStatus(string $status): void
    {
        $this->update(['status' => $status]);
    }

    /**
     * البحث عن باركود
     */
    public static function findByBarcode(string $barcode): ?self
    {
        return self::where('barcode', $barcode)->first();
    }

    /**
     * التحقق من وجود باركود
     */
    public static function exists(string $barcode): bool
    {
        return self::where('barcode', $barcode)->exists();
    }

    /**
     * الحصول على تاريخ الباركود
     */
    public function getHistory()
    {
        return ProductTracking::where('barcode', $this->barcode)
            ->orWhere('input_barcode', $this->barcode)
            ->orWhere('output_barcode', $this->barcode)
            ->orderBy('created_at')
            ->get();
    }
}
