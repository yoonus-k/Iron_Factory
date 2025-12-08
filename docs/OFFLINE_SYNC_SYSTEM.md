# نظام المزامنة بين الأوفلاين والأونلاين

## 📌 المحتويات
1. [مقدمة](#مقدمة)
2. [معمارية النظام](#معمارية-النظام)
3. [طرق التطبيق](#طرق-التطبيق)
4. [قاعدة البيانات](#قاعدة-البيانات)
5. [الكود التطبيقي](#الكود-التطبيقي)
6. [نظام المدير والموظفين](#نظام-المدير-والموظفين)
7. [خطوات التطبيق](#خطوات-التطبيق)

---

## 🎯 مقدمة

هذا الدليل يشرح كيفية بناء نظام متقدم للعمل الأوفلاين (بدون إنترنت) مع المزامنة التلقائية عند عودة الاتصال.

### المتطلبات:
- Laravel 11+
- Vue 3
- IndexedDB / SQLite محلي
- Service Worker
- Axios

---

## 🏗️ معمارية النظام

### الصورة الكاملة:

```
┌─────────────────────────────────────────────────────────────┐
│                      السرفر المركزي                          │
│              (Central Database Server)                       │
│                  (قاعدة بيانات موحدة)                         │
└─────────────────────────────────────────────────────────────┘
         ↑                    ↑                    ↑
         │                    │                    │
    [API Pull]           [API Pull]          [API Pull]
         │                    │                    │
    ┌────────────┐      ┌────────────┐      ┌────────────┐
    │   المدير     │      │  الموظف 1   │      │  الموظف 2   │
    │  (Manager)  │      │  (Staff 1)  │      │  (Staff 2)  │
    │             │      │             │      │             │
    │ LocalDB 1   │      │ LocalDB 1   │      │ LocalDB 1   │
    │ (SQLite)    │      │ (SQLite)    │      │ (SQLite)    │
    │             │      │             │      │             │
    │ - Materials │      │ - Materials │      │ - Materials │
    │ - Delivery  │      │ - Delivery  │      │ - Delivery  │
    │ - Staff A   │      │ - Staff A   │      │ - Staff A   │
    │ - Staff B   │      │ - Staff B   │      │ - Staff B   │
    │ - Staff C   │      │ - Staff C   │      │ - Staff C   │
    └────────────┘      └────────────┘      └────────────┘
```

### تدفق البيانات:

```
الموظف 1:
  يدخل مادة → LocalDB → API → Central DB → SyncHistory
                                    ↓
الموظف 2:
  يدخل مادة → LocalDB → API → Central DB → SyncHistory
                                    ↓
المدير:
  يضغط "تحديث" → يسحب من API → يحفظ في LocalDB
  → يشتغل أوفلاين على كل البيانات → يبحث/يصفي/يعمل تقارير
```

---

## 🔄 طرق التطبيق

### الطريقة 1: Progressive Web App (PWA) ⭐ الأفضل للويب

**الآلية:**
- Service Worker يحفظ صفحات + CSS + JS في الجهاز
- عند الانقطاع: الموظف يشتغل بالبيانات المحفوظة
- IndexedDB تحفظ البيانات المدخلة بشكل محلي
- عند العودة: تزامن تلقائي مع السرفر

**المزامنة:**
```
1. الموظف يدخل بيانات (العملية محفوظة محلياً فوراً) ✅
2. الموظف يضغط حفظ ✅
3. النظام يحاول الإرسال للسرفر
4. إذا الإنترنت قطعت → البيانات تبقى محفوظة محلياً
5. عند عودة الإنترنت → إرسال تلقائي ✅
```

### الطريقة 2: قاعدة بيانات محلية (SQLite) ⭐ الأقوى

**الآلية:**
- كل جهاز فيه نسخة محلية من البيانات
- الموظف يشتغل على البيانات المحلية 100%
- عند الاتصال: مزامنة دورية (كل 5 دقائق مثلاً)

**المزامنة:**
```
1. الموظف يدخل البيانات محلياً (بدون انتظار السرفر)
2. في الخلفية: النظام ياخذ التغييرات المحلية
3. يرسلها للسرفر عند توفر الإنترنت
4. السرفر يرد بالتحديثات الجديدة
5. يتم دمج البيانات المحلية مع السرفر
```

### الطريقة 3: Hybrid (مختلط) ⭐ الأكثر واقعية

**الآلية:**
- الموظف يشتغل أونلاين طبيعي
- عند الانقطاع: تحويل تلقائي لـ أوفلاين
- حفظ البيانات محلياً
- عند العودة: مزامنة

---

## 🗄️ قاعدة البيانات

### 1. جدول SyncLogs (تتبع المزامنة)

```sql
CREATE TABLE sync_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    
    -- نوع البيانات اللي تمت مزامنتها
    entity_type VARCHAR(50),  -- مثل: material, delivery_note, warehouse
    entity_id BIGINT,         -- معرف البيانة
    
    -- حالة المزامنة
    status ENUM('pending', 'synced', 'failed') DEFAULT 'pending',
    
    -- تفاصيل الخطأ
    error_message TEXT NULL,
    
    -- الأوقات
    created_at TIMESTAMP,
    synced_at TIMESTAMP NULL,
    
    -- تتبع البيانات
    data_payload JSON,        -- البيانات اللي تمت مزامنتها
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_status (user_id, status),
    INDEX idx_entity (entity_type, entity_id)
);
```

### 2. جدول SyncHistory (للمدير - سجل مركزي)

```sql
CREATE TABLE sync_history (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    
    -- من الموظف/المدير
    user_id BIGINT,
    user_type ENUM('staff', 'manager'),
    
    -- نوع البيانة
    entity_type VARCHAR(50),  -- material, delivery_note, etc
    entity_id BIGINT,
    
    -- البيانة الفعلية (JSON)
    data JSON,
    
    -- معلومات المزامنة
    action ENUM('create', 'update', 'delete'),
    synced_from_local DATETIME,
    synced_to_server DATETIME,
    
    -- للمدير: متى سحبها
    pulled_by_manager_at DATETIME NULL,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_user (user_id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_synced (synced_to_server)
);
```

### 3. إضافة أعمدة للجداول الموجودة

```sql
-- في جدول materials
ALTER TABLE materials ADD COLUMN sync_status ENUM('pending', 'synced', 'failed') DEFAULT 'synced';
ALTER TABLE materials ADD COLUMN is_synced BOOLEAN DEFAULT TRUE;
ALTER TABLE materials ADD COLUMN synced_at TIMESTAMP NULL;

-- في جدول delivery_notes
ALTER TABLE delivery_notes ADD COLUMN sync_status ENUM('pending', 'synced', 'failed') DEFAULT 'synced';
ALTER TABLE delivery_notes ADD COLUMN is_synced BOOLEAN DEFAULT TRUE;
ALTER TABLE delivery_notes ADD COLUMN synced_at TIMESTAMP NULL;

-- في جدول warehouse_transactions
ALTER TABLE warehouse_transactions ADD COLUMN sync_status ENUM('pending', 'synced', 'failed') DEFAULT 'synced';
ALTER TABLE warehouse_transactions ADD COLUMN is_synced BOOLEAN DEFAULT TRUE;
ALTER TABLE warehouse_transactions ADD COLUMN synced_at TIMESTAMP NULL;
```

---

## 💻 الكود التطبيقي

### 1. Model: SyncLog

**ملف:** `app/Models/SyncLog.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncLog extends Model
{
    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
        'status',
        'error_message',
        'data_payload',
        'synced_at'
    ];

    protected $casts = [
        'data_payload' => 'json',
        'synced_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // دالة لحفظ بيانة جديدة
    public static function logSync($userId, $entityType, $entityId, $data, $status = 'pending')
    {
        return self::create([
            'user_id' => $userId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'data_payload' => $data,
            'status' => $status,
        ]);
    }

    // دالة لتحديث الحالة
    public function markAsSynced()
    {
        $this->update([
            'status' => 'synced',
            'synced_at' => now(),
        ]);
    }

    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }
}
```

### 2. Model: SyncHistory

**ملف:** `app/Models/SyncHistory.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncHistory extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'entity_type',
        'entity_id',
        'data',
        'action',
        'synced_from_local',
        'synced_to_server',
        'pulled_by_manager_at',
    ];

    protected $casts = [
        'data' => 'json',
        'synced_from_local' => 'datetime',
        'synced_to_server' => 'datetime',
        'pulled_by_manager_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // جدول للبحث السريع
    protected $table = 'sync_history';
}
```

### 3. Controller: SyncController

**ملف:** `app/Http/Controllers/SyncController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\SyncLog;
use App\Models\Material;
use App\Models\DeliveryNote;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    // عرض سجل المزامنة
    public function index()
    {
        $syncs = SyncLog::with('user')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($syncs);
    }

    // حفظ بيانات جديدة للمزامنة
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'entity_type' => 'required|string',
                'entity_id' => 'required|integer',
                'data' => 'required|array',
            ]);

            $syncLog = SyncLog::logSync(
                auth()->id(),
                $validated['entity_type'],
                $validated['entity_id'],
                $validated['data'],
                'pending'
            );

            return response()->json([
                'success' => true,
                'sync_id' => $syncLog->id,
                'message' => 'تم حفظ البيانات بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // الحصول على حالة المزامنة
    public function getStatus($syncId)
    {
        $sync = SyncLog::find($syncId);

        if (!$sync) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json([
            'id' => $sync->id,
            'status' => $sync->status,
            'synced_at' => $sync->synced_at,
            'error_message' => $sync->error_message,
        ]);
    }

    // إعادة محاولة المزامنة الفاشلة
    public function retry($syncId)
    {
        $sync = SyncLog::find($syncId);

        if ($sync->status !== 'failed') {
            return response()->json(['error' => 'Only failed syncs can be retried'], 400);
        }

        $sync->update(['status' => 'pending', 'error_message' => null]);

        return response()->json(['success' => true, 'message' => 'تمت إعادة المحاولة']);
    }

    // عرض الإحصائيات
    public function stats()
    {
        return response()->json([
            'pending' => SyncLog::where('status', 'pending')->count(),
            'synced' => SyncLog::where('status', 'synced')->count(),
            'failed' => SyncLog::where('status', 'failed')->count(),
            'total' => SyncLog::count(),
        ]);
    }
}
```

### 4. Controller: ManagerSyncController

**ملف:** `app/Http/Controllers/ManagerSyncController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\DeliveryNote;
use App\Models\SyncHistory;
use Illuminate\Http\Request;

class ManagerSyncController extends Controller
{
    // سحب كل التحديثات منذ آخر مزامنة
    public function pullUpdates(Request $request)
    {
        $lastSyncTime = $request->input('last_sync', now()->subDay());
        $managerId = auth()->id();
        
        // جلب كل البيانات الجديدة
        $materials = Material::where('updated_at', '>', $lastSyncTime)->get();
        $deliveryNotes = DeliveryNote::where('updated_at', '>', $lastSyncTime)->get();
        $syncHistory = SyncHistory::where('synced_to_server', '>', $lastSyncTime)->get();
        
        // تسجيل أن المدير سحب البيانات
        SyncHistory::where('synced_to_server', '>', $lastSyncTime)
            ->update(['pulled_by_manager_at' => now()]);
        
        return response()->json([
            'materials' => $materials,
            'delivery_notes' => $deliveryNotes,
            'sync_history' => $syncHistory,
            'pulled_at' => now(),
        ]);
    }

    // عرض إحصائيات المزامنة
    public function getSyncStats()
    {
        return response()->json([
            'total_synced' => SyncHistory::count(),
            'today_synced' => SyncHistory::whereDate('synced_to_server', today())->count(),
            'pending_in_staff' => SyncHistory::whereNull('pulled_by_manager_at')->count(),
            'by_staff' => SyncHistory::groupBy('user_id')
                ->selectRaw('user_id, COUNT(*) as count')
                ->with('user:id,name')
                ->get(),
        ]);
    }

    // البحث عن بيانات معينة
    public function search(Request $request)
    {
        $type = $request->input('type');  // material, delivery_note
        $searchTerm = $request->input('search');
        
        if ($type === 'material') {
            return Material::where('name_ar', 'like', "%$searchTerm%")
                ->orWhere('barcode', $searchTerm)
                ->get();
        }
        
        if ($type === 'delivery_note') {
            return DeliveryNote::where('reference_number', 'like', "%$searchTerm%")
                ->with('material', 'user')
                ->get();
        }
    }
}
```

### 5. Service Worker: sync-manager.js

**ملف:** `resources/js/sync-manager.js`

```javascript
// resources/js/sync-manager.js

class SyncManager {
    constructor() {
        this.pendingItems = new Map();
        this.isOnline = navigator.onLine;
        this.syncInterval = null;
        
        this.setupEventListeners();
        this.loadFromLocalStorage();
    }

    // مراقبة الاتصال
    setupEventListeners() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            console.log('✅ عاد الاتصال - بدء المزامنة');
            this.syncAll();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            console.log('❌ انقطع الاتصال - حفظ محلي');
        });
    }

    // إضافة بيانة للمزامنة
    addItem(entityType, entityId, data) {
        const key = `${entityType}-${entityId}`;
        const item = {
            id: key,
            entityType,
            entityId,
            data,
            status: 'pending',
            timestamp: new Date().toISOString(),
        };

        this.pendingItems.set(key, item);
        this.saveToLocalStorage();
        
        return item;
    }

    // إرسال بيانة واحدة
    async syncItem(item) {
        try {
            const response = await fetch('/api/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content,
                },
                body: JSON.stringify({
                    entity_type: item.entityType,
                    entity_id: item.entityId,
                    data: item.data,
                }),
            });

            if (response.ok) {
                const result = await response.json();
                item.status = 'synced';
                item.syncedAt = new Date().toISOString();
                
                // إزالة من المعلقة
                this.pendingItems.delete(item.id);
                this.saveToLocalStorage();
                
                return { success: true, syncId: result.sync_id };
            } else {
                throw new Error(`Server error: ${response.status}`);
            }
        } catch (error) {
            item.status = 'failed';
            item.error = error.message;
            this.saveToLocalStorage();
            
            return { success: false, error: error.message };
        }
    }

    // مزامنة الكل
    async syncAll() {
        if (!this.isOnline || this.pendingItems.size === 0) {
            return;
        }

        console.log(`🔄 بدء مزامنة ${this.pendingItems.size} بيانة`);

        for (const [, item] of this.pendingItems) {
            if (item.status === 'pending' || item.status === 'failed') {
                await this.syncItem(item);
                await new Promise(r => setTimeout(r, 100)); // تأخير صغير
            }
        }

        console.log('✅ اكتملت المزامنة');
        this.notifyUI();
    }

    // حفظ في localStorage
    saveToLocalStorage() {
        const data = Array.from(this.pendingItems.values());
        localStorage.setItem('sync_queue', JSON.stringify(data));
    }

    // تحميل من localStorage
    loadFromLocalStorage() {
        const data = localStorage.getItem('sync_queue');
        if (data) {
            try {
                const items = JSON.parse(data);
                items.forEach(item => {
                    this.pendingItems.set(item.id, item);
                });
            } catch (e) {
                console.error('خطأ في تحميل البيانات', e);
            }
        }
    }

    // إخطار الواجهة
    notifyUI() {
        const event = new CustomEvent('syncStatusChanged', {
            detail: {
                pending: Array.from(this.pendingItems.values()).filter(i => i.status === 'pending'),
                synced: Array.from(this.pendingItems.values()).filter(i => i.status === 'synced'),
                failed: Array.from(this.pendingItems.values()).filter(i => i.status === 'failed'),
                isOnline: this.isOnline,
            }
        });
        document.dispatchEvent(event);
    }

    // الحصول على الحالة
    getStatus() {
        return {
            isOnline: this.isOnline,
            pendingCount: Array.from(this.pendingItems.values()).filter(i => i.status === 'pending').length,
            syncedCount: Array.from(this.pendingItems.values()).filter(i => i.status === 'synced').length,
            failedCount: Array.from(this.pendingItems.values()).filter(i => i.status === 'failed').length,
        };
    }
}

// تصدير الكلاس
window.syncManager = new SyncManager();
```

### 6. Service Worker للمدير: manager-sync-worker.js

**ملف:** `public/js/manager-sync-worker.js`

```javascript
// public/js/manager-sync-worker.js

class ManagerSyncService {
    constructor() {
        this.lastSyncTime = localStorage.getItem('manager_last_sync') || new Date(Date.now() - 24*60*60*1000);
        this.db = null;
        this.initDB();
    }

    async initDB() {
        // فتح قاعدة البيانات المحلية للمدير
        return new Promise((resolve, reject) => {
            const request = indexedDB.open('ManagerFactoryDB', 1);

            request.onerror = () => reject(request.error);
            request.onsuccess = () => {
                this.db = request.result;
                this.createTables();
                resolve();
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                
                // جداول للبيانات
                if (!db.objectStoreNames.contains('materials')) {
                    db.createObjectStore('materials', { keyPath: 'id' });
                }
                if (!db.objectStoreNames.contains('delivery_notes')) {
                    db.createObjectStore('delivery_notes', { keyPath: 'id' });
                }
                if (!db.objectStoreNames.contains('sync_history')) {
                    db.createObjectStore('sync_history', { keyPath: 'id' });
                }
                if (!db.objectStoreNames.contains('staff_data')) {
                    db.createObjectStore('staff_data', { keyPath: 'id' });
                }
            };
        });
    }

    // سحب البيانات من السرفر
    async pullFromServer() {
        try {
            const response = await fetch('/api/manager/pull-updates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content,
                },
                body: JSON.stringify({
                    last_sync: this.lastSyncTime
                })
            });

            if (!response.ok) throw new Error('Failed to pull data');

            const data = await response.json();

            // حفظ البيانات محلياً
            await this.saveMaterials(data.materials);
            await this.saveDeliveryNotes(data.delivery_notes);
            await this.saveSyncHistory(data.sync_history);

            // حفظ وقت آخر مزامنة
            this.lastSyncTime = data.pulled_at;
            localStorage.setItem('manager_last_sync', this.lastSyncTime);

            return {
                success: true,
                materialsCount: data.materials.length,
                deliveryNotesCount: data.delivery_notes.length,
                syncHistoryCount: data.sync_history.length,
            };

        } catch (error) {
            console.error('❌ خطأ في السحب:', error);
            return { success: false, error: error.message };
        }
    }

    // حفظ المواد محلياً
    async saveMaterials(materials) {
        const tx = this.db.transaction('materials', 'readwrite');
        const store = tx.objectStore('materials');

        for (const material of materials) {
            await store.put(material);
        }

        return new Promise((resolve, reject) => {
            tx.oncomplete = () => resolve();
            tx.onerror = () => reject(tx.error);
        });
    }

    // حفظ سندات التسليم محلياً
    async saveDeliveryNotes(notes) {
        const tx = this.db.transaction('delivery_notes', 'readwrite');
        const store = tx.objectStore('delivery_notes');

        for (const note of notes) {
            await store.put(note);
        }

        return new Promise((resolve, reject) => {
            tx.oncomplete = () => resolve();
            tx.onerror = () => reject(tx.error);
        });
    }

    // حفظ سجل المزامنة
    async saveSyncHistory(history) {
        const tx = this.db.transaction('sync_history', 'readwrite');
        const store = tx.objectStore('sync_history');

        for (const item of history) {
            await store.put(item);
        }

        return new Promise((resolve, reject) => {
            tx.oncomplete = () => resolve();
            tx.onerror = () => reject(tx.error);
        });
    }

    // البحث عن بيانات محلياً
    async searchLocally(type, term) {
        const tx = this.db.transaction(type === 'material' ? 'materials' : 'delivery_notes', 'readonly');
        const store = tx.objectStore(type === 'material' ? 'materials' : 'delivery_notes');

        return new Promise((resolve, reject) => {
            const request = store.getAll();
            request.onsuccess = () => {
                const results = request.result.filter(item => {
                    if (type === 'material') {
                        return item.name_ar?.includes(term) || item.barcode?.includes(term);
                    } else {
                        return item.reference_number?.includes(term);
                    }
                });
                resolve(results);
            };
            request.onerror = () => reject(request.error);
        });
    }

    // عرض الإحصائيات المحلية
    async getLocalStats() {
        const materials = await this.getAllMaterials();
        const deliveryNotes = await this.getAllDeliveryNotes();
        const syncHistory = await this.getAllSyncHistory();

        return {
            totalMaterials: materials.length,
            totalDeliveryNotes: deliveryNotes.length,
            totalSyncedItems: syncHistory.length,
            lastSync: this.lastSyncTime,
            staffCount: new Set(syncHistory.map(h => h.user_id)).size,
        };
    }

    async getAllMaterials() {
        const tx = this.db.transaction('materials', 'readonly');
        return new Promise((resolve) => {
            tx.objectStore('materials').getAll().onsuccess = (e) => resolve(e.target.result);
        });
    }

    async getAllDeliveryNotes() {
        const tx = this.db.transaction('delivery_notes', 'readonly');
        return new Promise((resolve) => {
            tx.objectStore('delivery_notes').getAll().onsuccess = (e) => resolve(e.target.result);
        });
    }

    async getAllSyncHistory() {
        const tx = this.db.transaction('sync_history', 'readonly');
        return new Promise((resolve) => {
            tx.objectStore('sync_history').getAll().onsuccess = (e) => resolve(e.target.result);
        });
    }

    // مزامنة دورية (كل 5 دقائق)
    startAutoSync(interval = 5 * 60 * 1000) {
        setInterval(() => {
            if (navigator.onLine) {
                this.pullFromServer();
            }
        }, interval);
    }
}

// تصدير الكلاس
window.managerSync = new ManagerSyncService();
```

---

## 🎯 نظام المدير والموظفين

### كيفية معرفة المزامنة:

#### الطريقة 1: من واجهة المستخدم (الموظف يشوف)

```
الموظف يلاحظ:
✅ علامة بجانب البيانات: "تم الحفظ" أو "✓"
⏳ في الحفظ (دائري يدور): "جاري المزامنة..."
❌ خطأ: "فشل الحفظ - سيحاول لاحقاً"
⚠️ أوفلاين: "محفوظ محلياً - في انتظار الاتصال"
```

#### الطريقة 2: من قائمة السجل (لوحة التحكم)

```
صفحة جديدة: "سجل المزامنة"
تعرض:
- التاريخ والوقت
- البيانات اللي تمت مزامنتها
- الحالة (نجح / فشل)
- من أدخلها (الموظف)
```

#### الطريقة 3: من قاعدة البيانات

```
جدول جديد: sync_logs
الأعمدة:
- id
- user_id (من الموظف)
- data_type (مثل: material, warehouse)
- status (pending, synced, failed)
- created_at
- synced_at
- error_message
```

### Blade View للمدير

**ملف:** `resources/views/manager/dashboard.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="manager-dashboard">
    <!-- حالة الاتصال والمزامنة -->
    <div class="sync-status-bar">
        <div id="connection-status">
            <span id="online-badge" style="display: none; color: green;">✅ متصل</span>
            <span id="offline-badge" style="display: none; color: red;">❌ غير متصل</span>
        </div>
        
        <button class="btn btn-primary" onclick="syncNow()">
            🔄 تحديث البيانات الآن
        </button>
        
        <div id="last-sync-time">
            آخر تحديث: <span id="sync-time">-</span>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="stats-container">
        <div class="stat-card">
            <h3>📦 المواد</h3>
            <div class="stat-number" id="materials-count">0</div>
        </div>
        <div class="stat-card">
            <h3>📋 سندات التسليم</h3>
            <div class="stat-number" id="delivery-notes-count">0</div>
        </div>
        <div class="stat-card">
            <h3>👥 الموظفون النشطون</h3>
            <div class="stat-number" id="staff-count">0</div>
        </div>
        <div class="stat-card">
            <h3>🔄 العمليات المزامنة</h3>
            <div class="stat-number" id="synced-count">0</div>
        </div>
    </div>

    <!-- البحث والتصفية -->
    <div class="search-section">
        <input type="text" id="search-input" placeholder="ابحث عن مادة أو سند تسليم">
        <select id="filter-type">
            <option value="material">المواد</option>
            <option value="delivery">سندات التسليم</option>
        </select>
        <button onclick="searchLocal()">🔍 بحث محلي</button>
    </div>

    <!-- جدول النتائج -->
    <div class="results-container">
        <table id="results-table" class="table">
            <thead>
                <tr>
                    <th>المعرّف</th>
                    <th>الاسم/الوصف</th>
                    <th>الموظف</th>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody id="results-body">
                <!-- النتائج تُضاف هنا -->
            </tbody>
        </table>
    </div>
</div>

<script src="{{ asset('js/manager-sync-worker.js') }}"></script>
<script>
    window.managerSync.initDB().then(() => {
        // تحميل البيانات عند الدخول
        loadDashboard();
        window.managerSync.startAutoSync();
    });

    async function syncNow() {
        const button = event.target;
        button.disabled = true;
        button.textContent = '⏳ جاري التحديث...';

        const result = await window.managerSync.pullFromServer();
        
        if (result.success) {
            showNotification('✅ تم تحديث البيانات بنجاح', 'success');
            await loadDashboard();
        } else {
            showNotification('❌ فشل التحديث: ' + result.error, 'error');
        }

        button.disabled = false;
        button.textContent = '🔄 تحديث البيانات الآن';
    }

    async function loadDashboard() {
        const stats = await window.managerSync.getLocalStats();
        
        document.getElementById('materials-count').textContent = stats.totalMaterials;
        document.getElementById('delivery-notes-count').textContent = stats.totalDeliveryNotes;
        document.getElementById('staff-count').textContent = stats.staffCount;
        document.getElementById('synced-count').textContent = stats.totalSyncedItems;
        document.getElementById('sync-time').textContent = new Date(stats.lastSync).toLocaleString('ar');

        // تحديث حالة الاتصال
        if (navigator.onLine) {
            document.getElementById('online-badge').style.display = 'inline';
            document.getElementById('offline-badge').style.display = 'none';
        } else {
            document.getElementById('online-badge').style.display = 'none';
            document.getElementById('offline-badge').style.display = 'inline';
        }
    }

    async function searchLocal() {
        const searchTerm = document.getElementById('search-input').value;
        const filterType = document.getElementById('filter-type').value;

        if (!searchTerm) return;

        const results = await window.managerSync.searchLocally(
            filterType === 'material' ? 'material' : 'delivery',
            searchTerm
        );

        displayResults(results, filterType);
    }

    function displayResults(results, type) {
        const tbody = document.getElementById('results-body');
        tbody.innerHTML = '';

        results.forEach(item => {
            const row = `
                <tr>
                    <td>${item.id}</td>
                    <td>${type === 'material' ? item.name_ar : item.reference_number}</td>
                    <td>${item.created_by || '-'}</td>
                    <td>${new Date(item.created_at).toLocaleDateString('ar')}</td>
                    <td>
                        ${item.synced ? '<span class="badge badge-success">✅ مزامن</span>' : 
                          '<span class="badge badge-warning">⏳ معلق</span>'}
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    function showNotification(message, type) {
        // يمكن استخدام مكتبة notifications
        alert(message);
    }

    // مراقبة حالة الاتصال
    window.addEventListener('online', loadDashboard);
    window.addEventListener('offline', loadDashboard);
</script>

<style>
    .sync-status-bar {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .stat-number {
        font-size: 32px;
        font-weight: bold;
        color: #007bff;
        margin-top: 10px;
    }

    .search-section {
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
    }

    .search-section input, .search-section select {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .results-container {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
    }

    .badge-success {
        background: #d4edda;
        color: #155724;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }
</style>
@endsection
```

---

## 📋 خطوات التطبيق

### 1️⃣ إنشاء الـ Models

```bash
php artisan make:model SyncLog -m
php artisan make:model SyncHistory -m
```

### 2️⃣ إنشاء الـ Controllers

```bash
php artisan make:controller SyncController
php artisan make:controller ManagerSyncController
```

### 3️⃣ إنشاء الجداول (Migrations)

قم بتشغيل الـ migrations:

```bash
php artisan migrate
```

### 4️⃣ إضافة الـ Routes

**ملف:** `routes/api.php`

```php
Route::middleware('auth:sanctum')->group(function () {
    // Sync routes for staff
    Route::post('/sync', [SyncController::class, 'store']);
    Route::get('/sync/{syncId}', [SyncController::class, 'getStatus']);
    Route::post('/sync/{syncId}/retry', [SyncController::class, 'retry']);
    Route::get('/sync-stats', [SyncController::class, 'stats']);

    // Sync routes for manager
    Route::post('/manager/pull-updates', [ManagerSyncController::class, 'pullUpdates']);
    Route::get('/manager/sync-stats', [ManagerSyncController::class, 'getSyncStats']);
    Route::get('/manager/search', [ManagerSyncController::class, 'search']);
});
```

### 5️⃣ إضافة الـ JS Files

انسخ ملفات JS إلى:
- `resources/js/sync-manager.js`
- `public/js/manager-sync-worker.js`

### 6️⃣ إضافة الـ Blade Views

أنشئ:
- `resources/views/manager/dashboard.blade.php`

### 7️⃣ الاختبار

```bash
# تشغيل السرفر
php artisan serve

# تشغيل Vite
npm run dev
```

---

## 🔍 مثال الاستخدام

### الموظف يدخل مادة:

```javascript
const newMaterial = {
    name: "حديد",
    quantity: 100,
    warehouse_id: 1
};

// حفظ محلياً أولاً
const syncItem = window.syncManager.addItem('material', null, newMaterial);

// ثم إرسال للسرفر
fetch('/api/materials', {
    method: 'POST',
    body: JSON.stringify(newMaterial),
    headers: { 'Content-Type': 'application/json' }
})
.then(response => {
    if (response.ok) {
        syncItem.status = 'synced';
    }
})
.catch(error => {
    // البيانات محفوظة محلياً بالفعل
    console.log('سيتم المزامنة لاحقاً');
});
```

### المدير يحدث البيانات:

```javascript
// تحميل البيانات الجديدة من السرفر
await window.managerSync.pullFromServer();

// البحث محلياً
const results = await window.managerSync.searchLocally('material', 'حديد');

// عرض الإحصائيات
const stats = await window.managerSync.getLocalStats();
console.log(`لديك ${stats.totalMaterials} مادة`);
```

---

## 📊 جدول المقارنة بين الطرق

| الميزة | PWA | SQLite محلي | Hybrid |
|--------|-----|-----------|--------|
| سهولة التطبيق | ✅ سهل | ❌ معقد | ✅ متوسط |
| أداء أوفلاين | ✅ جيد | ✅✅ ممتاز | ✅ جيد |
| المزامنة | ✅ تلقائية | ⚠️ معقدة | ✅ ذكية |
| التكلفة | ✅ منخفضة | ⚠️ عالية | ✅ متوسطة |
| الأمان | ⚠️ متوسط | ✅ عالي | ✅✅ عالي |

---

## ❓ أسئلة شائعة

### س: ماذا يحدث إذا كان هناك تعارض في البيانات؟

**ج:** يتم استخدام استراتيجية "آخر تعديل يفوز" (Last Write Wins):
- آخر حد عدّل البيانة = نسخته تنتصر
- يمكن تتبع كل التغييرات من جدول `sync_history`

### س: هل البيانات محفوظة في جهاز الموظف آمنة؟

**ج:** نعم، لأن:
- IndexedDB مشفر بواسطة المتصفح
- SQLite محلي محمي بكلمة مرور
- يمكن إضافة تشفير إضافي للحساسة

### س: كم مرة يجب مزامنة البيانات؟

**ج:** يفضل:
- كل 5 دقائق للمدير (ممكن تغييره)
- تلقائي عند عودة الإنترنت للموظف
- يدوي حسب الحاجة

---

## 🚀 التحسينات المستقبلية

1. ✅ تشفير البيانات المحلية
2. ✅ Conflict resolution متقدم
3. ✅ تقارير تفصيلية للمزامنة
4. ✅ Push notifications للتحديثات الجديدة
5. ✅ دعم التطبيقات الموبايل

---

## 📞 الدعم والمساعدة

للمزيد من المعلومات، راجع:
- [Firebase Realtime Database](https://firebase.google.com/docs/database)
- [Service Workers MDN](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [IndexedDB MDN](https://developer.mozilla.org/en-US/docs/Web/API/IndexedDB_API)
- [Laravel Queues](https://laravel.com/docs/queues)

---

**تم الإنشاء:** 7 ديسمبر 2025  
**الإصدار:** 1.0
