# 🏭 سيناريو النقل للإنتاج مع المراحل والتأكيد

## 📋 نظرة عامة

نظام متكامل لنقل المواد من المستودع للإنتاج مع تحديد المرحلة والموظف المستلم وتأكيد الاستلام.

---

## 🎯 السيناريو الكامل

### المرحلة 1️⃣: **طلب النقل (من مشرف المستودع)**

```
📦 المستودع
├─ البحث عن الدفعة: RW-2025-051
├─ اختيار الكمية: 300 كجم
├─ اختيار المرحلة: [المرحلة الأولى - القطع] ← default
├─ اختيار الموظف المستلم: [أحمد محمد - عامل القطع]
└─ الضغط على "نقل للإنتاج"
```

**ما يحدث في النظام:**
```php
DeliveryNote::create([
    'status' => 'pending_confirmation', // 🔶 في انتظار التأكيد
    'transfer_status' => 'pending',
    'production_stage' => 'stage_1',
    'production_stage_name' => 'المرحلة الأولى - القطع',
    'assigned_to' => 15, // user_id للموظف
    'transferred_by' => Auth::id(),
    'transferred_at' => now(),
]);
```

---

### المرحلة 2️⃣: **إشعار الموظف المستلم**

```
📱 الموظف (أحمد محمد) يستلم إشعار:
┌─────────────────────────────────────┐
│ 🔔 طلب استلام دفعة جديدة           │
├─────────────────────────────────────┤
│ الباركود: RW-2025-051              │
│ المادة: حديد خام                   │
│ الكمية: 300 كجم                    │
│ المرحلة: القطع                     │
│ من: محمد أحمد (مشرف المستودع)      │
│                                     │
│ [تأكيد الاستلام] [رفض]            │
└─────────────────────────────────────┘
```

---

### المرحلة 3️⃣: **تأكيد الاستلام (من الموظف)**

#### السيناريو أ: الموظف يؤكد الاستلام ✅

```
الموظف يضغط "تأكيد الاستلام"
└─ قد يُطلب منه:
   ├─ مسح الباركود بالماسح الضوئي
   ├─ إدخال رمز PIN
   └─ تأكيد الكمية المستلمة
```

**ما يحدث في النظام:**
```php
DeliveryNote::update([
    'status' => 'confirmed', // ✅ تم التأكيد
    'transfer_status' => 'confirmed',
    'confirmed_by' => 15, // الموظف المستلم
    'confirmed_at' => now(),
    'actual_received_quantity' => 300,
]);

// تحديث حالة الدفعة
MaterialBatch::update([
    'status' => 'in_production',
    'current_stage' => 'stage_1',
]);

// إنشاء سجل في التتبع
ProductTracking::create([
    'barcode' => 'PR-2025-009',
    'stage' => 'stage_1',
    'action' => 'received_at_production',
    'worker_id' => 15,
]);
```

#### السيناريو ب: الموظف يرفض الاستلام ❌

```
الموظف يضغط "رفض"
└─ يُطلب منه إدخال السبب:
   ├─ كمية غير صحيحة
   ├─ جودة غير مناسبة
   └─ خطأ في المادة
```

**ما يحدث في النظام:**
```php
DeliveryNote::update([
    'status' => 'rejected', // ❌ مرفوض
    'transfer_status' => 'rejected',
    'rejected_by' => 15,
    'rejected_at' => now(),
    'rejection_reason' => 'الكمية غير مطابقة',
]);

// إرجاع الكمية للمستودع
MaterialBatch::update([
    'available_quantity' => available_quantity + 300,
    'status' => 'available',
]);
```

---

## 🎨 واجهة المستخدم

### 1️⃣ **صفحة النقل (من المستودع)**

