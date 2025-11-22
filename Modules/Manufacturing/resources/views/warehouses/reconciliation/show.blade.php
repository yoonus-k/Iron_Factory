@extends('master')

@section('title', 'لوحة التسوية التفصيلية')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="page-header-card mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> رجوع
                </a>
            </div>
            <div class="col">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-0">تسوية التسليمة #{{ $deliveryNote->note_number ?? $deliveryNote->id }}</h1>
                        <p class="text-white-50 mb-0 mt-1">مقارنة الوزن الفعلي مع الفاتورة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-success-custom mb-4">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-3">
        <!-- معلومات الشحنة -->
        <div class="col-lg-4">
            <div class="info-card">
                <div class="info-card-header">
                    <i class="fas fa-box"></i>
                    <h5 class="mb-0">معلومات الشحنة</h5>
                </div>
                <div class="info-card-body">
                    <div class="info-item">
                        <label>الرقم:</label>
                        <p><strong>#{{ $deliveryNote->note_number ?? $deliveryNote->id }}</strong></p>
                    </div>

                    <div class="info-item">
                        <label>المورد:</label>
                        <p><strong>{{ $deliveryNote->supplier->name }}</strong></p>
                    </div>

                    <div class="info-item">
                        <label>الوزن الفعلي:</label>
                        <p>
                            <span class="badge-success-custom">
                                {{ number_format($deliveryNote->actual_weight, 2) }} كيلو
                            </span>
                        </p>
                    </div>

                    <div class="info-item">
                        <label>تاريخ التسليم:</label>
                        <p>{{ $deliveryNote->created_at ? $deliveryNote->created_at->format('d/m/Y') : '-' }}</p>
                    </div>

                    <div class="info-item">
                        <label>مسجل بواسطة:</label>
                        <p>{{ $deliveryNote->registeredBy ? $deliveryNote->registeredBy->name : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات الفاتورة -->
        <div class="col-lg-4">
            <div class="info-card">
                <div class="info-card-header">
                    <i class="fas fa-file-invoice"></i>
                    <h5 class="mb-0">معلومات الفاتورة</h5>
                </div>
                <div class="info-card-body">
                    <div class="info-item">
                        <label>رقم الفاتورة:</label>
                        <p>
                            <strong>{{ $deliveryNote->purchaseInvoice->invoice_number }}</strong>
                        </p>
                    </div>

                    <div class="info-item">
                        <label>المورد:</label>
                        <p>{{ $deliveryNote->purchaseInvoice->supplier->name }}</p>
                    </div>

                    <div class="info-item">
                        <label>الوزن المكتوب:</label>
                        <p>
                            <span class="badge-primary-custom">
                                {{ number_format($deliveryNote->invoice_weight, 2) }} كيلو
                            </span>
                        </p>
                    </div>

                    <div class="info-item">
                        <label>تاريخ الفاتورة:</label>
                        <p>{{ $deliveryNote->invoice_date ? \Carbon\Carbon::parse($deliveryNote->invoice_date)->format('d/m/Y') : '-' }}</p>
                    </div>

                    <div class="info-item">
                        <label>المبلغ الإجمالي:</label>
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
            <div class="comparison-card {{ $deliveryNote->weight_discrepancy > 0 ? 'danger-border' : 'success-border' }}">
                <div class="comparison-card-header {{ $deliveryNote->weight_discrepancy > 0 ? 'danger-bg' : 'success-bg' }}">
                    <i class="fas fa-balance-scale-right"></i>
                    <h5 class="mb-0">المقارنة والفرق</h5>
                </div>
                <div class="comparison-card-body">
                    <div class="comparison-item">
                        <label>الفرق (كيلو):</label>
                        <div class="comparison-value {{ $deliveryNote->weight_discrepancy > 0 ? 'danger' : 'success' }}">
                            {{ $deliveryNote->weight_discrepancy > 0 ? '+ ' : '- ' }}
                            {{ number_format(abs($deliveryNote->weight_discrepancy), 2) }} كيلو
                        </div>
                    </div>

                    <div class="comparison-item">
                        <label>النسبة المئوية:</label>
                        <div class="comparison-value {{ abs($deliveryNote->discrepancy_percentage) > 5 ? 'danger' : 'warning' }}">
                            {{ $deliveryNote->discrepancy_percentage > 0 ? '+ ' : '- ' }}
                            {{ number_format(min(abs($deliveryNote->discrepancy_percentage), 100), 2) }}%
                        </div>
                    </div>

                    @if ($deliveryNote->weight_discrepancy > 0)
                        <div class="alert-custom alert-danger-custom">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>المورد كاتب أكثر!</strong>
                                <small>المورد كتب {{ number_format($deliveryNote->weight_discrepancy, 2) }} كيلو زيادة</small>
                            </div>
                        </div>
                    @elseif ($deliveryNote->weight_discrepancy < 0)
                        <div class="alert-custom alert-success-custom-2">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>في صالحنا!</strong>
                                <small>المورد كتب {{ number_format(abs($deliveryNote->weight_discrepancy), 2) }} كيلو أقل</small>
                            </div>
                        </div>
                    @else
                        <div class="alert-custom alert-success-custom-2">
                            <i class="fas fa-check-double"></i>
                            <div>
                                <strong>متطابق تماماً!</strong>
                                <small>لا فروقات</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- جدول المقارنة -->
    <div class="data-card mb-4">
        <div class="data-card-header">
            <i class="fas fa-table"></i>
            <h5 class="mb-0">جدول المقارنة التفصيلي</h5>
        </div>
        <div class="data-card-body p-4">
            <div class="table-responsive">
                <table class="table custom-table table-bordered">
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
                                    {{ number_format(min(abs($deliveryNote->discrepancy_percentage), 100), 2) }}%
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
        <div class="decision-card mb-4">
            <div class="decision-card-header">
                <i class="fas fa-gavel"></i>
                <h5 class="mb-0">اتخاذ القرار</h5>
            </div>
            <div class="decision-card-body p-4">
                <form action="{{ route('manufacturing.warehouses.reconciliation.decide', $deliveryNote) }}" method="POST" id="decideForm">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold mb-3">اختر القرار <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="radio-card">
                                        <input class="form-check-input" type="radio" name="action" value="accepted" id="actionAccept" required>
                                        <label class="radio-label" for="actionAccept">
                                            <i class="fas fa-check-circle text-success"></i>
                                            <strong>قبول الفرق</strong>
                                            <small>نقبل الفاتورة كما هي حتى مع الفرق</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="radio-card">
                                        <input class="form-check-input" type="radio" name="action" value="rejected" id="actionReject" required>
                                        <label class="radio-label" for="actionReject">
                                            <i class="fas fa-times-circle text-danger"></i>
                                            <strong>رفض الفاتورة</strong>
                                            <small>ترجع الفاتورة للمورد للتصحيح</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="radio-card">
                                        <input class="form-check-input" type="radio" name="action" value="adjusted" id="actionAdjust" required>
                                        <label class="radio-label" for="actionAdjust">
                                            <i class="fas fa-tools text-warning"></i>
                                            <strong>تعديل البيانات</strong>
                                            <small>تعديل الوزن يدوياً</small>
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
                    <div class="row g-3">
                        <div class="col-auto">
                            <button type="submit" class="btn-submit-custom">
                                <i class="fas fa-save"></i> حفظ القرار
                            </button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn-cancel-custom">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="alert-info-custom mb-4">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>معلومة</strong>
                <p>هذه التسليمة مسوّاة بالفعل ولا يمكن تعديل قرارها</p>
            </div>
        </div>
    @endif

    <!-- سجل التسويات -->
    @if ($deliveryNote->reconciliationLogs->count() > 0)
        <div class="data-card mt-4">
            <div class="data-card-header">
                <i class="fas fa-history"></i>
                <h5 class="mb-0">سجل القرارات السابقة</h5>
            </div>
            <div class="data-card-body p-4">
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

@push('styles')
<style>
    /* ألوان الشعار */
    :root {
        --primary-blue: #0066B3;
        --secondary-gray: #4A5568;
        --light-gray: #E2E8F0;
        --success-green: #27ae60;
        --warning-orange: #f39c12;
        --danger-red: #e74c3c;
    }

    /* Page Header */
    .page-header-card {
        background: linear-gradient(135deg, var(--primary-blue) 0%, #0052a3 100%);
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 102, 179, 0.15);
    }

    .page-title {
        color: white;
        font-weight: 700;
        font-size: 1.75rem;
    }

    .header-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
    }

    .btn-back {
        background: white;
        color: var(--primary-blue);
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-back:hover {
        background: var(--light-gray);
        color: var(--primary-blue);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Alert Success */
    .alert-success-custom {
        background: linear-gradient(135deg, var(--success-green) 0%, #229954 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(39, 174, 96, 0.2);
    }

    .alert-success-custom i {
        font-size: 1.25rem;
    }

    /* Info Card */
    .info-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--light-gray);
        overflow: hidden;
        height: 100%;
    }

    .info-card-header {
        background: linear-gradient(135deg, var(--secondary-gray) 0%, #2D3748 100%);
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .info-card-header i {
        font-size: 1.25rem;
    }

    .info-card-header h5 {
        margin: 0;
        font-weight: 700;
    }

    .info-card-body {
        padding: 1.5rem;
    }

    .info-item {
        margin-bottom: 1.25rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid var(--light-gray);
    }

    .info-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .info-item label {
        font-size: 0.875rem;
        color: #718096;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .info-item p {
        margin: 0;
        color: var(--secondary-gray);
        font-weight: 500;
    }

    /* Badges */
    .badge-success-custom,
    .badge-primary-custom {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 1rem;
    }

    .badge-success-custom {
        background: linear-gradient(135deg, var(--success-green) 0%, #229954 100%);
        color: white;
    }

    .badge-primary-custom {
        background: linear-gradient(135deg, var(--primary-blue) 0%, #0052a3 100%);
        color: white;
    }

    /* Comparison Card */
    .comparison-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        height: 100%;
    }

    .comparison-card.danger-border {
        border: 2px solid var(--danger-red);
    }

    .comparison-card.success-border {
        border: 2px solid var(--success-green);
    }

    .comparison-card-header {
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .comparison-card-header.danger-bg {
        background: linear-gradient(135deg, var(--danger-red) 0%, #c0392b 100%);
    }

    .comparison-card-header.success-bg {
        background: linear-gradient(135deg, var(--success-green) 0%, #229954 100%);
    }

    .comparison-card-header i {
        font-size: 1.25rem;
    }

    .comparison-card-header h5 {
        margin: 0;
        font-weight: 700;
    }

    .comparison-card-body {
        padding: 1.5rem;
    }

    .comparison-item {
        margin-bottom: 1.25rem;
    }

    .comparison-item label {
        font-size: 0.875rem;
        color: #718096;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .comparison-value {
        font-size: 1.75rem;
        font-weight: 700;
        padding: 0.75rem;
        border-radius: 8px;
        text-align: center;
    }

    .comparison-value.danger {
        background: rgba(231, 76, 60, 0.1);
        color: var(--danger-red);
    }

    .comparison-value.success {
        background: rgba(39, 174, 96, 0.1);
        color: var(--success-green);
    }

    .comparison-value.warning {
        background: rgba(243, 156, 18, 0.1);
        color: var(--warning-orange);
    }

    /* Alert Custom */
    .alert-custom {
        padding: 1rem 1.25rem;
        border-radius: 8px;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .alert-custom i {
        font-size: 1.5rem;
        flex-shrink: 0;
        margin-top: 0.125rem;
    }

    .alert-custom strong {
        display: block;
        margin-bottom: 0.25rem;
        font-size: 1rem;
    }

    .alert-custom small {
        display: block;
        font-size: 0.875rem;
        opacity: 0.9;
    }

    .alert-danger-custom {
        background: rgba(231, 76, 60, 0.1);
        color: var(--danger-red);
        border: 1px solid rgba(231, 76, 60, 0.3);
    }

    .alert-success-custom-2 {
        background: rgba(39, 174, 96, 0.1);
        color: var(--success-green);
        border: 1px solid rgba(39, 174, 96, 0.3);
    }

    /* Data Card */
    .data-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--light-gray);
        overflow: hidden;
    }

    .data-card-header {
        background: linear-gradient(135deg, var(--secondary-gray) 0%, #2D3748 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .data-card-header i {
        font-size: 1.25rem;
    }

    .data-card-header h5 {
        margin: 0;
        font-weight: 700;
    }

    .data-card-body {
        background: white;
    }

    /* Custom Table */
    .custom-table {
        margin: 0;
    }

    .custom-table thead th {
        background: #F7FAFC;
        color: var(--secondary-gray);
        font-weight: 700;
        font-size: 0.875rem;
        padding: 1rem;
        border: 1px solid var(--light-gray);
    }

    .custom-table tbody td {
        padding: 1rem;
        vertical-align: middle;
        color: var(--secondary-gray);
        border: 1px solid var(--light-gray);
        font-weight: 600;
    }

    /* Decision Card */
    .decision-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--light-gray);
        overflow: hidden;
    }

    .decision-card-header {
        background: linear-gradient(135deg, var(--primary-blue) 0%, #0052a3 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .decision-card-header i {
        font-size: 1.25rem;
    }

    .decision-card-header h5 {
        margin: 0;
        font-weight: 700;
    }

    .decision-card-body {
        background: white;
    }

    /* Radio Card */
    .radio-card {
        position: relative;
        background: white;
        border: 2px solid var(--light-gray);
        border-radius: 12px;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        height: 100%;
    }

    .radio-card:hover {
        border-color: var(--primary-blue);
        box-shadow: 0 4px 12px rgba(0, 102, 179, 0.15);
        transform: translateY(-2px);
    }

    .radio-card input[type="radio"] {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .radio-card input[type="radio"]:checked ~ .radio-label {
        color: var(--primary-blue);
    }

    .radio-card input[type="radio"]:checked ~ .radio-label i {
        transform: scale(1.2);
    }

    .radio-label {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        cursor: pointer;
        padding-left: 0;
        margin: 0;
        transition: all 0.3s ease;
    }

    .radio-label i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .radio-label strong {
        font-size: 1.125rem;
        color: var(--secondary-gray);
        display: block;
    }

    .radio-label small {
        color: #718096;
        font-size: 0.875rem;
    }

    /* Form Controls */
    .form-control,
    .form-select {
        border: 2px solid var(--light-gray);
        border-radius: 8px;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.2rem rgba(0, 102, 179, 0.15);
    }

    /* Buttons */
    .btn-submit-custom {
        background: linear-gradient(135deg, var(--success-green) 0%, #229954 100%);
        color: white;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1.125rem;
        transition: all 0.3s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-submit-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
    }

    .btn-cancel-custom {
        background: var(--secondary-gray);
        color: white;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1.125rem;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-cancel-custom:hover {
        background: #2D3748;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(74, 85, 104, 0.3);
    }

    /* Alert Info Custom */
    .alert-info-custom {
        background: rgba(0, 102, 179, 0.1);
        color: var(--primary-blue);
        border: 1px solid rgba(0, 102, 179, 0.3);
        padding: 1.25rem 1.5rem;
        border-radius: 8px;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .alert-info-custom i {
        font-size: 1.5rem;
        flex-shrink: 0;
        margin-top: 0.125rem;
    }

    .alert-info-custom strong {
        display: block;
        margin-bottom: 0.25rem;
        font-size: 1rem;
        font-weight: 700;
    }

    .alert-info-custom p {
        margin: 0;
        font-size: 0.875rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header-card {
            padding: 1.5rem;
        }

        .header-icon {
            width: 50px;
            height: 50px;
            font-size: 24px;
        }

        .page-title {
            font-size: 1.25rem;
        }

        .comparison-value {
            font-size: 1.5rem;
        }

        .radio-card {
            padding: 1rem;
        }

        .radio-label i {
            font-size: 1.5rem;
        }

        .btn-submit-custom,
        .btn-cancel-custom {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush