@extends('master')

@section('title', 'تفاصيل المادة')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style-material.css') }}">
    <style>
        .action-btn.status {
            display: flex;
            align-items: center;
            color: #0066cc;
            border: none;
        }
        .action-btn.status:hover {
            color: #004499;
        }
        .dropdown-menu .dropdown-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
        }
        .dropdown-menu .dropdown-item.active {
            background-color: #0066cc;
            color: white;
        }
        .dropdown-menu .dropdown-item .badge {
            margin-right: 8px;
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
                        <i class="feather icon-package"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $material->name_ar }} ({{ $material->name_en }})</h1>
                        <div class="badges">
                            <span class="badge badge-{{ $material->status == 'available' ? 'success' : 'warning' }}">
                                {{ $material->status == 'available' ? 'متوفر' : 'قيد الاستخدام' }}
                            </span>
                            <span class="badge badge-info">{{ $material->getCategoryLabel() }}</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.warehouse-products.transactions', $material->id) }}" class="btn btn-info" title="عرض حركات المستودع">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                            <polyline points="17 6 23 6 23 12"></polyline>
                        </svg>
                        الحركات
                    </a>
                    <a href="{{ route('manufacturing.warehouse-products.edit', $material->id) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        تعديل
                    </a>

                    <!-- تغيير الحالة في الـ Header -->
                    <div class="um-dropdown">
                        <button class="btn " type="button" data-bs-toggle="dropdown" title="تغيير حالة المادة">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" >
                                <path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"></path>
                                <path d="M12 5v7l5 3"></path>
                            </svg>
                            الحالة
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <form method="POST" action="{{ route('manufacturing.warehouse-products.change-status', $material->id) }}" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="available">
                                    <button type="submit" class="dropdown-item {{ $material->status == 'available' ? 'active' : '' }}">
                                        <span class="badge badge-success me-2">●</span>
                                        متوفر
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('manufacturing.warehouse-products.change-status', $material->id) }}" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="in_use">
                                    <button type="submit" class="dropdown-item {{ $material->status == 'in_use' ? 'active' : '' }}">
                                        <span class="badge badge-warning me-2">●</span>
                                        قيد الاستخدام
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('manufacturing.warehouse-products.change-status', $material->id) }}" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="consumed">
                                    <button type="submit" class="dropdown-item {{ $material->status == 'consumed' ? 'active' : '' }}">
                                        <span class="badge badge-danger me-2">●</span>
                                        مستهلك
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('manufacturing.warehouse-products.change-status', $material->id) }}" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="expired">
                                    <button type="submit" class="dropdown-item {{ $material->status == 'expired' ? 'active' : '' }}">
                                        <span class="badge badge-secondary me-2">●</span>
                                        منتهي الصلاحية
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('manufacturing.warehouse-products.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        العودة
                    </a>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المادة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">رمز المادة:</div>
                        <div class="info-value">{{ $material->barcode }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">اسم المادة:</div>
                        <div class="info-value">{{ $material->name_ar }}</div>
                    </div>

                    @if($material->name_en)
                    <div class="info-item">
                        <div class="info-label">Material Name:</div>
                        <div class="info-value">{{ $material->name_en }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">إجمالي الوزن الأصلي:</div>
                        <div class="info-value">
                            {{ $material->materialDetails->sum('original_weight') }}
                            {{ $material->materialDetails->first()?->getUnitName() ?? 'وحدة' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">إجمالي الوزن المتبقي:</div>
                        <div class="info-value">
                            {{ $material->materialDetails->sum('remaining_weight') }}
                            {{ $material->materialDetails->first()?->getUnitName() ?? 'وحدة' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value">
                            @if ($material->status === 'available')
                                <span class="badge badge-success">متوفر</span>
                            @elseif ($material->status === 'in_use')
                                <span class="badge badge-warning">قيد الاستخدام</span>
                            @elseif ($material->status === 'consumed')
                                <span class="badge badge-danger">مستهلك</span>
                            @else
                                <span class="badge badge-secondary">منتهي الصلاحية</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">رقم مذكرة التسليم:</div>
                        <div class="info-value">{{ $material->delivery_note_number ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات إضافية</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">معرف الفاتورة:</div>
                        <div class="info-value">{{ $material->purchaseInvoice->invoice_number ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الصنع:</div>
                        <div class="info-value">{{ $material->manufacture_date?->format('Y-m-d') ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الصلاحية:</div>
                        <div class="info-value">
                            @if ($material->expiry_date)
                                @if ($material->isExpired())
                                    <span class="badge badge-danger">منتهي</span> {{ $material->expiry_date->format('Y-m-d') }}
                                @elseif ($material->isExpiringSoon())
                                    <span class="badge badge-warning">قريب الانتهاء</span> {{ $material->expiry_date->format('Y-m-d') }}
                                @else
                                    {{ $material->expiry_date->format('Y-m-d') }}
                                @endif
                            @else
                                N/A
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">موقع التخزين:</div>
                        <div class="info-value">
                            @if($material->shelf_location)
                                {{ $material->shelf_location }}
                            @elseif($material->shelf_location_en)
                                {{ $material->shelf_location_en }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>

                    @if($material->shelf_location && $material->shelf_location_en)
                    <div class="info-item">
                        <div class="info-label">Shelf Location:</div>
                        <div class="info-value">{{ $material->shelf_location_en }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">رقم الدفعة:</div>
                        <div class="info-value">{{ $material->batch_number ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تم الإدخال بواسطة:</div>
                        <div class="info-value">{{ $material->creator->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المستودع</h3>
                </div>
                <div class="card-body">
                    @php
                        $warehouseDetails = $material->materialDetails->groupBy('warehouse_id');
                    @endphp

                    @if($warehouseDetails->isNotEmpty())
                        <div class="warehouses-list">
                            @foreach($warehouseDetails as $warehouseId => $details)
                                @php
                                    $warehouse = $details->first()->warehouse;
                                    $totalQuantity = $details->sum('remaining_weight');
                                    $unit = $details->first()->unit?->unit_name ?? 'وحدة';
                                    $isEmpty = $totalQuantity <= 0;
                                @endphp
                                <div class="warehouse-item {{ $isEmpty ? 'empty' : '' }}">
                                    <div class="warehouse-item-header">
                                        <div class="warehouse-item-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                            </svg>
                                        </div>
                                        <h4 class="warehouse-item-name">{{ $warehouse->warehouse_name }}</h4>
                                    </div>
                                    <div class="warehouse-item-body">
                                        <div class="warehouse-quantity">
                                            <span class="warehouse-quantity-value">{{ number_format($totalQuantity, 2) }}</span>
                                            <span class="warehouse-quantity-unit">{{ $unit }}</span>
                                        </div>
                                        <div class="warehouse-status">
                                            <span class="warehouse-status-dot"></span>
                                            <span>{{ $isEmpty ? 'فارغ' : 'متوفر' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="text-align: center; color: #999; padding: 20px 0;">لا توجد كميات في المستودعات</p>
                    @endif

                    <div class="add-quantity-btn-wrapper">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuantityModal">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            إضافة كمية جديدة
                        </button>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">سجل العمليات</h3>
                </div>
                <div class="card-body">
                    @php
                        $operationLogs = $material->operationLogs()->orderBy('created_at', 'desc')->get();
                    @endphp

                    @if($operationLogs->isNotEmpty())
                        <div class="operations-timeline">
                            @foreach($operationLogs as $index => $log)
                                <div class="operation-item" style="padding-bottom: 20px; border-bottom: 1px solid #e9ecef; margin-bottom: 20px;">
                                    @if($index === count($operationLogs) - 1)
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
                                                    <!-- Badge للنوع -->
                                                    @switch($log->action)
                                                        @case('create')
                                                            <span class="badge" style="background-color: #27ae60; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                                <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                                                                </svg>
                                                                إنشاء
                                                            </span>
                                                            @break
                                                        @case('update')
                                                            <span class="badge" style="background-color: #3498db; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                                <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/>
                                                                </svg>
                                                                تعديل
                                                            </span>
                                                            @break
                                                        @case('delete')
                                                            <span class="badge" style="background-color: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                                <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-9l-1 1H5v2h14V4z"/>
                                                                </svg>
                                                                حذف
                                                            </span>
                                                            @break
                                                        @case('add_quantity')
                                                            <span class="badge" style="background-color: #f39c12; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                                <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                                                                </svg>
                                                                إضافة كمية
                                                            </span>
                                                            @break
                                                        @default
                                                            <span class="badge" style="background-color: #95a5a6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                                {{ $log->action_en ?? $log->action }}
                                                            </span>
                                                    @endswitch

                                                    <!-- الوصف -->
                                                    <strong style="color: #2c3e50; font-size: 14px;">{{ $log->description }}</strong>
                                                </div>
                                            </div>

                                            <!-- المستخدم والتاريخ -->
                                            <div style="display: flex; gap: 15px; font-size: 12px; color: #7f8c8d; flex-wrap: wrap;">
                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                        <circle cx="12" cy="7" r="4"></circle>
                                                    </svg>
                                                    <span><strong>{{ $log->user->name ?? 'مستخدم محذوف' }}</strong></span>
                                                </div>

                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 6 12 12 16 14"></polyline>
                                                    </svg>
                                                    <span>{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                                                </div>

                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 16 16 12 12 8"></polyline>
                                                        <polyline points="8 12 12 16 12 8"></polyline>
                                                    </svg>
                                                    <span>{{ $log->created_at->diffForHumans() }}</span>
                                                </div>

                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"></path>
                                                        <path d="M12 5v7l5 3"></path>
                                                    </svg>
                                                    <code style="background: #f0f2f5; padding: 2px 6px; border-radius: 3px;">{{ $log->ip_address }}</code>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px 20px; color: #95a5a6;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 48px; height: 48px; margin: 0 auto 15px; opacity: 0.5;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <p style="margin: 0; font-size: 14px;">لا توجد عمليات مسجلة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>


    </div>

    <!-- Modal: إضافة كمية جديدة -->
    <div class="modal fade" id="addQuantityModal" tabindex="-1" role="dialog" aria-labelledby="addQuantityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addQuantityModalLabel" style="flex: 1; text-align: right;">
                        <i class="feather icon-plus-circle"></i>
                        إضافة كمية جديدة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Alert Messages -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 15px 15px 0;">
                        <strong>خطأ!</strong>
                        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('manufacturing.warehouse-products.add-quantity', $material->id) }}" id="addQuantityForm">
                    @csrf
                    <div class="modal-body">
                        <!-- معلومات المستودع المختار -->
                        <div class="warehouse-info-container" id="warehouseInfoContainer" style="display: none;">
                            <div class="warehouse-card">
                                <div class="warehouse-card-header">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    </svg>
                                    <h5>معلومات المستودع المختار</h5>
                                </div>
                                <div class="warehouse-card-body">
                                    <div class="warehouse-detail">
                                        <span class="warehouse-label">اسم المستودع:</span>
                                        <span class="warehouse-value" id="warehouseName">-</span>
                                    </div>
                                    <div class="warehouse-detail">
                                        <span class="warehouse-label">الكمية الحالية:</span>
                                        <span class="warehouse-value quantity" id="warehouseQuantity">0</span>
                                        <span class="warehouse-unit" id="warehouseUnit">{{ $material->unit->unit_name??'-' }}</span>
                                    </div>
                                    <div class="warehouse-detail status">
                                        <span class="warehouse-label">الحالة:</span>
                                        <span class="warehouse-status-badge" id="warehouseStatus">
                                            <span class="status-dot"></span>
                                            <span class="status-text">-</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- اختيار المستودع -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="warehouse_id" class="form-label">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; display: inline-block; margin-left: 5px;">
                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                        </svg>
                                        المستودع
                                        <span style="color: #e74c3c; font-weight: 700;">*</span>
                                    </label>
                                    <select name="warehouse_id" id="warehouse_id" class="form-control" required onchange="updateWarehouseInfo()">
                                        <option value="">-- اختر المستودع --</option>
                                        @php
                                            $warehouses = \App\Models\Warehouse::all();
                                        @endphp
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" data-warehouse-name="{{ $warehouse->warehouse_name }}">
                                                {{ $warehouse->warehouse_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- اختيار الوحدة -->
                                <div class="form-group">
                                    <label for="unit_id" class="form-label">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; display: inline-block; margin-left: 5px;">
                                            <circle cx="12" cy="12" r="1"></circle>
                                            <path d="M12 2v5"></path>
                                            <path d="M12 17v5"></path>
                                            <path d="M5.64 5.64l3.54 3.54"></path>
                                            <path d="M14.82 14.82l3.54 3.54"></path>
                                            <path d="M2 12h5"></path>
                                            <path d="M17 12h5"></path>
                                            <path d="M5.64 18.36l3.54-3.54"></path>
                                            <path d="M14.82 9.18l3.54-3.54"></path>
                                        </svg>
                                        الوحدة
                                        <span style="color: #e74c3c; font-weight: 700;">*</span>
                                    </label>
                                    <select name="unit_id" id="unit_id" class="form-control" required onchange="updateUnitDisplay()">
                                        <option value="">-- اختر الوحدة --</option>
                                        @php
                                            $units = \App\Models\Unit::all();
                                            $defaultUnit = $material->materialDetails->first()?->unit_id;
                                        @endphp
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}" {{ $defaultUnit == $unit->id ? 'selected' : '' }} data-unit-name="{{ $unit->unit_name }}">
                                                {{ $unit->unit_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- إدخال الكمية -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="quantity" class="form-label">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; display: inline-block; margin-left: 5px;">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                        الكمية المراد إضافتها
                                        <span style="color: #e74c3c; font-weight: 700;">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="quantity" id="quantity" class="form-control"
                                               placeholder="أدخل الكمية" step="0.01" min="0.01" required>
                                        <div class="input-group-append">
                                           <span class="input-group-text" id="unitDisplay">{{ $material->materialDetails->first()?->getUnitName() ?? 'وحدة' }}</span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; display: inline-block; margin-left: 3px;">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="16" x2="12" y2="12"></line>
                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                        </svg>
                                        إجمالي الكمية المتبقية في جميع المستودعات: <strong style="color: #667eea;">{{ number_format($material->materialDetails->sum('remaining_weight'), 2) }}</strong>
                                        {{ $material->materialDetails->first()?->getUnitName() ?? 'وحدة' }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- الملاحظات -->
                        <div class="form-group">
                            <label for="notes" class="form-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; display: inline-block; margin-left: 5px;">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                                الملاحظات <span style="color: #95a5a6; font-size: 12px;">(اختياري)</span>
                            </label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"
                                    placeholder="أدخل أي ملاحظات إضافية حول هذه الكمية..."></textarea>
                        </div>

                        <!-- معلومات تلقائية -->
                        <div class="alert alert-info" role="alert">
                            <h6 class="alert-heading">
                                <i class="feather icon-info"></i>
                                معلومات مهمة
                            </h6>
                            <ul style="margin: 10px 0 0 0; padding-left: 20px; font-size: 13px; line-height: 1.8;">
                                <li>سيتم تحديث الكمية الأصلية والمتبقية في المستودع المختار</li>
                                <li>سيتم تسجيل حركة استقبال تلقائياً في سجل الحركات</li>
                                <li>سيتم حفظ اسم المستخدم الحالي مع العملية</li>
                                <li>التاريخ والوقت سيتم تسجيلهما تلقائياً</li>
                            </ul>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="feather icon-x"></i>
                            إلغاء
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="feather icon-check"></i>
                            إضافة الكمية
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // بيانات المستودعات والكميات
        const warehouseData = @json($material->materialDetails->groupBy('warehouse_id')->map(function($details) {
            return [
                'name' => $details->first()->warehouse->warehouse_name,
                'quantity' => $details->sum('remaining_weight'),
                'unit' => $details->first()->unit?->unit_name ?? 'وحدة'
            ];
        }));

        function updateUnitDisplay() {
            const unitSelect = document.getElementById('unit_id');
            const unitDisplay = document.getElementById('unitDisplay');

            if (unitSelect.value === '') {
                unitDisplay.textContent = 'وحدة';
                return;
            }

            const selectedOption = unitSelect.options[unitSelect.selectedIndex];
            const unitName = selectedOption.getAttribute('data-unit-name');
            unitDisplay.textContent = unitName;
        }

        function updateWarehouseInfo() {
            const warehouseSelect = document.getElementById('warehouse_id');
            const warehouseId = warehouseSelect.value;
            const infoContainer = document.getElementById('warehouseInfoContainer');

            if (warehouseId === '') {
                infoContainer.style.display = 'none';
                return;
            }

            // البحث عن بيانات المستودع
            const selectedOption = warehouseSelect.options[warehouseSelect.selectedIndex];
            const warehouseName = selectedOption.getAttribute('data-warehouse-name');

            // الحصول على الكمية من بيانات المستودع
            let quantity = 0;
            let unit = 'وحدة';

            if (warehouseData[warehouseId]) {
                quantity = warehouseData[warehouseId].quantity;
                unit = warehouseData[warehouseId].unit;
            }

            // تحديث المعلومات
            document.getElementById('warehouseName').textContent = warehouseName;
            document.getElementById('warehouseQuantity').textContent = quantity.toFixed(2);
            document.getElementById('warehouseUnit').textContent = unit;

            // تحديث حالة المستودع
            const statusElement = document.getElementById('warehouseStatus');
            if (quantity > 10) {
                statusElement.className = 'warehouse-status-badge available';
                statusElement.innerHTML = '<span class="status-dot"></span><span class="status-text">متوفر</span>';
            } else if (quantity > 0) {
                statusElement.className = 'warehouse-status-badge low';
                statusElement.innerHTML = '<span class="status-dot"></span><span class="status-text">منخفض</span>';
            } else {
                statusElement.className = 'warehouse-status-badge empty';
                statusElement.innerHTML = '<span class="status-dot"></span><span class="status-text">فارغ</span>';
            }

            infoContainer.style.display = 'block';
            setTimeout(() => {
                infoContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Validation للـ Add Quantity Form
            const addQuantityForm = document.getElementById('addQuantityForm');
            if (addQuantityForm) {
                addQuantityForm.addEventListener('submit', function(e) {
                    const warehouseId = document.getElementById('warehouse_id').value;
                    const quantity = document.getElementById('quantity').value;

                    if (!warehouseId) {
                        e.preventDefault();
                        alert('⚠️ يرجى اختيار المستودع');
                        document.getElementById('warehouse_id').focus();
                        return false;
                    }

                    if (!quantity || parseFloat(quantity) <= 0) {
                        e.preventDefault();
                        alert('⚠️ يرجى إدخال كمية صحيحة أكبر من صفر');
                        document.getElementById('quantity').focus();
                        return false;
                    }

                    // Show confirmation with warehouse name
                    const warehouseSelect = document.getElementById('warehouse_id');
                    const warehouseName = warehouseSelect.options[warehouseSelect.selectedIndex].text;

                    if (!confirm(`✅ هل تريد إضافة ${quantity} وحدة إلى مستودع "${warehouseName}"?`)) {
                        e.preventDefault();
                        return false;
                    }
                });
            }

            // Delete confirmation with SweetAlert2 if available
            const deleteButtons = document.querySelectorAll('.action-btn.delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'تأكيد الحذف',
                            text: 'هل أنت متأكد من حذف هذه المادة؟ هذا الإجراء لا يمكن التراجع عنه!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'نعم، احذف',
                            cancelButtonText: 'إلغاء',
                            confirmButtonColor: '#e74c3c',
                            cancelButtonColor: '#95a5a6',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        if (confirm('هل أنت متأكد من حذف هذه المادة؟ هذا الإجراء لا يمكن التراجع عنه!')) {
                            form.submit();
                        }
                    }
                });
            });

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert:not(.alert-info)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 5000);
            });

            // Show modal if there are errors
            @if ($errors->any())
                $('#addQuantityModal').modal('show');
            @endif
        });
    </script>
@endsection
