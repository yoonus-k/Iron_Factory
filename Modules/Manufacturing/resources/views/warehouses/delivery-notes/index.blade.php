@extends('master')

@section('title', 'إدارة أذون التسليم')

@section('content')
    <style>
        /* Pagination Styling */
        .um-pagination-section {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px 0;
            border-top: 1px solid #e9ecef;
        }

        .um-pagination-info {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }

        /* Bootstrap Pagination Custom Styling */
        .pagination {
            margin: 0;
            gap: 5px;
            display: flex;
            flex-wrap: wrap;
        }

        .pagination .page-link {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            color: #3498db;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 500;
            background-color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 36px;
            text-align: center;
            cursor: pointer;
        }

        .pagination .page-link:hover {
            background-color: #f0f2f5;
            border-color: #3498db;
            color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.15);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-color: #2980b9;
            color: white;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            border-color: #dee2e6;
            background-color: #f8f9fa;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .pagination .page-item.disabled .page-link:hover {
            transform: none;
            box-shadow: none;
        }

        @media (max-width: 768px) {
            .um-pagination-section {
                flex-direction: column;
                align-items: stretch;
            }

            .um-pagination-info {
                text-align: center;
            }

            .pagination {
                justify-content: center;
            }

            .pagination .page-link {
                padding: 6px 10px;
                font-size: 12px;
                min-width: 32px;
            }
        }
    </style>

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-file-text"></i>
                إدارة أذون التسليم
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>المستودع</span>
                <i class="feather icon-chevron-left"></i>
                <span>أذون التسليم</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        @if (session('success'))
            <div class="um-alert-custom um-alert-success" role="alert">
                <i class="feather icon-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="um-alert-custom um-alert-error" role="alert">
                <i class="feather icon-x-circle"></i>
                {{ session('error') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        <!-- Info Alert -->
        <div class="alert alert-info" style="border-right: 4px solid #3498db; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg style="width: 24px; height: 24px; min-width: 24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <div>
                    <strong>📋 نظام أذون التسليم:</strong>
                    <span style="display: block; margin-top: 5px; color: #666;">
                        ✅ <strong>الواردة (🔽):</strong> البضاعة القادمة من الموردين للمستودع
                        &nbsp;|&nbsp;
                        ✅ <strong>الصادرة (🔼):</strong> البضاعة الخارجة من المستودع للإنتاج أو العملاء
                        &nbsp;|&nbsp;
                        💡 جميع الإذونات تظهر في سجل العمليات
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    قائمة أذون التسليم (الواردة والصادرة)
                </h4>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('manufacturing.warehouse.registration.pending') }}" class="um-btn um-btn-primary">
                        <i class="feather icon-package"></i>
                        تسجيل البضاعة
                    </a>
                    <a href="{{ route('manufacturing.delivery-notes.create') }}" class="um-btn um-btn-primary">
                        <i class="feather icon-plus"></i>
                        إضافة أذن تسليم جديد
                    </a>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET" action="{{ route('manufacturing.delivery-notes.index') }}">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="text" name="search" class="um-form-control" placeholder="البحث في أذون التسليم..." value="{{ request('search') }}">
                        </div>
                        <div class="um-form-group">
                            <input type="text" name="delivery_number" class="um-form-control" placeholder="رقم الأذن..." value="{{ request('delivery_number') }}">
                        </div>
                        <div class="um-form-group">
                            <select name="type" class="um-form-control">
                                <option value="">جميع الأنواع</option>
                                <option value="incoming" {{ request('type') == 'incoming' ? 'selected' : '' }}>🔽 واردة</option>
                                <option value="outgoing" {{ request('type') == 'outgoing' ? 'selected' : '' }}>🔼 صادرة</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <select name="status" class="um-form-control">
                                <option value="">جميع الحالات</option>
                                <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>مستقبل</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            </select>
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-search"></i>
                                بحث
                            </button>
                            <a href="{{ route('manufacturing.delivery-notes.index') }}" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>رقم الأذن</th>
                            <th>النوع</th>
                            <th>تاريخ التسليم</th>
                            <th>المورد / الوجهة</th>
                            <th>الوزن</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveryNotes as $deliveryNote)
                        <tr>
                            <td>{{ $deliveryNote->note_number }}</td>
                            <td>
                                @if($deliveryNote->type === 'incoming')
                                    <span class="um-badge" style="background: #d4edda; color: #155724;">🔽 واردة</span>
                                @else
                                    <span class="um-badge" style="background: #f8d7da; color: #721c24;">🔼 صادرة</span>
                                @endif
                            </td>
                            <td>{{ $deliveryNote->delivery_date->format('Y-m-d') }}</td>
                            <td>
                                @if($deliveryNote->type === 'incoming')
                                    {{ $deliveryNote->supplier->name ?? 'غير محدد' }}
                                @else
                                    {{ $deliveryNote->destination->warehouse_name ?? 'غير محدد' }}
                                @endif
                            </td>
                            <td>{{ $deliveryNote->delivered_weight ?? '-' }} </td>
                            <td><span class="um-badge um-badge-success">{{ $deliveryNote->status ?? 'معلق' }}</span></td>
                            <td>
                                <div class="um-dropdown">
                                    <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="um-dropdown-menu">
                                        <a href="{{ route('manufacturing.delivery-notes.show', $deliveryNote->id) }}" class="um-dropdown-item um-btn-view">
                                            <i class="feather icon-eye"></i>
                                            <span>عرض</span>
                                        </a>
                                        <a href="{{ route('manufacturing.delivery-notes.edit', $deliveryNote->id) }}" class="um-dropdown-item um-btn-edit">
                                            <i class="feather icon-edit-2"></i>
                                            <span>تعديل</span>
                                        </a>
                                        <form action="{{ route('manufacturing.delivery-notes.destroy', $deliveryNote->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="um-dropdown-item um-btn-delete" title="حذف" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                <i class="feather icon-trash-2"></i>
                                                <span>حذف</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">لا توجد أذون تسليم</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile View -->
            <div class="um-mobile-view">
                @forelse($deliveryNotes as $deliveryNote)
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <h5>{{ $deliveryNote->note_number }}</h5>
                            @if($deliveryNote->type === 'incoming')
                                <span class="um-badge" style="background: #d4edda; color: #155724;">🔽 واردة</span>
                            @else
                                <span class="um-badge" style="background: #f8d7da; color: #721c24;">🔼 صادرة</span>
                            @endif
                        </div>
                        <span class="um-badge um-badge-success">{{ $deliveryNote->status ?? 'معلق' }}</span>
                    </div>
                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span>التاريخ:</span>
                            <span>{{ $deliveryNote->delivery_date->format('Y-m-d') }}</span>
                        </div>
                        <div class="um-info-row">
                            <span>النوع:</span>
                            <span>{{ $deliveryNote->type === 'incoming' ? 'واردة من مورد' : 'صادرة للإنتاج/عميل' }}</span>
                        </div>
                        <div class="um-info-row">
                            <span>{{ $deliveryNote->type === 'incoming' ? 'المورد:' : 'الوجهة:' }}</span>
                            <span>
                                @if($deliveryNote->type === 'incoming')
                                    {{ $deliveryNote->supplier->name ?? 'غير محدد' }}
                                @else
                                    {{ $deliveryNote->destination->warehouse_name ?? 'غير محدد' }}
                                @endif
                            </span>
                        </div>
                        <div class="um-info-row">
                            <span>الوزن:</span>
                            <span>{{ $deliveryNote->delivered_weight ?? '-' }} كجم</span>
                        </div>
                    </div>
                    <div class="um-category-card-footer">
                        <div class="um-dropdown">
                            <button class="um-btn-action um-btn-dropdown" title="الإجراءات">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="um-dropdown-menu">
                                <a href="{{ route('manufacturing.delivery-notes.show', $deliveryNote->id) }}" class="um-dropdown-item um-btn-view">
                                    <i class="feather icon-eye"></i>
                                    <span>عرض</span>
                                </a>
                                <a href="{{ route('manufacturing.delivery-notes.edit', $deliveryNote->id) }}" class="um-dropdown-item um-btn-edit">
                                    <i class="feather icon-edit-2"></i>
                                    <span>تعديل</span>
                                </a>
                                <form action="{{ route('manufacturing.delivery-notes.destroy', $deliveryNote->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="um-dropdown-item um-btn-delete" title="حذف" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                        <i class="feather icon-trash-2"></i>
                                        <span>حذف</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center">لا توجد أذون تسليم</div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($deliveryNotes->hasPages())
                <div class="um-pagination-section">
                    <div>
                        <p class="um-pagination-info">
                            عرض {{ $deliveryNotes->firstItem() ?? 0 }} إلى {{ $deliveryNotes->lastItem() ?? 0 }} من أصل
                            {{ $deliveryNotes->total() }} أذن تسليم
                        </p>
                    </div>
                    <div>
                        {{ $deliveryNotes->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
    </script>
@endsection
