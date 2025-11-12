@extends('master')

@section('title', 'إنشاء كرتون جديد - المرحلة الرابعة')

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l12-3"></path>
                <path d="M6 9v6a2 2 0 002 2h8a2 2 0 002-2V9"></path>
                <path d="M6 9l-2 12a2 2 0 002 2h12a2 2 0 002-2l-2-12"></path>
            </svg>
            إنشاء كرتون جديد
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> لوحة التحكم
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>المرحلة الرابعة</span>
            <i class="feather icon-chevron-left"></i>
            <span>إنشاء كرتون جديد</span>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.stage4.store') }}" id="stage4Form" enctype="multipart/form-data">
            @csrf

            <!-- Box Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l12-3"></path>
                            <path d="M6 9v6a2 2 0 002 2h8a2 2 0 002-2V9"></path>
                            <path d="M6 9l-2 12a2 2 0 002 2h12a2 2 0 002-2l-2-12"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">معلومات الكرتون</h3>
                        <p class="section-subtitle">أدخل البيانات الأساسية للكرتون</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="stage3_id" class="form-label">
                            الكويل
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <select name="stage3_id" id="stage3_id" class="form-input" required>
                                <option value="">اختر كويل من المرحلة الثالثة</option>
                                <option value="1">COIL-001 (250 كجم)</option>
                                <option value="2">COIL-002 (245 كجم)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="box_number" class="form-label">
                            رقم الكرتون
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 9l12-3"></path>
                                <path d="M6 9v6a2 2 0 002 2h8a2 2 0 002-2V9"></path>
                                <path d="M6 9l-2 12a2 2 0 002 2h12a2 2 0 002-2l-2-12"></path>
                            </svg>
                            <input type="text" name="box_number" id="box_number" class="form-input" value="" placeholder="أدخل رقم الكرتون" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="coils_count" class="form-label">عدد الكويلات <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <input type="number" name="coils_count" id="coils_count" class="form-input" value="" placeholder="أدخل عدد الكويلات" min="1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="packaging_type" class="form-label">
                            نوع التغليف
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 9l12-3"></path>
                                <path d="M6 9v6a2 2 0 002 2h8a2 2 0 002-2V9"></path>
                                <path d="M6 9l-2 12a2 2 0 002 2h12a2 2 0 002-2l-2-12"></path>
                            </svg>
                            <select name="packaging_type" id="packaging_type" class="form-input" required>
                                <option value="">اختر نوع التغليف</option>
                                <option value="carton">كرتون</option>
                                <option value="plastic">بلاستيك</option>
                                <option value="wood">خشب</option>
                            </select>
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

                    <!-- Customer Information Section -->
                    <div class="form-group full-width">
                        &nbsp;
                    </div>

                    <div class="form-group">
                        <label for="customer_name" class="form-label">
                            اسم العميل
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <input type="text" name="customer_name" id="customer_name" class="form-input" value="" placeholder="أدخل اسم العميل" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="customer_email" class="form-label">
                            البريد الإلكتروني للعميل
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                <path d="M22 4l-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 4"></path>
                            </svg>
                            <input type="email" name="customer_email" id="customer_email" class="form-input" value="" placeholder="أدخل البريد الإلكتروني" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="customer_phone" class="form-label">
                            رقم الهاتف للعميل
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            <input type="tel" name="customer_phone" id="customer_phone" class="form-input" value="" placeholder="أدخل رقم الهاتف" required>
                        </div>
                    </div>

                    <!-- Shipping Information Section -->
                    <div class="form-group full-width">
                        &nbsp;
                    </div>

                    <div class="form-group full-width">
                        <label for="shipping_address" class="form-label">
                            عنوان الشحن
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <textarea name="shipping_address" id="shipping_address" rows="3" class="form-input" placeholder="أدخل عنوان الشحن الكامل" required></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tracking_number" class="form-label">
                            رقم التتبع
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 9l12-3"></path>
                                <path d="M6 9v6a2 2 0 002 2h8a2 2 0 002-2V9"></path>
                                <path d="M6 9l-2 12a2 2 0 002 2h12a2 2 0 002-2l-2-12"></path>
                            </svg>
                            <input type="text" name="tracking_number" id="tracking_number" class="form-input" value="" placeholder="أدخل رقم التتبع (اختياري)">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="expected_delivery_date" class="form-label">
                            تاريخ التسليم المتوقع
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <input type="date" name="expected_delivery_date" id="expected_delivery_date" class="form-input" required>
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
                            <textarea name="notes" id="notes" rows="4" class="form-input" placeholder="أدخل ملاحظات للكرتون"></textarea>
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
                    حفظ الكرتون
                </button>
                <a href="{{ route('manufacturing.stage4.index') }}" class="btn-cancel">
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
            const form = document.getElementById('stage4Form');
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
