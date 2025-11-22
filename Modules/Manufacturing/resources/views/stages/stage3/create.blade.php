@extends('master')

@section('title', 'إنشاء لفائف - المرحلة الثالثة')

@section('content')

<div class="stage-container">
    <!-- Header -->
    <div class="stage-header">
        <h1>
            <span>🎨</span>
            المرحلة الثالثة - إنشاء اللفائف
        </h1>
        <p>قم بمسح باركود المرحلة الثانية وإضافة الصبغة والبلاستيك لإنشاء لفاف جديد (الوزن يزيد في هذه المرحلة)</p>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 style="margin: 0 0 15px 0; color: #9b59b6;">📷 مسح باركود المرحلة الثانية</h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="stage2Barcode" class="barcode-input" placeholder="امسح أو اكتب باركود المرحلة الثانية (ST2-XXXX)" autofocus>
            <span class="barcode-icon">📦</span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 10px;">💡 امسح الباركود أو اضغط Enter للبحث</small>
    </div>

    <!-- Stage2 Display -->
    <div id="stage2Display" class="stage2-display">
        <h4>✅ بيانات المرحلة الثانية</h4>
        <div class="stage2-info">
            <div class="info-item">
                <div class="info-label">الباركود</div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">نوع المعالجة</div>
                <div class="info-value" id="displayProcessType">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">الوزن المتبقي</div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
        </div>
    </div>

    <!-- Lafaf Form -->
    <div class="form-section">
        <h3 class="section-title">📝 بيانات اللفاف</h3>

        <div class="info-box">
            <strong>📌 ملاحظة هامة:</strong>
            <ul>
                <li><strong>الوزن يزيد</strong> في هذه المرحلة (إضافة الصبغة والبلاستيك)</li>
                <li>أدخل الوزن الكامل الشامل (وزن المرحلة السابقة + الصبغة + البلاستيك)</li>
                <li>سيتم حساب الوزن المضاف تلقائياً = الوزن الكامل - الوزن السابق</li>
                <li>لا يوجد هدر في هذه المرحلة</li>
            </ul>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>وزن الدخول من المرحلة السابقة (كجم)</label>
                <input type="number" id="inputWeight" class="form-control" readonly style="background: #ecf0f1; font-weight: 600;">
            </div>

            <div class="form-group">
                <label>الوزن الكامل الشامل (كجم) <span class="required">*</span></label>
                <input type="number" id="totalWeight" class="form-control" placeholder="مثال: 105.50" step="0.01">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">شامل وزن الدخول + الصبغة + البلاستيك</small>
            </div>
        </div>

            <div class="form-group">
                <label>الوزن المضاف (كجم)</label>
                <input type="number" id="addedWeight" class="form-control" readonly style="background: #e8f5e9; font-weight: 600; color: #27ae60;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">يتم الحساب تلقائياً</small>
            </div>
        </div>

        <!-- Weight Calculation Display -->
        <div id="weightCalcDisplay" class="weight-calc-display">
            <h5>🔢 حساب الوزن المضاف</h5>
            <div class="calc-formula">
                الوزن المضاف = الوزن الكامل الشامل - وزن الدخول<br>
                الوزن المضاف = <span id="calcTotal">0</span> - <span id="calcInput">0</span><br>
            </div>
            <div class="calc-result">
                ✅ الوزن المضاف (صبغة + بلاستيك) = <span id="calcAdded">0</span> كجم
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>اللون <span class="required">*</span></label>
                <input type="text" id="color" class="form-control" placeholder="مثال: أحمر، أزرق، أخضر...">
            </div>

            <div class="form-group">
                <label>نوع البلاستيك</label>
                <input type="text" id="plasticType" class="form-control" placeholder="مثال: PE، PP، PVC...">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>ملاحظات</label>
                <textarea id="notes" class="form-control" placeholder="أضف أي ملاحظات إضافية..."></textarea>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="form-actions">
        <button type="button" class="btn-success" onclick="submitLafaf()" id="submitBtn" disabled>
            ✅ حفظ اللفاف
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('manufacturing.stage3.index') }}'">
            ❌ إلغاء
        </button>
    </div>
</div>

<script>
let currentStage2 = null;

// Barcode scanner
document.getElementById('stage2Barcode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        loadStage2(this.value.trim());
    }
});

// Auto-calculate added weight
document.getElementById('totalWeight').addEventListener('input', calculateAddedWeight);

