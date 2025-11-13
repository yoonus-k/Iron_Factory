@extends('master')

@section('title', 'إضافة تصنيف جديد')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-plus-circle"></i>
                إضافة تصنيف جديد
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
                <span>إضافة تصنيف</span>
            </nav>
        </div>

        <!-- Form Card -->
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-layers"></i>
                    بيانات التصنيف الجديد
                </h4>
            </div>

            <form method="POST" action="{{ route('manufacturing.warehouse-settings.store-category') }}" class="um-form">
                @csrf

                <div class="um-form-group">
                    <label for="name" class="um-form-label">اسم التصنيف <span class="um-required">*</span></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="um-form-control @error('name') is-invalid @enderror"
                        placeholder="أدخل اسم التصنيف"
                        value="{{ old('name') }}"
                        required
                    >
                    @error('name')
                        <span class="um-error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="um-form-group">
                    <label for="description" class="um-form-label">الوصف</label>
                    <textarea
                        name="description"
                        id="description"
                        class="um-form-control @error('description') is-invalid @enderror"
                        placeholder="أدخل وصف التصنيف"
                        rows="4"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <span class="um-error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="um-form-actions">
                    <button type="submit" class="um-btn um-btn-primary">
                        <i class="feather icon-save"></i>
                        حفظ التصنيف
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
