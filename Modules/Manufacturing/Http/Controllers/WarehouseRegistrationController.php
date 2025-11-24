<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\Material;
use App\Models\RegistrationLog;
use App\Models\MaterialMovement;
use App\Models\WarehouseRecord;
use App\Models\User;
use App\Services\DuplicatePreventionService;
use App\Services\WarehouseTransferService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Manufacturing\Entities\MaterialBatch;
use App\Models\BarcodeSetting;
use App\Traits\StoresNotifications;

class WarehouseRegistrationController extends Controller
{
    use StoresNotifications;

    /**
     * Duplicate Prevention Service
     */
    protected $duplicateService;

    /**
     * Warehouse Transfer Service
     */
    protected $warehouseService;

    /**
     * Notification Service
     */
    protected $notificationService;

    public function __construct(
        DuplicatePreventionService $duplicateService,
        WarehouseTransferService $warehouseService,
        NotificationService $notificationService
    ) {
        $this->duplicateService = $duplicateService;
        $this->warehouseService = $warehouseService;
        $this->notificationService = $notificationService;
    }

    /**
     * Show list of unregistered shipments
     * يعرض البضاعة الواردة والصادرة مع فلترة التاريخ
     */
    public function pending(Request $request)
    {
        // الحصول على نطاق التاريخ من الطلب
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $sortBy = $request->get('sort_by', 'created_at'); // الحقل الافتراضي للترتيب
        $sortOrder = $request->get('sort_order', 'desc'); // ترتيب تنازلي افتراضي

        // الحصول على عدد السجلات لكل صفحة (مع قيمة افتراضية آمنة)
        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [15, 25, 50, 100]) ? $perPage : 15;

        // التحقق من صحة ترتيب الترتيب
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'desc';

        // بناء الاستعلام الأساسي للبضاعة الواردة غير المسجلة
        $unregisteredQuery = DeliveryNote::where('type', 'incoming')
            ->where('registration_status', 'not_registered')
            ->with(['supplier', 'recordedBy', 'material']);

        // تطبيق فلتر التاريخ
        if ($fromDate) {
            $unregisteredQuery->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $unregisteredQuery->whereDate('created_at', '<=', $toDate);
        }

        $incomingUnregistered = $unregisteredQuery
            ->orderBy($sortBy === 'date' ? 'created_at' : $sortBy, $sortOrder)
            ->paginate($perPage)
            ->appends($request->query());

        // بناء الاستعلام الأساسي للبضاعة الواردة المسجلة (لم تنقل بالكامل بعد)
        // ✅ فقط الشحنات التي تحتوي على كمية متبقية > 0
        $registeredQuery = DeliveryNote::where('type', 'incoming')
            ->where('registration_status', '!=', 'not_registered')
            ->where(function($query) {
                // إما quantity_remaining > 0 أو لم تتم معالجة الكمية بعد
                $query->where(function($q) {
                    $q->where('quantity_remaining', '>', 0);
                })
                ->orWhere(function($q) {
                    // شحنات مسجلة لم ننقل منها شيء بعد
                    $q->where('quantity_used', '=', 0)
                      ->where('quantity', '>', 0)
                      ->whereNull('quantity_remaining');
                });
            })
            ->with(['supplier', 'registeredBy', 'material', 'materialDetail']);

        // تطبيق فلتر التاريخ
        if ($fromDate) {
            $registeredQuery->whereDate('registered_at', '>=', $fromDate);
        }
        if ($toDate) {
            $registeredQuery->whereDate('registered_at', '<=', $toDate);
        }

        $incomingRegistered = $registeredQuery
            ->orderBy($sortBy === 'date' ? 'registered_at' : $sortBy, $sortOrder)
            ->paginate($perPage)
            ->appends($request->query());

        // بناء الاستعلام الأساسي للبضاعة المنقولة للإنتاج بالكامل
        // ✅ تظهر فقط عندما يكون quantity_remaining = 0
        $productionQuery = DeliveryNote::where('type', 'incoming')
            ->where(function($query) {
                $query->where('quantity_remaining', '<=', 0)
                      ->whereNotNull('quantity_remaining');
            })
            ->orWhere(function($query) {
                $query->where('registration_status', 'in_production')
                      ->where('quantity', '>', 0);
            })
            ->with(['supplier', 'registeredBy', 'material', 'materialDetail']);

        // تطبيق فلتر التاريخ
        if ($fromDate) {
            $productionQuery->whereDate('registered_at', '>=', $fromDate);
        }
        if ($toDate) {
            $productionQuery->whereDate('registered_at', '<=', $toDate);
        }

