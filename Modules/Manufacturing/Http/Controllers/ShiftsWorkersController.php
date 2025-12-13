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

            // Save old data for logging
            $oldData = [
                'supervisor_id' => $shift->supervisor_id,
                'supervisor_name' => $shift->supervisor?->name,
                'stage_number' => $shift->stage_number,
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
        $currentShift = ShiftAssignment::with(['user', 'supervisor', 'team'])->findOrFail($id);
        $workers = $currentShift->workers();
        $supervisor = $currentShift->supervisor;

        // جلب الورديات السابقة
        $previousShifts = ShiftAssignment::where('id', '!=', $id)
            ->where('shift_date', $currentShift->shift_date)
            ->with(['user', 'supervisor'])
            ->orderBy('end_time', 'desc')
            ->get();

        // جلب كل المسؤولين المتاحين
        $supervisors = User::orderBy('name')->get();

        // جلب كل العمال
        $allWorkers = \App\Models\Worker::orderBy('name')->get();

        return view('manufacturing::shifts-workers.transfer', compact(
            'currentShift',
            'workers',
            'supervisor',
            'previousShifts',
            'supervisors',
            'allWorkers'
        ));
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

            // استخدام newWorkers المنظفة
            $newWorkerIds = $newWorkers;

            $shift->update([
                'supervisor_id' => $supervisorId,
                'user_id' => $supervisorId,
                'worker_ids' => $newWorkerIds,
                'total_workers' => count($newWorkerIds),
                'notes' => ($shift->notes ?? '') . "\n[نقل وردية] " . ($request->transfer_notes ?? ''),
            ]);

            // تسجيل عملية النقل
            $newSupervisor = User::find($supervisorId);
            
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
                    'supervisor_name' => $newSupervisor?->name,
                    'workers_count' => count($newWorkerIds),
                    'worker_ids' => $newWorkerIds,
                ],
                description: "تم نقل الوردية من {$oldData['supervisor_name']} إلى {$newSupervisor?->name}",
                notes: $request->transfer_notes ?? ''
            );

            // إرسال إشعار
            $this->sendNotification(
                $shift,
                'shift_transferred',
                "تم نقل الوردية بنجاح",
                "تم نقل الوردية من {$oldData['supervisor_name']} إلى {$newSupervisor?->name}",
                $supervisorId
            );

            DB::commit();

            return redirect()->route('manufacturing.shifts-workers.show', $shift->id)
                ->with('success', 'تم نقل الوردية بنجاح');

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
                ->withInput()
                ->with('error', 'حدث خطأ أثناء نقل الوردية: ' . $e->getMessage());
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
}

