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

    /**
     * عرض صفحة نقل المرحلة (ستاند) من وردية لأخرى
     */
    public function transferStageView(Request $request)
    {
        $stageNumber = $request->input('stage_number');
        $stageRecordId = $request->input('stage_record_id');

        if (!$stageNumber || !$stageRecordId) {
            return redirect()->back()
                ->with('error', 'يجب تحديد المرحلة والمعرف');
        }

        // جلب الوردية الحالية
        $currentShift = ShiftAssignment::where('stage_number', $stageNumber)
            ->where('stage_record_id', $stageRecordId)
            ->first();

        if (!$currentShift) {
            return redirect()->back()
                ->with('error', 'لم يتم العثور على الوردية المرتبطة بهذه المرحلة');
        }

        // جلب العمال للوردية الحالية
        $currentShiftWorkers = [];
        if ($currentShift->worker_ids && count($currentShift->worker_ids) > 0) {
            $currentShiftWorkers = \App\Models\Worker::whereIn('id', $currentShift->worker_ids)->get();
        }

        // جلب الورديات المتاحة للنقل إليها - جميع الورديات النشطة ما عدا الوردية الحالية
        $availableShifts = ShiftAssignment::where('status', ShiftAssignment::STATUS_ACTIVE)
            ->where('id', '!=', $currentShift->id)
            ->with('user', 'supervisor')
            ->orderBy('shift_date', 'desc')
            ->get();

        // جلب العمال لكل وردية متاحة
        $shiftsWithWorkers = [];
        foreach ($availableShifts as $shift) {
            $workers = [];
            if ($shift->worker_ids && count($shift->worker_ids) > 0) {
                $workers = \App\Models\Worker::whereIn('id', $shift->worker_ids)->get();
            }
            $shiftsWithWorkers[] = [
                'shift' => $shift,
                'workers' => $workers
            ];
        }

        return view('manufacturing::shift-handovers.transfer-stage', compact(
            'currentShift',
            'currentShiftWorkers',
            'availableShifts',
            'shiftsWithWorkers',
            'stageNumber',
            'stageRecordId'
        ));
    }

    /**
     * تنفيذ نقل المرحلة (الستاند) من وردية لأخرى
     */
    public function transferStageStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_shift_id' => 'required|exists:shift_assignments,id',
            'to_shift_id' => 'required|exists:shift_assignments,id|different:from_shift_id',
            'stage_number' => 'required|integer|between:1,4',
            'stage_record_id' => 'required|integer',
            'stage_record_barcode' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
            'confirm_transfer' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            // إذا كان JSON request، نرد JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput()
                ->with('error', 'يجب عليك الموافقة على نقل المرحلة أولاً');
        }

        try {
            DB::beginTransaction();

            $fromShift = ShiftAssignment::findOrFail($request->from_shift_id);
            $toShift = ShiftAssignment::findOrFail($request->to_shift_id);
            $stageNumber = $request->stage_number;
            $stageRecordId = $request->stage_record_id;

            // التحقق من أن الوردية الحالية تمتلك هذه المرحلة
            if ($fromShift->stage_record_id != $stageRecordId) {
                DB::rollBack();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'المرحلة المحددة لا تنتمي للوردية الحالية'
                    ], 400);
                }

                return redirect()->back()
                    ->with('error', 'المرحلة المحددة لا تنتمي للوردية الحالية');
            }

            // تحديث الوردية الجديدة بمعلومات المرحلة
            $toShift->update([
                'stage_number' => $stageNumber,
                'stage_record_id' => $stageRecordId,
                'stage_record_barcode' => $request->stage_record_barcode ?? $fromShift->stage_record_barcode,
            ]);

            // تحديث حالة الوردية القديمة - ليس حذف المرحلة بل تحديث الحالة
            // لأن stage_number لا يقبل null في قاعدة البيانات
            $fromShift->update([
                'status' => ShiftAssignment::STATUS_COMPLETED,
            ]);

            // إنشاء سجل في جدول shift_handovers لتتبع النقل
            ShiftHandover::create([
                'from_user_id' => $fromShift->user_id,
                'to_user_id' => $toShift->user_id,
                'from_shift_id' => $fromShift->id,
                'to_shift_id' => $toShift->id,
                'stage_number' => $stageNumber,
                'shift_assignment_id' => $toShift->id,
                'handover_items' => [
                    'stage_record_id' => $stageRecordId,
                    'stage_record_barcode' => $request->stage_record_barcode ?? $fromShift->stage_record_barcode,
                    'transfer_type' => 'stage_transfer'
                ],
                'notes' => 'نقل المرحلة #' . $stageNumber . ' من الستاند رقم: ' . $stageRecordId . '. ' . ($request->notes ?? ''),
                'handover_time' => now(),
                'supervisor_approved' => false,
            ]);

            // تسجيل تغييرات العمال المرتبطين بالمرحلة
            DB::table('worker_stage_history')
                ->where('stage_type', 'stage' . $stageNumber . '_stands')
                ->where('stage_record_id', $stageRecordId)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'ended_at' => now(),
                    'status_after' => 'transferred',
                ]);

            DB::commit();

            $message = 'تم نقل المرحلة بنجاح من وردية ' . $fromShift->user->name . ' إلى وردية ' . $toShift->user->name;

            // إذا كان JSON request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'handover_id' => ShiftHandover::latest('id')->first()->id ?? null
                    ]
                ]);
            }

            return redirect()->route('manufacturing.shift-handovers.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * عرض صفحة نقل العمال من وردية لأخرى
     */
    public function transferWorkersView(Request $request)
    {
        $fromShiftId = $request->input('from_shift_id');

        if (!$fromShiftId) {
            return redirect()->back()
                ->with('error', 'يجب تحديد الوردية المراد النقل منها');
        }

        $fromShift = ShiftAssignment::with('user', 'supervisor')
            ->findOrFail($fromShiftId);

        // جلب العمال النشطين في الوردية من جدول worker_stage_history مع تفاصيل العامل
        $activeWorkers = DB::table('worker_stage_history as wsh')
            ->leftJoin('users as workers', 'wsh.worker_id', '=', 'workers.id')
            ->leftJoin('worker_teams', 'wsh.worker_team_id', '=', 'worker_teams.id')
            ->where('wsh.shift_assignment_id', $fromShiftId)
            ->where('wsh.is_active', true)
            ->select(
                'wsh.id',
                'wsh.worker_id',
                'wsh.worker_team_id',
                'wsh.worker_type',
                'wsh.started_at',
                'wsh.is_active',
                'workers.name as worker_name',
                'workers.email',
                'worker_teams.name as team_name'
            )
            ->orderBy('wsh.started_at', 'desc')
            ->get();

        // جلب جميع الورديات النشطة
        $availableShifts = ShiftAssignment::where('status', ShiftAssignment::STATUS_ACTIVE)
            ->where('id', '!=', $fromShiftId)
            ->with('user', 'supervisor')
            ->orderBy('shift_date', 'desc')
            ->get();

        // جلب جميع العمال
        $allWorkers = \App\Models\Worker::where('is_active', true)
            ->get();

        // جلب جميع فرق العمال
        $allTeams = \App\Models\WorkerTeam::where('is_active', true)
            ->get();

        return view('manufacturing::shift-handovers.transfer-workers', compact(
            'fromShift',
            'activeWorkers',
            'availableShifts',
            'allWorkers',
            'allTeams'
        ));
    }

    /**
     * تنفيذ نقل العمال من وردية لأخرى
     */
    public function transferWorkersStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_shift_id' => 'required|exists:shift_assignments,id',
            'to_shift_id' => 'required|exists:shift_assignments,id|different:from_shift_id',
            'worker_ids' => 'required|array|min:1',
            'worker_ids.*' => 'required|integer',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()
                ->with('error', 'بيانات غير صحيحة');
        }

        try {
            DB::beginTransaction();

            $fromShift = ShiftAssignment::findOrFail($request->from_shift_id);
            $toShift = ShiftAssignment::findOrFail($request->to_shift_id);
            $workerIds = $request->worker_ids;

            $transferredWorkers = [];

            foreach ($workerIds as $workerId) {
                // جلب سجل العامل النشط
                $workerHistory = DB::table('worker_stage_history')
                    ->where('shift_assignment_id', $fromShift->id)
                    ->where('worker_id', $workerId)
                    ->where('is_active', true)
                    ->first();

                if (!$workerHistory) {
                    continue;
                }

                // إنهاء السجل القديم
                DB::table('worker_stage_history')
                    ->where('id', $workerHistory->id)
                    ->update([
                        'is_active' => false,
                        'ended_at' => now(),
                        'status_after' => 'transferred_to_shift',
                    ]);

                // إنشاء سجل جديد للعامل في الوردية الجديدة
                DB::table('worker_stage_history')->insert([
                    'stage_type' => $workerHistory->stage_type,
                    'stage_record_id' => $workerHistory->stage_record_id,
                    'barcode' => $workerHistory->barcode,
                    'worker_id' => $workerId,
                    'worker_team_id' => $workerHistory->worker_team_id,
                    'worker_type' => $workerHistory->worker_type,
                    'started_at' => now(),
                    'status_before' => $workerHistory->status_after ?? 'active',
                    'is_active' => true,
                    'shift_assignment_id' => $toShift->id,
                    'assigned_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $transferredWorkers[] = [
                    'worker_id' => $workerId,
                    'from_shift' => $fromShift->id,
                    'to_shift' => $toShift->id,
                ];
            }

            if (empty($transferredWorkers)) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'لم يتم العثور على عمال نشطين للنقل');
            }

            // إنشاء سجل في جدول shift_handovers لتتبع النقل
            ShiftHandover::create([
                'from_user_id' => $fromShift->user_id,
                'to_user_id' => $toShift->user_id,
                'from_shift_id' => $fromShift->id,
                'to_shift_id' => $toShift->id,
                'stage_number' => $fromShift->stage_number,
                'shift_assignment_id' => $toShift->id,
                'handover_items' => [
                    'transferred_workers_count' => count($transferredWorkers),
                    'workers' => $transferredWorkers,
                    'transfer_type' => 'workers_transfer'
                ],
                'notes' => 'نقل ' . count($transferredWorkers) . ' عامل من الوردية. ' . ($request->notes ?? ''),
                'handover_time' => now(),
                'supervisor_approved' => false,
            ]);

            DB::commit();

            return redirect()->route('manufacturing.shift-handovers.index')
                ->with('success', 'تم نقل ' . count($transferredWorkers) . ' عامل من وردية ' . $fromShift->user->name . ' إلى وردية ' . $toShift->user->name);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * الحصول على الورديات المتاحة (API)
     */

            public function getAvailableShifts(Request $request)
    {
        $stageNumber = $request->input('stage_number');
        $excludeShiftId = $request->input('exclude_shift_id');

        try {
            $query = ShiftAssignment::where('status', ShiftAssignment::STATUS_ACTIVE);

            // إذا كان هناك stage_number، استخدمه كـ filter
            if ($stageNumber) {
                $query->where('stage_number', $stageNumber);
            }

            // استبعد الوردية الحالية إذا تم توفيرها
            if ($excludeShiftId) {
                $query->where('id', '!=', $excludeShiftId);
            }

            $shifts = $query->with('user', 'supervisor')
                ->orderBy('shift_date', 'desc')
                ->get()
                ->map(function($shift) {
                    return [
                        'id' => $shift->id,
                        'shift_code' => $shift->shift_code,
                        'worker_name' => $shift->user->name,
                        'supervisor_name' => $shift->supervisor?->name,
                        'shift_type' => $shift->shift_type,
                        'start_time' => $shift->start_time,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $shifts,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على العمال النشطين في وردية (API)
     */
    public function getActiveWorkers(Request $request)
    {
        $shiftId = $request->input('shift_id');

        if (!$shiftId) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تحديد الوردية'
            ], 422);
        }

        try {
            $shift = ShiftAssignment::findOrFail($shiftId);

            $activeWorkers = DB::table('worker_stage_history')
                ->where('shift_assignment_id', $shiftId)
                ->where('is_active', true)
                ->with('worker')
                ->get()
                ->map(function($worker) {
                    return [
                        'id' => $worker->id,
                        'worker_id' => $worker->worker_id,
                        'worker_name' => $worker->worker?->name ?? 'غير محدد',
                        'stage_type' => $worker->stage_type,
                        'started_at' => $worker->started_at,
                        'duration' => now()->diffInMinutes($worker->started_at),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'shift' => $shift,
                    'active_workers' => $activeWorkers,
                    'total_workers' => count($activeWorkers),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }
}
