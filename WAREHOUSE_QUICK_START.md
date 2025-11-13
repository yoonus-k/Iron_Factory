# استخدام سريع - نظام المستودعات

## البنية الأساسية

```
Modules/Manufacturing/
├── Http/
│   ├── Controllers/
│   │   └── WarehouseController.php (معدل)
│   └── Requests/
│       ├── StoreWarehouseRequest.php (جديد)
│       └── UpdateWarehouseRequest.php (جديد)
├── Repositories/
│   └── WarehouseRepository.php (جديد)
├── Services/
│   └── WarehouseService.php (جديد)
└── routes/
    └── web.php (معدل)
```

## كيفية الاستخدام

### 1. إضافة مستودع جديد

#### من خلال الـ Controller:
```php
// POST /warehouses
POST /warehouses HTTP/1.1
Content-Type: application/x-www-form-urlencoded

name=المستودع الرئيسي&code=WH-001&location=القاهرة&status=active&phone=0123456789
```

#### الاستجابة:
```
✅ تم إضافة المستودع بنجاح
redirect -> /warehouses
```

### 2. عرض جميع المستودعات

```
GET /warehouses
```

#### مع البحث والتصفية:
```
GET /warehouses?search=WH&status=active
```

### 3. عرض مستودع محدد

```
GET /warehouses/1
```

### 4. تعديل مستودع

```
PUT /warehouses/1
Content-Type: application/x-www-form-urlencoded

name=المستودع المعدل&code=WH-001&location=القاهرة الجديدة&status=active
```

### 5. حذف مستودع

```
DELETE /warehouses/1
```

## مثال عملي كامل

### إضافة مستودع عبر Blade Form:

```blade
@extends('master')

@section('content')
    <form action="{{ route('manufacturing.warehouses.store') }}" method="POST">
        @csrf
        
        <div>
            <label>اسم المستودع</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label>رمز المستودع</label>
            <input type="text" name="code" value="{{ old('code') }}" required>
            @error('code') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label>الموقع</label>
            <input type="text" name="location" value="{{ old('location') }}">
            @error('location') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label>رقم الهاتف</label>
            <input type="text" name="phone" value="{{ old('phone') }}">
            @error('phone') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label>الحالة</label>
            <select name="status" required>
                <option value="active">نشط</option>
                <option value="inactive">غير نشط</option>
            </select>
        </div>

        <button type="submit">إضافة المستودع</button>
    </form>
@endsection
```

### عرض جميع المستودعات:

```blade
@extends('master')

@section('content')
    <table>
        <thead>
            <tr>
                <th>اسم المستودع</th>
                <th>الرمز</th>
                <th>الموقع</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($warehouses as $warehouse)
                <tr>
                    <td>{{ $warehouse->warehouse_name }}</td>
                    <td>{{ $warehouse->warehouse_code }}</td>
                    <td>{{ $warehouse->location }}</td>
                    <td>
                        <span class="badge {{ $warehouse->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $warehouse->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('manufacturing.warehouses.show', $warehouse->id) }}">عرض</a>
                        <a href="{{ route('manufacturing.warehouses.edit', $warehouse->id) }}">تعديل</a>
                        <form action="{{ route('manufacturing.warehouses.destroy', $warehouse->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    {{ $warehouses->links() }}
@endsection
```

## استخدام الـ Repository والـ Service مباشرة

### في Controller أو Service خاص:

```php
use Modules\Manufacturing\Repositories\WarehouseRepository;
use Modules\Manufacturing\Services\WarehouseService;

class ReportController extends Controller
{
    public function __construct(
        private WarehouseRepository $warehouseRepo,
        private WarehouseService $warehouseService
    ) {}

    public function generateReport()
    {
        // جلب جميع المستودعات النشطة
        $activeWarehouses = $this->warehouseRepo->getActive();

        // البحث
        $results = $this->warehouseService->searchWarehouses([
            'search' => 'WH',
            'status' => 'active'
        ]);

        // الإحصائيات
        $stats = $this->warehouseService->getStatistics();
        
        // المخرجات
        echo "إجمالي المستودعات: " . $stats['total'];
        echo "المستودعات النشطة: " . $stats['active'];
        echo "المستودعات المعطلة: " . $stats['inactive'];
    }
}
```

## قواعد Validation مفصلة

### عند الإضافة (POST):
- `name` - مطلوب، فريد
- `code` - مطلوب، فريد، بحد أقصى 50 حرف
- `location` - اختياري
- `status` - مطلوب (active/inactive)
- `phone` - اختياري، بحد أقصى 20 حرف
- `email` - اختياري، يجب أن يكون بريد صحيح

### عند التعديل (PUT):
- نفس القواعد لكن بتحديث الفحص الفريد (يستثني السجل الحالي)

## رسائل الخطأ الشائعة

```
❌ "اسم المستودع مطلوب"
   السبب: لم يتم ملء حقل الاسم
   الحل: أدخل اسم المستودع

❌ "رمز المستودع موجود بالفعل"
   السبب: الرمز مستخدم في مستودع آخر
   الحل: استخدم رمز مختلف

❌ "المسؤول المختار غير موجود"
   السبب: رقم المستخدم غير صحيح
   الحل: اختر مسؤول من القائمة

❌ "البريد الإلكتروني غير صحيح"
   السبب: صيغة البريد غير صحيحة
   الحل: أدخل بريد صحيح مثل: user@example.com
```

## نصائح مهمة

1. **الرموز:** استخدم رموز موحدة مثل WH-001, WH-002, إلخ
2. **الأسماء:** لا تستخدم أحرف خاصة في الأسماء
3. **الترقيم:** النظام يعرض 10 مستودعات لكل صفحة تلقائياً
4. **البحث:** يبحث في الاسم والرمز والموقع
5. **التصفية:** يمكنك تصفية حسب الحالة (نشط/غير نشط)

## الرسوم البيانية API

### جلب الإحصائيات (JSON):
```
GET /warehouses/statistics

الاستجابة:
{
    "total": 10,
    "active": 8,
    "inactive": 2
}
```

### جلب المستودعات النشطة (JSON):
```
GET /warehouses/active

الاستجابة:
[
    {
        "id": 1,
        "warehouse_code": "WH-001",
        "warehouse_name": "المستودع الرئيسي",
        "location": "القاهرة",
        "is_active": true
    },
    ...
]
```
