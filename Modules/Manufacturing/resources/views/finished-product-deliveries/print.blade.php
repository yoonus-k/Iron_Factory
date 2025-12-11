<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.finished_products.delivery_note') }} {{ __('app.finished_products.note_number') }}: {{ $deliveryNote->note_number ?? $deliveryNote->id }}</title>
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
            border-bottom: 3px solid #333;
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
            color: #666;
            margin-bottom: 5px;
        }

        .header .note-number {
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
            border-bottom: 2px solid #0066cc;
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
            background: #333;
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
            border: 2px solid #0066cc;
            border-radius: 8px;
            flex: 1;
            margin: 0 10px;
        }

        .summary-box .number {
            font-size: 32px;
            font-weight: bold;
            color: #0066cc;
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
            background: #28a745;
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
            <img src="{{ asset('assets/images/logo/logo-dark.jpg') }}" alt="{{ __('app.finished_products.company_logo') }}" class="logo">
            <h1>🏭 {{ __('app.finished_products.factory_name') }}</h1>
            <h2>{{ __('app.finished_products.delivery_note') }}</h2>
            <div class="note-number">{{ __('app.finished_products.note_number') }}: {{ $deliveryNote->note_number ?? '#' . $deliveryNote->id }}</div>
            @if($deliveryNote->status == 'approved')
            <div class="status-badge status-approved">✓ {{ __('app.finished_products.approved') }}</div>
            @endif
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <!-- معلومات العميل -->
            <div class="info-box">
                <h3>👤 {{ __('app.finished_products.customer_details') }}</h3>
                @if($deliveryNote->customer)
                <div class="info-row">
                    <span class="label">{{ __('app.finished_products.customer_code') }}:</span>
                    <span class="value">{{ $deliveryNote->customer->customer_code }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('app.finished_products.customer_name') }}:</span>
                    <span class="value">{{ $deliveryNote->customer->name }}</span>
                </div>
                @if($deliveryNote->customer->company_name)
                <div class="info-row">
                    <span class="label">{{ __('app.finished_products.company') }}:</span>
                    <span class="value">{{ $deliveryNote->customer->company_name }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="label">{{ __('app.finished_products.phone') }}:</span>
                    <span class="value">{{ $deliveryNote->customer->phone }}</span>
                </div>
                @if($deliveryNote->customer->address)
                <div class="info-row">
                    <span class="label">{{ __('app.finished_products.address') }}:</span>
                    <span class="value">{{ $deliveryNote->customer->address }}</span>
                </div>
                @endif
                @else
                <p style="text-align: center; color: #999;">{{ __('app.finished_products.customer_not_set') }}</p>
                @endif
            </div>

            <!-- معلومات الإذن -->
            <div class="info-box">
                <h3>📋 {{ __('app.finished_products.note_info_section') }}</h3>
                <div class="info-row">
                    <span class="label">{{ __('app.finished_products.creation_date') }}:</span>
                    <span class="value">{{ $deliveryNote->created_at->format('Y-m-d') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('app.finished_products.time') }}:</span>
                    <span class="value">{{ $deliveryNote->created_at->format('H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('app.finished_products.created_by') }}:</span>
                    <span class="value">{{ $deliveryNote->recordedBy->name ?? '-' }}</span>
                </div>
                @if($deliveryNote->approved_at)
                <div class="info-row">
                    <span class="label">{{ __('app.finished_products.approval_date') }}:</span>
                    <span class="value">{{ $deliveryNote->approved_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">{{ __('app.finished_products.approved_by') }}:</span>
                    <span class="value">{{ $deliveryNote->approvedBy->name ?? '-' }}</span>
                </div>
                @endif
                @if($deliveryNote->print_count > 1)
                <div class="info-row">
                    <span class="label">{{ __('app.finished_products.print_count') }}:</span>
                    <span class="value">{{ $deliveryNote->print_count }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Delivery Information -->
        @if($deliveryNote->driver_name || $deliveryNote->vehicle_number || $deliveryNote->city)
        <div class="info-section">
            <div class="info-box" style="border-color: #17a2b8;">
                <h3>🚚 معلومات التوصيل</h3>
                @if($deliveryNote->driver_name)
                <div class="info-row">
                    <span class="label">اسم السائق:</span>
                    <span class="value">{{ $deliveryNote->driver_name }}</span>
                </div>
                @endif
                @if($deliveryNote->vehicle_number)
                <div class="info-row">
                    <span class="label">رقم اللوحة:</span>
                    <span class="value">{{ $deliveryNote->vehicle_number }}</span>
                </div>
                @endif
                @if($deliveryNote->city)
                <div class="info-row">
                    <span class="label">المدينة/الوجهة:</span>
                    <span class="value">{{ $deliveryNote->city }}</span>
                </div>
                @endif
            </div>
        </div>
        @else
        <div style="margin-bottom: 20px;"></div>
        @endif

        <!-- Return to normal flow -->
        <div style="clear: both;">
        </div>

        <!-- Summary Boxes -->
        <div class="summary-boxes">
            <div class="summary-box">
                <div class="number">{{ $deliveryNote->items->count() }}</div>
                <div class="label">{{ __('app.finished_products.boxes_count_label') }}</div>
            </div>
            <div class="summary-box">
                <div class="number">{{ number_format($deliveryNote->items->sum('weight'), 2) }}</div>
                <div class="label">{{ __('app.finished_products.total_weight_label') }} ({{ __('app.units.kg') }})</div>
            </div>
            <div class="summary-box">
                <div class="number">{{ number_format($deliveryNote->items->avg('weight'), 2) }}</div>
                <div class="label">{{ __('app.finished_products.average_box_weight') }} ({{ __('app.units.kg') }})</div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>

                    <th>الباركود</th>
                    <th>مواصفات المنتج</th>
                    <th>نوع التغليف</th>
                    <th style="width: 120px;">الوزن (كجم)</th>

                    <th>{{ __('app.finished_products.barcode') }}</th>
                    <th>{{ __('app.finished_products.packaging_type') }}</th>
                    <th>{{ __('app.finished_products.coils_count') }}</th>
                    <th style="width: 120px;">{{ __('app.finished_products.weight') }} ({{ __('app.units.kg') }})</th>

                </tr>
            </thead>
            <tbody>
                @php $totalWeight = 0; @endphp
                @foreach($deliveryNote->items as $index => $item)
                @php $totalWeight += $item->weight; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $item->barcode }}</strong></td>

                    <td>
                        @if(isset($item->materials) && $item->materials->count() > 0)
                            @foreach($item->materials as $material)
                                <span style="display:inline-block; padding:2px 6px; background:#e3f2fd; border-radius:4px; font-size:11px; margin:1px;">
                                    @if($material->color) {{ $material->color }} @endif
                                    @if($material->material_type) - {{ $material->material_type }} @endif
                                    @if($material->wire_size) - {{ $material->wire_size }} @endif
                                </span>
                            @endforeach
                        @else
                            <span style="color:#999;">-</span>
                        @endif
                    </td>
                    <td>{{ $item->packaging_type }}</td>
                    <td>{{ $item->stage4Box->productType->type_name ?? '-' }}</td>
                    <td>{{ $item->stage4Box->boxCoils->count() ?? 0 }}</td>

                    <td><strong>{{ number_format($item->weight, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: left;">{{ __('app.finished_products.total') }}:</td>
                    <td><strong>{{ number_format($totalWeight, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>

        <!-- Notes Section -->
        @if($deliveryNote->notes)
        <div class="notes-section">
            <h3>📝 {{ __('app.finished_products.notes') }}</h3>
            <p>{{ $deliveryNote->notes }}</p>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">
                    <div class="title">{{ __('app.finished_products.warehouse_manager') }}</div>
                    <div class="name">{{ $deliveryNote->recordedBy->name ?? '' }}</div>
                </div>
            </div>

            <div class="signature-box">
                <div class="signature-line">
                    <div class="title">{{ __('app.finished_products.general_manager') }}</div>
                    <div class="name">{{ $deliveryNote->approvedBy->name ?? '' }}</div>
                </div>
            </div>

            <div class="signature-box">
                <div class="signature-line">
                    <div class="title">{{ __('app.finished_products.receiver') }}</div>
                    <div class="name"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>{{ __('app.finished_products.print_date') }}: {{ now()->format('Y-m-d H:i:s') }}</p>
            <p>{{ __('app.finished_products.system_generated_document') }}</p>
        </div>
    </div>

    <script>
        // طباعة تلقائية عند فتح الصفحة
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
