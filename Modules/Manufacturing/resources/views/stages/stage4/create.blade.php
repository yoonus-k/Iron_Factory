@extends('master')

@section('title', 'تعبئة الكراتين - المرحلة الرابعة')

@section('content')
<style>
    /* Stage Container */
    .stage-container {
        max-width: 1100px;
        margin: 20px auto;
        padding: 0 15px;
    }

    /* Stage Header */
    .stage-header {
        background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
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
        border-bottom: 2px solid #9b59b6;
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
        border-color: #9b59b6;
        background: white;
        box-shadow: 0 0 0 3px rgba(155, 89, 182, 0.1);
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
        background: linear-gradient(135deg, #f3e5f5 0%, #e8d5ed 100%);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border: 2px dashed #9b59b6;
    }

    .barcode-input-wrapper {
        position: relative;
    }

    .barcode-input {
        width: 100%;
        padding: 15px 50px 15px 15px;
        font-size: 16px;
        border: 2px solid #9b59b6;
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
        color: #9b59b6;
    }

    /* Coil Display */
    .coil-display {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border-right: 4px solid #2196f3;
        display: none;
    }

    .coil-display.active {
        display: block;
        animation: slideIn 0.3s ease-out;
    }

    .coil-display h4 {
        color: #2196f3;
        margin: 0 0 10px 0;
        font-size: 16px;
    }

    .coil-info-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
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

    /* Box List */
    .box-list {
        margin-top: 20px;
    }

    .box-item {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-right: 4px solid #9b59b6;
        animation: slideIn 0.3s ease-out;
    }

    .box-info strong {
        color: var(--dark-color);
        font-size: 15px;
        display: block;
        margin-bottom: 6px;
    }

    .box-info small {
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
        background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
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
        box-shadow: 0 6px 16px rgba(155, 89, 182, 0.3);
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
        
        .coil-info-grid {
            grid-template-columns: repeat(2, 1fr);
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
            <span>📦</span>
            المرحلة الرابعة - تعبئة الكراتين
        </h1>
        <p>قم بمسح باركود الكويل وإضافة معلومات التعبئة والشحن</p>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 style="margin: 0 0 15px 0; color: #9b59b6;">📷 مسح باركود الكويل</h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="coilBarcode" class="barcode-input" placeholder="امسح أو اكتب باركود الكويل (CO3-XXX-2025)" autofocus>
            <span class="barcode-icon">🎯</span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 10px;">💡 امسح الباركود أو اضغط Enter للبحث</small>
    </div>

    <!-- Coil Display -->
    <div id="coilDisplay" class="coil-display">
        <h4>✅ بيانات الكويل</h4>
        <div class="coil-info-grid">
            <div class="info-item">
                <div class="info-label">الباركود</div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">لون الصبغة</div>
                <div class="info-value" id="displayDyeColor">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">نوع البلاستيك</div>
                <div class="info-value" id="displayPlasticType">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">الوزن الإجمالي</div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
        </div>
    </div>

    <!-- Scanned Coils List -->
    <div class="form-section">
        <h3 class="section-title">🎯 الكويلات الممسوحة للكرتون الحالي</h3>
        
        <div class="info-box">
            <strong>💡 كيف تعمل العملية:</strong>
            <ul>
                <li>امسح باركود كل كويل على حدة</li>
                <li>سيتم إضافة الكويل تلقائياً مع وزنه الفعلي</li>
                <li>الوزن الإجمالي يُحسب من مجموع جميع الكويلات</li>
            </ul>
        </div>

        <div id="scannedCoilsList" class="coil-list" style="min-height: 150px; background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <div class="empty-state" style="padding: 30px;">
                <p style="margin: 0; color: #7f8c8d;">لم يتم مسح أي كويلات بعد</p>
            </div>
        </div>

        <div class="form-row" style="background: linear-gradient(135deg, #e8f4f8 0%, #d4ebf5 100%); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <div class="info-item" style="text-align: center;">
                <div class="info-label" style="font-size: 13px; color: #7f8c8d;">عدد الكويلات</div>
                <div class="info-value" style="font-size: 24px; font-weight: 700; color: #2196f3;" id="scannedCoilsCount">0</div>
            </div>
            <div class="info-item" style="text-align: center;">
                <div class="info-label" style="font-size: 13px; color: #7f8c8d;">الوزن المتوقع</div>
                <div class="info-value" style="font-size: 24px; font-weight: 700; color: #27ae60;" id="expectedWeightDisplay">0 كجم</div>
            </div>
        </div>
    </div>

    <!-- Box Form -->
    <div class="form-section">
        <h3 class="section-title">📝 بيانات الكرتون</h3>

        <div class="form-row">
            <div class="form-group">
                <label>رقم الكرتون <span class="required">*</span></label>
                <input type="text" id="boxNumber" class="form-control" placeholder="BOX4-001-2025">
            </div>

            <div class="form-group">
                <label>الوزن المتوقع (كجم)</label>
                <input type="number" id="expectedWeight" class="form-control" placeholder="0.00" step="0.01" readonly style="background: #e8f4f8; font-weight: 600;">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>الوزن الفعلي بعد التعبئة (كجم) <span class="required">*</span></label>
                <input type="number" id="actualWeight" class="form-control" placeholder="0.00" step="0.01">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">💡 الوزن الفعلي بعد التعبئة والتغليف</small>
            </div>

            <div class="form-group">
                <label>كمية الهدر (كجم)</label>
                <input type="number" id="wasteAmount" class="form-control" readonly style="background: #fff3cd;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">نسبة الهدر: <span id="wastePercentDisplay">0%</span></small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>نوع التغليف <span class="required">*</span></label>
                <select id="packagingType" class="form-select">
                    <option value="">اختر نوع التغليف</option>
                    <option value="carton">كرتون عادي</option>
                    <option value="reinforced_carton">كرتون مقوى</option>
                    <option value="wooden">صندوق خشبي</option>
                    <option value="plastic">غلاف بلاستيكي</option>
                </select>
            </div>

            <div class="form-group">
                <label>التكلفة (ريال) <span class="required">*</span></label>
                <input type="number" id="cost" class="form-control" placeholder="0.00" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>عنوان الشحن</label>
                <textarea id="shippingAddress" class="form-control" placeholder="المدينة، الحي، الشارع... (اختياري)"></textarea>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>ملاحظات</label>
                <textarea id="notes" class="form-control" placeholder="أضف أي ملاحظات إضافية..."></textarea>
            </div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-primary" onclick="addBox()">
                ➕ إضافة الكرتون
            </button>
            <button type="button" class="btn-secondary" onclick="clearForm()">
                🔄 مسح النموذج
            </button>
        </div>
    </div>

    <!-- Boxes List -->
    <div class="form-section">
        <h3 class="section-title">📋 قائمة الكراتين المضافة (<span id="boxCount">0</span>)</h3>
        <div id="boxList" class="box-list">
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>لا توجد كراتين مضافة بعد</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="form-actions">
        <button type="button" class="btn-success" onclick="submitAll()" id="submitBtn" disabled>
            ✅ حفظ جميع الكراتين
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('manufacturing.stage4.index') }}'">
            ❌ إلغاء
        </button>
    </div>
</div>

<script>
let scannedCoils = []; // الكويلات الممسوحة للكرتون الحالي
let boxes = [];

// Load from localStorage on page load
document.addEventListener('DOMContentLoaded', function() {
    const saved = localStorage.getItem('stage4_boxes');
    if (saved) {
        boxes = JSON.parse(saved);
        renderBoxes();
    }
    
    // Auto-save every 30 seconds
    setInterval(saveOffline, 30000);
});

// Barcode scanner
document.getElementById('coilBarcode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const barcode = this.value.trim();
        if (barcode) {
            loadCoil(barcode);
            this.value = ''; // Clear for next scan
        }
    }
});

