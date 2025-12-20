# 🌐 دليل رفع المشروع على سيرفر أونلاين (NameCheap / cPanel)

## 📋 نظرة عامة

هذا الدليل يشرح كيفية رفع مشروع Laravel على **سيرفر أونلاين** (مثل NameCheap أو أي استضافة cPanel) وربط **السيرفرات المحلية** (Windows) به للمزامنة.

---

## 🏗️ المعمارية المطلوبة

```
┌─────────────────────────────────────────┐
│     سيرفر أونلاين (Central Server)      │
│     NameCheap / cPanel / VPS           │
│     https://yourdomain.com             │
│                                         │
│  - قاعدة بيانات مركزية                 │
│  - API للمزامنة                        │
│  - Dashboard للمراقبة                  │
└────────────┬────────────────────────────┘
             │
             │ Internet (HTTPS)
             │
     ┌───────┴───────┐
     │               │
     ▼               ▼
┌─────────┐    ┌─────────┐
│ جهاز 1   │    │ جهاز 2   │
│ Windows │    │ Windows │
│ Local   │    │ Local   │
└─────────┘    └─────────┘
```

---

## 📦 الجزء الأول: رفع المشروع على السيرفر الأونلاين

### 1️⃣ تحضير الملفات للرفع

#### أ. تنظيف المشروع
```bash
# احذف الملفات غير الضرورية
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### ب. ضغط المشروع
```bash
# في PowerShell
Compress-Archive -Path C:\Users\mon3em\Desktop\tesr_docker\* -DestinationPath project.zip
```

---

### 2️⃣ رفع الملفات على cPanel

#### أ. تسجيل الدخول لـ cPanel
1. اذهب إلى: `https://yourdomain.com/cpanel`
2. أدخل اسم المستخدم وكلمة المرور

#### ب. رفع الملفات
1. افتح **File Manager**
2. اذهب إلى مجلد `public_html` أو `www`
3. قم برفع ملف `project.zip`
4. استخرج الملفات (Extract)

#### ج. هيكل المجلدات المطلوب
```
/home/username/
├── public_html/              ← هنا محتويات مجلد public فقط
│   ├── index.php
│   ├── .htaccess
│   └── ...
├── laravel/                  ← باقي ملفات Laravel
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── routes/
│   └── ...
```

#### د. نقل الملفات للهيكل الصحيح
```bash
# في Terminal من cPanel
cd /home/username
mkdir laravel
mv public_html/* laravel/
mv laravel/public/* public_html/
```

---

### 3️⃣ إعداد قاعدة البيانات

#### أ. إنشاء قاعدة البيانات
1. افتح **MySQL Databases** في cPanel
2. أنشئ قاعدة بيانات جديدة: `username_factory`
3. أنشئ مستخدم: `username_dbuser`
4. أضف المستخدم للقاعدة بكل الصلاحيات

#### ب. استيراد البيانات (اختياري)
1. افتح **phpMyAdmin**
2. اختر قاعدة البيانات
3. اذهب لـ Import
4. ارفع ملف SQL من جهازك المحلي

---

### 4️⃣ تعديل ملف `.env` على السيرفر الأونلاين

```bash
# في File Manager، عدّل ملف .env

APP_NAME="Factory System"
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=false                    # مهم جداً للأمان!
APP_URL=https://yourdomain.com

# Database - السيرفر الأونلاين
DB_CONNECTION=mysql
DB_HOST=localhost                  # أو عنوان MySQL من cPanel
DB_PORT=3306
DB_DATABASE=username_factory       # اسم قاعدة البيانات
DB_USERNAME=username_dbuser        # مستخدم قاعدة البيانات
DB_PASSWORD=YOUR_DB_PASSWORD       # كلمة المرور

# هذا السيرفر هو المركزي
IS_CENTRAL_SERVER=true

# إعدادات المزامنة
SYNC_ENABLED=true
SYNC_AUTO_INTERVAL=300             # كل 5 دقائق
SYNC_BATCH_SIZE=100
SYNC_MAX_RETRIES=3

# Mail للإشعارات
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

### 5️⃣ إعداد Laravel على السيرفر الأونلاين

```bash
# في Terminal من cPanel (أو SSH)

