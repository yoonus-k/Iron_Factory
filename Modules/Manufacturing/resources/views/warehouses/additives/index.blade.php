@extends('master')

@section('title', 'إدارة الصبغات والبلاستيك')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-palette"></i>
                إدارة الصبغات والبلاستيك
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المستودع</span>
                <i class="feather icon-chevron-left"></i>
                <span>الصبغات والبلاستيك</span>
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
                    قائمة الصبغات والبلاستيك
                </h4>
                <a href="{{ route('manufacturing.additives.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إضافة صبغة/بلاستيك جديد
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث في المواد...">
                        </div>
                        <div class="um-form-group">
                            <select name="type" class="um-form-control">
                                <option value="">جميع الأنواع</option>
                                <option value="dye">صبغة</option>
                                <option value="plastic">بلاستيك</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="available">متوفر</option>
                                <option value="low_stock">مخزون منخفض</option>
                                <option value="out_of_stock">غير متوفر</option>
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
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>الكمية</th>
                            <th>الوحدة</th>
                            <th>المورد</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>صبغة أحمر</td>
                            <td>صبغة</td>
                            <td>50</td>
                            <td>لتر</td>
                            <td>شركة الأصباغ المتحدة</td>
                            <td><span class="um-badge um-badge-success">متوفر</span></td>
                            <td>
                                <a href="{{ route('manufacturing.additives.show', 1) }}" class="um-btn-action um-btn-view" title="عرض">
                                    <i class="feather icon-eye"></i>
                                </a>
                                <a href="{{ route('manufacturing.additives.edit', 1) }}" class="um-btn-action um-btn-edit" title="تعديل">
                                    <i class="feather icon-edit-2"></i>
                                </a>
                                <button class="um-btn-action um-btn-delete" title="حذف">
                                    <i class="feather icon-trash-2"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>بلاستيك شفاف</td>
                            <td>بلاستيك</td>
                            <td>20</td>
                            <td>كجم</td>
                            <td>شركة البلاستيك الخليج</td>
                            <td><span class="um-badge um-badge-warning">مخزون منخفض</span></td>
                            <td>
                                <a href="{{ route('manufacturing.additives.show', 2) }}" class="um-btn-action um-btn-view" title="عرض">
                                    <i class="feather icon-eye"></i>
                                </a>
                                <a href="{{ route('manufacturing.additives.edit', 2) }}" class="um-btn-action um-btn-edit" title="تعديل">
                                    <i class="feather icon-edit-2"></i>
                                </a>
                                <button class="um-btn-action um-btn-delete" title="حذف">
                                    <i class="feather icon-trash-2"></i>
                                </button>
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
                            <h5>صبغة أحمر</h5>
                            <p>صبغة</p>
                        </div>
                        <span class="um-badge um-badge-success">متوفر</span>
                    </div>
                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span>الكمية:</span>
                            <span>50 لتر</span>
                        </div>
                        <div class="um-info-row">
                            <span>المورد:</span>
                            <span>شركة الأصباغ</span>
                        </div>
                    </div>
                    <div class="um-category-card-footer">
                        <a href="{{ route('manufacturing.additives.show', 1) }}" class="um-btn-action um-btn-view" title="عرض">
                            <i class="feather icon-eye"></i>
                        </a>
                        <a href="{{ route('manufacturing.additives.edit', 1) }}" class="um-btn-action um-btn-edit" title="تعديل">
                            <i class="feather icon-edit-2"></i>
                        </a>
                        <button class="um-btn-action um-btn-delete" title="حذف">
                            <i class="feather icon-trash-2"></i>
                        </button>
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