// Auto-calculate waste when actual weight changes
document.getElementById('actualWeight').addEventListener('input', calculateWaste);

function loadCoil(barcode) {
    // Check if already scanned
    if (scannedCoils.some(c => c.barcode === barcode)) {
        showToast('⚠️ هذا الكويل تم مسحه مسبقاً!', 'error');
        return;
    }

    // Simulate API call - replace with actual AJAX
    // fetch(`/api/stage3/get-by-barcode/${barcode}`)
    //     .then(response => response.json())
    //     .then(data => { addCoilToBox(data); })

    // Mock data for demonstration
    const coilData = {
        id: Date.now(),
        barcode: barcode,
        dye_color: ['red', 'blue', 'green', 'yellow'][Math.floor(Math.random() * 4)],
        plastic_type: ['pe', 'pp', 'pvc'][Math.floor(Math.random() * 3)],
        total_weight: (90 + Math.random() * 20).toFixed(2) // وزن عشوائي بين 90-110
    };

    addCoilToBox(coilData);
}

function addCoilToBox(coilData) {
    scannedCoils.push(coilData);
    renderScannedCoils();
    updateTotalWeight();
    
    // Display last scanned coil
    const colorNames = {
        red: 'أحمر', blue: 'أزرق', green: 'أخضر', yellow: 'أصفر',
        black: 'أسود', white: 'أبيض', brown: 'بني'
    };
    const plasticNames = {
        pe: 'PE', pp: 'PP', pvc: 'PVC', pet: 'PET'
    };

    document.getElementById('displayBarcode').textContent = coilData.barcode;
    document.getElementById('displayDyeColor').textContent = colorNames[coilData.dye_color] || coilData.dye_color;
    document.getElementById('displayPlasticType').textContent = plasticNames[coilData.plastic_type] || coilData.plastic_type;
    document.getElementById('displayWeight').textContent = coilData.total_weight + ' كجم';
    document.getElementById('coilDisplay').classList.add('active');

    showToast('✅ تم إضافة الكويل: ' + coilData.barcode, 'success');
}

