# 🏭 نظام التسجيل والتسوية في المستودع
## Warehouse Registration & Reconciliation System

---

## 📌 ملخص المشاكل والحلول

### ❌ المشكلة الأولى: خروج البضاعة بدون تسجيل

**الحالة الخاطئة:**
```
شاحنة تصل
    ↓
بضاعة تدخل المستودع
    ↓
تخرج مباشرة للإنتاج
    ↓
❌ بدون أي تسجيل في النظام!
❌ المدير ما يعرف إيش موجود
❌ لا audit trail ولا inventory
```

**الحل الصحيح:**
```
شاحنة تصل
    ↓
❌ BLOCKED: ما تطلع بدون تسجيل
    ↓
أمين المستودع يسجل الوزن الفعلي
    ↓
✅ registration_status = "registered"
    ↓
الآن تقدر تطلع للإنتاج
```

---

### ❌ المشكلة الثانية: عدم مطابقة البضاعة مع الفاتورة

**الحالة الخاطئة:**
```
أمين المستودع سجل: 1000 كيلو ✅
الفاتورة من المورد: 1050 كيلو
    ↓
❌ ما في طريقة للمقارنة!
❌ الفرق (50 كيلو) ما تنكتشف!
❌ ممكن ندفع أموال إضافية بدون حق!
```

**الحل الصحيح:**
```
أمين المستودع: 1000 كيلو
الفاتورة: 1050 كيلو
    ↓
النظام يحسب الفرق تلقائياً:
├─ الفرق: +50 كيلو
├─ النسبة: +4.76%
└─ القيمة: 2,500 ريال
    ↓
عرض على المدير للقرار
    ↓
✅ قبول / ❌ رفض / 🔧 تعديل
```

---

## 🎯 النظام الجديد الكامل

### **الحالات (Status) الجديدة:**

```
للـ Delivery Notes:

1. not_registered (افتراضي)
   ├─ جديد توه وصل
   ├─ ❌ ما يمكن خروج البضاعة
   └─ أمين المستودع لازم يسجل

2. registered ✅
   ├─ تم التزين والتسجيل
   ├─ ✅ يمكن الخروج للإنتاج
   └─ بانتظار الفاتورة

3. in_production 🏭
   ├─ دخلت الإنتاج
   ├─ ما يمكن إرجاع
   └─ بانتظار التسوية

4. completed ✅
   ├─ اكتملت العملية كاملة
   └─ archived

للـ Reconciliation:

1. pending ⏳
   ├─ لا توجد فاتورة بعد
   └─ بانتظار المحاسب

2. matched ✅
   ├─ متطابق تماماً
   └─ لا توجد فروقات

3. discrepancy ⚠️
   ├─ يوجد فرق
   └─ بانتظار قرار المدير

4. adjusted ✅
   ├─ تم التسوية
   └─ جاهز للدفع

5. rejected ❌
   ├─ مرفوضة
   └─ تحتاج محاورة
```

---

## 📊 الجداول والحقول المطلوبة

### **جدول 1: delivery_notes (التعديلات)**

