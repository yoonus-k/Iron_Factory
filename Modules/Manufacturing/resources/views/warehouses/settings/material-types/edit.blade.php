@extends('master')

@section('title', __('warehouse.edit_material_type'))

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            {{ __('warehouse.edit_material_type') }}
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> {{ __('warehouse.dashboard') }}
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('warehouse.material_types') }}</span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('warehouse.edit_material_type') }}</span>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.warehouse-settings.material-types.update', $materialType->id) }}" id="materialTypeForm">
            @csrf
            @method('PUT')

            <!-- معلومات النوع الأساسية -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">{{ __('warehouse.material_type_information') }}</h3>
                        <p class="section-subtitle">{{ __('warehouse.edit_material_type_data') }}</p>
                    </div>
                </div>

                <div class="form-grid">
                    <!-- رمز النوع -->
                    <div class="form-group">
                        <label for="type_code" class="form-label">
                            {{ __('warehouse.type_code') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18"></path>
                                <path d="M3 12h18"></path>
                                <path d="M3 18h18"></path>
                            </svg>
                            <input type="text" name="type_code" id="type_code" class="form-input @error('type_code') error @enderror"
                                   placeholder="{{ __('warehouse.example') }}: RM001" value="{{ old('type_code', $materialType->type_code) }}" required>
                        </div>
                        @error('type_code')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                        <div class="error-message" id="type_code-error" style="display: none;"></div>
                    </div>

                    <!-- اسم النوع -->
                    <div class="form-group">
                        <label for="type_name" class="form-label">
                            {{ __('warehouse.type_name_ar') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16v16H4z"></path>
                                <line x1="4" y1="8" x2="20" y2="8"></line>
                            </svg>
                            <input type="text" name="type_name" id="type_name" class="form-input @error('type_name') error @enderror"
                                   placeholder="{{ __('warehouse.example') }}: {{ __('warehouse.raw_material_example') }}" value="{{ old('type_name', $materialType->type_name) }}" required>
                        </div>
                        @error('type_name')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                        <div class="error-message" id="type_name-error" style="display: none;"></div>
                    </div>

                    <!-- اسم النوع الإنجليزي -->
                    <div class="form-group">
                        <label for="type_name_en" class="form-label">{{ __('warehouse.type_name_en') }}</label>
                        <div class="input-wrapper">
                            <input type="text" name="type_name_en" id="type_name_en" class="form-input @error('type_name_en') error @enderror"
                                   placeholder="{{ __('warehouse.example') }}: {{ __('warehouse.raw_material_example_en') }}" value="{{ old('type_name_en', $materialType->type_name_en) }}">
                        </div>
                        @error('type_name_en')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- الفئة -->
                    <div class="form-group">
                        <label for="category" class="form-label">
                            {{ __('warehouse.category') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <select name="category" id="category" class="form-input @error('category') error @enderror" required>
                                <option value="">{{ __('warehouse.select_category') }}</option>
                                <option value="raw_material" {{ old('category', $materialType->category) == 'raw_material' ? 'selected' : '' }}>{{ __('warehouse.raw_material_category') }}</option>
                                <option value="finished_product" {{ old('category', $materialType->category) == 'finished_product' ? 'selected' : '' }}>{{ __('warehouse.finished_product_category') }}</option>
                                <option value="semi_finished" {{ old('category', $materialType->category) == 'semi_finished' ? 'selected' : '' }}>{{ __('warehouse.semi_finished_category') }}</option>
                                <option value="additive" {{ old('category', $materialType->category) == 'additive' ? 'selected' : '' }}>{{ __('warehouse.additive_category') }}</option>
                                <option value="packing_material" {{ old('category', $materialType->category) == 'packing_material' ? 'selected' : '' }}>{{ __('warehouse.packing_material_category') }}</option>
                                <option value="component" {{ old('category', $materialType->category) == 'component' ? 'selected' : '' }}>{{ __('warehouse.component_category') }}</option>
                            </select>
                        </div>
                        @error('category')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                        <div class="error-message" id="category-error" style="display: none;"></div>
                    </div>

                    <!-- الوحدة الافتراضية -->
                    <div class="form-group">
                        <label for="default_unit" class="form-label">{{ __('warehouse.default_unit') }}</label>
                        <div class="input-wrapper">
                            <select name="default_unit" id="default_unit" class="form-input @error('default_unit') error @enderror">
                                <option value="">{{ __('warehouse.select_unit') }}</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('default_unit', $materialType->default_unit) == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->unit_name }} ({{ $unit->unit_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('default_unit')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- التكلفة القياسية -->
                    <div class="form-group">
                        <label for="standard_cost" class="form-label">{{ __('warehouse.standard_cost') }}</label>
                        <div class="input-wrapper">
                            <input type="number" name="standard_cost" id="standard_cost" class="form-input @error('standard_cost') error @enderror"
                                   placeholder="0.00" value="{{ old('standard_cost', $materialType->standard_cost) }}" step="0.01" min="0">
                        </div>
                        @error('standard_cost')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- مدة الصلاحية (أيام) -->
                    <div class="form-group">
                        <label for="shelf_life_days" class="form-label">{{ __('warehouse.shelf_life_days') }}</label>
                        <div class="input-wrapper">
                            <input type="number" name="shelf_life_days" id="shelf_life_days" class="form-input @error('shelf_life_days') error @enderror"
                                   placeholder="365" value="{{ old('shelf_life_days', $materialType->shelf_life_days) }}" min="0">
                        </div>
                        @error('shelf_life_days')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- الحالة -->
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $materialType->is_active) ? 'checked' : '' }}>
                            {{ __('warehouse.active') }}
                        </label>
                    </div>
                </div>
            </div>

            <!-- الوصف وشروط التخزين -->
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
                        <h3 class="section-title">{{ __('warehouse.description_and_storage') }}</h3>
                        <p class="section-subtitle">{{ __('warehouse.edit_description_and_storage') }}</p>
                    </div>
                </div>

                <div class="form-grid">
                    <!-- الوصف -->
                    <div class="form-group full-width">
                        <label for="description" class="form-label">{{ __('warehouse.description_ar') }}</label>
                        <div class="input-wrapper">
                            <textarea name="description" id="description" class="form-input @error('description') error @enderror"
                                      placeholder="{{ __('warehouse.enter_description') }}" rows="3" maxlength="1000">{{ old('description', $materialType->description) }}</textarea>
                        </div>
                        @error('description')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- الوصف الإنجليزي -->
                    <div class="form-group full-width">
                        <label for="description_en" class="form-label">{{ __('warehouse.description_en') }}</label>
                        <div class="input-wrapper">
                            <textarea name="description_en" id="description_en" class="form-input @error('description_en') error @enderror"
                                      placeholder="{{ __('warehouse.enter_description_en') }}" rows="3" maxlength="1000">{{ old('description_en', $materialType->description_en) }}</textarea>
                        </div>
                        @error('description_en')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- شروط التخزين -->
                    <div class="form-group full-width">
                        <label for="storage_conditions" class="form-label">{{ __('warehouse.storage_conditions_ar') }}</label>
                        <div class="input-wrapper">
                            <textarea name="storage_conditions" id="storage_conditions" class="form-input @error('storage_conditions') error @enderror"
                                      placeholder="{{ __('warehouse.storage_conditions_example') }}" rows="2">{{ old('storage_conditions', $materialType->storage_conditions) }}</textarea>
                        </div>
                        @error('storage_conditions')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- شروط التخزين الإنجليزية -->
                    <div class="form-group full-width">
                        <label for="storage_conditions_en" class="form-label">{{ __('warehouse.storage_conditions_en') }}</label>
                        <div class="input-wrapper">
                            <textarea name="storage_conditions_en" id="storage_conditions_en" class="form-input @error('storage_conditions_en') error @enderror"
                                      placeholder="{{ __('warehouse.storage_conditions_example_en') }}" rows="2">{{ old('storage_conditions_en', $materialType->storage_conditions_en) }}</textarea>
                        </div>
                        @error('storage_conditions_en')
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
                    {{ __('warehouse.save_changes') }}
                </button>
                <a href="{{ route('manufacturing.warehouse-settings.material-types.index') }}" class="btn-cancel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    {{ __('warehouse.cancel') }}
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('materialTypeForm');
            const inputs = form.querySelectorAll('.form-input');

            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        showError(this.id, '{{ __('warehouse.required_field') }}');
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
                        showError(field.id, '{{ __('warehouse.required_field') }}');
                        isValid = false;
                    }
                });

                // If form is valid, submit it
                if (isValid) {
                    // Show SweetAlert2 confirmation
                    Swal.fire({
                        title: '{{ __('warehouse.confirm_save') }}',
                        text: '{{ __('warehouse.confirm_save_material_type_changes') }}',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '{{ __('warehouse.yes_save') }}',
                        cancelButtonText: '{{ __('warehouse.cancel') }}',
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
