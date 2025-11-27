@extends('master')

@section('title', 'تعديل بيانات العامل')

@section('content')

        <!-- Header -->
        <div class="um-header-section">
            @if(session('success'))
            <div class="um-alert-custom um-alert-success" role="alert">
                <i class="feather icon-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="um-alert-custom um-alert-danger" role="alert">
                <i class="feather icon-alert-circle"></i>
                {{ session('error') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
            @endif

            @if($errors->any())
            <div class="um-alert-custom um-alert-danger" role="alert">
                <i class="feather icon-alert-circle"></i>
                <ul style="margin: 0; padding-right: 20px;">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
            @endif
            <h1 class="um-page-title">
                <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                تعديل بيانات العامل
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>العمال</span>
                <i class="feather icon-chevron-left"></i>
                <span>تعديل: {{ $worker->name }}</span>
            </nav>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <form method="POST" action="{{ route('manufacturing.workers.update', $worker->id) }}" id="workerForm">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon personal">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">المعلومات الأساسية</h3>
                            <p class="section-subtitle">تعديل البيانات الأساسية للعامل</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="worker_code" class="form-label">
                                كود العامل
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text" id="worker_code" name="worker_code" class="form-input"
                                       value="{{ old('worker_code', $worker->worker_code) }}" required>
                                @error('worker_code')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="form-label">
                                اسم العامل
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text" id="name" name="name" class="form-input"
                                       value="{{ old('name', $worker->name) }}" required>
                                @error('name')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="national_id" class="form-label">
                                رقم الهوية
                            </label>
                            <div class="input-wrapper">
                                <input type="text" id="national_id" name="national_id" class="form-input"
                                       value="{{ old('national_id', $worker->national_id) }}">
                                @error('national_id')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">
                                رقم الهاتف
                            </label>
                            <div class="input-wrapper">
                                <input type="tel" id="phone" name="phone" class="form-input"
                                       value="{{ old('phone', $worker->phone) }}">
                                @error('phone')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">
                                البريد الإلكتروني
                            </label>
                            <div class="input-wrapper">
                                <input type="email" id="email" name="email" class="form-input"
                                       value="{{ old('email', $worker->email) }}">
                                @error('email')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="position" class="form-label">
                                الوظيفة
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <select id="position" name="role_id" class="form-input" required>
                                    <option value="">اختر الوظيفة</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" data-role-code="{{ $role->role_code }}" {{ old('role_id', $worker->user?->role_id ?? collect($roles)->firstWhere('role_code', strtoupper($worker->position))?->id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->role_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon account">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">معلومات العمل</h3>
                            <p class="section-subtitle">تعديل بيانات العمل للعامل</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <input type="hidden" name="shift_preference" value="any">

                        <div class="form-group">
                            <label for="hourly_rate" class="form-label">
                                الراتب بالساعة
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="number" id="hourly_rate" name="hourly_rate" class="form-input"
                                       value="{{ old('hourly_rate', $worker->hourly_rate) }}" step="0.01" required>
                                @error('hourly_rate')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="hire_date" class="form-label">
                                تاريخ التوظيف
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="date" id="hire_date" name="hire_date" class="form-input"
                                       value="{{ old('hire_date', $worker->hire_date ? $worker->hire_date->format('Y-m-d') : '') }}" required>
                                @error('hire_date')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">المراحل المسموح بها</label>
                            <div class="workers-selection">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="allowed_stages[]" value="1"
                                           {{ in_array(1, old('allowed_stages', $worker->allowed_stages ?? [])) ? 'checked' : '' }}>
                                    <span>المرحلة 1 - الاستقبال</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="allowed_stages[]" value="2"
                                           {{ in_array(2, old('allowed_stages', $worker->allowed_stages ?? [])) ? 'checked' : '' }}>
                                    <span>المرحلة 2 - التجهيز</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="allowed_stages[]" value="3"
                                           {{ in_array(3, old('allowed_stages', $worker->allowed_stages ?? [])) ? 'checked' : '' }}>
                                    <span>المرحلة 3 - المعالجة</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="allowed_stages[]" value="4"
                                           {{ in_array(4, old('allowed_stages', $worker->allowed_stages ?? [])) ? 'checked' : '' }}>
                                    <span>المرحلة 4 - التعبئة</span>
                                </label>
                            </div>
                            @error('allowed_stages')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group full-width">
                            <div class="switch-group">
                                <label for="is_active" class="form-label">
                                    <span>حالة العامل</span>
                                </label>
                                <div class="toggle-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $worker->is_active) ? 'checked' : '' }} class="toggle-input">
                                    <label for="is_active" class="toggle-label">
                                        <span class="toggle-inner"></span>
                                        <span class="toggle-switch-label">{{ $worker->is_active ? 'نشط' : 'معطل' }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Section -->
                <div class="form-section" id="permissionsSection" style="display: none;">
                    <div class="section-header">
                        <div class="section-icon security">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="1"></circle>
                                <path d="M12 1v6m6.16-1.16l-4.24 4.24m6 6l-4.24-4.24m4.24 4.24l-4.24 4.24m-6-6l4.24-4.24"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">الصلاحيات التلقائية</h3>
                            <p class="section-subtitle">الصلاحيات المعينة حسب الوظيفة المختارة</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <div id="permissionsContainer" class="permissions-list">
                                <p class="text-muted" style="text-align: center; padding: 20px;">
                                    <i class="feather icon-info"></i> جاري تحميل الصلاحيات...
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Account Management Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon security">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">حساب الدخول للنظام</h3>
                            <p class="section-subtitle">إدارة حساب الدخول للعامل</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        @if($worker->user)
                        <div class="form-group full-width">
                            <div class="alert alert-info">
                                <i class="feather icon-info"></i>
                                <strong>ملاحظة:</strong> هذا العامل مرتبط بحساب مستخدم.
                                <br>
                                <strong>اسم المستخدم:</strong> {{ $worker->user->username }}
                                <br>
                                <strong>البريد الإلكتروني:</strong> {{ $worker->user->email }}
                                <br>
                                <strong>الرول الحالي:</strong> {{ $worker->user->roleRelation?->role_name ?? 'بدون رول' }}
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="user_id" class="form-label">
                                تغيير حساب المستخدم (اختياري)
                            </label>
                            <div class="input-wrapper">
                                <select id="user_id" name="user_id" class="form-input">
                                    <option value="">-- بدون حساب مستخدم --</option>
                                    @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}" {{ $worker->user_id === $user->id ? 'selected' : '' }}>
                                        {{ $user->username }} ({{ $user->email }})
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">الرول الوظيفي سيتم تحديثه تلقائياً بناءً على وظيفة العامل</small>
                            </div>
                            @error('user_id')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        @else
                        <div class="form-group full-width">
                            <label for="allow_system_access_edit" class="form-label">
                                السماح بالدخول للنظام؟
                            </label>
                            <div class="input-wrapper">
                                <select id="allow_system_access_edit" name="allow_system_access" class="form-input" onchange="toggleUserAccountFieldsEdit()">
                                    <option value="no" {{ old('allow_system_access') == 'no' ? 'selected' : '' }}>لا - عامل فقط بدون حساب</option>
                                    <option value="existing" {{ old('allow_system_access') == 'existing' ? 'selected' : '' }}>نعم - ربط بحساب موجود</option>
                                    <option value="new" {{ old('allow_system_access') == 'new' ? 'selected' : '' }}>نعم - إنشاء حساب جديد</option>
                                </select>
                            </div>
                        </div>

                        <!-- Existing User Selection -->
                        <div id="existing_user_section_edit" class="form-group full-width" style="display: none;">
                            <label for="user_id" class="form-label">
                                اختر المستخدم
                            </label>
                            <div class="input-wrapper">
                                <select id="user_id" name="user_id" class="form-input">
                                    <option value="">-- اختر المستخدم --</option>
                                    @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }} ({{ $user->email }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="text-muted">يظهر فقط المستخدمين الذين ليس لديهم ملف عامل مسبقاً</small>
                        </div>

                        <!-- New User Creation Fields -->
                        <div id="new_user_section_edit" style="display: none;" class="full-width">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="new_username_edit" class="form-label">
                                        اسم المستخدم
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="text" name="new_username" id="new_username_edit" class="form-input"
                                            value="{{ old('new_username') }}" placeholder="مثال: ahmad.ali">
                                    </div>
                                    <small class="text-muted">اسم تسجيل الدخول (بالإنجليزية بدون مسافات)</small>
                                </div>

                                <div class="form-group">
                                    <label for="new_email_edit" class="form-label">
                                        البريد الإلكتروني
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="email" name="new_email" id="new_email_edit" class="form-input"
                                            value="{{ old('new_email') }}" placeholder="example@company.com">
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning" style="margin-top: 15px;">
                                <i data-feather="alert-triangle"></i>
                                <strong>تنبيه:</strong> سيتم إنشاء حساب مستخدم جديد وإرسال كلمة المرور عبر البريد الإلكتروني.
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon address">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">معلومات إضافية</h3>
                            <p class="section-subtitle">تعديل معلومات إضافية للعامل</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="emergency_contact" class="form-label">
                                جهة الاتصال في الطوارئ
                            </label>
                            <div class="input-wrapper">
                                <input type="text" id="emergency_contact" name="emergency_contact" class="form-input"
                                       value="{{ old('emergency_contact', $worker->emergency_contact) }}">
                                @error('emergency_contact')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="emergency_phone" class="form-label">
                                رقم هاتف الطوارئ
                            </label>
                            <div class="input-wrapper">
                                <input type="tel" id="emergency_phone" name="emergency_phone" class="form-input"
                                       value="{{ old('emergency_phone', $worker->emergency_phone) }}">
                                @error('emergency_phone')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <div class="input-wrapper">
                                <textarea id="notes" name="notes" class="form-input" rows="4">{{ old('notes', $worker->notes) }}</textarea>
                                @error('notes')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
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
                        حفظ التعديلات
                    </button>

                    <a href="{{ route('manufacturing.workers.index') }}" class="btn-cancel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        إلغاء
                    </a>

                </div>
            </form>
        </div>

<style>
    .input-group-with-button {
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .btn-generate {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.25);
        min-height: 48px;
    }

    .btn-generate:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(102, 126, 234, 0.35);
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }

    .btn-generate:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.25);
    }

    .btn-generate:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .btn-generate svg {
        width: 18px;
        height: 18px;
        animation: spin 0s linear infinite;
    }

    .btn-generate.loading svg {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .btn-generate.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .btn-generate.error {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    /* Permissions Styles */
    .permissions-list {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 15px;
        max-height: 400px;
        overflow-y: auto;
    }

    .permission-group {
        margin-bottom: 15px;
    }

    .permission-group-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e5e7eb;
        font-size: 14px;
    }

    .permission-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 8px 10px;
        margin-bottom: 6px;
        background: white;
        border-radius: 6px;
        border-left: 3px solid #dbeafe;
        transition: all 0.2s ease;
    }

    .permission-item:hover {
        background: #f3f4f6;
        border-left-color: #60a5fa;
    }

    .permission-item input[type="checkbox"] {
        margin-top: 3px;
        cursor: pointer;
        width: 18px;
        height: 18px;
        accent-color: #667eea;
    }

    .permission-item-content {
        flex: 1;
    }

    .permission-name {
        font-weight: 500;
        color: #1f2937;
        font-size: 14px;
    }

    .permission-description {
        font-size: 12px;
        color: #6b7280;
        margin-top: 2px;
    }

    .text-muted {
        color: #9ca3af;
        font-size: 14px;
    }

    /* Alert Styles */
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 15px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .alert-info {
        background-color: #e0f2fe;
        border-left: 4px solid #0284c7;
        color: #0c4a6e;
    }

    .alert strong {
        font-weight: 600;
    }

    /* Reduce icon sizes */
    .section-icon svg {
        width: 20px;
        height: 20px;
    }

    .input-icon {
        width: 18px;
        height: 18px;
    }

    .title-icon {
        width: 24px;
        height: 24px;
    }

    @media (max-width: 768px) {
        .input-group-with-button {
            flex-direction: column;
        }

        .btn-generate {
            width: 100%;
            justify-content: center;
        }

        .section-icon svg {
            width: 18px;
            height: 18px;
        }

        .input-icon {
            width: 16px;
            height: 16px;
        }

        .permissions-list {
            max-height: 300px;
        }
    }
