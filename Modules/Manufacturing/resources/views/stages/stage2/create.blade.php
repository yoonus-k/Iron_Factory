@extends('master')

@section('title', 'المرحلة الثانية - المعالجة')

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
</style>

<div class="stage-container">
    <!-- Header -->
    <div class="stage-header">
        <h1>
            <i class="fas fa-cog"></i>
            المرحلة الثانية - معالجة الاستاندات
        </h1>
        <p>امسح باركود الاستاند وأضف بيانات المعالجة لإنشاء منتج معالج جديد</p>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 style="margin: 0 0 15px 0; color: #0066B2;"><i class="fas fa-camera"></i> مسح باركود الاستاند <span class="info-tooltip">?<span class="tooltip-text">مسح باركود الاستاند من المرحلة الأولى</span></span></h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="standBarcode" class="barcode-input" placeholder="امسح أو اكتب باركود الاستاند (ST1-XXX-2025)" autofocus>
            <span class="barcode-icon">🔧</span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 10px;"><i class="fas fa-lightbulb"></i> <span class="info-tooltip">?<span class="tooltip-text">امسح الباركود أو اضغط Enter للبحث</span></span></small>
    </div>

    <!-- Stand Display -->
    <div id="standDisplay" class="stand-display">
        <h4><i class="fas fa-circle-check"></i> بيانات الاستاند</h4>
        <div class="stand-info">
            <div class="info-item">
                <div class="info-label">الباركود <span class="info-tooltip">?<span class="tooltip-text">الرمز الشريطي الفريد للاستند</span></span></div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">مقاس السلك <span class="info-tooltip">?<span class="tooltip-text">قياس قطر السلك بالملليمتر</span></span></div>
                <div class="info-value" id="displayWireSize">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">الوزن <span class="info-tooltip">?<span class="tooltip-text">الوزن الإجمالي للاستند بالكيلوغرام</span></span></div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
        </div>
    </div>

    <!-- Processed Form -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-edit"></i> بيانات المعالجة</h3>

        <div class="info-box">
            <div class="info-box-header">
                <strong><i class="fas fa-thumbtack"></i> ملاحظة هامة: <span class="info-tooltip">?<span class="tooltip-text"><strong>معادلة حساب الهدر والوزن:</strong><br><br>• المعادلة: وزن الخروج = وزن الدخول - كمية الهدر<br><br>• الهدر الافتراضي: 3% من وزن الدخول<br><br>• وزن الدخول يُحدد تلقائياً من الاستاند المُمسوح</span></span></strong>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>نوع المعالجة <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">اختر نوع العملية التي سيتم تطبيقها على الاستاند</span></span></label>
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
                <label>وزن الدخول (كجم) <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">الوزن الإجمالي للاستند قبل المعالجة</span></span></label>
                <input type="number" id="inputWeight" class="form-control" step="0.01" readonly style="background: #e8f4f8; font-weight: 600;">
                <small style="color: #27ae60; display: block; margin-top: 5px;"><i class="fas fa-chart-bar"></i> <span class="info-tooltip">?<span class="tooltip-text">وزن الدخول يتم ملأه تلقائياً من بيانات الاستاند المممسوح</span></span></small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>وزن الخروج (كجم) <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">الوزن بعد تطبيق المعالجة</span></span></label>
                <input type="number" id="outputWeight" class="form-control" step="0.01">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-lightbulb"></i> <span class="info-tooltip">?<span class="tooltip-text">الوزن بعد تطبيق المعالجة (التسخين أو التبريد أو القطع)</span></span></small>
            </div>

            <div class="form-group">
                <label>كمية الهدر (كجم) <span class="info-tooltip">?<span class="tooltip-text">الفرق بين وزن الدخول ووزن الخروج</span></span></label>
                <input type="number" id="wasteAmount" class="form-control" step="0.01" readonly style="background: #ecf0f1;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-percent"></i> نسبة الهدر: <span id="wastePercentDisplay">0%</span> <span class="info-tooltip">?<span class="tooltip-text">النسبة المئوية للهدر من وزن الدخول</span></span></small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>تفاصيل المعالجة <span class="info-tooltip">?<span class="tooltip-text">تفاصيل إضافية حول عملية المعالجة</span></span></label>
                <textarea id="processDetails" class="form-control" placeholder="تفاصيل إضافية عن المعالجة..."></textarea>
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-sticky-note"></i> <span class="info-tooltip">?<span class="tooltip-text">يمكنك إضافة تفاصيل إضافية حول المعالجة</span></span></small>
            </div>

            <div class="form-group">
                <label>ملاحظات <span class="info-tooltip">?<span class="tooltip-text">ملاحظات إضافية حول العملية</span></span></label>
                <textarea id="notes" class="form-control" placeholder="ملاحظات اختيارية..."></textarea>
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-comment"></i> <span class="info-tooltip">?<span class="tooltip-text">يمكنك إضافة أي ملاحظات إضافية هنا</span></span></small>
            </div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-primary" onclick="addProcessed()">
                <i class="fas fa-plus"></i> إضافة المعالجة
            </button>
            <button type="button" class="btn-secondary" onclick="clearForm()">
                <i class="fas fa-sync"></i> مسح النموذج
            </button>
        </div>
    </div>

    <!-- Processed List -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-clipboard"></i> المعالجات المضافة (<span id="processedCount">0</span>)</h3>
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
            <i class="fas fa-check"></i> حفظ جميع المعالجات
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('manufacturing.stage2.index') }}'">
            <i class="fas fa-times"></i> إلغاء
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
            showToast('تم تحميل بيانات الاستاند بنجاح!', 'success');
        })
        .catch(error => {
            alert('خطأ: ' + error.message);
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

    showToast('تم إضافة المعالجة بنجاح!', 'success');
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
        showToast('تم حذف المعالجة', 'info');
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
