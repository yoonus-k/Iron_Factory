@extends('master')

@section('title', 'لوحة التسوية التفصيلية')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-auto">
                <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn btn-secondary">
                    ← رجوع
                </a>
            </div>
            <div class="col">
                <h1 class="page-title">🔄 تسوية التسليمة #{{ $deliveryNote->note_number ?? $deliveryNote->id }}</h1>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- معلومات الشحنة -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">📦 معلومات الشحنة</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted">الرقم:</label>
                        <p><strong>#{{ $deliveryNote->note_number ?? $deliveryNote->id }}</strong></p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">المورد:</label>
                        <p><strong>{{ $deliveryNote->supplier->name }}</strong></p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">الوزن الفعلي:</label>
                        <p>
                            <strong class="text-success">
                                {{ number_format($deliveryNote->actual_weight, 2) }} كيلو
                            </strong>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">تاريخ التسليم:</label>
                        <p>{{ $deliveryNote->created_at ? $deliveryNote->created_at->format('d/m/Y') : '-' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">مسجل بواسطة:</label>
                        <p>{{ $deliveryNote->registeredBy ? $deliveryNote->registeredBy->name : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات الفاتورة -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">📄 معلومات الفاتورة</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted">رقم الفاتورة:</label>
                        <p>
                            <strong>{{ $deliveryNote->purchaseInvoice->invoice_number }}</strong>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">المورد:</label>
                        <p>{{ $deliveryNote->purchaseInvoice->supplier->name }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">الوزن المكتوب:</label>
                        <p>
                            <strong class="text-primary">
                                {{ number_format($deliveryNote->invoice_weight, 2) }} كيلو
                            </strong>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">تاريخ الفاتورة:</label>
                        <p>{{ $deliveryNote->invoice_date ? \Carbon\Carbon::parse($deliveryNote->invoice_date)->format('d/m/Y') : '-' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">المبلغ الإجمالي:</label>
                        <p>
                            {{ number_format($deliveryNote->purchaseInvoice->total_amount, 2) }}
                            {{ $deliveryNote->purchaseInvoice->currency }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- المقارنة والفرق -->
        <div class="col-lg-4">
            <div class="card mb-4 border-{{ $deliveryNote->weight_discrepancy > 0 ? 'danger' : 'success' }}">
                <div class="card-header">
                    <h5 class="mb-0">⚖️ المقارنة والفرق</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted">الفرق (كيلو):</label>
                        <p>
                            <strong class="text-{{ $deliveryNote->weight_discrepancy > 0 ? 'danger' : 'success' }} fs-5">
                                {{ $deliveryNote->weight_discrepancy > 0 ? '+ ' : '- ' }}
                                {{ number_format(abs($deliveryNote->weight_discrepancy), 2) }} كيلو
                            </strong>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">النسبة المئوية:</label>
                        <p>
                            <strong class="text-{{ abs($deliveryNote->discrepancy_percentage) > 5 ? 'danger' : 'warning' }} fs-5">
                                {{ $deliveryNote->discrepancy_percentage > 0 ? '+ ' : '- ' }}
                                {{ number_format(abs($deliveryNote->discrepancy_percentage), 2) }}%
                            </strong>
                        </p>
                    </div>

                    @if ($deliveryNote->weight_discrepancy > 0)
                        <div class="alert alert-danger">
                            <strong>⚠️ المورد كاتب أكثر!</strong>
                            <br>
                            <small>المورد كتب {{ number_format($deliveryNote->weight_discrepancy, 2) }} كيلو زيادة</small>
                        </div>
                    @elseif ($deliveryNote->weight_discrepancy < 0)
                        <div class="alert alert-success">
                            <strong>✅ في صالحنا!</strong>
                            <br>
                            <small>المورد كتب {{ number_format(abs($deliveryNote->weight_discrepancy), 2) }} كيلو أقل</small>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <strong>✅ متطابق تماماً!</strong>
                            <br>
                            <small>لا فروقات</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- جدول المقارنة -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">📊 جدول المقارنة التفصيلي</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>البيان</th>
                            <th class="text-center" style="background-color: #e7f3ff;">الفعلي (الميزان)</th>
                            <th class="text-center" style="background-color: #fff3e7;">الفاتورة (المورد)</th>
                            <th class="text-center" style="background-color: #ffe7e7;">الفرق</th>
                            <th class="text-center">النسبة %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>الوزن (كيلو)</strong></td>
                            <td class="text-center text-success">
                                <strong>{{ number_format($deliveryNote->actual_weight, 2) }}</strong>
                            </td>
                            <td class="text-center text-primary">
                                <strong>{{ number_format($deliveryNote->invoice_weight, 2) }}</strong>
                            </td>
                            <td class="text-center">
                                <strong class="text-{{ $deliveryNote->weight_discrepancy > 0 ? 'danger' : 'success' }}">
                                    {{ $deliveryNote->weight_discrepancy > 0 ? '+ ' : '- ' }}
                                    {{ number_format(abs($deliveryNote->weight_discrepancy), 2) }}
                                </strong>
                            </td>
                            <td class="text-center">
                                <strong class="text-{{ abs($deliveryNote->discrepancy_percentage) > 5 ? 'danger' : 'warning' }}">
                                    {{ $deliveryNote->discrepancy_percentage > 0 ? '+ ' : '- ' }}
                                    {{ number_format(abs($deliveryNote->discrepancy_percentage), 2) }}%
                                </strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- نموذج اتخاذ القرار -->
    @if ($canReconcile)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">⚙️ اتخاذ القرار</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('manufacturing.warehouses.reconciliation.decide', $deliveryNote) }}" method="POST" id="decideForm">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label">اختر القرار <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action" value="accepted" id="actionAccept" required>
                                        <label class="form-check-label" for="actionAccept">
                                            <strong>✓ قبول الفرق</strong>
                                            <br>
                                            <small class="text-muted">نقبل الفاتورة كما هي حتى مع الفرق</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action" value="rejected" id="actionReject" required>
                                        <label class="form-check-label" for="actionReject">
                                            <strong>✗ رفض الفاتورة</strong>
                                            <br>
                                            <small class="text-muted">ترجع الفاتورة للمورد للتصحيح</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action" value="adjusted" id="actionAdjust" required>
                                        <label class="form-check-label" for="actionAdjust">
                                            <strong>🔧 تعديل البيانات</strong>
                                            <br>
                                            <small class="text-muted">تعديل الوزن يدوياً</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- حقل الوزن المعدّل (يظهر عند اختيار تعديل) -->
                    <div class="row mb-4" id="adjustedWeightDiv" style="display:none;">
                        <div class="col-md-6">
                            <label class="form-label">الوزن المعدّل (كيلو):</label>
                            <input type="number"
                                   name="adjusted_weight"
                                   class="form-control"
                                   step="0.01"
                                   min="0.01"
                                   placeholder="الوزن الجديد">
                        </div>
                    </div>

                    <!-- السبب -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">السبب <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="reason"
                                   class="form-control"
                                   placeholder="مثال: فرق عادي، خطأ في الميزان، إلخ"
                                   required>
                        </div>
                    </div>

                    <!-- ملاحظات إضافية -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label">ملاحظات إضافية (اختيارية):</label>
                            <textarea name="comments"
                                      class="form-control"
                                      rows="3"
                                      placeholder="أي تفاصيل إضافية تريد تسجيلها"></textarea>
                        </div>
                    </div>

                    <!-- الأزرار -->
                    <div class="row">
                        <div class="col-auto">
                            <button type="submit" class="btn btn-lg btn-success">
                                💾 حفظ القرار
                            </button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn btn-lg btn-secondary">
                                ✗ إلغاء
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            ℹ️ هذه التسليمة مسوّاة بالفعل ولا يمكن تعديل قرارها
        </div>
    @endif

    <!-- سجل التسويات -->
    @if ($deliveryNote->reconciliationLogs->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">📚 سجل القرارات السابقة</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>التاريخ</th>
                                <th>القرار</th>
                                <th>السبب</th>
                                <th>من</th>
                                <th>الملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deliveryNote->reconciliationLogs as $log)
                                <tr>
                                    <td>{{ $log->decided_at ? $log->decided_at->format('d/m/Y H:i') : ($log->created_at ? $log->created_at->format('d/m/Y H:i') : '-') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $log->action === 'accepted' ? 'success' : ($log->action === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ $log->action ?? 'pending' }}
                                        </span>
                                    </td>
                                    <td>{{ $log->reason ?? '-' }}</td>
                                    <td>{{ $log->decidedBy ? $log->decidedBy->name : ($log->createdBy ? $log->createdBy->name : '-') }}</td>
                                    <td><small>{{ $log->comments ?? $log->notes ?? '-' }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
// إظهار/إخفاء حقل الوزن المعدّل
document.querySelectorAll('input[name="action"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const adjustedWeightDiv = document.getElementById('adjustedWeightDiv');
        const adjustedWeightInput = document.querySelector('input[name="adjusted_weight"]');

        if (this.value === 'adjusted') {
            adjustedWeightDiv.style.display = 'flex';
            adjustedWeightInput.required = true;
        } else {
            adjustedWeightDiv.style.display = 'none';
            adjustedWeightInput.required = false;
            adjustedWeightInput.value = '';
        }
    });
});
</script>
@endsection
