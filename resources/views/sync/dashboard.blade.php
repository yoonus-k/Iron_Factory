@extends('master')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ __('لوحة تحكم المزامنة') }}</h2>
            <p class="text-muted mb-0">{{ __('مراقبة حالة المزامنة بين السيرفر المحلي والأونلاين') }}</p>
        </div>
        <div>
            <button class="btn btn-primary" onclick="refreshAll()">
                <i class="fa fa-refresh"></i> {{ __('تحديث') }}
            </button>
            <button class="btn btn-success" onclick="syncNow()">
                <i class="fa fa-sync"></i> {{ __('مزامنة الآن') }}
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">{{ __('قيد الانتظار') }}</h6>
                            <h3 class="mb-0" id="stat-pending">-</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fa fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">{{ __('تمت المزامنة') }}</h6>
                            <h3 class="mb-0" id="stat-synced">-</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fa fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">{{ __('فشلت') }}</h6>
                            <h3 class="mb-0" id="stat-failed">-</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fa fa-exclamation-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">{{ __('معدل النجاح') }}</h6>
                            <h3 class="mb-0" id="stat-success-rate">-</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fa fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Next Sync Timer -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-4">
                    <h5 class="mb-3">{{ __('المزامنة القادمة خلال') }}</h5>
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        <div>
                            <span class="display-4 fw-bold" id="timer-minutes">5</span>
                            <small class="d-block">{{ __('دقائق') }}</small>
                        </div>
                        <span class="display-4">:</span>
                        <div>
                            <span class="display-4 fw-bold" id="timer-seconds">00</span>
                            <small class="d-block">{{ __('ثانية') }}</small>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             id="timer-progress" 
                             role="progressbar" 
                             style="width: 0%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('إحصائيات المزامنة (آخر 7 أيام)') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="syncChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('توزيع حسب نوع البيانات') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="entityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Operations Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('العمليات المعلقة') }}</h5>
                    <button class="btn btn-sm btn-warning" onclick="retryAll()">
                        <i class="fa fa-redo"></i> {{ __('إعادة محاولة الكل') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="pending-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('النوع') }}</th>
                                    <th>{{ __('العملية') }}</th>
                                    <th>{{ __('الحالة') }}</th>
                                    <th>{{ __('المستخدم') }}</th>
                                    <th>{{ __('التاريخ') }}</th>
                                    <th>{{ __('الإجراءات') }}</th>
                                </tr>
                            </thead>
                            <tbody id="pending-tbody">
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                                        <p class="mt-2">{{ __('جاري التحميل...') }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        border: none;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
    }

    .table th {
        font-weight: 600;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .display-4 {
        font-size: 3rem;
        color: #2c3e50;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let syncChart, entityChart;
let nextSyncTime = Date.now() + (5 * 60 * 1000); // 5 دقائق من الآن

// إعداد CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// تحميل الإحصائيات
async function loadStats() {
    try {
        const response = await fetch('/sync/stats');
        if (!response.ok) {
            throw new Error('Failed to load stats');
        }
        const data = await response.json();

        document.getElementById('stat-pending').textContent = data.total_pending || 0;
        document.getElementById('stat-synced').textContent = data.total_synced || 0;
        document.getElementById('stat-failed').textContent = data.total_failed || 0;
        document.getElementById('stat-success-rate').textContent = (data.success_rate || 0) + '%';

        // تحديث الرسوم البيانية
        updateCharts();
    } catch (error) {
        console.error('Error loading stats:', error);
        document.getElementById('stat-pending').textContent = '0';
        document.getElementById('stat-synced').textContent = '0';
        document.getElementById('stat-failed').textContent = '0';
        document.getElementById('stat-success-rate').textContent = '0%';
    }
}

// تحميل العمليات المعلقة
async function loadPending() {
    try {
        const response = await fetch('/sync/pending');
        if (!response.ok) {
            throw new Error('Failed to load pending syncs');
        }
        const data = await response.json();

        const tbody = document.getElementById('pending-tbody');
        
        if (data.data && data.data.length > 0) {
            tbody.innerHTML = data.data.map((item, index) => `
                <tr>
                    <td>${index + 1}</td>
                    <td><span class="badge bg-secondary">${item.entity_type}</span></td>
                    <td><span class="badge bg-info">${item.action}</span></td>
                    <td>${getStatusBadge(item.status)}</td>
                    <td>${item.user ? item.user.name : '-'}</td>
                    <td>${new Date(item.created_at).toLocaleString('ar-EG')}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="retryItem(${item.id})">
                            <i class="fa fa-redo"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteItem(${item.id})">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">لا توجد عمليات معلقة</td></tr>';
        }
    } catch (error) {
        console.error('Error loading pending:', error);
        const tbody = document.getElementById('pending-tbody');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger"><i class="fa fa-exclamation-triangle"></i> خطأ في تحميل البيانات: ' + error.message + '</td></tr>';
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger"><i class="fa fa-exclamation-triangle"></i> خطأ في تحميل البيانات: ' + error.message + '</td></tr>';
    }
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">معلقة</span>',
        'processing': '<span class="badge bg-info">قيد المعالجة</span>',
        'synced': '<span class="badge bg-success">تمت المزامنة</span>',
        'failed': '<span class="badge bg-danger">فشلت</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
}

// تحديث المؤقت
function updateTimer() {
    const now = Date.now();
    const diff = nextSyncTime - now;

    if (diff <= 0) {
        // انتهى الوقت - بدء مزامنة جديدة
        nextSyncTime = Date.now() + (5 * 60 * 1000);
        loadStats();
        loadPending();
    }

    const minutes = Math.floor(diff / 60000);
    const seconds = Math.floor((diff % 60000) / 1000);

    document.getElementById('timer-minutes').textContent = minutes;
    document.getElementById('timer-seconds').textContent = seconds.toString().padStart(2, '0');

    // تحديث شريط التقدم
    const totalSeconds = 5 * 60;
    const elapsedSeconds = totalSeconds - (minutes * 60 + seconds);
    const progress = (elapsedSeconds / totalSeconds) * 100;
    document.getElementById('timer-progress').style.width = progress + '%';
}

// تحديث الرسوم البيانية
async function updateCharts() {
    try {
        const response = await fetch('/sync/chart-data?days=7');
        const data = await response.json();

        // Line Chart
        if (syncChart) syncChart.destroy();
        syncChart = new Chart(document.getElementById('syncChart'), {
            type: 'line',
            data: {
                labels: data.map(d => new Date(d.date).toLocaleDateString('ar-EG')),
                datasets: [
                    {
                        label: 'تمت المزامنة',
                        data: data.map(d => d.synced),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'فشلت',
                        data: data.map(d => d.failed),
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });

        // Entity Distribution
        const statsResponse = await fetch('/sync/stats');
        const stats = await statsResponse.json();

        if (entityChart) entityChart.destroy();
        entityChart = new Chart(document.getElementById('entityChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(stats.by_entity || {}),
                datasets: [{
                    data: Object.values(stats.by_entity || {}),
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545',
                        '#17a2b8', '#6c757d', '#e83e8c', '#fd7e14'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    } catch (error) {
        console.error('Error updating charts:', error);
    }
}

// مزامنة فورية
async function syncNow() {
    try {
        const response = await fetch('/sync/process-now', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        const data = await response.json();
        
        Swal.fire({
            icon: 'success',
            title: 'تمت المزامنة',
            text: `تمت معالجة ${data.processed} عملية بنجاح`,
            timer: 2000
        });

        nextSyncTime = Date.now() + (5 * 60 * 1000);
        refreshAll();
    } catch (error) {
        Swal.fire('خطأ', 'فشلت المزامنة', 'error');
    }
}

// إعادة محاولة عملية واحدة
async function retryItem(id) {
    try {
        const response = await fetch(`/sync/retry/${id}`, { 
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        if (!response.ok) throw new Error('Retry failed');
        Swal.fire('نجح', 'تم إعادة المحاولة', 'success');
        loadPending();
        loadStats();
    } catch (error) {
        Swal.fire('خطأ', 'فشلت إعادة المحاولة', 'error');
    }
}

// حذف عملية
async function deleteItem(id) {
    const result = await Swal.fire({
        title: 'هل أنت متأكد؟',
        text: 'لن تتمكن من التراجع عن هذا',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/sync/delete/${id}`, { 
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            if (!response.ok) throw new Error('Delete failed');
            Swal.fire('تم الحذف', 'تم حذف العملية بنجاح', 'success');
            loadPending();
            loadStats();
        } catch (error) {
            Swal.fire('خطأ', 'فشل الحذف', 'error');
        }
    }
}

// إعادة محاولة الكل
async function retryAll() {
    try {
        const response = await fetch('/sync/retry-all', { 
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        if (!response.ok) throw new Error('Retry all failed');
        const data = await response.json();
        Swal.fire('نجح', `تم إعادة محاولة ${data.count} عملية`, 'success');
        refreshAll();
    } catch (error) {
        Swal.fire('خطأ', 'فشلت إعادة المحاولة', 'error');
    }
}

// تحديث الكل
function refreshAll() {
    loadStats();
    loadPending();
}

// التهيئة عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadPending();
    
    // تحديث المؤقت كل ثانية
    setInterval(updateTimer, 1000);
    
    // تحديث البيانات كل 30 ثانية
    setInterval(refreshAll, 30000);
});
</script>
@endpush
@endsection
