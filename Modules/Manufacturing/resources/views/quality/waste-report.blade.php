@extends('master')

@section('title', 'تقرير الهدر')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-trash"></i>
                تقرير الهدر
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>الجودة والهدر</span>
                <i class="feather icon-chevron-left"></i>
                <span>تقرير الهدر</span>
            </nav>
        </div>

        <!-- Statistics Cards -->
        <div class="um-stats-grid">
            <div class="um-stat-card um-stat-primary">
                <div class="um-stat-icon">
                    <i class="feather icon-trash-2"></i>
                </div>
                <div class="um-stat-content">
                    <span class="um-stat-label">إجمالي الهدر اليوم</span>
                    <span class="um-stat-value">247.5 كجم</span>
                    <span class="um-stat-change um-stat-danger">
                        <i class="feather icon-trending-up"></i> +12%
                    </span>
                </div>
            </div>

            <div class="um-stat-card um-stat-warning">
                <div class="um-stat-icon">
                    <i class="feather icon-percent"></i>
                </div>
                <div class="um-stat-content">
                    <span class="um-stat-label">نسبة الهدر الإجمالية</span>
                    <span class="um-stat-value">4.2%</span>
                    <span class="um-stat-change um-stat-success">
                        <i class="feather icon-trending-down"></i> -0.5%
                    </span>
                </div>
            </div>

            <div class="um-stat-card um-stat-danger">
                <div class="um-stat-icon">
                    <i class="feather icon-alert-triangle"></i>
                </div>
                <div class="um-stat-content">
                    <span class="um-stat-label">حالات تجاوز الحد</span>
                    <span class="um-stat-value">3 حالات</span>
                    <span class="um-stat-change um-stat-danger">
                        <i class="feather icon-alert-circle"></i> يتطلب مراجعة
                    </span>
                </div>
            </div>

            <div class="um-stat-card um-stat-success">
                <div class="um-stat-icon">
                    <i class="feather icon-dollar-sign"></i>
                </div>
                <div class="um-stat-content">
                    <span class="um-stat-label">قيمة الهدر المقدرة</span>
                    <span class="um-stat-value">4,850 ر.س</span>
                    <span class="um-stat-change um-stat-danger">
                        <i class="feather icon-trending-up"></i> +8%
                    </span>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-filter"></i>
                    فلترة التقارير
                </h4>
            </div>

            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <label class="um-form-label">من تاريخ</label>
                            <input type="date" name="date_from" class="um-form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="um-form-group">
                            <label class="um-form-label">إلى تاريخ</label>
                            <input type="date" name="date_to" class="um-form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="um-form-group">
                            <label class="um-form-label">المرحلة</label>
                            <select name="stage" class="um-form-control">
                                <option value="">جميع المراحل</option>
                                <option value="1">المرحلة 1: التقسيم</option>
                                <option value="2">المرحلة 2: المعالجة</option>
                                <option value="3">المرحلة 3: الكويلات</option>
                                <option value="4">المرحلة 4: التغليف</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <label class="um-form-label">الوردية</label>
                            <select name="shift" class="um-form-control">
                                <option value="">جميع الورديات</option>
                                <option value="morning">الصباحية</option>
                                <option value="evening">المسائية</option>
                                <option value="night">الليلية</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <button type="button" class="um-btn um-btn-success" onclick="exportReport()">
                                <i class="feather icon-download"></i>
                                تصدير Excel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <!-- Waste by Stage Chart -->
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-pie-chart"></i>
                    توزيع الهدر حسب المراحل
                </h4>
            </div>
            <div class="um-card-body">
                <canvas id="wasteByStageChart" height="100"></canvas>
            </div>
        </section>

        <!-- Detailed Waste Table -->
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    تفاصيل الهدر
                </h4>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الباركود</th>
                            <th>المرحلة</th>
                            <th>الوزن المدخل</th>
                            <th>الوزن المخرج</th>
                            <th>كمية الهدر</th>
                            <th>النسبة</th>
                            <th>السبب</th>
                            <th>المسؤول</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><span class="um-badge um-badge-info">ST1-001-2025</span></td>
                            <td>المرحلة 1: التقسيم</td>
                            <td>1000 كجم</td>
                            <td>955 كجم</td>
                            <td class="um-text-danger">45 كجم</td>
                            <td>
                                <span class="um-badge um-badge-danger">4.5%</span>
                            </td>
                            <td>عيوب تصنيعية</td>
                            <td>محمد أحمد</td>
                            <td><span class="um-badge um-badge-warning">قيد المراجعة</span></td>
                            <td>{{ date('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td><span class="um-badge um-badge-info">ST2-015-2025</span></td>
                            <td>المرحلة 2: المعالجة</td>
                            <td>500 كجم</td>
                            <td>485 كجم</td>
                            <td class="um-text-warning">15 كجم</td>
                            <td>
                                <span class="um-badge um-badge-warning">3.0%</span>
                            </td>
                            <td>معالجة حرارية</td>
                            <td>أحمد علي</td>
                            <td><span class="um-badge um-badge-success">موافق عليه</span></td>
                            <td>{{ date('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td><span class="um-badge um-badge-info">CO3-023-2025</span></td>
                            <td>المرحلة 3: الكويلات</td>
                            <td>300 كجم</td>
                            <td>288 كجم</td>
                            <td class="um-text-success">12 كجم</td>
                            <td>
                                <span class="um-badge um-badge-success">4.0%</span>
                            </td>
                            <td>قصاصات اعتيادية</td>
                            <td>خالد محمود</td>
                            <td><span class="um-badge um-badge-success">موافق عليه</span></td>
                            <td>{{ date('Y-m-d H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile View -->
            <div class="um-mobile-view">
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <h5>ST1-001-2025</h5>
                            <p>المرحلة 1: التقسيم</p>
                        </div>
                        <span class="um-badge um-badge-danger">4.5%</span>
                    </div>
                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span>الوزن المدخل:</span>
                            <span>1000 كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span>الوزن المخرج:</span>
                            <span>955 كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span>كمية الهدر:</span>
                            <span class="um-text-danger">45 كجم</span>
                        </div>
                        <div class="um-info-row">
                            <span>السبب:</span>
                            <span>عيوب تصنيعية</span>
                        </div>
                        <div class="um-info-row">
                            <span>المسؤول:</span>
                            <span>محمد أحمد</span>
                        </div>
                        <div class="um-info-row">
                            <span>الحالة:</span>
                            <span class="um-badge um-badge-warning">قيد المراجعة</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Waste by Stage Chart
        const ctx = document.getElementById('wasteByStageChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['المرحلة 1: التقسيم', 'المرحلة 2: المعالجة', 'المرحلة 3: الكويلات', 'المرحلة 4: التغليف'],
                datasets: [{
                    label: 'كمية الهدر (كجم)',
                    data: [85, 62, 55, 45],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        rtl: true,
                        labels: {
                            font: {
                                family: 'Cairo, sans-serif',
                                size: 12
                            },
                            padding: 15
                        }
                    },
                    tooltip: {
                        rtl: true,
                        textDirection: 'rtl',
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed + ' كجم';
                                return label;
                            }
                        }
                    }
                }
            }
        });

        function exportReport() {
            alert('جاري تصدير التقرير...');
            // هنا سيتم إضافة كود التصدير لاحقاً
        }
    </script>

@endsection
