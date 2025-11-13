# نظام إدارة المستودعات - Warehouse Management System

## نظرة عامة
هذا النظام يوفر إدارة كاملة للمستودعات (Warehouses) في نظام إدارة التصنيع.

## المكونات الرئيسية

### 1. Model (Eloquent Model)
**المسار:** `app/Models/Warehouse.php`

#### الحقول الرئيسية:
- `warehouse_code` - رمز المستودع (فريد ومطلوب)
- `warehouse_name` - اسم المستودع بالعربية
- `warehouse_name_en` - اسم المستودع بالإنجليزية
- `location` - الموقع
- `description` - الوصف
- `capacity` - السعة التخزينية
- `capacity_unit` - وحدة السعة (متر مكعب، متر مربع، إلخ)
- `manager_name` - اسم مسؤول المستودع
- `contact_number` - رقم الاتصال
- `is_active` - حالة النشاط

### 2. Repository Pattern
**المسار:** `Modules/Manufacturing/Repositories/WarehouseRepository.php`

يوفر الـ Repository جميع العمليات الأساسية:

```php
// الحصول على جميع المستودعات بترقيم الصفحات
$warehouses = $repository->getAllPaginated(15);

// البحث والتصفية
$results = $repository->search([
    'search' => 'البحث',
    'status' => 'active'
]);

// الحصول على مستودع بـ ID
$warehouse = $repository->getById($id);

// إنشاء مستودع جديد
$warehouse = $repository->create([
    'name' => 'المستودع الرئيسي',
    'code' => 'WH-001',
    'location' => 'القاهرة',
    'phone' => '0123456789',
    'status' => 'active'
]);

// تحديث مستودع
$warehouse = $repository->update($id, $data);

// حذف مستودع
$repository->delete($id);

// التحقق من وجود الرمز
$exists = $repository->codeExists('WH-001');

// الحصول على المستودعات النشطة فقط
$active = $repository->getActive();

// إحصائيات
$count = $repository->count();
$activeCount = $repository->countByStatus(true);
```

### 3. Service Layer
**المسار:** `Modules/Manufacturing/Services/WarehouseService.php`

يوفر طبقة من المنطق الأساسي للتحقق والمعالجة:

```php
use Modules\Manufacturing\Services\WarehouseService;

$service = app(WarehouseService::class);

// إنشاء مستودع (مع التحقق)
$warehouse = $service->createWarehouse($data);

// تحديث مستودع (مع التحقق)
$warehouse = $service->updateWarehouse($id, $data);

// حذف مستودع
$service->deleteWarehouse($id);

// البحث
$results = $service->searchWarehouses([
    'search' => 'البحث',
    'status' => 'active'
]);

// الإحصائيات
$stats = $service->getStatistics();
// Returns: ['total' => 10, 'active' => 8, 'inactive' => 2]
```

### 4. Controller
**المسار:** `Modules/Manufacturing/Http/Controllers/WarehouseController.php`

#### الدوال الرئيسية:

```php
// عرض جميع المستودعات مع البحث والتصفية
GET /warehouses
GET /warehouses?search=البحث&status=active

// عرض صفحة إنشاء مستودع
GET /warehouses/create

// حفظ مستودع جديد
POST /warehouses

// عرض تفاصيل مستودع
GET /warehouses/{id}

// عرض صفحة تعديل مستودع
GET /warehouses/{id}/edit

// تحديث مستودع
PUT /warehouses/{id}

// حذف مستودع
DELETE /warehouses/{id}

// الحصول على إحصائيات JSON
GET /warehouses/statistics

// الحصول على المستودعات النشطة JSON
GET /warehouses/active
```

### 5. Form Requests (Validation)
**المسار:** `Modules/Manufacturing/Http/Requests/`

#### StoreWarehouseRequest.php
للتحقق من بيانات إضافة مستودع جديد

#### UpdateWarehouseRequest.php
للتحقق من بيانات تعديل مستودع

## قواعد التحقق (Validation Rules)

```
name (اسم المستودع):
  - مطلوب
  - نص بحد أقصى 255 حرف
  - فريد (لا يمكن تكراره)

code (رمز المستودع):
  - مطلوب
  - نص بحد أقصى 50 حرف
  - فريد (لا يمكن تكراره)

location (الموقع):
  - اختياري
  - نص بحد أقصى 255 حرف

manager_id (مسؤول المستودع):
  - اختياري
  - يجب أن يكون موجود في جدول المستخدمين

description (الوصف):
  - اختياري
  - نص

capacity (السعة):
  - اختياري
  - رقم موجب

status (الحالة):
  - مطلوب
  - يجب أن يكون: active أو inactive

phone (الهاتف):
  - اختياري
  - نص بحد أقصى 20 حرف

email (البريد الإلكتروني):
  - اختياري
  - بريد إلكتروني صحيح
```