```html
┌─────────────────────────────────────────────┐
│ 📦 نقل دفعة للإنتاج                        │
├─────────────────────────────────────────────┤
│                                             │
│ الباركود: RW-2025-051                      │
│ المادة: حديد خام                           │
│ الكمية المتاحة: 1000 كجم                  │
│                                             │
│ ┌─────────────────────────────────────┐   │
│ │ الكمية المطلوبة: [300] كجم          │   │
│ └─────────────────────────────────────┘   │
│                                             │
│ ┌─────────────────────────────────────┐   │
│ │ المرحلة:                             │   │
│ │ ○ المرحلة الأولى - القطع    [✓]    │   │
│ │ ○ المرحلة الثانية - التشكيل        │   │
│ │ ○ المرحلة الثالثة - اللحام          │   │
│ │ ○ المرحلة الرابعة - الدهان          │   │
│ └─────────────────────────────────────┘   │
│                                             │
│ ┌─────────────────────────────────────┐   │
│ │ الموظف المستلم:                     │   │
│ │ [▼ اختر الموظف]                     │   │
│ │   - أحمد محمد (عامل القطع)          │   │
│ │   - خالد علي (عامل التشكيل)         │   │
│ └─────────────────────────────────────┘   │
│                                             │
│ [ نقل للإنتاج ] [ إلغاء ]                 │
└─────────────────────────────────────────────┘
```

---

### 2️⃣ **صفحة الموظف (تأكيد الاستلام)**

```html
┌─────────────────────────────────────────────┐
│ 📋 طلبات الاستلام المعلقة                  │
├─────────────────────────────────────────────┤
│                                             │
│ ┌─────────────────────────────────────┐   │
│ │ 🔶 في انتظار التأكيد                │   │
│ │                                       │   │
│ │ الباركود: RW-2025-051 → PR-2025-009  │   │
│ │ المادة: حديد خام                     │   │
│ │ الكمية: 300 كجم                      │   │
│ │ المرحلة: القطع                       │   │
│ │ من: محمد أحمد (مشرف المستودع)        │   │
│ │ التاريخ: 2025-11-24 10:30           │   │
│ │                                       │   │
│ │ [✓ تأكيد الاستلام] [✗ رفض]          │   │
│ └─────────────────────────────────────┘   │
│                                             │
│ ┌─────────────────────────────────────┐   │
│ │ ✅ تم التأكيد                        │   │
│ │                                       │   │
│ │ الباركود: RW-2025-048 → PR-2025-007  │   │
│ │ المادة: ألمنيوم                      │   │
│ │ الكمية: 500 كجم                      │   │
│ │ تم التأكيد: 2025-11-24 09:15        │   │
│ └─────────────────────────────────────┘   │
└─────────────────────────────────────────────┘
```

---

### 3️⃣ **صفحة تتبع الدفعة**

```html
┌─────────────────────────────────────────────┐
│ 🔍 تتبع الدفعة: RW-2025-051                │
├─────────────────────────────────────────────┤
│                                             │
│ الحالة: 🔶 في انتظار تأكيد الاستلام       │
│                                             │
│ ────────── Timeline ──────────              │
│                                             │
│ ✅ 2025-11-24 08:00                        │
│    دخول المستودع                           │
│    المستخدم: محمد أحمد                     │
│    الكمية: 1000 كجم                        │
│                                             │
│ ✅ 2025-11-24 10:30                        │
│    نقل للإنتاج (المرحلة الأولى)           │
│    المستخدم: محمد أحمد                     │
│    الكمية المنقولة: 300 كجم               │
│    الموظف المستلم: أحمد محمد              │
│                                             │
│ 🔶 في انتظار التأكيد...                   │
│    الموظف: أحمد محمد                       │
│    الوقت المتوقع: خلال 15 دقيقة           │
│                                             │
└─────────────────────────────────────────────┘
```

---

## 📊 حالات الدفعة (Status Flow)

