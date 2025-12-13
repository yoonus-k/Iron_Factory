@extends('master')

@section('title', 'إدارة الورديات والعمال')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-users"></i>
                {{ __('shifts-workers.manage_shifts_and_workers') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('shifts-workers.dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('shifts-workers.shifts_and_workers') }}</span>
            </nav>
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

        <!-- Main Courses Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    {{ __('shifts-workers.workers_list') }}
                </h4>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                    @if(auth()->user()->hasPermission('SHIFT_HANDOVERS_READ'))
                    <!-- زر جميع التسليمات -->
                    <a href="{{ route('manufacturing.shift-handovers.index') }}" class="um-btn um-btn-info" style="display: flex; align-items: center; gap: 8px;">
                        <i class="feather icon-list"></i>
                        {{ __('shifts-workers.all_handovers') }}
                    </a>

                    <!-- زر أشغالي المعلقة -->
                    <a href="{{ route('manufacturing.shift-handovers.my-pending-work') }}" class="um-btn um-btn-warning" style="display: flex; align-items: center; gap: 8px; position: relative;">
                        <i class="feather icon-inbox"></i>
                        {{ __('shifts-workers.my_pending_work') }}
                        @php
                            $pendingCount = \App\Models\ShiftHandover::where('to_user_id', auth()->id())
                                ->whereNull('acknowledged_at')
                                ->count();
                        @endphp
                        @if($pendingCount > 0)
                        <span style="position: absolute; top: -8px; right: -8px; background: #dc3545; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; font-weight: 600; min-width: 20px; text-align: center;">{{ $pendingCount }}</span>
                        @endif
                    </a>

                    <!-- زر الموافقات المعلقة -->
                    <a href="{{ route('manufacturing.shift-handovers.index', ['approval_status' => 'pending']) }}" class="um-btn um-btn-secondary" style="display: flex; align-items: center; gap: 8px; position: relative;">
                        <i class="feather icon-clock"></i>
                        {{ __('shifts-workers.pending_approvals') }}
                        @php
                            $approvalCount = \App\Models\ShiftHandover::where('supervisor_approved', false)->count();
                        @endphp
                        @if($approvalCount > 0)
                        <span style="position: absolute; top: -8px; right: -8px; background: #ffc107; color: #212529; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; font-weight: 600; min-width: 20px; text-align: center;">{{ $approvalCount }}</span>
                        @endif
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('SHIFTS_CREATE'))
                    <a href="{{ route('manufacturing.shifts-workers.create') }}" class="um-btn um-btn-primary">
                        <i class="feather icon-plus"></i>
                        {{ __('shifts-workers.add_new_shift') }}
                    </a>
                    @endif
                </div>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET" action="{{ route('manufacturing.shifts-workers.index') }}">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="date" name="date" class="um-form-control" placeholder="{{ __('shifts-workers.filter_by_date') }}" value="{{ request('date') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="shift_type" class="um-form-control">
                                <option value="">{{ __('shifts-workers.all_shift_types') }}</option>
                                <option value="morning" {{ request('shift_type') == 'morning' ? 'selected' : '' }}>{{ __('shifts-workers.first_period') }}</option>
                                <option value="evening" {{ request('shift_type') == 'evening' ? 'selected' : '' }}>{{ __('shifts-workers.second_period') }}</option>
                                <option value="night" {{ request('shift_type') == 'night' ? 'selected' : '' }}>{{ __('shifts-workers.night') }}</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">{{ __('shifts-workers.all_statuses') }}</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>{{ __('shifts-workers.scheduled') }}</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('shifts-workers.active') }}</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('shifts-workers.completed') }}</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('shifts-workers.cancelled') }}</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="stage_number" class="um-form-control">
                                <option value="">{{ __('shifts-workers.all_stages') }}</option>
                                <option value="1" {{ request('stage_number') == '1' ? 'selected' : '' }}>{{ __('shifts-workers.stage_first') }}</option>
                                <option value="2" {{ request('stage_number') == '2' ? 'selected' : '' }}>{{ __('shifts-workers.stage_second') }}</option>
                                <option value="3" {{ request('stage_number') == '3' ? 'selected' : '' }}>{{ __('shifts-workers.stage_third') }}</option>
                                <option value="4" {{ request('stage_number') == '4' ? 'selected' : '' }}>{{ __('shifts-workers.stage_fourth') }}</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                {{ __('shifts-workers.apply_filters') }}
                            </button>
                            <a href="{{ route('manufacturing.shifts-workers.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                {{ __('shifts-workers.clear_filters') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Courses Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('shifts-workers.shift_number') }}</th>
                            <th>{{ __('shifts-workers.date') }}</th>
                            <th>{{ __('shifts-workers.shift_type') }}</th>
                            <th>{{ __('shifts-workers.workers_count') }}</th>
                            <th>{{ __('shifts-workers.supervisor') }}</th>
                            <th>{{ __('shifts-workers.status') }}</th>
                            <th>{{ __('workers.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $index => $shift)
                        <tr>
                            <td>{{ $shifts->firstItem() + $index }}</td>
                            <td><strong>{{ $shift->shift_code }}</strong></td>
                            <td>{{ $shift->shift_date->format('Y-m-d') }}</td>
                            <td>
                                <span class="um-badge um-badge-{{ $shift->shift_type == 'morning' ? 'info' : ($shift->shift_type == 'evening' ? 'warning' : 'danger') }}">
                                    {{ $shift->shift_type_name }}
                                </span>
                            </td>
                            <td>{{ $shift->total_workers }}</td>
                            <td>{{ $shift->supervisor->name ?? 'غير محدد' }}</td>
                            <td>
                                <span class="um-badge um-badge-{{ $shift->status == 'active' ? 'success' : ($shift->status == 'scheduled' ? 'info' : 'secondary') }}">
                                    {{ $shift->status_name }}
                                </span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="{{ __('workers.actions') }}">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        @if (auth()->user()->hasPermission('SHIFTS_READ'))
                                        <a href="{{ route('manufacturing.shifts-workers.show', $shift->id) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>{{ __('shifts-workers.view') }}</span>
                                        </a>
                                        @endif
                                        @if (auth()->user()->hasPermission('SHIFTS_UPDATE'))
                                        <a href="{{ route('manufacturing.shifts-workers.edit', $shift->id) }}" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-edit-2"></i>
                                            <span>{{ __('shifts-workers.edit') }}</span>
                                        </a>
                                        @endif
                                        @if($shift->status == 'scheduled')
                                            @if (auth()->user()->hasPermission('SHIFTS_ACTIVATE'))
                                            <form method="POST" action="{{ route('manufacturing.shifts-workers.activate', $shift->id) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="um-dropdown-item um-btn-feature">
                                                    <i class="feather icon-play"></i>
                                                    <span>{{ __('shifts-workers.activate_shift') }}</span>
                                                </button>
                                            </form>
                                            @endif
                                        @elseif($shift->status == 'active')
                                            @if (auth()->user()->hasPermission('SHIFTS_COMPLETE'))
                                            <form method="POST" action="{{ route('manufacturing.shifts-workers.complete', $shift->id) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="um-dropdown-item um-btn-toggle">
                                                    <i class="feather icon-check"></i>
                                                    <span>{{ __('shifts-workers.complete_shift') }}</span>
                                                </button>
                                            </form>
                                            @endif
                                            @if (auth()->user()->hasPermission('SHIFTS_SUSPEND'))
                                            <button type="button" class="um-dropdown-item um-btn-warning" onclick="openSuspendModal({{ $shift->id }})">
                                                <i class="feather icon-pause"></i>
                                                <span>{{ __('shifts-workers.suspend_shift') }}</span>
                                            </button>
                                            @endif
                                            @if (auth()->user()->hasPermission('SHIFT_HANDOVERS_FROM_INDEX'))
                                            <button type="button" class="um-dropdown-item um-btn-info" onclick="openHandoverModal({{ $shift->id }}, '{{ $shift->shift_code }}')">
                                                <i class="feather icon-exchange-2"></i>
                                                <span>{{ __('shifts-workers.shift_handover') }}</span>
                                            </button>
                                            @endif
                                        @elseif($shift->status == 'suspended')
                                            @if (auth()->user()->hasPermission('SHIFTS_RESUME'))
                                            <form method="POST" action="{{ route('manufacturing.shifts-workers.resume', $shift->id) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="um-dropdown-item um-btn-feature">
                                                    <i class="feather icon-play"></i>
                                                    <span>{{ __('shifts-workers.resume_shift') }}</span>
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                        @if(in_array($shift->status, ['scheduled', 'cancelled']))
                                            @if (auth()->user()->hasPermission('SHIFTS_DELETE'))
                                            <form method="POST" action="{{ route('manufacturing.shifts-workers.destroy', $shift->id) }}" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="um-dropdown-item um-btn-delete">
                                                    <i class="feather icon-trash-2"></i>
                                                    <span>{{ __('shifts-workers.delete') }}</span>
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                                <i class="feather icon-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                {{ __('shifts-workers.no_records') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Courses Cards - Mobile View -->
            <div class="um-mobile-view">
                @forelse($shifts as $shift)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                <i class="feather icon-users" style="font-size: 18px;"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">{{ $shift->shift_type_name }}</h6>
                                <span class="um-category-id">{{ $shift->shift_code }}</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-{{ $shift->status == 'active' ? 'success' : ($shift->status == 'scheduled' ? 'info' : 'secondary') }}">
                            {{ $shift->status_name }}
                        </span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('shifts-workers.date') }}:</span>
                            <span class="um-info-value">{{ $shift->shift_date->format('Y-m-d') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('shifts-workers.workers_count') }}:</span>
                            <span class="um-info-value">{{ $shift->total_workers }} {{ __('shifts-workers.workers') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('shifts-workers.supervisor') }}:</span>
                            <span class="um-info-value">{{ $shift->supervisor->name ?? __('shifts-workers.not_specified') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">{{ __('shifts-workers.start_time') }}:</span>
                            <span class="um-info-value">{{ $shift->start_time }}</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        @if (auth()->user()->hasPermission('SHIFTS_READ'))
                        <a href="{{ route('manufacturing.shifts-workers.show', $shift->id) }}" class="um-btn um-btn-sm um-btn-primary">
                            <i class="feather icon-eye" style="font-size: 14px;"></i>
                            {{ __('shifts-workers.view') }}
                        </a>
                        @endif
                        @if (auth()->user()->hasPermission('SHIFTS_UPDATE'))
                        <a href="{{ route('manufacturing.shifts-workers.edit', $shift->id) }}" class="um-btn um-btn-sm um-btn-secondary">
                            <i class="feather icon-edit-2" style="font-size: 14px;"></i>
                            {{ __('shifts-workers.edit') }}
                        </a>
                        @endif
                        @if ($shift->status == 'scheduled' && auth()->user()->hasPermission('SHIFTS_ACTIVATE'))
                        <form method="POST" action="{{ route('manufacturing.shifts-workers.activate', $shift->id) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="um-btn um-btn-sm um-btn-success">
                                <i class="feather icon-play" style="font-size: 14px;"></i>
                                {{ __('shifts-workers.activate_shift') }}
                            </button>
                        </form>
                        @endif
                        @if ($shift->status == 'active' && auth()->user()->hasPermission('SHIFTS_COMPLETE'))
                        <form method="POST" action="{{ route('manufacturing.shifts-workers.complete', $shift->id) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="um-btn um-btn-sm um-btn-info">
                                <i class="feather icon-check" style="font-size: 14px;"></i>
                                {{ __('shifts-workers.complete_shift') }}
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 40px; color: #999;">
                    <i class="feather icon-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                    {{ __('shifts-workers.no_records') }}
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        {{ __('shifts-workers.showing') }} {{ $shifts->firstItem() ?? 0 }} {{ __('shifts-workers.to') }} {{ $shifts->lastItem() ?? 0 }} {{ __('shifts-workers.of') }} {{ $shifts->total() }} {{ __('shifts-workers.workers_list') }}
                    </p>
                </div>
                <div>
                    {{ $shifts->links() }}
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تأكيد الحذف
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('{{ __('shifts-workers.confirm_delete') }}')) {
                        form.submit();
                    }
                });
            });

            // إخفاء التنبيهات تلقائياً بعد 5 ثواني
            const alerts = document.querySelectorAll('.um-alert-custom');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.3s';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 300);
                }, 5000);
            });
        });

        // Suspend/Resume Modal Functions
        function openSuspendModal(shiftId) {
            const form = document.getElementById('suspendForm');
            form.action = `/manufacturing/shifts-workers/${shiftId}/suspend`;
            document.getElementById('suspendModal').style.display = 'flex';
        }

        function closeSuspendModal() {
            document.getElementById('suspendModal').style.display = 'none';
            document.getElementById('suspension_reason').value = '';
        }

        // Handover Modal Functions
        function openHandoverModal(shiftId, shiftCode) {
            const form = document.getElementById('handoverForm');
            form.action = `{{ route('manufacturing.shift-handovers.store') }}`;
            document.getElementById('handover_shift_id').value = shiftId;
            document.getElementById('handover_shift_code').textContent = shiftCode;
            document.getElementById('handoverModal').style.display = 'flex';
        }

        function closeHandoverModal() {
            document.getElementById('handoverModal').style.display = 'none';
            document.getElementById('handover_to_user_id').value = '';
            document.getElementById('handover_stage_number').value = '';
            document.getElementById('handover_notes').value = '';
            document.getElementById('handover_team_id').value = '';
            document.getElementById('handover_team_workers').value = '';
            // Reset to team tab
            switchHandoverTab('team');
            resetHandoverTeamSelection();
        }

        // Handover Tab Switching
        function switchHandoverTab(tab) {
            const teamTab = document.getElementById('team-tab');
            const individualTab = document.getElementById('individual-tab');
            const teamSelection = document.getElementById('team-selection');
            const individualSelection = document.getElementById('individual-selection');

            if (tab === 'team') {
                teamTab.classList.add('active');
                individualTab.classList.remove('active');
                teamSelection.classList.add('active');
                teamSelection.style.display = 'block';
                individualSelection.classList.remove('active');
                individualSelection.style.display = 'none';
                document.getElementById('handover_to_user_id').value = '';
            } else {
                individualTab.classList.add('active');
                teamTab.classList.remove('active');
                individualSelection.classList.add('active');
                individualSelection.style.display = 'block';
                teamSelection.classList.remove('active');
                teamSelection.style.display = 'none';
                resetHandoverTeamSelection();
            }
        }

        // Select Handover Team
        function selectHandoverTeam(teamId, workerIds) {
            // Remove selected class from all team cards
            document.querySelectorAll('.team-card-small').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selected class to clicked card
            if (event && event.currentTarget) {
                event.currentTarget.classList.add('selected');
            }

            // Set hidden fields
            document.getElementById('handover_team_id').value = teamId;
            document.getElementById('handover_team_workers').value = JSON.stringify(workerIds);
        }

        // Reset Team Selection
        function resetHandoverTeamSelection() {
            document.querySelectorAll('.team-card-small').forEach(card => {
                card.classList.remove('selected');
            });
            document.getElementById('handover_team_id').value = '';
            document.getElementById('handover_team_workers').value = '';
        }

        // Close modal when clicking outside
        document.getElementById('suspendModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeSuspendModal();
            }
        });

        document.getElementById('handoverModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeHandoverModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSuspendModal();
                closeHandoverModal();
            }
        });
    </script>

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
                    <button type="submit" class="btn btn-warning">{{ __('shifts-workers.suspend_shift') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Handover Modal -->
    <div id="handoverModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 700px;">
            <div class="modal-header">
                <h3>{{ __('shifts-workers.shift_handover') }}</h3>
                <button class="close-btn" onclick="closeHandoverModal()">×</button>
            </div>
            <form id="handoverForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('shifts-workers.shift_number') }}</label>
                        <input type="text" class="form-control" id="handover_shift_code" readonly style="background: #f5f5f5;">
                        <input type="hidden" id="handover_shift_id" name="shift_id">
                    </div>

                    <!-- اختيار المجموعة أو العامل الواحد -->
                    <div class="form-group">
                        <label style="font-weight: 600; margin-bottom: 15px; display: block;">
                            <i class="feather icon-users" style="margin-right: 8px;"></i>
                            اختر طريقة النقل:
                        </label>

                        <div class="selection-tabs" style="display: flex; gap: 10px; margin-bottom: 20px;">
                            <button type="button" class="tab-btn active" onclick="switchHandoverTab('team')" id="team-tab">
                                <i class="feather icon-users"></i> نقل المجموعة
                            </button>
                            <button type="button" class="tab-btn" onclick="switchHandoverTab('individual')" id="individual-tab">
                                <i class="feather icon-user"></i> نقل عامل واحد
                            </button>
                        </div>

                        <!-- Team Selection -->
                        <div id="team-selection" class="tab-content active">
                            <div class="teams-grid-small">
                                @forelse($teams as $team)
                                    <div class="team-card-small" onclick="selectHandoverTeam({{ $team['id'] }}, {{ json_encode($team['worker_ids']) }})">
                                        <div class="team-header-small">
                                            <div class="team-icon-small">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="9" cy="7" r="4"></circle>
                                                </svg>
                                            </div>
                                            <span class="team-name-small">{{ $team['name'] }}</span>
                                        </div>
                                        <div class="team-info-small">
                                            <small>المسؤول: {{ $team['manager_name'] }}</small>
                                            <small>{{ $team['workers_count'] }} عامل</small>
                                        </div>
                                    </div>
                                @empty
                                    <p style="color: #999;">لا توجد مجموعات متاحة</p>
                                @endforelse
                            </div>
                            <input type="hidden" id="handover_team_id" name="team_id">
                            <input type="hidden" id="handover_team_workers" name="team_worker_ids">
                        </div>

                        <!-- Individual Selection -->
                        <div id="individual-selection" class="tab-content" style="display: none;">
                            <select id="handover_to_user_id" name="to_user_id" class="form-control">
                                <option value="">-- اختر عامل --</option>
                                @foreach($shifts as $shift)
                                    @if($shift->supervisor)
                                        <option value="{{ $shift->supervisor->id }}">{{ $shift->supervisor->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label for="handover_stage_number">{{ __('shifts-workers.stage_number') }}</label>
                        <select id="handover_stage_number" name="stage_number" class="form-control" required>
                            <option value="">{{ __('shifts-workers.select_stage') }}</option>
                            <option value="1">{{ __('shifts-workers.stage_first') }}</option>
                            <option value="2">{{ __('shifts-workers.stage_second') }}</option>
                            <option value="3">{{ __('shifts-workers.stage_third') }}</option>
                            <option value="4">{{ __('shifts-workers.stage_fourth') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="handover_notes">{{ __('shifts-workers.handover_notes') }}</label>
                        <textarea
                            id="handover_notes"
                            name="notes"
                            class="form-control"
                            rows="3"
                            placeholder="{{ __('shifts-workers.handover_notes_placeholder') }}">
                        </textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeHandoverModal()">{{ __('shifts-workers.cancel') }}</button>
                    <button type="submit" class="btn btn-info" style="background: #17a2b8; color: white;">{{ __('shifts-workers.handover_confirm') }}</button>
                </div>
            </form>
        </div>
    </div>
        </div>
    </div>

    <style>
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
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
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

        .um-btn-warning {
            color: #f39c12 !important;
        }

        .um-btn-warning:hover {
            background: #f39c1220 !important;
        }

        .um-btn-info {
            color: #17a2b8 !important;
        }

        .um-btn-info:hover {
            background: #17a2b820 !important;
        }

        /* Mobile View Styles */
        @media (max-width: 768px) {
            .um-table-responsive.um-desktop-view {
                display: none;
            }

            .um-mobile-view {
                display: block;
            }

            .um-category-card {
                background: white;
                border-radius: 8px;
                margin-bottom: 15px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                border: 1px solid #e0e0e0;
            }

            .um-category-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 15px;
                border-bottom: 1px solid #f0f0f0;
                background: #f9f9f9;
            }

            .um-category-info {
                display: flex;
                gap: 10px;
                align-items: center;
                flex: 1;
            }

            .um-category-icon {
                min-width: 40px;
            }

            .um-category-name {
                font-size: 14px;
                font-weight: 600;
                margin: 0;
                color: #333;
            }

            .um-category-id {
                font-size: 12px;
                color: #999;
                display: block;
                margin-top: 3px;
            }

            .um-category-card-body {
                padding: 12px 15px;
            }

            .um-info-row {
                display: flex;
                justify-content: space-between;
                padding: 8px 0;
                border-bottom: 1px solid #f5f5f5;
                font-size: 13px;
            }

            .um-info-row:last-child {
                border-bottom: none;
            }

            .um-info-label {
                font-weight: 500;
                color: #666;
            }

            .um-info-value {
                color: #333;
                font-weight: 500;
                text-align: right;
            }

            .um-category-card-footer {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                padding: 10px 15px;
                background: #f9f9f9;
                border-top: 1px solid #f0f0f0;
            }

            .um-btn-sm {
                padding: 6px 10px !important;
                font-size: 12px !important;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                flex: 1;
                min-width: 100px;
                justify-content: center;
            }

            .um-badge {
                padding: 4px 8px;
                font-size: 11px;
                border-radius: 4px;
                font-weight: 500;
            }

            .um-badge-success {
                background: #d4edda;
                color: #155724;
            }

            .um-badge-info {
                background: #d1ecf1;
                color: #0c5460;
            }

            .um-badge-secondary {
                background: #e2e3e5;
                color: #383d41;
            }

            .um-btn {
                border-radius: 4px;
                padding: 8px 12px;
                font-size: 13px;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .um-btn-primary {
                background: #3b82f6;
                color: white;
            }

            .um-btn-secondary {
                background: #6b7280;
                color: white;
            }

            .um-btn-success {
                background: #10b981;
                color: white;
            }

            .um-btn-info {
                background: #0891b2;
                color: white;
            }

            .um-mobile-view {
                display: none;
            }
        }

        @media (min-width: 769px) {
            .um-mobile-view {
                display: none;
            }
        }

        /* Handover Modal Styles */
        .selection-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-btn {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #666;
        }

        .tab-btn:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            background: #f0f9ff;
        }

        .tab-btn.active {
            border-color: #3b82f6;
            background: #3b82f6;
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .teams-grid-small {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .team-card-small {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .team-card-small:hover {
            border-color: #3b82f6;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
            transform: translateY(-2px);
        }

        .team-card-small.selected {
            border-color: #10b981;
            background: #f0fdf4;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.15);
        }

        .team-header-small {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .team-icon-small {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .team-icon-small svg {
            width: 18px;
            height: 18px;
        }

        .team-name-small {
            font-weight: 600;
            color: #333;
            font-size: 14px;
            flex: 1;
        }

        .team-info-small {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 12px;
        }

        .team-info-small small {
            color: #666;
        }
    </style>

@endsection
