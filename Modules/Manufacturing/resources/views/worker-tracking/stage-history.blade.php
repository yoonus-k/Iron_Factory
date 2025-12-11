@extends('master')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ __('manufacturing::worker-tracking.stage_history') }}</h2>
            <p class="text-muted mb-0">
                {{ __('manufacturing::worker-tracking.' . $stageType) }}
            </p>
        </div>
        <a href="{{ route('worker-tracking.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right me-2"></i>
            {{ __('manufacturing::worker-tracking.dashboard') }}
        </a>
    </div>

    <!-- Current Worker Card -->
    @if($currentWorker)
    <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-user-clock me-2"></i>
                {{ __('manufacturing::worker-tracking.current_worker') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h6 class="text-muted mb-1">{{ __('manufacturing::worker-tracking.worker_name') }}</h6>
                    <h4 class="mb-0">{{ $currentWorker->worker_name }}</h4>
                    <span class="badge bg-info mt-2">
                        {{ __('manufacturing::worker-tracking.' . $currentWorker->worker_type . '_worker') }}
                    </span>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">{{ __('manufacturing::worker-tracking.started_at') }}</h6>
                    <p class="mb-0">{{ $currentWorker->started_at->format('Y-m-d H:i') }}</p>
                    <small class="text-muted">{{ $currentWorker->started_at->diffForHumans() }}</small>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">{{ __('manufacturing::worker-tracking.work_duration') }}</h6>
                    <h5 class="mb-0 text-primary">{{ $currentWorker->formatted_duration }}</h5>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-warning btn-sm"
                            onclick="showTransferModal({{ $currentWorker->id }})">
                        <i class="fas fa-exchange-alt me-1"></i>
                        {{ __('manufacturing::worker-tracking.transfer_work') }}
                    </button>
                    <button type="button" class="btn btn-danger btn-sm mt-2"
                            onclick="showEndSessionModal({{ $currentWorker->id }})">
                        <i class="fas fa-stop-circle me-1"></i>
                        {{ __('manufacturing::worker-tracking.end_session') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        {{ __('manufacturing::worker-tracking.no_current_worker') }}
    </div>
    @endif

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('manufacturing::worker-tracking.total_sessions') }}</h6>
                    <h3 class="mb-0">{{ $statistics['total_sessions'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('manufacturing::worker-tracking.total_workers') }}</h6>
                    <h3 class="mb-0">{{ $statistics['total_workers'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('manufacturing::worker-tracking.total_hours') }}</h6>
                    <h3 class="mb-0">{{ number_format($statistics['total_hours'], 1) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('manufacturing::worker-tracking.average_session_time') }}</h6>
                    <h3 class="mb-0">{{ number_format($statistics['average_session_minutes'] ?? 0, 0) }} {{ __('manufacturing::worker-tracking.minutes') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-history me-2"></i>
                {{ __('manufacturing::worker-tracking.previous_workers') }}
            </h5>
        </div>
        <div class="card-body">
            @if($history->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('manufacturing::worker-tracking.worker_name') }}</th>
                            <th>{{ __('manufacturing::worker-tracking.worker_type') }}</th>
                            <th>{{ __('manufacturing::worker-tracking.started_at') }}</th>
                            <th>{{ __('manufacturing::worker-tracking.ended_at') }}</th>
                            <th>{{ __('manufacturing::worker-tracking.work_duration') }}</th>
                            <th>{{ __('manufacturing::worker-tracking.status_before') }}</th>
                            <th>{{ __('manufacturing::worker-tracking.status_after') }}</th>
                            <th>{{ __('manufacturing::worker-tracking.assigned_by') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $record)
                        <tr class="{{ $record->is_active ? 'table-primary' : '' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $record->worker_name }}</strong>
                                @if($record->is_active)
                                <span class="badge bg-success ms-2">{{ __('manufacturing::worker-tracking.still_working') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $record->worker_type === 'team' ? 'info' : 'secondary' }}">
                                    {{ __('manufacturing::worker-tracking.' . $record->worker_type . '_worker') }}
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
                                <span class="text-primary">{{ __('manufacturing::worker-tracking.still_working') }}</span>
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
                                <small><strong>{{ __('manufacturing::worker-tracking.notes') }}:</strong> {{ $record->notes }}</small>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info mb-0">
                {{ __('manufacturing::worker-tracking.no_history_found') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Transfer Work Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('worker-tracking.transfer') }}">
                @csrf
                <input type="hidden" name="stage_type" value="{{ $stageType }}">
                <input type="hidden" name="stage_record_id" value="{{ $stageRecordId }}">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('manufacturing::worker-tracking.transfer_work') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('manufacturing::worker-tracking.select_new_worker') }}</label>
                        <select name="new_worker_id" class="form-select" required>
                            <option value="">{{ __('manufacturing::worker-tracking.select_new_worker') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('manufacturing::worker-tracking.transfer_notes') }}</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('manufacturing::worker-tracking.transfer_work') }}</button>
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
                    <h5 class="modal-title">{{ __('manufacturing::worker-tracking.end_session') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('manufacturing::worker-tracking.status_after') }}</label>
                        <input type="text" name="status_after" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('manufacturing::worker-tracking.end_session_notes') }}</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('manufacturing::worker-tracking.end_session') }}</button>
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
            select.innerHTML = '<option value="">{{ __("manufacturing::worker-tracking.select_new_worker") }}</option>';
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
