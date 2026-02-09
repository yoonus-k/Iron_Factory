@extends('master')

@section('title', __('app.quality.iron_journey.title'))

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/iron-journey.css') }}">

<div class="iron-journey-container">
    <!-- Header -->
    <div class="journey-header">
        <h1>
            <i class="fas fa-route"></i>
            {{ __('app.quality.iron_journey.title') }}
        </h1>
        <p class="subtitle">{{ __('app.quality.iron_journey.subtitle') }}</p>
    </div>

    <!-- Search Section -->
    <div class="journey-search-section">
        <h3 style="margin-bottom: 1rem; color: #1F2937;">
            <i class="fas fa-barcode"></i>
            {{ __('app.quality.iron_journey.search_title') }}
        </h3>
        <form id="journeySearchForm" method="GET" action="{{ route('manufacturing.iron-journey.show') }}">
            <div class="search-input-group">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input
                        type="text"
                        name="barcode"
                        id="barcodeInput"
                        placeholder="امسح أو أدخل الباركود (WH-001-2025, ST1-001-2025, BOX4-001-2025...)"
                        value="{{ request('barcode') }}"
                        required
                        autofocus
                    >
                </div>
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                    تتبع الآن
                </button>
            </div>
            <p style="color: #6B7280; font-size: 0.875rem; margin-top: 0.5rem;">
                💡 يمكنك مسح الباركود من أي مرحلة - سيعرض النظام الرحلة الكاملة من البداية حتى النهاية
            </p>
        </form>
    </div>

    @if(isset($journeyData))
    <!-- Journey Info Bar -->
    <div class="journey-info-bar">
        <div class="info-item">
            <span class="info-label">الباركود المطلوب</span>
            <span class="info-value">{{ $journeyData['searchedBarcode'] }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">نوع المنتج</span>
            <span class="info-value">{{ $journeyData['productType'] }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">الحالة الحالية</span>
            <span class="info-value">{{ $journeyData['currentStatus'] }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">إجمالي المدة</span>
            <span class="info-value">{{ $journeyData['summary']['totalDuration'] }}</span>
        </div>
    </div>

    <!-- Timeline Container -->
    <div class="journey-timeline-container">
        <h2 style="margin-bottom: 2rem; color: #1F2937; text-align: center;">
            <i class="fas fa-project-diagram"></i>
            الرحلة الكاملة للمنتج
        </h2>

        <!-- Timeline -->
        <div class="journey-timeline">
            <div class="timeline-line">
                <div class="timeline-progress" style="width: {{ $journeyData['progressPercentage'] }}%"></div>
            </div>

            <div class="timeline-horizontal">
                @foreach($journeyData['journey'] as $index => $stage)
                <!-- Stage Card -->
                <div class="stage-card {{ $stage['status'] }}" onclick="openStageModal({{ $index }})" data-stage="{{ $index }}">
                    <!-- Stage Icon -->
                    <div class="stage-icon">
                        @if($stage['icon'] == 'warehouse')
                            <i class="fas fa-warehouse"></i>
                        @elseif($stage['icon'] == 'cut')
                            <i class="fas fa-cut"></i>
                        @elseif($stage['icon'] == 'cogs')
                            <i class="fas fa-cogs"></i>
                        @elseif($stage['icon'] == 'coil')
                            <i class="fas fa-circle-notch"></i>
                        @elseif($stage['icon'] == 'box')
                            <i class="fas fa-box"></i>
                        @endif
                    </div>

                    <!-- Stage Header -->
                    <div class="stage-header">
                        <div class="stage-name">{{ $stage['name'] }}</div>
                        <div class="stage-barcode">{{ $stage['barcode'] }}</div>
                    </div>

                    <!-- Stage Details -->
                    <div class="stage-details">
                        @if(isset($stage['input']['weight']) && $stage['input']['weight'] > 0)
                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-arrow-down"></i>
                                المدخل
                            </span>
                            <span class="detail-value">{{ $stage['input']['weight'] }} كجم</span>
                        </div>
                        @endif

                        @if(isset($stage['output']['weight']) && $stage['output']['weight'] > 0)
                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-arrow-up"></i>
                                المخرج
                            </span>
                            <span class="detail-value success">{{ $stage['output']['weight'] }} كجم</span>
                        </div>
                        @endif

                        @if(isset($stage['waste']['amount']) && $stage['waste']['amount'] > 0)
                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-exclamation-triangle"></i>
                                الهدر
                            </span>
                            <span class="detail-value {{ $stage['waste']['percentage'] > 3 ? 'danger' : 'warning' }}">
                                {{ $stage['waste']['amount'] }} كجم ({{ $stage['waste']['percentage'] }}%)
                            </span>
                        </div>
                        @endif

                        @if(isset($stage['worker']))
                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-user"></i>
                                العامل
                            </span>
                            <span class="detail-value">{{ $stage['worker']['name'] }}</span>
                        </div>
                        @endif

                        @if(isset($stage['duration']))
                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-clock"></i>
                                المدة
                            </span>
                            <span class="detail-value">{{ $stage['duration'] }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Status Badge -->
                    <div class="status-badge {{ $stage['status'] }}">
                        @if($stage['status'] == 'completed')
                            <i class="fas fa-check-circle"></i>
                            مكتمل
                        @elseif($stage['status'] == 'in-progress')
                            <i class="fas fa-spinner"></i>
                            جاري العمل
                        @elseif($stage['status'] == 'issue')
                            <i class="fas fa-exclamation-circle"></i>
                            يحتاج انتباه
                        @else
                            <i class="fas fa-clock"></i>
                            قيد الانتظار
                        @endif
                    </div>
                </div>

                @if(!$loop->last)
                <!-- Arrow -->
                <div class="stage-arrow">
                    <i class="fas fa-arrow-left"></i>
                </div>
                @endif
                @endforeach
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="stats-container">
            <div class="stat-card" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                <div class="stat-value">{{ $journeyData['summary']['totalOutputWeight'] }} كجم</div>
                <div class="stat-label">الوزن النهائي</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">
                <div class="stat-value">{{ $journeyData['summary']['totalWaste'] }} كجم</div>
                <div class="stat-label">إجمالي الهدر</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
                <div class="stat-value">{{ $journeyData['summary']['totalWastePercentage'] }}%</div>
                <div class="stat-label">نسبة الهدر</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);">
                <div class="stat-value">{{ $journeyData['summary']['qualityScore'] }}/100</div>
                <div class="stat-label">درجة الجودة</div>
            </div>
        </div>

        <!-- Waste Analysis -->
        <div class="waste-analysis">
            <h3>
                <i class="fas fa-chart-pie"></i>
                تحليل الهدر بالتفصيل
            </h3>
            <div class="waste-breakdown">
                @foreach($journeyData['journey'] as $stage)
                    @if(isset($stage['waste']['amount']) && $stage['waste']['amount'] > 0)
                    <div class="waste-item">
                        <span style="font-weight: 600; min-width: 150px;">{{ $stage['name'] }}</span>
                        <div class="waste-bar">
                            <div class="waste-bar-fill" style="width: {{ ($stage['waste']['percentage'] / 5) * 100 }}%">
                                {{ $stage['waste']['percentage'] }}%
                            </div>
                        </div>
                        <span style="font-weight: 700; color: #DC2626;">{{ $stage['waste']['amount'] }} كجم</span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Recommendations -->
        @if(isset($journeyData['summary']['recommendations']))
        <div class="recommendations">
            <h3>
                <i class="fas fa-lightbulb"></i>
                توصيات لتحسين الأداء
            </h3>
            <ul>
                @foreach($journeyData['summary']['recommendations'] as $recommendation)
                <li>{{ $recommendation }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Action Buttons -->
        <div style="text-align: center; margin-top: 2rem; display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <button onclick="window.print()" class="search-btn" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                <i class="fas fa-print"></i>
                طباعة التقرير
            </button>
            <button onclick="exportToPDF()" class="search-btn" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">
                <i class="fas fa-file-pdf"></i>
                تصدير PDF
            </button>
            <a href="{{ route('manufacturing.iron-journey') }}" class="search-btn" style="background: linear-gradient(135deg, #6B7280 0%, #4B5563 100%); text-decoration: none;">
                <i class="fas fa-search"></i>
                بحث جديد
            </a>
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="journey-timeline-container" style="text-align: center; padding: 4rem 2rem;">
        <div style="font-size: 5rem; color: #D1D5DB; margin-bottom: 1rem;">
            <i class="fas fa-search"></i>
        </div>
        <h3 style="color: #6B7280; margin-bottom: 1rem;">{{ __('app.quality.iron_journey.empty_state_title') }}</h3>
        <p style="color: #9CA3AF;">{{ __('app.quality.iron_journey.empty_state_subtitle') }}</p>
    </div>
    @endif
</div>

<!-- Modal for Stage Details -->
<div class="journey-modal" id="stageModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-info-circle"></i>
                <span id="modalStageName">تفاصيل المرحلة</span>
            </div>
            <button class="modal-close" onclick="closeStageModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <!-- Tabs -->
            <div class="modal-tabs">
                <button class="tab-btn active" onclick="switchTab('overview')">
                    <i class="fas fa-eye"></i>
                    نظرة عامة
                </button>
                <button class="tab-btn" onclick="switchTab('materials')">
                    <i class="fas fa-boxes"></i>
                    المواد
                </button>
                <button class="tab-btn" onclick="switchTab('worker')">
                    <i class="fas fa-user"></i>
                    العامل
                </button>
                <button class="tab-btn" onclick="switchTab('logs')">
                    <i class="fas fa-history"></i>
                    السجل
                </button>
            </div>

            <!-- Tab Content -->
            <div id="tabOverview" class="tab-content active">
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-card-title">الباركود</div>
                        <div class="info-card-value" id="modalBarcode">-</div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-title">الحالة</div>
                        <div class="info-card-value" id="modalStatus">-</div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-title">تاريخ البدء</div>
                        <div class="info-card-value" id="modalStartTime">-</div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-title">المدة</div>
                        <div class="info-card-value" id="modalDuration">-</div>
                    </div>
                </div>

                <div style="background: #F9FAFB; padding: 1.5rem; border-radius: 12px; margin-top: 1.5rem;">
                    <h4 style="margin-bottom: 1rem; color: #1F2937;">تدفق المواد</h4>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; text-align: center;">
                        <div>
                            <div style="color: #6B7280; font-size: 0.875rem; margin-bottom: 0.5rem;">المدخل</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #3B82F6;" id="modalInputWeight">-</div>
                        </div>
                        <div>
                            <div style="color: #6B7280; font-size: 0.875rem; margin-bottom: 0.5rem;">المخرج</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #10B981;" id="modalOutputWeight">-</div>
                        </div>
                        <div>
                            <div style="color: #6B7280; font-size: 0.875rem; margin-bottom: 0.5rem;">الهدر</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #EF4444;" id="modalWaste">-</div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 1.5rem;">
                    <h4 style="margin-bottom: 1rem; color: #1F2937;">ملاحظات</h4>
                    <p id="modalNotes" style="color: #6B7280; line-height: 1.6;">لا توجد ملاحظات</p>
                </div>
            </div>

            <div id="tabMaterials" class="tab-content">
                <h4 style="margin-bottom: 1rem; color: #1F2937;">المواد المستخدمة</h4>
                <div id="modalMaterialsList">
                    <p style="color: #6B7280; text-align: center; padding: 2rem;">لا توجد مواد إضافية</p>
                </div>
            </div>

            <div id="tabWorker" class="tab-content">
                <div id="modalWorkerInfo">
                    <div class="worker-card" style="max-width: 500px; margin: 0 auto;">
                        <div class="worker-avatar" id="modalWorkerAvatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="worker-info">
                            <div class="worker-name" id="modalWorkerName">-</div>
                            <div class="worker-role" id="modalWorkerRole">-</div>
                            <div class="worker-performance" id="modalWorkerPerformance">
                                <!-- Stars will be added by JS -->
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 1.5rem; background: #F9FAFB; padding: 1.5rem; border-radius: 12px;">
                        <h4 style="margin-bottom: 1rem; color: #1F2937;">أداء العامل</h4>
                        <p id="modalWorkerStats" style="color: #6B7280;">جاري التحميل...</p>
                    </div>
                </div>
            </div>

            <div id="tabLogs" class="tab-content">
                <h4 style="margin-bottom: 1rem; color: #1F2937;">سجل الأحداث</h4>
                <div id="modalLogsList" style="max-height: 400px; overflow-y: auto;">
                    <p style="color: #6B7280; text-align: center; padding: 2rem;">لا توجد سجلات</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/iron-journey.js') }}"></script>

<script>
// Store journey data for modal
const journeyData = @json($journeyData ?? null);
</script>

@endsection
