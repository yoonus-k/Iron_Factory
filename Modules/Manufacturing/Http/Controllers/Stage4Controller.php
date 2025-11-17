<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Stage4Controller extends Controller
{
    /**
     * عرض قائمة جميع الكراتين
     */
    public function index()
    {
        $boxes = DB::table('stage4_boxes')
            ->leftJoin('material_details', 'stage4_boxes.material_id', '=', 'material_details.id')
            ->leftJoin('materials', 'material_details.material_id', '=', 'materials.id')
            ->select(
                'stage4_boxes.*',
                'materials.name_ar as material_name_ar',
                'materials.name_en as material_name_en'
            )
            ->orderBy('stage4_boxes.created_at', 'desc')
            ->get();

        return view('manufacturing::stages.stage4.index', compact('boxes'));
    }

    /**
     * عرض صفحة إنشاء كرتون جديد
     */
    public function create()
    {
        return view('manufacturing::stages.stage4.create');
    }

    /**
     * الحصول على بيانات اللفاف بواسطة الباركود
     */
    public function getByBarcode($barcode)
    {
        $lafaf = DB::table('stage3_coils')
            ->leftJoin('stage2_processed', 'stage3_coils.stage2_id', '=', 'stage2_processed.id')
            ->leftJoin('stage1_stands', 'stage3_coils.stage1_id', '=', 'stage1_stands.id')
            ->leftJoin('material_details', 'stage3_coils.material_id', '=', 'material_details.id')
            ->leftJoin('materials', 'material_details.material_id', '=', 'materials.id')
            ->where('stage3_coils.barcode', $barcode)
            ->select(
                'stage3_coils.*',
                'stage2_processed.barcode as stage2_barcode',
                'stage1_stands.barcode as stage1_barcode',
                'materials.name_ar as material_name_ar',
                'materials.name_en as material_name_en'
            )
            ->first();

        if (!$lafaf) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على باركود اللفاف'
            ], 404);
        }

        // التحقق من أن اللفاف في حالة نشطة
        if ($lafaf->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'حالة اللفاف غير صالحة للتعبئة'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $lafaf
        ]);
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

            // التحقق من أن مجموع أوزان الكراتين يساوي وزن اللفاف تقريباً
            $lafafWeight = $lafaf->total_weight;
            $difference = abs($lafafWeight - $totalBoxesWeight);
            $tolerance = $lafafWeight * 0.02; // تسامح 2%

            if ($difference > $tolerance) {
                throw new \Exception("مجموع أوزان الكراتين ({$totalBoxesWeight} كجم) لا يساوي وزن اللفاف ({$lafafWeight} كجم)");
            }

            $boxIds = [];
            $boxBarcodes = [];

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
                    'total_weight' => $totalBoxesWeight
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
        $box = DB::table('stage4_boxes')
            ->leftJoin('material_details', 'stage4_boxes.material_id', '=', 'material_details.id')
            ->leftJoin('materials', 'material_details.material_id', '=', 'materials.id')
            ->where('stage4_boxes.id', $id)
            ->select(
                'stage4_boxes.*',
                'materials.name_ar as material_name_ar',
                'materials.name_en as material_name_en'
            )
            ->first();

        if (!$box) {
            abort(404, 'الكرتون غير موجود');
        }

        // الحصول على اللفائف المرتبطة
        $coils = DB::table('box_coils')
            ->join('stage3_coils', 'box_coils.coil_id', '=', 'stage3_coils.id')
            ->where('box_coils.box_id', $id)
            ->select('stage3_coils.*', 'box_coils.weight as box_weight')
            ->get();

        return view('manufacturing::stages.stage4.show', compact('box', 'coils'));
    }
}
