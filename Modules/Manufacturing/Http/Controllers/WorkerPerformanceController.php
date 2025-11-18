<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkerPerformanceController extends Controller
{
    /**
     * Display workers performance list
     */
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(7)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $shiftType = $request->input('shift_type');
        $stageFilter = $request->input('stage');

        // Get all workers with their performance metrics
        $workers = $this->getWorkersPerformance($dateFrom, $dateTo, $shiftType, $stageFilter);

        // Get overall statistics
        $overallStats = $this->getOverallStats($workers);

        return view('manufacturing::reports.worker-performance-index', compact('workers', 'overallStats', 'dateFrom', 'dateTo'));
    }

    /**
     * Show detailed performance for specific worker
     */
    public function show(Request $request, $workerId)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Get worker info
        $worker = DB::table('users')->where('id', $workerId)->first();
        
        if (!$worker) {
            return redirect()->route('manufacturing.reports.worker-performance')
                ->with('error', 'العامل غير موجود');
        }

        // Get detailed metrics
        $metrics = $this->getWorkerDetailedMetrics($workerId, $dateFrom, $dateTo);
        
        // Get performance by stage
        $byStage = $this->getWorkerPerformanceByStage($workerId, $dateFrom, $dateTo);
        
        // Get daily performance trend
        $dailyTrend = $this->getWorkerDailyTrend($workerId, $dateFrom, $dateTo);
        
        // Get comparison with team average
        $teamComparison = $this->compareWithTeamAverage($workerId, $dateFrom, $dateTo);

        return view('manufacturing::reports.worker-performance-show', compact(
            'worker',
            'metrics',
            'byStage',
            'dailyTrend',
            'teamComparison',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Get workers performance metrics
     */
    private function getWorkersPerformance($dateFrom, $dateTo, $shiftType = null, $stageFilter = null)
    {
        $workers = [];

        // Stage 1 Performance
        $stage1Data = DB::table('stage1_stands as s1')
            ->select(
                's1.created_by as worker_id',
                'u.name as worker_name',
                DB::raw('COUNT(*) as items_count'),
                DB::raw('SUM(s1.weight) as total_output'),
                DB::raw('SUM(s1.waste) as total_waste'),
                DB::raw('AVG((s1.waste / s1.weight) * 100) as avg_waste_percentage')
            )
            ->leftJoin('users as u', 's1.created_by', '=', 'u.id')
            ->whereBetween('s1.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->whereNotNull('s1.created_by')
            ->groupBy('s1.created_by', 'u.name');

        if (!$stageFilter || $stageFilter == 1) {
            $stage1Results = $stage1Data->get();
            foreach ($stage1Results as $row) {
                $key = $row->worker_id;
                if (!isset($workers[$key])) {
                    $workers[$key] = [
                        'worker_id' => $row->worker_id,
                        'worker_name' => $row->worker_name ?? 'غير محدد',
                        'stage1' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                        'stage2' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                        'stage3' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                        'stage4' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                    ];
                }
                $workers[$key]['stage1'] = [
                    'items' => $row->items_count,
                    'output' => $row->total_output,
                    'waste' => $row->total_waste,
                    'waste_pct' => round($row->avg_waste_percentage, 2)
                ];
            }
        }

        // Stage 2 Performance
        if (!$stageFilter || $stageFilter == 2) {
            $stage2Data = DB::table('stage2_processed as s2')
                ->select(
                    's2.created_by as worker_id',
                    'u.name as worker_name',
                    DB::raw('COUNT(*) as items_count'),
                    DB::raw('SUM(s2.output_weight) as total_output'),
                    DB::raw('SUM(s2.waste) as total_waste'),
                    DB::raw('AVG((s2.waste / s2.input_weight) * 100) as avg_waste_percentage')
                )
                ->leftJoin('users as u', 's2.created_by', '=', 'u.id')
                ->whereBetween('s2.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->whereNotNull('s2.created_by')
                ->groupBy('s2.created_by', 'u.name')
                ->get();

            foreach ($stage2Data as $row) {
                $key = $row->worker_id;
                if (!isset($workers[$key])) {
                    $workers[$key] = [
                        'worker_id' => $row->worker_id,
                        'worker_name' => $row->worker_name ?? 'غير محدد',
                        'stage1' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                        'stage2' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                        'stage3' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                        'stage4' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                    ];
                }
                $workers[$key]['stage2'] = [
                    'items' => $row->items_count,
                    'output' => $row->total_output,
                    'waste' => $row->total_waste,
                    'waste_pct' => round($row->avg_waste_percentage, 2)
                ];
            }
        }

        // Stage 3 Performance
        if (!$stageFilter || $stageFilter == 3) {
            $stage3Data = DB::table('stage3_coils as s3')
                ->select(
                    's3.created_by as worker_id',
                    'u.name as worker_name',
                    DB::raw('COUNT(*) as items_count'),
                    DB::raw('SUM(s3.total_weight) as total_output'),
                    DB::raw('SUM(s3.waste) as total_waste'),
                    DB::raw('AVG((s3.waste / s3.base_weight) * 100) as avg_waste_percentage')
                )
                ->leftJoin('users as u', 's3.created_by', '=', 'u.id')
                ->whereBetween('s3.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->whereNotNull('s3.created_by')
                ->groupBy('s3.created_by', 'u.name')
                ->get();

            foreach ($stage3Data as $row) {
                $key = $row->worker_id;
                if (!isset($workers[$key])) {
                    $workers[$key] = [
                        'worker_id' => $row->worker_id,
                        'worker_name' => $row->worker_name ?? 'غير محدد',
                        'stage1' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                        'stage2' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                        'stage3' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                        'stage4' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                    ];
                }
                $workers[$key]['stage3'] = [
                    'items' => $row->items_count,
                    'output' => $row->total_output,
                    'waste' => $row->total_waste,
                    'waste_pct' => round($row->avg_waste_percentage, 2)
                ];
            }
        }

        // Stage 4 Performance
        if (!$stageFilter || $stageFilter == 4) {
            $stage4Data = DB::table('stage4_boxes as s4')
                ->select(
                    's4.created_by as worker_id',
                    'u.name as worker_name',
                    DB::raw('COUNT(*) as items_count'),
                    DB::raw('SUM(s4.total_weight) as total_output'),
                    DB::raw('SUM(s4.waste) as total_waste'),
                    DB::raw('AVG((s4.waste / s4.total_weight) * 100) as avg_waste_percentage')
                )
                ->leftJoin('users as u', 's4.created_by', '=', 'u.id')
                ->whereBetween('s4.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->whereNotNull('s4.created_by')
                ->groupBy('s4.created_by', 'u.name')
                ->get();

            foreach ($stage4Data as $row) {
                $key = $row->worker_id;
                if (!isset($workers[$key])) {
                    $workers[$key] = [
                        'worker_id' => $row->worker_id,
                        'worker_name' => $row->worker_name ?? 'غير محدد',
                        'stage1' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                        'stage2' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                        'stage3' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                        'stage4' => ['items' => 0, 'output' => 0, 'waste' => 0, 'waste_pct' => 0],
                    ];
                }
                $workers[$key]['stage4'] = [
                    'items' => $row->items_count,
                    'output' => $row->total_output,
                    'waste' => $row->total_waste,
                    'waste_pct' => round($row->avg_waste_percentage, 2)
                ];
            }
        }

        // Calculate totals and efficiency for each worker
        foreach ($workers as &$worker) {
            $totalItems = $worker['stage1']['items'] + $worker['stage2']['items'] + 
                         $worker['stage3']['items'] + $worker['stage4']['items'];
            $totalOutput = $worker['stage1']['output'] + $worker['stage2']['output'] + 
                          $worker['stage3']['output'] + $worker['stage4']['output'];
            $totalWaste = $worker['stage1']['waste'] + $worker['stage2']['waste'] + 
                         $worker['stage3']['waste'] + $worker['stage4']['waste'];
            
            $avgWaste = $totalOutput > 0 ? ($totalWaste / $totalOutput) * 100 : 0;
            $efficiency = 100 - $avgWaste;
            
            $worker['totals'] = [
                'items' => $totalItems,
                'output' => round($totalOutput, 2),
                'waste' => round($totalWaste, 2),
                'waste_pct' => round($avgWaste, 2),
                'efficiency' => round($efficiency, 2)
            ];
        }

        // Sort by total items DESC
        usort($workers, function($a, $b) {
            return $b['totals']['items'] - $a['totals']['items'];
        });

        return collect($workers);
    }

    /**
     * Get detailed metrics for specific worker
     */
    private function getWorkerDetailedMetrics($workerId, $dateFrom, $dateTo)
    {
        $metrics = [
            'total_items' => 0,
            'total_output_kg' => 0,
            'total_waste_kg' => 0,
            'avg_waste_percentage' => 0,
            'efficiency' => 0,
            'best_stage' => null,
            'worst_stage' => null,
            'working_days' => 0,
            'avg_items_per_day' => 0,
        ];

        // Get aggregated data from all stages
        $allRecords = collect();

        // Stage 1
        $s1 = DB::table('stage1_stands')
            ->select('weight as output_weight', 'waste', 'created_at')
            ->where('created_by', $workerId)
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->get();
        $allRecords = $allRecords->merge($s1);

        // Stage 3
        $s3 = DB::table('stage3_coils')
            ->select('total_weight as output_weight', 'waste', 'created_at')
            ->where('created_by', $workerId)
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->get();
        $allRecords = $allRecords->merge($s3);

        // Stage 1
        $s1 = DB::table('stage1_stands')
            ->select('weight as output_weight', 'waste', 'created_at')
            ->where('created_by', $workerId)
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->get();
        $allRecords = $allRecords->merge($s1);

        // Stage 4
        $s4 = DB::table('stage4_boxes')
            ->select('total_weight as output_weight', 'waste', 'created_at')
            ->where('created_by', $workerId)
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->get();
        $allRecords = $allRecords->merge($s4);

        if ($allRecords->isNotEmpty()) {
            $metrics['total_items'] = $allRecords->count();
            $metrics['total_output_kg'] = round($allRecords->sum('output_weight'), 2);
            $metrics['total_waste_kg'] = round($allRecords->sum('waste'), 2);
            $metrics['avg_waste_percentage'] = $metrics['total_output_kg'] > 0 
                ? round(($metrics['total_waste_kg'] / $metrics['total_output_kg']) * 100, 2) 
                : 0;
            $metrics['efficiency'] = round(100 - $metrics['avg_waste_percentage'], 2);
            
            // Working days
            $workingDays = $allRecords->map(function($item) {
                return Carbon::parse($item->created_at)->format('Y-m-d');
            })->unique()->count();
            $metrics['working_days'] = $workingDays;
            $metrics['avg_items_per_day'] = $workingDays > 0 
                ? round($metrics['total_items'] / $workingDays, 1) 
                : 0;
        }

        return $metrics;
    }

    /**
     * Get worker performance by stage
     */
    private function getWorkerPerformanceByStage($workerId, $dateFrom, $dateTo)
    {
        $byStage = [];
        $tableNames = ['stage1_stands', 'stage2_processed', 'stage3_coils', 'stage4_boxes'];

        for ($i = 1; $i <= 4; $i++) {
            $tableName = $tableNames[$i - 1];
            $weightColumn = ($i == 1) ? 'weight' : (($i == 3) ? 'total_weight' : (($i == 4) ? 'total_weight' : 'output_weight'));
            
            $data = DB::table($tableName)
                ->select(
                    DB::raw("COUNT(*) as items"),
                    DB::raw("SUM({$weightColumn}) as output"),
                    DB::raw("SUM(waste) as waste")
                )
                ->where('created_by', $workerId)
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->first();

            $byStage["stage{$i}"] = [
                'items' => $data->items ?? 0,
                'output' => round($data->output ?? 0, 2),
                'waste' => round($data->waste ?? 0, 2),
                'waste_pct' => ($data->output ?? 0) > 0 
                    ? round((($data->waste ?? 0) / ($data->output ?? 0)) * 100, 2) 
                    : 0,
            ];
        }

        return $byStage;
    }

    /**
     * Get worker daily performance trend
     */
    private function getWorkerDailyTrend($workerId, $dateFrom, $dateTo)
    {
        // Get daily counts from all stages
        $dailyData = [];
        $tableNames = ['stage1_stands', 'stage2_processed', 'stage3_coils', 'stage4_boxes'];

        for ($i = 1; $i <= 4; $i++) {
            $tableName = $tableNames[$i - 1];
            $weightColumn = ($i == 1) ? 'weight' : (($i == 3) ? 'total_weight' : (($i == 4) ? 'total_weight' : 'output_weight'));
            
            $records = DB::table($tableName)
                ->select(
                    DB::raw("DATE(created_at) as date"),
                    DB::raw("COUNT(*) as items"),
                    DB::raw("SUM({$weightColumn}) as output")
                )
                ->where('created_by', $workerId)
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->groupBy('date')
                ->get();

            foreach ($records as $record) {
                if (!isset($dailyData[$record->date])) {
                    $dailyData[$record->date] = ['items' => 0, 'output' => 0];
                }
                $dailyData[$record->date]['items'] += $record->items;
                $dailyData[$record->date]['output'] += $record->output;
            }
        }

        ksort($dailyData);
        return $dailyData;
    }

    /**
     * Compare worker with team average
     */
    private function compareWithTeamAverage($workerId, $dateFrom, $dateTo)
    {
        $workers = $this->getWorkersPerformance($dateFrom, $dateTo, null, null);
        
        $teamAvg = [
            'items' => $workers->avg('totals.items'),
            'output' => $workers->avg('totals.output'),
            'waste_pct' => $workers->avg('totals.waste_pct'),
            'efficiency' => $workers->avg('totals.efficiency'),
        ];

        $workerData = $workers->firstWhere('worker_id', $workerId);
        
        return [
            'team_avg' => $teamAvg,
            'worker' => $workerData ? $workerData['totals'] : null,
            'rank' => $workerData ? $workers->search(function($w) use ($workerId) {
                return $w['worker_id'] == $workerId;
            }) + 1 : null,
            'total_workers' => $workers->count(),
        ];
    }

    /**
     * Get overall statistics
     */
    private function getOverallStats($workers)
    {
        return [
            'total_workers' => $workers->count(),
            'total_items' => $workers->sum('totals.items'),
            'total_output' => round($workers->sum('totals.output'), 2),
            'avg_efficiency' => round($workers->avg('totals.efficiency'), 2),
            'top_performer' => $workers->sortByDesc('totals.efficiency')->first(),
            'most_productive' => $workers->sortByDesc('totals.items')->first(),
        ];
    }

    /**
     * Compare multiple workers
     */
    public function compare(Request $request)
    {
        // TODO: Implement worker comparison
        return response()->json([
            'message' => 'Comparison feature coming soon'
        ]);
    }
}
