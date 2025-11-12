@extends('master')

@section('title', 'إدارة أذون التسليم')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-file-text"></i>
                إدارة أذون التسليم
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المستودع</span>
                <i class="feather icon-chevron-left"></i>
                <span>أذون التسليم</span>
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
                <i class="feather icon-x-circle"></i>
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
                    قائمة أذون التسليم
                </h4>
                <a href="{{ route('manufacturing.delivery-notes.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إضافة أذن تسليم جديد
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث في أذون التسليم...">
                        </div>
                        <div class="um-form-group">
                            <input type="text" name="delivery_number" class="um-form-control" placeholder="رقم الأذن...">
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="received">مستقبل</option>
                                <option value="pending">قيد الانتظار</option>
                                <option value="rejected">مرفوض</option>
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

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رقم الأذن</th>
                            <th>تاريخ التسليم</th>
                            <th>المورد</th>
                            <th>عدد المواد</th>
                            <th>الوزن الإجمالي</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>DN-2024-001</td>
                            <td>2024-11-01</td>
                            <td>شركة الحديد والصلب</td>
                            <td>5</td>
                            <td>500 كجم</td>
                            <td><span class="um-badge um-badge-success">مستقبل</span></td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.delivery-notes.show', 1) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="{{ route('manufacturing.delivery-notes.edit', 1) }}" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-edit-2"></i>
                                            <span>تعديل</span>
                                        </a>
                                        <button class="um-dropdown-item um-btn-delete" title="حذف">
                                            <i class="feather icon-trash-2"></i>
                                            <span>حذف</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>DN-2024-002</td>
                            <td>2024-11-05</td>
                            <td>شركة المعادن المتحدة</td>
                            <td>3</td>
                            <td>300 كجم</td>
                            <td><span class="um-badge um-badge-warning">قيد الانتظار</span></td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.delivery-notes.show', 2) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="{{ route('manufacturing.delivery-notes.edit', 2) }}" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-edit-2"></i>
                                            <span>تعديل</span>
                                        </a>
                                        <button class="um-dropdown-item um-btn-delete" title="حذف">
                                            <i class="feather icon-trash-2"></i>
                                            <span>حذف</span>
                                        </button>
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
                            <h5>DN-2024-001</h5>
                            <p>شركة الحديد والصلب</p>
                        </div>
                        <span class="um-badge um-badge-success">مستقبل</span>
                    </div>
                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span>التاريخ:</span>
                            <span>2024-11-01</span>
                        </div>
                        <div class="um-info-row">
                            <span>عدد المواد:</span>
                            <span>5</span>
                        </div>
                        <div class="um-info-row">
                            <span>الوزن الإجمالي:</span>
                            <span>500 كجم</span>
                        </div>
                    </div>
                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                <a href="{{ route('manufacturing.delivery-notes.show', 1) }}" class="um-dropdown-item um-btn-view">
                                    <i class="feather icon-eye"></i>
                                    <span>عرض</span>
                                </a>
                                <a href="{{ route('manufacturing.delivery-notes.edit', 1) }}" class="um-dropdown-item um-btn-edit">
                                    <i class="feather icon-edit-2"></i>
                                    <span>تعديل</span>
                                </a>
                                <button class="um-dropdown-item um-btn-delete" title="حذف">
                                    <i class="feather icon-trash-2"></i>
                                    <span>حذف</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
