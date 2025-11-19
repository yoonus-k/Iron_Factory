@extends('master')

@section('title', 'تعبئة الكراتين - المرحلة الرابعة')

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
    <div class="stage-header">
        <h1>
            <span>📦</span>
            المرحلة الرابعة - تعبئة الكراتين
        </h1>
        <p><i class="fas fa-info-circle"></i> قم بمسح باركود اللفاف وتقسيمه على الكراتين (كل كرتون سيحصل على باركود خاص)</p>
    </div>

    <div class="form-section barcode-section">
        <h3 style="margin: 0 0 15px 0; color: #0066B2;"><i class="fas fa-camera"></i> مسح باركود اللفاف <span class="info-tooltip">?<span class="tooltip-text">مسح باركود اللفاف من المرحلة الثالثة</span></span></h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="lafafBarcode" class="barcode-input"
                placeholder="امسح أو اكتب باركود اللفاف (CO3-XXXX)" autofocus>
            <span class="barcode-icon"><i class="fas fa-box"></i></span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 10px;"><i class="fas fa-lightbulb"></i> <span class="info-tooltip">?<span class="tooltip-text">امسح الباركود أو اضغط Enter للبحث</span></span></small>
    </div>

    <div id="lafafDisplay" class="lafaf-display">
        <h4><i class="fas fa-circle-check"></i> بيانات اللفاف</h4>
        <div class="lafaf-info">
            <div class="info-item">
                <div class="info-label">الباركود <span class="info-tooltip">?<span class="tooltip-text">الرمز الشريطي للفاف</span></span></div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">اللون <span class="info-tooltip">?<span class="tooltip-text">لون اللفاف المنتج</span></span></div>
                <div class="info-value" id="displayColor">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">الوزن الكامل <span class="info-tooltip">?<span class="tooltip-text">الوزن الإجمالي للفاف</span></span></div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">نوع البلاستيك <span class="info-tooltip">?<span class="tooltip-text">نوع البلاستيك المستخدم في اللفاف</span></span></div>
                <div class="info-value" id="displayPlastic">-</div>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-boxes"></i> تقسيم الكراتين</h3>

        <div class="info-box">
            <div class="info-box-header">
                <strong><i class="fas fa-thumbtack"></i> ملاحظة هامة: <span class="info-tooltip">?<span class="tooltip-text"><strong>شروط تقسيم الكراتين:</strong><br><br>• مجموع أوزان الكراتين يجب أن يساوي وزن اللفاف تقريباً (تسامح 2%)<br><br>• كل كرتون سيحصل على باركود خاص (BOX4-XXXX)<br><br>• يمكنك تتبع كل كرتون بشكل منفصل من خلال صفحة التتبع</span></span></strong>
            </div>
        </div>

        <div id="boxesList"></div>

        <div class="button-group">
            <button type="button" class="btn-primary" onclick="addBox()">
                <i class="fas fa-plus"></i> إضافة كرتون
            </button>
        </div>

        <div id="summaryBox" class="summary-box" style="display: none;">
            <div class="summary-row">
                <span>عدد الكراتين: <span class="info-tooltip">?<span class="tooltip-text">عدد الكراتين المضافة</span></span></span>
                <span id="summaryBoxCount">0</span>
            </div>
            <div class="summary-row">
                <span>وزن اللفاف: <span class="info-tooltip">?<span class="tooltip-text">الوزن الإجمالي للفاف</span></span></span>
                <span id="summaryLafafWeight">0 كجم</span>
            </div>
            <div class="summary-row">
                <span>مجموع أوزان الكراتين: <span class="info-tooltip">?<span class="tooltip-text">مجموع أوزان جميع الكراتين</span></span></span>
                <span id="summaryTotalWeight">0 كجم</span>
            </div>
            <div class="summary-row">
                <span>الفرق: <span class="info-tooltip">?<span class="tooltip-text">الفرق بين وزن اللفاف ومجموع أوزان الكراتين</span></span></span>
                <span id="summaryDifference">0 كجم</span>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn-success" onclick="submitBoxes()" id="submitBtn" disabled>
            <i class="fas fa-check"></i> حفظ جميع الكراتين
        </button>
        <button type="button" class="btn-secondary"
            onclick="window.location.href='{{ route('manufacturing.stage4.index') }}'">
            <i class="fas fa-times"></i> إلغاء
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
                headers: {
                    'Accept': 'application/json'
                }
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
                showToast(error.message, 'error');
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
            showToast('يرجى مسح باركود اللفاف أولاً!', 'error');
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
            list.innerHTML =
            '<p style="text-align: center; color: #95a5a6; padding: 20px;"><i class="fas fa-inbox"></i> لم يتم إضافة كراتين بعد</p>';
            return;
        }

        list.innerHTML = boxes.map(box => `
        <div class="box-item" data-id="${box.id}">
            <div class="box-header">
                <span class="box-number"><i class="fas fa-box"></i> كرتون رقم ${box.number}</span>
            </div>
            <div class="box-form">
                <div class="form-group">
                    <label>الوزن (كجم) <span style="color: #e74c3c;"><i class="fas fa-asterisk"></i></span> <span class="info-tooltip">?<span class="tooltip-text">وزن الكرتون بالكيلوغرام</span></span></label>
                    <input type="number" class="form-control box-weight" data-id="${box.id}"
                           value="${box.weight}" step="0.001" placeholder="0.000"
                           oninput="updateBoxWeight(${box.id}, this.value)">
                </div>
                <div class="form-group">
                    <label>ملاحظات <span class="info-tooltip">?<span class="tooltip-text">ملاحظات إضافية عن الكرتون</span></span></label>
                    <input type="text" class="form-control" data-id="${box.id}"
                           value="${box.notes}" placeholder="ملاحظات إضافية..."
                           oninput="updateBoxNotes(${box.id}, this.value)">
                    <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-sticky-note"></i> <span class="info-tooltip">?<span class="tooltip-text">يمكنك إضافة ملاحظات إضافية هنا</span></span></small>
                </div>
                <button class="btn-delete" onclick="removeBox(${box.id})" type="button"><i class="fas fa-trash"></i></button>
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
        showToast('تم حذف الكرتون', 'info');
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
            showToast('يرجى مسح باركود اللفاف أولاً!', 'error');
            return;
        }

        if (boxes.length === 0) {
            showToast('يرجى إضافة كرتون واحد على الأقل!', 'error');
            return;
        }

        const invalidBoxes = boxes.filter(b => !b.weight || parseFloat(b.weight) <= 0);
        if (invalidBoxes.length > 0) {
            showToast('يرجى إدخال وزن لجميع الكراتين!', 'error');
            return;
        }

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';

        const formData = {
            lafaf_barcode: currentLafaf.barcode,
            boxes: boxes.map(b => ({
                weight: parseFloat(b.weight),
                notes: b.notes
            })),
            packaging_type: 'standard',
            _token: '{{ csrf_token() }}'
        };

        fetch('{{ route('manufacturing.stage4.store') }}', {
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
                    showToast('تم حفظ الكراتين بنجاح! عدد الكراتين: ' + data.data.box_count, 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route('manufacturing.stage4.index') }}';
                    }, 2000);
                } else {
                    throw new Error(data.message || 'حدث خطأ أثناء الحفظ');
                }
            })
            .catch(error => {
                showToast('خطأ: ' + error.message, 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check"></i> حفظ جميع الكراتين';
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
        font-size: 14px;
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
