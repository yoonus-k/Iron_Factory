@extends('master')

@section('title', __('warehouse.material_types_management'))

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-package"></i>
                {{ __('warehouse.material_types_management') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('warehouse.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('warehouse.material_types_management') }}</span>
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
                    {{ __('warehouse.material_types_list') }}
                </h4>
                <a href="{{ route('manufacturing.warehouse-settings.material-types.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    {{ __('warehouse.add_new_material_type') }}
                </a>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="{{ __('warehouse.search_material_types') }}" value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="category" class="um-form-control">
                                <option value="">-- {{ __('warehouse.select_category') }} --</option>
                                <option value="raw_material" {{ request('category') == 'raw_material' ? 'selected' : '' }}>{{ __('warehouse.raw_material') }}</option>
                                <option value="finished_product" {{ request('category') == 'finished_product' ? 'selected' : '' }}>{{ __('warehouse.finished_product') }}</option>
                                <option value="semi_finished" {{ request('category') == 'semi_finished' ? 'selected' : '' }}>{{ __('warehouse.semi_finished_product') }}</option>
                                <option value="additive" {{ request('category') == 'additive' ? 'selected' : '' }}>{{ __('warehouse.additive') }}</option>
                                <option value="packing_material" {{ request('category') == 'packing_material' ? 'selected' : '' }}>{{ __('warehouse.packing_material') }}</option>
                                <option value="component" {{ request('category') == 'component' ? 'selected' : '' }}>{{ __('warehouse.component') }}</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="is_active" class="um-form-control">
                                <option value="">-- {{ __('warehouse.select_status') }} --</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>{{ __('warehouse.active') }}</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>{{ __('warehouse.inactive') }}</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('warehouse.search') }}
                            </button>
                            <a href="{{ route('manufacturing.warehouse-settings.material-types.index') }}" class="um-btn um-btn-outline">
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
                            <th>{{ __('warehouse.type_code') }}</th>
                            <th>{{ __('warehouse.type_name') }}</th>
                            <th>{{ __('warehouse.category') }}</th>
                            <th>{{ __('warehouse.standard_cost') }}</th>
                            <th>{{ __('warehouse.status') }}</th>
                            <th>{{ __('warehouse.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materialTypes as $materialType)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ $materialType->type_code }}</span>
                                </td>
                                <td>
                                    <strong>{{ $materialType->type_name }}</strong><br>
                                    @if($materialType->type_name_en)
                                        <small class="text-muted">{{ $materialType->type_name_en }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $categories = [
                                            'raw_material' => 'خام',
                                            'finished_product' => 'منتج نهائي',
                                            'semi_finished' => 'منتج نصف نهائي',
                                            'additive' => 'إضافة',
                                            'packing_material' => 'مادة تغليف',
                                            'component' => 'مكون',
                                        ];
                                    @endphp
                                    <span class="badge badge-info">{{ $categories[$materialType->category] ?? $materialType->category }}</span>
                                </td>
                                <td>
                                    @if($materialType->standard_cost)
                                        <span class="text-success">{{ number_format($materialType->standard_cost, 2) }} {{ __('warehouse.currency') }}</span>
                                    @else
                                        <span class="text-muted">{{ __('warehouse.not_specified') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($materialType->is_active)
                                        <span class="badge badge-success">{{ __('warehouse.active') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('warehouse.inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="um-dropdown">
                                        <button class="um-btn-action um-btn-dropdown" title="{{ __('warehouse.actions') }}">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>

                                        <div class="um-dropdown-menu">

                                            <a href="{{ route('manufacturing.warehouse-settings.material-types.show', $materialType->id) }}"
                                               class="um-dropdown-item um-btn-view">
                                                <i class="feather icon-eye"></i>
                                                <span>{{ __('warehouse.view') }}</span>
                                            </a>

                                            <a href="{{ route('manufacturing.warehouse-settings.material-types.edit', $materialType->id) }}"
                                               class="um-dropdown-item um-btn-edit">
                                                <i class="feather icon-edit-2"></i>
                                                <span>{{ __('warehouse.edit') }}</span>
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('manufacturing.warehouse-settings.material-types.destroy', $materialType->id) }}"
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
                                    <i class="feather icon-inbox"></i> {{ __('warehouse.no_material_types') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($materialTypes->count())
                <div class="um-pagination-section">
                    <div>
                        <p class="um-pagination-info">
                            {{ __('warehouse.showing') }} {{ $materialTypes->firstItem() }} {{ __('warehouse.to') }} {{ $materialTypes->lastItem() }} {{ __('warehouse.of') }} {{ $materialTypes->total() }} {{ __('warehouse.types') }}
                        </p>
                    </div>
                    <div>
                        {{ $materialTypes->links() }}
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
                        title: '{{ __('warehouse.confirm_delete') }}',
                        text: '{{ __('warehouse.confirm_delete_type_message') }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '{{ __('warehouse.yes_delete') }}',
                        cancelButtonText: '{{ __('warehouse.cancel') }}',
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