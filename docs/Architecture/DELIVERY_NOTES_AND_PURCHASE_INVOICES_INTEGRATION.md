# 📦 ربط أذنات التسليم بفواتير الشراء
## Integration Guide: Delivery Notes ↔ Purchase Invoices

---

## 🎯 ملخص المشكلة والحل

### ❌ المشكلة الحالية:

```
Timeline الوضع الحالي (خاطئ):
┌─────────────────────────────────────────────────────────────┐
│ اليوم 1: الشاحنة تصل                                        │
│ ├─ أمين المستودع يسجل delivery note                      │
│ │  └─ بدون معرفة وزن الفاتورة                             │
│                                                             │
│ اليوم 3: المحاسب يستقبل الفاتورة                          │
│ ├─ يدخل البيانات في نظام محاسبي منفصل                     │
│ ├─ ما عنده ربط بـ delivery note!                          │
│ └─ ما يعرف إذا الأوزان متطابقة ولا لا                    │
│                                                             │
│ النتيجة:
│ ⚠️ بيانات مشتتة في نظامين منفصلين
│ ⚠️ ما في طريقة للمقارنة بين الفعلي والفاتورة
│ ⚠️ الفروقات ما تنكتشف إلا لو حد عد يدوي
└─────────────────────────────────────────────────────────────┘
```

### ✅ الحل المقترح:

```
Timeline الوضع الجديد (صحيح):
┌─────────────────────────────────────────────────────────────┐
│ اليوم 1: أمين المستودع                                     │
│ ├─ يسجل delivery note بـ actual_weight                     │
│ ├─ purchase_invoice_id = NULL (في الانتظار)             │
│ └─ Status = "pending_invoice"                             │
│                                                             │
│ اليوم 3: المحاسب                                            │
│ ├─ يدخل فاتورة شراء جديدة                                  │
│ ├─ يختار delivery notes من قائمة                          │
│ └─ يربط الفاتورة بـ delivery notes                        │
│                                                             │
│ النظام تلقائياً:                                           │
│ ├─ يحسب الفرق (actual - invoice)                          │
│ ├─ يحدّث status إلى "invoice_linked"                     │
│ └─ يعرض الفروقات للمدير                                   │
│                                                             │
│ النتيجة:
│ ✅ كل البيانات في نظام واحد
│ ✅ الفروقات محسوبة تلقائياً
│ ✅ الجميع عندهم visibility على الحقيقة
└─────────────────────────────────────────────────────────────┘
```

---

## 🔗 أنواع الربط الممكنة

### **النوع 1: One-to-One (واحد لواحد)**

```
Delivery Note: DN-2025-0001
       ↕️ (واحد فقط)
Purchase Invoice: INV-2024-001
```

**الخصائص:**
- كل تسليمة مرتبطة بفاتورة واحدة فقط
- كل فاتورة مرتبطة بتسليمة واحدة فقط

**متى نستخدمه:**
- المورد يرسل فاتورة منفصلة لكل شحنة

**المشكلة:**
```
اليوم 1: شحنة 1 (800 كيلو) → DN-2025-0001
اليوم 1: شحنة 2 (700 كيلو) → DN-2025-0002  
اليوم 1: شحنة 3 (500 كيلو) → DN-2025-0003

اليوم 4: المورد يرسل فاتورة واحدة تقول:
         "إجمالي شحنات هذا الأسبوع: 2000 كيلو"
         
❌ مشكلة! الفاتورة واحدة لكن التسليمات 3!
```

---

### **النوع 2: One-to-Many (واحد لمتعدد) ⭐ الأفضل**

```
Purchase Invoice: INV-2024-001
          ↓
    linked to MULTIPLE
          ↓
┌─────────────────────────────────┐
│ Delivery Notes:                 │
│ ├─ DN-2025-0001 (800 كيلو)    │
│ ├─ DN-2025-0002 (700 كيلو)    │
│ └─ DN-2025-0003 (500 كيلو)    │
│   الإجمالي: 2000 كيلو          │
└─────────────────────────────────┘
```

