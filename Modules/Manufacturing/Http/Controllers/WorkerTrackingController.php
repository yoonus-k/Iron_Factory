<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\WorkerTrackingService;
use App\Models\WorkerStageHistory;
use App\Models\User;
use Illuminate\Http\Request;

class WorkerTrackingController extends Controller
{
    protected WorkerTrackingService $trackingService;

    public function __construct(WorkerTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * عرض تاريخ مرحلة معينة
     */
    public function stageHistory(Request $request, string $stageType, int $stageRecordId)
    {
        $history = $this->trackingService->getStageHistory($stageType, $stageRecordId);
        $statistics = $this->trackingService->getStageStatistics($stageType, $stageRecordId);
        $currentWorker = $this->trackingService->getCurrentWorkerForStage($stageType, $stageRecordId);

        return view('manufacturing::worker-tracking.stage-history', compact(
            'history',
            'statistics',
            'currentWorker',
            'stageType',
            'stageRecordId'
        ));
    }

    /**
     * عرض تاريخ عامل معين
     */
    public function workerHistory(Request $request, int $workerId)
    {
        $worker = User::findOrFail($workerId);
        $stageType = $request->get('stage_type');

        $history = $this->trackingService->getWorkerHistory($workerId, $stageType);
        $statistics = $this->trackingService->getWorkerStatistics($workerId);

        return view('manufacturing::worker-tracking.worker-history', compact(
            'worker',
            'history',
            'statistics',
            'stageType'
        ));
    }

    /**
     * البحث بالباركود
     */
    public function searchByBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);

        $history = $this->trackingService->getHistoryByBarcode($request->barcode);

        return view('manufacturing::worker-tracking.barcode-search', compact('history'));
    }

    /**
     * نقل العمل من عامل لآخر
     */
    public function transferWork(Request $request)
    {
        $request->validate([
            'stage_type' => 'required|string',
            'stage_record_id' => 'required|integer',
            'new_worker_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $this->trackingService->transferWork(
            stageType: $request->stage_type,
            stageRecordId: $request->stage_record_id,
            newWorkerId: $request->new_worker_id,
            notes: $request->notes,
            assignedBy: auth()->id()
        );

        return redirect()->back()->with('success', __('manufacturing::worker-tracking.work_transferred_successfully'));
    }

    /**
     * إنهاء جلسة عمل
     */
    public function endSession(Request $request, int $historyId)
    {
        $request->validate([
            'status_after' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $this->trackingService->endWorkerSession(
            historyId: $historyId,
            statusAfter: $request->status_after,
            notes: $request->notes
        );

        return redirect()->back()->with('success', __('manufacturing::worker-tracking.session_ended_successfully'));
    }

    /**
     * عرض العمال المتاحين
     */
    public function availableWorkers(Request $request)
    {
        $stageType = $request->get('stage_type');
        $workers = $this->trackingService->getAvailableWorkers($stageType);

        return response()->json([
            'workers' => $workers->map(fn($w) => [
                'id' => $w->id,
                'name' => $w->name,
                'email' => $w->email,
            ])
        ]);
    }

    /**
     * لوحة التحكم - نظرة عامة
     */
    public function dashboard(Request $request)
    {
        // If AJAX request, return data
        if ($request->get('ajax')) {
            $activeWorkers = WorkerStageHistory::with(['worker', 'workerTeam'])
                ->where('is_active', true)
                ->get()
                ->map(function($history) {
                    return [
                        'worker_name' => $history->worker ? $history->worker->name : ($history->workerTeam ? $history->workerTeam->name : 'N/A'),
                        'stage_type' => $history->stage_type,
                        'stage_record_id' => $history->stage_record_id,
                        'barcode' => $history->barcode,
                        'started_at' => $history->started_at,
                    ];
                });

            $recentCompleted = WorkerStageHistory::with(['worker', 'workerTeam'])
                ->where('is_active', false)
                ->whereNotNull('ended_at')
                ->orderBy('ended_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($history) {
                    return [
                        'worker_name' => $history->worker ? $history->worker->name : ($history->workerTeam ? $history->workerTeam->name : 'N/A'),
                        'stage_type' => $history->stage_type,
                        'started_at' => $history->started_at,
                        'ended_at' => $history->ended_at,
                        'duration_minutes' => $history->duration_minutes,
                    ];
                });

            $today = now()->startOfDay();
            $todayHistory = WorkerStageHistory::whereDate('started_at', '>=', $today)->get();

            $stats = [
                'activeWorkers' => WorkerStageHistory::where('is_active', true)->count(),
                'stagesInProgress' => WorkerStageHistory::where('is_active', true)
                    ->distinct('stage_type', 'stage_record_id')
                    ->count(),
                'avgDurationToday' => $todayHistory->where('is_active', false)->avg('duration_minutes') ?? 0,
                'totalWorkMinutesToday' => $todayHistory->sum('duration_minutes') ?? 0,
            ];

            return response()->json([
                'activeWorkers' => $activeWorkers,
                'recentCompleted' => $recentCompleted,
                'stats' => $stats,
            ]);
        }

        // Return view for normal page load
        return view('manufacturing::worker-tracking.dashboard');
    }
}
