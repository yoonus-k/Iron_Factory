@extends('master')

@section('title', 'إنشاء لفائف - المرحلة الثالثة')

@section('content')

<style>
    .info-tooltip {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        background: #3498db;
        color: white;
        border-radius: 50%;
        font-size: 12px;
        font-weight: bold;
        cursor: help;
        margin-right: 5px;
        vertical-align: middle;
    }

    .info-tooltip:hover {
        background: #2980b9;
    }

    .info-tooltip .tooltip-text {
        visibility: hidden;
        width: 250px;
        background-color: #2c3e50;
        color: #fff;
        text-align: right;
        border-radius: 6px;
        padding: 12px;
        position: absolute;
        z-index: 1000;
        bottom: 125%;
        right: 50%;
        margin-right: -125px;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 13px;
        line-height: 1.6;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .info-tooltip .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        right: 50%;
        margin-right: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #2c3e50 transparent transparent transparent;
    }

    .info-tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    .info-box-header {
        cursor: help;
        display: inline-block;
    }

    /* Stage Container */
    .stage-container {
        max-width: 1100px;
        margin: 20px auto;
        padding: 0 15px;
    }

    /* Stage Header */
    .stage-header {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        padding: 25px 30px;
        border-radius: var(--border-radius);
        margin-bottom: 25px;
        box-shadow: var(--shadow-medium);
    }

    .stage-header h1 {
        margin: 0 0 8px 0;
        font-size: 26px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stage-header p {
        margin: 0;
        opacity: 0.95;
        font-size: 14px;
    }

    /* Form Section */
    .form-section {
        background: white;
        padding: 25px;
        border-radius: var(--border-radius);
        margin-bottom: 20px;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-light);
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--dark-color);
        margin: 0 0 20px 0;
        padding-bottom: 12px;
        border-bottom: 2px solid #3498db;
    }

    /* Form Layout */
    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 500;
        color: #34495e;
        margin-bottom: 8px;
    }

    .required {
        color: var(--danger-color);
        margin-right: 4px;
    }

    .form-control, .form-select {
        padding: 12px 15px;
        border: 1px solid #dce4ec;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
        background: #f8fafb;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #3498db;
        background: white;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .form-control:disabled, .form-control:read-only {
        background: #ecf0f1;
        cursor: not-allowed;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    /* Barcode Section */
    .barcode-section {
        background: linear-gradient(135deg, #e8f4f8 0%, #d4ebf5 100%);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border: 2px dashed #3498db;
    }

    .barcode-input-wrapper {
        position: relative;
    }

    .barcode-input {
        width: 100%;
        padding: 15px 50px 15px 15px;
        font-size: 16px;
        border: 2px solid #3498db;
        border-radius: 8px;
        font-weight: 500;
        background: white;
    }

    .barcode-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 24px;
        color: #3498db;
    }

    /* Processed Display */
    .processed-display {
        background: linear-gradient(135deg, #e8f8f5 0%, #d5f4e6 100%);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border-right: 4px solid var(--success-color);
        display: none;
    }

    .processed-display.active {
        display: block;
        animation: slideIn 0.3s ease-out;
    }

    .processed-display h4 {
        color: var(--success-color);
        margin: 0 0 10px 0;
        font-size: 16px;
    }

    .processed-info {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-top: 10px;
    }

    .info-item {
        background: rgba(255, 255, 255, 0.7);
        padding: 10px;
        border-radius: 6px;
    }

    .info-label {
        font-size: 12px;
        color: #7f8c8d;
        margin-bottom: 4px;
    }

    .info-value {
        font-size: 15px;
        font-weight: 600;
        color: var(--dark-color);
    }

    /* Coil List */
    .coil-list {
        margin-top: 20px;
    }

    .coil-item {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-right: 4px solid #3498db;
        animation: slideIn 0.3s ease-out;
    }

    .coil-info strong {
        color: var(--dark-color);
        font-size: 15px;
        display: block;
        margin-bottom: 6px;
    }

    .coil-info small {
        color: #7f8c8d;
        font-size: 13px;
        line-height: 1.6;
    }

    .btn-delete {
        background: var(--danger-color);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.3s;
    }

    .btn-delete:hover {
        background: #c0392b;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
    }

    /* Buttons */
    .btn-primary {
        background: linear-gradient(135deg, #0066B2 0%, #3A8FC7 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 102, 178, 0.3);
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success-color) 0%, #229954 100%);
        color: white;
        border: none;
        padding: 14px 32px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(39, 174, 96, 0.3);
    }

    .btn-success:disabled {
        background: #95a5a6;
        cursor: not-allowed;
        transform: none;
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
    }

    /* Actions */
    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 2px solid #ecf0f1;
        justify-content: center;
    }

    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 15px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #95a5a6;
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    /* Info Box */
    .info-box {
        background: linear-gradient(135deg, #fff9e6 0%, #ffeaa7 100%);
        border-right: 4px solid #f39c12;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .info-box strong {
        color: #e67e22;
        display: block;
        margin-bottom: 8px;
    }

    .info-box ul {
        margin: 8px 0 0 20px;
        color: #7f8c8d;
        font-size: 13px;
    }

    /* Animations */
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

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .processed-info {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }
    }
</style>

<div class="stage-container">
    <!-- Header -->
    <div class="stage-header">
        <h1>
            <span>🎯</span>
            المرحلة الثالثة - إنشاء الكويلات
        </h1>
        <p>قم بمسح باركود الاستاند المعالج وإضافة الصبغة والبلاستيك لإنشاء كويل جديد</p>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 style="margin: 0 0 15px 0; color: #3498db;"><i class="fas fa-camera"></i> مسح باركود الاستاند المعالج <span class="info-tooltip">?<span class="tooltip-text">مسح باركود المنتج المعالج من المرحلة الثانية</span></span></h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="processedBarcode" class="barcode-input" placeholder="امسح أو اكتب باركود الاستاند المعالج (ST2-XXX-2025)" autofocus>
            <span class="barcode-icon">📦</span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 10px;"><i class="fas fa-lightbulb"></i> <span class="info-tooltip">?<span class="tooltip-text">امسح الباركود أو اضغط Enter للبحث</span></span></small>
    </div>

    <!-- Processed Display -->
    <div id="processedDisplay" class="processed-display">
        <h4><i class="fas fa-circle-check"></i> بيانات الاستاند المعالج</h4>
        <div class="processed-info">
            <div class="info-item">
                <div class="info-label">الباركود <span class="info-tooltip">?<span class="tooltip-text">الرمز الشريطي الفريد للاستند المعالج</span></span></div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">نوع المعالجة <span class="info-tooltip">?<span class="tooltip-text">نوع العملية التي تمت على الاستاند</span></span></div>
                <div class="info-value" id="displayProcessType">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">الوزن النهائي <span class="info-tooltip">?<span class="tooltip-text">الوزن بعد تطبيق المعالجة</span></span></div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
        </div>
    </div>

    <!-- Coil Form -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-edit"></i> بيانات الكويل</h3>

        <div class="info-box">
            <div class="info-box-header">
                <strong><i class="fas fa-thumbtack"></i> ملاحظة هامة: <span class="info-tooltip">?<span class="tooltip-text"><strong>معادلة حساب الكويل:</strong><br><br>• المعادلة: الوزن الإجمالي = وزن الأساس + وزن الصبغة + وزن البلاستيك<br><br>• كمية الهدر = الوزن الإجمالي المتوقع - الوزن الإجمالي الفعلي<br><br>• نسبة الهدر الافتراضية: 5%</span></span></strong>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>رقم الكويل <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">الرقم التعريفي للكويل</span></span></label>
                <input type="text" id="coilNumber" class="form-control" placeholder="CO3-001-2025">
            </div>

            <div class="form-group">
                <label>وزن الأساس (كجم) <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">الوزن الأساسي للاستند المعالج</span></span></label>
                <input type="number" id="baseWeight" class="form-control" placeholder="95.00" step="0.01" readonly>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>لون الصبغة <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">لون الصبغة التي تمت إضافتها</span></span></label>
                <select id="dyeColor" class="form-control">
                    <option value="">اختر اللون</option>
                    <option value="red">أحمر</option>
                    <option value="blue">أزرق</option>
                    <option value="green">أخضر</option>
                    <option value="yellow">أصفر</option>
                    <option value="black">أسود</option>
                    <option value="white">أبيض</option>
                    <option value="brown">بني</option>
                </select>
            </div>

            <div class="form-group">
                <label>وزن الصبغة (كجم) <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">الوزن الإضافي للصبغة</span></span></label>
                <input type="number" id="dyeWeight" class="form-control" placeholder="2.00" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>نوع البلاستيك <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">نوع البلاستيك المضاف</span></span></label>
                <select id="plasticType" class="form-control">
                    <option value="">اختر النوع</option>
                    <option value="pe">بولي إيثيلين (PE)</option>
                    <option value="pp">بولي بروبيلين (PP)</option>
                    <option value="pvc">بولي فينيل كلورايد (PVC)</option>
                    <option value="pet">بولي إيثيلين تيرفثالات (PET)</option>
                </select>
            </div>

            <div class="form-group">
                <label>وزن البلاستيك (كجم) <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">الوزن الإضافي للبلاستيك</span></span></label>
                <input type="number" id="plasticWeight" class="form-control" placeholder="3.00" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>الوزن الإجمالي المتوقع (كجم) <span class="info-tooltip">?<span class="tooltip-text">الوزن المحسوب تلقائياً من مكونات الكويل</span></span></label>
                <input type="number" id="expectedWeight" class="form-control" readonly style="background: #e8f4f8; font-weight: 600;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-calculator"></i> <span class="info-tooltip">?<span class="tooltip-text">يتم الحساب تلقائياً: الوزن الأساسي + وزن الصبغة + وزن البلاستيك</span></span></small>
            </div>

            <div class="form-group">
                <label>الوزن الإجمالي الفعلي (كجم) <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">الوزن الحقيقي للكويل بعد الإنتاج</span></span></label>
                <input type="number" id="totalWeight" class="form-control" placeholder="100.00" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>كمية الهدر (كجم) <span class="info-tooltip">?<span class="tooltip-text">الفرق بين الوزن المتوقع والفعلي</span></span></label>
                <input type="number" id="wasteAmount" class="form-control" readonly>
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-percent"></i> نسبة الهدر: <span id="wastePercentDisplay">0%</span> <span class="info-tooltip">?<span class="tooltip-text">النسبة المئوية للهدر من الوزن المتوقع</span></span></small>
            </div>

            <div class="form-group">
                <label>التكلفة (ريال) <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">التكلفة الإجمالية للكويل</span></span></label>
                <input type="number" id="cost" class="form-control" placeholder="500.00" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>ملاحظات <span class="info-tooltip">?<span class="tooltip-text">ملاحظات إضافية حول الكويل</span></span></label>
                <textarea id="notes" class="form-control" placeholder="أضف أي ملاحظات إضافية..."></textarea>
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-sticky-note"></i> <span class="info-tooltip">?<span class="tooltip-text">يمكنك إضافة أي ملاحظات إضافية هنا</span></span></small>
            </div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-primary" onclick="addCoil()">
                <i class="fas fa-plus"></i> إضافة الكويل
            </button>
            <button type="button" class="btn-secondary" onclick="clearForm()">
                <i class="fas fa-sync"></i> مسح النموذج
            </button>
        </div>
    </div>

    <!-- Coils List -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-list"></i> قائمة الكويلات المضافة (<span id="coilCount">0</span>)</h3>
        <div id="coilList" class="coil-list">
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>لا توجد كويلات مضافة بعد</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="form-actions">
        <button type="button" class="btn-success" onclick="submitAll()" id="submitBtn" disabled>
            <i class="fas fa-check"></i> حفظ جميع الكويلات
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('manufacturing.stage3.index') }}'">
            <i class="fas fa-times"></i> إلغاء
        </button>
    </div>