```sql
ALTER TABLE delivery_notes ADD COLUMN (
    -- ========== معلومات التسجيل ==========
    registration_status ENUM(
        'not_registered',    -- جديد وصل
        'registered',        -- تم التسجيل
        'in_production',     -- دخل الإنتاج
        'completed'          -- اكتمل
    ) DEFAULT 'not_registered',
    
    registered_by BIGINT UNSIGNED NULL,  -- من سجّل
    registered_at TIMESTAMP NULL,         -- متى سجّل
    
    -- ========== معلومات الفاتورة ==========
    purchase_invoice_id BIGINT UNSIGNED NULL,
    invoice_weight DECIMAL(10,2) NULL,    -- من الفاتورة
    invoice_date DATE NULL,
    
    -- ========== حساب الفروقات (Generated) ==========
    weight_discrepancy DECIMAL(10,2) GENERATED ALWAYS AS (
        actual_weight - COALESCE(invoice_weight, 0)
    ) STORED COMMENT 'الفرق بالكيلو',
    
    discrepancy_percentage DECIMAL(5,2) GENERATED ALWAYS AS (
        CASE 
            WHEN COALESCE(invoice_weight, 0) = 0 THEN 0
            ELSE (
                (actual_weight - COALESCE(invoice_weight, 0)) 
                / COALESCE(invoice_weight, 1) * 100
            )
        END
    ) STORED COMMENT 'الفرق بالنسبة المئوية',
    
    discrepancy_financial DECIMAL(12,2) GENERATED ALWAYS AS (
        weight_discrepancy * 50  -- سعر الكيلو (مثال)
    ) STORED COMMENT 'التأثير المالي',
    
    -- ========== معلومات التسوية ==========
    reconciliation_status ENUM(
        'pending',          -- في انتظار الفاتورة
        'matched',          -- متطابق
        'discrepancy',      -- فرق
        'adjusted',         -- تم التسوية
        'rejected'          -- مرفوضة
    ) DEFAULT 'pending',
    
    reconciliation_notes TEXT NULL,
    reconciled_by BIGINT UNSIGNED NULL,   -- من وافق
    reconciled_at TIMESTAMP NULL,         -- متى وافق
    
    -- ========== معلومات إضافية ==========
    is_locked BOOLEAN DEFAULT FALSE,      -- لا يمكن تعديل
    lock_reason VARCHAR(255) NULL,
    
    -- ========== Foreign Keys ==========
    FOREIGN KEY (purchase_invoice_id) 
        REFERENCES purchase_invoices(id) ON DELETE SET NULL,
    FOREIGN KEY (registered_by) 
        REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (reconciled_by) 
        REFERENCES users(id) ON DELETE SET NULL,
    
    -- ========== Indexes ==========
    INDEX idx_registration_status (registration_status),
    INDEX idx_reconciliation_status (reconciliation_status),
    INDEX idx_is_locked (is_locked)
);
```

---

### **جدول 2: reconciliation_logs (سجل التسوية)**

```sql
CREATE TABLE reconciliation_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- ========== الربط ==========
    delivery_note_id BIGINT UNSIGNED NOT NULL,
    purchase_invoice_id BIGINT UNSIGNED NULL,
    
    -- ========== البيانات الفعلية ==========
    actual_weight DECIMAL(10,2) NOT NULL COMMENT 'من الميزان',
    invoice_weight DECIMAL(10,2) NOT NULL COMMENT 'من الفاتورة',
    
    -- ========== الحسابات ==========
    discrepancy_kg DECIMAL(10,2) GENERATED ALWAYS AS (
        actual_weight - invoice_weight
    ) STORED,
    
    discrepancy_percentage DECIMAL(5,2) GENERATED ALWAYS AS (
        CASE 
            WHEN invoice_weight = 0 THEN 0
            ELSE ((actual_weight - invoice_weight) / invoice_weight * 100)
        END
    ) STORED,
    
    financial_impact DECIMAL(12,2) COMMENT 'التأثير المالي',
    
    -- ========== القرار المتخذ ==========
    action ENUM(
        'accepted',         -- قبول الفرق
        'rejected',         -- رفض الفاتورة
        'adjusted',         -- تعديل البيانات
        'negotiated',       -- تمت محاورة
        'pending'           -- بانتظار قرار
    ) DEFAULT 'pending',
    
    reason VARCHAR(255) COMMENT 'سبب القرار',
    comments TEXT COMMENT 'تفاصيل إضافية',
    
    -- ========== من اتخذ القرار ==========
    decided_by BIGINT UNSIGNED NOT NULL,
    decided_at TIMESTAMP,
    
    -- ========== Metadata ==========
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- ========== Foreign Keys ==========
    FOREIGN KEY (delivery_note_id) 
        REFERENCES delivery_notes(id) ON DELETE CASCADE,
    FOREIGN KEY (purchase_invoice_id) 
        REFERENCES purchase_invoices(id) ON DELETE SET NULL,
    FOREIGN KEY (decided_by) 
        REFERENCES users(id) ON DELETE RESTRICT,
    
    -- ========== Indexes ==========
    INDEX idx_delivery_note_id (delivery_note_id),
    INDEX idx_purchase_invoice_id (purchase_invoice_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at),
    INDEX idx_decided_by (decided_by)
);
```

---

