@extends('master')

@section('title', 'فاتورة شراء جديدة')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style-material.css') }}">

    @if (session('success'))
        <div class="um-alert-custom um-alert-success" role="alert">
            <i class="feather icon-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="um-alert-custom um-alert-error" role="alert">
            <i class="feather icon-alert-circle"></i>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-file-plus"></i>
                    </div>
                    <div class="header-info">
                        <h1>فاتورة شراء جديدة</h1>
                        <p>إنشاء فاتورة شراء من مورد جديد</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.purchase-invoices.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        العودة
                    </a>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('manufacturing.purchase-invoices.store') }}" class="grid">
            @csrf

            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات الفاتورة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">رقم الفاتورة *</div>
                        <input type="text" class="form-control @error('invoice_number') is-invalid @enderror"
                               name="invoice_number" value="{{ old('invoice_number') }}" required
                               placeholder="مثال: INV-001">
                        @error('invoice_number')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="info-item">
                        <div class="info-label">رقم مرجع الفاتورة</div>
                        <input type="text" class="form-control" name="invoice_reference_number"
                               value="{{ old('invoice_reference_number') }}" placeholder="رقم مرجعي من المورد">
                    </div>

                    <div class="info-item">
                        <div class="info-label">المورد *</div>
                        <select class="form-control @error('supplier_id') is-invalid @enderror"
                                name="supplier_id" required>
                            <option value="">-- اختر مورد --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الفاتورة *</div>
                        <input type="date" class="form-control @error('invoice_date') is-invalid @enderror"
                               name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                        @error('invoice_date')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الاستحقاق</div>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                               name="due_date" value="{{ old('due_date') }}">
                        @error('due_date')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="1"></circle>
                            <circle cx="19" cy="12" r="1"></circle>
                            <circle cx="5" cy="12" r="1"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">المبالغ والعملة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">المبلغ الإجمالي *</div>
                        <div class="input-group">
                            <input type="number" class="form-control @error('total_amount') is-invalid @enderror"
                                   name="total_amount" value="{{ old('total_amount') }}" step="0.01" required
                                   placeholder="0.00" id="total_amount">
                            <select class="form-select" name="currency" style="max-width: 100px;">
                                <option value="SAR" {{ old('currency', 'SAR') == 'SAR' ? 'selected' : '' }}>SAR</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>AED</option>
                            </select>
                        </div>
                        @error('total_amount')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="info-item">
                        <div class="info-label">شروط الدفع</div>
                        <select class="form-control" name="payment_terms">
                            <option value="">-- اختر شروط الدفع --</option>
                            <option value="دفع فوري" {{ old('payment_terms') == 'دفع فوري' ? 'selected' : '' }}>دفع فوري</option>
                            <option value="15 يوم" {{ old('payment_terms') == '15 يوم' ? 'selected' : '' }}>15 يوم</option>
                            <option value="30 يوم" {{ old('payment_terms') == '30 يوم' ? 'selected' : '' }}>30 يوم</option>
                            <option value="45 يوم" {{ old('payment_terms') == '45 يوم' ? 'selected' : '' }}>45 يوم</option>
                            <option value="60 يوم" {{ old('payment_terms') == '60 يوم' ? 'selected' : '' }}>60 يوم</option>
                            <option value="مخصص" {{ old('payment_terms') == 'مخصص' ? 'selected' : '' }}>مخصص</option>
                        </select>
                    </div>

                    <div class="info-item">
                        <div class="info-label">النشاط</div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active"
                                   name="is_active" value="1" {{ old('is_active') ? 'checked' : 'checked' }}>
                            <label class="form-check-label" for="is_active">
                                ✓ الفاتورة نشطة
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">ملاحظات إضافية</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">ملاحظات</div>
                        <textarea class="form-control" name="notes" rows="4"
                                  placeholder="أي ملاحظات إضافية أو معلومات مهمة عن الفاتورة">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card bg-light">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">الملخص</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">المبلغ الإجمالي:</div>
                        <div class="info-value">
                            <strong id="total_display" style="font-size: 1.3em; color: #2c3e50;">0.00 SAR</strong>
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 10px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        حفظ الفاتورة
                    </button>
                    <a href="{{ route('manufacturing.purchase-invoices.index') }}" class="btn btn-secondary" style="width: 100%;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('total_amount')?.addEventListener('input', function() {
            const currency = document.querySelector('select[name="currency"]').value;
            const amount = parseFloat(this.value || 0).toFixed(2);
            document.getElementById('total_display').textContent = amount + ' ' + currency;
        });

        document.querySelector('select[name="currency"]')?.addEventListener('change', function() {
            const amount = parseFloat(document.getElementById('total_amount').value || 0).toFixed(2);
            document.getElementById('total_display').textContent = amount + ' ' + this.value;
        });
    </script>
