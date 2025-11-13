<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Manufacturing\Services\BarcodeService;
use App\Models\Barcode;
use App\Models\BarcodeSetting;
use Exception;

class BarcodeController extends Controller
{
    /**
     * عرض صفحة إعدادات الباركود
     */
    public function index()
    {
        $settings = BarcodeSetting::all();
        $statistics = BarcodeService::getStatistics();
        
        return view('manufacturing::barcode.index', compact('settings', 'statistics'));
    }

    /**
     * API: البحث عن باركود
     * GET /api/barcode/scan/{barcode}
     */
    public function scan(string $barcode): JsonResponse
    {
        try {
            $result = BarcodeService::lookup($barcode);
            
            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'الباركود غير موجود',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: توليد باركود جديد
     * POST /api/barcode/generate
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:raw_material,stage1,stage2,stage3,stage4',
            'reference_id' => 'nullable|integer',
            'reference_table' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        try {
            $barcode = BarcodeService::generate(
                $request->type,
                $request->reference_id,
                $request->reference_table,
                $request->metadata
            );

            return response()->json([
                'success' => true,
                'barcode' => $barcode,
                'message' => 'تم توليد الباركود بنجاح',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في توليد الباركود: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: الحصول على تاريخ منتج
     * GET /api/barcode/history/{barcode}
     */
    public function history(string $barcode): JsonResponse
    {
        try {
            $history = BarcodeService::getHistory($barcode);
            
            return response()->json([
                'success' => true,
                'data' => $history,
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: التتبع العكسي
     * GET /api/barcode/trace/{barcode}
     */
    public function trace(string $barcode): JsonResponse
    {
        try {
            $trace = BarcodeService::traceBack($barcode);
            
            return response()->json([
                'success' => true,
                'data' => $trace,
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: تقرير شامل
     * GET /api/barcode/report/{barcode}
     */
    public function report(string $barcode): JsonResponse
    {
        try {
            $report = BarcodeService::fullReport($barcode);
            
            return response()->json([
                'success' => true,
                'data' => $report,
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: تحديث حالة الباركود
     * PUT /api/barcode/status/{barcode}
     */
    public function updateStatus(Request $request, string $barcode): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:active,scanned,used,expired',
        ]);

        try {
            $success = BarcodeService::updateStatus($barcode, $request->status);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'الباركود غير موجود',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الحالة بنجاح',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: إحصائيات الباركود
     * GET /api/barcode/statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $type = $request->query('type');
            $statistics = BarcodeService::getStatistics($type);
            
            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تحديث إعدادات الباركود
     * PUT /barcode/settings/{id}
     */
    public function updateSettings(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'prefix' => 'required|string|max:10',
            'format' => 'required|string',
            'padding' => 'required|integer|min:1|max:10',
            'auto_increment' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        try {
            $setting = BarcodeSetting::findOrFail($id);
            $setting->update($request->only([
                'prefix',
                'format',
                'padding',
                'auto_increment',
                'is_active',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الإعدادات بنجاح',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * إضافة إعدادات باركود جديدة
     * POST /barcode/settings/store
     */
    public function storeSettings(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|unique:barcode_settings,type',
            'prefix' => 'required|string|max:10',
            'format' => 'required|string',
            'padding' => 'required|integer|min:1|max:10',
            'auto_increment' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        try {
            BarcodeSetting::create([
                'type' => $request->type,
                'prefix' => $request->prefix,
                'current_number' => 0,
                'year' => date('Y'),
                'format' => $request->format,
                'auto_increment' => $request->auto_increment,
                'padding' => $request->padding,
                'is_active' => $request->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة إعدادات الباركود بنجاح',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * طباعة الباركود
     * GET /api/barcode/print/{barcode}
     */
    public function print(string $barcode, Request $request): JsonResponse
    {
        try {
            $format = $request->query('format', 'svg');
            $output = BarcodeService::print($barcode, $format);
            
            return response()->json([
                'success' => true,
                'barcode' => $barcode,
                'format' => $format,
                'output' => $output,
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * إعادة تعيين الأرقام للسنة الجديدة
     * POST /barcode/reset-year
     */
    public function resetYear(): JsonResponse
    {
        try {
            BarcodeService::resetForNewYear();
            
            return response()->json([
                'success' => true,
                'message' => 'تم إعادة تعيين الأرقام للسنة الجديدة',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }
}
