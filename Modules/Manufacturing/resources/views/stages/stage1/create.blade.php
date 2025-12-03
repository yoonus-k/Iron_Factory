@extends('master')

@section('title', __('stages.stage1_division_materials'))

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
            {{ __('stages.stage1_title') }}
        </h1>
        <p>{{ __('stages.stage1_subtitle') }}</p>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 class="section-title"><i class="fas fa-barcode"></i> {{ __('stages.scan_barcode') }}</h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="materialBarcode" class="barcode-input" placeholder="{{ __('stages.scan_or_write_barcode') }}" autofocus>
            <span class="barcode-icon"><i class="fas fa-tag"></i></span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 20px; font-size: 16px;"><i class="fas fa-lightbulb"></i> {{ __('stages.scan_hint') }}</small>
    </div>

    <!-- Material Display -->
    <div id="materialDisplay" class="material-display">
        <h4><i class="fas fa-circle-check"></i> {{ __('stages.material_data') }}</h4>
        <div class="material-info">
            <div class="info-item">
                <div class="info-label">{{ __('stages.barcode') }}</div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.material_type') }}</div>
                <div class="info-value" id="displayMaterialType">-</div>
            </div>

            <div class="info-item">
                <div class="info-label">{{ __('stages.weight_transferred_production') }}</div>
                <div class="info-value" id="displayWeight">-</div>
            </div>

        </div>
    </div>

    <!-- Stand Form -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-target"></i> {{ __('stages.select_stand') }}</h3>

        <div class="info-box">
            <strong><i class="fas fa-thumbtack"></i> {{ __('stages.important_note') }}:</strong>
            <ul>
                <li><strong>{{ __('stages.net_weight_formula') }}</strong></li>
                <li>{{ __('stages.example_calculation') }}</li>
                <li>{{ __('stages.stand_status_change') }}</li>
            </ul>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="standSelect"><i class="fas fa-bullseye"></i> {{ __('stages.select_available_stand') }} <span class="required">*</span></label>
                <select id="standSelect" class="form-control" onchange="loadStand()" style="font-size: 17px; padding: 16px;">
                    <option value="">-- {{ __('stages.select_stand_from_list') }} --</option>
                </select>
                <small style="color: #7f8c8d; display: block; margin-top: 8px; font-size: 15px;"><i class="fas fa-lightbulb"></i> {{ __('stages.select_unused_stands') }}</small>
            </div>
        </div>

        <div id="standDetails" style="display: none; margin: 30px 0; padding: 30px; background: linear-gradient(135deg, #e8f8f5 0%, #d5f4e6 100%); border-radius: 12px; border-right: 5px solid #27ae60;">
            <h4 style="margin: 0 0 25px 0; color: #27ae60; font-size: 22px; display: flex; align-items: center; gap: 12px;"><i class="fas fa-box"></i> {{ __('stages.selected_stand') }}</h4>
            <div class="stand-info" style="grid-template-columns: repeat(2, 1fr);">
                <div class="info-item">
                    <div class="info-label">{{ __('stages.stand_number') }}</div>
                    <div class="info-value" id="selectedStandNumber" style="color: #27ae60; font-weight: 700;">-</div>
                </div>

                <div class="info-item">
                    <div class="info-label">{{ __('stages.stand_empty_weight') }}</div>
                    <div class="info-value" id="selectedStandWeight" style="color: #e67e22; font-weight: 700;">-</div>
                </div>

            </div>
        </div>


         <div class="form-row">
            <div class="form-group">
                <label for="wasteWeight"><i class="fas fa-trash-alt"></i> {{ __('stages.waste_weight') }}</label>
                <input type="number" id="wasteWeight" class="form-control" placeholder="{{ __('stages.auto_calculated') }}" step="0.01" readonly style="background: #ecf0f1;">
                <small style="color: #7f8c8d; display: block; margin-top: 8px; font-size: 15px;"><i class="fas fa-calculator"></i> يُحسب تلقائياً = (الوزن الكلي - الاستاند - الوزن الصافي)</small>
            </div>
            <div class="form-group">
                <label for="wastePercentage"><i class="fas fa-chart-bar"></i> {{ __('stages.waste_percentage') }}</label>
                <input type="number" id="wastePercentage" class="form-control" placeholder="0" step="0.01" readonly style="background: #ecf0f1;">
                <small style="color: #7f8c8d; display: block; margin-top: 8px; font-size: 15px;"><i class="fas fa-percent"></i> يُحسب تلقائياً = (وزن الهدر ÷ الوزن الكلي) × 100</small>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label for="totalWeight"><i class="fas fa-weight"></i> {{ __('stages.total_weight') }} <span class="required">*</span></label>
                <input type="number" id="totalWeight" class="form-control" placeholder="{{ __('stages.enter_total_weight') }}" step="0.01" oninput="calculateNetWeight()" style="font-size: 17px;">
                <small style="color: #7f8c8d; display: block; margin-top: 8px; font-size: 15px;"><i class="fas fa-balance-scale"></i> {{ __('stages.total_weight_hint') }}</small>
            </div>


            <div class="form-group">
                <label for="standWeight"><i class="fas fa-box-open"></i> {{ __('stages.stand_empty_weight') }}</label>
                <input type="number" id="standWeight" class="form-control" placeholder="{{ __('stages.fetched_automatically') }}" step="0.01" readonly style="background: #ecf0f1; font-weight: 600;">
                <small style="color: #7f8c8d; display: block; margin-top: 8px; font-size: 15px;"><i class="fas fa-sync"></i> {{ __('stages.auto_fetch_hint') }}</small>
            </div>

        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="netWeight"><i class="fas fa-check"></i> {{ __('stages.net_weight') }} <span class="required">*</span></label>
                <input type="number" id="netWeight" class="form-control" placeholder="{{ __('stages.auto_calculated') }}" step="0.01" oninput="calculateWasteFromNet()" style="background: linear-gradient(135deg, #d5f4e6 0%, #e8f8f5 100%); font-weight: 700; font-size: 22px; text-align: center; color: #27ae60; border: 3px solid #27ae60; border-radius: 12px;">
                <small style="color: #27ae60; display: block; margin-top: 10px; font-weight: 600; font-size: 16px;"><i class="fas fa-calculator"></i> يُحسب تلقائياً = (الوزن الكلي - الاستاند)، يمكن تعديله لحساب الهدر</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="notes"><i class="fas fa-sticky-note"></i> {{ __('stages.notes') }}</label>
                <textarea id="notes" class="form-control" placeholder="{{ __('stages.optional_notes') }}" rows="4"></textarea>
                <small style="color: #7f8c8d; display: block; margin-top: 8px; font-size: 15px;"><i class="fas fa-sticky-note"></i> {{ __('stages.add_any_notes') }}</small>
            </div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-primary" onclick="addProcessedStand()">
                <i class="fas fa-plus"></i> {{ __('stages.add_to_list') }}
            </button>
            <button type="button" class="btn-secondary" onclick="clearForm()">
                <i class="fas fa-sync"></i> {{ __('stages.clear_form') }}
            </button>
        </div>
    </div>

    <!-- Processed Stands List -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-list"></i> {{ __('stages.processed_stands') }} (<span id="standsCount">0</span>)</h3>
        <div id="standsList" class="stands-list">
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>{{ __('stages.no_processed_stands') }}</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="form-actions">
        <button type="button" class="btn-success" onclick="finishOperation()" id="finishBtn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-size: 18px; padding: 16px 32px;">
            <i class="fas fa-check-double"></i> {{ __('stages.finish_operation') }}
        </button>
        <button type="button" class="btn-secondary" onclick="if(confirm('{{ __('stages.confirm_exit') }}')) window.location.href='{{ route('manufacturing.stage1.index') }}'">
            <i class="fas fa-times"></i> {{ __('stages.cancel') }}
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
let materialTransferredWeight = 0; // وزن المادة المنقول للإنتاج (مرجع حساب الهدر)

