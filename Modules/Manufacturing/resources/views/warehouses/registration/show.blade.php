@extends('master')

@section('title', 'تفاصيل التسجيل')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style-material.css') }}">

<style>
    .action-btn.status {
        display: flex;
        align-items: center;
        color: rgb(0, 0, 0);
        border: none;
    }
    .action-btn.status:hover {
        color: white;
    }
</style>

@if (session('success'))
    <div class="um-alert-custom um-alert-success" role="alert" id="successMessage">
        <i class="feather icon-check-circle"></i>
        {{ session('success') }}
        <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
            <i class="feather icon-x"></i>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="um-alert-custom um-alert-error" role="alert" id="errorMessage">
        <i class="feather icon-alert-circle"></i>
        {{ session('error') }}
        <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
            <i class="feather icon-x"></i>
        </button>
    </div>
@endif

<div class="container">
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <div class="course-icon">
                    <i class="feather icon-box"></i>
                </div>
                <div class="header-info">
                    <h1>📦 #{{ $deliveryNote->note_number ?? $deliveryNote->id }}</h1>
                    <div class="badges">
                        <span class="badge badge-{{ $deliveryNote->registration_status === 'registered' ? 'success' : 'warning' }}">
                            @switch($deliveryNote->registration_status)
                                @case('not_registered')
                                    ⏳ معلقة
                                    @break
                                @case('registered')
                                    ✅ مسجلة
                                    @break
                                @case('in_production')
                                    🏭 في الإنتاج
                                    @break
                                @case('completed')
                                    ✔️ مكتملة
                                    @break
                                @default
                                    {{ $deliveryNote->registration_status }}
                            @endswitch
                        </span>
                        <span class="badge badge-info">{{ $deliveryNote->supplier->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('manufacturing.warehouse.registration.pending') }}" class="btn btn-back">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    العودة
                </a>

                @if ($deliveryNote->registration_status === 'registered' && $canMoveToProduction)
                    <a href="{{ route('manufacturing.warehouse.registration.transfer-form', $deliveryNote) }}" class="btn btn-success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3"></polyline>
                            <line x1="12" y1="12" x2="20" y2="7.5"></line>
                            <line x1="12" y1="12" x2="12" y2="21"></line>
                            <line x1="12" y1="12" x2="4" y2="7.5"></line>
                        </svg>
                        نقل للإنتاج
                    </a>
                @endif

                @if (!$deliveryNote->is_locked && $canEdit)
                    <a href="{{ route('manufacturing.warehouse.registration.create', $deliveryNote) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        تعديل
                    </a>
                @endif

                @if (!$deliveryNote->is_locked)
                    <button class="btn" type="button" data-bs-toggle="modal" data-bs-target="#lockModal" title="تقفيل الشحنة">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        تقفيل
                    </button>
                @else
                    <form action="{{ route('manufacturing.warehouse.registration.unlock', $deliveryNote) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-info" onclick="return confirm('هل تريد فتح القفل؟')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 9.2-1"></path>
                            </svg>
                            فتح القفل
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid">
        <!-- معلومات الشحنة -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                </div>
                <h3 class="card-title">📋 معلومات الشحنة</h3>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <div class="info-label">رقم الشحنة:</div>
                    <div class="info-value">#{{ $deliveryNote->note_number ?? $deliveryNote->id }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">المورد:</div>
                    <div class="info-value">{{ $deliveryNote->supplier->name ?? 'N/A' }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">حالة التسجيل:</div>
                    <div class="info-value">
                        <span class="badge badge-{{ $deliveryNote->registration_status === 'registered' ? 'success' : 'warning' }}">
                            {{ $deliveryNote->registration_status ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">تاريخ الوصول:</div>
                    <div class="info-value">{{ $deliveryNote->created_at->format('d/m/Y H:i') }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">الحالة الحالية:</div>
                    <div class="info-value">
                        @if ($deliveryNote->is_locked)
                            <span class="badge badge-danger">🔒 مقفلة</span>
                        @else
                            <span class="badge badge-success">🔓 مفتوحة</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- بيانات التسجيل -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </div>
                <h3 class="card-title">⚙️ بيانات التسجيل</h3>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <div class="info-label">الوزن الفعلي:</div>
                    <div class="info-value">
                        <span class="badge badge-info">{{ number_format($deliveryNote->actual_weight ?? 0, 2) }} كيلو</span>
                    </div>
                </div>

                @if ($deliveryNote->registeredBy)
                    <div class="info-item">
                        <div class="info-label">مسجل بواسطة:</div>
                        <div class="info-value">{{ $deliveryNote->registeredBy->name ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ التسجيل:</div>
                        <div class="info-value">{{ $deliveryNote->registered_at?->format('d/m/Y H:i') ?? 'N/A' }}</div>
                    </div>
                @else
                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value"><span class="badge badge-warning">لم يتم التسجيل بعد</span></div>
                    </div>
                @endif

                @if ($deliveryNote->registrationLogs && $deliveryNote->registrationLogs->count() > 0)
                    <div class="info-item">
                        <div class="info-label">الموقع:</div>
                        <div class="info-value">{{ $deliveryNote->registrationLogs->first()->location ?? 'N/A' }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- حالة التسوية -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <h3 class="card-title">🔄 حالة التسوية</h3>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <div class="info-label">حالة التسوية:</div>
                    <div class="info-value">
                        <span class="badge badge-{{ $deliveryNote->reconciliation_status === 'matched' ? 'success' : ($deliveryNote->reconciliation_status === 'discrepancy' ? 'warning' : 'info') }}">
                            {{ $deliveryNote->reconciliation_status ?? 'pending' }}
                        </span>
                    </div>
                </div>

                @if ($deliveryNote->purchase_invoice_id)
                    <div class="info-item">
                        <div class="info-label">الفاتورة المرتبطة:</div>
                        <div class="info-value">
                            <a href="{{ route('manufacturing.purchase-invoices.show', $deliveryNote->purchaseInvoice->id ?? '#') }}">
                                {{ $deliveryNote->purchaseInvoice->invoice_number ?? 'N/A' }}
                            </a>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الوزن من الفاتورة:</div>
                        <div class="info-value">
                            <span class="badge badge-primary">{{ number_format($deliveryNote->invoice_weight ?? 0, 2) }} كيلو</span>
                        </div>
                    </div>

                    @if (($deliveryNote->weight_discrepancy ?? 0) != 0)
                        <div class="info-item">
                            <div class="info-label">الفرق:</div>
                            <div class="info-value">
                                <span class="badge badge-{{ ($deliveryNote->weight_discrepancy ?? 0) > 0 ? 'danger' : 'success' }}">
                                    {{ ($deliveryNote->weight_discrepancy ?? 0) > 0 ? '+' : '' }}{{ number_format($deliveryNote->weight_discrepancy ?? 0, 2) }} كيلو
                                    ({{ number_format($deliveryNote->discrepancy_percentage ?? 0, 2) }}%)
                                </span>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value"><span class="badge badge-secondary">لم يتم ربط فاتورة بعد</span></div>
                    </div>
                @endif
            </div>
        </div>

        <!-- معلومات المستودع والنقل للإنتاج -->
        @if ($warehouseSummary)
            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">📦 إدارة المستودع والنقل</h3>
                </div>
                <div class="card-body">
                    <!-- الحالة الإجمالية -->
                    <div style="background: linear-gradient(135deg, {{ $warehouseSummary['status']['warehouse_status_color'] === 'success' ? '#27ae60' : ($warehouseSummary['status']['warehouse_status_color'] === 'warning' ? '#e74c3c' : '#3498db') }} 0%, {{ $warehouseSummary['status']['warehouse_status_color'] === 'success' ? '#229954' : ($warehouseSummary['status']['warehouse_status_color'] === 'warning' ? '#c0392b' : '#2980b9') }} 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <div style="font-size: 12px; opacity: 0.9; margin-bottom: 5px;">حالة البضاعة:</div>
                                <div style="font-size: 20px; font-weight: bold;">{{ $warehouseSummary['status']['warehouse_status_label'] }}</div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-size: 30px; font-weight: bold; margin-bottom: 5px;">{{ number_format($warehouseSummary['status']['transfer_percentage'], 1) }}%</div>
                                <div style="font-size: 12px; opacity: 0.9;">نسبة النقل</div>
                            </div>
                        </div>
                    </div>

                    <!-- الكميات -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div style="background: #f0f4f8; padding: 15px; border-radius: 8px; border-right: 4px solid #3498db;">
                            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">
                                <i class="fas fa-arrow-down"></i> الكمية المدخلة:
                            </div>
                            <div style="font-size: 18px; font-weight: bold; color: #3498db;">
                                {{ number_format($warehouseSummary['quantities']['warehouse_entry'], 2) }} كيلو
                            </div>
                        </div>

                        <div style="background: #f0f4f8; padding: 15px; border-radius: 8px; border-right: 4px solid #e74c3c;">
                            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">
                                <i class="fas fa-arrow-right"></i> المنقول للإنتاج:
                            </div>
                            <div style="font-size: 18px; font-weight: bold; color: #e74c3c;">
                                {{ number_format($warehouseSummary['quantities']['transferred_to_production'], 2) }} كيلو
                            </div>
                        </div>

                        <div style="background: #f0f4f8; padding: 15px; border-radius: 8px; border-right: 4px solid #27ae60;">
                            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">
                                <i class="fas fa-cube"></i> المتبقي:
                            </div>
                            <div style="font-size: 18px; font-weight: bold; color: #27ae60;">
                                {{ number_format($warehouseSummary['quantities']['remaining_in_warehouse'], 2) }} كيلو
                            </div>
                        </div>
                    </div>

                    <!-- شريط التقدم -->
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <label style="font-weight: 600; color: #2c3e50;">تقدم النقل للإنتاج:</label>
                            <span style="font-weight: 600; color: #3498db;">{{ number_format($warehouseSummary['status']['transfer_percentage'], 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 30px; border-radius: 4px;">
                            <div class="progress-bar" style="width: {{ $warehouseSummary['status']['transfer_percentage'] }}%; background: linear-gradient(90deg, #3498db 0%, #2980b9 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                {{ number_format($warehouseSummary['quantities']['transferred_to_production'], 1) }} كيلو
                            </div>
                        </div>
                    </div>

                    <!-- التواريخ -->
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <div style="font-weight: 600; color: #2c3e50; margin-bottom: 12px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
                            <i class="fas fa-clock"></i> سجل الحركات:
                        </div>

                        @if ($warehouseSummary['dates']['registered_to_warehouse'])
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #e9ecef;">
                                <div style="width: 10px; height: 10px; background: #3498db; border-radius: 50%;"></div>
                                <div style="flex: 1;">
                                    <div style="font-size: 12px; color: #999;">دخول المستودع:</div>
                                    <div style="font-weight: 600; color: #2c3e50;">{{ $warehouseSummary['dates']['registered_to_warehouse'] }}</div>
                                </div>
                                <div style="font-size: 11px; color: #999;">بواسطة: {{ $warehouseSummary['users']['registered_by'] ?? 'N/A' }}</div>
                            </div>
                        @endif

                        @if ($warehouseSummary['dates']['transferred_to_production'])
                            <div style="display: flex; align-items: center; gap: 10px; padding-bottom: 10px;">
                                <div style="width: 10px; height: 10px; background: #27ae60; border-radius: 50%;"></div>
                                <div style="flex: 1;">
                                    <div style="font-size: 12px; color: #999;">نقل للإنتاج:</div>
                                    <div style="font-weight: 600; color: #2c3e50;">{{ $warehouseSummary['dates']['transferred_to_production'] }}</div>
                                </div>
                                <div style="font-size: 11px; color: #999;">بواسطة: {{ $warehouseSummary['users']['transferred_by'] ?? 'N/A' }}</div>
                            </div>
                        @endif
                    </div>

                    <!-- الملاحظات -->
                    @if ($warehouseSummary['notes']['transfer_notes'])
                        <div style="background: #fff3cd; padding: 12px; border-radius: 4px; border-right: 4px solid #ffc107;">
                            <div style="font-weight: 600; color: #856404; margin-bottom: 5px;">
                                <i class="fas fa-sticky-note"></i> ملاحظات النقل:
                            </div>
                            <div style="color: #856404; font-size: 14px;">
                                {{ $warehouseSummary['notes']['transfer_notes'] }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>

    <!-- سجلات التسجيل -->
    @if ($deliveryNote->registrationLogs && $deliveryNote->registrationLogs->count() > 0)
        <div class="card" style="margin-top: 20px; margin-bottom: 20px;">
            <div class="card-header">
                <div class="card-icon primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h3 class="card-title">📚 سجلات التسجيل</h3>
            </div>
            <div class="card-body">
                <div class="operations-timeline">
                    @foreach ($deliveryNote->registrationLogs as $index => $log)
                        <div class="operation-item" style="padding-bottom: 20px; border-bottom: 1px solid #e9ecef; margin-bottom: 20px;">
                            @if($index === $deliveryNote->registrationLogs->count() - 1)
                                <style>
                                    .operation-item:last-child { border-bottom: none; }
                                </style>
                            @endif

                            <div class="operation-header" style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
                                <div style="flex: 1;">
                                    <div class="operation-description" style="margin-bottom: 8px;">
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                            <span class="badge" style="background-color: #27ae60; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                                                </svg>
                                                تسجيل
                                            </span>
                                            <strong style="color: #2c3e50; font-size: 14px;">تم تسجيل البضاعة</strong>
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 15px; font-size: 12px; color: #7f8c8d; flex-wrap: wrap;">
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                            <span><strong>{{ $log->registeredBy?->name ?? 'مستخدم محذوف' }}</strong></span>
                                        </div>

                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <span>{{ $log->registered_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</span>
                                        </div>

                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 16 16 12 12 8"></polyline>
                                            </svg>
                                            <span>{{ $log->registered_at?->diffForHumans() ?? 'N/A' }}</span>
                                        </div>

                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                <path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"></path>
                                                <path d="M12 5v7l5 3"></path>
                                            </svg>
                                            <code style="background: #f0f2f5; padding: 2px 6px; border-radius: 3px;">{{ $log->ip_address ?? 'N/A' }}</code>
                                        </div>
                                    </div>

                                    <div style="margin-top: 10px; padding: 8px; background: #f8f9fa; border-radius: 4px;">
                                        <small style="color: #555;">
                                            <strong>📍 الموقع:</strong> {{ $log->location ?? 'N/A' }}<br>
                                            <strong>⚖️ الوزن:</strong> {{ number_format($log->weight_recorded ?? 0, 2) }} كيلو
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- سجلات التسوية -->
    @if ($deliveryNote->reconciliationLogs && $deliveryNote->reconciliationLogs->count() > 0)
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <div class="card-icon primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h3 class="card-title">🔍 سجلات التسوية</h3>
            </div>
            <div class="card-body">
                <div class="operations-timeline">
                    @foreach ($deliveryNote->reconciliationLogs as $index => $log)
                        <div class="operation-item" style="padding-bottom: 20px; border-bottom: 1px solid #e9ecef; margin-bottom: 20px;">
                            @if($index === $deliveryNote->reconciliationLogs->count() - 1)
                                <style>
                                    .operation-item:last-child { border-bottom: none; }
                                </style>
                            @endif

                            <div class="operation-header" style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
                                <div style="flex: 1;">
                                    <div class="operation-description" style="margin-bottom: 8px;">
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                            @switch($log->action)
                                                @case('accepted')
                                                    <span class="badge" style="background-color: #27ae60; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                        ✅ قبول
                                                    </span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge" style="background-color: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                        ❌ رفض
                                                    </span>
                                                    @break
                                                @case('adjusted')
                                                    <span class="badge" style="background-color: #f39c12; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                        🔧 تعديل
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="badge" style="background-color: #95a5a6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                        {{ $log->action ?? 'N/A' }}
                                                    </span>
                                            @endswitch
                                            <strong style="color: #2c3e50; font-size: 14px;">تسوية البضاعة</strong>
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 15px; font-size: 12px; color: #7f8c8d; flex-wrap: wrap;">
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                            <span><strong>{{ $log->decidedBy?->name ?? 'مستخدم محذوف' }}</strong></span>
                                        </div>

                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <span>{{ $log->decided_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</span>
                                        </div>

                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 16 16 12 12 8"></polyline>
                                            </svg>
                                            <span>{{ $log->decided_at?->diffForHumans() ?? 'N/A' }}</span>
                                        </div>
                                    </div>

                                    <div style="margin-top: 10px; padding: 8px; background: #f8f9fa; border-radius: 4px;">
                                        <small style="color: #555;">
                                            <strong>📊 الفرق:</strong> {{ number_format($log->getDiscrepancyKg() ?? 0, 2) }} كيلو ({{ number_format($log->discrepancy_percentage ?? 0, 2) }}%)<br>
                                            <strong>💬 السبب:</strong> {{ $log->reason ?? 'N/A' }}<br>
                                            @if($log->comments)
                                                <strong>📝 ملاحظات:</strong> {{ $log->comments }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Modal للتقفيل -->
<div class="modal fade" id="lockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🔒 تقفيل الشحنة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('manufacturing.warehouse.registration.lock', $deliveryNote) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">السبب:</label>
                        <textarea name="lock_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning">تقفيل</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
