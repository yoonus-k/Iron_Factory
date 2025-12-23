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
            <i class="feather icon-home"></i> {{ __('menu.dashboard') }}
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
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="section-title">معلومات العامل الأساسية</h3>
                    <p class="section-subtitle">أدخل البيانات المطلوبة لإضافة عامل جديد</p>
                </div>
            </div>

            <div class="form-grid">
                <!-- Worker Code -->
                <div class="form-group">
                    <label for="worker_code" class="form-label">
                        كود العامل
                        <span class="required">*</span>
                    </label>
                    <div class="input-group-with-button">
                        <div class="input-wrapper" style="flex: 1;">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

                <!-- Worker Name -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        اسم العامل
                        <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <input type="text" name="name" id="name"
                            class="form-input"
                            value="{{ old('name') }}" placeholder="أدخل اسم العامل" required>
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
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->role_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Username -->
                <div class="form-group">
                    <label for="username" class="form-label">
                        اسم المستخدم
                        <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <input type="text" name="username" id="username"
                            class="form-input"
                            value="{{ old('username') }}" placeholder="أدخل اسم المستخدم للدخول" required>
                    </div>
                    <small class="text-muted">يستخدم للدخول إلى النظام</small>
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
                        <input type="password" name="password" id="password"
                            class="form-input"
                            placeholder="أدخل كلمة المرور (6 أحرف على الأقل)" required>
                    </div>
                    <small class="text-muted">يجب أن تكون 6 أحرف على الأقل</small>
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
    }

    .text-muted {
        color: #9ca3af;
        font-size: 13px;
        margin-top: 5px;
        display: block;
    }

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
    }
</style>

<script>
    // Generate worker code
    function generateWorkerCode() {
        const btn = document.getElementById('generateBtn');
        const input = document.getElementById('worker_code');
        
        btn.disabled = true;
        btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; animation: spin 1s linear infinite;"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg> جاري التوليد...';
        
        fetch('{{ route("manufacturing.workers.generate-code") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            input.value = data.worker_code;
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><polyline points="20 6 9 17 4 12"></polyline></svg> تم!';
            btn.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
            
            setTimeout(() => {
                btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg> توليد';
                btn.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                btn.disabled = false;
            }, 2000);
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg> خطأ';
            btn.style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
            btn.disabled = false;
        });
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
</script>

@endsection
