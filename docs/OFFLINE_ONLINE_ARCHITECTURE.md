# 🏭 نظام العمل الأوفلاين والأونلاين لمصنع الحديد

## 📋 جدول المحتويات
1. [الهدف العام](#الهدف-العام)
2. [المشكلة والحل](#المشكلة-والحل)
3. [أفضل 3 طرق للتنفيذ](#أفضل-3-طرق-للتنفيذ)
4. [الطريقة المقترحة](#الطريقة-المقترحة-pwa--laravel)
5. [البنية التقنية](#البنية-التقنية-النهائية)
6. [تعليمات التنفيذ](#تعليمات-التنفيذ)
7. [معايير الأمان](#معايير-الأمان)

---

## 🎯 الهدف العام

بناء نظام إدارة إنتاج الحديد يعمل في حالتين:

| الحالة | الوصف | المتطلبات |
|-------|-------|----------|
| **🟢 أونلاين** | متصل بالسيرفر المركزي | بيانات حية + مزامنة فورية |
| **🔴 أوفلاين** | بدون إنترنت | يكمل العمل بدون تأثر |
| **🔄 إعادة الاتصال** | عودة الإنترنت | مزامنة تلقائية للبيانات |

**🔧 التحديات:**
- ✋ أكثر من 50 جهاز متصل
- ✋ إمكانية انقطاع الإنترنت في أي لحظة
- ✋ عدم فقدان البيانات
- ✋ مزامنة ذكية بدون تعارضات
- ✋ أداء سريع حتى بدون إنترنت

---

## 🧩 أفضل 3 طرق للتنفيذ

### 🔹 الطريقة 1: Laravel محلي + مزامنة API

#### ✅ آلية العمل:
```
┌─────────────────────────────────────────────────────┐
│                    السيرفر المركزي                    │
│              Laravel + MySQL Central                │
└─────────────────────────────────────────────────────┘
                         ⬆️ ⬇️ API
        ┌────────────────────────────────────┐
        │   50+ أجهزة (كل جهاز مستقل)         │
        │                                    │
        │ ┌──────────────────────────────┐  │
        │ │ Device 1:                     │  │
        │ │ - Laravel Local              │  │
        │ │ - SQLite/MySQL Local         │  │
        │ │ - Sync Queue Jobs            │  │
        │ └──────────────────────────────┘  │
        │                                    │
        │ ┌──────────────────────────────┐  │
        │ │ Device 2-50: (نفس البنية)   │  │
        │ └──────────────────────────────┘  │
        └────────────────────────────────────┘
```

#### 🛠️ التنفيذ الفني:

```php
// 1. إنشاء جدول لتسجيل العمليات
php artisan make:migration create_sync_queue_table

// المحتوى:
Schema::create('sync_queue', function (Blueprint $table) {
    $table->id();
    $table->uuid('sync_id')->unique(); // معرّف فريد بدون تكرار
    $table->enum('action', ['create', 'update', 'delete']);
    $table->string('model_type'); // مثل: 'App\Models\Material'
    $table->json('data');
    $table->boolean('synced')->default(false);
    $table->timestamp('synced_at')->nullable();
    $table->timestamps();
});

// 2. إضافة Event Listener لتسجيل كل عملية
php artisan make:listener RecordSyncEvent

// المحتوى:
public function handle(ModelChanged $event)
{
    SyncQueue::create([
        'sync_id' => Str::uuid(),
        'action' => $event->action,
        'model_type' => get_class($event->model),
        'data' => $event->model->toArray(),
        'synced' => false
    ]);
}

// 3. Job لإرسال البيانات للسيرفر
php artisan make:job SyncToServer

// المحتوى:
class SyncToServer implements ShouldQueue
{
    public function handle()
    {
        $pending = SyncQueue::where('synced', false)
            ->limit(100) // في دفعات
            ->get();

        foreach ($pending as $sync) {
            try {
                $response = Http::post('https://central-server/api/sync', [
                    'sync_id' => $sync->sync_id,
                    'action' => $sync->action,
                    'model_type' => $sync->model_type,
                    'data' => $sync->data,
                    'device_id' => config('app.device_id'),
                    'timestamp' => now()
                ]);

                if ($response->successful()) {
                    $sync->update([
                        'synced' => true,
                        'synced_at' => now()
                    ]);
                }
            } catch (Exception $e) {
                // محاولة لاحقة
                Log::error('Sync failed', ['sync_id' => $sync->sync_id]);
            }
        }
    }
}

// 4. جدولة Job (Scheduler)
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // محاولة المزامنة كل 5 دقائق
    $schedule->job(new SyncToServer())
        ->everyFiveMinutes()
        ->withoutOverlapping();

    // أو عند توفر الاتصال مباشرة
    $schedule->call(function () {
        if ($this->isOnline()) {
            dispatch(new SyncToServer());
        }
    })->everyMinute();
}
```

#### ✅ المميزات:
- ✔️ يعمل بدون إنترنت تماماً
- ✔️ موثوق وآمن جداً
- ✔️ تحكم كامل بالبيانات

#### ❌ العيوب:
- ❌ حجم كبير على كل جهاز
- ❌ تحديث النظام صعب يدويًا
- ❌ استهلاك تخزين أكثر

---

### 🔹 الطريقة 2: PWA + Laravel Online ⭐ **الأفضل للمصانع**

#### ✅ آلية العمل:
```
┌──────────────────────────────────────────────────┐
│         السيرفر المركزي (Laravel)               │
│         https://production.factory.com            │
│         MySQL + API + Authentication             │
└──────────────────────────────────────────────────┘
                    ⬆️ ⬇️ API/REST
        ┌────────────────────────────────┐
        │   50+ أجهاز (متصفح Chrome)      │
        │                                 │
        │ ┌────────────────────────────┐ │
        │ │ PWA (Progressive Web App)  │ │
        │ │ ├─ IndexedDB (كاش محلي)    │ │
        │ │ ├─ Service Worker          │ │
        │ │ ├─ Cache Manifest          │ │
        │ │ └─ Offline Support         │ │
        │ └────────────────────────────┘ │
        └────────────────────────────────┘
```

#### 🛠️ التنفيذ الفني:

```php
// 1. تثبيت مكتبة PWA
composer require erag/laravel-pwa

// 2. نشر التكوين
php artisan vendor:publish --provider="Erag\LaravelPWA\PWAServiceProvider"

// 3. ملف Manifest (public/manifest.json)
{
  "name": "نظام إدارة مصنع الحديد",
  "short_name": "مصنع الحديد",
  "description": "نظام متكامل للإنتاج والمخزن والجودة",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#20b2aa",
  "scope": "/",
  "icons": [
    {
      "src": "/assets/images/icons/icon-72x72.png",
      "sizes": "72x72",
      "type": "image/png"
    },
    {
      "src": "/assets/images/icons/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/assets/images/icons/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ],
  "screenshots": [
    {
      "src": "/assets/images/screenshots/screenshot1.png",
      "sizes": "540x720",
      "type": "image/png"
    }
  ]
}

// 4. Service Worker (public/js/service-worker.js)
const CACHE_NAME = 'factory-app-v1';
const urlsToCache = [
  '/',
  '/css/app.css',
  '/js/app.js',
  '/assets/images/logo.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') {
    return;
  }

  event.respondWith(
    // Try network first
    fetch(event.request)
      .then(response => {
        const responseClone = response.clone();
        caches.open(CACHE_NAME).then(cache => {
          cache.put(event.request, responseClone);
        });
        return response;
      })
      .catch(() => {
        // Fallback to cache if offline
        return caches.match(event.request)
          .then(response => response || caches.match('/offline'));
      })
  );
});

// 5. IndexedDB للبيانات (resources/js/offline-storage.js)
class OfflineStorage {
  constructor() {
    this.db = null;
    this.initDB();
  }

  initDB() {
    return new Promise((resolve, reject) => {
      const request = indexedDB.open('FactoryDB', 1);

      request.onerror = () => reject(request.error);
      request.onsuccess = () => {
        this.db = request.result;
        resolve();
      };

      request.onupgradeneeded = (event) => {
        const db = event.target.result;

        // إنشاء جداول
        if (!db.objectStoreNames.contains('materials')) {
          db.createObjectStore('materials', { keyPath: 'id' });
        }
        if (!db.objectStoreNames.contains('production')) {
          db.createObjectStore('production', { keyPath: 'id' });
        }
        if (!db.objectStoreNames.contains('sync_queue')) {
          db.createObjectStore('sync_queue', { keyPath: 'id', autoIncrement: true });
        }
      };
    });
  }

  async saveData(storeName, data) {
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(storeName, 'readwrite');
      const store = transaction.objectStore(storeName);
      const request = store.put(data);

      request.onsuccess = () => resolve(request.result);
      request.onerror = () => reject(request.error);
    });
  }

  async getData(storeName, id) {
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(storeName, 'readonly');
      const store = transaction.objectStore(storeName);
      const request = store.get(id);

      request.onsuccess = () => resolve(request.result);
      request.onerror = () => reject(request.error);
    });
  }

  async getAllData(storeName) {
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(storeName, 'readonly');
      const store = transaction.objectStore(storeName);
      const request = store.getAll();

      request.onsuccess = () => resolve(request.result);
      request.onerror = () => reject(request.error);
    });
  }

  async deleteData(storeName, id) {
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction(storeName, 'readwrite');
      const store = transaction.objectStore(storeName);
      const request = store.delete(id);

      request.onsuccess = () => resolve();
      request.onerror = () => reject(request.error);
    });
  }
}

// 6. نظام المزامنة (resources/js/sync-manager.js)
class SyncManager {
  constructor(offlineStorage) {
    this.storage = offlineStorage;
    this.syncInProgress = false;
  }

  async addToSyncQueue(action, model, data) {
    await this.storage.saveData('sync_queue', {
      action: action,
      model: model,
      data: data,
      timestamp: new Date(),
      synced: false
    });
  }

  async syncWithServer() {
    if (this.syncInProgress || !navigator.onLine) {
      return;
    }

    this.syncInProgress = true;
    try {
      const queueItems = await this.storage.getAllData('sync_queue');
      const unsynced = queueItems.filter(item => !item.synced);

      for (const item of unsynced) {
        try {
          const response = await fetch('/api/sync', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Authorization': 'Bearer ' + this.getToken()
            },
            body: JSON.stringify({
              action: item.action,
              model: item.model,
              data: item.data,
              timestamp: item.timestamp
            })
          });

          if (response.ok) {
            item.synced = true;
            await this.storage.saveData('sync_queue', item);
            this.showNotification('✅ تم مزامنة البيانات بنجاح');
          }
        } catch (error) {
          console.error('Sync failed:', error);
        }
      }
    } finally {
      this.syncInProgress = false;
    }
  }

  getToken() {
    return localStorage.getItem('auth_token');
  }

  showNotification(message) {
    if ('Notification' in window && Notification.permission === 'granted') {
      new Notification('مصنع الحديد', { body: message });
    }
  }
}

// 7. في master.blade.php
<meta name="manifest" content="/manifest.json">
<meta name="theme-color" content="#20b2aa">

<script>
  // تسجيل Service Worker
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/js/service-worker.js')
      .then(reg => console.log('Service Worker registered'))
      .catch(err => console.log('Service Worker registration failed'));
  }

  // إنشاء مثيل من نظام التخزين والمزامنة
  const offlineStorage = new OfflineStorage();
  const syncManager = new SyncManager(offlineStorage);

  // مراقبة الاتصال بالإنترنت
  window.addEventListener('online', () => {
    console.log('🟢 متصل بالإنترنت');
    syncManager.syncWithServer(); // محاولة مزامنة فورية
  });

  window.addEventListener('offline', () => {
    console.log('🔴 بدون إنترنت - سيتم حفظ البيانات محليًا');
  });

  // محاولة المزامنة كل 5 دقائق
  setInterval(() => syncManager.syncWithServer(), 5 * 60 * 1000);
</script>

// 8. API Endpoint (routes/api.php)
Route::post('/sync', function (Request $request) {
    $validated = $request->validate([
        'action' => 'required|in:create,update,delete',
        'model' => 'required|string',
        'data' => 'required|array',
        'timestamp' => 'required|date'
    ]);

    $user = $request->user();
    $deviceId = $request->header('Device-ID');

    // معالجة كل نوع
    match($validated['action']) {
        'create' => $this->handleCreate($validated, $user, $deviceId),
        'update' => $this->handleUpdate($validated, $user, $deviceId),
        'delete' => $this->handleDelete($validated, $user, $deviceId),
    };

    return response()->json(['success' => true, 'message' => 'تمت المزامنة بنجاح']);
})->middleware('auth:api');
```

#### ✅ المميزات:
- ✔️ لا تحتاج تنصيب على الأجهزة
- ✔️ تحديث تلقائي من السيرفر
- ✔️ خفيفة وسريعة
- ✔️ مناسبة لـ 50+ جهاز
- ✔️ آمنة وموثوقة

#### ❌ العيوب:
- ❌ تحتاج متصفح حديث
- ❌ تصميم Frontend متطور

---

### 🔹 الطريقة 3: Flutter Desktop + Laravel API

#### ✅ الوصف:
تطبيق سطح مكتب (Desktop App) يتواصل مع Laravel API

#### ✅ المميزات:
- ✔️ تطبيق احترافي فعلي
- ✔️ تحكم كامل بالتخزين
- ✔️ أداء عالي

#### ❌ العيوب:
- ❌ أعلى تكلفة تطوير
- ❌ تحتاج خبرة Flutter

---

## 🌟 الطريقة المقترحة: PWA + Laravel

### 🎯 لماذا اخترنا هذه الطريقة؟

| المعيار | الطريقة 1 | الطريقة 2 (المقترحة) | الطريقة 3 |
|--------|---------|------------------|---------|
| سهولة التنصيب | ❌ صعبة | ✅ سهلة جداً | ⚠️ متوسطة |
| الأداء | ⚠️ متوسط | ✅ ممتاز | ✅ ممتاز |
| مناسبة 50+ جهاز | ❌ لا | ✅ نعم | ⚠️ نعم |
| التحديث الآلي | ❌ لا | ✅ نعم | ✅ نعم |
| التكلفة | ⚠️ متوسطة | ✅ منخفضة | ❌ عالية |
| الأوفلاين | ✅ كامل | ✅ كامل | ✅ كامل |

---

## 🏗️ البنية التقنية النهائية

```
┌─────────────────────────────────────────────────────────────┐
│                   🌐 Internet / Network                      │
└─────────────────────────────────────────────────────────────┘
                            ⬆️ ⬇️
┌─────────────────────────────────────────────────────────────┐
│          🖥️ Central Server (Linode / AWS / DigitalOcean)    │
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ Laravel Application                                    │ │
│  │ ├─ API Endpoints (/api/*)                            │ │
│  │ ├─ Authentication (JWT)                              │ │
│  │ ├─ Database Operations                               │ │
│  │ └─ Sync Logic                                        │ │
│  └────────────────────────────────────────────────────────┘ │
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ Database                                               │ │
│  │ ├─ mysql: production_main                            │ │
│  │ ├─ Tables (50+ جدول)                                │ │
│  │ └─ Backups (يومي)                                     │ │
│  └────────────────────────────────────────────────────────┘ │
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ Analytics & Monitoring                                │ │
│  │ ├─ Sentry (Error Tracking)                           │ │
│  │ ├─ New Relic (Performance)                           │ │
│  │ └─ Log Viewer                                        │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            ⬆️ ⬇️
        ┌──────────────────────────────────────┐
        │   50+ Client Devices (Chrome)        │
        │                                      │
        │  ┌──────────────────────────────┐  │
        │  │ PWA Application              │  │
        │  │ ├─ HTML/CSS/JS (Cached)     │  │
        │  │ ├─ Service Worker           │  │
        │  │ └─ Offline Pages            │  │
        │  └──────────────────────────────┘  │
        │                                      │
        │  ┌──────────────────────────────┐  │
        │  │ IndexedDB Storage            │  │
        │  │ ├─ Materials                 │  │
        │  │ ├─ Production Data           │  │
        │  │ ├─ Quality Control           │  │
        │  │ └─ Sync Queue                │  │
        │  └──────────────────────────────┘  │
        │                                      │
        │  ┌──────────────────────────────┐  │
        │  │ Sync Manager                 │  │
        │  │ ├─ Queue Management          │  │
        │  │ ├─ Conflict Resolution       │  │
        │  │ └─ Notifications             │  │
        │  └──────────────────────────────┘  │
        └──────────────────────────────────────┘
```

### 📊 مكونات النظام الرئيسية:

| المكون | التقنية | الدور |
|--------|---------|------|
| **السيرفر** | Laravel 11 + MySQL 8 | معالجة وتخزين البيانات |
| **API** | RESTful (JSON) | التواصل بين الأجهزة والسيرفر |
| **الأمان** | JWT + CORS | حماية البيانات والعمليات |
| **الواجهة** | HTML5 + Vue.js | تجربة المستخدم |
| **التخزين المحلي** | IndexedDB | حفظ مؤقت للبيانات |
| **المزامنة** | Sync Manager JS | ربط البيانات المحلية بالسيرفر |
| **الإشعارات** | Firebase / OneSignal | تنبيهات فورية |
| **المراقبة** | Sentry | تتبع الأخطاء |

---

## 🛠️ تعليمات التنفيذ

### المرحلة 1️⃣: إعداد السيرفر المركزي

```bash
# 1. إنشاء تطبيق Laravel جديد
composer create-project laravel/laravel factory-server

# 2. إعدادات .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=factory_production
DB_USERNAME=root
DB_PASSWORD=

# 3. تثبيت المكتبات المهمة
composer require tymon/jwt-auth
composer require barryvdh/laravel-cors
composer require spatie/laravel-activitylog

# 4. إنشاء جداول الهيكل الأساسي
php artisan migrate
php artisan db:seed

# 5. تثبيت JWT
php artisan jwt:secret
```

### المرحلة 2️⃣: بناء API Endpoints

```php
// routes/api.php

Route::middleware('auth:api')->group(function () {
    // المواد الخام (Warehouse)
    Route::apiResource('materials', MaterialController::class);

    // الإنتاج (Production)
    Route::apiResource('production', ProductionController::class);

    // المزامنة
    Route::post('/sync', SyncController::class);
    Route::get('/sync-status', SyncStatusController::class);

    // الإحصائيات
    Route::get('/statistics', StatisticsController::class);

    // الجودة
    Route::apiResource('quality-checks', QualityCheckController::class);
});

// المصادقة
Route::post('/login', LoginController::class);
Route::post('/logout', LogoutController::class);
Route::post('/refresh', RefreshController::class);
```

### المرحلة 3️⃣: تطوير Frontend (PWA)

```bash
# 1. إضافة Support PWA
composer require erag/laravel-pwa

# 2. إنشاء manifest.json
php artisan pwa:publish

# 3. إضافة Service Worker
# انظر الكود في القسم السابق

# 4. تكوين Cache Manifest
# تحديث في vite.config.js
```

### المرحلة 4️⃣: تفعيل نظام المزامنة

```javascript
// مثال على استخدام الـ Sync Manager في الفرونت إند
const syncManager = new SyncManager(offlineStorage);

// عند إضافة مادة جديدة
document.getElementById('addMaterial').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);

    // حفظ محليًا أولاً
    await offlineStorage.saveData('materials', data);

    // إضافة للـ sync queue
    await syncManager.addToSyncQueue('create', 'Material', data);

    // محاولة المزامنة فوراً إذا كان الاتصال متوفراً
    if (navigator.onLine) {
        await syncManager.syncWithServer();
    }

    showSuccess('تم حفظ المادة بنجاح');
});
```

### المرحلة 5️⃣: اختبار الأوفلاين

```javascript
// اختبار محاكاة انقطاع الإنترنت في DevTools
// Ctrl+Shift+I > Network > Throttling > Offline

// أو محاكاة برمجية
offline();
// النظام يستمر في العمل من IndexedDB

online();
// المزامنة تبدأ تلقائياً
```

---

## 🔐 معايير الأمان

### 1️⃣ المصادقة والتفويض

```php
// استخدام JWT Tokens
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6'
    ]);

    if (!$token = Auth::attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory()->getTTL() * 60
    ]);
});

// التحقق من كل طلب
Route::middleware('auth:api')->get('/user', function () {
    return auth()->user();
});
```

### 2️⃣ تشفير البيانات

```php
// تشفير بيانات حساسة قبل الحفظ
use Illuminate\Support\Facades\Crypt;

$encrypted = Crypt::encryptString($sensitiveData);
$decrypted = Crypt::decryptString($encrypted);
```

### 3️⃣ التحقق من صحة البيانات

```php
// Validation Rules
$validated = $request->validate([
    'material_name' => 'required|string|max:255',
    'quantity' => 'required|numeric|min:0',
    'unit' => 'required|in:kg,ton,piece',
    'supplier_id' => 'required|exists:suppliers,id'
]);
```

### 4️⃣ Conflict Resolution

```php
// عند التعارض بين تحديثين
class SyncConflictResolver
{
    public function resolve($local, $remote, $timestamp)
    {
        // الأحدث يفوز
        if ($remote['updated_at'] > $local['updated_at']) {
            return $remote;
        }

        // أو تطبيق منطق مخصص
        if ($this->isValidUpdate($remote)) {
            return $remote;
        }

        return $local;
    }
}
```

---

## 📋 خطة التنفيذ الزمنية

### ⏱️ الأسبوع 1-2: الإعداد الأساسي
- ✅ إعداد السيرفر المركزي
- ✅ إنشاء قاعدة البيانات الرئيسية
- ✅ بناء API الأساسية

### ⏱️ الأسبوع 3-4: الـ Frontend
- ✅ تطوير واجهة PWA
- ✅ إضافة Service Worker
- ✅ تطبيق IndexedDB

### ⏱️ الأسبوع 5-6: نظام المزامنة
- ✅ بناء Sync Manager
- ✅ اختبار الصراعات (Conflicts)
- ✅ المزامنة الآلية

### ⏱️ الأسبوع 7: الأمان والاختبار
- ✅ JWT Authentication
- ✅ اختبار الأوفلاين
- ✅ اختبار 50+ جهاز

### ⏱️ الأسبوع 8: الإطلاق والمراقبة
- ✅ الإطلاق التدريجي
- ✅ مراقبة الأداء
- ✅ دعم المستخدمين

---

## 🚀 الخطوات التالية

1. **اختبار الفكرة**: إنشاء نسخة نموذجية (POC)
2. **جمع الملاحظات**: من فريق المصنع
3. **التطوير الكامل**: تطبيق الحل النهائي
4. **التدريب**: تدريب المستخدمين
5. **الدعم والصيانة**: مراقبة مستمرة

---

## 📞 الدعم والمراقبة

```bash
# مراقبة الأخطاء
Sentry Dashboard: https://sentry.io

# تتبع الأداء
New Relic: https://newrelic.com

# السجلات
Laravel Logs: storage/logs/

# النسخ الاحتياطي
Database Backups: يومي / 7 ساعات
Code Repository: GitHub with auto-deploy
```

---

**✨ النتيجة النهائية: نظام موثوق، آمن، وسريع يعمل بكفاءة مع 50+ جهاز في أي ظرف!**
