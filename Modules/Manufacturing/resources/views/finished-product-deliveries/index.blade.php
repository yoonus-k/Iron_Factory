@extends('master')

@section('title', __('app.finished_products.delivery_notes'))

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

        .badge-approved {
            background: #3E4651;
            color: white;
        }

        .badge-rejected {
            background: #e74c3c;
            color: white;
        }

        .badge-completed {
            background: #27ae60;
            color: white;
        }

        .status-column {
            min-width: 180px;
        }
    </style>

    <div class="um-content-wrapper">
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="fas fa-box-open"></i>
                {{ __('app.finished_products.delivery_notes') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('app.menu.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('app.menu.finished_products') }}</span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('app.finished_products.delivery_notes') }}</span>
            </nav>
        </div>

        @if ($errors->any())
            <div class="um-alert-custom um-alert-error" role="alert">
                <i class="feather icon-x-circle"></i>
                <strong>❌ {{ __('app.messages.error.validation') }}</strong>
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
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-cube"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">{{ __('app.finished_products.total_notes') }}</span>
                    <span class="stat-number">{{ $deliveryNotes->total() }}</span>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">{{ __('app.finished_products.pending') }}</span>
                    <span class="stat-number">{{ $deliveryNotes->where('status', 'pending')->count() }}</span>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">{{ __('app.finished_products.approved') }}</span>
                    <span class="stat-number">{{ $deliveryNotes->where('status', 'approved')->count() }}</span>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">{{ __('app.finished_products.rejected') }}</span>
                    <span class="stat-number">{{ $deliveryNotes->where('status', 'rejected')->count() }}</span>
                </div>
            </div>
        </div>

        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="fas fa-box-open"></i>
                    {{ __('app.finished_products.management') }}
                </h4>
                <div style="display: flex; gap: 10px;">
                    @if (auth()->user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_CREATE'))
                        <a href="{{ route('manufacturing.finished-product-deliveries.create') }}" class="um-btn um-btn-primary">
                            <i class="fas fa-plus-circle"></i>
                            {{ __('app.finished_products.create_delivery_note') }}
                        </a>
                    @endif
                    @if (auth()->user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_APPROVE'))
                        <a href="{{ route('manufacturing.finished-product-deliveries.pending-approval') }}" class="um-btn um-btn-primary">
                            <i class="fas fa-clock"></i>
                            {{ __('app.finished_products.pending_notes') }}
                            @if(isset($pendingCount) && $pendingCount > 0)
                            <span class="badge bg-danger">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    @endif
                </div>
            </div>

            <div class="um-filters-section">
                <form method="GET" action="{{ route('manufacturing.finished-product-deliveries.index') }}" class="filter-form">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control"
                                   placeholder="{{ __('app.finished_products.search_placeholder') }}"
                                   value="{{ request('search') }}">
                        </div>

                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">{{ __('app.buttons.filter') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('app.status.pending') }}</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('app.status.active') }}</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('app.status.cancelled') }}</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('app.status.completed') }}</option>
                            </select>
                        </div>

                        <div class="um-form-group">
                            <input type="date" name="date_from" class="um-form-control"
                                   value="{{ request('date_from') }}">
                        </div>

                        <div class="um-form-group">
                            <input type="date" name="date_to" class="um-form-control"
                                   value="{{ request('date_to') }}">
                        </div>

                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('app.buttons.search') }}
                            </button>
                            <a href="{{ route('manufacturing.finished-product-deliveries.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-refresh-cw"></i>
                                {{ __('app.buttons.reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="um-table-responsive">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>{{ __('app.finished_products.note_number') }}</th>
                            <th>{{ __('app.finished_products.customer') }}</th>
                            <th>{{ __('app.finished_products.boxes_count') }}</th>
                            <th>{{ __('app.finished_products.total_weight') }}</th>
                            <th>{{ __('app.status.status') }}</th>
                            <th>{{ __('app.finished_products.creation_date') }}</th>
                            <th>{{ __('app.finished_products.created_by') }}</th>
                            <th class="status-column">{{ __('app.buttons.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveryNotes as $note)
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $note->note_number ?? '#' . $note->id }}</strong>
                                </td>

                                <td>
                                    @if($note->customer)
                                        <div>
                                            <strong>{{ $note->customer->name }}</strong><br>
                                            <small class="text-muted">{{ $note->customer->customer_code }}</small>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">{{ __('app.finished_products.not_set') }}</span>
                                    @endif
                                </td>

                                <td>
                                    <i class="fas fa-cube me-1"></i>
                                    {{ $note->items->count() }}
                                </td>

                                <td>
                                    <strong>{{ number_format($note->items->sum('weight'), 2) }}</strong> {{ __('app.units.kg') }}
                                </td>

                                <td class="status-column">
                                    @if($note->status == 'pending')
                                        <span class="um-badge badge-pending">
                                            <i class="fas fa-hourglass-half"></i> {{ __('app.status.pending') }}
                                        </span>
                                    @elseif($note->status == 'approved')
                                        <span class="um-badge badge-approved">
                                            <i class="fas fa-check-circle"></i> {{ __('app.status.active') }}
                                        </span>
                                    @elseif($note->status == 'rejected')
                                        <span class="um-badge badge-rejected">
                                            <i class="fas fa-times-circle"></i> {{ __('app.status.cancelled') }}
                                        </span>
                                    @elseif($note->status == 'completed')
                                        <span class="um-badge badge-completed">
                                            <i class="fas fa-check"></i> {{ __('app.status.completed') }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <small>{{ $note->created_at->format('Y-m-d H:i') }}</small>
                                </td>

                                <td>
                                    @if($note->recordedBy)
                                        {{ $note->recordedBy->name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="um-dropdown">
                                        <button class="um-btn-dropdown" type="button">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="um-dropdown-menu">
                                            @if (auth()->user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_READ'))
                                                <a href="{{ route('manufacturing.finished-product-deliveries.show', $note->id) }}" class="um-dropdown-item um-btn-view">
                                                    <i class="feather icon-eye"></i>
                                                    <span>{{ __('app.buttons.view') }}</span>
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_UPDATE') && $note->status == 'pending' && $note->prepared_by == Auth::id())
                                                <a href="{{ route('manufacturing.finished-product-deliveries.edit', $note->id) }}" class="um-dropdown-item um-btn-edit">
                                                    <i class="feather icon-edit-2"></i>
                                                    <span>{{ __('app.buttons.edit') }}</span>
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_PRINT') && $note->status == 'approved')
                                                <a href="{{ route('manufacturing.finished-product-deliveries.print', $note->id) }}" target="_blank" class="um-dropdown-item um-btn-print">
                                                    <i class="feather icon-printer"></i>
                                                    <span>{{ __('app.buttons.print') }}</span>
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_DELETE') && in_array($note->status, ['pending', 'rejected']))
                                                <form method="POST" action="{{ route('manufacturing.finished-product-deliveries.destroy', $note->id) }}" style="display: inline;" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="um-dropdown-item um-btn-delete" style="width: 100%; text-align: right; border: none; background: none; cursor: pointer;">
                                                        <i class="feather icon-trash-2"></i>
                                                        <span>{{ __('app.buttons.delete') }}</span>
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
                                    <i class="feather icon-inbox"></i> {{ __('app.finished_products.no_notes') }}
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
                            {{ __('app.showing') }} {{ $deliveryNotes->firstItem() }} {{ __('app.to') }} {{ $deliveryNotes->lastItem() }} {{ __('app.of') }}
                            {{ $deliveryNotes->total() }} {{ __('app.finished_products.delivery_notes_total') }}
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
                        title: '{{ __('app.messages.confirm.delete') }}',
                        text: '{{ __('app.finished_products.confirm_delete_message') }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '{{ __('app.buttons.delete') }}',
                        cancelButtonText: '{{ __('app.buttons.cancel') }}',
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
