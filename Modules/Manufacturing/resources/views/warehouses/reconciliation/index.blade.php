@extends('master')

@section('title', 'لوحة التسوية')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">🔄 لوحة تسوية البضاعة والفواتير</h1>
                <p class="text-muted">إدارة المقارنة بين الأوزان الفعلية والفواتير</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('manufacturing.warehouses.reconciliation.history') }}" class="btn btn-secondary">
                    📚 السجل
                </a>
                <a href="{{ route('manufacturing.warehouses.reconciliation.supplier-report') }}" class="btn btn-info">
                    📊 تقرير الموردين
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ❌ {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- الإحصائيات -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-truncate">
                        <h3 class="card-value">{{ $stats['total_pending'] }}</h3>
                        <p class="card-title text-muted">⏳ بانتظار</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-truncate">
                        <h3 class="card-value text-warning">{{ $stats['total_discrepancy'] }}</h3>
                        <p class="card-title text-muted">⚠️ فروقات</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-truncate">
                        <h3 class="card-value text-success">{{ $stats['total_matched'] }}</h3>
                        <p class="card-title text-muted">✅ متطابقة</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-truncate">
                        <h3 class="card-value text-info">{{ $stats['total_adjusted'] }}</h3>
                        <p class="card-title text-muted">🔧 مسوية</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-truncate">
                        <h3 class="card-value text-danger">{{ $stats['total_rejected'] }}</h3>
                        <p class="card-title text-muted">❌ مرفوضة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">المورد:</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">-- الكل --</option>
                        @foreach (\App\Models\Supplier::where('is_active', true)->get() as $supplier)
                            <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">من التاريخ:</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">إلى التاريخ:</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">🔍 بحث</button>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}" class="btn btn-secondary w-100">↻ إعادة تحديد</a>
                </div>
            </form>
        </div>
    </div>

    <!-- قائمة التسويات المعلقة -->
    @if ($pending->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">⚠️ تسويات معلقة ({{ $pending->total() }})</h5>
            </div>
            <div class="card-body">
                @foreach ($pending as $item)
                    <div class="card mb-3 border-{{ $item->reconciliation_status === 'discrepancy' ? 'warning' : 'info' }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <h6 class="mb-1">الشحنة:</h6>
                                    <p class="mb-0">
                                        <strong>#{{ $item->note_number ?? $item->id }}</strong>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="mb-1">المورد:</h6>
                                    <p class="mb-0">{{ $item->supplier->name }}</p>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="mb-1">الفاتورة:</h6>
                                    <p class="mb-0">
                                        {{ $item->purchaseInvoice->invoice_number }}
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="mb-1">الحالة:</h6>
                                    <p class="mb-0">
                                        <span class="badge bg-{{ $item->reconciliation_status === 'discrepancy' ? 'warning' : 'info' }}">
                                            {{ $item->reconciliation_status }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- المقارنة -->
                            @if ($item->actual_weight && $item->invoice_weight)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <th>البيان</th>
                                                <th class="text-end">الفعلي (الميزان)</th>
                                                <th class="text-end">الفاتورة</th>
                                                <th class="text-end">الفرق</th>
                                                <th class="text-end">النسبة</th>
                                            </tr>
                                            <tr>
                                                <td><strong>الوزن (كيلو)</strong></td>
                                                <td class="text-end">
                                                    <strong>{{ number_format($item->actual_weight, 2) }}</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong>{{ number_format($item->invoice_weight, 2) }}</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-{{ $item->weight_discrepancy > 0 ? 'danger' : 'success' }}">
                                                        {{ $item->weight_discrepancy > 0 ? '+' : '' }}{{ number_format($item->weight_discrepancy, 2) }}
                                                    </strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-{{ abs($item->discrepancy_percentage) > 5 ? 'danger' : 'warning' }}">
                                                        {{ number_format($item->discrepancy_percentage, 2) }}%
                                                    </strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-3">
                                <a href="{{ route('manufacturing.warehouses.reconciliation.show', $item) }}" class="btn btn-sm btn-primary">
                                    👁️ عرض التفاصيل
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-center">
                    {{ $pending->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-success">
            ✅ لا توجد تسويات معلقة! كل شيء مسوى.
        </div>
    @endif
</div>
@endsection
