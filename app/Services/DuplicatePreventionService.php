<?php

namespace App\Services;

use App\Models\DeliveryNote;
use App\Models\RegistrationLog;
use Illuminate\Support\Facades\Log;

class DuplicatePreventionService
{
    /**
     * Configuration
     */
    private $config;

    public function __construct()
    {
        $this->config = config('warehouse-registration');
    }

    /**
     * التحقق من وجود محاولة تسجيل سابقة
     * Check for previous registration attempts
     */
    public function hasPreviousAttempt(DeliveryNote $deliveryNote): bool
    {
        if (!$this->config['enabled']) {
            return false;
        }

        return RegistrationLog::where('delivery_note_id', $deliveryNote->id)
            ->exists();
    }

    /**
     * الحصول على آخر محاولة تسجيل
     * Get last registration attempt
     */
    public function getLastAttempt(DeliveryNote $deliveryNote): ?RegistrationLog
    {
        return RegistrationLog::where('delivery_note_id', $deliveryNote->id)
            ->latest('registered_at')
            ->first();
    }

    /**
     * الحصول على جميع محاولات التسجيل
     * Get all registration attempts
     */
    public function getAllAttempts(DeliveryNote $deliveryNote)
    {
        return RegistrationLog::where('delivery_note_id', $deliveryNote->id)
            ->latest('registered_at')
            ->get();
    }

    /**
     * توليد المفتاح الفريد
     * Generate unique key
     */
    public function generateUniqueKey(DeliveryNote $deliveryNote): string
    {
        $method = $this->config['key_method'];

        return match ($method) {
            'hash' => $this->generateHashKey($deliveryNote),
            'uuid' => $this->generateUuidKey($deliveryNote),
            'composite' => $this->generateCompositeKey($deliveryNote),
            default => $this->generateHashKey($deliveryNote),
        };
    }

    /**
     * توليد مفتاح التجزئة
     */
    private function generateHashKey(DeliveryNote $deliveryNote): string
    {
        $components = [
            $deliveryNote->note_number,
            $deliveryNote->supplier_id,
            $deliveryNote->created_at->format('Y-m-d'),
        ];

        $compositeString = implode('_', $components);
        $algorithm = $this->config['hash_algorithm'];

        return hash($algorithm, $compositeString);
    }

    /**
     * توليد مفتاح UUID
     */
    private function generateUuidKey(DeliveryNote $deliveryNote): string
    {
        return "DN-{$deliveryNote->id}-" . uniqid();
    }

    /**
     * توليد مفتاح مركب
     */
    private function generateCompositeKey(DeliveryNote $deliveryNote): string
    {
        return implode('|', [
            $deliveryNote->note_number,
            $deliveryNote->supplier_id,
            $deliveryNote->created_at->timestamp,
        ]);
    }

    /**
     * التحقق من تجاوز الحد الأقصى للمحاولات
     * Check if max attempts exceeded
     */
    public function hasExceededMaxAttempts(DeliveryNote $deliveryNote): bool
    {
        $maxAttempts = $this->config['max_attempts'];
        return ($deliveryNote->registration_attempts ?? 0) >= $maxAttempts;
    }

    /**
     * التحقق من الوصول لعتبة التحذير
     * Check if warning threshold reached
     */
    public function isWarningThreshold(DeliveryNote $deliveryNote): bool
    {
        $threshold = $this->config['warning_threshold'];
        return ($deliveryNote->registration_attempts ?? 0) >= $threshold;
    }

    /**
     * الحصول على عدد المحاولات المتبقية
     * Get remaining attempts
     */
    public function getRemainingAttempts(DeliveryNote $deliveryNote): int
    {
        $maxAttempts = $this->config['max_attempts'];
        $currentAttempts = $deliveryNote->registration_attempts ?? 0;

        return max(0, $maxAttempts - $currentAttempts);
    }

    /**
     * تسجيل محاولة التسجيل
     * Log registration attempt
     */
    public function logAttempt(DeliveryNote $deliveryNote, array $data, bool $success = false): void
    {
        if (!$this->config['logging']['enabled']) {
            return;
        }

        $logMessage = [
            'delivery_note_id' => $deliveryNote->id,
            'note_number' => $deliveryNote->note_number,
            'supplier_id' => $deliveryNote->supplier_id,
            'attempt_number' => ($deliveryNote->registration_attempts ?? 0) + 1,
            'success' => $success,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ];

        if ($this->config['logging']['log_attempts']) {
            Log::channel('registration')->info('Registration Attempt', $logMessage);
        }

        if ($this->config['logging']['log_duplicates'] && $this->hasPreviousAttempt($deliveryNote)) {
            Log::channel('registration')->warning('Duplicate Registration Attempt', $logMessage);
        }
    }

