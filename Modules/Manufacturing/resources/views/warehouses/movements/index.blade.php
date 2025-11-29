@extends('master')

@section('title', __('warehouse.movements_log'))

@section('content')
    <style>
        /* Pagination Styling */
        .um-pagination-section {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px 0;
            border-top: 1px solid #e9ecef;
        }

        .um-pagination-info {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }

        /* Bootstrap Pagination Custom Styling */
        .pagination {
            margin: 0;
            gap: 5px;
            display: flex;
            flex-wrap: wrap;
        }

        .pagination .page-link {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            color: #3498db;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 500;
            background-color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 36px;
            text-align: center;
            cursor: pointer;
        }

        .pagination .page-link:hover {
            background-color: #f0f2f5;
            border-color: #3498db;
            color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.15);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-color: #2980b9;
            color: white;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            border-color: #dee2e6;
            background-color: #f8f9fa;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .pagination .page-item.disabled .page-link:hover {
            transform: none;
            box-shadow: none;
        }

        @media (max-width: 768px) {
            .um-pagination-section {
                flex-direction: column;
                align-items: stretch;
            }

            .um-pagination-info {
                text-align: center;
            }

            .pagination {
                justify-content: center;
            }

            .pagination .page-link {
                padding: 6px 10px;
                font-size: 12px;
                min-width: 32px;
            }
        }
    </style>

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="fas fa-exchange-alt"></i>
                {{ __('warehouse.movements_log') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('warehouse.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('warehouse.warehouse_management') }}</span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('warehouse.movement_history') }}</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        @if (session('success'))
            <div class="um-alert-custom um-alert-success" role="alert">
                <i class="feather icon-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="um-alert-custom um-alert-error" role="alert">
                <i class="feather icon-x-circle"></i>
                {{ session('error') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="fas fa-list"></i>
                    {{ __('warehouse.movements_list') }}
                </h4>
                <a href="{{ route('manufacturing.warehouse.registration.pending') }}" class="um-btn um-btn-primary">
                    <i class="fas fa-arrow-right"></i>
                    {{ __('warehouse.back') }}
                </a>
            </div>

            <!-- Statistics Cards -->


            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET" action="{{ route('manufacturing.warehouse.movements.index') }}">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <select name="movement_type" class="um-form-control">
                                <option value="">{{ __('warehouse.all_movement_types') }}</option>
                                <option value="incoming" {{ request('movement_type') == 'incoming' ? 'selected' : '' }}>{{ __('warehouse.movement_incoming') }}</option>
                                <option value="to_production" {{ request('movement_type') == 'to_production' ? 'selected' : '' }}>{{ __('warehouse.movement_to_production') }}</option>
                                <option value="adjustment" {{ request('movement_type') == 'adjustment' ? 'selected' : '' }}>{{ __('warehouse.movement_adjustment') }}</option>
                                <option value="reconciliation" {{ request('movement_type') == 'reconciliation' ? 'selected' : '' }}>{{ __('warehouse.movement_reconciliation_update') }}</option>
                                <option value="transfer" {{ request('movement_type') == 'transfer' ? 'selected' : '' }}>{{ __('warehouse.movement_transfer_between_warehouses') }}</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="source" class="um-form-control">
                                <option value="">{{ __('warehouse.all_sources') }}</option>
                                <option value="registration" {{ request('source') == 'registration' ? 'selected' : '' }}>{{ __('warehouse.source_goods_registration') }}</option>
                                <option value="reconciliation" {{ request('source') == 'reconciliation' ? 'selected' : '' }}>{{ __('warehouse.source_reconciliation') }}</option>
                                <option value="production" {{ request('source') == 'production' ? 'selected' : '' }}>{{ __('warehouse.source_production') }}</option>
                                <option value="transfer" {{ request('source') == 'transfer' ? 'selected' : '' }}>{{ __('warehouse.source_transfer') }}</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="warehouse_id" class="um-form-control">
                                <option value="">{{ __('warehouse.all_warehouses') }}</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('warehouse.search') }}
                            </button>
                            <a href="{{ route('manufacturing.warehouse.movements.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                {{ __('warehouse.reset') }}
                            </a>
                        </div>
                    </div>
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="date" name="from_date" class="um-form-control" placeholder="{{ __('warehouse.from_date') }}" value="{{ request('from_date') }}">
                        </div>
                        <div class="um-form-group">
                            <input type="date" name="to_date" class="um-form-control" placeholder="{{ __('warehouse.to_date') }}" value="{{ request('to_date') }}">
                        </div>
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="{{ __('warehouse.search_movements') }}" value="{{ request('search') }}">
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>

                            <th>{{ __('warehouse.movement_number') }}</th>
                            <th>{{ __('warehouse.date') }}</th>
                            <th>{{ __('warehouse.type') }}</th>
                            <th>{{ __('warehouse.source') }}</th>
                            <th>{{ __('warehouse.material') }}</th>
                            <th>{{ __('warehouse.quantity') }}</th>
                            <th>{{ __('warehouse.from') }}</th>
                            <th>{{ __('warehouse.to') }}</th>
                            <th>{{ __('warehouse.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                        <tr>

                            <td>
                                <strong class="text-primary">{{ $movement->movement_number }}</strong>
                            </td>
                            <td>
                                <small>{{ $movement->movement_date->format('Y-m-d H:i') }}</small>
                            </td>
                            <td>
                                <span class="um-badge {{
                                    $movement->movement_type == 'incoming' ? 'um-badge-success' :
                                    ($movement->movement_type == 'to_production' ? 'um-badge-info' :
                                    ($movement->movement_type == 'adjustment' || $movement->movement_type == 'reconciliation' ? 'um-badge-warning' : 'um-badge-secondary'))
                                }}">
                                    {{ $movement->movement_type_name }}
                                </span>
                            </td>
                            <td>
                                <span class="um-badge um-badge-info">
                                    {{ $movement->source_name }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $movement->material->name_ar ?? __('warehouse.not_specified') }}</strong>
                            </td>
                            <td>
                                <span class="um-badge um-badge-secondary">
                                    {{ number_format($movement->quantity, 2) }}
                                    {{ $movement->unit->symbol_ar ?? '' }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $movement->fromWarehouse->name_ar ?? ($movement->supplier->name ?? '-') }}</small>
                            </td>
                            <td>
                                <small>{{ $movement->toWarehouse->name_ar ?? $movement->destination ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.warehouse.movements.show', $movement) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>{{ __('warehouse.view') }}</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">{{ __('warehouse.no_movements_found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile View -->
            <div class="um-mobile-view">
                @forelse($movements as $movement)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <h5>{{ $movement->movement_number }}</h5>
                        </div>
                        <span class="um-badge {{
                            $movement->movement_type == 'incoming' ? 'um-badge-success' :
                            ($movement->movement_type == 'to_production' ? 'um-badge-info' :
                            ($movement->movement_type == 'adjustment' || $movement->movement_type == 'reconciliation' ? 'um-badge-warning' : 'um-badge-secondary'))
                        }}">
                            {{ $movement->movement_type_name }}
                        </span>
                    </div>
                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('warehouse.date') }}:</span>
                            <span class="um-info-value">{{ $movement->movement_date->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('warehouse.source') }}:</span>
                            <span class="um-info-value">{{ $movement->source_name }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('warehouse.material') }}:</span>
                            <span class="um-info-value">{{ $movement->material->name_ar ?? __('warehouse.not_specified') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('warehouse.quantity') }}:</span>
                            <span class="um-info-value">{{ number_format($movement->quantity, 2) }} {{ $movement->unit->symbol_ar ?? '' }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('warehouse.from') }}:</span>
                            <span class="um-info-value">{{ $movement->fromWarehouse->name_ar ?? ($movement->supplier->name ?? '-') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('warehouse.to') }}:</span>
                            <span class="um-info-value">{{ $movement->toWarehouse->name_ar ?? $movement->destination ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                <a href="{{ route('manufacturing.warehouse.movements.show', $movement) }}" class="um-dropdown-item um-btn-view">
                                    <i class="feather icon-eye"></i>
                                    <span>{{ __('warehouse.view') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center">{{ __('warehouse.no_movements_found') }}</div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($movements->hasPages())
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        {{ __('warehouse.showing') }} {{ $movements->firstItem() ?? 0 }} {{ __('warehouse.to') }} {{ $movements->lastItem() ?? 0 }} {{ __('warehouse.of') }}
                        {{ $movements->total() }} {{ __('warehouse.movement') }}
                    </p>
                </div>
                <div>
                    {{ $movements->links('pagination::bootstrap-4') }}
                </div>
            </div>
            @endif
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.um-alert-custom');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.3s';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 300);
                }, 5000);
            });
        });
    </script>
@endsection
