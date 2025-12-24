@extends('master')

@section('title', __('warehouse_registration.warehouse_management'))

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
        
        /* Stats Row */
        .stats-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .stat-item {
            flex: 1;
            min-width: 280px;
            background: white;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0, 81, 229, 0.1);
            border-left: 4px solid #0051E5;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 81, 229, 0.15);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: linear-gradient(135deg, #e8f0ff 0%, #d0e1ff 100%);
            color: #0051E5;
            flex-shrink: 0;
        }

        .stat-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: #999;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #0051E5;
            line-height: 1;
        }

        @media (max-width: 768px) {
            .stat-item {
                min-width: 200px;
            }
        }
        
        /* Filter Form Styles */
        .filter-form {
            padding: 0;
        }

        .filter-form .form-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .filter-form .form-control,
        .filter-form .form-select {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .filter-form .form-control:focus,
        .filter-form .form-select:focus {
            border-color: #0051E5;
            box-shadow: 0 0 0 3px rgba(0, 81, 229, 0.1);
            background-color: white;
        }

        .filter-form .form-control::placeholder {
            color: #adb5bd;
        }

        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(0, 81, 229, 0.3);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
            color: #2c3e50;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background-color: #dee2e6;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }

        .card-header {
            border-bottom: 2px solid rgba(0, 0, 0, 0.1);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #3E4651 0%, #2C3339 100%);
        }

        .border-left-danger {
            border-left: 4px solid #0051E5;
        }

        .border-left-success {
            border-left: 4px solid #3E4651;
        }

        .border-left-info {
            border-left: 4px solid #0051E5;
        }

        .text-danger {
            color: #0051E5 !important;
        }

        .text-success {
            color: #3E4651 !important;
        }

        .text-info {
            color: #0051E5 !important;
        }

        .bg-warning {
            background-color: #e8f0ff !important;
            color: #0051E5 !important;
        }

        .badge-danger {
            background-color: #0051E5 !important;
            color: white !important;
        }

        .bg-success {
            background-color: #3E4651 !important;
            color: white !important;
        }

        .bg-info {
            background-color: #e8f0ff !important;
            color: #0051E5 !important;
        }

        .bg-danger {
            background-color: #ff6b6b !important;
            color: white !important;
        }

        .btn-info {
            background-color: #0051E5;
            border-color: #0051E5;
            color: white;
        }

        .btn-info:hover {
            background-color: #003FA0;
            border-color: #003FA0;
            color: white;
        }

        .btn-success {
            background-color: #0051E5;
            border-color: #0051E5;
            color: white;
        }

        .btn-success:hover {
            background-color: #003FA0;
            border-color: #003FA0;
            color: white;
        }

        .btn-warning {
            background-color: #0051E5;
            border-color: #0051E5;
            color: white;
        }

        .btn-warning:hover {
            background-color: #003FA0;
            border-color: #003FA0;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table thead th {
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem 0.75rem;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .badge {
            font-weight: 500;
        }

        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .table-responsive {
            border-radius: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .filter-form .row {
                gap: 0;
            }

            .filter-form .col-md-2,
            .filter-form .col-md-3 {
                margin-bottom: 15px;
            }

            .stat-item {
                min-width: 200px;
            }
        }
        
        /* Custom badge styles */
        .badge-pending {
            background: #0051E5;
            color: white;
        }
        
        .badge-registered {
            background: #3E4651;
            color: white;
        }
        
        .badge-moved {
            background: #27ae60;
            color: white;
        }
        
        .badge-warning-custom {
            background-color: #e74c3c;
            color: white;
        }
        
        /* Status column styling */
        .status-column {
            min-width: 180px;
        }
        
        /* Info Alert */
        .alert-info-custom {
            border-right: 4px solid #3498db;
            margin-bottom: 20px;
        }
    </style>

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="fas fa-box"></i>
                {{ __('warehouse_registration.warehouse_management') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('app.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('warehouse.warehouse') }}</span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('warehouse_registration.warehouse_registration') }}</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        @if ($errors->any())
            <div class="um-alert-custom um-alert-error" role="alert">
                <i class="feather icon-x-circle"></i>
                <strong>{{ __('app.error') }}!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if (session('success'))
            <div class="um-alert-custom um-alert-success" role="alert">
                <i class="feather icon-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if (session('info'))
            <div class="um-alert-custom um-alert-success" role="alert">
                <i class="feather icon-info"></i>
                {{ session('info') }}
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

        <!-- Info Alert -->
        <div class="alert alert-info alert-info-custom">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg style="width: 24px; height: 24px; min-width: 24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <div>
                    <strong>{{ __('warehouse_registration.warehouse_system') }}:</strong>
                    <span style="display: block; margin-top: 5px; color: #666;">
                        ✅ <strong>{{ __('warehouse_registration.pending') }} (🔴):</strong> {{ __('warehouse_registration.pending_shipments') }}
                        &nbsp;|&nbsp;
                        ✅ <strong>{{ __('warehouse_registration.registered') }} (🟢):</strong> {{ __('warehouse_registration.registered_shipments') }}
                        &nbsp;|&nbsp;
                        ✅ <strong>{{ __('warehouse_registration.moved_to_production') }} (🏭):</strong> {{ __('warehouse_registration.moved_shipments') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-row">
            <div class="stat-item pending-stat">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">{{ __('warehouse_registration.pending_shipments_waiting') }}</span>
                    <span class="stat-number" style="color: #0051E5;">{{ $incomingUnregistered->total() ?? 0 }}</span>
                </div>
            </div>

            <div class="stat-item registered-stat">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">{{ __('warehouse_registration.registered_shipments') }}</span>
                    <span class="stat-number" style="color: #3E4651;">{{ $incomingRegistered->total() ?? 0 }}</span>
                </div>
            </div>

            @if ($movedToProduction->count() > 0)
            <div class="stat-item production-stat">
                <div class="stat-icon">
                    <i class="fas fa-industry"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">{{ __('warehouse_registration.moved_to_production') }}</span>
                    <span class="stat-number" style="color: #0051E5;">{{ $movedToProduction->total() ?? 0 }}</span>
                </div>
            </div>
            @endif
        </div>

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="fas fa-box"></i>
                    {{ __('warehouse_registration.shipments_list') }}
                </h4>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('manufacturing.warehouse.movements.index') }}" class="um-btn um-btn-primary">
                        <i class="fas fa-exchange-alt"></i>
                        {{ __('warehouse_registration.movements_log') }}
                    </a>
                    <a href="{{ route('manufacturing.warehouses.reconciliation.link-invoice') }}" class="um-btn um-btn-primary">
                        <i class="fas fa-link"></i>
                        {{ __('warehouse_registration.link_invoice') }}
                    </a>
                    <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="um-btn um-btn-primary">
                        <i class="fas fa-balance-scale"></i>
                        {{ __('warehouse_registration.reconciliations') }}
                    </a>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET" action="{{ route('manufacturing.warehouse.registration.index') }}" class="filter-form">
                    <div class="um-filter-row">
                        <!-- From Date -->
                        <div class="um-form-group">
                            <label class="form-label" style="color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                                <i class="fas fa-calendar-alt" style="color: #0051E5; margin-left: 5px;"></i> {{ __('warehouse_registration.from_date') }}
                            </label>
                            <input type="date" name="from_date" class="um-form-control"
                                   value="{{ $appliedFilters['from_date'] ?? '' }}">
                        </div>

                        <!-- To Date -->
                        <div class="um-form-group">
                            <label class="form-label" style="color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                                <i class="fas fa-calendar-check" style="color: #0051E5; margin-left: 5px;"></i> {{ __('warehouse_registration.to_date') }}
                            </label>
                            <input type="date" name="to_date" class="um-form-control"
                                   value="{{ $appliedFilters['to_date'] ?? '' }}">
                        </div>

                        <!-- Sort By -->
                        <div class="um-form-group">
                            <label class="form-label" style="color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                                <i class="fas fa-sort" style="color: #0051E5; margin-left: 5px;"></i> {{ __('warehouse_registration.sort_by') }}
                            </label>
                            <select name="sort_by" class="um-form-control">
                                <option value="date" {{ ($appliedFilters['sort_by'] ?? 'date') === 'date' ? 'selected' : '' }}>{{ __('app.date') }}</option>
                                <option value="note_number" {{ ($appliedFilters['sort_by'] ?? 'date') === 'note_number' ? 'selected' : '' }}>{{ __('warehouse_registration.note_number') }}</option>
                            </select>
                        </div>

                        <!-- Sort Order -->
                        <div class="um-form-group">
                            <label class="form-label" style="color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                                <i class="fas fa-arrow-up-down" style="color: #0051E5; margin-left: 5px;"></i> {{ __('warehouse_registration.order') }}
                            </label>
                            <select name="sort_order" class="um-form-control">
                                <option value="desc" {{ ($appliedFilters['sort_order'] ?? 'desc') === 'desc' ? 'selected' : '' }}>{{ __('warehouse_registration.newest_first') }}</option>
                                <option value="asc" {{ ($appliedFilters['sort_order'] ?? 'desc') === 'asc' ? 'selected' : '' }}>{{ __('warehouse_registration.oldest_first') }}</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="fas fa-search"></i>
                                {{ __('app.search') }}
                            </button>
                            <a href="{{ route('manufacturing.warehouse.registration.index') }}" class="um-btn um-btn-outline">
                                <i class="fas fa-redo"></i>
                                {{ __('app.reset') }}
                            </a>
                        </div>
                    </div>

                    <!-- Filter Info -->
                    @if (($appliedFilters['from_date'] ?? null) || ($appliedFilters['to_date'] ?? null))
                        <div style="margin-top: 12px; padding: 10px 12px; background-color: #e8f0ff; border-radius: 6px; border-right: 4px solid #0051E5;">
                            <small style="color: #0051E5; font-weight: 500;">
                                <i class="fas fa-info-circle"></i>
                                {{ __('app.filter_applied') }}:
                                @if ($appliedFilters['from_date'])
                                    {{ __('app.from') }} <strong>{{ date('Y-m-d', strtotime($appliedFilters['from_date'])) }}</strong>
                                @endif
                                @if ($appliedFilters['to_date'])
                                    {{ __('app.to') }} <strong>{{ date('Y-m-d', strtotime($appliedFilters['to_date'])) }}</strong>
                                @endif
                            </small>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Combined Table for All Shipments -->
            <div class="um-table-responsive">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>{{ __('warehouse_registration.note_number') }}</th>
                            <th>{{ __('warehouse_registration.supplier') }}</th>
                            <th>{{ __('warehouse_registration.created_date') }}</th>
                            <th class="status-column">{{ __('app.status') }}</th>
                            <th>{{ __('warehouse_registration.registered_quantity') }}</th>
                            <th>{{ __('warehouse_registration.remaining_quantity') }}</th>
                            <th>{{ __('warehouse_registration.registered_by') }}</th>
                            <th>{{ __('warehouse_registration.registration_date') }}</th>
                            <th>{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Unregistered Shipments -->
                        @forelse($incomingUnregistered as $shipment)
                        <tr>
                            <td>{{ $shipment->note_number ?? $shipment->id }}</td>
                            <td>{{ $shipment->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $shipment->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="status-column">
                                <span class="um-badge badge-pending">
                                    <i class="fas fa-hourglass-half"></i> {{ __('warehouse_registration.pending') }}
                                </span>
                                @if ($shipment->registration_attempts > 0)
                                    <span class="um-badge badge-warning-custom">
                                        {{ __('warehouse_registration.attempt') }} {{ $shipment->registration_attempts + 1 }}
                                    </span>
                                @endif
                            </td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>
                                <a href="{{ route('manufacturing.warehouse.registration.create', $shipment) }}"
                                   class="um-btn um-btn-primary" style="padding: 4px 8px; font-size: 12px;">
                                    <i class="fas fa-edit"></i> {{ __('warehouse_registration.register') }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        @endforelse

                        <!-- Registered Shipments -->
                        @forelse($incomingRegistered as $shipment)
                            @php
                                // حساب الكمية المتبقية بشكل صحيح
                                $registeredQuantity = $shipment->quantity ?? 0;
                                $transferredQuantity = $shipment->quantity_used ?? 0;
                                $remainingQuantity = $shipment->quantity_remaining ?? ($registeredQuantity - $transferredQuantity);
                            @endphp
                            <tr>
                                <td>{{ $shipment->note_number ?? $shipment->id }}</td>
                                <td>{{ $shipment->supplier->name ?? 'N/A' }}</td>
                                <td>{{ $shipment->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="status-column">
                                    <span class="um-badge badge-registered">
                                        <i class="fas fa-check-circle"></i> {{ __('warehouse_registration.registered') }}
                                    </span>
                                    @if ($shipment->registration_attempts > 0)
                                        <span class="um-badge badge-warning-custom">
                                            {{ __('warehouse_registration.attempt') }} {{ $shipment->registration_attempts + 1 }}
                                        </span>
                                    @endif
                                    @if($remainingQuantity > 0)
                                        <span class="um-badge" style="background-color: #004B87; color: white; margin-top: 5px; display: inline-block;">
                                            {{ __('warehouse_registration.available') }}: {{ number_format($remainingQuantity, 2) }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ number_format($registeredQuantity, 2) }}</td>
                                <td>{{ number_format($remainingQuantity, 2) }}</td>
                                <td>{{ $shipment->registeredBy->name ?? 'N/A' }}</td>
                                <td>{{ $shipment->registered_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('manufacturing.warehouse.registration.show', $shipment) }}"
                                       class="um-btn um-btn-primary" style="padding: 4px 8px; font-size: 12px;">
                                        <i class="fas fa-eye"></i> {{ __('app.view') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                        @endforelse

                        <!-- Moved to Production Shipments -->
                        @forelse($movedToProduction as $shipment)
                            <tr>
                                <td>{{ $shipment->note_number ?? $shipment->id }}</td>
                                <td>{{ $shipment->supplier->name ?? 'N/A' }}</td>
                                <td>{{ $shipment->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="status-column">
                                    <span class="um-badge badge-moved">
                                        <i class="fas fa-industry"></i> {{ __('warehouse_registration.moved_to_production') }}
                                    </span>
                                    @if ($shipment->registration_attempts > 0)
                                        <span class="um-badge" style="background-color: #0051E5; color: white; margin-top: 5px; display: inline-block;">
                                            {{ __('warehouse_registration.attempts_count') }} {{ $shipment->registration_attempts }} {{ __('warehouse_registration.attempts') }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ number_format($shipment->quantity ?? 0, 2) }}</td>
                                <td>0.00</td>
                                <td>{{ $shipment->registeredBy->name ?? 'N/A' }}</td>
                                <td>{{ $shipment->registered_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('manufacturing.warehouse.registration.show', $shipment) }}"
                                       class="um-btn um-btn-primary" style="padding: 4px 8px; font-size: 12px;">
                                        <i class="fas fa-eye"></i> {{ __('app.view') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                        @endforelse

                        @if($incomingUnregistered->isEmpty() && $incomingRegistered->isEmpty() && $movedToProduction->isEmpty())
                        <tr>
                            <td colspan="9" class="text-center">{{ __('warehouse_registration.no_shipments') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($incomingUnregistered->hasPages() || $incomingRegistered->hasPages())
                <div class="um-pagination-section">
                    <div>
                        <p class="um-pagination-info">
                            {{ __('app.showing') }} {{ $incomingUnregistered->firstItem() ?? $incomingRegistered->firstItem() ?? 0 }} {{ __('app.to') }} {{ $incomingUnregistered->lastItem() ?? $incomingRegistered->lastItem() ?? 0 }} {{ __('app.of') }}
                            {{ $incomingUnregistered->total() + $incomingRegistered->total() + $movedToProduction->count() }} {{ __('warehouse_registration.shipments') }}
                        </p>
                    </div>
                    <div>
                        @if($incomingUnregistered->hasPages())
                            {{ $incomingUnregistered->links('pagination::bootstrap-4') }}
                        @elseif($incomingRegistered->hasPages())
                            {{ $incomingRegistered->links('pagination::bootstrap-4') }}
                        @endif
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