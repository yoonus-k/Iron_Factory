@extends('master')

@section('title', 'تفاصيل المستودع')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-home"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $warehouse->warehouse_name }}</h1>
                        @if($warehouse->warehouse_name_en)
                            <h2 class="text-muted">{{ $warehouse->warehouse_name_en }}</h2>
                        @endif
                        <div class="badges">
                            <span class="badge category">
                                {{ $warehouse->warehouse_code }}
                            </span>
                            <span class="badge {{ $warehouse->is_active ? 'active' : 'inactive' }}">
                                {{ $warehouse->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.warehouses.edit', $warehouse->id) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        تعديل
                    </a>
                    <form method="POST" action="{{ route('manufacturing.warehouses.toggle-status', $warehouse->id) }}" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn" style="background-color: {{ $warehouse->is_active ? '#e74c3c' : '#27ae60' }}; color: white; border: none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            {{ $warehouse->is_active ? 'تعطيل' : 'تفعيل' }}
                        </button>
                    </form>
                    <a href="{{ route('manufacturing.warehouses.index') }}" class="btn btn-back">
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
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">معلومات المستودع</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">اسم المستودع:</div>
                        <div class="info-value">{{ $warehouse->warehouse_name }}</div>
                    </div>

                    @if($warehouse->warehouse_name_en)
                    <div class="info-item">
                        <div class="info-label">Warehouse Name:</div>
                        <div class="info-value">{{ $warehouse->warehouse_name_en }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">رمز المستودع:</div>
                        <div class="info-value">{{ $warehouse->warehouse_code }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الموقع:</div>
                        <div class="info-value">{{ $warehouse->location ?? 'غير محدد' }}</div>
                    </div>

                    @if($warehouse->location_en)
                    <div class="info-item">
                        <div class="info-label">Location:</div>
                        <div class="info-value">{{ $warehouse->location_en }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">السعة التخزينية:</div>
                        <div class="info-value">{{ $warehouse->capacity ?? 'غير محدد' }} متر مكعب</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">المسؤول:</div>
                        <div class="info-value">{{ $warehouse->manager_name ?? 'غير محدد' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">رقم الهاتف:</div>
                        <div class="info-value">{{ $warehouse->contact_number ?? 'غير محدد' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">البريد الإلكتروني:</div>
                        <div class="info-value">{{ $warehouse->email ?? 'غير محدد' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الحالة:</div>
                        <div class="info-value">
                            <span class="badge {{ $warehouse->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $warehouse->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الإنشاء:</div>
                        <div class="info-value">{{ $warehouse->created_at->format('Y-m-d') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ التحديث:</div>
                        <div class="info-value">{{ $warehouse->updated_at->format('Y-m-d') }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6-6 6 6"></path>
                            <path d="M6 9v10a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V9"></path>
                            <line x1="9" y1="14" x2="15" y2="14"></line>
                        </svg>
                    </div>
                    <h3 class="card-title">المنتجات في المستودع</h3>
                </div>
                <div class="card-body">
                    @php
                        $materialDetails = $warehouse->materialDetails()->with(['material', 'unit'])->get();
                    @endphp

                    @if($materialDetails->isNotEmpty())
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                            @foreach($materialDetails as $detail)
                                <div style="border: 1px solid #e9ecef; border-radius: 8px; padding: 15px; background: #f8f9fa;">
                                    <div style="margin-bottom: 12px;">
                                        <h5 style="margin: 0 0 4px 0; color: #2c3e50; font-size: 14px; font-weight: 600;">
                                            {{ $detail->material->name_ar ?? '-' }}
                                        </h5>
                                        @if($detail->material->name_en)
                                            <small style="color: #7f8c8d;">{{ $detail->material->name_en }}</small>
                                        @endif
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                                        <div>
                                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 4px;">الكمية</div>
                                            <div style="font-size: 16px; font-weight: 600; color: #3498db;">
                                                {{ $detail->quantity }}
                                            </div>
                                        </div>
                                        <div>
                                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 4px;">الوحدة</div>
                                            <span style="background: #3498db; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                {{ $detail->unit?->unit_symbol ?? '-' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                                        <div>
                                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 4px;">الوزن الأصلي</div>
                                            <div style="font-size: 14px; font-weight: 500; color: #2c3e50;">
                                                {{ $detail->original_weight ?? '-' }}
                                            </div>
                                        </div>
                                        <div>
                                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 4px;">الوزن المتبقي</div>
                                            <div style="font-size: 14px; font-weight: 500; color: #27ae60;">
                                                {{ $detail->remaining_weight ?? '-' }}
                                            </div>
                                        </div>
                                    </div>

                                    @if($detail->location_in_warehouse)
                                        <div style="border-top: 1px solid #e9ecef; padding-top: 12px;">
                                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 4px;">الموقع في المستودع</div>
                                            <div style="font-size: 13px; color: #2c3e50;">
                                                {{ $detail->location_in_warehouse }}
                                            </div>
                                        </div>
                                    @endif
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
                    $operationLogs = $warehouse->operationLogs()->orderBy('created_at', 'desc')->get();
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
                    @if($warehouse->contact_number)
                    <button type="button" class="action-btn contact">
                        <div class="action-icon">
                            <i class="feather icon-phone"></i>
                        </div>
                        <div class="action-text">
                            <span>اتصل</span>
                        </div>
                    </button>
                    @endif

                    @if($warehouse->email)
                    <button type="button" class="action-btn email">
                        <div class="action-icon">
                            <i class="feather icon-mail"></i>
                        </div>
                        <div class="action-text">
                            <span>إرسال بريد</span>
                        </div>
                    </button>
                    @endif

                    <button type="button" class="action-btn delete">
                        <div class="action-icon">
                            <i class="feather icon-trash-2"></i>
                        </div>
                        <div class="action-text">
                            <span>حذف</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButton = document.querySelector('.action-btn.delete');
            if (deleteButton) {
                deleteButton.addEventListener('click', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: '⚠️ هل أنت متأكد من حذف هذا المستودع؟ هذا الإجراء لا يمكن التراجع عنه!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route("manufacturing.warehouses.destroy", $warehouse->id) }}';
                            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            }

            const contactButton = document.querySelector('.action-btn.contact');
            if (contactButton) {
                contactButton.addEventListener('click', function() {
                    Swal.fire({
                        title: 'رقم الهاتف',
                        text: '{{ $warehouse->contact_number }}',
                        icon: 'info',
                        confirmButtonText: 'موافق'
                    });
                });
            }

            const emailButton = document.querySelector('.action-btn.email');
            if (emailButton) {
                emailButton.addEventListener('click', function() {
                    Swal.fire({
                        title: 'البريد الإلكتروني',
                        text: '{{ $warehouse->email }}',
                        icon: 'info',
                        confirmButtonText: 'موافق'
                    });
                });
            }
        });
    </script>
@endsection
