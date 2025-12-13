@extends('master')

@section('title', __('shifts-workers.pending_transfers'))

@section('content')

<style>
    :root {
        --primary-color: #0066B2;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #3b82f6;
    }

    .handover-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-header {
        background: linear-gradient(135deg, var(--info-color) 0%, #1e40af 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .page-header h1 {
        font-size: 28px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-section {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-group label {
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }

    .filter-group select {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
    }

    .handover-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }

    .handover-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-color);
    }

    .handover-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f3f4f6;
    }

    .handover-info {
        flex: 1;
    }

    .handover-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-approved {
        background: #dcfce7;
        color: #15803d;
    }

    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .handover-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .handover-meta {
        font-size: 13px;
        color: #6b7280;
        margin-top: 5px;
    }

    .handover-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .detail-group {
        padding: 15px;
        background: #f9fafb;
        border-radius: 8px;
    }

    .detail-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .detail-value {
        font-size: 15px;
        font-weight: 600;
        color: #1f2937;
        word-break: break-all;
    }

    .workers-list {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        max-height: 300px;
        overflow-y: auto;
    }

    .workers-list h4 {
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 12px 0;
        color: #374151;
    }

    .worker-item {
        display: flex;
        align-items: center;
        padding: 8px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .worker-item:last-child {
        margin-bottom: 0;
    }

    .worker-badge {
        display: inline-block;
        background: var(--primary-color);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        margin-right: 8px;
        font-size: 11px;
        font-weight: 600;
        min-width: 40px;
        text-align: center;
    }

    .handover-actions {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-approve {
        background: var(--success-color);
        color: white;
        flex: 1;
    }

    .btn-approve:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-reject {
        background: var(--danger-color);
        color: white;
        flex: 1;
    }

    .btn-reject:hover {
        background: #dc2626;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-view {
        background: var(--primary-color);
        color: white;
        flex: 1;
    }

    .btn-view:hover {
        background: #0052a3;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    .empty-state svg {
        width: 100px;
        height: 100px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state h2 {
        font-size: 20px;
        margin: 0 0 10px 0;
    }

    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .alert-success {
        background: #dcfce7;
        border-left: 4px solid var(--success-color);
        color: #15803d;
    }

    .alert-danger {
        background: #fee2e2;
        border-left: 4px solid var(--danger-color);
        color: #991b1b;
    }

    @media (max-width: 768px) {
        .handover-header {
            flex-direction: column;
        }

        .handover-details {
            grid-template-columns: 1fr;
        }

        .handover-actions {
            flex-direction: column;
        }

        .filter-section {
            flex-direction: column;
        }

        .filter-group {
            width: 100%;
        }

        .filter-group select {
            width: 100%;
        }
    }
</style>

<div class="handover-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="feather icon-inbox"></i>
            طلبات نقل الوردية المعلقة
        </h1>
        <p style="margin: 10px 0 0 0;">عرض ومعالجة طلبات نقل الوردية التي تنتظر الموافقة</p>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="feather icon-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="feather icon-alert-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-group">
            <label for="statusFilter">الحالة:</label>
            <select id="statusFilter" onchange="filterHandovers()">
                <option value="">الكل</option>
                <option value="pending">معلق</option>
                <option value="approved">موافق عليه</option>
                <option value="rejected">مرفوض</option>
            </select>
        </div>
    </div>

    <!-- Handovers List -->
    <div id="handoversList">
        @if($handovers->count() > 0)
            @foreach($handovers as $handover)
                <div class="handover-card" data-status="{{ $handover->acknowledged_at ? 'approved' : ($handover->supervisor_approved === false ? 'rejected' : 'pending') }}">
                    <div class="handover-header">
                        <div class="handover-info">
                            <div class="handover-title">
                                الوردية: <strong>{{ $handover->shiftAssignment?->shift_code ?? 'N/A' }}</strong>
                            </div>
                            <div class="handover-status {{ $handover->acknowledged_at ? 'status-approved' : ($handover->supervisor_approved === false ? 'status-rejected' : 'status-pending') }}">
                                @if($handover->acknowledged_at)
                                    ✓ موافق عليه
                                @elseif($handover->supervisor_approved === false)
                                    ✗ مرفوض
                                @else
                                    ⏱ معلق
                                @endif
                            </div>
                            <div class="handover-meta">
                                <i class="feather icon-clock"></i>
                                {{ $handover->handover_time->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    <div class="handover-details">
                        <div class="detail-group">
                            <div class="detail-label">من المسؤول:</div>
                            <div class="detail-value">{{ $handover->fromUser?->name ?? 'N/A' }}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">إلى المسؤول:</div>
                            <div class="detail-value">{{ $handover->toUser?->name ?? 'N/A' }}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">المرحلة:</div>
                            <div class="detail-value">المرحلة {{ $handover->stage_number ?? 'N/A' }}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">عدد العمال:</div>
                            <div class="detail-value">{{ count($handover->handover_items ?? []) }} عامل</div>
                        </div>
                    </div>

                    @if($handover->handover_items && count($handover->handover_items) > 0)
                        <div class="workers-list">
                            <h4>قائمة العمال المنقولين:</h4>
                            @foreach($handover->handover_items as $item)
                                <div class="worker-item">
                                    <span class="worker-badge">#{{ $item['worker_id'] }}</span>
                                    <span>{{ $item['worker_name'] ?? 'عامل' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($handover->notes)
                        <div class="detail-group">
                            <div class="detail-label">ملاحظات:</div>
                            <div class="detail-value">{{ $handover->notes }}</div>
                        </div>
                    @endif

                    @if(!$handover->acknowledged_at && $handover->supervisor_approved !== false && auth()->user()->id === $handover->to_user_id)
                        <div class="handover-actions">
                            <form method="POST" action="{{ route('manufacturing.shift-handovers.acknowledge', $handover->id) }}" style="flex: 1;">
                                @csrf
                                <button type="submit" class="btn btn-approve" onclick="return confirm('هل أنت متأكد من الموافقة على نقل هذه الوردية؟')">
                                    <i class="feather icon-check"></i>
                                    الموافقة على النقل
                                </button>
                            </form>

                            <form method="POST" action="{{ route('manufacturing.shift-handovers.reject', $handover->id) }}" style="flex: 1;">
                                @csrf
                                <button type="submit" class="btn btn-reject" onclick="return confirm('هل تريد رفض نقل هذه الوردية؟')">
                                    <i class="feather icon-x"></i>
                                    رفض النقل
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <h2>لا توجد طلبات نقل معلقة</h2>
                <p>جميع طلبات النقل تمت معالجتها بنجاح</p>
            </div>
        @endif
    </div>
</div>

<script>
    function filterHandovers() {
        const filter = document.getElementById('statusFilter').value;
        const cards = document.querySelectorAll('.handover-card');

        cards.forEach(card => {
            if (filter === '' || card.getAttribute('data-status') === filter) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>

@endsection
