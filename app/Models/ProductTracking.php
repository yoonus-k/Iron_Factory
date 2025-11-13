<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductTracking extends Model
{
    use HasFactory;

    protected $table = 'product_tracking';

    protected $fillable = [
        'barcode',
        'stage',
        'action',
        'input_barcode',
        'output_barcode',
        'input_weight',
        'output_weight',
        'waste_amount',
        'waste_percentage',
        'cost',
        'worker_id',
        'shift_id',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'input_weight' => 'decimal:2',
        'output_weight' => 'decimal:2',
        'waste_amount' => 'decimal:2',
        'waste_percentage' => 'decimal:2',
        'cost' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * تسجيل حدث جديد
     */
    public static function track(array $data): self
    {
        return self::create($data);
    }

    /**
     * الحصول على تاريخ منتج معين
     */
    public static function getProductHistory(string $barcode)
    {
        return self::where('barcode', $barcode)
            ->orWhere('input_barcode', $barcode)
            ->orWhere('output_barcode', $barcode)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * التتبع العكسي (من المنتج النهائي للمادة الخام)
     */
    public static function traceBack(string $barcode): array
    {
        $chain = [];
        $currentBarcode = $barcode;
        $maxDepth = 10; // حماية من الحلقات اللانهائية

        while ($currentBarcode && $maxDepth > 0) {
            $record = self::where('output_barcode', $currentBarcode)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$record) {
                break;
            }

            $chain[] = [
                'barcode' => $record->output_barcode,
                'stage' => $record->stage,
                'action' => $record->action,
                'date' => $record->created_at,
                'input' => $record->input_barcode,
            ];

            $currentBarcode = $record->input_barcode;
            $maxDepth--;
        }

        return array_reverse($chain);
    }

    /**
     * تقرير شامل لمنتج
     */
    public static function fullReport(string $barcode): array
    {
        $history = self::getProductHistory($barcode);
        
        return [
            'barcode' => $barcode,
            'total_records' => $history->count(),
            'total_waste' => $history->sum('waste_amount'),
            'total_cost' => $history->sum('cost'),
            'stages' => $history->pluck('stage')->unique()->values(),
            'workers' => $history->whereNotNull('worker_id')->pluck('worker_id')->unique()->values(),
            'timeline' => $history->map(function ($item) {
                return [
                    'date' => $item->created_at->format('Y-m-d H:i'),
                    'stage' => $item->stage,
                    'action' => $item->action,
                    'waste' => $item->waste_amount,
                    'cost' => $item->cost,
                ];
            }),
            'current_status' => $history->last()?->action,
        ];
    }
}
