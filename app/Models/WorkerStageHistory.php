<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class WorkerStageHistory extends Model
{
    use SoftDeletes;

    protected $table = 'worker_stage_history';

    protected $fillable = [
        'stage_type',
        'stage_record_id',
        'barcode',
        'worker_id',
        'worker_team_id',
        'worker_type',
        'started_at',
        'ended_at',
        'duration_minutes',
        'status_before',
        'status_after',
        'is_active',
        'notes',
        'shift_assignment_id',
        'assigned_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Stage Types Constants
    const STAGE_1_STANDS = 'stage1_stands';
    const STAGE_2_PROCESSED = 'stage2_processed';
    const STAGE_3_COILS = 'stage3_coils';
    const STAGE_4_BOXES = 'stage4_boxes';

    // Worker Types
    const WORKER_TYPE_INDIVIDUAL = 'individual';
    const WORKER_TYPE_TEAM = 'team';

    /**
     * Relations
     */
    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function workerTeam(): BelongsTo
    {
        return $this->belongsTo(WorkerTeam::class, 'worker_team_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function shiftAssignment(): BelongsTo
    {
        return $this->belongsTo(ShiftAssignment::class, 'shift_assignment_id');
    }

    /**
     * Get the stage record (polymorphic-like behavior)
     */
    public function getStageRecord()
    {
        $modelClass = $this->getModelClassForStageType();

        if (!$modelClass) {
            return null;
        }

        return $modelClass::find($this->stage_record_id);
    }

    /**
     * Get model class for stage type
     */
    protected function getModelClassForStageType(): ?string
    {
        return match($this->stage_type) {
            self::STAGE_1_STANDS => Stage1Stand::class,
            self::STAGE_2_PROCESSED => Stage2Processed::class,
            self::STAGE_3_COILS => Stage3Coil::class,
            self::STAGE_4_BOXES => Stage4Box::class,
            default => null,
        };
    }

    /**
     * Get worker name (individual or team)
     */
    public function getWorkerNameAttribute(): string
    {
        if ($this->worker_type === self::WORKER_TYPE_TEAM && $this->workerTeam) {
            return $this->workerTeam->name;
        }

        if ($this->worker) {
            return $this->worker->name;
        }

        return 'غير محدد';
    }

    /**
     * Get stage name in Arabic
     */
    public function getStageNameAttribute(): string
    {
        return match($this->stage_type) {
            self::STAGE_1_STANDS => 'المرحلة الأولى - الستاند',
            self::STAGE_2_PROCESSED => 'المرحلة الثانية - المعالجة',
            self::STAGE_3_COILS => 'المرحلة الثالثة - الكويل',
            self::STAGE_4_BOXES => 'المرحلة الرابعة - الصندوق',
            default => 'غير معروف',
        };
    }

    /**
     * Calculate duration in minutes
     */
    public function calculateDuration(): int
    {
        if (!$this->ended_at) {
            // Still active - calculate from start to now
            return (int) $this->started_at->diffInMinutes(now());
        }

        return (int) $this->started_at->diffInMinutes($this->ended_at);
    }

    /**
     * End this work session
     */
    public function endWork(?string $statusAfter = null, ?string $notes = null): void
    {
        $this->ended_at = now();
        $this->duration_minutes = $this->calculateDuration();
        $this->is_active = false;

        if ($statusAfter) {
            $this->status_after = $statusAfter;
        }

        if ($notes) {
            $this->notes = $notes;
        }

        $this->save();
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->duration_minutes ?? $this->calculateDuration();

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return sprintf('%d ساعة و %d دقيقة', $hours, $mins);
        }

        return sprintf('%d دقيقة', $mins);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForWorker($query, int $workerId)
    {
        return $query->where('worker_id', $workerId)
                     ->where('worker_type', self::WORKER_TYPE_INDIVIDUAL);
    }

    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('worker_team_id', $teamId)
                     ->where('worker_type', self::WORKER_TYPE_TEAM);
    }

    public function scopeForStage($query, string $stageType, int $stageRecordId)
    {
        return $query->where('stage_type', $stageType)
                     ->where('stage_record_id', $stageRecordId);
    }

    public function scopeForBarcode($query, string $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    /**
     * Get current active worker for a stage
     */
    public static function getCurrentWorkerForStage(string $stageType, int $stageRecordId)
    {
        return self::forStage($stageType, $stageRecordId)
                   ->active()
                   ->with(['worker', 'workerTeam'])
                   ->latest('started_at')
                   ->first();
    }

    /**
     * Get history for a stage
     */
    public static function getStageHistory(string $stageType, int $stageRecordId)
    {
        return self::forStage($stageType, $stageRecordId)
                   ->with(['worker', 'workerTeam', 'assignedBy'])
                   ->orderBy('started_at', 'desc')
                   ->get();
    }

    /**
     * Assign worker to stage
     */
    public static function assignWorkerToStage(
        string $stageType,
        int $stageRecordId,
        ?int $workerId = null,
        ?int $workerTeamId = null,
        ?string $barcode = null,
        ?string $statusBefore = null,
        ?int $assignedBy = null,
        ?int $shiftAssignmentId = null
    ): self {
        // End any active sessions for this stage
        self::forStage($stageType, $stageRecordId)
            ->active()
            ->update([
                'ended_at' => now(),
                'is_active' => false,
            ]);

        // Calculate duration for ended sessions
        $endedSessions = self::forStage($stageType, $stageRecordId)
            ->whereNull('duration_minutes')
            ->whereNotNull('ended_at')
            ->get();

        foreach ($endedSessions as $session) {
            $session->duration_minutes = $session->calculateDuration();
            $session->save();
        }

        // Create new session
        $workerType = $workerTeamId ? self::WORKER_TYPE_TEAM : self::WORKER_TYPE_INDIVIDUAL;

        return self::create([
            'stage_type' => $stageType,
            'stage_record_id' => $stageRecordId,
            'barcode' => $barcode,
            'worker_id' => $workerId,
            'worker_team_id' => $workerTeamId,
            'worker_type' => $workerType,
            'started_at' => now(),
            'status_before' => $statusBefore,
            'is_active' => true,
            'assigned_by' => $assignedBy,
            'shift_assignment_id' => $shiftAssignmentId,
        ]);
    }
}
