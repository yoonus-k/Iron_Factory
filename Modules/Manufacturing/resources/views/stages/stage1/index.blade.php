@extends('master')

@section('title', 'المرحلة الأولى: التقسيم والاستاندات')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-package"></i>
                المرحلة الأولى: التقسيم والاستاندات
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المرحلة الأولى</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        <div class="um-alert-custom um-alert-success" role="alert" style="display: none;">
            <i class="feather icon-check-circle"></i>
            تم حفظ البيانات بنجاح
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    قائمة الاستاندات
                </h4>
                <a href="{{ route('manufacturing.stage1.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إضافة استاند جديد
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث بالباركود...">
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="created">تم الإنشاء</option>
                                <option value="in_process">قيد المعالجة</option>
                                <option value="completed">مكتمل</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <input type="date" name="date" class="um-form-control" placeholder="التاريخ">
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

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الباركود</th>
                            <th>المادة الخام</th>
                            <th>رقم الاستاند</th>
                            <th>حجم السلك</th>
                            <th>الوزن</th>
                            <th>نسبة الهدر</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>ST1-001-2025</td>
                            <td>
                                <div class="um-course-info">
                                    <h6 class="um-course-title">مادة خام رقم 1</h6>
                                    <p class="um-course-desc">حديد مسلح عالي الجودة</p>
                                </div>
                            </td>
                            <td>ST-001</td>
                            <td>2.5 مم</td>
                            <td>250 كجم</td>
                            <td>
                                <span class="um-badge um-badge-danger">5.2%</span>
                            </td>
                            <td>
                                <span class="um-badge um-badge-success">مكتمل</span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.stage1.show', 1) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="{{ route('manufacturing.stage1.edit', 1) }}" class="um-dropdown-item um-btn-edit">
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
                            <td>ST1-002-2025</td>
                            <td>
                                <div class="um-course-info">
                                    <h6 class="um-course-title">مادة خام رقم 2</h6>
                                    <p class="um-course-desc">ألمنيوم نقي</p>
                                </div>
                            </td>
                            <td>ST-002</td>
                            <td>3.0 مم</td>
                            <td>300 كجم</td>
                            <td>
                                <span class="um-badge um-badge-danger">6.8%</span>
                            </td>
                            <td>
                                <span class="um-badge um-badge-warning">قيد المعالجة</span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.stage1.show', 2) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="{{ route('manufacturing.stage1.edit', 2) }}" class="um-dropdown-item um-btn-edit">
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
                            <td>ST1-003-2025</td>
                            <td>
                                <div class="um-course-info">
                                    <h6 class="um-course-title">مادة خام رقم 3</h6>
                                    <p class="um-course-desc">نحاس أحمر</p>
                                </div>
                            </td>
                            <td>ST-003</td>
                            <td>2.0 مم</td>
                            <td>200 كجم</td>
                            <td>
                                <span class="um-badge um-badge-danger">4.5%</span>
                            </td>
                            <td>
                                <span class="um-badge um-badge-success">مكتمل</span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.stage1.show', 3) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="{{ route('manufacturing.stage1.edit', 3) }}" class="um-dropdown-item um-btn-edit">
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

            <!-- Cards - Mobile View -->
            <div class="um-mobile-view">
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5;">
                                <i class="feather icon-package"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">ST-001</h6>
                                <span class="um-category-id">#ST1-001-2025</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-success">مكتمل</span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">المادة الخام:</span>
                            <span class="um-info-value">مادة خام رقم 1</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">حجم السلك:</span>
                            <span class="um-info-value">2.5 مم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الوزن:</span>
                            <span class="um-info-value">250 كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">نسبة الهدر:</span>
                            <span class="um-info-value">5.2%</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                <a href="{{ route('manufacturing.stage1.show', 1) }}" class="um-dropdown-item um-btn-view">
                                    <i class="feather icon-eye"></i>
                                    <span>عرض</span>
                                </a>
                                <a href="{{ route('manufacturing.stage1.edit', 1) }}" class="um-dropdown-item um-btn-edit">
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
                                <h6 class="um-category-name">ST-002</h6>
                                <span class="um-category-id">#ST1-002-2025</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-warning">قيد المعالجة</span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">المادة الخام:</span>
                            <span class="um-info-value">مادة خام رقم 2</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">حجم السلك:</span>
                            <span class="um-info-value">3.0 مم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">الوزن:</span>
                            <span class="um-info-value">300 كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">نسبة الهدر:</span>
                            <span class="um-info-value">6.8%</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                <a href="{{ route('manufacturing.stage1.show', 2) }}" class="um-dropdown-item um-btn-view">
                                    <i class="feather icon-eye"></i>
                                    <span>عرض</span>
                                </a>
                                <a href="{{ route('manufacturing.stage1.edit', 2) }}" class="um-dropdown-item um-btn-edit">
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
                        عرض 1 إلى 3 من أصل 3 استاندات
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
                    if (confirm('هل أنت متأكد من حذف هذا الاستاند؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        alert('تم حذف الاستاند بنجاح');
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
