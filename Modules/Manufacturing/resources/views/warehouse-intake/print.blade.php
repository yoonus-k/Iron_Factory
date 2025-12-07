<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إذن إدخال مستودع - {{ $intakeRequest->request_number }}</title>
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
            background: white;
        }

        .print-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 30px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        .header h2 {
            font-size: 22px;
            color: #4CAF50;
            margin-bottom: 5px;
        }

        .header .request-number {
            font-size: 18px;
            color: #0066cc;
            font-weight: bold;
            margin-top: 10px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .info-box {
            flex: 1;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            margin: 0 10px;
        }

        .info-box:first-child {
            margin-right: 0;
        }

        .info-box:last-child {
            margin-left: 0;
        }

        .info-box h3 {
            font-size: 16px;
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .info-row .label {
            color: #666;
            font-weight: normal;
        }

        .info-row .value {
            color: #333;
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table thead {
            background: #4CAF50;
            color: white;
        }

        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .items-table th {
            font-size: 14px;
            font-weight: bold;
        }

        .items-table td {
            font-size: 13px;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .items-table tfoot {
            background: #f0f0f0;
            font-weight: bold;
        }

        .items-table tfoot td {
            font-size: 15px;
        }

        .summary-boxes {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }

        .summary-box {
            text-align: center;
            padding: 20px;
            border: 2px solid #4CAF50;
            border-radius: 8px;
            flex: 1;
            margin: 0 10px;
        }

        .summary-box .number {
            font-size: 32px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 5px;
        }

        .summary-box .label {
            font-size: 14px;
            color: #666;
        }

        .notes-section {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
            background: #fafafa;
        }

        .notes-section h3 {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
        }

        .notes-section p {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        .signature-box {
            text-align: center;
            flex: 1;
            margin: 0 15px;
        }

        .signature-line {
            border-top: 2px solid #333;
            margin-top: 60px;
            padding-top: 10px;
        }

        .signature-box .title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .signature-box .name {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            font-size: 12px;
            color: #999;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }

        .status-approved {
            background: #4CAF50;
            color: white;
        }

        @media print {
            body {
                padding: 0;
            }

            .print-container {
                padding: 15px;
            }

            @page {
                size: A4;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('assets/images/logo/logo-dark.jpg') }}" alt="{{ __('warehouse_intake.company_logo') }}" class="logo">
            <h1>🏭 {{ __('warehouse_intake.factory_name') }}</h1>
            <h2>{{ __('warehouse_intake.warehouse_entry_permit') }}</h2>
            <div class="request-number">{{ __('warehouse_intake.request_number') }}: {{ $intakeRequest->request_number }}</div>
            @if($intakeRequest->status === 'approved')
            <div class="status-badge status-approved">✓ {{ __('warehouse_intake.approved') }}</div>
            @endif
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <!-- معلومات الطلب -->
            <div class="info-box">
                <h3>📋 {{ __('warehouse_intake.request_information') }}</h3>
                <div class="info-row">
                    <span class="label">{{ __('warehouse_intake.creation_date') }}:</span>
                    <span class="value">{{ $intakeRequest->created_at->format('Y-m-d') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('warehouse_intake.time') }}:</span>
                    <span class="value">{{ $intakeRequest->created_at->format('H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('warehouse_intake.shift_responsible') }}:</span>
                    <span class="value">{{ $intakeRequest->requestedBy->name ?? '-' }}</span>
                </div>
                @if($intakeRequest->approved_at)
                <div class="info-row">
                    <span class="label">{{ __('warehouse_intake.approval_date') }}:</span>
                    <span class="value">{{ $intakeRequest->approved_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('warehouse_intake.warehouse_keeper') }}:</span>
                    <span class="value">{{ $intakeRequest->approvedBy->name ?? '-' }}</span>
                </div>
                @endif
            </div>

            <!-- إحصائيات -->
            <div class="info-box">
                <h3>📊 {{ __('warehouse_intake.statistics') }}</h3>
                <div class="info-row">
                    <span class="label">{{ __('warehouse_intake.boxes_count') }}:</span>
                    <span class="value">{{ $intakeRequest->boxes_count }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('warehouse_intake.total_weight') }}:</span>
                    <span class="value">{{ number_format($intakeRequest->total_weight, 2) }} كجم</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('warehouse_intake.average_weight') }}:</span>
                    <span class="value">{{ number_format($intakeRequest->total_weight / max($intakeRequest->boxes_count, 1), 2) }} كجم</span>
                </div>
            </div>
        </div>

        <!-- Summary Boxes -->
        <div class="summary-boxes">
            <div class="summary-box">
                <div class="number">{{ $intakeRequest->boxes_count }}</div>
                <div class="label">{{ __('warehouse_intake.boxes_count') }}</div>
            </div>
            <div class="summary-box">
                <div class="number">{{ number_format($intakeRequest->total_weight, 2) }}</div>
                <div class="label">{{ __('warehouse_intake.total_weight_kg') }}</div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>{{ __('warehouse_intake.barcode') }}</th>
                    <th>{{ __('warehouse_intake.packaging_type') }}</th>
                    <th>{{ __('warehouse_intake.specifications') }}</th>
                    <th style="width: 120px;">{{ __('warehouse_intake.weight_kg') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $totalWeight = 0; @endphp
                @foreach($intakeRequest->items as $index => $item)
                @php $totalWeight += $item->weight; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $item->barcode }}</strong></td>
                    <td>{{ $item->packaging_type }}</td>
                    <td style="text-align: right; font-size: 12px;">
                        @if(isset($item->materials) && $item->materials->count() > 0)
                            @foreach($item->materials as $material)
                                <div style="margin-bottom: 3px;">
                                    @if($material->color) {{ $material->color }} @endif
                                    @if($material->plastic_type) - {{ $material->plastic_type }} @endif
                                    @if($material->wire_size) - {{ $material->wire_size }} @endif
                                </div>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td><strong>{{ number_format($item->weight, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: left;">{{ __('warehouse_intake.total') }}:</td>
                    <td><strong>{{ number_format($totalWeight, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>

        <!-- Notes Section -->
        @if($intakeRequest->notes)
        <div class="notes-section">
            <h3>📝 {{ __('warehouse_intake.notes') }}</h3>
            <p>{{ $intakeRequest->notes }}</p>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">
                    <div class="title">{{ __('warehouse_intake.shift_responsible') }}</div>
                    <div class="name">{{ $intakeRequest->requestedBy->name ?? '' }}</div>
                </div>
            </div>

            <div class="signature-box">
                <div class="signature-line">
                    <div class="title">{{ __('warehouse_intake.warehouse_keeper') }}</div>
                    <div class="name">{{ $intakeRequest->approvedBy->name ?? '' }}</div>
                </div>
            </div>

            <div class="signature-box">
                <div class="signature-line">
                    <div class="title">{{ __('warehouse_intake.general_manager') }}</div>
                    <div class="name"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>{{ __('warehouse_intake.printed_at', ['time' => now()->format('Y-m-d H:i:s')]) }}</p>
            <p>{{ __('warehouse_intake.document_generated_by_system') }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
