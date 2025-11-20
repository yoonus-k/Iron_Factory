@extends('master')

@section('title', 'تقرير الحركات والتحويلات')

@section('content')
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="header-content">
                <div class="icon-box">
                    <i class="feather icon-repeat"></i>
                </div>
                <div>
                    <h1 class="page-title">تقرير الحركات والتحويلات</h1>
                    <p class="page-subtitle">سجل حركات المواد بين المستودعات</p>
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

        <!-- Filter Section -->
        <div class="filter-card">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label>من تاريخ</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                </div>
                <div class="filter-group">
                    <label>إلى تاريخ</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                </div>
                <div class="filter-group">
                    <label>المستودع</label>
                    <select name="warehouse_id" class="form-control">
                        <option value="">الكل</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->warehouse_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>نوع الحركة</label>
                    <select name="transaction_type" class="form-control">
                        <option value="">الكل</option>
                        <option value="incoming" {{ request('transaction_type') == 'incoming' ? 'selected' : '' }}>دخول بضاعة</option>
                        <option value="outgoing" {{ request('transaction_type') == 'outgoing' ? 'selected' : '' }}>خروج بضاعة</option>
                        <option value="transfer" {{ request('transaction_type') == 'transfer' ? 'selected' : '' }}>نقل بين مستودعات</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter">
                    <i class="feather icon-search"></i> بحث
                </button>
            </form>
        </div>

        <!-- Charts Section -->
        <div class="section-card">
            <h2 class="section-title">
                <i class="feather icon-bar-chart"></i>
                الإحصائيات
            </h2>
            <div class="chart-container">
                <canvas id="movementsChart"></canvas>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="feather icon-repeat"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['total_movements'] }}</h3>
                    <p>إجمالي الحركات</p>
                </div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon">
                    <i class="feather icon-arrow-down"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['receive_count'] }}</h3>
                    <p>دخول بضاعة</p>
                </div>
            </div>
            <div class="stat-card orange">
                <div class="stat-icon">
                    <i class="feather icon-arrow-up"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['issue_count'] }}</h3>
                    <p>خروج بضاعة</p>
                </div>
            </div>
            <div class="stat-card purple">
                <div class="stat-icon">
                    <i class="feather icon-shuffle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['transfer_count'] }}</h3>
                    <p>نقل بين مستودعات</p>
                </div>
            </div>
            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="feather icon-package"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_quantity'], 2) }}</h3>
                    <p>إجمالي الكميات</p>
                </div>
            </div>
        </div>

        <!-- Movements Table -->
        <div class="section-card">
            <h2 class="section-title">
                <i class="feather icon-list"></i>
                تفاصيل الحركات
            </h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم المادة</th>
                            <th>المستودع</th>
                            <th>النوع</th>
                            <th>الكمية</th>
                            <th>الوحدة</th>
                            <th>نوع الحركة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $index => $movement)
                            <tr>
                                <td>{{ $movements->firstItem() + $index }}</td>
                                <td><strong>{{ $movement->material->name_ar ?? $movement->material->name_en ?? 'غير محدد' }}</strong></td>
                                <td>
                                    @if($movement->fromWarehouse)
                                        {{ $movement->fromWarehouse->warehouse_name }}
                                    @elseif($movement->supplier)
                                        {{ $movement->supplier->name ?? 'غير محدد' }}
                                    @else
                                        غير محدد
                                    @endif
                                </td>
                                <td>{{ $movement->material->materialType->type_name ?? 'غير محدد' }}</td>
                                <td>
                                    <span class="quantity-badge">
                                        {{ number_format($movement->quantity, 2) }}
                                    </span>
                                </td>
                                <td>{{ $movement->unit->unit_name ?? 'وحدة' }}</td>
                                <td>
                                    @if($movement->movement_type == 'incoming')
                                        <span class="type-badge receive">
                                            <i class="feather icon-arrow-down"></i> استلام
                                        </span>
                                    @elseif($movement->movement_type == 'outgoing')
                                        <span class="type-badge issue">
                                            <i class="feather icon-arrow-up"></i> صرف
                                        </span>
                                    @elseif($movement->movement_type == 'transfer')
                                        <span class="type-badge transfer">
                                            <i class="feather icon-shuffle"></i> تحويل
                                        </span>
                                    @else
                                        <span class="type-badge">{{ $movement->movement_type }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($movement->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">لا توجد حركات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $movements->links() }}
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
            background: linear-gradient(135deg, #0066B2, #004d8a);
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

        .filter-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .filter-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 180px;
        }

        .filter-group label {
            display: block;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }

        .btn-filter {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            background: #0066B2;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
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
        .stat-card.green .stat-icon { background: linear-gradient(135deg, #27AE60, #1e8449); }
        .stat-card.orange .stat-icon { background: linear-gradient(135deg, #F39C12, #d68910); }
        .stat-card.purple .stat-icon { background: linear-gradient(135deg, #9C27B0, #7b1fa2); }

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
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
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
            white-space: nowrap;
        }

        .data-table td {
            padding: 15px;
            text-align: right;
            border-bottom: 1px solid #f0f0f0;
        }

        .data-table tbody tr:hover {
            background: #f8f9fa;
        }

        .quantity-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            background: #e8f5e9;
            color: #27AE60;
        }

        .type-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .type-badge.receive {
            background: #e8f5e9;
            color: #27AE60;
        }

        .type-badge.issue {
            background: #fff3e0;
            color: #F39C12;
        }

        .type-badge.transfer {
            background: #f3e5f5;
            color: #9C27B0;
        }

        .type-badge i {
            font-size: 14px;
        }

        .pagination-wrapper {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        /* Chart Styles */
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .report-header {
                flex-direction: column;
                text-align: center;
            }

            .header-content {
                flex-direction: column;
            }

            .filter-form {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Movements Chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('movementsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['إجمالي الحركات', 'دخول بضاعة', 'خروج بضاعة', 'نقل بين مستودعات'],
                    datasets: [{
                        label: 'الإحصائيات',
                        data: [
                            {{ $stats['total_movements'] }},
                            {{ $stats['receive_count'] }},
                            {{ $stats['issue_count'] }},
                            {{ $stats['transfer_count'] }}
                        ],
                        backgroundColor: [
                            'rgba(0, 102, 178, 0.8)',
                            'rgba(39, 174, 96, 0.8)',
                            'rgba(243, 156, 18, 0.8)',
                            'rgba(156, 39, 176, 0.8)'
                        ],
                        borderColor: [
                            'rgba(0, 102, 178, 1)',
                            'rgba(39, 174, 96, 1)',
                            'rgba(243, 156, 18, 1)',
                            'rgba(156, 39, 176, 1)'
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
@endsection