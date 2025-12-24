@extends('master')

@section('title', __('reconciliation.reconciliation_dashboard'))

@section('content')
@php
    // تعريف المتغيرات في أعلى الصفحة لاستخدامها في كل الأماكن
    // محاولة الحصول على الوزن من عدة مصادر
    $actualWeight = $deliveryNote->actual_weight 
        ?? $deliveryNote->weight_from_scale 
        ?? $deliveryNote->delivered_weight 
        ?? $deliveryNote->quantity 
        ?? 0;
    
    // إذا كان صفر، جرب من العلاقات (items)
    if ($actualWeight == 0 && $deliveryNote->items && $deliveryNote->items->count() > 0) {
        $actualWeight = $deliveryNote->items->sum('actual_weight') ?: $deliveryNote->items->sum('quantity');
    }
    
    $invoiceWeight = $deliveryNote->invoice_weight ?? 0;
    $discrepancy = $actualWeight - $invoiceWeight;
    $discrepancyPercentage = $invoiceWeight > 0 ? (($discrepancy / $invoiceWeight) * 100) : 0;
    $isInOurFavor = $discrepancy > 0; // الوزن الفعلي أكبر من الفاتورة = في صالحنا
    $isDeficit = $discrepancy < 0; // الوزن الفعلي أقل من الفاتورة = عجز
    
    // معلومات debugging
    $debugInfo = [
        'actual_weight' => $deliveryNote->actual_weight,
        'weight_from_scale' => $deliveryNote->weight_from_scale,
        'delivered_weight' => $deliveryNote->delivered_weight,
        'quantity' => $deliveryNote->quantity,
        'items_count' => $deliveryNote->items ? $deliveryNote->items->count() : 0,
        'items_sum' => $deliveryNote->items ? $deliveryNote->items->sum('actual_weight') : 0,
        'final_weight' => $actualWeight
    ];
@endphp

