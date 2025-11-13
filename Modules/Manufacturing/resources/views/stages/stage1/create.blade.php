@extends('master')

@section('title', 'المرحلة الأولى - تقسيم المواد')

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
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
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
        border-bottom: 2px solid #f39c12;
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
        border-color: #f39c12;
        background: white;
        box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
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
        background: linear-gradient(135deg, #fef9e7 0%, #fcf3cf 100%);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border: 2px dashed #f39c12;
    }

    .barcode-input-wrapper {
        position: relative;
    }

    .barcode-input {
        width: 100%;
        padding: 15px 50px 15px 15px;
        font-size: 16px;
        border: 2px solid #f39c12;
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
        color: #f39c12;
    }

    /* Material Display */
    .material-display {
        background: linear-gradient(135deg, #e8f8f5 0%, #d5f4e6 100%);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border-right: 4px solid var(--success-color);
        display: none;
    }

    .material-display.active {
        display: block;
        animation: slideIn 0.3s ease-out;
    }

    .material-display h4 {
        color: var(--success-color);
        margin: 0 0 10px 0;
        font-size: 16px;
    }

    .material-info {
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

    /* Stands List */
    .stands-list {
        margin-top: 20px;
    }

    .stand-item {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-right: 4px solid #f39c12;
        animation: slideIn 0.3s ease-out;
    }

    .stand-info strong {
        color: var(--dark-color);
        font-size: 15px;
        display: block;
        margin-bottom: 6px;
    }

    .stand-info small {
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
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
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
        box-shadow: 0 6px 16px rgba(243, 156, 18, 0.3);
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
        
        .material-info {
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
            <span>🔧</span>
            المرحلة الأولى - تقسيم المواد إلى استاندات
        </h1>
        <p>امسح باركود المادة الخام وأضف بيانات الاستاندات لإنشاء استاند جديد</p>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 style="margin: 0 0 15px 0; color: #f39c12;">📷 مسح باركود المادة الخام</h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="materialBarcode" class="barcode-input" placeholder="امسح أو اكتب باركود المادة الخام (WH-XXX-2025)" autofocus>
            <span class="barcode-icon">🏷️</span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 10px;">💡 امسح الباركود أو اضغط Enter للبحث</small>
    </div>

    <!-- Material Display -->
    <div id="materialDisplay" class="material-display">
        <h4>✅ بيانات المادة الخام</h4>
        <div class="material-info">
            <div class="info-item">
                <div class="info-label">الباركود</div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">نوع المادة</div>
                <div class="info-value" id="displayType">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">الوزن المتبقي</div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
        </div>
    </div>

    <!-- Stand Form -->
    <div class="form-section">
        <h3 class="section-title">📝 بيانات الاستاند الجديد</h3>

        <div class="info-box">
            <strong>📌 ملاحظة هامة:</strong>
            <ul>
                <li>المأخوذ من المخزن = وزن الاستاند النهائي + كمية الهدر</li>
                <li>مثال: 100 كجم من المخزن - 2 كجم هدر = 98 كجم وزن الاستاند</li>
                <li>الهدر الافتراضي: 2% من المأخوذ من المخزن</li>
            </ul>
        </div>

        <!-- Template Selector -->
        <div class="form-row" style="background: linear-gradient(135deg, #e8f6f3 0%, #d0ece7 100%); padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #27ae60;">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label style="color: #27ae60; font-weight: 600;">🎯 قوالب الاستاندات السريعة (اختياري)</label>
                <select id="standTemplate" class="form-control" onchange="loadTemplate()" style="border-color: #27ae60;">
                    <option value="">-- اختر قالب جاهز أو أدخل البيانات يدوياً --</option>
                    <option value="8mm">استاند 8 مم (وزن: 100 كجم)</option>
                    <option value="10mm">استاند 10 مم (وزن: 120 كجم)</option>
                    <option value="12mm">استاند 12 مم (وزن: 150 كجم)</option>
                    <option value="14mm">استاند 14 مم (وزن: 180 كجم)</option>
                    <option value="16mm">استاند 16 مم (وزن: 200 كجم)</option>
                </select>
                <small style="color: #27ae60; display: block; margin-top: 5px;">💡 اختر قالب لملء البيانات تلقائياً (الهدر سيُحسب تلقائياً 2%)</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>رقم الاستاند <span class="required">*</span></label>
                <input type="text" id="standNumber" class="form-control" placeholder="ST1-001-2025">
            </div>

            <div class="form-group">
                <label>مقاس السلك (مم) <span class="required">*</span></label>
                <input type="number" id="wireSize" class="form-control" placeholder="2.5" step="0.1">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>المأخوذ من المخزن (كجم) <span class="required">*</span></label>
                <input type="number" id="rawWeight" class="form-control" placeholder="100.00" step="0.01">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">الكمية الفعلية من المادة الخام</small>
            </div>

            <div class="form-group">
                <label>كمية الهدر (كجم)</label>
                <input type="number" id="wasteAmount" class="form-control" placeholder="2.00" step="0.01">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">نسبة الهدر: <span id="wastePercentDisplay">0%</span></small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>وزن الاستاند النهائي (كجم) <span class="required">*</span></label>
                <input type="number" id="weight" class="form-control" placeholder="98.00" step="0.01" readonly style="background: #e8f4f8; font-weight: 600;">
                <small style="color: #27ae60; display: block; margin-top: 5px;">📊 يُحسب تلقائياً: المأخوذ - الهدر</small>
            </div>

            <div class="form-group">
                <!-- Empty for spacing -->
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>💰 التكلفة (ريال) <span class="required">*</span></label>
                <input type="number" id="cost" class="form-control" placeholder="1500.00" step="0.01">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">تكلفة إنتاج هذا الاستاند</small>
            </div>

            <div class="form-group">
                <label>ملاحظات</label>
                <textarea id="notes" class="form-control" placeholder="ملاحظات اختيارية..."></textarea>
            </div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-primary" onclick="addStand()">
                ➕ إضافة الاستاند
            </button>
            <button type="button" class="btn-secondary" onclick="clearForm()">
                🔄 مسح النموذج
            </button>
        </div>
    </div>

    <!-- Stands List -->
    <div class="form-section">
        <h3 class="section-title">📋 الاستاندات المضافة (<span id="standsCount">0</span>)</h3>
        <div id="standsList" class="stands-list">
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>لا توجد استاندات مضافة بعد</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="form-actions">
        <button type="button" class="btn-success" onclick="submitAll()" id="submitBtn" disabled>
            ✅ حفظ جميع الاستاندات
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('manufacturing.stage1.index') }}'">
            ❌ إلغاء
        </button>
    </div>
</div>

<script>
let stands = [];
let currentMaterial = null;

// Load from localStorage on page load
document.addEventListener('DOMContentLoaded', function() {
    const saved = localStorage.getItem('stage1_stands');
    if (saved) {
        const data = JSON.parse(saved);
        if (confirm('تم العثور على بيانات محفوظة. هل تريد استعادتها؟')) {
            currentMaterial = data.material;
            stands = data.stands;
            if (currentMaterial) {
                document.getElementById('materialBarcode').value = currentMaterial.barcode;
                document.getElementById('displayBarcode').textContent = currentMaterial.barcode;
                document.getElementById('displayType').textContent = currentMaterial.type;
                document.getElementById('displayWeight').textContent = currentMaterial.remaining_weight + ' كجم';
                document.getElementById('materialDisplay').classList.add('active');
            }
            renderStands();
        } else {
            localStorage.removeItem('stage1_stands');
        }
    }
    
    // Auto-save every 30 seconds
    setInterval(saveOffline, 30000);
});

// Barcode scanner
document.getElementById('materialBarcode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        loadMaterial(this.value.trim());
    }
});

