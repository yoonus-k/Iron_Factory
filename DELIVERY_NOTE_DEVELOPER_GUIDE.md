# 👨‍💻 دليل المطورين - نموذج أذن التسليم المبسط

## 📁 هيكل الملفات

```
Modules/Manufacturing/resources/views/warehouses/delivery-notes/
├── create.blade.php          ← النموذج الجديد المبسط
├── edit.blade.php            ← التعديل (قد يتم تحديثه لاحقاً)
├── index.blade.php           ← قائمة الأذون (محدّثة)
├── show.blade.php            ← عرض الأذن
└── create.blade.php.bak      ← نسخة احتياطية من النموذج القديم
```

---

## 🏗️ بنية النموذج (create.blade.php)

### الأقسام الرئيسية:

```blade
1. الرأس (Header)
   - العنوان
   - التنقل (Breadcrumb)

2. بطاقة النموذج (Form Card)
   ├── رسائل النجاح
   ├── رسائل الأخطاء
   └── النموذج الرئيسي
       ├── اختيار النوع (Type Selection)
       ├── البيانات الأساسية (Basic Fields)
       ├── قسم الأذن الواردة (Incoming Section)
       ├── قسم الأذن الصادرة (Outgoing Section)
       └── أزرار الإجراء (Form Actions)

3. سكريبت JavaScript
   ├── معالجة التبديل بين الأقسام
   ├── تحديث قوائم المواد ديناميكياً
   └── التحقق من صحة البيانات
```

---

## 🎯 الميزات الرئيسية

### 1. التبديل الديناميكي بين الأقسام

```javascript
function toggleSections() {
    if (typeIncoming.checked) {
        // إظهار قسم الأذن الواردة
        incomingSection.style.display = '';
        outgoingSection.style.display = 'none';
        // تعيين الحقول المطلوبة
        document.getElementById('supplier_id').required = true;
        // إلخ...
    } else {
        // إظهار قسم الأذن الصادرة
        // ...
    }
}
```

### 2. تحديث قوائم المواد المتاحة

```javascript
function updateMaterials() {
    const warehouseId = warehouseFromId.value;
    const filtered = materialDetails.filter(m => 
        m.warehouse_id == warehouseId && m.quantity > 0
    );
    // إضافة المواد المتاحة إلى القائمة
}
```

### 3. عرض معلومات المادة

```javascript
materialSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const quantity = selectedOption.getAttribute('data-quantity');
    materialQuantityDisplay.innerHTML = 
        `✓ متوفر: <strong>${quantity}</strong>`;
});
```

---

## 📊 نموذج البيانات

### بيانات المواد (materialDetails)

```javascript
[
    {
        id: 1,
        material_id: 101,
        warehouse_id: 1,
        material_name: "سلك نحاسي",
        quantity: 500,
        unit_name: "كيلو"
    },
    {
        id: 2,
        material_id: 102,
        warehouse_id: 2,
        material_name: "كرتون",
        quantity: 1000,
        unit_name: "متر"
    }
]
```

---

## 🔐 التحقق من البيانات

### التحقق على جانب العميل (Client-side)

```javascript
form.addEventListener('submit', function(e) {
    // 1. التحقق من نوع الأذن
    if (!type) {
        e.preventDefault();
        alert('الرجاء اختيار نوع الأذن');
        return false;
    }

    // 2. التحقق حسب نوع الأذن
    if (type.value === 'incoming') {
        if (!document.getElementById('supplier_id').value) {
            alert('الرجاء اختيار المورد');
            return false;
        }
    } else {
        if (!warehouseFromId.value) {
            alert('الرجاء اختيار المستودع');
            return false;
        }
    }

    // 3. منع الإرسال المتكرر
    isSubmitting = true;
    submitBtn.disabled = true;
});
```

### التحقق على جانب الخادم (Server-side)

يجب إضافة التحقق في `DeliveryNoteController`:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'type' => 'required|in:incoming,outgoing',
        'delivery_date' => 'required|date',
        'supplier_id' => 'required_if:type,incoming',
        'warehouse_id' => 'required_if:type,incoming',
        'invoice_weight' => 'required_if:type,incoming|numeric|min:0',
        'warehouse_from_id' => 'required_if:type,outgoing',
        'material_detail_id' => 'required_if:type,outgoing',
        'delivery_quantity' => 'required_if:type,outgoing|numeric|min:0',
    ]);

    // معالجة البيانات...
}
```

---

## 🎨 التصميم والأنماط

### الفئات المستخدمة

```css
/* أقسام حسب الدور */
.form-section.warehouse-only { display: block; }
.form-section.admin-only { display: none; }