## Blade Templates (المواديل)

### 1. index.blade.php
عرض قائمة جميع المستودعات مع:
- جدول سطح المكتب
- بطاقات الهاتف
- البحث والتصفية
- الترقيم (Pagination)

### 2. create.blade.php
نموذج إضافة مستودع جديد

### 3. edit.blade.php
نموذج تعديل بيانات مستودع موجود

### 4. show.blade.php
عرض تفاصيل مستودع معين

## Routes (المسارات)

```php
// في Modules/Manufacturing/routes/web.php
Route::resource('warehouses', WarehouseController::class)->names('manufacturing.warehouses');
Route::get('warehouses/statistics', [WarehouseController::class, 'statistics'])->name('manufacturing.warehouses.statistics');
Route::get('warehouses/active', [WarehouseController::class, 'getActive'])->name('manufacturing.warehouses.active');
```

## أسماء الـ Routes

| الفعل | المسار | اسم الـ Route |
|------|-------|----------------|
| GET | /warehouses | manufacturing.warehouses.index |
| GET | /warehouses/create | manufacturing.warehouses.create |
| POST | /warehouses | manufacturing.warehouses.store |
| GET | /warehouses/{id} | manufacturing.warehouses.show |
| GET | /warehouses/{id}/edit | manufacturing.warehouses.edit |
| PUT | /warehouses/{id} | manufacturing.warehouses.update |
| DELETE | /warehouses/{id} | manufacturing.warehouses.destroy |
| GET | /warehouses/statistics | manufacturing.warehouses.statistics |
| GET | /warehouses/active | manufacturing.warehouses.active |

## أمثلة الاستخدام

### في Blade Template (لإنشاء رابط):

```blade
<!-- عرض جميع المستودعات -->
<a href="{{ route('manufacturing.warehouses.index') }}">المستودعات</a>

<!-- إضافة مستودع جديد -->
<a href="{{ route('manufacturing.warehouses.create') }}">إضافة مستودع</a>

<!-- عرض مستودع محدد -->
<a href="{{ route('manufacturing.warehouses.show', $warehouse->id) }}">عرض</a>

<!-- تعديل مستودع -->
<a href="{{ route('manufacturing.warehouses.edit', $warehouse->id) }}">تعديل</a>

<!-- حذف مستودع -->
<form action="{{ route('manufacturing.warehouses.destroy', $warehouse->id) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit">حذف</button>
</form>
```

### في JavaScript (لجلب البيانات):

```javascript
// جلب الإحصائيات
fetch('{{ route("manufacturing.warehouses.statistics") }}')
    .then(response => response.json())
    .then(data => {
        console.log(data);
        // { total: 10, active: 8, inactive: 2 }
    });

// جلب المستودعات النشطة
fetch('{{ route("manufacturing.warehouses.active") }}')
    .then(response => response.json())
    .then(warehouses => {
        console.log(warehouses);
    });
```

## رسائل الأخطاء والنجاح

```
✅ النجاح:
- "تم إضافة المستودع بنجاح"
- "تم تحديث بيانات المستودع بنجاح"
- "تم حذف المستودع بنجاح"

❌ الأخطاء:
- "اسم المستودع مطلوب"
- "رمز المستودع موجود بالفعل"
- "المستودع غير موجود"
- "المسؤول المختار غير موجود"
```

## ملاحظات مهمة

1. **الصلاحيات:** قد تحتاج لإضافة middleware للتحقق من الصلاحيات
2. **الترجمة:** يتم استخدام اللغة العربية في الرسائل
3. **الترقيم:** يتم عرض 10 مستودعات لكل صفحة
4. **البحث:** يتم البحث في الاسم والرمز والموقع
5. **التفعيل:** يمكن تفعيل/تعطيل المستودع من خلال حقل الحالة

## الخطوات التالية

1. ✅ إضافة الـ Backend Logic (تم)
2. ⏳ تحديث الـ Views مع البيانات الفعلية
3. ⏳ إضافة الـ Authorization والـ Policies
4. ⏳ إضافة Logging والـ Auditing
5. ⏳ إضافة الـ API Endpoints
