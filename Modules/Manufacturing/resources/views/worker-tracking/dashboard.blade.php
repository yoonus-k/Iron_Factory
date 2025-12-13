@extends('master')

@section('title', __('worker-tracking.dashboard_title'))

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ __('worker-tracking.dashboard_title') }}</h2>
            <p class="text-muted mb-0">{{ __('worker-tracking.dashboard_subtitle') }}</p>
        </div>
        <div>
            <span class="badge bg-primary p-2">
                <i class="fas fa-clock me-1"></i>
                {{ now()->format('Y-m-d H:i:s') }}
            </span>
        </div>
    </div>

    <!-- Overview Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-users text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">{{ __('worker-tracking.active_workers') }}</h6>
                            <h3 class="mb-0" id="activeWorkersCount">--</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-cogs text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">{{ __('worker-tracking.stages_in_progress') }}</h6>
                            <h3 class="mb-0" id="stagesInProgressCount">--</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-clock text-info fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">{{ __('worker-tracking.avg_duration_today') }}</h6>
                            <h3 class="mb-0" id="avgDurationToday">--</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-user-clock text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">{{ __('worker-tracking.total_work_hours_today') }}</h6>
                            <h3 class="mb-0" id="totalWorkHoursToday">--</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Workers by Stage -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0">{{ __('worker-tracking.active_workers_by_stage') }}</h5>
                </div>
                <div class="card-body">
                    <div id="stageWorkersChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0">{{ __('worker-tracking.work_distribution_today') }}</h5>
                </div>
                <div class="card-body">
                    <div id="workDistributionChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Currently Active Workers -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('worker-tracking.currently_active_workers') }}</h5>
                <button class="btn btn-sm btn-outline-primary" onclick="refreshDashboard()">
                    <i class="fas fa-sync-alt me-1"></i>
                    {{ __('worker-tracking.refresh') }}
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="activeWorkersTable">
                    <thead>
                        <tr>
                            <th>{{ __('worker-tracking.worker_name') }}</th>
                            <th>{{ __('worker-tracking.stage') }}</th>
                            <th>{{ __('worker-tracking.stage_barcode') }}</th>
                            <th>{{ __('worker-tracking.started_at') }}</th>
                            <th>{{ __('worker-tracking.duration') }}</th>
                            <th>{{ __('worker-tracking.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                {{ __('worker-tracking.loading') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Completed Sessions -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-3">
            <h5 class="mb-0">{{ __('worker-tracking.recent_completed_sessions') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="completedSessionsTable">
                    <thead>
                        <tr>
                            <th>{{ __('worker-tracking.worker_name') }}</th>
                            <th>{{ __('worker-tracking.stage') }}</th>
                            <th>{{ __('worker-tracking.started_at') }}</th>
                            <th>{{ __('worker-tracking.ended_at') }}</th>
                            <th>{{ __('worker-tracking.duration') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                {{ __('worker-tracking.loading') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    loadDashboardData();

    // Auto-refresh every 30 seconds
    setInterval(loadDashboardData, 30000);
});

function loadDashboardData() {
    // Load overview statistics
    $.ajax({
        url: '{{ route("worker-tracking.dashboard") }}',
        method: 'GET',
        data: { ajax: 1 },
        success: function(data) {
            updateOverviewCards(data);
            updateActiveWorkersTable(data.activeWorkers);
            updateCompletedSessionsTable(data.recentCompleted);
        },
        error: function(xhr) {
            console.error('Error loading dashboard data:', xhr);
        }
    });
}

function updateOverviewCards(data) {
    $('#activeWorkersCount').text(data.stats.activeWorkers || 0);
    $('#stagesInProgressCount').text(data.stats.stagesInProgress || 0);
    $('#avgDurationToday').text(formatDuration(data.stats.avgDurationToday || 0));
    $('#totalWorkHoursToday').text(formatHours(data.stats.totalWorkMinutesToday || 0));
}

function updateActiveWorkersTable(workers) {
    const tbody = $('#activeWorkersTable tbody');
    tbody.empty();

    if (!workers || workers.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="6" class="text-center text-muted">
                    {{ __('worker-tracking.no_active_workers') }}
                </td>
            </tr>
        `);
        return;
    }

    workers.forEach(function(worker) {
        const stageName = getStageDisplayName(worker.stage_type);
        const duration = calculateDuration(worker.started_at);

        tbody.append(`
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <strong>${worker.worker_name}</strong>
                    </div>
                </td>
                <td>
                    <span class="badge bg-info">${stageName}</span>
                </td>
                <td>
                    <code>${worker.barcode || '-'}</code>
                </td>
                <td>${formatDateTime(worker.started_at)}</td>
                <td>
                    <span class="badge bg-warning">${duration}</span>
                </td>
                <td>
                    <a href="{{ url('worker-tracking/stage') }}/${worker.stage_type}/${worker.stage_record_id}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>
                        {{ __('worker-tracking.view') }}
                    </a>
                </td>
            </tr>
        `);
    });
}

function updateCompletedSessionsTable(sessions) {
    const tbody = $('#completedSessionsTable tbody');
    tbody.empty();

    if (!sessions || sessions.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="5" class="text-center text-muted">
                    {{ __('worker-tracking.no_completed_sessions') }}
                </td>
            </tr>
        `);
        return;
    }

    sessions.forEach(function(session) {
        const stageName = getStageDisplayName(session.stage_type);

        tbody.append(`
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2">
                            <i class="fas fa-user text-success"></i>
                        </div>
                        ${session.worker_name}
                    </div>
                </td>
                <td>
                    <span class="badge bg-secondary">${stageName}</span>
                </td>
                <td>${formatDateTime(session.started_at)}</td>
                <td>${formatDateTime(session.ended_at)}</td>
                <td>
                    <span class="badge bg-success">${formatDuration(session.duration_minutes)}</span>
                </td>
            </tr>
        `);
    });
}

function getStageDisplayName(stageType) {
    const stages = {
        'stage1': '{{ __("worker-tracking.stage1") }}',
        'stage2': '{{ __("worker-tracking.stage2") }}',
        'stage3': '{{ __("worker-tracking.stage3") }}',
        'stage4': '{{ __("worker-tracking.stage4") }}'
    };
    return stages[stageType] || stageType;
}

function formatDuration(minutes) {
    if (!minutes || minutes < 0) return '0 {{ __("worker-tracking.minutes") }}';

    const hours = Math.floor(minutes / 60);
    const mins = Math.floor(minutes % 60);

    if (hours > 0) {
        return `${hours} {{ __("worker-tracking.hours") }} ${mins} {{ __("worker-tracking.minutes") }}`;
    }
    return `${mins} {{ __("worker-tracking.minutes") }}`;
}

function formatHours(minutes) {
    if (!minutes || minutes < 0) return '0 {{ __("worker-tracking.hours") }}';

    const hours = (minutes / 60).toFixed(1);
    return `${hours} {{ __("worker-tracking.hours") }}`;
}

function formatDateTime(datetime) {
    if (!datetime) return '-';
    const date = new Date(datetime);
    return date.toLocaleString('ar-EG', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function calculateDuration(startTime) {
    const start = new Date(startTime);
    const now = new Date();
    const diffMinutes = Math.floor((now - start) / 1000 / 60);
    return formatDuration(diffMinutes);
}

function refreshDashboard() {
    loadDashboardData();
    toastr.success('{{ __("worker-tracking.dashboard_refreshed") }}');
}
</script>
@endpush
@endsection
