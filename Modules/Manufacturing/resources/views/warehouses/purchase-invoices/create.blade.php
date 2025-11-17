@extends('master')

@section('title', 'إضافة فاتورة شراء جديدة')

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="12" y1="11" x2="12" y2="17"></line>
                <line x1="9" y1="14" x2="15" y2="14"></line>
            </svg>
            إضافة فاتورة شراء جديدة
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> لوحة التحكم
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>المستودع</span>
            <i class="feather icon-chevron-left"></i>
            <span>فواتير الشراء</span>
            <i class="feather icon-chevron-left"></i>
            <span>إضافة فاتورة جديدة</span>
        </nav>
    </div>

    <!-- Success and Error Messages -->
    @if (session('success'))
        <div class="um-alert-custom um-alert-success" role="alert" id="successMessage">
            <i class="feather icon-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="um-alert-custom um-alert-error" role="alert" id="validationErrors">
            <i class="feather icon-alert-circle"></i>
            <strong>خطأ في البيانات:</strong> الرجاء التحقق من المعلومات المدخلة.
            <ul style="margin-top: 10px; margin-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.purchase-invoices.store') }}" id="purchaseInvoiceForm">
            @csrf

            <!-- Basic Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">معلومات الفاتورة</h3>
                        <p class="section-subtitle">أدخل بيانات الفاتورة الأساسية</p>
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
                                <polyline points="21 8 21 21 3 21 3 8"></polyline>
                                <line x1="1" y1="3" x2="23" y2="3"></line>
                                <path d="M10 12v4"></path>
                                <path d="M14 12v4"></path>
                            </svg>
                            <input type="text" name="invoice_number" id="invoice_number"
                                class="form-input" placeholder="مثال: INV-2024-001" value="{{ old('invoice_number') }}" required>
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
                                class="form-input" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="supplier_id" class="form-label">
                            المورد
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <select name="supplier_id" id="supplier_id" class="form-input" required>
                                <option value="">-- اختر المورد --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="invoice_reference_number" class="form-label">رقم مرجع الفاتورة</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <line x1="9" y1="11" x2="15" y2="11"></line>
                                <line x1="9" y1="15" x2="15" y2="15"></line>
                            </svg>
                            <input type="text" name="invoice_reference_number" id="invoice_reference_number"
                                class="form-input" placeholder="رقم مرجعي من المورد" value="{{ old('invoice_reference_number') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="due_date" class="form-label">تاريخ الاستحقاق</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <input type="date" name="due_date" id="due_date" class="form-input" value="{{ old('due_date') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amounts and Currency Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="1"></circle>
                            <circle cx="19" cy="12" r="1"></circle>
                            <circle cx="5" cy="12" r="1"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">المبالغ والعملة</h3>
                        <p class="section-subtitle">أدخل معلومات المبالغ والعملة</p>
                    </div>
                </div>

                <div class="form-grid">
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
                                class="form-input" placeholder="0.00" step="0.01" value="{{ old('total_amount') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="currency" class="form-label">العملة</label>
                        <div class="input-wrapper">
                            <select name="currency" id="currency" class="form-input">
                                <option value="SAR" {{ old('currency', 'SAR') == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                                <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="payment_terms" class="form-label">شروط الدفع</label>
                        <div class="input-wrapper">
                            <select name="payment_terms" id="payment_terms" class="form-input">
                                <option value="">-- اختر شروط الدفع --</option>
                                <option value="دفع فوري" {{ old('payment_terms') == 'دفع فوري' ? 'selected' : '' }}>دفع فوري</option>
                                <option value="15 يوم" {{ old('payment_terms') == '15 يوم' ? 'selected' : '' }}>15 يوم</option>
                                <option value="30 يوم" {{ old('payment_terms') == '30 يوم' ? 'selected' : '' }}>30 يوم</option>
                                <option value="45 يوم" {{ old('payment_terms') == '45 يوم' ? 'selected' : '' }}>45 يوم</option>
                                <option value="60 يوم" {{ old('payment_terms') == '60 يوم' ? 'selected' : '' }}>60 يوم</option>
                                <option value="مخصص" {{ old('payment_terms') == 'مخصص' ? 'selected' : '' }}>مخصص</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status and Activity Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">الحالة والنشاط</h3>
                        <p class="section-subtitle">حدد حالة الفاتورة ونشاطها</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="status" class="form-label">
                            الحالة
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <select name="status" id="status" class="form-input" required>
                                <option value="">-- اختر الحالة --</option>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>موافق عليها</option>
                                <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">النشاط</label>
                        <div class="input-wrapper">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active"
                                       name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    ✓ الفاتورة نشطة
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">ملاحظات إضافية</h3>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="notes" class="form-label">الملاحظات</label>
                        <div class="input-wrapper">
                            <textarea name="notes" id="notes"
                                class="form-input" rows="4" placeholder="أدخل أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                        </div>
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
                <a href="{{ route('manufacturing.purchase-invoices.index') }}" class="btn-cancel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('purchaseInvoiceForm');
            const inputs = form.querySelectorAll('.form-input');

            // Auto-hide success/error messages after 5 seconds
            setTimeout(function() {
                const successMsg = document.getElementById('successMessage');
                const errorMsg = document.getElementById('errorMessage');
                const validationErrors = document.getElementById('validationErrors');
                
                if (successMsg) {
                    successMsg.style.transition = 'opacity 0.5s';
                    successMsg.style.opacity = '0';
                    setTimeout(() => successMsg.style.display = 'none', 500);
                }
                if (errorMsg) {
                    errorMsg.style.transition = 'opacity 0.5s';
                    errorMsg.style.opacity = '0';
                    setTimeout(() => errorMsg.style.display = 'none', 500);
                }
                if (validationErrors) {
                    validationErrors.style.transition = 'opacity 0.5s';
                    validationErrors.style.opacity = '0';
                    setTimeout(() => validationErrors.style.display = 'none', 500);
                }
            }, 5000);

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
