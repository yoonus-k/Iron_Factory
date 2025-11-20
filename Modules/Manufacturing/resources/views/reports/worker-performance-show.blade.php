@extends('master')

@section('title', 'تفاصيل أداء العامل - ' . $worker->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/shift-dashboard.css') }}">
@endpush

@section('content')
<style>
    /* تخصيصات إضافية خاصة بتفاصيل أداء العامل */
    .worker-detail-container {
        padding: 0;
        background: transparent;
    }
    
    .detail-header h1 svg {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }
    
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        margin-bottom: 1rem;
        transition: all var(--transition-base);
        padding: var(--spacing-xs) var(--spacing-sm);
        border-radius: var(--radius-sm);
    }
    
    .back-btn:hover {
        color: var(--white);
        background: rgba(255, 255, 255, 0.1);
    }
    
    /* بطاقات المقاييس */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
    }
    
    .metric-card {
        background: var(--white);
        padding: var(--spacing-lg);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-200);
        transition: all var(--transition-base);
        position: relative;
        overflow: hidden;
    }
    
    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-light-blue));
    }
    
    .metric-card.primary::before {
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-light-blue));
    }
    
    .metric-card.success::before {
        background: linear-gradient(90deg, var(--success-color), #34d399);
    }
    
    .metric-card.warning::before {
        background: linear-gradient(90deg, var(--warning-color), #fbbf24);
    }
    
    .metric-card.danger::before {
        background: linear-gradient(90deg, var(--danger-color), #f87171);
    }
    
    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary-blue);
    }
    
    .metric-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: var(--spacing-md);
    }
    
    .metric-title {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
        line-height: 1;
    }
    
    .metric-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .metric-icon.primary {
        background: linear-gradient(135deg, rgba(0, 102, 178, 0.1) 0%, rgba(58, 143, 199, 0.15) 100%);
        color: var(--primary-blue);
    }
    
    .metric-icon.success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.15) 100%);
        color: var(--success-color);
    }
    
    .metric-icon.warning {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(251, 191, 36, 0.15) 100%);
        color: var(--warning-color);
    }
    
    .metric-icon.danger {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(248, 113, 113, 0.15) 100%);
        color: var(--danger-color);
    }
    
    /* بطاقات المراحل */
    .stage-performance {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-xl);
    }
    
    .stage-card {
        background: var(--white);
        padding: var(--spacing-lg) var(--spacing-xl);
        border-radius: var(--radius-xl);
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
        height: 4px;
    }
    
    .stage-card.stage1::before {
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-light-blue));
    }
    
    .stage-card.stage2::before {
        background: linear-gradient(90deg, var(--warning-color), #fbbf24);
    }
    
    .stage-card.stage3::before {
        background: linear-gradient(90deg, #8b5cf6, #a78bfa);
    }
    
    .stage-card.stage4::before {
        background: linear-gradient(90deg, var(--success-color), #34d399);
    }
    
    .stage-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-blue);
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
    
    .stage-details {
        display: flex;
        justify-content: space-between;
        margin-top: var(--spacing-sm);
        font-size: 0.75rem;
        color: var(--gray-600);
    }
    
    /* بطاقة المقارنة */
    .comparison-card {
        background: var(--white);
        padding: var(--spacing-lg);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-200);
        position: relative;
        overflow: hidden;
    }
    
    .comparison-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-light-blue));
    }
    
    .comparison-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--spacing-md) 0;
        border-bottom: 1px solid var(--gray-200);
    }
    
    .comparison-row:last-child {
        border-bottom: none;
    }
    
    .comparison-label {
        font-size: 0.875rem;
        color: var(--gray-600);
    }
    
    .comparison-values {
        display: flex;
        gap: 2rem;
        align-items: center;
    }
    
    .comparison-worker {
        text-align: center;
    }
    
    .comparison-worker-label {
        font-size: 0.75rem;
        color: var(--gray-500);
        margin-bottom: 0.25rem;
    }
    
    .comparison-worker-value {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-800);
    }
    
    /* شارة المرتبة */
    .rank-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-full);
        font-weight: 600;
        font-size: 0.875rem;
        white-space: nowrap;
        background: linear-gradient(135deg, rgba(0, 102, 178, 0.1) 0%, rgba(58, 143, 199, 0.15) 100%);
        color: var(--primary-blue);
        border: 2px solid rgba(0, 102, 178, 0.2);
    }
    
    .rank-badge.top {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.15) 100%);
        color: var(--success-color);
        border-color: rgba(16, 185, 129, 0.2);
    }
    
    .rank-badge.mid {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(96, 165, 250, 0.15) 100%);
        color: var(--info-color);
        border-color: rgba(59, 130, 246, 0.2);
    }
    
    .rank-badge.low {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(251, 191, 36, 0.15) 100%);
        color: var(--warning-color);
        border-color: rgba(245, 158, 11, 0.2);
    }
    
    /* حاوية الرسم البياني */
    .chart-wrapper {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    /* استجابة الشاشات */
    @media (max-width: 768px) {
        .worker-detail-container {
            padding: var(--spacing-md);
        }
        
        .detail-header {
            padding: var(--spacing-lg);
        }
        
        .detail-header h1 {
            font-size: 1.5rem;
        }
        
        .metrics-grid,
        .stage-performance {
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
        
        .comparison-values {
            flex-direction: column;
            gap: var(--spacing-xs);
            align-items: flex-end;
        }
    }
</style>

<div class="shift-container worker-detail-container">
    <div class="shift-header detail-header">
        <a href="{{ route('manufacturing.reports.worker-performance') }}" class="back-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            العودة إلى القائمة
        </a>
        <h1>
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            تفاصيل أداء العامل: {{ $worker->name }}
        </h1>
        <p style="opacity: 0.9; margin: 0;">الفترة من {{ $dateFrom }} إلى {{ $dateTo }}</p>
    </div>

    <!-- المقاييس الرئيسية -->
    <div class="metrics-grid">
        <div class="metric-card primary">
            <div class="metric-header">
                <div>
                    <div class="metric-title">إجمالي القطع المنتجة</div>
                    <p class="metric-value">{{ number_format($metrics['total_items']) }}</p>
                </div>
                <div class="metric-icon primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                </div>
            </div>
            <small style="color: var(--gray-600);">{{ $metrics['avg_items_per_day'] }} قطعة/يوم</small>
        </div>

        <div class="metric-card success">
            <div class="metric-header">
                <div>
                    <div class="metric-title">إجمالي الإنتاج</div>
                    <p class="metric-value">{{ number_format($metrics['total_output_kg'], 2) }}</p>
                    <small style="color: var(--gray-600);">كيلوجرام</small>
                </div>
                <div class="metric-icon success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="metric-card warning">
            <div class="metric-header">
                <div>
                    <div class="metric-title">نسبة الهدر</div>
                    <p class="metric-value">{{ number_format($metrics['avg_waste_percentage'], 2) }}%</p>
                    <small style="color: var(--gray-600);">{{ number_format($metrics['total_waste_kg'], 2) }} كجم</small>
                </div>
                <div class="metric-icon warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="metric-card success">
            <div class="metric-header">
                <div>
                    <div class="metric-title">الكفاءة الإجمالية</div>
                    <p class="metric-value">{{ number_format($metrics['efficiency'], 1) }}%</p>
                </div>
                <div class="metric-icon success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
            </div>
            <div style="margin-top: 0.5rem;">
                @php
                    $rankClass = $teamComparison['rank'] <= 3 ? 'top' : ($teamComparison['rank'] <= 10 ? 'mid' : 'low');
                @endphp
                <span class="rank-badge {{ $rankClass }}">
                    المرتبة #{{ $teamComparison['rank'] }} من {{ $teamComparison['total_workers'] }}
                </span>
            </div>
        </div>

        <div class="metric-card primary">
            <div class="metric-header">
                <div>
                    <div class="metric-title">أيام العمل</div>
                    <p class="metric-value">{{ $metrics['working_days'] }}</p>
                    <small style="color: var(--gray-600);">يوم</small>
                </div>
                <div class="metric-icon primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- الأداء حسب المرحلة -->
    <div class="chart-container">
        <div class="chart-header">
            <h3 class="chart-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 3v18h18"></path>
                    <path d="m19 9-5 5-4-4-3 3"></path>
                </svg>
                الأداء حسب المرحلة
            </h3>
        </div>
        <div class="stage-performance">
            @foreach(['stage1' => 'المرحلة 1 - التقسيم', 'stage2' => 'المرحلة 2 - المعالجة', 'stage3' => 'المرحلة 3 - اللف', 'stage4' => 'المرحلة 4 - التعليب'] as $key => $name)
                <div class="stage-card {{ $key }}">
                    <div class="stage-name">{{ $name }}</div>
                    <p class="stage-items">{{ number_format($byStage[$key]['items']) }} قطعة</p>
                    <div class="stage-details">
                        <span>{{ number_format($byStage[$key]['output'], 2) }} كجم</span>
                        <span>هدر: {{ number_format($byStage[$key]['waste_pct'], 1) }}%</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- الإنتاجية اليومية -->
    <div class="chart-container">
        <div class="chart-header">
            <h3 class="chart-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    <polyline points="17 6 23 6 23 12"></polyline>
                </svg>
                الإنتاجية اليومية
            </h3>
        </div>
        <div class="chart-wrapper">
            <canvas id="dailyTrendChart"></canvas>
        </div>
    </div>

    <!-- المقارنة مع متوسط الفريق -->
    <div class="chart-container">
        <div class="chart-header">
            <h3 class="chart-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                المقارنة مع متوسط الفريق
            </h3>
        </div>
        <div class="chart-wrapper">
            <canvas id="comparisonChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Daily Trend Chart
    const dailyDates = @json(array_keys($dailyTrend));
    const dailyItems = @json(array_column($dailyTrend, 'items'));
    const dailyOutput = @json(array_column($dailyTrend, 'output'));

    new Chart(document.getElementById('dailyTrendChart'), {
        type: 'line',
        data: {
            labels: dailyDates,
            datasets: [{
                label: 'عدد القطع',
                data: dailyItems,
                borderColor: '#0066B2',
                backgroundColor: 'rgba(0, 102, 178, 0.1)',
                yAxisID: 'y',
                tension: 0.4,
                borderWidth: 3
            }, {
                label: 'الوزن (كجم)',
                data: dailyOutput,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                yAxisID: 'y1',
                tension: 0.4,
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'عدد القطع'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'الوزن (كجم)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });

    // Comparison Chart
    @if($teamComparison['worker'])
    new Chart(document.getElementById('comparisonChart'), {
        type: 'bar',
        data: {
            labels: ['عدد القطع', 'الإنتاج (كجم)', 'نسبة الهدر %', 'الكفاءة %'],
            datasets: [{
                label: 'العامل',
                data: [
                    {{ $teamComparison['worker']['items'] }},
                    {{ $teamComparison['worker']['output'] }},
                    {{ $teamComparison['worker']['waste_pct'] }},
                    {{ $teamComparison['worker']['efficiency'] }}
                ],
                backgroundColor: '#0066B2'
            }, {
                label: 'متوسط الفريق',
                data: [
                    {{ $teamComparison['team_avg']['items'] }},
                    {{ $teamComparison['team_avg']['output'] }},
                    {{ $teamComparison['team_avg']['waste_pct'] }},
                    {{ $teamComparison['team_avg']['efficiency'] }}
                ],
                backgroundColor: '#9ca3af'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    @endif
</script>

@endsection