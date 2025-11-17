@extends('master')

@section('title', 'تقرير تتبع الإنتاج')

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-bar-chart-2"></i>
                تقرير تتبع الإنتاج التفصيلي
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>تتبع الإنتاج</span>
                <i class="feather icon-chevron-left"></i>
                <span>تقرير التتبع</span>
            </nav>
        </div>

        <!-- Barcode Display -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; text-align: center;">
            <h2 style="margin: 0 0 10px 0; font-size: 24px;">{{ $barcode }}</h2>
            <p style="margin: 0; opacity: 0.9;">تاريخ التقرير: {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>

        <!-- Current Status -->
        @if(isset($trackingData['current_location']))
        <div class="um-category-card" style="margin-bottom: 20px;">
            <div class="um-category-card-header">
                <div class="um-category-info">
                    <div class="um-category-icon" style="background: #3498db20; color: #3498db; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                        <i class="feather icon-map-pin" style="font-size: 18px;"></i>
                    </div>
                    <div>
                        <h6 class="um-category-name">الموقع الحالي</h6>
                    </div>
                </div>
                <span class="um-badge um-badge-info">
                    {{ $trackingData['current_location']['stage'] }} - {{ $trackingData['current_location']['action'] }}
                </span>
            </div>
            <div class="um-category-card-body">
                <div class="um-info-row">
                    <span class="um-info-label">
                        <i class="feather icon-clock"></i>
                        الوقت
                    </span>
                    <span class="um-info-value">
                        {{ $trackingData['current_location']['time_ago'] }} ({{ $trackingData['current_location']['formatted_time'] }})
                    </span>
                </div>
            </div>
        </div>
        @endif

        <!-- Summary Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="um-category-card">
                <div class="um-category-card-body">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="background: rgba(52, 152, 219, 0.1); color: #3498db; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="feather icon-weight" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <div style="font-size: 14px; color: #64748b; margin-bottom: 4px;">الوزن الابتدائي</div>
                            <div style="font-size: 24px; font-weight: 700; color: #0f172a;">{{ number_format($trackingData['summary']['total_input'], 2) }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">كيلوجرام</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="um-category-card">
                <div class="um-category-card-body">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="background: rgba(46, 204, 113, 0.1); color: #2ecc71; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="feather icon-check-circle" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <div style="font-size: 14px; color: #64748b; margin-bottom: 4px;">الوزن النهائي</div>
                            <div style="font-size: 24px; font-weight: 700; color: #0f172a;">{{ number_format($trackingData['summary']['total_output'], 2) }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">كيلوجرام</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="um-category-card">
                <div class="um-category-card-body">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="background: rgba(231, 76, 60, 0.1); color: #e74c3c; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="feather icon-trash-2" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <div style="font-size: 14px; color: #64748b; margin-bottom: 4px;">إجمالي الهدر</div>
                            <div style="font-size: 24px; font-weight: 700; color: #0f172a;">{{ number_format($trackingData['summary']['total_waste'], 2) }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">كيلوجرام ({{ $trackingData['summary']['waste_percentage'] }}%)</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="um-category-card">
                <div class="um-category-card-body">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="background: rgba(243, 156, 18, 0.1); color: #f39c12; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="feather icon-clock" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <div style="font-size: 14px; color: #64748b; margin-bottom: 4px;">إجمالي المدة الزمنية</div>
                            <div style="font-size: 24px; font-weight: 700; color: #0f172a;">
                                @if($trackingData['summary']['total_minutes'] > 0)
                                    @if($trackingData['summary']['duration_days'] > 0)
                                        {{ $trackingData['summary']['duration_days'] }}
                                    @elseif($trackingData['summary']['duration_hours'] > 0)
                                        {{ $trackingData['summary']['duration_hours'] }}
                                    @else
                                        {{ $trackingData['summary']['duration_minutes'] }}
                                    @endif
                                @else
                                    <span style="font-size: 20px;">-</span>
                                @endif
                            </div>
                            <div style="font-size: 12px; color: #94a3b8;">
                                @if($trackingData['summary']['total_minutes'] > 0)
                                    @if($trackingData['summary']['duration_days'] > 0)
                                        يوم ({{ $trackingData['summary']['formatted_duration'] }})
                                    @elseif($trackingData['summary']['duration_hours'] > 0)
                                        ساعة ({{ $trackingData['summary']['formatted_duration'] }})
                                    @else
                                        دقيقة
                                    @endif
                                @else
                                    تم في نفس الوقت
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="um-category-card">
                <div class="um-category-card-body">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="background: rgba(155, 89, 182, 0.1); color: #9b59b6; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="feather icon-percent" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <div style="font-size: 14px; color: #64748b; margin-bottom: 4px;">كفاءة الإنتاج</div>
                            <div style="font-size: 24px; font-weight: 700; color: #0f172a;">{{ $trackingData['summary']['efficiency'] }}%</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ $trackingData['summary']['stages_count'] }} مراحل</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Production Journey Timeline -->
        <section class="um-main-card" style="margin-bottom: 30px;">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-activity"></i>
                    رحلة المنتج التفصيلية
                </h4>
            </div>
            <div style="padding: 20px;">
                <div style="position: relative; padding: 20px 0;">
                    <div style="position: absolute; top: 0; bottom: 0; right: 30px; width: 3px; background: linear-gradient(180deg, #3498db, #e74c3c);"></div>
                    
                    @foreach($trackingData['journey'] as $index => $stage)
                    <div style="position: relative; margin-bottom: 40px; min-height: 120px;">
                        <div style="position: absolute; right: 15px; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; z-index: 10; box-shadow: 0 3px 10px rgba(0,0,0,0.2); 
                            background: {{ 
                                $stage['color'] == 'primary' ? '#3498db' :
                                ($stage['color'] == 'info' ? '#1abc9c' :
                                ($stage['color'] == 'success' ? '#27ae60' :
                                ($stage['color'] == 'warning' ? '#f39c12' : '#e74c3c')))
                            }};">
                            <i class="feather icon-{{ $stage['icon'] }}"></i>
                        </div>
                        
                        <div style="margin-right: 70px; background: #f8fafc; border-radius: 12px; padding: 20px; position: relative; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
                            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 15px;">
                                <h6 style="font-size: 18px; font-weight: 600; color: #0f172a; margin: 0 0 8px 0; display: flex; align-items: center; flex-wrap: wrap; gap: 8px;">
                                    <span>
                                        {{ $stage['stage_name'] }}
                                        @if(isset($stage['action_name']))
                                            <span style="font-size: 14px; color: #64748b; font-weight: normal;">
                                                ({{ $stage['action_name'] }})
                                            </span>
                                        @endif
                                    </span>
                                    @if(isset($stage['items_count']) && $stage['items_count'] > 1)
                                        <span class="um-badge um-badge-primary">
                                            {{ $stage['items_count'] }} عنصر
                                        </span>
                                    @endif
                                </h6>
                                <p style="font-size: 13px; color: #64748b; margin: 0;">
                                    <i class="feather icon-calendar"></i> {{ $stage['formatted_time'] }}
                                    <span style="margin-right: 15px; color: #94a3b8;">
                                        <i class="feather icon-clock"></i> {{ $stage['time_ago'] }}
                                    </span>
                                </p>
                            </div>
                            
                            <div style="font-size: 14px; color: #334155;">
                                @if(isset($stage['items_count']) && $stage['items_count'] > 1)
                                    <div class="um-info-row" style="background: #dbeafe; padding: 10px; border-radius: 6px; margin-bottom: 10px;">
                                        <span class="um-info-label" style="color: #1d4ed8; font-weight: 600;">
                                            <i class="feather icon-layers"></i> إجمالي المرحلة:
                                        </span>
                                        <span class="um-info-value" style="color: #1d4ed8; font-weight: 600;">
                                            {{ $stage['items_count'] }} عنصر
                                        </span>
                                    </div>
                                @else
                                    <div class="um-info-row">
                                        <span class="um-info-label">الباركود:</span>
                                        <span class="um-info-value">{{ $stage['barcode'] }}</span>
                                    </div>
                                @endif
                                
                                @if($stage['input_barcode'])
                                <div class="um-info-row">
                                    <span class="um-info-label">مصدر المادة:</span>
                                    <span class="um-info-value">{{ $stage['input_barcode'] }}</span>
                                </div>
                                @endif
                                
                                @if($stage['output_barcode'] && $stage['output_barcode'] != $stage['barcode'])
                                <div class="um-info-row">
                                    <span class="um-info-label">الباركود الناتج:</span>
                                    <span class="um-info-value">{{ $stage['output_barcode'] }}</span>
                                </div>
                                @endif
                                
                                @if($stage['input_weight'] > 0)
                                <div class="um-info-row">
                                    <span class="um-info-label">وزن الدخول:</span>
                                    <span class="um-info-value">{{ number_format($stage['input_weight'], 2) }} كجم</span>
                                </div>
                                @endif
                                
                                @if($stage['output_weight'] > 0)
                                <div class="um-info-row">
                                    <span class="um-info-label">وزن الخروج:</span>
                                    <span class="um-info-value">{{ number_format($stage['output_weight'], 2) }} كجم</span>
                                </div>
                                @endif
                                
                                @if($stage['waste_amount'] > 0)
                                <div class="um-info-row">
                                    <span class="um-info-label">الهدر:</span>
                                    <span class="um-info-value">{{ number_format($stage['waste_amount'], 2) }} كجم ({{ number_format($stage['waste_percentage'], 2) }}%)</span>
                                </div>
                                @endif
                                
                                <div class="um-info-row">
                                    <span class="um-info-label">العامل:</span>
                                    <span class="um-info-value">{{ $stage['worker_name'] }}</span>
                                </div>
                                
                                @if($stage['notes'])
                                <div class="um-info-row">
                                    <span class="um-info-label">ملاحظات:</span>
                                    <span class="um-info-value">{{ $stage['notes'] }}</span>
                                </div>
                                @endif
                                
                                @if(isset($stage['additional_info']) && !empty($stage['additional_info']))
                                    @if(isset($stage['additional_info']['items_details']) && count($stage['additional_info']['items_details']) > 1)
                                    <div style="margin-top: 15px; padding: 15px; background: #f8fafc; border-radius: 8px; border-right: 4px solid #3b82f6;">
                                        <h6 style="margin: 0 0 12px 0; color: #0f172a; font-size: 14px; font-weight: 600;">
                                            <i class="feather icon-list"></i> تفاصيل العناصر ({{ count($stage['additional_info']['items_details']) }} عنصر):
                                        </h6>
                                        <div style="display: grid; gap: 10px;">
                                            @foreach($stage['additional_info']['items_details'] as $index => $item)
                                            <div style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                                    <span style="font-weight: 600; color: #334155; font-size: 13px;">
                                                        <i class="feather icon-barcode"></i> {{ $item['barcode'] ?? $item['output_barcode'] }}
                                                    </span>
                                                    <span style="background: #dbeafe; color: #1d4ed8; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                                                        #{{ $index + 1 }}
                                                    </span>
                                                </div>
                                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 8px; font-size: 12px;">
                                                    @if($item['input_weight'] > 0)
                                                    <div>
                                                        <span style="color: #64748b;">دخول:</span>
                                                        <span style="color: #16a34a; font-weight: 600;">{{ number_format($item['input_weight'], 2) }} كجم</span>
                                                    </div>
                                                    @endif
                                                    @if($item['output_weight'] > 0)
                                                    <div>
                                                        <span style="color: #64748b;">خروج:</span>
                                                        <span style="color: #2563eb; font-weight: 600;">{{ number_format($item['output_weight'], 2) }} كجم</span>
                                                    </div>
                                                    @endif
                                                    @if($item['waste_amount'] > 0)
                                                    <div>
                                                        <span style="color: #64748b;">هدر:</span>
                                                        <span style="color: #dc2626; font-weight: 600;">{{ number_format($item['waste_amount'], 2) }} كجم ({{ number_format($item['waste_percentage'], 1) }}%)</span>
                                                    </div>
                                                    @endif
                                                </div>
                                                @if($item['notes'])
                                                <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #64748b;">
                                                    <i class="feather icon-message-square"></i> {{ $item['notes'] }}
                                                </div>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                @endif
                                
                                <div class="um-info-row" style="background: #f1f5f9; padding: 10px; border-radius: 6px; margin-top: 10px;">
                                    <span class="um-info-label">
                                        <i class="feather icon-clock"></i> المدة الزمنية:
                                    </span>
                                    <span class="um-info-value">
                                        {{ $stage['duration'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Charts -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 30px; margin-bottom: 30px;">
            <section class="um-main-card">
                <div class="um-card-header">
                    <h4 class="um-card-title">
                        <i class="feather icon-trending-up"></i>
                        تطور الأوزان عبر المراحل
                    </h4>
                </div>
                <div style="padding: 20px;">
                    <div style="position: relative; height: 300px;">
                        <canvas id="weightChart"></canvas>
                    </div>
                </div>
            </section>

            <section class="um-main-card">
                <div class="um-card-header">
                    <h4 class="um-card-title">
                        <i class="feather icon-bar-chart"></i>
                        نسبة الهدر في كل مرحلة
                    </h4>
                </div>
                <div style="padding: 20px;">
                    <div style="position: relative; height: 300px;">
                        <canvas id="wasteChart"></canvas>
                    </div>
                </div>
            </section>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px; flex-wrap: wrap;">
            <a href="{{ route('manufacturing.production-tracking.scan') }}" class="um-btn um-btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="feather icon-barcode"></i>
                مسح باركود جديد
            </a>
            <button onclick="window.print()" class="um-btn um-btn-success" style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="feather icon-printer"></i>
                طباعة التقرير
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare data
        const stages = @json(array_column($trackingData['journey'], 'stage_name'));
        const weights = @json(array_column($trackingData['journey'], 'output_weight'));
        const waste = @json(array_column($trackingData['journey'], 'waste_amount'));

        // Weight Chart
        const weightCtx = document.getElementById('weightChart').getContext('2d');
        new Chart(weightCtx, {
            type: 'line',
            data: {
                labels: stages,
                datasets: [{
                    label: 'الوزن (كجم)',
                    data: weights,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end'
                    },
                    tooltip: {
                        rtl: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' كجم';
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
                            }
                        }
                    }
                }
            }
        });

        // Waste Chart
        const wasteCtx = document.getElementById('wasteChart').getContext('2d');
        new Chart(wasteCtx, {
            type: 'bar',
            data: {
                labels: stages,
                datasets: [{
                    label: 'الهدر (كجم)',
                    data: waste,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)'
                    ],
                    borderColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6'
                    ],
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end'
                    },
                    tooltip: {
                        rtl: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' كجم';
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
                            }
                        }
                    }
                }
            }
        });
    });
    </script>
@endsection