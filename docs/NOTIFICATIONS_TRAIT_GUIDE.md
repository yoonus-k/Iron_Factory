# دليل استخدام Trait StoresNotifications

## الهدف
توحيد طريقة تخزين الإشعارات في قاعدة البيانات عبر جميع الـ Controllers بكفاءة وسهولة.

## كيفية الاستخدام

### 1. استيراد الـ Trait في أي Controller

```php
use App\Traits\StoresNotifications;

class YourController extends Controller
{
    use StoresNotifications;
    
    // بقية الكود...
}
```

### 2. الدوال المتاحة

#### أ. `storeNotification()` - تخزين إشعار مخصص
```php
$this->storeNotification(
    $type,        // نوع الإشعار (مثل: 'material_created')
    $title,       // عنوان الإشعار
    $message,     // نص الإشعار
    $color,       // اللون (success, danger, warning, info) - اختياري
    $icon,        // أيقونة Font Awesome - اختياري
    $actionUrl    // رابط الإجراء - اختياري
);
```

**مثال:**
```php
$this->storeNotification(
    'material_created',
    'إضافة مادة جديدة',
    'تم إضافة مادة جديدة برقم: ' . $material->id,
    'success',
    'fas fa-plus-circle',
    route('materials.show', $material->id)
);
```

#### ب. `notifyCreate()` - إشعار الإنشاء
```php
$this->notifyCreate(
    'المادة',              // اسم الكيان
    $material->number,    // رقم الكيان (اختياري)
    route('materials.show', $material->id)  // الرابط (اختياري)
);
```

#### ج. `notifyUpdate()` - إشعار التحديث
```php
$this->notifyUpdate(
    'المادة',
    $material->number,
    route('materials.show', $material->id)
);
```

#### د. `notifyDelete()` - إشعار الحذف
```php
$this->notifyDelete(
    'المادة',
    $material->number,
    route('materials.index')
);
```

#### هـ. `notifyStatusChange()` - إشعار تغيير الحالة
```php
$this->notifyStatusChange(
    'أذن التسليم',
    'قيد الانتظار',  // الحالة القديمة
    'موافق عليها',  // الحالة الجديدة
    $deliveryNote->number,
    route('delivery-notes.show', $deliveryNote->id)
);
```

#### و. `notifyCustomOperation()` - إشعار عملية مخصصة
```php
$this->notifyCustomOperation(
    'inventory_check',
    'فحص المخزون',
    'تم فحص المخزون بنجاح',
    'info',
    'fas fa-check',
    route('inventory.index')
);
```

## الألوان المتاحة
- `success` - للعمليات الناجحة (أخضر)
- `danger` - للعمليات الخطيرة/الحذف (أحمر)
- `warning` - للتحذيرات (برتقالي)
- `info` - للمعلومات (أزرق)

## أيقونات Font Awesome المشهورة
- `fas fa-plus-circle` - إضافة
- `fas fa-edit` - تحرير
- `fas fa-trash` - حذف
- `fas fa-check-circle` - موافقة
- `fas fa-exclamation-triangle` - تحذير
- `fas fa-sync-alt` - تحديث
- `fas fa-arrow-down` - ورود
- `fas fa-arrow-up` - خروج
- `fas fa-file-invoice` - فاتورة

## مثال كامل في Controller

```php
<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Traits\StoresNotifications;

class MaterialController extends Controller
{
    use StoresNotifications;

    public function store(Request $request)
    {
        try {
            $material = Material::create($request->validated());
            
            // تخزين الإشعار
            $this->notifyCreate(
                'المادة',
                $material->number,
                route('materials.show', $material->id)
            );

            return redirect()->back()->with('success', 'تم إضافة المادة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $material = Material::findOrFail($id);
            $material->update($request->validated());
            
            // تخزين الإشعار
            $this->notifyUpdate(
                'المادة',
                $material->number,
                route('materials.show', $material->id)
            );

            return redirect()->back()->with('success', 'تم تحديث المادة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ');
        }
    }

    public function destroy($id)
    {
        try {
            $material = Material::findOrFail($id);
            $material->delete();
            
            // تخزين الإشعار
            $this->notifyDelete(
                'المادة',
                $material->number,
                route('materials.index')
            );

            return redirect()->back()->with('success', 'تم حذف المادة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ');
        }
    }
}
```

## ملاحظات مهمة

1. **الإشعارات تُخزن تلقائياً** بدون الحاجة لـ try-catch منفصل
2. **جميع الإشعارات موجهة للجميع** (user_id = null)
3. **معرف المستخدم الحالي** يُحفظ تلقائياً في `created_by`
4. **جميع الإشعارات تُعلّم كغير مقروءة** بشكل افتراضي
5. **في حالة الخطأ** يتم تسجيل الخطأ في الـ log وليس إيقاف العملية

## الفوائد

✅ كود موحد وسهل الصيانة
✅ عدم تكرار الكود في كل Controller
✅ إشعارات متسقة في جميع النظام
✅ سهل الإضافة إلى أي Controller جديد
✅ معالجة الأخطاء تلقائية وآمنة
