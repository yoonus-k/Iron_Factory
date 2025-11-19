@extends('master')

@section('title', 'لوحة التحكم في المستودعات')

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-home"></i>
                لوحة التحكم في المستودعات
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>لوحة التحكم في المستودعات</span>
            </nav>
        </div>

        <!-- Warehouse Dashboard Cards -->
        <div class="um-grid">
            <!-- Warehouse Management Card -->
            <div class="um-card um-card-link">
                <a href="{{ route('manufacturing.warehouses.index') }}" class="um-card-link-wrapper">
                    <div class="um-card-icon primary">
                        <i class="feather icon-home"></i>
                    </div>
                    <div class="um-card-content">
                        <h3 class="um-card-title">إدارة المستودعات</h3>
                        <p class="um-card-description">إدارة المستودعات والمخازن</p>
                    </div>
                    <div class="um-card-arrow">
                        <i class="feather icon-chevron-left"></i>
                    </div>
                </a>
            </div>

            <!-- Suppliers Card -->
            <div class="um-card um-card-link">
                <a href="{{ route('manufacturing.suppliers.index') }}" class="um-card-link-wrapper">
                    <div class="um-card-icon success">
                        <i class="feather icon-truck"></i>
                    </div>
                    <div class="um-card-content">
                        <h3 class="um-card-title">الموردين</h3>
                        <p class="um-card-description">إدارة معلومات الموردين والشركاء</p>
                    </div>
                    <div class="um-card-arrow">
                        <i class="feather icon-chevron-left"></i>
                    </div>
                </a>
            </div>

            <!-- Delivery Notes Card -->
            <div class="um-card um-card-link">
                <a href="{{ route('manufacturing.delivery-notes.index') }}" class="um-card-link-wrapper">
                    <div class="um-card-icon warning">
                        <i class="feather icon-file-text"></i>
                    </div>
                    <div class="um-card-content">
                        <h3 class="um-card-title">أذون التسليم</h3>
                        <p class="um-card-description">إدارة أذون تسليم المواد الخام</p>
                    </div>
                    <div class="um-card-arrow">
                        <i class="feather icon-chevron-left"></i>
                    </div>
                </a>
            </div>

            <!-- Purchase Invoices Card -->
            <div class="um-card um-card-link">
                <a href="{{ route('manufacturing.purchase-invoices.index') }}" class="um-card-link-wrapper">
                    <div class="um-card-icon info">
                        <i class="feather icon-file"></i>
                    </div>
                    <div class="um-card-content">
                        <h3 class="um-card-title">فواتير المشتريات</h3>
                        <p class="um-card-description">إدارة الفواتير الخاصة بالمشتريات</p>
                    </div>
                    <div class="um-card-arrow">
                        <i class="feather icon-chevron-left"></i>
                    </div>
                </a>
            </div>

            <!-- Additives Card -->
            <div class="um-card um-card-link">
                <a href="{{ route('manufacturing.additives.index') }}" class="um-card-link-wrapper">
                    <div class="um-card-icon purple">
                        <i class="feather icon-droplet"></i>
                    </div>
                    <div class="um-card-content">
                        <h3 class="um-card-title">الصبغات والبلاستيك</h3>
                        <p class="um-card-description">إدارة المواد الكيميائية والبلاستيك</p>
                    </div>
                    <div class="um-card-arrow">
                        <i class="feather icon-chevron-left"></i>
                    </div>
                </a>
            </div>

            <!-- Settings Card -->
            <div class="um-card um-card-link">
                <a href="{{ route('manufacturing.warehouse-settings.index') }}" class="um-card-link-wrapper">
                    <div class="um-card-icon secondary">
                        <i class="feather icon-settings"></i>
                    </div>
                    <div class="um-card-content">
                        <h3 class="um-card-title">إعدادات المستودع</h3>
                        <p class="um-card-description">إدارة إعدادات المستودع والتصنيفات</p>
                    </div>
                    <div class="um-card-arrow">
                        <i class="feather icon-chevron-left"></i>
                    </div>
                </a>
            </div>

            <!-- Reports Card -->
            <div class="um-card um-card-link">
                <a href="{{ route('manufacturing.warehouse-reports.index') }}" class="um-card-link-wrapper">
                    <div class="um-card-icon reports">
                        <i class="feather icon-bar-chart-2"></i>
                    </div>
                    <div class="um-card-content">
                        <h3 class="um-card-title">التقارير والإحصائيات</h3>
                        <p class="um-card-description">تقارير شاملة وإحصائيات متقدمة</p>
                    </div>
                    <div class="um-card-arrow">
                        <i class="feather icon-chevron-left"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <style>
        .um-content-wrapper {
            background: var(--light-color);
            border-radius: var(--border-radius);
            padding: 25px;
            margin: 20px;
            box-shadow: var(--shadow-light);
        }

        .um-header-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .um-page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0 0 15px 0;
        }

        .um-breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: var(--gray-500);
        }

        .um-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .um-card-link {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            transition: all 0.3s ease;
            background: #fff;
            box-shadow: var(--shadow-sm);
        }

        .um-card-link:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
            border-color: var(--primary-color);
        }

        .um-card-link-wrapper {
            display: flex;
            align-items: center;
            padding: 20px;
            text-decoration: none;
            color: inherit;
        }

        .um-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 15px;
            font-size: 20px;
        }

        .um-card-icon.primary {
            background: rgba(0, 102, 178, 0.1);
            color: var(--primary-color);
        }

        .um-card-icon.success {
            background: rgba(39, 174, 96, 0.1);
            color: var(--success-color);
        }

        .um-card-icon.warning {
            background: rgba(243, 156, 18, 0.1);
            color: var(--warning-color);
        }

        .um-card-icon.info {
            background: rgba(52, 152, 219, 0.1);
            color: var(--info-color);
        }

        .um-card-icon.purple {
            background: rgba(156, 39, 176, 0.1);
            color: #9c27b0;
        }

        .um-card-icon.secondary {
            background: rgba(158, 158, 158, 0.1);
            color: #9e9e9e;
        }

        .um-card-icon.reports {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .um-card-content {
            flex: 1;
        }

        .um-card-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 5px 0;
            color: var(--dark-color);
        }

        .um-card-description {
            font-size: 14px;
            color: var(--gray-500);
            margin: 0;
        }

        .um-card-arrow {
            color: var(--gray-400);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .um-content-wrapper {
                margin: 10px;
                padding: 15px;
            }

            .um-grid {
                grid-template-columns: 1fr;
            }

            .um-page-title {
                font-size: 20px;
            }
        }
    </style>
@endsection