function renderScannedCoils() {
    const list = document.getElementById('scannedCoilsList');
    document.getElementById('scannedCoilsCount').textContent = scannedCoils.length;

    if (scannedCoils.length === 0) {
        list.innerHTML = `
            <div class="empty-state" style="padding: 30px;">
                <p style="margin: 0; color: #7f8c8d;">لم يتم مسح أي كويلات بعد</p>
            </div>
        `;
        return;
    }

    const colorNames = {
        red: 'أحمر', blue: 'أزرق', green: 'أخضر', yellow: 'أصفر',
        black: 'أسود', white: 'أبيض', brown: 'بني'
    };

    list.innerHTML = scannedCoils.map((coil, index) => `
        <div style="background: white; padding: 12px; border-radius: 6px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; border-right: 3px solid #9b59b6;">
            <div>
                <strong style="color: #2c3e50; font-size: 14px;">${index + 1}. ${coil.barcode}</strong>
                <small style="display: block; color: #7f8c8d; font-size: 12px; margin-top: 4px;">
                    لون: ${colorNames[coil.dye_color]} | وزن: ${coil.total_weight} كجم
                </small>
            </div>
            <button onclick="removeScannedCoil(${coil.id})" style="background: #e74c3c; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                🗑️ حذف
            </button>
        </div>
    `).join('');
}

function removeScannedCoil(id) {
    scannedCoils = scannedCoils.filter(c => c.id !== id);
    renderScannedCoils();
    updateTotalWeight();
    showToast('تم حذف الكويل', 'info');
}

function updateTotalWeight() {
    const totalWeight = scannedCoils.reduce((sum, coil) => sum + parseFloat(coil.total_weight), 0).toFixed(2);
    document.getElementById('expectedWeight').value = totalWeight;
    document.getElementById('expectedWeightDisplay').textContent = totalWeight + ' كجم';
    
    // Recalculate waste if actual weight is entered
    calculateWaste();
}

function calculateWaste() {
    const expectedWeight = parseFloat(document.getElementById('expectedWeight').value) || 0;
    const actualWeight = parseFloat(document.getElementById('actualWeight').value) || 0;
    
    if (expectedWeight > 0 && actualWeight > 0) {
        if (actualWeight > expectedWeight) {
            showToast('⚠️ تحذير: الوزن الفعلي أكبر من المتوقع!', 'error');
        }
        
        const wasteAmount = (expectedWeight - actualWeight).toFixed(2);
        const wastePercent = ((expectedWeight - actualWeight) / expectedWeight * 100).toFixed(2);
        document.getElementById('wasteAmount').value = wasteAmount;
        document.getElementById('wastePercentDisplay').textContent = wastePercent + '%';
        
        // Change color based on waste percentage
        const wasteInput = document.getElementById('wasteAmount');
        if (parseFloat(wastePercent) > 5) {
            wasteInput.style.background = '#ffeaa7'; // Yellow warning
            wasteInput.style.color = '#d63031';
            wasteInput.style.fontWeight = 'bold';
        } else {
            wasteInput.style.background = '#fff3cd';
            wasteInput.style.color = '#856404';
            wasteInput.style.fontWeight = 'normal';
        }
    } else {
        document.getElementById('wasteAmount').value = '0';
        document.getElementById('wastePercentDisplay').textContent = '0%';
    }
}