</div>

<script>
let currentProcessed = null;
let coils = [];

// Load from localStorage on page load
document.addEventListener('DOMContentLoaded', function() {
    const saved = localStorage.getItem('stage3_coils');
    if (saved) {
        coils = JSON.parse(saved);
        renderCoils();
    }

    // Auto-save every 30 seconds
    setInterval(saveOffline, 30000);
});

// Barcode scanner
document.getElementById('processedBarcode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        loadProcessed(this.value.trim());
    }
});

// Auto-calculate expected weight
['baseWeight', 'dyeWeight', 'plasticWeight'].forEach(id => {
    document.getElementById(id).addEventListener('input', calculateExpectedWeight);
});

// Auto-calculate waste
['expectedWeight', 'totalWeight'].forEach(id => {
    document.getElementById(id).addEventListener('input', calculateWaste);
});

function loadProcessed(barcode) {
    if (!barcode) {
        alert('⚠️ يرجى إدخال باركود الاستاند المعالج!');
        return;
    }

    // Simulate API call - replace with actual AJAX
    // fetch(`/api/stage2/get-by-barcode/${barcode}`)
    //     .then(response => response.json())
    //     .then(data => { ... })

    // Mock data for demonstration
    currentProcessed = {
        barcode: barcode,
        process_type: 'heating',
        output_weight: 95.5
    };

    // Display processed data
    document.getElementById('displayBarcode').textContent = currentProcessed.barcode;
    document.getElementById('displayProcessType').textContent = getProcessTypeName(currentProcessed.process_type);
    document.getElementById('displayWeight').textContent = currentProcessed.output_weight + ' كجم';
    document.getElementById('processedDisplay').classList.add('active');

    // Fill base weight
    document.getElementById('baseWeight').value = currentProcessed.output_weight;
    calculateExpectedWeight();

    // Focus on coil number
    document.getElementById('coilNumber').focus();

    // Show success message
    showToast('✅ تم تحميل بيانات الاستاند المعالج بنجاح!', 'success');
}

