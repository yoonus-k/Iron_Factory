<?php

namespace App\Services;

use App\Models\StageSuspension;
use App\Models\User;
use App\Helpers\SystemSettingsHelper;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WasteCheckService
{
    /**
     * Check waste percentage and suspend stage if exceeded
     * 
     * @param int $stageNumber المرحلة (1 أو 2)
     * @param string $batchBarcode باركود الدفعة
     * @param int|null $batchId معرف الدفعة
     * @param float $inputWeight الوزن المدخل
     * @param float $outputWeight الوزن الناتج
     * @return array
     */
    public static function checkAndSuspend(
        int $stageNumber, 
        string $batchBarcode, 
        ?int $batchId, 
        float $inputWeight, 
        float $outputWeight
    ): array {
        $inputWeight = max(0, $inputWeight);
        $outputWeight = max(0, $outputWeight);
        $currentWaste = max(0, $inputWeight - $outputWeight);

        $existingTotals = self::getExistingStageTotals($stageNumber, $batchBarcode);

        $totalInput = $existingTotals['input'] + $inputWeight;
        $totalOutput = $existingTotals['output'] + $outputWeight;
        $totalWaste = $existingTotals['waste'] + $currentWaste;

        if ($totalInput <= 0) {
            return [
                'success' => true,
                'suspended' => false,
                'message' => '✅ لا توجد بيانات كافية لاحتساب نسبة الهدر حتى الآن',
                'data' => [
                    'waste_weight' => 0,
                    'waste_percentage' => 0,
                    'allowed_percentage' => SystemSettingsHelper::getStageWastePercentage($stageNumber),
                    'difference' => 0,
                    'current_waste' => round($currentWaste, 2),
                    'current_input_weight' => round($inputWeight, 3),
                    'current_output_weight' => round($outputWeight, 3),
                    'total_input_weight' => round($totalInput, 3),
                    'total_output_weight' => round($totalOutput, 3),
                ],
            ];
        }

        // فحص نسبة الهدر باستخدام البيانات التراكمية
        $check = SystemSettingsHelper::checkWastePercentage($stageNumber, $totalInput, $totalOutput);
        $check = array_merge($check, [
            'waste_weight' => round($totalWaste, 2),
            'current_waste' => round($currentWaste, 2),
            'previous_waste' => round($existingTotals['waste'], 2),
            'current_input_weight' => round($inputWeight, 3),
            'current_output_weight' => round($outputWeight, 3),
            'total_input_weight' => round($totalInput, 3),
            'total_output_weight' => round($totalOutput, 3),
        ]);

        // إذا تجاوز النسبة المسموح بها
        if ($check['exceeded'] && $check['should_suspend']) {
            try {
                DB::beginTransaction();

                // إنشاء سجل إيقاف المرحلة
                $suspension = StageSuspension::create([
                    'stage_number' => $stageNumber,
                    'batch_barcode' => $batchBarcode,
                    'batch_id' => $batchId,
                    'input_weight' => $inputWeight,
                    'output_weight' => $outputWeight,
                    'waste_weight' => $check['waste_weight'],
                    'waste_percentage' => $check['waste_percentage'],
                    'allowed_percentage' => $check['allowed_percentage'],
                    'status' => 'suspended',
                    'suspension_reason' => sprintf(
                        'تم إيقاف المرحلة تلقائياً بسبب تجاوز نسبة الهدر المسموح بها. النسبة الفعلية: %s%% - المسموح به: %s%%',
                        number_format($check['waste_percentage'], 2),
                        number_format($check['allowed_percentage'], 2)
                    ),
                    'suspended_by' => auth()->id(),
                    'suspended_at' => now(),
                    'additional_data' => [
                        'current_input_weight' => $check['current_input_weight'],
                        'current_output_weight' => $check['current_output_weight'],
                        'total_input_weight' => $check['total_input_weight'],
                        'total_output_weight' => $check['total_output_weight'],
                        'difference' => $check['difference'],
                    ],
                ]);

                // إرسال تنبيهات
                if ($check['should_alert']) {
                    self::sendWasteAlerts($suspension);
                }

                DB::commit();

                return [
                    'success' => false,
                    'suspended' => true,
                    'suspension_id' => $suspension->id,
                    'message' => sprintf(
                        '⚠️ تم إيقاف المرحلة %s تلقائياً بسبب تجاوز نسبة الهدر المسموح بها (%s%%). يرجى مراجعة الإدارة للموافقة على الاستئناف.',
                        $suspension->getStageName(),
                        number_format($check['waste_percentage'], 2)
                    ),
                    'data' => $check,
                ];

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error suspending stage: ' . $e->getMessage());
                
                return [
                    'success' => false,
                    'suspended' => false,
                    'error' => 'حدث خطأ أثناء إيقاف المرحلة',
                    'message' => $e->getMessage(),
                ];
            }
        }

        // النسبة ضمن الحد المسموح
        return [
            'success' => true,
            'suspended' => false,
            'message' => '✅ نسبة الهدر ضمن الحد المسموح به',
            'data' => $check,
        ];
    }

    /**
     * Get accumulated waste stats for the batch/stage before applying the current transaction
     */
    private static function getExistingStageTotals(int $stageNumber, string $batchBarcode): array
    {
        $totals = [
            'input' => 0.0,
            'output' => 0.0,
            'waste' => 0.0,
        ];

        if ($stageNumber === 1) {
            // في stage1_stands:
            // weight = الوزن الكلي (يشمل الاستاند)
            // remaining_weight = الوزن الصافي (net_weight)
            // waste = وزن الهدر
            // الوزن المادي (input) = remaining_weight + waste
            $stats = DB::table('stage1_stands')
                ->where('parent_barcode', $batchBarcode)
                ->selectRaw('
                    COALESCE(SUM(remaining_weight), 0) as total_output, 
                    COALESCE(SUM(waste), 0) as total_waste,
                    COALESCE(SUM(remaining_weight + waste), 0) as total_material_weight
                ')
                ->first();

            if ($stats) {
                $totals['output'] = (float) $stats->total_output;
                $totals['waste'] = (float) $stats->total_waste;
                $totals['input'] = (float) $stats->total_material_weight;
            }
        } elseif ($stageNumber === 2) {
            if (Schema::hasTable('stage2_processed')) {
                $stats = DB::table('stage2_processed')
                    ->where('parent_barcode', $batchBarcode)
                    ->selectRaw('COALESCE(SUM(output_weight), 0) as total_output, COALESCE(SUM(waste), 0) as total_waste, COALESCE(SUM(input_weight), 0) as total_input')
                    ->first();

                if ($stats) {
                    $totals['output'] = (float) $stats->total_output;
                    $totals['waste'] = (float) $stats->total_waste;
                    $totals['input'] = (float) $stats->total_input;
                }
            }
        }

        return $totals;
    }

    /**
     * Send alerts to admins and users with permission
     */
    private static function sendWasteAlerts(StageSuspension $suspension)
    {
        $notificationService = app(NotificationService::class);

        // إرسال للمستخدمين الذين لديهم صلاحية الموافقة على إيقاف المراحل
        $users = User::whereHas('roleRelation', function($q) {
            $q->whereHas('permissions', function($q2) {
                $q2->where('name', 'STAGE_SUSPENSION_APPROVE');
            });
        })->get();

        // إذا لم يوجد مستخدمين، أرسل للأدمن
        if ($users->isEmpty()) {
            $users = User::whereHas('roleRelation', function($q) {
                $q->where('role_code', 'ADMIN');
            })->get();
        }

        foreach ($users as $user) {
            try {
                $notificationService->notifyStageSuspensionTriggered($user, $suspension);
            } catch (\Exception $e) {
                \Log::error('Error sending waste alert to user ' . $user->id . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Check if stage is currently suspended
     */
    public static function isStageSuspended(string $batchBarcode): bool
    {
        return StageSuspension::where('batch_barcode', $batchBarcode)
            ->where('status', 'suspended')
            ->exists();
    }

    /**
     * Get active suspension for batch
     */
    public static function getActiveSuspension(string $batchBarcode): ?StageSuspension
    {
        return StageSuspension::where('batch_barcode', $batchBarcode)
            ->where('status', 'suspended')
            ->first();
    }
}
