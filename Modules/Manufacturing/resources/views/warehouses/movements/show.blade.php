@extends('master')

@section('title', 'تفاصيل حركة المادة')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style-material.css') }}">

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
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="header-info">
                        <h1>حركة المادة #{{ $movement->movement_number }}</h1>
                        <div class="badges">
                            <span class="badge badge-{{ 
                                $movement->movement_type == 'incoming' ? 'success' : 
                                ($movement->movement_type == 'to_production' ? 'primary' : 
                                ($movement->movement_type == 'adjustment' || $movement->movement_type == 'reconciliation' ? 'warning' : 'secondary')) 
                            }}">
                                {{ $movement->movement_type_name }}
                            </span>
                            <span class="badge badge-info">{{ $movement->movement_date->format('d-m-Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.warehouse.movements.index') }}" class="btn btn-back">
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
                    <h3 class="card-title">معلومات الحركة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">رقم الحركة:</div>
                        <div class="info-value">{{ $movement->movement_number }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">نوع الحركة:</div>
                        <div class="info-value">
                            <span class="badge badge-{{
                                $movement->movement_type == 'incoming' ? 'success' :
                                ($movement->movement_type == 'to_production' ? 'primary' :
                                ($movement->movement_type == 'adjustment' || $movement->movement_type == 'reconciliation' ? 'warning' : 'secondary'))
                            }}">
                                {{ $movement->movement_type_name }}
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">المصدر:</div>
                        <div class="info-value">
                            <span class="badge badge-info">
                                {{ $movement->source_name }}
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الحركة:</div>
                        <div class="info-value">{{ $movement->movement_date->format('Y-m-d H:i') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value">
                            @php
                                $statusClass = '';
                                $statusText = '';
                                switch($movement->status) {
                                    case 'pending':
                                        $statusClass = 'warning';
                                        $statusText = 'معلق';
                                        break;
                                    case 'completed':
                                        $statusClass = 'success';
                                        $statusText = 'مكتمل';
                                        break;
                                    case 'cancelled':
                                        $statusClass = 'danger';
                                        $statusText = 'ملغى';
                                        break;
                                    default:
                                        $statusClass = 'secondary';
                                        $statusText = $movement->status;
                                }
                            @endphp
                            <span class="badge badge-{{ $statusClass }}">{{ $statusText }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">رقم المرجع:</div>
                        <div class="info-value">{{ $movement->reference_number ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المادة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">اسم المادة:</div>
                        <div class="info-value">{{ $movement->material->name_ar ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الوحدة:</div>
                        <div class="info-value">{{ $movement->unit->name_ar ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الكمية:</div>
                        <div class="info-value">{{ number_format($movement->quantity, 2) }} {{ $movement->unit->symbol_ar ?? '' }}</div>
                    </div>

                    @if($movement->unit_price)
                    <div class="info-item">
                        <div class="info-label">سعر الوحدة:</div>
                        <div class="info-value">{{ number_format($movement->unit_price, 2) }}</div>
                    </div>
                    @endif

                    @if($movement->total_value)
                    <div class="info-item">
                        <div class="info-label">القيمة الإجمالية:</div>
                        <div class="info-value">{{ number_format($movement->total_value, 2) }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12h18M3 6h18M3 18h18"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المستودع</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">من المستودع:</div>
                        <div class="info-value">{{ $movement->fromWarehouse->name_ar ?? ($movement->supplier->name ?? 'N/A') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">إلى المستودع:</div>
                        <div class="info-value">{{ $movement->toWarehouse->name_ar ?? $movement->destination ?? 'N/A' }}</div>
                    </div>

                    @if($movement->materialDetail)
                    <div class="info-item">
                        <div class="info-label">موقع التخزين:</div>
                        <div class="info-value">{{ $movement->materialDetail->location_in_warehouse ?? 'N/A' }}</div>
                    </div>
                    @endif
                </div>
            </div>

            @if($movement->deliveryNote)
            <div class="card">
                <div class="card-header">
                    <div class="card-icon info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">بيانات الأذن المرتبطة</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">رقم الأذن:</div>
                        <div class="info-value">{{ $movement->deliveryNote->note_number ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الأذن:</div>
                        <div class="info-value">{{ $movement->deliveryNote->delivery_date->format('Y-m-d') ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">النوع:</div>
                        <div class="info-value">
                            @if($movement->deliveryNote->type === 'incoming')
                                <span class="badge badge-success">واردة</span>
                            @else
                                <span class="badge badge-warning">صادرة</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($movement->reconciliationLog)
            <div class="card">
                <div class="card-header">
                    <div class="card-icon danger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">بيانات التسوية</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">الوزن الفعلي:</div>
                        <div class="info-value">{{ number_format($movement->reconciliationLog->actual_weight, 2) }} كجم</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">وزن الفاتورة:</div>
                        <div class="info-value">{{ number_format($movement->reconciliationLog->invoice_weight, 2) }} كجم</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الفرق:</div>
                        <div class="info-value">{{ number_format($movement->reconciliationLog->discrepancy, 2) }} كجم</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value">
                            <span class="badge badge-{{
                                $movement->reconciliationLog->reconciliation_status === 'matched' ? 'success' : 
                                ($movement->reconciliationLog->reconciliation_status === 'discrepancy' ? 'warning' : 'secondary')
                            }}">
                                {{ $movement->reconciliationLog->reconciliation_status === 'matched' ? 'مطابق' : 'اختلاف' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات التسجيل</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">سجل بواسطة:</div>
                        <div class="info-value">{{ $movement->createdBy->name ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الموافقة بواسطة:</div>
                        <div class="info-value">{{ $movement->approvedBy->name ?? 'لم تتم الموافقة' }}</div>
                    </div>

                    @if($movement->approved_at)
                    <div class="info-item">
                        <div class="info-label">تاريخ الموافقة:</div>
                        <div class="info-value">{{ $movement->approved_at->format('Y-m-d H:i') }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">عنوان IP:</div>
                        <div class="info-value">{{ $movement->ip_address ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تم الإنشاء:</div>
                        <div class="info-value">{{ $movement->created_at->format('Y-m-d H:i:s') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">آخر تحديث:</div>
                        <div class="info-value">{{ $movement->updated_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                </div>
            </div>

            @if($movement->description || $movement->notes)
            <div class="card">
                <div class="card-header">
                    <div class="card-icon info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="8" y1="6" x2="21" y2="6"></line>
                            <line x1="8" y1="12" x2="21" y2="12"></line>
                            <line x1="8" y1="18" x2="21" y2="18"></line>
                            <line x1="3" y1="6" x2="3.01" y2="6"></line>
                            <line x1="3" y1="12" x2="3.01" y2="12"></line>
                            <line x1="3" y1="18" x2="3.01" y2="18"></line>
                        </svg>
                    </div>
                    <h3 class="card-title">الوصف والملاحظات</h3>
                </div>
                <div class="card-body">
                    @if($movement->description)
                    <div class="info-item">
                        <div class="info-label">الوصف:</div>
                        <div class="info-value">{{ $movement->description }}</div>
                    </div>
                    @endif

                    @if($movement->notes)
                    <div class="info-item">
                        <div class="info-label">الملاحظات:</div>
                        <div class="info-value">{{ $movement->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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