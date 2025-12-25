@extends('master')

@section('title', __('reconciliation.reconciliation_dashboard'))

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="page-header-card mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-0">{{ __('reconciliation.reconciliation_dashboard') }}</h1>
                        <p class="text-white-50 mb-0 mt-1">{{ __('reconciliation.reconciliation_management') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('manufacturing.warehouses.reconciliation.link-invoice') }}" class="btn-action-primary">
                        <i class="fas fa-link"></i>
                        {{ __('reconciliation.link_invoice') }}
                    </a>
                    @if(Route::has('manufacturing.warehouses.reconciliation.history'))
                    <a href="{{ route('manufacturing.warehouses.reconciliation.history') }}" class="btn-action-secondary">
                        <i class="fas fa-history"></i>
                        {{ __('reconciliation.reconciliation_history') }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Success and Error Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>{{ __('reconciliation.success') }}!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>{{ __('reconciliation.error') }}!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- إحصائيات سريعة -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stats-card stats-pending">
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['total_pending'] + $stats['total_discrepancy'] }}</h3>
                    <p>{{ __('reconciliation.pending_reconciliations') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-discrepancy">
                <div class="stats-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $pending->count() }}</h3>
                    <p>{{ __('reconciliation.total_weight_variation') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-matched">
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['total_matched'] }}</h3>
                    <p>{{ __('reconciliation.matched') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-adjusted">
                <div class="stats-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['total_adjusted'] }}</h3>
                    <p>{{ __('reconciliation.reconciled') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filter-card mb-4">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">{{ __('reconciliation.supplier') }}:</label>
                <select name="supplier_id" class="form-select">
                    <option value="">-- {{ __('reconciliation.supplier') }} --</option>
                    @foreach (\App\Models\Supplier::where('is_active', true)->get() as $supplier)
                        <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('reconciliation.date') }}:</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('reconciliation.date') }}:</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> {{ __('reconciliation.search') }}
                </button>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-redo"></i> {{ __('reconciliation.reset_filters') }}
                </a>
            </div>
        </form>
    </div>

    <!-- علامات التبويب للتسويات -->
    <div class="main-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list"></i>
                جميع التسويات
            </h5>
        </div>
        <div class="card-body">
            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                        <i class="fas fa-clock"></i>
                        معلقة
                        <span class="badge rounded-pill bg-secondary ms-2">{{ $stats['total_pending'] + $stats['total_discrepancy'] }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="matched-tab" data-bs-toggle="tab" data-bs-target="#matched" type="button" role="tab">
                        <i class="fas fa-check-circle"></i>
                        متطابقة
                        <span class="badge rounded-pill bg-success ms-2">{{ $stats['total_matched'] }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="adjusted-tab" data-bs-toggle="tab" data-bs-target="#adjusted" type="button" role="tab">
                        <i class="fas fa-tools"></i>
                        مسوّاة
                        <span class="badge rounded-pill bg-info ms-2">{{ $stats['total_adjusted'] }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                        <i class="fas fa-times-circle"></i>
                        مرفوضة
                        <span class="badge rounded-pill bg-danger ms-2">{{ $stats['total_rejected'] }}</span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Tab: المعلقة -->
                <div class="tab-pane fade show active" id="pending" role="tabpanel">
                    @if ($stats['total_pending'] + $stats['total_discrepancy'] > 0 && $pending->count() === 0)
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-filter me-2"></i>
                            <strong>توجد {{ $stats['total_pending'] + $stats['total_discrepancy'] }} تسوية معلقة</strong> ولكن لا تطابق الفلاتر الحالية.
                            <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-redo"></i> إعادة تعيين الفلاتر
                            </a>
                        </div>
                    @elseif ($pending->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>رقم الأذن</th>
                                        <th>المورد</th>
                                        <th>رقم الفاتورة</th>
                                        <th>التاريخ</th>
                                        <th class="text-center">الوزن الفعلي</th>
                                        <th class="text-center">وزن الفاتورة</th>
                                        <th class="text-center">الفرق</th>
                                        <th class="text-center">النسبة %</th>
                                        <th class="text-center">الحالة</th>
                                        <th class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pending as $item)
                                        @php
                                            // أولاً: نجرب الحقول المباشرة
                                            $actualWeight = $item->actual_weight ?? $item->weight_from_scale ?? $item->delivered_weight ?? $item->quantity ?? 0;

                                            // إذا كانت كلها صفر، نجرب من العلاقات
                                            if ($actualWeight == 0 && isset($item->items) && $item->items->count() > 0) {
                                                $actualWeight = $item->items->sum('actual_weight') ?: $item->items->sum('quantity');
                                            }

                                            $invoiceWeight = $item->invoice_weight ?? 0;
                                            $discrepancy = $actualWeight - $invoiceWeight;
                                            $discrepancyPercentage = $invoiceWeight > 0 ? (($discrepancy / $invoiceWeight) * 100) : 0;
                                            $isInOurFavor = $discrepancy < 0;

                                            // رسالة تنبيه للديباج
                                            $weightSource = 'غير محدد';
                                            if ($item->actual_weight > 0) $weightSource = 'actual_weight';
                                            elseif ($item->weight_from_scale > 0) $weightSource = 'weight_from_scale';
                                            elseif ($item->delivered_weight > 0) $weightSource = 'delivered_weight';
                                            elseif ($item->quantity > 0) $weightSource = 'quantity';
                                        @endphp
                                        <tr>
                                            <td><strong>#{{ $item->note_number ?? $item->id }}</strong></td>
                                            <td>{{ $item->supplier->name ?? 'غير محدد' }}</td>
                                            <td>{{ $item->purchaseInvoice->invoice_number ?? '-' }}</td>
                                            <td>{{ $item->created_at ? $item->created_at->format('Y-m-d') : '-' }}</td>
                                            <td class="text-center">
                                                @if ($actualWeight > 0)
                                                    <span class="badge bg-success">{{ number_format($actualWeight, 2) }} كجم</span>
                                                    <small class="d-block text-muted" style="font-size: 0.7rem;">{{ $weightSource }}</small>
                                                @else
                                                    <span class="badge bg-warning text-dark">لا يوجد وزن</span>
                                                    <small class="d-block text-danger" style="font-size: 0.7rem;">⚠ الحقول فارغة</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ number_format($invoiceWeight, 2) }} كجم</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $isInOurFavor ? 'success' : 'danger' }}">
                                                    {{ $discrepancy >= 0 ? '+' : '' }}{{ number_format($discrepancy, 2) }} كجم
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ abs($discrepancyPercentage) > 5 ? 'danger' : (abs($discrepancyPercentage) > 1 ? 'warning' : 'success') }}">
                                                    {{ $discrepancy >= 0 ? '+' : '' }}{{ number_format($discrepancyPercentage, 2) }}%
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if (abs($discrepancyPercentage) > 5)
                                                    <span class="badge bg-danger">فرق كبير</span>
                                                @elseif (abs($discrepancyPercentage) > 1)
                                                    <span class="badge bg-warning">فرق مقبول</span>
                                                @else
                                                    <span class="badge bg-secondary">معلق</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('manufacturing.warehouses.reconciliation.show', $item) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $pending->links() }}
                        </div>
                    @else
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>ممتاز!</strong> لا توجد تسويات معلقة تحتاج مراجعة.
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
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>رقم الأذن</th>
                                        <th>المورد</th>
                                        <th>رقم الفاتورة</th>
                                        <th>التاريخ</th>
                                        <th class="text-center">الوزن الفعلي</th>
                                        <th class="text-center">وزن الفاتورة</th>
                                        <th class="text-center">الفرق</th>
                                        <th class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($matchedItems as $item)
                                        @php
                                            $actualWeight = $item->actual_weight ?? $item->weight_from_scale ?? $item->delivered_weight ?? 0;
                                            $invoiceWeight = $item->invoice_weight ?? 0;
                                            $discrepancy = $actualWeight - $invoiceWeight;
                                        @endphp
                                        <tr>
                                            <td><strong>#{{ $item->note_number ?? $item->id }}</strong></td>
                                            <td>{{ $item->supplier->name ?? 'غير محدد' }}</td>
                                            <td>{{ $item->purchaseInvoice->invoice_number ?? '-' }}</td>
                                            <td>{{ $item->created_at ? $item->created_at->format('Y-m-d') : '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ number_format($actualWeight, 2) }} كجم</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ number_format($invoiceWeight, 2) }} كجم</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> متطابق
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('manufacturing.warehouses.reconciliation.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> عرض
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
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
                            ->limit(50)
                            ->get();
                    @endphp
                    @if ($adjustedItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>رقم الأذن</th>
                                        <th>المورد</th>
                                        <th>رقم الفاتورة</th>
                                        <th>التاريخ</th>
                                        <th class="text-center">الحالة</th>
                                        <th class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($adjustedItems as $item)
                                        <tr>
                                            <td><strong>#{{ $item->note_number ?? $item->id }}</strong></td>
                                            <td>{{ $item->supplier->name ?? 'غير محدد' }}</td>
                                            <td>{{ $item->purchaseInvoice->invoice_number ?? '-' }}</td>
                                            <td>{{ $item->created_at ? $item->created_at->format('Y-m-d') : '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info">
                                                    <i class="fas fa-tools"></i> مسوّاة
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('manufacturing.warehouses.reconciliation.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> عرض
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            لا توجد تسويات مسوّاة
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
                            ->limit(50)
                            ->get();
                    @endphp
                    @if ($rejectedItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>رقم الأذن</th>
                                        <th>المورد</th>
                                        <th>رقم الفاتورة</th>
                                        <th>التاريخ</th>
                                        <th class="text-center">الحالة</th>
                                        <th class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rejectedItems as $item)
                                        <tr>
                                            <td><strong>#{{ $item->note_number ?? $item->id }}</strong></td>
                                            <td>{{ $item->supplier->name ?? 'غير محدد' }}</td>
                                            <td>{{ $item->purchaseInvoice->invoice_number ?? '-' }}</td>
                                            <td>{{ $item->created_at ? $item->created_at->format('Y-m-d') : '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle"></i> مرفوضة
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('manufacturing.warehouses.reconciliation.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> عرض
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            لا توجد تسويات مرفوضة
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ألوان المشروع */
    :root {
        --primary-blue: #0066B3;
        --secondary-gray: #4A5568;
        --light-gray: #E2E8F0;
        --success-green: #27ae60;
        --warning-orange: #f39c12;
        --danger-red: #e74c3c;
    }

    /* Page Header */
    .page-header-card {
        background: linear-gradient(135deg, var(--primary-blue) 0%, #0052a3 100%);
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 102, 179, 0.15);
        color: white;
    }

    .page-title {
        color: white;
        font-weight: 700;
        font-size: 1.75rem;
    }

    .header-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    /* Action Buttons */
    .btn-action-primary {
        background: white;
        color: var(--primary-blue);
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-action-primary:hover {
        background: var(--light-gray);
        color: var(--primary-blue);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-action-secondary {
        background: transparent;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        border: 2px solid white;
    }

    .btn-action-secondary:hover {
        background: white;
        color: var(--primary-blue);
    }

    /* Stats Cards */
    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid var(--light-gray);
    }

    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stats-pending .stats-icon {
        background: rgba(74, 85, 104, 0.1);
        color: var(--secondary-gray);
    }

    .stats-discrepancy .stats-icon {
        background: rgba(243, 156, 18, 0.1);
        color: var(--warning-orange);
    }

    .stats-matched .stats-icon {
        background: rgba(39, 174, 96, 0.1);
        color: var(--success-green);
    }

    .stats-adjusted .stats-icon {
        background: rgba(0, 102, 179, 0.1);
        color: var(--primary-blue);
    }

    .stats-content h3 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        color: var(--secondary-gray);
    }

    .stats-content p {
        margin: 0;
        color: #718096;
        font-size: 0.875rem;
        font-weight: 600;
    }

    /* Filter Card */
    .filter-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--light-gray);
    }

    /* Main Card */
    .main-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--light-gray);
        overflow: hidden;
    }

    .main-card .card-header {
        background: linear-gradient(135deg, var(--primary-blue) 0%, #0052a3 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        border-bottom: none;
    }

    .main-card .card-header h5 {
        margin: 0;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Nav Tabs */
    .nav-tabs {
        border-bottom: 2px solid var(--light-gray);
    }

    .nav-tabs .nav-link {
        color: var(--secondary-gray);
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        border-bottom-color: var(--primary-blue);
        color: var(--primary-blue);
    }

    .nav-tabs .nav-link.active {
        color: var(--primary-blue);
        border-bottom-color: var(--primary-blue);
        background: transparent;
    }

    .nav-tabs .nav-link .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    /* Table Styles */
    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background: #F7FAFC;
        color: var(--secondary-gray);
        font-weight: 700;
        font-size: 0.875rem;
        padding: 1rem;
        border-bottom: 2px solid var(--light-gray);
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        color: var(--secondary-gray);
        border-bottom: 1px solid var(--light-gray);
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Badge Styles */
    .badge {
        font-weight: 600;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header-card {
            padding: 1.5rem;
        }

        .header-icon {
            width: 50px;
            height: 50px;
            font-size: 24px;
        }

        .page-title {
            font-size: 1.25rem;
        }

        .stats-card {
            margin-bottom: 1rem;
        }

        .btn-action-primary,
        .btn-action-secondary {
            width: 100%;
            justify-content: center;
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush
