<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Enums\DeliveryNoteStatus;
use App\Models\Customer;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\MaterialMovement;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Stage4Box;

class FinishedProductDeliveryController extends Controller
{
    /**
     * عرض قائمة إذونات الصرف للمنتجات النهائية
     */
    public function index(Request $request)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_READ')) {
            abort(403, 'ليس لديك صلاحية لعرض إذونات الصرف');
        }

        $query = DeliveryNote::finishedProductOutgoing()
            ->with(['customer', 'recordedBy', 'approvedBy', 'items.stage4Box']);

        // فلترة حسب الحالة
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // فلترة حسب العميل
        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        // البحث في رقم الإذن أو رمز العميل
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('note_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('customer', function($cq) use ($request) {
                      $cq->where('customer_code', 'like', '%' . $request->search . '%')
                         ->orWhere('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // فلترة حسب التاريخ
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // للمدير: عرض الكل، لغيره: عرض ما أنشأه فقط إلا إذا كان لديه صلاحية VIEW_ALL
        if (!Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_VIEW_ALL')) {
            $query->where('prepared_by', Auth::id());
        }

        $deliveryNotes = $query->orderBy('created_at', 'desc')->paginate(20);

        $customers = Customer::active()->orderBy('name')->get();
        $statuses = DeliveryNoteStatus::cases();
        
        // عدد الإذونات المعلقة
        $pendingCount = DeliveryNote::finishedProductOutgoing()
            ->where('status', DeliveryNoteStatus::PENDING)
            ->count();

        return view('manufacturing::finished-product-deliveries.index', compact('deliveryNotes', 'customers', 'statuses', 'pendingCount'));
    }

    /**
     * عرض صفحة إنشاء إذن صرف جديد
     */
    public function create()
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_CREATE')) {
            abort(403, 'ليس لديك صلاحية لإنشاء إذن صرف');
        }

        $customers = Customer::active()->orderBy('name')->get();

        return view('manufacturing::finished-product-deliveries.create', compact('customers'));
    }

    /**
     * حفظ إذن صرف جديد
     */
    public function store(Request $request)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_CREATE')) {
            return response()->json(['error' => 'ليس لديك صلاحية لإنشاء إذن صرف'], 403);
        }

        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'notes' => 'nullable|string',
            'boxes' => 'required|array|min:1',
            'boxes.*.box_id' => 'required|exists:stage4_boxes,id',
            'boxes.*.barcode' => 'required|string',
        ], [
            'boxes.required' => 'يجب اختيار صناديق واحدة على الأقل',
            'boxes.*.box_id.required' => 'معرف الصندوق مطلوب',
            'boxes.*.box_id.exists' => 'الصندوق المحدد غير موجود',
        ]);

        try {
            DB::beginTransaction();

            // توليد رقم إذن فريد
            $noteNumber = $this->generateDeliveryNoteNumber();

            // إنشاء إذن الصرف
            $deliveryNote = DeliveryNote::create([
                'note_number' => $noteNumber,
                'type' => 'finished_product_outgoing',
                'status' => DeliveryNoteStatus::PENDING,
                'prepared_by' => Auth::id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'source_type' => 'stage4',
                'source_id' => null, // يمكن استخدامه لاحقاً للربط بمرحلة معينة
                'delivery_date' => now()->toDateString(),
                'received_by' => Auth::id(),
            ]);

            // إضافة الصناديق
            $totalWeight = 0;
            $warehouseId = null;
            
            foreach ($validated['boxes'] as $boxData) {
                $stage4Box = Stage4Box::with('warehouse')->findOrFail($boxData['box_id']);

                // التحقق من أن الصندوق في المستودع
                if ($stage4Box->status !== 'in_warehouse') {
                    throw new \Exception('الصندوق ' . $stage4Box->barcode . ' غير موجود في المستودع. يجب اعتماد إدخاله للمستودع أولاً');
                }

                if (!$stage4Box->warehouse_id) {
                    throw new \Exception('الصندوق ' . $stage4Box->barcode . ' ليس له مستودع محدد');
                }

                // تحديد المستودع المستخدم (يجب أن تكون جميع الصناديق من نفس المستودع)
                if ($warehouseId === null) {
                    $warehouseId = $stage4Box->warehouse_id;
                } elseif ($warehouseId !== $stage4Box->warehouse_id) {
                    throw new \Exception('جميع الصناديق يجب أن تكون من نفس المستودع');
                }

                // إنشاء عنصر إذن الصرف
                $boxWeight = $stage4Box->total_weight ?? $stage4Box->weight ?? 0;
                
                DeliveryNoteItem::create([
                    'delivery_note_id' => $deliveryNote->id,
                    'stage4_box_id' => $stage4Box->id,
                    'barcode' => $stage4Box->barcode,
                    'packaging_type' => $stage4Box->packaging_type,
                    'weight' => $boxWeight,
                ]);

                $totalWeight += $boxWeight;

                // تحديث حالة الصندوق
                $stage4Box->update([
                    'status' => 'shipped',
                    'warehouse_id' => null // إزالة المستودع بعد الصرف
                ]);
            }

            // تسجيل حركة خروج في material_movements
            if ($warehouseId) {
                MaterialMovement::create([
                    'movement_number' => MaterialMovement::generateMovementNumber(),
                    'movement_type' => 'outgoing',
                    'source' => 'production',
                    'from_warehouse_id' => $warehouseId,
                    'quantity' => count($validated['boxes']),
                    'description' => 'صرف منتجات تامة للعميل',
                    'reference_number' => $noteNumber,
                    'notes' => "إذن الصرف رقم {$noteNumber} - عدد الصناديق: " . count($validated['boxes']) . " - الوزن: {$totalWeight} كجم",
                    'created_by' => Auth::id(),
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'movement_date' => now(),
                    'status' => 'completed',
                    'material_id' => 1,
                    'unit_id' => 1,
                ]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إنشاء إذن الصرف بنجاح',
                    'delivery_note_id' => $deliveryNote->id,
                    'note_number' => $deliveryNote->note_number,
                ]);
            }

            return redirect()->route('manufacturing.finished-product-deliveries.show', $deliveryNote->id)
                ->with('success', 'تم إنشاء إذن الصرف بنجاح - رقم الإذن: ' . $deliveryNote->note_number);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating finished product delivery: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['error' => 'فشل إنشاء إذن الصرف: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'فشل إنشاء إذن الصرف: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * عرض تفاصيل إذن صرف
     */
    public function show($id)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_READ')) {
            abort(403, 'ليس لديك صلاحية لعرض إذونات الصرف');
        }

        $deliveryNote = DeliveryNote::with([
            'customer',
            'recordedBy',
            'approvedBy',
            'items.stage4Box'
        ])->findOrFail($id);

        // التحقق من الصلاحية - إذا لم يكن لديه صلاحية VIEW_ALL يجب أن يكون هو من أنشأه
        if (!Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_VIEW_ALL') 
            && $deliveryNote->prepared_by !== Auth::id()) {
            abort(403, 'ليس لديك صلاحية لعرض هذا الإذن');
        }
        
        // قائمة العملاء للاستخدام في modal الاعتماد
        $customers = Customer::active()->orderBy('name')->get();

        return view('manufacturing::finished-product-deliveries.show', compact('deliveryNote', 'customers'));
    }

    /**
     * عرض صفحة التعديل
     */
    public function edit($id)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_UPDATE')) {
            abort(403, 'ليس لديك صلاحية لتعديل إذونات الصرف');
        }

        $deliveryNote = DeliveryNote::with(['customer', 'items.stage4Box'])
            ->findOrFail($id);

        // يمكن التعديل فقط إذا كان الإذن قيد الانتظار
        if ($deliveryNote->status !== DeliveryNoteStatus::PENDING) {
            return redirect()->route('manufacturing.finished-product-deliveries.show', $id)
                ->with('error', 'لا يمكن تعديل إذن صرف تم اعتماده أو رفضه');
        }

        // التحقق من أنه هو من أنشأه
        if ($deliveryNote->prepared_by !== Auth::id()) {
            abort(403, 'يمكنك تعديل الإذونات التي أنشأتها فقط');
        }

        $customers = Customer::active()->orderBy('name')->get();

        return view('manufacturing::finished-product-deliveries.edit', compact('deliveryNote', 'customers'));
    }

    /**
     * تحديث إذن صرف
     */
    public function update(Request $request, $id)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_UPDATE')) {
            return response()->json(['error' => 'ليس لديك صلاحية لتعديل إذونات الصرف'], 403);
        }

        $deliveryNote = DeliveryNote::findOrFail($id);

        // يمكن التعديل فقط إذا كان الإذن قيد الانتظار
        if ($deliveryNote->status !== DeliveryNoteStatus::PENDING) {
            return response()->json(['error' => 'لا يمكن تعديل إذن صرف تم اعتماده أو رفضه'], 400);
        }

        // التحقق من أنه هو من أنشأه
        if ($deliveryNote->prepared_by !== Auth::id()) {
            return response()->json(['error' => 'يمكنك تعديل الإذونات التي أنشأتها فقط'], 403);
        }

        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'notes' => 'nullable|string',
        ]);

        try {
            $deliveryNote->update($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث إذن الصرف بنجاح'
                ]);
            }

            return redirect()->route('manufacturing.finished-product-deliveries.show', $id)
                ->with('success', 'تم تحديث إذن الصرف بنجاح');
        } catch (\Exception $e) {
            \Log::error('Error updating finished product delivery: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['error' => 'فشل تحديث إذن الصرف'], 500);
            }

            return redirect()->back()->with('error', 'فشل تحديث إذن الصرف')->withInput();
        }
    }

    /**
     * عرض الإذونات المعلقة (للمدير)
     */
    public function pendingApproval()
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_APPROVE')) {
            abort(403, 'ليس لديك صلاحية لاعتماد إذونات الصرف');
        }

        $deliveryNotes = DeliveryNote::finishedProductOutgoing()
            ->where('status', DeliveryNoteStatus::PENDING)
            ->with(['customer', 'recordedBy', 'items.stage4Box'])
            ->orderBy('created_at', 'asc')
            ->paginate(20);
        
        // قائمة العملاء
        $customers = Customer::active()->orderBy('name')->get();

        return view('manufacturing::finished-product-deliveries.pending-approval', compact('deliveryNotes', 'customers'));
    }

    /**
     * اعتماد إذن صرف
     */
    public function approve(Request $request, $id)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_APPROVE')) {
            return response()->json(['error' => 'ليس لديك صلاحية لاعتماد إذونات الصرف'], 403);
        }

        $deliveryNote = DeliveryNote::findOrFail($id);

        if (!$deliveryNote->canApprove()) {
            return response()->json(['error' => 'لا يمكن اعتماد هذا الإذن'], 400);
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ], [
            'customer_id.required' => 'يجب اختيار العميل قبل الاعتماد',
        ]);

        try {
            $deliveryNote->approve(Auth::user(), $validated['customer_id']);

            return response()->json([
                'success' => true,
                'message' => 'تم اعتماد إذن الصرف بنجاح'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error approving delivery note: ' . $e->getMessage());
            return response()->json(['error' => 'فشل اعتماد إذن الصرف'], 500);
        }
    }

    /**
     * رفض إذن صرف
     */
    public function reject(Request $request, $id)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_REJECT')) {
            return response()->json(['error' => 'ليس لديك صلاحية لرفض إذونات الصرف'], 403);
        }

        $deliveryNote = DeliveryNote::findOrFail($id);

        if (!$deliveryNote->canApprove()) {
            return response()->json(['error' => 'لا يمكن رفض هذا الإذن'], 400);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'يجب ذكر سبب الرفض',
        ]);

        try {
            DB::beginTransaction();

            $deliveryNote->reject(Auth::user(), $validated['rejection_reason']);

            // إعادة حالة الصناديق إلى completed
            foreach ($deliveryNote->items as $item) {
                $item->stage4Box->update(['status' => 'completed']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم رفض إذن الصرف'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error rejecting delivery note: ' . $e->getMessage());
            return response()->json(['error' => 'فشل رفض إذن الصرف'], 500);
        }
    }

    /**
     * طباعة إذن صرف
     */
    public function print($id)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_PRINT')) {
            abort(403, 'ليس لديك صلاحية لطباعة إذونات الصرف');
        }

        $deliveryNote = DeliveryNote::with([
            'customer',
            'recordedBy',
            'approvedBy',
            'items.stage4Box'
        ])->findOrFail($id);

        if (!$deliveryNote->canPrint()) {
            return redirect()->back()->with('error', 'لا يمكن طباعة إذن غير معتمد');
        }

        // زيادة عداد الطباعة
        $deliveryNote->incrementPrintCount();

        return view('manufacturing::finished-product-deliveries.print', compact('deliveryNote'));
    }

    /**
     * توليد رقم إذن صرف فريد
     */
    private function generateDeliveryNoteNumber()
    {
        $prefix = 'FPD-' . date('Y') . '-'; // FPD = Finished Product Delivery
        $maxAttempts = 50;
        $attempt = 0;

        // الحصول على أعلى رقم تسلسلي لهذا العام
        $baseNumber = 1;
        $lastNote = DeliveryNote::where('note_number', 'like', $prefix . '%')
            ->where('type', 'finished_product_outgoing')
            ->orderByRaw('CAST(SUBSTRING(note_number, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        if ($lastNote) {
            $numberPart = substr($lastNote->note_number, strlen($prefix));
            if (preg_match('/^(\d+)/', $numberPart, $matches)) {
                $lastNumber = (int)$matches[1];
                $baseNumber = $lastNumber + 1;
            }
        }

        // محاولة إيجاد رقم فريد
        while ($attempt < $maxAttempts) {
            $noteNumber = $prefix . str_pad($baseNumber + $attempt, 4, '0', STR_PAD_LEFT);

            // التحقق من عدم وجود الرقم في قاعدة البيانات
            $exists = DeliveryNote::where('note_number', $noteNumber)->exists();

            if (!$exists) {
                return $noteNumber;
            }

            $attempt++;
        }

        throw new \Exception('فشل في توليد رقم إذن فريد بعد ' . $maxAttempts . ' محاولات');
    }

    /**
     * الحصول على الصناديق المتاحة من المرحلة الرابعة (API)
     */
    public function getAvailableBoxes(Request $request)
    {
        try {
            \Log::info('getAvailableBoxes called', [
                'search' => $request->search,
                'packaging_type' => $request->packaging_type
            ]);

            // البحث عن الصناديق التي تم إدخالها للمستودع فقط
            $query = Stage4Box::where('status', 'in_warehouse')
                ->with(['creator', 'boxCoils']);

            if ($request->has('search') && !empty($request->search)) {
                $query->where('barcode', 'like', '%' . $request->search . '%');
            }

            // فلترة حسب نوع التغليف
            if ($request->has('packaging_type') && !empty($request->packaging_type)) {
                $query->where('packaging_type', $request->packaging_type);
            }

            $boxes = $query->orderBy('created_at', 'desc')->limit(20)->get();

            // إضافة معلومات إضافية
            $boxes->transform(function($box) {
                return [
                    'id' => $box->id,
                    'barcode' => $box->barcode,
                    'packaging_type' => $box->packaging_type,
                    'weight' => $box->total_weight,
                    'coils_count' => $box->coils_count,
                    'status' => $box->status,
                    'worker' => $box->creator ? ['name' => $box->creator->name] : null,
                    'created_at' => $box->created_at
                ];
            });

            \Log::info('Boxes found', ['count' => $boxes->count()]);

            return response()->json($boxes);
        } catch (\Exception $e) {
            \Log::error('Error in getAvailableBoxes: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * حذف إذن صرف
     */
    public function destroy($id)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_DELETE')) {
            return response()->json(['error' => 'ليس لديك صلاحية لحذف إذونات الصرف'], 403);
        }

        try {
            $deliveryNote = DeliveryNote::findOrFail($id);

            // يمكن الحذف فقط إذا كان الإذن قيد الانتظار أو مرفوض
            if (!in_array($deliveryNote->status, [DeliveryNoteStatus::PENDING, DeliveryNoteStatus::REJECTED])) {
                return response()->json(['error' => 'لا يمكن حذف إذن معتمد'], 400);
            }

            // التحقق من أنه هو من أنشأه أو لديه صلاحية VIEW_ALL
            if ($deliveryNote->prepared_by !== Auth::id() 
                && !Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_VIEW_ALL')) {
                return response()->json(['error' => 'يمكنك حذف الإذونات التي أنشأتها فقط'], 403);
            }

            DB::beginTransaction();

            // إعادة حالة الصناديق إلى completed
            foreach ($deliveryNote->items as $item) {
                $item->stage4Box->update(['status' => 'completed']);
            }

            // حذف العناصر
            $deliveryNote->items()->delete();

            // حذف الإذن
            $deliveryNote->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف إذن الصرف بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting finished product delivery: ' . $e->getMessage());
            return response()->json(['error' => 'فشل حذف إذن الصرف'], 500);
        }
    }
}
