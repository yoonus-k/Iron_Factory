@extends('master')

@section('title', __('stages.stage1_details'))

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
    .status-consumed { background: #f5f5f5; color: #616161; }
</style>

<div class="um-content-wrapper">
    <!-- Header Section -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <i class="feather icon-eye"></i>
            {{ __('stages.stand_details_title') }} - {{ $stand->stand_number }}
        </h1>
        <nav class="um-breadcrumb-nav">
            <span><i class="feather icon-home"></i> {{ __('stages.dashboard') }}</span>
            <i class="feather icon-chevron-left"></i>
            <a href="{{ route('manufacturing.stage1.index') }}">{{ __('stages.first_phase') }}</a>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('stages.stand_details') }}</span>
        </nav>
    </div>

    <!-- Barcode Display -->
    <div class="barcode-display">
        <div style="font-size: 18px; opacity: 0.9; margin-bottom: 10px;">{{ __('stages.barcode_title') }}</div>
        <div class="barcode-code">{{ $stand->barcode }}</div>
        <button onclick="printBarcode('{{ $stand->barcode }}', '{{ $stand->stand_number }}', '{{ $stand->material_name }}', {{ $stand->remaining_weight }})"
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
            @if($stand->status == 'created')
            <span class="status-badge status-created">{{ __('stages.stand_status_created') }}</span>
            @elseif($stand->status == 'in_process')
            <span class="status-badge status-in_process">{{ __('stages.stand_status_in_process') }}</span>
            @elseif($stand->status == 'completed')
            <span class="status-badge status-completed">{{ __('stages.stand_status_completed') }}</span>
            @elseif($stand->status == 'consumed')
            <span class="status-badge status-consumed">{{ __('stages.stand_status_consumed') }}</span>
            @endif
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">{{ __('stages.stand_number_label') }}</div>
                <div class="info-value">{{ $stand->stand_number }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.material_label') }}</div>
                <div class="info-value">{{ $stand->material_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.parent_barcode_label') }}</div>
                <div class="info-value" style="font-size: 14px; font-family: monospace;">{{ $stand->parent_barcode }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.total_weight_label') }}</div>
                <div class="info-value" style="color: #3498db;">{{ number_format($stand->weight, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.net_weight_label') }}</div>
                <div class="info-value" style="color: #27ae60;">{{ number_format($stand->remaining_weight, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.waste_label') }}</div>
                <div class="info-value" style="color: #e74c3c;">{{ number_format($stand->waste, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.wire_size_label') }}</div>
                <div class="info-value">{{ $stand->wire_size }} مم</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.created_by_label') }}</div>
                <div class="info-value" style="font-size: 16px;">{{ $stand->created_by_name ?? __('stages.not_specified') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.created_at_label') }}</div>
                <div class="info-value" style="font-size: 16px;">{{ \Carbon\Carbon::parse($stand->created_at)->format('Y-m-d H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- {{ __('stages.usage_history') }} -->
    @if($usageHistory)
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
                <div class="info-label">{{ __('stages.total_weight_label') }}</div>
                <div class="info-value">{{ number_format($usageHistory->total_weight, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.net_weight_label') }}</div>
                <div class="info-value">{{ number_format($usageHistory->net_weight, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.stand_weight_label') }}</div>
                <div class="info-value">{{ number_format($usageHistory->stand_weight, 2) }} {{ __('stages.weight_unit') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.waste_percentage_label') }}</div>
                <div class="info-value">{{ number_format($usageHistory->waste_percentage, 2) }}%</div>
            </div>
            <div class="info-item">
                <div class="info-label">{{ __('stages.start_time_label') }}</div>
                <div class="info-value" style="font-size: 14px;">{{ \Carbon\Carbon::parse($usageHistory->started_at)->format('Y-m-d H:i') }}</div>
            </div>
        </div>

        @if($usageHistory->notes)
        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 8px; border-right: 3px solid #ffc107;">
            <strong style="color: #856404;">📝 {{ __('stages.notes_label') }}:</strong>
            <p style="margin: 8px 0 0 0; color: #856404;">{{ $usageHistory->notes }}</p>
        </div>
        @endif
    </div>
    @endif

    <!-- {{ __('stages.tracking_logs') }} -->
    @if($trackingLogs->count() > 0)
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
    @if($operationLogs->count() > 0)
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
        <a href="{{ route('manufacturing.stage1.index') }}" class="um-btn um-btn-primary" style="flex: 1;">
            <i class="feather icon-arrow-right"></i> {{ __('stages.back_to_list') }}
        </a>
        <button onclick="printBarcode('{{ $stand->barcode }}', '{{ $stand->stand_number }}', '{{ $stand->material_name }}', {{ $stand->remaining_weight }})"
                class="um-btn um-btn-success" style="flex: 1;">
            <i class="feather icon-printer"></i> {{ __('stages.print_barcode_button') }}
        </button>
    </div>
</div>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
function printBarcode(barcode, standNumber, materialName, netWeight) {
    const printWindow = window.open('', '', 'height=650,width=850');
    printWindow.document.write('<html dir="rtl"><head><title>' + '{{ __("stages.print_barcode_title") }}' + ' - ' + standNumber + '</title>');
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
    printWindow.document.write('<div class="stand-number">{{ __("stages.stand_label_print") }} ' + standNumber + '</div>');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
    printWindow.document.write('<div class="info">');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.material_label_print") }}:</span><span class="value">' + materialName + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __("stages.net_weight_label_print") }}:</span><span class="value">' + netWeight + ' {{ __("stages.weight_unit") }}</span></div>');
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
