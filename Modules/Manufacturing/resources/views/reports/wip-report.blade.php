@extends('master')

@section('title', 'تقرير القطع قيد التشغيل (WIP)')

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
    .wip-container {
        font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: var(--spacing-lg);
        background-color: var(--gray-50);
        min-height: calc(100vh - 60px);
    }

    /* رأس التقرير */
    .wip-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        padding: var(--spacing-xl);
        border-radius: var(--radius-xl);
        color: var(--white);
        margin-bottom: var(--spacing-xl);
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
    }

    .wip-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
        pointer-events: none;
    }

    .wip-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .wip-header p {
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

    .stat-card.danger::before {
        background: linear-gradient(90deg, var(--danger-color), #f87171);
    }

    .stat-card.warning::before {
        background: linear-gradient(90deg, var(--warning-color), #fbbf24);
    }

    .stat-card.success::before {
        background: linear-gradient(90deg, var(--success-color), #34d399);
    }

    .stat-card.info::before {
        background: linear-gradient(90deg, var(--info-color), #60a5fa);
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

    .stat-icon.danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }

    .stat-icon.warning {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .stat-icon.success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .stat-icon.info {
        background: rgba(59, 130, 246, 0.1);
        color: var(--info-color);
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

    .stage-count {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
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

    .filter-grid {
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

    .filter-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
    }

    .filter-input {
        padding: var(--spacing-sm) var(--spacing-md);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        transition: all var(--transition-base);
        background: var(--white);
        font-family: inherit;
    }

    .filter-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 102, 178, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: var(--spacing-md);
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: var(--spacing-sm) var(--spacing-xl);
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: var(--white);
        border: none;
        border-radius: var(--radius-md);
        cursor: pointer;
        font-weight: 600;
        transition: all var(--transition-base);
        box-shadow: var(--shadow-sm);
        font-family: inherit;
    }

    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .reset-btn {
        padding: var(--spacing-sm) var(--spacing-xl);
        background: var(--gray-600);
        color: var(--white);
        border: none;
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: 600;
        transition: all var(--transition-base);
        box-shadow: var(--shadow-sm);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: inherit;
    }

    .reset-btn:hover {
        background: var(--gray-800);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* جدول البيانات */
    .data-table-container {
        background: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-200);
        overflow: hidden;
        position: relative;
    }

    .data-table-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .table-header {
        padding: var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .table-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: right;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--gray-300);
        background: var(--gray-50);
    }

    .data-table tbody td {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: right;
        font-size: 0.875rem;
        color: var(--gray-800);
        border-bottom: 1px solid var(--gray-200);
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .data-table tbody tr {
        transition: all var(--transition-base);
    }

    .data-table tbody tr:hover {
        background-color: rgba(0, 102, 178, 0.05);
    }

    .delay-badge {
        display: inline-flex;
        align-items: center;
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
    }

    .delay-normal {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .delay-warning {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .delay-critical {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }

    .status-text {
        font-weight: 600;
    }

    .status-critical {
        color: var(--danger-color);
    }

    .status-warning {
        color: var(--warning-color);
    }

    .status-normal {
        color: var(--success-color);
    }

    /* حالة فارغة */
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--gray-600);
    }

    .empty-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
        color: var(--success-color);
    }

    /* استجابة الشاشات */
    @media (max-width: 768px) {
        .wip-container {
            padding: var(--spacing-md);
        }

        .wip-header {
            padding: var(--spacing-lg);
        }

        .wip-header h1 {
            font-size: 1.5rem;
        }

        .stats-grid,
        .stage-grid,
        .filter-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-direction: column;
        }

        .data-table {
            display: block;
            overflow-x: auto;
        }

        .table-header {
            padding: var(--spacing-md);
        }

        .data-table thead th,
        .data-table tbody td {
            padding: var(--spacing-sm);
        }
    }
</style>

<div class="wip-container">
    <div class="wip-header">
        <h1>
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            تقرير القطع قيد التشغيل (WIP)
        </h1>
        <p>القطع التي بدأت في مراحل الإنتاج ولم تكتمل بعد</p>
    </div>

    <!-- الإحصائيات الرئيسية -->
    <div class="stats-grid">
        <div class="stat-card danger">
            <div class="stat-header">
                <div>
                    <div class="stat-title">إجمالي القطع العالقة</div>
                    <p class="stat-value">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="stat-icon danger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-header">
                <div>
                    <div class="stat-title">متوسط التأخير</div>
                    <p class="stat-value">{{ $stats['avg_delay_hours'] }}</p>
                    <small style="color: var(--gray-600);">ساعة</small>
                </div>
                <div class="stat-icon warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card info">
            <div class="stat-header">
                <div>
                    <div class="stat-title">الوزن الإجمالي العالق</div>
                    <p class="stat-value">{{ number_format($stats['total_weight_kg'], 2) }}</p>
                    <small style="color: var(--gray-600);">كيلوجرام</small>
                </div>
                <div class="stat-icon info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card danger">
            <div class="stat-header">
                <div>
                    <div class="stat-title">حالات حرجة (>24 ساعة)</div>
                    <p class="stat-value">{{ number_format($stats['critical_count']) }}</p>
                </div>
                <div class="stat-icon danger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- توزيع المراحل -->
    <div class="stage-grid">
        <div class="stage-card stage-1">
            <div class="stage-name">المرحلة 1 - التقسيم</div>
            <p class="stage-count">{{ $stats['by_stage']['stage1'] }} قطعة</p>
        </div>
        <div class="stage-card stage-2">
            <div class="stage-name">المرحلة 2 - المعالجة</div>
            <p class="stage-count">{{ $stats['by_stage']['stage2'] }} قطعة</p>
        </div>
        <div class="stage-card stage-3">
            <div class="stage-name">المرحلة 3 - اللف</div>
            <p class="stage-count">{{ $stats['by_stage']['stage3'] }} قطعة</p>
        </div>
        <div class="stage-card stage-4">
            <div class="stage-name">المرحلة 4 - التعليب</div>
            <p class="stage-count">{{ $stats['by_stage']['stage4'] }} قطعة</p>
        </div>
    </div>

    <!-- فلاتر البيانات -->
    <div class="filters-card">
        <h3>فلترة البيانات</h3>
        <form method="GET" action="{{ route('manufacturing.reports.wip') }}">
            <div class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label">المرحلة</label>
                    <select name="stage" class="filter-input">
                        <option value="">جميع المراحل</option>
                        <option value="1" {{ request('stage') == 1 ? 'selected' : '' }}>المرحلة 1</option>
                        <option value="2" {{ request('stage') == 2 ? 'selected' : '' }}>المرحلة 2</option>
                        <option value="3" {{ request('stage') == 3 ? 'selected' : '' }}>المرحلة 3</option>
                        <option value="4" {{ request('stage') == 4 ? 'selected' : '' }}>المرحلة 4</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">الحد الأدنى للتأخير (ساعات)</label>
                    <select name="delay_hours" class="filter-input">
                        <option value="">الكل</option>
                        <option value="6" {{ request('delay_hours') == 6 ? 'selected' : '' }}>أكثر من 6 ساعات</option>
                        <option value="12" {{ request('delay_hours') == 12 ? 'selected' : '' }}>أكثر من 12 ساعة</option>
                        <option value="24" {{ request('delay_hours') == 24 ? 'selected' : '' }}>أكثر من 24 ساعة</option>
                        <option value="48" {{ request('delay_hours') == 48 ? 'selected' : '' }}>أكثر من 48 ساعة</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">العامل</label>
                    <select name="worker_id" class="filter-input">
                        <option value="">جميع العمال</option>
                        @foreach($workers as $worker)
                            <option value="{{ $worker->id }}" {{ request('worker_id') == $worker->id ? 'selected' : '' }}>
                                {{ $worker->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">من تاريخ</label>
                    <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
                </div>

                <div class="filter-group">
                    <label class="filter-label">إلى تاريخ</label>
                    <input type="date" name="date_to" class="filter-input" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="filter-btn">تطبيق الفلاتر</button>
                <a href="{{ route('manufacturing.reports.wip') }}" class="reset-btn">إعادة تعيين</a>
            </div>
        </form>
    </div>

    <!-- جدول البيانات -->
    <div class="data-table-container">
        <div class="table-header">
            <h3 class="table-title">تفاصيل القطع العالقة ({{ number_format($stuckItems->count()) }} قطعة)</h3>
        </div>

        @if($stuckItems->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الباركود</th>
                        <th>المرحلة الحالية</th>
                        <th>العامل</th>
                        <th>تاريخ البدء</th>
                        <th>مدة التأخير</th>
                        <th>الوزن (كجم)</th>
                        <th>الهدر (كجم)</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stuckItems as $item)
                        <tr>
                            <td style="font-weight: 600;">{{ $item->barcode }}</td>
                            <td>{{ $item->stage_name }}</td>
                            <td>{{ $item->worker_name ?? 'غير محدد' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->started_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                @php
                                    $hours = $item->hours_stuck;
                                    $badgeClass = $hours < 12 ? 'delay-normal' : ($hours < 24 ? 'delay-warning' : 'delay-critical');
                                @endphp
                                <span class="delay-badge {{ $badgeClass }}">
                                    {{ $hours }} ساعة
                                </span>
                            </td>
                            <td>{{ number_format($item->output_weight, 2) }}</td>
                            <td>{{ number_format($item->waste, 2) }}</td>
                            <td>
                                @if($hours >= 24)
                                    <span class="status-text status-critical">حرجة</span>
                                @elseif($hours >= 12)
                                    <span class="status-text status-warning">تحذير</span>
                                @else
                                    <span class="status-text status-normal">عادية</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <div class="empty-icon">✓</div>
                <h3 style="margin: 0 0 0.5rem 0; color: var(--success-color);">لا توجد قطع عالقة</h3>
                <p style="margin: 0; color: var(--gray-600);">جميع القطع تسير بشكل طبيعي في خط الإنتاج</p>
            </div>
        @endif
    </div>
</div>

@endsection