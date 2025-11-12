@extends('master')

@section('title', 'بدء معالجة جديدة')

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <path d="M3.27 6.96L12 12.7m8.73-5.74L12 12.7"></path>
                <line x1="12" y1="22.7" x2="12" y2="12"></line>
            </svg>
            بدء معالجة جديدة
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> لوحة التحكم
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>المرحلة الثانية</span>
            <i class="feather icon-chevron-left"></i>
            <span>بدء معالجة جديدة</span>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.stage2.store') }}" id="stage2Form" enctype="multipart/form-data">
            @csrf

            <!-- Processing Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <path d="M3.27 6.96L12 12.7m8.73-5.74L12 12.7"></path>
                            <line x1="12" y1="22.7" x2="12" y2="12"></line>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">معلومات المعالجة</h3>
                        <p class="section-subtitle">أدخل بيانات المعالجة</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="stage1_id" class="form-label">
                            الاستاند
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <select name="stage1_id" id="stage1_id" class="form-input" required>
                                <option value="">اختر استاند</option>
                                <option value="1">ST-001</option>
                                <option value="2">ST-002</option>
                                <option value="3">ST-003</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="input_weight" class="form-label">
                            وزن الدخول (كجم)
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <input type="number" name="input_weight" id="input_weight" class="form-input" value="" placeholder="أدخل وزن الدخول" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="process_details" class="form-label">
                            نوع المعالجة
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <select name="process_details" id="process_details" class="form-input" required>
                                <option value="">اختر نوع المعالجة</option>
                                <option value="heating">التسخين</option>
                                <option value="cooling">التبريد</option>
                                <option value="cutting">القطع</option>
                                <option value="rolling">الفرد</option>
                            </select>
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
                            <textarea name="notes" id="notes" rows="4" class="form-input" placeholder="أدخل ملاحظات للمعالجة"></textarea>
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
                    بدء المعالجة
                </button>
                <a href="{{ route('manufacturing.stage2.index') }}" class="btn-cancel">
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