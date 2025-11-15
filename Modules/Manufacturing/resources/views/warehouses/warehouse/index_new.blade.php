@extends('master')

@section('title', 'إدارة المستودعات')

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-home"></i>
                إدارة المستودعات
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المستودعات</span>
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
            <div class="um-alert-custom um-alert-danger" role="alert">
                <i class="feather icon-alert-circle"></i>
                {{ session('error') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        <!-- Main Warehouses Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    قائمة المستودعات
                </h4>
                <a href="{{ route('manufacturing.warehouses.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إضافة مستودع جديد
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث في المستودعات..." value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <a href="{{ route('manufacturing.warehouses.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Warehouses Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم المستودع</th>
                            <th>الرمز</th>
                            <th>الموقع</th>
                            <th>المسؤول</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouses as $key => $warehouse)
                        <tr>
                            <td>{{ $warehouses->firstItem() + $key }}</td>
                            <td>
                                <div class="um-course-info">
                                    <h6 class="um-course-title">{{ $warehouse->warehouse_name }}</h6>
                                    <p class="um-course-desc">{{ $warehouse->description ?? 'بدون وصف' }}</p>
                                </div>
                            </td>
                            <td>{{ $warehouse->warehouse_code }}</td>
                            <td>{{ $warehouse->location ?? 'غير محدد' }}</td>
                            <td>{{ $warehouse->manager_name ?? 'غير محدد' }}</td>
                            <td>
                                <span class="um-badge {{ $warehouse->is_active ? 'um-badge-success' : 'um-badge-danger' }}">
                                    {{ $warehouse->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.warehouses.show', $warehouse->id) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="{{ route('manufacturing.warehouses.edit', $warehouse->id) }}" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-edit-2"></i>
                                            <span>تعديل</span>
                                        </a>
                                        <form method="POST" action="{{ route('manufacturing.warehouses.destroy', $warehouse->id) }}" style="display: inline;" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="um-dropdown-item um-btn-delete">
                                                <i class="feather icon-trash-2"></i>
                                                <span>حذف</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding: 20px;">
                                <p>لا توجد مستودعات حالياً</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Warehouses Cards - Mobile View -->
            <div class="um-mobile-view">
                @forelse($warehouses as $warehouse)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5;">
                                <i class="feather icon-home"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">{{ $warehouse->warehouse_name }}</h6>
                                <span class="um-category-id">#{{ $warehouse->warehouse_code }}</span>
                            </div>
                        </div>
                        <span class="um-badge {{ $warehouse->is_active ? 'um-badge-success' : 'um-badge-danger' }}">
                            {{ $warehouse->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">الموقع:</span>
                            <span class="um-info-value">{{ $warehouse->location ?? 'غير محدد' }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">المسؤول:</span>
                            <span class="um-info-value">{{ $warehouse->manager_name ?? 'غير محدد' }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">السعة:</span>
                            <span class="um-info-value">{{ $warehouse->capacity ?? 'غير محدد' }}</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                <a href="{{ route('manufacturing.warehouses.show', $warehouse->id) }}" class="um-dropdown-item um-btn-view">
                                    <i class="feather icon-eye"></i>
                                    <span>عرض</span>
                                </a>
                                <a href="{{ route('manufacturing.warehouses.edit', $warehouse->id) }}" class="um-dropdown-item um-btn-edit">
                                    <i class="feather icon-edit-2"></i>
                                    <span>تعديل</span>
                                </a>
                                <form method="POST" action="{{ route('manufacturing.warehouses.destroy', $warehouse->id) }}" style="display: inline;" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="um-dropdown-item um-btn-delete">
                                        <i class="feather icon-trash-2"></i>
                                        <span>حذف</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 40px;">
                    <p>لا توجد مستودعات حالياً</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        @if($warehouses->count())
                            عرض {{ $warehouses->firstItem() }} إلى {{ $warehouses->lastItem() }} من أصل {{ $warehouses->total() }} مستودع
                        @else
                            لا توجد نتائج
                        @endif
                    </p>
                </div>
                <div>
                    {{ $warehouses->links() }}
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تأكيد الحذف
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('هل أنت متأكد من حذف هذا المستودع؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        form.submit();
                    }
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
@endsection