cd /home/username/laravel

# 1. تثبيت Composer Dependencies
composer install --no-dev --optimize-autoloader

# 2. توليد مفتاح التطبيق
php artisan key:generate

# 3. تشغيل Migrations
php artisan migrate --force

# 4. تشغيل Seeders
php artisan db:seed --force

# 5. ضبط الصلاحيات
chmod -R 775 storage bootstrap/cache
chown -R username:username storage bootstrap/cache

# 6. تحسين الأداء
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### 6️⃣ إنشاء API Token للأجهزة المحلية

#### الطريقة 1: عبر Tinker
```bash
php artisan tinker

# إنشاء توكن جديد للسيرفر المحلي
$user = \App\Models\User::where('email', 'admin@system.com')->first();
$token = $user->createToken('Local-Server-1', ['sync:*'])->plainTextToken;
echo $token;

# انسخ التوكن - ستحتاجه للأجهزة المحلية
```

#### الطريقة 2: عبر phpMyAdmin
```sql
-- في phpMyAdmin، نفّذ هذا الاستعلام
INSERT INTO personal_access_tokens 
(tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at)
VALUES 
('App\\Models\\User', 1, 'Local-Server-1', 
SHA2(CONCAT('random_string_', NOW()), 256), 
'["sync:*"]', NOW(), NOW());

-- سيتم إنشاء التوكن تلقائياً
SELECT CONCAT(id, '|', token) as full_token 
FROM personal_access_tokens 
WHERE name = 'Local-Server-1' 
ORDER BY id DESC LIMIT 1;
```

---

### 7️⃣ إضافة Routes للمزامنة

تأكد من وجود هذه الروتات في `routes/api.php`:

```php
// routes/api.php على السيرفر الأونلاين

use App\Http\Controllers\Api\SyncController;

Route::middleware(['auth:sanctum'])->prefix('sync')->group(function () {
    // استقبال البيانات من الأجهزة المحلية
    Route::post('/push', [SyncController::class, 'receiveData']);
    
    // إرسال البيانات للأجهزة المحلية
    Route::post('/pull', [SyncController::class, 'sendData']);
    
    // تسجيل الجهاز
    Route::post('/register', [SyncController::class, 'registerDevice']);
    
    // Heartbeat
    Route::post('/heartbeat', [SyncController::class, 'heartbeat']);
    
    // إحصائيات
    Route::get('/stats', [SyncController::class, 'getStats']);
});
```

---

### 8️⃣ اختبار السيرفر الأونلاين

```bash
# اختبر أن الموقع يعمل
curl https://yourdomain.com

# اختبر API المزامنة
curl -X POST https://yourdomain.com/api/sync/heartbeat \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

---

## 💻 الجزء الثاني: إعداد الأجهزة المحلية (Windows)

### 1️⃣ تعديل `.env` على الجهاز المحلي

```env
# .env على جهاز Windows المحلي

APP_NAME="Factory System - Local"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database - السيرفر المحلي
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=factory_local         # قاعدة بيانات محلية
DB_USERNAME=root
DB_PASSWORD=

# ⭐ إعدادات الاتصال بالسيرفر الأونلاين
IS_CENTRAL_SERVER=false
CENTRAL_SERVER_URL=https://yourdomain.com/api/sync
CENTRAL_SERVER_TOKEN=1|your_long_token_from_online_server_here

# إعدادات المزامنة
SYNC_ENABLED=true
SYNC_AUTO_INTERVAL=300            # كل 5 دقائق
SYNC_BATCH_SIZE=50                # أصغر على الأجهزة المحلية
SYNC_MAX_RETRIES=5
SYNC_RETRY_DELAY=60

# معرّف الجهاز الفريد
DEVICE_ID=LOCAL-001               # مختلف لكل جهاز
DEVICE_NAME="Factory Floor - PC1"
```

---

### 2️⃣ تحديث config/sync.php

```php
<?php
// config/sync.php

