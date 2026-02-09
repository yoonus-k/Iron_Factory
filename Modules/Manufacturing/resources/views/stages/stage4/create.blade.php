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

    .btn-primary, .btn-success, .btn-secondary, .btn-warning {
        border: none;
        border-radius: 8px;
        padding: 12px 20px;
        font-weight: 700;
        cursor: pointer;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    }
    .btn-primary { background: var(--brand-1); color: #fff; }
    .btn-success { background: var(--success); color: #fff; }
    .btn-secondary { background: #8e9aa4; color: #fff; }
    .btn-warning { background: #f39c12; color: #fff; }
    .btn-primary:active, .btn-success:active, .btn-secondary:active, .btn-warning:active {
        box-shadow: 0 4px 16px rgba(44, 62, 80, 0.18);
    }
    .btn-primary i, .btn-success i, .btn-secondary i, .btn-warning i {
        font-size: 1.2em;
    }

    .btn-print {
        background: #27ae60;
        color: #fff;
        padding: 12px 20px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 8px rgba(39,174,96,0.3);
        font-size: 1.1rem;
    }
    .btn-print i { font-size: 1.2em; }

    /* تحسين وضوح النص في الأزرار الكبيرة */
    #finishBtn {
        font-size: 1.2rem !important;
        padding: 16px 36px !important;
    }

    .empty-state{ padding:30px; text-align:center; color:#98a2a8 }

    .info-box{ background:linear-gradient(135deg,#fff9e6 0,#ffeaa7 100%); border-right:4px solid #f39c12; padding:15px; border-radius:8px; margin-bottom:20px }
    .info-box strong{ color:#e67e22; display:block; margin-bottom:8px }

    .divide-section{ background:linear-gradient(135deg,#e3f2fd 0,#bbdefb 100%); border-right:4px solid #2196f3; padding:15px; border-radius:8px; margin:15px 0 }
    .divide-section h4{ margin:0 0 12px 0; color:#1976d2; font-size:15px }

    @media (max-width:900px){ .form-row{ grid-template-columns:1fr } .lafaf-info{ grid-template-columns:repeat(2,1fr) } .stage-header{ flex-direction:column; text-align:center } }

    /* Pending Items Panel - تصميم موحد مع المرحلة الثانية */
    .pending-panel{ border:1px solid rgba(231,76,60,0.2); background:linear-gradient(135deg,#fff5f5 0,#ffecec 100%); border-radius:12px; padding:18px; margin-top:18px; }
    .pending-panel-header{ display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; }
    .pending-panel-actions{ display:flex; gap:10px; flex-wrap:wrap; }
    .pending-panel-actions a,
    .pending-panel-actions button{ border:none; border-radius:8px; padding:10px 16px; font-weight:700; cursor:pointer; text-decoration:none; }
    .pending-panel-actions a{ background:#ffffff; color:#d35400; border:1px solid rgba(231,76,60,0.3); }
    .pending-panel-actions button{ background:#d35400; color:#fff; }
    .pending-panel .helper-text{ margin:10px 0; color:#8c2f2f; font-weight:600; }
    .pending-stands-list{ display:flex; flex-direction:column; gap:12px; margin-top:16px; }
    .pending-stand-card{ display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; padding:16px; border-radius:12px; border:1px solid rgba(231,76,60,0.15); background:linear-gradient(180deg,#fff5f5,#ffecec); box-shadow:0 8px 24px rgba(231,76,60,0.08); }
    .pending-stand-info{ display:flex; flex-direction:column; gap:6px; color:#b33939; }
    .pending-stand-info strong{ color:#631010; font-size:16px; }
    .pending-stand-actions{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
    .pending-stand-actions button{ border:none; border-radius:8px; padding:10px 18px; font-weight:700; cursor:pointer; }
    .pending-stand-actions .btn-continue{ background:#d35400; color:#fff; }
    .pending-stand-actions .btn-finish-stand{ background:#e74c3c; color:#fff; }
    .pending-stand-actions .btn-transfer-stand{ background:#9b59b6; color:#fff; }
    
    /* Pending Transfers Panel - طلبات النقل الواردة */
    .pending-transfers-panel{ background:linear-gradient(135deg,#e8f5e9 0%,#c8e6c9 100%); border:1px solid rgba(39,174,96,0.3); border-radius:12px; padding:18px; margin-top:18px; }
    .pending-transfer-card{ display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; padding:16px; border-radius:12px; border:1px solid rgba(39,174,96,0.2); background:linear-gradient(180deg,#e8f5e9,#c8e6c9); box-shadow:0 8px 24px rgba(39,174,96,0.08); }
    .pending-transfer-info{ display:flex; flex-direction:column; gap:6px; color:#1e8449; }
    .pending-transfer-info strong{ color:#145a32; font-size:16px; }
    .pending-transfer-actions{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
    .pending-transfer-actions button{ border:none; border-radius:8px; padding:10px 18px; font-weight:700; cursor:pointer; }
    .btn-accept{ background:#27ae60; color:#fff; }
    .btn-accept:hover{ background:#1e8449; }
    .btn-reject{ background:#e74c3c; color:#fff; }
    .btn-reject:hover{ background:#c0392b; }
    
    /* Transfer Modal */
    .transfer-modal-overlay{ display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center; }
    .transfer-modal{ background:#fff; border-radius:16px; padding:24px; width:90%; max-width:500px; box-shadow:0 20px 60px rgba(0,0,0,0.2); }
    .transfer-modal-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:2px solid #e67e22; padding-bottom:15px; }
    .transfer-modal-header h3{ margin:0; color:#e67e22; font-size:18px; }
    .transfer-modal-close{ background:none; border:none; font-size:24px; cursor:pointer; color:#999; }
    .transfer-modal-close:hover{ color:#e74c3c; }
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
                <div class="info-label">{{ __('stages.stage_previous_barcode_label') }}</div>
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
                <small id="displayWeightDetails" style="display:block; color:#7f8c8d; font-weight:400; margin-top:4px;"></small>
            </div>
        </div>
    </div>

    <!-- Pending Lafafs Panel - لوحة اللفافات المتبقية للمستخدم الحالي - تصميم موحد مع المرحلة الثانية -->
    <div id="pendingBoxesPanel" class="form-section pending-panel" style="border:1px solid rgba(231,76,60,0.2);">
        <div class="pending-panel-header">
            <h3 class="section-title" style="color:#c0392b;">
                <i class="fas fa-exclamation-circle"></i>
                اللفافات المتبقية الخاصة بك (<span id="pendingBoxesCount">0</span>)
            </h3>
            <div class="pending-panel-actions">
                <button type="button" onclick="checkPendingBoxes()">
                    <i class="fas fa-sync-alt"></i> تحديث
                </button>
            </div>
        </div>
        <p class="helper-text">هذه اللفافات التي بدأت بتعبئتها ولا يزال لها وزن متبقي - يمكنك متابعة التعبئة أو نقل المتبقي لموظف آخر</p>
        <div id="pendingBoxesList" class="pending-stands-list"></div>
    </div>

    <!-- Pending Transfers Panel - طلبات النقل الواردة -->
    <div id="pendingTransfersPanel" class="form-section pending-transfers-panel" style="display:none;">
        <div class="pending-panel-header">
            <h3 class="section-title" style="color:#27ae60;">
                <i class="fas fa-exchange-alt"></i>
                طلبات النقل الواردة (<span id="pendingTransfersCount">0</span>)
            </h3>
            <div class="pending-panel-actions">
                <button type="button" onclick="checkPendingTransfers()" style="background:#27ae60;">
                    <i class="fas fa-sync-alt"></i> تحديث
                </button>
            </div>
        </div>
        <p class="helper-text" style="color:#1e8449;">هذه اللفافات التي تم نقلها إليك من موظفين آخرين - في انتظار موافقتك</p>
        <div id="pendingTransfersList" class="pending-stands-list"></div>
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
        <button type="button" class="btn-success" onclick="finishPackaging()" id="finishBtn" disabled style="padding:14px 32px; font-size:16px;">
            <i class="fas fa-check-circle"></i> إنهاء التعبئة وفحص الهدر
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('manufacturing.stage4.index') }}'">
            <i class="fas fa-times"></i> {{ __('app.cancel') }}
        </button>
    </div>
</div>

<!-- Transfer Modal - نقل اللفاف المتبقي لموظف آخر -->
<div id="transferModal" class="transfer-modal-overlay">
    <div class="transfer-modal">
        <div class="transfer-modal-header">
            <h3><i class="fas fa-exchange-alt"></i> نقل اللفاف المتبقي لموظف آخر</h3>
            <button type="button" class="transfer-modal-close" onclick="closeTransferModal()">&times;</button>
        </div>
        <div style="margin-bottom:15px; background:#f8f9fa; padding:12px; border-radius:8px; border-right:4px solid #e67e22;">
            <strong>باركود اللفاف:</strong> <code id="transferBoxBarcode" style="background:#e67e22; color:#fff; padding:2px 8px; border-radius:4px;">-</code><br>
            <strong>الوزن المتبقي:</strong> <span id="transferBoxWeight" style="color:#27ae60; font-weight:bold;">-</span> كجم
        </div>
        <div class="form-group" style="margin-bottom:15px;">
            <label style="font-weight:600;">اختر الموظف الجديد <span style="color:#e74c3c;">*</span></label>
            <select id="transferWorkerId" class="form-select" style="width:100%; padding:10px;">
                <option value="">-- اختر موظف --</option>
                @foreach(\App\Models\User::where('id', '!=', auth()->id())->orderBy('name')->get() as $worker)
                    <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin-bottom:15px;">
            <label style="font-weight:600;">سبب النقل</label>
            <select id="transferReason" class="form-select" style="width:100%; padding:10px;">
                <option value="انتهاء الوردية">انتهاء الوردية</option>
                <option value="توزيع العمل">توزيع العمل</option>
                <option value="طوارئ">طوارئ</option>
                <option value="أخرى">أخرى</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:20px;">
            <label style="font-weight:600;">ملاحظات</label>
            <textarea id="transferNotes" class="form-control" placeholder="ملاحظات إضافية (اختياري)" rows="2"></textarea>
        </div>
        <input type="hidden" id="transferBoxBarcodeValue">
        <div style="display:flex; gap:10px; justify-content:flex-end;">
            <button type="button" class="btn-secondary" onclick="closeTransferModal()">
                <i class="fas fa-times"></i> إلغاء
            </button>
            <button type="button" class="btn-primary" onclick="submitTransfer()" id="submitTransferBtn">
                <i class="fas fa-paper-plane"></i> إرسال طلب النقل
            </button>
        </div>
    </div>
</div>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
let currentLafaf = null;
let boxes = [];
let pendingItems = [];
let pendingItemsCount = 0;
let pendingBoxes = [];
let pendingBoxesCount = 0;
let pendingTransfers = [];
let pendingTransfersCount = 0;

// كائن الترجمات
const translations = {
    pending_boxes_title: 'الكراتين المعلقة الخاصة بك',
    pending_transfers_title: 'طلبات النقل الواردة',
    transfer_box: 'نقل الكرتون',
    accept_transfer: 'قبول',
    reject_transfer: 'رفض',
    from_worker: 'من',
    weight_label: 'الوزن',
    waste_label: 'الهدر',
    no_pending_boxes: 'لا توجد كراتين معلقة',
    no_pending_transfers: 'لا توجد طلبات نقل واردة',
    transfer_success: 'تم إرسال طلب النقل بنجاح',
    transfer_error: 'حدث خطأ أثناء النقل',
    accept_success: 'تم قبول النقل بنجاح',
    reject_success: 'تم رفض النقل',
    select_worker_required: 'يرجى اختيار الموظف',
    kg_unit: 'كجم',
    loading: 'جاري التحميل...',
    refresh: 'تحديث'
};

// تحميل العناصر المعلقة عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    checkPendingBoxes();
    checkPendingTransfers();
    
    // تحديث تلقائي كل 30 ثانية
    setInterval(() => {
        checkPendingBoxes();
        checkPendingTransfers();
    }, 30000);
});

// جلب الكراتين المعلقة للمستخدم الحالي
function checkPendingBoxes() {
    console.log('🔍 جاري التحقق من الكراتين المعلقة...');
    
    fetch('/stage4/pending-boxes', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        console.log('📥 Response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('📦 بيانات الكراتين المعلقة:', data);
        
        if (data.success) {
            pendingBoxes = data.items || [];
            pendingBoxesCount = data.count || 0;
            console.log('✅ تم جلب ' + pendingBoxesCount + ' كرتونة معلقة');
            renderPendingBoxesPanel();
        } else {
            console.error('❌ فشل في جلب البيانات:', data.message);
        }
    })
    .catch(error => {
        console.error('❌ خطأ في جلب الكراتين المعلقة:', error);
        // عرض رسالة خطأ في اللوحة
        const list = document.getElementById('pendingBoxesList');
        if (list) {
            list.innerHTML = '<div class="empty-state" style="padding:20px; text-align:center; color:#e74c3c;"><i class="fas fa-exclamation-triangle"></i> خطأ في التحميل: ' + error.message + '</div>';
        }
    });
}

// جلب طلبات النقل الواردة
function checkPendingTransfers() {
    console.log('🔍 جاري التحقق من طلبات النقل...');
    
    fetch('/stage4/pending-transfers', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        console.log('📥 Response status for transfers:', response.status);
        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('📦 بيانات طلبات النقل:', data);
        
        if (data.success) {
            pendingTransfers = data.transfers || [];
            pendingTransfersCount = data.count || 0;
            console.log('✅ تم جلب ' + pendingTransfersCount + ' طلب نقل');
            renderPendingTransfersPanel();
        } else {
            console.error('❌ فشل في جلب البيانات:', data.message);
        }
    })
    .catch(error => {
        console.error('❌ خطأ في جلب طلبات النقل:', error);
        // عرض رسالة خطأ في اللوحة
        const list = document.getElementById('pendingTransfersList');
        if (list) {
            list.innerHTML = '<div class="empty-state" style="padding:20px; text-align:center; color:#e74c3c;"><i class="fas fa-exclamation-triangle"></i> خطأ في التحميل: ' + error.message + '</div>';
        }
    });
}

// عرض لوحة اللفافات المتبقية - تصميم موحد مع المرحلة الثانية
function renderPendingBoxesPanel() {
    const panel = document.getElementById('pendingBoxesPanel');
    const list = document.getElementById('pendingBoxesList');
    const countSpan = document.getElementById('pendingBoxesCount');
    
    console.log('📦 renderPendingBoxesPanel:', { panel: !!panel, list: !!list, count: pendingBoxesCount });
    
    if (!panel || !list) {
        console.error('❌ عناصر لوحة اللفافات المتبقية غير موجودة!');
        return;
    }
    
    countSpan.textContent = pendingBoxesCount;
    panel.style.display = 'block';
    panel.style.visibility = 'visible';
    panel.style.opacity = '1';
    
    if (pendingBoxesCount === 0) {
        list.innerHTML = `
            <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; text-align: center; color: #7f8c8d;">
                <i class="fas fa-check-circle" style="color:#27ae60; font-size: 24px; margin-bottom: 10px;"></i>
                <br>
                <strong style="margin: 0 5px; display: block; margin-bottom: 8px;">لا توجد لفافات متبقية</strong>
                <div style="font-size: 14px;">ابدأ بمسح باركود لفاف جديد من المرحلة الثالثة</div>
            </div>
        `;
        return;
    }
    
    list.innerHTML = pendingBoxes.map(lafaf => {
        const remainingWeight = parseFloat(lafaf.remaining_weight || 0);
        const usedWeight = parseFloat(lafaf.used_weight || 0);
        const totalWeight = parseFloat(lafaf.net_weight || 0); // استخدام net_weight مباشرة
        const usagePercent = totalWeight > 0 ? Math.min(100, (usedWeight / totalWeight) * 100) : 0;
        const pendingBoxesCount = parseInt(lafaf.pending_boxes_count || 0);
        const hasRemainingWeight = remainingWeight > 0.01;
        const hasPendingBoxes = pendingBoxesCount > 0;
        const lafafStatus = lafaf.status;
        const isLafafPacked = lafafStatus === 'packed' || lafafStatus === 'completed';

        return `
            <div class="pending-stand-card">
                <div class="pending-stand-info">
                    <strong><i class="fas fa-circle-notch"></i> اللفاف: ${lafaf.barcode}</strong>
                    <span>المادة: ${lafaf.material_name || 'غير محدد'}</span>
                    ${lafaf.color ? `<span>اللون: ${lafaf.color}</span>` : ''}
                    <span>مستخدم: ${usedWeight.toFixed(3)} / ${totalWeight.toFixed(3)} كجم</span>
                    
                    <!-- شريط التقدم -->
                    <div style="margin-top:8px; background:#e0e0e0; border-radius:10px; height:20px; overflow:hidden; position:relative;">
                        <div style="background:linear-gradient(90deg,#27ae60,#2ecc71); height:100%; width:${usagePercent.toFixed(1)}%; transition:width 0.3s; border-radius:10px;"></div>
                        <span style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-size:12px; font-weight:700; color:#2c3e50;">${usagePercent.toFixed(1)}%</span>
                    </div>
                    
                    ${hasRemainingWeight ? `<span style="margin-top:5px;">المتبقي: <strong style="color:#e74c3c">${remainingWeight.toFixed(3)} كجم</strong></span>` : '<span style="margin-top:5px; color:#27ae60;"><i class="fas fa-check-double"></i> <strong>تم استهلاك المادة بالكامل</strong></span>'}
                    ${hasPendingBoxes ? `<span style="color:#e74c3c;"><i class="fas fa-box-open"></i> كراتين غير مكتملة: <strong>${pendingBoxesCount}</strong></span>` : ''}
                    ${isLafafPacked ? `<span style="color:#3498db;"><i class="fas fa-check-circle"></i> اللفاف: مكتمل</span>` : ''}
                </div>
                <div class="pending-stand-actions">
                    ${hasRemainingWeight ? `
                        <button class="btn-continue" type="button" onclick="loadPendingItem('${lafaf.barcode}')">
                            <i class="fas fa-play"></i> متابعة التعبئة
                        </button>
                    ` : ''}
                    ${hasPendingBoxes ? `
                        <button class="btn-finish-stand" type="button" onclick="finishLafaf('${lafaf.barcode}')">
                            <i class="fas fa-check-double"></i> إنهاء ${pendingBoxesCount} كرتون
                        </button>
                    ` : ''}
                    ${hasRemainingWeight ? `
                        <button class="btn-transfer-stand" type="button" onclick="openTransferModal('${lafaf.barcode}', ${remainingWeight.toFixed(3)})">
                            <i class="fas fa-share"></i> نقل لموظف آخر
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    }).join('');
}

// عرض لوحة طلبات النقل الواردة
function renderPendingTransfersPanel() {
    const panel = document.getElementById('pendingTransfersPanel');
    const list = document.getElementById('pendingTransfersList');
    const countSpan = document.getElementById('pendingTransfersCount');
    
    console.log('📨 renderPendingTransfersPanel:', { panel: !!panel, list: !!list, count: pendingTransfersCount });
    
    if (!panel || !list) {
        console.error('❌ عناصر لوحة طلبات النقل غير موجودة!');
        return;
    }
    
    countSpan.textContent = pendingTransfersCount;
    
    // إظهار اللوحة فقط إذا كان هناك طلبات
    if (pendingTransfersCount === 0) {
        panel.style.display = 'none';
        return;
    }
    
    panel.style.display = 'block';
    panel.style.visibility = 'visible';
    panel.style.opacity = '1';
    
    list.innerHTML = pendingTransfers.map(transfer => {
        const remainingWeight = parseFloat(transfer.remaining_weight || 0);
        const usedWeight = parseFloat(transfer.used_weight || 0);
        const totalWeight = parseFloat(transfer.total_weight || transfer.net_weight || 0);

        return `
            <div class="pending-transfer-card">
                <div class="pending-transfer-info">
                    <strong><i class="fas fa-box"></i> اللفاف: ${transfer.barcode}</strong>
                    <span>من الموظف: <strong>${transfer.sender_name}</strong></span>
                    <span>المادة: ${transfer.material_name || 'غير محدد'}</span>
                    <span>الوزن المتبقي: <strong style="color:#27ae60">${remainingWeight.toFixed(3)} كجم</strong></span>
                    ${transfer.reason ? `<span style="color:#666;"><i class="fas fa-comment"></i> ${transfer.reason}</span>` : ''}
                </div>
                <div class="pending-transfer-actions">
                    <button class="btn-accept" type="button" onclick="acceptTransfer('${transfer.barcode}')">
                        <i class="fas fa-check"></i> قبول
                    </button>
                    <button class="btn-reject" type="button" onclick="rejectTransfer('${transfer.barcode}')">
                        <i class="fas fa-times"></i> رفض
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

// فتح موديل النقل
function openTransferModal(barcode, weight) {
    document.getElementById('transferBoxBarcode').textContent = barcode;
    document.getElementById('transferBoxWeight').textContent = weight;
    document.getElementById('transferBoxBarcodeValue').value = barcode;
    document.getElementById('transferWorkerId').value = '';
    document.getElementById('transferReason').value = '';
    document.getElementById('transferNotes').value = '';
    document.getElementById('transferModal').style.display = 'flex';
}

// إغلاق موديل النقل
function closeTransferModal() {
    document.getElementById('transferModal').style.display = 'none';
}

// إنهاء اللفاف - تحويل جميع الكراتين المرتبطة إلى حالة مكتملة
function finishLafaf(barcode) {
    Swal.fire({
        title: 'تأكيد إنهاء اللفاف',
        html: `<div style="text-align:right; direction:rtl;">
            <p>هل أنت متأكد من إنهاء اللفاف <strong>${barcode}</strong>؟</p>
            <p style="color:#e74c3c; margin-top:10px;"><i class="fas fa-exclamation-triangle"></i> سيتم تحويل جميع الكراتين المرتبطة به إلى حالة مكتملة</p>
        </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#27ae60',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check-double"></i> نعم، إنهاء اللفاف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'جاري الإنهاء...',
                text: 'يرجى الانتظار',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/stage4/finish-lafaf', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ barcode: barcode })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    const boxesCount = data.boxes_count || data.data?.boxes_count || 0;
                    const updatedBoxes = data.updated_boxes_count || data.data?.updated_boxes_count || 0;
                    const waste = data.waste || data.data?.waste || 0;
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'تم إنهاء اللفاف بنجاح!',
                        html: `<div style="text-align:right; direction:rtl;">
                            <p><strong>عدد الكراتين الكلي:</strong> ${boxesCount} كرتون</p>
                            ${updatedBoxes > 0 ? `<p><strong>تم تحديث:</strong> ${updatedBoxes} كرتون إلى حالة مكتمل</p>` : ''}
                            ${waste > 0 ? `<p><strong>الهدر المحسوب:</strong> ${waste} كجم</p>` : ''}
                        </div>`,
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#27ae60'
                    });
                    checkPendingBoxes();
                    // إذا كان اللفاف المحمل حالياً هو نفسه، أعد تحميله
                    if (currentLafaf && currentLafaf.barcode === barcode) {
                        clearForm();
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: data.message || 'حدث خطأ أثناء إنهاء اللفاف',
                        confirmButtonColor: '#e74c3c'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('خطأ في إنهاء اللفاف:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ في الاتصال بالخادم',
                    confirmButtonColor: '#e74c3c'
                });
            });
        }
    });
}

// إرسال طلب النقل
function submitTransfer() {
    const barcode = document.getElementById('transferBoxBarcodeValue').value;
    const newWorkerId = document.getElementById('transferWorkerId').value;
    const reason = document.getElementById('transferReason').value;
    const notes = document.getElementById('transferNotes').value;
    
    if (!newWorkerId) {
        alert(translations.select_worker_required);
        return;
    }
    
    const btn = document.getElementById('submitTransferBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري النقل...';
    
    fetch('/stage4/transfer-box', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            barcode: barcode,
            new_worker_id: newWorkerId,
            reason: reason,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(translations.transfer_success, 'success');
            closeTransferModal();
            checkPendingBoxes();
        } else {
            showToast(data.message || translations.transfer_error, 'error');
        }
    })
    .catch(error => {
        console.error('خطأ في النقل:', error);
        showToast(translations.transfer_error, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال طلب النقل';
    });
}

// قبول طلب النقل
function acceptTransfer(barcode) {
    Swal.fire({
        title: 'تأكيد القبول',
        text: 'هل أنت متأكد من قبول نقل هذا الكرتون؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#27ae60',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'نعم، قبول',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/stage4/accept-transfer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ barcode: barcode })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(translations.accept_success, 'success');
                    checkPendingTransfers();
                    checkPendingBoxes();
                } else {
                    showToast(data.message || 'حدث خطأ', 'error');
                }
            })
            .catch(error => {
                console.error('خطأ في القبول:', error);
                showToast('حدث خطأ أثناء القبول', 'error');
            });
        }
    });
}

// رفض طلب النقل
function rejectTransfer(barcode) {
    Swal.fire({
        title: 'رفض النقل',
        text: 'هل تريد رفض نقل هذا الكرتون؟',
        input: 'text',
        inputPlaceholder: 'سبب الرفض (اختياري)',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'رفض',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/stage4/reject-transfer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    barcode: barcode,
                    reason: result.value || ''
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(translations.reject_success, 'success');
                    checkPendingTransfers();
                } else {
                    showToast(data.message || 'حدث خطأ', 'error');
                }
            })
            .catch(error => {
                console.error('خطأ في الرفض:', error);
                showToast('حدث خطأ أثناء الرفض', 'error');
            });
        }
    });
}

// تحميل عنصر معلق
function loadPendingItem(barcode) {
    if (!barcode) return;
    
    const barcodeInput = document.getElementById('lafafBarcode');
    if (barcodeInput) {
        barcodeInput.value = barcode;
    }
    loadLafaf(barcode);
}

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
        .then(response => response.json())
        .then(result => {
            // التحقق من حالة الاستجابة
            if (!result.success) {
                // 🔥 التحقق من أن اللفاف مستهلك بالكامل
                if (result.already_packed) {
                    Swal.fire({
                        title: '⚠️ تم استهلاك هذا اللفاف',
                        html: `
                            <div style="text-align: right; direction: rtl;">
                                <p style="font-size: 16px; color: #8e44ad; font-weight: bold;">
                                    هذا اللفاف تم تعبئته بالكامل ولا يمكن استخدامه مرة أخرى.
                                </p>
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-right: 4px solid #8e44ad; margin-top: 15px;">
                                    <p style="margin: 0; color: #666;">
                                        ${result.message.replace(/\n/g, '<br>')}
                                    </p>
                                </div>
                            </div>
                        `,
                        icon: 'warning',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#8e44ad'
                    });
                    document.getElementById('lafafBarcode').value = '';
                    document.getElementById('lafafBarcode').focus();
                    return;
                }
                
                // 🔥 التحقق من أن الباركود محظور (معاد إسناده)
                if (result.blocked) {
                    Swal.fire({
                        title: '⛔ الباركود محظور',
                        text: result.message,
                        icon: 'error',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#e74c3c'
                    });
                    document.getElementById('lafafBarcode').value = '';
                    document.getElementById('lafafBarcode').focus();
                    return;
                }
                
                // خطأ عام
                throw new Error(result.message || 'الكويل غير موجود');
            }

            const data = result.data;
            console.log('Lafaf data received:', data);

            const source = result.source || 'stage3';

            const netWeight = parseFloat(data.net_weight ?? data.total_weight ?? 0);
            const totalWeight = parseFloat(data.total_weight ?? netWeight);
            const wrappingWeight = parseFloat(data.wrapping_weight ?? data.wrapping_weight_db ?? 0);
            const remainingWeight = parseFloat(data.remaining_weight ?? netWeight);
            const usedWeight = parseFloat(data.used_weight ?? 0);
            const isPartial = data.is_partial ?? false;

            currentLafaf = {
                id: data.id || null,
                barcode: data.barcode,
                coil_number: data.coil_number || 'غير محدد',
                total_weight: totalWeight,
                net_weight: netWeight,
                remaining_weight: remainingWeight,
                used_weight: usedWeight,
                is_partial: isPartial,
                wrapping_weight: wrappingWeight,
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
            
            // 🔥 عرض الوزن المتبقي إذا كان اللفاف قيد المعالجة
            if (isPartial) {
                document.getElementById('displayWeight').innerHTML = `
                    <span style="color: #e67e22; font-weight: bold;">${remainingWeight.toFixed(3)} كجم متبقي</span>
                    <br><small style="color: #27ae60;">تم استخدام: ${usedWeight.toFixed(3)} كجم</small>
                `;
                showToast(`⚠️ هذا اللفاف قيد المعالجة - متبقي ${remainingWeight.toFixed(3)} كجم`, 'warning');
            } else {
                document.getElementById('displayWeight').textContent = currentLafaf.net_weight.toFixed(3) + ' كجم';
            }
            
            const weightDetails = document.getElementById('displayWeightDetails');
            if (weightDetails) {
                if (isPartial) {
                    weightDetails.innerHTML = `الوزن الأصلي: ${netWeight.toFixed(3)} كجم | مستخدم: ${usedWeight.toFixed(3)} كجم`;
                } else {
                    weightDetails.textContent = currentLafaf.wrapping_weight
                        ? `إجمالي: ${currentLafaf.total_weight.toFixed(3)} كجم | لفاف: ${currentLafaf.wrapping_weight.toFixed(3)} كجم`
                        : '';
                }
            }
            document.getElementById('lafafDisplay').classList.add('active');

            // Update summary - استخدام الوزن المتبقي
            document.getElementById('summaryLafaf').textContent = (isPartial ? remainingWeight : netWeight).toFixed(3) + ' كجم';

            // Pre-fill totalBoxesWeight with remaining weight (not full weight)
            document.getElementById('totalBoxesWeight').value = (isPartial ? remainingWeight : netWeight).toFixed(3);

            // Focus on box weight
            document.getElementById('boxWeight').focus();

            showToast('{{ __("stages.stage4_coil_loaded_success") }}', 'success');
        })
        .catch(error => {
            Swal.fire({
                title: '❌ خطأ',
                text: error.message || 'الكويل غير موجود',
                icon: 'error',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#e74c3c'
            });
            document.getElementById('lafafBarcode').value = '';
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

    // Disable button and show loading
    const divideBtn = event.target;
    const originalHtml = divideBtn.innerHTML;
    divideBtn.disabled = true;
    divideBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';

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
            divideBtn.disabled = false;
            divideBtn.innerHTML = originalHtml;
            break;
        }
    }

    renderBoxes();
    showToast(`{{ __("stages.stage4_box_saved_success") }}: ${boxes.length}! (${weightPerBox.toFixed(3)} كجم)`, 'success');

    // Re-enable button
    divideBtn.disabled = false;
    divideBtn.innerHTML = originalHtml;
    
    // مسح بيانات اللفاف الحالي
    currentLafaf = null;
    document.getElementById('lafafDisplay').classList.remove('active');

    // Clear divide inputs
    document.getElementById('totalBoxesWeight').value = '';
    document.getElementById('boxesCount').value = '';
    
    // Focus on barcode for next scan
    document.getElementById('lafafBarcode').focus();
}

function addBox() {
    if (!currentLafaf) {
        Swal.fire({
            title: '⚠️ يجب تحميل لفاف أولاً',
            html: `
                <div style="text-align: right; direction: rtl;">
                    <p style="font-size: 15px; color: #555;">
                        لإضافة كرتونة، يجب أولاً مسح أو إدخال باركود اللفاف من المرحلة الثالثة.
                    </p>
                    <div style="background: #fff3cd; padding: 12px; border-radius: 8px; border-right: 4px solid #f39c12; margin-top: 10px;">
                        <p style="margin: 0; font-size: 14px;">
                            <i class="fas fa-lightbulb" style="color: #f39c12;"></i>
                            أدخل باركود اللفاف في الحقل أعلاه ثم اضغط Enter
                        </p>
                    </div>
                </div>
            `,
            icon: 'warning',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#e67e22'
        }).then(() => {
            document.getElementById('lafafBarcode').focus();
        });
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
            
            // تحديث الوزن المتبقي بدلاً من مسح اللفاف
            const usedWeight = parseFloat(weight);
            currentLafaf.remaining_weight = (currentLafaf.remaining_weight || currentLafaf.net_weight) - usedWeight;
            currentLafaf.used_weight = (currentLafaf.used_weight || 0) + usedWeight;
            currentLafaf.is_partial = true;
            
            // تحديث العرض
            if (currentLafaf.remaining_weight > 0.01) {
                // لا يزال هناك وزن متبقي - تحديث العرض
                document.getElementById('displayWeight').innerHTML = `
                    <span style="color: #e67e22; font-weight: bold;">${currentLafaf.remaining_weight.toFixed(3)} كجم متبقي</span>
                    <br><small style="color: #27ae60;">تم استخدام: ${currentLafaf.used_weight.toFixed(3)} كجم</small>
                `;
                document.getElementById('totalBoxesWeight').value = currentLafaf.remaining_weight.toFixed(3);
                document.getElementById('summaryLafaf').textContent = currentLafaf.remaining_weight.toFixed(3) + ' كجم';
                
                showToast(`✅ تم حفظ الكرتونة! متبقي ${currentLafaf.remaining_weight.toFixed(3)} كجم`, 'success');
                // Focus on box weight for next box
                document.getElementById('boxWeight').focus();
            } else {
                // تم استهلاك اللفاف بالكامل
                showToast('✅ تم حفظ الكرتونة! اللفاف مستهلك بالكامل', 'success');
                currentLafaf = null;
                document.getElementById('lafafDisplay').classList.remove('active');
                document.getElementById('lafafBarcode').focus();
            }
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
        document.getElementById('lafafBarcode').focus();
    })
    .finally(() => {
        addBtn.disabled = false;
        addBtn.innerHTML = '<i class="fas fa-plus"></i> {{ __("stages.stage4_add_box_button") }}';
    });
}

function renderBoxes() {
    const list = document.getElementById('boxList');
    document.getElementById('boxCount').textContent = boxes.length;
    document.getElementById('finishBtn').disabled = boxes.length === 0;

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

// دالة إنهاء التعبئة وفحص الهدر
async function finishPackaging() {
    if (!currentLafaf) {
        alert('⚠️ لا يوجد لفاف محدد!');
        return;
    }

    if (boxes.length === 0) {
        alert('⚠️ لا توجد كراتين محفوظة!');
        return;
    }

    const finishBtn = document.getElementById('finishBtn');
    finishBtn.disabled = true;
    finishBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري فحص الهدر...';

    try {
        const response = await fetch('{{ route("manufacturing.stage4.check-final-waste") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                lafaf_barcode: currentLafaf.barcode
            })
        });

        const result = await response.json();

        if (result.success) {
            // 🔥 التحقق من وجود تنبيه هدر
            if (result.pending_approval && result.alert_message) {
                await Swal.fire({
                    title: result.alert_title || '⛔ تم إيقاف العملية',
                    html: result.alert_message,
                    icon: 'warning',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#e67e22',
                    width: '600px'
                });
                
                // الانتقال لصفحة السجل
                window.location.href = '{{ route("manufacturing.stage4.index") }}';
            } else {
                // لا يوجد تجاوز - نجاح
                await Swal.fire({
                    title: '✅ تم بنجاح',
                    html: `
                        <div style="text-align: right; direction: rtl;">
                            <p style="font-size: 16px; margin-bottom: 15px;">
                                <strong>تم فحص الهدر بنجاح - لا يوجد تجاوز في النسبة المسموح بها</strong>
                            </p>
                            <div style="background: #d1ecf1; padding: 15px; border-radius: 8px; border-right: 4px solid #17a2b8; margin-top: 15px;">
                                <table style="width: 100%; text-align: right;">
                                    <tr><td style="padding: 5px;"><strong>وزن اللفاف:</strong></td><td style="padding: 5px;">${result.data.lafaf_weight} كجم</td></tr>
                                    <tr><td style="padding: 5px;"><strong>إجمالي الكراتين:</strong></td><td style="padding: 5px;">${result.data.total_boxes_weight} كجم</td></tr>
                                    <tr><td style="padding: 5px;"><strong>الهدر:</strong></td><td style="padding: 5px; color: #28a745; font-weight: bold;">${result.data.waste_weight} كجم</td></tr>
                                    <tr><td style="padding: 5px;"><strong>نسبة الهدر:</strong></td><td style="padding: 5px; color: #28a745; font-weight: bold;">${result.data.waste_percentage}%</td></tr>
                                </table>
                            </div>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'العودة للسجل',
                    confirmButtonColor: '#27ae60'
                });
                
                window.location.href = '{{ route("manufacturing.stage4.index") }}';
            }
        } else {
            throw new Error(result.message || 'حدث خطأ');
        }
    } catch (error) {
        alert('{{ __("app.error") }}: ' + error.message);
        finishBtn.disabled = false;
        finishBtn.innerHTML = '<i class="fas fa-check-circle"></i> إنهاء التعبئة وفحص الهدر';
    }
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

// تصدير الدوال للاستخدام العام
window.checkPendingBoxes = checkPendingBoxes;
window.checkPendingTransfers = checkPendingTransfers;
window.openTransferModal = openTransferModal;
window.closeTransferModal = closeTransferModal;
window.submitTransfer = submitTransfer;
window.acceptTransfer = acceptTransfer;
window.rejectTransfer = rejectTransfer;
window.loadPendingItem = loadPendingItem;
</script>

@endsection
