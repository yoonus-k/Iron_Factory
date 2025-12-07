<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\WarehouseIntakeRequest;
use App\Models\WarehouseIntakeItem;
use App\Models\Stage4Box;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseIntakeController extends Controller
{
    /**
     * عرض قائمة طلبات الإدخال
     */
    public function index()
    {
        $requests = WarehouseIntakeRequest::with(['requestedBy', 'approvedBy', 'items'])
            ->latest()
            ->paginate(20);

        return view('manufacturing::warehouse-intake.index', compact('requests'));
    }

    /**
     * عرض صفحة إنشاء طلب إدخال جديد
     */
    public function create()
    {
        $availableBoxes = Stage4Box::with(['creator'])
            ->whereIn('status', ['packed', 'completed'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($box) {
                $materials = $box->resolvedMaterials();

                // دمج المواصفات في نص واحد مثل finished-product-deliveries
                $specs = [];
                $colors = $materials->pluck('color')->unique()->filter();
                $materialTypes = $materials->pluck('plastic_type')->unique()->filter();
                $wireSizes = $materials->pluck('wire_size')->unique()->filter();

                if ($colors->isNotEmpty()) {
                    $specs[] = $colors->implode(', ');
                }
                if ($materialTypes->isNotEmpty()) {
                    $specs[] = $materialTypes->implode(', ');
                }
                if ($wireSizes->isNotEmpty()) {
                    $specs[] = $wireSizes->implode(', ');
                }

                return [
                    'id' => $box->id,
                    'barcode' => $box->barcode,
                    'packaging_type' => $box->packaging_type,
                    'weight' => $box->total_weight ?? $box->weight ?? 0,
                    'coils_count' => $materials->count(),
                    'colors' => $colors->values()->toArray(),
                    'wire_sizes' => $wireSizes->values()->toArray(),
                    'material_types' => $materialTypes->values()->toArray(),
                    'specifications' => implode(' - ', $specs), // نص واحد للمواصفات
                    'creator' => $box->creator ? $box->creator->name : null,
                    'status' => $box->status,
                    'created_at' => optional($box->created_at)->format('Y-m-d H:i'),
                ];
            });

        return view('manufacturing::warehouse-intake.create', compact('availableBoxes'));
    }

    /**
     * API: الحصول على الصناديق المتاحة من المرحلة الرابعة
     */
    public function getAvailableBoxes(Request $request)
    {
        $query = Stage4Box::with(['creator', 'boxCoils'])
            ->whereIn('status', ['packed', 'completed']) // جاهزة للإرسال للمستودع
            ->latest();

        // البحث بالباركود
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('barcode', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        // فلتر حسب نوع التغليف
        if ($request->filled('packaging_type')) {
            $query->where('packaging_type', $request->packaging_type);
        }

        $boxes = $query->get()->map(function ($box) {
            return [
                'id' => $box->id,
                'barcode' => $box->barcode,
                'packaging_type' => $box->packaging_type,
                'weight' => $box->total_weight ?? $box->weight ?? 0,
                'coils_count' => $box->boxCoils->count(),
                'creator' => $box->creator ? $box->creator->name : null,
                'status' => $box->status,
            ];
        });

        return response()->json($boxes);
    }

    /**
     * حفظ طلب إدخال جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
            'boxes' => 'required|array|min:1',
            'boxes.*.box_id' => 'required|exists:stage4_boxes,id',
            'boxes.*.barcode' => 'required|string',
        ], [
            'boxes.required' => 'يجب اختيار صندوق واحد على الأقل',
            'boxes.min' => 'يجب اختيار صندوق واحد على الأقل',
        ]);

        try {
            DB::beginTransaction();

            // توليد رقم الطلب
            $requestNumber = $this->generateRequestNumber();

            // حساب الإجماليات
            $boxesCount = count($validated['boxes']);
            $totalWeight = 0;

            // إنشاء الطلب
            $intakeRequest = WarehouseIntakeRequest::create([
                'request_number' => $requestNumber,
                'status' => 'pending',
                'requested_by' => Auth::id(),
                'notes' => $validated['notes'] ?? null,
                'boxes_count' => $boxesCount,
                'total_weight' => 0, // سنحدثه بعد إضافة العناصر
            ]);

            // إضافة الصناديق
            foreach ($validated['boxes'] as $boxData) {
                $stage4Box = Stage4Box::findOrFail($boxData['box_id']);

                // التحقق من حالة الصندوق
                if (!in_array($stage4Box->status, ['packed', 'completed'])) {
                    throw new \Exception('الصندوق ' . $stage4Box->barcode . ' غير متاح للإدخال');
                }

                $boxWeight = $stage4Box->total_weight ?? $stage4Box->weight ?? 0;
                $totalWeight += $boxWeight;

                WarehouseIntakeItem::create([
                    'intake_request_id' => $intakeRequest->id,
                    'stage4_box_id' => $stage4Box->id,
                    'barcode' => $stage4Box->barcode,
                    'packaging_type' => $stage4Box->packaging_type,
                    'weight' => $boxWeight,
                ]);

                // تحديث حالة الصندوق إلى "قيد الإدخال"
                $stage4Box->update(['status' => 'intake_pending']);
            }

            // تحديث الوزن الإجمالي
            $intakeRequest->update(['total_weight' => $totalWeight]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إنشاء طلب الإدخال بنجاح',
                    'request_id' => $intakeRequest->id,
                    'request_number' => $intakeRequest->request_number,
                ]);
            }

            return redirect()->route('manufacturing.warehouse-intake.show', $intakeRequest->id)
                ->with('success', 'تم إنشاء طلب الإدخال بنجاح - رقم: ' . $intakeRequest->request_number);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating warehouse intake request: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['error' => 'فشل إنشاء طلب الإدخال: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'فشل إنشاء طلب الإدخال: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * عرض تفاصيل طلب إدخال
     */
    public function show($id)
    {
        $intakeRequest = WarehouseIntakeRequest::with([
            'requestedBy',
            'approvedBy',
            'items.stage4Box'
        ])->findOrFail($id);

        // إضافة معلومات المواد لكل صندوق
        return view('manufacturing::warehouse-intake.show', compact('intakeRequest'));
    }

    /**
     * عرض الطلبات المعلقة (للمستودع)
     */
    public function pendingApproval()
    {
        $pendingRequests = WarehouseIntakeRequest::with(['requestedBy', 'items'])
            ->pending()
            ->latest()
            ->paginate(20);

        // جلب قائمة المستودعات النشطة
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('manufacturing::warehouse-intake.pending-approval', compact('pendingRequests', 'warehouses'));
    }

    /**
     * اعتماد طلب إدخال
     */
    public function approve(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('WAREHOUSE_INTAKE_APPROVE')) {
            return response()->json(['error' => 'ليس لديك صلاحية لاعتماد طلبات الإدخال'], 403);
        }

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
        ], [
            'warehouse_id.required' => 'يجب اختيار المستودع',
            'warehouse_id.exists' => 'المستودع غير موجود',
        ]);

        $intakeRequest = WarehouseIntakeRequest::findOrFail($id);

        if (!$intakeRequest->canApprove()) {
            return response()->json(['error' => 'لا يمكن اعتماد هذا الطلب'], 400);
        }

        try {
            $warehouse = Warehouse::findOrFail($validated['warehouse_id']);
            
            if ($intakeRequest->approve(Auth::user(), $validated['warehouse_id'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم اعتماد طلب الإدخال بنجاح - تم إدخال ' . $intakeRequest->boxes_count . ' صندوق إلى مستودع: ' . $warehouse->warehouse_name
                ]);
            } else {
                return response()->json(['error' => 'فشل اعتماد الطلب'], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Error approving intake request: ' . $e->getMessage());
            return response()->json(['error' => 'فشل اعتماد الطلب: ' . $e->getMessage()], 500);
        }
    }

    /**
     * رفض طلب إدخال
     */
    public function reject(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('WAREHOUSE_INTAKE_REJECT')) {
            return response()->json(['error' => 'ليس لديك صلاحية لرفض طلبات الإدخال'], 403);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ], [
            'rejection_reason.required' => 'يجب ذكر سبب الرفض',
        ]);

        $intakeRequest = WarehouseIntakeRequest::findOrFail($id);

        if (!$intakeRequest->canApprove()) {
            return response()->json(['error' => 'لا يمكن رفض هذا الطلب'], 400);
        }

        try {
            DB::beginTransaction();

            // رفض الطلب
            if ($intakeRequest->reject(Auth::user(), $validated['rejection_reason'])) {
                // إرجاع حالة الصناديق
                foreach ($intakeRequest->items as $item) {
                    $item->stage4Box->update(['status' => 'completed']);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'تم رفض الطلب - تم إرجاع الصناديق للمرحلة الرابعة'
                ]);
            } else {
                DB::rollBack();
                return response()->json(['error' => 'فشل رفض الطلب'], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error rejecting intake request: ' . $e->getMessage());
            return response()->json(['error' => 'فشل رفض الطلب'], 500);
        }
    }

    /**
     * طباعة إذن الإدخال
     */
    public function print($id)
    {
        $intakeRequest = WarehouseIntakeRequest::with([
            'requestedBy',
            'approvedBy',
            'items.stage4Box'
        ])->findOrFail($id);

        if (!$intakeRequest->canPrint()) {
            return redirect()->back()->with('error', 'لا يمكن طباعة إذن غير معتمد');
        }

        // إضافة معلومات المواد لكل صندوق
        return view('manufacturing::warehouse-intake.print', compact('intakeRequest'));
    }

    /**
     * توليد رقم طلب فريد
     */
    private function generateRequestNumber()
    {
        $prefix = 'WIR-' . date('Y') . '-'; // WIR = Warehouse Intake Request
        
        $lastRequest = WarehouseIntakeRequest::where('request_number', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(request_number, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        $number = 1;
        if ($lastRequest) {
            $numberPart = substr($lastRequest->request_number, strlen($prefix));
            if (preg_match('/^(\d+)/', $numberPart, $matches)) {
                $number = (int)$matches[1] + 1;
            }
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

}
