@extends('master')

@section('title', 'إضافة مادة جديدة')

@section('content')

        <!-- Header -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                إضافة مادة جديدة
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المواد</span>
                <i class="feather icon-chevron-left"></i>
                <span>إضافة مادة جديدة</span>
            </nav>
        </div>

        @if (session('success'))
            <div class="um-alert-custom um-alert-success" role="alert" id="successMessage">
                <i class="feather icon-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="um-alert-custom um-alert-error" role="alert" id="errorMessage">
                <i class="feather icon-alert-circle"></i>
                {{ session('error') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif
        <!-- Form Card -->
        <div class="form-card">
            <form method="POST" action="{{ route('manufacturing.warehouse-products.store') }}" id="materialForm" enctype="multipart/form-data">
                @csrf

                <!-- Material Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon personal">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">معلومات المادة</h3>
                            <p class="section-subtitle">أدخل البيانات الأساسية للمادة</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="barcode" class="form-label">
                                رمز المادة
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 6h18"></path>
                                    <path d="M3 12h18"></path>
                                    <path d="M3 18h18"></path>
                                </svg>
                                <input type="text" name="barcode" id="barcode" class="form-input"
                                       placeholder="أدخل رمز المادة" value="{{ old('barcode') }}" required readonly>
                            </div>
                            <div class="error-message" id="barcode-error" style="display: none;"></div>
                        </div>



                        <div class="form-group">
                            <label for="material_type" class="form-label">
                                نوع المادة (عربي)
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16v16H4z"></path>
                                    <line x1="4" y1="8" x2="20" y2="8"></line>
                                </svg>
                                <input type="text" name="material_type" id="material_type" class="form-input"
                                       placeholder="اسم المادة" value="{{ old('material_type') }}" required>
                            </div>
                            <div class="error-message" id="material_type-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="material_type_en" class="form-label">نوع المادة (إنجليزي)</label>
                            <div class="input-wrapper">
                                <input type="text" name="material_type_en" id="material_type_en" class="form-input"
                                       placeholder="Material Name in English" value="{{ old('material_type_en') }}">
                            </div>
                            <div class="error-message" id="material_type_en-error" style="display: none;"></div>
                        </div>




                        <div class="form-group">
                            <label for="original_weight" class="form-label">
                                الوزن الأصلي
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="number" name="original_weight" id="original_weight" class="form-input"
                                       placeholder="الوزن الأصلي" value="{{ old('original_weight') }}" step="0.01" required>
                            </div>
                            <div class="error-message" id="original_weight-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="unit_id" class="form-label">
                                الوحدة
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <select name="unit_id" id="unit_id" class="form-input" required>
                                    <option value="">-- اختر الوحدة --</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->unit_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-message" id="unit_id-error" style="display: none;"></div>
                        </div>



                        <div class="form-group">
                            <label for="manufacture_date" class="form-label">تاريخ الصنع</label>
                            <div class="input-wrapper">
                                <input type="date" name="manufacture_date" id="manufacture_date" class="form-input"
                                       value="{{ old('manufacture_date') }}">
                            </div>
                            <div class="error-message" id="manufacture_date-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="expiry_date" class="form-label">تاريخ الصلاحية</label>
                            <div class="input-wrapper">
                                <input type="date" name="expiry_date" id="expiry_date" class="form-input"
                                       value="{{ old('expiry_date') }}">
                            </div>
                            <div class="error-message" id="expiry_date-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="shelf_location" class="form-label">موقع التخزين (عربي)</label>
                            <div class="input-wrapper">
                                <input type="text" name="shelf_location" id="shelf_location" class="form-input"
                                       placeholder="الموقع" value="{{ old('shelf_location') }}">
                            </div>
                            <div class="error-message" id="shelf_location-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="shelf_location_en" class="form-label">موقع التخزين (إنجليزي)</label>
                            <div class="input-wrapper">
                                <input type="text" name="shelf_location_en" id="shelf_location_en" class="form-input"
                                       placeholder="Location" value="{{ old('shelf_location_en') }}">
                            </div>
                            <div class="error-message" id="shelf_location_en-error" style="display: none;"></div>
                        </div>

                        <!-- المستودع والحد الأدنى والأقصى للكمية -->
                        <div class="form-group">
                            <label for="warehouse_id" class="form-label">
                                المستودع
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                </svg>
                                <select name="warehouse_id" id="warehouse_id" class="form-input" required>
                                    <option value="">-- اختر المستودع --</option>
                                    @php
                                        $warehouses = \App\Models\Warehouse::all();
                                    @endphp
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-message" id="warehouse_id-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="min_quantity" class="form-label">الحد الأدنى للكمية</label>
                            <div class="input-wrapper">
                                <input type="number" name="min_quantity" id="min_quantity" class="form-input"
                                       placeholder="الحد الأدنى" value="{{ old('min_quantity', 0) }}" step="0.01" min="0">
                            </div>
                            <div class="error-message" id="min_quantity-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="max_quantity" class="form-label">الحد الأقصى للكمية</label>
                            <div class="input-wrapper">
                                <input type="number" name="max_quantity" id="max_quantity" class="form-input"
                                       placeholder="الحد الأقصى" value="{{ old('max_quantity', 999999) }}" step="0.01" min="0">
                            </div>
                            <div class="error-message" id="max_quantity-error" style="display: none;"></div>
                        </div>

                        <div class="form-group full-width">
                            <label for="notes" class="form-label">الملاحظات (عربي)</label>
                            <div class="input-wrapper">
                                <textarea name="notes" id="notes" class="form-input" rows="3"
                                          placeholder="ملاحظات حول المادة">{{ old('notes') }}</textarea>
                            </div>
                            <div class="error-message" id="notes-error" style="display: none;"></div>
                        </div>

                        <div class="form-group full-width">
                            <label for="notes_en" class="form-label">الملاحظات (إنجليزي)</label>
                            <div class="input-wrapper">
                                <textarea name="notes_en" id="notes_en" class="form-input" rows="3"
                                          placeholder="Notes in English">{{ old('notes_en') }}</textarea>
                            </div>
                            <div class="error-message" id="notes_en-error" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        حفظ المادة
                    </button>
                    <a href="{{ route('manufacturing.warehouse-products.index') }}" class="btn-cancel">
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
        // Generate material barcode
        function generateMaterialBarcode() {
            const prefix = 'MAT-';
            const date = new Date();
            const year = date.getFullYear().toString().substr(-2);
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
            return prefix + year + month + day + '-' + random;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Generate material barcode on page load
            const barcodeInput = document.getElementById('barcode');
            if (barcodeInput && !barcodeInput.value) {
                barcodeInput.value = generateMaterialBarcode();
            }

            const form = document.getElementById('materialForm');
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
                    // Check if SweetAlert2 is available
                    if (typeof Swal !== 'undefined') {
                        // Show SweetAlert2 confirmation
                        Swal.fire({
                            title: 'تأكيد الحفظ',
                            text: 'هل أنت متأكد من حفظ بيانات المادة؟',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'نعم، احفظ',
                            cancelButtonText: 'إلغاء',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Remove event listener to allow form submission
                                form.removeEventListener('submit', arguments.callee);
                                // Submit the form
                                form.submit();
                            }
                        });
                    } else {
                        // If SweetAlert2 is not available, submit directly
                        console.warn('SweetAlert2 not loaded, submitting form directly');
                        form.removeEventListener('submit', arguments.callee);
                        form.submit();
                    }
                } else {
                    // Scroll to first error
                    const firstError = form.querySelector('.error-message');
                    if (firstError) {
                        firstError.scrollIntoView({
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

    <style>
        /* Alert Styles */
        .alert-container {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .alert-danger {
            background: linear-gradient(135deg, #ff5252 0%, #ff1744 100%);
            border: 1px solid #ff5252;
            color: #fff;
        }

        .alert-success {
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            border: 1px solid #4caf50;
            color: #fff;
        }

        .alert-warning {
            background: linear-gradient(135deg, #ff9800 0%, #fb8c00 100%);
            border: 1px solid #ff9800;
            color: #fff;
        }

        .alert-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .alert-icon {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
        }

        .alert-title {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .alert-body {
            padding: 12px 16px;
        }

        .alert-description {
            margin: 0;
            font-size: 14px;
            opacity: 0.95;
        }

        .error-list {
            margin: 0;
            padding: 0;
        }

        .error-list li {
            font-size: 13px;
            line-height: 1.6;
        }

        .error-list li span {
            display: flex;
            align-items: center;
        }

        /* Close button for alerts */
        .alert-close {
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s;
            position: absolute;
            top: 12px;
            right: 12px;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .alert-close:hover {
            opacity: 1;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .alert-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .alert-title {
                font-size: 15px;
            }
        }
    </style>
@endsection
