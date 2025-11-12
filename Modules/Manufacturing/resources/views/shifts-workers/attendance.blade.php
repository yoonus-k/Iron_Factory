@extends('master')

@section('title', 'سجل الحضور')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-user-check"></i>
                سجل الحضور
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>الورديات والعمال</span>
                <i class="feather icon-chevron-left"></i>
                <span>سجل الحضور</span>
            </nav>
        </div>

        <!-- Attendance Records Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    سجل الحضور اليومي
                </h4>
                <div class="um-card-actions">
                    <button class="um-btn um-btn-primary">
                        <i class="feather icon-download"></i>
                        تصدير التقرير
                    </button>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="date" name="date" class="um-form-control" value="2025-01-15">
                        </div>
                        <div class="um-form-group">
                            <select name="shift_type" class="um-form-control">
                                <option value="">جميع الورديات</option>
                                <option value="morning">صباحية</option>
                                <option value="evening">مسائية</option>
                                <option value="night">ليلية</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <input type="text" name="worker_name" class="um-form-control" placeholder="اسم العامل">
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-filter"></i>
                                تصفية
                            </button>
                            <button type="reset" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                إعادة تعيين
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Attendance Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>اسم العامل</th>
                            <th>رقم الوردية</th>
                            <th>التاريخ</th>
                            <th>نوع الوردية</th>
                            <th>وقت الحضور</th>
                            <th>وقت المغادرة</th>
                            <th>الحالة</th>
                            <th>ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>أحمد محمد</td>
                            <td>SHIFT-2025-001</td>
                            <td>2025-01-15</td>
                            <td>
                                <span class="um-badge um-badge-info">صباحية</span>
                            </td>
                            <td>08:05 صباحاً</td>
                            <td>-</td>
                            <td>
                                <span class="um-badge um-badge-success">حاضر</span>
                            </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>محمد علي</td>
                            <td>SHIFT-2025-001</td>
                            <td>2025-01-15</td>
                            <td>
                                <span class="um-badge um-badge-info">صباحية</span>
                            </td>
                            <td>08:02 صباحاً</td>
                            <td>-</td>
                            <td>
                                <span class="um-badge um-badge-success">حاضر</span>
                            </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>عمر خالد</td>
                            <td>SHIFT-2025-001</td>
                            <td>2025-01-15</td>
                            <td>
                                <span class="um-badge um-badge-info">صباحية</span>
                            </td>
                            <td>08:10 صباحاً</td>
                            <td>-</td>
                            <td>
                                <span class="um-badge um-badge-success">حاضر</span>
                            </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>خالد أحمد</td>
                            <td>SHIFT-2025-002</td>
                            <td>2025-01-15</td>
                            <td>
                                <span class="um-badge um-badge-warning">مسائية</span>
                            </td>
                            <td>14:05 مساءً</td>
                            <td>-</td>
                            <td>
                                <span class="um-badge um-badge-success">حاضر</span>
                            </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>سامي عبد الله</td>
                            <td>SHIFT-2025-002</td>
                            <td>2025-01-15</td>
                            <td>
                                <span class="um-badge um-badge-warning">مسائية</span>
                            </td>
                            <td>13:55 مساءً</td>
                            <td>-</td>
                            <td>
                                <span class="um-badge um-badge-success">حاضر</span>
                            </td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            </div>
        </section>
    </div>

@endsection