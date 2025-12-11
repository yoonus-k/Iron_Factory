@extends('master')

@section('title', __('product_tracking_report.page_title'))

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/shift-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/product-tracking-report.css') }}">

@endpush

@section('content')

<div class="tracking-report-container">
    <!-- Header -->
    <div class="report-header">
        <div class="report-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 11l3 3L22 4"></path>
                <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h1>{{ __('product_tracking_report.page_title') }}</h1>
        </div>
        <div style="display: flex; gap: 2rem; flex-wrap: wrap; opacity: 0.95;">
            <div>
                <div style="font-size: 0.875rem; opacity: 0.8;">{{ __('product_tracking_report.time_period') }}</div>
                <div style="font-weight: 600; font-size: 1.125rem;">{{ $dateFrom }} {{ __('product_tracking_report.to') }} {{ $dateTo }}</div>
            </div>
            @if($stage != 'all')
            <div>
                <div style="font-size: 0.875rem; opacity: 0.8;">{{ __('product_tracking_report.stage') }}</div>
                <div style="font-weight: 600; font-size: 1.125rem;">{{ $stage }}</div>
            </div>
            @endif
            @if($worker)
            <div>
                <div style="font-size: 0.875rem; opacity: 0.8;">{{ __('product_tracking_report.worker') }}</div>
                <div style="font-weight: 600; font-size: 1.125rem;">{{ $workers->where('id', $worker)->first()?->name ?? __('product_tracking_report.unspecified') }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('manufacturing.reports.product-tracking') }}">
            <div class="filter-row">
                <div class="filter-group">
                    <label>{{ __('product_tracking_report.from_date') }}</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" required>
                </div>

                <div class="filter-group">
                    <label>{{ __('product_tracking_report.to_date') }}</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" required>
                </div>

                <div class="filter-group">
                    <label>{{ __('product_tracking_report.stage_label') }}</label>
                    <select name="stage">
                        <option value="all">{{ __('product_tracking_report.all_stages') }}</option>
                        @foreach($stages as $st)
                            <option value="{{ $st }}" {{ $stage == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>{{ __('product_tracking_report.status_label') }}</label>
                    <select name="status">
                        <option value="all">{{ __('product_tracking_report.all_statuses') }}</option>
                        <option value="processing" {{ $status == 'processing' ? 'selected' : '' }}>{{ __('product_tracking_report.processing') }}</option>
                        <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>{{ __('product_tracking_report.completed') }}</option>
                        <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>{{ __('product_tracking_report.rejected') }}</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>{{ __('product_tracking_report.worker_label') }}</label>
                    <select name="worker">
                        <option value="">{{ __('product_tracking_report.all_workers') }}</option>
                        @foreach($workers as $w)
                            <option value="{{ $w->worker_id }}" {{ $worker == $w->worker_id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; margin-left: 0.5rem;">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    {{ __('product_tracking_report.search') }}
                </button>
                <a href="{{ route('manufacturing.reports.product-tracking') }}" class="btn-reset">
                    {{ __('product_tracking_report.reset') }}
                </a>
                <button type="button" class="btn-reset" onclick="window.print()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; margin-left: 0.5rem;">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    {{ __('product_tracking_report.print') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-label">📊 {{ __('product_tracking_report.total_records') }}</div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value">{{ number_format($summary['total_records']) }}</div>
                <div class="stat-unit">{{ __('product_tracking_report.operations_count') }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-label">📦 {{ __('product_tracking_report.total_production') }}</div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value">{{ number_format($summary['total_output_kg'], 2) }}</div>
                <div class="stat-unit">{{ __('product_tracking_report.kilogram') }}</div>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-card-header">
                <div class="stat-label">🗑️ {{ __('product_tracking_report.total_waste') }}</div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value">{{ number_format($summary['total_waste_kg'], 2) }}</div>
                <div class="stat-unit">{{ __('product_tracking_report.kilogram') }} ({{ $summary['waste_percentage'] }}%)</div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-card-header">
                <div class="stat-label">⚖️ {{ __('product_tracking_report.avg_waste_per_operation') }}</div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value">{{ number_format($summary['avg_waste_per_record'], 2) }}</div>
                <div class="stat-unit">{{ __('product_tracking_report.kilogram') }}</div>
            </div>
        </div>

        <div class="stat-card info">
            <div class="stat-card-header">
                <div class="stat-label">🏷️ {{ __('product_tracking_report.unique_barcodes') }}</div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value">{{ number_format($summary['unique_barcodes']) }}</div>
                <div class="stat-unit">{{ __('product_tracking_report.unique_product') }}</div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-card-header">
                <div class="stat-label">👥 {{ __('product_tracking_report.workers_count') }}</div>
            </div>
            <div class="stat-card-body">
                <div class="stat-value">{{ number_format($summary['unique_workers']) }}</div>
                <div class="stat-unit">{{ __('product_tracking_report.active_worker') }}</div>
            </div>
        </div>
    </div>
{{-- <div class="card">
<div class="card-body">


    <div class="production-flow-section">
        <div class="production-flow-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
            سير عملية الإنتاج والمراحل
        </div>
        <div class="production-flow-container">
            <div class="production-flow">
                @forelse($byStage as $index => $stage)
                    @if($index > 0)
                    <div class="flow-arrow">→</div>
                    @endif
                    <div class="stage-card {{ $stage['name'] == 'الأولى' ? 'active' : '' }}">
                        <div class="stage-card-header">
                            <div class="stage-number">{{ $index + 1 }}</div>
                            <div class="stage-name">{{ $stage['name'] }}</div>
                        </div>
                        <div class="stage-card-body">
                            <div class="stage-stats">
                                <div class="stage-stats-item">
                                    <span class="stage-stats-label">📊 العمليات:</span>
                                    <span class="stage-stats-value">{{ number_format($stage['count']) }}</span>
                                </div>
                                <div class="stage-stats-item">
                                    <span class="stage-stats-label">📦 الإنتاج:</span>
                                    <span class="stage-stats-value">{{ number_format($stage['output'], 1) }} كجم</span>
                                </div>
                                <div class="stage-stats-item">
                                    <span class="stage-stats-label">🗑️ الهدر:</span>
                                    <span class="stage-stats-value" style="color: #dc2626;">{{ number_format($stage['waste'], 1) }} كجم</span>
                                </div>
                                <div class="stage-stats-item">
                                    <span class="stage-stats-label">📈 نسبة الهدر:</span>
                                    <span class="stage-stats-value">{{ number_format($stage['waste_pct'], 1) }}%</span>
                                </div>
                                <div class="stage-stats-item">
                                    <span class="stage-stats-label">⚡ الكفاءة:</span>
                                    <span class="stage-stats-value" style="color: {{ $stage['efficiency'] >= 95 ? '#10b981' : ($stage['efficiency'] >= 85 ? '#f59e0b' : '#ef4444') }};">
                                        {{ number_format($stage['efficiency'], 1) }}%
                                    </span>
                                </div>
                            </div>
                            @if(isset($workersByStage[$stage['name']]) && count($workersByStage[$stage['name']]) > 0)
                            <div class="stage-workers">
                                <div class="stage-workers-title">👥 العمال ({{ count($workersByStage[$stage['name']]) }})</div>
                                <div class="workers-list">
                                    @foreach($workersByStage[$stage['name']] as $stageWorker)
                                    <div class="worker-badge">
                                        <div class="worker-name">{{ $stageWorker['worker_name'] }}</div>
                                        <div class="worker-info">
                                            <span>⚙️ {{ $stageWorker['count'] }}</span>
                                            <span>📦 {{ $stageWorker['output'] }}كج</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: #9ca3af; padding: 2rem;">
                        لا توجد بيانات عن المراحل
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    </div>
</div> --}}
    <!-- Charts -->
    <div class="charts-grid">
        <!-- Daily Production Trend -->
        <div class="chart-container">
            <div class="chart-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="20" x2="12" y2="10"></line>
                    <line x1="18" y1="20" x2="18" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="16"></line>
                </svg>
                {{ __('product_tracking_report.daily_production') }}
            </div>
            <div class="chart-wrapper">
                <canvas id="dailyTrendChart"></canvas>
            </div>
        </div>

        <!-- Production by Stage -->
        <div class="chart-container">
            <div class="chart-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                </svg>
                {{ __('product_tracking_report.production_by_stage') }}
            </div>
            <div class="chart-wrapper">
                <canvas id="stageDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Production by Stage Details -->
    <div class="data-table">
        <div class="table-header">
            <h3 class="table-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; margin-left: 0.5rem;">
                    <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                    <line x1="3" y1="9" x2="21" y2="9"></line>
                </svg>
                {{ __('product_tracking_report.stage_details_title') }}
            </h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>{{ __('product_tracking_report.stage') }}</th>
                    <th>{{ __('product_tracking_report.operations_count_col') }}</th>
                    <th>{{ __('product_tracking_report.production_kg') }}</th>
                    <th>{{ __('product_tracking_report.waste_kg') }}</th>
                    <th>{{ __('product_tracking_report.waste_ratio') }}</th>
                    <th>{{ __('product_tracking_report.efficiency_col') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byStage as $stage)
                <tr>
                    <td><strong>{{ $stage['name'] }}</strong></td>
                    <td>{{ number_format($stage['count']) }}</td>
                    <td>{{ number_format($stage['output'], 2) }}</td>
                    <td style="color: #dc2626;">{{ number_format($stage['waste'], 2) }}</td>
                    <td><span class="badge {{ $stage['waste_pct'] >= 5 ? 'badge-warning' : 'badge-success' }}">{{ $stage['waste_pct'] }}%</span></td>
                    <td>
                        <span class="badge {{ $stage['efficiency'] >= 95 ? 'badge-success' : ($stage['efficiency'] >= 85 ? 'badge-warning' : 'badge-danger') }}">
                            {{ number_format($stage['efficiency'], 1) }}%
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #9ca3af; padding: 2rem;">لا توجد بيانات</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Top Performing Workers -->
    <div class="data-table">
        <div class="table-header">
            <h3 class="table-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; margin-left: 0.5rem;">
                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2z"></path>
                </svg>
                {{ __('product_tracking_report.top_workers_title') }}
            </h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('product_tracking_report.worker_name') }}</th>
                    <th>{{ __('product_tracking_report.operations_count_col') }}</th>
                    <th>{{ __('product_tracking_report.production_kg') }}</th>
                    <th>{{ __('product_tracking_report.waste_kg') }}</th>
                    <th>{{ __('product_tracking_report.waste_ratio') }}</th>
                    <th>{{ __('product_tracking_report.efficiency_col') }}</th>
                    <th>{{ __('product_tracking_report.total_cost') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topWorkers as $index => $worker)
                <tr>
                    <td><strong>{{ $index + 1 }}</strong></td>
                    <td>{{ $worker['worker_name'] }}</td>
                    <td>{{ number_format($worker['total_records']) }}</td>
                    <td>{{ number_format($worker['total_output'], 2) }}</td>
                    <td style="color: #dc2626;">{{ number_format($worker['total_waste'], 2) }}</td>
                    <td><span class="badge badge-info">{{ $worker['waste_pct'] }}%</span></td>
                    <td>
                        <span class="badge {{ $worker['efficiency'] >= 95 ? 'badge-success' : 'badge-warning' }}">
                            {{ number_format($worker['efficiency'], 1) }}%
                        </span>
                    </td>
                    <td>{{ number_format($worker['total_cost'], 2) }} {{ __('product_tracking_report.sar') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #9ca3af; padding: 2rem;">{{ __('product_tracking_report.no_data') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Waste Analysis -->
    <div class="data-table">
        <div class="table-header">
            <h3 class="table-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; margin-left: 0.5rem;">
                    <polyline points="3 6 5 6 21 6"></polyline>
                </svg>
                {{ __('product_tracking_report.waste_analysis_title') }}
            </h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>{{ __('product_tracking_report.stage') }}</th>
                    <th>{{ __('product_tracking_report.total_waste_kg') }}</th>
                    <th>{{ __('product_tracking_report.avg_waste') }}</th>
                    <th>{{ __('product_tracking_report.max_waste') }}</th>
                    <th>{{ __('product_tracking_report.min_waste') }}</th>
                    <th>{{ __('product_tracking_report.severity') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wasteAnalysis as $waste)
                <tr>
                    <td><strong>{{ $waste['stage'] }}</strong></td>
                    <td style="color: #dc2626; font-weight: 600;">{{ number_format($waste['total_waste'], 2) }}</td>
                    <td>{{ number_format($waste['avg_waste_pct'], 2) }}%</td>
                    <td>{{ number_format($waste['max_waste_pct'], 2) }}%</td>
                    <td>{{ number_format($waste['min_waste_pct'], 2) }}%</td>
                    <td>
                        <span class="badge {{ $waste['severity'] == 'critical' ? 'badge-danger' : ($waste['severity'] == 'warning' ? 'badge-warning' : 'badge-success') }}">
                            {{ $waste['severity'] == 'critical' ? __('product_tracking_report.critical') : ($waste['severity'] == 'warning' ? __('product_tracking_report.warning') : __('product_tracking_report.normal')) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #9ca3af; padding: 2rem;">{{ __('product_tracking_report.no_data') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Top Tracked Barcodes (for tracing) -->
    @if(count($topBarcodes) > 0)
    <div style="margin-bottom: 2rem;">
        <div class="data-table">
            <div class="table-header">
                <h3 class="table-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; margin-left: 0.5rem;">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    {{ __('product_tracking_report.top_tracked_products') }}
                </h3>
            </div>
        </div>

        @foreach($topBarcodes as $barcode)
        <div class="trace-card">
            <div class="trace-header">
                <span class="trace-barcode">{{ $barcode['barcode'] }}</span>
                <span class="badge badge-info">{{ $barcode['track_count'] }} {{ __('product_tracking_report.step') }}</span>
            </div>
            <div class="trace-details">
                <div class="trace-detail-item">
                    <div class="trace-detail-label">{{ __('product_tracking_report.final_production') }}</div>
                    <div class="trace-detail-value">{{ number_format($barcode['total_output'], 2) }} {{ __('product_tracking_report.kg') }}</div>
                </div>
                <div class="trace-detail-item">
                    <div class="trace-detail-label">{{ __('product_tracking_report.waste') }}</div>
                    <div class="trace-detail-value" style="color: #dc2626;">{{ number_format($barcode['total_waste'], 2) }} {{ __('product_tracking_report.kg') }}</div>
                </div>
                <div class="trace-detail-item">
                    <div class="trace-detail-label">{{ __('product_tracking_report.stages') }}</div>
                    <div class="trace-detail-value">{{ $barcode['stages'] }} {{ __('product_tracking_report.stage_count') }}</div>
                </div>
                <div class="trace-detail-item">
                    <div class="trace-detail-label">{{ __('product_tracking_report.workers_label') }}</div>
                    <div class="trace-detail-value">{{ $barcode['workers'] }} {{ __('product_tracking_report.worker_count') }}</div>
                </div>
                <div class="trace-detail-item">
                    <div class="trace-detail-label">{{ __('product_tracking_report.current_status') }}</div>
                    <div class="trace-detail-value">{{ $barcode['current_status'] }}</div>
                </div>
                <div class="trace-detail-item">
                    <div class="trace-detail-label">{{ __('product_tracking_report.last_update') }}</div>
                    <div class="trace-detail-value">{{ $barcode['last_update']->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Tracking Records Table -->
    <div class="data-table">
        <div class="table-header">
            <h3 class="table-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; margin-left: 0.5rem;">
                    <path d="M9 11l3 3L22 4"></path>
                </svg>
                {{ __('product_tracking_report.all_records_title') }}
            </h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>{{ __('product_tracking_report.barcode') }}</th>
                    <th>{{ __('product_tracking_report.stage_col') }}</th>
                    <th>{{ __('product_tracking_report.status_col') }}</th>
                    <th>{{ __('product_tracking_report.input_kg') }}</th>
                    <th>{{ __('product_tracking_report.output_kg') }}</th>
                    <th>{{ __('product_tracking_report.waste_amount_kg') }}</th>
                    <th>{{ __('product_tracking_report.waste_pct') }}</th>
                    <th>{{ __('product_tracking_report.worker_col') }}</th>
                    <th>{{ __('product_tracking_report.date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trackingRecords as $record)
                <tr>
                    <td><code style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">{{ $record->barcode }}</code></td>
                    <td><span class="badge badge-info">{{ $record->stage }}</span></td>
                    <td>{{ $record->action }}</td>
                    <td>{{ number_format($record->input_weight, 2) }}</td>
                    <td>{{ number_format($record->output_weight, 2) }}</td>
                    <td style="color: #dc2626;">{{ number_format($record->waste_amount, 2) }}</td>
                    <td>{{ number_format($record->waste_percentage, 2) }}%</td>
                    <td>{{ $record->worker?->name ?? __('product_tracking_report.unspecified') }}</td>
                    <td>{{ is_string($record->created_at) ? \Carbon\Carbon::parse($record->created_at)->format('d/m/Y H:i') : $record->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #9ca3af; padding: 2rem;">{{ __('product_tracking_report.no_data') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($trackingRecords->hasPages())
    <div style="margin-top: 2rem; display: flex; justify-content: center;">
        {{ $trackingRecords->links() }}
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = 'Cairo, sans-serif';
    Chart.defaults.color = '#6b7280';

    const primaryBlue = '#0066B2';
    const successGreen = '#10b981';
    const warningOrange = '#f59e0b';
    const dangerRed = '#ef4444';

    // Daily Trend Chart
    const dailyData = @json($dailyTrend);
    const dailyLabels = dailyData.map(d => d.day_label);
    const dailyOutput = dailyData.map(d => d.output);
    const dailyWaste = dailyData.map(d => d.waste);

    new Chart(document.getElementById('dailyTrendChart'), {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [
                {
                    label: '{{ __('product_tracking_report.chart_production_label') }}',
                    data: dailyOutput,
                    borderColor: primaryBlue,
                    backgroundColor: 'rgba(0, 102, 178, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: primaryBlue,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: '{{ __('product_tracking_report.chart_waste_label') }}',
                    data: dailyWaste,
                    borderColor: warningOrange,
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: warningOrange,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true, padding: 15 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
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
    const stageCounts = stageData.map(s => s.count);

    new Chart(document.getElementById('stageDistributionChart'), {
        type: 'doughnut',
        data: {
            labels: stageLabels,
            datasets: [{
                data: stageCounts,
                backgroundColor: [primaryBlue, warningOrange, '#8b5cf6', successGreen, dangerRed],
                borderColor: '#fff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15 }
                }
            }
        }
    });
</script>
</div>

@endsection