</style>

<script>
    // جلب الصلاحيات حسب الدور
    function loadPermissionsByRole(roleId) {
        if (!roleId) {
            document.getElementById('permissionsSection').style.display = 'none';
            return;
        }

        const permissionsSection = document.getElementById('permissionsSection');
        const permissionsContainer = document.getElementById('permissionsContainer');

        // عرض حالة التحميل
        permissionsContainer.innerHTML = '<p class="text-muted" style="text-align: center; padding: 20px;"><i class="feather icon-loader"></i> جاري تحميل الصلاحيات...</p>';
        permissionsSection.style.display = 'block';

        fetch(`{{ route('manufacturing.workers.permissions-by-role') }}?role_id=${roleId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('فشل في جلب الصلاحيات');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.permissions.length > 0) {
                    renderPermissions(data.permissions);
                } else {
                    permissionsContainer.innerHTML = '<p class="text-muted" style="text-align: center; padding: 20px;">لا توجد صلاحيات محددة لهذه الوظيفة</p>';
                }
            })
            .catch(error => {
                console.error('Error loading permissions:', error);
                permissionsContainer.innerHTML = '<p class="text-muted" style="text-align: center; padding: 20px; color: #ef4444;"><i class="feather icon-alert-circle"></i> حدث خطأ في تحميل الصلاحيات</p>';
            });
    }

    // عرض الصلاحيات مجمعة حسب المجموعة
    function renderPermissions(permissions) {
        const container = document.getElementById('permissionsContainer');
        container.innerHTML = '';

        // تجميع الصلاحيات حسب المجموعة
        const grouped = {};
        permissions.forEach(permission => {
            if (!grouped[permission.group_name]) {
                grouped[permission.group_name] = [];
            }
            grouped[permission.group_name].push(permission);
        });

        // عرض كل مجموعة
        Object.keys(grouped).forEach(groupName => {
            const groupDiv = document.createElement('div');
            groupDiv.className = 'permission-group';

            const titleDiv = document.createElement('div');
            titleDiv.className = 'permission-group-title';
            titleDiv.textContent = groupName;
            groupDiv.appendChild(titleDiv);

            grouped[groupName].forEach(permission => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'permission-item';
                itemDiv.innerHTML = `
                    <input type="checkbox"
                           name="permissions[]"
                           value="${permission.id}"
                           checked
                           disabled
                           class="permission-checkbox">
                    <div class="permission-item-content">
                        <div class="permission-name">${permission.display_name}</div>
                        <div class="permission-description">${permission.name}</div>
                    </div>
                `;
                groupDiv.appendChild(itemDiv);
            });

            container.appendChild(groupDiv);
        });

        // إضافة رسالة معلومات
        const infoDiv = document.createElement('div');
        infoDiv.style.cssText = 'margin-top: 15px; padding: 12px 15px; background: #eff6ff; border-left: 3px solid #3b82f6; border-radius: 4px; font-size: 13px; color: #1e40af;';
        infoDiv.innerHTML = '<strong>ملاحظة:</strong> الصلاحيات سيتم تحديثها تلقائياً للعامل عند الحفظ بناءً على الوظيفة المختارة.';
        container.appendChild(infoDiv);
    }

    // Generate worker code by role code
    function generateWorkerCodeByRole(roleCode) {
        const codeInput = document.getElementById('worker_code');

        if (!roleCode) {
            alert('الرجاء اختيار الوظيفة أولاً');
            return;
        }

        // Map role code to position
        const positionMap = {
            'WORKER': 'worker',
            'SUPERVISOR': 'supervisor',
            'TECHNICIAN': 'technician',
            'QUALITY_INSPECTOR': 'quality_inspector'
        };

        const position = positionMap[roleCode] || 'worker';

        fetch(`{{ route('manufacturing.workers.generate-code') }}?position=${position}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('فشل في توليد الكود');
                }
                return response.json();
            })
            .then(data => {
                codeInput.value = data.worker_code;
            })
            .catch(error => {
                console.error('Error generating code:', error);
            });
    }

    // Toggle user account fields based on selection for edit page
    function toggleUserAccountFieldsEdit() {
        const accessType = document.getElementById('allow_system_access_edit').value;
        const existingSection = document.getElementById('existing_user_section_edit');
        const newSection = document.getElementById('new_user_section_edit');
        const userIdSelect = document.getElementById('user_id');

        // Hide all sections first
        existingSection.style.display = 'none';
        newSection.style.display = 'none';

        // Remove required from all fields first
        userIdSelect.required = false;
        const newUsernameEdit = document.getElementById('new_username_edit');
        const newEmailEdit = document.getElementById('new_email_edit');
        if (newUsernameEdit) newUsernameEdit.required = false;
        if (newEmailEdit) newEmailEdit.required = false;

        // Clear user_id if not using existing user
        if (accessType !== 'existing') {
            userIdSelect.value = '';
        }

        // Clear new user fields if not creating new user
        if (accessType !== 'new') {
            if (newUsernameEdit) newUsernameEdit.value = '';
            if (newEmailEdit) newEmailEdit.value = '';
        }

        // Show appropriate section
        if (accessType === 'existing') {
            existingSection.style.display = 'block';
            userIdSelect.required = true;
        } else if (accessType === 'new') {
            newSection.style.display = 'block';
            // Make new user fields required
            if (newUsernameEdit) newUsernameEdit.required = true;
            if (newEmailEdit) newEmailEdit.required = true;
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // جلب الصلاحيات الحالية عند تحميل الصفحة
        const positionSelect = document.getElementById('position');
        if (positionSelect && positionSelect.value) {
            // Permissions auto-sync on role change
        }

        // عند تغيير الوظيفة، تحديث البيانات
        positionSelect.addEventListener('change', function() {
            // Update on role change
        });

        // Initialize toggle if edit mode and no user
        @if(!$worker->user)
        toggleUserAccountFieldsEdit();
        @endif

        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>

        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection
