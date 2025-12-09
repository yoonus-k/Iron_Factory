@extends('master')

@section('title', __('warehouse.units_management'))

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-box"></i>
                {{ __('warehouse.units_management') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('warehouse.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('warehouse.units') }}</span>
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
                <i class="feather icon-alert-circle"></i>
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
                    {{ __('warehouse.units_list') }}
                </h4>
                <a href="{{ route('manufacturing.warehouse-settings.units.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    {{ __('warehouse.add_new_unit') }}
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="{{ __('warehouse.search_units') }}" value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="unit_type" class="um-form-control">
                                <option value="">-- {{ __('warehouse.choose_type') }} --</option>
                                <option value="weight" {{ request('unit_type') == 'weight' ? 'selected' : '' }}>{{ __('warehouse.weight') }}</option>
                                <option value="length" {{ request('unit_type') == 'length' ? 'selected' : '' }}>{{ __('warehouse.length') }}</option>
                                <option value="volume" {{ request('unit_type') == 'volume' ? 'selected' : '' }}>{{ __('warehouse.volume') }}</option>
                                <option value="area" {{ request('unit_type') == 'area' ? 'selected' : '' }}>{{ __('warehouse.area') }}</option>
                                <option value="quantity" {{ request('unit_type') == 'quantity' ? 'selected' : '' }}>{{ __('warehouse.quantity') }}</option>
                                <option value="time" {{ request('unit_type') == 'time' ? 'selected' : '' }}>{{ __('warehouse.time') }}</option>
                                <option value="temperature" {{ request('unit_type') == 'temperature' ? 'selected' : '' }}>{{ __('warehouse.temperature') }}</option>
                                <option value="other" {{ request('unit_type') == 'other' ? 'selected' : '' }}>{{ __('warehouse.other') }}</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="is_active" class="um-form-control">
                                <option value="">-- {{ __('warehouse.choose_status') }} --</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>{{ __('warehouse.active') }}</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>{{ __('warehouse.inactive') }}</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('warehouse.search') }}
                            </button>
                            <a href="{{ route('manufacturing.warehouse-settings.units.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-refresh-cw"></i>
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
                            <th>#</th>
                            <th>{{ __('warehouse.unit_code') }}</th>
                            <th>{{ __('warehouse.unit_name') }}</th>
                            <th>{{ __('warehouse.unit_symbol') }}</th>
                            <th>{{ __('warehouse.unit_type') }}</th>
                            <th>{{ __('warehouse.status') }}</th>
                            <th>{{ __('warehouse.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($units as $unit)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="">{{ $unit->unit_code }}</span>
                                </td>
                                <td>
                                    <strong>{{ $unit->unit_name }}</strong><br>
                                    @if($unit->unit_name_en)
                                        <small class="text-muted">{{ $unit->unit_name_en }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="">{{ $unit->unit_symbol }}</span>
                                </td>
                                <td>
                                    @php
                                        $types = [
                                            'weight' => __('warehouse.weight'),
                                            'length' => __('warehouse.length'),
                                            'volume' => __('warehouse.volume'),
                                            'area' => __('warehouse.area'),
                                            'quantity' => __('warehouse.quantity'),
                                            'time' => __('warehouse.time'),
                                            'temperature' => __('warehouse.temperature'),
                                            'other' => __('warehouse.other'),
                                        ];
                                    @endphp
                                    <span class="">{{ $types[$unit->unit_type] ?? $unit->unit_type }}</span>
                                </td>
                                <td>
                                    @if ($unit->is_active)
                                        <span class="">{{ __('warehouse.active') }}</span>
                                    @else
                                        <span class="">{{ __('warehouse.inactive') }}</span>
                                    @endif
                                </td>
                               <td>
    <div class="um-dropdown">
        <button class="um-btn-action um-btn-dropdown" title="{{ __('warehouse.actions') }}">
            <i class="feather icon-more-vertical"></i>
        </button>

        <div class="um-dropdown-menu">

            <a href="{{ route('manufacturing.warehouse-settings.units.show', $unit->id) }}"
               class="um-dropdown-item um-btn-view">
                <i class="feather icon-eye"></i>
                <span>{{ __('warehouse.view') }}</span>
            </a>

            <a href="{{ route('manufacturing.warehouse-settings.units.edit', $unit->id) }}"
               class="um-dropdown-item um-btn-edit">
                <i class="feather icon-edit-2"></i>
                <span>{{ __('warehouse.edit') }}</span>
            </a>

            <form method="POST"
                  action="{{ route('manufacturing.warehouse-settings.units.destroy', $unit->id) }}"
                  style="display:inline;" class="delete-form">
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
                                <td colspan="7" class="text-center text-muted">
                                    <i class="feather icon-inbox"></i> {{ __('warehouse.no_units') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($units->count())
                <div class="um-pagination-section">
                    <div>
                        <p class="um-pagination-info">
                            {{ __('warehouse.showing') }} {{ $units->firstItem() }} {{ __('warehouse.to') }} {{ $units->lastItem() }} {{ __('warehouse.of') }} {{ $units->total() }} {{ __('warehouse.units') }}
                        </p>
                    </div>
                    <div>
                        {{ $units->links() }}
                    </div>
                </div>
            @endif
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete confirmation with SweetAlert2
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: __('warehouse.confirm_delete'),
                        text: __('warehouse.confirm_delete_unit_message'),
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: __('warehouse.yes_delete'),
                        cancelButtonText: __('warehouse.cancel'),
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
