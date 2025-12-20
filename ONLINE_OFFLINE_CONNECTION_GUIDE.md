# 🌐 دليل ربط الأونلاين/أوفلاين - اتصال السيرفر المحلي بالسيرفر المركزي

## 📋 **نظرة عامة:**

```
┌─────────────────────────────────────┐
│     السيرفر المركزي (أونلاين)        │
│   Central Server (Cloud/VPS)       │
│   https://central-server.com       │
│                                     │
│   - قاعدة بيانات موحدة              │
│   - API للمزامنة                    │
│   - إدارة مركزية                    │
└─────────────────────────────────────┘
            ↑         ↑         ↑
            │         │         │
     Internet   Internet   Internet
            │         │         │
┌───────────┴─┐  ┌────┴────┐  ┌┴────────────┐
│ السيرفر 1   │  │ السيرفر 2│  │ السيرفر 3   │
│ (المصنع)    │  │ (المستودع)│  │ (الفرع)     │
│             │  │          │  │             │
│ - أوفلاين   │  │ - أوفلاين│  │ - أوفلاين   │
│ - مزامنة    │  │ - مزامنة │  │ - مزامنة    │
└─────────────┘  └──────────┘  └─────────────┘
```

---

## 🚀 **الخطوات الكاملة للإعداد:**

### **1️⃣ إعداد السيرفر المركزي (Central Server)**

#### أ) متطلبات السيرفر المركزي:

```bash
# تثبيت Laravel على السيرفر المركزي
# Domain: https://central-server.com
# أو IP: http://192.168.1.100

# تأكد من تشغيل:
- Laravel 11+
- MySQL/PostgreSQL
- SSL Certificate (لـ HTTPS)
```

#### ب) إعداد `.env` على السيرفر المركزي:

```env
APP_NAME="Central Server"
APP_ENV=production
APP_URL=https://central-server.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=central_db
DB_USERNAME=root
DB_PASSWORD=your_password

# تحديد أن هذا هو السيرفر المركزي
IS_CENTRAL_SERVER=true

# API Token للسيرفرات المحلية
SANCTUM_STATEFUL_DOMAINS=localhost,192.168.1.*
```

#### ج) تطبيق Migrations على السيرفر المركزي:

```bash
cd /path/to/central-server
php artisan migrate
php artisan db:seed # إن وجد
```

---

### **2️⃣ إعداد السيرفر المحلي (Local Server) - على Windows**

#### أ) إعداد `.env` على جهاز Windows:

```env
APP_NAME="مصنع - السيرفر المحلي 1"
APP_ENV=local
APP_URL=http://localhost

# Database المحلية
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=local_factory_db
DB_USERNAME=root
DB_PASSWORD=

# ═══════════════════════════════════════════════
# إعدادات الاتصال بالسيرفر المركزي
# ═══════════════════════════════════════════════

# URL السيرفر المركزي (الأونلاين)
CENTRAL_SERVER_URL=https://central-server.com
# أو إذا كان IP مباشر:
# CENTRAL_SERVER_URL=http://192.168.1.100

# API Token (يجب الحصول عليه من السيرفر المركزي)
CENTRAL_SERVER_TOKEN=your-api-token-here

# معرف السيرفر المحلي (فريد لكل جهاز)
LOCAL_SERVER_ID=factory-server-1
LOCAL_SERVER_NAME="مصنع الإنتاج - السيرفر 1"

# هذا ليس السيرفر المركزي
IS_CENTRAL_SERVER=false

# ═══════════════════════════════════════════════
# إعدادات المزامنة
# ═══════════════════════════════════════════════

# تفعيل المزامنة التلقائية
AUTO_SYNC_ENABLED=true

# كل كم دقيقة تحدث المزامنة
AUTO_SYNC_INTERVAL=1

# عدد السجلات في كل دفعة
SYNC_BATCH_SIZE=100

# عدد محاولات إعادة المحاولة
SYNC_MAX_RETRIES=3

# Timeout للاتصال (ثواني)
SYNC_CONNECTION_TIMEOUT=30

# التحقق من SSL (false للتطوير المحلي)
SYNC_VERIFY_SSL=false
```

#### ب) تطبيق Migrations على السيرفر المحلي:

```bash
cd C:\Users\mon3em\Desktop\tesr_docker
php artisan migrate
```

---

### **3️⃣ إنشاء API Token على السيرفر المركزي**

#### الطريقة 1: يدوياً عبر Database

```sql
-- على السيرفر المركزي
USE central_db;

INSERT INTO personal_access_tokens (
    tokenable_type,
    tokenable_id,
    name,
    token,
    abilities,
    created_at,
    updated_at
) VALUES (
    'App\\Models\\User',
    1, -- user_id للمدير
    'Local Server Token',
    'your-secret-token-here', -- يجب أن يكون hash
    '["*"]',
    NOW(),
    NOW()
);
```

#### الطريقة 2: عبر Artisan Command (مستحسن)

على السيرفر المركزي:

```bash
php artisan tinker

# ثم
$user = User::first(); // أو المستخدم المطلوب
$token = $user->createToken('Local Server 1')->plainTextToken;
echo $token;
// انسخ هذا الـ Token واستخدمه في .env
```

