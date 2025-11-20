@extends('master')

@section('title', 'التقرير الشامل للمستودع')

@section('content')
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="header-content">
                <div class="icon-box">
                    <i class="feather icon-file-text"></i>
                </div>
                <div>
                    <h1 class="page-title">التقرير الشامل للمستودع</h1>
                    <p class="page-subtitle">نظرة عامة متكاملة على جميع عمليات وأنشطة المستودعات</p>
                </div>
            </div>
            <div class="header-actions">
                <button onclick="window.print()" class="btn-action">
                    <i class="feather icon-printer"></i> طباعة
                </button>
                <button onclick="exportReport()" class="btn-action">
                    <i class="feather icon-download"></i> تصدير
                </button>
                <a href="{{ route('manufacturing.warehouse-reports.index') }}" class="btn-action">
                    <i class="feather icon-arrow-right"></i> رجوع
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-card">
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
                <button type="submit" class="btn-filter">
                    <i class="feather icon-search"></i>
                    <span>عرض</span>
                </button>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <!-- Warehouses Section -->
            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="feather icon-home"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $data['warehouses']['total'] }}</h3>
                    <p>إجمالي المستودعات</p>
                </div>
            </div>
            
            <div class="stat-card green">
                <div class="stat-icon">
                    <i class="feather icon-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $data['warehouses']['active'] }}</h3>
                    <p>المستودعات النشطة</p>
                </div>
            </div>
            
            <div class="stat-card orange">
                <div class="stat-icon">
                    <i class="feather icon-database"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($data['warehouses']['total_capacity'], 2) }}</h3>
                    <p>السعة الإجمالية</p>
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

        <!-- Detailed Statistics -->
        <div class="section-card">
            <h2 class="section-title">
                <i class="feather icon-list"></i>
                الإحصائيات التفصيلية
            </h2>
            
            <!-- Materials Section -->
            <div class="sub-section">
                <h3 class="sub-title">
                    <i class="feather icon-package"></i>
                    المواد الخام
                </h3>
                <div class="stats-cards">
                    <div class="stat-card blue">
                        <div class="stat-icon">
                            <i class="feather icon-package"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['materials']['total'] }}</h3>
                            <p>إجمالي المواد</p>
                        </div>
                    </div>
                    
                    <div class="stat-card green">
                        <div class="stat-icon">
                            <i class="feather icon-layers"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ number_format($data['materials']['total_quantity'], 2) }}</h3>
                            <p>إجمالي الكميات</p>
                        </div>
                    </div>
                    
                    <div class="stat-card orange">
                        <div class="stat-icon">
                            <i class="feather icon-alert-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['materials']['low_stock'] }}</h3>
                            <p>مخزون منخفض</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Delivery Notes Section -->
            <div class="sub-section">
                <h3 class="sub-title">
                    <i class="feather icon-file-text"></i>
                    أذون التسليم
                </h3>
                <div class="stats-cards">
                    <div class="stat-card blue">
                        <div class="stat-icon">
                            <i class="feather icon-file-text"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['delivery_notes']['total'] }}</h3>
                            <p>إجمالي الأذون</p>
                        </div>
                    </div>
                    
                    <div class="stat-card orange">
                        <div class="stat-icon">
                            <i class="feather icon-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['delivery_notes']['pending'] }}</h3>
                            <p>قيد الانتظار</p>
                        </div>
                    </div>
                    
                    <div class="stat-card green">
                        <div class="stat-icon">
                            <i class="feather icon-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['delivery_notes']['approved'] }}</h3>
                            <p>موافق عليه</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Invoices Section -->
            <div class="sub-section">
                <h3 class="sub-title">
                    <i class="feather icon-file"></i>
                    فواتير المشتريات
                </h3>
                <div class="stats-cards">
                    <div class="stat-card blue">
                        <div class="stat-icon">
                            <i class="feather icon-file"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['invoices']['total'] }}</h3>
                            <p>عدد الفواتير</p>
                        </div>
                    </div>
                    
                    <div class="stat-card green">
                        <div class="stat-icon">
                            <i class="feather icon-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ number_format($data['invoices']['paid'], 2) }}</h3>
                            <p>مدفوعة</p>
                        </div>
                    </div>
                    
                    <div class="stat-card orange">
                        <div class="stat-icon">
                            <i class="feather icon-alert-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ number_format($data['invoices']['pending'], 2) }}</h3>
                            <p>قيد الانتظار</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Report Footer -->
        <div class="section-card">
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
            border-color: #0066B2;
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
            transition: transform 0.2s;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
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

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sub-section {
            margin-bottom: 30px;
        }

        .sub-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 15px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Chart Styles */
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 20px;
        }

        .report-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .footer-info p {
            margin: 5px 0;
            color: #7f8c8d;
            font-size: 14px;
        }

        .powered-by {
            font-size: 18px;
            font-weight: 700;
            color: #0066B2;
            margin: 0;
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

            .filter-form {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }
        }

        /* Print Styles */
        @media print {
            .header-actions {
                display: none;
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
@endsection