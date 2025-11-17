@extends('master')

@section('title', 'إضافة استاند جديد')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-package"></i>
                إضافة استاند جديد
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <a href="{{ route('manufacturing.stands.index') }}">الاستاندات</a>
                <i class="feather icon-chevron-left"></i>
                <span>إضافة جديد</span>
            </nav>
        </div>

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="um-alert-custom um-alert-danger" role="alert">
                <i class="feather icon-alert-circle"></i>
                <strong>يوجد أخطاء في البيانات المدخلة:</strong>
                <ul style="margin: 10px 0 0 20px; padding: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-edit-3"></i>
                    بيانات الاستاند
                </h4>
                <a href="{{ route('manufacturing.stands.index') }}" class="um-btn um-btn-outline">
                    <i class="feather icon-arrow-right"></i>
                    رجوع
                </a>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('manufacturing.stands.store') }}">
                @csrf

                <div class="um-form-section">
                    <div class="um-row">
                        <!-- رقم الاستاند -->
                        <div class="um-col-md-6">
                            <div class="um-form-group">
                                <label for="stand_number">
                                    <i class="feather icon-hash"></i>
                                    رقم الاستاند
                                    <span class="um-required">*</span>
                                </label>
                                <input type="text" 
                                       name="stand_number" 
                                       id="stand_number" 
                                       class="um-form-control @error('stand_number') is-invalid @enderror" 
                                       value="{{ old('stand_number') }}"
                                       placeholder="أدخل رقم الاستاند"
                                       required>
                                @error('stand_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="um-help-text">أدخل رقم فريد للاستاند</small>
                            </div>
                        </div>

                        <!-- الوزن -->
                        <div class="um-col-md-6">
                            <div class="um-form-group">
                                <label for="weight">
                                    <i class="feather icon-activity"></i>
                                    الوزن (كجم)
                                    <span class="um-required">*</span>
                                </label>
                                <input type="number" 
                                       name="weight" 
                                       id="weight" 
                                       class="um-form-control @error('weight') is-invalid @enderror" 
                                       value="{{ old('weight') }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="أدخل وزن الاستاند بالكيلوجرام"
                                       required>
                                @error('weight')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- الملاحظات -->
                    <div class="um-row">
                        <div class="um-col-md-12">
                            <div class="um-form-group">
                                <label for="notes">
                                    <i class="feather icon-file-text"></i>
                                    ملاحظات
                                </label>
                                <textarea name="notes" 
                                          id="notes" 
                                          class="um-form-control @error('notes') is-invalid @enderror" 
                                          rows="4"
                                          maxlength="1000"
                                          placeholder="أضف أي ملاحظات إضافية حول الاستاند...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="um-help-text">الحد الأقصى 1000 حرف</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إشعار الحالة الافتراضية -->
                <div class="um-alert-custom um-alert-info" role="alert" style="margin: 20px;">
                    <i class="feather icon-info"></i>
                    <strong>ملاحظة:</strong> سيتم إنشاء الاستاند بحالة <strong>غير مستخدم</strong> بشكل افتراضي، ويمكنك تحديث الحالة لاحقاً.
                </div>

                <!-- Buttons -->
                <div class="um-form-actions">
                    <button type="submit" class="um-btn um-btn-primary">
                        <i class="feather icon-save"></i>
                        حفظ الاستاند
                    </button>
                    <a href="{{ route('manufacturing.stands.index') }}" class="um-btn um-btn-outline">
                        <i class="feather icon-x"></i>
                        إلغاء
                    </a>
                </div>
            </form>
        </section>
    </div>



@endsection
