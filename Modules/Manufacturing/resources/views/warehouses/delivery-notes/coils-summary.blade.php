@extends('master')

@section('title', 'عرض كويلات الشحنة')

@section('content')
<!-- مكتبة JsBarcode لتوليد باركود قابل للمسح -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<style>
    .summary-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .summary-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .summary-header h1 {
        margin: 0 0 15px 0;
        font-size: 28px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .summary-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .info-item {
        background: rgba(255, 255, 255, 0.1);
        padding: 15px;
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }

    .info-label {
        font-size: 12px;
        opacity: 0.9;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 18px;
        font-weight: bold;
    }

    .coils-grid {
        display: grid;
        gap: 15px;
    }

    .coil-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        display: grid;
        grid-template-columns: 60px 1fr 1fr 1fr 1fr 120px 120px;
        gap: 15px;
        align-items: center;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .coil-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.12);
    }

    .coil-number {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 20px;
    }

    .coil-detail {
        display: flex;
        flex-direction: column;
    }

    .detail-label {
        font-size: 11px;
        color: #777;
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    .detail-value {
        font-size: 15px;
        font-weight: 600;
        color: #333;
    }

    .barcode-display {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        background: #f5f5f5;
        padding: 8px;
        border-radius: 5px;
        border: 1px dashed #999;
        text-align: center;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
    }

    .status-available {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .status-partial {
        background: #fff3e0;
        color: #ef6c00;
    }

    .status-used {
        background: #ffebee;
        color: #c62828;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-print {
        background: #4caf50;
        color: white;
    }

    .btn-print:hover {
        background: #45a049;
        transform: scale(1.05);
    }

    .btn-transfer {
        background: #2196f3;
        color: white;
    }

    .btn-transfer:hover {
        background: #1976d2;
        transform: scale(1.05);
    }

    .btn-back {
        background: #757575;
        color: white;
        margin-bottom: 20px;
    }

    .btn-back:hover {
        background: #616161;
    }

    .btn-print-all {
        background: #ff9800;
        color: white;
        margin-bottom: 20px;
        margin-right: 10px;
    }

    .btn-print-all:hover {
        background: #f57c00;
    }

    .stats-section {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        margin-top: 15px;
    }

    .stat-item {
        text-align: center;
        padding: 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 10px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: bold;
        color: #667eea;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 13px;
        color: #666;
    }

    /* مؤشر التحميل للباركودات */
    .barcode-loading {
        display: inline-block;
        width: 150px;
        height: 60px;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 4px;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    @media print {
        .no-print {
            display: none !important;
        }
        
        .coil-card {
            page-break-inside: avoid;
            margin-bottom: 20px;
        }
    }
</style>

<div class="summary-container">
    <!-- Header -->
    <div class="summary-header">
        <h1>
            <span>📦</span>
            <span>كويلات الشحنة - أذن توريد رقم {{ $deliveryNote->id }}</span>
        </h1>
        <div class="summary-info">
            <div class="info-item">
                <div class="info-label">📅 تاريخ التوريد</div>
                <div class="info-value">{{ $deliveryNote->delivery_date }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">🏭 المستودع</div>
                <div class="info-value">{{ $deliveryNote->warehouse->warehouse_name ?? 'غير محدد' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">📦 المادة</div>
                <div class="info-value">{{ $deliveryNote->material->name_ar ?? 'غير محدد' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">⚖️ الكمية الإجمالية</div>
                <div class="info-value">{{ number_format($deliveryNote->quantity, 3) }} كجم</div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="no-print" style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('manufacturing.delivery-notes.index') }}" class="btn btn-back">
            ← العودة إلى القائمة
        </a>
        <a href="{{ route('manufacturing.coils.transfer-index') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #4caf50 0%, #45a049 100%); color: white; text-decoration: none;">
            🔄 نقل كويلات للإنتاج
        </a>
        <button onclick="printAllBarcodes()" class="btn btn-print-all">
            🖨️ طباعة جميع الباركودات
        </button>
    </div>

    <!-- Statistics -->
    <div class="stats-section no-print">
        <h3 style="margin: 0 0 15px 0; color: #333;">📊 إحصائيات الكويلات</h3>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">{{ $deliveryNote->coils()->count() }}</div>
                <div class="stat-label">إجمالي الكويلات</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($deliveryNote->coils()->sum('coil_weight'), 3) }}</div>
                <div class="stat-label">إجمالي الوزن (كجم)</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $deliveryNote->coils()->where('status', 'available')->count() }}</div>
                <div class="stat-label">كويلات متاحة</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($deliveryNote->coils()->sum('remaining_weight'), 3) }}</div>
                <div class="stat-label">الوزن المتبقي (كجم)</div>
            </div>
        </div>
    </div>

    <!-- Coils List -->
    <div class="coils-grid">
        @forelse($coils as $index => $coil)
            <div class="coil-card" id="coil-{{ $coil->id }}">
                <div class="coil-number">#{{ ($coils->currentPage() - 1) * $coils->perPage() + $index + 1 }}</div>
                
                <div class="coil-detail">
                    <div class="detail-label">🔢 رقم الكويل</div>
                    <div class="detail-value">{{ $coil->coil_number }}</div>
                </div>

                <div class="coil-detail">
                    <div class="detail-label">⚖️ الوزن الأصلي</div>
                    <div class="detail-value">{{ number_format($coil->coil_weight, 3) }} كجم</div>
                </div>

                <div class="coil-detail">
                    <div class="detail-label">📊 الوزن المتبقي</div>
                    <div class="detail-value" style="color: #2e7d32;">{{ number_format($coil->remaining_weight, 3) }} كجم</div>
                </div>

                <div class="coil-detail">
                    <div class="detail-label">📟 الباركود</div>
                    <div style="position: relative;">
                        <div class="barcode-loading" style="position: absolute; top: 0; left: 0;"></div>
                        <svg class="barcode" data-barcode="{{ $coil->coil_barcode }}" style="display: none;"></svg>
                    </div>
                    <div style="font-size: 10px; text-align: center; margin-top: 5px; font-family: monospace;">{{ $coil->coil_barcode }}</div>
                </div>

                <div class="coil-detail">
                    <div class="detail-label">📌 الحالة</div>
                    <div>
                        @if($coil->status === 'available')
                            <span class="status-badge status-available">✓ متاح</span>
                        @elseif($coil->status === 'partially_used')
                            <span class="status-badge status-partial">⚡ مستخدم جزئياً</span>
                        @else
                            <span class="status-badge status-used">✗ مستخدم بالكامل</span>
                        @endif
                    </div>
                </div>

                <button onclick="printCoilBarcode({{ $coil->id }})" class="btn btn-print no-print">
                    🖨️ طباعة
                </button>
            </div>
        @empty
            <div style="text-align: center; padding: 60px; background: white; border-radius: 12px;">
                <div style="font-size: 64px; margin-bottom: 20px;">📦</div>
                <h3 style="color: #999; margin: 0;">لا توجد كويلات مسجلة لهذه الشحنة</h3>
            </div>
        @endforelse
    </div>

    <!-- Pagination Links -->
    @if($coils->hasPages())
        <div class="no-print" style="margin-top: 30px; display: flex; justify-content: center;">
            {{ $coils->links() }}
        </div>
    @endif
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
    const coilCard = document.getElementById('coil-' + coilId);
    const coilNumber = coilCard.querySelector('.detail-value').textContent;
    const weight = coilCard.querySelectorAll('.detail-value')[1].textContent;
    const barcodeSvg = coilCard.querySelector('.barcode');
    const barcodeText = barcodeSvg.getAttribute('data-barcode');

    const printWindow = window.open('', '_blank', 'width=400,height=300');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>طباعة باركود - ${coilNumber}</title>
            <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    padding: 20px;
                }
                .barcode-container {
                    text-align: center;
                    border: 2px solid #000;
                    padding: 20px;
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
            <div class="barcode-container">
                <h3>🏭 مصنع الحديد</h3>
                <div class="info"><strong>رقم الكويل:</strong> ${coilNumber}</div>
                <div class="info"><strong>الوزن:</strong> ${weight}</div>
                <svg id="printBarcode"></svg>
                <div class="info" style="font-size: 11px; color: #666;">تاريخ الطباعة: ${new Date().toLocaleString('ar-EG')}</div>
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
