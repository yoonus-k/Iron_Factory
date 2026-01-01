<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductionConfirmation;
use App\Models\MaterialBatch;
use App\Models\ProductTracking;
use App\Models\WorkerStageHistory;
use App\Models\Notification;
use Carbon\Carbon;

class StageWorkerDashboardController extends Controller
{
    /**
     * عرض لوحة التحكم الرئيسية لعامل المرحلة
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $roleCode = $user->role->role_code ?? null;

        // تحديد رقم المرحلة من role_code
        $stageNumber = $this->getStageNumber($roleCode);

        if (!$stageNumber) {
            return redirect()->route('dashboard')->with('error', 'هذه الصفحة مخصصة لعمال المراحل فقط');
        }

        // جلب التأكيدات المنتظرة للعامل
        $pendingConfirmations = $this->getPendingConfirmations($user->id);

        // جلب الإشعارات الخاصة بالمرحلة
        $notifications = $this->getStageNotifications($stageNumber, $user->id);

        // إحصائيات سريعة
        $stats = $this->getStageStats($stageNumber, $user->id);

        return view('stage-worker.dashboard', compact(
            'pendingConfirmations',
            'notifications',
            'stats',
            'stageNumber'
        ));
    }

    /**
     * API endpoint لجلب التحديثات الجديدة (AJAX)
     */
    public function getUpdates(Request $request)
    {
        $user = auth()->user();
        $roleCode = $user->role->role_code ?? null;
        $stageNumber = $this->getStageNumber($roleCode);

        if (!$stageNumber) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $lastCheck = $request->input('last_check', now()->subMinutes(5));

        // جلب التأكيدات الجديدة
        $newConfirmations = ProductionConfirmation::where('assigned_to', $user->id)
            ->where('status', 'pending')
            ->where('created_at', '>', $lastCheck)
            ->with([
                'batch.material',
                'deliveryNote.material',
                'deliveryNote.materialBatch',
                'deliveryNote.productTracking',
                'assignedUser',
                'workerStageHistory'
            ])
            ->get();

        $newConfirmations->each(function ($confirmation) {
            $this->prepareConfirmationForDisplay($confirmation);
        });

        $pendingConfirmations = $this->getPendingConfirmations($user->id);

        // جلب الإشعارات الجديدة
        $newNotifications = $this->getStageNotifications($stageNumber, $user->id, $lastCheck);

        // إحصائيات محدثة
        $stats = $this->getStageStats($stageNumber, $user->id);

        return response()->json([
            'success' => true,
            'new_confirmations' => $newConfirmations,
            'new_notifications' => $newNotifications,
            'pending_confirmations' => $pendingConfirmations,
            'stats' => $stats,
            'has_updates' => $newConfirmations->count() > 0 || $newNotifications->count() > 0,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * تأكيد استلام سريع من لوحة التحكم
     */
    public function quickConfirm(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $confirmation = ProductionConfirmation::findOrFail($id);

            // التحقق من أن الحالة pending
            if ($confirmation->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'هـــذا الطلب تم معالجته بالفعل'], 400);
            }

            // تأكيد الاستلام باستخدام Model method
            $confirmation->confirm(
                auth()->id(),
                null,
                $request->input('notes')
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تأكيد الاستلام بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Quick confirm error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * رفض استلام سريع من لوحة التحكم
     */
    public function quickReject(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $confirmation = ProductionConfirmation::findOrFail($id);

            // التحقق من أن الحالة pending
            if ($confirmation->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'هـــذا الطلب تم معالجته بالفعل'], 400);
            }

            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:500'
            ], [
                'rejection_reason.required' => 'يجب إدخال سبب الرفض'
            ]);