function getProcessTypeName(type) {
    const types = {
        heating: 'تسخين',
        cooling: 'تبريد',
        cutting: 'قطع',
        rolling: 'لف',
        shaping: 'تشكيل',
        polishing: 'صقل'
    };
    return types[type] || type;
}

function calculateExpectedWeight() {
    const base = parseFloat(document.getElementById('baseWeight').value) || 0;
    const dye = parseFloat(document.getElementById('dyeWeight').value) || 0;
    const plastic = parseFloat(document.getElementById('plasticWeight').value) || 0;

    const expected = base + dye + plastic;
    document.getElementById('expectedWeight').value = expected.toFixed(2);

    calculateWaste();
}

function calculateWaste() {
    const expected = parseFloat(document.getElementById('expectedWeight').value) || 0;
    const actual = parseFloat(document.getElementById('totalWeight').value) || 0;

    if (expected > 0 && actual > 0) {
        const wasteAmount = (expected - actual).toFixed(2);
        const wastePercent = ((expected - actual) / expected * 100).toFixed(2);
        document.getElementById('wasteAmount').value = wasteAmount;
        document.getElementById('wastePercentDisplay').textContent = wastePercent + '%';
    } else {
        document.getElementById('wasteAmount').value = '0';
        document.getElementById('wastePercentDisplay').textContent = '0%';
    }
}

