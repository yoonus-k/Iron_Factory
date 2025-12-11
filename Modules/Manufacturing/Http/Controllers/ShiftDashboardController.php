<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Worker;
use App\Models\WorkerTeam;
use App\Models\ShiftAssignment;
use App\Models\ShiftHandover;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ShiftDashboardController extends Controller
{
    /**
     * Display shift dashboard
     */
    public function index(Request $request)
    {
        try {
            $date = $request->input('date', now()->format('Y-m-d'));
            $shiftType = $request->input('shift', 'evening');
            $fromDate = $request->input('from_date', $date);
            $toDate = $request->input('to_date', $date);

            // Get shift time range
            $timeRange = $this->getShiftTimeRange($date, $shiftType);

            // Get date range for filtering
            $dateRange = $this->getDateRange($fromDate, $toDate);

            // Get shift assignment
            $shiftAssignment = $this->getShiftAssignment($date, $shiftType);

            // Get shift summary
            $summary = $this->getShiftSummary($date, $shiftType, $dateRange);

            // Get top performers
            $topPerformers = $this->getTopPerformers($date, $shiftType, 10, $dateRange);

            // Get production by stage
            $byStage = $this->getProductionByStage($date, $shiftType, $dateRange);

            // Get WIP count
            $wipCount = $this->getWIPCount();

            // Get hourly production trend
            $hourlyTrend = $this->getHourlyProductionTrend($date, $shiftType, $dateRange);

            // Get shift handovers
            $handovers = $this->getShiftHandovers($date, $shiftType, $dateRange);

            // Get worker attendance
            $attendance = $this->getWorkerAttendance($date, $shiftType, $dateRange);

            // Get shift comparison (current vs previous)
            $comparison = $this->getShiftComparison($date, $shiftType, $dateRange);

            // Get stage efficiency details
            $stageEfficiency = $this->getStageEfficiencyDetails($date, $shiftType, $dateRange);

            // Get active teams
            $activeTeams = $this->getActiveTeams($date, $shiftType, $dateRange);

            // Get issues and alerts
            $issues = $this->getShiftIssues($date, $shiftType, $dateRange);

            // Get yesterday's date for comparison
            $yesterday = Carbon::parse($date)->format('Y-m-d');

            return view('manufacturing::reports.shift-dashboard', compact(
                'summary',
                'topPerformers',
                'byStage',
                'wipCount',
                'hourlyTrend',
                'date',
                'shiftType',
                'shiftAssignment',
                'handovers',
                'attendance',
                'comparison',
                'stageEfficiency',
                'activeTeams',
                'timeRange',
                'issues',
                'yesterday'
            ));
        } catch (\Exception $e) {
            \Log::error('Shift Dashboard Error: ' . $e->getMessage());
            return view('manufacturing::reports.shift-dashboard')->with('error', 'حدث خطأ أثناء جلب البيانات');
        }
    }

    /**
     * Get date range for filtering
     */
    private function getDateRange($fromDate = null, $toDate = null)
    {
        $from = $fromDate ? Carbon::parse($fromDate)->startOfDay() : Carbon::now()->startOfDay();
        $to = $toDate ? Carbon::parse($toDate)->endOfDay() : Carbon::now()->endOfDay();

        return [
            'start' => $from->toDateTimeString(),
            'end' => $to->toDateTimeString(),
        ];
    }

    /**
     * Get shift assignment details
     */
    private function getShiftAssignment($date, $shiftType)
    {
        try {
            $assignment = ShiftAssignment::where('shift_date', $date)
                ->where('shift_type', $shiftType)
                ->with(['user', 'supervisor'])
                ->first();

            if (!$assignment) {
                return null;
            }

            // Get workers from worker_ids array
            $workers = [];
            if ($assignment->worker_ids && is_array($assignment->worker_ids)) {
                $workers = User::whereIn('id', $assignment->worker_ids)->get();
            }

            return [
                'shift_code' => $assignment->shift_code ?? 'N/A',
                'supervisor' => $assignment->supervisor,
                'total_workers' => $assignment->total_workers ?? count($assignment->worker_ids ?? []),
                'workers' => $workers,
                'status' => $assignment->status,
                'status_name' => $assignment->getStatusNameAttribute(),
                'start_time' => $assignment->start_time,
                'end_time' => $assignment->end_time,
                'actual_end_time' => $assignment->actual_end_time,
                'notes' => $assignment->notes,
            ];
        } catch (\Exception $e) {
            \Log::error('Get Shift Assignment Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get shift handovers
     */
    private function getShiftHandovers($date, $shiftType, $dateRange = null)
    {
        try {
            if (!$dateRange) {
                $dateRange = $this->getShiftTimeRange($date, $shiftType);
            }

            $handovers = ShiftHandover::whereBetween('handover_time', [$dateRange['start'], $dateRange['end']])
                ->with(['fromUser', 'toUser', 'approver'])
                ->orderBy('handover_time', 'desc')
                ->get();

            return $handovers->map(function($handover) {
                return [
                    'id' => $handover->id,
                    'from_user' => $handover->fromUser ? $handover->fromUser->name : 'غير محدد',
                    'to_user' => $handover->toUser ? $handover->toUser->name : 'غير محدد',
                    'stage_number' => $handover->stage_number,
                    'stage_name' => $this->getStageName($handover->stage_number),
                    'handover_items' => $handover->handover_items,
                    'items_count' => is_array($handover->handover_items) ? count($handover->handover_items) : 0,
                    'notes' => $handover->notes,
                    'handover_time' => $handover->handover_time,
                    'supervisor_approved' => (bool) $handover->supervisor_approved,
                    'approver' => $handover->approver ? $handover->approver->name : null,
                ];
            })->toArray();
        } catch (\Exception $e) {
            \Log::error('Get Shift Handovers Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get worker attendance
     */
    private function getWorkerAttendance($date, $shiftType, $dateRange = null)
    {
        try {
            if (!$dateRange) {
                $dateRange = $this->getShiftTimeRange($date, $shiftType);
            }
            $attendance = [];

            // Get all workers who created records in this shift
            $tableNames = ['stage1_stands', 'stage2_processed', 'stage3_coils', 'stage4_boxes'];
            $workerIds = [];

            foreach ($tableNames as $tableName) {
                try {
                    $ids = DB::table($tableName)
                        ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                        ->whereNotNull('created_by')
                        ->distinct()
                        ->pluck('created_by')
                        ->toArray();

                    $workerIds = array_merge($workerIds, $ids);
                } catch (\Exception $e) {
                    \Log::warning("Table {$tableName} not found or error: " . $e->getMessage());
                    continue;
                }
            }

            $workerIds = array_unique($workerIds);

            // Get worker details with their production
            foreach ($workerIds as $workerId) {
                $user = User::with('worker')->find($workerId);
                if (!$user) continue;

                $workerProduction = $this->getWorkerProductionDetails($workerId, $date, $shiftType);

                // Get worker position
                $position = 'عامل';
                $workerCode = 'N/A';
                if ($user->worker) {
                    $workerCode = $user->worker->worker_code ?? 'N/A';
                    $position = $user->worker->position_name ?? $user->worker->position ?? 'عامل';
                }

                $attendance[] = [
                    'worker_id' => $workerId,
                    'worker_name' => $user->name ?? 'غير محدد',
                    'worker_code' => $workerCode,
                    'position' => $position,
                    'stages_worked' => $workerProduction['stages_worked'],
                    'total_items' => $workerProduction['total_items'],
                    'total_output' => $workerProduction['total_output'],
                    'total_waste' => $workerProduction['total_waste'],
                    'efficiency' => $workerProduction['efficiency'],
                    'first_activity' => $workerProduction['first_activity'],
                    'last_activity' => $workerProduction['last_activity'],
                    'hours_worked' => $workerProduction['hours_worked'],
                ];
            }

            // Sort by total items descending
            usort($attendance, function($a, $b) {
                return $b['total_items'] - $a['total_items'];
            });

            return $attendance;
        } catch (\Exception $e) {
            \Log::error('Get Worker Attendance Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get weight column name for a stage
     */
    private function getWeightColumn($stageNumber)
    {
        return match($stageNumber) {
            1 => 'weight',
            2 => 'output_weight',
            3 => 'total_weight',
            4 => 'total_weight',
            default => 'weight',
        };
    }

    /**
     * Check if column exists in table
     */
    private function columnExists($table, $column)
    {
        try {
            $columns = DB::connection()->getSchemaBuilder()->getColumnListing($table);
            return in_array($column, $columns);
        } catch (\Exception $e) {
            \Log::warning("Cannot get columns for table {$table}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get safe weight column
     */
    private function getSafeWeightColumn($table, $preferredColumn)
    {
        if ($this->columnExists($table, $preferredColumn)) {
            return $preferredColumn;
        }

        // Try fallback columns
        $fallbacks = ['weight', 'output_weight', 'total_weight'];
        foreach ($fallbacks as $col) {
            if ($this->columnExists($table, $col)) {
                return $col;
            }
        }

        return 'weight'; // Default
    }

    /**
     * Get worker production details
     */
    private function getWorkerProductionDetails($workerId, $date, $shiftType)
    {
        try {
            $timeRange = $this->getShiftTimeRange($date, $shiftType);
            $stagesWorked = [];
            $totalItems = 0;
            $totalOutput = 0;
            $totalWaste = 0;
            $firstActivity = null;
            $lastActivity = null;

            $tableNames = ['stage1_stands', 'stage2_processed', 'stage3_coils', 'stage4_boxes'];

            for ($i = 1; $i <= 4; $i++) {
                try {
                    $tableName = $tableNames[$i - 1];
                    $preferredColumn = $this->getWeightColumn($i);
                    $weightColumn = $this->getSafeWeightColumn($tableName, $preferredColumn);

                    $stageData = DB::table($tableName)
                        ->select(
                            DB::raw('COUNT(*) as items'),
                            DB::raw("SUM({$weightColumn}) as output"),
                            DB::raw('SUM(COALESCE(waste, 0)) as waste'),
                            DB::raw('MIN(created_at) as first_time'),
                            DB::raw('MAX(created_at) as last_time')
                        )
                        ->where('created_by', $workerId)
                        ->whereBetween('created_at', [$timeRange['start'], $timeRange['end']])
                        ->first();

                    if ($stageData && $stageData->items > 0) {
                        $stagesWorked[] = $i;
                        $totalItems += $stageData->items;
                        $totalOutput += $stageData->output ?? 0;
                        $totalWaste += $stageData->waste ?? 0;

                        if (!$firstActivity || $stageData->first_time < $firstActivity) {
                            $firstActivity = $stageData->first_time;
                        }
                        if (!$lastActivity || $stageData->last_time > $lastActivity) {
                            $lastActivity = $stageData->last_time;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning("Error processing stage {$i}: " . $e->getMessage());
                    continue;
                }
            }

            $hoursWorked = 0;
            if ($firstActivity && $lastActivity) {
                $start = Carbon::parse($firstActivity);
                $end = Carbon::parse($lastActivity);
                $hoursWorked = round($start->diffInMinutes($end) / 60, 1);
            }

            $efficiency = $totalOutput > 0 ? round((1 - ($totalWaste / $totalOutput)) * 100, 2) : 0;

            return [
                'stages_worked' => $stagesWorked,
                'total_items' => $totalItems,
                'total_output' => round($totalOutput, 2),
                'total_waste' => round($totalWaste, 2),
                'efficiency' => $efficiency,
                'first_activity' => $firstActivity,
                'last_activity' => $lastActivity,
                'hours_worked' => $hoursWorked,
            ];
        } catch (\Exception $e) {
            \Log::error('Get Worker Production Details Error: ' . $e->getMessage());
            return [
                'stages_worked' => [],
                'total_items' => 0,
                'total_output' => 0,
                'total_waste' => 0,
                'efficiency' => 0,
                'first_activity' => null,
                'last_activity' => null,
                'hours_worked' => 0,
            ];
        }
    }

    /**
     * Get shift comparison
     */
    private function getShiftComparison($date, $shiftType, $dateRange = null)
    {
        try {
            // Current shift
            $current = $this->getShiftSummary($date, $shiftType);

            // Previous shift
            $previousDate = Carbon::parse($date)->subDay()->format('Y-m-d');
            $previous = $this->getShiftSummary($previousDate, $shiftType);

            return [
                'current' => $current,
                'previous' => $previous,
                'items_change' => $this->calculateChange($previous['total_items'], $current['total_items']),
                'output_change' => $this->calculateChange($previous['total_output_kg'], $current['total_output_kg']),
                'waste_change' => $this->calculateChange($previous['total_waste_kg'], $current['total_waste_kg']),
                'efficiency_change' => $this->calculateChange($previous['efficiency'], $current['efficiency']),
            ];
        } catch (\Exception $e) {
            \Log::error('Get Shift Comparison Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate percentage change
     */
    private function calculateChange($old, $new)
    {
        if ($old == 0) {
            return ['value' => 0, 'direction' => 'neutral'];
        }

        $change = (($new - $old) / $old) * 100;
        $direction = $change > 0 ? 'up' : ($change < 0 ? 'down' : 'neutral');

        return [
            'value' => round(abs($change), 1),
            'direction' => $direction,
        ];
    }

    /**
     * Get stage efficiency details
     */
    private function getStageEfficiencyDetails($date, $shiftType, $dateRange = null)
    {
        try {
            if (!$dateRange) {
                $dateRange = $this->getShiftTimeRange($date, $shiftType);
            }
            $details = [];

            $tableNames = ['stage1_stands', 'stage2_processed', 'stage3_coils', 'stage4_boxes'];
            $stageNames = ['المرحلة 1 - التقسيم', 'المرحلة 2 - المعالجة', 'المرحلة 3 - اللف', 'المرحلة 4 - التعليب'];

            for ($i = 1; $i <= 4; $i++) {
                try {
                    $tableName = $tableNames[$i - 1];
                    $preferredColumn = $this->getWeightColumn($i);
                    $weightColumn = $this->getSafeWeightColumn($tableName, $preferredColumn);

                    // Get workers count
                    $workersCount = DB::table($tableName)
                        ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                        ->distinct('created_by')
                        ->count('created_by');

                    // Get production data
                    $data = DB::table($tableName)
                        ->select(
                            DB::raw('COUNT(*) as items'),
                            DB::raw("SUM({$weightColumn}) as output"),
                            DB::raw('SUM(COALESCE(waste, 0)) as waste'),
                            DB::raw('MIN(created_at) as first_item'),
                            DB::raw('MAX(created_at) as last_item')
                        )
                        ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                        ->first();

                    if (!$data) {
                        $data = (object) [
                            'items' => 0,
                            'output' => 0,
                            'waste' => 0,
                            'first_item' => null,
                            'last_item' => null
                        ];
                    }

                    $efficiency = ($data->output ?? 0) > 0
                        ? round((1 - (($data->waste ?? 0) / ($data->output ?? 1))) * 100, 2)
                        : 0;

                    $avgPerWorker = $workersCount > 0 ? round(($data->items ?? 0) / $workersCount, 1) : 0;

                    // Calculate production rate (items per hour)
                    $productionRate = 0;
                    if ($data->first_item && $data->last_item) {
                        $hours = Carbon::parse($data->first_item)->diffInMinutes(Carbon::parse($data->last_item)) / 60;
                        $productionRate = $hours > 0 ? round(($data->items ?? 0) / $hours, 1) : 0;
                    }

                    $details[] = [
                        'stage' => $i,
                        'name' => $stageNames[$i - 1],
                        'items' => $data->items ?? 0,
                        'output' => round($data->output ?? 0, 2),
                        'waste' => round($data->waste ?? 0, 2),
                        'waste_pct' => ($data->output ?? 0) > 0
                            ? round((($data->waste ?? 0) / ($data->output ?? 0)) * 100, 2)
                            : 0,
                        'efficiency' => $efficiency,
                        'workers_count' => $workersCount,
                        'avg_per_worker' => $avgPerWorker,
                        'production_rate' => $productionRate,
                        'first_item' => $data->first_item,
                        'last_item' => $data->last_item,
                    ];
                } catch (\Exception $e) {
                    Log::warning("Error processing stage {$i}: " . $e->getMessage());
                    continue;
                }
            }

            return $details;
        } catch (\Exception $e) {
            Log::error('Get Stage Efficiency Details Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get active teams
     */
    private function getActiveTeams($date, $shiftType, $dateRange = null)
    {
        try {
            if (!$dateRange) {
                $dateRange = $this->getShiftTimeRange($date, $shiftType);
            }

            // Get all active teams
            $teams = WorkerTeam::active()->get();

            $activeTeams = [];

            foreach ($teams as $team) {
                // Get worker IDs from the team
                $workerIds = $team->worker_ids && is_array($team->worker_ids) ? $team->worker_ids : [];

                if (empty($workerIds)) {
                    continue;
                }

                // Check if any team member was active in this shift
                $activeMembers = 0;
                $teamProduction = 0;

                foreach ($workerIds as $workerId) {
                    $workerData = $this->getWorkerProductionDetails($workerId, $date, $shiftType);
                    if ($workerData['total_items'] > 0) {
                        $activeMembers++;
                        $teamProduction += $workerData['total_items'];
                    }
                }

                if ($activeMembers > 0) {
                    $activeTeams[] = [
                        'team_code' => $team->team_code ?? 'N/A',
                        'team_name' => $team->name ?? 'فريق غير محدد',
                        'total_members' => count($workerIds),
                        'active_members' => $activeMembers,
                        'total_production' => $teamProduction,
                        'avg_per_member' => $activeMembers > 0 ? round($teamProduction / $activeMembers, 1) : 0,
                    ];
                }
            }

            return $activeTeams;
        } catch (\Exception $e) {
            Log::error('Get Active Teams Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get shift summary statistics
     */
    private function getShiftSummary($date, $shiftType, $dateRange = null)
    {
        try {
            if (!$dateRange) {
                $dateRange = $this->getShiftTimeRange($date, $shiftType);
            }

            $summary = [
                'total_items' => 0,
                'total_output_kg' => 0,
                'total_waste_kg' => 0,
                'waste_percentage' => 0,
                'efficiency' => 0,
                'workers_count' => 0,
                'avg_items_per_worker' => 0,
            ];

            $tableNames = ['stage1_stands', 'stage2_processed', 'stage3_coils', 'stage4_boxes'];

            for ($i = 1; $i <= 4; $i++) {
                try {
                    $tableName = $tableNames[$i - 1];
                    $preferredColumn = $this->getWeightColumn($i);
                    $weightColumn = $this->getSafeWeightColumn($tableName, $preferredColumn);

                    $stageData = DB::table($tableName)
                        ->select(
                            DB::raw('COUNT(*) as items'),
                            DB::raw("SUM({$weightColumn}) as output"),
                            DB::raw('SUM(COALESCE(waste, 0)) as waste'),
                            DB::raw('COUNT(DISTINCT created_by) as workers')
                        )
                        ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                        ->first();

                    if ($stageData) {
                        $summary['total_items'] += $stageData->items ?? 0;
                        $summary['total_output_kg'] += $stageData->output ?? 0;
                        $summary['total_waste_kg'] += $stageData->waste ?? 0;
                        $summary['workers_count'] = max($summary['workers_count'], $stageData->workers ?? 0);
                    }
                } catch (\Exception $e) {
                    \Log::warning("Error processing stage {$i}: " . $e->getMessage());
                    continue;
                }
            }

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
        } catch (\Exception $e) {
            Log::error('Get Shift Summary Error: ' . $e->getMessage());
            return [
                'total_items' => 0,
                'total_output_kg' => 0,
                'total_waste_kg' => 0,
                'waste_percentage' => 0,
                'efficiency' => 0,
                'workers_count' => 0,
                'avg_items_per_worker' => 0,
            ];
        }
    }

    /**
     * Get top performers in shift
     */
    private function getTopPerformers($date, $shiftType, $limit = 10, $dateRange = null)
    {
        try {
            if (!$dateRange) {
                $dateRange = $this->getShiftTimeRange($date, $shiftType);
            }
            $performers = [];

            $tableNames = ['stage1_stands', 'stage2_processed', 'stage3_coils', 'stage4_boxes'];

            for ($i = 1; $i <= 4; $i++) {
                try {
                    $tableName = $tableNames[$i - 1];
                    $preferredColumn = $this->getWeightColumn($i);
                    $weightColumn = $this->getSafeWeightColumn($tableName, $preferredColumn);

                    $stageData = DB::table($tableName . ' as s')
                        ->select(
                            's.created_by as worker_id',
                            'u.name as worker_name',
                            DB::raw('COUNT(*) as items'),
                            DB::raw("SUM(s.{$weightColumn}) as output"),
                            DB::raw('SUM(COALESCE(s.waste, 0)) as waste')
                        )
                        ->leftJoin('users as u', 's.created_by', '=', 'u.id')
                        ->whereBetween('s.created_at', [$dateRange['start'], $dateRange['end']])
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
                        $performers[$key]['items'] += $row->items ?? 0;
                        $performers[$key]['output'] += $row->output ?? 0;
                        $performers[$key]['waste'] += $row->waste ?? 0;
                    }
                } catch (\Exception $e) {
                    \Log::warning("Error processing stage {$i}: " . $e->getMessage());
                    continue;
                }
            }

            foreach ($performers as &$performer) {
                $performer['output'] = round($performer['output'], 2);
                $performer['waste'] = round($performer['waste'], 2);
                $performer['waste_pct'] = $performer['output'] > 0
                    ? round(($performer['waste'] / $performer['output']) * 100, 2)
                    : 0;
                $performer['efficiency'] = round(100 - $performer['waste_pct'], 2);
            }

            usort($performers, function($a, $b) {
                if ($a['efficiency'] == $b['efficiency']) {
                    return ($b['items'] ?? 0) - ($a['items'] ?? 0);
                }
                return ($b['efficiency'] ?? 0) <=> ($a['efficiency'] ?? 0);
            });

            return array_slice($performers, 0, $limit);
        } catch (\Exception $e) {
            \Log::error('Get Top Performers Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get production by stage
     */
    private function getProductionByStage($date, $shiftType, $dateRange = null)
    {
        if (!$dateRange) {
            $dateRange = $this->getShiftTimeRange($date, $shiftType);
        }
        return $this->getStageEfficiencyDetails($date, $shiftType, $dateRange);
    }

    /**
     * Get WIP count
     */
    private function getWIPCount()
    {
        try {
            $count = 0;

            $count += DB::table('stage1_stands as s1')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('stage2_processed as s2')
                        ->whereColumn('s2.parent_barcode', 's1.barcode');
                })
                ->count();

            $count += DB::table('stage2_processed as s2')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('stage3_coils as s3')
                        ->whereColumn('s3.parent_barcode', 's2.barcode');
                })
                ->count();

            $count += DB::table('stage3_coils as s3')
                ->where('status', '!=', 'packed')
                ->count();

            $count += DB::table('stage4_boxes')
                ->where('status', 'packing')
                ->count();

            return $count;
        } catch (\Exception $e) {
            \Log::error('Get WIP Count Error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get hourly production trend
     */
    private function getHourlyProductionTrend($date, $shiftType, $dateRange = null)
    {
        try {
            if (!$dateRange) {
                $dateRange = $this->getShiftTimeRange($date, $shiftType);
            }
            $hourlyData = [];

            $start = Carbon::parse($dateRange['start']);
            $end = Carbon::parse($dateRange['end']);
            $hours = [];

            while ($start <= $end) {
                $hour = $start->format('H:00');
                $hours[$hour] = 0;
                $start->addHour();
            }

            $tableNames = ['stage1_stands', 'stage2_processed', 'stage3_coils', 'stage4_boxes'];
            for ($i = 1; $i <= 4; $i++) {
                try {
                    $tableName = $tableNames[$i - 1];

                    $records = DB::table($tableName)
                        ->select(
                            DB::raw("HOUR(created_at) as hour"),
                            DB::raw('COUNT(*) as items')
                        )
                        ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                        ->groupBy('hour')
                        ->get();

                    foreach ($records as $record) {
                        $hourKey = str_pad($record->hour, 2, '0', STR_PAD_LEFT) . ':00';
                        if (isset($hours[$hourKey])) {
                            $hours[$hourKey] += $record->items;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning("Error processing hourly data for stage {$i}: " . $e->getMessage());
                    continue;
                }
            }

            $result = [];
            foreach ($hours as $hour => $count) {
                $result[] = [
                    'hour' => str_replace(':00', '', $hour),
                    'items' => $count
                ];
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error('Get Hourly Production Trend Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get shift time range
     */
    public function getShiftTimeRange($date, $shiftType)
    {
        $dateObj = Carbon::parse($date);

        if ($shiftType === 'morning') {
            return [
                'start' => $dateObj->copy()->setTime(6, 0, 0)->toDateTimeString(),
                'end' => $dateObj->copy()->setTime(18, 0, 0)->toDateTimeString(),
            ];
        } else {
            return [
                'start' => $dateObj->copy()->setTime(18, 0, 0)->toDateTimeString(),
                'end' => $dateObj->copy()->addDay()->setTime(6, 0, 0)->toDateTimeString(),
            ];
        }
    }

    /**
     * Get shift issues and alerts
     */
    private function getShiftIssues($date, $shiftType, $dateRange = null)
    {
        try {
            $timeRange = $this->getShiftTimeRange($date, $shiftType);
            $issues = [];

            // Check for low efficiency stages
            $stageEfficiency = $this->getStageEfficiencyDetails($date, $shiftType);
            foreach ($stageEfficiency as $stage) {
                if ($stage['efficiency'] < 85) {
                    $issues[] = [
                        'type' => 'low_efficiency',
                        'message' => "كفاءة منخفضة في {$stage['name']}: {$stage['efficiency']}%",
                        'severity' => 'warning',
                        'stage' => $stage['stage'],
                        'value' => $stage['efficiency'],
                    ];
                }
            }

            // Check for high waste percentage
            foreach ($stageEfficiency as $stage) {
                if ($stage['waste_pct'] > 10) {
                    $issues[] = [
                        'type' => 'high_waste',
                        'message' => "هدر مرتفع في {$stage['name']}: {$stage['waste_pct']}%",
                        'severity' => 'warning',
                        'stage' => $stage['stage'],
                        'value' => $stage['waste_pct'],
                    ];
                }
            }

            // Check for missing handovers
            $handovers = $this->getShiftHandovers($date, $shiftType);
            if (count($handovers) == 0) {
                $issues[] = [
                    'type' => 'no_handovers',
                    'message' => 'لم يتم تسجيل أي عمليات تسليم في هذه الوردية',
                    'severity' => 'info',
                ];
            }

            // Check for workers with zero production
            $attendance = $this->getWorkerAttendance($date, $shiftType);
            $zeroProduction = array_filter($attendance, function($worker) {
                return $worker['total_items'] == 0;
            });

            if (count($zeroProduction) > 0) {
                $issues[] = [
                    'type' => 'zero_production',
                    'message' => 'عدد العمال بدون إنتاج: ' . count($zeroProduction),
                    'severity' => 'warning',
                    'count' => count($zeroProduction),
                ];
            }

            return $issues;
        } catch (\Exception $e) {
            \Log::error('Get Shift Issues Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get stage name
     */
    private function getStageName($stageNumber)
    {
        $names = [
            1 => 'المرحلة 1 - التقسيم',
            2 => 'المرحلة 2 - المعالجة',
            3 => 'المرحلة 3 - اللف',
            4 => 'المرحلة 4 - التعليب',
        ];

        return $names[$stageNumber] ?? "المرحلة {$stageNumber}";
    }

    /**
     * Get live statistics (AJAX)
     */
    public function liveStats(Request $request)
    {
        try {
            $date = $request->input('date', now()->format('Y-m-d'));
            $shiftType = $request->input('shift_type', 'evening');

            $stats = [
                'summary' => $this->getShiftSummary($date, $shiftType),
                'wip_count' => $this->getWIPCount(),
                'top_performers' => $this->getTopPerformers($date, $shiftType, 5),
                'by_stage' => $this->getProductionByStage($date, $shiftType),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            \Log::error('Live Stats Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات',
            ], 500);
        }
    }

    /**
     * Export shift report
     */
    public function exportReport(Request $request)
    {
        try {
            $date = $request->input('date', now()->format('Y-m-d'));
            $shiftType = $request->input('shift', 'evening');

            // Gather all data
            $data = [
                'date' => $date,
                'shift_type' => $shiftType,
                'summary' => $this->getShiftSummary($date, $shiftType),
                'top_performers' => $this->getTopPerformers($date, $shiftType, 20),
                'by_stage' => $this->getProductionByStage($date, $shiftType),
                'attendance' => $this->getWorkerAttendance($date, $shiftType),
                'handovers' => $this->getShiftHandovers($date, $shiftType),
                'comparison' => $this->getShiftComparison($date, $shiftType),
            ];

            // Generate PDF or Excel
            // Implementation depends on your preferred export library

            return response()->json([
                'success' => true,
                'message' => 'تم تصدير التقرير بنجاح',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Export Report Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير التقرير',
            ], 500);
        }
    }
}
