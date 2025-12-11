<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Stage4Box;

class Stage4Controller extends Controller
{
    /**
     * عرض قائمة جميع الكراتين
     * Worker sees only their operations
     * Admin/Supervisor sees all operations
     */
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Query base
        $query = DB::table('stage4_boxes')
            ->leftJoin('material_details', 'stage4_boxes.material_id', '=', 'material_details.id')
            ->leftJoin('materials', 'material_details.material_id', '=', 'materials.id')
            ->leftJoin('users', 'stage4_boxes.created_by', '=', 'users.id')
            ->select(
                'stage4_boxes.*',
                'materials.name_ar as material_name',
                'users.name as created_by_name'
            );

        // إذا لم يكن لديه صلاحية رؤية جميع العمليات، يعرض فقط عملياته
        $viewingAll = $user->hasPermission('VIEW_ALL_STAGE4_OPERATIONS');
        
        if (!$viewingAll) {
            $query->where('stage4_boxes.created_by', $user->id);
        }

        $boxes = $query->orderBy('stage4_boxes.created_at', 'desc')
            ->paginate(20);

        return view('manufacturing::stages.stage4.index', compact('boxes', 'viewingAll'));
    }

    /**
     * عرض صفحة إنشاء كراتين جديدة
     */
    public function create()
    {
        // جلب الكرتون من المستودع
        $carton = DB::table('materials')
            ->join('material_types', 'materials.material_type_id', '=', 'material_types.id')
            ->where('material_types.type_name', 'كرتون')
            ->where('materials.status', 'available')
            ->select(
                'materials.id',
                'materials.name_ar',
                DB::raw('COALESCE((SELECT SUM(quantity) FROM material_details WHERE material_id = materials.id AND quantity > 0), 0) as available_quantity')
            )
            ->first();

        return view('manufacturing::stages.stage4.create', compact('carton'));
    }

    /**
     * Get material by barcode - Supports TWO sources:
     * 1. Stage 3 barcode (ST3-XXX) from stage3_coils table
     * 2. Warehouse direct transfer for Stage 4 (confirmed barcodes)
     */
    public function getByBarcode($barcode)
    {
        \Log::info('Stage4 getByBarcode called', ['barcode' => $barcode]);
        
        // المصدر الأول: باركود المرحلة الثالثة (ST3-XXX)
        $lafaf = DB::table('stage3_coils')
            ->leftJoin('stage2_processed', 'stage3_coils.stage2_id', '=', 'stage2_processed.id')
            ->leftJoin('stage1_stands', 'stage3_coils.stage1_id', '=', 'stage1_stands.id')
            ->leftJoin('materials', 'stage3_coils.material_id', '=', 'materials.id')
            ->where('stage3_coils.barcode', $barcode)
            ->select(
                'stage3_coils.*',
                'stage2_processed.barcode as stage2_barcode',
                'stage1_stands.barcode as stage1_barcode',
                'materials.name_ar as material_name'
            )
            ->first();

        if ($lafaf) {
            \Log::info('Stage4: Found in stage3_coils', ['lafaf_id' => $lafaf->id]);
            
            // التحقق من أن اللفاف ليس معبأ بالفعل
            if ($lafaf->status === 'packed') {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا اللفاف تم تعبئته بالفعل'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'source' => 'stage3',
                'data' => $lafaf
            ]);
        }

        \Log::info('Stage4: Not found in stage3_coils, checking warehouse_direct');
        
        // المصدر الثاني: باركود من المخزن مباشرة للمرحلة الرابعة
        $confirmation = DB::table('production_confirmations')
            ->join('delivery_notes', 'production_confirmations.delivery_note_id', '=', 'delivery_notes.id')
            ->join('material_batches', 'production_confirmations.batch_id', '=', 'material_batches.id')
            ->join('materials', 'material_batches.material_id', '=', 'materials.id')
            ->where('delivery_notes.production_barcode', $barcode)
            ->where('production_confirmations.stage_code', 'stage_4')
            ->where('production_confirmations.status', 'confirmed')
            ->select(
                'production_confirmations.id',
                'delivery_notes.production_barcode as barcode',
                'material_batches.material_id',
                DB::raw('COALESCE(production_confirmations.actual_received_quantity, delivery_notes.quantity, 0) as total_weight'),
                'material_batches.unit_id',
                'materials.name_ar as material_name',
                'materials.name_en as material_name_en',
                'delivery_notes.id as delivery_note_id'
            )
            ->first();

        if ($confirmation) {
            \Log::info('Stage4: Found in warehouse_direct', ['confirmation_id' => $confirmation->id]);
            return response()->json([
                'success' => true,
                'source' => 'warehouse_direct',
                'data' => $confirmation
            ]);
        }

        \Log::warning('Stage4: Barcode not found in any source', ['barcode' => $barcode]);
        
        // لم يتم العثور على الباركود في أي من المصدرين
        return response()->json([
            'success' => false,
            'message' => 'لم يتم العثور على الباركود في سجلات المرحلة الثالثة أو التحويلات المباشرة من المخزن. تأكد من: 1) الباركود صحيح 2) اللفاف موجود في المرحلة الثالثة 3) أو مصادق عليه للمرحلة الرابعة من المخزن'
        ], 404);
    }

    /**
     * حفظ بيانات الكراتين (المرحلة الرابعة)
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'lafaf_barcode' => 'required|string',
            'boxes' => 'required|array|min:1',
            'boxes.*.weight' => 'required|numeric|min:0.001',
            'boxes.*.notes' => 'nullable|string',
            'packaging_type' => 'nullable|string|max:100',
            'worker_id' => 'nullable|integer',
            'shift_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // الحصول على بيانات اللفاف
            $lafaf = DB::table('stage3_coils')
                ->where('barcode', $request->lafaf_barcode)
                ->first();

            if (!$lafaf) {
                throw new \Exception('باركود اللفاف غير موجود');
            }

            $boxes = $request->boxes;
            $totalBoxesWeight = array_sum(array_column($boxes, 'weight'));
            $boxesCount = count($boxes);

            // 🔍 التحقق من كمية الكراتين في المستودع
            $carton = DB::table('materials')
                ->join('material_types', 'materials.material_type_id', '=', 'material_types.id')
                ->where('material_types.type_name', 'كرتون')
                ->where('materials.status', 'available')
                ->select('materials.id', 'materials.name_ar')
                ->selectRaw('COALESCE((SELECT SUM(quantity) FROM material_details WHERE material_id = materials.id AND quantity > 0), 0) as available_quantity')
                ->first();

            if (!$carton) {
                throw new \Exception('❌ لا يوجد كرتون متاح في المستودع');
            }

            if ($carton->available_quantity < $boxesCount) {
                throw new \Exception(sprintf(
                    '❌ عدد الكراتين المطلوبة (%d كرتونة) أكبر من العدد المتاح في المستودع (%d كرتونة)',
                    $boxesCount,
                    (int)$carton->available_quantity
                ));
            }

            // التحقق من أن مجموع أوزان الكراتين يساوي وزن اللفاف تقريباً
            $lafafWeight = $lafaf->net_weight ?? $lafaf->total_weight;
            $displayLafafWeight = $lafafWeight;
            $displayTotalWeight = $lafaf->total_weight;
            $difference = abs($lafafWeight - $totalBoxesWeight);
            $tolerance = $lafafWeight * 0.02; // تسامح 2%

            if ($difference > $tolerance) {
                throw new \Exception("مجموع أوزان الكراتين ({$totalBoxesWeight} كجم) لا يساوي الوزن الصافي للَّفاف ({$displayLafafWeight} كجم)" . ($displayTotalWeight && $displayTotalWeight != $displayLafafWeight ? " - إجمالي اللفاف {$displayTotalWeight} كجم" : ''));
            }

            $boxIds = [];
            $boxBarcodes = [];
            $barcodeInfoArray = [];

            // إنشاء الكراتين
            foreach ($boxes as $index => $box) {
                $barcode = $this->generateStageBarcode('stage4');
                
                $boxId = DB::table('stage4_boxes')->insertGetId([
                    'barcode' => $barcode,
                    'parent_barcode' => $request->lafaf_barcode,
                    'material_id' => $lafaf->material_id,
                    'packaging_type' => $request->packaging_type ?? 'standard',
                    'coils_count' => 1, // كرتون واحد من لفاف واحد
                    'total_weight' => $box['weight'],
                    'waste' => 0,
                    'status' => 'packed',
                    'notes' => $box['notes'] ?? null,
                    'created_by' => auth()->id() ?? 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $boxIds[] = $boxId;
                $boxBarcodes[] = $barcode;
                
                // جمع معلومات الباركود للعرض
                $materialName = DB::table('materials')
                    ->join('material_details', 'materials.id', '=', 'material_details.material_id')
                    ->where('material_details.id', $lafaf->material_id)
                    ->value('materials.name_ar');
                    
                $barcodeInfoArray[] = [
                    'barcode' => $barcode,
                    'box_number' => 'كرتون ' . ($index + 1),
                    'material_name' => $materialName ?? 'غير محدد',
                    'weight' => $box['weight'],
                    'lafaf_barcode' => $request->lafaf_barcode,
                    'packaging_type' => $request->packaging_type ?? 'standard',
                    'notes' => $box['notes'] ?? ''
                ];

                // إدراج في جدول box_coils (ربط الكرتون باللفاف)
                DB::table('box_coils')->insert([
                    'box_id' => $boxId,
                    'coil_id' => $lafaf->id,
                    'added_at' => now()
                ]);

                // إدراج سجل في product_tracking
                DB::table('product_tracking')->insert([
                    'barcode' => $barcode,
                    'stage' => 'stage4',
                    'action' => 'packed',
                    'input_barcode' => $request->lafaf_barcode,
                    'output_barcode' => $barcode,
                    'input_weight' => $box['weight'],
                    'output_weight' => $box['weight'],
                    'waste_amount' => 0,
                    'waste_percentage' => 0,
                    'worker_id' => $request->worker_id,
                    'shift_id' => $request->shift_id,
                    'notes' => $box['notes'] ?? null,
                    'metadata' => json_encode([
                        'lafaf_id' => $lafaf->id,
                        'lafaf_barcode' => $request->lafaf_barcode,
                        'box_number' => $index + 1,
                        'total_boxes' => count($boxes),
                        'packaging_type' => $request->packaging_type
                    ]),
                    'created_at' => now()
                ]);

                // 📦 خصم كرتونة واحدة من المستودع
                try {
                    \Log::info("Stage4: Starting carton deduction", [
                        'carton_id' => $carton->id,
                        'box_index' => $index + 1,
                        'barcode' => $barcode
                    ]);
                    
                    $this->deductCartonFromWarehouse($carton->id, 1);
                    
                    \Log::info("Stage4: Carton deducted successfully", [
                        'carton_id' => $carton->id,
                        'box_index' => $index + 1
                    ]);
                } catch (\Exception $e) {
                    \Log::error("Stage4: Carton deduction failed", [
                        'error' => $e->getMessage(),
                        'carton_id' => $carton->id,
                        'box_index' => $index + 1
                    ]);
                    throw $e;
                }

                // إدراج سجل في barcodes
                DB::table('barcodes')->insert([
                    'barcode' => $barcode,
                    'type' => 'stage4',
                    'reference_id' => $boxId,
                    'reference_table' => 'stage4_boxes',
                    'status' => 'active',
                    'scan_count' => 0,
                    'metadata' => json_encode([
                        'weight' => $box['weight'],
                        'box_number' => $index + 1,
                        'total_boxes' => count($boxes)
                    ]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // تحديث حالة اللفاف
            DB::table('stage3_coils')
                ->where('id', $lafaf->id)
                ->update([
                    'status' => 'packed',
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الكراتين بنجاح',
                'data' => [
                    'box_count' => count($boxes),
                    'barcodes' => $boxBarcodes,
                    'total_weight' => $totalBoxesWeight,
                    'barcode_info' => $barcodeInfoArray
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حفظ كرتون واحد (حفظ فوري)
     */
    public function storeSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lafaf_barcode' => 'required|string',
            'lafaf_id' => 'nullable|integer',
            'source' => 'nullable|string',
            'material_id' => 'required|integer',
            'weight' => 'required|numeric|min:0.001',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 🔍 التحقق من كمية الكراتين في المستودع
            $carton = DB::table('materials')
                ->join('material_types', 'materials.material_type_id', '=', 'material_types.id')
                ->where('material_types.type_name', 'كرتون')
                ->where('materials.status', 'available')
                ->select('materials.id', 'materials.name_ar')
                ->selectRaw('COALESCE((SELECT SUM(quantity) FROM material_details WHERE material_id = materials.id AND quantity > 0), 0) as available_quantity')
                ->first();

            if (!$carton) {
                throw new \Exception('❌ لا يوجد كرتون متاح في المستودع');
            }

            if ($carton->available_quantity < 1) {
                throw new \Exception(sprintf(
                    '❌ لا توجد كراتين متاحة في المستودع. الكمية المتاحة: %d كرتونة',
                    (int)$carton->available_quantity
                ));
            }

            // توليد الباركود
            $barcode = $this->generateStageBarcode('stage4');

            // حساب رقم الكرتون
            $existingBoxesCount = DB::table('stage4_boxes')
                ->where('parent_barcode', $request->lafaf_barcode)
                ->count();
            $boxNumber = 'BOX-' . ($existingBoxesCount + 1);

            // إدراج الكرتون
            $boxId = DB::table('stage4_boxes')->insertGetId([
                'barcode' => $barcode,
                'parent_barcode' => $request->lafaf_barcode,
                'material_id' => $request->material_id,
                'packaging_type' => 'standard',
                'coils_count' => 1,
                'total_weight' => $request->weight,
                'waste' => 0,
                'status' => 'packed',
                'notes' => $request->notes,
                'created_by' => auth()->id() ?? 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // إدراج في جدول barcodes
            DB::table('barcodes')->insert([
                'barcode' => $barcode,
                'type' => 'stage4',
                'reference_id' => $boxId,
                'reference_table' => 'stage4_boxes',
                'status' => 'active',
                'scan_count' => 0,
                'metadata' => json_encode([
                    'weight' => $request->weight,
                    'box_number' => $boxNumber
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // جلب اسم المادة
            $materialName = DB::table('materials')
                ->where('materials.id', $request->material_id)
                ->value('name_ar');

            // 📦 خصم كرتونة واحدة من المستودع
            try {
                \Log::info("Stage4 storeSingle: Starting carton deduction", [
                    'carton_id' => $carton->id,
                    'barcode' => $barcode,
                    'box_number' => $boxNumber
                ]);
                
                $this->deductCartonFromWarehouse($carton->id, 1);
                
                \Log::info("Stage4 storeSingle: Carton deducted successfully", [
                    'carton_id' => $carton->id,
                    'barcode' => $barcode
                ]);
            } catch (\Exception $e) {
                \Log::error("Stage4 storeSingle: Carton deduction failed", [
                    'error' => $e->getMessage(),
                    'carton_id' => $carton->id
                ]);
                throw new \Exception('فشل خصم الكرتون من المستودع: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الكرتون بنجاح',
                'data' => [
                    'box_id' => $boxId,
                    'barcode' => $barcode,
                    'box_number' => $boxNumber,
                    'material_name' => $materialName ?? 'غير محدد',
                    'weight' => $request->weight
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * توليد باركود للمرحلة
     */
    private function generateStageBarcode($type)
    {
        $settings = DB::table('barcode_settings')
            ->where('type', $type)
            ->first();

        if (!$settings) {
            DB::table('barcode_settings')->insert([
                'type' => $type,
                'prefix' => 'BOX4',
                'format' => '{prefix}-{year}-{number}',
                'current_number' => 0,
                'padding' => 4,
                'auto_increment' => 1,
                'year' => date('Y'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $settings = DB::table('barcode_settings')
                ->where('type', $type)
                ->first();
        }

        $currentYear = date('Y');
        if ($settings->year != $currentYear) {
            DB::table('barcode_settings')
                ->where('type', $type)
                ->update([
                    'current_number' => 0,
                    'year' => $currentYear,
                    'updated_at' => now()
                ]);

            $settings->current_number = 0;
            $settings->year = $currentYear;
        }

        $newNumber = $settings->current_number + 1;

        DB::table('barcode_settings')
            ->where('type', $type)
            ->update([
                'current_number' => $newNumber,
                'updated_at' => now()
            ]);

        $paddedNumber = str_pad($newNumber, $settings->padding, '0', STR_PAD_LEFT);
        $barcode = str_replace(
            ['{prefix}', '{year}', '{number}'],
            [$settings->prefix, $currentYear, $paddedNumber],
            $settings->format
        );

        return $barcode;
    }

    /**
     * عرض تفاصيل كرتون
     */
    public function show($id)
    {
        $box = Stage4Box::with('creator')->findOrFail($id);

        if (!$box) {
            abort(404, 'الكرتون غير موجود');
        }

        // الحصول على مواصفات المنتج من stage3_coils
        $materials = DB::table('stage3_coils')
            ->leftJoin('materials', 'stage3_coils.material_id', '=', 'materials.id')
            ->where('stage3_coils.barcode', $box->parent_barcode)
            ->select('materials.color', 'materials.material_type', 'stage3_coils.wire_size')
            ->get();

        // جلب سجل العمليات من operation_logs
        $operationLogs = DB::table('operation_logs')
            ->leftJoin('users', 'operation_logs.user_id', '=', 'users.id')
            ->where(function($query) use ($id, $box) {
                $query->where('operation_logs.table_name', 'stage4_boxes')
                      ->where('operation_logs.record_id', $id);
            })
            ->orWhere('operation_logs.description', 'LIKE', '%' . $box->barcode . '%')
            ->select(
                'operation_logs.*',
                'users.name as user_name'
            )
            ->orderBy('operation_logs.created_at', 'desc')
            ->limit(50)
            ->get();

        // جلب سجل تتبع المنتج من product_tracking
        $trackingLogs = DB::table('product_tracking')
            ->leftJoin('users as worker', 'product_tracking.worker_id', '=', 'worker.id')
            ->where('product_tracking.barcode', $box->barcode)
            ->orWhere('product_tracking.input_barcode', $box->parent_barcode)
            ->orWhere('product_tracking.output_barcode', $box->barcode)
            ->select(
                'product_tracking.*',
                'worker.name as worker_name'
            )
            ->orderBy('product_tracking.created_at', 'desc')
            ->get();

        // جلب سجل الاستخدام
        $usageHistory = DB::table('stand_usage_history')
            ->leftJoin('users', 'stand_usage_history.user_id', '=', 'users.id')
            ->where('stand_usage_history.material_barcode', $box->parent_barcode)
            ->select(
                'stand_usage_history.*',
                'users.name as user_name'
            )
            ->orderBy('stand_usage_history.created_at', 'desc')
            ->first();

        return view('manufacturing::stages.stage4.show', compact('box', 'materials', 'operationLogs', 'trackingLogs', 'usageHistory'));
    }

    /**
     * خصم كراتين من المستودع
     */
    private function deductCartonFromWarehouse($cartonMaterialId, $quantity)
    {
        \Log::info("deductCartonFromWarehouse called", [
            'material_id' => $cartonMaterialId,
            'quantity' => $quantity
        ]);

        // البحث عن أقدم سجل متاح في material_details
        $materialDetail = DB::table('material_details')
            ->where('material_id', $cartonMaterialId)
            ->where('quantity', '>', 0)
            ->orderBy('created_at', 'asc')
            ->first();

        \Log::info("Material detail search result", [
            'found' => $materialDetail ? 'yes' : 'no',
            'detail' => $materialDetail
        ]);

        if (!$materialDetail) {
            throw new \Exception('لا توجد تفاصيل متاحة للكرتون في المستودع');
        }

        $remainingToDeduct = $quantity;

        // خصم من السجل الحالي
        if ($materialDetail->quantity >= $remainingToDeduct) {
            // الكمية كافية في هذا السجل
            $newQuantity = $materialDetail->quantity - $remainingToDeduct;
            
            DB::table('material_details')
                ->where('id', $materialDetail->id)
                ->update([
                    'quantity' => $newQuantity,
                    'updated_at' => now()
                ]);

            // تسجيل الحركة
            $movementNumber = 'MOV-' . date('Ymd') . '-' . str_pad(DB::table('material_movements')->count() + 1, 6, '0', STR_PAD_LEFT);
            
            DB::table('material_movements')->insert([
                'movement_number' => $movementNumber,
                'movement_type' => 'to_production',
                'source' => 'production',
                'material_id' => $cartonMaterialId,
                'material_detail_id' => $materialDetail->id,
                'unit_id' => $materialDetail->unit_id ?? null,
                'quantity' => $remainingToDeduct,
                'to_warehouse_id' => $materialDetail->warehouse_id ?? null,
                'description' => 'خصم كرتون للمرحلة الرابعة - التعبئة',
                'notes' => 'خصم تلقائي من المستودع',
                'created_by' => auth()->id() ?? 1,
                'movement_date' => now(),
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $remainingToDeduct = 0;
        } else {
            // الكمية غير كافية، نحتاج سجلات إضافية
            $deducted = $materialDetail->quantity;
            
            DB::table('material_details')
                ->where('id', $materialDetail->id)
                ->update([
                    'quantity' => 0,
                    'updated_at' => now()
                ]);

            // تسجيل الحركة
            $movementNumber = 'MOV-' . date('Ymd') . '-' . str_pad(DB::table('material_movements')->count() + 1, 6, '0', STR_PAD_LEFT);
            
            DB::table('material_movements')->insert([
                'movement_number' => $movementNumber,
                'movement_type' => 'to_production',
                'source' => 'production',
                'material_id' => $cartonMaterialId,
                'material_detail_id' => $materialDetail->id,
                'unit_id' => $materialDetail->unit_id ?? null,
                'quantity' => $deducted,
                'to_warehouse_id' => $materialDetail->warehouse_id ?? null,
                'description' => 'خصم كرتون للمرحلة الرابعة - التعبئة (جزئي)',
                'notes' => 'خصم تلقائي من المستودع - جزء من كمية أكبر',
                'created_by' => auth()->id() ?? 1,
                'movement_date' => now(),
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $remainingToDeduct -= $deducted;

            // استدعاء ذاتي للخصم من السجل التالي
            if ($remainingToDeduct > 0) {
                $this->deductCartonFromWarehouse($cartonMaterialId, $remainingToDeduct);
            }
        }
    }
}
