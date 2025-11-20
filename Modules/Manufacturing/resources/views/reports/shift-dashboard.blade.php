@extends('master')

@section('title', 'لوحة تحكم الوردية')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/shift-dashboard.css') }}">
@endpush

@section('content')

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
                <h3 class="chart-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="20" x2="12" y2="10"></line>
                        <line x1="18" y1="20" x2="18" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="16"></line>
                    </svg>
                    الإنتاج بالساعة
                </h3>
            </div>
            <div class="chart-wrapper">
                <canvas id="hourlyChart"></canvas>
            </div>
        </div>

        <!-- توزيع الإنتاج -->
        <div class="chart-container">
            <div class="chart-header">
                <h3 class="chart-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                        <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                    </svg>
                    توزيع الإنتاج
                </h3>
            </div>
            <div class="chart-wrapper">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>

        <!-- اتجاه الكفاءة - جديد -->
        <div class="chart-container">
            <div class="chart-header">
                <h3 class="chart-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    اتجاه الكفاءة خلال الوردية
                </h3>
            </div>
            <div class="chart-wrapper">
                <canvas id="efficiencyTrendChart"></canvas>
            </div>
        </div>

        <!-- مقارنة الإنتاج والهدر - جديد -->
        <div class="chart-container">
            <div class="chart-header">
                <h3 class="chart-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    مقارنة الإنتاج والهدر
                </h3>
            </div>
            <div class="chart-wrapper">
                <canvas id="outputWasteChart"></canvas>
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
    // تكوين الألوان بناءً على ألوان الشعار
    const primaryBlue = '#0066B2';
    const lightBlue = '#3A8FC7';
    const successGreen = '#10b981';
    const warningOrange = '#f59e0b';
    const dangerRed = '#ef4444';
    const grayColor = '#4f5d6c';

    // إعدادات عامة للـ Charts
    Chart.defaults.font.family = 'Cairo, sans-serif';
    Chart.defaults.color = '#475569';

    // 1. Hourly Production Chart
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
                backgroundColor: primaryBlue,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return 'القطع: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // 2. Stage Distribution Chart (Doughnut)
    const stageData = @json($byStage);
    const stageLabels = stageData.map(s => s.name);
    const stageItems = stageData.map(s => s.items);

    new Chart(document.getElementById('distributionChart'), {
        type: 'doughnut',
        data: {
            labels: stageLabels,
            datasets: [{
                data: stageItems,
                backgroundColor: [primaryBlue, warningOrange, '#8b5cf6', successGreen],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 13,
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' قطعة (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // 3. Efficiency Trend Chart (بيانات ثابتة - تصميم فقط)
    const efficiencyTrendData = {
        labels: ['6:00', '8:00', '10:00', '12:00', '14:00', '16:00', '18:00'],
        datasets: [{
            label: 'الكفاءة %',
            data: [85, 88, 92, 95, 93, 96, 94],
            borderColor: primaryBlue,
            backgroundColor: 'rgba(0, 102, 178, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 8,
            pointBackgroundColor: primaryBlue,
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointHoverBackgroundColor: primaryBlue,
            pointHoverBorderColor: '#ffffff',
            pointHoverBorderWidth: 3
        }, {
            label: 'الهدف 90%',
            data: [90, 90, 90, 90, 90, 90, 90],
            borderColor: dangerRed,
            backgroundColor: 'transparent',
            borderWidth: 2,
            borderDash: [10, 5],
            fill: false,
            pointRadius: 0,
            pointHoverRadius: 0
        }]
    };

    new Chart(document.getElementById('efficiencyTrendChart'), {
        type: 'line',
        data: efficiencyTrendData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    min: 80,
                    max: 100,
                    ticks: {
                        stepSize: 5,
                        callback: function(value) {
                            return value + '%';
                        },
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // 4. Output vs Waste Comparison Chart (بيانات ثابتة - تصميم فقط)
    const outputWasteData = {
        labels: ['المرحلة 1', 'المرحلة 2', 'المرحلة 3', 'المرحلة 4'],
        datasets: [{
            label: 'الإنتاج (كجم)',
            data: [450, 380, 520, 410],
            backgroundColor: successGreen,
            borderRadius: 8,
            borderSkipped: false,
        }, {
            label: 'الهدر (كجم)',
            data: [25, 32, 28, 22],
            backgroundColor: dangerRed,
            borderRadius: 8,
            borderSkipped: false,
        }]
    };

    new Chart(document.getElementById('outputWasteChart'), {
        type: 'bar',
        data: outputWasteData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        padding: 15,
                        font: {
                            size: 13,
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' كجم';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' كجم';
                        },
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
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
        const minutes = Math.floor(seconds / 60);

        if (seconds < 60) {
            document.getElementById('lastUpdateTime').textContent = 'الآن';
        } else if (minutes === 1) {
            document.getElementById('lastUpdateTime').textContent = 'منذ دقيقة';
        } else if (minutes < 60) {
            document.getElementById('lastUpdateTime').textContent = `منذ ${minutes} دقيقة`;
        } else {
            const hours = Math.floor(minutes / 60);
            document.getElementById('lastUpdateTime').textContent = `منذ ${hours} ساعة`;
        }
    }, 5000);

    const startTime = new Date();
</script>

@endsection
