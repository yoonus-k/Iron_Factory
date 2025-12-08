# تحديث نظام حساب الهدر للمراحل الإنتاجية

## التاريخ: 8 ديسمبر 2025

## الهدف
تغيير نظام حساب نسبة الهدر المسموح بها من قيمة عامة واحدة لجميع المراحل (`production_waste_percentage`) إلى قيم مخصصة لكل مرحلة على حدة.

## التغييرات

### 1. قاعدة البيانات (SystemSettingsSeeder.php)
تم تحديث نسب الهدر المسموح بها لكل مرحلة:
- **المرحلة الأولى (الاستاندات)**: 1%
- **المرحلة الثانية (المعالجة)**: 2%
- **المرحلة الثالثة (اللفائف)**: 3%
- **المرحلة الرابعة (التعبئة)**: 4%

### 2. SystemSettingsHelper.php
#### دوال جديدة:
```php
public static function getStageWastePercentage(int $stage): float
```
- تحصل على نسبة الهدر المسموح بها لمرحلة محددة
- تقرأ من إعدادات: `stage1_waste_percentage`, `stage2_waste_percentage`, إلخ
- القيمة الافتراضية: رقم المرحلة (1%, 2%, 3%, 4%)

#### دوال محدثة:
```php
public static function checkWastePercentage(int $stage, float $inputWeight, float $outputWeight): array
```
- تم تحديثها لاستخدام `getStageWastePercentage($stage)` بدلاً من `getProductionWastePercentage()`
- الآن تحسب نسبة الهدر بناءً على المرحلة المحددة

#### دوال مُهملة:
```php
public static function getProductionWastePercentage(): float
```
- تم وضع علامة `@deprecated` عليها
- لا تزال موجودة للتوافق الخلفي

### 3. WasteCheckService.php
تم تحديث استدعاء الدالة في السطر 49:
```php
// قبل التحديث:
'allowed_percentage' => SystemSettingsHelper::getProductionWastePercentage(),

// بعد التحديث:
'allowed_percentage' => SystemSettingsHelper::getStageWastePercentage($stageNumber),
```

## الاختبارات
تم إجراء اختبارات شاملة على جميع المراحل الأربعة، والنتائج كالتالي:

### المرحلة الأولى (1%):
- ✅ هدر 0.9% - مقبول
- ❌ هدر 1.5% - مرفوض

### المرحلة الثانية (2%):
- ✅ هدر 1.9% - مقبول
- ❌ هدر 2.5% - مرفوض

### المرحلة الثالثة (3%):
- ✅ هدر 2.9% - مقبول
- ❌ هدر 3.5% - مرفوض

### المرحلة الرابعة (4%):
- ✅ هدر 3.9% - مقبول
- ❌ هدر 4.5% - مرفوض

## التأثير على النظام
- ✅ جميع المراحل الآن تستخدم نسب الهدر الخاصة بها
- ✅ النظام يعمل بشكل صحيح مع قاعدة البيانات
- ✅ لا توجد أخطاء في الكود
- ✅ التوافق الخلفي محفوظ مع الدالة القديمة

## الملفات المعدلة
1. `database/seeders/SystemSettingsSeeder.php`
2. `app/Helpers/SystemSettingsHelper.php`
3. `app/Services/WasteCheckService.php`

## ملاحظات
- يمكن تغيير نسب الهدر من صفحة الإعدادات في لوحة التحكم
- النسب الحالية قابلة للتعديل حسب احتياجات المصنع
- النظام يدعم نسب مختلفة لكل مرحلة بشكل ديناميكي
