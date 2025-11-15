<?php

namespace Modules\Manufacturing\Services;

use App\Models\Material;
use App\Models\MaterialDetail;
use App\Models\WarehouseTransaction;
use Illuminate\Support\Str;

class MaterialService
{
    /**
     * إنشاء مادة جديدة مع تسجيل حركة المستودع
     * Create a new material and log warehouse transaction
     */
    public function createMaterial(array $data): Material
    {
        try {
            // تعيين القيم الافتراضية
            $data['created_by'] = $data['created_by'] ?? auth()->id();

            // إنشاء المادة
            $material = Material::create($data);

            // تسجيل حركة الاستقبال في المستودع


            // إنشاء تفاصيل المادة في المستودع
            if ($material->warehouse_id) {
                $this->createMaterialDetail($material, $data);
            }

            return $material;
        } catch (\Exception $e) {
            throw new \Exception('فشل في إنشاء المادة: ' . $e->getMessage());
        }
    }

    /**
     * تحديث مادة موجودة مع تسجيل التغييرات
     * Update an existing material and log transaction for quantity changes
     */
    public function updateMaterial(Material $material, array $data): Material
    {
        try {
            // حفظ القيم القديمة
            $oldWeight = $material->original_weight;
            $oldRemainingWeight = $material->remaining_weight;
            $oldWarehouseId = $material->warehouse_id;

            // تحديث المادة
            $material->update($data);

            // // تسجيل حركة إذا تم تغيير الكمية الأصلية (إضافة كمية جديدة)
            // if (isset($data['original_weight']) && $data['original_weight'] != $oldWeight) {
            //     $this->handleWeightChange($material, $oldWeight, $data['original_weight']);
            // }

            // تسجيل حركة إذا تم تغيير الكمية المتبقية (صرف أو استهلاك)
            if (isset($data['remaining_weight']) && $data['remaining_weight'] != $oldRemainingWeight) {
                $this->handleRemainingWeightChange($material, $oldRemainingWeight, $data['remaining_weight']);
            }

            // تحديث تفاصيل المادة إذا تم تغيير المستودع
            if (isset($data['warehouse_id']) && $data['warehouse_id'] != $oldWarehouseId) {
                $this->handleWarehouseChange($material, $oldWarehouseId, $data['warehouse_id']);
            }

            return $material;
        } catch (\Exception $e) {
            throw new \Exception('فشل في تحديث المادة: ' . $e->getMessage());
        }
    }

