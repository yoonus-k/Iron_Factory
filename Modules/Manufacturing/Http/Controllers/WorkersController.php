<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\Worker;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WorkersController extends Controller
{
    /**
     * Display a listing of workers.
     */
    public function index(Request $request)
    {
        $query = Worker::query();

        // Filter by position
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('worker_code', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $workers = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total' => Worker::count(),
            'active' => Worker::where('is_active', true)->count(),
            'supervisors' => Worker::where('position', Worker::POSITION_SUPERVISOR)->where('is_active', true)->count(),
            'workers' => Worker::where('position', Worker::POSITION_WORKER)->where('is_active', true)->count(),
        ];

        return view('manufacturing::workers.index', compact('workers', 'stats'));
    }

    /**
     * Show the form for creating a new worker.
     */
    public function create()
    {
        // Get available users (those who don't have worker profiles yet)
        $availableUsers = User::whereDoesntHave('worker')->where('is_active', true)->get();

        return view('manufacturing::workers.create', compact('availableUsers'));
    }

    /**
     * Store a newly created worker.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'worker_code' => 'required|string|max:50|unique:workers,worker_code',
            'name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:20|unique:workers,national_id',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:workers,email',
            'position' => 'required|in:worker,supervisor,technician,quality_inspector',
            'allowed_stages' => 'nullable|array',
            'allowed_stages.*' => 'integer|between:1,4',
            'hourly_rate' => 'required|numeric|min:0',
            'shift_preference' => 'required|in:morning,evening,night,any',
            'hire_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'user_id' => 'nullable|exists:users,id',
            // New user creation fields
            'new_username' => 'required_if:allow_system_access,new|nullable|string|max:255|unique:users,username',
            'new_email' => 'required_if:allow_system_access,new|nullable|email|max:255|unique:users,email',
            'new_password' => 'required_if:allow_system_access,new|nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            $userId = $request->user_id;

            // Create new user if requested
            if ($request->input('allow_system_access') === 'new' && $request->new_username) {
                $newUser = User::create([
                    'name' => $request->name,  // Worker's full name
                    'username' => $request->new_username,  // Username for login
                    'email' => $request->new_email,
                    'password' => bcrypt($request->new_password),
                    'is_active' => true,
                ]);
                $userId = $newUser->id;
            }

            $worker = Worker::create([
                'worker_code' => $request->worker_code,
                'name' => $request->name,
                'national_id' => $request->national_id,
                'phone' => $request->phone,
                'email' => $request->email,
                'position' => $request->position,
                'allowed_stages' => $request->allowed_stages,
                'hourly_rate' => $request->hourly_rate,
                'shift_preference' => $request->shift_preference,
                'is_active' => true,
                'hire_date' => $request->hire_date,
                'notes' => $request->notes,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'user_id' => $userId,
            ]);

            DB::commit();

            $message = 'تم إضافة العامل بنجاح';
            if ($userId && $request->input('allow_system_access') === 'new') {
                $message .= ' وتم إنشاء حساب مستخدم جديد';
            }

            return redirect()->route('manufacturing.workers.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة العامل: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified worker.
     */
    public function show($id)
    {
        $worker = Worker::with(['user', 'shiftAssignments'])->findOrFail($id);

        // Get recent shift assignments
        $recentShifts = $worker->shiftAssignments()
            ->with(['supervisor'])
            ->orderBy('shift_date', 'desc')
            ->limit(10)
            ->get();

        return view('manufacturing::workers.show', compact('worker', 'recentShifts'));
    }

    /**
     * Show the form for editing the specified worker.
     */
    public function edit($id)
    {
        $worker = Worker::findOrFail($id);
        
        // Get available users
        $availableUsers = User::where(function($q) use ($worker) {
            $q->whereDoesntHave('worker')
              ->orWhere('id', $worker->user_id);
        })->where('is_active', true)->get();

        return view('manufacturing::workers.edit', compact('worker', 'availableUsers'));
    }

    /**
     * Update the specified worker.
     */
    public function update(Request $request, $id)
    {
        $worker = Worker::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'worker_code' => 'required|string|max:50|unique:workers,worker_code,' . $id,
            'name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:20|unique:workers,national_id,' . $id,
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:workers,email,' . $id,
            'position' => 'required|in:worker,supervisor,technician,quality_inspector',
            'allowed_stages' => 'nullable|array',
            'allowed_stages.*' => 'integer|between:1,4',
            'hourly_rate' => 'required|numeric|min:0',
            'shift_preference' => 'required|in:morning,evening,night,any',
            'hire_date' => 'required|date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            $worker->update([
                'worker_code' => $request->worker_code,
                'name' => $request->name,
                'national_id' => $request->national_id,
                'phone' => $request->phone,
                'email' => $request->email,
                'position' => $request->position,
                'allowed_stages' => $request->allowed_stages,
                'hourly_rate' => $request->hourly_rate,
                'shift_preference' => $request->shift_preference,
                'is_active' => $request->input('is_active', true),
                'hire_date' => $request->hire_date,
                'notes' => $request->notes,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'user_id' => $request->user_id,
            ]);

            DB::commit();

            return redirect()->route('manufacturing.workers.index')
                ->with('success', 'تم تحديث بيانات العامل بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified worker.
     */
    public function destroy($id)
    {
        try {
            $worker = Worker::findOrFail($id);
            
            // Check if worker has active shift assignments
            $activeShifts = $worker->shiftAssignments()
                ->where('status', 'active')
                ->count();

            if ($activeShifts > 0) {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف عامل لديه ورديات نشطة');
            }

            $worker->delete();

            return redirect()->route('manufacturing.workers.index')
                ->with('success', 'تم حذف العامل بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف العامل: ' . $e->getMessage());
        }
    }

    /**
     * Toggle worker active status.
     */
    public function toggleStatus($id)
    {
        try {
            $worker = Worker::findOrFail($id);
            
            $worker->update([
                'is_active' => !$worker->is_active,
                'termination_date' => !$worker->is_active ? now() : null
            ]);

            $status = $worker->is_active ? 'تم تفعيل' : 'تم تعطيل';

            return redirect()->back()
                ->with('success', "{$status} العامل بنجاح");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Generate worker code automatically.
     */
    public function generateWorkerCode(Request $request)
    {
        $position = $request->input('position', 'worker');
        
        $positionPrefix = match($position) {
            'supervisor' => 'SUP',
            'technician' => 'TEC',
            'quality_inspector' => 'QI',
            default => 'WRK',
        };
        
        $year = date('Y');
        $count = Worker::whereYear('created_at', $year)
            ->where('position', $position)
            ->count() + 1;
        
        $code = "{$positionPrefix}-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
        
        return response()->json(['worker_code' => $code]);
    }

    /**
     * Get workers available for a specific shift.
     */
    public function getAvailableWorkers(Request $request)
    {
        $shiftDate = $request->input('shift_date');
        $shiftType = $request->input('shift_type');
        $stageNumber = $request->input('stage_number');

        $query = Worker::active();

        // Filter by stage if provided
        if ($stageNumber) {
            $query->where(function($q) use ($stageNumber) {
                $q->whereJsonContains('allowed_stages', $stageNumber)
                  ->orWhereNull('allowed_stages');
            });
        }

        // Filter by shift preference
        if ($shiftType) {
            $query->where(function($q) use ($shiftType) {
                $q->where('shift_preference', $shiftType)
                  ->orWhere('shift_preference', 'any');
            });
        }

        $workers = $query->get();

        return response()->json($workers);
    }
}
