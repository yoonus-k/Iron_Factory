@extends('master')

@section('title', __('stages.stage1_details'))

@section('content')

<style>
    .detail-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border-right: 4px solid #667eea;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .detail-title {
        font-size: 20px;
        font-weight: 700;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .info-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-right: 3px solid #667eea;
    }

    .info-label {
        font-size: 13px;
        color: #7f8c8d;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .info-value {
        font-size: 18px;
        color: #2c3e50;
        font-weight: 700;
    }

    .barcode-display {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        margin: 20px 0;
    }

    .barcode-code {
        font-size: 32px;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        letter-spacing: 4px;
        margin: 15px 0;
    }

    .log-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 12px;
        border-right: 3px solid #27ae60;
    }

    .log-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .log-action {
        font-weight: 600;
        color: #2c3e50;
    }

    .log-time {
        color: #7f8c8d;
        font-size: 13px;
    }

    .log-details {
        color: #555;
        font-size: 14px;
        line-height: 1.6;
    }

    .empty-logs {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-created { background: #e3f2fd; color: #1976d2; }
    .status-in_process { background: #fff3e0; color: #f57c00; }
    .status-completed { background: #e8f5e9; color: #388e3c; }
    .status-consumed { background: #f5f5f5; color: #616161; }
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

    <!-- Worker Tracking Section -->
    @php
    $currentWorker = \App\Models\WorkerStageHistory::getCurrentWorkerForStage(
        \App\Models\WorkerStageHistory::STAGE_1_STANDS,
        $stand->id
    );
    $workerStats = \App\Services\WorkerTrackingService::class;
    $stats = app($workerStats)->getStageStatistics(
        \App\Models\WorkerStageHistory::STAGE_1_STANDS,
        $stand->id
    );

    // جلب الوردية الحالية للمرحلة
    $currentShift = \App\Models\ShiftAssignment::where('status', 'active')
        ->where('stage_number', 1)
        ->latest('created_at')
        ->first();
    @endphp

    <!-- Current Shift Section -->
    @if($currentShift)
    <div class="detail-card" style="border-right-color: #3498db; background: linear-gradient(135deg, rgba(52, 152, 219, 0.05) 0%, rgba(41, 128, 185, 0.05) 100%);">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-briefcase"></i>
                الوردية الحالية
            </div>
            <a href="{{ route('manufacturing.shifts-workers.show', $currentShift->id) }}" class="um-btn um-btn-sm um-btn-info">
                <i class="feather icon-external-link"></i>
                عرض تفاصيل الوردية
            </a>
        </div>

        <div style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div>
                    <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">رقم الوردية</div>
                    <div style="font-size: 20px; font-weight: 700;">{{ $currentShift->shift_code }}</div>
                </div>
                <div>
                    <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">المسؤول</div>
                    <div style="font-size: 20px; font-weight: 700;">
                        <i class="feather icon-user"></i>
                        {{ $currentShift->supervisor->name ?? 'لم يحدد' }}
                    </div>
                </div>
                <div>
                    <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">فترة العمل</div>
                    <div style="font-size: 20px; font-weight: 700;">
                        {{ $currentShift->shift_type == 'morning' ? 'الفترة الأولى' : 'الفترة الثانية' }}
                    </div>
                </div>
                <div>
                    <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">عدد العمال</div>
                    <div style="font-size: 20px; font-weight: 700;">{{ count($currentShift->worker_ids ?? []) }}</div>
                </div>
            </div>
        </div>

        <!-- Workers in Current Shift -->
        @if(count($currentShift->worker_ids ?? []) > 0)
        <div style="margin-top: 20px;">
            <h5 style="font-size: 16px; font-weight: 700; margin-bottom: 15px; color: #2c3e50;">العمال في هذه الوردية:</h5>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px;">
                @php
                $shiftWorkers = \App\Models\Worker::whereIn('id', $currentShift->worker_ids ?? [])->get();
                @endphp
                @foreach($shiftWorkers as $worker)
                <div style="background: white; border: 2px solid #e0e0e0; border-radius: 8px; padding: 12px; transition: all 0.3s;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                            {{ substr($worker->name, 0, 1) }}
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #2c3e50;">{{ $worker->name }}</div>
                            <div style="font-size: 12px; color: #7f8c8d;">{{ $worker->worker_code }}</div>
                            @if($worker->assigned_stage)
                            <div style="font-size: 12px; color: #3498db; font-weight: 600; margin-top: 4px;">
                                المرحلة: {{ $worker->assigned_stage }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="detail-card" style="border-right-color: #95a5a6; background: linear-gradient(135deg, rgba(149, 165, 166, 0.05) 0%, rgba(127, 140, 141, 0.05) 100%);">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-briefcase"></i>
                الوردية الحالية
            </div>
        </div>
        <div class="alert alert-warning" style="margin: 0;">
            <i class="feather icon-alert-circle"></i>
            لا توجد وردية نشطة للمرحلة 1 حالياً
        </div>
    </div>
    @endif

    <div class="detail-card" style="border-right-color: #9b59b6;">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-users"></i>
                {{ __('worker-tracking.worker_tracking') }}
            </div>
            <a href="{{ route('worker-tracking.stage-history', ['stageType' => 'stage1_stands', 'stageRecordId' => $stand->id]) }}"
               class="um-btn um-btn-sm um-btn-info">
                <i class="feather icon-history"></i>
                {{ __('worker-tracking.view_history') }}
            </a>
        </div>

        @if($currentWorker)
        <div style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">{{ __('worker-tracking.current_worker') }}</div>
                    <div style="font-size: 24px; font-weight: 700;">{{ $currentWorker->worker_name }}</div>
                    <div style="margin-top: 8px; opacity: 0.95;">
                        <i class="feather icon-clock"></i>
                        {{ __('worker-tracking.started_at') }}: {{ $currentWorker->started_at->format('Y-m-d H:i') }}
                        <span style="margin: 0 10px;">•</span>
                        {{ $currentWorker->started_at->diffForHumans() }}
                    </div>
                </div>
                <div style="text-align: center; background: rgba(255,255,255,0.2); padding: 15px 25px; border-radius: 8px;">
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">{{ __('worker-tracking.work_duration') }}</div>
                    <div style="font-size: 28px; font-weight: 700;">{{ $currentWorker->formatted_duration }}</div>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-info" style="margin-bottom: 20px;">
            <i class="feather icon-info"></i>
            {{ __('worker-tracking.no_current_worker') }}
        </div>
        @endif

        <div class="info-grid">
            <div class="info-item" style="border-right-color: #9b59b6;">
                <div class="info-label">{{ __('worker-tracking.total_sessions') }}</div>
                <div class="info-value" style="color: #9b59b6;">{{ $stats['total_sessions'] }}</div>
            </div>
            <div class="info-item" style="border-right-color: #9b59b6;">
                <div class="info-label">{{ __('worker-tracking.total_workers') }}</div>
                <div class="info-value" style="color: #9b59b6;">{{ $stats['total_workers'] }}</div>
            </div>
            <div class="info-item" style="border-right-color: #9b59b6;">
                <div class="info-label">{{ __('worker-tracking.total_hours') }}</div>
                <div class="info-value" style="color: #9b59b6;">{{ number_format($stats['total_hours'], 1) }}</div>
            </div>
            <div class="info-item" style="border-right-color: #9b59b6;">
                <div class="info-label">{{ __('worker-tracking.average_session_time') }}</div>
                <div class="info-value" style="color: #9b59b6;">{{ number_format($stats['average_session_minutes'] ?? 0, 0) }} {{ __('worker-tracking.minutes') }}</div>
            </div>
        </div>
    </div>

    <!-- Workers in Stage Section -->
    <div class="detail-card" style="border-right-color: #27ae60;">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-users"></i>
                العمال في المرحلة 1
            </div>
        </div>

        @php
        // جلب جميع العمال الحاليين في المرحلة 1
        $workersInStage = \App\Models\WorkerStageHistory::where('stage_type', 'stage1_stands')
            ->where('stage_record_id', $stand->id)
            ->where('is_active', true)
            ->whereNull('ended_at')
            ->orderBy('started_at', 'desc')
            ->get();
        @endphp

        @if($workersInStage->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead style="background: #f5f5f5; border-bottom: 2px solid #e0e0e0;">
                    <tr>
                        <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">اسم العامل</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">الوردية</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">وقت البدء</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">المدة</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workersInStage as $history)
                    <tr style="border-bottom: 1px solid #f0f0f0; transition: background 0.2s;">
                        <td style="padding: 12px; color: #333; font-weight: 600;">
                            {{ $history->worker_name }}
                        </td>
                        <td style="padding: 12px; color: #666;">
                            <span style="display: inline-block; background: #e3f2fd; padding: 4px 12px; border-radius: 20px; font-size: 12px; color: #0066B2;">
                                {{ $history->shift_code ?? 'غير محددة' }}
                            </span>
                        </td>
                        <td style="padding: 12px; color: #666; font-size: 13px;">
                            {{ $history->started_at->format('Y-m-d H:i') }}
                        </td>
                        <td style="padding: 12px; color: #666; font-weight: 600;">
                            {{ $history->formatted_duration }}
                        </td>
                        <td style="padding: 12px;">
                            <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                                background: #d4edda; color: #155724;">
                                جاري
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 30px; color: #999;">
            <i class="feather icon-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
            <p style="margin: 0;">لا يوجد عمال في هذه المرحلة حالياً</p>
        </div>
        @endif
    </div>

    <!-- Transfer History Section -->
    <div class="detail-card" style="border-right-color: #e74c3c;">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-repeat"></i>
                سجل النقل والتعديلات (اعتماداً على الوردية)
            </div>
        </div>

        @php
        // جلب سجل العمليات المتعلقة بهذه الوردية
        $transferLogs = \App\Models\ShiftOperationLog::where('stage_number', 1)
            ->whereIn('operation_type', ['transfer', 'create', 'update', 'assign_stage'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();
        @endphp

        @if($transferLogs->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead style="background: #f5f5f5; border-bottom: 2px solid #e0e0e0;">
                    <tr>
                        <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">التاريخ والوقت</th>
                        <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">نوع العملية</th>
                        <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">الوصف</th>
                        <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">مصدر النقل (الوردية)</th>
                        <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">المسؤول من/إلى</th>
                        <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">عدد العمال</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transferLogs as $log)
                    <tr style="border-bottom: 1px solid #f0f0f0; transition: background 0.2s;">
                        <td style="padding: 10px; color: #666; font-size: 12px; white-space: nowrap;">
                            {{ $log->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td style="padding: 10px;">
                            <span style="display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
                                background: {{ $log->operation_type === 'transfer' ? '#fff3cd' : '#d1ecf1' }};
                                color: {{ $log->operation_type === 'transfer' ? '#856404' : '#0c5460' }};">
                                {{ $log->getOperationTypeLabel() }}
                            </span>
                        </td>
                        <td style="padding: 10px; color: #555; max-width: 200px; word-break: break-word; font-size: 12px;">
                            {{ $log->description ?? '-' }}
                        </td>
                        <td style="padding: 10px; color: #0066B2; font-weight: 600; font-size: 12px;">
                            @if($log->shift)
                            <a href="{{ route('manufacturing.shifts-workers.show', $log->shift->id) }}" style="color: #0066B2; text-decoration: none;">
                                {{ $log->shift->shift_code }}
                            </a>
                            @else
                            -
                            @endif
                        </td>
                        <td style="padding: 10px; font-size: 12px;">
                            @if($log->old_data && $log->new_data)
                            <span style="color: #e74c3c; font-weight: 600;">{{ $log->old_data['supervisor_name'] ?? '-' }}</span>
                            <span style="color: #999; margin: 0 5px;">→</span>
                            <span style="color: #27ae60; font-weight: 600;">{{ $log->new_data['supervisor_name'] ?? '-' }}</span>
                            @else
                            -
                            @endif
                        </td>
                        <td style="padding: 10px; font-weight: 600; color: #0066B2;">
                            {{ $log->new_data['workers_count'] ?? '-' }} عامل
                        </td>
                    </tr>
                    @if($log->old_data && $log->new_data && $log->operation_type === 'transfer')
                    <tr style="background: #f9f9f9; border-bottom: 1px solid #f0f0f0;">
                        <td colspan="6" style="padding: 10px; color: #666; font-size: 12px;">
                            <strong style="color: #e74c3c;">البيانات السابقة:</strong>
                            عدد العمال: <span style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">{{ $log->old_data['workers_count'] ?? '-' }}</span>

                            <span style="margin: 0 15px; color: #ccc;">|</span>

                            <strong style="color: #27ae60;">البيانات الجديدة:</strong>
                            عدد العمال: <span style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">{{ $log->new_data['workers_count'] ?? '-' }}</span>

                            @if($log->notes)
                            <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #e0e0e0;">
                                <strong>ملاحظات:</strong> {{ $log->notes }}
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 30px; color: #999;">
            <i class="feather icon-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
            <p style="margin: 0;">لا يوجد نقل أو تعديلات حالياً</p>
        </div>
        @endif
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

<!-- Operation Logs Section -->
<div class="detail-card" style="border-right-color: #10b981;">
    <div class="detail-header">
        <div class="detail-title">
            <i class="feather icon-activity"></i>
            سجل العمليات
        </div>
    </div>

    @php
    $logs = \App\Models\ShiftOperationLog::where('stage_number', 1)
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get();
    @endphp

    @if($logs->count() > 0)
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead style="background: #f5f5f5; border-bottom: 2px solid #e0e0e0;">
                <tr>
                    <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">التاريخ والوقت</th>
                    <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">العملية</th>
                    <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">الوصف</th>
                    <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">المستخدم</th>
                    <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr style="border-bottom: 1px solid #f0f0f0; transition: background 0.2s;">
                    <td style="padding: 12px; color: #666;">
                        {{ $log->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td style="padding: 12px; color: #666;">
                        {{ $log->getOperationTypeLabel() }}
                    </td>
                    <td style="padding: 12px; color: #555; max-width: 300px; word-break: break-word;">
                        {{ $log->description ?? '-' }}
                    </td>
                    <td style="padding: 12px; color: #666;">
                        {{ $log->user?->name ?? 'نظام' }}
                    </td>
                    <td style="padding: 12px;">
                        <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                            background: {{ $log->status === 'completed' ? '#d4edda' : ($log->status === 'failed' ? '#f8d7da' : '#fff3cd') }};
                            color: {{ $log->status === 'completed' ? '#155724' : ($log->status === 'failed' ? '#721c24' : '#856404') }};">
                            {{ $log->getStatusLabel() }}
                        </span>
                    </td>
                </tr>
                @if($log->old_data || $log->new_data)
                <tr style="background: #f9f9f9; border-bottom: 1px solid #f0f0f0;">
                    <td colspan="5" style="padding: 12px; color: #666; font-size: 13px;">
                        @if($log->old_data)
                        <strong>البيانات السابقة:</strong>
                        <pre style="background: white; padding: 8px; border-radius: 4px; overflow-x: auto; font-size: 12px;">{{ json_encode($log->old_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                        @endif
                        @if($log->new_data)
                        <strong>البيانات الجديدة:</strong>
                        <pre style="background: white; padding: 8px; border-radius: 4px; overflow-x: auto; font-size: 12px;">{{ json_encode($log->new_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                        @endif
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align: center; padding: 40px; color: #999;">
        <i class="feather icon-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
        <p style="margin: 0;">لا توجد عمليات مسجلة حالياً</p>
    </div>
    @endif
</div>

<!-- JsBarcode Library -->
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
