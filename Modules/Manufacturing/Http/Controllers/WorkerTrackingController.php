<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\WorkerTrackingService;
use App\Models\WorkerStageHistory;
use App\Models\User;
use App\Models\Stand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkerTrackingController extends Controller
{
    protected WorkerTrackingService $trackingService;

    public function __construct(WorkerTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * عرض تاريخ مرحلة معينة
     */
    public function stageHistory(Request $request, string $stageType, int $stageRecordId)
    {
        $history = $this->trackingService->getStageHistory($stageType, $stageRecordId);
        $statistics = $this->trackingService->getStageStatistics($stageType, $stageRecordId);
        $currentWorker = $this->trackingService->getCurrentWorkerForStage($stageType, $stageRecordId);

        return view('manufacturing::worker-tracking.stage-history', compact(
            'history',
            'statistics',
            'currentWorker',
            'stageType',
            'stageRecordId'
        ));
    }

    /**
     * عرض تاريخ عامل معين
     */
    public function workerHistory(Request $request, int $workerId)
    {
        $worker = User::findOrFail($workerId);
        $stageType = $request->get('stage_type');

        $history = $this->trackingService->getWorkerHistory($workerId, $stageType);
        $statistics = $this->trackingService->getWorkerStatistics($workerId);

        return view('manufacturing::worker-tracking.worker-history', compact(
            'worker',
            'history',
            'statistics',
            'stageType'
        ));
    }

    /**
     * البحث بالباركود
     */
    public function searchByBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);

        $history = $this->trackingService->getHistoryByBarcode($request->barcode);

        return view('manufacturing::worker-tracking.barcode-search', compact('history'));
    }

    /**
     * نقل العمل من عامل لآخر
     */
    public function transferWork(Request $request)
    {
        $request->validate([
            'stage_type' => 'required|string',
            'stage_record_id' => 'required|integer',
            'new_worker_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $this->trackingService->transferWork(
            stageType: $request->stage_type,
            stageRecordId: $request->stage_record_id,
            newWorkerId: $request->new_worker_id,
            notes: $request->notes,
            assignedBy: auth()->id()
        );

        return redirect()->back()->with('success', __('manufacturing::worker-tracking.work_transferred_successfully'));
    }

    /**
     * إنهاء جلسة عمل
     */
    public function endSession(Request $request, int $historyId)
    {
        $request->validate([
            'status_after' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $this->trackingService->endWorkerSession(
            historyId: $historyId,
            statusAfter: $request->status_after,
            notes: $request->notes
        );

        return redirect()->back()->with('success', __('manufacturing::worker-tracking.session_ended_successfully'));
    }

    /**
     * عرض العمال المتاحين
     */
    public function availableWorkers(Request $request)
    {
        $stageType = $request->get('stage_type');
        $workers = $this->trackingService->getAvailableWorkers($stageType);

        return response()->json([
            'workers' => $workers->map(fn($w) => [
                'id' => $w->id,
                'name' => $w->name,
                'email' => $w->email,
            ])
        ]);
    }

    /**
     * لوحة التحكم - نظرة عامة
     */
    public function dashboard(Request $request)
    {
        // If AJAX request, return data
        if ($request->get('ajax')) {
            $activeWorkers = WorkerStageHistory::with(['worker', 'workerTeam'])
                ->where('is_active', true)
                ->get()
                ->map(function($history) {
                    return [
                        'worker_name' => $history->worker ? $history->worker->name : ($history->workerTeam ? $history->workerTeam->name : 'N/A'),
                        'stage_type' => $history->stage_type,
                        'stage_record_id' => $history->stage_record_id,
                        'barcode' => $history->barcode,
                        'started_at' => $history->started_at,
                    ];
                });

            $recentCompleted = WorkerStageHistory::with(['worker', 'workerTeam'])
                ->where('is_active', false)
                ->whereNotNull('ended_at')
                ->orderBy('ended_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($history) {
                    return [
                        'worker_name' => $history->worker ? $history->worker->name : ($history->workerTeam ? $history->workerTeam->name : 'N/A'),
                        'stage_type' => $history->stage_type,
                        'started_at' => $history->started_at,
                        'ended_at' => $history->ended_at,
                        'duration_minutes' => $history->duration_minutes,
                    ];
                });

            $today = now()->startOfDay();
            $todayHistory = WorkerStageHistory::whereDate('started_at', '>=', $today)->get();

            $stats = [
                'activeWorkers' => WorkerStageHistory::where('is_active', true)->count(),
                'stagesInProgress' => WorkerStageHistory::where('is_active', true)
                    ->distinct('stage_type', 'stage_record_id')
                    ->count(),
                'avgDurationToday' => $todayHistory->where('is_active', false)->avg('duration_minutes') ?? 0,
                'totalWorkMinutesToday' => $todayHistory->sum('duration_minutes') ?? 0,
            ];

            return response()->json([
                'activeWorkers' => $activeWorkers,
                'recentCompleted' => $recentCompleted,
                'stats' => $stats,
            ]);
        }

        // Return view for normal page load
        return view('manufacturing::worker-tracking.dashboard');
    }

    /**
     * احصل على بيانات الوردية الحالية والعمال
     */
    public function currentShift(Request $request)
    {
        try {
            $shift = \App\Models\ShiftAssignment::where('status', 'active')
                ->latest('created_at')
                ->first();

            if (!$shift) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد وردية نشطة حالياً',
                    'shift' => null
                ]);
            }

            // احصل على بيانات المسؤول
            $supervisor = $shift->supervisor;

            // احصل على بيانات العمال
            $workers = \App\Models\Worker::whereIn('id', $shift->worker_ids ?? [])
                ->select('id', 'name', 'worker_code', 'position')
                ->get()
                ->toArray();

            return response()->json([
                'success' => true,
                'shift' => [
                    'id' => $shift->id,
                    'shift_code' => $shift->shift_code,
                    'shift_type' => $shift->shift_type,
                    'shift_date' => $shift->shift_date,
                    'stage_number' => $shift->stage_number,
                    'status' => $shift->status,
                    'worker_ids' => $shift->worker_ids ?? [],
                    'supervisor' => $supervisor ? [
                        'id' => $supervisor->id,
                        'name' => $supervisor->name,
                        'email' => $supervisor->email,
                        'phone' => $supervisor->phone ?? null
                    ] : null,
                    'workers' => $workers,
                    'workers_count' => count($workers)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ: ' . $e->getMessage(),
                'shift' => null
            ], 500);
        }
    }

    /**
     * احصل على العمال السابقين من الوردية السابقة
     */
    public function previousShiftWorkers(Request $request)
    {
        try {
            // احصل على الوردية السابقة (آخر وردية مكتملة)
            $previousShift = \App\Models\ShiftAssignment::where('status', 'completed')
                ->orWhere('status', 'inactive')
                ->latest('shift_date')
                ->first();

            if (!$previousShift) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد وردية سابقة',
                    'workers' => []
                ]);
            }

            // احصل على بيانات المسؤول للوردية السابقة
            $supervisor = $previousShift->supervisor;

            // احصل على بيانات العمال من الوردية السابقة
            $workers = \App\Models\Worker::whereIn('id', $previousShift->worker_ids ?? [])
                ->select('id', 'name', 'worker_code', 'position')
                ->get()
                ->map(function($worker) {
                    return [
                        'id' => $worker->id,
                        'name' => $worker->name,
                        'worker_code' => $worker->worker_code,
                        'position' => $worker->position ?? 'عام'
                    ];
                })
                ->toArray();

            return response()->json([
                'success' => true,
                'shift' => [
                    'id' => $previousShift->id,
                    'shift_code' => $previousShift->shift_code,
                    'shift_type' => $previousShift->shift_type,
                    'shift_date' => $previousShift->shift_date,
                    'status' => $previousShift->status,
                    'supervisor' => $supervisor ? [
                        'id' => $supervisor->id,
                        'name' => $supervisor->name,
                        'email' => $supervisor->email,
                    ] : null,
                ],
                'workers' => $workers,
                'workers_count' => count($workers)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ: ' . $e->getMessage(),
                'workers' => []
            ], 500);
        }
    }

    /**
     * نقل مرحلة من وردية إلى أخرى
     */
    public function transferStageToShift(Request $request)
    {
        Log::info('🔥 Transfer Stage Request Started', [
            'method' => $request->method(),
            'path' => $request->path(),
            'is_json' => $request->isJson(),
            'all_data' => $request->all()
        ]);

        try {
            $validated = $request->validate([
                'stand_id' => 'required|integer',
                'from_shift_id' => 'required|integer',
                'to_shift_id' => 'required|integer',
                'notes' => 'nullable|string|max:500'
            ]);

            Log::info('✅ Validation Passed', $validated);

            DB::beginTransaction();

            // احصل على الوردية الأصلية
            $fromShift = \App\Models\ShiftAssignment::findOrFail($validated['from_shift_id']);
            // احصل على الوردية الجديدة
            $toShift = \App\Models\ShiftAssignment::findOrFail($validated['to_shift_id']);

            // احصل على بيانات الستاند
            $stand = \App\Models\Stand::findOrFail($validated['stand_id']);

            // احصل على سجل stage1_stands الخاص بهذا الستاند
            $stage1Stand = DB::table('stage1_stands')
                ->where('stand_number', $stand->stand_number)
                ->where('material_id', $stand->material_id)
                ->orderBy('created_at', 'desc')
                ->first();

            // 🔥 أولاً: إزالة الستاند من الوردية الأصلية
            $fromShift->update([
                'stage_record_id' => null,
                'stage_record_barcode' => null,
            ]);

            // 🔥 ثانياً: إضافة الستاند إلى الوردية الجديدة
            $toShift->update([
                'stage_record_id' => $stand->id,
                'stage_record_barcode' => $stand->barcode ?? 'STAND-' . $stand->id,
            ]);

            Log::info('✅ Updated Shift Assignments', [
                'from_shift_id' => $fromShift->id,
                'to_shift_id' => $toShift->id,
                'stand_id' => $stand->id
            ]);

            // 🔥 تحديث سجل المرحلة ليعكس الوردية الجديدة (إذا كان موجوداً)
            if ($stage1Stand) {
                DB::table('stage1_stands')->where('id', $stage1Stand->id)->update([
                    'shift_id' => $toShift->id,
                    'supervisor_id' => $toShift->supervisor_id,
                    'updated_at' => now(),
                ]);
                Log::info('✅ Updated stage1_stands record', ['id' => $stage1Stand->id]);
            } else {
                Log::warning('⚠️ No stage1_stands record found for this stand', [
                    'stand_number' => $stand->stand_number,
                    'material_id' => $stand->material_id
                ]);
            }

            // ملاحظة: لا نحدث الستاند مباشرة لتجنب مشاكل الـ enum
            // الستاند سيحتفظ بحالته الحالية

            // تسجيل في جدول product_tracking لتتبع النقل
            // استخدم باركود الستاند أو رقم الستاند كـ fallback
            $trackingBarcode = $stand->barcode ?? 'STAND-' . $stand->id . '-' . $stand->stand_number;

            DB::table('product_tracking')->insert([
                'barcode' => $trackingBarcode,
                'stage' => 'stage1',
                'action' => 'transfer_shift',
                'input_barcode' => $trackingBarcode,
                'output_barcode' => $trackingBarcode,
                'worker_id' => auth()->id(),
                'shift_id' => $toShift->id,
                'notes' => json_encode([
                    'transfer_type' => 'stage1_stand',
                    'from_shift_id' => $fromShift->id,
                    'to_shift_id' => $toShift->id,
                    'from_shift_code' => $fromShift->shift_code,
                    'to_shift_code' => $toShift->shift_code,
                    'from_supervisor' => $fromShift->supervisor?->name,
                    'to_supervisor' => $toShift->supervisor?->name,
                ]),
                'metadata' => json_encode([
                    'stand_id' => $stand->id,
                    'stand_number' => $stand->stand_number,
                    'transferred_by' => auth()->user()?->name,
                    'transfer_reason' => $validated['notes'] ?? 'نقل روتيني',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 🔥 نقل تتبع العمال من الوردية الأصلية إلى الجديدة
            // إنهاء تتبع العمال في الوردية الأصلية
            \App\Models\WorkerStageHistory::where('stage_type', 'stage1_stands')
                ->where('stage_record_id', $stand->id)
                ->where('shift_assignment_id', $fromShift->id)
                ->whereNull('ended_at')
                ->where('is_active', true)
                ->update([
                    'ended_at' => now(),
                    'is_active' => false,
                    'notes' => 'تم نقل المرحلة إلى وردية أخرى - ' . ($validated['notes'] ?? '')
                ]);

            // إضافة تتبع جديد للعمال من الوردية الجديدة
            $toShiftWorkerIds = $toShift->worker_ids ?? [];

            if (!empty($toShiftWorkerIds)) {
                foreach ($toShiftWorkerIds as $workerId) {
                    // تحقق من عدم وجود سجل نشط بالفعل
                    $existingHistory = \App\Models\WorkerStageHistory::where('stage_type', 'stage1_stands')
                        ->where('stage_record_id', $stand->id)
                        ->where('shift_assignment_id', $toShift->id)
                        ->where('worker_id', $workerId)
                        ->whereNull('ended_at')
                        ->first();

                    if (!$existingHistory) {
                        $trackingBarcode = $stand->barcode ?? 'STAND-' . $stand->id . '-' . $stand->stand_number;

                        \App\Models\WorkerStageHistory::create([
                            'stage_type' => 'stage1_stands',
                            'stage_record_id' => $stand->id,
                            'barcode' => $trackingBarcode,
                            'worker_id' => $workerId,
                            'worker_type' => 'individual',
                            'started_at' => now(),
                            'ended_at' => null,
                            'is_active' => true,
                            'shift_assignment_id' => $toShift->id,
                            'assigned_by' => auth()->id(),
                            'notes' => 'نقل من الوردية ' . $fromShift->shift_code . ' إلى الوردية ' . $toShift->shift_code
                        ]);
                    }
                }
            }

            // إنشاء سجل نقل العملية في logs
            \App\Models\ShiftOperationLog::create([
                'stage_number' => 1,
                'shift_id' => $toShift->id,
                'operation_type' => 'transfer_stage',
                'description' => "نقل المرحلة {$stand->stand_number} من الوردية {$fromShift->shift_code} إلى الوردية {$toShift->shift_code}",
                'old_data' => [
                    'shift_id' => $fromShift->id,
                    'shift_code' => $fromShift->shift_code,
                    'supervisor_id' => $fromShift->supervisor_id,
                    'supervisor_name' => $fromShift->supervisor?->name,
                    'shift_type' => $fromShift->shift_type,
                    'workers_count' => count($fromShift->worker_ids ?? []),
                    'worker_ids' => $fromShift->worker_ids,
                    'stand_id' => $stand->id,
                    'stand_number' => $stand->stand_number,
                    'stand_status' => $stand->status
                ],
                'new_data' => [
                    'shift_id' => $toShift->id,
                    'shift_code' => $toShift->shift_code,
                    'supervisor_id' => $toShift->supervisor_id,
                    'supervisor_name' => $toShift->supervisor?->name,
                    'shift_type' => $toShift->shift_type,
                    'workers_count' => count($toShift->worker_ids ?? []),
                    'worker_ids' => $toShift->worker_ids,
                    'stand_id' => $stand->id,
                    'stand_number' => $stand->stand_number,
                    'stand_status' => $stand->status
                ],
                'notes' => $validated['notes'] ?? null,
                'user_id' => auth()->id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "✅ تم نقل المرحلة {$stand->stand_number} بنجاح من الوردية {$fromShift->shift_code} إلى الوردية {$toShift->shift_code}",
                'data' => [
                    'stand_id' => $stand->id,
                    'stand_number' => $stand->stand_number,
                    'stand_barcode' => $stand->barcode,
                    'stand_status' => $stand->status,
                    'from_shift' => [
                        'id' => $fromShift->id,
                        'shift_code' => $fromShift->shift_code,
                        'supervisor_id' => $fromShift->supervisor_id,
                        'supervisor_name' => $fromShift->supervisor?->name,
                    ],
                    'to_shift' => [
                        'id' => $toShift->id,
                        'shift_code' => $toShift->shift_code,
                        'supervisor_id' => $toShift->supervisor_id,
                        'supervisor_name' => $toShift->supervisor?->name,
                        'workers_count' => count($toShift->worker_ids ?? []),
                    ]
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation Failed:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من البيانات: ' . json_encode($e->errors()),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transfer Stage Error: ' . $e->getMessage(), [
                'stand_id' => $validated['stand_id'] ?? null,
                'from_shift_id' => $validated['from_shift_id'] ?? null,
                'to_shift_id' => $validated['to_shift_id'] ?? null,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => '❌ خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * احصل على قائمة الورديات المتاحة للنقل
     */
    public function getAvailableShifts(Request $request)
    {
        try {
            $currentShiftId = $request->get('current_shift_id');

            // احصل على جميع الورديات النشطة والمكتملة (ما عدا الوردية الحالية)
            $shifts = \App\Models\ShiftAssignment::where('stage_number', 1)
                ->whereIn('status', ['active', 'completed'])
                ->where('id', '!=', $currentShiftId)
                ->with('supervisor')
                ->orderBy('shift_date', 'desc')
                ->get()
                ->map(function ($shift) {
                    // احصل على بيانات العمال
                    $workers = \App\Models\Worker::whereIn('id', $shift->worker_ids ?? [])
                        ->select('id', 'name', 'worker_code', 'position')
                        ->get()
                        ->toArray();

                    return [
                        'id' => $shift->id,
                        'shift_code' => $shift->shift_code,
                        'shift_type' => $shift->shift_type,
                        'shift_date' => $shift->shift_date,
                        'status' => $shift->status,
                        'supervisor_name' => $shift->supervisor?->name ?? 'لم يحدد',
                        'supervisor' => $shift->supervisor ? [
                            'id' => $shift->supervisor->id,
                            'name' => $shift->supervisor->name,
                            'email' => $shift->supervisor->email
                        ] : null,
                        'workers_count' => count($workers),
                        'workers' => $workers
                    ];
                });

            return response()->json([
                'success' => true,
                'shifts' => $shifts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ: ' . $e->getMessage()
            ], 500);
        }
    }
}
