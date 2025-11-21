# 🔔 نظام الإشعارات والتنبيهات

## نظرة عامة
نظام إشعارات متكامل يسجل جميع العمليات والأنشطة في النظام ويرسل تنبيهات للمستخدمين عند حدوث أي عملية مهمة.

## الميزات الرئيسية

### ✅ أنواع الإشعارات المدعومة
1. **إضافة مادة جديدة** - `material_added`
2. **تحديث مادة** - `material_updated`  
3. **إنشاء فاتورة شراء** - `purchase_invoice_created`
4. **تسجيل أذن توصيل** - `delivery_note_registered`
5. **نقل إلى الإنتاج** - `moved_to_production`
6. **حركة مستودع** - `material_movement`
7. **فرق وزن** - `weight_discrepancy`
8. **محاولة مكررة** - `duplicate_attempt`
9. **تجاوز الحد الأقصى** - `max_attempts_exceeded`

### 🎨 ألوان الإشعارات
- **أخضر (Success)** - عمليات ناجحة: ✅ مادة مضافة، فاتورة تم إنشاؤها
- **أزرق (Info)** - معلومات عامة: ℹ️ حركات المستودع، تحديثات
- **برتقالي (Warning)** - تنبيهات: ⚠️ محاولات مكررة، نقل إلى إنتاج
- **أحمر (Danger)** - أخطاء حرجة: 🔴 فروقات وزن، تجاوز محاولات

## قاعدة البيانات

### جدول `notifications`
```sql
CREATE TABLE notifications (
    id - معرف فريد
    user_id - المستخدم المستقبل
    type - نوع الإشعار
    title - العنوان
    message - الرسالة
    icon - رمز الإشعار
    color - اللون (success, danger, warning, info)
    action_type - نوع الإجراء (create, update, delete, transfer...)
    model_type - نوع النموذج (Material, PurchaseInvoice...)
    model_id - معرف النموذج
    created_by - المستخدم الذي قام بالعملية
    is_read - هل تم قراءة الإشعار
    read_at - وقت القراءة
    action_url - رابط الإجراء
    metadata - بيانات إضافية (JSON)
);
```

## كيفية الاستخدام

### 1️⃣ Dependency Injection في Controller

```php
use App\Services\NotificationService;

class YourController extends Controller
{
    public function store(Request $request, NotificationService $notificationService)
    {
        // ... كود العملية
        
        // إرسال إشعار
        $notificationService->notifyMaterialAdded(
            $user,
            $material,
            Auth::user()
        );
    }
}
```

### 2️⃣ استخدام الـ Notification Service

#### إضافة مادة
```php
$notificationService->notifyMaterialAdded($user, $material, Auth::user());
```

#### تحديث مادة
```php
$notificationService->notifyMaterialUpdated($user, $material, Auth::user());
```

#### إنشاء فاتورة شراء
```php
$notificationService->notifyPurchaseInvoiceCreated($user, $invoice, Auth::user());
```

#### تسجيل أذن توصيل
```php
$notificationService->notifyDeliveryNoteRegistered($user, $deliveryNote, Auth::user());
```

#### نقل إلى الإنتاج
```php
$notificationService->notifyMoveToProduction($user, $deliveryNote, $quantity, Auth::user());
```

#### حركة مستودع
```php
$notificationService->notifyMaterialMovement($user, $movement, Auth::user());
```

#### تحذير فرق وزن
```php
$notificationService->notifyWeightDiscrepancy($user, $deliveryNote, $difference, Auth::user());
```

#### تحذير محاولة مكررة
```php
$notificationService->notifyDuplicateAttempt($user, $deliveryNote, $attemptCount, Auth::user());
```

#### تجاوز الحد الأقصى للمحاولات
```php
$notificationService->notifyMaxAttemptsExceeded($user, $deliveryNote, Auth::user());
```

#### إشعار مخصص
```php
$notificationService->notifyCustom(
    $user,
    'عنوان الإشعار',
    'نص الإشعار',
    'custom',  // النوع
    'warning', // اللون
    'feather icon-alert-circle', // الأيقونة
    '/path/to/action', // رابط الإجراء
    ['key' => 'value'] // بيانات إضافية
);
```

