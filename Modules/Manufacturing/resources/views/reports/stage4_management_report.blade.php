@extends('master')

@section('title', __('stage4_report.page_title'))

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/stage1-report.css') }}">

    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h1>
                        <i class="fas fa-box-open"></i>
                        {{ __('stage4_report.page_title') }}
                    </h1>
                    <p>🏭 {{ __('stage4_report.system_name') }}</p>
                </div>
                <div class="report-date">
                    <div style="font-weight: 600; margin-bottom: 5px;">{{ date('Y-m-d H:i') }}</div>
                    <div style="font-size: 12px;">{{ __('stage4_report.current_report') }}</div>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="kpi-grid">
            <!-- إجمالي الصناديق -->
            <div class="kpi-card success">
                <div class="kpi-icon">📦</div>
                <div class="kpi-label">{{ __('stage4_report.total_boxes') }}</div>
                <div class="kpi-value">{{ $stage4Total ?? 0 }}</div>
                <div class="kpi-unit">{{ __('stage4_report.unit_box') }}</div>
                <div class="kpi-change positive">
                    ↑ {{ $stage4Today ?? 0 }} {{ __('stage4_report.today') }}
                </div>
            </div>

            <!-- الصناديق المكتملة -->
            <div class="kpi-card success">
                <div class="kpi-icon">✅</div>
                <div class="kpi-label">{{ __('stage4_report.completed_boxes') }}</div>
                <div class="kpi-value">{{ $stage4CompletedCount ?? 0 }}</div>
                <div class="kpi-unit">{{ $stage4CompletionRate ?? 0 }}%</div>
                <div class="kpi-change positive">
                    ✓ {{ __('stage4_report.ready_for_shipping') }}
                </div>
            </div>

            <!-- الصناديق المشحونة -->
            <div class="kpi-card warning">
                <div class="kpi-icon">🚚</div>
                <div class="kpi-label">{{ __('stage4_report.shipped_boxes') }}</div>
                <div class="kpi-value">{{ $stage4StatusShipped ?? 0 }}</div>
                <div class="kpi-unit">{{ __('stage4_report.in_transit') }}</div>
                <div class="kpi-change">
                    📤 {{ __('stage4_report.on_the_way') }}
                </div>
            </div>

            <!-- الصناديق المسلمة -->
            <div class="kpi-card info">
                <div class="kpi-icon">🏠</div>
                <div class="kpi-label">{{ __('stage4_report.delivered_boxes') }}</div>
                <div class="kpi-value">{{ $stage4StatusDelivered ?? 0 }}</div>
                <div class="kpi-unit">{{ __('stage4_report.received') }}</div>
                <div class="kpi-change positive">
                    ✓ {{ __('stage4_report.completed') }}
                </div>
            </div>

            <!-- الوزن الكلي -->
            <div class="kpi-card success">
                <div class="kpi-icon">⚖️</div>
                <div class="kpi-label">{{ __('stage4_report.total_weight') }}</div>
                <div class="kpi-value">{{ $stage4TotalWeight ?? 0 }}</div>
                <div class="kpi-unit">{{ __('stage4_report.unit_kg') }}</div>
                <div class="kpi-change positive">
                    ✓ {{ __('stage4_report.processed') }}
                </div>
            </div>

            <!-- إجمالي الهدر -->
            <div class="kpi-card danger">
                <div class="kpi-icon">♻️</div>
                <div class="kpi-label">{{ __('stage4_report.total_waste') }}</div>
                <div class="kpi-value">{{ $stage4TotalWaste ?? 0 }}</div>
                <div class="kpi-unit">{{ __('stage4_report.unit_kg') }}</div>
                <div class="kpi-change">
                    📊 {{ __('stage4_report.average') }}: {{ $stage4AvgWastePercentage ?? 0 }}%
                </div>
            </div>

            <!-- إجمالي الكويلات -->
            <div class="kpi-card info">
                <div class="kpi-icon">🔄</div>
                <div class="kpi-label">{{ __('stage4_report.total_coils') }}</div>
                <div class="kpi-value">{{ $stage4TotalCoils ?? 0 }}</div>
                <div class="kpi-unit">{{ __('stage4_report.unit_coil') }}</div>
                <div class="kpi-change">
                    📊 {{ __('stage4_report.average') }}: {{ $stage4AvgCoilsPerBox ?? 0 }} {{ __('stage4_report.per_box') }}
                </div>
            </div>

            <!-- أعلى نسبة هدر -->
            <div class="kpi-card danger">
                <div class="kpi-icon">⚠️</div>
                <div class="kpi-label">{{ __('stage4_report.highest_waste') }}</div>
                <div class="kpi-value">{{ $stage4MaxWastePercentage ?? 0 }}%</div>
                <div class="kpi-unit">{{ __('stage4_report.box_label') }}: {{ $stage4MaxWasteBarcode ?? '-' }}</div>
                <div class="kpi-change negative">
                    🔴 {{ __('stage4_report.alert_attention') }}
                </div>
            </div>

            <!-- أقل نسبة هدر -->
            <div class="kpi-card success">
                <div class="kpi-icon">🎯</div>
                <div class="kpi-label">{{ __('stage4_report.lowest_waste') }}</div>
                <div class="kpi-value">{{ $stage4MinWastePercentage ?? 0 }}%</div>
                <div class="kpi-unit">{{ __('stage4_report.box_label') }}: {{ $stage4MinWasteBarcode ?? '-' }}</div>
                <div class="kpi-change positive">
                    ✓ {{ __('stage4_report.excellent') }}
                </div>
            </div>

            <!-- عدد العمال -->
            <div class="kpi-card info">
                <div class="kpi-icon">👥</div>
                <div class="kpi-label">{{ __('stage4_report.active_workers') }}</div>
                <div class="kpi-value">{{ $stage4ActiveWorkers ?? 0 }}</div>
                <div class="kpi-unit">{{ __('stage4_report.unit_worker') }}</div>
                <div class="kpi-change">
                    👨‍🔧 {{ __('stage4_report.in_this_period') }}
                </div>
            </div>

            <!-- متوسط وزن الصندوق -->
            <div class="kpi-card success">
                <div class="kpi-icon">📈</div>
                <div class="kpi-label">{{ __('stage4_report.avg_box_weight') }}</div>
                <div class="kpi-value">{{ $stage4AvgWeightPerBox ?? 0 }}</div>
                <div class="kpi-unit">{{ __('stage4_report.unit_per_box') }}</div>
                <div class="kpi-change positive">
                    ↑ {{ __('stage4_report.properly_weighted') }}
                </div>
            </div>

            <!-- نسبة الشحن -->
            <div class="kpi-card success">
                <div class="kpi-icon">✓</div>
                <div class="kpi-label">{{ __('stage4_report.shipping_rate') }}</div>
                <div class="kpi-value">{{ $stage4ShippedPercentage ?? 0 }}%</div>
                <div class="kpi-unit">{{ __('stage4_report.shipping_percentage') }}</div>
                <div class="kpi-change positive">
                    ✓ {{ __('stage4_report.ready') }}
                </div>
            </div>
        </div>

        <!-- Alerts Section -->
        @if (($stage4StatusPacking ?? 0) > 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>{{ __('stage4_report.alert_warning') }}:</strong> {{ __('stage4_report.packing_pending_msg', ['count' => $stage4StatusPacking ?? 0]) }}
            </div>
        @endif

        @if (($stage4MaxWastePercentage ?? 0) > 15)
            <div class="alert alert-danger">
                <i class="fas fa-alert-circle"></i>
                <strong>{{ __('stage4_report.alert_danger') }}:</strong> {{ __('stage4_report.high_waste_detected') }} ({{ $stage4MaxWastePercentage }}%) - {{ __('stage4_report.requires_review') }}
            </div>
        @endif

        @if (($stage4AvgWastePercentage ?? 0) < 5)
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <strong>{{ __('stage4_report.alert_success') }}:</strong> {{ __('stage4_report.optimal_waste_level') }} ({{ $stage4AvgWastePercentage }}%)
            </div>
        @endif

        <!-- Filters Section -->
        <div class="report-section">
            <div class="section-title">
                <i class="fas fa-filter"></i>
                {{ __('stage4_report.filters') }}
            </div>

            <form method="GET" action="{{ route('manufacturing.reports.stage4-management') }}" style="margin-top: 15px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">

                    <!-- البحث بالباركود -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">🔍 {{ __('stage4_report.search_barcode') }}</label>
                        <input type="text" name="search" class="um-form-control" placeholder="{{ __('stage4_report.barcode_placeholder') }}" value="{{ $filters['search'] ?? '' }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                    </div>

                    <!-- التصفية بالحالة -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📊 {{ __('stage4_report.status_label') }}</label>
                        <select name="status" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                            <option value="">{{ __('stage4_report.all_statuses') }}</option>
                            <option value="packing" {{ ($filters['status'] ?? '') === 'packing' ? 'selected' : '' }}>{{ __('stage4_report.status_packing') }}</option>
                            <option value="packed" {{ ($filters['status'] ?? '') === 'packed' ? 'selected' : '' }}>{{ __('stage4_report.status_packed') }}</option>
                            <option value="shipped" {{ ($filters['status'] ?? '') === 'shipped' ? 'selected' : '' }}>{{ __('stage4_report.status_shipped') }}</option>
                            <option value="delivered" {{ ($filters['status'] ?? '') === 'delivered' ? 'selected' : '' }}>{{ __('stage4_report.status_delivered') }}</option>
                            <option value="in_warehouse" {{ ($filters['status'] ?? '') === 'in_warehouse' ? 'selected' : '' }}>{{ __('stage4_report.status_in_warehouse') }}</option>
                        </select>
                    </div>

                    <!-- التصفية بنوع التغليف -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📦 {{ __('stage4_report.packaging_type_label') }}</label>
                        <select name="packaging_type" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                            <option value="">{{ __('stage4_report.all_statuses') }}</option>
                            @if ($stage4PackagingTypes)
                                @foreach ($stage4PackagingTypes as $type => $data)
                                    <option value="{{ $type }}" {{ ($filters['packaging_type'] ?? '') === $type ? 'selected' : '' }}>
                                        {{ $type }} ({{ $data['count'] ?? 0 }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- التصفية بالعامل -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">👤 {{ __('stage4_report.worker_label') }}</label>
                        <select name="worker_id" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                            <option value="">{{ __('stage4_report.all_workers') }}</option>
                            @foreach ($stage4Workers as $worker)
                                <option value="{{ $worker->id }}" {{ ($filters['worker_id'] ?? '') == $worker->id ? 'selected' : '' }}>
                                    {{ $worker->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- التصفية بالمستودع -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">🏢 {{ __('stage4_report.warehouse_label') }}</label>
                        <select name="warehouse_id" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                            <option value="">{{ __('stage4_report.all_warehouses') }}</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ ($filters['warehouse_id'] ?? '') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->warehouse_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- من التاريخ -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📅 {{ __('stage4_report.from_date') }}</label>
                        <input type="date" name="from_date" class="um-form-control" value="{{ $filters['from_date'] ?? '' }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                    </div>

                    <!-- إلى التاريخ -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📅 {{ __('stage4_report.to_date') }}</label>
                        <input type="date" name="to_date" class="um-form-control" value="{{ $filters['to_date'] ?? '' }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                    </div>

                    <!-- الترتيب -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">🔄 {{ __('stage4_report.sort_by_label') }}</label>
                        <select name="sort_by" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                            <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>{{ __('stage4_report.sort_by_date') }}</option>
                            <option value="total_weight" {{ request('sort_by') === 'total_weight' ? 'selected' : '' }}>{{ __('stage4_report.sort_by_weight') }}</option>
                            <option value="waste" {{ request('sort_by') === 'waste' ? 'selected' : '' }}>{{ __('stage4_report.sort_by_waste') }}</option>
                            <option value="barcode" {{ request('sort_by') === 'barcode' ? 'selected' : '' }}>{{ __('stage4_report.sort_by_barcode') }}</option>
                        </select>
                    </div>

                    <!-- ترتيب تصاعدي/تنازلي -->
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📈 {{ __('stage4_report.direction_label') }}</label>
                        <select name="sort_order" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                            <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>{{ __('stage4_report.descending') }}</option>
                            <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>{{ __('stage4_report.ascending') }}</option>
                        </select>
                    </div>
                </div>

                <!-- أزرار الإجراء -->
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button type="submit" class="um-btn um-btn-primary" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        <i class="fas fa-search"></i> {{ __('stage4_report.search_button') }}
                    </button>
                    <a href="{{ route('manufacturing.reports.stage4-management') }}" class="um-btn um-btn-outline" style="padding: 10px 20px; background: #ecf0f1; color: var(--dark); border: none; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block;">
                        <i class="fas fa-redo"></i> {{ __('stage4_report.reset_filters') }}
                    </a>
                </div>
            </form>
        </div>

        <!-- جدول السجلات الكاملة -->
        <div class="report-section">
            <div class="section-title">
                <i class="fas fa-table"></i>
                {{ __('stage4_report.all_boxes') }} ({{ $stage4Records->count() }} {{ __('stage4_report.box_count') }})
            </div>

            @if ($stage4Records && count($stage4Records) > 0)
                <div style="overflow-x: auto; margin-top: 15px;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('stage4_report.table_no') }}</th>
                                <th>{{ __('stage4_report.table_barcode') }}</th>
                                <th>{{ __('stage4_report.table_packaging_type') }}</th>
                                <th>{{ __('stage4_report.table_coils_count') }}</th>
                                <th>{{ __('stage4_report.table_total_weight') }}</th>
                                <th>{{ __('stage4_report.table_waste') }}</th>
                                <th>{{ __('stage4_report.table_waste_percentage') }}</th>
                                <th>{{ __('stage4_report.table_status') }}</th>
                                <th>{{ __('stage4_report.table_worker') }}</th>
                                <th>{{ __('stage4_report.table_warehouse') }}</th>
                                <th>{{ __('stage4_report.table_tracking_number') }}</th>
                                <th>{{ __('stage4_report.table_date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stage4Records as $index => $record)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $record->barcode ?? '-' }}</strong></td>
                                    <td>{{ $record->packaging_type ?? '-' }}</td>
                                    <td style="text-align: center;">{{ $record->coils_count ?? 0 }}</td>
                                    <td style="text-align: center;">{{ $record->total_weight ?? 0 }} {{ __('stage4_report.unit_kg') }}</td>
                                    <td style="text-align: center;">{{ $record->waste ?? 0 }} {{ __('stage4_report.unit_kg') }}</td>
                                    <td style="text-align: center;">
                                        @php
                                            $wastePerc = ($record->total_weight ?? 0) > 0 ? round((($record->waste ?? 0) / ($record->total_weight ?? 0)) * 100, 2) : 0;
                                            $wasteClass = $wastePerc > 12 ? 'critical' : ($wastePerc > 8 ? 'warning' : 'safe');
                                        @endphp
                                        <span class="waste-level {{ $wasteClass }}">{{ $wastePerc }}%</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="status-badge status-{{ $record->status ?? 'packing' }}">
                                            @if ($record->status === 'packing')
                                                {{ __('stage4_report.status_packing') }}
                                            @elseif($record->status === 'packed')
                                                {{ __('stage4_report.status_packed') }}
                                            @elseif($record->status === 'shipped')
                                                {{ __('stage4_report.status_shipped') }}
                                            @elseif($record->status === 'delivered')
                                                {{ __('stage4_report.status_delivered') }}
                                            @elseif($record->status === 'in_warehouse')
                                                {{ __('stage4_report.status_in_warehouse') }}
                                            @else
                                                {{ $record->status }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $record->created_by_name ?? '-' }}</td>
                                    <td>{{ $record->warehouse_name ?? '-' }}</td>
                                    <td>{{ $record->tracking_number ?? '-' }}</td>
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
                                    <td colspan="12" style="text-align: center; padding: 30px; color: #7f8c8d;">
                                        <i class="fas fa-inbox"></i> {{ __('stage4_report.no_records_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 40px; color: #7f8c8d;">
                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                    <p>{{ __('stage4_report.no_records') }}</p>
                </div>
            @endif
        </div>

        <!-- Detailed Statistics Section -->
        <div class="report-section">
            <div class="section-title">
                <i class="fas fa-bar-chart"></i>
                {{ __('stage4_report.detailed_statistics') }}
            </div>

            <div class="stat-row">
                <div class="stat-item success">
                    <div class="stat-label">{{ __('stage4_report.completion_rate') }}</div>
                    <div class="stat-value">{{ $stage4CompletionRate ?? 0 }}%</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $stage4CompletionRate ?? 0 }}%"></div>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-label">{{ __('stage4_report.waste_rate') }}</div>
                    <div class="stat-value">{{ $stage4AvgWastePercentage ?? 0 }}%</div>
                    <div class="progress-bar">
                        <div class="progress-fill {{ ($stage4AvgWastePercentage ?? 0) > 12 ? 'danger' : (($stage4AvgWastePercentage ?? 0) > 8 ? 'warning' : '') }}" style="width: {{ min($stage4AvgWastePercentage ?? 0, 100) }}%"></div>
                    </div>
                </div>

                <div class="stat-item success">
                    <div class="stat-label">{{ __('stage4_report.shipping_rate') }}</div>
                    <div class="stat-value">{{ $stage4ShippedPercentage ?? 0 }}%</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $stage4ShippedPercentage ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="report-section">
            <div class="section-title">
                <i class="fas fa-pie-chart"></i>
                {{ __('stage4_report.status_distribution') }}
            </div>

            <div class="status-distribution">
                @php
                    $totalBoxes = $stage4Total ?? 1;
                @endphp
                <div class="status-item">
                    <div class="status-color" style="background-color: #3498db;"></div>
                    <div class="status-info">
                        <div class="status-name">{{ __('stage4_report.packing') }}</div>
                        <div class="status-count">{{ $stage4StatusPacking ?? 0 }} {{ __('stage4_report.unit_box') }}</div>
                        <div class="status-percentage">
                            {{ $totalBoxes > 0 ? round((($stage4StatusPacking ?? 0) / $totalBoxes) * 100) : 0 }}%</div>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-color" style="background-color: #2ecc71;"></div>
                    <div class="status-info">
                        <div class="status-name">{{ __('stage4_report.packed') }}</div>
                        <div class="status-count">{{ $stage4StatusPacked ?? 0 }} {{ __('stage4_report.unit_box') }}</div>
                        <div class="status-percentage">
                            {{ $totalBoxes > 0 ? round((($stage4StatusPacked ?? 0) / $totalBoxes) * 100) : 0 }}%</div>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-color" style="background-color: #f39c12;"></div>
                    <div class="status-info">
                        <div class="status-name">{{ __('stage4_report.shipped') }}</div>
                        <div class="status-count">{{ $stage4StatusShipped ?? 0 }} {{ __('stage4_report.unit_box') }}</div>
                        <div class="status-percentage">
                            {{ $totalBoxes > 0 ? round((($stage4StatusShipped ?? 0) / $totalBoxes) * 100) : 0 }}%</div>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-color" style="background-color: #9b59b6;"></div>
                    <div class="status-info">
                        <div class="status-name">{{ __('stage4_report.delivered') }}</div>
                        <div class="status-count">{{ $stage4StatusDelivered ?? 0 }} {{ __('stage4_report.unit_box') }}</div>
                        <div class="status-percentage">
                            {{ $totalBoxes > 0 ? round((($stage4StatusDelivered ?? 0) / $totalBoxes) * 100) : 0 }}%</div>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-color" style="background-color: #e74c3c;"></div>
                    <div class="status-info">
                        <div class="status-name">{{ __('stage4_report.in_warehouse') }}</div>
                        <div class="status-count">{{ $stage4StatusInWarehouse ?? 0 }} {{ __('stage4_report.unit_box') }}</div>
                        <div class="status-percentage">
                            {{ $totalBoxes > 0 ? round((($stage4StatusInWarehouse ?? 0) / $totalBoxes) * 100) : 0 }}%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Worker Performance -->
        @if ($stage4WorkerPerformance && count($stage4WorkerPerformance) > 0)
            <div class="report-section">
                <div class="section-title">
                    <i class="fas fa-users"></i>
                    {{ __('stage4_report.worker_performance') }}
                </div>

                <div style="overflow-x: auto; margin-top: 15px;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('stage4_report.worker_name') }}</th>
                                <th>{{ __('stage4_report.boxes_count') }}</th>
                                <th>{{ __('stage4_report.total_weight_label') }}</th>
                                <th>{{ __('stage4_report.total_coils_label') }}</th>
                                <th>{{ __('stage4_report.total_waste_label') }}</th>
                                <th>{{ __('stage4_report.avg_waste_percentage') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stage4WorkerPerformance as $worker)
                                <tr>
                                    <td><strong>{{ $worker['name'] ?? '-' }}</strong></td>
                                    <td style="text-align: center;">{{ $worker['count'] ?? 0 }}</td>
                                    <td style="text-align: center;">{{ $worker['total_weight'] ?? 0 }} {{ __('stage4_report.unit_kg') }}</td>
                                    <td style="text-align: center;">{{ $worker['total_coils'] ?? 0 }}</td>
                                    <td style="text-align: center;">{{ $worker['total_waste'] ?? 0 }} {{ __('stage4_report.unit_kg') }}</td>
                                    <td style="text-align: center;">
                                        <span class="waste-level {{ ($worker['avg_waste'] ?? 0) > 12 ? 'critical' : (($worker['avg_waste'] ?? 0) > 8 ? 'warning' : 'safe') }}">
                                            {{ $worker['avg_waste'] ?? 0 }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Packaging Type Distribution -->
        @if ($stage4PackagingTypes && count($stage4PackagingTypes) > 0)
            <div class="report-section">
                <div class="section-title">
                    <i class="fas fa-cube"></i>
                    {{ __('stage4_report.packaging_type_distribution') }}
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px;">
                    @foreach ($stage4PackagingTypes as $type => $data)
                        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
                            <div style="font-weight: 600; margin-bottom: 10px; font-size: 16px;">{{ $type }}</div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
                                <span>{{ __('stage4_report.boxes_count_label') }}:</span>
                                <strong>{{ $data['count'] ?? 0 }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
                                <span>{{ __('stage4_report.total_weight_sum') }}:</span>
                                <strong>{{ $data['weight'] ?? 0 }} {{ __('stage4_report.unit_kg') }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 14px;">
                                <span>{{ __('stage4_report.coils_count_label') }}:</span>
                                <strong>{{ $data['coils'] ?? 0 }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Daily Operations -->
        @if ($stage4DateGroups && count($stage4DateGroups) > 0)
            <div class="report-section">
                <div class="section-title">
                    <i class="fas fa-calendar"></i>
                    {{ __('stage4_report.daily_operations') }}
                </div>

                <div style="overflow-x: auto; margin-top: 15px;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('stage4_report.date') }}</th>
                                <th>{{ __('stage4_report.daily_boxes_count') }}</th>
                                <th>{{ __('stage4_report.daily_total_weight') }}</th>
                                <th>{{ __('stage4_report.daily_total_coils') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stage4DateGroups as $date => $data)
                                <tr>
                                    <td><strong>{{ $date }}</strong></td>
                                    <td style="text-align: center;">{{ $data['count'] ?? 0 }}</td>
                                    <td style="text-align: center;">{{ $data['total_weight'] ?? 0 }} {{ __('stage4_report.unit_kg') }}</td>
                                    <td style="text-align: center;">{{ $data['total_coils'] ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Best Performer -->
        @if ($stage4BestWorkerName && $stage4BestWorkerName !== 'Not available')
            <div class="report-section">
                <div class="section-title">
                    <i class="fas fa-star"></i>
                    {{ __('stage4_report.best_performer') }}
                </div>

                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 10px;">🏆</div>
                    <div style="font-size: 24px; font-weight: 600; margin-bottom: 10px;">{{ $stage4BestWorkerName }}</div>
                    <div style="font-size: 14px; margin-bottom: 20px;">{{ __('stage4_report.best_worker_stage4') }}</div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-top: 20px;">
                        <div>
                            <div style="font-size: 32px; font-weight: 600;">{{ $stage4BestWorkerCount ?? 0 }}</div>
                            <div style="font-size: 12px; opacity: 0.9;">{{ __('stage4_report.boxes_processed') }}</div>
                        </div>
                        <div>
                            <div style="font-size: 32px; font-weight: 600;">{{ $stage4BestWorkerAvgWaste ?? 0 }}%</div>
                            <div style="font-size: 12px; opacity: 0.9;">{{ __('stage4_report.avg_waste') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Boxes -->
        @if ($recentBoxes && count($recentBoxes) > 0)
            <div class="report-section">
                <div class="section-title">
                    <i class="fas fa-clock"></i>
                    {{ __('stage4_report.recent_boxes') }}
                </div>

                <div style="overflow-x: auto; margin-top: 15px;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('stage4_report.recent_barcode') }}</th>
                                <th>{{ __('stage4_report.recent_packaging_type') }}</th>
                                <th>{{ __('stage4_report.recent_weight') }}</th>
                                <th>{{ __('stage4_report.recent_coils') }}</th>
                                <th>{{ __('stage4_report.recent_status') }}</th>
                                <th>{{ __('stage4_report.recent_worker') }}</th>
                                <th>{{ __('stage4_report.recent_time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentBoxes as $box)
                                <tr>
                                    <td><strong>{{ $box->barcode ?? '-' }}</strong></td>
                                    <td>{{ $box->packaging_type ?? '-' }}</td>
                                    <td style="text-align: center;">{{ $box->total_weight ?? 0 }} {{ __('stage4_report.unit_kg') }}</td>
                                    <td style="text-align: center;">{{ $box->coils_count ?? 0 }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $box->status ?? 'packing' }}">
                                            @if ($box->status === 'packing')
                                                {{ __('stage4_report.status_packing') }}
                                            @elseif($box->status === 'packed')
                                                {{ __('stage4_report.status_packed') }}
                                            @elseif($box->status === 'shipped')
                                                {{ __('stage4_report.status_shipped') }}
                                            @elseif($box->status === 'delivered')
                                                {{ __('stage4_report.status_delivered') }}
                                            @else
                                                {{ $box->status }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $box->created_by_name ?? '-' }}</td>
                                    <td>
                                        @if ($box->created_at)
                                            @if (is_string($box->created_at))
                                                {{ substr($box->created_at, 0, 16) }}
                                            @else
                                                {{ $box->created_at->format('H:i') }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>

    <style>
        .report-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            background: #f8f9fa;
        }

        .report-header {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .report-header h1 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 32px;
        }

        .report-header p {
            margin: 0;
            color: #7f8c8d;
        }

        .report-date {
            text-align: right;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .kpi-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #3498db;
        }

        .kpi-card.success {
            border-left-color: #2ecc71;
        }

        .kpi-card.warning {
            border-left-color: #f39c12;
        }

        .kpi-card.danger {
            border-left-color: #e74c3c;
        }

        .kpi-card.info {
            border-left-color: #3498db;
        }

        .kpi-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .kpi-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .kpi-value {
            font-size: 28px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .kpi-unit {
            font-size: 11px;
            color: #95a5a6;
        }

        .kpi-change {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 10px;
        }

        .kpi-change.positive {
            color: #2ecc71;
        }

        .kpi-change.negative {
            color: #e74c3c;
        }

        .report-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead tr {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .data-table th {
            padding: 12px;
            text-align: right;
            font-weight: 600;
            color: #2c3e50;
            font-size: 13px;
        }

        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        .data-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .waste-level {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
        }

        .waste-level.safe {
            background-color: #d4edda;
            color: #155724;
        }

        .waste-level.warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .waste-level.critical {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.status-packing {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-badge.status-packed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.status-shipped {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-badge.status-delivered {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.status-in_warehouse {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .stat-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .stat-item {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .stat-label {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .progress-bar {
            height: 8px;
            background-color: #ecf0f1;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background-color: #2ecc71;
            transition: width 0.3s ease;
        }

        .progress-fill.danger {
            background-color: #e74c3c;
        }

        .progress-fill.warning {
            background-color: #f39c12;
        }

        .status-distribution {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .status-item {
            display: flex;
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .status-color {
            width: 8px;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .status-info {
            flex: 1;
        }

        .status-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .status-count {
            font-size: 13px;
            color: #7f8c8d;
        }

        .status-percentage {
            font-size: 12px;
            color: #95a5a6;
            margin-top: 5px;
        }
    </style>

@endsection
