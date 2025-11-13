@extends('master')

@section('title', 'تعديل بيانات المستودع')

@section('content')

        <!-- Header -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                تعديل بيانات المستودع
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المستودعات</span>
                <i class="feather icon-chevron-left"></i>
                <span>تعديل مستودع</span>
            </nav>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <form method="POST" action="{{ route('manufacturing.warehouses.update', $warehouse->id) }}" id="warehouseForm">
                @csrf
                @method('PUT')

                <!-- Warehouse Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon personal">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">معلومات المستودع</h3>
                            <p class="section-subtitle">قم بتحديث البيانات الأساسية للمستودع</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name" class="form-label">
                                اسم المستودع
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                                <input type="text" name="name" id="name"
                                    class="form-input"
                                    value="{{ old('name', $warehouse->warehouse_name) }}" placeholder="أدخل اسم المستودع" required>
                            </div>
                            <div class="error-message" id="name-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="name_en" class="form-label">
                                اسم المستودع بالإنجليزية
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                                <input type="text" name="name_en" id="name_en"
                                    class="form-input"
                                    value="{{ old('name_en', $warehouse->warehouse_name_en) }}" placeholder="Enter warehouse name in English">
                            </div>
                            <div class="error-message" id="name_en-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="code" class="form-label">
                                رمز المستودع
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                    <line x1="8" y1="21" x2="16" y2="21"></line>
                                    <line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                                <input type="text" name="code" id="code"
                                    class="form-input"
                                    value="{{ old('code', $warehouse->warehouse_code) }}" placeholder="أدخل رمز المستودع" required readonly>
                            </div>
                            <div class="error-message" id="code-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="location" class="form-label">الموقع</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <input type="text" name="location" id="location"
                                    class="form-input" value="{{ old('location', $warehouse->location) }}"
                                    placeholder="أدخل موقع المستودع">
                            </div>
                            <div class="error-message" id="location-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="location_en" class="form-label">الموقع بالإنجليزية</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <input type="text" name="location_en" id="location_en"
                                    class="form-input" value="{{ old('location_en', $warehouse->location_en) }}"
                                    placeholder="Enter location in English">
                            </div>
                            <div class="error-message" id="location_en-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="manager_id" class="form-label">المسؤول</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <select name="manager_id" id="manager_id"
                                    class="form-input">
                                    <option value="">اختر المسؤول</option>
                                    <option value="1" {{ old('manager_id', $warehouse->manager_name) == 'أحمد محمد' ? 'selected' : '' }}>أحمد محمد</option>
                                    <option value="2" {{ old('manager_id', $warehouse->manager_name) == 'محمد علي' ? 'selected' : '' }}>محمد علي</option>
                                    <option value="3" {{ old('manager_id', $warehouse->manager_name) == 'سارة أحمد' ? 'selected' : '' }}>سارة أحمد</option>
                                </select>
                            </div>
                            <div class="error-message" id="manager_id-error" style="display: none;"></div>
                        </div>

                        <div class="form-group full-width">
                            <label for="description" class="form-label">الوصف</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <line x1="8" y1="6" x2="21" y2="6"></line>
                                    <line x1="8" y1="12" x2="21" y2="12"></line>
                                    <line x1="8" y1="18" x2="21" y2="18"></line>
                                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                </svg>
                                <textarea name="description" id="description" rows="4"
                                    class="form-input" placeholder="أدخل وصفاً للمستودع">{{ old('description', $warehouse->description) }}</textarea>
                            </div>
                            <div class="error-message" id="description-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="description_en" class="form-label">الوصف بالإنجليزية</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <line x1="8" y1="6" x2="21" y2="6"></line>
                                    <line x1="8" y1="12" x2="21" y2="12"></line>
                                    <line x1="8" y1="18" x2="21" y2="18"></line>
                                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                </svg>
                                <textarea name="description_en" id="description_en" rows="4"
                                    class="form-input" placeholder="Enter description in English">{{ old('description_en', $warehouse->description_en) }}</textarea>
                            </div>
                            <div class="error-message" id="description_en-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="capacity" class="form-label">السعة التخزينية (متر مكعب)</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                <input type="number" name="capacity" id="capacity"
                                    class="form-input" value="{{ old('capacity', $warehouse->capacity) }}"
                                    placeholder="أدخل السعة التخزينية" step="0.01" min="0">
                            </div>
                            <div class="error-message" id="capacity-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="status" class="form-label">الحالة</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                                    </path>
                                </svg>
                                <select name="status" id="status" class="form-input">
                                    <option value="active" {{ old('status', $warehouse->is_active ? 'active' : 'inactive') == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="inactive" {{ old('status', $warehouse->is_active ? 'active' : 'inactive') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                </select>
                            </div>
                            <div class="error-message" id="status-error" style="display: none;"></div>
                        </div>

                        <div class="form-group full-width">
                            <div class="switch-group">
                                <input type="checkbox" id="is_active" name="is_active" class="switch-input" {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="switch-label">
                                    <span class="switch-button"></span>
                                    <span class="switch-text">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                        </svg>
                                        تفعيل المستودع
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon account">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">معلومات الاتصال</h3>
                            <p class="section-subtitle">قم بتحديث معلومات الاتصال للمستودع</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <input type="text" name="phone" id="phone"
                                    class="form-input" value="{{ old('phone', $warehouse->contact_number) }}"
                                    placeholder="أدخل رقم الهاتف">
                            </div>
                            <div class="error-message" id="phone-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <input type="email" name="email" id="email"
                                    class="form-input" value="{{ old('email') }}"
                                    placeholder="أدخل البريد الإلكتروني">
                            </div>
                            <div class="error-message" id="email-error" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="created_at" class="form-label">
                                تاريخ الإنشاء
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2">
                                    </rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <input type="text" name="created_at" id="created_at"
                                    class="form-input"
                                    value="{{ old('created_at', $warehouse->created_at->format('Y-m-d')) }}" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="updated_at" class="form-label">
                                تاريخ التحديث
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2">
                                    </rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <input type="text" name="updated_at" id="updated_at"
                                    class="form-input"
                                    value="{{ old('updated_at', $warehouse->updated_at->format('Y-m-d')) }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('manufacturing.warehouses.index') }}" class="btn-cancel">
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
            const form = document.getElementById('warehouseForm');
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
                        text: 'هل أنت متأكد من حفظ التغييرات؟',
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