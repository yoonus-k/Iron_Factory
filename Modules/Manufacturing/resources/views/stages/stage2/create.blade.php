@extends('master')

@section('title', 'المرحلة الثانية - المعالجة')

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
        background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
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
        border-bottom: 2px solid #27ae60;
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
        border-color: #27ae60;
        background: white;
        box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
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
        background: linear-gradient(135deg, #e8f5e9 0%, #d5f0d9 100%);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border: 2px dashed #27ae60;
    }

    .barcode-input-wrapper {
        position: relative;
    }

    .barcode-input {
        width: 100%;
        padding: 15px 50px 15px 15px;
        font-size: 16px;
        border: 2px solid #27ae60;
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
        color: #27ae60;
    }

    /* Stand Display */
    .stand-display {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border-right: 4px solid #2196f3;
        display: none;
    }

    .stand-display.active {
        display: block;
        animation: slideIn 0.3s ease-out;
    }

    .stand-display h4 {
        color: #2196f3;
        margin: 0 0 10px 0;
        font-size: 16px;
    }

    .stand-info {
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

    /* Processed List */
    .processed-list {
        margin-top: 20px;
    }

    .processed-item {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-right: 4px solid #27ae60;
        animation: slideIn 0.3s ease-out;
    }

    .processed-info strong {
        color: var(--dark-color);
        font-size: 15px;
        display: block;
        margin-bottom: 6px;
    }

    .processed-info small {
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
        background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
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
        box-shadow: 0 6px 16px rgba(39, 174, 96, 0.3);
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
        
        .stand-info {
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
            <span>⚙️</span>
            المرحلة الثانية - معالجة الاستاندات
        </h1>
        <p>امسح باركود الاستاند وأضف بيانات المعالجة لإنشاء منتج معالج جديد</p>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 style="margin: 0 0 15px 0; color: #27ae60;">📷 مسح باركود الاستاند</h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="standBarcode" class="barcode-input" placeholder="امسح أو اكتب باركود الاستاند (ST1-XXX-2025)" autofocus>
            <span class="barcode-icon">🔧</span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 10px;">💡 امسح الباركود أو اضغط Enter للبحث</small>
    </div>

    <!-- Stand Display -->
    <div id="standDisplay" class="stand-display">
        <h4>✅ بيانات الاستاند</h4>
        <div class="stand-info">
            <div class="info-item">
                <div class="info-label">الباركود</div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">مقاس السلك</div>
                <div class="info-value" id="displayWireSize">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">الوزن</div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
        </div>
    </div>

    <!-- Processed Form -->
    <div class="form-section">
        <h3 class="section-title">📝 بيانات المعالجة</h3>

        <div class="info-box">
            <strong>📌 ملاحظة هامة:</strong>
            <ul>
                <li>المعادلة: وزن الخروج = وزن الدخول - كمية الهدر</li>
                <li>الهدر الافتراضي: 3% من وزن الدخول</li>
                <li>وزن الدخول يُحدد تلقائياً من الاستاند المُمسوح</li>
            </ul>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>نوع المعالجة <span class="required">*</span></label>
                <select id="processType" class="form-select">
                    <option value="">اختر نوع المعالجة</option>
                    <option value="heating">التسخين</option>
                    <option value="cooling">التبريد</option>
                    <option value="cutting">القطع</option>
                    <option value="rolling">الفرد</option>
                    <option value="shaping">التشكيل</option>
                    <option value="polishing">الصقل</option>
                </select>
            </div>

            <div class="form-group">
                <label>وزن الدخول (كجم) <span class="required">*</span></label>
                <input type="number" id="inputWeight" class="form-control" step="0.01" readonly style="background: #e8f4f8; font-weight: 600;">
                <small style="color: #27ae60; display: block; margin-top: 5px;">📊 يُملأ تلقائياً من وزن الاستاند</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>وزن الخروج (كجم) <span class="required">*</span></label>
                <input type="number" id="outputWeight" class="form-control" step="0.01">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">💡 الوزن بعد المعالجة</small>
            </div>

            <div class="form-group">
                <label>كمية الهدر (كجم)</label>
                <input type="number" id="wasteAmount" class="form-control" step="0.01" readonly style="background: #ecf0f1;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">نسبة الهدر: <span id="wastePercentDisplay">0%</span></small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>تفاصيل المعالجة</label>
                <textarea id="processDetails" class="form-control" placeholder="تفاصيل إضافية عن المعالجة..."></textarea>
            </div>

            <div class="form-group">
                <label>ملاحظات</label>
                <textarea id="notes" class="form-control" placeholder="ملاحظات اختيارية..."></textarea>
            </div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-primary" onclick="addProcessed()">
                ➕ إضافة المعالجة
            </button>
            <button type="button" class="btn-secondary" onclick="clearForm()">
                🔄 مسح النموذج
            </button>
        </div>
    </div>

    <!-- Processed List -->
    <div class="form-section">
        <h3 class="section-title">📋 المعالجات المضافة (<span id="processedCount">0</span>)</h3>
        <div id="processedList" class="processed-list">
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>لا توجد معالجات مضافة بعد</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="form-actions">
        <button type="button" class="btn-success" onclick="submitAll()" id="submitBtn" disabled>
            ✅ حفظ جميع المعالجات
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('manufacturing.stage2.index') }}'">
            ❌ إلغاء
        </button>
    </div>
</div>

<script>
let processedItems = [];
let currentStand = null;

// Load from localStorage on page load
document.addEventListener('DOMContentLoaded', function() {
    const saved = localStorage.getItem('stage2_processed');
    if (saved) {
        const data = JSON.parse(saved);
        if (confirm('تم العثور على بيانات محفوظة. هل تريد استعادتها؟')) {
            processedItems = data.items;
            renderProcessed();
        } else {
            localStorage.removeItem('stage2_processed');
        }
    }
    
    // Auto-save every 30 seconds
    setInterval(saveOffline, 30000);
});

// Barcode scanner
document.getElementById('standBarcode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        loadStand(this.value.trim());
        this.value = ''; // Clear for next scan
    }
});

