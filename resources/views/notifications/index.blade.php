@extends('master')

@section('title', 'الإشعارات')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">الإشعارات</h1>
                    <small class="text-muted">إدارة والاطلاع على جميع الإشعارات والتنبيهات</small>
                </div>
                <div class="btn-group">
                    <button id="markAllReadBtn" class="btn btn-primary btn-sm" title="وضع علامة على الكل كمقروء">
                        <i class="feather icon-check-circle"></i> وضع علامة الكل مقروء
                    </button>
                    <button id="deleteAllBtn" class="btn btn-danger btn-sm" title="حذف جميع الإشعارات">
                        <i class="feather icon-trash-2"></i> حذف الكل
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">إجمالي الإشعارات</p>
                                    <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                                </div>
                                <i class="feather icon-bell" style="font-size: 2.5rem; color: #3b82f6; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">غير مقروءة</p>
                                    <h3 class="mb-0" style="color: #f59e0b;">{{ $stats['unread'] ?? 0 }}</h3>
                                </div>
                                <i class="feather icon-alert-circle" style="font-size: 2.5rem; color: #f59e0b; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">مقروءة</p>
                                    <h3 class="mb-0" style="color: #10b981;">{{ $stats['read'] ?? 0 }}</h3>
                                </div>
                                <i class="feather icon-check-circle" style="font-size: 2.5rem; color: #10b981; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">تحذيرات</p>
                                    <h3 class="mb-0" style="color: #ef4444;">
                                        @if(isset($stats['by_color']) && $stats['by_color'] instanceof \Illuminate\Support\Collection)
                                            {{ $stats['by_color']->where('color', 'danger')->first()?->count ?? 0 }}
                                        @else
                                            0
                                        @endif
                                    </h3>
                                </div>
                                <i class="feather icon-alert-triangle" style="font-size: 2.5rem; color: #ef4444; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('notifications.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">النوع</label>
                            <select name="type" class="form-select" onchange="this.form.submit()">
                                <option value="">جميع الأنواع</option>
                                <option value="material_added" {{ request('type') === 'material_added' ? 'selected' : '' }}>مادة مضافة</option>
                                <option value="material_updated" {{ request('type') === 'material_updated' ? 'selected' : '' }}>مادة محدثة</option>
                                <option value="purchase_invoice_created" {{ request('type') === 'purchase_invoice_created' ? 'selected' : '' }}>فاتورة شراء</option>
                                <option value="delivery_note_registered" {{ request('type') === 'delivery_note_registered' ? 'selected' : '' }}>أذن توصيل</option>
                                <option value="weight_discrepancy" {{ request('type') === 'weight_discrepancy' ? 'selected' : '' }}>فرق وزن</option>
                                <option value="moved_to_production" {{ request('type') === 'moved_to_production' ? 'selected' : '' }}>نقل للإنتاج</option>
                                <option value="production_transfer" {{ request('type') === 'production_transfer' ? 'selected' : '' }}>طلب استلام دفعة</option>
                                <option value="production_confirmed" {{ request('type') === 'production_confirmed' ? 'selected' : '' }}>تأكيد استلام</option>
                                <option value="production_rejected" {{ request('type') === 'production_rejected' ? 'selected' : '' }}>رفض استلام</option>
                                <option value="custom" {{ request('type') === 'custom' ? 'selected' : '' }}>مخصص</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">الحالة</label>
                            <select name="unread" class="form-select" onchange="this.form.submit()">
                                <option value="">الكل</option>
                                <option value="1" {{ request('unread') === '1' ? 'selected' : (request('unread') === 'true' ? 'selected' : '') }}>غير مقروءة فقط</option>
                                <option value="0" {{ request('unread') === '0' ? 'selected' : (request('unread') === 'false' ? 'selected' : '') }}>مقروءة فقط</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="feather icon-search"></i> بحث
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    @if($notifications && $notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item notification-item {{ !$notification->is_read ? 'unread' : '' }}"
                                     style="background-color: {{ !$notification->is_read ? '#f3f4f6' : 'white' }}; border-left: 4px solid {{ $notification->color === 'success' ? '#10b981' : ($notification->color === 'danger' ? '#ef4444' : ($notification->color === 'warning' ? '#f59e0b' : '#3b82f6')) }};">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="dropdown" style="position: absolute; left: 15px; top: 15px;">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                <i class="feather icon-more-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if(!$notification->is_read)
                                                    <li>
                                                        <a class="dropdown-item mark-read" href="#" data-id="{{ $notification->id }}">
                                                            <i class="feather icon-check-circle"></i> وضع علامة مقروء
                                                        </a>
                                                    </li>
                                                @endif
                                                @if($notification->action_url && $notification->action_url !== '#')
                                                    <li>
                                                        <a class="dropdown-item" href="{{ $notification->action_url }}">
                                                            <i class="feather icon-external-link"></i> الذهاب للصفحة
                                                        </a>
                                                    </li>
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger delete-notif" href="#" data-id="{{ $notification->id }}">
                                                        <i class="feather icon-trash-2"></i> حذف
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="flex-grow-1" style="padding-left: 50px;">
                                            <div class="d-flex align-items-center mb-2">
                                                @if($notification->icon)
                                                    <i class="{{ $notification->icon }} me-2" style="font-size: 1.25rem; color: {{ $notification->color === 'success' ? '#10b981' : ($notification->color === 'danger' ? '#ef4444' : ($notification->color === 'warning' ? '#f59e0b' : '#3b82f6')) }};"></i>
                                                @endif
                                                <h6 class="mb-0" style="font-weight: 600;">{{ $notification->title }}</h6>
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-primary ms-2">جديد</span>
                                                @endif
                                            </div>
                                            <p class="mb-2 text-dark">{{ $notification->message }}</p>
                                            <small class="text-muted">
                                                <i class="feather icon-user"></i> {{ $notification->creator?->name ?? 'النظام' }}
                                                • <i class="feather icon-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="feather icon-inbox" style="font-size: 3rem; color: #d1d5db;"></i>
                            <h5 class="mt-3 text-muted">لا توجد إشعارات</h5>
                            <p class="text-muted">جميع الإشعارات محذوفة أو غير متاحة حالياً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification-item {
    padding: 1rem;
    transition: all 0.3s ease;
    position: relative;
}

.notification-item:hover {
    background-color: #f9fafb !important;
}

.notification-item.unread {
    font-weight: 500;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark as read
    document.querySelectorAll('.mark-read').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            fetch(`/notifications/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(err => console.error('Error:', err));
        });
    });

    // Delete notification
    document.querySelectorAll('.delete-notif').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('هل أنت متأكد من حذف هذا الإشعار؟')) {
                const id = this.getAttribute('data-id');
                fetch(`/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    })
                    .catch(err => console.error('Error:', err));
            }
        });
    });

    // Mark all as read
    document.getElementById('markAllReadBtn')?.addEventListener('click', function() {
        if (confirm('وضع علامة على جميع الإشعارات كمقروءة؟')) {
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(err => console.error('Error:', err));
        }
    });

    // Delete all
    document.getElementById('deleteAllBtn')?.addEventListener('click', function() {
        if (confirm('هل أنت متأكد من حذف جميع الإشعارات؟')) {
            fetch('/notifications', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(err => console.error('Error:', err));
        }
    });
});
</script>
@endsection
