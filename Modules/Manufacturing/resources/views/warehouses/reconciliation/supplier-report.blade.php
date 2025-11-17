@extends('manufacturing::layouts.app')

@section('title', 'تقرير أداء الموردين')

@section('content')
<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="fas fa-chart-bar"></i> تقرير أداء الموردين
            </h2>
            <small class="text-muted">تحليل شامل لأداء الموردين والفروقات</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> العودة
            </a>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">عدد الموردين</h6>
                    <h3 class="text-primary">{{ $suppliers->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">إجمالي الشحنات</h6>
                    <h3 class="text-success">{{ $totalShipments ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">متوسط الدقة</h6>
                    <h3 class="text-warning">{{ number_format($averageAccuracy ?? 0, 2) }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">إجمالي الفروقات</h6>
                    <h3 class="text-danger">{{ number_format($totalDiscrepancy ?? 0, 2) }} كيلو</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Suppliers Table -->
    @if($suppliers->count() > 0)
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">تفاصيل أداء الموردين</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>اسم المورد</th>
                            <th>الشحنات</th>
                            <th>المتطابقة</th>
                            <th>الفروقات</th>
                            <th>المعدّلة</th>
                            <th>المرفوضة</th>
                            <th>دقة الأداء</th>
                            <th>متوسط الفرق</th>
                            <th>إجمالي الفروقات</th>
                            <th>التصنيف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                            @php
                                $shipments = $supplier->deliveryNotes()->where('type', 'incoming')->get();
                                $total = $shipments->count();
                                $matched = $shipments->where('reconciliation_status', 'matched')->count();
                                $discrepancy = $shipments->where('reconciliation_status', 'discrepancy')->count();
                                $adjusted = $shipments->where('reconciliation_status', 'adjusted')->count();
                                $rejected = $shipments->where('reconciliation_status', 'rejected')->count();
                                $accuracy = $total > 0 ? (($matched + $adjusted) / $total * 100) : 0;
                                $avgDiscrepancy = $total > 0 ? $shipments->avg('discrepancy_percentage') : 0;
                                $totalDiscrepancy = $shipments->sum('weight_discrepancy');

                                // التصنيف
                                if ($accuracy >= 95) {
                                    $rating = ['label' => '⭐⭐⭐⭐⭐', 'class' => 'success'];
                                } elseif ($accuracy >= 90) {
                                    $rating = ['label' => '⭐⭐⭐⭐', 'class' => 'success'];
                                } elseif ($accuracy >= 85) {
                                    $rating = ['label' => '⭐⭐⭐', 'class' => 'warning'];
                                } elseif ($accuracy >= 75) {
                                    $rating = ['label' => '⭐⭐', 'class' => 'warning'];
                                } else {
                                    $rating = ['label' => '⭐', 'class' => 'danger'];
                                }
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $supplier->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $supplier->code ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $total }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $matched }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">{{ $discrepancy }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $adjusted }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-danger">{{ $rejected }}</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 1.5rem;">
                                        <div class="progress-bar" role="progressbar"
                                             style="width: {{ min($accuracy, 100) }}%; background-color: {{ $accuracy >= 90 ? '#28a745' : ($accuracy >= 75 ? '#ffc107' : '#dc3545') }};"
                                             aria-valuenow="{{ $accuracy }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($accuracy, 1) }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ abs($avgDiscrepancy) <= 1 ? 'bg-success' : (abs($avgDiscrepancy) <= 5 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ number_format($avgDiscrepancy, 2) }}%
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $totalDiscrepancy > 0 ? 'bg-danger' : ($totalDiscrepancy < 0 ? 'bg-warning' : 'bg-success') }}">
                                        {{ number_format($totalDiscrepancy, 2) }} كيلو
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $rating['class'] }}">
                                        {{ $rating['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Legend -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title mb-3">📊 شرح المؤشرات</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <small>
                                    <strong>⭐⭐⭐⭐⭐:</strong> دقة 95% فما فوق
                                </small>
                            </div>
                            <div class="col-md-3">
                                <small>
                                    <strong>⭐⭐⭐⭐:</strong> دقة 90-95%
                                </small>
                            </div>
                            <div class="col-md-3">
                                <small>
                                    <strong>⭐⭐⭐:</strong> دقة 85-90%
                                </small>
                            </div>
                            <div class="col-md-3">
                                <small>
                                    <strong>⭐⭐ أو ⭐:</strong> دقة أقل من 85%
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center py-5">
            <h5>📭 لا توجد بيانات للموردين</h5>
            <p class="mb-0">لم يتم إضافة أي موردين بعد</p>
        </div>
    @endif
</div>

@endsection

@push('styles')
<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1rem;
    }

    .card-header {
        border-bottom: 2px solid #dee2e6;
    }

    .table-striped tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }

    .progress {
        background-color: #e9ecef;
    }

    .progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.85rem;
    }
</style>
@endpush
