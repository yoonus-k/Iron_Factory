@extends('master')

@section('title', 'إدارة العمال')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-users"></i>
                إدارة العمال
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>العمال</span>
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

        <!-- Statistics Cards -->
        <div class="um-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="um-stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px;">
                <div style="font-size: 2.5rem; font-weight: 700;">{{ $stats['total'] }}</div>
                <div style="opacity: 0.9;">إجمالي العمال</div>
            </div>
            <div class="um-stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 20px; border-radius: 12px;">
                <div style="font-size: 2.5rem; font-weight: 700;">{{ $stats['active'] }}</div>
                <div style="opacity: 0.9;">عمال نشطون</div>
            </div>
            <div class="um-stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 20px; border-radius: 12px;">
                <div style="font-size: 2.5rem; font-weight: 700;">{{ $stats['supervisors'] }}</div>
                <div style="opacity: 0.9;">مشرفون</div>
            </div>
            <div class="um-stat-card" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 20px; border-radius: 12px;">
                <div style="font-size: 2.5rem; font-weight: 700;">{{ $stats['workers'] }}</div>
                <div style="opacity: 0.9;">عمال تشغيل</div>
            </div>
        </div>

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    قائمة العمال
                </h4>
                @if(auth()->user()->hasPermission('WORKERS_CREATE'))
                <a href="{{ route('manufacturing.workers.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إضافة عامل جديد
                </a>
                @endif
       </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET" action="{{ route('manufacturing.workers.index') }}">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث (الاسم، الكود، الهوية...)" value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="position" class="um-form-control">
                                <option value="">جميع الوظائف</option>
                                <option value="worker" {{ request('position') == 'worker' ? 'selected' : '' }}>عامل</option>
                                <option value="supervisor" {{ request('position') == 'supervisor' ? 'selected' : '' }}>مشرف</option>
                                <option value="technician" {{ request('position') == 'technician' ? 'selected' : '' }}>فني</option>
                                <option value="quality_inspector" {{ request('position') == 'quality_inspector' ? 'selected' : '' }}>مراقب جودة</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="is_active" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <a href="{{ route('manufacturing.workers.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>كود العامل</th>
                            <th>الاسم</th>
                            <th>الوظيفة</th>
                            <th>رقم الهاتف</th>
                            <th>تفضيل الوردية</th>
                            <th>الأجر/ساعة</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workers as $index => $worker)
                        <tr>
                            <td>{{ $workers->firstItem() + $index }}</td>
                            <td><strong>{{ $worker->worker_code }}</strong></td>
                            <td>{{ $worker->name }}</td>
                            <td>
                                <span class="um-badge um-badge-info">
                                    {{ $worker->position_name }}
                                </span>
                            </td>
                            <td>{{ $worker->phone ?? '-' }}</td>
                            <td>{{ $worker->shift_preference_name }}</td>
                            <td>{{ number_format($worker->hourly_rate, 2) }} ر.س</td>
                            <td>
                                <span class="um-badge um-badge-{{ $worker->is_active ? 'success' : 'secondary' }}">
                                    {{ $worker->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
@if (auth()->user()->hasPermission('WORKERS_READ'))
                                        <a href="{{ route('manufacturing.workers.show', $worker->id) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        @endif
                                        @if (auth()->user()->hasPermission('WORKERS_UPDATE'))
                                        <a href="{{ route('manufacturing.workers.edit', $worker->id) }}" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-edit-2"></i>
                                            <span>تعديل</span>
                                        </a>
                                        <form method="POST" action="{{ route('manufacturing.workers.toggle-status', $worker->id) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="um-dropdown-item um-btn-toggle">
                                                <i class="feather icon-{{ $worker->is_active ? 'pause' : 'play' }}-circle"></i>
                                                <span>{{ $worker->is_active ? 'تعطيل' : 'تفعيل' }}</span>
                                            </button>
                                        </form>
                                        @endif
                                        @if(!$worker->is_active)
                                        @if (auth()->user()->hasPermission('WORKERS_DELETE'))
                                        <form method="POST" action="{{ route('manufacturing.workers.destroy', $worker->id) }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="um-dropdown-item um-btn-delete">
                                                <i class="feather icon-trash-2"></i>
                                                <span>حذف</span>
                                            </button>
                                        </form>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: #999;">
                                <i class="feather icon-users" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                لا يوجد عمال
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="um-mobile-view">
                @foreach($workers as $worker)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #667eea20; color: #667eea; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                <i class="feather icon-user" style="font-size: 18px;"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">{{ $worker->name }}</h6>
                                <span class="um-category-id">{{ $worker->worker_code }}</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-{{ $worker->is_active ? 'success' : 'secondary' }}">
                            {{ $worker->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">الوظيفة:</span>
                            <span class="um-info-value">{{ $worker->position_name }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الهاتف:</span>
                            <span class="um-info-value">{{ $worker->phone ?? '-' }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الأجر/ساعة:</span>
                            <span class="um-info-value">{{ number_format($worker->hourly_rate, 2) }} ر.س</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <a href="{{ route('manufacturing.workers.show', $worker->id) }}" class="um-btn um-btn-sm um-btn-primary">
                            <i class="feather icon-eye" style="font-size: 14px;"></i>
                            عرض
                        </a>
                        <a href="{{ route('manufacturing.workers.edit', $worker->id) }}" class="um-btn um-btn-sm um-btn-secondary">
                            <i class="feather icon-edit-2" style="font-size: 14px;"></i>
                            تعديل
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        عرض {{ $workers->firstItem() ?? 0 }} إلى {{ $workers->lastItem() ?? 0 }} من أصل {{ $workers->total() }} عامل
                    </p>
                </div>
                <div>
                    {{ $workers->links() }}
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
                    if (confirm('هل أنت متأكد من حذف هذا العامل؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
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
