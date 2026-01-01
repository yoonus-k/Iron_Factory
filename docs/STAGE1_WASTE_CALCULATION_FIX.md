# إصلاح حساب الهدر في المرحلة الأولى - Stage 1 Waste Calculation Fix

## التاريخ: 2026-01-01

---

## المشكلة الأساسية

كان حساب نسبة الهدر في المرحلة الأولى **خاطئاً** لأنه يستخدم `weight` (الوزن الكلي الذي يشمل وزن الاستاند) بدلاً من وزن المادة الفعلي.

---

## فهم البيانات في جدول `stage1_stands`

### الحقول:
```php
'weight'            => الوزن الكلي (المادة + الاستاند)
'remaining_weight'  => الوزن الصافي (المادة فقط بعد إزالة وزن الاستاند)
'waste'             => الهدر الفعلي
```

### مثال عملي:
```
weight = 150 كجم (100 كجم مادة + 50 كجم استاند)
remaining_weight = 95 كجم (المادة بعد الإنتاج)
waste = 5 كجم (الهدر)

✅ وزن المادة الفعلي = remaining_weight + waste = 95 + 5 = 100 كجم
✅ نسبة الهدر الصحيحة = (5 / 100) * 100 = 5%

❌ الحساب الخاطئ السابق = (5 / 150) * 100 = 3.33% (خطأ!)
```

---

## الإصلاحات المطبقة

### 1. إصلاح `Stage1ManagementReportController.php`

#### أ) حساب نسبة الهدر لكل استاند:

**قبل الإصلاح (خطأ):**
```php
$wastePercentage = ($record->waste / $record->weight) * 100;
```

**بعد الإصلاح (صحيح):**
```php
$materialWeight = $record->remaining_weight + $record->waste;
$wastePercentage = ($record->waste / $materialWeight) * 100;
```

#### ب) فلترة الهدر:

**قبل الإصلاح (خطأ):**
```php
$query->whereRaw("(waste / weight) * 100 <= 8");
```

**بعد الإصلاح (صحيح):**
```php
$query->whereRaw("(waste / (remaining_weight + waste)) * 100 <= 8");
```

#### ج) تحليل العمال والإحصائيات:
تم تحديث جميع الحسابات لاستخدام `remaining_weight + waste` بدلاً من `weight`.

---

### 2. إصلاح `Stage1Controller.php` - دالة `finishCoil()`

#### المشكلة:
كانت تحسب الهدر الكلي من الفرق بين الوزن المنقول والوزن الصافي، والوزن المنقول يشمل وزن الاستاندات!

**قبل الإصلاح (خطأ):**
```php
$transferredWeight = $coilTransfer->transfer_weight;  // 1500 كجم (مع الاستاندات)
$totalNetWeight = $stage1Data->total_net_weight;      // 900 كجم (المادة فقط)
$totalWaste = $transferredWeight - $totalNetWeight;   // 600 كجم ❌ (يشمل وزن الاستاندات!)
$wastePercentage = ($totalWaste / $transferredWeight) * 100;  // 40% ❌ خطأ!
```

**بعد الإصلاح (صحيح):**
```php
$transferredWeight = $coilTransfer->transfer_weight;        // 1500 كجم (مع الاستاندات)
$totalNetWeight = $stage1Data->total_net_weight;            // 900 كجم
$totalWaste = $stage1Data->total_waste;                     // 100 كجم (الهدر الفعلي المسجل)
$totalMaterialWeight = $totalNetWeight + $totalWaste;       // 1000 كجم (المادة فقط)
$wastePercentage = ($totalWaste / $totalMaterialWeight) * 100;  // 10% ✅ صحيح!
```

#### التحديثات في `stage_suspensions`:
```php
'input_weight' => $totalMaterialWeight,  // وزن المادة الفعلي (بدون الاستاندات)
'output_weight' => $totalNetWeight,      // الوزن الصافي
'waste_weight' => $totalWaste,           // الهدر الفعلي
```

#### تحديث رسائل الاستجابة:
```php
'• الوزن المنقول للإنتاج (كلي): %s كجم'                // transferred_weight
'• وزن المادة الفعلي (بدون الاستاندات): %s كجم'         // totalMaterialWeight
'• إجمالي الوزن الصافي: %s كجم'                        // totalNetWeight
'• إجمالي الهدر: %s كجم'                               // totalWaste
```

