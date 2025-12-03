<?php

namespace App\Http\Controllers;

use App\Models\StageSuspension;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StageSuspensionController extends Controller
{
    /**
     * Display a listing of suspended stages
     */
    public function index(Request $request)
    {
        $query = StageSuspension::with(['suspendedBy', 'reviewedBy'])
            ->orderBy('suspended_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by stage
        if ($request->filled('stage')) {
            $query->where('stage_number', $request->stage);
        }

        $suspensions = $query->paginate(20);
        
        // Statistics
        $stats = [
            'total' => StageSuspension::count(),
            'pending' => StageSuspension::where('status', 'suspended')->count(),
            'approved' => StageSuspension::where('status', 'approved')->count(),
            'rejected' => StageSuspension::where('status', 'rejected')->count(),
        ];

        return view('stage-suspensions.index', compact('suspensions', 'stats'));
    }

    /**
     * Show the details of a suspension
     */
    public function show($id)
    {
        $suspension = StageSuspension::with(['suspendedBy', 'reviewedBy'])->findOrFail($id);
        
        return view('stage-suspensions.show', compact('suspension'));
    }

    /**
     * Approve a suspension and resume the stage
     */
    public function approve(Request $request, $id)
    {
        $suspension = StageSuspension::findOrFail($id);

        if ($suspension->status !== 'suspended') {
            return back()->with('error', 'هذا الإيقاف تمت مراجعته بالفعل');
        }

        $request->validate([
            'review_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $suspension->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => $request->review_notes,
            ]);

            // Send notification to the worker that stage is resumed
            $this->sendResumeNotification($suspension, 'approved');

            DB::commit();

            return redirect()->route('stage-suspensions.index')
                ->with('success', 'تمت الموافقة على استئناف المرحلة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Reject a suspension
     */
    public function reject(Request $request, $id)
    {
        $suspension = StageSuspension::findOrFail($id);

        if ($suspension->status !== 'suspended') {
            return back()->with('error', 'هذا الإيقاف تمت مراجعته بالفعل');
        }

        $request->validate([
            'review_notes' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $suspension->update([
                'status' => 'rejected',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => $request->review_notes,
            ]);

            // Send notification
            $this->sendResumeNotification($suspension, 'rejected');

            DB::commit();

            return redirect()->route('stage-suspensions.index')
                ->with('success', 'تم رفض الطلب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Send notification about stage resume
     */
    private function sendResumeNotification(StageSuspension $suspension, string $action)
    {
        $notificationService = app(NotificationService::class);

        $users = User::whereHas('roleRelation', function($q) {
            $q->whereHas('permissions', function($q2) {
                $q2->where('name', 'STAGE_SUSPENSION_APPROVE');
            });
        })->get();

        foreach ($users as $user) {
            try {
                $notificationService->notifyStageSuspensionReviewed($user, $suspension, $action, Auth::user());
            } catch (\Exception $e) {
                \Log::error('Error sending stage suspension review notification to user ' . $user->id . ': ' . $e->getMessage());
            }
        }
    }
}
