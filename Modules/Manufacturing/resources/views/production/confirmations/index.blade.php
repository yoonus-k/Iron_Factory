@extends('master')

@section('title', __('app.production_confirmations.title'))

@section('content')
<div class="container-fluid" style="padding: 20px; direction: rtl; font-family: 'Cairo', sans-serif;">

    <!-- العنوان الرئيسي -->
    <div style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(44, 62, 80, 0.3);">
        <h1 style="color: white; margin: 0; font-size: 32px; font-weight: bold;">
            📊 {{ __('app.production_confirmations.title') }}
        </h1>
        <p style="color: rgba(255, 255, 255, 0.9); margin: 10px 0 0 0; font-size: 16px;">
            {{ __('app.production_confirmations.subtitle') }}
        </p>
    </div>

    <!-- الإحصائيات السريعة -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- معلق -->
        <div style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); padding: 25px; border-radius: 15px; color: white; box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">{{ __('app.production_confirmations.stats.pending') }}</div>
                    <div style="font-size: 32px; font-weight: bold;">{{ $stats['pending'] }}</div>
                </div>
                <div style="font-size: 48px; opacity: 0.3;">⏳</div>
            </div>
        </div>

        <!-- مؤكد -->
        <div style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); padding: 25px; border-radius: 15px; color: white; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">{{ __('app.production_confirmations.stats.confirmed') }}</div>
                    <div style="font-size: 32px; font-weight: bold;">{{ $stats['confirmed'] }}</div>
                </div>
                <div style="font-size: 48px; opacity: 0.3;">✓</div>
            </div>
        </div>

        <!-- مرفوض -->
        <div style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); padding: 25px; border-radius: 15px; color: white; box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">{{ __('app.production_confirmations.stats.rejected') }}</div>
                    <div style="font-size: 32px; font-weight: bold;">{{ $stats['rejected'] }}</div>
                </div>
                <div style="font-size: 48px; opacity: 0.3;">✕</div>
            </div>
        </div>

        <!-- الإجمالي -->
        <div style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); padding: 25px; border-radius: 15px; color: white; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">{{ __('app.production_confirmations.stats.total') }}</div>
                    <div style="font-size: 32px; font-weight: bold;">{{ $stats['total'] }}</div>
                </div>
                <div style="font-size: 48px; opacity: 0.3;">📦</div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div style="background: white; padding: 25px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h3 style="color: #2c3e50; margin-bottom: 20px; font-size: 20px; font-weight: bold;">🔍 {{ __('app.production_confirmations.search_and_filter') }}</h3>

        <form method="GET" action="{{ route('manufacturing.production.confirmations.index') }}">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">

                <!-- الحالة -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">{{ __('app.production_confirmations.filter_status') }}</label>
                    <select name="status" style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px; cursor: pointer;">
                        <option value="">{{ __('app.common.all') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ {{ __('app.production_confirmations.pending') }}</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>✓ {{ __('app.production_confirmations.confirmed') }}</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>✕ {{ __('app.production_confirmations.rejected') }}</option>
                    </select>
                </div>

                <!-- المرحلة -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">{{ __('app.production_confirmations.filter_stage') }}</label>
                    <select name="stage" style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px; cursor: pointer;">
                        <option value="">{{ __('app.common.all') }}</option>
                        @foreach(\App\Models\ProductionStage::getActiveStages() as $stage)
                            <option value="{{ $stage->stage_code }}" {{ request('stage') == $stage->stage_code ? 'selected' : '' }}>
                                {{ $stage->stage_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- الموظف -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">{{ __('app.production_confirmations.filter_worker') }}</label>
                    <select name="worker" style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px; cursor: pointer;">
                        <option value="">{{ __('app.common.all') }}</option>
                        @foreach(\App\Models\User::where('is_active', 1)->orderBy('name')->get() as $worker)
                            <option value="{{ $worker->id }}" {{ request('worker') == $worker->id ? 'selected' : '' }}>
                                {{ $worker->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- من تاريخ -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">{{ __('app.production_confirmations.filter_from_date') }}</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}"
                           style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px;">
                </div>

                <!-- إلى تاريخ -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">{{ __('app.production_confirmations.filter_to_date') }}</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}"
                           style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px;">
                </div>

            </div>

            <!-- أزرار -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px;">
                <button type="submit"
                        style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; border: none; padding: 14px 24px; border-radius: 8px; font-weight: bold; font-size: 15px; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3); display: flex; align-items: center; justify-content: center; gap: 8px;"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 15px rgba(52, 152, 219, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 10px rgba(52, 152, 219, 0.3)'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    {{ __('app.production_confirmations.search_btn') }}
                </button>
                <a href="{{ route('manufacturing.production.confirmations.index') }}"
                   style="background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%); color: white; border: none; padding: 14px 24px; border-radius: 8px; font-weight: bold; font-size: 15px; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 10px rgba(149, 165, 166, 0.3); display: flex; align-items: center; justify-content: center; gap: 8px;"
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 15px rgba(149, 165, 166, 0.4)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 10px rgba(149, 165, 166, 0.3)'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                        <path d="M3 3v5h5"></path>
                    </svg>
                    {{ __('app.production_confirmations.reset_btn') }}
                </a>
            </div>
            </div>
        </form>
    </div>

    <!-- جدول التأكيدات -->
    @if($confirmations->isEmpty())
        <div style="background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 15px; padding: 60px; text-align: center;">
            <div style="font-size: 80px; margin-bottom: 20px; opacity: 0.3;">📭</div>
            <h3 style="color: #6c757d; margin-bottom: 15px;">{{ __('app.production_confirmations.no_results') }}</h3>
            <p style="color: #adb5bd; font-size: 16px;">{{ __('app.production_confirmations.no_results_desc') }}</p>
        </div>
    @else
        <div style="background: white; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
            <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%); color: white;">
                            <th style="padding: 15px 10px; text-align: center; font-weight: bold; font-size: 14px; border-left: 1px solid rgba(255,255,255,0.1); white-space: nowrap;">{{ __('app.production_confirmations.table.number') }}</th>
                            <th style="padding: 15px 10px; text-align: center; font-weight: bold; font-size: 14px; border-left: 1px solid rgba(255,255,255,0.1); white-space: nowrap;">{{ __('app.production_confirmations.table.batch_code') }}</th>
                        <th style="padding: 15px 10px; text-align: center; font-weight: bold; font-size: 14px; border-left: 1px solid rgba(255,255,255,0.1); white-space: nowrap;">{{ __('app.production_confirmations.table.material') }}</th>
                        <th style="padding: 15px 10px; text-align: center; font-weight: bold; font-size: 14px; border-left: 1px solid rgba(255,255,255,0.1); white-space: nowrap;">{{ __('app.production_confirmations.table.quantity') }}</th>
                        <th style="padding: 15px 10px; text-align: center; font-weight: bold; font-size: 14px; border-left: 1px solid rgba(255,255,255,0.1); white-space: nowrap;">{{ __('app.production_confirmations.table.stage') }}</th>
                        <th style="padding: 15px 10px; text-align: center; font-weight: bold; font-size: 14px; border-left: 1px solid rgba(255,255,255,0.1); white-space: nowrap;">{{ __('app.production_confirmations.table.worker') }}</th>
                        <th style="padding: 15px 10px; text-align: center; font-weight: bold; font-size: 14px; border-left: 1px solid rgba(255,255,255,0.1); white-space: nowrap;">{{ __('app.production_confirmations.table.status') }}</th>
                        <th style="padding: 15px 10px; text-align: center; font-weight: bold; font-size: 14px; border-left: 1px solid rgba(255,255,255,0.1); white-space: nowrap;">{{ __('app.production_confirmations.table.date') }}</th>
                        <th style="padding: 15px 10px; text-align: center; font-weight: bold; font-size: 14px; white-space: nowrap;">{{ __('app.production_confirmations.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($confirmations as $index => $confirmation)
                        <tr style="border-bottom: 1px solid #ecf0f1; transition: background 0.3s;"
                            onmouseover="this.style.background='#f8f9fa'"
                            onmouseout="this.style.background='white'">

                            <td style="padding: 12px 8px; text-align: center; font-weight: bold; color: #7f8c8d; white-space: nowrap;">
                                {{ $confirmations->firstItem() + $index }}
                            </td>

                            <td style="padding: 12px 8px; text-align: center;">
                                <span style="background: #9b59b6; color: white; padding: 6px 10px; border-radius: 6px; font-weight: bold; font-size: 12px; display: inline-block; white-space: nowrap;">
                                    {{ $confirmation->barcode ?? $confirmation->batch?->production_barcode ?? $confirmation->batch?->batch_code ?? $confirmation->metadata['barcode'] ?? __('app.production_confirmations.table.not_specified') }}
                                </span>
                            </td>

                            <td style="padding: 12px 8px; text-align: center;">
                                <div style="font-weight: bold; color: #2c3e50; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                                    {{ $confirmation->metadata['stage_name'] ?? $confirmation->deliveryNote?->material?->name_ar ?? $confirmation->batch?->material?->name ?? __('app.production_confirmations.table.not_specified') }}
                                </div>
                            </td>

                            <td style="padding: 12px 8px; text-align: center; white-space: nowrap;">
                                @php
                                    $weight = $confirmation->actual_received_quantity 
                                        ?? $confirmation->batch?->initial_quantity 
                                        ?? $confirmation->deliveryNote?->quantity 
                                        ?? ($confirmation->metadata['weight'] ?? null);
                                @endphp
                                @if($weight)
                                    <span style="font-size: 14px; font-weight: bold; color: #27ae60;">
                                        {{ number_format($weight, 2) }}
                                    </span>
                                    <span style="color: #7f8c8d; font-size: 11px;">{{ __('app.units.kg') }}</span>
                                @else
                                    <span style="color: #e74c3c; font-size: 12px;">{{ __('app.production_confirmations.table.data_unavailable') }}</span>
                                @endif
                            </td>

                            <td style="padding: 12px 8px; text-align: center;">
                                @php
                                    $stageMapping = [
                                        'stage1_stands' => 'المرحلة الأولى - الاستاند',
                                        'stage2_processed' => 'المرحلة الثانية - المعالج',
                                        'stage3_coils' => 'المرحلة الثالثة - اللفائف',
                                        'stage4_boxes' => 'المرحلة الرابعة - الصناديق',
                                        'warehouse' => 'المستودع',
                                    ];
                                    $stageName = $confirmation->metadata['stage_name'] 
                                        ?? ($stageMapping[$confirmation->stage_type] ?? null)
                                        ?? $confirmation->deliveryNote?->production_stage_name 
                                        ?? __('app.production_confirmations.table.not_specified');
                                @endphp
                                <div style="display: inline-block; background: #3498db; color: white; padding: 6px 10px; border-radius: 6px; font-weight: 600; font-size: 11px; white-space: nowrap; max-width: 160px; overflow: hidden; text-overflow: ellipsis;" title="{{ $stageName }}">
                                    {{ $stageName }}
                                </div>
                            </td>

                            <td style="padding: 12px 8px; text-align: center; color: #2c3e50; font-weight: 600; font-size: 13px; white-space: nowrap;">
                                {{ $confirmation->assignedUser?->name ?? __('app.production_confirmations.table.not_specified') }}
                            </td>

                            <td style="padding: 12px 8px; text-align: center;">
                                @if($confirmation->status == 'pending')
                                    <div style="display: inline-flex; align-items: center; gap: 4px; background: #f39c12; color: white; padding: 6px 10px; border-radius: 6px; font-weight: 600; font-size: 11px; white-space: nowrap;">
                                        <span>⏳</span>
                                        <span>{{ __('app.production_confirmations.pending') }}</span>
                                    </div>
                                @elseif($confirmation->status == 'confirmed')
                                    <div style="display: inline-flex; align-items: center; gap: 4px; background: #27ae60; color: white; padding: 6px 10px; border-radius: 6px; font-weight: 600; font-size: 11px; white-space: nowrap;">
                                        <span>✓</span>
                                        <span>{{ __('app.production_confirmations.confirmed') }}</span>
                                    </div>
                                @else
                                    <div style="display: inline-flex; align-items: center; gap: 4px; background: #e74c3c; color: white; padding: 6px 10px; border-radius: 6px; font-weight: 600; font-size: 11px; white-space: nowrap;">
                                        <span>✕</span>
                                        <span>{{ __('app.production_confirmations.rejected') }}</span>
                                    </div>
                                @endif
                            </td>

                            <td style="padding: 12px 8px; text-align: center; color: #7f8c8d; font-size: 12px; white-space: nowrap;">
                                {{ $confirmation->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td style="padding: 12px 8px; text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; align-items: center;">
                                    <a href="{{ route('manufacturing.production.confirmations.show', $confirmation->id) }}"
                                       style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 11px; transition: all 0.3s; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 2px 6px rgba(52, 152, 219, 0.3); white-space: nowrap;"
                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 10px rgba(52, 152, 219, 0.4)'"
                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(52, 152, 219, 0.3)'">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <span>عرض</span>
                                    </a>
                                    
                                    @if($confirmation->status == 'pending')
                                    <button type="button" 
                                            onclick="openTransferModal({{ $confirmation->id }}, '{{ $confirmation->assignedUser?->name ?? '' }}', {{ $confirmation->assigned_user_id ?? 'null' }})"
                                            style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white; padding: 6px 12px; border: none; border-radius: 6px; font-weight: 600; font-size: 11px; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 2px 6px rgba(155, 89, 182, 0.3); white-space: nowrap;"
                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 10px rgba(155, 89, 182, 0.4)'"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(155, 89, 182, 0.3)'">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <line x1="19" y1="8" x2="24" y2="13"></line>
                                            <line x1="24" y1="8" x2="19" y2="13"></line>
                                        </svg>
                                        <span>نقل</span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>

        <!-- Pagination -->
        <div style="margin-top: 30px; display: flex; justify-content: center;">
            {{ $confirmations->appends(request()->query())->links() }}
        </div>
    @endif

</div>

<!-- Modals -->
<div class="modal fade" id="quickConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="direction: rtl;">
            <div class="modal-header" style="background: #27ae60; color: white;">
                <h5 class="modal-title">✓ {{ __('app.production_confirmations.confirm_receipt') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="confirm-id">
                <div class="mb-3">
                    <label class="form-label">{{ __('app.production_confirmations.notes_optional') }}</label>
                    <textarea id="confirm-notes" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.buttons.cancel') }}</button>
                <button type="button" class="btn btn-success" onclick="submitConfirm()">✓ {{ __('app.buttons.confirm') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quickRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="direction: rtl;">
            <div class="modal-header" style="background: #e74c3c; color: white;">
                <h5 class="modal-title">✕ {{ __('app.production_confirmations.reject_receipt') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reject-id">
                <div class="mb-3">
                    <label class="form-label">{{ __('app.production_confirmations.rejection_reason') }} <span class="text-danger">*</span></label>
                    <textarea id="reject-reason" class="form-control" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.buttons.cancel') }}</button>
                <button type="button" class="btn btn-danger" onclick="submitReject()">✕ {{ __('app.buttons.reject') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Worker Modal -->
<div class="modal fade" id="transferWorkerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="direction: rtl;">
            <div class="modal-header" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white;">
                <h5 class="modal-title" style="display: flex; align-items: center; gap: 10px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="24" y2="13"></line>
                        <line x1="24" y1="8" x2="19" y2="13"></line>
                    </svg>
                    نقل التأكيد إلى موظف آخر
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 25px;">
                <input type="hidden" id="transfer-confirmation-id">
                
                <div class="mb-4" style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-right: 4px solid #9b59b6;">
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px;">
                        الموظف الحالي:
                    </label>
                    <div id="current-worker-name" style="font-size: 16px; color: #7f8c8d; font-weight: 600;">
                        غير محدد
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label" style="font-weight: bold; color: #2c3e50; display: flex; align-items: center; gap: 6px;">
                        <span style="color: #e74c3c;">*</span>
                        الموظف الجديد:
                    </label>
                    <select id="transfer-worker-id" class="form-control" style="padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px;" required>
                        <option value="">-- اختر الموظف --</option>
                        @foreach(\App\Models\User::where('is_active', 1)->orderBy('name')->get() as $worker)
                            <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label" style="font-weight: bold; color: #2c3e50;">
                        ملاحظات (اختياري):
                    </label>
                    <textarea id="transfer-notes" class="form-control" rows="3" style="padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px;" placeholder="أدخل سبب النقل أو أي ملاحظات..."></textarea>
                </div>
            </div>
            <div class="modal-footer" style="padding: 20px; background: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 10px 20px; border-radius: 8px;">
                    إلغاء
                </button>
                <button type="button" class="btn" onclick="submitTransfer()" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: 6px;">
                        <polyline points="9 11 12 14 22 4"></polyline>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                    تأكيد النقل
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let confirmModal, rejectModal;

document.addEventListener('DOMContentLoaded', function() {
    confirmModal = new bootstrap.Modal(document.getElementById('quickConfirmModal'));
    rejectModal = new bootstrap.Modal(document.getElementById('quickRejectModal'));
});

function quickConfirm(id) {
    document.getElementById('confirm-id').value = id;
    document.getElementById('confirm-notes').value = '';
    confirmModal.show();
}

function quickReject(id) {
    document.getElementById('reject-id').value = id;
    document.getElementById('reject-reason').value = '';
    rejectModal.show();
}

function submitConfirm() {
    const id = document.getElementById('confirm-id').value;
    const notes = document.getElementById('confirm-notes').value;

    fetch(`{{ url('manufacturing/production/confirmations') }}/${id}/confirm`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ notes: notes })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            confirmModal.hide();
            Swal.fire({
                icon: 'success',
                title: '{{ __('app.production_confirmations.confirmation_success') }}',
                text: data.message,
                confirmButtonText: '{{ __('app.buttons.ok') }}'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: '{{ __('app.production_confirmations.error') }}',
                text: data.message,
                confirmButtonText: '{{ __('app.buttons.ok') }}'
            });
        }
    })
    .catch(error => {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: '{{ __('app.production_confirmations.error') }}',
            text: '{{ __('app.production_confirmations.confirmation_error') }}',
            confirmButtonText: '{{ __('app.buttons.ok') }}'
        });
    });
}

function submitReject() {
    const id = document.getElementById('reject-id').value;
    const reason = document.getElementById('reject-reason').value;

    if (!reason.trim()) {
        Swal.fire({
            icon: 'warning',
            title: '{{ __('app.production_confirmations.warning') }}',
            text: '{{ __('app.production_confirmations.please_enter_rejection_reason') }}',
            confirmButtonText: '{{ __('app.buttons.ok') }}'
        });
        return;
    }

    fetch(`{{ url('manufacturing/production/confirmations') }}/${id}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ rejection_reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            rejectModal.hide();
            Swal.fire({
                icon: 'success',
                title: '{{ __('app.production_confirmations.rejection_success') }}',
                text: data.message,
                confirmButtonText: '{{ __('app.buttons.ok') }}'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: '{{ __('app.production_confirmations.error') }}',
                text: data.message,
                confirmButtonText: '{{ __('app.buttons.ok') }}'
            });
        }
    })
    .catch(error => {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: '{{ __('app.production_confirmations.error') }}',
            text: '{{ __('app.production_confirmations.rejection_error') }}',
            confirmButtonText: '{{ __('app.buttons.ok') }}'
        });
    });
}

// Transfer Worker Modal
let transferModal;
document.addEventListener('DOMContentLoaded', function() {
    transferModal = new bootstrap.Modal(document.getElementById('transferWorkerModal'));
});

function openTransferModal(confirmationId, currentWorkerName, currentWorkerId) {
    document.getElementById('transfer-confirmation-id').value = confirmationId;
    document.getElementById('current-worker-name').textContent = currentWorkerName || 'غير محدد';
    
    // Reset and populate worker select
    const workerSelect = document.getElementById('transfer-worker-id');
    workerSelect.value = '';
    
    // Highlight current worker if exists
    if(currentWorkerId) {
        Array.from(workerSelect.options).forEach(option => {
            if(option.value == currentWorkerId) {
                option.style.background = '#fff3cd';
                option.textContent = option.textContent.replace(' (الحالي)', '') + ' (الحالي)';
            }
        });
    }
    
    transferModal.show();
}

function submitTransfer() {
    const confirmationId = document.getElementById('transfer-confirmation-id').value;
    const newWorkerId = document.getElementById('transfer-worker-id').value;
    const notes = document.getElementById('transfer-notes').value;

    if (!newWorkerId) {
        Swal.fire({
            icon: 'warning',
            title: 'تنبيه',
            text: 'الرجاء اختيار الموظف الجديد',
            confirmButtonText: 'حسناً'
        });
        return;
    }

    fetch(`{{ url('manufacturing/production/confirmations') }}/${confirmationId}/transfer`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            new_worker_id: newWorkerId,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            transferModal.hide();
            Swal.fire({
                icon: 'success',
                title: 'نجح!',
                text: data.message || 'تم نقل التأكيد إلى الموظف الجديد بنجاح',
                confirmButtonText: 'حسناً'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: data.message || 'حدث خطأ أثناء نقل التأكيد',
                confirmButtonText: 'حسناً'
            });
        }
    })
    .catch(error => {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'حدث خطأ في الاتصال بالخادم',
            confirmButtonText: 'حسناً'
        });
    });
}
</script>
@endsection