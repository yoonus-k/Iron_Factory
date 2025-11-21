<?php

namespace App\Examples;

/**
 * مثال على كيفية استخدام نظام الإشعارات في Controllers
 * 
 * هذا الملف يوضح الأمثلة العملية لاستخدام NotificationService
 */

// ==========================================
// المثال 1: في WarehouseProductController
// ==========================================

class WarehouseProductControllerExample
{
    public function store(StoreMaterialRequest $request, NotificationService $notificationService)
    {
        // ... كود العملية
        
        // إنشاء المادة
        $material = Material::create($request->validated());

        // إرسال إشعار للمدير
        $manager = User::where('role', 'manager')->first();
        if ($manager) {
            $notificationService->notifyMaterialAdded($manager, $material, auth()->user());
        }

        // إرسال إشعار لمدير المستودع
        $warehouseManager = User::where('role', 'warehouse_manager')->first();
        if ($warehouseManager) {
            $notificationService->notifyMaterialAdded($warehouseManager, $material, auth()->user());
        }

        return redirect()->route('manufacturing.warehouse-products.show', $material->id)
                       ->with('success', 'تم إضافة المادة بنجاح');
    }

    public function update(UpdateMaterialRequest $request, $id, NotificationService $notificationService)
    {
        $material = Material::findOrFail($id);
        
        // ... كود التحديث
        
        $material->update($request->validated());

        // إرسال إشعار بالتحديث
        $notificationService->notifyMaterialUpdated(
            auth()->user(),
            $material,
            auth()->user()
        );

        return redirect()->back()->with('success', 'تم تحديث المادة بنجاح');
    }
}

// ==========================================
// المثال 2: في PurchaseInvoiceController
// ==========================================

class PurchaseInvoiceControllerExample
{
    public function store(Request $request, NotificationService $notificationService)
    {
        // ... كود الحفظ
        
        $invoice = PurchaseInvoice::create([
            // ... البيانات
        ]);

        // إرسال إشعار لمدير المشتريات
        $purchasingManager = User::where('department', 'purchasing')->first();
        if ($purchasingManager) {
            $notificationService->notifyPurchaseInvoiceCreated(
                $purchasingManager,
                $invoice,
                auth()->user()
            );
        }

        // إرسال إشعار للمدير
        $manager = User::where('role', 'manager')->first();
        if ($manager) {
            $notificationService->notifyPurchaseInvoiceCreated(
                $manager,
                $invoice,
                auth()->user()
            );
        }

        return redirect()->route('manufacturing.purchase-invoices.show', $invoice->id)
                       ->with('success', 'تم إنشاء الفاتورة بنجاح');
    }
}

// ==========================================
// المثال 3: في DeliveryNoteController
// ==========================================

class DeliveryNoteControllerExample
{
    public function store(Request $request, NotificationService $notificationService)
    {
        // ... كود الحفظ
        
        $deliveryNote = DeliveryNote::create([
            // ... البيانات
        ]);

        // إرسال إشعار لمدير المستودع
        $warehouseManager = User::where('role', 'warehouse_manager')->first();
        if ($warehouseManager) {
            $notificationService->notifyDeliveryNoteRegistered(
                $warehouseManager,
                $deliveryNote,
                auth()->user()
            );
        }

        return redirect()->route('manufacturing.warehouse-registration.show', $deliveryNote->id)
                       ->with('success', 'تم تسجيل الأذن بنجاح');
    }
}

// ==========================================
// المثال 4: في WarehouseRegistrationController
// ==========================================

class WarehouseRegistrationControllerExample
{
    public function transferToProduction(
        Request $request,
        DeliveryNote $deliveryNote,
        NotificationService $notificationService
    ) {
        $quantity = $request->input('quantity');
        
        // ... كود النقل
        
        // خصم من المستودع
        $deliveryNote->materialDetail->quantity -= $quantity;
        $deliveryNote->materialDetail->save();

        // إرسال إشعار لمدير الإنتاج
        $productionManager = User::where('department', 'production')->first();
        if ($productionManager) {
            $notificationService->notifyMoveToProduction(
                $productionManager,
                $deliveryNote,
                $quantity,
                auth()->user()
            );
        }

        // إرسال إشعار لمدير المستودع بالنقل
        $warehouseManager = User::where('role', 'warehouse_manager')->first();
        if ($warehouseManager) {
            $notificationService->notifyMoveToProduction(
                $warehouseManager,
                $deliveryNote,
                $quantity,
                auth()->user()
            );
        }

        return back()->with('success', 'تم النقل للإنتاج بنجاح');
    }

