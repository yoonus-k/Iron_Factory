@extends('master')

@section('title', 'لوحة التحكم - المرحلة ' . $stageNumber)

@section('content')
<div class="stage-worker-dashboard-wrapper" data-stage="{{ $stageNumber }}">
    <!-- Page Title Section -->
    <div class="page-title-section">
        <div class="title-content">
            <h1>
                <i class="fas fa-hard-hat"></i>
                لوحة تحكم المرحلة {{ $stageNumber }}
            </h1>
            <p class="subtitle">
                <i class="fas fa-user"></i> {{ auth()->user()->name }}
                <span class="separator">•</span>
                <i class="fas fa-clock"></i> آخر تحديث: <span id="last-update-time">الآن</span>
            </p>
        </div>
        <div class="title-actions">
            <a href="{{ route('manufacturing.stage' . $stageNumber . '.index') }}" class="stage-link-button">
                <i class="fas fa-industry"></i>
                <span>الانتقال للمرحلة {{ $stageNumber }}</span>
            </a>
            <button type="button" class="refresh-button" id="refresh-btn">
                <i class="fas fa-sync-alt"></i>
                <span>تحديث</span>
            </button>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="statistics-grid">
        <div class="stat-card card-warning">
            <div class="stat-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value" id="stat-pending">{{ $stats['pending_confirmations'] }}</div>
                <div class="stat-label">تأكيدات منتظرة</div>
            </div>
            <div class="stat-footer">
                <span class="stat-badge badge-warning">بحاجة إجراء</span>
            </div>
        </div>

        <div class="stat-card card-success">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value" id="stat-confirmed">{{ $stats['confirmed_today'] }}</div>
                <div class="stat-label">تم التأكيد اليوم</div>
            </div>
            <div class="stat-footer">
                <span class="stat-badge badge-success">مكتمل</span>
            </div>
        </div>

        <div class="stat-card card-danger">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value" id="stat-rejected">{{ $stats['rejected_today'] }}</div>
                <div class="stat-label">تم الرفض اليوم</div>
            </div>
            <div class="stat-footer">
                <span class="stat-badge badge-danger">مرفوض</span>
            </div>
        </div>

        <div class="stat-card card-info">
            <div class="stat-icon">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value" id="stat-weekly">{{ $stats['total_this_week'] }}</div>
                <div class="stat-label">إجمالي الأسبوع</div>
            </div>
            <div class="stat-footer">
                <span class="stat-badge badge-info">نشاط</span>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="content-section">
        <!-- Pending Confirmations -->
        <div class="confirmations-container">
            <div class="section-card">
                <div class="section-header">
                    <div class="header-title">
                        <i class="fas fa-clipboard-check"></i>
                        <h3>التأكيدات المنتظرة</h3>
                    </div>
                    <div class="header-badge">
                        <span class="count-badge" id="pending-badge">{{ $pendingConfirmations->count() }}</span>
                    </div>
                </div>
                <div class="section-body" id="confirmations-list">
                    @forelse($pendingConfirmations as $confirmation)
                        @include('stage-worker.partials.confirmation-item', ['confirmation' => $confirmation])
                    @empty
                        <div class="empty-state" id="no-confirmations">
                            <div class="empty-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <h4>لا توجد تأكيدات منتظرة</h4>
                            <p>جميع التأكيدات تم معالجتها بنجاح</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Notifications Sidebar -->
        <div class="notifications-container">
            <div class="section-card">
                <div class="section-header">
                    <div class="header-title">
                        <i class="fas fa-bell"></i>
                        <h3>الإشعارات</h3>
                    </div>
                    <div class="header-badge">
                        <span class="count-badge badge-danger" id="notifications-badge">{{ $notifications->count() }}</span>
                    </div>
                </div>
                <div class="section-body notifications-body" id="notifications-list">
                    @forelse($notifications as $notification)
                        <div class="notification-card notification-{{ $notification['type'] }}">
                            <div class="notification-icon">
                                <i class="fas fa-{{ $notification['type'] === 'warning' ? 'exclamation-triangle' : ($notification['type'] === 'success' ? 'check-circle' : 'times-circle') }}"></i>
                            </div>
                            <div class="notification-content">
                                <h5>{{ $notification['title'] }}</h5>
                                <p>{{ $notification['message'] }}</p>
                                <div class="notification-meta">
                                    <span class="notification-time">
                                        <i class="fas fa-clock"></i> {{ $notification['time'] }}
                                    </span>
                                    <a href="{{ $notification['url'] }}" class="notification-link">
                                        عرض التفاصيل <i class="fas fa-arrow-left"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state empty-state-small" id="no-notifications">
                            <div class="empty-icon">
                                <i class="fas fa-bell-slash"></i>
                            </div>
                            <p>لا توجد إشعارات جديدة</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Confirm Modal -->
