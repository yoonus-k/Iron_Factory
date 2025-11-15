@extends('master')

@section('title', 'إضافة وحدة جديدة')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-plus-circle"></i>
                    </div>
                    <div class="header-info">
                        <h1>إضافة وحدة جديدة</h1>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.warehouse-settings.units.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        رجوع
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('manufacturing.warehouse-settings.units.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- معلومات الوحدة الأساسية -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">معلومات الوحدة</h3>
                        <p class="section-subtitle">أدخل بيانات الوحدة الجديدة</p>
                    </div>
                </div>

                <div class="form-grid">
                    <!-- رمز الوحدة -->
                    <div class="form-group">
                        <label class="form-label">رمز الوحدة <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="unit_code" class="form-input @error('unit_code') error @enderror"
                                   placeholder="مثال: KG" value="{{ old('unit_code') }}" required>
                        </div>
                        @error('unit_code')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- اسم الوحدة -->
                    <div class="form-group">
                        <label class="form-label">اسم الوحدة (عربي) <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="unit_name" class="form-input @error('unit_name') error @enderror"
                                   placeholder="مثال: كيلوغرام" value="{{ old('unit_name') }}" required>
                        </div>
                        @error('unit_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- اسم الوحدة الإنجليزي -->
                    <div class="form-group">
                        <label class="form-label">اسم الوحدة (إنجليزي)</label>
                        <div class="input-wrapper">
                            <input type="text" name="unit_name_en" class="form-input @error('unit_name_en') error @enderror"
                                   placeholder="مثال: Kilogram" value="{{ old('unit_name_en') }}">
                        </div>
                        @error('unit_name_en')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- اختصار الوحدة -->
                    <div class="form-group">
                        <label class="form-label">الاختصار <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="unit_symbol" class="form-input @error('unit_symbol') error @enderror"
                                   placeholder="مثال: كغ" value="{{ old('unit_symbol') }}" required maxlength="10">
                        </div>
                        @error('unit_symbol')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- نوع الوحدة -->
                    <div class="form-group">
                        <label class="form-label">نوع الوحدة <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <select name="unit_type" class="form-input @error('unit_type') error @enderror" required>
                                <option value="">-- اختر النوع --</option>
                                <option value="weight" {{ old('unit_type') == 'weight' ? 'selected' : '' }}>الوزن</option>
                                <option value="length" {{ old('unit_type') == 'length' ? 'selected' : '' }}>الطول</option>
                                <option value="volume" {{ old('unit_type') == 'volume' ? 'selected' : '' }}>الحجم</option>
                                <option value="area" {{ old('unit_type') == 'area' ? 'selected' : '' }}>المساحة</option>
                                <option value="quantity" {{ old('unit_type') == 'quantity' ? 'selected' : '' }}>الكمية</option>
                                <option value="time" {{ old('unit_type') == 'time' ? 'selected' : '' }}>الوقت</option>
                                <option value="temperature" {{ old('unit_type') == 'temperature' ? 'selected' : '' }}>درجة الحرارة</option>
                                <option value="other" {{ old('unit_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                        </div>
                        @error('unit_type')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- معامل التحويل -->
                    <div class="form-group">
                        <label class="form-label">معامل التحويل</label>
                        <div class="input-wrapper">
                            <input type="number" name="conversion_factor" class="form-input @error('conversion_factor') error @enderror"
                                   placeholder="مثال: 1000" value="{{ old('conversion_factor') }}" step="0.01">
                        </div>
                        @error('conversion_factor')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- الوصف -->
                    <div class="form-group full-width">
                        <label class="form-label">الوصف</label>
                        <div class="input-wrapper">
                            <textarea name="description" class="form-input @error('description') error @enderror"
                                      placeholder="أدخل وصف الوحدة" rows="3">{{ old('description') }}</textarea>
                        </div>
                        @error('description')
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
                    حفظ الوحدة
                </button>
                <a href="{{ route('manufacturing.warehouse-settings.units.index') }}" class="btn btn-secondary">
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
