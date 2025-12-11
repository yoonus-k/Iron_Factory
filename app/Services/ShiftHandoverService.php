<?php

namespace App\Services;

use App\Models\Stage1Stand;
use App\Models\Stage2Processed;
use App\Models\Stage3Coil;
use App\Models\Stage4Box;
use App\Models\ShiftHandover;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ShiftHandoverService
{
    /**
     * جمع جميع الأشغال المعلقة للعامل في مرحلة معينة
     */
    public function collectPendingWork(int $userId, int $stageNumber): array
    {
        try {
            $items = match($stageNumber) {
                1 => $this->collectStage1Items($userId),
                2 => $this->collectStage2Items($userId),
                3 => $this->collectStage3Items($userId),
                4 => $this->collectStage4Items($userId),
                default => [],
            };

            return $items;
        } catch (\Exception $e) {
            Log::error('Error collecting pending work', [
                'user_id' => $userId,
                'stage' => $stageNumber,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * جمع الأشغال المعلقة من المرحلة الأولى
     */
    private function collectStage1Items(int $userId): array
    {
        return Stage1Stand::where('created_by', $userId)
            ->whereIn('status', [Stage1Stand::STATUS_CREATED, Stage1Stand::STATUS_IN_PROCESS])
            ->whereNull('completed_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'stage' => 1,
                    'barcode' => $item->barcode,
                    'type' => 'Stage1Stand',
                    'model_id' => $item->id,
                    'status' => $item->status,
                    'status_name' => $this->getStatusName($item->status, 1),
                    'progress' => $this->calculateStage1Progress($item),
                    'details' => [
                        'stand_number' => $item->stand_number,
                        'stand_number_en' => $item->stand_number_en,
                        'wire_size' => $item->wire_size,
                        'wire_size_en' => $item->wire_size_en,
                        'weight' => $item->weight,
                        'remaining_weight' => $item->remaining_weight,
                        'waste' => $item->waste,
                        'material' => $item->material ? $item->material->name : null,
                    ],
                    'created_at' => $item->created_at->format('Y-m-d H:i'),
                    'duration' => $item->created_at->diffForHumans(),
                ];
            })->toArray();
    }

    /**
     * جمع الأشغال المعلقة من المرحلة الثانية
     */
    private function collectStage2Items(int $userId): array
    {
        return Stage2Processed::where('created_by', $userId)
            ->whereIn('status', [Stage2Processed::STATUS_STARTED, Stage2Processed::STATUS_IN_PROGRESS])
            ->whereNull('completed_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'stage' => 2,
                    'barcode' => $item->barcode,
                    'type' => 'Stage2Processed',
                    'model_id' => $item->id,
                    'status' => $item->status,
                    'status_name' => $this->getStatusName($item->status, 2),
                    'progress' => $this->calculateStage2Progress($item),
                    'details' => [
                        'process_details' => $item->process_details,
                        'process_details_en' => $item->process_details_en,
                        'input_weight' => $item->input_weight,
                        'output_weight' => $item->output_weight,
                        'remaining_weight' => $item->remaining_weight,
                        'waste' => $item->waste,
                        'parent_barcode' => $item->parent_barcode,
                    ],
                    'created_at' => $item->created_at->format('Y-m-d H:i'),
                    'duration' => $item->created_at->diffForHumans(),
                ];
            })->toArray();
    }

    /**
     * جمع الأشغال المعلقة من المرحلة الثالثة
     */
    private function collectStage3Items(int $userId): array
    {
        return Stage3Coil::where('created_by', $userId)
            ->whereIn('status', [Stage3Coil::STATUS_CREATED, Stage3Coil::STATUS_IN_PROCESS])
            ->whereNull('completed_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'stage' => 3,
                    'barcode' => $item->barcode,
                    'type' => 'Stage3Coil',
                    'model_id' => $item->id,
                    'status' => $item->status,
                    'status_name' => $this->getStatusName($item->status, 3),
                    'progress' => $this->calculateStage3Progress($item),
                    'details' => [
                        'coil_number' => $item->coil_number,
                        'coil_number_en' => $item->coil_number_en,
                        'wire_size' => $item->wire_size,
                        'wire_size_en' => $item->wire_size_en,
                        'color' => $item->color,
                        'color_en' => $item->color_en,
                        'base_weight' => $item->base_weight,
                        'dye_weight' => $item->dye_weight,
                        'plastic_weight' => $item->plastic_weight,
                        'total_weight' => $item->total_weight,
                        'dye_type' => $item->dye_type,
                        'plastic_type' => $item->plastic_type,
                        'waste' => $item->waste,
                    ],
                    'created_at' => $item->created_at->format('Y-m-d H:i'),
                    'duration' => $item->created_at->diffForHumans(),
                ];
            })->toArray();
    }

    /**
     * جمع الأشغال المعلقة من المرحلة الرابعة
     */
    private function collectStage4Items(int $userId): array
    {
        return Stage4Box::where('created_by', $userId)
            ->whereIn('status', [Stage4Box::STATUS_PACKING])
            ->whereNull('packed_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'stage' => 4,
                    'barcode' => $item->barcode,
                    'type' => 'Stage4Box',
                    'model_id' => $item->id,
                    'status' => $item->status,
                    'status_name' => $this->getStatusName($item->status, 4),
                    'progress' => $this->calculateStage4Progress($item),
                    'details' => [
                        'packaging_type' => $item->packaging_type,
                        'packaging_type_en' => $item->packaging_type_en,
                        'coils_count' => $item->coils_count,
                        'total_weight' => $item->total_weight,
                        'waste' => $item->waste,
                        'customer_info' => $item->customer_info,
                    ],
                    'created_at' => $item->created_at->format('Y-m-d H:i'),
                    'duration' => $item->created_at->diffForHumans(),
                ];
            })->toArray();
    }

    /**
     * حساب نسبة التقدم - المرحلة الأولى
     */
    private function calculateStage1Progress($item): int
    {
        if (!$item->weight || $item->weight == 0) {
            return 0;
        }

        // إذا تم استهلاك جزء من الوزن
        if ($item->remaining_weight < $item->weight) {
            $consumed = $item->weight - $item->remaining_weight;
            return (int) (($consumed / $item->weight) * 100);
        }

        // إذا بدأ العمل لكن لم يتم استهلاك شيء
        if ($item->status === Stage1Stand::STATUS_IN_PROCESS) {
            return 10;
        }

        return 5; // بدأ فقط
    }

    /**
     * حساب نسبة التقدم - المرحلة الثانية
     */
    private function calculateStage2Progress($item): int
    {
        if (!$item->input_weight || $item->input_weight == 0) {
            return 0;
        }

        if ($item->output_weight > 0) {
            return (int) (($item->output_weight / $item->input_weight) * 100);
        }

        if ($item->status === Stage2Processed::STATUS_IN_PROGRESS) {
            return 20;
        }

        return 5;
    }

    /**
     * حساب نسبة التقدم - المرحلة الثالثة
     */
    private function calculateStage3Progress($item): int
    {
        $totalSteps = 4; // الوزن الأساسي، الصبغة، البلاستيك، الإكمال
        $completedSteps = 0;

        if ($item->base_weight > 0) $completedSteps++;
        if ($item->dye_weight > 0) $completedSteps++;
        if ($item->plastic_weight > 0) $completedSteps++;
        if ($item->total_weight > 0) $completedSteps++;

        return (int) (($completedSteps / $totalSteps) * 100);
    }

    /**
     * حساب نسبة التقدم - المرحلة الرابعة
     */
    private function calculateStage4Progress($item): int
    {
        if (!$item->coils_count || $item->coils_count == 0) {
            return 10; // بدأ التعبئة
        }

        // عدد الملفات الفعلية المضافة
        $actualCoils = $item->boxCoils()->count();

        if ($actualCoils > 0 && $item->coils_count > 0) {
            return (int) (($actualCoils / $item->coils_count) * 100);
        }

        return 20;
    }

    /**
     * الحصول على اسم الحالة
     */
    private function getStatusName(string $status, int $stage): string
    {
        $statusMap = [
            1 => [
                'created' => 'تم الإنشاء',
                'in_process' => 'قيد التنفيذ',
                'completed' => 'مكتمل',
            ],
            2 => [
                'started' => 'بدأ',
                'in_progress' => 'قيد التنفيذ',
                'completed' => 'مكتمل',
            ],
            3 => [
                'created' => 'تم الإنشاء',
                'in_process' => 'قيد التنفيذ',
                'completed' => 'مكتمل',
            ],
            4 => [
                'packing' => 'قيد التعبئة',
                'packed' => 'تم التعبئة',
                'shipped' => 'تم الشحن',
            ],
        ];

        return $statusMap[$stage][$status] ?? $status;
    }

    /**
     * البحث عن عامل الشفت التالي في نفس المرحلة
     */
    public function findNextShiftWorker(int $currentUserId, int $stageNumber, $currentShiftType): ?User
    {
        // تحديد نوع الشفت التالي
        $nextShiftType = $this->getNextShiftType($currentShiftType);

        // البحث عن شفت نشط في نفس المرحلة
        $nextShift = \App\Models\ShiftAssignment::where('stage_number', $stageNumber)
            ->where('shift_type', $nextShiftType)
            ->where('status', 'active')
            ->where('is_active', true)
            ->whereDate('shift_date', now()->toDateString())
            ->first();

        if ($nextShift) {
            return $nextShift->user;
        }

        // إذا لم يوجد شفت نشط، ابحث عن المشرف في هذه المرحلة
        return User::whereHas('worker', function($q) use ($stageNumber) {
            $q->where('position', 'supervisor')
              ->whereJsonContains('allowed_stages', $stageNumber);
        })->where('is_active', true)->first();
    }

    /**
     * تحديد نوع الشفت التالي
     */
    private function getNextShiftType($currentType): string
    {
        return match($currentType) {
            'morning' => 'evening',
            'evening' => 'morning',
            default => 'morning',
        };
    }

    /**
     * تحويل الأشغال المعلقة للعامل الجديد
     */
    public function transferPendingWork(array $items, int $toUserId, int $stageNumber): bool
    {
        try {
            foreach ($items as $item) {
                $modelClass = "App\\Models\\{$item['type']}";

                if (class_exists($modelClass)) {
                    $model = $modelClass::find($item['model_id']);

                    if ($model) {
                        // تحديث العامل المسؤول
                        $model->created_by = $toUserId;
                        $model->save();

                        Log::info('Work item transferred', [
                            'barcode' => $item['barcode'],
                            'from_stage' => $item['stage'],
                            'to_user' => $toUserId
                        ]);
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error transferring pending work', [
                'to_user' => $toUserId,
                'stage' => $stageNumber,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * إحصائيات الأشغال المعلقة للعامل
     */
    public function getPendingWorkStats(int $userId): array
    {
        return [
            'stage_1' => Stage1Stand::where('created_by', $userId)
                ->whereIn('status', [Stage1Stand::STATUS_CREATED, Stage1Stand::STATUS_IN_PROCESS])
                ->whereNull('completed_at')
                ->count(),
            'stage_2' => Stage2Processed::where('created_by', $userId)
                ->whereIn('status', [Stage2Processed::STATUS_STARTED, Stage2Processed::STATUS_IN_PROGRESS])
                ->whereNull('completed_at')
                ->count(),
            'stage_3' => Stage3Coil::where('created_by', $userId)
                ->whereIn('status', [Stage3Coil::STATUS_CREATED, Stage3Coil::STATUS_IN_PROCESS])
                ->whereNull('completed_at')
                ->count(),
            'stage_4' => Stage4Box::where('created_by', $userId)
                ->where('status', Stage4Box::STATUS_PACKING)
                ->whereNull('packed_at')
                ->count(),
        ];
    }
}