        $movedToProduction = $productionQuery
            ->orderBy($sortBy === 'date' ? 'registered_at' : $sortBy, $sortOrder)
            ->paginate($perPage)
            ->appends($request->query());

        // بناء الاستعلام الأساسي للبضاعة الخارجة (الصادرة)
        $outgoingQuery = DeliveryNote::where('type', 'outgoing')
            ->with(['destination', 'recordedBy', 'material']);

        // تطبيق فلتر التاريخ
        if ($fromDate) {
            $outgoingQuery->whereDate('delivery_date', '>=', $fromDate);
        }
        if ($toDate) {
            $outgoingQuery->whereDate('delivery_date', '<=', $toDate);
        }

        $outgoing = $outgoingQuery
            ->orderBy($sortBy === 'date' ? 'delivery_date' : $sortBy, $sortOrder)
            ->paginate($perPage)
            ->appends($request->query());

        // نقل معاملات التاريخ للـ View
        $appliedFilters = [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ];

        return view('manufacturing::warehouses.registration.pending', compact(
            'incomingUnregistered',
            'incomingRegistered',
            'movedToProduction',
            'outgoing',
            'appliedFilters'
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
        $materials = Material::all();
        // تحقق من وجود تسجيل سابق لنفس الشحنة
        $previousLog = $this->checkForDuplicateRegistration($deliveryNote);

        return view('manufacturing::warehouses.registration.create', compact('deliveryNote', 'previousLog', 'materials'));
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
            'actual_weight' => 'nullable|numeric|min:0.01',
            'material_id' => 'nullable|exists:materials,id',
            'unit_id' => 'nullable|exists:units,id',
            'location' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'use_existing' => 'nullable|boolean',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ], [

        ]);

        try {
            DB::beginTransaction();

            // تحضير بيانات التحديث - إضافة الكمية لكل أذن على حدة
            $updateData = [
                'actual_weight' => $validated['actual_weight'],
                'delivery_quantity' => $validated['actual_weight'], // ✅ استخدام الوزن كقيمة للكمية
                'delivered_weight' => $validated['actual_weight'], // ✅ نفس القيمة
                'quantity' => $validated['actual_weight'], // ✅ الكمية الخاصة بهذه الأذن
                'quantity_remaining' => $validated['actual_weight'], // ✅ الكمية المتبقية من الأذن
                'quantity_used' => 0, // ✅ لم تُستخدم بعد
                'material_id' => $validated['material_id'], // ✅ إضافة material_id
                'registration_status' => 'registered',
                'registered_by' => Auth::id(),
                'registered_at' => now(),
                'registration_attempts' => ($deliveryNote->registration_attempts ?? 0) + 1,
                'deduplicate_key' => $this->duplicateService->generateUniqueKey($deliveryNote),
            ];

            // تحديث البيانات مرة واحدة فقط
            $deliveryNote->update($updateData);

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

            // احفظ معرف السجل الأخير مع batch_id (إن وجد)
            $updateDataWithLog = ['last_registration_log_id' => $log->id];

            // تسجيل المحاولة
            $this->duplicateService->logAttempt($deliveryNote, $validated, true);

            // تسجيل البضاعة في المستودع تلقائياً (للبضاعة الواردة)
            $batch = null;
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

                // إضافة batch_id إلى بيانات التحديث
                $updateDataWithLog['batch_id'] = $batch->id;

                // تحديث واحد فقط مع batch_id و last_registration_log_id
                $deliveryNote->update($updateDataWithLog);

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
            } else {
                // للبضاعة الخارجة، تحديث بسيط
                $deliveryNote->update($updateDataWithLog);
            }