**الخصائص:**
- فاتورة واحدة تغطي عدة تسليمات
- كل تسليمة تنتمي لفاتورة واحدة فقط

**متى نستخدمه:**
- المورد يرسل "consolidated invoice" (فاتورة موحدة) لعدة شحنات
- **هذا ما يحصل في الواقع العملي عادة**

**الفوائد:**
```
✅ يعكس الواقع: المورد يرسل فاتورة موحدة لكل يوم أو أسبوع
✅ سهل في الإدارة: فاتورة واحدة = مدفوعة كاملة أو لا
✅ الربط واضح: كل تسليمة تعرف الفاتورة بتاعتها
✅ حسابات سهلة: جمع الأوزان وقارن مع الفاتورة
```

---

### **النوع 3: Many-to-Many (متعدد لمتعدد) ❌ تجنبه**

```
⚠️ معقد جداً ولا نبيه:

Purchase Invoice A
     ├─ DN-1 ✅
     ├─ DN-2 ✅ (مشترك مع الفاتورة B!)
     └─ DN-3 ✅

Purchase Invoice B
     ├─ DN-2 (مشترك!) ⚠️
     ├─ DN-4 ✅
     └─ DN-5 ✅

❌ مشكلة: DN-2 مرتبطة بفاتورتين! أيهما ندفع؟
❌ حسابات معقدة جداً
❌ audit trail معقد
```

---

## 🏆 الحل الموصى به: Many-to-One

**الفكرة البسيطة:**
```
كل delivery note يشير إلى فاتورة شراء واحدة (أو لا يشير إلى أي واحدة)
لكن فاتورة شراء واحدة تقدر تكون مشار إليها من عدة delivery notes
```

### **الرسم البياني:**

```
┌─────────────────────────────────────────────────────────┐
│                 Purchase Invoice                        │
│              INV-2024-001 (موحدة)                     │
│              ────────────────────                      │
│              إجمالي الكمية: 2000 كيلو                  │
│              الإجمالي: 100,000 ريال                   │
│              المورد: شركة الحديد                       │
│              الحالة: pending                           │
└─────────────────────────────────────────────────────────┘
              ↑           ↑           ↑
              │           │           │
         has_many(DeliveryNotes)
              │           │           │
              ↓           ↓           ↓
    ┌──────────────┐  ┌──────────────┐  ┌──────────────┐
    │ Delivery     │  │ Delivery     │  │ Delivery     │
    │ Note         │  │ Note         │  │ Note         │
    │ DN-0001      │  │ DN-0002      │  │ DN-0003      │
    ├──────────────┤  ├──────────────┤  ├──────────────┤
    │ وزن: 800 kg │  │ وزن: 700 kg │  │ وزن: 500 kg │
    │ invoice_id: │  │ invoice_id: │  │ invoice_id: │
    │ INV-001 ✅  │  │ INV-001 ✅  │  │ INV-001 ✅  │
    └──────────────┘  └──────────────┘  └──────────────┘
         belongs_to(PurchaseInvoice)
```

---

## 👥 الجهات والمسؤوليات

### **1️⃣ أمين المستودع (Warehouse Manager)**

**ماذا يسوي؟**
```
الشاحنة تصل ← يوزنها ← يسجل في النظام
```

**الحقول اللي يملأها:**
| الحقل | المثال | ملاحظات |
|--------|--------|---------|
| `type` | `incoming` | يختار من الخيارات |
| `supplier_id` | 5 | من قائمة الموردين |
| `material_type_id` | 3 | نوع المادة |
| `actual_weight` | 800 | **من الميزان - حقيقي** |
| `location` | "Area-A" | أين حط المواد |
| `recorded_by` | user_id | تسجيل تلقائي |

