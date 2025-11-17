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
            المرحلة الأولى - تقسيم المواد على الاستاندات
        </h1>
        <p>امسح باركود المادة الخام واختر استاند متوفر لبدء التقسيم</p>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 style="margin: 0 0 15px 0; color: #f39c12;">📷 مسح باركود المادة الخام</h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="materialBarcode" class="barcode-input" placeholder="امسح أو اكتب باركود المادة الخام" autofocus>
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
                <div class="info-value" id="displayMaterialType">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">الوزن المتبقي</div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
        </div>
    </div>

    <!-- Stand Form -->
    <div class="form-section">
        <h3 class="section-title">🎯 اختيار الاستاند المتوفر</h3>

        <div class="info-box">
            <strong>📌 ملاحظة:</strong>
            <ul>
                <li>الوزن الصافي = الوزن الإجمالي - وزن الاستاند الفارغ</li>
                <li>مثال: 100 كجم إجمالي - 2 كجم وزن الاستاند = 98 كجم صافي</li>
                <li>سيتم تحويل حالة الاستاند إلى "مستخدم" تلقائياً</li>
            </ul>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>🎯 اختر الاستاند المتوفر <span class="required">*</span></label>
                <select id="standSelect" class="form-control" onchange="loadStand()" style="font-size: 16px; padding: 14px;">
                    <option value="">-- اختر استاند متوفر من القائمة --</option>
                </select>
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">💡 اختر الاستاند الذي تريد استخدامه (فقط الاستاندات الغير مستخدمة)</small>
            </div>
        </div>

        <div id="standDetails" style="display: none; margin: 20px 0; padding: 20px; background: linear-gradient(135deg, #e8f8f5 0%, #d5f4e6 100%); border-radius: 8px; border-right: 4px solid #27ae60;">
            <h4 style="margin: 0 0 15px 0; color: #27ae60; font-size: 16px;">📦 الاستاند المختار</h4>
            <div class="material-info" style="grid-template-columns: repeat(2, 1fr);">
                <div class="info-item">
                    <div class="info-label">رقم الاستاند</div>
                    <div class="info-value" id="selectedStandNumber" style="color: #27ae60; font-weight: 700;">-</div>
                </div>
                <div class="info-item">
                    <div class="info-label">وزن الاستاند الفارغ</div>
                    <div class="info-value" id="selectedStandWeight" style="color: #e67e22; font-weight: 700;">-</div>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>🗑️ وزن الهدر (كجم)</label>
                <input type="number" id="wasteWeight" class="form-control" placeholder="سيتم حسابه تلقائياً" step="0.01" oninput="calculateWastePercentage()">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">يُحسب تلقائياً: الإجمالي - الصافي - وزن الاستاند (يمكن التعديل)</small>
            </div>
            <div class="form-group">
                <label>📊 نسبة الهدر (%)</label>
                <input type="number" id="wastePercentage" class="form-control" placeholder="0" step="0.01" readonly style="background: #ecf0f1;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">يُحسب تلقائياً من وزن الهدر</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>⚖️ الوزن الإجمالي (كجم) <span class="required">*</span></label>
                <input type="number" id="totalWeight" class="form-control" placeholder="أدخل الوزن الإجمالي" step="0.01" oninput="calculateNetWeight()" style="font-size: 15px;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">الوزن الكلي شامل وزن الاستاند</small>
            </div>

            <div class="form-group">
                <label>📦 وزن الاستاند الفارغ (كجم)</label>
                <input type="number" id="standWeight" class="form-control" placeholder="سيتم جلبه تلقائياً" step="0.01" readonly style="background: #ecf0f1; font-weight: 600;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">يتم جلبه تلقائياً من بيانات الاستاند</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>✅ الوزن الصافي (كجم) <span class="required">*</span></label>
                <input type="number" id="netWeight" class="form-control" placeholder="سيتم حسابه تلقائياً" step="0.01" readonly style="background: linear-gradient(135deg, #d5f4e6 0%, #e8f8f5 100%); font-weight: 700; font-size: 18px; text-align: center; color: #27ae60; border: 2px solid #27ae60;">
                <small style="color: #27ae60; display: block; margin-top: 8px; font-weight: 600;">📊 يُحسب تلقائياً: الوزن الإجمالي - وزن الاستاند الفارغ</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>ملاحظات</label>
                <textarea id="notes" class="form-control" placeholder="ملاحظات اختيارية..." rows="2"></textarea>
            </div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-primary" onclick="addProcessedStand()">
                ➕ إضافة للقائمة
            </button>
            <button type="button" class="btn-secondary" onclick="clearForm()">
                🔄 مسح النموذج
            </button>
        </div>
    </div>

    <!-- Processed Stands List -->
    <div class="form-section">
        <h3 class="section-title">📋 الاستاندات المعالجة (<span id="standsCount">0</span>)</h3>
        <div id="standsList" class="stands-list">
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>لا توجد استاندات معالجة بعد</p>
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
let processedStands = [];
let selectedStand = null;
let currentMaterial = null;

