@extends('master')

@section('title', 'تفاصيل إذن الصرف')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="bi bi-file-earmark-text me-2"></i>
                تفاصيل إذن الصرف
            </h2>
            <p class="text-muted mb-0">
                رقم الإذن: <strong class="text-primary">{{ $deliveryNote->note_number ?? '#' . $deliveryNote->id }}</strong>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manufacturing.finished-product-deliveries.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right me-1"></i>
                العودة للقائمة
            </a>
            
            @if(Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_PRINT') && $deliveryNote->canPrint())
            <a href="{{ route('manufacturing.finished-product-deliveries.print', $deliveryNote->id) }}" 
               class="btn btn-secondary" target="_blank">
                <i class="bi bi-printer me-1"></i>
                طباعة
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
                            <h6 class="text-muted mb-2">الحالة</h6>
                            @if($deliveryNote->status == 'pending')
                                <h4><span class="badge bg-warning text-dark">قيد الانتظار</span></h4>
                            @elseif($deliveryNote->status == 'approved')
                                <h4><span class="badge bg-success">معتمد</span></h4>
                            @elseif($deliveryNote->status == 'rejected')
                                <h4><span class="badge bg-danger">مرفوض</span></h4>
                            @elseif($deliveryNote->status == 'completed')
                                <h4><span class="badge bg-info">مكتمل</span></h4>
                            @endif
                        </div>
                        
                        @if($deliveryNote->status == 'pending' && Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_APPROVE'))
                        <div class="col-md-9 text-end">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bi bi-check-circle me-1"></i>
                                اعتماد الإذن
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-1"></i>
                                رفض الإذن
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
                        بيانات العميل
                    </h5>
                </div>
                <div class="card-body">
                    @if($deliveryNote->customer)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">كود العميل</label>
                                <div><strong>{{ $deliveryNote->customer->customer_code }}</strong></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">اسم العميل</label>
                                <div><strong>{{ $deliveryNote->customer->name }}</strong></div>
                            </div>
                        </div>
                        @if($deliveryNote->customer->company_name)
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">الشركة</label>
                                <div><strong>{{ $deliveryNote->customer->company_name }}</strong></div>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">الهاتف</label>
                                <div><strong>{{ $deliveryNote->customer->phone }}</strong></div>
                            </div>
                        </div>
                        @if($deliveryNote->customer->address)
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="text-muted small">العنوان</label>
                                <div><strong>{{ $deliveryNote->customer->address }}</strong></div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        لم يتم تحديد العميل بعد
                    </div>
                    @endif
                </div>
            </div>

            <!-- الصناديق -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam me-2"></i>
                        الصناديق ({{ $deliveryNote->items->count() }})
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
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="4" class="text-end"><strong>الإجمالي:</strong></td>
                                    <td><strong class="text-success">{{ number_format($totalWeight, 2) }} كجم</strong></td>
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
                        الملاحظات
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
                        سبب الرفض
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
                    <h6 class="mb-0">معلومات الإذن</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">تاريخ الإنشاء</label>
                        <div><strong>{{ $deliveryNote->created_at->format('Y-m-d H:i') }}</strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">أنشئ بواسطة</label>
                        <div><strong>{{ $deliveryNote->recordedBy->name ?? '-' }}</strong></div>
                    </div>

                    @if($deliveryNote->approved_at)
                    <div class="mb-3">
                        <label class="text-muted small">تاريخ الاعتماد</label>
                        <div><strong>{{ $deliveryNote->approved_at->format('Y-m-d H:i') }}</strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">اعتمد بواسطة</label>
                        <div><strong>{{ $deliveryNote->approvedBy->name ?? '-' }}</strong></div>
                    </div>
                    @endif

                    @if($deliveryNote->print_count > 0)
                    <div class="mb-3">
                        <label class="text-muted small">عدد مرات الطباعة</label>
                        <div><strong>{{ $deliveryNote->print_count }}</strong></div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- الإحصائيات -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">إحصائيات سريعة</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>عدد الصناديق:</span>
                        <strong>{{ $deliveryNote->items->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>الوزن الإجمالي:</span>
                        <strong>{{ number_format($deliveryNote->items->sum('weight'), 2) }} كجم</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>متوسط وزن الصندوق:</span>
                        <strong>{{ number_format($deliveryNote->items->avg('weight'), 2) }} كجم</strong>
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
                <h5 class="modal-title">اعتماد إذن الصرف</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">العميل <span class="text-danger">*</span></label>
                        <select name="customer_id" id="approveCustomerId" class="form-select" required>
                            <option value="">اختر العميل</option>
                            @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}" {{ $deliveryNote->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->customer_code }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        سيتم اعتماد الإذن وتحديث بيانات العميل
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        اعتماد
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
                <h5 class="modal-title">{{ __('finished_product_deliveries.reject_delivery_note') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('finished_product_deliveries.rejection_reason') }} <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" 
                                  placeholder="{{ __('finished_product_deliveries.rejection_reason_placeholder') }}" required></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ __('finished_product_deliveries.boxes_will_be_reset') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>
                        {{ __('finished_product_deliveries.reject') }}
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
    // {{ __('finished_product_deliveries.approve_delivery_note') }}
    $('#approveForm').on('submit', function(e) {
        e.preventDefault();
        
        const customerId = $('#approveCustomerId').val();
        if (!customerId) {
            Swal.fire('{{ __('finished_product_deliveries.alert') }}', '{{ __('finished_product_deliveries.customer_required') }}', 'warning');
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
                    Swal.fire('{{ __('finished_product_deliveries.success') }}!', response.message, 'success').then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                Swal.fire('{{ __('finished_product_deliveries.error') }}', xhr.responseJSON?.error || '{{ __('finished_product_deliveries.error_approving_note') }}', 'error');
            }
        });
    });

    // {{ __('finished_product_deliveries.reject_delivery_note') }}
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();
        
        const reason = $(this).find('[name="rejection_reason"]').val();
        if (!reason.trim()) {
            Swal.fire('{{ __('finished_product_deliveries.alert') }}', '{{ __('finished_product_deliveries.reason_required') }}', 'warning');
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
                    Swal.fire('{{ __('finished_product_deliveries.rejected') }}', response.message, 'success').then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                Swal.fire('{{ __('finished_product_deliveries.error') }}', xhr.responseJSON?.error || '{{ __('finished_product_deliveries.error_rejecting_note') }}', 'error');
            }
        });
    });
});
</script>
@endpush
