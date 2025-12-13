@extends('master')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ __('worker-tracking.stage_history') }}</h2>
            <p class="text-muted mb-0">
                {{ __('worker-tracking.' . $stageType) }}
            </p>
        </div>
        <a href="{{ route('worker-tracking.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right me-2"></i>
            {{ __('worker-tracking.dashboard') }}
        </a>
    </div>

    <!-- Current Shift Information -->
    @php
    $currentShift = \App\Models\ShiftAssignment::where('status', 'active')
        ->where('stage_number', 1)
        ->latest('created_at')
        ->first();
    @endphp

    @if($currentShift)
    <div class="card border-info mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-briefcase me-2"></i>
                الوردية الحالية
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">رقم الوردية</h6>
                    <h4 class="mb-0">{{ $currentShift->shift_code }}</h4>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">المسؤول</h6>
                    <h5 class="mb-0" style="color: #9b59b6;">
                        <i class="fas fa-user me-2"></i>
                        {{ $currentShift->supervisor?->name ?? 'لم يحدد' }}
                    </h5>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">فترة العمل</h6>
                    <h5 class="mb-0">
                        {{ $currentShift->shift_type == 'morning' ? 'الفترة الأولى' : 'الفترة الثانية' }}
                    </h5>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">عدد العمال</h6>
                    <h4 class="mb-0 text-primary">{{ count($currentShift->worker_ids ?? []) }}</h4>
                </div>
            </div>

            <!-- Workers in Current Shift -->
            @if(count($currentShift->worker_ids ?? []) > 0)
            <div class="mt-4 pt-3 border-top">
                <h6 class="mb-3 text-primary">👷 العمال في هذه الوردية:</h6>
                <div class="row">
                    @php
                    $shiftWorkers = \App\Models\Worker::whereIn('id', $currentShift->worker_ids ?? [])->get();
                    @endphp
                    @foreach($shiftWorkers as $worker)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user text-success fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $worker->name }}</h6>
                                        <small class="text-muted">{{ $worker->worker_code }}</small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="d-block text-muted mb-1"><strong>الوظيفة:</strong> {{ $worker->position ?? 'غير محددة' }}</small>
                                    <span class="badge bg-info">الوردية: {{ $currentShift->shift_code }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Current Worker Card -->
    @if($currentWorker)
    <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-user-clock me-2"></i>
                {{ __('worker-tracking.current_worker') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h6 class="text-muted mb-1">{{ __('worker-tracking.worker_name') }}</h6>
                    <h4 class="mb-0">{{ $currentWorker->worker_name }}</h4>
                    <span class="badge bg-info mt-2">
                        {{ __('worker-tracking.' . $currentWorker->worker_type . '_worker') }}
                    </span>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">{{ __('worker-tracking.started_at') }}</h6>
                    <p class="mb-0">{{ $currentWorker->started_at->format('Y-m-d H:i') }}</p>
                    <small class="text-muted">{{ $currentWorker->started_at->diffForHumans() }}</small>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">{{ __('worker-tracking.work_duration') }}</h6>
                    <h5 class="mb-0 text-primary">{{ $currentWorker->formatted_duration }}</h5>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-warning btn-sm"
                            onclick="showTransferModal({{ $currentWorker->id }})">
                        <i class="fas fa-exchange-alt me-1"></i>
                        {{ __('worker-tracking.transfer_work') }}
                    </button>
                    <button type="button" class="btn btn-danger btn-sm mt-2"
                            onclick="showEndSessionModal({{ $currentWorker->id }})">
                        <i class="fas fa-stop-circle me-1"></i>
                        {{ __('worker-tracking.end_session') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        {{ __('worker-tracking.no_current_worker') }}
    </div>
    @endif

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('worker-tracking.total_sessions') }}</h6>
                    <h3 class="mb-0">{{ $statistics['total_sessions'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('worker-tracking.total_workers') }}</h6>
                    <h3 class="mb-0">{{ $statistics['total_workers'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('worker-tracking.total_hours') }}</h6>
                    <h3 class="mb-0">{{ number_format($statistics['total_hours'], 1) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('worker-tracking.average_session_time') }}</h6>
                    <h3 class="mb-0">{{ number_format($statistics['average_session_minutes'] ?? 0, 0) }} {{ __('worker-tracking.minutes') }}</h3>
                </div>
            </div>
        </div>
    </div>

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
                            <div id="previousShiftInfo" style="min-height: 150px;">
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
                            <div id="previousWorkersDisplay" style="min-height: 200px;">
                                <div class="row" id="previousWorkersGrid">
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
                            <div id="currentShiftInfo" style="min-height: 150px;">
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
                            <div id="currentWorkersDisplay" style="min-height: 200px;">
                                <div class="row" id="currentWorkersGrid">
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

    <!-- History Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-history me-2"></i>
                {{ __('worker-tracking.previous_workers') }}
            </h5>
        </div>
        <div class="card-body">
            <!-- العمال السابقين من الوردية السابقة -->
            <div id="previousShiftWorkersSection" class="mb-4">
                <h6 class="mb-3 text-info">
                    <i class="fas fa-users me-2"></i>
                    👥 العمال من الوردية السابقة:
                </h6>
                <div id="previousWorkersContainer" class="row">
                    <div class="col-12">
                        <p class="text-muted text-center">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                            جاري تحميل العمال السابقين...
                        </p>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- جدول تاريخ العمل -->
            <h6 class="mb-3">
                <i class="fas fa-table me-2"></i>
                سجل العمل السابق:
            </h6>

            @if($history->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('worker-tracking.worker_name') }}</th>
                            <th>{{ __('worker-tracking.worker_type') }}</th>
                            <th>{{ __('worker-tracking.started_at') }}</th>
                            <th>{{ __('worker-tracking.ended_at') }}</th>
                            <th>{{ __('worker-tracking.work_duration') }}</th>
                            <th>{{ __('worker-tracking.status_before') }}</th>
                            <th>{{ __('worker-tracking.status_after') }}</th>
                            <th>{{ __('worker-tracking.assigned_by') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $record)
                        <tr class="{{ $record->is_active ? 'table-primary' : '' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $record->worker_name }}</strong>
                                @if($record->is_active)
                                <span class="badge bg-success ms-2">{{ __('worker-tracking.still_working') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $record->worker_type === 'team' ? 'info' : 'secondary' }}">
                                    {{ __('worker-tracking.' . $record->worker_type . '_worker') }}
                                </span>
                            </td>
                            <td>
                                {{ $record->started_at->format('Y-m-d H:i') }}
                                <br>
                                <small class="text-muted">{{ $record->started_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if($record->ended_at)
                                {{ $record->ended_at->format('Y-m-d H:i') }}
                                <br>
                                <small class="text-muted">{{ $record->ended_at->diffForHumans() }}</small>
                                @else
                                <span class="text-primary">{{ __('worker-tracking.still_working') }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $record->formatted_duration }}</strong>
                            </td>
                            <td>
                                @if($record->status_before)
                                <span class="badge bg-secondary">{{ $record->status_before }}</span>
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                @if($record->status_after)
                                <span class="badge bg-success">{{ $record->status_after }}</span>
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                @if($record->assignedBy)
                                {{ $record->assignedBy->name }}
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        @if($record->notes)
                        <tr>
                            <td colspan="9" class="bg-light">
                                <small><strong>{{ __('worker-tracking.notes') }}:</strong> {{ $record->notes }}</small>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info mb-0">
                {{ __('worker-tracking.no_history_found') }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// تحديث بيانات تتبع العمال من الوردية
document.addEventListener('DOMContentLoaded', function() {
    loadShiftWorkersData();
    loadPreviousShiftWorkers();
    // تحديث البيانات كل 30 ثانية
    setInterval(loadShiftWorkersData, 30000);
    setInterval(loadPreviousShiftWorkers, 30000);
});

function loadShiftWorkersData() {
    fetch('{{ route("worker-tracking.current-shift") }}')
        .then(response => response.json())
        .then(data => {
            console.log('✅ تم تحميل بيانات الوردية والعمال:', data);
            if(data.shift) {
                updateShiftInfo(data.shift);
            }
        })
        .catch(error => {
            console.error('❌ خطأ في تحميل بيانات الوردية:', error);
        });
}

function loadPreviousShiftWorkers() {
    fetch('{{ route("worker-tracking.previous-shift-workers") }}')
        .then(response => response.json())
        .then(data => {
            console.log('✅ تم تحميل العمال السابقين:', data);
            if(data.success && data.workers && data.workers.length > 0) {
                displayPreviousWorkers(data.workers, data.shift);
            } else {
                displayNoPreviousWorkers();
            }
        })
        .catch(error => {
            console.error('❌ خطأ في تحميل العمال السابقين:', error);
            displayNoPreviousWorkers();
        });
}

function displayPreviousWorkers(workers, shift) {
    const container = document.getElementById('previousWorkersContainer');

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
            <div class="col-md-6 col-lg-4 mb-3">
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
    const container = document.getElementById('previousWorkersContainer');
    container.innerHTML = `
        <div class="col-12">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                لا توجد وردية سابقة أو لم يتم العثور على عمال من الوردية السابقة
            </div>
        </div>
    `;
}

function updateShiftInfo(shift) {
    // تحديث المعلومات الديناميكية هنا إذا لزم الأمر
    console.log('وردية:', shift.shift_code, 'مسؤول:', shift.supervisor?.name, 'عمال:', shift.worker_ids?.length);
}

// ====== تحميل بيانات الورديات السابقة والحالية ======
document.addEventListener('DOMContentLoaded', function() {
    loadShiftsComparison();
    // تحديث البيانات كل 30 ثانية
    setInterval(loadShiftsComparison, 30000);
});

function loadShiftsComparison() {
    // تحميل الوردية الحالية وعمالها
    loadCurrentShiftData();
    // تحميل الوردية السابقة وعمالها
    loadPreviousShiftData();
}

function loadCurrentShiftData() {
    fetch('{{ route("worker-tracking.current-shift") }}')
        .then(response => response.json())
        .then(data => {
            console.log('✅ بيانات الوردية الحالية:', data);
            if(data.success && data.shift) {
                displayCurrentShiftInfo(data.shift);
                displayCurrentWorkers(data.shift.workers || []);
            } else {
                displayNoCurrentShift();
            }
        })
        .catch(error => {
            console.error('❌ خطأ في تحميل الوردية الحالية:', error);
            displayNoCurrentShift();
        });
}

function loadPreviousShiftData() {
    fetch('{{ route("worker-tracking.previous-shift-workers") }}')
        .then(response => response.json())
        .then(data => {
            console.log('✅ بيانات الوردية السابقة:', data);
            if(data.success && data.shift) {
                displayPreviousShiftInfo(data.shift);
                displayPreviousWorkers(data.workers || []);
            } else {
                displayNoPreviousShift();
            }
        })
        .catch(error => {
            console.error('❌ خطأ في تحميل الوردية السابقة:', error);
            displayNoPreviousShift();
        });
}

function displayCurrentShiftInfo(shift) {
    const container = document.getElementById('currentShiftInfo');

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

function displayPreviousShiftInfo(shift) {
    const container = document.getElementById('previousShiftInfo');

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

function displayCurrentWorkers(workers) {
    const container = document.getElementById('currentWorkersGrid');

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
            <div class="col-md-12 col-lg-6 mb-3">
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

function displayPreviousWorkers(workers) {
    const container = document.getElementById('previousWorkersGrid');

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
            <div class="col-md-12 col-lg-6 mb-3">
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

function displayNoCurrentShift() {
    const container = document.getElementById('currentShiftInfo');
    container.innerHTML = `
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            لا توجد وردية نشطة حالياً
        </div>
    `;

    document.getElementById('currentWorkersGrid').innerHTML = `
        <div class="col-12">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                لا توجد عمال في الوردية الحالية
            </div>
        </div>
    `;
}

function displayNoPreviousShift() {
    const container = document.getElementById('previousShiftInfo');
    container.innerHTML = `
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            لا توجد وردية سابقة
        </div>
    `;

    document.getElementById('previousWorkersGrid').innerHTML = `
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

<!-- Transfer Work Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('worker-tracking.transfer') }}">
                @csrf
                <input type="hidden" name="stage_type" value="{{ $stageType }}">
                <input type="hidden" name="stage_record_id" value="{{ $stageRecordId }}">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('worker-tracking.transfer_work') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('worker-tracking.select_new_worker') }}</label>
                        <select name="new_worker_id" class="form-select" required>
                            <option value="">{{ __('worker-tracking.select_new_worker') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('worker-tracking.transfer_notes') }}</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('worker-tracking.transfer_work') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- End Session Modal -->
<div class="modal fade" id="endSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="endSessionForm">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('worker-tracking.end_session') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('worker-tracking.status_after') }}</label>
                        <input type="text" name="status_after" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('worker-tracking.end_session_notes') }}</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('worker-tracking.end_session') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showTransferModal(historyId) {
    // Load available workers
    fetch('{{ route("worker-tracking.available-workers") }}?stage_type={{ $stageType }}')
        .then(response => response.json())
        .then(data => {
            const select = document.querySelector('#transferModal select[name="new_worker_id"]');
            select.innerHTML = '<option value="">{{ __("worker-tracking.select_new_worker") }}</option>';
            data.workers.forEach(worker => {
                select.innerHTML += `<option value="${worker.id}">${worker.name}</option>`;
            });
        });

    new bootstrap.Modal(document.getElementById('transferModal')).show();
}

function showEndSessionModal(historyId) {
    document.getElementById('endSessionForm').action = `{{ url('worker-tracking/end-session') }}/${historyId}`;
    new bootstrap.Modal(document.getElementById('endSessionModal')).show();
}
</script>
@endpush
@endsection
