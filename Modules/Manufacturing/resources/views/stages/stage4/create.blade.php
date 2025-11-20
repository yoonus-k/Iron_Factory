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
                    
                    // عرض نافذة الباركودات
                    if (data.data.barcode_info && data.data.barcode_info.length > 0) {
                        setTimeout(() => {
                            showBarcodesModal(data.data.barcode_info);
                        }, 500);
                    } else {
                        setTimeout(() => {
                            window.location.href = '{{ route('manufacturing.stage4.index') }}';
                        }, 2000);
                    }
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
        const totalWeight = barcodes.reduce((sum, item) => sum + parseFloat(item.weight), 0);
        const boxesCount = barcodes.length;

        let barcodesHTML = barcodes.map((item, index) => `
            <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%); padding: 25px; border-radius: 12px; margin-bottom: 20px; border-right: 5px solid #e67e22; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: start; margin-bottom: 20px;">
                    <div>
                        <h4 style="margin: 0 0 12px 0; color: #2c3e50; font-size: 20px; font-weight: 700;">
                            <i class="fas fa-box" style="color: #e67e22;"></i> ${item.box_number}
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 15px;">
                            <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #27ae60;">
                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">المادة</div>
                                <div style="font-size: 14px; color: #2c3e50; font-weight: 700;">${item.material_name}</div>
                            </div>
                            <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #e67e22;">
                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">الوزن</div>
                                <div style="font-size: 18px; color: #e67e22; font-weight: 700;">${item.weight} كجم</div>
                            </div>
                            <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #3498db;">
                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">نوع التعبئة</div>
                                <div style="font-size: 14px; color: #3498db; font-weight: 700;">${item.packaging_type}</div>
                            </div>
                            <div style="background: white; padding: 12px; border-radius: 8px; border-right: 3px solid #9b59b6;">
                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">باركود اللفاف</div>
                                <div style="font-size: 12px; color: #9b59b6; font-weight: 700;">${item.lafaf_barcode}</div>
                            </div>
                        </div>
                    </div>
                    <button onclick="printStage4Barcode('${item.barcode}', '${item.box_number}', '${item.material_name}', ${item.weight}, '${item.lafaf_barcode}')" style="background: linear-gradient(135deg, #e67e22 0%, #d35400 100%); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 8px; box-shadow: 0 3px 10px rgba(230, 126, 34, 0.3); transition: all 0.3s;">
                        <i class="fas fa-print"></i> طباعة
                    </button>
                </div>
                <div style="background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                    <svg id="barcode-stage4-${index}" style="max-width: 100%;"></svg>
                    <div style="font-family: 'Courier New', monospace; font-size: 18px; font-weight: bold; color: #2c3e50; margin-top: 12px; letter-spacing: 3px; background: #f8f9fa; padding: 10px; border-radius: 6px;">
                        ${item.barcode}
                    </div>
                </div>
            </div>
        `).join('');

        modal.innerHTML = `
            <div style="background: white; border-radius: 12px; max-width: 900px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
                <div style="background: linear-gradient(135deg, #e67e22 0%, #d35400 100%); color: white; padding: 25px; border-radius: 12px 12px 0 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="margin: 0; font-size: 24px; font-weight: 700;">
                            <i class="fas fa-check-circle"></i> تم تعبئة الكراتين بنجاح!
                        </h2>
                        <button onclick="closeBarcodesModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; font-size: 24px; cursor: pointer; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                            ✕
                        </button>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; padding: 15px; background: rgba(255,255,255,0.15); border-radius: 10px; backdrop-filter: blur(10px);">
                        <div style="text-align: center;">
                            <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">عدد الكراتين</div>
                            <div style="font-size: 28px; font-weight: 700;">${boxesCount}</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 13px; opacity: 0.9; margin-bottom: 5px;">إجمالي الوزن</div>
                            <div style="font-size: 28px; font-weight: 700;">${totalWeight.toFixed(2)} كجم</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 30px;">
                    <h3 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 18px; border-bottom: 2px solid #e9ecef; padding-bottom: 12px;">
                        <i class="fas fa-barcode"></i> الباركودات المولدة
                    </h3>
                    ${barcodesHTML}
                    <div style="display: flex; gap: 15px; margin-top: 25px; padding-top: 20px; border-top: 2px solid #e9ecef;">
                        <button onclick="printAllStage4Barcodes(${JSON.stringify(barcodes).replace(/"/g, '&quot;')})" style="flex: 1; background: #e67e22; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                            <i class="fas fa-print"></i> طباعة الكل
                        </button>
                        <button onclick="window.location.href='{{ route('manufacturing.stage4.index') }}'" style="flex: 1; background: #27ae60; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 10px;">
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
                JsBarcode(`#barcode-stage4-${index}`, item.barcode, {
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
        window.location.href = '{{ route("manufacturing.stage4.index") }}';
    }

    function printStage4Barcode(barcode, boxNumber, materialName, weight, lafafBarcode) {
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html dir="rtl"><head><title>طباعة الباركود - المرحلة الرابعة</title>');
        printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
        printWindow.document.write('<style>');
        printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
        printWindow.document.write('.barcode-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); text-align: center; max-width: 500px; }');
        printWindow.document.write('.title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 3px solid #e67e22; }');
        printWindow.document.write('.box-number { font-size: 20px; color: #e67e22; font-weight: bold; margin: 15px 0; }');
        printWindow.document.write('.barcode-code { font-size: 18px; font-weight: bold; color: #2c3e50; margin: 20px 0; letter-spacing: 3px; font-family: "Courier New", monospace; }');
        printWindow.document.write('.info { margin-top: 25px; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: right; }');
        printWindow.document.write('.info-row { margin: 10px 0; display: flex; justify-content: space-between; }');
        printWindow.document.write('.label { color: #7f8c8d; font-size: 14px; }');
        printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 16px; }');
        printWindow.document.write('@media print { body { background: white; } }');
        printWindow.document.write('</style></head><body>');
        printWindow.document.write('<div class="barcode-container">');
        printWindow.document.write('<div class="title">باركود الكرتون - المرحلة الرابعة</div>');
        printWindow.document.write('<div class="box-number">' + boxNumber + '</div>');
        printWindow.document.write('<svg id="print-barcode"></svg>');
        printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
        printWindow.document.write('<div class="info">');
        printWindow.document.write('<div class="info-row"><span class="label">المادة:</span><span class="value">' + materialName + '</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">الوزن:</span><span class="value">' + weight + ' كجم</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">باركود اللفاف:</span><span class="value">' + lafafBarcode + '</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">التاريخ:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
        printWindow.document.write('</div></div>');
        printWindow.document.write('<script>');
        printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2, height: 80, displayValue: false, margin: 10 });');
        printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
        printWindow.document.write('<\/script></body></html>');
        printWindow.document.close();
    }

    function printAllStage4Barcodes(barcodes) {
        const printWindow = window.open('', '', 'height=800,width=1000');
        printWindow.document.write('<html dir="rtl"><head><title>طباعة جميع الباركودات - المرحلة الرابعة</title>');
        printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
        printWindow.document.write('<style>');
        printWindow.document.write('body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }');
        printWindow.document.write('.barcode-item { background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); page-break-inside: avoid; }');
        printWindow.document.write('.title { font-size: 20px; font-weight: bold; color: #2c3e50; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e67e22; }');
        printWindow.document.write('.barcode-code { font-size: 16px; font-weight: bold; color: #2c3e50; margin: 15px 0; text-align: center; letter-spacing: 2px; font-family: "Courier New", monospace; }');
        printWindow.document.write('.info { margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 6px; }');
        printWindow.document.write('.info-row { margin: 8px 0; display: flex; justify-content: space-between; }');
        printWindow.document.write('.label { color: #7f8c8d; font-size: 13px; }');
        printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 14px; }');
        printWindow.document.write('@media print { body { background: white; padding: 0; } .barcode-item { box-shadow: none; page-break-after: always; } }');
        printWindow.document.write('</style></head><body>');
        
        barcodes.forEach((item, index) => {
            printWindow.document.write('<div class="barcode-item">');
            printWindow.document.write('<div class="title">باركود الكرتون - ' + item.box_number + '</div>');
            printWindow.document.write('<div style="text-align: center;"><svg id="print-barcode-' + index + '"></svg></div>');
            printWindow.document.write('<div class="barcode-code">' + item.barcode + '</div>');
            printWindow.document.write('<div class="info">');
            printWindow.document.write('<div class="info-row"><span class="label">الكرتون:</span><span class="value">' + item.box_number + '</span></div>');
            printWindow.document.write('<div class="info-row"><span class="label">المادة:</span><span class="value">' + item.material_name + '</span></div>');
            printWindow.document.write('<div class="info-row"><span class="label">الوزن:</span><span class="value">' + item.weight + ' كجم</span></div>');
            printWindow.document.write('<div class="info-row"><span class="label">باركود اللفاف:</span><span class="value">' + item.lafaf_barcode + '</span></div>');
            printWindow.document.write('<div class="info-row"><span class="label">نوع التعبئة:</span><span class="value">' + item.packaging_type + '</span></div>');
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
