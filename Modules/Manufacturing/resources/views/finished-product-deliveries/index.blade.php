@extends('master')

@section('title', 'إذونات صرف المنتجات النهائية')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">
                <i class="bi bi-box-seam me-2"></i>
                إذونات صرف المنتجات النهائية
            </h2>
        </div>
        <div class="col-md-6 text-end">
            @if(Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_CREATE'))
            <a href="{{ route('manufacturing.finished-product-deliveries.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>
                إنشاء إذن صرف جديد
            </a>
            @endif
            
            @if(Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_APPROVE'))
            <a href="{{ route('manufacturing.finished-product-deliveries.pending-approval') }}" class="btn btn-warning">
                <i class="bi bi-clock-history me-1"></i>
                الإذونات المعلقة
                @if(isset($pendingCount) && $pendingCount > 0)
                <span class="badge bg-danger">{{ $pendingCount }}</span>
                @endif
            </a>
            @endif
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('manufacturing.finished-product-deliveries.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">البحث</label>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                           placeholder="رقم الإذن أو رمز العميل">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">الحالة</label>
                    <select class="form-select" name="status">
                        <option value="">الكل</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                            @if($status->value == 'pending') قيد الانتظار
                            @elseif($status->value == 'approved') معتمد
                            @elseif($status->value == 'rejected') مرفوض
                            @elseif($status->value == 'completed') مكتمل
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">العميل</label>
                    <select class="form-select" name="customer_id">
                        <option value="">الكل</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">إجمالي الإذونات</h6>
                            <h3 class="mb-0">{{ $deliveryNotes->total() }}</h3>
                        </div>
                        <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">قيد الانتظار</h6>
                            <h3 class="mb-0">{{ $deliveryNotes->where('status', 'pending')->count() }}</h3>
                        </div>
                        <i class="bi bi-clock-history" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">معتمد</h6>
                            <h3 class="mb-0">{{ $deliveryNotes->where('status', 'approved')->count() }}</h3>
                        </div>
                        <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">مرفوض</h6>
                            <h3 class="mb-0">{{ $deliveryNotes->where('status', 'rejected')->count() }}</h3>
                        </div>
                        <i class="bi bi-x-circle" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Notes Table -->
    <div class="card">
        <div class="card-body">
            @if($deliveryNotes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>رقم الإذن</th>
                            <th>العميل</th>
                            <th>عدد الصناديق</th>
                            <th>الوزن الإجمالي</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>المُنشئ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveryNotes as $note)
                        <tr>
                            <td>
                                <strong class="text-primary">{{ $note->note_number ?? '#' . $note->id }}</strong>
                            </td>
                            <td>
                                @if($note->customer)
                                <div>
                                    <strong>{{ $note->customer->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $note->customer->customer_code }}</small>
                                </div>
                                @else
                                <span class="badge bg-secondary">لم يحدد بعد</span>
                                @endif
                            </td>
                            <td>
                                <i class="bi bi-box me-1"></i>
                                {{ $note->items->count() }}
                            </td>
                            <td>
                                <strong>{{ number_format($note->items->sum('weight'), 2) }}</strong> كجم
                            </td>
                            <td>
                                @if($note->status == 'pending')
                                    <span class="badge bg-warning">قيد الانتظار</span>
                                @elseif($note->status == 'approved')
                                    <span class="badge bg-success">معتمد</span>
                                @elseif($note->status == 'rejected')
                                    <span class="badge bg-danger">مرفوض</span>
                                @elseif($note->status == 'completed')
                                    <span class="badge bg-info">مكتمل</span>
                                @endif
                            </td>
                            <td>{{ $note->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($note->recordedBy)
                                {{ $note->recordedBy->name }}
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if(Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_READ'))
                                    <a href="{{ route('manufacturing.finished-product-deliveries.show', $note->id) }}" 
                                       class="btn btn-sm btn-info" title="عرض التفاصيل">
                                        <i class="bi bi-eye me-1"></i>عرض
                                    </a>
                                    @endif

                                    @if(Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_UPDATE') && $note->status == 'pending' && $note->prepared_by == Auth::id())
                                    <a href="{{ route('manufacturing.finished-product-deliveries.edit', $note->id) }}" 
                                       class="btn btn-sm btn-primary" title="تعديل">
                                        <i class="bi bi-pencil me-1"></i>تعديل
                                    </a>
                                    @endif

                                    @if(Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_PRINT') && $note->status == 'approved')
                                    <a href="{{ route('manufacturing.finished-product-deliveries.print', $note->id) }}" 
                                       class="btn btn-sm btn-secondary" target="_blank" title="طباعة">
                                        <i class="bi bi-printer me-1"></i>طباعة
                                        @if($note->print_count > 0)
                                        <span class="badge bg-light text-dark ms-1">{{ $note->print_count }}</span>
                                        @endif
                                    </a>
                                    @endif

                                    @if(Auth::user()->hasPermission('FINISHED_PRODUCT_DELIVERIES_DELETE') && in_array($note->status, ['pending', 'rejected']))
                                    <button type="button" class="btn btn-sm btn-danger delete-note" 
                                            data-id="{{ $note->id }}" title="حذف">
                                        <i class="bi bi-trash me-1"></i>حذف
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $deliveryNotes->links() }}
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                لا توجد إذونات صرف
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // حذف إذن صرف
    $(document).on('click', '.delete-note', function() {
        const noteId = $(this).data('id');
        
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم حذف إذن الصرف وإعادة حالة الصناديق',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'نعم، حذف',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/manufacturing/finished-product-deliveries/${noteId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('تم الحذف!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('خطأ', xhr.responseJSON?.error || 'فشل حذف إذن الصرف', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
