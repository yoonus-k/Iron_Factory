@extends('master')

@section('title', 'عرض كويلات الشحنة')

@section('content')
<!-- مكتبة JsBarcode لتوليد باركود قابل للمسح -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<style>
    .summary-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Header Section */
    .page-header {
        background: white;
        border-radius: 15px;
        padding: 25px 30px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-right: 5px solid #667eea;
    }

    .page-header h1 {
        margin: 0 0 10px 0;
        font-size: 24px;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-header .subtitle {
        color: #7f8c8d;
        font-size: 14px;
        margin: 0;
    }

    /* Info Cards */
    .info-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .info-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border-top: 4px solid #667eea;
        transition: all 0.3s;
    }

    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .info-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        color: #7f8c8d;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .info-card-icon {
        font-size: 20px;
    }

    .info-card-value {
        font-size: 26px;
        font-weight: bold;
        color: #2c3e50;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .btn-secondary {
        background: #ecf0f1;
        color: #2c3e50;
    }

    .btn-secondary:hover {
        background: #bdc3c7;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-success {
        background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
        color: white;
    }

    .btn-warning {
        background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
        color: white;
    }

    /* Coils Table */
    .coils-section {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .section-title {
        font-size: 18px;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 15px;
        border-bottom: 2px solid #ecf0f1;
    }

    .coils-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .coils-table thead th {
        background: #f8f9fa;
        padding: 15px;
        text-align: right;
        font-size: 13px;
        font-weight: 600;
        color: #7f8c8d;
        text-transform: uppercase;
        border: none;
    }

    .coils-table thead th:first-child {
        border-radius: 10px 0 0 10px;
    }

    .coils-table thead th:last-child {
        border-radius: 0 10px 10px 0;
    }

    .coils-table tbody tr {
        background: white;
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .coils-table tbody tr:hover {
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .coils-table tbody td {
        padding: 18px 15px;
        border-top: 1px solid #ecf0f1;
        border-bottom: 1px solid #ecf0f1;
    }

    .coils-table tbody td:first-child {
        border-right: 1px solid #ecf0f1;
        border-radius: 10px 0 0 10px;
    }

    .coils-table tbody td:last-child {
        border-left: 1px solid #ecf0f1;
        border-radius: 0 10px 10px 0;
    }

    .coil-number-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        font-weight: bold;
        font-size: 16px;
    }

    .barcode-cell {
        text-align: center;
    }

    .barcode-code {
        font-family: 'Courier New', monospace;
        font-size: 11px;
        color: #7f8c8d;
        margin-top: 5px;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-available {
        background: #d4edda;
        color: #155724;
    }

    .status-partial {
        background: #fff3cd;
        color: #856404;
    }

    .status-used {
        background: #f8d7da;
        color: #721c24;
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 13px;
        border-radius: 8px;
    }

    .barcode-loading {
        display: inline-block;
        width: 120px;
        height: 50px;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 6px;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    @media print {
        .no-print { display: none !important; }
        .coils-table tbody tr { page-break-inside: avoid; }
    }
</style>

<div class="summary-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <span style="font-size: 28px;">📦</span>
            كويلات الشحنة - أذن توريد رقم {{ $deliveryNote->id }}
        </h1>
        <p class="subtitle">عرض وإدارة جميع الكويلات المرتبطة بأذن التوريد</p>
    </div>

    <!-- Info Cards -->
    <div class="info-cards">
        <div class="info-card">
            <div class="info-card-header">
                <span class="info-card-icon">📅</span>
                <span>تاريخ التوريد</span>
            </div>
            <div class="info-card-value">{{ $deliveryNote->delivery_date }}</div>
        </div>
        
        <div class="info-card">
            <div class="info-card-header">
                <span class="info-card-icon">🏭</span>
                <span>المستودع</span>
            </div>
            <div class="info-card-value" style="font-size: 20px;">{{ $deliveryNote->warehouse->warehouse_name ?? 'غير محدد' }}</div>
        </div>
        
        <div class="info-card">
            <div class="info-card-header">
                <span class="info-card-icon">📦</span>
                <span>المادة</span>
            </div>
            <div class="info-card-value" style="font-size: 20px;">{{ $deliveryNote->material->name_ar ?? 'غير محدد' }}</div>
        </div>
        
        <div class="info-card" style="border-top-color: #4caf50;">
            <div class="info-card-header">
                <span class="info-card-icon">⚖️</span>
                <span>الكمية الإجمالية</span>
            </div>
            <div class="info-card-value" style="color: #4caf50;">{{ number_format($deliveryNote->quantity, 2) }} <span style="font-size: 18px;">كجم</span></div>
        </div>
        
        <div class="info-card" style="border-top-color: #2196f3;">
            <div class="info-card-header">
                <span class="info-card-icon">🔢</span>
                <span>عدد الكويلات</span>
            </div>
            <div class="info-card-value" style="color: #2196f3;">{{ $deliveryNote->coils()->count() }}</div>
        </div>
        
        <div class="info-card" style="border-top-color: #ff9800;">
            <div class="info-card-header">
                <span class="info-card-icon">📊</span>
                <span>الوزن المتبقي</span>
            </div>
            <div class="info-card-value" style="color: #ff9800;">{{ number_format($deliveryNote->coils()->sum('remaining_weight'), 2) }} <span style="font-size: 18px;">كجم</span></div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons no-print">
        <a href="{{ route('manufacturing.delivery-notes.index') }}" class="btn btn-secondary">
            <span>←</span>
            <span>العودة للقائمة</span>
        </a>
        
        <a href="{{ route('manufacturing.coils.transfer-index') }}" class="btn btn-success">
            <span>🔄</span>
            <span>نقل كويلات للإنتاج</span>
        </a>
        
        <button onclick="printAllBarcodes()" class="btn btn-warning">
            <span>🖨️</span>
            <span>طباعة جميع الباركودات</span>
        </button>
    </div>

    <!-- Coils Table -->
    <div class="coils-section">
        <div class="section-title">
            <span style="font-size: 22px;">📋</span>
            <span>قائمة الكويلات ({{ $coils->total() }} كويل)</span>
        </div>

        <table class="coils-table">
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">#</th>
                    <th>رقم الكويل</th>
                    <th style="text-align: center;">الوزن الأصلي</th>
                    <th style="text-align: center;">الوزن المتبقي</th>
                    <th style="text-align: center;">الباركود</th>
                    <th style="text-align: center;">الحالة</th>
                    <th style="text-align: center; width: 150px;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coils as $index => $coil)
                    <tr id="coil-{{ $coil->id }}">
                        <td style="text-align: center;">
                            <div class="coil-number-badge">#{{ ($coils->currentPage() - 1) * $coils->perPage() + $index + 1 }}</div>
                        </td>
                        
                        <td>
                            <strong style="font-size: 15px; color: #2c3e50;">{{ $coil->coil_number }}</strong>
                        </td>
                        
                        <td style="text-align: center;">
                            <strong style="color: #7f8c8d;">{{ number_format($coil->coil_weight, 3) }}</strong> كجم
                        </td>
                        
                        <td style="text-align: center;">
                            <strong style="color: #27ae60; font-size: 16px;">{{ number_format($coil->remaining_weight, 3) }}</strong> كجم
                        </td>
                        
                        <td class="barcode-cell">
                            <div style="position: relative; display: inline-block;">
                                <div class="barcode-loading" style="position: absolute; top: 0; left: 50%; transform: translateX(-50%);"></div>
                                <svg class="barcode" data-barcode="{{ $coil->coil_barcode }}" style="display: none; max-width: 150px;"></svg>
                            </div>
                            <div class="barcode-code">{{ $coil->coil_barcode }}</div>
                        </td>
                        
                        <td style="text-align: center;">
                            @if($coil->status === 'available')
                                <span class="status-badge status-available">✓ متاح</span>
                            @elseif($coil->status === 'partially_used')
                                <span class="status-badge status-partial">⚡ مستخدم جزئياً</span>
                            @else
                                <span class="status-badge status-used">✕ مستخدم بالكامل</span>
                            @endif
                        </td>
                        
                        <td style="text-align: center;">
                            <button onclick="printCoilBarcode({{ $coil->id }})" class="btn btn-success btn-sm" title="طباعة الباركود">
                                🖨️
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #7f8c8d;">
                            <div style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;">📦</div>
                            <p style="margin: 0; font-size: 16px;">لا توجد كويلات مسجلة لهذا الأذن</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($coils->hasPages())
            <div style="margin-top: 25px; display: flex; justify-content: center;">
                {{ $coils->links() }}
            </div>
        @endif
    </div>
</div>
</div>

<script>
// توليد جميع الباركودات في الصفحة
document.addEventListener('DOMContentLoaded', function() {
    const barcodes = document.querySelectorAll('.barcode');
    let index = 0;
    
    // توليد الباركودات بشكل تدريجي لتجنب تعليق الصفحة
    function generateNextBarcode() {
        if (index < barcodes.length) {
            const svg = barcodes[index];
            const code = svg.getAttribute('data-barcode');
            try {
                JsBarcode(svg, code, {
                    format: "CODE128",
                    width: 2,
                    height: 60,
                    displayValue: false,
                    margin: 5
                });
                // إخفاء مؤشر التحميل وإظهار الباركود
                const loadingEl = svg.parentElement.querySelector('.barcode-loading');
                if (loadingEl) {
                    loadingEl.style.display = 'none';
                }
                svg.style.display = 'block';
            } catch (e) {
                console.error('Error generating barcode:', e);
                // إخفاء مؤشر التحميل حتى في حالة الخطأ
                const loadingEl = svg.parentElement.querySelector('.barcode-loading');
                if (loadingEl) {
                    loadingEl.style.display = 'none';
                }
            }
            index++;
            // استخدام requestAnimationFrame للأداء الأفضل
            requestAnimationFrame(generateNextBarcode);
        }
    }
    
    // بدء التوليد
    if (barcodes.length > 0) {
        generateNextBarcode();
    }
});

// طباعة باركود كويل واحد
function printCoilBarcode(coilId) {
    const row = document.getElementById('coil-' + coilId);
    const coilNumber = row.querySelector('td:nth-child(2) strong').textContent;
    const weight = row.querySelector('td:nth-child(3) strong').textContent;
    const barcodeText = row.querySelector('.barcode').getAttribute('data-barcode');

    const printWindow = window.open('', '_blank', 'width=400,height=300');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>طباعة باركود - ${coilNumber}</title>
            <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    padding: 20px;
                    background: #f5f5f5;
                }
                .barcode-container {
                    text-align: center;
                    border: 3px solid #2c3e50;
                    padding: 30px;
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                }
                .company-name {
                    font-size: 22px;
                    font-weight: bold;
                    color: #2c3e50;
                    margin-bottom: 15px;
                }
                .info {
                    margin: 12px 0;
                    font-size: 16px;
                    color: #34495e;
                }
                .info strong {
                    color: #2c3e50;
                }
                .barcode-wrapper {
                    margin: 20px 0;
                    padding: 15px;
                    background: #f8f9fa;
                    border-radius: 8px;
                }
                .footer {
                    font-size: 11px;
                    color: #7f8c8d;
                    margin-top: 15px;
                    padding-top: 15px;
                    border-top: 1px solid #ecf0f1;
                }
                @media print {
                    body { 
                        padding: 0;
                        background: white;
                    }
                }
            </style>
        </head>
        <body>
            <div class="barcode-container">
                <div class="company-name">🏭 مصنع الحديد</div>
                <div class="info"><strong>رقم الكويل:</strong> ${coilNumber}</div>
                <div class="info"><strong>الوزن:</strong> ${weight} كجم</div>
                <div class="barcode-wrapper">
                    <svg id="printBarcode"></svg>
                </div>
                <div class="footer">
                    تاريخ الطباعة: ${new Date().toLocaleString('ar-EG')}
                </div>
            </div>
            <script>
                JsBarcode("#printBarcode", "${barcodeText}", {
                    format: "CODE128",
                    width: 2,
                    height: 80,
                    displayValue: true,
                    fontSize: 14,
                    margin: 10
                });
                window.onload = function() {
                    window.print();
                    setTimeout(function() { window.close(); }, 100);
                };
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}

// طباعة جميع الباركودات
function printAllBarcodes() {
    const coilCards = document.querySelectorAll('.coil-card');
    let html = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>طباعة جميع الباركودات</title>
            <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    padding: 20px;
                }
                .barcode-container {
                    text-align: center;
                    border: 2px solid #000;
                    padding: 20px;
                    margin-bottom: 30px;
                    page-break-after: always;
                    background: white;
                }
                .info {
                    margin: 10px 0;
                    font-size: 14px;
                }
                @media print {
                    body { padding: 0; }
                }
            </style>
        </head>
        <body>
    `;

    coilCards.forEach((card, index) => {
        const coilNumber = card.querySelector('.detail-value').textContent;
        const weight = card.querySelectorAll('.detail-value')[1].textContent;
        const barcodeText = card.querySelector('.barcode').getAttribute('data-barcode');

        html += `
            <div class="barcode-container">
                <h3>🏭 مصنع الحديد - كويل #${index + 1}</h3>
                <div class="info"><strong>رقم الكويل:</strong> ${coilNumber}</div>
                <div class="info"><strong>الوزن:</strong> ${weight}</div>
                <svg class="barcode-svg" data-code="${barcodeText}"></svg>
                <div class="info" style="font-size: 11px; color: #666;">تاريخ الطباعة: ${new Date().toLocaleString('ar-EG')}</div>
            </div>
        `;
    });

    html += `
            <script>
                document.querySelectorAll('.barcode-svg').forEach(function(svg) {
                    const code = svg.getAttribute('data-code');
                    JsBarcode(svg, code, {
                        format: "CODE128",
                        width: 2,
                        height: 80,
                        displayValue: true,
                        fontSize: 14,
                        margin: 10
                    });
                });
                window.onload = function() {
                    window.print();
                    setTimeout(function() { window.close(); }, 100);
                };
            <\/script>
        </body>
        </html>
    `;

    const printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.write(html);
    printWindow.document.close();
}
</script>

@endsection
