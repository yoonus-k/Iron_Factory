@extends('master')

@section('title', 'إدارة أنواع المواد')

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-package"></i>
                إدارة أنواع المواد
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>أنواع المواد</span>
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
                    قائمة أنواع المواد
                </h4>
                <a href="{{ route('manufacturing.warehouse-settings.material-types.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إضافة نوع مادة جديد
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث في أنواع المواد..." value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="category" class="um-form-control">
                                <option value="">-- اختر الفئة --</option>
                                <option value="raw_material" {{ request('category') == 'raw_material' ? 'selected' : '' }}>خام</option>
                                <option value="finished_product" {{ request('category') == 'finished_product' ? 'selected' : '' }}>منتج نهائي</option>
                                <option value="semi_finished" {{ request('category') == 'semi_finished' ? 'selected' : '' }}>منتج نصف نهائي</option>
                                <option value="additive" {{ request('category') == 'additive' ? 'selected' : '' }}>إضافة</option>
                                <option value="packing_material" {{ request('category') == 'packing_material' ? 'selected' : '' }}>مادة تغليف</option>
                                <option value="component" {{ request('category') == 'component' ? 'selected' : '' }}>مكون</option>
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
                            <a href="{{ route('manufacturing.warehouse-settings.material-types.index') }}" class="um-btn um-btn-outline">
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
                            <th>رمز النوع</th>
                            <th>اسم النوع</th>
                            <th>الفئة</th>
                            <th>التكلفة القياسية</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materialTypes as $materialType)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ $materialType->type_code }}</span>
                                </td>
                                <td>
                                    <strong>{{ $materialType->type_name }}</strong><br>
                                    @if($materialType->type_name_en)
                                        <small class="text-muted">{{ $materialType->type_name_en }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $categories = [
                                            'raw_material' => 'خام',
                                            'finished_product' => 'منتج نهائي',
                                            'semi_finished' => 'منتج نصف نهائي',
                                            'additive' => 'إضافة',
                                            'packing_material' => 'مادة تغليف',
                                            'component' => 'مكون',
                                        ];
                                    @endphp
                                    <span class="badge badge-info">{{ $categories[$materialType->category] ?? $materialType->category }}</span>
                                </td>
                                <td>
                                    @if($materialType->standard_cost)
                                        <span class="text-success">{{ number_format($materialType->standard_cost, 2) }} ر.س</span>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($materialType->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-secondary">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="um-dropdown">
                                        <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>

                                        <div class="um-dropdown-menu">

                                            <a href="{{ route('manufacturing.warehouse-settings.material-types.show', $materialType->id) }}"
                                               class="um-dropdown-item um-btn-view">
                                                <i class="feather icon-eye"></i>
                                                <span>عرض</span>
                                            </a>

                                            <a href="{{ route('manufacturing.warehouse-settings.material-types.edit', $materialType->id) }}"
                                               class="um-dropdown-item um-btn-edit">
                                                <i class="feather icon-edit-2"></i>
                                                <span>تعديل</span>
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('manufacturing.warehouse-settings.material-types.destroy', $materialType->id) }}"
                                                  style="display:inline;" class="delete-form">
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
                                <td colspan="7" class="text-center text-muted">
                                    <i class="feather icon-inbox"></i> لا توجد أنواع مواد لعرضها
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($materialTypes->count())
                <div class="um-pagination-section">
                    <div>
                        <p class="um-pagination-info">
                            عرض {{ $materialTypes->firstItem() }} إلى {{ $materialTypes->lastItem() }} من أصل {{ $materialTypes->total() }} نوع
                        </p>
                    </div>
                    <div>
                        {{ $materialTypes->links() }}
                    </div>
                </div>
            @endif
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete confirmation with SweetAlert2
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: 'هل أنت متأكد من حذف هذا النوع؟ هذا الإجراء لا يمكن التراجع عنه!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
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

@endsection