// Auto-calculate waste percentage
document.getElementById('weight').addEventListener('input', calculateWastePercent);
document.getElementById('wasteAmount').addEventListener('input', calculateWastePercent);

function loadMaterial(barcode) {
    if (!barcode) {
        alert('⚠️ يرجى إدخال باركود المادة الخام!');
        return;
    }

    // Simulate API call - replace with actual AJAX
    // fetch(`/api/warehouse/get-by-barcode/${barcode}`)
    //     .then(response => response.json())
    //     .then(data => { ... })

    // Mock data for demonstration
    currentMaterial = {
        barcode: barcode,
        type: 'سلك نحاسي',
        remaining_weight: 1000
    };

    // Display material data
    document.getElementById('displayBarcode').textContent = currentMaterial.barcode;
    document.getElementById('displayType').textContent = currentMaterial.type;
    document.getElementById('displayWeight').textContent = currentMaterial.remaining_weight + ' كجم';
    document.getElementById('materialDisplay').classList.add('active');

    // Focus on stand number
    document.getElementById('standNumber').focus();

    // Show success message
    showToast('✅ تم تحميل بيانات المادة الخام بنجاح!', 'success');
}

// حساب وزن الاستاند النهائي ونسبة الهدر تلقائياً
document.getElementById('rawWeight').addEventListener('input', calculateFinalWeight);
document.getElementById('wasteAmount').addEventListener('input', calculateFinalWeight);

