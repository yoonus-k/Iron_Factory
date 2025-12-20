# ⚡ دليل سريع - إعداد نظام المزامنة في 5 دقائق

## 📝 **الخطوات السريعة:**

### 1️⃣ **إضافة الإعدادات في `.env`:**

```env
# نسخ والصق في ملف .env الخاص بك

# ═══ إعدادات السيرفر المركزي ═══
CENTRAL_SERVER_URL=https://your-central-server.com
CENTRAL_SERVER_TOKEN=your-token-here
LOCAL_SERVER_ID=factory-1
LOCAL_SERVER_NAME="المصنع - السيرفر 1"
IS_CENTRAL_SERVER=false

# ═══ إعدادات المزامنة ═══
AUTO_SYNC_ENABLED=true
AUTO_SYNC_INTERVAL=1
SYNC_BATCH_SIZE=100
SYNC_MAX_RETRIES=3
SYNC_CONNECTION_TIMEOUT=30
SYNC_VERIFY_SSL=false
```

### 2️⃣ **إضافة Routes:**

أضف في `routes/web.php`:

```php
use App\Http\Controllers\SyncDashboardController;

Route::middleware(['auth'])->prefix('sync-dashboard')->group(function () {
    Route::get('/', [SyncDashboardController::class, 'index']);
    Route::get('/stats', [SyncDashboardController::class, 'stats']);
    Route::get('/pending', [SyncDashboardController::class, 'pending']);
    Route::get('/failed', [SyncDashboardController::class, 'failed']);
    Route::get('/history', [SyncDashboardController::class, 'history']);
    Route::get('/users', [SyncDashboardController::class, 'users']);
    Route::get('/chart-data', [SyncDashboardController::class, 'chartData']);
    Route::post('/retry/{id}', [SyncDashboardController::class, 'retry']);
    Route::delete('/delete/{id}', [SyncDashboardController::class, 'delete']);
    Route::post('/retry-all', [SyncDashboardController::class, 'retryAll']);
    Route::post('/cleanup', [SyncDashboardController::class, 'cleanup']);
});
```

### 3️⃣ **اختبار الاتصال:**

```bash
php artisan tinker

# ثم
App\Services\CentralServerService::test()
```

### 4️⃣ **تشغيل المزامنة:**

```bash
# Terminal 1
php artisan serve

# Terminal 2
php artisan schedule:work
```

### 5️⃣ **فتح Dashboard:**

```
http://localhost/sync-dashboard
```

---

## 🎯 **ملاحظات مهمة:**

### ✅ **للسيرفر المحلي (على Windows):**
- `IS_CENTRAL_SERVER=false`
- يحتاج Token من السيرفر المركزي
- يعمل Scheduler للمزامنة التلقائية

### ✅ **للسيرفر المركزي (أونلاين):**
- `IS_CENTRAL_SERVER=true`
- يستقبل البيانات من السيرفرات المحلية
- يوزع البيانات على كل السيرفرات

---

## 🔧 **اختبار سريع:**

```bash
# 1. إنشاء سجل تجريبي
php artisan tinker
Material::create(['name' => 'Test', 'barcode' => '123'])

# 2. فحص العمليات المعلقة
php artisan sync:process-pending

# 3. فحص Dashboard
# افتح: http://localhost/sync-dashboard
```

---

## 📞 **إذا واجهت مشكلة:**

```bash
# مسح الـ Cache
php artisan config:clear
php artisan cache:clear

# فحص الاتصال
App\Services\CentralServerService::test()

# فحص Logs
tail -f storage/logs/laravel.log
```

---

**✨ الآن نظامك جاهز للعمل أوفلاين/أونلاين!**
