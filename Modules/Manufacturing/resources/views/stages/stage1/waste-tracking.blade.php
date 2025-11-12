@extends('master')

@section('title', 'تتبع الهدر - المرحلة الأولى')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="page-title">
                <i class="fas fa-trash-alt"></i> تتبع الهدر - المرحلة الأولى
            </h1>
            <p class="text-muted">تتبع كمية الهدر والخسائر في عملية التقسيم والاستاندات</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">إجمالي الهدر اليومي</h6>
                    <h3 class="mb-0 text-danger">245 كغ</h3>
                    <small class="text-muted">↑ 12% من الأمس</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">نسبة الهدر</h6>
                    <h3 class="mb-0 text-warning">3.5%</h3>
                    <small class="text-muted">من الوزن الكلي</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">عدد الاستاندات</h6>
                    <h3 class="mb-0 text-info">156</h3>
                    <small class="text-muted">معالجة اليوم</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">متوسط الهدر/استاند</h6>
                    <h3 class="mb-0 text-success">1.57 كغ</h3>
                    <small class="text-muted">حسب الأوزان</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line"></i> رسم بياني للهدر
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="wasteChart" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> أنواع الهدر
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>هدر الأسلاك المقطوعة</span>
                            <span class="badge badge-warning">45%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>هدر التقسيم</span>
                            <span class="badge badge-warning">30%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>هدر العيوب</span>
                            <span class="badge badge-warning">15%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>أخرى</span>
                            <span class="badge badge-warning">10%</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">
                                <i class="fas fa-table"></i> تفاصيل الهدر بالاستاندات
                            </h5>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-sm btn-light" onclick="exportToExcel()">
                                <i class="fas fa-download"></i> تصدير
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الاستاند</th>
                                    <th>الوزن الأصلي</th>
                                    <th>وزن الهدر</th>
                                    <th>نسبة الهدر</th>
                                    <th>نوع الهدر</th>
                                    <th>الملاحظات</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>#001</strong></td>
                                    <td>100 كغ</td>
                                    <td class="text-danger">3.5 كغ</td>
                                    <td><span class="badge badge-warning">3.5%</span></td>
                                    <td>أسلاك مقطوعة</td>
                                    <td>عادي</td>
                                    <td>2025-11-12</td>
                                </tr>
                                <tr>
                                    <td><strong>#002</strong></td>
                                    <td>100 كغ</td>
                                    <td class="text-danger">2.8 كغ</td>
                                    <td><span class="badge badge-warning">2.8%</span></td>
                                    <td>تقسيم</td>
                                    <td>عادي</td>
                                    <td>2025-11-12</td>
                                </tr>
                                <tr>
                                    <td><strong>#003</strong></td>
                                    <td>100 كغ</td>
                                    <td class="text-danger">4.2 كغ</td>
                                    <td><span class="badge badge-danger">4.2%</span></td>
                                    <td>عيوب</td>
                                    <td>يحتاج فحص</td>
                                    <td>2025-11-12</td>
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
    // Initialize waste chart
    const ctx = document.getElementById('wasteChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
            datasets: [{
                label: 'كمية الهدر (كغ)',
                data: [200, 215, 230, 245, 240, 250, 245],
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    function exportToExcel() {
        alert('سيتم تصدير البيانات قريباً');
    }
</script>
@endsection
