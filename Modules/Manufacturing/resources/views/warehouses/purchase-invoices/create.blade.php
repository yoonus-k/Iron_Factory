@extends('master')

@section('title', 'إضافة فاتورة مشتريات جديدة')

@section('content')

    <!-- Header -->
    <div class="um-header-section">
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
