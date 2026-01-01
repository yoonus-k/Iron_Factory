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
        
        // Get date ranges based on shift type
        $dateRange = $this->getDateRangeForShift($dateFrom, $dateTo, $shiftType);

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
            ->whereBetween('s1.created_at', [$dateRange['start'], $dateRange['end']])
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
                ->whereBetween('s2.created_at', [$dateRange['start'], $dateRange['end']])
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
                ->whereBetween('s3.created_at', [$dateRange['start'], $dateRange['end']])
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
                ->whereBetween('s4.created_at', [$dateRange['start'], $dateRange['end']])
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

        // Don't sum stages - each item only exists in one stage at a time
        // Sorting by Stage 1 items as the starting point of production
        usort($workers, function($a, $b) {
            return $b['stage1']['items'] - $a['stage1']['items'];
        });

        return collect($workers);
    }

    /**
     * Get detailed metrics for specific worker
     * Note: Metrics are calculated per-stage, not summed (same item flows through all stages)
     */
    private function getWorkerDetailedMetrics($workerId, $dateFrom, $dateTo)
    {
        $metrics = [
            'by_stage' => [],
            'working_days' => 0,
            'avg_items_per_day_stage1' => 0,
        ];

        // Get metrics per stage (not merged to avoid counting same item 4 times)
        $stages = [
            ['table' => 'stage1_stands', 'weight_col' => 'weight', 'stage_num' => 1],
            ['table' => 'stage2_processed', 'weight_col' => 'output_weight', 'stage_num' => 2],
            ['table' => 'stage3_coils', 'weight_col' => 'total_weight', 'stage_num' => 3],
            ['table' => 'stage4_boxes', 'weight_col' => 'total_weight', 'stage_num' => 4],
        ];

        foreach ($stages as $stage) {
            $data = DB::table($stage['table'])
                ->select(
                    DB::raw('COUNT(*) as items'),
                    DB::raw("SUM({$stage['weight_col']}) as output"),
                    DB::raw('SUM(waste) as waste')
                )
                ->where('created_by', $workerId)
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->first();

            $output = $data->output ?? 0;
            $waste = $data->waste ?? 0;
            
            $metrics['by_stage']['stage' . $stage['stage_num']] = [
                'items' => $data->items ?? 0,
                'output_kg' => round($output, 2),
                'waste_kg' => round($waste, 2),
                'waste_pct' => $output > 0 ? round(($waste / $output) * 100, 2) : 0,
                'efficiency' => $output > 0 ? round(100 - (($waste / $output) * 100), 2) : 0,
            ];
        }

        // Working days calculation based on Stage 1 (starting point)
        $workingDays = DB::table('stage1_stands')
            ->select(DB::raw('DATE(created_at) as date'))
            ->where('created_by', $workerId)
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->count();

        $metrics['working_days'] = $workingDays;
        $metrics['avg_items_per_day_stage1'] = $workingDays > 0 
            ? round($metrics['by_stage']['stage1']['items'] / $workingDays, 1) 
            : 0;

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
     * Shows Stage 1 production only (starting point) to avoid counting same item multiple times
     */
    private function getWorkerDailyTrend($workerId, $dateFrom, $dateTo)
    {
        // Get daily counts from Stage 1 only (starting point of production)
        $dailyData = DB::table('stage1_stands')
            ->select(
                DB::raw("DATE(created_at) as date"),
                DB::raw("COUNT(*) as items"),
                DB::raw("SUM(weight) as output")
            )
            ->where('created_by', $workerId)
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy('date')
            ->map(function($record) {
                return [
                    'items' => $record->items,
                    'output' => round($record->output, 2)
                ];
            })
            ->toArray();

        ksort($dailyData);
        return $dailyData;
    }

    /**
     * Compare worker with team average (Stage 1 only to avoid duplicate counting)
     */
    private function compareWithTeamAverage($workerId, $dateFrom, $dateTo)
    {
        $workers = $this->getWorkersPerformance($dateFrom, $dateTo, null, null);
        
        $teamAvg = [
            'items' => round($workers->avg('stage1.items'), 1),
            'output' => round($workers->avg('stage1.output'), 2),
            'waste_pct' => round($workers->avg('stage1.waste_pct'), 2),
            'efficiency' => round($workers->avg(function($w) {
                $output = $w['stage1']['output'];
                $waste = $w['stage1']['waste'];
                return $output > 0 ? 100 - (($waste / $output) * 100) : 0;
            }), 2),
        ];

        $workerData = $workers->firstWhere('worker_id', $workerId);
        
        return [
            'team_avg' => $teamAvg,
            'worker' => $workerData ? $workerData['stage1'] : null,
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
            'total_items_stage1' => $workers->sum('stage1.items'),
            'total_output_stage1' => round($workers->sum('stage1.output'), 2),
            'avg_efficiency_stage1' => round($workers->avg(function($w) {
                $output = $w['stage1']['output'];
                $waste = $w['stage1']['waste'];
                return $output > 0 ? 100 - (($waste / $output) * 100) : 0;
            }), 2),
            'top_performer_stage1' => $workers->sortByDesc(function($w) {
                $output = $w['stage1']['output'];
                $waste = $w['stage1']['waste'];
                return $output > 0 ? 100 - (($waste / $output) * 100) : 0;
            })->first(),
            'most_productive_stage1' => $workers->sortByDesc('stage1.items')->first(),
        ];
    }

    /**
     * Get date range based on shift type
     */
    private function getDateRangeForShift($dateFrom, $dateTo, $shiftType = null)
    {
        $from = Carbon::parse($dateFrom);
        $to = Carbon::parse($dateTo);

        // If no shift type specified, use full day range
        if (!$shiftType) {
            return [
                'start' => $from->copy()->startOfDay()->toDateTimeString(),
                'end' => $to->copy()->endOfDay()->toDateTimeString(),
            ];
        }

        // If same date, get shift time range for that date
        if ($dateFrom === $dateTo) {
            if ($shiftType === 'morning') {
                // Morning shift: 6am-6pm
                return [
                    'start' => $from->copy()->setTime(6, 0, 0)->toDateTimeString(),
                    'end' => $from->copy()->setTime(18, 0, 0)->toDateTimeString(),
                ];
            } else {
                // Evening shift: 6pm-6am next day
                return [
                    'start' => $from->copy()->setTime(18, 0, 0)->toDateTimeString(),
                    'end' => $from->copy()->addDay()->setTime(6, 0, 0)->toDateTimeString(),
                ];
            }
        }

        // For date range
        if ($shiftType === 'morning') {
            // Morning shift range: from 6am first day to 6pm last day
            return [
                'start' => $from->copy()->setTime(6, 0, 0)->toDateTimeString(),
                'end' => $to->copy()->setTime(18, 0, 0)->toDateTimeString(),
            ];
        } else {
            // Evening shift range: from 6pm first day to 6am day after last
            return [
                'start' => $from->copy()->setTime(18, 0, 0)->toDateTimeString(),
                'end' => $to->copy()->addDay()->setTime(6, 0, 0)->toDateTimeString(),
            ];
        }
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
