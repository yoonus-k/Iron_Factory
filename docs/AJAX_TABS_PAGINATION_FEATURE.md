# ✨ AJAX تبويبات مع Pagination بدون Reload

## 📋 ملخص الميزة الجديدة

تم تطوير نظام تبويبات تفاعلي (Tabs) مع تحميل البيانات عبر **AJAX** بدون تحديث الصفحة، حيث يمكن للمستخدم:

- ✅ التنقل بين التبويبات (فواتير / أذون تسليم) بدون reload
- ✅ تحميل البيانات ديناميكياً لكل تبويب
- ✅ تصفح الصفحات بشكل مستقل لكل قسم
- ✅ واجهة احترافية مع pagination محسّنة

---

## 🎯 المميزات الرئيسية

### 1️⃣ نظام التبويبات (Tabs)
```blade
<ul class="nav nav-tabs supplier-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="invoices-tab" 
                data-bs-toggle="tab" data-bs-target="#invoices-panel">
            📄 فواتير المورد
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="delivery-tab" 
                data-bs-toggle="tab" data-bs-target="#delivery-panel">
            📦 أذون التسليم (الواردة)
        </button>
    </li>
</ul>
```

### 2️⃣ تحميل AJAX للبيانات

#### ملفات Partial المستخدمة:
- `partials/invoices-table.blade.php` - جدول الفواتير
- `partials/delivery-notes-table.blade.php` - جدول أذون التسليم

#### JavaScript للتحميل:
```javascript
function loadInvoices(supplierId, page = 1) {
    const container = document.getElementById('invoices-container');
    const loading = document.getElementById('invoices-loading');
    
    loading.style.display = 'block';
    container.style.opacity = '0.5';

    fetch(`/manufacturing/suppliers/${supplierId}/invoices?page=${page}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        container.innerHTML = data.html;
        pagination.innerHTML = data.pagination;
        container.style.opacity = '1';
        loading.style.display = 'none';
    });
}
```

### 3️⃣ Pagination محسّنة

#### التصميم الجديد:
```css
.pagination .page-link {
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    background-color: white;
    color: #0d6efd;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background-color: #0d6efd;
    color: white;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}
```

#### عرض معلومات الصفحة:
```blade
<div class="um-pagination-section">
    <div>
        <p class="um-pagination-info">
            عرض {{ $invoices->firstItem() ?? 0 }} إلى {{ $invoices->lastItem() ?? 0 }} 
            من أصل {{ $invoices->total() }} فاتورة
        </p>
    </div>
    <div id="invoices-pagination">
        {!! $invoices->render() !!}
    </div>
</div>
```

---

## 🛠️ التعديلات التقنية

### 1. SupplierController.php

#### الدوال الجديدة:

**getInvoices()** - الحصول على فواتير المورد عبر AJAX
```php
public function getInvoices($id, Request $request)
{
    $supplier = Supplier::findOrFail($id);
    $page = $request->get('page', 1);

    $invoices = $supplier->purchaseInvoices()
        ->orderBy('created_at', 'desc')
        ->paginate(10, ['*'], 'invoice_page', $page);

    if ($request->ajax()) {
        return response()->json([
            'html' => view('manufacturing::warehouses.suppliers.partials.invoices-table', 
                          compact('invoices'))->render(),
            'pagination' => (string) $invoices->links()
        ]);
    }

    return view('manufacturing::warehouses.suppliers.partials.invoices-table', 
               compact('invoices'));
}
```

**getDeliveryNotes()** - الحصول على أذون التسليم عبر AJAX
```php
public function getDeliveryNotes($id, Request $request)
{
    $supplier = Supplier::findOrFail($id);
    $page = $request->get('page', 1);

    $deliveryNotes = DeliveryNote::where('supplier_id', $supplier->id)
        ->where('type', 'incoming')
        ->orderBy('delivery_date', 'desc')
        ->paginate(10, ['*'], 'delivery_page', $page);

    if ($request->ajax()) {
        return response()->json([
            'html' => view('manufacturing::warehouses.suppliers.partials.delivery-notes-table', 
                          compact('deliveryNotes'))->render(),
            'pagination' => (string) $deliveryNotes->links()
        ]);
    }

    return view('manufacturing::warehouses.suppliers.partials.delivery-notes-table', 
               compact('deliveryNotes'));
}
```

### 2. Routes (web.php)

```php
Route::get('suppliers/{id}/invoices', [SupplierController::class, 'getInvoices'])
    ->name('manufacturing.suppliers.invoices');
    
Route::get('suppliers/{id}/delivery-notes', [SupplierController::class, 'getDeliveryNotes'])
    ->name('manufacturing.suppliers.delivery-notes');
