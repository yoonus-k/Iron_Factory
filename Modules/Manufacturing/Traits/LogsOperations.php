<?php

namespace Modules\Manufacturing\Traits;

use App\Models\OperationLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

trait LogsOperations
{
    /**
     * تسجيل عملية في سجل العمليات - إجباري
     * Log an operation to OperationLog - Mandatory
     */
    private function logOperation(string $action, string $actionEn, string $description, string $tableName, $recordId, ?array $oldValues = null, ?array $newValues = null): void
    {
        try {
            // التحقق من أن جدول العمليات موجود
            $operationLog = OperationLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => $action,
                'action_en' => $actionEn,
                'description' => $description,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'old_values' => $oldValues ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
                'new_values' => $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
                'ip_address' => request()->ip() ?? '0.0.0.0',
                'user_agent' => request()->userAgent() ?? 'Unknown',
                'created_at' => now(),
            ]);

            // تحقق من أن السجل تم حفظه بنجاح
            if (!$operationLog || !$operationLog->id) {
                throw new \Exception('فشل حفظ سجل العملية - معرف فارغ');
            }

            Log::info('Operation logged successfully', [
                'operation_log_id' => $operationLog->id,
                'action' => $action,
                'table' => $tableName,
                'record_id' => $recordId,
            ]);

        } catch (\Exception $e) {
            // سجل الخطأ بالتفاصيل الكاملة
            Log::error('CRITICAL: Error logging operation - تسجيل العملية فشل', [
                'error' => $e->getMessage(),
                'action' => $action,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'user_id' => Auth::id() ?? 'unknown',
                'exception' => $e->getTraceAsString(),
            ]);

            // رمي الاستثناء ليتم التعامل معه من قبل الـ caller
            throw new \Exception('فشل تسجيل العملية: ' . $e->getMessage() . ' [' . $action . ' على ' . $tableName . ']');
        }
    }
}