            // ✅ حفظ البيانات في جدول warehouse_records
            WarehouseRecord::create([
                'delivery_note_id' => $deliveryNote->id,
                'material_id' => $validated['material_id'] ?? null,
                'warehouse_id' => $deliveryNote->warehouse_id,
                'supplier_id' => $deliveryNote->supplier_id,
                'type' => $deliveryNote->type,
                'record_number' => WarehouseRecord::generateRecordNumber(),
                'recorded_at' => now(),
                'quantity' => $validated['actual_weight'] ?? 0,
                'weight' => $validated['actual_weight'] ?? 0,
                'location' => $validated['location'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'completed',
                'recorded_by' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            // ✅ تخزين الإشعار
            $this->storeNotification(
                'delivery_note_registered',
                'تم تسجيل أذن تسليم',
                'تم تسجيل أذن التسليم رقم ' . $deliveryNote->note_number . ' برقم دفعة ' . ($batch?->batch_code ?? 'N/A'),
                'success',
                'fas fa-check-circle',
                route('manufacturing.warehouse.registration.show', $deliveryNote->id)
            );

            $message = $deliveryNote->isIncoming()
                ? 'تم تسجيل البضاعة بنجاح وإدخالها للمستودع!'
                : 'تم تسجيل البضاعة بنجاح!';

            // إذا كانت واردة، أضف رسالة الباركود
            if ($deliveryNote->isIncoming() && $batch !== null) {
                session()->flash('batch_code', $batch->batch_code);
                session()->flash('batch_id', $batch->id);
                $message .= ' رقم الدفعة: ' . $batch->batch_code;
            }

            // إرسال إشعارات التسجيل
            try {
                $users = User::where('id', '!=', Auth::id())->get();
                foreach ($users as $user) {
                    $this->notificationService->notifyDeliveryNoteRegistered(
                        $user,
                        $deliveryNote->fresh(),
                        Auth::user()
                    );
                }
            } catch (\Exception $notifError) {
                \Illuminate\Support\Facades\Log::warning('Failed to send registration notifications: ' . $notifError->getMessage());
            }

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
            'materialDetail',
            'materialBatch'  // ✅ إضافة معلومات الدفعة والباركود
        ]);

        // الحصول على معلومات منع التكرار
        $statusInfo = $this->duplicateService->getStatusDescription($deliveryNote);
        $allAttempts = $this->duplicateService->getAllAttempts($deliveryNote);
        $attemptComparison = $this->duplicateService->compareAttempts($deliveryNote);

        // الحصول على معلومات المستودع من MaterialDetail
        $warehouseSummary = $this->warehouseService->getWarehouseSummary($deliveryNote);
        $movementHistory = $this->warehouseService->getMovementHistory($deliveryNote);

        // التحقق من إمكانية النقل (يجب أن تكون هناك كمية مسجلة ولم تُنقل بالكامل)
        $registeredQuantity = $deliveryNote->quantity ?? 0;
        $transferredQuantity = $deliveryNote->quantity_used ?? 0;
        $availableQuantity = $registeredQuantity - $transferredQuantity;

        $canMoveToProduction = $deliveryNote->isIncoming()
            && $registeredQuantity > 0
            && $availableQuantity > 0
            && $deliveryNote->registration_status !== 'completed';

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
        // تحميل العلاقات المطلوبة بقوة
        $deliveryNote->load(['materialDetail', 'materialDetail.unit', 'material']);

        // التحقق من وجود كمية مسجلة
        if (!$deliveryNote->quantity || $deliveryNote->quantity <= 0) {
            return back()->with('error', 'لم يتم تسجيل كمية من الكريت بعد');
        }

        // الكمية المتاحة = الكمية المسجلة - الكمية المنقولة بالفعل
        $registeredQuantity = $deliveryNote->quantity;
        $transferredQuantity = $deliveryNote->quantity_used ?? 0;
        $availableQuantity = $registeredQuantity - $transferredQuantity;

        if ($availableQuantity <= 0) {
            return back()->with('error', 'تم نقل كل الكمية المسجلة بالفعل للإنتاج');
        }

        // الحصول على كمية المستودع من MaterialDetail
        $warehouseQuantity = 0;
        $warehouseUnit = 'كيلو';

        if ($deliveryNote->materialDetail) {
            $warehouseQuantity = $deliveryNote->materialDetail->quantity ?? 0;
            $warehouseUnit = $deliveryNote->materialDetail->unit?->unit_name ?? 'كيلو';
        } else if ($deliveryNote->material_id) {
            // إذا لم يكن materialDetail محدد، ابحث عن أحدث واحد للمادة
            $materialDetail = \App\Models\MaterialDetail::where('material_id', $deliveryNote->material_id)
                ->where('warehouse_id', $deliveryNote->warehouse_id)
                ->with('unit')
                ->latest()
                ->first();

            if ($materialDetail) {
                $warehouseQuantity = $materialDetail->quantity ?? 0;
                $warehouseUnit = $materialDetail->unit?->unit_name ?? 'كيلو';
            }
        }

        // عرض نموذج نقل الكمية
        return view('manufacturing::warehouses.registration.transfer-form', compact(
            'deliveryNote',
            'availableQuantity',
            'registeredQuantity',
            'transferredQuantity',
            'warehouseQuantity',
            'warehouseUnit'
        ));
    }

