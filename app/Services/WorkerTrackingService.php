<?php

namespace App\Services;

use App\Models\WorkerStageHistory;
use App\Models\User;
use App\Models\WorkerTeam;
use Illuminate\Support\Collection;

class WorkerTrackingService
{
    /**
     * تعيين عامل فردي لمرحلة
     */
    public function assignWorkerToStage(
        string $stageType,
        int $stageRecordId,
        int $workerId,
        ?string $barcode = null,
        ?string $statusBefore = null,
        ?int $assignedBy = null,
        ?int $shiftAssignmentId = null
    ): WorkerStageHistory {
        return WorkerStageHistory::assignWorkerToStage(
            stageType: $stageType,
            stageRecordId: $stageRecordId,
            workerId: $workerId,
            barcode: $barcode,
            statusBefore: $statusBefore,
            assignedBy: $assignedBy,
            shiftAssignmentId: $shiftAssignmentId
        );
    }

    /**
     * تعيين فريق لمرحلة
     */
    public function assignTeamToStage(
        string $stageType,
        int $stageRecordId,
        int $workerTeamId,
        ?string $barcode = null,
        ?string $statusBefore = null,
        ?int $assignedBy = null,
        ?int $shiftAssignmentId = null
    ): WorkerStageHistory {
        return WorkerStageHistory::assignWorkerToStage(
            stageType: $stageType,
            stageRecordId: $stageRecordId,
            workerTeamId: $workerTeamId,
            barcode: $barcode,
            statusBefore: $statusBefore,
            assignedBy: $assignedBy,
            shiftAssignmentId: $shiftAssignmentId
        );
    }

    /**
     * إنهاء عمل العامل على المرحلة
     */
    public function endWorkerSession(
        int $historyId,
        ?string $statusAfter = null,
        ?string $notes = null
    ): void {
        $history = WorkerStageHistory::find($historyId);

        if ($history && $history->is_active) {
            $history->endWork($statusAfter, $notes);
        }
    }

    /**
     * إنهاء جميع الجلسات النشطة لمرحلة معينة
     */
    public function endAllActiveSessionsForStage(
        string $stageType,
        int $stageRecordId,
        ?string $statusAfter = null
    ): int {
        $activeSessions = WorkerStageHistory::forStage($stageType, $stageRecordId)
            ->active()
            ->get();

        foreach ($activeSessions as $session) {
            $session->endWork($statusAfter);
        }

        return $activeSessions->count();
    }

    /**
     * الحصول على العامل الحالي لمرحلة
     */
    public function getCurrentWorkerForStage(string $stageType, int $stageRecordId): ?WorkerStageHistory
    {
        return WorkerStageHistory::getCurrentWorkerForStage($stageType, $stageRecordId);
    }

    /**
     * الحصول على تاريخ كامل لمرحلة
     */
    public function getStageHistory(string $stageType, int $stageRecordId): Collection
    {
        return WorkerStageHistory::getStageHistory($stageType, $stageRecordId);
    }

    /**
     * الحصول على تاريخ عامل معين
     */
    public function getWorkerHistory(int $workerId, ?string $stageType = null): Collection
    {
        $query = WorkerStageHistory::forWorker($workerId)
            ->with(['workerTeam', 'assignedBy']);

        if ($stageType) {
            $query->where('stage_type', $stageType);
        }

        return $query->orderBy('started_at', 'desc')->get();
    }

    /**
     * الحصول على تاريخ فريق معين
     */
    public function getTeamHistory(int $teamId, ?string $stageType = null): Collection
    {
        $query = WorkerStageHistory::forTeam($teamId)
            ->with(['worker', 'assignedBy']);

        if ($stageType) {
            $query->where('stage_type', $stageType);
        }

        return $query->orderBy('started_at', 'desc')->get();
    }

    /**
     * الحصول على العمال النشطين حالياً في مرحلة معينة
     */
    public function getActiveWorkersForStage(string $stageType): Collection
    {
        return WorkerStageHistory::where('stage_type', $stageType)
            ->active()
            ->with(['worker', 'workerTeam'])
            ->get()
            ->groupBy('stage_record_id');
    }

    /**
     * الحصول على إحصائيات عامل
     */
    public function getWorkerStatistics(int $workerId): array
    {
        $history = WorkerStageHistory::forWorker($workerId)->get();

        return [
            'total_sessions' => $history->count(),
            'active_sessions' => $history->where('is_active', true)->count(),
            'completed_sessions' => $history->where('is_active', false)->count(),
            'total_minutes' => $history->sum('duration_minutes'),
            'total_hours' => round($history->sum('duration_minutes') / 60, 2),
            'stages_worked' => $history->pluck('stage_type')->unique()->values()->toArray(),
            'average_session_minutes' => $history->where('is_active', false)->avg('duration_minutes'),
        ];
    }

    /**
     * الحصول على إحصائيات مرحلة
     */
    public function getStageStatistics(string $stageType, int $stageRecordId): array
    {
        $history = WorkerStageHistory::forStage($stageType, $stageRecordId)->get();

        return [
            'total_sessions' => $history->count(),
            'total_workers' => $history->where('worker_type', WorkerStageHistory::WORKER_TYPE_INDIVIDUAL)
                ->pluck('worker_id')->unique()->count(),
            'total_teams' => $history->where('worker_type', WorkerStageHistory::WORKER_TYPE_TEAM)
                ->pluck('worker_team_id')->unique()->count(),
            'total_minutes' => $history->sum('duration_minutes'),
            'total_hours' => round($history->sum('duration_minutes') / 60, 2),
            'current_worker' => $this->getCurrentWorkerForStage($stageType, $stageRecordId),
            'average_session_minutes' => $history->where('is_active', false)->avg('duration_minutes'),
        ];
    }

    /**
     * البحث في التاريخ بالباركود
     */
    public function getHistoryByBarcode(string $barcode): Collection
    {
        return WorkerStageHistory::forBarcode($barcode)
            ->with(['worker', 'workerTeam', 'assignedBy'])
            ->orderBy('started_at', 'desc')
            ->get();
    }

    /**
     * نقل العمل من عامل لآخر
     */
    public function transferWork(
        string $stageType,
        int $stageRecordId,
        int $newWorkerId,
        ?string $notes = null,
        ?int $assignedBy = null
    ): WorkerStageHistory {
        // End current session
        $currentSession = $this->getCurrentWorkerForStage($stageType, $stageRecordId);

        if ($currentSession) {
            $currentSession->endWork(
                statusAfter: null,
                notes: $notes ? "تم النقل: " . $notes : "تم النقل لعامل آخر"
            );
        }

        // Get barcode and status from stage
        $stageRecord = $currentSession?->getStageRecord();
        $barcode = $stageRecord?->barcode ?? null;
        $statusBefore = $stageRecord?->status ?? null;

        // Start new session
        return $this->assignWorkerToStage(
            stageType: $stageType,
            stageRecordId: $stageRecordId,
            workerId: $newWorkerId,
            barcode: $barcode,
            statusBefore: $statusBefore,
            assignedBy: $assignedBy
        );
    }

    /**
     * الحصول على العمال المتاحين (غير مشغولين)
     */
    public function getAvailableWorkers(?string $stageType = null): Collection
    {
        $busyWorkerIds = WorkerStageHistory::active()
            ->where('worker_type', WorkerStageHistory::WORKER_TYPE_INDIVIDUAL)
            ->pluck('worker_id')
            ->unique()
            ->toArray();

        return User::whereNotIn('id', $busyWorkerIds)
            ->where('is_active', true)
            ->get();
    }
}
