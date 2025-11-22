@extends('master')

@section('title', 'تعديل ربط الفاتورة بالأذن')

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
                <h1 class="page-title">✏️ تعديل ربط الفاتورة بأذن التسليم</h1>
                <p class="text-muted">تعديل معلومات الفاتورة والملاحظات</p>
            </div>
        </div>
    </div>

    <!-- Process Explanation -->
    <div class="alert alert-info mb-4" style="background-color: #e8f0ff; border-left: 4px solid #0051E5; color: #003FA0;">
        <h5 class="mb-2"><strong>📌 تعديل البيانات:</strong></h5>
        <ol style="margin: 0; padding-right: 20px;">
            <li>يمكنك تعديل وزن الفاتورة</li>
            <li>يمكنك إضافة أو تعديل الملاحظات</li>
            <li>سيتم إعادة حساب الفرق تلقائياً</li>
            <li>يجب ذكر سبب التعديل</li>
        </ol>
        <hr class="my-2" style="border-top-color: #0051E5;">
        <small><strong>💡 ملاحظة:</strong> سيتم تحديث حالة التسوية بناءً على الوزن الجديد</small>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ❌ {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <h5>يوجد أخطاء:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('manufacturing.warehouses.reconciliation.link-invoice.update', $deliveryNote->id) }}" id="editLinkInvoiceForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- معلومات الأذن (للقراءة فقط) -->
            <div class="col-lg-6">
                <div class="card mb-4" style="border-left: 4px solid #27ae60;">
                    <div class="card-header" style="background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%); color: white;">
                        <h5 class="mb-0"><i class="feather icon-package"></i> بيانات أذن التسليم</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <label><i class="feather icon-hash"></i> رقم الأذن:</label>
                            <strong>#{{ $deliveryNote->note_number ?? $deliveryNote->id }}</strong>
                        </div>
                        <div class="info-row">
                            <label><i class="feather icon-user"></i> المورد:</label>
                            <strong>{{ $deliveryNote->supplier->name }}</strong>
                        </div>
                        <div class="info-row">
                            <label><i class="feather icon-calendar"></i> تاريخ التسليم:</label>
                            <strong>{{ $deliveryNote->delivery_date?->format('Y-m-d') ?? 'غير محدد' }}</strong>
                        </div>
                        <div class="info-row">
                            <label><i class="feather icon-trending-up"></i> الوزن الفعلي (الميزان):</label>
                            <strong style="color: #27ae60; font-size: 1.1em;">{{ number_format($deliveryNote->actual_weight, 2) }} كجم</strong>
                        </div>
                        <div class="info-row">
                            <label><i class="feather icon-info"></i> حالة التسوية:</label>
                            <span class="badge {{ $deliveryNote->reconciliation_status === 'discrepancy' ? 'bg-warning' : ($deliveryNote->reconciliation_status === 'matched' ? 'bg-success' : 'bg-info') }}">
                                {{ $deliveryNote->reconciliation_status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات الفاتورة -->
            <div class="col-lg-6">
                <div class="card mb-4" style="border-left: 4px solid #0051E5;">
                    <div class="card-header" style="background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%); color: white;">
                        <h5 class="mb-0"><i class="feather icon-file-text"></i> بيانات الفاتورة</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <label><i class="feather icon-file"></i> رقم الفاتورة:</label>
                            <strong>{{ $deliveryNote->purchaseInvoice->invoice_number }}</strong>
                        </div>
                        <div class="info-row">
                            <label><i class="feather icon-calendar"></i> تاريخ الفاتورة:</label>
                            <strong>{{ $deliveryNote->purchaseInvoice->invoice_date?->format('Y-m-d') }}</strong>
                        </div>

                        <hr class="my-3">

                        <div class="form-group mb-3">
                            <label class="form-label"><strong><i class="feather icon-trending-up"></i> وزن الفاتورة (كجم) <span class="text-danger">*</span></strong></label>
                            <input type="number" step="0.01" min="0" name="invoice_weight" id="invoice_weight"
                                class="form-control @error('invoice_weight') is-invalid @enderror"
                                placeholder="مثال: 1000.50" value="{{ old('invoice_weight', $deliveryNote->invoice_weight) }}" required>
                            @error('invoice_weight')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <small class="text-muted">الوزن المكتوب في الفاتورة</small>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label"><strong><i class="feather icon-tag"></i> رقم مرجع الفاتورة</strong></label>
                            <input type="text" name="invoice_reference_number" class="form-control @error('invoice_reference_number') is-invalid @enderror"
                                placeholder="رقم مرجع إضافي (اختياري)" value="{{ old('invoice_reference_number', $deliveryNote->invoice_reference_number) }}">
                            @error('invoice_reference_number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- حساب الفرق -->
        <div class="card mb-4" id="discrepancyCard" style="border-left: 4px solid #0051E5;">
            <div class="card-header" style="background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%); color: white;">
                <h5 class="mb-0"><i class="feather icon-bar-chart-2"></i> حساب الفرق</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">الوزن الفعلي (الميزان)</small>
                            <h4 id="display-actual-weight" class="mb-0 text-success">{{ number_format($deliveryNote->actual_weight, 2) }} كجم</h4>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                        <h3 class="mb-0">➖</h3>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">وزن الفاتورة</small>
                            <h4 id="display-invoice-weight" class="mb-0 text-primary">{{ number_format($deliveryNote->invoice_weight, 2) }} كجم</h4>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                        <h3 class="mb-0">=</h3>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">الفرق</small>
                            <h4 id="display-discrepancy" class="mb-0">{{ number_format($deliveryNote->actual_weight - $deliveryNote->invoice_weight, 2) }} كجم</h4>
                            <small id="display-percentage" class="text-muted">
                                ({{ $deliveryNote->invoice_weight > 0 ? number_format((($deliveryNote->actual_weight - $deliveryNote->invoice_weight) / $deliveryNote->invoice_weight) * 100, 2) : 0 }}%)
                            </small>
                        </div>
                    </div>
                </div>

                <!-- تحذير إذا كان هناك فرق كبير -->
                <div id="discrepancy-warning" style="display: none; margin-top: 20px;">
                    <div class="alert alert-warning">
                        <strong>⚠️ تنبيه:</strong> يوجد فرق كبير بين الوزن الفعلي ووزن الفاتورة. يرجى التأكد من البيانات.
                    </div>
                </div>
            </div>
        </div>

        <!-- ملاحظات -->
        <div class="card mb-4">
            <div class="card-header" style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                <h5 class="mb-0"><i class="feather icon-message-square"></i> ملاحظات</h5>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label class="form-label">ملاحظات حول الربط:</label>
                    <textarea name="reconciliation_notes" class="form-control @error('reconciliation_notes') is-invalid @enderror"
                        rows="3" placeholder="مثال: فرق طبيعي بسبب الرطوبة / يوجد عجز يحتاج متابعة">{{ old('reconciliation_notes', $deliveryNote->reconciliation_notes) }}</textarea>
                    @error('reconciliation_notes')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <!-- سبب التعديل -->
        <div class="card mb-4" style="border-left: 4px solid #f39c12;">
            <div class="card-header" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); color: white;">
                <h5 class="mb-0"><i class="feather icon-edit"></i> سبب التعديل</h5>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label class="form-label"><strong>يرجى ذكر سبب التعديل <span class="text-danger">*</span></strong></label>
                    <textarea name="edit_reason" class="form-control @error('edit_reason') is-invalid @enderror"
                        rows="2" placeholder="مثال: تصحيح خطأ في وزن الفاتورة / تحديث بيانات من المورد" required>{{ old('edit_reason') }}</textarea>
                    @error('edit_reason')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <!-- الإجراءات -->
        <div class="card mb-4" style="border-left: 4px solid #3E4651;">
            <div class="card-body">
                <div class="form-check mb-3">
                    <input type="checkbox" id="confirmCheck" class="form-check-input" required>
                    <label class="form-check-label" for="confirmCheck">
                        <strong>✓ أؤكد صحة البيانات المعدلة وأتحمل مسؤولية التعديل</strong>
                    </label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-info btn-lg" id="submitBtn" disabled>
                        <i class="feather icon-save"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn btn-outline-danger btn-lg">
                        <i class="feather icon-x"></i> إلغاء
                    </a>
                </div>

                <div class="alert alert-light mt-3 mb-0">
                    <small><strong>💡 ملاحظة:</strong> سيتم تسجيل جميع التعديلات في سجل النظام</small>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const invoiceWeightInput = document.getElementById('invoice_weight');
    const confirmCheck = document.getElementById('confirmCheck');
    const submitBtn = document.getElementById('submitBtn');

    // الوزن الفعلي من البيانات
    const actualWeight = {{ $deliveryNote->actual_weight ?? 0 }};

    // حساب الفرق عند تغيير وزن الفاتورة
    invoiceWeightInput.addEventListener('input', calculateDiscrepancy);

    function calculateDiscrepancy() {
        const invoiceWeight = parseFloat(invoiceWeightInput.value) || 0;

        if (!invoiceWeight) {
            return;
        }

        const discrepancy = actualWeight - invoiceWeight;
        const percentage = invoiceWeight > 0 ? ((discrepancy / invoiceWeight) * 100) : 0;

        document.getElementById('display-actual-weight').textContent = actualWeight.toFixed(2) + ' كجم';
        document.getElementById('display-invoice-weight').textContent = invoiceWeight.toFixed(2) + ' كجم';
        document.getElementById('display-discrepancy').textContent = (discrepancy >= 0 ? '+' : '') + discrepancy.toFixed(2) + ' كجم';
        document.getElementById('display-discrepancy').className = 'mb-0 ' + (discrepancy >= 0 ? 'text-danger' : 'text-success');
        document.getElementById('display-percentage').textContent = '(' + (percentage >= 0 ? '+' : '') + percentage.toFixed(2) + '%)';

        // تحذير إذا كان الفرق أكبر من 5%
        const warningDiv = document.getElementById('discrepancy-warning');
        if (Math.abs(percentage) > 5) {
            warningDiv.style.display = 'block';
        } else {
            warningDiv.style.display = 'none';
        }
    }

    // تفعيل/تعطيل زر الإرسال
    confirmCheck.addEventListener('change', function() {
        submitBtn.disabled = !this.checked;
    });

    // حساب الفرق عند تحميل الصفحة
    calculateDiscrepancy();
});
</script>

<style>
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-row label {
        color: #718096;
        font-size: 13px;
        margin: 0;
        font-weight: 600;
    }

    .info-row strong {
        color: #2D3748;
        font-size: 14px;
    }

    .info-row label i {
        margin-left: 5px;
        color: #0051E5;
    }

    .btn-info {
        background-color: #0051E5;
        border-color: #0051E5;
        color: white;
    }

    .btn-info:hover {
        background-color: #003FA0;
        border-color: #003FA0;
        color: white;
    }

    .btn-outline-danger {
        border-color: #E74C3C;
        color: #E74C3C;
    }

    .btn-outline-danger:hover {
        background-color: #E74C3C;
        border-color: #E74C3C;
        color: white;
    }

    .d-flex.gap-2 {
        gap: 0.5rem;
    }
</style>
@endsection
