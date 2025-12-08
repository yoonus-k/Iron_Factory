@extends('master')

@section('content')
    <style>
        /* Pagination Styling */
        .um-pagination-section {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px 0;
            border-top: 1px solid #e9ecef;
        }

        .um-pagination-info {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }

        /* Bootstrap Pagination Custom Styling */
        .pagination {
            margin: 0;
            gap: 5px;
            display: flex;
            flex-wrap: wrap;
        }

        .pagination .page-link {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            color: #3498db;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 500;
            background-color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 36px;
            text-align: center;
            cursor: pointer;
        }

        .pagination .page-link:hover {
            background-color: #f0f2f5;
            border-color: #3498db;
            color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.15);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-color: #2980b9;
            color: white;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            border-color: #dee2e6;
            background-color: #f8f9fa;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .pagination .page-item.disabled .page-link:hover {
            transform: none;
            box-shadow: none;
        }

        @media (max-width: 768px) {
            .um-pagination-section {
                flex-direction: column;
                align-items: stretch;
            }

            .um-pagination-info {
                text-align: center;
            }

            .pagination {
                justify-content: center;
            }

            .pagination .page-link {
                padding: 6px 10px;
                font-size: 12px;
                min-width: 32px;
            }
        }

        /* Filter Form Styles */
        .filter-form {
            padding: 0;
        }

        .filter-form .form-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .filter-form .form-control,
        .filter-form .form-select {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .filter-form .form-control:focus,
        .filter-form .form-select:focus {
            border-color: #0051E5;
            box-shadow: 0 0 0 3px rgba(0, 81, 229, 0.1);
            background-color: white;
        }

        .filter-form .form-control::placeholder {
            color: #adb5bd;
        }

        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(0, 81, 229, 0.3);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
            color: #2c3e50;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background-color: #dee2e6;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }

        .card-header {
            border-bottom: 2px solid rgba(0, 0, 0, 0.1);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #3E4651 0%, #2C3339 100%);
        }

        .border-left-danger {
            border-left: 4px solid #0051E5;
        }

        .border-left-success {
            border-left: 4px solid #3E4651;
        }

        .border-left-info {
            border-left: 4px solid #0051E5;
        }

        .text-danger {
            color: #0051E5 !important;
        }

        .text-success {
            color: #3E4651 !important;
        }

        .text-info {
            color: #0051E5 !important;
        }

        .bg-warning {
            background-color: #e8f0ff !important;
            color: #0051E5 !important;
        }

        .badge-danger {
            background-color: #0051E5 !important;
            color: white !important;
        }

        .bg-success {
            background-color: #3E4651 !important;
            color: white !important;
        }

        .bg-info {
            background-color: #e8f0ff !important;
            color: #0051E5 !important;
        }

        .bg-danger {
            background-color: #ff6b6b !important;
            color: white !important;
        }

        .btn-info {
            background-color: #0051E5;
            border-color: #0051E5;
            color: white;
        }

        .btn-info:hover {
            background-color: #003FA0;
            border-color: #003FA0;
            color: white;
        }

        .btn-success {
            background-color: #0051E5;
            border-color: #0051E5;
            color: white;
        }

        .btn-success:hover {
            background-color: #003FA0;
            border-color: #003FA0;
            color: white;
        }

        .btn-warning {
            background-color: #0051E5;
            border-color: #0051E5;
            color: white;
        }

        .btn-warning:hover {
            background-color: #003FA0;
            border-color: #003FA0;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table thead th {
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem 0.75rem;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .badge {
            font-weight: 500;
        }

        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .table-responsive {
            border-radius: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .filter-form .row {
                gap: 0;
            }

            .filter-form .col-md-2,
            .filter-form .col-md-3 {
                margin-bottom: 15px;
            }
        }

        /* Custom badge styles */
        .badge-pending {
            background: #0051E5;
            color: white;
        }

        .badge-registered {
            background: #3E4651;
            color: white;
        }

        .badge-moved {
            background: #27ae60;
            color: white;
        }

        .badge-warning-custom {
            background-color: #e74c3c;
            color: white;
        }

        /* Status column styling */
        .status-column {
            min-width: 180px;
        }

        /* Dropdown Styles */
        .um-dropdown {
            position: relative;
            display: inline-block;
        }

        .um-btn-dropdown {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 6px 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .um-btn-dropdown:hover {
            background-color: #e9ecef;
        }

        .um-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            display: none;
            min-width: 160px;
            padding: 8px 0;
            margin: 5px 0 0;
            font-size: 14px;
            text-align: right;
            list-style: none;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
        }

        .um-dropdown-menu.show {
            display: block;
        }

        .um-dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            clear: both;
            font-weight: 400;
            color: #212529;
            text-align: inherit;
            text-decoration: none;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
            transition: all 0.2s ease;
        }

        .um-dropdown-item:hover {
            background-color: #f8f9fa;
            color: #16181b;
        }

        .um-dropdown-item i {
            font-size: 16px;
        }

        /* Alert Styles */
        .um-alert-custom {
            position: relative;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .um-alert-success {
            color: #0f5132;
            background-color: #d1e7dd;
            border-color: #badbcc;
        }

        .um-alert-error {
            color: #842029;
            background-color: #f8d7da;
            border-color: #f5c2c7;
        }

        .um-alert-close {
            position: absolute;
            top: 10px;
            left: 15px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .um-alert-close:hover {
            opacity: 1;
        }

        /* UM Design System */
        .um-content-wrapper {
            padding: 20px;
        }

        .um-header-section {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .um-page-title {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .um-breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #6c757d;
        }

        .um-breadcrumb-nav span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .um-main-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            overflow: hidden;
            margin-bottom: 25px;
        }

        .um-card-header {
            padding: 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .um-card-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2c3e50;
        }

        .um-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 15px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }

        .um-btn-primary {
            background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%);
            color: white;
        }

        .um-btn-primary:hover {
            box-shadow: 0 4px 12px rgba(0, 81, 229, 0.3);
            transform: translateY(-1px);
        }

        .um-btn-outline {
            background-color: transparent;
            border: 1px solid #0051E5;
            color: #0051E5;
        }

        .um-btn-outline:hover {
            background-color: #0051E5;
            color: white;
        }

        .um-table-responsive {
            overflow-x: auto;
        }

        .um-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .um-table thead th {
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem 0.75rem;
            background-color: #f8f9fa;
        }

        .um-table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }

        .um-table tbody tr:last-child td {
            border-bottom: none;
        }

        .um-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Filter Section */
        .um-filters-section {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            background-color: #f8f9fa;
        }

        .um-filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: end;
        }

        .um-form-group {
            flex: 1;
            min-width: 200px;
        }

        .um-form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
        }

        .um-form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            background-color: white;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .um-form-control:focus {
            border-color: #0051E5;
            box-shadow: 0 0 0 3px rgba(0, 81, 229, 0.1);
            outline: none;
        }

        .um-filter-actions {
            display: flex;
            gap: 10px;
            align-items: end;
        }

        @media (max-width: 768px) {
            .um-filter-row {
                flex-direction: column;
            }

            .um-form-group {
                min-width: 100%;
            }

            .um-filter-actions {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="fas fa-key"></i>
                {{ __('permissions.manage_permissions') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('permissions.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('permissions.roles_and_permissions') }}</span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('permissions.permissions') }}</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        @if(session('success'))
            <div class="um-alert-custom um-alert-success" role="alert">
                <i class="feather icon-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="um-alert-custom um-alert-error" role="alert">
                <i class="feather icon-x-circle"></i>
                {{ session('error') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="fas fa-key"></i>
                    {{ __('permissions.permissions_list') }}
                </h4>
                @if(canCreate('MANAGE_PERMISSIONS'))
                    <a href="{{ route('permissions.create') }}" class="um-btn um-btn-primary">
                        <i class="fas fa-plus"></i>
                        {{ __('permissions.add_new_permission_btn') }}
                    </a>
                @endif
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET" action="{{ route('permissions.index') }}" class="filter-form">
                    <div class="um-filter-row">
                        <!-- Module Filter -->
                        <div class="um-form-group">
                            <label class="form-label">{{ __('permissions.filter_by_module') }}</label>
                            <select class="um-form-control" id="moduleFilter" onchange="filterByModule()">
                                <option value="">{{ __('permissions.all_modules') }}</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ $module }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-filter"></i>
                                {{ __('permissions.filter_btn') }}
                            </button>
                            <a href="{{ route('permissions.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-refresh-cw"></i>
                                {{ __('permissions.reset_btn') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="um-table-responsive">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>اسم الصلاحية</th>
                            <th>الاسم بالإنجليزية</th>
                            <th>الكود</th>
                            <th>الوحدة</th>
                            <th>عدد الأدوار</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                            <tr data-module="{{ $permission->module }}">
                                <td>{{ $permission->id }}</td>
                                <td>
                                    {{ $permission->permission_name }}
                                    @if($permission->is_system)
                                        <span class="badge bg-info">نظام</span>
                                    @endif
                                </td>
                                <td>{{ $permission->permission_name_en ?? '-' }}</td>
                                <td><code>{{ $permission->permission_code }}</code></td>
                                <td><span class="badge bg-secondary">{{ $permission->module }}</span></td>
                                <td>{{ $permission->roles->count() }}</td>
                                <td>
                                    @if($permission->is_active)
                                        <span class="badge bg-success">{{ __('permissions.active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('permissions.inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="um-dropdown">
                                        <button class="um-btn-dropdown" type="button">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="um-dropdown-menu">
                                            @if(canUpdate('MANAGE_PERMISSIONS'))
                                                <a href="{{ route('permissions.edit', $permission) }}" class="um-dropdown-item um-btn-edit">
                                                    <i class="feather icon-edit-2"></i>
                                                    <span>{{ __('permissions.edit') }}</span>
                                                </a>
                                            @endif

                                            @if(canDelete('MANAGE_PERMISSIONS') && !$permission->is_system)
                                                <form method="POST" action="{{ route('permissions.destroy', $permission) }}" style="display: inline;" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="um-dropdown-item um-btn-delete" style="width: 100%; text-align: right; border: none; background: none; cursor: pointer;">
                                                        <i class="feather icon-trash-2"></i>
                                                        <span>{{ __('permissions.delete') }}</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    <i class="feather icon-inbox"></i> {{ __('permissions.no_permissions') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($permissions->hasPages())
                <div class="um-pagination-section">
                    <div>
                        <p class="um-pagination-info">
                            عرض {{ $permissions->firstItem() }} إلى {{ $permissions->lastItem() }} من أصل
                            {{ $permissions->total() }} صلاحية
                        </p>
                    </div>
                    <div>
                        {{ $permissions->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            // Delete confirmation
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: '{{ __('permissions.confirm_delete') }}',
                        text: 'هل أنت متأكد من حذف هذه الصلاحية؟ هذا الإجراء لا يمكن التراجع عنه!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '{{ __('permissions.yes_delete') }}',
                        cancelButtonText: '{{ __('permissions.cancel') }}',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Dropdown functionality
            document.querySelectorAll('.um-btn-dropdown').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const dropdown = this.closest('.um-dropdown');
                    const menu = dropdown.querySelector('.um-dropdown-menu');

                    // Close all other dropdowns
                    document.querySelectorAll('.um-dropdown-menu').forEach(d => {
                        if (d !== menu) {
                            d.classList.remove('show');
                        }
                    });

                    // Toggle current dropdown
                    menu.classList.toggle('show');
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                document.querySelectorAll('.um-dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            });

            // Prevent closing dropdown when clicking inside
            document.querySelectorAll('.um-dropdown-menu').forEach(menu => {
                menu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

            // Module filter functionality
            window.filterByModule = function() {
                const module = document.getElementById('moduleFilter').value;
                const rows = document.querySelectorAll('tbody tr[data-module]');

                rows.forEach(row => {
                    if (module === '' || row.dataset.module === module) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            };

            // Apply filter on page load if module is selected
            filterByModule();
        });
    </script>
@endsection
