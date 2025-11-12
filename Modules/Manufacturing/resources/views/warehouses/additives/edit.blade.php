@extends('master')

@section('title', 'تعديل صبغة/بلاستيك')

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
            </svg>
            تعديل صبغة/بلاستيك
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> لوحة التحكم
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>المستودع</span>
            <i class="feather icon-chevron-left"></i>
            <span>الصبغات والبلاستيك</span>
            <i class="feather icon-chevron-left"></i>
            <span>تعديل</span>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.additives.update', 1) }}" id="additiveForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Additive Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">معلومات الصبغة/البلاستيك</h3>
                        <p class="section-subtitle">قم بتحديث البيانات</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="additive_name" class="form-label">
                            الاسم
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            </svg>
                            <input type="text" name="additive_name" id="additive_name"
                                class="form-input" value="صبغة أحمر" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="type" class="form-label">
                            النوع
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="1"></circle>
                            </svg>
                            <select name="type" id="type" class="form-input" required>
                                <option value="">-- اختر النوع --</option>
                                <option value="dye" selected>صبغة</option>
                                <option value="plastic">بلاستيك</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="quantity" class="form-label">
                            الكمية
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                            </svg>
                            <input type="number" name="quantity" id="quantity"
                                class="form-input" value="50" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="unit" class="form-label">الوحدة</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="5" r="3"></circle>
                            </svg>
                            <select name="unit" id="unit" class="form-input">
                                <option value="liter" selected>لتر</option>
                                <option value="kg">كيلوغرام</option>
                                <option value="gram">غرام</option>
                                <option value="unit">وحدة</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="supplier_id" class="form-label">المورد</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            </svg>
                            <select name="supplier_id" id="supplier_id" class="form-input">
                                <option value="">-- اختر المورد --</option>
                                <option value="1" selected>شركة الأصباغ المتحدة</option>
                                <option value="2">شركة البلاستيك الخليج</option>
                                <option value="3">شركة المواد الكيماوية</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="color" class="form-label">اللون (للصبغات)</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg>
                            <input type="color" name="color" id="color" class="form-input" value="#ff0000">
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <div class="input-wrapper">
                            <textarea name="notes" id="notes"
                                class="form-input" rows="4">صبغة عالية الجودة وممتازة للاستخدام</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">الحالة</label>
                        <div class="input-wrapper">
                            <select name="status" id="status" class="form-input">
                                <option value="available" selected>متوفر</option>
                                <option value="low_stock">مخزون منخفض</option>
                                <option value="out_of_stock">غير متوفر</option>
                            </select>
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
                <button type="button" class="btn-cancel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                    </svg>
                    إلغاء
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('additiveForm');
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