@endsection
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M13 2H3v20h18V9z"></path>
                <polyline points="13 2 13 9 20 9"></polyline>
            </svg>
            إضافة فاتورة مشتريات جديدة
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> لوحة التحكم
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>المستودع</span>
            <i class="feather icon-chevron-left"></i>
            <span>فواتير المشتريات</span>
            <i class="feather icon-chevron-left"></i>
            <span>إضافة فاتورة جديدة</span>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.purchase-invoices.store') }}" id="invoiceForm" enctype="multipart/form-data">
            @csrf

            <!-- Invoice Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 2H3v20h18V9z"></path>
                            <polyline points="13 2 13 9 20 9"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">معلومات الفاتورة</h3>
                        <p class="section-subtitle">أدخل بيانات فاتورة المشتريات الجديدة</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="invoice_number" class="form-label">
                            رقم الفاتورة
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            </svg>
                            <input type="text" name="invoice_number" id="invoice_number"
                                class="form-input" placeholder="مثال: INV-2024-001" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="invoice_date" class="form-label">
                            تاريخ الفاتورة
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <input type="date" name="invoice_date" id="invoice_date"
                                class="form-input" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="supplier_id" class="form-label">
                            المورد
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                            <select name="supplier_id" id="supplier_id" class="form-input" required>
                                <option value="">-- اختر المورد --</option>
                                <option value="1">شركة الحديد والصلب</option>
                                <option value="2">شركة المعادن المتحدة</option>
                                <option value="3">شركة الصناعات الثقيلة</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="due_date" class="form-label">تاريخ الاستحقاق</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            </svg>
                            <input type="date" name="due_date" id="due_date" class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="total_amount" class="form-label">
                            المبلغ الإجمالي
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="8"></circle>
                                <path d="M12 6v12"></path>
                                <path d="M9 9h6a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H9"></path>
                            </svg>
                            <input type="number" name="total_amount" id="total_amount"
                                class="form-input" placeholder="0.00" step="0.01" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="paid_amount" class="form-label">المبلغ المدفوع</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <input type="number" name="paid_amount" id="paid_amount"
                                class="form-input" placeholder="0.00" step="0.01">
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="description" class="form-label">الوصف / التفاصيل</label>
                        <div class="input-wrapper">
                            <textarea name="description" id="description"
                                class="form-input" rows="4" placeholder="أدخل تفاصيل الفاتورة..."></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">الحالة</label>
                        <div class="input-wrapper">
                            <select name="status" id="status" class="form-input">
                                <option value="pending">قيد الانتظار</option>
                                <option value="paid">مدفوعة</option>
                                <option value="overdue">متأخرة</option>
                                <option value="cancelled">ملغاة</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon account">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 4h-5.423A1.993 1.993 0 0 0 10 5.992v5.016A1.993 1.993 0 0 0 11.077 13h5.423A1.993 1.993 0 0 0 18 11.008V5.992A1.993 1.993 0 0 0 16.5 4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">معلومات إضافية</h3>
                        <p class="section-subtitle">أدخل المعلومات الإضافية</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        &nbsp;
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    حفظ الفاتورة
                </button>
                <button type="button" class="btn-cancel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    إلغاء
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('invoiceForm');
            const inputs = form.querySelectorAll('.form-input');

            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.required && !this.value) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            form.addEventListener('submit', function(e) {
                const firstInvalid = form.querySelector('.is-invalid, :invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            });
        });
    </script>

@endsection
