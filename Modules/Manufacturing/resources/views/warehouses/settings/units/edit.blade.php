@extends('master')

@section('title', 'تعديل الوحدة')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-edit-2"></i>
                    </div>
                    <div class="header-info">
                        <h1>تعديل الوحدة</h1>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.warehouse-settings.units.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        رجوع
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('manufacturing.warehouse-settings.units.update', $unit['id']) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- معلومات الوحدة الأساسية -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">معلومات الوحدة</h3>
                        <p class="section-subtitle">قم بتعديل بيانات الوحدة</p>
                    </div>
                </div>

                <div class="form-grid">
                    <!-- اسم الوحدة -->
                    <div class="form-group full-width">
                        <label class="form-label">اسم الوحدة <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22 6 12 13 2 6"></polyline>
                            </svg>
                            <input
                                type="text"
                                name="name"
                                class="form-input @error('name') error @enderror"
                                placeholder="مثال: كيلوغرام"
                                value="{{ old('name', $unit['name']) }}"
                                required
                            >
                        </div>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- الاختصار -->
                    <div class="form-group full-width">
                        <label class="form-label">الاختصار <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 4l-8 8"></path>
                                <path d="M7 4l8 8"></path>
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            </svg>
                            <input
                                type="text"
                                name="abbreviation"
                                class="form-input @error('abbreviation') error @enderror"
                                placeholder="مثال: كغ"
                                value="{{ old('abbreviation', $unit['abbreviation']) }}"
                                required
                                maxlength="10"
                            >
                        </div>
                        @error('abbreviation')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    تحديث الوحدة
                </button>
                <a href="{{ route('manufacturing.warehouse-settings.units.index') }}" class="btn btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    إلغاء
                </a>
            </div>
        </form>
    </div>

@endsection