### **جدول 3: registration_logs (سجل التسجيل)**

```sql
CREATE TABLE registration_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    delivery_note_id BIGINT UNSIGNED NOT NULL,
    
    -- ========== البيانات ==========
    weight_recorded DECIMAL(10,2) NOT NULL,
    supplier_id BIGINT UNSIGNED,
    material_type_id BIGINT UNSIGNED,
    location VARCHAR(100),
    
    -- ========== من فعل ==========
    registered_by BIGINT UNSIGNED NOT NULL,
    registered_at TIMESTAMP,
    
    -- ========== Metadata ==========
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- ========== Foreign Keys ==========
    FOREIGN KEY (delivery_note_id) 
        REFERENCES delivery_notes(id) ON DELETE CASCADE,
    FOREIGN KEY (registered_by) 
        REFERENCES users(id) ON DELETE RESTRICT,
    
    -- ========== Indexes ==========
    INDEX idx_delivery_note_id (delivery_note_id),
    INDEX idx_registered_by (registered_by),
    INDEX idx_registered_at (registered_at)
);
```

---

## 🎮 الواجهات المستخدم

### **1. صفحة أمين المستودع - البضاعة الجديدة**

```
┌─────────────────────────────────────────────────────────┐
│ 📦 الشحنات الجديدة (بانتظار التسجيل)                  │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ ⚠️ عدد الشحنات المعلقة: 5                              │
│                                                         │
│ ┌─────────────────────────────────────────────────────┐│
│ │ □ Truck #1                                          ││
│ │   المورد: شركة الحديد                              ││
│ │   وصول: 2025-11-17 08:00 AM                        ││
│ │   الفترة: منذ 2 ساعة ⚠️                           ││
│ │   Status: ❌ NOT REGISTERED                        ││
│ │                                                    ││
│ │   [📝 Register Now]                                ││
│ └─────────────────────────────────────────────────────┘│
│                                                         │
│ ┌─────────────────────────────────────────────────────┐│
│ │ □ Truck #2                                          ││
│ │   المورد: شركة المعادن                             ││
│ │   وصول: 2025-11-17 10:30 AM                        ││
│ │   الفترة: منذ 30 دقيقة                             ││
│ │   Status: ❌ NOT REGISTERED                        ││
│ │                                                    ││
│ │   [📝 Register Now]                                ││
│ └─────────────────────────────────────────────────────┘│
│                                                         │
│ ✅ الشحنات المسجلة: 10                                │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

### **2. نموذج التسجيل (Registration Form)**

```
┌─────────────────────────────────────────────────────────┐
│ 📝 تسجيل شحنة جديدة - Truck #1                        │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ 🔴 ★ هذا الحقول إجبارية                              │
│                                                         │
│ الخطوة 1: النوع والمورد                                │
│ ────────────────────────────                           │
│ نوع الشحنة: ○ Incoming (وارد)                        │
│             ○ Outgoing (صادر)                        │
│                                                         │
│ المورد: [شركة الحديد] 🔴 ★                             │
│         (مملوء تلقائياً من بيانات التوصيل)            │
│                                                         │
│ الخطوة 2: المادة والموقع                               │
│ ────────────────────────────                           │
│ نوع المادة: [تحديد من قائمة] 🔴 ★                     │
│           (حديد / نحاس / ألمنيوم)                      │
│                                                         │
│ موقع التخزين: [تحديد الموقع] 🔴 ★                     │
│              (Area-A / Area-B / Area-C)              │
│                                                         │
│ الخطوة 3: الوزن من الميزان                             │
│ ────────────────────────────                           │
│ ⚠️ هذا من الميزان الفعلي، ما تكتب من الفاتورة!      │
│                                                         │
│ الوزن الفعلي (كيلو): [___________] 🔴 ★              │
│                      (مثال: 1000)                      │
│                                                         │
│ ✓ تم التحقق من الميزان                               │
│                                                         │
│ الخطوة 4: ملاحظات (اختيارية)                           │
│ ────────────────────────────                           │
│ ملاحظات: [____________________________]               │
│          (مثال: بضاعة سليمة، بدون أضرار)              │
│                                                         │
│ الخطوة 5: تأكيد التسجيل                                │
│ ────────────────────────────                           │
│ □ أؤكد أن هذه البيانات صحيحة وتم التحقق منها        │
│                                                         │
│ [✓ تسجيل الآن] [✗ إلغاء]                             │
│                                                         │
│ ملخص ما سيتم:                                          │
│ ├─ تحديث registration_status → "registered"          │
│ ├─ حفظ الوزن الفعلي: 1000 كيلو                        │
│ ├─ تسجيل من: [اسمك] والوقت الحالي                     │
│ ├─ إنشاء سجل في registration_logs                    │
│ └─ ✅ بعدها تقدر تطلع البضاعة للإنتاج                │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

