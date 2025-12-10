@extends('master')

@section('title', 'تعديل لفاف')

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
                    <i class="fas fa-edit me-2" style="color: #f39c12;"></i>
                    تعديل لفاف
                </h2>
                <p class="text-muted mb-0">تعديل بيانات اللفاف: <strong>{{ $wrapping->wrapping_number }}</strong></p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-lg-8 col-xl-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('manufacturing.wrappings.update', $wrapping->id) }}" method="POST">
                        @csrf
                        @method('PUT')

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
                                   value="{{ old('wrapping_number', $wrapping->wrapping_number) }}"
                                   placeholder="مثال: WRP-001"
                                   required>
                            @error('wrapping_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                   value="{{ old('weight', $wrapping->weight) }}"
                                   step="0.01"
                                   min="0.01"
                                   required>
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                      placeholder="وصف اختياري للفاف">{{ old('description', $wrapping->description) }}</textarea>
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
                                       {{ old('is_active', $wrapping->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">
                                    <i class="fas fa-check-circle me-2 text-success"></i>
                                    اللفاف نشط
                                </label>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-save me-2"></i>
                                حفظ التعديلات
                            </button>
                            <a href="{{ route('manufacturing.wrappings.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
