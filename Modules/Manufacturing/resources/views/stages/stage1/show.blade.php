@extends('master')

@section('title', __('stages.stage1_details'))

@section('content')

    <style>
        /* ألوان العلامة التجارية */
        :root {
            --primary-blue: #0052CC;
            --primary-blue-dark: #003d99;
            --success-green: #27ae60;
            --success-green-dark: #1e8449;
            --warning-yellow: #f39c12;
        }

        .detail-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-right: 4px solid var(--primary-blue);
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
            color: var(--primary-blue);
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
            border-right: 3px solid var(--primary-blue);
        }

        .info-label {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .info-value {
            font-size: 18px;
            color: var(--primary-blue);
            font-weight: 700;
        }

        .barcode-display {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
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

        .status-created {
            background: #e3f2fd;
            color: #1976d2;
        }

        .status-in_process {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-completed {
            background: #e8f5e9;
            color: #388e3c;
        }

        .status-consumed {
            background: #f5f5f5;
            color: #616161;
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
            <button
                onclick="printBarcode('{{ $stand->barcode }}', '{{ $stand->stand_number }}', '{{ $stand->material_name }}', {{ $stand->remaining_weight }})"
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
                @if ($stand->status == 'created')
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
                    <div class="info-value" style="font-size: 14px; font-family: monospace;">{{ $stand->parent_barcode }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">{{ __('stages.total_weight_label') }}</div>
                    <div class="info-value" style="color: #3498db;">{{ number_format($stand->weight, 2) }}
                        {{ __('stages.weight_unit') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">{{ __('stages.net_weight_label') }}</div>
                    <div class="info-value" style="color: #27ae60;">{{ number_format($stand->remaining_weight, 2) }}
                        {{ __('stages.weight_unit') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">{{ __('stages.waste_label') }}</div>
                    <div class="info-value" style="color: #e74c3c;">{{ number_format($stand->waste, 2) }}
                        {{ __('stages.weight_unit') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">{{ __('stages.wire_size_label') }}</div>
                    <div class="info-value">{{ $stand->wire_size }} مم</div>
                </div>
                <div class="info-item">
                    <div class="info-label">{{ __('stages.created_by_label') }}</div>
                    <div class="info-value" style="font-size: 16px;">
                        {{ $stand->created_by_name ?? __('stages.not_specified') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">{{ __('stages.created_at_label') }}</div>
                    <div class="info-value" style="font-size: 16px;">
                        {{ \Carbon\Carbon::parse($stand->created_at)->format('Y-m-d H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Shifts and Workers Tracking Section -->
        @php
            // جلب الوردية الحالية للستاند (بناءً على مرتبط الستاند الحالي)
            $currentShiftAssignment = \App\Models\ShiftAssignment::where('stage_number', 1)
                ->where('stage_record_id', $stand->id)
                ->where('stage_record_barcode', $stand->barcode)
                ->latest('created_at')
                ->first();

            // إذا لم نجد ارتباط مباشر، جلب آخر وردية نشطة
            if (!$currentShiftAssignment) {
                $currentShiftAssignment = \App\Models\ShiftAssignment::where('status', 'active')
                    ->where('stage_number', 1)
                    ->latest('created_at')
                    ->first();
            }

            // جلب الوردية السابقة (ثاني أحدث وردية)
            $previousShift = \App\Models\ShiftAssignment::where('stage_number', 1)
                ->orderBy('created_at', 'desc')
                ->skip(1)
                ->first();

            // جلب العمال الحاليين
            $currentShiftWorkers = $currentShiftAssignment ? \App\Models\Worker::whereIn('id', $currentShiftAssignment->worker_ids ?? [])->get() : collect();

            // جلب العمال السابقين
            $previousShiftWorkers = $previousShift ? \App\Models\Worker::whereIn('id', $previousShift->worker_ids ?? [])->get() : collect();

            // جلب تتبع العمال للمرحلة الحالية
            $workerTracking = \App\Models\WorkerStageHistory::where('stage_type', 'stage1_stands')
                ->where('stage_record_id', $stand->id)
                ->where('is_active', true)
                ->whereNull('ended_at')
                ->get();
        @endphp

        <div class="detail-card" style="border-right-color: var(--primary-blue); background: linear-gradient(135deg, rgba(0, 82, 204, 0.03) 0%, rgba(0, 61, 153, 0.03) 100%);">
            <div class="detail-header">
                <div class="detail-title">
                    <i class="feather icon-activity"></i>
                    تتبع الورديات والعمال
                </div>
            </div>

            <!-- Shifts and Workers Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">

                <!-- Current Shift Card -->
                <div data-current-shift="true" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 82, 204, 0.2);">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                        <i class="feather icon-briefcase" style="font-size: 24px;"></i>
                        <h5 style="margin: 0; font-size: 18px; font-weight: 700;">الوردية الحالية</h5>
                    </div>

                    @if($currentShiftAssignment)
                        <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                            <div style="display: grid; gap: 10px;">
                                <div>
                                    <div style="font-size: 12px; opacity: 0.9;">رقم الوردية</div>
                                    <div style="font-size: 18px; font-weight: 700;" data-current-shift-code>{{ $currentShiftAssignment->shift_code }}</div>
                                </div>
                                <div>
                                    <div style="font-size: 12px; opacity: 0.9;">المسؤول</div>
                                    <div style="font-size: 16px; font-weight: 600;" data-current-shift-supervisor>
                                        <i class="feather icon-user" style="margin-left: 5px;"></i>
                                        {{ $currentShiftAssignment->supervisor?->name ?? 'لم يحدد' }}
                                    </div>
                                </div>
                                <div>
                                    <div style="font-size: 12px; opacity: 0.9;">الفترة</div>
                                    <div style="font-size: 16px; font-weight: 600;" data-current-shift-type>
                                        {{ $currentShiftAssignment->shift_type == 'morning' ? '🌅 الفترة الأولى' : '🌙 الفترة الثانية' }}
                                    </div>
                                </div>
                                <div>
                                    <div style="font-size: 12px; opacity: 0.9;">عدد العمال</div>
                                    <div style="font-size: 18px; font-weight: 700; color: var(--success-green);" data-current-shift-workers-count>
                                        {{ count($currentShiftAssignment->worker_ids ?? []) }} عامل
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Workers List -->
                        @if($currentShiftWorkers->count() > 0)
                            <div style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 8px; max-height: 200px; overflow-y: auto;" data-current-workers-list>
                                <div style="font-size: 12px; font-weight: 700; margin-bottom: 10px; opacity: 0.9;">👷 العمال:</div>
                                @foreach($currentShiftWorkers as $worker)
                                    <div style="background: rgba(255,255,255,0.05); padding: 8px; border-radius: 6px; margin-bottom: 6px; font-size: 13px;">
                                        <i class="feather icon-user-check" style="margin-left: 5px;"></i>
                                        <strong>{{ $worker->name }}</strong> - {{ $worker->worker_code }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; text-align: center; opacity: 0.8;">
                            <i class="feather icon-alert-circle" style="font-size: 24px; margin-bottom: 10px;"></i>
                            <div>لا توجد وردية نشطة حالياً</div>
                        </div>
                    @endif
                </div>

                <!-- Previous Shift Card -->
                <div style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(243, 156, 18, 0.2);">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                        <i class="feather icon-clock" style="font-size: 24px;"></i>
                        <h5 style="margin: 0; font-size: 18px; font-weight: 700;">الوردية السابقة</h5>
                    </div>

                    @if($previousShift)
                        <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                            <div style="display: grid; gap: 10px;">
                                <div>
                                    <div style="font-size: 12px; opacity: 0.9;">رقم الوردية</div>
                                    <div style="font-size: 18px; font-weight: 700;">{{ $previousShift->shift_code }}</div>
                                </div>
                                <div>
                                    <div style="font-size: 12px; opacity: 0.9;">المسؤول</div>
                                    <div style="font-size: 16px; font-weight: 600;">
                                        <i class="feather icon-user" style="margin-left: 5px;"></i>
                                        {{ $previousShift->supervisor?->name ?? 'لم يحدد' }}
                                    </div>
                                </div>
                                <div>
                                    <div style="font-size: 12px; opacity: 0.9;">الفترة</div>
                                    <div style="font-size: 16px; font-weight: 600;">
                                        {{ $previousShift->shift_type == 'morning' ? '🌅 الفترة الأولى' : '🌙 الفترة الثانية' }}
                                    </div>
                                </div>
                                <div>
                                    <div style="font-size: 12px; opacity: 0.9;">عدد العمال</div>
                                    <div style="font-size: 18px; font-weight: 700;">
                                        {{ count($previousShift->worker_ids ?? []) }} عامل
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Previous Workers List -->
                        <div style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 8px; max-height: 200px; overflow-y: auto;">
                            <div style="font-size: 12px; font-weight: 700; margin-bottom: 10px; opacity: 0.9;">👥 العمال:</div>
                            @forelse($previousShiftWorkers as $worker)
                                <div style="background: rgba(255,255,255,0.05); padding: 8px; border-radius: 6px; margin-bottom: 6px; font-size: 13px;">
                                    <i class="feather icon-check-circle" style="margin-left: 5px;"></i>
                                    <strong>{{ $worker->name }}</strong> - {{ $worker->worker_code }}
                                </div>
                            @empty
                                <div style="font-size: 12px; opacity: 0.7;">لا يوجد عمال</div>
                            @endforelse
                        </div>
                    @else
                        <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; text-align: center; opacity: 0.8;">
                            <i class="feather icon-alert-circle" style="font-size: 24px; margin-bottom: 10px;"></i>
                            <div>لا توجد وردية سابقة</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Active Workers Tracking Section -->
            @if($workerTracking->count() > 0)
                <div style="background: var(--success-green); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.2);">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                        <i class="feather icon-zap" style="font-size: 24px;"></i>
                        <h5 style="margin: 0; font-size: 18px; font-weight: 700;">👨‍🔧 العمال النشطون الآن</h5>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px;">
                        @foreach($workerTracking as $tracking)
                            <div style="background: rgba(255,255,255,0.15); padding: 12px; border-radius: 8px; border-left: 3px solid white;">
                                <div style="font-weight: 700; margin-bottom: 8px;">{{ $tracking->worker_name }}</div>
                                <div style="font-size: 12px; opacity: 0.9; margin-bottom: 6px;">
                                    <i class="feather icon-clock" style="margin-left: 5px;"></i>
                                    بدء العمل: {{ $tracking->started_at->format('H:i') }}
                                </div>
                                <div style="font-size: 12px; opacity: 0.9;">
                                    <i class="feather icon-timer" style="margin-left: 5px;"></i>
                                    المدة: {{ $tracking->started_at->diffInMinutes(now()) }} دقيقة
                                </div>
                                @if($tracking->notes)
                                    <div style="font-size: 11px; opacity: 0.85; margin-top: 8px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.2);">
                                        {{ $tracking->notes }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; padding: 20px; border-radius: 12px; text-align: center;">
                    <i class="feather icon-users" style="font-size: 24px; margin-bottom: 10px;"></i>
                    <div>لا يوجد عمال نشطون على هذه المرحلة الآن</div>
                </div>
            @endif
        </div>

        <!-- Current Worker Section (Keep for compatibility) -->
        @php
            $currentWorker = \App\Models\WorkerStageHistory::getCurrentWorkerForStage(
                \App\Models\WorkerStageHistory::STAGE_1_STANDS,
                $stand->id,
            );
        @endphp

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

            @php
                // 🔥 جلب بيانات الوردية الحالية مع العمال
                $currentShiftForTracking = \App\Models\ShiftAssignment::where('status', 'active')
                    ->where('stage_number', 1)
                    ->latest('created_at')
                    ->first();

                $trackingWorkers = collect();
                if ($currentShiftForTracking && !empty($currentShiftForTracking->worker_ids)) {
                    $trackingWorkers = \App\Models\Worker::whereIn('id', $currentShiftForTracking->worker_ids)->get();
                }
            @endphp

            @if ($currentShiftForTracking)
                <div
                    style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div>
                            <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">رقم الوردية</div>
                            <div style="font-size: 18px; font-weight: 700;">{{ $currentShiftForTracking->shift_code }}
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">المسؤول</div>
                            <div style="font-size: 18px; font-weight: 700;">
                                {{ $currentShiftForTracking->supervisor?->name ?? 'لم يحدد' }}
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">عدد العمال</div>
                            <div style="font-size: 18px; font-weight: 700;">
                                {{ count($currentShiftForTracking->worker_ids ?? []) }}</div>
                        </div>
                        <div>
                            <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">فترة العمل</div>
                            <div style="font-size: 18px; font-weight: 700;">
                                {{ $currentShiftForTracking->shift_type == 'morning' ? 'الفترة الأولى' : 'الفترة الثانية' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tracking Workers List -->
                @if ($trackingWorkers->count() > 0)
                    <div style="margin-bottom: 20px;">
                        <h5 style="font-size: 16px; font-weight: 700; margin-bottom: 15px; color: #2c3e50;">👷 العمال قيد
                            التتبع الحالي:</h5>
                        <div
                            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 12px;">
                            @foreach ($trackingWorkers as $worker)
                                <div
                                    style="background: #f8f9fa; border: 2px solid #e3f2fd; border-radius: 10px; padding: 15px; transition: all 0.3s;">
                                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                                        <div
                                            style="width: 45px; height: 45px; background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; flex-shrink: 0;">
                                            {{ substr($worker->name, 0, 1) }}
                                        </div>
                                        <div style="flex: 1;">
                                            <div style="font-weight: 700; color: #2c3e50; font-size: 15px;">
                                                {{ $worker->name }}</div>
                                            <div style="font-size: 13px; color: #7f8c8d; margin-top: 4px;">
                                                <strong>الكود:</strong> {{ $worker->worker_code }}
                                            </div>
                                            <div style="font-size: 13px; color: #7f8c8d; margin-top: 4px;">
                                                <strong>الوظيفة:</strong> {{ $worker->position ?? 'غير محددة' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div
                        style="background: #e8f5e9; border-left: 4px solid #4caf50; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <i class="feather icon-info"></i>
                        <strong style="color: #2e7d32;">معلومة:</strong>
                        <span style="color: #558b2f;"> لم يتم تحديد عمال لهذه الوردية حالياً</span>
                    </div>
                @endif
            @else
                <div class="alert alert-info" style="margin-bottom: 20px;">
                    <i class="feather icon-info"></i>
                    لا توجد وردية نشطة للمرحلة 1 حالياً
                </div>
            @endif

            <!-- Worker Tracking Stats -->
            @if ($currentWorker)
                <div
                    style="background: linear-gradient(135deg, rgba(155, 89, 182, 0.05) 0%, rgba(142, 68, 173, 0.05) 100%); border-right: 4px solid #9b59b6; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 14px; color: #7f8c8d; margin-bottom: 8px; font-weight: 600;">العامل قيد
                                العمل الآن</div>
                            <div style="font-size: 24px; font-weight: 700; color: #9b59b6;">
                                {{ $currentWorker->worker_name }}</div>
                            <div style="margin-top: 8px; color: #666; font-size: 13px;">
                                <i class="feather icon-clock"></i>
                                بدأ في: {{ $currentWorker->started_at->format('Y-m-d H:i') }}
                                <span style="margin: 0 10px; color: #ccc;">•</span>
                                {{ $currentWorker->started_at->diffForHumans() }}
                            </div>
                        </div>
                        <div
                            style="text-align: center; background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white; padding: 20px 30px; border-radius: 12px;">
                            <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px;">مدة العمل</div>
                            <div style="font-size: 32px; font-weight: 700;">{{ $currentWorker->formatted_duration }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Worker Tracking Statistics -->
            @php
                // حساب الإحصائيات
                $totalSessions = \App\Models\WorkerStageHistory::where('stage_type', 'stage1_stands')
                    ->where('stage_record_id', $stand->id)
                    ->count();

                $totalWorkers = \App\Models\WorkerStageHistory::where('stage_type', 'stage1_stands')
                    ->where('stage_record_id', $stand->id)
                    ->distinct('worker_id')
                    ->count('worker_id');

                $totalMinutes = \App\Models\WorkerStageHistory::where('stage_type', 'stage1_stands')
                    ->where('stage_record_id', $stand->id)
                    ->whereNotNull('ended_at')
                    ->get()
                    ->sum(function($record) {
                        return $record->started_at->diffInMinutes($record->ended_at);
                    });

                $totalHours = $totalMinutes / 60;
                $averageMinutes = $totalSessions > 0 ? $totalMinutes / $totalSessions : 0;
            @endphp

            <div class="info-grid">
                <div class="info-item" style="border-right-color: #9b59b6;">
                    <div class="info-label">{{ __('worker-tracking.total_sessions') }}</div>
                    <div class="info-value" style="color: #9b59b6;">{{ $totalSessions }}</div>
                </div>
                <div class="info-item" style="border-right-color: #9b59b6;">
                    <div class="info-label">{{ __('worker-tracking.total_workers') }}</div>
                    <div class="info-value" style="color: #9b59b6;">{{ $totalWorkers }}</div>
                </div>
                <div class="info-item" style="border-right-color: #9b59b6;">
                    <div class="info-label">{{ __('worker-tracking.total_hours') }}</div>
                    <div class="info-value" style="color: #9b59b6;">{{ number_format($totalHours, 1) }}</div>
                </div>
                <div class="info-item" style="border-right-color: #9b59b6;">
                    <div class="info-label">{{ __('worker-tracking.average_session_time') }}</div>
                    <div class="info-value" style="color: #9b59b6;">
                        {{ number_format($averageMinutes, 0) }} {{ __('worker-tracking.minutes') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Workers in Stage Section -->
        <div class="detail-card" style="border-right-color: #27ae60;">
            <div class="detail-header">
                <div class="detail-title">
                    <i class="feather icon-users"></i>
                    العمال في المرحلة 1 (من الوردية)
                </div>
            </div>

            @php
                // 🔥 جلب العمال من الوردية الحالية (ShiftAssignment.worker_ids)
                $shiftForWorkers = \App\Models\ShiftAssignment::where('status', 'active')
                    ->where('stage_number', 1)
                    ->latest('created_at')
                    ->first();

                $workersInStage = collect();
                if ($shiftForWorkers && !empty($shiftForWorkers->worker_ids)) {
                    // جلب بيانات العمال من Worker model
                    $workersInStage = \App\Models\Worker::whereIn('id', $shiftForWorkers->worker_ids)
                        ->select('id', 'name', 'worker_code', 'position')
                        ->get()
                        ->map(function ($worker) use ($shiftForWorkers) {
                            return (object) [
                                'id' => $worker->id,
                                'worker_name' => $worker->name,
                                'worker_code' => $worker->worker_code,
                                'position' => $worker->position,
                                'shift_code' => $shiftForWorkers->shift_code,
                                'shift_id' => $shiftForWorkers->id,
                                'shift_date' => $shiftForWorkers->shift_date,
                                'started_at' => now(),
                            ];
                        });
                }
            @endphp

            @if ($workersInStage->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead style="background: #f5f5f5; border-bottom: 2px solid #e0e0e0;">
                            <tr>
                                <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">اسم العامل
                                </th>
                                <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">الوظيفة</th>
                                <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">الوردية</th>
                                <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">تاريخ الوردية
                                </th>
                                <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">كود العامل
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($workersInStage as $worker)
                                <tr
                                    style="border-bottom: 1px solid #f0f0f0; transition: background 0.2s; hover { background: #f9f9f9; }">
                                    <td style="padding: 12px; color: #333; font-weight: 600;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div
                                                style="width: 35px; height: 35px; background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;">
                                                {{ substr($worker->worker_name, 0, 1) }}
                                            </div>
                                            {{ $worker->worker_name }}
                                        </div>
                                    </td>
                                    <td style="padding: 12px; color: #666;">
                                        {{ $worker->position ?? 'غير محددة' }}
                                    </td>
                                    <td style="padding: 12px; color: #666;">
                                        <span
                                            style="display: inline-block; background: #e3f2fd; padding: 4px 12px; border-radius: 20px; font-size: 12px; color: #0066B2; font-weight: 600;">
                                            {{ $worker->shift_code }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px; color: #666; font-size: 13px;">
                                        {{ $worker->shift_date ? \Carbon\Carbon::parse($worker->shift_date)->format('Y-m-d') : 'اليوم' }}
                                    </td>
                                    <td style="padding: 12px; color: #0066B2; font-weight: 600; font-family: monospace;">
                                        {{ $worker->worker_code }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 30px; color: #999;">
                    <i class="feather icon-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                    <p style="margin: 0;">لا يوجد عمال في الوردية النشطة حالياً</p>
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

            @if ($transferLogs->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead style="background: #f5f5f5; border-bottom: 2px solid #e0e0e0;">
                            <tr>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">التاريخ والوقت
                                </th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">نوع العملية
                                </th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">الوصف</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">مصدر النقل
                                    (الوردية)</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">المسؤول من/إلى
                                </th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">عدد العمال
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transferLogs as $log)
                                <tr style="border-bottom: 1px solid #f0f0f0; transition: background 0.2s;">
                                    <td style="padding: 10px; color: #666; font-size: 12px; white-space: nowrap;">
                                        {{ $log->created_at->format('Y-m-d H:i') }}
                                    </td>
                                    <td style="padding: 10px;">
                                        <span
                                            style="display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
                                background: {{ $log->operation_type === 'transfer' ? '#fff3cd' : '#d1ecf1' }};
                                color: {{ $log->operation_type === 'transfer' ? '#856404' : '#0c5460' }};">
                                            {{ $log->getOperationTypeLabel() }}
                                        </span>
                                    </td>
                                    <td
                                        style="padding: 10px; color: #555; max-width: 200px; word-break: break-word; font-size: 12px;">
                                        {{ $log->description ?? '-' }}
                                    </td>
                                    <td style="padding: 10px; color: #0066B2; font-weight: 600; font-size: 12px;">
                                        @if ($log->shift)
                                            <a href="{{ route('manufacturing.shifts-workers.show', $log->shift->id) }}"
                                                style="color: #0066B2; text-decoration: none;">
                                                {{ $log->shift->shift_code }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td style="padding: 10px; font-size: 12px;">
                                        @if ($log->old_data && $log->new_data)
                                            <span
                                                style="color: #e74c3c; font-weight: 600;">{{ $log->old_data['supervisor_name'] ?? '-' }}</span>
                                            <span style="color: #999; margin: 0 5px;">→</span>
                                            <span
                                                style="color: #27ae60; font-weight: 600;">{{ $log->new_data['supervisor_name'] ?? '-' }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td style="padding: 10px; font-weight: 600; color: #0066B2;">
                                        {{ $log->new_data['workers_count'] ?? '-' }} عامل
                                    </td>
                                </tr>
                                @if ($log->old_data && $log->new_data && $log->operation_type === 'transfer')
                                    <tr style="background: #f9f9f9; border-bottom: 1px solid #f0f0f0;">
                                        <td colspan="6" style="padding: 10px; color: #666; font-size: 12px;">
                                            <strong style="color: #e74c3c;">البيانات السابقة:</strong>
                                            عدد العمال: <span
                                                style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">{{ $log->old_data['workers_count'] ?? '-' }}</span>

                                            <span style="margin: 0 15px; color: #ccc;">|</span>

                                            <strong style="color: #27ae60;">البيانات الجديدة:</strong>
                                            عدد العمال: <span
                                                style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">{{ $log->new_data['workers_count'] ?? '-' }}</span>

                                            @if ($log->notes)
                                                <div
                                                    style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #e0e0e0;">
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
        @if ($usageHistory)
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
                        <div class="info-value">{{ number_format($usageHistory->total_weight, 2) }}
                            {{ __('stages.weight_unit') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">{{ __('stages.net_weight_label') }}</div>
                        <div class="info-value">{{ number_format($usageHistory->net_weight, 2) }}
                            {{ __('stages.weight_unit') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">{{ __('stages.stand_weight_label') }}</div>
                        <div class="info-value">{{ number_format($usageHistory->stand_weight, 2) }}
                            {{ __('stages.weight_unit') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">{{ __('stages.waste_percentage_label') }}</div>
                        <div class="info-value">{{ number_format($usageHistory->waste_percentage, 2) }}%</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">{{ __('stages.start_time_label') }}</div>
                        <div class="info-value" style="font-size: 14px;">
                            {{ \Carbon\Carbon::parse($usageHistory->started_at)->format('Y-m-d H:i') }}</div>
                    </div>
                </div>

                @if ($usageHistory->notes)
                    <div
                        style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 8px; border-right: 3px solid #ffc107;">
                        <strong style="color: #856404;">📝 {{ __('stages.notes_label') }}:</strong>
                        <p style="margin: 8px 0 0 0; color: #856404;">{{ $usageHistory->notes }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- {{ __('stages.tracking_logs') }} -->
        @if ($trackingLogs->count() > 0)
            <div class="detail-card" style="border-right-color: #3498db;">
                <div class="detail-header">
                    <div class="detail-title">
                        <i class="feather icon-map"></i>
                        {{ __('stages.tracking_logs') }} ({{ $trackingLogs->count() }})
                    </div>
                </div>

                @foreach ($trackingLogs as $log)
                    <div class="log-item">
                        <div class="log-header">
                            <div class="log-action">
                                <i class="feather icon-check-circle" style="color: #27ae60;"></i>
                                {{ $log->action }} - {{ $log->stage }}
                            </div>
                            <div class="log-time">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}
                            </div>
                        </div>
                        <div class="log-details">
                            <strong>{{ __('stages.worker_label') }}:</strong>
                            {{ $log->worker_name ?? __('stages.not_specified') }}<br>
                            <strong>{{ __('stages.input_weight_label') }}:</strong>
                            {{ number_format($log->input_weight, 2) }} {{ __('stages.weight_unit') }} |
                            <strong>{{ __('stages.output_weight_label') }}:</strong>
                            {{ number_format($log->output_weight, 2) }} {{ __('stages.weight_unit') }}<br>
                            <strong>{{ __('stages.waste_label') }}:</strong> {{ number_format($log->waste_amount, 2) }}
                            {{ __('stages.weight_unit') }} ({{ number_format($log->waste_percentage, 2) }}%)
                            @if ($log->notes)
                                <br><strong>{{ __('stages.notes_label') }}:</strong> {{ $log->notes }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- {{ __('stages.operation_logs') }} -->
        @if ($operationLogs->count() > 0)
            <div class="detail-card" style="border-right-color: #f39c12;">
                <div class="detail-header">
                    <div class="detail-title">
                        <i class="feather icon-list"></i>
                        {{ __('stages.operation_logs') }} ({{ $operationLogs->count() }})
                    </div>
                </div>

                @foreach ($operationLogs as $log)
                    <div class="log-item" style="border-right-color: #f39c12;">
                        <div class="log-header">
                            <div class="log-action">
                                <i class="feather icon-activity" style="color: #f39c12;"></i>
                                {{ $log->action ?? __('stages.operation_label') }}
                            </div>
                            <div class="log-time">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}
                            </div>
                        </div>
                        <div class="log-details">
                            <strong>{{ __('stages.user_label') }}:</strong>
                            {{ $log->user_name ?? __('stages.system_label') }}<br>
                            @if ($log->description)
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
            <a href="{{ route('manufacturing.stage1.index') }}" class="um-btn um-btn-primary" style="flex: 1; background: linear-gradient(135deg, #0052CC 0%, #003d99 100%); border: none;">
                <i class="feather icon-arrow-right"></i> {{ __('stages.back_to_list') }}
            </a>
            <button
                onclick="printBarcode('{{ $stand->barcode }}', '{{ $stand->stand_number }}', '{{ $stand->material_name }}', {{ $stand->remaining_weight }})"
                class="um-btn um-btn-success" style="flex: 1; background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%); border: none;">
                <i class="feather icon-printer"></i> {{ __('stages.print_barcode_button') }}
            </button>
            @if($currentShiftAssignment)
            <button onclick="openTransferStageModal({{ $stand->id }}, {{ $currentShiftAssignment->id }})" class="um-btn um-btn-warning" style="flex: 1; background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); border: none; color: white;">
                <i class="feather icon-arrow-right-circle"></i> نقل المرحلة لوردية أخرى
            </button>
            @endif
        </div>
    </div>



    <!-- JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <!-- Transfer Stage Modal -->
    <div class="modal fade" id="transferStageModal" tabindex="-1">
        <div class="modal-dialog modal-lg" style="max-width: 1200px;">
            <div class="modal-content" style="border-top: 4px solid var(--primary-blue);">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%); color: white; border: none;">
                    <h5 class="modal-title" style="font-weight: 700; font-size: 20px;">
                        <i class="feather icon-arrow-right-circle"></i> نقل المرحلة إلى وردية أخرى
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 30px; background: #f8f9fa;">
                    <div class="alert" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%); color: white; border: none; border-radius: 10px;">
                        <i class="feather icon-info"></i>
                        <strong>معلومة:</strong> قارن بين الوردية الحالية والوردية الجديدة واختر الوردية المناسبة للنقل
                    </div>

                    <!-- صف الورديات -->
                    <div class="row mt-4">
                        <!-- الوردية السابقة -->
                        <div class="col-lg-4 mb-4">
                            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-right: 4px solid #95a5a6;">
                                <h6 style="font-weight: 700; color: #7f8c8d; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #ecf0f1;">
                                    <i class="feather icon-clock"></i> الوردية السابقة
                                </h6>
                                <div id="previousShiftCard">
                                    <div class="text-center p-3" style="color: #95a5a6;">
                                        <i class="feather icon-loader" style="animation: spin 1s linear infinite;"></i>
                                        جاري التحميل...
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الوردية الحالية -->
                        <div class="col-lg-4 mb-4">
                            <div style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%); border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0, 82, 204, 0.2); border-right: 4px solid var(--primary-blue); color: white;">
                                <h6 style="font-weight: 700; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.2); opacity: 0.9;">
                                    <i class="feather icon-briefcase"></i> الوردية الحالية
                                </h6>
                                <div id="currentShiftCard" style="text-align: right;">
                                    <div class="text-center p-3" style="color: rgba(255,255,255,0.8);">
                                        <i class="feather icon-loader" style="animation: spin 1s linear infinite;"></i>
                                        جاري التحميل...
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الوردية الجديدة -->
                        <div class="col-lg-4 mb-4">
                            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-right: 4px solid var(--success-green);">
                                <h6 style="font-weight: 700; color: var(--success-green); margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #ecf0f1;">
                                    <i class="feather icon-arrow-right"></i> الوردية الجديدة
                                </h6>
                                <div class="mb-3">
                                    <select id="toShiftId" class="form-select" required style="border: 2px solid #e0e0e0; border-right: 4px solid var(--success-green);">
                                        <option value="">-- اختر وردية --</option>
                                    </select>
                                </div>
                                <div id="newShiftCard" style="display: none; text-align: right;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- تفاصيل العمال -->
                    <div class="row mt-4">
                        <!-- عمال الوردية السابقة -->
                        <div class="col-lg-4 mb-4">
                            <div id="previousShiftWorkers" style="background: white; border-radius: 10px; padding: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);"></div>
                        </div>

                        <!-- عمال الوردية الحالية -->
                        <div class="col-lg-4 mb-4">
                            <div id="currentShiftWorkers" style="background: white; border-radius: 10px; padding: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);"></div>
                        </div>

                        <!-- عمال الوردية الجديدة -->
                        <div class="col-lg-4 mb-4">
                            <div id="newShiftWorkers" style="background: white; border-radius: 10px; padding: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);"></div>
                        </div>
                    </div>

                    <!-- ملاحظات -->
                    <div class="mt-4 pt-3 border-top">
                        <label class="form-label" style="font-weight: 700; color: var(--primary-blue);">
                            <i class="feather icon-file-text"></i> ملاحظات (اختياري)
                        </label>
                        <textarea class="form-control" id="transferNotes" rows="3" placeholder="أضف ملاحظات عن سبب النقل..." style="border: 1px solid #ddd; border-right: 3px solid var(--primary-blue);"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="background: linear-gradient(135deg, #f8f9fa 0%, #ecf0f1 100%); border-top: 2px solid #e0e0e0;">
                    <button type="button" class="btn btn-lg" style="background: #e0e0e0; color: #333; font-weight: 600;" data-bs-dismiss="modal">
                        <i class="feather icon-x"></i> إلغاء
                    </button>
                    <button type="button" class="btn btn-lg" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%); color: white; font-weight: 600;" onclick="submitTransferStage()">
                        <i class="feather icon-send"></i> تأكيد النقل
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

    <script>
        let transferStageData = {
            standId: null,
            fromShiftId: null
        };

        function openTransferStageModal(standId, currentShiftId) {
            transferStageData.standId = standId;
            transferStageData.fromShiftId = currentShiftId;

            // جلب الوردية السابقة
            loadPreviousShiftData();

            // جلب بيانات الوردية الحالية
            loadCurrentShiftData();

            // جلب الورديات المتاحة
            loadAvailableShifts(currentShiftId);

            // فتح الـ Modal
            new bootstrap.Modal(document.getElementById('transferStageModal')).show();
        }

        // تحميل الوردية السابقة
        function loadPreviousShiftData() {
            fetch('{{ route("worker-tracking.previous-shift-workers") }}')
                .then(response => response.json())
                .then(data => {
                    if(data.success && data.shift) {
                        displayPreviousShiftInfo(data.shift, data.workers);
                    } else {
                        displayNoPreviousShift();
                    }
                })
                .catch(error => {
                    console.error('خطأ في تحميل الوردية السابقة:', error);
                    displayNoPreviousShift();
                });
        }

        // عرض بيانات الوردية السابقة
        function displayPreviousShiftInfo(shift, workers) {
            const card = document.getElementById('previousShiftCard');
            const workersContainer = document.getElementById('previousShiftWorkers');

            const supervisorName = shift.supervisor ? shift.supervisor.name : (shift.supervisor_name || 'لم يحدد');
            const shiftType = shift.shift_type === 'morning' ? '🌅 الفترة الأولى' : '🌙 الفترة الثانية';
            const shiftDate = new Date(shift.shift_date).toLocaleDateString('ar-EG');
            const status = shift.status === 'active' ? 'نشطة' : 'مكتملة';

            card.innerHTML = `
                <div style="text-align: right;">
                    <div style="margin-bottom: 12px;">
                        <small style="color: #7f8c8d; font-weight: 600;">📋 رقم الوردية</small>
                        <div style="font-size: 20px; font-weight: 700; color: var(--primary-blue);">${shift.shift_code || 'N/A'}</div>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <small style="color: #7f8c8d; font-weight: 600;">👤 المسؤول</small>
                        <div style="font-size: 16px; font-weight: 600; color: #2c3e50;">${supervisorName}</div>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <small style="color: #7f8c8d; font-weight: 600;">⏰ الفترة</small>
                        <div style="font-size: 14px; color: #555;">${shiftType}</div>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <small style="color: #7f8c8d; font-weight: 600;">📅 التاريخ</small>
                        <div style="font-size: 14px; color: #555;">${shiftDate}</div>
                    </div>
                    <div style="padding-top: 12px; border-top: 1px solid #ecf0f1;">
                        <small style="color: #7f8c8d; font-weight: 600;">✓ الحالة</small>
                        <div style="display: inline-block; margin-top: 6px;">
                            <span class="badge bg-secondary" style="font-size: 12px;">${status}</span>
                        </div>
                    </div>
                </div>
            `;

            // عرض العمال
            displayWorkersList(workers, 'previousShiftWorkers', 'الوردية السابقة');
        }

        // عرض عند عدم وجود وردية سابقة
        function displayNoPreviousShift() {
            const card = document.getElementById('previousShiftCard');
            const workersContainer = document.getElementById('previousShiftWorkers');

            card.innerHTML = `
                <div class="alert alert-info mb-0" style="text-align: right;">
                    <i class="feather icon-alert-circle"></i>
                    لا توجد وردية سابقة
                </div>
            `;

            workersContainer.innerHTML = `
                <div class="alert alert-info mb-0" style="text-align: right;">
                    <i class="feather icon-info"></i>
                    لا توجد بيانات
                </div>
            `;
        }

        // تحميل الوردية الحالية
        function loadCurrentShiftData() {
            fetch('{{ route("worker-tracking.current-shift") }}')
                .then(response => response.json())
                .then(data => {
                    if(data.success && data.shift) {
                        displayCurrentShiftInfo(data.shift);
                    }
                })
                .catch(error => console.error('خطأ:', error));
        }

        // عرض بيانات الوردية الحالية
        function displayCurrentShiftInfo(shift) {
            const content = document.getElementById('currentShiftCard');
            const workersHtml = document.getElementById('currentShiftWorkers');

            const supervisorName = shift.supervisor ? shift.supervisor.name : (shift.supervisor_name || 'لم يحدد');
            const shiftType = shift.shift_type === 'morning' ? '🌅 الفترة الأولى' : '🌙 الفترة الثانية';
            const shiftDate = new Date(shift.shift_date).toLocaleDateString('ar-EG');
            const workersCount = shift.workers_count || (shift.workers ? shift.workers.length : 0);

            content.innerHTML = `
                <div style="text-align: right;">
                    <div style="margin-bottom: 12px;">
                        <small style="opacity: 0.9; font-weight: 600;">📋 رقم الوردية</small>
                        <div style="font-size: 20px; font-weight: 700;">${shift.shift_code || 'N/A'}</div>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <small style="opacity: 0.9; font-weight: 600;">👤 المسؤول</small>
                        <div style="font-size: 16px; font-weight: 600;">${supervisorName}</div>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <small style="opacity: 0.9; font-weight: 600;">⏰ الفترة</small>
                        <div style="font-size: 14px;">${shiftType}</div>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <small style="opacity: 0.9; font-weight: 600;">📅 التاريخ</small>
                        <div style="font-size: 14px;">${shiftDate}</div>
                    </div>
                    <div style="padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.2);">
                        <small style="opacity: 0.9; font-weight: 600;">👷 عدد العمال</small>
                        <div style="font-size: 20px; font-weight: 700;">${workersCount} عامل</div>
                    </div>
                </div>
            `;

            // عرض العمال
            displayWorkersList(shift.workers || [], 'currentShiftWorkers', 'الوردية الحالية', true);
        }

        // تحميل الورديات المتاحة
        function loadAvailableShifts(currentShiftId) {
            fetch('{{ route("worker-tracking.available-shifts") }}?current_shift_id=' + currentShiftId)
                .then(response => response.json())
                .then(data => {
                    if(data.success && data.shifts) {
                        const select = document.getElementById('toShiftId');
                        select.innerHTML = '<option value="">-- اختر وردية --</option>';

                        data.shifts.forEach(shift => {
                            const option = document.createElement('option');
                            option.value = shift.id;
                            option.textContent = `${shift.shift_code} - ${shift.supervisor_name || 'لم يحدد'} (${shift.shift_type === 'morning' ? 'الفترة الأولى' : 'الفترة الثانية'})`;
                            option.dataset.shift = JSON.stringify(shift);
                            select.appendChild(option);
                        });

                        // إضافة event listener لعرض تفاصيل الوردية المختارة
                        select.addEventListener('change', function() {
                            if(this.value) {
                                const shift = JSON.parse(this.options[this.selectedIndex].dataset.shift);
                                displaySelectedShiftInfo(shift);
                            } else {
                                document.getElementById('newShiftCard').style.display = 'none';
                                document.getElementById('newShiftWorkers').innerHTML = '';
                            }
                        });
                    }
                })
                .catch(error => console.error('خطأ في تحميل الورديات:', error));
        }

        // عرض بيانات الوردية المختارة
        function displaySelectedShiftInfo(shift) {
            const card = document.getElementById('newShiftCard');
            const workersContainer = document.getElementById('newShiftWorkers');

            const shiftType = shift.shift_type === 'morning' ? '🌅 الفترة الأولى' : '🌙 الفترة الثانية';
            const shiftDate = new Date(shift.shift_date).toLocaleDateString('ar-EG');
            const supervisorName = shift.supervisor_name || 'لم يحدد';
            const status = shift.status === 'active' ? 'نشطة' : 'مكتملة';

            card.innerHTML = `
                <div style="text-align: right;">
                    <div style="margin-bottom: 12px;">
                        <small style="color: #7f8c8d; font-weight: 600;">📋 رقم الوردية</small>
                        <div style="font-size: 20px; font-weight: 700; color: var(--success-green);">${shift.shift_code || 'N/A'}</div>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <small style="color: #7f8c8d; font-weight: 600;">👤 المسؤول</small>
                        <div style="font-size: 16px; font-weight: 600; color: #2c3e50;">${supervisorName}</div>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <small style="color: #7f8c8d; font-weight: 600;">⏰ الفترة</small>
                        <div style="font-size: 14px; color: #555;">${shiftType}</div>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <small style="color: #7f8c8d; font-weight: 600;">📅 التاريخ</small>
                        <div style="font-size: 14px; color: #555;">${shiftDate}</div>
                    </div>
                    <div style="padding-top: 12px; border-top: 1px solid #ecf0f1;">
                        <small style="color: #7f8c8d; font-weight: 600;">✓ الحالة</small>
                        <div style="display: inline-block; margin-top: 6px;">
                            <span class="badge bg-success" style="font-size: 12px;">${status}</span>
                        </div>
                    </div>
                </div>
            `;

            // عرض العمال
            displayWorkersList(shift.workers || [], 'newShiftWorkers', 'الوردية الجديدة');
            card.style.display = 'block';
        }

        // دالة موحدة لعرض قائمة العمال
        function displayWorkersList(workers, containerId, title, isDark = false) {
            const container = document.getElementById(containerId);

            if (!workers || workers.length === 0) {
                container.innerHTML = `
                    <div style="text-align: right;">
                        <h6 style="font-weight: 600; color: ${isDark ? 'rgba(255,255,255,0.8)' : '#7f8c8d'}; margin-bottom: 10px; font-size: 13px;">
                            👷 العمال
                        </h6>
                        <div class="alert alert-info mb-0" style="text-align: center; font-size: 12px;">
                            لا توجد عمال في ${title}
                        </div>
                    </div>
                `;
                return;
            }

            let html = `
                <div style="text-align: right;">
                    <h6 style="font-weight: 600; color: ${isDark ? 'rgba(255,255,255,0.8)' : '#7f8c8d'}; margin-bottom: 10px; font-size: 13px;">
                        👷 العمال (${workers.length})
                    </h6>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 8px;">
            `;

            workers.forEach(worker => {
                const workerName = worker.name || 'غير محدد';
                const workerCode = worker.worker_code || 'N/A';
                const position = worker.position || 'غير محدد';

                html += `
                    <div style="background: ${isDark ? 'rgba(255,255,255,0.1)' : '#f8f9fa'}; padding: 10px; border-radius: 6px; border-right: 3px solid var(--primary-blue); font-size: 12px; text-align: right;">
                        <div style="font-weight: 600; color: ${isDark ? 'white' : '#2c3e50'};">${workerName}</div>
                        <small style="color: ${isDark ? 'rgba(255,255,255,0.7)' : '#7f8c8d'};">الكود: ${workerCode}</small>
                        <div style="margin-top: 4px;">
                            <small style="color: ${isDark ? 'rgba(255,255,255,0.6)' : '#555'};">الوظيفة: ${position}</small>
                        </div>
                    </div>
                `;
            });

            html += `
                    </div>
                </div>
            `;

            container.innerHTML = html;
        }

        // إرسال طلب النقل
        function submitTransferStage() {
            const toShiftId = parseInt(document.getElementById('toShiftId').value) || 0;
            const notes = document.getElementById('transferNotes').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            console.log('🔥 البيانات المرسلة:', {
                stand_id: transferStageData.standId,
                from_shift_id: transferStageData.fromShiftId,
                to_shift_id: toShiftId,
                notes: notes,
                csrf_token: csrfToken ? '✅ موجود' : '❌ غير موجود'
            });

            if(!toShiftId || toShiftId === 0) {
                if(typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '⚠️ تحذير',
                        text: 'الرجاء اختيار وردية للنقل إليها',
                        icon: 'warning',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: 'var(--primary-blue)'
                    });
                } else {
                    alert('الرجاء اختيار وردية للنقل إليها');
                }
                return;
            }

            // التأكيد من المستخدم
            if(!confirm('هل أنت متأكد من نقل المرحلة إلى الوردية المختارة؟')) {
                return;
            }

            // إرسال طلب النقل
            fetch('{{ route("worker-tracking.transfer-stage-to-shift") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    stand_id: transferStageData.standId,
                    from_shift_id: transferStageData.fromShiftId,
                    to_shift_id: toShiftId,
                    notes: notes
                })
            })
            .then(response => {
                // التحقق من حالة الاستجابة
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    // إغلاق الـ Modal
                    const modalElement = document.getElementById('transferStageModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if(modal) modal.hide();

                    // تحديث البيانات على الصفحة مباشرة
                    console.log('✅ Transfer successful, updating page data...');
                    updatePageAfterTransfer(data);

                    // عرض رسالة النجاح
                    if(typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '✅ نجح!',
                            text: data.message || 'تم نقل المرحلة بنجاح!',
                            icon: 'success',
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: 'var(--primary-blue)'
                        }).then(() => {
                            // إعادة تحميل الصفحة بعد 2 ثانية
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        });
                    } else {
                        alert('✅ ' + (data.message || 'تم نقل المرحلة بنجاح!'));
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }
                } else {
                    if(typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '❌ خطأ!',
                            text: data.message || 'فشل النقل. يرجى المحاولة مرة أخرى',
                            icon: 'error',
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: 'var(--primary-blue)'
                        });
                    } else {
                        alert('❌ ' + (data.message || 'فشل النقل. يرجى المحاولة مرة أخرى'));
                    }
                }
            })
            .catch(error => {
                console.error('خطأ:', error);
                if(typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '❌ خطأ!',
                        text: 'حدث خطأ في النقل. يرجى المحاولة مرة أخرى',
                        icon: 'error',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: 'var(--primary-blue)'
                    });
                } else {
                    alert('❌ حدث خطأ في النقل. يرجى المحاولة مرة أخرى');
                }
            });
        }

        // دالة تحديث البيانات بعد النقل بنجاح
        function updatePageAfterTransfer(data) {
            console.log('📊 تحديث بيانات الصفحة بعد النقل:', data);

            if(data.data && data.data.to_shift) {
                const toShift = data.data.to_shift;
                console.log('🔄 الوردية الجديدة:', toShift);

                // تحديث رقم الوردية
                const shiftCodeElement = document.querySelector('[data-current-shift-code]');
                if(shiftCodeElement) {
                    shiftCodeElement.textContent = toShift.shift_code;
                    console.log('✅ تم تحديث رقم الوردية إلى:', toShift.shift_code);
                }

                // تحديث المسؤول
                const supervisorElement = document.querySelector('[data-current-shift-supervisor]');
                if(supervisorElement) {
                    supervisorElement.innerHTML = '<i class="feather icon-user" style="margin-left: 5px;"></i>' +
                                                  (toShift.supervisor_name || 'لم يحدد');
                    console.log('✅ تم تحديث المسؤول إلى:', toShift.supervisor_name);
                }

                // تحديث عدد العمال
                const workersCountElement = document.querySelector('[data-current-shift-workers-count]');
                if(workersCountElement) {
                    workersCountElement.textContent = (toShift.workers_count || 0) + ' عامل';
                    console.log('✅ تم تحديث عدد العمال إلى:', toShift.workers_count);
                }
            } else {
                console.log('⚠️ لا توجد بيانات الوردية الجديدة في الاستجابة');
            }

            console.log('✅ انتظر إعادة تحميل الصفحة...');
        }
    </script>
        function printBarcode(barcode, standNumber, materialName, netWeight) {
            const printWindow = window.open('', '', 'height=650,width=850');
            printWindow.document.write('<html dir="rtl"><head><title>' + '{{ __('stages.print_barcode_title') }}' +
                ' - ' + standNumber + '</title>');
            printWindow.document.write(
                '<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
            printWindow.document.write('<style>');
            printWindow.document.write(
                'body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }'
                );
            printWindow.document.write(
                '.barcode-container { background: white; padding: 50px; border-radius: 16px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); text-align: center; max-width: 550px; }'
                );
            printWindow.document.write(
                '.title { font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 4px solid #667eea; }'
                );
            printWindow.document.write(
                '.stand-number { font-size: 24px; color: #667eea; font-weight: bold; margin: 20px 0; }');
            printWindow.document.write(
                '.barcode-code { font-size: 22px; font-weight: bold; color: #2c3e50; margin: 25px 0; letter-spacing: 4px; font-family: "Courier New", monospace; }'
                );
            printWindow.document.write(
                '.info { margin-top: 30px; padding: 25px; background: #f8f9fa; border-radius: 10px; text-align: right; }'
                );
            printWindow.document.write('.info-row { margin: 12px 0; display: flex; justify-content: space-between; }');
            printWindow.document.write('.label { color: #7f8c8d; font-size: 16px; }');
            printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 18px; }');
            printWindow.document.write('@media print { body { background: white; } }');
            printWindow.document.write('</style></head><body>');
            printWindow.document.write('<div class="barcode-container">');
            printWindow.document.write('<div class="title">{{ __('stages.barcode_title') }}</div>');
            printWindow.document.write('<div class="stand-number">{{ __('stages.stand_label_print') }} ' + standNumber +
                '</div>');
            printWindow.document.write('<svg id="print-barcode"></svg>');
            printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
            printWindow.document.write('<div class="info">');
            printWindow.document.write(
                '<div class="info-row"><span class="label">{{ __('stages.material_label_print') }}:</span><span class="value">' +
                materialName + '</span></div>');
            printWindow.document.write(
                '<div class="info-row"><span class="label">{{ __('stages.net_weight_label_print') }}:</span><span class="value">' +
                netWeight + ' {{ __('stages.weight_unit') }}</span></div>');
            printWindow.document.write(
                '<div class="info-row"><span class="label">{{ __('stages.date_label_print') }}:</span><span class="value">' +
                new Date().toLocaleDateString('ar-EG') + '</span></div>');
            printWindow.document.write('</div></div>');
            printWindow.document.write('<script>');
            printWindow.document.write('JsBarcode("#print-barcode", "' + barcode +
                '", { format: "CODE128", width: 2, height: 90, displayValue: false, margin: 12 });');
            printWindow.document.write(
                'window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };'
                );
            printWindow.document.write('<\/script></body></html>');
            printWindow.document.close();
        }
    </script>

@endsection
