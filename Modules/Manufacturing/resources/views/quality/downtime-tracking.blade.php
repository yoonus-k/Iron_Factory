@extends('master')

@section('title', __('app.quality.downtime.title'))

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-alert-triangle"></i>
                {{ __('app.quality.downtime.title') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('app.quality.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('app.quality.quality_waste') }}</span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('app.quality.downtime.breadcrumb') }}</span>
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
            <div class="um-stat-card um-stat-danger">
                <div class="um-stat-icon">
                    <i class="feather icon-power"></i>
                </div>
                <div class="um-stat-content">
                    <span class="um-stat-label">{{ __('app.quality.downtime.active_issues') }}</span>
                    <span class="um-stat-value">2</span>
                    <span class="um-stat-change um-stat-danger">
                        <i class="feather icon-alert-circle"></i> {{ __('app.quality.downtime.title') }}
                    </span>
                </div>
            </div>

            <div class="um-stat-card um-stat-warning">
                <div class="um-stat-icon">
                    <i class="feather icon-clock"></i>
                </div>
                <div class="um-stat-content">
                    <span class="um-stat-label">{{ __('app.quality.downtime.total_today') }}</span>
                    <span class="um-stat-value">3.5 ساعة</span>
                    <span class="um-stat-change um-stat-danger">
                        <i class="feather icon-trending-up"></i> +25%
                    </span>
                </div>
            </div>

            <div class="um-stat-card um-stat-info">
                <div class="um-stat-icon">
                    <i class="feather icon-tool"></i>
                </div>
                <div class="um-stat-content">
                    <span class="um-stat-label">{{ __('app.quality.downtime.avg_resolution') }}</span>
                    <span class="um-stat-value">7</span>
                    <span class="um-stat-change um-stat-success">
                        <i class="feather icon-check-circle"></i> معالج بنجاح
                    </span>
                </div>
            </div>

            <div class="um-stat-card um-stat-primary">
                <div class="um-stat-icon">
                    <i class="feather icon-target"></i>
                </div>
                <div class="um-stat-content">
                    <span class="um-stat-label">{{ __('app.quality.downtime.cost_today') }}</span>
                    <span class="um-stat-value">28 دقيقة</span>
                    <span class="um-stat-change um-stat-success">
                        <i class="feather icon-trending-down"></i> -5 دقائق
                    </span>
                </div>
            </div>
        </div>

        <!-- New Downtime Entry -->
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-plus-circle"></i>
                    {{ __('app.quality.downtime.new_entry') }}
                </h4>
            </div>

            <div class="um-card-body">
                <form method="POST" action="#" class="um-downtime-form">
                    @csrf
                    <div class="um-form-row">
                        <div class="um-form-group">
                            <label class="um-form-label">المرحلة المتأثرة</label>
                            <select name="stage" class="um-form-control" required>
                                <option value="">اختر المرحلة</option>
                                <option value="1">المرحلة 1: التقسيم</option>
                                <option value="2">المرحلة 2: المعالجة</option>
                                <option value="3">المرحلة 3: الكويلات</option>
                                <option value="4">المرحلة 4: التغليف</option>
                                <option value="warehouse">المستودع</option>
                            </select>
                        </div>

                        <div class="um-form-group">
                            <label class="um-form-label">نوع التوقف</label>
                            <select name="downtime_type" class="um-form-control" required onchange="toggleOtherReason(this.value)">
                                <option value="">اختر نوع التوقف</option>
                                <option value="mechanical">عطل ميكانيكي</option>
                                <option value="electrical">عطل كهربائي</option>
                                <option value="maintenance">صيانة دورية</option>
                                <option value="material_shortage">نقص مواد</option>
                                <option value="quality_issue">مشكلة جودة</option>
                                <option value="safety">أسباب سلامة</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>

                        <div class="um-form-group">
                            <label class="um-form-label">الأولوية</label>
                            <select name="priority" class="um-form-control" required>
                                <option value="">اختر الأولوية</option>
                                <option value="critical">حرجة - توقف كامل</option>
                                <option value="high">عالية - تأثير كبير</option>
                                <option value="medium">متوسطة</option>
                                <option value="low">منخفضة</option>
                            </select>
                        </div>
                    </div>

                    <div class="um-form-row" id="otherReasonGroup" style="display: none;">
                        <div class="um-form-group um-form-full">
                            <label class="um-form-label">سبب التوقف (أخرى)</label>
                            <input type="text" name="other_reason" class="um-form-control" placeholder="حدد سبب التوقف...">
                        </div>
                    </div>

                    <div class="um-form-row">
                        <div class="um-form-group">
                            <label class="um-form-label">وقت بداية التوقف</label>
                            <input type="datetime-local" name="start_time" class="um-form-control" required value="{{ date('Y-m-d\TH:i') }}">
                        </div>

                        <div class="um-form-group">
                            <label class="um-form-label">وقت نهاية التوقف (اختياري)</label>
                            <input type="datetime-local" name="end_time" class="um-form-control">
                        </div>

                        <div class="um-form-group">
                            <label class="um-form-label">مدة التوقف التقديرية (دقائق)</label>
                            <input type="number" name="estimated_duration" class="um-form-control" placeholder="30" min="1">
                        </div>
                    </div>

                    <div class="um-form-row">
                        <div class="um-form-group um-form-full">
                            <label class="um-form-label">وصف المشكلة</label>
                            <textarea name="description" class="um-form-control" rows="4" placeholder="وصف تفصيلي للمشكلة والأعراض..." required></textarea>
                        </div>
                    </div>

                    <div class="um-form-row">
                        <div class="um-form-group um-form-full">
                            <label class="um-form-label">الإجراء المتخذ (اختياري)</label>
                            <textarea name="action_taken" class="um-form-control" rows="3" placeholder="الإجراءات التي تم اتخاذها لحل المشكلة..."></textarea>
                        </div>
                    </div>

                    <div class="um-form-actions">
                        <button type="submit" class="um-btn um-btn-primary">
                            <i class="feather icon-save"></i>
                            حفظ التوقف
                        </button>
                        <button type="reset" class="um-btn um-btn-outline">
                            <i class="feather icon-refresh-cw"></i>
                            إعادة تعيين
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Active Downtimes -->
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-alert-circle"></i>
                    {{ __('app.quality.downtime.active_title') }}
                </h4>
                </h4>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المرحلة</th>
                            <th>نوع التوقف</th>
                            <th>الأولوية</th>
                            <th>وقت البداية</th>
                            <th>المدة الحالية</th>
                            <th>الوصف</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="um-row-danger">
                            <td>1</td>
                            <td>المرحلة 2: المعالجة</td>
                            <td><span class="um-badge um-badge-danger">عطل ميكانيكي</span></td>
                            <td><span class="um-badge um-badge-danger">حرجة</span></td>
                            <td>{{ date('Y-m-d H:i', strtotime('-45 minutes')) }}</td>
                            <td class="um-text-danger"><strong>45 دقيقة</strong></td>
                            <td>تعطل المحرك الرئيسي</td>
                            <td>
                                <button class="um-btn um-btn-success um-btn-sm" onclick="resolveDowntime(1)">
                                    <i class="feather icon-check"></i> حل
                                </button>
                            </td>
                        </tr>
                        <tr class="um-row-warning">
                            <td>2</td>
                            <td>المرحلة 3: الكويلات</td>
                            <td><span class="um-badge um-badge-warning">نقص مواد</span></td>
                            <td><span class="um-badge um-badge-warning">عالية</span></td>
                            <td>{{ date('Y-m-d H:i', strtotime('-20 minutes')) }}</td>
                            <td class="um-text-warning"><strong>20 دقيقة</strong></td>
                            <td>نقص في الصبغة الحمراء</td>
                            <td>
                                <button class="um-btn um-btn-success um-btn-sm" onclick="resolveDowntime(2)">
                                    <i class="feather icon-check"></i> حل
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile View -->
            <div class="um-mobile-view">
                <div class="um-category-card um-card-danger">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <h5>عطل ميكانيكي</h5>
                            <p>المرحلة 2: المعالجة</p>
                        </div>
                        <span class="um-badge um-badge-danger">حرجة</span>
                    </div>
                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span>الوصف:</span>
                            <span>تعطل المحرك الرئيسي</span>
                        </div>
                        <div class="um-info-row">
                            <span>المدة:</span>
                            <span class="um-text-danger"><strong>45 دقيقة</strong></span>
                        </div>
                        <div class="um-info-row">
                            <span>البداية:</span>
                            <span>{{ date('Y-m-d H:i', strtotime('-45 minutes')) }}</span>
                        </div>
                    </div>
                    <div class="um-category-card-footer">
                        <button class="um-btn um-btn-success um-btn-sm" onclick="resolveDowntime(1)">
                            <i class="feather icon-check"></i> حل المشكلة
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Downtime History -->
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    سجل الأعطال والتوقفات
                </h4>
                <div class="um-card-actions">
                    <select class="um-form-control um-filter-select" onchange="filterDowntime(this.value)">
                        <option value="all">جميع الفترات</option>
                        <option value="today">اليوم</option>
                        <option value="week">هذا الأسبوع</option>
                        <option value="month">هذا الشهر</option>
                    </select>
                </div>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المرحلة</th>
                            <th>نوع التوقف</th>
                            <th>الأولوية</th>
                            <th>وقت البداية</th>
                            <th>وقت النهاية</th>
                            <th>المدة</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>المرحلة 1: التقسيم</td>
                            <td>صيانة دورية</td>
                            <td><span class="um-badge um-badge-info">متوسطة</span></td>
                            <td>{{ date('Y-m-d 08:00') }}</td>
                            <td>{{ date('Y-m-d 08:45') }}</td>
                            <td>45 دقيقة</td>
                            <td><span class="um-badge um-badge-success">محلول</span></td>
                            <td>
                                <button class="um-btn-action um-btn-view" title="عرض التفاصيل" onclick="viewDetails(1)">
                                    <i class="feather icon-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>المرحلة 3: الكويلات</td>
                            <td>عطل كهربائي</td>
                            <td><span class="um-badge um-badge-danger">حرجة</span></td>
                            <td>{{ date('Y-m-d 10:30') }}</td>
                            <td>{{ date('Y-m-d 12:15') }}</td>
                            <td>1 ساعة 45 دقيقة</td>
                            <td><span class="um-badge um-badge-success">محلول</span></td>
                            <td>
                                <button class="um-btn-action um-btn-view" title="عرض التفاصيل" onclick="viewDetails(2)">
                                    <i class="feather icon-eye"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Downtime Analysis Chart -->
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-bar-chart-2"></i>
                    تحليل التوقفات حسب النوع - آخر 30 يوم
                </h4>
            </div>
            <div class="um-card-body">
                <canvas id="downtimeAnalysisChart" height="100"></canvas>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Downtime Analysis Chart
        const ctx = document.getElementById('downtimeAnalysisChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['عطل ميكانيكي', 'عطل كهربائي', 'صيانة دورية', 'نقص مواد', 'مشكلة جودة', 'أسباب سلامة'],
                datasets: [{
                    label: 'عدد الحالات',
                    data: [15, 8, 12, 6, 4, 2],
                    backgroundColor: 'rgba(255, 99, 132, 0.8)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2
                }, {
                    label: 'إجمالي الوقت (ساعات)',
                    data: [28, 12, 18, 8, 5, 3],
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
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

        function toggleOtherReason(value) {
            const otherGroup = document.getElementById('otherReasonGroup');
            if (value === 'other') {
                otherGroup.style.display = 'flex';
            } else {
                otherGroup.style.display = 'none';
            }
        }

        function resolveDowntime(id) {
            if (confirm('هل تريد تحديد هذا التوقف كمحلول؟')) {
                alert('تم تسجيل حل التوقف #' + id);
                // سيتم إضافة كود الحفظ هنا لاحقاً
            }
        }

        function viewDetails(id) {
            alert('عرض تفاصيل التوقف #' + id);
        }

        function filterDowntime(period) {
            alert('فلترة حسب الفترة: ' + period);
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
