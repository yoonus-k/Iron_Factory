# 🔌 ربط الـ Views مع Backend

## المرحلة الأخيرة: توصيل الواجهات مع البيانات الفعلية

في هذا الملف سأشرح كيفية تعديل الـ Blade Views لاستخدام البيانات من Database بدلاً من البيانات الثابتة.

---

## 1️⃣ تحديث `index.blade.php` (قائمة المستودعات)

### التغييرات المطلوبة:

#### قبل (بيانات ثابتة):
```blade
@foreach($warehouses as $warehouse)
    <tr>
        <td>1</td>
        <td>
            <div class="um-course-info">
                المستودع الرئيسي
            </div>
        </td>
        <td>WH-001</td>
        <td>القاهرة، المنطقة الصناعية</td>
        <td>أحمد محمد</td>
        <td>
            <span class="um-badge um-badge-success">نشط</span>
        </td>
    </tr>
@endforeach
```

#### بعد (بيانات فعلية):
```blade
@forelse($warehouses as $warehouse)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>
            <div class="um-course-info">
                {{ $warehouse->warehouse_name }}
            </div>
        </td>
        <td>{{ $warehouse->warehouse_code }}</td>
        <td>{{ $warehouse->location ?? 'بدون موقع' }}</td>
        <td>{{ $warehouse->manager_name ?? 'بدون مسؤول' }}</td>
        <td>
            <span class="um-badge {{ $warehouse->is_active ? 'um-badge-success' : 'um-badge-danger' }}">
                {{ $warehouse->is_active ? 'نشط' : 'غير نشط' }}
            </span>
        </td>
        <td>
            <div class="um-dropdown">
                <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                    <i class="feather icon-more-vertical"></i>
                </button>
                <div class="um-dropdown-menu">
                    <a href="{{ route('manufacturing.warehouses.show', $warehouse->id) }}" class="um-dropdown-item">
                        <i class="feather icon-eye"></i> عرض
                    </a>
                    <a href="{{ route('manufacturing.warehouses.edit', $warehouse->id) }}" class="um-dropdown-item">
                        <i class="feather icon-edit"></i> تعديل
                    </a>
                    <form action="{{ route('manufacturing.warehouses.destroy', $warehouse->id) }}" method="POST" class="um-dropdown-item">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="um-dropdown-item delete-form" onclick="return confirm('هل أنت متأكد؟')">
                            <i class="feather icon-trash-2"></i> حذف
                        </button>
                    </form>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center">لا توجد مستودعات</td>
    </tr>
@endforelse
```

### إضافة الترقيم:

```blade
<!-- قبل الجدول -->
@if($warehouses->isNotEmpty())
    <div class="um-pagination-section">
        <div>
            <p class="um-pagination-info">
                عرض {{ ($warehouses->currentPage() - 1) * $warehouses->perPage() + 1 }} 
                إلى 
                {{ min($warehouses->currentPage() * $warehouses->perPage(), $warehouses->total()) }} 
                من أصل {{ $warehouses->total() }} مستودع
            </p>
        </div>
        <div>
            {{ $warehouses->render() }}
        </div>
    </div>
@endif
```

---

## 2️⃣ تحديث `create.blade.php` (إضافة مستودع)

### التغييرات:

```blade
<!-- اختيار مسؤول من القائمة -->
<div class="form-group">
    <label for="manager_id" class="form-label">المسؤول</label>
    <div class="input-wrapper">
        <select name="manager_id" id="manager_id" class="form-input">
            <option value="">-- اختر مسؤول --</option>
            @foreach($managers as $manager)
                <option value="{{ $manager->id }}">
                    {{ $manager->name }}
                </option>
            @endforeach
        </select>
    </div>
    @error('manager_id')
        <span class="error-message">{{ $message }}</span>
    @enderror
</div>

<!-- ملء البيانات من old() -->
<div class="form-group">
    <label for="name" class="form-label">
        اسم المستودع
        <span class="required">*</span>
    </label>
    <div class="input-wrapper">
        <input type="text" name="name" id="name" 
               class="form-input @error('name') is-invalid @enderror" 
               value="{{ old('name') }}" 
               required 
               placeholder="أدخل اسم المستودع">
    </div>
    @error('name')
        <span class="error-message">{{ $message }}</span>
    @enderror
</div>

<!-- باقي الحقول -->
<div class="form-group">
    <label for="code" class="form-label">
        رمز المستودع
        <span class="required">*</span>
    </label>
    <div class="input-wrapper">
        <input type="text" name="code" id="code" 
               class="form-input @error('code') is-invalid @enderror" 
               value="{{ old('code') }}" 
               required 
               placeholder="مثال: WH-001">
    </div>
    @error('code')
        <span class="error-message">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="location" class="form-label">الموقع</label>
    <div class="input-wrapper">
        <input type="text" name="location" id="location" 
               class="form-input @error('location') is-invalid @enderror" 
               value="{{ old('location') }}" 
               placeholder="أدخل الموقع">
    </div>
    @error('location')
        <span class="error-message">{{ $message }}</span>
    @enderror
</div>

<div class="form-group full-width">
    <label for="description" class="form-label">الوصف</label>
    <div class="input-wrapper">
        <textarea name="description" id="description" 
                  class="form-input @error('description') is-invalid @enderror" 
                  placeholder="أدخل وصف المستودع">{{ old('description') }}</textarea>
    </div>
    @error('description')
        <span class="error-message">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="capacity" class="form-label">السعة التخزينية (متر مكعب)</label>
    <div class="input-wrapper">
        <input type="number" name="capacity" id="capacity" 
               class="form-input @error('capacity') is-invalid @enderror" 
               value="{{ old('capacity') }}" 
               placeholder="أدخل السعة" 
               step="0.01">
    </div>
    @error('capacity')
        <span class="error-message">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="status" class="form-label">الحالة</label>
    <div class="input-wrapper">
        <select name="status" id="status" class="form-input @error('status') is-invalid @enderror" required>
            <option value="">-- اختر الحالة --</option>
            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>نشط</option>
            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
        </select>
    </div>
    @error('status')
        <span class="error-message">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="phone" class="form-label">رقم الهاتف</label>
    <div class="input-wrapper">
        <input type="tel" name="phone" id="phone" 
               class="form-input @error('phone') is-invalid @enderror" 
               value="{{ old('phone') }}" 
               placeholder="أدخل رقم الهاتف">
    </div>
    @error('phone')
        <span class="error-message">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="email" class="form-label">البريد الإلكتروني</label>
    <div class="input-wrapper">
        <input type="email" name="email" id="email" 
               class="form-input @error('email') is-invalid @enderror" 
               value="{{ old('email') }}" 
               placeholder="أدخل البريد الإلكتروني">
    </div>
    @error('email')
        <span class="error-message">{{ $message }}</span>
    @enderror
</div>
```

---

## 3️⃣ تحديث `edit.blade.php` (تعديل مستودع)

### التغييرات (نفس create.blade.php مع إضافة):

```blade
<!-- Route للتعديل بدلاً من الإضافة -->
<form method="POST" action="{{ route('manufacturing.warehouses.update', $warehouse->id) }}" id="warehouseForm">
    @csrf
    @method('PUT')
    
    <!-- ملء البيانات من الكائن بدلاً من old() -->
    <input type="text" name="name" value="{{ old('name', $warehouse->warehouse_name) }}" required>
    <input type="text" name="code" value="{{ old('code', $warehouse->warehouse_code) }}" required>
    <input type="text" name="location" value="{{ old('location', $warehouse->location) }}">
    <textarea name="description">{{ old('description', $warehouse->description) }}</textarea>
    <input type="number" name="capacity" value="{{ old('capacity', $warehouse->capacity) }}" step="0.01">
    
    <select name="status" required>
        <option value="active" {{ old('status', $warehouse->is_active ? 'active' : 'inactive') === 'active' ? 'selected' : '' }}>نشط</option>
        <option value="inactive" {{ old('status', $warehouse->is_active ? 'active' : 'inactive') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
    </select>
    
    <input type="tel" name="phone" value="{{ old('phone', $warehouse->contact_number) }}">
    <input type="email" name="email" value="{{ old('email') }}">
    
    <!-- اختيار مسؤول -->
    <select name="manager_id">
        <option value="">-- اختر مسؤول --</option>
        @foreach($managers as $manager)
            <option value="{{ $manager->id }}" 
                    {{ old('manager_id', $warehouse->manager_name) == $manager->id ? 'selected' : '' }}>
                {{ $manager->name }}
            </option>
        @endforeach
    </select>
</form>
```

---

## 4️⃣ تحديث `show.blade.php` (عرض التفاصيل)

```blade
<!-- Route الصحيح -->
<a href="{{ route('manufacturing.warehouses.edit', $warehouse->id) }}" class="btn btn-edit">تعديل</a>

<!-- البيانات الفعلية -->
<div class="info-value">{{ $warehouse->warehouse_code }}</div>
<div class="info-value">{{ $warehouse->description }}</div>
<div class="info-value">{{ $warehouse->location }}</div>
<div class="info-value">{{ $warehouse->capacity }} متر مكعب</div>
<div class="info-value">{{ $warehouse->is_active ? 'نشط' : 'غير نشط' }}</div>

<!-- معلومات الاتصال -->
<div class="info-value">{{ $warehouse->contact_number }}</div>
<div class="info-value">{{ $warehouse->email ?? 'بدون بريد' }}</div>
<div class="info-value">{{ $warehouse->manager_name ?? 'بدون مسؤول' }}</div>

<!-- التواريخ -->
<div class="info-value">{{ $warehouse->created_at->format('Y-m-d') }}</div>
<div class="info-value">{{ $warehouse->updated_at->format('Y-m-d') }}</div>

<!-- حذف -->
<form action="{{ route('manufacturing.warehouses.destroy', $warehouse->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="action-btn delete" onclick="return confirm('هل أنت متأكد من الحذف؟')">
        <div class="action-text">
            <span>حذف المستودع</span>
        </div>
    </button>
</form>
```

---

## ✅ Checklist التحديث

- [ ] تحديث `index.blade.php` مع البيانات الفعلية
- [ ] تحديث `create.blade.php` مع `old()` و error messages
- [ ] تحديث `edit.blade.php` مع بيانات الكائن
- [ ] تحديث `show.blade.php` مع جميع الحقول
- [ ] تحديث جميع الـ Routes
- [ ] اختبار الإضافة
- [ ] اختبار التعديل
- [ ] اختبار الحذف
- [ ] اختبار البحث والتصفية
- [ ] اختبار الترقيم

---

## 🚀 الآن تم كل شيء!

بعد تحديث الـ Views ستكون لديك:
- ✅ نظام Backend كامل
- ✅ نظام Frontend متصل مع البيانات الفعلية
- ✅ البحث والتصفية يعملان
- ✅ الأخطاء تظهر بشكل صحيح
- ✅ النجاح يظهر برسائل مناسبة

**استمتع بنظامك الجديد!** 🎉
