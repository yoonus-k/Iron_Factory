@extends('master')

@section('title', __('stages.stage2_processing'))

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
            {{ __('stages.stage2_processing_stands') }}
        </h1>
        <p>{{ __('stages.stage2_scan_barcode_and_add_processing') }}</p>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 style="margin: 0 0 15px 0; color: #0066B2;"><i class="fas fa-camera"></i> {{ __('stages.stage2_scan_stand_barcode') }} <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_scan_stand_barcode_from_phase1') }}</span></span></h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="standBarcode" class="barcode-input" placeholder="{{ __('stages.stage2_scan_or_type_barcode') }}" autofocus>
            <span class="barcode-icon">🔧</span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 10px;"><i class="fas fa-lightbulb"></i> <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_scan_barcode_or_press_enter') }}</span></span></small>
    </div>

    <!-- Stand Display -->
    <div id="standDisplay" class="stand-display">
        <h4><i class="fas fa-circle-check"></i> {{ __('stages.stand_data') }}</h4>
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
        <h3 class="section-title"><i class="fas fa-edit"></i> {{ __('stages.stage2_processing_data') }}</h3>

        <div class="info-box">
            <div class="info-box-header">
                <strong><i class="fas fa-thumbtack"></i> {{ __('stages.important_note') }}: <span class="info-tooltip">?<span class="tooltip-text"><strong>{{ __('stages.stage2_waste_calculation_formula') }}:</strong><br><br>• {{ __('stages.stage2_formula') }}<br><br>• {{ __('stages.stage2_default_waste') }}<br><br>• {{ __('stages.stage2_input_weight_auto_filled') }}</span></span></strong>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>{{ __('stages.processing_type') }} <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_choose_process_type') }}</span></span></label>
                <select id="processType" class="form-select">
                    <option value="">{{ __('stages.stage2_select_processing_type') }}</option>
                    <option value="heating">{{ __('stages.process_heating') }}</option>
                    <option value="cooling">{{ __('stages.process_cooling') }}</option>
                    <option value="cutting">{{ __('stages.process_cutting') }}</option>
                    <option value="rolling">{{ __('stages.process_rolling') }}</option>
                    <option value="shaping">{{ __('stages.process_shaping') }}</option>
                    <option value="polishing">{{ __('stages.process_polishing') }}</option>
                </select>
            </div>


            <div class="form-group">
                <label>{{ __('stages.input_weight_label') }} <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_total_weight_before_processing') }}</span></span></label>
                <input type="number" id="inputWeight" class="form-control" step="0.01" readonly style="background: #e8f4f8; font-weight: 600;">
                <small style="color: #27ae60; display: block; margin-top: 5px;"><i class="fas fa-chart-bar"></i> <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_input_weight_auto_filled_tooltip') }}</span></span></small>
            </div>

        </div>


        <div class="form-row">
            <div class="form-group">
                <label>{{ __('stages.output_weight_label') }} <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_weight_after_processing') }}</span></span></label>
                <input type="number" id="outputWeight" class="form-control" step="0.01">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-lightbulb"></i> <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_weight_after_treatment') }}</span></span></small>
            </div>

            <div class="form-group">
                <label>{{ __('stages.waste_amount_label') }} <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_difference_between_weights') }}</span></span></label>
                <input type="number" id="wasteAmount" class="form-control" step="0.01" readonly style="background: #ecf0f1;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-percent"></i> {{ __('stages.waste_percentage') }}: <span id="wastePercentDisplay">0%</span> <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_waste_percentage_tooltip') }}</span></span></small>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label>{{ __('stages.processing_details_label') }} <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_additional_processing_details') }}</span></span></label>
                <textarea id="processDetails" class="form-control" placeholder="{{ __('stages.stage2_processing_details_placeholder') }}"></textarea>
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-sticky-note"></i> <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_add_processing_details') }}</span></span></small>
            </div>

            <div class="form-group">
                <label>{{ __('stages.notes_label') }} <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_additional_notes') }}</span></span></label>
                <textarea id="notes" class="form-control" placeholder="{{ __('stages.placeholder_notes') }}"></textarea>
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-comment"></i> <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_add_any_notes') }}</span></span></small>
            </div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-primary" onclick="addProcessed()">
                <i class="fas fa-plus"></i> {{ __('stages.stage2_add_processing') }}
            </button>
            <button type="button" class="btn-secondary" onclick="clearForm()">
                <i class="fas fa-sync"></i> {{ __('stages.clear_form') }}
            </button>
        </div>
    </div>

    <!-- Processed List -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-clipboard"></i> {{ __('stages.stage2_added_processings') }} (<span id="processedCount">0</span>)</h3>
        <div id="processedList" class="processed-list">
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>{{ __('stages.stage2_no_processings_added_yet') }}</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="form-actions">
        <button type="button" class="btn-success" onclick="finishOperation()" id="submitBtn" disabled>
            <i class="fas fa-check-double"></i> {{ __('stages.finish_operation') }}
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('manufacturing.stage2.index') }}'">
            <i class="fas fa-times"></i> {{ __('stages.cancel_button') }}
        </button>
    </div>
