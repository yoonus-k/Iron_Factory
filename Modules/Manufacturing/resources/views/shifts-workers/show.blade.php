@extends('master')

@section('title', __('shifts-workers.shift_details'))

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-users"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $shift->shift_type_name }} - {{ $shift->shift_code }}</h1>
                        <div class="badges">
                            <span class="badge category">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                {{ __('shifts-workers.shifts_and_workers') }}
                            </span>
                            <span class="badge {{ $shift->status == 'active' ? 'active' : ($shift->status == 'completed' ? 'completed' : 'scheduled') }}">
                                {{ $shift->status_name }}
                            </span>
                        </div>
                        <div class="shift-quick-info">
                            <span class="supervisor-info">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                </svg>
                                <strong>المسول:</strong> {{ $shift->supervisor->name ?? __('shifts-workers.not_specified') }}
                            </span>
                            <span class="end-time-info">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <strong>وقت الانتهاء:</strong> {{ $shift->end_time }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    @if($shift->status == 'scheduled' || $shift->status == 'active')
                        <a href="{{ route('manufacturing.shifts-workers.edit', $shift->id) }}" class="btn btn-edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            {{ __('shifts-workers.edit') }}
                        </a>

                        <a href="{{ route('manufacturing.shifts-workers.transfer', $shift->id) }}" class="btn btn-transfer" style="background-color: #f59e0b; color: white; border: none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="1" y1="4" x2="23" y2="4"></line>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                                <line x1="1" y1="16" x2="23" y2="16"></line>
                                <line x1="1" y1="22" x2="23" y2="22"></line>
                                <polyline points="4 7 10 7 10 13"></polyline>
                                <polyline points="20 17 14 17 14 11"></polyline>
                            </svg>
                            نقل الوردية
                        </a>

                        <a href="{{ route('manufacturing.shifts-workers.transfer-history', $shift->id) }}" class="btn btn-transfer" style="background-color: #6366f1; color: white; border: none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 12a9 9 0 010-18 9 9 0 0110 9M12 2v10M2 12h10"></path>
                            </svg>
                            سجل النقل
                        </a>
                    @endif
                    <a href="{{ route('manufacturing.shifts-workers.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        {{ __('shifts-workers.back') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('shifts-workers.shift_information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            {{ __('shifts-workers.shift_number') }}
                        </div>
                        <div class="info-value">{{ $shift->shift_code }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            {{ __('shifts-workers.shift_date') }}
                        </div>
                        <div class="info-value">{{ $shift->shift_date->format('Y-m-d') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            {{ __('shifts-workers.work_period') }}
                        </div>
                        <div class="info-value">
                            <span class="badge badge-info">{{ $shift->shift_type_name }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            {{ __('shifts-workers.supervisor') }}
                        </div>
                        <div class="info-value">
                            @if($supervisor)
                                <strong>{{ $supervisor->name }}</strong>
                            @else
                                {{ __('shifts-workers.not_specified') }}
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            {{ __('shifts-workers.start_time') }}
                        </div>
                        <div class="info-value">{{ $shift->start_time }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            {{ __('shifts-workers.end_time') }}
                        </div>
                        <div class="info-value">{{ $shift->end_time }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('shifts-workers.additional_information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            {{ __('shifts-workers.workers_count') }}
                        </div>
                        <div class="info-value">{{ $shift->total_workers }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                            {{ __('shifts-workers.shift_status') }}
                        </div>
                        <div class="info-value">
                            <span class="status {{ $shift->status }}">{{ $shift->status_name }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            {{ __('shifts-workers.creation_date') }}
                        </div>
                        <div class="info-value">{{ $shift->created_at->format('Y-m-d H:i') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                            </svg>
                            {{ __('shifts-workers.update_date') }}
                        </div>
                        <div class="info-value">{{ $shift->updated_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">
                        @if($team)
                            {{ __('shifts-workers.team') }}: <strong>{{ $team->name }}</strong> ({{ $workers->count() }} {{ __('shifts-workers.workers') }})
                        @else
                            {{ __('shifts-workers.shift_workers_list', ['count' => $workers->count()]) }}
                        @endif
                    </h3>
                </div>
                <div class="card-body">
                    @if($workers && $workers->count() > 0)
                        @if($team)
                            <!-- عرض الفريق مع المسول -->
                            <div class="team-members-section">
                                <div style="margin-bottom: 20px;">
                                    <h3 style="font-size: 20px; font-weight: 700; color: #1f2937; margin: 0 0 8px 0;">
                                        المجموعة: <strong>{{ $team->name }}</strong>
                                    </h3>
                                    <h4 style="font-size: 16px; font-weight: 600; color: #6b7280; margin: 0;">
                                        المسول: <strong style="color: #1f2937;">{{ $supervisor->name ?? 'غير محدد' }}</strong>
                                    </h4>
                                </div>

                                    <div class="workers-list-cards" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
                                        @foreach($workers as $index => $worker)
                                            <div class="card">
                                                <div class="card-body" style="display: flex; gap: 16px; align-items: flex-start; padding: 16px;">
                                                    <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; font-size: 14px;">{{ $index + 1 }}</div>
                                                    <div style="flex: 1;">
                                                        <h5 style="margin: 0 0 6px 0; font-weight: 700; color: #1f2937; font-size: 14px;">{{ $worker->name }}</h5>
                                                        @if($worker->email)
                                                            <p style="margin: 0 0 4px 0; color: #6b7280; font-size: 13px;">{{ $worker->email }}</p>
                                                        @endif
                                                        @if($worker->worker_code)
                                                            <p style="margin: 0 0 4px 0; color: #9ca3af; font-size: 12px; text-transform: uppercase; font-weight: 600;">{{ $worker->worker_code }}</p>
                                                        @endif

                                                        <!-- تحديد المرحلة -->
                                                        @if($shift->status == 'active' || $shift->status == 'scheduled')
                                                            <div style="margin-top: 8px;">
                                                                <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 4px;">المرحلة:</label>
                                                                <select class="stage-select" data-worker-id="{{ $worker->id }}" style="width: 100%; padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 12px;">
                                                                    <option value="">-- حدد المرحلة --</option>
                                                                    <option value="1" @if($worker->assigned_stage == 1) selected @endif>المرحلة 1 - الأستندات</option>
                                                                    <option value="2" @if($worker->assigned_stage == 2) selected @endif>المرحلة 2 - المعالجة</option>
                                                                    <option value="3" @if($worker->assigned_stage == 3) selected @endif>المرحلة 3 - الملفات</option>
                                                                    <option value="4" @if($worker->assigned_stage == 4) selected @endif>المرحلة 4 - الصناديق</option>
                                                                </select>
                                                            </div>
                                                        @else
                                                            <p style="margin: 8px 0 0 0; color: #6b7280; font-size: 12px;">
                                                                <strong>المرحلة:</strong>
                                                                {{ $worker->assigned_stage ? 'المرحلة ' . $worker->assigned_stage : 'غير محددة' }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- عرض العمال الفرديين -->
                            <div class="workers-list">
                                @foreach($workers as $index => $worker)
                                    <div class="worker-item">
                                        <div class="worker-info">
                                            <div class="worker-avatar">
                                                <i class="feather icon-user"></i>
                                            </div>
                                            <div class="worker-details">
                                                <h4>{{ $worker->name }}</h4>
                                                <p>{{ $worker->email ?? 'لا يوجد بريد' }}</p>
                                                @if(isset($worker->worker_code))
                                                    <p class="worker-code">{{ $worker->worker_code }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="worker-status">
                                            <span class="status active">{{ __('shifts-workers.assigned') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <p style="text-align: center; color: #999;">{{ __('shifts-workers.no_workers_assigned') }}</p>
                    @endif
                </div>
            </div>

            @if($shift->notes)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <div class="card-icon primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                            </svg>
                        </div>
                        <h3 class="card-title">{{ __('shifts-workers.shift_notes_section') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-value">{{ $shift->notes }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-icon warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="19" cy="12" r="1"></circle>
                        <circle cx="5" cy="12" r="1"></circle>
                    </svg>
                </div>
                <h3 class="card-title">{{ __('shifts-workers.available_actions') }}</h3>
            </div>
            <div class="card-body">
                <div class="actions-grid">
                    @if($shift->status == 'scheduled')
                        <a href="{{ route('manufacturing.shifts-workers.edit', $shift->id) }}" class="action-btn activate">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </div>
                            <div class="action-text">
                                <h4>{{ __('shifts-workers.edit_shift_description') }}</h4>
                                <p>{{ __('shifts-workers.edit_shift_description') }}</p>
                            </div>
                        </a>

                        <form action="{{ route('manufacturing.shifts-workers.activate', $shift->id) }}" method="POST" style="display: inline-block; width: 100%;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="action-btn activate" style="width: 100%; text-align: right;">
                                <div class="action-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <div class="action-text">
                                    <h4>{{ __('shifts-workers.activate_shift') }}</h4>
                                    <p>{{ __('shifts-workers.activate_shift_description') }}</p>
                                </div>
                            </button>
                        </form>

                        <form action="{{ route('manufacturing.shifts-workers.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('{{ __('shifts-workers.confirm_delete') }}');" style="display: inline-block; width: 100%;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn delete" style="width: 100%; text-align: right;">
                                <div class="action-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </div>
                                <div class="action-text">
                                    <h4>{{ __('shifts-workers.delete_shift') }}</h4>
                                    <p>{{ __('shifts-workers.delete_shift_description') }}</p>
                                </div>
                            </button>
                        </form>
                    @elseif($shift->status == 'active')
                        <a href="{{ route('manufacturing.shifts-workers.edit', $shift->id) }}" class="action-btn activate">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="action-text">
                                <h4>{{ __('shifts-workers.edit_workers') }}</h4>
                                <p>{{ __('shifts-workers.edit_workers_description') }}</p>
                            </div>
                        </a>

                        <form action="{{ route('manufacturing.shifts-workers.complete', $shift->id) }}" method="POST" style="display: inline-block; width: 100%;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="action-btn complete" style="width: 100%; text-align: right;" onclick="return confirm('{{ __('shifts-workers.confirm_complete') }}');">
                                <div class="action-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                </div>
                                <div class="action-text">
                                    <h4>{{ __('shifts-workers.complete_shift') }}</h4>
                                    <p>{{ __('shifts-workers.complete_shift_description') }}</p>
                                </div>
                            </button>
                        </form>
                    @else
                        <div class="action-btn" style="opacity: 0.6; cursor: not-allowed;">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                            </div>
                            <div class="action-text">
                                <h4>{{ __('shifts-workers.shift_completed') }}</h4>
                                <p>{{ __('shifts-workers.cannot_edit_completed') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

<script>
    // تحديث المرحلة للعامل
    document.querySelectorAll('.stage-select').forEach(select => {
        select.addEventListener('change', async function() {
            const workerId = this.getAttribute('data-worker-id');
            const stageNumber = this.value;

            if (!stageNumber) {
                alert('يرجى اختيار مرحلة');
                return;
            }

            try {
                const response = await fetch('{{ route("manufacturing.shifts-workers.assign-stage-to-worker", $shift->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        worker_id: workerId,
                        stage_number: stageNumber
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // عرض رسالة نجاح
                    const notification = document.createElement('div');
                    notification.style.cssText = `
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: #10b981;
                        color: white;
                        padding: 15px 20px;
                        border-radius: 8px;
                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                        z-index: 1000;
                        animation: slideIn 0.3s ease;
                    `;
                    notification.innerHTML = `<strong>✓</strong> ${data.message}`;
                    document.body.appendChild(notification);

                    // إزالة الرسالة بعد 3 ثواني
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                } else {
                    alert('خطأ: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('حدث خطأ في تحديث المرحلة');
            }
        });
    });

    // إضافة animation CSS
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
</script>

@endsection



