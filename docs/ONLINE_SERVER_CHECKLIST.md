# قائمة التحقق من إعدادات السيرفر الأونلاين
## Online Server Configuration Checklist

## المشكلة الحالية
اختبار الاتصال فشل (`connection: false, authentication: false`)  
الملفات موجودة على السيرفر، لكن هناك إعدادات ناقصة.

---

## 1️⃣ التحقق من ملف .env على السيرفر الأونلاين

### الوصول للملف
**الطريقة 1: عبر cPanel File Manager**
1. سجل دخول لـ cPanel في NameCheap
2. افتح File Manager
3. اذهب إلى مجلد المشروع الرئيسي
4. ابحث عن ملف `.env` (قد يكون مخفي، فعّل "Show Hidden Files")

**الطريقة 2: عبر FTP**
1. استخدم FileZilla أو أي FTP client
2. اتصل بالسيرفر
3. اذهب لمجلد المشروع
4. افتح `.env`

### الإعدادات المطلوبة ✅

تأكد من وجود هذه الأسطر في `.env` على السيرفر الأونلاين:

```env
# تأكد أن السيرفر معرّف كـ Central Server
IS_CENTRAL_SERVER=true

# يجب أن يكون APP_ENV=production
APP_ENV=production

# تأكد من صحة URL السيرفر
APP_URL=https://hitstest.sehoool.com

# إعدادات قاعدة البيانات
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=sehohoqm_fatwora
DB_USERNAME=sehohoqm_fatwora
DB_PASSWORD=YOUR_DATABASE_PASSWORD
```

### ❌ ما لا يجب وجوده على السيرفر الأونلاين:
```env
# هذه الإعدادات للأجهزة المحلية فقط، احذفها من السيرفر الأونلاين
CENTRAL_SERVER_URL=...
CENTRAL_SERVER_TOKEN=...
DEVICE_ID=...
```

---

## 2️⃣ التحقق من تشغيل Migrations

### فحص الجداول في قاعدة البيانات

**عبر phpMyAdmin:**
1. افتح phpMyAdmin من cPanel
2. اختر قاعدة البيانات `sehohoqm_fatwora`
3. تحقق من وجود هذه الجداول:
   - ✅ `sync_logs`
   - ✅ `sync_histories`
   - ✅ `pending_syncs`
   - ✅ `user_last_syncs`

### إذا كانت الجداول غير موجودة:

**الطريقة 1: عبر Terminal (إذا متاح SSH)**
```bash
cd /path/to/your/project
php artisan migrate
```

**الطريقة 2: عبر cPanel Terminal (إذا متاح)**
```bash
php artisan migrate
```

**الطريقة 3: تشغيل SQL يدوياً في phpMyAdmin**
- افتح ملفات الـ migrations من المشروع المحلي:
  - `database/migrations/2025_12_16_000001_create_sync_tables.php`
  - `database/migrations/2025_12_16_000002_add_sync_fields_to_all_tables.php`
- قم بنسخ أوامر SQL منها وتشغيلها يدوياً

---

## 3️⃣ التحقق من وجود ملفات النظام

تأكد من وجود هذه الملفات على السيرفر الأونلاين:

### Controllers
```
app/Http/Controllers/Api/SyncController.php ✅
```

### Models
```
app/Models/SyncLog.php ✅
app/Models/SyncHistory.php ✅
app/Models/PendingSync.php ✅
app/Models/UserLastSync.php ✅
```

### Services
```
app/Services/SyncService.php ✅
app/Services/CentralServerService.php ✅
```

### Traits
```
app/Traits/Syncable.php ✅
```

### Config
```
config/sync.php ✅
```

### Routes
```
routes/api.php (يحتوي على مسارات sync) ✅
```

---

## 4️⃣ التحقق من صحة Token

### فحص الـ Token في قاعدة البيانات

**في phpMyAdmin على السيرفر الأونلاين:**
```sql
SELECT id, tokenable_id, name, abilities, created_at 
FROM personal_access_tokens 
WHERE name = 'sync-token'
ORDER BY created_at DESC 
LIMIT 1;
```

**التأكد من:**
- ✅ الـ Token موجود
- ✅ `abilities` تحتوي على `["*"]` أو `["sync:push", "sync:pull"]`
- ✅ لم تنتهي صلاحيته (إذا كان هناك `expires_at`)

### الـ Token الحالي المستخدم في السيرفر المحلي:
```
2|6c0133a25a418d06ab9bebde361e5d09467923a352fb16f2acd622ec0827e154
```

---

## 5️⃣ التحقق من صحة Routes

### اختبار Routes عبر المتصفح

افتح المتصفح واذهب إلى:
```
https://hitstest.sehoool.com/api/sync/health
```

**النتيجة المتوقعة:**
- ✅ **إذا كانت الصفحة تعطي JSON أو خطأ Authentication:** Routes تعمل! ✔️
- ❌ **إذا كانت 404 Not Found:** Routes غير موجودة أو غير مفعلة

### التحقق من محتوى routes/api.php