function loadStage2(barcode) {
    if (!barcode) {
        showToast('⚠️ يرجى إدخال باركود المرحلة الثانية!', 'error');
        return;
    }

    // Show loading
    const barcodeInput = document.getElementById('stage2Barcode');
    barcodeInput.disabled = true;

    // Call API
    fetch(`/stage3/get-stage2-by-barcode/${barcode}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentStage2 = data.data;
            displayStage2Data();
            showToast('✅ تم تحميل بيانات المرحلة الثانية بنجاح!', 'success');
        } else {
            throw new Error(data.message || 'لم يتم العثور على الباركود');
        }
    })
    .catch(error => {
        showToast('❌ ' + error.message, 'error');
        currentStage2 = null;
        document.getElementById('stage2Display').classList.remove('active');
    })
    .finally(() => {
        barcodeInput.disabled = false;
    });
}

function displayStage2Data() {
    // Display stage2 info
    document.getElementById('displayBarcode').textContent = currentStage2.barcode;
    document.getElementById('displayProcessType').textContent = currentStage2.process_details || 'معالجة';

    const weight = currentStage2.remaining_weight || currentStage2.output_weight || 0;
    document.getElementById('displayWeight').textContent = weight + ' كجم';
    document.getElementById('stage2Display').classList.add('active');

    // Fill input weight
    document.getElementById('inputWeight').value = weight;

    // Enable submit button
    document.getElementById('submitBtn').disabled = false;

    // Focus on total weight
    document.getElementById('totalWeight').focus();
}

function calculateAddedWeight() {
    const inputWeight = parseFloat(document.getElementById('inputWeight').value) || 0;
    const totalWeight = parseFloat(document.getElementById('totalWeight').value) || 0;

    if (totalWeight > 0 && inputWeight > 0) {
        const addedWeight = totalWeight - inputWeight;

        if (addedWeight < 0) {
            showToast('⚠️ الوزن الكامل يجب أن يكون أكبر من وزن الدخول!', 'error');
            document.getElementById('addedWeight').value = '';
            document.getElementById('weightCalcDisplay').classList.remove('active');
            return;
        }

        document.getElementById('addedWeight').value = addedWeight.toFixed(3);

        // Show calculation
        document.getElementById('calcTotal').textContent = totalWeight.toFixed(3);
        document.getElementById('calcInput').textContent = inputWeight.toFixed(3);
        document.getElementById('calcAdded').textContent = addedWeight.toFixed(3);
        document.getElementById('weightCalcDisplay').classList.add('active');
    } else {
        document.getElementById('addedWeight').value = '';
        document.getElementById('weightCalcDisplay').classList.remove('active');
    }
}

function submitLafaf() {
    if (!currentStage2) {
        showToast('⚠️ يرجى مسح باركود المرحلة الثانية أولاً!', 'error');
        return;
    }

    const totalWeight = document.getElementById('totalWeight').value;
    const color = document.getElementById('color').value.trim();
    const plasticType = document.getElementById('plasticType').value.trim();
    const notes = document.getElementById('notes').value.trim();

    if (!totalWeight || !color) {
        showToast('⚠️ يرجى ملء جميع الحقول المطلوبة!', 'error');
        return;
    }

    const inputWeight = parseFloat(document.getElementById('inputWeight').value) || 0;
    const totalWeightNum = parseFloat(totalWeight);

    if (totalWeightNum <= inputWeight) {
        showToast('⚠️ الوزن الكامل يجب أن يكون أكبر من وزن الدخول!', 'error');
        return;
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ جاري الحفظ...';

    // Prepare data
    const formData = {
        stage2_barcode: currentStage2.barcode,
        total_weight: totalWeightNum,
        color: color,
        plastic_type: plasticType,
        notes: notes,
        _token: '{{ csrf_token() }}'
    };

    // Submit via AJAX
    fetch('{{ route("manufacturing.stage3.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('✅ تم حفظ اللفاف بنجاح! الباركود: ' + data.data.barcode, 'success');

            // عرض نافذة الباركود مثل باقي المراحل
            if (data.data && data.data.barcode_info) {
                showBarcodesModal([data.data.barcode_info]);
            } else {
                setTimeout(() => {
                    window.location.href = '{{ route("manufacturing.stage3.index") }}';
                }, 2000);
            }
        } else {
            throw new Error(data.message || 'حدث خطأ أثناء الحفظ');
        }
    })
    .catch(error => {
        showToast('❌ خطأ: ' + error.message, 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '✅ حفظ اللفاف';
    });
}

function showToast(message, type = 'info') {
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
        max-width: 400px;
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// عرض نافذة الباركود
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
        <div style="background: linear-gradient(135deg, #f8f9fa 0%, #fce4ec 100%); padding: 25px; border-radius: 12px; margin-bottom: 20px; border-right: 5px solid #9b59b6; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: start; margin-bottom: 20px;">
                <div>
                    <h4 style="margin: 0 0 12px 0; color: #2c3e50; font-size: 20px; font-weight: 700;">
                        <i class="fas fa-circle" style="color: #9b59b6;"></i> لفاف - المرحلة 3
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 15px;">
                        <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #9b59b6;">
                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">الوزن الكلي</div>
                            <div style="font-size: 18px; color: #9b59b6; font-weight: 700;">${item.total_weight || item.weight} كجم</div>
                        </div>
                        <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #3498db;">
                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">الوزن المضاف</div>
                            <div style="font-size: 16px; color: #3498db; font-weight: 700;">${item.added_weight || 0} كجم</div>
                        </div>
                        <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #e67e22;">
                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">اللون</div>
                            <div style="font-size: 14px; color: #e67e22; font-weight: 700;">${item.color || '-'}</div>
                        </div>
                        <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #27ae60;">
                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">البلاستيك</div>
                            <div style="font-size: 14px; color: #2c3e50; font-weight: 700;">${item.plastic_type || '-'}</div>
                        </div>
                    </div>
                </div>
                <button onclick="printStage3Barcode('${item.barcode}', '${item.total_weight || item.weight}', '${item.color}')" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 8px; box-shadow: 0 3px 10px rgba(155, 89, 182, 0.3); transition: all 0.3s;">
                    <i class="fas fa-print"></i> طباعة
                </button>
            </div>
            <div style="background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                <svg id="barcode-stage3-${index}" style="max-width: 100%;"></svg>
                <div style="font-family: 'Courier New', monospace; font-size: 18px; font-weight: bold; color: #2c3e50; margin-top: 12px; letter-spacing: 3px; background: #f8f9fa; padding: 10px; border-radius: 6px;">
                    ${item.barcode}
                </div>
            </div>
        </div>
    `).join('');

    modal.innerHTML = `
        <div style="background: white; border-radius: 12px; max-width: 900px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
            <div style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white; padding: 25px; border-radius: 12px 12px 0 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 24px; font-weight: 700;">
                        <i class="fas fa-check-circle"></i> تم إنتاج اللفاف بنجاح!
                    </h2>
                    <button onclick="closeBarcodesModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; font-size: 24px; cursor: pointer; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                        ✕
                    </button>
                </div>
            </div>
            <div style="padding: 30px;">
                <h3 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 18px; border-bottom: 2px solid #e9ecef; padding-bottom: 12px;">
                    <i class="fas fa-barcode"></i> الباركود المولد
                </h3>
                ${barcodesHTML}
                <div style="display: flex; gap: 15px; margin-top: 25px; padding-top: 20px; border-top: 2px solid #e9ecef;">
                    <button onclick="window.location.href='{{ route('manufacturing.stage3.index') }}'" style="flex: 1; background: #27ae60; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 10px;">
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
            JsBarcode(`#barcode-stage3-${index}`, item.barcode, {
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
    window.location.href = '{{ route("manufacturing.stage3.index") }}';
}

function printStage3Barcode(barcode, weight, color) {
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html dir="rtl"><head><title>طباعة الباركود - المرحلة الثالثة</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
    printWindow.document.write('.barcode-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); text-align: center; max-width: 500px; }');
    printWindow.document.write('.title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 3px solid #9b59b6; }');
    printWindow.document.write('.barcode-code { font-size: 18px; font-weight: bold; color: #2c3e50; margin: 20px 0; letter-spacing: 3px; font-family: "Courier New", monospace; }');
    printWindow.document.write('.info { margin-top: 25px; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: right; }');
    printWindow.document.write('.info-row { margin: 10px 0; display: flex; justify-content: space-between; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 14px; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 16px; }');
    printWindow.document.write('@media print { body { background: white; } }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<div class="barcode-container">');
    printWindow.document.write('<div class="title">باركود اللفاف - المرحلة الثالثة</div>');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
    printWindow.document.write('<div class="info">');
    printWindow.document.write('<div class="info-row"><span class="label">الوزن:</span><span class="value">' + weight + ' كجم</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">اللون:</span><span class="value">' + color + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">التاريخ:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
    printWindow.document.write('</div></div>');
    printWindow.document.write('<script>');
    printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2, height: 80, displayValue: false, margin: 10 });');
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
    printWindow.document.write('<\/script></body></html>');
    printWindow.document.close();
}

