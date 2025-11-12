@extends('master')

@section('title', 'تعديل الكويل - المرحلة الثالثة')

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <path d="M3.27 6.96L12 12.7m8.73-5.74L12 12.7"></path>
                <line x1="12" y1="22.7" x2="12" y2="12"></line>
            </svg>
            تعديل الكويل
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> لوحة التحكم
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>المرحلة الثالثة</span>
            <i class="feather icon-chevron-left"></i>
            <span>تعديل كويل</span>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.stage3.update', $stage3->id ?? 1) }}" id="stage3Form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Coil Information Section -->
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
                        <h3 class="section-title">معلومات الكويل</h3>
                        <p class="section-subtitle">قم بتحديث بيانات الكويل</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="coil_number" class="form-label">
                            رقم الكويل
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <input type="text" name="coil_number" id="coil_number" class="form-input" value="{{ $stage3->coil_number ?? old('coil_number') }}" placeholder="أدخل رقم الكويل" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="base_weight" class="form-label">وزن الأساس (كجم) <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <input type="number" name="base_weight" id="base_weight" class="form-input" value="{{ $stage3->base_weight ?? old('base_weight') }}" placeholder="أدخل وزن الأساس" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dye_color" class="form-label">
                            لون الصبغة
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <select name="dye_color" id="dye_color" class="form-input" required>
                                <option value="">اختر لون الصبغة</option>
                                <option value="red" {{ ($stage3->dye_color ?? old('dye_color')) == 'red' ? 'selected' : '' }}>أحمر</option>
                                <option value="blue" {{ ($stage3->dye_color ?? old('dye_color')) == 'blue' ? 'selected' : '' }}>أزرق</option>
                                <option value="green" {{ ($stage3->dye_color ?? old('dye_color')) == 'green' ? 'selected' : '' }}>أخضر</option>
                                <option value="yellow" {{ ($stage3->dye_color ?? old('dye_color')) == 'yellow' ? 'selected' : '' }}>أصفر</option>
                                <option value="black" {{ ($stage3->dye_color ?? old('dye_color')) == 'black' ? 'selected' : '' }}>أسود</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dye_weight" class="form-label">
                            وزن الصبغة (كجم)
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <input type="number" name="dye_weight" id="dye_weight" class="form-input" value="{{ $stage3->dye_weight ?? old('dye_weight') }}" placeholder="أدخل وزن الصبغة" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="plastic_type" class="form-label">
                            نوع البلاستيك
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 12h18M3 6h18M3 18h18"></path>
                            </svg>
                            <select name="plastic_type" id="plastic_type" class="form-input" required>
                                <option value="">اختر نوع البلاستيك</option>
                                <option value="pe" {{ ($stage3->plastic_type ?? old('plastic_type')) == 'pe' ? 'selected' : '' }}>بولي إيثيلين</option>
                                <option value="pp" {{ ($stage3->plastic_type ?? old('plastic_type')) == 'pp' ? 'selected' : '' }}>بولي بروبيلين</option>
                                <option value="pvc" {{ ($stage3->plastic_type ?? old('plastic_type')) == 'pvc' ? 'selected' : '' }}>PVC</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="plastic_weight" class="form-label">
                            وزن البلاستيك (كجم)
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <input type="number" name="plastic_weight" id="plastic_weight" class="form-input" value="{{ $stage3->plastic_weight ?? old('plastic_weight') }}" placeholder="أدخل وزن البلاستيك" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="total_weight" class="form-label">الوزن الإجمالي (كجم) <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <input type="number" name="total_weight" id="total_weight" class="form-input" value="{{ $stage3->total_weight ?? old('total_weight') }}" placeholder="أدخل الوزن الإجمالي" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="waste_percentage" class="form-label">نسبة الهدر (%)</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <input type="number" name="waste_percentage" id="waste_percentage" class="form-input" value="{{ $stage3->waste_percentage ?? old('waste_percentage') }}" placeholder="أدخل نسبة الهدر" step="0.01" min="0" max="100">
                        </div>
                    </div>
                        <label for="status" class="form-label">الحالة</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                            </svg>
                            <select name="status" id="status" class="form-input">
                                <option value="created" {{ ($stage3->status ?? old('status')) == 'created' ? 'selected' : '' }}>تم الإنشاء</option>
                                <option value="in_process" {{ ($stage3->status ?? old('status')) == 'in_process' ? 'selected' : '' }}>قيد الصنع</option>
                                <option value="completed" {{ ($stage3->status ?? old('status')) == 'completed' ? 'selected' : '' }}>جاهز</option>
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
                            <textarea name="notes" id="notes" rows="4" class="form-input" placeholder="أدخل ملاحظات للكويل">{{ $stage3->notes ?? old('notes') }}</textarea>
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
                        <p class="section-subtitle">معلومات تاريخية للكويل</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="created_at" class="form-label">
                            تاريخ الإنشاء
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <input type="text" name="created_at" id="created_at" class="form-input" value="{{ $stage3->created_at ?? '2025-01-15' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="updated_at" class="form-label">
                            تاريخ التحديث
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <input type="text" name="updated_at" id="updated_at" class="form-input" value="{{ $stage3->updated_at ?? '2025-01-15' }}" readonly>
                        </div>
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
                    حفظ التغييرات
                </button>
                <a href="{{ route('manufacturing.stage3.index') }}" class="btn-cancel">
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
            const form = document.getElementById('stage3Form');
            const inputs = form.querySelectorAll('.form-input');

            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.required && !this.value) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // Smooth scroll to first error
            form.addEventListener('submit', function(e) {
                const firstInvalid = form.querySelector('.is-invalid, :invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            });
        });
    </script>

@endsection
