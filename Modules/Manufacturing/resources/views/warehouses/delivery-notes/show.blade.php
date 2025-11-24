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
                    <a href="{{ route('manufacturing.delivery-notes.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        العودة
                    </a>

                    @if($deliveryNote->type === 'incoming')
                        <a href="{{ route('manufacturing.warehouse.registration.transfer-form', $deliveryNote) }}" class="btn btn-success">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3"></polyline>
                                <line x1="12" y1="12" x2="20" y2="7.5"></line>
                                <line x1="12" y1="12" x2="12" y2="21"></line>
                                <line x1="12" y1="12" x2="4" y2="7.5"></line>
                            </svg>
                            نقل للإنتاج
                        </a>

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
                    @endif

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



          @if ($deliveryNote->isIncoming() && ($deliveryNote->quantity > 0))
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
                        @php
                            // استخدام البيانات من DeliveryNote مباشرة
                            $registeredQuantity = $deliveryNote->quantity ?? 0;
                            $transferredQuantity = $deliveryNote->quantity_used ?? 0;
                            $remainingQuantity = $deliveryNote->quantity_remaining ?? 0;

                            // حساب النسبة المئوية
                            $transferPercentage = $registeredQuantity > 0 ? ($transferredQuantity / $registeredQuantity * 100) : 0;

                            // تحديد لون الحالة بناءً على النسبة الفعلية
                            $statusColor = 'success';
                            $statusColorStart = '#27ae60';
                            $statusColorEnd = '#229954';
                            $statusLabel = '✅ تم النقل بالكامل';

                            if ($transferPercentage == 0) {
                                $statusColor = 'warning';
                                $statusColorStart = '#e74c3c';
                                $statusColorEnd = '#c0392b';
                                $statusLabel = '⏳ لم يتم النقل بعد';
                            } elseif ($transferPercentage < 100) {
                                $statusColor = 'info';
                                $statusColorStart = '#3498db';
                                $statusColorEnd = '#2980b9';
                                $statusLabel = '⚡ نقل جزئي';
                            }
                        @endphp

                        <!-- الحالة الإجمالية -->
                        <div style="background: linear-gradient(135deg, {{ $statusColorStart }} 0%, {{ $statusColorEnd }} 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="font-size: 12px; opacity: 0.9; margin-bottom: 5px;">حالة البضاعة:</div>
                                    <div style="font-size: 20px; font-weight: bold;">{{ $statusLabel }}</div>
                                </div>
                                <div style="text-align: center;">
                                    <div style="font-size: 30px; font-weight: bold; margin-bottom: 5px;">{{ number_format($transferPercentage, 1) }}%</div>
                                    <div style="font-size: 12px; opacity: 0.9;">نسبة النقل</div>
                                </div>
                            </div>
                        </div>

                        <!-- الكميات -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                            <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; border-right: 4px solid #27ae60;">
                                <div style="font-size: 12px; color: #666; margin-bottom: 5px; font-weight: 600;">
                                    📥 الكمية الواردة (المسجلة):
                                </div>
                                <div style="font-size: 18px; font-weight: bold; color: #27ae60;">
                                    {{ number_format($registeredQuantity, 2) }}
                                    @if($deliveryNote->material && $deliveryNote->material->materialDetails->first() && $deliveryNote->material->materialDetails->first()->unit)
                                        {{ $deliveryNote->material->materialDetails->first()->unit->name ?? 'وحدة' }}
                                    @else
                                        وحدة
                                    @endif
                                </div>
                            </div>

                            <div style="background: #fff3e0; padding: 15px; border-radius: 8px; border-right: 4px solid #ff9800;">
                                <div style="font-size: 12px; color: #666; margin-bottom: 5px; font-weight: 600;">
                                    🏭 المنقول للإنتاج:
                                </div>
                                <div style="font-size: 18px; font-weight: bold; color: #ff9800;">
                                    {{ number_format($transferredQuantity, 2) }}
                                    @if($deliveryNote->material && $deliveryNote->material->materialDetails->first() && $deliveryNote->material->materialDetails->first()->unit)
                                        {{ $deliveryNote->material->materialDetails->first()->unit->name ?? 'وحدة' }}
                                    @else
                                        وحدة
                                    @endif
                                </div>
                            </div>

                            <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; border-right: 4px solid #3498db;">
                                <div style="font-size: 12px; color: #666; margin-bottom: 5px; font-weight: 600;">
                                    📦 المتبقي في المستودع:
                                </div>
                                <div style="font-size: 18px; font-weight: bold; color: #3498db;">
                                    {{ number_format($remainingQuantity, 2) }}
                                    @if($deliveryNote->material && $deliveryNote->material->materialDetails->first() && $deliveryNote->material->materialDetails->first()->unit)
                                        {{ $deliveryNote->material->materialDetails->first()->unit->name ?? 'وحدة' }}
                                    @else
                                        وحدة
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- شريط التقدم -->
                        <div style="margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <label style="font-weight: 600; color: #2c3e50;">📊 تقدم النقل للإنتاج:</label>
                                <span style="font-weight: 600; color: #3498db;">{{ number_format($transferPercentage, 1) }}%</span>
                            </div>
                            <div class="progress" style="height: 30px; border-radius: 4px;">
                                <div class="progress-bar" style="width: {{ min($transferPercentage, 100) }}%; background: linear-gradient(90deg, #27ae60 0%, #2ecc71 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    @if($transferPercentage > 10){{ number_format($transferredQuantity, 1) }}@endif
                                </div>
                            </div>
                        </div>

                        <!-- التواريخ والتفاصيل -->
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="font-weight: 600; color: #2c3e50; margin-bottom: 12px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
                                📅 سجل الحركات:
                            </div>

                            @if ($deliveryNote->registered_at)
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #e9ecef;">
                                    <div style="width: 10px; height: 10px; background: #3498db; border-radius: 50%;"></div>
                                    <div style="flex: 1;">
                                        <div style="font-size: 12px; color: #999;">📥 تسجيل الكمية من الكريت:</div>
                                        <div style="font-weight: 600; color: #2c3e50;">{{ $deliveryNote->registered_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                    <div style="font-size: 11px; color: #999;">بواسطة: {{ $deliveryNote->registeredBy?->name ?? 'N/A' }}</div>
                                </div>
                            @endif

                            @if ($deliveryNote->quantity_used && $deliveryNote->quantity_used > 0)
                                <div style="display: flex; align-items: center; gap: 10px; padding-bottom: 10px;">
                                    <div style="width: 10px; height: 10px; background: #27ae60; border-radius: 50%;"></div>
                                    <div style="flex: 1;">
                                        <div style="font-size: 12px; color: #999;">🏭 بدء نقل للإنتاج:</div>
                                        <div style="font-weight: 600; color: #2c3e50;">
                                            @if($deliveryNote->registrationLogs && $deliveryNote->registrationLogs->count() > 0)
                                                {{ $deliveryNote->registrationLogs->first()->created_at?->format('d/m/Y H:i') ?? 'معرّف' }}
                                            @else
                                                معرّف
                                            @endif
                                        </div>
                                    </div>
                                    <div style="font-size: 11px; color: #999;">بواسطة: النظام</div>
                                </div>
                            @endif
                        </div>


                    </div>
                </div>
            @endif
            <!-- حالة التسوية -->
            {{-- <div class="card">
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
                            <span class="-{{ $deliveryNote->reconciliation_status === 'matched' ? 'success' : ($deliveryNote->reconciliation_status === 'discrepancy' ? 'warning' : 'info') }}">
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
            </div> --}}

            <!-- معلومات المستودع والنقل للإنتاج -->



            </div>



            <!-- معلومات التسجيل والموافقة - في صفوف -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <!-- بطاقة من سجل -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon info">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <h3 class="card-title">سجل بواسطة</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label">الاسم:</div>
                            <div class="info-value" style="color: #000000; font-weight: 600;">{{ $deliveryNote->recordedBy->name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">تاريخ الإنشاء:</div>
                            <div class="info-value" style="color: #000000; font-weight: 600;">{{ $deliveryNote->created_at->format('d-m-Y H:i:s') }}</div>
                        </div>
                    </div>
                </div>

                <!-- بطاقة استقبل -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon success">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <h3 class="card-title">استقبل</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label">الاسم:</div>
                            <div class="info-value" style="color: #000000; font-weight: 600;">{{ $deliveryNote->receiver->name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">آخر تحديث:</div>
                            <div class="info-value" style="color: #000000; font-weight: 600;">{{ $deliveryNote->updated_at->format('d-m-Y H:i:s') }}</div>
                        </div>
                    </div>
                </div>

                <!-- بطاقة وافق -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon warning">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <h3 class="card-title">وافق</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label">الاسم:</div>
                            <div class="info-value" style="color: #000000; font-weight: 600;">{{ $deliveryNote->approvedBy->name ?? 'لم يتم الموافقة' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">تاريخ الموافقة:</div>
                            <div class="info-value" style="color: #000000; font-weight: 600;">{{ $deliveryNote->approved_at ? $deliveryNote->approved_at->format('d-m-Y H:i') : 'لم يتم تعيين' }}</div>
                        </div>
                    </div>
                </div>

                <!-- بطاقة ملخص -->

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

    <!-- سجلات التسجيل - في صفوف -->
    @if ($deliveryNote->registrationLogs && $deliveryNote->registrationLogs->count() > 0)
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;">
            @foreach ($deliveryNote->registrationLogs as $log)
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <h3 class="card-title">سجل التسجيل</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label">الموقع:</div>
                            <div class="info-value" style="color: #000000; font-weight: 600;">{{ $log->location ?? 'N/A' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">الوزن المسجل:</div>
                            <div class="info-value">
                                <span class="badge badge-success">{{ number_format($log->weight_recorded ?? 0, 2) }} كيلو</span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">سجل بواسطة:</div>
                            <div class="info-value" style="color: #000000; font-weight: 600;">{{ $log->registeredBy?->name ?? 'مستخدم محذوف' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">التاريخ والوقت:</div>
                            <div class="info-value" style="color: #000000; font-weight: 600;">{{ $log->registered_at?->format('d/m/Y H:i:s') ?? 'N/A' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">قبل:</div>
                            <div class="info-value" style="color: #000000; font-weight: 600;">{{ $log->registered_at?->diffForHumans() ?? 'N/A' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">IP Address:</div>
                            <div class="info-value">
                                <code style="background: #f0f2f5; padding: 6px 10px; border-radius: 3px; font-size: 12px; font-weight: 600; color: #000000;">
                                    {{ $log->ip_address ?? 'N/A' }}
                                </code>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
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

    <script>
@endsection
