@extends('master')

@section('title', 'تعديل بيانات المستخدم')

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-user"></i>
                تعديل بيانات المستخدم
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المستخدمين</span>
                <i class="feather icon-chevron-left"></i>
                <span>تعديل مستخدم</span>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-container">
                <div class="alert-header">
                    <i class="feather icon-alert-circle alert-icon"></i>
                    <h4 class="alert-title">يوجد أخطاء في البيانات المدخلة</h4>
                    <button type="button" class="alert-close" onclick="this.parentElement.parentElement.style.display='none'">
                        <i class="feather icon-x"></i>
                    </button>
                </div>
                <div class="alert-body">
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>
                                <span>
                                    <i class="feather icon-x-circle"></i>
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
            <form method="POST" action="{{ route('users.update', $user) }}" id="userForm">
                @csrf
                @method('PUT')

                <!-- User Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon personal">
                            <i class="feather icon-user"></i>
                        </div>
                        <div>
                            <h3 class="section-title">بيانات المستخدم</h3>
                            <p class="section-subtitle">تحديث معلومات المستخدم الأساسية</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <!-- Name -->
                        <div class="form-group">
                            <label for="name" class="form-label">
                                الاسم الكامل
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="feather icon-user input-icon"></i>
                                <input type="text" name="name" id="name" class="form-input"
                                    value="{{ old('name', $user->name) }}" placeholder="مثال: أحمد محمد علي" required>
                            </div>
                            <div class="error-message" id="name-error" style="display: none;"></div>
                        </div>

                        <!-- Username -->
                        <div class="form-group">
                            <label for="username" class="form-label">
                                اسم المستخدم
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="feather icon-at-sign input-icon"></i>
                                <input type="text" name="username" id="username" class="form-input"
                                    value="{{ old('username', $user->username) }}" placeholder="مثال: ahmed.ali" required>
                            </div>
                            <div class="error-message" id="username-error" style="display: none;"></div>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">
                                البريد الإلكتروني
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="feather icon-mail input-icon"></i>
                                <input type="email" name="email" id="email" class="form-input"
                                    value="{{ old('email', $user->email) }}" placeholder="example@domain.com" required>
                            </div>
                            <div class="error-message" id="email-error" style="display: none;"></div>
                        </div>

                        <!-- Password (Optional) -->
                        <div class="form-group">
                            <label for="password" class="form-label">
                                كلمة المرور الجديدة
                                <span class="optional">(اتركها فارغة إذا لم ترد التغيير)</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="feather icon-lock input-icon"></i>
                                <input type="password" name="password" id="password" class="form-input"
                                    placeholder="أدخل كلمة المرور الجديدة">
                            </div>
                            <div class="error-message" id="password-error" style="display: none;"></div>
                        </div>

                        <!-- Password Confirmation -->
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">
                                تأكيد كلمة المرور الجديدة
                            </label>
                            <div class="input-wrapper">
                                <i class="feather icon-lock input-icon"></i>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-input"
                                    placeholder="أعد إدخال كلمة المرور الجديدة">
                            </div>
                            <div class="error-message" id="password_confirmation-error" style="display: none;"></div>
                        </div>

                        <!-- Role -->
                        <div class="form-group">
                            <label for="role_id" class="form-label">
                                الدور
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="feather icon-shield input-icon"></i>
                                <select name="role_id" id="role_id" class="form-input" required>
                                    <option value="">-- اختر الدور --</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->role_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-message" id="role_id-error" style="display: none;"></div>
                        </div>

                        <!-- Shift -->
                        <div class="form-group">
                            <label for="shift" class="form-label">
                                الفترة الزمنية
                            </label>
                            <div class="input-wrapper">
                                <i class="feather icon-clock input-icon"></i>
                                <input type="text" name="shift" id="shift" class="form-input"
                                    value="{{ old('shift', $user->shift) }}" placeholder="صباحي / مسائي / إلخ">
                            </div>
                            <div class="error-message" id="shift-error" style="display: none;"></div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label class="form-label">الحالة</label>
                            <div class="switch-group">
                                <input type="checkbox" name="is_active" id="is_active" class="switch-input" value="1"
                                    {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="switch-label">
                                    <span class="switch-button"></span>
                                    <span class="switch-text">
                                        <i class="feather icon-check-circle"></i>
                                        تفعيل الحساب
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon security">
                            <i class="feather icon-info"></i>
                        </div>
                        <div>
                            <h3 class="section-title">معلومات النظام</h3>
                            <p class="section-subtitle">تفاصيل النظام للمستخدم</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">رقم المستخدم</label>
                            <div class="input-wrapper">
                                <input type="text" class="form-input" value="{{ $user->id }}" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">تاريخ الإنشاء</label>
                            <div class="input-wrapper">
                                <input type="text" class="form-input" value="{{ $user->created_at->format('Y-m-d H:i') }}" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">آخر تحديث</label>
                            <div class="input-wrapper">
                                <input type="text" class="form-input" value="{{ $user->updated_at->format('Y-m-d H:i') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="feather icon-save"></i>
                        تحديث المستخدم
                    </button>
                    <a href="{{ route('users.index') }}" class="btn-cancel">
                        <i class="feather icon-x"></i>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('userForm');
            const inputs = form.querySelectorAll('.form-input:not([readonly])');

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

                // Check password confirmation if password is provided
                const password = document.getElementById('password');
                const passwordConfirmation = document.getElementById('password_confirmation');
                if (password.value || passwordConfirmation.value) {
                    if (password.value !== passwordConfirmation.value) {
                        showError('password_confirmation', 'كلمة المرور وتأكيدها غير متطابقين');
                        isValid = false;
                    }
                }

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
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'swal-btn-confirm',
                            cancelButton: 'swal-btn-cancel'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading message
                            Swal.fire({
                                title: 'جاري الحفظ...',
                                allowOutsideClick: false,
                                didOpen: (modal) => {
                                    Swal.showLoading();
                                }
                            });
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