### **3. لوحة التسوية (Reconciliation Panel)**

```
┌─────────────────────────────────────────────────────────┐
│ 🔄 تسوية البضاعة والفواتير (للمحاسب/المدير)          │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ 🔍 فلاتر البحث:                                        │
│                                                         │
│ المورد: [All ▼]                                        │
│ حالة التسوية: [All ▼]                                 │
│              ├─ Pending (بانتظار الفاتورة)           │
│              ├─ Matched (متطابق)                     │
│              ├─ Discrepancy (فرق)                   │
│              ├─ Adjusted (مسوى)                     │
│              └─ Rejected (مرفوض)                    │
│                                                         │
│ من التاريخ: [2025-11-01]                              │
│ إلى التاريخ: [2025-11-17]                             │
│                                                         │
│ [🔍 بحث]  [↻ إعادة تحديد]                            │
│                                                         │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ ⚠️ تسوية مطلوبة: 3 حالات                              │
│                                                         │
│ ┌─────────────────────────────────────────────────────┐│
│ │ DN-2025-0001                                       ││
│ │ ─────────────────────────────────────────────────  ││
│ │ التاريخ: 2025-11-17                               ││
│ │ المورد: شركة الحديد                               ││
│ │ المادة: حديد 8mm                                  ││
│ │                                                   ││
│ │ 📊 المقارنة:                                      ││
│ │                                                   ││
│ │ ┌──────────────┬──────────────┬────────────────┐││
│ │ │ البيان       │ الفعلي       │ الفاتورة      │││
│ │ │ (الميزان)    │              │ (المورد)      │││
│ │ ├──────────────┼──────────────┼────────────────┤││
│ │ │ الوزن (كيلو) │ 1000 ✅     │ 1050          │││
│ │ │ الفرق        │              │ +50 ⚠️        │││
│ │ │ النسبة       │              │ +5.0% ⚠️      │││
│ │ │ القيمة       │              │ 2,500 ريال ⚠️ │││
│ │ └──────────────┴──────────────┴────────────────┘││
│ │                                                   ││
│ │ ⚠️ تحذير: المورد كاتب 50 كيلو أكثر من الفعلي!   ││
│ │                                                   ││
│ │ حالة التسوية: ⏳ بانتظار قرار                    ││
│ │ التاريخ: -                                        ││
│ │ من وافق: -                                       ││
│ │                                                   ││
│ │ القرار: 🔴 مطلوب                                 ││
│ │ ────────────                                     ││
│ │ [✓ قبول الفرق]                                  ││
│ │    (نقبل الفاتورة بـ 50 كيلو إضافي)             ││
│ │                                                   ││
│ │ [✗ رفض الفاتورة]                                ││
│ │    (نرجع الفاتورة للمورد ونطلب تعديل)           ││
│ │                                                   ││
│ │ [🔧 تعديل البيانات]                             ││
│ │    (نعدل الوزن أو الفاتورة يدوياً)              ││
│ │                                                   ││
│ │ ملاحظة إضافية (اختيارية):                        ││
│ │ [___________________________________]           ││
│ │ (مثال: شركة دايماً تكتب وزن زيادة)              ││
│ │                                                   ││
│ │ [💾 حفظ القرار]                                  ││
│ └─────────────────────────────────────────────────────┘│
│                                                         │
│ ┌─────────────────────────────────────────────────────┐│
│ │ DN-2025-0002                                       ││
│ │ ─────────────────────────────────────────────────  ││
│ │ ... (بقية البيانات)                               ││
│ └─────────────────────────────────────────────────────┘│
│                                                         │
│ ✅ تسويات مكتملة: 15                                  │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

### **4. لوحة المدير - نظرة عامة**

```
┌─────────────────────────────────────────────────────────┐
│ 📊 لوحة التحكم - الحالة العامة                        │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ 📈 إحصائيات اليوم (2025-11-17):                      │
│                                                         │
│ ┌────────────────┬──────────────┬────────────────────┐│
│ │ شحنات جديدة   │ مسجلة ✅    │ بانتظار التسوية   │││
│ │ 12             │ 10 (83%)    │ 2 (17%)            │││
│ ├────────────────┼──────────────┼────────────────────┤││
│ │ في الإنتاج   │ مسوية ✅    │ مرفوضة ❌          │││
│ │ 8              │ 15 (100%)   │ 0 (0%)             │││
│ └────────────────┴──────────────┴────────────────────┘│
│                                                         │
│ ⚠️ تنبيهات فروقات كبيرة:                              │
│                                                         │
│ 🔴 DN-2025-0001 vs INV-2024-001                       │
│    شركة الحديد                                         │
│    الفرق: +50 كيلو (+5.0%)                           │
│    القيمة: 2,500 ريال                                │
│    الحالة: ⏳ بانتظار قرار                             │
│    [Review]                                            │
│                                                         │
│ 🟠 DN-2025-0003 vs INV-2024-003                       │
│    شركة المعادن                                        │
│    الفرق: -30 كيلو (-3.3%)                           │
│    القيمة: -1,500 ريال (في صالحنا)                  │
│    الحالة: ✅ مسوية                                   │
│                                                         │
│ 📊 أداء الموردين:                                    │
│                                                         │
│ ┌────────────────────┬──────┬──────────┬──────────┐│
│ │ المورد             │ شحن  │ متطابق   │ فروقات  ││
│ ├────────────────────┼──────┼──────────┼──────────┤│
│ │ شركة الحديد       │ 15   │ 13 ✅   │ 2 ⚠️    ││
│ │ شركة المعادن      │ 8    │ 8 ✅    │ 0 ✅    ││
│ │ شركة الصناعات     │ 12   │ 10 ✅   │ 2 ⚠️    ││
│ │ المتوسط الكلي      │ 35   │ 31 ✅   │ 4 ⚠️    ││
│ └────────────────────┴──────┴──────────┴──────────┘│
│                                                         │
│ 💡 التوصيات:                                          │
│    ├─ شركة الحديد: دايماً تكتب وزن أعلى             │
│    ├─ لازم نراجع معهم أو نقلل العمل معهم           │
│    └─ المعادن: ممتازة في الدقة ✅                  │
│                                                         │
│ [📋 تقرير مفصل] [💾 تصدير PDF] [📧 إرسال]        │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

