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
        <div class="um-alert-custom um-alert-success" role="alert">
            <i class="feather icon-check-circle"></i>
            تم حفظ البيانات بنجاح
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>

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
                            <input type="text" name="search" class="um-form-control" placeholder="البحث في المستودعات...">
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
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
                        <tr>
                            <td>1</td>
                            <td>
                                <div class="um-course-info">
                                    <h6 class="um-course-title">المستودع الرئيسي</h6>
                                    <p class="um-course-desc">المستودع الرئيسي للشركة</p>
                                </div>
                            </td>
                            <td>WH-001</td>
                            <td>القاهرة، المنطقة الصناعية</td>
                            <td>أحمد محمد</td>
                            <td>
                                <span class="um-badge um-badge-success">نشط</span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.warehouses.show', 1) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="{{ route('manufacturing.warehouses.edit', 1) }}" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-edit-2"></i>
                                            <span>تعديل</span>
                                        </a>
                                        <button type="button" class="um-dropdown-item um-btn-feature">
                                            <i class="feather icon-star"></i>
                                            <span>تمييز</span>
                                        </button>
                                        <button type="button" class="um-dropdown-item um-btn-toggle">
                                            <i class="feather icon-pause-circle"></i>
                                            <span>تبديل الحالة</span>
                                        </button>
                                        <form method="POST" action="{{ route('manufacturing.warehouses.destroy', 1) }}" style="display: inline;" class="delete-form">
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
                        <tr>
                            <td>2</td>
                            <td>
                                <div class="um-course-info">
                                    <h6 class="um-course-title">المستودع الفرعي</h6>
                                    <p class="um-course-desc">المستودع الفرعي للشركة</p>
                                </div>
                            </td>
                            <td>WH-002</td>
                            <td>الإسكندرية، المنطقة الصناعية</td>
                            <td>محمد علي</td>
                            <td>
                                <span class="um-badge um-badge-success">نشط</span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.warehouses.show', 2) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="{{ route('manufacturing.warehouses.edit', 2) }}" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-edit-2"></i>
                                            <span>تعديل</span>
                                        </a>
                                        <button type="button" class="um-dropdown-item um-btn-feature">
                                            <i class="feather icon-star"></i>
                                            <span>تمييز</span>
                                        </button>
                                        <button type="button" class="um-dropdown-item um-btn-toggle">
                                            <i class="feather icon-pause-circle"></i>
                                            <span>تبديل الحالة</span>
                                        </button>
                                        <form method="POST" action="{{ route('manufacturing.warehouses.destroy', 2) }}" style="display: inline;" class="delete-form">
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
                    </tbody>
                </table>
            </div>

            <!-- Warehouses Cards - Mobile View -->
            <div class="um-mobile-view">
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5;">
                                <i class="feather icon-home"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">المستودع الرئيسي</h6>
                                <span class="um-category-id">#WH-001</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-success">نشط</span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">الموقع:</span>
                            <span class="um-info-value">القاهرة، المنطقة الصناعية</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">المسؤول:</span>
                            <span class="um-info-value">أحمد محمد</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                <a href="{{ route('manufacturing.warehouses.show', 1) }}" class="um-dropdown-item um-btn-view">
                                    <i class="feather icon-eye"></i>
                                    <span>عرض</span>
                                </a>
                                <a href="{{ route('manufacturing.warehouses.edit', 1) }}" class="um-dropdown-item um-btn-edit">
                                    <i class="feather icon-edit-2"></i>
                                    <span>تعديل</span>
                                </a>
                                <button type="button" class="um-dropdown-item um-btn-feature">
                                    <i class="feather icon-star"></i>
                                    <span>تمييز</span>
                                </button>
                                <button type="button" class="um-dropdown-item um-btn-toggle">
                                    <i class="feather icon-pause-circle"></i>
                                    <span>تبديل الحالة</span>
                                </button>
                                <form method="POST" action="{{ route('manufacturing.warehouses.destroy', 1) }}" style="display: inline;" class="delete-form">
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

                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5;">
                                <i class="feather icon-home"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">المستودع الفرعي</h6>
                                <span class="um-category-id">#WH-002</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-success">نشط</span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">الموقع:</span>
                            <span class="um-info-value">الإسكندرية، المنطقة الصناعية</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">المسؤول:</span>
                            <span class="um-info-value">محمد علي</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                <a href="{{ route('manufacturing.warehouses.show', 2) }}" class="um-dropdown-item um-btn-view">
                                    <i class="feather icon-eye"></i>
                                    <span>عرض</span>
                                </a>
                                <a href="{{ route('manufacturing.warehouses.edit', 2) }}" class="um-dropdown-item um-btn-edit">
                                    <i class="feather icon-edit-2"></i>
                                    <span>تعديل</span>
                                </a>
                                <button type="button" class="um-dropdown-item um-btn-feature">
                                    <i class="feather icon-star"></i>
                                    <span>تمييز</span>
                                </button>
                                <button type="button" class="um-dropdown-item um-btn-toggle">
                                    <i class="feather icon-pause-circle"></i>
                                    <span>تبديل الحالة</span>
                                </button>
                                <form method="POST" action="{{ route('manufacturing.warehouses.destroy', 2) }}" style="display: inline;" class="delete-form">
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
            </div>

            <!-- Pagination -->
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        عرض 1 إلى 2 من أصل 2 مستودع
                    </p>
                </div>
                <div>
                    <!-- يمكن إضافة pagination links هنا -->
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
                        // form.submit(); // Uncomment this line to actually submit the form
                        alert('تم حذف المستودع بنجاح');
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