```
┌─────────────────┐
│   available     │ ← الدفعة في المستودع
│   (متاح)        │
└────────┬────────┘
         │
         │ [نقل للإنتاج]
         ↓
┌─────────────────┐
│ pending_conf.   │ ← في انتظار تأكيد الموظف
│ (انتظار تأكيد) │
└────┬──────┬─────┘
     │      │
     │      │ [رفض]
     │      ↓
     │   ┌──────────┐
     │   │ rejected │ → إرجاع للمستودع
     │   │ (مرفوض)  │
     │   └──────────┘
     │
     │ [تأكيد]
     ↓
┌─────────────────┐
│   confirmed     │ ← تم التأكيد والبدء في الإنتاج
│ (في الإنتاج)   │
└────────┬────────┘
         │
         │ [انتهاء المرحلة]
         ↓
┌─────────────────┐
│   completed     │
│ (مكتمل)        │
└─────────────────┘
```

---

## 🗄️ تصميم قاعدة البيانات

### جدول: `delivery_notes` (تحديثات)

```sql
-- إضافة الحقول الجديدة
ALTER TABLE delivery_notes ADD COLUMN:
- production_stage VARCHAR(50) -- 'stage_1', 'stage_2', etc.
- production_stage_name VARCHAR(100) -- 'المرحلة الأولى - القطع'
- assigned_to INT -- user_id للموظف المستلم
- transfer_status ENUM('pending', 'confirmed', 'rejected')
- confirmed_by INT -- user_id للموظف الذي أكد
- confirmed_at DATETIME
- rejected_by INT
- rejected_at DATETIME
- rejection_reason TEXT
- actual_received_quantity DECIMAL(10,2)
```

### جدول جديد: `production_stages`

```sql
CREATE TABLE production_stages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    stage_code VARCHAR(50) UNIQUE, -- 'stage_1', 'stage_2'
    stage_name VARCHAR(100), -- 'القطع'
    stage_name_en VARCHAR(100), -- 'Cutting'
    stage_order INT, -- 1, 2, 3, 4
    description TEXT,
    estimated_duration INT, -- minutes
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- البيانات الافتراضية
INSERT INTO production_stages VALUES
(1, 'stage_1', 'المرحلة الأولى - القطع', 'Stage 1 - Cutting', 1, NULL, 120, 1),
(2, 'stage_2', 'المرحلة الثانية - التشكيل', 'Stage 2 - Forming', 2, NULL, 180, 1),
(3, 'stage_3', 'المرحلة الثالثة - اللحام', 'Stage 3 - Welding', 3, NULL, 240, 1),
(4, 'stage_4', 'المرحلة الرابعة - الدهان', 'Stage 4 - Painting', 4, NULL, 300, 1);
```

### جدول: `production_confirmations`

```sql
CREATE TABLE production_confirmations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    delivery_note_id BIGINT,
    batch_id BIGINT,
    stage_code VARCHAR(50),
    assigned_to INT, -- الموظف المستلم
    status ENUM('pending', 'confirmed', 'rejected'),
    confirmed_by INT,
    confirmed_at DATETIME,
    rejected_by INT,
    rejected_at DATETIME,
    rejection_reason TEXT,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (delivery_note_id) REFERENCES delivery_notes(id),
    FOREIGN KEY (batch_id) REFERENCES material_batches(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    FOREIGN KEY (confirmed_by) REFERENCES users(id)
);
```

---

## 🔔 نظام الإشعارات

### إشعار 1: عند النقل للإنتاج
```php
Notification::create([
    'user_id' => $assignedTo, // الموظف المستلم
    'type' => 'production_transfer',
    'title' => 'طلب استلام دفعة جديدة',
    'message' => "تم نقل دفعة {$batchCode} للمرحلة {$stageName}",
    'action_url' => route('production.confirmations.pending'),
    'priority' => 'high',
]);
```

### إشعار 2: عند التأكيد
```php
Notification::create([
    'user_id' => $transferredBy, // مشرف المستودع
    'type' => 'production_confirmed',
    'title' => 'تم تأكيد استلام الدفعة',
    'message' => "تم تأكيد استلام دفعة {$batchCode} من قبل {$workerName}",
    'action_url' => route('warehouse.registration.show', $deliveryNote),
]);
```

