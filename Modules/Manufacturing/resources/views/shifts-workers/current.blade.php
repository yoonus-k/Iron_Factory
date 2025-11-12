@extends('master')

@section('title', 'الورديات الحالية')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-clock"></i>
                الورديات الحالية
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>الورديات والعمال</span>
                <i class="feather icon-chevron-left"></i>
                <span>الورديات الحالية</span>
            </nav>
        </div>

        <!-- Active Shifts Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-activity"></i>
                    الورديات النشطة حالياً
                </h4>
                <div class="um-card-actions">
                    <button class="um-btn um-btn-outline">
                        <i class="feather icon-refresh-cw"></i>
                        تحديث
                    </button>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث في الورديات...">
                        </div>
                        <div class="um-form-group">
                            <select name="shift_type" class="um-form-control">
                                <option value="">جميع أنواع الورديات</option>
                                <option value="morning">صباحية</option>
                                <option value="evening">مسائية</option>
                                <option value="night">ليلية</option>
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

            <!-- Active Shifts Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>رقم الوردية</th>
                            <th>التاريخ</th>
                            <th>نوع الوردية</th>
                            <th>المسؤول</th>
                            <th>وقت البدء</th>
                            <th>عدد العمال</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>SHIFT-2025-001</td>
                            <td>2025-01-15</td>
                            <td>
                                <span class="um-badge um-badge-info">صباحية</span>
                            </td>
                            <td>أحمد محمد</td>
                            <td>08:00 صباحاً</td>
                            <td>8</td>
                            <td>
                                <span class="um-badge um-badge-success">نشطة</span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.shifts-workers.show', 1) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض التفاصيل</span>
                                        </a>
                                        <a href="#" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-check-circle"></i>
                                            <span>إنهاء الوردية</span>
                                        </a>
                                        <button type="button" class="um-dropdown-item um-btn-toggle">
                                            <i class="feather icon-pause-circle"></i>
                                            <span>تعليق الوردية</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>SHIFT-2025-002</td>
                            <td>2025-01-15</td>
                            <td>
                                <span class="um-badge um-badge-warning">مسائية</span>
                            </td>
                            <td>محمد علي</td>
                            <td>14:00 مساءً</td>
                            <td>6</td>
                            <td>
                                <span class="um-badge um-badge-success">نشطة</span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.shifts-workers.show', 2) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض التفاصيل</span>
                                        </a>
                                        <a href="#" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-check-circle"></i>
                                            <span>إنهاء الوردية</span>
                                        </a>
                                        <button type="button" class="um-dropdown-item um-btn-toggle">
                                            <i class="feather icon-pause-circle"></i>
                                            <span>تعليق الوردية</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            </div>
        </section>
    </div>

@endsection