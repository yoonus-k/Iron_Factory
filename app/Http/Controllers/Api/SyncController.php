<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SyncController extends Controller
{
    protected $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * رفع البيانات من الجهاز المحلي للسيرفر
     * 
     * POST /api/sync/push
     * 
     * Body:
     * {
     *   "data": [
     *     {
     *       "entity_type": "material",
     *       "action": "create",
     *       "local_id": "uuid-here",
     *       "data": {...}
     *     }
     *   ]
     * }
     */
    public function push(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.entity_type' => 'required|string',
            'data.*.action' => 'required|in:create,update,delete',
            'data.*.data' => 'required|array',
            'data.*.local_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = auth()->id();
        $data = $request->input('data');

        $result = $this->syncService->pushToServer($userId, $data);

        return response()->json($result);
    }

    /**
     * سحب البيانات من السيرفر للجهاز المحلي
     * 
     * GET /api/sync/pull
     * 
     * Query Parameters:
     * - last_sync_time: آخر وقت مزامنة (ISO 8601)
     */
    public function pull(Request $request)
    {
        $userId = auth()->id();
        $lastSyncTime = $request->input('last_sync_time');

        if ($lastSyncTime) {
            $lastSyncTime = \Carbon\Carbon::parse($lastSyncTime);
        }

        $result = $this->syncService->pullFromServer($userId, $lastSyncTime);

        return response()->json($result);
    }

    /**
     * معالجة العمليات المعلقة
     * 
     * POST /api/sync/process-pending
     */
    public function processPending(Request $request)
    {
        $userId = auth()->id();
        $limit = $request->input('limit', 50);

        $result = $this->syncService->processPendingSyncs($userId, $limit);

        return response()->json([
            'success' => true,
            'result' => $result,
        ]);
    }

    /**
     * إضافة عملية للانتظار (عند عدم وجود إنترنت)
     * 
     * POST /api/sync/queue
     * 
     * Body:
     * {
     *   "entity_type": "material",
     *   "action": "create",
     *   "data": {...},
     *   "priority": 0
     * }
     */
    public function queue(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entity_type' => 'required|string',
            'action' => 'required|in:create,update,delete',
            'data' => 'required|array',
            'priority' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = auth()->id();
        $entityType = $request->input('entity_type');
        $action = $request->input('action');
        $data = $request->input('data');
        $priority = $request->input('priority', 0);

        $pendingSync = $this->syncService->addToPendingQueue(
            $userId,
            $entityType,
            $action,
            $data,
            $priority
        );

        return response()->json([
            'success' => true,
            'pending_sync' => $pendingSync,
        ], 201);
    }

    /**
     * الحصول على إحصائيات المزامنة
     * 
     * GET /api/sync/stats
     */
    public function stats(Request $request)
    {
        $userId = auth()->id();
        $stats = $this->syncService->getSyncStats($userId);

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * التحقق من حالة الاتصال
     * 
     * GET /api/sync/health
     */
    public function health()
    {
        return response()->json([
            'success' => true,
            'status' => 'online',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * دمج البيانات (Batch sync)
     * 
     * POST /api/sync/batch
     * 
     * Body:
     * {
     *   "operations": [
     *     {
     *       "entity_type": "material",
     *       "action": "create",
     *       "data": {...}
     *     },
     *     ...
     *   ]
     * }
     */
    public function batch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'operations' => 'required|array',
            'operations.*.entity_type' => 'required|string',
            'operations.*.action' => 'required|in:create,update,delete',
            'operations.*.data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = auth()->id();
        $operations = $request->input('operations');

        $result = $this->syncService->pushToServer($userId, $operations);

        return response()->json($result);
    }

    /**
     * إعادة محاولة العمليات الفاشلة
     * 
     * POST /api/sync/retry-failed
     */
    public function retryFailed(Request $request)
    {
        $userId = auth()->id();
        
        $failedSyncs = \App\Models\PendingSync::where('user_id', $userId)
            ->where('status', 'failed')
            ->get();

        $retriedCount = 0;
        foreach ($failedSyncs as $sync) {
            $sync->retry();
            $retriedCount++;
        }

        return response()->json([
            'success' => true,
            'retried_count' => $retriedCount,
        ]);
    }
}
