<?php

namespace App\Helpers;

/**
 * مثال سريع لاستخدام نظام الترجمات
 * Quick Example for Translation System
 * 
 * ضع هذا الكود في Tinker أو Controller للاختبار
 * Run this in Tinker or Controller for testing
 */

// ==================================
// 1. إنشاء مادة جديدة مع ترجمات
// ==================================

// $material = Material::create([
//     'barcode' => 'MAT-001',
//     'warehouse_id' => 1,
//     'material_type_id' => 1,
//     'unit_id' => 1,
//     'status' => 'available'
// ]);
// 
// // تعيين الأسماء والملاحظات بلغات متعددة
// $material->setMultilingualName('مادة خام أساسية', 'Basic Raw Material');
// $material->setMultilingualNotes('جودة عالية وآمنة للاستخدام', 'High quality and safe for use');
// $material->setMultilingualShelfLocation('الرف A-1', 'Shelf A-1');
// $material->save();


// ==================================
// 2. الحصول على الترجمات
// ==================================

// $material = Material::find(1);
// 
// // باللغة الحالية
// echo $material->getDisplayName();     // عربي أو إنجليزي حسب اللغة الحالية
// echo $material->getDisplayNotes();
// echo $material->getDisplayShelfLocation();
// 
// // بلغة محددة
// echo $material->getDisplayName('ar');  // مادة خام أساسية
// echo $material->getDisplayName('en');  // Basic Raw Material


// ==================================
// 3. التحديث
// ==================================

// $material = Material::find(1);
// 
// // تحديث الترجمة الواحدة
// $material->setTranslation('name', 'مادة جديدة', 'ar');
// $material->setTranslation('name', 'New Material', 'en');
// 
// // أو استخدام الدوال السريعة
// $material->setMultilingualName('الجديدة', 'The New One');


// ==================================
// 4. البحث عن الترجمات
// ==================================

// $material = Material::find(1);
// 
// // الحصول على جميع الترجمات
// $allTrans = $material->getAllTranslations('ar');
// // ['name' => 'مادة خام أساسية', 'notes' => '...', 'shelf_location' => '...']
// 
// // ترجمة محددة
// $trans = $material->getTranslation('name', 'ar');
// // 'مادة خام أساسية'
// 
// // التحقق من الوجود
// $has = TranslationHelper::exists('App\\Models\\Material', 1, 'name', 'ar');
// 
// // الحصول بجميع اللغات
// $allLangs = TranslationHelper::getInAllLocales('App\\Models\\Material', 1, 'name');
// // ['ar' => '...', 'en' => '...']


// ==================================
// 5. في Blade Template
// ==================================

// <div>
//     <h1>{{ $material->getDisplayName() }}</h1>
//     <p>{{ $material->getDisplayNotes() }}</p>
//     <small>{{ $material->getDisplayShelfLocation() }}</small>
// </div>
// 
// {{-- بلغة محددة --}}
// <h1 lang="ar">{{ $material->getDisplayName('ar') }}</h1>
// <h1 lang="en">{{ $material->getDisplayName('en') }}</h1>


// ==================================
// 6. في Service Layer
// ==================================

// public function createMaterial(array $data)
// {
//     $material = Material::create($data);
//     
//     TranslationHelper::batchSave($material, [
//         'ar' => [
//             'name' => $data['name_ar'],
//             'notes' => $data['notes_ar'],
//             'shelf_location' => $data['location_ar']
//         ],
//         'en' => [
//             'name' => $data['name_en'],
//             'notes' => $data['notes_en'],
//             'shelf_location' => $data['location_en']
//         ]
//     ]);
//     
//     return $material;
// }


// ==================================
// 7. الدوال المتاحة
// ==================================

/**
 * دوال Model (Material):
 * 
 * من Material Instance:
 * - $material->getTranslation($key, $locale)          // ترجمة معينة
 * - $material->setTranslation($key, $value, $locale)   // حفظ ترجمة
 * - $material->getAllTranslations($locale)             // جميع الترجمات
 * - $material->getDisplayName($locale)                 // الاسم
 * - $material->getDisplayNotes($locale)                // الملاحظات
 * - $material->getDisplayShelfLocation($locale)        // موقع الرف
 * - $material->setMultilingualName($ar, $en)          // تعيين الاسم
 * - $material->setMultilingualNotes($ar, $en)         // تعيين الملاحظات
 * - $material->setMultilingualShelfLocation($ar, $en) // تعيين الموقع
 * 
 * دوال TranslationHelper:
 * - TranslationHelper::get($type, $id, $key, $locale)
 * - TranslationHelper::save($type, $id, $key, $value, $locale)
 * - TranslationHelper::getAll($type, $id, $locale)
 * - TranslationHelper::fallback($model, $attribute, $locale)
 * - TranslationHelper::delete($type, $id, $key, $locale)
 * - TranslationHelper::deleteAll($type, $id)
 * - TranslationHelper::exists($type, $id, $key, $locale)
 * - TranslationHelper::getInAllLocales($type, $id, $key)
 * - TranslationHelper::batchSave($model, $translations)
 * - TranslationHelper::search($type, $key, $value, $locale)
 */

echo "✅ Translation System Ready!";
