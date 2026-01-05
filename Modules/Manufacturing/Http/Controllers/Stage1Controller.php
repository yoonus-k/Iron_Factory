<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Stand;
use App\Models\StandUsageHistory;
use App\Models\StageSuspension;
use App\Services\WasteCheckService;
use App\Helpers\SystemSettingsHelper;

class Stage1Controller extends Controller
{
    /**
     * Display a listing of the resource.
     * Worker sees only their operations
     * Admin/Supervisor sees all operations
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // إذا لم يكن لديه صلاحية رؤية جميع العمليات، يعرض فقط عملياته
        $viewingAll = $user->hasPermission('VIEW_ALL_STAGE1_OPERATIONS');
        
        // للكويلات المعلقة: يمكن للمستخدم رؤية كويلاته أو لديه صلاحية الموافقة/عرض الكل
        $canViewAllPending = $viewingAll || $user->hasPermission('STAGE_SUSPENSION_APPROVE');

        $showPendingOnly = $request->status === 'pending';

        // عرض الكويلات المعلقة بشكل مجمّع
        if ($showPendingOnly) {
            // جلب الباركودات التي لها أي استاند غير مكتمل
            $pendingBarcodes = DB::table('stage1_stands')
                ->whereNotIn('status', ['completed', 'consumed'])
                ->when(!$canViewAllPending, fn($q) => $q->where('created_by', $user->id))
                ->distinct()
                ->pluck('parent_barcode');

            // جلب جميع الاستاندات لهذه الباركودات (مكتملة وغير مكتملة)
            // حساب الوزن المستخدم الكلي من جميع العمال
            $query = DB::table('stage1_stands')
                ->join('materials', 'stage1_stands.material_id', '=', 'materials.id')
                ->leftJoin('coil_transfers', 'stage1_stands.parent_barcode', '=', 'coil_transfers.production_barcode')
                ->whereIn('stage1_stands.parent_barcode', $pendingBarcodes)
                ->select(
                    'stage1_stands.parent_barcode',
                    'materials.name_ar as material_name',
                    DB::raw('COUNT(stage1_stands.id) as stands_count'),
                    DB::raw('SUM(stage1_stands.remaining_weight) as used_weight'),
                    DB::raw('COALESCE(MAX(coil_transfers.transfer_weight), SUM(stage1_stands.remaining_weight) + SUM(stage1_stands.waste)) as transfer_weight'),
                    DB::raw('MIN(stage1_stands.created_at) as created_at'),
                    DB::raw('MAX(stage1_stands.updated_at) as updated_at'),
                    DB::raw('GROUP_CONCAT(DISTINCT users.name ORDER BY users.name SEPARATOR ", ") as workers_names')
                )
                ->leftJoin('users', 'stage1_stands.created_by', '=', 'users.id')
                ->groupBy('stage1_stands.parent_barcode', 'materials.name_ar');

            if (!$canViewAllPending) {
                // فلترة الكويلات فقط، لكن حساب الوزن من جميع العمال
                $query->havingRaw('SUM(CASE WHEN stage1_stands.created_by = ? THEN 1 ELSE 0 END) > 0', [$user->id]);
            }

            $pendingCoils = $query->orderBy('updated_at', 'desc')
                ->paginate(20);

            // إضافة معلومات النقل لكل كويل
            foreach ($pendingCoils as $coil) {
                $transferInfo = DB::table('production_confirmations')
                    ->join('users', 'production_confirmations.assigned_to', '=', 'users.id')
                    ->where('production_confirmations.barcode', $coil->parent_barcode)
                    ->select('production_confirmations.status', 'users.name as recipient_name')
                    ->first();
                
                if ($transferInfo) {
                    $coil->transfer_status = $transferInfo->status;
                    $coil->transfer_recipient_name = $transferInfo->recipient_name;
                } else {
                    $coil->transfer_status = null;
                    $coil->transfer_recipient_name = null;
                }
            }

            return view('manufacturing::stages.stage1.index-pending', compact('pendingCoils', 'viewingAll', 'showPendingOnly', 'canViewAllPending'));
        }

        // العرض العادي للاستاندات
        $query = DB::table('stage1_stands')
            ->join('materials', 'stage1_stands.material_id', '=', 'materials.id')
            ->leftJoin('users', 'stage1_stands.created_by', '=', 'users.id')
            ->select(
                'stage1_stands.*',
                'materials.name_ar as material_name',
                'users.name as created_by_name'
            );

        if (!$viewingAll) {
            $query->where('stage1_stands.created_by', $user->id);
        }

        $stands = $query->orderBy('stage1_stands.created_at', 'desc')
            ->paginate(20);

        return view('manufacturing::stages.stage1.index', compact('stands', 'viewingAll', 'showPendingOnly'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturing::stages.stage1.create');
    }

    /**
     * Store a single stand immediately (instant save)
     */
    public function storeSingle(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|integer',
            'material_barcode' => 'required|string',
            'stand_id' => 'required|exists:stands,id',
            'wire_size' => 'nullable|numeric|min:0',
            'total_weight' => 'required|numeric|min:0',
            'net_weight' => 'nullable|numeric|min:0',
            'stand_weight' => 'nullable|numeric|min:0',
            'waste_weight' => 'nullable|numeric|min:0',
            'waste_percentage' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $userId = Auth::id();
            $materialId = $validated['material_id'];
            $barcode = $validated['material_barcode'];

            // التحقق من صلاحية استخدام الكويل
            // 1. هل الكويل تم نقله وفي انتظار الموافقة؟
            $pendingTransfer = DB::table('production_confirmations')
                ->where('barcode', $barcode)
                ->where('confirmation_type', 'coil_transfer')
                ->where('status', 'pending')
                ->first();

            if ($pendingTransfer) {
                // الكويل تم نقله - فقط المستلم يستطيع قبوله أولاً
                if ($pendingTransfer->assigned_to != $userId) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => '⚠️ هذا الكويل تم نقله لموظف آخر وفي انتظار موافقته.'
                    ], 422);
                }
                // المستلم يحاول استخدامه - يجب قبوله أولاً
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ يجب قبول نقل الكويل أولاً قبل البدء في العمل عليه.'
                ], 422);
            }

            // 2. هل هناك نقل مؤكد للكويل؟ (تم نقله لموظف آخر)
            $confirmedTransfer = DB::table('production_confirmations')
                ->where('barcode', $barcode)
                ->where('confirmation_type', 'coil_transfer')
                ->where('status', 'confirmed')
                ->orderBy('confirmed_at', 'desc')
                ->first();

            // 3. التحقق من من يملك الحق في استخدام الكويل
            $existingStands = DB::table('stage1_stands')
                ->where('parent_barcode', $barcode)
                ->exists();

            if ($existingStands) {
                // الكويل له استاندات سابقة
                if ($confirmedTransfer && $confirmedTransfer->assigned_to == $userId) {
                    // المستخدم الحالي هو المستلم المؤكد - يمكنه الاستمرار
                } else if (!$confirmedTransfer) {
                    // لا يوجد نقل - يجب أن يكون المستخدم هو المنشئ الأصلي
                    $isOriginalCreator = DB::table('stage1_stands')
                        ->where('parent_barcode', $barcode)
                        ->where('created_by', $userId)
                        ->exists();
                    
                    if (!$isOriginalCreator) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => '⚠️ ليس لديك صلاحية العمل على هذا الكويل.'
                        ], 422);
                    }
                } else {
                    // الكويل تم نقله لموظف آخر
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => '⚠️ هذا الكويل تم نقله لموظف آخر.'
                    ], 422);
                }
            }

            // منع بدء كويل جديد إذا كان لدى المستخدم كويلات معلقة أخرى
            // نتحقق من الكويلات التي أنشأها المستخدم أو تم نقلها إليه
            $hasOtherPendingCoils = DB::table('stage1_stands')
                ->where('created_by', $userId)
                ->where('parent_barcode', '!=', $barcode)
                ->whereNotIn('status', ['completed', 'consumed'])
                ->exists();
            
            // أيضاً نتحقق من الكويلات المنقولة إليه
            if (!$hasOtherPendingCoils) {
                $hasOtherPendingCoils = DB::table('production_confirmations')
                    ->where('assigned_to', $userId)
                    ->where('confirmation_type', 'coil_transfer')
                    ->where('status', 'confirmed')
                    ->where('barcode', '!=', $barcode)
                    ->whereExists(function($query) {
                        $query->select(DB::raw(1))
                            ->from('stage1_stands')
                            ->whereColumn('stage1_stands.parent_barcode', 'production_confirmations.barcode')
                            ->whereNotIn('stage1_stands.status', ['completed', 'consumed']);
                    })
                    ->exists();
            }

            if ($hasOtherPendingCoils) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => '⚠️ لديك كويلات معلقة لم يتم إنهاؤها بعد. يرجى إنهاء الكويل الحالي قبل البدء بآخر جديد.'
                ], 422);
            }

            // جلب بيانات الاستاند لحساب الوزن الصافي إذا لم يُرسل
            $stand = Stand::findOrFail($validated['stand_id']);

            // حساب stand_weight و net_weight إذا لم يتم إرسالهما (بسبب الصلاحيات)
            $standWeight = $validated['stand_weight'] ?? $stand->weight;
            $netWeight = $validated['net_weight'] ?? ($validated['total_weight'] - $standWeight);

            // البحث عن الباركود في جدول barcodes
            $barcodeRecord = DB::table('barcodes')
                ->where('barcode', $validated['material_barcode'])
                ->where('reference_table', 'material_batches')
                ->first();

            if (!$barcodeRecord) {
                $materialBatch = DB::table('material_batches')
                    ->join('materials', 'material_batches.material_id', '=', 'materials.id')
                    ->where('material_batches.batch_code', $validated['material_barcode'])
                    ->select('material_batches.*', 'materials.name_ar as material_name')
                    ->first();
            } else {
                $materialBatch = DB::table('material_batches')
                    ->join('materials', 'material_batches.material_id', '=', 'materials.id')
                    ->where('material_batches.id', $barcodeRecord->reference_id)
                    ->select('material_batches.*', 'materials.name_ar as material_name')
                    ->first();
            }

            if (!$materialBatch) {
                throw new \Exception('لم يتم العثور على المادة بهذا الباركود');
            }

            // البحث عن نقل الكويل بناءً على production_barcode
            $coilTransfer = DB::table('coil_transfers')
                ->where('production_barcode', $validated['material_barcode'])
                ->first();

            // حساب الكمية المنقولة للإنتاج
            if ($coilTransfer) {
                // إذا كان باركود كويل، نستخدم وزن النقل المحدد
                $transferredToProduction = $coilTransfer->transfer_weight;
            } else {
                // إذا كان باركود batch عادي، نبحث في material_movements
                $transferredToProduction = DB::table('material_movements')
                    ->where('batch_id', $materialBatch->id)
                    ->where('movement_type', 'to_production')
                    ->sum('quantity');
            }

            // حساب الكمية المستخدمة سابقاً (مجموع الأوزان الصافية للاستاندات)
            $usedInStage1 = DB::table('stage1_stands')
                ->where('parent_barcode', $validated['material_barcode'])
                ->sum('remaining_weight');

            $availableWeight = $transferredToProduction - $usedInStage1;

            // التحقق من أن الكويل لم يُستهلك بالكامل
            if ($availableWeight <= 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'coil_exhausted' => true,
                    'message' => '⚠️ تم استهلاك جميع وزن الكويل. الوزن المنقول: ' . number_format($transferredToProduction, 2) . ' كجم، المستخدم: ' . number_format($usedInStage1, 2) . ' كجم'
                ], 422);
            }

            if ($availableWeight < $netWeight) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "الكمية المتوفرة للإنتاج (" . number_format($availableWeight, 2) . " كجم) غير كافية للكمية المطلوبة (" . number_format($netWeight, 2) . " كجم)"
                ], 422);
            }

            // الحالة الافتراضية للاستاند هي 'created'
            // حساب الهدر الكلي سيتم عند إنهاء الكويل بالكامل (عبر دالة finishCoil)
            // لا نحفظ الهدر على مستوى الاستاند الفردي، بل على مستوى الكويل الكلي
            $recordStatus = 'created';
            $wasteWeight = 0;  // الهدر يُحسب على مستوى الكويل عند الإنهاء
            $wastePercentage = 0;

            // تحديث حالة الاستاند
            $stand->update([
                'status' => 'stage1',
                'usage_count' => $stand->usage_count + 1,
            ]);

            // تسجيل في stand_usage_history
            $usageHistory = StandUsageHistory::create([
                'stand_id' => $stand->id,
                'user_id' => $userId,
                'material_id' => $materialId,
                'material_barcode' => $validated['material_barcode'],
                'material_type' => $materialBatch->material_name ?? 'غير محدد',
                'wire_size' => $validated['wire_size'] ?? 0,
                'total_weight' => $validated['total_weight'],
                'net_weight' => $netWeight,
                'stand_weight' => $standWeight,
                'waste_percentage' => $validated['waste_percentage'] ?? 0,
                'cost' => $validated['cost'] ?? 0,
                'notes' => $validated['notes'],
                'status' => StandUsageHistory::STATUS_IN_USE,
                'started_at' => now(),
            ]);

            // حفظ في جدول stage1_stands
            $stage1Barcode = $this->generateStageBarcode('stage1');

            $stage1StandId = DB::table('stage1_stands')->insertGetId([
                'barcode' => $stage1Barcode,
                'parent_barcode' => $validated['material_barcode'],
                'material_id' => $materialId,
                'stand_number' => $stand->stand_number,
                'wire_size' => $validated['wire_size'] ?? '0',
                'weight' => $validated['total_weight'],
                'waste' => 0,  // الهدر يُحسب على مستوى الكويل عند الإنهاء، ليس على مستوى الاستاند
                'remaining_weight' => $netWeight,
                'status' => $recordStatus,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // تسجيل التتبع
            DB::table('product_tracking')->insert([
                'barcode' => $stage1Barcode,
                'stage' => 'stage1',
                'action' => 'created',
                'input_barcode' => $validated['material_barcode'],
                'output_barcode' => $stage1Barcode,
                'input_weight' => $validated['total_weight'],
                'output_weight' => $netWeight,
                'waste_amount' => $wasteWeight,
                'waste_percentage' => $wastePercentage,
                'worker_id' => $userId,
                'shift_id' => null,
                'notes' => $validated['notes'],
                'metadata' => json_encode([
                    'stand_id' => $stand->id,
                    'stand_number' => $stand->stand_number,
                    'material_id' => $materialId,
                    'batch_id' => $materialBatch->id,
                    'batch_code' => $materialBatch->batch_code,
                    'wire_size' => $validated['wire_size'] ?? 0,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 🔥 تسجيل العامل في نظام تتبع العمال
            try {
                $trackingService = app(\App\Services\WorkerTrackingService::class);
                $trackingService->assignWorkerToStage(
                    stageType: \App\Models\WorkerStageHistory::STAGE_1_STANDS,
                    stageRecordId: $stage1StandId,
                    workerId: $userId,
                    barcode: $stage1Barcode,
                    statusBefore: $recordStatus,
                    assignedBy: $userId
                );
            } catch (\Exception $e) {
                \Log::error('Failed to register worker tracking for Stage1', [
                    'error' => $e->getMessage(),
                    'stage1_id' => $stage1StandId,
                    'worker_id' => $userId,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الاستاند بنجاح!',
                'data' => [
                    'stand_id' => $stage1StandId,
                    'stand_number' => $stand->stand_number,
                    'barcode' => $stage1Barcode,
                    'net_weight' => $netWeight,
                    'material_name' => $materialBatch->material_name ?? 'غير محدد',
                    'usage_history_id' => $usageHistory->id,
                    'available_weight' => $availableWeight - $netWeight,
                    'waste_weight' => $wasteWeight,
                    'waste_percentage' => $wastePercentage,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|integer',
            'material_barcode' => 'required|string',
            'processed_stands' => 'required|array|min:1',
            'processed_stands.*.stand_id' => 'required|exists:stands,id',
            'processed_stands.*.wire_size' => 'nullable|numeric|min:0',
            'processed_stands.*.total_weight' => 'required|numeric|min:0',
            'processed_stands.*.net_weight' => 'required|numeric|min:0',
            'processed_stands.*.stand_weight' => 'required|numeric|min:0',
            'processed_stands.*.waste_weight' => 'nullable|numeric|min:0',
            'processed_stands.*.waste_percentage' => 'nullable|numeric|min:0',
            'processed_stands.*.cost' => 'nullable|numeric|min:0',
            'processed_stands.*.notes' => 'nullable|string|max:1000',
        ]);

        try {
            // التحقق من أن الباركود لم يُستخدم من قبل
            $barcodeExists = DB::table('stage1_stands')
                ->where('parent_barcode', $validated['material_barcode'])
                ->exists();

            if ($barcodeExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا الباركود تم استخدامه مسبقاً في المرحلة الأولى'
                ], 422);
            }

            DB::beginTransaction();

            $userId = Auth::id();
            $materialId = $validated['material_id'];

            // منع حفظ كويل جديد إذا كان هناك كويلات أخرى معلقة لنفس المستخدم
            $hasOtherPendingCoils = DB::table('stage1_stands')
                ->where('created_by', $userId)
                ->where('parent_barcode', '!=', $validated['material_barcode'])
                ->whereNotIn('status', ['completed', 'consumed'])
                ->exists();

            if ($hasOtherPendingCoils) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => '⚠️ لديك كويلات سابقة لم يتم إنهاؤها. أكمل الكويل المعلق قبل إضافة كويل جديد.'
                ], 422);
            }

            // البحث عن الباركود في جدول barcodes
            $barcodeRecord = DB::table('barcodes')
                ->where('barcode', $validated['material_barcode'])
                ->where('reference_table', 'material_batches')
                ->first();

            if (!$barcodeRecord) {
                // إذا لم يوجد في جدول barcodes، نبحث مباشرة في material_batches.batch_code
                $materialBatch = DB::table('material_batches')
                    ->join('materials', 'material_batches.material_id', '=', 'materials.id')
                    ->where('material_batches.batch_code', $validated['material_barcode'])
                    ->select('material_batches.*', 'materials.name_ar as material_name')
                    ->first();
            } else {
                // إذا وُجد في جدول barcodes، نجلب البيانات من material_batches باستخدام reference_id
                $materialBatch = DB::table('material_batches')
                    ->join('materials', 'material_batches.material_id', '=', 'materials.id')
                    ->where('material_batches.id', $barcodeRecord->reference_id)
                    ->select('material_batches.*', 'materials.name_ar as material_name')
                    ->first();
            }

            if (!$materialBatch) {
                throw new \Exception('لم يتم العثور على المادة بهذا الباركود');
            }

            // حساب إجمالي الوزن الصافي المطلوب (بدون وزن الاستاندات)
            $totalNetWeightNeeded = collect($validated['processed_stands'])->sum('net_weight');

            // حساب الكمية المنقولة للإنتاج
            $transferredToProduction = DB::table('material_movements')
                ->where('batch_id', $materialBatch->id)
                ->where('movement_type', 'to_production')
                ->sum('quantity');

            // حساب الكمية المستخدمة سابقاً في المرحلة الأولى (الوزن الصافي فقط)
            $usedInStage1 = DB::table('stage1_stands')
                ->where('parent_barcode', $validated['material_barcode'])
                ->sum('remaining_weight');

            // الكمية المتاحة للاستخدام = المنقولة للإنتاج - المستخدمة
            $availableWeight = $transferredToProduction - $usedInStage1;

            if ($availableWeight < $totalNetWeightNeeded) {
                throw new \Exception("الكمية المتوفرة للإنتاج ({$availableWeight} كجم) غير كافية للكمية المطلوبة ({$totalNetWeightNeeded} كجم)");
            }


            $processedRecords = [];

            foreach ($validated['processed_stands'] as $processedData) {
                // جلب بيانات الاستاند
                $stand = Stand::findOrFail($processedData['stand_id']);

                // الحالة الافتراضية للاستاند هي 'created'
                // حساب الهدر الكلي سيتم عند إنهاء الكويل بالكامل
                $recordStatus = 'created';

                // تحديث حالة الاستاند
                $stand->update([
                    'status' => 'stage1',
                    'usage_count' => $stand->usage_count + 1,
                ]);

                // تسجيل في stand_usage_history
                $usageHistory = StandUsageHistory::create([
                    'stand_id' => $stand->id,
                    'user_id' => $userId,
                    'material_id' => $materialId,
                    'material_barcode' => $validated['material_barcode'],
                    'material_type' => $materialBatch->material_name ?? 'غير محدد',
                    'wire_size' => $processedData['wire_size'] ?? 0,
                    'total_weight' => $processedData['total_weight'],
                    'net_weight' => $processedData['net_weight'],
                    'stand_weight' => $processedData['stand_weight'],
                    'waste_percentage' => $processedData['waste_percentage'] ?? 0,
                    'cost' => $processedData['cost'] ?? 0,
                    'notes' => $processedData['notes'],
                    'status' => StandUsageHistory::STATUS_IN_USE,
                    'started_at' => now(),
                ]);

                // حفظ في جدول stage1_stands
                $stage1Barcode = $this->generateStageBarcode('stage1');

                $stage1StandId = DB::table('stage1_stands')->insertGetId([
                    'barcode' => $stage1Barcode,
                    'parent_barcode' => $validated['material_barcode'],
                    'material_id' => $materialId,
                    'stand_number' => $stand->stand_number,
                    'wire_size' => $processedData['wire_size'] ?? '0',
                    'weight' => $processedData['total_weight'],
                    'waste' => $processedData['waste_weight'] ?? 0,
                    'remaining_weight' => $processedData['net_weight'],
                    'status' => $recordStatus,
                    'created_by' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // تسجيل التتبع في product_tracking
                DB::table('product_tracking')->insert([
                    'barcode' => $stage1Barcode,
                    'stage' => 'stage1',
                    'action' => 'created',
                    'input_barcode' => $validated['material_barcode'],
                    'output_barcode' => $stage1Barcode,
                    'input_weight' => $processedData['total_weight'],
                    'output_weight' => $processedData['net_weight'],
                    'waste_amount' => $processedData['waste_weight'] ?? 0,
                    'waste_percentage' => $processedData['waste_percentage'] ?? 0,
                    'worker_id' => $userId,
                    'shift_id' => null, // يمكن إضافة الوردية لاحقاً
                    'notes' => $processedData['notes'],
                    'metadata' => json_encode([
                        'stand_id' => $stand->id,
                        'stand_number' => $stand->stand_number,
                        'material_id' => $materialId,
                        'batch_id' => $materialBatch->id,
                        'batch_code' => $materialBatch->batch_code,
                        'wire_size' => $processedData['wire_size'] ?? 0,
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $processedRecords[] = [
                    'stand_number' => $stand->stand_number,
                    'net_weight' => $processedData['net_weight'],
                    'usage_history_id' => $usageHistory->id,
                    'stage1_stand_id' => $stage1StandId,
                    'stage1_barcode' => $stage1Barcode,
                    'status' => $recordStatus,
                    'waste_weight' => $processedData['waste_weight'] ?? 0,
                    'waste_percentage' => $processedData['waste_percentage'] ?? 0,
                ];
            }

            // ملاحظة: لا نقوم بتحديث available_quantity في material_batches
            // لأن الكمية المتوفرة تمثل ما هو موجود في المخزن فعلياً
            // نحن فقط نتتبع الاستخدام من الكمية المنقولة للإنتاج عبر جدول stage1_stands

            DB::commit();

            // تحضير قائمة الباركودات لعرضها
            $barcodesList = collect($processedRecords)->map(function($record) use ($materialBatch) {
                return [
                    'stand_number' => $record['stand_number'],
                    'barcode' => $record['stage1_barcode'],
                    'net_weight' => $record['net_weight'],
                    'material_name' => $materialBatch->material_name ?? 'غير محدد',
                    'status' => $record['status'] ?? 'created',
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ جميع الاستاندات بنجاح!',
                'data' => [
                    'processed_count' => count($processedRecords),
                    'total_weight_used' => $totalNetWeightNeeded,
                    'remaining_weight' => $availableWeight - $totalNetWeightNeeded,
                    'records' => $processedRecords,
                    'barcodes' => $barcodesList,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Generate barcode for stage based on barcode_settings
     */
    private function generateStageBarcode($stageType)
    {
        // جلب إعدادات الباركود للمرحلة
        $settings = DB::table('barcode_settings')
            ->where('type', $stageType)
            ->where('is_active', true)
            ->first();

        if (!$settings) {
            // إذا لم توجد إعدادات، استخدم نمط افتراضي
            $prefix = strtoupper($stageType);
            $number = DB::table('stage1_stands')->count() + 1;
            return "{$prefix}-" . date('Y') . "-" . str_pad($number, 3, '0', STR_PAD_LEFT);
        }

        // زيادة الرقم التسلسلي
        DB::table('barcode_settings')
            ->where('id', $settings->id)
            ->increment('current_number');

        // جلب الرقم الجديد
        $newNumber = $settings->current_number + 1;

        // تطبيق الـ padding
        $paddedNumber = str_pad($newNumber, $settings->padding, '0', STR_PAD_LEFT);

        // توليد الباركود وفقاً للصيغة
        $barcode = str_replace(
            ['{prefix}', '{year}', '{number}'],
            [$settings->prefix, $settings->year, $paddedNumber],
            $settings->format
        );

        return $barcode;
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        // جلب بيانات الاستاند مع العلاقات
        $stand = DB::table('stage1_stands')
            ->join('materials', 'stage1_stands.material_id', '=', 'materials.id')
            ->leftJoin('users as creator', 'stage1_stands.created_by', '=', 'creator.id')
            ->where('stage1_stands.id', $id)
            ->select(
                'stage1_stands.*',
                'materials.name_ar as material_name',
                'creator.name as created_by_name'
            )
            ->first();

        if (!$stand) {
            abort(404, 'الاستاند غير موجود');
        }

        // جلب سجل العمليات من operation_logs
        $operationLogs = DB::table('operation_logs')
            ->leftJoin('users', 'operation_logs.user_id', '=', 'users.id')
            ->where(function($query) use ($id, $stand) {
                $query->where('operation_logs.table_name', 'stage1_stands')
                      ->where('operation_logs.record_id', $id);
            })
            ->orWhere('operation_logs.description', 'LIKE', '%' . $stand->barcode . '%')
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
            ->where('product_tracking.barcode', $stand->barcode)
            ->orWhere('product_tracking.input_barcode', $stand->parent_barcode)
            ->orWhere('product_tracking.output_barcode', $stand->barcode)
            ->select(
                'product_tracking.*',
                'worker.name as worker_name'
            )
            ->orderBy('product_tracking.created_at', 'desc')
            ->get();

        // جلب سجل استخدام الاستاند من stand_usage_history
        $usageHistory = DB::table('stand_usage_history')
            ->leftJoin('users', 'stand_usage_history.user_id', '=', 'users.id')
            ->leftJoin('stands', 'stand_usage_history.stand_id', '=', 'stands.id')
            ->where('stand_usage_history.material_barcode', $stand->parent_barcode)
            ->where('stands.stand_number', $stand->stand_number)
            ->select(
                'stand_usage_history.*',
                'users.name as user_name',
                'stands.stand_number'
            )
            ->orderBy('stand_usage_history.created_at', 'desc')
            ->first();

        // جلب الوردية الحالية للمرحلة (بناءً على تاريخ اليوم)
        $currentShift = DB::table('shift_assignments')
            ->leftJoin('users', 'shift_assignments.user_id', '=', 'users.id')
            ->leftJoin('users as supervisors', 'shift_assignments.supervisor_id', '=', 'supervisors.id')
            ->where('shift_assignments.shift_date', '>=', $stand->created_at ? date('Y-m-d', strtotime($stand->created_at)) : date('Y-m-d'))
            ->where('shift_assignments.stage_number', 1)
            ->where('shift_assignments.status', 'active')
            ->select(
                'shift_assignments.*',
                'users.name as worker_name',
                'supervisors.name as supervisor_name'
            )
            ->orderBy('shift_assignments.shift_date', 'desc')
            ->first();

        // جلب العمال الحاليين مع المسؤول للوردية الحالية
        $currentShiftWorkers = null;
        $currentShiftSupervisor = null;
        if ($currentShift) {
            // جلب المسؤول للوردية الحالية
            $currentShiftSupervisor = DB::table('users')
                ->where('id', $currentShift->supervisor_id)
                ->first();

            // جلب العمال من shift_assignments أولاً (من worker_ids)
            $shiftAssignment = \App\Models\ShiftAssignment::find($currentShift->id);
            if ($shiftAssignment) {
                $currentShiftWorkers = $shiftAssignment->workers(); // يجلب من worker_ids
            } else {
                $currentShiftWorkers = collect();
            }

            // إذا لم نجد عمال من shift_assignments، جرب من worker_stage_history
            if (!$currentShiftWorkers || $currentShiftWorkers->count() == 0) {
                $currentShiftWorkers = DB::table('worker_stage_history')
                    ->leftJoin('users', 'worker_stage_history.worker_id', '=', 'users.id')
                    ->leftJoin('worker_teams', 'worker_stage_history.worker_team_id', '=', 'worker_teams.id')
                    ->where('worker_stage_history.stage_type', 'stage1_stands')
                    ->where('worker_stage_history.stage_record_id', $id)
                    ->where('worker_stage_history.shift_assignment_id', $currentShift->id)
                    ->select(
                        'worker_stage_history.id',
                        'worker_stage_history.worker_type',
                        'worker_stage_history.worker_id',
                        'worker_stage_history.worker_team_id',
                        'worker_stage_history.started_at',
                        'worker_stage_history.ended_at',
                        'worker_stage_history.duration_minutes',
                        'users.name as worker_name',
                        'worker_teams.name as team_name'
                    )
                    ->orderBy('worker_stage_history.started_at', 'desc')
                    ->get();
            }
        }

        // جلب الورديات السابقة للمرحلة
        $previousShifts = DB::table('shift_assignments')
            ->leftJoin('users', 'shift_assignments.user_id', '=', 'users.id')
            ->leftJoin('users as supervisors', 'shift_assignments.supervisor_id', '=', 'supervisors.id')
            ->where('shift_assignments.shift_date', '<=', $stand->created_at ? date('Y-m-d', strtotime($stand->created_at)) : date('Y-m-d'))
            ->where('shift_assignments.stage_number', 1)
            ->where('shift_assignments.status', '!=', 'active')
            ->select(
                'shift_assignments.*',
                'users.name as worker_name',
                'supervisors.name as supervisor_name'
            )
            ->orderBy('shift_assignments.shift_date', 'desc')
            ->limit(10)
            ->get();

        // جلب العمال والمسؤولين لكل وردية سابقة
        $previousShiftsData = [];
        foreach ($previousShifts as $shift) {
            // جلب العمال من shift_assignments
            $shiftAssignmentModel = \App\Models\ShiftAssignment::find($shift->id);
            $workers = [];

            if ($shiftAssignmentModel) {
                $workers = $shiftAssignmentModel->workers(); // يجلب من worker_ids
            }

            // إذا لم نجد عمال من worker_ids، جرب من worker_stage_history
            if (!$workers || $workers->count() == 0) {
                $workers = DB::table('worker_stage_history')
                    ->leftJoin('users', 'worker_stage_history.worker_id', '=', 'users.id')
                    ->leftJoin('worker_teams', 'worker_stage_history.worker_team_id', '=', 'worker_teams.id')
                    ->where('worker_stage_history.stage_type', 'stage1_stands')
                    ->where('worker_stage_history.stage_record_id', $id)
                    ->where('worker_stage_history.shift_assignment_id', $shift->id)
                    ->select(
                        'worker_stage_history.id',
                        'worker_stage_history.worker_type',
                        'worker_stage_history.worker_id',
                        'worker_stage_history.worker_team_id',
                        'worker_stage_history.started_at',
                        'worker_stage_history.ended_at',
                        'worker_stage_history.duration_minutes',
                        'users.name as worker_name',
                        'worker_teams.name as team_name'
                    )
                    ->orderBy('worker_stage_history.started_at', 'desc')
                    ->get();
            }

            $supervisor = DB::table('users')
                ->where('id', $shift->supervisor_id)
                ->first();

            $previousShiftsData[] = [
                'shift' => $shift,
                'workers' => $workers,
                'supervisor' => $supervisor
            ];
        }

        // جلب الورديات المتاحة للنقل إليها
        $availableShifts = DB::table('shift_assignments')
            ->leftJoin('users', 'shift_assignments.user_id', '=', 'users.id')
            ->leftJoin('users as supervisors', 'shift_assignments.supervisor_id', '=', 'supervisors.id')
            ->where('shift_assignments.stage_number', 1)
            ->where('shift_assignments.status', 'active')
            ->when($currentShift, function($query) use ($currentShift) {
                return $query->where('shift_assignments.id', '!=', $currentShift->id);
            })
            ->select(
                'shift_assignments.*',
                'users.name as worker_name',
                'supervisors.name as supervisor_name'
            )
            ->get();

        return view('manufacturing::stages.stage1.show', compact(
            'stand',
            'operationLogs',
            'trackingLogs',
            'usageHistory',
            'currentShift',
            'currentShiftWorkers',
            'currentShiftSupervisor',
            'previousShifts',
            'previousShiftsData',
            'availableShifts'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('manufacturing::stages.stage1.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'stand_number' => 'required|string',
            'wire_size' => 'required|numeric',
            'weight' => 'required|numeric',
            'waste_percentage' => 'nullable|numeric',
            'status' => 'nullable|in:created,in_process,completed,consumed',
            'notes' => 'nullable|string',
        ]);

        // $stand = Stage1Stand::find($id);
        // $stand->update($validated);

        return redirect()->route('manufacturing.stage1.index')
            ->with('success', 'تم تحديث الاستاند بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Stage1Stand::find($id)->delete();

        return redirect()->route('manufacturing.stage1.index')
            ->with('success', 'تم حذف الاستاند بنجاح');
    }

    /**
     * Get material by barcode
     */
    public function getMaterialByBarcode($barcode)
    {
        try {
            // 🔒 خطوة 1: التحقق من الموافقة على الباركود للمرحلة الأولى
            // البحث عن batch_code في material_batches
            $batch = DB::table('material_batches')
                ->where('batch_code', $barcode)
                ->first();
            
            if (!$batch) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ الباركود غير موجود في النظام'
                ], 404);
            }
            
            // البحث عن التأكيد المرتبط بهذا الـ batch
            $confirmation = DB::table('production_confirmations')
                ->where('batch_id', $batch->id)
                ->where('stage_code', 'stage_1')
                ->first();

            // التحقق من وجود الموافقة
            if (!$confirmation) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ هذا الباركود غير مسجل في نظام الموافقات للمرحلة الأولى'
                ], 404);
            }

            // التحقق من حالة الموافقة
            if ($confirmation->status === 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => '⏳ هذا الباركود في انتظار الموافقة. يجب على عامل المرحلة الأولى الموافقة عليه أولاً'
                ], 403);
            }

            if ($confirmation->status === 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ تم رفض هذا الباركود. السبب: ' . ($confirmation->rejection_reason ?? 'غير محدد')
                ], 403);
            }

            if ($confirmation->status !== 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ حالة الباركود غير صالحة: ' . $confirmation->status
                ], 403);
            }

            // ✅ الباركود مؤكد، نتابع جلب البيانات

            // جلب معلومات الكويل من جدول coil_transfers باستخدام production_barcode
            $coilTransfer = DB::table('coil_transfers')
                ->where('production_barcode', $barcode)
                ->first();

            if (!$coilTransfer) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على نقل كويل بهذا الباركود'
                ], 404);
            }

            // جلب معلومات الكويل الأصلي
            $coil = DB::table('delivery_note_coils')
                ->where('id', $coilTransfer->coil_id)
                ->first();

            if (!$coil) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على معلومات الكويل'
                ], 404);
            }

            // جلب معلومات إذن التسليم
            $deliveryNote = DB::table('delivery_notes')
                ->join('materials', 'delivery_notes.material_id', '=', 'materials.id')
                ->leftJoin('units', 'materials.unit_id', '=', 'units.id')
                ->where('delivery_notes.id', $coil->delivery_note_id)
                ->select(
                    'delivery_notes.*',
                    'materials.name_ar as material_name',
                    'materials.unit_id',
                    'units.unit_symbol'
                )
                ->first();

            if (!$deliveryNote) {
                return response()->json([
                    'success' => false,
                    'message' => 'الباركود غير موجود في النظام'
                ], 404);
            }

            // استخدام وزن النقل المحدد من coil_transfer (وزن الكويل المنقول فعلياً)
            $transferredToProduction = $coilTransfer->transfer_weight;

            // التحقق من توفر كمية منقولة للإنتاج
            if ($transferredToProduction <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم نقل أي كمية من هذا الإذن للإنتاج بعد. يجب النقل للإنتاج أولاً من صفحة تسجيل البضاعة.'
                ], 400);
            }

            // إعداد البيانات المرجعة
            $materialData = [
                'id' => $deliveryNote->material_id,
                'batch_id' => $deliveryNote->batch_id,
                'delivery_note_id' => $deliveryNote->id,
                'coil_id' => $coil->id,
                'coil_transfer_id' => $coilTransfer->id,
                'barcode' => $barcode,
                'coil_barcode' => $coil->coil_barcode,
                'coil_number' => $coil->coil_number,
                'material_name' => $deliveryNote->material_name,
                'material_type' => $deliveryNote->material_name,
                'initial_quantity' => $coilTransfer->transfer_weight,
                'available_quantity' => $coilTransfer->transfer_weight,
                'transferred_to_production' => $transferredToProduction,
                'production_weight' => $transferredToProduction,
                'remaining_weight' => $coilTransfer->transfer_weight,
                'unit_symbol' => $deliveryNote->unit_symbol ?? 'كجم',
                'warehouse_id' => $deliveryNote->warehouse_id,
                'unit_id' => $deliveryNote->unit_id,
            ];

            return response()->json([
                'success' => true,
                'material' => $materialData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show barcode scan page
     */
    public function barcodeScan()
    {
        return view('manufacturing::stages.stage1.barcode-scan');
    }

    /**
     * Process barcode scan
     */
    public function processBarcodeAction(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|string',
        ]);

        // Process barcode logic here
        return response()->json([
            'success' => true,
            'message' => 'تم معالجة الباركود بنجاح',
            'barcode' => $validated['barcode']
        ]);
    }

    /**
     * Show waste tracking page
     */
    public function wasteTracking()
    {
        return view('manufacturing::stages.stage1.waste-tracking');
    }

    /**
     * إنهاء الكويل وحساب الهدر الكلي
     * يتم استدعاء هذه الدالة عند انتهاء العامل من تقسيم الكويل على الاستاندات
     */
    public function finishCoil(Request $request)
    {
        $validated = $request->validate([
            'material_barcode' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $materialBarcode = $validated['material_barcode'];

            // جلب معلومات نقل الكويل
            $coilTransfer = DB::table('coil_transfers')
                ->where('production_barcode', $materialBarcode)
                ->first();

            if (!$coilTransfer) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على نقل الكويل بهذا الباركود'
                ], 404);
            }

            // حساب إجمالي الأوزان من الاستاندات
            $stage1Data = DB::table('stage1_stands')
                ->where('parent_barcode', $materialBarcode)
                ->selectRaw('
                    SUM(remaining_weight) as total_net_weight,
                    COUNT(*) as stands_count
                ')
                ->first();

            if (!$stage1Data || $stage1Data->stands_count == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على أي استاندات لهذا الكويل'
                ], 404);
            }

            // الوزن المنقول للإنتاج (وزن المادة الخام فقط، بدون وزن الاستاندات)
            $transferredWeight = $coilTransfer->transfer_weight;

            // إجمالي الوزن الصافي المنتج (المادة بعد التقسيم على الاستاندات)
            $totalNetWeight = $stage1Data->total_net_weight;

            // حساب الهدر الحقيقي
            // الهدر = الوزن المنقول - إجمالي الوزن الصافي المنتج
            $totalWaste = $transferredWeight - $totalNetWeight;
            $totalWaste = max(0, $totalWaste); // التأكد من عدم وجود قيم سالبة

            // حساب نسبة الهدر من الوزن المنقول
            $wastePercentage = $transferredWeight > 0 
                ? ($totalWaste / $transferredWeight) * 100 
                : 0;

            // جلب نسبة الهدر المسموح بها من إعدادات المرحلة الأولى
            // استخدام getStageWastePercentage للمرحلة 1
            $allowedPercentage = SystemSettingsHelper::getStageWastePercentage(1);

            // تحديد إذا تجاوز الهدر المسموح به
            $exceeded = $wastePercentage > $allowedPercentage;

            // تحديث حالة جميع الاستاندات
            $standStatus = $exceeded ? 'pending_approval' : 'completed';
            DB::table('stage1_stands')
                ->where('parent_barcode', $materialBarcode)
                ->update([
                    'status' => $standStatus,
                    'updated_at' => now(),
                ]);

            // إذا تجاوز الهدر، تسجيل في جدول stage_suspensions
            $suspensionId = null;
            if ($exceeded) {
                $suspension = StageSuspension::create([
                    'stage_number' => 1,
                    'batch_barcode' => $materialBarcode,
                    'input_weight' => $transferredWeight,  // الوزن المنقول للإنتاج
                    'output_weight' => $totalNetWeight,    // إجمالي الوزن الصافي المنتج
                    'waste_weight' => $totalWaste,
                    'waste_percentage' => $wastePercentage,
                    'allowed_percentage' => $allowedPercentage,
                    'status' => 'suspended',
                    'suspension_reason' => 'تجاوز نسبة الهدر المسموح بها في المرحلة الأولى',
                    'suspended_by' => Auth::id(),
                    'suspended_at' => now(),
                    'additional_data' => [
                        'coil_transfer_id' => $coilTransfer->id,
                        'stands_count' => $stage1Data->stands_count,
                    ],
                ]);
                $suspensionId = $suspension->id;
            }

            DB::commit();

            // إعداد الاستجابة
            $standStatus = $exceeded ? 'pending_approval' : 'completed';
            $response = [
                'success' => true,
                'message' => $exceeded 
                    ? '⛔ تم إنهاء الكويل مع تجاوز نسبة الهدر المسموح بها'
                    : '✅ تم إنهاء الكويل بنجاح',
                'exceeded' => $exceeded,
                'data' => [
                    'material_barcode' => $materialBarcode,
                    'transferred_weight' => $transferredWeight,  // الوزن المنقول للإنتاج
                    'total_net_weight' => $totalNetWeight,        // إجمالي الوزن الصافي المنتج
                    'total_waste' => $totalWaste,                 // الهدر = المنقول - الصافي
                    'waste_percentage' => round($wastePercentage, 2),
                    'allowed_percentage' => $allowedPercentage,
                    'stands_count' => $stage1Data->stands_count,
                    'status' => $standStatus,
                    'suspension_id' => $suspensionId,
                ]
            ];

            if ($exceeded) {
                $response['alert_title'] = '⛔ تجاوز نسبة الهدر المسموح بها';
                $response['alert_message'] = sprintf(
                    '🔴 تم إنهاء الكويل لكن تجاوزت نسبة الهدر الحد المسموح به:\n\n' .
                    '📊 ملخص الكويل:\n' .
                    '• الوزن المنقول للإنتاج: %s كجم\n' .
                    '• إجمالي الوزن الصافي المنتج: %s كجم\n' .
                    '• إجمالي الهدر: %s كجم\n' .
                    '• نسبة الهدر: %s%%\n' .
                    '• النسبة المسموح بها: %s%%\n' .
                    '• عدد الاستاندات: %d\n\n' .
                    '⏸️ تم إيقاف الاستاندات في انتظار موافقة الإدارة',
                    number_format($transferredWeight, 2),
                    number_format($totalNetWeight, 2),
                    number_format($totalWaste, 2),
                    number_format($wastePercentage, 2),
                    number_format($allowedPercentage, 2),
                    $stage1Data->stands_count
                );
            }

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error finishing coil', [
                'error' => $e->getMessage(),
                'barcode' => $validated['material_barcode'] ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنهاء الكويل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على معلومات الكويل والوزن المتبقي
     */
    public function getCoilInfo($barcode)
    {
        try {
            // جلب معلومات نقل الكويل
            $coilTransfer = DB::table('coil_transfers')
                ->where('production_barcode', $barcode)
                ->first();

            if (!$coilTransfer) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على الكويل'
                ], 404);
            }

            // حساب الوزن المستخدم
            $usedWeight = DB::table('stage1_stands')
                ->where('parent_barcode', $barcode)
                ->sum('remaining_weight');

            // حساب إجمالي الهدر
            $totalWaste = DB::table('stage1_stands')
                ->where('parent_barcode', $barcode)
                ->sum('waste');

            // عدد الاستاندات
            $standsCount = DB::table('stage1_stands')
                ->where('parent_barcode', $barcode)
                ->count();

            // جلب أسماء جميع العمال الذين عملوا على هذا الكويل
            $workersNames = DB::table('stage1_stands')
                ->join('users', 'stage1_stands.created_by', '=', 'users.id')
                ->where('stage1_stands.parent_barcode', $barcode)
                ->distinct()
                ->pluck('users.name')
                ->implode(', ');

            $transferredWeight = $coilTransfer->transfer_weight;
            $remainingWeight = $transferredWeight - $usedWeight;
            $usagePercentage = $transferredWeight > 0 
                ? ($usedWeight / $transferredWeight) * 100 
                : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'barcode' => $barcode,
                    'transferred_weight' => $transferredWeight,
                    'used_weight' => $usedWeight,
                    'remaining_weight' => $remainingWeight,
                    'total_waste' => $totalWaste,
                    'stands_count' => $standsCount,
                    'usage_percentage' => round($usagePercentage, 2),
                    'is_exhausted' => $remainingWeight <= 0,
                    'workers_names' => $workersNames,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * التحقق من وجود كويلات معلقة (غير منتهية) للمستخدم الحالي
     */
    public function getPendingCoils()
    {
        try {
            $userId = Auth::id();

            // جلب قائمة الباركودات التي تم نقلها وفي انتظار الموافقة (استبعادها)
            $pendingTransferBarcodes = DB::table('production_confirmations')
                ->where('status', 'pending')
                ->whereIn('confirmation_type', ['stand_transfer', 'coil_transfer'])
                ->pluck('barcode')
                ->toArray();

            // جلب قائمة الباركودات التي تم نقلها من هذا المستخدم (مؤكدة) - استبعادها
            $transferredFromMeBarcodes = DB::table('production_confirmations')
                ->where('assigned_by', $userId)
                ->where('confirmation_type', 'coil_transfer')
                ->where('status', 'confirmed')
                ->pluck('barcode')
                ->toArray();

            // جلب قائمة الباركودات التي تم نقلها إلى هذا المستخدم (مؤكدة) - إضافتها
            $transferredToMeBarcodes = DB::table('production_confirmations')
                ->where('assigned_to', $userId)
                ->where('confirmation_type', 'coil_transfer')
                ->where('status', 'confirmed')
                ->pluck('barcode')
                ->toArray();

            // 1. الكويلات التي أنشأها المستخدم ولم يتم نقلها
            $myCreatedBarcodes = DB::table('stage1_stands')
                ->where('created_by', $userId)
                ->whereNotIn('status', ['completed', 'consumed', 'pending_approval'])
                // استبعاد الكويلات التي تم نقلها (pending أو confirmed)
                ->when(!empty($pendingTransferBarcodes), fn($q) => $q->whereNotIn('parent_barcode', $pendingTransferBarcodes))
                ->when(!empty($transferredFromMeBarcodes), fn($q) => $q->whereNotIn('parent_barcode', $transferredFromMeBarcodes))
                ->distinct()
                ->pluck('parent_barcode')
                ->toArray();

            // 2. دمج مع الكويلات المنقولة إليه والتي لها وزن متبقي
            $allPendingBarcodes = array_unique(array_merge($myCreatedBarcodes, $transferredToMeBarcodes));

            // فلترة الكويلات - استبعاد فقط الكويلات في انتظار الموافقة
            $pendingBarcodes = collect($allPendingBarcodes)->filter(function($barcode) use ($pendingTransferBarcodes) {
                // استبعاد الكويلات في انتظار الموافقة
                if (in_array($barcode, $pendingTransferBarcodes)) {
                    return false;
                }

                $hasActiveStands = DB::table('stage1_stands')
                    ->where('parent_barcode', $barcode)
                    ->whereNotIn('status', ['completed', 'consumed', 'pending_approval'])
                    ->exists();

                // لا نعرض الكويل إذا لم يعد لديه استاندات نشطة
                if (!$hasActiveStands) {
                    return false;
                }
                // التحقق من وجود coil_transfer صالح
                $coilTransfer = DB::table('coil_transfers')
                    ->where('production_barcode', $barcode)
                    ->first();
                
                // إذا لم يكن هناك transfer، يكفي وجود استاندات نشطة لعرضه
                return (bool) $coilTransfer || $hasActiveStands;
            })->values();

            if ($pendingBarcodes->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'pending_coils' => [],
                    'count' => 0
                ]);
            }

            // جلب جميع الاستاندات لهذه الباركودات
            $pendingCoils = DB::table('stage1_stands as s')
                ->leftJoin('materials as m', 's.material_id', '=', 'm.id')
                ->leftJoin('coil_transfers as ct', 's.parent_barcode', '=', 'ct.production_barcode')
                ->leftJoin('users as u', 's.created_by', '=', 'u.id')
                ->whereIn('s.parent_barcode', $pendingBarcodes)
                ->select(
                    's.parent_barcode as barcode',
                    DB::raw('MAX(COALESCE(ct.transfer_weight, 0)) as transfer_weight'),
                    DB::raw('COALESCE(MAX(m.name_ar), MAX(m.name_en), CONCAT("كويل ", s.parent_barcode)) as material_name'),
                    DB::raw('COUNT(s.id) as stands_count'),
                    DB::raw('SUM(s.remaining_weight) as used_weight'),
                    DB::raw('SUM(s.waste) as total_waste'),
                    DB::raw('MAX(s.updated_at) as last_activity'),
                    DB::raw('GROUP_CONCAT(DISTINCT u.name ORDER BY u.name SEPARATOR ", ") as workers_names')
                )
                ->groupBy('s.parent_barcode')
                ->orderBy('last_activity', 'desc')
                ->get();

            // حساب transfer_weight إذا كان صفر
            $pendingCoils = $pendingCoils->map(function($coil) {
                if ($coil->transfer_weight == 0) {
                    $coil->transfer_weight = $coil->used_weight + $coil->total_waste;
                }
                return $coil;
            });

            return response()->json([
                'success' => true,
                'pending_coils' => $pendingCoils,
                'count' => $pendingCoils->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * جلب قائمة الموظفين المتاحين للنقل
     */
    public function getWorkersForTransfer()
    {
        try {
            $currentUserId = Auth::id();

            // جلب الموظفين النشطين (استثناء المستخدم الحالي)
            $workers = DB::table('users')
                ->where('is_active', true)
                ->where('id', '!=', $currentUserId)
                ->select('id', 'name', 'email')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'workers' => $workers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * نقل كويل غير مكتمل لموظف آخر
     */
    public function transferCoil(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|string',
            'new_worker_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ], [
            'barcode.required' => 'باركود الكويل مطلوب',
            'new_worker_id.required' => 'يجب اختيار الموظف الجديد',
            'new_worker_id.exists' => 'الموظف المحدد غير موجود',
        ]);

        try {
            DB::beginTransaction();

            $currentUserId = Auth::id();
            $newWorkerId = $validated['new_worker_id'];
            $barcode = $validated['barcode'];

            // التحقق من أن الكويل موجود ولديه استاندات غير مكتملة
            $coilStands = DB::table('stage1_stands')
                ->where('parent_barcode', $barcode)
                ->where('created_by', $currentUserId)
                ->whereNotIn('status', ['completed', 'consumed'])
                ->get();

            if ($coilStands->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يوجد كويل معلق بهذا الباركود أو أنت لست صاحب هذا الكويل'
                ], 404);
            }

            // التحقق من أن الموظف الجديد مختلف
            if ($currentUserId == $newWorkerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكنك نقل الكويل لنفسك'
                ], 400);
            }

            // جلب معلومات الكويل
            $coilInfo = DB::table('stage1_stands as s')
                ->leftJoin('materials as m', 's.material_id', '=', 'm.id')
                ->leftJoin('coil_transfers as ct', 's.parent_barcode', '=', 'ct.production_barcode')
                ->where('s.parent_barcode', $barcode)
                ->select(
                    's.parent_barcode',
                    's.material_id',
                    DB::raw('COALESCE(m.name_ar, m.name_en, s.parent_barcode) as material_name'),
                    DB::raw('MAX(COALESCE(ct.transfer_weight, 0)) as transfer_weight'),
                    DB::raw('SUM(s.remaining_weight) as used_weight'),
                    DB::raw('COUNT(s.id) as stands_count')
                )
                ->groupBy('s.parent_barcode', 's.material_id', 'm.name_ar', 'm.name_en')
                ->first();

            $transferWeight = $coilInfo->transfer_weight ?: $coilInfo->used_weight;
            $remainingWeight = $transferWeight - $coilInfo->used_weight;

            // ❌ لا نغير ملكية الاستاندات الموجودة - كل استاند يبقى باسم منشئه
            // ✅ بدلاً من ذلك، نستخدم production_confirmations لمنح الموظف الجديد الحق في استخدام الكويل
            // الاستاندات الجديدة التي سينشئها ستُسجل باسمه تلقائياً

            // إنشاء سجل تأكيد للموظف الجديد - يمنحه الحق في استخدام الكويل
            $confirmationId = DB::table('production_confirmations')->insertGetId([
                'delivery_note_id' => null,
                'batch_id' => null,
                'stage_code' => 'stage1',
                'stage_record_id' => $coilStands->first()->id,
                'stage_type' => 'stage1_stands',
                'worker_stage_history_id' => null,
                'barcode' => $barcode,
                'assigned_to' => $newWorkerId,
                'assigned_by' => $currentUserId,
                'status' => 'pending', // في انتظار الموافقة من الموظف المستلم
                'confirmation_type' => 'coil_transfer',
                'notes' => $validated['notes'] ?? null,
                'metadata' => json_encode([
                    'stage_name' => 'المرحلة الأولى',
                    'operation' => 'coil_transfer',
                    'reason' => $validated['reason'] ?? null,
                    'initiated_by' => Auth::user()?->name,
                    'previous_worker_id' => $currentUserId,
                    'material_name' => $coilInfo->material_name,
                    'transfer_weight' => $transferWeight,
                    'used_weight' => $coilInfo->used_weight,
                    'remaining_weight' => $remainingWeight,
                    'stands_count' => $coilInfo->stands_count,
                ]),
                'confirmed_at' => null,
                'confirmed_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // إرسال إشعار للموظف الجديد
            try {
                DB::table('notifications')->insert([
                    'user_id' => $newWorkerId,
                    'type' => 'coil_transfer',
                    'title' => 'تم نقل كويل إليك',
                    'message' => "تم نقل الكويل {$barcode} ({$coilInfo->material_name}) إليك من " . Auth::user()?->name . ". المتبقي: " . number_format($remainingWeight, 2) . " كجم",
                    'metadata' => json_encode([
                        'barcode' => $barcode,
                        'stage' => 'stage1',
                        'material_name' => $coilInfo->material_name,
                        'remaining_weight' => $remainingWeight,
                        'from_worker_id' => $currentUserId,
                        'confirmation_id' => $confirmationId,
                    ]),
                    'created_by' => $currentUserId,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $notifError) {
                // تجاهل خطأ الإشعار - لا يؤثر على عملية النقل
                \Log::warning('فشل إرسال إشعار نقل الكويل', ['error' => $notifError->getMessage()]);
            }

            DB::commit();

            $newWorker = DB::table('users')->where('id', $newWorkerId)->first();

            return response()->json([
                'success' => true,
                'message' => "تم نقل الكويل {$barcode} بنجاح إلى {$newWorker->name}",
                'data' => [
                    'barcode' => $barcode,
                    'new_worker_id' => $newWorkerId,
                    'new_worker_name' => $newWorker->name,
                    'remaining_weight' => $remainingWeight,
                    'confirmation_id' => $confirmationId,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء النقل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * قبول نقل كويل من موظف آخر
     */
    public function acceptCoilTransfer(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $userId = Auth::id();
            $barcode = $validated['barcode'];

            // التحقق من وجود طلب نقل معلق لهذا المستخدم
            $pendingTransfer = DB::table('production_confirmations')
                ->where('barcode', $barcode)
                ->where('assigned_to', $userId)
                ->where('confirmation_type', 'coil_transfer')
                ->where('status', 'pending')
                ->first();

            if (!$pendingTransfer) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يوجد طلب نقل معلق لهذا الكويل'
                ], 404);
            }

            // تحديث حالة النقل إلى مؤكدة
            DB::table('production_confirmations')
                ->where('id', $pendingTransfer->id)
                ->update([
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                    'confirmed_by' => $userId,
                    'updated_at' => now()
                ]);

            // إرسال إشعار للموظف الناقل
            try {
                $metadata = json_decode($pendingTransfer->metadata, true);
                DB::table('notifications')->insert([
                    'user_id' => $pendingTransfer->assigned_by,
                    'type' => 'coil_transfer_accepted',
                    'title' => 'تم قبول نقل الكويل',
                    'message' => "تم قبول نقل الكويل {$barcode} من قبل " . Auth::user()?->name,
                    'metadata' => json_encode([
                        'barcode' => $barcode,
                        'stage' => 'stage1',
                        'accepted_by' => Auth::user()?->name,
                        'accepted_by_id' => $userId,
                    ]),
                    'created_by' => $userId,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $notifError) {
                \Log::warning('فشل إرسال إشعار قبول نقل الكويل', ['error' => $notifError->getMessage()]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم قبول نقل الكويل بنجاح',
                'data' => [
                    'barcode' => $barcode,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء قبول النقل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * رفض نقل كويل من موظف آخر
     */
    public function rejectCoilTransfer(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|string',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $userId = Auth::id();
            $barcode = $validated['barcode'];

            // التحقق من وجود طلب نقل معلق لهذا المستخدم
            $pendingTransfer = DB::table('production_confirmations')
                ->where('barcode', $barcode)
                ->where('assigned_to', $userId)
                ->where('confirmation_type', 'coil_transfer')
                ->where('status', 'pending')
                ->first();

            if (!$pendingTransfer) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يوجد طلب نقل معلق لهذا الكويل'
                ], 404);
            }

            // تحديث حالة النقل إلى مرفوضة
            DB::table('production_confirmations')
                ->where('id', $pendingTransfer->id)
                ->update([
                    'status' => 'rejected',
                    'confirmed_at' => now(),
                    'confirmed_by' => $userId,
                    'notes' => $validated['reason'] ?? 'تم الرفض من قبل الموظف',
                    'updated_at' => now()
                ]);

            // إرسال إشعار للموظف الناقل
            try {
                DB::table('notifications')->insert([
                    'user_id' => $pendingTransfer->assigned_by,
                    'type' => 'coil_transfer_rejected',
                    'title' => 'تم رفض نقل الكويل',
                    'message' => "تم رفض نقل الكويل {$barcode} من قبل " . Auth::user()?->name . ($validated['reason'] ? ". السبب: " . $validated['reason'] : ''),
                    'metadata' => json_encode([
                        'barcode' => $barcode,
                        'stage' => 'stage1',
                        'rejected_by' => Auth::user()?->name,
                        'rejected_by_id' => $userId,
                        'reason' => $validated['reason'] ?? null,
                    ]),
                    'created_by' => $userId,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $notifError) {
                \Log::warning('فشل إرسال إشعار رفض نقل الكويل', ['error' => $notifError->getMessage()]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم رفض نقل الكويل',
                'data' => [
                    'barcode' => $barcode,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفض النقل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * جلب طلبات نقل الكويلات المعلقة للمستخدم الحالي
     */
    public function getPendingTransfers()
    {
        try {
            $userId = Auth::id();

            $pendingTransfers = DB::table('production_confirmations as pc')
                ->leftJoin('users as sender', 'pc.assigned_by', '=', 'sender.id')
                ->leftJoin('coil_transfers as ct', 'pc.barcode', '=', 'ct.production_barcode')
                ->leftJoin('materials as m', 'ct.material_id', '=', 'm.id')
                ->where('pc.assigned_to', $userId)
                ->where('pc.confirmation_type', 'coil_transfer')
                ->where('pc.status', 'pending')
                ->select(
                    'pc.id',
                    'pc.barcode',
                    'pc.assigned_by',
                    'sender.name as sender_name',
                    'pc.notes',
                    'pc.created_at',
                    'pc.metadata',
                    DB::raw('COALESCE(m.name_ar, m.name_en, pc.barcode) as material_name'),
                    DB::raw('COALESCE(ct.transfer_weight, 0) as transfer_weight')
                )
                ->orderBy('pc.created_at', 'desc')
                ->get();

            // حساب الوزن المتبقي لكل كويل
            $pendingTransfers = $pendingTransfers->map(function($transfer) {
                $usedWeight = DB::table('stage1_stands')
                    ->where('parent_barcode', $transfer->barcode)
                    ->sum('remaining_weight');
                
                $transfer->used_weight = $usedWeight;
                $transfer->remaining_weight = $transfer->transfer_weight - $usedWeight;
                
                // استخراج معلومات إضافية من metadata
                $metadata = json_decode($transfer->metadata, true) ?? [];
                $transfer->reason = $metadata['reason'] ?? null;
                
                return $transfer;
            });

            return response()->json([
                'success' => true,
                'transfers' => $pendingTransfers,
                'count' => $pendingTransfers->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }
}
