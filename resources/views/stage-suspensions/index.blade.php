@extends('master')

@section('title', __('stage_suspensions.suspended_stages'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="fas fa-pause-circle me-2 text-danger"></i>
                {{ __('stage_suspensions.waste_ratio_exceeded') }}
            </h2>
            <p class="text-muted mb-0">{{ __('stage_suspensions.monitor_suspensions') }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') ?? __('stage_suspensions.success_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') ?? __('stage_suspensions.error_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">{{ __('stage_suspensions.suspended') }}</h6>
                            <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                        </div>
                        <i class="fas fa-pause-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">{{ __('stage_suspensions.approved') }}</h6>
                            <h3 class="mb-0">{{ $stats['approved'] }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">{{ __('stage_suspensions.rejected') }}</h6>
                            <h3 class="mb-0">{{ $stats['rejected'] }}</h3>
                        </div>
                        <i class="fas fa-times-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">{{ __('stage_suspensions.total') }}</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <i class="fas fa-list fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('stage_suspensions.status') }}</label>
                        <select name="status" class="form-select">
                            <option value="">{{ __('stage_suspensions.all') }}</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>{{ __('stage_suspensions.suspended') }}</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('stage_suspensions.approved') }}</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('stage_suspensions.rejected') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('stage_suspensions.stage') }}</label>
                        <select name="stage" class="form-select">
                            <option value="">{{ __('stage_suspensions.all') }}</option>
                            <option value="1" {{ request('stage') == '1' ? 'selected' : '' }}>{{ __('stage_suspensions.stage_1') }}</option>
                            <option value="2" {{ request('stage') == '2' ? 'selected' : '' }}>{{ __('stage_suspensions.stage_2') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> {{ __('stage_suspensions.filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>    <!-- Suspensions Table -->
    <div class="card">
        <div class="card-body">
            @if($suspensions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('stage_suspensions.stage_name') }}</th>
                            <th>{{ __('stage_suspensions.barcode') }}</th>
                            <th>{{ __('stage_suspensions.input_weight') }}</th>
                            <th>{{ __('stage_suspensions.output_weight') }}</th>
                            <th>{{ __('stage_suspensions.waste_weight') }}</th>
                            <th>{{ __('stage_suspensions.waste_ratio') }}</th>
                            <th>{{ __('stage_suspensions.allowed_ratio') }}</th>
                            <th>{{ __('stage_suspensions.status_header') }}</th>
                            <th>{{ __('stage_suspensions.suspension_date') }}</th>
                            <th>{{ __('stage_suspensions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suspensions as $suspension)
                        <tr>
                            <td><strong>{{ $suspension->getStageName() }}</strong></td>
                            <td>
                                <code>{{ $suspension->production_barcode }}</code>
                                @if($suspension->production_barcode !== $suspension->batch_barcode)
                                    <br><small class="text-muted">المادة: {{ $suspension->batch_barcode }}</small>
                                @endif
                            </td>
                            <td>{{ number_format($suspension->input_weight, 2) }} {{ __('stage_suspensions.kg') }}</td>
                            <td>{{ number_format($suspension->output_weight, 2) }} {{ __('stage_suspensions.kg') }}</td>
                            <td class="text-danger">
                                <strong>{{ number_format($suspension->waste_weight, 2) }} {{ __('stage_suspensions.kg') }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-danger" style="font-size: 14px;">
                                    {{ number_format($suspension->waste_percentage, 2) }}%
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ number_format($suspension->allowed_percentage, 2) }}%
                                </span>
                            </td>
                            <td>{!! $suspension->getStatusBadge() !!}</td>
                            <td>{{ $suspension->suspended_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('stage-suspensions.show', $suspension->id) }}"
                                       class="btn btn-sm btn-info" title="{{ __('stage_suspensions.view_details') }}">
                                        <i class="bi bi-eye me-1"></i>{{ __('stage_suspensions.view') }}
                                    </a>

                                    @if($suspension->status == 'suspended' && Auth::user()->hasPermission('STAGE_SUSPENSION_APPROVE'))
                                    <button type="button"
                                            class="btn btn-sm btn-success approve-btn"
                                            data-id="{{ $suspension->id }}"
                                            title="{{ __('stage_suspensions.approve_title') }}">
                                        <i class="bi bi-check-circle me-1"></i>{{ __('stage_suspensions.approve') }}
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-danger reject-btn"
                                            data-id="{{ $suspension->id }}"
                                            title="{{ __('stage_suspensions.reject_request') }}">
                                        <i class="bi bi-x-circle me-1"></i>{{ __('stage_suspensions.reject') }}
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $suspensions->links() }}
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                {{ __('stage_suspensions.no_suspensions') }}
            </div>
            @endif
        </div>
    </div>
</div>

    <div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ __('stage_suspensions.approve_suspension') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ __('stage_suspensions.confirm_approve') }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('stage_suspensions.notes') }}</label>
                        <textarea name="review_notes" class="form-control" rows="3" placeholder="{{ __('stage_suspensions.notes_placeholder') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('stage_suspensions.cancel') }}</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>{{ __('stage_suspensions.confirm_approval') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle me-2"></i>
                        {{ __('stage_suspensions.reject_request') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ __('stage_suspensions.confirm_reject') }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('stage_suspensions.reject_reason') }} <span class="text-danger">*</span></label>
                        <textarea name="review_notes" class="form-control" rows="3" required placeholder="{{ __('stage_suspensions.reject_reason_placeholder') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('stage_suspensions.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i>{{ __('stage_suspensions.confirm_rejection') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Approve button
    $('.approve-btn').on('click', function() {
        const id = $(this).data('id');
        $('#approveForm').attr('action', `/stage-suspensions/${id}/approve`);
        $('#approveModal').modal('show');
    });

    // Reject button
    $('.reject-btn').on('click', function() {
        const id = $(this).data('id');
        $('#rejectForm').attr('action', `/stage-suspensions/${id}/reject`);
        $('#rejectModal').modal('show');
    });
});
</script>
@endpush
