@extends('master')

@section('title', 'تاريخ استخدام الاستاندات')

@section('content')
<style>
    .history-container {
        max-width: 1600px;
        margin: 20px auto;
        padding: 0 15px;
    }

    .history-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .history-header h1 {
        margin: 0 0 10px 0;
        font-size: 32px;
    }

    .history-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 16px;
    }

    /* Filters Card */
    .filters-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .filters-card h3 {
        margin: 0 0 20px 0;
        font-size: 18px;
        color: #2c3e50;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-group label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #495057;
        font-size: 14px;
    }

    .filter-group input,
    .filter-group select {
        padding: 10px 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .btn-filter {
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-filter.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-filter.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-filter.secondary {
        background: #e9ecef;
        color: #495057;
    }

    .btn-filter.secondary:hover {
        background: #dee2e6;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .stat-card.blue::before { background: linear-gradient(90deg, #3498db, #2980b9); }
    .stat-card.green::before { background: linear-gradient(90deg, #2ecc71, #27ae60); }
    .stat-card.orange::before { background: linear-gradient(90deg, #f39c12, #e67e22); }
    .stat-card.purple::before { background: linear-gradient(90deg, #9b59b6, #8e44ad); }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }

    .stat-card.blue .stat-icon { background: rgba(52, 152, 219, 0.1); color: #3498db; }
    .stat-card.green .stat-icon { background: rgba(46, 204, 113, 0.1); color: #2ecc71; }
    .stat-card.orange .stat-icon { background: rgba(243, 156, 18, 0.1); color: #f39c12; }
    .stat-card.purple .stat-icon { background: rgba(155, 89, 182, 0.1); color: #9b59b6; }

    .stat-label {
        font-size: 14px;
        color: #7f8c8d;
        margin-bottom: 5px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
    }

    /* Table Styles */
    .history-table-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow-x: auto;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .history-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .history-table th {
        padding: 15px;
        text-align: right;
        font-weight: 600;
        font-size: 14px;
        white-space: nowrap;
    }

    .history-table th:first-child {
        border-radius: 10px 0 0 0;
    }

    .history-table th:last-child {
        border-radius: 0 10px 0 0;
    }

    .history-table td {
        padding: 15px;
        border-bottom: 1px solid #e9ecef;
        color: #495057;
        font-size: 14px;
    }

    .history-table tbody tr {
        transition: all 0.3s;
    }

    .history-table tbody tr:hover {
        background: #f8f9fa;
        transform: scale(1.01);
    }

    .stand-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 13px;
    }

    .stand-badge.active {
        background: #d4edda;
        color: #155724;
    }

    .stand-badge.inactive {
        background: #f8d7da;
        color: #721c24;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 15px;
        font-weight: 600;
        font-size: 12px;
    }

    .status-badge.in_use {
        background: #fff3cd;
        color: #856404;
    }

    .status-badge.completed {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.returned {
        background: #d1ecf1;
        color: #0c5460;
    }

    .usage-count {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        background: #e3f2fd;
        color: #1976d2;
        border-radius: 15px;
        font-weight: 600;
        font-size: 13px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #95a5a6;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state h3 {
        margin: 0 0 10px 0;
        color: #7f8c8d;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-top: 30px;
    }

    .pagination a,
    .pagination span {
        padding: 8px 15px;
        border-radius: 8px;
        text-decoration: none;
        color: #495057;
        background: white;
        border: 2px solid #e9ecef;
        transition: all 0.3s;
    }

    .pagination a:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .pagination .active {
        background: #667eea;
        color: white;
        border-color: #667eea;
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
        <h1>📊 تاريخ استخدام الاستاندات</h1>
        <p>عرض تفصيلي لجميع عمليات استخدام الاستاندات في المصنع</p>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <h3><i class="fas fa-filter"></i> تصفية البيانات</h3>
        <form method="GET" action="{{ route('manufacturing.stands.usage-history') }}">
            <div class="filters-grid">
                <div class="filter-group">
                    <label>رقم الاستاند</label>
                    <input type="text" name="stand_number" value="{{ request('stand_number') }}" 
                           placeholder="مثال: ST-001" class="form-control">
                </div>

                <div class="filter-group">
                    <label>المستخدم</label>
                    <select name="user_id" class="form-control">
                        <option value="">الكل</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>الحالة</label>
                    <select name="status" class="form-control">
                        <option value="">الكل</option>
                        <option value="in_use" {{ request('status') == 'in_use' ? 'selected' : '' }}>قيد الاستخدام</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>مرتجع</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>من تاريخ</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>

                <div class="filter-group">
                    <label>إلى تاريخ</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter primary">
                    <i class="fas fa-search"></i> بحث
                </button>
                <a href="{{ route('manufacturing.stands.usage-history') }}" class="btn-filter secondary">
                    <i class="fas fa-redo"></i> إعادة تعيين
                </a>
                <button type="button" onclick="window.print()" class="btn-filter secondary">
                    <i class="fas fa-print"></i> طباعة
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">
                <i class="fas fa-database"></i>
            </div>
            <div class="stat-label">إجمالي الاستخدامات</div>
            <div class="stat-value">{{ $totalUsages }}</div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-label">عدد الاستاندات المستخدمة</div>
            <div class="stat-value">{{ $activeStands }}</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-icon">
                <i class="fas fa-weight"></i>
            </div>
            <div class="stat-label">إجمالي الوزن (كجم)</div>
            <div class="stat-value">{{ number_format($totalWeight, 2) }}</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-label">عدد العاملين</div>
            <div class="stat-value">{{ $totalUsers }}</div>
        </div>
    </div>

    <!-- History Table -->
    <div class="history-table-card">
        <h3><i class="fas fa-history"></i> سجل الاستخدامات</h3>
        
        @if($history->count() > 0)
        <table class="history-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>رقم الاستاند</th>
                    <th>عدد الاستخدامات</th>
                    <th>المستخدم</th>
                    <th>باركود المادة</th>
                    <th>نوع المادة</th>
                    <th>مقاس السلك</th>
                    <th>الوزن الإجمالي</th>
                    <th>الوزن الصافي</th>
                    <th>نسبة الهدر</th>
                    <th>الحالة</th>
                    <th>تاريخ البدء</th>
                    <th>تاريخ الانتهاء</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $index => $record)
                <tr>
                    <td>{{ $history->firstItem() + $index }}</td>
                    <td>
                        <strong style="color: #667eea;">{{ $record->stand->stand_number }}</strong>
                    </td>
                    <td>
                        <span class="usage-count">
                            <i class="fas fa-redo"></i>
                            {{ $record->stand->usage_count }} مرة
                        </span>
                    </td>
                    <td>
                        @if($record->user)
                            <i class="fas fa-user"></i> {{ $record->user->name }}
                        @else
                            <span style="color: #95a5a6;">غير محدد</span>
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
                            <span style="color: #e74c3c; font-weight: 600;">
                                {{ number_format($record->waste_percentage, 2) }}%
                            </span>
                        @else
                            <span style="color: #2ecc71;">0%</span>
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
                        <i class="far fa-calendar"></i>
                        {{ \Carbon\Carbon::parse($record->started_at)->format('Y-m-d H:i') }}
                    </td>
                    <td>
                        @if($record->completed_at)
                            <i class="far fa-calendar-check"></i>
                            {{ \Carbon\Carbon::parse($record->completed_at)->format('Y-m-d H:i') }}
                        @else
                            <span style="color: #95a5a6;">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            {{ $history->appends(request()->query())->links() }}
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>لا توجد سجلات</h3>
            <p>لم يتم العثور على أي سجلات استخدام للاستاندات</p>
        </div>
        @endif
    </div>
</div>

@endsection
