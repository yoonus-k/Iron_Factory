# 🌍 نظام إدارة الترجمات - Language Management System

## نظرة عامة
نظام متطور لإدارة الترجمات يدعم عدد غير محدود من اللغات وسهل التعامل معه.

---

## 1️⃣ الاستخدام الأساسي من Model

### الحصول على ترجمة
```php
$material = Material::find(1);

// باللغة الحالية
$name = $material->getTranslation('name');
$notes = $material->getTranslation('notes');

// بلغة محددة
$nameAr = $material->getTranslation('name', 'ar');
$nameEn = $material->getTranslation('name', 'en');
```

### حفظ ترجمة
```php
$material = Material::find(1);

// باللغة الحالية
$material->setTranslation('name', 'اسم المادة');
$material->setTranslation('notes', 'ملاحظات مهمة');

// بلغة محددة
$material->setTranslation('name', 'Material Name', 'en');
$material->setTranslation('notes', 'Important notes', 'en');
```

### الحصول على جميع الترجمات
```php
$material = Material::find(1);

// جميع الترجمات باللغة الحالية
$allTranslations = $material->getAllTranslations();
// Array: ['name' => 'اسم المادة', 'notes' => 'ملاحظات', ...]

// باللغة المحددة
$allTranslations = $material->getAllTranslations('en');
```

---

## 2️⃣ Helper Methods للعرض

### الحصول على الاسم بلغة معينة
```php
$material = Material::find(1);

// الاسم باللغة الحالية
$displayName = $material->getDisplayName();

// بلغة محددة
$displayNameAr = $material->getDisplayName('ar');
$displayNameEn = $material->getDisplayName('en');
```

### الملاحظات والحقول الأخرى
```php
$material = Material::find(1);

// الملاحظات
$notes = $material->getDisplayNotes('ar');
$notesEn = $material->getDisplayNotes('en');

// موقع الرف
$location = $material->getDisplayShelfLocation('ar');
$locationEn = $material->getDisplayShelfLocation('en');
```

---

## 3️⃣ تعيين البيانات متعددة اللغات

### تعيين الاسم بلغات متعددة
```php
$material = Material::find(1);

$material->setMultilingualName(
    'مادة خام',  // Arabic
    'Raw Material' // English
);

$material->save();
```

### تعيين الملاحظات
```php
$material->setMultilingualNotes(
    'ملاحظات مهمة جداً',
    'Very important notes'
);
```

### تعيين موقع الرف
```php
$material->setMultilingualShelfLocation(
    'الرف A1',
    'Shelf A1'
);
```

---

## 4️⃣ استخدام TranslationHelper

### دوال عملية مباشرة
```php
use App\Helpers\TranslationHelper;

// الحصول على ترجمة
$translation = TranslationHelper::get(
    'App\\Models\\Material',
    1,
    'name',
    'ar'
);

// حفظ ترجمة
TranslationHelper::save(
    'App\\Models\\Material',
    1,
    'name',
    'اسم جديد',
    'ar'
);

// الحصول على جميع الترجمات
$allTrans = TranslationHelper::getAll(
    'App\\Models\\Material',
    1,
    'ar'
);

// التحقق من الوجود
$exists = TranslationHelper::exists(
    'App\\Models\\Material',
    1,
    'name',
    'ar'
);
```

### حفظ متعدد الترجمات
```php
$material = Material::find(1);

TranslationHelper::batchSave($material, [
    'ar' => [
        'name' => 'اسم المادة',
        'notes' => 'ملاحظات',
        'shelf_location' => 'الرف A1'
    ],
    'en' => [
        'name' => 'Material Name',
        'notes' => 'Notes',
        'shelf_location' => 'Shelf A1'
    ]
]);
```

### البحث
```php
// البحث عن مواد باسم معين
$materialIds = TranslationHelper::search(
    'App\\Models\\Material',
    'name',
    'خام',
    'ar'
);

// ثم جلب الموديلات
$materials = Material::whereIn('id', $materialIds)->get();
```

---

## 5️⃣ الحصول على الحقل بجميع اللغات

```php
$material = Material::find(1);

// جميع الترجمات للاسم بجميع اللغات
$allNames = TranslationHelper::getInAllLocales(
    'App\\Models\\Material',
    1,
    'name'
);

// Result:
// [
//     'ar' => 'اسم المادة',
//     'en' => 'Material Name',
//     'fr' => 'Nom du matériau'
// ]
```

---

## 6️⃣ الاستخدام في Blade Templates

