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
                <div class="card-icon success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="16 16 12 12 8 16"></polyline>
                        <line x1="12" y1="12" x2="12" y2="21"></line>
                        <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path>
                        <polyline points="16 16 12 12 8 16"></polyline>
                    </svg>
                </div>
                <h3 class="card-title">حركات المواد على المستودع</h3>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <form method="GET" action="{{ route('manufacturing.warehouses.show', $warehouse->id) }}">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                            <div>
                                <label style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px; display: block; font-weight: 600;">نوع الحركة</label>
                                <select name="movement_type" class="form-control" style="padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                                    <option value="">-- جميع الأنواع --</option>
                                    <option value="incoming" {{ request('movement_type') == 'incoming' ? 'selected' : '' }}>دخول بضاعة</option>
                                    <option value="outgoing" {{ request('movement_type') == 'outgoing' ? 'selected' : '' }}>خروج بضاعة</option>
                                    <option value="transfer" {{ request('movement_type') == 'transfer' ? 'selected' : '' }}>نقل بين مستودعات</option>
                                    <option value="to_production" {{ request('movement_type') == 'to_production' ? 'selected' : '' }}>نقل للإنتاج</option>
                                    <option value="from_production" {{ request('movement_type') == 'from_production' ? 'selected' : '' }}>إرجاع من الإنتاج</option>
                                    <option value="adjustment" {{ request('movement_type') == 'adjustment' ? 'selected' : '' }}>تسوية</option>
                                    <option value="reconciliation" {{ request('movement_type') == 'reconciliation' ? 'selected' : '' }}>تعديل بعد التسوية</option>
                                    <option value="waste" {{ request('movement_type') == 'waste' ? 'selected' : '' }}>هدر</option>
                                    <option value="return" {{ request('movement_type') == 'return' ? 'selected' : '' }}>إرجاع للمورد</option>
                                </select>
                            </div>

                            <div>
                                <label style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px; display: block; font-weight: 600;">المصدر</label>
                                <select name="source" class="form-control" style="padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                                    <option value="">-- جميع المصادر --</option>
                                    <option value="registration" {{ request('source') == 'registration' ? 'selected' : '' }}>تسجيل البضاعة</option>
                                    <option value="reconciliation" {{ request('source') == 'reconciliation' ? 'selected' : '' }}>التسوية</option>
                                    <option value="production" {{ request('source') == 'production' ? 'selected' : '' }}>الإنتاج</option>
                                    <option value="transfer" {{ request('source') == 'transfer' ? 'selected' : '' }}>نقل بين مستودعات</option>
                                    <option value="manual" {{ request('source') == 'manual' ? 'selected' : '' }}>تعديل يدوي</option>
                                    <option value="system" {{ request('source') == 'system' ? 'selected' : '' }}>النظام</option>
                                </select>
                            </div>

                            <div>
                                <label style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px; display: block; font-weight: 600;">من تاريخ</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" style="padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                            </div>

                            <div>
                                <label style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px; display: block; font-weight: 600;">إلى تاريخ</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" style="padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                            </div>

                            <div style="display: flex; gap: 10px;">
                                <button type="submit" style="flex: 1; background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                                    <i class="feather icon-filter"></i> فلترة
                                </button>
                                <a href="{{ route('manufacturing.warehouses.show', $warehouse->id) }}" style="background: #95a5a6; color: white; border: none; padding: 10px 15px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s;">
                                    <i class="feather icon-refresh-cw"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                @php
                    $query = \App\Models\MaterialMovement::where(function($q) use ($warehouse) {
                        $q->where('from_warehouse_id', $warehouse->id)
                          ->orWhere('to_warehouse_id', $warehouse->id);
                    })
                    ->with(['material', 'unit', 'fromWarehouse', 'toWarehouse', 'supplier', 'createdBy']);

                    // Apply filters
                    if (request('movement_type')) {
                        $query->where('movement_type', request('movement_type'));
                    }

                    if (request('source')) {
                        $query->where('source', request('source'));
                    }

                    if (request('date_from')) {
                        $query->whereDate('movement_date', '>=', request('date_from'));
                    }

                    if (request('date_to')) {
                        $query->whereDate('movement_date', '<=', request('date_to'));
                    }

                    $materialMovements = $query->orderBy('created_at', 'desc')->paginate(20);
                @endphp

                @if($materialMovements->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table" style="margin: 0;">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">رقم الحركة</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">نوع الحركة</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">المادة</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">الكمية</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">من</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">إلى</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">المصدر</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">التاريخ</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">المستخدم</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600; text-align: center;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($materialMovements as $movement)
                                    <tr style="border-bottom: 1px solid #e9ecef;">
                                        <td style="padding: 12px; font-size: 13px; color: #2c3e50;">
                                            <code style="background: #f0f2f5; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                                                {{ $movement->movement_number }}
                                            </code>
                                        </td>
                                        <td style="padding: 12px;">
                                            @php
                                                $typeColors = [
                                                    'incoming' => '#27ae60',
                                                    'outgoing' => '#e74c3c',
                                                    'transfer' => '#3498db',
                                                    'to_production' => '#f39c12',
                                                    'from_production' => '#9b59b6',
                                                    'adjustment' => '#95a5a6',
                                                    'reconciliation' => '#1abc9c',
                                                    'waste' => '#e67e22',
                                                    'return' => '#34495e',
                                                ];
                                                $color = $typeColors[$movement->movement_type] ?? '#95a5a6';
                                            @endphp
                                            <span style="background: {{ $color }}; color: white; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; white-space: nowrap;">
                                                {{ $movement->movement_type_name }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px; font-size: 13px; color: #2c3e50; font-weight: 500;">
                                            {{ $movement->material->name_ar ?? '-' }}
                                            @if($movement->material?->name_en)
                                                <br><small style="color: #7f8c8d;">{{ $movement->material->name_en }}</small>
                                            @endif
                                        </td>
                                        <td style="padding: 12px; font-size: 14px; font-weight: 600;">
                                            <span style="color: #3498db;">{{ number_format($movement->quantity, 2) }}</span>
                                            <small style="color: #7f8c8d;">{{ $movement->unit?->unit_symbol ?? '' }}</small>
                                        </td>
                                        <td style="padding: 12px; font-size: 12px; color: #2c3e50;">
                                            @if($movement->fromWarehouse)
                                                <span style="background: #ecf0f1; padding: 3px 8px; border-radius: 4px;">
                                                    {{ $movement->fromWarehouse->warehouse_name }}
                                                </span>
                                            @elseif($movement->supplier)
                                                <span style="background: #e8f5e9; padding: 3px 8px; border-radius: 4px; color: #27ae60;">
                                                    {{ $movement->supplier->name }}
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
                                        <td style="padding: 12px; font-size: 11px;">
                                            <span style="background: #f8f9fa; padding: 3px 8px; border-radius: 4px; color: #7f8c8d;">
                                                {{ $movement->source_name }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px; font-size: 12px; color: #7f8c8d;">
                                            {{ $movement->movement_date ? $movement->movement_date->format('Y-m-d H:i') : $movement->created_at->format('Y-m-d H:i') }}
                                            <br>
                                            <small style="color: #95a5a6; font-size: 10px;">{{ $movement->created_at->diffForHumans() }}</small>
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
                                عرض <strong>{{ $materialMovements->firstItem() }}</strong> إلى <strong>{{ $materialMovements->lastItem() }}</strong> من أصل <strong>{{ $materialMovements->total() }}</strong> حركة
                            </div>
                            <div>
                                {{ $materialMovements->appends(request()->query())->links() }}
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

    <!-- Modal for Movement Details -->
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

    <style>
        /* Button Hover */
        .view-movement-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
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

        /* Filter Styles */
        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        button[type="submit"]:hover {
            background: #2980b9 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
        }

        a[href*="refresh"]:hover {
            background: #7f8c8d !important;
            transform: rotate(180deg);
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 5px;
        }

        .pagination li {
            display: inline-block;
        }

        .pagination li a,
        .pagination li span {
            display: block;
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            color: #2c3e50;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .pagination li a:hover {
            background: #3498db;
            color: white;
            border-color: #3498db;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
        }

        .pagination li.active span {
            background: #3498db;
            color: white;
            border-color: #3498db;
            font-weight: 700;
        }

        .pagination li.disabled span {
            background: #ecf0f1;
            color: #95a5a6;
            cursor: not-allowed;
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

        /* Responsive */
        @media (max-width: 768px) {
            div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <script>
        // View Movement Details
        function viewMovementDetails(movementId) {
            const modal = document.getElementById('movementDetailsModal');
            const content = document.getElementById('movementDetailsContent');

            modal.style.display = 'flex';
            content.innerHTML = '<div style="text-align: center; padding: 40px;"><div class="spinner-border text-primary" role="status"><span class="sr-only">جاري التحميل...</span></div></div>';

            // Fetch movement details
            fetch(`/manufacturing/material-movements/${movementId}`)
                .then(response => response.json())
                .then(data => {
                    const movement = data.movement;

                    const typeColors = {
                        'incoming': '#27ae60',
                        'outgoing': '#e74c3c',
                        'transfer': '#3498db',
                        'to_production': '#f39c12',
                        'from_production': '#9b59b6',
                        'adjustment': '#95a5a6',
                        'reconciliation': '#1abc9c',
                        'waste': '#e67e22',
                        'return': '#34495e'
                    };

                    const color = typeColors[movement.movement_type] || '#95a5a6';

                    content.innerHTML = `
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-right: 4px solid ${color};">
                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">رقم الحركة</div>
                                <div style="font-size: 16px; font-weight: 700; color: #2c3e50;">
                                    <code style="background: white; padding: 6px 10px; border-radius: 4px;">${movement.movement_number}</code>
                                </div>
                            </div>

                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-right: 4px solid ${color};">
                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">نوع الحركة</div>
                                <div style="font-size: 16px; font-weight: 700;">
                                    <span style="background: ${color}; color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px;">${movement.movement_type_name}</span>
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
                                    <div style="font-size: 14px; font-weight: 600; color: #2c3e50;">${movement.material_name || '-'}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">الكمية</div>
                                    <div style="font-size: 18px; font-weight: 700; color: #3498db;">${movement.quantity} <small style="color: #7f8c8d; font-size: 13px;">${movement.unit_symbol || ''}</small></div>
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
                                    <div style="font-size: 14px; font-weight: 600; color: #2c3e50;">${movement.from_location || '-'}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 5px; font-weight: 600;">إلى</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #2c3e50;">${movement.to_location || '-'}</div>
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
                                    <div style="font-weight: 600; color: #2c3e50;">${movement.source_name}</div>
                                </div>
                                <div>
                                    <div style="color: #7f8c8d; margin-bottom: 3px; font-weight: 600;">التاريخ</div>
                                    <div style="font-weight: 600; color: #2c3e50;">${movement.movement_date || movement.created_at}</div>
                                </div>
                                <div>
                                    <div style="color: #7f8c8d; margin-bottom: 3px; font-weight: 600;">المستخدم</div>
                                    <div style="font-weight: 600; color: #2c3e50;">${movement.created_by_name || '-'}</div>
                                </div>
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
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
            document.getElementById('movementDetailsModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('movementDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMovementModal();
            }
        });

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
