@extends('master')

@section('title', 'تقرير تتبع الإنتاج')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <h1 class="page-title">
                <i class="fas fa-file-alt"></i> تقرير تتبع الإنتاج
            </h1>
            <p class="text-muted">تقرير مفصل لمسار المنتج من المستودع حتى التغليف النهائي</p>
        </div>
    </div>


            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-barcode"></i> معلومات المنتج
                    </h5>
                    <span class="badge badge-light">{{ $productionData['barcode'] }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>الباركود المطلوب:</h6>
                            <p class="text-primary font-weight-bold">{{ $productionData['barcode'] }}</p>
                        </div>
                        <div class="col-md-6 text-md-left">
                            <h6>تاريخ التقرير:</h6>
                            <p>{{ date('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Production Journey Timeline -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-route"></i> رحلة الإنتاج
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Warehouse Stage -->
                        <div class="timeline-item">
                            <div class="timeline-badge bg-primary">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h6 class="timeline-title">المرحلة 1: المستودع</h6>
                                    <p class="timeline-date">{{ $productionData['warehouse']['received_date'] }}</p>
                                </div>
                                <div class="timeline-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>باركود المستودع:</strong> {{ $productionData['warehouse']['barcode'] }}</p>
                                            <p><strong>نوع المادة:</strong> {{ $productionData['warehouse']['material_type'] }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>الوزن الأصلي:</strong> {{ $productionData['warehouse']['original_weight'] }} كجم</p>
                                            <p><strong>المورد:</strong> {{ $productionData['warehouse']['supplier'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stage 1: Division and Stands -->
                        <div class="timeline-item">
                            <div class="timeline-badge bg-info">
                                <i class="fas fa-cut"></i>
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h6 class="timeline-title">المرحلة 2: التقسيم والاستاندات</h6>
                                    <p class="timeline-date">{{ $productionData['stage1']['completed_date'] }}</p>
                                </div>
                                <div class="timeline-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>باركود الاستاند:</strong> {{ $productionData['stage1']['barcode'] }}</p>
                                            <p><strong>رقم الاستاند:</strong> {{ $productionData['stage1']['stand_number'] }}</p>
                                            <p><strong>حجم السلك:</strong> {{ $productionData['stage1']['wire_size'] }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>الوزن:</strong> {{ $productionData['stage1']['weight'] }} كجم</p>
                                            <p><strong>الهدر:</strong> {{ $productionData['stage1']['waste'] }} كجم</p>
                                            <p><strong>العامل:</strong> {{ $productionData['stage1']['worker'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stage 2: Processing -->
                        <div class="timeline-item">
                            <div class="timeline-badge bg-success">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h6 class="timeline-title">المرحلة 3: المعالجة</h6>
                                    <p class="timeline-date">{{ $productionData['stage2']['completed_date'] }}</p>
                                </div>
                                <div class="timeline-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>باركود المعالجة:</strong> {{ $productionData['stage2']['barcode'] }}</p>
                                            <p><strong>تفاصيل العملية:</strong> {{ $productionData['stage2']['process_details'] }}</p>
                                            <p><strong>الوزن المدخل:</strong> {{ $productionData['stage2']['input_weight'] }} كجم</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>الوزن المخرج:</strong> {{ $productionData['stage2']['output_weight'] }} كجم</p>
                                            <p><strong>الهدر:</strong> {{ $productionData['stage2']['waste'] }} كجم</p>
                                            <p><strong>العامل:</strong> {{ $productionData['stage2']['worker'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stage 3: Coil Manufacturing -->
                        <div class="timeline-item">
                            <div class="timeline-badge bg-warning">
                                <i class="fas fa-codiepie"></i>
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h6 class="timeline-title">المرحلة 4: تصنيع الكويلات</h6>
                                    <p class="timeline-date">{{ $productionData['stage3']['completed_date'] }}</p>
                                </div>
                                <div class="timeline-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>باركود الكويل:</strong> {{ $productionData['stage3']['barcode'] }}</p>
                                            <p><strong>رقم الكويل:</strong> {{ $productionData['stage3']['coil_number'] }}</p>
                                            <p><strong>حجم السلك:</strong> {{ $productionData['stage3']['wire_size'] }}</p>
                                            <p><strong>الوزن الأساسي:</strong> {{ $productionData['stage3']['base_weight'] }} كجم</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>وزن الصبغة:</strong> {{ $productionData['stage3']['dye_weight'] }} كجم</p>
                                            <p><strong>وزن البلاستيك:</strong> {{ $productionData['stage3']['plastic_weight'] }} كجم</p>
                                            <p><strong>الوزن الإجمالي:</strong> {{ $productionData['stage3']['total_weight'] }} كجم</p>
                                            <p><strong>اللون:</strong> {{ $productionData['stage3']['color'] }}</p>
                                            <p><strong>العامل:</strong> {{ $productionData['stage3']['worker'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stage 4: Packaging -->
                        <div class="timeline-item">
                            <div class="timeline-badge bg-danger">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h6 class="timeline-title">المرحلة 5: التعبئة والتغليف</h6>
                                    <p class="timeline-date">{{ $productionData['stage4']['packed_date'] }}</p>
                                </div>
                                <div class="timeline-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>باركود الكرتون:</strong> {{ $productionData['stage4']['barcode'] }}</p>
                                            <p><strong>نوع التغليف:</strong> {{ $productionData['stage4']['packaging_type'] }}</p>
                                            <p><strong>عدد الكويلات:</strong> {{ $productionData['stage4']['coils_count'] }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>الوزن الإجمالي:</strong> {{ $productionData['stage4']['total_weight'] }} كجم</p>
                                            <p><strong>العامل:</strong> {{ $productionData['stage4']['worker'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i> ملخص الإنتاج
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="summary-item text-center">
                                <div class="summary-icon bg-primary text-white">
                                    <i class="fas fa-weight"></i>
                                </div>
                                <h6>الوزن الأصلي</h6>
                                <p class="summary-value">{{ $productionData['warehouse']['original_weight'] }} كجم</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-item text-center">
                                <div class="summary-icon bg-info text-white">
                                    <i class="fas fa-percentage"></i>
                                </div>
                                <h6>إجمالي الهدر</h6>
                                <p class="summary-value">
                                    {{ $productionData['stage1']['waste'] + $productionData['stage2']['waste'] + $productionData['stage3']['waste'] }} كجم
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-item text-center">
                                <div class="summary-icon bg-success text-white">
                                    <i class="fas fa-balance-scale"></i>
                                </div>
                                <h6>الوزن النهائي</h6>
                                <p class="summary-value">{{ $productionData['stage4']['total_weight'] }} كجم</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-item text-center">
                                <div class="summary-icon bg-warning text-white">
                                    <i class="fas fa-percentage"></i>
                                </div>
                                <h6>نسبة الهدر</h6>
                                <p class="summary-value">
                                    {{ round((($productionData['warehouse']['original_weight'] - $productionData['stage4']['total_weight']) / $productionData['warehouse']['original_weight']) * 100, 2) }}%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>


    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="{{ route('manufacturing.production-tracking.scan') }}" class="btn btn-primary">
                <i class="fas fa-barcode"></i> مسح باركود جديد
            </a>
            <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print"></i> طباعة التقرير
            </button>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 4px;
        background-color: #e9ecef;
        left: 50%;
        margin-left: -2px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 50px;
    }

    .timeline-badge {
        position: absolute;
        left: 50%;
        margin-left: -20px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 100;
    }

    .timeline-panel {
        width: 45%;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 5px;
        padding: 20px;
        position: relative;
    }

    .timeline-item:nth-child(odd) .timeline-panel {
        float: left;
        margin-left: 5%;
    }

    .timeline-item:nth-child(even) .timeline-panel {
        float: right;
        margin-right: 5%;
    }

    .timeline-heading {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .timeline-title {
        margin-top: 0;
        color: inherit;
        font-weight: bold;
    }

    .timeline-date {
        margin-bottom: 0;
        color: #6c757d;
        font-size: 0.875em;
    }

    .summary-item {
        padding: 20px;
    }

    .summary-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 1.5rem;
    }

    .summary-value {
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .timeline:before {
            left: 40px;
        }

        .timeline-badge {
            left: 40px;
            margin-left: 0;
        }

        .timeline-panel {
            width: calc(100% - 100px);
            float: right !important;
            margin-right: 20px !important;
        }
    }
</style>
@endsection
