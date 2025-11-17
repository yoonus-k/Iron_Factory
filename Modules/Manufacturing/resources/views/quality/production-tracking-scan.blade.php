@extends('master')

@section('title', 'تتبع الإنتاج - مسح الباركود')

@section('content')
<style>
    .scan-container {
        max-width: 800px;
        margin: 40px auto;
        padding: 0 15px;
    }

    .scan-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .scan-header h1 {
        font-size: 32px;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .scan-header p {
        font-size: 16px;
        color: #7f8c8d;
    }

    .scanner-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        padding: 40px;
        margin-bottom: 30px;
    }

    .scanner-icon {
        text-align: center;
        margin-bottom: 30px;
    }

    .scanner-icon i {
        font-size: 80px;
        color: #3498db;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }

    .barcode-input-group {
        position: relative;
        margin-bottom: 25px;
    }

    .barcode-input {
        width: 100%;
        padding: 18px 20px;
        font-size: 20px;
        border: 3px solid #3498db;
        border-radius: 10px;
        text-align: center;
        font-weight: 600;
        transition: all 0.3s;
    }

    .barcode-input:focus {
        outline: none;
        border-color: #2980b9;
        box-shadow: 0 0 20px rgba(52, 152, 219, 0.3);
    }

    .barcode-input::placeholder {
        color: #bdc3c7;
        font-weight: 400;
    }

    .scan-button {
        width: 100%;
        padding: 18px;
        font-size: 18px;
        font-weight: 600;
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
    }

    .scan-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
    }

    .scan-button:active {
        transform: translateY(0);
    }

    .info-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .info-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 25px;
        border-radius: 12px;
        text-align: center;
        border: 2px solid #dee2e6;
    }

    .info-card i {
        font-size: 36px;
        margin-bottom: 15px;
        display: block;
    }

    .info-card h6 {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 8px;
    }

    .info-card p {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }

    .alert {
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 25px;
        border: none;
        font-size: 15px;
    }

    .alert-danger {
        background: #fee;
        color: #c0392b;
        border-right: 4px solid #e74c3c;
    }

    .alert-success {
        background: #efe;
        color: #27ae60;
        border-right: 4px solid #2ecc71;
    }

    .recent-scans {
        margin-top: 40px;
    }

    .recent-scans h5 {
        color: #2c3e50;
        margin-bottom: 20px;
        font-size: 20px;
    }

    .recent-item {
        background: white;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.3s;
    }

    .recent-item:hover {
        transform: translateX(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .recent-barcode {
        font-weight: 600;
        color: #3498db;
        font-size: 16px;
    }

    .recent-time {
        color: #95a5a6;
        font-size: 13px;
    }
</style>

<div class="scan-container">
    <!-- Header -->
    <div class="scan-header">
        <h1>🔍 تتبع الإنتاج</h1>
        <p>امسح الباركود للحصول على تقرير تفصيلي عن رحلة المنتج</p>
    </div>

    <!-- Error/Success Messages -->
    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Scanner Card -->
    <div class="scanner-card">
        <div class="scanner-icon">
            <i class="fas fa-barcode"></i>
        </div>

        <form id="barcodeForm" method="POST" action="{{ route('manufacturing.production-tracking.process') }}">
            @csrf
            <div class="barcode-input-group">
                <input
                    type="text"
                    id="barcode"
                    name="barcode"
                    class="barcode-input"
                    placeholder="امسح الباركود أو اكتبه هنا..."
                    autofocus
                    required
                >
            </div>
            <button type="submit" class="scan-button">
                <i class="fas fa-search"></i> تتبع الإنتاج الآن
            </button>
        </form>
    </div>

    <!-- Info Cards -->
    <div class="info-cards">
        <div class="info-card" style="border-color: #3498db;">
            <i class="fas fa-warehouse" style="color: #3498db;"></i>
            <h6>المستودع</h6>
            <p>WH-XXX</p>
        </div>
        <div class="info-card" style="border-color: #1abc9c;">
            <i class="fas fa-cut" style="color: #1abc9c;"></i>
            <h6>المرحلة 1</h6>
            <p>ST1-XXX</p>
        </div>
        <div class="info-card" style="border-color: #9b59b6;">
            <i class="fas fa-cogs" style="color: #9b59b6;"></i>
            <h6>المرحلة 2</h6>
            <p>ST2-XXX</p>
        </div>
        <div class="info-card" style="border-color: #e67e22;">
            <i class="fas fa-brush" style="color: #e67e22;"></i>
            <h6>المرحلة 3</h6>
            <p>CO3-XXX</p>
        </div>
        <div class="info-card" style="border-color: #e74c3c;">
            <i class="fas fa-box" style="color: #e74c3c;"></i>
            <h6>المرحلة 4</h6>
            <p>BOX-XXX</p>
        </div>
    </div>

    <!-- Recent Scans -->
    <div class="recent-scans" id="recentScans" style="display: none;">
        <h5>📋 عمليات البحث الأخيرة</h5>
        <div id="recentScansList"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const barcodeInput = document.getElementById('barcode');
    const barcodeForm = document.getElementById('barcodeForm');
    
    // Focus on input
    barcodeInput.focus();

    // Load recent scans from localStorage
    loadRecentScans();

    // Handle form submission
    barcodeForm.addEventListener('submit', function(e) {
        const barcode = barcodeInput.value.trim();

        if (barcode === '') {
            e.preventDefault();
            alert('⚠️ يرجى إدخال رقم الباركود');
            return;
        }

        // Save to recent scans
        saveRecentScan(barcode);
    });

    // Auto-submit on Enter or barcode scan completion
    barcodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            barcodeForm.submit();
        }
    });
});

function saveRecentScan(barcode) {
    let recent = JSON.parse(localStorage.getItem('recentScans') || '[]');
    
    // Remove duplicate if exists
    recent = recent.filter(item => item.barcode !== barcode);
    
    // Add to beginning
    recent.unshift({
        barcode: barcode,
        timestamp: new Date().toISOString()
    });
    
    // Keep only last 5
    recent = recent.slice(0, 5);
    
    localStorage.setItem('recentScans', JSON.stringify(recent));
}

function loadRecentScans() {
    const recent = JSON.parse(localStorage.getItem('recentScans') || '[]');
    
    if (recent.length === 0) return;
    
    const recentScansDiv = document.getElementById('recentScans');
    const recentScansList = document.getElementById('recentScansList');
    
    recentScansDiv.style.display = 'block';
    
    recentScansList.innerHTML = recent.map(item => {
        const time = timeAgo(new Date(item.timestamp));
        return `
            <div class="recent-item" onclick="searchBarcode('${item.barcode}')">
                <div>
                    <div class="recent-barcode">${item.barcode}</div>
                    <div class="recent-time">${time}</div>
                </div>
                <i class="fas fa-chevron-left" style="color: #bdc3c7;"></i>
            </div>
        `;
    }).join('');
}

function searchBarcode(barcode) {
    document.getElementById('barcode').value = barcode;
    document.getElementById('barcodeForm').submit();
}

function timeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    
    if (seconds < 60) return 'منذ لحظات';
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `منذ ${minutes} دقيقة`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `منذ ${hours} ساعة`;
    const days = Math.floor(hours / 24);
    return `منذ ${days} يوم`;
}
</script>
@endsection
