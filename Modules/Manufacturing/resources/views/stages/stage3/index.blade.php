@extends('master')

@section('title', 'المرحلة الثالثة: تصنيع الكويلات')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-package"></i>
                المرحلة الثالثة: تصنيع الكويلات
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المرحلة الثالثة</span>
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
                    قائمة الكويلات
                </h4>
                <a href="{{ route('manufacturing.stage3.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إنشاء كويل جديد
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث برقم الكويل...">
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="created">تم الإنشاء</option>
                                <option value="in_process">قيد الصنع</option>
                                <option value="completed">جاهز</option>
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
                            <th>رقم الكويل</th>
                            <th>الوزن</th>
                            <th>اللون</th>
                            <th>نوع البلاستيك</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><span class="badge badge-info">COIL-001</span></td>
                            <td>245 كجم</td>
                            <td>أحمر</td>
                            <td>بولي إيثيلين</td>
                            <td>
                                <span class="um-badge um-badge-success">جاهز</span>
                            </td>
                            <td>2025-01-15</td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.stage3.show', 1) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="{{ route('manufacturing.stage3.edit', 1) }}" class="um-dropdown-item um-btn-edit">
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
                                <h6 class="um-category-name">COIL-001</h6>
                                <span class="um-category-id">#1</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-success">جاهز</span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">الوزن:</span>
                            <span class="um-info-value">245 كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">اللون:</span>
                            <span class="um-info-value">أحمر</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">نوع البلاستيك:</span>
                            <span class="um-info-value">بولي إيثيلين</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">التاريخ:</span>
                            <span class="um-info-value">2025-01-15</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                <a href="{{ route('manufacturing.stage3.show', 1) }}" class="um-dropdown-item um-btn-view">
                                    <i class="feather icon-eye"></i>
                                    <span>عرض</span>
                                </a>
                                <a href="{{ route('manufacturing.stage3.edit', 1) }}" class="um-dropdown-item um-btn-edit">
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
                        عرض 1 إلى 1 من أصل 1 كويل
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
                    if (confirm('هل أنت متأكد من حذف هذا الكويل؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        alert('تم حذف الكويل بنجاح');
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
