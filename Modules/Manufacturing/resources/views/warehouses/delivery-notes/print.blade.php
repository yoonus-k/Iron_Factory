<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('delivery_notes.delivery_note') }} - {{ $deliveryNote->note_number ?? $deliveryNote->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            padding: 20px;
            background: #ffffff;
            color: #1f2933;
        }

        .print-container {
            max-width: 210mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 32px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #1e88e5;
            padding-bottom: 24px;
            margin-bottom: 32px;
        }

        .header .logo {
            max-width: 160px;
            height: auto;
            margin-bottom: 16px;
        }

        .header h1 {
            font-size: 28px;
            color: #111827;
            margin-bottom: 8px;
        }

        .header h2 {
            font-size: 20px;
            color: #1e88e5;
            margin-bottom: 12px;
        }

        .request-number {
            font-size: 16px;
            color: #2563eb;
            font-weight: bold;
        }

        .header-meta {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 18px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .meta-item {
            min-width: 180px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 16px;
        }

        .meta-label {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .meta-value {
            font-size: 15px;
            color: #111827;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 12px;
        }

        .status-success {
            background: #d1fae5;
            color: #065f46;
        }

        .status-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .status-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-info {
            background: #e0f2fe;
            color: #075985;
        }

        .info-section {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-box {
            flex: 1;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
        }

        .info-box h3 {
            font-size: 16px;
            color: #0f172a;
            margin-bottom: 12px;
            border-bottom: 2px solid #1e88e5;
            padding-bottom: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .info-row .label {
            color: #6b7280;
        }

        .info-row .value {
            color: #111827;
            font-weight: 600;
        }

        .summary-boxes {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .summary-box {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 18px;
            text-align: center;
            background: #f8fafc;
        }

        .summary-box.accent {
            background: #1e88e5;
            color: #ffffff;
            border-color: #1e88e5;
        }

        .summary-box .number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .summary-box .label {
            font-size: 13px;
        }

        .section-title {
            font-size: 18px;
            color: #0f172a;
            margin-bottom: 12px;
            border-right: 4px solid #1e88e5;
            padding-right: 12px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 13px;
        }

        .items-table thead {
            background: #1e293b;
            color: #ffffff;
        }

        .items-table th,
        .items-table td {
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .notes-section {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            background: #fefce8;
            margin-bottom: 32px;
        }

        .notes-section h3 {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .signatures {
            display: flex;
            gap: 25px;
            margin-top: 40px;
        }

        .signature-box {
            flex: 1;
            text-align: center;
        }

        .signature-line {
            border-top: 2px solid #0f172a;
            margin-top: 60px;
            padding-top: 10px;
        }

        .signature-box .title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }

        .signature-box .name {
            font-size: 13px;
            color: #4b5563;
            margin-top: 6px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            margin-top: 40px;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
        }

        @media print {
            body {
                padding: 0;
                background: #ffffff;
            }

            .print-container {
                padding: 20px;
            }

            @page {
                size: A4;
                margin: 12mm;
            }
        }
    </style>
</head>
<body>
@php
    $materialDetails = $deliveryNote->material?->materialDetails;
    if (is_array($materialDetails)) {
        $materialDetails = collect($materialDetails);
    }
    $primaryMaterialDetail = $materialDetails instanceof \Illuminate\Support\Collection ? $materialDetails->first() : null;
    $unitName = $primaryMaterialDetail?->unit?->name ?? __('delivery_notes.unit');
    $warehouseName = '-';
    if ($deliveryNote->warehouse) {
        $warehouseName = method_exists($deliveryNote->warehouse, 'getName')
            ? $deliveryNote->warehouse->getName()
            : ($deliveryNote->warehouse->warehouse_name ?? $deliveryNote->warehouse->name ?? '-');
    } elseif ($primaryMaterialDetail?->warehouse) {
        $warehouse = $primaryMaterialDetail->warehouse;
        $warehouseName = method_exists($warehouse, 'getName')
            ? $warehouse->getName()
            : ($warehouse->warehouse_name ?? $warehouse->name ?? '-');
    }
    $coils = $deliveryNote->coils ?? collect();
    if (!($coils instanceof \Illuminate\Support\Collection)) {
        $coils = collect($coils);
    }
    $materialName = optional($deliveryNote->material)->display_name
        ?? optional($deliveryNote->material)->name_ar
        ?? optional($deliveryNote->material)->name_en
        ?? '-';
    $supplierContact = $deliveryNote->received_from_person
        ?: ($deliveryNote->driver_name ?: null);
    $registrationTranslationKey = match ($deliveryNote->registration_status) {
        'registered' => 'registered',
        'in_production' => 'in_production',
        'completed' => 'completed',
        default => 'pending',
    };
@endphp
    <div class="print-container">
        <div class="header">
            <img src="{{ asset('assets/images/logo/logo-dark.jpg') }}" alt="logo" class="logo">
            <h1>{{ __('delivery_notes.delivery_note') }}</h1>
            <h2>{{ __('delivery_notes.delivery_note_details') }}</h2>
            <div class="request-number">{{ __('delivery_notes.note_number') }}: {{ $deliveryNote->note_number ?? $deliveryNote->id }}</div>
        </div>

        <div class="header-meta">
            <div class="meta-item">
                <span class="meta-label">{{ __('delivery_notes.vehicle_plate_number') }}</span>
                <span class="meta-value">{{ $deliveryNote->vehicle_plate_number ?? '—' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">{{ __('delivery_notes.driver_name') }}</span>
                <span class="meta-value">{{ $deliveryNote->driver_name ?? ($deliveryNote->received_from_person ?? '—') }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">{{ __('delivery_notes.received_from_person') }}</span>
                <span class="meta-value">{{ $supplierContact ?? '—' }}</span>
            </div>
        </div>

        <div class="info-section">
            <div class="info-box">
                <h3>{{ __('delivery_notes.basic_info') }}</h3>
                <div class="info-row">
                    <span class="label">{{ __('delivery_notes.delivery_date') }}:</span>
                    <span class="value">{{ $deliveryNote->delivery_date?->format('Y-m-d') ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('delivery_notes.driver_name') }}:</span>
                    <span class="value">{{ $deliveryNote->driver_name ?? ($deliveryNote->received_from_person ?? '-') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('delivery_notes.material') }}:</span>
                    <span class="value">{{ $materialName }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('delivery_notes.warehouse') }}:</span>
                    <span class="value">{{ $warehouseName }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('delivery_notes.invoice_number') }}:</span>
                    <span class="value">{{ $deliveryNote->invoice_number ?? $deliveryNote->invoice_reference_number ?? '-' }}</span>
                </div>
            </div>

            <div class="info-box">
                <h3>{{ __('delivery_notes.warehouse_management') }}</h3>
                <div class="info-row">
                    <span class="label">{{ __('delivery_notes.vehicle_plate_number') }}:</span>
                    <span class="value">{{ $deliveryNote->vehicle_plate_number ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('delivery_notes.received_from_person') }}:</span>
                    <span class="value">{{ $supplierContact ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('delivery_notes.received_by') }}:</span>
                    <span class="value">{{ $deliveryNote->receiver->name ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('delivery_notes.recorded_by') }}:</span>
                    <span class="value">{{ $deliveryNote->recordedBy->name ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('delivery_notes.registered_by') }}:</span>
                    <span class="value">{{ $deliveryNote->registeredBy->name ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('delivery_notes.registration_status') }}:</span>
                    <span class="value">{{ __('delivery_notes.' . $registrationTranslationKey) }}</span>
                </div>
            </div>
        </div>

        <div class="summary-boxes">
            <div class="summary-box">
                <div class="number">{{ number_format($transferSummary['registered'], 2) }} {{ $unitName }}</div>
                <div class="label">{{ __('delivery_notes.incoming_quantity_registered') }}</div>
            </div>
            <div class="summary-box">
                <div class="number">{{ number_format($transferSummary['transferred'], 2) }} {{ $unitName }}</div>
                <div class="label">{{ __('delivery_notes.transferred_to_production') }}</div>
            </div>
            <div class="summary-box">
                <div class="number">{{ number_format($transferSummary['remaining'], 2) }} {{ $unitName }}</div>
                <div class="label">{{ __('delivery_notes.remaining_in_warehouse') }}</div>
            </div>
            <div class="summary-box accent">
                <div class="number">{{ number_format($transferSummary['percentage'], 1) }}%</div>
                <div class="label">{{ __('delivery_notes.transfer_percentage') }}</div>
            </div>
        </div>

        @if($coils->count() > 0)
            <h3 class="section-title">{{ __('delivery_notes.coils_details') }}</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('delivery_notes.coil_barcodes') }}</th>
                        <th>{{ __('delivery_notes.coil_number') }}</th>
                        <th>{{ __('delivery_notes.coil_weight') }}</th>
                        <th>{{ __('delivery_notes.remaining_weight') }}</th>
                    </tr>
                </thead>
                <tbody>
                @php $totalCoilWeight = 0; @endphp
                @foreach($coils as $index => $coil)
                    @php $totalCoilWeight += $coil->coil_weight ?? 0; @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $coil->coil_barcode ?? '-' }}</td>
                        <td>{{ $coil->coil_number ?? '-' }}</td>
                        <td>{{ number_format($coil->coil_weight ?? 0, 2) }}</td>
                        <td>{{ number_format($coil->remaining_weight ?? 0, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right; font-weight:bold;">{{ __('delivery_notes.total') }}</td>
                        <td colspan="2" style="font-weight:bold;">{{ number_format($totalCoilWeight, 2) }} {{ $unitName }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <h3 class="section-title">{{ __('delivery_notes.delivery_note_info') }}</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>{{ __('delivery_notes.material') }}</th>
                        <th>{{ __('delivery_notes.quantity') }}</th>
                        <th>{{ __('delivery_notes.unit') }}</th>
                        <th>{{ __('delivery_notes.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $materialName }}</td>
                        <td>{{ number_format($deliveryNote->quantity ?? $deliveryNote->delivery_quantity ?? 0, 2) }}</td>
                        <td>{{ $unitName }}</td>
                        <td>{{ $deliveryNote->notes ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        @endif

        @if(!empty($deliveryNote->notes))
            <div class="notes-section">
                <h3>{{ __('delivery_notes.notes') }}</h3>
                <p>{{ $deliveryNote->notes }}</p>
            </div>
        @endif

        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">
                    <div class="title">{{ __('delivery_notes.recorded_by') }}</div>
                    <div class="name">{{ $deliveryNote->recordedBy->name ?? '-' }}</div>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <div class="title">{{ __('delivery_notes.received_by') }}</div>
                    <div class="name">{{ $deliveryNote->receiver->name ?? '-' }}</div>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <div class="title">{{ __('delivery_notes.supplier') }}</div>
                    <div class="name">{{ $deliveryNote->supplier->name ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="footer">
            {{ __('delivery_notes.delivery_note') }} #{{ $deliveryNote->note_number ?? $deliveryNote->id }} · {{ __('delivery_notes.print') }}: {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
