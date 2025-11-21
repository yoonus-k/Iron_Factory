<div class="um-reconciliation-item" style="background: {{ $item->reconciliation_status === 'discrepancy' ? '#FFF9E6' : ($item->reconciliation_status === 'rejected' ? '#F8D7DA' : '#E6F2FF') }}; border-right: 4px solid {{ $item->reconciliation_status === 'discrepancy' ? '#F59E0B' : ($item->reconciliation_status === 'rejected' ? '#EF4444' : ($item->reconciliation_status === 'matched' ? '#10B981' : '#0066CC')) }};">
    <div class="row align-items-center mb-3">
        <div class="col-md-2">
            <div class="um-info-box">
                <span class="um-info-label">
                    <i class="feather icon-package"></i>
                    الشحنة:
                </span>
                <strong class="um-info-value">#{{ $item->note_number ?? $item->id }}</strong>
            </div>
        </div>
        <div class="col-md-2">
            <div class="um-info-box">
                <span class="um-info-label">
                    <i class="feather icon-user"></i>
                    المورد:
                </span>
                <strong class="um-info-value">{{ $item->supplier->name }}</strong>
            </div>
        </div>
        <div class="col-md-2">
            <div class="um-info-box">
                <span class="um-info-label">
                    <i class="feather icon-file-text"></i>
                    الفاتورة:
                </span>
                <strong class="um-info-value">{{ $item->purchaseInvoice->invoice_number }}</strong>
            </div>
        </div>
        <div class="col-md-2">
            <div class="um-info-box">
                <span class="um-info-label">
                    <i class="feather icon-calendar"></i>
                    التاريخ:
                </span>
                <strong class="um-info-value">{{ $item->created_at->format('Y-m-d') }}</strong>
            </div>
        </div>
        <div class="col-md-2">
            <div class="um-info-box">
                <span class="um-info-label">
                    <i class="feather icon-info"></i>
                    الحالة:
                </span>
                <span class="um-badge {{ $item->reconciliation_status === 'discrepancy' ? 'um-badge-warning' : ($item->reconciliation_status === 'rejected' ? 'um-badge-danger' : ($item->reconciliation_status === 'matched' ? 'um-badge-success' : 'um-badge-info')) }}">
                    @switch($item->reconciliation_status)
                        @case('pending')
                            معلقة
                        @break
                        @case('discrepancy')
                            فروقات
                        @break
                        @case('matched')
                            متطابقة
                        @break
                        @case('adjusted')
                            مسوية
                        @break
                        @case('rejected')
                            مرفوضة
                        @break
                        @default
                            {{ $item->reconciliation_status }}
                    @endswitch
                </span>
            </div>
        </div>
        <div class="col-md-2 text-end">
            <div class="um-actions">
                <a href="{{ route('manufacturing.warehouses.reconciliation.show', $item) }}" class="um-btn-action um-btn-view" title="عرض التفاصيل">
                    <i class="feather icon-eye"></i>
                </a>
                @if($item->reconciliation_status === 'discrepancy' || $item->reconciliation_status === 'pending')
                    <a href="{{ route('manufacturing.warehouses.reconciliation.link-invoice.edit', $item->id) }}" class="um-btn-action um-btn-edit" title="تعديل">
                        <i class="feather icon-edit-2"></i>
                    </a>
                @endif
                @if($item->reconciliation_status !== 'matched' && $item->reconciliation_status !== 'adjusted')
                    <form action="{{ route('manufacturing.warehouses.reconciliation.link-invoice.delete', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل تريد حذف هذه التسوية؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="um-btn-action um-btn-delete" title="حذف" style="border: none; background: transparent; cursor: pointer;">
                            <i class="feather icon-trash-2"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- المقارنة -->
    @if ($item->actual_weight && $item->invoice_weight)
        <div class="um-comparison-table">
            <table class="um-table">
                <thead>
                    <tr>
                        <th>البيان</th>
                        <th class="text-end">الفعلي (الميزان)</th>
                        <th class="text-end">الفاتورة</th>
                        <th class="text-end">الفرق</th>
                        <th class="text-end">النسبة</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>الوزن/الكمية (وحدة)</strong></td>
                        <td class="text-end">
                            <span class="um-badge um-badge-info">{{ number_format($item->actual_weight, 2) }}</span>
                        </td>
                        <td class="text-end">
                            <span class="um-badge um-badge-secondary">{{ number_format($item->invoice_weight, 2) }}</span>
                        </td>
                        <td class="text-end">
                            @php
                                $discrepancy = $item->actual_weight - $item->invoice_weight;
                            @endphp
                            <span class="um-badge {{ $discrepancy > 0 ? 'um-badge-danger' : 'um-badge-success' }}">
                                {{ $discrepancy > 0 ? '+' : '' }}{{ number_format($discrepancy, 2) }}
                            </span>
                        </td>
                        <td class="text-end">
                            @php
                                $percentage = $item->invoice_weight > 0 ? (($discrepancy / $item->invoice_weight) * 100) : 0;
                            @endphp
                            <span class="um-badge {{ abs($percentage) > 5 ? 'um-badge-danger' : (abs($percentage) > 1 ? 'um-badge-warning' : 'um-badge-success') }}">
                                {{ number_format($percentage, 2) }}%
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    <!-- الملاحظات -->
    @if ($item->reconciliation_notes)
        <div class="um-notes-section mt-3">
            <strong>📝 ملاحظات:</strong>
            <p class="text-muted mb-0">{{ $item->reconciliation_notes }}</p>
        </div>
    @endif
</div>
