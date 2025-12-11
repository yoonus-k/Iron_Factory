@extends('master')

@section('title', __('stage3_report.page_title'))

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/stage1-report.css') }}">

<div class="report-container">
    <!-- Header -->
    <div class="report-header">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h1>
                    <i class="fas fa-palette"></i>
                    {{ __('stage3_report.page_title') }}
                </h1>
                <p>🏭 {{ __('stage3_report.system_name') }}</p>
            </div>
            <div class="report-date">
                <div style="font-weight: 600; margin-bottom: 5px;">{{ date('Y-m-d H:i') }}</div>
                <div style="font-size: 12px;">{{ __('stage3_report.current_report') }}</div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <!-- إجمالي الملفات -->
        <div class="kpi-card success">
            <div class="kpi-icon">📦</div>
            <div class="kpi-label">{{ __('stage3_report.total_files') }}</div>
            <div class="kpi-value">{{ $stage3Total ?? 0 }}</div>
            <div class="kpi-unit">{{ __('stage3_report.unit_file') }}</div>
            <div class="kpi-change positive">
                ↑ {{ $stage3Today ?? 0 }} {{ __('stage3_report.today') }}
            </div>
        </div>

        <!-- الملفات المكتملة -->
        <div class="kpi-card success">
            <div class="kpi-icon">✅</div>
            <div class="kpi-label">{{ __('stage3_report.completed_files') }}</div>
            <div class="kpi-value">{{ $stage3CompletedCount ?? 0 }}</div>
            <div class="kpi-unit">{{ $stage3CompletionRate ?? 0 }}%</div>
            <div class="kpi-change positive">
                ✓ {{ __('stage3_report.ready_for_delivery') }}
            </div>
        </div>

        <!-- الملفات المعبأة -->
        <div class="kpi-card warning">
            <div class="kpi-icon">📦</div>
            <div class="kpi-label">{{ __('stage3_report.packed_files') }}</div>
            <div class="kpi-value">{{ $stage3StatusPacked ?? 0 }}</div>
            <div class="kpi-unit">{{ __('stage3_report.ready_for_shipping') }}</div>
            <div class="kpi-change">
                ⚠️ {{ __('stage3_report.ready_for_shipping') }}
            </div>
        </div>

        <!-- إجمالي وزن القاعدة -->
        <div class="kpi-card info">
            <div class="kpi-icon">📥</div>
            <div class="kpi-label">{{ __('stage3_report.total_base_weight') }}</div>
            <div class="kpi-value">{{ $stage3TotalBaseWeight ?? 0 }}</div>
            <div class="kpi-unit">{{ __('stage3_report.unit_kg') }}</div>
            <div class="kpi-change">
                🏭 {{ __('stage3_report.from_stage2') }}
            </div>
        </div>

        <!-- الوزن الكلي النهائي -->
        <div class="kpi-card success">
            <div class="kpi-icon">📤</div>
            <div class="kpi-label">{{ __('stage3_report.total_final_weight') }}</div>
            <div class="kpi-value">{{ $stage3TotalWeight ?? 0 }}</div>
            <div class="kpi-unit">{{ __('stage3_report.unit_kg') }}</div>
            <div class="kpi-change positive">
                ✓ {{ __('stage3_report.ready_for_delivery') }}
            </div>
        </div>

        <!-- إجمالي الهدر -->
        <div class="kpi-card danger">
            <div class="kpi-icon">♻️</div>
            <div class="kpi-label">{{ __('stage3_report.total_waste') }}</div>
            <div class="kpi-value">{{ $stage3TotalWaste ?? 0 }}</div>
            <div class="kpi-unit">{{ __('stage3_report.unit_kg') }}</div>
            <div class="kpi-change">
                📊 {{ __('stage3_report.average') }}: {{ $stage3AvgWastePercentage ?? 0 }}%
            </div>
        </div>

        <!-- أعلى نسبة هدر -->
        <div class="kpi-card danger">
            <div class="kpi-icon">⚠️</div>
            <div class="kpi-label">{{ __('stage3_report.highest_waste') }}</div>
            <div class="kpi-value">{{ $stage3MaxWastePercentage ?? 0 }}%</div>
            <div class="kpi-unit">{{ __('stage3_report.file_label') }}: {{ $stage3MaxWasteBarcode ?? '-' }}</div>
            <div class="kpi-change negative">
                🔴 {{ __('stage3_report.alert_attention') }}
            </div>
        </div>

        <!-- أقل نسبة هدر -->
        <div class="kpi-card success">
            <div class="kpi-icon">🎯</div>
            <div class="kpi-label">{{ __('stage3_report.lowest_waste') }}</div>
            <div class="kpi-value">{{ $stage3MinWastePercentage ?? 0 }}%</div>
            <div class="kpi-unit">{{ __('stage3_report.file_label') }}: {{ $stage3MinWasteBarcode ?? '-' }}</div>
            <div class="kpi-change positive">
                ✓ {{ __('stage3_report.excellent') }}
            </div>
        </div>

        <!-- عدد العمال -->
        <div class="kpi-card info">
            <div class="kpi-icon">👥</div>
            <div class="kpi-label">{{ __('stage3_report.active_workers') }}</div>
            <div class="kpi-value">{{ $stage3ActiveWorkers ?? 0 }}</div>
            <div class="kpi-unit">{{ __('stage3_report.unit_worker') }}</div>
            <div class="kpi-change">
                👨‍🔧 {{ __('stage3_report.in_this_period') }}
            </div>
        </div>

        <!-- متوسط الأداء اليومي -->
        <div class="kpi-card success">
            <div class="kpi-icon">📈</div>
            <div class="kpi-label">{{ __('stage3_report.avg_daily_production') }}</div>
            <div class="kpi-value">{{ $stage3AvgDailyProduction ?? 0 }}</div>
            <div class="kpi-unit">{{ __('stage3_report.unit_per_day') }}</div>
            <div class="kpi-change positive">
                ↑ {{ __('stage3_report.positive_growth') }}
            </div>
        </div>

        <!-- كفاءة الإنتاج -->
        <div class="kpi-card success">
            <div class="kpi-icon">✓</div>
            <div class="kpi-label">{{ __('stage3_report.production_efficiency') }}</div>
            <div class="kpi-value">{{ $stage3ProductionEfficiency ?? 0 }}%</div>
            <div class="kpi-unit">{{ __('stage3_report.efficiency_rate') }}</div>
            <div class="kpi-change positive">
                ✓ {{ __('stage3_report.excellent') }}
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    @if(($stage3StatusInProcess ?? 0) > 0)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>{{ __('stage3_report.alert_warning') }}:</strong> {{ __('stage3_report.coloring_issues_msg', ['count' => $stage3StatusInProcess ?? 0]) }}
    </div>
    @endif

    @if(($stage3MaxWastePercentage ?? 0) > 15)
    <div class="alert alert-danger">
        <i class="fas fa-alert-circle"></i>
        <strong>{{ __('stage3_report.alert_danger') }}:</strong> {{ __('stage3_report.high_waste_detected') }} ({{ $stage3MaxWastePercentage }}%) - {{ __('stage3_report.requires_review') }}
    </div>
    @endif

    @if(($stage3AvgWastePercentage ?? 0) < 5)
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <strong>{{ __('stage3_report.alert_success') }}:</strong> {{ __('stage3_report.optimal_error_level') }} ({{ $stage3AvgWastePercentage }}%)
    </div>
    @endif

    <!-- Filters Section -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-filter"></i>
            {{ __('stage3_report.filters') }}
        </div>

        <form method="GET" action="{{ route('manufacturing.reports.stage3-management') }}" style="margin-top: 15px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">

                <!-- البحث بالباركود -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">🔍 {{ __('stage3_report.search_barcode') }}</label>
                    <input type="text" name="search" class="um-form-control" placeholder="{{ __('stage3_report.barcode_placeholder') }}" value="{{ $filters['search'] ?? '' }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                </div>

                <!-- التصفية بالحالة -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📊 {{ __('stage3_report.status_label') }}</label>
                    <select name="status" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="">{{ __('stage3_report.all_statuses') }}</option>
                        <option value="created" {{ ($filters['status'] ?? '') === 'created' ? 'selected' : '' }}>{{ __('stage3_report.status_created') }}</option>
                        <option value="in_process" {{ ($filters['status'] ?? '') === 'in_process' ? 'selected' : '' }}>{{ __('stage3_report.status_in_process') }}</option>
                        <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>{{ __('stage3_report.status_completed') }}</option>
                        <option value="packed" {{ ($filters['status'] ?? '') === 'packed' ? 'selected' : '' }}>{{ __('stage3_report.status_packed') }}</option>
                    </select>
                </div>

                <!-- التصفية بالعامل -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">👤 {{ __('stage3_report.worker_label') }}</label>
                    <select name="worker_id" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="">{{ __('stage3_report.all_workers') }}</option>
                        @foreach($stage3Workers as $worker)
                        <option value="{{ $worker->id }}" {{ ($filters['worker_id'] ?? '') == $worker->id ? 'selected' : '' }}>{{ $worker->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- من التاريخ -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📅 {{ __('stage3_report.from_date') }}</label>
                    <input type="date" name="from_date" class="um-form-control" value="{{ $filters['from_date'] ?? '' }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                </div>

                <!-- إلى التاريخ -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📅 {{ __('stage3_report.to_date') }}</label>
                    <input type="date" name="to_date" class="um-form-control" value="{{ $filters['to_date'] ?? '' }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                </div>

                <!-- مستوى الهدر -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">⚠️ {{ __('stage3_report.waste_level_label') }}</label>
                    <select name="waste_level" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="">{{ __('stage3_report.all_levels') }}</option>
                        <option value="safe" {{ ($filters['waste_level'] ?? '') === 'safe' ? 'selected' : '' }}>{{ __('stage3_report.waste_safe') }}</option>
                        <option value="warning" {{ ($filters['waste_level'] ?? '') === 'warning' ? 'selected' : '' }}>{{ __('stage3_report.waste_warning') }}</option>
                        <option value="critical" {{ ($filters['waste_level'] ?? '') === 'critical' ? 'selected' : '' }}>{{ __('stage3_report.waste_critical') }}</option>
                    </select>
                </div>

                <!-- الترتيب -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">🔄 {{ __('stage3_report.sort_by_label') }}</label>
                    <select name="sort_by" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>{{ __('stage3_report.sort_by_date') }}</option>
                        <option value="weight" {{ request('sort_by') === 'weight' ? 'selected' : '' }}>{{ __('stage3_report.sort_by_weight') }}</option>
                        <option value="waste" {{ request('sort_by') === 'waste' ? 'selected' : '' }}>{{ __('stage3_report.sort_by_waste') }}</option>
                        <option value="barcode" {{ request('sort_by') === 'barcode' ? 'selected' : '' }}>{{ __('stage3_report.sort_by_barcode') }}</option>
                    </select>
                </div>

                <!-- ترتيب تصاعدي/تنازلي -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📈 {{ __('stage3_report.direction_label') }}</label>
                    <select name="sort_order" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>{{ __('stage3_report.descending') }}</option>
                        <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>{{ __('stage3_report.ascending') }}</option>
                    </select>
                </div>
            </div>

            <!-- أزرار الإجراء -->
            <div style="display: flex; gap: 10px; margin-top: 15px;">
                <button type="submit" class="um-btn um-btn-primary" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-search"></i> {{ __('stage3_report.search_button') }}
                </button>
                <a href="{{ route('manufacturing.reports.stage3-management') }}" class="um-btn um-btn-outline" style="padding: 10px 20px; background: #ecf0f1; color: var(--dark); border: none; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block;">
                    <i class="fas fa-redo"></i> {{ __('stage3_report.reset_filters') }}
                </a>
            </div>
        </form>
    </div>

    <!-- جدول السجلات الكاملة -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-table"></i>
            {{ __('stage3_report.all_files') }} ({{ $stage3Records->count() }} {{ __('stage3_report.file_count') }})
        </div>

        @if($stage3Records && count($stage3Records) > 0)
        <div style="overflow-x: auto; margin-top: 15px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('stage3_report.table_no') }}</th>
                        <th>{{ __('stage3_report.table_barcode') }}</th>
                        <th>{{ __('stage3_report.table_color') }}</th>
                        <th>{{ __('stage3_report.table_coil_number') }}</th>
                        <th>{{ __('stage3_report.table_total_weight') }}</th>
                        <th>{{ __('stage3_report.table_net_weight') }}</th>
                        <th>{{ __('stage3_report.table_waste') }}</th>
                        <th>{{ __('stage3_report.table_waste_percentage') }}</th>
                        <th>{{ __('stage3_report.table_status') }}</th>
                        <th>{{ __('stage3_report.table_worker') }}</th>
                        <th>{{ __('stage3_report.table_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stage3Records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $record->barcode ?? '-' }}</strong></td>
                        <td>{{ $record->color ?? '-' }}</td>
                        <td>{{ $record->coil_number ?? '-' }}</td>
                        <td style="text-align: center;">{{ $record->total_weight ?? 0 }} {{ __('stage3_report.unit_kg') }}</td>
                        <td style="text-align: center;">{{ round(($record->total_weight ?? 0) - ($record->waste ?? 0), 2) }} {{ __('stage3_report.unit_kg') }}</td>
                        <td style="text-align: center;">{{ $record->waste ?? 0 }} {{ __('stage3_report.unit_kg') }}</td>
                        <td style="text-align: center;">
                            @php
                                $wastePerc = ($record->total_weight ?? 0) > 0 ? round((($record->waste ?? 0) / ($record->total_weight ?? 0)) * 100, 2) : 0;
                                $wasteClass = $wastePerc > 12 ? 'critical' : ($wastePerc > 8 ? 'warning' : 'safe');
                            @endphp
                            <span class="waste-level {{ $wasteClass }}">{{ $wastePerc }}%</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge status-{{ $record->status ?? 'created' }}">
                                @if($record->status === 'created')
                                    {{ __('stage3_report.status_created') }}
                                @elseif($record->status === 'in_process')
                                    {{ __('stage3_report.status_in_process') }}
                                @elseif($record->status === 'completed')
                                    {{ __('stage3_report.status_completed') }}
                                @elseif($record->status === 'packed')
                                    {{ __('stage3_report.status_packed') }}
                                @else
                                    {{ $record->status }}
                                @endif
                            </span>
                        </td>
                        <td>{{ $record->created_by_name ?? '-' }}</td>
                        <td>
                            @if ($record->created_at)
                                @if (is_string($record->created_at))
                                    {{ substr($record->created_at, 0, 16) }}
                                @else
                                    {{ $record->created_at->format('Y-m-d H:i') }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 30px; color: #7f8c8d;">
                            <i class="fas fa-inbox"></i> {{ __('stage3_report.no_records_found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
            <p>{{ __('stage3_report.no_records') }}</p>
        </div>
        @endif
    </div>

    <!-- Detailed Statistics Section -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-bar-chart"></i>
            {{ __('stage3_report.detailed_statistics') }}
        </div>

        <div class="stat-row">
            <div class="stat-item success">
                <div class="stat-label">{{ __('stage3_report.completion_rate') }}</div>
                <div class="stat-value">{{ $stage3CompletionRate ?? 0 }}%</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stage3CompletionRate ?? 0 }}%"></div>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-label">{{ __('stage3_report.waste_rate') }}</div>
                <div class="stat-value">{{ $stage3AvgWastePercentage ?? 0 }}%</div>
                <div class="progress-bar">
                    <div class="progress-fill {{ ($stage3AvgWastePercentage ?? 0) > 12 ? 'danger' : (($stage3AvgWastePercentage ?? 0) > 8 ? 'warning' : '') }}" style="width: {{ min($stage3AvgWastePercentage ?? 0, 100) }}%"></div>
                </div>
            </div>

            <div class="stat-item success">
                <div class="stat-label">{{ __('stage3_report.production_efficiency') }}</div>
                <div class="stat-value">{{ $stage3ProductionEfficiency ?? 0 }}%</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stage3ProductionEfficiency ?? 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-pie-chart"></i>
            {{ __('stage3_report.status_distribution') }}
        </div>

        <div class="stat-row">
            <div class="stat-item">
                <div class="stat-label">{{ __('stage3_report.created') }}</div>
                <div class="stat-value" style="color: #3498db;">{{ $stage3StatusCreated ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ round(($stage3StatusCreated ?? 0) / max($stage3Total, 1) * 100) }}%</small>
            </div>

            <div class="stat-item warning">
                <div class="stat-label">{{ __('stage3_report.in_process') }}</div>
                <div class="stat-value" style="color: #f39c12;">{{ $stage3StatusInProcess ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ round(($stage3StatusInProcess ?? 0) / max($stage3Total, 1) * 100) }}%</small>
            </div>

            <div class="stat-item success">
                <div class="stat-label">{{ __('stage3_report.completed') }}</div>
                <div class="stat-value" style="color: #27ae60;">{{ $stage3StatusCompleted ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ round(($stage3StatusCompleted ?? 0) / max($stage3Total, 1) * 100) }}%</small>
            </div>

            <div class="stat-item">
                <div class="stat-label">{{ __('stage3_report.packed') }}</div>
                <div class="stat-value" style="color: #8e44ad;">{{ $stage3StatusPacked ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ round(($stage3StatusPacked ?? 0) / max($stage3Total, 1) * 100) }}%</small>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-trophy"></i>
            {{ __('stage3_report.top_performers') }}
        </div>

        <div class="two-column">
            <!-- Best Worker -->
            <div>
                <h4 style="margin-bottom: 15px; color: var(--dark);">🏆 {{ __('stage3_report.best_worker') }}</h4>
                <div class="stat-item success">
                    <div class="stat-label">{{ __('stage3_report.name') }}</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $stage3BestWorkerName ?? __('stage3_report.not_available') }}</div>
                    <hr style="margin: 10px 0; border: none; border-top: 1px solid var(--light);">
                    <div class="stat-label">{{ __('stage3_report.stands_count') }}</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $stage3BestWorkerCount ?? 0 }}</div>
                    <div class="stat-label" style="margin-top: 10px;">{{ __('stage3_report.avg_waste') }}</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $stage3BestWorkerAvgWaste ?? 0 }}%</div>
                </div>
            </div>

            <!-- Best Stand -->
            <div>
                <h4 style="margin-bottom: 15px; color: var(--dark);">⭐ {{ __('stage3_report.best_stand') }}</h4>
                <div class="stat-item success">
                    <div class="stat-label">{{ __('stage3_report.stand_number') }}</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $bestStandNumber ?? __('stage3_report.not_available') }}</div>
                    <hr style="margin: 10px 0; border: none; border-top: 1px solid var(--light);">
                    <div class="stat-label">{{ __('stage3_report.waste_percentage') }}</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $bestStandWaste ?? 0 }}%</div>
                    <div class="stat-label" style="margin-top: 10px;">{{ __('stage3_report.usage_count') }}</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $bestStandUsageCount ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Records Table -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-list"></i>
            {{ __('stage3_report.last_10_records') }}
        </div>

        @if($stage3Records && count($stage3Records) > 0)
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('stage3_report.table_no') }}</th>
                        <th>{{ __('stage3_report.table_barcode') }}</th>
                        <th>{{ __('stage3_report.table_color') }}</th>
                        <th>{{ __('stage3_report.table_net_weight') }}</th>
                        <th>{{ __('stage3_report.table_waste') }}</th>
                        <th>{{ __('stage3_report.table_waste_percentage') }}</th>
                        <th>{{ __('stage3_report.table_status') }}</th>
                        <th>{{ __('stage3_report.table_worker') }}</th>
                        <th>{{ __('stage3_report.table_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stage3Records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $record->barcode ?? '-' }}</strong></td>
                        <td>{{ $record->color ?? '-' }}</td>
                        <td>{{ round(($record->total_weight ?? 0) - ($record->waste ?? 0), 2) }} {{ __('stage3_report.unit_kg') }}</td>
                        <td>{{ $record->waste ?? 0 }} {{ __('stage3_report.unit_kg') }}</td>
                        <td>
                            @php
                                $wastePerc = ($record->total_weight ?? 0) > 0 ? round((($record->waste ?? 0) / ($record->total_weight ?? 0)) * 100, 2) : 0;
                                $class = $wastePerc > 12 ? 'critical' : ($wastePerc > 8 ? 'warning' : 'safe');
                            @endphp
                            <span class="waste-level {{ $class }}">{{ $wastePerc }}%</span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $record->status ?? 'created' }}">
                                @if($record->status === 'created')
                                    {{ __('stage3_report.status_created') }}
                                @elseif($record->status === 'in_process')
                                    {{ __('stage3_report.status_in_process') }}
                                @elseif($record->status === 'completed')
                                    {{ __('stage3_report.status_completed') }}
                                @elseif($record->status === 'packed')
                                    {{ __('stage3_report.status_packed') }}
                                @else
                                    {{ $record->status }}
                                @endif
                            </span>
                        </td>
                        <td>{{ $record->created_by_name ?? '-' }}</td>
                        <td>
                            @if ($record->created_at)
                                @if (is_string($record->created_at))
                                    {{ substr($record->created_at, 0, 16) }}
                                @else
                                    {{ $record->created_at->format('Y-m-d H:i') }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 20px; color: #7f8c8d;">
                            <i class="fas fa-inbox"></i> {{ __('stage3_report.no_records_yet') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <i class="fas fa-chart-line" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
            <p>{{ __('stage3_report.no_data_yet') }}</p>
        </div>
        @endif
    </div>

    <!-- Waste Analysis -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-eye"></i>
            {{ __('stage3_report.waste_analysis') }}
        </div>

        <div class="stat-row">
            <div class="stat-item success">
                <div class="stat-label">{{ __('stage3_report.acceptable_waste') }}</div>
                <div class="stat-value" style="color: #27ae60;">{{ $stage3AcceptableWaste ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ __('stage3_report.stands') }}</small>
            </div>

            <div class="stat-item warning">
                <div class="stat-label">{{ __('stage3_report.warning_waste') }}</div>
                <div class="stat-value" style="color: #f39c12;">{{ $stage3WarningWaste ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ __('stage3_report.stands') }} - {{ __('stage3_report.requires_attention') }}</small>
            </div>

            <div class="stat-item danger">
                <div class="stat-label">{{ __('stage3_report.critical_waste') }}</div>
                <div class="stat-value" style="color: #e74c3c;">{{ $stage3CriticalWaste ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ __('stage3_report.stands') }} - {{ __('stage3_report.requires_follow_up') }}</small>
            </div>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border-right: 3px solid var(--primary);">
            <h4 style="margin-top: 0; color: var(--dark);">📊 {{ __('stage3_report.quality_summary') }}</h4>
            <p>{{ __('stage3_report.avg_error_label') }}: <strong>{{ $avgWastePercentage ?? 0 }}%</strong></p>

            <p style="margin-bottom: 0;">
                @if(($avgWastePercentage ?? 0) < 8)
                    <span class="badge badge-success">✓ {{ __('stage3_report.performance_excellent') }}</span>
                @elseif(($avgWastePercentage ?? 0) < 12)
                    <span class="badge badge-warning">⚠️ {{ __('stage3_report.performance_good') }}</span>
                @else
                    <span class="badge badge-danger">⚠️ {{ __('stage3_report.performance_warning') }}</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Material Flow -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-arrow-right"></i>
            {{ __('stage3_report.material_flow') }}
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <div style="text-align: center;">
                <div style="font-size: 28px; color: var(--primary); font-weight: 700;">{{ $stage3TotalBaseWeight ?? 0 }} {{ __('stage3_report.unit_kg') }}</div>
                <div style="color: #7f8c8d; font-size: 13px; margin-top: 5px;">{{ __('stage3_report.input_material') }}</div>
                <div style="color: #95a5a6; font-size: 11px;">{{ __('stage3_report.warehouse_label') }}</div>
            </div>

            <div style="font-size: 32px; color: #bdc3c7;">→</div>

            <div style="text-align: center;">
                <div style="font-size: 28px; color: var(--success); font-weight: 700;">{{ $stage3TotalWeight ?? 0 }} {{ __('stage3_report.unit_kg') }}</div>
                <div style="color: #7f8c8d; font-size: 13px; margin-top: 5px;">{{ __('stage3_report.final_products') }}</div>
                <div style="color: #95a5a6; font-size: 11px;">{{ __('stage3_report.ready_for_delivery_label') }}</div>
            </div>

            <div style="font-size: 32px; color: #bdc3c7;">→</div>

            <div style="text-align: center;">
                <div style="font-size: 28px; color: var(--danger); font-weight: 700;">{{ $stage3TotalWaste ?? 0 }} {{ __('stage3_report.unit_kg') }}</div>
                <div style="color: #7f8c8d; font-size: 13px; margin-top: 5px;">{{ __('stage3_report.waste_kg') }}</div>
                <div style="color: #95a5a6; font-size: 11px;">{{ round(($stage3TotalWaste ?? 0) / max($stage3TotalBaseWeight, 1) * 100) }}%</div>
            </div>
        </div>
    </div>

    <!-- Daily Operations Timeline -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-history"></i>
            {{ __('stage3_report.daily_operations') }}
        </div>

        @if($stage3DailyOperations && count($stage3DailyOperations) > 0)
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('stage3_report.date') }}</th>
                        <th>{{ __('stage3_report.count') }}</th>
                        <th>{{ __('stage3_report.total_input') }}</th>
                        <th>{{ __('stage3_report.total_output') }}</th>
                        <th>{{ __('stage3_report.daily_waste') }}</th>
                        <th>{{ __('stage3_report.daily_avg_waste') }}</th>
                        <th>{{ __('stage3_report.daily_completed') }}</th>
                        <th>{{ __('stage3_report.daily_packed') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stage3DailyOperations as $day)
                    <tr>
                        <td><strong>{{ $day['date'] }}</strong></td>
                        <td style="text-align: center;">
                            <span class="badge badge-primary">{{ $day['count'] }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--primary); font-weight: 600;">{{ $day['total_base_weight'] }} {{ __('stage3_report.unit_kg') }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--success); font-weight: 600;">{{ $day['total_weight'] }} {{ __('stage3_report.unit_kg') }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--danger); font-weight: 600;">{{ $day['total_waste'] }} {{ __('stage3_report.unit_kg') }}</span>
                        </td>
                        <td style="text-align: center;">
                            @php
                                $wasteClass = $day['avg_waste'] > 12 ? 'critical' : ($day['avg_waste'] > 8 ? 'warning' : 'safe');
                            @endphp
                            <span class="waste-level {{ $wasteClass }}">{{ $day['avg_waste'] }}%</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge status-completed">{{ $day['completed'] }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge status-packed">{{ $day['packed'] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px; color: #7f8c8d;">
                            <i class="fas fa-inbox"></i> {{ __('stage3_report.no_daily_data') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <i class="fas fa-history" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
            <p>{{ __('stage3_report.no_daily_data') }}</p>
        </div>
        @endif
    </div>

    <!-- Cumulative Progress -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-chart-area"></i>
            {{ __('stage3_report.cumulative_progress') }}
        </div>

        @if($stage3CumulativeData && count($stage3CumulativeData) > 0)
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('stage3_report.date') }}</th>
                        <th>{{ __('stage3_report.cumulative_base_weight') }}</th>
                        <th>{{ __('stage3_report.cumulative_total_weight') }}</th>
                        <th>{{ __('stage3_report.cumulative_waste') }}</th>
                        <th>{{ __('stage3_report.completion_percentage') }}</th>
                        <th>{{ __('stage3_report.waste_percentage_label') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stage3CumulativeData as $day)
                    <tr>
                        <td><strong>{{ $day['date'] }}</strong></td>
                        <td style="text-align: center;">
                            <span style="color: var(--primary); font-weight: 600;">{{ $day['cumulative_base_weight'] }} {{ __('stage3_report.unit_kg') }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--success); font-weight: 600;">{{ $day['cumulative_total_weight'] }} {{ __('stage3_report.unit_kg') }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--danger); font-weight: 600;">{{ $day['cumulative_waste'] }} {{ __('stage3_report.unit_kg') }}</span>
                        </td>
                        <td style="text-align: center;">
                            <div class="progress-bar" style="width: 100px; margin: 0 auto;">
                                <div class="progress-fill" style="width: {{ $day['completion_percentage'] }}%; min-width: 20px;"></div>
                            </div>
                            <small style="color: #7f8c8d; display: block; margin-top: 5px;">{{ $day['completion_percentage'] }}%</small>
                        </td>
                        <td style="text-align: center;">
                            @php
                                $wastePerc = $day['total_waste_percentage'];
                                $wasteClass = $wastePerc > 12 ? 'critical' : ($wastePerc > 8 ? 'warning' : 'safe');
                            @endphp
                            <span class="waste-level {{ $wasteClass }}">{{ $wastePerc }}%</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #7f8c8d;">
                            <i class="fas fa-inbox"></i> {{ __('stage3_report.no_cumulative_data') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <i class="fas fa-chart-area" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
            <p>{{ __('stage3_report.no_cumulative_data') }}</p>
        </div>
        @endif
    </div>

    <!-- Print Button -->
    <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn btn-primary" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
            <i class="fas fa-print"></i> {{ __('stage3_report.print_report') }}
        </button>
        <button onclick="window.history.back()" class="btn btn-secondary" style="padding: 10px 20px; background: #95a5a6; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; margin-right: 10px;">
            <i class="fas fa-arrow-left"></i> {{ __('stage3_report.back') }}
        </button>
    </div>

    <!-- Footer -->
    <div style="text-align: center; color: #7f8c8d; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--light);">
        <p>{{ __('stage3_report.generated_by') }}</p>
        <p>{{ __('stage3_report.copyright') }} {{ __('stage3_report.footer_text') }}</p>
    </div>
</div>

@endsection
