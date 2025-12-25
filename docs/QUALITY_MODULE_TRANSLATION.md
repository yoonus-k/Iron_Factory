# Quality Module Translation - Documentation

## تاريخ التحديث: 24 ديسمبر 2025

## نظرة عامة
تم إنشاء نظام ترجمة كامل لوحدة الجودة والهدر (Quality & Waste Module) للأربع لغات المدعومة في النظام.

---

## الملفات المحدثة

### 1. ملفات اللغة (Language Files)

تم إضافة قسم `quality` كامل في الملفات التالية:

#### ✅ English (EN) - `resources/lang/en/app.php`
- **عدد المفاتيح المضافة**: ~270 مفتاح ترجمة
- **الأقسام الفرعية**: 
  - Common keys (مفاتيح عامة)
  - trace_item (عنصر التتبع)
  - downtime (الأعطال والتوقفات)
  - iron_journey (رحلة الحديد)
  - tracking_report (تقرير التتبع)
  - tracking_scan (مسح التتبع)
  - monitoring (مراقبة الجودة)
  - waste_limits (حدود الهدر)
  - waste_report (تقرير الهدر)

#### ✅ Arabic (AR) - `resources/lang/ar/app.php`
- نفس المفاتيح بالترجمة العربية
- تم الحفاظ على التنسيق والبنية المماثلة

#### ✅ Urdu (UR) - `resources/lang/ur/app.php`
- ترجمة كاملة للأردية مع دعم RTL
- جميع المفاتيح متوافقة مع النسخة العربية

#### ✅ Hindi (HI) - `resources/lang/hi/app.php`
- ترجمة كاملة للهندية
- دعم LTR للواجهة

---

## الملفات Blade المترجمة

### ✅ 1. trace-item.blade.php (مكتمل 100%)
**الموقع**: `Modules/Manufacturing/resources/views/quality/partials/trace-item.blade.php`

**التغييرات**:
- ✓ ترجمة "مصدر" / "منتج" → `__('app.quality.trace_item.source/product')`
- ✓ ترجمة "الوزن" → `__('app.quality.weight')`
- ✓ ترجمة "المستوى" → `__('app.quality.trace_item.level')`
- ✓ ترجمة "كجم" → `__('app.quality.kg')`

**عدد الاستبدالات**: 5 نصوص عربية

---

### ✅ 2. waste-report.blade.php (مكتمل 95%)
**الموقع**: `Modules/Manufacturing/resources/views/quality/waste-report.blade.php`

**التغييرات**:
#### Header & Title
- ✓ عنوان الصفحة
- ✓ مسار التنقل (Breadcrumb)

#### Statistics Cards (4 بطاقات)
- ✓ إجمالي الهدر اليوم
- ✓ نسبة الهدر الإجمالية
- ✓ حالات تجاوز الحد
- ✓ قيمة الهدر المقدرة

#### Filter Section
- ✓ عنوان الفلترة
- ✓ حقل "من تاريخ"
- ✓ حقل "إلى تاريخ"
- ✓ قائمة المراحل (4 مراحل)
- ✓ زر الفلترة
- ✓ زر التصدير

#### Table Headers
- ✓ جميع رؤوس الجدول (11 عمود)
- ✓ بيانات الجدول (3 صفوف نموذجية)

#### Charts
- ✓ عناوين الرسوم البيانية
- ✓ تسميات المراحل في JavaScript

**عدد الاستبدالات**: 25+ نص عربي

**المتبقي**: 
- بعض النصوص في الجزء Mobile View
- بعض رسائل JavaScript

---

## الملفات المتبقية (تحتاج إلى ترجمة)

### ⏳ 3. downtime-tracking.blade.php
**التقدير**: ~40-50 نص عربي
**الأولوية**: عالية
**الأقسام الرئيسية**:
- Header & breadcrumb
- Statistics cards (4 بطاقات)
- New entry form
- Active downtimes table
- History table
- Chart labels

### ⏳ 4. iron-journey.blade.php
**التقدير**: ~35-45 نص عربي
**الأولوية**: عالية
**الأقسام الرئيسية**:
- Header & search section
- Journey info bar
- Timeline
- Summary statistics
- Waste analysis
- Recommendations
- Modal tabs

### ⏳ 5. production-tracking-report.blade.php
**التقدير**: ~50-60 نص عربي
**الأولوية**: متوسطة
**الأقسام الرئيسية**:
- Header & barcode display
- Current status
- Summary cards (5 بطاقات)
- Reverse tracking section
- Forward tracking section
- Split history
- Charts section

### ⏳ 6. production-tracking-scan.blade.php
**التقدير**: ~20-25 نص عربي
**الأولوية**: منخفضة
**الأقسام الرئيسية**:
- Header & subtitle
- Scanner card
- Info cards (3 بطاقات)
- Recent scans section

### ⏳ 7. quality-monitoring.blade.php
**التقدير**: ~35-40 نص عربي
**الأولوية**: عالية
**الأقسام الرئيسية**:
- Header & breadcrumb
- Statistics cards (4 بطاقات)
- Inspection form
- History table
- Trend chart

