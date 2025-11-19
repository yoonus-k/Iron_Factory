@extends('master')

@section('title', 'إحصائيات المستودعات')

@section('content')
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="header-content">
                <div class="icon-box">
                    <i class="feather icon-home"></i>
                </div>
                <div>
                    <h1 class="page-title">إحصائيات المستودعات</h1>
                    <p class="page-subtitle">تقرير شامل عن جميع المستودعات والسعات التخزينية</p>
                </div>
            </div>
            <div class="header-actions">
                <button onclick="window.print()" class="btn-action">
                    <i class="feather icon-printer"></i> طباعة
                </button>
                <a href="{{ route('manufacturing.warehouse-reports.index') }}" class="btn-action">
                    <i class="feather icon-arrow-right"></i> رجوع
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="feather icon-home"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['total_warehouses'] }}</h3>
                    <p>إجمالي المستودعات</p>
                </div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon">
                    <i class="feather icon-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['active_warehouses'] }}</h3>
                    <p>مستودعات نشطة</p>
                </div>
            </div>
            <div class="stat-card gray">
                <div class="stat-icon">
                    <i class="feather icon-x-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['inactive_warehouses'] }}</h3>
                    <p>مستودعات غير نشطة</p>
                </div>
            </div>
            <div class="stat-card orange">
                <div class="stat-icon">
                    <i class="feather icon-database"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_capacity'] ?? 0, 2) }}</h3>
                    <p>السعة الإجمالية</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="section-card">
            <h2 class="section-title">
                <i class="feather icon-bar-chart"></i>
                الإحصائيات
            </h2>
            <div class="chart-container">
                <canvas id="warehousesChart"></canvas>
            </div>
        </div>

        <!-- Warehouses by Type -->
        <div class="section-card">
            <h2 class="section-title">
                <i class="feather icon-pie-chart"></i>
                توزيع المستودعات حسب النوع
            </h2>
            <div class="types-grid">
                @foreach($warehousesByType as $type => $count)
                    <div class="type-item">
                        <div class="type-label">
                            @if($type == 'raw_materials')
                                <i class="feather icon-box"></i> مواد خام
                            @elseif($type == 'additives')
                                <i class="feather icon-droplet"></i> صبغات وبلاستيك
                            @elseif($type == 'finished_goods')
                                <i class="feather icon-package"></i> منتجات نهائية
                            @else
                                <i class="feather icon-archive"></i> عام
                            @endif
                        </div>
                        <div class="type-count">{{ $count }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Warehouses Table -->
        <div class="section-card">
            <h2 class="section-title">
                <i class="feather icon-list"></i>
                تفاصيل المستودعات
            </h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رمز المستودع</th>
                            <th>اسم المستودع</th>
                            <th>النوع</th>
                            <th>الموقع</th>
                            <th>السعة</th>
                            <th>عدد المواد</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouses as $index => $warehouse)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge badge-info">{{ $warehouse->warehouse_code }}</span></td>
                                <td><strong>{{ $warehouse->warehouse_name }}</strong></td>
                                <td>
                                    @if($warehouse->warehouse_type == 'raw_materials')
                                        <span class="type-badge raw">مواد خام</span>
                                    @elseif($warehouse->warehouse_type == 'additives')
                                        <span class="type-badge additives">صبغات وبلاستيك</span>
                                    @elseif($warehouse->warehouse_type == 'finished_goods')
                                        <span class="type-badge finished">منتجات نهائية</span>
                                    @else
                                        <span class="type-badge general">عام</span>
                                    @endif
                                </td>
                                <td>{{ $warehouse->location ?? '-' }}</td>
                                <td>{{ $warehouse->capacity ?? '-' }} {{ $warehouse->capacity_unit ?? '' }}</td>
                                <td><span class="count-badge">{{ $warehouse->materials_count ?? 0 }}</span></td>
                                <td>
                                    @if($warehouse->is_active)
                                        <span class="status-badge active">نشط</span>
                                    @else
                                        <span class="status-badge inactive">غير نشط</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">لا توجد مستودعات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .report-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .report-header {
            background: linear-gradient(135deg, #0066B2 0%, #004d8a 100%);
            border-radius: 16px;
            padding: 30px 40px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .icon-box {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 5px 0;
        }

        .page-subtitle {
            font-size: 14px;
            margin: 0;
            opacity: 0.9;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-action:hover {
            background: white;
            color: #0066B2;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
        }

        .stat-card.blue .stat-icon { background: linear-gradient(135deg, #0066B2, #004d8a); }
        .stat-card.gray .stat-icon { background: linear-gradient(135deg, #455A64, #37474F); }
        .stat-card.green .stat-icon { background: linear-gradient(135deg, #27AE60, #1e8449); }
        .stat-card.orange .stat-icon { background: linear-gradient(135deg, #F39C12, #d68910); }

        .stat-info h3 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 5px 0;
            color: #2c3e50;
        }

        .stat-info p {
            font-size: 14px;
            color: #7f8c8d;
            margin: 0;
        }

        .section-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        /* Chart Styles */
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 20px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .type-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }

        .type-label {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .type-count {
            font-size: 28px;
            font-weight: 700;
            color: #0066B2;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: #f8f9fa;
        }

        .data-table th {
            padding: 15px;
            text-align: right;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e0e0e0;
        }

        .data-table td {
            padding: 15px;
            text-align: right;
            border-bottom: 1px solid #f0f0f0;
        }

        .data-table tbody tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-info {
            background: #e3f2fd;
            color: #0066B2;
        }

        .type-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .type-badge.raw { background: #e8f5e9; color: #27AE60; }
        .type-badge.additives { background: #f3e5f5; color: #9C27B0; }
        .type-badge.finished { background: #e3f2fd; color: #0066B2; }
        .type-badge.general { background: #f5f5f5; color: #757575; }

        .count-badge {
            background: #f8f9fa;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            color: #0066B2;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.active {
            background: #e8f5e9;
            color: #27AE60;
        }

        .status-badge.inactive {
            background: #ffebee;
            color: #e74c3c;
        }

        @media (max-width: 768px) {
            .report-header {
                flex-direction: column;
                text-align: center;
            }

            .header-content {
                flex-direction: column;
            }

            .stats-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Warehouses Chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('warehousesChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['إجمالي المستودعات', 'مستودعات نشطة', 'مستودعات غير نشطة', 'السعة الإجمالية'],
                    datasets: [{
                        label: 'الإحصائيات',
                        data: [
                            {{ $stats['total_warehouses'] }},
                            {{ $stats['active_warehouses'] }},
                            {{ $stats['inactive_warehouses'] }},
                            {{ number_format($stats['total_capacity'] ?? 0, 2) }}
                        ],
                        backgroundColor: [
                            'rgba(0, 102, 178, 0.8)',
                            'rgba(39, 174, 96, 0.8)',
                            'rgba(243, 156, 18, 0.8)',
                            'rgba(52, 152, 219, 0.8)'
                        ],
                        borderColor: [
                            'rgba(0, 102, 178, 1)',
                            'rgba(39, 174, 96, 1)',
                            'rgba(243, 156, 18, 1)',
                            'rgba(52, 152, 219, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            rtl: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>

        @media print {
            .header-actions {
                display: none;
            }
        }
    </style>
@endsection
