<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\ShiftAssignment;
use App\Models\User;
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

            $shift = ShiftAssignment::create([
                'shift_code' => $request->shift_code,
                'shift_type' => $request->shift_type,
                'user_id' => $request->supervisor_id,
                'supervisor_id' => $request->supervisor_id,
                'team_id' => $teamId,
                'stage_number' => $request->stage_number,
                'shift_date' => $request->shift_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => ShiftAssignment::STATUS_SCHEDULED,
                'notes' => $request->notes,
                'is_active' => $request->input('is_active', true),
                'total_workers' => count($workerIds),
                'worker_ids' => $workerIds,
            ]);

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
                'shift_date' => $request->shift_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'notes' => $request->notes,
                'total_workers' => count($workerIds),
                'worker_ids' => $workerIds,
            ]);

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
}

