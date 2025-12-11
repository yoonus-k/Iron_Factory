@extends('master')

@section('title', __('shifts-workers.pending_work'))

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <i class="feather icon-inbox"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ __('shifts-workers.my_pending_work') }}</h1>
                        <p class="subtitle">{{ __('shifts-workers.view_pending_work') }}</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.shifts-workers.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        {{ __('shifts-workers.back_button') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Success and Error Messages -->
        @if(session('success'))
        <div class="um-alert-custom um-alert-success" role="alert">
            <i class="feather icon-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="um-alert-custom um-alert-danger" role="alert">
            <i class="feather icon-alert-circle"></i>
            {{ session('error') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 12px; color: white;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <p style="opacity: 0.9; font-size: 0.875rem; margin-bottom: 5px;">{{ __('shifts-workers.stage_1') }}</p>
                        <h2 style="font-size: 2rem; font-weight: bold; margin: 0;">{{ $stats['stage_1'] }}</h2>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="feather icon-package" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 20px; border-radius: 12px; color: white;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <p style="opacity: 0.9; font-size: 0.875rem; margin-bottom: 5px;">{{ __('shifts-workers.stage_2') }}</p>
                        <h2 style="font-size: 2rem; font-weight: bold; margin: 0;">{{ $stats['stage_2'] }}</h2>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="feather icon-settings" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); padding: 20px; border-radius: 12px; color: white;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <p style="opacity: 0.9; font-size: 0.875rem; margin-bottom: 5px;">{{ __('shifts-workers.stage_3') }}</p>
                        <h2 style="font-size: 2rem; font-weight: bold; margin: 0;">{{ $stats['stage_3'] }}</h2>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="feather icon-disc" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 20px; border-radius: 12px; color: white;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <p style="opacity: 0.9; font-size: 0.875rem; margin-bottom: 5px;">{{ __('shifts-workers.stage_4') }}</p>
                        <h2 style="font-size: 2rem; font-weight: bold; margin: 0;">{{ $stats['stage_4'] }}</h2>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="feather icon-box" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Handovers -->
        @if($pendingHandovers->count() > 0)
        <div class="card" style="margin-bottom: 30px;">
            <div class="card-header">
                <div class="card-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="feather icon-bell"></i>
                </div>
                <div style="flex: 1;">
                    <h3>{{ __('shifts-workers.pending_handovers') }}</h3>
                    <p class="subtitle">{{ __('shifts-workers.handovers_not_acknowledged') }}</p>
                </div>
                <span class="badge" style="background: #dc3545; color: white; padding: 8px 16px; border-radius: 20px;">
                    {{ $pendingHandovers->count() }}
                </span>
            </div>
            <div class="card-body">
                @foreach($pendingHandovers as $handover)
                <div class="handover-item" style="border: 2px solid #f093fb; border-radius: 12px; padding: 20px; margin-bottom: 15px; background: linear-gradient(to right, #fff5f8, #ffffff);">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <span style="background: #f5576c; color: white; padding: 6px 14px; border-radius: 20px; font-size: 0.875rem; font-weight: 600;">
                                    {{ __('shifts-workers.stage') }} {{ $handover->stage_number }}
                                </span>
                                <span style="background: #ffc107; color: #212529; padding: 6px 14px; border-radius: 20px; font-size: 0.875rem; font-weight: 600;">
                                    <i class="feather icon-clock"></i> {{ __('shifts-workers.not_acknowledged') }}
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <i class="feather icon-user" style="color: #667eea;"></i>
                                <span style="color: #6c757d; font-size: 0.938rem;">{{ __('shifts-workers.from') }}:</span>
                                <strong style="color: #2c3e50;">{{ $handover->fromUser->name }}</strong>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="feather icon-calendar" style="color: #667eea;"></i>
                                <span style="color: #6c757d; font-size: 0.875rem;">{{ $handover->handover_time->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="background: #f5576c; color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin-bottom: 8px;">
                                {{ $handover->pending_items_count }}
                            </div>
                            <small style="color: #6c757d; font-size: 0.75rem;">{{ __('shifts-workers.pending_items_count') }}</small>
                        </div>
                    </div>

                    @if($handover->notes)
                    <div style="background: #fff; border-left: 4px solid #667eea; padding: 12px 15px; border-radius: 6px; margin-bottom: 15px;">
                        <small style="color: #6c757d; display: block; margin-bottom: 5px;">{{ __('shifts-workers.notes') }}:</small>
                        <p style="margin: 0; color: #2c3e50; font-size: 0.938rem;">{{ $handover->notes }}</p>
                    </div>
                    @endif

                    <!-- Pending Items Preview -->
                    @if($handover->handover_items && count($handover->handover_items) > 0)
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <strong style="display: block; margin-bottom: 10px; color: #2c3e50;">{{ __('shifts-workers.pending_work_items') }}:</strong>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px;">
                            @foreach(array_slice($handover->handover_items, 0, 4) as $item)
                            <div style="background: white; padding: 10px; border-radius: 6px; border: 1px solid #dee2e6;">
                                <div style="font-size: 0.813rem; color: #667eea; font-weight: 600; margin-bottom: 5px;">{{ $item['barcode'] ?? '' }}</div>
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <div style="flex: 1; background: #e9ecef; height: 4px; border-radius: 2px; overflow: hidden;">
                                        <div style="background: #667eea; height: 100%; width: {{ $item['progress'] ?? 0 }}%;"></div>
                                    </div>
                                    <small style="color: #6c757d; font-size: 0.75rem;">{{ $item['progress'] ?? 0 }}%</small>
                                </div>
                            </div>
                            @endforeach
                            @if(count($handover->handover_items) > 4)
                            <div style="background: #e9ecef; padding: 10px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <span style="color: #6c757d; font-size: 0.875rem; font-weight: 600;">+{{ count($handover->handover_items) - 4 }} {{ __('shifts-workers.more') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <a href="{{ route('manufacturing.shift-handovers.show', $handover->id) }}" class="btn btn-secondary" style="text-decoration: none;">
                            <i class="feather icon-eye"></i>
                            {{ __('shifts-workers.view_details') }}
                        </a>
                        <form action="{{ route('manufacturing.shift-handovers.acknowledge', $handover->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <i class="feather icon-check"></i>
                                {{ __('shifts-workers.acknowledge_handover') }}
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 60px 20px;">
                <div style="width: 100px; height: 100px; margin: 0 auto 20px; background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="feather icon-check-circle" style="font-size: 3rem; color: white;"></i>
                </div>
                <h3 style="color: #28a745; margin-bottom: 10px;">{{ __('shifts-workers.no_pending_handovers') }}</h3>
                <p style="color: #6c757d; margin-bottom: 30px;">{{ __('shifts-workers.all_handovers_acknowledged') }}</p>
            </div>
        </div>
        @endif

        <!-- Current Work Summary -->
        @php
            $totalPending = array_sum($stats);
        @endphp
        @if($totalPending > 0)
        <div class="card">
            <div class="card-header">
                <div class="card-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="feather icon-activity"></i>
                </div>
                <div>
                    <h3>{{ __('shifts-workers.current_work_summary') }}</h3>
                    <p class="subtitle">{{ __('shifts-workers.total_pending_items') }}: {{ $totalPending }}</p>
                </div>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    @foreach(['stage_1', 'stage_2', 'stage_3', 'stage_4'] as $stage)
                        @if($stats[$stage] > 0)
                        <div style="border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; background: linear-gradient(to bottom, #ffffff, #f8f9fa);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <h4 style="margin: 0; color: #2c3e50;">{{ __('shifts-workers.' . $stage) }}</h4>
                                <span style="background: #667eea; color: white; padding: 4px 12px; border-radius: 15px; font-weight: 600;">{{ $stats[$stage] }}</span>
                            </div>
                            <a href="{{ route('manufacturing.stage' . str_replace('stage_', '', $stage) . '.index') }}" class="btn btn-sm btn-outline-primary" style="width: 100%; margin-top: 10px;">
                                <i class="feather icon-arrow-right"></i>
                                {{ __('shifts-workers.go_to_stage') }}
                            </a>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <style>
        .um-alert-custom {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }
        .um-alert-success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        .um-alert-danger {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        .um-alert-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            opacity: 0.5;
        }
        .um-alert-close:hover {
            opacity: 1;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 0.938rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .btn-outline-primary {
            background: white;
            color: #667eea;
            border: 1px solid #667eea;
        }
        .btn-outline-primary:hover {
            background: #667eea;
            color: white;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.813rem;
        }
    </style>
@endsection
