<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkerStageHistory;
use Modules\Manufacturing\Models\ProductTracking;
use App\Models\ProductionConfirmation;
use App\Models\User;
use App\Models\Notification;

class PendingProductionController extends Controller
{
    /**
     * عرض السجلات غير المكتملة
     */
    public function index(Request $request)
    {
        // استخدام worker_stage_history مباشرة للحصول على العمليات النشطة
        $query = DB::table('worker_stage_history as wsh')
            ->select(
                'wsh.id',
                'wsh.barcode',
                'wsh.stage_type',
                'wsh.stage_record_id',
                'wsh.worker_id',
                'wsh.started_at',
                'wsh.ended_at',
                'wsh.duration_minutes',
                'wsh.status_before',
                'wsh.is_active',
                'u.name as worker_name'
            )
            ->leftJoin('users as u', 'wsh.worker_id', '=', 'u.id')
            ->where('wsh.is_active', true) // فقط العمليات النشطة
            ->where(function($q) {
                // استثناء السجلات التي أكملت المرحلة الرابعة ووصلت للمستودع
                $q->where('wsh.stage_type', '!=', 'stage4_boxes')
                  ->orWhereExists(function($subQuery) {
                      $subQuery->select(DB::raw(1))
                          ->from('stage4_boxes as s4')
                          ->whereColumn('s4.id', 'wsh.stage_record_id')
                          ->whereNotIn('s4.status', ['in_warehouse', 'shipped', 'delivered']);
                  });
            });

        // فلترة بالباركود
        if ($request->filled('barcode')) {
            $query->where('wsh.barcode', 'like', '%' . $request->barcode . '%');
        }

        // فلترة بالمرحلة
        if ($request->filled('stage')) {
            $stageTypeMap = [
                'stage1' => 'stage1_stands',
                'stage2' => 'stage2_processed',
                'stage3' => 'stage3_coils',
                'stage4' => 'stage4_boxes',
            ];
            if (isset($stageTypeMap[$request->stage])) {
                $query->where('wsh.stage_type', $stageTypeMap[$request->stage]);
            }
        }

        // فلترة بالعامل
        if ($request->filled('worker_id')) {
            $query->where('wsh.worker_id', $request->worker_id);
        }

        // فلترة بالتاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('wsh.started_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('wsh.started_at', '<=', $request->date_to);
        }

        // ترتيب حسب الأحدث
        $query->orderBy('wsh.started_at', 'desc');

        $records = $query->paginate(20);

        // جلب قائمة العمال للفلترة والتعيين (جميع المستخدمين)
        $workers = User::select('id', 'name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // المراحل المتاحة
        $stages = [
            'warehouse' => 'المستودع',
            'stage1' => 'المرحلة الأولى',
            'stage2' => 'المرحلة الثانية',
            'stage3' => 'المرحلة الثالثة',
            'stage4' => 'المرحلة الرابعة',
        ];

        return view('manufacturing::pending-production.index', compact('records', 'workers', 'stages'));
    }

