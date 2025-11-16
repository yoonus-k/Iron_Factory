@extends('master')

@section('title', 'إضافة عامل جديد')

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
                إضافة عامل جديد
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>العمال</span>
                <i class="feather icon-chevron-left"></i>
                <span>إضافة عامل جديد</span>
            </nav>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <form method="POST" action="{{ route('manufacturing.workers.store') }}" id="workerForm">
                @csrf

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
                            <p class="section-subtitle">أدخل البيانات الأساسية للعامل</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="worker_code" class="form-label">
                                كود العامل
                                <span class="required">*</span>
                            </label>
                            <div class="input-group-with-button">
                                <div class="input-wrapper" style="flex: 1;">
                                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <input type="text" name="worker_code" id="worker_code"
                                        class="form-input"
                                        value="{{ old('worker_code') }}" placeholder="أدخل كود العامل" required>
                                </div>
                                <button type="button" class="btn-generate" onclick="generateWorkerCode()" id="generateBtn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="23 4 23 10 17 10"></polyline>
                                        <polyline points="1 20 1 14 7 14"></polyline>
                                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                                    </svg>
                                    توليد 
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="form-label">
                                اسم العامل
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <input type="text" name="name" id="name"
                                    class="form-input"
                                    value="{{ old('name') }}" placeholder="أدخل اسم العامل" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="national_id" class="form-label">
                                رقم الهوية
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                <input type="text" name="national_id" id="national_id"
                                    class="form-input"
                                    value="{{ old('national_id') }}" placeholder="أدخل رقم الهوية">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">
                                رقم الهاتف
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <input type="text" name="phone" id="phone"
                                    class="form-input"
                                    value="{{ old('phone') }}" placeholder="أدخل رقم الهاتف">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">
                                البريد الإلكتروني
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <input type="email" name="email" id="email"
                                    class="form-input"
                                    value="{{ old('email') }}" placeholder="أدخل البريد الإلكتروني">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="position" class="form-label">
                                الوظيفة
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
                                <select name="position" id="position"
                                    class="form-input" required>
                                    <option value="">اختر الوظيفة</option>
                                    <option value="worker" {{ old('position') == 'worker' ? 'selected' : '' }}>عامل</option>
                                    <option value="supervisor" {{ old('position') == 'supervisor' ? 'selected' : '' }}>مشرف</option>
                                    <option value="technician" {{ old('position') == 'technician' ? 'selected' : '' }}>فني</option>
                                    <option value="quality_inspector" {{ old('position') == 'quality_inspector' ? 'selected' : '' }}>مفتش جودة</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon account">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                <line x1="8" y1="21" x2="16" y2="21"></line>
                                <line x1="12" y1="17" x2="12" y2="21"></line>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">معلومات العمل</h3>
                            <p class="section-subtitle">أدخل بيانات العمل للعامل</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <!-- تم إزالة تفضيل الوردية - العمال يعملون حسب الجدولة -->
                        <input type="hidden" name="shift_preference" value="any">

                        <div class="form-group">
                            <label for="hourly_rate" class="form-label">
                                الأجر بالساعة (IQD)
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                <input type="number" name="hourly_rate" id="hourly_rate"
                                    class="form-input"
                                    value="{{ old('hourly_rate', 0) }}" step="0.01" min="0" placeholder="أدخل الأجر بالساعة" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="hire_date" class="form-label">
                                تاريخ التعيين
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
                                <input type="date" name="hire_date" id="hire_date"
                                    class="form-input"
                                    value="{{ old('hire_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">المراحل المسموح بها</label>
                            <div class="workers-selection">
                                @for($i = 1; $i <= 4; $i++)
                                <div class="worker-item">
                                    <input type="checkbox" id="stage{{ $i }}" name="allowed_stages[]" value="{{ $i }}"
                                        {{ (is_array(old('allowed_stages')) && in_array($i, old('allowed_stages'))) ? 'checked' : '' }}>
                                    <label for="stage{{ $i }}">المرحلة {{ $i }}</label>
                                </div>
                                @endfor
                                <p class="text-muted" style="margin-top: 10px;">اترك فارغاً للسماح بجميع المراحل</p>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <div class="switch-group">
                                <input type="checkbox" id="is_active" name="is_active" value="1" class="switch-input" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active" class="switch-label">
                                    <span class="switch-button"></span>
                                    <span class="switch-text">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                        </svg>
                                        تفعيل العامل
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Account Management Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon security">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">حساب الدخول للنظام</h3>
                            <p class="section-subtitle">إدارة حساب الدخول للعامل (اختياري)</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="allow_system_access" class="form-label">
                                السماح بالدخول للنظام؟
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <select name="allow_system_access" id="allow_system_access"
                                    class="form-input" onchange="toggleUserAccountFields()">
                                    <option value="no" {{ old('allow_system_access') == 'no' ? 'selected' : '' }}>لا - عامل فقط بدون حساب</option>
                                    <option value="existing" {{ old('allow_system_access') == 'existing' ? 'selected' : '' }}>نعم - ربط بحساب موجود</option>
                                    <option value="new" {{ old('allow_system_access') == 'new' ? 'selected' : '' }}>نعم - إنشاء حساب جديد</option>
                                </select>
                            </div>
                        </div>

                        <!-- Existing User Selection -->
                        <div id="existing_user_section" class="form-group full-width" style="display: none;">
                            <label for="user_id" class="form-label">
                                اختر المستخدم
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <select name="user_id" id="user_id"
                                    class="form-input">
                                    <option value="">اختر مستخدم</option>
                                    @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="text-muted">يظهر فقط المستخدمين الذين ليس لديهم ملف عامل مسبقاً</small>
                        </div>

                        <!-- New User Creation Fields -->
                        <div id="new_user_section" style="display: none;" class="full-width">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="new_username" class="form-label">
                                        اسم المستخدم
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        <input type="text" name="new_username" id="new_username"
                                            class="form-input"
                                            value="{{ old('new_username') }}" placeholder="مثال: ahmad.ali">
                                    </div>
                                    <small class="text-muted">اسم تسجيل الدخول (بالإنجليزية بدون مسافات)</small>
                                </div>

                                <div class="form-group">
                                    <label for="new_email" class="form-label">
                                        البريد الإلكتروني
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                            <polyline points="22,6 12,13 2,6"></polyline>
                                        </svg>
                                        <input type="email" name="new_email" id="new_email"
                                            class="form-input"
                                            value="{{ old('new_email') }}" placeholder="example@company.com">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="new_password" class="form-label">
                                        كلمة المرور
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                        <input type="password" name="new_password" id="new_password"
                                            class="form-input"
                                            placeholder="أدخل كلمة المرور">
                                    </div>
                                    <small class="text-muted">يجب أن تكون 8 أحرف على الأقل</small>
                                </div>

                                <div class="form-group">
                                    <label for="new_password_confirmation" class="form-label">
                                        تأكيد كلمة المرور
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                        <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                            class="form-input"
                                            placeholder="أعد إدخال كلمة المرور">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-warning" style="margin-top: 15px;">
                                <i data-feather="alert-triangle"></i>
                                <strong>تنبيه:</strong> سيتم إنشاء حساب مستخدم جديد تلقائياً عند حفظ بيانات العامل.
                            </div>
                        </div>
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
                            <p class="section-subtitle">أدخل معلومات إضافية للعامل</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="emergency_contact" class="form-label">
                                اسم جهة الاتصال للطوارئ
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <input type="text" name="emergency_contact" id="emergency_contact"
                                    class="form-input"
                                    value="{{ old('emergency_contact') }}" placeholder="أدخل اسم جهة الاتصال">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="emergency_phone" class="form-label">
                                رقم هاتف الطوارئ
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <input type="text" name="emergency_phone" id="emergency_phone"
                                    class="form-input"
                                    value="{{ old('emergency_phone') }}" placeholder="أدخل رقم هاتف الطوارئ">
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
                                    class="form-input" placeholder="أدخل ملاحظات للعامل">{{ old('notes') }}</textarea>
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
                        حفظ العامل
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
    }
