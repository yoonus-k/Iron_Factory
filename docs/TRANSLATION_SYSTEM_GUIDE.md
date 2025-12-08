# 🌍 شرح نظام الترجمات (Translation System)

## 📋 ملخص النظام

جدول `translations` منفصل يخزن كل الترجمات لأي Model في النظام بطريقة مرنة وسهلة.

---

## 📊 بنية جدول Translations

```sql
translations:
  ├── id (Primary Key)
  ├── translatable_type    → اسم الموديل (App\Models\Material)
  ├── translatable_id      → ID الموديل
  ├── locale              → اللغة (ar, en, ...)
  ├── key                 → اسم الحقل (name, notes, shelf_location)
  ├── value               → قيمة الترجمة
  ├── created_at
  └── updated_at
```

**مثال في الجدول:**
```
| id | translatable_type      | translatable_id | locale | key      | value                | 
|----|------------------------|-----------------|--------|----------|----------------------|
| 1  | App\Models\Material    | 5               | ar     | name     | مادة خام أولى        |
| 2  | App\Models\Material    | 5               | en     | name     | Raw Material First   |
| 3  | App\Models\Material    | 5               | ar     | notes    | ملاحظات إضافية       |
| 4  | App\Models\Material    | 5               | en     | notes    | Additional Notes     |
```

---

## 🔧 طريقة الاستخدام

### 1️⃣ **حفظ ترجمة جديدة**

```php
// من داخل Controller أو أي مكان

$material = Material::find(5);

// الطريقة الأولى: استخدام Model مباشرة
$material->setTranslation('name', 'مادة جديدة', 'ar');
$material->setTranslation('name', 'New Material', 'en');
$material->setTranslation('notes', 'ملاحظات مهمة', 'ar');

// أو استخدام Static Method من Translation
Translation::saveTranslation(
    'App\Models\Material',
    5,
    'name',
    'مادة جديدة',
    'ar'
);
```

### 2️⃣ **استدعاء ترجمة**

```php
$material = Material::find(5);

// الطريقة الأولى: استدعاء مباشر حسب الـ Locale الحالي
$name = $material->getTranslation('name');        // سيستخدم اللغة الحالية

// الطريقة الثانية: بتحديد اللغة
$nameAr = $material->getTranslation('name', 'ar');   // العربية
$nameEn = $material->getTranslation('name', 'en');   // الإنجليزية

// الطريقة الثالثة: استخدام Helper Methods (مختصرة)
$name = $material->getName();                     // يستخدم الـ Locale الحالي
$notes = $material->getNotes();                   // ملاحظات
$location = $material->getShelfLocation();        // موقع الرف
```

### 3️⃣ **استخدام في Blade (عرض)**

```blade
<!-- عرض الترجمات بناءً على اللغة الحالية -->
<h2>{{ $material->getName() }}</h2>
<p>{{ $material->getNotes() }}</p>

<!-- عرض الترجمة بلغة محددة -->
<h2>{{ $material->getName('ar') }}</h2>
<h2>{{ $material->getName('en') }}</h2>
```

### 4️⃣ **تحديث ترجمة موجودة**

```php
$material = Material::find(5);

// الطريقة البسيطة
$material->setTranslation('name', 'اسم محدث', 'ar');

// سيحدث تلقائياً إذا كانت موجودة أو ينشئ جديدة
```

---

## 🎯 أمثلة عملية

### مثال 1: إنشاء مادة مع ترجماتها

```php
// StoreController
$material = Material::create([
    'warehouse_id' => 1,
    'material_type_id' => 2,
    'barcode' => 'WH-001-2025',
    'status' => 'available',
    'created_by' => auth()->id(),
]);

// إضافة الترجمات
$material->setTranslation('name', 'حديد خام', 'ar');
$material->setTranslation('name', 'Raw Iron', 'en');
$material->setTranslation('notes', 'من المورد الرئيسي', 'ar');
$material->setTranslation('notes', 'From main supplier', 'en');
$material->setTranslation('shelf_location', 'الرف أ - 5', 'ar');
$material->setTranslation('shelf_location', 'Shelf A - 5', 'en');
```

### مثال 2: عرض قائمة المواد مع الترجمات

```php
// ListController
public function index()
{
    $materials = Material::all();
    
    // عند عرضها في الـ Blade
    return view('materials.index', ['materials' => $materials]);
}
```

**في الـ Blade:**
```blade
<table>
    @foreach($materials as $material)
        <tr>
            <td>{{ $material->barcode }}</td>
            <td>{{ $material->getName() }}</td>  <!-- سيجلب الترجمة من DB -->
            <td>{{ $material->getNotes() }}</td>
            <td>{{ $material->getShelfLocation() }}</td>
        </tr>
    @endforeach
</table>
```

### مثال 3: البحث والفلترة حسب الترجمات

```php
// إيجاد المواد بناءً على الترجمة
$translation = Translation::where('translatable_type', 'App\Models\Material')
    ->where('key', 'name')
    ->where('locale', 'ar')
    ->where('value', 'like', '%حديد%')
    ->get();

// أو أسهل
$materials = Material::whereHas('translations', function($q) {
    $q->where('key', 'name')
      ->where('locale', 'ar')
      ->where('value', 'like', '%حديد%');
})->get();
```

---

## 📝 جدول المقارنة

| الميزة | النظام القديم | النظام الجديد |
|--------|-------------|-------------|
| **الحقول** | `name_ar`, `name_en`, `notes`, `notes_en`... | حقل واحد فقط `id` + جدول منفصل |
| **المرونة** | محدود لعدد لغات معين | غير محدود - أي لغة ممكنة |
| **الصيانة** | إضافة حقل جديد = migration جديدة | كل شي في جدول واحد |
| **الأداء** | استعلام واحد | استعلام إضافي (يمكن تحسينه بـ Eager Loading) |

---

## ⚡ تحسين الأداء (Eager Loading)

```php
// بدل:
$materials = Material::all();
foreach($materials as $material) {
    echo $material->getName(); // كل iteration = query جديدة
}

// استخدم Eager Loading:
$materials = Material::with('translations')->get();
// الآن كل الترجمات محملة مسبقاً
```

---

## 🛠️ ملخص الـ Methods

| Method | الوصف |
|--------|------|
| `getTranslation($key, $locale)` | جلب ترجمة محددة |
| `setTranslation($key, $value, $locale)` | حفظ أو تحديث ترجمة |
| `getName($locale)` | استدعاء اسم المادة |
| `getNotes($locale)` | استدعاء الملاحظات |
| `getShelfLocation($locale)` | استدعاء موقع الرف |

---

## 📌 ملاحظات مهمة

1. **الـ Locale الافتراضي**: إذا ما حددت لغة، راح يستخدم `app()->getLocale()`
2. **المرونة**: يمكنك تضيف أي key جديد بدون تعديل الـ Schema
3. **الحقول المتروكة**: الحقول القديمة (`name_ar`, `name_en`) بقيت في الجدول للتوافقية

---

## 🚀 الخطوات التطبيقية

1. ✅ شغل الـ Migration:
```bash
php artisan migrate
```

2. ✅ استخدم Model بشكل طبيعي
3. ✅ أضف الترجمات عند الإنشاء
4. ✅ اعرض الترجمات في الـ Views
