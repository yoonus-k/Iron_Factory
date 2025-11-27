@extends('master')

@section('title', 'تعديل إذن الصرف')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="bi bi-pencil-square me-2"></i>
                تعديل إذن الصرف
            </h2>
            <p class="text-muted mb-0">
                رقم الإذن: <strong class="text-primary">{{ $deliveryNote->note_number ?? '#' . $deliveryNote->id }}</strong>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manufacturing.finished-product-deliveries.show', $deliveryNote->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right me-1"></i>
                العودة للتفاصيل
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil me-2"></i>
                        تعديل البيانات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>ملاحظة:</strong> يمكن تعديل العميل والملاحظات فقط. لا يمكن تعديل الصناديق بعد الإنشاء.
                    </div>

                    <form id="editForm" method="POST" action="{{ route('manufacturing.finished-product-deliveries.update', $deliveryNote->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- العميل -->
                        <div class="mb-3">
                            <label class="form-label">العميل</label>
                            <select name="customer_id" class="form-select">
                                <option value="">يمكن تحديده لاحقاً من قبل المدير</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $deliveryNote->customer_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->customer_code }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">إذا لم يتم التحديد، سيقوم المدير بتحديده عند الاعتماد</small>
                        </div>

                        <!-- الملاحظات -->
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="4" 
                                      placeholder="أضف ملاحظات إن وجدت">{{ $deliveryNote->notes }}</textarea>
                        </div>

                        <!-- الأزرار -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                حفظ التعديلات
                            </button>
                            <a href="{{ route('manufacturing.finished-product-deliveries.show', $deliveryNote->id) }}" 
                               class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>
                                إلغاء
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
                    <h6 class="mb-0">الصناديق المحددة</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>عدد الصناديق:</span>
                            <strong>{{ $deliveryNote->items->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>الوزن الإجمالي:</span>
                            <strong>{{ number_format($deliveryNote->items->sum('weight'), 2) }} كجم</strong>
                        </div>
                    </div>

                    <hr>

                    <div style="max-height: 400px; overflow-y: auto;">
                        @foreach($deliveryNote->items as $index => $item)
                        <div class="alert alert-light py-2 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $item->barcode }}</strong><br>
                                    <small>{{ number_format($item->weight, 2) }} كجم</small>
                                </div>
                                <i class="bi bi-lock text-muted" title="لا يمكن التعديل"></i>
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
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> جاري الحفظ...');

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
                        title: 'نجح!',
                        text: response.message,
                        confirmButtonText: 'حسناً'
                    }).then(() => {
                        window.location.href = '{{ route("manufacturing.finished-product-deliveries.show", $deliveryNote->id) }}';
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'حدث خطأ أثناء الحفظ';
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: errorMessage
                });
                
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endpush
@endsection
