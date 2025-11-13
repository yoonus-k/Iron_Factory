@extends('master')

@section('title', 'إضافة نوع مادة جديد')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-plus"></i>
                    </div>
                    <div class="header-info">
                        <h1>إضافة نوع مادة جديد</h1>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.warehouse-settings.material-types.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        رجوع
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('manufacturing.warehouse-settings.material-types.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- معلومات النوع الأساسية -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">معلومات النوع الأساسية</h3>
                        <p class="section-subtitle">أدخل بيانات نوع المادة الجديد</p>
                    </div>
                </div>

                <div class="form-grid">
                    <!-- رمز النوع -->
                    <div class="form-group">
                        <label class="form-label">رمز النوع <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="type_code" class="form-input @error('type_code') error @enderror"
                                   placeholder="مثال: RM001" value="{{ old('type_code') }}" required>
                        </div>
                        @error('type_code')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- اسم النوع -->
                    <div class="form-group">
                        <label class="form-label">اسم النوع (عربي) <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="type_name" class="form-input @error('type_name') error @enderror"
                                   placeholder="مثال: حديد خام" value="{{ old('type_name') }}" required>
                        </div>
                        @error('type_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- اسم النوع الإنجليزي -->
                    <div class="form-group">
                        <label class="form-label">اسم النوع (إنجليزي)</label>
                        <div class="input-wrapper">
                            <input type="text" name="type_name_en" class="form-input @error('type_name_en') error @enderror"
                                   placeholder="مثال: Raw Iron" value="{{ old('type_name_en') }}">
                        </div>
                        @error('type_name_en')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- الفئة -->
                    <div class="form-group">
                        <label class="form-label">الفئة <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <select name="category" class="form-input @error('category') error @enderror" required>
                                <option value="">-- اختر الفئة --</option>
                                <option value="raw_material" {{ old('category') == 'raw_material' ? 'selected' : '' }}>مادة خام</option>
                                <option value="finished_product" {{ old('category') == 'finished_product' ? 'selected' : '' }}>منتج نهائي</option>
                                <option value="semi_finished" {{ old('category') == 'semi_finished' ? 'selected' : '' }}>منتج شبه مكتمل</option>
                                <option value="additive" {{ old('category') == 'additive' ? 'selected' : '' }}>مادة مضافة</option>
                                <option value="packing_material" {{ old('category') == 'packing_material' ? 'selected' : '' }}>مادة تغليف</option>
                                <option value="component" {{ old('category') == 'component' ? 'selected' : '' }}>مكون</option>
                            </select>
                        </div>
                        @error('category')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- الوحدة الافتراضية -->
                    <div class="form-group">
                        <label class="form-label">الوحدة الافتراضية</label>
                        <div class="input-wrapper">
                            <select name="default_unit" class="form-input @error('default_unit') error @enderror">
                                <option value="">-- اختر وحدة --</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('default_unit') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->unit_name }} ({{ $unit->unit_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('default_unit')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- التكلفة القياسية -->
                    <div class="form-group">
                        <label class="form-label">التكلفة القياسية</label>
                        <div class="input-wrapper">
                            <input type="number" name="standard_cost" class="form-input @error('standard_cost') error @enderror"
                                   placeholder="0.00" value="{{ old('standard_cost') }}" step="0.01" min="0">
                        </div>
                        @error('standard_cost')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- مدة الصلاحية (أيام) -->
                    <div class="form-group">
                        <label class="form-label">مدة الصلاحية (أيام)</label>
                        <div class="input-wrapper">
                            <input type="number" name="shelf_life_days" class="form-input @error('shelf_life_days') error @enderror"
                                   placeholder="365" value="{{ old('shelf_life_days') }}" min="0">
                        </div>
                        @error('shelf_life_days')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- الوصف -->
                    <div class="form-group full-width">
                        <label class="form-label">الوصف</label>
                        <div class="input-wrapper">
                            <textarea name="description" class="form-input @error('description') error @enderror"
                                      placeholder="أدخل وصف النوع" rows="3" maxlength="1000">{{ old('description') }}</textarea>
                        </div>
                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- الوصف الإنجليزي -->
                    <div class="form-group full-width">
                        <label class="form-label">الوصف (إنجليزي)</label>
                        <div class="input-wrapper">
                            <textarea name="description_en" class="form-input @error('description_en') error @enderror"
                                      placeholder="Enter description in English" rows="3" maxlength="1000">{{ old('description_en') }}</textarea>
                        </div>
                        @error('description_en')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- شروط التخزين -->
                    <div class="form-group full-width">
                        <label class="form-label">شروط التخزين</label>
                        <div class="input-wrapper">
                            <textarea name="storage_conditions" class="form-input @error('storage_conditions') error @enderror"
                                      placeholder="مثال: تخزين بدرجة حرارة معتدلة بعيداً عن الرطوبة" rows="2">{{ old('storage_conditions') }}</textarea>
                        </div>
                        @error('storage_conditions')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- شروط التخزين الإنجليزية -->
                    <div class="form-group full-width">
                        <label class="form-label">شروط التخزين (إنجليزي)</label>
                        <div class="input-wrapper">
                            <textarea name="storage_conditions_en" class="form-input @error('storage_conditions_en') error @enderror"
                                      placeholder="Store at moderate temperature away from moisture" rows="2">{{ old('storage_conditions_en') }}</textarea>
                        </div>
                        @error('storage_conditions_en')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- الحالة -->
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                            نشط
                        </label>
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    حفظ النوع
                </button>
                <a href="{{ route('manufacturing.warehouse-settings.material-types.index') }}" class="btn btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    إلغاء
                </a>
            </div>
        </form>
    </div>

@endsection
