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

    <!-- Current Shift Card -->
    @php
    $currentShift = \App\Models\ShiftAssignment::where('status', 'active')->latest('created_at')->first();
    @endphp

    @if($currentShift)
    <div class="card border-info mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-briefcase me-2"></i>
                الوردية الحالية والعمال
            </h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="bg-light p-3 rounded">
                        <h6 class="text-muted mb-2">رقم الوردية</h6>
                        <h4 class="mb-0 text-info">{{ $currentShift->shift_code }}</h4>
                        <small class="text-muted d-block mt-1">
                            {{ $currentShift->shift_type == 'morning' ? '✅ الفترة الأولى' : '🌙 الفترة الثانية' }}
                        </small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light p-3 rounded">
                        <h6 class="text-muted mb-2">المسؤول</h6>
                        <h5 class="mb-0" style="color: #9b59b6;">
                            <i class="fas fa-user-tie me-2"></i>
                            {{ $currentShift->supervisor?->name ?? 'لم يحدد' }}
                        </h5>
                        @if($currentShift->supervisor?->email)
                        <small class="text-muted d-block mt-1">{{ $currentShift->supervisor->email }}</small>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light p-3 rounded">
                        <h6 class="text-muted mb-2">التاريخ</h6>
                        <h5 class="mb-0">{{ \Carbon\Carbon::parse($currentShift->shift_date)->format('Y-m-d') }}</h5>
                        <small class="text-muted d-block mt-1">{{ \Carbon\Carbon::parse($currentShift->shift_date)->diffForHumans() }}</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light p-3 rounded">
                        <h6 class="text-muted mb-2">عدد العمال</h6>
                        <h4 class="mb-0 text-success">{{ count($currentShift->worker_ids ?? []) }}</h4>
                        <small class="text-muted d-block mt-1">عامل مسجل</small>
                    </div>
                </div>
            </div>

            <!-- Workers in Current Shift -->
            @if(count($currentShift->worker_ids ?? []) > 0)
            <div class="pt-3 border-top">
                <h6 class="mb-3 text-primary">
                    <i class="fas fa-users me-2"></i>
                    👷 قائمة العمال في هذه الوردية:
                </h6>
                <div class="row">
                    @php
                    $shiftWorkers = \App\Models\Worker::whereIn('id', $currentShift->worker_ids ?? [])->get();
                    @endphp
                    @foreach($shiftWorkers as $worker)
                    <div class="col-md-4 col-lg-3 mb-3">
                        <div class="card border-success h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-user text-success fa-lg"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <h6 class="mb-0 fw-bold">{{ $worker->name }}</h6>
                                        <small class="text-success fw-bold">{{ $worker->worker_code }}</small>
                                    </div>
                                </div>
                                <div class="mt-2 pt-2 border-top">
                                    <small class="d-block text-muted mb-2">
                                        <strong>الوظيفة:</strong> {{ $worker->position ?? 'عام' }}
                                    </small>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <span class="badge bg-info">{{ $currentShift->shift_code }}</span>
                                        <span class="badge bg-success">{{ $currentShift->shift_type == 'morning' ? 'صباحي' : 'مسائي' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Previous Shift Workers -->
            <div class="pt-4 border-top mt-4">
                <h6 class="mb-3 text-warning">
                    <i class="fas fa-history me-2"></i>
                    👥 العمال من الوردية السابقة:
                </h6>
                <div id="previousShiftWorkersContainer" class="row">
                    <div class="col-12">
                        <p class="text-muted text-center">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                            جاري تحميل البيانات...
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Previous & Current Shifts Comparison Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h5 class="mb-0">
                <i class="fas fa-exchange-alt me-2"></i>
                مقارنة الورديات - السابقة والحالية
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Previous Shift Card -->
                <div class="col-md-6 mb-4">
                    <div class="card border-left border-warning h-100">
                        <div class="card-header bg-warning bg-opacity-10">
                            <h6 class="mb-0 text-warning">
                                <i class="fas fa-history me-2"></i>
                                الوردية السابقة
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="prevShiftInfoDash" style="min-height: 150px;">
                                <div class="text-center">
                                    <i class="fas fa-spinner fa-spin text-warning me-2"></i>
                                    <span class="text-muted">جاري التحميل...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Previous Shift Workers -->
                    <div class="card mt-3 border-left border-warning">
                        <div class="card-header bg-warning bg-opacity-10">
                            <h6 class="mb-0 text-warning">
                                <i class="fas fa-users me-2"></i>
                                عمال الوردية السابقة
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="prevWorkersDisplayDash" style="min-height: 200px;">
                                <div class="row" id="prevWorkersGridDash">
                                    <div class="col-12">
                                        <div class="text-center">
                                            <i class="fas fa-spinner fa-spin text-warning me-2"></i>
                                            <span class="text-muted">جاري التحميل...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Shift Card -->
                <div class="col-md-6 mb-4">
                    <div class="card border-left border-success h-100">
                        <div class="card-header bg-success bg-opacity-10">
                            <h6 class="mb-0 text-success">
                                <i class="fas fa-briefcase me-2"></i>
                                الوردية الحالية
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="currShiftInfoDash" style="min-height: 150px;">
                                <div class="text-center">
                                    <i class="fas fa-spinner fa-spin text-success me-2"></i>
                                    <span class="text-muted">جاري التحميل...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Current Shift Workers -->
                    <div class="card mt-3 border-left border-success">
                        <div class="card-header bg-success bg-opacity-10">
                            <h6 class="mb-0 text-success">
                                <i class="fas fa-users me-2"></i>
                                عمال الوردية الحالية
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="currWorkersDisplayDash" style="min-height: 200px;">
                                <div class="row" id="currWorkersGridDash">
                                    <div class="col-12">
                                        <div class="text-center">
                                            <i class="fas fa-spinner fa-spin text-success me-2"></i>
                                            <span class="text-muted">جاري التحميل...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    // تحديث بيانات الوردية والعمال
    loadCurrentShiftWorkers();
    toastr.success('{{ __("worker-tracking.dashboard_refreshed") }}');
}

// تحميل بيانات الوردية والعمال الحاليين
function loadCurrentShiftWorkers() {
    fetch('{{ route("worker-tracking.current-shift") }}')
        .then(response => response.json())
        .then(data => {
            console.log('✅ بيانات الوردية الحالية:', data);
            if(data.shift) {
                console.log('المسؤول:', data.shift.supervisor?.name);
                console.log('عدد العمال:', data.shift.worker_ids?.length);
            }
        })
        .catch(error => console.error('❌ خطأ:', error));
}

// تحميل بيانات الوردية السابقة والعمال السابقين
function loadPreviousShiftWorkers() {
    fetch('{{ route("worker-tracking.previous-shift-workers") }}')
        .then(response => response.json())
        .then(data => {
            console.log('✅ بيانات الوردية السابقة:', data);
            if(data.success && data.workers && data.workers.length > 0) {
                displayPreviousShiftWorkers(data.workers, data.shift);
            } else {
                displayNoPreviousWorkers();
            }
        })
        .catch(error => {
            console.error('❌ خطأ في تحميل الوردية السابقة:', error);
            displayNoPreviousWorkers();
        });
}

function displayPreviousShiftWorkers(workers, shift) {
    const container = document.getElementById('previousShiftWorkersContainer');

    if (!workers || workers.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <p class="text-muted text-center">لا توجد بيانات عمال سابقين</p>
            </div>
        `;
        return;
    }

    let html = '';
    workers.forEach(worker => {
        html += `
            <div class="col-md-4 col-lg-3 mb-3">
                <div class="card border-warning h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-user-check text-warning fa-lg"></i>
                            </div>
                            <div style="flex: 1;">
                                <h6 class="mb-0 fw-bold">${worker.name}</h6>
                                <small class="text-warning fw-bold">${worker.worker_code}</small>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top">
                            <small class="d-block text-muted mb-2">
                                <strong>الوظيفة:</strong> ${worker.position}
                            </small>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-warning text-dark">${shift.shift_code}</span>
                                <span class="badge bg-secondary">السابقة</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function displayNoPreviousWorkers() {
    const container = document.getElementById('previousShiftWorkersContainer');
    container.innerHTML = `
        <div class="col-12">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                لا توجد وردية سابقة أو لم يتم العثور على عمال
            </div>
        </div>
    `;
}

// تحديث بيانات الوردية عند تحميل الصفحة
$(document).ready(function() {
    loadCurrentShiftWorkers();
    loadPreviousShiftWorkers();
    loadShiftsComparisonDashboard();
    // تحديث البيانات كل 30 ثانية
    setInterval(loadShiftsComparisonDashboard, 30000);
});

// ====== تحميل بيانات الورديات للـ Dashboard ======
function loadShiftsComparisonDashboard() {
    loadCurrentShiftDataDash();
    loadPreviousShiftDataDash();
}

function loadCurrentShiftDataDash() {
    fetch('{{ route("worker-tracking.current-shift") }}')
        .then(response => response.json())
        .then(data => {
            if(data.success && data.shift) {
                displayCurrentShiftInfoDash(data.shift);
                displayCurrentWorkersDash(data.shift.workers || []);
            } else {
                displayNoCurrentShiftDash();
            }
        })
        .catch(error => {
            console.error('❌ خطأ في تحميل الوردية الحالية:', error);
            displayNoCurrentShiftDash();
        });
}

function loadPreviousShiftDataDash() {
    fetch('{{ route("worker-tracking.previous-shift-workers") }}')
        .then(response => response.json())
        .then(data => {
            if(data.success && data.shift) {
                displayPreviousShiftInfoDash(data.shift);
                displayPreviousWorkersDash(data.workers || []);
            } else {
                displayNoPreviousShiftDash();
            }
        })
        .catch(error => {
            console.error('❌ خطأ في تحميل الوردية السابقة:', error);
            displayNoPreviousShiftDash();
        });
}

function displayCurrentShiftInfoDash(shift) {
    const container = document.getElementById('currShiftInfoDash');

    const shiftType = shift.shift_type === 'morning' ? '✅ الفترة الأولى' : '🌙 الفترة الثانية';

    container.innerHTML = `
        <div class="row">
            <div class="col-6 mb-3">
                <div class="bg-success bg-opacity-10 p-3 rounded">
                    <h6 class="text-muted mb-1">رقم الوردية</h6>
                    <h5 class="mb-0 text-success fw-bold">${shift.shift_code}</h5>
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="bg-success bg-opacity-10 p-3 rounded">
                    <h6 class="text-muted mb-1">الفترة</h6>
                    <h5 class="mb-0 text-success">${shiftType}</h5>
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="bg-success bg-opacity-10 p-3 rounded">
                    <h6 class="text-muted mb-1">المسؤول</h6>
                    <h5 class="mb-0 text-success" style="color: #9b59b6 !important;">
                        <i class="fas fa-user-tie me-1"></i>
                        ${shift.supervisor ? shift.supervisor.name : 'لم يحدد'}
                    </h5>
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="bg-success bg-opacity-10 p-3 rounded">
                    <h6 class="text-muted mb-1">عدد العمال</h6>
                    <h5 class="mb-0 text-success">${shift.workers_count || 0}</h5>
                </div>
            </div>
        </div>
    `;
}

function displayPreviousShiftInfoDash(shift) {
    const container = document.getElementById('prevShiftInfoDash');

    const shiftType = shift.shift_type === 'morning' ? '✅ الفترة الأولى' : '🌙 الفترة الثانية';
    const status = shift.status === 'completed' ? '✔️ مكتملة' : '⏸️ موقوفة';

    container.innerHTML = `
        <div class="row">
            <div class="col-6 mb-3">
                <div class="bg-warning bg-opacity-10 p-3 rounded">
                    <h6 class="text-muted mb-1">رقم الوردية</h6>
                    <h5 class="mb-0 text-warning fw-bold">${shift.shift_code}</h5>
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="bg-warning bg-opacity-10 p-3 rounded">
                    <h6 class="text-muted mb-1">الفترة</h6>
                    <h5 class="mb-0 text-warning">${shiftType}</h5>
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="bg-warning bg-opacity-10 p-3 rounded">
                    <h6 class="text-muted mb-1">المسؤول</h6>
                    <h5 class="mb-0 text-warning" style="color: #9b59b6 !important;">
                        <i class="fas fa-user-tie me-1"></i>
                        ${shift.supervisor ? shift.supervisor.name : 'لم يحدد'}
                    </h5>
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="bg-warning bg-opacity-10 p-3 rounded">
                    <h6 class="text-muted mb-1">الحالة</h6>
                    <h5 class="mb-0 text-warning">${status}</h5>
                </div>
            </div>
        </div>
    `;
}

function displayCurrentWorkersDash(workers) {
    const container = document.getElementById('currWorkersGridDash');

    if (!workers || workers.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    لا توجد عمال في الوردية الحالية
                </div>
            </div>
        `;
        return;
    }

    let html = '';
    workers.forEach(worker => {
        html += `
            <div class="col-md-12 mb-3">
                <div class="card border-success h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-user text-success fa-lg"></i>
                            </div>
                            <div style="flex: 1;">
                                <h6 class="mb-0 fw-bold text-success">${worker.name}</h6>
                                <small class="text-muted d-block">${worker.worker_code}</small>
                                <small class="d-block text-success mt-1">
                                    <strong>الوظيفة:</strong> ${worker.position || 'غير محددة'}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function displayPreviousWorkersDash(workers) {
    const container = document.getElementById('prevWorkersGridDash');

    if (!workers || workers.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    لا توجد عمال من الوردية السابقة
                </div>
            </div>
        `;
        return;
    }

    let html = '';
    workers.forEach(worker => {
        html += `
            <div class="col-md-12 mb-3">
                <div class="card border-warning h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-user text-warning fa-lg"></i>
                            </div>
                            <div style="flex: 1;">
                                <h6 class="mb-0 fw-bold text-warning">${worker.name}</h6>
                                <small class="text-muted d-block">${worker.worker_code}</small>
                                <small class="d-block text-warning mt-1">
                                    <strong>الوظيفة:</strong> ${worker.position || 'غير محددة'}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function displayNoCurrentShiftDash() {
    const container = document.getElementById('currShiftInfoDash');
    container.innerHTML = `
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            لا توجد وردية نشطة حالياً
        </div>
    `;

    document.getElementById('currWorkersGridDash').innerHTML = `
        <div class="col-12">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                لا توجد عمال في الوردية الحالية
            </div>
        </div>
    `;
}

function displayNoPreviousShiftDash() {
    const container = document.getElementById('prevShiftInfoDash');
    container.innerHTML = `
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            لا توجد وردية سابقة
        </div>
    `;

    document.getElementById('prevWorkersGridDash').innerHTML = `
        <div class="col-12">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                لا توجد عمال من الوردية السابقة
            </div>
        </div>
    `;
}
</script>
@endpush
@endsection
