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
                        <div class="info-value">{{ $shift->supervisor->name ?? __('shifts-workers.not_specified') }}</div>
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
                            {{ __('shifts-workers.team') }}: {{ $team->name }} ({{ $workers->count() }} {{ __('shifts-workers.workers') }})
                        @else
                            {{ __('shifts-workers.shift_workers_list', ['count' => $workers->count()]) }}
                        @endif
                    </h3>
                </div>
                <div class="card-body">
                    @if($workers->count() > 0)
                        @if($team)
                            <!-- عرض الفريق مع المسول -->
                            <div class="team-display-card">
                                <div class="team-header-section">
                                    <div class="team-info-box">
                                        <div class="team-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="9" cy="7" r="4"></circle>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                                        </div>
                                        <div>
                                            <p class="team-label">{{ __('shifts-workers.team_name') }}</p>
                                            <h3 class="team-value">{{ $team->name }}</h3>
                                            <p class="team-code">{{ $team->team_code }}</p>
                                        </div>
                                    </div>

                                    <div class="supervisor-info-box">
                                        <div class="supervisor-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="9" cy="7" r="4"></circle>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="supervisor-label">{{ __('shifts-workers.team_manager') }}</p>
                                            <h3 class="supervisor-value">{{ $teamManager->name ?? ($team->manager->name ?? 'غير محدد') }}</h3>
                                        </div>
                                    </div>
                                </div>

                                <!-- قائمة أعضاء الفريق -->
                                <div class="team-members-section">
                                    <h4 class="members-title">أعضاء الفريق ({{ $workers->count() }})</h4>
                                    <div class="workers-grid">
                                        @foreach($workers as $index => $worker)
                                            <div class="worker-card">
                                                <div class="worker-card-header">
                                                    <div class="worker-number">{{ $index + 1 }}</div>
                                                    <div class="worker-avatar">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                            <circle cx="12" cy="7" r="4"></circle>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="worker-card-body">
                                                    <h5 class="worker-name">{{ $worker->name }}</h5>
                                                    @if($worker->email)
                                                        <p class="worker-email">{{ $worker->email }}</p>
                                                    @endif
                                                    @if($worker->worker_code)
                                                        <p class="worker-code">{{ $worker->worker_code }}</p>
                                                    @endif
                                                </div>
                                                <div class="worker-card-footer">
                                                    <span class="status-badge assigned">{{ __('shifts-workers.assigned') }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- عرض العمال الفرديين -->
                            <div class="workers-list">
                                @foreach($workers as $worker)
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

    <style>
        .shift-quick-info {
            display: flex;
            gap: 20px;
            margin-top: 12px;
            flex-wrap: wrap;
            font-size: 13px;
        }

        .supervisor-info,
        .end-time-info {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 12px;
            border-radius: 6px;
            color: #fff;
            font-weight: 500;
        }

        .supervisor-info svg,
        .end-time-info svg {
            flex-shrink: 0;
            opacity: 0.9;
        }

        .supervisor-info strong,
        .end-time-info strong {
            font-weight: 600;
            margin-right: 4px;
        }

        /* Team Display Styles */
        .team-display-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
        }

        .team-header-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .team-info-box,
        .supervisor-info-box {
            display: flex;
            gap: 15px;
            align-items: flex-start;
            padding: 15px;
            background: white;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .team-info-box:hover,
        .supervisor-info-box:hover {
            border-color: #3b82f6;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
        }

        .team-icon,
        .supervisor-icon {
            width: 48px;
            height: 48px;
            min-width: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .supervisor-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .team-icon svg,
        .supervisor-icon svg {
            width: 24px;
            height: 24px;
        }

        .team-label,
        .supervisor-label {
            font-size: 12px;
            color: #999;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .team-value,
        .supervisor-value {
            font-size: 16px;
            font-weight: 700;
            margin: 5px 0 3px 0;
            color: #333;
        }

        .team-code {
            font-size: 12px;
            color: #666;
            margin: 0;
            font-weight: 500;
        }

        .team-members-section {
            margin-top: 20px;
        }

        .members-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .members-title::before {
            content: '';
            width: 4px;
            height: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }

        .workers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
        }

        .worker-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .worker-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
            transform: translateY(-2px);
        }

        .worker-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .worker-number {
            font-size: 20px;
            font-weight: 700;
            min-width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .worker-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .worker-avatar svg {
            width: 24px;
            height: 24px;
        }

        .worker-card-body {
            padding: 15px;
            flex-grow: 1;
        }

        .worker-name {
            font-size: 14px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #333;
        }

        .worker-email {
            font-size: 12px;
            color: #666;
            margin: 0 0 5px 0;
            word-break: break-all;
        }

        .worker-code {
            font-size: 11px;
            color: #999;
            margin: 0;
            font-weight: 600;
            text-transform: uppercase;
        }

        .worker-card-footer {
            padding: 10px 15px;
            border-top: 1px solid #f0f0f0;
            background: #f9f9f9;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.assigned {
            background: #d4edda;
            color: #155724;
        }

        @media (max-width: 1024px) {
            .team-header-section {
                grid-template-columns: 1fr;
            }

            .workers-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .shift-quick-info {
                flex-direction: column;
                gap: 10px;
            }

            .supervisor-info,
            .end-time-info {
                width: 100%;
                justify-content: space-between;
            }

            .team-display-card {
                padding: 15px;
            }

            .team-header-section {
                gap: 12px;
                margin-bottom: 18px;
                padding-bottom: 15px;
            }

            .team-info-box,
            .supervisor-info-box {
                padding: 12px;
                gap: 10px;
            }

            .team-icon,
            .supervisor-icon {
                width: 40px;
                height: 40px;
            }

            .team-value,
            .supervisor-value {
                font-size: 14px;
            }

            .workers-grid {
                grid-template-columns: 1fr;
            }

            .worker-card-header {
                padding: 12px;
            }

            .worker-card-body {
                padding: 12px;
            }
        }
    </style>
@endsection
