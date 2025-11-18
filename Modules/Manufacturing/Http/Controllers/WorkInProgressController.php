<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkInProgressController extends Controller
{
    /**
     * Display work in progress report
     */
    public function index(Request $request)
    {
        // Get filters
        $stageFilter = $request->input('stage');
        $delayFilter = $request->input('delay_hours');
        $workerFilter = $request->input('worker_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Get stuck items (القطع العالقة)
        $stuckItems = $this->getStuckItems($stageFilter, $delayFilter, $workerFilter, $dateFrom, $dateTo);

        // Get statistics
        $stats = $this->getStats($stuckItems);

        // Get workers for filter
        $workers = DB::table('users')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('manufacturing::reports.wip-report', compact('stuckItems', 'stats', 'workers'));
    }

    /**
     * Get items stuck in production (القطع التي بدأت ولم تنتهِ)
     */
    private function getStuckItems($stageFilter = null, $delayFilter = null, $workerFilter = null, $dateFrom = null, $dateTo = null)
    {
        // Stage 1: القطع التي تم تقسيمها لكن لم تدخل Stage 2
        $stage1Query = DB::table('stage1_stands as s1')
            ->select(
                's1.barcode',
                DB::raw('1 as current_stage'),
                DB::raw('"المرحلة 1 - التقسيم" as stage_name'),
                's1.weight as input_weight',
                's1.weight as output_weight',
                's1.waste',
                's1.created_by',
                'u.name as worker_name',
                's1.created_at as started_at',
                DB::raw('NULL as completed_at'),
                DB::raw('TIMESTAMPDIFF(HOUR, s1.created_at, NOW()) as hours_stuck')
            )
            ->leftJoin('users as u', 's1.created_by', '=', 'u.id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('stage2_processed as s2')
                    ->whereColumn('s2.parent_barcode', 's1.barcode');
            });

        // Stage 2: القطع التي تمت معالجتها لكن لم تدخل Stage 3
        $stage2Query = DB::table('stage2_processed as s2')
            ->select(
                's2.barcode',
                DB::raw('2 as current_stage'),
                DB::raw('"المرحلة 2 - المعالجة" as stage_name'),
                's2.input_weight',
                's2.output_weight',
                's2.waste',
                's2.created_by',
                'u.name as worker_name',
                's2.created_at as started_at',
                DB::raw('NULL as completed_at'),
                DB::raw('TIMESTAMPDIFF(HOUR, s2.created_at, NOW()) as hours_stuck')
            )
            ->leftJoin('users as u', 's2.created_by', '=', 'u.id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('stage3_coils as s3')
                    ->whereColumn('s3.parent_barcode', 's2.barcode');
            });

        // Stage 3: القطع التي تم لفها لكن لم تدخل Stage 4
        $stage3Query = DB::table('stage3_coils as s3')
            ->select(
                's3.barcode',
                DB::raw('3 as current_stage'),
                DB::raw('"المرحلة 3 - اللف" as stage_name'),
                's3.base_weight as input_weight',
                's3.total_weight as output_weight',
                's3.waste',
                's3.created_by',
                'u.name as worker_name',
                's3.created_at as started_at',
                DB::raw('NULL as completed_at'),
                DB::raw('TIMESTAMPDIFF(HOUR, s3.created_at, NOW()) as hours_stuck')
            )
            ->leftJoin('users as u', 's3.created_by', '=', 'u.id')
            ->where('s3.status', '!=', 'packed');

        // Stage 4: القطع في التعليب لكن لم تكتمل
        $stage4Query = DB::table('stage4_boxes as s4')
            ->select(
                's4.barcode',
                DB::raw('4 as current_stage'),
                DB::raw('"المرحلة 4 - التعليب" as stage_name'),
                's4.total_weight as input_weight',
                's4.total_weight as output_weight',
                's4.waste',
                's4.created_by',
                'u.name as worker_name',
                's4.created_at as started_at',
                DB::raw('NULL as completed_at'),
                DB::raw('TIMESTAMPDIFF(HOUR, s4.created_at, NOW()) as hours_stuck')
            )
            ->leftJoin('users as u', 's4.created_by', '=', 'u.id')
            ->whereIn('s4.status', ['packing']);

        // Apply stage filter
        if ($stageFilter) {
            $queries = [
                1 => $stage1Query,
                2 => $stage2Query,
                3 => $stage3Query,
                4 => $stage4Query
            ];
            $selectedQuery = $queries[$stageFilter] ?? null;
            if ($selectedQuery) {
                $allItems = $selectedQuery->get();
            } else {
                $allItems = collect();
            }
        } else {
            // Union all queries
            $allItems = $stage1Query
                ->unionAll($stage2Query)
                ->unionAll($stage3Query)
                ->unionAll($stage4Query)
                ->get();
        }

        // Apply additional filters
        if ($delayFilter) {
            $allItems = $allItems->filter(function ($item) use ($delayFilter) {
                return $item->hours_stuck >= $delayFilter;
            });
        }

        if ($workerFilter) {
            $allItems = $allItems->filter(function ($item) use ($workerFilter) {
                return $item->created_by == $workerFilter;
            });
        }

        if ($dateFrom) {
            $allItems = $allItems->filter(function ($item) use ($dateFrom) {
                return Carbon::parse($item->started_at)->gte(Carbon::parse($dateFrom));
            });
        }

        if ($dateTo) {
            $allItems = $allItems->filter(function ($item) use ($dateTo) {
                return Carbon::parse($item->started_at)->lte(Carbon::parse($dateTo)->endOfDay());
            });
        }

        return $allItems->sortByDesc('hours_stuck')->values();
    }

    /**
     * Calculate statistics
     */
    private function getStats($stuckItems)
    {
        $total = $stuckItems->count();
        
        // Group by stage
        $byStage = $stuckItems->groupBy('current_stage')->map->count();
        
        // Average delay in hours
        $avgDelay = $stuckItems->avg('hours_stuck') ?? 0;
        
        // Critical items (stuck > 24 hours)
        $critical = $stuckItems->filter(function ($item) {
            return $item->hours_stuck > 24;
        })->count();

        // Total weight stuck
        $totalWeight = $stuckItems->sum('output_weight');

        // Items by worker
        $byWorker = $stuckItems->groupBy('worker_name')->map->count()->sortDesc()->take(5);

        return [
            'total' => $total,
            'by_stage' => [
                'stage1' => $byStage->get(1, 0),
                'stage2' => $byStage->get(2, 0),
                'stage3' => $byStage->get(3, 0),
                'stage4' => $byStage->get(4, 0),
            ],
            'avg_delay_hours' => round($avgDelay, 1),
            'critical_count' => $critical,
            'total_weight_kg' => round($totalWeight, 2),
            'top_workers' => $byWorker,
        ];
    }

    /**
     * Get WIP statistics for API/AJAX
     */
    public function stats()
    {
        $stuckItems = $this->getStuckItems();
        $stats = $this->getStats($stuckItems);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Export WIP report to Excel
     */
    public function export(Request $request)
    {
        // TODO: Implement Excel export
        return response()->json([
            'message' => 'Export functionality coming soon'
        ]);
    }
}
