@extends('master')

@section('title', 'لوحة تحكم الوردية')

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
    .shift-container {
        font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: var(--spacing-lg);
        background-color: var(--gray-50);
        min-height: calc(100vh - 60px);
    }

    /* رأس التقرير */
    .shift-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        padding: var(--spacing-xl);
        border-radius: var(--radius-xl);
        color: var(--white);
        margin-bottom: var(--spacing-xl);
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
    }

    .shift-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
        pointer-events: none;
    }

    .shift-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .shift-info {
        display: flex;
        gap: var(--spacing-lg);
        align-items: center;
        margin-top: var(--spacing-md);
        flex-wrap: wrap;
    }

    .shift-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: var(--spacing-sm) var(--spacing-md);
        border-radius: var(--radius-md);
        font-weight: 600;
        background: rgba(255, 255, 255, 0.2);
        font-size: 0.875rem;
    }

    .live-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--success-color);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* عناصر التحكم */
    .controls-bar {
        display: flex;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-xl);
        flex-wrap: wrap;
        align-items: center;
        background: var(--white);
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-200);
        position: relative;
        overflow: hidden;
    }

    .controls-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .control-group {
        display: flex;
        gap: var(--spacing-xs);
        align-items: center;
    }

    .control-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
    }

    .select-input {
        padding: var(--spacing-sm) var(--spacing-md);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        transition: all var(--transition-base);
        background: var(--white);
        font-family: inherit;
    }

    .select-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 102, 178, 0.1);
    }

    .refresh-btn {
        padding: var(--spacing-sm) var(--spacing-lg);
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: var(--white);
        border: none;
        border-radius: var(--radius-md);
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all var(--transition-base);
        box-shadow: var(--shadow-sm);
        font-family: inherit;
    }

    .refresh-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .last-update {
        font-size: 0.75rem;
        color: var(--gray-600);
        margin-right: auto;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* تنبيه القطع العالقة */
    .wip-alert {
        background: linear-gradient(135deg, rgba(254, 243, 199, 0.8) 0%, rgba(253, 230, 138, 0.8) 100%);
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        margin-bottom: var(--spacing-xl);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-200);
        position: relative;
        overflow: hidden;
    }

    .wip-alert::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--warning-color), #fbbf24);
    }

    .wip-alert.critical {
        background: linear-gradient(135deg, rgba(254, 226, 226, 0.8) 0%, rgba(254, 202, 202, 0.8) 100%);
    }

    .wip-alert.critical::before {
        background: linear-gradient(90deg, var(--danger-color), #f87171);
    }

    .wip-alert-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .wip-alert-title {
        font-weight: 600;
        color: #92400e;
        margin: 0;
    }

    .wip-alert.critical .wip-alert-title {
        color: #991b1b;
    }

    .wip-alert p {
        margin: 0;
        color: #92400e;
        font-size: 0.875rem;
    }

    .wip-alert.critical p {
        color: #991b1b;
    }

    .wip-alert a {
        font-weight: 600;
        text-decoration: underline;
        color: inherit;
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

    .stat-card.danger::before {
        background: linear-gradient(90deg, var(--danger-color), #f87171);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: var(--spacing-md);
    }

    .stat-title {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
        line-height: 1;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon.primary {
        background: rgba(59, 130, 246, 0.1);
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

    .stat-icon.danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }

    /* بطاقات المراحل */
    .stage-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-xl);
    }

    .stage-card {
        background: var(--white);
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        transition: all var(--transition-base);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .stage-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
    }

    .stage-card.stage-1::before {
        background: linear-gradient(90deg, #3b82f6, #60a5fa);
    }

    .stage-card.stage-2::before {
        background: linear-gradient(90deg, #f59e0b, #fbbf24);
    }

    .stage-card.stage-3::before {
        background: linear-gradient(90deg, #8b5cf6, #a78bfa);
    }

    .stage-card.stage-4::before {
        background: linear-gradient(90deg, #10b981, #34d399);
    }

    .stage-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary-color);
    }

    .stage-name {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: var(--spacing-sm);
        font-weight: 600;
    }

    .stage-items {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
    }

    /* حاويات الرسوم البيانية */
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
    }

    .chart-container {
        background: var(--white);
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-200);
        position: relative;
        overflow: hidden;
    }

    .chart-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--spacing-lg);
        padding-bottom: var(--spacing-md);
        border-bottom: 1px solid var(--gray-200);
    }

    .chart-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
    }

    /* حاوية الرسم البياني */
    .chart-wrapper {
        position: relative;
        height: 300px;
        width: 100%;
    }

    /* لوحة المتصدرين */
    .leaderboard {
        background: var(--white);
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-200);
        position: relative;
        overflow: hidden;
    }

    .leaderboard::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .leaderboard-item {
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
        padding: var(--spacing-md);
        border-bottom: 1px solid var(--gray-200);
        transition: all var(--transition-base);
    }

    .leaderboard-item:last-child {
        border-bottom: none;
    }

    .leaderboard-item:hover {
        background: var(--gray-50);
    }

    .rank-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.125rem;
    }

    .rank-1 {
        background: #fbbf24;
        color: white;
    }

    .rank-2 {
        background: #9ca3af;
        color: white;
    }

    .rank-3 {
        background: #d97706;
        color: white;
    }

    .rank-other {
        background: var(--gray-200);
        color: var(--gray-600);
    }

    .worker-info {
        flex: 1;
    }

    .worker-name {
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
    }

    .worker-stats {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin: 0.25rem 0 0 0;
    }

    .efficiency-badge {
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .efficiency-excellent {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .efficiency-good {
        background: rgba(59, 130, 246, 0.1);
        color: var(--info-color);
    }

    .efficiency-average {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    /* استجابة الشاشات */
    @media (max-width: 768px) {
        .shift-container {
            padding: var(--spacing-md);
        }

        .shift-header {
            padding: var(--spacing-lg);
        }

        .shift-header h1 {
            font-size: 1.5rem;
        }

        .shift-info {
            flex-direction: column;
            align-items: flex-start;
            gap: var(--spacing-sm);
        }

        .controls-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .control-group {
            justify-content: space-between;
        }

        .last-update {
            margin-right: 0;
            justify-content: center;
        }

        .stats-grid,
        .stage-grid,
        .charts-grid {
            grid-template-columns: 1fr;
        }

        .chart-container {
            padding: var(--spacing-md);
        }

        .chart-header {
            flex-direction: column;
            align-items: flex-start;
            gap: var(--spacing-sm);
        }

        .chart-wrapper {
            height: 250px;
        }
    }
</style>

<div class="shift-container">
    <div class="shift-header">
        <h1>
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            لوحة تحكم الوردية
        </h1>
        <div class="shift-info">
            <span class="shift-badge">
                <span class="live-indicator"></span>
                وردية {{ $shiftType == 'morning' ? 'صباحية' : 'مسائية' }}
            </span>
            <span style="opacity: 0.9;">{{ $date }}</span>
            @php
                $timeRange = (new \Modules\Manufacturing\Http\Controllers\ShiftDashboardController())->getShiftTimeRange($date, $shiftType);
                $shiftStart = $timeRange['start'];
                $shiftEnd = $timeRange['end'];
            @endphp
            <span style="opacity: 0.9;">{{ date('H:i', strtotime($shiftStart)) }} - {{ date('H:i', strtotime($shiftEnd)) }}</span>
        </div>
    </div>

    <!-- عناصر التحكم -->
    <div class="controls-bar">
        <div class="control-group">
            <label class="control-label">التاريخ:</label>
            <input type="date" class="select-input" id="dateFilter" value="{{ $date }}" onchange="applyFilters()">
        </div>

        <div class="control-group">
            <label class="control-label">الوردية:</label>
            <select class="select-input" id="shiftFilter" onchange="applyFilters()">
                <option value="morning" {{ $shiftType == 'morning' ? 'selected' : '' }}>صباحية (6 ص - 6 م)</option>
                <option value="evening" {{ $shiftType == 'evening' ? 'selected' : '' }}>مسائية (6 م - 6 ص)</option>
            </select>
        </div>

        <button class="refresh-btn" onclick="refreshData()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
            تحديث
        </button>

        <div class="last-update">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            آخر تحديث: <span id="lastUpdateTime">الآن</span>
        </div>
    </div>

    <!-- تنبيه القطع العالقة -->
    @if($wipCount > 0)
        <div class="wip-alert {{ $wipCount > 50 ? 'critical' : '' }}">
            <div class="wip-alert-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <h3 class="wip-alert-title">تنبيه: قطع قيد التشغيل</h3>
            </div>
            <p>
                يوجد {{ $wipCount }} قطعة عالقة في مراحل الإنتاج. يرجى المتابعة.
                <a href="{{ route('manufacturing.reports.wip') }}">عرض التفاصيل</a>
            </p>
        </div>
    @endif

    <!-- الإحصائيات الرئيسية -->
    <div class="stats-grid" id="statsGrid">
        <div class="stat-card primary">
            <div class="stat-header">
                <div>
                    <div class="stat-title">إجمالي القطع المنتجة</div>
                    <p class="stat-value">{{ number_format($summary['total_items']) }}</p>
                </div>
                <div class="stat-icon primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-header">
                <div>
                    <div class="stat-title">إجمالي الإنتاج</div>
                    <p class="stat-value">{{ number_format($summary['total_output_kg'], 2) }}</p>
                    <small style="color: var(--gray-600);">كيلوجرام</small>
                </div>
                <div class="stat-icon success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-header">
                <div>
                    <div class="stat-title">نسبة الهدر</div>
                    <p class="stat-value">{{ number_format($summary['waste_percentage'], 2) }}%</p>
                    <small style="color: var(--gray-600);">{{ number_format($summary['total_waste_kg'], 2) }} كجم</small>
                </div>
                <div class="stat-icon warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-header">
                <div>
                    <div class="stat-title">الكفاءة الإجمالية</div>
                    <p class="stat-value">{{ number_format($summary['efficiency'], 1) }}%</p>
                </div>
                <div class="stat-icon success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- الإنتاج حسب المرحلة -->
    <div class="chart-container">
        <div class="chart-header">
            <h3 class="chart-title">الإنتاج حسب المرحلة</h3>
        </div>
        <div class="stage-grid">
            @foreach($byStage as $stage)
                <div class="stage-card stage-{{ $stage['stage'] }}">
                    <div class="stage-name">{{ $stage['name'] }}</div>
                    <p class="stage-items">{{ number_format($stage['items']) }} قطعة</p>
                    <div style="font-size: 0.75rem; color: var(--gray-600); margin-top: 0.5rem;">
                        {{ number_format($stage['output'], 2) }} كجم
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- الرسوم البيانية -->
    <div class="charts-grid">
        <!-- الإنتاج بالساعة -->
        <div class="chart-container">
            <div class="chart-header">
                <h3 class="chart-title">الإنتاج بالساعة</h3>
            </div>
            <div class="chart-wrapper">
                <canvas id="hourlyChart"></canvas>
            </div>
        </div>

        <!-- توزيع الإنتاج -->
        <div class="chart-container">
            <div class="chart-header">
                <h3 class="chart-title">توزيع الإنتاج</h3>
            </div>
            <div class="chart-wrapper">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- أفضل أداء في الوردية -->
    <div class="leaderboard">
        <div class="chart-header">
            <h3 class="chart-title">أفضل أداء في الوردية</h3>
        </div>
        @forelse($topPerformers as $index => $performer)
            <div class="leaderboard-item">
                <div class="rank-number rank-{{ $index < 3 ? ($index + 1) : 'other' }}">
                    {{ $index + 1 }}
                </div>
                <div class="worker-info">
                    <p class="worker-name">{{ $performer->name }}</p>
                    <p class="worker-stats">
                        {{ $performer->items }} قطعة • {{ number_format($performer->output, 2) }} كجم
                    </p>
                </div>
                <div class="efficiency-badge efficiency-{{ $performer->efficiency >= 95 ? 'excellent' : ($performer->efficiency >= 90 ? 'good' : 'average') }}">
                    {{ number_format($performer->efficiency, 1) }}% كفاءة
                </div>
            </div>
        @empty
            <p style="text-align: center; color: var(--gray-600); padding: 2rem;">لا توجد بيانات لهذه الوردية</p>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Hourly Production Chart
    const hourlyData = @json($hourlyTrend);
    const hourlyHours = hourlyData.map(d => d.hour + ':00');
    const hourlyItems = hourlyData.map(d => d.items);

    new Chart(document.getElementById('hourlyChart'), {
        type: 'bar',
        data: {
            labels: hourlyHours,
            datasets: [{
                label: 'القطع المنتجة',
                data: hourlyItems,
                backgroundColor: '#3b82f6',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Stage Distribution Chart
    const stageData = @json($byStage);
    const stageLabels = stageData.map(s => s.name);
    const stageItems = stageData.map(s => s.items);

    new Chart(document.getElementById('distributionChart'), {
        type: 'doughnut',
        data: {
            labels: stageLabels,
            datasets: [{
                data: stageItems,
                backgroundColor: ['#3b82f6', '#f59e0b', '#8b5cf6', '#10b981'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Auto-refresh functions
    function applyFilters() {
        const date = document.getElementById('dateFilter').value;
        const shift = document.getElementById('shiftFilter').value;
        window.location.href = `{{ route('manufacturing.reports.shift-dashboard') }}?date=${date}&shift=${shift}`;
    }

    function refreshData() {
        location.reload();
    }

    // Auto-refresh every 60 seconds
    let autoRefreshInterval = setInterval(() => {
        refreshData();
    }, 60000);

    // Update last update time
    setInterval(() => {
        const now = new Date();
        const seconds = Math.floor((now - startTime) / 1000);
        document.getElementById('lastUpdateTime').textContent = seconds < 60 ? 'الآن' : `منذ ${seconds} ثانية`;
    }, 5000);

    const startTime = new Date();
</script>

@endsection
