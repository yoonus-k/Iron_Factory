@extends('manufacturing::layouts.app')

@section('title', 'سجل التسويات')

@section('content')
<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="fas fa-history"></i> سجل التسويات المكتملة
            </h2>
            <small class="text-muted">جميع التسويات المنجزة والمتطابقة والمرفوضة</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> العودة
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('manufacturing.warehouses.reconciliation.history') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">المورد</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">-- اختر المورد --</option>
                        @foreach($suppliers ?? [] as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">من التاريخ</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">إلى التاريخ</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-info flex-grow-1">
                        <i class="fas fa-filter"></i> فلترة
                    </button>
                    <a href="{{ route('manufacturing.warehouses.reconciliation.history') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">✅ متطابقة</h6>
                    <h3>{{ $stats['matched'] ?? 0 }}</h3>
                    <small>تسويات متطابقة بدون فروقات</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">🔧 معدّلة</h6>
                    <h3>{{ $stats['adjusted'] ?? 0 }}</h3>
                    <small>تسويات تم تعديلها</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">❌ مرفوضة</h6>
                    <h3>{{ $stats['rejected'] ?? 0 }}</h3>
                    <small>فواتير مرفوضة</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">📊 إجمالي</h6>
                    <h3>{{ ($stats['matched'] ?? 0) + ($stats['adjusted'] ?? 0) + ($stats['rejected'] ?? 0) }}</h3>
                    <small>جميع التسويات المكتملة</small>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    @if($reconciliations->count() > 0)
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">السجل الكامل للتسويات</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
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
            <div class="card-footer">
                {{ $reconciliations->links() }}
            </div>
        </div>
    @else
        <div class="alert alert-info text-center py-5">
            <h5>📭 لا توجد تسويات مكتملة</h5>
            <p class="mb-0">جميع التسويات المعلقة لم تُنجز بعد</p>
        </div>
    @endif
</div>

@endsection

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-header {
        border-bottom: 2px solid #dee2e6;
    }
</style>
@endpush