            // رفض الاستلام باستخدام Model method
            $confirmation->reject(auth()->id(), $validated['rejection_reason']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم رفض الاستلام بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Quick reject error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديد رقم المرحلة من role_code
     */
    private function getStageNumber($roleCode)
    {
        if (!$roleCode)
            return null;

        $stageMapping = [
            'STAGE1_WORKER' => 1,
            'STAGE2_WORKER' => 2,
            'STAGE3_WORKER' => 3,
            'STAGE4_WORKER' => 4,
        ];

        return $stageMapping[$roleCode] ?? null;
    }

    /**
     * جلب التأكيدات المنتظرة للعامل
     */
    private function getPendingConfirmations($userId)
    {
        $confirmations = ProductionConfirmation::where('assigned_to', $userId)
            ->where('status', 'pending')
            ->with([
                'batch.material',
                'deliveryNote.material',
                'deliveryNote.materialBatch',
                'deliveryNote.productTracking',
                'assignedUser',
                'workerStageHistory'
            ])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Load stage records based on stage_type
        $confirmations->each(function ($confirmation) {
            $this->prepareConfirmationForDisplay($confirmation);
        });

        return $confirmations;
    }

    /**
     * جلب الإشعارات الخاصة بالمرحلة
     */
    private function getStageNotifications($stageNumber, $userId, $since = null)
    {
        // جلب إشعارات المستخدم من جدول notifications
        $query = Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc');

        if ($since) {
            $query->where('created_at', '>', $since);
        }

        return $query->limit(10)
            ->get()
            ->map(function ($notification) {
                $data = json_decode($notification->data, true) ?? [];

                // تحديد الـ URL بناءً على البيانات المتاحة
                $url = '#';
                if (!empty($data['barcode'])) {
                    $url = route('manufacturing.pending-production.history', $data['barcode']);
                } elseif (!empty($data['url'])) {
                    $url = $data['url'];
                }

                return [
                    'id' => $notification->id,
                    'type' => $this->mapNotificationType($notification->type),
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'time' => $notification->created_at->diffForHumans(),
                    'is_read' => $notification->read_at !== null,
                    'url' => $url
                ];
            });
    }

    /**
     * تحويل نوع الإشعار إلى نوع CSS
     */
    private function mapNotificationType($type)
    {
        $typeMap = [
            'work_assigned' => 'success',
            'work_reassigned' => 'warning',
            'shift_transfer' => 'info',
            'work_completed' => 'success',
            'work_rejected' => 'danger',
        ];

        return $typeMap[$type] ?? 'info';
    }

    /**
     * جلب إحصائيات المرحلة
     */
    private function getStageStats($stageNumber, $userId)
    {
        $today = Carbon::today();

        return [
            'pending_confirmations' => ProductionConfirmation::where('assigned_to', $userId)
                ->where('status', 'pending')
                ->count(),

            'confirmed_today' => ProductionConfirmation::where('assigned_to', $userId)
                ->where('status', 'confirmed')
                ->whereDate('confirmed_at', $today)
                ->count(),

            'rejected_today' => ProductionConfirmation::where('assigned_to', $userId)
                ->where('status', 'rejected')
                ->whereDate('rejected_at', $today)
                ->count(),

            'total_this_week' => ProductionConfirmation::where('assigned_to', $userId)
                ->whereIn('status', ['confirmed', 'rejected'])
                ->whereBetween('updated_at', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()])
                ->count(),
        ];
    }

    /**
     * تحديد نوع الإشعار بناءً على الحالة
     */
    private function getNotificationType($status)
    {
        return [
            'pending' => 'warning',
            'confirmed' => 'success',
            'rejected' => 'danger'
        ][$status] ?? 'info';
    }

    /**
     * عنوان الإشعار
     */
    private function getNotificationTitle($confirmation)
    {
        $titles = [
            'pending' => 'تأكيد استلام منتظر',
            'confirmed' => 'تم تأكيد الاستلام',
            'rejected' => 'تم رفض الاستلام'
        ];

        return $titles[$confirmation->status] ?? 'إشعار';
    }

    /**
     * رسالة الإشعار
     */
    private function getNotificationMessage($confirmation)
    {
        $materialName = $confirmation->batch?->material?->name ?? 'غير محدد';
        $batchCode = $confirmation->batch?->batch_code ?? 'غير محدد';

        $messages = [
            'pending' => "يوجد تأكيد استلام منتظر للمادة {$materialName} - دفعة {$batchCode}",
            'confirmed' => "تم تأكيد استلام المادة {$materialName} - دفعة {$batchCode}",
            'rejected' => "تم رفض استلام المادة {$materialName} - دفعة {$batchCode}"
        ];

        return $messages[$confirmation->status] ?? 'تحديث جديد';
    }

