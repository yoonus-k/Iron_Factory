@extends('master')

@section('title', 'تسجيل الشحنة (مبسط)')

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="um-page-title">
                        <i class="feather icon-edit-3"></i>
                        ⚖️ تسجيل الوزن من الميزان
                    </h1>
                    <nav class="um-breadcrumb-nav">
                        <span>
                            <i class="feather icon-home"></i> لوحة التحكم
                        </span>
                        <i class="feather icon-chevron-left"></i>
                        <span>المستودع</span>
                        <i class="feather icon-chevron-left"></i>
                        <span>التسجيل</span>
                        <i class="feather icon-chevron-left"></i>
                        <span>#{{ $deliveryNote->note_number ?? $deliveryNote->id }}</span>
                    </nav>
                </div>
                <div class="col-auto">
                    <a href="{{ route('manufacturing.warehouse.registration.pending') }}" class="um-btn um-btn-outline">
                        <i class="feather icon-arrow-right"></i> رجوع
                    </a>
                </div>
            </div>
        </div>

        <!-- Step Indicator -->
        <div class="um-alert-custom um-alert-info"
            style="display: flex; align-items: center; gap: 15px; margin-bottom: 24px;">
            <div
                style="background: #0066CC; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0; font-size: 18px;">
                📊
            </div>
            <div>
                <strong>خطوة التسجيل:</strong> قراءة الوزن الفعلي من الميزان والتحقق من المادة والمستودع
            </div>
        </div>

        @if (session('success'))
            <div class="um-alert-custom um-alert-success" role="alert" id="successMessage">
                <i class="feather icon-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="um-alert-custom um-alert-error" role="alert" id="errorMessage">
                <i class="feather icon-alert-circle"></i>
                {{ session('error') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-container">
                <div class="alert-header">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h4 class="alert-title">يوجد أخطاء في البيانات المدخلة</h4>
                    <button type="button" class="alert-close"
                        onclick="this.parentElement.parentElement.style.display='none'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="alert-body">
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('manufacturing.warehouse.registration.store', $deliveryNote) }}" method="POST" id="registrationForm">
            @csrf

            <div class="row">
                <!-- معلومات الشحنة (قراءة فقط) -->
                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">📦 بيانات الشحنة (للمرجعية)</h5>
                            <small class="text-muted">هذه البيانات من أذن التسليم</small>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>رقم الشحنة:</strong></label>
                                <input type="text" class="form-control" value="{{ $deliveryNote->note_number ?? $deliveryNote->id }}" disabled>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label"><strong>تاريخ الوصول:</strong></label>
                                <input type="text" class="form-control" value="{{ $deliveryNote->created_at->format('d/m/Y H:i') }}" disabled>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label"><strong>المورد:</strong></label>
                                <input type="text" class="form-control" value="{{ $deliveryNote->supplier->name ?? 'N/A' }}" disabled>
                            </div>

                            <div class="form-group mb-0">
                                <label class="form-label"><strong>الوزن من الفاتورة:</strong></label>
                                <input type="text" class="form-control" value="{{ $deliveryNote->invoice_weight ?? 'N/A' }} كيلو" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- بيانات التسجيل المطلوبة (مبسطة) -->
                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">⚠️ البيانات المطلوبة (3 حقول فقط)</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-3">
                                <strong>💡 تعليمات:</strong> أدخل الوزن من الميزان فقط - البقية معبأة تلقائياً
                            </div>

                            @if ($deliveryNote->warehouse_id)
                                <input type="hidden" name="warehouse_id" value="{{ $deliveryNote->warehouse_id }}">
                            @endif

                            <!-- الحقل 1: المادة -->
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>المادة <span class="text-danger">*</span></strong></label>
                                <select name="material_id" class="form-select @error('material_id') is-invalid @enderror" id="materialSelect" required>
                                    <option value="">-- اختر المادة --</option>
                                    @foreach ($materials as $mat)
                                        <option value="{{ $mat->id }}" @selected(old('material_id', $previousLog->material_id ?? '') == $mat->id)>
                                            {{ $mat->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('material_id')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- الحقل 2: الوزن من الميزان -->
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>⚖️ الوزن الفعلي من الميزان (كيلو) <span class="text-danger">*</span></strong></label>
                                <div class="input-group">
                                    <input type="number" name="actual_weight"
                                        class="form-control @error('actual_weight') is-invalid @enderror"
                                        placeholder="مثال: 1000.50" step="0.01" min="0.01"
                                        value="{{ old('actual_weight', $previousLog->weight_recorded ?? '') }}" required
                                        autocomplete="off" autofocus>
                                    <span class="input-group-text">كيلو</span>
                                </div>
                                @error('actual_weight')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                                <small class="text-success d-block mt-2">
                                    📊 الفرق من الفاتورة: <strong id="weight-diff">--</strong>
                                </small>
                            </div>

                            <!-- الحقل 3: الموقع -->
                            <div class="form-group mb-0">
                                <label class="form-label"><strong>📍 موقع التخزين <span class="text-danger">*</span></strong></label>
                                <input type="text" name="location"
                                    class="form-control @error('location') is-invalid @enderror"
                                    placeholder="مثال: المنطقة أ - الصف 1 - الرف 3"
                                    value="{{ old('location', $previousLog->location ?? '') }}" required
                                    autocomplete="off">
                                @error('location')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="card border-success">
                <div class="card-body">
                    <div class="form-check mb-4">
                        <input type="checkbox" id="confirmCheck" class="form-check-input" required>
                        <label class="form-check-label" for="confirmCheck">
                            <strong>✓ أؤكد أن جميع البيانات صحيحة وقد تم قراءة الوزن من الميزان مباشرة</strong>
                        </label>
                    </div>

                    <div class="row g-2">
                        <div class="col-auto">
                            <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                                <i class="feather icon-check"></i> تأكيد التسجيل
                            </button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('manufacturing.warehouse.registration.pending') }}" class="btn btn-secondary">
                                <i class="feather icon-x"></i> إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const confirmCheck = document.getElementById('confirmCheck');
            const submitBtn = document.getElementById('submitBtn');
            const actualWeightInput = document.querySelector('input[name="actual_weight"]');
            const weightDiffDisplay = document.getElementById('weight-diff');
            const invoiceWeight = {{ $deliveryNote->invoice_weight ?? 0 }};

            confirmCheck.addEventListener('change', function() {
                submitBtn.disabled = !this.checked;
            });

            // حساب الفرق من الفاتورة
            if (actualWeightInput) {
                actualWeightInput.addEventListener('input', function() {
                    const actualWeight = parseFloat(this.value) || 0;
                    const difference = actualWeight - invoiceWeight;
                    const percentage = invoiceWeight > 0 ? ((difference / invoiceWeight) * 100).toFixed(2) : 0;

                    if (difference === 0) {
                        weightDiffDisplay.innerHTML = '<span style="color: #27ae60;">✓ متطابق (0 كيلو، 0%)</span>';
                    } else if (difference > 0) {
                        weightDiffDisplay.innerHTML = `<span style="color: #3498db;">+${difference.toFixed(2)} كيلو (+${percentage}%)</span>`;
                    } else {
                        weightDiffDisplay.innerHTML = `<span style="color: #e74c3c;">${difference.toFixed(2)} كيلو (${percentage}%)</span>`;
                    }
                });
            }
        });
    </script>
@endsection
