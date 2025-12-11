<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\ProductTracking;

class ProductTrackingReportController extends Controller
{
    /**
     * Display comprehensive product tracking report
     */
    public function index(Request $request)
    {

            $dateFrom = $request->input('date_from', now()->subDays(7)->format('Y-m-d'));
            $dateTo = $request->input('date_to', now()->format('Y-m-d'));
            $stage = $request->input('stage', 'all');
            $status = $request->input('status', 'all');
            $worker = $request->input('worker', null);

            // Get all tracking records within date range
            $trackingRecords = $this->getTrackingRecords($dateFrom, $dateTo, $stage, $status, $worker);

            // Get summary statistics
            $summary = $this->getSummaryStatistics($dateFrom, $dateTo, $stage, $status, $worker);

            // Get production by stage results
            $byStage = $this->getProductionByStage($dateFrom, $dateTo, $stage, $status, $worker);

            // Get workers by stage
            $workersByStage = $this->getWorkersByStage($dateFrom, $dateTo, $stage, $status, $worker);

            // Get top performing workers
            $topWorkers = $this->getTopPerformingWorkers($dateFrom, $dateTo, $stage, $status);

            // Get daily production trend
            $dailyTrend = $this->getDailyProductionTrend($dateFrom, $dateTo, $stage, $status, $worker);

            // Get waste analysis
            $wasteAnalysis = $this->getWasteAnalysis($dateFrom, $dateTo, $stage, $worker);

            // Get stage performance comparison
            $stageComparison = $this->getStagePerformanceComparison($dateFrom, $dateTo);

            // Get barcode tracing examples (top 5 products)
            $topBarcodes = $this->getTopTrackedBarcodes($dateFrom, $dateTo, 5);

            // Get available filters
            $stages = DB::table('product_tracking')
                ->distinct()
                ->whereNotNull('stage')
                ->pluck('stage');

            $workers = DB::table('product_tracking')
                ->leftJoin('users', 'product_tracking.worker_id', '=', 'users.id')
                ->distinct()
                ->whereNotNull('product_tracking.worker_id')
                ->select('product_tracking.worker_id', 'users.name')
                ->get();

            return view('manufacturing::reports.product-tracking-report', compact(
                'trackingRecords',
                'summary',
                'byStage',
                'workersByStage',
                'topWorkers',
                'dailyTrend',
                'wasteAnalysis',
                'stageComparison',
                'topBarcodes',
                'dateFrom',
                'dateTo',
                'stage',
                'status',
                'worker',
                'stages',
                'workers'
            ));

    }