return [
    'enabled' => env('SYNC_ENABLED', true),
    
    // ⭐ عنوان السيرفر الأونلاين
    'central_server_url' => env('CENTRAL_SERVER_URL', 'https://yourdomain.com/api/sync'),
    
    // ⭐ التوكن من السيرفر الأونلاين
    'central_server_token' => env('CENTRAL_SERVER_TOKEN'),
    
    'is_central_server' => env('IS_CENTRAL_SERVER', false),
    
    // معلومات الجهاز
    'device_id' => env('DEVICE_ID', gethostname()),
    'device_name' => env('DEVICE_NAME', 'Unknown Device'),
    
    // باقي الإعدادات...
];
```

---

### 3️⃣ اختبار الاتصال من الجهاز المحلي

```bash
# في PowerShell على Windows

cd C:\Users\mon3em\Desktop\tesr_docker

# اختبار الاتصال بالسيرفر الأونلاين
php artisan tinker
```

```php
// في Tinker
use App\Services\CentralServerService;

$service = app(CentralServerService::class);

// اختبار الاتصال
$result = $service->test();
print_r($result);

// يجب أن ترى:
// [
//     'status' => 'success',
//     'message' => 'Connection successful',
//     'server_time' => '...',
//     'latency' => 250
// ]
```

---

### 4️⃣ تشغيل المزامنة التلقائية على Windows

#### أ. إنشاء ملف Batch للمزامنة
```batch
@echo off
REM save as: C:\sync\sync_factory.bat

cd C:\Users\mon3em\Desktop\tesr_docker
php artisan sync:process
```

#### ب. جدولة المهمة في Task Scheduler

1. افتح **Task Scheduler** (مجدول المهام)
2. اختر **Create Basic Task**
3. الاسم: `Factory Sync`
4. Trigger: **Daily** → Repeat every **5 minutes**
5. Action: **Start a Program**
   - Program: `C:\sync\sync_factory.bat`
6. Finish

---

### 5️⃣ إضافة Dashboard Routes (على الجهاز المحلي)

```php
// routes/web.php

use App\Http\Controllers\SyncDashboardController;

Route::middleware(['auth'])->prefix('sync-dashboard')->group(function () {
    Route::get('/', [SyncDashboardController::class, 'index'])->name('sync.dashboard');
    Route::get('/stats', [SyncDashboardController::class, 'stats'])->name('sync.stats');
    Route::get('/pending', [SyncDashboardController::class, 'pending'])->name('sync.pending');
    Route::get('/failed', [SyncDashboardController::class, 'failed'])->name('sync.failed');
    Route::get('/history', [SyncDashboardController::class, 'history'])->name('sync.history');
    Route::post('/retry/{id}', [SyncDashboardController::class, 'retry'])->name('sync.retry');
    Route::post('/retry-all', [SyncDashboardController::class, 'retryAll'])->name('sync.retry-all');
    Route::delete('/delete/{id}', [SyncDashboardController::class, 'delete'])->name('sync.delete');
    Route::post('/cleanup', [SyncDashboardController::class, 'cleanup'])->name('sync.cleanup');
    Route::get('/chart-data', [SyncDashboardController::class, 'chartData'])->name('sync.chart-data');
});
```

---

## 🔄 سيناريوهات المزامنة

### سيناريو 1: إضافة عامل جديد على الجهاز المحلي

```
1. المستخدم يضيف عامل في الجهاز المحلي (Worker)
2. يتم حفظه في قاعدة البيانات المحلية
3. Syncable trait يضيف السجل إلى pending_syncs
4. كل 5 دقائق، sync:process يرسل البيانات للسيرفر الأونلاين
5. السيرفر الأونلاين يحفظ العامل في قاعدته
6. الأجهزة الأخرى تستلم التحديث عند المزامنة التالية
```

### سيناريو 2: تعديل مادة على السيرفر الأونلاين

```
1. المدير يعدّل مادة من لوحة التحكم الأونلاين
2. يتم حفظ التغيير في قاعدة البيانات المركزية
3. الأجهزة المحلية ترسل pull request كل 5 دقائق
4. تستلم التحديثات وتطبقها على قواعدها المحلية
5. إذا كان هناك تعارض (conflict)، الأحدث يفوز
```

---

## 🛡️ الأمان والحماية

### 1. HTTPS إلزامي
```bash
# تأكد من أن السيرفر الأونلاين يستخدم HTTPS
# استخدم Let's Encrypt مجاناً من cPanel
```

### 2. تشفير التوكنات
```php
// تأكد أن التوكنات مشفرة في config/sync.php
'encryption_enabled' => env('SYNC_ENCRYPTION', true),
```

### 3. IP Whitelist (اختياري)
```php
// في middleware TrackDeviceId
$allowedIps = ['123.45.67.89', '98.76.54.32'];
if (!in_array($request->ip(), $allowedIps)) {
    abort(403, 'Unauthorized IP');
}
```

---

## 🧪 اختبار المزامنة الكاملة

### 1. من الجهاز المحلي
```bash
# أضف سجل اختبار
php artisan tinker
```

```php
use App\Models\Worker;