    /**
     * الحصول على حالة البضاعة (وصف)
     * Get shipment status description
     */
    public function getStatusDescription(DeliveryNote $deliveryNote): array
    {
        $attempts = $deliveryNote->registration_attempts ?? 0;
        $maxAttempts = $this->config['max_attempts'];
        $warningThreshold = $this->config['warning_threshold'];

        return [
            'status' => match (true) {
                $this->hasExceededMaxAttempts($deliveryNote) => 'exceeded',
                $this->isWarningThreshold($deliveryNote) => 'warning',
                $attempts > 0 => 'attempted',
                default => 'new',
            },
            'attempts' => $attempts,
            'max_attempts' => $maxAttempts,
            'remaining' => $this->getRemainingAttempts($deliveryNote),
            'is_warning' => $this->isWarningThreshold($deliveryNote),
            'is_exceeded' => $this->hasExceededMaxAttempts($deliveryNote),
            'message' => $this->getStatusMessage($deliveryNote),
            'icon' => $this->getStatusIcon($deliveryNote),
            'color' => $this->getStatusColor($deliveryNote),
        ];
    }

    /**
     * الحصول على رسالة الحالة
     */
    private function getStatusMessage(DeliveryNote $deliveryNote): string
    {
        $attempts = $deliveryNote->registration_attempts ?? 0;
        $remaining = $this->getRemainingAttempts($deliveryNote);

        if ($this->hasExceededMaxAttempts($deliveryNote)) {
            return "تم تجاوز الحد الأقصى للمحاولات ({$this->config['max_attempts']})";
        }

        if ($this->isWarningThreshold($deliveryNote)) {
            return "تحذير: محاولة {$attempts} من {$this->config['max_attempts']} ({$remaining} متبقية)";
        }

        if ($attempts > 0) {
            return "محاولة {$attempts} من {$this->config['max_attempts']}";
        }

        return "شحنة جديدة - لم تُسجل بعد";
    }

    /**
     * الحصول على الرمز
     */
    private function getStatusIcon(DeliveryNote $deliveryNote): string
    {
        if ($this->hasExceededMaxAttempts($deliveryNote)) {
            return '❌';
        }

        if ($this->isWarningThreshold($deliveryNote)) {
            return '⚠️';
        }

        if (($deliveryNote->registration_attempts ?? 0) > 0) {
            return '📋';
        }

        return '✅';
    }

    /**
     * الحصول على اللون
     */
    private function getStatusColor(DeliveryNote $deliveryNote): string
    {
        if ($this->hasExceededMaxAttempts($deliveryNote)) {
            return $this->config['ui']['colors']['warning'];
        }

        if ($this->isWarningThreshold($deliveryNote)) {
            return $this->config['ui']['colors']['warning'];
        }

        if (($deliveryNote->registration_attempts ?? 0) > 0) {
            return $this->config['ui']['colors']['info'];
        }

        return $this->config['ui']['colors']['registered'];
    }

    /**
     * المقارنة بين المحاولات
     * Compare attempts
     */
    public function compareAttempts(DeliveryNote $deliveryNote): array
    {
        $attempts = $this->getAllAttempts($deliveryNote);

        if ($attempts->count() < 2) {
            return [];
        }

        $comparison = [];
        for ($i = 0; $i < $attempts->count() - 1; $i++) {
            $current = $attempts[$i];
            $previous = $attempts[$i + 1];

            $comparison[] = [
                'attempt_number' => $i + 1,
                'previous' => [
                    'weight' => $previous->weight_recorded,
                    'location' => $previous->location,
                    'material_type_id' => $previous->material_type_id,
                ],
                'current' => [
                    'weight' => $current->weight_recorded,
                    'location' => $current->location,
                    'material_type_id' => $current->material_type_id,
                ],
                'changes' => [
                    'weight_changed' => $current->weight_recorded != $previous->weight_recorded,
                    'location_changed' => $current->location != $previous->location,
                    'material_type_changed' => $current->material_type_id != $previous->material_type_id,
                ],
            ];
        }

        return $comparison;
    }
}
