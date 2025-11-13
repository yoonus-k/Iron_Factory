@extends('master')

@section('title', 'إدارة المستودعات')

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-home"></i>
                إدارة المستودعات
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المستودعات</span>
            </nav>
        </div>

        <!-- Warehouse Dashboard Cards -->
        <div class="um-grid">
            <!-- Products Card -->
            <div class="um-card um-card-link">
                <a href="{{ route('manufacturing.warehouse-products.index') }}" class="um-card-link-wrapper">
                    <div class="um-card-icon primary">
                        <i class="feather icon-package"></i>
                    </div>
                    <div class="um-card-content">
                        <h3 class="um-card-title">المنتجات</h3>
                        <p class="um-card-description">إدارة المواد الخام والمنتجات في المستودع</p>
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
        </div>
    </div>

    <style>
        .um-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .um-card-link {
            border: 1px solid #e1e5eb;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: #fff;
        }

        .um-card-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: #3f51b5;
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
            background: rgba(63, 81, 181, 0.1);
            color: #3f51b5;
        }

        .um-card-icon.success {
            background: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }

        .um-card-icon.warning {
            background: rgba(255, 152, 0, 0.1);
            color: #ff9800;
        }

        .um-card-icon.info {
            background: rgba(33, 150, 243, 0.1);
            color: #2196f3;
        }

        .um-card-icon.purple {
            background: rgba(156, 39, 176, 0.1);
            color: #9c27b0;
        }

        .um-card-icon.secondary {
            background: rgba(158, 158, 158, 0.1);
            color: #9e9e9e;
        }

        .um-card-content {
            flex: 1;
        }

        .um-card-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 5px 0;
        }

        .um-card-description {
            font-size: 14px;
            color: #666;
            margin: 0;
        }

        .um-card-arrow {
            color: #9e9e9e;
        }
    </style>
@endsection