</script>

@endsection

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
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
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
        box-shadow: 0 6px 16px rgba(52, 152, 219, 0.3);
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
        <h3 style="margin: 0 0 15px 0; color: #3498db;">📷 مسح باركود الاستاند المعالج</h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="processedBarcode" class="barcode-input" placeholder="امسح أو اكتب باركود الاستاند المعالج (ST2-XXX-2025)" autofocus>
            <span class="barcode-icon">📦</span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 10px;">💡 امسح الباركود أو اضغط Enter للبحث</small>
    </div>

    <!-- Processed Display -->
    <div id="processedDisplay" class="processed-display">
        <h4>✅ بيانات الاستاند المعالج</h4>
        <div class="processed-info">
            <div class="info-item">
                <div class="info-label">الباركود</div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">نوع المعالجة</div>
                <div class="info-value" id="displayProcessType">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">الوزن النهائي</div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
        </div>
    </div>

    <!-- Coil Form -->
    <div class="form-section">
        <h3 class="section-title">📝 بيانات الكويل</h3>

        <div class="info-box">
            <strong>📌 ملاحظة هامة:</strong>
            <ul>
                <li>المعادلة: الوزن الإجمالي = وزن الأساس + وزن الصبغة + وزن البلاستيك</li>
                <li>كمية الهدر = الوزن الإجمالي المتوقع - الوزن الإجمالي الفعلي</li>
                <li>نسبة الهدر الافتراضية: 5%</li>
            </ul>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>رقم الكويل <span class="required">*</span></label>
                <input type="text" id="coilNumber" class="form-control" placeholder="CO3-001-2025">
            </div>

            <div class="form-group">
                <label>وزن الأساس (كجم) <span class="required">*</span></label>
                <input type="number" id="baseWeight" class="form-control" placeholder="95.00" step="0.01" readonly>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>لون الصبغة <span class="required">*</span></label>
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
                <label>وزن الصبغة (كجم) <span class="required">*</span></label>
                <input type="number" id="dyeWeight" class="form-control" placeholder="2.00" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>نوع البلاستيك <span class="required">*</span></label>
                <select id="plasticType" class="form-control">
                    <option value="">اختر النوع</option>
                    <option value="pe">بولي إيثيلين (PE)</option>
                    <option value="pp">بولي بروبيلين (PP)</option>
                    <option value="pvc">بولي فينيل كلورايد (PVC)</option>
                    <option value="pet">بولي إيثيلين تيرفثالات (PET)</option>
                </select>
            </div>

            <div class="form-group">
                <label>وزن البلاستيك (كجم) <span class="required">*</span></label>
                <input type="number" id="plasticWeight" class="form-control" placeholder="3.00" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>الوزن الإجمالي المتوقع (كجم)</label>
                <input type="number" id="expectedWeight" class="form-control" readonly style="background: #e8f4f8; font-weight: 600;">
            </div>

            <div class="form-group">
                <label>الوزن الإجمالي الفعلي (كجم) <span class="required">*</span></label>
                <input type="number" id="totalWeight" class="form-control" placeholder="100.00" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>كمية الهدر (كجم)</label>
                <input type="number" id="wasteAmount" class="form-control" readonly>
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">نسبة الهدر: <span id="wastePercentDisplay">0%</span></small>
            </div>

            <div class="form-group">
                <label>التكلفة (ريال) <span class="required">*</span></label>
                <input type="number" id="cost" class="form-control" placeholder="500.00" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>ملاحظات</label>
                <textarea id="notes" class="form-control" placeholder="أضف أي ملاحظات إضافية..."></textarea>
            </div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-primary" onclick="addCoil()">
                ➕ إضافة الكويل
            </button>
            <button type="button" class="btn-secondary" onclick="clearForm()">
                🔄 مسح النموذج
            </button>
        </div>
    </div>

    <!-- Coils List -->
    <div class="form-section">
        <h3 class="section-title">📋 قائمة الكويلات المضافة (<span id="coilCount">0</span>)</h3>
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
            ✅ حفظ جميع الكويلات
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('manufacturing.stage3.index') }}'">
            ❌ إلغاء
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

    showToast('✅ تم إضافة الكويل بنجاح!', 'success');
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
            <button class="btn-delete" onclick="removeCoil(${coil.id})">🗑️ حذف</button>
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
    submitBtn.innerHTML = '⏳ جاري الحفظ...';

    // إرسال كل كويل على حدة وجمع البيانات
    let completed = 0;
    const total = coils.length;
    const barcodesData = [];

    coils.forEach((coil, index) => {
        fetch('{{ route("manufacturing.stage3.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(coil)
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
                    showToast('✅ تم حفظ جميع اللفائف بنجاح!', 'success');
                    localStorage.removeItem('stage3_coils');

                    // عرض نافذة الباركودات
                    if (barcodesData.length > 0) {
                        showBarcodesModal(barcodesData);
                    } else {
                        setTimeout(() => {
                            window.location.href = '{{ route("manufacturing.stage3.index") }}';
                        }, 1500);
                    }
                }
            } else {
                throw new Error(data.message || 'حدث خطأ أثناء الحفظ');
            }
        })
        .catch(error => {
            alert('❌ خطأ في اللفاف ' + (index + 1) + ': ' + error.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '✅ حفظ جميع اللفائف';
        });
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
    const totalWeight = barcodes.reduce((sum, item) => sum + parseFloat(item.total_weight), 0);
    const totalAddedWeight = barcodes.reduce((sum, item) => sum + parseFloat(item.added_weight || 0), 0);
    const coilsCount = barcodes.length;

    let barcodesHTML = barcodes.map((item, index) => `
        <div style="background: linear-gradient(135deg, #f8f9fa 0%, #fce4ec 100%); padding: 25px; border-radius: 12px; margin-bottom: 20px; border-right: 5px solid #9b59b6; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: start; margin-bottom: 20px;">
                <div>
                    <h4 style="margin: 0 0 12px 0; color: #2c3e50; font-size: 20px; font-weight: 700;">
                        <i class="fas fa-circle" style="color: #9b59b6;"></i> ${item.coil_number}
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 15px;">
                        <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #27ae60;">
                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">المادة</div>
                            <div style="font-size: 14px; color: #2c3e50; font-weight: 700;">${item.material_name}</div>
                        </div>
                        <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #9b59b6;">
                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">الوزن الكلي</div>
                            <div style="font-size: 18px; color: #9b59b6; font-weight: 700;">${item.total_weight} كجم</div>
                        </div>
                        <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #3498db;">
                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">الوزن المضاف</div>
                            <div style="font-size: 16px; color: #3498db; font-weight: 700;">${item.added_weight} كجم</div>
                        </div>
                        <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #e67e22;">
                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">اللون</div>
                            <div style="font-size: 14px; color: #e67e22; font-weight: 700;">${item.color}</div>
                        </div>
                    </div>
                </div>
                <button onclick="printStage3Barcode('${item.barcode}', '${item.coil_number}', '${item.material_name}', ${item.total_weight}, '${item.color}')" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 8px; box-shadow: 0 3px 10px rgba(155, 89, 182, 0.3); transition: all 0.3s;">
                    <i class="fas fa-print"></i> طباعة
                </button>
            </div>
            <div style="background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                <svg id="barcode-stage3-${index}" style="max-width: 100%;"></svg>
                <div style="font-family: 'Courier New', monospace; font-size: 18px; font-weight: bold; color: #2c3e50; margin-top: 12px; letter-spacing: 3px; background: #f8f9fa; padding: 10px; border-radius: 6px;">
                    ${item.barcode}
                </div>
            </div>
        </div>
    `).join('');

    modal.innerHTML = `
        <div style="background: white; border-radius: 12px; max-width: 900px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
            <div style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white; padding: 25px; border-radius: 12px 12px 0 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 24px; font-weight: 700;">
                        <i class="fas fa-check-circle"></i> تم إنتاج اللفائف بنجاح!
                    </h2>
                    <button onclick="closeBarcodesModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; font-size: 24px; cursor: pointer; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                        ✕
                    </button>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; padding: 15px; background: rgba(255,255,255,0.15); border-radius: 10px; backdrop-filter: blur(10px);">
                    <div style="text-align: center;">
                        <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">عدد اللفائف</div>
                        <div style="font-size: 28px; font-weight: 700;">${coilsCount}</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">إجمالي الوزن</div>
                        <div style="font-size: 28px; font-weight: 700;">${totalWeight.toFixed(2)} كجم</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">الوزن المضاف</div>
                        <div style="font-size: 28px; font-weight: 700;">${totalAddedWeight.toFixed(2)} كجم</div>
                    </div>
                </div>
            </div>
            <div style="padding: 30px;">
                <h3 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 18px; border-bottom: 2px solid #e9ecef; padding-bottom: 12px;">
                    <i class="fas fa-barcode"></i> الباركودات المولدة
                </h3>
                ${barcodesHTML}
                <div style="display: flex; gap: 15px; margin-top: 25px; padding-top: 20px; border-top: 2px solid #e9ecef;">
                    <button onclick="printAllStage3Barcodes(${JSON.stringify(barcodes).replace(/"/g, '&quot;')})" style="flex: 1; background: #9b59b6; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <i class="fas fa-print"></i> طباعة الكل
                    </button>
                    <button onclick="window.location.href='{{ route('manufacturing.stage3.index') }}'" style="flex: 1; background: #27ae60; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 10px;">
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
            JsBarcode(`#barcode-stage3-${index}`, item.barcode, {
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
    window.location.href = '{{ route("manufacturing.stage3.index") }}';
}

