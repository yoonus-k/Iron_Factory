@extends('master')

@section('title', 'إدارة الورديات والعمال')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-users"></i>
                إدارة الورديات والعمال
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>الورديات والعمال</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        @if(session('success'))
        <div class="um-alert-custom um-alert-success" role="alert">
            <i class="feather icon-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="um-alert-custom um-alert-danger" role="alert">
            <i class="feather icon-alert-circle"></i>
            {{ session('error') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
        @endif

        <!-- Main Courses Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    قائمة الورديات
                </h4>
                <div style="display: flex; gap: 10px;">
@if(auth()->user()->hasPermission('SHIFT_HANDOVERS_READ'))
                    <a href="{{ route('manufacturing.shift-handovers.index') }}" class="um-btn um-btn-info">
                        <i class="feather icon-exchange-2"></i>
                        نقل الورديات
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('SHIFTS_CREATE'))
                    <a href="{{ route('manufacturing.shifts-workers.create') }}" class="um-btn um-btn-primary">
                        <i class="feather icon-plus"></i>
                        إضافة وردية جديدة
                    </a>
                    @endif
                </div>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET" action="{{ route('manufacturing.shifts-workers.index') }}">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="date" name="date" class="um-form-control" placeholder="التاريخ..." value="{{ request('date') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="shift_type" class="um-form-control">
                                <option value="">جميع أنواع الورديات</option>
                                <option value="morning" {{ request('shift_type') == 'morning' ? 'selected' : '' }}>الفترة الصباحية</option>
                                <option value="evening" {{ request('shift_type') == 'evening' ? 'selected' : '' }}>الفترة المسائية</option>
                                <option value="night" {{ request('shift_type') == 'night' ? 'selected' : '' }}>الفترة الليلية</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشطة</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="stage_number" class="um-form-control">
                                <option value="">جميع المراحل</option>
                                <option value="1" {{ request('stage_number') == '1' ? 'selected' : '' }}>المرحلة الأولى</option>
                                <option value="2" {{ request('stage_number') == '2' ? 'selected' : '' }}>المرحلة الثانية</option>
                                <option value="3" {{ request('stage_number') == '3' ? 'selected' : '' }}>المرحلة الثالثة</option>
                                <option value="4" {{ request('stage_number') == '4' ? 'selected' : '' }}>المرحلة الرابعة</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <a href="{{ route('manufacturing.shifts-workers.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Courses Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رقم الوردية</th>
                            <th>التاريخ</th>
                            <th>نوع الوردية</th>
                            <th>عدد العمال</th>
                            <th>المسؤول</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $index => $shift)
                        <tr>
                            <td>{{ $shifts->firstItem() + $index }}</td>
                            <td><strong>{{ $shift->shift_code }}</strong></td>
                            <td>{{ $shift->shift_date->format('Y-m-d') }}</td>
                            <td>
                                <span class="um-badge um-badge-{{ $shift->shift_type == 'morning' ? 'info' : ($shift->shift_type == 'evening' ? 'warning' : 'danger') }}">
                                    {{ $shift->shift_type_name }}
                                </span>
                            </td>
                            <td>{{ $shift->total_workers }}</td>
                            <td>{{ $shift->supervisor->name ?? 'غير محدد' }}</td>
                            <td>
                                <span class="um-badge um-badge-{{ $shift->status == 'active' ? 'success' : ($shift->status == 'scheduled' ? 'info' : 'secondary') }}">
                                    {{ $shift->status_name }}
                                </span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
@if (auth()->user()->hasPermission('SHIFTS_READ'))


                                        <a href="{{ route('manufacturing.shifts-workers.show', $shift->id) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        @endif
@if (auth()->user()->hasPermission('SHIFTS_UPDATE'))
                                        <a href="{{ route('manufacturing.shifts-workers.edit', $shift->id) }}" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-edit-2"></i>
                                            <span>تعديل</span>
                                        </a>
                                        @endif
                                        @if($shift->status == 'scheduled')
                                        @if (auth()->user()->hasPermission('SHIFTS_ACTIVATE'))
                                        <form method="POST" action="{{ route('manufacturing.shifts-workers.activate', $shift->id) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="um-dropdown-item um-btn-feature">
                                                <i class="feather icon-play"></i>
                                                <span>تفعيل</span>
                                            </button>
                                        </form>
                                        @endif
                                        @endif
                                        @if($shift->status == 'active')
                                            @if (auth()->user()->hasPermission('SHIFTS_COMPLETE'))
                                            <form method="POST" action="{{ route('manufacturing.shifts-workers.complete', $shift->id) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="um-dropdown-item um-btn-toggle">
                                                    <i class="feather icon-check"></i>
                                                    <span>إكمال</span>
                                                </button>
                                            </form>
                                            @endif
                                            @if (auth()->user()->hasPermission('SHIFTS_SUSPEND'))
                                            <button type="button" class="um-dropdown-item um-btn-warning" onclick="openSuspendModal({{ $shift->id }})">
                                                <i class="feather icon-pause"></i>
                                                <span>تعليق</span>
                                            </button>
                                            @endif
                                            @if (auth()->user()->hasPermission('SHIFT_HANDOVERS_FROM_INDEX'))
                                            <button type="button" class="um-dropdown-item um-btn-info" onclick="openHandoverModal({{ $shift->id }}, '{{ $shift->shift_code }}')">
                                                <i class="feather icon-exchange-2"></i>
                                                <span>نقل الوردية</span>
                                            </button>
                                            @endif
                                        @elseif($shift->status == 'suspended')
                                            @if (auth()->user()->hasPermission('SHIFTS_RESUME'))
                                            <form method="POST" action="{{ route('manufacturing.shifts-workers.resume', $shift->id) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="um-dropdown-item um-btn-feature">
                                                    <i class="feather icon-play"></i>
                                                    <span>استئناف</span>
                                                </button>
                                            </form>
                                            @endif
                                        @elseif(in_array($shift->status, ['scheduled', 'cancelled']))
                                            @if (auth()->user()->hasPermission('SHIFTS_DELETE'))
                                            <form method="POST" action="{{ route('manufacturing.shifts-workers.destroy', $shift->id) }}" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="um-dropdown-item um-btn-delete">
                                                    <i class="feather icon-trash-2"></i>
                                                    <span>حذف</span>
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                                <i class="feather icon-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                لا توجد ورديات
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Courses Cards - Mobile View -->
            <div class="um-mobile-view">
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                <i class="feather icon-users" style="font-size: 18px;"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">وردية صباحية</h6>
                                <span class="um-category-id">#SHIFT-2025-001</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-success">نشطة</span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">التاريخ:</span>
                            <span class="um-info-value">2025-01-15</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">عدد العمال:</span>
                            <span class="um-info-value">8 عمال</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">المسؤول:</span>
                            <span class="um-info-value">أحمد محمد</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
@if (auth()->user()->hasPermission('SHIFTS_READ'))
                        <a href="{{ route('manufacturing.shifts-workers.show', 1) }}" class="um-btn um-btn-sm um-btn-primary">
                            <i class="feather icon-eye" style="font-size: 14px;"></i>
                            عرض
                        </a>
@endif
                        @if (auth()->user()->hasPermission('SHIFTS_UPDATE'))
                        <a href="{{ route('manufacturing.shifts-workers.edit', 1) }}" class="um-btn um-btn-sm um-btn-secondary">
                            <i class="feather icon-edit-2" style="font-size: 14px;"></i>
                            تعديل
                        </a>
                        @endif
                    </div>
                </div>

                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <div class="um-category-icon" style="background: #3f51b520; color: #3f51b5; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                <i class="feather icon-users" style="font-size: 18px;"></i>
                            </div>
                            <div>
                                <h6 class="um-category-name">وردية مسائية</h6>
                                <span class="um-category-id">#SHIFT-2025-002</span>
                            </div>
                        </div>
                        <span class="um-badge um-badge-success">نشطة</span>
                    </div>

                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span class="um-info-label">التاريخ:</span>
                            <span class="um-info-value">2025-01-15</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">عدد العمال:</span>
                            <span class="um-info-value">6 عمال</span>
                        </div>
                        <div class="um-info-row">
                            <span class="um-info-label">المسؤول:</span>
                            <span class="um-info-value">محمد علي</span>
                        </div>
                    </div>

                    <div class="um-category-card-footer">
                        @if (auth()->user()->hasPermission('SHIFTS_READ'))
                        <a href="{{ route('manufacturing.shifts-workers.show', 2) }}" class="um-btn um-btn-sm um-btn-primary">
                            <i class="feather icon-eye" style="font-size: 14px;"></i>
                            عرض
                        </a>
                        @endif
                        @if (auth()->user()->hasPermission('SHIFTS_UPDATE'))
                        <a href="{{ route('manufacturing.shifts-workers.edit', 2) }}" class="um-btn um-btn-sm um-btn-secondary">
                            <i class="feather icon-edit-2" style="font-size: 14px;"></i>
                            تعديل
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="um-pagination-section">
                <div>
                    <p class="um-pagination-info">
                        عرض {{ $shifts->firstItem() ?? 0 }} إلى {{ $shifts->lastItem() ?? 0 }} من أصل {{ $shifts->total() }} وردية
                    </p>
                </div>
                <div>
                    {{ $shifts->links() }}
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تأكيد الحذف
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('هل أنت متأكد من حذف هذه الوردية؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                        alert('تم حذف الوردية بنجاح');
                    }
                });
            });

            // إخفاء التنبيهات تلقائياً بعد 5 ثواني
            const alerts = document.querySelectorAll('.um-alert-custom');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.3s';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 300);
                }, 5000);
            });
        });

        // Suspend/Resume Modal Functions
        function openSuspendModal(shiftId) {
            const form = document.getElementById('suspendForm');
            form.action = `/manufacturing/shifts-workers/${shiftId}/suspend`;
            document.getElementById('suspendModal').style.display = 'flex';
        }

        function closeSuspendModal() {
            document.getElementById('suspendModal').style.display = 'none';
            document.getElementById('suspension_reason').value = '';
        }

        // Handover Modal Functions
        function openHandoverModal(shiftId, shiftCode) {
            const form = document.getElementById('handoverForm');
            form.action = `{{ route('manufacturing.shift-handovers.store') }}`;
            document.getElementById('handover_shift_id').value = shiftId;
            document.getElementById('handover_shift_code').textContent = shiftCode;
            document.getElementById('handoverModal').style.display = 'flex';
        }

        function closeHandoverModal() {
            document.getElementById('handoverModal').style.display = 'none';
            document.getElementById('handover_to_user_id').value = '';
            document.getElementById('handover_stage_number').value = '';
            document.getElementById('handover_notes').value = '';
        }

        // Close modal when clicking outside
        document.getElementById('suspendModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeSuspendModal();
            }
        });

        document.getElementById('handoverModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeHandoverModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSuspendModal();
                closeHandoverModal();
            }
        });
    </script>

    <!-- Suspend Modal -->
    <div id="suspendModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>تعليق الوردية</h3>
                <button class="close-btn" onclick="closeSuspendModal()">×</button>
            </div>
            <form id="suspendForm" method="POST">
                @csrf
                @method('PATCH')@
                <div class="modal-body">
                    <div class="form-group">
                        <label for="suspension_reason">السبب (اختياري)</label>
                        <textarea
                            id="suspension_reason"
                            name="suspension_reason"
                            class="form-control"
                            rows="4"
                            placeholder="أدخل سبب تعليق الوردية...">
                        </textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeSuspendModal()">إلغاء</button>
                    <button type="submit" class="btn btn-warning">تعليق الوردية</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Handover Modal -->
    <div id="handoverModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>نقل الوردية</h3>
                <button class="close-btn" onclick="closeHandoverModal()">×</button>
            </div>
            <form id="handoverForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>رقم الوردية</label>
                        <input type="text" class="form-control" id="handover_shift_code" readonly style="background: #f5f5f5;">
                        <input type="hidden" id="handover_shift_id" name="shift_id">
                    </div>
                    <div class="form-group">
                        <label for="handover_to_user_id">انقل إلى العامل</label>
                        <select id="handover_to_user_id" name="to_user_id" class="form-control" required>
                            <option value="">-- اختر العامل الجديد --</option>
                            @foreach($shifts as $shift)
                                @if($shift->supervisor)
                                    <option value="{{ $shift->supervisor->id }}">{{ $shift->supervisor->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="handover_stage_number">المرحلة</label>
                        <select id="handover_stage_number" name="stage_number" class="form-control" required>
                            <option value="">-- اختر المرحلة --</option>
                            <option value="1">المرحلة الأولى</option>
                            <option value="2">المرحلة الثانية</option>
                            <option value="3">المرحلة الثالثة</option>
                            <option value="4">المرحلة الرابعة</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="handover_notes">ملاحظات (اختياري)</label>
                        <textarea
                            id="handover_notes"
                            name="notes"
                            class="form-control"
                            rows="3"
                            placeholder="أضف ملاحظات عن النقل...">
                        </textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeHandoverModal()">إلغاء</button>
                    <button type="submit" class="btn btn-info" style="background: #17a2b8; color: white;">نقل الوردية</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-btn:hover {
            color: #333;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
        }

        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .btn-warning {
            background: #f39c12;
            color: white;
        }

        .btn-warning:hover {
            background: #e67e22;
        }

        .um-btn-warning {
            color: #f39c12 !important;
        }

        .um-btn-warning:hover {
            background: #f39c1220 !important;
        }

        .um-btn-info {
            color: #17a2b8 !important;
        }

        .um-btn-info:hover {
            background: #17a2b820 !important;
        }
    </style>

@endsection
