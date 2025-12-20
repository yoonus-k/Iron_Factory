<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\Material;
use App\Models\RegistrationLog;
use App\Models\MaterialMovement;
use App\Models\WarehouseRecord;
use App\Models\ProductionStage;
use App\Models\ProductionConfirmation;
use App\Models\Notification;
use App\Models\User;
use App\Services\DuplicatePreventionService;
use App\Services\WarehouseTransferService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Manufacturing\Entities\MaterialBatch;
use App\Models\ProductTracking;
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

        // ✅ حماية عمليات النقل والتحويل بالصلاحيات
        $this->middleware('permission:WAREHOUSE_REGISTRATION_TRANSFER', [
            'only' => ['showTransferForm', 'transferToProduction', 'moveToProduction', 'showProductionBarcode']
        ]);

        $this->middleware('permission:WAREHOUSE_REGISTRATION_LOCK', [
            'only' => ['lock', 'unlock']
        ]);
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
            return redirect()->route('manufacturing.warehouse.registration.show', ['deliveryNote' => $deliveryNote->id])
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
            'coil_number' => 'nullable|string|max:100', // ✅ رقم الكويل
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
                    'coil_number' => $validated['coil_number'] ?? null, // ✅ رقم الكويل
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

            return redirect()->route('manufacturing.warehouse.registration.show', ['deliveryNote' => $deliveryNote->id])
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
        \Log::info('showTransferForm called', ['id' => $deliveryNote->id]);

        // تحميل العلاقات المطلوبة بقوة
        $deliveryNote->load(['materialDetail', 'materialDetail.unit', 'material', 'supplier', 'materialBatch']);

        // التحقق من وجود كمية مسجلة
        if (!$deliveryNote->quantity || $deliveryNote->quantity <= 0) {
            return redirect()->route('manufacturing.warehouse.registration.show', ['deliveryNote' => $deliveryNote->id])
                ->with('error', 'لم يتم تسجيل كمية من الكريت بعد');
        }

        // الكمية المتاحة = الكمية المسجلة - الكمية المنقولة بالفعل
        $registeredQuantity = $deliveryNote->quantity;
        $transferredQuantity = $deliveryNote->quantity_used ?? 0;
        $availableQuantity = $registeredQuantity - $transferredQuantity;

        if ($availableQuantity <= 0) {
            return redirect()->route('manufacturing.warehouse.registration.show', ['deliveryNote' => $deliveryNote->id])
                ->with('error', 'تم نقل كل الكمية المسجلة بالفعل للإنتاج');
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
        // تحميل العلاقات المطلوبة
        $deliveryNote->load(['materialDetail', 'materialDetail.unit', 'material', 'supplier']);

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
            'production_stage' => 'required|string|exists:production_stages,stage_code', // ✅ المرحلة الإنتاجية
            'assigned_to' => 'required|exists:users,id', // ✅ الموظف المستلم
            'notes' => 'nullable|string|max:500',
        ], [
            'quantity.required' => 'الكمية مطلوبة',
            'quantity.numeric' => 'الكمية يجب أن تكون رقم',
            'quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
            'quantity.max' => 'الكمية المدخلة تتجاوز الكمية المتاحة (' . $availableQuantity . ' كيلو)! لا يمكن نقل أكثر من الكمية المسجلة في أذن التسليم.',
            'production_stage.required' => 'يجب اختيار المرحلة الإنتاجية',
            'production_stage.exists' => 'المرحلة المحددة غير موجودة',
            'assigned_to.required' => 'يجب اختيار الموظف المستلم',
            'assigned_to.exists' => 'الموظف المحدد غير موجود',
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

            // ✅✅ نظام تقسيم الباركود الذكي (Barcode Splitting System)
            $barcodeSetting = BarcodeSetting::where('type', 'raw_material')->first();
            $productionBarcode = null;
            $remainingBarcode = null;

            if ($batchId) {
                $batch = MaterialBatch::find($batchId);
                if ($batch && $barcodeSetting) {
                    $originalBarcode = $batch->batch_code;
                    $newAvailableQty = max(0, $batch->available_quantity - $transferQuantity);

                    // 🔹 السيناريو 1: نقل كامل (استخدام نفس الباركود)
                    if ($isFullTransfer) {
                        $productionBarcode = $originalBarcode; // نفس الباركود ينتقل للإنتاج

                        // تحديث حالة الدفعة الأصلية
                        $batch->available_quantity = 0;
                        $batch->status = 'consumed'; // استُهلكت بالكامل
                        $batch->latest_production_barcode = $productionBarcode;
                        $batch->save();

                        Log::info('✅ نقل كامل - نفس الباركود', [
                            'original_barcode' => $originalBarcode,
                            'production_barcode' => $productionBarcode,
                            'quantity' => $transferQuantity,
                        ]);

                        // 🔹 نقل كامل: حركة واحدة فقط
                        MaterialMovement::create([
                            'movement_number' => MaterialMovement::generateMovementNumber(),
                            'movement_type' => 'to_production',
                            'source' => 'production',
                            'delivery_note_id' => $deliveryNote->id,
                            'material_detail_id' => $deliveryNote->material_detail_id,
                            'material_id' => $deliveryNote->material_id,
                            'unit_id' => $batch->unit_id ?? ($deliveryNote->materialDetail->unit_id ?? null),
                            'quantity' => $transferQuantity,
                            'from_warehouse_id' => $deliveryNote->warehouse_id,
                            'destination' => 'الإنتاج',
                            'description' => '✅ نقل كامل للإنتاج - باركود: ' . $originalBarcode,
                            'notes' => 'نقل كامل الدفعة للإنتاج بنفس الباركود',
                            'created_by' => Auth::id(),
                            'movement_date' => now(),
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'status' => 'completed',
                        ]);

                        // تسجيل التتبع للنقل الكامل
                        ProductTracking::create([
                            'barcode' => $originalBarcode,
                            'stage' => 'warehouse',
                            'action' => 'transferred_to_production',
                            'input_barcode' => $originalBarcode,
                            'output_barcode' => $productionBarcode,
                            'input_weight' => $transferQuantity,
                            'output_weight' => $transferQuantity,
                            'waste_amount' => 0,
                            'waste_percentage' => 0,
                            'worker_id' => Auth::id(),
                            'notes' => '🏭 نقل كامل للإنتاج بنفس الباركود: ' . $originalBarcode . ' (' . $transferQuantity . ' كجم)',
                            'metadata' => json_encode([
                                'action_type' => 'full_transfer_to_production',
                                'barcode' => $originalBarcode,
                                'batch_id' => $batchId,
                                'transferred_quantity' => $transferQuantity,
                                'delivery_note_id' => $deliveryNote->id,
                            ]),
                        ]);
                    }
                    // 🔹 السيناريو 2: نقل جزئي (تقسيم الباركود)
                    else {
                        // 1️⃣ إنشاء باركود جديد للكمية المنقولة للإنتاج
                        $nextNumber1 = $barcodeSetting->getNextNumber();
                        $productionBarcode = $barcodeSetting->generateBarcode($nextNumber1);

                        // 2️⃣ إنشاء باركود جديد للكمية المتبقية في المستودع
                        $nextNumber2 = $barcodeSetting->getNextNumber();
                        $remainingBarcode = $barcodeSetting->generateBarcode($nextNumber2);

                        // 3️⃣ تحديث الدفعة الأصلية - تقليل الكمية فقط (الباركود يبقى كما هو)
                        $originalInitialQuantity = $batch->initial_quantity;
                        $batch->available_quantity = 0; // تم استهلاك كل الكمية (جزء للإنتاج وجزء للباركود الجديد)
                        $batch->status = 'consumed'; // مستهلك بالكامل
                        $batch->latest_production_barcode = $productionBarcode;
                        $batch->notes = ($batch->notes ?? '') . ' | تم التقسيم: ' . $transferQuantity . ' كجم للإنتاج (' . $productionBarcode . ') + ' . $newAvailableQty . ' كجم للمستودع (' . $remainingBarcode . ')';
                        $batch->save();

                        // 4️⃣ إنشاء دفعة جديدة للكمية المتبقية في المستودع
                        $remainingBatch = MaterialBatch::create([
                            'material_id' => $batch->material_id,
                            'unit_id' => $batch->unit_id,
                            'batch_code' => $remainingBarcode, // الباركود الجديد للكمية المتبقية
                            'coil_number' => $batch->coil_number,
                            'initial_quantity' => $newAvailableQty,
                            'available_quantity' => $newAvailableQty,
                            'batch_date' => now()->toDateString(),
                            'warehouse_id' => $batch->warehouse_id,
                            'unit_price' => $batch->unit_price,
                            'total_value' => $batch->unit_price ? ($batch->unit_price * $newAvailableQty) : null,
                            'status' => 'available',
                            'notes' => 'تم تقسيمه من الباركود الأصلي: ' . $originalBarcode . ' - كمية متبقية في المستودع',
                        ]);

                        // 5️⃣ إنشاء دفعة جديدة للكمية المنقولة للإنتاج
                        $productionBatch = MaterialBatch::create([
                            'material_id' => $batch->material_id,
                            'unit_id' => $batch->unit_id,
                            'batch_code' => $productionBarcode, // الباركود الجديد للإنتاج
                            'coil_number' => $batch->coil_number,
                            'initial_quantity' => $transferQuantity,
                            'available_quantity' => $transferQuantity,
                            'batch_date' => now()->toDateString(),
                            'warehouse_id' => null, // في الإنتاج الآن
                            'unit_price' => $batch->unit_price,
                            'total_value' => $batch->unit_price ? ($batch->unit_price * $transferQuantity) : null,
                            'status' => 'in_production',
                            'notes' => 'تم تقسيمه من الباركود الأصلي: ' . $originalBarcode . ' - كمية منقولة للإنتاج',
                        ]);

                        // 6️⃣ تسجيل التقسيم الأصلي في product_tracking
                        ProductTracking::create([
                            'barcode' => $originalBarcode,
                            'stage' => 'warehouse',
                            'action' => 'split',
                            'input_barcode' => $originalBarcode,
                            'output_barcode' => $productionBarcode . ',' . $remainingBarcode,
                            'input_weight' => $originalInitialQuantity,
                            'output_weight' => $originalInitialQuantity, // نفس الوزن لأنه تقسيم (لا يوجد فقد)
                            'waste_amount' => 0,
                            'waste_percentage' => 0,
                            'worker_id' => Auth::id(),
                            'notes' => '🔄 تقسيم الدفعة: ' . $originalBarcode . ' ← إنتاج: ' . $productionBarcode . ' (' . $transferQuantity . ' كجم) + مستودع: ' . $remainingBarcode . ' (' . $newAvailableQty . ' كجم)',
                            'metadata' => json_encode([
                                'split_type' => 'partial_transfer',
                                'original_barcode' => $originalBarcode,
                                'original_batch_id' => $batch->id,
                                'production_barcode' => $productionBarcode,
                                'production_batch_id' => $productionBatch->id,
                                'remaining_barcode' => $remainingBarcode,
                                'remaining_batch_id' => $remainingBatch->id,
                                'production_quantity' => $transferQuantity,
                                'remaining_quantity' => $newAvailableQty,
                                'delivery_note_id' => $deliveryNote->id,
                            ]),
                        ]);

                        // 7️⃣ تسجيل التتبع للباركود المتبقي في المستودع
                        ProductTracking::create([
                            'barcode' => $remainingBarcode,
                            'stage' => 'warehouse',
                            'action' => 'warehouse_remaining',
                            'input_barcode' => $originalBarcode,
                            'output_barcode' => $remainingBarcode,
                            'input_weight' => $originalInitialQuantity,
                            'output_weight' => $newAvailableQty, // الكمية المتبقية فقط
                            'waste_amount' => 0,
                            'waste_percentage' => 0,
                            'worker_id' => Auth::id(),
                            'notes' => '📦 باركود جديد للكمية المتبقية في المستودع: ' . $remainingBarcode . ' (' . $newAvailableQty . ' كجم) - مقسم من: ' . $originalBarcode,
                            'metadata' => json_encode([
                                'action_type' => 'warehouse_remaining_after_split',
                                'original_barcode' => $originalBarcode,
                                'original_batch_id' => $batch->id,
                                'new_barcode' => $remainingBarcode,
                                'new_batch_id' => $remainingBatch->id,
                                'remaining_quantity' => $newAvailableQty,
                                'transferred_to_production' => $transferQuantity,
                                'delivery_note_id' => $deliveryNote->id,
                            ]),
                        ]);

                        // 8️⃣ تسجيل التتبع للباركود المنقول للإنتاج
                        ProductTracking::create([
                            'barcode' => $productionBarcode,
                            'stage' => 'warehouse',
                            'action' => 'transferred_to_production',
                            'input_barcode' => $originalBarcode,
                            'output_barcode' => $productionBarcode,
                            'input_weight' => $originalInitialQuantity,
                            'output_weight' => $transferQuantity, // الكمية المنقولة للإنتاج فقط
                            'waste_amount' => 0,
                            'waste_percentage' => 0,
                            'worker_id' => Auth::id(),
                            'notes' => '🏭 نقل للإنتاج: ' . $productionBarcode . ' (' . $transferQuantity . ' كجم) - مقسم من: ' . $originalBarcode,
                            'metadata' => json_encode([
                                'action_type' => 'production_transfer_after_split',
                                'original_barcode' => $originalBarcode,
                                'original_batch_id' => $batch->id,
                                'production_barcode' => $productionBarcode,
                                'production_batch_id' => $productionBatch->id,
                                'transferred_quantity' => $transferQuantity,
                                'remaining_in_warehouse' => $newAvailableQty,
                                'delivery_note_id' => $deliveryNote->id,
                            ]),
                        ]);

                        Log::info('✅ نقل جزئي - تقسيم الباركود', [
                            'original_barcode' => $originalBarcode,
                            'original_batch_id' => $batch->id,
                            'production_barcode' => $productionBarcode,
                            'production_quantity' => $transferQuantity,
                            'production_batch_id' => $productionBatch->id,
                            'remaining_barcode' => $remainingBarcode,
                            'remaining_quantity' => $newAvailableQty,
                            'remaining_batch_id' => $remainingBatch->id,
                        ]);

                        // 🔹 1️⃣ حركة تحديث الكمية - إنشاء باركود جديد للكمية المتبقية في المستودع
                        MaterialMovement::create([
                            'movement_number' => MaterialMovement::generateMovementNumber(),
                            'movement_type' => 'adjustment',
                            'source' => 'production',
                            'delivery_note_id' => $deliveryNote->id,
                            'material_detail_id' => $deliveryNote->material_detail_id,
                            'material_id' => $deliveryNote->material_id,
                            'unit_id' => $remainingBatch->unit_id ?? ($batch->unit_id ?? ($deliveryNote->materialDetail->unit_id ?? null)),
                            'quantity' => $newAvailableQty,
                            'to_warehouse_id' => $deliveryNote->warehouse_id,
                            'description' => '📦 تحديث الكمية - إنشاء باركود جديد',
                            'notes' => 'إنشاء باركود جديد للكمية المتبقية بعد النقل الجزئي: ' . $originalBarcode . ' → ' . $remainingBarcode . ' | الكمية: ' . $newAvailableQty . ' كجم (تم نقل ' . $transferQuantity . ' كجم للإنتاج)',
                            'reference_number' => $originalBarcode,
                            'created_by' => Auth::id(),
                            'movement_date' => now(),
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'status' => 'completed',
                        ]);

                        // 🔹 2️⃣ حركة النقل للإنتاج
                        MaterialMovement::create([
                            'movement_number' => MaterialMovement::generateMovementNumber(),
                            'movement_type' => 'to_production',
                            'source' => 'production',
                            'delivery_note_id' => $deliveryNote->id,
                            'material_detail_id' => $deliveryNote->material_detail_id,
                            'material_id' => $deliveryNote->material_id,
                            'unit_id' => $productionBatch->unit_id ?? ($batch->unit_id ?? ($deliveryNote->materialDetail->unit_id ?? null)),
                            'quantity' => $transferQuantity,
                            'from_warehouse_id' => $deliveryNote->warehouse_id,
                            'destination' => 'الإنتاج',
                            'description' => '🏭 نقل للإنتاج',
                            'notes' => 'نقل جزئي من الباركود الأصلي: ' . $originalBarcode . ' | باركود الإنتاج: ' . $productionBarcode . ' | الكمية: ' . $transferQuantity . ' كجم',
                            'reference_number' => $originalBarcode,
                            'created_by' => Auth::id(),
                            'movement_date' => now(),
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'status' => 'completed',
                        ]);
                    }

                    // ملاحظة: تم تسجيل التتبع داخل كل حالة (نقل كامل أو جزئي) بشكل منفصل ودقيق
                }
            }

            // ✅ الحصول على معلومات المرحلة
            $stage = ProductionStage::getByCode($validated['production_stage']);

            // ✅ تحديث كمية الأذن المنقولة وحفظ باركود الإنتاج والمرحلة
            $newQuantityUsed = ($deliveryNote->quantity_used ?? 0) + $transferQuantity;
            $deliveryNote->update([
                'quantity_used' => $newQuantityUsed,
                'quantity_remaining' => max(0, $deliveryNote->quantity - $newQuantityUsed),
                'production_barcode' => $productionBarcode, // ✅ حفظ باركود الإنتاج في الجدول
                'production_stage' => $validated['production_stage'], // ✅ المرحلة
                'production_stage_name' => $stage?->stage_name, // ✅ اسم المرحلة
                'assigned_to' => $validated['assigned_to'], // ✅ الموظف المستلم
                'transfer_status' => 'pending', // ✅ في انتظار التأكيد
                'transferred_by' => Auth::id(),
            ]);

            // ✅ إنشاء سجل تأكيد الإنتاج
            $confirmation = ProductionConfirmation::create([
                'delivery_note_id' => $deliveryNote->id,
                'batch_id' => $batchId,
                'stage_code' => $validated['production_stage'],
                'assigned_to' => $validated['assigned_to'],
                'status' => 'pending',
                'actual_received_quantity' => $transferQuantity, // ✅ حفظ الكمية المنقولة فعلياً
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // ✅ إنشاء إشعار للموظف المستلم
            $assignedUser = User::find($validated['assigned_to']);
            Notification::create([
                'user_id' => $validated['assigned_to'],
                'type' => 'production_transfer',
                'title' => '🔔 طلب استلام دفعة جديدة',
                'message' => "تم نقل دفعة {$batch->batch_code} بكمية {$transferQuantity} كجم إلى {$stage?->stage_name}. يرجى تأكيد الاستلام من صفحة التأكيدات المعلقة.",
                'icon' => 'fas fa-box',
                'color' => 'warning',
                'action_type' => 'transfer',
                'model_type' => 'ProductionConfirmation',
                'model_id' => $confirmation->id,
                'created_by' => Auth::id(),
                'action_url' => route('manufacturing.production.confirmations.pending'),
                'metadata' => json_encode([
                    'batch_code' => $batch->batch_code,
                    'quantity' => $transferQuantity,
                    'stage' => $stage?->stage_name,
                    'material_name' => $batch->material?->name_ar,
                    'confirmation_id' => $confirmation->id,
                ]),
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

            return redirect()->route('manufacturing.warehouse.registration.show', ['deliveryNote' => $deliveryNote->id])
                ->with('success', $successMessage)
                ->with('production_barcode', $productionBarcode); // ✅ إرسال باركود الإنتاج

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transfer to production failed: ' . $e->getMessage(), [
                'delivery_note_id' => $deliveryNote->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'حدث خطأ أثناء النقل: ' . $e->getMessage());
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

            return redirect()->route('manufacturing.warehouse.registration.show', ['deliveryNote' => $deliveryNote->id])
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
     * عرض صفحة باركود الإنتاج
     */
    public function showProductionBarcode(DeliveryNote $deliveryNote)
    {
        // التحقق من وجود باركود إنتاج
        if (!$deliveryNote->production_barcode) {
            return redirect()->route('manufacturing.warehouse.registration.show', $deliveryNote->id)
                ->with('error', 'لم يتم إنشاء باركود إنتاج لهذه الشحنة بعد');
        }

        $deliveryNote->load(['material', 'supplier', 'materialBatch']);

        return view('manufacturing::warehouses.registration.production-barcode', compact('deliveryNote'));
    }

    /**
     * التحقق من وجود تسجيل سابق لنفس الشحنة
     */
    private function checkForDuplicateRegistration(DeliveryNote $deliveryNote): ?RegistrationLog
    {
        return $this->duplicateService->getLastAttempt($deliveryNote);
    }
}
