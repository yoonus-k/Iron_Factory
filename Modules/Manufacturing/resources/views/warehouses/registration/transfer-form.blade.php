@extends('master')

@section('title', __('warehouse_registration.transfer_to_production'))

@section('content')
<style>
    .simple-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .header-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    }
    
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    
    .card-title {
        font-size: 20px;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 3px solid #667eea;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .stat-box {
        text-align: center;
        padding: 25px;
        border-radius: 12px;
    }
    
    .stat-number {
        font-size: 36px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 14px;
        font-weight: 600;
        opacity: 0.8;
    }
    
    .input-group-simple {
        margin-bottom: 25px;
    }
    
    .label-simple {
        display: block;
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .input-simple {
        width: 100%;
        padding: 15px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 20px;
        font-weight: bold;
        transition: all 0.3s;
        color: #667eea;
    }
    
    .input-simple:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .btn-full {
        background: #3498db;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 10px;
    }
    
    .btn-full:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        color: white;
        padding: 18px 40px;
        border: none;
        border-radius: 12px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        width: 100%;
        transition: all 0.3s;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(39, 174, 96, 0.4);
    }
    
    .btn-cancel {
        background: #95a5a6;
        color: white;
        padding: 18px 40px;
        border: none;
        border-radius: 12px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        width: 100%;
        text-align: center;
        display: block;
        text-decoration: none;
        margin-top: 10px;
    }
    
    .barcode-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .barcode-number {
        font-size: 32px;
        font-weight: bold;
        letter-spacing: 3px;
        font-family: 'Courier New', monospace;
        margin: 15px 0;
    }
    
    .preview-box {
        background: #f0f9ff;
        border: 2px solid #3498db;
        padding: 20px;
        border-radius: 12px;
        margin-top: 20px;
    }
    
    .preview-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #ddd;
    }
    
    .preview-item:last-child {
        border-bottom: none;
    }
</style>

