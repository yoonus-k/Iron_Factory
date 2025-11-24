@extends('master')

@section('title', 'إدارة المستخدمين')

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-users"></i>
                إدارة المستخدمين
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المستخدمين</span>
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

        <!-- Main Users Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    قائمة المستخدمين
                </h4>
                @if(auth()->user()->hasPermission('MANAGE_USERS', 'create'))
                <a href="{{ route('users.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إضافة مستخدم جديد
                </a>
                @endif
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث في المستخدمين..." value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="role" class="um-form-control">
                                <option value="">-- اختر الدور --</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                    {{ $role->role_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">-- حالة الحساب --</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <a href="{{ route('users.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Users Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>اسم المستخدم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الدور</th>
                            <th>الفترة الزمنية</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                            </td>
                            <td>
                                <code>{{ $user->username }}</code>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->roleRelation)
                                <span class="um-badge um-badge-info">
                                    {{ $user->roleRelation->role_name }}
                                </span>
                                @else
                                <span class="um-badge um-badge-secondary">
                                    لا يوجد دور
                                </span>
                                @endif
                            </td>
                            <td>{{ $user->shift ?? '-' }}</td>
                            <td>
                                @if($user->is_active)
                                <span class="um-badge um-badge-success">نشط</span>
                                @else
                                <span class="um-badge um-badge-danger">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        @if(auth()->user()->hasPermission('MANAGE_USERS', 'read'))
                                        <a href="{{ route('users.show', $user) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        @endif

                                        @if(auth()->user()->hasPermission('MANAGE_USERS', 'update'))
                                        <a href="{{ route('users.edit', $user) }}" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-edit-2"></i>
                                            <span>تعديل</span>
                                        </a>
                                        @endif

                                        @if(auth()->user()->hasPermission('MANAGE_USERS', 'delete') && $user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" style="display: inline;" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="um-dropdown-item um-btn-delete">
                                                <i class="feather icon-trash-2"></i>
                                                <span>حذف</span>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center" style="padding: 20px;">
                                <p>لا توجد مستخدمين حالياً</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Users Cards - Mobile View -->
            <div class="um-mobile-view">
                @forelse($users as $user)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5;">
                                <i class="feather icon-user"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">{{ $user->name }}</h6>
                                <span class="um-category-id">#{{ $user->username }}</span>
                            </div>
                        </div>
                        <span class="um-badge {{ $user->is_active ? 'um-badge-success' : 'um-badge-danger' }}">
                            {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">البريد الإلكتروني:</span>
                            <span class="um-info-value">{{ $user->email }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الدور:</span>
                            <span class="um-info-value">
                                @if($user->roleRelation)
                                    {{ $user->roleRelation->role_name }}
                                @else
                                    لا يوجد دور
                                @endif
                            </span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الفترة الزمنية:</span>
                            <span class="um-info-value">{{ $user->shift ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                @if(auth()->user()->hasPermission('MANAGE_USERS', 'read'))
                                <a href="{{ route('users.show', $user) }}" class="um-dropdown-item um-btn-view">
                                    <i class="feather icon-eye"></i>
                                    <span>عرض</span>
                                </a>
                                @endif

                                @if(auth()->user()->hasPermission('MANAGE_USERS', 'update'))
                                <a href="{{ route('users.edit', $user) }}" class="um-dropdown-item um-btn-edit">
                                    <i class="feather icon-edit-2"></i>
                                    <span>تعديل</span>
                                </a>
                                @endif

                                @if(auth()->user()->hasPermission('MANAGE_USERS', 'delete') && $user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}" style="display: inline;" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="um-dropdown-item um-btn-delete">
                                        <i class="feather icon-trash-2"></i>
                                        <span>حذف</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 40px;">
                    <p>لا توجد مستخدمين حالياً</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        @if($users->count())
                            عرض {{ $users->firstItem() }} إلى {{ $users->lastItem() }} من أصل {{ $users->total() }} مستخدم
                        @else
                            لا توجد نتائج
                        @endif
                    </p>
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تأكيد الحذف باستخدام SweetAlert2
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: 'هل أنت متأكد من حذف هذا المستخدم؟ هذا الإجراء لا يمكن التراجع عنه!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، احذف',
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
                                title: 'جاري الحذف...',
                                allowOutsideClick: false,
                                didOpen: (modal) => {
                                    Swal.showLoading();
                                }
                            });
                            form.submit();
                        }
                    });
                });
            });

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
        }

        .swal-btn-confirm:hover {
            background-color: #218838 !important;
        }

        .swal-btn-cancel {
            background-color: #6c757d !important;
            color: white !important;
        }

        .swal-btn-cancel:hover {
            background-color: #5a6268 !important;
        }
    </style>
@endsection