<div class="container-fluid px-4 py-4">
    <div class="page-header-card mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> {{ __('reconciliation.back') }}
                    </a>
                    <button onclick="window.print()" class="btn-back" style="background: white; color: var(--secondary-gray);">
                        <i class="fas fa-print"></i> {{ __('reconciliation.save') }}
                    </button>
                </div>
            </div>
            <div class="col">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-0">تسوية الأذن #{{ $deliveryNote->note_number ?? $deliveryNote->id }}</h1>
                        <p class="text-white-50 mb-0 mt-1">مقارنة الوزن الفعلي مع وزن الفاتورة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-success-custom mb-4" style="animation: slideInDown 0.5s ease;">
            <i class="fas fa-check-circle"></i>
            <div>
                <strong>{{ session('success') }}</strong>
                <br>
                <small>تم حساب الفرق تلقائياً: {{ $discrepancy >= 0 ? '+' : '' }}{{ number_format($discrepancy, 2) }} كجم ({{ number_format(abs($discrepancyPercentage), 2) }}%)</small>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mb-4" style="animation: slideInDown 0.5s ease;">
            <i class="fas fa-exclamation-triangle"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="row g-3">
        <!-- معلومات الشحنة -->
        <div class="col-lg-4">
            <div class="info-card">
                <div class="info-card-header">
                    <i class="fas fa-truck-loading"></i>
                    <h5 class="mb-0">معلومات أذن التسليم</h5>
                </div>
                <div class="info-card-body">
                    <div class="info-item">
                        <label>رقم الأذن:</label>
                        <p><strong>#{{ $deliveryNote->note_number ?? $deliveryNote->id }}</strong></p>
                    </div>

                    <div class="info-item">
                        <label>المورد:</label>
                        <p><strong>{{ $deliveryNote->supplier->name ?? 'غير محدد' }}</strong></p>
                    </div>

                    <div class="info-item">
                        <label>الوزن الفعلي (من الميزان):</label>
                        <p>
                            @if ($actualWeight > 0)
                                <span class="badge-success-custom">
                                    {{ number_format($actualWeight, 2) }} كجم
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    0.00 كجم - لا يوجد وزن!
                                </span>
                            @endif
                        </p>
                        
                        {{-- معلومات debugging مفصلة --}}
                        @if ($actualWeight == 0)
                            <div class="alert alert-danger p-2 mt-2" style="font-size: 0.85rem;">
                                <strong>⚠️ جميع حقول الوزن فارغة:</strong><br>
                                <small>
                                    • actual_weight: {{ $debugInfo['actual_weight'] ?? 'null' }}<br>
                                    • weight_from_scale: {{ $debugInfo['weight_from_scale'] ?? 'null' }}<br>
                                    • delivered_weight: {{ $debugInfo['delivered_weight'] ?? 'null' }}<br>
                                    • quantity: {{ $debugInfo['quantity'] ?? 'null' }}<br>
                                    • عدد العناصر: {{ $debugInfo['items_count'] }}<br>
                                    • مجموع أوزان العناصر: {{ $debugInfo['items_sum'] }}
                                </small>
                            </div>
                            <small class="text-info d-block mt-1">
                                💡 <strong>الحل:</strong> ارجع لصفحة تسجيل الأذن وأدخل الوزن الفعلي من الميزان
                            </small>
                        @else
                            <small class="text-success d-block mt-1">
                                ✓ الوزن مأخوذ من: 
                                @if($deliveryNote->actual_weight > 0)
                                    actual_weight
                                @elseif($deliveryNote->weight_from_scale > 0)
                                    weight_from_scale
                                @elseif($deliveryNote->delivered_weight > 0)
                                    delivered_weight
                                @elseif($deliveryNote->quantity > 0)
                                    quantity
                                @else
                                    items ({{ $debugInfo['items_count'] }} عنصر)
                                @endif
                            </small>
                        @endif
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
                    <i class="fas fa-file-invoice-dollar"></i>
                    <h5 class="mb-0">معلومات الفاتورة</h5>
                </div>
                <div class="info-card-body">
                    <div class="info-item">
                        <label>رقم الفاتورة:</label>
                        <p>
                            <strong>#{{ $deliveryNote->purchaseInvoice->invoice_number ?? 'غير محدد' }}</strong>
                        </p>
                    </div>

                    <div class="info-item">
                        <label>مورد الفاتورة:</label>
                        <p>{{ $deliveryNote->purchaseInvoice->supplier->name ?? 'غير محدد' }}</p>
                    </div>

                    <div class="info-item">
                        <label>الوزن في الفاتورة:</label>
                        <p>
                            <span class="badge-primary-custom">
                                {{ number_format($deliveryNote->invoice_weight ?? 0, 2) }} كجم
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
            <div class="comparison-card {{ $isDeficit ? 'danger-border' : 'success-border' }}">
                <div class="comparison-card-header {{ $isDeficit ? 'danger-bg' : 'success-bg' }}">
                    <i class="fas fa-balance-scale-right"></i>
                    <h5 class="mb-0">المقارنة والفرق</h5>
                </div>
                <div class="comparison-card-body">
                    <div class="comparison-item">
                        <label>الفرق (كجم):</label>
                        <div class="comparison-value {{ $isDeficit ? 'danger' : 'success' }}">
                            {{ $discrepancy >= 0 ? '+ ' : '- ' }}
                            {{ number_format(abs($discrepancy), 2) }} كجم
                        </div>
                    </div>

                    <div class="comparison-item">
                        <label>النسبة المئوية:</label>
                        <div class="comparison-value {{ abs($discrepancyPercentage) > 5 ? 'danger' : 'warning' }}">
                            {{ $discrepancy >= 0 ? '+ ' : '- ' }}
                            {{ number_format(abs($discrepancyPercentage), 2) }}%
                        </div>
                    </div>

                    @if (abs($discrepancy) < 0.01)
                        <div class="alert-custom alert-success-custom-2">
                            <i class="fas fa-check-double"></i>
                            <div>
                                <strong>✓ الأوزان متطابقة تماماً!</strong>
                                <small>لا يوجد أي فرق في الوزن</small>
                            </div>
                        </div>
                    @elseif ($isInOurFavor)
                        <div class="alert-custom alert-success-custom-2">
                            <i class="fas fa-arrow-up"></i>
                            <div>
                                <strong>✓ في صالحنا - استلمنا أكثر مما تم فوترته!</strong>
                                <small>الفاتورة: {{ number_format($invoiceWeight, 2) }} كجم | الفعلي: {{ number_format($actualWeight, 2) }} كجم<br>
                                المورد فوَّتر أقل بـ {{ number_format(abs($discrepancy), 2) }} كجم</small>
                            </div>
                        </div>
                    @elseif ($isDeficit)
                        <div class="alert-custom alert-danger-custom">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>⚠ عجز - الوزن الفعلي أقل من وزن الفاتورة!</strong>
                                <small>الفاتورة: {{ number_format($invoiceWeight, 2) }} كجم | الفعلي: {{ number_format($actualWeight, 2) }} كجم<br>
                                يوجد نقص مقداره {{ number_format(abs($discrepancy), 2) }} كجم مقارنة بالفاتورة</small>
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
            <i class="fas fa-calculator"></i>
            <h5 class="mb-0">جدول المقارنة التفصيلي</h5>
        </div>
        <div class="data-card-body p-4">
            <div class="table-responsive">
                <table class="table custom-table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 20%;">البيان</th>
                            <th class="text-center" style="background-color: #e7f3ff; width: 20%;">الوزن الفعلي (الميزان)</th>
                            <th class="text-center" style="background-color: #fff3e7; width: 20%;">وزن الفاتورة (المورد)</th>
                            <th class="text-center" style="background-color: #ffe7e7; width: 20%;">الفرق</th>
                            <th class="text-center" style="width: 20%;">النسبة %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>الوزن (كجم)</strong></td>
                            <td class="text-center">
                                <span class="badge bg-success" style="font-size: 1.1rem; padding: 0.5rem 1rem;">
                                    {{ number_format($actualWeight, 2) }} كجم
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary" style="font-size: 1.1rem; padding: 0.5rem 1rem;">
                                    {{ number_format($invoiceWeight, 2) }} كجم
                                </span>
                            </td>
                            <td class="text-center">
                                <strong class="text-{{ $isDeficit ? 'danger' : 'success' }}" style="font-size: 1.2rem;">
                                    {{ $discrepancy >= 0 ? '+ ' : '- ' }}
                                    {{ number_format(abs($discrepancy), 2) }} كجم
                                </strong>
                            </td>
                            <td class="text-center">
                                <strong class="text-{{ abs($discrepancyPercentage) > 5 ? 'danger' : (abs($discrepancyPercentage) > 1 ? 'warning' : 'success') }}" style="font-size: 1.2rem;">
                                    {{ $discrepancy >= 0 ? '+ ' : '- ' }}
                                    {{ number_format(abs($discrepancyPercentage), 2) }}%
                                </strong>
                            </td>
                        </tr>
                        <tr style="background-color: #f8f9fa;">
                            <td colspan="5" class="text-center">
                                <small class="text-muted">
                                    <strong>المعادلة:</strong> الفرق = الوزن الفعلي - وزن الفاتورة = 
                                    {{ number_format($actualWeight, 2) }} - {{ number_format($invoiceWeight, 2) }} = 
                                    <span class="text-{{ $isDeficit ? 'danger' : 'success' }}">{{ number_format($discrepancy, 2) }} كجم</span>
                                </small>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ملخص سريع -->
    <div class="alert" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-right: 5px solid {{ $isDeficit ? '#e74c3c' : '#27ae60' }}; padding: 1.5rem; margin-bottom: 2rem; border-radius: 12px;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-2"><i class="fas fa-info-circle" style="color: {{ $isDeficit ? '#e74c3c' : '#27ae60' }};"></i> <strong>ملخص سريع:</strong></h5>
                <p class="mb-0" style="font-size: 1.1rem; color: #2D3748;">
                    @if (abs($discrepancy) < 0.01)
                        ✓ الأوزان متطابقة تماماً - لا يوجد أي فرق
                    @elseif ($isInOurFavor)
                        ✓ استلمنا <strong style="color: #27ae60;">{{ number_format(abs($discrepancy), 2) }} كجم</strong> أكثر مما تم فوترته - في صالحنا
                    @elseif ($isDeficit)
                        ⚠ يوجد عجز <strong style="color: #e74c3c;">{{ number_format(abs($discrepancy), 2) }} كجم</strong> - الوزن الفعلي أقل من وزن الفاتورة
                    @endif
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div style="font-size: 2.5rem; font-weight: 700; color: {{ $isDeficit ? '#e74c3c' : '#27ae60' }};">
                    {{ $discrepancy >= 0 ? '+' : '' }}{{ number_format($discrepancy, 2) }}
                    <small style="font-size: 1rem; display: block; color: #718096;">كيلوجرام</small>
                </div>
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

    /* Badge Enhancements */
    .badge {
        font-weight: 600 !important;
        letter-spacing: 0.3px;
    }

    /* Table Enhancements */
    .custom-table tbody tr:hover {
        background-color: #f8f9fa;
        transition: all 0.2s ease;
    }

    .custom-table strong {
        font-weight: 700;
    }

    /* Icon Animations */
    .alert-custom i {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-success-custom {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .alert-success-custom strong {
        display: block;
        font-size: 1.1rem;
    }

    .alert-success-custom small {
        opacity: 0.9;
    }

    /* Tooltip Enhancement */
    [title] {
        cursor: help;
    }

    /* Smooth Transitions */
    .comparison-card,
    .info-card,
    .data-card,
    .decision-card {
        transition: all 0.3s ease;
    }

    .comparison-card:hover,
    .info-card:hover,
    .data-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    /* Success/Danger Border Enhancements */
    .success-border {
        border-color: var(--success-green) !important;
        box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
    }

    .danger-border {
        border-color: var(--danger-red) !important;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
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

        .alert [class*="col-md"] {
            text-align: center !important;
            margin-bottom: 1rem;
        }
    }

    /* Print Styles */
    @media print {
        .btn-back,
        .decision-card,
        .page-header-card {
            display: none;
        }

        .info-card,
        .comparison-card,
        .data-card {
            page-break-inside: avoid;
            box-shadow: none;
            border: 2px solid #333;
        }
    }
</style>
@endpush