# 🎯 مرجع سريع - الصلاحيات التفصيلية

## الصلاحيات المتاحة

```php
// المرحلة الأولى
'STAGE1_VIEW_WEIGHT'   // عرض الوزن والهدر
'STAGE1_EDIT_WEIGHT'   // تعديل الوزن
'STAGE1_VIEW_WORKER'   // عرض معلومات العامل

// المرحلة الثانية
'STAGE2_VIEW_WEIGHT'   // عرض الوزن والهدر
'STAGE2_EDIT_WEIGHT'   // تعديل الوزن
'STAGE2_VIEW_WORKER'   // عرض معلومات العامل

// المرحلة الثالثة
'STAGE3_VIEW_WEIGHT'   // عرض الوزن المضاف
'STAGE3_EDIT_WEIGHT'   // تعديل الوزن
'STAGE3_VIEW_WORKER'   // عرض معلومات العامل

// المرحلة الرابعة
'STAGE4_VIEW_WEIGHT'   // عرض الوزن
'STAGE4_EDIT_WEIGHT'   // تعديل الوزن
'STAGE4_VIEW_WORKER'   // عرض معلومات العامل

// عام
'VIEW_PRICES'          // عرض الأسعار
'EDIT_PRICES'          // تعديل الأسعار
'VIEW_COSTS'           // عرض التكاليف
'DELETE_RECORDS'       // حذف السجلات
```

---

## 🔥 أمثلة سريعة

### إخفاء حقل
```blade
@if(canRead('STAGE1_VIEW_WEIGHT'))
<input type="number" name="weight" class="form-control">
@endif
```

### حقل للقراءة فقط
```blade
<input type="number" 
       name="weight" 
       @if(!canUpdate('STAGE1_EDIT_WEIGHT')) readonly @endif>
```

### إخفاء عمود في جدول
```blade
@if(canRead('STAGE1_VIEW_WEIGHT'))
<th>الوزن</th>
@endif
```

### إخفاء زر
```blade
@if(canDelete('DELETE_RECORDS'))
<button class="btn btn-danger">حذف</button>
@endif
```

### قسم كامل
```blade
@if(canRead('VIEW_PRICES'))
<div class="card">
    {{-- معلومات الأسعار --}}
</div>
@endif
```

---

## 📋 قالب جاهز للنسخ

```blade
{{-- حقول الوزن --}}
@if(canRead('STAGE1_VIEW_WEIGHT'))
<div class="row">
    <div class="col-md-3">
        <label>الوزن</label>
        <input type="number" name="weight" class="form-control" 
               @if(!canUpdate('STAGE1_EDIT_WEIGHT')) readonly @endif>
    </div>
    <div class="col-md-3">
        <label>الهدر</label>
        <input type="number" name="waste" class="form-control" readonly>
    </div>
</div>
@endif

{{-- معلومات العامل --}}
@if(canRead('STAGE1_VIEW_WORKER'))
<div class="row">
    <div class="col-md-6">
        <label>العامل</label>
        <select name="worker_id" class="form-control">
            <option>اختر العامل</option>
        </select>
    </div>
</div>
@endif

{{-- الأسعار --}}
@if(canRead('VIEW_PRICES'))
<div class="row">
    <div class="col-md-6">
        <label>السعر</label>
        <input type="number" name="price" class="form-control"
               @if(!canUpdate('EDIT_PRICES')) readonly @endif>
    </div>
</div>
@endif

{{-- التكاليف --}}
@if(canRead('VIEW_COSTS'))
<div class="alert alert-info">
    التكلفة: {{ $cost }} ريال
</div>
@endif
```

---

## 🎯 من يرى ماذا؟

| الحقل | Admin | Manager | Supervisor | Worker |
|------|-------|---------|------------|--------|
| الوزن (عرض) | ✅ | ✅ | ✅ | ❌ |
| الوزن (تعديل) | ✅ | ✅ | ❌ | ❌ |
| العامل | ✅ | ✅ | ✅ | ❌ |
| السعر | ✅ | ✅ | ❌ | ❌ |
| التكلفة | ✅ | ✅ | ✅ | ❌ |
| الحذف | ✅ | ❌ | ❌ | ❌ |

---

## ⚡ نصيحة سريعة

لعرض البيانات في جدول مع إخفاء أعمدة:

```blade
<table>
    <thead>
        <tr>
            <th>الباركود</th>
            @if(canRead('STAGE1_VIEW_WEIGHT'))
            <th>الوزن</th>
            @endif
            @if(canRead('VIEW_PRICES'))
            <th>السعر</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item->barcode }}</td>
            @if(canRead('STAGE1_VIEW_WEIGHT'))
            <td>{{ $item->weight }}</td>
            @endif
            @if(canRead('VIEW_PRICES'))
            <td>{{ $item->price }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
```

---

**استخدم هذا المرجع عند إنشاء أي صفحة جديدة!** 🚀
