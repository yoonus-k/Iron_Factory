@extends('master')

@section('title', 'الملف الشخصي')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-user-circle"></i>
                الملف الشخصي للموظف
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>الملف الشخصي</span>
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

        <!-- Main Content -->
        <div class="container">
            <!-- Profile Header Card -->
            <div class="profile-header-card">
                <div class="profile-avatar">
                    <i class="feather icon-user"></i>
                </div>
                <div class="profile-info">
                    <h1 class="profile-name">{{ $user->name }}</h1>
                    <p class="profile-username">{{ $user->username }}</p>
                    <div class="profile-meta">
                        <span class="meta-item">
                            <i class="feather icon-mail"></i>
                            {{ $user->email }}
                        </span>
                        <span class="meta-item">
                            <i class="feather icon-id-card"></i>
                            الرقم: #{{ $user->id }}
                        </span>
                    </div>
                </div>
                <div class="profile-status">
                    <span class="badge {{ $user->is_active ? 'badge-active' : 'badge-inactive' }}">
                        <i class="feather {{ $user->is_active ? 'icon-check-circle' : 'icon-x-circle' }}"></i>
                        {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="grid">
                <!-- User Information Card -->
                <div class="card card-primary">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="feather icon-user"></i>
                        </div>
                        <h3 class="card-title">معلومات الحساب</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-group">
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="feather icon-user"></i>
                                    الاسم الكامل
                                </div>
                                <div class="info-value">{{ $user->name }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="feather icon-at-sign"></i>
                                    اسم المستخدم
                                </div>
                                <div class="info-value">{{ $user->username }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="feather icon-mail"></i>
                                    البريد الإلكتروني
                                </div>
                                <div class="info-value">{{ $user->email }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="feather icon-clock"></i>
                                    الفترة الزمنية
                                </div>
                                <div class="info-value">{{ $user->shift ?? 'غير محدد' }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="feather icon-shield"></i>
                                    الحالة
                                </div>
                                <div class="info-value">
                                    <span class="status-badge {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                                        {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="feather icon-calendar"></i>
                                    تاريخ الإنشاء
                                </div>
                                <div class="info-value">{{ $user->created_at->format('Y-m-d H:i:s') }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="feather icon-calendar"></i>
                                    آخر تحديث
                                </div>
                                <div class="info-value">{{ $user->updated_at->format('Y-m-d H:i:s') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role Information Card -->
                <div class="card card-info">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="feather icon-award"></i>
                        </div>
                        <h3 class="card-title">معلومات الدور</h3>
                    </div>
                    <div class="card-body">
                        @if($user->roleRelation)
                            <div class="role-badge">
                                <span class="role-name">{{ $user->roleRelation->role_name }}</span>
                                <span class="role-level">المستوى {{ $user->roleRelation->level }}</span>
                            </div>

                            <div class="info-group">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="feather icon-file-text"></i>
                                        وصف الدور
                                    </div>
                                    <div class="info-value">
                                        {{ $user->roleRelation->description ?? 'لا يوجد وصف' }}
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="feather icon-info"></i>
                                        حالة الدور
                                    </div>
                                    <div class="info-value">
                                        <span class="status-badge {{ $user->roleRelation->is_active ? 'status-active' : 'status-inactive' }}">
                                            {{ $user->roleRelation->is_active ? 'فعال' : 'معطل' }}
                                        </span>
                                    </div>
                                </div>

                                @if($user->isAdmin())
                                    <div class="admin-badge">
                                        <i class="feather icon-crown"></i>
                                        مسؤول النظام
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="feather icon-alert-triangle"></i>
                                لم يتم تعيين دور لهذا الحساب
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Permissions Card -->
            <div class="card card-permissions">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="feather icon-lock"></i>
                    </div>
                    <h3 class="card-title">الصلاحيات المتاحة</h3>
                    @if($permissions)
                        <span class="permission-count">{{ $permissions->count() }} صلاحية</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($permissions && $permissions->count() > 0)
                        <div class="permissions-grid">
                            @php
                                // Group permissions by group_name
                                $groupedPermissions = $permissions->groupBy('group_name');
                            @endphp

                            @foreach($groupedPermissions as $groupName => $perms)
                                <div class="permission-group">
                                    <div class="group-header">
                                        <h4 class="group-title">{{ $groupName ?? 'عام' }}</h4>
                                    </div>
                                    <div class="permissions-list">
                                        @foreach($perms as $permission)
                                            <div class="permission-item">
                                                <div class="permission-checkbox">
                                                    <i class="feather icon-check"></i>
                                                </div>
                                                <div class="permission-details">
                                                    <div class="permission-name">{{ $permission->display_name }}</div>
                                                    <div class="permission-desc">{{ $permission->description ?? '' }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif($user->isAdmin())
                        <div class="admin-permissions">
                            <i class="feather icon-crown"></i>
                            <p>لديك <strong>جميع الصلاحيات</strong> كمسؤول النظام</p>
                        </div>
                    @else
                        <div class="no-permissions">
                            <i class="feather icon-slash"></i>
                            <p>لا توجد صلاحيات مخصصة لك</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity Card -->
            @if($operationLogs && $operationLogs->count() > 0)
            <div class="card card-logs">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="feather icon-activity"></i>
                    </div>
                    <h3 class="card-title">آخر النشاطات</h3>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        @foreach($operationLogs as $log)
                        <div class="activity-item">
                            <div class="activity-icon {{ strtolower($log->operation_type) }}">
                                <i class="feather icon-circle"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">
                                    <strong>{{ $log->operation_type }}</strong>
                                    @if($log->user)
                                        - بواسطة <span class="text-primary">{{ $log->user->name }}</span>
                                    @endif
                                </div>
                                <div class="activity-description">{{ $log->description ?? '-' }}</div>
                                <div class="activity-time">
                                    <i class="feather icon-clock"></i>
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
@endsection
