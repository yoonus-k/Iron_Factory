<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
                ->where('barcode', $barcode)
                ->first();

            if ($stage1Data) {
                // 🔒 التحقق من حالة الاستاند
                if ($stage1Data->status === 'pending_approval') {
                    return response()->json([
                        'success' => false,
                        'blocked' => true,
                        'message' => '⛔ هذا الاستاند في انتظار الموافقة ولا يمكن استخدامه في المرحلة الثانية'
                    ], 403);
                }

                // ✅ وُجد في المرحلة الأولى
                return response()->json([
                    'success' => true,
                    'data' => $stage1Data,
                    'source' => 'stage1'
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
                    'source' => 'warehouse_direct'
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

                $inputWeight = $stage1Data->remaining_weight;
                $materialId = $stage1Data->material_id ?? null;
                $wireSize = $stage1Data->wire_size ?? null;
                $standNumber = $stage1Data->stand_number ?? 'غير محدد';
                $stage1Id = $validated['stage1_id'];
            }

            // حساب الأوزان
            $wasteWeight = $validated['waste_weight'] ?? ($inputWeight * 0.03); // افتراض 3% هدر
            $outputWeight = $validated['total_weight'] ?? ($inputWeight - $wasteWeight);
            $netWeight = $validated['net_weight'] ?? $outputWeight;

            // 🔥 فحص نسبة الهدر قبل الحفظ
            $wasteCheck = \App\Services\WasteCheckService::checkAndSuspend(
                stageNumber: 2,
                batchBarcode: $validated['stage1_barcode'],
                batchId: $materialId,
                inputWeight: $inputWeight,
                outputWeight: $outputWeight
            );
            $wasteData = $wasteCheck['data'] ?? [];

            // تسجيل نتيجة فحص الهدر
            \Log::info('Stage 2 Waste Check Result', [
                'suspended' => $wasteCheck['suspended'] ?? false,
                'suspension_id' => $wasteCheck['suspension_id'] ?? null,
                'waste_percentage' => $wasteData['waste_percentage'] ?? 0,
                'allowed_percentage' => $wasteData['allowed_percentage'] ?? 0,
                'input_weight' => $inputWeight,
                'output_weight' => $outputWeight,
            ]);

            // تحديد الحالة بناءً على فحص الهدر
            $recordStatus = $wasteCheck['suspended'] ? 'pending_approval' : 'in_progress';
            $suspensionId = $wasteCheck['suspension_id'] ?? null;

            \Log::info('Stage 2 Record Status Determined', [
                'status' => $recordStatus,
                'will_show_alert' => $recordStatus === 'pending_approval',
            ]);

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

            // تحديث حالة المرحلة الأولى (فقط إذا كان المصدر stage1)
            if ($stage1Id) {
                DB::table('stage1_stands')
                    ->where('id', $stage1Id)
                    ->update([
                        'status' => 'in_process',
                        'updated_at' => now(),
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

            // إذا كانت الحالة pending_approval، نرجع استجابة خاصة
            if ($recordStatus === 'pending_approval') {
                return response()->json([
                    'success' => true,
                    'pending_approval' => true,
                    'blocked' => true,
                    'message' => '⛔ تم إيقاف الانتقال للمرحلة الثالثة',
                    'alert_title' => '⛔ تم إيقاف الانتقال للمرحلة الثالثة',
                    'alert_message' => sprintf(
                        '🔴 <strong>تم حفظ المعالجة بنجاح لكن تم إيقاف الانتقال للمرحلة الثالثة</strong><br><br>'.
                        '📊 <strong>تفاصيل الهدر:</strong><br>'.
                        '• نسبة الهدر الفعلية: <span style="color: #dc3545; font-weight: bold;">%s%%</span><br>'.
                        '• النسبة المسموح بها: <span style="color: #28a745; font-weight: bold;">%s%%</span><br><br>'.
                        '⏸️ <strong>الحالة:</strong> في انتظار موافقة الإدارة<br><br>'.
                        '⚠️ <strong>مهم:</strong> لن يمكن استخدام هذا السجل في المرحلة الثالثة حتى تتم الموافقة عليه من قبل الإدارة.',
                        number_format($wasteData['waste_percentage'] ?? 0, 2),
                        number_format($wasteData['allowed_percentage'] ?? 0, 2)
                    ),
                    'data' => [
                        'stage2_id' => $stage2Id,
                        'barcode' => $stage2Barcode,
                        'stand_number' => $standNumber ?? 'غير محدد',
                        'net_weight' => $netWeight,
                        'material_name' => $materialName ?? 'غير محدد',
                        'status' => 'pending_approval',
                        'suspension_id' => $suspensionId,
                        'waste_weight' => $wasteData['waste_weight'] ?? 0,
                        'waste_percentage' => $wasteData['waste_percentage'] ?? 0,
                        'allowed_percentage' => $wasteData['allowed_percentage'] ?? 0,
                    ]
                ]);
            }

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

            // تحديث حالة المرحلة الأولى
            DB::table('stage1_stands')
                ->where('id', $validated['stage1_id'])
                ->update([
                    'status' => 'in_process',
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
}