</style>

<script>
    // Generate worker code automatically
    function generateWorkerCode() {
        const position = document.getElementById('position').value;
        const btn = document.getElementById('generateBtn');
        const codeInput = document.getElementById('worker_code');
        
        if (!position) {
            alert('الرجاء اختيار الوظيفة أولاً');
            document.getElementById('position').focus();
            return;
        }

        // Add loading state
        btn.disabled = true;
        btn.classList.add('loading');
        const originalText = btn.innerHTML;
        btn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
            جاري التوليد...
        `;
        
        fetch(`{{ route('manufacturing.workers.generate-code') }}?position=${position}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('فشل في توليد الكود');
                }
                return response.json();
            })
            .then(data => {
                codeInput.value = data.worker_code;
                
                // Success state
                btn.classList.remove('loading');
                btn.classList.add('success');
                btn.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    تم التوليد بنجاح
                `;
                
                // Reset after 2 seconds
                setTimeout(() => {
                    btn.classList.remove('success');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 2000);
            })
            .catch(error => {
                console.error('Error generating code:', error);
                
                // Error state
                btn.classList.remove('loading');
                btn.classList.add('error');
                btn.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    فشل التوليد
                `;
                
                alert('حدث خطأ في توليد الكود. الرجاء المحاولة مرة أخرى.');
                
                // Reset after 2 seconds
                setTimeout(() => {
                    btn.classList.remove('error');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 2000);
            });
    }

    // Auto-generate when position changes
    document.addEventListener('DOMContentLoaded', function() {
        const positionSelect = document.getElementById('position');
        const codeInput = document.getElementById('worker_code');
        
        positionSelect.addEventListener('change', function() {
            if (!codeInput.value || confirm('هل تريد توليد كود جديد بناءً على الوظيفة المختارة؟')) {
                generateWorkerCode();
            }
        });
    });

    // Toggle user account fields based on selection
    function toggleUserAccountFields() {
        const accessType = document.getElementById('allow_system_access').value;
        const existingSection = document.getElementById('existing_user_section');
        const newSection = document.getElementById('new_user_section');
        const userIdSelect = document.getElementById('user_id');
        
        // Hide all sections first
        existingSection.style.display = 'none';
        newSection.style.display = 'none';
        
        // Clear user_id if not using existing user
        if (accessType !== 'existing') {
            userIdSelect.value = '';
        }
        
        // Clear new user fields if not creating new user
        if (accessType !== 'new') {
            document.getElementById('new_username').value = '';
            document.getElementById('new_email').value = '';
            document.getElementById('new_password').value = '';
            document.getElementById('new_password_confirmation').value = '';
        }
        
        // Show appropriate section
        if (accessType === 'existing') {
            existingSection.style.display = 'block';
            userIdSelect.required = true;
        } else if (accessType === 'new') {
            newSection.style.display = 'block';
            // Make new user fields required
            document.getElementById('new_username').required = true;
            document.getElementById('new_email').required = true;
            document.getElementById('new_password').required = true;
            document.getElementById('new_password_confirmation').required = true;
        } else {
            // No access - remove required
            userIdSelect.required = false;
            document.getElementById('new_username').required = false;
            document.getElementById('new_email').required = false;
            document.getElementById('new_password').required = false;
            document.getElementById('new_password_confirmation').required = false;
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleUserAccountFields();
        
        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection