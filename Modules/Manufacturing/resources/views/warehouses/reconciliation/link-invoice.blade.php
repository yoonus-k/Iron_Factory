@extends('master')

@section('title', 'ربط الفاتورة بالأذن')

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
                <h1 class="page-title">🔗 ربط الفاتورة بأذن التسليم</h1>
                <p class="text-muted">لمعالجة الفواتير المتأخرة وحساب الفروقات</p>
            </div>
        </div>
    </div>

    <!-- Process Explanation -->
    <div class="alert alert-info mb-4">
        <h5 class="mb-2"><strong>📌 كيفية العمل:</strong></h5>
        <ol style="margin: 0; padding-right: 20px;">
            <li>اختر الأذن التي وصلت البضاعة لها</li>
            <li>أدخل معلومات الفاتورة (الرقم، التاريخ، الوزن)</li>
            <li>سيتم حساب الفرق بين الوزن الفعلي (من الميزان) ووزن الفاتورة تلقائياً</li>
            <li>يمكنك معالجة الفرق أو قبوله</li>
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

    <form method="POST" action="{{ route('manufacturing.warehouses.reconciliation.link-invoice.store') }}" id="linkInvoiceForm">
        @csrf

        <div class="row">
            <!-- اختيار الأذن -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">📦 بيانات الأذن</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label"><strong>اختر الأذن <span class="text-danger">*</span></strong></label>
                            <select name="delivery_note_id" id="delivery_note_id" class="form-select @error('delivery_note_id') is-invalid @enderror" required>
                                <option value="">-- اختر أذن التسليم --</option>
                                @foreach($deliveryNotes as $note)
                                    <option value="{{ $note->id }}"
                                        data-actual-weight="{{ $note->actual_weight ?? 0 }}"
                                        data-supplier="{{ $note->supplier->name ?? 'N/A' }}"
                                        data-date="{{ $note->delivery_date?->format('d/m/Y') }}"
                                        data-note-number="{{ $note->note_number }}"
                                        {{ old('delivery_note_id') == $note->id ? 'selected' : '' }}>
                                        #{{ $note->note_number }} - {{ $note->supplier->name ?? 'N/A' }} - {{ $note->delivery_date?->format('d/m/Y') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('delivery_note_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- عرض معلومات الأذن المختارة -->
                        <div id="deliveryNoteInfo" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px;">
                            <h6 class="mb-3"><strong>معلومات الأذن:</strong></h6>
                            <div style="display: grid; gap: 10px;">
                                <div>
                                    <small class="text-muted">رقم الأذن:</small>
                                    <div id="info-note-number" style="font-weight: 600;"></div>
                                </div>
                                <div>
                                    <small class="text-muted">المورد:</small>
                                    <div id="info-supplier" style="font-weight: 600;"></div>
                                </div>
                                <div>
                                    <small class="text-muted">تاريخ الأذن:</small>
                                    <div id="info-date" style="font-weight: 600;"></div>
                                </div>
                                <div>
                                    <small class="text-muted">الوزن الفعلي (من الميزان):</small>
                                    <div id="info-actual-weight" style="font-weight: 600; color: #27ae60;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات الفاتورة -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">📄 بيانات الفاتورة</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label"><strong>رقم الفاتورة <span class="text-danger">*</span></strong></label>
                            <input type="text" name="invoice_number" class="form-control @error('invoice_number') is-invalid @enderror"
                                placeholder="مثال: INV-2024-001" value="{{ old('invoice_number') }}" required>
                            @error('invoice_number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label"><strong>تاريخ الفاتورة <span class="text-danger">*</span></strong></label>
                            <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror"
                                value="{{ old('invoice_date') }}" required>
                            @error('invoice_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label"><strong>وزن الفاتورة (كيلو) <span class="text-danger">*</span></strong></label>
                            <input type="number" step="0.01" min="0" name="invoice_weight" id="invoice_weight"
                                class="form-control @error('invoice_weight') is-invalid @enderror"
                                placeholder="مثال: 1000.50" value="{{ old('invoice_weight') }}" required>
                            @error('invoice_weight')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <small class="text-muted">الوزن المكتوب في الفاتورة</small>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label"><strong>رقم مرجع الفاتورة</strong></label>
                            <input type="text" name="invoice_reference_number" class="form-control @error('invoice_reference_number') is-invalid @enderror"
                                placeholder="رقم مرجع إضافي (اختياري)" value="{{ old('invoice_reference_number') }}">
                            @error('invoice_reference_number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- حساب الفرق -->
        <div class="card mb-4" id="discrepancyCard" style="display: none; border-left: 4px solid #f39c12;">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">⚖️ حساب الفرق</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">الوزن الفعلي (الميزان)</small>
                            <h4 id="display-actual-weight" class="mb-0 text-success">0.00 كجم</h4>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                        <h3 class="mb-0">➖</h3>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">وزن الفاتورة</small>
                            <h4 id="display-invoice-weight" class="mb-0 text-primary">0.00 كجم</h4>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                        <h3 class="mb-0">=</h3>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">الفرق</small>
                            <h4 id="display-discrepancy" class="mb-0">0.00 كجم</h4>
                            <small id="display-percentage" class="text-muted"></small>
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
            <div class="card-header bg-light">
                <h5 class="mb-0">📝 ملاحظات</h5>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label class="form-label">ملاحظات حول الفرق (إن وجد):</label>
                    <textarea name="reconciliation_notes" class="form-control @error('reconciliation_notes') is-invalid @enderror"
                        rows="3" placeholder="مثال: فرق طبيعي بسبب الرطوبة / يوجد عجز يحتاج متابعة">{{ old('reconciliation_notes') }}</textarea>
                    @error('reconciliation_notes')
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
                        <strong>✓ أؤكد صحة البيانات المدخلة وأن الفاتورة مطابقة للأذن</strong>
                    </label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
                        <i class="fas fa-link"></i> ربط الفاتورة وحساب الفرق
                    </button>
                    <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                </div>

                <div class="alert alert-light mt-3 mb-0">
                    <small><strong>✓ بعد الربط:</strong> سيتم حساب الفرق تلقائياً وإضافة السجل في صفحة التسوية</small>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deliveryNoteSelect = document.getElementById('delivery_note_id');
    const invoiceWeightInput = document.getElementById('invoice_weight');
    const deliveryNoteInfo = document.getElementById('deliveryNoteInfo');
    const discrepancyCard = document.getElementById('discrepancyCard');
    const confirmCheck = document.getElementById('confirmCheck');
    const submitBtn = document.getElementById('submitBtn');

    // عرض معلومات الأذن عند الاختيار
    deliveryNoteSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (this.value) {
            const actualWeight = parseFloat(selectedOption.dataset.actualWeight) || 0;
            const supplier = selectedOption.dataset.supplier;
            const date = selectedOption.dataset.date;
            const noteNumber = selectedOption.dataset.noteNumber;

            document.getElementById('info-note-number').textContent = noteNumber;
            document.getElementById('info-supplier').textContent = supplier;
            document.getElementById('info-date').textContent = date;
            document.getElementById('info-actual-weight').textContent = actualWeight.toFixed(2) + ' كجم';

            deliveryNoteInfo.style.display = 'block';
            calculateDiscrepancy();
        } else {
            deliveryNoteInfo.style.display = 'none';
            discrepancyCard.style.display = 'none';
        }
    });

    // حساب الفرق عند تغيير وزن الفاتورة
    invoiceWeightInput.addEventListener('input', calculateDiscrepancy);

    function calculateDiscrepancy() {
        const deliveryNoteSelect = document.getElementById('delivery_note_id');
        const selectedOption = deliveryNoteSelect.options[deliveryNoteSelect.selectedIndex];

        if (!deliveryNoteSelect.value || !invoiceWeightInput.value) {
            discrepancyCard.style.display = 'none';
            return;
        }

        const actualWeight = parseFloat(selectedOption.dataset.actualWeight) || 0;
        const invoiceWeight = parseFloat(invoiceWeightInput.value) || 0;
        const discrepancy = actualWeight - invoiceWeight;
        const percentage = invoiceWeight > 0 ? ((discrepancy / invoiceWeight) * 100) : 0;

        document.getElementById('display-actual-weight').textContent = actualWeight.toFixed(2) + ' كجم';
        document.getElementById('display-invoice-weight').textContent = invoiceWeight.toFixed(2) + ' كجم';
        document.getElementById('display-discrepancy').textContent = (discrepancy >= 0 ? '+' : '') + discrepancy.toFixed(2) + ' كجم';
        document.getElementById('display-discrepancy').className = 'mb-0 ' + (discrepancy >= 0 ? 'text-danger' : 'text-success');
        document.getElementById('display-percentage').textContent = '(' + (percentage >= 0 ? '+' : '') + percentage.toFixed(2) + '%)';

        discrepancyCard.style.display = 'block';

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
});
</script>
@endsection
