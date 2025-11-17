# هيكلة جداول المراحل المرنة

## نظرة عامة
تم تصميم جداول المراحل لتدعم **المرونة الكاملة** في التنقل بين المراحل، مما يسمح بـ:
- التدفق الطبيعي: warehouse → stage1 → stage2 → stage3 → stage4
- القفز بين المراحل: warehouse → stage2 مباشرة
- القفز المتقدم: warehouse → stage4 مباشرة
- أي تسلسل ممكن حسب احتياجات الإنتاج

---

## 📊 الجداول والحقول

### 1️⃣ **stage1_stands** (المرحلة الأولى - التقسيم على الاستاندات)

```sql
id                  BIGINT          PK
barcode             VARCHAR(50)     UNIQUE - ST1-2025-001
parent_barcode      VARCHAR(50)     باركود المادة الخام (WH-XXX-2025)
material_id         BIGINT          FK → materials
stand_number        VARCHAR(50)     رقم الاستاند
wire_size           VARCHAR(20)     مقاس السلك
weight              DECIMAL(10,3)   الوزن الإجمالي
waste               DECIMAL(10,3)   كمية الهدر
remaining_weight    DECIMAL(10,3)   الوزن المتبقي
status              ENUM            created, in_process, completed, consumed
created_by          BIGINT          FK → users
completed_at        TIMESTAMP       
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**السيناريوهات:**
- ✅ warehouse → stage1: `parent_barcode = WH-XXX-2025`, `material_id = 5`

---

### 2️⃣ **stage2_processed** (المرحلة الثانية - المعالجة)

```sql
id                  BIGINT          PK
barcode             VARCHAR(50)     UNIQUE - ST2-2025-001
parent_barcode      VARCHAR(50)     باركود المصدر (WH-XXX أو ST1-XXX)
stage1_id           BIGINT          FK → stage1_stands (NULLABLE) ✨
material_id         BIGINT          FK → materials (NULLABLE) ✨
wire_size           VARCHAR(20)     مقاس السلك ✨
process_details     TEXT            تفاصيل المعالجة
input_weight        DECIMAL(10,3)   وزن الدخول
output_weight       DECIMAL(10,3)   وزن الخروج
waste               DECIMAL(10,3)   كمية الهدر
remaining_weight    DECIMAL(10,3)   الوزن المتبقي
status              ENUM            started, in_progress, completed, consumed
notes               TEXT            ملاحظات ✨
created_by          BIGINT          FK → users
completed_at        TIMESTAMP
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**السيناريوهات:**
- ✅ stage1 → stage2: `parent_barcode = ST1-XXX-2025`, `stage1_id = 10`, `material_id = NULL`
- ✅ warehouse → stage2: `parent_barcode = WH-XXX-2025`, `stage1_id = NULL`, `material_id = 5`

**الحقول الجديدة:**
- `material_id`: للربط المباشر مع المادة الخام عند القفز من المستودع
- `wire_size`: توحيد مع stage1
- `notes`: ملاحظات إضافية

---

### 3️⃣ **stage3_coils** (المرحلة الثالثة - الملفات)