// Load stands on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStandsList();
    
    const saved = localStorage.getItem('stage1_processed');
    if (saved) {
        const data = JSON.parse(saved);
        if (confirm('تم العثور على بيانات محفوظة. هل تريد استعادتها؟')) {
            currentMaterial = data.material;
            processedStands = data.stands;
            if (currentMaterial) {
                document.getElementById('materialBarcode').value = currentMaterial.barcode;
                displayMaterialInfo(currentMaterial);
            }
            renderStands();
        } else {
            localStorage.removeItem('stage1_processed');
        }
    }
    
    setInterval(saveOffline, 30000);
});

// Barcode scanner
document.getElementById('materialBarcode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        loadMaterialByBarcode(this.value.trim());
    }
});

// Load material by barcode
function loadMaterialByBarcode(barcode) {
    if (!barcode) {
        alert('⚠️ يرجى إدخال باركود المادة الخام!');
        return;
    }

    // TODO: Replace with actual API call
    fetch(`/manufacturing/warehouse-products/get-by-barcode/${barcode}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.material) {
            currentMaterial = data.material;
            displayMaterialInfo(currentMaterial);
            showToast('✅ تم تحميل بيانات المادة الخام بنجاح!', 'success');
        } else {
            throw new Error(data.message || 'المادة غير موجودة');
        }
    })
    .catch(error => {
        console.error('خطأ:', error);
        // Mock data for testing
        currentMaterial = {
            id: 1,
            barcode: barcode,
            material_type: 'سلك حديد',
            remaining_weight: 1000
        };
        displayMaterialInfo(currentMaterial);
        showToast('✅ تم تحميل بيانات المادة (وضع تجريبي)', 'success');
    });
}

function displayMaterialInfo(material) {
    document.getElementById('displayBarcode').textContent = material.barcode;
    document.getElementById('displayMaterialType').textContent = material.material_type || 'غير محدد';
    document.getElementById('displayWeight').textContent = (material.remaining_weight || 0) + ' كجم';
    document.getElementById('materialDisplay').classList.add('active');
}

// Load stands from API
function loadStandsList() {
    console.log('🔄 جاري تحميل الاستاندات...');
    
    fetch('/stands?status=unused', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ البيانات المستلمة:', data);
        const select = document.getElementById('standSelect');
        select.innerHTML = '<option value="">-- اختر استاند متوفر من القائمة --</option>';
        
        if (data.stands && data.stands.length > 0) {
            console.log('📦 عدد الاستاندات:', data.stands.length);
            data.stands.forEach(stand => {
                const option = document.createElement('option');
                option.value = stand.id;
                option.textContent = `${stand.stand_number} - وزن فارغ: ${stand.weight} كجم`;
                option.dataset.stand = JSON.stringify(stand);
                select.appendChild(option);
            });
            showToast(`✅ تم تحميل ${data.stands.length} استاند متاح`, 'success');
        } else {
            console.warn('⚠️ لا توجد استاندات متاحة');
            select.innerHTML = '<option value="">لا توجد استاندات متاحة - أضف استاند جديد أولاً</option>';
            showToast('⚠️ لا توجد استاندات متاحة حالياً', 'warning');
        }
    })
    .catch(error => {
        console.error('❌ خطأ في تحميل الاستاندات:', error);
        const select = document.getElementById('standSelect');
        select.innerHTML = '<option value="">حدث خطأ في تحميل الاستاندات - حاول مرة أخرى</option>';
        showToast('❌ فشل تحميل قائمة الاستاندات: ' + error.message, 'error');
    });
}

// Load selected stand
function loadStand() {
    const select = document.getElementById('standSelect');
    const selectedOption = select.options[select.selectedIndex];
    
    if (!selectedOption.value) {
        document.getElementById('standDetails').style.display = 'none';
        document.getElementById('standWeight').value = '';
        document.getElementById('netWeight').value = '';
        selectedStand = null;
        return;
    }
    
    selectedStand = JSON.parse(selectedOption.dataset.stand);
    
    document.getElementById('selectedStandNumber').textContent = selectedStand.stand_number;
    document.getElementById('selectedStandWeight').textContent = selectedStand.weight + ' كجم';
    document.getElementById('standWeight').value = selectedStand.weight;
    document.getElementById('standDetails').style.display = 'block';
    
    calculateNetWeight();
    showToast('✅ تم تحميل بيانات الاستاند', 'success');
}

// Calculate net weight and waste
function calculateNetWeight() {
    const total = parseFloat(document.getElementById('totalWeight').value) || 0;
    const standWeight = parseFloat(document.getElementById('standWeight').value) || 0;
    
    if (total > 0 && standWeight > 0) {
        const net = total - standWeight;
        document.getElementById('netWeight').value = net.toFixed(2);
        
        // حساب وزن الهدر تلقائياً (الفرق بين الإجمالي والصافي والاستاند)
        const waste = total - standWeight - net;
        if (waste >= 0) {
            document.getElementById('wasteWeight').value = waste.toFixed(2);
            calculateWastePercentage();
        }
    } else {
        document.getElementById('netWeight').value = '';
        document.getElementById('wasteWeight').value = '';
        document.getElementById('wastePercentage').value = '';
    }
}

// Calculate waste percentage from weight
function calculateWastePercentage() {
    const wasteWeight = parseFloat(document.getElementById('wasteWeight').value) || 0;
    const totalWeight = parseFloat(document.getElementById('totalWeight').value) || 0;
    
    if (totalWeight > 0 && wasteWeight >= 0) {
        const percentage = (wasteWeight / totalWeight) * 100;
        document.getElementById('wastePercentage').value = percentage.toFixed(2);
    } else {
        document.getElementById('wastePercentage').value = '0';
    }
}



function addProcessedStand() {
    if (!currentMaterial) {
        alert('⚠️ يرجى مسح باركود المادة الخام أولاً!');
        return;
    }
    
    if (!selectedStand) {
        alert('⚠️ يرجى اختيار استاند متوفر من القائمة!');
        return;
    }

    const totalWeight = document.getElementById('totalWeight').value;
    const netWeight = document.getElementById('netWeight').value;
    const wasteWeight = document.getElementById('wasteWeight').value || 0;
    const wastePercentage = document.getElementById('wastePercentage').value || 0;
    const notes = document.getElementById('notes').value.trim();

    if (!totalWeight || !netWeight) {
        alert('⚠️ يرجى ملء جميع الحقول المطلوبة!');
        return;
    }

    const processedData = {
        id: Date.now(),
        material_id: currentMaterial.id,
        material_barcode: currentMaterial.barcode,
        material_type: currentMaterial.material_type,
        stand_id: selectedStand.id,
        stand_number: selectedStand.stand_number,
        stand_weight: parseFloat(document.getElementById('standWeight').value),
        wire_size: 0,
        total_weight: parseFloat(totalWeight),
        net_weight: parseFloat(netWeight),
        waste_weight: parseFloat(wasteWeight),
        waste_percentage: parseFloat(wastePercentage),
        cost: 0,
        notes: notes
    };

    processedStands.push(processedData);
    renderStands();
    clearForm();
    saveOffline();

    showToast('✅ تم إضافة البيانات بنجاح!', 'success');
}

function renderStands() {
    const list = document.getElementById('standsList');
    document.getElementById('standsCount').textContent = processedStands.length;
    document.getElementById('submitBtn').disabled = processedStands.length === 0;

    if (processedStands.length === 0) {
        list.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>لا توجد استاندات معالجة بعد</p>
            </div>
        `;
        return;
    }

    list.innerHTML = processedStands.map(item => `
        <div class="stand-item">
            <div class="stand-info">
                <strong>🔧 ${item.stand_number}</strong>
                <small>
                    مادة: ${item.material_type} | 
                    إجمالي: ${item.total_weight} كجم | 
                    صافي: ${item.net_weight} كجم | 
                    وزن الاستاند: ${item.stand_weight} كجم | 
                    هدر: ${item.waste_weight || 0} كجم (${item.waste_percentage || 0}%)
                    ${item.notes ? '<br>📝 ' + item.notes : ''}
                </small>
            </div>
            <button class="btn-delete" onclick="removeStand(${item.id})">🗑️ حذف</button>
        </div>
    `).join('');
}

