@extends('master')

@section('title', 'إدارة الوحدات')

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-box"></i>
                إدارة الوحدات
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>الوحدات</span>
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
                    قائمة الوحدات
                </h4>
                <a href="{{ route('manufacturing.warehouse-settings.units.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إضافة وحدة جديدة
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث في الوحدات..." value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="unit_type" class="um-form-control">
                                <option value="">-- اختر النوع --</option>
                                <option value="weight" {{ request('unit_type') == 'weight' ? 'selected' : '' }}>الوزن</option>
                                <option value="length" {{ request('unit_type') == 'length' ? 'selected' : '' }}>الطول</option>
                                <option value="volume" {{ request('unit_type') == 'volume' ? 'selected' : '' }}>الحجم</option>
                                <option value="area" {{ request('unit_type') == 'area' ? 'selected' : '' }}>المساحة</option>
                                <option value="quantity" {{ request('unit_type') == 'quantity' ? 'selected' : '' }}>الكمية</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="is_active" class="um-form-control">
                                <option value="">-- اختر الحالة --</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <a href="{{ route('manufacturing.warehouse-settings.units.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-refresh-cw"></i>
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
                            <th>رمز الوحدة</th>
                            <th>اسم الوحدة</th>
                            <th>الاختصار</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($units as $unit)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ $unit->unit_code }}</span>
                                </td>
                                <td>
                                    <strong>{{ $unit->unit_name }}</strong><br>
                                    <small class="text-muted">{{ $unit->unit_name_en }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $unit->unit_symbol }}</span>
                                </td>
                                <td>
                                    @php
                                        $types = [
                                            'weight' => 'الوزن',
                                            'length' => 'الطول',
                                            'volume' => 'الحجم',
                                            'area' => 'المساحة',
                                            'quantity' => 'الكمية',
                                            'time' => 'الوقت',
                                            'temperature' => 'درجة الحرارة',
                                            'other' => 'أخرى',
                                        ];
                                    @endphp
                                    {{ $types[$unit->unit_type] ?? $unit->unit_type }}
                                </td>
                                <td>
                                    @if ($unit->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-secondary">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('manufacturing.warehouse-settings.units.show', $unit->id) }}"
                                           class="btn btn-sm btn-info" title="عرض">
                                            <i class="feather icon-eye"></i>
                                        </a>
                                        <a href="{{ route('manufacturing.warehouse-settings.units.edit', $unit->id) }}"
                                           class="btn btn-sm btn-warning" title="تعديل">
                                            <i class="feather icon-edit"></i>
                                        </a>
                                        <form method="POST"
                                              action="{{ route('manufacturing.warehouse-settings.units.destroy', $unit->id) }}"
                                              style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="حذف"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذه الوحدة؟')">
                                                <i class="feather icon-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="feather icon-inbox"></i> لا توجد وحدات لعرضها
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($units->count())
                <div class="um-pagination-section">
                    <div>
                        <p class="um-pagination-info">
                            عرض {{ $units->firstItem() }} إلى {{ $units->lastItem() }} من أصل {{ $units->total() }} وحدة
                        </p>
                    </div>
                    <div>
                        {{ $units->links() }}
                    </div>
                </div>
            @endif
        </section>
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
