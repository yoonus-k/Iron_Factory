<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\ProductTracking;
use App\Models\Stage4Box;
use App\Models\StageSuspension;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $roleCode = $user->role->role_code ?? null;

        // إذا كان المستخدم عامل مرحلة، توجيهه إلى لوحة التحكم الخاصة به
        if (in_array($roleCode, ['STAGE1_WORKER', 'STAGE2_WORKER', 'STAGE3_WORKER', 'STAGE4_WORKER'])) {
            return redirect()->route('stage-worker.dashboard.index');
        }

        // Get ALL notifications without any conditions or restrictions
        $notifications = Notification::with('creator')
            ->latest()
            ->get();

        // Get unread count for all notifications
        $unreadCount = Notification::unread()->count();

        $today = Carbon::today();
        $startOfToday = $today->copy()->startOfDay();
        $endOfToday = $today->copy()->endOfDay();
        $yesterday = $today->copy()->subDay();
        $weeklyWindowStart = $today->copy()->subDays(6)->startOfDay();

        $dailyProduction = (float) ProductTracking::whereBetween('created_at', [$startOfToday, $endOfToday])
            ->sum('output_weight');

        $yesterdayProduction = (float) ProductTracking::whereBetween('created_at', [$yesterday->copy()->startOfDay(), $yesterday->copy()->endOfDay()])
            ->sum('output_weight');

        $dailyTrend = $yesterdayProduction > 0
            ? (($dailyProduction - $yesterdayProduction) / $yesterdayProduction) * 100
            : null;

        $qualityAgg = Stage4Box::selectRaw(
            sprintf(
                "COUNT(*) as total, SUM(CASE WHEN status IN ('%s','%s','%s') THEN 1 ELSE 0 END) as passed",
                Stage4Box::STATUS_PACKED,
                Stage4Box::STATUS_SHIPPED,
                Stage4Box::STATUS_DELIVERED
            )
        )->first();

        $qualityRate = ($qualityAgg?->total ?? 0) > 0
            ? ($qualityAgg->passed / $qualityAgg->total) * 100
            : null;

        $downtimeCount = StageSuspension::pending()->count();

        $throughput = ProductTracking::selectRaw('SUM(input_weight) as total_input, SUM(output_weight) as total_output')
            ->whereBetween('created_at', [$weeklyWindowStart, $endOfToday])
            ->first();

        $machineEfficiency = ($throughput?->total_input ?? 0) > 0
            ? ($throughput->total_output / $throughput->total_input) * 100
            : null;

        $dashboardStats = [
            [
                'title' => 'الإنتاج اليومي',
                'icon' => 'fas fa-industry',
                'style' => 'primary',
                'value' => $dailyProduction,
                'decimals' => 0,
                'unit' => 'كجم',
                'trend' => $dailyTrend,
                'hint' => $dailyProduction > 0 ? 'مجموع الإنتاج المسجل اليوم' : 'لا توجد عمليات اليوم',
            ],
            [
                'title' => 'معدل الجودة',
                'icon' => 'fas fa-check-circle',
                'style' => 'success',
                'value' => $qualityRate,
                'decimals' => 1,
                'unit' => '%',
                'trend' => null,
                'hint' => $qualityRate === null ? 'لا توجد بيانات جارية' : ($qualityRate >= 95 ? 'ممتاز' : 'بحاجة متابعة'),
            ],
            [
                'title' => 'بلاغات التوقف',
                'icon' => 'fas fa-exclamation-triangle',
                'style' => 'warning',
                'value' => $downtimeCount,
                'decimals' => 0,
                'unit' => 'حالة',
                'trend' => null,
                'hint' => $downtimeCount > 0 ? 'يوجد حالات قيد المراجعة' : 'لا توجد توقيفات نشطة',
            ],
            [
                'title' => 'كفاءة التحويل',
                'icon' => 'fas fa-tachometer-alt',
                'style' => 'info',
                'value' => $machineEfficiency,
                'decimals' => 1,
                'unit' => '%',
                'trend' => null,
                'hint' => $machineEfficiency === null ? 'لا توجد بيانات كافية' : 'متوسط آخر 7 أيام',
            ],
        ];

        $weeklyRaw = ProductTracking::select(
            DB::raw('DATE(created_at) as day'),
            DB::raw('SUM(output_weight) as total_output')
        )
            ->whereBetween('created_at', [$weeklyWindowStart, $endOfToday])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total_output', 'day');

        $weeklyProduction = [];
        for ($date = $weeklyWindowStart->copy(); $date->lte($today); $date->addDay()) {
            $key = $date->toDateString();
            $weeklyProduction[] = [
                'label' => $date->locale(app()->getLocale())->translatedFormat('D'),
                'value' => round((float) ($weeklyRaw[$key] ?? 0), 2),
                'date' => $key,
            ];
        }

        $statusRaw = Stage4Box::select('status', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$weeklyWindowStart, $endOfToday])
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusMap = [
            Stage4Box::STATUS_PACKING => ['label' => 'قيد التعبئة', 'color' => '#f59e0b'],
            Stage4Box::STATUS_PACKED => ['label' => 'جاهزة للتسليم', 'color' => '#22c55e'],
            'pending_approval' => ['label' => 'بانتظار الموافقة', 'color' => '#fb923c'],
            Stage4Box::STATUS_SHIPPED => ['label' => 'تم الشحن', 'color' => '#3b82f6'],
            Stage4Box::STATUS_DELIVERED => ['label' => 'تم التسليم', 'color' => '#8b5cf6'],
            'in_process' => ['label' => 'قيد التنفيذ', 'color' => '#0ea5e9'],
            'in_warehouse' => ['label' => 'مخزن في المستودع', 'color' => '#6366f1'],
        ];

        $totalStatusCount = array_sum($statusRaw);
        $statusSegments = [];
        if ($totalStatusCount > 0) {
            foreach ($statusRaw as $status => $count) {
                $config = $statusMap[$status] ?? ['label' => ucfirst($status), 'color' => '#94a3b8'];
                $statusSegments[] = [
                    'status' => $status,
                    'label' => $config['label'],
                    'color' => $config['color'],
                    'count' => (int) $count,
                    'percentage' => round(($count / $totalStatusCount) * 100, 1),
                ];
            }

            usort($statusSegments, fn ($a, $b) => $b['count'] <=> $a['count']);
        }

        $lineNames = [
            1 => 'المرحلة 1: التقسيم والاستاندات',
            2 => 'المرحلة 2: المعالجة',
            3 => 'المرحلة 3: تصنيع الكويلات',
            4 => 'المرحلة 4: التعبئة',
        ];

        $lineOutputs = ProductTracking::select('stage', DB::raw('SUM(output_weight) as total_output'))
            ->whereBetween('created_at', [$startOfToday, $endOfToday])
            ->groupBy('stage')
            ->pluck('total_output', 'stage')
            ->map(fn ($value) => round((float) $value, 2))
            ->toArray();

        $lineAlerts = StageSuspension::pending()
            ->select('stage_number', DB::raw('COUNT(*) as total'))
            ->groupBy('stage_number')
            ->pluck('total', 'stage_number')
            ->toArray();

        $productionLines = [];
        foreach ($lineNames as $stageNumber => $title) {
            $stageKey = 'stage' . $stageNumber;
            $issues = (int) ($lineAlerts[$stageNumber] ?? 0);
            $productionLines[] = [
                'title' => $title,
                'status' => $issues > 0 ? 'maintenance' : 'active',
                'status_text' => $issues > 0 ? 'قيد المراجعة' : 'نشط',
                'notes' => $issues > 0 ? "يوجد {$issues} حالة موقوفة" : 'تشغيل مستقر',
                'output' => $lineOutputs[$stageKey] ?? 0,
                'issues' => $issues,
            ];
        }

        Carbon::setLocale(app()->getLocale());

        return view('dashboard', compact(
            'notifications',
            'unreadCount',
            'dashboardStats',
            'weeklyProduction',
            'statusSegments',
            'productionLines'
        ));
    }
}