function removeStand(id) {
    if (confirm('هل أنت متأكد من حذف هذه البيانات؟')) {
        processedStands = processedStands.filter(s => s.id !== id);
        renderStands();
        saveOffline();
        showToast('🗑️ تم حذف البيانات', 'info');
    }
}

function clearForm() {
    document.getElementById('standSelect').value = '';
    document.getElementById('standDetails').style.display = 'none';
    document.getElementById('wireSize').value = '';
    document.getElementById('totalWeight').value = '';
    document.getElementById('standWeight').value = '';
    document.getElementById('netWeight').value = '';
    document.getElementById('wastePercentage').value = '';
    document.getElementById('cost').value = '';
    document.getElementById('notes').value = '';
    selectedStand = null;
    
    document.getElementById('standSelect').focus();
}

function saveOffline() {
    localStorage.setItem('stage1_processed', JSON.stringify({
        material: currentMaterial,
        stands: processedStands,
        timestamp: new Date().toISOString()
    }));
}

function submitAll() {
    if (processedStands.length === 0) {
        alert('⚠️ يرجى إضافة استاند واحد معالج على الأقل!');
        return;
    }

    if (!currentMaterial) {
        alert('⚠️ بيانات المادة الخام مفقودة!');
        return;
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ جاري الحفظ...';

    const formData = {
        material_id: currentMaterial.id,
        material_barcode: currentMaterial.barcode,
        processed_stands: processedStands,
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
    localStorage.setItem('stage1_processed', JSON.stringify({
        stands: processedStands,
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