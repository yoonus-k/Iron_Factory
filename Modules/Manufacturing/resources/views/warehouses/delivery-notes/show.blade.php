@extends('master')

@section('title', 'تفاصيل أذن التسليم')

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
        .dropdown-menu .dropdown-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
        }
        .dropdown-menu .dropdown-item.active {
            background-color: #667eea;
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
                        <i class="feather icon-file-text"></i>
                    </div>
                    <div class="header-info">
                        <h1>أذن تسليم #{{ $deliveryNote->note_number }}</h1>
                        <div class="badges">
                            <span class="badge badge-{{ $deliveryNote->type === 'incoming' ? 'success' : 'warning' }}">
                                {{ $deliveryNote->type === 'incoming' ? '🔽 واردة' : '🔼 صادرة' }}
                            </span>
                            <span class="badge badge-info">{{ $deliveryNote->delivery_date->format('d-m-Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.delivery-notes.edit', $deliveryNote->id) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        تعديل
                    </a>

                    <!-- تغيير الحالة (Status) في الـ Header -->
                    <div class="dropdown">
                        <button class="btn" type="button" data-bs-toggle="dropdown" title="تغيير حالة الأذن">
                            @php
                                $statusColor = $deliveryNote->status->color();
                                $colorCode = $statusColor === 'yellow' ? '#f39c12' : ($statusColor === 'green' ? '#27ae60' : ($statusColor === 'red' ? '#e74c3c' : '#3498db'));
                            @endphp
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: {{ $colorCode }}; margin-left: 6px;"></span>
                            {{ $deliveryNote->status->label() }}
                        </button>
                        <ul class="dropdown-menu">
                            @foreach(\App\Models\DeliveryNoteStatus::cases() as $status)
                                @php
                                    $btnColor = $status->color();
                                    $btnColorCode = $btnColor === 'yellow' ? '#f39c12' : ($btnColor === 'green' ? '#27ae60' : ($btnColor === 'red' ? '#e74c3c' : '#3498db'));
                                @endphp
                                <li>
                                    <form method="POST" action="{{ route('manufacturing.delivery-notes.update-status', $deliveryNote->id) }}" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="{{ $status->value }}">
                                        <button type="submit" class="dropdown-item {{ $deliveryNote->status === $status ? 'active' : '' }}" style="padding: 10px 15px;">
                                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: {{ $btnColorCode }}; margin-left: 8px;"></span>
                                            {{ $status->label() }}
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <a href="{{ route('manufacturing.delivery-notes.index') }}" class="btn btn-back">
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
                    <h3 class="card-title">معلومات أذن التسليم</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">رقم الأذن:</div>
                        <div class="info-value">{{ $deliveryNote->note_number }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">النوع:</div>
                        <div class="info-value">
                            @if($deliveryNote->type === 'incoming')
                                <span class="badge badge-success">🔽 واردة (من المورد)</span>
                            @else
                                <span class="badge badge-warning">🔼 صادرة (للزبون)</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ التسليم:</div>
                        <div class="info-value">{{ $deliveryNote->delivery_date->format('Y-m-d') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الكمية المسلمة:</div>
                        <div class="info-value">{{ number_format($deliveryNote->delivery_quantity ?? 0, 2) }} وحدة</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value">
                            @php
                                $statusColor = $deliveryNote->status->color();
                                $bgColor = $statusColor === 'yellow' ? '#fff3cd' : ($statusColor === 'green' ? '#d4edda' : ($statusColor === 'red' ? '#f8d7da' : '#d1ecf1'));
                                $textColor = $statusColor === 'yellow' ? '#856404' : ($statusColor === 'green' ? '#155724' : ($statusColor === 'red' ? '#721c24' : '#0c5460'));
                                $dotColor = $statusColor === 'yellow' ? '#f39c12' : ($statusColor === 'green' ? '#27ae60' : ($statusColor === 'red' ? '#e74c3c' : '#3498db'));
                            @endphp
                            <span style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 6px; background-color: {{ $bgColor }}; color: {{ $textColor }}; font-weight: 500; width: fit-content;">
                                <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: {{ $dotColor }};"></span>
                                {{ $deliveryNote->status->label() }}
                            </span>
                        </div>
                    </div>

                    @if ($deliveryNote->is_active ?? true)
                        <div class="info-item">
                            <div class="info-label">النشاط:</div>
                            <div class="info-value">
                                <span class="badge badge-success">✓ فعّالة</span>
                            </div>
                        </div>
                    @else
                        <div class="info-item">
                            <div class="info-label">النشاط:</div>
                            <div class="info-value">
                                <span class="badge badge-warning">⚠ معطّلة</span>
                            </div>
                        </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">رقم الفاتورة:</div>
                        <div class="info-value">{{ $deliveryNote->invoice_number ?? $deliveryNote->invoice_reference_number ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المادة والمستودع</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">اسم المادة:</div>
                        <div class="info-value">{{ $deliveryNote->material->name_ar ?? 'N/A' }}</div>
                    </div>

                    @if($deliveryNote->materialDetail)
                        <div class="info-item">
                            <div class="info-label">المستودع:</div>
                            <div class="info-value">{{ $deliveryNote->materialDetail->warehouse->warehouse_name ?? 'N/A' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">موقع التخزين:</div>
                            <div class="info-value">{{ $deliveryNote->materialDetail->location_in_warehouse ?? 'N/A' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">الوحدة:</div>
                            <div class="info-value">{{ $deliveryNote->materialDetail->unit->unit_name ?? 'N/A' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">الكمية الحالية بالمستودع:</div>
                            <div class="info-value">{{ number_format($deliveryNote->materialDetail->quantity, 2) }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">الوزن الحالي:</div>
                            <div class="info-value">{{ number_format($deliveryNote->materialDetail->actual_weight ?? 0, 2) }} كجم</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">الوزن الأصلي:</div>
                            <div class="info-value">{{ number_format($deliveryNote->materialDetail->original_weight ?? 0, 2) }} كجم</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="5" r="3"></circle>
                            <line x1="9" y1="9" x2="9" y2="16"></line>
                            <line x1="15" y1="9" x2="15" y2="16"></line>
                            <path d="M9 16h6"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات الأوزان</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">الوزن الفعلي (من الميزان):</div>
                        <div class="info-value">{{ number_format($deliveryNote->actual_weight, 2) }} كجم</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">وزن الفاتورة:</div>
                        <div class="info-value">{{ number_format($deliveryNote->invoice_weight ?? 0, 2) }} كجم</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الوزن المسلم:</div>
                        <div class="info-value">{{ number_format($deliveryNote->delivered_weight, 2) }} كجم</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الفرق (الفعلي - الفاتورة):</div>
                        <div class="info-value" style="color: {{ $deliveryNote->weight_discrepancy > 0 ? '#27ae60' : ($deliveryNote->weight_discrepancy < 0 ? '#e74c3c' : '#95a5a6') }};">
                            {{ $deliveryNote->weight_discrepancy ? (($deliveryNote->weight_discrepancy > 0 ? '+' : '') . number_format($deliveryNote->weight_discrepancy, 2)) : '0.00' }} كجم
                        </div>
                    </div>
                </div>
            </div>

            @if($deliveryNote->isIncoming())
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon success">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <h3 class="card-title">بيانات المورد</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label">المورد:</div>
                            <div class="info-value">{{ $deliveryNote->supplier->name ?? 'N/A' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">الهاتف:</div>
                            <div class="info-value">{{ $deliveryNote->supplier->phone ?? 'N/A' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">الفاكس:</div>
                            <div class="info-value">{{ $deliveryNote->supplier->fax ?? 'N/A' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">البريد الإلكتروني:</div>
                            <div class="info-value">{{ $deliveryNote->supplier->email ?? 'N/A' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">اسم السائق:</div>
                            <div class="info-value">{{ $deliveryNote->driver_name ?? 'N/A' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">رقم المركبة:</div>
                            <div class="info-value">{{ $deliveryNote->vehicle_number ?? 'N/A' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">رقم الفاتورة المرجعي:</div>
                            <div class="info-value">{{ $deliveryNote->invoice_reference_number ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            @endif

            @if($deliveryNote->isOutgoing())
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon danger">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="card-title">بيانات الوجهة</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label">الوجهة / المستودع:</div>
                            <div class="info-value">{{ $deliveryNote->destination->name ?? 'N/A' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">المستقبل:</div>
                            <div class="info-value">{{ $deliveryNote->receiver->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="card-icon info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات التسجيل والموافقة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">سجل بواسطة:</div>
                        <div class="info-value">{{ $deliveryNote->recordedBy->name ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">استقبل:</div>
                        <div class="info-value">{{ $deliveryNote->receiver->name ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">وافق:</div>
                        <div class="info-value">{{ $deliveryNote->approvedBy->name ?? 'لم يتم الموافقة' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الموافقة:</div>
                        <div class="info-value">{{ $deliveryNote->approved_at ? $deliveryNote->approved_at->format('d-m-Y H:i') : 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تم الإنشاء:</div>
                        <div class="info-value">{{ $deliveryNote->created_at->format('d-m-Y H:i:s') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">آخر تحديث:</div>
                        <div class="info-value">{{ $deliveryNote->updated_at->format('d-m-Y H:i:s') }}</div>
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
                        $operationLogs = $deliveryNote->operationLogs()->orderBy('created_at', 'desc')->get();
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
                                                        @default
                                                            <span class="badge" style="background-color: #95a5a6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                                {{ $log->action_en ?? $log->action }}
                                                            </span>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete confirmation
            const deleteButtons = document.querySelectorAll('.action-btn.delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'تأكيد الحذف',
                            text: 'هل أنت متأكد من حذف هذه الأذن؟ هذا الإجراء لا يمكن التراجع عنه!',
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
                        if (confirm('هل أنت متأكد من حذف هذه الأذن؟ هذا الإجراء لا يمكن التراجع عنه!')) {
                            form.submit();
                        }
                    }
                });
            });

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.um-alert-custom');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
@endsection
