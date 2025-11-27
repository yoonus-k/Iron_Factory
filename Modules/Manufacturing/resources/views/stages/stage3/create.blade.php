@extends('master')

@section('title', 'المرحلة الثالثة - إنشاء اللفائف')

@section('content')

<style>
    :root{
        --brand-1: #9b59b6;
        --brand-2: #8e44ad;
        --muted: #6e7a81;
        --surface: #f5f7fa;
        --card: #ffffff;
        --success: #27ae60;
        --danger: #e74c3c;
        --radius: 12px;
    }

    .stage-container{ max-width:1200px; margin:26px auto; padding:20px; font-family: 'Segoe UI', Tahoma, Arial; color:#24303a }

    .stage-header{ display:flex; gap:14px; align-items:center; background: linear-gradient(90deg,var(--brand-1),var(--brand-2)); color:#fff; padding:20px 22px; border-radius:10px; box-shadow:0 10px 30px rgba(155,89,182,0.12) }
    .stage-header h1{ margin:0; font-size:20px }
    .stage-header p{ margin:0; opacity:0.95; font-size:13px }

    .form-section{ background:var(--card); padding:18px; border-radius:var(--radius); margin-top:18px; box-shadow:0 6px 18px rgba(10,30,60,0.04); border:1px solid rgba(34,47,62,0.04) }
    .section-title{ font-size:16px; color:var(--brand-1); font-weight:700 }

    .barcode-section{ background: linear-gradient(180deg,#f9f3fc 0,#f3e5f5 100%); padding:20px; border-radius:10px; text-align:center; border:1px dashed rgba(155,89,182,0.06) }
    .barcode-input-wrapper{ max-width:720px; margin:0 auto; position:relative }
    .barcode-input{ width:100%; padding:16px 18px; border-radius:10px; border:2px solid rgba(155,89,182,0.12); font-size:16px; font-weight:600 }
    .barcode-icon{ position:absolute; left:16px; top:50%; transform:translateY(-50%); font-size:18px }

    .stage2-display{ display:none; padding:14px; border-radius:10px; background:linear-gradient(180deg,#f8fcff,#eef9ff); border-left:4px solid var(--brand-1); margin-top:12px }
    .stage2-display.active{ display:block }
    .stage2-info{ display:grid; grid-template-columns:repeat(3,1fr); gap:12px }
    .info-item{ background:var(--card); padding:12px; border-radius:8px; text-align:center; box-shadow:0 4px 12px rgba(10,30,60,0.03) }
    .info-label{ font-size:13px; color:var(--muted); margin-bottom:6px; font-weight:600 }
    .info-value{ font-size:15px; font-weight:700; color:#22303a }

    .form-row{ display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:14px; margin-top:10px }
    .form-group label{ font-size:13px; color:var(--muted); font-weight:700; margin-bottom:6px }
    .form-control, .form-select{ padding:10px 12px; border-radius:8px; border:1.5px solid #e7eef5; background:#fbfeff }
    .form-control[readonly]{ background:#f1f6f9; font-weight:600 }

    textarea.form-control{ min-height:100px }

    .lafafs-list{ margin-top:20px }
    .lafaf-item{ display:flex; justify-content:space-between; align-items:start; padding:18px; border-radius:12px; background:linear-gradient(135deg, #f8fcff 0%, #e8f8f5 100%); box-shadow:0 6px 18px rgba(10,30,60,0.03); margin-bottom:15px; border-right:4px solid #27ae60 }
    .lafaf-info strong{ font-size:15px }

    .button-group{ display:flex; gap:10px; flex-wrap:wrap; margin-top:10px }
    .btn-primary, .btn-success, .btn-secondary{ border:none; border-radius:8px; padding:10px 14px; font-weight:700; cursor:pointer }
    .btn-primary{ background:var(--brand-1); color:white }
    .btn-success{ background:var(--success); color:white }
    .btn-secondary{ background:#8e9aa4; color:white }

    .btn-print{ background:#27ae60; color:white; padding:10px 16px; border-radius:8px; border:none; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:6px; box-shadow:0 2px 8px rgba(39,174,96,0.3) }

    .empty-state{ padding:30px; text-align:center; color:#98a2a8 }

    .info-box{ background:linear-gradient(135deg,#fff9e6 0,#ffeaa7 100%); border-right:4px solid #f39c12; padding:15px; border-radius:8px; margin-bottom:20px }
    .info-box strong{ color:#e67e22; display:block; margin-bottom:8px }

    @media (max-width:900px){ .form-row{ grid-template-columns:1fr } .stage2-info{ grid-template-columns:1fr } .stage-header{ flex-direction:column; text-align:center } }
</style>

<div class="stage-container">
    <!-- Header -->
    <div class="stage-header">
        <h1>
            <i class="fas fa-circle"></i>
            المرحلة الثالثة - إنشاء اللفائف
        </h1>
        <p>امسح باركود المرحلة الثانية وأضف الصبغة والبلاستيك لإنشاء لفاف (الوزن يزيد في هذه المرحلة)</p>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 style="margin: 0 0 15px 0; color: #9b59b6;"><i class="fas fa-camera"></i> مسح باركود المرحلة الثانية</h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="stage2Barcode" class="barcode-input" placeholder="امسح أو اكتب باركود المرحلة الثانية (ST2-XXX-2025)" autofocus>
            <span class="barcode-icon">🎨</span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 10px;"><i class="fas fa-lightbulb"></i> امسح الباركود أو اضغط Enter للبحث</small>
    </div>

    <!-- Stage2 Display -->
    <div id="stage2Display" class="stage2-display">
        <h4><i class="fas fa-circle-check"></i> بيانات المرحلة الثانية</h4>
        <div class="stage2-info">
            <div class="info-item">
                <div class="info-label">الباركود</div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">الاستاند</div>
                <div class="info-value" id="displayStand">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">الوزن المتبقي</div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
        </div>
    </div>

    <!-- Lafaf Form -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-edit"></i> بيانات اللفاف</h3>

        <div class="info-box">
            <strong><i class="fas fa-thumbtack"></i> ملاحظة هامة:</strong>
            <ul style="margin:8px 0 0 20px; color:#7f8c8d; font-size:13px;">
                <li><strong>الوزن يزيد</strong> في هذه المرحلة (إضافة الصبغة والبلاستيك)</li>
                <li>أدخل الوزن الكامل الشامل (وزن المرحلة السابقة + الصبغة + البلاستيك)</li>
                <li>سيتم حساب الوزن المضاف تلقائياً</li>
            </ul>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>وزن الدخول من المرحلة السابقة (كجم)</label>
                <input type="number" id="inputWeight" class="form-control" readonly style="background: #ecf0f1; font-weight: 600;">
            </div>

            <div class="form-group">
                <label>الوزن الكامل الشامل (كجم) <span style="color:#e74c3c;">*</span></label>
                <input type="number" id="totalWeight" class="form-control" placeholder="مثال: 105.50" step="0.01">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">شامل وزن الدخول + الصبغة + البلاستيك</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>الوزن المضاف (كجم)</label>
                <input type="number" id="addedWeight" class="form-control" readonly style="background: #e8f5e9; font-weight: 600; color: #27ae60;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">يتم الحساب تلقائياً = الوزن الكامل - وزن الدخول</small>
            </div>

            <div class="form-group">
                <label>اللون <span style="color:#e74c3c;">*</span></label>
                <input type="text" id="color" class="form-control" placeholder="مثال: أحمر، أزرق، أخضر...">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>نوع البلاستيك</label>
                <input type="text" id="plasticType" class="form-control" placeholder="مثال: PE، PP، PVC...">
            </div>

            <div class="form-group">
                <label>ملاحظات</label>
                <textarea id="notes" class="form-control" placeholder="أضف أي ملاحظات إضافية..."></textarea>
            </div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-primary" onclick="addLafaf()">
                <i class="fas fa-plus"></i> إضافة اللفاف
            </button>
            <button type="button" class="btn-secondary" onclick="clearForm()">
                <i class="fas fa-sync"></i> مسح النموذج
            </button>
        </div>
    </div>

    <!-- Lafafs List -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-clipboard"></i> اللفائف المضافة (<span id="lafafCount">0</span>)</h3>
        <div id="lafafList" class="lafafs-list">
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;opacity:0.3;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>لا توجد لفائف مضافة بعد</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div style="display:flex; gap:15px; justify-content:center; margin-top:25px; padding-top:20px; border-top:2px solid #ecf0f1;">
        <button type="button" class="btn-success" onclick="finishOperation()" id="submitBtn" disabled style="padding:14px 32px; font-size:16px;">
            <i class="fas fa-check-double"></i> إنهاء العملية
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('manufacturing.stage3.index') }}'">
            <i class="fas fa-times"></i> إلغاء
        </button>
    </div>
</div>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
let currentStage2 = null;
let lafafs = [];

// Barcode scanner
document.getElementById('stage2Barcode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        loadStage2(this.value.trim());
        this.value = '';
    }
});

// Auto-calculate added weight
document.getElementById('totalWeight').addEventListener('input', calculateAddedWeight);

function loadStage2(barcode) {
    if (!barcode) {
        alert('⚠️ يرجى إدخال باركود المرحلة الثانية!');
        return;
    }

    fetch(`/stage3/get-stage2-by-barcode/${barcode}`)
        .then(response => {
            if (!response.ok) throw new Error('لم يتم العثور على البيانات');
            return response.json();
        })
        .then(result => {
            if (!result.success) throw new Error(result.message);

            const data = result.data;
            const source = result.source || 'stage2';
            
            currentStage2 = {
                id: data.id || null,
                barcode: data.barcode,
                stand_number: data.stand_number || 'غير محدد',
                output_weight: parseFloat(data.remaining_weight || data.output_weight || data.quantity),
                material_id: data.material_id,
                material_name: data.material_name || 'غير محدد',
                source: source
            };

            // Display stage2 data
            document.getElementById('displayBarcode').textContent = currentStage2.barcode;
            document.getElementById('displayStand').textContent = currentStage2.stand_number;
            document.getElementById('displayWeight').textContent = currentStage2.output_weight + ' كجم';
            document.getElementById('stage2Display').classList.add('active');

            // Fill input weight
            document.getElementById('inputWeight').value = currentStage2.output_weight;

            // Calculate initial added weight
            calculateAddedWeight();

            // Focus on total weight
            document.getElementById('totalWeight').focus();

            showToast('تم تحميل بيانات المرحلة الثانية بنجاح!', 'success');
        })
        .catch(error => {
            alert('خطأ: ' + error.message);
            document.getElementById('stage2Barcode').focus();
        });
}

function calculateAddedWeight() {
    const inputWeight = parseFloat(document.getElementById('inputWeight').value) || 0;
    const totalWeight = parseFloat(document.getElementById('totalWeight').value) || 0;

    if (totalWeight > 0 && inputWeight > 0) {
        const addedWeight = totalWeight - inputWeight;
        
        if (addedWeight < 0) {
            showToast('⚠️ الوزن الكامل يجب أن يكون أكبر من وزن الدخول!', 'error');
            document.getElementById('addedWeight').value = '';
            return;
        }

        document.getElementById('addedWeight').value = addedWeight.toFixed(3);
    } else {
        document.getElementById('addedWeight').value = '';
    }
}

function addLafaf() {
    if (!currentStage2) {
        alert('⚠️ يرجى مسح باركود المرحلة الثانية أولاً!');
        document.getElementById('stage2Barcode').focus();
        return;
    }

    const totalWeight = document.getElementById('totalWeight').value;
    const color = document.getElementById('color').value.trim();
    const plasticType = document.getElementById('plasticType').value.trim();
    const notes = document.getElementById('notes').value.trim();

    if (!totalWeight || !color) {
        alert('⚠️ يرجى ملء جميع الحقول المطلوبة!');
        return;
    }

    const inputWeight = parseFloat(document.getElementById('inputWeight').value) || 0;
    const totalWeightNum = parseFloat(totalWeight);

    if (totalWeightNum <= inputWeight) {
        alert('⚠️ الوزن الكامل يجب أن يكون أكبر من وزن الدخول!');
        return;
    }

    const data = {
        stage2_id: currentStage2.id || null,
        stage2_barcode: currentStage2.barcode,
        source: currentStage2.source || 'stage2',
        material_id: currentStage2.material_id || null,
        input_weight: inputWeight,
        total_weight: totalWeightNum,
        color: color,
        plastic_type: plasticType,
        notes: notes
    };

    // حفظ فوري للفاف
    const addBtn = event.target;
    addBtn.disabled = true;
    addBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';

    fetch('{{ route("manufacturing.stage3.store-single") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const lafaf = {
                id: result.data.lafaf_id,
                barcode: result.data.barcode,
                coil_number: result.data.coil_number,
                material_name: result.data.material_name,
                total_weight: result.data.total_weight,
                input_weight: result.data.input_weight,
                added_weight: result.data.added_weight,
                color: result.data.color,
                plastic_type: result.data.plastic_type,
                notes: notes,
                saved: true
            };

            lafafs.push(lafaf);
            renderLafafs();
            clearForm();
            
            showToast('✅ تم حفظ اللفاف بنجاح!', 'success');
            
            document.getElementById('stage2Barcode').focus();
        } else {
            throw new Error(result.message || 'حدث خطأ أثناء الحفظ');
        }
    })
    .catch(error => {
        alert('❌ خطأ: ' + error.message);
    })
    .finally(() => {
        addBtn.disabled = false;
        addBtn.innerHTML = '<i class="fas fa-plus"></i> إضافة اللفاف';
    });
}

function renderLafafs() {
    const list = document.getElementById('lafafList');
    document.getElementById('lafafCount').textContent = lafafs.length;
    document.getElementById('submitBtn').disabled = lafafs.length === 0;

    if (lafafs.length === 0) {
        list.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;opacity:0.3;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>لا توجد لفائف مضافة بعد</p>
            </div>
        `;
        return;
    }

    list.innerHTML = lafafs.map(item => `
        <div class="lafaf-item">
            <div class="lafaf-info" style="flex:1;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                    <strong style="color:#2c3e50; font-size:16px;">
                        <i class="fas fa-circle" style="color:#27ae60;"></i> ${item.coil_number}
                    </strong>
                    <span style="background:#27ae60; color:white; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600;">✓ محفوظ</span>
                </div>
                <small style="display:block; line-height:1.6;">
                    <strong>المادة:</strong> ${item.material_name} |
                    <strong>الباركود:</strong> <code style="background:#f8f9fa; padding:2px 6px; border-radius:4px; font-family:monospace;">${item.barcode}</code><br>
                    <strong>وزن كامل:</strong> ${item.total_weight} كجم |
                    <strong>وزن مضاف:</strong> ${item.added_weight} كجم |
                    <strong>اللون:</strong> ${item.color}
                    ${item.plastic_type ? ' | <strong>بلاستيك:</strong> ' + item.plastic_type : ''}
                    ${item.notes ? '<br>📝 <strong>ملاحظات:</strong> ' + item.notes : ''}
                </small>
            </div>
            <div style="display:flex; gap:8px;">
                <button class="btn-print" onclick="printLafafBarcode('${item.barcode}', '${item.coil_number}', '${item.material_name}', ${item.total_weight}, '${item.color}')">
                    <i class="fas fa-print"></i> طباعة الباركود
                </button>
            </div>
        </div>
    `).join('');
}

function finishOperation() {
    if (lafafs.length === 0) {
        alert('⚠️ لا توجد لفائف محفوظة!');
        return;
    }

    showToast('✅ تم إنهاء العملية بنجاح!', 'success');
    setTimeout(() => {
        window.location.href = '{{ route("manufacturing.stage3.index") }}';
    }, 1000);
}

function clearForm() {
    document.getElementById('totalWeight').value = '';
    document.getElementById('addedWeight').value = '';
    document.getElementById('color').value = '';
    document.getElementById('plasticType').value = '';
    document.getElementById('notes').value = '';

    if (currentStage2) {
        document.getElementById('inputWeight').value = currentStage2.output_weight;
    }
}

function printLafafBarcode(barcode, coilNumber, materialName, totalWeight, color) {
    const printWindow = window.open('', '', 'height=650,width=850');
    printWindow.document.write('<html dir="rtl"><head><title>طباعة الباركود - ' + coilNumber + '</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
    printWindow.document.write('.barcode-container { background: white; padding: 50px; border-radius: 16px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); text-align: center; max-width: 550px; }');
    printWindow.document.write('.title { font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 4px solid #9b59b6; }');
    printWindow.document.write('.coil-number { font-size: 24px; color: #9b59b6; font-weight: bold; margin: 20px 0; }');
    printWindow.document.write('.barcode-code { font-size: 22px; font-weight: bold; color: #2c3e50; margin: 25px 0; letter-spacing: 4px; font-family: "Courier New", monospace; }');
    printWindow.document.write('.info { margin-top: 30px; padding: 25px; background: #f8f9fa; border-radius: 10px; text-align: right; }');
    printWindow.document.write('.info-row { margin: 12px 0; display: flex; justify-content: space-between; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 16px; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 18px; }');
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
    printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2, height: 90, displayValue: false, margin: 12 });');
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
    printWindow.document.write('<\/script></body></html>');
    printWindow.document.close();
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
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

@endsection
