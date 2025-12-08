<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Helpers\TranslationHelper;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;

/**
 * نموذج كامل لاستخدام نظام الترجمات في Controller
 * Complete example of using Translation System in Controller
 * 
 * استخدم هذا كمرجع عند إنشاء form views و requests
 */
class MaterialTranslationControllerExample extends Controller
{
    /**
     * عرض قائمة الموارد
     */
    public function index()
    {
        // جلب كل المواد
        $materials = Material::paginate(15);

        // في Blade: استخدم getDisplayName() الخ
        return view('materials.index', compact('materials'));
    }

    /**
     * عرض نموذج الإنشاء
     */
    public function create()
    {
        // يحتاج الـ form إلى حقول:
        // - name_ar (اسم عربي)
        // - name_en (اسم إنجليزي)
        // - notes_ar (ملاحظات عربية)
        // - notes_en (ملاحظات إنجليزية)
        // - shelf_location_ar
        // - shelf_location_en
        
        return view('materials.create');
    }

    /**
     * حفظ المورد الجديد
     */
    public function store(StoreMaterialRequest $request)
    {
        // إنشاء المادة بالبيانات الأساسية
        $material = Material::create($request->validated());

        // ✅ الطريقة 1: استخدام الدوال السريعة
        $material->setMultilingualName(
            $request->get('name_ar'),
            $request->get('name_en')
        );

        $material->setMultilingualNotes(
            $request->get('notes_ar'),
            $request->get('notes_en')
        );

        $material->setMultilingualShelfLocation(
            $request->get('shelf_location_ar'),
            $request->get('shelf_location_en')
        );

        // ✅ الطريقة 2: استخدام Helper (بديلة)
        // TranslationHelper::batchSave($material, [
        //     'ar' => [
        //         'name' => $request->get('name_ar'),
        //         'notes' => $request->get('notes_ar'),
        //         'shelf_location' => $request->get('shelf_location_ar')
        //     ],
        //     'en' => [
        //         'name' => $request->get('name_en'),
        //         'notes' => $request->get('notes_en'),
        //         'shelf_location' => $request->get('shelf_location_en')
        //     ]
        // ]);

        return redirect()
            ->route('materials.show', $material)
            ->with('success', 'تم إنشاء المادة بنجاح');
    }

    /**
     * عرض التفاصيل
     */
    public function show(Material $material)
    {
        // في Blade يمكنك استخدام:
        // {{ $material->getDisplayName() }}
        // {{ $material->getDisplayNotes() }}
        // {{ $material->getDisplayShelfLocation() }}
        
        return view('materials.show', compact('material'));
    }

    /**
     * عرض نموذج التعديل
     */
    public function edit(Material $material)
    {
        // جلب الترجمات الحالية لإظهارها في الـ form
        $translations = [
            'ar' => [
                'name' => $material->getDisplayName('ar'),
                'notes' => $material->getDisplayNotes('ar'),
                'shelf_location' => $material->getDisplayShelfLocation('ar')
            ],
            'en' => [
                'name' => $material->getDisplayName('en'),
                'notes' => $material->getDisplayNotes('en'),
                'shelf_location' => $material->getDisplayShelfLocation('en')
            ]
        ];

        return view('materials.edit', compact('material', 'translations'));
    }

    /**
     * تحديث المورد
     */
    public function update(UpdateMaterialRequest $request, Material $material)
    {
        // تحديث البيانات الأساسية
        $material->update($request->validated());

        // ✅ تحديث الترجمات
        if ($request->has('name_ar') || $request->has('name_en')) {
            $material->setMultilingualName(
                $request->get('name_ar') ?? $material->getDisplayName('ar'),
                $request->get('name_en') ?? $material->getDisplayName('en')
            );
        }

        if ($request->has('notes_ar') || $request->has('notes_en')) {
            $material->setMultilingualNotes(
                $request->get('notes_ar') ?? $material->getDisplayNotes('ar'),
                $request->get('notes_en') ?? $material->getDisplayNotes('en')
            );
        }

        if ($request->has('shelf_location_ar') || $request->has('shelf_location_en')) {
            $material->setMultilingualShelfLocation(
                $request->get('shelf_location_ar') ?? $material->getDisplayShelfLocation('ar'),
                $request->get('shelf_location_en') ?? $material->getDisplayShelfLocation('en')
            );
        }

        return redirect()
            ->route('materials.show', $material)
            ->with('success', 'تم تحديث المادة بنجاح');
    }

    /**
     * حذف المورد
     */
    public function destroy(Material $material)
    {
        // سيتم حذف الترجمات تلقائياً بسبب الـ foreign key cascading
        // أو يمكنك حذفها يدوياً:
        // TranslationHelper::deleteAll(Material::class, $material->id);
        
        $material->delete();

        return redirect()
            ->route('materials.index')
            ->with('success', 'تم حذف المادة بنجاح');
    }

    /**
     * ============================================================
     * أمثلة إضافية
     * ============================================================
     */

    /**
     * مثال: الحصول على البيانات للـ API
     */
    public function apiShow(Material $material)
    {
        return response()->json([
            'id' => $material->id,
            'barcode' => $material->barcode,
            'translations' => [
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
            ]
        ]);
    }

    /**
     * مثال: البحث متعدد اللغات
     */
    public function search(string $query)
    {
        $locale = app()->getLocale();

        // البحث في الترجمات
        $materialIds = TranslationHelper::search(
            Material::class,
            'name',
            $query,
            $locale
        );

        // أو البحث في جميع اللغات
        $materialsAr = TranslationHelper::search(
            Material::class,
            'name',
            $query,
            'ar'
        );

        $materialsEn = TranslationHelper::search(
            Material::class,
            'name',
            $query,
            'en'
        );

        // دمج النتائج
        $allIds = $materialsAr->merge($materialsEn)->unique();

        $materials = Material::whereIn('id', $allIds)->paginate(15);

        return view('materials.search', compact('materials', 'query'));
    }

    /**
     * مثال: تصدير البيانات بجميع اللغات
     */
    public function export()
    {
        $materials = Material::all();

        $data = $materials->map(function ($material) {
            return [
                'id' => $material->id,
                'barcode' => $material->barcode,
                'name_ar' => $material->getDisplayName('ar'),
                'name_en' => $material->getDisplayName('en'),
                'notes_ar' => $material->getDisplayNotes('ar'),
                'notes_en' => $material->getDisplayNotes('en'),
                'location_ar' => $material->getDisplayShelfLocation('ar'),
                'location_en' => $material->getDisplayShelfLocation('en'),
            ];
        });

        // قم بالتصدير إلى CSV أو Excel
        return $data;
    }
}