**الحقول اللي ما يملأها:**
| الحقل | السبب |
|--------|------|
| `purchase_invoice_id` | NULL (للمحاسب لاحقاً) |
| `invoice_weight` | NULL (من الفاتورة) |
| `status` | يتحدّث تلقائياً |

**مثال الحالة:**
```php
DeliveryNote {
    id: 1,
    type: "incoming",
    actual_weight: 800,        // ← ملأها أمين المستودع
    purchase_invoice_id: null, // ← فارغ (ينتظر المحاسب)
    status: "pending_invoice", // ← في انتظار الفاتورة
    recorded_at: "2025-11-17 09:00:00"
}
```

---

### **2️⃣ المحاسب (Accountant)**

**ماذا يسوي؟**
```
استقبل الفاتورة ← دخل البيانات ← ربط مع التسليمات
```

**الحقول اللي يملأها:**
| الحقل | المثال | ملاحظات |
|--------|--------|---------|
| `invoice_number` | "INV-2024-001" | من المورد |
| `supplier_id` | 5 | المورد |
| `invoice_date` | "2025-11-17" | متى الفاتورة |
| `due_date` | "2025-12-17" | دفع بعد 30 يوم |
| `total_amount` | 100000 | المبلغ الكلي |
| `currency` | "SAR" | العملة |
| `payment_terms` | "30 يوم" | شروط الدفع |

**الخطوة الثانية - الربط:**
```
المحاسب يفتح الفاتورة ويقول:
"هذي الفاتورة من شركة الحديد"

النظام يعرض له:
┌────────────────────────────────────────┐
│ Unlinked Delivery Notes                 │
│ من نفس المورد (شركة الحديد)            │
├────────────────────────────────────────┤
│ □ DN-2025-0001 (800 كيلو)             │
│ □ DN-2025-0002 (700 كيلو)             │
│ □ DN-2025-0003 (500 كيلو)             │
└────────────────────────────────────────┘

المحاسب يختار الـ 3 (يقول: هذي الفاتورة تغطيهم)
```

**النظام يعمل تلقائياً:**
```php
// عند الربط:
foreach (selectedDeliveryNotes as $dn) {
    $dn->purchase_invoice_id = $invoice->id;
    $dn->status = "invoice_linked";  // تغيير الحالة
    $dn->save();
    
    // حساب الفرق:
    $dn->weight_discrepancy = $dn->actual_weight - $fis->invoice_weight_for_this_dn;
}

// تحديث حالة الفاتورة:
$invoice->status = "pending_approval";  // بانتظار موافقة المدير
$invoice->save();
```

---

### **3️⃣ مدير الإنتاج (Production Manager)**

**ماذا يسوي؟**
```
يراقب ← يحلل الفروقات ← يتخذ قرار
```

**التقرير اللي يراه:**
```
Purchase Invoice: INV-2024-001
────────────────────────────────────────
المورد: شركة الحديد
الفاتورة: 2000 كيلو
الفعلي: 2000 كيلو
الفرق: 0 كيلو ✅

معلومات التفصيل:
├─ DN-2025-0001: 800 (فعلي) ↔ 800 (فاتورة) = 0 ✅
├─ DN-2025-0002: 700 (فعلي) ↔ 700 (فاتورة) = 0 ✅
└─ DN-2025-0003: 500 (فعلي) ↔ 500 (فاتورة) = 0 ✅

الإجراء:
[✓ Approve] [✗ Reject] [💬 Comment]
```

**حالة بها فرق:**
```
Purchase Invoice: INV-2024-002
────────────────────────────────────────
المورد: شركة الحديد
الفاتورة: 2150 كيلو  ← المورد كاتب أكثر!
الفعلي: 2000 كيلو
الفرق: +150 كيلو ⚠️ (150 كيلو زيادة)
القيمة: 150 × 50 = 7,500 ريال ⚠️

معلومات التفصيل:
├─ DN-2025-0004: 1100 (فعلي) ↔ 1250 (فاتورة) = +150 ⚠️
├─ DN-2025-0005: 900 (فعلي) ↔ 900 (فاتورة) = 0 ✅

⚠️ شركة الحديد كاتبة 150 كيلو أكثر من اللي بعتت!

الإجراء:
[✓ Approve Anyway] [✗ Reject] [💬 Negotiate]
```

