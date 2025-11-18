@extends('master')

@section('title', 'تقرير أداء الموردين')

@section('content')
<div class="um-content-wrapper">
    <!-- Header Section -->
    <div class="um-header-section">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="um-page-title">
                    <i class="feather icon-bar-chart-2"></i>
                    تقرير أداء الموردين
                </h1>
                <nav class="um-breadcrumb-nav">
                    <span>
                        <i class="feather icon-home"></i> لوحة التحكم
                    </span>
                    <i class="feather icon-chevron-left"></i>
                    <span>المستودع</span>
                    <i class="feather icon-chevron-left"></i>
                    <span>التسوية</span>
                    <i class="feather icon-chevron-left"></i>
                    <span>تقرير الموردين</span>
                </nav>
            </div>
            <div class="col-auto">
                <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="um-btn um-btn-outline">
                    <i class="feather icon-arrow-right"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="um-stat-card">
                <div class="um-stat-icon" style="background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);">
                    <i class="feather icon-users"></i>
                </div>
                <div class="um-stat-content">
                    <h3 class="um-stat-value" style="color: #0066CC;">{{ $suppliers->count() }}</h3>
                    <p class="um-stat-label">عدد الموردين</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="um-stat-card">
                <div class="um-stat-icon" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                    <i class="feather icon-package"></i>
                </div>
                <div class="um-stat-content">
                    <h3 class="um-stat-value" style="color: #10B981;">{{ $totalShipments ?? 0 }}</h3>
                    <p class="um-stat-label">إجمالي الشحنات</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="um-stat-card">
                <div class="um-stat-icon" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
                    <i class="feather icon-target"></i>
                </div>
                <div class="um-stat-content">
                    <h3 class="um-stat-value" style="color: #F59E0B;">{{ number_format($averageAccuracy ?? 0, 2) }}%</h3>
                    <p class="um-stat-label">متوسط الدقة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="um-stat-card">
                <div class="um-stat-icon" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">
                    <i class="feather icon-alert-circle"></i>
                </div>
                <div class="um-stat-content">
                    <h3 class="um-stat-value" style="color: #EF4444;">{{ number_format($totalDiscrepancy ?? 0, 2) }}</h3>
                    <p class="um-stat-label">إجمالي الفروقات (كيلو)</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .um-stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        .um-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 102, 204, 0.15);
        }
        .um-stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .um-stat-icon i {
            font-size: 28px;
        }
        .um-stat-content {
            flex: 1;
        }
        .um-stat-value {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
            color: #1A202C;
        }
        .um-stat-label {
            font-size: 14px;
            color: #718096;
            margin: 0;
            font-weight: 500;
        }
    </style>

    <!-- Suppliers Table -->
    @if($suppliers->count() > 0)
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-activity"></i>
                    تفاصيل أداء الموردين
                </h4>
            </div>
            <div class="um-table-responsive">
                <table class="um-table">
                    <thead>
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
        </section>

        <!-- Legend -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="um-alert-custom um-alert-info">
                    <i class="feather icon-info"></i>
                    <div>
                        <h6 style="margin: 0 0 10px 0; font-weight: 600;">شرح المؤشرات</h6>
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
        <div class="um-alert-custom um-alert-info" style="text-align: center; padding: 40px;">
            <i class="feather icon-inbox" style="font-size: 48px; margin-bottom: 15px;"></i>
            <h4>لا توجد بيانات للموردين</h4>
            <p class="mb-0">لم يتم إضافة أي موردين بعد</p>
        </div>
    @endif
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