```sql
id                  BIGINT          PK
barcode             VARCHAR(50)     UNIQUE - CO3-2025-001
parent_barcode      VARCHAR(50)     باركود المصدر (WH-XXX أو ST1-XXX أو ST2-XXX)
stage2_id           BIGINT          FK → stage2_processed (NULLABLE) ✨
material_id         BIGINT          FK → materials (NULLABLE) ✨
stage1_id           BIGINT          FK → stage1_stands (NULLABLE) ✨
coil_number         VARCHAR(50)     رقم الملف
wire_size           VARCHAR(20)     مقاس السلك
base_weight         DECIMAL(10,3)   الوزن الأساسي
dye_weight          DECIMAL(10,3)   وزن الصبغة
plastic_weight      DECIMAL(10,3)   وزن البلاستيك
total_weight        DECIMAL(10,3)   الوزن الكلي
color               VARCHAR(50)     اللون
waste               DECIMAL(10,3)   كمية الهدر
dye_type            VARCHAR(100)    نوع الصبغة
plastic_type        VARCHAR(100)    نوع البلاستيك
status              ENUM            created, in_process, completed, packed
notes               TEXT            ملاحظات ✨
created_by          BIGINT          FK → users
completed_at        TIMESTAMP
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**السيناريوهات:**
- ✅ stage2 → stage3: `parent_barcode = ST2-XXX`, `stage2_id = 15`, `stage1_id = NULL`, `material_id = NULL`
- ✅ stage1 → stage3: `parent_barcode = ST1-XXX`, `stage2_id = NULL`, `stage1_id = 10`, `material_id = NULL`
- ✅ warehouse → stage3: `parent_barcode = WH-XXX`, `stage2_id = NULL`, `stage1_id = NULL`, `material_id = 5`

**الحقول الجديدة:**
- `material_id`: للقفز المباشر من المستودع
- `stage1_id`: للقفز من stage1 مباشرة (تخطي stage2)
- `notes`: ملاحظات إضافية

---

### 4️⃣ **stage4_boxes** (المرحلة الرابعة - الصناديق)

```sql
id                  BIGINT          PK
barcode             VARCHAR(50)     UNIQUE - BOX4-2025-001
parent_barcode      VARCHAR(50)     باركود المصدر ✨
material_id         BIGINT          FK → materials (NULLABLE) ✨
packaging_type      VARCHAR(100)    نوع التغليف
coils_count         INT             عدد الملفات
total_weight        DECIMAL(10,3)   الوزن الكلي
waste               DECIMAL(10,3)   كمية الهدر
status              ENUM            packing, packed, shipped, delivered
customer_info       TEXT            بيانات العميل
shipping_address    TEXT            عنوان الشحن
tracking_number     VARCHAR(100)    رقم التتبع
notes               TEXT            ملاحظات ✨
created_by          BIGINT          FK → users
packed_at           TIMESTAMP
shipped_at          TIMESTAMP
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**السيناريوهات:**
- ✅ stage3 → stage4: `parent_barcode = CO3-XXX`, `material_id = NULL` (يتم الربط عبر box_coils)
- ✅ warehouse → stage4: `parent_barcode = WH-XXX`, `material_id = 5` (تعبئة مباشرة)

**الحقول الجديدة:**
- `parent_barcode`: باركود آخر مرحلة في السلسلة
- `material_id`: للقفز المباشر من المستودع
- `notes`: ملاحظات إضافية

---

### 5️⃣ **box_coils** (علاقة الصناديق بالملفات)

```sql
id          BIGINT          PK
box_id      BIGINT          FK → stage4_boxes
coil_id     BIGINT          FK → stage3_coils
added_at    TIMESTAMP       
```

**الاستخدام:**
- ربط ملف واحد أو أكثر بصندوق واحد
- يُستخدم في stage3 → stage4 عبر الملفات

---

## 🔄 سيناريوهات التدفق

### السيناريو 1: التدفق الكامل العادي
```
warehouse (WH-001) 
  ↓ material_id=5
stage1 (ST1-001) [material_id=5, parent=WH-001]
  ↓ stage1_id=10
stage2 (ST2-001) [stage1_id=10, parent=ST1-001]
  ↓ stage2_id=15
stage3 (CO3-001) [stage2_id=15, parent=ST2-001]
  ↓ box_coils
stage4 (BOX4-001) [parent=CO3-001]
```

### السيناريو 2: قفز stage1 (مباشرة للمعالجة)
```
warehouse (WH-001)
  ↓ material_id=5
stage2 (ST2-002) [material_id=5, stage1_id=NULL, parent=WH-001]
  ↓ stage2_id=16
stage3 (CO3-002) [stage2_id=16, parent=ST2-002]
  ↓ box_coils
stage4 (BOX4-002) [parent=CO3-002]
```

### السيناريو 3: قفز stage2 (من stage1 للملفات مباشرة)
```
warehouse (WH-001)
  ↓ material_id=5
stage1 (ST1-003) [material_id=5, parent=WH-001]
  ↓ stage1_id=11
stage3 (CO3-003) [stage1_id=11, stage2_id=NULL, parent=ST1-003]
  ↓ box_coils
stage4 (BOX4-003) [parent=CO3-003]
```

