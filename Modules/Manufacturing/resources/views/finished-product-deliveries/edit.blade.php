@extends('master')

@section('title', __('app.finished_products.edit_note'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="bi bi-pencil-square me-2"></i>
                {{ __('app.finished_products.edit_note') }}
            </h2>
            <p class="text-muted mb-0">
                {{ __('app.finished_products.note_number') }}: <strong class="text-primary">{{ $deliveryNote->note_number ?? '#' . $deliveryNote->id }}</strong>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manufacturing.finished-product-deliveries.show', $deliveryNote->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right me-1"></i>
                {{ __('app.finished_products.back_to_list') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil me-2"></i>
                        {{ __('app.finished_products.edit_data') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>{{ __('app.messages.info.note') }}:</strong> {{ __('app.finished_products.note_info') }}
                    </div>

                    <form id="editForm" method="POST" action="{{ route('manufacturing.finished-product-deliveries.update', $deliveryNote->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- العميل -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.finished_products.customer') }}</label>
                            <select name="customer_id" class="form-select">
                                <option value="">{{ __('app.finished_products.customer_placeholder') }}</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $deliveryNote->customer_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->customer_code }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('app.finished_products.customer_help') }}</small>
                        </div>

                        <!-- الملاحظات -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.finished_products.notes') }}</label>
                            <textarea name="notes" class="form-control" rows="4"
                                      placeholder="{{ __('app.finished_products.notes_placeholder') }}">{{ $deliveryNote->notes }}</textarea>
                        </div>

                        <!-- الأزرار -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                {{ __('app.finished_products.save_changes') }}
                            </button>
                            <a href="{{ route('manufacturing.finished-product-deliveries.show', $deliveryNote->id) }}"
                               class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>
                                {{ __('app.finished_products.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- قسم الصناديق (للعرض فقط) -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">{{ __('app.finished_products.specified_boxes') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('app.finished_products.boxes_count_label') }}:</span>
                            <strong>{{ $deliveryNote->items->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>{{ __('app.finished_products.total_weight_label') }}:</span>
                            <strong>{{ number_format($deliveryNote->items->sum('weight'), 2) }} {{ __('app.units.kg') }}</strong>
                        </div>
                    </div>

                    <hr>

                    <div style="max-height: 400px; overflow-y: auto;">
                        @foreach($deliveryNote->items as $index => $item)
                        <div class="alert alert-light py-2 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $item->barcode }}</strong><br>
                                    <small>{{ number_format($item->weight, 2) }} {{ __('app.units.kg') }}</small>
                                </div>
                                <i class="bi bi-lock text-muted" title="{{ __('app.messages.info.cannot_edit') }}"></i>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> {{ __("app.messages.loading.saving") }}');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("app.finished_products.success") }}!',
                        text: response.message,
                        confirmButtonText: '{{ __("app.buttons.ok") }}'
                    }).then(() => {
                        window.location.href = '{{ route("manufacturing.finished-product-deliveries.show", $deliveryNote->id) }}';
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = '{{ __("app.messages.error.general") }}';

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }

                Swal.fire({
                    icon: 'error',
                    title: '{{ __("app.finished_products.error") }}',
                    text: errorMessage
                });

                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endpush
