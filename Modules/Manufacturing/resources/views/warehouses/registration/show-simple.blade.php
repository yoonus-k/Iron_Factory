@extends('master')

@section('title', __('warehouse_registration.shipment_details'))

@section('content')
<style>
    .simple-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .success-banner {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(17, 153, 142, 0.3);
    }
    
    .barcode-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }
    
    .barcode-number {
        font-size: 36px;
        font-weight: bold;
        letter-spacing: 4px;
        font-family: 'Courier New', monospace;
        margin: 20px 0;
    }
    
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    
    .card-header-simple {
        font-size: 20px;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 3px solid #667eea;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .info-item-simple {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .info-label-simple {
        font-size: 14px;
        color: #7f8c8d;
        font-weight: 500;
    }
    
    .info-value-simple {
        font-size: 18px;
        color: #2c3e50;
        font-weight: bold;
    }
    
    .status-badge-simple {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: bold;
    }
    
    .status-success {
        background: #d4edda;
        color: #155724;
    }
    
    .status-warning {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-info {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .btn-group-simple {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }
    
    .btn-simple {
        padding: 12px 24px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-success {
        background: #28a745;
        color: white;
    }
    
    .btn-warning {
        background: #ffc107;
        color: #000;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-simple:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .progress-bar-container {
        background: #ecf0f1;
        border-radius: 10px;
        height: 30px;
        overflow: hidden;
        display: flex;
        border: 2px solid #bdc3c7;
    }
    
    .progress-segment {
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 13px;
        font-weight: bold;
    }
</style>

<div class="simple-container">
    <!-- رسالة النجاح -->
    @if (session('success'))
        <div class="success-banner">
            <div style="display: flex; align-items: center; gap: 20px;">
                <div style="font-size: 48px;">✅</div>
                <div style="flex: 1;">
                    <div style="font-size: 20px; font-weight: bold; margin-bottom: 10px;">{{ session('success') }}</div>
                    @if(session('batch_code'))
                        <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 10px; margin-top: 10px;">
                            <div style="font-size: 32px; font-weight: bold; letter-spacing: 3px; font-family: 'Courier New', monospace;">
                                {{ session('batch_code') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- معلومات الشحنة الأساسية -->
    <div class="info-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h1 style="font-size: 32px; color: #2c3e50; margin: 0;">📦 {{ __('warehouse_registration.delivery_note') }} #{{ $deliveryNote->note_number ?? $deliveryNote->id }}</h1>
                <div style="margin-top: 10px;">
                    <span class="status-badge-simple {{ $deliveryNote->registration_status === 'registered' ? 'status-success' : ($deliveryNote->registration_status === 'in_production' ? 'status-info' : 'status-warning') }}">
                        @switch($deliveryNote->registration_status)
                            @case('not_registered') ⏳ {{ __('warehouse_registration.pending') }} @break
                            @case('registered') ✅ {{ __('warehouse_registration.registered') }} @break
                            @case('in_production') 🏭 {{ __('warehouse_registration.in_production') }} @break
                            @case('completed') ✔️ {{ __('warehouse_registration.completed') }} @break
                            @default {{ $deliveryNote->registration_status }}
                        @endswitch
                    </span>
                </div>
            </div>
        </div>
        
        <div class="info-grid">
            <div class="info-item-simple">
                <span class="info-label-simple">🏢 {{ __('warehouse_registration.supplier') }}</span>
                <span class="info-value-simple">{{ $deliveryNote->supplier->name ?? 'غير محدد' }}</span>
            </div>
            
            <div class="info-item-simple">
                <span class="info-label-simple">📅 {{ __('warehouse_registration.arrival_date') }}</span>
                <span class="info-value-simple">{{ $deliveryNote->created_at->format('Y-m-d') }}</span>
            </div>
            
            <div class="info-item-simple">
                <span class="info-label-simple">📦 {{ __('warehouse_registration.material') }}</span>
                <span class="info-value-simple">{{ $deliveryNote->material->name_ar ?? 'غير محدد' }}</span>
            </div>
            
            <div class="info-item-simple">
                <span class="info-label-simple">⚖️ {{ __('warehouse_registration.actual_weight') }}</span>
                <span class="info-value-simple">{{ number_format($deliveryNote->actual_weight ?? 0, 2) }} كجم</span>
            </div>
        </div>
    </div>

    <!-- بطاقة الباركود -->
    @if($deliveryNote->materialBatch && $deliveryNote->materialBatch->batch_code)
        <div class="barcode-card">
            <div style="font-size: 22px; font-weight: bold; margin-bottom: 15px;">🏷️ {{ __('warehouse_registration.batch_barcode') }}</div>
            <svg id="warehouse-barcode" style="background: white; padding: 20px; border-radius: 12px; margin: 20px auto; display: block;"></svg>
            <div class="barcode-number">{{ $deliveryNote->materialBatch->batch_code }}</div>
            <div style="margin-top: 20px; opacity: 0.9;">
                <div style="margin-bottom: 5px;">📊 {{ __('warehouse_registration.quantity') }}: {{ number_format($deliveryNote->materialBatch->initial_quantity, 2) }} {{ __('warehouse_registration.kg') }}</div>
                <div>📅 {{ __('warehouse_registration.generation_date') }}: {{ $deliveryNote->materialBatch->created_at->format('Y-m-d H:i') }}</div>
            </div>
            <button onclick="printWarehouseBarcode('{{ $deliveryNote->materialBatch->batch_code }}', '{{ $deliveryNote->note_number }}', '{{ $deliveryNote->material->name_ar ?? 'غير محدد' }}', {{ $deliveryNote->materialBatch->initial_quantity }}, '{{ $deliveryNote->supplier->name ?? 'غير محدد' }}')" 
                    style="background: white; color: #667eea; padding: 15px 30px; border: none; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 20px; display: inline-flex; align-items: center; gap: 10px;">
                <i class="feather icon-printer"></i> {{ __('warehouse_registration.print_barcode') }}
            </button>
        </div>
    @endif

    <!-- الكميات والحالة -->
    @if($deliveryNote->quantity && $deliveryNote->quantity > 0)
        <div class="info-card">
            <div class="card-header-simple">📊 {{ __('warehouse_registration.quantity_distribution') }}</div>
            
            @php
                $totalQuantity = $deliveryNote->quantity ?? 0;
                $transferredQuantity = $deliveryNote->quantity_used ?? 0;
                $remainingQuantity = $deliveryNote->quantity_remaining ?? 0;
                $transferPercentage = $totalQuantity > 0 ? ($transferredQuantity / $totalQuantity * 100) : 0;
                $remainingPercentage = $totalQuantity > 0 ? ($remainingQuantity / $totalQuantity * 100) : 0;
            @endphp
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
                <div style="text-align: center; padding: 20px; background: #e3f2fd; border-radius: 12px;">
                    <div style="font-size: 32px; font-weight: bold; color: #1976d2;">{{ number_format($totalQuantity, 2) }}</div>
                    <div style="color: #1976d2; font-weight: 600;">{{ __('warehouse_registration.total_quantity') }}</div>
                </div>
                
                <div style="text-align: center; padding: 20px; background: #e8f5e9; border-radius: 12px;">
                    <div style="font-size: 32px; font-weight: bold; color: #388e3c;">{{ number_format($transferredQuantity, 2) }}</div>
                    <div style="color: #388e3c; font-weight: 600;">{{ __('warehouse_registration.transferred') }}</div>
                </div>
                
                <div style="text-align: center; padding: 20px; background: #fff3e0; border-radius: 12px;">
                    <div style="font-size: 32px; font-weight: bold; color: #f57c00;">{{ number_format($remainingQuantity, 2) }}</div>
                    <div style="color: #f57c00; font-weight: 600;">{{ __('warehouse_registration.remaining') }}</div>
                </div>
            </div>
            
            <div class="progress-bar-container">
                @if($transferPercentage > 0)
                    <div class="progress-segment" style="width: {{ $transferPercentage }}%; background: linear-gradient(90deg, #27ae60, #2ecc71);">
                        @if($transferPercentage > 15){{ number_format($transferPercentage, 0) }}%@endif
                    </div>
                @endif
                @if($remainingPercentage > 0)
                    <div class="progress-segment" style="width: {{ $remainingPercentage }}%; background: linear-gradient(90deg, #3498db, #5dade2);">
                        @if($remainingPercentage > 15){{ number_format($remainingPercentage, 0) }}%@endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- الأزرار -->
    <div class="btn-group-simple">
        <a href="{{ route('manufacturing.warehouse.registration.pending') }}" class="btn-simple btn-secondary">
            ← {{ __('warehouse_registration.return_to_list') }}
        </a>
        
        @if($canMoveToProduction ?? false)
            <a href="{{ route('manufacturing.warehouse.registration.transfer-form', $deliveryNote) }}" class="btn-simple btn-success">
                🚚 {{ __('warehouse_registration.transfer_to_production') }}
            </a>
        @endif
        
        @if(!$deliveryNote->is_locked && ($canEdit ?? false))
            <a href="{{ route('manufacturing.warehouse.registration.create', $deliveryNote) }}" class="btn-simple btn-warning">
                ✏️ {{ __('app.edit') }}
            </a>
        @endif
    </div>
</div>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
// رسم الباركود
@if($deliveryNote->materialBatch && $deliveryNote->materialBatch->batch_code)
document.addEventListener('DOMContentLoaded', function() {
    JsBarcode("#warehouse-barcode", "{{ $deliveryNote->materialBatch->batch_code }}", {
        format: "CODE128",
        width: 3,
        height: 100,
        displayValue: false,
        margin: 15
    });
});
@endif

// طباعة الباركود
function printWarehouseBarcode(barcode, noteNumber, materialName, quantity, supplierName) {
    const printWindow = window.open('', '', 'height=650,width=850');
    printWindow.document.write('<html dir="rtl"><head><title>طباعة الباركود - ' + noteNumber + '</title>');
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
    printWindow.document.write('<div class="title">{{ __('warehouse_registration.warehouse_barcode') }}</div>');
    printWindow.document.write('<div class="note-number">{{ __('warehouse_registration.delivery_note') }} ' + noteNumber + '</div>');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
    printWindow.document.write('<div class="info">');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('warehouse_registration.material') }}:</span><span class="value">' + materialName + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('warehouse_registration.quantity') }}:</span><span class="value">' + quantity + ' {{ __('warehouse_registration.kg') }}</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('warehouse_registration.supplier') }}:</span><span class="value">' + supplierName + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('app.date') }}:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
    printWindow.document.write('</div></div>');
    printWindow.document.write('<script>');
    printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2, height: 90, displayValue: false, margin: 12 });');
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
    printWindow.document.write('<\/script></body></html>');
    printWindow.document.close();
}
</script>

@endsection
