<?php

namespace Modules\Manufacturing\Services;

use App\Models\Barcode;
use App\Models\BarcodeSetting;
use App\Models\ProductTracking;
use Illuminate\Support\Facades\DB;
use Exception;

class BarcodeService
{
    /**
     * توليد باركود جديد
     * 
     * @param string $type (raw_material, stage1, stage2, stage3, stage4)
     * @param int|null $referenceId
     * @param string|null $referenceTable
     * @return string الباركود المولد
     */
    public static function generate(
        string $type,
        ?int $referenceId = null,
        ?string $referenceTable = null,
        ?array $metadata = null
    ): string {
        DB::beginTransaction();
        
        try {
            // الحصول على إعدادات الباركود
            $setting = BarcodeSetting::getByType($type);
            
            if (!$setting) {
                throw new Exception("لا توجد إعدادات باركود للنوع: {$type}");
            }

            // الحصول على الرقم التالي
            $number = $setting->getNextNumber();
            
            // توليد الباركود
            $barcode = $setting->generateBarcode($number);
            
            // التحقق من عدم التكرار (حماية مضاعفة)
            $attempts = 0;
            while (Barcode::exists($barcode) && $attempts < 10) {
                $number = $setting->getNextNumber();
                $barcode = $setting->generateBarcode($number);
                $attempts++;
            }

            if ($attempts >= 10) {
                throw new Exception("فشل في توليد باركود فريد بعد 10 محاولات");
            }

            // حفظ سجل الباركود
            if ($referenceId && $referenceTable) {
                Barcode::create([
                    'barcode' => $barcode,
                    'type' => $type,
                    'reference_id' => $referenceId,
                    'reference_table' => $referenceTable,
                    'status' => 'active',
                    'metadata' => $metadata,
                ]);
            }

            DB::commit();
            
            return $barcode;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * البحث عن باركود وإرجاع بياناته
     * 
     * @param string $barcode
     * @return array|null
     */
    public static function lookup(string $barcode): ?array
    {
        $barcodeRecord = Barcode::findByBarcode($barcode);
        
        if (!$barcodeRecord) {
            return null;
        }

        // زيادة عداد المسح
        $barcodeRecord->incrementScan();

        // الحصول على بيانات المنتج المرتبط
        $product = null;
        try {
            $product = DB::table($barcodeRecord->reference_table)
                ->where('id', $barcodeRecord->reference_id)
                ->first();
        } catch (Exception $e) {
            // في حالة عدم وجود الجدول
        }

        return [
            'barcode' => $barcodeRecord->barcode,
            'type' => $barcodeRecord->type,
            'status' => $barcodeRecord->status,
            'scan_count' => $barcodeRecord->scan_count,
            'last_scanned' => $barcodeRecord->last_scanned_at?->format('Y-m-d H:i:s'),
            'product' => $product,
            'metadata' => $barcodeRecord->metadata,
        ];
    }

    /**
     * تسجيل عملية في تتبع المنتج
     * 
     * @param array $data
     * @return ProductTracking
     */
    public static function track(array $data): ProductTracking
    {
        return ProductTracking::track($data);
    }

    /**
     * التتبع العكسي لمنتج (من النهاية للبداية)
     * 
     * @param string $barcode
     * @return array
     */
    public static function traceBack(string $barcode): array
    {
        return ProductTracking::traceBack($barcode);
    }

    /**
     * الحصول على تاريخ منتج كامل
     * 
     * @param string $barcode
     * @return array
     */
    public static function getHistory(string $barcode): array
    {
        $records = ProductTracking::getProductHistory($barcode);
        
        return $records->map(function ($record) {
            return [
                'date' => $record->created_at->format('Y-m-d H:i:s'),
                'stage' => $record->stage,
                'action' => $record->action,
                'input_barcode' => $record->input_barcode,
                'output_barcode' => $record->output_barcode,
                'input_weight' => $record->input_weight,
                'output_weight' => $record->output_weight,
                'waste' => $record->waste_amount,
                'waste_percentage' => $record->waste_percentage,
                'cost' => $record->cost,
                'worker_id' => $record->worker_id,
                'shift_id' => $record->shift_id,
                'notes' => $record->notes,
            ];
        })->toArray();
    }

    /**
     * تقرير شامل لمنتج
     * 
     * @param string $barcode
     * @return array
     */
    public static function fullReport(string $barcode): array
    {
        return ProductTracking::fullReport($barcode);
    }

    /**
     * التحقق من صحة الباركود
     * 
     * @param string $barcode
     * @return bool
     */
    public static function validate(string $barcode): bool
    {
        return Barcode::exists($barcode);
    }

    /**
     * تحديث حالة الباركود
     * 
     * @param string $barcode
     * @param string $status
     * @return bool
     */
    public static function updateStatus(string $barcode, string $status): bool
    {
        $barcodeRecord = Barcode::findByBarcode($barcode);
        
        if (!$barcodeRecord) {
            return false;
        }

        $barcodeRecord->updateStatus($status);
        return true;
    }

    /**
     * الحصول على إحصائيات الباركود
     * 
     * @param string|null $type
     * @return array
     */
    public static function getStatistics(?string $type = null): array
    {
        $query = Barcode::query();
        
        if ($type) {
            $query->where('type', $type);
        }

        return [
            'total' => $query->count(),
            'active' => $query->where('status', 'active')->count(),
            'used' => $query->where('status', 'used')->count(),
            'total_scans' => $query->sum('scan_count'),
            'by_type' => Barcode::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type')
                ->toArray(),
        ];
    }

    /**
     * إعادة تعيين الأرقام للسنة الجديدة
     * 
     * @return void
     */
    public static function resetForNewYear(): void
    {
        $currentYear = date('Y');
        
        BarcodeSetting::where('year', '<', $currentYear)
            ->update([
                'year' => $currentYear,
                'current_number' => 0,
            ]);
    }

    /**
     * طباعة الباركود (إرجاع SVG أو base64)
     * 
     * @param string $barcode
     * @param string $format (svg, png, base64)
     * @return string
     */
    public static function print(string $barcode, string $format = 'svg'): string
    {
        // يمكن استخدام مكتبة مثل: picqer/php-barcode-generator
        // هنا نرجع SVG بسيط كمثال
        
        if ($format === 'svg') {
            return self::generateBarcodeSVG($barcode);
        }
        
        return $barcode;
    }

    /**
     * توليد SVG للباركود
     * 
     * @param string $barcode
     * @return string
     */
    private static function generateBarcodeSVG(string $barcode): string
    {
        // SVG بسيط - يمكن استبداله بمكتبة متخصصة
        $width = 200;
        $height = 60;
        
        $svg = '<svg width="' . $width . '" height="' . $height . '" xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<rect width="100%" height="100%" fill="white"/>';
        $svg .= '<text x="50%" y="50%" text-anchor="middle" font-size="16" font-family="monospace">' . htmlspecialchars($barcode) . '</text>';
        $svg .= '</svg>';
        
        return $svg;
    }
}
