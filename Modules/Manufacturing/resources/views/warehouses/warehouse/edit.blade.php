@extends('master')

@section('title', __('warehouse.edit_warehouse'))

@section('content')

        <!-- Header -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                {{ __('warehouse.edit_warehouse') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('warehouse.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('warehouse.warehouse') }}</span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('warehouse.edit_warehouse') }}</span>
            </nav>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <form method="POST" action="{{ route('manufacturing.warehouses.update', $warehouse->id) }}" id="warehouseForm">
                @csrf
                @method('PUT')

                <!-- Warehouse Information Section - SIMPLIFIED -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon personal">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('warehouse.warehouse_information') }}</h3>
                            <p class="section-subtitle">{{ __('warehouse.update_warehouse_data') }}</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <!-- Name Arabic - REQUIRED -->
                        <div class="form-group">
                            <label for="name" class="form-label">
                                {{ __('warehouse.warehouse_name') }}
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                                <input type="text" name="name" id="name" class="form-input"
                                    value="{{ old('name', $warehouse->name ?? $warehouse->warehouse_name) }}"
                                    placeholder="{{ __('warehouse.example') }}: المستودع الرئيسي" required>
                            </div>
                            <div class="error-message" id="name-error" style="display: none;"></div>
                        </div>

                        <!-- Code - Read Only -->
                        <div class="form-group">
                            <label for="code" class="form-label">
                                {{ __('warehouse.warehouse_code') }}
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                    <line x1="8" y1="21" x2="16" y2="21"></line>
                                    <line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                                <input type="text" name="code" id="code" class="form-input"
                                    value="{{ old('code', $warehouse->code ?? $warehouse->warehouse_code) }}" readonly>
                            </div>
                            <div class="error-message" id="code-error" style="display: none;"></div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="is_active" class="form-label">{{ __('warehouse.status') }}</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                <select name="is_active" id="is_active" class="form-input">
                                    <option value="1" {{ old('is_active', $warehouse->is_active) == 1 ? 'selected' : '' }}>
                                        {{ __('warehouse.active') }}
                                    </option>
                                    <option value="0" {{ old('is_active', $warehouse->is_active) == 0 ? 'selected' : '' }}>
                                        {{ __('warehouse.inactive') }}
                                    </option>
                                </select>
                            </div>
                            <div class="error-message" id="is_active-error" style="display: none;"></div>
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
                    <a href="{{ route('manufacturing.warehouses.index') }}" class="btn-cancel">
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
            const form = document.getElementById('warehouseForm');
            const inputs = form.querySelectorAll('.form-input');

            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        showError(this.id, '{{ __("warehouse.required_field") }}');
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
                        showError(field.id, '{{ __("warehouse.required_field") }}');
                        isValid = false;
                    }
                });

                // If form is valid, submit it
                if (isValid) {
                    // Show SweetAlert2 confirmation
                    Swal.fire({
                        title: '{{ __("warehouse.confirm_save") }}',
                        text: '{{ __("warehouse.confirm_save_changes") }}',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '{{ __("warehouse.yes_save") }}',
                        cancelButtonText: '{{ __("warehouse.cancel") }}',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submit the form
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
