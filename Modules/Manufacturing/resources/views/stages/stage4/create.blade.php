@extends('master')

@section('title', __('stages.stage4_create_title'))

@section('content')

<style>
    :root{
        --brand-1: #e67e22;
        --brand-2: #d35400;
        --muted: #6e7a81;
        --surface: #f5f7fa;
        --card: #ffffff;
        --success: #27ae60;
        --danger: #e74c3c;
        --radius: 12px;
    }

    .stage-container{ max-width:1200px; margin:26px auto; padding:20px; font-family: 'Segoe UI', Tahoma, Arial; color:#24303a }

    .stage-header{ display:flex; gap:14px; align-items:center; background: linear-gradient(90deg,var(--brand-1),var(--brand-2)); color:#fff; padding:20px 22px; border-radius:10px; box-shadow:0 10px 30px rgba(230,126,34,0.12) }
    .stage-header h1{ margin:0; font-size:20px }
    .stage-header p{ margin:0; opacity:0.95; font-size:13px }

    .form-section{ background:var(--card); padding:18px; border-radius:var(--radius); margin-top:18px; box-shadow:0 6px 18px rgba(10,30,60,0.04); border:1px solid rgba(34,47,62,0.04) }
    .section-title{ font-size:16px; color:var(--brand-1); font-weight:700 }

    .barcode-section{ background: linear-gradient(180deg,#fef5f1 0,#ffe8dc 100%); padding:20px; border-radius:10px; text-align:center; border:1px dashed rgba(230,126,34,0.06) }
    .barcode-input-wrapper{ max-width:720px; margin:0 auto; position:relative }
    .barcode-input{ width:100%; padding:16px 18px; border-radius:10px; border:2px solid rgba(230,126,34,0.12); font-size:16px; font-weight:600 }
    .barcode-icon{ position:absolute; left:16px; top:50%; transform:translateY(-50%); font-size:18px }

    .lafaf-display{ display:none; padding:14px; border-radius:10px; background:linear-gradient(180deg,#fef9f3,#fff4e6); border-left:4px solid var(--brand-1); margin-top:12px }
    .lafaf-display.active{ display:block }
    .lafaf-info{ display:grid; grid-template-columns:repeat(4,1fr); gap:12px }
    .info-item{ background:var(--card); padding:12px; border-radius:8px; text-align:center; box-shadow:0 4px 12px rgba(10,30,60,0.03) }
    .info-label{ font-size:13px; color:var(--muted); margin-bottom:6px; font-weight:600 }
    .info-value{ font-size:15px; font-weight:700; color:#22303a }

    .form-row{ display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:14px; margin-top:10px }
    .form-group label{ font-size:13px; color:var(--muted); font-weight:700; margin-bottom:6px; display:block }
    .form-control, .form-select{ width:100%; padding:10px 12px; border-radius:8px; border:1.5px solid #e7eef5; background:#fbfeff }
    .form-control[readonly]{ background:#f1f6f9; font-weight:600 }

    .boxes-list{ margin-top:20px }
    .box-item{ display:flex; justify-content:space-between; align-items:start; padding:18px; border-radius:12px; background:linear-gradient(135deg, #fef9f3 0%, #ffe8dc 100%); box-shadow:0 6px 18px rgba(10,30,60,0.03); margin-bottom:15px; border-right:4px solid #27ae60 }
    .box-info strong{ font-size:15px }

    .button-group{ display:flex; gap:10px; flex-wrap:wrap; margin-top:10px }
    .btn-primary, .btn-success, .btn-secondary, .btn-warning{ border:none; border-radius:8px; padding:10px 14px; font-weight:700; cursor:pointer }
    .btn-primary{ background:var(--brand-1); color:white }
    .btn-success{ background:var(--success); color:white }
    .btn-secondary{ background:#8e9aa4; color:white }
    .btn-warning{ background:#f39c12; color:white }

    .btn-print{ background:#27ae60; color:white; padding:10px 16px; border-radius:8px; border:none; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:6px; box-shadow:0 2px 8px rgba(39,174,96,0.3) }

    .empty-state{ padding:30px; text-align:center; color:#98a2a8 }

    .info-box{ background:linear-gradient(135deg,#fff9e6 0,#ffeaa7 100%); border-right:4px solid #f39c12; padding:15px; border-radius:8px; margin-bottom:20px }
    .info-box strong{ color:#e67e22; display:block; margin-bottom:8px }

    .divide-section{ background:linear-gradient(135deg,#e3f2fd 0,#bbdefb 100%); border-right:4px solid #2196f3; padding:15px; border-radius:8px; margin:15px 0 }
    .divide-section h4{ margin:0 0 12px 0; color:#1976d2; font-size:15px }

    @media (max-width:900px){ .form-row{ grid-template-columns:1fr } .lafaf-info{ grid-template-columns:repeat(2,1fr) } .stage-header{ flex-direction:column; text-align:center } }
</style>

<div class="stage-container">
    <!-- Header -->
    <div class="stage-header">
        <h1>
            <i class="fas fa-box"></i>
            {{ __('stages.stage4_create_title') }}
        </h1>
        <p>{{ __('stages.stage4_packaging_title') }}</p>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 style="margin: 0 0 15px 0; color: #e67e22;"><i class="fas fa-camera"></i> {{ __('stages.stage4_scan_stage3_barcode') }}</h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="lafafBarcode" class="barcode-input" placeholder="{{ __('stages.stage4_scan_or_press_enter') }}" autofocus>
            <span class="barcode-icon">📦</span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 10px;"><i class="fas fa-lightbulb"></i> {{ __('stages.stage4_scan_or_press_enter') }}</small>
    </div>

    <!-- Lafaf Display -->
    <div id="lafafDisplay" class="lafaf-display">
        <h4><i class="fas fa-circle-check"></i> {{ __('stages.stage4_coil_information') }}</h4>
        <div class="lafaf-info">
            <div class="info-item">
                <div class="info-label">{{ __('stages.stage1_barcode_label') }}</div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.stage2_input_weight_label') }}</div>
                <div class="info-value" id="displayMaterial">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.stage3_dye_color_label') }}</div>
                <div class="info-value" id="displayColor">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.stage4_box_weight_label') }}</div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
        </div>
    </div>

    <!-- Box Form -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-boxes"></i> {{ __('stages.stage4_box_data') }}</h3>

        <div class="info-box">
            <strong><i class="fas fa-thumbtack"></i> {{ __('stages.stage4_important_note') }}:</strong>
            <ul style="margin:8px 0 0 20px; color:#7f8c8d; font-size:13px;">
                <li><strong>{{ __('stages.stage4_weight_can_increase') }}</strong></li>
                <li>{{ __('stages.stage4_auto_divide_option') }}</li>
                <li>{{ __('stages.stage4_manual_add_option') }}</li>
                <li>{{ __('stages.stage4_each_gets_barcode') }}</li>
            </ul>
            @if($carton)
            <div style="margin-top:15px; padding:10px; background:#e8f5e9; border-radius:8px; border-right:3px solid #27ae60;">
                <strong style="color:#27ae60;"><i class="fas fa-box"></i> الكراتين المتاحة في المستودع:</strong>
                @php
                    $cartonQuantity = DB::table('material_details')
                        ->where('material_id', $carton->id)
                        ->where('quantity', '>', 0)
                        ->sum('quantity');
                @endphp
                <span style="font-size:16px; font-weight:700; color:#1e7e34; margin-right:10px;">
                    {{ number_format($cartonQuantity, 0) }} كرتونة
                </span>
                <small style="display:block; margin-top:5px; color:#666;">
                    <i class="fas fa-info-circle"></i> سيتم خصم كرتونة واحدة لكل كرتونة مضافة
                </small>
            </div>
            @else
            <div style="margin-top:15px; padding:10px; background:#fff3cd; border-radius:8px; border-right:3px solid #ffc107;">
                <strong style="color:#856404;"><i class="fas fa-exclamation-triangle"></i> تحذير:</strong>
                <span style="color:#856404;">لا توجد كراتين متاحة في المستودع</span>
            </div>
            @endif
        </div>

        <!-- Auto Divide Section -->
        <div class="divide-section">
            <h4><i class="fas fa-calculator"></i> {{ __('stages.stage4_auto_divide') }}</h4>
            <div class="form-row">
                <div class="form-group">
                    <label>{{ __('stages.stage4_total_boxes_weight') }}</label>
                    <input type="number" id="totalBoxesWeight" class="form-control" placeholder="{{ __('stages.stage4_example') }}: 110.5" step="0.001">
                    <small style="color: #7f8c8d; display: block; margin-top: 5px;">{{ __('stages.stage4_weight_can_be_more') }}</small>
                </div>
                <div class="form-group">
                    <label>{{ __('stages.stage4_boxes_count') }}</label>
                    <input type="number" id="boxesCount" class="form-control" placeholder="{{ __('stages.stage4_example') }}: 5" min="1">
                </div>
            </div>
            <button type="button" class="btn-warning" onclick="divideWeight()">
                <i class="fas fa-divide"></i> {{ __('stages.stage4_divide_weight_auto') }}
            </button>
        </div>

        <!-- Manual Box Entry -->
        <div style="margin-top: 20px;">
            <h4 style="color: #e67e22; margin-bottom: 12px;"><i class="fas fa-hand-pointer"></i> {{ __('stages.stage4_manual_add') }}</h4>
            <div class="form-row">
                <div class="form-group">
                    <label>{{ __('stages.stage4_box_weight_label') }} <span style="color:#e74c3c;">*</span></label>
                    <input type="number" id="boxWeight" class="form-control" placeholder="{{ __('stages.stage4_example') }}: 22.5" step="0.001">
                </div>
                <div class="form-group">
                    <label>{{ __('stages.stage4_notes_label') }}</label>
                    <input type="text" id="boxNotes" class="form-control" placeholder="{{ __('stages.stage4_additional_notes') }}">
                </div>
            </div>
            <div class="button-group">
                <button type="button" class="btn-primary" onclick="addBox()">
                    <i class="fas fa-plus"></i> {{ __('stages.stage4_add_box_button') }}
                </button>
                <button type="button" class="btn-secondary" onclick="clearForm()">
                    <i class="fas fa-sync"></i> {{ __('stages.stage4_clear_form_button') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Boxes List -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-clipboard"></i> {{ __('stages.stage4_added_boxes') }} (<span id="boxCount">0</span>)</h3>
        <div id="boxList" class="boxes-list">
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;opacity:0.3;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>{{ __('stages.stage4_no_boxes_added') }}</p>
            </div>
        </div>

        <!-- Summary -->
        <div id="summaryBox" style="display:none; background:linear-gradient(135deg,#e8f5e9 0,#c8e6c9 100%); padding:15px; border-radius:10px; margin-top:15px; border-right:4px solid #27ae60;">
            <h4 style="margin:0 0 10px 0; color:#2e7d32;"><i class="fas fa-chart-bar"></i> {{ __('stages.stage4_summary') }}</h4>
            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px;">
                <div style="background:white; padding:10px; border-radius:8px; text-align:center;">
                    <div style="font-size:12px; color:#7f8c8d; margin-bottom:4px;">{{ __('stages.stage4_total_boxes') }}</div>
                    <div style="font-size:20px; font-weight:700; color:#2e7d32;" id="summaryCount">0</div>
                </div>
                <div style="background:white; padding:10px; border-radius:8px; text-align:center;">
                    <div style="font-size:12px; color:#7f8c8d; margin-bottom:4px;">{{ __('stages.stage4_total_weight_sum') }}</div>
                    <div style="font-size:20px; font-weight:700; color:#e67e22;" id="summaryTotal">0</div>
                </div>
                <div style="background:white; padding:10px; border-radius:8px; text-align:center;">
                    <div style="font-size:12px; color:#7f8c8d; margin-bottom:4px;">{{ __('stages.stage4_lafaf_weight') }}</div>
                    <div style="font-size:20px; font-weight:700; color:#3498db;" id="summaryLafaf">0</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div style="display:flex; gap:15px; justify-content:center; margin-top:25px; padding-top:20px; border-top:2px solid #ecf0f1;">
        <button type="button" class="btn-success" onclick="finishOperation()" id="submitBtn" disabled style="padding:14px 32px; font-size:16px;">
            <i class="fas fa-check-double"></i> {{ __('stages.stage4_finish_shipment') }}
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('manufacturing.stage4.index') }}'">
            <i class="fas fa-times"></i> {{ __('app.cancel') }}
        </button>
    </div>
</div>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
let currentLafaf = null;
let boxes = [];

// Barcode scanner
document.getElementById('lafafBarcode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        loadLafaf(this.value.trim());
        this.value = '';
    }
});

function loadLafaf(barcode) {
    if (!barcode) {
        alert('{{ __("stages.stage4_please_enter_barcode") }}');
        return;
    }

    fetch(`{{ url('/stage4/get-lafaf-by-barcode') }}/${barcode}`)
        .then(response => {
            if (!response.ok) throw new Error('{{ __("stages.stage4_coil_not_found") }}');
            return response.json();
        })
        .then(result => {
            if (!result.success) throw new Error(result.message);

            const data = result.data;
            console.log('Lafaf data received:', data);

            const source = result.source || 'stage3';

            currentLafaf = {
                id: data.id || null,
                barcode: data.barcode,
                coil_number: data.coil_number || 'غير محدد',
                total_weight: parseFloat(data.total_weight),
                color: data.color || 'غير محدد',
                plastic_type: data.plastic_type || 'غير محدد',
                material_id: data.material_id,
                material_name: data.material_name || 'غير محدد',
                source: source
            };

            console.log('currentLafaf:', currentLafaf);

            // Display lafaf data
            document.getElementById('displayBarcode').textContent = currentLafaf.barcode;
            document.getElementById('displayMaterial').textContent = currentLafaf.material_name;
            document.getElementById('displayColor').textContent = currentLafaf.color;
            document.getElementById('displayWeight').textContent = currentLafaf.total_weight + ' كجم';
            document.getElementById('lafafDisplay').classList.add('active');

            // Update summary
            document.getElementById('summaryLafaf').textContent = currentLafaf.total_weight.toFixed(3) + ' كجم';

            // Focus on box weight
            document.getElementById('boxWeight').focus();

            showToast('{{ __("stages.stage4_coil_loaded_success") }}', 'success');
        })
        .catch(error => {
            alert('خطأ: ' + error.message);
            document.getElementById('lafafBarcode').focus();
        });
}

async function divideWeight() {
    if (!currentLafaf) {
        alert('⚠️ يرجى مسح باركود اللفاف أولاً!');
        document.getElementById('lafafBarcode').focus();
        return;
    }

    const totalWeight = parseFloat(document.getElementById('totalBoxesWeight').value);
    const count = parseInt(document.getElementById('boxesCount').value);

    if (!totalWeight || totalWeight <= 0) {
        alert('⚠️ يرجى إدخال الوزن الإجمالي!');
        document.getElementById('totalBoxesWeight').focus();
        return;
    }

    if (!count || count <= 0) {
        alert('⚠️ يرجى إدخال عدد الكراتين!');
        document.getElementById('boxesCount').focus();
        return;
    }

    // Calculate weight per box
    const weightPerBox = totalWeight / count;

    showToast(`{{ __("stages.stage4_saving_boxes") }}: ${count}...`, 'info');

    // Save each box
    for (let i = 0; i < count; i++) {
        const data = {
            lafaf_barcode: currentLafaf.barcode,
            lafaf_id: currentLafaf.id || null,
            source: currentLafaf.source || 'stage3',
            material_id: currentLafaf.material_id,
            weight: parseFloat(weightPerBox.toFixed(3)),
            notes: `كرتون ${i + 1} من ${count}`
        };

        console.log('Saving box', i + 1, 'with data:', data);

        try {
            const response = await fetch('{{ route("manufacturing.stage4.store-single") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            console.log('Result for box', i + 1, ':', result);

            if (result.success) {
                const box = {
                    id: result.data.box_id,
                    barcode: result.data.barcode,
                    box_number: result.data.box_number,
                    material_name: result.data.material_name,
                    weight: result.data.weight,
                    notes: data.notes,
                    saved: true
                };

                console.log('Box object created:', box);
                boxes.push(box);
            } else {
                throw new Error(result.message || 'حدث خطأ أثناء الحفظ');
            }
        } catch (error) {
            alert('❌ خطأ في حفظ الكرتون رقم ' + (i + 1) + ': ' + error.message);
            break;
        }
    }

    renderBoxes();
    showToast(`{{ __("stages.stage4_box_saved_success") }}: ${boxes.length}! (${weightPerBox.toFixed(3)} كجم)`, 'success');

    // Clear divide inputs
    document.getElementById('totalBoxesWeight').value = '';
    document.getElementById('boxesCount').value = '';
}

function addBox() {
    if (!currentLafaf) {
        alert('{{ __("stages.stage4_please_enter_barcode") }}');
        document.getElementById('lafafBarcode').focus();
        return;
    }

    const weight = document.getElementById('boxWeight').value;
    const notes = document.getElementById('boxNotes').value.trim();

    if (!weight || parseFloat(weight) <= 0) {
        alert('{{ __("stages.stage4_invalid_weight") }}');
        document.getElementById('boxWeight').focus();
        return;
    }

    const data = {
        lafaf_barcode: currentLafaf.barcode,
        lafaf_id: currentLafaf.id,
        material_id: currentLafaf.material_id,
        weight: parseFloat(weight),
        notes: notes
    };

    // Save box immediately
    const addBtn = event.target;
    addBtn.disabled = true;
    addBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("app.saving") }}...';

    fetch('{{ route("manufacturing.stage4.store-single") }}', {
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
            const box = {
                id: result.data.box_id,
                barcode: result.data.barcode,
                box_number: result.data.box_number,
                material_name: result.data.material_name,
                weight: result.data.weight,
                notes: notes,
                saved: true
            };

            boxes.push(box);
            renderBoxes();
            clearForm();

            showToast('{{ __("stages.stage4_box_saved_success") }}', 'success');

            document.getElementById('boxWeight').focus();
        } else {
            throw new Error(result.message || 'حدث خطأ أثناء الحفظ');
        }
    })
    .catch(error => {
        alert('{{ __("app.error") }}: ' + error.message);
    })
    .finally(() => {
        addBtn.disabled = false;
        addBtn.innerHTML = '<i class="fas fa-plus"></i> {{ __("stages.stage4_add_box_button") }}';
    });
}

function renderBoxes() {
    const list = document.getElementById('boxList');
    document.getElementById('boxCount').textContent = boxes.length;
    document.getElementById('submitBtn').disabled = boxes.length === 0;

    if (boxes.length === 0) {
        list.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;opacity:0.3;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>{{ __("stages.stage4_no_boxes_added") }}</p>
            </div>
        `;
        document.getElementById('summaryBox').style.display = 'none';
        return;
    }

    list.innerHTML = boxes.map((item, index) => `
        <div class="box-item">
            <div class="box-info" style="flex:1;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                    <strong style="color:#2c3e50; font-size:16px;">
                        <i class="fas fa-box" style="color:#27ae60;"></i> ${item.box_number || 'كرتون ' + (index + 1)}
                    </strong>
                    <span style="background:#27ae60; color:white; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600;">✓ {{ __("app.saved") }}</span>
                </div>
                <small style="display:block; line-height:1.6;">
                    <strong>المادة:</strong> ${item.material_name || 'غير محدد'} |
                    <strong>الباركود:</strong> <code style="background:#f8f9fa; padding:2px 6px; border-radius:4px; font-family:monospace;">${item.barcode || 'غير متوفر'}</code><br>
                    <strong>الوزن:</strong> ${item.weight} كجم
                    ${item.notes ? ' | 📝 <strong>ملاحظات:</strong> ' + item.notes : ''}
                </small>
            </div>
            <div style="display:flex; gap:8px;">
                <button class="btn-print" onclick="printBoxBarcode('${item.barcode}', '${item.box_number || '{{ __("stages.stage4_box") }}'}', '${item.material_name || '{{ __("app.not_specified") }}'}', ${item.weight}, '${currentLafaf ? currentLafaf.barcode : ''}')">
                    <i class="fas fa-print"></i> {{ __("app.print") }}
                </button>
            </div>
        </div>
    `).join('');

    // Update summary
    updateSummary();
}

function updateSummary() {
    if (boxes.length === 0) {
        document.getElementById('summaryBox').style.display = 'none';
        return;
    }

    const totalWeight = boxes.reduce((sum, box) => sum + parseFloat(box.weight), 0);

    document.getElementById('summaryCount').textContent = boxes.length;
    document.getElementById('summaryTotal').textContent = totalWeight.toFixed(3) + ' كجم';
    document.getElementById('summaryBox').style.display = 'block';
}

function finishOperation() {
    if (boxes.length === 0) {
        alert('⚠️ لا توجد كراتين محفوظة!');
        return;
    }

    showToast('✅ تم إنهاء العملية بنجاح!', 'success');
    setTimeout(() => {
        window.location.href = '{{ route("manufacturing.stage4.index") }}';
    }, 1000);
}

function clearForm() {
    document.getElementById('boxWeight').value = '';
    document.getElementById('boxNotes').value = '';
}

function printBoxBarcode(barcode, boxNumber, materialName, weight, lafafBarcode) {
    const printWindow = window.open('', '', 'height=650,width=850');
    printWindow.document.write('<html dir="rtl"><head><title>طباعة الباركود - ' + boxNumber + '</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
    printWindow.document.write('.barcode-container { background: white; padding: 50px; border-radius: 16px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); text-align: center; max-width: 550px; }');
    printWindow.document.write('.title { font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 4px solid #e67e22; }');
    printWindow.document.write('.barcode-display { font-size: 24px; color: #e67e22; font-weight: bold; margin: 20px 0; }');
    printWindow.document.write('.barcode-code { font-size: 22px; font-weight: bold; color: #2c3e50; margin: 25px 0; letter-spacing: 4px; font-family: "Courier New", monospace; }');
    printWindow.document.write('.info { margin-top: 30px; padding: 25px; background: #f8f9fa; border-radius: 10px; text-align: right; }');
    printWindow.document.write('.info-row { margin: 12px 0; display: flex; justify-content: space-between; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 16px; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 18px; }');
    printWindow.document.write('@media print { body { background: white; } }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<div class="barcode-container">');
    printWindow.document.write('<div class="title">باركود المرحلة الرابعة - كرتون</div>');
    printWindow.document.write('<div class="barcode-display">' + barcode + '</div>');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
    printWindow.document.write('<div class="info">');
    printWindow.document.write('<div class="info-row"><span class="label">المادة:</span><span class="value">' + materialName + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">الوزن:</span><span class="value">' + weight + ' كجم</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">باركود اللفاف:</span><span class="value">' + lafafBarcode + '</span></div>');
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
