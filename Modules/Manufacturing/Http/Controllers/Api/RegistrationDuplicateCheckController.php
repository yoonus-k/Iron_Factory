<?php

namespace Modules\Manufacturing\Http\Controllers\Api;

use App\Models\DeliveryNote;
use App\Services\DuplicatePreventionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class RegistrationDuplicateCheckController extends Controller
{
    protected $duplicateService;

    public function __construct(DuplicatePreventionService $duplicateService)
    {
        $this->duplicateService = $duplicateService;
    }

    /**
     * التحقق من وجود محاولة تسجيل سابقة
     * Check if delivery note has previous registration attempt
     *
     * GET /api/warehouse/registration/check-duplicate/{deliveryNoteId}
     */
    public function checkDuplicate(DeliveryNote $deliveryNote): JsonResponse
    {
        try {
            $hasPrevious = $this->duplicateService->hasPreviousAttempt($deliveryNote);
            $lastAttempt = $this->duplicateService->getLastAttempt($deliveryNote);
            $statusInfo = $this->duplicateService->getStatusDescription($deliveryNote);

            return response()->json([
                'success' => true,
                'has_previous_attempt' => $hasPrevious,
                'status_info' => $statusInfo,
                'last_attempt' => $lastAttempt ? [
                    'weight' => $lastAttempt->weight_recorded,
                    'location' => $lastAttempt->location,
                    'material_type_id' => $lastAttempt->material_type_id,
                    'registered_at' => $lastAttempt->registered_at,
                    'registered_by' => $lastAttempt->registeredBy?->name,
                ] : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على جميع محاولات التسجيل
     * Get all registration attempts for a delivery note
     *
     * GET /api/warehouse/registration/attempts/{deliveryNoteId}
     */
    public function getAttempts(DeliveryNote $deliveryNote): JsonResponse
    {
        try {
            $attempts = $this->duplicateService->getAllAttempts($deliveryNote);

            return response()->json([
                'success' => true,
                'total' => $attempts->count(),
                'attempts' => $attempts->map(fn ($attempt) => [
                    'id' => $attempt->id,
                    'weight' => $attempt->weight_recorded,
                    'location' => $attempt->location,
                    'material_type' => $attempt->materialType?->type_name,
                    'registered_by' => $attempt->registeredBy?->name,
                    'registered_at' => $attempt->registered_at,
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * مقارنة محاولات التسجيل
     * Compare registration attempts
     *
     * GET /api/warehouse/registration/compare/{deliveryNoteId}
     */
    public function compareAttempts(DeliveryNote $deliveryNote): JsonResponse
    {
        try {
            $comparison = $this->duplicateService->compareAttempts($deliveryNote);

            return response()->json([
                'success' => true,
                'comparison' => $comparison,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على حالة منع التكرار
     * Get duplicate prevention status
     *
     * GET /api/warehouse/registration/status/{deliveryNoteId}
     */
    public function getStatus(DeliveryNote $deliveryNote): JsonResponse
    {
        try {
            $statusInfo = $this->duplicateService->getStatusDescription($deliveryNote);
            $hasPrevious = $this->duplicateService->hasPreviousAttempt($deliveryNote);
            $remaining = $this->duplicateService->getRemainingAttempts($deliveryNote);

            return response()->json([
                'success' => true,
                'delivery_note_id' => $deliveryNote->id,
                'note_number' => $deliveryNote->note_number,
                'status' => $statusInfo['status'],
                'attempts' => $statusInfo['attempts'],
                'max_attempts' => $statusInfo['max_attempts'],
                'remaining_attempts' => $remaining,
                'has_previous_attempt' => $hasPrevious,
                'is_warning' => $statusInfo['is_warning'],
                'is_exceeded' => $statusInfo['is_exceeded'],
                'message' => $statusInfo['message'],
                'icon' => $statusInfo['icon'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
