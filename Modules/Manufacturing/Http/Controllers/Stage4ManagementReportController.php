<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Stage4Box;

class Stage4ManagementReportController extends Controller
{
    /**
     * Display stage4 management report
     * تقرير إدارة المرحلة الرابعة (التعبئة والتغليف)
     */
    public function index()
    {
        // Permission check
        if (!Auth::user()->hasPermission('STAGE4_PACKAGING_READ')) {
            abort(403);
        }

        // Get all records
        $query = DB::table('stage4_boxes')
            ->leftJoin('users', 'stage4_boxes.created_by', '=', 'users.id')
            ->leftJoin('warehouses', 'stage4_boxes.warehouse_id', '=', 'warehouses.id')
            ->select(
                'stage4_boxes.*',
                'users.name as created_by_name',
                'warehouses.warehouse_name as warehouse_name'
            );

        // ========== FILTERS ==========
        $filters = [];

        // Search by barcode
        if (request('search')) {
            $search = request('search');
            $query->where('stage4_boxes.barcode', 'like', "%$search%")
                  ->orWhere('stage4_boxes.parent_barcode', 'like', "%$search%")
                  ->orWhere('stage4_boxes.tracking_number', 'like', "%$search%");
            $filters['search'] = $search;
        }

        // Filter by status
        if (request('status')) {
            $status = request('status');
            $query->where('stage4_boxes.status', $status);
            $filters['status'] = $status;
        }

        // Filter by worker
        if (request('worker_id')) {
            $workerId = request('worker_id');
            $query->where('stage4_boxes.created_by', $workerId);
            $filters['worker_id'] = $workerId;
        }

        // Filter by packaging type
        if (request('packaging_type')) {
            $packagingType = request('packaging_type');
            $query->where('stage4_boxes.packaging_type', $packagingType);
            $filters['packaging_type'] = $packagingType;
        }

        // Filter by warehouse
        if (request('warehouse_id')) {
            $warehouseId = request('warehouse_id');
            $query->where('stage4_boxes.warehouse_id', $warehouseId);
            $filters['warehouse_id'] = $warehouseId;
        }

        // Filter by date range
        if (request('from_date')) {
            $fromDate = request('from_date');
            $query->whereDate('stage4_boxes.created_at', '>=', $fromDate);
            $filters['from_date'] = $fromDate;
        }

        if (request('to_date')) {
            $toDate = request('to_date');
            $query->whereDate('stage4_boxes.created_at', '<=', $toDate);
            $filters['to_date'] = $toDate;
        }

        // Sort results
        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'desc');
        $query->orderBy("stage4_boxes.$sortBy", $sortOrder);

        $stage4Records = $query->get();

        // Get workers for filter dropdown
        $stage4Workers = DB::table('users')
            ->whereIn('id', DB::table('stage4_boxes')->pluck('created_by'))
            ->select('id', 'name')
            ->get();

        // Get warehouses for filter dropdown
        $warehouses = DB::table('warehouses')
            ->select('id', 'warehouse_name')
            ->get();

        // ========== BASIC KPIs ==========
        $stage4Total = $stage4Records->count();
        $stage4Today = $stage4Records->filter(function ($record) {
            if (!$record->created_at) return false;
            if (is_string($record->created_at)) {
                $createdDate = substr($record->created_at, 0, 10);
            } else {
                $createdDate = Carbon::parse($record->created_at)->format('Y-m-d');
            }
            return $createdDate === today()->toDateString();
        })->count();

        // ========== STATUS DISTRIBUTION ==========
        $stage4StatusPacking = $stage4Records->where('status', 'packing')->count();
        $stage4StatusPacked = $stage4Records->where('status', 'packed')->count();
        $stage4StatusShipped = $stage4Records->where('status', 'shipped')->count();
        $stage4StatusDelivered = $stage4Records->where('status', 'delivered')->count();
        $stage4StatusInWarehouse = $stage4Records->where('status', 'in_warehouse')->count();

        // ========== COMPLETION RATE ==========
        $stage4CompletedCount = $stage4StatusPacked + $stage4StatusShipped + $stage4StatusDelivered;
        $stage4CompletionRate = $stage4Total > 0 ? round(($stage4CompletedCount / $stage4Total) * 100) : 0;

        // ========== WEIGHT CALCULATIONS ==========
        $stage4TotalWeight = round($stage4Records->sum('total_weight'), 2);
        $stage4TotalWaste = round($stage4Records->sum('waste'), 2);
        $stage4TotalCoils = $stage4Records->sum('coils_count');

        // ========== WASTE PERCENTAGES ==========
        $stage4WastePercentages = $stage4Records->map(function ($record) {
            if ($record->total_weight > 0) {
                return (($record->waste) / $record->total_weight) * 100;
            }
            return 0;
        })->filter(function ($val) {
            return $val >= 0;
        })->values();

        $stage4AvgWastePercentage = $stage4WastePercentages->count() > 0 ? round($stage4WastePercentages->avg(), 2) : 0;
        $stage4MaxWastePercentage = $stage4WastePercentages->count() > 0 ? round($stage4WastePercentages->max(), 2) : 0;
        $stage4MinWastePercentage = $stage4WastePercentages->count() > 0 ? round($stage4WastePercentages->min(), 2) : 0;

