<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class QualityController extends Controller
{
    /**
     * Display the quality dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('manufacturing::quality.index');
    }

    /**
     * Display the waste report.
     *
     * @return \Illuminate\Http\Response
     */
    public function wasteReport()
    {
        return view('manufacturing::quality.waste-report');
    }

    /**
     * Display the quality monitoring page.
     *
     * @return \Illuminate\Http\Response
     */
    public function qualityMonitoring()
    {
        return view('manufacturing::quality.quality-monitoring');
    }

    /**
     * Display the downtime tracking page.
     *
     * @return \Illuminate\Http\Response
     */
    public function downtimeTracking()
    {
        return view('manufacturing::quality.downtime-tracking');
    }

    /**
     * Display the waste limits configuration page.
     *
     * @return \Illuminate\Http\Response
     */
    public function wasteLimits()
    {
        return view('manufacturing::quality.waste-limits');
    }

    /**
     * Show the form for creating a new quality check.
     *
     * @return \Illuminate\Http\Response
     */
    public function qualityCreate()
    {
        return view('manufacturing::quality.quality-create');
    }

    /**
     * Display the specified quality check.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function qualityShow($id)
    {
        // In a real application, you would retrieve the quality check from the database
        return view('manufacturing::quality.quality-show', compact('id'));
    }

    /**
     * Show the form for editing the specified quality check.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function qualityEdit($id)
    {
        // In a real application, you would retrieve the quality check from the database
        return view('manufacturing::quality.quality-edit', compact('id'));
    }

    /**
     * Show the form for creating a new downtime record.
     *
     * @return \Illuminate\Http\Response
     */
    public function downtimeCreate()
    {
        return view('manufacturing::quality.downtime-create');
    }

    /**
     * Display the specified downtime record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downtimeShow($id)
    {
        // In a real application, you would retrieve the downtime record from the database
        return view('manufacturing::quality.downtime-show', compact('id'));
    }

    /**
     * Show the form for editing the specified downtime record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downtimeEdit($id)
    {
        // In a real application, you would retrieve the downtime record from the database
        return view('manufacturing::quality.downtime-edit', compact('id'));
    }

    /**
     * Show the production tracking scan page.
     *
     * @return \Illuminate\Http\Response
     */
    public function productionTrackingScan()
    {
        return view('manufacturing::quality.production-tracking-scan');
    }

    /**
     * Process the production tracking barcode scan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processProductionTracking(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|string',
        ]);

        // In a real application, you would process the barcode and retrieve the production data
        // For now, we'll just redirect to the report page with the barcode
        return redirect()->route('manufacturing.production-tracking.report', ['barcode' => $validated['barcode']]);
    }

    /**
     * Display the production tracking report.
     *
     * @param  string  $barcode
     * @return \Illuminate\Http\Response
     */
    public function productionTrackingReport()
    {
        // In a real application, you would retrieve all production data related to this barcode
        // This is sample data for demonstration purposes


        return view('manufacturing::quality.production-tracking-report');
    }

    /**
     * Display the Iron Journey tracking page.
     *
     * @return \Illuminate\Http\Response
     */
    public function ironJourney(Request $request)
    {
        $barcode = $request->get('barcode');
        
        if (!$barcode) {
            return view('manufacturing::quality.iron-journey');
        }

        return redirect()->route('manufacturing.iron-journey.show', ['barcode' => $barcode]);
    }

    /**
     * Show the Iron Journey for a specific barcode.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showIronJourney(Request $request)
    {
        $barcode = $request->get('barcode');
        
        if (!$barcode) {
            return redirect()->route('manufacturing.iron-journey');
        }

        // Sample data for demonstration - In production, fetch from database
        $journeyData = $this->generateSampleJourneyData($barcode);

        return view('manufacturing::quality.iron-journey', compact('journeyData'));
    }

    /**
     * Generate sample journey data for demonstration.
     *
     * @param  string  $barcode
     * @return array
     */
    private function generateSampleJourneyData($barcode)
    {
        // Determine the stage based on barcode prefix
        $prefix = substr($barcode, 0, 3);
        
        return [
            'searchedBarcode' => $barcode,
            'productType' => 'سلك حديد ملون',
            'currentStatus' => 'مكتمل',
            'progressPercentage' => 100,
            'journey' => [
                [
                    'stage' => 0,
                    'name' => 'المستودع',
                    'barcode' => 'WH-001-2025',
                    'icon' => 'warehouse',
                    'status' => 'completed',
                    'timestamp' => '2025-11-10 08:00:00',
                    'duration' => '2 ساعة 30 دقيقة',
                    'input' => ['weight' => 0, 'unit' => 'kg'],
                    'output' => ['weight' => 1000, 'unit' => 'kg'],
                    'waste' => ['amount' => 0, 'percentage' => 0, 'reason' => ''],
                    'worker' => [
                        'id' => 1,
                        'name' => 'أحمد علي',
                        'role' => 'أمين مستودع',
                        'performance' => 95
                    ],
                    'supervisor' => 'محمد حسن',
                    'materials' => [
                        ['type' => 'سلك خام', 'weight' => 1000, 'supplier' => 'شركة الحديد المتحدة']
                    ],
                    'quality' => 'A+',
                    'notes' => 'المواد الخام وصلت بحالة ممتازة، لا توجد مشاكل في الاستلام',
                ],
                [
                    'stage' => 1,
                    'name' => 'التقسيم والاستاندات',
                    'barcode' => 'ST1-001-2025',
                    'icon' => 'cut',
                    'status' => 'completed',
                    'timestamp' => '2025-11-10 11:00:00',
                    'duration' => '3 ساعات 15 دقيقة',
                    'input' => ['weight' => 1000, 'unit' => 'kg'],
                    'output' => ['weight' => 980, 'unit' => 'kg'],
                    'waste' => [
                        'amount' => 20,
                        'percentage' => 2.0,
                        'reason' => 'هدر طبيعي أثناء التقطيع',
                        'approved' => true
                    ],
                    'worker' => [
                        'id' => 2,
                        'name' => 'حسن محمد',
                        'role' => 'عامل تقسيم',
                        'performance' => 88
                    ],
                    'details' => [
                        'wireSize' => '2.5 مم',
                        'standNumber' => 'ST-A-001'
                    ],
                    'quality' => 'A',
                    'notes' => 'عملية التقسيم تمت بشكل قياسي',
                ],
                [
                    'stage' => 2,
                    'name' => 'المعالجة',
                    'barcode' => 'ST2-001-2025',
                    'icon' => 'cogs',
                    'status' => 'completed',
                    'timestamp' => '2025-11-11 09:00:00',
                    'duration' => '4 ساعات',
                    'input' => ['weight' => 980, 'unit' => 'kg'],
                    'output' => ['weight' => 940, 'unit' => 'kg'],
                    'waste' => [
                        'amount' => 40,
                        'percentage' => 4.1,
                        'reason' => 'فقد أثناء المعالجة الحرارية',
                        'approved' => true,
                        'exceededLimit' => true
                    ],
                    'worker' => [
                        'id' => 3,
                        'name' => 'علي عبدالله',
                        'role' => 'عامل معالجة',
                        'performance' => 78
                    ],
                    'quality' => 'B+',
                    'notes' => 'تأخير بسيط بسبب معايرة الآلة',
                    'issues' => ['الهدر أعلى من الحد الطبيعي']
                ],
                [
                    'stage' => 3,
                    'name' => 'تصنيع الكويلات',
                    'barcode' => 'CO3-001-2025',
                    'icon' => 'coil',
                    'status' => 'completed',
                    'timestamp' => '2025-11-12 08:00:00',
                    'duration' => '5 ساعات 30 دقيقة',
                    'input' => ['weight' => 940, 'unit' => 'kg'],
                    'output' => ['weight' => 920, 'unit' => 'kg'],
                    'waste' => [
                        'amount' => 20,
                        'percentage' => 2.1,
                        'reason' => 'هدر أثناء وضع الصبغة والبلاستيك'
                    ],
                    'worker' => [
                        'id' => 4,
                        'name' => 'عمر صالح',
                        'role' => 'عامل تصنيع كويلات',
                        'performance' => 92
                    ],
                    'additionalMaterials' => [
                        ['type' => 'صبغة حمراء', 'weight' => 8, 'cost' => 80],
                        ['type' => 'طلاء بلاستيك', 'weight' => 12, 'cost' => 60]
                    ],
                    'details' => [
                        'coilCount' => 36,
                        'color' => 'أحمر',
                        'wireSize' => '2.5 مم'
                    ],
                    'quality' => 'A',
                    'notes' => 'اتساق ممتاز في اللون'
                ],
                [
                    'stage' => 4,
                    'name' => 'التعبئة والتغليف',
                    'barcode' => 'BOX4-001-2025',
                    'icon' => 'box',
                    'status' => 'completed',
                    'timestamp' => '2025-11-13 10:00:00',
                    'duration' => '2 ساعة',
                    'input' => ['coils' => 36, 'weight' => 920, 'unit' => 'kg'],
                    'output' => ['boxes' => 3, 'weight' => 920, 'unit' => 'kg'],
                    'waste' => ['amount' => 0, 'percentage' => 0],
                    'worker' => [
                        'id' => 5,
                        'name' => 'خالد يوسف',
                        'role' => 'عامل تعبئة',
                        'performance' => 98
                    ],
                    'details' => [
                        'boxCount' => 3,
                        'coilsPerBox' => 12,
                        'packagingType' => 'كرتون'
                    ],
                    'shipping' => [
                        'trackingNumber' => 'SHIP-2025-1234',
                        'destination' => 'مركز التوزيع - الرياض',
                        'shippedAt' => '2025-11-14 08:00:00'
                    ],
                    'quality' => 'A+',
                    'notes' => 'جاهز للشحن'
                ]
            ],
            'summary' => [
                'totalDuration' => '17 ساعة 15 دقيقة',
                'totalInputWeight' => 1000,
                'totalOutputWeight' => 920,
                'totalWaste' => 80,
                'totalWastePercentage' => 8.0,
                'totalCost' => 1840,
                'qualityScore' => 91,
                'efficiency' => 'جيد',
                'recommendations' => [
                    'المرحلة الثانية تحتاج مراجعة - الهدر أعلى من المعدل المقبول',
                    'الأداء العام ضمن النطاق المقبول',
                    'يُنصح بتحسين عملية المعالجة الحرارية في المرحلة الثانية',
                    'عامل التعبئة (خالد يوسف) حقق أداءً ممتازاً - 98%'
                ]
            ],
            'comparisons' => [
                'averageWaste' => 6.5,
                'companyBest' => 5.2,
                'industryAverage' => 9.0,
                'performance' => 'أعلى من المتوسط'
            ]
        ];
    }
}