</div>

<script>
let processedItems = [];
let currentStand = null;

// تم إزالة الحفظ المحلي لأن النظام يحفظ مباشرة في قاعدة البيانات

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

    // Fetch data from API - استخدام stage2 للحصول على البيانات من المصدرين
    fetch(`/stage2/get-by-barcode/${barcode}`)
        .then(response => {
            // التعامل مع الاستجابات غير الناجحة
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'لم يتم العثور على البيانات');
                });
            }
            return response.json();
        })
        .then(result => {
            // التحقق من حالة blocked
            if (result.blocked) {
                Swal.fire({
                    icon: 'error',
                    title: '⛔ غير مسموح',
                    text: result.message,
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#dc3545',
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'swal2-rtl'
                    }
                });
                document.getElementById('standBarcode').focus();
                return;
            }
            
            if (!result.success) throw new Error(result.message);

            const data = result.data;
            const source = result.source || 'stage1'; // stage1 أو warehouse_direct

            currentStand = {
                id: data.id || null,
                barcode: data.barcode,
                wire_size: data.wire_size || '0',
                weight: parseFloat(data.remaining_weight),
                material_id: data.material_id,
                source: source
            };

            // Display stand data
            document.getElementById('displayBarcode').textContent = currentStand.barcode;
            document.getElementById('displayWireSize').textContent = currentStand.wire_size + ' مم';

            const displayWeightElement = document.getElementById('displayWeight');
            if (displayWeightElement) {
                displayWeightElement.textContent = currentStand.weight + ' كجم';
            }

            document.getElementById('standDisplay').classList.add('active');

            // Fill input weight automatically
            const inputWeightElement = document.getElementById('inputWeight');
            if (inputWeightElement) {
                inputWeightElement.value = currentStand.weight;
            }

            // Calculate expected output weight (default 3% waste)
            const expectedWaste = currentStand.weight * 0.03;
            const expectedOutput = currentStand.weight - expectedWaste;

            const outputWeightElement = document.getElementById('outputWeight');
            if (outputWeightElement) {
                outputWeightElement.value = '';
            }

            // Calculate initial waste
            calculateWaste();

            // Focus on process type
            document.getElementById('processType').focus();

            // Show success message
            showToast('{{ __("stages.stand_data_loaded_successfully") }}', 'success');
        })
        .catch(error => {
            alert('{{ __("stages.error_label") }}: ' + error.message);
            document.getElementById('standBarcode').focus();
        });
}

function calculateWaste() {
    const inputWeightElement = document.getElementById('inputWeight');
    const outputWeightElement = document.getElementById('outputWeight');
    const wasteAmountElement = document.getElementById('wasteAmount');
    const wastePercentElement = document.getElementById('wastePercentDisplay');

    const inputWeight = inputWeightElement ? (parseFloat(inputWeightElement.value) || 0) : 0;
    const outputWeight = outputWeightElement ? (parseFloat(outputWeightElement.value) || 0) : 0;

    if (inputWeight > 0 && outputWeight > 0) {
        const wasteAmount = (inputWeight - outputWeight).toFixed(2);
        const wastePercent = ((inputWeight - outputWeight) / inputWeight * 100).toFixed(2);

        if (wasteAmountElement) {
            wasteAmountElement.value = wasteAmount;
        }
        if (wastePercentElement) {
            wastePercentElement.textContent = wastePercent + '%';
        }
    } else {
        if (wasteAmountElement) {
            wasteAmountElement.value = '0';
        }
        if (wastePercentElement) {
            wastePercentElement.textContent = '0%';
        }
    }
}

