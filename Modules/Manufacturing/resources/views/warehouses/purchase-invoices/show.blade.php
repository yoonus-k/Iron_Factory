@extends('master')

@section('title', 'تفاصيل فاتورة المشتريات')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-file-invoice-dollar"></i>
                    </div>
                    <div class="header-info">
                        <h1>INV-2024-001</h1>
                        <div class="badges">
                            <span class="badge category">
                                شركة الحديد والصلب
                            </span>
                            <span class="badge active">مدفوعة</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.purchase-invoices.edit', 1) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        </svg>
                        تعديل
                    </a>
                    <a href="{{ route('manufacturing.purchase-invoices.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                        </svg>
                        العودة
                    </a>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات الفاتورة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">رقم الفاتورة:</div>
                        <div class="info-value">INV-2024-001</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الفاتورة:</div>
                        <div class="info-value">2024-11-01</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الاستحقاق:</div>
                        <div class="info-value">2024-11-30</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">المبلغ الإجمالي:</div>
                        <div class="info-value">5,000.00 ريال</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">المبلغ المدفوع:</div>
                        <div class="info-value">5,000.00 ريال</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">المبلغ المتبقي:</div>
                        <div class="info-value">0.00 ريال</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value"><span class="badge badge-success">مدفوعة</span></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المورد</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">اسم المورد:</div>
                        <div class="info-value">شركة الحديد والصلب</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الشخص المسؤول:</div>
                        <div class="info-value">محمود علي</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الهاتف:</div>
                        <div class="info-value">0123456789</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">البريد الإلكتروني:</div>
                        <div class="info-value">supplier@example.com</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">العنوان:</div>
                        <div class="info-value">شارع الصناعة، المنطقة الرابعة، القاهرة</div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="3" y1="9" x2="21" y2="9"></line>
                            <line x1="9" y1="3" x2="9" y2="21"></line>
                        </svg>
                    </div>
                    <h3 class="card-title">تفاصيل الفاتورة</h3>
                </div>
                <div class="card-body">
                    <p class="info-text">
                        فاتورة شراء مواد خام عالية الجودة مطابقة للمواصفات المتفق عليها بين الشركتين
                    </p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-icon warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="19" cy="12" r="1"></circle>
                        <circle cx="5" cy="12" r="1"></circle>
                    </svg>
                </div>
                <h3 class="card-title">الإجراءات المتاحة</h3>
            </div>
            <div class="card-body">
                <div class="actions-grid">
                    <button type="button" class="action-btn print">
                        <div class="action-icon">
                            <i class="feather icon-printer"></i>
                        </div>
                        <div class="action-text">
                            <span>طباعة</span>
                        </div>
                    </button>

                    <button type="button" class="action-btn download">
                        <div class="action-icon">
                            <i class="feather icon-download"></i>
                        </div>
                        <div class="action-text">
                            <span>تحميل PDF</span>
                        </div>
                    </button>

                    <button type="button" class="action-btn delete">
                        <div class="action-icon">
                            <i class="feather icon-trash-2"></i>
                        </div>
                        <div class="action-text">
                            <span>حذف</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButton = document.querySelector('.action-btn.delete');
            if (deleteButton) {
                deleteButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('⚠️ هل أنت متأكد من حذف هذه الفاتورة؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        alert('تم حذف الفاتورة بنجاح!');
                    }
                });
            }

            const printButton = document.querySelector('.action-btn.print');
            if (printButton) {
                printButton.addEventListener('click', function() {
                    window.print();
                });
            }

            const downloadButton = document.querySelector('.action-btn.download');
            if (downloadButton) {
                downloadButton.addEventListener('click', function() {
                    alert('جاري تحميل ملف PDF...');
                });
            }
        });
    </script>
@endsection
