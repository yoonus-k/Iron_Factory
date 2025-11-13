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
                <span>المنتجات</span>
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
                            <label for="course_code" class="form-label">
                                رمز المادة
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                                <input type="text" name="barcode" id="barcode"
                                    class="form-input"
                                    value="" placeholder="أدخل رمز المادة" required>
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="course_title" class="form-label">
                                نوع المادة
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                                <input type="text" name="material_type" id="material_type"
                                    class="form-input"
                                    value="" placeholder="أدخل نوع المادة" required>
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="supplier_id" class="form-label">
                                المورد
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                    <line x1="8" y1="21" x2="16" y2="21"></line>
                                    <line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                                <select name="supplier_id" id="supplier_id"
                                    class="form-input" required>
                                    <option value="">اختر المورد</option>
                                    <!-- Add supplier options here -->
                                </select>
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="original_weight" class="form-label">الوزن الأصلي</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                <input type="number" name="original_weight" id="original_weight"
                                    class="form-input" value=""
                                    placeholder="أدخل الوزن الأصلي" step="0.001" min="0">
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="unit" class="form-label">الوحدة</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <select name="unit" id="unit" class="form-input">
                                    <option value="kg">كيلوغرام</option>
                                    <option value="ton">طن</option>
                                    <option value="gram">غرام</option>
                                </select>
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="delivery_note_number" class="form-label">رقم مذكرة التسليم</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <input type="text" name="delivery_note_number" id="delivery_note_number"
                                    class="form-input"
                                    value="" placeholder="أدخل رقم مذكرة التسليم">
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="purchase_invoice_id" class="form-label">معرف الفاتورة</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2">
                                    </rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <path d="M21 15l-5-5L5 21"></path>
                                </svg>
                                <input type="number" name="purchase_invoice_id" id="purchase_invoice_id"
                                    class="form-input" placeholder="أدخل معرف الفاتورة">
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="notes" class="form-label">الملاحظات</label>
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
                                <textarea name="notes" id="notes" rows="4"
                                    class="form-input" placeholder="أدخل ملاحظات للمادة"></textarea>
                            </div>
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
                                    <option value="available">متوفر</option>
                                    <option value="in_use">قيد الاستخدام</option>
                                    <option value="consumed">مستهلك</option>
                                    <option value="expired">منتهي الصلاحية</option>
                                </select>
                            </div>
                            @error('status')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="created_by" class="form-label">أدخل بواسطة</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <select name="created_by" id="created_by" class="form-input">
                                    <option value="">اختر الموظف</option>
                                    <!-- Add employee options here -->
                                </select>
                            </div>
                            @error('created_by')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- This section was removed as it was a duplicate of the supplier field above -->

                        <div class="form-group full-width">
                            &nbsp;
                        </div>

                        <div class="form-group full-width">
                            <div class="switch-group">
                                <input type="checkbox" id="is_active" name="is_active" class="switch-input" checked>
                                <label for="is_active" class="switch-label">
                                    <span class="switch-button"></span>
                                    <span class="switch-text">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                        </svg>
                                        تفعيل المادة
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            &nbsp;
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
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
                            <h3 class="section-title">معلومات إضافية</h3>
                            <p class="section-subtitle">أدخل المعلومات الإضافية للمادة</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            &nbsp;
                        </div>

                        <div class="form-group">
                            &nbsp;
                        </div>

                        <div class="form-group">
                            &nbsp;
                        </div>

                        <div class="form-group">
                            &nbsp;
                        </div>

                        <div class="form-group">
                            &nbsp;
                        </div>

                        <div class="form-group">
                            &nbsp;
                        </div>

                        <div class="form-group">
                            &nbsp;
                        </div>

                        <div class="form-group full-width">
                            &nbsp;
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
                    <button type="button" class="btn-cancel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        إلغاء
                    </button>
                </div>
            </form>
        </div>

@endsection
