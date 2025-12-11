@extends('master')

@section('title', __('stage2_report.page_title'))

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/stage2-report.css') }}">
<div class="report-container">
    <!-- Header -->
    <div class="report-header">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h1>
                    <i class="fas fa-chart-line"></i>
                    {{ __('stage2_report.page_title') }}
                </h1>
                <p>🏭 {{ __('stage2_report.system_name') }}</p>
            </div>
            <div class="report-date">
                <div style="font-weight: 600; margin-bottom: 5px;">{{ date('Y-m-d H:i') }}</div>
                <div style="font-size: 12px;">{{ __('stage2_report.current_report') }}</div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <!-- إجمالي السجلات المعالجة -->
        <div class="kpi-card success">
            <div class="kpi-icon">📦</div>
            <div class="kpi-label">{{ __('stage2_report.total_records') }}</div>
            <div class="kpi-value">{{ $stage2Total ?? 0 }}</div>
            <div class="kpi-unit">{{ __('stage2_report.unit_record') }}</div>
            <div class="kpi-change positive">
                ↑ {{ $stage2Today ?? 0 }} {{ __('stage2_report.today') }}
            </div>
        </div>

        <!-- السجلات المكتملة -->
        <div class="kpi-card success">
            <div class="kpi-icon">✅</div>
            <div class="kpi-label">{{ __('stage2_report.completed_records') }}</div>
            <div class="kpi-value">{{ $stage2CompletedCount ?? 0 }}</div>
            <div class="kpi-unit">{{ $stage2CompletionRate ?? 0 }}%</div>
            <div class="kpi-change positive">
                ✓ {{ __('stage2_report.completed_successfully') }}
            </div>
        </div>

        <!-- السجلات قيد المعالجة -->
        <div class="kpi-card warning">
            <div class="kpi-icon">⏸️</div>
            <div class="kpi-label">{{ __('stage2_report.in_progress_records') }}</div>
            <div class="kpi-value">{{ $stage2StatusInProgress ?? 0 }}</div>
            <div class="kpi-unit">{{ __('stage2_report.processing') }}</div>
            <div class="kpi-change">
                ⚠️ {{ __('stage2_report.under_process') }}
            </div>
        </div>

        <!-- إجمالي المادة الداخلة -->
        <div class="kpi-card info">
            <div class="kpi-icon">📥</div>
            <div class="kpi-label">{{ __('stage2_report.total_input_weight') }}</div>
            <div class="kpi-value">{{ $stage2TotalInputWeight ?? 0 }}</div>
            <div class="kpi-unit">{{ __('stage2_report.unit_kg') }}</div>
            <div class="kpi-change">
                🏭 {{ __('stage2_report.from_stage1') }}
            </div>
        </div>

        <!-- الوزن الصافي الخارج -->
        <div class="kpi-card success">
            <div class="kpi-icon">📤</div>
            <div class="kpi-label">{{ __('stage2_report.output_weight') }}</div>
            <div class="kpi-value">{{ $stage2TotalOutputWeight ?? 0 }}</div>
            <div class="kpi-unit">{{ __('stage2_report.unit_kg') }}</div>
            <div class="kpi-change positive">
                ✓ {{ __('stage2_report.ready_for_stage3') }}
            </div>
        </div>

        <!-- إجمالي الهدر -->
        <div class="kpi-card danger">
            <div class="kpi-icon">♻️</div>
            <div class="kpi-label">{{ __('stage2_report.total_waste') }}</div>
            <div class="kpi-value">{{ $stage2TotalWaste ?? 0 }}</div>
            <div class="kpi-unit">{{ __('stage2_report.unit_kg') }}</div>
            <div class="kpi-change">
                📊 {{ __('stage2_report.average') }}: {{ $stage2AvgWastePercentage ?? 0 }}%
            </div>
        </div>

        <!-- أعلى نسبة هدر -->
        <div class="kpi-card danger">
            <div class="kpi-icon">⚠️</div>
            <div class="kpi-label">{{ __('stage2_report.highest_waste') }}</div>
            <div class="kpi-value">{{ $stage2MaxWastePercentage ?? 0 }}%</div>
            <div class="kpi-unit">{{ __('stage2_report.record_label') }}: {{ $stage2MaxWasteBarcode ?? '-' }}</div>
            <div class="kpi-change negative">
                🔴 {{ __('stage2_report.alert_attention') }}
            </div>
        </div>

        <!-- أقل نسبة هدر -->
        <div class="kpi-card success">
            <div class="kpi-icon">🎯</div>
            <div class="kpi-label">{{ __('stage2_report.lowest_waste') }}</div>
            <div class="kpi-value">{{ $stage2MinWastePercentage ?? 0 }}%</div>
            <div class="kpi-unit">{{ __('stage2_report.record_label') }}: {{ $stage2MinWasteBarcode ?? '-' }}</div>
            <div class="kpi-change positive">
                ✓ {{ __('stage2_report.excellent') }}
            </div>
        </div>

        <!-- عدد العمال -->
        <div class="kpi-card info">
            <div class="kpi-icon">👥</div>
            <div class="kpi-label">{{ __('stage2_report.active_workers') }}</div>
            <div class="kpi-value">{{ $stage2ActiveWorkers ?? 0 }}</div>
            <div class="kpi-unit">{{ __('stage2_report.unit_worker') }}</div>
            <div class="kpi-change">
                👨‍🔧 {{ __('stage2_report.in_this_period') }}
            </div>
        </div>

        <!-- متوسط الأداء اليومي -->
        <div class="kpi-card success">
            <div class="kpi-icon">📈</div>
            <div class="kpi-label">{{ __('stage2_report.avg_daily_production') }}</div>
            <div class="kpi-value">{{ $stage2AvgDailyProduction ?? 0 }}</div>
            <div class="kpi-unit">{{ __('stage2_report.unit_per_day') }}</div>
            <div class="kpi-change positive">
                ↑ {{ __('stage2_report.positive_growth') }}
            </div>
        </div>

        <!-- كفاءة الإنتاج -->
        <div class="kpi-card success">
            <div class="kpi-icon">✓</div>
            <div class="kpi-label">{{ __('stage2_report.production_efficiency') }}</div>
            <div class="kpi-value">{{ $stage2ProductionEfficiency ?? 0 }}%</div>
            <div class="kpi-unit">{{ __('stage2_report.efficiency_rate') }}</div>
            <div class="kpi-change positive">
                ✓ {{ __('stage2_report.excellent') }}
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    @if($stage2StatusInProgress > 0)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>{{ __('stage2_report.alert_warning') }}:</strong> {{ __('stage2_report.in_progress_msg') }}
    </div>
    @endif

    @if($stage2MaxWastePercentage > 15)
    <div class="alert alert-danger">
        <i class="fas fa-alert-circle"></i>
        <strong>{{ __('stage2_report.alert_danger') }}:</strong> {{ __('stage2_report.high_waste_detected') }} ({{ $stage2MaxWastePercentage }}%) - {{ __('stage2_report.requires_review') }}
    </div>
    @endif

    @if($stage2AvgWastePercentage < 5)
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <strong>{{ __('stage2_report.alert_success') }}:</strong> {{ __('stage2_report.optimal_waste_level') }} ({{ $stage2AvgWastePercentage }}%)
    </div>
    @endif

    <!-- Filters Section -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-filter"></i>
            {{ __('stage2_report.filters') }}
        </div>

        <form method="GET" action="{{ route('manufacturing.reports.stage2-management') }}" style="margin-top: 15px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">

                <!-- البحث بالباركود -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">{{ __('stage2_report.search_barcode') }}</label>
                    <input type="text" name="search" class="um-form-control" placeholder="{{ __('stage2_report.barcode_placeholder') }}" value="{{ $filters['search'] ?? '' }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                </div>

                <!-- الحالة -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">{{ __('stage2_report.status_label') }}</label>
                    <select name="status" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="">{{ __('stage2_report.all_statuses') }}</option>
                        <option value="started" {{ ($filters['status'] ?? '') === 'started' ? 'selected' : '' }}>{{ __('stage2_report.status_started') }}</option>
                        <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>{{ __('stage2_report.status_in_progress') }}</option>
                        <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>{{ __('stage2_report.status_completed') }}</option>
                        <option value="consumed" {{ ($filters['status'] ?? '') === 'consumed' ? 'selected' : '' }}>{{ __('stage2_report.status_consumed') }}</option>
                    </select>
                </div>

                <!-- العامل -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">{{ __('stage2_report.worker_label') }}</label>
                    <select name="worker_id" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="">{{ __('stage2_report.all_workers') }}</option>
                        @foreach($stage2Workers as $worker)
                        <option value="{{ $worker->id }}" {{ ($filters['worker_id'] ?? '') == $worker->id ? 'selected' : '' }}>{{ $worker->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- من التاريخ -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📅 {{ __('stage2_report.from_date') }}</label>
                    <input type="date" name="from_date" class="um-form-control" value="{{ $filters['from_date'] ?? '' }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                </div>

                <!-- إلى التاريخ -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📅 {{ __('stage2_report.to_date') }}</label>
                    <input type="date" name="to_date" class="um-form-control" value="{{ $filters['to_date'] ?? '' }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                </div>

                <!-- مستوى الهدر -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">⚠️ {{ __('stage2_report.waste_level_label') }}</label>
                    <select name="waste_level" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="">{{ __('stage2_report.all_levels') }}</option>
                        <option value="safe" {{ ($filters['waste_level'] ?? '') === 'safe' ? 'selected' : '' }}>{{ __('stage2_report.waste_safe') }}</option>
                        <option value="warning" {{ ($filters['waste_level'] ?? '') === 'warning' ? 'selected' : '' }}>{{ __('stage2_report.waste_warning') }}</option>
                        <option value="critical" {{ ($filters['waste_level'] ?? '') === 'critical' ? 'selected' : '' }}>{{ __('stage2_report.waste_critical') }}</option>
                    </select>
                </div>

                <!-- الترتيب -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">🔄 {{ __('stage2_report.sort_by_label') }}</label>
                    <select name="sort_by" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>{{ __('stage2_report.sort_by_date') }}</option>
                        <option value="output_weight" {{ request('sort_by') === 'output_weight' ? 'selected' : '' }}>{{ __('stage2_report.sort_by_weight') }}</option>
                        <option value="waste" {{ request('sort_by') === 'waste' ? 'selected' : '' }}>{{ __('stage2_report.sort_by_waste') }}</option>
                        <option value="barcode" {{ request('sort_by') === 'barcode' ? 'selected' : '' }}>{{ __('stage2_report.sort_by_barcode') }}</option>
                    </select>
                </div>

                <!-- ترتيب تصاعدي/تنازلي -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📈 {{ __('stage2_report.direction_label') }}</label>
                    <select name="sort_order" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>{{ __('stage2_report.descending') }}</option>
                        <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>{{ __('stage2_report.ascending') }}</option>
                    </select>
                </div>
            </div>

            <!-- أزرار الإجراء -->
            <div style="display: flex; gap: 10px; margin-top: 15px;">
                <button type="submit" class="um-btn um-btn-primary" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-search"></i> {{ __('stage2_report.search_button') }}
                </button>
                <a href="{{ route('manufacturing.reports.stage2-management') }}" class="um-btn um-btn-outline" style="padding: 10px 20px; background: #ecf0f1; color: var(--dark); border: none; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block;">
                    <i class="fas fa-redo"></i> {{ __('stage2_report.reset_filters') }}
                </a>
            </div>
        </form>
    </div>

    <!-- جدول السجلات الكاملة -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-table"></i>
            {{ __('stage2_report.all_records') }} ({{ $stage2Records->count() }} {{ __('stage2_report.record_count') }})
        </div>

        @if($stage2Records && count($stage2Records) > 0)
        <div style="overflow-x: auto; margin-top: 15px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('stage2_report.table_no') }}</th>
                        <th>{{ __('stage2_report.table_barcode') }}</th>
                        <th>{{ __('stage2_report.table_parent_barcode') }}</th>
                        <th>{{ __('stage2_report.table_input_weight') }}</th>
                        <th>{{ __('stage2_report.table_output_weight') }}</th>
                        <th>{{ __('stage2_report.table_waste') }}</th>
                        <th>{{ __('stage2_report.table_waste_percentage') }}</th>
                        <th>{{ __('stage2_report.table_status') }}</th>
                        <th>{{ __('stage2_report.table_worker') }}</th>
                        <th>{{ __('stage2_report.table_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stage2Records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $record->barcode ?? '-' }}</strong></td>
                        <td>{{ $record->parent_barcode ?? '-' }}</td>
                        <td style="text-align: center;">{{ number_format($record->input_weight ?? 0, 2) }} {{ __('stage2_report.unit_kg') }}</td>
                        <td style="text-align: center;">{{ number_format($record->output_weight ?? 0, 2) }} {{ __('stage2_report.unit_kg') }}</td>
                        <td style="text-align: center;">{{ number_format($record->waste ?? 0, 2) }} {{ __('stage2_report.unit_kg') }}</td>
                        <td style="text-align: center;">
                            @php
                                $wastePerc = $record->input_weight > 0 ? round(($record->waste / $record->input_weight) * 100, 2) : 0;
                                $wasteClass = $wastePerc > 12 ? 'critical' : ($wastePerc > 8 ? 'warning' : 'safe');
                            @endphp
                            <span class="waste-level {{ $wasteClass }}">{{ $wastePerc }}%</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge status-{{ $record->status ?? 'started' }}">
                                @if($record->status === 'started')
                                    {{ __('stage2_report.status_started') }}
                                @elseif($record->status === 'in_progress')
                                    {{ __('stage2_report.status_in_progress') }}
                                @elseif($record->status === 'completed')
                                    {{ __('stage2_report.status_completed') }}
                                @elseif($record->status === 'consumed')
                                    {{ __('stage2_report.status_consumed') }}
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
                        <td colspan="10" style="text-align: center; padding: 30px; color: #7f8c8d;">
                            <i class="fas fa-inbox"></i> {{ __('stage2_report.no_records_found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
            <p>{{ __('stage2_report.no_records') }}</p>
        </div>
        @endif
    </div>

    <!-- Detailed Statistics Section -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-bar-chart"></i>
            {{ __('stage2_report.detailed_statistics') }}
        </div>

        <div class="stat-row">
            <div class="stat-item success">
                <div class="stat-label">{{ __('stage2_report.completion_rate') }}</div>
                <div class="stat-value">{{ $stage2CompletionRate ?? 0 }}%</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stage2CompletionRate ?? 0 }}%"></div>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-label">{{ __('stage2_report.waste_rate') }}</div>
                <div class="stat-value">{{ $stage2AvgWastePercentage ?? 0 }}%</div>
                <div class="progress-bar">
                    <div class="progress-fill {{ $stage2AvgWastePercentage > 12 ? 'danger' : ($stage2AvgWastePercentage > 8 ? 'warning' : '') }}" style="width: {{ min($stage2AvgWastePercentage ?? 0, 100) }}%"></div>
                </div>
            </div>

            <div class="stat-item success">
                <div class="stat-label">{{ __('stage2_report.production_efficiency') }}</div>
                <div class="stat-value">{{ $stage2ProductionEfficiency ?? 0 }}%</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stage2ProductionEfficiency ?? 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-pie-chart"></i>
            {{ __('stage2_report.status_distribution') }}
        </div>

        <div class="stat-row">
            <div class="stat-item">
                <div class="stat-label">{{ __('stage2_report.started') }}</div>
                <div class="stat-value" style="color: #3498db;">{{ $stage2StatusStarted ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ round(($stage2StatusStarted ?? 0) / max($stage2Total, 1) * 100) }}%</small>
            </div>

            <div class="stat-item warning">
                <div class="stat-label">{{ __('stage2_report.in_progress') }}</div>
                <div class="stat-value" style="color: #f39c12;">{{ $stage2StatusInProgress ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ round(($stage2StatusInProgress ?? 0) / max($stage2Total, 1) * 100) }}%</small>
            </div>

            <div class="stat-item success">
                <div class="stat-label">{{ __('stage2_report.completed') }}</div>
                <div class="stat-value" style="color: #27ae60;">{{ $stage2StatusCompleted ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ round(($stage2StatusCompleted ?? 0) / max($stage2Total, 1) * 100) }}%</small>
            </div>

            <div class="stat-item">
                <div class="stat-label">{{ __('stage2_report.consumed') }}</div>
                <div class="stat-value" style="color: #8e44ad;">{{ $stage2StatusConsumed ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ round(($stage2StatusConsumed ?? 0) / max($stage2Total, 1) * 100) }}%</small>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-trophy"></i>
            {{ __('stage2_report.top_performers') }}
        </div>

        <div class="two-column">
            <!-- Best Worker -->
            <div>
                <h4 style="margin-bottom: 15px; color: var(--dark);">🏆 {{ __('stage2_report.best_worker') }}</h4>
                <div class="stat-item success">
                    <div class="stat-label">{{ __('stage2_report.name') }}</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $stage2BestWorkerName ?? __('stage2_report.not_available') }}</div>
                    <hr style="margin: 10px 0; border: none; border-top: 1px solid var(--light);">
                    <div class="stat-label">{{ __('stage2_report.worker_records_count') }}</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $stage2BestWorkerCount ?? 0 }}</div>
                    <div class="stat-label" style="margin-top: 10px;">{{ __('stage2_report.avg_waste') }}</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $stage2BestWorkerAvgWaste ?? 0 }}%</div>
                </div>
            </div>

            <!-- Best Record -->
            <div>
                <h4 style="margin-bottom: 15px; color: var(--dark);">⭐ {{ __('stage2_report.best_record') }}</h4>
                <div class="stat-item success">
                    <div class="stat-label">{{ __('stage2_report.record_number') }}</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $stage2MaxWasteBarcode ?? __('stage2_report.not_available') }}</div>
                    <hr style="margin: 10px 0; border: none; border-top: 1px solid var(--light);">
                    <div class="stat-label">{{ __('stage2_report.lowest_waste_rate') }}</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $stage2MinWastePercentage ?? 0 }}%</div>
                    <div class="stat-label" style="margin-top: 10px;">{{ __('stage2_report.process_efficiency') }}</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $stage2ProductionEfficiency ?? 0 }}%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Waste Analysis -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-eye"></i>
            {{ __('stage2_report.waste_analysis') }}
        </div>

        <div class="stat-row">
            <div class="stat-item success">
                <div class="stat-label">{{ __('stage2_report.acceptable_waste') }}</div>
                <div class="stat-value" style="color: #27ae60;">{{ $stage2AcceptableWaste ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ __('stage2_report.stands') }}</small>
            </div>

            <div class="stat-item warning">
                <div class="stat-label">{{ __('stage2_report.warning_waste') }}</div>
                <div class="stat-value" style="color: #f39c12;">{{ $stage2WarningWaste ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ __('stage2_report.stands') }} - {{ __('stage2_report.requires_attention') }}</small>
            </div>

            <div class="stat-item danger">
                <div class="stat-label">{{ __('stage2_report.critical_waste') }}</div>
                <div class="stat-value" style="color: #e74c3c;">{{ $stage2CriticalWaste ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ __('stage2_report.stands') }} - {{ __('stage2_report.requires_follow_up') }}</small>
            </div>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border-right: 3px solid var(--primary);">
            <h4 style="margin-top: 0; color: var(--dark);">📊 {{ __('stage2_report.quality_summary') }}</h4>
            <p>{{ __('stage2_report.avg_waste_label') }}: <strong>{{ $stage2AvgWastePercentage ?? 0 }}%</strong></p>

            <p style="margin-bottom: 0;">
                @if(($stage2AvgWastePercentage ?? 0) < 8)
                    <span class="badge badge-success">✓ {{ __('stage2_report.performance_excellent') }}</span>
                @elseif(($stage2AvgWastePercentage ?? 0) < 12)
                    <span class="badge badge-warning">⚠️ {{ __('stage2_report.performance_good') }}</span>
                @else
                    <span class="badge badge-danger">⚠️ {{ __('stage2_report.performance_warning') }}</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Material Flow -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-arrow-right"></i>
            {{ __('stage2_report.material_flow') }}
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <div style="text-align: center;">
                <div style="font-size: 28px; color: var(--primary); font-weight: 700;">{{ $stage2TotalInputWeight ?? 0 }} {{ __('stage2_report.unit_kg') }}</div>
                <div style="color: #7f8c8d; font-size: 13px; margin-top: 5px;">{{ __('stage2_report.input_material') }}</div>
                <div style="color: #95a5a6; font-size: 11px;">{{ __('stage2_report.warehouse_label') }}</div>
            </div>

            <div style="font-size: 32px; color: #bdc3c7;">→</div>

            <div style="text-align: center;">
                <div style="font-size: 28px; color: var(--success); font-weight: 700;">{{ $stage2TotalOutputWeight ?? 0 }} {{ __('stage2_report.unit_kg') }}</div>
                <div style="color: #7f8c8d; font-size: 13px; margin-top: 5px;">{{ __('stage2_report.net_material') }}</div>
                <div style="color: #95a5a6; font-size: 11px;">{{ __('stage2_report.stage3') }}</div>
            </div>

            <div style="font-size: 32px; color: #bdc3c7;">→</div>

            <div style="text-align: center;">
                <div style="font-size: 28px; color: var(--danger); font-weight: 700;">{{ $stage2TotalWaste ?? 0 }} {{ __('stage2_report.unit_kg') }}</div>
                <div style="color: #7f8c8d; font-size: 13px; margin-top: 5px;">{{ __('stage2_report.waste_kg') }}</div>
                <div style="color: #95a5a6; font-size: 11px;">{{ round(($stage2TotalWaste ?? 0) / max($stage2TotalInputWeight, 1) * 100) }}%</div>
            </div>
        </div>
    </div>

    <!-- Daily Operations Timeline -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-history"></i>
            {{ __('stage2_report.daily_operations') }}
        </div>

        @if($stage2DailyOperations && count($stage2DailyOperations) > 0)
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('stage2_report.date') }}</th>
                        <th>{{ __('stage2_report.count') }}</th>
                        <th>{{ __('stage2_report.total_input') }}</th>
                        <th>{{ __('stage2_report.total_output') }}</th>
                        <th>{{ __('stage2_report.daily_waste') }}</th>
                        <th>{{ __('stage2_report.daily_avg_waste') }}</th>
                        <th>{{ __('stage2_report.daily_completed') }}</th>
                        <th>{{ __('stage2_report.daily_in_progress') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stage2DailyOperations as $day)
                    <tr>
                        <td><strong>{{ $day['date'] }}</strong></td>
                        <td style="text-align: center;">
                            <span class="badge badge-primary">{{ $day['count'] }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--primary); font-weight: 600;">{{ $day['total_input'] }} {{ __('stage2_report.unit_kg') }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--success); font-weight: 600;">{{ $day['total_output'] }} {{ __('stage2_report.unit_kg') }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--danger); font-weight: 600;">{{ $day['total_waste'] }} {{ __('stage2_report.unit_kg') }}</span>
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
                            <span class="status-badge status-in_progress">{{ $day['in_progress'] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px; color: #7f8c8d;">
                            <i class="fas fa-inbox"></i> {{ __('stage2_report.no_daily_data') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <i class="fas fa-history" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
            <p>{{ __('stage2_report.no_daily_data') }}</p>
        </div>
        @endif
    </div>

    <!-- Cumulative Progress -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-chart-area"></i>
            {{ __('stage2_report.cumulative_progress') }}
        </div>

        @if($stage2CumulativeData && count($stage2CumulativeData) > 0)
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('stage2_report.date') }}</th>
                        <th>{{ __('stage2_report.cumulative_input') }}</th>
                        <th>{{ __('stage2_report.cumulative_output') }}</th>
                        <th>{{ __('stage2_report.cumulative_waste') }}</th>
                        <th>{{ __('stage2_report.completion_percentage') }}</th>
                        <th>{{ __('stage2_report.waste_percentage_label') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stage2CumulativeData as $day)
                    <tr>
                        <td><strong>{{ $day['date'] }}</strong></td>
                        <td style="text-align: center;">
                            <span style="color: var(--primary); font-weight: 600;">{{ $day['cumulative_input'] }} {{ __('stage2_report.unit_kg') }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--success); font-weight: 600;">{{ $day['cumulative_output'] }} {{ __('stage2_report.unit_kg') }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--danger); font-weight: 600;">{{ $day['cumulative_waste'] }} {{ __('stage2_report.unit_kg') }}</span>
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
                            <i class="fas fa-inbox"></i> {{ __('stage2_report.no_cumulative_data') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <i class="fas fa-chart-area" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
            <p>{{ __('stage2_report.no_cumulative_data') }}</p>
        </div>
        @endif
    </div>

    <!-- Worker Performance Table -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-users"></i>
            {{ __('stage2_report.worker_performance') }}
        </div>

        @if($stage2WorkerPerformance && count($stage2WorkerPerformance) > 0)
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('stage2_report.worker_name') }}</th>
                        <th>{{ __('stage2_report.records_count') }}</th>
                        <th>{{ __('stage2_report.total_input_material') }}</th>
                        <th>{{ __('stage2_report.total_waste_amount') }}</th>
                        <th>{{ __('stage2_report.avg_waste_percentage') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stage2WorkerPerformance as $index => $worker)
                    <tr>
                        <td><strong>{{ $worker['name'] ?? '-' }}</strong></td>
                        <td style="text-align: center;">{{ $worker['count'] }}</td>
                        <td style="text-align: center;">{{ $worker['total_input'] }} {{ __('stage2_report.unit_kg') }}</td>
                        <td style="text-align: center;">{{ $worker['total_waste'] }} {{ __('stage2_report.unit_kg') }}</td>
                        <td style="text-align: center;">
                            @php
                                $wasteClass = $worker['avg_waste'] > 12 ? 'critical' : ($worker['avg_waste'] > 8 ? 'warning' : 'safe');
                            @endphp
                            <span class="waste-level {{ $wasteClass }}">{{ $worker['avg_waste'] }}%</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: #7f8c8d;">
                            <i class="fas fa-inbox"></i> {{ __('stage2_report.no_worker_data') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <i class="fas fa-users" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
            <p>{{ __('stage2_report.no_worker_data') }}</p>
        </div>
        @endif
    </div>

    <!-- Print Button -->
    <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn btn-primary" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
            <i class="fas fa-print"></i> {{ __('stage2_report.print_report') }}
        </button>
        <button onclick="window.history.back()" class="btn btn-secondary" style="padding: 10px 20px; background: #95a5a6; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; margin-right: 10px;">
            <i class="fas fa-arrow-left"></i> {{ __('stage2_report.back') }}
        </button>
    </div>

    <!-- Footer -->
    <div style="text-align: center; color: #7f8c8d; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--light);">
        <p>{{ __('stage2_report.generated_by') }}</p>
        <p>{{ __('stage2_report.copyright') }} {{ __('stage2_report.footer_text') }}</p>
    </div>
</div>

@endsection
