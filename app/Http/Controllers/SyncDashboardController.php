<?php

namespace App\Http\Controllers;

use App\Models\SyncLog;
use App\Models\SyncHistory;
use App\Models\PendingSync;
use App\Models\UserLastSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncDashboardController extends Controller
{
    /**
     * عرض Dashboard المزامنة
     */
    public function index()
    {
        return view('sync.dashboard');
    }

    /**
     * الحصول على إحصائيات المزامنة العامة
     */
    public function stats()
    {
        $stats = [
            // إحصائيات عامة
            'total_pending' => PendingSync::pending()->count(),
            'total_failed' => PendingSync::failed()->count(),
            'total_synced' => SyncLog::synced()->count(),
            'total_users' => UserLastSync::count(),
            
            // حسب الحالة
            'by_status' => PendingSync::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status'),
            
            // حسب نوع الكيان
            'by_entity' => PendingSync::select('entity_type', DB::raw('count(*) as count'))
                ->groupBy('entity_type')
                ->limit(10)
                ->pluck('count', 'entity_type'),
            
            // العمليات الأخيرة (آخر 24 ساعة)
            'last_24h' => [
                'pending' => PendingSync::where('created_at', '>=', now()->subDay())->count(),
                'synced' => SyncLog::where('synced_at', '>=', now()->subDay())->synced()->count(),
                'failed' => PendingSync::where('created_at', '>=', now()->subDay())->failed()->count(),
            ],
            
            // معدل النجاح
            'success_rate' => $this->calculateSuccessRate(),
            
            // متوسط وقت المزامنة
            'avg_sync_time' => $this->calculateAverageSyncTime(),
        ];

        return response()->json($stats);
    }

    /**
     * الحصول على العمليات المعلقة
     */
    public function pending(Request $request)
    {
        $query = PendingSync::with('user:id,name,email')
            ->pending()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc');

        // فلترة حسب نوع الكيان
        if ($request->has('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        // فلترة حسب المستخدم
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $pending = $query->paginate(20);

        return response()->json($pending);
    }

    /**
     * الحصول على العمليات الفاشلة
     */
    public function failed(Request $request)
    {
        $query = PendingSync::with('user:id,name,email')
            ->failed()
            ->orderBy('updated_at', 'desc');

        $failed = $query->paginate(20);

        return response()->json($failed);
    }

    /**
     * الحصول على سجل المزامنة
     */
    public function history(Request $request)
    {
        $query = SyncHistory::with('user:id,name,email')
            ->orderBy('synced_to_server', 'desc');

        // فلترة حسب الفترة الزمنية
        if ($request->has('from')) {
            $query->where('synced_to_server', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->where('synced_to_server', '<=', $request->to);
        }

        // فلترة حسب نوع العملية
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        $history = $query->paginate(50);

        return response()->json($history);
    }

    /**
     * الحصول على حالة المزامنة للمستخدمين
     */
    public function users()
    {
        $users = UserLastSync::with('user:id,name,email')
            ->orderBy('last_push_at', 'desc')
            ->get()
            ->map(function ($userSync) {
                return [
                    'id' => $userSync->user->id,
                    'name' => $userSync->user->name,
                    'email' => $userSync->user->email,
                    'last_pull' => $userSync->last_pull_at?->diffForHumans(),
                    'last_push' => $userSync->last_push_at?->diffForHumans(),
                    'pending_count' => $userSync->pending_count,
                    'failed_count' => $userSync->failed_count,
                    'status' => $this->getUserSyncStatus($userSync),
                ];
            });

        return response()->json($users);
    }

    /**
     * إعادة محاولة عملية معلقة
     */
    public function retry($id)
    {
        $pendingSync = PendingSync::findOrFail($id);
        $pendingSync->retry();

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة المحاولة بنجاح',
        ]);
    }

    /**
     * حذف عملية معلقة
     */
    public function delete($id)
    {
        $pendingSync = PendingSync::findOrFail($id);
        $pendingSync->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم الحذف بنجاح',
        ]);
    }

    /**
     * إعادة محاولة كل العمليات الفاشلة
     */
    public function retryAll()
    {
        $failedSyncs = PendingSync::failed()->get();
        $count = 0;

        foreach ($failedSyncs as $sync) {
            $sync->retry();
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "تم إعادة محاولة {$count} عملية",
            'count' => $count,
        ]);
    }

    /**
     * مسح العمليات القديمة المزامنة
     */
    public function cleanup(Request $request)
    {
        $days = $request->input('days', 30);
        
        $deleted = SyncLog::where('synced_at', '<', now()->subDays($days))
            ->synced()
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "تم حذف {$deleted} سجل قديم",
            'deleted' => $deleted,
        ]);
    }

    /**
     * الحصول على إحصائيات الرسم البياني
     */
    public function chartData(Request $request)
    {
        $days = $request->input('days', 7);
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            
            $data[] = [
                'date' => $date,
                'synced' => SyncLog::whereDate('synced_at', $date)->synced()->count(),
                'failed' => PendingSync::whereDate('created_at', $date)->failed()->count(),
                'pending' => PendingSync::whereDate('created_at', $date)->pending()->count(),
            ];
        }

        return response()->json($data);
    }

    /**
     * حساب معدل النجاح
     */
    protected function calculateSuccessRate()
    {
        $total = SyncLog::count();
        if ($total === 0) return 0;

        $synced = SyncLog::synced()->count();
        return round(($synced / $total) * 100, 2);
    }

    /**
     * حساب متوسط وقت المزامنة
     */
    protected function calculateAverageSyncTime()
    {
        // يمكن تحسينه لاحقاً بإضافة حقل duration في SyncLog
        return 0;
    }

    /**
     * تحديد حالة المزامنة للمستخدم
     */
    protected function getUserSyncStatus($userSync)
    {
        if ($userSync->failed_count > 0) {
            return 'error';
        }

        if ($userSync->pending_count > 0) {
            return 'pending';
        }

        if ($userSync->last_push_at && $userSync->last_push_at->gt(now()->subMinutes(5))) {
            return 'active';
        }

        return 'idle';
    }
}
