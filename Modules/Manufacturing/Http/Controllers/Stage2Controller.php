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
     */
    public function index()
    {
        // جلب جميع المعالجات من المرحلة الثانية مع البيانات المرتبطة
        $processed = DB::table('stage2_processed')
            ->leftJoin('stage1_stands', 'stage2_processed.stage1_id', '=', 'stage1_stands.id')
            ->leftJoin('materials', 'stage2_processed.material_id', '=', 'materials.id')
            ->leftJoin('users', 'stage2_processed.created_by', '=', 'users.id')
            ->select(
                'stage2_processed.*',
                'stage1_stands.stand_number',
                'stage1_stands.barcode as stage1_barcode',
                'materials.name_ar as material_name',
                'users.name as created_by_name'
            )
            ->orderBy('stage2_processed.created_at', 'desc')
            ->paginate(20);

        return view('manufacturing::stages.stage2.index', compact('processed'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturing::stages.stage2.create');
    }

    /**
     * Get Stage1 data by barcode
     */
    public function getByBarcode($barcode)
    {
        try {
            $stage1Data = DB::table('stage1_stands')
                ->where('barcode', $barcode)
                ->first();

            if (!$stage1Data) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على بيانات بهذا الباركود'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $stage1Data
            ]);
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
            'stage1_id' => 'required|integer',
            'stage1_barcode' => 'required|string',
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
            
            // جلب بيانات المرحلة الأولى
            $stage1Data = DB::table('stage1_stands')
                ->where('id', $validated['stage1_id'])
                ->first();

            if (!$stage1Data) {
                throw new \Exception('لم يتم العثور على بيانات المرحلة الأولى');
            }
            
            // حساب الأوزان إذا لم يتم إرسالها (بسبب الصلاحيات)
            $inputWeight = $stage1Data->remaining_weight;
            $wasteWeight = $validated['waste_weight'] ?? ($inputWeight * 0.03); // افتراض 3% هدر
            $outputWeight = $validated['total_weight'] ?? ($inputWeight - $wasteWeight);
            $netWeight = $validated['net_weight'] ?? $outputWeight;

            // توليد باركود المرحلة الثانية
            $stage2Barcode = $this->generateStageBarcode('stage2');

            // حفظ في جدول stage2_processed
            $stage2Id = DB::table('stage2_processed')->insertGetId([
                'barcode' => $stage2Barcode,
                'parent_barcode' => $validated['stage1_barcode'],
                'stage1_id' => $validated['stage1_id'],
                'material_id' => $stage1Data->material_id ?? null,
                'wire_size' => $stage1Data->wire_size ?? null,
                'input_weight' => $inputWeight,
                'output_weight' => $outputWeight,
                'waste' => $wasteWeight,
                'remaining_weight' => $netWeight,
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
                'input_weight' => $inputWeight,
                'output_weight' => $netWeight,
                'waste_amount' => $wasteWeight,
                'waste_percentage' => $inputWeight > 0 ? 
                    ($wasteWeight / $inputWeight * 100) : 0,
                'worker_id' => $userId,
                'shift_id' => null,
                'notes' => $validated['notes'],
                'metadata' => json_encode([
                    'stage1_id' => $validated['stage1_id'],
                    'stage1_barcode' => $validated['stage1_barcode'],
                    'material_id' => $stage1Data->material_id,
                    'wire_size' => $stage1Data->wire_size,
                    'process_type' => $validated['process_type'] ?? null,
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
                'message' => 'تم حفظ المعالجة بنجاح!',
                'data' => [
                    'stage2_id' => $stage2Id,
                    'stage2_barcode' => $stage2Barcode,
                    'stand_number' => $stage1Data->stand_number ?? 'غير محدد',
                    'net_weight' => $netWeight,
                    'material_name' => $materialName,
                    'waste_weight' => $validated['waste_weight'] ?? 0,
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
        return view('manufacturing::stages.stage2.show');
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
