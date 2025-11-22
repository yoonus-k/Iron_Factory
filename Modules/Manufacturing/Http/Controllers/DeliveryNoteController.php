<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\DeliveryNote;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\MaterialMovement;
use App\Services\NotificationService;
use App\Traits\StoresNotifications;
use Modules\Manufacturing\Traits\LogsOperations;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryNoteController extends Controller
{
    use LogsOperations, StoresNotifications;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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

        // جلب جميع أذون التسليم للجدول العام (مع الفلاتر الأساسية)
        $query = DeliveryNote::with(['material', 'receiver', 'supplier', 'destination']);

        // فلتر حسب النوع (واردة/صادرة)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلتر حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلتر حسب رقم الأذن
        if ($request->filled('delivery_number')) {
            $query->where('note_number', 'like', '%' . $request->delivery_number . '%');
        }

        // بحث عام
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('note_number', 'like', '%' . $request->search . '%')
                  ->orWhere('notes', 'like', '%' . $request->search . '%')
                  ->orWhere('driver_name', 'like', '%' . $request->search . '%');
            });
        }

        // ترتيب البيانات حسب الأحدث أولاً مع الباجنيشن
        $deliveryNotes = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('manufacturing::warehouses.delivery-notes.index', compact(
            'deliveryNotes',
            'incomingUnregistered',
            'incomingRegistered',
            'movedToProduction',
            'outgoing',
            'appliedFilters'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * ✅ تم التعديل: إضافة قائمة المستودعات
     */
    public function create()
    {
        // ✅ استخدام المنتجات الموجودة في المستودع (اختياري الآن)
        // Load all material details, not just those with quantity > 0 to avoid filtering issues
        $materialDetails = \App\Models\MaterialDetail::with(['material', 'warehouse', 'unit'])
            ->get();

        // Debug: Log the material details to see what we're getting
        \Illuminate\Support\Facades\Log::info('Material Details Count: ' . $materialDetails->count());
        \Illuminate\Support\Facades\Log::info('First Material Detail: ' . $materialDetails->first());

        $suppliers = Supplier::all();
        $warehouses = Warehouse::all(); // ✅ قائمة المستودعات
        $users = User::all();
        $materials = Material::all(); // ✅ قائمة المواد

        // توليد رقم الأذن تلقائياً
        $autoGeneratedNumber = $this->generateDeliveryNoteNumber();

        return view('manufacturing::warehouses.delivery-notes.create', compact(
            'materialDetails',
            'suppliers',
            'warehouses', // ✅ تمرير المستودعات
            'materials', // ✅ تمرير المواد
            'users',
            'autoGeneratedNumber'
        ));
    }

    /**
     * Generate a unique delivery note number
     */
    private function generateDeliveryNoteNumber()
    {
        $prefix = 'DN-' . date('Y') . '-';
        $maxAttempts = 50;
        $attempt = 0;

        // Get the highest sequential number for this year (ignoring old format numbers)
        $baseNumber = 1;
        $lastNote = DeliveryNote::where('note_number', 'like', $prefix . '%')
            ->where('note_number', 'NOT LIKE', $prefix . '%-%-%-%') // Exclude old format (DN-YYYY-MM-DD-XXXX)
            ->orderByRaw('CAST(SUBSTRING(note_number, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        if ($lastNote) {
            $numberPart = substr($lastNote->note_number, strlen($prefix));
            // Extract just the numeric part (handle cases like "0001" or "0001-extra")
            if (preg_match('/^(\d+)/', $numberPart, $matches)) {
                $lastNumber = (int)$matches[1];
                $baseNumber = $lastNumber + 1;
            }
        }

        // Try to find a unique number
        while ($attempt < $maxAttempts) {
            $noteNumber = $prefix . str_pad($baseNumber + $attempt, 4, '0', STR_PAD_LEFT);

            // Check if this number already exists in database
            $exists = DeliveryNote::where('note_number', $noteNumber)->exists();

            if (!$exists) {
                return $noteNumber;
            }

            $attempt++;
        }

        // If we couldn't generate a unique number, throw error
        throw new \Exception('فشل في توليد رقم أذن فريد بعد ' . $maxAttempts . ' محاولات. يرجى المحاولة لاحقاً أو اتصل بالدعم الفني.');
    }
    /**
     * Store a newly created resource in storage.
     * ✅ تم التعديل: يسمح بتسجيل الأذن بدون مادة محددة (اختيار المستودع فقط)
     */
    public function store(Request $request)
    {
        try {
            // التحقق من البيانات بناءً على النوع
            $type = $request->input('type', 'incoming');

            // ✅ تحقق صحيح: material_id و warehouse_id للواردة، material_detail_id و warehouse_from_id للصادرة
            $validated = $request->validate([
                'type' => 'required|in:incoming,outgoing',
                'delivery_date' => 'required|date',
                'material_id' => $type === 'incoming' ? 'required|exists:materials,id' : 'nullable|exists:materials,id',
                'material_detail_id' => $type === 'outgoing' ? 'required|exists:material_details,id' : 'nullable|exists:material_details,id',
                'warehouse_id' => $type === 'incoming' ? 'required|exists:warehouses,id' : 'nullable|exists:warehouses,id',
                'warehouse_from_id' => $type === 'outgoing' ? 'required|exists:warehouses,id' : 'nullable|exists:warehouses,id',
                'quantity' => $type === 'incoming' ? 'required|numeric|min:0.01' : 'nullable|numeric|min:0',
                'delivery_quantity' => $type === 'outgoing' ? 'required|numeric|min:0.01' : 'nullable|numeric|min:0',
                'actual_weight' => 'nullable|numeric|min:0',
                'weight_from_scale' => 'nullable|numeric|min:0',
                'invoice_weight' => 'nullable|numeric|min:0',
                'driver_name' => 'nullable|string|max:255',
                'vehicle_number' => 'nullable|string|max:50',
                'received_by' => 'nullable|exists:users,id',
                'destination_id' => $type === 'outgoing' ? 'required|in:client,production_stage,production_transfer' : 'nullable|in:client,production_stage,production_transfer',
                'invoice_number' => 'nullable|string|max:100',
                'invoice_reference_number' => 'nullable|string|max:100',
            ], [
                'type.required' => 'نوع الأذن مطلوب',
                'type.in' => 'نوع الأذن غير صحيح',
                'delivery_date.required' => 'التاريخ مطلوب',
                'delivery_date.date' => 'التاريخ غير صحيح',
                'warehouse_id.required' => 'المستودع مطلوب',
                'warehouse_id.exists' => 'المستودع المختار غير موجود',
                'warehouse_from_id.required' => 'المستودع المصدر مطلوب',
                'warehouse_from_id.exists' => 'المستودع المصدر غير موجود',
                'material_id.required' => 'يجب اختيار المادة',
                'material_id.exists' => 'المادة المختارة غير موجودة',
                'material_detail_id.required' => 'يجب اختيار المادة',
                'material_detail_id.exists' => 'المادة المختارة غير موجودة',
                'quantity.required' => 'الكمية مطلوبة للأذن الواردة',
                'quantity.numeric' => 'الكمية يجب أن تكون رقماً',
                'quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
                'delivery_quantity.required' => 'الكمية مطلوبة للأذن الصادرة',
                'delivery_quantity.numeric' => 'الكمية يجب أن تكون رقماً',
                'delivery_quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
                'actual_weight.numeric' => 'الوزن يجب أن يكون رقماً',
                'actual_weight.min' => 'الوزن لا يمكن أن يكون سالباً',
                'weight_from_scale.numeric' => 'وزن الميزان يجب أن يكون رقماً',
                'weight_from_scale.min' => 'وزن الميزان لا يمكن أن يكون سالباً',
                'invoice_weight.numeric' => 'وزن الفاتورة يجب أن يكون رقماً',
                'driver_name.string' => 'اسم السائق يجب أن يكون نصاً',
                'driver_name.max' => 'اسم السائق طويل جداً',
                'vehicle_number.string' => 'رقم المركبة يجب أن يكون نصاً',
                'vehicle_number.max' => 'رقم المركبة طويل جداً',
                'received_by.exists' => 'المستخدم المختار غير موجود',
                'destination_id.required' => 'الوجهة مطلوبة للأذن الصادرة',
                'destination_id.in' => 'الوجهة المختارة غير صحيحة',
                'invoice_number.string' => 'رقم الفاتورة يجب أن يكون نصاً',
                'invoice_reference_number.string' => 'رقم مرجع الفاتورة يجب أن يكون نصاً',
            ]);

            // Generate a new delivery note number (always generate fresh, don't use form value)
            $noteNumber = $this->generateDeliveryNoteNumber();
            $validated['note_number'] = $noteNumber;

            // للإذن الصادر: استخدام المستودع المصدر كـ warehouse_id
            if ($type === 'outgoing' && !empty($validated['warehouse_from_id'])) {
                $validated['warehouse_id'] = $validated['warehouse_from_id'];
                // ✅ للإذن الصادر: استخدام delivery_quantity كـ quantity
                $validated['quantity'] = $validated['delivery_quantity'] ?? 0;
            }

            // إذا كان هناك وزن فعلي، استخدمه
            if (!empty($validated['actual_weight'])) {
                $validated['delivered_weight'] = $validated['actual_weight'];
            }

            // إذا كان هناك material_detail_id، استخدم بيانات المادة
            if (!empty($validated['material_detail_id'])) {
                $materialDetail = \App\Models\MaterialDetail::findOrFail($validated['material_detail_id']);
                if (empty($validated['material_id'])) {
                    $validated['material_id'] = $materialDetail->material_id;
                }
            }

            // Set the user fields
            $validated['received_by'] = $validated['received_by'] ?? Auth::id() ?? 1;
            $validated['recorded_by'] = Auth::id() ?? 1;

            // Calculate weight discrepancy if both weights exist
            if (!empty($validated['actual_weight']) && !empty($validated['invoice_weight'])) {
                $validated['weight_discrepancy'] = $validated['actual_weight'] - $validated['invoice_weight'];
            }

            DB::beginTransaction();

            try {
                // Create the delivery note
                $deliveryNote = DeliveryNote::create($validated);
            } catch (\Illuminate\Database\QueryException $e) {
                // If duplicate note number, retry with a new generated number
                if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'note_number') !== false) {
                    DB::rollBack();

                    // Try generating a new note number (up to 5 times)
                    for ($i = 0; $i < 5; $i++) {
                        try {
                            $validated['note_number'] = $this->generateDeliveryNoteNumber();
                            DB::beginTransaction();
                            $deliveryNote = DeliveryNote::create($validated);
                            break;
                        } catch (\Illuminate\Database\QueryException $retryError) {
                            if ($i === 4) {
                                // Last attempt failed, throw original error
                                throw new \Exception('حدث خطأ في حفظ أذن التسليم: ' . $e->getMessage());
                            }
                        }
                    }
                } else {
                    throw $e;
                }
            }

            // للأذن الواردة: وضع حالة التسجيل كـ "مسجلة" و تحديث الكمية في المستودع
            if ($type === 'incoming') {
                $deliveryNote->update([
                    'registration_status' => 'registered',
                    'registered_by' => Auth::id() ?? 1,
                    'registered_at' => now(),
                ]);

                // ✅ تحديث كمية المادة في المستودع الواردة
                if (!empty($validated['material_id']) && !empty($validated['warehouse_id'])) {
                    try {
                        // البحث عن MaterialDetail موجود أو إنشاء واحد جديد
                        $materialDetail = \App\Models\MaterialDetail::firstOrCreate(
                            [
                                'warehouse_id' => $validated['warehouse_id'],
                                'material_id' => $validated['material_id'],
                            ],
                            [
                                'quantity' => 0,
                                'unit_id' => \App\Models\Material::find($validated['material_id'])->unit_id ?? 1,
                                'created_by' => Auth::id() ?? 1,
                            ]
                        );

                        // زيادة الكمية بالقيمة المسجلة من حقل quantity في الأذن
                        $quantityToAdd = $validated['quantity'] ?? 0;
                        if ($quantityToAdd > 0) {
                            $materialDetail->addIncomingQuantity(
                                $quantityToAdd,
                                $quantityToAdd,
                                $quantityToAdd
                            );
                        }

                        Log::info('✅ تم تحديث كمية المادة بنجاح', [
                            'material_id' => $validated['material_id'],
                            'warehouse_id' => $validated['warehouse_id'],
                            'quantity_added' => $quantityToAdd,
                            'delivery_note' => $deliveryNote->note_number,
                        ]);
                    } catch (\Exception $inventoryError) {
                        Log::error('⚠️ فشل تحديث كمية المادة: ' . $inventoryError->getMessage(), [
                            'material_id' => $validated['material_id'],
                            'warehouse_id' => $validated['warehouse_id'],
                            'error' => $inventoryError,
                        ]);
                    }
                }
            }

            // تسجيل الحركة في material_movements
            if ($type === 'outgoing' && !empty($validated['delivery_quantity']) && $validated['delivery_quantity'] > 0) {
                $materialDetail = null;
                if (!empty($validated['material_detail_id'])) {
                    $materialDetail = \App\Models\MaterialDetail::find($validated['material_detail_id']);
                }

                MaterialMovement::create([
                    'movement_number' => MaterialMovement::generateMovementNumber(),
                    'movement_type' => 'outgoing',
                    'source' => 'registration',
                    'delivery_note_id' => $deliveryNote->id,
                    'material_detail_id' => $validated['material_detail_id'] ?? null,
                    'material_id' => $validated['material_id'],
                    'unit_id' => $materialDetail ? $materialDetail->unit_id : null,
                    'quantity' => $validated['delivery_quantity'],
                    'from_warehouse_id' => $validated['warehouse_id'],
                    'to_warehouse_id' => null,
                    'supplier_id' => null,
                    'destination' => $validated['destination_id'] ?? 'غير محدد',
                    'description' => 'خروج بضاعة - أذن رقم ' . $deliveryNote->note_number,
                    'notes' => $validated['notes'] ?? null,
                    'reference_number' => $validated['invoice_number'] ?? null,
                    'created_by' => Auth::id() ?? 1,
                    'movement_date' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status' => 'completed',
                ]);
            }

            // تحديث كمية المستودع (للصادر فقط)
            if ($type === 'outgoing' && !empty($validated['material_detail_id']) && !empty($validated['delivery_quantity'])) {
                try {
                    $materialDetail = \App\Models\MaterialDetail::find($validated['material_detail_id']);
                    if ($materialDetail->quantity < $validated['delivery_quantity']) {
                        throw new \Exception('الكمية المتوفرة غير كافية');
                    }
                    $materialDetail->quantity -= $validated['delivery_quantity'];
                    $materialDetail->save();
                } catch (\Exception $inventoryError) {
                    Log::error('Failed to update inventory: ' . $inventoryError->getMessage());
                    $deliveryNote->delete();
                    throw new \Exception('فشل تحديث المستودع: ' . $inventoryError->getMessage());
                }
            }

            // Log the operation
            try {
                $this->logOperation(
                    'create',
                    'Create Delivery Note',
                    'تم إنشاء أذن تسليم ' . ($type === 'incoming' ? 'واردة' : 'صادرة') . ' جديدة: ' . $deliveryNote->note_number,
                    'delivery_notes',
                    $deliveryNote->id,
                    null,
                    $deliveryNote->toArray()
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log delivery note creation: ' . $logError->getMessage());
            }

            // Send notifications
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyDeliveryNoteRegistered(
                        $manager,
                        $deliveryNote,
                        Auth::user()
                    );
                }

                $this->storeNotification(
                    'delivery_note_created',
                    'أذن تسليم ' . ($type === 'incoming' ? 'واردة' : 'صادرة') . ' جديدة',
                    'تم إنشاء أذن تسليم جديدة برقم: ' . $deliveryNote->note_number,
                    $type === 'incoming' ? 'success' : 'info',
                    $type === 'incoming' ? 'fas fa-arrow-down' : 'fas fa-arrow-up',
                    route('manufacturing.delivery-notes.show', $deliveryNote->id)
                );
            } catch (\Exception $notifError) {
                Log::warning('Failed to send delivery note notifications: ' . $notifError->getMessage());
            }

            DB::commit();

            $successMessage = $type === 'incoming'
                ? 'تم إضافة أذن التسليم الواردة بنجاح - رقم الأذن: ' . $deliveryNote->note_number
                : 'تم إضافة أذن التسليم الصادرة بنجاح - رقم الأذن: ' . $deliveryNote->note_number;

            // For incoming delivery notes, redirect to registration page
            if ($type === 'incoming') {
                return redirect()->route('manufacturing.warehouse.registration.create', $deliveryNote)
                    ->with('success', $successMessage);
            }

            return redirect()->route('manufacturing.delivery-notes.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating delivery note: ' . $e->getMessage(), [
                'exception' => $e,
                'input' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إضافة أذن التسليم: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $deliveryNote = DeliveryNote::with([
            'material',
            'receiver',
            'recordedBy',
            'approvedBy',
            'supplier',
            'destination'
        ])->findOrFail($id);

        // Load operation logs
        $operationLogs = $deliveryNote->operationLogs()->orderBy('created_at', 'desc')->get();

        return view('manufacturing::warehouses.delivery-notes.show', compact('deliveryNote', 'operationLogs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $deliveryNote = DeliveryNote::findOrFail($id);
        $materials = Material::all();
        $suppliers = Supplier::all();
        $warehouses = Warehouse::all();
        $users = User::all();

        // ✅ استخدام المنتجات الموجودة في المستودع (اختياري الآن)
        // Load all material details, not just those with quantity > 0 to avoid filtering issues
        $materialDetails = \App\Models\MaterialDetail::with(['material', 'warehouse', 'unit'])
            ->get();

        // Debug: Log the material details to see what we're getting
        \Illuminate\Support\Facades\Log::info('Edit Material Details Count: ' . $materialDetails->count());
        if ($materialDetails->count() > 0) {
            \Illuminate\Support\Facades\Log::info('Edit First Material Detail: ' . json_encode($materialDetails->first()));
        }

        return view('manufacturing::warehouses.delivery-notes.edit', compact(
            'deliveryNote',
            'materials',
            'suppliers',
            'warehouses',
            'users',
            'materialDetails'
        ));
    }

    /**
     * Update the specified resource in storage.
     * ✅ تم التعديل: دعم تحديث الأذن بدون مادة
     */
    public function update(Request $request, $id)
    {
        try {
            $deliveryNote = DeliveryNote::findOrFail($id);
            $oldValues = $deliveryNote->toArray();
            $type = $deliveryNote->type;

            // ✅ تحديث التحقق: material_id اختياري، warehouse_id إجباري
            $validated = $request->validate([
                'delivery_number' => 'required|string|unique:delivery_notes,note_number,' . $deliveryNote->id,
                'delivery_date' => 'required|date',
                'material_id' => $type === 'incoming' ? 'required|exists:materials,id' : 'nullable|exists:materials,id',
                'warehouse_id' => $type === 'incoming' ? 'required|exists:warehouses,id' : 'nullable|exists:warehouses,id',
                'quantity' => $type === 'incoming' ? 'required|numeric|min:0.01' : 'nullable|numeric|min:0',
                'material_detail_id' => $type === 'outgoing' ? 'required|exists:material_details,id' : 'nullable|exists:material_details,id',
                'warehouse_from_id' => $type === 'outgoing' ? 'required|exists:warehouses,id' : 'nullable|exists:warehouses,id',
                'delivery_quantity' => $type === 'outgoing' ? 'required|numeric|min:0.01' : 'nullable|numeric|min:0',
                'destination_id' => $type === 'outgoing' ? 'required|in:client,production_transfer' : 'nullable|in:client,production_transfer',
                'driver_name' => 'nullable|string|max:255',
                'vehicle_number' => 'nullable|string|max:50',
                'received_by' => 'nullable|exists:users,id',
                'invoice_number' => 'nullable|string|max:100',
                'invoice_reference_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
            ], [
                // رسائل الخطأ بالعربي
                'delivery_number.required' => 'رقم الأذن مطلوب',
                'delivery_number.string' => 'رقم الأذن يجب أن يكون نصاً',
                'delivery_number.unique' => 'رقم الأذن موجود بالفعل',
                'delivery_date.required' => 'التاريخ مطلوب',
                'delivery_date.date' => 'التاريخ غير صحيح',
                'warehouse_id.required' => 'المستودع مطلوب',
                'warehouse_id.exists' => 'المستودع المختار غير موجود',
                'warehouse_from_id.required' => 'المستودع المصدر مطلوب',
                'warehouse_from_id.exists' => 'المستودع المصدر غير موجود',
                'material_id.required' => 'يجب اختيار المادة',
                'material_id.exists' => 'المادة المختارة غير موجودة',
                'material_detail_id.required' => 'يجب اختيار تفاصيل المادة',
                'material_detail_id.exists' => 'تفاصيل المادة غير موجودة',
                'quantity.required' => 'الكمية مطلوبة للأذن الواردة',
                'quantity.numeric' => 'الكمية يجب أن تكون رقماً',
                'quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
                'delivery_quantity.required' => 'الكمية مطلوبة للأذن الصادرة',
                'delivery_quantity.numeric' => 'الكمية يجب أن تكون رقماً',
                'delivery_quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
                'destination_id.required' => 'الوجهة مطلوبة',
                'destination_id.in' => 'الوجهة المختارة غير صحيحة',
                'driver_name.string' => 'اسم السائق يجب أن يكون نصاً',
                'driver_name.max' => 'اسم السائق طويل جداً',
                'vehicle_number.string' => 'رقم المركبة يجب أن يكون نصاً',
                'vehicle_number.max' => 'رقم المركبة طويل جداً',
                'received_by.exists' => 'المستخدم المختار غير موجود',
                'invoice_number.string' => 'رقم الفاتورة يجب أن يكون نصاً',
                'invoice_reference_number.string' => 'رقم مرجع الفاتورة يجب أن يكون نصاً',
                'notes.string' => 'الملاحظات يجب أن تكون نصاً',
            ]);

            // Prepare the data
            $validated['note_number'] = $validated['delivery_number'];
            unset($validated['delivery_number']);

            // Handle warehouse_from_id for outgoing notes
            if ($type === 'outgoing' && !empty($validated['warehouse_from_id'])) {
                $validated['warehouse_id'] = $validated['warehouse_from_id'];
                // ✅ للإذن الصادر: استخدام delivery_quantity كـ quantity
                $validated['quantity'] = $validated['delivery_quantity'] ?? 0;
            }

            // Handle material_detail_id for outgoing notes - extract material_id
            if ($type === 'outgoing' && !empty($validated['material_detail_id'])) {
                $materialDetail = \App\Models\MaterialDetail::find($validated['material_detail_id']);
                if ($materialDetail) {
                    $validated['material_id'] = $materialDetail->material_id;
                }
            }

            // ✅ تحديث الكمية في المستودع للأذن الواردة عند التعديل
            if ($type === 'incoming') {
                try {
                    // الفرق بين الكمية الجديدة والقديمة
                    $oldQuantity = $oldValues['quantity'] ?? 0;
                    $newQuantity = $validated['quantity'] ?? 0;
                    $quantityDifference = $newQuantity - $oldQuantity;

                    // إذا كانت هناك فروقات
                    if ($quantityDifference != 0) {
                        // البحث أو إنشاء MaterialDetail
                        $materialDetail = \App\Models\MaterialDetail::firstOrCreate(
                            [
                                'warehouse_id' => $validated['warehouse_id'],
                                'material_id' => $validated['material_id'],
                            ],
                            [
                                'quantity' => 0,
                                'unit_id' => \App\Models\Material::find($validated['material_id'])->unit_id ?? 1,
                                'created_by' => Auth::id() ?? 1,
                            ]
                        );

                        // تطبيق الفرق
                        if ($quantityDifference > 0) {
                            // إضافة الفرق (زيادة)
                            $materialDetail->addIncomingQuantity(
                                $quantityDifference,
                                $quantityDifference,
                                $quantityDifference
                            );
                        } else {
                            // خصم الفرق (نقصان)
                            $materialDetail->quantity += $quantityDifference; // negative فسيطرح
                            $materialDetail->save();
                        }

                        Log::info('✅ تم تحديث كمية المادة عند التعديل', [
                            'material_id' => $validated['material_id'],
                            'warehouse_id' => $validated['warehouse_id'],
                            'quantity_difference' => $quantityDifference,
                            'delivery_note' => $deliveryNote->note_number,
                        ]);
                    }
                } catch (\Exception $inventoryError) {
                    Log::error('⚠️ فشل تحديث كمية المادة: ' . $inventoryError->getMessage());
                }
            }

            // ✅ تحديث الكمية في المستودع للأذن الصادرة عند التعديل
            if ($type === 'outgoing') {
                try {
                    // الفرق بين الكمية الجديدة والقديمة
                    $oldQuantity = $oldValues['delivery_quantity'] ?? 0;
                    $newQuantity = $validated['delivery_quantity'] ?? 0;
                    $quantityDifference = $newQuantity - $oldQuantity;

                    // إذا كانت هناك فروقات
                    if ($quantityDifference != 0) {
                        $materialDetail = \App\Models\MaterialDetail::find($validated['material_detail_id']);
                        if ($materialDetail) {
                            // التحقق من توفر الكمية (في حالة الزيادة)
                            if ($quantityDifference > 0 && $materialDetail->quantity < $quantityDifference) {
                                throw new \Exception('الكمية المتوفرة غير كافية لتطبيق التعديل');
                            }

                            // تطبيق الفرق
                            $materialDetail->quantity -= $quantityDifference;
                            $materialDetail->save();

                            Log::info('✅ تم تحديث كمية المادة الصادرة عند التعديل', [
                                'material_detail_id' => $validated['material_detail_id'],
                                'warehouse_id' => $validated['warehouse_id'] ?? $oldValues['warehouse_id'],
                                'quantity_difference' => $quantityDifference,
                                'delivery_note' => $deliveryNote->note_number,
                            ]);
                        }
                    }
                } catch (\Exception $inventoryError) {
                    Log::error('⚠️ فشل تحديث كمية المادة الصادرة: ' . $inventoryError->getMessage());
                    throw new \Exception('فشل تحديث المستودع: ' . $inventoryError->getMessage());
                }
            }

            // Update the delivery note
            $deliveryNote->update($validated);
            $newValues = $deliveryNote->fresh()->toArray();

            // Log the operation
            try {
                $this->logOperation(
                    'update',
                    'Update Delivery Note',
                    'تم تحديث أذن تسليم: ' . $deliveryNote->note_number,
                    'delivery_notes',
                    $deliveryNote->id,
                    $oldValues,
                    $newValues
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log delivery note update: ' . $logError->getMessage());
                // Don't throw - just log the error
            }

            // إرسال إشعار بالتحديث
            try {
                $users = User::where('id', '!=', Auth::id())->get();
                foreach ($users as $user) {
                    $this->notificationService->notifyDeliveryNoteRegistered(
                        $user,
                        $deliveryNote->fresh(),
                        Auth::user()
                    );
                }

                // ✅ تخزين إشعار التحديث في قاعدة البيانات
                $this->storeNotification(
                    'delivery_note_updated',
                    'تحديث أذن تسليم',
                    'تم تحديث أذن التسليم برقم: ' . $deliveryNote->note_number,
                    'warning',
                    'fas fa-edit',
                    route('manufacturing.delivery-notes.show', $deliveryNote->id)
                );
            } catch (\Exception $notifError) {
                Log::warning('Failed to send update notifications: ' . $notifError->getMessage());
            }

            return redirect()->route('manufacturing.delivery-notes.index')
                ->with('success', 'تم تحديث أذن التسليم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating delivery note: ' . $e->getMessage(), [
                'exception' => $e,
                'delivery_note_id' => $id,
                'input' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث أذن التسليم: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $deliveryNote = DeliveryNote::findOrFail($id);
            $oldValues = $deliveryNote->toArray();

            // ✅ استرجاع الكمية إلى المستودع قبل الحذف (للأذن الواردة فقط)
            if ($deliveryNote->type === 'incoming' && !empty($deliveryNote->material_id) && !empty($deliveryNote->warehouse_id)) {
                try {
                    $materialDetail = \App\Models\MaterialDetail::where([
                        'warehouse_id' => $deliveryNote->warehouse_id,
                        'material_id' => $deliveryNote->material_id,
                    ])->first();

                    if ($materialDetail) {
                        // استرجاع الكمية المحفوظة من حقل quantity
                        $quantityToRemove = $deliveryNote->quantity ?? 0;
                        if ($quantityToRemove > 0) {
                            $materialDetail->quantity -= $quantityToRemove;
                            $materialDetail->save();

                            Log::info('✅ تم استرجاع الكمية من الأذن المحذوفة', [
                                'material_id' => $deliveryNote->material_id,
                                'warehouse_id' => $deliveryNote->warehouse_id,
                                'quantity_removed' => $quantityToRemove,
                                'delivery_note' => $deliveryNote->note_number,
                            ]);
                        }
                    }
                } catch (\Exception $inventoryError) {
                    Log::error('⚠️ فشل استرجاع الكمية: ' . $inventoryError->getMessage());
                    // لا نوقف الحذف، فقط نسجل الخطأ
                }
            }

            // ✅ استرجاع الكمية إلى المستودع قبل الحذف (للأذن الصادرة فقط)
            if ($deliveryNote->type === 'outgoing' && !empty($deliveryNote->material_detail_id)) {
                try {
                    $materialDetail = \App\Models\MaterialDetail::find($deliveryNote->material_detail_id);

                    if ($materialDetail) {
                        // استرجاع الكمية المسحوبة من حقل delivery_quantity
                        $quantityToRestore = $deliveryNote->delivery_quantity ?? 0;
                        if ($quantityToRestore > 0) {
                            $materialDetail->quantity += $quantityToRestore;
                            $materialDetail->save();

                            Log::info('✅ تم استرجاع الكمية الصادرة من الأذن المحذوفة', [
                                'material_detail_id' => $deliveryNote->material_detail_id,
                                'warehouse_id' => $materialDetail->warehouse_id,
                                'quantity_restored' => $quantityToRestore,
                                'delivery_note' => $deliveryNote->note_number,
                            ]);
                        }
                    }
                } catch (\Exception $inventoryError) {
                    Log::error('⚠️ فشل استرجاع الكمية الصادرة: ' . $inventoryError->getMessage());
                    // لا نوقف الحذف، فقط نسجل الخطأ
                }
            }

            // Log the operation before deletion
            try {
                $this->logOperation(
                    'delete',
                    'Delete Delivery Note',
                    'تم حذف أذن تسليم: ' . $deliveryNote->note_number,
                    'delivery_notes',
                    $deliveryNote->id,
                    $oldValues,
                    null
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log delivery note deletion: ' . $logError->getMessage());
                // Don't throw - just log the error
            }

            $deliveryNote->delete();

            // إرسال إشعار بحذف أذن التسليم
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'تم حذف أذن تسليم',
                        'تم حذف أذن التسليم برقم: ' . $deliveryNote->note_number,
                        'delete_delivery_note',
                        'danger',
                        'feather icon-trash-2',
                        route('manufacturing.delivery-notes.index')
                    );
                }

                // ✅ تخزين إشعار الحذف في قاعدة البيانات
                $this->storeNotification(
                    'delivery_note_deleted',
                    'حذف أذن تسليم',
                    'تم حذف أذن التسليم برقم: ' . $deliveryNote->note_number,
                    'danger',
                    'fas fa-trash',
                    route('manufacturing.delivery-notes.index')
                );
            } catch (\Exception $notifError) {
                Log::warning('Failed to send delivery note delete notifications: ' . $notifError->getMessage());
            }

            return redirect()->route('manufacturing.delivery-notes.index')
                ->with('success', 'تم حذف أذن التسليم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting delivery note: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف أذن التسليم: ' . $e->getMessage());
        }
    }

    /**
     * Toggle delivery note status (active/inactive).
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $deliveryNote = DeliveryNote::findOrFail($id);

            $oldStatus = $deliveryNote->is_active;
            $newStatus = !$oldStatus;

            $deliveryNote->update(['is_active' => $newStatus]);

            // Log the status change
            try {
                $this->logOperation(
                    'update',
                    'Toggle Status',
                    'تم تغيير حالة أذن التسليم من ' . ($oldStatus ? 'مفعلة' : 'معطلة') . ' إلى ' . ($newStatus ? 'مفعلة' : 'معطلة'),
                    'delivery_notes',
                    $deliveryNote->id,
                    ['is_active' => $oldStatus],
                    ['is_active' => $newStatus]
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log delivery note status change: ' . $logError->getMessage());
                // Don't throw - just log the error
            }

            $statusText = $newStatus ? 'مفعلة' : 'معطلة';

            // إرسال إشعار بتغيير حالة أذن التسليم
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'تغيير حالة أذن التسليم',
                        'تم تغيير حالة أذن التسليم رقم ' . $deliveryNote->note_number . ' إلى ' . $statusText,
                        'toggle_delivery_note_status',
                        'info',
                        'feather icon-toggle-' . ($newStatus ? 'right' : 'left'),
                        route('manufacturing.delivery-notes.show', $deliveryNote->id)
                    );
                }

                // ✅ تخزين إشعار تغيير الحالة في قاعدة البيانات
                $this->storeNotification(
                    'delivery_note_status_changed',
                    'تغيير حالة أذن التسليم',
                    'تم تغيير حالة أذن التسليم رقم ' . $deliveryNote->note_number . ' إلى ' . $statusText,
                    'info',
                    'fas fa-exchange-alt',
                    route('manufacturing.delivery-notes.show', $deliveryNote->id)
                );
            } catch (\Exception $notifError) {
                Log::warning('Failed to send delivery note status toggle notifications: ' . $notifError->getMessage());
            }

            return redirect()->back()
                           ->with('success', 'تم تغيير حالة أذن التسليم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error toggling delivery note status: ' . $e->getMessage());
            return redirect()->back()
                           ->withErrors(['error' => 'فشل في تغيير حالة أذن التسليم: ' . $e->getMessage()]);
        }
    }

    /**
     * Add invoice details to an existing delivery note
     */
    public function addInvoiceDetails(Request $request, $id)
    {
        try {
            $deliveryNote = DeliveryNote::findOrFail($id);
            $oldValues = $deliveryNote->toArray();

            $validated = $request->validate([
                'invoice_number' => 'required|string|max:100',
                'invoice_weight' => 'required|numeric|min:0',
                'invoice_reference_number' => 'nullable|string|max:100',
            ]);

            $validated['approved_by'] = Auth::id();
            $validated['approved_at'] = now();

            // Calculate discrepancy
            if ($deliveryNote->actual_weight) {
                $validated['weight_discrepancy'] = $deliveryNote->actual_weight - $validated['invoice_weight'];
            }

            $deliveryNote->update($validated);
            $newValues = $deliveryNote->fresh()->toArray();

            // Log the operation
            try {
                $this->logOperation(
                    'update',
                    'Add Invoice Details',
                    'تم إضافة تفاصيل الفاتورة لأذن التسليم: ' . $deliveryNote->note_number,
                    'delivery_notes',
                    $deliveryNote->id,
                    $oldValues,
                    $newValues
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log invoice details addition: ' . $logError->getMessage());
                // Don't throw
            }

            // إرسال إشعار بإضافة تفاصيل الفاتورة
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'إضافة تفاصيل فاتورة',
                        'تم إضافة تفاصيل الفاتورة لأذن التسليم رقم: ' . $deliveryNote->note_number,
                        'add_invoice_details',
                        'success',
                        'feather icon-file-text',
                        route('manufacturing.delivery-notes.show', $deliveryNote->id)
                    );
                }

                // ✅ تخزين إشعار إضافة تفاصيل الفاتورة في قاعدة البيانات
                $this->storeNotification(
                    'invoice_details_added',
                    'إضافة تفاصيل فاتورة',
                    'تم إضافة تفاصيل الفاتورة لأذن التسليم رقم: ' . $deliveryNote->note_number . ' (فاتورة: ' . $validated['invoice_number'] . ')',
                    'success',
                    'fas fa-file-invoice',
                    route('manufacturing.delivery-notes.show', $deliveryNote->id)
                );
            } catch (\Exception $notifError) {
                Log::warning('Failed to send add invoice details notifications: ' . $notifError->getMessage());
            }

            return redirect()->back()
                ->with('success', 'تم إضافة تفاصيل الفاتورة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error adding invoice details: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Update delivery note status (pending, approved, rejected, completed)
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $deliveryNote = DeliveryNote::findOrFail($id);
            $oldValues = $deliveryNote->toArray();

            $validated = $request->validate([
                'status' => 'required|in:pending,approved,rejected,completed',
            ]);

            $deliveryNote->update($validated);
            $newValues = $deliveryNote->fresh()->toArray();

            // Log the operation
            try {
                $statusLabel = \App\Models\DeliveryNoteStatus::from($validated['status'])->label();
                $this->logOperation(
                    'update',
                    'Update Status',
                    'تم تغيير حالة أذن التسليم إلى ' . $statusLabel . ': ' . $deliveryNote->note_number,
                    'delivery_notes',
                    $deliveryNote->id,
                    $oldValues,
                    $newValues
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log delivery note status update: ' . $logError->getMessage());
                // Don't throw - just log the error
            }

            // إرسال إشعار بتحديث حالة أذن التسليم
            try {
                $statusLabel = \App\Models\DeliveryNoteStatus::from($validated['status'])->label();
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'تحديث حالة أذن التسليم',
                        'تم تغيير حالة أذن التسليم رقم ' . $deliveryNote->note_number . ' إلى ' . $statusLabel,
                        'update_delivery_note_status',
                        'info',
                        'feather icon-edit',
                        route('manufacturing.delivery-notes.show', $deliveryNote->id)
                    );
                }

                // ✅ تخزين إشعار تحديث الحالة في قاعدة البيانات
                $this->storeNotification(
                    'delivery_note_status_updated',
                    'تحديث حالة أذن التسليم',
                    'تم تغيير حالة أذن التسليم رقم ' . $deliveryNote->note_number . ' إلى ' . $statusLabel,
                    'info',
                    'fas fa-sync-alt',
                    route('manufacturing.delivery-notes.show', $deliveryNote->id)
                );
            } catch (\Exception $notifError) {
                Log::warning('Failed to send delivery note status update notifications: ' . $notifError->getMessage());
            }

            return redirect()->back()
                           ->with('success', 'تم تحديث حالة الأذن بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating delivery note status: ' . $e->getMessage());
            return redirect()->back()
                           ->withErrors(['error' => 'فشل في تحديث حالة الأذن: ' . $e->getMessage()]);
        }
    }

    /**
     * Change delivery note status (active/inactive)
     */
    public function changeStatus(Request $request, $id)
    {
        try {
            $deliveryNote = DeliveryNote::findOrFail($id);
            $oldValues = $deliveryNote->toArray();

            $validated = $request->validate([
                'is_active' => 'required|boolean',
            ]);

            $deliveryNote->update($validated);
            $newValues = $deliveryNote->fresh()->toArray();

            // Log the operation
            try {
                $statusText = $validated['is_active'] ? 'فعّالة' : 'معطّلة';
                $this->logOperation(
                    'update',
                    'Change Status',
                    'تم تغيير حالة أذن التسليم إلى ' . $statusText . ': ' . $deliveryNote->note_number,
                    'delivery_notes',
                    $deliveryNote->id,
                    $oldValues,
                    $newValues
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log status change: ' . $logError->getMessage());
                // Don't throw
            }

            // إرسال إشعار بتغيير حالة أذن التسليم
            try {
                $statusText = $validated['is_active'] ? 'فعّالة' : 'معطّلة';
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'تغيير حالة أذن التسليم',
                        'تم تغيير حالة أذن التسليم رقم ' . $deliveryNote->note_number . ' إلى ' . $statusText,
                        'change_delivery_note_status',
                        'info',
                        'feather icon-toggle-' . ($validated['is_active'] ? 'right' : 'left'),
                        route('manufacturing.delivery-notes.show', $deliveryNote->id)
                    );
                }

                // ✅ تخزين إشعار تغيير الحالة النشطة في قاعدة البيانات
                $this->storeNotification(
                    'delivery_note_active_status_changed',
                    'تغيير حالة النشاط',
                    'تم تغيير حالة نشاط أذن التسليم رقم ' . $deliveryNote->note_number . ' إلى ' . $statusText,
                    $validated['is_active'] ? 'success' : 'warning',
                    $validated['is_active'] ? 'fas fa-check-circle' : 'fas fa-ban',
                    route('manufacturing.delivery-notes.show', $deliveryNote->id)
                );
            } catch (\Exception $notifError) {
                Log::warning('Failed to send delivery note status change notifications: ' . $notifError->getMessage());
            }

            return redirect()->back()
                ->with('success', 'تم تغيير حالة الأذن بنجاح');
        } catch (\Exception $e) {
            Log::error('Error changing delivery note status: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
