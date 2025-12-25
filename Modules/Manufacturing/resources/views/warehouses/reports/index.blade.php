@extends('master')

@section('title', __('warehouse.reports_and_statistics'))

@section('content')
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="header-content">
                <div class="icon-box">
                    <i class="feather icon-bar-chart-2"></i>
                </div>
                <div>
                    <h1 class="page-title">{{ __('warehouse.reports_and_statistics') }}</h1>
                    <p class="page-subtitle">{{ __('warehouse.reports_center_subtitle') }}</p>
                </div>
            </div>
            <div class="header-actions">
                <button onclick="window.print()" class="btn-action">
                    <i class="feather icon-printer"></i> {{ __('warehouse.print') }}
                </button>
                <a href="{{ url()->previous() }}" class="btn-action">
                    <i class="feather icon-arrow-right"></i> {{ __('warehouse.back') }}
                </a>
            </div>
        </div>

        <!-- Reports Grid -->
        <div class="section-card">
            <h2 class="section-title">
                <i class="feather icon-layout"></i>
                {{ __('warehouse.reports_list') }}
            </h2>
            <div class="reports-grid">
                <!-- Comprehensive Report -->
                <div class="report-card featured">
                    <a href="{{ route('manufacturing.warehouse-reports.comprehensive') }}" class="report-link">
                        <div class="report-icon comprehensive">
                            <i class="feather icon-file-text"></i>
                        </div>
                        <div class="report-content">
                            <h3 class="report-title">{{ __('warehouse.comprehensive_report') }}</h3>
                            <p class="report-description">{{ __('warehouse.comprehensive_report_description') }}</p>
                            <div class="report-badge">
                                <i class="feather icon-trending-up"></i>
                                {{ __('warehouse.integrated_report') }}
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
                            <h3 class="report-title">{{ __('warehouse.warehouses_statistics') }}</h3>
                            <p class="report-description">{{ __('warehouse.warehouses_statistics_description') }}</p>
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
                            <h3 class="report-title">{{ __('warehouse.materials_report') }}</h3>
                            <p class="report-description">{{ __('warehouse.materials_report_description') }}</p>
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
                            <h3 class="report-title">{{ __('warehouse.delivery_notes_report') }}</h3>
                            <p class="report-description">{{ __('warehouse.delivery_notes_report_description') }}</p>
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
                            <h3 class="report-title">{{ __('warehouse.purchase_invoices_report') }}</h3>
                            <p class="report-description">{{ __('warehouse.purchase_invoices_report_description') }}</p>
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
                            <h3 class="report-title">{{ __('warehouse.movements_report') }}</h3>
                            <p class="report-description">{{ __('warehouse.movements_report_description') }}</p>
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
                            <h3 class="report-title">{{ __('warehouse.suppliers_report') }}</h3>
                            <p class="report-description">{{ __('warehouse.suppliers_report_description') }}</p>
                        </div>
                        <div class="report-arrow">
                            <i class="feather icon-arrow-left"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="section-card">
            <h2 class="section-title">
                <i class="feather icon-zap"></i>
                {{ __('warehouse.quick_actions') }}
            </h2>
            <div class="quick-actions-grid">
                <button class="action-btn" onclick="window.print()">
                    <i class="feather icon-printer"></i>
                    <span>{{ __('warehouse.print') }}</span>
                </button>
                <button class="action-btn" onclick="exportToExcel()">
                    <i class="feather icon-download"></i>
                    <span>{{ __('warehouse.export_excel') }}</span>
                </button>
                <button class="action-btn" onclick="exportToPDF()">
                    <i class="feather icon-file"></i>
                    <span>{{ __('warehouse.export_pdf') }}</span>
                </button>
                <button class="action-btn" onclick="shareReport()">
                    <i class="feather icon-share-2"></i>
                    <span>{{ __('warehouse.share') }}</span>
                </button>
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

        /* Reports Grid */
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 24px;
        }

        .report-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            border: 1px solid #eee;
        }

        .report-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }

        .report-card.featured {
            grid-column: span 2;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .report-icon.comprehensive {
            background: white;
            color: #667eea;
        }

        .report-icon.primary {
            background: linear-gradient(135deg, #0066B2, #004d8a);
            color: white;
        }

        .report-icon.success {
            background: linear-gradient(135deg, #27AE60, #1e8449);
            color: white;
        }

        .report-icon.warning {
            background: linear-gradient(135deg, #F39C12, #d68910);
            color: white;
        }

        .report-icon.info {
            background: linear-gradient(135deg, #3498DB, #2874a6);
            color: white;
        }

        .report-icon.purple {
            background: linear-gradient(135deg, #9C27B0, #7b1fa2);
            color: white;
        }

        .report-icon.orange {
            background: linear-gradient(135deg, #FF6B35, #e55527);
            color: white;
        }

        .report-icon.secondary {
            background: linear-gradient(135deg, #455A64, #263238);
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
            background: #0066B2;
            border-color: #0066B2;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
            .report-header {
                flex-direction: column;
                text-align: center;
            }

            .header-content {
                flex-direction: column;
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
            .header-actions {
                display: none;
            }

            .report-card {
                break-inside: avoid;
            }
        }
    </style>

    <script>
        function exportToExcel() {
            alert('{{ __('warehouse.exporting_report_to_excel') }}');
            // Add export functionality here
        }

        function exportToPDF() {
            alert('{{ __('warehouse.exporting_report_to_pdf') }}');
            // Add export functionality here
        }

        function shareReport() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ __('warehouse.warehouse_reports') }}',
                    text: '{{ __('warehouse.reports_and_statistics') }}',
                    url: window.location.href
                });
            } else {
                alert('{{ __('warehouse.share_not_supported') }}');
            }
        }
    </script>
@endsection