```

### 3. View (show.blade.php)

#### هيكل التبويبات:
```blade
<!-- تبويبات الفواتير وأذون التسليم -->
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs supplier-tabs" role="tablist">
            <!-- buttons here -->
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <!-- invoices tab -->
            <div class="tab-pane fade show active" id="invoices-panel">
                <div id="invoices-container">
                    <!-- data loaded here -->
                </div>
                <div id="invoices-loading" style="display: none;">
                    <!-- loading spinner -->
                </div>
                <div id="invoices-pagination">
                    <!-- pagination links -->
                </div>
            </div>
            
            <!-- delivery notes tab -->
            <div class="tab-pane fade" id="delivery-panel">
                <!-- same structure -->
            </div>
        </div>
    </div>
</div>
```

---

## 📱 الاستخدام

### للمستخدم النهائي:

1. **فتح صفحة تفاصيل المورد**
   ```
   /manufacturing/suppliers/{id}
   ```

2. **التنقل بين التبويبات**
   - انقر على "📄 فواتير المورد" لعرض الفواتير
   - انقر على "📦 أذون التسليم" لعرض أذون التسليم
   - لا يوجد refresh للصفحة

3. **التنقل بين الصفحات**
   - انقر على أزرار الصفحات بدون reload
   - البيانات تحمّل ديناميكياً

---

## 🎨 التصميم والألوان

### الحالات اللونية للفواتير:
- 🟦 `draft` - رمادي (#95a5a6)
- 🟨 `pending` - أصفر (#f39c12)
- 🟩 `approved` - أخضر (#27ae60)
- 🟦 `paid` - أزرق (#3498db)
- 🟥 `rejected` - أحمر (#e74c3c)

### الحالات اللونية لأذون التسليم:
- 🟨 `pending` - أصفر (#f39c12)
- 🟩 `approved/registered` - أخضر (#27ae60)
- 🟦 `in_production` - أزرق (#3498db)
- 🟥 `rejected` - أحمر (#e74c3c)
- 🟦 `not_registered` - رمادي (#95a5a6)

### أزرار الـ Pagination:
```css
الحالة العادية: أبيض مع حد أزرق، نص أزرق
الحالة النشطة: أزرق مع نص أبيض
الحالة معطلة: رمادي فاتح مع نص رمادي
```

---

## 🔍 معالجة الأخطاء

### في JavaScript:
```javascript
.catch(error => {
    console.error('Error:', error);
    loading.style.display = 'none';
    container.style.opacity = '1';
});
```

### في PHP:
```php
if ($request->ajax()) {
    return response()->json([...]);
}
// Fallback for non-AJAX requests
```

---

## ⚡ الأداء

| العنصر | القيمة |
|--------|---------|
| حجم البيانات لكل صفحة (الفواتير) | 10 سجلات |
| حجم البيانات لكل صفحة (أذون التسليم) | 10 سجلات |
| وقت التحميل المتوقع | < 500ms |
| طريقة التخزين المؤقت | JSON Response |

---

## 📦 الملفات المتأثرة

### تم تعديل:
1. `SupplierController.php` - إضافة getInvoices و getDeliveryNotes
2. `show.blade.php` - إضافة نظام التبويبات
3. `routes/web.php` - إضافة الروابط الجديدة

### تم إنشاء:
1. `partials/invoices-table.blade.php` - جدول الفواتير
2. `partials/delivery-notes-table.blade.php` - جدول أذون التسليم

---

## 🧪 اختبار الميزة

### 1. اختبار بدون Reload:
1. افتح صفحة المورد
2. انقر على تبويب الفواتير
3. تحقق من عدم حدوث refresh
4. انقر على تبويب أذون التسليم
5. تحقق من عدم حدوث refresh

### 2. اختبار Pagination:
1. إذا كان هناك أكثر من 10 فواتير
2. انقر على صفحة التالية
3. تحقق من عدم refresh الصفحة الأساسية
4. تحقق من ظهور البيانات الجديدة

### 3. اختبار الأداء:
1. افتح Developer Tools (F12)
2. اذهب لـ Network tab
3. لاحظ الطلبات AJAX
4. تحقق من أن الطلب يحتوي على JSON فقط

---

## 🚀 الخطوات التالية (اختياري)

### يمكن إضافة في المستقبل:
- [ ] البحث والفلترة داخل التبويبات
- [ ] تصدير البيانات لـ Excel
- [ ] مشاركة البيانات عبر البريد الإلكتروني
- [ ] طباعة البيانات
- [ ] تنقية التعارضات والنسخ المكررة

---

## 📞 للدعم الفني

في حالة وجود مشاكل:
1. تحقق من console لوجود أخطاء JavaScript
2. تحقق من Laravel logs
3. تحقق من توفر الـ routes
4. تأكد من أن Ajax headers صحيحة

---

**آخر تحديث:** 22 نوفمبر 2025
**الحالة:** ✅ جاهز للإنتاج