### ⏳ 8. waste-limits.blade.php
**التقدير**: ~45-50 نص عربي
**الأولوية**: متوسطة
**الأقسام الرئيسية**:
- Header & info alert
- Configuration form (4 مراحل)
- Current limits table
- Form labels & hints

---

## كيفية استخدام الترجمة

### في ملفات Blade:
```blade
<!-- النص العادي -->
{{ __('app.quality.waste_report.title') }}

<!-- في العناوين -->
@section('title', __('app.quality.monitoring.title'))

<!-- في الـ Attributes -->
<input placeholder="{{ __('app.quality.search') }}">

<!-- في JavaScript -->
<script>
    const label = '{{ __("app.quality.waste_report.chart_label") }}';
</script>
```

### المسارات المتاحة:

**Common Keys**:
- `app.quality.dashboard`
- `app.quality.back`
- `app.quality.save`
- `app.quality.weight`
- `app.quality.date`
- `app.quality.barcode`
- `app.quality.stage`

**Downtime Module**:
- `app.quality.downtime.title`
- `app.quality.downtime.total_today`
- `app.quality.downtime.active_issues`

**Waste Report**:
- `app.quality.waste_report.title`
- `app.quality.waste_report.total_today`
- `app.quality.waste_report.filter_title`

**وهكذا...**

---

## الإحصائيات

### ملفات اللغة:
- ✅ **4 ملفات** تم تحديثها بالكامل
- ✅ **~270 مفتاح** ترجمة لكل لغة
- ✅ **~1080 مفتاح** إجمالي في الـ 4 لغات

### ملفات Blade:
- ✅ **2 ملفات** مكتملة (trace-item, waste-report)
- ⏳ **6 ملفات** متبقية
- 📊 **التقدم**: 25% من إجمالي الملفات

### التقدير الزمني للمتبقي:
- ⏱️ **downtime-tracking**: 1-1.5 ساعة
- ⏱️ **iron-journey**: 1-1.5 ساعة
- ⏱️ **production-tracking-report**: 1.5-2 ساعة
- ⏱️ **production-tracking-scan**: 30-45 دقيقة
- ⏱️ **quality-monitoring**: 1-1.5 ساعة
- ⏱️ **waste-limits**: 1-1.5 ساعة
- **الإجمالي**: ~7-9 ساعات عمل

---

## ملاحظات مهمة

### 1. تنسيق JavaScript
عند ترجمة نصوص داخل JavaScript، استخدم:
```javascript
'{{ __("app.quality.key") }}' // بعلامات تنصيص مفردة خارجية
```

### 2. المراحل الأربع
المراحل الإنتاجية لها مفاتيح محددة:
- `chart_stage1`: المرحلة 1: التقسيم
- `chart_stage2`: المرحلة 2: المعالجة
- `chart_stage3`: المرحلة 3: الكويلات
- `chart_stage4`: المرحلة 4: التغليف

### 3. الوحدات والعملات
- الوزن: `kg` → `__('app.quality.kg')`
- العملة: `sar` → `__('app.quality.sar')`

### 4. الحالات (Status)
- `normal`: عادي
- `warning`: تحذير
- `exceeded`: متجاوز
- `passed`: ناجح
- `failed`: مرفوض

---

## الخطوات التالية

1. ✅ ترجمة ملفات اللغة الأربعة - **مكتمل**
2. ✅ ترجمة trace-item.blade.php - **مكتمل**
3. ✅ ترجمة waste-report.blade.php - **مكتمل 95%**
4. ⏳ ترجمة downtime-tracking.blade.php
5. ⏳ ترجمة iron-journey.blade.php
6. ⏳ ترجمة production-tracking-report.blade.php
7. ⏳ ترجمة production-tracking-scan.blade.php
8. ⏳ ترجمة quality-monitoring.blade.php
9. ⏳ ترجمة waste-limits.blade.php
10. ⏳ اختبار جميع الملفات في الـ 4 لغات

---

## الاختبار

### تبديل اللغة:
```bash
# في ملف .env أو من الإعدادات
APP_LOCALE=ar  # العربية (افتراضي)
APP_LOCALE=en  # الإنجليزية
APP_LOCALE=ur  # الأردية
APP_LOCALE=hi  # الهندية
```

### التأكد من عمل الترجمة:
1. افتح أي صفحة من صفحات Quality Module
2. بدّل اللغة من الإعدادات
3. تأكد من تغيير جميع النصوص
4. تحقق من RTL/LTR للعربية والأردية

---

## المساهمون
- **تاريخ البداية**: 24 ديسمبر 2025
- **الحالة**: قيد التطوير (25% مكتمل)
- **آخر تحديث**: 24 ديسمبر 2025

---

## جهة الاتصال
للاستفسارات أو المساعدة في إكمال الترجمة، يرجى الرجوع إلى فريق التطوير.
