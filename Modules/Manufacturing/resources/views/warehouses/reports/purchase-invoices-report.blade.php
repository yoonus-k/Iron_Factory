@extends('master')

@section('title', __('warehouse.purchase_invoices_report'))

@section('content')
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="header-content">
                <div class="icon-box">
                    <i class="feather icon-file"></i>
                </div>
                <div>
                    <h1 class="page-title">{{ __('warehouse.purchase_invoices_report') }}</h1>
                    <p class="page-subtitle">{{ __('warehouse.purchase_invoices_subtitle') }}</p>
                </div>
            </div>
            <div class="header-actions">
                <button onclick="window.print()" class="btn-action">
                    <i class="feather icon-printer"></i> {{ __('warehouse.print') }}
                </button>
                <a href="{{ route('manufacturing.warehouse-reports.index') }}" class="btn-action">
                    <i class="feather icon-arrow-right"></i> {{ __('warehouse.back') }}
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-card">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label>{{ __('warehouse.from_date') }}</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                </div>
                <div class="filter-group">
                    <label>{{ __('warehouse.to_date') }}</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                </div>
                <div class="filter-group">
                    <label>{{ __('warehouse.supplier') }}</label>
                    <select name="supplier_id" class="form-control">
                        <option value="">{{ __('warehouse.all') }}</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>{{ __('warehouse.payment_status') }}</label>
                    <select name="status" class="form-control">
                        <option value="">{{ __('warehouse.all') }}</option>
                        <option value="pending">{{ __('warehouse.pending') }}</option>
                        <option value="paid">{{ __('warehouse.paid') }}</option>
                        <option value="partially_paid">{{ __('warehouse.partially_paid') }}</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter">
                    <i class="feather icon-search"></i> {{ __('warehouse.search') }}
                </button>
            </form>
        </div>

        <!-- Charts Section -->
        <div class="section-card">
            <h2 class="section-title">
                <i class="feather icon-bar-chart"></i>
                {{ __('warehouse.statistics') }}
            </h2>
            <div class="chart-container">
                <canvas id="invoicesChart"></canvas>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="feather icon-file"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['total_invoices'] }}</h3>
                    <p>{{ __('warehouse.total_invoices') }}</p>
                </div>
            </div>
            <div class="stat-card purple">
                <div class="stat-icon">
                    <i class="feather icon-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_amount'], 2) }}</h3>
                    <p>{{ __('warehouse.total_amount') }} ({{ __('warehouse.sar') }})</p>
                </div>
            </div>

            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="feather icon-file-text"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['total_invoices'] }}</h3>
                    <p>{{ __('warehouse.total_invoices') }}</p>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="section-card">
            <h2 class="section-title">
                <i class="feather icon-list"></i>
                {{ __('warehouse.invoices_details') }}
            </h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('warehouse.invoice_number') }}</th>
                            <th>{{ __('warehouse.supplier') }}</th>
                            <th>{{ __('warehouse.invoice_date') }}</th>
                            <th>{{ __('warehouse.total_amount') }}</th>
                            <th>{{ __('warehouse.paid_amount') }}</th>
                            <th>{{ __('warehouse.remaining') }}</th>
                            <th>{{ __('warehouse.payment_status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $index => $invoice)
                            <tr>
                                <td>{{ $invoices->firstItem() + $index }}</td>
                                <td><span class="badge badge-info">{{ $invoice->invoice_number }}</span></td>
                                <td>{{ $invoice->supplier->supplier_name ?? '-' }}</td>
                                <td>{{ $invoice->invoice_date }}</td>
                                <td><strong>{{ number_format($invoice->total_amount, 2) }} {{ __('warehouse.sar') }}</strong></td>
                                <td>{{ number_format($invoice->paid_amount ?? 0, 2) }} {{ __('warehouse.sar') }}</td>
                                <td>{{ number_format($invoice->total_amount - ($invoice->paid_amount ?? 0), 2) }} {{ __('warehouse.sar') }}</td>
                                <td>
                                    {{-- @if($invoice->payment_status == 'paid')
                                        <span class="status-badge paid">{{ __('warehouse.paid') }}</span>
                                    @elseif($invoice->payment_status == 'pending')
                                        <span class="status-badge pending">{{ __('warehouse.pending') }}</span>
                                    @elseif($invoice->payment_status == 'partially_paid')
                                        <span class="status-badge partial">{{ __('warehouse.partially_paid') }}</span>
                                    @else
                                        <span class="status-badge">{{ $invoice->payment_status }}</span>
                                    @endif --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ __('warehouse.no_invoices') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $invoices->links() }}
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
            background: linear-gradient(135deg, #3498DB, #2874a6);
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
            color: #3498DB;
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
            background: #3498DB;
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

        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.paid {
            background: #e8f5e9;
            color: #27AE60;
        }

        .status-badge.pending {
            background: #ffebee;
            color: #e74c3c;
        }

        .status-badge.partial {
            background: #fff3e0;
            color: #F39C12;
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
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Invoices Chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('invoicesChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['{{ __('warehouse.total_invoices') }}', '{{ __('warehouse.total_amount') }}', '{{ __('warehouse.paid_amount') }}', '{{ __('warehouse.pending_amount') }}'],
                    datasets: [{
                        label: '{{ __('warehouse.statistics') }}',
                        data: [
                            {{ $stats['total_invoices'] }},
                            {{ $stats['total_amount'] }},


                        ],
                        backgroundColor: [
                            'rgba(0, 102, 178, 0.8)',
                            'rgba(156, 39, 176, 0.8)',

                        ],
                        borderColor: [
                            'rgba(0, 102, 178, 1)',
                            'rgba(156, 39, 176, 1)',

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
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    if (context.dataIndex === 0) {
                                        return context.dataset.label + ': ' + context.parsed.y.toLocaleString();
                                    } else {
                                        return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' {{ __('warehouse.sar') }}';
                                    }
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
        });
    </script>
    </style>
@endsection