## 🔄 العمليات والـ Workflows

### **Workflow 1: من الوصول للتسجيل**

```
الخطوة 1: الشاحنة تصل
┌──────────────────────────────┐
│ Truck arrives at Gate        │
│ delivery_note created (auto) │
│ registration_status:         │
│   ❌ "not_registered"        │
│ ⚠️ BLOCKED: لا تخرج بعد      │
└──────────────────────────────┘
         ↓ (أمين المستودع)
         
الخطوة 2: يفتح صفحة التسجيل
┌──────────────────────────────┐
│ أمين المستودع               │
│ ├─ يختار نوع الشحنة         │
│ ├─ يختار نوع المادة         │
│ ├─ يختار الموقع             │
│ └─ يدخل الوزن من الميزان   │
│    (مثال: 1000 كيلو)        │
└──────────────────────────────┘
         ↓

الخطوة 3: يضغط "تسجيل الآن"
┌──────────────────────────────┐
│ النظام ينفذ:                 │
│ 1. يحدّث registration_status │
│    → "registered" ✅          │
│ 2. يحفظ registered_by & at   │
│ 3. ينشئ سجل في               │
│    registration_logs         │
│ 4. يرسل إشعار:               │
│    "تم تسجيل البضاعة"       │
│ 5. ✅ الآن تقدر تطلع         │
│    للإنتاج                   │
└──────────────────────────────┘
         ↓

الخطوة 4: العمال ياخذون البضاعة
┌──────────────────────────────┐
│ العامل:                      │
│ ├─ يختار delivery note      │
│ ├─ يقول: "بـ هنا"           │
│ └─ النظام يحدّث:            │
│    registration_status:     │
│    → "in_production"         │
│ ✅ البضاعة دخلت الإنتاج      │
└──────────────────────────────┘
```