// Load stands on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStandsList();

    const saved = localStorage.getItem('stage1_processed');
    if (saved) {
        const data = JSON.parse(saved);
        if (confirm('{{ __("stages.found_saved_data") }}')) {
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
        alert('⚠️ ' + '{{ __('stages.enter_raw_material_barcode') }}');
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
            showToast('✅ {{ __("stages.material_loaded_success") }}', 'success');
        } else {
            throw new Error(data.message || '{{ __("stages.material_not_found") }}');
        }
    })
    .catch(error => {
        console.error('{{ __("stages.error_label") }}:', error);
        showToast('❌ ' + error.message, 'error');
    });
}

function displayMaterialInfo(material) {
    document.getElementById('displayBarcode').textContent = material.barcode;
    document.getElementById('displayMaterialType').textContent = material.material_name || material.material_type || '{{ __("warehouse.undefined") }}';

    materialTransferredWeight = parseFloat(material.transferred_to_production || material.production_weight || 0) || 0;

    // فقط إذا كان العنصر موجود (بناءً على الصلاحية)
    const weightElement = document.getElementById('displayWeight');
    if (weightElement) {
        weightElement.textContent = materialTransferredWeight + ' ' + (material.unit_symbol || 'كجم');
    }

    document.getElementById('materialDisplay').classList.add('active');
    
    const netWeightElement = document.getElementById('netWeight');
    if (netWeightElement && !netWeightElement.value) {
        netWeightElement.value = materialTransferredWeight ? materialTransferredWeight.toFixed(2) : '';
    }
    calculateWasteFromNet();
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
        console.log('✅ Data received:', data);
        const select = document.getElementById('standSelect');
        select.innerHTML = '<option value="">-- {{ __("stages.select_stand_from_list") }} --</option>';

        if (data.stands && data.stands.length > 0) {
            console.log('Stands count:', data.stands.length);
            data.stands.forEach(stand => {
                const option = document.createElement('option');
                option.value = stand.id;
                option.textContent = `${stand.stand_number} - {{ __("stages.stand_empty_weight") }}: ${stand.weight} {{ __("stages.weight_unit") }}`;
                option.dataset.stand = JSON.stringify(stand);
                select.appendChild(option);
            });
            showToast(`✅ {{ __("stages.stands_loaded") }} ${data.stands.length} {{ __("stages.stands_available") }}`, 'success');
        } else {
            console.warn('No stands available');
            select.innerHTML = '<option value="">{{ __("stages.no_stands_available") }} - {{ __("stages.add_first_stand") }}</option>';
            showToast('⚠️ {{ __("stages.no_stands_available") }}', 'warning');
        }
    })
    .catch(error => {
        console.error('Error loading stands:', error);
        const select = document.getElementById('standSelect');
        select.innerHTML = '<option value="">{{ __("stages.failed_load_stands") }}</option>';
        showToast('❌ {{ __("stages.failed_load_stands") }}: ' + error.message, 'error');
    });
}

