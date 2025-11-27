@extends('master')

@section('title', __('workers.workers_management'))

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-users"></i>
                {{ __('workers.workers_management') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('app.menu.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('workers.workers') }}</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        @if(session('success'))
        <div class="um-alert-custom um-alert-success" role="alert">
            <i class="feather icon-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="um-alert-custom um-alert-danger" role="alert">
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
                    {{ __('workers.workers') }} {{ __('app.list') }}
                </h4>
                @if(auth()->user()->hasPermission('WORKERS_CREATE'))
                <a href="{{ route('manufacturing.workers.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    {{ __('workers.add_worker') }}
                </a>
                @endif
       </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET" action="{{ route('manufacturing.workers.index') }}">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="{{ __('app.search') }} ({{ __('workers.worker_name') }}, {{ __('workers.worker_code') }}, {{ __('workers.national_id') }}...)" value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="position" class="um-form-control">
                                <option value="">{{ __('app.all') }} {{ __('app.jobs') }}</option>
                                <option value="worker" {{ request('position') == 'worker' ? 'selected' : '' }}>{{ __('workers.worker') }}</option>
                                <option value="supervisor" {{ request('position') == 'supervisor' ? 'selected' : '' }}>{{ __('workers.supervisor') }}</option>
                                <option value="technician" {{ request('position') == 'technician' ? 'selected' : '' }}>{{ __('workers.technician') }}</option>
                                <option value="quality_inspector" {{ request('position') == 'quality_inspector' ? 'selected' : '' }}>{{ __('workers.quality_inspector') }}</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="is_active" class="um-form-control">
                                <option value="">{{ __('app.all') }} {{ __('app.status.status') }}</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>{{ __('workers.active') }}</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>{{ __('workers.inactive') }}</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('app.buttons.search') }}
                            </button>
                            <a href="{{ route('manufacturing.workers.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                {{ __('app.buttons.reset') }}
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
                            <th>{{ __('workers.code') }}</th>
                            <th>{{ __('workers.name') }}</th>
                            <th>{{ __('workers.job') }}</th>
                            <th>{{ __('workers.phone') }}</th>
                            <th>{{ __('workers.shift_pref') }}</th>
                            <th>{{ __('workers.salary_hour') }}</th>
                            <th>{{ __('app.status.status') }}</th>
                            <th>{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workers as $index => $worker)
                        <tr>
                            <td>{{ $workers->firstItem() + $index }}</td>
                            <td><strong>{{ $worker->worker_code }}</strong></td>
                            <td>{{ $worker->name }}</td>
                            <td>
                                <span class="um-badge um-badge-info">
                                    {{ $worker->position_name }}
                                </span>
                            </td>
                            <td>{{ $worker->phone ?? '-' }}</td>
                            <td>{{ $worker->shift_preference_name }}</td>
                            <td>{{ number_format($worker->hourly_rate, 2) }} {{ __('workers.currency') }}</td>
                            <td>
                                <span class="um-badge um-badge-{{ $worker->is_active ? 'success' : 'secondary' }}">
                                    {{ $worker->is_active ? __('workers.active') : __('workers.inactive') }}
                                </span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="{{ __('app.actions') }}">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
@if (auth()->user()->hasPermission('WORKERS_READ'))
                                        <a href="{{ route('manufacturing.workers.show', $worker->id) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>{{ __('app.buttons.view') }}</span>
                                        </a>
                                        @endif
                                        @if (auth()->user()->hasPermission('WORKERS_UPDATE'))
                                        <a href="{{ route('manufacturing.workers.edit', $worker->id) }}" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-edit-2"></i>
                                            <span>{{ __('app.buttons.edit') }}</span>
                                        </a>
                                        <form method="POST" action="{{ route('manufacturing.workers.toggle-status', $worker->id) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="um-dropdown-item um-btn-toggle">
                                                <i class="feather icon-{{ $worker->is_active ? 'pause' : 'play' }}-circle"></i>
                                                <span>{{ $worker->is_active ? __('workers.disable') : __('workers.enable') }}</span>
                                            </button>
                                        </form>
                                        @endif
                                        @if(!$worker->is_active)
                                        @if (auth()->user()->hasPermission('WORKERS_DELETE'))
                                        <form method="POST" action="{{ route('manufacturing.workers.destroy', $worker->id) }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="um-dropdown-item um-btn-delete">
                                                <i class="feather icon-trash-2"></i>
                                                <span>{{ __('app.buttons.delete') }}</span>
                                            </button>
                                        </form>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: #999;">
                                <i class="feather icon-users" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                {{ __('workers.no_workers_found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View - Cards -->
            <div class="um-mobile-view">
                @forelse($workers as $index => $worker)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div style="flex: 1;">
                            <h5 style="margin: 0; font-weight: 600;">{{ $worker->worker_code }}</h5>
                            <small style="color: #999;">{{ $worker->name }}</small>
                        </div>
                        <span class="um-badge um-badge-{{ $worker->is_active ? 'success' : 'secondary' }}">
                            {{ $worker->is_active ? __('workers.active') : __('workers.inactive') }}
                        </span>
                    </div>
                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('workers.job') }}:</span>
                            <span class="um-info-value um-badge um-badge-info">{{ $worker->position_name }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('workers.phone') }}:</span>
                            <span class="um-info-value">{{ $worker->phone ?? '-' }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('workers.shift_pref') }}:</span>
                            <span class="um-info-value">{{ $worker->shift_preference_name }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('workers.salary_hour') }}:</span>
                            <span class="um-info-value">{{ number_format($worker->hourly_rate, 2) }} {{ __('workers.currency') }}</span>
                        </div>
                    </div>
                    <div class="um-category-card-footer">
                        @if(auth()->user()->hasPermission('WORKERS_READ'))
                        <a href="{{ route('manufacturing.workers.show', $worker->id) }}" class="um-btn um-btn-sm um-btn-outline">
                            <i class="feather icon-eye"></i>
                            {{ __('app.buttons.view') }}
                        </a>
                        @endif
                        @if(auth()->user()->hasPermission('WORKERS_UPDATE'))
                        <a href="{{ route('manufacturing.workers.edit', $worker->id) }}" class="um-btn um-btn-sm um-btn-primary">
                            <i class="feather icon-edit-2"></i>
                            {{ __('app.buttons.edit') }}
                        </a>
                        @endif
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 40px; color: #999;">
                    <i class="feather icon-users" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                    {{ __('workers.no_workers_found') }}
                </div>
                @endforelse
            </div>


            <!-- Pagination -->
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        {{ __('app.showing') }} {{ $workers->firstItem() ?? 0 }} {{ __('app.to') }} {{ $workers->lastItem() ?? 0 }} {{ __('app.of') }} {{ $workers->total() }} {{ __('workers.workers') }}
                    </p>
                </div>
                <div>
                    {{ $workers->links() }}
                </div>
            </div>
        </section>
    </div>

    <style>
        /* Mobile View Styles */
        @media (max-width: 768px) {
            .um-table-responsive.um-desktop-view {
                display: none;
            }

            .um-mobile-view {
                display: block;
            }

            .um-mobile-view {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .um-category-card {
                background: white;
                border: 1px solid #e3e6f0;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                margin-bottom: 10px;
                transition: box-shadow 0.3s ease;
            }

            .um-category-card:active {
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }

            .um-category-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 15px;
                background: #f8f9fa;
                border-bottom: 1px solid #e3e6f0;
            }

            .um-category-card-header h5 {
                margin: 0;
                font-weight: 600;
                color: #1a202c;
                font-size: 14px;
            }

            .um-category-card-header small {
                display: block;
                color: #999;
                font-size: 12px;
                margin-top: 3px;
            }

            .um-category-card-body {
                padding: 12px 15px;
            }

            .um-info-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border-bottom: 1px solid #f1f1f1;
                font-size: 13px;
            }

            .um-info-row:last-child {
                border-bottom: none;
            }

            .um-info-label {
                font-weight: 500;
                color: #666;
                min-width: 90px;
            }

            .um-info-value {
                text-align: right;
                color: #333;
                font-weight: 500;
                flex: 1;
                padding-right: 10px;
            }

            .um-category-card-footer {
                display: flex;
                gap: 8px;
                padding: 12px 15px;
                border-top: 1px solid #e3e6f0;
                background: #f8f9fa;
            }

            .um-category-card-footer .um-btn {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
                font-size: 12px;
                padding: 8px 12px;
            }

            .um-btn-sm {
                padding: 8px 12px !important;
                font-size: 12px !important;
                border-radius: 5px;
            }

            .um-badge {
                display: inline-block;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 11px;
                font-weight: 500;
            }

            .um-badge-success {
                background-color: #d4edda;
                color: #155724;
            }

            .um-badge-secondary {
                background-color: #e2e3e5;
                color: #383d41;
            }

            .um-badge-info {
                background-color: #d1ecf1;
                color: #0c5460;
            }
        }

        /* Desktop View - Hide Mobile Elements */
        @media (min-width: 769px) {
            .um-mobile-view {
                display: none;
            }

            .um-table-responsive.um-desktop-view {
                display: block;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // {{ __('workers.confirm_delete') }}
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('{{ __('workers.confirm_delete') }}')) {
                        form.submit();
                    }
                });
            });

            // {{ __('workers.auto_hide_alerts') }}
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
