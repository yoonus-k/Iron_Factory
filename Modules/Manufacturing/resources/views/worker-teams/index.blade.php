@extends('master')

@section('title', __('worker-teams.manage_worker_teams'))

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-users"></i>
                {{ __('worker-teams.manage_worker_teams') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('worker-teams.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('worker-teams.worker_teams') }}</span>
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

        <!-- Statistics Cards -->


        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    {{ __('worker-teams.worker_teams_list') }}
                </h4>
                @if(auth()->user()->hasPermission('WORKER_TEAMS_CREATE'))
                <a href="{{ route('manufacturing.worker-teams.create') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-plus"></i>
                    {{ __('worker-teams.add_new_team') }}
                </a>
                @endif
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET" action="{{ route('manufacturing.worker-teams.index') }}">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="{{ __('worker-teams.search_placeholder') }}" value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="is_active" class="um-form-control">
                                <option value="">{{ __('worker-teams.all_statuses') }}</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>{{ __('worker-teams.active') }}</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>{{ __('worker-teams.inactive') }}</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('worker-teams.search_action') }}
                            </button>
                            <a href="{{ route('manufacturing.worker-teams.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                {{ __('worker-teams.reset_action') }}
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
                            <th>{{ __('worker-teams.team_code') }}</th>
                            <th>{{ __('worker-teams.team_name') }}</th>
                            <th>{{ __('worker-teams.workers_count') }}</th>
                            <th>{{ __('worker-teams.supervisor') }}</th>
                            <th>{{ __('worker-teams.created_date') }}</th>
                            <th>{{ __('worker-teams.status') }}</th>
                            <th>{{ __('worker-teams.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teams as $index => $team)
                        <tr>
                            <td>{{ $teams->firstItem() + $index }}</td>
                            <td><strong>{{ $team->team_code }}</strong></td>
                            <td>{{ $team->name }}</td>
                            <td><span class="um-badge um-badge-info">{{ $team->workers_count ?? 0 }}</span></td>
                            <td>{{ $team->supervisor->name ?? '-' }}</td>
                            <td>{{ $team->created_at->format('Y-m-d') }}</td>
                            <td>
                                <span class="um-badge um-badge-{{ $team->is_active ? 'success' : 'secondary' }}">
                                    {{ $team->is_active ? __('worker-teams.active') : __('worker-teams.inactive') }}
                                </span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="{{ __('worker-teams.actions') }}">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        @if (auth()->user()->hasPermission('WORKER_TEAMS_READ'))
                                        <a href="{{ route('manufacturing.worker-teams.show', $team->id) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>{{ __('worker-teams.view') }}</span>
                                        </a>
                                        @endif
                                        @if (auth()->user()->hasPermission('WORKER_TEAMS_UPDATE'))
                                        <a href="{{ route('manufacturing.worker-teams.edit', $team->id) }}" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-edit-2"></i>
                                            <span>{{ __('worker-teams.edit') }}</span>
                                        </a>
                                        <form method="POST" action="{{ route('manufacturing.worker-teams.toggle-status', $team->id) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="um-dropdown-item um-btn-toggle">
                                                <i class="feather icon-{{ $team->is_active ? 'pause' : 'play' }}-circle"></i>
                                                <span>{{ $team->is_active ? __('worker-teams.disable') : __('worker-teams.activate') }}</span>
                                            </button>
                                        </form>
                                        @endif
                                        @if(!$team->is_active)
                                        @if (auth()->user()->hasPermission('WORKER_TEAMS_DELETE'))
                                        <form method="POST" action="{{ route('manufacturing.worker-teams.destroy', $team->id) }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="um-dropdown-item um-btn-delete">
                                                <i class="feather icon-trash-2"></i>
                                                <span>{{ __('worker-teams.delete') }}</span>
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
                            <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                                <i class="feather icon-users" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                {{ __('worker-teams.no_teams') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="um-mobile-view">
                @foreach($teams as $team)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #667eea20; color: #667eea; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                <i class="feather icon-users" style="font-size: 18px;"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">{{ $team->name }}</h6>
                                <span class="um-category-id">{{ $team->team_code }}</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-{{ $team->is_active ? 'success' : 'secondary' }}">
                            {{ $team->is_active ? __('worker-teams.active') : __('worker-teams.inactive') }}
                        </span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('worker-teams.workers_count_label') }}</span>
                            <span class="um-info-value">{{ $team->workers_count ?? 0 }} {{ __('worker-teams.worker') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('worker-teams.supervisor_label_mobile') }}</span>
                            <span class="um-info-value">{{ $team->supervisor->name ?? __('worker-teams.not_specified') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('worker-teams.creation_date_label') }}</span>
                            <span class="um-info-value">{{ $team->created_at->format('Y-m-d') }}</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        <a href="{{ route('manufacturing.worker-teams.show', $team->id) }}" class="um-btn um-btn-sm um-btn-primary">
                            <i class="feather icon-eye" style="font-size: 14px;"></i>
                            {{ __('worker-teams.view') }}
                        </a>
                        <a href="{{ route('manufacturing.worker-teams.edit', $team->id) }}" class="um-btn um-btn-sm um-btn-secondary">
                            <i class="feather icon-edit-2" style="font-size: 14px;"></i>
                            {{ __('worker-teams.edit') }}
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        {{ __('worker-teams.showing') }} {{ $teams->firstItem() ?? 0 }} {{ __('worker-teams.to') }} {{ $teams->lastItem() ?? 0 }} {{ __('worker-teams.of') }} {{ $teams->total() }} {{ __('worker-teams.team') }}
                    </p>
                </div>
                <div>
                    {{ $teams->links() }}
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // {{ __('worker-teams.confirm_delete') }}
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('{{ __('worker-teams.confirm_delete') }}')) {
                        form.submit();
                    }
                });
            });

            // {{ __('worker-teams.auto_hide_alerts') }}
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
