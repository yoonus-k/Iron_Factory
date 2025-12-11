@extends('master')

@section('title', __('shifts-workers.end_shift_with_handover'))

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <i class="feather icon-log-out"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ __('shifts-workers.end_shift_with_handover') }}</h1>
                        <p class="subtitle">{{ __('shifts-workers.stage') }} {{ $stageNumber }}</p>
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

        @if($errors->any())
        <div class="um-alert-custom um-alert-danger" role="alert">
            <i class="feather icon-alert-circle"></i>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
        @endif

        <!-- Shift Info Card -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <div class="card-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="feather icon-clock"></i>
                </div>
                <div>
                    <h3>{{ __('shifts-workers.shift_information') }}</h3>
                    <p class="subtitle">{{ __('shifts-workers.current_shift') }}</p>
                </div>
            </div>
            <div class="card-body">
                <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div class="info-item">
                        <label style="display: block; color: #6c757d; font-size: 0.875rem; margin-bottom: 5px;">{{ __('shifts-workers.shift_code') }}</label>
                        <strong style="display: block; color: #2c3e50;">{{ $currentShift->shift_code }}</strong>
                    </div>
                    <div class="info-item">
                        <label style="display: block; color: #6c757d; font-size: 0.875rem; margin-bottom: 5px;">{{ __('shifts-workers.shift_type') }}</label>
                        <strong style="display: block; color: #2c3e50;">{{ $currentShift->shift_type_name }}</strong>
                    </div>
                    <div class="info-item">
                        <label style="display: block; color: #6c757d; font-size: 0.875rem; margin-bottom: 5px;">{{ __('shifts-workers.shift_date') }}</label>
                        <strong style="display: block; color: #2c3e50;">{{ $currentShift->shift_date->format('Y-m-d') }}</strong>
                    </div>
                    <div class="info-item">
                        <label style="display: block; color: #6c757d; font-size: 0.875rem; margin-bottom: 5px;">{{ __('shifts-workers.stage') }}</label>
                        <strong style="display: block; color: #2c3e50;">{{ __('shifts-workers.stage_' . $stageNumber) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Work Items -->
        @if(count($pendingItems) > 0)
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <div class="card-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="feather icon-alert-circle"></i>
                </div>
                <div style="flex: 1;">
                    <h3>{{ __('shifts-workers.pending_work') }}</h3>
                    <p class="subtitle">{{ count($pendingItems) }} {{ __('shifts-workers.pending_work_items') }}</p>
                </div>
                <span class="badge" style="background: #f5576c; color: white; padding: 8px 16px; border-radius: 20px; font-size: 0.875rem;">
                    {{ __('shifts-workers.must_handover') }}
                </span>
            </div>
            <div class="card-body">
                <div class="pending-items-list">
                    @foreach($pendingItems as $index => $item)
                    <div class="pending-item" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: #f8f9fa;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                    <span style="background: #667eea; color: white; padding: 4px 12px; border-radius: 15px; font-size: 0.813rem; font-weight: 600;">
                                        {{ $item['barcode'] }}
                                    </span>
                                    <span style="background: #28a745; color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem;">
                                        {{ $item['status_name'] }}
                                    </span>
                                </div>
                                <p style="color: #6c757d; font-size: 0.875rem; margin: 5px 0;">
                                    <i class="feather icon-clock"></i> {{ $item['duration'] }}
                                </p>
                            </div>
                            <div style="text-align: center; min-width: 80px;">
                                <div class="progress-circle" style="position: relative; width: 60px; height: 60px;">
                                    <svg width="60" height="60" style="transform: rotate(-90deg);">
                                        <circle cx="30" cy="30" r="25" fill="none" stroke="#e9ecef" stroke-width="5"/>
                                        <circle cx="30" cy="30" r="25" fill="none" stroke="#667eea" stroke-width="5"
                                                stroke-dasharray="{{ 2 * 3.14159 * 25 }}"
                                                stroke-dashoffset="{{ 2 * 3.14159 * 25 * (1 - $item['progress'] / 100) }}"
                                                style="transition: stroke-dashoffset 0.5s ease;"/>
                                    </svg>
                                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; color: #667eea;">
                                        {{ $item['progress'] }}%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Item Details -->
                        <div class="item-details" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; padding-top: 10px; border-top: 1px solid #dee2e6;">
                            @foreach($item['details'] as $key => $value)
                                @if(!is_null($value) && $value !== '')
                                <div>
                                    <small style="display: block; color: #6c757d; font-size: 0.75rem;">{{ __('shifts-workers.' . $key) }}</small>
                                    <strong style="display: block; color: #2c3e50; font-size: 0.875rem;">{{ is_numeric($value) ? number_format($value, 2) : $value }}</strong>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Handover Form -->
        <form action="{{ route('manufacturing.shift-handovers.store-end-shift') }}" method="POST" id="handoverForm">
            @csrf
            <input type="hidden" name="stage_number" value="{{ $stageNumber }}">
            <input type="hidden" name="pending_items" value="{{ json_encode($pendingItems) }}">

            <div class="card">
                <div class="card-header">
                    <div class="card-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <i class="feather icon-user-check"></i>
                    </div>
                    <div>
                        <h3>{{ __('shifts-workers.transfer_to_next_shift') }}</h3>
                        <p class="subtitle">{{ __('shifts-workers.select_next_worker') }}</p>
                    </div>
                </div>
                <div class="card-body">
                    @if($nextShiftWorker)
                    <!-- Next Shift Worker Info -->
                    <div class="alert" style="background: #d1ecf1; border-left: 4px solid #0c5460; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="background: #0c5460; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                <i class="feather icon-user"></i>
                            </div>
                            <div>
                                <h4 style="margin: 0 0 5px 0; color: #0c5460;">{{ __('shifts-workers.next_shift_worker') }}</h4>
                                <p style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #155724;">{{ $nextShiftWorker->name }}</p>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="to_user_id" value="{{ $nextShiftWorker->id }}">
                    @else
                    <!-- Manual Selection -->
                    <div class="alert" style="background: #fff3cd; border-left: 4px solid #856404; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <i class="feather icon-alert-triangle"></i>
                        {{ __('shifts-workers.no_next_shift_worker') }}
                    </div>
                    <div class="form-group">
                        <label for="to_user_id">{{ __('shifts-workers.select_worker') }} <span style="color: red;">*</span></label>
                        <select name="to_user_id" id="to_user_id" class="form-control" required>
                            <option value="">{{ __('shifts-workers.select_worker') }}</option>
                            @foreach(\App\Models\User::where('is_active', true)->orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Notes -->
                    <div class="form-group" style="margin-top: 20px;">
                        <label for="notes">{{ __('shifts-workers.notes') }} ({{ __('shifts-workers.optional') }})</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="{{ __('shifts-workers.handover_notes_placeholder') }}">{{ old('notes') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="notes_en">{{ __('shifts-workers.notes') }} ({{ __('shifts-workers.english') }}) ({{ __('shifts-workers.optional') }})</label>
                        <textarea name="notes_en" id="notes_en" rows="3" class="form-control" placeholder="{{ __('shifts-workers.handover_notes_placeholder') }}">{{ old('notes_en') }}</textarea>
                    </div>
                </div>
                <div class="card-footer" style="display: flex; gap: 10px; justify-content: flex-end; padding: 20px; background: #f8f9fa; border-top: 1px solid #dee2e6;">
                    <a href="{{ route('manufacturing.shifts-workers.index') }}" class="btn btn-secondary">
                        <i class="feather icon-x"></i>
                        {{ __('shifts-workers.cancel_button') }}
                    </a>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="feather icon-check"></i>
                        {{ __('shifts-workers.handover_confirm') }}
                    </button>
                </div>
            </div>
        </form>

        @else
        <!-- No Pending Work -->
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 60px 20px;">
                <div style="width: 100px; height: 100px; margin: 0 auto 20px; background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="feather icon-check-circle" style="font-size: 3rem; color: white;"></i>
                </div>
                <h3 style="color: #28a745; margin-bottom: 10px;">{{ __('shifts-workers.no_pending_work') }}</h3>
                <p style="color: #6c757d; margin-bottom: 30px;">{{ __('shifts-workers.all_work_completed') }}</p>
                <a href="{{ route('manufacturing.shifts-workers.index') }}" class="btn btn-primary">
                    <i class="feather icon-arrow-left"></i>
                    {{ __('shifts-workers.back_to_shifts') }}
                </a>
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
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 0.938rem;
        }
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.938rem;
        }
    </style>
@endsection
