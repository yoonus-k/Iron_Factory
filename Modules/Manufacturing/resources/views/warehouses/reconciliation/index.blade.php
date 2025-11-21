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

    <!-- Statistics Cards with Logo Style -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-2">
            <div class="um-stat-card" style="background: linear-gradient(135deg, #0052A3 0%, #0066CC 100%); color: white;">
                <div class="um-stat-icon" style="background: rgba(255,255,255,0.2); box-shadow: none;">
                    <i class="feather icon-clock"></i>
                </div>
                <div class="um-stat-content">
                    <h3 class="um-stat-value" style="color: white;">{{ $stats['total_pending'] }}</h3>
                    <p class="um-stat-label" style="color: rgba(255,255,255,0.8);">بانتظار</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="um-stat-card" style="background: linear-gradient(135deg, #456575 0%, #34495E 100%); color: white;">
                <div class="um-stat-icon" style="background: rgba(255,255,255,0.2); box-shadow: none;">
                    <i class="feather icon-alert-triangle"></i>
                </div>
                <div class="um-stat-content">
                    <h3 class="um-stat-value" style="color: white;">{{ $stats['total_discrepancy'] }}</h3>
                    <p class="um-stat-label" style="color: rgba(255,255,255,0.8);">فروقات</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="um-stat-card" style="background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%); color: white;">
                <div class="um-stat-icon" style="background: rgba(255,255,255,0.2); box-shadow: none;">
                    <i class="feather icon-check-circle"></i>
                </div>
                <div class="um-stat-content">
                    <h3 class="um-stat-value" style="color: white;">{{ $stats['total_matched'] }}</h3>
                    <p class="um-stat-label" style="color: rgba(255,255,255,0.8);">متطابقة</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="um-stat-card" style="background: linear-gradient(135deg, #34495E 0%, #456575 100%); color: white;">
                <div class="um-stat-icon" style="background: rgba(255,255,255,0.2); box-shadow: none;">
                    <i class="feather icon-tool"></i>
                </div>
                <div class="um-stat-content">
                    <h3 class="um-stat-value" style="color: white;">{{ $stats['total_adjusted'] }}</h3>
                    <p class="um-stat-label" style="color: rgba(255,255,255,0.8);">مسوية</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="um-stat-card" style="background: linear-gradient(135deg, #456575 0%, #34495E 100%); color: white;">
                <div class="um-stat-icon" style="background: rgba(255,255,255,0.2); box-shadow: none;">
                    <i class="feather icon-x-circle"></i>
                </div>
                <div class="um-stat-content">
                    <h3 class="um-stat-value" style="color: white;">{{ $stats['total_rejected'] }}</h3>
                    <p class="um-stat-label" style="color: rgba(255,255,255,0.8);">مرفوضة</p>
                </div>
            </div>
        </div>
    </div>

