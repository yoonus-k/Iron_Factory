@extends('master')

@section('title', 'إضافة لفاف جديد')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex align-items-center mb-3">
            <a href="{{ route('manufacturing.wrappings.index') }}" class="btn btn-outline-secondary me-3">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-tape me-2" style="color: #3498db;"></i>
                    إضافة لفاف جديد
                </h2>
                <p class="text-muted mb-0">إضافة لفاف جديد لاستخدامه في المرحلة الثالثة</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-lg-8 col-xl-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('manufacturing.wrappings.store') }}" method="POST">
                        @csrf

                        <!-- Wrapping Number -->
                        <div class="mb-4">
                            <label for="wrapping_number" class="form-label fw-bold">
                                <i class="fas fa-hashtag me-2 text-primary"></i>
                                رقم اللفاف <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('wrapping_number') is-invalid @enderror" 
                                   id="wrapping_number" 
                                   name="wrapping_number" 
                                   value="{{ old('wrapping_number') }}"
                                   placeholder="مثال: WRP-001"
                                   required>
                            @error('wrapping_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">أدخل رقم تعريف فريد للفاف</small>
                        </div>

                        <!-- Weight -->
                        <div class="mb-4">
                            <label for="weight" class="form-label fw-bold">
                                <i class="fas fa-weight me-2 text-info"></i>
                                وزن اللفاف (كجم) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control form-control-lg @error('weight') is-invalid @enderror" 
                                   id="weight" 
                                   name="weight" 
                                   value="{{ old('weight') }}"
                                   step="0.01"
                                   min="0.01"
                                   placeholder="0.00"
                                   required>
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">أدخل وزن اللفاف بالكيلوجرام</small>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">
                                <i class="fas fa-align-right me-2 text-secondary"></i>
                                الوصف
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="وصف اختياري للفاف">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror>
                        </div>

                        <!-- Active Status -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">
                                    <i class="fas fa-check-circle me-2 text-success"></i>
                                    اللفاف نشط
                                </label>
                            </div>
                            <small class="form-text text-muted ms-4">اللفافات النشطة فقط تظهر في المرحلة الثالثة</small>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-save me-2"></i>
                                حفظ اللفاف
                            </button>
                            <a href="{{ route('manufacturing.wrappings.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info mt-3" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>ملاحظة:</strong> سيتم استخدام هذا اللفاف في المرحلة الثالثة لحساب الوزن الصافي (الوزن الإجمالي - وزن اللفاف)
            </div>
        </div>
    </div>
</div>
@endsection