### إشعار 3: عند الرفض
```php
Notification::create([
    'user_id' => $transferredBy,
    'type' => 'production_rejected',
    'title' => 'تم رفض استلام الدفعة',
    'message' => "رفض {$workerName} استلام دفعة {$batchCode}. السبب: {$reason}",
    'priority' => 'urgent',
]);
```

---

## 🔐 الصلاحيات

### الأدوار والصلاحيات المطلوبة:

```php
// مشرف المستودع
'warehouse.transfer_to_production' => true,
'warehouse.view_confirmations' => true,

// موظف الإنتاج
'production.confirm_receipt' => true,
'production.reject_receipt' => true,
'production.view_pending' => true,

// مدير الإنتاج
'production.view_all' => true,
'production.override_confirmation' => true,
```

---

## 📱 Routes المطلوبة

```php
// نقل للإنتاج
Route::post('warehouse/transfer-to-production', [Controller::class, 'transferToProduction']);

// صفحة الموظف
Route::get('production/confirmations/pending', [Controller::class, 'pendingConfirmations']);

// تأكيد الاستلام
Route::post('production/confirmations/{id}/confirm', [Controller::class, 'confirmReceipt']);

// رفض الاستلام
Route::post('production/confirmations/{id}/reject', [Controller::class, 'rejectReceipt']);

// عرض التفاصيل
Route::get('production/confirmations/{id}', [Controller::class, 'showConfirmation']);
```

---

## ⏱️ Timeline التنفيذ

### الخطوة 1: إعداد قاعدة البيانات (30 دقيقة)
- ✅ إنشاء migrations
- ✅ إضافة حقول جديدة
- ✅ إنشاء جدول production_stages
- ✅ إنشاء جدول production_confirmations

### الخطوة 2: Models و Relations (20 دقيقة)
- ✅ تحديث DeliveryNote model
- ✅ إنشاء ProductionStage model
- ✅ إنشاء ProductionConfirmation model
- ✅ إضافة العلاقات

### الخطوة 3: Controller Methods (45 دقيقة)
- ✅ transferToProduction()
- ✅ pendingConfirmations()
- ✅ confirmReceipt()
- ✅ rejectReceipt()

### الخطوة 4: Views (60 دقيقة)
- ✅ تحديث صفحة النقل
- ✅ صفحة الموظف (pending confirmations)
- ✅ صفحة التأكيد
- ✅ Modal للرفض مع السبب

### الخطوة 5: نظام الإشعارات (30 دقيقة)
- ✅ إشعارات للموظف المستلم
- ✅ إشعارات للمشرف عند التأكيد/الرفض
- ✅ Real-time notifications (optional)

---

## ✅ فوائد هذا النظام

1. **تتبع دقيق** 📊
   - معرفة من استلم الدفعة ومتى
   - تاريخ كامل للحركات

2. **مسؤولية واضحة** 👤
   - كل موظف مسؤول عن ما يستلمه
   - لا يمكن التهرب من المسؤولية

3. **منع الأخطاء** ❌
   - الموظف يؤكد الكمية قبل البدء
   - إمكانية الرفض إذا كان هناك خطأ

4. **تقارير دقيقة** 📈
   - وقت الاستلام الفعلي
   - نسبة التأكيد/الرفض
   - أداء الموظفين

5. **تكامل مع الباركود** 📦
   - مسح الباركود عند التأكيد
   - لا يمكن التأكيد بدون الباركود الصحيح

---

## 🎯 ملخص السيناريو

```
1. مشرف المستودع ينقل دفعة للإنتاج
   ├─ يختار المرحلة (افتراضي: المرحلة الأولى)
   └─ يختار الموظف المستلم

2. الموظف يستلم إشعار
   └─ يذهب لصفحة "طلبات الاستلام المعلقة"

3. الموظف يؤكد أو يرفض
   ├─ إذا أكد → الدفعة تصبح "في الإنتاج"
   └─ إذا رفض → الدفعة ترجع للمستودع

4. مشرف المستودع يستلم إشعار بالنتيجة
   └─ يمكنه تتبع حالة الدفعة في أي وقت
```

---

**هل تريد البدء في التنفيذ الآن؟** 🚀
