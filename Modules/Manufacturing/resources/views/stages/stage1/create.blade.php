@extends('master')

@section('title', 'المرحلة الأولى - تقسيم المواد')

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
            <i class="fas fa-tools"></i>
            المرحلة الأولى - تقسيم المواد على الاستاندات
        </h1>
        <p>امسح باركود المادة الخام واختر استاند متوفر لبدء التقسيم</p>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 style="margin: 0 0 20px 0; color: #0066B2; font-size: 22px;"><i class="fas fa-barcode"></i> مسح باركود المادة الخام</h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="materialBarcode" class="barcode-input" placeholder="امسح أو اكتب باركود المادة الخام" autofocus>
            <span class="barcode-icon"><i class="fas fa-tag" title="أيقونة تشير إلى حقل إدخال الباركود"></i></span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 15px; font-size: 15px;"><i class="fas fa-lightbulb"></i> امسح الباركود أو اضغط Enter للبحث عن المادة الخام</small>
    </div>

    <!-- Material Display -->
    <div id="materialDisplay" class="material-display">
        <h4><i class="fas fa-circle-check"></i> بيانات المادة الخام</h4>
        <div class="material-info">
            <div class="info-item">
                <div class="info-label">الباركود <span class="info-tooltip">?<span class="tooltip-text">الرمز الشريطي الفريد للمادة الخام</span></span></div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">نوع المادة <span class="info-tooltip">?<span class="tooltip-text">تصنيف المادة الخام المستخدمة في الإنتاج</span></span></div>
                <div class="info-value" id="displayMaterialType">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">الوزن المتبقي <span class="info-tooltip">?<span class="tooltip-text">الكمية المتاحة من المادة الخام بالكيلوغرام</span></span></div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
        </div>
    </div>

    <!-- Stand Form -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-target"></i> اختيار الاستاند المتوفر</h3>

        <div class="info-box">
            <div class="info-box-header">
                <strong><i class="fas fa-thumbtack"></i> ملاحظة: <span class="info-tooltip">?<span class="tooltip-text"><strong>معلومات هامة عن تقسيم المواد:</strong><br><br>• الوزن الصافي = الوزن الإجمالي - وزن الاستاند الفارغ<br><br>• مثال: 100 كجم إجمالي - 2 كجم وزن الاستاند = 98 كجم صافي<br><br>• سيتم تحويل حالة الاستاند إلى "مستخدم" تلقائياً</span></span></strong>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="standSelect"><i class="fas fa-bullseye"></i> اختر الاستاند المتوفر <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">اختر الاستاند الفارغ الذي سيتم استخدامه لتقسيم المادة الخام</span></span></label>
                <select id="standSelect" class="form-control" onchange="loadStand()" style="font-size: 16px; padding: 14px;">
                    <option value="">-- اختر استاند متوفر من القائمة --</option>
                </select>
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-lightbulb"></i> اختر الاستاند الذي تريد استخدامه (فقط الاستاندات الغير مستخدمة)</small>
            </div>
        </div>

        <div id="standDetails" style="display: none; margin: 25px 0; padding: 25px; background: linear-gradient(135deg, #e8f8f5 0%, #d5f4e6 100%); border-radius: 10px; border-right: 4px solid #27ae60;">
            <h4 style="margin: 0 0 20px 0; color: #27ae60; font-size: 18px; display: flex; align-items: center; gap: 10px;"><i class="fas fa-box"></i> الاستاند المختار</h4>
            <div class="material-info" style="grid-template-columns: repeat(2, 1fr);">
                <div class="info-item">
                    <div class="info-label">رقم الاستاند <span class="info-tooltip">?<span class="tooltip-text">الرقم التعريفي الفريد للاستند</span></span></div>
                    <div class="info-value" id="selectedStandNumber" style="color: #27ae60; font-weight: 700;">-</div>
                </div>
                <div class="info-item">
                    <div class="info-label">وزن الاستاند الفارغ <span class="info-tooltip">?<span class="tooltip-text">الوزن الأساسي للاستاند بدون المادة الخام</span></span></div>
                    <div class="info-value" id="selectedStandWeight" style="color: #e67e22; font-weight: 700;">-</div>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="wasteWeight"><i class="fas fa-trash-alt"></i> وزن الهدر (كجم) <span class="info-tooltip">?<span class="tooltip-text">الكمية المفقودة أثناء عملية التقسيم</span></span></label>
                <input type="number" id="wasteWeight" class="form-control" placeholder="سيتم حسابه تلقائياً" step="0.01" oninput="calculateWastePercentage()">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-calculator"></i> <span class="info-tooltip">?<span class="tooltip-text">يُحسب تلقائياً: الإجمالي - الصافي - وزن الاستاند (يمكن التعديل)</span></span></small>
            </div>
            <div class="form-group">
                <label for="wastePercentage"><i class="fas fa-chart-bar"></i> نسبة الهدر (%) <span class="info-tooltip">?<span class="tooltip-text">النسبة المئوية للهدر من الوزن الإجمالي</span></span></label>
                <input type="number" id="wastePercentage" class="form-control" placeholder="0" step="0.01" readonly style="background: #ecf0f1;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-percent"></i> <span class="info-tooltip">?<span class="tooltip-text">يُحسب تلقائياً من وزن الهدر</span></span></small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="totalWeight"><i class="fas fa-weight"></i> الوزن الإجمالي (كجم) <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">الوزن الكلي للستاند مع المادة الخام</span></span></label>
                <input type="number" id="totalWeight" class="form-control" placeholder="أدخل الوزن الإجمالي" step="0.01" oninput="calculateNetWeight()" style="font-size: 16px;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-balance-scale"></i> <span class="info-tooltip">?<span class="tooltip-text">الوزن الكلي شامل وزن الاستاند</span></span></small>
            </div>

            <div class="form-group">
                <label for="standWeight"><i class="fas fa-box-open"></i> وزن الاستاند الفارغ (كجم) <span class="info-tooltip">?<span class="tooltip-text">الوزن الأساسي للاستاند بدون المادة</span></span></label>
                <input type="number" id="standWeight" class="form-control" placeholder="سيتم جلبه تلقائياً" step="0.01" readonly style="background: #ecf0f1; font-weight: 600;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-sync"></i> <span class="info-tooltip">?<span class="tooltip-text">يتم جلبه تلقائياً من بيانات الاستاند</span></span></small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="netWeight"><i class="fas fa-check"></i> الوزن الصافي (كجم) <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">الوزن الفعلي للمادة الخام بدون وزن الاستاند</span></span></label>
                <input type="number" id="netWeight" class="form-control" placeholder="سيتم حسابه تلقائياً" step="0.01" readonly style="background: linear-gradient(135deg, #d5f4e6 0%, #e8f8f5 100%); font-weight: 700; font-size: 20px; text-align: center; color: #27ae60; border: 2px solid #27ae60;">
                <small style="color: #27ae60; display: block; margin-top: 8px; font-weight: 600; font-size: 15px;"><i class="fas fa-calculator"></i> <span class="info-tooltip">?<span class="tooltip-text">يُحسب تلقائياً: الوزن الإجمالي - وزن الاستاند الفارغ</span></span></small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="notes">ملاحظات <span class="info-tooltip">?<span class="tooltip-text">ملاحظات إضافية حول عملية التقسيم</span></span></label>
                <textarea id="notes" class="form-control" placeholder="ملاحظات اختيارية..." rows="3"></textarea>
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-sticky-note"></i> <span class="info-tooltip">?<span class="tooltip-text">يمكنك إضافة أي ملاحظات إضافية هنا</span></span></small>
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

    // TODO: Replace with actual API call
    fetch(`/warehouse-products/get-by-barcode/${barcode}`, {
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
            showToast('<i class="fas fa-check-circle"></i> تم حفظ جميع الاستاندات بنجاح!', 'success');
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
        background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#f39c12'};
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