/* شارة الدور */
.role-badge {
    background: #d4edda;
    color: #155724;
}

/* الأزرار */
.btn-submit { /* محدد في CSS عام */ }
.btn-submit:disabled { 
    background-color: #95a5a6;
    opacity: 0.7;
}
```

---

## 📱 الاستجابة (Responsiveness)

النموذج مصمم ليعمل على:
- الشاشات الكبيرة (Desktop): عرض جانبي للحقول
- الشاشات المتوسطة (Tablet): عمود واحد
- الشاشات الصغيرة (Mobile): نموذج طويل رأسي

---

## 🔗 العلاقات بين النماذج

```
DeliveryNote (أذن التسليم)
├── Supplier (المورد) - للأذن الواردة
├── Warehouse (المستودع)
├── MaterialDetail (تفاصيل المادة) - للأذن الصادرة
└── Material (المادة)
```

---

## 🚀 كيفية التوسع والتحديث

### إضافة حقل جديد

1. **في النموذج (Blade):**
```blade
<div class="form-group">
    <label for="new_field">الحقل الجديد</label>
    <input type="text" name="new_field" id="new_field">
</div>
```

2. **في الـ Controller:**
```php
'new_field' => 'required|string',
```

3. **في الـ Migration (إذا لزم الأمر):**
```php
Schema::table('delivery_notes', function (Blueprint $table) {
    $table->string('new_field')->nullable();
});
```

### إضافة تحقق ديناميكي

```javascript
// أضف event listener للحقل الجديد
document.getElementById('new_field').addEventListener('change', function() {
    // منطق التحقق أو التحديث
});
```

---

## 🐛 تصحيح الأخطاء (Debugging)

### طباعة البيانات

```javascript
console.log('Material Details:', materialDetails);
console.log('Selected Type:', document.querySelector('input[name="type"]:checked').value);
```

### تتبع الأخطاء

```javascript
try {
    // كود يحتمل أن يسبب خطأ
} catch (error) {
    console.error('Error:', error);
    alert('حدث خطأ: ' + error.message);
}
```

---

## 📝 معايير الكود

### Blade Templates
- استخدام `@php` للمنطق البسيط فقط
- استخدام الـ loops والـ conditionals بشكل واضح
- إضافة تعليقات للأقسام الكبيرة

### JavaScript
- استخدام `const` و `let` بدلاً من `var`
- إضافة تعليقات للدوال المعقدة
- معالجة الأخطاء بشكل صحيح

### CSS
- تنظيم الأنماط حسب المكونات
- استخدام متغيرات للألوان
- تجنب الأنماط الإضافية

---

## 🔄 دورة حياة الطلب

```
1. صفحة الفهرس (Index)
   ↓
2. نقر على "إنشاء أذن تسليم جديدة"
   ↓
3. صفحة الإنشاء (Create)
   ↓
4. اختيار النوع والبيانات
   ↓
5. التحقق من جانب العميل
   ↓
6. إرسال الطلب (POST)
   ↓
7. معالجة في Controller
   ↓
8. التحقق من جانب الخادم
   ↓
9. حفظ في قاعدة البيانات
   ↓
10. إعادة التوجيه إلى الفهرس
```

---

## 📚 المراجع والموارد

- **Laravel Documentation**: https://laravel.com/docs
- **Blade Templates**: https://laravel.com/docs/blade
- **Form Validation**: https://laravel.com/docs/validation
- **Database Relationships**: https://laravel.com/docs/eloquent-relationships

---

## ✅ قائمة التحقق للتطوير

- [ ] النموذج يعمل بدون أخطاء
- [ ] التحقق من البيانات يعمل بشكل صحيح
- [ ] الأخطاء تُعرض بشكل واضح
- [ ] الصفحة تستجيب على جميع الأجهزة
- [ ] الأداء مرضٍ (< 2 ثانية)
- [ ] لا توجد تسريبات في الأمان
- [ ] الكود موثق بشكل جيد
- [ ] تم الاختبار على متصفحات مختلفة

---

**آخر تحديث:** 23 نوفمبر 2025
**الإصدار:** 1.0
**حالة التوثيق:** ✅ مكتملة
