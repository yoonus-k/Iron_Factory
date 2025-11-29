@extends('master')

@section('title', __('users.edit_user'))

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-user"></i>
                {{ __('users.edit_user') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('users.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('users.users') }}</span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('users.edit_user') }}</span>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-container">
                <div class="alert-header">
                    <i class="feather icon-alert-circle alert-icon"></i>
                    <h4 class="alert-title">{{ __('users.validation_errors') }}</h4>
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
                            <h3 class="section-title">{{ __('users.user_information') }}</h3>
                            <p class="section-subtitle">{{ __('users.update_user_info') }}</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <!-- Name -->
                        <div class="form-group">
                            <label for="name" class="form-label">
                                {{ __('users.full_name') }}
                                <span class="required">{{ __('users.required') }}</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="feather icon-user input-icon"></i>
                                <input type="text" name="name" id="name" class="form-input"
                                    value="{{ old('name', $user->name) }}" placeholder="{{ __('users.name_placeholder') }}" required>
                            </div>
                            <div class="error-message" id="name-error" style="display: none;"></div>
                        </div>

                        <!-- Username -->
                        <div class="form-group">
                            <label for="username" class="form-label">
                                {{ __('users.username') }}
                                <span class="required">{{ __('users.required') }}</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="feather icon-at-sign input-icon"></i>
                                <input type="text" name="username" id="username" class="form-input"
                                    value="{{ old('username', $user->username) }}" placeholder="{{ __('users.username_placeholder') }}" required>
                            </div>
                            <div class="error-message" id="username-error" style="display: none;"></div>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">
                                {{ __('users.email') }}
                                <span class="required">{{ __('users.required') }}</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="feather icon-mail input-icon"></i>
                                <input type="email" name="email" id="email" class="form-input"
                                    value="{{ old('email', $user->email) }}" placeholder="{{ __('users.email_placeholder') }}" required>
                            </div>
                            <div class="error-message" id="email-error" style="display: none;"></div>
                        </div>

                        <!-- Password (Optional) -->
                        <div class="form-group">
                            <label for="password" class="form-label">
                                {{ __('users.new_password') }}
                                <span class="optional">({{ __('users.leave_empty_no_change') }})</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="feather icon-lock input-icon"></i>
                                <input type="password" name="password" id="password" class="form-input"
                                    placeholder="{{ __('users.password_placeholder') }}">
                            </div>
                            <div class="error-message" id="password-error" style="display: none;"></div>
                        </div>

                        <!-- Password Confirmation -->
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">
                                {{ __('users.confirm_new_password') }}
                            </label>
                            <div class="input-wrapper">
                                <i class="feather icon-lock input-icon"></i>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-input"
                                    placeholder="{{ __('users.password_confirmation_placeholder') }}">
                            </div>
                            <div class="error-message" id="password_confirmation-error" style="display: none;"></div>
                        </div>

                        <!-- Role -->
                        <div class="form-group">
                            <label for="role_id" class="form-label">
                                {{ __('users.role') }}
                                <span class="required">{{ __('users.required') }}</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="feather icon-shield input-icon"></i>
                                <select name="role_id" id="role_id" class="form-input" required>
                                    <option value="">{{ __('users.select_role') }}</option>
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
                                {{ __('users.shift') }}
                            </label>
                            <div class="input-wrapper">
                                <i class="feather icon-clock input-icon"></i>
                                <input type="text" name="shift" id="shift" class="form-input"
                                    value="{{ old('shift', $user->shift) }}" placeholder="{{ __('users.shift_placeholder') }}">
                            </div>
                            <div class="error-message" id="shift-error" style="display: none;"></div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label class="form-label">{{ __('users.status') }}</label>
                            <div class="switch-group">
                                <input type="checkbox" name="is_active" id="is_active" class="switch-input" value="1"
                                    {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="switch-label">
                                    <span class="switch-button"></span>
                                    <span class="switch-text">
                                        <i class="feather icon-check-circle"></i>
                                        {{ __('users.enable_account') }}
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
                            <h3 class="section-title">{{ __('users.system_information') }}</h3>
                            <p class="section-subtitle">{{ __('users.system_info_description') }}</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">{{ __('users.user_number') }}</label>
                            <div class="input-wrapper">
                                <input type="text" class="form-input" value="{{ $user->id }}" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('users.created_at') }}</label>
                            <div class="input-wrapper">
                                <input type="text" class="form-input" value="{{ $user->created_at->format('Y-m-d H:i') }}" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('users.updated_at') }}</label>
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
                        {{ __('users.update_user') }}
                    </button>
                    <a href="{{ route('users.index') }}" class="btn-cancel">
                        <i class="feather icon-x"></i>
                        {{ __('users.cancel') }}
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
                        showError(this.id, '{{ __('users.field_required') }}');
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
                        showError(field.id, '{{ __('users.field_required') }}');
                        isValid = false;
                    }
                });

                // Check password confirmation if password is provided
                const password = document.getElementById('password');
                const passwordConfirmation = document.getElementById('password_confirmation');
                if (password.value || passwordConfirmation.value) {
                    if (password.value !== passwordConfirmation.value) {
                        showError('password_confirmation', '{{ __('users.passwords_not_match') }}');
                        isValid = false;
                    }
                }

                // If form is valid, submit it
                if (isValid) {
                    // Show SweetAlert2 confirmation
                    Swal.fire({
                        title: '{{ __('users.confirm_update') }}',
                        text: '{{ __('users.confirm_update') }}',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '{{ __('users.yes_update') }}',
                        cancelButtonText: '{{ __('users.no_cancel') }}',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'swal-btn-confirm',
                            cancelButton: 'swal-btn-cancel'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading message
                            Swal.fire({
                                title: '{{ __('users.updating') }}',
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
