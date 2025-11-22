@extends('master')

@section('title', 'المرحلة الأولى - تقسيم المواد')

@section('content')

<style>
    /* Design system variables */
    :root{
        --brand-1: #0b5fa5; /* deep factory blue */
        --brand-2: #2a9fd6; /* accent */
        --success: #27ae60;
        --muted: #7f8c8d;
        --card-bg: #ffffff;
        --surface: #f5f7fa;
        --danger: #e74c3c;
        --radius: 12px;
        --gap: 20px;
    }

    /* Container */
    .stage-container{
        max-width: 1200px;
        margin: 28px auto;
        padding: 24px;
        font-family: 'Segoe UI', Tahoma, system-ui, -apple-system, 'Helvetica Neue', Arial;
        color: #263238;
    }

    /* Header */
    .stage-header{
        background: linear-gradient(90deg, var(--brand-1), var(--brand-2));
        color: #fff;
        padding: 28px 30px;
        border-radius: 14px;
        display:flex;
        gap: 18px;
        align-items: center;
        box-shadow: 0 10px 30px rgba(11,95,165,0.12);
    }

    .stage-header h1{ font-size: 22px; margin: 0; font-weight: 700; display:flex; gap:12px; align-items:center }
    .stage-header p{ margin:0; opacity:0.95; font-size:14px }

    /* Card sections */
    .form-section{
        background: var(--card-bg);
        padding: 22px;
        border-radius: var(--radius);
        margin-top: var(--gap);
        box-shadow: 0 6px 18px rgba(40,50,60,0.04);
        border: 1px solid rgba(34,47,62,0.04);
    }

    .section-title{ font-size:18px; font-weight:700; color:var(--brand-1); display:flex; gap:10px; align-items:center }

    /* Grid layout */
    .form-row{ display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:18px; margin-top:16px }
    .form-group label{ font-size:14px; color:var(--muted); font-weight:600; margin-bottom:8px }

    .form-control, .form-select{ padding:12px 14px; border-radius:10px; border:1.5px solid #e6edf3; font-size:15px; background:#fbfeff; transition:box-shadow .18s, border-color .18s }
    .form-control:focus, .form-select:focus{ outline:none; border-color:var(--brand-1); box-shadow:0 6px 20px rgba(11,95,165,0.08) }

    textarea.form-control{ min-height:110px }

    /* Barcode prominent input */
    .barcode-section{ background: linear-gradient(180deg,#f3fbff 0,#e8f6ff 100%); padding:26px; border-radius:12px; border:1px dashed rgba(10,110,180,0.08); text-align:center }
    .barcode-input-wrapper{ max-width:720px; margin:0 auto; position:relative }
    .barcode-input{ width:100%; padding:20px 22px; border-radius:10px; font-size:18px; border:2px solid rgba(11,95,165,0.12); font-weight:600; box-shadow: inset 0 -6px 18px rgba(0,0,0,0.02) }
    .barcode-icon{ position:absolute; left:18px; top:50%; transform:translateY(-50%); color:var(--brand-1); font-size:22px }

    /* Info cards */
    .material-display, .stand-display{ display:none; padding:18px; border-radius:12px; background:linear-gradient(180deg,#f8fcff 0,#eef8ff 100%); border-left:4px solid var(--brand-1) }
    .material-display.active, .stand-display.active{ display:block }
    .material-info{ display:grid; grid-template-columns: repeat(3,1fr); gap:12px }
    .info-item{ background: #fff; padding:14px; border-radius:10px; box-shadow:0 4px 14px rgba(10,30,60,0.03); text-align:center }
    .info-label{ font-size:13px; color:var(--muted); margin-bottom:8px; font-weight:600 }
    .info-value{ font-size:16px; font-weight:700; color:#22303a }

    /* Action buttons improved */
    .button-group{ display:flex; gap:12px; flex-wrap:wrap }
    .btn-primary, .btn-success, .btn-secondary{ border: none; border-radius:10px; padding:12px 20px; font-weight:700; cursor:pointer }
    .btn-primary{ background:var(--brand-1); color:white; box-shadow:0 8px 24px rgba(11,95,165,0.12) }
    .btn-primary:hover{ transform:translateY(-3px) }
    .btn-success{ background:var(--success); color:white }
    .btn-secondary{ background:#8e9aa4; color:white }

    .form-actions{ display:flex; gap:12px; justify-content:center; margin-top:20px }

    /* Lists */
    .stand-item{ display:flex; justify-content:space-between; gap:12px; align-items:center; padding:12px; border-radius:10px; background:linear-gradient(180deg,#ffffff,#fbfdff); box-shadow:0 6px 18px rgba(10,30,60,0.03) }

    .btn-delete{ background:var(--danger); color:#fff; padding:8px 12px; border-radius:8px }
    .btn-print{ background:#1976d2; color:#fff; padding:8px 12px; border-radius:8px }

    /* Empty state */
    .empty-state{ padding:36px; text-align:center; color:#96a0a6 }

    /* Small helpers */
    .note { font-size:13px; color:var(--muted); }

    /* Responsive */
    @media (max-width: 900px){ .form-row{ grid-template-columns: 1fr } .material-info{ grid-template-columns:1fr } .stage-header{ flex-direction:column; text-align:center } .stage-header p{ font-size:13px } }
    @media (max-width: 480px){ .barcode-input{ font-size:16px; padding:14px } .btn-primary, .btn-success, .btn-secondary{ width:100%; padding:12px } }

    /* small animation */
    @keyframes subtlePop{ from{ transform: translateY(-6px); opacity:0 } to{ transform:none; opacity:1 } }
    .material-display.active .info-item{ animation: subtlePop .25s ease }
</style>

<div class="stage-container">
    <!-- Header -->
    <div class="stage-header">
        <h1>
            <i class="fas fa-tools"></i>
            المرحلة الأولى - تقسيم المواد على الاستاندات
        </h1>
        <p>امسح باركود المادة الخام واختر استاند متوفر لبدء التقسيم</p>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 class="section-title"><i class="fas fa-barcode"></i> مسح باركود المادة الخام</h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="materialBarcode" class="barcode-input" placeholder="امسح أو اكتب باركود المادة الخام" autofocus>
            <span class="barcode-icon"><i class="fas fa-tag"></i></span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 20px; font-size: 16px;"><i class="fas fa-lightbulb"></i> امسح الباركود أو اضغط Enter للبحث عن المادة الخام</small>
    </div>

    <!-- Material Display -->
    <div id="materialDisplay" class="material-display">
        <h4><i class="fas fa-circle-check"></i> بيانات المادة الخام</h4>
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
                <div class="info-label">الوزن المنقول للإنتاج</div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
        </div>
    </div>

    <!-- Stand Form -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-target"></i> اختيار الاستاند المتوفر</h3>

        <div class="info-box">
            <strong><i class="fas fa-thumbtack"></i> ملاحظة مهمة:</strong>
            <ul>
                <li><strong>الوزن الصافي = الوزن الإجمالي - وزن الاستاند الفارغ</strong></li>
                <li>مثال: 100 كجم إجمالي - 2 كجم وزن الاستاند = 98 كجم صافي</li>
                <li>سيتم تحويل حالة الاستاند إلى "مستخدم" تلقائياً</li>
            </ul>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="standSelect"><i class="fas fa-bullseye"></i> اختر الاستاند المتوفر <span class="required">*</span></label>
                <select id="standSelect" class="form-control" onchange="loadStand()" style="font-size: 17px; padding: 16px;">
                    <option value="">-- اختر استاند متوفر من القائمة --</option>
                </select>
                <small style="color: #7f8c8d; display: block; margin-top: 8px; font-size: 15px;"><i class="fas fa-lightbulb"></i> اختر الاستاند الذي تريد استخدامه (فقط الاستاندات الغير مستخدمة)</small>
            </div>
        </div>

        <div id="standDetails" style="display: none; margin: 30px 0; padding: 30px; background: linear-gradient(135deg, #e8f8f5 0%, #d5f4e6 100%); border-radius: 12px; border-right: 5px solid #27ae60;">
            <h4 style="margin: 0 0 25px 0; color: #27ae60; font-size: 22px; display: flex; align-items: center; gap: 12px;"><i class="fas fa-box"></i> الاستاند المختار</h4>
            <div class="stand-info" style="grid-template-columns: repeat(2, 1fr);">
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
                <label for="wasteWeight"><i class="fas fa-trash-alt"></i> وزن الهدر (كجم)</label>
                <input type="number" id="wasteWeight" class="form-control" placeholder="سيتم حسابه تلقائياً" step="0.01" oninput="calculateWastePercentage()">
                <small style="color: #7f8c8d; display: block; margin-top: 8px; font-size: 15px;"><i class="fas fa-calculator"></i> يُحسب تلقائياً: الإجمالي - الصافي - وزن الاستاند (يمكن التعديل)</small>
            </div>
            <div class="form-group">
                <label for="wastePercentage"><i class="fas fa-chart-bar"></i> نسبة الهدر (%)</label>
                <input type="number" id="wastePercentage" class="form-control" placeholder="0" step="0.01" readonly style="background: #ecf0f1;">
                <small style="color: #7f8c8d; display: block; margin-top: 8px; font-size: 15px;"><i class="fas fa-percent"></i> يُحسب تلقائياً من وزن الهدر</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="totalWeight"><i class="fas fa-weight"></i> الوزن الإجمالي (كجم) <span class="required">*</span></label>
                <input type="number" id="totalWeight" class="form-control" placeholder="أدخل الوزن الإجمالي" step="0.01" oninput="calculateNetWeight()" style="font-size: 17px;">
                <small style="color: #7f8c8d; display: block; margin-top: 8px; font-size: 15px;"><i class="fas fa-balance-scale"></i> الوزن الكلي شامل وزن الاستاند</small>
            </div>

            <div class="form-group">
                <label for="standWeight"><i class="fas fa-box-open"></i> وزن الاستاند الفارغ (كجم)</label>
                <input type="number" id="standWeight" class="form-control" placeholder="سيتم جلبه تلقائياً" step="0.01" readonly style="background: #ecf0f1; font-weight: 600;">
                <small style="color: #7f8c8d; display: block; margin-top: 8px; font-size: 15px;"><i class="fas fa-sync"></i> يتم جلبه تلقائياً من بيانات الاستاند</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="netWeight"><i class="fas fa-check"></i> الوزن الصافي (كجم) <span class="required">*</span></label>
                <input type="number" id="netWeight" class="form-control" placeholder="سيتم حسابه تلقائياً" step="0.01" readonly style="background: linear-gradient(135deg, #d5f4e6 0%, #e8f8f5 100%); font-weight: 700; font-size: 22px; text-align: center; color: #27ae60; border: 3px solid #27ae60; border-radius: 12px;">
                <small style="color: #27ae60; display: block; margin-top: 10px; font-weight: 600; font-size: 16px;"><i class="fas fa-calculator"></i> يُحسب تلقائياً: الوزن الإجمالي - وزن الاستاند الفارغ</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="notes"><i class="fas fa-sticky-note"></i> ملاحظات</label>
                <textarea id="notes" class="form-control" placeholder="ملاحظات اختيارية..." rows="4"></textarea>
                <small style="color: #7f8c8d; display: block; margin-top: 8px; font-size: 15px;"><i class="fas fa-sticky-note"></i> يمكنك إضافة أي ملاحظات إضافية هنا</small>
            </div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-primary" onclick="addProcessedStand()">
                <i class="fas fa-plus"></i> إضافة للقائمة
            </button>
            <button type="button" class="btn-secondary" onclick="clearForm()">
                <i class="fas fa-sync"></i> مسح النموذج
            </button>
        </div>
    </div>

    <!-- Processed Stands List -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-list"></i> الاستاندات المعالجة (<span id="standsCount">0</span>)</h3>
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
            <i class="fas fa-save"></i> حفظ جميع الاستاندات
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('manufacturing.stage1.index') }}'">
            <i class="fas fa-times"></i> إلغاء
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

    fetch(`/material-batches/get-by-barcode/${barcode}`, {
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
        showToast('❌ ' + error.message, 'error');
    });
}

function displayMaterialInfo(material) {
    document.getElementById('displayBarcode').textContent = material.barcode;
    document.getElementById('displayMaterialType').textContent = material.material_name || material.material_type || 'غير محدد';
    document.getElementById('displayWeight').textContent = (material.transferred_to_production || material.production_weight || 0) + ' ' + (material.unit_symbol || 'كجم');
    document.getElementById('materialDisplay').classList.add('active');
}

// Load stands from API
function loadStandsList() {
    console.log('Loading stands...');

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
                <strong><i class="fas fa-wrench"></i> ${item.stand_number}</strong>
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
                <button class="btn-print" onclick="printBarcode(${item.id})"><i class="fas fa-print"></i> طباعة</button>
                <button class="btn-delete" onclick="removeStand(${item.id})"><i class="fas fa-trash"></i> حذف</button>
            </div>
        </div>
    `).join('');
}

function removeStand(id) {
    if (confirm('هل أنت متأكد من حذف هذه البيانات؟')) {
        processedStands = processedStands.filter(s => s.id !== id);
        renderStands();
        saveOffline();
        showToast('تم حذف البيانات', 'info');
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
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';

    const formData = {
        material_id: currentMaterial.id || currentMaterial.material_id,
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
            showToast('<i class="fas fa-check-circle"></i> تم حفظ جميع الاستاندات بنجاح!', 'success');
            localStorage.removeItem('stage1_processed');
            
            // عرض قائمة الباركودات
            if (data.data && data.data.barcodes) {
                showBarcodesModal(data.data.barcodes);
            } else {
                setTimeout(() => {
                    window.location.href = '{{ route("manufacturing.stage1.index") }}';
                }, 1500);
            }
        } else {
            throw new Error(data.message || 'حدث خطأ أثناء الحفظ');
        }
    })
    .catch(error => {
        alert('❌ خطأ: ' + error.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> حفظ جميع الاستاندات';
    });
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

    let barcodesHTML = barcodes.map((item, index) => `
        <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e9 100%); padding: 30px; border-radius: 16px; margin-bottom: 25px; border-right: 5px solid #27ae60; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div style="display: grid; grid-template-columns: 1fr auto; gap: 25px; align-items: start; margin-bottom: 25px;">
                <div>
                    <h4 style="margin: 0 0 15px 0; color: #2c3e50; font-size: 22px; font-weight: 700;">
                        <i class="fas fa-box" style="color: #27ae60;"></i> استاند ${item.stand_number}
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-top: 20px;">
                        <div style="background: white; padding: 15px; border-radius: 10px; border-right: 4px solid #3498db; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                            <div style="font-size: 13px; color: #7f8c8d; margin-bottom: 8px; font-weight: 600;">المادة</div>
                            <div style="font-size: 17px; color: #2c3e50; font-weight: 700;">${item.material_name}</div>
                        </div>
                        <div style="background: white; padding: 15px; border-radius: 10px; border-right: 4px solid #e67e22; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                            <div style="font-size: 13px; color: #7f8c8d; margin-bottom: 8px; font-weight: 600;">الوزن الصافي</div>
                            <div style="font-size: 20px; color: #e67e22; font-weight: 700;">${item.net_weight} كجم</div>
                        </div>
                    </div>
                </div>
                <button onclick="printSingleBarcode('${item.barcode}', '${item.stand_number}', '${item.material_name}', ${item.net_weight})" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white; border: none; padding: 15px 30px; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 16px; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3); transition: all 0.3s;">
                    <i class="fas fa-print"></i> طباعة
                </button>
            </div>
            <div style="background: white; padding: 25px; border-radius: 12px; text-align: center; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                <svg id="barcode-${index}" style="max-width: 100%;"></svg>
                <div style="font-family: 'Courier New', monospace; font-size: 20px; font-weight: bold; color: #2c3e50; margin-top: 15px; letter-spacing: 4px; background: #f8f9fa; padding: 12px; border-radius: 8px;">
                    ${item.barcode}
                </div>
            </div>
        </div>
    `).join('');

    // حساب الإجماليات
    const totalWeight = barcodes.reduce((sum, item) => sum + parseFloat(item.net_weight), 0);
    const standsCount = barcodes.length;

    modal.innerHTML = `
        <div style="background: white; border-radius: 16px; max-width: 950px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 15px 50px rgba(0,0,0,0.3);">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 16px 16px 0 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <h2 style="margin: 0; font-size: 28px; font-weight: 700;">
                        <i class="fas fa-check-circle"></i> تم الحفظ بنجاح!
                    </h2>
                    <button onclick="closeBarcodesModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; font-size: 28px; cursor: pointer; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                        ✕
                    </button>
                </div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; padding: 20px; background: rgba(255,255,255,0.15); border-radius: 12px; backdrop-filter: blur(10px);">
                    <div style="text-align: center;">
                        <div style="font-size: 15px; opacity: 0.9; margin-bottom: 8px;">عدد الاستاندات</div>
                        <div style="font-size: 32px; font-weight: 700;">${standsCount}</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 15px; opacity: 0.9; margin-bottom: 8px;">إجمالي الوزن</div>
                        <div style="font-size: 32px; font-weight: 700;">${totalWeight.toFixed(2)} كجم</div>
                    </div>
                </div>
            </div>
            <div style="padding: 35px;">
                <h3 style="margin: 0 0 25px 0; color: #2c3e50; font-size: 22px; border-bottom: 3px solid #e9ecef; padding-bottom: 15px;">
                    <i class="fas fa-barcode"></i> الباركودات المولدة
                </h3>
                ${barcodesHTML}
                <div style="display: flex; gap: 20px; margin-top: 30px; padding-top: 25px; border-top: 3px solid #e9ecef;">
                    <button onclick="printAllBarcodes(${JSON.stringify(barcodes).replace(/"/g, '&quot;')})" style="flex: 1; background: #3498db; color: white; border: none; padding: 18px; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 18px; display: flex; align-items: center; justify-content: center; gap: 12px; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);">
                        <i class="fas fa-print"></i> طباعة الكل
                    </button>
                    <button onclick="window.location.href='{{ route('manufacturing.stage1.index') }}'" style="flex: 1; background: #27ae60; color: white; border: none; padding: 18px; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 18px; display: flex; align-items: center; justify-content: center; gap: 12px; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);">
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
            JsBarcode(`#barcode-${index}`, item.barcode, {
                format: 'CODE128',
                width: 2,
                height: 70,
                displayValue: false,
                margin: 12
            });
        });
    }, 100);
}

function closeBarcodesModal() {
    const modal = document.getElementById('barcodesModal');
    if (modal) {
        modal.remove();
    }
    window.location.href = '{{ route("manufacturing.stage1.index") }}';
}

function printSingleBarcode(barcode, standNumber, materialName, netWeight) {
    const printWindow = window.open('', '', 'height=650,width=850');
    printWindow.document.write('<html dir="rtl"><head><title>طباعة الباركود - ' + standNumber + '</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
    printWindow.document.write('.barcode-container { background: white; padding: 50px; border-radius: 16px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); text-align: center; max-width: 550px; }');
    printWindow.document.write('.title { font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 4px solid #667eea; }');
    printWindow.document.write('.stand-number { font-size: 24px; color: #667eea; font-weight: bold; margin: 20px 0; }');
    printWindow.document.write('.barcode-code { font-size: 22px; font-weight: bold; color: #2c3e50; margin: 25px 0; letter-spacing: 4px; font-family: "Courier New", monospace; }');
    printWindow.document.write('.info { margin-top: 30px; padding: 25px; background: #f8f9fa; border-radius: 10px; text-align: right; }');
    printWindow.document.write('.info-row { margin: 12px 0; display: flex; justify-content: space-between; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 16px; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 18px; }');
    printWindow.document.write('@media print { body { background: white; } }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<div class="barcode-container">');
    printWindow.document.write('<div class="title">باركود المرحلة الأولى</div>');
    printWindow.document.write('<div class="stand-number">استاند ' + standNumber + '</div>');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
    printWindow.document.write('<div class="info">');
    printWindow.document.write('<div class="info-row"><span class="label">المادة:</span><span class="value">' + materialName + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">الوزن الصافي:</span><span class="value">' + netWeight + ' كجم</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">التاريخ:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
    printWindow.document.write('</div></div>');
    printWindow.document.write('<script>');
    printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2, height: 90, displayValue: false, margin: 12 });');
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
    printWindow.document.write('<\/script></body></html>');
    printWindow.document.close();
}

function printAllBarcodes(barcodes) {
    const printWindow = window.open('', '', 'height=900,width=1100');
    printWindow.document.write('<html dir="rtl"><head><title>طباعة جميع الباركودات</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; margin: 0; padding: 30px; background: #f5f5f5; }');
    printWindow.document.write('.barcode-item { background: white; padding: 35px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 3px 12px rgba(0,0,0,0.1); page-break-inside: avoid; }');
    printWindow.document.write('.title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 3px solid #667eea; }');
    printWindow.document.write('.barcode-code { font-size: 18px; font-weight: bold; color: #2c3e50; margin: 20px 0; text-align: center; letter-spacing: 3px; font-family: "Courier New", monospace; }');
    printWindow.document.write('.info { margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 8px; }');
    printWindow.document.write('.info-row { margin: 10px 0; display: flex; justify-content: space-between; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 15px; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 16px; }');
    printWindow.document.write('@media print { body { background: white; padding: 0; } .barcode-item { box-shadow: none; page-break-after: always; } }');
    printWindow.document.write('</style></head><body>');
    
    barcodes.forEach((item, index) => {
        printWindow.document.write('<div class="barcode-item">');
        printWindow.document.write('<div class="title">باركود المرحلة الأولى - ' + item.stand_number + '</div>');
        printWindow.document.write('<div style="text-align: center;"><svg id="print-barcode-' + index + '"></svg></div>');
        printWindow.document.write('<div class="barcode-code">' + item.barcode + '</div>');
        printWindow.document.write('<div class="info">');
        printWindow.document.write('<div class="info-row"><span class="label">الاستاند:</span><span class="value">' + item.stand_number + '</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">المادة:</span><span class="value">' + item.material_name + '</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">الوزن الصافي:</span><span class="value">' + item.net_weight + ' كجم</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">التاريخ:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
        printWindow.document.write('</div></div>');
    });
    
    printWindow.document.write('<script>');
    barcodes.forEach((item, index) => {
        printWindow.document.write('JsBarcode("#print-barcode-' + index + '", "' + item.barcode + '", { format: "CODE128", width: 2, height: 80, displayValue: false, margin: 12 });');
    });
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 800); };');
    printWindow.document.write('<\/script></body></html>');
    printWindow.document.close();
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
        <div style="text-align: center; padding: 25px; font-family: Arial, sans-serif;">
            <h2 style="margin: 0 0 15px 0;">استاند مُعالج - ${stand.stand_number}</h2>
            <div style="margin: 20px 0;">
                <div style="font-size: 20px; font-weight: bold;">${stand.material_type}</div>
                <div style="font-size: 18px; margin: 8px 0;">الوزن الصافي: ${stand.net_weight} كجم</div>
                <div style="font-size: 16px; color: #666;">الباركود: ${stand.material_barcode}</div>
            </div>
            <div style="margin: 25px 0;">
                <img src="https://barcode.tec-it.com/barcode.ashx?data=${stand.material_barcode}&code=Code128&translate-esc=on" alt="Barcode">
            </div>
            <div style="font-size: 14px; color: #888;">
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
                body { margin: 0; padding: 25px; font-family: Arial, sans-serif; }
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
        top: 30px;
        right: 30px;
        background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#f39c12'};
        color: white;
        padding: 20px 30px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideIn 0.4s ease-out;
        max-width: 450px;
        font-size: 16px;
        font-weight: 500;
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'fadeOut 0.4s ease-out';
        setTimeout(() => toast.remove(), 400);
    }, 4000);
}
</script>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

@endsection