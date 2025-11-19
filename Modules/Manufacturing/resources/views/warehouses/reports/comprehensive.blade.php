@extends('master')

@section('title', 'التقرير الشامل للمستودع')

@section('content')
    <div class="comprehensive-report-wrapper">
        <!-- Header Section -->
        <div class="report-header">
            <div class="header-content">
                <div class="icon-box">
                    <i class="feather icon-file-text"></i>
                </div>
                <div class="header-text">
                    <h1 class="page-title">التقرير الشامل للمستودع</h1>
                    <p class="page-subtitle">نظرة عامة متكاملة على جميع عمليات وأنشطة المستودعات</p>
                </div>
            </div>
            <div class="header-actions">
                <button onclick="window.print()" class="action-button print">
                    <i class="feather icon-printer"></i>
                    <span>طباعة</span>
                </button>
                <button onclick="exportReport()" class="action-button export">
                    <i class="feather icon-download"></i>
                    <span>تصدير</span>
                </button>
                <a href="{{ route('manufacturing.warehouse-reports.index') }}" class="action-button back">
                    <i class="feather icon-arrow-right"></i>
                    <span>رجوع</span>
                </a>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="filter-section">
            <form method="GET" action="{{ route('manufacturing.warehouse-reports.comprehensive') }}" class="filter-form">
                <div class="filter-group">
                    <label>
                        <i class="feather icon-calendar"></i>
                        من تاريخ
                    </label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                </div>
                <div class="filter-group">
                    <label>
                        <i class="feather icon-calendar"></i>
                        إلى تاريخ
                    </label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                </div>
                <button type="submit" class="filter-button">
                    <i class="feather icon-search"></i>
                    <span>عرض</span>
                </button>
            </form>
        </div>

        <!-- Statistics Overview Grid -->
        <div class="stats-grid">
            <!-- Warehouses Section -->
            <div class="stat-section warehouses">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="feather icon-home"></i>
                    </div>
                    <h2 class="section-title">المستودعات</h2>
                </div>
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-label">إجمالي المستودعات</span>
                        <span class="stat-value">{{ $data['warehouses']['total'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">المستودعات النشطة</span>
                        <span class="stat-value success">{{ $data['warehouses']['active'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">السعة الإجمالية</span>
                        <span class="stat-value info">{{ number_format($data['warehouses']['total_capacity'], 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Materials Section -->
            <div class="stat-section materials">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="feather icon-package"></i>
                    </div>
                    <h2 class="section-title">المواد الخام</h2>
                </div>
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-label">إجمالي المواد</span>
                        <span class="stat-value">{{ $data['materials']['total'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">إجمالي الكميات</span>
                        <span class="stat-value success">{{ number_format($data['materials']['total_quantity'], 2) }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">مخزون منخفض</span>
                        <span class="stat-value warning">{{ $data['materials']['low_stock'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">الوزن الكلي</span>
                        <span class="stat-value info">{{ number_format($data['materials']['total_weight'], 2) }} كجم</span>
                    </div>
                </div>
            </div>

            <!-- Delivery Notes Section -->
            <div class="stat-section delivery-notes">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="feather icon-file-text"></i>
                    </div>
                    <h2 class="section-title">أذون التسليم</h2>
                </div>
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-label">إجمالي الأذون</span>
                        <span class="stat-value">{{ $data['delivery_notes']['total'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">قيد الانتظار</span>
                        <span class="stat-value warning">{{ $data['delivery_notes']['pending'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">موافق عليه</span>
                        <span class="stat-value success">{{ $data['delivery_notes']['approved'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">مكتمل</span>
                        <span class="stat-value info">{{ $data['delivery_notes']['completed'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Invoices Section -->
            <div class="stat-section invoices">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="feather icon-file"></i>
                    </div>
                    <h2 class="section-title">فواتير المشتريات</h2>
                </div>
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-label">عدد الفواتير</span>
                        <span class="stat-value">{{ $data['invoices']['total'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">مدفوعة</span>
                        <span class="stat-value success">{{ $data['invoices']['paid'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">قيد الانتظار</span>
                        <span class="stat-value warning">{{ $data['invoices']['pending'] }}</span>
                    </div>
                    <div class="stat-item wide">
                        <span class="stat-label">إجمالي المبلغ</span>
                        <span class="stat-value primary">{{ number_format($data['invoices']['total_amount'], 2) }} ريال</span>
                    </div>
                </div>
            </div>

            <!-- Movements Section -->
            <div class="stat-section movements">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="feather icon-repeat"></i>
                    </div>
                    <h2 class="section-title">الحركات والتحويلات</h2>
                </div>
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-label">إجمالي الحركات</span>
                        <span class="stat-value">{{ $data['movements']['total'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">استلام</span>
                        <span class="stat-value success">{{ $data['movements']['receive'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">صرف</span>
                        <span class="stat-value warning">{{ $data['movements']['issue'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">تحويل</span>
                        <span class="stat-value info">{{ $data['movements']['transfer'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Additives Section -->
            <div class="stat-section additives">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="feather icon-droplet"></i>
                    </div>
                    <h2 class="section-title">الصبغات والبلاستيك</h2>
                </div>
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-label">إجمالي المواد</span>
                        <span class="stat-value">{{ $data['additives']['total'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">المواد النشطة</span>
                        <span class="stat-value success">{{ $data['additives']['active'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">إجمالي الكمية</span>
                        <span class="stat-value info">{{ number_format($data['additives']['total_quantity'], 2) }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">القيمة الإجمالية</span>
                        <span class="stat-value primary">{{ number_format($data['additives']['total_value'], 2) }} ريال</span>
                    </div>
                </div>
            </div>

            <!-- Suppliers Section -->
            <div class="stat-section suppliers">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="feather icon-truck"></i>
                    </div>
                    <h2 class="section-title">الموردين</h2>
                </div>
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-label">إجمالي الموردين</span>
                        <span class="stat-value">{{ $data['suppliers']['total'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">الموردين النشطين</span>
                        <span class="stat-value success">{{ $data['suppliers']['active'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="section-card">
            <h2 class="section-title">
                <i class="feather icon-bar-chart"></i>
                الإحصائيات الشاملة
            </h2>
            <div class="chart-container">
                <canvas id="comprehensiveChart"></canvas>
            </div>
        </div>

        <!-- Report Footer -->
        <div class="report-footer">
            <div class="footer-info">
                <p>تم إنشاء التقرير: {{ now()->format('Y-m-d H:i') }}</p>
                <p>الفترة: من {{ $startDate }} إلى {{ $endDate }}</p>
            </div>
            <div class="footer-logo">
                <p class="powered-by">مصنع الحديد الذكي</p>
            </div>
        </div>
    </div>

    <style>
        :root {
            --primary-color: #0066B2;
            --secondary-color: #455A64;
            --success-color: #27AE60;
            --warning-color: #F39C12;
            --info-color: #3498DB;
            --gradient-header: linear-gradient(135deg, #0066B2 0%, #004d8a 100%);
        }

        .comprehensive-report-wrapper {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Header Styles */
        .report-header {
            background: var(--gradient-header);
            border-radius: 16px;
            padding: 30px 40px;
            margin-bottom: 30px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .icon-box {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
        }

        .header-text {
            color: white;
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

        .action-button {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }

        .action-button:hover {
            background: white;
            color: var(--primary-color);
        }

        /* Filter Section */
        .filter-section {
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
            min-width: 200px;
        }

        .filter-group label {
            display: flex;
            align-items: center;
            gap: 6px;
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
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .filter-button {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .filter-button:hover {
            transform: translateY(-2px);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 24px;
            margin-bottom: 30px;
        }

        .stat-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-top: 4px solid var(--primary-color);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .section-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            background: linear-gradient(135deg, #0066B2 0%, #004d8a 100%);
            color: white;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
        }

        .stat-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-item.wide {
            grid-column: span 2;
        }

        .stat-label {
            display: block;
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 8px;
        }

        .stat-value {
            display: block;
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
        }

        .stat-value.success {
            color: var(--success-color);
        }

        .stat-value.warning {
            color: var(--warning-color);
        }

        .stat-value.info {
            color: var(--info-color);
        }

        .stat-value.primary {
            color: var(--primary-color);
        }

        /* Report Footer */
        .report-footer {
            background: white;
            border-radius: 12px;
            padding: 20px 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        /* Chart Styles */
        .chart-container {
            position: relative;
            height: 400px;
            margin-top: 20px;
        }

        .footer-info p {
            margin: 5px 0;
            color: #7f8c8d;
            font-size: 14px;
        }

        .powered-by {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .comprehensive-report-wrapper {
                padding: 0 10px;
                margin: 15px auto;
            }

            .report-header {
                padding: 20px;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .page-title {
                font-size: 22px;
            }

            .header-actions {
                width: 100%;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .filter-form {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }
        }

        /* Print Styles */
        @media print {
            .report-header .header-actions,
            .filter-section {
                display: none;
            }

            .stat-section {
                break-inside: avoid;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Comprehensive Chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('comprehensiveChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [
                        'المستودعات',
                        'المواد الخام',
                        'أذون التسليم',
                        'فواتير المشتريات',
                        'الحركات',
                        'الصبغات',
                        'الموردين'
                    ],
                    datasets: [{
                        label: 'الإحصائيات',
                        data: [
                            {{ $data['warehouses']['total'] }},
                            {{ $data['materials']['total'] }},
                            {{ $data['delivery_notes']['total'] }},
                            {{ $data['invoices']['total'] }},
                            {{ $data['movements']['total'] }},
                            {{ $data['additives']['total'] }},
                            {{ $data['suppliers']['total'] }}
                        ],
                        backgroundColor: [
                            'rgba(0, 102, 178, 0.8)',
                            'rgba(39, 174, 96, 0.8)',
                            'rgba(243, 156, 18, 0.8)',
                            'rgba(52, 152, 219, 0.8)',
                            'rgba(156, 39, 176, 0.8)',
                            'rgba(231, 76, 60, 0.8)',
                            'rgba(142, 68, 173, 0.8)'
                        ],
                        borderColor: [
                            'rgba(0, 102, 178, 1)',
                            'rgba(39, 174, 96, 1)',
                            'rgba(243, 156, 18, 1)',
                            'rgba(52, 152, 219, 1)',
                            'rgba(156, 39, 176, 1)',
                            'rgba(231, 76, 60, 1)',
                            'rgba(142, 68, 173, 1)'
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

        function exportReport() {
            alert('جارٍ تصدير التقرير...');
            // Add export functionality
        }
    </script>
    </style>

    <script>
        function exportReport() {
            alert('جارٍ تصدير التقرير...');
            // Add export functionality
        }
    </script>
@endsection
