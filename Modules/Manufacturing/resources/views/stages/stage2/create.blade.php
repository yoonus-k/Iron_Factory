@extends('master')

@section('title', __('stages.stage2_processing'))

@section('content')

<style>
    :root{
        --brand-1: #8e44ad;
        --brand-2: #c084fc;
        --success: #27ae60;
        --muted: #7f8c8d;
        --card-bg: #ffffff;
        --surface: #f5f7fa;
        --danger: #e74c3c;
        --radius: 12px;
    }

    .stage-container{
        max-width: 1200px;
        margin: 28px auto;
        padding: 24px;
        font-family: 'Segoe UI', Tahoma, system-ui, -apple-system, 'Helvetica Neue', Arial;
        color: #263238;
    }

    .stage-header{
        background: linear-gradient(90deg, var(--brand-1), var(--brand-2));
        color: #fff;
        padding: 28px 30px;
        border-radius: 14px;
        display:flex;
        gap: 18px;
        align-items: center;
        box-shadow: 0 10px 30px rgba(142,68,173,0.18);
    }

    .stage-header h1{ font-size: 22px; margin: 0; font-weight: 700; display:flex; gap:12px; align-items:center }
    .stage-header p{ margin:0; opacity:0.95; font-size:14px }

    .form-section{
        background: var(--card-bg);
        padding: 22px;
        border-radius: var(--radius);
        margin-top: 20px;
        box-shadow: 0 6px 18px rgba(40,50,60,0.05);
        border: 1px solid rgba(34,47,62,0.05);
    }

    .section-title{ font-size:18px; font-weight:700; color:var(--brand-1); display:flex; gap:10px; align-items:center }

    .barcode-section{ background: linear-gradient(180deg,#f7f1ff 0,#ede7ff 100%); padding:26px; border-radius:12px; border:1px dashed rgba(142,68,173,0.15); text-align:center }
    .barcode-input-wrapper{ max-width:720px; margin:0 auto; position:relative }
    .barcode-input{ width:100%; padding:20px 22px; border-radius:10px; font-size:18px; border:2px solid rgba(142,68,173,0.2); font-weight:600; box-shadow: inset 0 -6px 18px rgba(0,0,0,0.02) }
    .barcode-icon{ position:absolute; left:18px; top:50%; transform:translateY(-50%); color:var(--brand-1); font-size:22px }

    .stand-display{ display:none; padding:18px; border-radius:12px; background:linear-gradient(180deg,#fbf6ff 0,#f1ecff 100%); border-left:4px solid var(--brand-1); margin-top:20px }
    .stand-display.active{ display:block }
    .stand-info{ display:grid; grid-template-columns: repeat(4,1fr); gap:12px }
    .info-item{ background:#fff; padding:14px; border-radius:10px; box-shadow:0 4px 14px rgba(10,30,60,0.03); text-align:center }
    .info-label{ font-size:13px; color:var(--muted); margin-bottom:8px; font-weight:600 }
    .info-value{ font-size:16px; font-weight:700; color:#22303a }
    .info-item--highlight{ background:linear-gradient(135deg,#e6fff5,#f3fff9); border:2px solid #27ae60; }
    .info-item--highlight .info-value{ color:#27ae60; font-size:18px; }

    .form-row{ display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:18px; margin-top:16px }
    .form-group label{ font-size:14px; color:var(--muted); font-weight:600; margin-bottom:8px }
    .form-control, .form-select{ padding:12px 14px; border-radius:10px; border:1.5px solid #e6edf3; font-size:15px; background:#fbfeff; transition:box-shadow .18s, border-color .18s }
    .form-control:focus, .form-select:focus{ outline:none; border-color:var(--brand-1); box-shadow:0 6px 20px rgba(142,68,173,0.12) }
    textarea.form-control{ min-height:110px }

    .button-group{ display:flex; gap:12px; flex-wrap:wrap; margin-top:14px }
    .btn-primary, .btn-success, .btn-secondary{ border:none; border-radius:10px; padding:12px 20px; font-weight:700; cursor:pointer }
    .btn-primary{ background:var(--brand-1); color:white; box-shadow:0 8px 24px rgba(142,68,173,0.2) }
    .btn-success{ background:var(--success); color:white }
    .btn-secondary{ background:#8e9aa4; color:white }

    .processed-item{ display:flex; justify-content:space-between; gap:12px; align-items:center; padding:16px; border-radius:12px; background:linear-gradient(180deg,#ffffff,#fbfdff); box-shadow:0 6px 18px rgba(10,30,60,0.04); margin-bottom:12px }
    .empty-state{ padding:36px; text-align:center; color:#96a0a6 }

    .progress-wrapper{ margin-top:18px; background:#fff; padding:16px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.04) }
    .progress-meta{ display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; font-size:13px; color:#555; margin-bottom:10px }
    .progress-track{ background:#e9ecef; border-radius:8px; height:12px; overflow:hidden; box-shadow: inset 0 2px 4px rgba(0,0,0,0.08); }
    .progress-track span{ display:block; height:100%; background:linear-gradient(90deg,#27ae60,#2ecc71); width:0%; transition:width 0.3s ease }

    .form-actions{ display:flex; gap:12px; justify-content:center; margin-top:24px }

    /* Pending Items Panel - تصميم موحد مع المرحلة الأولى */
    .pending-panel{ border:1px solid rgba(231,76,60,0.2); background:linear-gradient(135deg,#fff5f5 0,#ffecec 100%); }
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
    .pending-stand-actions .btn-open-page{ background:#3498db; color:#fff; }

    @media (max-width: 900px){ .form-row{ grid-template-columns: 1fr } .stand-info{ grid-template-columns:1fr } .stage-header{ flex-direction:column; text-align:center } }
    @media (max-width: 480px){ .barcode-input{ font-size:16px; padding:14px } .btn-primary, .btn-success, .btn-secondary{ width:100%; padding:12px } }
</style>

<div class="stage-container">
    <!-- Header -->
    <div class="stage-header">
        <div style="flex: 1;">
            <h1>
                <i class="fas fa-cog"></i>
                {{ __('stages.stage2_processing_stands') }}
            </h1>
            <p>{{ __('stages.stage2_scan_barcode_and_add_processing') }}</p>
        </div>
        <a href="{{ route('manufacturing.stage2.complete-processing') }}" class="btn-secondary" style="text-decoration:none; white-space:nowrap; padding:10px 18px; border-radius:8px; display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-clock"></i> العمليات المعلقة
        </a>
    </div>

    <!-- Barcode Scanner -->
    <div class="form-section barcode-section">
        <h3 class="section-title" style="justify-content:center;"><i class="fas fa-camera"></i> {{ __('stages.stage2_scan_stand_barcode') }} <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_scan_stand_barcode_from_phase1') }}</span></span></h3>
        <div class="barcode-input-wrapper">
            <input type="text" id="standBarcode" class="barcode-input" placeholder="{{ __('stages.stage2_scan_or_type_barcode') }}" autofocus>
            <span class="barcode-icon"><i class="fas fa-barcode"></i></span>
        </div>
        <small style="color: #7f8c8d; display: block; margin-top: 16px; font-size:15px;"><i class="fas fa-lightbulb"></i> <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.stage2_scan_barcode_or_press_enter') }}</span></span></small>
    </div>

    <!-- Stand Display -->
    <div id="standDisplay" class="stand-display">
        <h4><i class="fas fa-circle-check"></i> {{ __('stages.stand_data') }}</h4>
        <div class="stand-info">
            <div class="info-item">
                <div class="info-label">{{ __('stages.barcode') }} <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.barcode') }}</span></span></div>
                <div class="info-value" id="displayBarcode">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.wire_size') }} <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.wire_size') }}</span></span></div>
                <div class="info-value" id="displayWireSize">-</div>
            </div>

            <div class="info-item">
                <div class="info-label">{{ __('stages.total_weight') }} <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.total_weight') }}</span></span></div>
                <div class="info-value" id="displayWeight">-</div>
            </div>
            <div class="info-item info-item--highlight">
                <div class="info-label">{{ __('stages.remaining_weight_stat') }} <span class="info-tooltip">?<span class="tooltip-text">{{ __('stages.remaining_weight_stat') }}</span></span></div>
                <div class="info-value" id="displayRemainingWeight">-</div>
            </div>
        </div>
        
        <!-- Stand Usage Progress - تصميم مطابق للمرحلة الأولى -->
        <div id="standProgressSection" style="margin-top: 20px;">
            <h5 style="color: var(--brand-1); margin-bottom: 10px;"><i class="fas fa-chart-pie"></i> {{ __('stages.stand_usage_status') }}</h5>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 10px;">
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 15px;">
                    <div style="text-align: center;">
                        <div style="font-size: 12px; color: #7f8c8d;">{{ __('stages.total_weight_stat') }}</div>
                        <div style="font-size: 16px; font-weight: bold; color: #2c3e50;" id="standTotalWeight">-</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 12px; color: #7f8c8d;">{{ __('stages.used_weight_stat') }}</div>
                        <div style="font-size: 16px; font-weight: bold; color: #27ae60;" id="standUsedWeight">-</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 12px; color: #7f8c8d;">{{ __('stages.remaining_weight_stat') }}</div>
                        <div style="font-size: 16px; font-weight: bold; color: #e67e22;" id="standRemainingDisplay">-</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 12px; color: #7f8c8d;">{{ __('stages.processing_count_stat') }}</div>
                        <div style="font-size: 16px; font-weight: bold; color: #3498db;" id="standProcessingsCount">0</div>
                    </div>
                </div>
                <div style="background: #e9ecef; border-radius: 6px; height: 10px; overflow: hidden;">
                    <div id="standProgressBar" style="height: 100%; background: linear-gradient(90deg, #27ae60, #2ecc71); width: 0%; transition: width 0.3s ease;"></div>
                </div>
                <div style="text-align: center; margin-top: 8px; font-size: 14px; color: #7f8c8d;">
                    <span id="standUsagePercentage">0</span>% {{ __('stages.usage_percentage') }}
                </div>
                <!-- رسالة اكتمال الاستهلاك -->
                <div id="standConsumedMessage" style="display: none; margin-top: 12px; padding: 12px; background: linear-gradient(135deg, #d4edda, #c3e6cb); border: 1px solid #28a745; border-radius: 8px; text-align: center;">
                    <i class="fas fa-check-circle" style="color: #28a745; font-size: 20px;"></i>
                    <p style="margin: 8px 0 0; color: #155724; font-weight: bold;">{{ __('stages.stand_fully_consumed') }}</p>
                </div>
            </div>
        </div>
        
        <!-- أزرار التحكم بالاستاند -->
        <div class="stand-actions" style="display: flex; gap: 12px; margin-top: 18px; flex-wrap: wrap; justify-content: center;">
            <button type="button" class="btn-success" onclick="finishStand()" id="finishStandBtn" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white; padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; font-weight: 700; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);">
                <i class="fas fa-check-circle"></i> {{ __('stages.finish_stand') }}
            </button>
            <button type="button" class="btn-transfer" onclick="showTransferStandModal()" id="transferStandBtn" style="background: linear-gradient(135deg, #c084fc 0%, #a855f7 100%); color: white; padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; font-weight: 700; box-shadow: 0 4px 15px rgba(168, 85, 247, 0.3);">
                <i class="fas fa-share"></i> {{ __('stages.transfer_to_employee') }}
            </button>
            <button type="button" class="btn-cancel" onclick="cancelCurrentStand()" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white; padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; font-weight: 700; box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);">
                <i class="fas fa-times"></i> {{ __('stages.cancel_action') }}
            </button>
        </div>
    </div>

    <!-- Pending Transfers Panel - طلبات النقل المعلقة -->
    <div id="pendingTransfersPanel" class="form-section pending-panel" style="border:1px solid rgba(39,174,96,0.3); display:none;">
        <div class="pending-panel-header">
            <h3 class="section-title" style="color:#27ae60;">
                <i class="fas fa-exchange-alt"></i>
                {{ __('stages.stand_transfer_requests') }}
            </h3>
        </div>
        <p class="helper-text" style="color:#1e8449;">{{ __('stages.stands_transferred_waiting_approval') }}</p>
        <div id="pendingTransfersList" class="pending-stands-list"></div>
    </div>

    <!-- Pending Items Panel - تصميم موحد مع المرحلة الأولى -->
    <div id="pendingItemsPanel" class="form-section pending-panel" style="border:1px solid rgba(231,76,60,0.2);">
        <div class="pending-panel-header">
            <h3 class="section-title" style="color:#c0392b;">
                <i class="fas fa-exclamation-circle"></i>
                {{ __('stages.pending_stands_waiting_finish') }}
            </h3>
            <div class="pending-panel-actions">
                <a href="{{ route('manufacturing.stage2.complete-processing') }}" style="text-decoration:none;">
                    <i class="fas fa-list"></i> {{ __('stages.view_pending_stands') }}
                </a>
                <button type="button" onclick="checkPendingItems()">
                    <i class="fas fa-sync-alt"></i> {{ __('stages.reset_button') }}
                </button>
            </div>
        </div>
        <p class="helper-text">{{ __('stages.cannot_start_new_stand') }}</p>
        <div id="pendingItemsList" class="pending-stands-list"></div>
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
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>{{ __('stages.output_weight_label') }} (وزن المخرجات بعد المعالجة) <span class="required">*</span> <span class="info-tooltip">?<span class="tooltip-text">أدخل وزن المخرجات بعد المعالجة - الهدر سيُحسب تلقائياً عند إنهاء الاستاند</span></span></label>
                <input type="number" id="outputWeight" class="form-control" step="0.01" placeholder="أدخل وزن المخرجات">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;"><i class="fas fa-lightbulb"></i> يمكنك تقسيم الاستاند لعدة معالجات - الهدر الإجمالي سيُحسب عند الإنهاء</small>
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
// Translation object for JavaScript
const translations = {
    stand: '{{ __('stages.stand_label') }}',
    material: '{{ __('stages.material_stat') }}',
    standNumber: '{{ __('stages.stand_number_stat') }}',
    processingCount: '{{ __('stages.processing_count_stat') }}',
    used: '{{ __('stages.used_weight_stat') }}',
    remaining: '{{ __('stages.remaining_weight_stat') }}',
    kg: '{{ __('stages.kg_unit') }}',
    continueWork: '{{ __('stages.continue_work') }}',
    finishStand: '{{ __('stages.finish_stand') }}',
    transferToEmployee: '{{ __('stages.transfer_to_employee') }}',
    noPendingStands: '{{ __('stages.no_pending_stands') }}',
    pendingStandHint: '{{ __('stages.pending_stand_hint') }}',
    errorOccurred: '{{ __('stages.error_occurred') }}',
    understand: '{{ __('stages.understand') }}',
    standBarcode: '{{ __('stages.stand_barcode') }}',
    remainingWeight: '{{ __('stages.remaining_weight_label') }}',
    newEmployee: '{{ __('stages.new_employee') }}',
    selectEmployee: '{{ __('stages.select_employee') }}',
    transferReason: '{{ __('stages.transfer_reason') }}',
    endOfShift: '{{ __('stages.end_of_shift') }}',
    employeeBusy: '{{ __('stages.employee_busy') }}',
    workDistribution: '{{ __('stages.work_distribution') }}',
    otherReason: '{{ __('stages.other_reason') }}',
    confirmTransfer: '{{ __('stages.confirm_transfer') }}',
    cancelAction: '{{ __('stages.cancel_action') }}',
    standTransferredSuccess: '{{ __('stages.stand_transferred_success') }}',
    standWillAppear: '{{ __('stages.stand_will_appear_in_employee_panel') }}',
    mustFinishCurrentStand: '{{ __('stages.must_finish_current_stand') }}',
    pendingStandsExist: '{{ __('stages.pending_stands_exist') }}',
    mustFinishAllStands: '{{ __('stages.must_finish_all_stands') }}',
    pendingStandExists: '{{ __('stages.pending_stand_exists') }}',
    mustFinishOrTransfer: '{{ __('stages.must_finish_or_transfer') }}',
    noProcessingsAdded: '{{ __('stages.no_processings_added') }}',
    pendingExcessWaste: '{{ __('stages.pending_excess_waste') }}',
    confirmed: '{{ __('stages.confirmed') }}',
    pendingStatus: '{{ __('stages.pending_status') }}',
    processingWeight: '{{ __('stages.processing_weight') }}',
    details: '{{ __('stages.details') }}',
    notes: '{{ __('stages.notes') }}',
    pendingProcessingsExist: '{{ __('stages.pending_processings_exist') }}',
    mustFinishStandOrTransfer: '{{ __('stages.must_finish_stand_or_transfer') }}',
    standFinishedConfirmed: '{{ __('stages.stand_finished_confirmed') }}',
    canScanNewStand: '{{ __('stages.can_scan_new_stand') }}',
    printBarcodeStage2: '{{ __('stages.print_barcode_stage2') }}',
    barcodeStage2Title: '{{ __('stages.barcode_stage2_title') }}',
    netWeight: '{{ __('stages.net_weight_print') }}',
    waste: '{{ __('stages.waste_print') }}',
    totalProcessings: '{{ __('stages.total_processings') }}',
    totalWeightSum: '{{ __('stages.total_weight_sum') }}',
    totalWaste: '{{ __('stages.total_waste_sum') }}',
    dateLabel: '{{ __('stages.date_label') }}',
    accept: '{{ __('stages.accept') }}',
    reject: '{{ __('stages.reject') }}',
    transferredStand: '{{ __('stages.transferred_stand') }}',
    from: '{{ __('stages.from') }}',
    acceptTransfer: '{{ __('stages.accept_transfer') }}',
    rejectTransfer: '{{ __('stages.reject_transfer') }}',
    transferAcceptedSuccess: '{{ __('stages.transfer_accepted_success') }}',
    transferRejectedSuccess: '{{ __('stages.transfer_rejected_success') }}',
    noPendingTransfers: '{{ __('stages.no_pending_transfers') }}',
    pendingTransfersHint: '{{ __('stages.pending_transfers_hint') }}'
};

let processedItems = [];
let currentStand = null;
let standOriginalWeight = 0; // الوزن الأصلي للاستاند (weight من قاعدة البيانات)
let standInitialLoadedWeight = 0; // الوزن عند تحميل الاستاند (قبل المعالجات الحالية)
let standRemainingWeight = 0; // الوزن المتبقي
let standProcessedWeight = 0; // الوزن الذي تم معالجته
let pendingItems = [];
let pendingItemsCount = 0;
// helper: returns true when the currently loaded stand still has unconfirmed processings
function hasCurrentStandProcessings() {
    if (!currentStand) {
        return false;
    }
    return processedItems.some(item => item.stage1_barcode === currentStand.barcode && item.status !== 'confirmed');
}

// تحميل العناصر المعلقة عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    checkPendingItems();
    checkPendingTransfers(); // التحقق من طلبات النقل المعلقة
    cleanUpLocalStorage(); // تنظيف البيانات المحذوفة قبل العرض
    
    // تحميل الباركود من URL إذا كان موجوداً
    const urlParams = new URLSearchParams(window.location.search);
    const barcodeFromUrl = urlParams.get('barcode');
    if (barcodeFromUrl) {
        document.getElementById('standBarcode').value = barcodeFromUrl;
        loadStand(barcodeFromUrl);
    }
    
    // تحديث تلقائي كل 30 ثانية
    setInterval(() => {
        checkPendingItems();
        checkPendingTransfers();
    }, 30000);
    
    // حفظ offline كل 30 ثانية
    setInterval(saveOffline, 30000);
});

// تحميل قائمة الاستاندات من localStorage
function loadStandsList() {
    try {
        const saved = localStorage.getItem('stage2_processings');
        if (saved) {
            // تحميل المعالجات واستبعاد المؤكدة
            const allProcessings = JSON.parse(saved);
            processedItems = allProcessings.filter(item => item.status !== 'confirmed');
            
            // تحديث localStorage بالمعالجات المعلقة فقط
            localStorage.setItem('stage2_processings', JSON.stringify(processedItems));
            
            renderProcessed();
            console.log('✅ تم تحميل ' + processedItems.length + ' معالجة معلقة من localStorage');
        }
    } catch (error) {
        console.error('❌ خطأ في تحميل البيانات:', error);
    }
}

// تنظيف البيانات المحذوفة من localStorage
function cleanUpLocalStorage() {
    console.log('🧹 جاري تنظيف البيانات المحذوفة...');
    
    const saved = localStorage.getItem('stage2_processings');
    if (!saved) {
        console.log('ℹ️ لا توجد بيانات محلية للتنظيف');
        renderProcessed();
        return;
    }
    
    try {
        const allProcessings = JSON.parse(saved);
        
        // إذا كانت القائمة فارغة، لا حاجة للتحقق
        if (!allProcessings || allProcessings.length === 0) {
            processedItems = [];
            renderProcessed();
            console.log('ℹ️ القائمة المحلية فارغة');
            return;
        }
        
        // جلب السجلات المحذوفة التي لها ID من السيرفر
        const serverIds = allProcessings
            .filter(item => item.id && item.saved && item.syncedFromServer)
            .map(item => item.id);
        
        if (serverIds.length === 0) {
            // كل المعالجات محلية فقط (لم تُحفظ في السيرفر بعد)
            processedItems = allProcessings.filter(item => item.status !== 'confirmed');
            renderProcessed();
            console.log('ℹ️ جميع المعالجات محلية فقط');
            return;
        }
        
        // التحقق من السيرفر
        fetch('/stage2/verify-processings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ ids: serverIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const validIds = new Set(data.valid_ids || []);
                
                // تصفية المعالجات: إزالة المحذوفة من السيرفر
                processedItems = allProcessings.filter(item => {
                    // إذا كانت معالجة محلية فقط، احتفظ بها
                    if (!item.id || !item.saved || !item.syncedFromServer) {
                        return item.status !== 'confirmed';
                    }
                    // إذا كانت محفوظة في السيرفر، تحقق من وجودها
                    return validIds.has(item.id) && item.status !== 'confirmed';
                });
                
                const removedCount = allProcessings.length - processedItems.length;
                
                // تحديث localStorage
                localStorage.setItem('stage2_processings', JSON.stringify(processedItems));
                
                renderProcessed();
                
                if (removedCount > 0) {
                    console.log(`✅ تم إزالة ${removedCount} معالجة محذوفة من السيرفر`);
                } else {
                    console.log('✅ جميع البيانات المحلية متزامنة مع السيرفر');
                }
            } else {
                // في حالة الخطأ، اعرض البيانات المحلية كما هي
                processedItems = allProcessings.filter(item => item.status !== 'confirmed');
                renderProcessed();
                console.warn('⚠️ فشل التحقق من السيرفر، عرض البيانات المحلية');
            }
        })
        .catch(error => {
            console.error('❌ خطأ في التحقق من السيرفر:', error);
            // في حالة الخطأ، اعرض البيانات المحلية
            processedItems = allProcessings.filter(item => item.status !== 'confirmed');
            renderProcessed();
        });
        
    } catch (error) {
        console.error('❌ خطأ في تنظيف البيانات:', error);
        processedItems = [];
        renderProcessed();
    }
}

function syncServerProcessingsWithLocal(barcode, serverProcessings, materialName) {
    if (!Array.isArray(serverProcessings)) return;

    const serverIds = new Set(serverProcessings.map(proc => proc.id));

    processedItems = processedItems.filter(item => {
        if (item.stage1_barcode !== barcode) return true;
        if (item.syncedFromServer && !serverIds.has(item.id)) {
            return false;
        }
        return true;
    });

    serverProcessings.forEach(proc => {
        const normalized = {
            id: proc.id,
            stage1_barcode: proc.parent_barcode || barcode,
            barcode: proc.barcode,
            process_type: proc.process_type || 'processing',
            total_weight: parseFloat(proc.output_weight ?? proc.total_weight ?? 0) || 0,
            net_weight: parseFloat(proc.net_weight ?? proc.output_weight ?? 0) || 0,
            material_name: proc.material_name || materialName || 'غير محدد',
            process_details: proc.process_details,
            notes: proc.notes,
            status: proc.status || 'in_progress',
            saved: true,
            syncedFromServer: true
        };

        const existingIndex = processedItems.findIndex(item => item.id === normalized.id);
        if (existingIndex !== -1) {
            processedItems[existingIndex] = { ...processedItems[existingIndex], ...normalized };
        } else {
            processedItems.push(normalized);
        }
    });
}

// حفظ offline
function saveOffline() {
    try {
        localStorage.setItem('stage2_processings', JSON.stringify(processedItems));
        console.log('✅ Offline save: ' + processedItems.length + ' processings');
    } catch (error) {
        console.error('❌ خطأ في الحفظ:', error);
    }
}

// جلب العناصر المعلقة من API
function checkPendingItems() {
    console.log('🔍 جاري التحقق من العناصر المعلقة...');
    
    fetch('/stage2/pending-items', {
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

// عرض لوحة العناصر المعلقة - تصميم موحد مع المرحلة الأولى
function renderPendingItemsPanel() {
    const panel = document.getElementById('pendingItemsPanel');
    const list = document.getElementById('pendingItemsList');
    
    if (!panel || !list) return;
    
    // دائماً نعرض اللوحة
    panel.style.display = 'block';
    panel.style.visibility = 'visible';
    panel.style.opacity = '1';
    
    if (!pendingItemsCount || pendingItems.length === 0) {
        list.innerHTML = `
            <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; text-align: center; color: #7f8c8d;">
                <i class="fas fa-check-circle" style="color:#27ae60; font-size: 24px; margin-bottom: 10px;"></i>
                <br>
                <strong style="margin: 0 5px; display: block; margin-bottom: 8px;">${translations.noPendingStands}</strong>
                <div style="font-size: 14px;">${translations.pendingStandHint}</div>
            </div>
        `;
        return;
    }
    
    list.innerHTML = pendingItems.map(item => {
        // remaining_weight هو الوزن المتبقي الفعلي في قاعدة البيانات (بعد الخصم)
        // total_processed هو إجمالي ما تم معالجته
        // لا نحتاج لطرح processed من remaining_weight لأنه مخصوم مسبقاً
        const remainingWeight = parseFloat(item.remaining_weight || 0);
        const processed = parseFloat(item.total_processed || 0);
        // الوزن الأصلي = المتبقي + المعالج
        const originalWeight = remainingWeight + processed;
        const usagePercent = originalWeight > 0 ? Math.min(100, (processed / originalWeight) * 100) : 0;
        const isFullyConsumed = remainingWeight <= 0;

        return `
            <div class="pending-stand-card">
                <div class="pending-stand-info">
                    <strong>${translations.stand}: ${item.barcode}</strong>
                    <span>${translations.standNumber}: ${item.stand_number || '-'}</span>
                    <span>${translations.material}: ${item.material_name || '-'}</span>
                    <span>${translations.processingCount}: ${item.pending_count || 0}</span>
                    <span>${translations.used}: ${processed.toFixed(2)} / ${originalWeight.toFixed(2)} ${translations.kg}</span>
                    <span>${translations.remaining}: <strong style="color:${isFullyConsumed ? '#e74c3c' : '#27ae60'}">${remainingWeight.toFixed(2)} ${translations.kg}</strong></span>
                </div>
                <div class="pending-stand-actions">
                    <button class="btn-continue" type="button" onclick="loadPendingItem('${item.barcode}')">
                        <i class="fas fa-play"></i> ${translations.continueWork}
                    </button>
                    <button class="btn-finish-stand" type="button" onclick="finishStandFromPending('${item.barcode}')">
                        <i class="fas fa-check-double"></i> ${translations.finishStand}
                    </button>
                    <button class="btn-transfer-stand" type="button" 
                            onclick="showTransferStandModalForPending('${item.barcode}')" 
                            style="${isFullyConsumed ? 'cursor:not-allowed; opacity:0.6;' : ''}"
                            ${isFullyConsumed ? 'disabled' : ''}>
                        <i class="fas fa-share"></i> ${translations.transferToEmployee}
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

// تحميل عنصر معلق
function loadPendingItem(barcode) {
    if (!barcode) return;
    
    // فحص إذا كان هناك استاند مفتوح حالياً
    if (currentStand && currentStand.barcode !== barcode && hasCurrentStandProcessings()) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ ' + translations.pendingStandExists,
            html: `<div style="text-align:right; direction:rtl;">${translations.mustFinishCurrentStand} (${currentStand.barcode})</div>`,
            confirmButtonText: translations.understand
        });
        return;
    }
    
    loadStand(barcode);
}

// Barcode scanner
document.getElementById('standBarcode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const barcode = this.value.trim();
        if (barcode) {
            // فحص إذا كان هناك استاند معلق
            if (currentStand && currentStand.barcode !== barcode && hasCurrentStandProcessings()) {
                Swal.fire({
                    icon: 'warning',
                    title: '⚠️ ' + translations.pendingStandExists,
                    html: `<div style="text-align:right; direction:rtl;">${translations.mustFinishCurrentStand} (${currentStand.barcode})</div>`,
                    confirmButtonText: translations.understand
                });
                this.value = '';
                return;
            }
            loadStand(barcode);
        }
        this.value = ''; // Clear for next scan
    }
});

// تحديث الوزن المتبقي عند إدخال وزن المعالجة
document.getElementById('outputWeight').addEventListener('input', function() {
    if (currentStand && this.value) {
        const processingWeight = parseFloat(this.value) || 0;
        const remainingAfter = standRemainingWeight - processingWeight;
        
        // تحديث عرض الوزن المتبقي مباشرة
        const remainingDisplay = document.getElementById('displayRemainingWeight');
        if (remainingDisplay) {
            if (remainingAfter < 0) {
                remainingDisplay.style.color = '#e74c3c';
                remainingDisplay.textContent = '⚠️ ' + remainingAfter.toFixed(2) + ' كجم (تجاوز!)';
            } else {
                remainingDisplay.style.color = '#27ae60';
                remainingDisplay.textContent = remainingAfter.toFixed(2) + ' كجم';
            }
        }
    }
});

function loadStand(barcode) {
    if (!barcode) {
        alert('⚠️ يرجى إدخال باركود الاستاند!');
        return;
    }

    // فحص إذا كان هناك استاند معلق من القائمة
    if (pendingItemsCount > 0) {
        // التحقق إذا كان الباركود المطلوب ليس من ضمن المعلقة
        const isPendingBarcode = pendingItems.some(item => item.barcode === barcode);
        
        if (!isPendingBarcode) {
            const pendingList = pendingItems.map(item => `• ${item.barcode} (الاستاند: ${item.stand_number})`).join('<br>');
            Swal.fire({
                icon: 'warning',
                title: '⚠️ يوجد استاندات معلقة',
                html: `<div style="text-align:right; direction:rtl;">
                    <p><strong>لديك ${pendingItemsCount} استاند معلق:</strong></p>
                    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin: 10px 0; border-right: 4px solid #ffc107;">
                        ${pendingList}
                    </div>
                    <p style="color: #d63031; font-weight: bold;">⛔ يجب إنهاء جميع الاستاندات المعلقة قبل البدء باستاند جديد</p>
                </div>`,
                confirmButtonText: 'فهمت',
                confirmButtonColor: '#e74c3c',
                width: '600px'
            });
            document.getElementById('standBarcode').value = '';
            return;
        }
    }

    // فحص إذا كان هناك استاند معلق حالياً
    if (currentStand && currentStand.barcode !== barcode && hasCurrentStandProcessings()) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ يوجد استاند معلق',
            html: `<div style="text-align:right; direction:rtl;">
                <p>لديك استاند معلق حالياً: <strong>${currentStand.barcode}</strong></p>
                <p>يجب إنهاء الاستاند الحالي أو نقله قبل فتح استاند جديد.</p>
            </div>`,
            confirmButtonText: 'فهمت',
            confirmButtonColor: '#e74c3c'
        });
        document.getElementById('standBarcode').value = '';
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

            // دمج المعالجات المحفوظة على السيرفر لهذا الاستاند
            const serverProcessings = result.pending_processings || [];
            syncServerProcessingsWithLocal(data.barcode, serverProcessings, data.material_name);

            // ⚡ الوزن الأصلي هو weight من قاعدة البيانات، والوزن المتبقي هو remaining_weight
            standOriginalWeight = parseFloat(data.weight) || 0; // الوزن الأصلي للاستاند (ثابت)
            standInitialLoadedWeight = parseFloat(data.remaining_weight) || parseFloat(data.weight) || 0; // الوزن عند التحميل
            standRemainingWeight = standInitialLoadedWeight; // الوزن المتبقي يبدأ من الوزن المحمّل
            standProcessedWeight = 0; // لا نحتاج حساب محلي لأن السيرفر يخصم تلقائياً
            
            currentStand = {
                id: data.id || null,
                barcode: data.barcode,
                wire_size: data.wire_size || '0',
                original_weight: standOriginalWeight, // الوزن الأصلي الثابت
                initial_loaded_weight: standInitialLoadedWeight, // الوزن عند التحميل
                remaining_weight: standRemainingWeight,
                material_id: data.material_id,
                source: source
            };

            // Display stand data
            document.getElementById('displayBarcode').textContent = currentStand.barcode;
            document.getElementById('displayWireSize').textContent = currentStand.wire_size + ' مم';
            document.getElementById('displayWeight').textContent = standOriginalWeight.toFixed(2) + ' كجم';
            document.getElementById('displayRemainingWeight').textContent = standRemainingWeight.toFixed(2) + ' كجم';

            // إضافة: ملء حقل وزن الدخول بالوزن المتبقي (وليس الأصلي)
            const inputWeightField = document.getElementById('inputWeight');
            if (inputWeightField) {
                inputWeightField.value = standRemainingWeight.toFixed(2);
            }
            
            // حساب الوزن المعالج (= الوزن الأصلي - الوزن المتبقي)
            standProcessedWeight = standOriginalWeight - standRemainingWeight;
            if (standProcessedWeight < 0) standProcessedWeight = 0;

            // تحديث Progress Bar
            updateStandProgressBar();

            document.getElementById('standDisplay').classList.add('active');

            // Focus on process type
            document.getElementById('processType').focus();

            // Show success message
            showToast('{{ __("stages.stand_data_loaded_successfully") }}', 'success');

            renderProcessed();
            saveOffline();
        })
        .catch(error => {
            alert('{{ __("stages.error_label") }}: ' + error.message);
            document.getElementById('standBarcode').focus();
        });
}

function addProcessed() {
    if (!currentStand) {
        alert('⚠️ يرجى مسح باركود الاستاند أولاً!');
        document.getElementById('standBarcode').focus();
        return;
    }

    const processType = document.getElementById('processType').value;
    const outputWeight = parseFloat(document.getElementById('outputWeight').value) || 0;
    const processDetails = document.getElementById('processDetails').value.trim();
    const notes = document.getElementById('notes').value.trim();

    if (!processType || !outputWeight || outputWeight <= 0) {
        alert('⚠️ يرجى ملء نوع المعالجة ووزن المعالجة!');
        return;
    }

    // فحص الوزن المتبقي
    if (outputWeight > standRemainingWeight) {
        Swal.fire({
            icon: 'error',
            title: '⚠️ وزن المعالجة أكبر من الوزن المتبقي',
            html: `<div style="text-align:right; direction:rtl;">
                <p>الوزن المتبقي: <strong>${standRemainingWeight.toFixed(2)} كجم</strong></p>
                <p>وزن المعالجة المدخل: <strong>${outputWeight.toFixed(2)} كجم</strong></p>
            </div>`,
            confirmButtonText: 'فهمت'
        });
        return;
    }

    const data = {
        material_id: currentStand.material_id || null,
        stage1_id: currentStand.id || null,
        stage1_barcode: currentStand.barcode,
        source: currentStand.source || 'stage1',
        process_type: processType,
        total_weight: outputWeight,
        net_weight: outputWeight,
        process_details: processDetails,
        notes: notes,
        status: 'pending'
    };

    // حفظ فوري في السيرفر بحالة pending
    const addBtn = event.target;
    addBtn.disabled = true;
    addBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';

    fetch('/stage2/store-single', {
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
        
        if (result.success) {
            const processed = {
                id: result.data.stage2_id,
                stage1_barcode: currentStand.barcode,
                barcode: result.data.stage2_barcode || result.data.barcode,
                process_type: processType,
                total_weight: outputWeight,
                net_weight: result.data.net_weight || outputWeight,
                material_name: result.data.material_name,
                process_details: processDetails,
                notes: notes,
                status: 'in_progress',
                saved: true
            };
            
            processedItems.push(processed);
            
            // ⚡ تحديث الوزن المتبقي فقط (السيرفر يخصم تلقائياً)
            standRemainingWeight = standRemainingWeight - outputWeight;
            if (standRemainingWeight < 0) standRemainingWeight = 0;
            // ⚠️ لا نُحدّث standOriginalWeight - يجب أن يبقى ثابتاً
            document.getElementById('displayRemainingWeight').textContent = standRemainingWeight.toFixed(2) + ' كجم';
            // ⚠️ لا نُحدّث displayWeight - الوزن الأصلي لا يتغير
            
            // تحديث حقل وزن الدخول
            const inputWeightField = document.getElementById('inputWeight');
            if (inputWeightField) {
                inputWeightField.value = standRemainingWeight.toFixed(2);
            }
            
            // تحديث Progress Bar
            updateStandProgressBar();
            
            renderProcessed();
            clearForm();
            saveOffline();
            showToast('✅ تم حفظ المعالجة بنجاح', 'success');
            document.getElementById('processType').focus();
        } else {
            throw new Error(result.message || 'حدث خطأ أثناء الحفظ');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: '❌ خطأ في الحفظ',
            text: error.message,
            confirmButtonText: 'حسناً'
        });
    })
    .finally(() => {
        addBtn.disabled = false;
        addBtn.innerHTML = '<i class="fas fa-plus"></i> {{ __("stages.stage2_add_processing") }}';
    });
}

// تحديث Progress Bar للاستاند
function updateStandProgressBar() {
    if (!currentStand) return;

    const processed = standProcessedWeight || 0;
    const original = standOriginalWeight || 0;
    const initialLoaded = standInitialLoadedWeight || 0; // الوزن عند التحميل (المتبقي من المرحلة الأولى)
    const remaining = standRemainingWeight || 0;
    
    // حساب الوزن المعالج في الجلسة الحالية (من الوزن المحمّل وليس الأصلي)
    const processedInSession = initialLoaded - remaining;
    
    // تحديث الحقول الرئيسية
    document.getElementById('displayRemainingWeight').textContent = remaining.toFixed(2) + ' كجم';
    
    // تحديث البيانات الجديدة (تصميم المرحلة الأولى)
    const standTotalWeight = document.getElementById('standTotalWeight');
    if (standTotalWeight) {
        standTotalWeight.textContent = initialLoaded.toFixed(2) + ' كجم';
    }
    const standUsedWeight = document.getElementById('standUsedWeight');
    if (standUsedWeight) {
        standUsedWeight.textContent = processedInSession.toFixed(2) + ' كجم';
    }
    const standRemaining = document.getElementById('standRemainingDisplay');
    if (standRemaining) {
        standRemaining.textContent = remaining.toFixed(2) + ' كجم';
    }
    const processingsCount = document.getElementById('standProcessingsCount');
    if (processingsCount) {
        // حساب عدد المعالجات للاستاند الحالي
        const currentStandProcessings = processedItems.filter(p => p.stage1_barcode === currentStand.barcode).length;
        processingsCount.textContent = currentStandProcessings;
    }
    
    // حساب النسبة المئوية من الوزن المحمّل (وليس الأصلي)
    const percentage = initialLoaded > 0 ? (processedInSession / initialLoaded * 100) : 0;
    
    // تحديث Progress Bar
    const progressBar = document.getElementById('standProgressBar');
    if (progressBar) {
        progressBar.style.width = Math.min(percentage, 100).toFixed(1) + '%';
        // تغيير اللون إذا اكتمل الاستهلاك
        if (percentage >= 100) {
            progressBar.style.background = 'linear-gradient(90deg, #27ae60, #2ecc71)';
        }
    }
    
    // تحديث النسبة المئوية
    document.getElementById('standUsagePercentage').textContent = Math.min(percentage, 100).toFixed(1);
    
    // إظهار رسالة عند اكتمال استهلاك الاستاند
    if (remaining <= 0 && initialLoaded > 0) {
        const consumedMessage = document.getElementById('standConsumedMessage');
        if (consumedMessage) {
            consumedMessage.style.display = 'block';
        }
    } else {
        const consumedMessage = document.getElementById('standConsumedMessage');
        if (consumedMessage) {
            consumedMessage.style.display = 'none';
        }
    }
}

function renderProcessed() {
    const list = document.getElementById('processedList');
    
    // عرض فقط المعالجات غير المؤكدة (المعلقة)
    const pendingProcessings = processedItems.filter(item => item.status !== 'confirmed');
    
    document.getElementById('processedCount').textContent = pendingProcessings.length;
    document.getElementById('submitBtn').disabled = pendingProcessings.length === 0;

    if (pendingProcessings.length === 0) {
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

    list.innerHTML = pendingProcessings.map((item, index) => {
        // تحديد حالة العنصر
        const isPendingApproval = item.status === 'pending_approval' || item.pending_approval === true;
        const isConfirmed = item.status === 'confirmed';
        const isPending = !isPendingApproval && !isConfirmed;
        
        let statusBadge = '';
        let borderColor = '#ffc107';
        
        if (isPendingApproval) {
            statusBadge = '<span style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; box-shadow: 0 2px 8px rgba(231,76,60,0.3);">⛔ معلق (هدر زائد)</span>';
            borderColor = '#e74c3c';
        } else if (isConfirmed) {
            statusBadge = '<span style="background: #27ae60; color: white; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">✓ مؤكد</span>';
            borderColor = '#27ae60';
        } else {
            statusBadge = '<span style="background: #ffc107; color: #000; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">⏳ معلق</span>';
            borderColor = '#ffc107';
        }
        
        return `
        <div class="processed-item" style="border-right: 4px solid ${borderColor}; background: linear-gradient(135deg, #f8fcff 0%, #eef8ff 100%); display: flex; justify-content: space-between; align-items: start; padding: 18px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 4px 12px rgba(11, 95, 165, 0.1);">
            <div class="processed-info" style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; flex-wrap: wrap;">
                    <strong style="color: #2c3e50; font-size: 16px;">
                        <i class="fas fa-cog" style="color: #8e44ad;"></i> ${item.barcode || item.stage1_barcode} → ${processTypeNames[item.process_type]}
                    </strong>
                    ${statusBadge}
                </div>
                <small style="display: block; line-height: 1.6;">
                    <strong>الباركود:</strong> <code style="background: #f8f9fa; padding: 2px 6px; border-radius: 4px; font-family: monospace;">${item.barcode || item.stage1_barcode}</code><br>
                    <strong>وزن المعالجة:</strong> ${parseFloat(item.total_weight).toFixed(2)} كجم
                    ${item.process_details ? '<br>📝 <strong>تفاصيل:</strong> ' + item.process_details : ''}
                    ${item.notes ? '<br>💬 <strong>ملاحظات:</strong> ' + item.notes : ''}
                    ${isPendingApproval ? '<br><span style="color: #e74c3c; font-weight: bold;">⚠️ تم إيقاف الانتقال للمرحلة الثالثة بسبب تجاوز نسبة الهدر (' + (item.waste_percentage || 0).toFixed(2) + '% > ' + (item.allowed_percentage || 0).toFixed(2) + '%)</span>' : ''}
                </small>
            </div>
            <div class="stand-actions" style="display: flex; gap: 8px; flex-wrap: wrap;">
                ${item.barcode ? `<button onclick="printStage2Barcode('${item.barcode}', '${item.stage1_barcode}', '${item.material_name || 'غير محدد'}', ${parseFloat(item.total_weight).toFixed(2)})" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; border: none; padding: 10px 16px; border-radius: 8px; cursor: pointer; font-weight: 600; box-shadow: 0 3px 10px rgba(52, 152, 219, 0.3);">
                    <i class="fas fa-print"></i> طباعة
                </button>` : ''}
            </div>
        </div>
        `;
    }).join('');
}

// حذف معالجة (فقط المعالجات المعلقة)
function deleteProcessing(index) {
    const item = processedItems[index];
    
    if (item.status === 'completed') {
        alert('⚠️ لا يمكن حذف معالجة منتهية!');
        return;
    }
    
    if (!confirm('هل أنت متأكد من حذف هذه المعالجة؟')) return;
    
    // حذف من السيرفر
    fetch(`/stage2/delete-processing/${item.id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // حذف من القائمة المحلية
            const deleted = processedItems[index];
            processedItems.splice(index, 1);
            
            // ⚡ إعادة الوزن المحذوف إلى الوزن المتبقي (السيرفر يعيده تلقائياً)
            if (currentStand && deleted.stage1_barcode === currentStand.barcode) {
                standRemainingWeight += parseFloat(deleted.total_weight);
                standOriginalWeight = standRemainingWeight;
                document.getElementById('displayRemainingWeight').textContent = standRemainingWeight.toFixed(2) + ' كجم';
                document.getElementById('displayWeight').textContent = standRemainingWeight.toFixed(2) + ' كجم';
                
                // تحديث حقل وزن الدخول
                const inputWeightField = document.getElementById('inputWeight');
                if (inputWeightField) {
                    inputWeightField.value = standRemainingWeight.toFixed(2);
                }
                
                // تحديث Progress Bar
                updateStandProgressBar();
            }
            
            renderProcessed();
            saveOffline();
            showToast('✅ تم حذف المعالجة وإعادة الوزن', 'success');
        } else {
            throw new Error(data.message || 'فشل الحذف');
        }
    })
    .catch(error => {
        alert('❌ خطأ: ' + error.message);
    });
}

function clearForm() {
    document.getElementById('processType').value = '';
    document.getElementById('processDetails').value = '';
    document.getElementById('notes').value = '';
    document.getElementById('outputWeight').value = '';
    
    // إعادة تعيين عرض الوزن المتبقي
    if (currentStand) {
        document.getElementById('displayRemainingWeight').textContent = standRemainingWeight.toFixed(2) + ' كجم';
        document.getElementById('displayRemainingWeight').style.color = '#27ae60';
    }
}

// إلغاء الاستاند الحالي (إخفاء بياناته فقط بدون حذف المعالجات)
function cancelCurrentStand() {
    if (!currentStand) {
        alert('⚠️ لا يوجد استاند محمل!');
        return;
    }
    
    // التحقق من وجود معالجات معلقة
    if (hasCurrentStandProcessings()) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ لا يمكن إلغاء الاستاند',
            html: `<div style="text-align:right; direction:rtl;">
                <p>يوجد معالجات معلقة لهذا الاستاند.</p>
                <p>يجب إنهاء الاستاند أو نقله لموظف آخر.</p>
            </div>`,
            confirmButtonText: 'فهمت'
        });
        return;
    }
    
    // مسح بيانات الاستاند الحالي
    currentStand = null;
    standOriginalWeight = 0;
    standRemainingWeight = 0;
    standProcessedWeight = 0;
    document.getElementById('standDisplay').classList.remove('active');
    document.getElementById('standBarcode').value = '';
    document.getElementById('standBarcode').focus();
    
    showToast('✅ تم إلغاء الاستاند', 'success');
}

// إنهاء الاستاند (حساب الهدر وإغلاقه)
function finishStand() {
    if (!currentStand) {
        alert('⚠️ لا يوجد استاند محمل!');
        return;
    }
    
    const standProcessings = processedItems.filter(p => p.stage1_barcode === currentStand.barcode);
    if (standProcessings.length === 0) {
        alert('⚠️ يجب إضافة معالجة واحدة على الأقل قبل إنهاء الاستاند!');
        return;
    }
    
    if (!confirm(`هل أنت متأكد من إنهاء الاستاند ${currentStand.barcode}؟\n\nسيتم حساب الهدر الكلي ومقارنته بالنسبة المسموح بها.`)) {
        return;
    }
    
    // إنهاء الاستاند وتحويل حالة المعالجات إلى confirmed
    fetch('/stage2/finish-stand', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            stand_barcode: currentStand.barcode,
            processing_ids: standProcessings.map(p => p.id) // إرسال IDs فقط
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // مسح البيانات المحلية
            localStorage.removeItem('stage2_processings');
            
            // تفريغ قائمة المعالجات المعروضة
            processedItems = processedItems.filter(p => p.stage1_barcode !== currentStand.barcode);
            renderProcessed();
            
            // عرض رسالة النجاح أو التحذير ثم إعادة تحميل الصفحة
            if (data.exceeded) {
                Swal.fire({
                    icon: 'warning',
                    title: data.alert_title || '⚠️ تم إنهاء الاستاند مع تجاوز الهدر',
                    html: data.alert_message || `<div style="text-align:right; direction:rtl;">نسبة الهدر: ${data.data.waste_percentage}%<br>النسبة المسموحة: ${data.data.allowed_percentage}%</div>`,
                    confirmButtonText: 'فهمت',
                    width: '600px'
                }).then(() => {
                    // إعادة تحميل الصفحة مثل المرحلة الأولى
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'success',
                    title: '✅ تم إنهاء الاستاند بنجاح',
                    html: `
                        <div style="text-align: right; direction: rtl;">
                            <div style="background: #d4edda; padding: 20px; border-radius: 10px;">
                                <p style="margin: 0; color: #155724;">تم إنهاء الاستاند وتأكيد جميع المعالجات.</p>
                                <p style="margin: 10px 0 0; color: #155724;">يمكنك الآن مسح استاند جديد.</p>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'رائع!',
                    confirmButtonColor: '#28a745',
                    width: '500px'
                }).then(() => {
                    // إعادة تحميل الصفحة مثل المرحلة الأولى
                    window.location.reload();
                });
            }
        } else {
            showToast('❌ ' + (data.message || 'حدث خطأ'), 'error');
        }
    })
    .catch(error => {
        console.error('خطأ في إنهاء الاستاند:', error);
        showToast('❌ حدث خطأ أثناء إنهاء الاستاند', 'error');
    });
}

// إنهاء استاند من لوحة المعلقة
async function finishStandFromPending(barcode) {
    if (!barcode) return;
    
    // جلب معالجات الاستاند
    try {
        const response = await fetch(`/stage2/get-by-barcode/${barcode}`);
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'فشل تحميل بيانات الاستاند');
        }
        
        // جلب المعالجات المعلقة لهذا الاستاند
        const processingsResponse = await fetch('/stage2/pending-items');
        const processingsData = await processingsResponse.json();
        
        const stand = processingsData.items?.find(item => item.barcode === barcode);
        if (!stand) {
            throw new Error('لم يتم العثور على معالجات لهذا الاستاند');
        }
        
        if (!confirm(`هل أنت متأكد من إنهاء الاستاند ${barcode}؟\n\nسيتم حساب الهدر الكلي ومقارنته بالنسبة المسموح بها.`)) {
            return;
        }
        
        // إنهاء الاستاند
        const finishResponse = await fetch('/stage2/finish-stand', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                stand_barcode: barcode,
                processing_ids: [] // سيتم جلبها من السيرفر
            })
        });
        
        const finishData = await finishResponse.json();
        
        if (finishData.success) {
            // مسح البيانات المحلية
            localStorage.removeItem('stage2_processings');
            
            if (finishData.exceeded) {
                await Swal.fire({
                    icon: 'warning',
                    title: finishData.alert_title || '⚠️ تم إنهاء الاستاند مع تجاوز الهدر',
                    html: finishData.alert_message,
                    confirmButtonText: 'فهمت',
                    width: '600px'
                });
            } else {
                await Swal.fire({
                    icon: 'success',
                    title: '✅ تم إنهاء الاستاند بنجاح',
                    html: `
                        <div style="text-align: right; direction: rtl;">
                            <div style="background: #d4edda; padding: 20px; border-radius: 10px;">
                                <p style="margin: 0; color: #155724;">تم إنهاء الاستاند وتأكيد جميع المعالجات.</p>
                                <p style="margin: 10px 0 0; color: #155724;">يمكنك الآن مسح استاند جديد.</p>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'رائع!',
                    confirmButtonColor: '#28a745',
                    width: '500px'
                });
            }
            
            // إعادة تحميل الصفحة مثل المرحلة الأولى
            window.location.reload();
        } else {
            throw new Error(finishData.message || 'فشل إنهاء الاستاند');
        }
        
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '❌ خطأ',
            text: error.message,
            confirmButtonText: 'حسناً'
        });
    }
}

// نقل استاند من لوحة المعلقة
async function showTransferStandModalForPending(barcode) {
    if (!barcode) return;
    
    try {
        // جلب قائمة الموظفين
        const response = await fetch('/stage2/workers-for-transfer', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        const data = await response.json();

        if (!data.success || !data.workers || data.workers.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'لا يوجد موظفين متاحين',
                text: 'لا يوجد موظفين آخرين متاحين للنقل',
                confirmButtonText: 'حسناً'
            });
            return;
        }

        const { value: formValues } = await Swal.fire({
            title: 'نقل الاستاند لموظف آخر',
            width: '500px',
            html: `
                <div style="text-align:right; direction:rtl;">
                    <div style="background:linear-gradient(135deg, #8e44ad 0%, #c084fc 100%); color:white; padding:15px; border-radius:10px; margin-bottom:20px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <div style="font-size:12px; opacity:0.9;">باركود الاستاند</div>
                                <div style="font-size:16px; font-weight:bold; font-family:monospace;">${barcode}</div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:#333;">
                            <i class="fas fa-user" style="color:#8e44ad;"></i> الموظف الجديد <span style="color:#e74c3c;">*</span>
                        </label>
                        <select id="swal-new-worker" style="width:100%; padding:12px; border-radius:8px; border:2px solid #e0e0e0; font-size:14px;">
                            <option value="">-- اختر الموظف --</option>
                            ${data.workers.map(w => `<option value="${w.id}">${w.name}</option>`).join('')}
                        </select>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:#333;">
                            <i class="fas fa-question-circle" style="color:#8e44ad;"></i> سبب النقل
                        </label>
                        <select id="swal-reason" style="width:100%; padding:12px; border-radius:8px; border:2px solid #e0e0e0; font-size:14px;">
                            <option value="نهاية الوردية">نهاية الوردية</option>
                            <option value="إجازة">إجازة</option>
                            <option value="ظرف طارئ">ظرف طارئ</option>
                            <option value="توزيع العمل">توزيع العمل</option>
                            <option value="آخر">سبب آخر</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:#333;">
                            <i class="fas fa-sticky-note" style="color:#8e44ad;"></i> ملاحظات (اختياري)
                        </label>
                        <textarea id="swal-notes" placeholder="أضف أي ملاحظات إضافية..." style="width:100%; padding:12px; border-radius:8px; border:2px solid #e0e0e0; font-size:14px; min-height:70px; resize:vertical;"></textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-share"></i> نقل الاستاند',
            cancelButtonText: '<i class="fas fa-times"></i> إلغاء',
            confirmButtonColor: '#8e44ad',
            cancelButtonColor: '#95a5a6',
            preConfirm: () => {
                const newWorkerId = document.getElementById('swal-new-worker').value;
                const reason = document.getElementById('swal-reason').value;
                const notes = document.getElementById('swal-notes').value;

                if (!newWorkerId) {
                    Swal.showValidationMessage('<i class="fas fa-exclamation-circle"></i> يجب اختيار الموظف الجديد');
                    return false;
                }

                return { newWorkerId, reason, notes };
            }
        });

        if (formValues) {
            await executeStandTransferForPending(barcode, formValues.newWorkerId, formValues.reason, formValues.notes);
        }

    } catch (error) {
        console.error('خطأ في عرض نافذة نقل الاستاند:', error);
        showToast('❌ حدث خطأ أثناء تحميل بيانات النقل', 'error');
    }
}

async function executeStandTransferForPending(barcode, newWorkerId, reason, notes) {
    try {
        Swal.fire({
            title: 'جاري نقل الاستاند...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const response = await fetch('/stage2/transfer-stand', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                barcode: barcode,
                new_worker_id: newWorkerId,
                reason: reason,
                notes: notes
            })
        });

        const data = await response.json();

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: '✅ تم نقل الاستاند بنجاح',
                html: `<div style="text-align:right; direction:rtl;">تم نقل الاستاند ${barcode} بنجاح</div>`,
                confirmButtonText: 'حسناً'
            });
            
            checkPendingItems();
        } else {
            throw new Error(data.message || 'فشل نقل الاستاند');
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: '❌ فشل النقل',
            text: error.message,
            confirmButtonText: 'حسناً'
        });
    }
}

// نقل الاستاند لموظف آخر
async function showTransferStandModal() {
    if (!currentStand) {
        alert('⚠️ لا يوجد استاند محمل!');
        return;
    }
    
    if (standRemainingWeight <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'لا يمكن النقل',
            text: 'لا يمكن نقل استاند تم استهلاكه بالكامل. يرجى إنهاء الاستاند بدلاً من ذلك.',
            confirmButtonText: 'فهمت'
        });
        return;
    }
    
    try {
        // جلب قائمة الموظفين
        const response = await fetch('/stage2/workers-for-transfer', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        const data = await response.json();

        if (!data.success || !data.workers || data.workers.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'لا يوجد موظفين متاحين',
                text: 'لا يوجد موظفين آخرين متاحين للنقل',
                confirmButtonText: 'حسناً'
            });
            return;
        }

        const { value: formValues } = await Swal.fire({
            title: 'نقل الاستاند لموظف آخر',
            width: '500px',
            html: `
                <div style="text-align:right; direction:rtl;">
                    <div style="background:linear-gradient(135deg, #0b5fa5 0%, #2a9fd6 100%); color:white; padding:15px; border-radius:10px; margin-bottom:20px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                            <div>
                                <div style="font-size:12px; opacity:0.9;">باركود الاستاند</div>
                                <div style="font-size:16px; font-weight:bold; font-family:monospace;">${currentStand.barcode}</div>
                            </div>
                            <div style="text-align:left;">
                                <div style="font-size:12px; opacity:0.9;">الوزن المتبقي</div>
                                <div style="font-size:16px; font-weight:bold;">${standRemainingWeight.toFixed(2)} كجم</div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:#333;">
                            <i class="fas fa-user" style="color:#0b5fa5;"></i> الموظف الجديد <span style="color:#e74c3c;">*</span>
                        </label>
                        <select id="swal-new-worker" style="width:100%; padding:12px; border-radius:8px; border:2px solid #e0e0e0; font-size:14px;">
                            <option value="">-- اختر الموظف --</option>
                            ${data.workers.map(w => `<option value="${w.id}">${w.name}</option>`).join('')}
                        </select>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:#333;">
                            <i class="fas fa-clipboard-list" style="color:#0b5fa5;"></i> سبب النقل
                        </label>
                        <select id="swal-reason" style="width:100%; padding:12px; border-radius:8px; border:2px solid #e0e0e0; font-size:14px;">
                            <option value="انتهاء الوردية">انتهاء الوردية</option>
                            <option value="انشغال الموظف">انشغال الموظف</option>
                            <option value="توزيع العمل">توزيع العمل</option>
                            <option value="آخر">سبب آخر</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:#333;">
                            <i class="fas fa-sticky-note" style="color:#0b5fa5;"></i> ملاحظات (اختياري)
                        </label>
                        <textarea id="swal-notes" placeholder="أضف أي ملاحظات إضافية..." style="width:100%; padding:12px; border-radius:8px; border:2px solid #e0e0e0; font-size:14px; min-height:70px; resize:vertical;"></textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-share"></i> نقل الاستاند',
            cancelButtonText: '<i class="fas fa-times"></i> إلغاء',
            confirmButtonColor: '#0b5fa5',
            cancelButtonColor: '#95a5a6',
            preConfirm: () => {
                const newWorkerId = document.getElementById('swal-new-worker').value;
                const reason = document.getElementById('swal-reason').value;
                const notes = document.getElementById('swal-notes').value;

                if (!newWorkerId) {
                    Swal.showValidationMessage('<i class="fas fa-exclamation-circle"></i> يجب اختيار الموظف الجديد');
                    return false;
                }

                return { newWorkerId, reason, notes };
            }
        });

        if (formValues) {
            await executeStandTransfer(currentStand.barcode, formValues.newWorkerId, formValues.reason, formValues.notes);
        }

    } catch (error) {
        console.error('خطأ في عرض نافذة نقل الاستاند:', error);
        showToast('❌ حدث خطأ أثناء تحميل بيانات النقل', 'error');
    }
}

async function executeStandTransfer(barcode, newWorkerId, reason, notes) {
    try {
        Swal.fire({
            title: 'جاري نقل الاستاند...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const response = await fetch('/stage2/transfer-stand', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                barcode: barcode,
                new_worker_id: newWorkerId,
                reason: reason,
                notes: notes
            })
        });

        const data = await response.json();

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: '✅ تم نقل الاستاند بنجاح',
                html: `
                    <div style="text-align:right; direction:rtl;">
                        <p>تم نقل الاستاند <strong>${barcode}</strong> إلى:</p>
                        <p style="font-size:18px; font-weight:bold; color:#27ae60;">${data.data.new_worker_name}</p>
                        <p style="color:#666; font-size:13px;">سيظهر الاستاند في لوحة تحكم الموظف الجديد</p>
                    </div>
                `,
                confirmButtonText: 'حسناً'
            });

            // مسح الاستاند الحالي
            currentStand = null;
            document.getElementById('standDisplay').classList.remove('active');
            checkPendingItems();
            document.getElementById('standBarcode').focus();
        } else {
            throw new Error(data.message || 'فشل نقل الاستاند');
        }
    } catch (error) {
        await Swal.fire({
            icon: 'error',
            title: '❌ فشل النقل',
            text: error.message,
            confirmButtonText: 'حسناً'
        });
    }
}

function finishOperation() {
    if (processedItems.length === 0) {
        alert('⚠️ لا توجد معالجات محفوظة!');
        return;
    }
    
    if (currentStand && hasCurrentStandProcessings()) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ لديك استاند معلق',
            html: `<div style="text-align:right; direction:rtl;">يجب إنهاء الاستاند الحالي (${currentStand.barcode}) قبل إنهاء العملية</div>`,
            confirmButtonText: 'فهمت'
        });
        return;
    }

    // جميع الاستاندات تم إنهاؤها، انتقل للصفحة الرئيسية
    localStorage.removeItem('stage2_processings');
    showToast('✅ تم إنهاء العملية بنجاح!', 'success');
    setTimeout(() => {
        window.location.href = '{{ route("manufacturing.stage2.index") }}';
    }, 1000);
}

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

// مسح الذاكرة المحلية
function clearLocalStorage() {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: 'سيتم مسح جميع المعالجات المحفوظة محلياً وإعادة المزامنة مع السيرفر',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#8e44ad',
        cancelButtonColor: '#95a5a6',
        confirmButtonText: 'نعم، امسح الذاكرة',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            // مسح localStorage
            localStorage.removeItem('stage2_processings');
            
            // مسح sessionStorage
            sessionStorage.clear();
            
            // مسح المتغيرات
            processedItems = [];
            pendingItems = [];
            pendingItemsCount = 0;
            currentStand = null;
            
            // تحديث العرض
            renderProcessed();
            checkPendingItems();
            
            // إخفاء معلومات الاستاند
            document.getElementById('standInfo').style.display = 'none';
            
            Swal.fire({
                title: 'تم المسح',
                text: 'تم مسح جميع البيانات المحلية وإعادة المزامنة',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload(); // إعادة تحميل الصفحة
            });
        }
    });
}