// Load selected stand
function loadStand() {
    const select = document.getElementById('standSelect');
    const selectedOption = select.options[select.selectedIndex];

    if (!selectedOption.value) {
        document.getElementById('standDetails').style.display = 'none';

        const standWeightElement = document.getElementById('standWeight');
        if (standWeightElement) {
            standWeightElement.value = '';
        }

        const netWeightElement = document.getElementById('netWeight');
        if (netWeightElement) {
            netWeightElement.value = '';
        }

        selectedStand = null;
        return;
    }

    selectedStand = JSON.parse(selectedOption.dataset.stand);

    document.getElementById('selectedStandNumber').textContent = selectedStand.stand_number;

    const standWeightElement = document.getElementById('selectedStandWeight');
    if (standWeightElement) {
        standWeightElement.textContent = selectedStand.weight + ' كجم';
    }

    const standWeightInputElement = document.getElementById('standWeight');
    if (standWeightInputElement) {
        standWeightInputElement.value = selectedStand.weight;
    }

    document.getElementById('standDetails').style.display = 'block';

    calculateNetWeight();
    showToast('✅ {{ __("stages.stand_loaded_success") }}', 'success');
}

// Calculate net weight automatically (total - stand)
function calculateNetWeight() {
    const total = parseFloat(document.getElementById('totalWeight').value) || 0;
    const standWeight = parseFloat(document.getElementById('standWeight').value) || 0;

    if (total > 0 && standWeight > 0) {
        // حساب الوزن الصافي تلقائياً (افتراضياً بدون هدر)
        const netWeight = total - standWeight;
        
        const netWeightElement = document.getElementById('netWeight');
        if (netWeightElement) {
            netWeightElement.value = netWeight.toFixed(2);
        }
        
        calculateWasteFromNet();
    } else {
        const netWeightElement = document.getElementById('netWeight');
        if (netWeightElement) {
            netWeightElement.value = '';
        }

        const wasteWeightElement = document.getElementById('wasteWeight');
        if (wasteWeightElement) {
            wasteWeightElement.value = '';
        }

        const wastePercentageElement = document.getElementById('wastePercentage');
        if (wastePercentageElement) {
            wastePercentageElement.value = '';
        }
    }
}