### عرض الترجمات
```blade
@php
    $material = Material::find(1)
@endphp

{{-- الاسم باللغة الحالية --}}
<h1>{{ $material->getDisplayName() }}</h1>

{{-- اللغة المحددة --}}
<h1>{{ $material->getDisplayName('ar') }}</h1>
<h1>{{ $material->getDisplayName('en') }}</h1>

{{-- الملاحظات --}}
<p>{{ $material->getDisplayNotes() }}</p>

{{-- موقع الرف --}}
<small>{{ $material->getDisplayShelfLocation() }}</small>
```

### استخدام Helper
```blade
@php
    use App\Helpers\TranslationHelper;
@endphp

{{-- الحصول على الترجمة --}}
<h1>{{ TranslationHelper::display($material, 'name') }}</h1>

{{-- بلغة محددة --}}
<h1>{{ TranslationHelper::display($material, 'name', 'ar') }}</h1>
```

---

## 7️⃣ إضافة Trait إلى أي موديل

إذا أردت استخدام نظام الترجمات على موديل آخر:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasMultilingualContent;

class MyModel extends Model
{
    use HasMultilingualContent;

    // ... باقي الكود
}
```

ثم استخدمه بنفس الطريقة!

---

## 8️⃣ في Controllers و Services

### في Form Submission
```php
class MaterialController extends Controller
{
    public function store(StoreMaterialRequest $request)
    {
        $material = Material::create($request->validated());

        // حفظ الترجمات
        $material->setMultilingualName(
            $request->get('name_ar'),
            $request->get('name_en')
        );

        $material->setMultilingualNotes(
            $request->get('notes_ar'),
            $request->get('notes_en')
        );

        return redirect()->route('materials.show', $material);
    }

    public function update(UpdateMaterialRequest $request, Material $material)
    {
        $material->update($request->validated());

        // تحديث الترجمات
        if ($request->has('name_ar')) {
            $material->setTranslation('name', $request->get('name_ar'), 'ar');
        }
        if ($request->has('name_en')) {
            $material->setTranslation('name', $request->get('name_en'), 'en');
        }

        return redirect()->route('materials.show', $material);
    }
}
```

---

## 9️⃣ في Service Layer

```php
class MaterialService
{
    public function createWithTranslations(array $data)
    {
        $material = Material::create($data);

        // تعيين الترجمات
        TranslationHelper::batchSave($material, [
            'ar' => [
                'name' => $data['name_ar'],
                'notes' => $data['notes_ar'],
                'shelf_location' => $data['location_ar']
            ],
            'en' => [
                'name' => $data['name_en'],
                'notes' => $data['notes_en'],
                'shelf_location' => $data['location_en']
            ]
        ]);

        return $material;
    }

    public function getTranslationsForApi(Material $material)
    {
        return [
            'ar' => [
                'name' => $material->getDisplayName('ar'),
                'notes' => $material->getDisplayNotes('ar'),
                'location' => $material->getDisplayShelfLocation('ar')
            ],
            'en' => [
                'name' => $material->getDisplayName('en'),
                'notes' => $material->getDisplayNotes('en'),
                'location' => $material->getDisplayShelfLocation('en')
            ]
        ];
    }
}
```

---

## 🔟 نقاط مهمة

### ✅ الحقول المدعومة للترجمات:
- `name` - اسم المادة
- `notes` - الملاحظات
- `shelf_location` - موقع الرف

### ✅ اللغات المدعومة:
- `ar` - العربية
- `en` - الإنجليزية

### ✅ الخصائص:
- ✔️ Fallback إلى الحقول المباشرة إذا لم توجد ترجمة
- ✔️ دعم اللغة الحالية تلقائياً
- ✔️ سهولة البحث والفلترة
- ✔️ Batch updates
- ✔️ Unique constraints

---

## 1️⃣1️⃣ مثال كامل

```php
// إنشاء مادة مع ترجمات
$material = Material::create([
    'barcode' => '12345',
    'warehouse_id' => 1,
    'material_type_id' => 1,
    'unit_id' => 1,
    'status' => 'available'
]);

$material->setMultilingualName('مادة خام', 'Raw Material');
$material->setMultilingualNotes('مادة جودة عالية', 'High quality material');
$material->save();

// الوصول إليها
echo $material->getDisplayName('ar'); // مادة خام
echo $material->getDisplayName('en'); // Raw Material

// في Blade
{{ $material->getDisplayName() }} {{-- بالغة الحالية --}}
```

---

## الخلاصة ✨
نظام ترجمات مرن وقوي يدعم أي عدد من اللغات ويوفر طرقاً متعددة للتعامل مع المحتوى متعدد اللغات بسهولة وفعالية!
