<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Stage3Coil;

class Stage3Controller extends Controller
{
    /**
     * عرض قائمة جميع اللفائف
     * Worker sees only their operations
     * Admin/Supervisor sees all operations
     */
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Query base
        $query = DB::table('stage3_coils')
            ->leftJoin('stage2_processed', 'stage3_coils.stage2_id', '=', 'stage2_processed.id')
            ->leftJoin('stage1_stands', 'stage3_coils.stage1_id', '=', 'stage1_stands.id')
            ->leftJoin('materials', 'stage3_coils.material_id', '=', 'materials.id')
            ->leftJoin('wrappings', 'stage3_coils.wrapping_id', '=', 'wrappings.id')
            ->leftJoin('users', 'stage3_coils.created_by', '=', 'users.id')
            ->select(
                'stage3_coils.*',
                'stage2_processed.barcode as stage2_barcode',
                'stage1_stands.barcode as stage1_barcode',
                'stage1_stands.stand_number',
                'materials.name_ar as material_name',
                'wrappings.wrapping_number',
                'wrappings.weight as wrapping_weight_db',
                'users.name as created_by_name'
            );

        // إذا لم يكن لديه صلاحية رؤية جميع العمليات، يعرض فقط عملياته
        $viewingAll = $user->hasPermission('VIEW_ALL_STAGE3_OPERATIONS');
        
        if (!$viewingAll) {
            $query->where('stage3_coils.created_by', $user->id);
        }

        $lafafs = $query->orderBy('stage3_coils.created_at', 'desc')
            ->paginate(20);

