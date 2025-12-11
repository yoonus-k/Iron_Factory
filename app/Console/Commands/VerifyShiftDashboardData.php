<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Manufacturing\Http\Controllers\ShiftDashboardController;
use Carbon\Carbon;

class VerifyShiftDashboardData extends Command
{
    protected $signature = 'shift:verify-data {date?} {shift?}';
    protected $description = 'التحقق من بيانات تقرير الورديات';

    public function handle()
    {
        $date = $this->argument('date') ?? now()->format('Y-m-d');
        $shift = $this->argument('shift') ?? 'evening';

        $this->info('🔍 جاري التحقق من بيانات الوردية...');
        $this->newLine();

        $controller = app(ShiftDashboardController::class);

        try {
            // اختبر جلب الوقت
            $this->info('✓ اختبار: getShiftTimeRange');
            $timeRange = $controller->getShiftTimeRange($date, $shift);
            $this->line("  من: {$timeRange['start']}");
            $this->line("  إلى: {$timeRange['end']}");
            $this->newLine();

            // اختبر جلب الملخص
            $this->info('✓ اختبار: getShiftSummary');
            $summary = $controller->getShiftSummary($date, $shift);
            $this->table(
                ['الخاصية', 'القيمة'],
                [
                    ['إجمالي القطع', $summary['total_items']],
                    ['الإنتاج (كجم)', $summary['total_output_kg']],
                    ['الهدر (كجم)', $summary['total_waste_kg']],
                    ['نسبة الهدر', $summary['waste_percentage'] . '%'],
                    ['الكفاءة', $summary['efficiency'] . '%'],
                    ['عدد العمال', $summary['workers_count']],
                ]
            );
            $this->newLine();

            // اختبر جلب أفضل أداء
            $this->info('✓ اختبار: getTopPerformers');
            $topPerformers = $controller->getTopPerformers($date, $shift, 5);
            if (count($topPerformers) > 0) {
                $data = [];
                foreach ($topPerformers as $index => $performer) {
                    $data[] = [
                        $index + 1,
                        $performer['worker_name'],
                        $performer['items'],
                        $performer['efficiency'] . '%',
                    ];
                }
                $this->table(['الترتيب', 'الاسم', 'القطع', 'الكفاءة'], $data);
            } else {
                $this->warn('  لا توجد بيانات أداء');
            }
            $this->newLine();

            // اختبر جلب حضور العمال
            $this->info('✓ اختبار: getWorkerAttendance');
            $attendance = $controller->getWorkerAttendance($date, $shift);
            $this->line("  عدد العمال: " . count($attendance));
            if (count($attendance) > 0) {
                $this->table(
                    ['الاسم', 'الكود', 'القطع', 'الكفاءة'],
                    array_map(function($w) {
                        return [
                            $w['worker_name'],
                            $w['worker_code'],
                            $w['total_items'],
                            number_format($w['efficiency'], 1) . '%',
                        ];
                    }, array_slice($attendance, 0, 5))
                );
            }
            $this->newLine();

            // اختبر جلب كفاءة المراحل
            $this->info('✓ اختبار: getStageEfficiencyDetails');
            $stageEfficiency = $controller->getStageEfficiencyDetails($date, $shift);
            $data = [];
            foreach ($stageEfficiency as $stage) {
                $data[] = [
                    $stage['name'],
                    $stage['items'],
                    $stage['efficiency'] . '%',
                    $stage['waste_pct'] . '%',
                ];
            }
            $this->table(['المرحلة', 'القطع', 'الكفاءة', 'الهدر'], $data);
            $this->newLine();

            // اختبر جلب تسليم الورديات
            $this->info('✓ اختبار: getShiftHandovers');
            $handovers = $controller->getShiftHandovers($date, $shift);
            $this->line("  عدد التسليمات: " . count($handovers));
            if (count($handovers) > 0) {
                $this->table(
                    ['المرحلة', 'من', 'إلى', 'معتمد'],
                    array_map(function($h) {
                        return [
                            $h['stage_name'],
                            $h['from_user'],
                            $h['to_user'],
                            $h['supervisor_approved'] ? '✓' : '✗',
                        ];
                    }, array_slice($handovers, 0, 5))
                );
            }
            $this->newLine();

            // اختبر جلب الفرق النشطة
            $this->info('✓ اختبار: getActiveTeams');
            $teams = $controller->getActiveTeams($date, $shift);
            $this->line("  عدد الفرق النشطة: " . count($teams));
            if (count($teams) > 0) {
                $this->table(
                    ['اسم الفريق', 'الأعضاء النشطين', 'الإنتاج الكلي'],
                    array_map(function($t) {
                        return [
                            $t['team_name'],
                            $t['active_members'] . '/' . $t['total_members'],
                            $t['total_production'],
                        ];
                    }, array_slice($teams, 0, 5))
                );
            }
            $this->newLine();

            // اختبر جلب المشاكل
            $this->info('✓ اختبار: getShiftIssues');
            $issues = $controller->getShiftIssues($date, $shift);
            $this->line("  عدد المشاكل: " . count($issues));
            if (count($issues) > 0) {
                foreach ($issues as $issue) {
                    $severity = $issue['severity'] == 'warning' ? '⚠️' : 'ℹ️';
                    $this->warn("  {$severity} {$issue['message']}");
                }
            } else {
                $this->info('  ✓ لا توجد مشاكل');
            }
            $this->newLine();

            $this->info('✅ تم التحقق من جميع البيانات بنجاح!');

        } catch (\Exception $e) {
            $this->error('❌ حدث خطأ: ' . $e->getMessage());
            $this->line('السطر: ' . $e->getLine());
            $this->line('الملف: ' . $e->getFile());
            return 1;
        }

        return 0;
    }
}
