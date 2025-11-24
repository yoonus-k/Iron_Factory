<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductionConfirmation;
use App\Models\ProductionStage;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductionConfirmationController extends Controller
{
    /**
     * عرض الطلبات المعلقة للموظف الحالي
     */
    public function pendingConfirmations()
    {
        $userId = Auth::id();
        
        $pendingConfirmations = ProductionConfirmation::with([
            'deliveryNote.material',
            'deliveryNote.warehouse',
            'batch',
            'assignedUser'
        ])
        ->pending()
        ->forUser($userId)
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        return view('manufacturing::production.confirmations.pending', compact('pendingConfirmations'));
    }

    /**
     * عرض جميع التأكيدات (للمشرفين)
     */
    public function index(Request $request)
    {
        $query = ProductionConfirmation::with([
            'deliveryNote.material',
            'batch',
            'assignedUser',
            'confirmedByUser',
            'rejectedByUser'
        ]);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by stage
        if ($request->stage) {
            $query->where('stage_code', $request->stage);
        }

        // Filter by assigned user
        if ($request->worker) {
            $query->where('assigned_to', $request->worker);
        }

        // Filter by date range
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $confirmations = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get stages and users for filters
        $stages = ProductionStage::getActiveStages();
        $users = User::where('is_active', 1)->orderBy('name')->get();

        // Calculate statistics
        $stats = [
            'pending' => ProductionConfirmation::where('status', 'pending')->count(),
            'confirmed' => ProductionConfirmation::where('status', 'confirmed')->count(),
            'rejected' => ProductionConfirmation::where('status', 'rejected')->count(),
            'total' => ProductionConfirmation::count(),
        ];

        return view('manufacturing::production.confirmations.index', compact('confirmations', 'stages', 'users', 'stats'));
    }

    /**
     * عرض تفاصيل تأكيد معين
     */
    public function show($id)
    {
        $confirmation = ProductionConfirmation::with([
            'deliveryNote.material',
            'deliveryNote.warehouse',
            'batch',
            'assignedUser',
            'confirmedByUser',
            'rejectedByUser'
        ])->findOrFail($id);

        return view('manufacturing::production.confirmations.show', compact('confirmation'));
    }

    /**
     * تأكيد استلام الدفعة
     */
    public function confirm(Request $request, $id)
    {
        $validated = $request->validate([
            'actual_quantity' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $confirmation = ProductionConfirmation::findOrFail($id);

            // التحقق من أن المستخدم هو المكلف بالاستلام
            if ($confirmation->assigned_to != Auth::id()) {
                return response()->json(['success' => false, 'message' => 'ليس لديك صلاحية تأكيد هذا الطلب'], 403);
            }

            // التحقق من أن الحالة pending
            if ($confirmation->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'هذا الطلب تم معالجته بالفعل'], 400);
            }

            // تأكيد الاستلام
            $confirmation->confirm(
                Auth::id(),
                $validated['actual_quantity'] ?? null,
                $validated['notes'] ?? null
            );

            // إنشاء إشعار للمشرف
            $batchCode = $confirmation->batch?->batch_code ?? 'غير محدد';
            Notification::create([
                'user_id' => $confirmation->deliveryNote->transferred_by ?? 1,
                'type' => 'production_confirmed',
                'title' => 'تم تأكيد استلام الدفعة',
                'message' => "تم تأكيد استلام دفعة {$batchCode} من قبل " . Auth::user()->name,
                'action_url' => route('manufacturing.warehouse.registration.show', ['deliveryNote' => $confirmation->delivery_note_id]),
                'priority' => 'medium',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تأكيد استلام الدفعة بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirming production receipt: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تأكيد الاستلام: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * رفض استلام الدفعة
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ], [
            'rejection_reason.required' => 'يجب إدخال سبب الرفض',
        ]);

        try {
            DB::beginTransaction();

            $confirmation = ProductionConfirmation::findOrFail($id);

            // التحقق من أن المستخدم هو المكلف بالاستلام
            if ($confirmation->assigned_to != Auth::id()) {
                return response()->json(['success' => false, 'message' => 'ليس لديك صلاحية رفض هذا الطلب'], 403);
            }

            // التحقق من أن الحالة pending
            if ($confirmation->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'هذا الطلب تم معالجته بالفعل'], 400);
            }

            // رفض الاستلام
            $confirmation->reject(Auth::id(), $validated['rejection_reason']);

            // إنشاء إشعار للمشرف
            $batchCode = $confirmation->batch?->batch_code ?? 'غير محدد';
            Notification::create([
                'user_id' => $confirmation->deliveryNote->transferred_by ?? 1,
                'type' => 'production_rejected',
                'title' => 'تم رفض استلام الدفعة',
                'message' => "رفض " . Auth::user()->name . " استلام دفعة {$batchCode}. السبب: {$validated['rejection_reason']}",
                'action_url' => route('manufacturing.warehouse.registration.show', ['deliveryNote' => $confirmation->delivery_note_id]),
                'priority' => 'high',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم رفض الدفعة وإعادة الكمية للمستودع'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting production receipt: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفض الاستلام: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get confirmation details as JSON (API endpoint)
     */
    public function getDetails($id)
    {
        $confirmation = ProductionConfirmation::with([
            'deliveryNote.material',
            'batch',
            'assignedUser'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'confirmation' => [
                'id' => $confirmation->id,
                'status' => $confirmation->status,
                'stage_code' => $confirmation->stage_code,
                'stage_name' => ProductionStage::getByCode($confirmation->stage_code)?->stage_name,
                'batch_code' => $confirmation->batch?->batch_code,
                'production_barcode' => $confirmation->deliveryNote?->production_barcode,
                'material_name' => $confirmation->deliveryNote?->material?->name_ar,
                'quantity' => $confirmation->deliveryNote?->quantity,
                'assigned_to_name' => $confirmation->assignedUser?->name,
                'created_at' => $confirmation->created_at->format('Y-m-d H:i'),
            ]
        ]);
    }
}
