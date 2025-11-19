@extends('master')

@section('title', 'تقرير أداء العمال')

@section('content')
<style>
    /* استيراد الخطوط */
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap');

    /* المتغيرات */
    :root {
        --primary-color: #0066B2;
        --primary-light: #3A8FC7;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #3b82f6;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-600: #4b5563;
        --gray-800: #1f2937;
        --white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
        --radius-xl: 1rem;
        --spacing-xs: 0.25rem;
        --spacing-sm: 0.5rem;
        --spacing-md: 1rem;
        --spacing-lg: 1.5rem;
        --spacing-xl: 2rem;
        --transition-base: 250ms ease;
    }

    /* تنسيقات أساسية */
    .perf-container {
        font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: var(--spacing-lg);
        background-color: var(--gray-50);
        min-height: calc(100vh - 60px);
    }

    /* رأس التقرير */
    .perf-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        padding: var(--spacing-xl);
        border-radius: var(--radius-xl);
        color: var(--white);
        margin-bottom: var(--spacing-xl);
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
    }

    .perf-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
        pointer-events: none;
    }

    .perf-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .perf-header p {
        opacity: 0.9;
        margin: 0;
        font-size: 1rem;
    }

    /* بطاقات الإحصائيات */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
    }

    .stat-card {
        background: var(--white);
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-200);
        transition: all var(--transition-base);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .stat-card.primary::before {
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .stat-card.success::before {
        background: linear-gradient(90deg, var(--success-color), #34d399);
    }

    .stat-card.warning::before {
        background: linear-gradient(90deg, var(--warning-color), #fbbf24);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .stat-header {
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-md);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .stat-icon.primary {
        background: rgba(0, 102, 178, 0.1);
        color: var(--primary-color);
    }

    .stat-icon.success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .stat-icon.warning {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .stat-content h3 {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin: 0 0 0.25rem 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-content p {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0;
        color: var(--gray-800);
        line-height: 1;
    }

    /* بطاقة الفلاتر */
    .filters-card {
        background: var(--white);
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-200);
        margin-bottom: var(--spacing-xl);
        position: relative;
        overflow: hidden;
    }

    .filters-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .filters-card h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0 0 var(--spacing-lg) 0;
        padding-bottom: var(--spacing-md);
        border-bottom: 1px solid var(--gray-200);
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-lg);
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-xs);
    }

    .filter-group label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
    }

    .filter-group select,
    .filter-group input {
        padding: var(--spacing-sm) var(--spacing-md);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        transition: all var(--transition-base);
        background: var(--white);
        font-family: inherit;
    }

    .filter-group select:focus,
    .filter-group input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 102, 178, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: var(--spacing-md);
        flex-wrap: wrap;
    }

    .btn {
        padding: var(--spacing-sm) var(--spacing-lg);
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all var(--transition-base);
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        font-family: inherit;
        box-shadow: var(--shadow-sm);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: var(--white);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--gray-600);
        color: var(--white);
    }

    .btn-secondary:hover {
        background: var(--gray-800);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-view {
        padding: var(--spacing-xs) var(--spacing-md);
        font-size: 0.8rem;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
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

    /* جدول العمال */
    .workers-table-container {
        background: var(--white);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-200);
        position: relative;
        overflow: hidden;
    }

    .workers-table-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
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
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
    }

    .efficiency-badge.excellent {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .efficiency-badge.good {
        background: rgba(59, 130, 246, 0.1);
        color: var(--info-color);
    }

    .efficiency-badge.average {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .efficiency-badge.poor {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
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
        background: var(--info-color);
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
        border-radius: var(--radius-lg);
        color: var(--white);
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
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
        background: linear-gradient(135deg, var(--primary-color) 0%, #4f46e5 100%);
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

<div class="perf-container">
    <div class="perf-header">
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
                <div class="stat-icon primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3>إجمالي العمال</h3>
                    <p>{{ $overallStats['total_workers'] }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-header">
                <div class="stat-icon success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3>إجمالي القطع المنتجة</h3>
                    <p>{{ number_format($overallStats['total_items']) }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-header">
                <div class="stat-icon warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3>إجمالي الإنتاج (كجم)</h3>
                    <p>{{ number_format($overallStats['total_output'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-header">
                <div class="stat-icon success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3>متوسط الكفاءة</h3>
                    <p>{{ number_format($overallStats['avg_efficiency'], 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="filters-card">
        <h3>فلترة البيانات</h3>
        <form method="GET" action="{{ route('manufacturing.reports.worker-performance') }}">
            <div class="filters-grid">
                <div class="filter-group">
                    <label>من تاريخ</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}">
                </div>

                <div class="filter-group">
                    <label>إلى تاريخ</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}">
                </div>

                <div class="filter-group">
                    <label>نوع الوردية</label>
                    <select name="shift_type">
                        <option value="">جميع الورديات</option>
                        <option value="morning" {{ request('shift_type') == 'morning' ? 'selected' : '' }}>الفترة الأولى (صباحي)</option>
                        <option value="evening" {{ request('shift_type') == 'evening' ? 'selected' : '' }}>الفترة الثانية (مسائي)</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>المرحلة</label>
                    <select name="stage">
                        <option value="">جميع المراحل</option>
                        <option value="1" {{ request('stage') == '1' ? 'selected' : '' }}>المرحلة 1</option>
                        <option value="2" {{ request('stage') == '2' ? 'selected' : '' }}>المرحلة 2</option>
                        <option value="3" {{ request('stage') == '3' ? 'selected' : '' }}>المرحلة 3</option>
                        <option value="4" {{ request('stage') == '4' ? 'selected' : '' }}>المرحلة 4</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    بحث
                </button>
                <a href="{{ route('manufacturing.reports.worker-performance') }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="1 4 1 10 7 10"></polyline>
                        <path d="M3.51 9A9 9 0 0 1 5.64 5.64"></path>
                    </svg>
                    إعادة تعيين
                </a>
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