function addCoil() {
    if (!currentProcessed) {
        alert('⚠️ يرجى مسح باركود الاستاند المعالج أولاً!');
        return;
    }

    const coilNumber = document.getElementById('coilNumber').value.trim();
    const baseWeight = document.getElementById('baseWeight').value;
    const dyeColor = document.getElementById('dyeColor').value;
    const dyeWeight = document.getElementById('dyeWeight').value;
    const plasticType = document.getElementById('plasticType').value;
    const plasticWeight = document.getElementById('plasticWeight').value;
    const totalWeight = document.getElementById('totalWeight').value;
    const wasteAmount = document.getElementById('wasteAmount').value || 0;
    const cost = document.getElementById('cost').value;
    const notes = document.getElementById('notes').value.trim();

    if (!coilNumber || !dyeColor || !dyeWeight || !plasticType || !plasticWeight || !totalWeight || !cost) {
        alert('⚠️ يرجى ملء جميع الحقول المطلوبة!');
        return;
    }

    const expectedWeight = parseFloat(document.getElementById('expectedWeight').value) || 0;
    const wastePercentage = expectedWeight > 0 ?
        ((expectedWeight - parseFloat(totalWeight)) / expectedWeight * 100).toFixed(2) : 0;

    const coil = {
        id: Date.now(),
        processed_barcode: currentProcessed.barcode,
        coil_number: coilNumber,
        base_weight: parseFloat(baseWeight),
        dye_color: dyeColor,
        dye_weight: parseFloat(dyeWeight),
        plastic_type: plasticType,
        plastic_weight: parseFloat(plasticWeight),
        expected_weight: parseFloat(expectedWeight),
        total_weight: parseFloat(totalWeight),
        waste_amount: parseFloat(wasteAmount),
        waste_percentage: parseFloat(wastePercentage),
        cost: parseFloat(cost),
        notes: notes
    };

    coils.push(coil);
    renderCoils();
    clearForm();
    saveOffline();

    showToast('تم إضافة الكويل بنجاح!', 'success');
}

