@extends('master')

@section('title', 'إنشاء كويل جديد - المرحلة الثالثة')

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <path d="M3.27 6.96L12 12.7m8.73-5.74L12 12.7"></path>
                <line x1="12" y1="22.7" x2="12" y2="12"></line>
            </svg>
            إنشاء كويل جديد
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> لوحة التحكم
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>المرحلة الثالثة</span>
            <i class="feather icon-chevron-left"></i>
            <span>إنشاء كويل جديد</span>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.stage3.store') }}" id="stage3Form" enctype="multipart/form-data">
            @csrf

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
                        <p class="section-subtitle">أدخل البيانات الأساسية للكويل</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="stage2_id" class="form-label">
                            المعالجة السابقة
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <select name="stage2_id" id="stage2_id" class="form-input" required>
                                <option value="">اختر معالجة من المرحلة الثانية</option>
                                <option value="1">ST-001 معالج (245 كجم)</option>
                                <option value="2">ST-002 معالج (295 كجم)</option>
                            </select>
                        </div>
                    </div>

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
                            <input type="text" name="coil_number" id="coil_number" class="form-input" value="" placeholder="أدخل رقم الكويل" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="base_weight" class="form-label">وزن الأساس (كجم) <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <input type="number" name="base_weight" id="base_weight" class="form-input" value="" placeholder="أدخل وزن الأساس" step="0.01" min="0" required>
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
                                <option value="red">أحمر</option>
                                <option value="blue">أزرق</option>
                                <option value="green">أخضر</option>
                                <option value="yellow">أصفر</option>
                                <option value="black">أسود</option>
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
                            <input type="number" name="dye_weight" id="dye_weight" class="form-input" value="" placeholder="أدخل وزن الصبغة" step="0.01" min="0" required>
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
                                <option value="pe">بولي إيثيلين</option>
                                <option value="pp">بولي بروبيلين</option>
                                <option value="pvc">PVC</option>
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
                            <input type="number" name="plastic_weight" id="plastic_weight" class="form-input" value="" placeholder="أدخل وزن البلاستيك" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="total_weight" class="form-label">الوزن الإجمالي (كجم) <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <input type="number" name="total_weight" id="total_weight" class="form-input" value="" placeholder="أدخل الوزن الإجمالي" step="0.01" min="0" required>
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
                            <textarea name="notes" id="notes" rows="4" class="form-input" placeholder="أدخل ملاحظات للكويل"></textarea>
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
                    حفظ الكويل
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
