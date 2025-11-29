@extends('master')

@section('title', __('warehouse.edit_unit'))

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            {{ __('warehouse.edit_unit') }}
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> {{ __('warehouse.dashboard') }}
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('warehouse.units') }}</span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('warehouse.edit_unit') }}</span>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.warehouse-settings.units.update', $unit->id) }}" id="unitForm">
            @csrf
            @method('PUT')

            <!-- معلومات الوحدة الأساسية -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">{{ __('warehouse.unit_information') }}</h3>
                        <p class="section-subtitle">{{ __('warehouse.edit_unit_data') }}</p>
                    </div>
                </div>

                <div class="form-grid">
                    <!-- رمز الوحدة -->
                    <div class="form-group">
                        <label for="unit_code" class="form-label">
                            {{ __('warehouse.unit_code') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18"></path>
                                <path d="M3 12h18"></path>
                                <path d="M3 18h18"></path>
                            </svg>
                            <input type="text" name="unit_code" id="unit_code" class="form-input @error('unit_code') error @enderror"
                                   placeholder="{{ __('warehouse.example') }}: KG" value="{{ old('unit_code', $unit->unit_code) }}" required>
                        </div>
                        @error('unit_code')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                        <div class="error-message" id="unit_code-error" style="display: none;"></div>
                    </div>

                    <!-- اسم الوحدة -->
                    <div class="form-group">
                        <label for="unit_name" class="form-label">
                            {{ __('warehouse.unit_name_ar') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16v16H4z"></path>
                                <line x1="4" y1="8" x2="20" y2="8"></line>
                            </svg>
                            <input type="text" name="unit_name" id="unit_name" class="form-input @error('unit_name') error @enderror"
                                   placeholder="{{ __('warehouse.example') }}: كيلوغرام" value="{{ old('unit_name', $unit->unit_name) }}" required>
                        </div>
                        @error('unit_name')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                        <div class="error-message" id="unit_name-error" style="display: none;"></div>
                    </div>

                    <!-- اسم الوحدة الإنجليزي -->
                    <div class="form-group">
                        <label for="unit_name_en" class="form-label">{{ __('warehouse.unit_name_en') }}</label>
                        <div class="input-wrapper">
                            <input type="text" name="unit_name_en" id="unit_name_en" class="form-input @error('unit_name_en') error @enderror"
                                   placeholder="{{ __('warehouse.example') }}: Kilogram" value="{{ old('unit_name_en', $unit->unit_name_en) }}">
                        </div>
                        @error('unit_name_en')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- اختصار الوحدة -->
                    <div class="form-group">
                        <label for="unit_symbol" class="form-label">
                            {{ __('warehouse.unit_symbol') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <input type="text" name="unit_symbol" id="unit_symbol" class="form-input @error('unit_symbol') error @enderror"
                                   placeholder="{{ __('warehouse.example') }}: كغ" value="{{ old('unit_symbol', $unit->unit_symbol) }}" required maxlength="10">
                        </div>
                        @error('unit_symbol')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                        <div class="error-message" id="unit_symbol-error" style="display: none;"></div>
                    </div>

                    <!-- نوع الوحدة -->
                    <div class="form-group">
                        <label for="unit_type" class="form-label">
                            {{ __('warehouse.unit_type') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <select name="unit_type" id="unit_type" class="form-input @error('unit_type') error @enderror" required>
                                <option value="">-- {{ __('warehouse.choose_type') }} --</option>
                                <option value="weight" {{ old('unit_type', $unit->unit_type) == 'weight' ? 'selected' : '' }}>{{ __('warehouse.weight') }}</option>
                                <option value="length" {{ old('unit_type', $unit->unit_type) == 'length' ? 'selected' : '' }}>{{ __('warehouse.length') }}</option>
                                <option value="volume" {{ old('unit_type', $unit->unit_type) == 'volume' ? 'selected' : '' }}>{{ __('warehouse.volume') }}</option>
                                <option value="area" {{ old('unit_type', $unit->unit_type) == 'area' ? 'selected' : '' }}>{{ __('warehouse.area') }}</option>
                                <option value="quantity" {{ old('unit_type', $unit->unit_type) == 'quantity' ? 'selected' : '' }}>{{ __('warehouse.quantity') }}</option>
                                <option value="time" {{ old('unit_type', $unit->unit_type) == 'time' ? 'selected' : '' }}>{{ __('warehouse.time') }}</option>
                                <option value="temperature" {{ old('unit_type', $unit->unit_type) == 'temperature' ? 'selected' : '' }}>{{ __('warehouse.temperature') }}</option>
                                <option value="other" {{ old('unit_type', $unit->unit_type) == 'other' ? 'selected' : '' }}>{{ __('warehouse.other') }}</option>
                            </select>
                        </div>
                        @error('unit_type')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                        <div class="error-message" id="unit_type-error" style="display: none;"></div>
                    </div>



                    <!-- الحالة -->
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $unit->is_active) ? 'checked' : '' }}>
                            نشط
                        </label>
                    </div>
                </div>
            </div>

            <!-- الوصف -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon description">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">الوصف</h3>
                        <p class="section-subtitle">تعديل وصف الوحدة</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="description" class="form-label">الوصف (عربي)</label>
                        <div class="input-wrapper">
                            <textarea name="description" id="description" class="form-input @error('description') error @enderror"
                                      placeholder="أدخل وصف الوحدة" rows="3">{{ old('description', $unit->description) }}</textarea>
                        </div>
                        @error('description')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label for="description_en" class="form-label">الوصف (إنجليزي)</label>
                        <div class="input-wrapper">
                            <textarea name="description_en" id="description_en" class="form-input @error('description_en') error @enderror"
                                      placeholder="Enter unit description in English" rows="3">{{ old('description_en', $unit->description_en) }}</textarea>
                        </div>
                        @error('description_en')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    حفظ التعديلات
                </button>
                <a href="{{ route('manufacturing.warehouse-settings.units.index') }}" class="btn-cancel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('unitForm');
            const inputs = form.querySelectorAll('.form-input');

            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        showError(this.id, 'هذا الحقل مطلوب');
                    } else {
                        hideError(this.id);
                    }
                });

                input.addEventListener('input', function() {
                    hideError(this.id);
                });
            });

            // Form submission handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Reset all errors
                clearAllErrors();

                // Validate required fields
                let isValid = true;
                const requiredFields = form.querySelectorAll('[required]');

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        showError(field.id, 'هذا الحقل مطلوب');
                        isValid = false;
                    }
                });

                // If form is valid, submit it
                if (isValid) {
                    // Show SweetAlert2 confirmation
                    Swal.fire({
                        title: 'تأكيد الحفظ',
                        text: 'هل أنت متأكد من حفظ التعديلات على الوحدة؟',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، احفظ',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    // Scroll to first error
                    const firstError = form.querySelector('.error-message:not([style*="display: none"])');
                    if (firstError) {
                        firstError.previousElementSibling.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });
        });

        function showError(fieldId, message) {
            const errorElement = document.getElementById(fieldId + '-error');
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
        }

        function hideError(fieldId) {
            const errorElement = document.getElementById(fieldId + '-error');
            if (errorElement) {
                errorElement.style.display = 'none';
            }
        }

        function clearAllErrors() {
            const errorElements = document.querySelectorAll('.error-message');
            errorElements.forEach(element => {
                element.style.display = 'none';
            });
        }
    </script>
@endsection