function calculateFinalWeight() {
    const rawWeight = parseFloat(document.getElementById('rawWeight').value) || 0;
    const wasteAmount = parseFloat(document.getElementById('wasteAmount').value) || 0;
    
    // وزن الاستاند النهائي = المأخوذ من المخزن - الهدر
    const finalWeight = rawWeight - wasteAmount;
    document.getElementById('weight').value = finalWeight > 0 ? finalWeight.toFixed(2) : '0.00';
    
    // حساب نسبة الهدر
    if (rawWeight > 0) {
        const percent = (wasteAmount / rawWeight * 100).toFixed(2);
        document.getElementById('wastePercentDisplay').textContent = percent + '%';
    } else {
        document.getElementById('wastePercentDisplay').textContent = '0%';
    }
}

function addStand() {
    if (!currentMaterial) {
        alert('⚠️ يرجى مسح باركود المادة الخام أولاً!');
        return;
    }

    const standNumber = document.getElementById('standNumber').value.trim();
    const wireSize = document.getElementById('wireSize').value;
    const rawWeight = document.getElementById('rawWeight').value;
    const wasteAmount = document.getElementById('wasteAmount').value || 0;
    const weight = document.getElementById('weight').value;
    const cost = document.getElementById('cost').value;
    const notes = document.getElementById('notes').value.trim();

    if (!standNumber || !wireSize || !rawWeight || !cost) {
        alert('⚠️ يرجى ملء جميع الحقول المطلوبة!');
        return;
    }

    const wastePercentage = parseFloat(rawWeight) > 0 ? (parseFloat(wasteAmount) / parseFloat(rawWeight) * 100).toFixed(2) : 0;

    const stand = {
        id: Date.now(),
        stand_number: standNumber,
        wire_size: parseFloat(wireSize),
        raw_weight: parseFloat(rawWeight),
        waste_amount: parseFloat(wasteAmount),
        waste_percentage: parseFloat(wastePercentage),
        weight: parseFloat(weight),
        cost: parseFloat(cost),
        notes: notes,
        material_barcode: currentMaterial.barcode
    };

    stands.push(stand);
    renderStands();
    clearForm();
    saveOffline();

    showToast('✅ تم إضافة الاستاند بنجاح!', 'success');
}

