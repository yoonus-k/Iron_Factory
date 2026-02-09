@extends('master')

@section('title', __('stages.stage3_create_title'))

@section('content')

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    /* Pending Items Panel */
    .pending-items-panel{ background:linear-gradient(135deg,#fff5f5 0%,#ffe8e8 100%); border:1px solid rgba(231,76,60,0.2); border-radius:12px; padding:18px; margin-top:18px; }
    .pending-items-panel-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
    .pending-items-panel-header h4{ margin:0; color:#c0392b; font-size:16px; }
    .pending-items-list{ display:flex; flex-direction:column; gap:12px; }
    .pending-item-card{ background:#fff; border-radius:10px; padding:14px; border-right:4px solid #9b59b6; box-shadow:0 4px 12px rgba(155,89,182,0.1); }
    .pending-item-info{ display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
    .pending-item-info strong{ color:#631010; font-size:15px; }
    .pending-item-actions{ display:flex; gap:8px; }
    .pending-item-actions button{ padding:8px 14px; border-radius:6px; border:none; cursor:pointer; font-weight:600; font-size:13px; }
    .btn-load-item{ background:#9b59b6; color:#fff; }
    .btn-load-item:hover{ background:#8e44ad; }
    
    /* Pending Lafafs Panel */
    .pending-lafafs-panel{ background:linear-gradient(135deg,#fff5e6 0%,#ffe8cc 100%); border:1px solid rgba(230,126,34,0.3); border-radius:12px; padding:18px; margin-top:18px; }
    .pending-lafafs-panel-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
    .pending-lafafs-panel-header h4{ margin:0; color:#d35400; font-size:16px; }
    .pending-lafaf-card{ background:#fff; border-radius:10px; padding:14px; border-right:4px solid #e67e22; box-shadow:0 4px 12px rgba(230,126,34,0.1); }
    .btn-continue{ background:#e67e22; color:#fff; }
    .btn-continue:hover{ background:#d35400; }
    .btn-transfer{ background:#3498db; color:#fff; }
    .btn-transfer:hover{ background:#2980b9; }
    
    /* Pending Transfers Panel */
    .pending-transfers-panel{ background:linear-gradient(135deg,#e8f5e9 0%,#c8e6c9 100%); border:1px solid rgba(39,174,96,0.3); border-radius:12px; padding:18px; margin-top:18px; }
    .pending-transfers-panel-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
    .pending-transfers-panel-header h4{ margin:0; color:#27ae60; font-size:16px; }
    .pending-transfer-card{ background:#fff; border-radius:10px; padding:14px; border-right:4px solid #27ae60; box-shadow:0 4px 12px rgba(39,174,96,0.1); }
    .btn-accept{ background:#27ae60; color:#fff; }
    .btn-accept:hover{ background:#1e8449; }
    .btn-reject{ background:#e74c3c; color:#fff; }
    .btn-reject:hover{ background:#c0392b; }
    
    /* Transfer Modal */
    .transfer-modal-overlay{ display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center; }
    .transfer-modal{ background:#fff; border-radius:16px; padding:24px; width:90%; max-width:500px; box-shadow:0 20px 60px rgba(0,0,0,0.2); }
    .transfer-modal-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:2px solid #9b59b6; padding-bottom:15px; }
    .transfer-modal-header h3{ margin:0; color:#9b59b6; font-size:18px; }
    .transfer-modal-close{ background:none; border:none; font-size:24px; cursor:pointer; color:#999; }
    .transfer-modal-close:hover{ color:#e74c3c; }
</style>

<div class="stage-container">
    <!-- Header -->
    <div class="stage-header">
        <h1>
            <i class="fas fa-circle"></i>
            {{ __('stages.stage3_create_title') }}
        </h1>
        <p>{{ __('stages.stage3_header_description') }}</p>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 style="margin: 0 0 15px 0; color: #9b59b6;"><i class="fas fa-camera"></i> {{ __('stages.stage3_scan_stage2_barcode') }}</h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="stage2Barcode" class="barcode-input" placeholder="{{ __('stages.stage3_barcode_placeholder') }}" autofocus>
            <span class="barcode-icon">🎨</span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 10px;"><i class="fas fa-lightbulb"></i> {{ __('stages.stage3_scan_or_press_enter') }}</small>
    </div>

    <!-- Stage2 Display -->
    <div id="stage2Display" class="stage2-display">
        <h4><i class="fas fa-circle-check"></i> {{ __('stages.stage3_stage2_data') }}</h4>
        <div class="stage2-info">
            <div class="info-item">
                <div class="info-label">{{ __('stages.stand_label') }}</div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.material_label') }}</div>
                <div class="info-value" id="displayMaterial">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.output_weight_label') }}</div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
        </div>
    </div>

    <!-- Pending Items Panel -->
    <div id="pendingItemsPanel" class="pending-items-panel form-section" style="display:none;">
        <div class="pending-items-panel-header">
            <h4><i class="fas fa-exclamation-triangle"></i> المعالجات المتاحة للتلفيف (<span id="pendingItemsCount">0</span>)</h4>
            <button type="button" class="btn-secondary" onclick="checkPendingItems()" style="padding:6px 12px; font-size:12px;">
                <i class="fas fa-sync-alt"></i> تحديث
            </button>
        </div>
        <div id="pendingItemsList" class="pending-items-list">
            <div class="empty-state" style="padding:20px;">
                <p>جاري التحميل...</p>
            </div>
        </div>
    </div>

    <!-- Pending Lafafs Panel - لوحة اللفائف المعلقة للمستخدم الحالي -->
    <div id="pendingLafafsPanel" class="pending-lafafs-panel form-section" style="display:none;">
        <div class="pending-lafafs-panel-header">
            <h4><i class="fas fa-hourglass-half"></i> اللفائف المعلقة الخاصة بك (<span id="pendingLafafsCount">0</span>)</h4>
            <button type="button" class="btn-secondary" onclick="checkPendingLafafs()" style="padding:6px 12px; font-size:12px;">
                <i class="fas fa-sync-alt"></i> تحديث
            </button>
        </div>
        <div id="pendingLafafsList" class="pending-items-list">
            <div class="empty-state" style="padding:20px;">
                <p>جاري التحميل...</p>
            </div>
        </div>
    </div>

    <!-- Pending Transfers Panel - لوحة طلبات النقل الواردة -->
    <div id="pendingTransfersPanel" class="pending-transfers-panel form-section" style="display:none;">
        <div class="pending-transfers-panel-header">
            <h4><i class="fas fa-exchange-alt"></i> طلبات النقل الواردة (<span id="pendingTransfersCount">0</span>)</h4>
            <button type="button" class="btn-secondary" onclick="checkPendingTransfers()" style="padding:6px 12px; font-size:12px;">
                <i class="fas fa-sync-alt"></i> تحديث
            </button>
        </div>
        <div id="pendingTransfersList" class="pending-items-list">
            <div class="empty-state" style="padding:20px;">
                <p>جاري التحميل...</p>
            </div>
        </div>
    </div>

    <!-- Lafaf Form -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-edit"></i> {{ __('stages.stage3_lafaf_data') }}</h3>

        <div class="info-box">
            <strong><i class="fas fa-thumbtack"></i> {{ __('stages.stage3_important_note') }}:</strong>
            <ul style="margin:8px 0 0 20px; color:#7f8c8d; font-size:13px;">
                <li>{{ __('stages.stage3_weight_increases_note') }}</li>
                <li>{{ __('stages.stage3_enter_complete_weight_note') }}</li>
                <li>{{ __('stages.stage3_auto_calc_note') }}</li>
            </ul>
            @if($plastic)
            @php
                $plasticAvailable = $plastic->available_weight ?? 0;
            @endphp
            <div style="margin-top:15px; padding:10px; background:#e8f5e9; border-radius:8px; border-right:3px solid #27ae60;">
                <strong style="color:#27ae60;"><i class="fas fa-box"></i> البلاستيك المتاح في المستودع:</strong>
                <span style="font-size:16px; font-weight:700; color:#1e7e34; margin-right:10px;">
                    {{ number_format($plasticAvailable, 3) }} كجم
                </span>
                <small style="display:block; margin-top:5px; color:#666;">
                    <i class="fas fa-info-circle"></i> سيتم خصم الوزن المضاف بالكامل من البلاستيك
                </small>
            </div>
            @else
            <div style="margin-top:15px; padding:10px; background:#fff3cd; border-radius:8px; border-right:3px solid #ffc107;">
                <strong style="color:#856404;"><i class="fas fa-exclamation-triangle"></i> تحذير:</strong>
                <span style="color:#856404;">لا يوجد بلاستيك متاح في المستودع</span>
            </div>
            @endif
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>{{ __('stages.stage3_input_weight_from_previous') }}</label>
                <input type="number" id="inputWeight" class="form-control" readonly style="background: #ecf0f1; font-weight: 600;">
            </div>

            <div class="form-group">
                <label>{{ __('stages.stage3_total_weight_label') }} <span style="color:#e74c3c;">*</span></label>
                <input type="number" id="totalWeight" class="form-control" placeholder="{{ __('stages.stage3_example_weight') }}" step="0.01" oninput="calculateNetWeight()">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">{{ __('stages.stage3_total_weight_note') }}</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>اللفاف</label>
                <select id="wrappingSelect" class="form-select" style="padding:10px 12px; border-radius:8px; border:1.5px solid #e7eef5;" onchange="onWrappingChange()">
                    <option value="">-- بدون لفاف --</option>
                    @foreach($wrappings as $wrapping)
                        <option value="{{ $wrapping->id }}" data-weight="{{ $wrapping->weight }}">{{ $wrapping->wrapping_number }} - {{ $wrapping->weight }} كجم</option>
                    @endforeach
                </select>
                <input type="hidden" id="wrappingId" value="">
                <input type="hidden" id="wrappingWeight" value="0">
            </div>

            <div class="form-group">
                <label>الوزن الصافي (بعد خصم اللفاف)</label>
                <input type="number" id="netWeight" class="form-control" readonly style="background: #e3f2fd; font-weight: 600; color: #1976d2;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">يتم الحساب تلقائياً: الوزن الكلي - وزن اللفاف</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>{{ __('stages.stage3_added_weight_label') }}</label>
                <input type="number" id="addedWeight" class="form-control" readonly style="background: #e8f5e9; font-weight: 600; color: #27ae60;">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">{{ __('stages.stage3_auto_calc_label') }}</small>
            </div>

            <div class="form-group">
                <label>{{ __('stages.stage3_color_label') }} <span style="color:#e74c3c;">*</span></label>
                <select id="colorSelect" class="form-select" style="padding:10px 12px; border-radius:8px; border:1.5px solid #e7eef5;">
                    <option value="">-- اختر اللون --</option>
                    @forelse($colors as $color)
                        <option value="{{ $color->id }}" data-name="{{ $color->name_ar }}" data-available="{{ $color->available_weight ?? 0 }}">
                            {{ $color->name_ar }}
                            @if(($color->available_weight ?? 0) > 0)
                                ({{ number_format($color->available_weight, 2) }} كجم)
                            @endif
                        </option>
                    @empty
                        <option value="" disabled>لا توجد ألوان مسجلة حالياً</option>
                    @endforelse
                </select>
                <input type="hidden" id="color" value="">
                <input type="hidden" id="colorMaterialId" value="">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>{{ __('stages.stage3_plastic_type_label') }}</label>
                <input type="text" id="plasticType" class="form-control" placeholder="{{ __('stages.stage3_plastic_placeholder') }}">
            </div>

            <div class="form-group">
                <label>{{ __('stages.stage3_notes_label') }}</label>
                <textarea id="notes" class="form-control" placeholder="{{ __('stages.stage3_notes_placeholder') }}"></textarea>
            </div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-primary" onclick="addLafaf(this)">
                <i class="fas fa-plus"></i> {{ __('stages.stage3_add_lafaf_button') }}
            </button>
            <button type="button" class="btn-secondary" onclick="clearForm()">
                <i class="fas fa-sync"></i> {{ __('stages.stage3_clear_form_button') }}
            </button>
        </div>
    </div>

    <!-- Lafafs List -->
    <div class="form-section">
        <h3 class="section-title"><i class="fas fa-clipboard"></i> {{ __('stages.stage3_added_lafafs') }} (<span id="lafafCount">0</span>)</h3>
        <div id="lafafList" class="lafafs-list">
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;opacity:0.3;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>{{ __('stages.stage3_no_lafafs_added_yet') }}</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div style="display:flex; gap:15px; justify-content:center; margin-top:25px; padding-top:20px; border-top:2px solid #ecf0f1;">
        <button type="button" class="btn-success" onclick="finishOperation()" id="submitBtn" disabled style="padding:14px 32px; font-size:16px;">
            <i class="fas fa-check-double"></i> {{ __('stages.stage3_finish_operation') }}
        </button>
        <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('manufacturing.stage3.index') }}'">
            <i class="fas fa-times"></i> {{ __('stages.cancel_button') }}
        </button>
    </div>
</div>

<!-- Transfer Modal -->
<div id="transferModal" class="transfer-modal-overlay">
    <div class="transfer-modal">
        <div class="transfer-modal-header">
            <h3><i class="fas fa-exchange-alt"></i> نقل اللفاف لموظف آخر</h3>
            <button type="button" class="transfer-modal-close" onclick="closeTransferModal()">&times;</button>
        </div>
        <div style="margin-bottom:15px;">
            <strong>اللفاف:</strong> <span id="transferLafafNumber">-</span><br>
            <strong>الباركود:</strong> <code id="transferLafafBarcode">-</code><br>
            <strong>الوزن:</strong> <span id="transferLafafWeight">-</span> كجم
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
            <input type="text" id="transferReason" class="form-control" placeholder="اختياري - سبب النقل">
        </div>
        <div class="form-group" style="margin-bottom:20px;">
            <label style="font-weight:600;">ملاحظات</label>
            <textarea id="transferNotes" class="form-control" placeholder="ملاحظات إضافية (اختياري)" rows="2"></textarea>
        </div>
        <input type="hidden" id="transferLafafBarcodeValue">
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

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
let currentStage2 = null;
let lafafs = [];
let pendingItems = [];
let pendingItemsCount = 0;
let pendingLafafs = [];
let pendingLafafsCount = 0;
let pendingTransfers = [];
let pendingTransfersCount = 0;

// كائن الترجمات
const translations = {
    pending_lafafs_title: 'اللفائف المعلقة الخاصة بك',
    pending_transfers_title: 'طلبات النقل الواردة',
    transfer_lafaf: 'نقل اللفاف',
    continue_lafaf: 'متابعة',
    accept_transfer: 'قبول',
    reject_transfer: 'رفض',
    from_worker: 'من',
    weight_label: 'الوزن',
    color_label: 'اللون',
    no_pending_lafafs: 'لا توجد لفائف معلقة',
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

// جلب العناصر المعلقة من API
function checkPendingItems() {
    console.log('🔍 جاري التحقق من العناصر المعلقة...');
    
    fetch('/stage3/pending-items', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('📦 بيانات العناصر المعلقة:', data);
        
        if (data.success) {
            pendingItems = data.items || [];
            pendingItemsCount = data.count || 0;
            renderPendingItemsPanel();
        }
    })
    .catch(error => {
        console.error('❌ خطأ في جلب العناصر المعلقة:', error);
    });
}

// جلب اللفائف المعلقة للمستخدم الحالي
function checkPendingLafafs() {
    console.log('🔍 جاري التحقق من اللفائف المعلقة...');
    
    fetch('/stage3/pending-lafafs', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('📦 بيانات اللفائف المعلقة:', data);
        
        if (data.success) {
            pendingLafafs = data.items || [];
            pendingLafafsCount = data.count || 0;
            renderPendingLafafsPanel();
        }
    })
    .catch(error => {
        console.error('❌ خطأ في جلب اللفائف المعلقة:', error);
    });
}

// جلب طلبات النقل الواردة
function checkPendingTransfers() {
    console.log('🔍 جاري التحقق من طلبات النقل...');
    
    fetch('/stage3/pending-transfers', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('📦 بيانات طلبات النقل:', data);
        
        if (data.success) {
            pendingTransfers = data.transfers || [];
            pendingTransfersCount = data.count || 0;
            renderPendingTransfersPanel();
        }
    })
    .catch(error => {
        console.error('❌ خطأ في جلب طلبات النقل:', error);
    });
}

// عرض لوحة اللفائف المعلقة
function renderPendingLafafsPanel() {
    const panel = document.getElementById('pendingLafafsPanel');
    const list = document.getElementById('pendingLafafsList');
    const countSpan = document.getElementById('pendingLafafsCount');
    
    if (!panel || !list) return;
    
    countSpan.textContent = pendingLafafsCount;
    
    if (pendingLafafsCount === 0) {
        panel.style.display = 'none';
        return;
    }
    
    panel.style.display = 'block';
    
    list.innerHTML = pendingLafafs.map(lafaf => `
        <div class="pending-lafaf-card">
            <div class="pending-item-info">
                <div>
                    <strong><i class="fas fa-circle"></i> ${lafaf.coil_number || lafaf.barcode}</strong>
                    <div style="font-size:13px; color:#666; margin-top:4px;">
                        ${lafaf.material_name || 'غير محدد'} | ${translations.weight_label}: ${lafaf.total_weight || 0} ${translations.kg_unit} | ${translations.color_label}: ${lafaf.color || 'غير محدد'}
                    </div>
                    <div style="font-size:12px; color:#999; margin-top:2px;">
                        <i class="fas fa-barcode"></i> ${lafaf.barcode}
                    </div>
                </div>
                <div class="pending-item-actions">
                    <button type="button" class="btn-transfer" onclick="openTransferModal('${lafaf.barcode}', '${lafaf.coil_number || ''}', ${lafaf.total_weight || 0})">
                        <i class="fas fa-exchange-alt"></i> ${translations.transfer_lafaf}
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// عرض لوحة طلبات النقل الواردة
function renderPendingTransfersPanel() {
    const panel = document.getElementById('pendingTransfersPanel');
    const list = document.getElementById('pendingTransfersList');
    const countSpan = document.getElementById('pendingTransfersCount');
    
    if (!panel || !list) return;
    
    countSpan.textContent = pendingTransfersCount;
    
    if (pendingTransfersCount === 0) {
        panel.style.display = 'none';
        return;
    }
    
    panel.style.display = 'block';
    
    list.innerHTML = pendingTransfers.map(transfer => `
        <div class="pending-transfer-card">
            <div class="pending-item-info">
                <div>
                    <strong><i class="fas fa-circle"></i> ${transfer.coil_number || transfer.barcode}</strong>
                    <div style="font-size:13px; color:#666; margin-top:4px;">
                        ${translations.from_worker}: <strong>${transfer.sender_name}</strong> | 
                        ${translations.weight_label}: ${transfer.total_weight || 0} ${translations.kg_unit}
                        ${transfer.color ? ' | ' + translations.color_label + ': ' + transfer.color : ''}
                    </div>
                    <div style="font-size:12px; color:#999; margin-top:2px;">
                        <i class="fas fa-barcode"></i> ${transfer.barcode}
                        ${transfer.reason ? ' | <i class="fas fa-comment"></i> ' + transfer.reason : ''}
                    </div>
                </div>
                <div class="pending-item-actions">
                    <button type="button" class="btn-accept" onclick="acceptTransfer('${transfer.barcode}')">
                        <i class="fas fa-check"></i> ${translations.accept_transfer}
                    </button>
                    <button type="button" class="btn-reject" onclick="rejectTransfer('${transfer.barcode}')">
                        <i class="fas fa-times"></i> ${translations.reject_transfer}
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// فتح موديل النقل
function openTransferModal(barcode, coilNumber, weight) {
    document.getElementById('transferLafafNumber').textContent = coilNumber || barcode;
    document.getElementById('transferLafafBarcode').textContent = barcode;
    document.getElementById('transferLafafWeight').textContent = weight;
    document.getElementById('transferLafafBarcodeValue').value = barcode;
    document.getElementById('transferWorkerId').value = '';
    document.getElementById('transferReason').value = '';
    document.getElementById('transferNotes').value = '';
    document.getElementById('transferModal').style.display = 'flex';
}

// إغلاق موديل النقل
function closeTransferModal() {
    document.getElementById('transferModal').style.display = 'none';
}

// إرسال طلب النقل
function submitTransfer() {
    const barcode = document.getElementById('transferLafafBarcodeValue').value;
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
    
    fetch('/stage3/transfer-lafaf', {
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
            checkPendingLafafs();
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
        text: 'هل أنت متأكد من قبول نقل هذا اللفاف؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#27ae60',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'نعم، قبول',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/stage3/accept-transfer', {
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
                    checkPendingLafafs();
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
        text: 'هل تريد رفض نقل هذا اللفاف؟',
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
            fetch('/stage3/reject-transfer', {
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

// عرض لوحة العناصر المعلقة
function renderPendingItemsPanel() {
    const panel = document.getElementById('pendingItemsPanel');
    const list = document.getElementById('pendingItemsList');
    const countSpan = document.getElementById('pendingItemsCount');
    
    if (!panel || !list) return;
    
    countSpan.textContent = pendingItemsCount;
    
    if (pendingItemsCount === 0) {
        panel.style.display = 'none';
        return;
    }
    
    panel.style.display = 'block';
    
    list.innerHTML = pendingItems.map(item => `
        <div class="pending-item-card">
            <div class="pending-item-info">
                <div>
                    <strong><i class="fas fa-barcode"></i> ${item.barcode}</strong>
                    <div style="font-size:13px; color:#666; margin-top:4px;">
                        ${item.material_name || 'غير محدد'} - ${item.output_weight || 0} كجم
                    </div>
                </div>
                <div class="pending-item-actions">
                    <button type="button" class="btn-load-item" onclick="loadPendingItem('${item.barcode}')">
                        <i class="fas fa-arrow-left"></i> تحميل
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// تحميل عنصر معلق
function loadPendingItem(barcode) {
    if (!barcode) return;
    
    const barcodeInput = document.getElementById('stage2Barcode');
    if (barcodeInput) {
        barcodeInput.value = barcode;
    }
    loadStage2(barcode);
}

// Barcode scanner
document.addEventListener('DOMContentLoaded', function() {
    // جلب العناصر المعلقة
    checkPendingItems();
    checkPendingLafafs();
    checkPendingTransfers();
    
    // تحديث تلقائي كل 30 ثانية
    setInterval(() => {
        checkPendingItems();
        checkPendingLafafs();
        checkPendingTransfers();
    }, 30000);
    
    const barcodeInput = document.getElementById('stage2Barcode');

    if (barcodeInput) {
        console.log('✅ Barcode input found and event listener attached');

        barcodeInput.addEventListener('keypress', function(e) {
            console.log('🔑 Key pressed:', e.key, 'Value:', this.value);

            if (e.key === 'Enter') {
                e.preventDefault();
                const barcode = this.value.trim();
                console.log('📦 Loading barcode:', barcode);
                loadStage2(barcode);
                this.value = '';
            }
        });
    } else {
        console.error('❌ Barcode input not found!');
    }

    // Auto-calculate added weight
    const totalWeightInput = document.getElementById('totalWeight');
    if (totalWeightInput) {
        totalWeightInput.addEventListener('input', calculateAddedWeight);
        console.log('✅ Total weight input listener attached');
    } else {
        console.error('❌ Total weight input not found!');
    }

    // تحديث اللون عند الاختيار
    const colorSelect = document.getElementById('colorSelect');
    if (colorSelect) {
        colorSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const colorName = selectedOption.getAttribute('data-name');
            const colorId = this.value;

            document.getElementById('color').value = colorName || '';
            document.getElementById('colorMaterialId').value = colorId || '';
        });
    }
});

function loadStage2(barcode) {
    console.log('🚀 loadStage2 called with barcode:', barcode);

    if (!barcode) {
        alert('{{ __('stages.stage3_please_enter_barcode') }}');
        return;
    }

    console.log('📡 Fetching:', `/stage3/get-stage2-by-barcode/${barcode}`);

    fetch(`/stage3/get-stage2-by-barcode/${barcode}`)
        .then(response => {
            // التعامل مع الاستجابات غير الناجحة
            if (!response.ok) {
                return response.json().then(data => {
                    // رمي الخطأ مع البيانات الكاملة
                    const error = {
                        blocked: data.blocked || false,
                        message: data.message || '{{ __('stages.stage3_data_not_found') }}'
                    };
                    throw error;
                });
            }
            return response.json();
        })
        .then(result => {
            // التحقق من حالة blocked
            if (result && result.blocked) {
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
                document.getElementById('stage2Barcode').focus();
                return;
            }

            if (result && !result.success) throw { message: result.message };

            const data = result.data;
            const source = result.source || 'stage2';

            currentStage2 = {
                id: data.id || null,
                barcode: data.barcode,
                stand_number: data.stand_number || '{{ __('stages.not_specified') }}',
                output_weight: parseFloat(data.remaining_weight || data.output_weight || data.quantity),
                material_id: data.material_id,
                material_name: data.material_name || '{{ __('stages.not_specified') }}',
                source: source
            };

            // Display stage2 data safely
            const barcodeElement = document.getElementById('displayBarcode');
            if (barcodeElement) {
                barcodeElement.textContent = currentStage2.barcode;
            }

            const standElement = document.getElementById('displayStand');
            if (standElement) {
                standElement.textContent = currentStage2.stand_number;
            }

            const materialElement = document.getElementById('displayMaterial');
            if (materialElement) {
                materialElement.textContent = currentStage2.material_name;
            }

            const weightElement = document.getElementById('displayWeight');
            if (weightElement) {
                weightElement.textContent = currentStage2.output_weight + ' {{ __('stages.kg_unit') }}';
            }

            const stage2DisplayElement = document.getElementById('stage2Display');
            if (stage2DisplayElement) {
                stage2DisplayElement.classList.add('active');
            }

            // Fill input weight
            document.getElementById('inputWeight').value = currentStage2.output_weight;

            // Calculate initial added weight
            calculateAddedWeight();

            // Focus on total weight
            document.getElementById('totalWeight').focus();

            showToast('{{ __('stages.stage3_stage2_loaded_success') }}', 'success');
        })
        .catch(error => {
            const errorMessage = error.message || '{{ __('stages.stage3_error_label') }}' + error;
            if (error.blocked) {
                Swal.fire({
                    icon: 'error',
                    title: '⛔ غير مسموح',
                    text: errorMessage,
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#dc3545',
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'swal2-rtl'
                    }
                });
            } else {
                alert(errorMessage);
            }
            document.getElementById('stage2Barcode').focus();
        });
}

function calculateAddedWeight() {
    const inputWeight = parseFloat(document.getElementById('inputWeight').value) || 0;
    const totalWeight = parseFloat(document.getElementById('totalWeight').value) || 0;
    const wrappingWeight = parseFloat(document.getElementById('wrappingWeight').value) || 0;
    
    console.log('🔢 حساب الوزن المضاف:');
    console.log('   وزن الدخول:', inputWeight);
    console.log('   الوزن الكلي:', totalWeight);
    console.log('   وزن اللفاف:', wrappingWeight);
    
    // حساب الوزن الصافي أولاً
    const netWeight = totalWeight - wrappingWeight;
    console.log('   الوزن الصافي:', netWeight);

    if (netWeight > 0 && inputWeight > 0) {
        const addedWeight = netWeight - inputWeight;
        console.log('   الوزن المضاف:', addedWeight);

        if (addedWeight < 0) {
            showToast('الوزن الصافي يجب أن يكون أكبر من وزن الدخول', 'error');
            document.getElementById('addedWeight').value = '';
            return;
        }

        document.getElementById('addedWeight').value = addedWeight.toFixed(3);
    } else {
        document.getElementById('addedWeight').value = '';
    }
    
    // Also calculate net weight when total weight changes
    calculateNetWeight();
}

function onWrappingChange() {
    const select = document.getElementById('wrappingSelect');
    const selectedOption = select.options[select.selectedIndex];
    
    console.log('🎁 تم تغيير اللفاف:', selectedOption.text);
    
    if (selectedOption.value) {
        const wrappingWeight = parseFloat(selectedOption.dataset.weight) || 0;
        document.getElementById('wrappingId').value = selectedOption.value;
        document.getElementById('wrappingWeight').value = wrappingWeight;
        console.log('   وزن اللفاف المختار:', wrappingWeight);
    } else {
        document.getElementById('wrappingId').value = '';
        document.getElementById('wrappingWeight').value = '0';
        console.log('   تم إلغاء اللفاف');
    }
    
    calculateNetWeight();
    calculateAddedWeight();
}

function calculateNetWeight() {
    const totalWeight = parseFloat(document.getElementById('totalWeight').value) || 0;
    const wrappingWeight = parseFloat(document.getElementById('wrappingWeight').value) || 0;
    
    if (totalWeight > 0) {
        const netWeight = totalWeight - wrappingWeight;
        document.getElementById('netWeight').value = netWeight.toFixed(3);
    } else {
        document.getElementById('netWeight').value = '';
    }
}

function addLafaf(button = null) {
    if (!currentStage2) {
        alert('{{ __('stages.stage3_please_load_stage2_first') }}');
        document.getElementById('stage2Barcode').focus();
        return;
    }

    const totalWeight = document.getElementById('totalWeight').value;
    const color = document.getElementById('color').value.trim();
    const colorMaterialId = document.getElementById('colorMaterialId').value;
    const plasticType = document.getElementById('plasticType').value.trim();
    const notes = document.getElementById('notes').value.trim();
    const wrappingId = document.getElementById('wrappingId').value;
    const wrappingWeight = parseFloat(document.getElementById('wrappingWeight').value) || 0;

    if (!totalWeight || !color) {
        alert('{{ __('stages.stage3_fill_required_fields') }}');
        return;
    }

    const inputWeight = parseFloat(document.getElementById('inputWeight').value) || 0;
    const totalWeightNum = parseFloat(totalWeight);
    const netWeight = totalWeightNum - wrappingWeight;

    if (netWeight <= inputWeight) {
        alert('الوزن الصافي (بعد خصم اللفاف) يجب أن يكون أكبر من وزن الدخول');
        return;
    }

    const data = {
        stage2_id: currentStage2.id || null,
        stage2_barcode: currentStage2.barcode,
        source: currentStage2.source || 'stage2',
        material_id: currentStage2.material_id || null,
        input_weight: inputWeight,
        total_weight: totalWeightNum,
        wrapping_id: wrappingId || null,
        wrapping_weight: wrappingWeight,
        color: color,
        plastic_type: plasticType,
        notes: notes
    };

    // حفظ فوري للفاف
    const addBtn = button || document.querySelector('.btn-primary[onclick*="addLafaf"]');
    addBtn.disabled = true;
    addBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('stages.stage3_saving') }}...';

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
        console.log('📦 Response من السيرفر:', result);
        if (result.success) {
            const lafaf = {
                id: result.data.lafaf_id,
                barcode: result.data.barcode,
                coil_number: result.data.coil_number,
                material_name: result.data.material_name,
                total_weight: result.data.total_weight,
                net_weight: result.data.net_weight,
                wrapping_weight: result.data.wrapping_weight,
                input_weight: result.data.input_weight,
                added_weight: result.data.added_weight,
                color: result.data.color,
                plastic_type: result.data.plastic_type,
                notes: notes,
                saved: true
            };
            
            console.log('📝 اللفاف المضاف:', lafaf);

            lafafs.push(lafaf);
            renderLafafs();
            clearForm();

            showToast('{{ __('stages.stage3_lafaf_saved_success') }}', 'success');

            document.getElementById('stage2Barcode').focus();
        } else {
            throw new Error(result.message || '{{ __('stages.stage3_error_saving_lafaf') }}');
        }
    })
    .catch(error => {
        alert('{{ __('stages.stage3_error_label') }}' + error.message);
    })
    .finally(() => {
        addBtn.disabled = false;
        addBtn.innerHTML = '<i class="fas fa-plus"></i> {{ __('stages.stage3_add_lafaf_button') }}';
    });
}

function escapeAttribute(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
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
                <p>{{ __('stages.stage3_no_lafafs_added_yet') }}</p>
            </div>
        `;
        return;
    }

    list.innerHTML = lafafs.map(item => {
        const totalWeight = typeof item.total_weight === 'number' ? item.total_weight : parseFloat(item.total_weight) || 0;
        const wrappingWeight = typeof item.wrapping_weight === 'number' ? item.wrapping_weight : parseFloat(item.wrapping_weight) || 0;
        const netWeight = typeof item.net_weight === 'number' ? item.net_weight : parseFloat(item.net_weight) || totalWeight;
        const addedWeight = typeof item.added_weight === 'number' ? item.added_weight : parseFloat(item.added_weight) || 0;
        const barcodeAttr = escapeAttribute(item.barcode || '');
        const coilAttr = escapeAttribute(item.coil_number || '');
        const materialAttr = escapeAttribute(item.material_name || '');
        const colorAttr = escapeAttribute(item.color || '');

        return `
        <div class="lafaf-item">
            <div class="lafaf-info" style="flex:1;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                    <strong style="color:#2c3e50; font-size:16px;">
                        <i class="fas fa-circle" style="color:#27ae60;"></i> ${item.coil_number}
                    </strong>
                    <span style="background:#27ae60; color:white; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600;">✓ {{ __('stages.stage3_saved_label') }}</span>
                </div>
                <small style="display:block; line-height:1.6;">
                    <strong>{{ __('stages.material_label') }}:</strong> ${item.material_name} |
                    <strong>{{ __('stages.barcode_label') }}:</strong> <code style="background:#f8f9fa; padding:2px 6px; border-radius:4px; font-family:monospace;">${item.barcode}</code><br>
                    <strong>الوزن الإجمالي:</strong> ${totalWeight.toFixed(3)} {{ __('stages.kg_unit') }} |
                    <strong>وزن اللفاف:</strong> ${wrappingWeight.toFixed(3)} {{ __('stages.kg_unit') }} |
                    <strong>الوزن الصافي:</strong> ${netWeight.toFixed(3)} {{ __('stages.kg_unit') }}<br>
                    <strong>{{ __('stages.stage3_added_weight_label') }}:</strong> ${addedWeight.toFixed(3)} {{ __('stages.kg_unit') }} |
                    <strong>{{ __('stages.stage3_color_label') }}:</strong> ${item.color}
                    ${item.plastic_type ? ' | <strong>{{ __('stages.stage3_plastic_type_label') }}:</strong> ' + item.plastic_type : ''}
                    ${item.notes ? '<br>📝 <strong>{{ __('stages.stage3_notes_label') }}:</strong> ' + item.notes : ''}
                </small>
            </div>
            <div style="display:flex; gap:8px;">
                <button class="btn-print"
                        data-barcode="${barcodeAttr}"
                        data-coil="${coilAttr}"
                        data-material="${materialAttr}"
                        data-net="${netWeight.toFixed(3)}"
                        data-total="${totalWeight.toFixed(3)}"
                        data-wrapping="${wrappingWeight.toFixed(3)}"
                        data-color="${colorAttr}"
                        onclick="handlePrintClick(event)">
                    <i class="fas fa-print"></i> {{ __('stages.stage3_print_barcode') }}
                </button>
            </div>
        </div>
        `;
    }).join('');
}

function finishOperation() {
    if (lafafs.length === 0) {
        alert('{{ __('stages.stage3_add_at_least_one_lafaf') }}');
        return;
    }

    showToast('{{ __('stages.stage3_operation_finished_success') }}', 'success');
    setTimeout(() => {
        window.location.href = '{{ route("manufacturing.stage3.index") }}';
    }, 1000);
}

function clearForm() {
    document.getElementById('totalWeight').value = '';
    document.getElementById('addedWeight').value = '';
    document.getElementById('netWeight').value = '';
    document.getElementById('wrappingSelect').selectedIndex = 0;
    document.getElementById('wrappingId').value = '';
    document.getElementById('wrappingWeight').value = '0';
    document.getElementById('color').value = '';
    document.getElementById('plasticType').value = '';
    document.getElementById('notes').value = '';

    if (currentStage2) {
        document.getElementById('inputWeight').value = currentStage2.output_weight;
    }
}

function handlePrintClick(event) {
    const button = event.currentTarget;
    printLafafBarcode(
        button.dataset.barcode || '',
        button.dataset.coil || '',
        button.dataset.material || '',
        button.dataset.net || '0',
        button.dataset.total || '0',
        button.dataset.wrapping || '0',
        button.dataset.color || ''
    );
}

function printLafafBarcode(barcode, coilNumber, materialName, netWeight, totalWeight, wrappingWeight, color) {
    const numericNet = Number(netWeight || 0);
    const numericTotal = Number(totalWeight || netWeight || 0);
    const numericWrap = Number(wrappingWeight || 0);

    const cleanNet = numericNet.toFixed(3);
    const cleanTotal = numericTotal.toFixed(3);
    const cleanWrap = numericWrap.toFixed(3);

    const printWindow = window.open('', '', 'height=650,width=850');
    printWindow.document.write('<html dir="rtl"><head><title>{{ __('stages.stage3_print_barcode') }} - ' + coilNumber + '</title>');
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
    printWindow.document.write('<div class="title">{{ __('stages.stage3_barcode_title') }}</div>');
    printWindow.document.write('<div class="coil-number">' + coilNumber + '</div>');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
    printWindow.document.write('<div class="info">');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('stages.material_label') }}:</span><span class="value">' + materialName + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">الوزن الصافي (بعد خصم اللفاف):</span><span class="value">' + cleanNet + ' {{ __('stages.kg_unit') }}</span></div>');
    if (numericWrap > 0) {
        printWindow.document.write('<div class="info-row"><span class="label">{{ __('stages.total_weight_label') }}:</span><span class="value">' + cleanTotal + ' {{ __('stages.kg_unit') }}</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">وزن اللفاف:</span><span class="value">' + cleanWrap + ' {{ __('stages.kg_unit') }}</span></div>');
    } else {
        printWindow.document.write('<div class="info-row"><span class="label">{{ __('stages.total_weight_label') }}:</span><span class="value">' + cleanTotal + ' {{ __('stages.kg_unit') }}</span></div>');
    }
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('stages.stage3_color_label') }}:</span><span class="value">' + color + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('stages.date_label_print') }}:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
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

// جعل الدوال متاحة للأزرار في الواجهة
window.addLafaf = addLafaf;
window.clearForm = clearForm;
window.finishOperation = finishOperation;
window.handlePrintClick = handlePrintClick;
window.checkPendingItems = checkPendingItems;
window.checkPendingLafafs = checkPendingLafafs;
window.checkPendingTransfers = checkPendingTransfers;
window.loadPendingItem = loadPendingItem;
window.openTransferModal = openTransferModal;
window.closeTransferModal = closeTransferModal;
window.submitTransfer = submitTransfer;
window.acceptTransfer = acceptTransfer;
window.rejectTransfer = rejectTransfer;
</script>

@endsection
