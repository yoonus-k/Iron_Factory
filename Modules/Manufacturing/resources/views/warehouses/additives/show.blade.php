@extends('master')

@section('title', 'تفاصيل الصبغة/البلاستيك')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-palette"></i>
                    </div>
                    <div class="header-info">
                        <h1>صبغة أحمر</h1>
                        <div class="badges">
                            <span class="badge category">
                                نوع: صبغة
                            </span>
                            <span class="badge active">متوفر</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.additives.edit', 1) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        تعديل
                    </a>
                    <a href="{{ route('manufacturing.additives.index') }}" class="btn btn-back">
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
                            <circle cx="12" cy="12" r="10"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات الصبغة/البلاستيك</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">الاسم:</div>
                        <div class="info-value">صبغة أحمر</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">النوع:</div>
                        <div class="info-value">صبغة</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الكمية المتوفرة:</div>
                        <div class="info-value">50 لتر</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الوحدة:</div>
                        <div class="info-value">لتر</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">اللون:</div>
                        <div class="info-value">
                            <span style="display: inline-block; width: 20px; height: 20px; background-color: #ff0000; border-radius: 3px; border: 1px solid #ccc; vertical-align: middle;"></span>
                            أحمر
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value"><span class="badge badge-success">متوفر</span></div>
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
                        <div class="info-value">شركة الأصباغ المتحدة</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الشخص المسؤول:</div>
                        <div class="info-value">علي محمود</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الهاتف:</div>
                        <div class="info-value">0198765432</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">البريد الإلكتروني:</div>
                        <div class="info-value">dyes@example.com</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">ملاحظات</h3>
                </div>
                <div class="card-body">
                    <p class="info-text">
                        صبغة عالية الجودة وممتازة للاستخدام في جميع أنواع الكويلات
                    </p>
                </div>
            </div>

            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">حركة المخزون</h3>
                </div>
                <div class="card-body">
                    <div class="um-table-responsive">
                        <table class="um-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>النوع</th>
                                    <th>الكمية</th>
                                    <th>الرصيد</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>2024-11-10</td>
                                    <td>خروج</td>
                                    <td>10 لتر</td>
                                    <td>50 لتر</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>2024-11-08</td>
                                    <td>دخول</td>
                                    <td>30 لتر</td>
                                    <td>60 لتر</td>
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
                            <span>تحميل</span>
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
                    if (confirm('⚠️ هل أنت متأكد من حذف هذه المادة؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        alert('تم حذف المادة بنجاح!');
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
                    alert('جاري تحميل الملف...');
                });
            }
        });
    </script>
@endsection
