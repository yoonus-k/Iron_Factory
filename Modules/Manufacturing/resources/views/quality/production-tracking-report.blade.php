@extends('master')

@section('title', __('app.quality.tracking_report.title'))

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 class="um-page-title">
                    <i class="feather icon-bar-chart-2"></i>
                    {{ __('app.quality.tracking_report.title') }}
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
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('manufacturing.production-tracking.scan') }}" class="btn btn-secondary" style="display: flex; align-items: center; gap: 8px;">
                    <i class="feather icon-arrow-right"></i>
                    <span>{{ __('app.quality.tracking_scan.search_button') }}</span>
                </a>
                <button onclick="window.print()" class="btn btn-primary" style="display: flex; align-items: center; gap: 8px;">
                    <i class="feather icon-printer"></i>
                    <span>طباعة التقرير</span>
                </button>
            </div>
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

        <!-- Reverse Tracking (من أين جاء هذا المنتج) -->
        @if(isset($reverseTracking) && count($reverseTracking) > 0)
        <section class="um-main-card" style="margin-bottom: 30px; border: 2px solid #8b5cf6; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.15);">
            <div class="um-card-header" style="background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%); color: white;">
                <h4 class="um-card-title" style="color: white; display: flex; align-items: center; gap: 10px;">
                    <i class="feather icon-arrow-left-circle" style="font-size: 24px;"></i>
                    <span>التتبع العكسي - أصل المنتج</span>
                    <span class="um-badge" style="background: rgba(255,255,255,0.3); color: white;">{{ count($reverseTracking) }} مرحلة سابقة</span>
                </h4>
                <p style="margin: 5px 0 0 0; font-size: 13px; opacity: 0.9;">من أين جاء هذا الباركود؟</p>
            </div>
            <div style="padding: 25px; background: linear-gradient(180deg, #faf5ff 0%, #f5f3ff 100%);">
                <div style="position: relative; padding: 10px 0;">
                    <div style="position: absolute; top: 0; bottom: 0; right: 30px; width: 3px; background: linear-gradient(180deg, #8b5cf6, #6d28d9); opacity: 0.4;"></div>
                    
                    @foreach($reverseTracking as $index => $item)
                    <div style="position: relative; margin-bottom: 25px;">
                        <div style="position: absolute; right: 18px; width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, #a78bfa, #8b5cf6); display: flex; align-items: center; justify-content: center; color: white; font-size: 13px; font-weight: bold; z-index: 10; box-shadow: 0 3px 10px rgba(139, 92, 246, 0.4);">
                            {{ $index + 1 }}
                        </div>
                        
                        <div style="margin-right: 65px; background: white; border-radius: 10px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 2px solid #e9d5ff;">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                <div>
                                    <div style="font-size: 11px; color: #6b21a8; font-weight: 600; margin-bottom: 5px;">الباركود</div>
                                    <div style="font-family: 'Courier New', monospace; font-size: 16px; font-weight: 700; color: #581c87;">{{ $item['barcode'] }}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #6b21a8; font-weight: 600; margin-bottom: 5px;">المرحلة</div>
                                    <div style="font-size: 14px; color: #1e293b; font-weight: 600;">{{ $item['stage_name'] }}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #6b21a8; font-weight: 600; margin-bottom: 5px;">الإجراء</div>
                                    <div style="font-size: 14px; color: #1e293b;">{{ $item['action_name'] }}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #6b21a8; font-weight: 600; margin-bottom: 5px;">الوزن</div>
                                    <div style="font-size: 16px; font-weight: 700; color: #7c3aed;">{{ number_format($item['weight'], 2) }} كجم</div>
                                </div>
                            </div>
                            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e9d5ff; font-size: 12px; color: #6b7280;">
                                <i class="feather icon-clock"></i> {{ $item['formatted_time'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Forward Tracking (ماذا تم إنتاجه من هذا الباركود) -->
        @if(isset($forwardTracking) && count($forwardTracking) > 0)
        <section class="um-main-card" style="margin-bottom: 30px; border: 2px solid #10b981; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.15);">
            <div class="um-card-header" style="background: linear-gradient(135deg, #34d399 0%, #10b981 100%); color: white;">
                <h4 class="um-card-title" style="color: white; display: flex; align-items: center; gap: 10px;">
                    <i class="feather icon-arrow-right-circle" style="font-size: 24px;"></i>
                    <span>التتبع الأمامي - المنتجات المشتقة</span>
                    <span class="um-badge" style="background: rgba(255,255,255,0.3); color: white;">{{ count($forwardTracking) }} منتج</span>
                </h4>
                <p style="margin: 5px 0 0 0; font-size: 13px; opacity: 0.9;">ماذا تم إنتاجه من هذا الباركود؟</p>
            </div>
            <div style="padding: 25px; background: linear-gradient(180deg, #f0fdf4 0%, #dcfce7 100%);">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                    @foreach($forwardTracking as $product)
                    <div style="background: white; border-radius: 10px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 2px solid #86efac;">
                        <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); padding: 10px; border-radius: 8px; margin-bottom: 12px; text-align: center;">
                            <div style="font-size: 10px; color: #065f46; font-weight: 600; margin-bottom: 4px;">الباركود</div>
                            <div style="font-family: 'Courier New', monospace; font-size: 16px; font-weight: 700; color: #047857;">{{ $product['barcode'] }}</div>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <div style="font-size: 11px; color: #059669; font-weight: 600; margin-bottom: 3px;">المرحلة</div>
                            <div style="font-size: 14px; color: #1e293b; font-weight: 600;">{{ $product['stage_name'] }}</div>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <div style="font-size: 11px; color: #059669; font-weight: 600; margin-bottom: 3px;">الإجراء</div>
                            <div style="font-size: 13px; color: #64748b;">{{ $product['action_name'] }}</div>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <div style="font-size: 11px; color: #059669; font-weight: 600; margin-bottom: 3px;">الوزن</div>
                            <div style="font-size: 18px; font-weight: 700; color: #10b981;">{{ number_format($product['weight'], 2) }} كجم</div>
                        </div>
                        <div style="padding-top: 8px; border-top: 1px solid #86efac; font-size: 11px; color: #6b7280;">
                            <i class="feather icon-clock"></i> {{ $product['formatted_time'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Charts Section (رسوم بيانية) -->
        <section class="um-main-card" style="margin-bottom: 30px; border: 2px solid #3b82f6; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.15);">
            <div class="um-card-header" style="background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%); color: white;">
                <h4 class="um-card-title" style="color: white; display: flex; align-items: center; gap: 10px;">
                    <i class="feather icon-bar-chart-2" style="font-size: 24px;"></i>
                    <span>التحليل البياني</span>
                </h4>
            </div>
            <div style="padding: 25px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px;">
                    <!-- Weight Flow Chart -->
                    <div>
                        <h6 style="text-align: center; color: #1e293b; font-weight: 600; margin-bottom: 15px;">
                            <i class="feather icon-trending-up"></i> تدفق الأوزان عبر المراحل
                        </h6>
                        <div style="height: 300px;">
                            <canvas id="weightFlowChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Waste Chart -->
                    <div>
                        <h6 style="text-align: center; color: #1e293b; font-weight: 600; margin-bottom: 15px;">
                            <i class="feather icon-alert-triangle"></i> الهدر في كل مرحلة
                        </h6>
                        <div style="height: 300px;">
                            <canvas id="wasteChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Efficiency Doughnut Chart -->
                <div style="margin-top: 30px; max-width: 400px; margin-left: auto; margin-right: auto;">
                    <h6 style="text-align: center; color: #1e293b; font-weight: 600; margin-bottom: 15px;">
                        <i class="feather icon-pie-chart"></i> كفاءة الإنتاج الإجمالية
                    </h6>
                    <div style="height: 300px;">
                        <canvas id="efficiencyChart"></canvas>
                    </div>
                </div>
            </div>
        </section>

        <!-- Barcode Split History (NEW) -->
        @php
            $splitEvents = collect($trackingData['journey'] ?? [])->filter(function($stage) {
                return ($stage['action'] ?? '') === 'split';
            });
        @endphp
        
        @if($splitEvents->count() > 0)
        <section class="um-main-card" style="margin-bottom: 30px; border: 2px solid #f59e0b; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.15);">
            <div class="um-card-header" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: white;">
                <h4 class="um-card-title" style="color: white; display: flex; align-items: center; gap: 10px;">
                    <i class="feather icon-git-branch" style="font-size: 24px;"></i>
                    <span>تاريخ تقسيم الباركود</span>
                    <span class="um-badge" style="background: rgba(255,255,255,0.3); color: white;">{{ $splitEvents->count() }} عملية</span>
                </h4>
            </div>
            <div style="padding: 25px;">
                @foreach($splitEvents as $split)
                @php
                    $metadata = is_string($split['metadata'] ?? null) ? json_decode($split['metadata'], true) : ($split['metadata'] ?? []);
                    $outputBarcodes = explode(',', $split['output_barcode'] ?? '');
                @endphp
                <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 16px; padding: 25px; margin-bottom: 20px; border: 2px solid #fbbf24; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.1);">
                    <!-- Header -->
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px dashed #f59e0b;">
                        <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);">
                            <i class="feather icon-scissors" style="font-size: 24px;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h5 style="margin: 0 0 5px 0; color: #78350f; font-size: 18px; font-weight: 700;">
                                🔄 عملية تقسيم الدفعة
                            </h5>
                            <p style="margin: 0; color: #92400e; font-size: 13px;">
                                <i class="feather icon-calendar"></i> {{ $split['formatted_time'] ?? 'غير محدد' }}
                                <span style="margin-right: 15px;">
                                    <i class="feather icon-clock"></i> {{ $split['time_ago'] ?? '' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Split Diagram -->
                    <div style="display: grid; grid-template-columns: 1fr auto 2fr; gap: 20px; align-items: center; margin: 20px 0;">
                        <!-- Original Barcode -->
                        <div style="background: white; border: 3px solid #d97706; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 3px 10px rgba(217, 119, 6, 0.2);">
                            <div style="color: #92400e; font-size: 12px; font-weight: 600; margin-bottom: 8px;">الباركود الأصلي</div>
                            <div style="font-size: 18px; font-weight: 700; color: #78350f; font-family: monospace; margin-bottom: 8px;">
                                {{ $split['input_barcode'] ?? 'N/A' }}
                            </div>
                            <div style="background: #fef3c7; padding: 8px; border-radius: 8px; margin-top: 10px;">
                                <div style="font-size: 11px; color: #92400e; margin-bottom: 3px;">الكمية الأصلية</div>
                                <div style="font-size: 16px; font-weight: 700; color: #d97706;">
                                    {{ number_format($split['input_weight'] ?? 0, 2) }} كجم
                                </div>
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div style="text-align: center;">
                            <i class="feather icon-arrow-left" style="font-size: 36px; color: #f59e0b;"></i>
                        </div>

                        <!-- Split Results -->
                        <div style="display: grid; gap: 15px;">
                            @if(isset($metadata['production_barcode']))
                            <!-- Production Barcode -->
                            <div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border: 3px solid #3b82f6; border-radius: 12px; padding: 15px; box-shadow: 0 3px 10px rgba(59, 130, 246, 0.2);">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="background: #3b82f6; color: white; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="feather icon-box" style="font-size: 20px;"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-size: 11px; color: #1e40af; font-weight: 600; margin-bottom: 4px;">
                                            🏭 للإنتاج
                                        </div>
                                        <div style="font-size: 16px; font-weight: 700; color: #1e3a8a; font-family: monospace; margin-bottom: 4px;">
                                            {{ $metadata['production_barcode'] }}
                                        </div>
                                        <div style="font-size: 13px; font-weight: 600; color: #3b82f6;">
                                            {{ number_format($metadata['production_quantity'] ?? 0, 2) }} كجم
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(isset($metadata['remaining_barcode']))
                            <!-- Warehouse Barcode -->
                            <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border: 3px solid #10b981; border-radius: 12px; padding: 15px; box-shadow: 0 3px 10px rgba(16, 185, 129, 0.2);">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="background: #10b981; color: white; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="feather icon-package" style="font-size: 20px;"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-size: 11px; color: #065f46; font-weight: 600; margin-bottom: 4px;">
                                            📦 في المستودع
                                        </div>
                                        <div style="font-size: 16px; font-weight: 700; color: #064e3b; font-family: monospace; margin-bottom: 4px;">
                                            {{ $metadata['remaining_barcode'] }}
                                        </div>
                                        <div style="font-size: 13px; font-weight: 600; color: #10b981;">
                                            {{ number_format($metadata['remaining_quantity'] ?? 0, 2) }} كجم
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Additional Info -->
                    @if($split['notes'] ?? false)
                    <div style="background: white; border-right: 4px solid #f59e0b; border-radius: 8px; padding: 12px; margin-top: 15px;">
                        <div style="color: #92400e; font-size: 12px; font-weight: 600; margin-bottom: 5px;">
                            <i class="feather icon-message-square"></i> ملاحظات:
                        </div>
                        <div style="color: #78350f; font-size: 13px;">
                            {{ $split['notes'] }}
                        </div>
                    </div>
                    @endif

                    <!-- Worker Info -->
                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 15px; padding-top: 15px; border-top: 2px dashed #f59e0b;">
                        <div style="background: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #f59e0b;">
                            <i class="feather icon-user" style="color: #d97706; font-size: 16px;"></i>
                        </div>
                        <div style="color: #78350f; font-size: 13px;">
                            <strong>العامل:</strong> {{ $split['worker_name'] ?? 'غير محدد' }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

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
                                <!-- Barcode Card -->
                                <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); padding: 15px; border-radius: 10px; margin-bottom: 15px; border: 2px solid #3b82f6;">
                                    <div style="text-align: center;">
                                        <div style="color: #1e40af; font-size: 11px; font-weight: 600; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px;">
                                            الباركود
                                        </div>
                                        <div style="font-family: 'Courier New', monospace; font-size: 20px; font-weight: 700; color: #1e3a8a; letter-spacing: 2px;">
                                            {{ $stage['barcode'] }}
                                        </div>
                                    </div>
                                </div>
                                
                                @if(isset($stage['items_count']) && $stage['items_count'] > 1)
                                <div style="background: #fef3c7; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px; text-align: center; border: 2px solid #fbbf24;">
                                    <span style="color: #78350f; font-weight: 600; font-size: 13px;">
                                        <i class="feather icon-layers"></i> {{ $stage['items_count'] }} عملية في هذه المرحلة
                                    </span>
                                </div>
                                @endif
                                
                                <!-- Worker and Notes -->
                                <div style="background: #f8fafc; padding: 12px; border-radius: 8px; margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">
                                    <div style="background: #e0e7ff; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="feather icon-user" style="color: #4f46e5; font-size: 16px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-size: 10px; color: #64748b; margin-bottom: 2px;">العامل المسؤول</div>
                                        <div style="font-size: 13px; color: #1e293b; font-weight: 600;">{{ $stage['worker_name'] }}</div>
                                    </div>
                                </div>
                                
                                @if($stage['notes'])
                                <div style="background: #fffbeb; border-right: 3px solid #f59e0b; padding: 10px 12px; border-radius: 6px; margin-bottom: 10px;">
                                    <div style="font-size: 10px; color: #92400e; margin-bottom: 4px; font-weight: 600;">
                                        <i class="feather icon-message-square"></i> ملاحظات
                                    </div>
                                    <div style="font-size: 12px; color: #78350f;">{{ $stage['notes'] }}</div>
                                </div>
                                @endif
                                
                                @if($stage['waste_amount'] > 0)
                                <div style="background: #fef2f2; border: 2px solid #fca5a5; padding: 12px; border-radius: 8px; margin-bottom: 10px;">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <span style="color: #991b1b; font-size: 12px; font-weight: 600;">
                                            <i class="feather icon-trash-2"></i> الهدر
                                        </span>
                                        <span style="color: #dc2626; font-size: 16px; font-weight: 700;">
                                            {{ number_format($stage['waste_amount'], 2) }} كجم ({{ number_format($stage['waste_percentage'], 2) }}%)
                                        </span>
                                    </div>
                                </div>
                                @endif
                                
                                @if(isset($stage['additional_info']) && !empty($stage['additional_info']))
                                    @if(isset($stage['additional_info']['items_details']) && count($stage['additional_info']['items_details']) >= 1)
                                    <div style="margin-top: 15px; padding: 15px; background: #f8fafc; border-radius: 8px; border-right: 4px solid #3b82f6;">
                                        <h6 style="margin: 0 0 12px 0; color: #0f172a; font-size: 14px; font-weight: 600;">
                                            <i class="feather icon-list"></i> تفاصيل العمليات ({{ count($stage['additional_info']['items_details']) }} عملية):
                                        </h6>
                                        <div style="display: grid; gap: 10px;">
                                            @foreach($stage['additional_info']['items_details'] as $index => $item)
                                            <div style="background: white; padding: 15px; border-radius: 8px; border: 2px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                                <!-- Header -->
                                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; padding-bottom: 10px; border-bottom: 2px dashed #e2e8f0;">
                                                    <div style="flex: 1;">
                                                        <div style="font-size: 10px; color: #64748b; margin-bottom: 4px; font-weight: 600;">
                                                            العملية #{{ $index + 1 }}
                                                        </div>
                                                        <div style="font-weight: 700; color: #1e40af; font-size: 14px; font-family: monospace; background: #eff6ff; padding: 6px 10px; border-radius: 6px; display: inline-block;">
                                                            <i class="feather icon-hash" style="font-size: 12px;"></i> {{ $item['barcode'] }}
                                                        </div>
                                                    </div>
                                                    @if($item['formatted_time'] ?? false)
                                                    <div style="text-align: left; font-size: 11px; color: #64748b;">
                                                        <i class="feather icon-clock"></i> {{ $item['formatted_time'] }}
                                                    </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Source and Output -->
                                                @if($item['input_barcode'] ?? false)
                                                <div style="background: #fef3c7; padding: 8px 12px; border-radius: 6px; margin-bottom: 8px; border-right: 3px solid #f59e0b;">
                                                    <div style="font-size: 10px; color: #78350f; margin-bottom: 3px; font-weight: 600;">
                                                        <i class="feather icon-arrow-down"></i> مصدر المادة
                                                    </div>
                                                    <div style="color: #92400e; font-weight: 600; font-size: 12px; font-family: monospace;">
                                                        {{ $item['input_barcode'] }}
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                @if(($item['output_barcode'] ?? false) && $item['output_barcode'] != $item['barcode'])
                                                <div style="background: #d1fae5; padding: 8px 12px; border-radius: 6px; margin-bottom: 8px; border-right: 3px solid #10b981;">
                                                    <div style="font-size: 10px; color: #065f46; margin-bottom: 3px; font-weight: 600;">
                                                        <i class="feather icon-arrow-up"></i> الباركود الناتج
                                                    </div>
                                                    <div style="color: #047857; font-weight: 600; font-size: 12px; font-family: monospace;">
                                                        {{ $item['output_barcode'] }}
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                <!-- Action Type -->
                                                @if($item['action'] ?? false)
                                                <div style="background: #e0e7ff; padding: 6px 12px; border-radius: 6px; margin-bottom: 10px; display: inline-block;">
                                                    <span style="color: #3730a3; font-size: 11px; font-weight: 600;">
                                                        <i class="feather icon-activity"></i> 
                                                        @switch($item['action'])
                                                            @case('split') تقسيم @break
                                                            @case('transferred_to_production') نقل للإنتاج @break
                                                            @case('warehouse_remaining') متبقي في المستودع @break
                                                            @default {{ $item['action'] }}
                                                        @endswitch
                                                    </span>
                                                </div>
                                                @endif
                                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; font-size: 12px; margin-top: 8px;">
                                                    @if($item['input_weight'] > 0)
                                                    <div style="background: #f0fdf4; padding: 8px; border-radius: 6px; text-align: center; border: 1px solid #bbf7d0;">
                                                        <div style="color: #15803d; font-size: 10px; margin-bottom: 3px;">
                                                            <i class="feather icon-arrow-down"></i> دخول
                                                        </div>
                                                        <div style="color: #16a34a; font-weight: 700; font-size: 14px;">{{ number_format($item['input_weight'], 2) }}</div>
                                                        <div style="color: #4ade80; font-size: 9px;">كجم</div>
                                                    </div>
                                                    @endif
                                                    @if($item['output_weight'] > 0)
                                                    <div style="background: #eff6ff; padding: 8px; border-radius: 6px; text-align: center; border: 1px solid #bfdbfe;">
                                                        <div style="color: #1e40af; font-size: 10px; margin-bottom: 3px;">
                                                            <i class="feather icon-arrow-up"></i> خروج
                                                        </div>
                                                        <div style="color: #2563eb; font-weight: 700; font-size: 14px;">{{ number_format($item['output_weight'], 2) }}</div>
                                                        <div style="color: #60a5fa; font-size: 9px;">كجم</div>
                                                    </div>
                                                    @endif
                                                    @if($item['waste_amount'] > 0)
                                                    <div style="background: #fef2f2; padding: 8px; border-radius: 6px; text-align: center; border: 1px solid #fecaca;">
                                                        <div style="color: #991b1b; font-size: 10px; margin-bottom: 3px;">
                                                            <i class="feather icon-trash-2"></i> هدر
                                                        </div>
                                                        <div style="color: #dc2626; font-weight: 700; font-size: 14px;">{{ number_format($item['waste_amount'], 2) }}</div>
                                                        <div style="color: #f87171; font-size: 9px;">كجم ({{ number_format($item['waste_percentage'], 1) }}%)</div>
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
    
    <style>
        @media print {
            .um-header-section button,
            .um-header-section a {
                display: none !important;
            }
            
            @page {
                margin: 15mm;
                size: A4;
            }
            
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .um-main-card {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            canvas {
                max-height: 400px !important;
            }
        }
    </style>
    
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

        // Efficiency Doughnut Chart
        const efficiencyCtx = document.getElementById('efficiencyChart').getContext('2d');
        const efficiency = {{ $trackingData['summary']['efficiency'] ?? 100 }};
        const waste_percent = {{ $trackingData['summary']['waste_percentage'] ?? 0 }};
        
        new Chart(efficiencyCtx, {
            type: 'doughnut',
            data: {
                labels: ['كفاءة الإنتاج', 'الهدر'],
                datasets: [{
                    data: [efficiency, waste_percent],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        '#10b981',
                        '#ef4444'
                    ],
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        rtl: true,
                        labels: {
                            font: { size: 14, weight: 'bold' },
                            padding: 15
                        }
                    },
                    tooltip: {
                        rtl: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        padding: 15,
                        titleFont: { size: 15, weight: 'bold' },
                        bodyFont: { size: 14 },
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed.toFixed(2) + '%';
                            }
                        }
                    }
                }
            }
        });
    });
    </script>
@endsection