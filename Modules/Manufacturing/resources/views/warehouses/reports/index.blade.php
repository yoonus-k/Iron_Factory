@extends('master')

@section('title', 'تقارير وإحصائيات المستودع')

@section('content')
    <div class="reports-wrapper">
        <!-- Header Section -->
        <div class="reports-header">
            <div class="header-content">
                <div class="icon-wrapper">
                    <i class="feather icon-bar-chart-2"></i>
                </div>
                <div class="header-text">
                    <h1 class="page-title">تقارير وإحصائيات المستودع</h1>
                    <p class="page-subtitle">مركز التقارير الشامل لجميع عمليات المستودعات</p>
                </div>
            </div>
            <nav class="breadcrumb-nav">
                <a href="" class="breadcrumb-item">
                    <i class="feather icon-home"></i>
                    الرئيسية
                </a>
                <i class="feather icon-chevron-left"></i>
                <span class="breadcrumb-item active">التقارير والإحصائيات</span>
            </nav>
        </div>

        <!-- Reports Grid -->
        <div class="reports-grid">
            <!-- Comprehensive Report -->
            <div class="report-card featured">
                <div class="card-decoration">
                    <div class="decoration-circle circle-1"></div>
                    <div class="decoration-circle circle-2"></div>
                </div>
                <a href="{{ route('manufacturing.warehouse-reports.comprehensive') }}" class="report-link">
                    <div class="report-icon comprehensive">
                        <i class="feather icon-file-text"></i>
                    </div>
                    <div class="report-content">
                        <h3 class="report-title">التقرير الشامل</h3>
                        <p class="report-description">نظرة عامة شاملة على جميع أقسام المستودع</p>
                        <div class="report-badge">
                            <i class="feather icon-trending-up"></i>
                            تقرير متكامل
                        </div>
                    </div>
                    <div class="report-arrow">
                        <i class="feather icon-arrow-left"></i>
                    </div>
                </a>
            </div>

            <!-- Warehouses Statistics -->
            <div class="report-card">
                <a href="{{ route('manufacturing.warehouse-reports.warehouses-statistics') }}" class="report-link">
                    <div class="report-icon primary">
                        <i class="feather icon-home"></i>
                    </div>
                    <div class="report-content">
                        <h3 class="report-title">إحصائيات المستودعات</h3>
                        <p class="report-description">تقرير شامل عن جميع المستودعات والسعات التخزينية</p>
                    </div>
                    <div class="report-arrow">
                        <i class="feather icon-arrow-left"></i>
                    </div>
                </a>
            </div>

            <!-- Materials Report -->
            <div class="report-card">
                <a href="{{ route('manufacturing.warehouse-reports.materials') }}" class="report-link">
                    <div class="report-icon success">
                        <i class="feather icon-package"></i>
                    </div>
                    <div class="report-content">
                        <h3 class="report-title">تقرير المواد والمخزون</h3>
                        <p class="report-description">حالة المواد الخام والكميات المتاحة والتنبيهات</p>
                    </div>
                    <div class="report-arrow">
                        <i class="feather icon-arrow-left"></i>
                    </div>
                </a>
            </div>

            <!-- Delivery Notes Report -->
            <div class="report-card">
                <a href="{{ route('manufacturing.warehouse-reports.delivery-notes') }}" class="report-link">
                    <div class="report-icon warning">
                        <i class="feather icon-file-text"></i>
                    </div>
                    <div class="report-content">
                        <h3 class="report-title">تقرير أذون التسليم</h3>
                        <p class="report-description">متابعة أذون التسليم والشحنات الواردة</p>
                    </div>
                    <div class="report-arrow">
                        <i class="feather icon-arrow-left"></i>
                    </div>
                </a>
            </div>

            <!-- Purchase Invoices Report -->
            <div class="report-card">
                <a href="{{ route('manufacturing.warehouse-reports.purchase-invoices') }}" class="report-link">
                    <div class="report-icon info">
                        <i class="feather icon-file"></i>
                    </div>
                    <div class="report-content">
                        <h3 class="report-title">تقرير فواتير المشتريات</h3>
                        <p class="report-description">التقارير المالية والفواتير وحالات الدفع</p>
                    </div>
                    <div class="report-arrow">
                        <i class="feather icon-arrow-left"></i>
                    </div>
                </a>
            </div>

            <!-- Additives Report -->
            <div class="report-card">
                <a href="{{ route('manufacturing.warehouse-reports.additives') }}" class="report-link">
                    <div class="report-icon purple">
                        <i class="feather icon-droplet"></i>
                    </div>
                    <div class="report-content">
                        <h3 class="report-title">تقرير الصبغات والبلاستيك</h3>
                        <p class="report-description">حالة المواد الكيميائية والإضافات</p>
                    </div>
                    <div class="report-arrow">
                        <i class="feather icon-arrow-left"></i>
                    </div>
                </a>
            </div>

            <!-- Movements Report -->
            <div class="report-card">
                <a href="{{ route('manufacturing.warehouse-reports.movements') }}" class="report-link">
                    <div class="report-icon orange">
                        <i class="feather icon-repeat"></i>
                    </div>
                    <div class="report-content">
                        <h3 class="report-title">تقرير الحركات والتحويلات</h3>
                        <p class="report-description">سجل حركات المواد بين المستودعات</p>
                    </div>
                    <div class="report-arrow">
                        <i class="feather icon-arrow-left"></i>
                    </div>
                </a>
            </div>

            <!-- Suppliers Report -->
            <div class="report-card">
                <a href="{{ route('manufacturing.warehouse-reports.suppliers') }}" class="report-link">
                    <div class="report-icon secondary">
                        <i class="feather icon-truck"></i>
                    </div>
                    <div class="report-content">
                        <h3 class="report-title">تقرير الموردين</h3>
                        <p class="report-description">إحصائيات الموردين والتعاملات معهم</p>
                    </div>
                    <div class="report-arrow">
                        <i class="feather icon-arrow-left"></i>
                    </div>
                </a>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="quick-actions-section">
            <h2 class="section-title">
                <i class="feather icon-zap"></i>
                إجراءات سريعة
            </h2>
            <div class="quick-actions-grid">
                <button class="action-btn" onclick="window.print()">
                    <i class="feather icon-printer"></i>
                    <span>طباعة</span>
                </button>
                <button class="action-btn" onclick="exportToExcel()">
                    <i class="feather icon-download"></i>
                    <span>تصدير Excel</span>
                </button>
                <button class="action-btn" onclick="exportToPDF()">
                    <i class="feather icon-file"></i>
                    <span>تصدير PDF</span>
                </button>
                <button class="action-btn" onclick="shareReport()">
                    <i class="feather icon-share-2"></i>
                    <span>مشاركة</span>
                </button>
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
            --purple-color: #9C27B0;
            --orange-color: #FF6B35;
            --gradient-primary: linear-gradient(135deg, #0066B2 0%, #004d8a 100%);
            --gradient-success: linear-gradient(135deg, #27AE60 0%, #1e8449 100%);
            --gradient-warning: linear-gradient(135deg, #F39C12 0%, #d68910 100%);
            --gradient-info: linear-gradient(135deg, #3498DB 0%, #2874a6 100%);
            --gradient-purple: linear-gradient(135deg, #9C27B0 0%, #7b1fa2 100%);
            --gradient-orange: linear-gradient(135deg, #FF6B35 0%, #e55527 100%);
            --gradient-comprehensive: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 8px rgba(0,0,0,0.1);
            --shadow-lg: 0 8px 16px rgba(0,0,0,0.15);
            --shadow-xl: 0 12px 24px rgba(0,0,0,0.2);
        }

        .reports-wrapper {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Header Styles */
        .reports-header {
            background: linear-gradient(135deg, #0066B2 0%, #004d8a 100%);
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .reports-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .reports-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
            z-index: 1;
        }

        .icon-wrapper {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            backdrop-filter: blur(10px);
        }

        .header-text {
            flex: 1;
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: white;
            margin: 0 0 8px 0;
        }

        .page-subtitle {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
        }

        .breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            font-size: 14px;
            position: relative;
            z-index: 1;
        }

        .breadcrumb-item {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.3s;
        }

        .breadcrumb-item:hover {
            color: white;
        }

        .breadcrumb-item.active {
            color: white;
            font-weight: 600;
        }

        /* Reports Grid */
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .report-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .report-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }

        .report-card.featured {
            grid-column: span 2;
            background: var(--gradient-comprehensive);
        }

        .card-decoration {
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            bottom: 0;
            pointer-events: none;
            opacity: 0.3;
        }

        .decoration-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        .circle-1 {
            width: 200px;
            height: 200px;
            top: -50px;
            right: -50px;
        }

        .circle-2 {
            width: 150px;
            height: 150px;
            bottom: -30px;
            left: -30px;
        }

        .report-link {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 28px;
            text-decoration: none;
            color: inherit;
            position: relative;
            z-index: 1;
        }

        .report-icon {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            flex-shrink: 0;
            box-shadow: var(--shadow-sm);
        }

        .report-icon.comprehensive {
            background: white;
            color: #667eea;
        }

        .report-icon.primary {
            background: var(--gradient-primary);
            color: white;
        }

        .report-icon.success {
            background: var(--gradient-success);
            color: white;
        }

        .report-icon.warning {
            background: var(--gradient-warning);
            color: white;
        }

        .report-icon.info {
            background: var(--gradient-info);
            color: white;
        }

        .report-icon.purple {
            background: var(--gradient-purple);
            color: white;
        }

        .report-icon.orange {
            background: var(--gradient-orange);
            color: white;
        }

        .report-icon.secondary {
            background: linear-gradient(135deg, #455A64 0%, #263238 100%);
            color: white;
        }

        .report-content {
            flex: 1;
        }

        .report-card.featured .report-content {
            color: white;
        }

        .report-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #2c3e50;
        }

        .report-card.featured .report-title {
            font-size: 22px;
            color: white;
        }

        .report-description {
            font-size: 14px;
            color: #7f8c8d;
            margin: 0;
            line-height: 1.5;
        }

        .report-card.featured .report-description {
            color: rgba(255, 255, 255, 0.9);
            font-size: 15px;
        }

        .report-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 12px;
        }

        .report-arrow {
            color: #bdc3c7;
            font-size: 20px;
            transition: transform 0.3s;
        }

        .report-card:hover .report-arrow {
            transform: translateX(-5px);
        }

        .report-card.featured .report-arrow {
            color: white;
        }

        /* Quick Actions Section */
        .quick-actions-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: var(--shadow-md);
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

        .section-title i {
            color: var(--primary-color);
        }

        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 20px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            cursor: pointer;
            transition: all 0.3s;
        }

        .action-btn:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .action-btn i {
            font-size: 24px;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .report-card.featured {
                grid-column: span 1;
            }
        }

        @media (max-width: 768px) {
            .reports-wrapper {
                padding: 0 15px;
                margin: 15px auto;
            }

            .reports-header {
                padding: 25px;
                border-radius: 12px;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .page-title {
                font-size: 24px;
            }

            .page-subtitle {
                font-size: 14px;
            }

            .reports-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .report-card.featured {
                grid-column: span 1;
            }

            .report-link {
                padding: 20px;
            }

            .report-icon {
                width: 50px;
                height: 50px;
                font-size: 22px;
            }

            .report-title {
                font-size: 16px;
            }

            .quick-actions-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Print Styles */
        @media print {
            .reports-header,
            .quick-actions-section {
                display: none;
            }

            .report-card {
                break-inside: avoid;
            }
        }
    </style>

    <script>
        function exportToExcel() {
            alert('جارٍ تصدير التقرير إلى Excel...');
            // Add export functionality here
        }

        function exportToPDF() {
            alert('جارٍ تصدير التقرير إلى PDF...');
            // Add export functionality here
        }

        function shareReport() {
            if (navigator.share) {
                navigator.share({
                    title: 'تقارير المستودع',
                    text: 'تقارير وإحصائيات المستودع',
                    url: window.location.href
                });
            } else {
                alert('المشاركة غير مدعومة في هذا المتصفح');
            }
        }
    </script>
@endsection
