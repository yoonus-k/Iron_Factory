@extends('master')

@section('title', __('app.finished_products.pending_approvals'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="bi bi-clock-history me-2 text-warning"></i>
                {{ __('app.finished_products.pending_approvals') }}
            </h2>
            <p class="text-muted mb-0">{{ __('app.finished_products.review_approvals') }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manufacturing.finished-product-deliveries.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right me-1"></i>
                {{ __('app.finished_products.back_to_list') }}
            </a>
        </div>
    </div>

    @if($deliveryNotes->count() > 0)
    <div class="alert alert-warning mb-4">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>{{ __('app.finished_products.pending_count') }} {{ $deliveryNotes->total() }}</strong>
    </div>

    @foreach($deliveryNotes as $note)
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        {{ __('app.finished_products.note_number') }}: {{ $note->note_number ?? '#' . $note->id }}
                    </h5>
                    <small class="text-muted">
                        {{ __('app.finished_products.approval_note') }}: <strong>{{ $note->recordedBy->name ?? '-' }}</strong> -
                        {{ $note->created_at->diffForHumans() }}
                    </small>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('manufacturing.finished-product-deliveries.show', $note->id) }}"
                       class="btn btn-sm btn-info" target="_blank">
                        <i class="bi bi-eye me-1"></i>
                        {{ __('app.finished_products.view_full_details') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- معلومات الإذن -->
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">{{ __('app.finished_products.note_info_section') }}</h6>

                    <div class="mb-3">
                        <label class="text-muted small">{{ __('app.finished_products.current_customer') }}</label>
                        <div>
                            @if($note->customer)
                                <strong class="text-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    {{ $note->customer->name }} ({{ $note->customer->customer_code }})
                                </strong>
                            @else
                                <span class="text-warning">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ __('app.finished_products.customer_warning') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">{{ __('app.finished_products.boxes_count_label') }}</label>
                        <div><strong>{{ $note->items->count() }} {{ __('app.finished_products.boxes') }}</strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">{{ __('app.finished_products.total_weight_label') }}</label>
                        <div><strong class="text-primary">{{ number_format($note->items->sum('weight'), 2) }} {{ __('app.units.kg') }}</strong></div>
                    </div>

                    @if($note->notes)
                    <div class="mb-3">
                        <label class="text-muted small">{{ __('app.finished_products.notes') }}</label>
                        <div class="alert alert-light py-2">{{ $note->notes }}</div>
                    </div>
                    @endif
                </div>

                <!-- الصناديق -->
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">{{ __('app.finished_products.boxes') }}</h6>

                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('app.finished_products.barcode') }}</th>
                                    <th>{{ __('app.finished_products.packaging_type') }}</th>
                                    <th>{{ __('app.finished_products.weight') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($note->items as $item)
                                <tr>
                                    <td><small><strong>{{ $item->barcode }}</strong></small></td>
                                    <td><small>{{ $item->stage4Box->productType->type_name ?? '-' }}</small></td>
                                    <td><small><strong>{{ number_format($item->weight, 2) }}</strong></small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- نموذج اختيار العميل والإجراءات -->
            <hr>
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-person-check me-1"></i>
                            {{ __('app.finished_products.select_confirm_customer') }} <span class="text-danger">*</span>
                        </label>
                        <select class="form-select customer-select" data-note-id="{{ $note->id }}">
                            <option value="">-- {{ __('app.buttons.select') }} --</option>
                            @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}" {{ $note->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->customer_code }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-success approve-btn" data-note-id="{{ $note->id }}">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ __('app.finished_products.approve') }}
                        </button>
                        <button type="button" class="btn btn-danger reject-btn" data-note-id="{{ $note->id }}">
                            <i class="bi bi-x-circle me-1"></i>
                            {{ __('app.finished_products.reject') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Pagination -->
    <div class="mt-3">
        {{ $deliveryNotes->links() }}
    </div>

    @else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
            <h4 class="mt-3">{{ __('app.finished_products.no_pending') }}</h4>
            <p class="text-muted">{{ __('app.finished_products.all_approved') }}</p>
            <a href="{{ route('manufacturing.finished-product-deliveries.index') }}" class="btn btn-primary">
                {{ __('app.finished_products.view_all') }}
            </a>
        </div>
    </div>
    @endif
</div>

<!-- Modal للرفض -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">{{ __('app.finished_products.reject_delivery_note') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <input type="hidden" id="rejectNoteId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('app.finished_products.rejection_reason') }} <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4"
                                  placeholder="{{ __('app.finished_products.rejection_reason') }}" required></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ __('app.finished_products.reset_boxes_status') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.buttons.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>
                        {{ __('app.finished_products.confirm_rejection') }}
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
    // اعتماد الإذن
    $(document).on('click', '.approve-btn', function() {
        const noteId = $(this).data('note-id');
        const customerId = $(`.customer-select[data-note-id="${noteId}"]`).val();

        if (!customerId) {
            Swal.fire('{{ __("app.finished_products.alert") }}', '{{ __("app.finished_products.customer_required") }}', 'warning');
            return;
        }

        Swal.fire({
            title: '{{ __("app.finished_products.confirm_approval") }}',
            text: '{{ __("app.finished_products.are_you_sure") }}',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: '{{ __("app.finished_products.yes_approve") }}',
            cancelButtonText: '{{ __("app.buttons.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('finished-product-deliveries') }}/${noteId}/approve`,
                    method: 'POST',
                    data: { customer_id: customerId },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('{{ __("app.finished_products.success") }}!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('{{ __("app.finished_products.error") }}', xhr.responseJSON?.error || '{{ __("app.finished_products.error_approving_note") }}', 'error');
                    }
                });
            }
        });
    });

    // رفض الإذن - فتح Modal
    $(document).on('click', '.reject-btn', function() {
        const noteId = $(this).data('note-id');
        $('#rejectNoteId').val(noteId);
        $('#rejectModal').modal('show');
    });

    // تأكيد الرفض
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();

        const noteId = $('#rejectNoteId').val();
        const reason = $(this).find('[name="rejection_reason"]').val();

        if (!reason.trim()) {
            Swal.fire('{{ __("app.finished_products.alert") }}', '{{ __("app.finished_products.reason_required") }}', 'warning');
            return;
        }

        $.ajax({
            url: `{{ url('finished-product-deliveries') }}/${noteId}/reject`,
            method: 'POST',
            data: { rejection_reason: reason },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#rejectModal').modal('hide');
                    Swal.fire('{{ __("app.finished_products.rejection_confirmed") }}', response.message, 'success').then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                Swal.fire('{{ __("app.finished_products.error") }}', xhr.responseJSON?.error || '{{ __("app.finished_products.error_rejecting_note") }}', 'error');
            }
        });
    });
});
</script>
@endpush
