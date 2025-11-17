<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Stage3Controller extends Controller
{
    /**
     * عرض قائمة جميع اللفائف
     */
    public function index()
    {
        $lafafs = DB::table('stage3_coils')
            ->leftJoin('stage2_processed', 'stage3_coils.stage2_id', '=', 'stage2_processed.id')
            ->leftJoin('stage1_stands', 'stage3_coils.stage1_id', '=', 'stage1_stands.id')
            ->leftJoin('material_details', 'stage3_coils.material_id', '=', 'material_details.id')
            ->leftJoin('materials', 'material_details.material_id', '=', 'materials.id')
            ->select(
                'stage3_coils.*',
                'stage2_processed.barcode as stage2_barcode',
                'stage1_stands.barcode as stage1_barcode',
                'materials.name_ar as material_name_ar',
                'materials.name_en as material_name_en'
            )
            ->orderBy('stage3_coils.created_at', 'desc')
            ->get();

        return view('manufacturing::stages.stage3.index', compact('lafafs'));
    }

    /**
     * عرض صفحة إنشاء لفاف جديد (المرحلة الثالثة)
     */
    public function create()
    {
        return view('manufacturing::stages.stage3.create');
    }

    /**
     * الحصول على بيانات المرحلة الثانية بواسطة الباركود
     */
    public function getByBarcode($barcode)
    {
        $stage2 = DB::table('stage2_processed')
            ->leftJoin('stage1_stands', 'stage2_processed.stage1_id', '=', 'stage1_stands.id')
            ->leftJoin('material_details', 'stage2_processed.material_id', '=', 'material_details.id')
            ->leftJoin('materials', 'material_details.material_id', '=', 'materials.id')
            ->where('stage2_processed.barcode', $barcode)
            ->select(
                'stage2_processed.*',
                'stage1_stands.barcode as stage1_barcode',
                'stage1_stands.stand_number',
                'materials.name_ar as material_name_ar',
                'materials.name_en as material_name_en',
                'material_details.unit_id'
            )
            ->first();

        if (!$stage2) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على باركود المرحلة الثانية'
            ], 404);
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
            'data' => $stage2
        ]);
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
            DB::beginTransaction();

            // الحصول على بيانات المرحلة الثانية
            $stage2 = DB::table('stage2_processed')
                ->where('barcode', $request->stage2_barcode)
                ->first();

            if (!$stage2) {
                throw new \Exception('باركود المرحلة الثانية غير موجود');
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

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ اللفاف بنجاح',
                'data' => [
                    'id' => $lafafId,
                    'barcode' => $barcode,
                    'input_weight' => $inputWeight,
                    'total_weight' => $totalWeight,
                    'added_weight' => $addedWeight
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
        $lafaf = DB::table('stage3_coils')
            ->leftJoin('stage2_processed', 'stage3_coils.stage2_id', '=', 'stage2_processed.id')
            ->leftJoin('stage1_stands', 'stage3_coils.stage1_id', '=', 'stage1_stands.id')
            ->leftJoin('material_details', 'stage3_coils.material_id', '=', 'material_details.id')
            ->leftJoin('materials', 'material_details.material_id', '=', 'materials.id')
            ->where('stage3_coils.id', $id)
            ->select(
                'stage3_coils.*',
                'stage2_processed.barcode as stage2_barcode',
                'stage1_stands.barcode as stage1_barcode',
                'materials.name_ar as material_name_ar',
                'materials.name_en as material_name_en'
            )
            ->first();

        if (!$lafaf) {
            abort(404, 'اللفاف غير موجود');
        }

        return view('manufacturing::stages.stage3.show', compact('lafaf'));
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
}
