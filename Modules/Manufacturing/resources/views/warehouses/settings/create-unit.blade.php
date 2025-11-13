@extends('master')

@section('title', 'إضافة وحدة قياس جديدة')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-plus-circle"></i>
                إضافة وحدة قياس جديدة
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>إدارة المخزون</span>
                <i class="feather icon-chevron-left"></i>
                <a href="{{ route('manufacturing.warehouse-settings.index') }}">الإعدادات</a>
                <i class="feather icon-chevron-left"></i>
                <span>إضافة وحدة</span>
            </nav>
        </div>

        <!-- Form Card -->
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-box"></i>
                    بيانات الوحدة الجديدة
                </h4>
            </div>

            <form method="POST" action="{{ route('manufacturing.warehouse-settings.store-unit') }}" class="um-form">
                @csrf

                <div class="um-form-group">
                    <label for="name" class="um-form-label">اسم الوحدة <span class="um-required">*</span></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="um-form-control @error('name') is-invalid @enderror"
                        placeholder="مثال: كيلوجرام"
                        value="{{ old('name') }}"
                        required
                    >
                    @error('name')
                        <span class="um-error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="um-form-group">
                    <label for="abbreviation" class="um-form-label">الاختصار <span class="um-required">*</span></label>
                    <input
                        type="text"
                        name="abbreviation"
                        id="abbreviation"
                        class="um-form-control @error('abbreviation') is-invalid @enderror"
                        placeholder="مثال: كجم"
                        value="{{ old('abbreviation') }}"
                        required
                        maxlength="50"
                    >
                    @error('abbreviation')
                        <span class="um-error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="um-form-actions">
                    <button type="submit" class="um-btn um-btn-primary">
                        <i class="feather icon-save"></i>
                        حفظ الوحدة
                    </button>
                    <a href="{{ route('manufacturing.warehouse-settings.index') }}" class="um-btn um-btn-outline">
                        <i class="feather icon-x"></i>
                        إلغاء
                    </a>
                </div>
            </form>
        </section>
    </div>

@endsection