### 3️⃣ عرض الإشعارات

#### الصفحة الرئيسية للإشعارات
```
http://localhost/notifications
```

#### الحصول على الإشعارات عبر API
```
GET /notifications/api?limit=20&unread=true
```

#### الرد:
```json
{
    "success": true,
    "count": 5,
    "unread_count": 3,
    "notifications": [
        {
            "id": 1,
            "title": "تم إضافة مادة جديدة",
            "message": "تم إضافة المادة 'الحديد' بنجاح",
            "type": "material_added",
            "color": "success",
            "is_read": false,
            "created_at": "منذ 5 دقائق",
            "created_by_name": "أحمد محمد"
        }
    ]
}
```

## التطبيق في المشروع الحالي

### 1️⃣ في `WarehouseProductController`

عند إضافة مادة:
```php
public function store(StoreMaterialRequest $request)
{
    // ... كود الحفظ
    
    $this->notificationService->notifyMaterialAdded(
        User::where('is_admin', true)->first(),
        $material,
        Auth::user()
    );
}
```

### 2️⃣ في `PurchaseInvoiceController`

عند إنشاء فاتورة:
```php
public function store(Request $request)
{
    // ... كود الحفظ
    
    $this->notificationService->notifyPurchaseInvoiceCreated(
        User::where('role', 'manager')->first(),
        $invoice,
        Auth::user()
    );
}
```

### 3️⃣ في `WarehouseRegistrationController`

عند تسجيل أذن:
```php
public function store(Request $request, DeliveryNote $deliveryNote)
{
    // ... كود التسجيل
    
    $this->notificationService->notifyDeliveryNoteRegistered(
        User::where('role', 'warehouse_manager')->first(),
        $deliveryNote,
        Auth::user()
    );
}
```

## الإحصائيات والتقارير

### الحصول على إحصائيات المستخدم
```php
$stats = Notification::getStatistics(Auth::id());

// النتيجة:
[
    'total' => 150,
    'unread' => 5,
    'read' => 145,
    'by_type' => [...],
    'by_color' => [...]
]
```

## الأوامر في Terminal

### حذف الإشعارات القديمة
```bash
php artisan notifications:clean --days=30
```

### حذف الإشعارات التي مضى عليها أكثر من 60 يوم
```bash
php artisan notifications:clean --days=60
```

## التخصيص والتوسع

### إضافة نوع إشعار جديد

```php
// في NotificationService
public function notifyNewFeature($user, $data, $creator = null)
{
    return $this->create(
        $user,
        'new_feature',
        'عنوان الإشعار',
        'نص الإشعار',
        'action_type',
        'ModelType',
        $data->id,
        'info', // اللون
        'feather icon-star', // الأيقونة
        route('resource.show', $data->id), // الرابط
        ['key' => 'value'] // البيانات
    );
}
```

### تصفية الإشعارات

```php
// الحصول على الإشعارات غير المقروءة فقط
$unread = Notification::where('user_id', Auth::id())
    ->unread()
    ->latest()
    ->get();

// الحصول على إشعارات معينة
$material_notifications = Notification::where('user_id', Auth::id())
    ->byType('material_added')
    ->latest()
    ->get();

// الإشعارات الحرجة فقط
$critical = Notification::where('user_id', Auth::id())
    ->byColor('danger')
    ->latest()
    ->get();
```

## الأداء والتحسينات

### نصائح لتحسين الأداء:
1. حذف الإشعارات القديمة بشكل دوري
2. استخدام caching للإشعارات المتكررة
3. تحديد الفهارس على الأعمدة الأكثر استخداماً
4. استخدام Queues لإرسال الإشعارات غير الحرجة

### Migration للتشغيل
```bash
php artisan migrate
```

## الدعم والمساعدة

للمزيد من المعلومات أو الإبلاغ عن مشاكل، يرجى التواصل مع فريق التطوير.

---
**تم إنشاء هذا النظام في: 2025-11-21**
