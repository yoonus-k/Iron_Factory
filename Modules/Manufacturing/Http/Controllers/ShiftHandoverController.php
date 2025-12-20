<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\ShiftHandover;
use App\Models\ShiftAssignment;
use App\Models\User;
use App\Models\Worker;
use App\Services\ShiftHandoverService;
use App\Notifications\PendingWorkHandoverNotification;
use App\Traits\StoresNotifications;
use App\Checks\ShiftHandoverPermissionsCheck;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShiftHandoverController extends Controller
{
    use StoresNotifications;

    protected $handoverService;

    public function __construct(ShiftHandoverService $handoverService)
    {
        $this->handoverService = $handoverService;
    }

    public function index(Request $request)
    {
        $query = ShiftHandover::with(['fromUser', 'toUser', 'approver'])
            ->orderBy('handover_time', 'desc');

        if ($request->filled('approval_status')) {
            if ($request->approval_status === 'approved') {
                $query->where('supervisor_approved', true);
            } elseif ($request->approval_status === 'pending') {
                $query->where('supervisor_approved', false);
            }
        }

        if ($request->filled('stage_number')) {
            $query->where('stage_number', $request->stage_number);
        }

        if ($request->filled('date')) {
            $query->whereDate('handover_time', $request->date);
        }

        $handovers = $query->paginate(20);

        $stats = [
            'total' => ShiftHandover::count(),
            'approved' => ShiftHandover::where('supervisor_approved', true)->count(),
            'pending' => ShiftHandover::where('supervisor_approved', false)->count(),
        ];

        return view('manufacturing::shift-handovers.index', compact('handovers', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('is_active', true)
            ->orderBy('name')
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

        return view('manufacturing::shift-handovers.create', compact('users', 'teams'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'notes' => 'nullable|string|max:1000',
        ];

        // إذا كان النقل من الـ index (من الـ shift_id)
        if ($request->filled('shift_id')) {
            $rules['shift_id'] = 'required|exists:shift_assignments,id';
            $rules['stage_number'] = 'required|integer|between:1,4';

            // إما team_id أو to_user_id
            if (!$request->filled('team_id') && !$request->filled('to_user_id')) {
                return redirect()->back()
                    ->with('error', 'يرجى اختيار مجموعة أو عامل للنقل إليه');
            }

            if ($request->filled('team_id')) {
                $rules['team_id'] = 'required|exists:worker_teams,id';
                $rules['team_worker_ids'] = 'required|json';
            } else {
                $rules['to_user_id'] = 'required|exists:users,id';
            }
        } else {
            // النقل من صفحة create (الطريقة العادية)
            $rules['from_user_id'] = 'required|exists:users,id';
            $rules['stage_number'] = 'required|integer|between:1,4';
            $rules['handover_items'] = 'nullable|array';
            $rules['to_user_id'] = 'required|exists:users,id|different:from_user_id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            if ($request->filled('shift_id')) {
                // النقل من الـ index
                $shift = ShiftAssignment::findOrFail($request->shift_id);
                $fromUser = $shift->user;
                $stageNumber = $request->stage_number;
                $activeShift = $shift;

                // إذا كان نقل مجموعة
                if ($request->filled('team_id')) {
                    $team = \App\Models\WorkerTeam::findOrFail($request->team_id);
                    $workerIds = json_decode($request->team_worker_ids, true) ?? [];

                    // نقل الوردية لكل عامل في المجموعة
                    foreach ($workerIds as $workerId) {
                        $toUser = User::findOrFail($workerId);

                        ShiftHandover::create([
                            'from_user_id' => $fromUser->id,
                            'to_user_id' => $toUser->id,
                            'stage_number' => $stageNumber,
                            'handover_items' => [],
                            'notes' => $request->notes . " - نقل من المجموعة: " . $team->name,
                            'handover_time' => now(),
                            'supervisor_approved' => false,
                        ]);

                        $activeShift->update(['user_id' => $toUser->id]);
                    }

                    $message = 'تم نقل الوردية لجميع أعضاء المجموعة بنجاح';
                } else {
                    // نقل فردي
                    $toUser = User::findOrFail($request->to_user_id);

                    $handover = ShiftHandover::create([
                        'from_user_id' => $fromUser->id,
                        'to_user_id' => $toUser->id,
                        'stage_number' => $stageNumber,
                        'handover_items' => [],
                        'notes' => $request->notes,
                        'handover_time' => now(),
                        'supervisor_approved' => false,
                    ]);

                    $activeShift->update(['user_id' => $request->to_user_id]);

                    $message = 'تم نقل الوردية بنجاح';
                }
            } else {
                // النقل من صفحة create
                $fromUser = User::findOrFail($request->from_user_id);
                $toUser = User::findOrFail($request->to_user_id);
                $stageNumber = $request->stage_number;

                $activeShift = ShiftAssignment::where('user_id', $request->from_user_id)
                    ->where('stage_number', $stageNumber)
                    ->where('status', ShiftAssignment::STATUS_ACTIVE)
                    ->first();

                if (!$activeShift) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'لا توجد وردية نشطة للعامل الأول في هذه المرحلة');
                }

                $handover = ShiftHandover::create([
                    'from_user_id' => $fromUser->id,
                    'to_user_id' => $toUser->id,
                    'stage_number' => $stageNumber,
                    'handover_items' => $request->handover_items ?? [],
                    'notes' => $request->notes,
                    'handover_time' => now(),
                    'supervisor_approved' => false,
                ]);

                $activeShift->update(['user_id' => $request->to_user_id]);

                $message = 'تم نقل الوردية بنجاح';
            }

            $this->storeNotification(
                'shift_handover_sent',
                'نقل وردية',
                'نقل وردية من ' . $fromUser->name . ' الى ' . $toUser->name ?? 'المجموعة',
                'info',
                'fas fa-exchange-alt',
                route('manufacturing.shift-handovers.index')
            );

            DB::commit();

            $redirectTo = $request->filled('shift_id')
                ? route('manufacturing.shifts-workers.index')
                : route('manufacturing.shift-handovers.index');

            return redirect($redirectTo)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ShiftHandover $shiftHandover)
    {
        $handover = ShiftHandover::with(['fromUser', 'toUser', 'approver'])->findOrFail($shiftHandover->id);
        return view('manufacturing::shift-handovers.show', compact('handover'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShiftHandover $shiftHandover)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShiftHandover $shiftHandover)
    {
        //
    }

    /**
     * Approve a shift handover (by supervisor).
     */
    public function approve(Request $request, $id)
    {
        try {
            $handover = ShiftHandover::findOrFail($id);

            if ($handover->supervisor_approved) {
                return redirect()->back()
                    ->with('warning', 'تم الموافقة على هذا النقل بالفعل');
            }

            $handover->update([
                'supervisor_approved' => true,
                'approved_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            $this->storeNotification(
                'shift_handover_approved',
                'موافقة على نقل وردية',
                'موافقة على نقل وردية من ' . $handover->fromUser->name . ' الى ' . $handover->toUser->name,
                'success',
                'fas fa-check-circle',
                route('manufacturing.shift-handovers.show', $handover->id)
            );

            return redirect()->back()
                ->with('success', 'تمت الموافقة على النقل بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Reject a shift handover.
     */
    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)
                ->with('error', 'يرجى إدخال سبب الرفض');
        }

        try {
            $handover = ShiftHandover::findOrFail($id);

            if ($handover->supervisor_approved) {
                return redirect()->back()
                    ->with('error', 'لا يمكن رفض نقل تمت الموافقة عليه');
            }

            $activeShift = ShiftAssignment::where('user_id', $handover->to_user_id)
                ->where('stage_number', $handover->stage_number)
                ->where('status', ShiftAssignment::STATUS_ACTIVE)
                ->first();

            if ($activeShift) {
                $activeShift->update(['user_id' => $handover->from_user_id]);
            }

            $handover->delete();

            $this->storeNotification(
                'shift_handover_rejected',
                'رفض نقل وردية',
                'رفض نقل وردية من ' . $handover->fromUser->name,
                'danger',
                'fas fa-times-circle',
                route('manufacturing.shift-handovers.index')
            );

            return redirect()->back()
                ->with('success', 'تم رفض النقل وإعادة الوردية للعامل الأصلي');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShiftHandover $shiftHandover)
    {
        //
    }

    /**
     * عرض صفحة إنهاء الشفت مع الأشغال المعلقة
     */
    public function endShift(Request $request)
    {
        $userId = Auth::id();
        $stageNumber = $request->input('stage_number');

        if (!$stageNumber) {
            return redirect()->back()
                ->with('error', __('shifts-workers.stage_number') . ' ' . __('shifts-workers.required'));
        }

        // جمع الأشغال المعلقة تلقائياً
        $pendingItems = $this->handoverService->collectPendingWork($userId, $stageNumber);

        // البحث عن الشفت الحالي
        $currentShift = ShiftAssignment::where('user_id', $userId)
            ->where('stage_number', $stageNumber)
            ->where('status', ShiftAssignment::STATUS_ACTIVE)
            ->first();

        if (!$currentShift) {
            return redirect()->back()
                ->with('error', __('shifts-workers.no_active_shift'));
        }

        // البحث عن عامل الشفت التالي
        $nextShiftWorker = $this->handoverService->findNextShiftWorker(
            $userId,
            $stageNumber,
            $currentShift->shift_type
        );

        return view('manufacturing::shift-handovers.end-shift', compact(
            'pendingItems',
            'currentShift',
            'nextShiftWorker',
            'stageNumber'
        ));
    }

    /**
     * حفظ تسليم الوردية مع الأشغال المعلقة
     */
    public function storeEndShift(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to_user_id' => 'required|exists:users,id',
            'stage_number' => 'required|integer|between:1,4',
            'pending_items' => 'required|json',
            'notes' => 'nullable|string|max:1000',
            'notes_en' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()
                ->with('error', __('shifts-workers.validation_error'));
        }

        try {
            DB::beginTransaction();

            $userId = Auth::id();
            $toUserId = $request->to_user_id;
            $stageNumber = $request->stage_number;
            $pendingItems = json_decode($request->pending_items, true);

            // البحث عن الشفت الحالي
            $currentShift = ShiftAssignment::where('user_id', $userId)
                ->where('stage_number', $stageNumber)
                ->where('status', ShiftAssignment::STATUS_ACTIVE)
                ->first();

            if (!$currentShift) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', __('shifts-workers.no_active_shift'));
            }

            // إنشاء سجل تسليم الوردية
            $handover = ShiftHandover::create([
                'from_user_id' => $userId,
                'to_user_id' => $toUserId,
                'stage_number' => $stageNumber,
                'shift_assignment_id' => $currentShift->id,
                'handover_items' => $pendingItems,
                'auto_collected' => true,
                'pending_items_count' => count($pendingItems),
                'notes' => $request->notes,
                'notes_en' => $request->notes_en,
                'handover_time' => now(),
                'supervisor_approved' => false,
            ]);

            // تحويل الأشغال المعلقة للعامل الجديد
            $transferred = $this->handoverService->transferPendingWork(
                $pendingItems,
                $toUserId,
                $stageNumber
            );

            if (!$transferred) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', __('shifts-workers.transfer_failed'));
            }

            // إرسال إشعار للعامل الجديد
            $toUser = User::find($toUserId);
            if ($toUser) {
                $toUser->notify(new PendingWorkHandoverNotification($handover, count($pendingItems)));
            }

            // تسجيل الإشعارات
            $fromUser = Auth::user();
            $this->storeNotification(
                'shift_handover_with_pending_work',
                __('shifts-workers.shift_handover'),
                __('shifts-workers.handover_created_successfully') . ' - ' . count($pendingItems) . ' ' . __('shifts-workers.pending_items_count'),
                'info',
                'fas fa-exchange-alt',
                route('manufacturing.shift-handovers.show', $handover->id)
            );

            DB::commit();

            return redirect()->route('manufacturing.shift-handovers.show', $handover->id)
                ->with('success', __('shifts-workers.handover_created_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', __('shifts-workers.error') . ': ' . $e->getMessage());
        }
    }

    /**
     * استلام الوردية من قبل العامل الجديد
     */
    public function acknowledge($id)
    {
        try {
            $handover = ShiftHandover::findOrFail($id);

            // التحقق من أن المستخدم هو المستلم
            if ($handover->to_user_id != Auth::id()) {
                return redirect()->back()
                    ->with('error', __('shifts-workers.unauthorized'));
            }

            // التحقق من عدم الاستلام مسبقاً
            if ($handover->isAcknowledged()) {
                return redirect()->back()
                    ->with('warning', __('shifts-workers.already_acknowledged'));
            }

            // تسجيل الاستلام
            $handover->acknowledge(Auth::id());

            // إشعار
            $this->storeNotification(
                'shift_handover_acknowledged',
                __('shifts-workers.acknowledge_handover'),
                __('shifts-workers.handover_acknowledged_successfully'),
                'success',
                'fas fa-check-circle',
                route('manufacturing.shift-handovers.show', $handover->id)
            );

            return redirect()->route('manufacturing.shift-handovers.show', $handover->id)
                ->with('success', __('shifts-workers.handover_acknowledged_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('shifts-workers.error') . ': ' . $e->getMessage());
        }
    }

    /**
     * عرض الأشغال المعلقة للمستخدم الحالي
     */
    public function myPendingWork()
    {
        $userId = Auth::id();

        // الحصول على إحصائيات الأشغال المعلقة
        $stats = $this->handoverService->getPendingWorkStats($userId);

        // الحصول على التسليمات غير المستلمة
        $pendingHandovers = ShiftHandover::where('to_user_id', $userId)
            ->whereNull('acknowledged_at')
            ->with(['fromUser', 'shiftAssignment'])
            ->orderBy('handover_time', 'desc')
            ->get();

        return view('manufacturing::shift-handovers.my-pending-work', compact('stats', 'pendingHandovers'));
    }

    /**
     * جمع الأشغال المعلقة (AJAX)
     */
    public function collectPendingWork(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stage_number' => 'required|integer|between:1,4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('shifts-workers.validation_error'),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = Auth::id();
            $stageNumber = $request->stage_number;

            $pendingItems = $this->handoverService->collectPendingWork($userId, $stageNumber);

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $pendingItems,
                    'count' => count($pendingItems),
                ],
                'message' => __('shifts-workers.pending_work_collected')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('shifts-workers.error') . ': ' . $e->getMessage()
            ], 500);
        }
    }
}