    /**
     * تسجيل حركة استقبال للمادة الجديدة
     * Log receive transaction for new material
     */
    private function createReceiveTransaction(Material $material, array $data): void
    {
        WarehouseTransaction::create([
            'transaction_number' => $this->generateTransactionNumber(),
            'warehouse_id' => $material->warehouse_id,
            'material_id' => $material->id,
            'transaction_type' => WarehouseTransaction::TYPE_RECEIVE,

            'unit_id' => $material->unit_id,
            'reference_number' => $material->delivery_note_number ?? null,
            'notes' => 'استقبال مادة جديدة - ' . $material->name_ar,
            'notes_en' => 'Receiving new material - ' . $material->name_en,
            'created_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }

    /**
     * معالجة تغيير الوزن الأصلي (إضافة كمية إضافية)
     * Handle original weight change (adding more quantity)
     */
    private function handleWeightChange(Material $material, float $oldWeight, float $newWeight): void
    {
        $weightDifference = $newWeight - $oldWeight;

        if ($weightDifference > 0) {
            // إضافة كمية
            WarehouseTransaction::create([
                'transaction_number' => $this->generateTransactionNumber(),
                'warehouse_id' => $material->warehouse_id,
                'material_id' => $material->id,
                'transaction_type' => WarehouseTransaction::TYPE_RECEIVE,
                'quantity' => $weightDifference,
                'unit_id' => $material->unit_id,
                'reference_number' => $material->delivery_note_number ?? null,
                'notes' => 'إضافة كمية للمادة - ' . $material->name_ar,
                'notes_en' => 'Adding quantity to material - ' . $material->name_en,
                'created_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        } elseif ($weightDifference < 0) {
            // طرح كمية
            WarehouseTransaction::create([
                'transaction_number' => $this->generateTransactionNumber(),
                'warehouse_id' => $material->warehouse_id,
                'material_id' => $material->id,
                'transaction_type' => WarehouseTransaction::TYPE_ADJUSTMENT,
                'quantity' => abs($weightDifference),
                'unit_id' => $material->unit_id,
                'notes' => 'تعديل كمية المادة (طرح) - ' . $material->name_ar,
                'notes_en' => 'Material quantity adjustment (deduction) - ' . $material->name_en,
                'created_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        }
    }

    /**
     * معالجة تغيير الكمية المتبقية (صرف أو استهلاك)
     * Handle remaining weight change (consumption or issue)
     */
    private function handleRemainingWeightChange(Material $material, float $oldRemaining, float $newRemaining): void
    {
        $remainingDifference = $oldRemaining - $newRemaining;

        if ($remainingDifference > 0) {
            // صرف أو استهلاك
            WarehouseTransaction::create([
                'transaction_number' => $this->generateTransactionNumber(),
                'warehouse_id' => $material->warehouse_id,
                'material_id' => $material->id,
                'transaction_type' => WarehouseTransaction::TYPE_ISSUE,
                'quantity' => $remainingDifference,
                'unit_id' => $material->unit_id,
                'notes' => 'صرف مادة - ' . $material->name_ar,
                'notes_en' => 'Material issue - ' . $material->name_en,
                'created_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        } elseif ($remainingDifference < 0) {
            // إضافة كمية متبقية
            WarehouseTransaction::create([
                'transaction_number' => $this->generateTransactionNumber(),
                'warehouse_id' => $material->warehouse_id,
                'material_id' => $material->id,
                'transaction_type' => WarehouseTransaction::TYPE_ADJUSTMENT,
                'quantity' => abs($remainingDifference),
                'unit_id' => $material->unit_id,
                'notes' => 'تعديل الكمية المتبقية - ' . $material->name_ar,
                'notes_en' => 'Adjustment of remaining quantity - ' . $material->name_en,
                'created_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        }
    }

    /**
     * معالجة تغيير المستودع (نقل بين مستودعات)
     * Handle warehouse change (transfer between warehouses)
     */
    private function handleWarehouseChange(Material $material, int $oldWarehouseId, int $newWarehouseId): void
    {
        if ($material->remaining_weight > 0) {
            // تسجيل حركة نقل
            WarehouseTransaction::create([
                'transaction_number' => $this->generateTransactionNumber(),
                'warehouse_id' => $newWarehouseId,
                'material_id' => $material->id,
                'transaction_type' => WarehouseTransaction::TYPE_TRANSFER,
                'quantity' => $material->remaining_weight,
                'unit_id' => $material->unit_id,
                'from_warehouse_id' => $oldWarehouseId,
                'to_warehouse_id' => $newWarehouseId,
                'notes' => 'نقل مادة من المستودع - ' . $material->name_ar,
                'notes_en' => 'Transfer material from warehouse - ' . $material->name_en,
                'created_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        }
    }

    /**
     * إنشاء تفاصيل المادة في المستودع
     * Create material details in warehouse
     */
    private function createMaterialDetail(Material $material, array $data): void
    {
        MaterialDetail::firstOrCreate(
            [
                'warehouse_id' => $material->warehouse_id,
                'material_id' => $material->id,
            ],
            [
                'quantity' => $material->remaining_weight,
                'min_quantity' => $data['min_quantity'] ?? 0,
                'max_quantity' => $data['max_quantity'] ?? $material->original_weight * 2,
                'location_in_warehouse' => $data['location_in_warehouse'] ?? $material->shelf_location,
                'location_in_warehouse_en' => $data['location_in_warehouse_en'] ?? $material->shelf_location_en,
                'created_by' => auth()->id(),
            ]
        );
    }

    /**
     * تحديث تفاصيل المادة
     * Update material details
     */
    public function updateMaterialDetail(Material $material, array $data): void
    {
        MaterialDetail::where('material_id', $material->id)
            ->where('warehouse_id', $material->warehouse_id)
            ->update([
                'quantity' => $material->remaining_weight,
                'min_quantity' => $data['min_quantity'] ?? null,
                'max_quantity' => $data['max_quantity'] ?? null,
                'location_in_warehouse' => $data['location_in_warehouse'] ?? $material->shelf_location,
                'location_in_warehouse_en' => $data['location_in_warehouse_en'] ?? $material->shelf_location_en,
            ]);
    }

    /**
     * توليد رقم عملية فريد
     * Generate unique transaction number
     */
    private function generateTransactionNumber(): string
    {
        $prefix = 'TRX-' . date('Y-m-d-');
        $sequence = WarehouseTransaction::where('transaction_number', 'like', $prefix . '%')
            ->count() + 1;

        return $prefix . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }
}