function printStage3Barcode(barcode, coilNumber, materialName, totalWeight, color) {
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html dir="rtl"><head><title>طباعة الباركود - المرحلة الثالثة</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
    printWindow.document.write('.barcode-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); text-align: center; max-width: 500px; }');
    printWindow.document.write('.title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 3px solid #9b59b6; }');
    printWindow.document.write('.coil-number { font-size: 20px; color: #9b59b6; font-weight: bold; margin: 15px 0; }');
    printWindow.document.write('.barcode-code { font-size: 18px; font-weight: bold; color: #2c3e50; margin: 20px 0; letter-spacing: 3px; font-family: "Courier New", monospace; }');
    printWindow.document.write('.info { margin-top: 25px; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: right; }');
    printWindow.document.write('.info-row { margin: 10px 0; display: flex; justify-content: space-between; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 14px; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 16px; }');
    printWindow.document.write('@media print { body { background: white; } }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<div class="barcode-container">');
    printWindow.document.write('<div class="title">باركود اللفاف - المرحلة الثالثة</div>');
    printWindow.document.write('<div class="coil-number">' + coilNumber + '</div>');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
    printWindow.document.write('<div class="info">');
    printWindow.document.write('<div class="info-row"><span class="label">المادة:</span><span class="value">' + materialName + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">الوزن الكلي:</span><span class="value">' + totalWeight + ' كجم</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">اللون:</span><span class="value">' + color + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">التاريخ:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
    printWindow.document.write('</div></div>');
    printWindow.document.write('<script>');
    printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2, height: 80, displayValue: false, margin: 10 });');
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
    printWindow.document.write('<\/script></body></html>');
    printWindow.document.close();
}

