<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ProductTrackingController extends Controller
{
    /**
     * Display the barcode scan page
     */
    public function scan()
    {
        return view('manufacturing::quality.production-tracking-scan');
    }

    /**
     * Process barcode and redirect to report
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|string',
        ]);

        $barcode = $validated['barcode'];
        
        // التحقق من وجود الباركود
        $exists = DB::table('product_tracking')
            ->where('barcode', $barcode)
            ->orWhere('input_barcode', $barcode)
            ->orWhere('output_barcode', $barcode)
            ->exists();
        
        if (!$exists) {
            return redirect()->route('manufacturing.production-tracking.scan')
                ->with('error', 'لم يتم العثور على بيانات لهذا الباركود: ' . $barcode);
        }

        return redirect()->route('manufacturing.production-tracking.report', ['barcode' => $barcode]);
    }

    /**
     * Display tracking report
     */
    public function report(Request $request)
    {
        $barcode = $request->get('barcode');
        
        if (!$barcode) {
            return redirect()->route('manufacturing.production-tracking.scan')
                ->with('error', 'يرجى إدخال باركود صحيح');
        }

        // جلب بيانات التتبع الحقيقية
        $trackingData = $this->getTrackingData($barcode);
        
        if (!$trackingData) {
            return redirect()->route('manufacturing.production-tracking.scan')
                ->with('error', 'لم يتم العثور على بيانات لهذا الباركود: ' . $barcode);
        }

        // جلب التتبع العكسي (من أين جاء هذا الباركود)
        $reverseTracking = $this->getReverseTracking($barcode);
        
        // جلب التتبع الأمامي (ماذا تم إنتاجه من هذا الباركود)
        $forwardTracking = $this->getForwardTracking($barcode);

        return view('manufacturing::quality.production-tracking-report', [
            'trackingData' => $trackingData,
            'reverseTracking' => $reverseTracking,
            'forwardTracking' => $forwardTracking,
            'barcode' => $barcode
        ]);
    }

    /**
     * Get reverse tracking (من أين جاء هذا الباركود)
     */
    private function getReverseTracking($barcode)
    {
        $chain = [];
        $currentBarcode = $barcode;
        $maxDepth = 10; // لتجنب اللوبات اللانهائية
        $depth = 0;

        while ($depth < $maxDepth) {
            // البحث عن السجل الذي أنتج هذا الباركود
            $record = DB::table('product_tracking')
                ->where('output_barcode', $currentBarcode)
                ->orWhere('barcode', $currentBarcode)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$record || !$record->input_barcode) {
                break;
            }

            // إضافة معلومات المرحلة السابقة
            $chain[] = [
                'barcode' => $record->input_barcode,
                'stage' => $record->stage,
                'stage_name' => $this->getStageNameAr($record->stage),
                'action' => $record->action,
                'action_name' => $this->getActionNameAr($record->action),
                'weight' => $record->input_weight ?? 0,
                'timestamp' => $record->created_at,
                'formatted_time' => date('Y-m-d H:i:s', strtotime($record->created_at)),
            ];

            $currentBarcode = $record->input_barcode;
            $depth++;
        }

        return array_reverse($chain); // عكس الترتيب ليكون من الأول للأخير
    }

    /**
     * Get forward tracking (ماذا تم إنتاجه من هذا الباركود)
     */
    private function getForwardTracking($barcode)
    {
        $products = [];

        // البحث عن جميع المنتجات التي تم إنتاجها من هذا الباركود
        $records = DB::table('product_tracking')
            ->where('input_barcode', $barcode)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($records as $record) {
            $products[] = [
                'barcode' => $record->output_barcode ?? $record->barcode,
                'stage' => $record->stage,
                'stage_name' => $this->getStageNameAr($record->stage),
                'action' => $record->action,
                'action_name' => $this->getActionNameAr($record->action),
                'weight' => $record->output_weight ?? 0,
                'timestamp' => $record->created_at,
                'formatted_time' => date('Y-m-d H:i:s', strtotime($record->created_at)),
            ];
        }

        return $products;
    }

    /**
     * Get stage name in Arabic
     */
    private function getStageNameAr($stage)
    {
        $names = [
            'warehouse' => 'المستودع',
            'stage1' => 'المرحلة الأولى - التقسيم',
            'stage2' => 'المرحلة الثانية - المعالجة',
            'stage3' => 'المرحلة الثالثة - الملفات',
            'stage4' => 'المرحلة الرابعة - التعبئة',
        ];
        return $names[$stage] ?? $stage;
    }

    /**
     * Get action name in Arabic
     */
    private function getActionNameAr($action)
    {
        $names = [
            'received' => 'استلام',
            'created' => 'إنشاء',
            'processed' => 'معالجة',
            'moved' => 'نقل',
            'quality_check' => 'فحص جودة',
            'packed' => 'تعبئة',
            'shipped' => 'شحن',
            'split' => 'تقسيم',
        ];
        return $names[$action] ?? $action;
    }

    /**
     * Get real tracking data from database
     */
    private function getTrackingData($barcode)
    {
        // جلب جميع السجلات المرتبطة بهذا الباركود في السلسلة الكاملة
        $allBarcodes = $this->getAllRelatedBarcodes($barcode);
        
        if (empty($allBarcodes)) {
            return null;
        }

        // جلب سجلات التتبع لجميع الباركودات المرتبطة
        $trackingRecords = DB::table('product_tracking')
            ->whereIn('barcode', $allBarcodes)
            ->orWhereIn('input_barcode', $allBarcodes)
            ->orWhereIn('output_barcode', $allBarcodes)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($trackingRecords->isEmpty()) {
            return null;
        }

        // تحديد نوع الباركود
        $prefix = substr($barcode, 0, 3);
        
        $data = [
            'barcode' => $barcode,
            'type' => $this->getBarcodeType($prefix),
            'journey' => [],
            'summary' => [],
            'current_location' => null,
        ];

        // بناء رحلة المنتج (إزالة التكرار)
        // نجمع كل السجلات حسب المرحلة
        $stageRecords = [];
        
        // جمع كل السجلات حسب المرحلة
        foreach ($trackingRecords as $record) {
            if (!isset($stageRecords[$record->stage])) {
                $stageRecords[$record->stage] = [];
            }
            $stageRecords[$record->stage][] = $record;
        }
        
        // ترتيب المراحل حسب الترتيب المنطقي
        $stageOrder = ['warehouse', 'stage1', 'stage2', 'stage3', 'stage4'];
        foreach ($stageOrder as $stageName) {
            if (isset($stageRecords[$stageName])) {
                // دمج كل سجلات المرحلة في عرض واحد
                $stageData = $this->getAggregatedStageDetails($stageRecords[$stageName], $stageName);
                if ($stageData) {
                    $data['journey'][] = $stageData;
                }
            }
        }

        // حساب الملخص
        $data['summary'] = $this->calculateSummary($data['journey']);
        
        // جلب الموقع الحالي
        $data['current_location'] = $this->getCurrentLocation($barcode);

        // جلب تفاصيل إضافية حسب نوع الباركود
        $data['details'] = $this->getProductDetails($barcode, $data['type']);

        return $data;
    }

    /**
     * Get all related barcodes in the production chain
     */
    private function getAllRelatedBarcodes($barcode)
    {
        $barcodes = [$barcode];
        $queue = [$barcode];
        $processed = [];

        // البحث للأمام والخلف في السلسلة
        while (!empty($queue)) {
            $currentBarcode = array_shift($queue);
            
            if (in_array($currentBarcode, $processed)) {
                continue;
            }
            
            $processed[] = $currentBarcode;

            // البحث عن الباركودات المرتبطة
            $related = DB::table('product_tracking')
                ->where(function($query) use ($currentBarcode) {
                    $query->where('barcode', $currentBarcode)
                          ->orWhere('input_barcode', $currentBarcode)
                          ->orWhere('output_barcode', $currentBarcode);
                })
                ->get();

            foreach ($related as $record) {
                if ($record->input_barcode && !in_array($record->input_barcode, $barcodes)) {
                    $barcodes[] = $record->input_barcode;
                    $queue[] = $record->input_barcode;
                }
                if ($record->output_barcode && !in_array($record->output_barcode, $barcodes)) {
                    $barcodes[] = $record->output_barcode;
                    $queue[] = $record->output_barcode;
                }
                if ($record->barcode && !in_array($record->barcode, $barcodes)) {
                    $barcodes[] = $record->barcode;
                }
            }
        }

        return array_unique($barcodes);
    }

    /**
     * Get aggregated stage details from multiple records
     */
    private function getAggregatedStageDetails($records, $stageName)
    {
        if (empty($records)) {
            return null;
        }

        $stageNames = [
            'warehouse' => 'المستودع',
            'stage1' => 'المرحلة الأولى - التقسيم',
            'stage2' => 'المرحلة الثانية - المعالجة',
            'stage3' => 'المرحلة الثالثة - الملفات',
            'stage4' => 'المرحلة الرابعة - التعبئة',
        ];

        $stageIcons = [
            'warehouse' => 'warehouse',
            'stage1' => 'cut',
            'stage2' => 'cogs',
            'stage3' => 'brush',
            'stage4' => 'box',
        ];

        $stageColors = [
            'warehouse' => 'primary',
            'stage1' => 'info',
            'stage2' => 'success',
            'stage3' => 'warning',
            'stage4' => 'danger',
        ];

        $actionNames = [
            'received' => 'استلام المادة',
            'created' => 'إنشاء منتج جديد',
            'processed' => 'معالجة وتصنيع',
            'moved' => 'نقل بين المراحل',
            'quality_check' => 'فحص جودة',
            'packed' => 'تعبئة وتغليف',
            'shipped' => 'شحن',
        ];

        $itemsCount = count($records);
        $firstRecord = $records[0];
        
        // تفاصيل كل عنصر - نعرض كل عملية بشكل منفصل
        $itemsDetails = [];
        
        foreach ($records as $record) {
            // تحديد الأوزان الصحيحة حسب نوع العملية
            $displayInputWeight = 0;
            $displayOutputWeight = 0;
            
            // تحليل نوع العملية
            switch ($record->action) {
                case 'split':
                    // التقسيم: الدخول = الخروج (لا يوجد فقد)
                    $displayInputWeight = $record->input_weight ?? 0;
                    $displayOutputWeight = $record->output_weight ?? 0;
                    break;
                    
                case 'warehouse_remaining':
                case 'transferred_to_production':
                    // الناتج من التقسيم: نعرض فقط وزن الخروج (الوزن الفعلي للباركود)
                    $displayInputWeight = 0; // لا نعرض وزن دخول لأنه ناتج من تقسيم
                    $displayOutputWeight = $record->output_weight ?? 0;
                    break;
                    
                default:
                    // باقي العمليات: نعرض الدخول والخروج
                    $displayInputWeight = $record->input_weight ?? 0;
                    $displayOutputWeight = $record->output_weight ?? 0;
            }
            
            $itemsDetails[] = [
                'barcode' => $record->barcode,
                'input_barcode' => $record->input_barcode,
                'output_barcode' => $record->output_barcode,
                'action' => $record->action,
                'input_weight' => $displayInputWeight,
                'output_weight' => $displayOutputWeight,
                'waste_amount' => $record->waste_amount ?? 0,
                'waste_percentage' => $record->waste_percentage ?? 0,
                'notes' => $record->notes,
                'formatted_time' => date('Y-m-d H:i:s', strtotime($record->created_at)),
            ];
        }
        
        // للعرض الرئيسي: نستخدم بيانات أول سجل فقط (الأهم)
        $totalInputWeight = $firstRecord->input_weight ?? 0;
        $totalOutputWeight = $firstRecord->output_weight ?? 0;
        $totalWaste = $firstRecord->waste_amount ?? 0;

        // جلب اسم الموظف - نحاول من created_by أولاً، ثم worker_id
        $workerId = $firstRecord->created_by ?? $firstRecord->worker_id ?? null;
        $worker = null;
        if ($workerId) {
            $worker = DB::table('users')->where('id', $workerId)->first();
        }
        
        // حساب المدة من أول سجل
        $duration = $this->calculateDuration($firstRecord->created_at);

        // تحديد اسم الإجراء
        $actionName = $actionNames[$firstRecord->action] ?? $firstRecord->action;
        
        // معلومات إضافية
        $additionalInfo = [
            'items_details' => $itemsDetails,
        ];
        
        if ($itemsCount > 1) {
            $additionalInfo['items_count'] = $itemsCount;
        }

        // حساب متوسط نسبة الهدر
        $wastePercentage = $totalInputWeight > 0 
            ? round(($totalWaste / $totalInputWeight) * 100, 2)
            : 0;

        return [
            'stage' => $stageName,
            'stage_name' => $stageNames[$stageName] ?? $stageName,
            'action_name' => $actionName,
            'barcode' => $firstRecord->barcode,
            'input_barcode' => $firstRecord->input_barcode,
            'output_barcode' => $firstRecord->output_barcode,
            'action' => $firstRecord->action,
            'input_weight' => $totalInputWeight,
            'output_weight' => $totalOutputWeight,
            'waste_amount' => $totalWaste,
            'waste_percentage' => $wastePercentage,
            'worker_name' => $worker ? $worker->name : 'غير محدد',
            'notes' => $firstRecord->notes,
            'metadata' => json_decode($firstRecord->metadata, true) ?? [],
            'timestamp' => $firstRecord->created_at,
            'formatted_time' => date('Y-m-d H:i:s', strtotime($firstRecord->created_at)),
            'time_ago' => $this->timeAgo($firstRecord->created_at),
            'duration' => $duration,
            'icon' => $stageIcons[$stageName] ?? 'circle',
            'color' => $stageColors[$stageName] ?? 'secondary',
            'additional_info' => $additionalInfo,
            'items_count' => $itemsCount,
        ];
    }

    /**
     * Get barcode type from prefix
     */
    private function getBarcodeType($prefix)
    {
        $types = [
            'WH-' => 'warehouse',
            'RW-' => 'raw_material',
            'ST1' => 'stage1',
            'ST2' => 'stage2',
            'CO3' => 'stage3',
            'BOX' => 'stage4',
        ];

        return $types[$prefix] ?? 'unknown';
    }

    /**
     * Get stage details from tracking record
     */
    private function getStageDetails($record)
    {
        $stageNames = [
            'warehouse' => 'المستودع',
            'stage1' => 'المرحلة الأولى - التقسيم',
            'stage2' => 'المرحلة الثانية - المعالجة',
            'stage3' => 'المرحلة الثالثة - الملفات',
            'stage4' => 'المرحلة الرابعة - التعبئة',
        ];

        $stageIcons = [
            'warehouse' => 'warehouse',
            'stage1' => 'cut',
            'stage2' => 'cogs',
            'stage3' => 'brush',
            'stage4' => 'box',
        ];

        $stageColors = [
            'warehouse' => 'primary',
            'stage1' => 'info',
            'stage2' => 'success',
            'stage3' => 'warning',
            'stage4' => 'danger',
        ];

        $actionNames = [
            'received' => 'استلام المادة',
            'created' => 'إنشاء منتج جديد',
            'processed' => 'معالجة وتصنيع',
            'moved' => 'نقل بين المراحل',
            'quality_check' => 'فحص جودة',
            'packed' => 'تعبئة وتغليف',
            'shipped' => 'شحن',
        ];

        // جلب اسم الموظف - نحاول من created_by أولاً، ثم worker_id
        $workerId = $record->created_by ?? $record->worker_id ?? null;
        $worker = null;
        if ($workerId) {
            $worker = DB::table('users')->where('id', $workerId)->first();
        }
        
        // حساب المدة
        $duration = $this->calculateDuration($record->created_at);

        // تحديد اسم الإجراء
        $actionName = $actionNames[$record->action] ?? $record->action;
        
        // إذا كانت المرحلة الرابعة، نضيف تفاصيل الكراتين
        $additionalInfo = [];
        if ($record->stage == 'stage4' && $record->input_barcode) {
            // عدد الكراتين من نفس اللفاف
            $boxesCount = DB::table('stage4_boxes')
                ->where('parent_barcode', $record->input_barcode)
                ->count();
            if ($boxesCount > 0) {
                $additionalInfo['boxes_count'] = $boxesCount;
                $additionalInfo['boxes_info'] = "تم تقسيم اللفاف إلى {$boxesCount} كرتون";
            }
        }

        return [
            'stage' => $record->stage,
            'stage_name' => $stageNames[$record->stage] ?? $record->stage,
            'action_name' => $actionName,
            'barcode' => $record->barcode,
            'input_barcode' => $record->input_barcode,
            'output_barcode' => $record->output_barcode,
            'action' => $record->action,
            'input_weight' => $record->input_weight ?? 0,
            'output_weight' => $record->output_weight ?? 0,
            'waste_amount' => $record->waste_amount ?? 0,
            'waste_percentage' => $record->waste_percentage ?? 0,
            'worker_name' => $worker ? $worker->name : 'غير محدد',
            'notes' => $record->notes,
            'metadata' => json_decode($record->metadata, true) ?? [],
            'timestamp' => $record->created_at,
            'formatted_time' => date('Y-m-d H:i:s', strtotime($record->created_at)),
            'time_ago' => $this->timeAgo($record->created_at),
            'duration' => $duration,
            'icon' => $stageIcons[$record->stage] ?? 'circle',
            'color' => $stageColors[$record->stage] ?? 'secondary',
            'additional_info' => $additionalInfo,
        ];
    }

    /**
     * Calculate summary statistics
     */
    private function calculateSummary($journey)
    {
        if (empty($journey)) {
            return [
                'total_input' => 0,
                'total_output' => 0,
                'total_waste' => 0,
                'waste_percentage' => 0,
                'duration_days' => 0,
                'duration_hours' => 0,
                'duration_minutes' => 0,
                'total_minutes' => 0,
                'total_hours' => 0,
                'stages_count' => 0,
                'efficiency' => 0,
            ];
        }

        $totalWaste = array_sum(array_column($journey, 'waste_amount'));
        $firstStage = $journey[0];
        $lastStage = end($journey);
        
        // حساب المدة الإجمالية من بداية أول مرحلة حتى الآن
        $startTime = new \DateTime($firstStage['timestamp']);
        $now = new \DateTime();
        $duration = $startTime->diff($now);

        $totalMinutes = ($duration->days * 24 * 60) + ($duration->h * 60) + $duration->i;
        
        // الوزن الأولي (من أول مرحلة)
        $totalInput = $firstStage['input_weight'] > 0 
            ? $firstStage['input_weight'] 
            : ($firstStage['output_weight'] > 0 ? $firstStage['output_weight'] : 0);
        
        // الوزن النهائي (من آخر مرحلة)
        $totalOutput = $lastStage['output_weight'] ?? 0;

        return [
            'total_input' => $totalInput,
            'total_output' => $totalOutput,
            'total_waste' => $totalWaste,
            'waste_percentage' => $totalInput > 0 
                ? round(($totalWaste / $totalInput) * 100, 2)
                : 0,
            // كفاءة الإنتاج = 100% - نسبة الهدر
            // هذا يعمل بشكل صحيح حتى إذا زاد الوزن (مثل المرحلة الثالثة)
            'efficiency' => $totalInput > 0 && $totalWaste >= 0
                ? round(100 - (($totalWaste / $totalInput) * 100), 2)
                : 100,
            'duration_days' => $duration->days,
            'duration_hours' => $duration->h,
            'duration_minutes' => $duration->i,
            'total_minutes' => $totalMinutes,
            'total_hours' => round($totalMinutes / 60, 1),
            'stages_count' => count($journey),
            'formatted_duration' => $this->formatDuration($duration),
        ];
    }

    /**
     * Format duration nicely
     */
    private function formatDuration($duration)
    {
        $parts = [];
        
        if ($duration->days > 0) {
            $parts[] = $duration->days . ' يوم';
        }
        if ($duration->h > 0) {
            $parts[] = $duration->h . ' ساعة';
        }
        if ($duration->i > 0) {
            $parts[] = $duration->i . ' دقيقة';
        }
        
        if (empty($parts)) {
            return 'أقل من دقيقة';
        }
        
        return implode(' و ', $parts);
    }

    /**
     * Get current location of barcode
     */
    private function getCurrentLocation($barcode)
    {
        $latest = DB::table('product_tracking')
            ->where('barcode', $barcode)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latest) {
            return null;
        }

        $stageNames = [
            'warehouse' => 'المستودع',
            'stage1' => 'المرحلة الأولى (الاستاندات)',
            'stage2' => 'المرحلة الثانية (المعالجة)',
            'stage3' => 'المرحلة الثالثة (الملفات)',
            'stage4' => 'المرحلة الرابعة (التعبئة)',
        ];

        $actionNames = [
            'received' => 'تم الاستلام',
            'created' => 'تم الإنشاء',
            'processed' => 'تمت المعالجة',
            'moved' => 'تم النقل',
            'quality_check' => 'فحص الجودة',
            'packed' => 'تم التعبئة',
            'shipped' => 'تم الشحن',
        ];

        return [
            'stage' => $stageNames[$latest->stage] ?? $latest->stage,
            'action' => $actionNames[$latest->action] ?? $latest->action,
            'timestamp' => $latest->created_at,
            'formatted_time' => date('Y-m-d H:i:s', strtotime($latest->created_at)),
            'time_ago' => $this->timeAgo($latest->created_at),
        ];
    }

    /**
     * Get product details based on type
     */
    private function getProductDetails($barcode, $type)
    {
        $details = null;

        switch ($type) {
            case 'stage1':
                $details = DB::table('stage1_stands')->where('barcode', $barcode)->first();
                break;
            case 'stage2':
                $details = DB::table('stage2_processed')->where('barcode', $barcode)->first();
                break;
            case 'stage3':
                $details = DB::table('stage3_coils')->where('barcode', $barcode)->first();
                break;
            case 'stage4':
                $details = DB::table('stage4_boxes')->where('barcode', $barcode)->first();
                break;
        }

        return $details;
    }

    /**
     * Calculate duration from timestamp (منذ متى تم)
     */
    private function calculateDuration($timestamp)
    {
        $time = new \DateTime($timestamp);
        $now = new \DateTime();
        $diff = $time->diff($now);

        $parts = [];
        if ($diff->days > 0) $parts[] = $diff->days . ' يوم';
        if ($diff->h > 0) $parts[] = $diff->h . ' ساعة';
        if ($diff->i > 0) $parts[] = $diff->i . ' دقيقة';

        return !empty($parts) ? 'منذ ' . implode(' و ', $parts) : 'منذ لحظات';
    }

    /**
     * Calculate time ago
     */
    private function timeAgo($timestamp)
    {
        $time = strtotime($timestamp);
        $now = time();
        $diff = $now - $time;

        if ($diff < 60) {
            return 'منذ لحظات';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return "منذ {$mins} دقيقة";
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return "منذ {$hours} ساعة";
        } else {
            $days = floor($diff / 86400);
            return "منذ {$days} يوم";
        }
    }

    /**
     * Get chart data for visualization
     */
    public function getChartData($barcode)
    {
        $trackingData = $this->getTrackingData($barcode);
        
        if (!$trackingData) {
            return response()->json(['error' => 'No data found'], 404);
        }

        // إعداد بيانات الرسم البياني
        $stages = [];
        $weights = [];
        $waste = [];

        foreach ($trackingData['journey'] as $stage) {
            $stages[] = $stage['stage_name'];
            $weights[] = $stage['output_weight'];
            $waste[] = $stage['waste_amount'];
        }

        return response()->json([
            'stages' => $stages,
            'weights' => $weights,
            'waste' => $waste,
            'summary' => $trackingData['summary'],
        ]);
    }
}
