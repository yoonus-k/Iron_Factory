@extends('layouts.auth')

@section('title', __('app.users.login'))

@push('styles')
<style>
    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
    }

    .auth-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        padding: 40px;
        max-width: 450px;
        width: 100%;
    }

    .auth-logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .auth-logo img {
        max-width: 150px;
        margin-bottom: 15px;
    }

    .auth-logo h2 {
        color: #333;
        font-weight: 600;
        margin: 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 500;
        color: #555;
        margin-bottom: 8px;
    }

    .form-control {
        height: 50px;
        border-radius: 8px;
        border: 2px solid #e0e0e0;
        padding: 0 15px;
        font-size: 15px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .input-group {
        position: relative;
    }

    .input-group-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
    }

    .input-group .form-control {
        padding-right: 45px;
    }

    .btn-login {
        height: 50px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s;
        width: 100%;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }

    .remember-forgot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .form-check-label {
        color: #666;
        font-size: 14px;
    }

    .forgot-link {
        color: #667eea;
        text-decoration: none;
        font-size: 14px;
    }

    .forgot-link:hover {
        text-decoration: underline;
    }

    .register-link {
        text-align: center;
        margin-top: 20px;
        color: #666;
        font-size: 14px;
    }

    .register-link a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .register-link a:hover {
        text-decoration: underline;
    }

    .alert {
        border-radius: 8px;
        padding: 12px 15px;
        margin-bottom: 20px;
    }

    [dir="rtl"] .input-group-icon {
        left: auto;
        right: 15px;
    }

    [dir="rtl"] .input-group .form-control {
        padding-right: 15px;
        padding-left: 45px;
    }
</style>
@endpush

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="{{ asset('assets/images/logo/logo-dark.jpg') }}" alt="Logo">
            <h2>{{ __('app.users.login') }}</h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <!-- Username or Email -->
            <div class="form-group">
                <label class="form-label">{{ __('app.users.username_or_email') }}</label>
                <div class="input-group">
                    <input 
                        type="text" 
                        name="login" 
                        class="form-control @error('login') is-invalid @enderror" 
                        placeholder="{{ __('app.users.enter_username_or_email') }}"
                        value="{{ old('login') }}"
                        required
                        autofocus
                    >
                    <i class="fas fa-user input-group-icon"></i>
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label">{{ __('app.users.password') }}</label>
                <div class="input-group">
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        placeholder="{{ __('app.users.enter_password') }}"
                        required
                    >
                    <i class="fas fa-lock input-group-icon"></i>
                </div>
            </div>

            <!-- Remember & Forgot -->
            <div class="remember-forgot">
                <div class="form-check">
                    <input 
                        type="checkbox" 
                        class="form-check-input" 
                        id="remember" 
                        name="remember"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="remember">
                        {{ __('app.users.remember_me') }}
                    </label>
                </div>
                {{-- <a href="{{ route('password.request') }}" class="forgot-link">
                    {{ __('app.users.forgot_password') }}
                </a> --}}
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>
                {{ __('app.buttons.login') }}
            </button>

            <!-- Register Link -->
            {{-- <div class="register-link">
                {{ __('app.users.no_account') }}
                <a href="{{ route('register') }}">{{ __('app.users.register_now') }}</a>
            </div> --}}
        </form>
    </div>
</div>
@endsection