    /**
     * Transfer to production
     * نقل البضاعة للإنتاج مع خصم من المستودع
     * ✅ الآن يدعم النقل الجزئي
     */
    public function transferToProduction(Request $request, DeliveryNote $deliveryNote)
    {
        // التحقق من وجود كمية مسجلة
        if (!$deliveryNote->quantity || $deliveryNote->quantity <= 0) {
            return back()->with('error', 'لم يتم تسجيل كمية من الكريت بعد');
        }

        // حساب الكمية المتاحة
        $registeredQuantity = $deliveryNote->quantity;
        $transferredQuantity = $deliveryNote->quantity_used ?? 0;
        $availableQuantity = $registeredQuantity - $transferredQuantity;

        // التحقق من البيانات - ✅ مع قيد max للكمية المتاحة فقط
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.01|max:' . $availableQuantity, // ✅ لا يمكن نقل أكثر من المتاح
            'notes' => 'nullable|string|max:500',
        ], [
            'quantity.required' => 'الكمية مطلوبة',
            'quantity.numeric' => 'الكمية يجب أن تكون رقم',
            'quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
            'quantity.max' => 'الكمية المدخلة تتجاوز الكمية المتاحة (' . $availableQuantity . ' كيلو)! لا يمكن نقل أكثر من الكمية المسجلة في أذن التسليم.',
        ]);

        $transferQuantity = (float)$validated['quantity'];
        $isFullTransfer = abs($transferQuantity - $availableQuantity) < 0.001;

        try {
            DB::beginTransaction();

            // نقل البضاعة للإنتاج
            $this->warehouseService->transferToProduction(
                $deliveryNote,
                $transferQuantity,
                Auth::id(),
                $validated['notes'] ?? null
            );

            // جلب batch_id من DeliveryNote
            $batchId = $deliveryNote->batch_id;

            // ✅ تسجيل حركة النقل للإنتاج مع batch_id
            MaterialMovement::create([
                'movement_number' => MaterialMovement::generateMovementNumber(),
                'movement_type' => 'to_production',
                'source' => 'production',
                'delivery_note_id' => $deliveryNote->id,
                'material_detail_id' => $deliveryNote->material_detail_id,
                'material_id' => $deliveryNote->material_id,
                'batch_id' => $batchId,
                'unit_id' => $deliveryNote->materialDetail->unit_id ?? null,
                'quantity' => $transferQuantity,
                'from_warehouse_id' => $deliveryNote->warehouse_id,
                'destination' => 'الإنتاج',
                'description' => 'نقل بضاعة للإنتاج - أذن رقم ' . ($deliveryNote->note_number ?? $deliveryNote->id) . ($isFullTransfer ? ' (نقل كامل)' : ' (نقل جزئي)'),
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
                'movement_date' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'completed',
            ]);

            // ✅ تحديث الكمية المتبقية في الدفعة
            if ($batchId) {
                $batch = MaterialBatch::find($batchId);
                if ($batch) {
                    $newAvailableQty = max(0, $batch->available_quantity - $transferQuantity);
                    $batch->available_quantity = $newAvailableQty;
                    $batch->save();
                }
            }

            // ✅ تحديث كمية الأذن المنقولة
            $newQuantityUsed = ($deliveryNote->quantity_used ?? 0) + $transferQuantity;
            $deliveryNote->update([
                'quantity_used' => $newQuantityUsed,
                'quantity_remaining' => max(0, $deliveryNote->quantity - $newQuantityUsed)
            ]);

            // ✅ تحديث حالة التسجيل فقط إذا كان النقل كاملاً
            if ($isFullTransfer) {
                $deliveryNote->update(['registration_status' => 'in_production']);
            }

            // ✅ رسالة نجاح
            if ($isFullTransfer) {
                $successMessage = '✅ تم نقل البضاعة بالكامل للإنتاج بنجاح!';
            } else {
                $successMessage = '✅ تم نقل ' . number_format($transferQuantity, 2) . ' كيلو للإنتاج! المتبقي: ' . number_format($availableQuantity - $transferQuantity, 2) . ' كيلو';
            }

            DB::commit();

            // ✅ تخزين الإشعار
            $this->storeNotification(
                'delivery_transferred_to_production',
                'تم نقل البضاعة للإنتاج',
                'تم نقل ' . number_format($transferQuantity, 2) . ' من أذن التسليم رقم ' . $deliveryNote->note_number . ' للإنتاج',
                'info',
                'fas fa-arrow-right',
                route('manufacturing.warehouse.registration.show', $deliveryNote->id)
            );

            // إرسال إشعار بنقل البضاعة للإنتاج
            $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
            foreach ($managers as $manager) {
                $this->notificationService->notifyMoveToProduction(
                    $manager,
                    $deliveryNote->fresh(),
                    $transferQuantity,
                    Auth::user()
                );
            }

            return redirect()->route('manufacturing.warehouse.registration.show', $deliveryNote)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء النقل: ' . $e->getMessage());
        }
    }

    public function moveToProduction(Request $request, DeliveryNote $deliveryNote)
    {
        // التحقق من وجود كمية مسجلة
        if (!$deliveryNote->quantity || $deliveryNote->quantity <= 0) {
            return back()->with('error', 'لم يتم تسجيل كمية من الكريت بعد');
        }

        try {
            // نقل كامل الكمية المتبقية
            $registeredQuantity = $deliveryNote->quantity;
            $transferredQuantity = $deliveryNote->quantity_used ?? 0;
            $availableQuantity = $registeredQuantity - $transferredQuantity;

            if ($availableQuantity <= 0) {
                return back()->with('error', 'تم نقل كل الكمية المسجلة بالفعل للإنتاج');
            }

            $this->warehouseService->transferToProduction(
                $deliveryNote,
                $availableQuantity,
                Auth::id(),
                'نقل فوري للإنتاج'
            );

            // تحديث كمية الأذن المنقولة والمتبقية
            $deliveryNote->update([
                'quantity_used' => $registeredQuantity,
                'quantity_remaining' => 0,
                'registration_status' => 'in_production'
            ]);

            // ✅ تخزين الإشعار
            $this->storeNotification(
                'delivery_moved_to_production',
                'نقل فوري للإنتاج',
                'تم نقل أذن التسليم رقم ' . $deliveryNote->note_number . ' بالكامل للإنتاج',
                'info',
                'fas fa-arrow-right',
                route('manufacturing.warehouse.registration.show', $deliveryNote->id)
            );

            // إرسال إشعار بنقل البضاعة للإنتاج
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyMoveToProduction(
                        $manager,
                        $deliveryNote->fresh(),
                        $availableQuantity,
                        Auth::user()
                    );
                }
            } catch (\Exception $notifError) {
                \Illuminate\Support\Facades\Log::warning('Failed to send move to production notifications: ' . $notifError->getMessage());
            }

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

            // ✅ تخزين الإشعار
            $this->storeNotification(
                'shipment_locked',
                'تم تقفيل أذن التسليم',
                'تم تقفيل أذن التسليم رقم ' . $deliveryNote->note_number . ' للسبب: ' . $validated['lock_reason'],
                'warning',
                'fas fa-lock',
                route('manufacturing.warehouse.registration.show', $deliveryNote->id)
            );

            // إرسال إشعار بالتقفيل
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'تم تقفيل الشحنة',
                        'تم تقفيل الشحنة برقم الأذن: ' . $deliveryNote->note_number . ' للسبب: ' . $validated['lock_reason'],
                        'lock_shipment',
                        'warning',
                        'feather icon-lock',
                        route('manufacturing.warehouse.registration.show', $deliveryNote->id)
                    );
                }
            } catch (\Exception $notifError) {
                \Illuminate\Support\Facades\Log::warning('Failed to send lock notifications: ' . $notifError->getMessage());
            }

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

            // ✅ تخزين الإشعار
            $this->storeNotification(
                'shipment_unlocked',
                'تم فتح أذن التسليم',
                'تم فتح أذن التسليم رقم ' . $deliveryNote->note_number . ' وإزالة التقفيل',
                'success',
                'fas fa-unlock',
                route('manufacturing.warehouse.registration.show', $deliveryNote->id)
            );

            // إرسال إشعار بفتح الشحنة
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'تم فتح الشحنة',
                        'تم فتح الشحنة برقم الأذن: ' . $deliveryNote->note_number,
                        'unlock_shipment',
                        'success',
                        'feather icon-unlock',
                        route('manufacturing.warehouse.registration.show', $deliveryNote->id)
                    );
                }
            } catch (\Exception $notifError) {
                \Illuminate\Support\Facades\Log::warning('Failed to send unlock notifications: ' . $notifError->getMessage());
            }

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
