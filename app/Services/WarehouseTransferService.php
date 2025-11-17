<?php

namespace App\Services;

use App\Models\DeliveryNote;
use App\Models\MaterialDetail;
use App\Models\Material;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * WarehouseTransferService
 * خدمة إدارة عمليات النقل والمستودع
 *
 * IMPORTANT: هذه الخدمة تعمل بشكل مباشر مع MaterialDetail
 * DeliveryNote هي فقط سجل الأذن (document)
 * MaterialDetail هي سجل المستودع الحقيقي (ledger)
 *
 * تعامل مع:
 * - تسجيل البضاعة في المستودع (MaterialDetail)
 * - نقل البضاعة للإنتاج (تقليل MaterialDetail)
 * - إدارة المخزون والكميات عبر MaterialDetail فقط
 */
class WarehouseTransferService
{
    /**
     * تسجيل البضاعة في المستودع بشكل تلقائي
     * عند الموافقة على أذن التسليم
     *
     * هذه الوظيفة تقوم بـ:
     * 1. الحصول على أو إنشاء MaterialDetail
     * 2. إضافة الكمية للمستودع عبر MaterialDetail
     * 3. ربط أذن التسليم بـ MaterialDetail
     */
    public function registerDeliveryToWarehouse(DeliveryNote $deliveryNote, int $userId, int $materialId = null, int $unitId = null): bool
    {
        try {
            DB::beginTransaction();

            // تحديد الكمية المراد تسجيلها
            $quantityToRegister = $deliveryNote->delivery_quantity ?? 0;

            if ($quantityToRegister <= 0) {
                throw new Exception('الكمية المدخلة غير صحيحة: ' . $quantityToRegister);
            }

            // الحصول على أو إنشاء MaterialDetail
            $materialDetail = $this->getOrCreateMaterialDetail($deliveryNote, $materialId, $unitId);

            // إضافة الكمية للمستودع عبر MaterialDetail
            if ($deliveryNote->isIncoming()) {
                $materialDetail->addIncomingQuantity(
                    $quantityToRegister,
                    $deliveryNote->delivered_weight ?? 0,
                    $deliveryNote->invoice_weight ?? 0
                );
            }

            // ربط أذن التسليم بـ MaterialDetail
            $deliveryNote->material_detail_id = $materialDetail->id;
            $deliveryNote->save();

            DB::commit();

            Log::info("تم تسجيل أذن التسليم #{$deliveryNote->id} في المستودع", [
                'delivery_note_id' => $deliveryNote->id,
                'material_detail_id' => $materialDetail->id,
                'quantity' => $quantityToRegister,
                'registered_by' => $userId,
                'timestamp' => now()
            ]);

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("خطأ في تسجيل البضاعة: " . $e->getMessage(), [
                'delivery_note_id' => $deliveryNote->id,
                'error' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * نقل البضاعة من المستودع للإنتاج
     *
     * هذه الوظيفة تقوم بـ:
     * 1. التحقق من وجود MaterialDetail مرتبطة بأذن التسليم
     * 2. تقليل الكمية من المستودع عبر MaterialDetail
     * 3. تسجيل العملية في السجل
     */
    public function transferToProduction(
        DeliveryNote $deliveryNote,
        float $quantity,
        int $userId,
        string $notes = null
    ): bool {
        try {
            DB::beginTransaction();

            // التحقق من وجود MaterialDetail
            if (!$deliveryNote->material_detail_id) {
                throw new Exception('لم يتم تسجيل هذه البضاعة في المستودع بعد');
            }

            $materialDetail = $deliveryNote->materialDetail;
            if (!$materialDetail) {
                throw new Exception('سجل المستودع المرتبط غير موجود');
            }

            // التحقق من الكمية المتاحة
            $availableQuantity = $materialDetail->quantity ?? 0;
            if ($quantity > $availableQuantity) {
                throw new Exception(
                    "الكمية المطلوبة ({$quantity}) أكبر من المتاحة ({$availableQuantity})"
                );
            }

            if ($quantity <= 0) {
                throw new Exception('الكمية المطلوبة يجب أن تكون أكبر من صفر');
            }

            // خصم من المستودع عبر MaterialDetail
            $materialDetail->reduceOutgoingQuantity($quantity);

            DB::commit();

            Log::info("تم نقل بضاعة للإنتاج من أذن التسليم #{$deliveryNote->id}", [
                'delivery_note_id' => $deliveryNote->id,
                'material_detail_id' => $materialDetail->id,
                'quantity_transferred' => $quantity,
                'remaining_quantity' => $materialDetail->quantity,
                'transferred_by' => $userId,
                'notes' => $notes,
                'timestamp' => now()
            ]);

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("خطأ في نقل البضاعة للإنتاج: " . $e->getMessage(), [
                'delivery_note_id' => $deliveryNote->id,
                'quantity' => $quantity,
                'error' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * الحصول على ملخص المستودع لأذن تسليم معينة
     * يقرأ من MaterialDetail بدلاً من DeliveryNote
     */
    public function getWarehouseSummary(DeliveryNote $deliveryNote): array
    {
        // الحصول على بيانات MaterialDetail
        $materialDetail = $deliveryNote->materialDetail;
        $isRegistered = ($materialDetail !== null);
        $qtyInWarehouse = ($materialDetail?->quantity ?? 0);
        $qtyDelivered = ($deliveryNote->delivery_quantity ?? 0);

        // بناء المصفوفة الرئيسية
        $result = [
            'delivery_note_id' => $deliveryNote->id,
            'note_number' => $deliveryNote->note_number,
            'type' => [
                'incoming' => $deliveryNote->isIncoming(),
                'outgoing' => $deliveryNote->isOutgoing(),
            ],
            'material' => [
                'id' => $deliveryNote->material_id,
                'name' => $deliveryNote->material?->name,
            ],
        ];

        // إضافة حالة المستودع
        $result['warehouse_status'] = [
            'is_registered' => $isRegistered,
            'quantity_in_warehouse' => $qtyInWarehouse,
            'quantity_delivered' => $qtyDelivered,
            'actual_weight' => ($materialDetail?->actual_weight ?? 0),
            'original_weight' => ($materialDetail?->original_weight ?? 0),
            'remaining_weight' => ($materialDetail?->remaining_weight ?? 0),
        ];

        // إضافة التواريخ
        $result['dates'] = [
            'delivery_date' => $deliveryNote->delivery_date?->format('Y-m-d H:i'),
            'registered_at' => $deliveryNote->registered_at?->format('Y-m-d H:i'),
            'registered_to_warehouse' => $deliveryNote->registered_at?->format('d/m/Y H:i'),
            'transferred_to_production' => null,  // سيتم تعديله إذا تم النقل
        ];

        // إضافة المستخدمين
        $result['users'] = [
            'recorded_by' => $deliveryNote->recordedBy?->name,
            'registered_by' => $deliveryNote->registeredBy?->name,
            'transferred_by' => null,  // سيتم تعديله إذا تم النقل
        ];

        // إضافة الكميات
        $result['quantities'] = [
            'delivery' => $qtyDelivered,
            'warehouse_entry' => $qtyInWarehouse,
            'transferred_to_production' => 0,
            'remaining_in_warehouse' => $qtyInWarehouse,
        ];

        // إضافة الملاحظات
        $result['notes'] = [
            'transfer_notes' => null,  // سيتم إضافة ملاحظات النقل إذا وجدت
        ];

        // إضافة الحالة والنسبة المئوية
        $statusLabel = $isRegistered ? 'في المستودع' : 'قيد الانتظار';
        $statusColor = $isRegistered ? 'success' : 'warning';
        $canTransfer = ($isRegistered && $qtyInWarehouse > 0);

        $result['status'] = [
            'warehouse_status' => ($isRegistered ? 'in_warehouse' : 'pending'),
            'warehouse_status_label' => $statusLabel,
            'warehouse_status_color' => $statusColor,
            'can_transfer_to_production' => $canTransfer,
            'transfer_percentage' => 0,
        ];

        return $result;
    }

    /**
     * الحصول على سجل حركات البضاعة من MaterialDetail
     */
    public function getMovementHistory(DeliveryNote $deliveryNote): array
    {
        $materialDetail = $deliveryNote->materialDetail;

        if (!$materialDetail) {
            return [];
        }

        $movements = [];

        // 1. دخول المستودع (من المادة)
        $movements[] = [
            'date' => $deliveryNote->registered_at ?? now(),
            'type' => 'warehouse_entry',
            'description' => 'دخول المستودع',
            'quantity' => $deliveryNote->delivery_quantity,
            'user' => $deliveryNote->registeredBy?->name ?? 'النظام',
            'status_badge' => 'success',
            'icon' => 'inbox'
        ];

        return $movements;
    }

    /**
     * الحصول على أو إنشاء MaterialDetail
     *
     * هذه الوظيفة المساعدة تقوم بـ:
     * 1. البحث عن MaterialDetail موجودة للمادة
     * 2. إنشاء واحدة جديدة إذا لم توجد
     */
    private function getOrCreateMaterialDetail(DeliveryNote $deliveryNote, int $materialId = null, int $unitId = null): MaterialDetail
    {
        // إذا كانت هناك material_detail_id بالفعل، استخدمها
        if ($deliveryNote->material_detail_id) {
            return $deliveryNote->materialDetail;
        }

        // استخدام material_id المُمرر أو من deliveryNote
        $mId = $materialId ?? $deliveryNote->material_id;

        if (!$mId) {
            throw new Exception('لم يتم تحديد المادة للبضاعة');
        }

        // تحديد warehouse_id من DeliveryNote
        // يكون إما من warehouse_id المباشر أو من materialDetail إذا وجدت
        $warehouseId = $deliveryNote->warehouse_id;

        if (!$warehouseId) {
            // البحث عن MaterialDetail موجودة للمادة لأخذ warehouse_id منها
            $existingDetail = MaterialDetail::where('material_id', $mId)
                ->latest('id')
                ->first();

            if ($existingDetail) {
                $warehouseId = $existingDetail->warehouse_id;
            }
        }

        // إذا لم نجد warehouse_id بعد، استخدم المستودع من Material إذا وجد
        if (!$warehouseId) {
            $material = Material::find($mId);
            if ($material && $material->warehouse_id) {
                $warehouseId = $material->warehouse_id;
            }
        }

        // التحقق من أن warehouse_id موجود
        if (!$warehouseId) {
            throw new Exception('لم يتم تحديد المستودع للبضاعة. تأكد من أن أذن التسليم لها warehouse_id أو أن المادة لها warehouse_id');
        }

        // البحث عن MaterialDetail موجودة للمادة والمستودع معاً
        $materialDetail = MaterialDetail::where('material_id', $mId)
            ->where('warehouse_id', $warehouseId)
            ->latest('id')
            ->first();

        // إذا لم توجد، أنشئ واحدة جديدة مع warehouse_id و material_id و unit_id
        if (!$materialDetail) {
            $materialDetail = MaterialDetail::create([
                'warehouse_id' => $warehouseId,
                'material_id' => $mId,
                'unit_id' => $unitId,  // إضافة unit_id إذا كان موجود
                'quantity' => 0,
                'actual_weight' => 0,
                'original_weight' => 0,
                'remaining_weight' => 0,
                'notes' => 'تم الإنشاء من تسجيل أذن التسليم #' . $deliveryNote->id
            ]);
        } elseif ($unitId && !$materialDetail->unit_id) {
            // تحديث unit_id إذا لم يكن موجود من قبل
            $materialDetail->update(['unit_id' => $unitId]);
        }

        return $materialDetail;
    }
}