// Auto-calculate waste when output weight changes
document.getElementById('outputWeight').addEventListener('input', calculateWaste);

function loadStand(barcode) {
    if (!barcode) {
        alert('⚠️ يرجى إدخال باركود الاستاند!');
        return;
    }

    // Fetch data from API
    fetch(`/stage1/get-by-barcode/${barcode}`)
        .then(response => {
            if (!response.ok) throw new Error('لم يتم العثور على البيانات');
            return response.json();
        })
        .then(result => {
            if (!result.success) throw new Error(result.message);
            
            const data = result.data;
            currentStand = {
                id: data.id,
                barcode: data.barcode,
                wire_size: data.wire_size || '0',
                weight: parseFloat(data.remaining_weight),
                material_id: data.material_id
            };

            // Display stand data
            document.getElementById('displayBarcode').textContent = currentStand.barcode;
            document.getElementById('displayWireSize').textContent = currentStand.wire_size + ' مم';
            document.getElementById('displayWeight').textContent = currentStand.weight + ' كجم';
            document.getElementById('standDisplay').classList.add('active');

            // Fill input weight automatically
            document.getElementById('inputWeight').value = currentStand.weight;

            // Calculate expected output weight (default 3% waste)
            const expectedWaste = currentStand.weight * 0.03;
            const expectedOutput = currentStand.weight - expectedWaste;
            document.getElementById('outputWeight').value = '';
            
            // Calculate initial waste
            calculateWaste();

            // Focus on process type
            document.getElementById('processType').focus();

            // Show success message
            showToast('✅ تم تحميل بيانات الاستاند بنجاح!', 'success');
        })
        .catch(error => {
            alert('❌ خطأ: ' + error.message);
            document.getElementById('standBarcode').focus();
        });
}

function calculateWaste() {
    const inputWeight = parseFloat(document.getElementById('inputWeight').value) || 0;
    const outputWeight = parseFloat(document.getElementById('outputWeight').value) || 0;
    
    if (inputWeight > 0 && outputWeight > 0) {
        const wasteAmount = (inputWeight - outputWeight).toFixed(2);
        const wastePercent = ((inputWeight - outputWeight) / inputWeight * 100).toFixed(2);
        document.getElementById('wasteAmount').value = wasteAmount;
        document.getElementById('wastePercentDisplay').textContent = wastePercent + '%';
    } else {
        document.getElementById('wasteAmount').value = '0';
        document.getElementById('wastePercentDisplay').textContent = '0%';
    }
}

