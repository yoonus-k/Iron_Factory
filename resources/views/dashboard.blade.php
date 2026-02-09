@extends('master')

@section('content')
<div class="dashboard-wrapper">
    <!-- صفحة الرئيسية/لوحة التحكم -->
    <div class="page-title-section">
        <h1>{{ __('app.menu.dashboard') }}</h1>
        <p class="subtitle">{{ __('messages.welcome') }}</p>
    </div>

    <!-- الإحصائيات الرئيسية -->
    <div class="statistics-grid">
        @forelse($dashboardStats as $stat)
            <div class="stat-card card-{{ $stat['style'] ?? 'primary' }}">
                <div class="stat-header">
                    <i class="{{ $stat['icon'] ?? 'fas fa-chart-area' }}"></i>
                    <h3>{{ $stat['title'] ?? '—' }}</h3>
                </div>
                <div class="stat-value">
                    @if(isset($stat['value']))
                        {{ number_format($stat['value'], $stat['decimals'] ?? 0) }}
                        @if(!empty($stat['unit']))
                            <span class="stat-unit">{{ $stat['unit'] }}</span>
                        @endif
                    @else
                        <span class="stat-placeholder">—</span>
                    @endif
                </div>
                @if(!empty($stat['hint']))
                    <p class="stat-hint">{{ $stat['hint'] }}</p>
                @endif
                @php $trendValue = $stat['trend'] ?? null; @endphp
                @if(!is_null($trendValue))
                    @php $trendPositive = $trendValue >= 0; @endphp
                    <div class="stat-footer">
                        <div class="stat-change {{ $trendPositive ? 'positive' : 'negative' }}">
                            <i class="fas {{ $trendPositive ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                            {{ number_format(abs($trendValue), 1) }}%
                        </div>
                        <span class="stat-period">مقارنة بالأمس</span>
                    </div>
                @endif
            </div>
        @empty
            <div class="stat-card empty-card">
                <div class="stat-header">
                    <h3>لا توجد بيانات جاهزة</h3>
                </div>
                <div class="stat-value">
                    <span class="stat-placeholder">—</span>
                </div>
                <p class="stat-hint">الرجاء التأكد من تسجيل بيانات الإنتاج.</p>
            </div>
        @endforelse
    </div>

    <div class="charts-section">
        <div class="chart-container">
            <div class="chart-header">
                <h3>إنتاج الأسبوع</h3>
                <span class="chart-label">آخر 7 أيام</span>
            </div>
            <div class="chart-content">
                @php
                    $maxWeeklyValue = collect($weeklyProduction ?? [])->max('value') ?? 0;
                    $maxWeeklyValue = $maxWeeklyValue > 0 ? $maxWeeklyValue : 1;
                @endphp
                @if(!empty($weeklyProduction))
                    <div class="chart-bars">
                        @foreach($weeklyProduction as $day)
                            @php
                                $height = ($day['value'] / $maxWeeklyValue) * 100;
                            @endphp
                            <div class="bar-item">
                                <div class="bar-graph" style="height: {{ max(4, $height) }}%;"></div>
                                <span class="bar-label">{{ $day['label'] }}</span>
                                <span class="bar-value">{{ number_format($day['value'], 0) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">لا توجد بيانات للأسبوع الحالي.</div>
                @endif
            </div>
        </div>

        <div class="pie-container">
            <div class="chart-header">
                <h3>توزيع حالات التعبئة</h3>
                <span class="chart-label">آخر 7 أيام</span>
            </div>
            <div class="pie-chart">
                @if(!empty($statusSegments))
                    @php
                        $circumference = 2 * pi() * 55;
                        $offset = 0;
                    @endphp
                    <svg viewBox="0 0 120 120">
                        @foreach($statusSegments as $segment)
                            @php $dash = ($segment['percentage'] / 100) * $circumference; @endphp
                            <circle cx="60" cy="60" r="55" fill="none"
                                    stroke="{{ $segment['color'] }}" stroke-width="20"
                                    stroke-dasharray="{{ $dash }} {{ $circumference }}"
                                    stroke-dashoffset="-{{ $offset }}"></circle>
                            @php $offset += $dash; @endphp
                        @endforeach
                    </svg>
                    <div class="pie-labels">
                        @foreach($statusSegments as $segment)
                            <div class="pie-label">
                                <span class="pie-color" style="background-color: {{ $segment['color'] }};"></span>
                                <span class="pie-text">{{ $segment['label'] }}: {{ number_format($segment['percentage'], 1) }}% ({{ $segment['count'] }})</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">لا توجد بيانات حديثة لحالات التعبئة.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="production-lines">
        <h3 class="section-title">حالة الخطوط الإنتاجية</h3>
        @if(!empty($productionLines))
            <div class="lines-grid">
                @foreach($productionLines as $line)
                    <div class="production-line-card">
                        <div class="line-header">
                            <h4>{{ $line['title'] }}</h4>
                            <span class="status-badge {{ $line['status'] === 'maintenance' ? 'status-maintenance' : 'status-active' }}">
                                {{ $line['status_text'] }}
                            </span>
                        </div>
                        <div class="line-stats">
                            <div class="stat">
                                <span class="stat-label">الإنتاج اليومي:</span>
                                <span class="stat-val">{{ number_format($line['output'], 1) }} كجم</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">الملاحظات:</span>
                                <span class="stat-val">{{ $line['notes'] }}</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">الحالات المعلقة:</span>
                                <span class="stat-val">{{ $line['issues'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">لا توجد بيانات للخطوط الإنتاجية حالياً.</div>
        @endif
    </div>

    <!-- الأنشطة الأخيرة والإشعارات -->

</div>

<style>
    .dashboard-wrapper {
        padding: 20px;
        background-color: #f5f5f5;
    }

    .page-title-section {
        margin-bottom: 30px;
    }

    .page-title-section h1 {
        font-size: 28px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 5px;
    }

    .page-title-section .subtitle {
        color: #666;
        font-size: 14px;
    }

    /* شبكة الإحصائيات */
    .statistics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid #333;
        transition: all 0.3s ease;
    }

    .stat-card.empty-card {
        border-left-color: #d1d5db;
        text-align: center;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .stat-card.card-primary {
        border-left-color: #007bff;
    }

    .stat-card.card-success {
        border-left-color: #28a745;
    }

    .stat-card.card-warning {
        border-left-color: #ffc107;
    }

    .stat-card.card-info {
        border-left-color: #17a2b8;
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .stat-header i {
        font-size: 24px;
        color: #666;
    }

    .stat-header h3 {
        font-size: 14px;
        color: #666;
        margin: 0;
        flex-grow: 1;
        margin-right: 10px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 5px;
    }

    .stat-placeholder {
        color: #c5c5c5;
    }

    .stat-unit {
        font-size: 12px;
        color: #999;
        margin-bottom: 10px;
    }

    .stat-hint {
        font-size: 12px;
        color: #777;
        margin: 5px 0 10px;
    }

    .stat-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid #eee;
    }

    .stat-period {
        font-size: 11px;
        color: #999;
    }

    .stat-change {
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .stat-change.positive {
        color: #28a745;
    }

    .stat-change.negative {
        color: #dc3545;
    }

    /* الرسوم البيانية */
    .charts-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .chart-container,
    .pie-container {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .chart-header h3 {
        margin: 0;
        font-size: 16px;
        color: #1a1a1a;
    }

    .chart-label {
        font-size: 12px;
        color: #999;
    }

    /* المخطط البياني */
    .chart-content {
        min-height: 220px;
    }

    .chart-content .empty-state {
        margin: 0 auto;
    }

    .chart-bars {
        display: flex;
        justify-content: space-around;
        align-items: flex-end;
        height: 200px;
        gap: 10px;
    }

    .bar-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }

    .bar-graph {
        width: 100%;
        background: linear-gradient(to top, #007bff, #4d94ff);
        border-radius: 4px 4px 0 0;
        min-height: 20px;
        transition: all 0.3s ease;
    }

    .bar-graph:hover {
        background: linear-gradient(to top, #0056b3, #2970cc);
    }

    .bar-label {
        font-size: 11px;
        color: #666;
        margin-top: 10px;
    }

    .bar-value {
        font-size: 10px;
        color: #999;
        margin-top: 5px;
    }

    /* الرسم الدائري */
    .pie-chart {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 30px;
    }

    .pie-chart svg {
        width: 120px;
        height: 120px;
    }

    .pie-labels {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .pie-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
    }

    .pie-color {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .pie-text {
        color: #666;
    }

    .empty-state {
        width: 100%;
        padding: 30px 10px;
        text-align: center;
        color: #888;
        background: #fafafa;
        border-radius: 8px;
    }

    /* الخطوط الإنتاجية */
    .production-lines {
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 20px;
    }

    .lines-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .production-line-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .line-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #eee;
    }

    .line-header h4 {
        margin: 0;
        font-size: 14px;
        color: #1a1a1a;
    }

    .status-badge {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 600;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-maintenance {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-error {
        background-color: #f8d7da;
        color: #721c24;
    }

    .line-stats {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .stat {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
    }

    .stat-label {
        color: #666;
        font-weight: 500;
    }

    .stat-val {
        color: #1a1a1a;
        font-weight: 600;
    }

    /* الأنشطة الأخيرة */
    .recent-activities {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .activities-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .activity-item {
        display: flex;
        gap: 15px;
        align-items: flex-start;
        padding: 12px;
        background: #f9f9f9;
        border-radius: 6px;
        border-right: 3px solid #007bff;
    }

    .activity-icon {
        font-size: 18px;
        color: #007bff;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
    }

    .activity-text {
        margin: 0;
        font-size: 13px;
        color: #1a1a1a;
        font-weight: 500;
    }

    .activity-time {
        font-size: 11px;
        color: #999;
        margin-top: 3px;
        display: inline-block;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-wrapper {
            padding: 15px;
        }

        .statistics-grid {
            grid-template-columns: 1fr;
        }

        .charts-section {
            grid-template-columns: 1fr;
        }

        .page-title-section h1 {
            font-size: 22px;
        }

        .chart-bars {
            height: 150px;
        }
    }
</style>
@endsection
