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

        @if (session('success'))
            <div class="um-alert-custom um-alert-success" role="alert" id="successMessage">
                <i class="feather icon-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="um-alert-custom um-alert-error" role="alert" id="errorMessage">
                <i class="feather icon-alert-circle"></i>
                {{ session('error') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        {{-- عرض جميع أخطاء التحقق --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-container">
                <div class="alert-header">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h4 class="alert-title">يوجد أخطاء في البيانات المدخلة</h4>
                    <button type="button" class="alert-close" onclick="this.parentElement.parentElement.style.display='none'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="alert-body">
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>
                                <span>
                                    <svg style="width: 16px; height: 16px; margin-left: 8px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                    </svg>
                                    {{ $error }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

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
                            <div class="input-wrapper {{ $errors->has('barcode') ? 'has-error' : '' }}">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 6h18"></path>
                                    <path d="M3 12h18"></path>
                                    <path d="M3 18h18"></path>
                                </svg>
                                <input type="text" name="barcode" id="barcode"
                                       class="form-input @error('barcode') input-error @enderror"
                                       placeholder="أدخل رمز المادة"
                                       value="{{ old('barcode') }}"
                                       required readonly>
                            </div>
                            @error('barcode')
                                <div class="error-message" style="display: block;">{{ $message }}</div>
                            @else
                                <div class="error-message" id="barcode-error" style="display: none;"></div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="material_type" class="form-label">
                                نوع المادة (عربي)
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper {{ $errors->has('name_ar') ? 'has-error' : '' }}">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16v16H4z"></path>
                                    <line x1="4" y1="8" x2="20" y2="8"></line>
                                </svg>
                                <input type="text" name="name_ar" id="material_type"
                                       class="form-input @error('name_ar') input-error @enderror"
                                       placeholder="اسم المادة"
                                       value="{{ old('name_ar') }}"
                                       required>
                            </div>
                            @error('name_ar')
                                <div class="error-message" style="display: block;">{{ $message }}</div>
                            @else
                                <div class="error-message" id="material_type-error" style="display: none;"></div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="material_type_en" class="form-label">نوع المادة (إنجليزي)</label>
                            <div class="input-wrapper {{ $errors->has('name_en') ? 'has-error' : '' }}">
                                <input type="text" name="name_en" id="material_type_en"
                                       class="form-input @error('name_en') input-error @enderror"
                                       placeholder="Material Name in English"
                                       value="{{ old('name_en') }}">
                            </div>
                            @error('name_en')
                                <div class="error-message" style="display: block;">{{ $message }}</div>
                            @else
                                <div class="error-message" id="material_type_en-error" style="display: none;"></div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="manufacture_date" class="form-label">تاريخ الصنع</label>
                            <div class="input-wrapper {{ $errors->has('manufacture_date') ? 'has-error' : '' }}">
                                <input type="date" name="manufacture_date" id="manufacture_date"
                                       class="form-input @error('manufacture_date') input-error @enderror"
                                       value="{{ old('manufacture_date') }}">
                            </div>
                            @error('manufacture_date')
                                <div class="error-message" style="display: block;">{{ $message }}</div>
                            @else
                                <div class="error-message" id="manufacture_date-error" style="display: none;"></div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="expiry_date" class="form-label">تاريخ الصلاحية</label>
                            <div class="input-wrapper {{ $errors->has('expiry_date') ? 'has-error' : '' }}">
                                <input type="date" name="expiry_date" id="expiry_date"
                                       class="form-input @error('expiry_date') input-error @enderror"
                                       value="{{ old('expiry_date') }}">
                            </div>
                            @error('expiry_date')
                                <div class="error-message" style="display: block;">{{ $message }}</div>
                            @else
                                <div class="error-message" id="expiry_date-error" style="display: none;"></div>
                            @enderror
                        </div>

                        <div class="form-group full-width">
                            <label for="notes" class="form-label">الملاحظات (عربي)</label>
                            <div class="input-wrapper {{ $errors->has('notes') ? 'has-error' : '' }}">
                                <textarea name="notes" id="notes"
                                          class="form-input @error('notes') input-error @enderror"
                                          rows="3"
                                          placeholder="ملاحظات حول المادة">{{ old('notes') }}</textarea>
                            </div>
                            @error('notes')
                                <div class="error-message" style="display: block;">{{ $message }}</div>
                            @else
                                <div class="error-message" id="notes-error" style="display: none;"></div>
                            @enderror
                        </div>

                        <div class="form-group full-width">
                            <label for="notes_en" class="form-label">الملاحظات (إنجليزي)</label>
                            <div class="input-wrapper {{ $errors->has('notes_en') ? 'has-error' : '' }}">
                                <textarea name="notes_en" id="notes_en"
                                          class="form-input @error('notes_en') input-error @enderror"
                                          rows="3"
                                          placeholder="Notes in English">{{ old('notes_en') }}</textarea>
                            </div>
                            @error('notes_en')
                                <div class="error-message" style="display: block;">{{ $message }}</div>
                            @else
                                <div class="error-message" id="notes_en-error" style="display: none;"></div>
                            @enderror
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

            // إزالة الخطأ عند التعديل
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        showError(this.id, 'هذا الحقل مطلوب');
                        this.classList.add('input-error');
                        this.closest('.input-wrapper').classList.add('has-error');
                    } else {
                        hideError(this.id);
                        this.classList.remove('input-error');
                        this.closest('.input-wrapper').classList.remove('has-error');
                    }
                });

                input.addEventListener('input', function() {
                    hideError(this.id);
                    this.classList.remove('input-error');
                    this.closest('.input-wrapper').classList.remove('has-error');
                });
            });

            // Form submission handler
            form.addEventListener('submit', function(e) {
                // لا نمنع الإرسال، فقط نتحقق من الحقول المطلوبة
                let isValid = true;
                const requiredFields = form.querySelectorAll('[required]');

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        showError(field.id, 'هذا الحقل مطلوب');
                        field.classList.add('input-error');
                        field.closest('.input-wrapper').classList.add('has-error');
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstError = form.querySelector('.input-error');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }

                    // عرض رسالة خطأ عامة
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'خطأ في البيانات',
                            text: 'الرجاء ملء جميع الحقول المطلوبة',
                            icon: 'error',
                            confirmButtonText: 'حسناً'
                        });
                    }
                }
            });

            // Auto-hide success/error messages after 5 seconds
            setTimeout(function() {
                const successMsg = document.getElementById('successMessage');
                const errorMsg = document.getElementById('errorMessage');
                if (successMsg) {
                    successMsg.style.transition = 'opacity 0.5s';
                    successMsg.style.opacity = '0';
                    setTimeout(() => successMsg.style.display = 'none', 500);
                }
                if (errorMsg) {
                    errorMsg.style.transition = 'opacity 0.5s';
                    errorMsg.style.opacity = '0';
                    setTimeout(() => errorMsg.style.display = 'none', 500);
                }
            }, 5000);

            // Scroll to errors on page load if they exist
            const firstServerError = document.querySelector('.input-error');
            if (firstServerError) {
                setTimeout(() => {
                    firstServerError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 100);
            }
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

    <style>
        /* Alert Styles */
        .alert-container {
            margin-bottom: 20px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            padding: 0;
        }

        .alert-danger {
            background: linear-gradient(135deg, #ff5252 0%, #ff1744 100%);
            border: 1px solid #ff5252;
            color: #fff;
        }

        .alert-success {
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            border: 1px solid #4caf50;
            color: #fff;
        }

        .alert-warning {
            background: linear-gradient(135deg, #ff9800 0%, #fb8c00 100%);
            border: 1px solid #ff9800;
            color: #fff;
        }

        .alert-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
        }

        .alert-icon {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
        }

        .alert-title {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            flex: 1;
        }

        .alert-body {
            padding: 12px 16px;
        }

        .alert-description {
            margin: 0;
            font-size: 14px;
            opacity: 0.95;
        }

        .error-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .error-list li {
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 6px;
        }

        .error-list li:last-child {
            margin-bottom: 0;
        }

        .error-list li span {
            display: flex;
            align-items: flex-start;
        }

        /* Close button for alerts */
        .alert-close {
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            color: inherit;
            padding: 0;
        }

        .alert-close:hover {
            opacity: 1;
        }

        .alert-close svg {
            width: 18px;
            height: 18px;
        }

        /* Error message styles */
        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .error-message::before {
            content: "⚠";
            font-size: 14px;
        }

        /* Input error styles */
        .input-error {
            border-color: #dc3545 !important;
            background-color: #fff5f5 !important;
        }

        .input-wrapper.has-error {
            border-color: #dc3545;
        }

        .input-wrapper.has-error .input-icon {
            color: #dc3545;
        }

        /* Custom alert styles for um-alert-custom */
        .um-alert-custom {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            animation: slideDown 0.3s ease-out;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .um-alert-success {
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: #fff;
            border: 1px solid #4caf50;
        }

        .um-alert-error {
            background: linear-gradient(135deg, #ff5252 0%, #ff1744 100%);
            color: #fff;
            border: 1px solid #ff5252;
        }

        .um-alert-custom i {
            font-size: 20px;
        }

        .um-alert-close {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .um-alert-close:hover {
            opacity: 1;
        }

        .um-alert-close i {
            font-size: 18px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .alert-header {
                flex-direction: column;
                align-items: flex-start;
                padding-left: 40px;
            }

            .alert-title {
                font-size: 15px;
            }

            .error-list li {
                font-size: 13px;
            }
        }
    </style>
@endsection