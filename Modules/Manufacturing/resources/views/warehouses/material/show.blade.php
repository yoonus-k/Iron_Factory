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

        /* Modal Animation */
        #movementDetailsModal[style*="display: flex"] {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Spinner */
        .spinner-border {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            vertical-align: text-bottom;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border 0.75s linear infinite;
        }

        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }

        .text-primary {
            color: #3498db !important;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0,0,0,0);
            white-space: nowrap;
            border-width: 0;
        }

        /* Button Hover */
        .view-movement-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
        }

        /* Table Responsive Styles */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #3498db;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #2980b9;
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
                        // جلب تفاصيل المادة من جدول material_details مع المستودعات
                        $warehouseDetails = $material->materialDetails()->with(['warehouse', 'unit'])->get()->groupBy('warehouse_id');
                    @endphp

                    @if($warehouseDetails->isNotEmpty())
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                            @foreach($warehouseDetails as $warehouseId => $details)
                                @php
                                    $warehouse = $details->first()->warehouse;
                                    // حساب الكميات من material_details بشكل صحيح
                                    $totalQuantity = $details->sum('quantity');
                                    $totalOriginalWeight = $details->sum('original_weight');
                                    $totalActualWeight = $details->sum('actual_weight');
                                    $totalRemainingWeight = $details->sum('remaining_weight');
                                    $unit = $details->first()->unit?->unit_name ?? 'وحدة';
                                    $unitSymbol = $details->first()->unit?->unit_symbol ?? 'كغ';
                                    $isEmpty = $totalRemainingWeight <= 0;
                                    $percentageRemaining = $totalOriginalWeight > 0 ? ($totalRemainingWeight / $totalOriginalWeight * 100) : 0;
                                @endphp
                                <div style="border: 1px solid #e9ecef; border-radius: 8px; padding: 15px; background: #f8f9fa;">
                                    <div style="margin-bottom: 12px;">
                                        <h5 style="margin: 0 0 4px 0; color: #2c3e50; font-size: 14px; font-weight: 600;">
                                            {{ $warehouse->warehouse_name ?? '-' }}
                                        </h5>
                                        @if($warehouse->warehouse_code)
                                            <small style="color: #7f8c8d;">{{ $warehouse->warehouse_code }}</small>
                                        @endif
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                                        <div>
                                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 4px;">الكمية</div>
                                            <div style="font-size: 16px; font-weight: 600; color: #3498db;">
                                                {{ $totalQuantity }}
                                            </div>
                                        </div>
                                        <div>
                                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 4px;">الوحدة</div>
                                            <span style="background: #3498db; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                {{ $unitSymbol }}
                                            </span>
                                        </div>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">

                                    </div>

                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            @if($isEmpty)
                                                <span class="badge badge-danger">فارغ</span>
                                            @elseif($percentageRemaining < 30)
                                                <span class="badge badge-warning">منخفض</span>
                                            @else
                                                <span class="badge badge-success">متوفر</span>
                                            @endif
                                        </div>
                                        <div>
                                            <small style="color: #7f8c8d; font-size: 11px;">عدد السجلات: {{ $details->count() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 60px 20px; color: #95a5a6;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 64px; height: 64px; margin: 0 auto 15px; opacity: 0.3;">
                                <path d="M6 9l6-6 6 6"></path>
                                <path d="M6 9v10a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V9"></path>
                            </svg>
                            <p style="margin: 0; font-size: 16px; font-weight: 500;">لا توجد منتجات في هذا المستودع</p>
                        </div>
                    @endif

                    <div class="add-quantity-btn-wrapper" style="margin-top: 20px; text-align: center;">
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

                                    <div class="operation-header" style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
                                        <div style="flex: 1;">
                                            <div class="operation-description" style="margin-bottom: 8px;">
                                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                                    @switch($log->action)
                                                        @case('create')
                                                            <span class="badge" style="background-color: #27ae60; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">إنشاء</span>
                                                            @break
                                                        @case('update')
                                                            <span class="badge" style="background-color: #3498db; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">تعديل</span>
                                                            @break
                                                        @case('delete')
                                                            <span class="badge" style="background-color: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">حذف</span>
                                                            @break
                                                        @case('add_quantity')
                                                            <span class="badge" style="background-color: #f39c12; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">إضافة كمية</span>
                                                            @break
                                                        @default
                                                            <span class="badge" style="background-color: #95a5a6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">{{ $log->action_en ?? $log->action }}</span>
                                                    @endswitch

                                                    <strong style="color: #2c3e50; font-size: 14px;">{{ $log->description }}</strong>
                                                </div>
                                            </div>

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

    <!-- حركة المخزون - Full Width -->
    <div class="row">
        <div class="col-12">
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">حركة المخزون</h3>
                </div>
                <div class="card-body">
                    <!-- فلاتر البحث -->
                    <form method="GET" action="{{ route('manufacturing.warehouses.material.show', $material->id) }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label" style="font-weight: 600; color: var(--secondary-gray); font-size: 14px;">نوع الحركة</label>
                                <select name="movement_type" class="form-select" style="border: 2px solid #e9ecef; border-radius: 8px;">
                                    <option value="">كل الأنواع</option>
                                    <option value="in" {{ request('movement_type') == 'in' ? 'selected' : '' }}>وارد</option>
                                    <option value="out" {{ request('movement_type') == 'out' ? 'selected' : '' }}>صادر</option>
                                    <option value="transfer" {{ request('movement_type') == 'transfer' ? 'selected' : '' }}>تحويل</option>
                                    <option value="adjustment" {{ request('movement_type') == 'adjustment' ? 'selected' : '' }}>تسوية</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="font-weight: 600; color: var(--secondary-gray); font-size: 14px;">المستودع</label>
                                <select name="warehouse_id" class="form-select" style="border: 2px solid #e9ecef; border-radius: 8px;">
                                    <option value="">كل المستودعات</option>
                                    @foreach(\App\Models\Warehouse::all() as $wh)
                                        <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                            {{ $wh->warehouse_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" style="font-weight: 600; color: var(--secondary-gray); font-size: 14px;">من تاريخ</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" style="border: 2px solid #e9ecef; border-radius: 8px;">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" style="font-weight: 600; color: var(--secondary-gray); font-size: 14px;">إلى تاريخ</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" style="border: 2px solid #e9ecef; border-radius: 8px;">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary " >
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                <a href="{{ route('manufacturing.warehouses.material.show', $material->id) }}" class="btn btn-outline-secondary" style="border: 2px solid var(--secondary-gray); border-radius: 8px; padding: 10px 15px;">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- جدول الحركات -->
                    @php
                        $query = \App\Models\MaterialMovement::with(['material', 'warehouse', 'destinationWarehouse', 'deliveryNote.supplier', 'fromWarehouse', 'toWarehouse', 'supplier', 'createdBy'])
                            ->where('material_id', $material->id);

                        if(request('movement_type')) {
                            $query->where('movement_type', request('movement_type'));
                        }

                        if(request('warehouse_id')) {
                            $query->where(function($q) {
                                $q->where('warehouse_id', request('warehouse_id'))
                                  ->orWhere('destination_warehouse_id', request('warehouse_id'));
                            });
                        }

                        if(request('date_from')) {
                            $query->whereDate('movement_date', '>=', request('date_from'));
                        }

                        if(request('date_to')) {
                            $query->whereDate('movement_date', '<=', request('date_to'));
                        }

                        $movements = $query->orderBy('movement_date', 'desc')->paginate(20);

                        // Movement type colors to match warehouse design
                        $movementTypeColors = [
                            'in' => '#27ae60',
                            'out' => '#e74c3c',
                            'transfer' => '#3498db',
                            'adjustment' => '#95a5a6',
                            'reconciliation' => '#1abc9c',
                            'waste' => '#e67e22',
                            'return' => '#34495e'
                        ];
                    @endphp

                    @if($movements->count() > 0)
                        <div class="table-responsive">
                            <table class="table" style="margin: 0;">
                                <thead style="background: #f8f9fa;">
                                    <tr>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">التاريخ</th>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">نوع الحركة</th>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">المستودع المصدر</th>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">المستودع الوجهة</th>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">الكمية</th>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">المورد</th>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">المستخدم</th>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600; text-align: center;">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($movements as $movement)
                                    <tr style="border-bottom: 1px solid #e9ecef;">
                                        <td style="padding: 12px; font-size: 12px; color: #7f8c8d;">
                                            {{ $movement->movement_date ? $movement->movement_date->format('Y-m-d H:i') : $movement->created_at->format('Y-m-d H:i') }}
                                            <br>
                                            <small style="color: #95a5a6; font-size: 10px;">{{ $movement->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td style="padding: 12px;">
                                            @php
                                                $typeColors = [
                                                    'in' => ['bg' => '#d4edda', 'text' => '#155724', 'label' => 'وارد'],
                                                    'out' => ['bg' => '#f8d7da', 'text' => '#721c24', 'label' => 'صادر'],
                                                    'transfer' => ['bg' => '#d1ecf1', 'text' => '#0c5460', 'label' => 'تحويل'],
                                                    'adjustment' => ['bg' => '#fff3cd', 'text' => '#856404', 'label' => 'تسوية']
                                                ];
                                                $typeInfo = $typeColors[$movement->movement_type] ?? ['bg' => '#e2e3e5', 'text' => '#383d41', 'label' => $movement->movement_type];
                                                $color = $movementTypeColors[$movement->movement_type] ?? '#95a5a6';
                                            @endphp
                                            <span style="background: {{ $color }}; color: white; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; white-space: nowrap;">
                                                {{ $movement->movement_type_name }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px; font-size: 12px; color: #2c3e50;">
                                            @if($movement->fromWarehouse)
                                                <span style="background: #ecf0f1; padding: 3px 8px; border-radius: 4px;">
                                                    {{ $movement->fromWarehouse->warehouse_name }}
                                                </span>
                                            @elseif($movement->supplier)
                                                <span style="background: #e8f5e9; padding: 3px 8px; border-radius: 4px; color: #27ae60;">
                                                    {{ $movement->supplier->supplier_name ?? '-' }}
                                                </span>
                                            @else
                                                <span style="color: #95a5a6;">-</span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px; font-size: 12px; color: #2c3e50;">
                                            @if($movement->toWarehouse)
                                                <span style="background: #ecf0f1; padding: 3px 8px; border-radius: 4px;">
                                                    {{ $movement->toWarehouse->warehouse_name }}
                                                </span>
                                            @elseif($movement->destination)
                                                <span style="background: #fff3e0; padding: 3px 8px; border-radius: 4px; color: #f39c12;">
                                                    {{ $movement->destination }}
                                                </span>
                                            @else
                                                <span style="color: #95a5a6;">-</span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px; font-size: 14px; font-weight: 600;">
                                            <span style="color: #3498db;">{{ number_format($movement->quantity, 2) }}</span>
                                            <small style="color: #7f8c8d;">{{ $movement->unit?->unit_symbol ?? '' }}</small>
                                        </td>
                                        <td style="padding: 12px; font-size: 12px; color: #2c3e50;">
                                            {{ $movement->deliveryNote?->supplier?->supplier_name ?? '-' }}
                                        </td>
                                        <td style="padding: 12px; font-size: 12px; color: #2c3e50;">
                                            {{ $movement->createdBy?->name ?? '-' }}
                                        </td>
                                        <td style="padding: 12px; text-align: center;">
                                            <button type="button" class="view-movement-btn" onclick="viewMovementDetails({{ $movement->id }})" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 5px;">
                                                <i class="feather icon-eye"></i>
                                                عرض
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div style="padding: 20px; border-top: 1px solid #e9ecef; background: #f8f9fa;">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                                <div style="font-size: 14px; color: #7f8c8d;">
                                    عرض <strong>{{ $movements->firstItem() }}</strong> إلى <strong>{{ $movements->lastItem() }}</strong> من أصل <strong>{{ $movements->total() }}</strong> حركة
                                </div>
                                <div>
                                    {{ $movements->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="text-align: center; padding: 60px 20px; color: #95a5a6;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 64px; height: 64px; margin: 0 auto 15px; opacity: 0.3;">
                                <polyline points="16 16 12 12 8 16"></polyline>
                                <line x1="12" y1="12" x2="12" y2="21"></line>
                                <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path>
                            </svg>
                            <p style="margin: 0; font-size: 16px; font-weight: 500;">لا توجد حركات مسجلة على هذا المستودع</p>
                            <small style="color: #bdc3c7; display: block; margin-top: 8px;">سيتم عرض جميع حركات الدخول والخروج هنا</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- سجل العمليات -->

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
        // بيانات المستودعات والكميات من material_details
        const warehouseData = @json($material->materialDetails->groupBy('warehouse_id')->map(function($details) {
            return [
                'name' => $details->first()->warehouse->warehouse_name,
                'quantity' => $details->sum('quantity'), // ✅ الكمية من quantity field
                'unit' => $details->first()->unit?->unit_name ?? 'وحدة'
            ];
        })->toArray());

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

        // View Movement Details (matching warehouse design)
        function viewMovementDetails(movementId) {
            const modal = document.createElement('div');
            modal.innerHTML = `
                <div id="movementDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
                    <div style="background: white; border-radius: 12px; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                        <!-- Modal Header -->
                        <div style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="margin: 0; font-size: 20px; font-weight: 700;">
                                <i class="feather icon-info"></i>
                                تفاصيل الحركة
                            </h3>
                            <button onclick="closeMovementModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; font-size: 20px; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                                <i class="feather icon-x"></i>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div id="movementDetailsContent" style="padding: 25px;">
                            <div style="text-align: center; padding: 40px;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">جاري التحميل...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            const modalElement = document.getElementById('movementDetailsModal');
            modalElement.style.display = 'flex';

            // Fetch movement details
            fetch(`/manufacturing/material-movements/${movementId}`)
                .then(response => response.json())
                .then(data => {
                    const movement = data.movement;

                    // Movement type colors to match warehouse design
                    const typeColors = {
                        'in': '#27ae60',
                        'out': '#e74c3c',
                        'transfer': '#3498db',
                        'adjustment': '#95a5a6',
                        'reconciliation': '#1abc9c',
                        'waste': '#e67e22',
                        'return': '#34495e'
                    };

                    const color = typeColors[movement.movement_type] || '#95a5a6';

                    const content = document.getElementById('movementDetailsContent');
                    content.innerHTML = `
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-right: 4px solid ${color};">
                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">رقم الحركة</div>
                                <div style="font-size: 16px; font-weight: 700; color: #2c3e50;">
                                    <code style="background: white; padding: 6px 10px; border-radius: 4px;">${movement.movement_number || '-'}</code>
                                </div>
                            </div>

                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-right: 4px solid ${color};">
                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">نوع الحركة</div>
                                <div style="font-size: 16px; font-weight: 700;">
                                    <span style="background: ${color}; color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px;">${movement.movement_type_name || movement.movement_type}</span>
                                </div>
                            </div>
                        </div>

                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <h4 style="margin: 0 0 15px 0; font-size: 16px; color: #2c3e50; font-weight: 700; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
                                <i class="feather icon-package"></i> معلومات المادة
                            </h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">المادة</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #2c3e50;">${movement.material?.name_ar || movement.material?.name_en || '-'}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">الكمية</div>
                                    <div style="font-size: 18px; font-weight: 700; color: #3498db;">${movement.quantity || 0} <small style="color: #7f8c8d; font-size: 13px;">${movement.unit?.unit_symbol || ''}</small></div>
                                </div>
                                ${movement.unit_price ? `
                                <div>
                                    <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">سعر الوحدة</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #27ae60;">${movement.unit_price}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">القيمة الإجمالية</div>
                                    <div style="font-size: 16px; font-weight: 700; color: #27ae60;">${movement.total_value}</div>
                                </div>
                                ` : ''}
                            </div>
                        </div>

                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <h4 style="margin: 0 0 15px 0; font-size: 16px; color: #2c3e50; font-weight: 700; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
                                <i class="feather icon-truck"></i> معلومات النقل
                            </h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">من</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #2c3e50;">${movement.fromWarehouse?.warehouse_name || movement.supplier?.supplier_name || '-'}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">إلى</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #2c3e50;">${movement.toWarehouse?.warehouse_name || movement.destination || '-'}</div>
                                </div>
                            </div>
                        </div>

                        ${movement.description || movement.notes ? `
                        <div style="background: #fff3cd; padding: 15px; border-radius: 8px; border-right: 4px solid #f39c12; margin-bottom: 20px;">
                            <h4 style="margin: 0 0 10px 0; font-size: 14px; color: #856404; font-weight: 700;">
                                <i class="feather icon-file-text"></i> ملاحظات
                            </h4>
                            ${movement.description ? `<p style="margin: 0 0 8px 0; color: #856404; font-size: 13px;"><strong>الوصف:</strong> ${movement.description}</p>` : ''}
                            ${movement.notes ? `<p style="margin: 0; color: #856404; font-size: 13px;"><strong>ملاحظات:</strong> ${movement.notes}</p>` : ''}
                        </div>
                        ` : ''}

                        <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; border-right: 4px solid #27ae60;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; font-size: 12px;">
                                <div>
                                    <div style="color: #7f8c8d; margin-bottom: 3px; font-weight: 600;">المصدر</div>
                                    <div style="font-weight: 600; color: #2c3e50;">${movement.source_name || '-'}</div>
                                </div>
                                <div>
                                    <div style="color: #7f8c8d; margin-bottom: 3px; font-weight: 600;">التاريخ</div>
                                    <div style="font-weight: 600; color: #2c3e50;">${movement.movement_date || movement.created_at}</div>
                                </div>
                                <div>
                                    <div style="color: #7f8c8d; margin-bottom: 3px; font-weight: 600;">المستخدم</div>
                                    <div style="font-weight: 600; color: #2c3e50;">${movement.createdBy?.name || '-'}</div>
                                </div>
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    const content = document.getElementById('movementDetailsContent');
                    content.innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #e74c3c;">
                            <i class="feather icon-alert-circle" style="font-size: 48px; margin-bottom: 15px;"></i>
                            <p style="margin: 0; font-size: 16px; font-weight: 600;">حدث خطأ في تحميل البيانات</p>
                            <small style="color: #95a5a6;">يرجى المحاولة مرة أخرى</small>
                        </div>
                    `;
                });
        }

        function closeMovementModal() {
            const modal = document.getElementById('movementDetailsModal');
            if (modal) {
                modal.remove();
            }
        }

        // Close modal when clicking outside (matching warehouse design)
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('movementDetailsModal');
            if (modal && e.target === modal) {
                closeMovementModal();
            }
        });
    </script>
@endsection