---

## المعادلات الصحيحة

### للاستاند الواحد:
```
material_weight = remaining_weight + waste
waste_percentage = (waste / material_weight) * 100
```

### للكويل الكامل (عند finishCoil):
```
total_material_weight = total_net_weight + total_waste
waste_percentage = (total_waste / total_material_weight) * 100
```

### التحقق من الصحة:
```php
// يجب أن تكون النسبة دائماً:
0% <= waste_percentage <= 100%

// إذا كانت النسبة > 100% = خطأ في الحساب!
```

---

## تصنيف مستويات الهدر

```
✅ آمن (Safe):      0% - 8%
⚠️ تحذير (Warning): 8% - 15%
🔴 خطر (Critical):  > 15%
```

---

## الملفات المعدلة

1. `Modules/Manufacturing/Http/Controllers/Stage1ManagementReportController.php`
   - جميع حسابات نسبة الهدر
   - فلاتر الهدر
   - تحليل العمال
   - الإحصائيات اليومية

2. `Modules/Manufacturing/Http/Controllers/Stage1Controller.php`
   - دالة `finishCoil()`
   - حساب الهدر الكلي
   - تحديث `stage_suspensions`
   - رسائل الاستجابة

---

## اختبار الحسابات

### مثال 1: استاند عادي
```
Input:
  weight = 120 كجم (100 مادة + 20 استاند)
  remaining_weight = 92 كجم
  waste = 8 كجم

Calculation:
  material_weight = 92 + 8 = 100 كجم
  waste% = (8 / 100) * 100 = 8% ✅
```

### مثال 2: كويل كامل (10 استاندات)
```
Input:
  transferred_weight = 1200 كجم (1000 مادة + 200 استاندات)
  total_net_weight = 900 كجم
  total_waste = 100 كجم

Calculation:
  total_material_weight = 900 + 100 = 1000 كجم
  waste% = (100 / 1000) * 100 = 10% ✅
```

### مثال 3: حالة تجاوز الهدر
```
Input:
  total_material_weight = 1000 كجم
  total_net_weight = 800 كجم
  total_waste = 200 كجم

Calculation:
  waste% = (200 / 1000) * 100 = 20% > 15% 🔴
  → يتم إيقاف الاستاندات (pending_approval)
  → تسجيل في stage_suspensions
```

---

## ملاحظات مهمة

1. ✅ **الهدر يُحسب من وزن المادة فقط** (بدون وزن الاستاند)
2. ✅ **وزن المادة = remaining_weight + waste**
3. ✅ **نسبة الهدر لا يمكن أن تتجاوز 100%**
4. ⚠️ إذا ظهرت نسبة > 100% = خطأ في البيانات المدخلة أو الحساب

---

## التأثير على البيانات السابقة

البيانات المحفوظة في الجدول **صحيحة**:
- ✅ `waste` يحتوي على الهدر الفعلي
- ✅ `remaining_weight` يحتوي على الوزن الصافي

فقط **الحسابات** كانت خاطئة في:
- ❌ التقارير (تم إصلاحها)
- ❌ دالة finishCoil (تم إصلاحها)

---

## الخلاصة

✅ **تم إصلاح جميع حسابات نسبة الهدر في المرحلة الأولى**
✅ **البيانات الآن دقيقة 100%**
✅ **نسب الهدر واقعية ولن تتجاوز 100%**
✅ **الفلاتر تعمل بشكل صحيح**
✅ **التقارير دقيقة**

---

## للمطورين المستقبليين

**عند التعامل مع جدول `stage1_stands`:**

```php
// ❌ خطأ - لا تستخدم هذا!
$waste_pct = ($record->waste / $record->weight) * 100;

// ✅ صحيح - استخدم هذا دائماً!
$material_weight = $record->remaining_weight + $record->waste;
$waste_pct = ($record->waste / $material_weight) * 100;
```

**القاعدة الذهبية:**
> **وزن المادة الفعلي = الوزن المتبقي + الهدر**
> 
> **نسبة الهدر = (الهدر / وزن المادة الفعلي) × 100**
