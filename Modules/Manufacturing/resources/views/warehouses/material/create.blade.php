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
                            <label for="material_category" class="form-label">
                                الفئة
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <select name="material_category" id="material_category" class="form-input" required>
                                    <option value="">-- اختر الفئة --</option>
                                    <option value="raw" {{ old('material_category') == 'raw' ? 'selected' : '' }}>خام</option>
                                    <option value="manufactured" {{ old('material_category') == 'manufactured' ? 'selected' : '' }}>مصنع</option>
                                    <option value="finished" {{ old('material_category') == 'finished' ? 'selected' : '' }}>جاهز</option>
                                </select>
                            </div>
                            <div class="error-message" id="material_category-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_id" class="form-label">
                                المورد
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <select name="supplier_id" id="supplier_id" class="form-input" required>
                                    <option value="">-- اختر المورد --</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-message" id="supplier_id-error" style="display: none;"></div>
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
                                            {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-message" id="unit_id-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="delivery_note_number" class="form-label">رقم مذكرة التسليم</label>
                            <div class="input-wrapper">
                                <input type="text" name="delivery_note_number" id="delivery_note_number" class="form-input"
                                       placeholder="رقم المذكرة" value="{{ old('delivery_note_number') }}">
                            </div>
                            <div class="error-message" id="delivery_note_number-error" style="display: none;"></div>
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

                        <div class="form-group">
                            <label for="status" class="form-label">
                                الحالة
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <select name="status" id="status" class="form-input" required>
                                    <option value="">-- اختر الحالة --</option>
                                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>متوفر</option>
                                    <option value="in_use" {{ old('status') == 'in_use' ? 'selected' : '' }}>قيد الاستخدام</option>
                                    <option value="consumed" {{ old('status') == 'consumed' ? 'selected' : '' }}>مستهلك</option>
                                    <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>منتهي الصلاحية</option>
                                </select>
                            </div>
                            <div class="error-message" id="status-error" style="display: none;"></div>
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