function addProcessed() {
    if (!currentStand) {
        alert('⚠️ يرجى مسح باركود الاستاند أولاً!');
        document.getElementById('standBarcode').focus();
        return;
    }

    const processType = document.getElementById('processType').value;
    const processDetails = document.getElementById('processDetails').value.trim();
    const notes = document.getElementById('notes').value.trim();

    // حساب الأوزان من العناصر أو من currentStand إذا كانت مخفية
    const inputWeightElement = document.getElementById('inputWeight');
    const outputWeightElement = document.getElementById('outputWeight');
    const wasteAmountElement = document.getElementById('wasteAmount');

    const inputWeight = inputWeightElement ? inputWeightElement.value : currentStand.weight;
    const outputWeight = outputWeightElement ? outputWeightElement.value : (currentStand.weight * 0.97); // افتراض 3% هدر
    const wasteAmount = wasteAmountElement ? (wasteAmountElement.value || 0) : (currentStand.weight * 0.03);

    if (!processType || !inputWeight || !outputWeight) {
        alert('⚠️ يرجى ملء جميع الحقول المطلوبة!');
        return;
    }

    const data = {
        stage1_id: currentStand.id || null, // null إذا كان warehouse_direct
        stage1_barcode: currentStand.barcode,
        source: currentStand.source || 'stage1', // إضافة المصدر
        material_id: currentStand.material_id || null,
        input_weight: parseFloat(inputWeight) || 0,
        process_type: processType,
        total_weight: parseFloat(outputWeight) || 0,
        waste_weight: parseFloat(wasteAmount) || 0,
        net_weight: parseFloat(outputWeight) || 0,
        process_details: processDetails,
        notes: notes
    };

    // حفظ فوري للمعالجة
    const addBtn = event.target;
    addBtn.disabled = true;
    addBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';

    fetch('{{ route("manufacturing.stage2.store-single") }}', {
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
        console.log('📥 Server Response:', result);
        
        // 🔥 فحص pending_approval أولاً قبل success
        if (result.pending_approval) {
            // تم الحفظ لكن في انتظار الموافقة بسبب تجاوز نسبة الهدر
            const processed = {
                id: result.data.stage2_id,
                stand_number: result.data.stand_number,
                barcode: result.data.barcode,
                stage1_barcode: currentStand.barcode,
                process_type: processType,
                total_weight: parseFloat(outputWeight),
                waste_weight: parseFloat(wasteAmount),
                net_weight: result.data.net_weight,
                material_name: result.data.material_name,
                process_details: processDetails,
                notes: notes,
                saved: true,
                pending_approval: true,
                status: 'pending_approval'
            };

            processedItems.push(processed);
            renderProcessed();
            clearForm();
            
            // مسح بيانات الباركود والاستاند الحالي
            currentStand = null;
            document.getElementById('standDisplay').classList.remove('active');

            // عرض رسالة SweetAlert مع أيقونة خطأ
            Swal.fire({
                icon: 'error',
                title: result.alert_title || '⛔ تم إيقاف الانتقال للمرحلة الثالثة',
                html: result.alert_message,
                confirmButtonText: 'فهمت',
                confirmButtonColor: '#dc3545',
                allowOutsideClick: false,
                width: '600px',
                customClass: {
                    popup: 'swal2-rtl',
                    title: 'text-danger'
                }
            });
            
            // Focus on barcode for next scan
            document.getElementById('standBarcode').focus();
            return; // إنهاء التنفيذ هنا
        }
        
        if (result.success) {
            // إضافة إلى القائمة المحلية مع الباركود الحقيقي
            const processed = {
                id: result.data.stage2_id,
                stand_number: result.data.stand_number,
                barcode: result.data.stage2_barcode,
                stage1_barcode: currentStand.barcode,
                process_type: processType,
                total_weight: parseFloat(outputWeight),
                waste_weight: parseFloat(wasteAmount),
                net_weight: result.data.net_weight,
                material_name: result.data.material_name,
                process_details: processDetails,
                notes: notes,
                saved: true // علامة أنه محفوظ
            };

            processedItems.push(processed);
            renderProcessed();
            clearForm();
            
            // مسح بيانات الباركود والاستاند الحالي
            currentStand = null;
            document.getElementById('standDisplay').classList.remove('active');

            showToast('✅ تم حفظ المعالجة بنجاح', 'success');

            // Focus on barcode for next scan
            document.getElementById('standBarcode').focus();
        } else {
            throw new Error(result.message || 'حدث خطأ أثناء الحفظ');
        }
    })
    .catch(error => {
        // عرض رسالة خطأ باستخدام SweetAlert
        Swal.fire({
            icon: 'error',
            title: '❌ خطأ',
            text: error.message,
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#dc3545',
            customClass: {
                popup: 'swal2-rtl'
            }
        });
        
        // Focus back on barcode input
        document.getElementById('standBarcode').focus();
    })
    .finally(() => {
        addBtn.disabled = false;
        addBtn.innerHTML = '<i class="fas fa-plus"></i> {{ __("stages.stage2_add_processing") }}';
    });
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
        <div class="processed-item" style="border-right: 4px solid #27ae60; background: linear-gradient(135deg, #f8fcff 0%, #e8f8f5 100%); display: flex; justify-content: space-between; align-items: start; padding: 18px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 4px 12px rgba(39, 174, 96, 0.1);">
            <div class="processed-info" style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <strong style="color: #2c3e50; font-size: 16px;">
                        <i class="fas fa-cog" style="color: #27ae60;"></i> ${item.stand_number} → ${processTypeNames[item.process_type]}
                    </strong>
                    <span style="background: #27ae60; color: white; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">✓ محفوظ</span>
                </div>
                <small style="display: block; line-height: 1.6;">
                    <strong>المادة:</strong> ${item.material_name} |
                    <strong>الباركود:</strong> <code style="background: #f8f9fa; padding: 2px 6px; border-radius: 4px; font-family: monospace;">${item.barcode}</code><br>
                    <strong>وزن إجمالي:</strong> ${item.total_weight} كجم |
                    <strong>صافي:</strong> ${item.net_weight} كجم |
                    <strong>هدر:</strong> ${item.waste_weight} كجم
                    ${item.process_details ? '<br>📝 <strong>تفاصيل:</strong> ' + item.process_details : ''}
                    ${item.notes ? '<br>💬 <strong>ملاحظات:</strong> ' + item.notes : ''}
                </small>
            </div>
            <div class="stand-actions" style="display: flex; gap: 8px;">
                <button class="btn-print" onclick="printStage2Barcode('${item.barcode}', '${item.stand_number}', '${item.material_name}', ${item.net_weight})" style="background: #27ae60; color: white; border: none; padding: 10px 16px; border-radius: 8px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);">
                    <i class="fas fa-print"></i> طباعة الباركود
                </button>
            </div>
        </div>
    `).join('');
}

// تم إزالة وظيفة الحذف لأن المعالجات محفوظة مباشرة في قاعدة البيانات

function clearForm() {
    // Keep current stand data
    document.getElementById('processType').value = '';
    document.getElementById('processDetails').value = '';
    document.getElementById('notes').value = '';

    const inputWeightElement = document.getElementById('inputWeight');
    const outputWeightElement = document.getElementById('outputWeight');
    const wasteAmountElement = document.getElementById('wasteAmount');
    const wastePercentElement = document.getElementById('wastePercentDisplay');

    if (inputWeightElement) {
        inputWeightElement.value = '';
    }
    if (outputWeightElement) {
        outputWeightElement.value = '';
    }
    if (wasteAmountElement) {
        wasteAmountElement.value = '';
    }
    if (wastePercentElement) {
        wastePercentElement.textContent = '0%';
    }

    // Reset input weight from current stand
    if (currentStand && inputWeightElement) {
        inputWeightElement.value = currentStand.weight;
    }
}

function finishOperation() {
    if (processedItems.length === 0) {
        alert('⚠️ لا توجد معالجات محفوظة!');
        return;
    }

    // جميع العناصر محفوظة مسبقاً، فقط انتقل للصفحة الرئيسية
    showToast('✅ تم إنهاء العملية بنجاح!', 'success');
    setTimeout(() => {
        window.location.href = '{{ route("manufacturing.stage2.index") }}';
    }, 1000);
}

// تم إزالة وظيفة الحفظ المحلي

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

// تم إزالة نافذة الباركودات الجماعية لأن الطباعة تتم فورياً لكل معالجة

// عرض نافذة الباركودات (غير مستخدمة)
function old_showBarcodesModal(barcodes) {
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
    printWindow.document.write('<html dir="rtl"><head><title>{{ __("stages.print_barcode") }} - {{ __("stages.stage2_title") }}</title>');
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
    printWindow.document.write('<div class="title">{{ __("stages.barcode_title") }}</div>');
    printWindow.document.write('<div class="stand-number">' + standNumber + '</div>');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
    printWindow.document.write('<div class="info">');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.material_label") }}:</span><span class="value">' + materialName + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.net_weight_label") }}:</span><span class="value">' + netWeight + ' {{ __("stages.kg_unit") }}</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.date_label_print") }}:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
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
