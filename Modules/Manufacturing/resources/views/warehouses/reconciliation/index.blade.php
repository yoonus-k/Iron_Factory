@extends('master')

@section('title', 'لوحة التسوية')

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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-2">
            <div class="um-stat-card">
                <div class="um-stat-icon" style="background: linear-gradient(135deg, #94A3B8 0%, #64748B 100%);">
                    <i class="feather icon-clock"></i>
                </div>
                <div class="um-stat-content">
                    <h3 class="um-stat-value">{{ $stats['total_pending'] }}</h3>
                    <p class="um-stat-label">بانتظار</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="um-stat-card">
                <div class="um-stat-icon" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
                    <i class="feather icon-alert-triangle"></i>
                </div>
                <div class="um-stat-content">
                    <h3 class="um-stat-value" style="color: #F59E0B;">{{ $stats['total_discrepancy'] }}</h3>
                    <p class="um-stat-label">فروقات</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="um-stat-card">
                <div class="um-stat-icon" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                    <i class="feather icon-check-circle"></i>
                </div>
                <div class="um-stat-content">
                    <h3 class="um-stat-value" style="color: #10B981;">{{ $stats['total_matched'] }}</h3>
                    <p class="um-stat-label">متطابقة</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="um-stat-card">
                <div class="um-stat-icon" style="background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);">
                    <i class="feather icon-tool"></i>
                </div>
                <div class="um-stat-content">
                    <h3 class="um-stat-value" style="color: #0066CC;">{{ $stats['total_adjusted'] }}</h3>
                    <p class="um-stat-label">مسوية</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="um-stat-card">
                <div class="um-stat-icon" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">
                    <i class="feather icon-x-circle"></i>
                </div>
                <div class="um-stat-content">
                    <h3 class="um-stat-value" style="color: #EF4444;">{{ $stats['total_rejected'] }}</h3>
                    <p class="um-stat-label">مرفوضة</p>
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

    <!-- قائمة التسويات المعلقة -->
    @if ($pending->count() > 0)
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-alert-circle"></i>
                    تسويات معلقة ({{ $pending->total() }})
                </h4>
            </div>
            <div class="card-body">
                @foreach ($pending as $item)
                    <div class="um-reconciliation-item" style="background: {{ $item->reconciliation_status === 'discrepancy' ? '#FFF9E6' : '#E6F2FF' }}; border-right: 4px solid {{ $item->reconciliation_status === 'discrepancy' ? '#F59E0B' : '#0066CC' }};">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-3">
                                <div class="um-info-box">
                                    <span class="um-info-label">
                                        <i class="feather icon-package"></i>
                                        الشحنة:
                                    </span>
                                    <strong class="um-info-value">#{{ $item->note_number ?? $item->id }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="um-info-box">
                                    <span class="um-info-label">
                                        <i class="feather icon-user"></i>
                                        المورد:
                                    </span>
                                    <strong class="um-info-value">{{ $item->supplier->name }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="um-info-box">
                                    <span class="um-info-label">
                                        <i class="feather icon-file-text"></i>
                                        الفاتورة:
                                    </span>
                                    <strong class="um-info-value">{{ $item->purchaseInvoice->invoice_number }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="um-info-box">
                                    <span class="um-info-label">
                                        <i class="feather icon-info"></i>
                                        الحالة:
                                    </span>
                                    <span class="um-badge {{ $item->reconciliation_status === 'discrepancy' ? 'um-badge-warning' : 'um-badge-info' }}">
                                        {{ $item->reconciliation_status }}
                                    </span>
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
                                            <th class="text-center">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>الوزن (كيلو)</strong></td>
                                            <td class="text-end">
                                                <span class="um-badge um-badge-info">{{ number_format($item->actual_weight, 2) }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="um-badge um-badge-secondary">{{ number_format($item->invoice_weight, 2) }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="um-badge {{ $item->weight_discrepancy > 0 ? 'um-badge-danger' : 'um-badge-success' }}">
                                                    {{ $item->weight_discrepancy > 0 ? '+' : '' }}{{ number_format($item->weight_discrepancy, 2) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <span class="um-badge {{ abs($item->discrepancy_percentage) > 5 ? 'um-badge-danger' : 'um-badge-warning' }}">
                                                    {{ number_format($item->discrepancy_percentage, 2) }}%
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('manufacturing.warehouses.reconciliation.show', $item) }}" class="um-btn-action um-btn-view" title="عرض التفاصيل">
                                                    <i class="feather icon-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="um-pagination-section">
                    {{ $pending->links() }}
                </div>
            </div>
        </section>
    @else
        <div class="um-alert-custom um-alert-success" style="text-align: center;">
            <i class="feather icon-check-circle" style="font-size: 48px; margin-bottom: 10px;"></i>
            <h4>لا توجد تسويات معلقة!</h4>
            <p>جميع التسويات تمت بنجاح.</p>
        </div>
    @endif
</div>

<style>
    .um-reconciliation-item {
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }
    .um-reconciliation-item:hover {
        box-shadow: 0 8px 20px rgba(0, 102, 204, 0.15);
        transform: translateY(-2px);
    }
    .um-info-box {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .um-info-label {
        font-size: 13px;
        color: #718096;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .um-info-label i {
        font-size: 14px;
    }
    .um-info-value {
        font-size: 16px;
        color: #1A202C;
        font-weight: 600;
    }
    .um-comparison-table {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    .um-comparison-table .um-table {
        margin: 0;
    }
    .um-comparison-table .um-table thead {
        background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);
    }
    .um-btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        background: transparent;
    }
    .um-btn-action:hover {
        background: var(--logo-blue-lighter);
        transform: translateY(-2px);
    }
    .um-btn-action i {
        font-size: 18px;
    }
</style>

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
