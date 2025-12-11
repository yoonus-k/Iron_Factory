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
    public function dashboard()
    {
        // Get active workers per stage
        $stage1Active = $this->trackingService->getActiveWorkersForStage(WorkerStageHistory::STAGE_1_STANDS);
        $stage2Active = $this->trackingService->getActiveWorkersForStage(WorkerStageHistory::STAGE_2_PROCESSED);
        $stage3Active = $this->trackingService->getActiveWorkersForStage(WorkerStageHistory::STAGE_3_COILS);
        $stage4Active = $this->trackingService->getActiveWorkersForStage(WorkerStageHistory::STAGE_4_BOXES);

        return view('manufacturing::worker-tracking.dashboard', compact(
            'stage1Active',
            'stage2Active',
            'stage3Active',
            'stage4Active'
        ));
    }
}
