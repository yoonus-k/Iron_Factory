@extends('master')

@section('title', __('warehouse.warehouse_settings'))

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-settings"></i>
                {{ __('warehouse.warehouse_settings') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('warehouse.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('warehouse.warehouse_settings') }}</span>
            </nav>
        </div>

        <!-- Settings Cards -->
        <div class="um-grid">

            @if(auth()->user()->hasPermission('WAREHOUSE_UNITS_READ'))
            <div class="um-category-card">
                <div class="um-category-card-header">
                    <div class="um-category-info">
                        <div class="um-category-icon" style="background: #4caf5020; color: #4caf50;">
                            <i class="feather icon-box"></i>
                        </div>
                        <div>
                            <h6 class="um-category-name">{{ __('warehouse.units') }}</h6>
                        </div>
                    </div>
                </div>

                <div class="um-category-card-body">
                    <div class="um-info-row">
                        <span class="um-info-value">{{ __('warehouse.manage_measurement_units') }}</span>
                    </div>
                </div>

                <div class="um-category-card-footer">
                    <a href="{{ route('manufacturing.warehouse-settings.units.index') }}" class="um-btn um-btn-primary">
                        <i class="feather icon-arrow-right"></i>
                        {{ __('warehouse.go') }}
                    </a>
                </div>
            </div>
            @endif

            @if(auth()->user()->hasPermission('WAREHOUSE_MATERIAL_TYPES_READ'))
            <div class="um-category-card">
                <div class="um-category-card-header">
                    <div class="um-category-info">
                        <div class="um-category-icon" style="background: #ff980020; color: #ff9800;">
                            <i class="feather icon-layers"></i>
                        </div>
                        <div>
                            <h6 class="um-category-name">{{ __('warehouse.material_types') }}</h6>
                        </div>
                    </div>
                </div>

                <div class="um-category-card-body">
                    <div class="um-info-row">
                        <span class="um-info-value">{{ __('warehouse.manage_material_types_and_categories') }}</span>
                    </div>
                </div>

                <div class="um-category-card-footer">
                    <a href="{{ route('manufacturing.warehouse-settings.material-types.index') }}" class="um-btn um-btn-primary">
                        <i class="feather icon-arrow-right"></i>
                        {{ __('warehouse.go') }}
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <style>
        .um-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .um-category-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .um-category-card-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .um-category-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .um-category-icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .um-category-name {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .um-category-card-body {
            padding: 20px;
        }

        .um-info-row {
            margin-bottom: 10px;
        }

        .um-info-value {
            color: #4a5568;
        }

        .um-category-card-footer {
            padding: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: left;
        }

        .um-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
        }

        .um-btn-primary {
            background: #3f51b5;
            color: white;
        }

        .um-btn-primary:hover {
            background: #303f9f;
        }
    </style>
@endsection