### السيناريو 4: تعبئة مباشرة (منتج جاهز)
```
warehouse (WH-001)
  ↓ material_id=5
stage4 (BOX4-004) [material_id=5, parent=WH-001]
```

---

## 📝 قواعد الاستخدام

### ✅ القواعد الصحيحة:

1. **parent_barcode** دائماً يشير للمصدر المباشر
2. **material_id** يُستخدم فقط عند القفز المباشر من warehouse
3. **stage1_id, stage2_id** nullable - تُملأ فقط عند المرور بتلك المرحلة
4. **product_tracking** يسجل كل حركة بـ `input_barcode` و `output_barcode`

### ❌ ممنوع:

1. ❌ ملء `material_id` عند المرور عبر مرحلة سابقة (يؤخذ من المرحلة السابقة)
2. ❌ ملء `stage1_id` في stage2 مع وجود `material_id` (تناقض)
3. ❌ ترك `parent_barcode` فارغاً (يجب دائماً تتبع المصدر)

---

## 🔍 الاستعلامات الشائعة

### تتبع رحلة باركود كاملة:
```sql
SELECT * FROM product_tracking 
WHERE barcode = 'ST1-2025-001' 
   OR input_barcode = 'ST1-2025-001' 
   OR output_barcode = 'ST1-2025-001'
ORDER BY created_at ASC;
```

### معرفة المصدر الأصلي لأي منتج:
```sql
-- من stage2
SELECT m.* FROM stage2_processed s2
LEFT JOIN stage1_stands s1 ON s2.stage1_id = s1.id
LEFT JOIN materials m ON COALESCE(s2.material_id, s1.material_id) = m.id
WHERE s2.barcode = 'ST2-2025-001';

-- من stage3
SELECT m.* FROM stage3_coils s3
LEFT JOIN stage2_processed s2 ON s3.stage2_id = s2.id
LEFT JOIN stage1_stands s1 ON COALESCE(s3.stage1_id, s2.stage1_id) = s1.id
LEFT JOIN materials m ON COALESCE(s3.material_id, s2.material_id, s1.material_id) = m.id
WHERE s3.barcode = 'CO3-2025-001';
```

### الوقت المستغرق في كل مرحلة:
```sql
SELECT 
    stage,
    barcode,
    MIN(created_at) as entered_at,
    MAX(created_at) as exited_at,
    TIMESTAMPDIFF(MINUTE, MIN(created_at), MAX(created_at)) as minutes_in_stage
FROM product_tracking
WHERE barcode = 'ST1-2025-001'
GROUP BY stage, barcode;
```

---

## 🛠️ الملاحظات الفنية

1. ✅ **التكامل مع product_tracking**: كل مرحلة تسجل في product_tracking مع:
   - `input_barcode`: الباركود المصدر
   - `output_barcode`: الباركود الجديد
   - `stage`: اسم المرحلة
   - `action`: created, processed, packed, etc.

2. ✅ **التكامل مع barcodes**: كل باركود يُسجل في جدول barcodes مع:
   - `type`: stage1, stage2, stage3, stage4
   - `reference_table`: stage1_stands, stage2_processed, etc.
   - `reference_id`: ID السجل في الجدول المرجعي

3. ✅ **التكامل مع waste_tracking**: كل هدر يُسجل بـ:
   - `stage_number`: 1, 2, 3, 4
   - `item_barcode`: الباركود المصدر
   - `waste_amount`: كمية الهدر

---

## 📅 تاريخ التحديثات

- **2025-11-16**: إصلاح الهيكلة لدعم القفز بين المراحل
  - جعل `stage1_id` في stage2 nullable
  - إضافة `material_id` لـ stage2, stage3, stage4
  - إضافة `stage1_id` لـ stage3
  - إضافة `parent_barcode` لـ stage4
  - إضافة `notes` لجميع المراحل
  - إضافة `wire_size` و `process_details` لـ stage2

---

## 🎯 التوصيات

1. ✅ استخدم `parent_barcode` دائماً للتتبع
2. ✅ تحقق من وجود `stage1_id` قبل استخدام `material_id`
3. ✅ سجل في `product_tracking` عند كل انتقال
4. ✅ استخدم transactions عند الحفظ
5. ✅ تحقق من توفر الكمية قبل الخصم