// ===== دوال طلبات النقل =====
let pendingTransfers = [];

async function checkPendingTransfers() {
    try {
        const response = await fetch('/stage2/pending-transfers', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        const data = await response.json();
        
        if (data.success) {
            pendingTransfers = data.transfers || [];
            renderPendingTransfersPanel();
        }
    } catch (error) {
        console.error('خطأ في جلب طلبات النقل:', error);
    }
}

function renderPendingTransfersPanel() {
    const panel = document.getElementById('pendingTransfersPanel');
    const list = document.getElementById('pendingTransfersList');
    
    if (!panel || !list) return;
    
    if (pendingTransfers.length === 0) {
        panel.style.display = 'none';
        return;
    }
    
    panel.style.display = 'block';
    
    list.innerHTML = pendingTransfers.map(transfer => {
        const remainingWeight = parseFloat(transfer.remaining_weight || 0);
        const createdAt = new Date(transfer.created_at).toLocaleString('ar-EG');
        
        return `
            <div class="pending-stand-card" style="border-right: 4px solid #27ae60;">
                <div class="pending-stand-info">
                    <strong style="color:#27ae60;"><i class="fas fa-exchange-alt"></i> استاند منقول: ${transfer.barcode}</strong>
                    <span>المادة: ${transfer.material_name || '-'}</span>
                    <span>من: <strong>${transfer.sender_name}</strong></span>
                    <span>الوزن المتبقي: <strong style="color:#27ae60;">${remainingWeight.toFixed(2)} كجم</strong></span>
                    ${transfer.reason ? `<span>السبب: ${transfer.reason}</span>` : ''}
                    <span style="font-size:12px; color:#999;">تاريخ النقل: ${createdAt}</span>
                </div>
                <div class="pending-stand-actions">
                    <button class="btn-continue" type="button" onclick="acceptStandTransfer('${transfer.barcode}')" style="background:#27ae60;">
                        <i class="fas fa-check"></i> قبول
                    </button>
                    <button class="btn-finish-stand" type="button" onclick="rejectStandTransfer('${transfer.barcode}')" style="background:#e74c3c; color:#fff;">
                        <i class="fas fa-times"></i> رفض
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

async function acceptStandTransfer(barcode) {
    const result = await Swal.fire({
        title: 'قبول نقل الاستاند',
        html: `<div style="text-align:right; direction:rtl;">هل تريد قبول نقل الاستاند <strong>${barcode}</strong>؟<br>سيظهر في قائمة الاستاندات المعلقة ويمكنك العمل عليه.</div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> قبول',
        cancelButtonText: '<i class="fas fa-times"></i> إلغاء',
        confirmButtonColor: '#27ae60'
    });
    
    if (!result.isConfirmed) return;
    
    try {
        Swal.fire({ title: 'جاري قبول النقل...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        const response = await fetch('/stage2/accept-transfer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ barcode })
        });
        
        const data = await response.json();
        
        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: '✅ تم قبول النقل',
                text: 'الاستاند متاح الآن للعمل عليه',
                confirmButtonText: 'حسناً'
            });
            checkPendingTransfers();
            checkPendingItems();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'خطأ', text: error.message });
    }
}

async function rejectStandTransfer(barcode) {
    const { value: reason } = await Swal.fire({
        title: 'رفض نقل الاستاند',
        html: `<div style="text-align:right; direction:rtl;">هل تريد رفض نقل الاستاند <strong>${barcode}</strong>؟</div>`,
        input: 'textarea',
        inputLabel: 'سبب الرفض (اختياري)',
        inputPlaceholder: 'أدخل سبب الرفض...',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-times"></i> رفض النقل',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#e74c3c'
    });
    
    if (reason === undefined) return; // المستخدم ضغط إلغاء
    
    try {
        Swal.fire({ title: 'جاري رفض النقل...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        const response = await fetch('/stage2/reject-transfer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ barcode, reason: reason || '' })
        });
        
        const data = await response.json();
        
        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'تم رفض النقل',
                text: 'تم إبلاغ الموظف الناقل بالرفض',
                confirmButtonText: 'حسناً'
            });
            checkPendingTransfers();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'خطأ', text: error.message });
    }
}
</script>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

@endsection
