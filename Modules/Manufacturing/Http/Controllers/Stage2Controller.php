<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SystemSettingsHelper;
use Modules\Manufacturing\Models\StandUsageHistory;

class Stage2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     * Worker sees only their operations
     * Admin/Supervisor sees all operations
     */
    public function index()
    {
        $user = Auth::user();

        // Query base
        $query = DB::table('stage2_processed')
            ->leftJoin('stage1_stands', 'stage2_processed.stage1_id', '=', 'stage1_stands.id')
            ->leftJoin('materials', 'stage2_processed.material_id', '=', 'materials.id')
            ->leftJoin('users', 'stage2_processed.created_by', '=', 'users.id')
            ->select(
                'stage2_processed.*',
                'stage1_stands.stand_number',
                'stage1_stands.barcode as stage1_barcode',
                'materials.name_ar as material_name',
                'users.name as created_by_name'
            );

        // إذا لم يكن لديه صلاحية رؤية جميع العمليات، يعرض فقط عملياته
        $viewingAll = $user->hasPermission('VIEW_ALL_STAGE2_OPERATIONS');

        if (!$viewingAll) {
            $query->where('stage2_processed.created_by', $user->id);
        }

        $processed = $query->orderBy('stage2_processed.created_at', 'desc')
            ->paginate(20);

        return view('manufacturing::stages.stage2.index', compact('processed', 'viewingAll'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturing::stages.stage2.create');
    }

    /**
     * Get data by barcode - accepts two sources:
     * 1. Stage1 barcode (ST1-XXX)
     * 2. Direct production barcode from warehouse (for Stage2)
     */
    public function getByBarcode($barcode)
    {
        try {
            // 🔍 خطوة 1: البحث في stage1_stands أولاً
            $stage1Data = DB::table('stage1_stands')
                ->leftJoin('materials', 'stage1_stands.material_id', '=', 'materials.id')
                ->where('stage1_stands.barcode', $barcode)
                ->select('stage1_stands.*', 'materials.name_ar as material_name')
                ->first();

            if ($stage1Data) {
                // 🔒 التحقق من حالة الاستاند - يجب أن يكون مكتمل أو مستهلك
                $allowedStatuses = ['completed', 'consumed', 'in_progress'];
                
                if ($stage1Data->status === 'pending_approval') {
                    return response()->json([
                        'success' => false,
                        'blocked' => true,
                        'message' => '⛔ هذا الاستاند في انتظار الموافقة ولا يمكن استخدامه في المرحلة الثانية'
                    ], 403);
                }
                
                // التحقق من أن الاستاند منتهي من المرحلة الأولى (ليس created أو unused)
                if (in_array($stage1Data->status, ['created', 'unused'])) {
                    return response()->json([
                        'success' => false,
                        'blocked' => true,
                        'message' => '⛔ هذا الاستاند لم يتم إنهاء الكويل الخاص به بعد. يرجى إنهاء الكويل أولاً من المرحلة الأولى.'
                    ], 403);
                }
                
                // 🔒 التحقق من أن الاستاند لم يُنهَ بالكامل في المرحلة الثانية
                // التحقق من وجود معالجات مكتملة لهذا الاستاند
                $completedProcessings = DB::table('stage2_processed')
                    ->where('parent_barcode', $barcode)
                    ->whereIn('status', ['completed', 'consumed'])
                    ->exists();
                
                // إذا كان هناك معالجات مكتملة والوزن المتبقي = 0، فالاستاند منتهي
                if ($completedProcessings && $stage1Data->remaining_weight <= 0) {
                    return response()->json([
                        'success' => false,
                        'blocked' => true,
                        'message' => '⛔ هذا الاستاند تم إنهاؤه بالكامل في المرحلة الثانية ولا يمكن استخدامه مرة أخرى.'
                    ], 403);
                }

                // ✅ التحقق من عدم وجود confirmation معلقة لهذا الباركود (معاد إسناده)
                $pendingConfirmation = \App\Models\ProductionConfirmation::where('barcode', $stage1Data->barcode)
                    ->where('status', 'pending')
                    ->first();

                if ($pendingConfirmation) {
                    return response()->json([
                        'success' => false,
                        'blocked' => true,
                        'message' => '⛔ هذا الباركود معاد إسناده ويحتاج موافقة من العامل المسند إليه أولاً'
                    ], 403);
                }

                // ✅ وُجد في المرحلة الأولى
                return response()->json([
                    'success' => true,
                    'data' => $stage1Data,
                    'source' => 'stage1',
                    'pending_processings' => $this->getPendingProcessingsForBarcode($barcode)
                ]);
            }

            // 🔍 خطوة 2: البحث في delivery_notes (باركودات مرسلة مباشرة للمرحلة الثانية)
            $confirmation = DB::table('production_confirmations')
                ->join('delivery_notes', 'production_confirmations.delivery_note_id', '=', 'delivery_notes.id')
                ->join('material_batches', 'production_confirmations.batch_id', '=', 'material_batches.id')
                ->join('materials', 'material_batches.material_id', '=', 'materials.id')
                ->where('delivery_notes.production_barcode', $barcode)
                ->where('production_confirmations.stage_code', 'stage_2')
                ->where('production_confirmations.status', 'confirmed')
                ->select(
                    'production_confirmations.id',
                    'delivery_notes.production_barcode as barcode',
                    DB::raw('COALESCE(production_confirmations.actual_received_quantity, delivery_notes.quantity, 0) as remaining_weight'),
                    'material_batches.material_id',
                    'materials.name_ar as material_name',
                    DB::raw('0 as wire_size')
                )
                ->first();

            if ($confirmation) {
                // ✅ وُجد كباركود مرسل مباشرة للمرحلة الثانية
                return response()->json([
                    'success' => true,
                    'data' => $confirmation,
                    'source' => 'warehouse_direct',
                    'pending_processings' => $this->getPendingProcessingsForBarcode($barcode)
                ]);
            }

            // ❌ لم يُعثر على الباركود في أي مصدر
            return response()->json([
                'success' => false,
                'message' => '❌ لم يتم العثور على هذا الباركود في المرحلة الأولى أو في الباركودات المرسلة مباشرة للمرحلة الثانية'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a single processed item immediately (instant save)
     */
    public function storeSingle(Request $request)
    {
        $validated = $request->validate([
            'stage1_id' => 'nullable|integer', // nullable لأن قد يكون المصدر warehouse_direct
            'stage1_barcode' => 'required|string',
            'source' => 'nullable|string', // stage1 or warehouse_direct
            'material_id' => 'nullable|integer',
            'input_weight' => 'nullable|numeric|min:0',
            'total_weight' => 'nullable|numeric|min:0',
            'waste_weight' => 'nullable|numeric|min:0',
            'net_weight' => 'nullable|numeric|min:0',
            'process_type' => 'nullable|string',
            'process_details' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $userId = Auth::id();
            $source = $validated['source'] ?? 'stage1';

            // جلب البيانات حسب المصدر
            if ($source === 'warehouse_direct') {
                // المصدر من المخزن مباشرة - استخدام البيانات المرسلة
                $inputWeight = $validated['input_weight'] ?? 0;
                $materialId = $validated['material_id'] ?? null;
                $wireSize = 0; // لا يوجد wire_size من المخزن
                $standNumber = $validated['stage1_barcode']; // استخدام الباركود كرقم
                $stage1Id = null; // لا يوجد stage1_id
            } else {
                // المصدر من المرحلة الأولى
                $stage1Data = DB::table('stage1_stands')
                    ->where('id', $validated['stage1_id'])
                    ->first();

                if (!$stage1Data) {
                    throw new \Exception('لم يتم العثور على بيانات المرحلة الأولى');
                }

                // 🔒 التحقق من حالة الاستاند
                if ($stage1Data->status === 'pending_approval') {
                    throw new \Exception('⛔ هذا الاستاند في انتظار الموافقة ولا يمكن استخدامه في المرحلة الثانية');
                }

                // ✅ التحقق من عدم وجود confirmation معلقة لهذا الباركود (معاد إسناده)
                $pendingConfirmation = \App\Models\ProductionConfirmation::where('barcode', $stage1Data->barcode)
                    ->where('status', 'pending')
                    ->first();

                if ($pendingConfirmation) {
                    throw new \Exception('⛔ هذا الباركود معاد إسناده ويحتاج موافقة من العامل المسند إليه أولاً');
                }

                $inputWeight = $stage1Data->remaining_weight;
                $materialId = $stage1Data->material_id ?? null;
                $wireSize = $stage1Data->wire_size ?? null;
                $standNumber = $stage1Data->stand_number ?? 'غير محدد';
                $stage1Id = $validated['stage1_id'];
                
                // ⚡ التحقق من أن الوزن المطلوب لا يتجاوز الوزن المتبقي
                $requestedWeight = $validated['total_weight'] ?? 0;
                if ($requestedWeight > $inputWeight) {
                    throw new \Exception("⚠️ وزن المعالجة ({$requestedWeight} كجم) أكبر من الوزن المتبقي ({$inputWeight} كجم)");
                }
                
                // ⚡ التحقق من وجود وزن متبقي
                if ($inputWeight <= 0) {
                    throw new \Exception('⚠️ لا يوجد وزن متبقي في هذا الاستاند');
                }
            }

            // حساب الأوزان
            $wasteWeight = $validated['waste_weight'] ?? 0;
            $outputWeight = $validated['total_weight'] ?? $inputWeight;
            $netWeight = $validated['net_weight'] ?? $outputWeight;

            // الحالة الافتراضية: in_progress (سيتم حساب الهدر عند إنهاء الاستاند)
            $recordStatus = 'in_progress';

            // توليد باركود المرحلة الثانية
            $stage2Barcode = $this->generateStageBarcode('stage2');

            // حفظ في جدول stage2_processed
            $stage2Id = DB::table('stage2_processed')->insertGetId([
                'barcode' => $stage2Barcode,
                'parent_barcode' => $validated['stage1_barcode'],
                'stage1_id' => $stage1Id, // null إذا كان warehouse_direct
                'material_id' => $materialId,
                'wire_size' => $wireSize,
                'input_weight' => $inputWeight,
                'output_weight' => $outputWeight,
                'waste' => $wasteWeight,
                'remaining_weight' => $netWeight,
                'process_details' => $validated['process_details'] ?? null,
                'status' => $recordStatus, // استخدام الحالة المحددة من فحص الهدر
                'notes' => $validated['notes'],
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // تحديث حالة المرحلة الأولى وخصم الوزن (فقط إذا كان المصدر stage1)
            if ($stage1Id) {
                // ⚡ خصم وزن المعالجة من الوزن المتبقي في الاستاند
                $newRemainingWeight = $stage1Data->remaining_weight - $outputWeight;
                
                // التأكد من أن الوزن المتبقي لا يكون سالباً
                if ($newRemainingWeight < 0) {
                    $newRemainingWeight = 0;
                }
                
                // تحديد الحالة: إذا استُهلك الوزن بالكامل يصبح consumed، وإلا يبقى completed (جزء منه استُخدم)
                $newStatus = ($newRemainingWeight <= 0) ? 'consumed' : 'completed';
                
                DB::table('stage1_stands')
                    ->where('id', $stage1Id)
                    ->update([
                        'remaining_weight' => $newRemainingWeight,
                        'status' => $newStatus,
                        'updated_at' => now(),
                    ]);
                
                // 🔥 إنهاء سجل العامل في المرحلة الأولى
                \App\Models\WorkerStageHistory::where('stage_type', \App\Models\WorkerStageHistory::STAGE_1_STANDS)
                    ->where('stage_record_id', $stage1Id)
                    ->where('is_active', true)
                    ->update([
                        'is_active' => false,
                        'ended_at' => now(),
                        'duration_minutes' => DB::raw('TIMESTAMPDIFF(MINUTE, started_at, NOW())'),
                        'status_after' => 'completed',
                    ]);
            }

            // تسجيل التتبع في product_tracking
            DB::table('product_tracking')->insert([
                'barcode' => $stage2Barcode,
                'stage' => 'stage2',
                'action' => 'processed',
                'input_barcode' => $validated['stage1_barcode'],
                'output_barcode' => $stage2Barcode,
                'input_weight' => $inputWeight,
                'output_weight' => $netWeight,
                'waste_amount' => $wasteWeight,
                'waste_percentage' => $inputWeight > 0 ?
                    ($wasteWeight / $inputWeight * 100) : 0,
                'worker_id' => $userId,
                'shift_id' => null,
                'notes' => $validated['notes'],
                'metadata' => json_encode([
                    'source' => $source,
                    'stage1_id' => $stage1Id,
                    'stage1_barcode' => $validated['stage1_barcode'],
                    'material_id' => $materialId,
                    'wire_size' => $wireSize,
                    'process_type' => $validated['process_type'] ?? null,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 🔥 تسجيل العامل في نظام تتبع العمال
            try {
                $trackingService = app(\App\Services\WorkerTrackingService::class);
                $trackingService->assignWorkerToStage(
                    stageType: \App\Models\WorkerStageHistory::STAGE_2_PROCESSED,
                    stageRecordId: $stage2Id,
                    workerId: $userId,
                    barcode: $stage2Barcode,
                    statusBefore: $recordStatus,
                    assignedBy: $userId
                );
            } catch (\Exception $e) {
                \Log::error('Failed to register worker tracking for Stage2', [
                    'error' => $e->getMessage(),
                    'stage2_id' => $stage2Id,
                    'worker_id' => $userId,
                ]);
            }

            DB::commit();

            // الحصول على اسم المادة
            $materialName = 'غير محدد';
            if ($materialId) {
                $material = DB::table('materials')->where('id', $materialId)->first();
                $materialName = $material->name_ar ?? 'غير محدد';
            }

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ المعالجة بنجاح!',
                'data' => [
                    'stage2_id' => $stage2Id,
                    'stage2_barcode' => $stage2Barcode,
                    'stand_number' => $standNumber,
                    'net_weight' => $netWeight,
                    'material_name' => $materialName,
                    'waste_weight' => $wasteWeight,
                    'source' => $source,
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
            'stage1_id' => 'required|integer',
            'stage1_barcode' => 'required|string',
            'total_weight' => 'required|numeric|min:0',
            'waste_weight' => 'nullable|numeric|min:0',
            'net_weight' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // التحقق من أن الباركود لم يُستخدم من قبل
            $barcodeExists = DB::table('stage2_processed')
                ->where('parent_barcode', $validated['stage1_barcode'])
                ->exists();

            if ($barcodeExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا الباركود تم استخدامه مسبقاً في المرحلة الثانية'
                ], 422);
            }

            DB::beginTransaction();

            $userId = Auth::id();

            // جلب بيانات المرحلة الأولى
            $stage1Data = DB::table('stage1_stands')
                ->where('id', $validated['stage1_id'])
                ->first();

            if (!$stage1Data) {
                throw new \Exception('لم يتم العثور على بيانات المرحلة الأولى');
            }

            // توليد باركود المرحلة الثانية
            $stage2Barcode = $this->generateStageBarcode('stage2');

            // حفظ في جدول stage2_processed
            $stage2Id = DB::table('stage2_processed')->insertGetId([
                'barcode' => $stage2Barcode,
                'parent_barcode' => $validated['stage1_barcode'],
                'stage1_id' => $validated['stage1_id'],
                'material_id' => $stage1Data->material_id ?? null,
                'wire_size' => $stage1Data->wire_size ?? null,
                'input_weight' => $stage1Data->remaining_weight,
                'output_weight' => $validated['total_weight'],
                'waste' => $validated['waste_weight'] ?? 0,
                'remaining_weight' => $validated['net_weight'],
                'process_details' => $validated['process_details'] ?? null,
                'status' => 'in_progress',
                'notes' => $validated['notes'],
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // تحديث حالة المرحلة الأولى (consumed بدلاً من in_process لأنها انتقلت للمرحلة التالية)
            DB::table('stage1_stands')
                ->where('id', $validated['stage1_id'])
                ->update([
                    'status' => 'consumed',
                    'updated_at' => now(),
                ]);

            // تسجيل التتبع في product_tracking
            DB::table('product_tracking')->insert([
                'barcode' => $stage2Barcode,
                'stage' => 'stage2',
                'action' => 'processed',
                'input_barcode' => $validated['stage1_barcode'],
                'output_barcode' => $stage2Barcode,
                'input_weight' => $stage1Data->remaining_weight,
                'output_weight' => $validated['net_weight'],
                'waste_amount' => $validated['waste_weight'] ?? 0,
                'waste_percentage' => $stage1Data->remaining_weight > 0 ?
                    (($validated['waste_weight'] ?? 0) / $stage1Data->remaining_weight * 100) : 0,
                'worker_id' => $userId,
                'shift_id' => null,
                'notes' => $validated['notes'],
                'metadata' => json_encode([
                    'stage1_id' => $validated['stage1_id'],
                    'stage1_barcode' => $validated['stage1_barcode'],
                    'material_id' => $stage1Data->material_id,
                    'wire_size' => $stage1Data->wire_size,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // الحصول على اسم المادة
            $materialName = 'غير محدد';
            if ($stage1Data->material_id) {
                $material = DB::table('materials')->where('id', $stage1Data->material_id)->first();
                $materialName = $material->name_ar ?? 'غير محدد';
            }

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ البيانات بنجاح!',
                'data' => [
                    'stage2_id' => $stage2Id,
                    'stage2_barcode' => $stage2Barcode,
                    'net_weight' => $validated['net_weight'],
                    'barcode_info' => [
                        'barcode' => $stage2Barcode,
                        'stand_number' => $stage1Data->stand_number ?? 'غير محدد',
                        'material_name' => $materialName,
                        'net_weight' => $validated['net_weight'],
                        'input_weight' => $stage1Data->remaining_weight,
                        'waste_weight' => $validated['waste_weight'] ?? 0,
                    ]
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
        $settings = DB::table('barcode_settings')
            ->where('type', $stageType)
            ->where('is_active', true)
            ->first();

        if (!$settings) {
            $prefix = strtoupper($stageType);
            $number = DB::table('stage2_processed')->count() + 1;
            return "{$prefix}-" . date('Y') . "-" . str_pad($number, 3, '0', STR_PAD_LEFT);
        }

        DB::table('barcode_settings')
            ->where('id', $settings->id)
            ->increment('current_number');

        $newNumber = $settings->current_number + 1;
        $paddedNumber = str_pad($newNumber, $settings->padding, '0', STR_PAD_LEFT);

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
        // جلب البيانات من stage2_processed
        $record = DB::table('stage2_processed')
            ->leftJoin('users as creator', 'stage2_processed.created_by', '=', 'creator.id')
            ->where('stage2_processed.id', $id)
            ->select(
                'stage2_processed.*',
                'creator.name as created_by_name'
            )
            ->first();

        if (!$record) {
            abort(404, 'السجل غير موجود');
        }

        // تحويل created_at إلى Carbon instance
        $record->created_at = \Carbon\Carbon::parse($record->created_at);

        // إنشاء creator object
        $record->creator = (object) ['name' => $record->created_by_name ?? 'غير محدد'];

        // جلب سجل العمليات من operation_logs
        $operationLogs = DB::table('operation_logs')
            ->leftJoin('users', 'operation_logs.user_id', '=', 'users.id')
            ->where(function($query) use ($id, $record) {
                $query->where('operation_logs.table_name', 'stage2_processed')
                      ->where('operation_logs.record_id', $id);
            })
            ->orWhere('operation_logs.description', 'LIKE', '%' . $record->barcode . '%')
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
            ->where(function($query) use ($record) {
                $query->where('product_tracking.barcode', $record->barcode)
                      ->orWhere('product_tracking.input_barcode', $record->parent_barcode)
                      ->orWhere('product_tracking.output_barcode', $record->barcode);
            })
            ->select(
                'product_tracking.*',
                'worker.name as worker_name'
            )
            ->orderBy('product_tracking.created_at', 'desc')
            ->get();

        // جلب سجل الاستخدام
        $usageHistory = DB::table('stand_usage_history')
            ->leftJoin('users', 'stand_usage_history.user_id', '=', 'users.id')
            ->where('stand_usage_history.material_barcode', $record->parent_barcode)
            ->select(
                'stand_usage_history.*',
                'users.name as user_name'
            )
            ->orderBy('stand_usage_history.created_at', 'desc')
            ->first();

        return view('manufacturing::stages.stage2.show', compact('record', 'operationLogs', 'trackingLogs', 'usageHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('manufacturing::stages.stage2.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'process_details' => 'required|string',
            'output_weight' => 'required|numeric',
            'waste' => 'nullable|numeric',
            'status' => 'nullable|in:started,in_progress,completed,consumed',
        ]);

        // $processed = Stage2Processed::find($id);
        // $processed->update($validated);

        return redirect()->route('manufacturing.stage2.index')
            ->with('success', 'تم تحديث المعالجة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Stage2Processed::find($id)->delete();

        return redirect()->route('manufacturing.stage2.index')
            ->with('success', 'تم حذف المعالجة بنجاح');
    }

    /**
     * Show complete processing page
     */
    public function completeProcessing()
    {
        return view('manufacturing::stages.stage2.complete-processing');
    }

    /**
     * Complete processing action
     */
    public function completeAction(Request $request)
    {
        $validated = $request->validate([
            'output_weight' => 'required|numeric|min:0',
            'waste_weight' => 'required|numeric|min:0',
            'quality_status' => 'required|in:excellent,good,acceptable,rejected',
            'notes' => 'nullable|string',
        ]);

        // Complete processing logic here
        return redirect()->route('manufacturing.stage2.index')
            ->with('success', 'تم إنهاء المعالجة بنجاح');
    }

    /**
     * Show waste statistics page
     */
    public function wasteStatistics()
    {
        return view('manufacturing::stages.stage2.waste-statistics');
    }

    /**
     * جلب الاستاندات المعلقة للمستخدم الحالي (من المرحلة الأولى)
     * الاستاندات التي تم البدء بها ولم يتم إنهاؤها
     */
    public function getPendingItems()
    {
        try {
            $user = Auth::user();
            $userId = $user->id;

            // التحقق من الصلاحيات - هل يمكن رؤية جميع الاستاندات؟
            $viewingAll = $user->hasPermission('VIEW_ALL_STAGE2_OPERATIONS');
            $canViewAllPending = $viewingAll || $user->hasPermission('STAGE_SUSPENSION_APPROVE');

            // جلب قائمة الباركودات التي تم نقلها وفي انتظار الموافقة
            $transferredBarcodes = DB::table('production_confirmations')
                ->where('status', 'pending')
                ->whereIn('confirmation_type', ['stand_transfer', 'coil_transfer'])
                ->pluck('barcode')
                ->toArray();

            // جلب الاستاندات التي لها معالجات pending (غير منتهية)
            // ملاحظة: نحتاج الاستاندات التي لها معالجات in_progress بغض النظر عن حالة الاستاند نفسه
            $pendingItems = DB::table('stage1_stands')
                ->join('stage2_processed', 'stage1_stands.barcode', '=', 'stage2_processed.parent_barcode')
                ->leftJoin('materials', 'stage1_stands.material_id', '=', 'materials.id')
                ->when(!$canViewAllPending, fn($q) => $q->where('stage2_processed.created_by', $userId))
                ->where('stage2_processed.status', 'in_progress') // فقط المعالجات المعلقة
                // استبعاد الاستاندات التي تم نقلها وفي انتظار الموافقة
                ->when(!empty($transferredBarcodes), fn($q) => $q->whereNotIn('stage1_stands.barcode', $transferredBarcodes))
                ->select(
                    'stage1_stands.id',
                    'stage1_stands.barcode',
                    'stage1_stands.stand_number',
                    'stage1_stands.remaining_weight',
                    'stage1_stands.wire_size',
                    'stage1_stands.status',
                    DB::raw('COUNT(stage2_processed.id) as pending_count'),
                    DB::raw('SUM(stage2_processed.output_weight) as total_processed'),
                    DB::raw('GROUP_CONCAT(stage2_processed.barcode) as stage2_barcodes'),
                    'materials.name_ar as material_name'
                )
                ->groupBy(
                    'stage1_stands.id',
                    'stage1_stands.barcode',
                    'stage1_stands.stand_number',
                    'stage1_stands.remaining_weight',
                    'stage1_stands.wire_size',
                    'stage1_stands.status',
                    'materials.name_ar'
                )
                ->orderBy('stage1_stands.updated_at', 'desc')
                ->get();

            // Debug logging
            \Log::info('Pending Items Query Result:', [
                'count' => $pendingItems->count(),
                'items' => $pendingItems->toArray()
            ]);

            return response()->json([
                'success' => true,
                'count' => $pendingItems->count(),
                'items' => $pendingItems,
                'viewing_all' => $canViewAllPending
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify processing IDs exist in database
     * Used to clean up localStorage from deleted records
     */
    public function verifyProcessings(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json([
                    'success' => true,
                    'valid_ids' => []
                ]);
            }
            
            // جلب الـ IDs الموجودة فعلياً في قاعدة البيانات
            $validIds = DB::table('stage2_processed')
                ->whereIn('id', $ids)
                ->pluck('id')
                ->toArray();
            
            return response()->json([
                'success' => true,
                'valid_ids' => $validIds,
                'checked_count' => count($ids),
                'valid_count' => count($validIds),
                'removed_count' => count($ids) - count($validIds)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finish stand - تحويل جميع معالجات الاستاند من pending إلى confirmed
     */
    public function finishStand(Request $request)
    {
        $validated = $request->validate([
            'processing_ids' => 'nullable|array',
            'processing_ids.*' => 'integer',
            'stand_barcode' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $userId = Auth::id();
            $standBarcode = $validated['stand_barcode'];
            
            // إذا لم يتم إرسال processing_ids، نجلبها من قاعدة البيانات
            if (empty($validated['processing_ids'])) {
                $processingIds = DB::table('stage2_processed')
                    ->where('parent_barcode', $standBarcode)
                    ->where('created_by', $userId)
                    ->where('status', 'in_progress')
                    ->pluck('id')
                    ->toArray();
            } else {
                $processingIds = $validated['processing_ids'];
            }

            // التحقق من أن جميع المعالجات موجودة وتابعة لنفس الاستاند
            $processings = DB::table('stage2_processed')
                ->whereIn('id', $processingIds)
                ->where('parent_barcode', $standBarcode)
                ->where('created_by', $userId)
                ->get();

            if ($processings->count() !== count($processingIds)) {
                throw new \Exception('بعض المعالجات غير موجودة أو لا تنتمي لهذا الاستاند');
            }

            // حساب الهدر الإجمالي
            $totalInput = $processings->sum('input_weight');
            $totalOutput = $processings->sum('output_weight');
            $totalWaste = $totalInput - $totalOutput;
            $wastePercentage = $totalInput > 0 ? ($totalWaste / $totalInput * 100) : 0;

            // جلب نسبة الهدر المسموح بها من إعدادات المرحلة الثانية
            // استخدام getStageWastePercentage للمرحلة 2
            $allowedPercentage = SystemSettingsHelper::getStageWastePercentage(2);

            // تحديد إذا تجاوز الهدر المسموح به
            $exceeded = $wastePercentage > $allowedPercentage;

            // تحديد حالة الاستاند
            $standStatus = $exceeded ? 'pending_approval' : 'completed';

            // إذا تجاوز الهدر، تسجيل في جدول stage_suspensions
            $suspensionId = null;
            if ($exceeded) {
                $suspension = \App\Models\StageSuspension::create([
                    'stage_number' => 2,
                    'batch_barcode' => $standBarcode,
                    'input_weight' => $totalInput,
                    'output_weight' => $totalOutput,
                    'waste_weight' => $totalWaste,
                    'waste_percentage' => $wastePercentage,
                    'allowed_percentage' => $allowedPercentage,
                    'status' => 'suspended',
                    'suspension_reason' => 'تجاوز نسبة الهدر المسموح بها في المرحلة الثانية',
                    'suspended_by' => Auth::id(),
                    'suspended_at' => now(),
                    'additional_data' => [
                        'processing_count' => $processings->count(),
                        'processing_ids' => $processingIds,
                    ],
                ]);
                $suspensionId = $suspension->id;
            }

            // تحديث حالة جميع المعالجات
            DB::table('stage2_processed')
                ->whereIn('id', $processingIds)
                ->update([
                    'status' => $standStatus,
                    'waste' => DB::raw('input_weight - output_weight'), // حساب الهدر لكل معالجة
                    'updated_at' => now()
                ]);

            // تحديث حالة الاستاند في stage1
            // ملاحظة: حالة stage1_stands يجب أن تبقى 'consumed' ولا تتغير إلى 'pending_approval'
            // لأن pending_approval هي حالة خاصة بـ stage2_processed فقط
            $stage1Stand = DB::table('stage1_stands')
                ->where('barcode', $standBarcode)
                ->first();

            if ($stage1Stand) {
                // حالة الاستاند في المرحلة الأولى تبقى 'consumed' دائماً عند استخدامه في المرحلة الثانية
                // فقط سجلات stage2_processed تأخذ حالة pending_approval إذا تجاوز الهدر
                DB::table('stage1_stands')
                    ->where('barcode', $standBarcode)
                    ->update([
                        'status' => 'consumed', // دائماً consumed وليس $standStatus
                        'updated_at' => now()
                    ]);
            }

            DB::commit();

            // إعداد الاستجابة
            $response = [
                'success' => true,
                'message' => $exceeded 
                    ? '⛔ تم إنهاء الاستاند مع تجاوز نسبة الهدر المسموح بها'
                    : '✅ تم إنهاء الاستاند بنجاح',
                'exceeded' => $exceeded,
                'data' => [
                    'stand_barcode' => $standBarcode,
                    'total_processings' => count($processingIds),
                    'total_input' => $totalInput,
                    'total_output' => $totalOutput,
                    'total_waste' => $totalWaste,
                    'waste_percentage' => round($wastePercentage, 2),
                    'allowed_percentage' => $allowedPercentage,
                    'status' => $standStatus,
                    'suspension_id' => $suspensionId,
                ]
            ];

            if ($exceeded) {
                $response['alert_title'] = '⚠️ تجاوز نسبة الهدر المسموح بها';
                $response['alert_message'] = sprintf(
                    '🔴 تم إنهاء الاستاند لكن تجاوزت نسبة الهدر الحد المسموح به:<br><br>' .
                    '📊 ملخص الاستاند:<br>' .
                    '• إجمالي الوزن الداخل: %s كجم<br>' .
                    '• إجمالي الوزن الخارج: %s كجم<br>' .
                    '• إجمالي الهدر: %s كجم<br>' .
                    '• نسبة الهدر: <span style="color: #dc3545; font-weight: bold;">%s%%</span><br>' .
                    '• النسبة المسموح بها: <span style="color: #28a745; font-weight: bold;">%s%%</span><br>' .
                    '• عدد المعالجات: %d<br><br>' .
                    '⏸️ تم إيقاف المعالجات في انتظار موافقة الإدارة',
                    number_format($totalInput, 2),
                    number_format($totalOutput, 2),
                    number_format($totalWaste, 2),
                    number_format($wastePercentage, 2),
                    number_format($allowedPercentage, 2),
                    count($processingIds)
                );
            }

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'فشل إنهاء الاستاند: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete processing - حذف معالجة معلقة فقط (pending)
     */
    public function deleteProcessing($id)
    {
        try {
            $userId = Auth::id();

            // التحقق من وجود المعالجة وأنها pending
            $processing = DB::table('stage2_processed')
                ->where('id', $id)
                ->where('created_by', $userId)
                ->first();

            if (!$processing) {
                return response()->json([
                    'success' => false,
                    'message' => 'المعالجة غير موجودة أو ليس لديك صلاحية لحذفها'
                ], 404);
            }

            if ($processing->status !== 'in_progress') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف معالجة منتهية (completed). يمكن حذف المعالجات المعلقة فقط.'
                ], 403);
            }

            DB::beginTransaction();

            // ⚡ إعادة الوزن المحذوف إلى الاستاند الأصلي
            if ($processing->stage1_id) {
                DB::table('stage1_stands')
                    ->where('id', $processing->stage1_id)
                    ->increment('remaining_weight', $processing->output_weight);
            }

            // حذف المعالجة
            DB::table('stage2_processed')
                ->where('id', $id)
                ->delete();

            // حذف سجل التتبع
            DB::table('product_tracking')
                ->where('barcode', $processing->barcode)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المعالجة بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'فشل حذف المعالجة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get workers for transfer - جلب قائمة العمال المتاحين لنقل الاستاند
     */
    public function getWorkersForTransfer()
    {
        try {
            $currentUserId = Auth::id();

            // جلب جميع العمال النشطين عدا المستخدم الحالي
            $workers = DB::table('users')
                ->where('id', '!=', $currentUserId)
                ->where('is_active', true)
                ->select('id', 'name', 'email')
                ->get();

            return response()->json([
                'success' => true,
                'workers' => $workers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل جلب قائمة العمال: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transfer stand - نقل استاند لموظف آخر (المرحلة الثانية)
     */
    public function transferStand(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|string',
            'new_worker_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000'
        ], [
            'barcode.required' => 'باركود الاستاند مطلوب',
            'new_worker_id.required' => 'يجب اختيار الموظف الجديد',
            'new_worker_id.exists' => 'الموظف المحدد غير موجود'
        ]);

        try {
            DB::beginTransaction();

            $currentUserId = Auth::id();
            $newWorkerId = $validated['new_worker_id'];
            $barcode = $validated['barcode'];

            // التحقق من أن الاستاند موجود في stage1_stands
            // ملاحظة: في المرحلة الثانية، الاستاند قد يكون بحالة consumed أو completed
            // لكن يجب أن يكون لديه وزن متبقي أو معالجات in_progress
            $stand = DB::table('stage1_stands')
                ->where('barcode', $barcode)
                ->first();

            if (!$stand) {
                return response()->json([
                    'success' => false,
                    'message' => 'الاستاند غير موجود'
                ], 404);
            }
            
            // التحقق من الصلاحية:
            // 1. صاحب الاستاند الأصلي (created_by في stage1_stands)
            // 2. أو لديه معالجات في المرحلة الثانية (بأي حالة)
            // 3. أو تم نقل الاستاند إليه (confirmation مؤكدة)
            $hasStage2Processings = DB::table('stage2_processed')
                ->where('parent_barcode', $barcode)
                ->where('created_by', $currentUserId)
                ->exists(); // أي معالجة وليس فقط in_progress
            
            $isOriginalOwner = ($stand->created_by == $currentUserId);
            
            // التحقق من وجود نقل مؤكد لهذا المستخدم
            $hasTransferToMe = DB::table('production_confirmations')
                ->where('barcode', $barcode)
                ->where('assigned_to', $currentUserId)
                ->where('status', 'confirmed')
                ->whereIn('confirmation_type', ['stand_transfer', 'coil_transfer'])
                ->exists();
            
            if (!$isOriginalOwner && !$hasStage2Processings && !$hasTransferToMe) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا تملك صلاحية نقل هذا الاستاند'
                ], 403);
            }

            // التحقق من أن الموظف الجديد مختلف
            if ($currentUserId == $newWorkerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكنك نقل الاستاند لنفسك'
                ], 400);
            }

            // التحقق من الوزن المتبقي
            if ($stand->remaining_weight <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن نقل استاند تم استهلاكه بالكامل'
                ], 400);
            }

            // جلب معلومات الموظف الجديد
            $newWorker = DB::table('users')
                ->where('id', $newWorkerId)
                ->first();

            if (!$newWorker) {
                return response()->json([
                    'success' => false,
                    'message' => 'الموظف المحدد غير موجود'
                ], 404);
            }

            // جلب معلومات المادة
            $materialName = DB::table('materials')
                ->where('id', $stand->material_id)
                ->value('name_ar');

            // ❌ لا نغير ملكية الاستاند أو المعالجات الموجودة - كل معالجة تبقى باسم منشئها
            // ✅ بدلاً من ذلك، نستخدم production_confirmations لمنح الموظف الجديد الحق في استخدام الاستاند
            // المعالجات الجديدة التي سينشئها ستُسجل باسمه تلقائياً

            // إنشاء سجل تأكيد في production_confirmations - يمنح الموظف الجديد الحق في استخدام الاستاند
            $confirmationId = DB::table('production_confirmations')->insertGetId([
                'delivery_note_id' => null,
                'batch_id' => null,
                'stage_code' => 'stage2',
                'stage_record_id' => $stand->id,
                'stage_type' => 'stage1_stands', // لأنه استاند من المرحلة الأولى
                'worker_stage_history_id' => null,
                'barcode' => $barcode,
                'assigned_to' => $newWorkerId,
                'assigned_by' => $currentUserId,
                'status' => 'pending', // في انتظار الموافقة من الموظف المستلم
                'confirmation_type' => 'stand_transfer',
                'notes' => $validated['notes'] ?? null,
                'metadata' => json_encode([
                    'stage_name' => 'المرحلة الثانية',
                    'operation' => 'stand_transfer',
                    'reason' => $validated['reason'] ?? 'نقل الاستاند',
                    'initiated_by' => Auth::user()?->name,
                    'previous_worker_id' => $currentUserId,
                    'material_name' => $materialName,
                    'remaining_weight' => $stand->remaining_weight,
                    'wire_size' => $stand->wire_size
                ]),
                'confirmed_at' => null,
                'confirmed_by' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // إرسال إشعار للموظف الجديد
            try {
                DB::table('notifications')->insert([
                    'user_id' => $newWorkerId,
                    'type' => 'stand_transfer',
                    'title' => 'تم نقل استاند إليك',
                    'message' => "تم نقل الاستاند {$barcode} ({$materialName}) إليك من " . Auth::user()?->name . ". المتبقي: " . number_format($stand->remaining_weight, 2) . " كجم",
                    'metadata' => json_encode([
                        'barcode' => $barcode,
                        'stage' => 'stage2',
                        'material_name' => $materialName,
                        'remaining_weight' => $stand->remaining_weight,
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
                \Log::warning('فشل إرسال إشعار نقل الاستاند', ['error' => $notifError->getMessage()]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم نقل الاستاند بنجاح',
                'data' => [
                    'barcode' => $barcode,
                    'new_worker_name' => $newWorker->name,
                    'confirmation_id' => $confirmationId
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('فشل نقل الاستاند في Stage2', [
                'error' => $e->getMessage(),
                'barcode' => $validated['barcode'] ?? null,
                'new_worker_id' => $validated['new_worker_id'] ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل نقل الاستاند: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getPendingProcessingsForBarcode(string $barcode)
    {
        return DB::table('stage2_processed')
            ->leftJoin('materials', 'stage2_processed.material_id', '=', 'materials.id')
            ->where('stage2_processed.parent_barcode', $barcode)
            ->where('stage2_processed.status', 'in_progress')
            ->select(
                'stage2_processed.id',
                'stage2_processed.barcode',
                'stage2_processed.parent_barcode',
                'stage2_processed.process_details',
                'stage2_processed.output_weight',
                'stage2_processed.status',
                'materials.name_ar as material_name'
            )
            ->orderBy('stage2_processed.id')
            ->get();
    }

    /**
     * قبول نقل استاند من موظف آخر
     */
    public function acceptStandTransfer(Request $request)
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
                ->where('confirmation_type', 'stand_transfer')
                ->where('status', 'pending')
                ->first();

            if (!$pendingTransfer) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يوجد طلب نقل معلق لهذا الاستاند'
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
                DB::table('notifications')->insert([
                    'user_id' => $pendingTransfer->assigned_by,
                    'type' => 'stand_transfer_accepted',
                    'title' => 'تم قبول نقل الاستاند',
                    'message' => "تم قبول نقل الاستاند {$barcode} من قبل " . Auth::user()?->name,
                    'metadata' => json_encode([
                        'barcode' => $barcode,
                        'stage' => 'stage2',
                        'accepted_by' => Auth::user()?->name,
                        'accepted_by_id' => $userId,
                    ]),
                    'created_by' => $userId,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $notifError) {
                \Log::warning('فشل إرسال إشعار قبول نقل الاستاند', ['error' => $notifError->getMessage()]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم قبول نقل الاستاند بنجاح',
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
     * رفض نقل استاند من موظف آخر
     */
    public function rejectStandTransfer(Request $request)
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
                ->where('confirmation_type', 'stand_transfer')
                ->where('status', 'pending')
                ->first();

            if (!$pendingTransfer) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يوجد طلب نقل معلق لهذا الاستاند'
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
                    'type' => 'stand_transfer_rejected',
                    'title' => 'تم رفض نقل الاستاند',
                    'message' => "تم رفض نقل الاستاند {$barcode} من قبل " . Auth::user()?->name . ($validated['reason'] ? ". السبب: " . $validated['reason'] : ''),
                    'metadata' => json_encode([
                        'barcode' => $barcode,
                        'stage' => 'stage2',
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
                \Log::warning('فشل إرسال إشعار رفض نقل الاستاند', ['error' => $notifError->getMessage()]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم رفض نقل الاستاند',
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
     * جلب طلبات نقل الاستاندات المعلقة للمستخدم الحالي
     */
    public function getPendingTransfers()
    {
        try {
            $userId = Auth::id();

            $pendingTransfers = DB::table('production_confirmations as pc')
                ->leftJoin('users as sender', 'pc.assigned_by', '=', 'sender.id')
                ->leftJoin('stage1_stands as s', 'pc.barcode', '=', 's.barcode')
                ->leftJoin('materials as m', 's.material_id', '=', 'm.id')
                ->where('pc.assigned_to', $userId)
                ->where('pc.confirmation_type', 'stand_transfer')
                ->where('pc.status', 'pending')
                ->select(
                    'pc.id',
                    'pc.barcode',
                    'pc.assigned_by',
                    'sender.name as sender_name',
                    'pc.notes',
                    'pc.created_at',
                    'pc.metadata',
                    's.remaining_weight',
                    's.wire_size',
                    's.stand_number',
                    DB::raw('COALESCE(m.name_ar, m.name_en, pc.barcode) as material_name')
                )
                ->orderBy('pc.created_at', 'desc')
                ->get();

            // استخراج معلومات إضافية من metadata
            $pendingTransfers = $pendingTransfers->map(function($transfer) {
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