<style>
    /* ===== تصميم الإحصائيات على نمط الشعار ===== */
    .um-stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0, 82, 163, 0.12);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 102, 204, 0.1);
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 15px;
        position: relative;
        overflow: hidden;
    }

    .um-stat-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .um-stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 12px 30px rgba(0, 82, 163, 0.2);
        border-color: rgba(0, 102, 204, 0.3);
    }

    .um-stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
        font-size: 32px;
        font-weight: 600;
    }

    .um-stat-content {
        flex: 1;
        position: relative;
        z-index: 1;
    }

    .um-stat-value {
        font-size: 36px;
        font-weight: 800;
        margin: 0;
        background: linear-gradient(135deg, #0052A3 0%, #456575 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -1px;
    }

    .um-stat-label {
        font-size: 13px;
        color: #718096;
        margin: 6px 0 0 0;
        font-weight: 600;
        text-transform: capitalize;
        letter-spacing: 0.5px;
    }

    /* ===== علامات التبويب على نمط الشعار ===== */
    .nav-tabs {
        gap: 8px;
        background: linear-gradient(90deg, rgba(0, 82, 163, 0.05) 0%, rgba(67, 101, 117, 0.05) 100%);
        padding: 12px;
        border-radius: 12px;
        margin-bottom: 24px;
        border: 1px solid rgba(0, 102, 204, 0.1);
    }

    .nav-tabs .nav-link {
        color: #718096;
        border: 2px solid transparent;
        padding: 12px 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        border-radius: 10px;
        transition: all 0.3s ease;
        background: transparent;
    }

    .nav-tabs .nav-link:hover {
        color: #0066CC;
        background: rgba(0, 102, 204, 0.08);
        border-color: rgba(0, 102, 204, 0.2);
    }

    .nav-tabs .nav-link.active {
        color: white;
        background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);
        border-color: #0066CC;
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    }

    .nav-tabs .nav-link .badge {
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
    }

    .nav-tabs .nav-link.active .badge {
        background: rgba(255, 255, 255, 0.3) !important;
        color: white;
    }

    /* ===== عناصر التسوية على نمط الشعار ===== */
    .um-reconciliation-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .um-reconciliation-item {
        border-radius: 14px;
        padding: 28px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .um-reconciliation-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #0066CC 0%, #456575 100%);
    }

    .um-reconciliation-item:hover {
        box-shadow: 0 12px 30px rgba(0, 82, 163, 0.15);
        transform: translateY(-4px);
        border-color: rgba(0, 102, 204, 0.2);
    }

    /* ===== صناديق المعلومات ===== */
    .um-info-box {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .um-info-label {
        font-size: 12px;
        color: #718096;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .um-info-label i {
        font-size: 14px;
        color: #0066CC;
    }

    .um-info-value {
        font-size: 16px;
        color: #1A202C;
        font-weight: 700;
    }

    /* ===== جدول المقارنة ===== */
    .um-comparison-table {
        background: linear-gradient(135deg, rgba(0, 102, 204, 0.03) 0%, rgba(67, 101, 117, 0.03) 100%);
        border-radius: 12px;
        padding: 18px;
        margin-top: 20px;
        border: 1px solid rgba(0, 102, 204, 0.1);
        overflow: hidden;
    }

    .um-comparison-table .um-table {
        margin: 0;
    }

    .um-comparison-table .um-table thead {
        background: linear-gradient(135deg, #0052A3 0%, #0066CC 100%);
    }

    .um-comparison-table .um-table thead th {
        color: white;
        font-weight: 700;
        border: none;
    }

    /* ===== الإجراءات ===== */
    .um-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .um-btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        cursor: pointer;
        background: rgba(0, 102, 204, 0.1);
        color: #0066CC;
        font-size: 18px;
        position: relative;
        overflow: hidden;
    }

    .um-btn-action::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(0, 102, 204, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.3s, height 0.3s;
    }

    .um-btn-action:hover::before {
        width: 40px;
        height: 40px;
    }

    .um-btn-action:hover {
        background: linear-gradient(135deg, rgba(0, 102, 204, 0.2) 0%, rgba(67, 101, 117, 0.1) 100%);
        transform: translateY(-3px);
        border-color: #0066CC;
    }

    .um-btn-action.um-btn-edit:hover {
        color: #F59E0B;
        background: rgba(245, 158, 11, 0.1);
        border-color: #F59E0B;
    }

    .um-btn-action.um-btn-delete:hover {
        color: #EF4444;
        background: rgba(239, 68, 68, 0.1);
        border-color: #EF4444;
    }

    .um-btn-action i {
        position: relative;
        z-index: 1;
    }

    /* ===== ملاحظات ===== */
    .um-notes-section {
        background: linear-gradient(135deg, rgba(0, 102, 204, 0.08) 0%, rgba(67, 101, 117, 0.05) 100%);
        padding: 16px;
        border-radius: 10px;
        border-left: 4px solid #0066CC;
        margin-top: 20px;
    }

    .um-notes-section strong {
        color: #0066CC;
        font-weight: 700;
    }

    /* ===== تنبيهات ===== */
    .um-alert-info {
        background: linear-gradient(135deg, #E8F0FF 0%, #F0F4F8 100%);
        border-left: 4px solid #0066CC;
        border-radius: 10px;
    }

    .um-alert-success {
        background: linear-gradient(135deg, #E6F7ED 0%, #F0F8F5 100%);
        border-left: 4px solid #10B981;
        border-radius: 10px;
    }

    /* ===== بطاقات البيانات الرئيسية ===== */
    .um-main-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 82, 163, 0.1);
        border: 1px solid rgba(0, 102, 204, 0.1);
        overflow: hidden;
    }

    .um-card-header {
        background: linear-gradient(135deg, #0052A3 0%, #0066CC 100%);
        padding: 24px;
        color: white;
    }

    .um-card-title {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .um-card-title i {
        font-size: 24px;
    }

    /* ===== تأثيرات عامة ===== */
    .badge {
        font-weight: 600;
        letter-spacing: 0.3px;
    }
</style>    <!-- Filters Section -->
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
                        <span class="badge bg-secondary ms-2">{{ $stats['total_pending'] }}</span>
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
                        <div class="um-reconciliation-list">
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
                                                <a href="{{ route('manufacturing.warehouses.reconciliation.link-invoice.edit', $item->id) }}" class="um-btn-action um-btn-edit" title="تعديل">
                                                    <i class="feather icon-edit-2"></i>
                                                </a>
                                                <form action="{{ route('manufacturing.warehouses.reconciliation.link-invoice.delete', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل تريد حذف هذه التسوية؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="um-btn-action um-btn-delete" title="حذف" style="border: none; background: transparent; cursor: pointer;">
                                                        <i class="feather icon-trash-2"></i>
                                                    </button>
                                                </form>
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
        margin: 0 2px;
    }
    .um-btn-action:hover {
        background: var(--logo-blue-lighter);
        transform: translateY(-2px);
    }
    .um-btn-action.um-btn-edit:hover {
        background: #FFF3CD;
        color: #856404;
    }
    .um-btn-action.um-btn-delete:hover {
        background: #F8D7DA;
        color: #721C24;
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
