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
    <div class="alert alert-info mb-4">
        <h5 class="mb-2"><strong>📌 يمكنك تعديل:</strong></h5>
        <ol style="margin: 0; padding-right: 20px;">
            <li>معلومات الفاتورة (الرقم، التاريخ)</li>
            <li>وزن الفاتورة</li>
            <li>الملاحظات والمراجع</li>
            <li>سيتم إعادة حساب الفرق تلقائياً</li>
        </ol>
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

    <form method="POST" action="{{ route('manufacturing.warehouses.reconciliation.link-invoice.update', $reconciliation->id) }}" id="editLinkInvoiceForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- معلومات الأذن (للقراءة فقط) -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">📦 بيانات الأذن (للقراءة فقط)</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label"><strong>رقم الأذن</strong></label>
                            <input type="text" class="form-control" value="#{{ $reconciliation->deliveryNote->note_number ?? $reconciliation->deliveryNote->id }}" disabled>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label"><strong>المورد</strong></label>
                            <input type="text" class="form-control" value="{{ $reconciliation->deliveryNote->supplier->name ?? 'N/A' }}" disabled>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label"><strong>تاريخ الأذن</strong></label>
                            <input type="text" class="form-control" value="{{ $reconciliation->deliveryNote->delivery_date?->format('d/m/Y') }}" disabled>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label"><strong>الوزن الفعلي (من الميزان)</strong></label>
                            <input type="text" class="form-control" value="{{ number_format($reconciliation->deliveryNote->actual_weight, 2) }} كجم" style="color: #27ae60; font-weight: 600;" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات الفاتورة (قابلة للتعديل) -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">📄 بيانات الفاتورة</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label"><strong>رقم الفاتورة <span class="text-danger">*</span></strong></label>
                            <input type="text" name="invoice_number" class="form-control @error('invoice_number') is-invalid @enderror"
                                placeholder="مثال: INV-2024-001" value="{{ old('invoice_number', $reconciliation->invoice_number) }}" required>
                            @error('invoice_number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label"><strong>تاريخ الفاتورة <span class="text-danger">*</span></strong></label>
                            <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror"
                                value="{{ old('invoice_date', $reconciliation->invoice_date?->format('Y-m-d')) }}" required>
                            @error('invoice_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label"><strong>وزن الفاتورة (كيلو) <span class="text-danger">*</span></strong></label>
                            <input type="number" step="0.01" min="0" name="invoice_weight" id="invoice_weight"
                                class="form-control @error('invoice_weight') is-invalid @enderror"
                                placeholder="مثال: 1000.50" value="{{ old('invoice_weight', $reconciliation->invoice_weight) }}" required>
                            @error('invoice_weight')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <small class="text-muted">الوزن المكتوب في الفاتورة</small>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label"><strong>رقم مرجع الفاتورة</strong></label>
                            <input type="text" name="invoice_reference_number" class="form-control @error('invoice_reference_number') is-invalid @enderror"
                                placeholder="رقم مرجع إضافي (اختياري)" value="{{ old('invoice_reference_number', $reconciliation->invoice_reference_number) }}">
                            @error('invoice_reference_number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- حساب الفرق -->
        <div class="card mb-4" id="discrepancyCard" style="border-left: 4px solid #f39c12;">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">⚖️ حساب الفرق</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">الوزن الفعلي (الميزان)</small>
                            <h4 id="display-actual-weight" class="mb-0 text-success">{{ number_format($reconciliation->deliveryNote->actual_weight, 2) }} كجم</h4>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                        <h3 class="mb-0">➖</h3>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">وزن الفاتورة</small>
                            <h4 id="display-invoice-weight" class="mb-0 text-primary">{{ number_format($reconciliation->invoice_weight, 2) }} كجم</h4>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                        <h3 class="mb-0">=</h3>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">الفرق</small>
                            <h4 id="display-discrepancy" class="mb-0">{{ number_format($reconciliation->weight_discrepancy, 2) }} كجم</h4>
                            <small id="display-percentage" class="text-muted">({{ number_format($reconciliation->discrepancy_percentage, 2) }}%)</small>
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

        <!-- معلومات إضافية عن التسوية -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">📊 معلومات التسوية</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item mb-3">
                            <label class="text-muted">حالة التسوية:</label>
                            <p>
                                <span class="badge badge-{{ $reconciliation->reconciliation_status === 'discrepancy' ? 'warning' : 'info' }}">
                                    {{ $reconciliation->reconciliation_status === 'discrepancy' ? 'بها فروقات' : 'متطابقة' }}
                                </span>
                            </p>
                        </div>
                        <div class="info-item mb-3">
                            <label class="text-muted">تاريخ الإنشاء:</label>
                            <p>{{ $reconciliation->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="info-item">
                            <label class="text-muted">آخر تحديث:</label>
                            <p>{{ $reconciliation->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">👤 معلومات المستخدم</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item mb-3">
                            <label class="text-muted">من أنشأ:</label>
                            <p>{{ $reconciliation->createdBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="info-item mb-3">
                            <label class="text-muted">من عدّل:</label>
                            <p>{{ $reconciliation->updatedBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="info-item">
                            <label class="text-muted">عدد مرات التعديل:</label>
                            <p>
                                <span class="badge badge-info">
                                    {{ $reconciliation->edit_count ?? 0 }} مرة
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ملاحظات -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">📝 ملاحظات</h5>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label class="form-label">ملاحظات حول الفرق (إن وجد):</label>
                    <textarea name="reconciliation_notes" class="form-control @error('reconciliation_notes') is-invalid @enderror"
                        rows="3" placeholder="مثال: فرق طبيعي بسبب الرطوبة / يوجد عجز يحتاج متابعة">{{ old('reconciliation_notes', $reconciliation->reconciliation_notes) }}</textarea>
                    @error('reconciliation_notes')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <!-- سبب التعديل -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">📌 سبب التعديل</h5>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label class="form-label"><strong>اذكر السبب <span class="text-danger">*</span></strong></label>
                    <textarea name="edit_reason" class="form-control @error('edit_reason') is-invalid @enderror"
                        rows="2" placeholder="مثال: تصحيح خطأ في البيانات / تحديث معلومات من المورد" required>{{ old('edit_reason') }}</textarea>
                    @error('edit_reason')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <!-- الإجراءات -->
        <div class="card border-success mb-4">
            <div class="card-body">
                <div class="form-check mb-3">
                    <input type="checkbox" id="confirmCheck" class="form-check-input" required>
                    <label class="form-check-label" for="confirmCheck">
                        <strong>✓ أؤكد صحة البيانات المعدلة</strong>
                    </label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                        <i class="fas fa-save"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                </div>

                <div class="alert alert-light mt-3 mb-0">
                    <small><strong>✓ ملاحظة:</strong> سيتم تسجيل جميع التعديلات في السجل</small>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const invoiceWeightInput = document.getElementById('invoice_weight');
    const discrepancyCard = document.getElementById('discrepancyCard');
    const confirmCheck = document.getElementById('confirmCheck');
    const submitBtn = document.getElementById('submitBtn');

    // الوزن الفعلي من البيانات
    const actualWeight = {{ $reconciliation->deliveryNote->actual_weight ?? 0 }};

    // حساب الفرق عند تغيير وزن الفاتورة
    invoiceWeightInput.addEventListener('input', calculateDiscrepancy);

    function calculateDiscrepancy() {
        if (!invoiceWeightInput.value) {
            return;
        }

        const invoiceWeight = parseFloat(invoiceWeightInput.value) || 0;
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
    .info-item {
        padding-bottom: 0.75rem;
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
        color: #2D3748;
        font-weight: 500;
    }

    .badge-warning {
        background-color: #f39c12;
        color: white;
    }

    .badge-info {
        background-color: #0066B3;
        color: white;
    }
</style>
@endsection
