@extends('master')

@section('title', 'تعبئة الكراتين - المرحلة الرابعة')

@section('content')
<style>
    .stage-container {
        max-width: 1100px;
        margin: 20px auto;
        padding: 0 15px;
    }

    .stage-header {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
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

    .form-section {
        background: white;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 20px;
        border: 1px solid #e8e8e8;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 20px 0;
        padding-bottom: 12px;
        border-bottom: 2px solid #e74c3c;
    }

    .barcode-section {
        background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border: 2px dashed #e74c3c;
    }

    .barcode-input-wrapper {
        position: relative;
    }

    .barcode-input {
        width: 100%;
        padding: 15px 50px 15px 15px;
        font-size: 16px;
        border: 2px solid #e74c3c;
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
        color: #e74c3c;
    }

    .lafaf-display {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border-right: 4px solid #27ae60;
        display: none;
    }

    .lafaf-display.active {
        display: block;
        animation: slideIn 0.3s ease-out;
    }

    .lafaf-display h4 {
        color: #27ae60;
        margin: 0 0 10px 0;
        font-size: 16px;
    }

    .lafaf-info {
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
        color: #2c3e50;
    }

    .box-item {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 12px;
        border-right: 4px solid #e74c3c;
        animation: slideIn 0.3s ease-out;
    }

    .box-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .box-number {
        font-size: 16px;
        font-weight: 600;
        color: #e74c3c;
    }

    .box-form {
        display: grid;
        grid-template-columns: 2fr 3fr 80px;
        gap: 10px;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 500;
        color: #34495e;
        margin-bottom: 6px;
    }

    .form-control {
        padding: 10px 12px;
        border: 1px solid #dce4ec;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.3s;
        background: white;
    }

    .form-control:focus {
        outline: none;
        border-color: #e74c3c;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .btn-delete {
        background: #95a5a6;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.3s;
    }

    .btn-delete:hover {
        background: #7f8c8d;
    }

    .summary-box {
        background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
        padding: 15px;
        border-radius: 8px;
        border-right: 4px solid #f39c12;
        margin: 20px 0;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .summary-row:last-child {
        margin-bottom: 0;
        font-size: 16px;
        font-weight: 600;
        color: #e67e22;
        padding-top: 8px;
        border-top: 2px dashed rgba(0,0,0,0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
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
        box-shadow: 0 6px 16px rgba(231, 76, 60, 0.3);
    }

    .btn-success {
        background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        color: white;
        border: none;
        padding: 14px 32px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
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

    .info-box {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        border-right: 4px solid #27ae60;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .info-box strong {
        color: #27ae60;
        display: block;
        margin-bottom: 8px;
    }

    .info-box ul {
        margin: 8px 0 0 20px;
        color: #555;
        font-size: 13px;
    }

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

    @media (max-width: 768px) {
        .lafaf-info {
            grid-template-columns: repeat(2, 1fr);
        }
        .box-form {
            grid-template-columns: 1fr;
        }
        .form-actions {
            flex-direction: column;
        }
    }
</style>

<div class="stage-container">
    <div class="stage-header">
        <h1>
            <span>📦</span>
            المرحلة الرابعة - تعبئة الكراتين
        </h1>
        <p>قم بمسح باركود اللفاف وتقسيمه على الكراتين (كل كرتون سيحصل على باركود خاص)</p>
    </div>

    <div class="form-section barcode-section">
        <h3 style="margin: 0 0 15px 0; color: #e74c3c;">📷 مسح باركود اللفاف</h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="lafafBarcode" class="barcode-input" placeholder="امسح أو اكتب باركود اللفاف (CO3-XXXX)" autofocus>
            <span class="barcode-icon">📦</span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 10px;">💡 امسح الباركود أو اضغط Enter للبحث</small>
    </div>

    <div id="lafafDisplay" class="lafaf-display">
        <h4>✅ بيانات اللفاف</h4>
        <div class="lafaf-info">
            <div class="info-item">
                <div class="info-label">الباركود</div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">اللون</div>
                <div class="info-value" id="displayColor">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">الوزن الكامل</div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">نوع البلاستيك</div>
                <div class="info-value" id="displayPlastic">-</div>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3 class="section-title">📦 تقسيم الكراتين</h3>

        <div class="info-box">
            <strong>📌 ملاحظة هامة:</strong>
            <ul>
                <li>مجموع أوزان الكراتين يجب أن يساوي وزن اللفاف تقريباً (تسامح 2%)</li>
                <li>كل كرتون سيحصل على باركود خاص (BOX4-XXXX)</li>
                <li>يمكنك تتبع كل كرتون بشكل منفصل من خلال صفحة التتبع</li>
            </ul>
        </div>

        <div id="boxesList"></div>

        <div class="button-group">
            <button type="button" class="btn-primary" onclick="addBox()">
                ➕ إضافة كرتون
            </button>
        </div>

        <div id="summaryBox" class="summary-box" style="display: none;">
            <div class="summary-row">
                <span>عدد الكراتين:</span>
                <span id="summaryBoxCount">0</span>
            </div>
            <div class="summary-row">
                <span>وزن اللفاف:</span>
                <span id="summaryLafafWeight">0 كجم</span>
            </div>
            <div class="summary-row">
                <span>مجموع أوزان الكراتين:</span>
                <span id="summaryTotalWeight">0 كجم</span>
            </div>
            <div class="summary-row">
                <span>الفرق:</span>
                <span id="summaryDifference">0 كجم</span>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn-success" onclick="submitBoxes()" id="submitBtn" disabled>
            ✅ حفظ جميع الكراتين
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('manufacturing.stage4.index') }}'">
            ❌ إلغاء
        </button>
    </div>
</div>

<script>
let currentLafaf = null;
let boxes = [];
let boxCounter = 0;

document.getElementById('lafafBarcode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        loadLafaf(this.value.trim());
    }
});

