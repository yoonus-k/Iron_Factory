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
        <div class="um-alert-custom um-alert-success" role="alert">
            <i class="feather icon-check-circle"></i>
            تم حفظ البيانات بنجاح
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>

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
                                <option value="1">خامات معدنية</option>
                                <option value="2">خامات بلاستيكية</option>
                                <option value="3">خامات خشبية</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="active">متوفر</option>
                                <option value="inactive">قيد الاستخدام</option>
                                <option value="consumed">مستهلك</option>
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
                        <tr>
                            <td>1</td>
                            <td>MAT-2024-001</td>
                            <td>
                                <div class="um-course-info">
                                    <h6 class="um-course-title">ألمنيوم خام</h6>
                                    <p class="um-course-desc">مادة خام من الألمنيوم عالي الجودة مستورد من الصين</p>
                                </div>
                            </td>
                            <td>
                                <span class="um-badge um-badge-info">خامات معدنية</span>
                            </td>
                            <td>500</td>
                            <td>كجم</td>
                            <td>شركة المعادن المتحدة</td>
                            <td>
                                <span class="um-badge um-badge-success">متوفر</span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="#" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="#" class="um-dropdown-item um-btn-edit">
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
                                        <form method="POST" action="#" style="display: inline;" class="delete-form">
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
                            <td>MAT-2024-002</td>
                            <td>
                                <div class="um-course-info">
                                    <h6 class="um-course-title">بلاستيك PVC</h6>
                                    <p class="um-course-desc">بلاستيك من نوع PVC مقاوم للحرارة والرطوبة</p>
                                </div>
                            </td>
                            <td>
                                <span class="um-badge um-badge-info">خامات بلاستيكية</span>
                            </td>
                            <td>300</td>
                            <td>كجم</td>
                            <td>مصنع البلاستيك الحديث</td>
                            <td>
                                <span class="um-badge um-badge-warning">قيد الاستخدام</span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="#" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="#" class="um-dropdown-item um-btn-edit">
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
                                        <form method="POST" action="#" style="display: inline;" class="delete-form">
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
                            <td>3</td>
                            <td>MAT-2024-003</td>
                            <td>
                                <div class="um-course-info">
                                    <h6 class="um-course-title">خشب زان</h6>
                                    <p class="um-course-desc">خشب زان طبيعي عالي الجودة للأثاث الفاخر</p>
                                </div>
                            </td>
                            <td>
                                <span class="um-badge um-badge-info">خامات خشبية</span>
                            </td>
                            <td>200</td>
                            <td>متر مكعب</td>
                            <td>معرض الأخشاب الدولي</td>
                            <td>
                                <span class="um-badge um-badge-success">متوفر</span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="#" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="#" class="um-dropdown-item um-btn-edit">
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
                                        <form method="POST" action="#" style="display: inline;" class="delete-form">
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
                            <td>4</td>
                            <td>MAT-2024-004</td>
                            <td>
                                <div class="um-course-info">
                                    <h6 class="um-course-title">حديد مسلح</h6>
                                    <p class="um-course-desc">قضبان حديد مسلح للإنشاءات والمباني</p>
                                </div>
                            </td>
                            <td>
                                <span class="um-badge um-badge-info">خامات معدنية</span>
                            </td>
                            <td>1000</td>
                            <td>كجم</td>
                            <td>مصنع الحديد والصلب</td>
                            <td>
                                <span class="um-badge um-badge-danger">مستهلك</span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="#" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="#" class="um-dropdown-item um-btn-edit">
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
                                        <form method="POST" action="#" style="display: inline;" class="delete-form">
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
                            <td>5</td>
                            <td>MAT-2024-005</td>
                            <td>
                                <div class="um-course-info">
                                    <h6 class="um-course-title">نحاس أحمر</h6>
                                    <p class="um-course-desc">نحاس أحمر نقي 99.9% للتوصيلات الكهربائية</p>
                                </div>
                            </td>
                            <td>
                                <span class="um-badge um-badge-info">خامات معدنية</span>
                            </td>
                            <td>150</td>
                            <td>كجم</td>
                            <td>شركة النحاس العالمية</td>
                            <td>
                                <span class="um-badge um-badge-success">متوفر</span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="#" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="#" class="um-dropdown-item um-btn-edit">
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
                                        <form method="POST" action="#" style="display: inline;" class="delete-form">
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

            <!-- Courses Cards - Mobile View -->
            <div class="um-mobile-view">
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5;">
                                <i class="feather icon-package"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">ألمنيوم خام</h6>
                                <span class="um-category-id">#MAT-2024-001</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-success">متوفر</span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">الفئة:</span>
                            <span class="um-info-value">خامات معدنية</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الوزن الأصلي:</span>
                            <span class="um-info-value">500 كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الوزن المتبقي:</span>
                            <span class="um-info-value">450 كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">المورد:</span>
                            <span class="um-info-value">شركة المعادن المتحدة</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                <a href="#" class="um-dropdown-item um-btn-view">
                                    <i class="feather icon-eye"></i>
                                    <span>عرض</span>
                                </a>
                                <a href="#" class="um-dropdown-item um-btn-edit">
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
                                <form method="POST" action="#" style="display: inline;" class="delete-form">
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
                                <i class="feather icon-package"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">بلاستيك PVC</h6>
                                <span class="um-category-id">#MAT-2024-002</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-warning">قيد الاستخدام</span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">الفئة:</span>
                            <span class="um-info-value">خامات بلاستيكية</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الوزن الأصلي:</span>
                            <span class="um-info-value">300 كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الوزن المتبقي:</span>
                            <span class="um-info-value">180 كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">المورد:</span>
                            <span class="um-info-value">مصنع البلاستيك الحديث</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                <a href="#" class="um-dropdown-item um-btn-view">
                                    <i class="feather icon-eye"></i>
                                    <span>عرض</span>
                                </a>
                                <a href="#" class="um-dropdown-item um-btn-edit">
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
                                <form method="POST" action="#" style="display: inline;" class="delete-form">
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
                                <i class="feather icon-package"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">خشب زان</h6>
                                <span class="um-category-id">#MAT-2024-003</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-success">متوفر</span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">الفئة:</span>
                            <span class="um-info-value">خامات خشبية</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الوزن الأصلي:</span>
                            <span class="um-info-value">200 متر مكعب</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الوزن المتبقي:</span>
                            <span class="um-info-value">200 متر مكعب</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">المورد:</span>
                            <span class="um-info-value">معرض الأخشاب الدولي</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                <a href="#" class="um-dropdown-item um-btn-view">
                                    <i class="feather icon-eye"></i>
                                    <span>عرض</span>
                                </a>
                                <a href="#" class="um-dropdown-item um-btn-edit">
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
                                <form method="POST" action="#" style="display: inline;" class="delete-form">
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
                        عرض 1 إلى 5 من أصل 5 منتج
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
                    if (confirm('هل أنت متأكد من حذف هذا المنتج؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        alert('تم حذف المنتج بنجاح');
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