function printAllStage3Barcodes(barcodes) {
    const printWindow = window.open('', '', 'height=800,width=1000');
    printWindow.document.write('<html dir="rtl"><head><title>طباعة جميع الباركودات - المرحلة الثالثة</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }');
    printWindow.document.write('.barcode-item { background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); page-break-inside: avoid; }');
    printWindow.document.write('.title { font-size: 20px; font-weight: bold; color: #2c3e50; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #9b59b6; }');
    printWindow.document.write('.barcode-code { font-size: 16px; font-weight: bold; color: #2c3e50; margin: 15px 0; text-align: center; letter-spacing: 2px; font-family: "Courier New", monospace; }');
    printWindow.document.write('.info { margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 6px; }');
    printWindow.document.write('.info-row { margin: 8px 0; display: flex; justify-content: space-between; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 13px; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 14px; }');
    printWindow.document.write('@media print { body { background: white; padding: 0; } .barcode-item { box-shadow: none; page-break-after: always; } }');
    printWindow.document.write('</style></head><body>');

    barcodes.forEach((item, index) => {
        printWindow.document.write('<div class="barcode-item">');
        printWindow.document.write('<div class="title">باركود اللفاف - ' + item.coil_number + '</div>');
        printWindow.document.write('<div style="text-align: center;"><svg id="print-barcode-' + index + '"></svg></div>');
        printWindow.document.write('<div class="barcode-code">' + item.barcode + '</div>');
        printWindow.document.write('<div class="info">');
        printWindow.document.write('<div class="info-row"><span class="label">اللفاف:</span><span class="value">' + item.coil_number + '</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">المادة:</span><span class="value">' + item.material_name + '</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">الوزن الكلي:</span><span class="value">' + item.total_weight + ' كجم</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">الوزن المضاف:</span><span class="value">' + item.added_weight + ' كجم</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">اللون:</span><span class="value">' + item.color + '</span></div>');
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