---

### **Workflow 2: التسوية والمقارنة**

```
الخطوة 1: المحاسب يستقبل الفاتورة
┌──────────────────────────────────────┐
│ الفاتورة تصل من المورد:             │
│ INV-2024-001                         │
│ ├─ المورد: شركة الحديد              │
│ ├─ التاريخ: 2025-11-17               │
│ ├─ الكمية: 1050 كيلو                │
│ └─ المبلغ: 52,500 ريال             │
│    (1050 × 50 ريال/كيلو)            │
└──────────────────────────────────────┘
         ↓

الخطوة 2: المحاسب يدخل الفاتورة
┌──────────────────────────────────────┐
│ المحاسب في النظام:                  │
│ ├─ يختار فاتورة جديدة              │
│ ├─ يدخل البيانات                   │
│ ├─ يختار delivery notes ذات صلة:   │
│ │  ☑ DN-2025-0001 (1000 كيلو)     │
│ └─ يضغط "Link & Calculate"         │
└──────────────────────────────────────┘
         ↓

الخطوة 3: النظام يحسب الفروقات
┌──────────────────────────────────────┐
│ الحسابات (AUTOMATIC):               │
│                                      │
│ actual_weight = 1000 كيلو           │
│ invoice_weight = 1050 كيلو          │
│ weight_discrepancy = 1000 - 1050    │
│                     = -50 كيلو       │
│ discrepancy_percentage = -50/1050   │
│                        = -4.76%     │
│ financial_impact = -50 × 50         │
│                  = -2,500 ريال      │
│                                      │
│ reconciliation_status: "discrepancy" │
│ ⚠️ يحتاج قرار من المدير             │
└──────────────────────────────────────┘
         ↓

الخطوة 4: إشعار للمدير
┌──────────────────────────────────────┐
│ Dashboard Notification:              │
│                                      │
│ 🔔 جديد: Discrepancy في:            │
│    DN-2025-0001 vs INV-2024-001     │
│                                      │
│ المورد: شركة الحديد                 │
│ الفرق: +50 كيلو (+4.76%)            │
│ القيمة: 2,500 ريال                  │
│                                      │
│ [View & Decide]                     │
└──────────────────────────────────────┘
         ↓

الخطوة 5: المدير يتخذ قرار
┌──────────────────────────────────────┐
│ المدير يختار من 3 خيارات:           │
│                                      │
│ ✓ Accept: قبول الفرق               │
│   └─ reconciliation_status = "adj"   │
│   └─ ملاحظة: "OK, فرق عادي"        │
│                                      │
│ ✗ Reject: رفض الفاتورة              │
│   └─ reconciliation_status = "rej"   │
│   └─ ملاحظة: "شركة كاتبة أوزان"    │
│   └─ ترجع الفاتورة للمورد           │
│                                      │
│ 🔧 Adjust: تعديل البيانات          │
│   └─ يمكن تعديل الوزن               │
│   └─ يكتب السبب                     │
│   └─ يحفظ التغيير                   │
└──────────────────────────────────────┘
         ↓

الخطوة 6: التسجيل في Audit Trail
┌──────────────────────────────────────┐
│ في جدول reconciliation_logs:        │
│ ├─ delivery_note_id: 1              │
│ ├─ purchase_invoice_id: 5           │
│ ├─ actual_weight: 1000              │
│ ├─ invoice_weight: 1050             │
│ ├─ discrepancy_kg: -50              │
│ ├─ action: "accepted"               │
│ ├─ reason: "OK, فرق عادي"           │
│ ├─ decided_by: manager_id           │
│ └─ decided_at: 2025-11-17 15:30     │
│                                      │
│ ✅ جاهز للدفع الآن!                  │
└──────────────────────────────────────┘
```

---

