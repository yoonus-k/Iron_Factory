@extends('master')

@section('title', 'الورديات الحالية')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-clock"></i>
                الورديات الحالية
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>الورديات والعمال</span>
                <i class="feather icon-chevron-left"></i>
                <span>الورديات الحالية</span>
            </nav>
        </div>

    <!-- Messages -->
    @if (session('success'))
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">
            ❌ {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <strong>خطأ في البيانات:</strong>
            <ul style="margin: 8px 0 0 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

        <!-- Active Shifts Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-activity"></i>
                    الورديات النشطة حالياً
                </h4>
                <div class="um-card-actions">
                    <button class="um-btn um-btn-outline">
                        <i class="feather icon-refresh-cw"></i>
                        تحديث
                    </button>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث في الورديات...">
                        </div>
                        <div class="um-form-group">
                            <select name="shift_type" class="um-form-control">
                                <option value="">جميع أنواع الورديات</option>
                                <option value="morning">صباحية</option>
                                <option value="evening">مسائية</option>
                                <option value="night">ليلية</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <button type="reset" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                إعادة تعيين
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Active Shifts Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>رقم الوردية</th>
                            <th>التاريخ</th>
                            <th>نوع الوردية</th>
                            <th>المسؤول</th>
                            <th>وقت البدء</th>
                            <th>عدد العمال</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($currentShifts as $shift)
                        <tr>
                            <td><strong>{{ $shift->shift_code }}</strong></td>
                            <td>{{ $shift->shift_date->format('Y-m-d') }}</td>
                            <td>
                                <span class="um-badge um-badge-{{ $shift->shift_type == 'morning' ? 'info' : ($shift->shift_type == 'evening' ? 'warning' : 'danger') }}">
                                    {{ $shift->shift_type_name }}
                                </span>
                            </td>
                            <td>{{ $shift->supervisor->name ?? 'غير محدد' }}</td>
                            <td>{{ $shift->start_time }}</td>
                            <td>{{ $shift->total_workers }}</td>
                            <td>
                                <span class="um-badge um-badge-success">{{ $shift->status_name }}</span>
                            </td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        @if(auth()->user()->hasPermission(''))
                                        <a href="{{ route('manufacturing.shifts-workers.show', $shift->id) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض التفاصيل</span>
                                        </a>
                                        @endif
                                        @if(auth()->user()->hasPermission(''))
                                        <form action="{{ route('manufacturing.shifts-workers.complete', $shift->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="um-dropdown-item um-btn-edit" onclick="return confirm('هل أنت متأكد من إكمال هذه الوردية؟');">
                                                <i class="feather icon-check-circle"></i>
                                                <span>إنهاء الوردية</span>
                                            </button>
                                        </form>
                                        @endif
                                        @if(auth()->user()->hasPermission(''))
                                        <button type="button" class="um-dropdown-item um-btn-toggle" onclick="openSuspendModal({{ $shift->id }})">
                                            <i class="feather icon-pause-circle"></i>
                                            <span>تعليق الوردية</span>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                                <i class="feather icon-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                لا توجد ورديات نشطة حالياً
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            </div>
        </section>
    </div>

    <!-- Suspend Modal -->
    <div id="suspendModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>تعليق الوردية</h3>
                <button class="close-btn" onclick="closeSuspendModal()">×</button>
            </div>
            <form id="suspendForm" method="POST">
                @csrf
                @method('PATCH')
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
            border-color: #0052B3;
            box-shadow: 0 0 0 3px rgba(0, 82, 179, 0.1);
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
    </style>

    <script>
        function openSuspendModal(shiftId) {
            const form = document.getElementById('suspendForm');
            form.action = `/manufacturing/shifts-workers/${shiftId}/suspend`;
            document.getElementById('suspendModal').style.display = 'flex';
        }

        function closeSuspendModal() {
            document.getElementById('suspendModal').style.display = 'none';
            document.getElementById('suspension_reason').value = '';
        }

        // Close modal when clicking outside
        document.getElementById('suspendModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSuspendModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSuspendModal();
            }
        });
    </script>

@endsection


