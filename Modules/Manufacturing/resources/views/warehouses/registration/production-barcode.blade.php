@extends('master')

@section('title', 'باركود الإنتاج')

@section('content')
<style>
    .production-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 30px 20px;
    }
    
    .production-header {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .production-card {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        padding: 50px 40px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(17, 153, 142, 0.3);
        margin-bottom: 30px;
    }
    
    .barcode-section {
        background: white;
        padding: 40px;
        border-radius: 15px;
        margin: 30px 0;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .barcode-number {
        font-size: 42px;
        font-weight: bold;
        letter-spacing: 4px;
        font-family: 'Courier New', monospace;
        color: #2c3e50;
        text-align: center;
        margin-top: 25px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin: 30px 0;
    }
    
    .info-item {
        background: rgba(255,255,255,0.2);
        padding: 20px;
        border-radius: 12px;
    }
    
    .info-label {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 8px;
    }
    
    .info-value {
        font-size: 20px;
        font-weight: bold;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        flex-wrap: wrap;
    }
    
    .btn-action {
        flex: 1;
        min-width: 200px;
        padding: 18px 30px;
        border: none;
        border-radius: 12px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: all 0.3s;
        text-decoration: none;
    }
    
    .btn-print {
        background: white;
        color: #11998e;
    }
    
    .btn-back {
        background: rgba(255,255,255,0.2);
        color: white;
        border: 2px solid white;
    }
    
    .btn-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .status-badge {
        display: inline-block;
        background: rgba(255,255,255,0.3);
        padding: 12px 25px;
        border-radius: 30px;
        font-size: 16px;
        font-weight: bold;
        margin: 20px 0;
    }
</style>

<div class="production-container">
    <div class="production-header">
        <h1 style="font-size: 42px; color: #2c3e50; margin: 0 0 15px 0;">🏭 باركود الإنتاج</h1>
        <p style="font-size: 18px; color: #7f8c8d;">أذن تسليم #{{ $deliveryNote->note_number ?? $deliveryNote->id }}</p>
    </div>

    <div class="production-card">
        <div style="text-align: center; margin-bottom: 25px;">
            <div style="font-size: 24px; font-weight: bold; margin-bottom: 10px;">✅ تم النقل للإنتاج بنجاح</div>
            <div class="status-badge">باركود فعّال للتتبع</div>
        </div>

        <div class="barcode-section">
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="font-size: 22px; font-weight: bold; color: #11998e; margin-bottom: 10px;">باركود الإنتاج</div>
                <div style="font-size: 14px; color: #7f8c8d;">استخدم هذا الباركود للتتبع في جميع مراحل الإنتاج</div>
            </div>
            
            <svg id="production-barcode" style="display: block; margin: 20px auto; max-width: 100%;"></svg>
            
            <div class="barcode-number">{{ $deliveryNote->production_barcode }}</div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">📦 المادة</div>
                <div class="info-value">{{ $deliveryNote->material->name_ar ?? 'غير محدد' }}</div>
            </div>
            
            <div class="info-item">
                <div class="info-label">🏢 المورد</div>
                <div class="info-value">{{ $deliveryNote->supplier->name ?? 'غير محدد' }}</div>
            </div>
            
            <div class="info-item">
                <div class="info-label">📅 تاريخ النقل</div>
                <div class="info-value">{{ now()->format('Y-m-d') }}</div>
            </div>
            
            <div class="info-item">
                <div class="info-label">⏰ الوقت</div>
                <div class="info-value">{{ now()->format('H:i') }}</div>
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 12px; margin-top: 20px; text-align: center;">
            <div style="font-size: 16px; line-height: 1.8;">
                💡 <strong>ملاحظة:</strong> هذا الباركود مرتبط بأذن التسليم الأصلي ويمكن استخدامه في جميع مراحل الإنتاج للتتبع الكامل للمنتج.
            </div>
        </div>

        <div class="action-buttons">
            <button onclick="printBarcode()" class="btn-action btn-print">
                <i class="feather icon-printer"></i>
                طباعة الباركود
            </button>
            
            <a href="{{ route('manufacturing.warehouse.registration.show', $deliveryNote->id) }}" class="btn-action btn-back">
                <i class="feather icon-arrow-right"></i>
                العودة للشحنة
            </a>
        </div>
    </div>
</div>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
// رسم الباركود
document.addEventListener('DOMContentLoaded', function() {
    JsBarcode("#production-barcode", "{{ $deliveryNote->production_barcode }}", {
        format: "CODE128",
        width: 3,
        height: 120,
        displayValue: false,
        margin: 20
    });
});

// طباعة الباركود
function printBarcode() {
    const printWindow = window.open('', '', 'height=700,width=900');
    printWindow.document.write('<html dir="rtl"><head><title>طباعة باركود الإنتاج</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
    printWindow.document.write('.print-container { background: white; padding: 60px; border-radius: 20px; box-shadow: 0 5px 30px rgba(0,0,0,0.1); text-align: center; max-width: 600px; }');
    printWindow.document.write('.header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; }');
    printWindow.document.write('.title { font-size: 32px; font-weight: bold; margin-bottom: 10px; }');
    printWindow.document.write('.subtitle { font-size: 18px; opacity: 0.95; }');
    printWindow.document.write('.barcode-wrapper { background: #f8f9fa; padding: 30px; border-radius: 15px; margin: 25px 0; border: 3px solid #11998e; }');
    printWindow.document.write('.barcode-number { font-size: 28px; font-weight: bold; color: #2c3e50; margin-top: 20px; letter-spacing: 4px; font-family: "Courier New", monospace; padding: 15px; background: white; border-radius: 10px; }');
    printWindow.document.write('.info-section { background: #f8f9fa; padding: 25px; border-radius: 12px; margin-top: 20px; text-align: right; }');
    printWindow.document.write('.info-row { margin: 15px 0; display: flex; justify-content: space-between; border-bottom: 1px solid #ddd; padding-bottom: 10px; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 16px; font-weight: 600; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 18px; }');
    printWindow.document.write('.badge { background: #11998e; color: white; padding: 12px 25px; border-radius: 25px; display: inline-block; margin: 15px 0; font-weight: bold; }');
    printWindow.document.write('@media print { body { background: white; } .no-print { display: none; } }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<div class="print-container">');
    printWindow.document.write('<div class="header">');
    printWindow.document.write('<div class="title">🏭 باركود الإنتاج</div>');
    printWindow.document.write('<div class="subtitle">أذن تسليم {{ $deliveryNote->note_number ?? $deliveryNote->id }}</div>');
    printWindow.document.write('</div>');
    printWindow.document.write('<div class="badge">✓ تم النقل للإنتاج</div>');
    printWindow.document.write('<div class="barcode-wrapper">');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-number">{{ $deliveryNote->production_barcode }}</div>');
    printWindow.document.write('</div>');
    printWindow.document.write('<div class="info-section">');
    printWindow.document.write('<div class="info-row"><span class="label">المادة:</span><span class="value">{{ $deliveryNote->material->name_ar ?? "غير محدد" }}</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">المورد:</span><span class="value">{{ $deliveryNote->supplier->name ?? "غير محدد" }}</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">باركود الإنتاج:</span><span class="value">{{ $deliveryNote->production_barcode }}</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">التاريخ:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">الوقت:</span><span class="value">' + new Date().toLocaleTimeString('ar-EG') + '</span></div>');
    printWindow.document.write('</div>');
    printWindow.document.write('</div>');
    printWindow.document.write('<script>');
    printWindow.document.write('JsBarcode("#print-barcode", "{{ $deliveryNote->production_barcode }}", { format: "CODE128", width: 2.5, height: 100, displayValue: false, margin: 15 });');
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 600); };');
    printWindow.document.write('<\/script></body></html>');
    printWindow.document.close();
}
</script>

@endsection