<div class="simple-container">
    <!-- Header -->
    <div class="header-card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="margin: 0; font-size: 28px; margin-bottom: 5px;">🚚 {{ __('warehouse_registration.transfer_to_production') }}</h1>
                <p style="margin: 0; opacity: 0.9;">أذن #{{ $deliveryNote->note_number ?? $deliveryNote->id }}</p>
            </div>
            <a href="{{ route('manufacturing.warehouse.registration.show', $deliveryNote) }}" style="background: rgba(255,255,255,0.2); color: white; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: bold; border: 2px solid white;">
                {{ __('app.back') }}
            </a>
        </div>
    </div>

    @if (session('error'))
        <div style="background: #f8d7da; border: 2px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
            ❌ {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background: #f8d7da; border: 2px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
            <strong>{{ __('app.error') }}:</strong>
            <ul style="margin: 10px 0 0 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- بطاقة الباركود
    @if($deliveryNote->materialBatch && $deliveryNote->materialBatch->batch_code)
        <div class="barcode-card">
            <div style="font-size: 18px; margin-bottom: 10px;">🏷️ باركود الدفعة</div>
            <svg id="transfer-barcode" style="background: white; padding: 15px; border-radius: 10px; margin: 15px auto; display: block;"></svg>
            <div class="barcode-number">{{ $deliveryNote->materialBatch->batch_code }}</div>
            <button onclick="printTransferBarcode('{{ $deliveryNote->materialBatch->batch_code }}', '{{ $deliveryNote->note_number }}', '{{ $deliveryNote->material->name_ar ?? 'غير محدد' }}', {{ $availableQuantity }})" 
                    style="background: white; color: #667eea; padding: 12px 25px; border: none; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 15px;">
                🖨️ طباعة الباركود
            </button>
        </div>
    @endif -->

    <!-- معلومات الشحنة - مخفي -->

    <!-- الكميات -->
    <div class="info-card">
        <div class="card-title">📊 {{ __('warehouse_registration.quantities') }}</div>
        
        <div class="stats-grid">
            <div class="stat-box" style="background: #e3f2fd;">
                <div class="stat-number" style="color: #1976d2;">{{ number_format($registeredQuantity, 2) }}</div>
                <div class="stat-label" style="color: #1976d2;">{{ __('warehouse_registration.total_registered') }}</div>
            </div>
            
            <div class="stat-box" style="background: #e8f5e9;">
                <div class="stat-number" style="color: #388e3c;">{{ number_format($transferredQuantity, 2) }}</div>
                <div class="stat-label" style="color: #388e3c;">{{ __('warehouse_registration.transferred') }}</div>
            </div>
            
            <div class="stat-box" style="background: #fff3e0;">
                <div class="stat-number" style="color: #f57c00;">{{ number_format($availableQuantity, 2) }}</div>
                <div class="stat-label" style="color: #f57c00;">{{ __('warehouse_registration.available_to_transfer') }}</div>
            </div>
        </div>
    </div>

    <!-- نموذج النقل -->
    <form id="transferForm" action="{{ route('manufacturing.warehouse.registration.transfer-to-production', $deliveryNote) }}" method="POST" onsubmit="console.log('Form submitted'); return true;">
        @csrf
        
        <div class="info-card">
            <div class="card-title">✏️ {{ __('warehouse_registration.transfer_details') }}</div>
            
            <!-- المادة -->
            <div class="input-group-simple">
                <label class="label-simple">📦 {{ __('warehouse_registration.material') }}</label>
                <div class="input-simple" style="background: #f8f9fa; color: #2c3e50; cursor: default;">
                    {{ $deliveryNote->material->name_ar ?? 'غير محدد' }}
                </div>
            </div>
            
            <!-- اختيار المرحلة الإنتاجية -->
            <div class="input-group-simple">
                <label class="label-simple">🏭 {{ __('warehouse_registration.production_stage') }} <span style="color: #e74c3c;">*</span></label>
                <select name="production_stage" id="productionStage" class="input-simple" required style="cursor: pointer;">
                    @php
                        $productionStages = \App\Models\ProductionStage::getActiveStages();
                    @endphp
                    @foreach($productionStages as $stage)
                        <option value="{{ $stage->stage_code }}" {{ $stage->stage_code == 'stage_1' ? 'selected' : '' }}>
                            {{ $stage->stage_name }} 
                           
                        </option>
                    @endforeach
                </select>
                <small style="color: #7f8c8d; font-size: 13px; margin-top: 5px; display: block;">
                    {{ __('warehouse_registration.first_stage_default') }}
                </small>
            </div>
            
            <!-- اختيار الموظف المستلم -->
            <div class="input-group-simple">
                <label class="label-simple">👤 {{ __('warehouse_registration.receiving_employee') }} <span style="color: #e74c3c;">*</span></label>
                <select name="assigned_to" id="assignedTo" class="input-simple" required style="cursor: pointer;">
                    <option value="">{{ __('warehouse_registration.select_receiving_employee') }}</option>
                    @php
                        $workers = \App\Models\User::where('is_active', 1)
                            ->orderBy('name')
                            ->get();
                    @endphp
                    @foreach($workers as $worker)
                        <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                    @endforeach
                </select>
                <small style="color: #7f8c8d; font-size: 13px; margin-top: 5px; display: block;">
                    {{ __('warehouse_registration.notification_sent_employee') }}
                </small>
            </div>
            
            <div class="input-group-simple">
                <label class="label-simple">⚖️ {{ __('warehouse_registration.quantity_kg') }} <span style="color: #e74c3c;">*</span></label>
                <input type="number" 
                       name="quantity" 
                       id="quantityInput"
                       class="input-simple" 
                       step="0.01" 
                       min="0.01" 
                       max="{{ $availableQuantity }}"
                       value="{{ old('quantity', $availableQuantity) }}"
                       placeholder="أدخل الكمية" 
                       required>
                
                <button type="button" class="btn-full" onclick="document.getElementById('quantityInput').value = {{ $availableQuantity }}; updatePreview();">
                    {{ __('warehouse_registration.use_all') }} ({{ number_format($availableQuantity, 2) }} {{ __('warehouse_registration.kg') }})
                </button>
            </div>
            
            <!-- معاينة مباشرة -->
            <div class="preview-box">
                <div style="font-weight: bold; color: #3498db; margin-bottom: 15px; text-align: center;">📋 {{ __('warehouse_registration.transfer_preview') }}</div>
                
                <div class="preview-item">
                    <span style="color: #7f8c8d;">{{ __('warehouse_registration.transferred_quantity') }}:</span>
                    <span id="transferAmount" style="font-weight: bold; color: #27ae60;">{{ number_format($availableQuantity, 2) }} {{ __('warehouse_registration.kg') }}</span>
                </div>
                
                <div class="preview-item">
                    <span style="color: #7f8c8d;">{{ __('warehouse_registration.remaining_in_warehouse') }}:</span>
                    <span id="remaining" style="font-weight: bold; color: #f57c00;">0.00 {{ __('warehouse_registration.kg') }}</span>
                </div>
                
                <div class="preview-item">
                    <span style="color: #7f8c8d;">{{ __('warehouse_registration.shipment_status') }}:</span>
                    <span id="statusBadge" style="background: #f39c12; color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: bold;">⏳ {{ __('warehouse_registration.awaiting_confirmation') }}</span>
                </div>
            </div>
            
            <div class="input-group-simple" style="margin-top: 25px;">
                <label class="label-simple">📝 {{ __('app.notes') }} ({{ __('app.optional') }})</label>
                <textarea name="notes" 
                          class="input-simple" 
                          rows="3" 
                          placeholder="{{ __('warehouse_registration.transfer_notes_placeholder') }}"
                          style="font-size: 16px; font-weight: normal; resize: vertical;"></textarea>
            </div>
        </div>

        <!-- الأزرار -->
        <div style="margin-top: 30px;">
            <button type="submit" class="btn-submit">
                <span style="font-size: 24px;">✓</span>
                <span>{{ __('warehouse_registration.confirm_transfer_to_production') }}</span>
            </button>
            <a href="{{ route('manufacturing.warehouse.registration.show', $deliveryNote) }}" class="btn-cancel">
                {{ __('app.cancel') }}
            </a>
        </div>
    </form>

    <!-- نصائح سريعة -->
    <div class="info-card" style="background: #f0f9ff; border: 2px solid #3498db;">
        <div style="display: flex; align-items: start; gap: 15px;">
            <div style="font-size: 32px;">💡</div>
            <div>
                <div style="font-weight: bold; color: #2c3e50; margin-bottom: 10px;">{{ __('warehouse_registration.quick_tips') }}:</div>
                <ul style="margin: 0; padding-right: 20px; color: #555; line-height: 1.8;">
                    <li>{{ __('warehouse_registration.transfer_full_or_partial') }}</li>
                    <li>{{ __('warehouse_registration.first_stage_default_tip') }}</li>
                    <li>{{ __('warehouse_registration.notification_sent_tip') }}</li>
                    <li>{{ __('warehouse_registration.batch_moves_after_confirmation') }}</li>
                    <li>{{ __('warehouse_registration.quantity_returns_if_rejected') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
const availableQuantity = {{ $availableQuantity }};
const quantityInput = document.getElementById('quantityInput');

// رسم الباركود
@if($deliveryNote->materialBatch && $deliveryNote->materialBatch->batch_code)
document.addEventListener('DOMContentLoaded', function() {
    JsBarcode("#transfer-barcode", "{{ $deliveryNote->materialBatch->batch_code }}", {
        format: "CODE128",
        width: 3,
        height: 80,
        displayValue: false,
        margin: 10
    });
});
@endif

// تحديث المعاينة
function updatePreview() {
    const quantity = parseFloat(quantityInput.value) || 0;
    const remaining = availableQuantity - quantity;
    const isFullTransfer = Math.abs(remaining) < 0.01;
    
    document.getElementById('transferAmount').textContent = quantity.toFixed(2) + ' كجم';
    document.getElementById('remaining').textContent = Math.max(0, remaining).toFixed(2) + ' كجم';
    
    const statusBadge = document.getElementById('statusBadge');
    if (isFullTransfer) {
        statusBadge.innerHTML = '🏭 في الإنتاج';
        statusBadge.style.background = '#27ae60';
    } else {
        statusBadge.innerHTML = '📋 مسجلة (نقل جزئي)';
        statusBadge.style.background = '#3498db';
    }
}

quantityInput.addEventListener('input', updatePreview);

// طباعة الباركود
function printTransferBarcode(barcode, noteNumber, materialName, quantity) {
    const printWindow = window.open('', '', 'height=650,width=850');
    printWindow.document.write('<html dir="rtl"><head><title>طباعة الباركود - نقل للإنتاج</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
    printWindow.document.write('.barcode-container { background: white; padding: 50px; border-radius: 16px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); text-align: center; max-width: 550px; }');
    printWindow.document.write('.title { font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 4px solid #667eea; }');
    printWindow.document.write('.note-number { font-size: 24px; color: #667eea; font-weight: bold; margin: 20px 0; }');
    printWindow.document.write('.barcode-code { font-size: 22px; font-weight: bold; color: #2c3e50; margin: 25px 0; letter-spacing: 4px; font-family: "Courier New", monospace; }');
    printWindow.document.write('.info { margin-top: 30px; padding: 25px; background: #f8f9fa; border-radius: 10px; text-align: right; }');
    printWindow.document.write('.info-row { margin: 12px 0; display: flex; justify-content: space-between; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 16px; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 18px; }');
    printWindow.document.write('@media print { body { background: white; } }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<div class="barcode-container">');
    printWindow.document.write('<div class="title">{{ __('warehouse_registration.production_transfer_barcode') }}</div>');
    printWindow.document.write('<div class="note-number">أذن تسليم ' + noteNumber + '</div>');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
    printWindow.document.write('<div class="info">');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('warehouse_registration.material') }}:</span><span class="value">' + materialName + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('warehouse_registration.transferred_quantity') }}:</span><span class="value">' + quantity + ' {{ __('warehouse_registration.kg') }}</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">التاريخ:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('app.status') }}:</span><span class="value">🚚 {{ __('warehouse_registration.transfer_completed') }}</span></div>');
    printWindow.document.write('</div></div>');
    printWindow.document.write('<script>');
    printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2, height: 90, displayValue: false, margin: 12 });');
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
    printWindow.document.write('<\/script></body></html>');
    printWindow.document.close();
}
</script>

@endsection
