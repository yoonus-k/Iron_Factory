<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialDetail;
use App\Models\MaterialType;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Models\OperationLog;
use App\Services\NotificationService;
use App\Traits\StoresNotifications;
use Modules\Manufacturing\Http\Requests\StoreMaterialRequest;
use Modules\Manufacturing\Http\Requests\UpdateMaterialRequest;
use Modules\Manufacturing\Services\MaterialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class WarehouseProductController extends Controller
{
    use StoresNotifications;

    private MaterialService $materialService;
    private NotificationService $notificationService;

    public function __construct(MaterialService $materialService, NotificationService $notificationService)
    {
        $this->materialService = $materialService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the warehouse products with filtering.
     */
    public function index(Request $request)
    {
        $query = Material::with(['supplier', 'unit', 'creator']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('batch_number', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('material_category', $request->get('category'));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Supplier filter
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->get('supplier_id'));
        }

        // ترتيب البيانات حسب الأحدث أولاً مع الباجنيشن
        $materials = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());
        $suppliers = Supplier::all();

        return view('manufacturing::warehouses.material.index', compact('materials', 'suppliers'));
    }

    /**
     * Show the form for creating a new warehouse product.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $units = Unit::all();
        $users = User::all();
        $materialTypes =MaterialType::all();

        return view('manufacturing::warehouses.material.create', compact('suppliers', 'units', 'users', 'materialTypes'));
    }

    /**
     * Store a newly created warehouse product in storage.
     */
    public function store(StoreMaterialRequest $request)
    {
        try {
            $validated = $request->validated();

            // إنشاء المادة عبر الـ Service
            $material = $this->materialService->createMaterial($validated);

            // ✅ حفظ الترجمات في جدول Translation للغات الأربع
            $this->saveTranslations($material, $validated);

            // إضافة الكمية الأولية إلى MaterialDetail
            if (isset($validated['warehouse_id']) && isset($validated['original_weight'])) {
                \App\Models\MaterialDetail::create([
                    'material_id' => $material->id,
                    'warehouse_id' => $validated['warehouse_id'],
                    'quantity' => $validated['original_weight'],
                    'original_weight' => $validated['original_weight'],    // ✅ جديد
                    'remaining_weight' => $validated['original_weight'],   // ✅ جديد
                    'unit_id' => $material->unit_id,           // ✅ استخدام وحدة المنتج
                    'min_quantity' => $validated['min_quantity'] ?? 0,
                    'max_quantity' => $validated['max_quantity'] ?? 999999,
                    'created_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                ]);

                // إنشاء أذن مخزنية (Delivery Note) عند الإنشاء
                $deliveryNote = \App\Models\DeliveryNote::create([
                    'note_number' => $this->generateDeliveryNoteNumber(),
                    'material_id' => $material->id,
                    'delivered_weight' => $validated['original_weight'],
                    'delivery_date' => now()->toDateString(),
                    'driver_name' => 'مادة جديدة',
                    'driver_name_en' => 'New Material',
                    'vehicle_number' => 'N/A',
                    'received_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                ]);
            }

            // تسجيل العملية - إجباري ولا يجب أن يفشل
            try {
                $this->logOperation(
                    'create',
                    'Create Material',
                    'تم إنشاء مادة جديدة: ' . $material->name_ar,
                    'materials',
                    $material->id,
                    null,
                    $material->toArray()
                );
            } catch (\Exception $logError) {
                // إذا فشل التسجيل، سجل الخطأ لكن استمر
                Log::error('Failed to log material creation: ' . $logError->getMessage(), [
                    'material_id' => $material->id,
                    'original_error' => $logError
                ]);
                // أعد رمي الاستثناء ليتم التعامل معه في catch الخارجية
                throw new \Exception('فشل تسجيل العملية: ' . $logError->getMessage());
            }

            // ✅ تخزين الإشعار
            $this->notifyCreate(
                'مادة',
                $material->name_ar,
                route('manufacturing.warehouse-products.show', $material->id)
            );

            // إرسال الإشعارات
            try {
                $this->createMaterialWithNotification($material);
            } catch (\Exception $notifError) {
                Log::warning('Failed to send notifications for material creation: ' . $notifError->getMessage());
                // لا نفشل العملية بسبب فشل الإشعارات
            }

            return redirect()->route('manufacturing.warehouse-products.index')
                           ->with('success', 'تم إضافة المادة بنجاح وتسجيل حركة المستودع');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error creating material: ' . $e->getMessage(), [
                'exception' => $e,
                'input' => $request->all()
            ]);

            // Return back with error message
            return redirect()->back()
                           ->withInput()
                           ->withErrors(['error' => 'فشل في حفظ المادة: ' . $e->getMessage()]);
        }
    }

    /**
     * اضافة مادة وإرسال إشعار
     */
    private function createMaterialWithNotification($material)
    {
        // إرسال إشعار لجميع المستخدمين الآخرين (بما فيهم الـ admins و managers)
        $users = User::where('id', '!=', Auth::id())->get();

        foreach ($users as $user) {
            try {
                $this->notificationService->notifyMaterialAdded(
                    $user,
                    $material,
                    Auth::user()
                );
            } catch (\Exception $e) {
                Log::warning('Failed to send notification to user ' . $user->id . ': ' . $e->getMessage());
            }
        }
    }
    public function show($id)
    {
        $material = Material::with(['supplier', 'unit', 'creator', 'purchaseInvoice'])->findOrFail($id);

        return view('manufacturing::warehouses.material.show', compact('material'));
    }

    /**
     * Display the specified material (alias for show method).
     */
    public function showMaterial($id)
    {
        return $this->show($id);
    }

    /**
     * Show the form for editing the specified warehouse product.
     */
    public function edit($id)
    {
        $material = Material::findOrFail($id);
        $suppliers = Supplier::all();
        $units = Unit::all();
        $users = User::all();
        $materialTypes = \App\Models\MaterialType::all();

        return view('manufacturing::warehouses.material.edit', compact('material', 'suppliers', 'units', 'users', 'materialTypes'));
    }

    /**
     * Update the specified warehouse product in storage.
     */
    public function update(UpdateMaterialRequest $request, $id)
    {
        try {
            $material = Material::findOrFail($id);
            $oldValues = $material->toArray();
            $validated = $request->validated();

            $this->materialService->updateMaterial($material, $validated);
            $newValues = $material->fresh()->toArray();

            // ✅ تحديث الترجمات في جدول Translation
            $this->saveTranslations($material, $validated);

            // تسجيل العملية - إجباري
            try {
                $this->logOperation(
                    'update',
                    'Update Material',
                    'تم تحديث مادة: ' . $material->name_ar,
                    'materials',
                    $material->id,
                    $oldValues,
                    $newValues
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log material update: ' . $logError->getMessage());
                throw new \Exception('فشل تسجيل تحديث المادة: ' . $logError->getMessage());
            }

            // ✅ تخزين الإشعار
            $this->notifyUpdate(
                'مادة',
                $material->name_ar,
                route('manufacturing.warehouse-products.show', $material->id)
            );

            // إرسال إشعار بالتحديث
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyMaterialUpdated(
                        $manager,
                        $material,
                        Auth::user()
                    );
                }
            } catch (\Exception $notifError) {
                Log::warning('Failed to send update notifications: ' . $notifError->getMessage());
            }

            return redirect()->route('manufacturing.warehouse-products.index')
                           ->with('success', 'تم تحديث المادة بنجاح وتسجيل حركة المستودع');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error updating material: ' . $e->getMessage(), [
                'exception' => $e,
                'material_id' => $id,
                'input' => $request->all()
            ]);

            // Return back with error message
            return redirect()->back()
                           ->withInput()
                           ->withErrors(['error' => 'فشل في تحديث المادة: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified warehouse product from storage.
     */
    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        $oldValues = $material->toArray();

        // تسجيل العملية قبل الحذف
        $this->logOperation(
            'delete',
            'Delete Material',
            'تم حذف مادة: ' . $material->name_ar,
            'materials',
            $material->id,
            $oldValues,
            null
        );

        $material->delete();

        // ✅ تخزين الإشعار
        $this->notifyDelete(
            'مادة',
            $material->name_ar,
            route('manufacturing.warehouse-products.index')
        );

        // إرسال إشعار بحذف المادة
        try {
            $users = User::where('id', '!=', Auth::id())->get();
            foreach ($users as $user) {
                $this->notificationService->notifyCustom(
                    $user,
                    'تم حذف مادة',
                    'تم حذف المادة: ' . $material->name_ar,
                    'delete_material',
                    'danger',
                    'feather icon-trash-2',
                    route('manufacturing.warehouse-products.index')
                );
            }
        } catch (\Exception $notifError) {
            \Illuminate\Support\Facades\Log::warning('Failed to send material delete notifications: ' . $notifError->getMessage());
        }

        return redirect()->route('manufacturing.warehouse-products.index')
                       ->with('success', 'تم حذف المادة بنجاح');
    }

    /**
     * Display warehouse transactions for a material.
     */
    public function transactions($id)
    {
        $material = Material::with(['warehouseTransactions.unit'])->findOrFail($id);
        $transactions = $material->warehouseTransactions()->orderBy('created_at', 'desc')->get();

        // إرسال إشعار بعرض حركات المادة
        try {
            $this->notificationService->notifyCustom(
                Auth::user(),
                'عرض حركات المادة',
                'تم عرض حركات المادة: ' . $material->name_ar,
                'view_transactions',
                'info',
                'feather icon-list',
                route('manufacturing.warehouse-products.show', $id)
            );
        } catch (\Exception $notifError) {
            \Illuminate\Support\Facades\Log::warning('Failed to send view transactions notification: ' . $notifError->getMessage());
        }

        return view('manufacturing::warehouses.material.transactions', compact('material', 'transactions'));
    }

    /**
     * إضافة كمية جديدة للمادة - تسجيل في MaterialDetail و Delivery Note
     * Add quantity to material - record in MaterialDetail and create Delivery Note
     */
    public function addQuantity(\Illuminate\Http\Request $request, $id)
    {
        try {
            $material = Material::findOrFail($id);

            // التحقق من صحة البيانات
            $validated = $request->validate([
                'warehouse_id' => 'required|exists:warehouses,id',
                'quantity' => 'required|numeric|min:0.01',
'unit_id' => 'required|exists:units,id',
                'notes' => 'nullable|string|max:500',
            ], [
                'warehouse_id.required' => 'المستودع مطلوب',
                'warehouse_id.exists' => 'المستودع غير موجود',
                'quantity.required' => 'الكمية مطلوبة',
'unit_id.required' => 'الوحدة مطلوبة',
                'quantity.numeric' => 'الكمية يجب أن تكون رقم',
                'quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
            ]);

            // الحصول على أول MaterialDetail لنأخذ unit_id منه
            $firstDetail = MaterialDetail::where('material_id', $material->id)->first();
            $unitId = $firstDetail?->unit_id;

            // البحث أو إنشاء سجل في MaterialDetail
            $materialDetail = MaterialDetail::where('material_id', $material->id)
                                                       ->where('warehouse_id', $validated['warehouse_id'])
                                                       ->first();

            if ($materialDetail) {
                // تحديث الكمية في سجل موجود
                $materialDetail->quantity += $validated['quantity'];
$materialDetail->unit_id = $validated['unit_id']; // ✅ تحديث في MaterialDetail
                $materialDetail->original_weight += $validated['quantity'];        // ✅ تحديث في MaterialDetail
                $materialDetail->remaining_weight += $validated['quantity'];      // ✅ تحديث في MaterialDetail
                $materialDetail->save();
            } else {
                // إنشاء سجل جديد
                $materialDetail = \App\Models\MaterialDetail::create([
                    'material_id' => $material->id,
                    'warehouse_id' => $validated['warehouse_id'],
                    'quantity' => $validated['quantity'],
                    'original_weight' => $validated['quantity'],                 // ✅ جديد
                    'remaining_weight' => $validated['quantity'],                // ✅ جديد
                    'unit_id' => $unitId,                                        // ✅ جديد
                    'min_quantity' => 0,

                    'created_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                ]);
            }

            // إنشاء أذن مخزنية (Delivery Note) تلقائياً - وارد (incoming)
            $deliveryNote = \App\Models\DeliveryNote::create([
                'note_number' => $this->generateDeliveryNoteNumber(),
                'type' => 'incoming', // ✅ نوع الأذن: وارد
                'status' => 'approved', // ✅ الحالة: موافق عليه
                'material_id' => $material->id,
                'warehouse_id' => $validated['warehouse_id'], // ✅ المستودع المقصود
                'delivered_weight' => $validated['quantity'],
                'delivery_date' => now()->toDateString(),
                'driver_name' => 'إضافة مستودع', // قيمة افتراضية
                'driver_name_en' => 'Warehouse Addition',
                'vehicle_number' => 'N/A',
                'received_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                'recorded_by' => \Illuminate\Support\Facades\Auth::id() ?? 1, // ✅ من سجل الأذن
                'approved_by' => \Illuminate\Support\Facades\Auth::id() ?? 1, // ✅ من وافق
                'approved_at' => now(), // ✅ وقت الموافقة
                'is_active' => true, // ✅ نشط
            ]);

            // تسجيل الحركة في المستودع
            \App\Models\WarehouseTransaction::create([
                'transaction_number' => 'TRX-' . date('Y-m-d') . '-' . uniqid(),
                'material_id' => $material->id,
                'warehouse_id' => $validated['warehouse_id'],
                'transaction_type' => 'receive', // استقبال
                'quantity' => $validated['quantity'],
                'unit_id' => $validated['unit_id'] ?? $unitId,
                'notes' => $validated['notes'] ?? 'أذن مخزنية رقم: ' . $deliveryNote->note_number,
                'created_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            ]);

            // تسجيل العملية - إجباري
             try {
                $this->logOperation(
                    'create',
                    'Add Quantity',
                    'تم اضافة كمية: ' . $material->name_ar . ' بكمية ' . $validated['quantity'],
                    'materials',
                    $material->id,
                    null,
                    $material->toArray()
                );
            } catch (\Exception $logError) {
                // إذا فشل التسجيل، سجل الخطأ لكن استمر
                Log::error('Failed to log material creation: ' . $logError->getMessage(), [
                    'material_id' => $material->id,
                    'original_error' => $logError
                ]);
                // أعد رمي الاستثناء ليتم التعامل معه في catch الخارجية
                throw new \Exception('فشل تسجيل العملية: ' . $logError->getMessage());
            }

            // ✅ تخزين الإشعار
            $this->notifyCustomOperation(
                'quantity_added',
                'إضافة كمية للمادة',
                'تم إضافة كمية ' . $validated['quantity'] . ' للمادة: ' . $material->name_ar,
                'success',
                'fas fa-plus',
                route('manufacturing.warehouse-products.show', $material->id)
            );

            return redirect()->back()
                           ->with('success', 'تم إضافة الكمية بنجاح وإنشاء أذن مخزنية رقم: ' . $deliveryNote->note_number);
        } catch (\Exception $e) {
            Log::error('Error adding quantity: ' . $e->getMessage(), [
                'exception' => $e,
                'material_id' => $id,
                'input' => $request->all()
            ]);

            return redirect()->back()
                           ->withInput()
                           ->withErrors(['error' => 'فشل في إضافة الكمية: ' . $e->getMessage()]);
        }
    }

    /**
     * توليد رقم أذن مخزنية فريد
     * Generate unique delivery note number
     */
    private function generateDeliveryNoteNumber(): string
    {
        $prefix = 'DN-' . date('Y-m-d') . '-';
        $sequence = \App\Models\DeliveryNote::where('note_number', 'like', $prefix . '%')
            ->count() + 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * تسجيل عملية في سجل العمليات
     * Log an operation to OperationLog
     */
    private function logOperation(string $action, string $actionEn, string $description, string $tableName, $recordId, ?array $oldValues = null, ?array $newValues = null): void
    {
        try {
            OperationLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => $action,
                'action_en' => $actionEn,
                'description' => $description,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging operation: ' . $e->getMessage());
        }
    }


    public function changeStatus(Request $request, $id)
    {
        try {
            $material = Material::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:available,in_use,consumed,expired',
            ], [
                'status.required' => 'الحالة مطلوبة',
                'status.in' => 'الحالة غير صحيحة',
            ]);

            $oldStatus = $material->status;
            $material->update(['status' => $validated['status']]);

            // تسجيل العملية - إجباري
            try {
                $this->logOperation(
                    'update',
                    'Update Status',
                    'تم تغيير حالة المادة من ' . $this->getStatusLabel($oldStatus) . ' إلى ' . $this->getStatusLabel($validated['status']),
                    'materials',
                    $material->id,
                    ['status' => $oldStatus],
                    ['status' => $validated['status']]
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log status change: ' . $logError->getMessage());
                throw new \Exception('فشل تسجيل تغيير الحالة: ' . $logError->getMessage());
            }

            // ✅ تخزين الإشعار
            $this->notifyStatusChange(
                'مادة',
                $this->getStatusLabel($oldStatus),
                $this->getStatusLabel($validated['status']),
                $material->name_ar,
                route('manufacturing.warehouse-products.show', $material->id)
            );

            return redirect()->back()
                           ->with('success', 'تم تغيير الحالة من ' . $this->getStatusLabel($oldStatus) . ' إلى ' . $this->getStatusLabel($validated['status']));
        } catch (\Exception $e) {
            Log::error('Error changing status: ' . $e->getMessage());
            return redirect()->back()
                           ->withErrors(['error' => 'فشل في تغيير الحالة: ' . $e->getMessage()]);
        }
    }

    /**
     * الحصول على تسمية الحالة
     * Get status label
     */
    private function getStatusLabel($status): string
    {
        $statusLabels = [
            'available' => 'متوفر',
            'in_use' => 'قيد الاستخدام',
            'consumed' => 'مستهلك',
            'expired' => 'منتهي الصلاحية',
        ];

        return $statusLabels[$status] ?? $status;
    }

    /**
     * ✅ حفظ الترجمات في جدول Translation للغات الأربع
     * Save translations in Translation table for four languages
     */
    private function saveTranslations($material, $validated)
    {
        try {
            $translations = [
                'ar' => [
                    'name' => $validated['name_ar'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'shelf_location' => $validated['shelf_location'] ?? null,
                ],
                'en' => [
                    'name' => $validated['name_en'] ?? null,
                    'notes' => $validated['notes_en'] ?? null,
                    'shelf_location' => $validated['shelf_location_en'] ?? null,
                ],
                'hi' => [
                    'name' => $validated['name_hi'] ?? null,
                    'notes' => $validated['notes_hi'] ?? null,
                    'shelf_location' => $validated['shelf_location_hi'] ?? null,
                ],
                'ur' => [
                    'name' => $validated['name_ur'] ?? null,
                    'notes' => $validated['notes_ur'] ?? null,
                    'shelf_location' => $validated['shelf_location_ur'] ?? null,
                ],
            ];

            // حفظ الترجمات في جدول Translation
            foreach ($translations as $locale => $fields) {
                foreach ($fields as $key => $value) {
                    if (!empty($value)) {
                        \App\Models\Translation::saveTranslation(
                            'App\Models\Material',
                            $material->id,
                            $key,
                            $value,
                            $locale
                        );
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to save translations: ' . $e->getMessage(), [
                'material_id' => $material->id,
                'error' => $e
            ]);
            // لا نفشل العملية إذا فشل حفظ الترجمات
        }
    }
}

