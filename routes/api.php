<?php

use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes للمزامنة (Offline/Online Sync)
|--------------------------------------------------------------------------
|
| هذه الـ Routes مخصصة لنظام المزامنة بين الأجهزة المحلية والسيرفر المركزي
|
*/

// Route للاختبار بدون authentication
// Route::get('/sync', function () {
//     return response()->json([
//         'status' => 'ok',
//         'message' => 'Sync API is working',
//         'version' => '1.0',
//         'endpoints' => [
//             '/api/sync/health',
//             '/api/sync/push',
//             '/api/sync/pull',
//             '/api/sync/stats',
//         ]
//     ]);
// });

Route::middleware('auth:sanctum')->prefix('sync')->group(function () {
    
    // رفع البيانات من الجهاز المحلي للسيرفر
    Route::post('/push', [SyncController::class, 'push'])->name('sync.push');
    
    // سحب البيانات من السيرفر للجهاز المحلي
    Route::get('/pull', [SyncController::class, 'pull'])->name('sync.pull');
    
    // معالجة العمليات المعلقة
    Route::post('/process-pending', [SyncController::class, 'processPending'])->name('sync.process-pending');
    
    // إضافة عملية للانتظار
    Route::post('/queue', [SyncController::class, 'queue'])->name('sync.queue');
    
    // الحصول على إحصائيات المزامنة
    Route::get('/stats', [SyncController::class, 'stats'])->name('sync.stats');
    
    // دمج البيانات (Batch sync)
    Route::post('/batch', [SyncController::class, 'batch'])->name('sync.batch');
    
    // إعادة محاولة العمليات الفاشلة
    Route::post('/retry-failed', [SyncController::class, 'retryFailed'])->name('sync.retry-failed');
    
    // التحقق من حالة الاتصال
    Route::get('/health', [SyncController::class, 'health'])->name('sync.health');
});
