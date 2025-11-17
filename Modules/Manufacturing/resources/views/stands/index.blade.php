@extends('master')

@section('title', 'إدارة الاستاندات')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-package"></i>
                إدارة الاستاندات
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>الاستاندات</span>
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

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    قائمة الاستاندات
                </h4>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('manufacturing.stands.usage-history') }}" class="um-btn um-btn-info">
                        <i class="feather icon-clock"></i>
                        تاريخ الاستخدام
                    </a>
                    <a href="{{ route('manufacturing.stands.create') }}" class="um-btn um-btn-primary">
                        <i class="feather icon-plus"></i>
                        إضافة استاند جديد
                    </a>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث برقم الاستاند..." value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="unused" {{ request('status') == 'unused' ? 'selected' : '' }}>غير مستخدم</option>
                                <option value="stage1" {{ request('status') == 'stage1' ? 'selected' : '' }}>المرحلة الأولى</option>
                                <option value="stage2" {{ request('status') == 'stage2' ? 'selected' : '' }}>المرحلة الثانية</option>
                                <option value="stage3" {{ request('status') == 'stage3' ? 'selected' : '' }}>المرحلة الثالثة</option>
                                <option value="stage4" {{ request('status') == 'stage4' ? 'selected' : '' }}>المرحلة الرابعة</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <input type="date" name="date" class="um-form-control" placeholder="التاريخ" value="{{ request('date') }}">
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <a href="{{ route('manufacturing.stands.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رقم الاستاند</th>
                            <th>الوزن (كجم)</th>
                            <th>المرحلة الحالية</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stands as $stand)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $stand->stand_number }}</strong></td>
                                <td>{{ number_format($stand->weight, 2) }} كجم</td>
                                <td>
                                    <span class="um-badge {{ $stand->status_badge }}">{{ $stand->status_name }}</span>
                                </td>
                                <td>{{ $stand->created_at->format('Y-m-d') }}</td>
                                <td>
                                    @if($stand->is_active)
                                        <span class="um-badge um-badge-success">نشط</span>
                                    @else
                                        <span class="um-badge um-badge-secondary">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="um-dropdown">
                                        <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="um-dropdown-menu">
                                            <a href="{{ route('manufacturing.stands.show', $stand->id) }}" class="um-dropdown-item um-btn-view">
                                                <i class="feather icon-eye"></i>
                                                <span>عرض</span>
                                            </a>
                                            <a href="{{ route('manufacturing.stands.edit', $stand->id) }}" class="um-dropdown-item um-btn-edit">
                                                <i class="feather icon-edit-2"></i>
                                                <span>تعديل</span>
                                            </a>
                                            <form action="{{ route('manufacturing.stands.toggle-status', $stand->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="um-dropdown-item um-btn-toggle">
                                                    <i class="feather icon-{{ $stand->is_active ? 'pause' : 'play' }}-circle"></i>
                                                    <span>{{ $stand->is_active ? 'تعطيل' : 'تفعيل' }}</span>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('manufacturing.stands.destroy', $stand->id) }}" style="display: inline;" class="delete-form">
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
                                <td colspan="7" class="text-center" style="padding: 40px; color: #999;">
                                    <i class="feather icon-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                    لا توجد استاندات بعد. ابدأ بإضافة استاند جديد!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile View -->
            <div class="um-mobile-view">
                @forelse($stands as $stand)
                    <div class="um-category-card">
                        <div class="um-category-card-header">
                            <div class="um-category-info">
                                <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5;">
                                    <i class="feather icon-package"></i>
                                </div>
                                <div>
                                    <h6 class="um-category-name">{{ $stand->stand_number }}</h6>
                                    <span class="um-category-id">الوزن: {{ number_format($stand->weight, 2) }} كجم</span>
                                </div>
                            </div>
                            <span class="um-badge {{ $stand->status_badge }}">{{ $stand->status_name }}</span>
                        </div>

                        <div class="um-category-card-body">
                            <div class="um-info-row">
                                <span class="um-info-label">تاريخ الإنشاء:</span>
                                <span class="um-info-value">{{ $stand->created_at->format('Y-m-d') }}</span>
                            </div>
                            <div class="um-info-row">
                                <span class="um-info-label">الحالة:</span>
                                <span class="um-info-value">
                                    @if($stand->is_active)
                                        <span class="um-badge um-badge-success">نشط</span>
                                    @else
                                        <span class="um-badge um-badge-secondary">غير نشط</span>
                                    @endif
                                </span>
                            </div>
                            @if($stand->notes)
                                <div class="um-info-row">
                                    <span class="um-info-label">ملاحظات:</span>
                                    <span class="um-info-value">{{ Str::limit($stand->notes, 50) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="um-category-card-footer">
                            <a href="{{ route('manufacturing.stands.show', $stand->id) }}" class="um-btn um-btn-sm um-btn-outline">
                                <i class="feather icon-eye"></i> عرض
                            </a>
                            <a href="{{ route('manufacturing.stands.edit', $stand->id) }}" class="um-btn um-btn-sm um-btn-primary">
                                <i class="feather icon-edit-2"></i> تعديل
                            </a>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 40px; color: #999;">
                        <i class="feather icon-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                        <p>لا توجد استاندات بعد</p>
                        <a href="{{ route('manufacturing.stands.create') }}" class="um-btn um-btn-primary" style="margin-top: 15px;">
                            <i class="feather icon-plus"></i> إضافة استاند جديد
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($stands->hasPages())
                <div class="um-pagination-section">
                    <div>
                        <p class="um-pagination-info">
                            عرض {{ $stands->firstItem() }} إلى {{ $stands->lastItem() }} من أصل {{ $stands->total() }} استاند
                        </p>
                    </div>
                    <div>
                        {{ $stands->links() }}
                    </div>
                </div>
            @endif
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تأكيد الحذف
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('هل أنت متأكد من حذف هذا الاستاند؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        this.submit();
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