---

## 📊 السيناريوهات العملية

### **السيناريو 1: كل شيء متطابق (الحالة المثالية)**

```
الشاحنة تصل - اليوم 1 صباح:
├─ أمين المستودع:
│  ├─ يوزن المواد: 1000 كيلو
│  ├─ يسجل DN-2025-0001
│  │  ├─ actual_weight: 1000
│  │  ├─ purchase_invoice_id: NULL
│  │  └─ status: "pending_invoice"
│  └─ ✅ خلص

المورد يرسل الفاتورة - اليوم 3:
├─ المحاسب:
│  ├─ يدخل INV-2024-001
│  │  ├─ supplier: شركة الحديد
│  │  ├─ invoice_weight: 1000
│  │  └─ total: 50,000
│  ├─ يختار DN-2025-0001
│  └─ النظام يربطهم
│     ├─ DN.purchase_invoice_id = INV.id
│     ├─ DN.status = "invoice_linked"
│     ├─ DN.weight_discrepancy = 1000 - 1000 = 0 ✅
│     └─ INV.status = "pending_approval"

المدير يراجع - اليوم 4:
├─ يرى التقرير:
│  ├─ الفرق: 0 كيلو ✅
│  ├─ كل شيء متطابق
│  └─ [✓ Approve]
└─ النتيجة:
   ├─ INV.status = "approved"
   ├─ DN.status = "approved"
   └─ جاهز للدفع ✅
```

---

### **السيناريو 2: شحنات متعددة + فاتورة موحدة**

```
الشاحنات تصل - اليوم 1:
├─ صباح (8:00):
│  ├─ Truck 1: 800 كيلو → DN-2025-0001
│  │  ├─ actual_weight: 800
│  │  └─ status: "pending_invoice"
│  │
├─ ظهر (12:00):
│  ├─ Truck 2: 700 كيلو → DN-2025-0002
│  │  ├─ actual_weight: 700
│  │  └─ status: "pending_invoice"
│  │
└─ عصر (16:00):
   ├─ Truck 3: 500 كيلو → DN-2025-0003
   │  ├─ actual_weight: 500
   │  └─ status: "pending_invoice"

   الإجمالي الفعلي: 2000 كيلو

المورد يرسل فاتورة موحدة - اليوم 4:
├─ المحاسب يدخل: INV-2024-001
│  ├─ المورد: شركة الحديد
│  ├─ الكمية: 2000 كيلو (تغطية أسبوعية)
│  ├─ المبلغ: 100,000 ريال
│  └─ currency: SAR
│
├─ المحاسب يختار: "Select Linked Delivery Notes"
│  ├─ يضيف DN-2025-0001 (800)
│  ├─ يضيف DN-2025-0002 (700)
│  └─ يضيف DN-2025-0003 (500)
│     النظام يقول: "الإجمالي = 2000 ✅"
│
└─ المحاسب يسجل معلومات لكل DN:
   ├─ DN-0001:
   │  ├─ invoice_weight_for_this_dn: 800
   │  └─ discrepancy: 800 - 800 = 0 ✅
   │
   ├─ DN-0002:
   │  ├─ invoice_weight_for_this_dn: 700
   │  └─ discrepancy: 700 - 700 = 0 ✅
   │
   └─ DN-0003:
      ├─ invoice_weight_for_this_dn: 500
      └─ discrepancy: 500 - 500 = 0 ✅

النتيجة:
├─ كل DN.purchase_invoice_id = INV-2024-001
├─ كل DN.status = "invoice_linked"
├─ INV.status = "pending_approval"
└─ المدير يرى: فاتورة موحدة صحيحة ✅
```

---

### **السيناريو 3: الحالة الخطيرة - فروقات كبيرة**