    public function checkWeightDiscrepancy(
        DeliveryNote $deliveryNote,
        NotificationService $notificationService
    ) {
        $expectedWeight = $deliveryNote->materialDetail->quantity;
        $actualWeight = $deliveryNote->actual_weight;
        $difference = $actualWeight - $expectedWeight;
        $percentageDiff = abs($difference / $expectedWeight * 100);

        // إذا كان الفرق أكثر من 5%
        if ($percentageDiff > 5) {
            // إرسال تحذير لمدير المستودع
            $manager = User::where('role', 'manager')->first();
            if ($manager) {
                $notificationService->notifyWeightDiscrepancy(
                    $manager,
                    $deliveryNote,
                    $difference,
                    auth()->user()
                );
            }
        }
    }

    public function checkDuplicateAttempt(
        DeliveryNote $deliveryNote,
        NotificationService $notificationService
    ) {
        $attemptCount = $deliveryNote->registration_attempts ?? 1;

        if ($attemptCount > 2) {
            // إرسال تحذير
            $manager = User::where('role', 'manager')->first();
            if ($manager) {
                $notificationService->notifyDuplicateAttempt(
                    $manager,
                    $deliveryNote,
                    $attemptCount,
                    auth()->user()
                );
            }
        }

        if ($attemptCount > 5) {
            // إرسال تحذير حرج
            $manager = User::where('role', 'manager')->first();
            if ($manager) {
                $notificationService->notifyMaxAttemptsExceeded(
                    $manager,
                    $deliveryNote,
                    auth()->user()
                );
            }
        }
    }
}

// ==========================================
// المثال 5: في MaterialMovementController
// ==========================================

class MaterialMovementControllerExample
{
    public function store(Request $request, NotificationService $notificationService)
    {
        // ... كود الحفظ
        
        $movement = MaterialMovement::create([
            // ... البيانات
        ]);

        // إرسال إشعار لمدير المستودع
        $warehouseManager = User::where('role', 'warehouse_manager')->first();
        if ($warehouseManager) {
            $notificationService->notifyMaterialMovement(
                $warehouseManager,
                $movement,
                auth()->user()
            );
        }

        return redirect()->back()->with('success', 'تم تسجيل الحركة بنجاح');
    }
}

// ==========================================
// المثال 6: استخدام الإشعارات المخصصة
// ==========================================

class CustomNotificationExample
{
    public function sendCustomAlert(NotificationService $notificationService)
    {
        $user = User::find(1);
        
        // إشعار مخصص
        $notificationService->notifyCustom(
            $user,
            'تنبيه مهم',
            'هناك مشكلة في نظام المخزون تحتاج لمراجعة',
            'system_alert',
            'danger',
            'feather icon-alert-triangle',
            route('dashboard'),
            ['problem' => 'inventory_issue']
        );
    }
}

// ==========================================
// المثال 7: الحصول على الإشعارات
// ==========================================

class NotificationRetrievalExample
{
    public function getMyNotifications(NotificationService $notificationService)
    {
        $user = auth()->user();
        
        // الحصول على آخر 50 إشعار
        $notifications = $notificationService->getUserNotifications($user, 50);
        
        // الحصول على الإشعارات غير المقروءة فقط
        $unreadNotifications = $notificationService->getUserNotifications($user, 50, true);
        
        // عدد الإشعارات غير المقروءة
        $unreadCount = $notificationService->getUnreadCount($user);
    }
}

// ==========================================
// المثال 8: التعامل مع الإشعارات
// ==========================================

class NotificationInteractionExample
{
    public function markAsRead(NotificationService $notificationService, $notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->user_id == auth()->id()) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(NotificationService $notificationService)
    {
        $notificationService->markAllAsRead(auth()->user());
    }

    public function deleteOldNotifications(NotificationService $notificationService)
    {
        // حذف الإشعارات التي مضى عليها أكثر من 30 يوم
        $deleted = $notificationService->deleteOldNotifications(30);
        return "تم حذف {$deleted} إشعار";
    }
}

// ==========================================
// نصائح مهمة:
// ==========================================

/*
1. تأكد من أن المستخدم موجود قبل إرسال الإشعار
   if ($user) {
       $notificationService->notify...($user, ...);
   }

2. استخدم صور الأيقونات من Font Awesome
   'feather icon-plus-circle'
   'feather icon-edit-2'
   'feather icon-check-circle'

3. اختر اللون المناسب:
   - success: للعمليات الناجحة
   - info: للمعلومات العامة
   - warning: للتنبيهات
   - danger: للأخطاء الحرجة

4. استخدم رابط الإجراء للانتقال المباشر
   route('resource.show', $resource->id)

5. اضف بيانات إضافية في metadata
   ['supplier_name' => $supplier->name, 'total' => $total]
*/
