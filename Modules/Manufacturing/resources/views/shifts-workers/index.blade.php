@extends('master')

@section('title', 'إدارة الورديات والعمال')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-users"></i>
                إدارة الورديات والعمال
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>الورديات والعمال</span>
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
        <div class="um-alert-custom um-alert-danger" role="alert">
            <i class="feather icon-alert-circle"></i>
            {{ session('error') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
        @endif

        <!-- Main Courses Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    قائمة الورديات
                </h4>
                <a href="{{ route('manufacturing.shifts-workers.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إضافة وردية جديدة
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET" action="{{ route('manufacturing.shifts-workers.index') }}">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="date" name="date" class="um-form-control" placeholder="التاريخ..." value="{{ request('date') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="shift_type" class="um-form-control">
                                <option value="">جميع أنواع الورديات</option>
                                <option value="morning" {{ request('shift_type') == 'morning' ? 'selected' : '' }}>الفترة الصباحية</option>
                                <option value="evening" {{ request('shift_type') == 'evening' ? 'selected' : '' }}>الفترة المسائية</option>
                                <option value="night" {{ request('shift_type') == 'night' ? 'selected' : '' }}>الفترة الليلية</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشطة</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="stage_number" class="um-form-control">
                                <option value="">جميع المراحل</option>
                                <option value="1" {{ request('stage_number') == '1' ? 'selected' : '' }}>المرحلة الأولى</option>
                                <option value="2" {{ request('stage_number') == '2' ? 'selected' : '' }}>المرحلة الثانية</option>
                                <option value="3" {{ request('stage_number') == '3' ? 'selected' : '' }}>المرحلة الثالثة</option>
                                <option value="4" {{ request('stage_number') == '4' ? 'selected' : '' }}>المرحلة الرابعة</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <a href="{{ route('manufacturing.shifts-workers.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Courses Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رقم الوردية</th>
                            <th>التاريخ</th>
                            <th>نوع الوردية</th>
                            <th>عدد العمال</th>
                            <th>المسؤول</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $index => $shift)
                        <tr>
                            <td>{{ $shifts->firstItem() + $index }}</td>
                            <td><strong>{{ $shift->shift_code }}</strong></td>
                            <td>{{ $shift->shift_date->format('Y-m-d') }}</td>
                            <td>
                                <span class="um-badge um-badge-{{ $shift->shift_type == 'morning' ? 'info' : ($shift->shift_type == 'evening' ? 'warning' : 'danger') }}">
                                    {{ $shift->shift_type_name }}
                                </span>
                            </td>
                            <td>{{ $shift->total_workers }}</td>
                            <td>{{ $shift->supervisor->name ?? 'غير محدد' }}</td>
                            <td>
                                <span class="um-badge um-badge-{{ $shift->status == 'active' ? 'success' : ($shift->status == 'scheduled' ? 'info' : 'secondary') }}">
                                    {{ $shift->status_name }}
                                </span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.shifts-workers.show', $shift->id) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="{{ route('manufacturing.shifts-workers.edit', $shift->id) }}" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-edit-2"></i>
                                            <span>تعديل</span>
                                        </a>
                                        @if($shift->status == 'scheduled')
                                        <form method="POST" action="{{ route('manufacturing.shifts-workers.activate', $shift->id) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="um-dropdown-item um-btn-feature">
                                                <i class="feather icon-play"></i>
                                                <span>تفعيل</span>
                                            </button>
                                        </form>
                                        @endif
                                        @if($shift->status == 'active')
                                        <form method="POST" action="{{ route('manufacturing.shifts-workers.complete', $shift->id) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="um-dropdown-item um-btn-toggle">
                                                <i class="feather icon-check"></i>
                                                <span>إكمال</span>
                                            </button>
                                        </form>
                                        @endif
                                        @if(in_array($shift->status, ['scheduled', 'cancelled']))
                                        <form method="POST" action="{{ route('manufacturing.shifts-workers.destroy', $shift->id) }}" class="delete-form">
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
                            <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                                <i class="feather icon-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                لا توجد ورديات
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Courses Cards - Mobile View -->
            <div class="um-mobile-view">
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                <i class="feather icon-users" style="font-size: 18px;"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">وردية صباحية</h6>
                                <span class="um-category-id">#SHIFT-2025-001</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-success">نشطة</span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">التاريخ:</span>
                            <span class="um-info-value">2025-01-15</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">عدد العمال:</span>
                            <span class="um-info-value">8 عمال</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">المسؤول:</span>
                            <span class="um-info-value">أحمد محمد</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <a href="{{ route('manufacturing.shifts-workers.show', 1) }}" class="um-btn um-btn-sm um-btn-primary">
                            <i class="feather icon-eye" style="font-size: 14px;"></i>
                            عرض
                        </a>
                        <a href="{{ route('manufacturing.shifts-workers.edit', 1) }}" class="um-btn um-btn-sm um-btn-secondary">
                            <i class="feather icon-edit-2" style="font-size: 14px;"></i>
                            تعديل
                        </a>
                    </div>
                </div>

                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                <i class="feather icon-users" style="font-size: 18px;"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">وردية مسائية</h6>
                                <span class="um-category-id">#SHIFT-2025-002</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-success">نشطة</span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">التاريخ:</span>
                            <span class="um-info-value">2025-01-15</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">عدد العمال:</span>
                            <span class="um-info-value">6 عمال</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">المسؤول:</span>
                            <span class="um-info-value">محمد علي</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <a href="{{ route('manufacturing.shifts-workers.show', 2) }}" class="um-btn um-btn-sm um-btn-primary">
                            <i class="feather icon-eye" style="font-size: 14px;"></i>
                            عرض
                        </a>
                        <a href="{{ route('manufacturing.shifts-workers.edit', 2) }}" class="um-btn um-btn-sm um-btn-secondary">
                            <i class="feather icon-edit-2" style="font-size: 14px;"></i>
                            تعديل
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        عرض {{ $shifts->firstItem() ?? 0 }} إلى {{ $shifts->lastItem() ?? 0 }} من أصل {{ $shifts->total() }} وردية
                    </p>
                </div>
                <div>
                    {{ $shifts->links() }}
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
                    if (confirm('هل أنت متأكد من حذف هذه الوردية؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        alert('تم حذف الوردية بنجاح');
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