```
الشاحنات الفعلية - اليوم 1:
├─ DN-2025-0010: 1100 كيلو (فعلي)
├─ DN-2025-0011: 900 كيلو (فعلي)
└─ الإجمالي الفعلي: 2000 كيلو

الفاتورة من المورد - اليوم 4:
├─ INV-2024-002
├─ الكمية المدرجة: 2150 كيلو ← أكثر من الفعلي! ⚠️
└─ المبلغ: 107,500 ريال

المحاسب يربط:
├─ DN-0010:
│  ├─ actual: 1100
│  ├─ invoice: 1250 ← المورد كاتب أكثر!
│  └─ discrepancy: +150 ⚠️
│
└─ DN-0011:
   ├─ actual: 900
   ├─ invoice: 900
   └─ discrepancy: 0 ✅

النظام يحسب:
├─ الفرق الإجمالي: +150 كيلو
├─ القيمة المالية: 150 × 50 = 7,500 ريال
└─ التنبيه: ⚠️ DISCREPANCY DETECTED

المدير يرى التقرير:
┌─────────────────────────────────────────┐
│ ⚠️ Invoice Discrepancy Alert            │
├─────────────────────────────────────────┤
│ Invoice: INV-2024-002                   │
│ Supplier: شركة الحديد                 │
│                                         │
│ Invoice Claims: 2150 kg                 │
│ Actual Received: 2000 kg                │
│ Difference: +150 kg ⚠️                 │
│ Financial Impact: 7,500 SAR ⚠️         │
│                                         │
│ Details:                                │
│ ├─ DN-0010: +150 kg discrepancy        │
│ └─ DN-0011: 0 kg discrepancy           │
│                                         │
│ Recommendation:                         │
│ "شركة الحديد كاتبة وزن أكثر من الفعلي" │
│ يمكنك:                                  │
│ [✓ Approve Anyway]                     │
│ [✗ Reject Invoice]                     │
│ [📞 Contact Supplier]                  │
│ [💬 Add Comment]                       │
└─────────────────────────────────────────┘

المدير يضيف ملاحظة:
├─ "شركة الحديد دايماً تكتب أوزان عالية"
├─ "الفرق معقول لكن لازم نقول لهم"
└─ Status: "discrepancy_noted"

النتيجة:
├─ INV.status = "discrepancy_noted"
├─ DN-0010.status = "discrepancy_noted"
├─ يتم الحفظ في audit log:
│  ├─ من: دخل الفاتورة بـ 2150
│  ├─ إلى: منخفض لـ 2000
│  └─ السبب: من الميزان الفعلي
└─ الفاتورة ما تتدفع لين التصحيح ✅
```

---

## 🔄 حالات الـ Status (الحالات)

### **لـ Delivery Notes:**

```
pending_invoice
    ↓
    (عند ربط بفاتورة)
    ↓
invoice_linked
    ↓
    (حسب قرار المدير)
    ├─→ approved ✅
    ├─→ discrepancy_noted ⚠️
    └─→ rejected ❌
```

### **لـ Purchase Invoices:**

```
draft
    ↓ (المحاسب يدخل الفاتورة)
pending_approval
    ↓ (المدير يراجع)
    ├─→ approved ✅
    ├─→ discrepancy_noted ⚠️
    ├─→ rejected ❌
    └─→ pending_payment
        ↓ (عند الدفع)
        └─→ paid ✅
```

---

## 📈 التقارير المهمة

### **تقرير 1: Supplier Performance**

