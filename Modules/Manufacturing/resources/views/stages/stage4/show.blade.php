@extends('master')

@section('title', 'تفاصيل الكرتون')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-package"></i>
                    </div>
                    <div class="header-info">
                        <h1>كرتون رقم BOX-001</h1>
                        <div class="badges">
                            <span class="badge category">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 9l12-3"></path>
                                    <path d="M6 9v6a2 2 0 002 2h8a2 2 0 002-2V9"></path>
                                    <path d="M6 9l-2 12a2 2 0 002 2h12a2 2 0 002-2l-2-12"></path>
                                </svg>
                                المرحلة الرابعة
                            </span>
                            <span class="badge active">جاهز للشحن</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.stage4.edit', 1) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        تعديل
                    </a>
                    <a href="{{ route('manufacturing.stage4.index') }}" class="btn btn-back">
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
                            <path d="M6 9l12-3"></path>
                            <path d="M6 9v6a2 2 0 002 2h8a2 2 0 002-2V9"></path>
                            <path d="M6 9l-2 12a2 2 0 002 2h12a2 2 0 002-2l-2-12"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات الكرتون</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            رقم الكرتون
                        </div>
                        <div class="info-value"><span class="badge badge-info">BOX-001</span></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <circle cx="12" cy="12" r="6"></circle>
                                <circle cx="12" cy="12" r="2"></circle>
                            </svg>
                            عدد الكويلات
                        </div>
                        <div class="info-value">5</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            الوزن الإجمالي
                        </div>
                        <div class="info-value">250 كيلوغرام</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 9l12-3"></path>
                                <path d="M6 9v6a2 2 0 002 2h8a2 2 0 002-2V9"></path>
                            </svg>
                            نوع التغليف
                        </div>
                        <div class="info-value">كرتون</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z"></path>
                                <line x1="12" y1="2" x2="12" y2="22"></line>
                            </svg>
                            الكويل المرتبط
                        </div>
                        <div class="info-value"><span class="badge badge-info">COIL-001</span></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات إضافية</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            حالة الكرتون
                        </div>
                        <div class="info-value">
                            <span class="status active">جاهز للشحن</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            تاريخ الإنشاء
                        </div>
                        <div class="info-value">2025-01-15 09:00</div>
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
                        <div class="info-value">2025-01-15 15:30</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            &nbsp;
                        </div>
                        <div class="info-value">&nbsp;</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">بيانات العميل</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            اسم العميل
                        </div>
                        <div class="info-value">أحمد محمد علي</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                <path d="M22 4l-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 4"></path>
                            </svg>
                            البريد الإلكتروني
                        </div>
                        <div class="info-value">customer@example.com</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            رقم الهاتف
                        </div>
                        <div class="info-value">+966501234567</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات الشحن</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            عنوان الشحن
                        </div>
                        <div class="info-value">
                            <span class="text-info">
                                شارع الأمير محمد بن عبدالعزيز، الرياض 12345<br>
                                المملكة العربية السعودية
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 9l12-3"></path>
                                <path d="M6 9v6a2 2 0 002 2h8a2 2 0 002-2V9"></path>
                                <path d="M6 9l-2 12a2 2 0 002 2h12a2 2 0 002-2l-2-12"></path>
                            </svg>
                            رقم التتبع
                        </div>
                        <div class="info-value"><span class="badge badge-info">TRK-2025-001234</span></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            تاريخ التسليم المتوقع
                        </div>
                        <div class="info-value">2025-01-25</div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">سجل الأنشطة</h3>
                </div>
                <div class="card-body">
                    <div class="schedule-grid">
                        <div class="info-item">
                            <div class="info-label">تم الإنشاء:</div>
                            <div class="info-value">2025-01-15 09:00 - بواسطة أحمد محمد</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">تم التحديث:</div>
                            <div class="info-value">2025-01-15 12:30 - تحديث الوزن</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">تم الإكمال:</div>
                            <div class="info-value">2025-01-15 15:30 - جاهز للشحن</div>
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
                    <a href="{{ route('manufacturing.stage3.index') }}" class="action-btn activate" style="background: linear-gradient(135deg, #9E9E9E, #757575);">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h4>⬅️ عودة للمرحلة 3</h4>
                            <p>العودة لقائمة الكويلات</p>
                        </div>
                    </a>

                    <a href="{{ route('manufacturing.stage4.edit', 1) }}" class="action-btn activate">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h4>تعديل الكرتون</h4>
                            <p>تعديل تفاصيل الكرتون</p>
                        </div>
                    </a>

                    <button type="button" class="action-btn activate" style="background: linear-gradient(135deg, #FF9800, #F57C00); border: none; cursor: pointer;">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h4>🚚 تسليم للعميل</h4>
                            <p>تسليم الكرتون وتحديث حالته</p>
                        </div>
                    </button>

                    <button type="button" class="action-btn delete">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h4>حذف الكرتون</h4>
                            <p>حذف نهائي للكرتون من النظام</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation - Final Stage -->
        <div class="card" style="margin-top: 20px; background: linear-gradient(135deg, #fff3cd, #fffbea); border-left: 5px solid #FF9800;">
            <div class="card-header" style="border-bottom: 2px solid #FF9800;">
                <h3 class="card-title" style="color: #e65100;">📦 هذه المرحلة النهائية</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 20px;">
                    <div>
                        <h4 style="margin: 0 0 5px 0; color: #e65100;">الكراتين المعبأة جاهزة للشحن</h4>
                        <p style="margin: 0; color: #e65100; font-size: 14px;">اضغط الزر أدناه لتسليم الكرتون للعميل</p>
                    </div>
                    <button type="button" onclick="alert('سيتم تسليم الكرتون للعميل قريباً')" style="padding: 12px 24px; background: #FF9800; color: white; border: none; border-radius: 6px; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(255, 152, 0, 0.3); cursor: pointer;">
                        <span>🚚</span>
                        <span>تسليم للعميل</span>
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
                    if (confirm('⚠️ هل أنت متأكد من حذف هذا الكرتون؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        alert('تم حذف الكرتون بنجاح!');
                    }
                });
            });
        });
    </script>
@endsection
