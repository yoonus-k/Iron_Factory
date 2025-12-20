# 📦 ملفات المزامنة المطلوب رفعها على السيرفر الأونلاين

## 📁 القائمة الكاملة للملفات:

### 1️⃣ Migrations (قاعدة البيانات)
```
database/migrations/2025_12_16_000001_create_sync_tables.php
database/migrations/2025_12_16_000002_add_sync_fields_to_all_tables.php
```

### 2️⃣ Models (النماذج)
```
app/Models/SyncLog.php
app/Models/SyncHistory.php
app/Models/PendingSync.php
app/Models/UserLastSync.php
```

### 3️⃣ Services (الخدمات)
```
app/Services/SyncService.php
app/Services/CentralServerService.php
```

### 4️⃣ Traits (السمات)
```
app/Traits/Syncable.php
```

### 5️⃣ Controllers (المتحكمات)
```
app/Http/Controllers/Api/SyncController.php
app/Http/Controllers/SyncDashboardController.php
```

### 6️⃣ Middleware
```
app/Http/Middleware/TrackDeviceId.php
```

### 7️⃣ Commands (الأوامر)
```
app/Console/Commands/ProcessPendingSyncs.php
app/Console/Commands/GenerateSyncToken.php
app/Console/Commands/TestSyncConnection.php
```

### 8️⃣ Config (الإعدادات)
```
config/sync.php
```

### 9️⃣ Routes (المسارات)
```
routes/api.php  (أضف فقط routes المزامنة الموجودة في الملف)
```

---

## 🚀 طريقة الرفع السريعة:

### الخيار 1: ضغط الملفات ورفعها عبر cPanel

1. **في جهازك المحلي، افتح PowerShell:**

```powershell
cd C:\Users\mon3em\Desktop\tesr_docker

# إنشاء مجلد مؤقت للملفات
New-Item -ItemType Directory -Force -Path "C:\Users\mon3em\Desktop\sync_files"

# نسخ الملفات المطلوبة
Copy-Item "database\migrations\2025_12_16_000001_create_sync_tables.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"
Copy-Item "database\migrations\2025_12_16_000002_add_sync_fields_to_all_tables.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"
Copy-Item "app\Models\SyncLog.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"
Copy-Item "app\Models\SyncHistory.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"
Copy-Item "app\Models\PendingSync.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"
Copy-Item "app\Models\UserLastSync.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"
Copy-Item "app\Services\SyncService.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"
Copy-Item "app\Services\CentralServerService.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"
Copy-Item "app\Traits\Syncable.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"
Copy-Item "app\Http\Controllers\Api\SyncController.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"
Copy-Item "app\Http\Controllers\SyncDashboardController.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"
Copy-Item "app\Http\Middleware\TrackDeviceId.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"
Copy-Item "app\Console\Commands\ProcessPendingSyncs.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"
Copy-Item "app\Console\Commands\GenerateSyncToken.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"
Copy-Item "config\sync.php" -Destination "C:\Users\mon3em\Desktop\sync_files\"

# ضغط الملفات
Compress-Archive -Path "C:\Users\mon3em\Desktop\sync_files\*" -DestinationPath "C:\Users\mon3em\Desktop\sync_files.zip" -Force
```

2. **ارفع الملف `sync_files.zip` عبر FTP أو File Manager في cPanel**

3. **على السيرفر الأونلاين (عبر SSH أو File Manager):**

```bash
# استخرج الملفات
cd ~/
unzip sync_files.zip -d sync_temp

# انقل الملفات لأماكنها الصحيحة
# (افترض أن Laravel في ~/public_html)
cd ~/public_html

# نسخ Migrations
cp ~/sync_temp/2025_12_16_000001_create_sync_tables.php database/migrations/
cp ~/sync_temp/2025_12_16_000002_add_sync_fields_to_all_tables.php database/migrations/

# نسخ Models
cp ~/sync_temp/SyncLog.php app/Models/
cp ~/sync_temp/SyncHistory.php app/Models/
cp ~/sync_temp/PendingSync.php app/Models/
cp ~/sync_temp/UserLastSync.php app/Models/

# نسخ Services
mkdir -p app/Services
cp ~/sync_temp/SyncService.php app/Services/
cp ~/sync_temp/CentralServerService.php app/Services/

# نسخ Traits
mkdir -p app/Traits
cp ~/sync_temp/Syncable.php app/Traits/

# نسخ Controllers
mkdir -p app/Http/Controllers/Api
cp ~/sync_temp/SyncController.php app/Http/Controllers/Api/
cp ~/sync_temp/SyncDashboardController.php app/Http/Controllers/

# نسخ Middleware
cp ~/sync_temp/TrackDeviceId.php app/Http/Middleware/

# نسخ Commands
cp ~/sync_temp/ProcessPendingSyncs.php app/Console/Commands/
cp ~/sync_temp/GenerateSyncToken.php app/Console/Commands/

# نسخ Config
cp ~/sync_temp/sync.php config/

# تنظيف
rm -rf ~/sync_temp
```

4. **تشغيل Migrations:**

```bash
cd ~/public_html  # أو المجلد الصحيح
php artisan migrate
```

5. **إضافة API Routes يدوياً في `routes/api.php`:**

```php
// في نهاية الملف routes/api.php
use App\Http\Controllers\Api\SyncController;

Route::middleware(['auth:sanctum'])->prefix('sync')->group(function () {
    Route::post('/push', [SyncController::class, 'receiveData']);
    Route::post('/pull', [SyncController::class, 'sendData']);
    Route::post('/register', [SyncController::class, 'registerDevice']);
    Route::post('/heartbeat', [SyncController::class, 'heartbeat']);
    Route::get('/health', [SyncController::class, 'health']);
    Route::get('/stats', [SyncController::class, 'getStats']);
    Route::get('/pending', [SyncController::class, 'getPendingSync']);
    Route::post('/resolve-conflict', [SyncController::class, 'resolveConflict']);
});
```

---

## ✅ بعد الرفع:

1. **تحديث `.env` على السيرفر الأونلاين:**
```env
IS_CENTRAL_SERVER=true
CENTRAL_SERVER_URL=
CENTRAL_SERVER_TOKEN=
SYNC_ENABLED=true
```

2. **اختبر من الجهاز المحلي:**
```bash
php artisan sync:test-connection
```

---

## 💡 ملاحظة مهمة:

الملفات موجودة في:
```
C:\Users\mon3em\Desktop\tesr_docker\
```

يمكنك نسخها يدوياً أو استخدام الأوامر أعلاه!
