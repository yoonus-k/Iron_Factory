<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Stage2ManagementReportController extends Controller
{
    /**
     * Display stage2 management report
     */
    public function index()
    {
        // Permission check
        if (!Auth::user()->hasPermission('STAGE2_PROCESSED_READ')) {
            abort(403);
        }

        // Get all records
        $query = DB::table('stage2_processed')
            ->leftJoin('stage1_stands', 'stage2_processed.stage1_id', '=', 'stage1_stands.id')
            ->leftJoin('users', 'stage2_processed.created_by', '=', 'users.id')
            ->select(
                'stage2_processed.*',
                'stage1_stands.barcode as stage1_barcode',
                'users.name as created_by_name'
            );

        // ========== FILTERS ==========
        $filters = [];

        // Search by barcode
        if (request('search')) {
            $search = request('search');
            $query->where('stage2_processed.barcode', 'like', "%$search%")
                  ->orWhere('stage2_processed.parent_barcode', 'like', "%$search%");
            $filters['search'] = $search;
        }

        // Filter by status
        if (request('status')) {
            $status = request('status');
            $query->where('stage2_processed.status', $status);
            $filters['status'] = $status;
        }

        // Filter by worker
        if (request('worker_id')) {
            $workerId = request('worker_id');
            $query->where('stage2_processed.created_by', $workerId);
            $filters['worker_id'] = $workerId;
        }

        // Filter by date range
        if (request('from_date')) {
            $fromDate = request('from_date');
            $query->whereDate('stage2_processed.created_at', '>=', $fromDate);
            $filters['from_date'] = $fromDate;
        }

        if (request('to_date')) {
            $toDate = request('to_date');
            $query->whereDate('stage2_processed.created_at', '<=', $toDate);
            $filters['to_date'] = $toDate;
        }

        // Filter by waste level
        if (request('waste_level')) {
            $wasteLevel = request('waste_level');
            if ($wasteLevel === 'safe') {
                $query->whereRaw('(waste / input_weight) * 100 <= 8');
            } elseif ($wasteLevel === 'warning') {
                $query->whereRaw('(waste / input_weight) * 100 > 8 AND (waste / input_weight) * 100 <= 15');
            } elseif ($wasteLevel === 'critical') {
                $query->whereRaw('(waste / input_weight) * 100 > 15');
            }
            $filters['waste_level'] = $wasteLevel;
        }

        // Sort results
        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'desc');
        $query->orderBy("stage2_processed.$sortBy", $sortOrder);

        $stage2Records = $query->get();

        // Get workers for filter dropdown
        $stage2Workers = DB::table('users')
            ->whereIn('id', DB::table('stage2_processed')->pluck('created_by'))
            ->select('id', 'name')
            ->get();

        // ========== BASIC KPIs ==========
        $stage2Total = $stage2Records->count();
        $stage2Today = $stage2Records->filter(function ($record) {
            if (!$record->created_at) return false;
            if (is_string($record->created_at)) {
                $createdDate = substr($record->created_at, 0, 10);
            } else {
                $createdDate = Carbon::parse($record->created_at)->format('Y-m-d');
            }
            return $createdDate === today()->toDateString();
        })->count();

        // ========== STATUS DISTRIBUTION ==========
        $stage2StatusStarted = $stage2Records->where('status', 'started')->count();
        $stage2StatusInProgress = $stage2Records->where('status', 'in_progress')->count();
        $stage2StatusCompleted = $stage2Records->where('status', 'completed')->count();
        $stage2StatusConsumed = $stage2Records->where('status', 'consumed')->count();

        // ========== COMPLETION RATE ==========
        $stage2CompletedCount = $stage2StatusCompleted;
        $stage2CompletionRate = $stage2Total > 0 ? round(($stage2CompletedCount / $stage2Total) * 100) : 0;

        // ========== WEIGHT CALCULATIONS ==========
        $stage2TotalInputWeight = round($stage2Records->sum('input_weight'), 2);
        $stage2TotalOutputWeight = round($stage2Records->sum('output_weight'), 2);
        $stage2TotalWaste = round($stage2Records->sum('waste'), 2);

        // ========== WASTE PERCENTAGES ==========
        $stage2WastePercentages = $stage2Records->map(function ($record) {
            if ($record->input_weight > 0) {
                return (($record->waste) / $record->input_weight) * 100;
            }
            return 0;
        })->filter(function ($val) {
            return $val >= 0;
        })->values();

        $stage2AvgWastePercentage = $stage2WastePercentages->count() > 0 ? round($stage2WastePercentages->avg(), 2) : 0;
        $stage2MaxWastePercentage = $stage2WastePercentages->count() > 0 ? round($stage2WastePercentages->max(), 2) : 0;
        $stage2MinWastePercentage = $stage2WastePercentages->count() > 0 ? round($stage2WastePercentages->min(), 2) : 0;

        // Find best and worst performing records
        $stage2MaxWasteRecord = $stage2Records->sortByDesc(function ($record) {
            if ($record->input_weight > 0) {
                return (($record->waste) / $record->input_weight) * 100;
            }
            return 0;
        })->first();
        $stage2MaxWasteBarcode = $stage2MaxWasteRecord ? $stage2MaxWasteRecord->barcode : '-';

        $stage2MinWasteRecord = $stage2Records->sortBy(function ($record) {
            if ($record->input_weight > 0) {
                return (($record->waste) / $record->input_weight) * 100;
            }
            return 0;
        })->first();
        $stage2MinWasteBarcode = $stage2MinWasteRecord ? $stage2MinWasteRecord->barcode : '-';

        // ========== WORKER PERFORMANCE ==========
        $stage2WorkerPerformance = $stage2Records->groupBy('created_by_name')->map(function ($items, $workerName) {
            $count = $items->count();
            $totalInput = round($items->sum('input_weight'), 2);
            $totalWaste = round($items->sum('waste'), 2);
            $wastePercs = $items->map(function ($record) {
                if ($record->input_weight > 0) {
                    return (($record->waste) / $record->input_weight) * 100;
                }
                return 0;
            })->filter(function ($val) {
                return $val >= 0;
            });

            return [
                'name' => $workerName,
                'count' => $count,
                'total_input' => $totalInput,
                'total_waste' => $totalWaste,
                'avg_waste' => $wastePercs->count() > 0 ? round($wastePercs->avg(), 2) : 0,
            ];
        });

        $stage2BestWorker = $stage2WorkerPerformance->sortByDesc('count')->first();
        $stage2BestWorkerName = $stage2BestWorker ? $stage2BestWorker['name'] : 'Not available';
        $stage2BestWorkerCount = $stage2BestWorker ? $stage2BestWorker['count'] : 0;
        $stage2BestWorkerAvgWaste = $stage2BestWorker ? $stage2BestWorker['avg_waste'] : 0;

        // ========== ACTIVE WORKERS ==========
        $stage2ActiveWorkers = $stage2Records->filter(function ($record) {
            if (!$record->created_at) return false;
            $createdDate = is_string($record->created_at) ? strtotime($record->created_at) : strtotime($record->created_at);
            $weekAgoTime = now()->subDays(7)->timestamp;
            return $createdDate >= $weekAgoTime;
        })->pluck('created_by')->unique()->count();

        // ========== DAILY OPERATIONS ==========
        $stage2DateGroups = $stage2Records->groupBy(function ($item) {
            if (!$item->created_at) return null;
            if (is_string($item->created_at)) {
                $date = substr($item->created_at, 0, 10);
            } else {
                $date = Carbon::parse($item->created_at)->format('Y-m-d');
            }
            return $date;
        })->filter();

        $stage2AvgDailyProduction = $stage2DateGroups->count() > 0 ? round($stage2Total / $stage2DateGroups->count()) : 0;

        // ========== PRODUCTION EFFICIENCY ==========
        $stage2ProductionEfficiency = $stage2TotalInputWeight > 0 ? round(($stage2TotalOutputWeight / $stage2TotalInputWeight) * 100, 2) : 0;

        // ========== ACCEPTABLE / WARNING / CRITICAL WASTE ==========
        $stage2AcceptableWaste = $stage2Records->filter(function ($record) {
            if ($record->input_weight > 0) {
                $waste = (($record->waste) / $record->input_weight) * 100;
                return $waste <= 8;
            }
            return true;
        })->count();

        $stage2WarningWaste = $stage2Records->filter(function ($record) {
            if ($record->input_weight > 0) {
                $waste = (($record->waste) / $record->input_weight) * 100;
                return $waste > 8 && $waste <= 15;
            }
            return false;
        })->count();

        $stage2CriticalWaste = $stage2Records->filter(function ($record) {
            if ($record->input_weight > 0) {
                $waste = (($record->waste) / $record->input_weight) * 100;
                return $waste > 15;
            }
            return false;
        })->count();

        // ========== DAILY OPERATIONS DATA ==========
        $stage2DailyOperations = [];
        $stage2DateGroups->each(function ($records, $date) use (&$stage2DailyOperations) {
            $count = $records->count();
            $totalInput = round($records->sum('input_weight'), 2);
            $totalOutput = round($records->sum('output_weight'), 2);
            $totalWaste = round($records->sum('waste'), 2);

            $wastePercs = $records->map(function ($record) {
                if ($record->input_weight > 0) {
                    return (($record->waste) / $record->input_weight) * 100;
                }
                return 0;
            })->filter(function ($val) {
                return $val >= 0;
            });

            $avgWaste = $wastePercs->count() > 0 ? round($wastePercs->avg(), 2) : 0;
            $completed = $records->where('status', 'completed')->count();
            $inProgress = $records->where('status', 'in_progress')->count();

            $stage2DailyOperations[] = [
                'date' => $date,
                'count' => $count,
                'total_input' => $totalInput,
                'total_output' => $totalOutput,
                'total_waste' => $totalWaste,
                'avg_waste' => $avgWaste,
                'completed' => $completed,
                'in_progress' => $inProgress,
            ];
        });

        // ========== CUMULATIVE DATA ==========
        $stage2CumulativeData = [];
        $stage2CumulativeInput = 0;
        $stage2CumulativeOutput = 0;
        $stage2CumulativeWaste = 0;

        foreach ($stage2DailyOperations as $day) {
            $stage2CumulativeInput += $day['total_input'];
            $stage2CumulativeOutput += $day['total_output'];
            $stage2CumulativeWaste += $day['total_waste'];

            $completionPerc = $stage2CumulativeInput > 0 ? round(($stage2CumulativeOutput / $stage2CumulativeInput) * 100, 2) : 0;
            $totalWastePerc = $stage2CumulativeInput > 0 ? round(($stage2CumulativeWaste / $stage2CumulativeInput) * 100, 2) : 0;

            $stage2CumulativeData[] = [
                'date' => $day['date'],
                'cumulative_input' => $stage2CumulativeInput,
                'cumulative_output' => $stage2CumulativeOutput,
                'cumulative_waste' => $stage2CumulativeWaste,
                'completion_percentage' => $completionPerc,
                'total_waste_percentage' => $totalWastePerc,
            ];
        }

        // ========== DATA COMPILATION ==========
        $data = compact(
            'stage2Total',
            'stage2Today',
            'stage2CompletedCount',
            'stage2CompletionRate',
            'stage2TotalInputWeight',
            'stage2TotalOutputWeight',
            'stage2TotalWaste',
            'stage2AvgWastePercentage',
            'stage2MaxWastePercentage',
            'stage2MinWastePercentage',
            'stage2MaxWasteBarcode',
            'stage2MinWasteBarcode',
            'stage2ActiveWorkers',
            'stage2AvgDailyProduction',
            'stage2ProductionEfficiency',
            'stage2StatusStarted',
            'stage2StatusInProgress',
            'stage2StatusCompleted',
            'stage2StatusConsumed',
            'stage2BestWorkerName',
            'stage2BestWorkerCount',
            'stage2BestWorkerAvgWaste',
            'stage2AcceptableWaste',
            'stage2WarningWaste',
            'stage2CriticalWaste',
            'stage2DailyOperations',
            'stage2CumulativeData',
            'stage2WorkerPerformance',
            'stage2Records',
            'stage2Workers',
            'filters'
        );

        return view('manufacturing::reports.stage2_management_report', $data);
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
