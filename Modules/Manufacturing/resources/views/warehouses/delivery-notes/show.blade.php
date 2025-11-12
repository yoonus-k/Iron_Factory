@extends('master')

@section('title', 'تفاصيل أذن التسليم')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-file-text"></i>
                    </div>
                    <div class="header-info">
                        <h1>DN-2024-001</h1>
                        <div class="badges">
                            <span class="badge category">
                                شركة الحديد والصلب
                            </span>
                            <span class="badge active">مستقبل</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.delivery-notes.edit', 1) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        تعديل
                    </a>
                    <a href="{{ route('manufacturing.delivery-notes.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
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
                    <h3 class="card-title">معلومات أذن التسليم</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">رقم الأذن:</div>
                        <div class="info-value">DN-2024-001</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ التسليم:</div>
                        <div class="info-value">2024-11-01</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الوزن الإجمالي:</div>
                        <div class="info-value">500 كيلوغرام</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">عدد المواد:</div>
                        <div class="info-value">5</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value"><span class="badge badge-success">مستقبل</span></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات الاستقبال</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">استقبل بواسطة:</div>
                        <div class="info-value">أحمد محمد</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الاستقبال:</div>
                        <div class="info-value">2024-11-01 10:30</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">ملاحظات:</div>
                        <div class="info-value">تم استقبال المواد بنجاح في الموعد المحدد</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="1"></circle>
                            <path d="M12 1v6m0 6v6"></path>
                            <path d="M4.22 4.22l4.24 4.24m5.08 5.08l4.24 4.24"></path>
                            <path d="M1 12h6m6 0h6"></path>
                            <path d="M4.22 19.78l4.24-4.24m5.08-5.08l4.24-4.24"></path>
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
                            <polyline points="21 8 21 21 3 21 3 8"></polyline>
                            <line x1="1" y1="3" x2="23" y2="3"></line>
                            <path d="M10 12v4"></path>
                            <path d="M14 12v4"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">المواد المستقبلة</h3>
                </div>
                <div class="card-body">
                    <div class="um-table-responsive">
                        <table class="um-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم المادة</th>
                                    <th>الوزن</th>
                                    <th>الكمية</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>مسمار حديد 3*20</td>
                                    <td>100 كجم</td>
                                    <td>1</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>سلك معادن</td>
                                    <td>150 كجم</td>
                                    <td>1</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>ألمنيوم خام</td>
                                    <td>250 كجم</td>
                                    <td>1</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
                    if (confirm('⚠️ هل أنت متأكد من حذف هذا الأذن؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        alert('تم حذف الأذن بنجاح!');
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
