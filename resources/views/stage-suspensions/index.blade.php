@extends('master')

@section('title', 'المراحل الموقوفة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="fas fa-pause-circle me-2 text-danger"></i>
                المراحل الموقوفة - تجاوز نسبة الهدر
            </h2>
            <p class="text-muted mb-0">مراقبة واعتماد استئناف المراحل التي تجاوزت نسبة الهدر المسموح بها</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
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
                            <h6 class="card-title mb-0">موقوفة</h6>
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
                            <h6 class="card-title mb-0">تمت الموافقة</h6>
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
                            <h6 class="card-title mb-0">مرفوضة</h6>
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
                            <h6 class="card-title mb-0">الإجمالي</h6>
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
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">الكل</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>موقوفة</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>تمت الموافقة</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">المرحلة</label>
                    <select name="stage" class="form-select">
                        <option value="">الكل</option>
                        <option value="1" {{ request('stage') == '1' ? 'selected' : '' }}>المرحلة الأولى</option>
                        <option value="2" {{ request('stage') == '2' ? 'selected' : '' }}>المرحلة الثانية</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> تصفية
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Suspensions Table -->
    <div class="card">
        <div class="card-body">
            @if($suspensions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>المرحلة</th>
                            <th>الباركود</th>
                            <th>الوزن المدخل</th>
                            <th>الوزن الناتج</th>
                            <th>وزن الهدر</th>
                            <th>نسبة الهدر</th>
                            <th>النسبة المسموح</th>
                            <th>الحالة</th>
                            <th>تاريخ الإيقاف</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suspensions as $suspension)
                        <tr>
                            <td><strong>{{ $suspension->getStageName() }}</strong></td>
                            <td><code>{{ $suspension->batch_barcode }}</code></td>
                            <td>{{ number_format($suspension->input_weight, 2) }} كجم</td>
                            <td>{{ number_format($suspension->output_weight, 2) }} كجم</td>
                            <td class="text-danger">
                                <strong>{{ number_format($suspension->waste_weight, 2) }} كجم</strong>
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
                                       class="btn btn-sm btn-info" title="عرض التفاصيل">
                                        <i class="bi bi-eye me-1"></i>عرض
                                    </a>
                                    
                                    @if($suspension->status == 'suspended' && Auth::user()->hasPermission('STAGE_SUSPENSION_APPROVE'))
                                    <button type="button" 
                                            class="btn btn-sm btn-success approve-btn" 
                                            data-id="{{ $suspension->id }}"
                                            title="الموافقة على الاستئناف">
                                        <i class="bi bi-check-circle me-1"></i>موافقة
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger reject-btn" 
                                            data-id="{{ $suspension->id }}"
                                            title="رفض">
                                        <i class="bi bi-x-circle me-1"></i>رفض
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
                لا توجد مراحل موقوفة
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>
                        الموافقة على استئناف المرحلة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        هل أنت متأكد من الموافقة على استئناف هذه المرحلة؟
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات (اختياري)</label>
                        <textarea name="review_notes" class="form-control" rows="3" placeholder="أضف ملاحظاتك هنا..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>تأكيد الموافقة
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
                        رفض الطلب
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        يرجى توضيح سبب الرفض
                    </div>
                    <div class="mb-3">
                        <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                        <textarea name="review_notes" class="form-control" rows="3" required placeholder="اذكر سبب رفض الطلب..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i>تأكيد الرفض
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
