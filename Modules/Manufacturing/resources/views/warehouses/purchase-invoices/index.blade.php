@extends('master')

@section('title', __('warehouse.purchase_invoices_management'))

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-file-text"></i>
                {{ __('warehouse.purchase_invoices_management') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('warehouse.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('warehouse.warehouse_management') }}</span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('warehouse.purchase_invoices') }}</span>
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
                    <i class="feather icon-list"></i>
                    {{ __('warehouse.purchase_invoices_list') }}
                </h4>
                @if (auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_CREATE'))
                    <a href="{{ route('manufacturing.purchase-invoices.create') }}" class="um-btn um-btn-primary">
                        <i class="feather icon-plus"></i>
                        {{ __('warehouse.add_new_purchase_invoice') }}
                    </a>
                @endif
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET" action="{{ route('manufacturing.purchase-invoices.index') }}">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="{{ __('warehouse.search_invoices') }}" value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <input type="text" name="invoice_number" class="um-form-control" placeholder="{{ __('warehouse.invoice_number') }}" value="{{ request('invoice_number') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="supplier_id" class="um-form-control">
                                <option value="">{{ __('warehouse.all_suppliers') }}</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">{{ __('warehouse.all_statuses') }}</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('warehouse.status_draft') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('warehouse.status_pending') }}</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('warehouse.status_approved') }}</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('warehouse.status_paid') }}</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('warehouse.status_rejected') }}</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('warehouse.search') }}
                            </button>
                            <a href="{{ route('manufacturing.purchase-invoices.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                {{ __('warehouse.reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>

                            <th>{{ __('warehouse.invoice_number') }}</th>
                            <th>{{ __('warehouse.supplier') }}</th>
                            <th>{{ __('warehouse.invoice_date') }}</th>
                            <th>{{ __('warehouse.amount') }}</th>
                            <th>{{ __('warehouse.status') }}</th>
                            <th>{{ __('warehouse.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        <tr>

                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                            <td>{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</td>
                            <td>
                                @php
                                    $statusColor = $invoice->status->color();
                                    $bgColor = $statusColor === 'yellow' ? '#fff3cd' : (
                                        $statusColor === 'green' ? '#d4edda' : (
                                        $statusColor === 'red' ? '#f8d7da' : (
                                        $statusColor === 'blue' ? '#d1ecf1' : '#e2e3e5'
                                    )));
                                    $textColor = $statusColor === 'yellow' ? '#856404' : (
                                        $statusColor === 'green' ? '#155724' : (
                                        $statusColor === 'red' ? '#721c24' : (
                                        $statusColor === 'blue' ? '#0c5460' : '#383d41'
                                    )));
                                @endphp
                                <span class="um-badge" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                    {{ $invoice->status->label() }}
                                </span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="{{ __('warehouse.actions') }}">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        @if (auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_READ'))
                                            <a href="{{ route('manufacturing.purchase-invoices.show', $invoice->id) }}" class="um-dropdown-item um-btn-view">
                                                <i class="feather icon-eye"></i>
                                                <span>{{ __('warehouse.view') }}</span>
                                            </a>
                                        @endif
                                        @if (auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_UPDATE'))
                                            <a href="{{ route('manufacturing.purchase-invoices.edit', $invoice->id) }}" class="um-dropdown-item um-btn-edit">
                                                <i class="feather icon-edit-2"></i>
                                                <span>{{ __('warehouse.edit') }}</span>
                                            </a>
                                        @endif
                                        @if (auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_DELETE'))
                                            <form action="{{ route('manufacturing.purchase-invoices.destroy', $invoice->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="um-dropdown-item um-btn-delete" title="{{ __('warehouse.delete') }}" onclick="return confirm('{{ __('warehouse.confirm_delete') }}?')">
                                                    <i class="feather icon-trash-2"></i>
                                                    <span>{{ __('warehouse.delete') }}</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">{{ __('warehouse.no_purchase_invoices') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile View -->
            <div class="um-mobile-view">
                @forelse($invoices as $invoice)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <h5>{{ $invoice->invoice_number }}</h5>
                            <p>{{ $invoice->supplier->name ?? 'N/A' }}</p>
                        </div>
                        @php
                            $statusColor = $invoice->status->color();
                            $bgColor = $statusColor === 'yellow' ? '#fff3cd' : (
                                $statusColor === 'green' ? '#d4edda' : (
                                $statusColor === 'red' ? '#f8d7da' : (
                                $statusColor === 'blue' ? '#d1ecf1' : '#e2e3e5'
                            )));
                            $textColor = $statusColor === 'yellow' ? '#856404' : (
                                $statusColor === 'green' ? '#155724' : (
                                $statusColor === 'red' ? '#721c24' : (
                                $statusColor === 'blue' ? '#0c5460' : '#383d41'
                            )));
                        @endphp
                        <span class="um-badge" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                            {{ $invoice->status->label() }}
                        </span>
                    </div>
                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span>{{ __('warehouse.invoice_date') }}:</span>
                            <span>{{ $invoice->invoice_date->format('Y-m-d') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span>{{ __('warehouse.amount') }}:</span>
                            <span>{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</span>
                        </div>
                        @if($invoice->due_date)
                        <div class="um-info-row">
                            <span>{{ __('warehouse.due_date') }}:</span>
                            <span>{{ $invoice->due_date->format('Y-m-d') }}
                                @if($invoice->isOverdue())
                                    <span class="um-badge um-badge-danger">{{ __('warehouse.overdue') }}</span>
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>
                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="{{ __('warehouse.actions') }}">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                @if (auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_READ'))
                                    <a href="{{ route('manufacturing.purchase-invoices.show', $invoice->id) }}" class="um-dropdown-item um-btn-view">
                                        <i class="feather icon-eye"></i>
                                        <span>{{ __('warehouse.view') }}</span>
                                    </a>
                                @endif
                                @if (auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_UPDATE'))
                                    <a href="{{ route('manufacturing.purchase-invoices.edit', $invoice->id) }}" class="um-dropdown-item um-btn-edit">
                                        <i class="feather icon-edit-2"></i>
                                        <span>{{ __('warehouse.edit') }}</span>
                                    </a>
                                @endif
                                @if (auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_DELETE'))
                                    <form action="{{ route('manufacturing.purchase-invoices.destroy', $invoice->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="um-dropdown-item um-btn-delete" title="{{ __('warehouse.delete') }}" onclick="return confirm('{{ __('warehouse.confirm_delete') }}?')">
                                            <i class="feather icon-trash-2"></i>
                                            <span>{{ __('warehouse.delete') }}</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center">{{ __('warehouse.no_purchase_invoices') }}</div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($invoices->hasPages())
                <div class="um-pagination-section">
                    <div>
                        <p class="um-pagination-info">
                            {{ __('warehouse.showing') }} {{ $invoices->firstItem() ?? 0 }} {{ __('warehouse.to') }} {{ $invoices->lastItem() ?? 0 }} {{ __('warehouse.of') }}
                            {{ $invoices->total() }} {{ __('warehouse.purchase_invoice') }}
                        </p>
                    </div>
                    <div>
                        {{ $invoices->links() }}
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
