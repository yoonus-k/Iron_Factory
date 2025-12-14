<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\ShiftAssignment;
use App\Models\ShiftOperationLog;
use App\Models\ShiftTransferHistory;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkerStageHistory;
use App\Traits\StoresNotifications;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShiftsWorkersController extends Controller
{
    use StoresNotifications;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ShiftAssignment::with(['user', 'supervisor'])
            ->orderBy('shift_date', 'desc')
            ->orderBy('start_time', 'desc');

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('shift_date', $request->date);
        }

        // Filter by shift type
        if ($request->filled('shift_type')) {
            $query->where('shift_type', $request->shift_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by stage
        if ($request->filled('stage_number')) {
            $query->where('stage_number', $request->stage_number);
        }

        $shifts = $query->paginate(20);

        // Get statistics
        $stats = [
            'total' => ShiftAssignment::count(),
            'active' => ShiftAssignment::where('status', ShiftAssignment::STATUS_ACTIVE)->count(),
            'scheduled' => ShiftAssignment::where('status', ShiftAssignment::STATUS_SCHEDULED)->count(),
            'completed_today' => ShiftAssignment::where('status', ShiftAssignment::STATUS_COMPLETED)
                ->whereDate('shift_date', today())
                ->count(),
        ];

        // Get active worker teams with their manager and workers
        $teams = \App\Models\WorkerTeam::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function($team) {
                // Get manager name if exists
                $manager = $team->manager;
                $managerName = $manager ? $manager->name : 'لا يوجد مسؤول';

                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'code' => $team->team_code,
                    'manager_id' => $team->manager_id,
                    'manager_name' => $managerName,
                    'worker_ids' => $team->worker_ids ?? [],
                    'workers_count' => count($team->worker_ids ?? [])
                ];
            });

        return view('manufacturing::shifts-workers.index', compact('shifts', 'stats', 'teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all workers from workers table
        $workersFromTable = \App\Models\Worker::orderBy('name')->get();

        // Get all users
        $usersWorkers = User::orderBy('name')->get();

        // Merge workers and users, removing duplicates by id
        $workers = collect();

        // Add workers from workers table
        foreach ($workersFromTable as $worker) {
            $workers->push($worker);
        }

        // Add users that are not already in workers
        foreach ($usersWorkers as $user) {
            if (!$workers->contains('id', $user->id)) {
                $workers->push($user);
            }
        }

        // Sort by name
        $workers = $workers->sortBy('name')->values();

        // Get supervisors (users with supervisor role or permission)
        $supervisors = User::orderBy('name')
            ->get();

        // Get active worker teams with their manager and workers
        $teams = \App\Models\WorkerTeam::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function($team) {
                // Get manager name if exists
                $manager = $team->manager;
                $managerName = $manager ? $manager->name : 'لا يوجد مسؤول';

                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'code' => $team->team_code,
                    'manager_id' => $team->manager_id,
                    'manager_name' => $managerName,
                    'worker_ids' => $team->worker_ids ?? [],
                    'workers_count' => count($team->worker_ids ?? [])
                ];
            });

        return view('manufacturing::shifts-workers.create', compact('workers', 'supervisors', 'teams'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shift_code' => 'required|string|max:50|unique:shift_assignments,shift_code',
            'shift_date' => 'required|date',
            'shift_type' => 'required|in:morning,evening',
            'supervisor_id' => 'required|exists:users,id',
            'team_id' => 'nullable|exists:worker_teams,id',
            'stage_number' => 'nullable|integer|between:0,4',
            'stage_record_barcode' => 'nullable|string|max:100',
            'stage_record_id' => 'nullable|integer',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'workers' => 'nullable|array',
            'workers.*' => 'exists:workers,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            $workerIds = $request->input('workers', []);
            $teamId = $request->input('team_id');

            // Ensure worker_ids is an array and clean it
            if (!is_array($workerIds)) {
                $workerIds = [];
            }
            $workerIds = array_filter($workerIds);
            $workerIds = array_values($workerIds);

            // Debug logging
            \Log::info('Creating shift with data:', [
                'shift_code' => $request->shift_code,
                'supervisor_id' => $request->supervisor_id,
                'stage_number' => $request->stage_number,
                'stage_record_barcode' => $request->stage_record_barcode,
                'stage_record_id' => $request->stage_record_id,
                'workers_count' => count($workerIds),
                'worker_ids' => $workerIds
            ]);

            $shift = ShiftAssignment::create([
                'shift_code' => $request->shift_code,
                'shift_type' => $request->shift_type,
                'user_id' => $request->supervisor_id,
                'supervisor_id' => $request->supervisor_id,
                'team_id' => $teamId,
                'stage_number' => $request->stage_number,
                'stage_record_barcode' => $request->stage_record_barcode,
                'stage_record_id' => $request->stage_record_id,
                'shift_date' => $request->shift_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => ShiftAssignment::STATUS_SCHEDULED,
                'notes' => $request->notes,
                'is_active' => $request->input('is_active', true),
                'total_workers' => count($workerIds),
                'worker_ids' => $workerIds,
            ]);

            // Log the shift creation
            $supervisor = User::find($request->supervisor_id);
            \App\Models\ShiftOperationLog::logOperation(
                $shift,
                \App\Models\ShiftOperationLog::OPERATION_CREATE,
                oldData: [],
                newData: [
                    'shift_code' => $shift->shift_code,
                    'supervisor_id' => $supervisor->id,
                    'supervisor_name' => $supervisor->name,
                    'shift_type' => $shift->shift_type,
                    'stage_number' => $shift->stage_number,
                    'workers_count' => count($workerIds),
                    'worker_ids' => $workerIds,
                    'shift_date' => $shift->shift_date->format('Y-m-d'),
                ],
                description: "تم إنشاء وردية جديدة {$shift->shift_code}",
                stageNumber: (string) $request->stage_number
            );

            // تسجيل تتبع العمال: إضافة كل عامل إلى المرحلة
            if (!empty($workerIds)) {
                // إذا كان هناك stage_number استخدمه، وإلا استخدم stage_record_barcode
                $stageNumber = $request->stage_number;
                $stageRecordId = $request->stage_record_id;

                if (!empty($stageNumber) && !empty($stageRecordId)) {
                    $stageType = 'stage' . $stageNumber . '_' . $this->getStageTableName($stageNumber);

                    foreach ($workerIds as $workerId) {
                        try {
                            \App\Models\WorkerStageHistory::create([
                                'stage_type' => $stageType,
                                'stage_record_id' => $stageRecordId,
                                'barcode' => $request->stage_record_barcode,
                                'worker_id' => $workerId,
                                'worker_type' => 'individual',
                                'started_at' => now(),
                                'ended_at' => null,
                                'is_active' => true,
                                'shift_assignment_id' => $shift->id,
                                'assigned_by' => auth()->user()->id,
                                'notes' => 'تعيين أولي للعامل في الوردية'
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Error creating worker stage history: ' . $e->getMessage(), [
                                'worker_id' => $workerId,
                                'stage_type' => $stageType,
                                'stage_record_id' => $stageRecordId,
                                'shift_id' => $shift->id
                            ]);
                        }
                    }
                }
            }

            // Store notification
            $this->notifyCreate(
                'وردية',
                $shift->shift_code,
                route('manufacturing.shifts-workers.show', $shift->id)
            );

            DB::commit();

            return redirect()->route('manufacturing.shifts-workers.index')
                ->with('success', 'تم إنشاء الوردية بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الوردية: ' . $e->getMessage());

        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * Display pending handovers for current user
     */
    public function handoversIndex()
    {
        // جلب الطلبات المعلقة التي ينتظرها المستخدم الحالي
        $handovers = \App\Models\ShiftHandover::where('to_user_id', auth()->user()->id)
            ->with(['fromUser', 'toUser', 'shiftAssignment'])
            ->orderBy('handover_time', 'desc')
            ->get();

        return response()
            ->view('manufacturing::shifts-workers.handover-list', compact('handovers'))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function show($id)
    {
        $shift = ShiftAssignment::with(['user', 'supervisor', 'team'])->findOrFail($id);
        $workers = $shift->workers(); // استدعاء الدالة لجلب العمال

        // جلب الفريق إن وجد
        $team = $shift->team;

        // جلب المسول من supervisor_id
        $supervisor = null;
        if ($shift->supervisor_id) {
            $supervisor = \App\Models\User::find($shift->supervisor_id);
        }

        return response()
            ->view('manufacturing::shifts-workers.show', compact('shift', 'workers', 'team', 'supervisor'))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Get shift details in JSON format for AJAX requests
     */
    public function getShiftDetails($id)
    {
        $shift = ShiftAssignment::with(['user', 'supervisor'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'shift' => $shift
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $shift = ShiftAssignment::findOrFail($id);

        // Get all workers from workers table
        $workersFromTable = \App\Models\Worker::orderBy('name')->get();

        // Get all users
        $usersWorkers = User::orderBy('name')->get();

        // Merge workers and users, removing duplicates by id
        $workers = collect();

        // Add workers from workers table
        foreach ($workersFromTable as $worker) {
            $workers->push($worker);
        }

        // Add users that are not already in workers
        foreach ($usersWorkers as $user) {
            if (!$workers->contains('id', $user->id)) {
                $workers->push($user);
            }
        }

        // Sort by name
        $workers = $workers->sortBy('name')->values();

        // Get supervisors
        $supervisors = User::orderBy('name')
            ->get();

        // Get active worker teams with their manager and workers
        $teams = \App\Models\WorkerTeam::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function($team) {
                // Get manager name if exists
                $manager = $team->manager;
                $managerName = $manager ? $manager->name : 'لا يوجد مسؤول';

                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'code' => $team->team_code,
                    'manager_id' => $team->manager_id,
                    'manager_name' => $managerName,
                    'worker_ids' => $team->worker_ids ?? [],
                    'workers_count' => count($team->worker_ids ?? [])
                ];
            });

        return view('manufacturing::shifts-workers.edit', compact('shift', 'workers', 'supervisors', 'teams'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $shift = ShiftAssignment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'shift_code' => 'required|string|max:50|unique:shift_assignments,shift_code,' . $id,
            'shift_date' => 'required|date',
            'shift_type' => 'required|in:morning,evening',
            'supervisor_id' => 'required|exists:users,id',
            'team_id' => 'nullable|exists:worker_teams,id',
            'stage_number' => 'nullable|integer|between:0,4',
            'stage_record_barcode' => 'nullable|string|max:100',
            'stage_record_id' => 'nullable|integer',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
            'workers' => 'nullable|array',
            'workers.*' => 'exists:workers,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            // Save old data for logging
            $oldData = [
                'supervisor_id' => $shift->supervisor_id,
                'supervisor_name' => $shift->supervisor?->name,
                'stage_number' => $shift->stage_number,
                'stage_record_id' => $shift->stage_record_id,
                'workers_count' => $shift->total_workers,
                'worker_ids' => $shift->worker_ids,
            ];

            $workerIds = $request->input('workers', []);
            $teamId = $request->input('team_id');

            // Ensure worker_ids is an array and clean it
            if (!is_array($workerIds)) {
                $workerIds = [];
            }
            $workerIds = array_filter($workerIds);
            $workerIds = array_values($workerIds);

            $shift->update([
                'shift_code' => $request->shift_code,
                'shift_type' => $request->shift_type,
                'user_id' => $request->supervisor_id,
                'supervisor_id' => $request->supervisor_id,
                'team_id' => $teamId,
                'stage_number' => $request->stage_number ?? 0,
                'stage_record_barcode' => $request->stage_record_barcode,
                'stage_record_id' => $request->stage_record_id,
                'shift_date' => $request->shift_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'notes' => $request->notes,
                'total_workers' => count($workerIds),
                'worker_ids' => $workerIds,
            ]);

            // Log the update
            $newSupervisor = User::find($request->supervisor_id);
            \App\Models\ShiftOperationLog::logOperation(
                $shift,
                \App\Models\ShiftOperationLog::OPERATION_UPDATE,
                oldData: $oldData,
                newData: [
                    'supervisor_id' => $request->supervisor_id,
                    'supervisor_name' => $newSupervisor?->name,
                    'stage_number' => $request->stage_number ?? 0,
                    'workers_count' => count($workerIds),
                    'worker_ids' => $workerIds,
                ],
                description: "تم تحديث الوردية {$shift->shift_code}",
                stageNumber: (string) ($request->stage_number ?? 0)
            );

            // تسجيل تتبع العمال: إذا تم تغيير قائمة العمال
            $oldWorkerIds = $oldData['worker_ids'] ?? [];
            if ($oldWorkerIds !== $workerIds) {
                // إنهاء تتبع العمال القدامى
                if (!empty($oldWorkerIds) && $oldData['stage_number']) {
                    \App\Models\WorkerStageHistory::where('stage_type', 'stage' . $oldData['stage_number'] . '_' . $this->getStageTableName($oldData['stage_number']))
                        ->where('stage_record_id', $oldData['stage_record_id'] ?? 0)
                        ->where('shift_assignment_id', $shift->id)
                        ->whereNull('ended_at')
                        ->where('is_active', true)
                        ->update([
                            'ended_at' => now(),
                            'is_active' => false,
                            'notes' => 'تم إزالة العامل من الوردية'
                        ]);
                }

                // إضافة العمال الجدد
                if (!empty($workerIds) && $request->stage_number && $request->stage_record_id) {
                    $stageType = 'stage' . $request->stage_number . '_' . $this->getStageTableName($request->stage_number);

                    foreach ($workerIds as $workerId) {
                        // تحقق من عدم وجود السجل بالفعل
                        $existingHistory = \App\Models\WorkerStageHistory::where('stage_type', $stageType)
                            ->where('stage_record_id', $request->stage_record_id)
                            ->where('shift_assignment_id', $shift->id)
                            ->where('worker_id', $workerId)
                            ->whereNull('ended_at')
                            ->first();

                        if (!$existingHistory) {
                            try {
                                \App\Models\WorkerStageHistory::create([
                                    'stage_type' => $stageType,
                                    'stage_record_id' => $request->stage_record_id,
                                    'barcode' => $request->stage_record_barcode,
                                    'worker_id' => $workerId,
                                    'worker_type' => 'individual',
                                    'started_at' => now(),
                                    'ended_at' => null,
                                    'is_active' => true,
                                    'shift_assignment_id' => $shift->id,
                                    'assigned_by' => auth()->user()->id,
                                    'notes' => 'عامل مضاف أثناء تحديث الوردية'
                                ]);
                            } catch (\Exception $e) {
                                \Log::error('Error creating worker stage history on update: ' . $e->getMessage(), [
                                    'worker_id' => $workerId,
                                    'stage_record_id' => $request->stage_record_id,
                                    'shift_id' => $shift->id
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('manufacturing.shifts-workers.show', $shift->id)
                ->with('success', 'تم تحديث الوردية بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الوردية: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $shift = ShiftAssignment::findOrFail($id);

            // Prevent deletion of active or completed shifts
            if (in_array($shift->status, [ShiftAssignment::STATUS_ACTIVE, ShiftAssignment::STATUS_COMPLETED])) {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف وردية نشطة أو مكتملة');
            }

            $shift->delete();

            return redirect()->route('manufacturing.shifts-workers.index')
                ->with('success', 'تم حذف الوردية بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الوردية: ' . $e->getMessage());
        }
    }

    /**
     * Display current active shifts.
     */
    public function current()
    {
        $currentShifts = ShiftAssignment::with(['user', 'supervisor'])
            ->where('status', ShiftAssignment::STATUS_ACTIVE)
            ->whereDate('shift_date', today())
            ->orderBy('start_time')
            ->get();

        return view('manufacturing::shifts-workers.current', compact('currentShifts'));
    }

    /**
     * Display attendance records.
     */
    public function attendance(Request $request)
    {
        $date = $request->input('date', today()->format('Y-m-d'));

        $shifts = ShiftAssignment::with(['user', 'supervisor'])
            ->whereDate('shift_date', $date)
            ->orderBy('start_time')
            ->get();

        return view('manufacturing::shifts-workers.attendance', compact('shifts', 'date'));
    }

    /**
     * Activate a shift (mark as active).
     */
    public function activate($id)
    {
        try {
            $shift = ShiftAssignment::findOrFail($id);

            if ($shift->status !== ShiftAssignment::STATUS_SCHEDULED) {
                return redirect()->back()
                    ->with('error', 'يمكن تفعيل الورديات المجدولة فقط');
            }

            $shift->update(['status' => ShiftAssignment::STATUS_ACTIVE]);

            // Store notification
            $this->notifyStatusChange(
                'وردية',
                'مجدولة',
                'نشطة',
                $shift->shift_code,
                route('manufacturing.shifts-workers.show', $shift->id)
            );

            return redirect()->back()
                ->with('success', 'تم تفعيل الوردية بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Complete a shift (mark as completed).
     */
    public function complete($id)
    {
        try {
            $shift = ShiftAssignment::findOrFail($id);

            if ($shift->status !== ShiftAssignment::STATUS_ACTIVE) {
                return redirect()->back()
                    ->with('error', 'يمكن إكمال الورديات النشطة فقط');
            }

            $shift->update([
                'status' => ShiftAssignment::STATUS_COMPLETED,
                'actual_end_time' => now(),
                'completed_at' => now(),
            ]);

            // Store notification
            $this->notifyStatusChange(
                'وردية',
                'نشطة',
                'مكتملة',
                $shift->shift_code,
                route('manufacturing.shifts-workers.show', $shift->id)
            );

            return redirect()->back()
                ->with('success', 'تم إكمال الوردية بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Pause/Suspend a shift (mark as suspended).
     */
    public function suspend($id, Request $request)
    {
        try {
            $shift = ShiftAssignment::findOrFail($id);

            if ($shift->status !== ShiftAssignment::STATUS_ACTIVE) {
                return redirect()->back()
                    ->with('error', 'يمكن تعليق الورديات النشطة فقط');
            }

            $request->validate([
                'suspension_reason' => 'nullable|string|max:500',
            ]);

            $suspensionReason = $request->input('suspension_reason') ?? 'بدون سبب محدد';

            $shift->update([
                'status' => ShiftAssignment::STATUS_SUSPENDED,
                'suspension_reason' => $request->input('suspension_reason'),
                'suspended_at' => now(),
            ]);

            // Store notification
            $this->storeNotification(
                'shift_suspended',
                'تعليق وردية',
                "تم تعليق الوردية {$shift->shift_code} - السبب: {$suspensionReason}",
                'warning',
                'fas fa-pause-circle',
                route('manufacturing.shifts-workers.show', $shift->id)
            );

            return redirect()->back()
                ->with('success', 'تم تعليق الوردية بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Resume a suspended shift (mark as active).
     */
    public function resume($id)
    {
        try {
            $shift = ShiftAssignment::findOrFail($id);

            if ($shift->status !== ShiftAssignment::STATUS_SUSPENDED) {
                return redirect()->back()
                    ->with('error', 'يمكن استئناف الورديات المعلقة فقط');
            }

            $shift->update([
                'status' => ShiftAssignment::STATUS_ACTIVE,
                'resumed_at' => now(),
            ]);

            // Store notification
            $this->notifyStatusChange(
                'وردية',
                'معلقة',
                'نشطة',
                $shift->shift_code,
                route('manufacturing.shifts-workers.show', $shift->id)
            );

            return redirect()->back()
                ->with('success', 'تم استئناف الوردية بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Generate shift code automatically.
     */
    public function generateShiftCode(Request $request)
    {
        $date = $request->input('date', today()->format('Y-m-d'));
        $type = $request->input('type', 'morning');

        $typePrefix = match($type) {
            'morning' => 'M',
            'evening' => 'E',
            'night' => 'N',
            default => 'S',
        };

        $dateStr = Carbon::parse($date)->format('Ymd');
        $count = ShiftAssignment::whereDate('shift_date', $date)
            ->where('shift_type', $type)
            ->count() + 1;

        $code = "SH-{$typePrefix}-{$dateStr}-" . str_pad($count, 3, '0', STR_PAD_LEFT);

        return response()->json(['shift_code' => $code]);
    }

    /**
     * Get team details with supervisor information
     */
    public function getTeamDetails($teamId)
    {
        try {
            $team = \App\Models\WorkerTeam::with('manager')->findOrFail($teamId);

            return response()->json([
                'success' => true,
                'team' => [
                    'id' => $team->id,
                    'name' => $team->name,
                    'code' => $team->team_code,
                    'manager_id' => $team->manager_id,
                    'manager_name' => $team->manager ? $team->manager->name : 'لا يوجد مسؤول',
                    'worker_ids' => $team->worker_ids ?? [],
                    'workers_count' => count($team->worker_ids ?? [])
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'الفريق غير موجود'
            ], 404);
        }
    }

    /**
     * عرض صفحة نقل الوردية
     */
    public function transferView($id)
    {
        try {
            $shift = ShiftAssignment::with(['supervisor'])->findOrFail($id);

            // الحصول على العمال الحاليين
            $workers = Worker::whereIn('id', $shift->worker_ids ?? [])->get();

            // الحصول على جميع العمال المتاحين
            $allWorkers = Worker::where('is_active', true)->get();

            // الحصول على جميع المسؤولين
            $supervisors = User::where('is_active', true)->get();

            // الحصول على جميع فئات العمال (Teams)
            $teams = \App\Models\WorkerTeam::where('is_active', true)->get();

            return view('manufacturing::shifts-workers.transfer', [
                'currentShift' => $shift,
                'supervisor' => $shift->supervisor,
                'workers' => $workers,
                'allWorkers' => $allWorkers,
                'supervisors' => $supervisors,
                'teams' => $teams,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in transferView: ' . $e->getMessage());
            return redirect()->route('manufacturing.shifts-workers.index')
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * حفظ نقل الوردية
     */
    public function transferStore(Request $request, $id)
    {
        try {
            // تنظيف البيانات أولاً
            $supervisorId = (int) $request->input('new_supervisor_id');

            $newWorkers = $request->input('new_workers', []);
            if (is_string($newWorkers)) {
                $newWorkers = json_decode($newWorkers, true) ?? [];
            }
            if (!is_array($newWorkers)) {
                $newWorkers = [];
            }
            // تحويل إلى integers
            $newWorkers = array_map(function($w) {
                return (int) $w;
            }, $newWorkers);
            $newWorkers = array_filter($newWorkers);
            $newWorkers = array_values($newWorkers);

            $validator = Validator::make([
                'new_supervisor_id' => $supervisorId,
                'new_workers' => $newWorkers,
                'transfer_notes' => $request->transfer_notes,
            ], [
                'new_supervisor_id' => 'required|integer|exists:users,id',
                'new_workers' => 'array',
                'new_workers.*' => 'integer|exists:workers,id',
                'transfer_notes' => 'nullable|string|max:1000',
            ], [
                'new_supervisor_id.required' => 'المسؤول الجديد مطلوب',
                'new_supervisor_id.integer' => 'المسؤول يجب أن يكون رقماً',
                'new_supervisor_id.exists' => 'المسؤول المختار غير موجود',
                'new_workers.array' => 'العمال يجب أن يكونوا مصفوفة',
                'new_workers.*.integer' => 'كل عامل يجب أن يكون رقماً',
                'new_workers.*.exists' => 'أحد العمال المختارين غير موجود',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'خطأ في البيانات: ' . implode(', ', $validator->errors()->all()));
            }

            DB::beginTransaction();

            $shift = ShiftAssignment::findOrFail($id);

            // حفظ البيانات القديمة
            $oldData = [
                'supervisor_id' => $shift->supervisor_id,
                'supervisor_name' => $shift->supervisor ? $shift->supervisor->name : 'لا يوجد',
                'workers_count' => $shift->total_workers,
                'worker_ids' => $shift->worker_ids,
            ];

            $newWorkerIds = $newWorkers;

            // ===== تنهاية تتبع العمال القدامى =====
            if (!empty($oldData['worker_ids']) && $shift->stage_number && $shift->stage_record_id) {
                \Log::info('Ending old workers tracking', [
                    'shift_id' => $shift->id,
                    'stage_number' => $shift->stage_number,
                    'stage_record_id' => $shift->stage_record_id,
                    'old_workers' => $oldData['worker_ids']
                ]);

                $stageName = $this->getStageTableName($shift->stage_number);
                $stageType = 'stage' . $shift->stage_number . '_' . $stageName;

                $updated = \App\Models\WorkerStageHistory::where('stage_type', $stageType)
                    ->where('stage_record_id', $shift->stage_record_id)
                    ->where('shift_assignment_id', $shift->id)
                    ->whereNull('ended_at')
                    ->where('is_active', true)
                    ->update([
                        'ended_at' => now(),
                        'is_active' => false,
                        'notes' => 'تم نقل الوردية - ' . ($request->transfer_notes ?? '')
                    ]);

                \Log::info('Old workers tracking ended', [
                    'records_updated' => $updated,
                    'shift_id' => $shift->id
                ]);
            }

            // ===== إضافة تتبع العمال الجدد =====
            if (!empty($newWorkerIds) && $shift->stage_number && $shift->stage_record_id) {
                \Log::info('Starting new workers tracking', [
                    'shift_id' => $shift->id,
                    'stage_number' => $shift->stage_number,
                    'stage_record_id' => $shift->stage_record_id,
                    'new_workers' => $newWorkerIds
                ]);

                $stageName = $this->getStageTableName($shift->stage_number);
                $stageType = 'stage' . $shift->stage_number . '_' . $stageName;

                foreach ($newWorkerIds as $workerId) {
                    try {
                        $workerHistory = \App\Models\WorkerStageHistory::create([
                            'stage_type' => $stageType,
                            'stage_record_id' => $shift->stage_record_id,
                            'barcode' => $shift->stage_record_barcode,
                            'worker_id' => $workerId,
                            'worker_type' => 'individual',
                            'started_at' => now(),
                            'ended_at' => null,
                            'is_active' => true,
                            'shift_assignment_id' => $shift->id,
                            'assigned_by' => auth()->user()->id,
                            'notes' => 'عامل جديد من نقل الوردية'
                        ]);

                        \Log::info('Worker tracking record created', [
                            'worker_id' => $workerId,
                            'record_id' => $workerHistory->id,
                            'stage_record_id' => $shift->stage_record_id
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Error creating worker stage history on transfer: ' . $e->getMessage(), [
                            'worker_id' => $workerId,
                            'stage_record_id' => $shift->stage_record_id,
                            'shift_id' => $shift->id,
                            'error' => $e->getTraceAsString()
                        ]);
                        throw $e;
                    }
                }
            }

            // ===== تحديث الوردية مباشرة =====
            $shift->update([
                'supervisor_id' => $supervisorId,
                'user_id' => $supervisorId,
                'worker_ids' => $newWorkerIds,
                'total_workers' => count($newWorkerIds),
                'notes' => ($shift->notes ?? '') . "\n[نقل مباشر] " . ($request->transfer_notes ?? ''),
            ]);

            \Log::info('Shift updated after transfer', [
                'shift_id' => $shift->id,
                'new_supervisor_id' => $supervisorId,
                'new_workers_count' => count($newWorkerIds)
            ]);

            // ===== تسجيل عملية النقل في الـ log =====
            try {
                \App\Models\ShiftOperationLog::logOperation(
                    $shift,
                    \App\Models\ShiftOperationLog::OPERATION_TRANSFER,
                    oldData: [
                        'supervisor_id' => $oldData['supervisor_id'],
                        'supervisor_name' => $oldData['supervisor_name'],
                        'workers_count' => $oldData['workers_count'],
                        'worker_ids' => $oldData['worker_ids'],
                    ],
                    newData: [
                        'supervisor_id' => $supervisorId,
                        'supervisor_name' => User::find($supervisorId)?->name,
                        'workers_count' => count($newWorkerIds),
                        'worker_ids' => $newWorkerIds,
                    ],
                    description: "تم نقل الوردية من {$oldData['supervisor_name']} إلى " . User::find($supervisorId)?->name,
                    notes: $request->transfer_notes ?? ''
                );
            } catch (\Exception $e) {
                \Log::error('Error logging shift transfer operation: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('manufacturing.shifts-workers.show', $shift->id)
                ->with('success', 'تم نقل الوردية والعمال بنجاح وتم تسجيلهم في النظام');

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            \Log::error('Shift Transfer Error', [
                'shift_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء نقل الوردية: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * الموافقة على نقل الوردية (Acknowledge Transfer)
     */
    public function acknowledgeTransfer(Request $request, $handoverId)
    {
        try {
            DB::beginTransaction();

            $handover = \App\Models\ShiftHandover::findOrFail($handoverId);

            // التحقق من أن المستخدم الحالي هو المسؤول الجديد
            if ($handover->to_user_id !== auth()->user()->id) {
                return redirect()->back()
                    ->with('error', 'لا توجد صلاحيات لتأكيد هذا النقل');
            }

            // التحقق من أن الوردية موجودة
            $shift = ShiftAssignment::findOrFail($handover->shift_assignment_id);

            // جمع بيانات العمال الجدد من handover items
            $newWorkerIds = array_map(function($item) {
                return $item['worker_id'];
            }, $handover->handover_items);

            $oldData = [
                'supervisor_id' => $shift->supervisor_id,
                'supervisor_name' => $shift->supervisor ? $shift->supervisor->name : 'لا يوجد',
                'workers_count' => $shift->total_workers,
                'worker_ids' => $shift->worker_ids,
            ];

            // تحديث الوردية بالمسؤول والعمال الجدد
            $shift->update([
                'supervisor_id' => $handover->to_user_id,
                'user_id' => $handover->to_user_id,
                'worker_ids' => $newWorkerIds,
                'total_workers' => count($newWorkerIds),
                'notes' => ($shift->notes ?? '') . "\n[نقل موافق عليه] " . ($handover->notes ?? ''),
            ]);

            // تسجيل الموافقة على النقل
            $handover->acknowledge(auth()->user()->id);

            // تسجيل عملية النقل
            \App\Models\ShiftOperationLog::logOperation(
                $shift,
                \App\Models\ShiftOperationLog::OPERATION_TRANSFER,
                oldData: [
                    'supervisor_id' => $oldData['supervisor_id'],
                    'supervisor_name' => $oldData['supervisor_name'],
                    'workers_count' => $oldData['workers_count'],
                    'worker_ids' => $oldData['worker_ids'],
                ],
                newData: [
                    'supervisor_id' => $handover->to_user_id,
                    'supervisor_name' => User::find($handover->to_user_id)?->name,
                    'workers_count' => count($newWorkerIds),
                    'worker_ids' => $newWorkerIds,
                ],
                description: "تم نقل الوردية من {$oldData['supervisor_name']} إلى {User::find($handover->to_user_id)?->name} (موافق)",
                notes: $handover->notes ?? ''
            );

            // تسجيل تتبع العمال: إنهاء تتبع العمال القدامى
            if (!empty($oldData['worker_ids']) && $shift->stage_number) {
                \App\Models\WorkerStageHistory::where('stage_type', 'stage' . $shift->stage_number . '_' . $this->getStageTableName($shift->stage_number))
                    ->where('stage_record_id', $shift->stage_record_id)
                    ->where('shift_assignment_id', $shift->id)
                    ->whereNull('ended_at')
                    ->where('is_active', true)
                    ->update([
                        'ended_at' => now(),
                        'is_active' => false,
                        'notes' => ($handover->notes ?? '') . ' [تم نقل الوردية - موافق عليه]'
                    ]);
            }

            // تسجيل تتبع العمال الجدد: إضافة العمال الجدد
            if (!empty($newWorkerIds) && $shift->stage_number && $shift->stage_record_id) {
                foreach ($newWorkerIds as $workerId) {
                    try {
                        \App\Models\WorkerStageHistory::create([
                            'stage_type' => 'stage' . $shift->stage_number . '_' . $this->getStageTableName($shift->stage_number),
                            'stage_record_id' => $shift->stage_record_id,
                            'barcode' => $shift->stage_record_barcode,
                            'worker_id' => $workerId,
                            'worker_type' => 'individual',
                            'started_at' => now(),
                            'ended_at' => null,
                            'is_active' => true,
                            'shift_assignment_id' => $shift->id,
                            'assigned_by' => auth()->user()->id,
                            'notes' => 'عامل جديد من نقل الوردية (موافق عليه)'
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Error creating worker stage history on transfer: ' . $e->getMessage(), [
                            'worker_id' => $workerId,
                            'stage_record_id' => $shift->stage_record_id,
                            'shift_id' => $shift->id
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('manufacturing.shifts-workers.show', $shift->id)
                ->with('success', 'تم الموافقة على نقل الوردية بنجاح');

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            \Log::error('Shift Transfer Acknowledgement Error', [
                'handover_id' => $handoverId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء الموافقة على النقل: ' . $e->getMessage());
        }
    }

    /**
     * رفض نقل الوردية
     */
    public function rejectTransfer(Request $request, $handoverId)
    {
        try {
            $handover = \App\Models\ShiftHandover::findOrFail($handoverId);

            // التحقق من أن المستخدم الحالي هو المسؤول الجديد
            if ($handover->to_user_id !== auth()->user()->id) {
                return redirect()->back()
                    ->with('error', 'لا توجد صلاحيات لرفض هذا النقل');
            }

            $shift = ShiftAssignment::findOrFail($handover->shift_assignment_id);
            $oldSupervisor = User::find($handover->from_user_id);
            $newSupervisor = User::find($handover->to_user_id);

            $handover->update([
                'supervisor_approved' => false,
                'approved_by' => auth()->user()->id,
            ]);

            \Log::info('Shift Transfer Rejected', [
                'handover_id' => $handover->id,
                'shift_id' => $shift->id,
                'from_supervisor' => $oldSupervisor?->name,
                'to_supervisor' => $newSupervisor?->name,
            ]);

            return redirect()->back()
                ->with('success', 'تم رفض النقل');

        } catch (\Exception $e) {
            \Log::error('Shift Transfer Rejection Error', [
                'handover_id' => $handoverId,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء رفض النقل: ' . $e->getMessage());
        }
    }

    /**
     * جلب تفاصيل نقل الوردية (JSON API)
     */
    public function getHandoverDetails($id)
    {
        try {
            $shift = ShiftAssignment::with(['supervisor'])->findOrFail($id);

            $workers = \App\Models\Worker::whereIn('id', $shift->worker_ids ?? [])->get();

            return response()->json([
                'success' => true,
                'shift' => [
                    'id' => $shift->id,
                    'shift_code' => $shift->shift_code,
                    'shift_date' => $shift->shift_date->format('Y-m-d'),
                    'shift_type' => $shift->shift_type,
                    'stage_number' => $shift->stage_number,
                    'start_time' => $shift->start_time,
                    'end_time' => $shift->end_time,
                    'supervisor' => [
                        'id' => $shift->supervisor_id,
                        'name' => $shift->supervisor ? $shift->supervisor->name : 'لا يوجد',
                    ],
                    'workers' => $workers->map(function($worker) {
                        return [
                            'id' => $worker->id,
                            'name' => $worker->name,
                            'position' => $worker->position,
                            'assigned_stage' => $worker->assigned_stage,
                        ];
                    }),
                    'total_workers' => count($workers),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'الوردية غير موجودة'
            ], 404);
        }
    }

    /**
     * تحديد المرحلة للعامل من الوردية
     */
    public function assignStageToWorker(Request $request, $shiftId)
    {
        $validator = Validator::make($request->all(), [
            'worker_id' => 'required|exists:workers,id',
            'stage_number' => 'required|integer|between:1,4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة'
            ], 422);
        }

        try {
            $shift = ShiftAssignment::findOrFail($shiftId);

            // تحديث المرحلة للعامل
            $worker = \App\Models\Worker::findOrFail($request->worker_id);
            $worker->update(['assigned_stage' => $request->stage_number]);

            // تسجيل العملية
            \Log::info('Worker Stage Assignment', [
                'worker_id' => $worker->id,
                'worker_name' => $worker->name,
                'shift_id' => $shift->id,
                'shift_code' => $shift->shift_code,
                'assigned_stage' => $request->stage_number,
                'assigned_by' => auth()->user()->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تعيين المرحلة للعامل بنجاح',
                'worker' => [
                    'id' => $worker->id,
                    'name' => $worker->name,
                    'assigned_stage' => $worker->assigned_stage,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to send notifications
     */
    private function sendNotification($shift, $type, $title, $message, $userId = null)
    {
        try {
            $user = $userId ? User::find($userId) : auth()->user();

            if (!$user) {
                return;
            }

            // Store notification
            $notification = [
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'shift_id' => $shift->id,
                'shift_code' => $shift->shift_code,
                'read_at' => null,
            ];

            $user->notifications()->create($notification);

        } catch (\Exception $e) {
            \Log::error('Failed to send notification', [
                'error' => $e->getMessage(),
                'shift_id' => $shift->id,
            ]);
        }
    }

    /**
     * Get stage table name from stage number
     */
    private function getStageTableName($stageNumber)
    {
        return match($stageNumber) {
            1 => 'stands',
            2 => 'processed',
            3 => 'coils',
            4 => 'boxes',
            default => 'unknown'
        };
    }

    /**
     * Load stage records (barcodes) for a specific stage (AJAX)
     */
    public function getStageRecords(Request $request)
    {
        try {
            $stageNumber = (int) $request->input('stage_number');

            if (!in_array($stageNumber, [1, 2, 3, 4])) {
                return response()->json([
                    'success' => false,
                    'message' => 'رقم المرحلة غير صحيح'
                ], 400);
            }

            $tableName = match($stageNumber) {
                1 => 'stage1_stands',
                2 => 'stage2_processed',
                3 => 'stage3_coils',
                4 => 'stage4_boxes',
            };

            $records = DB::table($tableName)
                ->select('id', 'barcode')
                ->orderBy('barcode', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'records' => $records,
                'count' => $records->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading stage records', [
                'stage_number' => $request->input('stage_number'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل السجلات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على بيانات المسؤول
     */
    public function getSupervisor($supervisorId)
    {
        try {
            $supervisor = User::findOrFail($supervisorId);
            return response()->json([
                'id' => $supervisor->id,
                'name' => $supervisor->name,
                'email' => $supervisor->email,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'المسؤول غير موجود'
            ], 404);
        }
    }

    /**
     * الحصول على بيانات العمال
     */
    public function getWorkers(Request $request)
    {
        try {
            $workerIds = $request->input('worker_ids', []);

            if (empty($workerIds)) {
                return response()->json([
                    'workers' => []
                ]);
            }

            $workers = Worker::whereIn('id', $workerIds)
                ->get()
                ->map(function($worker) {
                    return [
                        'id' => $worker->id,
                        'worker_code' => $worker->worker_code,
                        'name' => $worker->name,
                        'position' => $worker->position ?? 'غير محدد',
                    ];
                });

            return response()->json([
                'workers' => $workers
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading workers: ' . $e->getMessage());
            return response()->json([
                'error' => 'حدث خطأ في تحميل العمال'
            ], 500);
        }
    }

    /**
     * الحصول على العمال من الوردية الحالية للاستاند
     */
    public function getStandWorkers($standId)
    {
        try {
            $stand = \Modules\Manufacturing\Entities\Stand::findOrFail($standId);

            // 🔥 البحث عن الوردية بطرق متعددة:
            // 1. البحث المباشر بـ stage_record_id
            $shift = ShiftAssignment::where('stage_record_id', $standId)
                ->orderBy('created_at', 'desc')
                ->first();

            // 2. إذا لم نجد، نبحث عن الوردية من خلال العامل الذي أنشأ الاستاند في نفس اليوم
            if (!$shift && $stand->created_by) {
                $standDate = $stand->created_at ? $stand->created_at->format('Y-m-d') : today()->format('Y-m-d');

                // جلب شيفت نشط أو مكتمل حديث للعامل في نفس اليوم
                $shift = ShiftAssignment::whereIn('status', ['active', 'completed', 'scheduled'])
                    ->where('shift_date', $standDate)
                    ->where(function($query) use ($stand) {
                        $query->where('user_id', $stand->created_by)
                            ->orWhere(function($q) use ($stand) {
                                // البحث عن وردية تحتوي على العامل في worker_ids
                                if ($stand->created_by) {
                                    $q->whereJsonContains('worker_ids', $stand->created_by);
                                }
                            });
                    })
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            // 3. إذا لم نجد وردية، نبحث عن أي وردية نشطة في نفس اليوم
            if (!$shift) {
                $standDate = $stand->created_at ? $stand->created_at->format('Y-m-d') : today()->format('Y-m-d');
                $shift = ShiftAssignment::where('shift_date', $standDate)
                    ->whereIn('status', ['active', 'completed'])
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            if (!$shift) {
                return response()->json([
                    'workers' => [],
                    'count' => 0,
                    'stand_number' => $stand->stand_number,
                    'supervisor' => null,
                    'message' => 'لا توجد وردية مرتبطة بهذا الاستاند',
                ]);
            }

            // جلب العمال من worker_ids في الوردية
            $workerIds = $shift->worker_ids ?? [];
            $workers = [];
            $supervisor = null;

            if (!empty($workerIds) && is_array($workerIds)) {
                // 🔥 البحث عن العمال: قد تكون Worker IDs أو User IDs
                // نحاول الأول مع Worker model ثم مع User model
                $workers = Worker::whereIn('id', $workerIds)
                    ->select('id', 'worker_code', 'name', 'position')
                    ->get()
                    ->map(function($worker) {
                        return [
                            'id' => $worker->id,
                            'worker_code' => $worker->worker_code ?? $worker->id,
                            'name' => $worker->name,
                            'position' => $worker->position ?? 'غير محدد',
                        ];
                    })
                    ->toArray();

                // إذا لم نجد عمال (ربما كانت User IDs)، نبحث عن Users
                if (empty($workers)) {
                    $workers = User::whereIn('id', $workerIds)
                        ->select('id', 'name', 'email')
                        ->get()
                        ->map(function($user) {
                            return [
                                'id' => $user->id,
                                'worker_code' => $user->id,
                                'name' => $user->name,
                                'position' => 'موظف',
                            ];
                        })
                        ->toArray();
                }
            }

            // جلب بيانات المسؤول
            if ($shift->supervisor_id) {
                $supervisorUser = User::find($shift->supervisor_id);
                if ($supervisorUser) {
                    $supervisor = [
                        'id' => $supervisorUser->id,
                        'name' => $supervisorUser->name,
                        'email' => $supervisorUser->email,
                    ];
                }
            }

            return response()->json([
                'workers' => $workers,
                'count' => count($workers),
                'stand_number' => $stand->stand_number,
                'shift_id' => $shift->id,
                'shift_code' => $shift->shift_code,
                'supervisor_id' => $shift->supervisor_id,
                'supervisor' => $supervisor,
                'shift_type' => $shift->shift_type,
                'shift_date' => $shift->shift_date,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading stand workers: ' . $e->getMessage(), [
                'stand_id' => $standId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'حدث خطأ في تحميل عمال الاستاند: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * نقل العمال من استاند لآخر مع التسجيل في ShiftHandover
     */
    public function transferStandWorkers(Request $request, $standId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'from_shift_id' => 'required|exists:shift_assignments,id',
                'to_shift_id' => 'required|exists:shift_assignments,id',
                'worker_ids' => 'required|array',
                'worker_ids.*' => 'exists:workers,id',
                'transfer_notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => implode(', ', $validator->errors()->all())
                ], 422);
            }

            DB::beginTransaction();

            $stand = \Modules\Manufacturing\Entities\Stand::findOrFail($standId);
            $fromShift = ShiftAssignment::findOrFail($request->from_shift_id);
            $toShift = ShiftAssignment::findOrFail($request->to_shift_id);
            $workerIds = array_map('intval', $request->worker_ids);

            // البيانات القديمة (قبل)
            $beforeData = [
                'from_shift_id' => $fromShift->id,
                'from_shift_code' => $fromShift->shift_code,
                'from_supervisor_id' => $fromShift->supervisor_id,
                'from_supervisor_name' => $fromShift->supervisor?->name,
                'workers' => Worker::whereIn('id', array_intersect($workerIds, $fromShift->worker_ids ?? []))
                    ->select('id', 'name', 'worker_code')
                    ->get()
                    ->map(function($w) { return ['id' => $w->id, 'name' => $w->name, 'worker_code' => $w->worker_code]; })
                    ->toArray(),
            ];

            // تحديث الوردية الأصلية: إزالة العمال
            $fromWorkers = $fromShift->worker_ids ?? [];
            $remainingWorkers = array_diff($fromWorkers, $workerIds);
            $fromShift->update([
                'worker_ids' => array_values($remainingWorkers),
                'total_workers' => count($remainingWorkers),
            ]);

            // تحديث الوردية المستقبلة: إضافة العمال
            $toWorkers = $toShift->worker_ids ?? [];
            $newToWorkers = array_unique(array_merge($toWorkers, $workerIds));
            $toShift->update([
                'worker_ids' => $newToWorkers,
                'total_workers' => count($newToWorkers),
            ]);

            // البيانات الجديدة (بعد)
            $afterData = [
                'to_shift_id' => $toShift->id,
                'to_shift_code' => $toShift->shift_code,
                'to_supervisor_id' => $toShift->supervisor_id,
                'to_supervisor_name' => $toShift->supervisor?->name,
                'workers' => Worker::whereIn('id', $workerIds)
                    ->select('id', 'name', 'worker_code')
                    ->get()
                    ->map(function($w) { return ['id' => $w->id, 'name' => $w->name, 'worker_code' => $w->worker_code]; })
                    ->toArray(),
            ];

            // تسجيل في ShiftHandover (قبل وبعد)
            $handover = \App\Models\ShiftHandover::create([
                'from_user_id' => $fromShift->supervisor_id,
                'to_user_id' => $toShift->supervisor_id,
                'stage_number' => 1,
                'shift_assignment_id' => $toShift->id,
                'handover_items' => $afterData['workers'],
                'auto_collected' => false,
                'pending_items_count' => count($afterData['workers']),
                'notes' => $request->transfer_notes ?? '',
                'notes_en' => '',
                'handover_time' => now(),
                'acknowledged_at' => now(),
                'acknowledged_by' => auth()->user()->id,
                'supervisor_approved' => true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم نقل العمال بنجاح وتسجيلهم في النظام',
                'handover_id' => $handover->id,
                'before' => $beforeData,
                'after' => $afterData,
            ]);

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            \Log::error('Stand Workers Transfer Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء نقل العمال: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حفظ نقل الوردية مع تمييز العمال الأفراد والمجموعات
     */
    public function transferStoreV2(Request $request, $id)
    {
        try {
            $supervisorId = (int) $request->input('new_supervisor_id');
            $transferType = $request->input('transfer_type', 'individual');
            $individualWorkers = $request->input('individual_workers', []);
            $teams = $request->input('teams', []);
            $transferNotes = $request->transfer_notes ?? '';

            // تنظيف البيانات
            if (is_string($individualWorkers)) {
                $individualWorkers = json_decode($individualWorkers, true) ?? [];
            }
            $individualWorkers = array_map(fn($w) => (int) $w, (array) $individualWorkers);
            $individualWorkers = array_filter($individualWorkers);
            $individualWorkers = array_values($individualWorkers);

            if (is_string($teams)) {
                $teams = json_decode($teams, true) ?? [];
            }
            $teams = (array) $teams;

            $validator = Validator::make([
                'new_supervisor_id' => $supervisorId,
                'transfer_type' => $transferType,
                'individual_workers' => $individualWorkers,
            ], [
                'new_supervisor_id' => 'required|integer|exists:users,id',
                'transfer_type' => 'required|in:individual,team,mixed',
                'individual_workers' => 'array',
                'individual_workers.*' => 'integer|exists:workers,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'خطأ في البيانات: ' . implode(', ', $validator->errors()->all()));
            }

            DB::beginTransaction();

            $shift = ShiftAssignment::findOrFail($id);
            $oldSupervisor = $shift->supervisor;

            // حفظ البيانات القديمة
            $oldData = [
                'supervisor_id' => $shift->supervisor_id,
                'supervisor_name' => $oldSupervisor?->name ?? 'غير محدد',
                'individual_worker_ids' => $shift->individual_worker_ids ?? [],
                'team_groups' => $shift->team_groups ?? [],
            ];

            // تحضير البيانات الجديدة - إضافة للموجود وليس استبدال
            // إضافة العمال الأفراد الجدد للقدامى (بدون تكرار)
            $newIndividualWorkerIds = array_values(array_unique(array_merge(
                $oldData['individual_worker_ids'] ?? [],
                $individualWorkers
            )));

            $newTeamGroups = $oldData['team_groups'] ?? [];
            $newTeamWorkerIds = [];

            // معالجة المجموعات الجديدة وإضافتها
            foreach ($teams as $teamData) {
                if (is_string($teamData)) {
                    $teamData = json_decode($teamData, true);
                }

                if (is_array($teamData) && isset($teamData['team_id'])) {
                    $teamId = (int) $teamData['team_id'];

                    // تحقق من أن المجموعة ليست موجودة بالفعل
                    $teamExists = collect($newTeamGroups)->firstWhere('team_id', $teamId);

                    if (!$teamExists) {
                        $newTeamGroups[] = [
                            'team_id' => $teamId,
                            'team_name' => $teamData['team_name'] ?? '',
                            'worker_ids' => (array) ($teamData['worker_ids'] ?? []),
                            'added_at' => now()->format('Y-m-d H:i:s'),
                        ];
                    }

                    $newTeamWorkerIds = array_merge($newTeamWorkerIds, (array) ($teamData['worker_ids'] ?? []));
                }
            }

            $newTeamWorkerIds = array_values(array_unique($newTeamWorkerIds));

            // لا نهاية تتبع العمال القدامى - سنضيف فقط الجدد
            // العمال القدامى يبقون نشطين

            // إضافة تتبع للعمال الجدد فقط
            $oldAllWorkerIds = array_merge($oldData['individual_worker_ids'],
                array_merge(...array_map(fn($g) => $g['worker_ids'] ?? [], $oldData['team_groups'])));

            // العمال الجدد = المختارين الآن
            $newlyAddedWorkers = array_unique(array_merge($individualWorkers, $newTeamWorkerIds));

            if (!empty($newlyAddedWorkers) && $shift->stage_number && $shift->stage_record_id) {
                $stageName = $this->getStageTableName($shift->stage_number);
                $stageType = 'stage' . $shift->stage_number . '_' . $stageName;

                foreach ($newlyAddedWorkers as $workerId) {
                    // تحقق من أن العامل موجود في جدول workers
                    $worker = Worker::find($workerId);
                    if (!$worker) {
                        \Log::warning("Worker not found: {$workerId}");
                        continue; // تخطي العامل غير الموجود
                    }

                    // تحقق من أن العامل لم يتم إضافته بالفعل
                    $existingRecord = WorkerStageHistory::where('stage_type', $stageType)
                        ->where('stage_record_id', $shift->stage_record_id)
                        ->where('worker_id', $workerId)
                        ->where('is_active', true)
                        ->first();

                    if (!$existingRecord) {
                        $workerType = in_array($workerId, $individualWorkers) ? 'individual' : 'team';

                        try {
                            WorkerStageHistory::create([
                                'stage_type' => $stageType,
                                'stage_record_id' => $shift->stage_record_id,
                                'barcode' => $shift->stage_record_barcode,
                                'worker_id' => $workerId,
                                'worker_type' => $workerType,
                                'started_at' => now(),
                                'ended_at' => null,
                                'is_active' => true,
                                'shift_assignment_id' => $shift->id,
                                'assigned_by' => auth()->user()->id,
                                'notes' => 'عامل جديد تمت إضافته للوردية (' . ($workerType === 'individual' ? 'فردي' : 'مجموعة') . ')'
                            ]);
                        } catch (\Exception $e) {
                            \Log::error("Error creating worker stage history for worker {$workerId}: " . $e->getMessage());
                        }
                    }
                }
            }            // تحديث الوردية
            $shift->update([
                'supervisor_id' => $supervisorId,
                'user_id' => $supervisorId,
                'individual_worker_ids' => $newIndividualWorkerIds,
                'team_worker_ids' => $newTeamWorkerIds,
                'team_groups' => $newTeamGroups,
                'worker_ids' => array_unique(array_merge($newIndividualWorkerIds, $newTeamWorkerIds)),
                'total_workers' => count(array_unique(array_merge($newIndividualWorkerIds, $newTeamWorkerIds))),
                'notes' => ($shift->notes ?? '') . "\n[نقل مباشر] " . $transferNotes,
            ]);

            // حفظ في جدول السجل التاريخي
            ShiftTransferHistory::create([
                'shift_id' => $shift->id,
                'from_supervisor_id' => $oldData['supervisor_id'],
                'to_supervisor_id' => $supervisorId,
                'old_data' => [
                    'individual_worker_ids' => $oldData['individual_worker_ids'],
                    'team_groups' => $oldData['team_groups'],
                ],
                'new_data' => [
                    'individual_worker_ids' => $newIndividualWorkerIds,
                    'team_groups' => $newTeamGroups,
                ],
                'transfer_notes' => $transferNotes,
                'transfer_type' => $transferType,
                'transferred_by' => auth()->user()->id,
                'status' => ShiftTransferHistory::STATUS_COMPLETED,
            ]);

            // تسجيل في ShiftOperationLog
            ShiftOperationLog::logOperation(
                $shift,
                ShiftOperationLog::OPERATION_TRANSFER,
                oldData: $oldData,
                newData: [
                    'supervisor_id' => $supervisorId,
                    'supervisor_name' => User::find($supervisorId)?->name,
                    'individual_worker_ids' => $newIndividualWorkerIds,
                    'team_groups' => $newTeamGroups,
                ],
                description: "تم إضافة عمال للوردية من {$oldData['supervisor_name']} إلى " . User::find($supervisorId)?->name . " (" . $transferType . ")",
                notes: $transferNotes
            );

            DB::commit();

            return redirect()->route('manufacturing.shifts-workers.show', $shift->id)
                ->with('success', 'تم إضافة العمال للوردية بنجاح وتم تسجيلهم في النظام');

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            \Log::error('Shift Transfer Error V2: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء نقل الوردية: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض سجل النقل التاريخي للوردية
     */
    public function transferHistory($id)
    {
        try {
            $shift = ShiftAssignment::with(['supervisor'])->findOrFail($id);

            // الحصول على سجل النقل مع التصفح
            $transfers = ShiftTransferHistory::where('shift_id', $id)
                ->with(['fromSupervisor', 'toSupervisor', 'transferredBy', 'approvedBy'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('manufacturing::shifts-workers.transfer-history', [
                'shift' => $shift,
                'transfers' => $transfers,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in transferHistory: ' . $e->getMessage());
            return redirect()->route('manufacturing.shifts-workers.index')
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}

