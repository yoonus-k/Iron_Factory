@extends('master')

@section('title', __('workers.add_new_worker'))

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
                {{ __('workers.add_new_worker') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('app.menu.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('workers.workers') }}</span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('workers.add_new_worker') }}</span>
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
                            </div>
                            <div>
                                <h3 class="section-title">{{ __('workers.basic_information') }}</h3>
                                <p class="section-subtitle">{{ __('workers.basic_info_desc') }}</p>
                            </div>
                        </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="worker_code" class="form-label">
                                {{ __('workers.worker_code') }}
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
                                        value="{{ old('worker_code') }}" placeholder="{{ __('workers.enter_worker_code') }}" required>
                                </div>
                                <button type="button" class="btn-generate" onclick="generateWorkerCode()" id="generateBtn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="23 4 23 10 17 10"></polyline>
                                        <polyline points="1 20 1 14 7 14"></polyline>
                                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                                    </svg>
                                    {{ __('workers.generate') }}

                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="form-label">
                                {{ __('workers.worker_name') }}
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
                                    value="{{ old('name') }}" placeholder="{{ __('workers.enter_worker_name') }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="national_id" class="form-label">
                                {{ __('workers.national_id') }}
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                <input type="text" name="national_id" id="national_id"
                                    class="form-input"
                                    value="{{ old('national_id') }}" placeholder="{{ __('workers.enter_national_id') }}">

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">
                                {{ __('workers.phone') }}
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <input type="text" name="phone" id="phone"
                                    class="form-input"
                                    value="{{ old('phone') }}" placeholder="{{ __('workers.enter_phone') }}">

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">
                                {{ __('workers.email') }}
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <input type="email" name="email" id="email"
                                    class="form-input"
                                    value="{{ old('email') }}" placeholder="{{ __('workers.enter_email') }}">

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="position" class="form-label">
                                {{ __('workers.position') }}
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
                                <select name="role_id" id="position"
                                    class="form-input" required>
                                    <option value="">{{ __('workers.select_position') }}</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" data-role-code="{{ $role->role_code }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->role_name }}
                                    </option>
                                    @endforeach
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
                            <h3 class="section-title">{{ __('workers.work_information') }}</h3>
                            <p class="section-subtitle">{{ __('workers.work_info_desc') }}</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <!-- تم إزالة تفضيل الوردية - العمال يعملون حسب الجدولة -->
                        <input type="hidden" name="shift_preference" value="any">

                        <div class="form-group">
                            <label for="hourly_rate" class="form-label">
                                {{ __('workers.hourly_rate') }} (IQD)
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
                                    value="{{ old('hourly_rate', 0) }}" step="0.01" min="0" placeholder="{{ __('workers.enter_hourly_rate') }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="hire_date" class="form-label">
                                {{ __('workers.hire_date') }}
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
                            <label class="form-label">{{ __('workers.allowed_stages') }}</label>
                            <div class="workers-selection">
                                @for($i = 1; $i <= 4; $i++)
                                <div class="worker-item">
                                    <input type="checkbox" id="stage{{ $i }}" name="allowed_stages[]" value="{{ $i }}"
                                        {{ (is_array(old('allowed_stages')) && in_array($i, old('allowed_stages'))) ? 'checked' : '' }}>
                                    <label for="stage{{ $i }}">{{ __('workers.stage') }} {{ $i }}</label>
                                </div>
                                @endfor
                                <p class="text-muted" style="margin-top: 10px;">{{ __('workers.leave_empty_for_all_stages') }}</p>
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
                                        {{ __('workers.enable') }} {{ __('workers.worker') }}
                                    </span>
                                </label>
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
                            <h3 class="section-title">{{ __('workers.account_management') }}</h3>
                            <p class="section-subtitle">{{ __('workers.account_info_desc') }}</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <div id="permissionsContainer" class="permissions-list">
                                <p class="text-muted" style="text-align: center; padding: 20px;">
                                    <i class="feather icon-info"></i> {{ __('workers.select_position_first') }} {{ __('workers.to_view_permissions') }}
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
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('workers.user_account_management') }}</h3>
                            <p class="section-subtitle">{{ __('workers.user_account_desc') }}</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="allow_system_access" class="form-label">
                                {{ __('workers.system_access') }}?
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
                                    <option value="no" {{ old('allow_system_access') == 'no' ? 'selected' : '' }}>{{ __('workers.worker_only') }}</option>
                                    <option value="existing" {{ old('allow_system_access') == 'existing' ? 'selected' : '' }}>{{ __('workers.link_existing_account') }}</option>
                                    <option value="new" {{ old('allow_system_access') == 'new' ? 'selected' : '' }}>{{ __('workers.create_new_account') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Existing User Selection -->
                        <div id="existing_user_section" class="form-group full-width" style="display: none;">
                            <label for="user_id" class="form-label">
                                {{ __('workers.select_user') }}
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
                                    <option value="">{{ __('workers.select_user') }}</option>
                                    @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="text-muted">{{ __('workers.users_without_worker_file') }}</small>
                        </div>

                        <!-- New User Creation Fields -->
                        <div id="new_user_section" style="display: none;" class="full-width">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="new_username" class="form-label">
                                        {{ __('workers.username') }}
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
                                            value="{{ old('new_username') }}" placeholder="{{ __('workers.enter_username') }}">

                                    </div>
                                    <small class="text-muted">{{ __('workers.username_desc') }}</small>
                                </div>

                                <div class="form-group">
                                    <label for="new_email" class="form-label">
                                        {{ __('workers.email') }}
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
                            </div>

                            <div class="alert alert-warning" style="margin-top: 15px;">
                                <i data-feather="alert-triangle"></i>
                                <strong>{{ __('workers.note') }}:</strong> {{ __('workers.password_will_be_sent') }}

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
                            <h3 class="section-title">{{ __('workers.additional_information') }}</h3>
                            <p class="section-subtitle">{{ __('workers.additional_info_desc') }}</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="emergency_contact" class="form-label">
                                {{ __('workers.emergency_contact') }}
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
                                    value="{{ old('emergency_contact') }}" placeholder="{{ __('workers.enter_emergency_contact') }}">

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="emergency_phone" class="form-label">
                                {{ __('workers.emergency_phone') }}
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <input type="text" name="emergency_phone" id="emergency_phone"
                                    class="form-input"
                                    value="{{ old('emergency_phone') }}" placeholder="{{ __('workers.enter_emergency_phone') }}">

                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="notes" class="form-label">{{ __('workers.notes') }}</label>
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
                                    class="form-input" placeholder="{{ __('workers.enter_notes') }}">{{ old('notes') }}</textarea>

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
                        {{ __('workers.save') }} {{ __('workers.worker') }}
                    </button>

                    <a href="{{ route('manufacturing.workers.index') }}" class="btn-cancel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        {{ __('app.buttons.cancel') }}
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
        infoDiv.innerHTML = '<strong>ملاحظة:</strong> الصلاحيات سيتم تعيينها تلقائياً للعامل عند الحفظ بناءً على الوظيفة المختارة.';
        container.appendChild(infoDiv);
    }

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

    // Generate worker code by role code
    function generateWorkerCodeByRole(roleCode) {
        const btn = document.getElementById('generateBtn');
        const codeInput = document.getElementById('worker_code');

        if (!roleCode) {
            alert('الرجاء اختيار الوظيفة أولاً');
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
                // الحصول على role_code من الخيار المختار
                const selectedOption = this.options[this.selectedIndex];
                const roleCode = selectedOption.getAttribute('data-role-code');
                generateWorkerCodeByRole(roleCode);
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

        // Remove required from all fields first
        userIdSelect.required = false;
        document.getElementById('new_username').required = false;
        document.getElementById('new_email').required = false;

        // Clear user_id if not using existing user
        if (accessType !== 'existing') {
            userIdSelect.value = '';
        }

        // Clear new user fields if not creating new user
        if (accessType !== 'new') {
            document.getElementById('new_username').value = '';
            document.getElementById('new_email').value = '';
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
        }
    }    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleUserAccountFields();

        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection
