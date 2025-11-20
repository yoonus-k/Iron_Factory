@extends('master')

@section('title', 'تقرير القطع قيد التشغيل (WIP)')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/shift-dashboard.css') }}">
@endpush

@section('content')
<style>
    /* تخصيصات إضافية خاصة بتقرير WIP */
    .delay-badge {
        display: inline-flex;
        align-items: center;
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
    }

    .delay-normal {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .delay-warning {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .delay-critical {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }

    .status-text {
        font-weight: 600;
    }

    .status-critical {
        color: var(--danger-color);
    }

    .status-warning {
        color: var(--warning-color);
    }

    .status-normal {
        color: var(--success-color);
    }

    .data-table-container {
        background: var(--white);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-200);
        overflow: hidden;
        position: relative;
        margin-top: var(--spacing-xl);
    }

    .data-table-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-light-blue));
    }

    .table-header {
        padding: var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .table-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: right;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--gray-300);
        background: var(--gray-50);
    }

    .data-table tbody td {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: right;
        font-size: 0.875rem;
        color: var(--gray-800);
        border-bottom: 1px solid var(--gray-200);
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .data-table tbody tr {
        transition: all var(--transition-base);
    }

    .data-table tbody tr:hover {
        background-color: rgba(0, 102, 178, 0.05);
    }
</style>

<div class="shift-container">
    <div class="shift-header">
        <h1>
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            تقرير القطع قيد التشغيل (WIP)
        </h1>
        <p>القطع التي بدأت في مراحل الإنتاج ولم تكتمل بعد</p>
    </div>

    <!-- الإحصائيات الرئيسية -->
    <div class="stats-grid">
        <div class="stat-card danger">
            <div class="stat-header">
                <div>
                    <div class="stat-title">القطع العالقة</div>
                    <p class="stat-value">{{ number_format($stats['total_stuck']) }}</p>
                </div>
                <div class="stat-icon danger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-header">
                <div>
                    <div class="stat-title">متوسط وقت التأخير</div>
                    <p class="stat-value">{{ number_format($stats['avg_delay_hours'], 1) }}</p>
                    <small style="color: var(--gray-600);">ساعة</small>
                </div>
                <div class="stat-icon warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card info">
            <div class="stat-header">
                <div>
                    <div class="stat-title">أطول تأخير</div>
                    <p class="stat-value">{{ number_format($stats['max_delay_hours'], 1) }}</p>
                    <small style="color: var(--gray-600);">ساعة</small>
                </div>
                <div class="stat-icon info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card danger">
            <div class="stat-header">
                <div>
                    <div class="stat-title">حالة حرجة</div>
                    <p class="stat-value">{{ number_format($stats['critical_count']) }}</p>
                </div>
                <div class="stat-icon danger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- توزيع المراحل -->
    <div class="stage-grid">
        <div class="stage-card stage-1">
            <div class="stage-name">المرحلة 1 - التقسيم</div>
            <p class="stage-items">{{ $stats['by_stage']['stage1'] }} قطعة</p>
        </div>
        <div class="stage-card stage-2">
            <div class="stage-name">المرحلة 2 - المعالجة</div>
            <p class="stage-items">{{ $stats['by_stage']['stage2'] }} قطعة</p>
        </div>
        <div class="stage-card stage-3">
            <div class="stage-name">المرحلة 3 - اللف</div>
            <p class="stage-items">{{ $stats['by_stage']['stage3'] }} قطعة</p>
        </div>
        <div class="stage-card stage-4">
            <div class="stage-name">المرحلة 4 - التعليب</div>
            <p class="stage-items">{{ $stats['by_stage']['stage4'] }} قطعة</p>
        </div>
    </div>

    <!-- فلاتر البيانات -->
    <div class="filters-card">
        <h3>فلترة البيانات</h3>
        <form method="GET" action="{{ route('manufacturing.reports.wip') }}">
            <div class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label">المرحلة</label>
                    <select name="stage" class="select-input">
                        <option value="">كل المراحل</option>
                        <option value="1" {{ request('stage') == '1' ? 'selected' : '' }}>المرحلة 1</option>
                        <option value="2" {{ request('stage') == '2' ? 'selected' : '' }}>المرحلة 2</option>
                        <option value="3" {{ request('stage') == '3' ? 'selected' : '' }}>المرحلة 3</option>
                        <option value="4" {{ request('stage') == '4' ? 'selected' : '' }}>المرحلة 4</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">العامل</label>
                    <select name="worker_id" class="select-input">
                        <option value="">كل العمال</option>
                        @foreach($workers as $worker)
                        <option value="{{ $worker->id }}" {{ request('worker_id') == $worker->id ? 'selected' : '' }}>
                            {{ $worker->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">الحالة</label>
                    <select name="delay_status" class="select-input">
                        <option value="">كل الحالات</option>
                        <option value="normal" {{ request('delay_status') == 'normal' ? 'selected' : '' }}>عادي</option>
                        <option value="warning" {{ request('delay_status') == 'warning' ? 'selected' : '' }}>تحذير</option>
                        <option value="critical" {{ request('delay_status') == 'critical' ? 'selected' : '' }}>حرج</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">تاريخ البداية</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="select-input">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="refresh-btn">
                    تطبيق الفلاتر
                </button>
                <a href="{{ route('manufacturing.reports.wip') }}" class="reset-btn">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- جدول البيانات -->
    <div class="data-table-container">
        <div class="table-header">
            <h3 class="table-title">تفاصيل القطع العالقة ({{ number_format($stuckItems->count()) }} قطعة)</h3>
        </div>

        @if($stuckItems->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>رقم القطعة</th>
                        <th>المرحلة</th>
                        <th>العامل</th>
                        <th>تاريخ البدء</th>
                        <th>وقت التأخير</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stuckItems as $item)
                    <tr>
                        <td><strong>{{ $item->barcode }}</strong></td>
                        <td>{{ $item->stage_name }}</td>
                        <td>{{ $item->worker_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->started_at)->format('Y-m-d H:i') }}</td>
                        <td>
                            <span class="delay-badge delay-{{
                                $item->delay_hours < 24 ? 'normal' :
                                ($item->delay_hours < 48 ? 'warning' : 'critical')
                            }}">
                                {{ number_format($item->delay_hours, 1) }} ساعة
                            </span>
                        </td>
                        <td>
                            <span class="status-text status-{{
                                $item->delay_hours < 24 ? 'normal' :
                                ($item->delay_hours < 48 ? 'warning' : 'critical')
                            }}">
                                {{ $item->delay_hours < 24 ? 'عادي' : ($item->delay_hours < 48 ? 'تحذير' : 'حرج') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <div class="empty-icon">✅</div>
                <h3>ممتاز! لا توجد قطع عالقة</h3>
                <p>جميع القطع تسير بشكل طبيعي في مراحل الإنتاج</p>
            </div>
        @endif
    </div>
</div>

@endsection
