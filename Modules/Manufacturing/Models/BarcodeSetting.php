<?php

namespace Modules\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarcodeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'prefix',
        'current_number',
        'year',
        'format',
        'auto_increment',
        'padding',
        'is_active',
    ];

    protected $casts = [
        'auto_increment' => 'boolean',
        'is_active' => 'boolean',
        'padding' => 'integer',
        'current_number' => 'integer',
    ];

    /**
     * الحصول على الرقم التالي وزيادة العداد
     */
    public function getNextNumber(): int
    {
        if (!$this->auto_increment) {
            return $this->current_number;
        }

        // التحقق من تغيير السنة
        $currentYear = date('Y');
        if ($this->year != $currentYear) {
            $this->year = $currentYear;
            $this->current_number = 0;
        }

        $this->increment('current_number');
        $this->refresh();

        return $this->current_number;
    }

    /**
     * توليد الباركود بناءً على الصيغة
     */
    public function generateBarcode(int $number): string
    {
        $paddedNumber = str_pad($number, $this->padding, '0', STR_PAD_LEFT);
        
        $barcode = str_replace(
            ['{prefix}', '{year}', '{number}'],
            [$this->prefix, $this->year, $paddedNumber],
            $this->format
        );

        return $barcode;
    }

    /**
     * الحصول على الإعدادات حسب النوع
     */
    public static function getByType(string $type): ?self
    {
        return self::where('type', $type)->where('is_active', true)->first();
    }
}
