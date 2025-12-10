
@extends('master')

@section('title', 'تقرير إدارة المرحلة الثالثة')

@section('content')

<style>
    :root {
        --primary: #0b5fa5;
        --success: #27ae60;
        --warning: #f39c12;
        --danger: #e74c3c;
        --info: #3498db;
        --light: #ecf0f1;
        --dark: #2c3e50;
    }

    * {
        box-sizing: border-box;
    }

    body {
        background: #f5f6fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .report-container {
        max-width: 1400px;
        margin: 30px auto;
        padding: 20px;
    }

    /* Header */
    .report-header {
        background: linear-gradient(135deg, var(--primary) 0%, #2a9fd6 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(11, 95, 165, 0.2);
    }

    .report-header h1 {
        margin: 0;
        font-size: 32px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .report-header p {
        margin: 10px 0 0 0;
        opacity: 0.9;
        font-size: 16px;
    }

    .report-date {
        text-align: right;
        font-size: 14px;
        opacity: 0.8;
    }

    /* KPI Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .kpi-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-top: 4px solid var(--primary);
        transition: all 0.3s ease;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .kpi-card.success {
        border-top-color: var(--success);
    }

    .kpi-card.warning {
        border-top-color: var(--warning);
    }

    .kpi-card.danger {
        border-top-color: var(--danger);
    }

    .kpi-card.info {
        border-top-color: var(--info);
    }

    .kpi-icon {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .kpi-label {
        font-size: 13px;
        color: #7f8c8d;
        font-weight: 600;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .kpi-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 5px;
    }

    .kpi-unit {
        font-size: 13px;
        color: #95a5a6;
    }

    .kpi-change {
        font-size: 12px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--light);
    }

    .kpi-change.positive {
        color: var(--success);
    }

    .kpi-change.negative {
        color: var(--danger);
    }

    /* Section */
    .report-section {
        background: white;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 25px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--light);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title i {
        color: var(--primary);
        font-size: 24px;
    }

    /* Table */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .data-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid var(--light);
    }

    .data-table th {
        padding: 12px 15px;
        text-align: right;
        font-weight: 600;
        color: var(--dark);
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .data-table td {
        padding: 12px 15px;
        border-bottom: 1px solid var(--light);
        font-size: 14px;
    }

    .data-table tbody tr:hover {
        background: #f8f9fa;
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
    }

    .status-created {
        background: #e3f2fd;
        color: #1976d2;
    }

    .status-in_process {
        background: #fff3e0;
        color: #f57c00;
    }

    .status-completed {
        background: #e8f5e9;
        color: #388e3c;
    }

    .status-pending_approval {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .status-consumed {
        background: #eeeeee;
        color: #616161;
    }

    /* Progress Bar */
    .progress-bar {
        height: 8px;
        background: #ecf0f1;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 10px;
    }

    .progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s ease;
        background: linear-gradient(90deg, var(--success), #27ae60);
    }

    .progress-fill.warning {
        background: linear-gradient(90deg, var(--warning), #e67e22);
    }

    .progress-fill.danger {
        background: linear-gradient(90deg, var(--danger), #c0392b);
    }

    /* Waste Level */
    .waste-level {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
    }

    .waste-level.safe {
        background: #e8f5e9;
        color: #388e3c;
    }

    .waste-level.warning {
        background: #fff3e0;
        color: #f57c00;
    }

    .waste-level.critical {
        background: #ffebee;
        color: #d32f2f;
    }

    /* Stat Row */
    .stat-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .stat-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-right: 3px solid var(--primary);
    }

    .stat-item.success {
        border-right-color: var(--success);
    }

    .stat-item.warning {
        border-right-color: var(--warning);
    }

    .stat-item.danger {
        border-right-color: var(--danger);
    }

    .stat-label {
        font-size: 12px;
        color: #7f8c8d;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 20px;
        font-weight: 700;
        color: var(--dark);
    }

    /* Alert */
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-right: 4px solid;
    }

    .alert-success {
        background: #e8f5e9;
        border-right-color: var(--success);
        color: #388e3c;
    }

    .alert-warning {
        background: #fff3e0;
        border-right-color: var(--warning);
        color: #f57c00;
    }

    .alert-danger {
        background: #ffebee;
        border-right-color: var(--danger);
        color: #d32f2f;
    }

    .alert-info {
        background: #e3f2fd;
        border-right-color: var(--info);
        color: #1976d2;
    }

    /* Chart Container */
    .chart-container {
        position: relative;
        height: 300px;
        margin-top: 20px;
    }

    /* Two Column Layout */
    .two-column {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .two-column {
            grid-template-columns: 1fr;
        }

        .kpi-grid {
            grid-template-columns: 1fr;
        }

        .report-header h1 {
            font-size: 24px;
        }
    }

    /* Badge */
    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-primary {
        background: #e3f2fd;
        color: #1976d2;
    }

    .badge-success {
        background: #e8f5e9;
        color: #388e3c;
    }

    .badge-warning {
        background: #fff3e0;
        color: #f57c00;
    }

    .badge-danger {
        background: #ffebee;
        color: #d32f2f;
    }

    /* Print Styles */
    @media print {
        .report-container {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        .report-section {
            page-break-inside: avoid;
            box-shadow: none;
            border: 1px solid #ddd;
        }

        .kpi-card {
            page-break-inside: avoid;
        }
    }
</style>

<div class="report-container">
    <!-- Header -->
    <div class="report-header">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h1>
                    <i class="fas fa-palette"></i>
                    تقرير إدارة المرحلة الثالثة
                </h1>
                <p>🏭 نظام إدارة الإنتاج المتكامل - Iron Factory</p>
            </div>
            <div class="report-date">
                <div style="font-weight: 600; margin-bottom: 5px;">{{ date('Y-m-d H:i') }}</div>
                <div style="font-size: 12px;">التقرير الحالي</div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <!-- إجمالي الملفات -->
        <div class="kpi-card success">
            <div class="kpi-icon">📄</div>
            <div class="kpi-label">إجمالي الملفات</div>
            <div class="kpi-value">{{ $totalStands ?? 0 }}</div>
            <div class="kpi-unit">ملف</div>
            <div class="kpi-change positive">
                ↑ {{ $standsToday ?? 0 }} اليوم
            </div>
        </div>

        <!-- الملفات المكتملة -->
        <div class="kpi-card success">
            <div class="kpi-icon">✅</div>
            <div class="kpi-label">الملفات المكتملة</div>
            <div class="kpi-value">{{ $completedStands ?? 0 }}</div>
            <div class="kpi-unit">{{ $completionRate ?? 0 }}%</div>
            <div class="kpi-change positive">
                ✓ مجهزة للتسليم
            </div>
        </div>

        <!-- الملفات المعلقة -->
        <div class="kpi-card warning">
            <div class="kpi-icon">⏸️</div>
            <div class="kpi-label">الملفات المعلقة</div>
            <div class="kpi-value">{{ $pendingStands ?? 0 }}</div>
            <div class="kpi-unit">في انتظار الموافقة</div>
            <div class="kpi-change">
                ⚠️ بسبب مشاكل في التلوين
            </div>
        </div>

        <!-- إجمالي المادة الداخلة -->
        <div class="kpi-card info">
            <div class="kpi-icon">📥</div>
            <div class="kpi-label">إجمالي المادة الداخلة</div>
            <div class="kpi-value">{{ $totalInputWeight ?? 0 }}</div>
            <div class="kpi-unit">كجم</div>
            <div class="kpi-change">
                🏭 من المرحلة الثانية
            </div>
        </div>

        <!-- الوزن الصافي الخارج -->
        <div class="kpi-card success">
            <div class="kpi-icon">📤</div>
            <div class="kpi-label">الوزن الصافي الخارج</div>
            <div class="kpi-value">{{ $totalOutputWeight ?? 0 }}</div>
            <div class="kpi-unit">كجم</div>
            <div class="kpi-change positive">
                ✓ جاهز للتسليم
            </div>
        </div>

        <!-- إجمالي الهدر -->
        <div class="kpi-card danger">
            <div class="kpi-icon">♻️</div>
            <div class="kpi-label">إجمالي الهدر</div>
            <div class="kpi-value">{{ $totalWaste ?? 0 }}</div>
            <div class="kpi-unit">كجم</div>
            <div class="kpi-change">
                📊 متوسط: {{ $avgWastePercentage ?? 0 }}%
            </div>
        </div>

        <!-- أعلى نسبة هدر -->
        <div class="kpi-card danger">
            <div class="kpi-icon">⚠️</div>
            <div class="kpi-label">أعلى نسبة هدر</div>
            <div class="kpi-value">{{ $maxWastePercentage ?? 0 }}%</div>
            <div class="kpi-unit">Stand: {{ $maxWasteBarcode ?? '-' }}</div>
            <div class="kpi-change negative">
                🔴 تنبيه
            </div>
        </div>

        <!-- أقل نسبة هدر -->
        <div class="kpi-card success">
            <div class="kpi-icon">🎯</div>
            <div class="kpi-label">أقل نسبة هدر</div>
            <div class="kpi-value">{{ $minWastePercentage ?? 0 }}%</div>
            <div class="kpi-unit">Stand: {{ $minWasteBarcode ?? '-' }}</div>
            <div class="kpi-change positive">
                ✓ ممتاز
            </div>
        </div>

        <!-- عدد العمال -->
        <div class="kpi-card info">
            <div class="kpi-icon">👥</div>
            <div class="kpi-label">عدد العمال النشطين</div>
            <div class="kpi-value">{{ $activeWorkers ?? 0 }}</div>
            <div class="kpi-unit">عامل</div>
            <div class="kpi-change">
                👨‍🔧 في هذه الفترة
            </div>
        </div>

        <!-- متوسط الأداء اليومي -->
        <div class="kpi-card success">
            <div class="kpi-icon">📈</div>
            <div class="kpi-label">متوسط أداء يومي</div>
            <div class="kpi-value">{{ $avgDailyProduction ?? 0 }}</div>
            <div class="kpi-unit">استاند/يوم</div>
            <div class="kpi-change positive">
                ↑ نمو إيجابي
            </div>
        </div>

        <!-- معدل الالتزام -->
        <div class="kpi-card success">
            <div class="kpi-icon">✓</div>
            <div class="kpi-label">معدل الالتزام بالجودة</div>
            <div class="kpi-value">{{ $complianceRate ?? 0 }}%</div>
            <div class="kpi-unit">استاندات مقبولة</div>
            <div class="kpi-change positive">
                ✓ ممتاز
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    @if($pendingStands > 0)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>تنبيه:</strong> هناك {{ $pendingStands }} ملف في انتظار الموافقة بسبب مشاكل في التلوين
    </div>
    @endif

    @if($maxWastePercentage > 15)
    <div class="alert alert-danger">
        <i class="fas fa-alert-circle"></i>
        <strong>خطر:</strong> تم اكتشاف استاند بنسبة هدر عالية جداً ({{ $maxWastePercentage }}%) - يتطلب مراجعة فورية
    </div>
    @endif

    @if($avgWastePercentage < 5)
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <strong>ممتاز:</strong> متوسط نسبة الخطأ في المرحلة الثالثة في المستوى الأمثل ({{ $avgWastePercentage }}%)
    </div>
    @endif

    <!-- Filters Section -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-filter"></i>
            البحث والتصفية
        </div>

        <form method="GET" action="{{ route('manufacturing.reports.stage1-management') }}" style="margin-top: 15px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">

                <!-- البحث بالباركود -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">🔍 البحث بالباركود</label>
                    <input type="text" name="search" class="um-form-control" placeholder="مثلاً: ST1-001" value="{{ $filters['search'] ?? '' }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                </div>

                <!-- التصفية بالحالة -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📊 الحالة</label>
                    <select name="status" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="">-- الكل --</option>
                        <option value="created" {{ ($filters['status'] ?? '') === 'created' ? 'selected' : '' }}>إنشاء جديد</option>
                        <option value="in_process" {{ ($filters['status'] ?? '') === 'in_process' ? 'selected' : '' }}>قيد المعالجة</option>
                        <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="packed" {{ ($filters['status'] ?? '') === 'packed' ? 'selected' : '' }}>مغلف</option>
                    </select>
                </div>

                <!-- التصفية بالعامل -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">👤 العامل</label>
                    <select name="worker_id" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="">-- الكل --</option>
                        @foreach($workers as $worker)
                        <option value="{{ $worker->id }}" {{ ($filters['worker_id'] ?? '') == $worker->id ? 'selected' : '' }}>{{ $worker->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- من التاريخ -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📅 من التاريخ</label>
                    <input type="date" name="from_date" class="um-form-control" value="{{ $filters['from_date'] ?? '' }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                </div>

                <!-- إلى التاريخ -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📅 إلى التاريخ</label>
                    <input type="date" name="to_date" class="um-form-control" value="{{ $filters['to_date'] ?? '' }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                </div>

                <!-- مستوى الهدر -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">⚠️ مستوى الهدر</label>
                    <select name="waste_level" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="">-- الكل --</option>
                        <option value="safe" {{ ($filters['waste_level'] ?? '') === 'safe' ? 'selected' : '' }}>آمن (0-8%)</option>
                        <option value="warning" {{ ($filters['waste_level'] ?? '') === 'warning' ? 'selected' : '' }}>تحذير (8-15%)</option>
                        <option value="critical" {{ ($filters['waste_level'] ?? '') === 'critical' ? 'selected' : '' }}>حرج (>15%)</option>
                    </select>
                </div>

                <!-- الترتيب -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">🔄 ترتيب حسب</label>
                    <select name="sort_by" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>التاريخ</option>
                        <option value="weight" {{ request('sort_by') === 'weight' ? 'selected' : '' }}>الوزن الكلي</option>
                        <option value="waste" {{ request('sort_by') === 'waste' ? 'selected' : '' }}>الهدر</option>
                        <option value="barcode" {{ request('sort_by') === 'barcode' ? 'selected' : '' }}>الباركود</option>
                    </select>
                </div>

                <!-- ترتيب تصاعدي/تنازلي -->
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: var(--dark);">📈 الاتجاه</label>
                    <select name="sort_order" class="um-form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                        <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>تنازلي (الأحدث أولاً)</option>
                        <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>تصاعدي</option>
                    </select>
                </div>
            </div>

            <!-- أزرار الإجراء -->
            <div style="display: flex; gap: 10px; margin-top: 15px;">
                <button type="submit" class="um-btn um-btn-primary" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-search"></i> بحث وتصفية
                </button>
                <a href="{{ route('manufacturing.reports.stage1-management') }}" class="um-btn um-btn-outline" style="padding: 10px 20px; background: #ecf0f1; color: var(--dark); border: none; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block;">
                    <i class="fas fa-redo"></i> إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- جدول السجلات الكاملة -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-table"></i>
            جميع السجلات ({{ $allRecords->count() }} سجل)
        </div>

        @if($allRecords && count($allRecords) > 0)
        <div style="overflow-x: auto; margin-top: 15px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الباركود</th>
                        <th>المادة</th>
                        <th>الملف</th>
                        <th>الوزن الكلي</th>
                        <th>الوزن الصافي</th>
                        <th>الهدر</th>
                        <th>نسبة الهدر</th>
                        <th>الحالة</th>
                        <th>العامل</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allRecords as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $record->barcode ?? '-' }}</strong></td>
                        <td>{{ $record->material_name ?? '-' }}</td>
                        <td>{{ $record->stand_number ?? '-' }}</td>
                        <td style="text-align: center;">{{ $record->weight ?? 0 }} كجم</td>
                        <td style="text-align: center;">{{ $record->remaining_weight ?? 0 }} كجم</td>
                        <td style="text-align: center;">{{ $record->waste ?? 0 }} كجم</td>
                        <td style="text-align: center;">
                            @php
                                $wastePerc = $record->weight > 0 ? round((($record->weight - $record->remaining_weight) / $record->weight) * 100, 2) : 0;
                                $wasteClass = $wastePerc > 12 ? 'critical' : ($wastePerc > 8 ? 'warning' : 'safe');
                            @endphp
                            <span class="waste-level {{ $wasteClass }}">{{ $wastePerc }}%</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge status-{{ $record->status ?? 'created' }}">
                                @if($record->status === 'created')
                                    إنشاء جديد
                                @elseif($record->status === 'in_process')
                                    قيد المعالجة
                                @elseif($record->status === 'completed')
                                    مكتمل
                                @elseif($record->status === 'pending_approval')
                                    في انتظار موافقة
                                @elseif($record->status === 'consumed')
                                    مستهلك
                                @else
                                    {{ $record->status }}
                                @endif
                            </span>
                        </td>
                        <td>{{ $record->created_by_name ?? '-' }}</td>
                        <td>
                            @if ($record->created_at)
                                @if (is_string($record->created_at))
                                    {{ substr($record->created_at, 0, 16) }}
                                @else
                                    {{ $record->created_at->format('Y-m-d H:i') }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 30px; color: #7f8c8d;">
                            <i class="fas fa-inbox"></i> لا توجد سجلات تطابق معايير البحث
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
            <p>لا توجد سجلات</p>
        </div>
        @endif
    </div>

    <!-- Detailed Statistics Section -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-bar-chart"></i>
            إحصائيات مفصلة
        </div>

        <div class="stat-row">
            <div class="stat-item success">
                <div class="stat-label">معدل الإتمام</div>
                <div class="stat-value">{{ $completionRate ?? 0 }}%</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $completionRate ?? 0 }}%"></div>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-label">معدل الهدر</div>
                <div class="stat-value">{{ $avgWastePercentage ?? 0 }}%</div>
                <div class="progress-bar">
                    <div class="progress-fill {{ $avgWastePercentage > 12 ? 'danger' : ($avgWastePercentage > 8 ? 'warning' : '') }}" style="width: {{ min($avgWastePercentage ?? 0, 100) }}%"></div>
                </div>
            </div>

            <div class="stat-item success">
                <div class="stat-label">كفاءة الإنتاج</div>
                <div class="stat-value">{{ $productionEfficiency ?? 0 }}%</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $productionEfficiency ?? 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-pie-chart"></i>
            توزيع حالات الاستاندات
        </div>

        <div class="stat-row">
            <div class="stat-item">
                <div class="stat-label">إنشاء جديد</div>
                <div class="stat-value" style="color: #3498db;">{{ $statusCreated ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ round(($statusCreated ?? 0) / max($totalStands, 1) * 100) }}%</small>
            </div>

            <div class="stat-item warning">
                <div class="stat-label">قيد المعالجة</div>
                <div class="stat-value" style="color: #f39c12;">{{ $statusInProcess ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ round(($statusInProcess ?? 0) / max($totalStands, 1) * 100) }}%</small>
            </div>

            <div class="stat-item success">
                <div class="stat-label">مكتمل</div>
                <div class="stat-value" style="color: #27ae60;">{{ $statusCompleted ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ round(($statusCompleted ?? 0) / max($totalStands, 1) * 100) }}%</small>
            </div>

            <div class="stat-item">
                <div class="stat-label">في انتظار موافقة</div>
                <div class="stat-value" style="color: #8e44ad;">{{ $statusPending ?? 0 }}</div>
                <small style="color: #7f8c8d;">{{ round(($statusPending ?? 0) / max($totalStands, 1) * 100) }}%</small>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-trophy"></i>
            أفضل الأداء
        </div>

        <div class="two-column">
            <!-- Best Worker -->
            <div>
                <h4 style="margin-bottom: 15px; color: var(--dark);">🏆 أفضل عامل</h4>
                <div class="stat-item success">
                    <div class="stat-label">الاسم</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $bestWorkerName ?? 'غير متوفر' }}</div>
                    <hr style="margin: 10px 0; border: none; border-top: 1px solid var(--light);">
                    <div class="stat-label">عدد الاستاندات</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $bestWorkerCount ?? 0 }}</div>
                    <div class="stat-label" style="margin-top: 10px;">متوسط الهدر</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $bestWorkerAvgWaste ?? 0 }}%</div>
                </div>
            </div>

            <!-- Best Stand -->
            <div>
                <h4 style="margin-bottom: 15px; color: var(--dark);">⭐ أفضل استاند</h4>
                <div class="stat-item success">
                    <div class="stat-label">رقم الاستاند</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $bestStandNumber ?? 'غير متوفر' }}</div>
                    <hr style="margin: 10px 0; border: none; border-top: 1px solid var(--light);">
                    <div class="stat-label">نسبة الهدر</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $bestStandWaste ?? 0 }}%</div>
                    <div class="stat-label" style="margin-top: 10px;">عدد الاستخدامات</div>
                    <div class="stat-value" style="font-size: 18px;">{{ $bestStandUsageCount ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Records Table -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-list"></i>
            آخر 10 سجلات
        </div>

        @if($recentRecords && count($recentRecords) > 0)
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الباركود</th>
                        <th>المادة</th>
                        <th>الوزن الصافي</th>
                        <th>الهدر</th>
                        <th>النسبة %</th>
                        <th>الحالة</th>
                        <th>العامل</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRecords as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $record->barcode ?? '-' }}</strong></td>
                        <td>{{ $record->material_name ?? '-' }}</td>
                        <td>{{ $record->remaining_weight ?? 0 }} كجم</td>
                        <td>{{ $record->waste ?? 0 }} كجم</td>
                        <td>
                            @php
                                $wastePerc = $record->waste_percentage ?? 0;
                                $class = $wastePerc > 12 ? 'critical' : ($wastePerc > 8 ? 'warning' : 'safe');
                            @endphp
                            <span class="waste-level {{ $class }}">{{ $wastePerc }}%</span>
                        </td>
                        <td><span class="status-badge status-{{ $record->status ?? 'created' }}">{{ ucfirst($record->status ?? 'created') }}</span></td>
                        <td>{{ $record->created_by_name ?? '-' }}</td>
                        <td>
                            @if ($record->created_at)
                                @if (is_string($record->created_at))
                                    {{ substr($record->created_at, 0, 16) }}
                                @else
                                    {{ $record->created_at->format('Y-m-d H:i') }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 20px; color: #7f8c8d;">
                            <i class="fas fa-inbox"></i> لا توجد سجلات حتى الآن
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <i class="fas fa-chart-line" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
            <p>لا توجد بيانات في المرحلة الثالثة حتى الآن</p>
        </div>
        @endif
    </div>

    <!-- Waste Analysis -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-eye"></i>
            تحليل الهدر
        </div>

        <div class="stat-row">
            <div class="stat-item success">
                <div class="stat-label">الهدر المقبول (0-8%)</div>
                <div class="stat-value" style="color: #27ae60;">{{ $acceptableWaste ?? 0 }}</div>
                <small style="color: #7f8c8d;">استاند</small>
            </div>

            <div class="stat-item warning">
                <div class="stat-label">الهدر التحذيري (8-15%)</div>
                <div class="stat-value" style="color: #f39c12;">{{ $warningWaste ?? 0 }}</div>
                <small style="color: #7f8c8d;">استاند - يتطلب ملاحظة</small>
            </div>

            <div class="stat-item danger">
                <div class="stat-label">الهدر الحرج (>15%)</div>
                <div class="stat-value" style="color: #e74c3c;">{{ $criticalWaste ?? 0 }}</div>
                <small style="color: #7f8c8d;">استاند - يتطلب متابعة</small>
            </div>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border-right: 3px solid var(--primary);">
            <h4 style="margin-top: 0; color: var(--dark);">📊 ملخص جودة الإنتاج</h4>
            <p>متوسط نسبة الخطأ في المرحلة الثالثة: <strong>{{ $avgWastePercentage ?? 0 }}%</strong></p>

            <p style="margin-bottom: 0;">
                @if(($avgWastePercentage ?? 0) < 8)
                    <span class="badge badge-success">✓ ممتاز - الأداء أفضل من المتوقع</span>
                @elseif(($avgWastePercentage ?? 0) < 12)
                    <span class="badge badge-warning">⚠️ جيد - ضمن الحدود المقبولة</span>
                @else
                    <span class="badge badge-danger">⚠️ تحذير - يتطلب مراجعة</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Material Flow -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-arrow-right"></i>
            تتبع تدفق المادة
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <div style="text-align: center;">
                <div style="font-size: 28px; color: var(--primary); font-weight: 700;">{{ $totalInputWeight ?? 0 }} كجم</div>
                <div style="color: #7f8c8d; font-size: 13px; margin-top: 5px;">المادة الداخلة</div>
                <div style="color: #95a5a6; font-size: 11px;">من المرحلة الثانية</div>
            </div>

            <div style="font-size: 32px; color: #bdc3c7;">→</div>

            <div style="text-align: center;">
                <div style="font-size: 28px; color: var(--success); font-weight: 700;">{{ $totalOutputWeight ?? 0 }} كجم</div>
                <div style="color: #7f8c8d; font-size: 13px; margin-top: 5px;">المنتجات النهائية</div>
                <div style="color: #95a5a6; font-size: 11px;">جاهز للتسليم</div>
            </div>

            <div style="font-size: 32px; color: #bdc3c7;">→</div>

            <div style="text-align: center;">
                <div style="font-size: 28px; color: var(--danger); font-weight: 700;">{{ $totalWaste ?? 0 }} كجم</div>
                <div style="color: #7f8c8d; font-size: 13px; margin-top: 5px;">الهدر</div>
                <div style="color: #95a5a6; font-size: 11px;">{{ round(($totalWaste ?? 0) / max($totalInputWeight, 1) * 100) }}%</div>
            </div>
        </div>
    </div>

    <!-- Daily Operations Timeline -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-history"></i>
            سجل العمليات اليومية (Timeline)
        </div>

        @if($dailyOperations && count($dailyOperations) > 0)
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>عدد الاستاندات</th>
                        <th>إجمالي الكمية الداخلة</th>
                        <th>إجمالي الكمية الخارجة</th>
                        <th>إجمالي الهدر</th>
                        <th>متوسط نسبة الهدر</th>
                        <th>الاستاندات المكتملة</th>
                        <th>الاستاندات المعلقة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyOperations as $day)
                    <tr>
                        <td><strong>{{ $day['date'] }}</strong></td>
                        <td style="text-align: center;">
                            <span class="badge badge-primary">{{ $day['count'] }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--primary); font-weight: 600;">{{ $day['total_input'] }} كجم</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--success); font-weight: 600;">{{ $day['total_output'] }} كجم</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--danger); font-weight: 600;">{{ $day['total_waste'] }} كجم</span>
                        </td>
                        <td style="text-align: center;">
                            @php
                                $wasteClass = $day['avg_waste'] > 12 ? 'critical' : ($day['avg_waste'] > 8 ? 'warning' : 'safe');
                            @endphp
                            <span class="waste-level {{ $wasteClass }}">{{ $day['avg_waste'] }}%</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge status-completed">{{ $day['completed'] }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge status-pending_approval">{{ $day['pending'] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px; color: #7f8c8d;">
                            <i class="fas fa-inbox"></i> لا توجد بيانات يومية
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <i class="fas fa-history" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
            <p>لا توجد بيانات يومية</p>
        </div>
        @endif
    </div>

    <!-- Cumulative Progress -->
    <div class="report-section">
        <div class="section-title">
            <i class="fas fa-chart-area"></i>
            تراكم العمليات (Cumulative)
        </div>

        @if($cumulativeData && count($cumulativeData) > 0)
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>إجمالي كمي الداخلة (منذ البداية)</th>
                        <th>إجمالي الكمية المنجزة</th>
                        <th>إجمالي الهدر (منذ البداية)</th>
                        <th>نسبة الإنجاز</th>
                        <th>نسبة الهدر الإجمالية</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cumulativeData as $day)
                    <tr>
                        <td><strong>{{ $day['date'] }}</strong></td>
                        <td style="text-align: center;">
                            <span style="color: var(--primary); font-weight: 600;">{{ $day['cumulative_input'] }} كجم</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--success); font-weight: 600;">{{ $day['cumulative_output'] }} كجم</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="color: var(--danger); font-weight: 600;">{{ $day['cumulative_waste'] }} كجم</span>
                        </td>
                        <td style="text-align: center;">
                            <div class="progress-bar" style="width: 100px; margin: 0 auto;">
                                <div class="progress-fill" style="width: {{ $day['completion_percentage'] }}%; min-width: 20px;"></div>
                            </div>
                            <small style="color: #7f8c8d; display: block; margin-top: 5px;">{{ $day['completion_percentage'] }}%</small>
                        </td>
                        <td style="text-align: center;">
                            @php
                                $wastePerc = $day['total_waste_percentage'];
                                $wasteClass = $wastePerc > 12 ? 'critical' : ($wastePerc > 8 ? 'warning' : 'safe');
                            @endphp
                            <span class="waste-level {{ $wasteClass }}">{{ $wastePerc }}%</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #7f8c8d;">
                            <i class="fas fa-inbox"></i> لا توجد بيانات تراكمية
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <i class="fas fa-chart-area" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
            <p>لا توجد بيانات تراكمية</p>
        </div>
        @endif
    </div>

    <!-- Print Button -->
    <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn btn-primary" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
            <i class="fas fa-print"></i> طباعة التقرير
        </button>
        <button onclick="window.history.back()" class="btn btn-secondary" style="padding: 10px 20px; background: #95a5a6; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; margin-right: 10px;">
            <i class="fas fa-arrow-left"></i> رجوع
        </button>
    </div>

    <!-- Footer -->
    <div style="text-align: center; color: #7f8c8d; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--light);">
        <p>تم إنشاء هذا التقرير من قبل نظام إدارة الإنتاج المتكامل - Iron Factory</p>
        <p>© 2025 جميع الحقوق محفوظة</p>
    </div>
</div>

@endsection