افتح `routes/api.php` على السيرفر وتأكد من وجود:
```php
use App\Http\Controllers\Api\SyncController;

Route::middleware('auth:sanctum')->prefix('sync')->group(function () {
    Route::post('/push', [SyncController::class, 'push']);
    Route::get('/pull', [SyncController::class, 'pull']);
    Route::get('/health', [SyncController::class, 'health']);
    // ... بقية المسارات
});
```

---

## 6️⃣ فحص الـ Cache

أحياناً Routes أو Config تكون محفوظة في الـ cache

### تنظيف الـ Cache (عبر Terminal أو cPanel Terminal):
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan optimize:clear
```

### إذا لم يكن لديك وصول للـ Terminal:
- احذف محتويات مجلد `bootstrap/cache/` عبر File Manager
- احذف محتويات مجلد `storage/framework/cache/` عبر File Manager

---

## 7️⃣ التحقق من Permissions

تأكد من صلاحيات المجلدات:

### المجلدات المطلوبة:
```
storage/          (775 أو 777)
storage/logs/     (775 أو 777)
storage/framework/ (775 أو 777)
bootstrap/cache/   (775 أو 777)
```

### تغيير الصلاحيات عبر cPanel File Manager:
1. اضغط بالزر الأيمن على المجلد
2. اختر "Change Permissions"
3. ضع القيم المناسبة (775 أو 777)

---

## 8️⃣ فحص Error Logs

### عبر cPanel:
1. اذهب إلى "Errors" في cPanel
2. أو افتح ملف `storage/logs/laravel.log`
3. ابحث عن أي أخطاء متعلقة بـ "sync" أو "auth"

### أخطاء شائعة:
- ❌ `Class 'App\Http\Controllers\Api\SyncController' not found` → الـ Controller غير موجود
- ❌ `Table 'sync_logs' doesn't exist` → Migrations لم تُشغل
- ❌ `Unauthenticated` → مشكلة في الـ Token أو Sanctum

---

## 9️⃣ اختبار يدوي للـ API

### استخدام Postman أو curl:

**Test 1: Health Check (بدون Token)**
```bash
curl https://hitstest.sehoool.com/api/sync/health
```
**النتيجة المتوقعة:** خطأ 401 Unauthenticated (هذا طبيعي!)

**Test 2: Health Check (مع Token)**
```bash
curl -H "Authorization: Bearer 2|6c0133a25a418d06ab9bebde361e5d09467923a352fb16f2acd622ec0827e154" \
     https://hitstest.sehoool.com/api/sync/health
```
**النتيجة المتوقعة:** 
```json
{
    "status": "ok",
    "server": "central",
    "timestamp": "2025-12-17..."
}
```

---

## 🔟 إعادة اختبار الاتصال من السيرفر المحلي

بعد التأكد من كل النقاط أعلاه، ارجع للسيرفر المحلي وشغّل:

```bash
php artisan sync:test-connection
```

**النتيجة المتوقعة:**
```
✓ Connection: Success
✓ Authentication: Success
✓ Push Test: Success
✓ Pull Test: Success
```

---

## 📋 ملخص الخطوات السريعة

1. ✅ افتح `.env` على السيرفر الأونلاين
2. ✅ تأكد من `IS_CENTRAL_SERVER=true`
3. ✅ احذف `CENTRAL_SERVER_URL` و `DEVICE_ID` و `CENTRAL_SERVER_TOKEN` (إذا موجودة)
4. ✅ افتح phpMyAdmin وتحقق من وجود جداول `sync_logs`, `sync_histories`, `pending_syncs`, `user_last_syncs`
5. ✅ إذا لم تكن موجودة، شغّل `php artisan migrate`
6. ✅ افتح المتصفح: `https://hitstest.sehoool.com/api/sync/health`
7. ✅ إذا كانت 404، تحقق من `routes/api.php`
8. ✅ نظّف الـ cache: `php artisan optimize:clear`
9. ✅ ارجع للسيرفر المحلي وشغّل: `php artisan sync:test-connection`

---

## 🆘 إذا استمرت المشكلة

### شارك هذه المعلومات:

1. **نتيجة اختبار URL في المتصفح:**
   ```
   https://hitstest.sehoool.com/api/sync/health
   ```

2. **محتوى .env على السيرفر الأونلاين** (فقط الأسطر المتعلقة بـ APP, DB, SYNC)

3. **قائمة الجداول في قاعدة البيانات** (من phpMyAdmin)

4. **آخر 10-20 سطر من ملف** `storage/logs/laravel.log` على السيرفر الأونلاين

5. **نتيجة الأمر:** `php artisan sync:test-connection` من السيرفر المحلي

---

## 📝 ملاحظات مهمة

- ⚠️ **الـ Token يجب أن يبدأ برقم ID:** مثل `2|6c0133a25a418d...`
- ⚠️ **لا تشارك الـ Token الكامل مع أحد!**
- ⚠️ **احفظ نسخة احتياطية من `.env` قبل التعديل**
- ⚠️ **بعد أي تعديل على Routes أو Config، نظّف الـ cache**

---

✅ **بعد اكتمال كل الخطوات، سيعمل نظام المزامنة بشكل كامل!**
