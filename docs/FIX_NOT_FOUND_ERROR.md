# دليل حل مشكلة "Not Found" للمزامنة

## المشكلة
عند محاولة الوصول إلى `https://hitstest.sehoool.com/api/sync` تظهر رسالة **404 Not Found**

## الحلول المحتملة (بالترتيب)

---

## ✅ الحل 1: التحقق من ملف .htaccess في مجلد public

### الخطوة 1: افتح cPanel File Manager
1. سجل دخول لـ cPanel
2. افتح File Manager
3. اذهب إلى مجلد المشروع → `public`

### الخطوة 2: تحقق من وجود .htaccess
ابحث عن ملف `.htaccess` في مجلد `public`

**إذا لم يكن موجوداً أو تالف، انسخ هذا المحتوى:**

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### الخطوة 3: تأكد من الصلاحيات
- صلاحيات .htaccess يجب أن تكون **644**

---

## ✅ الحل 2: تنظيف الـ Cache والـ Routes

### على السيرفر الأونلاين (عبر cPanel Terminal أو SSH):

```bash
cd /path/to/your/project

# تنظيف كل أنواع الـ cache
php artisan optimize:clear

# أو بشكل منفصل
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### إذا لم يكن لديك وصول Terminal:
احذف يدوياً (عبر File Manager):
- `bootstrap/cache/config.php`
- `bootstrap/cache/routes-v7.php`
- `bootstrap/cache/services.php`
- محتويات مجلد `storage/framework/cache/`
- محتويات مجلد `storage/framework/views/`

---

## ✅ الحل 3: التأكد من routes/api.php

### افتح ملف routes/api.php على السيرفر الأونلاين

تأكد من وجود هذا الكود:

```php
<?php

use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('sync')->group(function () {
    Route::post('/push', [SyncController::class, 'push']);
    Route::get('/pull', [SyncController::class, 'pull']);
    Route::post('/process-pending', [SyncController::class, 'processPending']);
    Route::post('/queue', [SyncController::class, 'queue']);
    Route::get('/stats', [SyncController::class, 'stats']);
    Route::post('/batch', [SyncController::class, 'batch']);
    Route::post('/retry-failed', [SyncController::class, 'retryFailed']);
    Route::get('/health', [SyncController::class, 'health']);
});
```

---

## ✅ الحل 4: التحقق من RouteServiceProvider

### افتح bootstrap/app.php

تأكد من وجود هذه الأسطر:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

**أو** إذا كان Laravel 10 أو أقدم، افتح `app/Providers/RouteServiceProvider.php`:

```php
protected function mapApiRoutes()
{
    Route::prefix('api')
        ->middleware('api')
        ->namespace($this->namespace)
        ->group(base_path('routes/api.php'));
}
```

---

## ✅ الحل 5: اختبار مباشر (تجاوز Routes)

### أنشئ ملف test-api.php في مجلد public على السيرفر الأونلاين:

```php
<?php
// test-api.php
header('Content-Type: application/json');

echo json_encode([
    'status' => 'ok',
    'message' => 'API is working',
    'timestamp' => date('Y-m-d H:i:s'),
    'server' => 'online'
], JSON_PRETTY_PRINT);
```

### اختبر في المتصفح:
```
https://hitstest.sehoool.com/test-api.php
```

**إذا عمل:** المشكلة في Routes أو .htaccess  
**إذا لم يعمل:** المشكلة في إعدادات الدومين

---

## ✅ الحل 6: تحديد مسار الـ API بشكل مباشر

### في ملف .env على السيرفر المحلي

جرب تغيير الرابط إلى:

```env
# جرب بدون /api
CENTRAL_SERVER_URL=https://hitstest.sehoool.com/sync

# أو جرب مع index.php
CENTRAL_SERVER_URL=https://hitstest.sehoool.com/index.php/api/sync
```

---

## ✅ الحل 7: التحقق من Document Root في cPanel

### المشكلة المحتملة:
قد يكون الدومين يشير إلى مجلد خاطئ

### الحل:
1. افتح cPanel → **Domains**
2. اختر `hitstest.sehoool.com`
3. تأكد أن Document Root يشير إلى: `public_html/public` أو `hitstest/public`

### إذا كان يشير لمجلد خاطئ:
- غيره ليشير إلى مجلد `public` داخل مشروع Laravel

---

## 🧪 اختبارات سريعة

### اختبار 1: فحص الـ Routes عبر Artisan
```bash
php artisan route:list | grep sync
```

**النتيجة المتوقعة:**
```
GET|HEAD  api/sync/health ............. sync.health › SyncController@health
POST      api/sync/push ............... sync.push › SyncController@push
GET|HEAD  api/sync/pull ............... sync.pull › SyncController@pull
...
```

### اختبار 2: فحص الرابط المباشر
افتح في المتصفح:
```
https://hitstest.sehoool.com/api/sync/health
```

**النتائج المحتملة:**
- ✅ `{"message":"Unauthenticated."}` → ممتاز! Routes تعمل
- ❌ `404 Not Found` → المشكلة في .htaccess أو Routes
- ❌ `500 Internal Server Error` → المشكلة في الكود
- ❌ صفحة فارغة → المشكلة في PHP أو Server Config

### اختبار 3: استخدم cURL من السيرفر المحلي
```bash
curl -v https://hitstest.sehoool.com/api/sync/health
```

---

## 📋 الخطوات السريعة للحل

1. ✅ ارفع ملف `test-sync.php` إلى `public/` على السيرفر الأونلاين
2. ✅ افتح `https://hitstest.sehoool.com/test-sync.php` في المتصفح
3. ✅ إذا عمل، المشكلة في Routes
4. ✅ تحقق من `.htaccess` في مجلد `public`
5. ✅ شغّل `php artisan optimize:clear` على السيرفر
6. ✅ تحقق من `routes/api.php` يحتوي على مسارات sync
7. ✅ افتح `https://hitstest.sehoool.com/api/sync/health` في المتصفح
8. ✅ إذا ظهر Unauthenticated، معناها الـ Routes تعمل! ✔️

---

## 🎯 الحل الأسرع والأفضل

### على السيرفر الأونلاين فقط:

```bash
# 1. نظف الـ cache
php artisan optimize:clear

# 2. تأكد من Routes
php artisan route:list | grep sync

# 3. إذا لم تظهر Routes، شغّل:
php artisan route:cache
```

---

## 📞 بعد تجربة الحلول

شارك النتائج التالية:

1. **نتيجة فتح:** `https://hitstest.sehoool.com/test-sync.php`
2. **نتيجة فتح:** `https://hitstest.sehoool.com/api/sync/health`
3. **هل .htaccess موجود في public؟** نعم/لا
4. **Document Root في cPanel يشير إلى أي مجلد؟**
5. **نتيجة:** `php artisan route:list | grep sync` (على السيرفر الأونلاين)

---

## ⚠️ ملاحظة مهمة

إذا كان السيرفر الأونلاين يستخدم cPanel:
- قد يكون Laravel مثبت في `public_html/hitstest/`
- وDocument Root يجب أن يشير إلى `public_html/hitstest/public/`
- تأكد من ذلك في **cPanel → Domains**

---

✅ **بعد حل المشكلة، سيعمل الاتصال بنجاح!**
