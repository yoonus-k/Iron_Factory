@extends('master')

@section('title', __('stages.stage3_details'))

@section('content')

<style>
    .detail-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border-right: 4px solid #667eea;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .detail-title {
        font-size: 20px;
        font-weight: 700;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .info-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-right: 3px solid #667eea;
    }

    .info-label {
        font-size: 13px;
        color: #7f8c8d;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .info-value {
        font-size: 18px;
        color: #2c3e50;
        font-weight: 700;
    }

    .barcode-display {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        margin: 20px 0;
    }

    .barcode-code {
        font-size: 32px;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        letter-spacing: 4px;
        margin: 15px 0;
    }

    .log-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 12px;
        border-right: 3px solid #27ae60;
    }

    .log-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .log-action {
        font-weight: 600;
        color: #2c3e50;
    }

    .log-time {
        color: #7f8c8d;
        font-size: 13px;
    }

    .log-details {
        color: #555;
        font-size: 14px;
        line-height: 1.6;
    }

    .empty-logs {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-created { background: #e3f2fd; color: #1976d2; }
    .status-in_process { background: #fff3e0; color: #f57c00; }
    .status-completed { background: #e8f5e9; color: #388e3c; }
    .status-packed { background: #f3e5f5; color: #7b1fa2; }
</style>

<div class="um-content-wrapper">
    <!-- Header Section -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <i class="feather icon-eye"></i>
            {{ __('stages.stage3_details_title') }} - {{ $coil->barcode }}
        </h1>
        <nav class="um-breadcrumb-nav">
            <span><i class="feather icon-home"></i> {{ __('stages.dashboard') }}</span>
            <i class="feather icon-chevron-left"></i>
            <a href="{{ route('manufacturing.stage3.index') }}">{{ __('stages.third_phase') }}</a>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('stages.details') }}</span>
        </nav>
    </div>

    <!-- Barcode Display -->
    <div class="barcode-display">
        <div style="font-size: 18px; opacity: 0.9; margin-bottom: 10px;">{{ __('stages.barcode_title') }}</div>
        <div class="barcode-code">{{ $coil->barcode }}</div>
        <button onclick="printBarcode('{{ $coil->barcode }}', '{{ $coil->color ?? '' }}', '{{ $coil->wire_size }}', {{ $coil->net_weight ?? $coil->total_weight }}, {{ $coil->total_weight }}, {{ $coil->wrapping_weight ?? 0 }})"
                style="background: rgba(255,255,255,0.2); border: 2px solid white; color: white; padding: 12px 30px; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 15px; transition: all 0.3s;">
            <i class="feather icon-printer"></i> {{ __('stages.print_barcode_button') }}
        </button>
    </div>

    <!-- {{ __('stages.basic_information') }} -->
    <div class="detail-card">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-info"></i>
                {{ __('stages.basic_information') }}
            </div>
            @if($coil->status == 'created')
            <span class="status-badge status-created">{{ __('stages.stand_status_created') }}</span>
            @elseif($coil->status == 'in_process')
            <span class="status-badge status-in_process">{{ __('stages.stand_status_in_process') }}</span>
            @elseif($coil->status == 'completed')
            <span class="status-badge status-completed">{{ __('stages.stand_status_completed') }}</span>
            @elseif($coil->status == 'packed')
            <span class="status-badge status-packed">{{ __('stages.packed_status') }}</span>
            @endif
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">{{ __('stages.barcode_label') }}</div>
                <div class="info-value" style="font-size: 14px; font-family: monospace;">{{ $coil->barcode }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.parent_barcode_label') }}</div>
                <div class="info-value" style="font-size: 14px; font-family: monospace;">{{ $coil->parent_barcode }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.color_label') }}</div>
                <div class="info-value">{{ $coil->color ?? __('stages.not_specified') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.wire_size_label') }}</div>
                <div class="info-value">{{ $coil->wire_size }} مم</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.base_weight_label') }}</div>
                <div class="info-value" style="color: #3498db;">{{ number_format($coil->base_weight, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.plastic_weight_label') }}</div>
                <div class="info-value" style="color: #9b59b6;">{{ number_format($coil->plastic_weight, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.dye_weight_label') }}</div>
                <div class="info-value" style="color: #e67e22;">{{ number_format($coil->dye_weight, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">الوزن الصافي (بعد خصم اللفاف)</div>
                <div class="info-value" style="color: #27ae60;">{{ number_format($coil->net_weight ?? $coil->total_weight, 2) }} {{ __('stages.weight_unit') }}</div>
                @if($coil->wrapping_weight)
                <small style="color: #7f8c8d; font-weight: 400; display:block; margin-top:4px;">{{ __('stages.total_weight_label') }}: {{ number_format($coil->total_weight, 2) }} {{ __('stages.weight_unit') }} | وزن اللفاف: {{ number_format($coil->wrapping_weight, 2) }} {{ __('stages.weight_unit') }}</small>
                @endif
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.waste_label') }}</div>
                <div class="info-value" style="color: #e74c3c;">{{ number_format($coil->waste, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.plastic_type_label') }}</div>
                <div class="info-value" style="font-size: 16px;">{{ $coil->plastic_type ?? __('stages.not_specified') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.created_by_label') }}</div>
                <div class="info-value" style="font-size: 16px;">{{ $coil->creator->name ?? __('stages.not_specified') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.created_at_label') }}</div>
                <div class="info-value" style="font-size: 16px;">{{ $coil->created_at->format('Y-m-d H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- {{ __('stages.usage_history') }} -->
    @if(isset($usageHistory) && $usageHistory)
    <div class="detail-card" style="border-right-color: #27ae60;">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-activity"></i>
                {{ __('stages.usage_history') }}
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">{{ __('stages.user_label') }}</div>
                <div class="info-value" style="font-size: 16px;">{{ $usageHistory->user_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.action_type') }}</div>
                <div class="info-value">{{ $usageHistory->action ?? __('stages.not_specified') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.input_weight_label') }}</div>
                <div class="info-value">{{ number_format($usageHistory->input_weight ?? 0, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.output_weight_label') }}</div>
                <div class="info-value">{{ number_format($usageHistory->output_weight ?? 0, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.start_time_label') }}</div>
                <div class="info-value" style="font-size: 14px;">{{ isset($usageHistory->started_at) ? \Carbon\Carbon::parse($usageHistory->started_at)->format('Y-m-d H:i') : __('stages.not_specified') }}</div>
            </div>
        </div>

        @if(isset($usageHistory->notes) && $usageHistory->notes)
        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 8px; border-right: 3px solid #ffc107;">
            <strong style="color: #856404;">📝 {{ __('stages.notes_label') }}:</strong>
            <p style="margin: 8px 0 0 0; color: #856404;">{{ $usageHistory->notes }}</p>
        </div>
        @endif
    </div>
    @endif

    <!-- {{ __('stages.tracking_logs') }} -->
    @if(isset($trackingLogs) && $trackingLogs->count() > 0)
    <div class="detail-card" style="border-right-color: #3498db;">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-map"></i>
                {{ __('stages.tracking_logs') }} ({{ $trackingLogs->count() }})
            </div>
        </div>

        @foreach($trackingLogs as $log)
        <div class="log-item">
            <div class="log-header">
                <div class="log-action">
                    <i class="feather icon-check-circle" style="color: #27ae60;"></i>
                    {{ $log->action }} - {{ $log->stage }}
                </div>
                <div class="log-time">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</div>
            </div>
            <div class="log-details">
                <strong>{{ __('stages.worker_label') }}:</strong> {{ $log->worker_name ?? __('stages.not_specified') }}<br>
                <strong>{{ __('stages.input_weight_label') }}:</strong> {{ number_format($log->input_weight, 2) }} {{ __('stages.weight_unit') }} |
                <strong>{{ __('stages.output_weight_label') }}:</strong> {{ number_format($log->output_weight, 2) }} {{ __('stages.weight_unit') }}<br>
                <strong>{{ __('stages.waste_label') }}:</strong> {{ number_format($log->waste_amount, 2) }} {{ __('stages.weight_unit') }} ({{ number_format($log->waste_percentage, 2) }}%)
                @if($log->notes)
                <br><strong>{{ __('stages.notes_label') }}:</strong> {{ $log->notes }}
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- {{ __('stages.operation_logs') }} -->
    @if(isset($operationLogs) && $operationLogs->count() > 0)
    <div class="detail-card" style="border-right-color: #f39c12;">
        <div class="detail-header">
            <div class="detail-title">
                <i class="feather icon-list"></i>
                {{ __('stages.operation_logs') }} ({{ $operationLogs->count() }})
            </div>
        </div>

        @foreach($operationLogs as $log)
        <div class="log-item" style="border-right-color: #f39c12;">
            <div class="log-header">
                <div class="log-action">
                    <i class="feather icon-activity" style="color: #f39c12;"></i>
                    {{ $log->action ?? __('stages.operation_label') }}
                </div>
                <div class="log-time">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</div>
            </div>
            <div class="log-details">
                <strong>{{ __('stages.user_label') }}:</strong> {{ $log->user_name ?? __('stages.system_label') }}<br>
                @if($log->description)
                <strong>{{ __('stages.description_label') }}:</strong> {{ $log->description }}
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="detail-card">
        <div class="empty-logs">
            <i class="feather icon-inbox" style="font-size: 48px; opacity: 0.3;"></i>
            <p>{{ __('stages.no_operations_logged') }}</p>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div style="display: flex; gap: 15px; margin-top: 25px;">
        <a href="{{ route('manufacturing.stage3.index') }}" class="um-btn um-btn-primary" style="flex: 1;">
            <i class="feather icon-arrow-right"></i> {{ __('stages.back_to_list') }}
        </a>
        <button onclick="printBarcode('{{ $coil->barcode }}', '{{ $coil->color ?? '' }}', '{{ $coil->wire_size }}', {{ $coil->net_weight ?? $coil->total_weight }}, {{ $coil->total_weight }}, {{ $coil->wrapping_weight ?? 0 }})"
                class="um-btn um-btn-success" style="flex: 1;">
            <i class="feather icon-printer"></i> {{ __('stages.print_barcode_button') }}
        </button>
    </div>
</div>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
function printBarcode(barcode, color, wireSize, netWeight, totalWeight, wrappingWeight) {
    const cleanNet = Number(netWeight || 0).toFixed(2);
    const cleanTotal = Number(totalWeight || 0).toFixed(2);
    const cleanWrap = Number(wrappingWeight || 0).toFixed(2);
    const printWindow = window.open('', '', 'height=650,width=850');
    printWindow.document.write('<html dir="rtl"><head><title>' + '{{ __("stages.print_barcode_title") }}' + ' - ' + barcode + '</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
    printWindow.document.write('.barcode-container { background: white; padding: 50px; border-radius: 16px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); text-align: center; max-width: 550px; }');
    printWindow.document.write('.title { font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 4px solid #667eea; }');
    printWindow.document.write('.barcode-code { font-size: 22px; font-weight: bold; color: #2c3e50; margin: 25px 0; letter-spacing: 4px; font-family: "Courier New", monospace; }');
    printWindow.document.write('.info { margin-top: 30px; padding: 25px; background: #f8f9fa; border-radius: 10px; text-align: right; }');
    printWindow.document.write('.info-row { margin: 12px 0; display: flex; justify-content: space-between; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 16px; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 18px; }');
    printWindow.document.write('@media print { body { background: white; } }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<div class="barcode-container">');
    printWindow.document.write('<div class="title">{{ __("stages.third_phase") }}</div>');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
    printWindow.document.write('<div class="info">');
    if (color) {
        printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.color_label") }}:</span><span class="value">' + color + '</span></div>');
    }
    printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.wire_size_label") }}:</span><span class="value">' + wireSize + ' مم</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">الوزن الصافي (بعد خصم اللفاف):</span><span class="value">' + cleanNet + ' {{ __("stages.weight_unit") }}</span></div>');
    if (cleanWrap > 0) {
        printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.total_weight_label") }}:</span><span class="value">' + cleanTotal + ' {{ __("stages.weight_unit") }}</span></div>');
        printWindow.document.write('<div class="info-row"><span class="label">وزن اللفاف:</span><span class="value">' + cleanWrap + ' {{ __("stages.weight_unit") }}</span></div>');
    } else {
        printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.total_weight_label") }}:</span><span class="value">' + cleanTotal + ' {{ __("stages.weight_unit") }}</span></div>');
    }
    printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.date_label_print") }}:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
    printWindow.document.write('</div></div>');
    printWindow.document.write('<script>');
    printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2, height: 90, displayValue: false, margin: 12 });');
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
    printWindow.document.write('<\/script></body></html>');
    printWindow.document.close();
}
</script>

@endsection
