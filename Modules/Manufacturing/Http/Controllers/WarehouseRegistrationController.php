<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\Material;
use App\Models\RegistrationLog;
use App\Models\MaterialMovement;
use App\Models\User;
use App\Services\DuplicatePreventionService;
use App\Services\WarehouseTransferService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Manufacturing\Entities\MaterialBatch;
use Modules\Manufacturing\Entities\BarcodeSetting;

class WarehouseRegistrationController extends Controller
{
    /**
     * Duplicate Prevention Service
     */
    protected $duplicateService;

    /**
     * Warehouse Transfer Service
     */
    protected $warehouseService;

    public function __construct(
        DuplicatePreventionService $duplicateService,
        WarehouseTransferService $warehouseService
    ) {
        $this->duplicateService = $duplicateService;
        $this->warehouseService = $warehouseService;
    }

    /**
     * Show list of unregistered shipments
     * يعرض البضاعة الواردة والصادرة
     */
    public function pending()
    {
        // البضاعة الداخلة (الواردة) غير المسجلة
        $incomingUnregistered = DeliveryNote::where('type', 'incoming')
            ->where('registration_status', 'not_registered')
            ->with(['supplier', 'recordedBy', 'material'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // البضاعة الداخلة (الواردة) المسجلة
        $incomingRegistered = DeliveryNote::where('type', 'incoming')
            ->where('registration_status', '!=', 'not_registered')
            ->with(['supplier', 'registeredBy', 'material'])
            ->orderBy('registered_at', 'desc')
            ->paginate(15);

        // البضاعة الخارجة (الصادرة)
        $outgoing = DeliveryNote::where('type', 'outgoing')
            ->with(['destination', 'recordedBy', 'material'])
            ->orderBy('delivery_date', 'desc')
            ->paginate(15);

        return view('manufacturing::warehouses.registration.pending', compact(
            'incomingUnregistered',
            'incomingRegistered',
            'outgoing'
        ));
    }

    /**
     * Show registration form
     */
    public function create(DeliveryNote $deliveryNote)
    {
        // تحقق من أن التسليمة لم تُسجل بعد
        if ($deliveryNote->registration_status !== 'not_registered') {
            return redirect()->route('manufacturing.warehouse.registration.show', $deliveryNote)
                ->with('info', 'هذه التسليمة مسجلة بالفعل. إذا أردت التعديل، اضغط على زر التعديل');
        }
$matrials = Material::all();
        // تحقق من وجود تسجيل سابق لنفس الشحنة
        $previousLog = $this->checkForDuplicateRegistration($deliveryNote);

        return view('manufacturing::warehouses.registration.create', compact('deliveryNote', 'previousLog', 'matrials'));
    }

    /**
     * Store registration data
     */
    public function store(Request $request, DeliveryNote $deliveryNote)
    {
        
        // تحقق من أن التسليمة لم تُسجل بعد
        if ($deliveryNote->registration_status !== 'not_registered') {
            return back()->with('error', 'هذه التسليمة مسجلة بالفعل');
        }

        // التحقق من تجاوز الحد الأقصى للمحاولات
        if ($this->duplicateService->hasExceededMaxAttempts($deliveryNote)) {
            return back()->with('error', 'تم تجاوز الحد الأقصى لمحاولات التسجيل. الرجاء التواصل مع الإدارة.');
        }

        // التحقق من صحة البيانات
        $validated = $request->validate([
            'actual_weight' => 'required|numeric|min:0.01',
            'material_id' => 'required|exists:materials,id',
            'unit_id' => 'required|exists:units,id',
            'location' => 'required|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'use_existing' => 'nullable|boolean',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ], [
            'actual_weight.required' => 'الوزن الفعلي مطلوب',
            'actual_weight.numeric' => 'الوزن يجب أن يكون رقم',
            'actual_weight.min' => 'الوزن يجب أن يكون أكبر من صفر',
            'material_id.required' => 'المادة مطلوبة',
            'material_id.exists' => 'المادة غير موجودة',
            'unit_id.required' => 'الوحدة مطلوبة',
            'unit_id.exists' => 'الوحدة غير موجودة',
            'location.required' => 'موقع التخزين مطلوب',
        ]);

        try {
            DB::beginTransaction();

            // تحديث البيانات
            $deliveryNote->update([
                'actual_weight' => $validated['actual_weight'],
                'delivery_quantity' => $validated['actual_weight'], // ✅ استخدام الوزن كقيمة للكمية
                'delivered_weight' => $validated['actual_weight'], // ✅ نفس القيمة
                'material_id' => $validated['material_id'], // ✅ إضافة material_id
                'registration_status' => 'registered',
                'registered_by' => Auth::id(),
                'registered_at' => now(),
                'registration_attempts' => ($deliveryNote->registration_attempts ?? 0) + 1,
                'deduplicate_key' => $this->duplicateService->generateUniqueKey($deliveryNote),
            ]);

            // إنشاء سجل التسجيل
            $log = RegistrationLog::create([
                'delivery_note_id' => $deliveryNote->id,
                'weight_recorded' => $validated['actual_weight'],
                'supplier_id' => $deliveryNote->supplier_id,
                'material_id' => $validated['material_id'],
                'unit_id' => $validated['unit_id'],
                'location' => $validated['location'],
                'registered_by' => Auth::id(),
                'registered_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // احفظ معرف السجل الأخير
            $deliveryNote->update(['last_registration_log_id' => $log->id]);

            // تسجيل المحاولة
            $this->duplicateService->logAttempt($deliveryNote, $validated, true);

            // ✅ تحديث الأذن لضمان وجود delivery_quantity قبل التسجيل في المستودع
            $deliveryNote->refresh(); // إعادة تحميل البيانات المحدثة

            // تسجيل البضاعة في المستودع تلقائياً (للبضاعة الواردة)
            if ($deliveryNote->isIncoming()) {
                $this->warehouseService->registerDeliveryToWarehouse(
                    $deliveryNote,
                    Auth::id(),
                    $validated['material_id'],  // تمرير material_id
                    $validated['unit_id']       // تمرير unit_id
                );

                // === إنشاء سجل دفعة المواد وتوليد الباركود ===
                // جلب إعدادات الباركود للمواد الخام
                $barcodeSetting = BarcodeSetting::where('type', 'raw_material')->first();
                if (!$barcodeSetting) {
                    throw new \Exception('إعدادات الباركود للمواد الخام غير موجودة!');
                }
                $year = date('Y');
                $nextNumber = $barcodeSetting->current_number + 1;
                $numberStr = str_pad($nextNumber, $barcodeSetting->padding, '0', STR_PAD_LEFT);
                $batchCode = str_replace(
                    ['{prefix}', '{year}', '{number}'],
                    [$barcodeSetting->prefix, $year, $numberStr],
                    $barcodeSetting->format
                );
                // تحديث رقم الباركود في الإعدادات
                $barcodeSetting->current_number = $nextNumber;
                $barcodeSetting->save();

                // إنشاء سجل الدفعة
                $batch = MaterialBatch::create([
                    'material_id' => $validated['material_id'],
                    'unit_id' => $validated['unit_id'],
                    'batch_code' => $batchCode,
                    'initial_quantity' => $validated['actual_weight'],
                    'available_quantity' => $validated['actual_weight'],
                    'batch_date' => now()->toDateString(),
                    'warehouse_id' => $deliveryNote->warehouse_id,
                    'unit_price' => null,
                    'total_value' => null,
                    'notes' => $validated['notes'] ?? null,
                ]);

                // ✅ تسجيل الحركة في جدول material_movements مع batch_id
                MaterialMovement::create([
                    'movement_number' => MaterialMovement::generateMovementNumber(),
                    'movement_type' => 'incoming',
                    'source' => 'registration',
                    'delivery_note_id' => $deliveryNote->id,
                    'material_detail_id' => $deliveryNote->material_detail_id,
                    'material_id' => $validated['material_id'],
                    'batch_id' => $batch->id,
                    'unit_id' => $validated['unit_id'],
                    'quantity' => $validated['actual_weight'],
                    'to_warehouse_id' => $deliveryNote->warehouse_id,
                    'supplier_id' => $deliveryNote->supplier_id,
                    'description' => 'تسجيل بضاعة واردة - أذن رقم ' . ($deliveryNote->note_number ?? $deliveryNote->id),
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => Auth::id(),
                    'movement_date' => now(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'status' => 'completed',
                ]);
            }

            DB::commit();

            $message = $deliveryNote->isIncoming()
                ? 'تم تسجيل البضاعة بنجاح وإدخالها للمستودع!'
                : 'تم تسجيل البضاعة بنجاح!';

            return redirect()->route('manufacturing.warehouse.registration.show', $deliveryNote)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            // تسجيل المحاولة الفاشلة
            $this->duplicateService->logAttempt($deliveryNote, $request->all(), false);

            return back()->with('error', 'حدث خطأ أثناء التسجيل: ' . $e->getMessage());
        }
    }

    /**
     * Show registration details
     */
    public function show(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load([
            'supplier',
            'registeredBy',
            'registrationLogs',
            'purchaseInvoice',
            'purchaseInvoice.supplier',
            'reconciliationLogs',
            'material',
            'materialDetail'  // أضفنا MaterialDetail
        ]);

        // الحصول على معلومات منع التكرار
        $statusInfo = $this->duplicateService->getStatusDescription($deliveryNote);
        $allAttempts = $this->duplicateService->getAllAttempts($deliveryNote);
        $attemptComparison = $this->duplicateService->compareAttempts($deliveryNote);

        // الحصول على معلومات المستودع من MaterialDetail
        $warehouseSummary = $this->warehouseService->getWarehouseSummary($deliveryNote);
        $movementHistory = $this->warehouseService->getMovementHistory($deliveryNote);

        // التحقق من إمكانية النقل (يجب أن تكون هناك MaterialDetail مع كمية متاحة)
        $canMoveToProduction = $deliveryNote->materialDetail
            && $deliveryNote->materialDetail->quantity > 0
            && $deliveryNote->isIncoming();

        // التحقق من إمكانية التعديل (يجب أن تكون الشحنة غير مقفلة وغير مسجلة أو الحالة سماح بالتعديل)
        $canEdit = !$deliveryNote->is_locked && $deliveryNote->registration_status !== 'completed';

        return view('manufacturing::warehouses.registration.show', compact(
            'deliveryNote',
            'statusInfo',
            'allAttempts',
            'attemptComparison',
            'warehouseSummary',
            'movementHistory',
            'canMoveToProduction',
            'canEdit'
        ));
    }

    /**
     * Show production transfer interface
     * واجهة لاختيار الكمية المراد نقلها للإنتاج
     */
    public function showTransferForm(DeliveryNote $deliveryNote)
    {
        // التحقق من وجود MaterialDetail
        if (!$deliveryNote->material_detail_id || !$deliveryNote->materialDetail) {
            return back()->with('error', 'هذه البضاعة لم تسجل في المستودع بعد');
        }

        $availableQuantity = $deliveryNote->materialDetail->quantity ?? 0;

        if ($availableQuantity <= 0) {
            return back()->with('error', 'لا توجد كمية متاحة للنقل');
        }

        return view('manufacturing::warehouses.registration.transfer', compact('deliveryNote', 'availableQuantity'));
    }

    /**
     * Transfer to production
     * نقل البضاعة للإنتاج مع خصم من المستودع
     */
    // public function transferToProduction(Request $request, DeliveryNote $deliveryNote)
    // {
    //     // التحقق من وجود MaterialDetail
    //     if (!$deliveryNote->material_detail_id || !$deliveryNote->materialDetail) {
    //         return back()->with('error', 'هذه البضاعة لم تسجل في المستودع بعد');
    //     }

    //     // التحقق من البيانات
    //     $validated = $request->validate([
    //         'quantity' => 'required|numeric|min:0.01',
    //         'notes' => 'nullable|string|max:500',
    //     ], [
    //         'quantity.required' => 'الكمية مطلوبة',
    //         'quantity.numeric' => 'الكمية يجب أن تكون رقم',
    //         'quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         // نقل البضاعة للإنتاج
    //         $this->warehouseService->transferToProduction(
    //             $deliveryNote,
    //             (float)$validated['quantity'],
    //             Auth::id(),
    //             $validated['notes'] ?? null
    //         );

    //         // ✅ تسجيل حركة النقل للإنتاج
    //         $movement = MaterialMovement::create([
    //             'movement_number' => MaterialMovement::generateMovementNumber(),
    //             'movement_type' => 'to_production',
    //             'source' => 'production',
    //             'delivery_note_id' => $deliveryNote->id,
    //             'material_detail_id' => $deliveryNote->material_detail_id,
    //             'material_id' => $deliveryNote->material_id,
    //             'unit_id' => $deliveryNote->materialDetail->unit_id ?? null,
    //             'quantity' => (float)$validated['quantity'],
    //             'from_warehouse_id' => $deliveryNote->warehouse_id,
    //             'destination' => 'الإنتاج',
    //             'description' => 'نقل بضاعة للإنتاج - أذن رقم ' . ($deliveryNote->note_number ?? $deliveryNote->id),
    //             'notes' => $validated['notes'] ?? null,
    //             'created_by' => Auth::id(),
    //             'movement_date' => now(),
    //             'ip_address' => request()->ip(),
    //             'user_agent' => request()->userAgent(),
    //             'status' => 'completed',
    //         ]);

    //         // تحديث الكمية المتبقية في الدفعة إذا كان هناك batch_id
    //         if ($movement->batch_id) {
    //             $batch = MaterialBatch::find($movement->batch_id);
    //             if ($batch) {
    //                 $batch->available_quantity = max(0, $batch->available_quantity - (float)$validated['quantity']);
    //                 $batch->save();
    //             }
    //         }

    //         DB::commit();

    //         return redirect()->route('manufacturing.warehouse.registration.show', $deliveryNote)
    //             ->with('success', 'تم نقل البضاعة للإنتاج بنجاح!');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
    //     }
    // }

    public function transferToProduction(Request $request, DeliveryNote $deliveryNote)
{
    // التحقق من وجود MaterialDetail
    if (!$deliveryNote->material_detail_id || !$deliveryNote->materialDetail) {
        return back()->with('error', 'هذه البضاعة لم تسجل في المستودع بعد');
    }

    // التحقق من البيانات
    $validated = $request->validate([
        'quantity' => 'required|numeric|min:0.01',
        'notes' => 'nullable|string|max:500',
    ], [
        'quantity.required' => 'الكمية مطلوبة',
        'quantity.numeric' => 'الكمية يجب أن تكون رقم',
        'quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
    ]);

    try {
        DB::beginTransaction();

        $quantityToTransfer = (float)$validated['quantity'];
        $remainingQuantity = $quantityToTransfer;

        // البحث عن الدفعات المتاحة (FIFO)
        $batches = MaterialBatch::where('material_id', $deliveryNote->material_id)
            ->where('available_quantity', '>', 0)
            ->orderBy('batch_date')
            ->get();

        if ($batches->isEmpty()) {
            return back()->with('error', 'لا توجد كمية كافية في المخزن للمنتج المحدد');
        }

        foreach ($batches as $batch) {
            if ($remainingQuantity <= 0) {
                break; // تم تغطية الكمية المطلوبة
            }

            // تحديد كمية النقل من هذه الدفعة
            $transferQuantity = min($batch->available_quantity, $remainingQuantity);

            // إنشاء حركة النقل للإنتاج لكل دفعة
            MaterialMovement::create([
                'movement_number' => MaterialMovement::generateMovementNumber(),
                'movement_type' => 'to_production',
                'source' => 'production',
                'delivery_note_id' => $deliveryNote->id,
                'material_detail_id' => $deliveryNote->material_detail_id,
                'material_id' => $deliveryNote->material_id,
                'unit_id' => $deliveryNote->materialDetail->unit_id ?? null,
                'batch_id' => $batch->id,
                'quantity' => $transferQuantity,
                'from_warehouse_id' => $deliveryNote->warehouse_id,
                'destination' => 'الإنتاج',
                'description' => 'نقل بضاعة للإنتاج - أذن رقم ' . ($deliveryNote->note_number ?? $deliveryNote->id),
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
                'movement_date' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'completed',
            ]);

            // تحديث الكمية المتبقية في الدفعة
            $batch->available_quantity -= $transferQuantity;
            $batch->save();

            // خصم الكمية المنقولة من الكمية المطلوبة
            $remainingQuantity -= $transferQuantity;
        }

        if ($remainingQuantity > 0) {
            DB::rollBack();
            return back()->with('error', 'لا توجد كمية كافية في المخزن لتغطية الطلب بالكامل.');
        }

        DB::commit();

        return redirect()->route('manufacturing.warehouse.registration.show', $deliveryNote)
            ->with('success', 'تم نقل البضاعة للإنتاج بنجاح!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
    }
}

    /**
     * Move to production (لاحتفاظ الرجعية)
     * نقل كامل الكمية المتاحة للإنتاج
     */
    public function moveToProduction(Request $request, DeliveryNote $deliveryNote)
    {
        // التحقق من وجود MaterialDetail
        if (!$deliveryNote->material_detail_id || !$deliveryNote->materialDetail) {
            return back()->with('error', 'هذه البضاعة لم تسجل في المستودع بعد');
        }

        try {
            // نقل كامل الكمية المتبقية
            $availableQuantity = $deliveryNote->materialDetail->quantity ?? 0;

            if ($availableQuantity <= 0) {
                return back()->with('error', 'لا توجد كمية متاحة للنقل');
            }

            $this->warehouseService->transferToProduction(
                $deliveryNote,
                $availableQuantity,
                Auth::id(),
                'نقل فوري للإنتاج'
            );

            return redirect()->route('manufacturing.warehouse.registration.show', $deliveryNote)
                ->with('success', 'تم نقل البضاعة إلى الإنتاج بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Lock shipment
     */
    public function lock(Request $request, DeliveryNote $deliveryNote)
    {
        $validated = $request->validate([
            'lock_reason' => 'required|string|max:255',
        ]);

        try {
            $deliveryNote->update([
                'is_locked' => true,
                'lock_reason' => $validated['lock_reason'],
            ]);

            return back()->with('success', 'تم تقفيل الشحنة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Unlock shipment
     */
    public function unlock(DeliveryNote $deliveryNote)
    {
        try {
            $deliveryNote->update([
                'is_locked' => false,
                'lock_reason' => null,
            ]);

            return back()->with('success', 'تم فتح الشحنة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * التحقق من وجود تسجيل سابق لنفس الشحنة
     */
    private function checkForDuplicateRegistration(DeliveryNote $deliveryNote): ?RegistrationLog
    {
        return $this->duplicateService->getLastAttempt($deliveryNote);
    }
}