## 🔐 القوانين التجارية والتحقق

### **Rule 1: لا خروج بدون تسجيل**

```php
// عند محاولة نقل البضاعة للإنتاج:
if ($deliveryNote->registration_status != 'registered' 
    && $deliveryNote->registration_status != 'in_production') {
    
    return response()->json([
        'error' => 'البضاعة لم تُسجّل في المستودع بعد!',
        'status' => $deliveryNote->registration_status,
        'action' => 'register_first'
    ], 403);
}

// يسمح بالنقل فقط
$deliveryNote->update(['registration_status' => 'in_production']);
```

---

### **Rule 2: حساب الفروقات التلقائي**

```php
// عند ربط الفاتورة:
$discrepancy = $deliveryNote->actual_weight - $invoice->weight;
$percentage = ($discrepancy / $invoice->weight) * 100;

// تحديد الحالة بناءً على الفرق:
if (abs($percentage) <= 1) {
    // فرق صغير جداً (≤1%) - اقبله تلقائياً
    $reconciliation_status = 'matched';
    $action = 'auto_accepted';
    
} else if (abs($percentage) <= 5) {
    // فرق معقول (1-5%) - احتاج موافقة
    $reconciliation_status = 'discrepancy';
    $action = 'pending_approval';
    
    // أرسل نبيه للمدير
    Notification::send($manager, new DiscrepancyAlert($deliveryNote));
    
} else {
    // فرق كبير (>5%) - لازم يراجع
    $reconciliation_status = 'discrepancy';
    $action = 'requires_review';
    
    // أرسل تنبيه عاجل
    Notification::send($manager, new LargeDiscrepancyAlert($deliveryNote));
}

// احفظ في سجل
ReconciliationLog::create([
    'delivery_note_id' => $deliveryNote->id,
    'purchase_invoice_id' => $invoice->id,
    'actual_weight' => $deliveryNote->actual_weight,
    'invoice_weight' => $invoice->weight,
    'financial_impact' => $discrepancy * 50,
    'action' => $action
]);
```

---

### **Rule 3: منع الدفع بدون تسوية**

```php
// عند محاولة دفع الفاتورة:
$unreconciled = $invoice->deliveryNotes()
    ->whereNotIn('reconciliation_status', ['matched', 'adjusted'])
    ->count();

if ($unreconciled > 0) {
    return response()->json([
        'error' => "لا يمكن دفع الفاتورة!",
        'details' => "هناك {$unreconciled} تسليمة بدون تسوية",
        'action' => 'complete_reconciliation_first'
    ], 403);
}

// يسمح بالدفع فقط
$invoice->update(['status' => 'ready_to_pay']);
```

---

### **Rule 4: منع التعديل بعد التسجيل**

```php
// عند محاولة تعديل البيانات:
if ($deliveryNote->registration_status == 'in_production') {
    return response()->json([
        'error' => 'لا يمكن تعديل بيانات البضاعة بعد دخول الإنتاج',
        'status' => 'locked_for_editing'
    ], 403);
}

// يسمح بالتعديل فقط قبل التسجيل
$deliveryNote->update($data);
```

---

## 📊 التقارير المهمة

### **تقرير 1: أداء الموردين (Supplier Performance)**

```
Period: November 2025

═══════════════════════════════════════════════════════════

Supplier: شركة الحديد
───────────────────
Total Shipments: 15
├─ Registered: 15 ✅
├─ In Production: 10 🏭
└─ Completed: 15 ✅

Reconciliation Results:
├─ Matched (متطابق): 13 ✅ (86.7%)
├─ Discrepancies: 2 ⚠️ (13.3%)
│  ├─ DN-001: +50 kg (+5.0%)
│  └─ DN-007: +75 kg (+3.2%)
└─ Rejected: 0 ❌ (0%)

Financial Impact:
├─ Total Charged: 750,000 SAR
├─ Discrepancies: 6,250 SAR (+0.83%)
├─ Adjusted: 5,000 SAR
└─ Outstanding: 1,250 SAR (pending negotiation)

Average Accuracy: 98.2% ✅

Recommendation: Acceptable, slight overcharging pattern
Action: Follow up on discrepancy details

═══════════════════════════════════════════════════════════

Supplier: شركة المعادن
──────────────────
Total Shipments: 8
└─ All Matched: 8 ✅ (100%)

Financial Impact:
└─ Discrepancies: 0 ✅

Average Accuracy: 100% ⭐

Recommendation: Excellent supplier!

═══════════════════════════════════════════════════════════
```

