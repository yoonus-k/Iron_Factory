<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Stand;
use App\Models\StandUsageHistory;

class Stage1Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manufacturing::stages.stage1.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturing::stages.stage1.create');
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
                    'status' => 'created',
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
        return view('manufacturing::stages.stage1.show');
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
