@extends('master')

@section('title', 'إدارة الموردين')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-truck"></i>
                إدارة الموردين
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المستودع</span>
                <i class="feather icon-chevron-left"></i>
                <span>الموردين</span>
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
                    قائمة الموردين
                </h4>
                <a href="{{ route('manufacturing.suppliers.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    إضافة مورد جديد
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث في الموردين...">
                        </div>
                        <div class="um-form-group">
                            <input type="text" name="phone" class="um-form-control" placeholder="البحث برقم الهاتف...">
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

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم المورد</th>
                            <th>الشخص المسؤول</th>
                            <th>الهاتف</th>
                            <th>البريد الإلكتروني</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>شركة الحديد والصلب</td>
                            <td>محمود علي</td>
                            <td>0123456789</td>
                            <td>supplier1@example.com</td>
                            <td><span class="um-badge um-badge-success">نشط</span></td>
                            <td>
                                <a href="{{ route('manufacturing.suppliers.show', 1) }}" class="um-btn-action um-btn-view" title="عرض">
                                    <i class="feather icon-eye"></i>
                                </a>
                                <a href="{{ route('manufacturing.suppliers.edit', 1) }}" class="um-btn-action um-btn-edit" title="تعديل">
                                    <i class="feather icon-edit-2"></i>
                                </a>
                                <button class="um-btn-action um-btn-delete" title="حذف">
                                    <i class="feather icon-trash-2"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>شركة المعادن المتحدة</td>
                            <td>أحمد محمد</td>
                            <td>0198765432</td>
                            <td>supplier2@example.com</td>
                            <td><span class="um-badge um-badge-success">نشط</span></td>
                            <td>
                                <a href="{{ route('manufacturing.suppliers.show', 2) }}" class="um-btn-action um-btn-view" title="عرض">
                                    <i class="feather icon-eye"></i>
                                </a>
                                <a href="{{ route('manufacturing.suppliers.edit', 2) }}" class="um-btn-action um-btn-edit" title="تعديل">
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
                            <h5>شركة الحديد والصلب</h5>
                            <p>محمود علي</p>
                        </div>
                        <span class="um-badge um-badge-success">نشط</span>
                    </div>
                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span>الهاتف:</span>
                            <span>0123456789</span>
                        </div>
                        <div class="um-info-row">
                            <span>البريد:</span>
                            <span>supplier1@example.com</span>
                        </div>
                    </div>
                    <div class="um-category-card-footer">
                        <a href="{{ route('manufacturing.suppliers.show', 1) }}" class="um-btn-action um-btn-view" title="عرض">
                            <i class="feather icon-eye"></i>
                        </a>
                        <a href="{{ route('manufacturing.suppliers.edit', 1) }}" class="um-btn-action um-btn-edit" title="تعديل">
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
