@extends('master')

@section('title', 'فواتير الشراء')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style-material.css') }}">

    @if (session('success'))
        <div class="um-alert-custom um-alert-success" role="alert">
            <i class="feather icon-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
    @endif

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-file-text"></i>
                    </div>
                    <div class="header-info">
                        <h1>فواتير الشراء</h1>
                        <p>إدارة فواتير الشراء من الموردين</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.purchase-invoices.create') }}" class="btn btn-primary">
                        <i class="feather icon-plus"></i>
                        فاتورة جديدة
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">🔍 البحث والتصفية</h3>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="البحث برقم الفاتورة أو المورد" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="supplier_id" class="form-control">
                            <option value="">-- جميع الموردين --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">-- جميع الحالات --</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="feather icon-search"></i>
                            بحث
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📋 قائمة الفواتير</h3>
                <p class="text-muted">إجمالي الفواتير: {{ $invoices->total() }}</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>المورد</th>
                                <th>تاريخ الفاتورة</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>تاريخ الاستحقاق</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td>
                                        <strong>{{ $invoice->invoice_number }}</strong>
                                        @if($invoice->invoice_reference_number)
                                            <br><small class="text-muted">{{ $invoice->invoice_reference_number }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $invoice->supplier->name ?? 'N/A' }}</td>
                                    <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                    <td>
                                        <strong>{{ number_format($invoice->total_amount, 2) }}</strong>
                                        <span class="badge badge-secondary">{{ $invoice->currency }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColor = $invoice->status->color();
                                            $bgColor = $statusColor === 'yellow' ? '#fff3cd' : (
                                                $statusColor === 'green' ? '#d4edda' : (
                                                $statusColor === 'red' ? '#f8d7da' : (
                                                $statusColor === 'blue' ? '#d1ecf1' : '#e2e3e5'
                                            )));
                                            $textColor = $statusColor === 'yellow' ? '#856404' : (
                                                $statusColor === 'green' ? '#155724' : (
                                                $statusColor === 'red' ? '#721c24' : (
                                                $statusColor === 'blue' ? '#0c5460' : '#383d41'
                                            )));
                                        @endphp
                                        <span style="background-color: {{ $bgColor }}; color: {{ $textColor }}; padding: 4px 8px; border-radius: 4px; display: inline-block;">
                                            {{ $invoice->status->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($invoice->due_date)
                                            <span class="badge {{ $invoice->isOverdue() ? 'badge-danger' : 'badge-info' }}">
                                                {{ $invoice->due_date->format('Y-m-d') }}
                                                @if($invoice->isOverdue())
                                                    (متأخر)
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('manufacturing.purchase-invoices.show', $invoice->id) }}" class="btn btn-info" title="عرض">
                                                <i class="feather icon-eye"></i>
                                            </a>
                                            <a href="{{ route('manufacturing.purchase-invoices.edit', $invoice->id) }}" class="btn btn-warning" title="تعديل">
                                                <i class="feather icon-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('manufacturing.purchase-invoices.destroy', $invoice->id) }}" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="حذف" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                    <i class="feather icon-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="feather icon-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                                        <p>لا توجد فواتير</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
        <!-- Header Section -->

@section('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
