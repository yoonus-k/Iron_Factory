@extends('master')

@section('title', __('shifts-workers.current_shifts'))

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-clock"></i>
                {{ __('shifts-workers.current_shifts') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('shifts-workers.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('shifts-workers.shifts_and_workers') }}</span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('shifts-workers.current_shifts') }}</span>
            </nav>
        </div>

    <!-- Messages -->
    @if (session('success'))
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">
            ❌ {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <strong>{{ __('shifts-workers.error_occurred') }}:</strong>
            <ul style="margin: 8px 0 0 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

        <!-- Active Shifts Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-activity"></i>
                    {{ __('shifts-workers.active_shifts_now') }}
                </h4>
                <div class="um-card-actions">
                    <button class="um-btn um-btn-outline">
                        <i class="feather icon-refresh-cw"></i>
                        {{ __('shifts-workers.refresh') }}
                    </button>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="{{ __('shifts-workers.search_shifts') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="shift_type" class="um-form-control">
                                <option value="">{{ __('shifts-workers.all_shift_types_filter') }}</option>
                                <option value="morning">{{ __('shifts-workers.morning') }}</option>
                                <option value="evening">{{ __('shifts-workers.evening') }}</option>
                                <option value="night">{{ __('shifts-workers.night') }}</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('shifts-workers.search') }}
                            </button>
                            <button type="reset" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                {{ __('shifts-workers.reset') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Active Shifts Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>{{ __('shifts-workers.shift_number') }}</th>
                            <th>{{ __('shifts-workers.date') }}</th>
                            <th>{{ __('shifts-workers.shift_type') }}</th>
                            <th>{{ __('shifts-workers.supervisor') }}</th>
                            <th>{{ __('shifts-workers.start_time') }}</th>
                            <th>{{ __('shifts-workers.workers_count') }}</th>
                            <th>{{ __('shifts-workers.status') }}</th>
                            <th>{{ __('shifts-workers.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($currentShifts as $shift)
                        <tr>
                            <td><strong>{{ $shift->shift_code }}</strong></td>
                            <td>{{ $shift->shift_date->format('Y-m-d') }}</td>
                            <td>
                                <span class="um-badge um-badge-{{ $shift->shift_type == 'morning' ? 'info' : ($shift->shift_type == 'evening' ? 'warning' : 'danger') }}">
                                    {{ $shift->shift_type_name }}
                                </span>
                            </td>
                            <td>{{ $shift->supervisor->name ?? __('shifts-workers.not_specified') }}</td>
                            <td>{{ $shift->start_time }}</td>
                            <td>{{ $shift->total_workers }}</td>
                            <td>
                                <span class="um-badge um-badge-success">{{ $shift->status_name }}</span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="{{ __('shifts-workers.actions') }}">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        @if(auth()->user()->hasPermission(''))
                                        <a href="{{ route('manufacturing.shifts-workers.show', $shift->id) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>{{ __('shifts-workers.view_details') }}</span>
                                        </a>
                                        @endif
                                        @if(auth()->user()->hasPermission(''))
                                        <form action="{{ route('manufacturing.shifts-workers.complete', $shift->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="um-dropdown-item um-btn-edit" onclick="return confirm('{{ __('shifts-workers.confirm_complete') }}');">
                                                <i class="feather icon-check-circle"></i>
                                                <span>{{ __('shifts-workers.end_shift_now') }}</span>
                                            </button>
                                        </form>
                                        @endif
                                        @if(auth()->user()->hasPermission(''))
                                        <button type="button" class="um-dropdown-item um-btn-toggle" onclick="openSuspendModal({{ $shift->id }})">
                                            <i class="feather icon-pause-circle"></i>
                                            <span>{{ __('shifts-workers.suspend_shift') }}</span>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                                <i class="feather icon-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                {{ __('shifts-workers.no_shifts_found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View - Cards -->
            <div class="um-mobile-view">
                @forelse($currentShifts as $shift)
                <div class="um-mobile-card">
                    <div class="um-mobile-card-header">
                        <div class="um-mobile-card-title">
                            <strong>{{ $shift->shift_code }}</strong>
                            <span class="um-badge um-badge-{{ $shift->shift_type == 'morning' ? 'info' : ($shift->shift_type == 'evening' ? 'warning' : 'danger') }}">
                                {{ $shift->shift_type_name }}
                            </span>
                        </div>
                        <span class="um-badge um-badge-success">{{ $shift->status_name }}</span>
                    </div>

                    <div class="um-mobile-card-body">
                        <div class="um-mobile-info-row">
                            <span class="um-mobile-label">{{ __('shifts-workers.date') }}:</span>
                            <span class="um-mobile-value">{{ $shift->shift_date->format('Y-m-d') }}</span>
                        </div>
                        <div class="um-mobile-info-row">
                            <span class="um-mobile-label">{{ __('shifts-workers.supervisor') }}:</span>
                            <span class="um-mobile-value">{{ $shift->supervisor->name ?? __('shifts-workers.not_specified') }}</span>
                        </div>
                        <div class="um-mobile-info-row">
                            <span class="um-mobile-label">{{ __('shifts-workers.start_time') }}:</span>
                            <span class="um-mobile-value">{{ $shift->start_time }}</span>
                        </div>
                        <div class="um-mobile-info-row">
                            <span class="um-mobile-label">{{ __('shifts-workers.workers_count') }}:</span>
                            <span class="um-mobile-value">{{ $shift->total_workers }}</span>
                        </div>
                    </div>

                    <div class="um-mobile-card-footer">
                        @if(auth()->user()->hasPermission(''))
                        <a href="{{ route('manufacturing.shifts-workers.show', $shift->id) }}" class="um-btn um-btn-sm um-btn-primary">
                            <i class="feather icon-eye"></i>
                            {{ __('shifts-workers.view_details') }}
                        </a>
                        @endif
                        @if(auth()->user()->hasPermission(''))
                        <form action="{{ route('manufacturing.shifts-workers.complete', $shift->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="um-btn um-btn-sm um-btn-success" onclick="return confirm('{{ __('shifts-workers.confirm_complete') }}');">
                                <i class="feather icon-check-circle"></i>
                                {{ __('shifts-workers.end_shift_now') }}
                            </button>
                        </form>
                        @endif
                        @if(auth()->user()->hasPermission(''))
                        <button type="button" class="um-btn um-btn-sm um-btn-warning" onclick="openSuspendModal({{ $shift->id }})">
                            <i class="feather icon-pause-circle"></i>
                            {{ __('shifts-workers.suspend_shift') }}
                        </button>
                        @endif
                    </div>
                </div>
                @empty
                <div class="um-empty-state">
                    <i class="feather icon-inbox"></i>
                    <p>{{ __('shifts-workers.no_shifts_found') }}</p>
                </div>
                @endforelse
            </div>

        </section>
    </div>

    <!-- Suspend Modal -->
    <div id="suspendModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>{{ __('shifts-workers.suspend_shift_title') }}</h3>
                <button class="close-btn" onclick="closeSuspendModal()">×</button>
            </div>
            <form id="suspendForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="suspension_reason">{{ __('shifts-workers.suspension_reason') }}</label>
                        <textarea
                            id="suspension_reason"
                            name="suspension_reason"
                            class="form-control"
                            rows="4"
                            placeholder="{{ __('shifts-workers.suspension_reason_placeholder') }}">
                        </textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeSuspendModal()">{{ __('shifts-workers.cancel') }}</button>
                    <button type="submit" class="btn btn-warning">{{ __('shifts-workers.suspend_shift_action') }}</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Desktop/Mobile View Toggle */
        .um-desktop-view {
            display: block;
        }

        .um-mobile-view {
            display: none;
        }

        /* Mobile Cards Styles */
        .um-mobile-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .um-mobile-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }

        .um-mobile-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 600;
        }

        .um-mobile-card-body {
            padding: 15px;
        }

        .um-mobile-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .um-mobile-info-row:last-child {
            border-bottom: none;
        }

        .um-mobile-label {
            font-weight: 500;
            color: #666;
            font-size: 14px;
        }

        .um-mobile-value {
            font-weight: 400;
            color: #333;
            font-size: 14px;
        }

        .um-mobile-card-footer {
            padding: 15px;
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .um-mobile-card-footer .um-btn {
            flex: 1;
            min-width: 120px;
        }

        .um-empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .um-empty-state i {
            font-size: 64px;
            display: block;
            margin-bottom: 15px;
            opacity: 0.3;
        }

        .um-empty-state p {
            font-size: 16px;
            margin: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .um-desktop-view {
                display: none;
            }

            .um-mobile-view {
                display: block;
            }

            .um-filter-row {
                flex-direction: column;
            }

            .um-form-group {
                width: 100% !important;
            }

            .um-filter-actions {
                width: 100%;
                display: flex;
                gap: 8px;
            }

            .um-filter-actions .um-btn {
                flex: 1;
            }

            .um-mobile-card-footer {
                flex-direction: column;
            }

            .um-mobile-card-footer .um-btn {
                width: 100%;
                min-width: auto;
            }
        }

        /* Modal Styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-btn:hover {
            color: #333;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
        }

        .form-control:focus {
            outline: none;
            border-color: #0052B3;
            box-shadow: 0 0 0 3px rgba(0, 82, 179, 0.1);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .btn-warning {
            background: #f39c12;
            color: white;
        }

        .btn-warning:hover {
            background: #e67e22;
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 10px;
            }

            .modal-footer {
                flex-direction: column;
            }

            .modal-footer .btn {
                width: 100%;
            }
        }
    </style>

    <script>
        function openSuspendModal(shiftId) {
            const form = document.getElementById('suspendForm');
            form.action = `/manufacturing/shifts-workers/${shiftId}/suspend`;
            document.getElementById('suspendModal').style.display = 'flex';
        }

        function closeSuspendModal() {
            document.getElementById('suspendModal').style.display = 'none';
            document.getElementById('suspension_reason').value = '';
        }

        // Close modal when clicking outside
        document.getElementById('suspendModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSuspendModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSuspendModal();
            }
        });
    </script>

@endsection


