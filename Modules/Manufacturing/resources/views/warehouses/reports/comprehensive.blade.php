@extends('master')

@section('title', 'التقرير الشامل للمستودعات')

@section('content')
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="header-content">
                <div class="icon-box">
                    <i class="feather icon-bar-chart-2"></i>
                </div>
                <div>
                    <h1 class="page-title">التقرير الشامل للمستودعات</h1>
                    <p class="page-subtitle">نظرة عامة متكاملة على جميع عمليات وأنشطة المستودعات والمواد والتسليمات</p>
                </div>
            </div>
            <div class="header-actions">
                <button onclick="window.print()" class="btn-action print-btn">
                    <i class="feather icon-printer"></i> طباعة
                </button>
                <button onclick="exportReport()" class="btn-action export-btn">
                    <i class="feather icon-download"></i> تصدير PDF
                </button>
                <a href="{{ route('manufacturing.warehouse-reports.index') }}" class="btn-action back-btn">
                    <i class="feather icon-arrow-right"></i> رجوع
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-card">
            <h3 class="filter-title">
                <i class="feather icon-sliders"></i>
                تصفية التقرير
            </h3>
            <form method="GET" action="{{ route('manufacturing.warehouse-reports.comprehensive') }}" class="filter-form">
                <div class="filter-group">
                    <label for="start_date">
                        <i class="feather icon-calendar"></i>
                        من تاريخ
                    </label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="form-control">
                </div>
                <div class="filter-group">
                    <label for="end_date">
                        <i class="feather icon-calendar"></i>
                        إلى تاريخ
                    </label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="form-control">
                </div>
                <button type="submit" class="btn-filter">
                    <i class="feather icon-search"></i>
                    <span>عرض النتائج</span>
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

            <div class="stat-card blue-light">
                <div class="stat-icon">
                    <i class="feather icon-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $data['warehouses']['active'] }}</h3>
                    <p>المستودعات النشطة</p>
                </div>
            </div>

            <div class="stat-card blue-dark">
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
                <i class="feather icon-bar-chart-2"></i>
                رسم بياني للإحصائيات
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
                    إحصائيات المواد الخام
                </h3>
                <div class="stats-cards">
                    <div class="stat-card blue">
                        <div class="stat-icon">
                            <i class="feather icon-package"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['materials']['total'] }}</h3>
                            <p>إجمالي الأنواع</p>
                        </div>
                    </div>

                    <div class="stat-card blue-light">
                        <div class="stat-icon">
                            <i class="feather icon-layers"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ number_format($data['materials']['total_quantity'], 2) }}</h3>
                            <p>إجمالي الكميات</p>
                        </div>
                    </div>

                    <div class="stat-card blue-dark">
                        <div class="stat-icon">
                            <i class="feather icon-alert-triangle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['materials']['low_stock'] }}</h3>
                            <p>مخزون منخفض</p>
                        </div>
                    </div>

                    <div class="stat-card blue">
                        <div class="stat-icon">
                            <i class="feather icon-weight"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ number_format($data['materials']['total_weight'], 2) }}</h3>
                            <p>الوزن الإجمالي</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Notes Section -->
            <div class="sub-section">
                <h3 class="sub-title">
                    <i class="feather icon-file-text"></i>
                    إحصائيات أذون التسليم
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

                    <div class="stat-card blue-light">
                        <div class="stat-icon">
                            <i class="feather icon-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['delivery_notes']['pending'] }}</h3>
                            <p>قيد الانتظار</p>
                        </div>
                    </div>

                    <div class="stat-card blue-dark">
                        <div class="stat-icon">
                            <i class="feather icon-check-square"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['delivery_notes']['approved'] }}</h3>
                            <p>موافق عليه</p>
                        </div>
                    </div>

                    <div class="stat-card blue">
                        <div class="stat-icon">
                            <i class="feather icon-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['delivery_notes']['completed'] }}</h3>
                            <p>مكتمل</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoices Section -->
            <div class="sub-section">
                <h3 class="sub-title">
                    <i class="feather icon-file"></i>
                    إحصائيات فواتير المشتريات
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

                    <div class="stat-card blue-light">
                        <div class="stat-icon">
                            <i class="feather icon-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ number_format($data['invoices']['total_amount'], 2) }}</h3>
                            <p>إجمالي المبلغ</p>
                        </div>
                    </div>

                    <div class="stat-card blue-dark">
                        <div class="stat-icon">
                            <i class="feather icon-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['invoices']['paid'] }}</h3>
                            <p>مدفوعة</p>
                        </div>
                    </div>

                    <div class="stat-card blue">
                        <div class="stat-icon">
                            <i class="feather icon-hourglass"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['invoices']['pending'] }}</h3>
                            <p>قيد الانتظار</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Movements Section -->
            <div class="sub-section">
                <h3 class="sub-title">
                    <i class="feather icon-move"></i>
                    إحصائيات الحركات والتحويلات
                </h3>
                <div class="stats-cards">
                    <div class="stat-card blue">
                        <div class="stat-icon">
                            <i class="feather icon-move"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['movements']['total'] }}</h3>
                            <p>إجمالي الحركات</p>
                        </div>
                    </div>

                    <div class="stat-card blue-light">
                        <div class="stat-icon">
                            <i class="feather icon-inbox"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['movements']['receive'] }}</h3>
                            <p>عمليات الاستقبال</p>
                        </div>
                    </div>

                    <div class="stat-card blue-dark">
                        <div class="stat-icon">
                            <i class="feather icon-send"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['movements']['issue'] }}</h3>
                            <p>عمليات الصرف</p>
                        </div>
                    </div>

                    <div class="stat-card blue">
                        <div class="stat-icon">
                            <i class="feather icon-shuffle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['movements']['transfer'] }}</h3>
                            <p>عمليات التحويل</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additives Section -->
            <div class="sub-section">
                <h3 class="sub-title">
                    <i class="feather icon-droplet"></i>
                    إحصائيات الصبغات والبلاستيك
                </h3>
                <div class="stats-cards">
                    <div class="stat-card blue">
                        <div class="stat-icon">
                            <i class="feather icon-droplet"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['additives']['total'] }}</h3>
                            <p>إجمالي الأنواع</p>
                        </div>
                    </div>

                    <div class="stat-card blue-light">
                        <div class="stat-icon">
                            <i class="feather icon-check-square"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['additives']['active'] }}</h3>
                            <p>نشطة</p>
                        </div>
                    </div>

                    <div class="stat-card blue-dark">
                        <div class="stat-icon">
                            <i class="feather icon-layers"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ number_format($data['additives']['total_quantity'], 2) }}</h3>
                            <p>إجمالي الكميات</p>
                        </div>
                    </div>

                    <div class="stat-card blue">
                        <div class="stat-icon">
                            <i class="feather icon-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ number_format($data['additives']['total_value'], 2) }}</h3>
                            <p>إجمالي القيمة</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suppliers Section -->
            <div class="sub-section">
                <h3 class="sub-title">
                    <i class="feather icon-users"></i>
                    إحصائيات الموردين
                </h3>
                <div class="stats-cards">
                    <div class="stat-card blue">
                        <div class="stat-icon">
                            <i class="feather icon-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['suppliers']['total'] }}</h3>
                            <p>إجمالي الموردين</p>
                        </div>
                    </div>

                    <div class="stat-card blue-light">
                        <div class="stat-icon">
                            <i class="feather icon-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $data['suppliers']['active'] }}</h3>
                            <p>موردين نشطين</p>
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
        :root {
            --primary-blue: #0066B2;
            --primary-dark-blue: #004d8a;
            --primary-light-blue: #3385c7;
            --secondary-blue: #1e5a96;
            --text-dark: #1a2332;
            --text-gray: #6c757d;
            --light-bg: #f5f7fa;
            --border-color: #dee2e6;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Tahoma', 'Geneva', 'Verdana', 'sans-serif';
            background-color: var(--light-bg);
            color: var(--text-dark);
            line-height: 1.6;
        }

        .report-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* ========== HEADER SECTION ========== */
        .report-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark-blue) 100%);
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--white);
            box-shadow: 0 10px 30px rgba(0, 102, 178, 0.25);
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 25px;
            flex: 1;
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: var(--white);
            flex-shrink: 0;
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 8px 0;
            letter-spacing: 0.5px;
        }

        .page-subtitle {
            font-size: 15px;
            margin: 0;
            opacity: 0.95;
            font-weight: 400;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-action {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 8px;
            color: var(--white);
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-action:hover {
            background: var(--white);
            color: var(--primary-blue);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        /* ========== FILTER SECTION ========== */
        .filter-card {
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border-left: 5px solid var(--primary-blue);
        }

        .filter-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
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
            gap: 8px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 10px;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            transition: all 0.3s ease;
            background: var(--white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(0, 102, 178, 0.1);
            background: var(--white);
        }

        .btn-filter {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: var(--primary-blue);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-filter:hover {
            background: var(--secondary-blue);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 102, 178, 0.3);
        }

        /* ========== STATISTICS CARDS ========== */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border-top: 4px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 102, 178, 0.15);
        }

        .stat-card.blue {
            border-top-color: var(--primary-blue);
        }

        .stat-card.blue-light {
            border-top-color: var(--primary-light-blue);
        }

        .stat-card.blue-dark {
            border-top-color: var(--primary-dark-blue);
        }

        .stat-icon {
            width: 65px;
            height: 65px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: var(--white);
            flex-shrink: 0;
        }

        .stat-card.blue .stat-icon {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        }

        .stat-card.blue-light .stat-icon {
            background: linear-gradient(135deg, var(--primary-light-blue), var(--primary-blue));
        }

        .stat-card.blue-dark .stat-icon {
            background: linear-gradient(135deg, var(--primary-dark-blue), #003a6a);
        }

        .stat-info {
            flex: 1;
        }

        .stat-info h3 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 6px 0;
            color: var(--text-dark);
            line-height: 1;
        }

        .stat-info p {
            font-size: 14px;
            color: var(--text-gray);
            margin: 0;
            font-weight: 500;
        }

        /* ========== SECTION CARD ========== */
        .section-card {
            background: var(--white);
            border-radius: 12px;
            padding: 35px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border-left: 5px solid var(--primary-blue);
        }

        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 25px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: 0.3px;
        }

        .section-title i {
            color: var(--primary-blue);
            font-size: 26px;
        }

        .sub-section {
            margin-bottom: 35px;
            padding-bottom: 30px;
            border-bottom: 2px solid var(--light-bg);
        }

        .sub-section:last-child {
            margin-bottom: 0;
            border-bottom: none;
        }

        .sub-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sub-title i {
            color: var(--primary-blue);
            font-size: 20px;
        }

        /* ========== CHART SECTION ========== */
        .chart-container {
            position: relative;
            height: 350px;
            margin-top: 25px;
            padding: 20px;
            background: var(--light-bg);
            border-radius: 8px;
        }

        /* ========== FOOTER SECTION ========== */
        .report-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            padding: 30px 0;
            border-top: 2px solid var(--border-color);
        }

        .footer-info p {
            margin: 8px 0;
            color: var(--text-gray);
            font-size: 14px;
            font-weight: 500;
        }

        .powered-by {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-blue);
            margin: 0;
        }

        /* ========== RESPONSIVE DESIGN ========== */
        @media (max-width: 768px) {
            .report-header {
                flex-direction: column;
                text-align: center;
                padding: 25px;
                gap: 15px;
            }

            .header-content {
                flex-direction: column;
                width: 100%;
            }

            .page-title {
                font-size: 24px;
            }

            .stats-cards {
                grid-template-columns: 1fr;
            }

            .filter-form {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
                min-width: unset;
            }

            .section-card {
                padding: 20px;
            }

            .stat-info h3 {
                font-size: 24px;
            }
        }

        /* ========== PRINT STYLES ========== */
        @media print {
            .report-container {
                margin: 0;
                padding: 0;
            }

            .header-actions {
                display: none;
            }

            .filter-card {
                display: none;
            }

            .report-header {
                color-adjust: exact;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .section-card {
                page-break-inside: avoid;
            }

            body {
                background: white;
            }
        }

        /* ========== ANIMATIONS ========== */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .report-header,
        .section-card,
        .stat-card {
            animation: slideInUp 0.5s ease;
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
                        label: 'الكمية الإجمالية',
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
                            'rgba(0, 102, 178, 0.85)',
                            'rgba(30, 90, 150, 0.85)',
                            'rgba(0, 102, 178, 0.75)',
                            'rgba(52, 133, 199, 0.85)',
                            'rgba(0, 77, 138, 0.85)',
                            'rgba(62, 113, 166, 0.85)',
                            'rgba(45, 100, 160, 0.85)'
                        ],
                        borderColor: [
                            'rgba(0, 102, 178, 1)',
                            'rgba(30, 90, 150, 1)',
                            'rgba(0, 102, 178, 1)',
                            'rgba(52, 133, 199, 1)',
                            'rgba(0, 77, 138, 1)',
                            'rgba(62, 113, 166, 1)',
                            'rgba(45, 100, 160, 1)'
                        ],
                        borderWidth: 2,
                        borderRadius: 8,
                        hoverBackgroundColor: [
                            'rgba(0, 102, 178, 1)',
                            'rgba(30, 90, 150, 1)',
                            'rgba(0, 102, 178, 1)',
                            'rgba(52, 133, 199, 1)',
                            'rgba(0, 77, 138, 1)',
                            'rgba(62, 113, 166, 1)',
                            'rgba(45, 100, 160, 1)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14,
                                    weight: '600',
                                    family: "'Segoe UI', 'Tahoma', 'Geneva'"
                                },
                                color: '#1a2332',
                                padding: 20,
                                usePointStyle: true,
                                boxWidth: 12
                            }
                        },
                        tooltip: {
                            rtl: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.85)',
                            titleFont: {
                                size: 14,
                                weight: '600',
                                family: "'Segoe UI', 'Tahoma', 'Geneva'"
                            },
                            bodyFont: {
                                size: 13,
                                family: "'Segoe UI', 'Tahoma', 'Geneva'"
                            },
                            padding: 12,
                            borderColor: '#0066B2',
                            borderWidth: 1,
                            titleColor: '#fff',
                            bodyColor: '#fff'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '500'
                                },
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0, 102, 178, 0.05)',
                                borderDash: [5, 5]
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '500'
                                },
                                color: '#6c757d'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });

        function exportReport() {
            // You can use a library like html2pdf or jsPDF for actual PDF export
            alert('جارٍ تصدير التقرير... سيتم الحفظ كملف PDF');
            window.print();
        }
    </script>
@endsection
