@extends('master')

@section('title', 'سجل التسويات')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="page-header-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <h2 class="mb-1 fw-bold">سجل التسويات المكتملة</h2>
                        <p class="text-muted mb-0">جميع التسويات المنجزة والمتطابقة والمرفوضة</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="filter-card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('manufacturing.warehouses.reconciliation.history') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">المورد</label>
                    <select name="supplier_id" class="form-select custom-select">
                        <option value="">-- اختر المورد --</option>
                        @foreach($suppliers ?? [] as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">من التاريخ</label>
                    <input type="date" name="from_date" class="form-control custom-input" value="{{ request('from_date') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">إلى التاريخ</label>
                    <input type="date" name="to_date" class="form-control custom-input" value="{{ request('to_date') }}">
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn-primary-custom flex-grow-1">
                        <i class="fas fa-filter"></i> فلترة
                    </button>
                    <button type="button" onclick="window.location.href='{{ route('manufacturing.warehouses.reconciliation.history') }}'" class="btn-secondary-custom">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h6 class="stat-label">متطابقة</h6>
                    <h3 class="stat-value">{{ $stats['matched'] ?? 0 }}</h3>
                    <p class="stat-desc">تسويات متطابقة بدون فروقات</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-warning">
                <div class="stat-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="stat-content">
                    <h6 class="stat-label">معدّلة</h6>
                    <h3 class="stat-value">{{ $stats['adjusted'] ?? 0 }}</h3>
                    <p class="stat-desc">تسويات تم تعديلها</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-danger">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h6 class="stat-label">مرفوضة</h6>
                    <h3 class="stat-value">{{ $stats['rejected'] ?? 0 }}</h3>
                    <p class="stat-desc">فواتير مرفوضة</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="stat-content">
                    <h6 class="stat-label">إجمالي</h6>
                    <h3 class="stat-value">{{ ($stats['matched'] ?? 0) + ($stats['adjusted'] ?? 0) + ($stats['rejected'] ?? 0) }}</h3>
                    <p class="stat-desc">جميع التسويات المكتملة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    @if($reconciliations->count() > 0)
        <div class="data-card">
            <div class="data-card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-list"></i>
                    <h5 class="mb-0 fw-bold">السجل الكامل للتسويات</h5>
                </div>
            </div>
            <div class="table-responsive p-3">
                <table class="table custom-table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>رقم الشحنة</th>
                            <th>المورد</th>
                            <th>رقم الفاتورة</th>
                            <th>الوزن الفعلي</th>
                            <th>وزن الفاتورة</th>
                            <th>الفرق</th>
                            <th>النسبة</th>
                            <th>الحالة</th>
                            <th>تاريخ التسوية</th>
                            <th>من قبل</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reconciliations as $reconciliation)
                            <tr>
                                <td>
                                    <strong>#{{ $reconciliation->note_number ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    {{ $reconciliation->supplier->name ?? 'N/A' }}
                                </td>
                                <td>
                                    {{ $reconciliation->purchaseInvoice->invoice_number ?? 'N/A' }}
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ number_format($reconciliation->actual_weight, 2) }} كيلو
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ number_format($reconciliation->invoice_weight, 2) }} كيلو
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $diff = ($reconciliation->actual_weight ?? 0) - ($reconciliation->invoice_weight ?? 0);
                                    @endphp
                                    <span class="badge {{ $diff > 0 ? 'bg-danger' : ($diff < 0 ? 'bg-warning' : 'bg-success') }}">
                                        {{ number_format($diff, 2) }} كيلو
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $percentage = $reconciliation->discrepancy_percentage ?? 0;
                                    @endphp
                                    <span class="badge {{ abs($percentage) <= 1 ? 'bg-success' : (abs($percentage) <= 5 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ number_format($percentage, 2) }}%
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusMap = [
                                            'matched' => ['label' => '✅ متطابقة', 'class' => 'success'],
                                            'adjusted' => ['label' => '🔧 معدّلة', 'class' => 'warning'],
                                            'rejected' => ['label' => '❌ مرفوضة', 'class' => 'danger'],
                                        ];
                                        $status = $statusMap[$reconciliation->reconciliation_status] ?? ['label' => '؟', 'class' => 'secondary'];
                                    @endphp
                                    <span class="badge bg-{{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        {{ $reconciliation->reconciled_at?->format('Y-m-d H:i') ?? 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        {{ $reconciliation->reconciledBy?->name ?? 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('manufacturing.warehouses.reconciliation.show', $reconciliation->id) }}"
                                       class="btn btn-sm btn-info" title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="data-card-footer p-3">
                {{ $reconciliations->links() }}
            </div>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <h5 class="mb-2">لا توجد تسويات مكتملة</h5>
            <p class="text-muted mb-0">جميع التسويات المعلقة لم تُنجز بعد</p>
        </div>
    @endif