---

### **4️⃣ إضافة Routes للـ Dashboard**

```php
// routes/web.php

use App\Http\Controllers\SyncDashboardController;

Route::middleware(['auth'])->group(function () {
    Route::prefix('sync-dashboard')->name('sync-dashboard.')->group(function () {
        Route::get('/', [SyncDashboardController::class, 'index'])->name('index');
        Route::get('/stats', [SyncDashboardController::class, 'stats'])->name('stats');
        Route::get('/pending', [SyncDashboardController::class, 'pending'])->name('pending');
        Route::get('/failed', [SyncDashboardController::class, 'failed'])->name('failed');
        Route::get('/history', [SyncDashboardController::class, 'history'])->name('history');
        Route::get('/users', [SyncDashboardController::class, 'users'])->name('users');
        Route::get('/chart-data', [SyncDashboardController::class, 'chartData'])->name('chart-data');
        Route::post('/retry/{id}', [SyncDashboardController::class, 'retry'])->name('retry');
        Route::delete('/delete/{id}', [SyncDashboardController::class, 'delete'])->name('delete');
        Route::post('/retry-all', [SyncDashboardController::class, 'retryAll'])->name('retry-all');
        Route::post('/cleanup', [SyncDashboardController::class, 'cleanup'])->name('cleanup');
    });
});
```

---

### **5️⃣ اختبار الاتصال**

#### أ) Command لاختبار الاتصال:

```bash
php artisan tinker

# ثم
App\Services\CentralServerService::test();
```

**النتيجة المتوقعة:**
```php
[
    "connection" => true,
    "authentication" => true,
    "push" => true,
    "pull" => true
]
```

#### ب) من المتصفح:

```
http://localhost/sync-dashboard
```

---

### **6️⃣ تشغيل المزامنة التلقائية**

#### على Windows (للتطوير):

```bash
# Terminal 1: تشغيل المشروع
php artisan serve

# Terminal 2: تشغيل Scheduler
php artisan schedule:work
```

#### على Windows (للإنتاج):

استخدم **Task Scheduler**:

1. افتح Task Scheduler
2. Create Basic Task
3. الاسم: "Laravel Sync"
4. Trigger: عند بدء التشغيل
5. Action: Start a program
6. Program: `C:\php\php.exe`
7. Arguments: `C:\path\to\project\artisan schedule:work`

---

### **7️⃣ سيناريوهات الاستخدام**

#### السيناريو 1: إنشاء سجل جديد (أونلاين)

```php
// السيرفر المحلي متصل بالإنترنت
$material = Material::create([
    'name' => 'مادة جديدة',
    'barcode' => '12345'
]);

// ✅ تلقائياً:
// 1. يتم حفظ السجل محلياً
// 2. يضاف لـ pending_syncs
// 3. Scheduler يرسله للسيرفر المركزي خلال دقيقة
// 4. السيرفر المركزي يستقبله ويحفظه
```

#### السيناريو 2: إنشاء سجل (أوفلاين)

```php
// السيرفر المحلي بدون إنترنت
$material = Material::create([
    'name' => 'مادة أوفلاين',
    'barcode' => '54321'
]);

// ✅ تلقائياً:
// 1. يتم حفظ السجل محلياً
// 2. يضاف لـ pending_syncs مع status = 'pending'
// 3. عند عودة الإنترنت: Scheduler يرسله تلقائياً
```

#### السيناريو 3: سحب البيانات من السيرفر المركزي

```bash
# يدوياً:
php artisan sync:process-pending

# أو تلقائياً كل دقيقة عبر Scheduler
```

---

### **8️⃣ مراقبة المزامنة**

#### Dashboard Web:
```
http://localhost/sync-dashboard
```

#### Command Line:
```bash
# عرض الإحصائيات
php artisan sync:process-pending --help

# معالجة يدوية
php artisan sync:process-pending

# معالجة لمستخدم محدد
php artisan sync:process-pending --user=1
```

---

## 🔧 **حل المشاكل الشائعة:**

### مشكلة 1: Connection timeout

```env
# زيادة Timeout
SYNC_CONNECTION_TIMEOUT=60
```

### مشكلة 2: SSL verification failed

```env
# تعطيل SSL للتطوير
SYNC_VERIFY_SSL=false
```

### مشكلة 3: Authentication failed

```bash
# تأكد من Token صحيح
php artisan tinker
App\Services\CentralServerService::test()
```

### مشكلة 4: لا يتم المزامنة تلقائياً

```bash
# تأكد من تشغيل Scheduler
php artisan schedule:work

# تأكد من الإعدادات
php artisan config:clear
php artisan cache:clear
```

---

## 📊 **الخلاصة:**

✅ **السيرفر المركزي:** جاهز على الإنترنت  
✅ **السيرفر المحلي:** يعمل على Windows  
✅ **الاتصال:** عبر API + Token  
✅ **المزامنة:** تلقائية كل دقيقة  
✅ **Dashboard:** متوفر للمراقبة  
✅ **الأوفلاين:** يحفظ محلياً ويرسل عند عودة النت  

**🎉 النظام جاهز للعمل!**