function renderStands() {
    const list = document.getElementById('standsList');
    document.getElementById('standsCount').textContent = stands.length;
    document.getElementById('submitBtn').disabled = stands.length === 0;

    if (stands.length === 0) {
        list.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>لا توجد استاندات مضافة بعد</p>
            </div>
        `;
        return;
    }

    list.innerHTML = stands.map(stand => `
        <div class="stand-item">
            <div class="stand-info">
                <strong>🔧 ${stand.stand_number}</strong>
                <small>
                    مقاس: ${stand.wire_size} مم | 
                    مأخوذ: ${stand.raw_weight} كجم | 
                    وزن نهائي: ${stand.weight} كجم | 
                    هدر: ${stand.waste_amount} كجم (${stand.waste_percentage}%) | 
                    تكلفة: ${stand.cost} ريال
                    ${stand.notes ? '<br>📝 ' + stand.notes : ''}
                </small>
            </div>
            <button class="btn-delete" onclick="removeStand(${stand.id})">🗑️ حذف</button>
        </div>
    `).join('');
}

function removeStand(id) {
    if (confirm('هل أنت متأكد من حذف هذا الاستاند؟')) {
        stands = stands.filter(s => s.id !== id);
        renderStands();
        saveOffline();
        showToast('🗑️ تم حذف الاستاند', 'info');
    }
}

function clearForm() {
    // Keep current material data
    document.getElementById('standTemplate').value = '';
    document.getElementById('standNumber').value = '';
    document.getElementById('wireSize').value = '';
    document.getElementById('rawWeight').value = '';
    document.getElementById('wasteAmount').value = '';
    document.getElementById('weight').value = '';
    document.getElementById('wastePercentDisplay').textContent = '0%';
    document.getElementById('cost').value = '';
    document.getElementById('notes').value = '';
    
    document.getElementById('standNumber').focus();
}

// Load template data
function loadTemplate() {
    const template = document.getElementById('standTemplate').value;
    
    if (!template) return;
    
    const templates = {
        '8mm': {
            wireSize: 8,
            weight: 100,
            cost: 1200
        },
        '10mm': {
            wireSize: 10,
            weight: 120,
            cost: 1500
        },
        '12mm': {
            wireSize: 12,
            weight: 150,
            cost: 1800
        },
        '14mm': {
            wireSize: 14,
            weight: 180,
            cost: 2200
        },
        '16mm': {
            wireSize: 16,
            weight: 200,
            cost: 2500
        }
    };
    
    const data = templates[template];
    if (data) {
        document.getElementById('wireSize').value = data.wireSize;
        document.getElementById('rawWeight').value = data.weight;
        document.getElementById('cost').value = data.cost;
        
        // Calculate waste amount automatically (2% default)
        const wasteAmount = (data.weight * 0.02).toFixed(2);
        document.getElementById('wasteAmount').value = wasteAmount;
        calculateFinalWeight();
        
        // Focus on stand number
        document.getElementById('standNumber').focus();
        
        showToast('✅ تم تطبيق القالب بنجاح! الوزن النهائي محسوب تلقائياً ويمكنك تعديل الهدر', 'success');
    }
}

function submitAll() {
    if (stands.length === 0) {
        alert('⚠️ يرجى إضافة استاند واحد على الأقل!');
        return;
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ جاري الحفظ...';

    // Prepare data
    const formData = {
        material_barcode: currentMaterial.barcode,
        stands: stands,
        _token: '{{ csrf_token() }}'
    };

    // Submit via AJAX
    fetch('{{ route("manufacturing.stage1.store") }}', {
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
            showToast('✅ تم حفظ جميع الاستاندات بنجاح!', 'success');
            localStorage.removeItem('stage1_stands');
            setTimeout(() => {
                window.location.href = '{{ route("manufacturing.stage1.index") }}';
            }, 1500);
        } else {
            throw new Error(data.message || 'حدث خطأ أثناء الحفظ');
        }
    })
    .catch(error => {
        alert('❌ خطأ: ' + error.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '✅ حفظ جميع الاستاندات';
    });
}

function saveOffline() {
    localStorage.setItem('stage1_stands', JSON.stringify({
        material: currentMaterial,
        stands: stands,
        timestamp: new Date().toISOString()
    }));
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#f39c12'};
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