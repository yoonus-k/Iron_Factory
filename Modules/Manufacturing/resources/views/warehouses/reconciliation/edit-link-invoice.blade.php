@extends('master')

@section('title', __('reconciliation.edit_link_invoice'))

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-auto">
                <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn btn-info">
                    ← {{ __('reconciliation.back') }}
                </a>
            </div>
            <div class="col">
                <h1 class="page-title">✏️ {{ __('reconciliation.edit_link_invoice') }}</h1>
                <p class="text-muted">{{ __('reconciliation.edit_link_invoice') }}</p>
            </div>
        </div>
    </div>

    <!-- Process Explanation -->
    <div class="alert alert-info mb-4" style="background-color: #e8f0ff; border-left: 4px solid #0051E5; color: #003FA0;">
        <h5 class="mb-2"><strong>📌 {{ __('reconciliation.how_it_works') }}</strong></h5>
        <ol style="margin: 0; padding-right: 20px;">
            <li>{{ __('reconciliation.step_1') }}</li>
            <li>{{ __('reconciliation.step_2') }}</li>
            <li>{{ __('reconciliation.step_3') }}</li>
            <li>{{ __('reconciliation.step_4') }}</li>
            <li>{{ __('reconciliation.step_5') }}</li>
        </ol>
        <hr class="my-2" style="border-top-color: #0051E5;">
        <small><strong>💡 {{ __('reconciliation.note') }}</strong> {{ __('reconciliation.note_text') }}</small>
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
            <h5>{{ __('reconciliation.error') }}:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('manufacturing.warehouses.reconciliation.link-invoice.update', $deliveryNote->id) }}" id="linkInvoiceForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- اختيار الأذن -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%); color: white;">
                        <h5 class="mb-0">📦 {{ __('reconciliation.delivery_note') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label"><strong>{{ __('reconciliation.search_delivery_notes') }} <span class="text-danger">*</span></strong></label>
                            <input type="text" id="delivery_note_search" class="form-control"
                                   placeholder="{{ __('reconciliation.search_delivery_notes') }}"
                                   value="#{{ $deliveryNote->note_number ?? $deliveryNote->id }} - {{ $deliveryNote->supplier->name }}">
                            <small class="text-muted d-block mt-1">{{ __('reconciliation.search_delivery_notes') }}</small>
                        </div>

                        <!-- نتائج البحث -->
                        <div id="delivery_notes_results" class="list-group" style="display: none; max-height: 300px; overflow-y: auto; position: absolute; z-index: 1000; width: 100%; margin-top: -5px;">
                        </div>

                        <input type="hidden" name="delivery_note_id" id="delivery_note_id" value="{{ $deliveryNote->id }}">

                        <!-- عرض معلومات الأذن المختارة -->
                        <div id="deliveryNoteInfo" style="display: block; background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #0051E5;">
                            <h6 class="mb-3"><strong>{{ __('reconciliation.delivery_note') }}:</strong></h6>
                            <div style="display: grid; gap: 10px;">
                                <div>
                                    <small class="text-muted">{{ __('reconciliation.delivery_note_number') }}:</small>
                                    <div id="info-note-number" style="font-weight: 600; color: #0051E5;">#{{ $deliveryNote->note_number ?? $deliveryNote->id }}</div>
                                </div>
                                <div>
                                    <small class="text-muted">{{ __('reconciliation.supplier') }}:</small>
                                    <div id="info-supplier" style="font-weight: 600;">{{ $deliveryNote->supplier->name }}</div>
                                </div>
                                <div>
                                    <small class="text-muted">{{ __('reconciliation.date') }}:</small>
                                    <div id="info-date" style="font-weight: 600;">{{ $deliveryNote->delivery_date?->format('Y-m-d') }}</div>
                                </div>
                                <div>
                                    <small class="text-muted">{{ __('reconciliation.actual_weight') }}:</small>
                                    <div id="info-actual-weight" style="font-weight: 600; color: #3E4651;">{{ number_format($deliveryNote->actual_weight, 2) }} {{ __('reconciliation.kg') }}</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="clearDeliveryNote">
                                    <i class="fas fa-times"></i> {{ __('reconciliation.clear') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات الفاتورة -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #3E4651 0%, #2C3339 100%); color: white;">
                        <h5 class="mb-0">📄 {{ __('reconciliation.invoice') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label"><strong>{{ __('reconciliation.search_invoices') }} <span class="text-danger">*</span></strong></label>
                            <input type="text" id="invoice_search" class="form-control"
                                   placeholder="{{ __('reconciliation.search_invoices') }}"
                                   value="{{ $deliveryNote->purchaseInvoice->invoice_number }} - {{ $deliveryNote->purchaseInvoice->supplier->name }}">
                            <small class="text-muted d-block mt-1">{{ __('reconciliation.search_invoices') }}</small>
                        </div>

                        <!-- نتائج البحث عن الفواتير -->
                        <div id="invoices_results" class="list-group" style="display: none; max-height: 300px; overflow-y: auto; position: absolute; z-index: 1000; width: 100%; margin-top: -5px;">
                        </div>

                        <input type="hidden" name="invoice_id" id="invoice_id" value="{{ $deliveryNote->purchase_invoice_id }}">
                        <input type="hidden" name="invoice_weight" id="invoice_weight" value="{{ $deliveryNote->invoice_weight }}">

                        <!-- عرض معلومات الفاتورة المختارة -->
                        <div id="invoiceInfo" style="display: block; background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #3E4651;">
                            <h6 class="mb-3"><strong>{{ __('reconciliation.invoice') }}:</strong></h6>
                            <div style="display: grid; gap: 10px;">
                                <div>
                                    <small class="text-muted">{{ __('reconciliation.invoice_number') }}:</small>
                                    <div id="info-invoice-number" style="font-weight: 600; color: #3E4651;">{{ $deliveryNote->purchaseInvoice->invoice_number }}</div>
                                </div>
                                <div>
                                    <small class="text-muted">{{ __('reconciliation.supplier') }}:</small>
                                    <div id="info-invoice-supplier" style="font-weight: 600;">{{ $deliveryNote->purchaseInvoice->supplier->name }}</div>
                                </div>
                                <div>
                                    <small class="text-muted">{{ __('reconciliation.date') }}:</small>
                                    <div id="info-invoice-date" style="font-weight: 600;">{{ $deliveryNote->purchaseInvoice->invoice_date?->format('Y-m-d') }}</div>
                                </div>
                                <div>
                                    <small class="text-muted">{{ __('reconciliation.weight') }}:</small>
                                    <div id="info-invoice-weight" style="font-weight: 600; color: #3E4651;">{{ number_format($deliveryNote->invoice_weight, 2) }} {{ __('reconciliation.weight') }}</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="clearInvoice">
                                    <i class="fas fa-times"></i> {{ __('reconciliation.clear') }}
                                </button>
                            </div>
                        </div>

                        <!-- عرض المنتجات في الفاتورة -->
                        <div id="invoiceItemsInfo" style="display: block; margin-top: 20px;">
                            <h6 class="mb-3"><strong>🛍️ {{ __('reconciliation.select_products') }}:</strong></h6>
                            <div id="invoiceItemsList" class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th style="text-align: right;">{{ __('reconciliation.product_name') }}</th>
                                            <th>{{ __('reconciliation.quantity') }}</th>
                                            <th>{{ __('reconciliation.material_unit') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="invoiceItemsBody">
                                        @foreach($deliveryNote->purchaseInvoice->items ?? [] as $item)
                                        <tr>
                                            <td style="text-align: right;">
                                                <strong>{{ $item->item_name ?? __('reconciliation.product_name') }}</strong>
                                            </td>
                                            <td>{{ number_format($item->quantity ?? 0, 2) }}</td>
                                            <td>{{ $item->unit ?? 'قطعة' }}</td>

                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- بطاقة إنشاء أذن تسليم من الفاتورة -->
        <div class="card mb-4" id="createDeliveryNoteCard" style="display: block; border-left: 4px solid #27ae60;">
            <div class="card-header" style="background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%); color: white;">
                <h5 class="mb-0">📦 {{ __('reconciliation.create_delivery_note') }}</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">{{ __('reconciliation.select_products') }}:</p>
                <div id="productsChecklistContainer">
                    @foreach($deliveryNote->purchaseInvoice->items ?? [] as $index => $item)
                    <div class="form-check" style="margin-bottom: 12px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                        <input class="form-check-input product-checkbox" type="checkbox" id="product_{{ $index }}"
                               data-index="{{ $index }}" data-item-id="{{ $item->id }}"
                               data-name="{{ $item->item_name ?? __('reconciliation.product_name') }}"
                               data-quantity="{{ $item->quantity ?? 0 }}"
                               data-unit="{{ $item->unit ?? __('reconciliation.material_unit') }}"
                               data-weight="{{ $item->weight ?? 0 }}">
                        <label class="form-check-label" for="product_{{ $index }}" style="cursor: pointer; margin-bottom: 0;">
                            <strong>{{ $item->item_name ?? __('reconciliation.product_name') }}</strong>
                            <br>
                            <small class="text-muted">{{ __('reconciliation.quantity') }}: {{ number_format($item->quantity ?? 0, 2) }} {{ $item->unit ?? __('reconciliation.material_unit') }}</small>
                            <br><small class="text-muted">{{ __('reconciliation.weight') }}: {{ number_format($item->weight ?? 0, 2) }} {{ $item->weight_unit ?? __('reconciliation.kg') }}</small>
                        </label>
                    </div>
                    @endforeach
                </div>

                <!-- ملخص البيانات المختارة -->
                <div id="selectionSummary" style="display: none; margin-top: 20px; padding: 15px; background: #e8f5e9; border-radius: 8px; border-left: 4px solid #27ae60;">
                    <h6 class="mb-3"><strong>📊 {{ __('reconciliation.summary') }}:</strong></h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>{{ __('reconciliation.selected_items') }}:</strong>
                                <span id="selectedItemsCount" class="badge bg-info">0</span>
                            </div>
                            <div class="mb-2">
                                <strong>{{ __('reconciliation.total_weight') }}:</strong>
                                <span id="selectedTotalWeight" style="font-weight: 600; color: #27ae60;">0.00 {{ __('reconciliation.kg') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>{{ __('reconciliation.total_quantity') }}:</strong>
                                <span id="selectedTotalQuantity" style="font-weight: 600; color: #27ae60;">0.00</span>
                            </div>
                            <div class="mb-2">
                                <strong>{{ __('reconciliation.supplier') }}:</strong>
                                <span id="selectedSupplier" style="font-weight: 600;">{{ $deliveryNote->purchaseInvoice->supplier->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success mt-3" id="createDeliveryNoteBtn">
                    <i class="fas fa-save"></i> {{ __('reconciliation.create_delivery_note') }}
                </button>
            </div>
        </div>

        <!-- حساب الفرق -->
        <div class="card mb-4" id="discrepancyCard" style="display: block; border-left: 4px solid #0051E5;">
            <div class="card-header" style="background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%); color: white;">
                <h5 class="mb-0">⚖️ {{ __('reconciliation.discrepancy_calculation') }}</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">{{ __('reconciliation.actual_weight') }}</small>
                            <h4 id="display-actual-weight" class="mb-0 text-success">{{ number_format($deliveryNote->actual_weight, 2) }} {{ __('reconciliation.kg') }}</h4>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                        <h3 class="mb-0">➖</h3>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">{{ __('reconciliation.invoice_weight') }}</small>
                            <h4 id="display-invoice-weight" class="mb-0 text-primary">{{ number_format($deliveryNote->invoice_weight, 2) }} {{ __('reconciliation.kg') }}</h4>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                        <h3 class="mb-0">=</h3>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">{{ __('reconciliation.difference') }}</small>
                            <h4 id="display-discrepancy" class="mb-0 {{ ($deliveryNote->actual_weight - $deliveryNote->invoice_weight) >= 0 ? 'text-danger' : 'text-success' }}">
                                {{ ($deliveryNote->actual_weight - $deliveryNote->invoice_weight) >= 0 ? '+' : '' }}{{ number_format($deliveryNote->actual_weight - $deliveryNote->invoice_weight, 2) }} {{ __('reconciliation.kg') }}
                            </h4>
                            <small id="display-percentage" class="text-muted">
                                ({{ $deliveryNote->invoice_weight > 0 ? number_format((($deliveryNote->actual_weight - $deliveryNote->invoice_weight) / $deliveryNote->invoice_weight) * 100, 2) : 0 }}%)
                            </small>
                        </div>
                    </div>
                </div>

                <!-- تحذير إذا كان هناك فرق كبير -->
                <div id="discrepancy-warning" style="display: {{ abs((($deliveryNote->actual_weight - $deliveryNote->invoice_weight) / $deliveryNote->invoice_weight) * 100) > 5 ? 'block' : 'none' }}; margin-top: 20px;">
                    <div class="alert alert-warning">
                        <strong>⚠️ {{ __('reconciliation.warning') }}:</strong> {{ __('reconciliation.error_message') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- ملاحظات -->
        <div class="card mb-4">
            <div class="card-header" style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                <h5 class="mb-0">📝 {{ __('reconciliation.notes') }}</h5>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label class="form-label">{{ __('reconciliation.notes') }}:</label>
                    <textarea name="reconciliation_notes" class="form-control @error('reconciliation_notes') is-invalid @enderror"
                        rows="3" placeholder="{{ __('reconciliation.remarks') }}">{{ old('reconciliation_notes', $deliveryNote->reconciliation_notes) }}</textarea>
                    @error('reconciliation_notes')
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
                        <strong>✓ {{ __('reconciliation.i_confirm') }}</strong>
                    </label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-info btn-lg" id="submitBtn" disabled>
                        <i class="fas fa-save"></i> {{ __('reconciliation.save') }}
                    </button>
                    <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn btn-info btn-lg">
                        <i class="fas fa-times"></i> {{ __('reconciliation.cancel') }}
                    </a>
                </div>

                <div class="alert alert-light mt-3 mb-0" style="border-left: 4px solid #0051E5;">
                    <small><strong>✓ {{ __('reconciliation.note') }}:</strong> {{ __('reconciliation.saved_successfully') }}</small>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    /* تنسيق نتائج البحث */
    #delivery_notes_results, #invoices_results {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        box-shadow: 0 2px 8px rgba(0, 81, 229, 0.1);
    }

    .delivery-note-item, .invoice-item {
        border-bottom: 1px solid #e9ecef !important;
        transition: all 0.2s ease;
        padding: 12px 15px !important;
    }

    .delivery-note-item:hover, .invoice-item:hover {
        background-color: #e8f0ff !important;
        border-left: 4px solid #0051E5 !important;
        padding-left: 11px !important;
    }

    .delivery-note-item:last-child, .invoice-item:last-child {
        border-bottom: none !important;
    }

    /* تنسيق الأزرار */
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

    /* تنسيق بطاقات المعلومات */
    #deliveryNoteInfo, #invoiceInfo {
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* تنسيق صناديق الحساب */
    .text-success {
        color: #27ae60 !important;
    }

    .text-danger {
        color: #E74C3C !important;
    }

    .text-primary {
        color: #0051E5 !important;
    }

    .d-flex.gap-2 {
        gap: 0.5rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // البيانات
    let deliveryNotesData = @json($deliveryNotes ?? []);
    let invoicesData = @json($invoices ?? []);

    // البيانات الحالية
    const currentDeliveryNote = @json($deliveryNote);
    const currentInvoice = @json($deliveryNote->purchaseInvoice);

    // دالة لتحويل التاريخ للميلادي
    function formatGregorianDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // عناصر البحث عن أذن التسليم
    const deliveryNoteSearchInput = document.getElementById('delivery_note_search');
    const deliveryNoteResultsList = document.getElementById('delivery_notes_results');
    const deliveryNoteIdInput = document.getElementById('delivery_note_id');
    const deliveryNoteInfo = document.getElementById('deliveryNoteInfo');
    const clearDeliveryNoteBtn = document.getElementById('clearDeliveryNote');

    // عناصر البحث عن الفاتورة
    const invoiceSearchInput = document.getElementById('invoice_search');
    const invoiceResultsList = document.getElementById('invoices_results');
    const invoiceIdInput = document.getElementById('invoice_id');
    const invoiceWeightInput = document.getElementById('invoice_weight');
    const invoiceInfo = document.getElementById('invoiceInfo');
    const clearInvoiceBtn = document.getElementById('clearInvoice');

    const discrepancyCard = document.getElementById('discrepancyCard');
    const confirmCheck = document.getElementById('confirmCheck');
    const submitBtn = document.getElementById('submitBtn');

    // ===== وظائف البحث عن أذن التسليم =====
    deliveryNoteSearchInput.addEventListener('input', function() {
        const searchText = this.value.toLowerCase().trim();

        if (searchText.length === 0) {
            deliveryNoteResultsList.style.display = 'none';
            return;
        }

        const filteredNotes = deliveryNotesData.filter(note => {
            const noteNumber = (note.note_number || '').toLowerCase();
            const supplier = (note.supplier?.name || '').toLowerCase();
            const date = (note.delivery_date || '').toLowerCase();

            return noteNumber.includes(searchText) ||
                   supplier.includes(searchText) ||
                   date.includes(searchText);
        });

        displayDeliveryNoteResults(filteredNotes);
    });

    function displayDeliveryNoteResults(notes) {
        if (notes.length === 0) {
            deliveryNoteResultsList.innerHTML = '<div class="p-3 text-muted text-center">لم يتم العثور على نتائج</div>';
            deliveryNoteResultsList.style.display = 'block';
            return;
        }

        deliveryNoteResultsList.innerHTML = notes.map(note => `
            <button type="button" class="list-group-item list-group-item-action delivery-note-item"
                    data-id="${note.id}"
                    data-actual-weight="${note.actual_weight || 0}"
                    data-supplier="${note.supplier?.name || 'N/A'}"
                    data-date="${formatGregorianDate(note.delivery_date)}"
                    data-note-number="${note.note_number}"
                    style="text-align: right;">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">#${note.note_number}</small>
                    <div>
                        <strong>${note.supplier?.name || 'N/A'}</strong>
                        <br>
                        <small class="text-muted">${formatGregorianDate(note.delivery_date)} | وزن: ${parseFloat(note.actual_weight || 0).toFixed(2)} كجم</small>
                    </div>
                </div>
            </button>
        `).join('');

        deliveryNoteResultsList.style.display = 'block';

        document.querySelectorAll('.delivery-note-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                selectDeliveryNote(this);
            });
        });
    }

    function selectDeliveryNote(element) {
        const id = element.dataset.id;
        const noteNumber = element.dataset.noteNumber;
        const supplier = element.dataset.supplier;
        const date = element.dataset.date;
        const actualWeight = element.dataset.actualWeight;

        deliveryNoteIdInput.value = id;
        deliveryNoteSearchInput.value = `#${noteNumber} - ${supplier}`;
        deliveryNoteResultsList.style.display = 'none';

        document.getElementById('info-note-number').textContent = `#${noteNumber}`;
        document.getElementById('info-supplier').textContent = supplier;
        document.getElementById('info-date').textContent = date;
        document.getElementById('info-actual-weight').textContent = `${parseFloat(actualWeight).toFixed(2)} كجم`;

        deliveryNoteInfo.style.display = 'block';
        calculateDiscrepancy();
    }

    clearDeliveryNoteBtn.addEventListener('click', function() {
        deliveryNoteIdInput.value = '';
        deliveryNoteSearchInput.value = '';
        deliveryNoteInfo.style.display = 'none';
        deliveryNoteResultsList.style.display = 'none';
        discrepancyCard.style.display = 'none';
    });

    // ===== وظائف البحث عن الفاتورة =====
    invoiceSearchInput.addEventListener('input', function() {
        const searchText = this.value.toLowerCase().trim();

        if (searchText.length === 0) {
            invoiceResultsList.style.display = 'none';
            return;
        }

        const filteredInvoices = invoicesData.filter(invoice => {
            const invoiceNumber = (invoice.invoice_number || '').toLowerCase();
            const supplier = (invoice.supplier?.name || '').toLowerCase();
            const date = (invoice.invoice_date || '').toLowerCase();

            return invoiceNumber.includes(searchText) ||
                   supplier.includes(searchText) ||
                   date.includes(searchText);
        });

        displayInvoiceResults(filteredInvoices);
    });

    function displayInvoiceResults(invoices) {
        if (invoices.length === 0) {
            invoiceResultsList.innerHTML = '<div class="p-3 text-muted text-center">لم يتم العثور على فواتير</div>';
            invoiceResultsList.style.display = 'block';
            return;
        }

        invoiceResultsList.innerHTML = invoices.map(invoice => {
            let displayWeight = invoice.weight || 0;
            if (displayWeight === 0 && invoice.items && invoice.items.length > 0) {
                displayWeight = invoice.items.reduce((total, item) => {
                    return total + (parseFloat(item.quantity) || 0);
                }, 0);
            }

            return `
                <button type="button" class="list-group-item list-group-item-action invoice-item"
                        data-id="${invoice.id}"
                        data-invoice-number="${invoice.invoice_number}"
                        data-supplier="${invoice.supplier?.name || 'N/A'}"
                        data-date="${formatGregorianDate(invoice.invoice_date)}"
                        data-weight="${displayWeight}"
                        style="text-align: right;">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">${invoice.invoice_number}</small>
                        <div>
                            <strong>${invoice.supplier?.name || 'N/A'}</strong>
                            <br>
                            <small class="text-muted">${formatGregorianDate(invoice.invoice_date)} | {{ __('reconciliation.total_quantity') }}: ${parseFloat(displayWeight).toFixed(2)} {{ __('reconciliation.material_unit') }}</small>
                        </div>
                    </div>
                </button>
            `;
        }).join('');

        invoiceResultsList.style.display = 'block';

        document.querySelectorAll('.invoice-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                selectInvoice(this);
            });
        });
    }

    function selectInvoice(element) {
        const id = element.dataset.id;
        const invoiceNumber = element.dataset.invoiceNumber;
        const supplier = element.dataset.supplier;
        const date = element.dataset.date;
        let weight = parseFloat(element.dataset.weight) || 0;

        const selectedInvoice = invoicesData.find(inv => inv.id == id);

        if (weight === 0 && selectedInvoice && selectedInvoice.items && selectedInvoice.items.length > 0) {
            weight = selectedInvoice.items.reduce((total, item) => {
                return total + (parseFloat(item.quantity) || 0);
            }, 0);
        }

        invoiceIdInput.value = id;
        invoiceSearchInput.value = `${invoiceNumber} - ${supplier}`;
        invoiceWeightInput.value = parseFloat(weight).toFixed(2);
        invoiceResultsList.style.display = 'none';

        document.getElementById('info-invoice-number').textContent = invoiceNumber;
        document.getElementById('info-invoice-supplier').textContent = supplier;
        document.getElementById('info-invoice-date').textContent = date;
        document.getElementById('info-invoice-weight').textContent = `${parseFloat(weight).toFixed(2)} {{ __('reconciliation.material_unit') }}`;

        invoiceInfo.style.display = 'block';

        if (selectedInvoice && selectedInvoice.items && selectedInvoice.items.length > 0) {
            displayInvoiceItems(selectedInvoice);
        }

        calculateDiscrepancy();
    }

    function displayInvoiceItems(invoice) {
        const invoiceItemsInfo = document.getElementById('invoiceItemsInfo');
        const invoiceItemsBody = document.getElementById('invoiceItemsBody');
        const productsChecklistContainer = document.getElementById('productsChecklistContainer');
        const createDeliveryNoteCard = document.getElementById('createDeliveryNoteCard');

        if (!invoice.items || invoice.items.length === 0) {
            invoiceItemsInfo.style.display = 'none';
            createDeliveryNoteCard.style.display = 'none';
            return;
        }

        invoiceItemsBody.innerHTML = invoice.items.map((item, index) => {
            const itemName = item.item_name || '{{ __('reconciliation.product_name') }}';
            const weight = item.weight ? parseFloat(item.weight).toFixed(2) : '0.00';
            const unit = item.unit || '{{ __('reconciliation.material_unit') }}';

            return `
                <tr>
                    <td style="text-align: right;">
                        <strong>${itemName}</strong>
                    </td>
                    <td>${parseFloat(item.quantity || 0).toFixed(2)}</td>
                    <td>${unit}</td>
                    <td>${weight} ${item.weight_unit || '{{ __('reconciliation.kg') }}'}</td>
                </tr>
            `;
        }).join('');

        invoiceItemsInfo.style.display = 'block';

        productsChecklistContainer.innerHTML = invoice.items.map((item, index) => {
            const itemName = item.item_name || '{{ __('reconciliation.product_name') }}';
            const quantity = parseFloat(item.quantity || 0).toFixed(2);
            const unit = item.unit || '{{ __('reconciliation.material_unit') }}';
            const weight = item.weight ? parseFloat(item.weight).toFixed(2) : '0.00';

            return `
                <div class="form-check" style="margin-bottom: 12px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                    <input class="form-check-input product-checkbox" type="checkbox" id="product_${index}"
                           data-index="${index}" data-item-id="${item.id}" data-name="${itemName}"
                           data-quantity="${quantity}" data-unit="${unit}" data-weight="${weight}">
                    <label class="form-check-label" for="product_${index}" style="cursor: pointer; margin-bottom: 0;">
                        <strong>${itemName}</strong>
                        <br>
                        <small class="text-muted">{{ __('reconciliation.quantity') }}: ${quantity} ${unit}</small>
                        <br><small class="text-muted">{{ __('reconciliation.weight') }}: ${weight} ${item.weight_unit || '{{ __('reconciliation.kg') }}'}</small>
                    </label>
                </div>
            `;
        }).join('');

        createDeliveryNoteCard.style.display = 'block';

        document.getElementById('createDeliveryNoteBtn').addEventListener('click', createDeliveryNoteFromInvoice);

        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectionSummary);
        });

        updateSelectionSummary();
    }

    function updateSelectionSummary() {
        const selectedItems = Array.from(document.querySelectorAll('.product-checkbox:checked'));
        const selectionSummary = document.getElementById('selectionSummary');
        const invoiceId = invoiceIdInput.value;
        const selectedInvoice = invoicesData.find(inv => inv.id == invoiceId) || currentInvoice;

        if (selectedItems.length === 0) {
            selectionSummary.style.display = 'none';
            return;
        }

        let totalWeight = 0;
        let totalQuantity = 0;

        selectedItems.forEach(checkbox => {
            const index = checkbox.dataset.index;
            const item = selectedInvoice.items[index];
            const itemWeight = parseFloat(item.weight) || 0;
            const itemQuantity = parseFloat(item.quantity) || 0;

            totalWeight += itemWeight;
            totalQuantity += itemQuantity;
        });

        document.getElementById('selectedItemsCount').textContent = selectedItems.length;
        document.getElementById('selectedTotalWeight').textContent = totalWeight.toFixed(2) + ' {{ __('reconciliation.kg') }}';
        document.getElementById('selectedTotalQuantity').textContent = totalQuantity.toFixed(2);
        document.getElementById('selectedSupplier').textContent = selectedInvoice.supplier?.name || '-';

        selectionSummary.style.display = 'block';
    }

    function createDeliveryNoteFromInvoice() {
        const selectedItems = Array.from(document.querySelectorAll('.product-checkbox:checked'));

        if (selectedItems.length === 0) {
            alert('{{ __('reconciliation.please_select') }}');
            return;
        }

        const invoiceId = invoiceIdInput.value;
        const selectedInvoice = invoicesData.find(inv => inv.id == invoiceId) || currentInvoice;

        if (!selectedInvoice) {
            alert('{{ __('reconciliation.error') }}: {{ __('reconciliation.no_data_found') }}');
            return;
        }

        const selectedItemIds = selectedItems.map(checkbox => {
            const index = checkbox.dataset.index;
            return selectedInvoice.items[index].id;
        });

        const csrfToken = document.querySelector('input[name="_token"]')?.value ||
                         document.querySelector('meta[name="csrf-token"]')?.content || '';

        fetch('{{ route("manufacturing.warehouses.reconciliation.api.create-delivery-note-from-invoice") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                invoice_id: invoiceId,
                selected_items: selectedItemIds
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.error || '{{ __('reconciliation.error') }}');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const message = `✅ {{ __('reconciliation.created_successfully') }}!\n━━━━━━━━━━━━━━━━━━━━━\n📄 {{ __('reconciliation.delivery_note_number') }}: ${data.note_number}\n📦 {{ __('reconciliation.items_count') }}: ${data.items_count}\n⚖️ {{ __('reconciliation.total_weight') }}: ${data.total_weight.toFixed(2)} {{ __('reconciliation.kg') }}\n📊 {{ __('reconciliation.total_quantity') }}: ${data.total_quantity.toFixed(2)}\n${data.is_matched ? '✓ {{ __('reconciliation.weights_matched') }}' : '⚠️ {{ __('reconciliation.weight_discrepancy') }} ' + Math.abs(data.discrepancy).toFixed(2) + ' {{ __('reconciliation.kg') }}'}\n━━━━━━━━━━━━━━━━━━━━━`;

                alert(message);

                clearInvoiceBtn.click();

                setTimeout(() => window.location.reload(), 1500);
            }
        })
        .catch(error => {
            console.error('❌ {{ __('reconciliation.error') }}:', error);
            alert('❌ {{ __('reconciliation.error') }}: ' + error.message);
        });
    }

    clearInvoiceBtn.addEventListener('click', function() {
        invoiceIdInput.value = '';
        invoiceSearchInput.value = '';
        invoiceWeightInput.value = '';
        invoiceInfo.style.display = 'none';
        document.getElementById('invoiceItemsInfo').style.display = 'none';
        document.getElementById('createDeliveryNoteCard').style.display = 'none';
        invoiceResultsList.style.display = 'none';
        discrepancyCard.style.display = 'none';
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#delivery_note_search') && !e.target.closest('#delivery_notes_results')) {
            deliveryNoteResultsList.style.display = 'none';
        }
        if (!e.target.closest('#invoice_search') && !e.target.closest('#invoices_results')) {
            invoiceResultsList.style.display = 'none';
        }
    });

    // ===== حساب الفرق =====
    function calculateDiscrepancy() {
        const deliveryNoteId = deliveryNoteIdInput.value;
        const invoiceWeight = parseFloat(invoiceWeightInput.value) || 0;

        if (!deliveryNoteId || !invoiceWeight) {
            discrepancyCard.style.display = 'none';
            return;
        }

        const selectedNote = deliveryNotesData.find(n => n.id == deliveryNoteId) || currentDeliveryNote;
        if (!selectedNote) {
            discrepancyCard.style.display = 'none';
            return;
        }

        const actualWeight = parseFloat(selectedNote.actual_weight) || 0;
        const discrepancy = actualWeight - invoiceWeight;
        const percentage = invoiceWeight > 0 ? ((discrepancy / invoiceWeight) * 100) : 0;

        document.getElementById('display-actual-weight').textContent = actualWeight.toFixed(2) + ' كجم';
        document.getElementById('display-invoice-weight').textContent = invoiceWeight.toFixed(2) + ' كجم';
        document.getElementById('display-discrepancy').textContent = (discrepancy >= 0 ? '+' : '') + discrepancy.toFixed(2) + ' كجم';
        document.getElementById('display-discrepancy').className = 'mb-0 ' + (discrepancy >= 0 ? 'text-danger' : 'text-success');
        document.getElementById('display-percentage').textContent = '(' + (percentage >= 0 ? '+' : '') + percentage.toFixed(2) + '%)';

        discrepancyCard.style.display = 'block';

        const warningDiv = document.getElementById('discrepancy-warning');
        if (Math.abs(percentage) > 5) {
            warningDiv.style.display = 'block';
        } else {
            warningDiv.style.display = 'none';
        }
    }

    invoiceWeightInput.addEventListener('input', calculateDiscrepancy);

    // تفعيل/تعطيل زر الإرسال
    confirmCheck.addEventListener('change', function() {
        submitBtn.disabled = !this.checked;
    });

    // إضافة مستمعين لـ checkboxes الموجودة مسبقاً
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectionSummary);
    });
});
</script>
@endsection
