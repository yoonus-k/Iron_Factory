# دليل الصلاحيات التفصيلية - التحكم في الحقول

## 🎯 نظرة عامة

تم إضافة **17 صلاحية تفصيلية** جديدة للتحكم الدقيق في عرض وتعديل الحقول في صفحات المراحل.

---

## 📋 الصلاحيات التفصيلية المتاحة

### المرحلة الأولى (Stage 1)
| الصلاحية | الكود | الوصف |
|---------|------|-------|
| عرض تفاصيل الوزن | `STAGE1_VIEW_WEIGHT` | عرض الوزن الإجمالي، المتبقي، الهدر، النسب |
| تعديل الوزن | `STAGE1_EDIT_WEIGHT` | تعديل وزن الاستاندات |
| عرض معلومات العامل | `STAGE1_VIEW_WORKER` | عرض اسم العامل، الوردية، الوقت |

### المرحلة الثانية (Stage 2)
| الصلاحية | الكود | الوصف |
|---------|------|-------|
| عرض تفاصيل الوزن | `STAGE2_VIEW_WEIGHT` | عرض تفاصيل المعالجة والهدر |
| تعديل الوزن | `STAGE2_EDIT_WEIGHT` | تعديل أوزان المعالجة |
| عرض معلومات العامل | `STAGE2_VIEW_WORKER` | عرض بيانات العامل المسؤول |

### المرحلة الثالثة (Stage 3)
| الصلاحية | الكود | الوصف |
|---------|------|-------|
| عرض تفاصيل الوزن | `STAGE3_VIEW_WEIGHT` | عرض الوزن المضاف (الصبغة + البلاستيك) |
| تعديل الوزن | `STAGE3_EDIT_WEIGHT` | تعديل أوزان اللفائف |
| عرض معلومات العامل | `STAGE3_VIEW_WORKER` | عرض بيانات العامل |

### المرحلة الرابعة (Stage 4)
| الصلاحية | الكود | الوصف |
|---------|------|-------|
| عرض تفاصيل الوزن | `STAGE4_VIEW_WEIGHT` | عرض تفاصيل التعبئة والوزن |
| تعديل الوزن | `STAGE4_EDIT_WEIGHT` | تعديل أوزان الكراتين |
| عرض معلومات العامل | `STAGE4_VIEW_WORKER` | عرض بيانات العامل |

### صلاحيات عامة
| الصلاحية | الكود | الوصف |
|---------|------|-------|
| عرض الأسعار | `VIEW_PRICES` | عرض أسعار المواد والمنتجات |
| تعديل الأسعار | `EDIT_PRICES` | تعديل الأسعار |
| عرض التكاليف | `VIEW_COSTS` | عرض تكاليف الإنتاج |
| حذف السجلات | `DELETE_RECORDS` | حذف سجلات الإنتاج |

---

## 🔑 توزيع الصلاحيات على الأدوار

### Admin (مدير النظام)
✅ **كل الصلاحيات** - يرى ويعدل كل شيء

### Manager (المدير)
✅ عرض وتعديل تفاصيل الوزن في جميع المراحل  
✅ عرض معلومات العمال  
✅ عرض وتعديل الأسعار  
✅ عرض التكاليف  
❌ حذف السجلات

### Supervisor (المشرف)
✅ عرض تفاصيل الوزن في جميع المراحل (قراءة فقط)  
✅ عرض معلومات العمال  
✅ عرض التكاليف  
❌ تعديل الأوزان  
❌ عرض/تعديل الأسعار  
❌ حذف السجلات

### Worker (العامل)
❌ لا يرى التفاصيل الحساسة  
❌ لا يرى الأسعار والتكاليف  
❌ لا يرى معلومات العمال الآخرين  
✅ يستطيع إدخال البيانات الأساسية فقط

---

## 💡 أمثلة الاستخدام في Blade

### 1. إخفاء/إظهار حقل بناءً على الصلاحية

```blade
{{-- حقل الوزن - يظهر فقط لمن لديه صلاحية --}}
@if(canRead('STAGE1_VIEW_WEIGHT'))
<div class="mb-3">
    <label class="form-label">الوزن الإجمالي</label>
    <input type="number" name="weight" class="form-control" value="{{ $stand->weight }}">
</div>
@endif
```

