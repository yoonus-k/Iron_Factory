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
                    <p class="page-subtitle">متابعة أذون التسليم والشحنات الواردة والصادرة</p>
                </div>
            </div>
            <div class="header-actions">
                <button onclick="window.print()" class="btn-action">
                    <i class="feather icon-printer"></i> طباعة
                </button>
                <button onclick="exportToPDF()" class="btn-action">
                    <i class="feather icon-download"></i> تحميل PDF
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
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
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
                    <label>الحالة</label>
                    <select name="status" class="form-control">
                        <option value="">الكل</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter">
                    <i class="feather icon-search"></i> بحث
                </button>
                @if(request('start_date') || request('end_date') || request('supplier_id') || request('warehouse_id') || request('status'))
                    <a href="{{ route('manufacturing.warehouse-reports.delivery-notes') }}" class="btn-filter btn-reset">
                        <i class="feather icon-x"></i> إعادة تعيين
                    </a>
                @endif
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="feather icon-file-text"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['total_notes'] }}</h3>
                    <p>إجمالي الأذون</p>
                    <span class="stat-detail">في الفترة المحددة</span>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="stat-icon">
                    <i class="feather icon-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['pending_notes'] }}</h3>
                    <p>قيد الانتظار</p>
                    <span class="stat-detail">{{ round(($stats['pending_notes'] / max($stats['total_notes'], 1)) * 100, 1) }}%</span>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-icon">
                    <i class="feather icon-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['approved_notes'] }}</h3>
                    <p>موافق عليه</p>
                    <span class="stat-detail">{{ round(($stats['approved_notes'] / max($stats['total_notes'], 1)) * 100, 1) }}%</span>
                </div>
            </div>

            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="feather icon-package"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['completed_notes'] }}</h3>
                    <p>مكتمل</p>
                    <span class="stat-detail">{{ round(($stats['completed_notes'] / max($stats['total_notes'], 1)) * 100, 1) }}%</span>
                </div>
            </div>

            <div class="stat-card red">
                <div class="stat-icon">
                    <i class="feather icon-x-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['rejected_notes'] }}</h3>
                    <p>مرفوض</p>
                    <span class="stat-detail">{{ round(($stats['rejected_notes'] / max($stats['total_notes'], 1)) * 100, 1) }}%</span>
                </div>
            </div>

            <div class="stat-card gray">
                <div class="stat-icon">
                    <i class="feather icon-bar-chart-2"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_quantity'], 2) }}</h3>
                    <p>إجمالي الكمية</p>
                    <span class="stat-detail">متوسط: {{ $stats['avg_quantity'] }}</span>
                </div>
            </div>
        </div>

        <!-- Secondary Statistics -->
        <div class="secondary-stats">
            <div class="info-card">
                <div class="info-icon" style="background: #e3f2fd; color: #0066B2;">
                    <i class="feather icon-arrow-down"></i>
                </div>
                <div class="info-content">
                    <h4>الحركات الواردة</h4>
                    <p class="big-number">{{ $typeStats['incoming'] ?? 0 }}</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon" style="background: #f3e5f5; color: #9c27b0;">
                    <i class="feather icon-arrow-up"></i>
                </div>
                <div class="info-content">
                    <h4>الحركات الصادرة</h4>
                    <p class="big-number">{{ $typeStats['outgoing'] ?? 0 }}</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon" style="background: #fff3e0; color: #F39C12;">
                    <i class="feather icon-weight"></i>
                </div>
                <div class="info-content">
                    <h4>إجمالي الوزن</h4>
                    <p class="big-number">{{ number_format($stats['total_weight'] ?? 0, 2) }} كجم</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon" style="background: #e8f5e9; color: #27AE60;">
                    <i class="feather icon-trending-up"></i>
                </div>
                <div class="info-content">
                    <h4>متوسط الوزن</h4>
                    <p class="big-number">{{ number_format($stats['avg_weight'] ?? 0, 2) }} كجم</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-grid">
            <!-- Type Distribution Chart -->
            <div class="chart-card">
                <h3 class="chart-title">توزيع نوع الحركات</h3>
                <canvas id="typeChart"></canvas>
                <div class="chart-legend">
                    <div class="legend-item">
                        <span class="legend-color" style="background: #0066B2;"></span>
                        <span>واردة: {{ $typeStats['incoming'] ?? 0 }}</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #9c27b0;"></span>
                        <span>صادرة: {{ $typeStats['outgoing'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Daily Trends Chart -->
            <div class="chart-card" style="grid-column: 1 / -1;">
                <h2 class="chart-title">
                    <i class="feather icon-trending-up"></i>
                    الاتجاهات الشهرية
                </h2>
                <canvas id="trendsChart" height="80"></canvas>
            </div>
        </div>

        <!-- Top Suppliers -->
        @if($topSuppliers->count() > 0)
            <div class="section-card">
                <h2 class="section-title">
                    <i class="feather icon-users"></i>
                    أفضل 10 موردين
                </h2>
                <div class="top-suppliers-grid">
                    @foreach($topSuppliers as $index => $supplier)
                        <div class="supplier-card">
                            <div class="supplier-rank">{{ $index + 1 }}</div>
                            <div class="supplier-content">
                                <h4>{{ $supplier->supplier->name ?? 'غير محدد' }}</h4>
                                <div class="supplier-stats">
                                    <span class="badge-supplier">{{ $supplier->count }} أذن</span>
                                    <span class="badge-supplier qty">{{ number_format($supplier->total_quantity, 2) }} وحدة</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Warehouse Statistics -->
        @if($warehouseStats->count() > 0)
            <div class="section-card">
                <h2 class="section-title">
                    <i class="feather icon-archive"></i>
                    إحصائيات المستودعات
                </h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم المستودع</th>
                                <th>عدد الأذون</th>
                                <th>إجمالي الكمية</th>
                                <th>النسبة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($warehouseStats as $index => $warehouse)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $warehouse->warehouse->warehouse_name ?? 'غير محدد' }}</strong></td>
                                    <td>
                                        <span class="badge badge-info">{{ $warehouse->count }}</span>
                                    </td>
                                    <td>{{ number_format($warehouse->total_quantity, 2) }}</td>
                                    <td>
                                        <div class="progress-bar">
                                            <div class="progress" style="width: {{ ($warehouse->count / max($warehouseStats->sum('count'), 1)) * 100 }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Units/Materials Distribution Table -->
        @if(isset($materialsDistribution) && $materialsDistribution->count() > 0)
            <div class="section-card">
                <h2 class="section-title">
                    <i class="feather icon-package"></i>
                    توزيع الوحدات والمواد
                </h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم المادة</th>
                                <th>عدد الأذون</th>
                                <th>إجمالي الكمية</th>
                                <th>متوسط الكمية</th>
                                <th>إجمالي الوزن</th>
                                <th>النسبة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materialsDistribution as $index => $material)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>
                                            @if($material->material && $material->material->name_ar)
                                                {{ $material->material->name_ar }}
                                            @else
                                                غير محدد
                                            @endif
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $material->count }}</span>
                                    </td>
                                    <td>{{ number_format($material->total_quantity, 2) }}</td>
                                    <td>{{ number_format($material->avg_quantity, 2) }}</td>
                                    <td>{{ number_format($material->total_weight ?? 0, 2) }} كجم</td>
                                    <td>
                                        <div class="progress-bar">
                                            <div class="progress" style="width: {{ ($material->count / max($materialsDistribution->sum('count'), 1)) * 100 }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

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
                            <th>النوع</th>
                            <th>تاريخ التسليم</th>
                            <th>الكمية</th>
                            <th>الوزن (كجم)</th>
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
                                <td>
                                    @if($note->type == 'incoming')
                                        <span class="badge badge-primary">واردة</span>
                                    @else
                                        <span class="badge badge-warning">صادرة</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($note->delivery_date)->format('Y-m-d') }}</td>
                                <td><strong>{{ number_format($note->delivery_quantity, 2) }}</strong></td>
                                <td>{{ number_format($note->actual_weight ?? 0, 2) }}</td>
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
                                <td colspan="10" class="text-center text-muted">لا توجد أذون تسليم</td>
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
            direction: rtl;
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
            min-width: 150px;
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
            transition: all 0.3s;
        }

        .btn-filter:hover {
            background: #004d8a;
        }

        .btn-reset {
            background: #e74c3c;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-reset:hover {
            background: #c0392b;
            text-decoration: none;
            color: white;
        }

        /* Statistics Grid */
        .stats-grid {
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
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.12);
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
            flex-shrink: 0;
        }

        .stat-card.blue .stat-icon { background: linear-gradient(135deg, #0066B2, #004d8a); }
        .stat-card.gray .stat-icon { background: linear-gradient(135deg, #455A64, #37474F); }
        .stat-card.green .stat-icon { background: linear-gradient(135deg, #27AE60, #1e8449); }
        .stat-card.orange .stat-icon { background: linear-gradient(135deg, #F39C12, #d68910); }
        .stat-card.red .stat-icon { background: linear-gradient(135deg, #e74c3c, #c0392b); }

        .stat-info h3 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 5px 0;
            color: #2c3e50;
        }

        .stat-info p {
            font-size: 14px;
            color: #7f8c8d;
            margin: 0 0 5px 0;
        }

        .stat-detail {
            font-size: 12px;
            color: #95a5a6;
        }

        /* Secondary Stats */
        .secondary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .info-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .info-content h4 {
            font-size: 12px;
            color: #7f8c8d;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            font-weight: 600;
        }

        .big-number {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        /* Charts Section */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .chart-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 20px 0;
            text-align: right;
        }

        .chart-legend {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #555;
        }

        .legend-color {
            width: 14px;
            height: 14px;
            border-radius: 3px;
            flex-shrink: 0;
        }

        /* Section Card */
        .section-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
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

        /* Table Styles */
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
            font-size: 14px;
        }

        .data-table td {
            padding: 15px;
            text-align: right;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }

        .data-table tbody tr:hover {
            background: #f8f9fa;
        }

        /* Badge Styles */
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-info {
            background: #e3f2fd;
            color: #0066B2;
        }

        .badge-primary {
            background: #e3f2fd;
            color: #0066B2;
        }

        .badge-warning {
            background: #fff3e0;
            color: #F39C12;
        }

        .badge-supplier {
            display: inline-block;
            background: #e3f2fd;
            color: #0066B2;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin: 3px;
        }

        .badge-supplier.qty {
            background: #e8f5e9;
            color: #27AE60;
        }

        /* Status Badge */
        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
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

        /* Top Suppliers Grid */
        .top-suppliers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }

        .supplier-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #f0f2f5 100%);
            border-radius: 10px;
            padding: 15px;
            position: relative;
            border-left: 4px solid #0066B2;
        }

        .supplier-rank {
            position: absolute;
            top: -10px;
            right: 10px;
            background: #F39C12;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .supplier-content {
            margin-top: 10px;
        }

        .supplier-content h4 {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 8px 0;
        }

        .supplier-stats {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        /* Progress Bar */
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #ecf0f1;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress {
            height: 100%;
            background: linear-gradient(90deg, #0066B2, #004d8a);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .pagination-wrapper {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #7f8c8d;
        }

        @media (max-width: 768px) {
            .report-header {
                flex-direction: column;
                text-align: center;
            }

            .header-content {
                flex-direction: column;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .top-suppliers-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .data-table {
                font-size: 12px;
            }

            .data-table th,
            .data-table td {
                padding: 10px 5px;
            }
        }

        @media print {
            .header-actions,
            .filter-card {
                display: none;
            }

            .report-container {
                margin: 0;
                padding: 0;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        // Type Distribution Chart
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: ['واردة', 'صادرة'],
                datasets: [{
                    data: [
                        {{ $typeStats['incoming'] ?? 0 }},
                        {{ $typeStats['outgoing'] ?? 0 }}
                    ],
                    backgroundColor: ['#0066B2', '#9c27b0'],
                    borderColor: ['#fff', '#fff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Daily Trends Chart
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($monthlyTrends as $trend)
                        '{{ \Carbon\Carbon::parse($trend->month . '-01')->format("M Y") }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'عدد الأذون',
                    data: [
                        @foreach($monthlyTrends as $trend)
                            {{ $trend->count }},
                        @endforeach
                    ],
                    borderColor: '#0066B2',
                    backgroundColor: 'rgba(0, 102, 178, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#0066B2',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }, {
                    label: 'إجمالي الكمية',
                    data: [
                        @foreach($monthlyTrends as $trend)
                            {{ $trend->quantity }},
                        @endforeach
                    ],
                    borderColor: '#27AE60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#27AE60',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        align: 'top'
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'عدد الأذون'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'الكمية'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                }
            }
        });

        function exportToPDF() {
            const element = document.querySelector('.report-container');
            const opt = {
                margin: 10,
                filename: 'تقرير-أذون-التسليم.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
            };
            html2pdf().set(opt).save(element);
        }
    </script>
@endsection
