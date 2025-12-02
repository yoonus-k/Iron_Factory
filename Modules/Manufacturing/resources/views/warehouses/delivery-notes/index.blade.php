@extends('master')

@section('title', __('delivery_notes.delivery_notes'))

@section('content')
    <style>
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

        .status-column {
            min-width: 180px;
        }
    </style>

    <div class="um-content-wrapper">
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="fas fa-box"></i>
                {{ __('delivery_notes.delivery_notes') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('delivery_notes.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('delivery_notes.warehouse') }}</span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('delivery_notes.delivery_notes') }}</span>
            </nav>
        </div>

        @if ($errors->any())
            <div class="um-alert-custom um-alert-error" role="alert">
                <i class="feather icon-x-circle"></i>
                <strong>❌ {{ __('delivery_notes.data_error') }}</strong>
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

        <div class="stats-row">
            <div class="stat-item pending-stat">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">🔴 {{ __('delivery_notes.pending_shipments') }}</span>
                    <span class="stat-number" style="color: #0051E5;">{{ $incomingUnregistered ?? 0 }}</span>
                </div>
            </div>

            <div class="stat-item registered-stat">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">🟢 {{ __('delivery_notes.registered_shipments') }}</span>
                    <span class="stat-number" style="color: #3E4651;">{{ $incomingRegistered ?? 0 }}</span>
                </div>
            </div>

            @if ($movedToProduction > 0)
            <div class="stat-item production-stat">
                <div class="stat-icon">
                    <i class="fas fa-industry"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">🏭 {{ __('delivery_notes.moved_to_production') }}</span>
                    <span class="stat-number" style="color: #0051E5;">{{ $movedToProduction ?? 0 }}</span>
                </div>
            </div>
            @endif
        </div>

        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="fas fa-box"></i>
                    {{ __('delivery_notes.management') }}
                </h4>
                <div style="display: flex; gap: 10px;">
                    @if (auth()->user()->hasPermission('WAREHOUSE_MOVEMENTS_DETAILS'))
                        <a href="{{ route('manufacturing.warehouse.movements.index') }}" class="um-btn um-btn-primary">
                            <i class="fas fa-exchange-alt"></i>
                            {{ __('delivery_notes.movements_log') }}
                        </a>
                    @endif
                    @if (auth()->user()->hasPermission('WAREHOUSE_RECONCILIATION_LINK_INVOICE'))
                        <a href="{{ route('manufacturing.warehouses.reconciliation.link-invoice') }}" class="um-btn um-btn-primary">
                            <i class="fas fa-link"></i>
                            {{ __('delivery_notes.link_invoice') }}
                        </a>
                    @endif
                    @if (auth()->user()->hasPermission('WAREHOUSE_RECONCILIATION_READ'))
                        <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="um-btn um-btn-primary">
                            <i class="fas fa-balance-scale"></i>
                            {{ __('delivery_notes.reconciliations') }}
                        </a>
                    @endif
                    @if (auth()->user()->hasPermission('WAREHOUSE_DELIVERY_NOTES_CREATE'))
                        <a href="{{ route('manufacturing.delivery-notes.create') }}" class="um-btn um-btn-primary">
                            <i class="fas fa-balance-scale"></i>
                            {{ __('delivery_notes.add_delivery_note') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="um-filters-section">
                <form method="GET" action="{{ route('manufacturing.delivery-notes.index') }}" class="filter-form">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="{{ __('delivery_notes.search_placeholder') }}"
                                value="{{ request('search') }}">
                        </div>

                        <div class="um-form-group">
                            <select name="type" class="um-form-control">
                                <option value="">{{ __('delivery_notes.select_type') }}</option>
                                <option value="incoming" {{ request('type') == 'incoming' ? 'selected' : '' }}>{{ __('delivery_notes.incoming') }}</option>
                                <option value="outgoing" {{ request('type') == 'outgoing' ? 'selected' : '' }}>{{ __('delivery_notes.outgoing') }}</option>
                            </select>
                        </div>

                        <div class="um-form-group">
                            <input type="date" name="from_date" class="um-form-control"
                                   value="{{ request('from_date') }}">
                        </div>

                        <div class="um-form-group">
                            <input type="date" name="to_date" class="um-form-control"
                                   value="{{ request('to_date') }}">
                        </div>

                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('delivery_notes.search') }}
                            </button>
                            <a href="{{ route('manufacturing.delivery-notes.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-refresh-cw"></i>
                                {{ __('delivery_notes.reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="um-table-responsive">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>{{ __('delivery_notes.type') }}</th>
                            <th>{{ __('delivery_notes.note_number') }}</th>
                            <th>{{ __('delivery_notes.material_destination') }}</th>
                            <th>{{ __('delivery_notes.supplier_source') }}</th>
                            <th>{{ __('delivery_notes.quantity') }}</th>
                            <th>{{ __('delivery_notes.creation_date') }}</th>
                            <th class="status-column">{{ __('delivery_notes.status') }}</th>
                            <th>{{ __('delivery_notes.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveryNotes as $note)
                            <tr>
                                <td>
                                    @if($note->type === 'incoming')
                                        <span class="badge" style="background-color: #0051E5; color: white;">
                                            <i class="fas fa-arrow-down"></i> {{ __('delivery_notes.incoming') }}
                                        </span>
                                    @else
                                        <span class="badge" style="background-color: #3E4651; color: white;">
                                            <i class="fas fa-arrow-up"></i> {{ __('delivery_notes.outgoing') }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <strong>{{ $note->note_number ?? $note->id }}</strong>
                                </td>

                                <td>
                                    @if($note->type === 'incoming' && $note->material)
                                        <strong>{{ $note->material->name_ar }}</strong><br>
                                        @if($note->material->name_en)
                                            <small class="text-muted">{{ $note->material->name_en }}</small>
                                        @endif
                                    @elseif($note->type === 'outgoing' && $note->destination)
                                        <strong>{{ $note->destination->name ?? 'N/A' }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    @if($note->type === 'incoming' && $note->supplier)
                                        {{ $note->supplier->name }}
                                    @elseif($note->type === 'outgoing')
                                        <span class="text-muted">{{ __('delivery_notes.warehouse') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    @if($note->type === 'incoming')
                                        {{ number_format($note->quantity, 2) }}
                                        @if($note->material && $note->material->materialDetails->first())
                                            {{ $note->material->materialDetails->first()->unit->unit_name ?? '' }}
                                        @endif
                                    @else
                                        {{ number_format($note->delivery_quantity, 2) }}
                                        @if($note->material && $note->material->materialDetails->first())
                                            {{ $note->material->materialDetails->first()->unit->unit_name ?? '' }}
                                        @endif
                                    @endif
                                </td>

                                <td>
                                    <small>{{ $note->created_at->format('Y-m-d H:i') }}</small>
                                </td>

                                <td class="status-column">
                                    @if($note->type === 'incoming')
                                        @if($note->registration_status === 'not_registered')
                                            <span class="um-badge badge-pending">
                                                <i class="fas fa-hourglass-half"></i> {{ __('delivery_notes.pending') }}
                                            </span>
                                        @elseif($note->registration_status === 'in_production' || $note->quantity_remaining <= 0)
                                            <span class="um-badge badge-moved">
                                                <i class="fas fa-industry"></i> {{ __('delivery_notes.moved') }}
                                            </span>
                                        @else
                                            <span class="um-badge badge-registered">
                                                <i class="fas fa-check-circle"></i> {{ __('delivery_notes.registered') }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="um-badge badge-registered">
                                            <i class="fas fa-check-circle"></i> {{ __('delivery_notes.outgoing') }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="um-dropdown">
                                        <button class="um-btn-dropdown" type="button">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="um-dropdown-menu">
                                            @if (auth()->user()->hasPermission('WAREHOUSE_DELIVERY_NOTES_READ'))
                                                <a href="{{ route('manufacturing.delivery-notes.show', $note->id) }}" class="um-dropdown-item um-btn-view">
                                                    <i class="feather icon-eye"></i>
                                                    <span>{{ __('delivery_notes.view') }}</span>
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermission('WAREHOUSE_DELIVERY_NOTES_UPDATE'))
                                                <a href="{{ route('manufacturing.delivery-notes.edit', $note->id) }}" class="um-dropdown-item um-btn-edit">
                                                    <i class="feather icon-edit-2"></i>
                                                    <span>{{ __('delivery_notes.edit') }}</span>
                                                </a>
                                            @endif
                                            @if (auth()->user()->hasPermission('WAREHOUSE_DELIVERY_NOTES_DELETE'))
                                                <form method="POST" action="{{ route('manufacturing.delivery-notes.destroy', $note->id) }}" style="display: inline;" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="um-dropdown-item um-btn-delete" style="width: 100%; text-align: right; border: none; background: none; cursor: pointer;">
                                                        <i class="feather icon-trash-2"></i>
                                                        <span>{{ __('delivery_notes.delete') }}</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    <i class="feather icon-inbox"></i> {{ __('delivery_notes.no_delivery_notes') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($deliveryNotes->hasPages())
                <div class="um-pagination-section">
                    <div>
                        <p class="um-pagination-info">
                            {{ __('delivery_notes.showing') }} {{ $deliveryNotes->firstItem() }} {{ __('delivery_notes.to') }} {{ $deliveryNotes->lastItem() }} {{ __('delivery_notes.of') }}
                            {{ $deliveryNotes->total() }} {{ __('delivery_notes.delivery_notes_total') }}
                        </p>
                    </div>
                    <div>
                        {{ $deliveryNotes->links('pagination::bootstrap-4') }}
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

            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: '{{ __('delivery_notes.confirm_delete') }}',
                        text: '{{ __('delivery_notes.confirm_delete_message') }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '{{ __('delivery_notes.yes_delete') }}',
                        cancelButtonText: '{{ __('delivery_notes.cancel') }}',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            document.querySelectorAll('.um-btn-dropdown').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const dropdown = this.closest('.um-dropdown');
                    const menu = dropdown.querySelector('.um-dropdown-menu');

                    document.querySelectorAll('.um-dropdown-menu').forEach(d => {
                        if (d !== menu) {
                            d.classList.remove('show');
                        }
                    });

                    menu.classList.toggle('show');
                });
            });

            document.addEventListener('click', function() {
                document.querySelectorAll('.um-dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            });

            document.querySelectorAll('.um-dropdown-menu').forEach(menu => {
                menu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });
        });
    </script>
@endsection