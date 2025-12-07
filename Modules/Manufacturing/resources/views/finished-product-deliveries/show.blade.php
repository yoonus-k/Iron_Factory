@extends('master')

@section('title', __('app.finished_products.note_details'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="bi bi-file-earmark-text me-2"></i>
                {{ __('app.finished_products.note_details') }}
            </h2>
            <p class="text-muted mb-0">
                {{ __('app.finished_products.note_number') }}: <strong class="text-primary">{{ $deliveryNote->note_number ?? '#' . $deliveryNote->id }}</strong>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manufacturing.finished-product-deliveries.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right me-1"></i>
                {{ __('app.finished_products.back_to_list') }}
            </a>

            @if(Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_PRINT') && $deliveryNote->canPrint())
            <a href="{{ route('manufacturing.finished-product-deliveries.print', $deliveryNote->id) }}"
               class="btn btn-secondary" target="_blank">
                <i class="bi bi-printer me-1"></i>
                {{ __('app.finished_products.print') }}
                @if($deliveryNote->print_count > 0)
                <span class="badge bg-light text-dark">{{ $deliveryNote->print_count }}</span>
                @endif
            </a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- معلومات الإذن -->
        <div class="col-md-8">
            <!-- بطاقة الحالة -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h6 class="text-muted mb-2">{{ __('app.finished_products.status') }}</h6>
                            @if($deliveryNote->status == 'pending')
                                <h4><span class="badge bg-warning text-dark">{{ __('app.finished_products.pending') }}</span></h4>
                            @elseif($deliveryNote->status == 'approved')
                                <h4><span class="badge bg-success">{{ __('app.finished_products.approved') }}</span></h4>
                            @elseif($deliveryNote->status == 'rejected')
                                <h4><span class="badge bg-danger">{{ __('app.rejected_status') }}</span></h4>
                            @elseif($deliveryNote->status == 'completed')
                                <h4><span class="badge bg-info">{{ __('app.finished_products.completed') }}</span></h4>
                            @endif
                        </div>

                        @if($deliveryNote->status == 'pending' && Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_APPROVE'))
                        <div class="col-md-9 text-end">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bi bi-check-circle me-1"></i>
                                {{ __('app.finished_products.approve_delivery_note') }}
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-1"></i>
                                {{ __('app.finished_products.reject_delivery_note') }}
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- بيانات العميل -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person me-2"></i>
                        {{ __('app.finished_products.customer_details') }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($deliveryNote->customer)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('app.finished_products.customer_code') }}</label>
                                <div><strong>{{ $deliveryNote->customer->customer_code }}</strong></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('app.finished_products.customer_name') }}</label>
                                <div><strong>{{ $deliveryNote->customer->name }}</strong></div>
                            </div>
                        </div>
                        @if($deliveryNote->customer->company_name)
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('app.finished_products.company') }}</label>
                                <div><strong>{{ $deliveryNote->customer->company_name }}</strong></div>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('app.finished_products.phone') }}</label>
                                <div><strong>{{ $deliveryNote->customer->phone }}</strong></div>
                            </div>
                        </div>
                        @if($deliveryNote->customer->address)
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('app.finished_products.address') }}</label>
                                <div><strong>{{ $deliveryNote->customer->address }}</strong></div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ __('app.finished_products.customer_not_set') }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- الصناديق -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam me-2"></i>
                        {{ __('app.finished_products.boxes') }} ({{ $deliveryNote->items->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>

                                    <th>الباركود</th>
                                    <th>مواصفات المنتج</th>
                                    <th>نوع التغليف</th>
                                    <th>الوزن (كجم)</th>

                                    <th>{{ __('app.finished_products.barcode') }}</th>
                                    <th>{{ __('app.finished_products.packaging_type') }}</th>
                                    <th>{{ __('app.finished_products.weight') }}</th>
                                    <th>{{ __('app.finished_products.coils_count') }}</th>

                                </tr>
                            </thead>
                            <tbody>
                                @php $totalWeight = 0; @endphp
                                @foreach($deliveryNote->items as $index => $item)
                                @php $totalWeight += $item->weight; @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong class="text-primary">{{ $item->barcode }}</strong></td>

                                    <td>
                                        @if(isset($item->materials) && $item->materials->count() > 0)
                                            @foreach($item->materials as $material)
                                                <span class="badge bg-info me-1">
                                                    @if($material->color) {{ $material->color }} @endif
                                                    @if($material->material_type) - {{ $material->material_type }} @endif
                                                    @if($material->wire_size) - {{ $material->wire_size }} @endif
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->packaging_type }}</td>
                                    <td><strong>{{ number_format($item->weight, 2) }} كجم</strong></td>

                                    <td>{{ $item->stage4Box->productType->type_name ?? '-' }}</td>
                                    <td><strong>{{ number_format($item->weight, 2) }} {{ __('app.units.kg') }}</strong></td>
                                    <td>{{ $item->stage4Box->boxCoils->count() ?? 0 }}</td>

                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active">

                                    <td colspan="4" class="text-end"><strong>الإجمالي:</strong></td>
                                    <td><strong class="text-success">{{ number_format($totalWeight, 2) }} كجم</strong></td>

                                    <td colspan="3" class="text-end"><strong>{{ __('app.finished_products.total') }}:</strong></td>
                                    <td colspan="2"><strong class="text-success">{{ number_format($totalWeight, 2) }} {{ __('app.units.kg') }}</strong></td>

                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- الملاحظات -->
            @if($deliveryNote->notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-clipboard me-2"></i>
                        {{ __('app.finished_products.notes') }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $deliveryNote->notes }}</p>
                </div>
            </div>
            @endif

            <!-- سبب الرفض -->
            @if($deliveryNote->status == 'rejected' && $deliveryNote->rejection_reason)
            <div class="card mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ __('app.finished_products.rejection_reason') }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $deliveryNote->rejection_reason }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- معلومات جانبية -->
        <div class="col-md-4">
            <!-- معلومات التواريخ -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('app.finished_products.note_info_section') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">{{ __('app.finished_products.creation_date') }}</label>
                        <div><strong>{{ $deliveryNote->created_at->format('Y-m-d H:i') }}</strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">{{ __('app.finished_products.created_by') }}</label>
                        <div><strong>{{ $deliveryNote->recordedBy->name ?? '-' }}</strong></div>
                    </div>

                    @if($deliveryNote->approved_at)
                    <div class="mb-3">
                        <label class="text-muted small">{{ __('app.finished_products.approval_date') }}</label>
                        <div><strong>{{ $deliveryNote->approved_at->format('Y-m-d H:i') }}</strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">{{ __('app.finished_products.approved_by') }}</label>
                        <div><strong>{{ $deliveryNote->approvedBy->name ?? '-' }}</strong></div>
                    </div>
                    @endif

                    @if($deliveryNote->print_count > 0)
                    <div class="mb-3">
                        <label class="text-muted small">{{ __('app.finished_products.print_count') }}</label>
                        <div><strong>{{ $deliveryNote->print_count }}</strong></div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- الإحصائيات -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">{{ __('app.finished_products.quick_stats') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('app.finished_products.boxes_count_label') }}:</span>
                        <strong>{{ $deliveryNote->items->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('app.finished_products.total_weight_label') }}:</span>
                        <strong>{{ number_format($deliveryNote->items->sum('weight'), 2) }} {{ __('app.units.kg') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{ __('app.finished_products.average_box_weight') }}:</span>
                        <strong>{{ number_format($deliveryNote->items->avg('weight'), 2) }} {{ __('app.units.kg') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal للاعتماد -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">{{ __('app.finished_products.approve_delivery_note') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('app.finished_products.customer') }} <span class="text-danger">*</span></label>
                        <select name="customer_id" id="approveCustomerId" class="form-select" required>
                            <option value="">-- {{ __('app.buttons.select') }} --</option>
                            @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}" {{ $deliveryNote->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->customer_code }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ __('app.finished_products.approve_message') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.buttons.cancel') }}</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ __('app.finished_products.approve') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
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
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('app.finished_products.rejection_reason') }} <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4"
                                  placeholder="{{ __('app.finished_products.rejection_reason_placeholder') }}" required></textarea>
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
                        {{ __('app.finished_products.reject') }}
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
    $('#approveForm').on('submit', function(e) {
        e.preventDefault();

        const customerId = $('#approveCustomerId').val();
        if (!customerId) {
            Swal.fire('{{ __("app.finished_products.alert") }}', '{{ __("app.finished_products.customer_required") }}', 'warning');
            return;
        }

        $.ajax({
            url: '{{ route("manufacturing.finished-product-deliveries.approve", $deliveryNote->id) }}',
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
    });

    // رفض الإذن
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();

        const reason = $(this).find('[name="rejection_reason"]').val();
        if (!reason.trim()) {
            Swal.fire('{{ __("app.finished_products.alert") }}', '{{ __("app.finished_products.reason_required") }}', 'warning');
            return;
        }

        $.ajax({
            url: '{{ route("manufacturing.finished-product-deliveries.reject", $deliveryNote->id) }}',
            method: 'POST',
            data: { rejection_reason: reason },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
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