### 2. جعل حقل للقراءة فقط حسب الصلاحية

```blade
<div class="mb-3">
    <label class="form-label">وزن الاستاند</label>
    <input type="number" 
           name="stand_weight" 
           class="form-control" 
           @if(!canUpdate('STAGE1_EDIT_WEIGHT')) readonly @endif>
    
    @if(!canUpdate('STAGE1_EDIT_WEIGHT'))
    <small class="text-muted">لا يمكنك تعديل الوزن (تحتاج صلاحية)</small>
    @endif
</div>
```

### 3. إخفاء عمود في جدول

```blade
<table class="table">
    <thead>
        <tr>
            <th>الباركود</th>
            <th>المادة</th>
            
            {{-- عمود الوزن يظهر فقط لمن لديه صلاحية --}}
            @if(canRead('STAGE1_VIEW_WEIGHT'))
            <th>الوزن</th>
            <th>الهدر</th>
            @endif
            
            {{-- عمود السعر يظهر فقط لمن لديه صلاحية --}}
            @if(canRead('VIEW_PRICES'))
            <th>السعر</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item->barcode }}</td>
            <td>{{ $item->material_name }}</td>
            
            @if(canRead('STAGE1_VIEW_WEIGHT'))
            <td>{{ $item->weight }}</td>
            <td>{{ $item->waste }}</td>
            @endif
            
            @if(canRead('VIEW_PRICES'))
            <td>{{ $item->price }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
```

### 4. إخفاء قسم كامل

```blade
{{-- قسم معلومات العامل - يظهر فقط لمن لديه صلاحية --}}
@if(canRead('STAGE1_VIEW_WORKER'))
<div class="card mb-3">
    <div class="card-header">
        <h5>معلومات العامل</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <label>اسم العامل</label>
                <input type="text" value="{{ $worker->name }}" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label>الوردية</label>
                <input type="text" value="{{ $worker->shift }}" class="form-control" readonly>
            </div>
        </div>
    </div>
</div>
@endif
```

### 5. إخفاء زر بناءً على الصلاحية

```blade
<div class="d-flex gap-2">
    {{-- زر الحفظ - يظهر للجميع من لديهم صلاحية إنشاء --}}
    @if(canCreate('STAGE1_STANDS'))
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> حفظ
    </button>
    @endif
    
    {{-- زر الحذف - يظهر فقط لمن لديه صلاحية حذف --}}
    @if(canDelete('DELETE_RECORDS'))
    <button type="button" class="btn btn-danger" onclick="deleteRecord()">
        <i class="fas fa-trash"></i> حذف
    </button>
    @endif
</div>
```

### 6. رسالة بديلة للمستخدمين بدون صلاحية

```blade
@if(canRead('VIEW_COSTS'))
    <div class="alert alert-info">
        <strong>إجمالي التكلفة:</strong> {{ number_format($totalCost, 2) }} ريال
    </div>
@else
    <div class="alert alert-secondary">
        <i class="fas fa-lock"></i> التكاليف متاحة للمديرين فقط
    </div>
@endif
```

---

## 🎨 أمثلة متقدمة

### مثال 1: نموذج بصلاحيات متعددة

```blade
<form id="productionForm">
    {{-- القسم الأساسي - يظهر للجميع --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <label>الباركود</label>
            <input type="text" name="barcode" class="form-control" required>
        </div>
    </div>

    {{-- قسم الوزن - بصلاحية محددة --}}
    @if(canRead('STAGE1_VIEW_WEIGHT'))
    <div class="border rounded p-3 mb-3 bg-light">
        <h6 class="text-primary mb-3">
            <i class="fas fa-weight"></i> تفاصيل الوزن
        </h6>
        <div class="row">
            <div class="col-md-4">
                <label>الوزن</label>
                <input type="number" 
                       name="weight" 
                       class="form-control"
                       @if(!canUpdate('STAGE1_EDIT_WEIGHT')) readonly @endif>
            </div>
            <div class="col-md-4">
                <label>الهدر</label>
                <input type="number" name="waste" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label>النسبة</label>
                <input type="text" id="wastePercentage" class="form-control" readonly>
            </div>
        </div>
    </div>
    @endif

    {{-- قسم السعر - بصلاحية محددة --}}
    @if(canRead('VIEW_PRICES'))
    <div class="border rounded p-3 mb-3 bg-warning bg-opacity-10">
        <h6 class="text-warning mb-3">
            <i class="fas fa-dollar-sign"></i> معلومات السعر
        </h6>
        <div class="row">
            <div class="col-md-6">
                <label>سعر الكيلو</label>
                <input type="number" 
                       name="price_per_kg" 
                       class="form-control"
                       @if(!canUpdate('EDIT_PRICES')) readonly @endif>
            </div>
            @if(canRead('VIEW_COSTS'))
            <div class="col-md-6">
                <label>التكلفة الإجمالية</label>
                <input type="number" id="totalCost" class="form-control" readonly>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- الأزرار --}}
    <div class="d-flex gap-2">
        @if(canCreate('STAGE1_STANDS'))
        <button type="submit" class="btn btn-primary">حفظ</button>
        @endif
        
        @if(canDelete('DELETE_RECORDS'))
        <button type="button" class="btn btn-danger">حذف</button>
        @endif
    </div>
</form>
```

