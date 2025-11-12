@extends('master')

@section('title', 'إحصائيات الهدر - المرحلة الثانية')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="page-title">
                <i class="fas fa-chart-pie"></i> إحصائيات الهدر - المرحلة الثانية
            </h1>
            <p class="text-muted">تحليل تفصيلي لنسب الهدر والخسائر في عملية المعالجة</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card border-left-danger">
                <div class="card-body">
                    <h6 class="text-muted mb-2">إجمالي الهدر</h6>
                    <h3 class="mb-0 text-danger">1,245 كغ</h3>
                    <small class="text-muted">من جميع المعالجات</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-left-warning">
                <div class="card-body">
                    <h6 class="text-muted mb-2">متوسط نسبة الهدر</h6>
                    <h3 class="mb-0 text-warning">4.2%</h3>
                    <small class="text-muted">من الوزن الكلي</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-left-info">
                <div class="card-body">
                    <h6 class="text-muted mb-2">عدد المعالجات</h6>
                    <h3 class="mb-0 text-info">324</h3>
                    <small class="text-muted">معالجة مكتملة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-left-success">
                <div class="card-body">
                    <h6 class="text-muted mb-2">الكفاءة</h6>
                    <h3 class="mb-0 text-success">95.8%</h3>
                    <small class="text-muted">معدل النجاح</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-pie-chart"></i> توزيع نسب الهدر
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="wasteDistributionChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-line-chart"></i> اتجاهات الهدر اليومية
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="wasteTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> أسباب الهدر الرئيسية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">عيوب المواد الخام</h6>
                                    <small class="text-muted">مشاكل في جودة المدخلات</small>
                                </div>
                                <span class="badge badge-danger badge-lg">35%</span>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">خطأ في المعالجة</h6>
                                    <small class="text-muted">عملية معالجة غير دقيقة</small>
                                </div>
                                <span class="badge badge-warning badge-lg">25%</span>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">تأثير البيئة</h6>
                                    <small class="text-muted">الحرارة والرطوبة والضغط</small>
                                </div>
                                <span class="badge badge-info badge-lg">20%</span>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">أخرى</h6>
                                    <small class="text-muted">عوامل أخرى مختلفة</small>
                                </div>
                                <span class="badge badge-secondary badge-lg">20%</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-star"></i> أفضل أداء اليوم
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>العامل/الورديه</th>
                                <th>عدد المعالجات</th>
                                <th>نسبة الهدر</th>
                                <th>التقييم</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>الورديه الصباحية</td>
                                <td>85</td>
                                <td>3.1%</td>
                                <td><span class="badge badge-success">ممتاز</span></td>
                            </tr>
                            <tr>
                                <td>الورديه المسائية</td>
                                <td>78</td>
                                <td>4.5%</td>
                                <td><span class="badge badge-info">جيد</span></td>
                            </tr>
                            <tr>
                                <td>الورديه الليلية</td>
                                <td>82</td>
                                <td>4.8%</td>
                                <td><span class="badge badge-info">جيد</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">
                                <i class="fas fa-table"></i> تقرير تفصيلي
                            </h5>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-sm btn-light" onclick="exportReport()">
                                <i class="fas fa-download"></i> تصدير PDF
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>عدد المعالجات</th>
                                    <th>الوزن الكلي</th>
                                    <th>الهدر الكلي</th>
                                    <th>نسبة الهدر</th>
                                    <th>الملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2025-11-12</td>
                                    <td>245</td>
                                    <td>29,500 كغ</td>
                                    <td>1,240 كغ</td>
                                    <td><span class="badge badge-warning">4.2%</span></td>
                                    <td>عادي</td>
                                </tr>
                                <tr>
                                    <td>2025-11-11</td>
                                    <td>238</td>
                                    <td>28,800 كغ</td>
                                    <td>1,105 كغ</td>
                                    <td><span class="badge badge-warning">3.8%</span></td>
                                    <td>عادي</td>
                                </tr>
                                <tr>
                                    <td>2025-11-10</td>
                                    <td>256</td>
                                    <td>31,200 كغ</td>
                                    <td>1,512 كغ</td>
                                    <td><span class="badge badge-danger">4.8%</span></td>
                                    <td>نسبة عالية</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Waste Distribution Chart
    const distCtx = document.getElementById('wasteDistributionChart').getContext('2d');
    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: ['عيوب المواد', 'خطأ في المعالجة', 'تأثير البيئة', 'أخرى'],
            datasets: [{
                data: [35, 25, 20, 20],
                backgroundColor: ['#dc3545', '#ffc107', '#17a2b8', '#6c757d'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Waste Trend Chart
    const trendCtx = document.getElementById('wasteTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ['اليوم -7', 'اليوم -6', 'اليوم -5', 'اليوم -4', 'اليوم -3', 'اليوم -2', 'اليوم'],
            datasets: [{
                label: 'نسبة الهدر %',
                data: [3.8, 4.1, 4.5, 4.2, 3.9, 4.0, 4.2],
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.3,
                fill: true,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 10
                }
            }
        }
    });

    function exportReport() {
        alert('سيتم تصدير التقرير كملف PDF قريباً');
    }
</script>
@endsection
