<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Stand;
use App\Models\StandUsageHistory;
use App\Services\WasteCheckService;
use App\Helpers\SystemSettingsHelper;

class Stage1Controller extends Controller
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
        $query = DB::table('stage1_stands')
            ->join('materials', 'stage1_stands.material_id', '=', 'materials.id')
            ->leftJoin('users', 'stage1_stands.created_by', '=', 'users.id')
            ->select(
                'stage1_stands.*',
                'materials.name_ar as material_name',
                'users.name as created_by_name'
            );

        // إذا لم يكن لديه صلاحية رؤية جميع العمليات، يعرض فقط عملياته
        $viewingAll = $user->hasPermission('VIEW_ALL_STAGE1_OPERATIONS');

        if (!$viewingAll) {
            $query->where('stage1_stands.created_by', $user->id);
        }

        $stands = $query->orderBy('stage1_stands.created_at', 'desc')
            ->paginate(20);

        return view('manufacturing::stages.stage1.index', compact('stands', 'viewingAll'));
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

            // حساب الكمية المنقولة للإنتاج
            $transferredToProduction = DB::table('material_movements')
                ->where('batch_id', $materialBatch->id)
                ->where('movement_type', 'to_production')
                ->sum('quantity');

            // حساب الكمية المستخدمة سابقاً
            $usedInStage1 = DB::table('stage1_stands')
                ->where('parent_barcode', $validated['material_barcode'])
                ->sum('remaining_weight');

            $availableWeight = $transferredToProduction - $usedInStage1;

            if ($availableWeight < $netWeight) {
                throw new \Exception("الكمية المتوفرة للإنتاج ({$availableWeight} كجم) غير كافية للكمية المطلوبة ({$netWeight} كجم)");
            }

            // 🔥 فحص نسبة الهدر قبل الحفظ
            // الحساب الصحيح:
            // inputWeight = الوزن الصافي + وزن الهدر (المادة الفعلية المستخدمة)
            // outputWeight = الوزن الصافي (ما تبقى بعد التصنيع)
            // waste = inputWeight - outputWeight
            $outputWeight = $netWeight; // المادة الخارجة
            $wasteWeight = $validated['waste_weight'] ?? 0; // الهدر
            $materialWeight = $outputWeight + $wasteWeight; // المادة الداخلة الفعلية

            \Log::info('Waste Calculation Check', [
                'net_weight' => $outputWeight,
                'waste_weight' => $wasteWeight,
                'material_weight' => $materialWeight,
                'total_weight' => $validated['total_weight'],
                'stand_weight' => $standWeight,
            ]);

            $wasteCheck = WasteCheckService::checkAndSuspend(
                stageNumber: 1,
                batchBarcode: $validated['material_barcode'],
                batchId: $materialBatch->id,
                inputWeight: $materialWeight,
                outputWeight: $outputWeight
            );
            $wasteData = $wasteCheck['data'] ?? [];

            // تسجيل نتيجة فحص الهدر
            \Log::info('Waste Check Result', [
                'suspended' => $wasteCheck['suspended'] ?? false,
                'suspension_id' => $wasteCheck['suspension_id'] ?? null,
                'waste_percentage' => $wasteData['waste_percentage'] ?? 0,
                'allowed_percentage' => $wasteData['allowed_percentage'] ?? 0,
                'exceeded' => $wasteData['exceeded'] ?? false,
                'material_weight' => $materialWeight,
                'output_weight' => $outputWeight,
            ]);

            // تحديد الحالة بناءً على فحص الهدر
            // إذا تجاوز الهدر، يتم الحفظ بحالة pending_approval
            $recordStatus = $wasteCheck['suspended'] ? 'pending_approval' : 'created';
            $suspensionId = $wasteCheck['suspension_id'] ?? null;

            \Log::info('Record Status Determined', [
                'status' => $recordStatus,
                'will_show_alert' => $recordStatus === 'pending_approval',
            ]);

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
                'waste' => $validated['waste_weight'] ?? 0,
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
                'waste_amount' => $validated['waste_weight'] ?? 0,
                'waste_percentage' => $validated['waste_percentage'] ?? 0,
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

            DB::commit();

            // إذا كانت الحالة pending_approval، نرجع استجابة خاصة
            if ($recordStatus === 'pending_approval') {
                return response()->json([
                    'success' => true,
                    'pending_approval' => true,
                    'blocked' => true,
                    'message' => '⛔ تم إيقاف الانتقال للمرحلة الثانية',
                    'alert_title' => '⛔ تم إيقاف الانتقال للمرحلة الثانية',
                    'alert_message' => sprintf(
                        '🔴 <strong>تم حفظ الاستاند بنجاح لكن تم إيقاف الانتقال للمرحلة الثانية</strong>\n\n'.                        '📊 <strong>تفاصيل الهدر:</strong>\n'.                        '• نسبة الهدر الفعلية: <span style="color: #dc3545; font-weight: bold;">%s%%</span>\n'.                        '• النسبة المسموح بها: <span style="color: #28a745; font-weight: bold;">%s%%</span>\n\n'.                        '⏸️ <strong>الحالة:</strong> في انتظار موافقة الإدارة\n\n'.                        '⚠️ <strong>مهم:</strong> لن يمكن استخدام هذا الاستاند في المرحلة الثانية حتى تتم الموافقة عليه من قبل الإدارة.',
                        number_format($wasteData['waste_percentage'] ?? 0, 2),
                        number_format($wasteData['allowed_percentage'] ?? 0, 2)
                    ),
                    'data' => [
                        'stand_id' => $stage1StandId,
                        'barcode' => $stage1Barcode,
                        'stand_number' => $stand->stand_number,
                        'net_weight' => $netWeight,
                        'material_name' => $materialBatch->material_name ?? 'غير محدد',
                        'status' => 'pending_approval',
                        'suspension_id' => $suspensionId,
                        'waste_weight' => $wasteData['waste_weight'] ?? 0,
                        'waste_percentage' => $wasteData['waste_percentage'] ?? 0,
                        'allowed_percentage' => $wasteData['allowed_percentage'] ?? 0,
                    ]
                ]);
            }

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

                // 🔥 فحص نسبة الهدر قبل الحفظ
                // الوزن الفعلي للمادة (بدون وزن الاستاند)
                $materialWeight = $processedData['total_weight'] - $processedData['stand_weight'];
                $outputWeight = $processedData['net_weight'];

                $wasteCheck = WasteCheckService::checkAndSuspend(
                    stageNumber: 1,
                    batchBarcode: $validated['material_barcode'],
                    batchId: $materialBatch->id,
                    inputWeight: $materialWeight,
                    outputWeight: $outputWeight
                );
                $wasteData = $wasteCheck['data'] ?? [];

                // 🔥 تحديد حالة السجل: 'pending_approval' إذا تم الإيقاف، 'created' إذا كان عادي
                $recordStatus = $wasteCheck['suspended'] ? 'pending_approval' : 'created';

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

                // خصم الكمية من المخزن (إنشاء معاملة خروج)
                // DB::table('warehouse_transactions')->insert([
                //     'warehouse_id' => $materialDetail->warehouse_id,
                //     'material_id' => $materialId,
                //     'transaction_number' => 'OUT-' . $validated['material_barcode'] . '-' . $stand->id,
                //     'transaction_type' => 'issue',
                //     'quantity' => $processedData['total_weight'],
                //     'unit_id' => $materialDetail->unit_id ?? 1,
                //     'reference_number' => 'STAGE1-' . $usageHistory->id,
                //     'notes' => "تقسيم على استاند {$stand->stand_number} - المرحلة الأولى",
                //     'created_by' => $userId,
                //     'created_at' => now(),
                //     'updated_at' => now(),
                // ]);

                // تحديث remaining_weight في material_details
                // DB::table('material_details')
                //     ->where('material_id', $materialId)
                //     ->decrement('remaining_weight', $processedData['total_weight']);

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
                    'status' => $recordStatus, // 🔥 استخدام الحالة الديناميكية
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
                    'status' => $recordStatus, // 🔥 إضافة الحالة
                    'pending_approval' => $wasteCheck['suspended'],
                    'waste_percentage' => $wasteData['waste_percentage'] ?? 0,
                    'allowed_percentage' => $wasteData['allowed_percentage'] ?? 0,
                ];
            }

            // ملاحظة: لا نقوم بتحديث available_quantity في material_batches
            // لأن الكمية المتوفرة تمثل ما هو موجود في المخزن فعلياً
            // نحن فقط نتتبع الاستخدام من الكمية المنقولة للإنتاج عبر جدول stage1_stands

            DB::commit();

            // فحص إذا كان هناك أي سجلات في انتظار الموافقة
            $hasPendingApproval = collect($processedRecords)->contains('pending_approval', true);
            $pendingCount = collect($processedRecords)->where('pending_approval', true)->count();

            // تحضير قائمة الباركودات لعرضها
            $barcodesList = collect($processedRecords)->map(function($record) use ($materialBatch) {
                return [
                    'stand_number' => $record['stand_number'],
                    'barcode' => $record['stage1_barcode'],
                    'net_weight' => $record['net_weight'],
                    'material_name' => $materialBatch->material_name ?? 'غير محدد',
                    'status' => $record['status'] ?? 'created',
                    'pending_approval' => $record['pending_approval'] ?? false,
                ];
            })->toArray();

            $response = [
                'success' => true,
                'message' => 'تم حفظ جميع الاستاندات بنجاح!',
                'data' => [
                    'processed_count' => count($processedRecords),
                    'total_weight_used' => $totalNetWeightNeeded,
                    'remaining_weight' => $availableWeight - $totalNetWeightNeeded,
                    'records' => $processedRecords,
                    'barcodes' => $barcodesList,
                ]
            ];

            // إذا كان هناك سجلات في انتظار الموافقة، نضيف تنبيه
            if ($hasPendingApproval) {
                $response['has_pending_approval'] = true;
                $response['pending_count'] = $pendingCount;
                $response['alert_title'] = 'تم الحفظ مع تجاوز نسبة الهدر';
                $response['alert_message'] = sprintf(
                    '%d من %d استاند في انتظار الموافقة بسبب تجاوز نسبة الهدر المسموح بها. لن يمكن استخدامها في المرحلة الثانية حتى تتم الموافقة عليها من قبل الإدارة.',
                    $pendingCount,
                    count($processedRecords)
                );
            }

            return response()->json($response);

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

        // جلب الوردية الحالية للمرحلة
        $currentShift = DB::table('shift_assignments')
            ->leftJoin('users', 'shift_assignments.user_id', '=', 'users.id')
            ->leftJoin('users as supervisors', 'shift_assignments.supervisor_id', '=', 'supervisors.id')
            ->where('shift_assignments.stage_record_id', $id)
            ->where('shift_assignments.stage_number', 1)
            ->where('shift_assignments.status', 'active')
            ->select(
                'shift_assignments.*',
                'users.name as worker_name',
                'supervisors.name as supervisor_name'
            )
            ->first();

        // جلب الورديات السابقة للمرحلة
        $previousShifts = DB::table('shift_handovers')
            ->leftJoin('users as from_user', 'shift_handovers.from_user_id', '=', 'from_user.id')
            ->leftJoin('users as to_user', 'shift_handovers.to_user_id', '=', 'to_user.id')
            ->where('shift_handovers.stage_number', 1)
            ->where(function($query) use ($id) {
                $query->where('shift_handovers.handover_items', 'LIKE', '%"stage_record_id":' . $id . '%')
                      ->orWhere('shift_handovers.notes', 'LIKE', '%' . $id . '%');
            })
            ->select(
                'shift_handovers.*',
                'from_user.name as from_user_name',
                'to_user.name as to_user_name'
            )
            ->orderBy('shift_handovers.handover_time', 'desc')
            ->limit(10)
            ->get();

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
            'previousShifts',
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
            $confirmation = DB::table('production_confirmations')
                ->join('delivery_notes', 'production_confirmations.delivery_note_id', '=', 'delivery_notes.id')
                ->where('delivery_notes.production_barcode', $barcode)
                ->where('production_confirmations.stage_code', 'stage_1')
                ->select(
                    'production_confirmations.*',
                    'delivery_notes.production_barcode',
                    'delivery_notes.batch_id'
                )
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

            // البحث عن الباركود في جدول barcodes أولاً
            $barcodeRecord = DB::table('barcodes')
                ->where('barcode', $barcode)
                ->where('reference_table', 'material_batches')
                ->first();

            if (!$barcodeRecord) {
                // إذا لم يوجد في جدول barcodes، نبحث مباشرة في material_batches.batch_code
                $batch = DB::table('material_batches')
                    ->join('materials', 'material_batches.material_id', '=', 'materials.id')
                    ->join('units', 'material_batches.unit_id', '=', 'units.id')
                    ->where('material_batches.batch_code', $barcode)
                    ->select(
                        'material_batches.*',
                        'materials.name_ar as material_name',
                        'units.unit_symbol'
                    )
                    ->first();
            } else {
                // إذا وُجد في جدول barcodes، نجلب البيانات باستخدام reference_id
                $batch = DB::table('material_batches')
                    ->join('materials', 'material_batches.material_id', '=', 'materials.id')
                    ->join('units', 'material_batches.unit_id', '=', 'units.id')
                    ->where('material_batches.id', $barcodeRecord->reference_id)
                    ->select(
                        'material_batches.*',
                        'materials.name_ar as material_name',
                        'units.unit_symbol'
                    )
                    ->first();
            }

            if (!$batch) {
                return response()->json([
                    'success' => false,
                    'message' => 'الباركود غير موجود في النظام'
                ], 404);
            }

            // حساب الكمية المنقولة للإنتاج (to_production)
            $transferredToProduction = DB::table('material_movements')
                ->where('batch_id', $batch->id)
                ->where('movement_type', 'to_production')
                ->sum('quantity');

            // التحقق من توفر كمية منقولة للإنتاج
            if ($transferredToProduction <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم نقل أي كمية من هذه المادة للإنتاج بعد. يجب النقل للإنتاج أولاً من صفحة تسجيل البضاعة.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'material' => [
                    'id' => $batch->material_id,
                    'batch_id' => $batch->id,
                    'barcode' => $batch->batch_code,
                    'material_name' => $batch->material_name,
                    'material_type' => $batch->material_name,
                    'initial_quantity' => $batch->initial_quantity,
                    'available_quantity' => $batch->available_quantity,
                    'transferred_to_production' => $transferredToProduction,
                    'production_weight' => $transferredToProduction,
                    'remaining_weight' => $batch->available_quantity,
                    'unit_symbol' => $batch->unit_symbol,
                    'warehouse_id' => $batch->warehouse_id,
                    'unit_id' => $batch->unit_id,
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
}
