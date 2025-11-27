@extends('master')

@section('title', 'تقرير أداء العمال')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/shift-dashboard.css') }}">
@endpush

@section('content')
<style>
    /* تخصيصات إضافية خاصة بتقرير أداء العمال */
    .perf-container {
        padding: 0;
        background: transparent;
    }

    .perf-header h1 svg {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }

    .btn-view {
        padding: var(--spacing-xs) var(--spacing-md);
        font-size: 0.8rem;
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-light-blue) 100%);
        color: var(--white);
        border-radius: var(--radius-sm);
        text-decoration: none;
        transition: all var(--transition-base);
        font-weight: 600;
        box-shadow: var(--shadow-sm);
        border: none;
        cursor: pointer;
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .workers-table-container {
        background: var(--white);
        border-radius: var(--radius-xl);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-200);
        position: relative;
        overflow: hidden;
        margin-top: var(--spacing-xl);
    }

    .workers-table-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-light-blue));
    }

    .workers-table {
        width: 100%;
        border-collapse: collapse;
    }

    .workers-table thead {
        background: var(--gray-50);
    }

    .workers-table th {
        padding: var(--spacing-md);
        text-align: right;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--gray-300);
    }

    .workers-table td {
        padding: var(--spacing-md);
        font-size: 0.875rem;
        color: var(--gray-800);
        border-bottom: 1px solid var(--gray-200);
    }

    .workers-table tbody tr {
        transition: all var(--transition-base);
        cursor: pointer;
    }

    .workers-table tbody tr:hover {
        background: rgba(0, 102, 178, 0.05);
    }

    .worker-name {
        font-weight: 600;
        color: var(--gray-800);
    }

    .efficiency-badge {
        display: inline-flex;
        align-items: center;
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-full);
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
        border: 2px solid transparent;
        transition: all var(--transition-base);
    }

    .efficiency-badge.excellent {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.15) 100%);
        color: var(--success-color);
        border-color: rgba(16, 185, 129, 0.2);
    }

    .efficiency-badge.good {
        background: linear-gradient(135deg, rgba(0, 102, 178, 0.1) 0%, rgba(58, 143, 199, 0.15) 100%);
        color: var(--primary-blue);
        border-color: rgba(0, 102, 178, 0.2);
    }

    .efficiency-badge.average {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(251, 191, 36, 0.15) 100%);
        color: var(--warning-color);
        border-color: rgba(245, 158, 11, 0.2);
    }

    .efficiency-badge.poor {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(248, 113, 113, 0.15) 100%);
        color: var(--danger-color);
        border-color: rgba(239, 68, 68, 0.2);
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: var(--gray-200);
        border-radius: 3px;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .progress-fill {
        height: 100%;
        border-radius: 3px;
        transition: width var(--transition-base);
    }

    .progress-fill.excellent {
        background: var(--success-color);
    }

    .progress-fill.good {
        background: var(--primary-blue);
    }

    .progress-fill.average {
        background: var(--warning-color);
    }

    .progress-fill.poor {
        background: var(--danger-color);
    }

    /* بطاقات المتصدرين */
    .top-performers {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: var(--spacing-lg);
        margin-top: var(--spacing-xl);
    }

    .performer-card {
        padding: var(--spacing-lg);
        border-radius: var(--radius-xl);
        color: var(--white);
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark-blue) 100%);
    }

    .performer-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
        pointer-events: none;
    }

    .performer-card.efficiency {
        background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
    }

    .performer-card.productivity {
        background: linear-gradient(135deg, var(--primary-blue) 0%, #4f46e5 100%);
    }

    .performer-card h3 {
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .performer-card p {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .performer-card .detail {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
        font-size: 0.875rem;
    }

    /* استجابة الشاشات */
    @media (max-width: 768px) {
        .perf-container {
            padding: var(--spacing-md);
        }

        .perf-header {
            padding: var(--spacing-lg);
        }

        .perf-header h1 {
            font-size: 1.5rem;
        }

        .stats-grid,
        .filters-grid,
        .top-performers {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-direction: column;
        }

        .workers-table {
            display: block;
            overflow-x: auto;
        }

        .workers-table th,
        .workers-table td {
            padding: var(--spacing-sm);
        }
    }
</style>

<div class="shift-container perf-container">
    <div class="shift-header perf-header">
        <h1>
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <polyline points="17 11 19 13 23 9"></polyline>
            </svg>
            تقرير أداء العمال
        </h1>
        <p>تحليل شامل لأداء العمال وإنتاجيتهم في جميع المراحل</p>
    </div>

    <!-- الإحصائيات العامة -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-header">
                <div>
                    <div class="stat-title">إجمالي العمال</div>
                    <p class="stat-value">{{ $overallStats['total_workers'] }}</p>
                </div>
                <div class="stat-icon primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-header">
                <div>
                    <div class="stat-title">إجمالي القطع المنتجة</div>
                    <p class="stat-value">{{ number_format($overallStats['total_items']) }}</p>
                </div>
                <div class="stat-icon success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-header">
                <div>
                    <div class="stat-title">إجمالي الإنتاج (كجم)</div>
                    <p class="stat-value">{{ number_format($overallStats['total_output'], 2) }}</p>
                </div>
                <div class="stat-icon warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-header">
                <div>
                    <div class="stat-title">متوسط الكفاءة</div>
                    <p class="stat-value">{{ number_format($overallStats['avg_efficiency'], 1) }}%</p>
                </div>
                <div class="stat-icon success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="chart-container">
        <div class="chart-header">
            <h3 class="chart-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                فلترة البيانات
            </h3>
        </div>
        <form method="GET" action="{{ route('manufacturing.reports.worker-performance') }}">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="control-label">من تاريخ</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="select-input">
                </div>

                <div class="filter-group">
                    <label class="control-label">إلى تاريخ</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="select-input">
                </div>

                <div class="filter-group">
                    <label class="control-label">نوع الوردية</label>
                    <select name="shift_type" class="select-input">
                        <option value="">جميع الورديات</option>
                        <option value="morning" {{ request('shift_type') == 'morning' ? 'selected' : '' }}>الفترة الأولى (صباحي)</option>
                        <option value="evening" {{ request('shift_type') == 'evening' ? 'selected' : '' }}>الفترة الثانية (مسائي)</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="control-label">المرحلة</label>
                    <select name="stage" class="select-input">
                        <option value="">جميع المراحل</option>
                        <option value="1" {{ request('stage') == '1' ? 'selected' : '' }}>المرحلة 1</option>
                        <option value="2" {{ request('stage') == '2' ? 'selected' : '' }}>المرحلة 2</option>
                        <option value="3" {{ request('stage') == '3' ? 'selected' : '' }}>المرحلة 3</option>
                        <option value="4" {{ request('stage') == '4' ? 'selected' : '' }}>المرحلة 4</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                @can('REPORTS_WORKER_PERFORMANCE_VIEW')
                <button type="submit" class="refresh-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    بحث
                </button>
                <a href="{{ route('manufacturing.reports.worker-performance') }}" class="reset-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="1 4 1 10 7 10"></polyline>
                        <path d="M3.51 9A9 9 0 0 1 5.64 5.64"></path>
                    </svg>
                    إعادة تعيين
                </a>
                @endcan
            </div>
        </form>
    </div>

    <!-- جدول أداء العمال -->
    <div class="workers-table-container">
        <table class="workers-table">
            <thead>
                <tr>
                    <th>الترتيب</th>
                    <th>اسم العامل</th>
                    <th>إجمالي القطع</th>
                    <th>الإنتاج (كجم)</th>
                    <th>الهدر (كجم)</th>
                    <th>نسبة الهدر</th>
                    <th>الكفاءة</th>
                    <th>تفاصيل</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workers as $index => $worker)
                    <tr onclick="window.location='{{ route('manufacturing.reports.worker-performance.show', $worker['worker_id']) }}?date_from={{ $dateFrom }}&date_to={{ $dateTo }}'">
                        <td><strong>#{{ $index + 1 }}</strong></td>
                        <td class="worker-name">{{ $worker['worker_name'] }}</td>
                        <td>{{ number_format($worker['totals']['items']) }}</td>
                        <td>{{ number_format($worker['totals']['output'], 2) }}</td>
                        <td>{{ number_format($worker['totals']['waste'], 2) }}</td>
                        <td>{{ number_format($worker['totals']['waste_pct'], 2) }}%</td>
                        <td>
                            @php
                                $efficiency = $worker['totals']['efficiency'];
                                $class = $efficiency >= 95 ? 'excellent' : ($efficiency >= 90 ? 'good' : ($efficiency >= 85 ? 'average' : 'poor'));
                            @endphp
                            <span class="efficiency-badge {{ $class }}">
                                {{ number_format($efficiency, 1) }}%
                            </span>
                            <div class="progress-bar">
                                <div class="progress-fill {{ $class }}" style="width: {{ $efficiency }}%"></div>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('manufacturing.reports.worker-performance.show', $worker['worker_id']) }}?date_from={{ $dateFrom }}&date_to={{ $dateTo }}" class="btn-view" onclick="event.stopPropagation()">
                                عرض التفاصيل
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 3rem; color: var(--gray-600);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 1rem; opacity: 0.5;">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <p style="margin: 0; font-size: 1rem;">لا توجد بيانات للفترة المحددة</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- المتصدرون -->
    @if($overallStats['top_performer'] && $overallStats['most_productive'])
    <div class="top-performers">
        <div class="performer-card efficiency">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
                الأكثر كفاءة
            </h3>
            <p>{{ $overallStats['top_performer']['worker_name'] }}</p>
            <div class="detail">كفاءة: {{ number_format($overallStats['top_performer']['totals']['efficiency'], 1) }}%</div>
        </div>

        <div class="performer-card productivity">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                </svg>
                الأكثر إنتاجية
            </h3>
            <p>{{ $overallStats['most_productive']['worker_name'] }}</p>
            <div class="detail">{{ number_format($overallStats['most_productive']['totals']['items']) }} قطعة</div>
        </div>
    </div>
    @endif
</div>

@endsection