$worker = Worker::create([
    'worker_code' => 'TEST-001',
    'name' => 'اختبار المزامنة',
    'phone' => '0100000000',
    'position' => 'operator',
    'shift' => 'morning',
    'hourly_rate' => 50,
    'hire_date' => now(),
]);

// تحقق من pending_syncs
\App\Models\PendingSync::latest()->first();
```

### 2. تشغيل المزامنة يدوياً
```bash
php artisan sync:process
```

### 3. تحقق من السيرفر الأونلاين
```bash
# في phpMyAdmin على السيرفر الأونلاين
SELECT * FROM workers WHERE worker_code = 'TEST-001';
SELECT * FROM sync_logs ORDER BY created_at DESC LIMIT 10;
```

---

## 🔍 مراقبة المزامنة

### Dashboard المزامنة
```
http://localhost/sync-dashboard          # الجهاز المحلي
https://yourdomain.com/sync-dashboard    # السيرفر الأونلاين
```

### Logs
```bash
# على الجهاز المحلي
tail -f storage/logs/laravel.log

# على السيرفر الأونلاين (cPanel)
# File Manager → storage/logs/laravel.log
```

---

## ❌ حل المشاكل الشائعة

### المشكلة 1: `Connection refused`
```bash
# تحقق من:
1. أن CENTRAL_SERVER_URL صحيح
2. أن السيرفر الأونلاين يعمل
3. أن Firewall لا يحجب الاتصال

# اختبر:
curl https://yourdomain.com/api/sync/heartbeat
```

### المشكلة 2: `Unauthenticated`
```bash
# تحقق من:
1. أن CENTRAL_SERVER_TOKEN صحيح
2. أن التوكن موجود في personal_access_tokens
3. أن التوكن لم ينتهي

# أعد إنشاء التوكن:
php artisan tinker
$user = User::first();
$token = $user->createToken('Local-Server-1')->plainTextToken;
echo $token;
```

### المشكلة 3: `500 Internal Server Error`
```bash
# على السيرفر الأونلاين، افحص:
tail -n 50 storage/logs/laravel.log

# تحقق من:
1. أن قاعدة البيانات متصلة
2. أن الصلاحيات صحيحة (chmod 775 storage)
3. أن composer dependencies مثبتة
```

---

## 📊 ملخص سريع

| البند | السيرفر الأونلاين | الجهاز المحلي |
|------|-------------------|---------------|
| **URL** | https://yourdomain.com | http://localhost |
| **IS_CENTRAL_SERVER** | true | false |
| **CENTRAL_SERVER_URL** | - | https://yourdomain.com/api/sync |
| **CENTRAL_SERVER_TOKEN** | - | من السيرفر الأونلاين |
| **Database** | MySQL على الاستضافة | MySQL محلي |
| **Sync Direction** | يستقبل من الأجهزة | يرسل للسيرفر |

---

## 📞 الخطوات التالية

1. ✅ رفع المشروع على السيرفر الأونلاين
2. ✅ إنشاء API Token
3. ✅ تعديل `.env` على الأجهزة المحلية
4. ✅ اختبار الاتصال
5. ✅ جدولة المزامنة التلقائية
6. ✅ مراقبة Dashboard

---

**🎉 تم! الآن لديك نظام مزامنة كامل بين الأجهزة المحلية والسيرفر الأونلاين.**
