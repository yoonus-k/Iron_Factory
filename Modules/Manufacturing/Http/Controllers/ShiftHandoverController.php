<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\ShiftHandover;
use App\Models\ShiftAssignment;
use App\Models\User;
use App\Models\Worker;
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

        return view('manufacturing::shift-handovers.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التحقق من الصلاحية
        if (!ShiftHandoverPermissionsCheck::canCreateHandover()) {
            abort(403, 'ليس لديك صلاحية لإنشاء نقل وردية');
        }

        $rules = [
            'to_user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ];

        // إذا كان النقل من الـ index (من الـ shift_id)
        if ($request->filled('shift_id')) {
            $rules['shift_id'] = 'required|exists:shift_assignments,id';
        } else {
            // النقل من صفحة create (الطريقة العادية)
            $rules['from_user_id'] = 'required|exists:users,id|different:to_user_id';
            $rules['stage_number'] = 'required|integer|between:1,4';
            $rules['handover_items'] = 'nullable|array';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            $toUser = User::findOrFail($request->to_user_id);

            if ($request->filled('shift_id')) {
                // النقل من الـ index
                $shift = ShiftAssignment::findOrFail($request->shift_id);
                $fromUser = $shift->user;
                $stageNumber = $shift->stage_number;
                $activeShift = $shift;
            } else {
                // النقل من صفحة create
                $fromUser = User::findOrFail($request->from_user_id);
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
            }

            $handover = ShiftHandover::create([
                'from_user_id' => $fromUser->id,
                'to_user_id' => $request->to_user_id,
                'stage_number' => $stageNumber,
                'handover_items' => $request->handover_items ?? [],
                'notes' => $request->notes,
                'handover_time' => now(),
                'supervisor_approved' => false,
            ]);

            $activeShift->update(['user_id' => $request->to_user_id]);

            $this->storeNotification(
                'shift_handover_sent',
                'نقل وردية',
                'نقل وردية من ' . $fromUser->name . ' الى ' . $toUser->name,
                'info',
                'fas fa-exchange-alt',
                route('manufacturing.shift-handovers.show', $handover->id)
            );

            $this->storeNotification(
                'shift_handover_received',
                'استقبال وردية',
                'استقبال وردية من ' . $fromUser->name,
                'success',
                'fas fa-inbox',
                route('manufacturing.shift-handovers.show', $handover->id)
            );

            DB::commit();

            $redirectTo = $request->filled('shift_id')
                ? route('manufacturing.shifts-workers.index')
                : route('manufacturing.shift-handovers.index');

            return redirect($redirectTo)
                ->with('success', 'تم نقل الوردية بنجاح');

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
        // التحقق من الصلاحية
        if (!ShiftHandoverPermissionsCheck::canApproveHandover()) {
            abort(403, 'ليس لديك صلاحية للموافقة على نقل الوردية');
        }

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
        // التحقق من الصلاحية
        if (!ShiftHandoverPermissionsCheck::canRejectHandover()) {
            abort(403, 'ليس لديك صلاحية لرفض نقل الوردية');
        }

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
}
