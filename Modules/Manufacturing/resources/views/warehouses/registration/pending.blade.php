@extends('master')

@section('title', 'تسجيل البضاعة - المستودع')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title">
                        <i class="fas fa-box"></i> تسجيل البضاعة في المستودع
                    </h1>
                    <p class="text-muted mb-0">إدارة تسجيل الشحنات الواردة والتحكم في حركتها</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('manufacturing.warehouse.movements.index') }}"
                       class="btn btn-info btn-lg">
                        <i class="fas fa-exchange-alt"></i> سجل الحركات
                    </a>
                    <a href="{{ route('manufacturing.warehouses.reconciliation.link-invoice') }}"
                       class="btn btn-info btn-lg">
                        <i class="fas fa-link"></i> ربط فاتورة
                    </a>
                    <a href="{{ route('manufacturing.warehouses.reconciliation.index') }}"
                       class="btn btn-info btn-lg">
                        <i class="fas fa-balance-scale"></i> التسويات
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>❌ خطأ!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="stats-row">
                    <div class="stat-item pending-stat">
                        <div class="stat-icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">🔴 شحنات معلقة (بانتظار التسجيل)</span>
                            <span class="stat-number" style="color: #0051E5;">{{ $incomingUnregistered->total() ?? 0 }}</span>
                        </div>
                    </div>

                    <div class="stat-item registered-stat">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">🟢 شحنات مسجلة</span>
                            <span class="stat-number" style="color: #3E4651;">{{ $incomingRegistered->total() ?? 0 }}</span>
                        </div>
                    </div>

                    @if ($movedToProduction->count() > 0)
                    <div class="stat-item production-stat">
                        <div class="stat-icon">
                            <i class="fas fa-industry"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">🏭 منقولة للإنتاج</span>
                            <span class="stat-number" style="color: #0051E5;">{{ $movedToProduction->total() ?? 0 }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card" style="border: 1px solid #e9ecef; box-shadow: 0 2px 6px rgba(0, 81, 229, 0.08);">
                    <div class="card-header" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-bottom: 2px solid #dee2e6;">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0" style="color: #2c3e50; font-weight: 600;">
                                    <i class="fas fa-filter" style="color: #0051E5; margin-left: 8px;"></i> فلترة متقدمة
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('manufacturing.warehouse.registration.pending') }}" class="filter-form">
                            <div class="row align-items-end">
                                <!-- From Date -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" style="color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                                        <i class="fas fa-calendar-alt" style="color: #0051E5; margin-left: 5px;"></i> من التاريخ
                                    </label>
                                    <input type="date" name="from_date" class="form-control form-control-lg"
                                           value="{{ $appliedFilters['from_date'] ?? '' }}"
                                           style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;">
                                </div>

                                <!-- To Date -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" style="color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                                        <i class="fas fa-calendar-check" style="color: #0051E5; margin-left: 5px;"></i> إلى التاريخ
                                    </label>
                                    <input type="date" name="to_date" class="form-control form-control-lg"
                                           value="{{ $appliedFilters['to_date'] ?? '' }}"
                                           style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;">
                                </div>

                                <!-- Sort By -->
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" style="color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                                        <i class="fas fa-sort" style="color: #0051E5; margin-left: 5px;"></i> ترتيب حسب
                                    </label>
                                    <select name="sort_by" class="form-select form-control-lg"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;">
                                        <option value="date" {{ ($appliedFilters['sort_by'] ?? 'date') === 'date' ? 'selected' : '' }}>التاريخ</option>
                                        <option value="note_number" {{ ($appliedFilters['sort_by'] ?? 'date') === 'note_number' ? 'selected' : '' }}>رقم الأذن</option>
                                    </select>
                                </div>

                                <!-- Sort Order -->
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" style="color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                                        <i class="fas fa-arrow-up-down" style="color: #0051E5; margin-left: 5px;"></i> الترتيب
                                    </label>
                                    <select name="sort_order" class="form-select form-control-lg"
                                            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;">
                                        <option value="desc" {{ ($appliedFilters['sort_order'] ?? 'desc') === 'desc' ? 'selected' : '' }}>الأحدث أولاً</option>
                                        <option value="asc" {{ ($appliedFilters['sort_order'] ?? 'desc') === 'asc' ? 'selected' : '' }}>الأقدم أولاً</option>
                                    </select>
                                </div>

                                <!-- Action Buttons -->
                                <div class="col-md-2 mb-3">
                                    <div style="display: flex; gap: 8px;">
                                        <button type="submit" class="btn btn-primary btn-lg"
                                                style="background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%); border: none; padding: 8px 12px; flex: 1; border-radius: 6px; font-weight: 600;">
                                            <i class="fas fa-search"></i> بحث
                                        </button>
                                        <a href="{{ route('manufacturing.warehouse.registration.pending') }}" class="btn btn-secondary btn-lg"
                                           style="background-color: #e9ecef; border: none; padding: 8px 12px; border-radius: 6px; font-weight: 600; color: #2c3e50; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Info -->
                            @if (($appliedFilters['from_date'] ?? null) || ($appliedFilters['to_date'] ?? null))
                                <div style="margin-top: 12px; padding: 10px 12px; background-color: #e8f0ff; border-radius: 6px; border-right: 4px solid #0051E5;">
                                    <small style="color: #0051E5; font-weight: 500;">
                                        <i class="fas fa-info-circle"></i>
                                        تم تطبيق الفلترة:
                                        @if ($appliedFilters['from_date'])
                                            من <strong>{{ date('Y-m-d', strtotime($appliedFilters['from_date'])) }}</strong>
                                        @endif
                                        @if ($appliedFilters['to_date'])
                                            إلى <strong>{{ date('Y-m-d', strtotime($appliedFilters['to_date'])) }}</strong>
                                        @endif
                                    </small>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unregistered Shipments -->

    <div class="row">
        <!-- Unregistered Shipments Column -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header" style="background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%); color: white;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0" style="color: white;">🔴 شحنات معلقة (تحتاج تسجيل)</h3>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-white" style="color: #0051E5; font-size: 14px; padding: 6px 12px;">{{ $incomingUnregistered->total() ?? 0 }} شحنة</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($incomingUnregistered->count() > 0)
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-arrow-down"></i> <strong>الخطوة 1:</strong> اختر شحنة من القائمة أدناه
                        </div>

                        <div class="operations-timeline">
                            @foreach ($incomingUnregistered as $index => $shipment)
                                <div class="operation-item" style="padding-bottom: 20px; border-bottom: 1px solid #e9ecef; margin-bottom: 20px;">
                                    @if($index === count($incomingUnregistered) - 1)
                                        <style>
                                            .operation-item:last-child { border-bottom: none; }
                                        </style>
                                    @endif

                                    <!-- رأس العملية -->
                                    <div class="operation-header" style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
                                        <div style="flex: 1;">
                                            <!-- النوع والوصف -->
                                            <div class="operation-description" style="margin-bottom: 8px;">
                                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                                    <!-- Badge للحالة -->
                                    <span class="badge" style="background-color: #0051E5; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        معلقة
                                    </span>                                                    <!-- تحذير التكرار إن وجد -->
                                                    @if ($shipment->registration_attempts > 0)
                                                        <span class="badge" style="background-color: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                            ⚠️ محاولة {{ $shipment->registration_attempts + 1 }}
                                                        </span>
                                                    @endif

                                                    <!-- رقم الشحنة -->
                                                    <strong style="color: #2c3e50; font-size: 14px;">
                                                        #{{ $shipment->note_number ?? $shipment->id }}
                                                    </strong>
                                                </div>
                                            </div>

                                            <!-- تفاصيل الشحنة -->
                                            <div style="display: flex; gap: 15px; font-size: 12px; color: #7f8c8d; flex-wrap: wrap;">
                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                        <circle cx="12" cy="7" r="4"></circle>
                                                    </svg>
                                                    <span><strong>{{ $shipment->supplier->name ?? 'N/A' }}</strong></span>
                                                </div>

                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 6 12 12 16 14"></polyline>
                                                    </svg>
                                                    <span>{{ $shipment->created_at->format('Y-m-d H:i:s') }}</span>
                                                </div>

                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 16 16 12 12 8"></polyline>
                                                        <polyline points="8 12 12 16 12 8"></polyline>
                                                    </svg>
                                                    <span style="color: #e74c3c; font-weight: 600;">{{ $shipment->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- زر الإجراء -->
                                        <div style="margin-right: 10px;">
                                            <a href="{{ route('manufacturing.warehouse.registration.create', $shipment) }}"
                                                style="background-color: #0051E5; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/>
                                                </svg>
                                                تسجيل الآن
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div style="margin-top: 20px;">
                            @include('manufacturing::components.advanced-pagination', ['paginator' => $incomingUnregistered])
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px 20px; color: #27ae60;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 48px; height: 48px; margin: 0 auto 15px; opacity: 0.5;">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <p style="margin: 0; font-size: 14px; font-weight: 600;">
                                ✅ لا توجد شحنات معلقة! جميع الشحنات مسجلة بنجاح.
                            </p>
                            <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">
                                اذهب لقسم الشحنات المسجلة لعرض تفاصيل أو نقل للإنتاج
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Registered Shipments Column -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header" style="background: linear-gradient(135deg, #3E4651 0%, #2C3339 100%); color: white;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0" style="color: white;">🟢 الشحنات المسجلة</h3>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-white" style="color: #3E4651; font-size: 14px; padding: 6px 12px;">{{ $incomingRegistered->total() ?? 0 }} شحنة</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($incomingRegistered->count() > 0 || $movedToProduction->count() > 0)
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-arrow-right"></i> <strong>الخطوة 2:</strong> عرض التفاصيل أو نقل للإنتاج
                        </div>

                        <!-- Regular Registered Shipments -->
                        @foreach ($incomingRegistered as $index => $shipment)
                            @php
                                // استخدام الكمية المسجلة لهذه الأذن على حدة
                                $availableQuantity = $shipment->quantity_remaining ?? ($shipment->quantity ?? 0);
                                $isMovedToProduction = $availableQuantity == 0;
                                $productionStatus = $shipment->registration_status === 'in_production' || $isMovedToProduction;
                            @endphp
                            <div class="operation-item" style="padding-bottom: 20px; border-bottom: 1px solid #e9ecef; margin-bottom: 20px;">
                                @if($index === count($incomingRegistered) - 1 && $movedToProduction->count() == 0)
                                    <style>
                                        .operation-item:last-child { border-bottom: none; }
                                    </style>
                                @endif

                                <!-- رأس العملية -->
                                <div class="operation-header" style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
                                    <div style="flex: 1;">
                                        <!-- النوع والوصف -->
                                        <div class="operation-description" style="margin-bottom: 8px;">
                                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                                <!-- Badge للحالة -->
                                                @if($productionStatus)
                                                    <span class="badge" style="background-color: #0051E5; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                                        </svg>
                                                        منقولة للإنتاج
                                                    </span>
                                                @else
                                                    <span class="badge" style="background-color: #3E4651; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                        </svg>
                                                        في المستودع
                                                    </span>
                                                @endif

                                                <!-- معلومات محاولات التسجيل -->
                                                @if ($shipment->registration_attempts > 0)
                                                    <span class="badge" style="background-color: #E74C3C; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                        ℹ️ {{ $shipment->registration_attempts }} محاولة
                                                    </span>
                                                @endif

                                                <!-- رقم الشحنة -->
                                                <strong style="color: #2c3e50; font-size: 14px;">
                                                    #{{ $shipment->note_number ?? $shipment->id }}
                                                </strong>

                                                <!-- الكمية المتبقية -->
                                                @if($productionStatus)
                                                    <span class="badge" style="background-color: #95a5a6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                                        </svg>
                                                        الكمية: 0 كجم (مكتملة)
                                                    </span>
                                                @else
                                                    <span class="badge" style="background-color: #004B87; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                                        </svg>
                                                        متاح: {{ number_format($availableQuantity, 2) }} كجم
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- تفاصيل الشحنة -->
                                        <div style="display: flex; gap: 15px; font-size: 12px; color: #7f8c8d; flex-wrap: wrap;">
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="12" cy="7" r="4"></circle>
                                                </svg>
                                                <span><strong>{{ $shipment->supplier->name ?? 'N/A' }}</strong></span>
                                            </div>

                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="8.5" cy="7" r="4"></circle>
                                                    <polyline points="17 11 19 13 23 9"></polyline>
                                                </svg>
                                                <span>{{ $shipment->registeredBy->name ?? 'N/A' }}</span>
                                            </div>

                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                <span>{{ $shipment->registered_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</span>
                                            </div>

                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 16 16 12 12 8"></polyline>
                                                    <polyline points="8 12 12 16 12 8"></polyline>
                                                </svg>
                                                <span>{{ $shipment->registered_at?->diffForHumans() ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- زر الإجراء -->
                                    <div style="margin-right: 10px;">
                                        <a href="{{ route('manufacturing.warehouse.registration.show', $shipment) }}"
                                            style="background-color: #0051E5; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            عرض
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Moved to Production Shipments (within Registered Shipments card) -->
                        @foreach ($movedToProduction as $index => $shipment)
                            @php
                                // استخدام الكمية المسجلة لهذه الأذن على حدة
                                $availableQuantity = $shipment->quantity_remaining ?? ($shipment->quantity ?? 0);
                                $isMovedToProduction = $availableQuantity == 0;
                                $productionStatus = $shipment->registration_status === 'in_production' || $isMovedToProduction;
                            @endphp
                            <div class="operation-item" style="padding-bottom: 20px; border-bottom: 1px solid #e9ecef; margin-bottom: 20px;">
                                @if($index === count($movedToProduction) - 1)
                                    <style>
                                        .operation-item:last-child { border-bottom: none; }
                                    </style>
                                @endif

                                <!-- رأس العملية -->
                                <div class="operation-header" style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
                                    <div style="flex: 1;">
                                        <!-- النوع والوصف -->
                                        <div class="operation-description" style="margin-bottom: 8px;">
                                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                                <!-- Badge للحالة - Different styling for moved to production -->
                                                <span class="badge" style="background-color: #0051E5; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                    <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                                    </svg>
                                                    منقولة للإنتاج
                                                </span>

                                                <!-- معلومات محاولات التسجيل -->
                                                @if ($shipment->registration_attempts > 0)
                                                    <span class="badge" style="background-color: #0051E5; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                        ℹ️ {{ $shipment->registration_attempts }} محاولة
                                                    </span>
                                                @endif

                                                <!-- رقم الشحنة -->
                                                <strong style="color: #2c3e50; font-size: 14px;">
                                                    #{{ $shipment->note_number ?? $shipment->id }}
                                                </strong>

                                                <!-- الكمية المتبقية -->
                                                <span class="badge" style="background-color: #95a5a6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                    <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                                    </svg>
                                                    @if($isMovedToProduction)
                                                        الكمية: 0 كجم (مكتملة)
                                                    @else
                                                        متاح: {{ number_format($availableQuantity, 2) }} كجم
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        <!-- تفاصيل الشحنة -->
                                        <div style="display: flex; gap: 15px; font-size: 12px; color: #7f8c8d; flex-wrap: wrap;">
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="12" cy="7" r="4"></circle>
                                                </svg>
                                                <span><strong>{{ $shipment->supplier->name ?? 'N/A' }}</strong></span>
                                            </div>

                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="8.5" cy="7" r="4"></circle>
                                                    <polyline points="17 11 19 13 23 9"></polyline>
                                                </svg>
                                                <span>{{ $shipment->registeredBy->name ?? 'N/A' }}</span>
                                            </div>

                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                <span>{{ $shipment->registered_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</span>
                                            </div>

                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 16 16 12 12 8"></polyline>
                                                    <polyline points="8 12 12 16 12 8"></polyline>
                                                </svg>
                                                <span>{{ $shipment->registered_at?->diffForHumans() ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- زر الإجراء -->
                                    <div style="margin-right: 10px;">
                                        <a href="{{ route('manufacturing.warehouse.registration.show', $shipment) }}"
                                            style="background-color: #0051E5; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            عرض
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>

                        <!-- Pagination -->
                        <div style="margin-top: 20px;">
                            @include('manufacturing::components.advanced-pagination', ['paginator' => $incomingRegistered])
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px 20px; color: #95a5a6;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 48px; height: 48px; margin: 0 auto 15px; opacity: 0.5;">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <p style="margin: 0; font-size: 14px;">
                                ℹ️ لا توجد شحنات مسجلة حتى الآن. ابدأ بتسجيل الشحنات من القائمة اليسرى.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Stats Row */
        .stats-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .stat-item {
            flex: 1;
            min-width: 280px;
            background: white;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0, 81, 229, 0.1);
            border-left: 4px solid #0051E5;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 81, 229, 0.15);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: linear-gradient(135deg, #e8f0ff 0%, #d0e1ff 100%);
            color: #0051E5;
            flex-shrink: 0;
        }

        .stat-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: #999;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #0051E5;
            line-height: 1;
        }

        /* Filter Form Styles */
        .filter-form {
            padding: 0;
        }

        .filter-form .form-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .filter-form .form-control,
        .filter-form .form-select {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .filter-form .form-control:focus,
        .filter-form .form-select:focus {
            border-color: #0051E5;
            box-shadow: 0 0 0 3px rgba(0, 81, 229, 0.1);
            background-color: white;
        }

        .filter-form .form-control::placeholder {
            color: #adb5bd;
        }

        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(0, 81, 229, 0.3);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
            color: #2c3e50;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background-color: #dee2e6;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }

        .card-header {
            border-bottom: 2px solid rgba(0, 0, 0, 0.1);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #3E4651 0%, #2C3339 100%);
        }

        .border-left-danger {
            border-left: 4px solid #0051E5;
        }

        .border-left-success {
            border-left: 4px solid #3E4651;
        }

        .border-left-info {
            border-left: 4px solid #0051E5;
        }

        .text-danger {
            color: #0051E5 !important;
        }

        .text-success {
            color: #3E4651 !important;
        }

        .text-info {
            color: #0051E5 !important;
        }

        .bg-warning {
            background-color: #e8f0ff !important;
            color: #0051E5 !important;
        }

        .badge-danger {
            background-color: #0051E5 !important;
            color: white !important;
        }

        .bg-success {
            background-color: #3E4651 !important;
            color: white !important;
        }

        .bg-info {
            background-color: #e8f0ff !important;
            color: #0051E5 !important;
        }

        .bg-danger {
            background-color: #ff6b6b !important;
            color: white !important;
        }

        .btn-info {
            background-color: #0051E5;
            border-color: #0051E5;
            color: white;
        }

        .btn-info:hover {
            background-color: #003FA0;
            border-color: #003FA0;
            color: white;
        }

        .btn-success {
            background-color: #0051E5;
            border-color: #0051E5;
            color: white;
        }

        .btn-success:hover {
            background-color: #003FA0;
            border-color: #003FA0;
            color: white;
        }

        .btn-warning {
            background-color: #0051E5;
            border-color: #0051E5;
            color: white;
        }

        .btn-warning:hover {
            background-color: #003FA0;
            border-color: #003FA0;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table thead th {
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem 0.75rem;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .badge {
            font-weight: 500;
        }

        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .table-responsive {
            border-radius: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .filter-form .row {
                gap: 0;
            }

            .filter-form .col-md-2,
            .filter-form .col-md-3 {
                margin-bottom: 15px;
            }

            .stat-item {
                min-width: 200px;
            }
        }
    </style>
@endsection
