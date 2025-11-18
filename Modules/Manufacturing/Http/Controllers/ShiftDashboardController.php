<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftDashboardController extends Controller
{
    /**
     * Display shift dashboard
     */
    public function index(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $shiftType = $request->input('shift', 'evening'); // Default to evening (night shift)

        // Get shift time range
        $timeRange = $this->getShiftTimeRange($date, $shiftType);

        // Get shift summary
        $summary = $this->getShiftSummary($date, $shiftType);
        
        // Get top performers
        $topPerformers = $this->getTopPerformers($date, $shiftType);
        
        // Get production by stage
        $byStage = $this->getProductionByStage($date, $shiftType);
        
        // Get WIP count
        $wipCount = $this->getWIPCount();
        
        // Get hourly production trend
        $hourlyTrend = $this->getHourlyProductionTrend($date, $shiftType);

        return view('manufacturing::reports.shift-dashboard', compact(
            'summary',
            'topPerformers',
            'byStage',
            'wipCount',
            'hourlyTrend',
            'date',
            'shiftType'
        ));
    }

    /**
     * Get night shift summary (for morning display)
     */
    public function nightShiftSummary()
    {
        // Get yesterday's evening shift (night shift)
        $yesterday = now()->subDay()->format('Y-m-d');
        
        $summary = $this->getShiftSummary($yesterday, 'evening');
        $topPerformers = $this->getTopPerformers($yesterday, 'evening', 5);
        $issues = $this->getShiftIssues($yesterday, 'evening');

        return view('manufacturing::reports.night-shift-summary', compact(
            'summary',
            'topPerformers',
            'issues',
            'yesterday'
        ));
    }

    /**
     * Get live statistics (AJAX)
     */
    public function liveStats(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $shiftType = $request->input('shift_type', 'evening');

        $stats = [
            'summary' => $this->getShiftSummary($date, $shiftType),
            'wip_count' => $this->getWIPCount(),
            'active_workers' => $this->getActiveWorkersCount($date, $shiftType),
            'current_efficiency' => $this->getCurrentEfficiency($date, $shiftType),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Get shift summary statistics
     */
    private function getShiftSummary($date, $shiftType)
    {
        // Determine time range based on shift type
        $timeRange = $this->getShiftTimeRange($date, $shiftType);
        
        $summary = [
            'total_items' => 0,
            'total_output_kg' => 0,
            'total_waste_kg' => 0,
            'waste_percentage' => 0,
            'efficiency' => 0,
            'workers_count' => 0,
            'avg_items_per_worker' => 0,
        ];

        // Aggregate from all stages
        $tableNames = ['stage1_stands', 'stage2_processed', 'stage3_coils', 'stage4_boxes'];
        for ($i = 1; $i <= 4; $i++) {
            $tableName = $tableNames[$i - 1];
            $weightColumn = ($i == 1) ? 'weight' : (($i == 3) ? 'total_weight' : (($i == 4) ? 'total_weight' : 'output_weight'));
            
            $stageData = DB::table($tableName)
                ->select(
                    DB::raw('COUNT(*) as items'),
                    DB::raw("SUM({$weightColumn}) as output"),
                    DB::raw('SUM(waste) as waste'),
                    DB::raw('COUNT(DISTINCT created_by) as workers')
                )
                ->whereBetween('created_at', [$timeRange['start'], $timeRange['end']])
                ->first();

            if ($stageData) {
                $summary['total_items'] += $stageData->items;
                $summary['total_output_kg'] += $stageData->output ?? 0;
                $summary['total_waste_kg'] += $stageData->waste ?? 0;
                $summary['workers_count'] = max($summary['workers_count'], $stageData->workers ?? 0);
            }
        }

        // Calculate percentages
        if ($summary['total_output_kg'] > 0) {
            $summary['waste_percentage'] = round(($summary['total_waste_kg'] / $summary['total_output_kg']) * 100, 2);
            $summary['efficiency'] = round(100 - $summary['waste_percentage'], 2);
        }

        if ($summary['workers_count'] > 0) {
            $summary['avg_items_per_worker'] = round($summary['total_items'] / $summary['workers_count'], 1);
        }

        $summary['total_output_kg'] = round($summary['total_output_kg'], 2);
        $summary['total_waste_kg'] = round($summary['total_waste_kg'], 2);

        return $summary;
    }

    /**
     * Get top performers in shift
     */
    private function getTopPerformers($date, $shiftType, $limit = 10)
    {
        $timeRange = $this->getShiftTimeRange($date, $shiftType);
        $performers = [];

        // Collect data from all stages
        $tableNames = ['stage1_stands', 'stage2_processed', 'stage3_coils', 'stage4_boxes'];
        for ($i = 1; $i <= 4; $i++) {
            $tableName = $tableNames[$i - 1];
            $weightColumn = ($i == 1) ? 'weight' : (($i == 3) ? 'total_weight' : (($i == 4) ? 'total_weight' : 'output_weight'));
            
            $stageData = DB::table($tableName . ' as s')
                ->select(
                    's.created_by as worker_id',
                    'u.name as worker_name',
                    DB::raw('COUNT(*) as items'),
                    DB::raw("SUM(s.{$weightColumn}) as output"),
                    DB::raw('SUM(s.waste) as waste')
                )
                ->leftJoin('users as u', 's.created_by', '=', 'u.id')
                ->whereBetween('s.created_at', [$timeRange['start'], $timeRange['end']])
                ->whereNotNull('s.created_by')
                ->groupBy('s.created_by', 'u.name')
                ->get();

            foreach ($stageData as $row) {
                $key = $row->worker_id;
                if (!isset($performers[$key])) {
                    $performers[$key] = [
                        'worker_id' => $row->worker_id,
                        'worker_name' => $row->worker_name ?? 'غير محدد',
                        'items' => 0,
                        'output' => 0,
                        'waste' => 0,
                    ];
                }
                $performers[$key]['items'] += $row->items;
                $performers[$key]['output'] += $row->output ?? 0;
                $performers[$key]['waste'] += $row->waste ?? 0;
            }
        }

        // Calculate efficiency and sort
        foreach ($performers as &$performer) {
            $performer['output'] = round($performer['output'], 2);
            $performer['waste'] = round($performer['waste'], 2);
            $performer['waste_pct'] = $performer['output'] > 0 
                ? round(($performer['waste'] / $performer['output']) * 100, 2) 
                : 0;
            $performer['efficiency'] = round(100 - $performer['waste_pct'], 2);
        }

        // Sort by efficiency then by items
        usort($performers, function($a, $b) {
            if ($a['efficiency'] == $b['efficiency']) {
                return $b['items'] - $a['items'];
            }
            return $b['efficiency'] <=> $a['efficiency'];
        });

        return array_slice($performers, 0, $limit);
    }

    /**
     * Get production by stage
     */
    private function getProductionByStage($date, $shiftType)
    {
        $timeRange = $this->getShiftTimeRange($date, $shiftType);
        $byStage = [];
        $tableNames = ['stage1_stands', 'stage2_processed', 'stage3_coils', 'stage4_boxes'];
        $stageNames = ['المرحلة 1 - التقسيم', 'المرحلة 2 - المعالجة', 'المرحلة 3 - اللف', 'المرحلة 4 - التعليب'];

        for ($i = 1; $i <= 4; $i++) {
            $tableName = $tableNames[$i - 1];
            $weightColumn = ($i == 1) ? 'weight' : (($i == 3) ? 'total_weight' : (($i == 4) ? 'total_weight' : 'output_weight'));
            
            $data = DB::table($tableName)
                ->select(
                    DB::raw('COUNT(*) as items'),
                    DB::raw("SUM({$weightColumn}) as output"),
                    DB::raw('SUM(waste) as waste')
                )
                ->whereBetween('created_at', [$timeRange['start'], $timeRange['end']])
                ->first();

            $byStage[] = [
                'stage' => $i,
                'name' => $stageNames[$i - 1],
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
     * Get WIP count
     */
    private function getWIPCount()
    {
        $count = 0;

        // Stage 1 incomplete
        $count += DB::table('stage1_stands as s1')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('stage2_processed as s2')
                    ->whereColumn('s2.parent_barcode', 's1.barcode');
            })
            ->count();

        // Stage 2 incomplete
        $count += DB::table('stage2_processed as s2')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('stage3_coils as s3')
                    ->whereColumn('s3.parent_barcode', 's2.barcode');
            })
            ->count();

        // Stage 3 incomplete
        $count += DB::table('stage3_coils as s3')
            ->where('status', '!=', 'packed')
            ->count();

        // Stage 4 incomplete
        $count += DB::table('stage4_boxes')
            ->where('status', 'packing')
            ->count();

        return $count;
    }

    /**
     * Get hourly production trend
     */
    private function getHourlyProductionTrend($date, $shiftType)
    {
        $timeRange = $this->getShiftTimeRange($date, $shiftType);
        $hourlyData = [];

        // Initialize hours
        $start = Carbon::parse($timeRange['start']);
        $end = Carbon::parse($timeRange['end']);
        $hours = [];
        
        while ($start <= $end) {
            $hour = $start->format('H:00');
            $hours[$hour] = 0;
            $start->addHour();
        }

        // Aggregate from all stages
        $tableNames = ['stage1_stands', 'stage2_processed', 'stage3_coils', 'stage4_boxes'];
        for ($i = 1; $i <= 4; $i++) {
            $tableName = $tableNames[$i - 1];
            
            $records = DB::table($tableName)
                ->select(
                    DB::raw("HOUR(created_at) as hour"),
                    DB::raw('COUNT(*) as items')
                )
                ->whereBetween('created_at', [$timeRange['start'], $timeRange['end']])
                ->groupBy('hour')
                ->get();

            foreach ($records as $record) {
                $hourKey = str_pad($record->hour, 2, '0', STR_PAD_LEFT) . ':00';
                if (isset($hours[$hourKey])) {
                    $hours[$hourKey] += $record->items;
                }
            }
        }

        // Convert to array format for Chart.js
        $result = [];
        foreach ($hours as $hour => $count) {
            $result[] = [
                'hour' => str_replace(':00', '', $hour),
                'items' => $count
            ];
        }

        return $result;
    }

    /**
     * Get shift issues (delays, high waste, etc.)
     */
    private function getShiftIssues($date, $shiftType)
    {
        $timeRange = $this->getShiftTimeRange($date, $shiftType);
        $issues = [];

        // High waste items
        $tableNames = ['stage1_stands', 'stage2_processed', 'stage3_coils', 'stage4_boxes'];
        for ($i = 1; $i <= 4; $i++) {
            $tableName = $tableNames[$i - 1];
            $weightColumn = $i == 4 ? 'input_weight' : 'input_weight';
            
            $highWaste = DB::table($tableName)
                ->select(
                    'barcode',
                    'waste',
                    'input_weight',
                    DB::raw("(waste / {$weightColumn}) * 100 as waste_pct")
                )
                ->whereBetween('created_at', [$timeRange['start'], $timeRange['end']])
                ->havingRaw('waste_pct > 10') // More than 10% waste
                ->orderByDesc('waste_pct')
                ->limit(5)
                ->get();

            foreach ($highWaste as $item) {
                $issues[] = [
                    'type' => 'high_waste',
                    'stage' => $i,
                    'barcode' => $item->barcode,
                    'waste_pct' => round($item->waste_pct, 2),
                    'message' => "هدر عالي ({$item->waste_pct}%) في المرحلة {$i}"
                ];
            }
        }

        return $issues;
    }

    /**
     * Get active workers count
     */
    private function getActiveWorkersCount($date, $shiftType)
    {
        $timeRange = $this->getShiftTimeRange($date, $shiftType);
        $workers = [];

        for ($i = 1; $i <= 4; $i++) {
            $tableName = "stage{$i}_records";
            
            $stageWorkers = DB::table($tableName)
                ->select('created_by')
                ->whereBetween('created_at', [$timeRange['start'], $timeRange['end']])
                ->whereNotNull('created_by')
                ->distinct()
                ->pluck('created_by')
                ->toArray();

            $workers = array_merge($workers, $stageWorkers);
        }

        return count(array_unique($workers));
    }

    /**
     * Get current efficiency
     */
    private function getCurrentEfficiency($date, $shiftType)
    {
        $summary = $this->getShiftSummary($date, $shiftType);
        return $summary['efficiency'];
    }

    /**
     * Get shift time range
     */
    public function getShiftTimeRange($date, $shiftType)
    {
        $dateObj = Carbon::parse($date);

        if ($shiftType === 'morning') {
            // Morning shift: 6 AM - 6 PM
            return [
                'start' => $dateObj->copy()->setTime(6, 0, 0)->toDateTimeString(),
                'end' => $dateObj->copy()->setTime(18, 0, 0)->toDateTimeString(),
            ];
        } else {
            // Evening/Night shift: 6 PM - 6 AM next day
            return [
                'start' => $dateObj->copy()->setTime(18, 0, 0)->toDateTimeString(),
                'end' => $dateObj->copy()->addDay()->setTime(6, 0, 0)->toDateTimeString(),
            ];
        }
    }
}
