@extends('master')

@section('title', 'تفاصيل المورد')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <style>
        /* Tabs Styling */
        .tabs-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .tabs-container:hover {
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }

        .tabs-header {
            display: flex;
            background: linear-gradient(135deg, #f8f9fa 0%, #f0f2f5 100%);
            border-bottom: 2px solid #e9ecef;
            overflow-x: auto;
            position: relative;
            padding: 0 20px;
        }

        .tabs-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            height: 2px;
            background: transparent;
            transition: all 0.3s ease;
        }

        .tab-button {
            flex: 1;
            min-width: 160px;
            padding: 16px 20px;
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            white-space: nowrap;
        }

        .tab-button::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3498db, #2ecc71);
            border-radius: 2px 2px 0 0;
            transform: scaleX(0);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .tab-button:hover {
            color: #2c3e50;
            background: rgba(52, 152, 219, 0.05);
        }

        .tab-button.active {
            color: #3498db;
            background: rgba(52, 152, 219, 0.08);
        }

        .tab-button.active::after {
            transform: scaleX(1);
        }

        .tab-button svg {
            width: 20px;
            height: 20px;
            transition: transform 0.3s ease;
        }

        .tab-button:hover svg {
            transform: scale(1.1);
        }

        .tab-content {
            display: none;
            padding: 30px 20px;
            animation: slideInFade 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .tab-content.active {
            display: block;
        }

        @keyframes slideInFade {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .badge-count {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            margin-right: 5px;
            box-shadow: 0 2px 6px rgba(52, 152, 219, 0.3);
            transition: all 0.3s ease;
        }

        .tab-button:hover .badge-count {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.4);
        }

        .tab-button.active .badge-count {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            box-shadow: 0 2px 6px rgba(46, 204, 113, 0.3);
        }

        /* Smooth scroll for tabs */
        .tabs-header {
            scroll-behavior: smooth;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .tab-button {
                min-width: 130px;
                padding: 14px 12px;
                font-size: 13px;
            }

            .tabs-header {
                padding: 0 10px;
            }

            .tab-content {
                padding: 20px 15px;
            }

            .badge-count {
                display: none;
            }
        }

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

        .pagination-info {
            flex: 1;
            min-width: 200px;
        }

        .um-pagination-info {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }

        .pagination-links {
            flex: 1;
            display: flex;
            justify-content: flex-end;
            min-width: 300px;
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

        /* Mobile Pagination */
        @media (max-width: 768px) {
            .um-pagination-section {
                flex-direction: column;
                align-items: stretch;
            }

            .pagination-info {
                text-align: center;
            }

            .pagination-links {
                justify-content: center;
            }

            .pagination .page-link {
                padding: 6px 10px;
                font-size: 12px;
                min-width: 32px;
            }
        }

        /* Action Buttons Styling */
        .badge {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        .badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .badge svg {
            vertical-align: middle;
        }
    </style>

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-truck"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $supplier->getName() }}</h1>
                        <div class="badges">
                            <span class="badge category">
                                {{ $supplier->contact_person }}
                            </span>
                            @if($supplier->is_active)
                                <span class="badge active">نشط</span>
                            @else
                                <span class="badge inactive">غير نشط</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    @if (auth()->user()->hasPermission('WAREHOUSE_SUPPLIERS_UPDATE'))
                        <a href="{{ route('manufacturing.suppliers.edit', $supplier->id) }}" class="btn btn-edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            تعديل
                        </a>
                    @endif
                    @if (auth()->user()->hasPermission('WAREHOUSE_SUPPLIERS_UPDATE'))
                        <form method="POST" action="{{ route('manufacturing.suppliers.toggle-status', $supplier->id) }}" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn" style="background-color: {{ $supplier->is_active ? '#e74c3c' : '#27ae60' }}; color: white; border: none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                {{ $supplier->is_active ? 'تعطيل' : 'تفعيل' }}
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('manufacturing.suppliers.index') }}" class="btn btn-back">
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
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المورد</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">اسم المورد:</div>
                        <div class="info-value">{{ $supplier->getName() }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الشخص المسؤول:</div>
                        <div class="info-value">{{ $supplier->contact_person }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">رقم الهاتف:</div>
                        <div class="info-value">{{ $supplier->phone }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">البريد الإلكتروني:</div>
                        <div class="info-value">{{ $supplier->email }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">العنوان:</div>
                        <div class="info-value">{{ $supplier->address ?? 'غير محدد' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">المدينة:</div>
                        <div class="info-value">{{ $supplier->city ?? 'غير محدد' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value">
                            @if($supplier->is_active)
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-danger">غير نشط</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a4 4 0 0 1 4-4h2a4 4 0 0 1 4 4v4"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">الملاحظات</h3>
                </div>
                <div class="card-body">
                    <p class="info-text">{{ $supplier->notes ?? 'لا توجد ملاحظات' }}</p>
                </div>
            </div>
        </div>

        <!-- Tabs Container -->
        <div class="tabs-container">
            <div class="tabs-header">
                <button class="tab-button active" data-tab="invoices">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    </svg>
                    📄 فواتير المورد
                    <span class="badge-count">{{ $invoices->total() }}</span>
                </button>

                <button class="tab-button" data-tab="delivery-notes">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 19h6m-6 0a7 7 0 1 1 6 0m-6 0H3m6 0h6"></path>
                        <path d="M12 5v9m-4-4l4-4 4 4"></path>
                    </svg>
                    📦 أذون التسليم
                    <span class="badge-count">{{ $deliveryNotes->total() }}</span>
                </button>

                <button class="tab-button" data-tab="operation-logs">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    📋 سجل العمليات
                    @php
                        $operationLogs = $supplier->operationLogs()->orderBy('created_at', 'desc')->get();
                    @endphp
                    <span class="badge-count">{{ $operationLogs->count() }}</span>
                </button>
            </div>

            <!-- Tab Content: Invoices -->
            <div class="tab-content active" id="invoices" data-tab="invoices">
                <div class="um-table-responsive">
                    <table class="um-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم الفاتورة</th>
                                <th>التاريخ</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $index => $invoice)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                <td><strong style="color: #27ae60;">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</strong></td>
                                <td>
                                    @php
                                        $statusBg = [
                                            'draft' => '#95a5a6',
                                            'pending' => '#f39c12',
                                            'approved' => '#27ae60',
                                            'paid' => '#3498db',
                                            'rejected' => '#e74c3c',
                                        ];
                                        $bg = $statusBg[$invoice->status->value] ?? '#95a5a6';
                                    @endphp
                                    <span class="badge" style="background-color: {{ $bg }}; color: white; padding: 4px 8px; border-radius: 4px;">
                                        {{ $invoice->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px; justify-content: flex-start;">
                                        <a href="{{ route('manufacturing.purchase-invoices.show', $invoice->id) }}" class="badge badge-info" style="cursor: pointer; background-color: #3498db; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;" title="عرض">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; display: inline; margin-right: 4px;">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            عرض
                                        </a>
                                        <a href="{{ route('manufacturing.purchase-invoices.edit', $invoice->id) }}" class="badge" style="cursor: pointer; background-color: #3498db; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;" title="تعديل">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; display: inline; margin-right: 4px;">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                            تعديل
                                        </a>
                                        <button onclick="deleteInvoice({{ $invoice->id }})" class="badge" style="cursor: pointer; background-color: #3498db; color: white; padding: 5px 10px; border-radius: 4px; border: none; font-size: 12px;" title="حذف">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; display: inline; margin-right: 4px;">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                            حذف
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">📭 لا توجد فواتير</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($invoices->hasPages())
                <div class="um-pagination-section">
                    <div class="pagination-info">
                        <p class="um-pagination-info">
                            عرض {{ $invoices->firstItem() ?? 0 }} إلى {{ $invoices->lastItem() ?? 0 }} من أصل {{ $invoices->total() }} فاتورة
                        </p>
                    </div>
                    <div class="pagination-links">
                        {{ $invoices->links('pagination::bootstrap-4') }}
                    </div>
                </div>
                @endif
            </div>

            <!-- Tab Content: Delivery Notes -->
            <div class="tab-content" id="delivery-notes" data-tab="delivery-notes">
                <div class="um-table-responsive">
                    <table class="um-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم الأذن</th>
                                <th>التاريخ</th>
                                <th>المادة</th>
                                <th>الكمية</th>
                                <th>الوزن</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deliveryNotes as $index => $note)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $note->note_number }}</strong></td>
                                <td>{{ $note->delivery_date->format('Y-m-d') }}</td>
                                <td>{{ $note->material->name_ar ?? 'مادة محذوفة' }}</td>
                                <td>{{ $note->delivery_quantity ?? '-' }}</td>
                                <td><strong>{{ $note->delivered_weight ?? '-' }} كجم</strong></td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => '#f39c12',
                                            'approved' => '#27ae60',
                                            'rejected' => '#e74c3c',
                                            'completed' => '#3498db',
                                            'not_registered' => '#95a5a6',
                                            'registered' => '#27ae60',
                                            'in_production' => '#3498db',
                                        ];
                                        $statusLabel = [
                                            'pending' => 'قيد الانتظار',
                                            'approved' => 'موافق عليه',
                                            'rejected' => 'مرفوض',
                                            'completed' => 'مكتمل',
                                            'not_registered' => 'غير مسجلة',
                                            'registered' => 'مسجلة',
                                            'in_production' => 'في الإنتاج',
                                        ];
                                        $statusKey = $note->registration_status ?? $note->status ?? 'pending';
                                        $bg = $statusColors[$statusKey] ?? '#95a5a6';
                                        $label = $statusLabel[$statusKey] ?? $statusKey;
                                    @endphp
                                    <span class="badge" style="background-color: {{ $bg }}; color: white; padding: 4px 8px; border-radius: 4px;">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px; justify-content: flex-start;">
                                        <a href="{{ route('manufacturing.delivery-notes.show', $note->id) }}" class="badge badge-info" style="cursor: pointer; background-color: #3498db; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;" title="عرض">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; display: inline; margin-right: 4px;">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            عرض
                                        </a>
                                        <a href="{{ route('manufacturing.delivery-notes.edit', $note->id) }}" class="badge" style="cursor: pointer; background-color: #3498db; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;" title="تعديل">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; display: inline; margin-right: 4px;">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                            تعديل
                                        </a>
                                        <button onclick="deleteDeliveryNote({{ $note->id }})" class="badge" style="cursor: pointer; background-color: #3498db; color: white; padding: 5px 10px; border-radius: 4px; border: none; font-size: 12px;" title="حذف">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; display: inline; margin-right: 4px;">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                            حذف
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">📭 لا توجد أذون تسليم</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($deliveryNotes->hasPages())
                <div class="um-pagination-section">
                    <div class="pagination-info">
                        <p class="um-pagination-info">
                            عرض {{ $deliveryNotes->firstItem() ?? 0 }} إلى {{ $deliveryNotes->lastItem() ?? 0 }} من أصل {{ $deliveryNotes->total() }} أذن تسليم
                        </p>
                    </div>
                    <div class="pagination-links">
                        {{ $deliveryNotes->links('pagination::bootstrap-4') }}
                    </div>
                </div>
                @endif
            </div>

            <!-- Tab Content: Operation Logs -->
            <div class="tab-content" id="operation-logs">
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
                    <button type="button" class="action-btn contact" onclick="window.location='tel:{{ $supplier->phone }}'">
                        <div class="action-icon">
                            <i class="feather icon-phone"></i>
                        </div>
                        <div class="action-text">
                            <span>اتصل</span>
                        </div>
                    </button>

                    <button type="button" class="action-btn email" onclick="window.location='mailto:{{ $supplier->email }}'">
                        <div class="action-icon">
                            <i class="feather icon-mail"></i>
                        </div>
                        <div class="action-text">
                            <span>إرسال بريد</span>
                        </div>
                    </button>

                    @if (auth()->user()->hasPermission('WAREHOUSE_SUPPLIERS_DELETE'))
                        <button type="button" class="action-btn delete" onclick="deleteSupplier({{ $supplier->id }})">
                            <div class="action-icon">
                                <i class="feather icon-trash-2"></i>
                            </div>
                            <div class="action-text">
                                <span>حذف</span>
                            </div>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tabs Functionality with AJAX Pagination
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            // Function to switch tab
            function switchTab(targetTab) {
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                const activeButton = document.querySelector(`[data-tab="${targetTab}"]`);
                const activeContent = document.getElementById(targetTab);

                if (activeButton && activeContent) {
                    activeButton.classList.add('active');
                    activeContent.classList.add('active');
                    window.location.hash = targetTab;
                }
            }

            // Handle pagination links for each tab
            function setupPaginationHandlers() {
                tabContents.forEach(content => {
                    const tabName = content.getAttribute('data-tab');
                    const paginationLinks = content.querySelectorAll('a[href*="?page="]');

                    paginationLinks.forEach(link => {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();

                            const url = this.getAttribute('href');
                            const page = new URL(url, window.location.origin).searchParams.get('page');

                            // Add loading state
                            content.style.opacity = '0.6';
                            content.style.pointerEvents = 'none';

                            // Fetch the page content via AJAX
                            fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.text())
                            .then(html => {
                                // Parse the response to extract only the tab content
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');

                                // Find the corresponding tab content in the response
                                const newTabContent = doc.querySelector(`#${tabName}`);

                                if (newTabContent) {
                                    // Replace the current tab content
                                    content.innerHTML = newTabContent.innerHTML;

                                    // Re-setup pagination handlers for new content
                                    setupPaginationHandlers();

                                    // Update URL
                                    window.history.pushState({}, '', url);
                                }

                                // Remove loading state
                                content.style.opacity = '1';
                                content.style.pointerEvents = 'auto';
                            })
                            .catch(error => {
                                console.error('Error loading page:', error);
                                content.style.opacity = '1';
                                content.style.pointerEvents = 'auto';
                                alert('حدث خطأ في تحميل البيانات');
                            });
                        });
                    });
                });
            }

            // Switch tabs with event listeners
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');
                    switchTab(targetTab);
                });
            });

            // Initial setup
            setupPaginationHandlers();

            // Check URL hash on page load
            const hash = window.location.hash.slice(1);
            if (hash && document.getElementById(hash)) {
                switchTab(hash);
            }

            // Delete functionality
            const deleteSupplierBtn = document.querySelector('.action-btn.delete');
            if (deleteSupplierBtn) {
                deleteSupplierBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    deleteSupplier({{ $supplier->id }});
                });
            }
        });

        function deleteSupplier(id) {
            if (confirm('⚠️ هل أنت متأكد من حذف هذا المورد؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url('manufacturing/suppliers') }}/' + id;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteInvoice(id) {
            if (confirm('⚠️ هل أنت متأكد من حذف هذه الفاتورة؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url('manufacturing/purchase-invoices') }}/' + id;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteDeliveryNote(id) {
            if (confirm('⚠️ هل أنت متأكد من حذف هذه الأذن؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url('manufacturing/delivery-notes') }}/' + id;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