function addBox() {
    if (scannedCoils.length === 0) {
        alert('⚠️ يرجى مسح كويل واحد على الأقل أولاً!');
        return;
    }

    const boxNumber = document.getElementById('boxNumber').value.trim();
    const packagingType = document.getElementById('packagingType').value;
    const expectedWeight = document.getElementById('expectedWeight').value;
    const actualWeight = document.getElementById('actualWeight').value;
    const wasteAmount = document.getElementById('wasteAmount').value || 0;
    const shippingAddress = document.getElementById('shippingAddress').value.trim();
    const cost = document.getElementById('cost').value;
    const notes = document.getElementById('notes').value.trim();

    if (!boxNumber || !packagingType || !actualWeight || !cost) {
        alert('⚠️ يرجى ملء جميع الحقول المطلوبة!');
        return;
    }

    const wastePercentage = parseFloat(expectedWeight) > 0 ? 
        ((parseFloat(expectedWeight) - parseFloat(actualWeight)) / parseFloat(expectedWeight) * 100).toFixed(2) : 0;

    const box = {
        id: Date.now(),
        box_number: boxNumber,
        coils: [...scannedCoils], // نسخة من الكويلات الممسوحة
        coils_count: scannedCoils.length,
        packaging_type: packagingType,
        expected_weight: parseFloat(expectedWeight),
        actual_weight: parseFloat(actualWeight),
        waste_amount: parseFloat(wasteAmount),
        waste_percentage: parseFloat(wastePercentage),
        shipping_address: shippingAddress,
        cost: parseFloat(cost),
        notes: notes
    };

    boxes.push(box);
    renderBoxes();
    clearForm();
    saveOffline();

    showToast('✅ تم إضافة الكرتون بنجاح! (' + scannedCoils.length + ' كويلات)', 'success');
}

function renderBoxes() {
    const list = document.getElementById('boxList');
    document.getElementById('boxCount').textContent = boxes.length;
    document.getElementById('submitBtn').disabled = boxes.length === 0;

    if (boxes.length === 0) {
        list.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>لا توجد كراتين مضافة بعد</p>
            </div>
        `;
        return;
    }

    const packagingNames = {
        carton: 'كرتون عادي',
        reinforced_carton: 'كرتون مقوى',
        wooden: 'صندوق خشبي',
        plastic: 'غلاف بلاستيكي'
    };

    list.innerHTML = boxes.map(box => {
        const coilsList = box.coils.map(c => c.barcode).join(', ');
        const wasteColor = parseFloat(box.waste_percentage) > 5 ? '#e74c3c' : '#f39c12';
        return `
        <div class="box-item">
            <div class="box-info">
                <strong>📦 ${box.box_number}</strong>
                <small>
                    كويلات: ${box.coils_count} | 
                    متوقع: ${box.expected_weight} كجم | 
                    فعلي: ${box.actual_weight} كجم | 
                    <span style="color: ${wasteColor}; font-weight: bold;">هدر: ${box.waste_amount} كجم (${box.waste_percentage}%)</span>
                    <br>تغليف: ${packagingNames[box.packaging_type]} | 
                    تكلفة: ${box.cost} ريال
                    <br>🎯 الكويلات: ${coilsList}
                    ${box.shipping_address ? '<br>📍 عنوان: ' + box.shipping_address : ''}
                    ${box.notes ? '<br>📝 ' + box.notes : ''}
                </small>
            </div>
            <button class="btn-delete" onclick="removeBox(${box.id})">🗑️ حذف</button>
        </div>
    `}).join('');
}

function removeBox(id) {
    if (confirm('هل أنت متأكد من حذف هذا الكرتون؟')) {
        boxes = boxes.filter(b => b.id !== id);
        renderBoxes();
        saveOffline();
        showToast('🗑️ تم حذف الكرتون', 'info');
    }
}

function clearForm() {
    // Clear scanned coils
    scannedCoils = [];
    renderScannedCoils();
    updateTotalWeight();
    
    // Clear form fields
    document.getElementById('boxNumber').value = '';
    document.getElementById('actualWeight').value = '';
    document.getElementById('wasteAmount').value = '';
    document.getElementById('wastePercentDisplay').textContent = '0%';
    document.getElementById('packagingType').value = '';
    document.getElementById('shippingAddress').value = '';
    document.getElementById('cost').value = '';
    document.getElementById('notes').value = '';
    
    // Clear coil display
    document.getElementById('coilDisplay').classList.remove('active');
    
    // Focus on barcode scanner
    document.getElementById('coilBarcode').focus();
}

function submitAll() {
    if (boxes.length === 0) {
        alert('⚠️ يرجى إضافة كرتون واحد على الأقل!');
        return;
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ جاري الحفظ...';

    // Prepare data
    const formData = {
        boxes: boxes,
        _token: '{{ csrf_token() }}'
    };

    // Submit via AJAX
    fetch('{{ route("manufacturing.stage4.store") }}', {
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
            showToast('✅ تم حفظ جميع الكراتين بنجاح!', 'success');
            localStorage.removeItem('stage4_boxes');
            setTimeout(() => {
                window.location.href = '{{ route("manufacturing.stage4.index") }}';
            }, 1500);
        } else {
            throw new Error(data.message || 'حدث خطأ أثناء الحفظ');
        }
    })
    .catch(error => {
        alert('❌ خطأ: ' + error.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '✅ حفظ جميع الكراتين';
    });
}

function saveOffline() {
    localStorage.setItem('stage4_boxes', JSON.stringify(boxes));
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#9b59b6'};
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