function renderCoils() {
    const list = document.getElementById('coilList');
    document.getElementById('coilCount').textContent = coils.length;
    document.getElementById('submitBtn').disabled = coils.length === 0;

    if (coils.length === 0) {
        list.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>لا توجد كويلات مضافة بعد</p>
            </div>
        `;
        return;
    }

    const dyeColorNames = {
        red: 'أحمر', blue: 'أزرق', green: 'أخضر', yellow: 'أصفر',
        black: 'أسود', white: 'أبيض', brown: 'بني'
    };

    const plasticTypeNames = {
        pe: 'PE', pp: 'PP', pvc: 'PVC', pet: 'PET'
    };

    list.innerHTML = coils.map(coil => `
        <div class="coil-item">
            <div class="coil-info">
                <strong>🎯 ${coil.coil_number}</strong>
                <small>
                    صبغة: ${dyeColorNames[coil.dye_color]} (${coil.dye_weight} كجم) |
                    بلاستيك: ${plasticTypeNames[coil.plastic_type]} (${coil.plastic_weight} كجم) |
                    إجمالي: ${coil.total_weight} كجم |
                    هدر: ${coil.waste_amount} كجم (${coil.waste_percentage}%) |
                    تكلفة: ${coil.cost} ريال
                    ${coil.notes ? '<br>📝 ' + coil.notes : ''}
                </small>
            </div>
            <button class="btn-delete" onclick="removeCoil(${coil.id})"><i class="fas fa-trash"></i> حذف</button>
        </div>
    `).join('');
}

function removeCoil(id) {
    if (confirm('هل أنت متأكد من حذف هذا الكويل؟')) {
        coils = coils.filter(c => c.id !== id);
        renderCoils();
        saveOffline();
        showToast('🗑️ تم حذف الكويل', 'info');
    }
}

function clearForm() {
    // Keep current processed data
    document.getElementById('coilNumber').value = '';
    document.getElementById('dyeColor').value = '';
    document.getElementById('dyeWeight').value = '';
    document.getElementById('plasticType').value = '';
    document.getElementById('plasticWeight').value = '';
    document.getElementById('expectedWeight').value = '';
    document.getElementById('totalWeight').value = '';
    document.getElementById('wasteAmount').value = '';
    document.getElementById('wastePercentDisplay').textContent = '0%';
    document.getElementById('cost').value = '';
    document.getElementById('notes').value = '';

    // Reset base weight from current processed
    if (currentProcessed) {
        document.getElementById('baseWeight').value = currentProcessed.output_weight;
    }

    document.getElementById('coilNumber').focus();
}

function submitAll() {
    if (coils.length === 0) {
        alert('⚠️ يرجى إضافة كويل واحد على الأقل!');
        return;
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';

    // Prepare data
    const formData = {
        coils: coils,
        _token: '{{ csrf_token() }}'
    };

    // Submit via AJAX
    fetch('{{ route("manufacturing.stage3.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('✅ تم حفظ جميع الكويلات بنجاح!', 'success');
            localStorage.removeItem('stage3_coils');
            setTimeout(() => {
                window.location.href = '{{ route("manufacturing.stage3.index") }}';
            }, 1500);
        } else {
            throw new Error(data.message || 'حدث خطأ أثناء الحفظ');
        }
    })
    .catch(error => {
        alert('❌ خطأ: ' + error.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check"></i> حفظ جميع الكويلات';
    });
}

function saveOffline() {
    localStorage.setItem('stage3_coils', JSON.stringify(coils));
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db'};
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

@endsection