<div class="modal fade" id="quickConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle"></i> تأكيد الاستلام
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من استلام هذه المواد؟</p>
                <div class="mb-3">
                    <label class="form-label">ملاحظات (اختياري)</label>
                    <textarea class="form-control" id="confirm-notes" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success" id="confirm-submit">
                    <i class="fas fa-check"></i> تأكيد
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Reject Modal -->
<div class="modal fade" id="quickRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle"></i> رفض الاستلام
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>الرجاء توضيح سبب الرفض:</p>
                <div class="mb-3">
                    <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="reject-reason" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="reject-submit">
                    <i class="fas fa-times"></i> رفض
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Main Wrapper */
    .stage-worker-dashboard-wrapper {
        padding: 20px;
        background-color: #f5f5f5;
        min-height: 100vh;
    }

    /* Page Title Section */
    .page-title-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .title-content h1 {
        font-size: 26px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .title-content h1 i {
        color: #007bff;
        font-size: 24px;
    }

    .title-content .subtitle {
        color: #666;
        font-size: 14px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .separator {
        color: #ddd;
        margin: 0 5px;
    }

    /* Title Actions */
    .title-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Stage Link Button */
    .stage-link-button {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(40,167,69,0.3);
        text-decoration: none;
    }

    .stage-link-button:hover {
        background: linear-gradient(135deg, #1e7e34 0%, #155724 100%);
        box-shadow: 0 4px 12px rgba(40,167,69,0.4);
        transform: translateY(-2px);
        color: white;
    }

    /* Refresh Button */
    .refresh-button {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,123,255,0.3);
    }

    .refresh-button:hover {
        background: linear-gradient(135deg, #0056b3 0%, #003d82 100%);
        box-shadow: 0 4px 12px rgba(0,123,255,0.4);
        transform: translateY(-2px);
    }

    .refresh-button i {
        transition: transform 0.5s ease;
    }

    .refresh-button.spinning i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Statistics Grid */
    .statistics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    @media (max-width: 1200px) {
        .statistics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .statistics-grid {
            grid-template-columns: 1fr;
        }

        .page-title-section {
            flex-direction: column;
            gap: 15px;
        }

        .title-actions {
            width: 100%;
            flex-direction: column;
        }

        .stage-link-button,
        .refresh-button {
            width: 100%;
            justify-content: center;
        }
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid #333;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(0,0,0,0.03);
        border-radius: 50%;
        transform: translate(30px, -30px);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }

    .stat-card.card-warning { border-left-color: #ffc107; }
    .stat-card.card-success { border-left-color: #28a745; }
    .stat-card.card-danger { border-left-color: #dc3545; }
    .stat-card.card-info { border-left-color: #17a2b8; }

    .stat-icon {
        font-size: 36px;
        margin-bottom: 15px;
        opacity: 0.8;
    }

    .stat-card.card-warning .stat-icon { color: #ffc107; }
    .stat-card.card-success .stat-icon { color: #28a745; }
    .stat-card.card-danger .stat-icon { color: #dc3545; }
    .stat-card.card-info .stat-icon { color: #17a2b8; }

    .stat-content {
        margin-bottom: 15px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #1a1a1a;
        line-height: 1;
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 14px;
        color: #666;
        font-weight: 500;
    }

    .stat-footer {
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }

    .stat-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-warning { background: #fff3cd; color: #856404; }
    .badge-success { background: #d4edda; color: #155724; }
    .badge-danger { background: #f8d7da; color: #721c24; }
    .badge-info { background: #d1ecf1; color: #0c5460; }

    /* Content Section */
    .content-section {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 20px;
    }

    @media (max-width: 1200px) {
        .content-section {
            grid-template-columns: 1fr;
        }
    }

    /* Section Card */
    .section-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
    }

    .header-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-title h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .header-title i {
        font-size: 20px;
    }

    .count-badge {
        background: rgba(255,255,255,0.25);
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        backdrop-filter: blur(10px);
    }

    .count-badge.badge-danger {
        background: #dc3545;
    }

    .section-body {
        padding: 20px;
        max-height: 600px;
        overflow-y: auto;
    }

    .section-body::-webkit-scrollbar {
        width: 8px;
    }

    .section-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .section-body::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .section-body::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Confirmation Card */
    .confirmation-card {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 18px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        border-left: 4px solid #007bff;
    }

    .confirmation-card:hover {
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateX(-3px);
    }

    /* Notification Card */
    .notifications-body {
        padding: 15px !important;
    }

    .notification-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 12px;
        display: flex;
        gap: 12px;
        transition: all 0.3s ease;
        border-left: 4px solid #333;
    }

    .notification-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateX(-3px);
    }

    .notification-card.notification-warning { border-left-color: #ffc107; background: #fffbf0; }
    .notification-card.notification-success { border-left-color: #28a745; background: #f0fff4; }
    .notification-card.notification-danger { border-left-color: #dc3545; background: #fff5f5; }

    .notification-icon {
        font-size: 24px;
        opacity: 0.7;
    }

    .notification-warning .notification-icon { color: #ffc107; }
    .notification-success .notification-icon { color: #28a745; }
    .notification-danger .notification-icon { color: #dc3545; }

    .notification-content {
        flex: 1;
    }

    .notification-content h5 {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 5px;
    }

    .notification-content p {
        font-size: 13px;
        color: #666;
        margin-bottom: 10px;
        line-height: 1.5;
    }

    .notification-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
    }

    .notification-time {
        color: #999;
    }

    .notification-link {
        color: #007bff;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .notification-link:hover {
        color: #0056b3;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state-small {
        padding: 40px 20px;
    }

    .empty-icon {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }

    .empty-state h4 {
        font-size: 18px;
        color: #666;
        margin-bottom: 10px;
    }

    .empty-state p {
        font-size: 14px;
        color: #999;
        margin: 0;
    }

    /* Animations */
    .new-item {
        animation: slideInRight 0.5s ease-out;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Modal Improvements */
    .modal-content {
        border: none;
        border-radius: 8px;
        overflow: hidden;
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-body {
        padding: 25px;
    }

    .modal-footer {
        border-top: 1px solid #f0f0f0;
        padding: 15px 25px;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    console.log('=== Stage Worker Dashboard Initialized ===');
    
    let currentConfirmationId = null;
    let lastCheckTime = new Date().toISOString();
    let autoRefreshInterval;
    
    // تهيئة Bootstrap Modals
    let confirmModal, rejectModal;
    
    // التحقق من توفر Bootstrap
    if (typeof bootstrap !== 'undefined') {
        console.log('Bootstrap 5 detected, initializing modals...');
        try {
            const confirmModalEl = document.getElementById('quickConfirmModal');
            const rejectModalEl = document.getElementById('quickRejectModal');
            
            if (confirmModalEl) {
                confirmModal = new bootstrap.Modal(confirmModalEl);
                console.log('Confirm modal initialized');
            } else {
                console.error('Confirm modal element not found!');
            }
            
            if (rejectModalEl) {
                rejectModal = new bootstrap.Modal(rejectModalEl);
                console.log('Reject modal initialized');
            } else {
                console.error('Reject modal element not found!');
            }
            
            console.log('Bootstrap Modals initialized successfully');
        } catch (error) {
            console.error('Error initializing Bootstrap Modals:', error);
        }
    } else {
        console.warn('Bootstrap not found, will use jQuery modal fallback');
    }
    
    // Log all confirmation cards on page load
    console.log('Confirmation cards found:', $('.confirmation-card').length);
    console.log('Quick confirm buttons found:', $('.quick-confirm').length);
    console.log('Quick reject buttons found:', $('.quick-reject').length);

    // تحديث تلقائي كل دقيقة
    function startAutoRefresh() {
        autoRefreshInterval = setInterval(function() {
            fetchUpdates();
        }, 60000); // 60 ثانية
    }

    // زر التحديث اليدوي
    $('#refresh-btn').on('click', function() {
        console.log('=== Refresh Button Clicked ===');
        const $btn = $(this);
        $btn.addClass('spinning');
        $btn.prop('disabled', true);
        
        fetchUpdates()
            .done(function() {
                console.log('Refresh completed successfully');
                showNotification('تم تحديث البيانات', 'success');
            })
            .fail(function(xhr) {
                console.error('Refresh failed:', xhr);
                showNotification('فشل التحديث', 'error');
            })
            .always(function() {
                $btn.removeClass('spinning');
                $btn.prop('disabled', false);
            });
    });

    // جلب التحديثات
    function fetchUpdates() {
        return $.ajax({
            url: '{{ route("stage-worker.dashboard.updates") }}',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                last_check: lastCheckTime
            },
            success: function(response) {
                if (response.success) {
                    // تحديث الإحصائيات
                    updateStats(response.stats);

                    // تحديث التأكيدات
                    if (response.new_confirmations.length > 0) {
                        updateConfirmations(response.new_confirmations);
                    }

                    // تحديث الإشعارات
                    if (response.new_notifications.length > 0) {
                        updateNotifications(response.new_notifications);
                    }

                    // تحديث وقت آخر تحديث
                    lastCheckTime = response.timestamp;
                    $('#last-update-time').text('الآن');

                    // إظهار رسالة إذا كان هناك تحديثات
                    if (response.has_updates) {
                        showNotification('تم استلام تحديثات جديدة', 'success');
                    }
                }
            },
            error: function(xhr) {
                console.error('فشل جلب التحديثات:', xhr);
            }
        });
    }

    // تحديث الإحصائيات
    function updateStats(stats) {
        $('#stat-pending').text(stats.pending_confirmations);
        $('#stat-confirmed').text(stats.confirmed_today);
        $('#stat-rejected').text(stats.rejected_today);
        $('#stat-weekly').text(stats.total_this_week);
        $('#pending-badge').text(stats.pending_confirmations);
    }

    // تحديث التأكيدات
    function updateConfirmations(confirmations) {
        const $list = $('#confirmations-list');
        $('#no-confirmations').remove();
        
        confirmations.forEach(function(confirmation) {
            // تحقق إذا كان التأكيد موجود بالفعل
            if ($('#confirmation-' + confirmation.id).length === 0) {
                const html = createConfirmationHtml(confirmation);
                $list.prepend(html);
            }
        });
    }

    // تحديث الإشعارات
    function updateNotifications(notifications) {
        const $list = $('#notifications-list');
        $('#no-notifications').remove();
        
        notifications.forEach(function(notification) {
            const html = createNotificationHtml(notification);
            $list.prepend(html);
        });

        $('#notifications-badge').text($('.notification-item').length);
    }

    // إنشاء HTML للتأكيد
    function createConfirmationHtml(confirmation) {
        const materialName = confirmation.delivery_note?.material?.name_ar || 
                           confirmation.delivery_note?.material?.name || 
                           confirmation.batch?.material?.name_ar || 
                           confirmation.batch?.material?.name || 'غير محدد';
        const productionBarcode = confirmation.delivery_note?.production_barcode || 
                                 confirmation.delivery_note?.material_batch?.batch_code || 
                                 confirmation.batch?.batch_code || 'غير محدد';
        const weight = confirmation.delivery_note?.quantity_used || 
                      confirmation.delivery_note?.material_batch?.initial_quantity || 
                      confirmation.batch?.initial_quantity || 
                      confirmation.delivery_note?.quantity || 0;
        const finalWeight = weight > 0 ? parseFloat(weight).toFixed(2) : 'غير محدد';

        return `
            <div class="confirmation-card new-item" id="confirmation-${confirmation.id}">
                <div class="confirmation-header">
                    <div class="confirmation-title">
                        <i class="fas fa-box-open"></i>
                        <h4>${materialName}</h4>
                    </div>
                    <div class="confirmation-time">
                        <i class="fas fa-clock"></i>
                        الآن
                    </div>
                </div>
                <div class="confirmation-body">
                    <div class="confirmation-details">
                        <div class="detail-item">
                            <span class="detail-label">
                                <i class="fas fa-barcode"></i> باركود الإنتاج
                            </span>
                            <span class="detail-value">${productionBarcode}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">
                                <i class="fas fa-weight"></i> الوزن النهائي
                            </span>
                            <span class="detail-value">${finalWeight} كجم</span>
                        </div>
                    </div>
                </div>
                <div class="confirmation-actions">
                    <button class="action-btn btn-confirm quick-confirm" data-id="${confirmation.id}">
                        <i class="fas fa-check"></i>
                        <span>تأكيد</span>
                    </button>
                    <button class="action-btn btn-reject quick-reject" data-id="${confirmation.id}">
                        <i class="fas fa-times"></i>
                        <span>رفض</span>
                    </button>
                    <a href="/manufacturing/production/confirmations/${confirmation.id}" class="action-btn btn-details">
                        <i class="fas fa-eye"></i>
                        <span>تفاصيل</span>
                    </a>
                </div>
            </div>
        `;
    }

    // إنشاء HTML للإشعار
    function createNotificationHtml(notification) {
        return `
            <div class="notification-item alert alert-${notification.type} alert-dismissible fade show new-item" role="alert">
                <strong>${notification.title}</strong>
                <p class="mb-1 small">${notification.message}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-clock"></i> ${notification.time}
                    </small>
                    <a href="${notification.url}" class="btn btn-sm btn-outline-primary">
                        عرض التفاصيل
                    </a>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }

    // تأكيد سريع - استخدام event delegation للعناصر الديناميكية والثابتة
    $(document).on('click', '.quick-confirm', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        currentConfirmationId = $(this).data('id');
        console.log('=== Quick Confirm Button Clicked ===');
        console.log('Confirmation ID:', currentConfirmationId);
        console.log('Button element:', this);
        
        if (!currentConfirmationId) {
            console.error('ERROR: No data-id attribute found on button!');
            showNotification('خطأ: لم يتم العثور على معرف التأكيد', 'error');
            return;
        }
        
        // مسح الملاحظات السابقة
        $('#confirm-notes').val('');
        
        console.log('Opening confirm modal...');
        try {
            if (typeof bootstrap !== 'undefined' && confirmModal) {
                confirmModal.show();
                console.log('Modal shown using Bootstrap 5');
            } else {
                $('#quickConfirmModal').modal('show');
                console.log('Modal shown using jQuery');
            }
        } catch (error) {
            console.error('Error opening modal:', error);
        }
    });

    $('#confirm-submit').on('click', function(e) {
        e.preventDefault();
        console.log('=== Confirm Submit Button Clicked ===');
        
        const notes = $('#confirm-notes').val();
        const btn = $(this);
        
        console.log('Current Confirmation ID:', currentConfirmationId);
        console.log('Notes:', notes);
        
        if (!currentConfirmationId) {
            console.error('ERROR: No confirmation ID set!');
            showNotification('خطأ: لم يتم تحديد التأكيد', 'error');
            return;
        }
        
        console.log('Starting AJAX request to:', `/manufacturing/production/confirmations/${currentConfirmationId}/confirm`);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> جاري التأكيد...');
        
        $.ajax({
            url: `/manufacturing/production/confirmations/${currentConfirmationId}/confirm`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            contentType: 'application/json',
            data: JSON.stringify({ 
                notes: notes,
                _token: '{{ csrf_token() }}'
            }),
            success: function(response) {
                console.log('Confirm success:', response);
                
                // إغلاق Modal
                try {
                    if (typeof bootstrap !== 'undefined' && confirmModal) {
                        confirmModal.hide();
                    } else {
                        $('#quickConfirmModal').modal('hide');
                    }
                } catch (e) {
                    console.error('Error closing modal:', e);
                }
                
                // إزالة البطاقة من القائمة
                const $card = $('#confirmation-' + currentConfirmationId);
                if ($card.length) {
                    $card.fadeOut(300, function() {
                        $(this).remove();
                        // التحقق إذا لم يبق تأكيدات
                        if ($('#confirmations-list .confirmation-card').length === 0) {
                            $('#confirmations-list').html(`
                                <div class="empty-state" id="no-confirmations">
                                    <div class="empty-icon">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <h4>لا توجد تأكيدات منتظرة</h4>
                                    <p>جميع التأكيدات تم معالجتها بنجاح</p>
                                </div>
                            `);
                        }
                    });
                }
                
                showNotification(response.message || 'تم التأكيد بنجاح', 'success');
                
                // تحديث الإحصائيات
                setTimeout(() => fetchUpdates(), 500);
                
                // إعادة تعيين الزر
                btn.prop('disabled', false).html('<i class="fas fa-check"></i> تأكيد');
                
                // مسح ID
                currentConfirmationId = null;
            },
            error: function(xhr, status, error) {
                console.error('Confirm AJAX error:');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                console.error('Status Code:', xhr.status);
                
                let message = 'فشل التأكيد';
                
                if (xhr.status === 404) {
                    message = 'لم يتم العثور على التأكيد المطلوب';
                } else if (xhr.status === 403) {
                    message = 'ليس لديك صلاحية لتنفيذ هذا الإجراء';
                } else if (xhr.status === 422) {
                    message = xhr.responseJSON?.message || 'بيانات غير صحيحة';
                } else if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.statusText) {
                    message += ': ' + xhr.statusText;
                }
                
                showNotification(message, 'error');
                btn.prop('disabled', false).html('<i class="fas fa-check"></i> تأكيد');
            }
        });
    });

    // رفض سريع - استخدام event delegation للعناصر الديناميكية والثابتة
    $(document).on('click', '.quick-reject', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        currentConfirmationId = $(this).data('id');
        console.log('=== Quick Reject Button Clicked ===');
        console.log('Confirmation ID:', currentConfirmationId);
        console.log('Button element:', this);
        
        if (!currentConfirmationId) {
            console.error('ERROR: No data-id attribute found on button!');
            showNotification('خطأ: لم يتم العثور على معرف التأكيد', 'error');
            return;
        }
        
        // مسح سبب الرفض السابق
        $('#reject-reason').val('');
        
        console.log('Opening reject modal...');
        try {
            if (typeof bootstrap !== 'undefined' && rejectModal) {
                rejectModal.show();
                console.log('Modal shown using Bootstrap 5');
            } else {
                $('#quickRejectModal').modal('show');
                console.log('Modal shown using jQuery');
            }
        } catch (error) {
            console.error('Error opening modal:', error);
        }
    });

    $('#reject-submit').on('click', function(e) {
        e.preventDefault();
        console.log('=== Reject Submit Button Clicked ===');
        
        const reason = $('#reject-reason').val();
        const btn = $(this);
        
        console.log('Current Confirmation ID:', currentConfirmationId);
        console.log('Rejection Reason:', reason);
        
        if (!reason.trim()) {
            console.error('ERROR: No rejection reason provided!');
            showNotification('الرجاء إدخال سبب الرفض', 'error');
            $('#reject-reason').focus();
            return;
        }
        
        if (!currentConfirmationId) {
            console.error('ERROR: No confirmation ID set!');
            showNotification('خطأ: لم يتم تحديد التأكيد', 'error');
            return;
        }

        console.log('Starting AJAX request for rejection to:', `/manufacturing/production/confirmations/${currentConfirmationId}/reject`);
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> جاري الرفض...');
        
        $.ajax({
            url: `/manufacturing/production/confirmations/${currentConfirmationId}/reject`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            contentType: 'application/json',
            data: JSON.stringify({ 
                rejection_reason: reason,
                _token: '{{ csrf_token() }}'
            }),
            success: function(response) {
                console.log('Reject success:', response);
                
                // إغلاق Modal
                try {
                    if (typeof bootstrap !== 'undefined' && rejectModal) {
                        rejectModal.hide();
                    } else {
                        $('#quickRejectModal').modal('hide');
                    }
                } catch (e) {
                    console.error('Error closing modal:', e);
                }
                
                // إزالة البطاقة من القائمة
                const $card = $('#confirmation-' + currentConfirmationId);
                if ($card.length) {
                    $card.fadeOut(300, function() {
                        $(this).remove();
                        // التحقق إذا لم يبق تأكيدات
                        if ($('#confirmations-list .confirmation-card').length === 0) {
                            $('#confirmations-list').html(`
                                <div class="empty-state" id="no-confirmations">
                                    <div class="empty-icon">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <h4>لا توجد تأكيدات منتظرة</h4>
                                    <p>جميع التأكيدات تم معالجتها بنجاح</p>
                                </div>
                            `);
                        }
                    });
                }
                
                $('#reject-reason').val('');
                showNotification(response.message || 'تم الرفض بنجاح', 'success');
                
                // تحديث الإحصائيات
                setTimeout(() => fetchUpdates(), 500);
                
                // إعادة تعيين الزر
                btn.prop('disabled', false).html('<i class="fas fa-times"></i> رفض');
                
                // مسح ID
                currentConfirmationId = null;
            },
            error: function(xhr, status, error) {
                console.error('Reject AJAX error:');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                console.error('Status Code:', xhr.status);
                
                let message = 'فشل الرفض';
                
                if (xhr.status === 404) {
                    message = 'لم يتم العثور على التأكيد المطلوب';
                } else if (xhr.status === 403) {
                    message = 'ليس لديك صلاحية لتنفيذ هذا الإجراء';
                } else if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors && errors.rejection_reason) {
                        message = errors.rejection_reason[0];
                    } else {
                        message = xhr.responseJSON?.message || 'بيانات غير صحيحة';
                    }
                } else if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.statusText) {
                    message += ': ' + xhr.statusText;
                }
                
                showNotification(message, 'error');
                btn.prop('disabled', false).html('<i class="fas fa-times"></i> رفض');
            }
        });
    });

    // إظهار إشعار Toast
    function showNotification(message, type) {
        const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
        const toast = `
            <div class="toast align-items-center text-white ${bgClass} border-0" role="alert" style="position: fixed; top: 20px; left: 20px; z-index: 9999;">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        $('body').append(toast);
        const $toast = $('.toast').last();
        const bsToast = new bootstrap.Toast($toast[0]);
        bsToast.show();
        setTimeout(() => $toast.remove(), 5000);
    }

    // بدء التحديث التلقائي
    startAutoRefresh();

    // تنظيف عند مغادرة الصفحة
    $(window).on('beforeunload', function() {
        clearInterval(autoRefreshInterval);
    });
});
</script>
@endpush
