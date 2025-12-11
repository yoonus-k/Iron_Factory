@extends('master')

@section('title', __('shifts-workers.shift_detailed_report'))

@push('styles')

@endpush

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/shift-dashboard.css') }}">
<style>
    .comparison-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 1.5rem;
    }

    .comparison-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .comparison-item:last-child {
        border-bottom: none;
    }

    .change-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .change-badge.up {
        background: #d1fae5;
        color: #059669;
    }

    .change-badge.down {
        background: #fee2e2;
        color: #dc2626;
    }

    .change-badge.neutral {
        background: #f3f4f6;
        color: #6b7280;
    }

    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .attendance-table th,
    .attendance-table td {
        padding: 0.75rem 1rem;
        text-align: {{ app()->getLocale() == 'ar' || app()->getLocale() == 'ur' ? 'right' : 'left' }};
        border-bottom: 1px solid #f0f0f0;
    }

    .attendance-table th {
        background: #f9fafb;
        font-weight: 600;
        color: #374151;
    }

    .attendance-table tbody tr:hover {
        background: #f9fafb;
    }

    .handover-card {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        border-{{ app()->getLocale() == 'ar' || app()->getLocale() == 'ur' ? 'right' : 'left' }}: 4px solid #0066B2;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .handover-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .handover-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem;
        background: #e0f2fe;
        color: #0369a1;
        border-radius: 20px;
        font-size: 0.875rem;
    }

    .approved-badge {
        background: #d1fae5;
        color: #059669;
    }

    .teams-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .team-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    .team-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .team-icon {
        width: 40px;
        height: 40px;
        background: #e0f2fe;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0369a1;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .stage-efficiency-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .efficiency-card {
        background: white;
        border-radius: 8px;
        padding: 1.25rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    .efficiency-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .efficiency-title {
        font-weight: 600;
        color: #1f2937;
        font-size: 1rem;
    }

    .efficiency-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .efficiency-excellent {
        background: #d1fae5;
        color: #059669;
    }

    .efficiency-good {
        background: #dbeafe;
        color: #0369a1;
    }

    .efficiency-average {
        background: #fef3c7;
        color: #d97706;
    }

    .efficiency-poor {
        background: #fee2e2;
        color: #dc2626;
    }

    .print-button {
        background: white;
        border: 2px solid #0066B2;
        color: #0066B2;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .print-button:hover {
        background: #0066B2;
        color: white;
    }

    @media print {
        .controls-bar,
        .refresh-btn,
        .print-button {
            display: none !important;
        }
    }
</style>
<div class="shift-container">
    <!-- Header -->
    <div class="shift-header">
        <h1>
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            {{ __('shifts-workers.shift_detailed_report') }}
        </h1>
        <div class="shift-info">
            <span class="shift-badge">
                <span class="live-indicator"></span>
                {{ __('shifts-workers.live_shift') }} {{ $shiftType == 'morning' ? __('shifts-workers.morning') : __('shifts-workers.evening') }}
            </span>
            <span style="opacity: 0.9;">{{ $date }}</span>
            <span style="opacity: 0.9;">{{ date('H:i', strtotime($timeRange['start'])) }} - {{ date('H:i', strtotime($timeRange['end'])) }}</span>
            <button class="print-button" onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-{{ app()->getLocale() == 'ar' || app()->getLocale() == 'ur' ? 'left' : 'right' }}: 0.5rem;">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                {{ __('shifts-workers.print_report') }}
            </button>
        </div>
    </div>

    <!-- Controls -->
    <div class="controls-bar">
        <div class="control-group">
            <label class="control-label">{{ __('shifts-workers.date_filter') }}</label>
            <input type="date" class="select-input" id="dateFilter" value="{{ $date }}" onchange="applyFilters()">
        </div>

        <div class="control-group">
            <label class="control-label">من تاريخ</label>
            <input type="date" class="select-input" id="fromDateFilter" value="{{ request('from_date', $date) }}" onchange="applyFilters()">
        </div>

        <div class="control-group">
            <label class="control-label">إلى تاريخ</label>
            <input type="date" class="select-input" id="toDateFilter" value="{{ request('to_date', $date) }}" onchange="applyFilters()">
        </div>

        <div class="control-group">
            <label class="control-label">{{ __('shifts-workers.shift_filter') }}</label>
            <select class="select-input" id="shiftFilter" onchange="applyFilters()">
                <option value="morning" {{ $shiftType == 'morning' ? 'selected' : '' }}>{{ __('shifts-workers.morning_shift_time') }}</option>
                <option value="evening" {{ $shiftType == 'evening' ? 'selected' : '' }}>{{ __('shifts-workers.evening_shift_time') }}</option>
            </select>
        </div>

        <button class="refresh-btn" onclick="refreshData()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
            {{ __('shifts-workers.refresh') }}
        </button>

        <div class="last-update">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            {{ __('shifts-workers.last_update') }} <span id="lastUpdateTime">{{ __('shifts-workers.now') }}</span>
        </div>
    </div>

    <!-- Shift Assignment Info -->
    @if($shiftAssignment)
    <div class="comparison-card">
        <h3 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            {{ __('shifts-workers.shift_info') }}
        </h3>
        <div class="detail-row">
            <span style="color: #6b7280;">{{ __('shifts-workers.shift_code_label') }}</span>
            <strong>{{ $shiftAssignment['shift_code'] }}</strong>
        </div>
        <div class="detail-row">
            <span style="color: #6b7280;">{{ __('shifts-workers.supervisor_name_label') }}</span>
            <strong>{{ $shiftAssignment['supervisor']->name ?? __('shifts-workers.not_specified') }}</strong>
        </div>
        <div class="detail-row">
            <span style="color: #6b7280;">{{ __('shifts-workers.workers_count_label') }}</span>
            <strong>{{ $shiftAssignment['total_workers'] }}</strong>
        </div>
        <div class="detail-row">
            <span style="color: #6b7280;">{{ __('shifts-workers.shift_status_label') }}</span>
            <span class="change-badge {{ $shiftAssignment['status'] == 'active' ? 'up' : 'neutral' }}">
                {{ $shiftAssignment['status_name'] }}
            </span>
        </div>
        @if($shiftAssignment['notes'])
        <div class="detail-row">
            <span style="color: #6b7280;">{{ __('shifts-workers.notes_label') }}</span>
            <span>{{ $shiftAssignment['notes'] }}</span>
        </div>
        @endif
    </div>
    @endif

    <!-- WIP Alert -->
    @if($wipCount > 0)
        <div class="wip-alert {{ $wipCount > 50 ? 'critical' : '' }}">
            <div class="wip-alert-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <h3 class="wip-alert-title">{{ __('shifts-workers.wip_alert_title') }}</h3>
            </div>
            <p>
                {{ __('shifts-workers.wip_alert_message', ['count' => $wipCount]) }}
            </p>
        </div>
    @endif

    <!-- Main Statistics -->
    <div class="stats-grid" id="statsGrid">
        <div class="stat-card primary">
            <div class="stat-header">
                <div>
                    <div class="stat-title">{{ __('shifts-workers.total_items_produced') }}</div>
                    <p class="stat-value">{{ number_format($summary['total_items']) }}</p>
                    @if(isset($comparison['items_change']))
                    <span class="change-badge {{ $comparison['items_change']['direction'] }}">
                        {{ $comparison['items_change']['direction'] == 'up' ? '↑' : ($comparison['items_change']['direction'] == 'down' ? '↓' : '→') }}
                        {{ $comparison['items_change']['value'] }}%
                    </span>
                    @endif
                </div>
                <div class="stat-icon primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-header">
                <div>
                    <div class="stat-title">{{ __('shifts-workers.total_output') }}</div>
                    <p class="stat-value">{{ number_format($summary['total_output_kg'], 2) }}</p>
                    <small style="color: var(--gray-600);">{{ __('shifts-workers.kilogram') }}</small>
                    @if(isset($comparison['output_change']))
                    <br>
                    <span class="change-badge {{ $comparison['output_change']['direction'] }}">
                        {{ $comparison['output_change']['direction'] == 'up' ? '↑' : ($comparison['output_change']['direction'] == 'down' ? '↓' : '→') }}
                        {{ $comparison['output_change']['value'] }}%
                    </span>
                    @endif
                </div>
                <div class="stat-icon success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-header">
                <div>
                    <div class="stat-title">{{ __('shifts-workers.waste_percentage') }}</div>
                    <p class="stat-value">{{ number_format($summary['waste_percentage'], 2) }}%</p>
                    <small style="color: var(--gray-600);">{{ number_format($summary['total_waste_kg'], 2) }} {{ __('shifts-workers.kg') }}</small>
                </div>
                <div class="stat-icon warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-header">
                <div>
                    <div class="stat-title">{{ __('shifts-workers.overall_efficiency') }}</div>
                    <p class="stat-value">{{ number_format($summary['efficiency'], 1) }}%</p>
                    @if(isset($comparison['efficiency_change']))
                    <span class="change-badge {{ $comparison['efficiency_change']['direction'] }}">
                        {{ $comparison['efficiency_change']['direction'] == 'up' ? '↑' : ($comparison['efficiency_change']['direction'] == 'down' ? '↓' : '→') }}
                        {{ $comparison['efficiency_change']['value'] }}%
                    </span>
                    @endif
                </div>
                <div class="stat-icon success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Stage Efficiency Details -->
    <div class="comparison-card">
        <h3 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
            </svg>
            {{ __('shifts-workers.stage_efficiency_details') }}
        </h3>
        <div class="stage-efficiency-grid">
            @foreach($stageEfficiency as $stage)
            <div class="efficiency-card">
                <div class="efficiency-header">
                    <div class="efficiency-title">{{ $stage['name'] }}</div>
                    <span class="efficiency-badge efficiency-{{ $stage['efficiency'] >= 95 ? 'excellent' : ($stage['efficiency'] >= 90 ? 'good' : ($stage['efficiency'] >= 85 ? 'average' : 'poor')) }}">
                        {{ number_format($stage['efficiency'], 1) }}%
                    </span>
                </div>
                <div class="detail-row">
                    <span style="color: #6b7280;">{{ __('shifts-workers.items_produced') }}</span>
                    <strong>{{ number_format($stage['items']) }}</strong>
                </div>
                <div class="detail-row">
                    <span style="color: #6b7280;">{{ __('shifts-workers.output_label') }}</span>
                    <strong>{{ number_format($stage['output'], 2) }} {{ __('shifts-workers.kg') }}</strong>
                </div>
                <div class="detail-row">
                    <span style="color: #6b7280;">{{ __('shifts-workers.waste_label') }}</span>
                    <strong style="color: #dc2626;">{{ number_format($stage['waste'], 2) }} {{ __('shifts-workers.kg') }} ({{ $stage['waste_pct'] }}%)</strong>
                </div>
                <div class="detail-row">
                    <span style="color: #6b7280;">{{ __('shifts-workers.workers_count_short') }}</span>
                    <strong>{{ $stage['workers_count'] }}</strong>
                </div>
                <div class="detail-row">
                    <span style="color: #6b7280;">{{ __('shifts-workers.average_per_worker') }}</span>
                    <strong>{{ number_format($stage['avg_per_worker'], 1) }} {{ __('shifts-workers.pieces') }}</strong>
                </div>
                <div class="detail-row">
                    <span style="color: #6b7280;">{{ __('shifts-workers.production_rate') }}</span>
                    <strong>{{ number_format($stage['production_rate'], 1) }} {{ __('shifts-workers.pieces_per_hour') }}</strong>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <!-- Hourly Production -->
        <div class="chart-container">
            <div class="chart-header">
                <h3 class="chart-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="20" x2="12" y2="10"></line>
                        <line x1="18" y1="20" x2="18" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="16"></line>
                    </svg>
                    {{ __('shifts-workers.hourly_productivity') }}
                </h3>
            </div>
            <div class="chart-wrapper">
                <canvas id="hourlyChart"></canvas>
            </div>
        </div>

        <!-- Stage Distribution -->
        <div class="chart-container">
            <div class="chart-header">
                <h3 class="chart-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                        <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                    </svg>
                    {{ __('shifts-workers.stage_distribution') }}
                </h3>
            </div>
            <div class="chart-wrapper">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="leaderboard">
        <div class="chart-header">
            <h3 class="chart-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2z"></path>
                </svg>
                {{ __('shifts-workers.top_performers') }}
            </h3>
        </div>
        @forelse($topPerformers as $index => $performer)
            <div class="leaderboard-item">
                <div class="rank-number rank-{{ $index < 3 ? ($index + 1) : 'other' }}">
                    {{ $index + 1 }}
                </div>
                <div class="worker-info">
                    <p class="worker-name">{{ $performer['worker_name'] }}</p>
                    <p class="worker-stats">
                        {{ __('shifts-workers.worker_stats_format', [
                            'items' => $performer['items'],
                            'output' => number_format($performer['output'], 2),
                            'waste' => number_format($performer['waste_pct'], 1)
                        ]) }}
                    </p>
                </div>
                <div class="efficiency-badge efficiency-{{ $performer['efficiency'] >= 95 ? 'excellent' : ($performer['efficiency'] >= 90 ? 'good' : 'average') }}">
                    {{ number_format($performer['efficiency'], 1) }}% {{ __('shifts-workers.efficiency_label') }}
                </div>
            </div>
        @empty
            <p style="text-align: center; color: var(--gray-600); padding: 2rem;">{{ __('shifts-workers.no_data_for_shift') }}</p>
        @endforelse
    </div>

    <!-- Worker Attendance -->
    <div class="comparison-card">
        <h3 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            {{ __('shifts-workers.worker_attendance', ['count' => count($attendance)]) }}
        </h3>
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>{{ __('shifts-workers.attendance_table_headers.number') }}</th>
                    <th>{{ __('shifts-workers.attendance_table_headers.name') }}</th>
                    <th>{{ __('shifts-workers.attendance_table_headers.code') }}</th>
                    <th>{{ __('shifts-workers.attendance_table_headers.position') }}</th>
                    <th>{{ __('shifts-workers.attendance_table_headers.stages') }}</th>
                    <th>{{ __('shifts-workers.attendance_table_headers.pieces') }}</th>
                    <th>{{ __('shifts-workers.attendance_table_headers.output_kg') }}</th>
                    <th>{{ __('shifts-workers.attendance_table_headers.waste_kg') }}</th>
                    <th>{{ __('shifts-workers.attendance_table_headers.efficiency') }}</th>
                    <th>{{ __('shifts-workers.attendance_table_headers.hours') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendance as $index => $worker)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $worker['worker_name'] }}</strong></td>
                    <td>{{ $worker['worker_code'] }}</td>
                    <td>{{ $worker['position'] }}</td>
                    <td>
                        @foreach($worker['stages_worked'] as $stage)
                            <span style="display: inline-block; padding: 0.125rem 0.5rem; background: #e0f2fe; color: #0369a1; border-radius: 4px; font-size: 0.75rem; margin: 0 0.125rem;">
                                {{ __('shifts-workers.stage_label', ['number' => $stage]) }}
                            </span>
                        @endforeach
                    </td>
                    <td>{{ number_format($worker['total_items']) }}</td>
                    <td>{{ number_format($worker['total_output'], 2) }} {{ __('shifts-workers.kg') }}</td>
                    <td style="color: #dc2626;">{{ number_format($worker['total_waste'], 2) }} {{ __('shifts-workers.kg') }}</td>
                    <td>
                        <span class="efficiency-badge efficiency-{{ $worker['efficiency'] >= 95 ? 'excellent' : ($worker['efficiency'] >= 90 ? 'good' : 'average') }}">
                            {{ number_format($worker['efficiency'], 1) }}%
                        </span>
                    </td>
                    <td>{{ $worker['hours_worked'] }} {{ __('shifts-workers.hours_short') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align: center; color: #6b7280; padding: 2rem;">{{ __('shifts-workers.no_attendance_data') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Shift Handovers -->
    @if(count($handovers) > 0)
    <div class="comparison-card">
        <h3 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <polyline points="17 11 19 13 23 9"></polyline>
            </svg>
            {{ __('shifts-workers.shift_handovers_list', ['count' => count($handovers)]) }}
        </h3>
        @foreach($handovers as $handover)
        <div class="handover-card">
            <div class="handover-header">
                <div>
                    <strong>{{ $handover['stage_name'] }}</strong>
                    <span style="margin: 0 0.5rem; color: #6b7280;">•</span>
                    <span style="color: #6b7280;">{{ __('shifts-workers.from_to_format', ['from' => $handover['from_user'], 'to' => $handover['to_user']]) }}</span>
                </div>
                @if($handover['supervisor_approved'])
                <span class="handover-badge approved-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    {{ __('shifts-workers.approved_status') }}
                </span>
                @else
                <span class="handover-badge">{{ __('shifts-workers.pending_status') }}</span>
                @endif
            </div>
            <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">
                {{ __('shifts-workers.items_transferred', ['count' => $handover['items_count']]) }} • {{ __('shifts-workers.handover_time_label', ['time' => $handover['handover_time']->format('H:i')]) }}
            </div>
            @if($handover['notes'])
            <div style="padding: 0.75rem; background: #f9fafb; border-radius: 6px; font-size: 0.875rem;">
                {{ $handover['notes'] }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Active Teams -->
    @if(count($activeTeams) > 0)
    <div class="comparison-card">
        <h3 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            {{ __('shifts-workers.active_teams_list', ['count' => count($activeTeams)]) }}
        </h3>
        <div class="teams-grid">
            @foreach($activeTeams as $team)
            <div class="team-card">
                <div class="team-header">
                    <div class="team-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #1f2937;">{{ $team['team_name'] }}</div>
                        <div style="font-size: 0.875rem; color: #6b7280;">{{ $team['team_code'] }}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <span style="color: #6b7280;">{{ __('shifts-workers.team_members') }}</span>
                    <strong>{{ __('shifts-workers.active_members_format', ['active' => $team['active_members'], 'total' => $team['total_members']]) }}</strong>
                </div>
                <div class="detail-row">
                    <span style="color: #6b7280;">{{ __('shifts-workers.total_team_production') }}</span>
                    <strong>{{ number_format($team['total_production']) }} {{ __('shifts-workers.pieces') }}</strong>
                </div>
                <div class="detail-row">
                    <span style="color: #6b7280;">{{ __('shifts-workers.average_per_member') }}</span>
                    <strong>{{ number_format($team['avg_per_member'], 1) }} {{ __('shifts-workers.pieces') }}</strong>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const primaryBlue = '#0066B2';
    const successGreen = '#10b981';
    const warningOrange = '#f59e0b';
    const dangerRed = '#ef4444';

    Chart.defaults.font.family = '{{ app()->getLocale() == "ar" ? "Cairo" : (app()->getLocale() == "ur" ? "Noto Nastaliq Urdu" : "Roboto") }}, sans-serif';
    Chart.defaults.color = '#475569';

    // Hourly Production Chart
    const hourlyData = @json($hourlyTrend);
    const hourlyHours = hourlyData.map(d => d.hour + ':00');
    const hourlyItems = hourlyData.map(d => d.items);

    new Chart(document.getElementById('hourlyChart'), {
        type: 'bar',
        data: {
            labels: hourlyHours,
            datasets: [{
                label: '{{ __("shifts-workers.pieces_count_chart") }}',
                data: hourlyItems,
                backgroundColor: primaryBlue,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    rtl: {{ app()->getLocale() == 'ar' || app()->getLocale() == 'ur' ? 'true' : 'false' }},
                    callbacks: {
                        label: function(context) {
                            return '{{ __("shifts-workers.pieces_count_chart") }}: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Stage Distribution Chart
    const stageData = @json($byStage);
    const stageLabels = stageData.map(s => s.name);
    const stageItems = stageData.map(s => s.items);

    new Chart(document.getElementById('distributionChart'), {
        type: 'doughnut',
        data: {
            labels: stageLabels,
            datasets: [{
                data: stageItems,
                backgroundColor: [primaryBlue, warningOrange, '#8b5cf6', successGreen],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    rtl: {{ app()->getLocale() == 'ar' || app()->getLocale() == 'ur' ? 'true' : 'false' }},
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    rtl: {{ app()->getLocale() == 'ar' || app()->getLocale() == 'ur' ? 'true' : 'false' }},
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    function applyFilters() {
        const date = document.getElementById('dateFilter').value;
        const fromDate = document.getElementById('fromDateFilter').value;
        const toDate = document.getElementById('toDateFilter').value;
        const shift = document.getElementById('shiftFilter').value;
        window.location.href = `{{ route('manufacturing.reports.shift-dashboard') }}?date=${date}&shift=${shift}&from_date=${fromDate}&to_date=${toDate}`;
    }

    function refreshData() {
        location.reload();
    }

    setInterval(() => {
        const now = new Date();
        const seconds = Math.floor((now - startTime) / 1000);
        const minutes = Math.floor(seconds / 60);

        if (seconds < 60) {
            document.getElementById('lastUpdateTime').textContent = '{{ __("shifts-workers.now") }}';
        } else if (minutes === 1) {
            document.getElementById('lastUpdateTime').textContent = '{{ __("shifts-workers.one_minute_ago") }}';
        } else if (minutes < 60) {
            document.getElementById('lastUpdateTime').textContent = `{{ __("shifts-workers.minutes_ago", ["minutes" => ""]) }}`.replace('', minutes);
        }
    }, 5000);

    const startTime = new Date();
</script>

@endsection
