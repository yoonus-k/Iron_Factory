@extends('master')

@section('title', 'لوحة التسوية')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/warehouses/reconciliation.css') }}">
@endpush

@section('content')
<div class="um-content-wrapper">
    <!-- Header Section -->
    <div class="um-header-section">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="um-page-title">
                    <i class="feather icon-git-merge"></i>
                    لوحة تسوية البضاعة والفواتير
                </h1>
                <nav class="um-breadcrumb-nav">
                    <span>
                        <i class="feather icon-home"></i> لوحة التحكم
                    </span>
                    <i class="feather icon-chevron-left"></i>
                    <span>المستودع</span>
                    <i class="feather icon-chevron-left"></i>
                    <span>التسوية</span>
                </nav>
            </div>
            <div class="col-auto">
                <a href="{{ route('manufacturing.warehouses.reconciliation.history') }}" class="um-btn um-btn-outline">
                    <i class="feather icon-clock"></i>
                    السجل
                </a>
                <a href="{{ route('manufacturing.warehouses.reconciliation.supplier-report') }}" class="um-btn um-btn-primary">
                    <i class="feather icon-bar-chart-2"></i>
                    تقرير الموردين
                </a>
            </div>
        </div>
    </div>

    <!-- Success and Error Messages -->
    @if (session('success'))
        <div class="um-alert-custom um-alert-success" role="alert">
            <i class="feather icon-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="um-alert-custom um-alert-error" role="alert">
            <i class="feather icon-x-circle"></i>
            {{ session('error') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif


    <!-- Filters Section -->
    <div class="um-filters-section">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">المورد:</label>
                <div class="input-wrapper">
                    <select name="supplier_id" class="um-form-control">
                        <option value="">-- جميع الموردين --</option>
                        @foreach (\App\Models\Supplier::where('is_active', true)->get() as $supplier)
                            <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">من التاريخ:</label>
                <div class="input-wrapper">
                    <input type="date" name="from_date" class="um-form-control" value="{{ request('from_date') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">إلى التاريخ:</label>
                <div class="input-wrapper">
                    <input type="date" name="to_date" class="um-form-control" value="{{ request('to_date') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="um-btn um-btn-primary w-100">
                    <i class="feather icon-search"></i>
                    بحث
                </button>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="um-btn um-btn-outline w-100">
                    <i class="feather icon-rotate-ccw"></i>
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- علامات التبويب للتسويات -->
    <div class="um-main-card">
        <div class="um-card-header">
            <h4 class="um-card-title">
                <i class="feather icon-git-merge"></i>
                جميع التسويات
            </h4>
        </div>
        <div class="card-body">
            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs mb-4" role="tablist" style="border-bottom: 2px solid #E2E8F0;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                        <i class="feather icon-clock"></i>
                        معلقة
                        <span class="badge bg-secondary ms-2"></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="discrepancy-tab" data-bs-toggle="tab" data-bs-target="#discrepancy" type="button" role="tab">
                        <i class="feather icon-alert-triangle"></i>
                        فروقات
                        <span class="badge bg-warning ms-2">{{ $stats['total_discrepancy'] }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="matched-tab" data-bs-toggle="tab" data-bs-target="#matched" type="button" role="tab">
                        <i class="feather icon-check-circle"></i>
                        متطابقة
                        <span class="badge bg-success ms-2">{{ $stats['total_matched'] }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="adjusted-tab" data-bs-toggle="tab" data-bs-target="#adjusted" type="button" role="tab">
                        <i class="feather icon-tool"></i>
                        مسوية
                        <span class="badge bg-info ms-2">{{ $stats['total_adjusted'] }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                        <i class="feather icon-x-circle"></i>
                        مرفوضة
                        <span class="badge bg-danger ms-2">{{ $stats['total_rejected'] }}</span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Tab: المعلقة -->
                <div class="tab-pane fade show active" id="pending" role="tabpanel">
                    @if ($pending->count() > 0)
                        <div class="um-reconciliation-table-wrapper">
                            <table class="um-main-table">
                                <thead>
                                    <tr>
                                        <th>الشحنة</th>
                                        <th>المورد</th>
                                        <th>الفاتورة</th>
                                        <th>الحالة</th>
                                        <th class="text-end">الفعلي (الميزان)</th>
                                        <th class="text-end">الفاتورة</th>
                                        <th class="text-end">الفرق</th>
                                        <th class="text-end">النسبة</th>
                                        <th class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pending as $item)
                                        <tr class="um-table-row {{ $item->reconciliation_status === 'discrepancy' ? 'row-discrepancy' : 'row-pending' }}">
                                            <td><strong>#{{ $item->note_number ?? $item->id }}</strong></td>
                                            <td>{{ $item->supplier->name }}</td>
                                            <td>{{ $item->purchaseInvoice->invoice_number }}</td>
                                            <td>
                                                <span class="um-badge {{ $item->reconciliation_status === 'discrepancy' ? 'um-badge-warning' : 'um-badge-info' }}">
                                                    {{ $item->reconciliation_status }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                @if ($item->actual_weight)
                                                    <span class="um-badge um-badge-info">{{ number_format($item->actual_weight, 2) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($item->invoice_weight)
                                                    <span class="um-badge um-badge-secondary">{{ number_format($item->invoice_weight, 2) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($item->weight_discrepancy)
                                                    <span class="um-badge {{ $item->weight_discrepancy > 0 ? 'um-badge-danger' : 'um-badge-success' }}">
                                                        {{ $item->weight_discrepancy > 0 ? '+' : '' }}{{ number_format($item->weight_discrepancy, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if (isset($item->discrepancy_percentage))
                                                    <span class="um-badge {{ abs($item->discrepancy_percentage) > 5 ? 'um-badge-danger' : 'um-badge-warning' }}">
                                                        {{ number_format($item->discrepancy_percentage, 2) }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="um-actions">
                                                    <a href="{{ route('manufacturing.warehouses.reconciliation.show', $item) }}" class="um-btn-action um-btn-view" title="عرض التفاصيل">
                                                        <i class="feather icon-eye"></i>
                                                    </a>
                                                    <a href="{{ route('manufacturing.warehouses.reconciliation.link-invoice.edit', $item->id) }}" class="um-btn-action um-btn-edit" title="تعديل">
                                                        <i class="feather icon-edit-2"></i>
                                                    </a>
                                                    <form action="{{ route('manufacturing.warehouses.reconciliation.link-invoice.delete', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل تريد حذف هذه التسوية؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="um-btn-action um-btn-delete" title="حذف">
                                                            <i class="feather icon-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                <div class="um-pagination-section">
                    {{ $pending->links() }}
                </div>
                        </div>
                    @else
                        <div class="um-alert-custom um-alert-info">
                            <i class="feather icon-info"></i>
                            لا توجد تسويات معلقة
                        </div>
                    @endif
                </div>

                <!-- Tab: الفروقات -->
                <div class="tab-pane fade" id="discrepancy" role="tabpanel">
                    @php
                        $discrepancyItems = $pending->filter(fn($item) => $item->reconciliation_status === 'discrepancy');
                    @endphp
                    @if ($discrepancyItems->count() > 0)
                        <div class="um-reconciliation-list">
                            @foreach ($discrepancyItems as $item)
                                @include('manufacturing::warehouses.reconciliation.partials.reconciliation-item', ['item' => $item])
                            @endforeach
                        </div>
                    @else
                        <div class="um-alert-custom um-alert-success">
                            <i class="feather icon-check-circle"></i>
                            لا توجد فروقات بحاجة إلى مراجعة
                        </div>
                    @endif
                </div>

                <!-- Tab: المتطابقة -->
                <div class="tab-pane fade" id="matched" role="tabpanel">
                    @php
                        $matchedItems = \App\Models\DeliveryNote::where('type', 'incoming')
                            ->where('purchase_invoice_id', '!=', null)
                            ->where('reconciliation_status', 'matched')
                            ->with(['supplier', 'purchaseInvoice'])
                            ->orderBy('created_at', 'desc')
                            ->get();
                    @endphp
                    @if ($matchedItems->count() > 0)
                        <div class="um-reconciliation-list">
                            @foreach ($matchedItems as $item)
                                @include('manufacturing::warehouses.reconciliation.partials.reconciliation-item', ['item' => $item])
                            @endforeach
                        </div>
                    @else
                        <div class="um-alert-custom um-alert-info">
                            <i class="feather icon-info"></i>
                            لا توجد تسويات متطابقة
                        </div>
                    @endif
                </div>

                <!-- Tab: المسوية -->
                <div class="tab-pane fade" id="adjusted" role="tabpanel">
                    @php
                        $adjustedItems = \App\Models\DeliveryNote::where('type', 'incoming')
                            ->where('purchase_invoice_id', '!=', null)
                            ->where('reconciliation_status', 'adjusted')
                            ->with(['supplier', 'purchaseInvoice'])
                            ->orderBy('created_at', 'desc')
                            ->get();
                    @endphp
                    @if ($adjustedItems->count() > 0)
                        <div class="um-reconciliation-list">
                            @foreach ($adjustedItems as $item)
                                @include('manufacturing::warehouses.reconciliation.partials.reconciliation-item', ['item' => $item])
                            @endforeach
                        </div>
                    @else
                        <div class="um-alert-custom um-alert-info">
                            <i class="feather icon-info"></i>
                            لا توجد تسويات مسوية
                        </div>
                    @endif
                </div>

                <!-- Tab: المرفوضة -->
                <div class="tab-pane fade" id="rejected" role="tabpanel">
                    @php
                        $rejectedItems = \App\Models\DeliveryNote::where('type', 'incoming')
                            ->where('purchase_invoice_id', '!=', null)
                            ->where('reconciliation_status', 'rejected')
                            ->with(['supplier', 'purchaseInvoice'])
                            ->orderBy('created_at', 'desc')
                            ->get();
                    @endphp
                    @if ($rejectedItems->count() > 0)
                        <div class="um-reconciliation-list">
                            @foreach ($rejectedItems as $item)
                                @include('manufacturing::warehouses.reconciliation.partials.reconciliation-item', ['item' => $item])
                            @endforeach
                        </div>
                    @else
                        <div class="um-alert-custom um-alert-info">
                            <i class="feather icon-info"></i>
                            لا توجد تسويات مرفوضة
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss alerts after 5 seconds
        const alerts = document.querySelectorAll('.um-alert-custom');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.3s';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 300);
            }, 5000);
        });
    });
</script>
@endsection
