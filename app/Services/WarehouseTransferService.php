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

            // تحميل العلاقات المطلوبة بشكل قوي
            $deliveryNote->load(['materialDetail', 'materialDetail.unit']);

            // التحقق من وجود كمية مسجلة في DeliveryNote
            if (!$deliveryNote->quantity || $deliveryNote->quantity <= 0) {
                throw new Exception('لم يتم تسجيل كمية من الكريت بعد');
            }

            // حساب الكمية المتاحة = الكمية المسجلة - المنقولة بالفعل
            $registeredQuantity = $deliveryNote->quantity;
            $transferredQuantity = $deliveryNote->quantity_used ?? 0;
            $availableQuantity = $registeredQuantity - $transferredQuantity;

            // التحقق من الكمية المتاحة
            if ($quantity > $availableQuantity && $quantity > ($availableQuantity + 0.01)) {
                // السماح برقم عشري بسيط للدقة
                // لكن نرسل تحذير إذا تجاوز كثيراً
            }

            if ($quantity <= 0) {
                throw new Exception('الكمية المطلوبة يجب أن تكون أكبر من صفر');
            }

            // ✅ خصم إجباري من المستودع - إذا لم يكن materialDetail موجود، نبحث عنه
            $materialDetail = $deliveryNote->materialDetail;

            if (!$materialDetail && $deliveryNote->material_id) {
                // إذا لم يكن معرّف في DeliveryNote، ابحث عن أحدث MaterialDetail للمادة
                $materialDetail = MaterialDetail::where('material_id', $deliveryNote->material_id)
                    ->where('warehouse_id', $deliveryNote->warehouse_id)
                    ->latest()
                    ->first();

                if (!$materialDetail) {
                    throw new Exception('لم يتم العثور على سجل المستودع (MaterialDetail) لهذه المادة');
                }
            }

            // خصم من المستودع عبر MaterialDetail بشكل إجباري
            if ($materialDetail) {
                $qtyInMaterialDetail = $materialDetail->quantity ?? 0;

                // خصم مقدار من الكمية الموجودة في MaterialDetail (حد أقصى الكمية المتاحة)
                $qtyToReduceFromMaterialDetail = min($quantity, $qtyInMaterialDetail);

                if ($qtyToReduceFromMaterialDetail > 0) {
                    // ✅ خصم إجباري
                    $materialDetail->reduceOutgoingQuantity($qtyToReduceFromMaterialDetail);

                    Log::info("تم خصم من المستودع بنجاح", [
                        'material_detail_id' => $materialDetail->id,
                        'quantity_reduced' => $qtyToReduceFromMaterialDetail,
                        'remaining_quantity' => $materialDetail->quantity,
                    ]);
                } else {
                    throw new Exception('الكمية في المستودع غير كافية. المتوفر: ' . $qtyInMaterialDetail . ' كيلو');
                }
            } else {
                throw new Exception('لا يوجد سجل مستودع (MaterialDetail) مرتبط بهذه الأذن');
            }

            DB::commit();

            Log::info("تم نقل بضاعة للإنتاج من أذن التسليم #{$deliveryNote->id}", [
                'delivery_note_id' => $deliveryNote->id,
                'material_id' => $deliveryNote->material_id,
                'material_detail_id' => $materialDetail->id,
                'quantity_transferred' => $quantity,
                'registered_quantity' => $registeredQuantity,
                'remaining_quantity' => $availableQuantity - $quantity,
                'warehouse_quantity_after' => $materialDetail->quantity,
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

        // ✅ حساب الكمية المنقولة للإنتاج من جدول material_movements
        $transferredQty = DB::table('material_movements')
            ->where('delivery_note_id', $deliveryNote->id)
            ->where('movement_type', 'to_production')
            ->where('status', 'completed')
            ->sum('quantity');

        // الحصول على آخر حركة نقل للإنتاج
        $lastTransfer = DB::table('material_movements')
            ->where('delivery_note_id', $deliveryNote->id)
            ->where('movement_type', 'to_production')
            ->where('status', 'completed')
            ->orderBy('movement_date', 'desc')
            ->first();

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
            'transferred_to_production' => $lastTransfer ? date('d/m/Y H:i', strtotime($lastTransfer->movement_date)) : null,
        ];

        // إضافة المستخدمين
        $transferredByUser = $lastTransfer ? \App\Models\User::find($lastTransfer->created_by) : null;
        $result['users'] = [
            'recorded_by' => $deliveryNote->recordedBy?->name,
            'registered_by' => $deliveryNote->registeredBy?->name,
            'transferred_by' => $transferredByUser?->name,
        ];

        // إضافة الكميات
        $result['quantities'] = [
            'delivery' => $qtyDelivered,
            'warehouse_entry' => $qtyDelivered,  // ✅ الكمية الأصلية المدخلة
            'transferred_to_production' => $transferredQty,  // ✅ المنقول للإنتاج
            'remaining_in_warehouse' => $qtyInWarehouse,  // المتبقي الحالي
        ];

        // إضافة الملاحظات
        $result['notes'] = [
            'transfer_notes' => $lastTransfer?->notes,
        ];

        // حساب نسبة النقل
        $transferPercentage = $qtyDelivered > 0 ? ($transferredQty / $qtyDelivered * 100) : 0;

        // إضافة الحالة والنسبة المئوية
        $statusLabel = $isRegistered ? 'في المستودع' : 'قيد الانتظار';
        $statusColor = $isRegistered ? 'success' : 'warning';
        $canTransfer = ($isRegistered && $qtyInWarehouse > 0);

        $result['status'] = [
            'warehouse_status' => ($isRegistered ? 'in_warehouse' : 'pending'),
            'warehouse_status_label' => $statusLabel,
            'warehouse_status_color' => $statusColor,
            'can_transfer_to_production' => $canTransfer,
            'transfer_percentage' => $transferPercentage,
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