    /**
     * تجهيز بيانات التأكيد قبل إرسالها للواجهة بما في ذلك الباركود والوزن الصحيحين
     */
    private function prepareConfirmationForDisplay($confirmation)
    {
        $metadata = $confirmation->metadata ?? [];

        if ($confirmation->stage_record_id && $confirmation->stage_type) {
            $stageRecord = $confirmation->loadStageRecord();

            if ($stageRecord) {
                $metadata['stage_barcode'] = $stageRecord->barcode ?? ($metadata['stage_barcode'] ?? null);
                $metadata['stage_weight'] = $stageRecord->remaining_weight
                    ?? $stageRecord->final_weight
                    ?? ($metadata['stage_weight'] ?? null);

                if (!isset($metadata['stage_name'])) {
                    $metadata['stage_name'] = $stageRecord->stage_name ?? null;
                }
            }
        }

        $confirmation->setAttribute('metadata', $metadata);
        $confirmation->setAttribute(
            'resolved_barcode',
            $metadata['stage_barcode']
            ?? $confirmation->barcode
            ?? $confirmation->deliveryNote?->production_barcode
            ?? $confirmation->deliveryNote?->materialBatch?->batch_code
            ?? $confirmation->batch?->production_barcode
            ?? $confirmation->batch?->batch_code
        );

        $confirmation->setAttribute(
            'resolved_weight',
            $metadata['stage_weight']
            ?? $confirmation->actual_received_quantity
            ?? $confirmation->deliveryNote?->quantity_used
            ?? $confirmation->deliveryNote?->quantity
            ?? $confirmation->batch?->initial_quantity
        );

        $displayWeight = $this->resolveFinalWeight($confirmation, $metadata);

        $confirmation->setAttribute('display_material_name', $this->resolveMaterialName($confirmation, $metadata));
        $confirmation->setAttribute('display_stage_label', $this->resolveStageLabel($confirmation, $metadata));
        $confirmation->setAttribute('display_production_barcode', $this->resolveProductionBarcode($confirmation, $metadata));
        $confirmation->setAttribute('display_confirmation_type_label', $this->getConfirmationTypeLabel($confirmation->confirmation_type));
        $confirmation->setAttribute('display_final_weight', $displayWeight);
        $confirmation->setAttribute(
            'display_final_weight_label',
            $displayWeight ? number_format((float) $displayWeight, 2) . ' كجم' : 'غير محدد'
        );
        $confirmation->setAttribute('display_reason_text', $metadata['reason'] ?? null);

        return $confirmation;
    }

    private function resolveMaterialName($confirmation, array $metadata)
    {
        return $confirmation->deliveryNote?->material?->name_ar
            ?? $confirmation->deliveryNote?->material?->name
            ?? $confirmation->batch?->material?->name_ar
            ?? $confirmation->batch?->material?->name
            ?? ($metadata['stage_name'] ?? 'غير محدد');
    }

    private function resolveStageLabel($confirmation, array $metadata)
    {
        if (!empty($metadata['stage_name'])) {
            return $metadata['stage_name'];
        }

        if (!empty($confirmation->stage_type)) {
            $stageTypeName = $this->mapStageTypeToDisplayName($confirmation->stage_type);
            if ($stageTypeName) {
                return $stageTypeName;
            }
        }

        if (!empty($confirmation->stage_code)) {
            $stageCodeName = $this->mapStageCodeToDisplayName($confirmation->stage_code);
            if ($stageCodeName) {
                return $stageCodeName;
            }
        }

        return 'غير محدد';
    }

    private function resolveProductionBarcode($confirmation, array $metadata)
    {
        return $metadata['stage_barcode']
            ?? $confirmation->barcode
            ?? $confirmation->batch?->production_barcode
            ?? $confirmation->batch?->batch_code
            ?? $confirmation->deliveryNote?->production_barcode
            ?? $confirmation->deliveryNote?->materialBatch?->batch_code
            ?? ($metadata['barcode'] ?? 'غير محدد');
    }

    private function resolveFinalWeight($confirmation, array $metadata)
    {
        $candidates = [
            $confirmation->resolved_weight,
            $metadata['stage_weight'] ?? null,
            $confirmation->actual_received_quantity,
            $confirmation->deliveryNote?->quantity_used,
            $confirmation->deliveryNote?->materialBatch?->initial_quantity,
            $confirmation->deliveryNote?->materialBatch?->available_quantity,
            $confirmation->batch?->initial_quantity,
            $confirmation->batch?->available_quantity,
            $confirmation->deliveryNote?->quantity,
            $confirmation->deliveryNote?->actual_weight,
            $metadata['weight'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (!is_null($candidate) && (float) $candidate > 0) {
                return (float) $candidate;
            }
        }

        return null;
    }

    private function mapStageCodeToDisplayName(?string $stageCode)
    {
        if (!$stageCode) {
            return null;
        }

        $mapping = [
            'stage_1' => 'المرحلة الأولى',
            'stage_2' => 'المرحلة الثانية',
            'stage_3' => 'المرحلة الثالثة',
            'stage_4' => 'المرحلة الرابعة',
            'warehouse' => 'المستودع',
        ];

        return $mapping[$stageCode] ?? null;
    }

    private function mapStageTypeToDisplayName(?string $stageType)
    {
        if (!$stageType) {
            return null;
        }

        $mapping = [
            'stage1_stands' => 'المرحلة الأولى - الاستاند',
            'stage2_processed' => 'المرحلة الثانية - المعالج',
            'stage3_coils' => 'المرحلة الثالثة - اللفائف',
            'stage4_boxes' => 'المرحلة الرابعة - الصناديق',
            'warehouse' => 'المستودع',
        ];

        return $mapping[$stageType] ?? null;
    }

    private function getConfirmationTypeLabel(?string $type)
    {
        return match ($type) {
            'reassignment' => 'إعادة إسناد',
            'shift_transfer' => 'نقل للوردية التالية',
            default => 'نقل المواد',
        };
    }
}
