@extends('master')

@section('title', __('shifts-workers.transfer_history'))

@section('content')

<style>
    :root {
        --primary-color: #0066B2;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #3b82f6;
    }

    .history-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    .history-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #0052a3 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .history-header h1 {
        margin: 0 0 10px 0;
        font-size: 28px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .history-header p {
        margin: 5px 0;
        opacity: 0.95;
    }

    .transfer-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        border-left: 5px solid var(--primary-color);
    }

    .transfer-card.completed {
        border-left-color: var(--success-color);
    }

    .transfer-card.pending {
        border-left-color: var(--warning-color);
    }

    .transfer-header-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f3f4f6;
    }

    .info-block {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-weight: 600;
        color: #6b7280;
        font-size: 12px;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .info-value {
        font-size: 16px;
        color: #1f2937;
        font-weight: 500;
    }

    .supervisor-badge {
        display: inline-block;
        background: #dbeafe;
        color: #0c4a6e;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 14px;
    }

    .supervisor-badge.from {
        background: #fee2e2;
        color: #7f1d1d;
    }

    .supervisor-badge.to {
        background: #dcfce7;
        color: #15803d;
    }

    .workers-comparison {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin: 20px 0;
    }

    .workers-section {
        background: #f9fafb;
        padding: 15px;
        border-radius: 8px;
    }

    .workers-section h4 {
        margin: 0 0 12px 0;
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .workers-section.old h4 {
        color: var(--warning-color);
    }

    .workers-section.new h4 {
        color: var(--success-color);
    }

    .worker-type-group {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e5e7eb;
    }

    .worker-type-group:last-child {
        margin-bottom: 0;
        border-bottom: none;
    }

    .worker-type-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 8px;
        text-transform: uppercase;
        padding: 4px 8px;
        background: white;
        border-radius: 4px;
    }

    .worker-badge {
        display: inline-block;
        background: white;
        border: 1px solid #d1d5db;
        padding: 6px 10px;
        border-radius: 6px;
        margin: 4px;
        font-size: 12px;
        color: #1f2937;
    }

    .worker-badge.individual {
        border-left: 3px solid var(--info-color);
    }

    .worker-badge.team {
        border-left: 3px solid var(--success-color);
        background: #f0fdf4;
    }

    .team-members {
        font-size: 11px;
        color: #6b7280;
        margin-top: 4px;
        padding-left: 10px;
        border-left: 2px solid #e5e7eb;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.completed {
        background: #dcfce7;
        color: #15803d;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.approved {
        background: #dbeafe;
        color: #0c4a6e;
    }

    .status-badge.rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .transfer-footer {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        padding-top: 15px;
        border-top: 1px solid #f3f4f6;
    }

    .footer-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .footer-label {
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        font-weight: 600;
    }

    .footer-value {
        font-size: 14px;
        color: #1f2937;
    }

    .notes-section {
        background: #f3f4f6;
        padding: 12px;
        border-radius: 6px;
        margin-top: 15px;
        border-left: 3px solid var(--primary-color);
    }

    .notes-section.approval {
        border-left-color: var(--success-color);
        background: #f0fdf4;
    }

    .notes-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .notes-text {
        font-size: 13px;
        color: #1f2937;
        line-height: 1.5;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }

    .empty-state svg {
        width: 80px;
        height: 80px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 30px;
    }

    .back-btn {
        display: inline-block;
        padding: 10px 20px;
        background: var(--primary-color);
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        margin-bottom: 20px;
    }

    .back-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 102, 178, 0.3);
    }

    @media (max-width: 768px) {
        .workers-comparison {
            grid-template-columns: 1fr;
        }

        .transfer-header-info {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="history-container">
    <!-- Header -->
    <div class="history-header">
        <h1>
            <i class="feather icon-repeat"></i>
            سجل نقل الوردية
        </h1>
        <p>
            <strong>الوردية:</strong> {{ $shift->shift_code }} |
            <strong>التاريخ:</strong> {{ $shift->shift_date->format('Y-m-d') }} |
            <strong>المسؤول الحالي:</strong> {{ $shift->supervisor?->name ?? 'غير محدد' }}
        </p>
    </div>

    <!-- Back Button -->
    <a href="{{ route('manufacturing.shifts-workers.show', $shift->id) }}" class="back-btn">
        <i class="feather icon-arrow-right" style="margin-right: 5px;"></i>
        العودة للوردية
    </a>

    <!-- Transfer History Records -->
    @if($transfers->count() > 0)
        @foreach($transfers as $transfer)
            <div class="transfer-card {{ $transfer->status }}">
                <!-- Transfer Header Info -->
                <div class="transfer-header-info">
                    <div class="info-block">
                        <span class="info-label">من المسؤول</span>
                        <span class="supervisor-badge from">
                            {{ $transfer->fromSupervisor?->name ?? 'غير محدد' }}
                        </span>
                    </div>

                    <div class="info-block">
                        <span class="info-label">إلى المسؤول</span>
                        <span class="supervisor-badge to">
                            {{ $transfer->toSupervisor?->name ?? 'غير محدد' }}
                        </span>
                    </div>

                    <div class="info-block">
                        <span class="info-label">نوع النقل</span>
                        <span style="font-size: 14px; color: #1f2937; font-weight: 500;">
                            {{ $transfer->getTransferTypeLabel() }}
                        </span>
                    </div>

                    <div class="info-block">
                        <span class="info-label">الحالة</span>
                        <span class="status-badge {{ $transfer->status }}">
                            {{ $transfer->getStatusLabel() }}
                        </span>
                    </div>
                </div>

                <!-- Workers Comparison -->
                <div class="workers-comparison">
                    <!-- Old Workers -->
                    <div class="workers-section old">
                        <h4>
                            <i class="feather icon-arrow-left"></i>
                            العمال القدامى ({{ $transfer->getOldWorkerCount() }})
                        </h4>

                        @if(!empty($transfer->old_data['individual_worker_ids']))
                            <div class="worker-type-group">
                                <div class="worker-type-label">
                                    <i class="feather icon-user" style="font-size: 12px;"></i>
                                    عمال أفراد
                                </div>
                                <div>
                                    @foreach($transfer->old_data['individual_worker_ids'] as $workerId)
                                        @php
                                            $worker = \App\Models\Worker::find($workerId);
                                        @endphp
                                        @if($worker)
                                            <span class="worker-badge individual">
                                                {{ $worker->name }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!empty($transfer->old_data['team_groups']))
                            <div class="worker-type-group">
                                <div class="worker-type-label">
                                    <i class="feather icon-users" style="font-size: 12px;"></i>
                                    مجموعات عمال
                                </div>
                                @foreach($transfer->old_data['team_groups'] as $group)
                                    <div style="margin-bottom: 10px;">
                                        <span class="worker-badge team">
                                            👥 {{ $group['team_name'] ?? 'مجموعة' }}
                                        </span>
                                        <div class="team-members">
                                            {{ count($group['worker_ids'] ?? []) }} عامل في المجموعة
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(empty($transfer->old_data['individual_worker_ids']) && empty($transfer->old_data['team_groups']))
                            <p style="color: #9ca3af; font-size: 12px;">لا يوجد عمال قدامى</p>
                        @endif
                    </div>

                    <!-- New Workers -->
                    <div class="workers-section new">
                        <h4>
                            <i class="feather icon-arrow-right"></i>
                            العمال الجدد ({{ $transfer->getNewWorkerCount() }})
                        </h4>

                        @if(!empty($transfer->new_data['individual_worker_ids']))
                            <div class="worker-type-group">
                                <div class="worker-type-label">
                                    <i class="feather icon-user" style="font-size: 12px;"></i>
                                    عمال أفراد
                                </div>
                                <div>
                                    @foreach($transfer->new_data['individual_worker_ids'] as $workerId)
                                        @php
                                            $worker = \App\Models\Worker::find($workerId);
                                        @endphp
                                        @if($worker)
                                            <span class="worker-badge individual">
                                                {{ $worker->name }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!empty($transfer->new_data['team_groups']))
                            <div class="worker-type-group">
                                <div class="worker-type-label">
                                    <i class="feather icon-users" style="font-size: 12px;"></i>
                                    مجموعات عمال
                                </div>
                                @foreach($transfer->new_data['team_groups'] as $group)
                                    <div style="margin-bottom: 10px;">
                                        <span class="worker-badge team">
                                            👥 {{ $group['team_name'] ?? 'مجموعة' }}
                                        </span>
                                        <div class="team-members">
                                            {{ count($group['worker_ids'] ?? []) }} عامل في المجموعة
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(empty($transfer->new_data['individual_worker_ids']) && empty($transfer->new_data['team_groups']))
                            <p style="color: #9ca3af; font-size: 12px;">لا يوجد عمال جدد</p>
                        @endif
                    </div>
                </div>

                <!-- Transfer Notes -->
                @if($transfer->transfer_notes)
                    <div class="notes-section">
                        <div class="notes-label">📝 ملاحظات النقل</div>
                        <div class="notes-text">{{ $transfer->transfer_notes }}</div>
                    </div>
                @endif

                <!-- Approval Notes (إذا وجدت) -->
                @if($transfer->approval_notes)
                    <div class="notes-section approval">
                        <div class="notes-label">✓ ملاحظات الموافقة</div>
                        <div class="notes-text">{{ $transfer->approval_notes }}</div>
                    </div>
                @endif

                <!-- Transfer Footer -->
                <div class="transfer-footer">
                    <div class="footer-item">
                        <span class="footer-label">نقل بواسطة</span>
                        <span class="footer-value">{{ $transfer->transferredBy?->name ?? 'غير معروف' }}</span>
                    </div>

                    <div class="footer-item">
                        <span class="footer-label">تاريخ النقل</span>
                        <span class="footer-value">{{ $transfer->created_at->format('Y-m-d H:i') }}</span>
                    </div>

                    @if($transfer->approved_by)
                        <div class="footer-item">
                            <span class="footer-label">موافق بواسطة</span>
                            <span class="footer-value">{{ $transfer->approvedBy?->name ?? 'غير معروف' }}</span>
                        </div>

                        <div class="footer-item">
                            <span class="footer-label">تاريخ الموافقة</span>
                            <span class="footer-value">{{ $transfer->approved_at?->format('Y-m-d H:i') ?? '-' }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        @if($transfers->hasPages())
            <div class="pagination-wrapper">
                {{ $transfers->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
            </svg>
            <p>لا توجد عمليات نقل حتى الآن لهذه الوردية</p>
        </div>
    @endif
</div>

@endsection