</div>

@endsection

@push('styles')
<style>
    /* ألوان الشعار */
    :root {
        --primary-blue: #0066B3;
        --secondary-gray: #4A5568;
        --light-gray: #E2E8F0;
        --success-green: #27ae60;
        --warning-orange: #f39c12;
        --danger-red: #e74c3c;
    }

    /* Page Header Card */
    .page-header-card {
        background: linear-gradient(135deg, var(--primary-blue) 0%, #0052a3 100%);
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 102, 179, 0.15);
        margin-bottom: 2rem;
    }

    .page-header-card h2,
    .page-header-card p {
        color: white;
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
        color: white;
    }

    .btn-back {
        background: white;
        color: var(--primary-blue);
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-back:hover {
        background: var(--light-gray);
        color: var(--primary-blue);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Filter Card */
    .filter-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--light-gray);
    }

    .custom-select,
    .custom-input {
        border: 2px solid var(--light-gray);
        border-radius: 8px;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }

    .custom-select:focus,
    .custom-input:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.2rem rgba(0, 102, 179, 0.15);
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary-blue) 0%, #0052a3 100%);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 102, 179, 0.3);
    }

    .btn-secondary-custom {
        background: var(--secondary-gray);
        color: white;
        border: none;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-secondary-custom:hover {
        background: #2D3748;
        transform: translateY(-2px);
    }

    /* Statistics Cards */
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--light-gray);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .stat-primary .stat-icon {
        background: linear-gradient(135deg, var(--primary-blue) 0%, #0052a3 100%);
        color: white;
    }

    .stat-success .stat-icon {
        background: linear-gradient(135deg, var(--success-green) 0%, #229954 100%);
        color: white;
    }

    .stat-warning .stat-icon {
        background: linear-gradient(135deg, var(--warning-orange) 0%, #e67e22 100%);
        color: white;
    }

    .stat-danger .stat-icon {
        background: linear-gradient(135deg, var(--danger-red) 0%, #c0392b 100%);
        color: white;
    }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--secondary-gray);
        margin-bottom: 0.25rem;
        font-weight: 600;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: var(--secondary-gray);
        margin-bottom: 0.25rem;
    }

    .stat-desc {
        font-size: 0.75rem;
        color: #718096;
        margin: 0;
    }

    /* Data Card */
    .data-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--light-gray);
        overflow: hidden;
    }

    .data-card-header {
        background: linear-gradient(135deg, var(--secondary-gray) 0%, #2D3748 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        border-bottom: none;
    }

    .data-card-header i {
        font-size: 1.25rem;
    }

    .data-card-footer {
        background: #F7FAFC;
        border-top: 1px solid var(--light-gray);
    }

    /* Custom Table */
    .custom-table {
        margin: 0;
    }

    .custom-table thead th {
        background: #F7FAFC;
        color: var(--secondary-gray);
        font-weight: 700;
        font-size: 0.875rem;
        text-transform: uppercase;
        padding: 1rem;
        border-bottom: 2px solid var(--light-gray);
    }

    .custom-table tbody td {
        padding: 1rem;
        vertical-align: middle;
        color: var(--secondary-gray);
        border-bottom: 1px solid var(--light-gray);
    }

    .custom-table tbody tr {
        transition: all 0.2s ease;
    }

    .custom-table tbody tr:hover {
        background: #F7FAFC;
        transform: scale(1.01);
    }

    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Badges */
    .badge {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
        font-weight: 600;
        border-radius: 6px;
    }

    .bg-success {
        background: var(--success-green) !important;
    }

    .bg-primary {
        background: var(--primary-blue) !important;
    }

    .bg-warning {
        background: var(--warning-orange) !important;
    }

    .bg-danger {
        background: var(--danger-red) !important;
    }

    /* Action Buttons */
    .btn-sm {
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-sm.btn-info {
        background: var(--primary-blue);
        color: white;
    }

    .btn-sm.btn-info:hover {
        background: #0052a3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 102, 179, 0.3);
    }

    /* Empty State */
    .empty-state {
        background: white;
        border-radius: 12px;
        padding: 4rem 2rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--light-gray);
    }

    .empty-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--light-gray) 0%, #CBD5E0 100%);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: var(--secondary-gray);
        margin-bottom: 1.5rem;
    }

    .empty-state h5 {
        color: var(--secondary-gray);
        font-weight: 700;
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

        .stat-value {
            font-size: 1.5rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
    }
</style>
@endpush
