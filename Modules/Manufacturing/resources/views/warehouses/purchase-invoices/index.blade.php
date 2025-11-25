@extends('master')

@section('title', 'إدارة فواتير الشراء')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-file-text"></i>
                إدارة فواتير الشراء
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المستودع</span>
                <i class="feather icon-chevron-left"></i>
                <span>فواتير الشراء</span>
            </nav>
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

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    قائمة فواتير الشراء
                </h4>
                @if (auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_CREATE'))
                    <a href="{{ route('manufacturing.purchase-invoices.create') }}" class="um-btn um-btn-primary">
                        <i class="feather icon-plus"></i>
                        إضافة فاتورة شراء جديدة
                    </a>
                @endif
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET" action="{{ route('manufacturing.purchase-invoices.index') }}">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث في الفواتير..." value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <input type="text" name="invoice_number" class="um-form-control" placeholder="رقم الفاتورة..." value="{{ request('invoice_number') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="supplier_id" class="um-form-control">
                                <option value="">جميع الموردين</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <a href="{{ route('manufacturing.purchase-invoices.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>

                            <th>رقم الفاتورة</th>
                            <th>المورد</th>
                            <th>تاريخ الفاتورة</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        <tr>

                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                            <td>{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</td>
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
                                <span class="um-badge" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                    {{ $invoice->status->label() }}
                                </span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        @if (auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_READ'))
                                            <a href="{{ route('manufacturing.purchase-invoices.show', $invoice->id) }}" class="um-dropdown-item um-btn-view">
                                                <i class="feather icon-eye"></i>
                                                <span>عرض</span>
                                            </a>
                                        @endif
                                        @if (auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_UPDATE'))
                                            <a href="{{ route('manufacturing.purchase-invoices.edit', $invoice->id) }}" class="um-dropdown-item um-btn-edit">
                                                <i class="feather icon-edit-2"></i>
                                                <span>تعديل</span>
                                            </a>
                                        @endif
                                        @if (auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_DELETE'))
                                            <form action="{{ route('manufacturing.purchase-invoices.destroy', $invoice->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="um-dropdown-item um-btn-delete" title="حذف" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                    <i class="feather icon-trash-2"></i>
                                                    <span>حذف</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">لا توجد فواتير شراء</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile View -->
            <div class="um-mobile-view">
                @forelse($invoices as $invoice)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <h5>{{ $invoice->invoice_number }}</h5>
                            <p>{{ $invoice->supplier->name ?? 'N/A' }}</p>
                        </div>
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
                        <span class="um-badge" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                            {{ $invoice->status->label() }}
                        </span>
                    </div>
                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span>تاريخ الفاتورة:</span>
                            <span>{{ $invoice->invoice_date->format('Y-m-d') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span>المبلغ:</span>
                            <span>{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</span>
                        </div>
                        @if($invoice->due_date)
                        <div class="um-info-row">
                            <span>تاريخ الاستحقاق:</span>
                            <span>{{ $invoice->due_date->format('Y-m-d') }}
                                @if($invoice->isOverdue())
                                    <span class="um-badge um-badge-danger">متأخر</span>
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>
                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                @if (auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_READ'))
                                    <a href="{{ route('manufacturing.purchase-invoices.show', $invoice->id) }}" class="um-dropdown-item um-btn-view">
                                        <i class="feather icon-eye"></i>
                                        <span>عرض</span>
                                    </a>
                                @endif
                                @if (auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_UPDATE'))
                                    <a href="{{ route('manufacturing.purchase-invoices.edit', $invoice->id) }}" class="um-dropdown-item um-btn-edit">
                                        <i class="feather icon-edit-2"></i>
                                        <span>تعديل</span>
                                    </a>
                                @endif
                                @if (auth()->user()->hasPermission('WAREHOUSE_PURCHASE_INVOICES_DELETE'))
                                    <form action="{{ route('manufacturing.purchase-invoices.destroy', $invoice->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="um-dropdown-item um-btn-delete" title="حذف" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                            <i class="feather icon-trash-2"></i>
                                            <span>حذف</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center">لا توجد فواتير شراء</div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($invoices->hasPages())
                <div class="um-pagination-section">
                    <div>
                        <p class="um-pagination-info">
                            عرض {{ $invoices->firstItem() ?? 0 }} إلى {{ $invoices->lastItem() ?? 0 }} من أصل
                            {{ $invoices->total() }} فاتورة شراء
                        </p>
                    </div>
                    <div>
                        {{ $invoices->links() }}
                    </div>
                </div>
            @endif
        </section>
    </div>

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