---

### **تقرير 2: التسجيل والتسوية اليومية**

```
Daily Report: 2025-11-17
═══════════════════════════════════════════════════════════

📦 Registration Status:
├─ New Arrivals: 12
├─ Registered: 10 (83%)
├─ In Production: 8 (67%)
├─ Completed: 6 (50%)
└─ Pending: 2 ⏳ (17%)

⚠️ Alerts:
└─ 2 Shipments NOT REGISTERED (over 2 hours)
   └─ Action Required: Contact Warehouse Manager

🔄 Reconciliation Status:
├─ Pending Invoice: 3 ⏳
├─ Matched: 5 ✅
├─ Discrepancies: 2 ⚠️
├─ Adjusted: 4 ✅
└─ Rejected: 0 ❌

💰 Financial Summary:
├─ Total Invoiced: 500,000 SAR
├─ Discrepancies: 7,500 SAR (+1.5%)
├─ Adjustments: -5,000 SAR
└─ Net Variance: +2,500 SAR

Action Items:
□ Review 2 pending reconciliations
□ Follow up with Supplier #3
□ Register 2 pending shipments

═══════════════════════════════════════════════════════════
```

---

## ✅ Validation Rules (قواعد التحقق)

### **عند التسجيل:**

```
☐ registration_status == 'not_registered'
☐ actual_weight > 0
☐ supplier_id exists
☐ material_type_id exists
☐ location is not empty
☐ registered_by (current user)
☐ registered_at (current timestamp)
```

### **عند الربط بالفاتورة:**

```
☐ delivery_note_id exists
☐ purchase_invoice_id exists
☐ invoice_weight > 0
☐ registration_status == 'registered' or 'in_production'
☐ reconciliation_status != 'adjusted' and != 'rejected'
```

### **عند اتخاذ قرار:**

```
☐ reconciliation_status == 'discrepancy' or 'pending'
☐ action in ['accepted', 'rejected', 'adjusted']
☐ decided_by (current user)
☐ decided_at (current timestamp)
☐ reason is not empty
```

---

## 🎯 ملخص الفوائد

```
✅ منع الخروج بدون تسجيل
   └─ كل بضاعة يكون فيها سجل

✅ تسوية تلقائية ودقيقة
   └─ النظام يحسب الفروقات بنفسه

✅ شفافية كاملة
   └─ كل جهة تعرف دورها وحقها

✅ حماية مالية
   └─ الفروقات تنكتشف قبل الدفع

✅ Audit Trail كامل
   └─ معرفة من فعل إيش ومتى

✅ تقارير دقيقة
   └─ تقييم أداء الموردين بدقة

✅ عملية سريعة
   └─ Automation للحسابات والتقارير
```

---

## 📞 نقاط للمراجعة مع العميل

قبل البدء بالبرمجة:

1. **حدود الفروقات المقبولة؟**
   - متى نعتبر الفرق "طبيعي"؟
   - متى نعتبره "مهم"؟

2. **قبول الفروقات تلقائياً؟**
   - هل نقبل الفروقات الصغيرة (< 1%) تلقائياً؟
   - أم كل فرق يحتاج موافقة؟

3. **رفع الفاتورة الناقصة؟**
   - لو رفضنا الفاتورة، هل ترجع تلقائياً؟
   - أم نحتاج محاورة يدوية؟

4. **الأسعار المختلفة؟**
   - الوزن = الوزن
   - الحساب: الكيلو × 50 ريال (مثال)
   - صحيح هذا السعر الثابت؟

5. **التقارير المطلوبة؟**
   - تقرير أداء الموردين يومي/أسبوعي/شهري؟
   - تقرير التسوية يومي؟
   - تقارير إضافية؟

---

**تاريخ الإنشاء:** 17 نوفمبر 2025  
**الإصدار:** 1.0  
**الحالة:** جاهز للتطوير
