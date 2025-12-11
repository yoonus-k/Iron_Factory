<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Stage1ManagementReportController extends Controller
{
    /**
     * عرض تقرير إدارة المرحلة الأولى
     */
    public function index()
    {
        // التحقق من الصلاحيات
        if (!Auth::user()->hasPermission('STAGE1_STANDS_READ')) {
            abort(403);
        }

        // جلب جميع السجلات
        $query = DB::table('stage1_stands')
            ->join('materials', 'stage1_stands.material_id', '=', 'materials.id')
            ->leftJoin('users', 'stage1_stands.created_by', '=', 'users.id')
            ->select(
                'stage1_stands.*',
                'materials.name_ar as material_name',
                'users.name as created_by_name'
            );

        // ========== الفلاتر ==========
        $filters = [];

        // البحث بالباركود
        if (request('search')) {
            $search = request('search');
            $query->where('stage1_stands.barcode', 'like', "%$search%");
            $filters['search'] = $search;
        }

        // التصفية بالحالة
        if (request('status')) {
            $status = request('status');
            $query->where('stage1_stands.status', $status);
            $filters['status'] = $status;
        }

        // التصفية بالمادة
        if (request('material_id')) {
            $materialId = request('material_id');
            $query->where('stage1_stands.material_id', $materialId);
            $filters['material_id'] = $materialId;
        }

        // التصفية بالعامل
        if (request('worker_id')) {
            $workerId = request('worker_id');
            $query->where('stage1_stands.created_by', $workerId);
            $filters['worker_id'] = $workerId;
        }

        // التصفية بالتاريخ
        if (request('from_date')) {
            $fromDate = request('from_date');
            $query->whereDate('stage1_stands.created_at', '>=', $fromDate);
            $filters['from_date'] = $fromDate;
        }

        if (request('to_date')) {
            $toDate = request('to_date');
            $query->whereDate('stage1_stands.created_at', '<=', $toDate);
            $filters['to_date'] = $toDate;
        }

        // التصفية بنسبة الهدر
        if (request('waste_level')) {
            $wasteLevel = request('waste_level');
            if ($wasteLevel === 'safe') {
                $query->whereRaw("(waste / weight) * 100 <= 8");
            } elseif ($wasteLevel === 'warning') {
                $query->whereRaw("(waste / weight) * 100 > 8 AND (waste / weight) * 100 <= 15");
            } elseif ($wasteLevel === 'critical') {
                $query->whereRaw("(waste / weight) * 100 > 15");
            }
            $filters['waste_level'] = $wasteLevel;
        }

        // ترتيب النتائج
        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'desc');
        $query->orderBy("stage1_stands.$sortBy", $sortOrder);

        $allRecords = $query->get();

        // جلب المواد والعمال للفلاتر
        $materials = DB::table('materials')->select('id', 'name_ar')->get();
        $workers = DB::table('users')
            ->whereIn('id', DB::table('stage1_stands')->pluck('created_by'))
            ->select('id', 'name')
            ->get();

        // ========== KPI الأساسيات ==========
        $totalStands = $allRecords->count();
        $standsToday = $allRecords->filter(function ($record) {
            if (!$record->created_at) return false;
            if (is_string($record->created_at)) {
                $createdDate = substr($record->created_at, 0, 10);
            } else {
                $createdDate = \Carbon\Carbon::parse($record->created_at)->format('Y-m-d');
            }
            return $createdDate === today()->toDateString();
        })->count();

        // ========== حالات الاستاندات ==========
        $statusCreated = $allRecords->where('status', 'created')->count();
        $statusInProcess = $allRecords->where('status', 'in_process')->count();
        $statusCompleted = $allRecords->where('status', 'completed')->count();
        $statusPending = $allRecords->where('status', 'pending_approval')->count();
        $statusConsumed = $allRecords->where('status', 'consumed')->count();

        // ========== معدلات الإتمام ==========
        $completedStands = $statusCompleted;
        $completionRate = $totalStands > 0 ? round(($completedStands / $totalStands) * 100) : 0;
        $pendingStands = $statusPending;

        // ========== الأوزان ==========
        // وزن المادة الكلي = weight (الدخل الكلي)
        $totalInputWeight = round($allRecords->sum('weight'), 2);

        // وزن المادة الخارج = remaining_weight (الخارج بدون وزن الاستاند)
        $totalOutputWeight = round($allRecords->sum('remaining_weight'), 2);

        // جلب إجمالي الهدر من حقل waste مباشرة (الهدر الفعلي من المادة فقط)
        $totalWaste = round($allRecords->sum('waste'), 2);

        // ========== معايير الهدر ==========
        // حساب نسبة الهدر على أساس: الهدر / وزن المادة الفعلي (بدون وزن الاستاند)
        $wastePercentages = $allRecords->map(function ($record) {
            if ($record->weight > 0) {
                // النسبة = waste / weight (الهدر من وزن المادة الفعلي)
                return ($record->waste / $record->weight) * 100;
            }
            return 0;
        })->filter(function ($val) {
            return $val >= 0;
        })->values();

        $avgWastePercentage = $wastePercentages->count() > 0 ? round($wastePercentages->avg(), 2) : 0;
        $maxWastePercentage = $wastePercentages->count() > 0 ? round($wastePercentages->max(), 2) : 0;
        $minWastePercentage = $wastePercentages->count() > 0 ? round($wastePercentages->min(), 2) : 0;

        // البحث عن الاستاند الأفضل والأسوأ (على أساس نسبة الهدر من المادة فقط)
        $maxWasteRecord = $allRecords->sortByDesc(function ($record) {
            if ($record->weight > 0) {
                return ($record->waste / $record->weight) * 100;
            }
            return 0;
        })->first();
        $maxWasteBarcode = $maxWasteRecord ? $maxWasteRecord->barcode : '-';

        $minWasteRecord = $allRecords->sortBy(function ($record) {
            if ($record->weight > 0) {
                return ($record->waste / $record->weight) * 100;
            }
            return 0;
        })->first();
        $minWasteBarcode = $minWasteRecord ? $minWasteRecord->barcode : '-';

        // ========== تحليل العمال ==========
        $workerPerformance = $allRecords->groupBy('created_by_name')->map(function ($items, $workerName) {
            $count = $items->count();
            $wastePercs = $items->map(function ($record) {
                if ($record->weight > 0) {
                    // حساب النسبة من الهدر الفعلي للمادة فقط
                    return ($record->waste / $record->weight) * 100;
                }
                return 0;
            })->filter(function ($val) {
                return $val >= 0;
            });

            return [
                'name' => $workerName,
                'count' => $count,
                'avg_waste' => $wastePercs->count() > 0 ? round($wastePercs->avg(), 2) : 0,
            ];
        });

        $bestWorker = $workerPerformance->sortByDesc('count')->first();
        $bestWorkerName = $bestWorker ? $bestWorker['name'] : 'غير متوفر';
        $bestWorkerCount = $bestWorker ? $bestWorker['count'] : 0;
        $bestWorkerAvgWaste = $bestWorker ? $bestWorker['avg_waste'] : 0;

        // ========== أفضل استاند ==========
        $stands = DB::table('stands')
            ->select('id', 'stand_number', 'usage_count')
            ->get();

        $bestStand = $stands->sortByDesc('usage_count')->first();
        $bestStandNumber = $bestStand ? $bestStand->stand_number : 'غير متوفر';
        $bestStandUsageCount = $bestStand ? $bestStand->usage_count : 0;
        $bestStandWaste = $minWastePercentage;

        // ========== عدد العمال النشطين ==========
        $activeWorkers = $allRecords->filter(function ($record) {
            if (!$record->created_at) return false;
            $createdDate = is_string($record->created_at) ? strtotime($record->created_at) : strtotime($record->created_at);
            $weekAgoTime = now()->subDays(7)->timestamp;
            return $createdDate >= $weekAgoTime;
        })->pluck('created_by')->unique()->count();

        // ========== متوسط الأداء اليومي ==========
        $dateGroups = $allRecords->groupBy(function ($item) {
            if (!$item->created_at) return null;
            if (is_string($item->created_at)) {
                $date = substr($item->created_at, 0, 10);
            } else {
                $date = \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
            }
            return $date;
        })->filter();

        $avgDailyProduction = $dateGroups->count() > 0 ? round($totalStands / $dateGroups->count()) : 0;

        // ========== كفاءة الإنتاج ==========
        $productionEfficiency = $totalInputWeight > 0 ? round(($totalOutputWeight / $totalInputWeight) * 100, 2) : 0;

        // ========== معدل الالتزام ==========
        $compliantRecords = $allRecords->filter(function ($record) {
            if ($record->weight > 0) {
                $waste = ($record->waste / $record->weight) * 100;
                return $waste <= 15 && $record->status !== 'pending_approval';
            }
            return true;
        })->count();

        $complianceRate = $totalStands > 0 ? round(($compliantRecords / $totalStands) * 100) : 0;

        // ========== تحليل النفايات ==========
        $acceptableWaste = $allRecords->filter(function ($record) {
            if ($record->weight > 0) {
                $waste = ($record->waste / $record->weight) * 100;
                return $waste <= 8;
            }
            return true;
        })->count();

        $warningWaste = $allRecords->filter(function ($record) {
            if ($record->weight > 0) {
                $waste = ($record->waste / $record->weight) * 100;
                return $waste > 8 && $waste <= 15;
            }
            return false;
        })->count();

        $criticalWaste = $allRecords->filter(function ($record) {
            if ($record->weight > 0) {
                $waste = ($record->waste / $record->weight) * 100;
                return $waste > 15;
            }
            return false;
        })->count();

        // ========== آخر السجلات ==========
        $recentRecords = DB::table('stage1_stands')
            ->join('materials', 'stage1_stands.material_id', '=', 'materials.id')
            ->leftJoin('users', 'stage1_stands.created_by', '=', 'users.id')
            ->select(
                'stage1_stands.*',
                'materials.name_ar as material_name',
                'users.name as created_by_name'
            )
            ->orderBy('stage1_stands.created_at', 'desc')
            ->limit(10)
            ->get();

        // ========== البيانات اليومية ==========
        $dailyOperations = [];
        $dateGroups->each(function ($records, $date) use (&$dailyOperations) {
            $count = $records->count();
            $totalInput = round($records->sum('weight'), 2);
            $totalOutput = round($records->sum('remaining_weight'), 2);
            // الهدر الفعلي من المادة (حقل waste)
            $totalWaste = round($records->sum('waste'), 2);

            $wastePercs = $records->map(function ($record) {
                if ($record->weight > 0) {
                    // حساب النسبة من الهدر الفعلي
                    return ($record->waste / $record->weight) * 100;
                }
                return 0;
            })->filter(function ($val) {
                return $val >= 0;
            });

            $avgWaste = $wastePercs->count() > 0 ? round($wastePercs->avg(), 2) : 0;
            $completed = $records->where('status', 'completed')->count();
            $pending = $records->where('status', 'pending_approval')->count();

            $dailyOperations[] = [
                'date' => $date,
                'count' => $count,
                'total_input' => $totalInput,
                'total_output' => $totalOutput,
                'total_waste' => $totalWaste,
                'avg_waste' => $avgWaste,
                'completed' => $completed,
                'pending' => $pending,
            ];
        });

        // ========== البيانات التراكمية (Cumulative) ==========
        $cumulativeData = [];
        $cumulativeInput = 0;
        $cumulativeOutput = 0;
        $cumulativeWaste = 0;

        foreach ($dailyOperations as $day) {
            $cumulativeInput += $day['total_input'];
            $cumulativeOutput += $day['total_output'];
            $cumulativeWaste += $day['total_waste'];

            $completionPerc = $cumulativeInput > 0 ? round(($cumulativeOutput / $cumulativeInput) * 100, 2) : 0;
            $totalWastePerc = $cumulativeInput > 0 ? round(($cumulativeWaste / $cumulativeInput) * 100, 2) : 0;

            $cumulativeData[] = [
                'date' => $day['date'],
                'cumulative_input' => $cumulativeInput,
                'cumulative_output' => $cumulativeOutput,
                'cumulative_waste' => $cumulativeWaste,
                'completion_percentage' => $completionPerc,
                'total_waste_percentage' => $totalWastePerc,
            ];
        }

        // ========== تجميع البيانات ==========
        $data = compact(
            'totalStands',
            'standsToday',
            'completedStands',
            'completionRate',
            'pendingStands',
            'totalInputWeight',
            'totalOutputWeight',
            'totalWaste',
            'avgWastePercentage',
            'maxWastePercentage',
            'minWastePercentage',
            'maxWasteBarcode',
            'minWasteBarcode',
            'activeWorkers',
            'avgDailyProduction',
            'complianceRate',
            'productionEfficiency',
            'statusCreated',
            'statusInProcess',
            'statusCompleted',
            'statusPending',
            'statusConsumed',
            'bestWorkerName',
            'bestWorkerCount',
            'bestWorkerAvgWaste',
            'bestStandNumber',
            'bestStandUsageCount',
            'bestStandWaste',
            'acceptableWaste',
            'warningWaste',
            'criticalWaste',
            'recentRecords',
            'dailyOperations',
            'cumulativeData',
            'filters',
            'materials',
            'workers',
            'allRecords'
        );

        return view('manufacturing::reports.stage1_management_report', $data);
    }

    /**
     * تصدير التقرير كـ PDF
     */
    public function exportPdf()
    {
        // يمكن استخدام مكتبة TCPDF أو Dompdf
        // للآن، نستخدم طباعة المتصفح
        return redirect()->back();
    }

    /**
     * تصدير التقرير كـ Excel
     */
    public function exportExcel()
    {
        // يمكن استخدام مكتبة PhpSpreadsheet أو Maatwebsite\Excel
        // للآن، نستخدم تنزيل CSV
        return redirect()->back();
    }
}