function loadLafaf(barcode) {
    if (!barcode) {
        showToast('⚠️ يرجى إدخال باركود اللفاف!', 'error');
        return;
    }

    const barcodeInput = document.getElementById('lafafBarcode');
    barcodeInput.disabled = true;

    fetch(`/stage4/get-lafaf-by-barcode/${barcode}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentLafaf = data.data;
            displayLafafData();
            showToast('✅ تم تحميل بيانات اللفاف بنجاح!', 'success');
        } else {
            throw new Error(data.message || 'لم يتم العثور على الباركود');
        }
    })
    .catch(error => {
        showToast('❌ ' + error.message, 'error');
        currentLafaf = null;
        document.getElementById('lafafDisplay').classList.remove('active');
    })
    .finally(() => {
        barcodeInput.disabled = false;
    });
}

function displayLafafData() {
    document.getElementById('displayBarcode').textContent = currentLafaf.barcode;
    document.getElementById('displayColor').textContent = currentLafaf.color || '-';
    document.getElementById('displayWeight').textContent = currentLafaf.total_weight + ' كجم';
    document.getElementById('displayPlastic').textContent = currentLafaf.plastic_type || '-';
    document.getElementById('lafafDisplay').classList.add('active');
    document.getElementById('submitBtn').disabled = false;
    
    if (boxes.length === 0) {
        addBox();
    }
}

function addBox() {
    if (!currentLafaf) {
        showToast('⚠️ يرجى مسح باركود اللفاف أولاً!', 'error');
        return;
    }

    boxCounter++;
    boxes.push({
        id: Date.now(),
        number: boxCounter,
        weight: '',
        notes: ''
    });

    renderBoxes();
    updateSummary();
}

function renderBoxes() {
    const list = document.getElementById('boxesList');
    
    if (boxes.length === 0) {
        list.innerHTML = '<p style="text-align: center; color: #95a5a6; padding: 20px;">لم يتم إضافة كراتين بعد</p>';
        return;
    }

    list.innerHTML = boxes.map(box => `
        <div class="box-item" data-id="${box.id}">
            <div class="box-header">
                <span class="box-number">📦 كرتون رقم ${box.number}</span>
            </div>
            <div class="box-form">
                <div class="form-group">
                    <label>الوزن (كجم) <span style="color: #e74c3c;">*</span></label>
                    <input type="number" class="form-control box-weight" data-id="${box.id}" 
                           value="${box.weight}" step="0.001" placeholder="0.000" 
                           oninput="updateBoxWeight(${box.id}, this.value)">
                </div>
                <div class="form-group">
                    <label>ملاحظات</label>
                    <input type="text" class="form-control" data-id="${box.id}" 
                           value="${box.notes}" placeholder="ملاحظات إضافية..."
                           oninput="updateBoxNotes(${box.id}, this.value)">
                </div>
                <button class="btn-delete" onclick="removeBox(${box.id})" type="button">🗑️</button>
            </div>
        </div>
    `).join('');
}

function updateBoxWeight(id, weight) {
    const box = boxes.find(b => b.id === id);
    if (box) {
        box.weight = weight;
        updateSummary();
    }
}

function updateBoxNotes(id, notes) {
    const box = boxes.find(b => b.id === id);
    if (box) {
        box.notes = notes;
    }
}

function removeBox(id) {
    if (boxes.length === 1) {
        showToast('⚠️ يجب أن يكون هناك كرتون واحد على الأقل!', 'error');
        return;
    }

    boxes = boxes.filter(b => b.id !== id);
    renderBoxes();
    updateSummary();
    showToast('🗑️ تم حذف الكرتون', 'info');
}

function updateSummary() {
    if (!currentLafaf || boxes.length === 0) {
        document.getElementById('summaryBox').style.display = 'none';
        return;
    }

    const lafafWeight = parseFloat(currentLafaf.total_weight);
    const totalWeight = boxes.reduce((sum, box) => sum + (parseFloat(box.weight) || 0), 0);
    const difference = Math.abs(lafafWeight - totalWeight);

    document.getElementById('summaryBoxCount').textContent = boxes.length;
    document.getElementById('summaryLafafWeight').textContent = lafafWeight.toFixed(3) + ' كجم';
    document.getElementById('summaryTotalWeight').textContent = totalWeight.toFixed(3) + ' كجم';
    document.getElementById('summaryDifference').textContent = difference.toFixed(3) + ' كجم';
    document.getElementById('summaryBox').style.display = 'block';

    const tolerance = lafafWeight * 0.02;
    const differenceSpan = document.getElementById('summaryDifference');
    differenceSpan.style.color = difference > tolerance ? '#e74c3c' : '#27ae60';
}

function submitBoxes() {
    if (!currentLafaf) {
        showToast('⚠️ يرجى مسح باركود اللفاف أولاً!', 'error');
        return;
    }

    if (boxes.length === 0) {
        showToast('⚠️ يرجى إضافة كرتون واحد على الأقل!', 'error');
        return;
    }

    const invalidBoxes = boxes.filter(b => !b.weight || parseFloat(b.weight) <= 0);
    if (invalidBoxes.length > 0) {
        showToast('⚠️ يرجى إدخال وزن لجميع الكراتين!', 'error');
        return;
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ جاري الحفظ...';

    const formData = {
        lafaf_barcode: currentLafaf.barcode,
        boxes: boxes.map(b => ({
            weight: parseFloat(b.weight),
            notes: b.notes
        })),
        packaging_type: 'standard',
        _token: '{{ csrf_token() }}'
    };

    fetch('{{ route("manufacturing.stage4.store") }}', {
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
            showToast('✅ تم حفظ الكراتين بنجاح! عدد الكراتين: ' + data.data.box_count, 'success');
            setTimeout(() => {
                window.location.href = '{{ route("manufacturing.stage4.index") }}';
            }, 2000);
        } else {
            throw new Error(data.message || 'حدث خطأ أثناء الحفظ');
        }
    })
    .catch(error => {
        showToast('❌ خطأ: ' + error.message, 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '✅ حفظ جميع الكراتين';
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
</script>

@endsection