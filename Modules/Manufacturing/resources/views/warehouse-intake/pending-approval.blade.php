@extends('master')

@section('title', 'طلبات الإدخال المعلقة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-2">
                <i class="bi bi-hourglass-split me-2 text-warning"></i>
                طلبات إدخال المستودع - بانتظار الموافقة
            </h2>
            <p class="text-muted mb-0">
                قم بمراجعة واعتماد أو رفض طلبات إدخال الصناديق للمستودع
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manufacturing.warehouse-intake.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right me-1"></i>
                جميع الطلبات
            </a>
        </div>
    </div>

    @if($pendingRequests->count() > 0)
    <div class="alert alert-warning mb-4">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>يوجد {{ $pendingRequests->total() }} طلب بانتظار المراجعة</strong>
    </div>

    @foreach($pendingRequests as $request)
    <div class="card mb-4 border-warning shadow-sm">
        <div class="card-header bg-warning">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        طلب رقم: <strong>{{ $request->request_number }}</strong>
                    </h5>
                    <small class="text-dark">
                        <i class="bi bi-person me-1"></i>
                        من: <strong>{{ $request->requestedBy->name ?? '-' }}</strong> • 
                        <i class="bi bi-clock me-1"></i>
                        {{ $request->created_at->diffForHumans() }}
                    </small>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('manufacturing.warehouse-intake.show', $request->id) }}" 
                       class="btn btn-sm btn-info" target="_blank">
                        <i class="bi bi-eye me-1"></i>
                        عرض التفاصيل
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- معلومات الطلب -->
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        معلومات الطلب
                    </h6>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">عدد الصناديق</small>
                                <h4 class="mb-0 text-primary">{{ $request->boxes_count }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">الوزن الإجمالي</small>
                                <h4 class="mb-0 text-success">{{ number_format($request->total_weight, 2) }} كجم</h4>
                            </div>
                        </div>
                    </div>

                    @if($request->notes)
                    <div class="mt-3">
                        <small class="text-muted">ملاحظات الطلب:</small>
                        <div class="alert alert-light py-2 mb-0 mt-1">
                            <i class="bi bi-chat-left-text me-1"></i>
                            {{ $request->notes }}
                        </div>
                    </div>
                    @endif
                </div>

                <!-- الصناديق -->
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">
                        <i class="bi bi-box-seam me-1"></i>
                        قائمة الصناديق
                    </h6>
                    
                    <div style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الباركود</th>
                                    <th>التغليف</th>
                                    <th>الوزن</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($request->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong class="text-primary">{{ $item->barcode }}</strong></td>
                                    <td>{{ $item->packaging_type }}</td>
                                    <td>{{ number_format($item->weight, 2) }} كجم</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-success btn-lg approve-btn" 
                                data-request-id="{{ $request->id }}"
                                data-request-number="{{ $request->request_number }}">
                            <i class="bi bi-check-circle me-2"></i>
                            اعتماد وإدخال للمستودع
                        </button>
                        <button type="button" class="btn btn-danger btn-lg reject-btn" 
                                data-request-id="{{ $request->id }}"
                                data-request-number="{{ $request->request_number }}">
                            <i class="bi bi-x-circle me-2"></i>
                            رفض الطلب
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $pendingRequests->links() }}
    </div>

    @else
    <div class="card border-success">
        <div class="card-body text-center py-5">
            <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
            <h4 class="mt-3 mb-2">لا توجد طلبات معلقة</h4>
            <p class="text-muted">جميع الطلبات تمت معالجتها ✓</p>
        </div>
    </div>
    @endif
</div>

<!-- Modal اختيار المستودع للاعتماد -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>
                    اعتماد طلب الإدخال
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm">
                <input type="hidden" id="approveRequestId">
                <input type="hidden" id="approveRequestNumber">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        اختر المستودع الذي سيتم إدخال الصناديق إليه
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المستودع المستهدف <span class="text-danger">*</span></label>
                        <select name="warehouse_id" class="form-select" required>
                            <option value="">-- اختر المستودع --</option>
                            @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">
                                {{ $warehouse->warehouse_name }}
                                @if($warehouse->location)
                                    - {{ $warehouse->location }}
                                @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="request-summary" class="alert alert-light d-none">
                        <!-- سيتم ملؤه بواسطة JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        تأكيد الاعتماد والإدخال
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal الرفض -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-x-circle me-2"></i>
                    رفض طلب الإدخال
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <input type="hidden" id="rejectRequestId">
                <input type="hidden" id="rejectRequestNumber">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        سيتم إرجاع الصناديق للمرحلة الرابعة بحالة "مكتمل"
                    </div>
                    <div class="mb-3">
                        <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" 
                                  placeholder="اذكر سبب رفض طلب الإدخال..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>
                        تأكيد الرفض
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
    // فتح modal الاعتماد
    $(document).on('click', '.approve-btn', function() {
        const requestId = $(this).data('request-id');
        const requestNumber = $(this).data('request-number');
        
        $('#approveRequestId').val(requestId);
        $('#approveRequestNumber').val(requestNumber);
        
        // إظهار ملخص الطلب
        const card = $(this).closest('.card');
        const boxesCount = card.find('.badge.bg-primary').text().split(' ')[0];
        const totalWeight = card.find('.badge.bg-info').text().split(' ')[0];
        
        $('#request-summary').removeClass('d-none').html(`
            <strong>ملخص الطلب ${requestNumber}:</strong><br>
            <i class="bi bi-box-seam me-1"></i> عدد الصناديق: <strong>${boxesCount}</strong><br>
            <i class="bi bi-file-earmark-bar-graph me-1"></i> الوزن الإجمالي: <strong>${totalWeight} كجم</strong>
        `);
        
        $('#approveModal').modal('show');
    });

    // تأكيد الاعتماد
    $('#approveForm').on('submit', function(e) {
        e.preventDefault();
        
        const requestId = $('#approveRequestId').val();
        const warehouseId = $(this).find('[name="warehouse_id"]').val();

        if (!warehouseId) {
            Swal.fire('تنبيه', 'يجب اختيار المستودع', 'warning');
            return;
        }

        $.ajax({
            url: `/warehouse-intake/${requestId}/approve`,
            method: 'POST',
            data: { warehouse_id: warehouseId },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#approveModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الاعتماد بنجاح!',
                        text: response.message,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                Swal.fire('خطأ', xhr.responseJSON?.error || 'فشل اعتماد الطلب', 'error');
            }
        });
    });

    // فتح modal الرفض
    $(document).on('click', '.reject-btn', function() {
        const requestId = $(this).data('request-id');
        const requestNumber = $(this).data('request-number');
        
        $('#rejectRequestId').val(requestId);
        $('#rejectRequestNumber').val(requestNumber);
        $('#rejectModal').modal('show');
    });

    // تأكيد الرفض
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();
        
        const requestId = $('#rejectRequestId').val();
        const reason = $(this).find('[name="rejection_reason"]').val();

        if (!reason.trim()) {
            Swal.fire('تنبيه', 'يجب ذكر سبب الرفض', 'warning');
            return;
        }

        $.ajax({
            url: `/warehouse-intake/${requestId}/reject`,
            method: 'POST',
            data: { rejection_reason: reason },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#rejectModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الرفض',
                        text: response.message,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                Swal.fire('خطأ', xhr.responseJSON?.error || 'فشل رفض الطلب', 'error');
            }
        });
    });
});
</script>
@endpush
