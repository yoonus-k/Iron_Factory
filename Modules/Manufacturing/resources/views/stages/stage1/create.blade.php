@extends('master')

@section('title', 'المرحلة الأولى - تقسيم المواد')

@section('content')
<style>
    /* Stage Container */
    .stage-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 0 15px;
    }

    /* Stage Header */
    .stage-header {
        background: #f8f9fa;
        color: #343a40;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        text-align: center;
        border: 1px solid #dee2e6;
    }

    .stage-header h1 {
        margin: 0 0 8px 0;
        font-size: 24px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .stage-header p {
        margin: 0;
        color: #6c757d;
        font-size: 16px;
    }

    /* Form Section */
    .form-section {
        background: white;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: #343a40;
        margin: 0 0 20px 0;
        padding-bottom: 12px;
        border-bottom: 2px solid #6c757d;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Form Layout */
    .form-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 15px;
        font-weight: 500;
        color: #343a40;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .required {
        color: #dc3545;
        margin-right: 4px;
    }

    .form-control, .form-select {
        padding: 14px 15px;
        border: 1px solid #ced4da;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s;
        background: #ffffff;
        height: 50px;
        touch-action: manipulation;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #86b7fe;
        background: white;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.25);
    }

    .form-control:disabled, .form-control:read-only {
        background: #e9ecef;
        cursor: not-allowed;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    /* Barcode Section */
    .barcode-section {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 25px;
        border: 1px dashed #6c757d;
        text-align: center;
    }

    .barcode-input-wrapper {
        position: relative;
        max-width: 100%;
        margin: 0 auto;
    }

    .barcode-input {
        width: 100%;
        padding: 16px 50px 16px 15px;
        font-size: 17px;
        border: 2px solid #6c757d;
        border-radius: 8px;
        font-weight: 500;
        background: white;
        text-align: center;
        height: 55px;
    }

    .barcode-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 24px;
        color: #6c757d;
    }

    /* Material Display */
    .material-display {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 25px;
        border-left: 4px solid #28a745;
        display: none;
    }

    .material-display.active {
        display: block;
        animation: slideIn 0.3s ease-out;
    }

    .material-display h4 {
        color: #28a745;
        margin: 0 0 15px 0;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .material-info {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        margin-top: 15px;
    }

    .info-item {
        background: rgba(255, 255, 255, 0.7);
        padding: 15px;
        border-radius: 8px;
        text-align: center;
    }

    .info-label {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .info-value {
        font-size: 18px;
        font-weight: 700;
        color: #343a40;
    }

    /* Stands List */
    .stands-list {
        margin-top: 25px;
    }

    .stand-item {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        border-left: 4px solid #6c757d;
        animation: slideIn 0.3s ease-out;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .stand-info {
        flex: 1;
    }

    .stand-info strong {
        color: #343a40;
        font-size: 17px;
        display: block;
        margin-bottom: 8px;
    }

    .stand-info small {
        color: #6c757d;
        font-size: 14px;
        line-height: 1.6;
    }

    .stand-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .btn-delete, .btn-print {
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 5px;
        min-height: 45px;
        touch-action: manipulation;
    }

    .btn-delete {
        background: #dc3545;
    }

    .btn-print {
        background: #17a2b8;
    }

    .btn-delete:hover {
        background: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    .btn-print:hover {
        background: #138496;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(23, 162, 184, 0.3);
    }

    /* Buttons */
    .btn-primary {
        background: #6c757d;
        color: white;
        border: none;
        padding: 14px 28px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        min-height: 50px;
        touch-action: manipulation;
    }

    .btn-primary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
    }

    .btn-success {
        background: #28a745;
        color: white;
        border: none;
        padding: 16px 36px;
        border-radius: 8px;
        font-size: 17px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        min-height: 55px;
        touch-action: manipulation;
    }

    .btn-success:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }

    .btn-success:disabled {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
        padding: 14px 28px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        min-height: 50px;
        touch-action: manipulation;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
    }

    /* Actions */
    .form-actions {
        display: flex;
        gap: 20px;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #e9ecef;
        justify-content: center;
        flex-wrap: wrap;
    }

    .button-group {
        display: flex;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
        flex-direction: column;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #6c757d;
    }

    .empty-state svg {
        width: 70px;
        height: 70px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    /* Info Box */
    .info-box {
        background: #f8f9fa;
        border-left: 4px solid #6c757d;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .info-box strong {
        color: #343a40;
        display: block;
        margin-bottom: 10px;
        font-size: 16px;
    }

    .info-box ul {
        margin: 10px 0 0 25px;
        color: #6c757d;
        font-size: 14px;
        line-height: 1.7;
    }

    /* Print Area */
    .print-area {
        display: none;
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
    @media (max-width: 992px) {
        .stage-container {
            max-width: 100%;
        }
        
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .material-info {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .stand-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .stand-actions {
            width: 100%;
            justify-content: flex-end;
        }
    }

    @media (max-width: 576px) {
        .stage-header {
            padding: 20px 15px;
        }
        
        .stage-header h1 {
            font-size: 20px;
            flex-direction: column;
            gap: 10px;
        }
        
        .form-section {
            padding: 20px 15px;
        }
        
        .section-title {
            font-size: 18px;
        }
        
        .btn-primary, .btn-secondary {
            padding: 12px 20px;
            font-size: 15px;
        }
        
        .btn-success {
            padding: 14px 25px;
            font-size: 16px;
        }
        
        .button-group {
            flex-direction: column;
        }
        
        .button-group .btn-primary, 
        .button-group .btn-secondary {
            width: 100%;
            justify-content: center;
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
        <h3 style="margin: 0 0 20px 0; color: #343a40; font-size: 22px;">📷 مسح باركود المادة الخام</h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="materialBarcode" class="barcode-input" placeholder="امسح أو اكتب باركود المادة الخام" autofocus>
            <span class="barcode-icon">🏷️</span>
        </div>
        <small style="color: #6c757d; display: block; margin-top: 15px; font-size: 15px;">💡 امسح الباركود أو اضغط Enter للبحث</small>
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
            <div class="form-group">
                <label for="standSelect">🎯 اختر الاستاند المتوفر <span class="required">*</span></label>
                <select id="standSelect" class="form-control" onchange="loadStand()" style="font-size: 16px;">
                    <option value="">-- اختر استاند متوفر من القائمة --</option>
                </select>
                <small style="color: #6c757d; display: block; margin-top: 5px;">💡 اختر الاستاند الذي تريد استخدامه (فقط الاستاندات الغير مستخدمة)</small>
            </div>
        </div>

        <div id="standDetails" style="display: none; margin: 25px 0; padding: 25px; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #28a745;">
            <h4 style="margin: 0 0 20px 0; color: #28a745; font-size: 18px; display: flex; align-items: center; gap: 10px;">📦 الاستاند المختار</h4>
            <div class="material-info">
                <div class="info-item">
                    <div class="info-label">رقم الاستاند</div>
                    <div class="info-value" id="selectedStandNumber" style="color: #28a745; font-weight: 700;">-</div>
                </div>
                <div class="info-item">
                    <div class="info-label">وزن الاستاند الفارغ</div>
                    <div class="info-value" id="selectedStandWeight" style="color: #343a40; font-weight: 700;">-</div>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="wasteWeight">🗑️ وزن الهدر (كجم)</label>
                <input type="number" id="wasteWeight" class="form-control" placeholder="سيتم حسابه تلقائياً" step="0.01" oninput="calculateWastePercentage()">
                <small style="color: #6c757d; display: block; margin-top: 5px;">يُحسب تلقائياً: الإجمالي - الصافي - وزن الاستاند (يمكن التعديل)</small>
            </div>
            <div class="form-group">
                <label for="wastePercentage">📊 نسبة الهدر (%)</label>
                <input type="number" id="wastePercentage" class="form-control" placeholder="0" step="0.01" readonly style="background: #e9ecef;">
                <small style="color: #6c757d; display: block; margin-top: 5px;">يُحسب تلقائياً من وزن الهدر</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="totalWeight">⚖️ الوزن الإجمالي (كجم) <span class="required">*</span></label>
                <input type="number" id="totalWeight" class="form-control" placeholder="أدخل الوزن الإجمالي" step="0.01" oninput="calculateNetWeight()" style="font-size: 16px;">
                <small style="color: #6c757d; display: block; margin-top: 5px;">الوزن الكلي شامل وزن الاستاند</small>
            </div>

            <div class="form-group">
                <label for="standWeight">📦 وزن الاستاند الفارغ (كجم)</label>
                <input type="number" id="standWeight" class="form-control" placeholder="سيتم جلبه تلقائياً" step="0.01" readonly style="background: #e9ecef; font-weight: 600;">
                <small style="color: #6c757d; display: block; margin-top: 5px;">يتم جلبه تلقائياً من بيانات الاستاند</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="netWeight">✅ الوزن الصافي (كجم) <span class="required">*</span></label>
                <input type="number" id="netWeight" class="form-control" placeholder="سيتم حسابه تلقائياً" step="0.01" readonly style="background: #f8f9fa; font-weight: 700; font-size: 20px; text-align: center; color: #28a745; border: 2px solid #28a745;">
                <small style="color: #28a745; display: block; margin-top: 8px; font-weight: 600; font-size: 15px;">📊 يُحسب تلقائياً: الوزن الإجمالي - وزن الاستاند الفارغ</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="notes">ملاحظات</label>
                <textarea id="notes" class="form-control" placeholder="ملاحظات اختيارية..." rows="3"></textarea>
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

<!-- Print Area (Hidden) -->
<div id="printArea" class="print-area">
    <div id="barcodeContainer"></div>
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
            <div class="stand-actions">
                <button class="btn-print" onclick="printBarcode(${item.id})">🖨️ طباعة</button>
                <button class="btn-delete" onclick="removeStand(${item.id})">🗑️ حذف</button>
            </div>
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
    document.getElementById('totalWeight').value = '';
    document.getElementById('standWeight').value = '';
    document.getElementById('netWeight').value = '';
    document.getElementById('wasteWeight').value = '';
    document.getElementById('wastePercentage').value = '';
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
            localStorage.removeItem('stage1_processed');
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

// Print barcode for a processed stand
function printBarcode(id) {
    const stand = processedStands.find(s => s.id === id);
    if (!stand) {
        alert('❌ لم يتم العثور على بيانات الاستاند!');
        return;
    }

    // Create barcode content
    const barcodeContent = `
        <div style="text-align: center; padding: 20px; font-family: Arial, sans-serif;">
            <h2 style="margin: 0 0 10px 0;">استاند مُعالج - ${stand.stand_number}</h2>
            <div style="margin: 15px 0;">
                <div style="font-size: 18px; font-weight: bold;">${stand.material_type}</div>
                <div style="font-size: 16px; margin: 5px 0;">الوزن الصافي: ${stand.net_weight} كجم</div>
                <div style="font-size: 14px; color: #666;">الباركود: ${stand.material_barcode}</div>
            </div>
            <div style="margin: 20px 0;">
                <img src="https://barcode.tec-it.com/barcode.ashx?data=${stand.material_barcode}&code=Code128&translate-esc=on" alt="Barcode">
            </div>
            <div style="font-size: 12px; color: #888;">
                تاريخ الطباعة: ${new Date().toLocaleDateString('ar-EG')}
            </div>
        </div>
    `;

    // Create print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>طباعة الباركود - ${stand.stand_number}</title>
            <style>
                body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
                @media print {
                    body { padding: 0; }
                }
            </style>
        </head>
        <body>
            ${barcodeContent}
            <script>
                window.onload = function() {
                    window.print();
                    // Close after printing (optional)
                    // window.close();
                }
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}

function showToast(message, type = 'info') {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast-notification');
    existingToasts.forEach(toast => toast.remove());

    // Create toast notification
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#6c757d'};
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
        max-width: 400px;
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

@endsection