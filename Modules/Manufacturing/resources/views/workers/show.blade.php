@extends('master')

@section('title', __('shifts-workers.worker_details') . ' - ' . $worker->name)

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">
    <style>
        .action-btn.status {
            display: flex;
            align-items: center;
            color: #0066cc;
            border: none;
        }
        .action-btn.status:hover {
            color: #004499;
        }

        /* Modal Animation */
        #movementDetailsModal[style*="display: flex"] {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Spinner */
        .spinner-border {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            vertical-align: text-bottom;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border 0.75s linear infinite;
        }

        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }

        .text-primary {
            color: #3498db !important;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0,0,0,0);
            white-space: nowrap;
            border-width: 0;
        }

        /* Table Responsive Styles */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #3498db;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #2980b9;
        }

        .info-item {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-right: 4px solid #3498db;
        }

        .info-item label {
            font-size: 11px;
            color: #7f8c8d;
            margin-bottom: 5px;
            font-weight: 600;
            display: block;
        }

        .info-item .value {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .card-icon.primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }

        .card-icon.success {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        }

        .card-icon.warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }

        .card-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
        }

        .card-body {
            padding: 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-info h1 {
            margin: 0 0 5px 0;
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
        }

        .header-info p {
            margin: 0;
            color: #7f8c8d;
            font-size: 14px;
        }

        .worker-icon {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
        }

        .btn-back {
            background: white;
            border: 1px solid #e9ecef;
            color: #2c3e50;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: #f8f9fa;
            border-color: #3498db;
            color: #3498db;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.active {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .col-12 {
            grid-column: 1 / -1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background: #f8f9fa;
        }

        table th {
            padding: 15px;
            text-align: right;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e9ecef;
        }

        table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            color: #555;
        }

        table tbody tr:hover {
            background: #f8f9fa;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }

        .action-btn.view {
            background: #3498db;
            color: white;
        }

        .action-btn.view:hover {
            background: #2980b9;
        }

        .view-shift-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #3498db;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .view-shift-btn:hover {
            color: #2980b9;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #7f8c8d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
            opacity: 0.5;
        }
    </style>

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

    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="worker-icon">
                        <i class="feather icon-user"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $worker->name }}</h1>
                        <p><strong>{{ __('shifts-workers.code') }}:</strong> {{ $worker->worker_code }} | <strong>{{ __('shifts-workers.job') }}:</strong> {{ $worker->position_name }}</p>
                    </div>
                </div>
                <div class="header-actions">
                    @can('WORKERS_UPDATE')
                    <a href="{{ route('manufacturing.shifts-workers.edit', $worker->id) }}" class="action-btn view">
                        <i class="feather icon-edit-2"></i> {{ __('app.buttons.edit') }}
                    </a>
                    @endcan
                    @can('WORKERS_READ')
                    <a href="{{ route('manufacturing.shifts-workers.index') }}" class="btn-back">
                        <i class="feather icon-arrow-right"></i> {{ __('shifts-workers.back') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="grid">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <i class="feather icon-user-check"></i>
                    </div>
                    <h3 class="card-title">{{ __('shifts-workers.basic_information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <label>{{ __('shifts-workers.worker_name') }}</label>
                        <div class="value">{{ $worker->name }}</div>
                    </div>

                    <div class="info-item">
                        <label>{{ __('shifts-workers.worker_code') }}</label>
                        <div class="value"><code style="background: white; padding: 6px 10px; border-radius: 4px;">{{ $worker->worker_code }}</code></div>
                    </div>

                    <div class="info-item">
                        <label>{{ __('shifts-workers.position') }}</label>
                        <div class="value">
                            <span class="status-badge active">{{ $worker->position_name }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <label>{{ __('shifts-workers.national_id') }}</label>
                        <div class="value">{{ $worker->national_id ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <i class="feather icon-phone"></i>
                    </div>
                    <h3 class="card-title">{{ __('shifts-workers.contact_information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <label>{{ __('shifts-workers.phone') }}</label>
                        <div class="value">{{ $worker->phone ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <label>{{ __('shifts-workers.email') }}</label>
                        <div class="value">{{ $worker->email ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <label>{{ __('shifts-workers.emergency_contact') }}</label>
                        <div class="value">{{ $worker->emergency_contact ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <label>{{ __('shifts-workers.emergency_phone') }}</label>
                        <div class="value">{{ $worker->emergency_phone ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <i class="feather icon-briefcase"></i>
                    </div>
                    <h3 class="card-title">{{ __('shifts-workers.work_information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <label>{{ __('shifts-workers.hourly_rate') }}</label>
                        <div class="value">{{ number_format($worker->hourly_rate, 2) }} {{ __('shifts-workers.currency') }}</div>
                    </div>

                    <div class="info-item">
                        <label>{{ __('shifts-workers.hire_date') }}</label>
                        <div class="value">{{ $worker->hire_date?->format('Y-m-d') ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <label>{{ __('shifts-workers.shift_preference') }}</label>
                        <div class="value">{{ $worker->shift_preference_name }}</div>
                    </div>

                    <div class="info-item">
                        <label>{{ __('app.status.status') }}</label>
                        <div class="value">
                            <span class="status-badge {{ $worker->is_active ? 'active' : 'inactive' }}">
                                {{ $worker->is_active ? __('shifts-workers.active') : __('shifts-workers.inactive') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shifts History -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon primary">
                            <i class="feather icon-calendar"></i>
                        </div>
                        <h3 class="card-title">{{ __('shifts-workers.shift_history') }}</h3>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <form method="GET" action="{{ route('manufacturing.shifts-workers.show', $worker->id) }}" class="mb-4" style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                                <div>
                                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #2c3e50;">{{ __('shifts-workers.shift_types.shift_type') }}</label>
                                    <select name="shift_type" class="form-control" style="padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                                        <option value="">{{ __('app.all') }} {{ __('app.types') }}</option>
                                        <option value="morning" {{ request('shift_type') == 'morning' ? 'selected' : '' }}>{{ __('shifts-workers.shift_types.morning') }}</option>
                                        <option value="evening" {{ request('shift_type') == 'evening' ? 'selected' : '' }}>{{ __('shifts-workers.shift_types.evening') }}</option>
                                        <option value="night" {{ request('shift_type') == 'night' ? 'selected' : '' }}>{{ __('shifts-workers.shift_types.night') }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #2c3e50;">{{ __('shifts-workers.from_date') }}</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" style="padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                                </div>

                                <div>
                                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #2c3e50;">{{ __('shifts-workers.to_date') }}</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" style="padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                                </div>

                                <div style="display: flex; align-items: flex-end; gap: 8px;">
                                    <button type="submit" class="action-btn view" style="flex: 1;">
                                        <i class="feather icon-search"></i> {{ __('app.buttons.search') }}
                                    </button>
                                    <a href="{{ route('manufacturing.workers.show', $worker->id) }}" class="action-btn" style="background: #95a5a6; color: white; flex: 1; justify-content: center;">
                                        <i class="feather icon-x"></i> {{ __('app.buttons.reset') }}
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Shifts Table -->
                        @if($shifts->count() > 0)
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>{{ __('shifts-workers.shift_number') }}</th>
                                            <th>{{ __('shifts-workers.date') }}</th>
                                            <th>{{ __('shifts-workers.type') }}</th>
                                            <th>{{ __('shifts-workers.time') }}</th>
                                            <th>{{ __('shifts-workers.stage') }}</th>
                                            <th>{{ __('app.status.status') }}</th>
                                            <th>{{ __('app.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($shifts as $shift)
                                            <tr>
                                                <td><code>{{ $shift->shift_code ?? '-' }}</code></td>
                                                <td>{{ $shift->shift_date?->format('Y-m-d') ?? '-' }}</td>
                                                <td>
                                                    @php
                                                        $typeLabel = match($shift->shift_type ?? null) {
                                                            'morning' => __('shifts-workers.shift_types.morning'),
                                                            'evening' => __('shifts-workers.shift_types.evening'),
                                                            'night' => __('shifts-workers.shift_types.night'),
                                                            default => $shift->shift_type ?? '-'
                                                        };
                                                        $typeColor = match($shift->shift_type ?? null) {
                                                            'morning' => '#27ae60',
                                                            'evening' => '#3498db',
                                                            'night' => '#9b59b6',
                                                            default => '#95a5a6'
                                                        };
                                                    @endphp
                                                    <span style="background: {{ $typeColor }}; color: white; padding: 6px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                        {{ $typeLabel }}
                                                    </span>
                                                </td>
                                                <td>{{ $shift->start_time }} - {{ $shift->end_time }}</td>
                                                <td>{{ __('shifts-workers.stage') }} {{ $shift->stage_number ?? '-' }}</td>
                                                <td>
                                                    @php
                                                        $statusLabel = match($shift->status ?? null) {
                                                            'scheduled' => __('shifts-workers.shift_statuses.scheduled'),
                                                            'active' => __('shifts-workers.shift_statuses.active'),
                                                            'completed' => __('shifts-workers.shift_statuses.completed'),
                                                            'cancelled' => __('shifts-workers.shift_statuses.cancelled'),
                                                            default => $shift->status ?? '-'
                                                        };
                                                        $statusBg = match($shift->status ?? null) {
                                                            'scheduled' => '#fff3cd',
                                                            'active' => '#d4edda',
                                                            'completed' => '#d1ecf1',
                                                            'cancelled' => '#f8d7da',
                                                            default => '#e2e3e5'
                                                        };
                                                        $statusColor = match($shift->status ?? null) {
                                                            'scheduled' => '#856404',
                                                            'active' => '#155724',
                                                            'completed' => '#0c5460',
                                                            'cancelled' => '#721c24',
                                                            default => '#383d41'
                                                        };
                                                    @endphp
                                                    <span style="background: {{ $statusBg }}; color: {{ $statusColor }}; padding: 6px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                        {{ $statusLabel }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="view-shift-btn" onclick="viewShiftDetails({{ $shift->id }})">
                                                        <i class="feather icon-eye"></i> {{ __('shifts-workers.show_details') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div style="margin-top: 20px; display: flex; justify-content: center;">
                                {{ $shifts->links() }}
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="feather icon-inbox"></i>
                                <p><strong>{{ __('shifts-workers.no_shifts') }}</strong></p>
                                <small>{{ __('shifts-workers.no_shifts_assigned') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // View Shift Details Modal
        function viewShiftDetails(shiftId) {
            const modal = document.createElement('div');
            modal.innerHTML = `
                <div id="movementDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
                    <div style="background: white; border-radius: 12px; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                        <!-- Modal Header -->
                        <div style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="margin: 0; font-size: 20px; font-weight: 700;">
                                <i class="feather icon-info"></i>
                                {{ __('workers.shift_details') }}
                            </h3>
                            <button onclick="closeMovementModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; font-size: 20px; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                                <i class="feather icon-x"></i>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div id="movementDetailsContent" style="padding: 25px;">
                            <div style="text-align: center; padding: 40px;">
                                <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">{{ __('workers.loading') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            document.body.appendChild(modal);

            const modalElement = document.getElementById('movementDetailsModal');
            modalElement.style.display = 'flex';

            // Fetch shift details
            fetch(`/manufacturing/shifts-workers/${shiftId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error('Failed to load shift details');
                    }

                    const shift = data.shift;

                    // Shift type colors
                    const typeColors = {
                        'morning': '#27ae60',
                        'evening': '#3498db',
                        'night': '#9b59b6'
                    };

                    const color = typeColors[shift.shift_type] || '#95a5a6';

                    const content = document.getElementById('movementDetailsContent');
                    content.innerHTML = `
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-right: 4px solid ${color};">
                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">{{ __('workers.shift_number') }}</div>
                                <div style="font-size: 16px; font-weight: 700; color: #2c3e50;">
                                    <code style="background: white; padding: 6px 10px; border-radius: 4px;">${shift.shift_code || '-'}</code>
                                </div>
                            </div>

                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-right: 4px solid ${color};">
                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">{{ __('workers.shift_types.shift_type') }}</div>
                                <div style="font-size: 16px; font-weight: 700;">
                                    <span style="background: ${color}; color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px;">${getShiftTypeLabel(shift.shift_type)}</span>
                                </div>
                            </div>
                        </div>

                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <h4 style="margin: 0 0 15px 0; font-size: 16px; color: #2c3e50; font-weight: 700; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
                                <i class="feather icon-calendar"></i> {{ __('workers.shift_information') }}
                            </h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">{{ __('workers.date') }}</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #2c3e50;">${shift.shift_date || '-'}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">{{ __('workers.stage') }}</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #2c3e50;">${shift.stage_number ? '{{ __('workers.stage') }} ' + shift.stage_number : '-'}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">{{ __('app.start_time') }}</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #2c3e50;">${shift.start_time || '-'}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">{{ __('app.end_time') }}</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #2c3e50;">${shift.end_time || '-'}</div>
                                </div>
                            </div>
                        </div>

                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <h4 style="margin: 0 0 15px 0; font-size: 16px; color: #2c3e50; font-weight: 700; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
                                <i class="feather icon-activity"></i> {{ __('app.status.status') }}
                            </h4>
                            <div style="display: flex; justify-content: center;">
                                ${(() => {
                                    const statusColors = {
                                        'scheduled': ['#fff3cd', '#856404', '{{ __('workers.shift_statuses.scheduled') }}'],
                                        'active': ['#d4edda', '#155724', '{{ __('workers.shift_statuses.active') }}'],
                                        'completed': ['#d1ecf1', '#0c5460', '{{ __('workers.shift_statuses.completed') }}'],
                                        'cancelled': ['#f8d7da', '#721c24', '{{ __('workers.shift_statuses.cancelled') }}']
                                    };
                                    const [bg, text, label] = statusColors[shift.status] || ['#e2e3e5', '#383d41', shift.status];
                                    return `<span style="background: ${bg}; color: ${text}; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 600;">${label}</span>`;
                                })()}
                            </div>
                        </div>

                        ${shift.notes ? `
                        <div style="background: #fff3cd; padding: 15px; border-radius: 8px; border-right: 4px solid #f39c12; margin-bottom: 20px;">
                            <h4 style="margin: 0 0 10px 0; font-size: 14px; color: #856404; font-weight: 700;">
                                <i class="feather icon-file-text"></i> {{ __('shifts-workers.notes') }}
                            </h4>
                            <p style="margin: 0; color: #856404; font-size: 13px;">${shift.notes}</p>
                        </div>
                        ` : ''}
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    const content = document.getElementById('movementDetailsContent');
                    content.innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #e74c3c;">
                            <i class="feather icon-alert-circle" style="font-size: 48px; margin-bottom: 15px;"></i>
                            <p style="margin: 0; font-size: 16px; font-weight: 600;">{{ __('workers.error_loading_data') }}</p>
                            <small style="color: #95a5a6;">{{ __('app.please_try_again') }}</small>
                        </div>
                    `;
                });
        }

        function closeMovementModal() {
            const modal = document.getElementById('movementDetailsModal');
            if (modal) {
                modal.remove();
            }
        }

        function getShiftTypeLabel(type) {
            const labels = {
                'morning': '{{ __('workers.shift_types.morning') }}',
                'evening': '{{ __('workers.shift_types.evening') }}',
                'night': '{{ __('workers.shift_types.night') }}'
            };
            return labels[type] || type;
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('movementDetailsModal');
            if (modal && e.target === modal) {
                closeMovementModal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert:not(.alert-info)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
@endsection
