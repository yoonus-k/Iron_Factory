@extends('master')

@section('title', 'إدارة الموردين')

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
    </style>

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-truck"></i>
                إدارة الموردين
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المستودع</span>
                <i class="feather icon-chevron-left"></i>
                <span>الموردين</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        @if (session('success'))
            <div class="um-alert-custom um-alert-success" role="alert">
                <i class="feather icon-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
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
                    <i class="feather icon-list"></i>
                    قائمة الموردين
                </h4>
                <a href="{{ route('manufacturing.suppliers.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إضافة مورد جديد
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث في الموردين..." value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <input type="text" name="phone" class="um-form-control" placeholder="البحث برقم الهاتف..." value="{{ request('phone') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <button type="reset" class="um-btn um-btn-outline" onclick="window.location='{{ route('manufacturing.suppliers.index') }}'">
                                <i class="feather icon-x"></i>
                                إعادة تعيين
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>

                            <th>اسم المورد</th>
                            <th>الشخص المسؤول</th>
                            <th>الهاتف</th>
                            <th>البريد الإلكتروني</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>

                            <td>{{ $supplier->getName() }}</td>
                            <td>{{ $supplier->contact_person }}</td>
                            <td>{{ $supplier->phone }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>
                                @if($supplier->is_active)
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

            <a href="{{ route('manufacturing.suppliers.show', $supplier->id) }}"
               class="um-dropdown-item um-btn-view">
                <i class="feather icon-eye"></i>
                <span>عرض</span>
            </a>

            <a href="{{ route('manufacturing.suppliers.edit', $supplier->id) }}"
               class="um-dropdown-item um-btn-edit">
                <i class="feather icon-edit-2"></i>
                <span>تعديل</span>
            </a>

            <button class="um-dropdown-item um-btn-delete"
                    onclick="deleteSupplier({{ $supplier->id }})">
                <i class="feather icon-trash-2"></i>
                <span>حذف</span>
            </button>

            <button class="um-dropdown-item um-btn-status"
                    onclick="toggleStatus({{ $supplier->id }})">
                <i class="feather {{ $supplier->is_active ? 'icon-toggle-right' : 'icon-toggle-left' }}"></i>
                <span>{{ $supplier->is_active ? 'تعطيل' : 'تفعيل' }}</span>
            </button>

        </div>
    </div>
</td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">لا توجد موردين</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($suppliers->hasPages())
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        عرض {{ $suppliers->firstItem() ?? 0 }} إلى {{ $suppliers->lastItem() ?? 0 }} من أصل
                        {{ $suppliers->total() }} مورد
                    </p>
                </div>
                <div>
                    {{ $suppliers->links('pagination::bootstrap-4') }}
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
        });

        function deleteSupplier(id) {
            if (confirm('⚠️ هل أنت متأكد من حذف هذا المورد؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                // Create a form dynamically
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url('manufacturing/suppliers') }}/' + id;

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Add method spoofing for DELETE
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                // Submit the form
                document.body.appendChild(form);
                form.submit();
            }
        }

        function toggleStatus(id) {
            if (confirm('هل أنت متأكد من تغيير حالة هذا المورد؟')) {
                // Create a form dynamically
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url('manufacturing/suppliers') }}/' + id + '/toggle-status';

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Add method spoofing for PUT
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PUT';
                form.appendChild(methodField);

                // Submit the form
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

@endsection