```
Supplier: شركة الحديد

Total Invoices: 10
Total Shipments: 28
Discrepancies Found: 7

Average Discrepancy: +2.3% (المورد يكتب أوزان أعلى)

Breakdown:
├─ Invoice 1: 0% difference ✅
├─ Invoice 2: +3.2% difference ⚠️
├─ Invoice 3: +1.8% difference ⚠️
├─ Invoice 4: 0% difference ✅
├─ Invoice 5: +2.5% difference ⚠️
├─ Invoice 6: -0.5% difference (أقل من الفعلي) ✅
├─ Invoice 7: +2.1% difference ⚠️
├─ Invoice 8: +1.9% difference ⚠️
├─ Invoice 9: +3.8% difference ⚠️
└─ Invoice 10: 0% difference ✅

Financial Impact:
├─ Total Overcharged: 45,000 SAR
├─ Adjustment Made: 35,000 SAR
└─ Pending Negotiation: 10,000 SAR

Recommendation:
"قلل من هذا المورد أو اطلب تصحيح دوري"
```

### **تقرير 2: Monthly Reconciliation**

```
Period: November 2025

Summary:
├─ Invoices Received: 15
├─ Delivery Notes: 52
├─ Fully Matched: 13 ✅
├─ Discrepancies: 2 ⚠️
└─ Pending: 0

Discrepancies Detail:
├─ INV-2024-009 (Supplier A): +150 kg ⚠️
└─ INV-2024-014 (Supplier B): -75 kg ⚠️

Total Financial Variance: -22,500 SAR (في صالحنا)

Status:
├─ Ready to Pay: 13 invoices
├─ Needs Review: 2 invoices
└─ Awaiting: 0 invoices
```

---

## 🗄️ جداول قاعدة البيانات المطلوبة

### **جدول: delivery_notes (تعديلات)**

```sql
ALTER TABLE delivery_notes ADD COLUMN (
    purchase_invoice_id BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (purchase_invoice_id) 
        REFERENCES purchase_invoices(id) 
        ON DELETE SET NULL
);

-- INDEX للبحث السريع
CREATE INDEX idx_delivery_notes_invoice 
ON delivery_notes(purchase_invoice_id);
```

### **جدول جديد: delivery_note_invoice_details**

```sql
CREATE TABLE delivery_note_invoice_details (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    delivery_note_id BIGINT UNSIGNED NOT NULL,
    purchase_invoice_id BIGINT UNSIGNED NOT NULL,
    
    -- الوزن المدرج في الفاتورة لهذي التسليمة
    invoice_weight_for_this_dn DECIMAL(10,2),
    
    -- الفرق المحسوب
    weight_discrepancy DECIMAL(10,2) GENERATED ALWAYS AS (
        -- سنحسبه في الـ application layer
    ),
    
    -- ملاحظات من المحاسب
    notes TEXT NULL,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (delivery_note_id) REFERENCES delivery_notes(id),
    FOREIGN KEY (purchase_invoice_id) REFERENCES purchase_invoices(id),
    
    UNIQUE KEY unique_dn_invoice (delivery_note_id, purchase_invoice_id)
);
```

---

## 💻 العلاقات في Models

### **DeliveryNote Model:**

```php
// العلاقة الجديدة:
public function purchaseInvoice(): BelongsTo
{
    return $this->belongsTo(PurchaseInvoice::class);
}

// الحقول الجديدة:
protected $fillable = [
    // ... الحقول القديمة ...
    'purchase_invoice_id',  // NEW
];

// الـ casts:
protected $casts = [
    // ... القديم ...
    'purchase_invoice_id' => 'integer',
];

// helper methods:
public function isLinkedToInvoice(): bool
{
    return $this->purchase_invoice_id !== null;
}

public function getWeightDiscrepancy(): float
{
    if (!$this->purchaseInvoice) {
        return 0;
    }
    // النظام سيحسبها من جدول details
    return $this->getDiscrepancyFromDetails();
}
```

### **PurchaseInvoice Model:**

