@extends('master')

@section('title', 'إضافة مستخدم جديد')

@section('content')
<style>
    .um-header-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        margin-bottom: 2rem;
        border-radius: 12px;
        color: white;
    }

    .um-page-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .title-icon {
        width: 32px;
        height: 32px;
    }

    .um-breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .um-alert-custom {
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
    }

    .um-alert-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .um-alert-danger {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .um-alert-close {
        position: absolute;
        left: 1rem;
        background: none;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0.7;
    }

    .um-alert-close:hover {
        opacity: 1;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .section-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .section-icon.personal {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .section-icon.account {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .section-icon svg {
        width: 24px;
        height: 24px;
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin: 0;
        color: #2d3748;
    }

    .section-subtitle {
        font-size: 0.9rem;
        color: #718096;
        margin: 0.25rem 0 0 0;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .required {
        color: #e53e3e;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        right: 12px;
        width: 20px;
        height: 20px;
        color: #a0aec0;
        pointer-events: none;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem 2.5rem 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .input-group-with-button {
        display: flex;
        gap: 0.5rem;
    }

    .btn-generate {
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        transition: all 0.3s;
        white-space: nowrap;
    }

    .btn-generate:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-generate svg {
        width: 18px;
        height: 18px;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid #f0f0f0;
    }

    .btn {
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #e2e8f0;
        color: #4a5568;
    }

    .btn-secondary:hover {
        background: #cbd5e0;
    }

    .btn svg {
        width: 18px;
        height: 18px;
    }

    .help-text {
        font-size: 0.85rem;
        color: #718096;
        margin-top: 0.25rem;
    }
</style>

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
        إضافة مستخدم جديد
    </h1>
    <nav class="um-breadcrumb-nav">
        <span>
            <i class="feather icon-home"></i> {{ __('menu.dashboard') }}
        </span>
        <i class="feather icon-chevron-left"></i>
        <span>المستخدمين</span>
        <i class="feather icon-chevron-left"></i>
        <span>إضافة مستخدم جديد</span>
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
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="section-title">معلومات المستخدم الأساسية</h3>
                    <p class="section-subtitle">أدخل البيانات الأساسية للمستخدم</p>
                </div>
            </div>

            <div class="form-grid">
                <!-- Worker Code -->
                <div class="form-group">
                    <label for="worker_code" class="form-label">
                        كود المستخدم
                        <span class="required">*</span>
                    </label>
                    <div class="input-group-with-button">
                        <div class="input-wrapper" style="flex: 1;">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <input type="text" name="worker_code" id="worker_code" class="form-input"
                                value="{{ old('worker_code') }}" placeholder="أدخل كود المستخدم" required>
                        </div>
                        <button type="button" class="btn-generate" onclick="generateWorkerCode()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 4 23 10 17 10"></polyline>
                                <polyline points="1 20 1 14 7 14"></polyline>
                                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                            </svg>
                            توليد
                        </button>
                    </div>
                </div>

                <!-- Worker Name -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        اسم المستخدم
                        <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <input type="text" name="name" id="name" class="form-input"
                            value="{{ old('name') }}" placeholder="أدخل اسم المستخدم" required>
                    </div>
                </div>

                <!-- Role -->
                <div class="form-group">
                    <label for="role_id" class="form-label">
                        الدور الوظيفي
                        <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <select name="role_id" id="role_id" class="form-input" required>
                            <option value="">اختر الدور الوظيفي</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" data-role-code="{{ $role->role_code }}" 
                                {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->role_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <span class="help-text">سيتم تحديد صلاحيات المستخدم بناءً على الدور المختار</span>
                </div>
            </div>
        </div>

        <!-- Account Information Section -->
        <div class="form-section">
            <div class="section-header">
                <div class="section-icon account">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="section-title">معلومات الدخول للنظام</h3>
                    <p class="section-subtitle">أدخل اسم المستخدم وكلمة المرور للدخول للنظام</p>
                </div>
            </div>

            <div class="form-grid">
                <!-- Username -->
                <div class="form-group">
                    <label for="username" class="form-label">
                        اسم المستخدم للدخول
                        <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <input type="text" name="username" id="username" class="form-input"
                            value="{{ old('username') }}" placeholder="أدخل اسم المستخدم" required>
                    </div>
                    <span class="help-text">سيستخدم هذا الاسم لتسجيل الدخول للنظام</span>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        كلمة المرور
                        <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <input type="password" name="password" id="password" class="form-input"
                            placeholder="أدخل كلمة المرور (6 أحرف على الأقل)" required minlength="6">
                    </div>
                    <span class="help-text">يجب أن تكون كلمة المرور 6 أحرف على الأقل</span>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="window.location='{{ route('manufacturing.workers.index') }}'">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                إلغاء
            </button>
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                حفظ المستخدم
            </button>
        </div>
    </form>
</div>

<script>
    function generateWorkerCode() {
        const btn = event.target.closest('.btn-generate');
        const codeInput = document.getElementById('worker_code');
        const roleSelect = document.getElementById('role_id');
        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const roleCode = selectedOption.getAttribute('data-role-code');

        if (!roleCode) {
            alert('الرجاء اختيار الدور الوظيفي أولاً');
            return;
        }

        // Show loading state
        btn.disabled = true;
        btn.classList.add('loading');
        const originalText = btn.innerHTML;
        btn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
            جاري التوليد...
        `;

        // Position map
        const positionMap = {
            'WORKER': 'worker',
            'SUPERVISOR': 'supervisor',
            'STAGE1_WORKER': 'stage1_worker',
            'STAGE2_WORKER': 'stage2_worker',
            'STAGE3_WORKER': 'stage3_worker',
            'STAGE4_WORKER': 'stage4_worker',
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

                // Success state
                btn.classList.remove('loading');
                btn.classList.add('success');
                btn.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    تم التوليد
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
                    فشل
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

    // Auto-generate code when role changes
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role_id');
        const codeInput = document.getElementById('worker_code');

        roleSelect.addEventListener('change', function() {
            if (!codeInput.value || confirm('هل تريد توليد كود جديد بناءً على الدور المختار؟')) {
                const selectedOption = this.options[this.selectedIndex];
                const roleCode = selectedOption.getAttribute('data-role-code');
                if (roleCode) {
                    generateWorkerCode();
                }
            }
        });
    });
</script>

<style>
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .btn-generate.loading {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .btn-generate.success {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    }

    .btn-generate.error {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
    }
</style>
@endsection
