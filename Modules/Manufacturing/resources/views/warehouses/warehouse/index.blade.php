@extends('master')

@section('title', __('warehouse.warehouse_management'))

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-home"></i>
                {{ __('warehouse.warehouse_management') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('warehouse.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('warehouse.warehouse') }}</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        @if (session('success'))
            <div class="um-alert-custom um-alert-success" role="alert" id="successMessage">
                <i class="feather icon-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="um-alert-custom um-alert-danger" role="alert" id="errorMessage">
                <i class="feather icon-alert-circle"></i>
                {{ session('error') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        <!-- Main Warehouses Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    {{ __('warehouse.warehouses_list') }}
                </h4>
                <a href="{{ route('manufacturing.warehouses.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    {{ __('warehouse.add_new_warehouse') }}
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control"
                                   placeholder="{{ __('warehouse.search_warehouses') }}"
                                   value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">{{ __('warehouse.all_statuses') }}</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                    {{ __('warehouse.active') }}
                                </option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                    {{ __('warehouse.inactive') }}
                                </option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('warehouse.search') }}
                            </button>
                            <a href="{{ route('manufacturing.warehouses.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                {{ __('warehouse.reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Warehouses Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>{{ __('warehouse.warehouse_name') }}</th>
                            <th>{{ __('warehouse.warehouse_code') }}</th>
                            <th>{{ __('warehouse.status') }}</th>
                            <th>{{ __('warehouse.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouses as $warehouse)
                        <tr>
                            <td>
                                <strong>{{ $warehouse->name ?? $warehouse->warehouse_name }}</strong>
                            </td>
                            <td>{{ $warehouse->code ?? $warehouse->warehouse_code }}</td>
                            <td>
                                <span class="um-badge {{ $warehouse->is_active ? 'um-badge-success' : 'um-badge-danger' }}">
                                    {{ $warehouse->is_active ? __('warehouse.active') : __('warehouse.inactive') }}
                                </span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="{{ __('warehouse.actions') }}">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.warehouses.show', $warehouse->id) }}" class="um-dropdown-item um-btn-eye">
                                            <i class="feather icon-eye"></i>
                                            <span>{{ __('warehouse.view') }}</span>
                                        </a>
                                        <a href="{{ route('manufacturing.warehouses.edit', $warehouse->id) }}" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-edit-2"></i>
                                            <span>{{ __('warehouse.edit') }}</span>
                                        </a>
                                        <form method="POST" action="{{ route('manufacturing.warehouses.destroy', $warehouse->id) }}" style="display: inline;" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="um-dropdown-item um-btn-delete">
                                                <i class="feather icon-trash-2"></i>
                                                <span>{{ __('warehouse.delete') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 20px;">
                                <p>{{ __('warehouse.no_warehouses_found') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Warehouses Cards - Mobile View -->
            <div class="um-mobile-view">
                @forelse($warehouses as $warehouse)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5;">
                                <i class="feather icon-home"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">{{ $warehouse->name ?? $warehouse->warehouse_name }}</h6>
                                <span class="um-category-id">#{{ $warehouse->code ?? $warehouse->warehouse_code }}</span>
                            </div>
                        </div>
                        <span class="um-badge {{ $warehouse->is_active ? 'um-badge-success' : 'um-badge-danger' }}">
                            {{ $warehouse->is_active ? __('warehouse.active') : __('warehouse.inactive') }}
                        </span>
                    </div>

                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="{{ __('warehouse.actions') }}">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                <a href="{{ route('manufacturing.warehouses.edit', $warehouse->id) }}" class="um-dropdown-item um-btn-edit">
                                    <i class="feather icon-edit-2"></i>
                                    <span>{{ __('warehouse.edit') }}</span>
                                </a>
                                <form method="POST" action="{{ route('manufacturing.warehouses.destroy', $warehouse->id) }}" style="display: inline;" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="um-dropdown-item um-btn-delete">
                                        <i class="feather icon-trash-2"></i>
                                        <span>{{ __('warehouse.delete') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 40px;">
                    <p>{{ __('warehouse.no_warehouses_found') }}</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        @if($warehouses->count())
                            {{ __('warehouse.showing') }} {{ $warehouses->firstItem() }} {{ __('warehouse.to') }} {{ $warehouses->lastItem() }} {{ __('warehouse.of') }} {{ $warehouses->total() }} {{ __('warehouse.warehouse') }}
                        @else
                            {{ __('warehouse.no_warehouses_found') }}
                        @endif
                    </p>
                </div>
                <div>
                    {{ $warehouses->links() }}
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تأكيد الحذف باستخدام SweetAlert2
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: '{{ __('warehouse.confirm_delete') }}',
                        text: '{{ __('warehouse.confirm_delete_warehouse_message') }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '{{ __("warehouse.yes_delete") }}',
                        cancelButtonText: '{{ __("warehouse.cancel") }}',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // إخفاء التنبيهات تلقائياً بعد 5 ثواني
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
