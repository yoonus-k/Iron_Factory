<div class="um-reconciliation-item" style="background: {{ $item->reconciliation_status === 'discrepancy' ? '#FFF9E6' : ($item->reconciliation_status === 'rejected' ? '#F8D7DA' : '#E6F2FF') }}; border-right: 4px solid {{ $item->reconciliation_status === 'discrepancy' ? '#F59E0B' : ($item->reconciliation_status === 'rejected' ? '#EF4444' : ($item->reconciliation_status === 'matched' ? '#10B981' : '#0066CC')) }};">
    <div class="row align-items-center mb-3">
        <div class="col-md-2">
            <div class="um-info-box">
                <span class="um-info-label">
                    <i class="feather icon-package"></i>
                    {{ __('reconciliation.shipment') }}:
                </span>
                <strong class="um-info-value">#{{ $item->note_number ?? $item->id }}</strong>
            </div>
        </div>
        <div class="col-md-2">
            <div class="um-info-box">
                <span class="um-info-label">
                    <i class="feather icon-user"></i>
                    {{ __('reconciliation.supplier') }}:
                </span>
                <strong class="um-info-value">{{ $item->supplier->name ?? "---" }}</strong>
            </div>
        </div>
        <div class="col-md-2">
            <div class="um-info-box">
                <span class="um-info-label">
                    <i class="feather icon-file-text"></i>
                    {{ __('reconciliation.invoice') }}:
                </span>
                <strong class="um-info-value">{{ $item->purchaseInvoice->invoice_number }}</strong>
            </div>
        </div>
        <div class="col-md-2">
            <div class="um-info-box">
                <span class="um-info-label">
                    <i class="feather icon-calendar"></i>
                    {{ __('reconciliation.date') }}:
                </span>
                <strong class="um-info-value">{{ $item->created_at->format('Y-m-d') }}</strong>
            </div>
        </div>
        <div class="col-md-2">
            <div class="um-info-box">
                <span class="um-info-label">
                    <i class="feather icon-info"></i>
                    {{ __('reconciliation.status') }}:
                </span>
                <span class="um-badge {{ $item->reconciliation_status === 'discrepancy' ? 'um-badge-warning' : ($item->reconciliation_status === 'rejected' ? 'um-badge-danger' : ($item->reconciliation_status === 'matched' ? 'um-badge-success' : 'um-badge-info')) }}">
                    @switch($item->reconciliation_status)
                        @case('pending')
                            {{ __('reconciliation.pending') }}
                        @break
                        @case('discrepancy')
                            {{ __('reconciliation.discrepancies') }}
                        @break
                        @case('matched')
                            {{ __('reconciliation.matched') }}
                        @break
                        @case('adjusted')
                            {{ __('reconciliation.adjusted') }}
                        @break
                        @case('rejected')
                            {{ __('reconciliation.rejected') }}
                        @break
                        @default
                            {{ $item->reconciliation_status }}
                    @endswitch
                </span>
            </div>
        </div>
        <div class="col-md-2 text-end">
            <div class="um-actions">
                <a href="{{ route('manufacturing.warehouses.reconciliation.show', $item) }}" class="um-btn-action um-btn-view" title="{{ __('reconciliation.view_details') }}">
                    <i class="feather icon-eye"></i>
                </a>
                <a href="{{ route('manufacturing.warehouses.reconciliation.link-invoice.edit', $item->id) }}" class="um-btn-action um-btn-edit" title="{{ __('reconciliation.edit') }}">
                    <i class="feather icon-edit-2"></i>
                </a>
                @if($item->reconciliation_status !== 'matched' && $item->reconciliation_status !== 'adjusted')
                    <form action="{{ route('manufacturing.warehouses.reconciliation.link-invoice.delete', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('{{ __('reconciliation.confirm_delete') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="um-btn-action um-btn-delete" title="{{ __('reconciliation.delete') }}">
                            <i class="feather icon-trash-2"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- {{ __('reconciliation.comparison') }} -->
    @if ($item->actual_weight && $item->invoice_weight)
        <div class="um-comparison-table">
            <table class="um-table">
                <thead>
                    <tr>
                        <th>{{ __('reconciliation.statement') }}</th>
                        <th class="text-end">{{ __('reconciliation.actual_weight') }} ({{ __('reconciliation.scale') }})</th>
                        <th class="text-end">{{ __('reconciliation.invoice') }}</th>
                        <th class="text-end">{{ __('reconciliation.difference') }}</th>
                        <th class="text-end">{{ __('reconciliation.percentage') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>{{ __('reconciliation.weight_quantity') }} ({{ __('reconciliation.unit') }})</strong></td>
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

    <!-- {{ __('reconciliation.notes') }} -->
    @if ($item->reconciliation_notes)
        <div class="um-notes-section mt-3">
            <strong>📝 {{ __('reconciliation.notes') }}:</strong>
            <p class="text-muted mb-0">{{ $item->reconciliation_notes }}</p>
        </div>
    @endif
</div>
