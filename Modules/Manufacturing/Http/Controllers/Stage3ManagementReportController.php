<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Stage3ManagementReportController extends Controller
{
    /**
     * Display stage3 management report
     */
    public function index()
    {
        // Permission check
        if (!Auth::user()->hasPermission('STAGE3_COILS_READ')) {
            abort(403);
        }

        // Get all records
        $query = DB::table('stage3_coils')
            ->leftJoin('stage2_processed', 'stage3_coils.stage2_id', '=', 'stage2_processed.id')
            ->leftJoin('users', 'stage3_coils.created_by', '=', 'users.id')
            ->select(
                'stage3_coils.*',
                'stage2_processed.barcode as stage2_barcode',
                'users.name as created_by_name'
            );

        // ========== FILTERS ==========
        $filters = [];

        // Search by barcode
        if (request('search')) {
            $search = request('search');
            $query->where('stage3_coils.barcode', 'like', "%$search%")
                  ->orWhere('stage3_coils.parent_barcode', 'like', "%$search%")
                  ->orWhere('stage3_coils.coil_number', 'like', "%$search%");
            $filters['search'] = $search;
        }

        // Filter by status
        if (request('status')) {
            $status = request('status');
            $query->where('stage3_coils.status', $status);
            $filters['status'] = $status;
        }

        // Filter by worker
        if (request('worker_id')) {
            $workerId = request('worker_id');
            $query->where('stage3_coils.created_by', $workerId);
            $filters['worker_id'] = $workerId;
        }

        // Filter by date range
        if (request('from_date')) {
            $fromDate = request('from_date');
            $query->whereDate('stage3_coils.created_at', '>=', $fromDate);
            $filters['from_date'] = $fromDate;
        }

        if (request('to_date')) {
            $toDate = request('to_date');
            $query->whereDate('stage3_coils.created_at', '<=', $toDate);
            $filters['to_date'] = $toDate;
        }

        // Filter by waste level
        if (request('waste_level')) {
            $wasteLevel = request('waste_level');
            if ($wasteLevel === 'safe') {
                $query->whereRaw('(waste / total_weight) * 100 <= 8');
            } elseif ($wasteLevel === 'warning') {
                $query->whereRaw('(waste / total_weight) * 100 > 8 AND (waste / total_weight) * 100 <= 15');
            } elseif ($wasteLevel === 'critical') {
                $query->whereRaw('(waste / total_weight) * 100 > 15');
            }
            $filters['waste_level'] = $wasteLevel;
        }

        // Sort results
        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'desc');
        $query->orderBy("stage3_coils.$sortBy", $sortOrder);

        $stage3Records = $query->get();

        // Get workers for filter dropdown
        $stage3Workers = DB::table('users')
            ->whereIn('id', DB::table('stage3_coils')->pluck('created_by'))
            ->select('id', 'name')
            ->get();

        // ========== BASIC KPIs ==========
        $stage3Total = $stage3Records->count();
        $stage3Today = $stage3Records->filter(function ($record) {
            if (!$record->created_at) return false;
            if (is_string($record->created_at)) {
                $createdDate = substr($record->created_at, 0, 10);
            } else {
                $createdDate = Carbon::parse($record->created_at)->format('Y-m-d');
            }
            return $createdDate === today()->toDateString();
        })->count();

        // ========== STATUS DISTRIBUTION ==========
        $stage3StatusCreated = $stage3Records->where('status', 'created')->count();
        $stage3StatusInProcess = $stage3Records->where('status', 'in_process')->count();
        $stage3StatusCompleted = $stage3Records->where('status', 'completed')->count();
        $stage3StatusPacked = $stage3Records->where('status', 'packed')->count();

        // ========== COMPLETION RATE ==========
        $stage3CompletedCount = $stage3StatusCompleted;
        $stage3CompletionRate = $stage3Total > 0 ? round(($stage3CompletedCount / $stage3Total) * 100) : 0;

        // ========== WEIGHT CALCULATIONS ==========
        $stage3TotalBaseWeight = round($stage3Records->sum('base_weight'), 2);
        $stage3TotalDyeWeight = round($stage3Records->sum('dye_weight'), 2);
        $stage3TotalPlasticWeight = round($stage3Records->sum('plastic_weight'), 2);
        $stage3TotalWeight = round($stage3Records->sum('total_weight'), 2);
        $stage3TotalWaste = round($stage3Records->sum('waste'), 2);

        // ========== WASTE PERCENTAGES ==========
        $stage3WastePercentages = $stage3Records->map(function ($record) {
            if ($record->total_weight > 0) {
                return (($record->waste) / $record->total_weight) * 100;
            }
            return 0;
        })->filter(function ($val) {
            return $val >= 0;
        })->values();

        $stage3AvgWastePercentage = $stage3WastePercentages->count() > 0 ? round($stage3WastePercentages->avg(), 2) : 0;
        $stage3MaxWastePercentage = $stage3WastePercentages->count() > 0 ? round($stage3WastePercentages->max(), 2) : 0;
        $stage3MinWastePercentage = $stage3WastePercentages->count() > 0 ? round($stage3WastePercentages->min(), 2) : 0;

        // Find best and worst performing records
        $stage3MaxWasteRecord = $stage3Records->sortByDesc(function ($record) {
            if ($record->total_weight > 0) {
                return (($record->waste) / $record->total_weight) * 100;
            }
            return 0;
        })->first();
        $stage3MaxWasteBarcode = $stage3MaxWasteRecord ? $stage3MaxWasteRecord->barcode : '-';

        $stage3MinWasteRecord = $stage3Records->sortBy(function ($record) {
            if ($record->total_weight > 0) {
                return (($record->waste) / $record->total_weight) * 100;
            }
            return 0;
        })->first();
        $stage3MinWasteBarcode = $stage3MinWasteRecord ? $stage3MinWasteRecord->barcode : '-';

        // ========== WORKER PERFORMANCE ==========
        $stage3WorkerPerformance = $stage3Records->groupBy('created_by_name')->map(function ($items, $workerName) {
            $count = $items->count();
            $totalWeight = round($items->sum('total_weight'), 2);
            $totalWaste = round($items->sum('waste'), 2);
            $wastePercs = $items->map(function ($record) {
                if ($record->total_weight > 0) {
                    return (($record->waste) / $record->total_weight) * 100;
                }
                return 0;
            })->filter(function ($val) {
                return $val >= 0;
            });

            return [
                'name' => $workerName,
                'count' => $count,
                'total_weight' => $totalWeight,
                'total_waste' => $totalWaste,
                'avg_waste' => $wastePercs->count() > 0 ? round($wastePercs->avg(), 2) : 0,
            ];
        });

        $stage3BestWorker = $stage3WorkerPerformance->sortByDesc('count')->first();
        $stage3BestWorkerName = $stage3BestWorker ? $stage3BestWorker['name'] : 'Not available';
        $stage3BestWorkerCount = $stage3BestWorker ? $stage3BestWorker['count'] : 0;
        $stage3BestWorkerAvgWaste = $stage3BestWorker ? $stage3BestWorker['avg_waste'] : 0;

        // ========== ACTIVE WORKERS ==========
        $stage3ActiveWorkers = $stage3Records->filter(function ($record) {
            if (!$record->created_at) return false;
            $createdDate = is_string($record->created_at) ? strtotime($record->created_at) : $record->created_at->timestamp;
            $weekAgoTime = now()->subDays(7)->timestamp;
            return $createdDate >= $weekAgoTime;
        })->pluck('created_by')->unique()->count();

        // ========== DAILY OPERATIONS ==========
        $stage3DateGroups = $stage3Records->groupBy(function ($item) {
            if (!$item->created_at) return null;
            if (is_string($item->created_at)) {
                $date = substr($item->created_at, 0, 10);
            } else {
                $date = Carbon::parse($item->created_at)->format('Y-m-d');
            }
            return $date;
        })->filter();

        $stage3AvgDailyProduction = $stage3DateGroups->count() > 0 ? round($stage3Total / $stage3DateGroups->count()) : 0;

        // ========== PRODUCTION EFFICIENCY ==========
        $stage3ProductionEfficiency = $stage3TotalBaseWeight > 0 ? round(($stage3TotalWeight / $stage3TotalBaseWeight) * 100, 2) : 0;

        // ========== ACCEPTABLE / WARNING / CRITICAL WASTE ==========
        $stage3AcceptableWaste = $stage3Records->filter(function ($record) {
            if ($record->total_weight > 0) {
                $waste = (($record->waste) / $record->total_weight) * 100;
                return $waste <= 8;
            }
            return true;
        })->count();

        $stage3WarningWaste = $stage3Records->filter(function ($record) {
            if ($record->total_weight > 0) {
                $waste = (($record->waste) / $record->total_weight) * 100;
                return $waste > 8 && $waste <= 15;
            }
            return false;
        })->count();

        $stage3CriticalWaste = $stage3Records->filter(function ($record) {
            if ($record->total_weight > 0) {
                $waste = (($record->waste) / $record->total_weight) * 100;
                return $waste > 15;
            }
            return false;
        })->count();

        // ========== DAILY OPERATIONS DATA ==========
        $stage3DailyOperations = [];
        $stage3DateGroups->each(function ($records, $date) use (&$stage3DailyOperations) {
            $count = $records->count();
            $totalBaseWeight = round($records->sum('base_weight'), 2);
            $totalDyeWeight = round($records->sum('dye_weight'), 2);
            $totalPlasticWeight = round($records->sum('plastic_weight'), 2);
            $totalWeight = round($records->sum('total_weight'), 2);
            $totalWaste = round($records->sum('waste'), 2);

            $wastePercs = $records->map(function ($record) {
                if ($record->total_weight > 0) {
                    return (($record->waste) / $record->total_weight) * 100;
                }
                return 0;
            })->filter(function ($val) {
                return $val >= 0;
            });

            $avgWaste = $wastePercs->count() > 0 ? round($wastePercs->avg(), 2) : 0;
            $completed = $records->where('status', 'completed')->count();
            $packed = $records->where('status', 'packed')->count();

            $stage3DailyOperations[] = [
                'date' => $date,
                'count' => $count,
                'total_base_weight' => $totalBaseWeight,
                'total_dye_weight' => $totalDyeWeight,
                'total_plastic_weight' => $totalPlasticWeight,
                'total_weight' => $totalWeight,
                'total_waste' => $totalWaste,
                'avg_waste' => $avgWaste,
                'completed' => $completed,
                'packed' => $packed,
            ];
        });

        // ========== CUMULATIVE DATA ==========
        $stage3CumulativeData = [];
        $stage3CumulativeBaseWeight = 0;
        $stage3CumulativeDyeWeight = 0;
        $stage3CumulativePlasticWeight = 0;
        $stage3CumulativeTotalWeight = 0;
        $stage3CumulativeWaste = 0;

        foreach ($stage3DailyOperations as $day) {
            $stage3CumulativeBaseWeight += $day['total_base_weight'];
            $stage3CumulativeDyeWeight += $day['total_dye_weight'];
            $stage3CumulativePlasticWeight += $day['total_plastic_weight'];
            $stage3CumulativeTotalWeight += $day['total_weight'];
            $stage3CumulativeWaste += $day['total_waste'];

            $completionPerc = $stage3CumulativeBaseWeight > 0 ? round(($stage3CumulativeTotalWeight / $stage3CumulativeBaseWeight) * 100, 2) : 0;
            $totalWastePerc = $stage3CumulativeTotalWeight > 0 ? round(($stage3CumulativeWaste / $stage3CumulativeTotalWeight) * 100, 2) : 0;

            $stage3CumulativeData[] = [
                'date' => $day['date'],
                'cumulative_base_weight' => $stage3CumulativeBaseWeight,
                'cumulative_dye_weight' => $stage3CumulativeDyeWeight,
                'cumulative_plastic_weight' => $stage3CumulativePlasticWeight,
                'cumulative_total_weight' => $stage3CumulativeTotalWeight,
                'cumulative_waste' => $stage3CumulativeWaste,
                'completion_percentage' => $completionPerc,
                'total_waste_percentage' => $totalWastePerc,
            ];
        }

        // ========== DATA COMPILATION ==========
        $data = compact(
            'stage3Total',
            'stage3Today',
            'stage3CompletedCount',
            'stage3CompletionRate',
            'stage3TotalBaseWeight',
            'stage3TotalDyeWeight',
            'stage3TotalPlasticWeight',
            'stage3TotalWeight',
            'stage3TotalWaste',
            'stage3AvgWastePercentage',
            'stage3MaxWastePercentage',
            'stage3MinWastePercentage',
            'stage3MaxWasteBarcode',
            'stage3MinWasteBarcode',
            'stage3ActiveWorkers',
            'stage3AvgDailyProduction',
            'stage3ProductionEfficiency',
            'stage3StatusCreated',
            'stage3StatusInProcess',
            'stage3StatusCompleted',
            'stage3StatusPacked',
            'stage3BestWorkerName',
            'stage3BestWorkerCount',
            'stage3BestWorkerAvgWaste',
            'stage3AcceptableWaste',
            'stage3WarningWaste',
            'stage3CriticalWaste',
            'stage3DailyOperations',
            'stage3CumulativeData',
            'stage3WorkerPerformance',
            'stage3Records',
            'stage3Workers',
            'filters'
        );

        return view('manufacturing::reports.stage3_management_report', $data);
    }

    /**
     * Export report as PDF
     */
    public function exportPdf()
    {
        return redirect()->back();
    }

    /**
     * Export report as Excel
     */
    public function exportExcel()
    {
        return redirect()->back();
    }
}