function addProcessed() {
    if (!currentStand) {
        alert('⚠️ يرجى مسح باركود الاستاند أولاً!');
        document.getElementById('standBarcode').focus();
        return;
    }

    const processType = document.getElementById('processType').value;
    const inputWeight = document.getElementById('inputWeight').value;
    const outputWeight = document.getElementById('outputWeight').value;
    const wasteAmount = document.getElementById('wasteAmount').value || 0;
    const processDetails = document.getElementById('processDetails').value.trim();
    const notes = document.getElementById('notes').value.trim();

    if (!processType || !inputWeight || !outputWeight) {
        alert('⚠️ يرجى ملء جميع الحقول المطلوبة!');
        return;
    }

    const wastePercentage = parseFloat(inputWeight) > 0 ? 
        ((parseFloat(inputWeight) - parseFloat(outputWeight)) / parseFloat(inputWeight) * 100).toFixed(2) : 0;

    const processed = {
        id: Date.now(),
        stand_barcode: currentStand.barcode,
        stage1_id: currentStand.id,
        stage1_barcode: currentStand.barcode,
        process_type: processType,
        total_weight: parseFloat(outputWeight),
        waste_weight: parseFloat(wasteAmount),
        net_weight: parseFloat(outputWeight),
        process_details: processDetails,
        notes: notes
    };

    processedItems.push(processed);
    renderProcessed();
    clearForm();
    saveOffline();

    // Focus on barcode for next scan
    document.getElementById('standBarcode').focus();

    showToast('✅ تم إضافة المعالجة بنجاح!', 'success');
}

function renderProcessed() {
    const list = document.getElementById('processedList');
    document.getElementById('processedCount').textContent = processedItems.length;
    document.getElementById('submitBtn').disabled = processedItems.length === 0;

    if (processedItems.length === 0) {
        list.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>لا توجد معالجات مضافة بعد</p>
            </div>
        `;
        return;
    }

    const processTypeNames = {
        heating: 'التسخين',
        cooling: 'التبريد',
        cutting: 'القطع',
        rolling: 'الفرد',
        shaping: 'التشكيل',
        polishing: 'الصقل'
    };

    list.innerHTML = processedItems.map(item => `
        <div class="processed-item">
            <div class="processed-info">
                <strong>⚙️ ${item.stand_barcode} → ${processTypeNames[item.process_type]}</strong>
                <small>
                    وزن إجمالي: ${item.total_weight} كجم | 
                    وزن صافي: ${item.net_weight} كجم | 
                    هدر: ${item.waste_weight} كجم
                    ${item.process_details ? '<br>📝 ' + item.process_details : ''}
                    ${item.notes ? '<br>💬 ' + item.notes : ''}
                </small>
            </div>
            <button class="btn-delete" onclick="removeProcessed(${item.id})">🗑️ حذف</button>
        </div>
    `).join('');
}

function removeProcessed(id) {
    if (confirm('هل أنت متأكد من حذف هذه المعالجة؟')) {
        processedItems = processedItems.filter(p => p.id !== id);
        renderProcessed();
        saveOffline();
        showToast('🗑️ تم حذف المعالجة', 'info');
    }
}

function clearForm() {
    // Keep current stand data
    document.getElementById('processType').value = '';
    document.getElementById('inputWeight').value = '';
    document.getElementById('outputWeight').value = '';
    document.getElementById('wasteAmount').value = '';
    document.getElementById('wastePercentDisplay').textContent = '0%';
    document.getElementById('processDetails').value = '';
    document.getElementById('notes').value = '';
    
    // Reset input weight from current stand
    if (currentStand) {
        document.getElementById('inputWeight').value = currentStand.weight;
    }
}

function submitAll() {
    if (processedItems.length === 0) {
        alert('⚠️ يرجى إضافة معالجة واحدة على الأقل!');
        return;
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ جاري الحفظ...';

    // Send each item individually
    let completed = 0;
    const total = processedItems.length;

    processedItems.forEach((item, index) => {

        fetch('{{ route("manufacturing.stage2.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(item)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                completed++;
                if (completed === total) {
                    showToast('✅ تم حفظ جميع المعالجات بنجاح!', 'success');
                    localStorage.removeItem('stage2_processed');
                    setTimeout(() => {
                        window.location.href = '{{ route("manufacturing.stage2.index") }}';
                    }, 1500);
                }
            } else {
                throw new Error(data.message || 'حدث خطأ أثناء الحفظ');
            }
        })
        .catch(error => {
            alert('❌ خطأ في المعالجة ' + (index + 1) + ': ' + error.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '✅ حفظ جميع المعالجات';
        });
    });
}

function saveOffline() {
    localStorage.setItem('stage2_processed', JSON.stringify({
        items: processedItems,
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
        background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#27ae60'};
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