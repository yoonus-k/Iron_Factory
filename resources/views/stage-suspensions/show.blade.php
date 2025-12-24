@extends('master')

@section('title', __('stage_suspensions.suspension_details'))

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-pause-circle me-2 text-danger"></i>
                        {{ __('stage_suspensions.suspension_details') }}
                    </h2>
                    <p class="text-muted mb-0">
                        {{ __('stage_suspensions.barcode') }}: <strong>{{ $suspension->barcode }}</strong>
                    </p>
                </div>
                <a href="{{ route('stage-suspensions.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>
                    {{ __('stage_suspensions.back_to_list') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="row mb-4">
        <div class="col-md-12">
            @if($suspension->status === 'pending')
                <span class="badge bg-danger fs-6 px-3 py-2">
                    <i class="fas fa-pause-circle me-1"></i>
                    {{ __('stage_suspensions.suspended') }}
                </span>
            @elseif($suspension->status === 'approved')
                <span class="badge bg-success fs-6 px-3 py-2">
                    <i class="fas fa-check-circle me-1"></i>
                    {{ __('stage_suspensions.approved') }}
                </span>
            @else
                <span class="badge bg-secondary fs-6 px-3 py-2">
                    <i class="fas fa-times-circle me-1"></i>
                    {{ __('stage_suspensions.rejected') }}
                </span>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-md-8">
            <!-- Suspension Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __('stage_suspensions.suspension_info') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted d-block mb-1">{{ __('stage_suspensions.barcode') }}</label>
                            <h5 class="mb-0">
                                <code style="font-size: 1.1em;">{{ $suspension->production_barcode }}</code>
                            </h5>
                            @if($suspension->production_barcode !== $suspension->batch_barcode)
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-box me-1"></i>باركود المادة: {{ $suspension->batch_barcode }}
                                </small>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted d-block mb-1">{{ __('stage_suspensions.stage') }}</label>
                            <h5 class="mb-0">
                                <span class="badge bg-primary">
                                    {{ $suspension->getStageName() }}
                                </span>
                            </h5>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted d-block mb-1">{{ __('stage_suspensions.input_weight') }}</label>
                            <h5 class="mb-0 text-primary">{{ number_format($suspension->input_weight, 2) }} {{ __('stage_suspensions.kg') }}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted d-block mb-1">{{ __('stage_suspensions.output_weight') }}</label>
                            <h5 class="mb-0 text-success">{{ number_format($suspension->output_weight, 2) }} {{ __('stage_suspensions.kg') }}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted d-block mb-1">{{ __('stage_suspensions.waste_amount') }}</label>
                            <h5 class="mb-0 text-danger">{{ number_format($suspension->waste_amount, 2) }} {{ __('stage_suspensions.kg') }}</h5>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted d-block mb-1">{{ __('stage_suspensions.allowed_waste_percentage') }}</label>
                            <h5 class="mb-0">
                                <span class="badge bg-info">{{ number_format($suspension->allowed_waste_percentage, 2) }}%</span>
                            </h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted d-block mb-1">{{ __('stage_suspensions.actual_waste_percentage') }}</label>
                            <h5 class="mb-0">
                                <span class="badge bg-danger">{{ number_format($suspension->actual_waste_percentage, 2) }}%</span>
                            </h5>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-muted d-block mb-1">{{ __('stage_suspensions.exceeded_by') }}</label>
                            <h5 class="mb-0">
                                <span class="badge bg-warning text-dark">
                                    +{{ number_format($suspension->actual_waste_percentage - $suspension->allowed_waste_percentage, 2) }}%
                                </span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reason Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">
                        <i class="fas fa-comment-dots me-2"></i>
                        {{ __('stage_suspensions.reason') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ $suspension->reason ?? __('stage_suspensions.no_reason') }}
                    </div>
                </div>
            </div>

            <!-- Images Card -->
            @if($suspension->images && count($suspension->images) > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-images me-2"></i>
                        {{ __('stage_suspensions.attached_images') }} ({{ count($suspension->images) }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($suspension->images as $image)
                        <div class="col-md-4">
                            <a href="{{ Storage::url($image) }}" target="_blank" class="d-block">
                                <img src="{{ Storage::url($image) }}" 
                                     alt="Image" 
                                     class="img-fluid rounded shadow-sm hover-shadow"
                                     style="height: 200px; width: 100%; object-fit: cover;">
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Review Section -->
            @if($suspension->status !== 'pending')
            <div class="card shadow-sm mb-4">
                <div class="card-header {{ $suspension->status === 'approved' ? 'bg-success' : 'bg-danger' }} text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-check me-2"></i>
                        {{ __('stage_suspensions.review_decision') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert {{ $suspension->status === 'approved' ? 'alert-success' : 'alert-danger' }} mb-3">
                        <i class="fas {{ $suspension->status === 'approved' ? 'fa-check-circle' : 'fa-times-circle' }} me-2"></i>
                        <strong>
                            @if($suspension->status === 'approved')
                                {{ __('stage_suspensions.approved_message') }}
                            @else
                                {{ __('stage_suspensions.rejected_message') }}
                            @endif
                        </strong>
                    </div>

                    @if($suspension->review_notes)
                    <div class="mb-3">
                        <label class="text-muted d-block mb-1">{{ __('stage_suspensions.review_notes') }}</label>
                        <div class="bg-light p-3 rounded">
                            {{ $suspension->review_notes }}
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted d-block mb-1">{{ __('stage_suspensions.reviewed_by') }}</label>
                            <p class="mb-0">
                                <i class="fas fa-user me-1"></i>
                                {{ $suspension->reviewedBy->name ?? __('stage_suspensions.unknown') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted d-block mb-1">{{ __('stage_suspensions.reviewed_at') }}</label>
                            <p class="mb-0">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $suspension->reviewed_at ? $suspension->reviewed_at->format('Y-m-d H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Timeline Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        {{ __('stage_suspensions.timeline') }}
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="mb-3">
                            <i class="fas fa-pause-circle text-danger"></i>
                            <div>
                                <strong>{{ __('stage_suspensions.suspended') }}</strong>
                                <p class="text-muted mb-0">
                                    {{ $suspension->created_at->format('Y-m-d H:i') }}
                                </p>
                                <small class="text-muted">
                                    {{ __('stage_suspensions.by') }}: {{ $suspension->suspendedBy->name ?? __('stage_suspensions.system') }}
                                </small>
                            </div>
                        </li>
                        @if($suspension->reviewed_at)
                        <li>
                            <i class="fas {{ $suspension->status === 'approved' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i>
                            <div>
                                <strong>
                                    @if($suspension->status === 'approved')
                                        {{ __('stage_suspensions.approved') }}
                                    @else
                                        {{ __('stage_suspensions.rejected') }}
                                    @endif
                                </strong>
                                <p class="text-muted mb-0">
                                    {{ $suspension->reviewed_at->format('Y-m-d H:i') }}
                                </p>
                                <small class="text-muted">
                                    {{ __('stage_suspensions.by') }}: {{ $suspension->reviewedBy->name ?? __('stage_suspensions.unknown') }}
                                </small>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Action Buttons -->
            @if($suspension->status === 'pending' && auth()->user()->can('approve-stage-suspensions'))
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks me-2"></i>
                        {{ __('stage_suspensions.actions') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('stage-suspensions.approve', $suspension->id) }}" 
                          method="POST" 
                          class="mb-3"
                          onsubmit="return confirm('{{ __('stage_suspensions.confirm_approve') }}')">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label for="review_notes" class="form-label">
                                {{ __('stage_suspensions.review_notes') }}
                                <small class="text-muted">({{ __('stage_suspensions.optional') }})</small>
                            </label>
                            <textarea name="review_notes" 
                                      id="review_notes" 
                                      class="form-control" 
                                      rows="3"
                                      placeholder="{{ __('stage_suspensions.review_notes_placeholder') }}"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-check me-2"></i>
                            {{ __('stage_suspensions.approve') }}
                        </button>
                    </form>

                    <form action="{{ route('stage-suspensions.reject', $suspension->id) }}" 
                          method="POST"
                          onsubmit="return confirm('{{ __('stage_suspensions.confirm_reject') }}')">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label for="reject_notes" class="form-label text-danger">
                                {{ __('stage_suspensions.rejection_reason') }}
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="review_notes" 
                                      id="reject_notes" 
                                      class="form-control" 
                                      rows="3"
                                      required
                                      placeholder="{{ __('stage_suspensions.rejection_reason_placeholder') }}"></textarea>
                        </div>

                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times me-2"></i>
                            {{ __('stage_suspensions.reject') }}
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    transform: scale(1.05);
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3) !important;
}

.timeline {
    list-style: none;
    padding: 0;
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline li {
    position: relative;
    padding-left: 35px;
}

.timeline li i {
    position: absolute;
    left: 0;
    top: 5px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: white;
}
</style>
@endsection
