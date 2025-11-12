@extends('master')

@section('title', 'إضافة استاند جديد')

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            إضافة استاند جديد
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> لوحة التحكم
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>المرحلة الأولى</span>
            <i class="feather icon-chevron-left"></i>
            <span>إضافة استاند جديد</span>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.stage1.store') }}" id="stage1Form" enctype="multipart/form-data">
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
                        <h3 class="section-title">معلومات الاستاند</h3>
                        <p class="section-subtitle">أدخل البيانات الأساسية للاستاند</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="material_id" class="form-label">
                            المادة الخام
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            </svg>
                            <select name="material_id" id="material_id" class="form-input" required>
                                <option value="">اختر المادة الخام</option>
                                <option value="1">مادة خام رقم 1 - حديد مسلح</option>
                                <option value="2">مادة خام رقم 2 - ألمنيوم نقي</option>
                                <option value="3">مادة خام رقم 3 - نحاس أحمر</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="stand_number" class="form-label">
                            رقم الاستاند
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            <input type="text" name="stand_number" id="stand_number" class="form-input" value="" placeholder="أدخل رقم الاستاند" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="wire_size" class="form-label">
                            حجم السلك (مم)
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="3" y1="12" x2="21" y2="12"></line>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <line x1="3" y1="18" x2="21" y2="18"></line>
                            </svg>
                            <input type="number" name="wire_size" id="wire_size" class="form-input" value="" placeholder="أدخل حجم السلك" step="0.1" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="weight" class="form-label">الوزن (كجم) <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <input type="number" name="weight" id="weight" class="form-input" value="" placeholder="أدخل الوزن" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="waste_percentage" class="form-label">نسبة الهدر (%)</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <input type="number" name="waste_percentage" id="waste_percentage" class="form-input" value="" placeholder="أدخل نسبة الهدر" step="0.01" min="0" max="100">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">الحالة</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                            </svg>
                            <select name="status" id="status" class="form-input">
                                <option value="created">تم الإنشاء</option>
                                <option value="in_process">قيد المعالجة</option>
                                <option value="completed">مكتمل</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="notes" class="form-label">الملاحظات</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                <line x1="3" y1="18" x2="3.01" y2="18"></line>
                            </svg>
                            <textarea name="notes" id="notes" rows="4" class="form-input" placeholder="أدخل ملاحظات للاستاند"></textarea>
                        </div>
                    </div>

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
                                    تفعيل الاستاند
                                </span>
                            </label>
                        </div>
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
                    حفظ الاستاند
                </button>
                <a href="{{ route('manufacturing.stage1.index') }}" class="btn-cancel">
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