### مثال 2: JavaScript مع الصلاحيات

```blade
<script>
// تمرير الصلاحيات إلى JavaScript
const permissions = {
    canViewWeight: {{ canRead('STAGE1_VIEW_WEIGHT') ? 'true' : 'false' }},
    canEditWeight: {{ canUpdate('STAGE1_EDIT_WEIGHT') ? 'true' : 'false' }},
    canViewPrices: {{ canRead('VIEW_PRICES') ? 'true' : 'false' }},
    canEditPrices: {{ canUpdate('EDIT_PRICES') ? 'true' : 'false' }},
};

// استخدامها في JavaScript
function calculateTotal() {
    if (permissions.canViewPrices) {
        // احسب السعر
        let total = weight * pricePerKg;
        document.getElementById('totalPrice').value = total;
    }
}

function saveData() {
    if (!permissions.canEditWeight) {
        alert('ليس لديك صلاحية تعديل الوزن');
        return;
    }
    
    // حفظ البيانات
}
</script>
```

---

## 🛠️ كيفية تطبيق الصلاحيات على صفحاتك الحالية

### الخطوة 1: حدد الحقول الحساسة
- حقول الوزن والهدر
- معلومات العمال
- الأسعار والتكاليف
- أزرار الحذف والتعديل

### الخطوة 2: أضف الصلاحيات المناسبة
```blade
@if(canRead('STAGE1_VIEW_WEIGHT'))
    {{-- الحقول الحساسة هنا --}}
@endif
```

### الخطوة 3: اختبر مع مستخدمين مختلفين
- سجل دخول كـ Admin → يجب أن يرى كل شيء
- سجل دخول كـ Manager → يرى التفاصيل
- سجل دخول كـ Worker → لا يرى التفاصيل الحساسة

---

## 📊 جدول توزيع الصلاحيات

| الصلاحية | Admin | Manager | Supervisor | Worker |
|---------|-------|---------|-----------|--------|
| عرض الوزن | ✅ | ✅ | ✅ | ❌ |
| تعديل الوزن | ✅ | ✅ | ❌ | ❌ |
| عرض العامل | ✅ | ✅ | ✅ | ❌ |
| عرض الأسعار | ✅ | ✅ | ❌ | ❌ |
| تعديل الأسعار | ✅ | ✅ | ❌ | ❌ |
| عرض التكاليف | ✅ | ✅ | ✅ | ❌ |
| حذف السجلات | ✅ | ❌ | ❌ | ❌ |

---

## ✅ قائمة التحقق السريعة

عند إضافة صفحة جديدة، تأكد من:

- [ ] حماية حقول الوزن بـ `STAGE*_VIEW_WEIGHT`
- [ ] حماية تعديل الوزن بـ `STAGE*_EDIT_WEIGHT`
- [ ] حماية معلومات العامل بـ `STAGE*_VIEW_WORKER`
- [ ] حماية الأسعار بـ `VIEW_PRICES`
- [ ] حماية التكاليف بـ `VIEW_COSTS`
- [ ] حماية زر الحذف بـ `DELETE_RECORDS`
- [ ] اختبار الصفحة مع أدوار مختلفة

---

**تم التحديث:** 22 نوفمبر 2025
