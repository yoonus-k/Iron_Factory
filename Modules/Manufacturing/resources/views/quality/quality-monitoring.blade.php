@extends('master')

@section('title', 'مراقبة الجودة')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-check-square"></i>
                مراقبة الجودة
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>الجودة والهدر</span>
                <i class="feather icon-chevron-left"></i>
                <span>مراقبة الجودة</span>
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

        <!-- Statistics Cards -->
        <div class="um-stats-grid">
            <div class="um-stat-card um-stat-success">
                <div class="um-stat-icon">
                    <i class="feather icon-check-circle"></i>
                </div>
                <div class="um-stat-content">
                    <span class="um-stat-label">فحوصات ناجحة اليوم</span>
                    <span class="um-stat-value">142</span>
                    <span class="um-stat-change um-stat-success">
                        <i class="feather icon-trending-up"></i> +5.2%
                    </span>
                </div>
            </div>

            <div class="um-stat-card um-stat-danger">
                <div class="um-stat-icon">
                    <i class="feather icon-x-circle"></i>
                </div>
                <div class="um-stat-content">
                    <span class="um-stat-label">فحوصات مرفوضة</span>
                    <span class="um-stat-value">8</span>
                    <span class="um-stat-change um-stat-danger">
                        <i class="feather icon-alert-triangle"></i> يتطلب اهتمام
                    </span>
                </div>
            </div>

            <div class="um-stat-card um-stat-info">
                <div class="um-stat-icon">
                    <i class="feather icon-percent"></i>
                </div>
                <div class="um-stat-content">
                    <span class="um-stat-label">نسبة القبول</span>
                    <span class="um-stat-value">94.7%</span>
                    <span class="um-stat-change um-stat-success">
                        <i class="feather icon-trending-up"></i> +1.3%
                    </span>
                </div>
            </div>

            <div class="um-stat-card um-stat-warning">
                <div class="um-stat-icon">
                    <i class="feather icon-alert-circle"></i>
                </div>
                <div class="um-stat-content">
                    <span class="um-stat-label">قيد المراجعة</span>
                    <span class="um-stat-value">12</span>
                    <span class="um-stat-change um-stat-warning">
                        <i class="feather icon-clock"></i> بانتظار القرار
                    </span>
                </div>
            </div>
        </div>

        <!-- New Quality Check Form -->
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-plus-circle"></i>
                    فحص جودة جديد
                </h4>
            </div>

            <div class="um-card-body">
                <form method="POST" action="#" class="um-quality-form">
                    @csrf
                    <div class="um-form-row">
                        <div class="um-form-group">
                            <label class="um-form-label">الباركود</label>
                            <div class="um-input-group">
                                <input type="text" name="barcode" class="um-form-control" placeholder="مسح أو إدخال الباركود" required>
                                <button type="button" class="um-btn um-btn-secondary" onclick="scanBarcode()">
                                    <i class="feather icon-maximize"></i>
                                    مسح
                                </button>
                            </div>
                        </div>

                        <div class="um-form-group">
                            <label class="um-form-label">المرحلة</label>
                            <select name="stage" class="um-form-control" required>
                                <option value="">اختر المرحلة</option>
                                <option value="1">المرحلة 1: التقسيم</option>
                                <option value="2">المرحلة 2: المعالجة</option>
                                <option value="3">المرحلة 3: الكويلات</option>
                                <option value="4">المرحلة 4: التغليف</option>
                            </select>
                        </div>

                        <div class="um-form-group">
                            <label class="um-form-label">نوع الفحص</label>
                            <select name="inspection_type" class="um-form-control" required>
                                <option value="">اختر نوع الفحص</option>
                                <option value="visual">فحص بصري</option>
                                <option value="dimensional">فحص الأبعاد</option>
                                <option value="weight">فحص الوزن</option>
                                <option value="color">فحص اللون</option>
                                <option value="strength">فحص المتانة</option>
                                <option value="complete">فحص شامل</option>
                            </select>
                        </div>
                    </div>

                    <div class="um-form-row">
                        <div class="um-form-group">
                            <label class="um-form-label">حالة الفحص</label>
                            <div class="um-radio-group">
                                <label class="um-radio-label">
                                    <input type="radio" name="status" value="passed" required>
                                    <span class="um-radio-custom"></span>
                                    <span class="um-text-success">
                                        <i class="feather icon-check-circle"></i> مقبول
                                    </span>
                                </label>
                                <label class="um-radio-label">
                                    <input type="radio" name="status" value="failed" required>
                                    <span class="um-radio-custom"></span>
                                    <span class="um-text-danger">
                                        <i class="feather icon-x-circle"></i> مرفوض
                                    </span>
                                </label>
                                <label class="um-radio-label">
                                    <input type="radio" name="status" value="review" required>
                                    <span class="um-radio-custom"></span>
                                    <span class="um-text-warning">
                                        <i class="feather icon-alert-circle"></i> يحتاج مراجعة
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="um-form-row">
                        <div class="um-form-group um-form-full">
                            <label class="um-form-label">ملاحظات الفحص</label>
                            <textarea name="notes" class="um-form-control" rows="4" placeholder="أدخل ملاحظات مفصلة عن الفحص..."></textarea>
                        </div>
                    </div>

                    <div class="um-form-actions">
                        <button type="submit" class="um-btn um-btn-primary">
                            <i class="feather icon-save"></i>
                            حفظ الفحص
                        </button>
                        <button type="reset" class="um-btn um-btn-outline">
                            <i class="feather icon-refresh-cw"></i>
                            إعادة تعيين
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Quality Checks History -->
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    سجل فحوصات الجودة
                </h4>
                <div class="um-card-actions">
                    <select class="um-form-control um-filter-select" onchange="filterByStatus(this.value)">
                        <option value="">جميع الحالات</option>
                        <option value="passed">مقبول</option>
                        <option value="failed">مرفوض</option>
                        <option value="review">قيد المراجعة</option>
                    </select>
                </div>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الباركود</th>
                            <th>المرحلة</th>
                            <th>نوع الفحص</th>
                            <th>الحالة</th>
                            <th>الملاحظات</th>
                            <th>المفتش</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><span class="um-badge um-badge-info">CO3-045-2025</span></td>
                            <td>المرحلة 3: الكويلات</td>
                            <td>فحص شامل</td>
                            <td><span class="um-badge um-badge-success">مقبول</span></td>
                            <td>جودة ممتازة، جميع المعايير مستوفاة</td>
                            <td>أحمد محمود</td>
                            <td>{{ date('Y-m-d H:i') }}</td>
                            <td>
                                <button class="um-btn-action um-btn-view" title="عرض التفاصيل" onclick="viewDetails(1)">
                                    <i class="feather icon-eye"></i>
                                </button>
                                <button class="um-btn-action um-btn-edit" title="طباعة" onclick="printReport(1)">
                                    <i class="feather icon-printer"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td><span class="um-badge um-badge-info">ST2-032-2025</span></td>
                            <td>المرحلة 2: المعالجة</td>
                            <td>فحص المتانة</td>
                            <td><span class="um-badge um-badge-danger">مرفوض</span></td>
                            <td>ضعف في المتانة، يحتاج إعادة معالجة</td>
                            <td>خالد سعيد</td>
                            <td>{{ date('Y-m-d H:i', strtotime('-2 hours')) }}</td>
                            <td>
                                <button class="um-btn-action um-btn-view" title="عرض التفاصيل" onclick="viewDetails(2)">
                                    <i class="feather icon-eye"></i>
                                </button>
                                <button class="um-btn-action um-btn-edit" title="طباعة" onclick="printReport(2)">
                                    <i class="feather icon-printer"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td><span class="um-badge um-badge-info">ST1-018-2025</span></td>
                            <td>المرحلة 1: التقسيم</td>
                            <td>فحص الأبعاد</td>
                            <td><span class="um-badge um-badge-warning">قيد المراجعة</span></td>
                            <td>انحراف طفيف في القياسات</td>
                            <td>محمد علي</td>
                            <td>{{ date('Y-m-d H:i', strtotime('-4 hours')) }}</td>
                            <td>
                                <button class="um-btn-action um-btn-view" title="عرض التفاصيل" onclick="viewDetails(3)">
                                    <i class="feather icon-eye"></i>
                                </button>
                                <button class="um-btn-action um-btn-edit" title="طباعة" onclick="printReport(3)">
                                    <i class="feather icon-printer"></i>
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
                            <h5>CO3-045-2025</h5>
                            <p>المرحلة 3: الكويلات</p>
                        </div>
                        <span class="um-badge um-badge-success">مقبول</span>
                    </div>
                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span>نوع الفحص:</span>
                            <span>فحص شامل</span>
                        </div>
                        <div class="um-info-row">
                            <span>الملاحظات:</span>
                            <span>جودة ممتازة، جميع المعايير مستوفاة</span>
                        </div>
                        <div class="um-info-row">
                            <span>المفتش:</span>
                            <span>أحمد محمود</span>
                        </div>
                        <div class="um-info-row">
                            <span>التاريخ:</span>
                            <span>{{ date('Y-m-d H:i') }}</span>
                        </div>
                    </div>
                    <div class="um-category-card-footer">
                        <button class="um-btn-action um-btn-view" onclick="viewDetails(1)">
                            <i class="feather icon-eye"></i>
                        </button>
                        <button class="um-btn-action um-btn-edit" onclick="printReport(1)">
                            <i class="feather icon-printer"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quality Trend Chart -->
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-trending-up"></i>
                    اتجاه الجودة - آخر 7 أيام
                </h4>
            </div>
            <div class="um-card-body">
                <canvas id="qualityTrendChart" height="100"></canvas>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Quality Trend Chart
        const ctx = document.getElementById('qualityTrendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
                datasets: [{
                    label: 'مقبول',
                    data: [135, 142, 138, 145, 148, 140, 142],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'مرفوض',
                    data: [12, 10, 15, 8, 7, 9, 8],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'قيد المراجعة',
                    data: [8, 12, 10, 14, 11, 10, 12],
                    borderColor: 'rgba(255, 205, 86, 1)',
                    backgroundColor: 'rgba(255, 205, 86, 0.2)',
                    tension: 0.4,
                    fill: true
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
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        function scanBarcode() {
            alert('فتح ماسح الباركود...');
        }

        function viewDetails(id) {
            alert('عرض تفاصيل الفحص #' + id);
        }

        function printReport(id) {
            alert('طباعة تقرير الفحص #' + id);
        }

        function filterByStatus(status) {
            alert('فلترة حسب الحالة: ' + (status || 'الكل'));
        }

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.um-alert-custom');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.3s';
                    setTimeout(() => alert.style.display = 'none', 300);
                }, 5000);
            });
        });
    </script>

@endsection
