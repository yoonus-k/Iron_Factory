@extends('master')

@section('title', __('stages.stage1_details'))

@section('content')

<style>
    /* ========== تنسيق الألوان حسب الشعار ========== */
    :root {
        --primary-blue: #1e3a8a;
        --secondary-blue: #3b82f6;
        --light-blue: #60a5fa;
        --dark-blue: #1e40af;
        --pale-blue: #dbeafe;
        --border-color: #bfdbfe;
    }

    body {
        background-color: #f9fafb;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .detail-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 4px 16px rgba(30, 58, 138, 0.08);
        border-right: 5px solid var(--primary-blue);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .detail-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(30, 58, 138, 0.12);
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 3px solid var(--light-gray);
        background: linear-gradient(to right, rgba(30, 58, 138, 0.03), transparent);
        padding: 15px;
        border-radius: 8px;
    }

    .detail-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--primary-blue);
        display: flex;
        align-items: center;
        gap: 12px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .detail-title i {
        color: var(--light-blue);
        font-size: 24px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 25px;
    }

    .info-item {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 20px;
        border-radius: 12px;
        border-right: 4px solid var(--secondary-blue);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .info-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(to right, var(--primary-blue), var(--light-blue));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .info-item:hover {
        transform: translateX(-3px);
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.1);
        border-right-color: var(--light-blue);
    }

    .info-item:hover::before {
        opacity: 1;
    }

    .info-label {
        font-size: 14px;
        color: var(--secondary-blue);
        margin-bottom: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 20px;
        color: var(--primary-blue);
        font-weight: 700;
    }

    .barcode-display {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 50%, var(--light-blue) 100%);
        color: white;
        padding: 40px;
        border-radius: 16px;
        text-align: center;
        margin: 25px 0;
        box-shadow: 0 8px 24px rgba(30, 58, 138, 0.2);
        position: relative;
        overflow: hidden;
    }

    .barcode-display::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: pulse 3s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.3; }
        50% { transform: scale(1.1); opacity: 0.6; }
    }

    .barcode-code {
        font-size: 36px;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        letter-spacing: 6px;
        margin: 20px 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        position: relative;
        z-index: 1;
    }

    .log-item {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 15px;
        border-right: 4px solid var(--secondary-blue);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .log-item:hover {
        transform: translateX(-3px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .log-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
    }

    .log-action {
        font-weight: 600;
        color: var(--primary-blue);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .log-time {
        color: var(--primary-blue);
        font-size: 13px;
        background: var(--pale-blue);
        padding: 4px 10px;
        border-radius: 6px;
    }

    .log-details {
        color: var(--dark-blue);
        font-size: 14px;
        line-height: 1.8;
    }

    .empty-logs {
        text-align: center;
        padding: 60px 40px;
        color: var(--secondary-blue);
    }

    .empty-logs i {
        font-size: 64px;
        opacity: 0.3;
        color: var(--primary-blue);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-created {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: var(--primary-blue);
        border: 1px solid #93c5fd;
    }
    .status-in_process {
        background: linear-gradient(135deg, #bfdbfe 0%, #93c5fd 100%);
        color: var(--dark-blue);
        border: 1px solid var(--secondary-blue);
    }
    .status-completed {
        background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
        color: white;
        border: 1px solid var(--primary-blue);
    }
    .status-consumed {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: var(--primary-blue);
        border: 1px solid #93c5fd;
    }

    .form-control {
        padding: 12px 18px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--secondary-blue);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    select.form-control {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231e3a8a' stroke-width='2'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: left 12px center;
        padding-left: 40px;
        appearance: none;
    }

    .um-btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .um-btn-success {
        background: linear-gradient(135deg, var(--secondary-blue) 0%, var(--primary-blue) 100%);
        color: white;
    }

    .um-btn-success:hover:not(:disabled) {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--dark-blue) 100%);
        box-shadow: 0 4px 16px rgba(30, 58, 138, 0.3);
        transform: translateY(-2px);
    }

    .um-btn-success:disabled {
        background: var(--pale-blue);
        cursor: not-allowed;
        opacity: 0.6;
    }

    .um-btn-primary {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        color: white;
    }

    .um-btn-primary:hover {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        box-shadow: 0 4px 16px rgba(30, 58, 138, 0.3);
        transform: translateY(-2px);
    }

    /* تحسينات إضافية للعناصر */
    .um-content-wrapper {
        padding: 30px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .um-header-section {
        background: white;
        padding: 25px 30px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 2px 12px rgba(30, 58, 138, 0.08);
        border-right: 5px solid var(--light-blue);
    }

    .um-page-title {
        color: var(--primary-blue);
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
    }

    .um-page-title i {
        color: var(--light-blue);
    }

    .um-breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--secondary-blue);
        font-size: 14px;
    }

    .um-breadcrumb-nav a {
        color: var(--primary-blue);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .um-breadcrumb-nav a:hover {
        color: var(--light-blue);
    }

</style>

<div class="um-content-wrapper">
    <!-- Header Section -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <i class="feather icon-eye"></i>
            {{ __('stages.stand_details_title') }} - {{ $stand->stand_number }}
        </h1>
        <nav class="um-breadcrumb-nav">
            <span><i class="feather icon-home"></i> {{ __('stages.dashboard') }}</span>
            <i class="feather icon-chevron-left"></i>
            <a href="{{ route('manufacturing.stage1.index') }}">{{ __('stages.first_phase') }}</a>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('stages.stand_details') }}</span>
        </nav>
    </div>

    <!-- Barcode Display -->
    <div class="barcode-display">
        <div style="font-size: 18px; opacity: 0.9; margin-bottom: 10px;">{{ __('stages.barcode_title') }}</div>
        <div class="barcode-code">{{ $stand->barcode }}</div>
        <button onclick="printBarcode('{{ $stand->barcode }}', '{{ $stand->stand_number }}', '{{ $stand->material_name }}', {{ $stand->remaining_weight }})"
                style="background: rgba(255,255,255,0.2); border: 2px solid white; color: white; padding: 12px 30px; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 15px; transition: all 0.3s;">
            <i class="feather icon-printer"></i> {{ __('stages.print_barcode_button') }}
        </button>
    </div>

    <!-- {{ __('stages.basic_information') }} -->
    <div class="detail-card">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-info"></i>
                {{ __('stages.basic_information') }}
            </div>
            @if($stand->status == 'created')
            <span class="status-badge status-created">{{ __('stages.stand_status_created') }}</span>
            @elseif($stand->status == 'in_process')
            <span class="status-badge status-in_process">{{ __('stages.stand_status_in_process') }}</span>
            @elseif($stand->status == 'completed')
            <span class="status-badge status-completed">{{ __('stages.stand_status_completed') }}</span>
            @elseif($stand->status == 'consumed')
            <span class="status-badge status-consumed">{{ __('stages.stand_status_consumed') }}</span>
            @endif
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">{{ __('stages.stand_number_label') }}</div>
                <div class="info-value">{{ $stand->stand_number }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.material_label') }}</div>
                <div class="info-value">{{ $stand->material_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.parent_barcode_label') }}</div>
                <div class="info-value" style="font-size: 14px; font-family: monospace;">{{ $stand->parent_barcode }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.total_weight_label') }}</div>
                <div class="info-value" style="color: #3498db;">{{ number_format($stand->weight, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.net_weight_label') }}</div>
                <div class="info-value" style="color: #27ae60;">{{ number_format($stand->remaining_weight, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.waste_label') }}</div>
                <div class="info-value" style="color: #e74c3c;">{{ number_format($stand->waste, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.wire_size_label') }}</div>
                <div class="info-value">{{ $stand->wire_size }} مم</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.created_by_label') }}</div>
                <div class="info-value" style="font-size: 16px;">{{ $stand->created_by_name ?? __('stages.not_specified') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.created_at_label') }}</div>
                <div class="info-value" style="font-size: 16px;">{{ \Carbon\Carbon::parse($stand->created_at)->format('Y-m-d H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- {{ __('stages.usage_history') }} -->
    @if($usageHistory)
    <div class="detail-card" style="border-right-color: #27ae60;">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-activity"></i>
                {{ __('stages.usage_history') }}
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">{{ __('stages.user_label') }}</div>
                <div class="info-value" style="font-size: 16px;">{{ $usageHistory->user_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.total_weight_label') }}</div>
                <div class="info-value">{{ number_format($usageHistory->total_weight, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.net_weight_label') }}</div>
                <div class="info-value">{{ number_format($usageHistory->net_weight, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.stand_weight_label') }}</div>
                <div class="info-value">{{ number_format($usageHistory->stand_weight, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.waste_percentage_label') }}</div>
                <div class="info-value">{{ number_format($usageHistory->waste_percentage, 2) }}%</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.start_time_label') }}</div>
                <div class="info-value" style="font-size: 14px;">{{ \Carbon\Carbon::parse($usageHistory->started_at)->format('Y-m-d H:i') }}</div>
            </div>
        </div>

        @if($usageHistory->notes)
        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 8px; border-right: 3px solid #ffc107;">
            <strong style="color: #856404;">📝 {{ __('stages.notes_label') }}:</strong>
            <p style="margin: 8px 0 0 0; color: #856404;">{{ $usageHistory->notes }}</p>
        </div>
        @endif
    </div>
    @endif

    <!-- {{ __('stages.current_shift') }} -->
    @if($currentShift)
    <div class="detail-card" style="border-right-color: #3498db;">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-briefcase"></i>
                {{ __('stages.current_shift') }}
            </div>
            <span style="background: #27ae60; color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600;">
                <i class="feather icon-check-circle"></i> {{ __('stages.shift_active') }}
            </span>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">{{ __('stages.shift_code_label') }}</div>
                <div class="info-value" style="font-size: 16px;">{{ $currentShift->shift_code }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.current_worker_label') }}</div>
                <div class="info-value" style="font-size: 16px;">{{ $currentShift->worker_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.supervisor_label') }}</div>
                <div class="info-value" style="font-size: 16px;">
                    @if($currentShiftSupervisor)
                        <span style="background: #e3f2fd; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                            <i class="feather icon-user-check"></i> {{ $currentShiftSupervisor->name }}
                        </span>
                    @else
                        <span style="color: #e74c3c;">{{ __('stages.no_supervisor') }}</span>
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.shift_type_label') }}</div>
                <div class="info-value" style="font-size: 16px;">
                    @if($currentShift->shift_type == 'morning')
                        {{ __('stages.shift_type_morning') }}
                    @elseif($currentShift->shift_type == 'evening')
                        {{ __('stages.shift_type_evening') }}
                    @else
                        {{ $currentShift->shift_type }}
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.start_time_label') }}</div>
                <div class="info-value" style="font-size: 16px;">{{ \Carbon\Carbon::parse($currentShift->start_time)->format('H:i') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.shift_date_label') }}</div>
                <div class="info-value" style="font-size: 16px;">{{ \Carbon\Carbon::parse($currentShift->shift_date)->format('Y-m-d') }}</div>
            </div>
        </div>

        <!-- قسم العمال الحاليين -->
        @if($currentShiftWorkers && count($currentShiftWorkers) > 0)
        <div style="margin-top: 20px; padding: 20px; background: #f0f7ff; border-radius: 8px; border-right: 3px solid #3498db;">
            <h5 style="color: #1565c0; margin-bottom: 15px; font-weight: 700;">
                <i class="feather icon-users"></i>
                {{ __('stages.shift_registered_workers') }} ({{ count($currentShiftWorkers) }})
            </h5>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px;">
                @foreach($currentShiftWorkers as $worker)
                @if(isset($worker->worker_type))
                    <!-- من worker_stage_history -->
                    <div style="background: white; padding: 15px; border-radius: 8px; border-left: 3px solid #27ae60; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            @if($worker->worker_type == 'individual')
                                <span style="background: #e8f5e9; color: #27ae60; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                    <i class="feather icon-user"></i>
                                </span>
                            @else
                                <span style="background: #e3f2fd; color: #1976d2; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                    <i class="feather icon-users"></i>
                                </span>
                            @endif
                            <div style="flex: 1;">
                                <strong style="color: #2c3e50; display: block; font-size: 15px;">
                                    @if($worker->worker_type == 'individual')
                                        {{ $worker->worker_name }}
                                    @else
                                        {{ $worker->team_name ?? 'فريق' }}
                                    @endif
                                </strong>
                                <small style="color: #7f8c8d;">
                                    @if($worker->worker_type == 'individual')
                                        {{ __('stages.individual_worker') }}
                                    @else
                                        {{ __('stages.team_work') }}
                                    @endif
                                </small>
                            </div>
                        </div>

                        <div style="background: #f8f9fa; padding: 10px; border-radius: 6px; font-size: 13px;">
                            <div style="margin-bottom: 6px;">
                                <span style="color: #7f8c8d;">📅 {{ __('stages.start') }}:</span>
                                <span style="color: #2c3e50; font-weight: 600;">{{ \Carbon\Carbon::parse($worker->started_at)->format('H:i') }}</span>
                            </div>
                            @if($worker->ended_at)
                            <div style="margin-bottom: 6px;">
                                <span style="color: #7f8c8d;">⏹️ {{ __('stages.end') }}:</span>
                                <span style="color: #2c3e50; font-weight: 600;">{{ \Carbon\Carbon::parse($worker->ended_at)->format('H:i') }}</span>
                            </div>
                            @endif
                            @if($worker->duration_minutes)
                            <div>
                                <span style="color: #7f8c8d;">⏱️ {{ __('stages.duration') }}:</span>
                                <span style="color: #2c3e50; font-weight: 600;">
                                    @php
                                        $hours = floor($worker->duration_minutes / 60);
                                        $mins = $worker->duration_minutes % 60;
                                        echo ($hours > 0 ? $hours . ' ' . __('stages.hour_unit') . ' ' : '') . $mins . ' ' . __('stages.minute_unit');
                                    @endphp
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- من shift_assignments worker_ids -->
                    <div style="background: white; padding: 15px; border-radius: 8px; border-left: 3px solid #27ae60; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span style="background: #e8f5e9; color: #27ae60; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                <i class="feather icon-user"></i>
                            </span>
                            <div style="flex: 1;">
                                <strong style="color: #2c3e50; display: block; font-size: 15px;">
                                    {{ $worker->name ?? $worker->worker_name }}
                                </strong>
                                <small style="color: #7f8c8d;">عامل</small>
                            </div>
                        </div>

                        <div style="background: #f8f9fa; padding: 10px; border-radius: 6px; font-size: 13px;">
                            <span style="color: #27ae60; font-weight: 600;">✓ {{ __('stages.registered_in_shift') }}</span>
                        </div>
                    </div>
                @endif
                @endforeach
            </div>
        </div>
        @else
        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 8px; border-right: 3px solid #ffc107;">
            <i class="feather icon-alert-circle" style="color: #f39c12;"></i>
            <span style="color: #856404;">{{ __('stages.no_workers_registered_current_shift') }}</span>
        </div>
        @endif

        <!-- {{ __('stages.shift_transfer_options') }} -->
        @if(auth()->user()->hasPermission('SHIFT_HANDOVERS_CREATE') && $currentShift)
        <div style="margin-top: 20px; padding: 15px; background: #e3f2fd; border-radius: 8px; border-right: 3px solid #3498db;">
            <h6 style="color: #1565c0; margin-bottom: 15px; font-weight: 700;">
                <i class="feather icon-arrow-right-circle"></i>
                {{ __('stages.shift_transfer_options') }}
            </h6>

            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <!-- {{ __('stages.transfer_stage_button') }} -->
                <a href="{{ route('manufacturing.shift-handovers.transfer-stage', ['stage_record_id' => $stand->id, 'stage_number' => 1, 'from_shift_id' => $currentShift->id]) }}"
                   class="um-btn um-btn-success" style="white-space: nowrap;">
                    <i class="feather icon-send"></i> {{ __('stages.transfer_stage_button') }}
                </a>
            </div>
            <small style="display: block; margin-top: 12px; color: #1565c0;">
                <i class="feather icon-info"></i>
                • {{ __('stages.transfer_stage_note') }}
            </small>
        </div>
        @endif
    </div>
    @endif

    <!-- {{ __('stages.previous_shifts_history') }} -->
    @if($previousShiftsData && count($previousShiftsData) > 0)
    <div class="detail-card" style="border-right-color: #f39c12;">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-history"></i>
                {{ __('stages.previous_shifts_history') }} ({{ count($previousShiftsData) }})
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 20px;">
            @foreach($previousShiftsData as $shiftData)
            <div style="background: #fff; padding: 20px; border-radius: 8px; border-right: 3px solid #f39c12; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <!-- معلومات الوردية والمسؤول -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
                    <div>
                        <strong style="color: #2c3e50; display: block; font-size: 15px; margin-bottom: 4px;">
                            {{ $shiftData['shift']->shift_code }}
                        </strong>
                        <small style="color: #7f8c8d;">
                            {{ \Carbon\Carbon::parse($shiftData['shift']->created_at)->format('d/m/Y H:i') }}
                        </small>
                    </div>

                    @if($shiftData['supervisor'])
                    <div style="text-align: right;">
                        <span style="background: #e8f5e9; color: #27ae60; padding: 8px 15px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="feather icon-user-check"></i>
                            {{ $shiftData['supervisor']->name }}
                        </span>
                        <small style="display: block; color: #7f8c8d; margin-top: 4px;">{{ __('stages.supervisor_label') }}</small>
                    </div>
                    @endif
                </div>

                <!-- قائمة العمال في الوردية السابقة -->
                @if($shiftData['workers'] && count($shiftData['workers']) > 0)
                <div style="margin-bottom: 15px;">
                    <h6 style="color: #2c3e50; font-weight: 700; margin-bottom: 12px; font-size: 14px;">
                        <i class="feather icon-users"></i>
                        {{ __('stages.workers_registered') }} ({{ count($shiftData['workers']) }})
                    </h6>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 12px;">
                        @foreach($shiftData['workers'] as $worker)
                        <div style="background: #f8f9fa; padding: 12px; border-radius: 6px; border-left: 2px solid #f39c12;">
                            <div style="display: flex; align-items: flex-start; gap: 10px;">
                                @if(isset($worker->worker_type) && $worker->worker_type == 'individual')
                                    <span style="background: #e8f5e9; color: #27ae60; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; margin-top: 2px;">
                                        <i class="feather icon-user" style="font-size: 14px;"></i>
                                    </span>
                                @elseif(isset($worker->worker_type))
                                    <span style="background: #e3f2fd; color: #1976d2; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; margin-top: 2px;">
                                        <i class="feather icon-users" style="font-size: 14px;"></i>
                                    </span>
                                @else
                                    <span style="background: #e8f5e9; color: #27ae60; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; margin-top: 2px;">
                                        <i class="feather icon-user" style="font-size: 14px;"></i>
                                    </span>
                                @endif

                                <div style="flex: 1; min-width: 0;">
                                    <strong style="color: #2c3e50; display: block; font-size: 14px; word-break: break-word;">
                                        @if(isset($worker->worker_type) && $worker->worker_type == 'team')
                                            {{ $worker->team_name ?? 'فريق عمل' }}
                                        @else
                                            {{ $worker->name ?? $worker->worker_name }}
                                        @endif
                                    </strong>
                                    <small style="color: #7f8c8d; display: block;">
                                        @if(isset($worker->worker_type) && $worker->worker_type == 'team')
                                            {{ __('stages.team_work') }}
                                        @else
                                            {{ __('stages.worker') }}
                                        @endif
                                    </small>

                                    @if(isset($worker->started_at))
                                    <div style="margin-top: 6px; padding: 6px; background: white; border-radius: 4px; font-size: 12px;">
                                        <div style="color: #7f8c8d; margin-bottom: 3px;">
                                            🕐 {{ \Carbon\Carbon::parse($worker->started_at)->format('H:i') }}
                                            @if(isset($worker->ended_at) && $worker->ended_at)
                                                - {{ \Carbon\Carbon::parse($worker->ended_at)->format('H:i') }}
                                            @endif
                                        </div>
                                        @if(isset($worker->duration_minutes) && $worker->duration_minutes)
                                        <div style="color: #27ae60; font-weight: 600;">
                                            ⏱️
                                            @php
                                                $hours = floor($worker->duration_minutes / 60);
                                                $mins = $worker->duration_minutes % 60;
                                                echo ($hours > 0 ? $hours . ' ' . __('stages.hour_unit') . ' ' : '') . $mins . ' ' . __('stages.minute_unit');
                                            @endphp
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div style="padding: 12px; background: #fff3cd; border-radius: 6px; color: #856404; font-size: 13px;">
                    <i class="feather icon-alert-circle"></i> {{ __('stages.no_workers_in_this_shift') }}
                </div>
                @endif

                @if($shiftData['shift']->notes)
                <div style="margin-top: 12px; padding: 12px; background: #fff3cd; border-radius: 6px; border-right: 2px solid #ffc107; font-size: 13px;">
                    <strong style="color: #856404;">📝 {{ __('stages.notes') }}:</strong>
                    <p style="margin: 6px 0 0 0; color: #856404;">{{ $shiftData['shift']->notes }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @elseif($previousShifts && count($previousShifts) > 0)
    <div class="detail-card" style="border-right-color: #f39c12;">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-history"></i>
                {{ __('stages.previous_shifts_history') }} ({{ count($previousShifts) }})
            </div>
        </div>

        <div style="max-height: 400px; overflow-y: auto;">
            @foreach($previousShifts as $shift)
            <div style="background: #fff; padding: 15px; margin-bottom: 12px; border-radius: 8px; border-left: 3px solid #f39c12;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <div>
                        <strong style="color: #2c3e50; display: block; font-size: 16px;">{{ $shift->worker_name }}</strong>
                        <small style="color: #7f8c8d;">{{ __('stages.worker') }}</small>
                    </div>
                    <div style="text-align: right;">
                        <strong style="color: #2c3e50; display: block; font-size: 16px;">{{ $shift->shift_code }}</strong>
                        <small style="color: #7f8c8d;">{{ __('stages.shift_code') }}</small>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                    <div>
                        <small style="color: #7f8c8d;">{{ __('stages.start_time') }}:</small>
                        <span style="display: block; color: #2c3e50; font-weight: 600;">
                            {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}
                        </span>
                    </div>
                    <div>
                        <small style="color: #7f8c8d;">{{ __('stages.supervisor_label') }}:</small>
                        <span style="display: block; color: #2c3e50; font-weight: 600;">
                            {{ $shift->supervisor_name ?? __('stages.no_supervisor') }}
                        </span>
                    </div>
                </div>

                @if($shift->notes)
                <div style="margin-top: 10px; padding: 8px; background: #f8f9fa; border-radius: 4px;">
                    <small style="color: #7f8c8d;">{{ __('stages.notes') }}:</small>
                    <p style="margin: 4px 0 0 0; color: #555; font-size: 13px;">{{ $shift->notes }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- {{ __('stages.tracking_logs') }} -->
    @if($trackingLogs->count() > 0)
    <div class="detail-card" style="border-right-color: #3498db;">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-map"></i>
                {{ __('stages.tracking_logs') }} ({{ $trackingLogs->count() }})
            </div>
        </div>

        @foreach($trackingLogs as $log)
        <div class="log-item">
            <div class="log-header">
                <div class="log-action">
                    <i class="feather icon-check-circle" style="color: #27ae60;"></i>
                    {{ $log->action }} - {{ $log->stage }}
                </div>
                <div class="log-time">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</div>
            </div>
            <div class="log-details">
                <strong>{{ __('stages.worker_label') }}:</strong> {{ $log->worker_name ?? __('stages.not_specified') }}<br>
                <strong>{{ __('stages.input_weight_label') }}:</strong> {{ number_format($log->input_weight, 2) }} {{ __('stages.weight_unit') }} |
                <strong>{{ __('stages.output_weight_label') }}:</strong> {{ number_format($log->output_weight, 2) }} {{ __('stages.weight_unit') }}<br>
                <strong>{{ __('stages.waste_label') }}:</strong> {{ number_format($log->waste_amount, 2) }} {{ __('stages.weight_unit') }} ({{ number_format($log->waste_percentage, 2) }}%)
                @if($log->notes)
                <br><strong>{{ __('stages.notes_label') }}:</strong> {{ $log->notes }}
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- {{ __('stages.operation_logs') }} -->
    @if($operationLogs->count() > 0)
    <div class="detail-card" style="border-right-color: #f39c12;">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-list"></i>
                {{ __('stages.operation_logs') }} ({{ $operationLogs->count() }})
            </div>
        </div>

        @foreach($operationLogs as $log)
        <div class="log-item" style="border-right-color: #f39c12;">
            <div class="log-header">
                <div class="log-action">
                    <i class="feather icon-activity" style="color: #f39c12;"></i>
                    {{ $log->action ?? __('stages.operation_label') }}
                </div>
                <div class="log-time">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</div>
            </div>
            <div class="log-details">
                <strong>{{ __('stages.user_label') }}:</strong> {{ $log->user_name ?? __('stages.system_label') }}<br>
                @if($log->description)
                <strong>{{ __('stages.description_label') }}:</strong> {{ $log->description }}
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="detail-card">
        <div class="empty-logs">
            <i class="feather icon-inbox" style="font-size: 48px; opacity: 0.3;"></i>
            <p>{{ __('stages.no_operations_logged') }}</p>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div style="display: flex; gap: 15px; margin-top: 25px;">
        <a href="{{ route('manufacturing.stage1.index') }}" class="um-btn um-btn-primary" style="flex: 1;">
            <i class="feather icon-arrow-right"></i> {{ __('stages.back_to_list') }}
        </a>
        <button onclick="printBarcode('{{ $stand->barcode }}', '{{ $stand->stand_number }}', '{{ $stand->material_name }}', {{ $stand->remaining_weight }})"
                class="um-btn um-btn-success" style="flex: 1;">
            <i class="feather icon-printer"></i> {{ __('stages.print_barcode_button') }}
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
function printBarcode(barcode, standNumber, materialName, netWeight) {
    const printWindow = window.open('', '', 'height=650,width=850');
    printWindow.document.write('<html dir="rtl"><head><title>' + '{{ __("stages.print_barcode_title") }}' + ' - ' + standNumber + '</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
    printWindow.document.write('.barcode-container { background: white; padding: 50px; border-radius: 16px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); text-align: center; max-width: 550px; }');
    printWindow.document.write('.title { font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 4px solid #667eea; }');
    printWindow.document.write('.stand-number { font-size: 24px; color: #667eea; font-weight: bold; margin: 20px 0; }');
    printWindow.document.write('.barcode-code { font-size: 22px; font-weight: bold; color: #2c3e50; margin: 25px 0; letter-spacing: 4px; font-family: "Courier New", monospace; }');
    printWindow.document.write('.info { margin-top: 30px; padding: 25px; background: #f8f9fa; border-radius: 10px; text-align: right; }');
    printWindow.document.write('.info-row { margin: 12px 0; display: flex; justify-content: space-between; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 16px; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 18px; }');
    printWindow.document.write('@media print { body { background: white; } }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<div class="barcode-container">');
    printWindow.document.write('<div class="title">{{ __("stages.barcode_title") }}</div>');
    printWindow.document.write('<div class="stand-number">{{ __("stages.stand_label_print") }} ' + standNumber + '</div>');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
    printWindow.document.write('<div class="info">');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.material_label_print") }}:</span><span class="value">' + materialName + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.net_weight_label_print") }}:</span><span class="value">' + netWeight + ' {{ __("stages.weight_unit") }}</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.date_label_print") }}:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
    printWindow.document.write('</div></div>');
    printWindow.document.write('<script>');
    printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2, height: 90, displayValue: false, margin: 12 });');
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
    printWindow.document.write('<\/script></body></html>');
    printWindow.document.close();
}
</script>

@endsection
