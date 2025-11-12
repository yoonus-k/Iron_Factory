@extends('master')


@section('title', 'إدارة المنتجات')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-package"></i>
                إدارة المنتجات
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المنتجات</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        @if (session('success'))
            <div class="um-alert-custom um-alert-success" role="alert">
                <i class="feather icon-check-circle"></i>
                الرسالة هنا
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="um-alert-custom um-alert-error" role="alert">
                <i class="feather icon-x-circle"></i>
                رسالة خطأ هنا
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
                    قائمة المنتجات
                </h4>
                <a href="{{ route('manufacturing.warehouse-products.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إضافة منتج جديد
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث في المنتجات...">
                        </div>
                        <div class="um-form-group">
                            <select name="category_id" class="um-form-control">
                                <option value="">جميع الفئات</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="active">مفعل</option>
                                <option value="inactive">معطل</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <button type="reset" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                إعادة تعيين
                            </button>
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
                            <th>رمز المادة</th>
                            <th>اسم المادة</th>
                            <th>الفئة</th>
                            <th>الوزن الأصلي</th>
                            <th>الوحدة</th>
                            <th>المورد</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @forelse($materials as $material)
                            <tr>
                                <td>{{ $material->id }}</td>
                                <td>{{ $material->barcode }}</td>
                                <td>
                                    <div class="um-course-info">
                                        <h6 class="um-course-title">{{ $material->material_type }}</h6>
                                        <p class="um-course-desc">{{ Str::limit($material->notes, 50) }}</p>
                                    </div>
                                </td>
                                <td>
                                    <span class="um-badge um-badge-info">{{ $material->material_type }}</span>
                                </td>
                                <td>{{ $material->original_weight }}</td>
                                <td>{{ $material->unit }}</td>
                                <td>
                                    @if ($material->supplier)
                                        {{ $material->supplier->name }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($material->status == 'available')
                                        <span class="um-badge um-badge-success">متوفر</span>
                                    @elseif ($material->status == 'in_use')
                                        <span class="um-badge um-badge-warning">قيد الاستخدام</span>
                                    @elseif ($material->status == 'consumed')
                                        <span class="um-badge um-badge-danger">مستهلك</span>
                                    @else
                                        <span class="um-badge um-badge-secondary">{{ $material->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="um-action-buttons">
                                        <a href="{{ route('manufacturing.warehouse-products.show', $material->id) }}" class="um-btn-action um-btn-view" title="عرض">
                                            <i class="feather icon-eye"></i>
                                        </a>
                                        <a href="{{ route('manufacturing.warehouse-products.edit', $material->id) }}" class="um-btn-action um-btn-edit" title="تعديل">
                                            <i class="feather icon-edit-2"></i>
                                        </a>
                                        <button type="button" class="um-btn-action um-btn-feature" title="تمييز">
                                            <i class="feather icon-star"></i>
                                        </button>
                                        <button type="button" class="um-btn-action um-btn-toggle" title="تبديل الحالة">
                                            <i class="feather icon-pause-circle"></i>
                                        </button>
                                        <form method="POST" action="{{ route('manufacturing.warehouse-products.destroy', $material->id) }}" style="display: inline;" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="um-btn-action um-btn-delete" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                                <i class="feather icon-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="um-empty-state">
                                        <div class="um-empty-icon">
                                            <i class="feather icon-package"></i>
                                        </div>
                                        <h5 class="um-empty-title">لا توجد منتجات</h5>
                                        <p class="um-empty-desc">لم يتم العثور على أي منتجات مطابقة للبحث</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse --}}
                    </tbody>
                </table>
            </div>

            <!-- Courses Cards - Mobile View -->
            <div class="um-mobile-view">
                {{-- @forelse($materials as $material)
                    <div class="um-category-card">
                        <div class="um-category-card-header">
                            <div class="um-category-info">
                                <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5;">
                                    <i class="feather icon-package"></i>
                                </div>
                                <div>
                                    <h6 class="um-category-name">{{ $material->material_type }}</h6>
                                    <span class="um-category-id">#{{ $material->barcode }}</span>
                                </div>
                            </div>
                            @if ($material->status == 'available')
                                <span class="um-badge um-badge-success">متوفر</span>
                            @elseif ($material->status == 'in_use')
                                <span class="um-badge um-badge-warning">قيد الاستخدام</span>
                            @elseif ($material->status == 'consumed')
                                <span class="um-badge um-badge-danger">مستهلك</span>
                            @else
                                <span class="um-badge um-badge-secondary">{{ $material->status }}</span>
                            @endif
                        </div>

                        <div class="um-category-card-body">
                            <div class="um-info-row">
                                <span class="um-info-label">الفئة:</span>
                                <span class="um-info-value">{{ $material->material_type }}</span>
                            </div>
                            <div class="um-info-row">
                                <span class="um-info-label">الوزن الأصلي:</span>
                                <span class="um-info-value">{{ $material->original_weight }} {{ $material->unit }}</span>
                            </div>
                            <div class="um-info-row">
                                <span class="um-info-label">الوزن المتبقي:</span>
                                <span class="um-info-value">{{ $material->remaining_weight }} {{ $material->unit }}</span>
                            </div>
                            <div class="um-info-row">
                                <span class="um-info-label">المورد:</span>
                                <span class="um-info-value">
                                    @if ($material->supplier)
                                        {{ $material->supplier->name }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="um-category-card-footer">
                            <a href="{{ route('manufacturing.warehouse-products.show', $material->id) }}" class="um-btn-action um-btn-view" title="عرض">
                                <i class="feather icon-eye"></i>
                            </a>
                            <a href="{{ route('manufacturing.warehouse-products.edit', $material->id) }}" class="um-btn-action um-btn-edit" title="تعديل">
                                <i class="feather icon-edit-2"></i>
                            </a>
                            <button type="button" class="um-btn-action um-btn-feature" title="تمييز">
                                <i class="feather icon-star"></i>
                            </button>
                            <button type="button" class="um-btn-action um-btn-toggle" title="تبديل الحالة">
                                <i class="feather icon-pause-circle"></i>
                            </button>
                            <form method="POST" action="{{ route('manufacturing.warehouse-products.destroy', $material->id) }}" style="display: inline;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="um-btn-action um-btn-delete" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                    <i class="feather icon-trash-2"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="um-empty-state">
                        <div class="um-empty-icon">
                            <i class="feather icon-package"></i>
                        </div>
                        <h5 class="um-empty-title">لا توجد منتجات</h5>
                        <p class="um-empty-desc">لم يتم العثور على أي منتجات مطابقة للبحث</p>
                    </div>
                @endforelse --}}
            </div>

            <!-- Pagination -->
            {{-- @if ($materials->hasPages())
                <div class="um-pagination-section">
                    <div>
                        <p class="um-pagination-info">
                            عرض {{ $materials->firstItem() ?? 0 }} إلى {{ $materials->lastItem() ?? 0 }} من أصل
                            {{ $materials->total() }}
                            منتج
                        </p>
                    </div>
                    <div>
                        {{ $materials->links() }}
                    </div>
                </div>
            @endif --}}
        </section>
    </div>
 <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تأكيد الحذف
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm(
                            'هل أنت متأكد من حذف هذا المنتج؟\n\nهذا الإجراء لا يمكن التراجع عنه!'
                        )) {
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
