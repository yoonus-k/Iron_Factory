@extends('master')

@section('title', 'تفاصيل المستودع')

@section('content')
    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-home"></i>
                    </div>
                    <div class="header-info">
                        <h1>المستودع الرئيسي</h1>
                        <div class="badges">
                            <span class="badge category">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                                مستودع
                            </span>
                            <span class="badge active">نشط</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.warehouses.edit', 1) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        تعديل
                    </a>
                    <a href="{{ route('manufacturing.warehouses.index') }}" class="btn btn-back">
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
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المستودع</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            رمز المستودع
                        </div>
                        <div class="info-value">WH-001</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                            </svg>
                            الوصف
                        </div>
                        <div class="info-value">المستودع الرئيسي للشركة</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            الموقع
                        </div>
                        <div class="info-value">القاهرة، المنطقة الصناعية</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            السعة التخزينية
                        </div>
                        <div class="info-value">1000 متر مكعب</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            حالة المستودع
                        </div>
                        <div class="info-value">نشط</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات الاتصال</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            رقم الهاتف
                        </div>
                        <div class="info-value">0123456789</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            البريد الإلكتروني
                        </div>
                        <div class="info-value">warehouse@example.com</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            المسؤول
                        </div>
                        <div class="info-value">أحمد محمد</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                                </polygon>
                            </svg>
                            تاريخ الإنشاء
                        </div>
                        <div class="info-value">2024-01-01</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                            </svg>
                            تاريخ التحديث
                        </div>
                        <div class="info-value">2024-11-13</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <path d="M21 15l-5-5L5 21"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">المواد في المستودع</h3>
                </div>
                <div class="card-body">
                    <div class="schedule-grid">
                        <div class="info-item">
                            <div class="info-label">عدد المواد:</div>
                            <div class="info-value">25</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">إجمالي القيمة:</div>
                            <div class="info-value">50,000 جنيه</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">آخر تحديث:</div>
                            <div class="info-value">2024-11-12</div>
                        </div>
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
                    <button type="button" class="action-btn activate">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h4>تعديل المستودع</h4>
                            <p>تعديل تفاصيل المستودع</p>
                        </div>
                    </button>

                    <button type="button" class="action-btn delete">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path
                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h4>حذف المستودع</h4>
                            <p>حذف نهائي للمستودع من النظام</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.action-btn.delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm(
                            '⚠️ هل أنت متأكد من حذف هذا المستودع؟\n\nهذا الإجراء لا يمكن التراجع عنه!'
                        )) {
                        // Here you would typically make an AJAX call to delete the warehouse
                        alert('تم حذف المستودع بنجاح!');
                    }
                });
            });
        });
    </script>
@endsection