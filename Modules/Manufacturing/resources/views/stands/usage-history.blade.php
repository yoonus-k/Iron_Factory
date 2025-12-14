@extends('master')

@section('title', __('stands.title.usage_history'))

@section('content')
<style>
    /* استيراد الخطوط */
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap');

    /* Reset and Base */
    * {
        box-sizing: border-box;
    }

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
    .history-container {
        font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 0.75rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        max-width: 1600px;
        margin: 0 auto;
    }

    /* رأس الصفحة */
    .history-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        padding: 1.25rem 1.5rem;
        border-radius: var(--radius-md);
        color: var(--white);
        margin-bottom: 1rem;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }

    .history-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
        pointer-events: none;
    }

    .history-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 0.35rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .history-header p {
        opacity: 0.9;
        margin: 0;
        font-size: 0.9rem;
    }

    /* بطاقة الفلاتر */
    .filters-card {
        background: var(--white);
        padding: 1rem;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        margin-bottom: 1rem;
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
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
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
        width: 100%;
        box-sizing: border-box;
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

    .btn-filter {
        padding: var(--spacing-sm) var(--spacing-lg);
        border-radius: var(--radius-md);
        font-weight: 600;
        cursor: pointer;
        transition: all var(--transition-base);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-family: inherit;
        border: none;
    }

    .btn-filter.primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: var(--white);
        box-shadow: var(--shadow-sm);
    }

    .btn-filter.primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-filter.secondary {
        background: var(--gray-600);
        color: var(--white);
        box-shadow: var(--shadow-sm);
    }

    .btn-filter.secondary:hover {
        background: var(--gray-800);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* بطاقات الإحصائيات */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.875rem;
        margin-bottom: 1rem;
    }

    .stat-card {
        background: var(--white);
        padding: 1rem;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        transition: all var(--transition-base);
        position: relative;
        overflow: hidden;
        min-height: 120px;
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

    .stat-card.blue::before {
        background: linear-gradient(90deg, #3498db, #2980b9);
    }

    .stat-card.green::before {
        background: linear-gradient(90deg, #2ecc71, #27ae60);
    }

    .stat-card.orange::before {
        background: linear-gradient(90deg, #f39c12, #e67e22);
    }

    .stat-card.purple::before {
        background: linear-gradient(90deg, #9b59b6, #8e44ad);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: var(--spacing-md);
    }

    .stat-card.blue .stat-icon {
        background: rgba(52, 152, 219, 0.1);
        color: #3498db;
    }

    .stat-card.green .stat-icon {
        background: rgba(46, 204, 113, 0.1);
        color: #2ecc71;
    }

    .stat-card.orange .stat-icon {
        background: rgba(243, 156, 18, 0.1);
        color: #f39c12;
    }

    .stat-card.purple .stat-icon {
        background: rgba(155, 89, 182, 0.1);
        color: #9b59b6;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: var(--spacing-xs);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
        line-height: 1;
    }

    /* جدول التاريخ */
    .history-table-card {
        background: var(--white);
        border-radius: var(--radius-md);
        padding: 1rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .history-table-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .history-table-card h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0 0 var(--spacing-lg) 0;
        padding-bottom: var(--spacing-md);
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .history-table thead {
        background: var(--gray-50);
    }

    .history-table th {
        padding: 0.625rem 0.5rem;
        text-align: right;
        font-weight: 600;
        font-size: 0.8rem;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border-bottom: 2px solid var(--gray-300);
        white-space: nowrap;
    }

    .history-table td {
        padding: 0.625rem 0.5rem;
        border-bottom: 1px solid var(--gray-200);
        color: var(--gray-800);
        font-size: 0.8rem;
    }

    .history-table tbody tr {
        transition: all var(--transition-base);
    }

    .history-table tbody tr:hover {
        background: rgba(0, 102, 178, 0.05);
    }

    .history-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* تحسين عرض البيانات في الأعمدة */
    .history-table td {
        vertical-align: middle;
    }

    .history-table td svg {
        display: inline-block;
        vertical-align: middle;
        margin-left: 0.25rem;
    }

    .stand-badge {
        display: inline-block;
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.75rem;
    }

    .stand-badge.active {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .stand-badge.inactive {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }

    .status-badge {
        display: inline-block;
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.75rem;
    }

    .status-badge.in_use {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .status-badge.completed {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .status-badge.returned {
        background: rgba(59, 130, 246, 0.1);
        color: var(--info-color);
    }

    .usage-count {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: var(--spacing-xs) var(--spacing-sm);
        background: rgba(59, 130, 246, 0.1);
        color: var(--info-color);
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.75rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--gray-600);
        background: var(--gray-50);
        border-radius: var(--radius-md);
        margin: var(--spacing-md) 0;
    }

    .empty-state svg {
        margin-bottom: 1rem;
        opacity: 0.3;
        color: var(--gray-400);
    }

    .empty-state h3 {
        margin: 0 0 0.5rem 0;
        color: var(--gray-700);
        font-size: 1.25rem;
        font-weight: 600;
    }

    .empty-state p {
        margin: 0;
        font-size: 0.875rem;
        color: var(--gray-500);
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: var(--spacing-xs);
        margin-top: var(--spacing-lg);
        flex-wrap: wrap;
        padding: var(--spacing-md) 0;
    }

    .pagination a,
    .pagination span {
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-sm);
        text-decoration: none;
        color: var(--gray-700);
        background: var(--white);
        border: 1px solid var(--gray-300);
        transition: all var(--transition-base);
        font-size: 0.875rem;
        min-width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .pagination a:hover {
        background: var(--primary-color);
        color: var(--white);
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    .pagination .active {
        background: var(--primary-color);
        color: var(--white);
        border-color: var(--primary-color);
        font-weight: 600;
    }

    .pagination .disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Responsive Table Wrapper */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-top: 1rem;
        border-radius: var(--radius-md);
        box-shadow: inset 0 0 0 1px var(--gray-200);
        background: var(--white);
    }

    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: var(--gray-100);
        border-radius: var(--radius-sm);
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: var(--gray-400);
        border-radius: var(--radius-sm);
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: var(--gray-600);
    }

    /* استجابة الشاشات */
    @media (max-width: 1400px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .filters-card,
        .stats-grid,
        .history-table-card {
            margin-left: 0.75rem;
            margin-right: 0.75rem;
        }
    }

    @media (max-width: 1200px) {
        .history-header {
            padding: 1.25rem;
        }

        .history-header h1 {
            font-size: 1.35rem;
        }

        .filters-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 992px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .filters-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .history-container {
            padding: 0.5rem;
            width: 100%;
            overflow-x: hidden;
        }

        .history-header {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
        }

        .history-header h1 {
            font-size: 1.1rem;
            flex-direction: column;
            text-align: center;
            gap: 0.5rem;
        }

        .history-header h1 svg {
            margin-bottom: 0;
        }

        .history-header p {
            font-size: 0.875rem;
            text-align: center;
        }

        .filters-card,
        .history-table-card {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
        }

        .filters-card h3,
        .history-table-card h3 {
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
        }

        .filters-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .stats-grid {
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
        }

        .stat-card {
            padding: 0.75rem;
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.35rem;
        }

        .stat-label {
            font-size: 0.7rem;
        }

        .filter-actions {
            flex-direction: column;
            gap: 0.5rem;
        }

        .btn-filter {
            width: 100%;
            justify-content: center;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .table-responsive {
            margin-top: 0.5rem;
        }

        .history-table {
            font-size: 0.7rem;
        }

        .history-table th,
        .history-table td {
            padding: 0.35rem 0.25rem;
            font-size: 0.7rem;
        }

        .status-badge,
        .stand-badge,
        .usage-count {
            font-size: 0.6rem;
            padding: 0.125rem 0.3rem;
        }
    }

    @media (max-width: 480px) {
        .history-container {
            padding: 0.375rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .stat-card {
            padding: 0.75rem 0.5rem;
        }

        .stat-value {
            font-size: 1.5rem;
        }

        .stat-label {
            font-size: 0.65rem;
        }

        .history-header {
            padding: 0.75rem;
        }

        .history-header h1 {
            font-size: 1rem;
        }

        .history-header p {
            font-size: 0.8rem;
        }

        .filters-card,
        .history-table-card {
            padding: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .filters-card h3,
        .history-table-card h3 {
            font-size: 0.85rem;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
        }

        .history-table th,
        .history-table td {
            padding: 0.25rem 0.2rem;
            font-size: 0.65rem;
        }
    }

    @media print {
        .filters-card,
        .btn-filter,
        .history-header {
            display: none;
        }
    }
</style>

<div class="history-container">
    <!-- Header -->
    <div class="history-header">
        <h1>
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            {{ __('stands.header.usage_history') }}
        </h1>
        <p>{{ __('stands.stats.total_uses') }} - {{ __('stands.header.usage_history') }}</p>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            تصفية البيانات
        </h3>
        <form method="GET" action="{{ route('manufacturing.stands.usage-history') }}">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">رقم الاستاند</label>
                    <input type="text" name="stand_number" value="{{ request('stand_number') }}"
                           placeholder="مثال: ST-001" class="filter-input">
                </div>

                <div class="filter-group">
                    <label class="filter-label">المستخدم</label>
                    <select name="user_id" class="filter-input">
                        <option value="">الكل</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">{{ __('stands.form.status') }}</label>
                    <select name="status" class="filter-input">
                        <option value="">{{ __('stands.filter.all_statuses') }}</option>
                        <option value="in_use" {{ request('status') == 'in_use' ? 'selected' : '' }}>{{ __('stands.status.in_use') }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('stands.status.completed') }}</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>{{ __('stands.status.returned') }}</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">{{ __('stands.filter.date') }}</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-input">
                </div>

                <div class="filter-group">
                    <label class="filter-label">{{ __('stands.filter.date') }}</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-input">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    بحث
                </button>
                <a href="{{ route('manufacturing.stands.usage-history') }}" class="btn-filter secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="1 4 1 10 7 10"></polyline>
                        <path d="M3.51 9A9 9 0 0 1 5.64 5.64"></path>
                    </svg>
                    إعادة تعيين
                </a>
                <button type="button" onclick="window.print()" class="btn-filter secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    طباعة
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <div class="stat-label">{{ __('stands.stats.total_uses') }}</div>
            <div class="stat-value">{{ $totalUsages }}</div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                </svg>
            </div>
            <div class="stat-label">{{ __('stands.stats.active_stands') }}</div>
            <div class="stat-value">{{ $activeStands }}</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </div>
            <div class="stat-label">{{ __('stands.form.weight') }}</div>
            <div class="stat-value">{{ number_format($totalWeight, 2) }}</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="stat-label">عدد العاملين</div>
            <div class="stat-value">{{ $totalUsers }}</div>
        </div>
    </div>

    <!-- History Table -->
    <div class="history-table-card">
        <h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            {{ __('stands.breadcrumb.stands') }} - {{ __('stands.stats.total_uses') }}
        </h3>

        @if($history->count() > 0)
        <div class="table-responsive">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('stands.form.stand_number') }}</th>
                        <th>{{ __('stands.stats.total_uses') }}</th>
                        <th>المستخدم</th>
                        <th>المسؤول</th>
                        <th>الوردية</th>
                        <th>باركود المادة</th>
                        <th>نوع المادة</th>
                        <th>مقاس السلك</th>
                        <th>الوزن الإجمالي</th>
                        <th>الوزن الصافي</th>
                        <th>نسبة الهدر</th>
                        <th>{{ __('stands.form.status') }}</th>
                        <th>{{ __('stands.form.created_at') }}</th>
                        <th>تاريخ الانتهاء</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($history as $index => $record)
                <tr>
                    <td>{{ $history->firstItem() + $index }}</td>
                    <td>
                        <strong style="color: var(--primary-color);">{{ $record->stand->stand_number }}</strong>
                    </td>
                    <td>
                        <span class="usage-count">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="1 4 1 10 7 10"></polyline>
                                <path d="M3.51 9A9 9 0 0 1 5.64 5.64"></path>
                            </svg>
                            {{ $record->stand->usage_count }} مرة
                        </span>
                    </td>
                    <td>
                        @if($record->user)
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            {{ $record->user->name }}
                        @else
                            <span style="color: var(--gray-500);">غير محدد</span>
                        @endif
                    </td>
                    <td>
                        @if($record->supervisor)
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <strong style="color: #9b59b6;">{{ $record->supervisor->name }}</strong>
                        @else
                            <span style="color: var(--gray-500);">-</span>
                        @endif
                    </td>
                    <td>
                        @if($record->shift)
                            <span style="background: #e3f2fd; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; color: #1976d2;">
                                {{ $record->shift->shift_code }}
                            </span>
                        @else
                            <span style="color: var(--gray-500);">-</span>
                        @endif
                    </td>
                    <td>{{ $record->material_barcode ?? '-' }}</td>
                    <td>{{ $record->material_type ?? '-' }}</td>
                    <td>
                        @if($record->wire_size)
                            {{ $record->wire_size }} مم
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ number_format($record->total_weight, 2) }} كجم</td>
                    <td>{{ number_format($record->net_weight, 2) }} كجم</td>
                    <td>
                        @if($record->waste_percentage > 0)
                            <span style="color: var(--danger-color); font-weight: 600;">
                                {{ number_format($record->waste_percentage, 2) }}%
                            </span>
                        @else
                            <span style="color: var(--success-color);">0%</span>
                        @endif
                    </td>
                    <td>
                        @if($record->status == 'in_use')
                            <span class="status-badge in_use">قيد الاستخدام</span>
                        @elseif($record->status == 'completed')
                            <span class="status-badge completed">مكتمل</span>
                        @else
                            <span class="status-badge returned">مرتجع</span>
                        @endif
                    </td>
                    <td>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        {{ \Carbon\Carbon::parse($record->started_at)->format('Y-m-d H:i') }}
                    </td>
                    <td>
                        @if($record->completed_at)
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                <path d="M16 14h.01"></path>
                                <path d="M8 14h.01"></path>
                                <path d="M16 18h.01"></path>
                                <path d="M8 18h.01"></path>
                            </svg>
                            {{ \Carbon\Carbon::parse($record->completed_at)->format('Y-m-d H:i') }}
                        @else
                            <span style="color: var(--gray-500);">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            {{ $history->appends(request()->query())->links() }}
        </div>
        @else
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
            <h3>لا توجد سجلات</h3>
            <p>لم يتم العثور على أي سجلات استخدام للاستاندات</p>
        </div>
        @endif
    </div>
</div>

@endsection
