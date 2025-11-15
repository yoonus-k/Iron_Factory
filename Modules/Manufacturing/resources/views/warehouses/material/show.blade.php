@extends('master')

@section('title', 'تفاصيل المادة')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <style>
        /* تحسينات تصميم المستودعات */
        .warehouses-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .warehouse-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 20px;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
            transition: all 0.3s ease;
        }

        .warehouse-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }

        .warehouse-item.empty {
            background: linear-gradient(135deg, #757575 0%, #616161 100%);
            opacity: 0.7;
        }

        .warehouse-item-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .warehouse-item-icon {
            width: 35px;
            height: 35px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .warehouse-item-icon svg {
            width: 20px;
            height: 20px;
        }

        .warehouse-item-name {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }

        .warehouse-item-body {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .warehouse-quantity {
            display: flex;
            align-items: baseline;
            gap: 5px;
        }

        .warehouse-quantity-value {
            font-size: 28px;
            font-weight: 700;
            line-height: 1;
        }

        .warehouse-quantity-unit {
            font-size: 14px;
            opacity: 0.9;
        }

        .warehouse-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            width: fit-content;
        }

        .warehouse-status-dot {
            width: 6px;
            height: 6px;
            background: #4caf50;
            border-radius: 50%;
            display: inline-block;
        }

        .warehouse-item.empty .warehouse-status-dot {
            background: #ff5252;
        }

        /* تحسينات المودل */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px 25px;
            border: none;
        }

        .modal-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .modal-title i {
            font-size: 22px;
        }

        .modal-header .close {
            color: white;
            opacity: 0.9;
            text-shadow: none;
            font-size: 28px;
            font-weight: 300;
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        .modal-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .input-group-text {
            background: #667eea;
            color: white;
            border: 2px solid #667eea;
            border-radius: 0 8px 8px 0;
            font-weight: 500;
        }

        .warehouse-info-container {
            margin-bottom: 25px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .warehouse-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            overflow: hidden;
            color: white;
        }

        .warehouse-card-header {
            padding: 15px 20px;
            background: rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .warehouse-card-header svg {
            width: 20px;
            height: 20px;
        }

        .warehouse-card-header h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .warehouse-card-body {
            padding: 20px;
        }

        .warehouse-detail {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .warehouse-detail:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .warehouse-label {
            font-size: 13px;
            opacity: 0.9;
            min-width: 100px;
        }

        .warehouse-value {
            font-weight: 600;
            font-size: 15px;
        }

        .warehouse-value.quantity {
            font-size: 24px;
            font-weight: 700;
        }

        .warehouse-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        .warehouse-status-badge .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4caf50;
        }

        .warehouse-status-badge.empty .status-dot {
            background: #ff5252;
        }

        .warehouse-status-badge.low .status-dot {
            background: #ffc107;
        }

        .alert-info {
            background: #e3f2fd;
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
        }

        .alert-heading {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #1976d2;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .modal-footer {
            padding: 15px 25px;
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
            border-radius: 0 0 15px 15px;
        }

        .modal-footer .btn {
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .add-quantity-btn-wrapper {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }

        .add-quantity-btn-wrapper .btn {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .add-quantity-btn-wrapper .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }
    </style>

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-package"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $material->material_type }} ({{ $material->material_type_en }})</h1>
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
                        <div class="info-value">{{ $material->material_type }}</div>
                    </div>

                    @if($material->material_type_en)
                    <div class="info-item">
                        <div class="info-label">Material Name:</div>
                        <div class="info-value">{{ $material->material_type_en }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">إجمالي الوزن الأصلي:</div>
                        <div class="info-value">
                            {{ $material->materialDetails->sum('original_weight') }}
                            {{ $material->materialDetails->first()?->unit?->name ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">إجمالي الوزن المتبقي:</div>
                        <div class="info-value">
                            {{ $material->materialDetails->sum('remaining_weight') }}
                            {{ $material->materialDetails->first()?->unit?->name ?? 'N/A' }}
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
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
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
                                    $unit = $details->first()->unit?->name ?? 'وحدة';
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
                    <h3 class="card-title">الملاحظات</h3>
                </div>
                <div class="card-body">
                    @if($material->notes)
                        <p>{{ $material->notes }}</p>
                    @elseif($material->notes_en)
                        <p>{{ $material->notes_en }}</p>
                    @else
                        <p>لا توجد ملاحظات</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-icon warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="19" cy="12" r="1"></circle>
                        <circle cx="5" cy="12" r="1"></circle>
                    </svg>
                </div>
                <h3 class="card-title">الإجراءات المتاحة</h3>
            </div>
            <div class="card-body">
                <div class="actions-grid">
                    <a href="{{ route('manufacturing.warehouse-products.transactions', $material->id) }}" class="action-btn info">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                <polyline points="17 6 23 6 23 12"></polyline>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h5>سجل الحركات</h5>
                            <p>عرض جميع حركات المستودع</p>
                        </div>
                    </a>

                    <a href="{{ route('manufacturing.warehouse-products.edit', $material->id) }}" class="action-btn activate">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </div>
                        <div class="action-text">
                            <h5>تعديل المادة</h5>
                            <p>تحديث معلومات المادة</p>
                        </div>
                    </a>

                    <form method="POST" action="{{ route('manufacturing.warehouse-products.destroy', $material->id) }}" style="flex: 1;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn delete" onclick="return confirm('هل أنت متأكد من حذف هذه المادة؟\n\nهذا الإجراء لا يمكن التراجع عنه!')">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </div>
                            <div class="action-text">
                                <h5>حذف المادة</h5>
                                <p>إزالة المادة من النظام</p>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: إضافة كمية جديدة -->
    <div class="modal fade" id="addQuantityModal" tabindex="-1" role="dialog" aria-labelledby="addQuantityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addQuantityModalLabel">
                        <i class="feather icon-plus-circle"></i>
                        إضافة كمية جديدة
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
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
                                        <span class="warehouse-unit" id="warehouseUnit">وحدة</span>
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

                        <!-- إدخال الكمية -->
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
                                   <span class="input-group-text">{{ $material->materialDetails->first()?->unit?->name ?? 'وحدة' }}</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; display: inline-block; margin-left: 3px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                إجمالي الكمية المتبقية في جميع المستودعات: <strong style="color: #667eea;">{{ number_format($material->materialDetails->sum('remaining_weight'), 2) }}</strong>
                                {{ $material->materialDetails->first()?->unit?->name ?? 'وحدة' }}
                            </small>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
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
                'unit' => $details->first()->unit?->name ?? 'وحدة'
            ];
        }));

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