        // Find best and worst performing records
        $stage4MaxWasteRecord = $stage4Records->sortByDesc(function ($record) {
            if ($record->total_weight > 0) {
                return (($record->waste) / $record->total_weight) * 100;
            }
            return 0;
        })->first();
        $stage4MaxWasteBarcode = $stage4MaxWasteRecord ? $stage4MaxWasteRecord->barcode : '-';

        $stage4MinWasteRecord = $stage4Records->sortBy(function ($record) {
            if ($record->total_weight > 0) {
                return (($record->waste) / $record->total_weight) * 100;
            }
            return 0;
        })->first();
        $stage4MinWasteBarcode = $stage4MinWasteRecord ? $stage4MinWasteRecord->barcode : '-';

        // ========== WORKER PERFORMANCE ==========
        $stage4WorkerPerformance = $stage4Records->groupBy('created_by_name')->map(function ($items, $workerName) {
            $count = $items->count();
            $totalWeight = round($items->sum('total_weight'), 2);
            $totalWaste = round($items->sum('waste'), 2);
            $totalCoils = $items->sum('coils_count');
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
                'total_coils' => $totalCoils,
                'avg_waste' => $wastePercs->count() > 0 ? round($wastePercs->avg(), 2) : 0,
            ];
        });

        $stage4BestWorker = $stage4WorkerPerformance->sortByDesc('count')->first();
        $stage4BestWorkerName = $stage4BestWorker ? $stage4BestWorker['name'] : 'Not available';
        $stage4BestWorkerCount = $stage4BestWorker ? $stage4BestWorker['count'] : 0;
        $stage4BestWorkerAvgWaste = $stage4BestWorker ? $stage4BestWorker['avg_waste'] : 0;

        // ========== ACTIVE WORKERS ==========
        $stage4ActiveWorkers = $stage4Records->filter(function ($record) {
            if (!$record->created_at) return false;
            $createdDate = is_string($record->created_at) ? strtotime($record->created_at) : $record->created_at->timestamp;
            $weekAgoTime = now()->subDays(7)->timestamp;
            return $createdDate >= $weekAgoTime;
        })->pluck('created_by')->unique()->count();

        // ========== DAILY OPERATIONS ==========
        $stage4DateGroups = $stage4Records->groupBy(function ($item) {
            if (!$item->created_at) return null;
            if (is_string($item->created_at)) {
                $date = substr($item->created_at, 0, 10);
            } else {
                $date = Carbon::parse($item->created_at)->format('Y-m-d');
            }
            return $date;
        })->map(function ($items) {
            return [
                'count' => $items->count(),
                'total_weight' => round($items->sum('total_weight'), 2),
                'total_coils' => $items->sum('coils_count'),
            ];
        })->sortKeys()->reverse();

        // ========== PACKAGING TYPE DISTRIBUTION ==========
        $stage4PackagingTypes = $stage4Records->groupBy('packaging_type')->map(function ($items) {
            return [
                'count' => $items->count(),
                'weight' => round($items->sum('total_weight'), 2),
                'coils' => $items->sum('coils_count'),
            ];
        });

        // ========== WAREHOUSE DISTRIBUTION ==========
        $stage4WarehouseDistribution = $stage4Records->groupBy('warehouse_name')->map(function ($items) {
            return [
                'count' => $items->count(),
                'weight' => round($items->sum('total_weight'), 2),
                'coils' => $items->sum('coils_count'),
            ];
        });

        // ========== SHIPPED & DELIVERED ==========
        $stage4ShippedTotal = $stage4StatusShipped + $stage4StatusDelivered;
        $stage4ShippedPercentage = $stage4Total > 0 ? round(($stage4ShippedTotal / $stage4Total) * 100) : 0;

        // ========== RECENT BOXES ==========
        $recentBoxes = $stage4Records->sortByDesc('created_at')->take(10);

        // ========== AVERAGE BOX METRICS ==========
        $stage4AvgWeightPerBox = $stage4Total > 0 ? round($stage4TotalWeight / $stage4Total, 2) : 0;
        $stage4AvgCoilsPerBox = $stage4Total > 0 ? round($stage4TotalCoils / $stage4Total, 1) : 0;

        return view('manufacturing::reports.stage4_management_report', compact(
            'stage4Records',
            'filters',
            'stage4Total',
            'stage4Today',
            'stage4StatusPacking',
            'stage4StatusPacked',
            'stage4StatusShipped',
            'stage4StatusDelivered',
            'stage4StatusInWarehouse',
            'stage4CompletedCount',
            'stage4CompletionRate',
            'stage4TotalWeight',
            'stage4TotalWaste',
            'stage4TotalCoils',
            'stage4AvgWastePercentage',
            'stage4MaxWastePercentage',
            'stage4MinWastePercentage',
            'stage4MaxWasteBarcode',
            'stage4MinWasteBarcode',
            'stage4WorkerPerformance',
            'stage4BestWorkerName',
            'stage4BestWorkerCount',
            'stage4BestWorkerAvgWaste',
            'stage4ActiveWorkers',
            'stage4DateGroups',
            'stage4PackagingTypes',
            'stage4WarehouseDistribution',
            'stage4ShippedPercentage',
            'recentBoxes',
            'stage4AvgWeightPerBox',
            'stage4AvgCoilsPerBox',
            'stage4Workers',
            'warehouses'
        ));
    }
}