        return view('manufacturing::stages.stage3.index', compact('lafafs', 'viewingAll'));
    }

    /**
     * عرض صفحة إنشاء لفاف جديد (المرحلة الثالثة)
     */
    public function create()
    {
        // جلب قائمة الألوان (الصبغات) من المواد
        // نعتمد على type_code = 'DYE' لضمان عدم ظهور مواد أخرى مثل البلاستيك حتى لو حدثت أخطاء في الأسماء
        $colors = DB::table('materials')
            ->join('material_types', 'materials.material_type_id', '=', 'material_types.id')
            ->where('material_types.type_code', 'DYE')
            ->where('materials.status', 'available')
            ->select('materials.id', 'materials.name_ar', 'materials.barcode')
            ->orderBy('materials.name_ar')
            ->get();

        // جلب البلاستيك من المستودع
        $plastic = DB::table('materials')
            ->join('material_types', 'materials.material_type_id', '=', 'material_types.id')
            ->where('material_types.type_code', 'PLASTIC')
            ->where('materials.status', 'available')
            ->select('materials.id', 'materials.name_ar', 'materials.barcode')
            ->first();

        // جلب اللفافات النشطة
        $wrappings = \App\Models\Wrapping::active()->orderBy('wrapping_number')->get();

        return view('manufacturing::stages.stage3.create', compact('colors', 'plastic', 'wrappings'));
    }

    /**
     * الحصول على بيانات المرحلة الثانية بواسطة الباركود
     */
    /**
     * Get material by barcode - Supports TWO sources:
     * 1. Stage 2 barcode (ST2-XXX) from stage2_processed table
     * 2. Warehouse direct transfer for Stage 3 (confirmed barcodes)
     */
    public function getByBarcode($barcode)
    {
        // المصدر الأول: باركود المرحلة الثانية (ST2-XXX)
        $stage2 = DB::table('stage2_processed')
            ->leftJoin('stage1_stands', 'stage2_processed.stage1_id', '=', 'stage1_stands.id')
            ->leftJoin('materials', 'stage2_processed.material_id', '=', 'materials.id')
            ->where('stage2_processed.barcode', $barcode)
            ->select(
                'stage2_processed.*',
                'stage1_stands.barcode as stage1_barcode',
                'stage1_stands.stand_number',
                'materials.name_ar as material_name',
                'materials.name_en as material_name_en'
            )
            ->first();

        if ($stage2) {
            // 🔒 التحقق من حالة pending_approval
            if ($stage2->status === 'pending_approval') {
                return response()->json([
                    'success' => false,
                    'blocked' => true,
                    'message' => '⛔ هذا السجل في انتظار الموافقة ولا يمكن استخدامه في المرحلة الثالثة'
                ], 403);
            }

            // ✅ التحقق من عدم وجود confirmation معلقة لهذا الباركود (معاد إسناده)
            $pendingConfirmation = \App\Models\ProductionConfirmation::where('barcode', $stage2->barcode)
                ->where('status', 'pending')
                ->first();

            if ($pendingConfirmation) {
                return response()->json([
                    'success' => false,
                    'blocked' => true,
                    'message' => '⛔ هذا الباركود معاد إسناده ويحتاج موافقة من العامل المسند إليه أولاً'
                ], 403);
            }
            
            // التحقق من أن المرحلة الثانية في حالة نشطة
            if ($stage2->status !== 'in_progress' && $stage2->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'حالة المرحلة الثانية غير صالحة للمعالجة'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'source' => 'stage2',
                'data' => $stage2
            ]);
        }

        // المصدر الثاني: باركود من المخزن مباشرة للمرحلة الثالثة
        $confirmation = DB::table('production_confirmations')
            ->join('delivery_notes', 'production_confirmations.delivery_note_id', '=', 'delivery_notes.id')
            ->join('material_batches', 'production_confirmations.batch_id', '=', 'material_batches.id')
            ->join('materials', 'material_batches.material_id', '=', 'materials.id')
            ->where('delivery_notes.production_barcode', $barcode)
            ->where('production_confirmations.stage_code', 'stage_3')
            ->where('production_confirmations.status', 'confirmed')
            ->select(
                'production_confirmations.id',
                'delivery_notes.production_barcode as barcode',
                'material_batches.material_id',
                DB::raw('COALESCE(production_confirmations.actual_received_quantity, delivery_notes.quantity, 0) as remaining_weight'),
                'material_batches.unit_id',
                'materials.name_ar as material_name',
                'materials.name_en as material_name_en',
                'delivery_notes.id as delivery_note_id'
            )
            ->first();

        if ($confirmation) {
            return response()->json([
                'success' => true,
                'source' => 'warehouse_direct',
                'data' => $confirmation
            ]);
        }

        // لم يتم العثور على الباركود في أي من المصدرين
        return response()->json([
            'success' => false,
            'message' => 'لم يتم العثور على الباركود في سجلات المرحلة الثانية أو التحويلات المباشرة من المخزن'
        ], 404);
    }

    /**
     * حفظ لفاف واحد فوراً (instant save)
     */
    public function storeSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stage2_barcode' => 'required|string',
            'stage2_id' => 'nullable|integer',
            'source' => 'nullable|string',
            'material_id' => 'nullable|integer',
            'input_weight' => 'nullable|numeric|min:0',
            'total_weight' => 'required|numeric|min:0.001',
            'wrapping_id' => 'nullable|exists:wrappings,id',
            'wrapping_weight' => 'nullable|numeric|min:0',
            'color' => 'required|string|max:100',
            'plastic_type' => 'nullable|string|max:100',
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
            // التحقق من أن الباركود لم يُستخدم من قبل
            $barcodeExists = DB::table('stage3_coils')
                ->where('parent_barcode', $request->stage2_barcode)
                ->exists();

            if ($barcodeExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا الباركود تم استخدامه مسبقاً في المرحلة الثالثة'
                ], 422);
            }

            DB::beginTransaction();

            $source = $request->source ?? 'stage2';
            \Log::info('Stage3 storeSingle incoming', [
                'stage2_barcode' => $request->stage2_barcode,
                'total_weight' => $request->total_weight,
                'wrapping_id' => $request->wrapping_id,
                'wrapping_weight' => $request->wrapping_weight,
                'input_weight' => $request->input_weight,
            ]);
            
            // جلب البيانات حسب المصدر
            if ($source === 'warehouse_direct') {
                // المصدر من المخزن مباشرة
                $inputWeight = $request->input_weight ?? 0;
                $materialId = $request->material_id ?? null;
                $stage2Id = null;
                $stage1Id = null;
                $wireSize = 0;
            } else {
                // المصدر من المرحلة الثانية
                $stage2 = DB::table('stage2_processed')
                    ->where('id', $request->stage2_id)
                    ->first();

                if (!$stage2) {
                    throw new \Exception('باركود المرحلة الثانية غير موجود');
                }

                // ✅ التحقق من عدم وجود confirmation معلقة لهذا الباركود (معاد إسناده)
                $pendingConfirmation = \App\Models\ProductionConfirmation::where('barcode', $stage2->barcode)
                    ->where('status', 'pending')
                    ->first();

                if ($pendingConfirmation) {
                    throw new \Exception('⛔ هذا الباركود معاد إسناده ويحتاج موافقة من العامل المسند إليه أولاً');
                }

                $inputWeight = $stage2->remaining_weight ?? $stage2->output_weight;
                $materialId = $stage2->material_id;
                $stage2Id = $stage2->id;
                $stage1Id = $stage2->stage1_id ?? null;
                $wireSize = $stage2->wire_size ?? 0;
            }

            $totalWeight = $request->total_weight;
            $wrappingWeight = $request->wrapping_weight ?? 0;
            
            // حساب الوزن الصافي (بعد خصم وزن اللفاف)
            $netWeight = $totalWeight - $wrappingWeight;
            \Log::info('Stage3 storeSingle weights calc', [
                'total' => $totalWeight,
                'wrapping' => $wrappingWeight,
                'net' => $netWeight,
                'input' => $inputWeight,
            ]);

            if ($netWeight <= $inputWeight) {
                throw new \Exception('الوزن الصافي يجب أن يكون أكبر من وزن الدخول');
            }

            // البلاستيك المضاف = الوزن الصافي - وزن الدخول
            $addedWeight = $netWeight - $inputWeight;
            $plasticWeight = $addedWeight;
            $dyeWeight = 0; // الصبغة لا يتم خصمها

            // 🔍 التحقق من كمية البلاستيك في المستودع
            $plastic = DB::table('materials')
                ->join('material_types', 'materials.material_type_id', '=', 'material_types.id')
                ->where('material_types.type_code', 'PLASTIC')
                ->where('materials.status', 'available')
                ->select('materials.id', 'materials.name_ar')
                ->selectRaw('COALESCE((SELECT SUM(quantity) FROM material_details WHERE material_id = materials.id AND quantity > 0), 0) as available_quantity')
                ->first();

            if (!$plastic) {
                throw new \Exception('❌ لا يوجد بلاستيك متاح في المستودع');
            }

            if ($plastic->available_quantity < $plasticWeight) {
                throw new \Exception(sprintf(
                    '❌ كمية البلاستيك المطلوبة (%.3f كجم) أكبر من الكمية المتاحة في المستودع (%.3f كجم)',
                    $plasticWeight,
                    $plastic->available_quantity
                ));
            }

            $barcode = $this->generateStageBarcode('stage3');
            $lafafCount = DB::table('stage3_coils')->count() + 1;

            $lafafId = DB::table('stage3_coils')->insertGetId([
                'barcode' => $barcode,
                'parent_barcode' => $request->stage2_barcode,
                'stage2_id' => $stage2Id,
                'material_id' => $materialId,
                'stage1_id' => $stage1Id,
                'coil_number' => 'LF-' . date('Ymd') . '-' . str_pad($lafafCount, 4, '0', STR_PAD_LEFT),
                'wire_size' => $wireSize,
                'input_weight' => $inputWeight,
                'base_weight' => $inputWeight,
                'total_weight' => $totalWeight,
                'net_weight' => $netWeight,
                'wrapping_id' => $request->wrapping_id,
                'wrapping_weight' => $wrappingWeight,
                'dye_weight' => $dyeWeight,
                'plastic_weight' => $plasticWeight,
                'color' => $request->color,
                'dye_type' => $request->dye_type ?? null,
                'plastic_type' => $request->plastic_type,
                'waste' => 0,
                'status' => 'completed',
                'notes' => $request->notes,
                'created_by' => auth()->id() ?? 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 📦 خصم كمية البلاستيك من المستودع
            $this->deductPlasticFromWarehouse($plastic->id, $plasticWeight);

            // تحديث حالة المرحلة الثانية (فقط إذا كان المصدر stage2)
            if ($stage2Id) {
                DB::table('stage2_processed')
                    ->where('id', $stage2Id)
                    ->update([
                        'status' => 'completed',
                        'updated_at' => now()
                    ]);
                
                // 🔥 إنهاء سجل العامل في المرحلة الثانية
                \App\Models\WorkerStageHistory::where('stage_type', \App\Models\WorkerStageHistory::STAGE_2_PROCESSED)
                    ->where('stage_record_id', $stage2Id)
                    ->where('is_active', true)
                    ->update([
                        'is_active' => false,
                        'ended_at' => now(),
                        'duration_minutes' => DB::raw('TIMESTAMPDIFF(MINUTE, started_at, NOW())'),
                        'status_after' => 'completed',
                    ]);
            }

            DB::table('product_tracking')->insert([
                'barcode' => $barcode,
                'stage' => 'stage3',
                'action' => 'processed',
                'input_barcode' => $request->stage2_barcode,
                'output_barcode' => $barcode,
                'input_weight' => $inputWeight,
                'output_weight' => $totalWeight,
                'waste_amount' => 0,
                'waste_percentage' => 0,
                'worker_id' => auth()->id() ?? 1,
                'shift_id' => null,
                'notes' => $request->notes,
                'metadata' => json_encode([
                    'source' => $source,
                    'stage2_id' => $stage2Id,
                    'stage2_barcode' => $request->stage2_barcode,
                    'stage1_id' => $stage1Id,
                    'material_id' => $materialId,
                    'wire_size' => $wireSize,
                    'added_weight' => $addedWeight,
                    'color' => $request->color,
                    'plastic_type' => $request->plastic_type
                ]),
                'created_at' => now()
            ]);

            // 🔥 تسجيل العامل في نظام تتبع العمال
            try {
                $trackingService = app(\App\Services\WorkerTrackingService::class);
                $trackingService->assignWorkerToStage(
                    stageType: \App\Models\WorkerStageHistory::STAGE_3_COILS,
                    stageRecordId: $lafafId,
                    workerId: auth()->id() ?? 1,
                    barcode: $barcode,
                    statusBefore: $recordStatus ?? 'active',
                    assignedBy: auth()->id() ?? 1
                );
            } catch (\Exception $e) {
                \Log::error('Failed to register worker tracking for Stage3', [
                    'error' => $e->getMessage(),
                    'lafaf_id' => $lafafId,
                    'worker_id' => auth()->id(),
                ]);
            }

            DB::commit();

            $materialName = 'غير محدد';
            if ($materialId) {
                $material = DB::table('materials')->where('id', $materialId)->first();
                $materialName = $material->name_ar ?? 'غير محدد';
            }

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ اللفاف بنجاح',
                'data' => [
                    'lafaf_id' => $lafafId,
                    'barcode' => $barcode,
                    'coil_number' => 'LF-' . date('Ymd') . '-' . str_pad($lafafCount, 4, '0', STR_PAD_LEFT),
                    'material_name' => $materialName,
                    'total_weight' => $totalWeight,
                    'net_weight' => $netWeight,
                    'wrapping_weight' => $wrappingWeight,
                    'input_weight' => $inputWeight,
                    'added_weight' => $addedWeight,
                    'color' => $request->color,
                    'plastic_type' => $request->plastic_type ?? 'غير محدد',
                    'source' => $source
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
     * حفظ بيانات اللفاف (المرحلة الثالثة)
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'stage2_barcode' => 'required|string',
            'total_weight' => 'required|numeric|min:0.001',
            'color' => 'nullable|string|max:100',
            'plastic_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
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
            // التحقق من أن الباركود لم يُستخدم من قبل
            $barcodeExists = DB::table('stage3_coils')
                ->where('parent_barcode', $request->stage2_barcode)
                ->exists();

            if ($barcodeExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا الباركود تم استخدامه مسبقاً في المرحلة الثالثة'
                ], 422);
            }

            DB::beginTransaction();

            // الحصول على بيانات المرحلة الثانية
            $stage2 = DB::table('stage2_processed')
                ->where('barcode', $request->stage2_barcode)
                ->first();

            if (!$stage2) {
                throw new \Exception('باركود المرحلة الثانية غير موجود');
            }

            // ✅ التحقق من عدم وجود confirmation معلقة لهذا الباركود (معاد إسناده)
            $pendingConfirmation = \App\Models\ProductionConfirmation::where('barcode', $stage2->barcode)
                ->where('status', 'pending')
                ->first();

            if ($pendingConfirmation) {
                throw new \Exception('⛔ هذا الباركود معاد إسناده ويحتاج موافقة من العامل المسند إليه أولاً');
            }

            // التحقق من أن الوزن الكامل أكبر من وزن الدخول
            $inputWeight = $stage2->remaining_weight ?? $stage2->output_weight;
            $totalWeight = $request->total_weight;

            if ($totalWeight <= $inputWeight) {
                throw new \Exception('الوزن الكامل يجب أن يكون أكبر من وزن الدخول (الوزن يزيد في هذه المرحلة)');
            }

            // حساب الوزن المضاف (الصبغة + البلاستيك)
            $addedWeight = $totalWeight - $inputWeight;

            // توليد باركود للفاف
            $barcode = $this->generateStageBarcode('stage3');

            // إدراج بيانات اللفاف
            $lafafId = DB::table('stage3_coils')->insertGetId([
                'barcode' => $barcode,
                'parent_barcode' => $request->stage2_barcode,
                'stage2_id' => $stage2->id,
                'material_id' => $stage2->material_id,
                'stage1_id' => $stage2->stage1_id,
                'coil_number' => 'LF-' . date('Ymd') . '-' . str_pad($lafafId ?? 1, 4, '0', STR_PAD_LEFT),
                'wire_size' => $stage2->wire_size,
                'input_weight' => $inputWeight,
                'base_weight' => $inputWeight,
                'total_weight' => $totalWeight,
                'dye_weight' => $addedWeight * 0.3, // تقدير: 30% صبغة
                'plastic_weight' => $addedWeight * 0.7, // تقدير: 70% بلاستيك
                'color' => $request->color,
                'dye_type' => $request->dye_type,
                'plastic_type' => $request->plastic_type,
                'waste' => 0, // لا يوجد هدر في المرحلة الثالثة
                'status' => 'completed',
                'notes' => $request->notes,
                'created_by' => auth()->id() ?? 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // تحديث حالة المرحلة الثانية
            DB::table('stage2_processed')
                ->where('id', $stage2->id)
                ->update([
                    'status' => 'completed',
                    'updated_at' => now()
                ]);

            // إدراج سجل في product_tracking
            DB::table('product_tracking')->insert([
                'barcode' => $barcode,
                'stage' => 'stage3',
                'action' => 'processed',
                'input_barcode' => $request->stage2_barcode,
                'output_barcode' => $barcode,
                'input_weight' => $inputWeight,
                'output_weight' => $totalWeight,
                'waste_amount' => 0,
                'waste_percentage' => 0,
                'worker_id' => $request->worker_id,
                'shift_id' => $request->shift_id,
                'notes' => $request->notes,
                'metadata' => json_encode([
                    'stage2_id' => $stage2->id,
                    'stage2_barcode' => $request->stage2_barcode,
                    'stage1_id' => $stage2->stage1_id,
                    'material_id' => $stage2->material_id,
                    'wire_size' => $stage2->wire_size,
                    'added_weight' => $addedWeight,
                    'color' => $request->color,
                    'plastic_type' => $request->plastic_type
                ]),
                'created_at' => now()
            ]);

            // إدراج سجل في barcodes
            DB::table('barcodes')->insert([
                'barcode' => $barcode,
                'type' => 'stage3',
                'reference_id' => $lafafId,
                'reference_table' => 'stage3_coils',
                'status' => 'active',
                'scan_count' => 0,
                'metadata' => json_encode([
                    'total_weight' => $totalWeight,
                    'color' => $request->color
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            // الحصول على اسم المادة
            $materialName = 'غير محدد';
            if ($stage2->material_id) {
                $material = DB::table('materials')->where('id', $stage2->material_id)->first();
                $materialName = $material->name_ar ?? 'غير محدد';
            }

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ اللفاف بنجاح',
                'data' => [
                    'id' => $lafafId,
                    'barcode' => $barcode,
                    'input_weight' => $inputWeight,
                    'total_weight' => $totalWeight,
                    'added_weight' => $addedWeight,
                    'barcode_info' => [
                        'barcode' => $barcode,
                        'coil_number' => 'LF-' . date('Ymd') . '-' . str_pad($lafafId, 4, '0', STR_PAD_LEFT),
                        'material_name' => $materialName,
                        'total_weight' => $totalWeight,
                        'input_weight' => $inputWeight,
                        'added_weight' => $addedWeight,
                        'color' => $request->color ?? 'غير محدد',
                        'plastic_type' => $request->plastic_type ?? 'غير محدد',
                    ]
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
     * توليد باركود للمرحلة
     */
    private function generateStageBarcode($type)
    {
        // الحصول على إعدادات الباركود
        $settings = DB::table('barcode_settings')
            ->where('type', $type)
            ->first();

        if (!$settings) {
            // إنشاء إعدادات افتراضية إذا لم تكن موجودة
            DB::table('barcode_settings')->insert([
                'type' => $type,
                'prefix' => 'CO3',
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

        // التحقق من السنة
        $currentYear = date('Y');
        if ($settings->year != $currentYear) {
            // إعادة تعيين الرقم للسنة الجديدة
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

        // زيادة الرقم الحالي
        $newNumber = $settings->current_number + 1;

        // تحديث الرقم في قاعدة البيانات
        DB::table('barcode_settings')
            ->where('type', $type)
            ->update([
                'current_number' => $newNumber,
                'updated_at' => now()
            ]);

        // توليد الباركود
        $paddedNumber = str_pad($newNumber, $settings->padding, '0', STR_PAD_LEFT);
        $barcode = str_replace(
            ['{prefix}', '{year}', '{number}'],
            [$settings->prefix, $currentYear, $paddedNumber],
            $settings->format
        );

        return $barcode;
    }

    /**
     * عرض تفاصيل لفاف
     */
    public function show($id)
    {
        $coil = Stage3Coil::with('creator')->findOrFail($id);

        if (!$coil) {
            abort(404, 'اللفاف غير موجود');
        }

        // جلب سجل العمليات من operation_logs
        $operationLogs = DB::table('operation_logs')
            ->leftJoin('users', 'operation_logs.user_id', '=', 'users.id')
            ->where(function($query) use ($id, $coil) {
                $query->where('operation_logs.table_name', 'stage3_coils')
                      ->where('operation_logs.record_id', $id);
            })
            ->orWhere('operation_logs.description', 'LIKE', '%' . $coil->barcode . '%')
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
            ->where('product_tracking.barcode', $coil->barcode)
            ->orWhere('product_tracking.input_barcode', $coil->parent_barcode)
            ->orWhere('product_tracking.output_barcode', $coil->barcode)
            ->select(
                'product_tracking.*',
                'worker.name as worker_name'
            )
            ->orderBy('product_tracking.created_at', 'desc')
            ->get();

        // جلب سجل الاستخدام
        $usageHistory = DB::table('stand_usage_history')
            ->leftJoin('users', 'stand_usage_history.user_id', '=', 'users.id')
            ->where('stand_usage_history.material_barcode', $coil->parent_barcode)
            ->select(
                'stand_usage_history.*',
                'users.name as user_name'
            )
            ->orderBy('stand_usage_history.created_at', 'desc')
            ->first();

        return view('manufacturing::stages.stage3.show', compact('coil', 'operationLogs', 'trackingLogs', 'usageHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('manufacturing::stages.stage3.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'coil_number' => 'required|string',
            'total_weight' => 'required|numeric',
            'color' => 'required|string',
            'status' => 'nullable|in:created,in_process,completed,packed',
        ]);

        // تحديث اللفاف
        return redirect()->route('manufacturing.stage3.index')
            ->with('success', 'تم تحديث اللفاف بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // حذف اللفاف
        return redirect()->route('manufacturing.stage3.index')
            ->with('success', 'تم حذف اللفاف بنجاح');
    }

    /**
     * خصم كمية البلاستيك من المستودع
     */
    private function deductPlasticFromWarehouse($plasticMaterialId, $quantity)
    {
        // البحث عن أقدم سجل متاح في material_details
        $materialDetail = DB::table('material_details')
            ->where('material_id', $plasticMaterialId)
            ->where('quantity', '>', 0)
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$materialDetail) {
            throw new \Exception('لا توجد تفاصيل متاحة للبلاستيك في المستودع');
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
                'material_id' => $plasticMaterialId,
                'material_detail_id' => $materialDetail->id,
                'unit_id' => $materialDetail->unit_id ?? null,
                'quantity' => $remainingToDeduct,
                'to_warehouse_id' => $materialDetail->warehouse_id ?? null,
                'description' => 'خصم بلاستيك للمرحلة الثالثة - اللفائف',
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
                'material_id' => $plasticMaterialId,
                'material_detail_id' => $materialDetail->id,
                'unit_id' => $materialDetail->unit_id ?? null,
                'quantity' => $deducted,
                'to_warehouse_id' => $materialDetail->warehouse_id ?? null,
                'description' => 'خصم بلاستيك للمرحلة الثالثة - اللفائف (جزئي)',
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
                $this->deductPlasticFromWarehouse($plasticMaterialId, $remainingToDeduct);
            }
        }
    }
}
