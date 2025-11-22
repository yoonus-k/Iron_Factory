@extends('master')

@section('title', 'المرحلة الثانية - المعالجة')

@section('content')

<style>
    :root{
        --brand-1: #0b5fa5;
        --brand-2: #2a9fd6;
        --muted: #6e7a81;
        --surface: #f5f7fa;
        --card: #ffffff;
        --success: #27ae60;
        --danger: #e74c3c;
        --radius: 12px;
    }

    /* base layout */
    .stage-container{ max-width:1200px; margin:26px auto; padding:20px; font-family: 'Segoe UI', Tahoma, Arial; color:#24303a }

    .stage-header{ display:flex; gap:14px; align-items:center; background: linear-gradient(90deg,var(--brand-1),var(--brand-2)); color:#fff; padding:20px 22px; border-radius:10px; box-shadow:0 10px 30px rgba(11,95,165,0.12) }
    .stage-header h1{ margin:0; font-size:20px }
    .stage-header p{ margin:0; opacity:0.95; font-size:13px }

    /* cards */
    .form-section{ background:var(--card); padding:18px; border-radius:var(--radius); margin-top:18px; box-shadow:0 6px 18px rgba(10,30,60,0.04); border:1px solid rgba(34,47,62,0.04) }
    .section-title{ font-size:16px; color:var(--brand-1); font-weight:700 }

    /* tooltip */
    .info-tooltip{ position:relative; display:inline-flex; align-items:center; justify-content:center; width:20px; height:20px; background:var(--brand-1); color:#fff; border-radius:50%; font-size:11px; font-weight:700; cursor:help; margin-left:6px }
    .info-tooltip .tooltip-text{ visibility:hidden; opacity:0; width:260px; background:#24303a; color:#fff; padding:10px; border-radius:8px; position:absolute; z-index:1000; right:50%; transform:translateX(50%); bottom:130%; font-size:13px; line-height:1.5; box-shadow:0 6px 18px rgba(0,0,0,0.12) }
    .info-tooltip:hover .tooltip-text{ visibility:visible; opacity:1 }

    /* barcode */
    .barcode-section{ background: linear-gradient(180deg,#f3fbff 0,#eef9ff 100%); padding:20px; border-radius:10px; text-align:center; border:1px dashed rgba(11,95,165,0.06) }
    .barcode-input-wrapper{ max-width:720px; margin:0 auto; position:relative }
    .barcode-input{ width:100%; padding:16px 18px; border-radius:10px; border:2px solid rgba(11,95,165,0.12); font-size:16px; font-weight:600 }
    .barcode-icon{ position:absolute; left:16px; top:50%; transform:translateY(-50%); font-size:18px }

    /* display cards */
    .stand-display{ display:none; padding:14px; border-radius:10px; background:linear-gradient(180deg,#f8fcff,#eef9ff); border-left:4px solid var(--brand-1); margin-top:12px }
    .stand-display.active{ display:block }
    .stand-info{ display:grid; grid-template-columns:repeat(3,1fr); gap:12px }
    .info-item{ background:var(--card); padding:12px; border-radius:8px; text-align:center; box-shadow:0 4px 12px rgba(10,30,60,0.03) }
    .info-label{ font-size:13px; color:var(--muted); margin-bottom:6px; font-weight:600 }
    .info-value{ font-size:15px; font-weight:700; color:#22303a }

    /* form grid */
    .form-row{ display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:14px; margin-top:10px }
    .form-group label{ font-size:13px; color:var(--muted); font-weight:700; margin-bottom:6px }
    .form-control, .form-select{ padding:10px 12px; border-radius:8px; border:1.5px solid #e7eef5; background:#fbfeff }
    .form-control[readonly]{ background:#f1f6f9; font-weight:600 }

    textarea.form-control{ min-height:100px }

    /* processed list */
    .processed-item{ display:flex; justify-content:space-between; align-items:center; gap:12px; padding:12px; border-radius:10px; background:linear-gradient(180deg,#ffffff,#fbfeff); box-shadow:0 6px 18px rgba(10,30,60,0.03); margin-bottom:10px }
    .processed-info strong{ font-size:15px }

    /* buttons */
    .button-group{ display:flex; gap:10px; flex-wrap:wrap; margin-top:10px }
    .btn-primary, .btn-success, .btn-secondary{ border:none; border-radius:8px; padding:10px 14px; font-weight:700; cursor:pointer }
    .btn-primary{ background:var(--brand-1); color:white }
    .btn-success{ background:var(--success); color:white }
    .btn-secondary{ background:#8e9aa4; color:white }

    .btn-delete{ background:var(--danger); color:white; padding:8px 12px; border-radius:8px }
    .btn-print{ background:#1976d2; color:white; padding:8px 12px; border-radius:8px }

    .empty-state{ padding:30px; text-align:center; color:#98a2a8 }

    /* responsive */
    @media (max-width:900px){ .form-row{ grid-template-columns:1fr } .stand-info{ grid-template-columns:1fr } .stage-header{ flex-direction:column; text-align:center } }
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
    const barcodesData = [];

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
                
                // جمع بيانات الباركود
                if (data.data && data.data.barcode_info) {
                    barcodesData.push(data.data.barcode_info);
                }
                
                if (completed === total) {
                    showToast('✅ تم حفظ جميع المعالجات بنجاح!', 'success');
                    localStorage.removeItem('stage2_processed');
                    
                    // عرض نافذة الباركودات
                    if (barcodesData.length > 0) {
                        showBarcodesModal(barcodesData);
                    } else {
                        setTimeout(() => {
                            window.location.href = '{{ route("manufacturing.stage2.index") }}';
                        }, 1500);
                    }
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

// عرض نافذة الباركودات
function showBarcodesModal(barcodes) {
    const modal = document.createElement('div');
    modal.id = 'barcodesModal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        padding: 20px;
    `;

    // حساب الإجماليات
    const totalWeight = barcodes.reduce((sum, item) => sum + parseFloat(item.net_weight), 0);
    const totalWaste = barcodes.reduce((sum, item) => sum + parseFloat(item.waste_weight || 0), 0);
    const itemsCount = barcodes.length;

    let barcodesHTML = barcodes.map((item, index) => `
        <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%); padding: 25px; border-radius: 12px; margin-bottom: 20px; border-right: 5px solid #3498db; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: start; margin-bottom: 20px;">
                <div>
                    <h4 style="margin: 0 0 12px 0; color: #2c3e50; font-size: 20px; font-weight: 700;">
                        <i class="fas fa-cog" style="color: #3498db;"></i> ${item.stand_number}
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 15px;">
                        <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #27ae60;">
                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">المادة</div>
                            <div style="font-size: 14px; color: #2c3e50; font-weight: 700;">${item.material_name}</div>
                        </div>
                        <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #3498db;">
                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">الوزن الصافي</div>
                            <div style="font-size: 18px; color: #3498db; font-weight: 700;">${item.net_weight} كجم</div>
                        </div>
                        <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #e74c3c;">
                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">الهدر</div>
                            <div style="font-size: 16px; color: #e74c3c; font-weight: 700;">${item.waste_weight || 0} كجم</div>
                        </div>
                    </div>
                </div>
                <button onclick="printStage2Barcode('${item.barcode}', '${item.stand_number}', '${item.material_name}', ${item.net_weight})" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 8px; box-shadow: 0 3px 10px rgba(52, 152, 219, 0.3); transition: all 0.3s;">
                    <i class="fas fa-print"></i> طباعة
                </button>
            </div>
            <div style="background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                <svg id="barcode-stage2-${index}" style="max-width: 100%;"></svg>
                <div style="font-family: 'Courier New', monospace; font-size: 18px; font-weight: bold; color: #2c3e50; margin-top: 12px; letter-spacing: 3px; background: #f8f9fa; padding: 10px; border-radius: 6px;">
                    ${item.barcode}
                </div>
            </div>
        </div>
    `).join('');

    modal.innerHTML = `
        <div style="background: white; border-radius: 12px; max-width: 900px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
            <div style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; padding: 25px; border-radius: 12px 12px 0 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 24px; font-weight: 700;">
                        <i class="fas fa-check-circle"></i> تم معالجة المرحلة الثانية بنجاح!
                    </h2>
                    <button onclick="closeBarcodesModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; font-size: 24px; cursor: pointer; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                        ✕
                    </button>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; padding: 15px; background: rgba(255,255,255,0.15); border-radius: 10px; backdrop-filter: blur(10px);">
                    <div style="text-align: center;">
                        <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">عدد المعالجات</div>
                        <div style="font-size: 28px; font-weight: 700;">${itemsCount}</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">إجمالي الوزن</div>
                        <div style="font-size: 28px; font-weight: 700;">${totalWeight.toFixed(2)} كجم</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">إجمالي الهدر</div>
                        <div style="font-size: 28px; font-weight: 700;">${totalWaste.toFixed(2)} كجم</div>
                    </div>
                </div>
            </div>
            <div style="padding: 30px;">
                <h3 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 18px; border-bottom: 2px solid #e9ecef; padding-bottom: 12px;">
                    <i class="fas fa-barcode"></i> الباركودات المولدة
                </h3>
                ${barcodesHTML}
                <div style="display: flex; gap: 15px; margin-top: 25px; padding-top: 20px; border-top: 2px solid #e9ecef;">
                    <button onclick="printAllStage2Barcodes(${JSON.stringify(barcodes).replace(/"/g, '&quot;')})" style="flex: 1; background: #3498db; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <i class="fas fa-print"></i> طباعة الكل
                    </button>
                    <button onclick="window.location.href='{{ route('manufacturing.stage2.index') }}'" style="flex: 1; background: #27ae60; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <i class="fas fa-check"></i> تم، العودة للرئيسية
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // توليد الباركودات
    setTimeout(() => {
        barcodes.forEach((item, index) => {
            JsBarcode(`#barcode-stage2-${index}`, item.barcode, {
                format: 'CODE128',
                width: 2,
                height: 60,
                displayValue: false,
                margin: 10
            });
        });
    }, 100);
}

function closeBarcodesModal() {
    const modal = document.getElementById('barcodesModal');
    if (modal) {
        modal.remove();
    }
    window.location.href = '{{ route("manufacturing.stage2.index") }}';
}

function printStage2Barcode(barcode, standNumber, materialName, netWeight) {
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html dir="rtl"><head><title>طباعة الباركود - المرحلة الثانية</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
    printWindow.document.write('.barcode-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); text-align: center; max-width: 500px; }');
    printWindow.document.write('.title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 3px solid #3498db; }');
    printWindow.document.write('.stand-number { font-size: 20px; color: #3498db; font-weight: bold; margin: 15px 0; }');
    printWindow.document.write('.barcode-code { font-size: 18px; font-weight: bold; color: #2c3e50; margin: 20px 0; letter-spacing: 3px; font-family: "Courier New", monospace; }');
    printWindow.document.write('.info { margin-top: 25px; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: right; }');
    printWindow.document.write('.info-row { margin: 10px 0; display: flex; justify-content: space-between; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 14px; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 16px; }');
    printWindow.document.write('@media print { body { background: white; } }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<div class="barcode-container">');
    printWindow.document.write('<div class="title">باركود المرحلة الثانية</div>');
    printWindow.document.write('<div class="stand-number">' + standNumber + '</div>');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
    printWindow.document.write('<div class="info">');
    printWindow.document.write('<div class="info-row"><span class="label">المادة:</span><span class="value">' + materialName + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">الوزن الصافي:</span><span class="value">' + netWeight + ' كجم</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">التاريخ:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
    printWindow.document.write('</div></div>');
    printWindow.document.write('<script>');
    printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2, height: 80, displayValue: false, margin: 10 });');
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
    printWindow.document.write('<\/script></body></html>');
    printWindow.document.close();
}

function printAllStage2Barcodes(barcodes) {
    const printWindow = window.open('', '', 'height=800,width=1000');
    printWindow.document.write('<html dir="rtl"><head><title>طباعة جميع الباركودات - المرحلة الثانية</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }');
    printWindow.document.write('.barcode-item { background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); page-break-inside: avoid; }');
    printWindow.document.write('.title { font-size: 20px; font-weight: bold; color: #2c3e50; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #3498db; }');
    printWindow.document.write('.barcode-code { font-size: 16px; font-weight: bold; color: #2c3e50; margin: 15px 0; text-align: center; letter-spacing: 2px; font-family: "Courier New", monospace; }');
    printWindow.document.write('.info { margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 6px; }');
    printWindow.document.write('.info-row { margin: 8px 0; display: flex; justify-content: space-between; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 13px; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 14px; }');
    printWindow.document.write('@media print { body { background: white; padding: 0; } .barcode-item { box-shadow: none; page-break-after: always; } }');
    printWindow.document.write('</style></head><body>');
    
    barcodes.forEach((item, index) => {
        printWindow.document.write('<div class="barcode-item">');
        printWindow.document.write('<div class="title">باركود المرحلة الثانية - ' + item.stand_number + '</div>');
        printWindow.document.write('<div style="text-align: center;"><svg id="print-barcode-' + index + '"></svg></div>');
        printWindow.document.write('<div class="barcode-code">' + item.barcode + '</div>');
        printWindow.document.write('<div class="info">');
        printWindow.document.write('<div class="info-row"><span class="label">الاستاند:</span><span class="value">' + item.stand_number + '</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">المادة:</span><span class="value">' + item.material_name + '</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">الوزن الصافي:</span><span class="value">' + item.net_weight + ' كجم</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">الهدر:</span><span class="value">' + (item.waste_weight || 0) + ' كجم</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">التاريخ:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
        printWindow.document.write('</div></div>');
    });
    
    printWindow.document.write('<script>');
    barcodes.forEach((item, index) => {
        printWindow.document.write('JsBarcode("#print-barcode-' + index + '", "' + item.barcode + '", { format: "CODE128", width: 2, height: 70, displayValue: false, margin: 10 });');
    });
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 800); };');
    printWindow.document.write('<\/script></body></html>');
    printWindow.document.close();
}
</script>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

@endsection
