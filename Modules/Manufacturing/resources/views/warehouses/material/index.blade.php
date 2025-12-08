@extends('master')

@section('title', __('warehouse.materials_management'))

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
                <i class="feather icon-package"></i>
                {{ __('warehouse.materials_management') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('warehouse.breadcrumb_dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('warehouse.materials') }}</span>
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
            <div class="um-alert-custom um-alert-error" role="alert" id="errorMessage">
                <i class="feather icon-alert-circle"></i>
                {{ session('error') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        <!-- Main Courses Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    {{ __('warehouse.material_name_col') }}
                </h4>
                {{-- ✅ التحقق من صلاحية الإضافة --}}
                @if (auth()->user()->hasPermission('WAREHOUSE_MATERIALS_CREATE'))
                    <a href="{{ route('manufacturing.warehouse-products.create') }}" class="um-btn um-btn-primary">
                        <i class="feather icon-plus"></i>
                        {{ __('warehouse.add_material') }}
                    </a>
                @endif
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="{{ __('warehouse.search_materials') }}"
                                value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="category" class="um-form-control">
                                <option value="">{{ __('warehouse.select_category') }}</option>
                                <option value="raw" {{ request('category') == 'raw' ? 'selected' : '' }}>{{ __('warehouse.raw') }}</option>
                                <option value="manufactured" {{ request('category') == 'manufactured' ? 'selected' : '' }}>
                                    {{ __('warehouse.manufactured') }}</option>
                                <option value="finished" {{ request('category') == 'finished' ? 'selected' : '' }}>{{ __('warehouse.finished') }}
                                </option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">{{ __('warehouse.select_status') }}</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>{{ __('warehouse.available') }}
                                </option>
                                <option value="in_use" {{ request('status') == 'in_use' ? 'selected' : '' }}>{{ __('warehouse.in_use') }}
                                </option>
                                <option value="consumed" {{ request('status') == 'consumed' ? 'selected' : '' }}>{{ __('warehouse.consumed') }}
                                </option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>{{ __('warehouse.expired') }}</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="supplier_id" class="um-form-control">
                                <option value="">{{ __('warehouse.select_supplier') }}</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('warehouse.search') }}
                            </button>
                            <a href="{{ route('manufacturing.warehouse-products.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-refresh-cw"></i>
                                {{ __('warehouse.reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Courses Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>

                            <th>{{ __('warehouse.material_code') }}</th>
                            <th>{{ __('warehouse.material_name_col') }}</th>
                            <th>{{ __('warehouse.category_col') }}</th>

                            <th>{{ __('warehouse.unit_col') }}</th>

                            <th>{{ __('warehouse.status_col') }}</th>
                            <th>{{ __('warehouse.date_added') }}</th>
                            <th>{{ __('warehouse.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materials as $material)
                            <tr>

                                <td>
                                    <span class="badge badge-primary">{{ $material->barcode ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <strong>{{ $material->name_ar }}</strong><br>
                                    @if ($material->name_en)
                                        <small class="text-muted">{{ $material->name_en }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $material->getCategoryLabel() }}</span>
                                </td>



                                <td>
                                    @if($material->unit)
                                        {{ $material->unit->unit_name }}
                                    @else
                                        <span style="color: #95a5a6; font-style: italic;">{{ __('warehouse.not_specified') }}</span>
                                    @endif
                                </td>

                                <td>

                                    @if ($material->status === 'available')
                                        <span
                                            class="bg-success text-white px-2 py-1 rounded d-inline-flex align-items-center">
                                            <i class="feather icon-check-circle mr-1"></i> {{ __('warehouse.available') }}
                                        </span>
                                    @elseif ($material->status === 'in_use')
                                        <span
                                            class="bg-warning text-dark px-2 py-1 rounded d-inline-flex align-items-center">
                                            <i class="feather icon-clock mr-1"></i> {{ __('warehouse.in_use') }}
                                        </span>
                                    @elseif ($material->status === 'consumed')
                                        <span
                                            class="bg-danger text-white px-2 py-1 rounded d-inline-flex align-items-center">
                                            <i class="feather icon-x-circle mr-1"></i> {{ __('warehouse.consumed') }}
                                        </span>
                                    @elseif ($material->status === 'expired')
                                        <span
                                            class="bg-secondary text-white px-2 py-1 rounded d-inline-flex align-items-center">
                                            <i class="feather icon-alert-circle mr-1"></i> {{ __('warehouse.expired') }}
                                        </span>
                                    @else
                                        <span class="bg-dark text-white px-2 py-1 rounded">N/A</span>
                                    @endif
                                </td>


                                <td>
                                    @if ($material->created_at)
                                        <small class="text-muted">{{ $material->created_at->format('Y-m-d') }}</small><br>
                                        <small class="text-muted">{{ $material->created_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- ✅ التحقق من الصلاحيات قبل عرض الأزرار --}}
                                    @if (auth()->user()->hasPermission('WAREHOUSE_MATERIALS_READ') || auth()->user()->hasPermission('WAREHOUSE_MATERIALS_UPDATE') || auth()->user()->hasPermission('WAREHOUSE_MATERIALS_DELETE'))
                                        <div class="um-dropdown">
                                            <button class="um-btn-action um-btn-dropdown" title="{{ __('warehouse.actions') }}">
                                                <i class="feather icon-more-vertical"></i>
                                            </button>
                                            <div class="um-dropdown-menu">
                                                {{-- عرض المادة --}}
                                                @if (auth()->user()->hasPermission('WAREHOUSE_MATERIALS_READ'))
                                                    <a href="{{ route('manufacturing.warehouse-products.show', $material->id) }}"
                                                        class="um-dropdown-item um-btn-view">
                                                        <i class="feather icon-eye"></i>
                                                        <span>{{ __('warehouse.view') }}</span>
                                                    </a>
                                                @endif

                                                {{-- تعديل المادة --}}
                                                @if (auth()->user()->hasPermission('WAREHOUSE_MATERIALS_UPDATE'))
                                                    <a href="{{ route('manufacturing.warehouse-products.edit', $material->id) }}"
                                                        class="um-dropdown-item um-btn-edit">
                                                        <i class="feather icon-edit-2"></i>
                                                        <span>{{ __('warehouse.edit') }}</span>
                                                    </a>
                                                @endif

                                                {{-- حذف المادة --}}
                                                @if (auth()->user()->hasPermission('WAREHOUSE_MATERIALS_DELETE'))
                                                    <form method="POST"
                                                        action="{{ route('manufacturing.warehouse-products.destroy', $material->id) }}"
                                                        style="display: inline;" class="delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="um-dropdown-item um-btn-delete">
                                                            <i class="feather icon-trash-2"></i>
                                                            <span>{{ __('warehouse.delete') }}</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">{{ __('warehouse.no_warehouses_found') }}</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted">
                                    <i class="feather icon-inbox"></i> {{ __('warehouse.no_materials_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($materials->count())
                <div class="um-pagination-section">
                    <div>
                        <p class="um-pagination-info">
                            {{ __('warehouse.showing') }} {{ $materials->firstItem() }} {{ __('warehouse.to') }} {{ $materials->lastItem() }} {{ __('warehouse.of') }}
                            {{ $materials->total() }} {{ __('warehouse.material') }}
                        </p>
                    </div>
                    <div>
                        {{ $materials->links('pagination::bootstrap-4') }}
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
                        title: '{{ __('warehouse.confirm_delete_title') }}',
                        text: '{{ __('warehouse.confirm_delete') }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '{{ __('warehouse.yes_delete') }}',
                        cancelButtonText: '{{ __('warehouse.no_cancel') }}',
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
