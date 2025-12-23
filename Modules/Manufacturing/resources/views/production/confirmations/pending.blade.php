@extends('master')

@section('title', __('app.production_confirmations.pending_title'))

@section('content')
<div class="container-fluid py-4">

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-clock text-warning me-2"></i>
                        {{ __('app.production_confirmations.pending_title') }}
                    </h4>
                    <small class="text-muted">{{ __('app.production_confirmations.pending_subtitle') }}</small>
                </div>
            </div>
        </div>
    </div>

    @if($pendingConfirmations->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('app.production_confirmations.no_pending') }}</h5>
                        <p class="text-muted">{{ __('app.production_confirmations.no_pending_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center" style="white-space: nowrap; font-size: 13px;">{{ __('app.production_confirmations.table.batch_code') }}</th>
                                        <th class="text-center" style="white-space: nowrap; font-size: 13px;">{{ __('app.production_confirmations.table.material') }}</th>
                                        <th class="text-center" style="white-space: nowrap; font-size: 13px;">{{ __('app.production_confirmations.table.quantity') }}</th>
                                        <th class="text-center" style="white-space: nowrap; font-size: 13px;">{{ __('app.production_confirmations.table.stage') }}</th>
                                        <th class="text-center" style="white-space: nowrap; font-size: 13px;">{{ __('app.production_confirmations.table.date') }}</th>
                                        <th class="text-center" style="white-space: nowrap; font-size: 13px;">{{ __('app.production_confirmations.table.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingConfirmations as $index => $confirmation)
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-purple" style="font-size: 11px; white-space: nowrap;">{{ $confirmation->batch?->batch_code ?? __('app.production_confirmations.table.not_specified') }}</span>
                                        </td>
                                        <td class="text-center" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 13px;" title="{{ $confirmation->batch?->material?->name ?? __('app.production_confirmations.table.not_specified') }}">{{ $confirmation->batch?->material?->name ?? __('app.production_confirmations.table.not_specified') }}</td>
                                        <td class="text-center" style="white-space: nowrap;">
                                            <strong class="text-success" style="font-size: 13px;">{{ number_format($confirmation->actual_received_quantity ?? 0, 2) }}</strong>
                                            <small class="text-muted" style="font-size: 10px;">{{ __('app.units.kg') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info" style="font-size: 11px; white-space: nowrap;">{{ $confirmation->deliveryNote?->production_stage_name ?? __('app.production_confirmations.table.not_specified') }}</span>
                                        </td>
                                        <td class="text-center text-muted" style="white-space: nowrap; font-size: 13px;">{{ $confirmation->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group" style="flex-wrap: nowrap;">
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="showDetails({{ $confirmation->id }})" title="{{ __('app.production_confirmations.view_details') }}" style="padding: 4px 8px; font-size: 11px;">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-success btn-sm" onclick="confirmReceipt({{ $confirmation->id }})" title="{{ __('app.production_confirmations.confirm_receipt') }}" style="padding: 4px 8px; font-size: 11px;">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="rejectReceipt({{ $confirmation->id }})" title="{{ __('app.production_confirmations.reject_receipt') }}" style="padding: 4px 8px; font-size: 11px;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($pendingConfirmations->hasPages())
                        <div class="card-footer bg-white">
                            {{ $pendingConfirmations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>

<!-- {{ __('app.production_confirmations.details_modal_title') }} -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    {{ __('app.production_confirmations.details_modal_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ __('app.production_confirmations.loading') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- {{ __('app.production_confirmations.confirm_modal_title') }} -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ __('app.production_confirmations.confirm_modal_title') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="confirmForm">
                <div class="modal-body">
                    <input type="hidden" id="confirmId">

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('app.production_confirmations.actual_quantity_label') }} <span class="text-danger">*</span></label>
                        <input type="number" id="actualQuantity" class="form-control" step="0.01" min="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('app.production_confirmations.optional_notes') }}</label>
                        <textarea id="confirmNotes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> {{ __('app.production_confirmations.cancel_btn') }}
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> {{ __('app.production_confirmations.confirm_btn') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- {{ __('app.production_confirmations.reject_modal_title') }} -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle me-2"></i>
                    {{ __('app.production_confirmations.reject_modal_title') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" id="rejectId">

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ __('app.production_confirmations.reject_warning') }}
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('app.production_confirmations.reason_required') }} <span class="text-danger">*</span></label>
                        <textarea id="rejectReason" class="form-control" rows="4" required placeholder="{{ __('app.production_confirmations.rejection_reason_desc') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('app.production_confirmations.cancel_btn') }}
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> {{ __('app.production_confirmations.reject_btn') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* تنسيق الأزرار */
.btn-group .btn {
    min-width: 38px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 0;
    transition: all 0.2s ease;
}

.btn-group .btn:first-child {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

.btn-group .btn:last-child {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    z-index: 1;
}

/* تحسين مظهر الـ Modal */
.modal-header {
    border-bottom: 2px solid rgba(0,0,0,0.1);
}

.modal-footer {
    border-top: 2px solid rgba(0,0,0,0.1);
    gap: 0.5rem;
}

.modal-footer .btn {
    min-width: 120px;
    font-weight: 600;
}

/* تحسين مظهر الجدول */
.table tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

.badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
}
</style>
@endpush

@push('scripts')
<script>
// Initialize Bootstrap modals
let detailsModal, confirmModal, rejectModal;

document.addEventListener('DOMContentLoaded', function() {
    detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
    confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
});

function showDetails(confirmationId) {
    const content = document.getElementById('modalContent');
    content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"><span class="visually-hidden">{{ __('app.production_confirmations.loading_details') }}</span></div></div>';

    detailsModal.show();

    fetch(`{{ url('/manufacturing/production/confirmations') }}/${confirmationId}/details`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) throw new Error(data.message);

            const conf = data.confirmation;
            content.innerHTML = `
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block mb-1">{{ __('app.production_confirmations.batch_code_label') }}</small>
                            <div class="fw-bold">${conf.batch_code}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block mb-1">{{ __('app.production_confirmations.material_label') }}</small>
                            <div class="fw-bold">${conf.material_name}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block mb-1">{{ __('app.production_confirmations.final_weight') }}</small>
                            <div class="fw-bold text-success">${conf.quantity} {{ __('app.units.kg') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block mb-1">{{ __('app.production_confirmations.production_stage') }}</small>
                            <div class="fw-bold text-info">${conf.stage_name}</div>
                        </div>
                    </div>
                </div>

                ${conf.production_barcode ? `
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-barcode me-2"></i>
                            {{ __('app.production_confirmations.production_barcode') }}
                        </div>
                        <div class="card-body text-center bg-light">
                            <img src="data:image/png;base64,${conf.production_barcode}" alt="Barcode" class="img-fluid" style="max-height: 100px;">
                        </div>
                    </div>
                ` : ''}

                <div class="text-muted">
                    <i class="fas fa-calendar me-2"></i>
                    {{ __('app.production_confirmations.created_at') }}: ${conf.created_at}
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ __('app.production_confirmations.error_loading') }}
                </div>
            `;
        });
}

function confirmReceipt(confirmationId) {
    document.getElementById('confirmId').value = confirmationId;

    fetch(`{{ url('/manufacturing/production/confirmations') }}/${confirmationId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('actualQuantity').value = data.confirmation.quantity;
            }
        });

    confirmModal.show();
}

function rejectReceipt(confirmationId) {
    document.getElementById('rejectId').value = confirmationId;
    rejectModal.show();
}

// Handle Confirm Form
document.getElementById('confirmForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> {{ __('app.production_confirmations.confirming') }}';
    btn.classList.add('disabled');

    const confirmId = document.getElementById('confirmId').value;
    const actualQuantity = document.getElementById('actualQuantity').value;
    const notes = document.getElementById('confirmNotes').value;

    fetch(`{{ url('manufacturing/production/confirmations') }}/${confirmId}/confirm`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            actual_quantity: actualQuantity,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            confirmModal.hide();
            alert('✓ {{ __('app.production_confirmations.confirm_success') }}');
            location.reload();
        } else {
            alert('❌ ' + data.message);
            btn.disabled = false;
            btn.classList.remove('disabled');
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        alert('❌ {{ __('app.production_confirmations.error_confirm') }}');
        btn.disabled = false;
        btn.classList.remove('disabled');
        btn.innerHTML = originalText;
    });
});

// Handle Reject Form
document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> {{ __('app.production_confirmations.rejecting') }}';
    btn.classList.add('disabled');

    const rejectId = document.getElementById('rejectId').value;
    const reason = document.getElementById('rejectReason').value;

    fetch(`{{ url('manufacturing/production/confirmations') }}/${rejectId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            rejection_reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            rejectModal.hide();
            alert('✓ {{ __('app.production_confirmations.reject_success') }}');
            location.reload();
        } else {
            alert('❌ ' + data.message);
            btn.disabled = false;
            btn.classList.remove('disabled');
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        alert('❌ {{ __('app.production_confirmations.error_reject') }}');
        btn.disabled = false;
        btn.classList.remove('disabled');
        btn.innerHTML = originalText;
    });
});
</script>
@endpush

    @if($pendingConfirmations->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد طلبات معلقة</h5>
                        <p class="text-muted">جميع الطلبات المسندة إليك تم تأكيدها أو رفضها</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center" style="white-space: nowrap; font-size: 13px;">#</th>
                                        <th class="text-center" style="white-space: nowrap; font-size: 13px;">رمز الدفعة</th>
                                        <th style="white-space: nowrap; font-size: 13px;">المادة</th>
                                        <th class="text-center" style="white-space: nowrap; font-size: 13px;">الكمية</th>
                                        <th class="text-center" style="white-space: nowrap; font-size: 13px;">المرحلة</th>
                                        <th class="text-center" style="white-space: nowrap; font-size: 13px;">التاريخ</th>
                                        <th class="text-center" style="white-space: nowrap; font-size: 13px;">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingConfirmations as $index => $confirmation)
                                        <tr>
                                            <td class="text-center" style="font-size: 13px;">{{ $pendingConfirmations->firstItem() + $index }}</td>

                                            <td class="text-center">
                                                <span class="badge bg-primary" style="font-size: 11px;">{{ $confirmation->batch->batch_code }}</span>
                                            </td>

                                            <td style="max-width: 200px;">
                                                <div class="fw-bold" style="font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $confirmation->batch->material->name }}">{{ $confirmation->batch->material->name }}</div>
                                                <small class="text-muted" style="font-size: 11px;">{{ $confirmation->batch->material->category }}</small>
                                            </td>

                                            <td class="text-center">
                                                <span class="fw-bold" style="font-size: 13px;">{{ number_format($confirmation->actual_received_quantity ?? 0, 2) }}</span>
                                                <small class="text-muted d-block" style="font-size: 10px;">كجم</small>
                                            </td>

                                            <td class="text-center">
                                                <span class="badge bg-info" style="font-size: 11px; white-space: nowrap;">{{ $confirmation->deliveryNote->production_stage_name }}</span>
                                            </td>

                                            <td class="text-center text-muted" style="white-space: nowrap;">
                                                <div style="font-size: 12px;">{{ $confirmation->created_at->format('Y/m/d') }}</div>
                                                <small style="font-size: 10px;">{{ $confirmation->created_at->format('h:i A') }}</small>
                                            </td>

                                            <td class="text-center">
                                                <div class="btn-group" role="group" style="white-space: nowrap;">
                                                    <button onclick="showDetails({{ $confirmation->id }})" class="btn btn-sm btn-info" title="التفاصيل" style="padding: 4px 8px;">
                                                        <i class="fas fa-eye" style="font-size: 12px;"></i>
                                                    </button>
                                                    <button onclick="confirmReceipt({{ $confirmation->id }})" class="btn btn-sm btn-success" title="تأكيد الاستلام" style="padding: 4px 8px;">
                                                        <i class="fas fa-check" style="font-size: 12px;"></i>
                                                    </button>
                                                    <button onclick="rejectReceipt({{ $confirmation->id }})" class="btn btn-sm btn-danger" title="رفض الاستلام" style="padding: 4px 8px;">
                                                        <i class="fas fa-times" style="font-size: 12px;"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($pendingConfirmations->hasPages())
                        <div class="card-footer bg-white">
                            {{ $pendingConfirmations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>

<!-- Modal التفاصيل -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    تفاصيل الدفعة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal التأكيد -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>
                    تأكيد استلام الدفعة
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="confirmForm">
                <div class="modal-body">
                    <input type="hidden" id="confirmId">

                    <div class="mb-3">
                        <label class="form-label fw-bold">الكمية المستلمة فعلياً (كجم) <span class="text-danger">*</span></label>
                        <input type="number" id="actualQuantity" class="form-control" step="0.01" min="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">ملاحظات (اختياري)</label>
                        <textarea id="confirmNotes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> تأكيد الاستلام
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
                    <i class="fas fa-times-circle me-2"></i>
                    رفض استلام الدفعة
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" id="rejectId">

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        عند الرفض، ستعود الكمية للمستودع تلقائياً
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">سبب الرفض <span class="text-danger">*</span></label>
                        <textarea id="rejectReason" class="form-control" rows="4" required placeholder="يرجى توضيح سبب رفض استلام الدفعة..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-arrow-left me-1"></i> إلغاء
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> تأكيد الرفض
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* تنسيق الأزرار */
.btn-group .btn {
    min-width: 38px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 0;
    transition: all 0.2s ease;
}

.btn-group .btn:first-child {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

.btn-group .btn:last-child {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    z-index: 1;
}

/* تحسين مظهر الـ Modal */
.modal-header {
    border-bottom: 2px solid rgba(0,0,0,0.1);
}

.modal-footer {
    border-top: 2px solid rgba(0,0,0,0.1);
    gap: 0.5rem;
}

.modal-footer .btn {
    min-width: 120px;
    font-weight: 600;
}

/* تحسين مظهر الجدول */
.table tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

.badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
}
</style>
@endpush

@push('scripts')
<script>
// Initialize Bootstrap modals
let detailsModal, confirmModal, rejectModal;

document.addEventListener('DOMContentLoaded', function() {
    detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
    confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
});

function showDetails(confirmationId) {
    const content = document.getElementById('modalContent');
    content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"><span class="visually-hidden">جاري التحميل...</span></div></div>';

    detailsModal.show();

    fetch(`{{ url('/manufacturing/production/confirmations') }}/${confirmationId}/details`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) throw new Error(data.message);

            const conf = data.confirmation;
            content.innerHTML = `
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block mb-1">رمز الدفعة</small>
                            <div class="fw-bold">${conf.batch_code}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block mb-1">المادة</small>
                            <div class="fw-bold">${conf.material_name}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block mb-1">الكمية</small>
                            <div class="fw-bold text-success">${conf.quantity} كجم</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <small class="text-muted d-block mb-1">المرحلة</small>
                            <div class="fw-bold text-info">${conf.stage_name}</div>
                        </div>
                    </div>
                </div>

                ${conf.production_barcode ? `
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-barcode me-2"></i>
                            الباركود الإنتاجي
                        </div>
                        <div class="card-body text-center bg-light">
                            <img src="data:image/png;base64,${conf.production_barcode}" alt="Barcode" class="img-fluid" style="max-height: 100px;">
                        </div>
                    </div>
                ` : ''}

                <div class="text-muted">
                    <i class="fas fa-calendar me-2"></i>
                    تاريخ الطلب: ${conf.created_at}
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    حدث خطأ أثناء تحميل التفاصيل
                </div>
            `;
        });
}

function confirmReceipt(confirmationId) {
    document.getElementById('confirmId').value = confirmationId;

    fetch(`{{ url('/manufacturing/production/confirmations') }}/${confirmationId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('actualQuantity').value = data.confirmation.quantity;
            }
        });

    confirmModal.show();
}

function rejectReceipt(confirmationId) {
    document.getElementById('rejectId').value = confirmationId;
    rejectModal.show();
}

// Handle Confirm Form
document.getElementById('confirmForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> {{ __('app.production_confirmations.confirming') }}';
    btn.classList.add('disabled');

    const confirmId = document.getElementById('confirmId').value;
    const actualQuantity = document.getElementById('actualQuantity').value;
    const notes = document.getElementById('confirmNotes').value;

    fetch(`{{ url('manufacturing/production/confirmations') }}/${confirmId}/confirm`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            actual_quantity: actualQuantity,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            confirmModal.hide();
            alert('✓ {{ __('app.production_confirmations.confirm_success') }}');
            location.reload();
        } else {
            alert('❌ ' + data.message);
            btn.disabled = false;
            btn.classList.remove('disabled');
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        alert('❌ {{ __('app.production_confirmations.error_confirm') }}');
        btn.disabled = false;
        btn.classList.remove('disabled');
        btn.innerHTML = originalText;
    });
});

// Handle Reject Form
document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> {{ __('app.production_confirmations.rejecting') }}';
    btn.classList.add('disabled');

    const rejectId = document.getElementById('rejectId').value;
    const reason = document.getElementById('rejectReason').value;

    fetch(`{{ url('manufacturing/production/confirmations') }}/${rejectId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            rejection_reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            rejectModal.hide();
            alert('✓ {{ __('app.production_confirmations.reject_success') }}');
            location.reload();
        } else {
            alert('❌ ' + data.message);
            btn.disabled = false;
            btn.classList.remove('disabled');
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        alert('❌ {{ __('app.production_confirmations.error_reject') }}');
        btn.disabled = false;
        btn.classList.remove('disabled');
        btn.innerHTML = originalText;
    });
});
</script>
@endpush