// Calculate waste when user modifies net weight manually
function calculateWasteFromNet() {
    const total = parseFloat(document.getElementById('totalWeight').value) || 0;
    const standWeight = parseFloat(document.getElementById('standWeight').value) || 0;
    const netWeight = parseFloat(document.getElementById('netWeight').value) || 0;
    
    let referenceWeight = 0;
    if (materialTransferredWeight > 0) {
        referenceWeight = materialTransferredWeight;
    } else if (total > 0 && standWeight > 0) {
        referenceWeight = total - standWeight;
    }

    const wasteWeightElement = document.getElementById('wasteWeight');

    if (referenceWeight > 0 && netWeight > 0) {
        const wasteWeight = Math.max(0, referenceWeight - netWeight);

        if (wasteWeightElement) {
            wasteWeightElement.value = wasteWeight.toFixed(2);
        }

        calculateWastePercentage(referenceWeight);
    } else {
        if (wasteWeightElement) {
            wasteWeightElement.value = '';
        }

        const wastePercentageElement = document.getElementById('wastePercentage');
        if (wastePercentageElement) {
            wastePercentageElement.value = '';
        }
    }
}

// Calculate waste percentage from weight
function calculateWastePercentage(materialWeight = null) {
    const wasteWeight = parseFloat(document.getElementById('wasteWeight').value) || 0;
    
    // إذا لم يتم تمرير الوزن المادي، احسبه من الوزن الكلي - الاستاند
    if (!materialWeight) {
        if (materialTransferredWeight > 0) {
            materialWeight = materialTransferredWeight;
        } else {
            const totalWeight = parseFloat(document.getElementById('totalWeight').value) || 0;
            const standWeight = parseFloat(document.getElementById('standWeight').value) || 0;
            materialWeight = totalWeight - standWeight;
        }
    }

    const wastePercentageElement = document.getElementById('wastePercentage');
    if (wastePercentageElement) {
        if (materialWeight > 0 && wasteWeight >= 0) {
            const percentage = (wasteWeight / materialWeight) * 100;
            wastePercentageElement.value = percentage.toFixed(2);
        } else {
            wastePercentageElement.value = '0.00';
        }
    }
}

function addProcessedStand() {
    if (!currentMaterial) {
        alert('⚠️ {{ __("stages.enter_raw_material_barcode") }}');
        return;
    }

    if (!selectedStand) {
        alert('⚠️ {{ __("stages.select_available_stand") }}');
        return;
    }

    const totalWeight = parseFloat(document.getElementById('totalWeight').value) || 0;

    if (!totalWeight) {
        alert('⚠️ {{ __("stages.enter_total_weight_required") }}');
        return;
    }

    // حساب الوزن الصافي من البيانات المتاحة (حتى لو كانت الحقول مخفية)
    const standWeight = selectedStand.weight || 0;
    const netWeight = totalWeight - standWeight;

    // التحقق من وجود العناصر قبل قراءة قيمها (للحقول الاختيارية)
    const wasteWeightElement = document.getElementById('wasteWeight');
    const wasteWeight = wasteWeightElement ? (parseFloat(wasteWeightElement.value) || 0) : 0;

    const wastePercentageElement = document.getElementById('wastePercentage');
    const wastePercentage = wastePercentageElement ? (parseFloat(wastePercentageElement.value) || 0) : 0;

    const notes = document.getElementById('notes').value.trim();

    // Disable add button temporarily
    const addBtn = event.target;
    addBtn.disabled = true;
    addBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("stages.saving") }}...';

    // إرسال للخادم فوراً
    const formData = {
        material_id: currentMaterial.id || currentMaterial.material_id,
        material_barcode: currentMaterial.barcode,
        stand_id: selectedStand.id,
        wire_size: 0,
        total_weight: totalWeight,
        net_weight: netWeight,
        stand_weight: standWeight,
        waste_weight: wasteWeight,
        waste_percentage: wastePercentage,
        cost: 0,
        notes: notes,
        _token: '{{ csrf_token() }}'
    };

    fetch('{{ route("manufacturing.stage1.store-single") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('📥 Server Response:', data);
        
        // 🔥 فحص pending_approval أولاً قبل success
        if (data.pending_approval) {
            // تم الحفظ لكن في انتظار الموافقة بسبب تجاوز نسبة الهدر
            const processedData = {
                id: data.data.stand_id,
                material_id: currentMaterial.id,
                material_barcode: currentMaterial.barcode,
                material_type: data.data.material_name,
                material_name: data.data.material_name,
                stand_id: selectedStand.id,
                stand_number: data.data.stand_number,
                stand_weight: standWeight,
                wire_size: 0,
                total_weight: totalWeight,
                net_weight: data.data.net_weight,
                waste_weight: data.data.waste_weight,
                waste_percentage: data.data.waste_percentage,
                cost: 0,
                notes: notes,
                barcode: data.data.barcode,
                saved: true,
                pending_approval: true,
                status: 'pending_approval',
                allowed_percentage: data.data.allowed_percentage
            };

            processedStands.push(processedData);
            renderStands();
            clearForm();
            saveOffline();
            loadStandsList();

            // عرض رسالة SweetAlert مع أيقونة خطأ
            Swal.fire({
                icon: 'error',
                title: data.alert_title || '⛔ تم إيقاف الانتقال للمرحلة الثانية',
                html: data.alert_message,
                confirmButtonText: 'فهمت',
                confirmButtonColor: '#dc3545',
                allowOutsideClick: false,
                width: '600px',
                customClass: {
                    popup: 'swal2-rtl',
                    title: 'text-danger'
                }
            });
            
            return; // إنهاء التنفيذ هنا
        }
        
        if (data.success) {
            // إضافة البيانات المحفوظة مع الباركود الحقيقي
            const processedData = {
                id: data.data.stand_id,
                material_id: currentMaterial.id,
                material_barcode: currentMaterial.barcode,
                material_type: data.data.material_name,
                material_name: data.data.material_name,
                stand_id: selectedStand.id,
                stand_number: data.data.stand_number,
                stand_weight: standWeight,
                wire_size: 0,
                total_weight: totalWeight,
                net_weight: data.data.net_weight,
                waste_weight: wasteWeight,
                waste_percentage: wastePercentage,
                cost: 0,
                notes: notes,
                barcode: data.data.barcode, // الباركود الحقيقي من الخادم
                saved: true // علامة أنه محفوظ
            };

            processedStands.push(processedData);
            renderStands();
            clearForm();
            saveOffline();
            loadStandsList(); // إعادة تحميل قائمة الاستاندات المتاحة

            showToast('✅ {{ __("stages.stand_saved_print_now") }}', 'success');
        } else if (data.suspended) {
            // 🔥 تم إيقاف المرحلة بسبب تجاوز نسبة الهدر
            Swal.fire({
                icon: 'warning',
                title: data.alert_title || 'تجاوز نسبة الهدر المسموح بها',
                html: `
                    <div style="text-align: right; direction: rtl;">
                        <p style="font-size: 16px; margin-bottom: 15px;">${data.alert_message}</p>
                        <div style="background: #fff3cd; padding: 15px; border-radius: 8px; border-right: 4px solid #ffc107; margin-top: 20px;">
                            <h5 style="color: #856404; margin-bottom: 10px;">
                                <i class="fas fa-exclamation-triangle"></i> تفاصيل الهدر:
                            </h5>
                            <table style="width: 100%; text-align: right;">
                                <tr>
                                    <td style="padding: 5px;"><strong>الوزن المدخل:</strong></td>
                                    <td style="padding: 5px;">${data.details.input_weight} كجم</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px;"><strong>الوزن الناتج:</strong></td>
                                    <td style="padding: 5px;">${data.details.output_weight} كجم</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px;"><strong>وزن الهدر:</strong></td>
                                    <td style="padding: 5px; color: #dc3545;">${data.details.waste_weight} كجم</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px;"><strong>نسبة الهدر:</strong></td>
                                    <td style="padding: 5px; color: #dc3545; font-weight: bold;">${data.details.waste_percentage}%</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px;"><strong>النسبة المسموح بها:</strong></td>
                                    <td style="padding: 5px; color: #28a745;">${data.details.allowed_percentage}%</td>
                                </tr>
                            </table>
                        </div>
                        <div style="background: #d1ecf1; padding: 15px; border-radius: 8px; border-right: 4px solid #17a2b8; margin-top: 15px;">
                            <p style="color: #0c5460; margin: 0;">
                                <i class="fas fa-info-circle"></i> 
                                <strong>تم إرسال تنبيه للإدارة للمراجعة والموافقة.</strong><br>
                                لن تتمكن من الاستمرار في هذه المرحلة حتى تتم الموافقة من قبل المسؤولين.
                            </p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'فهمت',
                confirmButtonColor: '#3085d6',
                width: '600px',
                allowOutsideClick: false,
                customClass: {
                    popup: 'swal2-rtl'
                }
            });
        } else {
            throw new Error(data.message || '{{ __("stages.error_saving") }}');
        }
    })
    .catch(error => {
        console.error('{{ __("stages.error_label") }}:', error);
        alert('❌ {{ __("stages.error_label") }}: ' + error.message);
    })
    .finally(() => {
        addBtn.disabled = false;
        addBtn.innerHTML = '<i class="fas fa-plus"></i> {{ __("stages.add_to_list") }}';
    });
}

function renderStands() {
    const list = document.getElementById('standsList');
    document.getElementById('standsCount').textContent = processedStands.length;

    if (processedStands.length === 0) {
        list.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>{{ __('stages.no_processed_stands') }}</p>
            </div>
        `;
        return;
    }

    list.innerHTML = processedStands.map(item => {
        // تحديد اللون والشارة حسب الحالة
        const isPending = item.status === 'pending_approval' || item.pending_approval === true;
        const borderColor = isPending ? '#ffc107' : '#27ae60';
        const iconColor = isPending ? '#ffc107' : '#27ae60';
        const iconClass = isPending ? 'fa-clock' : 'fa-check-circle';
        const badgeColor = isPending ? '#ffc107' : '#27ae60';
        const badgeText = isPending ? '⏸️ في انتظار الموافقة' : '{{ __("stages.saved_badge") }}';
        
        return `
            <div class="stand-item" style="border-right: 4px solid ${borderColor};">
                <div class="stand-info">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                        <strong style="font-size: 18px;"><i class="fas ${iconClass}" style="color: ${iconColor};"></i> ${item.stand_number}</strong>
                        <span style="background: ${badgeColor}; color: white; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">${badgeText}</span>
                    </div>
                    ${isPending ? `
                    <div style="background: #fff3cd; border-right: 3px solid #ffc107; padding: 8px 12px; margin-bottom: 8px; border-radius: 6px;">
                        <p style="margin: 0; color: #856404; font-size: 13px;">
                            <i class="fas fa-exclamation-triangle"></i> <strong>تنبيه:</strong> هذا الاستاند في انتظار موافقة الإدارة بسبب تجاوز نسبة الهدر المسموح بها (${item.waste_percentage}% > ${item.allowed_percentage || '3'}%).
                            لن يمكن استخدامه في المرحلة الثانية حتى تتم الموافقة عليه.
                        </p>
                    </div>
                    ` : ''}
                    <small style="display: block; line-height: 1.6;">
                        <strong>{{ __('stages.material_label') }}</strong> ${item.material_name || item.material_type} |
                        <strong>{{ __('stages.barcode_label') }}</strong> <code style="background: #f8f9fa; padding: 2px 6px; border-radius: 4px; font-family: monospace;">${item.barcode}</code><br>
                        <strong>{{ __('stages.total_weight_label') }}</strong> ${item.total_weight} {{ __('stages.weight_unit') }} |
                        <strong>{{ __('stages.net_weight_label') }}</strong> ${item.net_weight} {{ __('stages.weight_unit') }} |
                        <strong>{{ __('stages.stand_weight_label') }}</strong> ${item.stand_weight} {{ __('stages.weight_unit') }} |
                        <strong>{{ __('stages.waste_label') }}</strong> ${item.waste_weight || 0} {{ __('stages.weight_unit') }} (<span style="color: ${isPending ? '#dc3545' : 'inherit'}; font-weight: ${isPending ? 'bold' : 'normal'};">${item.waste_percentage || 0}%</span>)
                        ${item.notes ? '<br>📝 <strong>{{ __("stages.notes_label") }}</strong> ' + item.notes : ''}
                    </small>
                </div>
                <div class="stand-actions" style="display: flex; gap: 8px;">
                    <button class="btn-print" onclick='printStandBarcode(${JSON.stringify(item).replace(/'/g, "\\'")})' style="background: ${badgeColor};">
                        <i class="fas fa-print"></i> {{ __('stages.print_barcode_button') }}
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

function finishOperation() {
    if (processedStands.length === 0) {
        if (confirm('{{ __("stages.stands_added_confirm_exit") }}')) {
            localStorage.removeItem('stage1_processed');
            window.location.href = '{{ route("manufacturing.stage1.index") }}';
        }
        return;
    }

    const message = `{{ __("stages.stands_saved_confirm_home") }}`.replace('{count}', processedStands.length);
    if (confirm(message)) {
        localStorage.removeItem('stage1_processed');
        showToast('✅ {{ __("stages.operation_success") }}', 'success');
        setTimeout(() => {
            window.location.href = '{{ route("manufacturing.stage1.index") }}';
        }, 1000);
    }
}

function clearForm() {
    document.getElementById('standSelect').value = '';
    document.getElementById('standDetails').style.display = 'none';
    document.getElementById('totalWeight').value = '';

    const standWeightElement = document.getElementById('standWeight');
    if (standWeightElement) {
        standWeightElement.value = '';
    }

    const netWeightElement = document.getElementById('netWeight');
    if (netWeightElement) {
        netWeightElement.value = '';
    }

    const wasteWeightElement = document.getElementById('wasteWeight');
    if (wasteWeightElement) {
        wasteWeightElement.value = '';
    }

    const wastePercentageElement = document.getElementById('wastePercentage');
    if (wastePercentageElement) {
        wastePercentageElement.value = '';
    }

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

// دالة submitAll تم إزالتها لأن الحفظ أصبح فوري لكل استاند
// دالة showBarcodesModal تم إزالتها لأن الطباعة أصبحت فورية لكل استاند

// (deleted unused modal functions)
function _unused_showBarcodesModal(barcodes) {
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

function _unused_closeBarcodesModal() {
    const modal = document.getElementById('barcodesModal');
    if (modal) {
        modal.remove();
    }
    window.location.href = '{{ route("manufacturing.stage1.index") }}';
}

function _unused_printSingleBarcode(barcode, standNumber, materialName, netWeight) {
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

function _unused_printAllBarcodes(barcodes) {
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

// Print barcode for a saved stand
function printStandBarcode(stand) {
    if (!stand || !stand.barcode) {
        alert('❌ {{ __("stages.barcode_not_found") }}');
        return;
    }

    const printWindow = window.open('', '', 'height=650,width=850');
    printWindow.document.write('<html dir="rtl"><head><title>{{ __("stages.print_barcode_button") }} - ' + stand.stand_number + '</title>');
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
    printWindow.document.write('<div class="title">{{ __("stages.barcode_title") }}</div>');
    printWindow.document.write('<div class="stand-number">{{ __("stages.stand_label_print") }} ' + stand.stand_number + '</div>');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-code">' + stand.barcode + '</div>');
    printWindow.document.write('<div class="info">');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.material_label_print") }}</span><span class="value">' + (stand.material_name || stand.material_type) + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.net_weight_label_print") }}</span><span class="value">' + stand.net_weight + ' {{ __("stages.weight_unit") }}</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.date_label_print") }}</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
    printWindow.document.write('</div></div>');
    printWindow.document.write('<script>');
    printWindow.document.write('JsBarcode("#print-barcode", "' + stand.barcode + '", { format: "CODE128", width: 2, height: 90, displayValue: false, margin: 12 });');
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
    printWindow.document.write('<\/script></body></html>');
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