    /**
     * Get tracking records
     */
    private function getTrackingRecords($dateFrom, $dateTo, $stage, $status, $worker)
    {
        $query = ProductTracking::query()
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($stage != 'all') {
            $query->where('stage', $stage);
        }

        if ($status != 'all') {
            $query->where('action', $status);
        }

        if ($worker) {
            $query->where('worker_id', $worker);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    /**
     * Get summary statistics
     */
    private function getSummaryStatistics($dateFrom, $dateTo, $stage, $status, $worker)
    {
        $query = ProductTracking::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($stage != 'all') {
            $query->where('stage', $stage);
        }

        if ($status != 'all') {
            $query->where('action', $status);
        }

        if ($worker) {
            $query->where('worker_id', $worker);
        }

        $records = $query->get();

        return [
            'total_records' => $records->count(),
            'total_output_kg' => round($records->sum('output_weight'), 2),
            'total_input_kg' => round($records->sum('input_weight'), 2),
            'total_waste_kg' => round($records->sum('waste_amount'), 2),
            'waste_percentage' => $records->sum('input_weight') > 0
                ? round(($records->sum('waste_amount') / $records->sum('input_weight')) * 100, 2)
                : 0,
            'total_cost' => round($records->sum('cost'), 2),
            'avg_waste_per_record' => $records->count() > 0
                ? round($records->sum('waste_amount') / $records->count(), 2)
                : 0,
            'unique_barcodes' => $records->pluck('barcode')->unique()->count(),
            'unique_workers' => $records->whereNotNull('worker_id')->pluck('worker_id')->unique()->count(),
            'unique_stages' => $records->whereNotNull('stage')->pluck('stage')->unique()->count(),
        ];
    }

    /**
     * Get production by stage
     */
    private function getProductionByStage($dateFrom, $dateTo, $stage, $status, $worker)
    {
        $query = ProductTracking::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->whereNotNull('stage');

        if ($stage != 'all') {
            $query->where('stage', $stage);
        }

        if ($status != 'all') {
            $query->where('action', $status);
        }

        if ($worker) {
            $query->where('worker_id', $worker);
        }

        return $query->groupBy('stage')
            ->select(
                'stage',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(output_weight) as total_output'),
                DB::raw('SUM(input_weight) as total_input'),
                DB::raw('SUM(waste_amount) as total_waste'),
                DB::raw('AVG(waste_percentage) as avg_waste_pct')
            )
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->stage,
                    'count' => $item->count,
                    'output' => round($item->total_output ?? 0, 2),
                    'input' => round($item->total_input ?? 0, 2),
                    'waste' => round($item->total_waste ?? 0, 2),
                    'waste_pct' => round($item->avg_waste_pct ?? 0, 2),
                    'efficiency' => $item->total_input > 0
                        ? round(((($item->total_input - ($item->total_waste ?? 0)) / $item->total_input) * 100), 1)
                        : 0,
                ];
            })
            ->toArray();
    }

    /**
     * Get top performing workers
     */
    private function getTopPerformingWorkers($dateFrom, $dateTo, $stage, $status)
    {
        $query = ProductTracking::whereBetween('product_tracking.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->whereNotNull('worker_id')
            ->leftJoin('users', 'product_tracking.worker_id', '=', 'users.id');

        if ($stage != 'all') {
            $query->where('product_tracking.stage', $stage);
        }

        if ($status != 'all') {
            $query->where('product_tracking.action', $status);
        }

        return $query->groupBy('product_tracking.worker_id', 'users.name')
            ->select(
                'product_tracking.worker_id',
                'users.name',
                DB::raw('COUNT(*) as total_records'),
                DB::raw('SUM(product_tracking.output_weight) as total_output'),
                DB::raw('SUM(product_tracking.waste_amount) as total_waste'),
                DB::raw('AVG(product_tracking.waste_percentage) as avg_waste_pct'),
                DB::raw('SUM(product_tracking.cost) as total_cost')
            )
            ->orderByRaw('SUM(product_tracking.output_weight) DESC')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $totalInput = DB::table('product_tracking')
                    ->where('worker_id', $item->worker_id)
                    ->whereBetween('created_at', [Carbon::parse(request('date_from'))->format('Y-m-d') . ' 00:00:00', Carbon::parse(request('date_to'))->format('Y-m-d') . ' 23:59:59'])
                    ->sum('input_weight');

                return [
                    'worker_id' => $item->worker_id,
                    'worker_name' => $item->name,
                    'total_records' => $item->total_records,
                    'total_output' => round($item->total_output ?? 0, 2),
                    'total_waste' => round($item->total_waste ?? 0, 2),
                    'waste_pct' => round($item->avg_waste_pct ?? 0, 2),
                    'total_cost' => round($item->total_cost ?? 0, 2),
                    'efficiency' => $totalInput > 0
                        ? round((($totalInput - ($item->total_waste ?? 0)) / $totalInput) * 100, 1)
                        : 0,
                ];
            })
            ->toArray();
    }

    /**
     * Get daily production trend
     */
    private function getDailyProductionTrend($dateFrom, $dateTo, $stage, $status, $worker)
    {
        $query = ProductTracking::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($stage != 'all') {
            $query->where('stage', $stage);
        }

        if ($status != 'all') {
            $query->where('action', $status);
        }

        if ($worker) {
            $query->where('worker_id', $worker);
        }

        return $query->groupBy(DB::raw('DATE(created_at)'))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(output_weight) as total_output'),
                DB::raw('SUM(waste_amount) as total_waste'),
                DB::raw('AVG(waste_percentage) as avg_waste_pct')
            )
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('Y-m-d'),
                    'day_label' => Carbon::parse($item->date)->format('d/m'),
                    'count' => $item->count,
                    'output' => round($item->total_output ?? 0, 2),
                    'waste' => round($item->total_waste ?? 0, 2),
                    'waste_pct' => round($item->avg_waste_pct ?? 0, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Get waste analysis by stage
     */
    private function getWasteAnalysis($dateFrom, $dateTo, $stage, $worker)
    {
        $query = ProductTracking::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->whereNotNull('stage');

        if ($stage != 'all') {
            $query->where('stage', $stage);
        }

        if ($worker) {
            $query->where('worker_id', $worker);
        }

        return $query->groupBy('stage')
            ->select(
                'stage',
                DB::raw('SUM(waste_amount) as total_waste'),
                DB::raw('SUM(input_weight) as total_input'),
                DB::raw('AVG(waste_percentage) as avg_waste_pct'),
                DB::raw('MAX(waste_percentage) as max_waste_pct'),
                DB::raw('MIN(waste_percentage) as min_waste_pct'),
                DB::raw('COUNT(*) as record_count')
            )
            ->orderByRaw('SUM(waste_amount) DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'stage' => $item->stage,
                    'total_waste' => round($item->total_waste ?? 0, 2),
                    'total_input' => round($item->total_input ?? 0, 2),
                    'avg_waste_pct' => round($item->avg_waste_pct ?? 0, 2),
                    'max_waste_pct' => round($item->max_waste_pct ?? 0, 2),
                    'min_waste_pct' => round($item->min_waste_pct ?? 0, 2),
                    'record_count' => $item->record_count,
                    'severity' => $item->avg_waste_pct >= 10 ? 'critical' : ($item->avg_waste_pct >= 5 ? 'warning' : 'normal'),
                ];
            })
            ->toArray();
    }

    /**
     * Get stage performance comparison
     */
    private function getStagePerformanceComparison($dateFrom, $dateTo)
    {
        return DB::table('product_tracking')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->whereNotNull('stage')
            ->groupBy('stage')
            ->select(
                'stage',
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(output_weight) as total_output'),
                DB::raw('SUM(input_weight) as total_input'),
                DB::raw('SUM(waste_amount) as total_waste'),
                DB::raw('AVG(output_weight) as avg_output'),
                DB::raw('AVG(input_weight) as avg_input'),
                DB::raw('AVG(waste_percentage) as avg_waste_pct'),
                DB::raw('COUNT(DISTINCT worker_id) as worker_count')
            )
            ->get()
            ->map(function ($item) {
                return [
                    'stage' => $item->stage,
                    'total_count' => $item->total_count,
                    'total_output' => round($item->total_output ?? 0, 2),
                    'total_input' => round($item->total_input ?? 0, 2),
                    'total_waste' => round($item->total_waste ?? 0, 2),
                    'avg_output' => round($item->avg_output ?? 0, 2),
                    'avg_input' => round($item->avg_input ?? 0, 2),
                    'avg_waste_pct' => round($item->avg_waste_pct ?? 0, 2),
                    'worker_count' => $item->worker_count,
                    'efficiency' => $item->total_input > 0
                        ? round(((($item->total_input - ($item->total_waste ?? 0)) / $item->total_input) * 100), 1)
                        : 0,
                ];
            })
            ->toArray();
    }

    /**
     * Get top tracked barcodes (for tracing)
     */
    private function getTopTrackedBarcodes($dateFrom, $dateTo, $limit = 5)
    {
        $barcodes = DB::table('product_tracking')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->whereNotNull('barcode')
            ->groupBy('barcode')
            ->select(
                'barcode',
                DB::raw('COUNT(*) as track_count'),
                DB::raw('SUM(output_weight) as total_output'),
                DB::raw('SUM(waste_amount) as total_waste'),
                DB::raw('MAX(created_at) as last_update')
            )
            ->orderByRaw('COUNT(*) DESC')
            ->limit($limit)
            ->get();

        return $barcodes->map(function ($barcode) {
            $fullReport = ProductTracking::fullReport($barcode->barcode);
            return [
                'barcode' => $barcode->barcode,
                'track_count' => $barcode->track_count,
                'total_output' => round($barcode->total_output ?? 0, 2),
                'total_waste' => round($barcode->total_waste ?? 0, 2),
                'last_update' => Carbon::parse($barcode->last_update),
                'stages' => count($fullReport['stages']) ?? 0,
                'workers' => count($fullReport['workers']) ?? 0,
                'current_status' => $fullReport['current_status'] ?? 'غير محدد',
            ];
        })->toArray();
    }

    /**
     * Get workers by stage
     */
    private function getWorkersByStage($dateFrom, $dateTo, $stage, $status, $worker)
    {
        $query = ProductTracking::whereBetween('product_tracking.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->whereNotNull('product_tracking.worker_id')
            ->whereNotNull('product_tracking.stage')
            ->leftJoin('users', 'product_tracking.worker_id', '=', 'users.id');

        if ($stage != 'all') {
            $query->where('product_tracking.stage', $stage);
        }

        if ($status != 'all') {
            $query->where('product_tracking.action', $status);
        }

        if ($worker) {
            $query->where('product_tracking.worker_id', $worker);
        }

        $results = $query->select(
                'product_tracking.stage',
                'product_tracking.worker_id',
                'users.name',
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(product_tracking.output_weight) as stage_output'),
                DB::raw('SUM(product_tracking.waste_amount) as stage_waste'),
                DB::raw('AVG(product_tracking.waste_percentage) as stage_waste_pct')
            )
            ->groupBy('product_tracking.stage', 'product_tracking.worker_id', 'users.name')
            ->get();

        // Organize by stage
        $byStage = [];
        foreach ($results as $item) {
            if (!isset($byStage[$item->stage])) {
                $byStage[$item->stage] = [];
            }
            $byStage[$item->stage][] = [
                'worker_id' => $item->worker_id,
                'worker_name' => $item->name,
                'count' => $item->total_count,
                'output' => round($item->stage_output ?? 0, 2),
                'waste' => round($item->stage_waste ?? 0, 2),
                'waste_pct' => round($item->stage_waste_pct ?? 0, 2),
            ];
        }

        return $byStage;
    }
}