```php
// العلاقة الجديدة:
public function deliveryNotes(): HasMany
{
    return $this->hasMany(DeliveryNote::class);
}

// helper method:
public function getTotalActualWeight(): float
{
    return $this->deliveryNotes()
        ->sum('actual_weight');
}

public function getTotalInvoiceWeight(): float
{
    // من جدول details
    return DB::table('delivery_note_invoice_details')
        ->where('purchase_invoice_id', $this->id)
        ->sum('invoice_weight_for_this_dn');
}

public function getTotalDiscrepancy(): float
{
    return $this->getTotalActualWeight() 
         - $this->getTotalInvoiceWeight();
}

public function hasDiscrepancies(): bool
{
    return abs($this->getTotalDiscrepancy()) > 0.01; // tolerance
}
```

---

## 🎬 خطوات التنفيذ

### **Phase 1: تحضير قاعدة البيانات**
```
1. إضافة migration:
   - إضافة purchase_invoice_id إلى delivery_notes
   - إنشاء جدول delivery_note_invoice_details

2. تحديث Models:
   - إضافة العلاقات الجديدة
```

### **Phase 2: واجهة المحاسب**
```
1. صفحة جديدة: "Link Invoice to Delivery Notes"
   - اختيار الفاتورة
   - عرض unlinked delivery notes من نفس المورد
   - اختيار delivery notes للربط
   - إدخال invoice_weight لكل واحد
   - حساب الفروقات تلقائياً
   - زر: Save & Link

2. تحديث صفحة show للفاتورة:
   - عرض جميع linked delivery notes
   - عرض الفروقات
```

### **Phase 3: واجهة المدير**
```
1. تقرير جديد: "Invoice Discrepancies"
   - فلتر حسب المورد
   - فلتر حسب نطاق التاريخ
   - عرض الفروقات
   - زر Approve/Reject

2. Dashboard update:
   - عرض invoices pending approval
   - تحذيرات للفروقات الكبيرة
```

### **Phase 4: Notifications & Logging**
```
1. إرسال إشعارات:
   - إخطار المدير بـ invoices الجديدة
   - تنبيه للفروقات الكبيرة

2. Audit Trail:
   - تسجيل كل ربط
   - تسجيل كل موافقة/رفض
```

---

## ✅ Checklist قبل البدء

```
☐ الموافقة على النموذج من العميل
☐ فهم واضح للحالات والفروقات
☐ تحديد tolerance للفروقات الصغيرة (مثلاً 1% أو 50 كيلو)
☐ توثيق شروط الموافقة الآلية
☐ قائمة الأذونات (من يقدر يوافق؟ من يقدر يرفض؟)
☐ خطة الإخطارات (بريد أم SMS أم في النظام؟)
☐ النسخة الاحتياطية من البيانات
☐ خطة الاختبار
```

---

## 📞 أسئلة مهمة للعميل

قبل البدء في التطوير:

1. **متى تكتشف الفروقات بـ توقف العملية؟**
   - لو الفرق > 500 كيلو تحتاج موافقة المدير؟

2. **كيف تتعامل مع الفروقات المنخفضة؟**
   - لو الفرق < 50 كيلو، تتجاهله تلقائياً؟

3. **من يقدر يوافق على الفروقات؟**
   - المدير فقط؟
   - الأمين أيضاً؟

4. **هل تبي قائمة سوداء للموردين؟**
   - إذا تكرر الفرق مع مورد معين

5. **كيف تتعامل مع الفاتورة المرفوضة؟**
   - تعود للمورد؟
   - تحفظ للمراجعة؟

---

## 🎯 الخلاصة

```
✅ الحل يوفر:
├─ Unified view لـ الشحنات والفواتير
├─ Automatic discrepancy calculation
├─ Full audit trail
├─ Supplier performance tracking
└─ Data-driven decision making

📊 الفوائد:
├─ 🛡️ حماية مالية (الفروقات تنكتشف فوراً)
├─ 👥 شفافية (كل جهة تعرف دورها)
├─ 📈 تقارير دقيقة (لتقييم الموردين)
└─ ⚡ عملية سريعة (automate الحسابات)
```

---

**تاريخ التحديث:** 17 نوفمبر 2025  
**الإصدار:** 1.0  
**الحالة:** جاهز للمناقشة مع العميل
