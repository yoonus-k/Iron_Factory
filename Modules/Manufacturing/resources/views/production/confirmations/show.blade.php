@extends('master')

@section('title', __('app.production_confirmations.show_title'))

@section('content')
<div class="container-fluid" style="padding: 20px; direction: rtl; font-family: 'Cairo', sans-serif;">

    <!-- العنوان الرئيسي -->
    <div style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="color: white; margin: 0 0 10px 0; font-size: 32px; font-weight: bold;">
                    📋 {{ __('app.production_confirmations.show_title') }}
                </h1>
                <p style="color: rgba(255, 255, 255, 0.9); margin: 0; font-size: 16px;">
                    {{ __('app.production_confirmations.show_subtitle') }}
                </p>
            </div>

            <!-- الحالة الكبيرة -->
            <div>
                @if($confirmation->status == 'pending')
                    <div style="background: #f39c12; color: white; padding: 15px 30px; border-radius: 12px; font-weight: bold; font-size: 20px; box-shadow: 0 4px 15px rgba(243, 156, 18, 0.4);">
                        ⏳ {{ __('app.production_confirmations.pending') }}
                    </div>
                @elseif($confirmation->status == 'confirmed')
                    <div style="background: #27ae60; color: white; padding: 15px 30px; border-radius: 12px; font-weight: bold; font-size: 20px; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);">
                        ✓ {{ __('app.production_confirmations.confirmed') }}
                    </div>
                @else
                    <div style="background: #e74c3c; color: white; padding: 15px 30px; border-radius: 12px; font-weight: bold; font-size: 20px; box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);">
                        ✕ {{ __('app.production_confirmations.rejected') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- زر العودة والأزرار -->
    <div style="margin-bottom: 20px; display: flex; gap: 10px; align-items: center;">
        <a href="{{ route('manufacturing.production.confirmations.index') }}"
           style="background: #95a5a6; color: white; text-decoration: none; padding: 12px 25px; border-radius: 10px; font-weight: bold; display: inline-block; transition: all 0.3s;"
           onmouseover="this.style.background='#7f8c8d'"
           onmouseout="this.style.background='#95a5a6'">
            {{ __('app.production_confirmations.back_arrow') }}
        </a>

        @if($confirmation->status == 'pending' && $confirmation->assigned_to == auth()->id())
            <form action="{{ route('manufacturing.production.confirmations.confirm', $confirmation->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('{{ __('app.production_confirmations.confirm_sure') }}')">
                @csrf
                <button type="submit" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); color: white; border: none; padding: 12px 25px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: all 0.3s;"
                        onmouseover="this.style.background='linear-gradient(135deg, #1e7e34 0%, #155724 100%)'"
                        onmouseout="this.style.background='linear-gradient(135deg, #28a745 0%, #1e7e34 100%)'">
                    ✓ {{ __('app.production_confirmations.confirm_receipt') }}
                </button>
            </form>

            <button type="button" data-bs-toggle="modal" data-bs-target="#rejectModal"
                    style="background: linear-gradient(135deg, #dc3545 0%, #bd2130 100%); color: white; border: none; padding: 12px 25px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: all 0.3s;"
                    onmouseover="this.style.background='linear-gradient(135deg, #bd2130 0%, #a71d2a 100%)'"
                    onmouseout="this.style.background='linear-gradient(135deg, #dc3545 0%, #bd2130 100%)'">
                ✕ {{ __('app.production_confirmations.reject_receipt') }}
            </button>
        @endif
    </div>

    <!-- Modal للرفض -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="direction: rtl;">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('app.production_confirmations.reject_modal_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('manufacturing.production.confirmations.reject', $confirmation->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('app.production_confirmations.rejection_reason') }} <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="{{ __('app.production_confirmations.rejection_reason_desc') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.production_confirmations.cancel_btn') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('app.production_confirmations.reject_btn') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">

        <!-- العمود الأيسر - التفاصيل الرئيسية -->
        <div>

            <!-- معلومات الدفعة -->
            <div style="background: white; border-radius: 15px; padding: 30px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="color: #2c3e50; margin-bottom: 25px; font-size: 22px; font-weight: bold; border-bottom: 3px solid #3498db; padding-bottom: 15px;">
                    📦 {{ __('app.production_confirmations.batch_info') }}
                </h3>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-right: 4px solid #9b59b6;">
                        <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">{{ __('app.production_confirmations.batch_code_label') }}</div>
                        <div style="font-weight: bold; color: #2c3e50; font-size: 18px;">
                            {{ $confirmation->batch?->batch_code ?? __('app.production_confirmations.table.not_specified') }}
                        </div>
                    </div>

                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-right: 4px solid #3498db;">
                        <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">{{ __('app.production_confirmations.material_label') }}</div>
                        <div style="font-weight: bold; color: #2c3e50; font-size: 18px;">
                            {{ $confirmation->batch?->material?->name ?? __('app.production_confirmations.table.not_specified') }}
                        </div>
                        <div style="color: #95a5a6; font-size: 13px; margin-top: 3px;">
                            @if($confirmation->batch?->material?->weight)
                                {{ __('app.production_confirmations.material_label') }}: {{ $confirmation->batch->material->weight }} {{ __('app.units.kg') }}
                            @endif
                        </div>
                    </div>

                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-right: 4px solid #27ae60;">
                        <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">{{ __('app.production_confirmations.final_weight') }}</div>
                        <div style="font-weight: bold; color: #27ae60; font-size: 20px;">
                            @if($confirmation->actual_received_quantity)
                                {{ number_format($confirmation->actual_received_quantity, 2) }}
                            @elseif($confirmation->batch?->initial_quantity)
                                {{ number_format($confirmation->batch->initial_quantity, 2) }}
                            @elseif($confirmation->deliveryNote?->quantity)
                                {{ number_format($confirmation->deliveryNote->quantity, 2) }}
                            @else
                                <span style="color: #e74c3c;">{{ __('app.production_confirmations.table.data_unavailable') }}</span>
                            @endif
                        </div>
                        <div style="color: #95a5a6; font-size: 13px; margin-top: 3px;">{{ __('app.units.kg') }}</div>
                    </div>

                    @if($confirmation->actual_received_quantity)
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-right: 4px solid #16a085;">
                        <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">{{ __('app.production_confirmations.actual_received') }}</div>
                        <div style="font-weight: bold; color: #16a085; font-size: 20px;">
                            {{ number_format($confirmation->actual_received_quantity, 2) }}
                        </div>
                        <div style="color: #95a5a6; font-size: 13px; margin-top: 3px;">{{ __('app.units.kg') }}</div>
                    </div>
                    @endif

                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-right: 4px solid #e67e22;">
                        <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">{{ __('app.production_confirmations.production_stage') }}</div>
                        <div style="font-weight: bold; color: #2c3e50; font-size: 18px;">{{ $confirmation->deliveryNote?->production_stage_name ?? __('app.production_confirmations.table.not_specified') }}</div>
                    </div>

                    @if($confirmation->batch?->coil_number)
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-right: 4px solid #8e44ad;">
                        <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">{{ __('app.production_confirmations.coil_number') }}</div>
                        <div style="font-weight: bold; color: #2c3e50; font-size: 18px;">{{ $confirmation->batch->coil_number }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- الباركود -->
            @if($confirmation->batch?->production_barcode)
            <div style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); border-radius: 15px; padding: 30px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3); text-align: center;">
                <h3 style="color: white; margin-bottom: 20px; font-size: 22px; font-weight: bold;">
                    🏷️ {{ __('app.production_confirmations.production_barcode') }}
                </h3>
                <div style="background: white; padding: 25px; border-radius: 10px; display: inline-block;">
                    {!! DNS1D::getBarcodeHTML($confirmation->batch->production_barcode, 'C128', 2, 80) !!}
                    <div style="margin-top: 15px; font-weight: bold; color: #2c3e50; font-size: 18px;">
                        {{ $confirmation->batch->production_barcode }}
                    </div>
                </div>
            </div>
            @endif

            <!-- الملاحظات -->
            @if($confirmation->deliveryNote?->notes || $confirmation->notes)
            <div style="background: white; border-radius: 15px; padding: 30px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="color: #2c3e50; margin-bottom: 20px; font-size: 22px; font-weight: bold; border-bottom: 3px solid #f39c12; padding-bottom: 15px;">
                    📝 {{ __('app.production_confirmations.notes') }}
                </h3>

                @if($confirmation->deliveryNote?->notes)
                <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 10px; padding: 20px; margin-bottom: 15px;">
                    <div style="font-weight: bold; color: #856404; margin-bottom: 10px; font-size: 15px;">{{ __('app.production_confirmations.notes_on_transfer') }}:</div>
                    <div style="color: #856404; line-height: 1.8; font-size: 15px;">{{ $confirmation->deliveryNote->notes }}</div>
                </div>
                @endif

                @if($confirmation->notes)
                <div style="background: #d1ecf1; border: 2px solid #17a2b8; border-radius: 10px; padding: 20px;">
                    <div style="font-weight: bold; color: #0c5460; margin-bottom: 10px; font-size: 15px;">{{ __('app.production_confirmations.notes_on_confirm') }}:</div>
                    <div style="color: #0c5460; line-height: 1.8; font-size: 15px;">{{ $confirmation->notes }}</div>
                </div>
                @endif
            </div>
            @endif

            <!-- سبب الرفض -->
            @if($confirmation->rejection_reason)
            <div style="background: white; border-radius: 15px; padding: 30px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="color: #e74c3c; margin-bottom: 20px; font-size: 22px; font-weight: bold; border-bottom: 3px solid #e74c3c; padding-bottom: 15px;">
                    ⚠️ {{ __('app.production_confirmations.rejection_reason') }}
                </h3>
                <div style="background: #f8d7da; border: 2px solid #e74c3c; border-radius: 10px; padding: 20px;">
                    <div style="color: #721c24; line-height: 1.8; font-size: 16px;">{{ $confirmation->rejection_reason }}</div>
                </div>
            </div>
            @endif

        </div>

        <!-- العمود الأيمن - الجدول الزمني -->
        <div>

            <!-- معلومات الموظفين -->
            <div style="background: white; border-radius: 15px; padding: 25px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="color: #2c3e50; margin-bottom: 20px; font-size: 20px; font-weight: bold; border-bottom: 3px solid #9b59b6; padding-bottom: 12px;">
                    👥 {{ __('app.production_confirmations.employees') }}
                </h3>

                <div style="margin-bottom: 20px;">
                    <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">{{ __('app.production_confirmations.assigned_to') }}</div>
                    <div style="font-weight: bold; color: #2c3e50; font-size: 16px;">{{ $confirmation->assignedUser?->name ?? __('app.production_confirmations.table.not_specified') }}</div>
                </div>

                @if($confirmation->confirmedByUser)
                <div style="margin-bottom: 20px;">
                    <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">{{ __('app.production_confirmations.confirmed_by') }}</div>
                    <div style="font-weight: bold; color: #27ae60; font-size: 16px;">{{ $confirmation->confirmedByUser->name }}</div>
                </div>
                @endif

                @if($confirmation->rejectedByUser)
                <div>
                    <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">{{ __('app.production_confirmations.rejected_by') }}</div>
                    <div style="font-weight: bold; color: #e74c3c; font-size: 16px;">{{ $confirmation->rejectedByUser->name }}</div>
                </div>
                @endif
            </div>

            <!-- الجدول الزمني -->
            <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="color: #2c3e50; margin-bottom: 25px; font-size: 20px; font-weight: bold; border-bottom: 3px solid #3498db; padding-bottom: 12px;">
                    ⏱️ {{ __('app.production_confirmations.timeline') }}
                </h3>

                <!-- Timeline -->
                <div style="position: relative; padding-right: 30px;">

                    <!-- خط الجدول الزمني -->
                    <div style="position: absolute; right: 9px; top: 0; bottom: 0; width: 2px; background: #ecf0f1;"></div>

                    <!-- تم الإنشاء -->
                    <div style="position: relative; margin-bottom: 30px;">
                        <div style="position: absolute; right: -8px; width: 18px; height: 18px; background: #3498db; border: 3px solid white; border-radius: 50%; box-shadow: 0 0 0 2px #3498db;"></div>
                        <div style="margin-right: 30px;">
                            <div style="font-weight: bold; color: #2c3e50; font-size: 15px;">{{ __('app.production_confirmations.created_at') }}</div>
                            <div style="color: #7f8c8d; font-size: 14px;">{{ $confirmation->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    <!-- تم التأكيد / الرفض -->
                    @if($confirmation->status == 'confirmed')
                        <div style="position: relative; margin-bottom: 30px;">
                            <div style="position: absolute; right: -8px; width: 18px; height: 18px; background: #27ae60; border: 3px solid white; border-radius: 50%; box-shadow: 0 0 0 2px #27ae60;"></div>
                            <div style="margin-right: 30px;">
                                <div style="font-weight: bold; color: #27ae60; font-size: 15px;">{{ __('app.production_confirmations.confirmed_at') }}</div>
                                <div style="color: #7f8c8d; font-size: 14px;">{{ $confirmation->confirmed_at ? $confirmation->confirmed_at->format('d/m/Y H:i') : '-' }}</div>
                            </div>
                        </div>
                    @elseif($confirmation->status == 'rejected')
                        <div style="position: relative; margin-bottom: 30px;">
                            <div style="position: absolute; right: -8px; width: 18px; height: 18px; background: #e74c3c; border: 3px solid white; border-radius: 50%; box-shadow: 0 0 0 2px #e74c3c;"></div>
                            <div style="margin-right: 30px;">
                                <div style="font-weight: bold; color: #e74c3c; font-size: 15px;">{{ __('app.production_confirmations.rejected_at') }}</div>
                                <div style="color: #7f8c8d; font-size: 14px;">{{ $confirmation->rejected_at ? $confirmation->rejected_at->format('d/m/Y H:i') : '-' }}</div>
                            </div>
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

<style>
@keyframes pulse {
    0%, 100% {
        box-shadow: 0 0 0 2px #f39c12;
    }
    50% {
        box-shadow: 0 0 0 6px rgba(243, 156, 18, 0.3);
    }
}
</style>

@endsection
