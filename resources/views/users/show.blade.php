@extends('master')

@section('title', 'تفاصيل المستخدم')

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-user"></i>
                تفاصيل المستخدم: {{ $user->name }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المستخدمين</span>
                <i class="feather icon-chevron-left"></i>
                <span>تفاصيل المستخدم</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        @if (session('success'))
            <div class="um-alert-custom um-alert-success" role="alert" id="successMessage">
                <div class="um-alert-content">
                    <i class="feather icon-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="um-alert-custom um-alert-danger" role="alert" id="errorMessage">
                <div class="um-alert-content">
                    <i class="feather icon-alert-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if (session('warning'))
            <div class="um-alert-custom um-alert-warning" role="alert" id="warningMessage">
                <div class="um-alert-content">
                    <i class="feather icon-alert-triangle"></i>
                    <span>{{ session('warning') }}</span>
                </div>
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        <!-- Main Content -->
        <div class="container">
            <div class="page-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="course-icon">
                            <i class="feather icon-user"></i>
                        </div>
                        <div class="header-info">
                            <h1>{{ $user->name }}</h1>
                            @if($user->username)
                                <h2 class="text-muted">{{ $user->username }}</h2>
                            @endif
                            <div class="badges">
                                <span class="badge category">
                                    #{{ $user->id }}
                                </span>
                                <span class="badge {{ $user->is_active ? 'active' : 'inactive' }}">
                                    {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="header-actions">
                        @if(auth()->user()->hasPermission('MANAGE_USERS', 'update'))
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-edit">
                            <i class="feather icon-edit"></i>
                            تعديل
                        </a>
                        @endif

                        @if($user->is_active && auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('users.impersonate', $user) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-impersonate" title="تسجيل الدخول كموظف">
                                <i class="feather icon-log-in"></i>
                                دخول كموظف
                            </button>
                        </form>
                        @endif

                        @if(auth()->user()->hasPermission('MANAGE_USERS', 'update'))
                        <form method="POST" action="{{ route('users.resend-credentials', $user) }}" style="display: inline;" class="resend-form">
                            @csrf
                            <button type="submit" class="btn btn-mail" title="إرسال بيانات الدخول للبريد الإلكتروني">
                                <i class="feather icon-send"></i>
                                إرسال بيانات الدخول
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('users.index') }}" class="btn btn-back">
                            <i class="feather icon-arrow-right"></i>
                            العودة
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid">
                <!-- User Information Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon primary">
                            <i class="feather icon-user"></i>
                        </div>
                        <h3 class="card-title">معلومات المستخدم</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label">الاسم الكامل:</div>
                            <div class="info-value">{{ $user->name }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">اسم المستخدم:</div>
                            <div class="info-value">{{ $user->username }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">البريد الإلكتروني:</div>
                            <div class="info-value">{{ $user->email }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">الفترة الزمنية:</div>
                            <div class="info-value">{{ $user->shift ?? 'غير محدد' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">الحالة:</div>
                            <div class="info-value">
                                <span class=" {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">رقم المستخدم:</div>
                            <div class="info-value">{{ $user->id }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">تاريخ الإنشاء:</div>
                            <div class="info-value">{{ $user->created_at->format('Y-m-d H:i:s') }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">تاريخ التحديث:</div>
                            <div class="info-value">{{ $user->updated_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Role and Permissions Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon info">
                            <i class="feather icon-shield"></i>
                        </div>
                        <h3 class="card-title">الدور والصلاحيات</h3>
                    </div>
                    <div class="card-body">
                        @if($user->roleRelation)
                        <div class="info-item">
                            <div class="info-label">الدور الأساسي:</div>
                            <div class="info-value">
                                <span class="">{{ $user->roleRelation->role_name }}</span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">وصف الدور:</div>
                            <div class="info-value">{{ $user->roleRelation->description ?? 'لا يوجد وصف' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">مستوى الدور:</div>
                            <div class="info-value">
                                <span class="">المستوى {{ $user->roleRelation->level }}</span>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning">
                            لم يتم تعيين دور لهذا المستخدم
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Operation Logs Card -->
            @if($operationLogs->count() > 0)
            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <i class="feather icon-clock"></i>
                    </div>
                    <h3 class="card-title">سجل العمليات</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="margin: 0;">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">التاريخ والوقت</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">نوع العملية</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">التفاصيل</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">المستخدم</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($operationLogs->take(10) as $log)
                                <tr style="border-bottom: 1px solid #e9ecef;">
                                    <td style="padding: 12px; font-size: 13px; color: #2c3e50;">
                                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td style="padding: 12px;">
                                        <span class="badge badge-dark">{{ $log->operation_type }}</span>
                                    </td>
                                    <td style="padding: 12px; font-size: 12px; color: #2c3e50;">
                                        {{ $log->description ?? '-' }}
                                    </td>
                                    <td style="padding: 12px; font-size: 12px; color: #2c3e50;">
                                        {{ $log->user->name ?? '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .course-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .header-info h1 {
            margin: 0 0 8px 0;
            font-size: 24px;
            color: #2c3e50;
            font-weight: 700;
        }

        .header-info h2 {
            margin: 0 0 12px 0;
            font-size: 16px;
            color: #7f8c8d;
            font-weight: 500;
        }

        .badges {
            display: flex;
            gap: 10px;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge.category {
            background: #e9ecef;
            color: #495057;
        }

        .badge.active {
            background: #28a745;
            color: white;
        }

        .badge.inactive {
            background: #dc3545;
            color: white;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-edit {
            background: #007bff;
            color: white;
        }

        .btn-edit:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .btn-impersonate {
            background: #6f42c1;
            color: white;
        }

        .btn-impersonate:hover {
            background: #5a32a3;
            transform: translateY(-2px);
        }

        .btn-mail {
            background: #28a745;
            color: white;
        }

        .btn-mail:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background: #545b62;
            transform: translateY(-2px);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
            overflow: hidden;
        }

        .card-header {
            display: flex;
            align-items: center;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 15px;
            color: white;
            font-size: 18px;
        }

        .card-icon.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-icon.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .card-icon.success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .card-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
        }

        .card-body {
            padding: 20px;
        }

        .info-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f3f5;
        }

        .info-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-label {
            flex: 0 0 150px;
            font-weight: 600;
            color: #495057;
            font-size: 14px;
        }

        .info-value {
            flex: 1;
            color: #2c3e50;
            font-size: 14px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            text-align: right;
            background: #f8f9fa;
            font-weight: 600;
        }

        .table td, .table th {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: stretch;
            }

            .header-left {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
                justify-content: center;
            }

            .btn {
                flex: 1;
                justify-content: center;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .info-item {
                flex-direction: column;
                gap: 5px;
            }

            .info-label {
                flex: 0 0 auto;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // معالج نموذج إرسال بيانات الدخول
            const resendForm = document.querySelector('.resend-form');
            if (resendForm) {
                resendForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'تأكيد الإرسال',
                        text: 'هل تريد إرسال بيانات دخول جديدة للبريد الإلكتروني؟',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، أرسل',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'swal-btn-confirm',
                            cancelButton: 'swal-btn-cancel'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading message
                            Swal.fire({
                                title: 'جاري الإرسال...',
                                allowOutsideClick: false,
                                didOpen: (modal) => {
                                    Swal.showLoading();
                                }
                            });
                            resendForm.submit();
                        }
                    });
                });
            }

            // إخفاء التنبيهات تلقائياً بعد 5 ثواني
            const alerts = document.querySelectorAll('.um-alert-custom');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.3s';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 300);
                }, 5000);
            });
        });
    </script>

    <style>
        /* Alert Styling */
        .um-alert-custom {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.4s ease;
            border-left: 4px solid;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .um-alert-custom .um-alert-content {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .um-alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #28a745;
        }

        .um-alert-success i {
            color: #28a745;
            font-size: 20px;
        }

        .um-alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #dc3545;
        }

        .um-alert-danger i {
            color: #dc3545;
            font-size: 20px;
        }

        .um-alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffc107;
        }

        .um-alert-warning i {
            color: #ffc107;
            font-size: 20px;
        }

        .um-alert-close {
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            font-size: 20px;
            padding: 0;
            margin-left: 15px;
            transition: transform 0.2s;
        }

        .um-alert-close:hover {
            transform: scale(1.2);
        }

        /* SweetAlert2 Custom Styling */
        .swal-btn-confirm {
            background-color: #28a745 !important;
            color: white !important;
            border: none !important;
            padding: 10px 20px !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            transition: all 0.3s !important;
        }

        .swal-btn-confirm:hover {
            background-color: #218838 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 8px rgba(33, 136, 56, 0.3) !important;
        }

        .swal-btn-cancel {
            background-color: #6c757d !important;
            color: white !important;
            border: none !important;
            padding: 10px 20px !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            transition: all 0.3s !important;
        }

        .swal-btn-cancel:hover {
            background-color: #5a6268 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 8px rgba(90, 98, 104, 0.3) !important;
        }
    </style>
@endsection
