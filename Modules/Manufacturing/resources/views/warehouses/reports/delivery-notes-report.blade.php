@extends('master')

@section('title', 'تقرير أذون التسليم')

@section('content')
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="header-content">
                <div class="icon-box">
                    <i class="feather icon-file-text"></i>
                </div>
                <div>
                    <h1 class="page-title">تقرير أذون التسليم</h1>
                    <p class="page-subtitle">متابعة أذون التسليم والشحنات الواردة</p>
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
                    <label>المورد</label>
                    <select name="supplier_id" class="form-control">
                        <option value="">الكل</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>الحالة</label>
                    <select name="status" class="form-control">
                        <option value="">الكل</option>
                        <option value="pending">قيد الانتظار</option>
                        <option value="approved">موافق عليه</option>
                        <option value="completed">مكتمل</option>
                        <option value="rejected">مرفوض</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter">
                    <i class="feather icon-search"></i> بحث
                </button>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="feather icon-file-text"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['total_notes'] }}</h3>
                    <p>إجمالي الأذون</p>
                </div>
            </div>
            <div class="stat-card orange">
                <div class="stat-icon">
                    <i class="feather icon-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['pending_notes'] }}</h3>
                    <p>قيد الانتظار</p>
                </div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon">
                    <i class="feather icon-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['received_notes'] }}</h3>
                    <p>موافق عليه</p>
                </div>
            </div>
            <div class="stat-card gray">
                <div class="stat-icon">
                    <i class="feather icon-package"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_quantity'], 2) }}</h3>
                    <p>إجمالي الكمية</p>
                </div>
            </div>
        </div>

        <!-- Delivery Notes Table -->
        <div class="section-card">
            <h2 class="section-title">
                <i class="feather icon-list"></i>
                تفاصيل أذون التسليم
            </h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رقم الإذن</th>
                            <th>المورد</th>
                            <th>المستودع</th>
                            <th>تاريخ التسليم</th>
                            <th>الكمية</th>
                            <th>المسجل بواسطة</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveryNotes as $index => $note)
                            <tr>
                                <td>{{ $deliveryNotes->firstItem() + $index }}</td>
                                <td><span class="badge badge-info">{{ $note->note_number }}</span></td>
                                <td>{{ $note->supplier->name ?? '-' }}</td>
                                <td>{{ $note->warehouse->warehouse_name ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($note->delivery_date)->format('Y-m-d') }}</td>
                                <td><strong>{{ number_format($note->delivery_quantity, 2) }}</strong></td>
                                <td>{{ $note->recordedBy->name ?? '-' }}</td>
                                <td>
                                    @if($note->status == 'pending')
                                        <span class="status-badge pending">قيد الانتظار</span>
                                    @elseif($note->status == 'approved')
                                        <span class="status-badge approved">موافق عليه</span>
                                    @elseif($note->status == 'completed')
                                        <span class="status-badge completed">مكتمل</span>
                                    @elseif($note->status == 'rejected')
                                        <span class="status-badge rejected">مرفوض</span>
                                    @else
                                        <span class="status-badge">{{ $note->status->label() ?? $note->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">لا توجد أذون تسليم</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $deliveryNotes->links() }}
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

        .status-badge.pending {
            background: #fff3e0;
            color: #F39C12;
        }

        .status-badge.approved {
            background: #e8f5e9;
            color: #27AE60;
        }

        .status-badge.completed {
            background: #e3f2fd;
            color: #0066B2;
        }

        .status-badge.rejected {
            background: #ffebee;
            color: #e74c3c;
        }

        .pagination-wrapper {
            margin-top: 20px;
            display: flex;
            justify-content: center;
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
@endsection
