@extends('master')

@section('title', 'تعديل بيانات الوردية')

@section('content')

        <!-- Header -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                تعديل بيانات الوردية
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>الورديات والعمال</span>
                <i class="feather icon-chevron-left"></i>
                <span>تعديل وردية</span>
            </nav>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <form method="POST" action="{{ route('manufacturing.shifts-workers.update', 1) }}" id="shiftForm">
                @csrf
                @method('PUT')

                <!-- Shift Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon personal">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">معلومات الوردية</h3>
                            <p class="section-subtitle">قم بتحديث البيانات الأساسية للوردية</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="shift_code" class="form-label">
                                رقم الوردية
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <input type="text" name="shift_code" id="shift_code"
                                    class="form-input"
                                    value="SHIFT-2025-001" placeholder="أدخل رقم الوردية" required readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="shift_date" class="form-label">
                                تاريخ الوردية
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <input type="date" name="shift_date" id="shift_date"
                                    class="form-input"
                                    value="2025-01-15" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="shift_type" class="form-label">
                                نوع الوردية
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <select name="shift_type" id="shift_type"
                                    class="form-input" required>
                                    <option value="">اختر نوع الوردية</option>
                                    <option value="morning">صباحية</option>
                                    <option value="evening" selected>مسائية</option>
                                    <option value="night">ليلية</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supervisor_id" class="form-label">
                                المسؤول عن الوردية
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <select name="supervisor_id" id="supervisor_id"
                                    class="form-input" required>
                                    <option value="">اختر المسؤول</option>
                                    <option value="1">أحمد محمد</option>
                                    <option value="2" selected>محمد علي</option>
                                    <option value="3">عمر خالد</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="start_time" class="form-label">
                                وقت البدء
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <input type="time" name="start_time" id="start_time"
                                    class="form-input"
                                    value="14:00" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="end_time" class="form-label">
                                وقت الانتهاء
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <input type="time" name="end_time" id="end_time"
                                    class="form-input"
                                    value="22:00" required>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="notes" class="form-label">ملاحظات</label>
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
                                    class="form-input" placeholder="أدخل ملاحظات للوردية">وردية مسائية نشطة</textarea>
                            </div>
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
                                        تفعيل الوردية
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Workers Assignment Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon account">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">تعيين العمال</h3>
                            <p class="section-subtitle">حدث تعيين العمال لهذه الوردية</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">العمال المعينون</label>
                            <div class="workers-selection">
                                <div class="worker-item">
                                    <input type="checkbox" id="worker_1" name="workers[]" value="1">
                                    <label for="worker_1">أحمد محمد - عامل تقطيع</label>
                                </div>
                                <div class="worker-item">
                                    <input type="checkbox" id="worker_2" name="workers[]" value="2" checked>
                                    <label for="worker_2">محمد علي - عامل تقطيع</label>
                                </div>
                                <div class="worker-item">
                                    <input type="checkbox" id="worker_3" name="workers[]" value="3" checked>
                                    <label for="worker_3">عمر خالد - عامل تقطيع</label>
                                </div>
                                <div class="worker-item">
                                    <input type="checkbox" id="worker_4" name="workers[]" value="4" checked>
                                    <label for="worker_4">خالد أحمد - عامل تقطيع</label>
                                </div>
                                <div class="worker-item">
                                    <input type="checkbox" id="worker_5" name="workers[]" value="5">
                                    <label for="worker_5">سامي عبد الله - عامل تقطيع</label>
                                </div>
                            </div>
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
                            <p class="section-subtitle">معلومات إضافية عن الوردية</p>
                        </div>
                    </div>

                    <div class="form-grid">
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
                                    value="2025-01-15 08:00" readonly>
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
                                    value="2025-01-15 10:30" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            &nbsp;
                        </div>

                        <div class="form-group">
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
                        حفظ التغييرات
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