    /**
     * إعادة إسناد سجل لعامل آخر
     */
    public function reassign(Request $request, $id)
    {
        $validated = $request->validate([
            'new_worker_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'shift_transfer' => 'nullable|boolean', // نقل للوردية التالية
        ]);

        try {
            DB::beginTransaction();

            // جلب السجل من worker_stage_history
            $record = WorkerStageHistory::find($id);

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'السجل غير موجود'
                ], 404);
            }

            // التحقق من أن السجل نشط
            if (!$record->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا السجل غير نشط'
                ], 400);
            }

            // التحقق من أن العامل الجديد مختلف
            if ($record->worker_id == $validated['new_worker_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'العامل الجديد هو نفس العامل الحالي'
                ], 400);
            }

            $oldWorkerId = $record->worker_id;
            $newWorkerId = $validated['new_worker_id'];

            // إنهاء التعيين الحالي
            $record->update([
                'is_active' => false,
                'ended_at' => now(),
                'duration_minutes' => DB::raw('TIMESTAMPDIFF(MINUTE, started_at, NOW())'),
                'status_after' => 'reassigned',
            ]);

            // إنشاء سجل جديد للعامل الجديد
            $shiftTransfer = $validated['shift_transfer'] ?? false;
            $newHistory = WorkerStageHistory::create([
                'stage_type' => $record->stage_type,
                'stage_record_id' => $record->stage_record_id,
                'barcode' => $record->barcode,
                'worker_id' => $newWorkerId,
                'worker_type' => 'individual',
                'started_at' => now(),
                'status_before' => $shiftTransfer ? 'shift_transfer' : 'reassigned',
                'is_active' => true,
                'assigned_by' => Auth::id(),
                'notes' => $shiftTransfer ? 'نقل للوردية التالية' . ($validated['notes'] ? ' - ' . $validated['notes'] : '') : ($validated['notes'] ?? null),
            ]);

            // إنشاء production_confirmation للعامل الجديد (للوحة العامل)
            $stageCode = $this->mapStageTypeToStageCode($record->stage_type);
            $stageName = $this->getStageNameFromType($record->stage_type);

            ProductionConfirmation::create([
                'delivery_note_id' => null,
                'batch_id' => null,
                'stage_code' => $stageCode,
                'stage_record_id' => $record->stage_record_id,
                'stage_type' => $record->stage_type,
                'worker_stage_history_id' => $newHistory->id,
                'barcode' => $record->barcode,
                'assigned_to' => $newWorkerId,
                'assigned_by' => Auth::id(),
                'status' => 'pending',
                'confirmation_type' => $shiftTransfer ? 'shift_transfer' : 'reassignment',
                'notes' => $validated['notes'] ?? null,
                'metadata' => [
                    'stage_name' => $stageName,
                    'operation' => $shiftTransfer ? 'shift_transfer' : 'reassignment',
                    'reason' => $validated['reason'] ?? null,
                    'initiated_by' => Auth::user()?->name,
                    'previous_worker_id' => $oldWorkerId,
                    'shift_transfer' => $shiftTransfer,
                    'barcode' => $record->barcode,
                ],
            ]);

            // إرسال إشعار للعامل القديم
            if ($oldWorkerId) {
                $stageName = $this->getStageNameFromType($record->stage_type);
                Notification::create([
                    'user_id' => $oldWorkerId,
                    'type' => 'work_reassigned',
                    'title' => 'تم نقل العمل',
                    'message' => "تم نقل العمل على الباركود {$record->barcode} في {$stageName} إلى عامل آخر",
                    'data' => json_encode([
                        'barcode' => $record->barcode,
                        'stage' => $record->stage_type,
                        'new_worker_id' => $newWorkerId,
                        'reason' => $validated['reason'] ?? null,
                    ]),
                ]);
            }

            // إرسال إشعار للعامل الجديد
            $stageName = $this->getStageNameFromType($record->stage_type);
            Notification::create([
                'user_id' => $newWorkerId,
                'type' => 'work_assigned',
                'title' => 'تم إسناد عمل جديد',
                'message' => "تم إسناد العمل على الباركود {$record->barcode} في {$stageName} إليك",
                'data' => json_encode([
                    'barcode' => $record->barcode,
                    'stage' => $record->stage_type,
                    'old_worker_id' => $oldWorkerId,
                    'notes' => $validated['notes'] ?? null,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إعادة إسناد السجل بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إعادة الإسناد: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض تاريخ التعيينات لباركود معين
     */
    public function history($barcode)
    {
        $logs = WorkerStageHistory::with(['worker', 'assignedBy', 'shiftAssignment'])
            ->where('barcode', $barcode)
            ->orderBy('started_at', 'desc')
            ->get();

        return view('manufacturing::pending-production.history', compact('barcode', 'logs'));
    }

    /**
     * عرض تفاصيل التعيين
     */
    public function show($id)
    {
        $log = WorkerStageHistory::with(['worker', 'assignedBy', 'shiftAssignment'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $log->id,
                'barcode' => $log->barcode,
                'stage' => $this->getStageNameFromType($log->stage_type),
                'worker' => $log->worker ? $log->worker->name : 'غير محدد',
                'assigned_by' => $log->assignedBy ? $log->assignedBy->name : 'النظام',
                'status' => $log->is_active ? 'نشط' : 'منتهي',
                'started_at' => $log->started_at->format('Y-m-d H:i:s'),
                'ended_at' => $log->ended_at ? $log->ended_at->format('Y-m-d H:i:s') : null,
                'duration_minutes' => $log->duration_minutes,
                'notes' => $log->notes,
            ]
        ]);
    }

    /**
     * الحصول على اسم المرحلة بالعربية
     */
    private function getStageName($stage): string
    {
        $stageNames = [
            'warehouse' => 'المستودع',
            'stage1' => 'المرحلة الأولى',
            'stage2' => 'المرحلة الثانية',
            'stage3' => 'المرحلة الثالثة',
            'stage4' => 'المرحلة الرابعة',
        ];

        return $stageNames[$stage] ?? $stage;
    }

    /**
     * تحويل اسم المرحلة إلى stage_type
     */
    private function getStageTypeFromStage($stage): string
    {
        $mapping = [
            'stage1' => 'stage1_stands',
            'stage2' => 'stage2_processed',
            'stage3' => 'stage3_coils',
            'stage4' => 'stage4_boxes',
            'warehouse' => 'warehouse',
        ];

        return $mapping[$stage] ?? $stage;
    }

    /**
     * تحويل stage_type إلى stage_code المعتمد في production_confirmations
     */
    private function mapStageTypeToStageCode($stageType): string
    {
        $mapping = [
            'stage1_stands' => 'stage_1',
            'stage2_processed' => 'stage_2',
            'stage3_coils' => 'stage_3',
            'stage4_boxes' => 'stage_4',
            'warehouse' => 'warehouse',
        ];

        return $mapping[$stageType] ?? 'custom';
    }

    /**
     * تحويل stage_type إلى اسم بالعربية
     */
    private function getStageNameFromType($stageType): string
    {
        $stageNames = [
            'warehouse' => 'المستودع',
            'stage1_stands' => 'المرحلة الأولى - الاستاند',
            'stage2_processed' => 'المرحلة الثانية - المعالج',
            'stage3_coils' => 'المرحلة الثالثة - اللفائف',
            'stage4_boxes' => 'المرحلة الرابعة - الصناديق',
        ];

        return $stageNames[$stageType] ?? $stageType;
    }
}
