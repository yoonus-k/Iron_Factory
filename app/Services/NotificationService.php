<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\StageSuspension;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Create notification
     */
    public function create($user, $type, $title, $message, $actionType = null, $modelType = null,
                          $modelId = null, $color = 'info', $icon = null, $actionUrl = null, $metadata = [])
    {
        $userId = $user instanceof User ? $user->id : $user;
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_type' => $actionType,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'color' => $color,
            'icon' => $icon,
            'action_url' => $actionUrl,
            'created_by' => Auth::id() ?? 1,
            'metadata' => $metadata,
        ]);
    }

    /**
     * إرسال اشعار إضافة مادة جديدة
     */
    public function notifyMaterialAdded(User $user, $material, User $createdBy = null)
    {
        $createdByName = $createdBy ? $createdBy->name : 'النظام';
        $supplierName = $material->supplier ? $material->supplier->name : null;
        $materialCode = $material->material_code ?? null;

        return $this->create(
            $user,
            'material_added',
            'تم إضافة مادة جديدة',
            "تم إضافة المادة '{$material->name_ar}' بنجاح من قبل {$createdByName}",
            'create',
            'Material',
            $material->id,
            'success',
            'feather icon-plus-circle',
            route('manufacturing.warehouse-products.show', $material->id),
            [
                'material_code' => $materialCode,
                'supplier_name' => $supplierName,
            ]
        );
    }

    /**
     * Notify about material update
     */
    public function notifyMaterialUpdated($user, $material, $updatedBy = null)
    {
        $updatedByName = $updatedBy ? $updatedBy->name : 'النظام';
        $message = "تم تحديث المادة '" . $material->name_ar . "' من قبل " . $updatedByName;
        return $this->create(
            $user,
            'material_updated',
            'تم تحديث مادة',
            $message,
            'update',
            'Material',
            $material->id,
            'info',
            'feather icon-edit-2',
            route('manufacturing.warehouse-products.show', $material->id)
        );
    }

    /**
     * Notify about purchase invoice created
     */
    public function notifyPurchaseInvoiceCreated($user, $invoice, $createdBy = null)
    {
        $createdByName = $createdBy ? $createdBy->name : 'النظام';
        $supplierName = $invoice->supplier ? $invoice->supplier->name : 'غير معروف';
        $totalAmount = number_format($invoice->total_amount, 2);
        $message = "تم إنشاء فاتورة شراء رقم " . $invoice->invoice_number . " من المورد " . $supplierName . " بقيمة " . $totalAmount . " ريال من قبل " . $createdByName;

        return $this->create(
            $user,
            'purchase_invoice_created',
            'تم إنشاء فاتورة شراء جديدة',
            $message,
            'create',
            'PurchaseInvoice',
            $invoice->id,
            'success',
            'feather icon-file-plus',
            route('manufacturing.purchase-invoices.show', $invoice->id),
            [
                'invoice_number' => $invoice->invoice_number,
                'supplier_name' => $supplierName,
                'total_amount' => $invoice->total_amount,
                'items_count' => $invoice->items ? count($invoice->items) : 0,
            ]
        );
    }

    /**
     * Notify about delivery note registered
     */
    public function notifyDeliveryNoteRegistered($user, $deliveryNote, $registeredBy = null)
    {
        $type = $deliveryNote->type === 'incoming' ? 'واردة' : 'صادرة';
        $registeredByName = $registeredBy ? $registeredBy->name : 'النظام';
        $unit = $deliveryNote->materialDetail && $deliveryNote->materialDetail->unit ? $deliveryNote->materialDetail->unit->unit_symbol : '';
        $message = "تم تسجيل أذن توصيل رقم " . $deliveryNote->note_number . " (" . $type . ") بكمية " . $deliveryNote->delivered_weight . " " . $unit . " من قبل " . $registeredByName;

        return $this->create(
            $user,
            'delivery_note_registered',
            'تم تسجيل أذن توصيل ' . $type,
            $message,
            'register',
            'DeliveryNote',
            $deliveryNote->id,
            'success',
            'feather icon-check-circle',
            route('manufacturing.warehouse.registration.show', $deliveryNote->id),
            [
                'note_number' => $deliveryNote->note_number,
                'type' => $type,
                'quantity' => $deliveryNote->delivered_weight,
            ]
        );
    }

    /**
     * Notify about move to production
     */
    public function notifyMoveToProduction($user, $deliveryNote, $quantity, $movedBy = null)
    {
        $movedByName = $movedBy ? $movedBy->name : 'النظام';
        $unit = $deliveryNote->materialDetail && $deliveryNote->materialDetail->unit ? $deliveryNote->materialDetail->unit->unit_symbol : '';
        $materialName = $deliveryNote->material ? $deliveryNote->material->name_ar : 'غير معروفة';
        $message = "تم نقل كمية " . $quantity . " " . $unit . " من المادة '" . $materialName . "' إلى الإنتاج من قبل " . $movedByName;

        return $this->create(
            $user,
            'moved_to_production',
            'تم نقل مواد إلى الإنتاج',
            $message,
            'transfer',
            'DeliveryNote',
            $deliveryNote->id,
            'warning',
            'feather icon-arrow-right',
            route('manufacturing.warehouse.registration.show', $deliveryNote->id),
            [
                'quantity' => $quantity,
                'material_name' => $materialName,
            ]
        );
    }

    /**
     * Notify about material movement
     */
    public function notifyMaterialMovement($user, $movement, $createdBy = null)
    {
        $createdByName = $createdBy ? $createdBy->name : 'النظام';
        $materialName = $movement->material ? $movement->material->name_ar : 'غير معروفة';
        $unit = $movement->unit ? $movement->unit->unit_symbol : '';
        $fromWarehouse = $movement->fromWarehouse ? $movement->fromWarehouse->warehouse_name : 'خارج';
        $toWarehouse = $movement->toWarehouse ? $movement->toWarehouse->warehouse_name : 'خارج';
        $message = "تم تسجيل حركة للمادة '" . $materialName . "': " . $movement->quantity . " " . $unit . " من " . $fromWarehouse . " إلى " . $toWarehouse . " من قبل " . $createdByName;

        return $this->create(
            $user,
            'material_movement',
            'تم تسجيل حركة مستودع',
            $message,
            'movement',
            'MaterialMovement',
            $movement->id,
            'info',
            'feather icon-move',
            '#',
            [
                'material_name' => $materialName,
                'quantity' => $movement->quantity,
                'from_warehouse' => $fromWarehouse,
                'to_warehouse' => $toWarehouse,
            ]
        );
    }

    /**
     * Notify about weight discrepancy
     */
    public function notifyWeightDiscrepancy($user, $deliveryNote, $difference, $detectedBy = null)
    {
        $detectedByName = $detectedBy ? $detectedBy->name : 'النظام';
        $unit = $deliveryNote->materialDetail && $deliveryNote->materialDetail->unit ? $deliveryNote->materialDetail->unit->unit_symbol : '';
        $percentage = $deliveryNote->delivered_weight > 0 ? abs($difference / $deliveryNote->delivered_weight * 100) : 0;
        $message = "تم اكتشاف فرق في الوزن للأذن رقم " . $deliveryNote->note_number . ": الفرق " . $difference . " " . $unit . " (" . round($percentage, 2) . "%). تم الكشف بواسطة " . $detectedByName;

        return $this->create(
            $user,
            'weight_discrepancy',
            'تحذير: فرق في الوزن',
            $message,
            'discrepancy',
            'DeliveryNote',
            $deliveryNote->id,
            'danger',
            'feather icon-alert-triangle',
            route('manufacturing.warehouse.registration.show', $deliveryNote->id),
            [
                'note_number' => $deliveryNote->note_number,
                'difference' => $difference,
                'percentage' => $percentage,
            ]
        );
    }

    /**
     * Notify about duplicate attempt
     */
    public function notifyDuplicateAttempt($user, $deliveryNote, $attemptCount, $detectedBy = null)
    {
        $detectedByName = $detectedBy ? $detectedBy->name : 'النظام';
        $message = "تم اكتشاف محاولة تسجيل مكررة للأذن رقم " . $deliveryNote->note_number . ". هذه المحاولة رقم " . $attemptCount . " من قبل " . $detectedByName;

        return $this->create(
            $user,
            'duplicate_attempt',
            'تحذير: محاولة تسجيل مكررة',
            $message,
            'duplicate',
            'DeliveryNote',
            $deliveryNote->id,
            'warning',
            'feather icon-alert-circle',
            route('manufacturing.warehouse.registration.show', $deliveryNote->id),
            [
                'note_number' => $deliveryNote->note_number,
                'attempt_count' => $attemptCount,
            ]
        );
    }

    /**
     * Notify about max attempts exceeded
     */
    public function notifyMaxAttemptsExceeded($user, $deliveryNote, $detectedBy = null)
    {
        $detectedByName = $detectedBy ? $detectedBy->name : 'النظام';
        $message = "تم تجاوز الحد الأقصى للمحاولات للأذن رقم " . $deliveryNote->note_number . ". يرجى مراجعة المسؤول. تم الكشف بواسطة " . $detectedByName;

        return $this->create(
            $user,
            'max_attempts_exceeded',
            'تحذير حرج: تجاوز الحد الأقصى للمحاولات',
            $message,
            'error',
            'DeliveryNote',
            $deliveryNote->id,
            'danger',
            'feather icon-x-circle',
            route('manufacturing.warehouse.registration.show', $deliveryNote->id),
            [
                'note_number' => $deliveryNote->note_number,
            ]
        );
    }

    /**
     * Send custom notification
     */
    public function notifyCustom($user, $title, $message, $type = 'custom', $color = 'info', $icon = null, $actionUrl = null, $metadata = [])
    {
        return $this->create(
            $user,
            $type,
            $title,
            $message,
            null,
            null,
            null,
            $color,
            $icon,
            $actionUrl,
            $metadata
        );
    }

    /**
     * Notify admins when a stage is suspended due to waste limit exceed
     */
    public function notifyStageSuspensionTriggered(User $user, StageSuspension $suspension)
    {
        $stageName = $suspension->getStageName();
        $wastePercentage = number_format($suspension->waste_percentage, 2);
        $allowedPercentage = number_format($suspension->allowed_percentage, 2);

        $message = sprintf(
            'تم إيقاف %s للدفعة %s بسبب نسبة هدر %s%% (المسموح %s%%).',
            $stageName,
            $suspension->batch_barcode,
            $wastePercentage,
            $allowedPercentage
        );

        return $this->create(
            $user,
            'stage_waste_exceeded',
            'تنبيه: تم إيقاف ' . $stageName,
            $message,
            'suspend',
            'StageSuspension',
            $suspension->id,
            'danger',
            'feather icon-alert-triangle',
            route('stage-suspensions.index', ['stage' => $suspension->stage_number, 'status' => 'suspended']),
            [
                'suspension_id' => $suspension->id,
                'stage_number' => $suspension->stage_number,
                'batch_barcode' => $suspension->batch_barcode,
                'waste_percentage' => $suspension->waste_percentage,
                'allowed_percentage' => $suspension->allowed_percentage,
            ]
        );
    }

    /**
     * Notify admins when a suspension gets reviewed (approved/rejected)
     */
    public function notifyStageSuspensionReviewed(User $user, StageSuspension $suspension, string $action, ?User $reviewer = null)
    {
        $stageName = $suspension->getStageName();
        $reviewerName = $reviewer ? $reviewer->name : 'الإدارة';

        $isApproved = $action === 'approved';
        $title = $isApproved
            ? 'تمت الموافقة على استئناف ' . $stageName
            : 'تم رفض استئناف ' . $stageName;

        $message = $isApproved
            ? sprintf('تمت الموافقة على استئناف %s للدفعة %s بواسطة %s.', $stageName, $suspension->batch_barcode, $reviewerName)
            : sprintf('تم رفض استئناف %s للدفعة %s بواسطة %s.', $stageName, $suspension->batch_barcode, $reviewerName);

        $color = $isApproved ? 'success' : 'danger';
        $icon = $isApproved ? 'feather icon-check-circle' : 'feather icon-x-circle';

        return $this->create(
            $user,
            'stage_suspension_reviewed',
            $title,
            $message,
            'review',
            'StageSuspension',
            $suspension->id,
            $color,
            $icon,
            route('stage-suspensions.index', ['stage' => $suspension->stage_number]),
            [
                'suspension_id' => $suspension->id,
                'stage_number' => $suspension->stage_number,
                'batch_barcode' => $suspension->batch_barcode,
                'review_action' => $action,
            ]
        );
    }

    /**
     * Get user notifications
     */
    public function getUserNotifications($user, $limit = 50, $unreadOnly = false)
    {
        $query = Notification::where('user_id', $user->id)->latest();

        if ($unreadOnly) {
            $query->unread();
        }

        return $query->paginate($limit);
    }

    /**
     * Get unread count
     */
    public function getUnreadCount($user)
    {
        return Notification::where('user_id', $user->id)->unread()->count();
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead($user)
    {
        Notification::where('user_id', $user->id)->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Delete old notifications
     */
    public function deleteOldNotifications($daysOld = 30)
    {
        return Notification::where('created_at', '<', now()->subDays($daysOld))->delete();
    }
}
