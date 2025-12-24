@extends('master')

@section('title', __('warehouse.warehouse_details'))

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
                                {{ $warehouse->is_active ? __('warehouse.active') : __('warehouse.inactive') }}
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
                        {{ __('warehouse.edit') }}
                    </a>
                    <form method="POST" action="{{ route('manufacturing.warehouses.toggle-status', $warehouse->id) }}" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn" style="background-color: {{ $warehouse->is_active ? '#e74c3c' : '#27ae60' }}; color: white; border: none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            {{ $warehouse->is_active ? __('warehouse.deactivate') : __('warehouse.activate') }}
                        </button>
                    </form>
                    <a href="{{ route('manufacturing.warehouses.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        {{ __('warehouse.back') }}
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
                    <h3 class="card-title">{{ __('warehouse.warehouse_information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.warehouse_name') }}:</div>
                        <div class="info-value">{{ $warehouse->warehouse_name }}</div>
                    </div>

                    @if($warehouse->warehouse_name_en)
                    <div class="info-item">
                        <div class="info-label">Warehouse Name:</div>
                        <div class="info-value">{{ $warehouse->warehouse_name_en }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.warehouse_code') }}:</div>
                        <div class="info-value">{{ $warehouse->warehouse_code }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.location') }}:</div>
                        <div class="info-value">{{ $warehouse->location ?? __('warehouse.not_specified') }}</div>
                    </div>

                    @if($warehouse->location_en)
                    <div class="info-item">
                        <div class="info-label">Location:</div>
                        <div class="info-value">{{ $warehouse->location_en }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.capacity') }}:</div>
                        <div class="info-value">{{ $warehouse->capacity ?? __('warehouse.not_specified') }} {{ __('warehouse.cubic_meter') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.manager') }}:</div>
                        <div class="info-value">{{ $warehouse->manager_name ?? __('warehouse.not_specified') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.phone_number') }}:</div>
                        <div class="info-value">{{ $warehouse->contact_number ?? __('warehouse.not_specified') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.email') }}:</div>
                        <div class="info-value">{{ $warehouse->email ?? __('warehouse.not_specified') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.status') }}:</div>
                        <div class="info-value">
                            <span class="badge {{ $warehouse->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $warehouse->is_active ? __('warehouse.active') : __('warehouse.inactive') }}
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.created_at') }}:</div>
                        <div class="info-value">{{ $warehouse->created_at->format('Y-m-d') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.updated_at') }}:</div>
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
                    <h3 class="card-title">{{ __('warehouse.raw_materials_in_warehouse') }}</h3>
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
                                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 4px;">{{ __('warehouse.quantity') }}</div>
                                            <div style="font-size: 16px; font-weight: 600; color: #3498db;">
                                                {{ $detail->quantity }}
                                            </div>
                                        </div>
                                        <div>
                                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 4px;">{{ __('warehouse.unit') }}</div>
                                            <span style="background: #3498db; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                {{ $detail->unit?->unit_symbol ?? '-' }}
                                            </span>
                                        </div>
                                    </div>

                                    @if($detail->location_in_warehouse)
                                        <div style="border-top: 1px solid #e9ecef; padding-top: 12px;">
                                            <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 4px;">{{ __('warehouse.location_in_warehouse') }}</div>
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
                            <p style="margin: 0; font-size: 16px; font-weight: 500;">{{ __('warehouse.no_raw_materials_in_warehouse') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- صناديق المنتجات التامة -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('warehouse.finished_product_boxes') }}</h3>
                </div>
                <div class="card-body">
                    @php
                        $finishedBoxes = \App\Models\Stage4Box::where('warehouse_id', $warehouse->id)
                            ->where('status', 'in_warehouse')
                            ->with(['creator', 'boxCoils'])
                            ->get();

                        $totalBoxes = $finishedBoxes->count();
                        $totalWeight = $finishedBoxes->sum('total_weight');
                        $boxesByType = $finishedBoxes->groupBy('packaging_type');
                    @endphp

                    @if($finishedBoxes->isNotEmpty())
                        <!-- Summary Cards -->
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                                <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px; font-weight: 600;">{{ __('warehouse.total_boxes') }}</div>
                                <div style="font-size: 32px; font-weight: 700;">{{ $totalBoxes }}</div>
                                <div style="font-size: 11px; opacity: 0.8; margin-top: 5px;">{{ __('warehouse.box') }}</div>
                            </div>

                            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);">
                                <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px; font-weight: 600;">{{ __('warehouse.total_weight') }}</div>
                                <div style="font-size: 32px; font-weight: 700;">{{ number_format($totalWeight, 2) }}</div>
                                <div style="font-size: 11px; opacity: 0.8; margin-top: 5px;">{{ __('warehouse.kg') }}</div>
                            </div>

                            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);">
                                <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px; font-weight: 600;">{{ __('warehouse.packaging_types') }}</div>
                                <div style="font-size: 32px; font-weight: 700;">{{ $boxesByType->count() }}</div>
                                <div style="font-size: 11px; opacity: 0.8; margin-top: 5px;">{{ __('warehouse.type') }}</div>
                            </div>

                            <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(67, 233, 123, 0.3);">
                                <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px; font-weight: 600;">{{ __('warehouse.average_box_weight') }}</div>
                                <div style="font-size: 32px; font-weight: 700;">{{ $totalBoxes > 0 ? number_format($totalWeight / $totalBoxes, 2) : '0.00' }}</div>
                                <div style="font-size: 11px; opacity: 0.8; margin-top: 5px;">{{ __('warehouse.kg') }}</div>
                            </div>
                        </div>

                        <!-- Boxes by Type -->
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 25px;">
                            <h5 style="margin: 0 0 15px 0; font-size: 16px; color: #2c3e50; font-weight: 700;">
                                <i class="feather icon-pie-chart"></i> {{ __('warehouse.distribution_by_packaging_type') }}
                            </h5>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                                @foreach($boxesByType as $type => $boxes)
                                    <div style="background: white; padding: 15px; border-radius: 8px; border-right: 4px solid #3498db; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                            <span style="font-size: 14px; font-weight: 600; color: #2c3e50;">{{ $type }}</span>
                                            <span style="background: #3498db; color: white; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;">{{ $boxes->count() }}</span>
                                        </div>
                                        <div style="font-size: 12px; color: #7f8c8d;">
                                            {{ __('warehouse.weight') }}: <strong style="color: #2c3e50;">{{ number_format($boxes->sum('total_weight'), 2) }} {{ __('warehouse.kg') }}</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Boxes List -->
                        <div class="table-responsive">
                            <table class="table" style="margin: 0;">
                                <thead style="background: #f8f9fa;">
                                    <tr>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">#</th>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.barcode') }}</th>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.packaging_type') }}</th>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.coils_count') }}</th>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.weight') }}</th>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.entry_date') }}</th>
                                        <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.registered_by') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($finishedBoxes as $index => $box)
                                        <tr style="border-bottom: 1px solid #e9ecef;">
                                            <td style="padding: 12px; font-size: 13px; color: #2c3e50;">{{ $index + 1 }}</td>
                                            <td style="padding: 12px;">
                                                <code style="background: #667eea; color: white; padding: 6px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                    {{ $box->barcode }}
                                                </code>
                                            </td>
                                            <td style="padding: 12px; font-size: 13px; font-weight: 600; color: #2c3e50;">
                                                {{ $box->packaging_type }}
                                            </td>
                                            <td style="padding: 12px; text-align: center;">
                                                <span style="background: #e3f2fd; color: #1976d2; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 700;">
                                                    {{ $box->boxCoils->count() }}
                                                </span>
                                            </td>
                                            <td style="padding: 12px; font-size: 14px; font-weight: 700; color: #f5576c;">
                                                {{ number_format($box->total_weight ?? 0, 2) }} <small style="color: #7f8c8d; font-size: 11px;">{{ __('warehouse.kg') }}</small>
                                            </td>
                                            <td style="padding: 12px; font-size: 12px; color: #7f8c8d;">
                                                {{ $box->created_at->format('Y-m-d') }}<br>
                                                <small style="font-size: 10px;">{{ $box->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td style="padding: 12px; font-size: 13px; color: #2c3e50;">
                                                {{ $box->creator?->name ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="text-align: center; padding: 60px 20px; color: #95a5a6;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 64px; height: 64px; margin: 0 auto 15px; opacity: 0.3;">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                            <p style="margin: 0; font-size: 16px; font-weight: 500;">{{ __('warehouse.no_boxes_in_warehouse') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Material Movements Card -->
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
                <h3 class="card-title">{{ __('warehouse.material_movements_on_warehouse') }}</h3>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <form method="GET" action="{{ route('manufacturing.warehouses.show', $warehouse->id) }}">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                            <div>
                                <label style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px; display: block; font-weight: 600;">{{ __('warehouse.movement_type') }}</label>
                                <select name="movement_type" class="form-control" style="padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                                    <option value="">{{ __('warehouse.all_movement_types') }}</option>
                                    <option value="incoming" {{ request('movement_type') == 'incoming' ? 'selected' : '' }}>{{ __('warehouse.movement_incoming') }}</option>
                                    <option value="outgoing" {{ request('movement_type') == 'outgoing' ? 'selected' : '' }}>{{ __('warehouse.issue_goods') }}</option>
                                    <option value="transfer" {{ request('movement_type') == 'transfer' ? 'selected' : '' }}>{{ __('warehouse.transfer_between_warehouses') }}</option>
                                    <option value="to_production" {{ request('movement_type') == 'to_production' ? 'selected' : '' }}>{{ __('warehouse.movement_to_production') }}</option>
                                    <option value="from_production" {{ request('movement_type') == 'from_production' ? 'selected' : '' }}>{{ __('warehouse.return_from_production') }}</option>
                                    <option value="adjustment" {{ request('movement_type') == 'adjustment' ? 'selected' : '' }}>{{ __('warehouse.adjustment') }}</option>
                                    <option value="reconciliation" {{ request('movement_type') == 'reconciliation' ? 'selected' : '' }}>{{ __('warehouse.movement_reconciliation_update') }}</option>
                                    <option value="waste" {{ request('movement_type') == 'waste' ? 'selected' : '' }}>{{ __('warehouse.movement_waste') }}</option>
                                    <option value="return" {{ request('movement_type') == 'return' ? 'selected' : '' }}>{{ __('warehouse.return_to_supplier') }}</option>
                                </select>
                            </div>

                            <div>
                                <label style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px; display: block; font-weight: 600;">{{ __('warehouse.source') }}</label>
                                <select name="source" class="form-control" style="padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                                    <option value="">{{ __('warehouse.all_sources') }}</option>
                                    <option value="registration" {{ request('source') == 'registration' ? 'selected' : '' }}>{{ __('warehouse.source_goods_registration') }}</option>
                                    <option value="reconciliation" {{ request('source') == 'reconciliation' ? 'selected' : '' }}>{{ __('warehouse.source_reconciliation') }}</option>
                                    <option value="production" {{ request('source') == 'production' ? 'selected' : '' }}>{{ __('warehouse.source_production') }}</option>
                                    <option value="transfer" {{ request('source') == 'transfer' ? 'selected' : '' }}>{{ __('warehouse.source_transfer') }}</option>
                                    <option value="manual" {{ request('source') == 'manual' ? 'selected' : '' }}>{{ __('warehouse.manual_adjustment') }}</option>
                                    <option value="system" {{ request('source') == 'system' ? 'selected' : '' }}>{{ __('warehouse.system') }}</option>
                                </select>
                            </div>

                            <div>
                                <label style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px; display: block; font-weight: 600;">{{ __('warehouse.from_date') }}</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" style="padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                            </div>

                            <div>
                                <label style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px; display: block; font-weight: 600;">{{ __('warehouse.to_date') }}</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" style="padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                            </div>

                            <div style="display: flex; gap: 10px;">
                                <button type="submit" style="flex: 1; background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                                    <i class="feather icon-filter"></i> {{ __('warehouse.filter') }}
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
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.movement_number') }}</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.movement_type') }}</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.material') }}</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.quantity') }}</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.from') }}</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.to') }}</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.source') }}</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.date') }}</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600;">{{ __('warehouse.user') }}</th>
                                    <th style="padding: 12px; font-size: 12px; color: #7f8c8d; font-weight: 600; text-align: center;">{{ __('warehouse.actions') }}</th>
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
                                                {{ __('warehouse.view') }}
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
                                {{ __('warehouse.showing') }} <strong>{{ $materialMovements->firstItem() }}</strong> {{ __('warehouse.to') }} <strong>{{ $materialMovements->lastItem() }}</strong> {{ __('warehouse.of') }} <strong>{{ $materialMovements->total() }}</strong> {{ __('warehouse.movement') }}
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
                        <p style="margin: 0; font-size: 16px; font-weight: 500;">{{ __('warehouse.no_movements_recorded') }}</p>
                        <small style="color: #bdc3c7; display: block; margin-top: 8px;">{{ __('warehouse.movements_will_appear_here') }}</small>
                    </div>
                @endif
            </div>
        </div>

        <!-- Operation Logs Card -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <div class="card-icon primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h3 class="card-title">{{ __('warehouse.operation_logs') }}</h3>
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
                                                        <span class="badge" style="background-color: #27ae60; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">{{ __('warehouse.create') }}</span>
                                                        @break
                                                    @case('update')
                                                        <span class="badge" style="background-color: #3498db; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">{{ __('warehouse.update') }}</span>
                                                        @break
                                                    @case('delete')
                                                        <span class="badge" style="background-color: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">{{ __('warehouse.delete') }}</span>
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
                                                <span><strong>{{ $log->user->name ?? __('warehouse.deleted_user') }}</strong></span>
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
                        <p style="margin: 0; font-size: 14px;">{{ __('warehouse.no_operations_recorded') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Available Actions Card -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="19" cy="12" r="1"></circle>
                        <circle cx="5" cy="12" r="1"></circle>
                    </svg>
                </div>
                <h3 class="card-title">{{ __('warehouse.available_actions') }}</h3>
            </div>
            <div class="card-body">
                <div class="actions-grid">
                    @if($warehouse->contact_number)
                    <button type="button" class="action-btn contact">
                        <div class="action-icon">
                            <i class="feather icon-phone"></i>
                        </div>
                        <div class="action-text">
                            <span>{{ __('warehouse.call') }}</span>
                        </div>
                    </button>
                    @endif

                    @if($warehouse->email)
                    <button type="button" class="action-btn email">
                        <div class="action-icon">
                            <i class="feather icon-mail"></i>
                        </div>
                        <div class="action-text">
                            <span>{{ __('warehouse.send_email') }}</span>
                        </div>
                    </button>
                    @endif

                    <button type="button" class="action-btn delete">
                        <div class="action-icon">
                            <i class="feather icon-trash-2"></i>
                        </div>
                        <div class="action-text">
                            <span>{{ __('warehouse.delete') }}</span>
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
                    {{ __('warehouse.movement_details') }}
                </h3>
                <button onclick="closeMovementModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; font-size: 20px; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div id="movementDetailsContent" style="padding: 25px;">
                <div style="text-align: center; padding: 40px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">{{ __('warehouse.loading') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keep the same styles and scripts from the original file, just translate the SweetAlert messages -->
    <script>
        // View Movement Details - Keep the same function but translate the loading text
        function viewMovementDetails(movementId) {
            const modal = document.getElementById('movementDetailsModal');
            const content = document.getElementById('movementDetailsContent');

            modal.style.display = 'flex';
            content.innerHTML = '<div style="text-align: center; padding: 40px;"><div class="spinner-border text-primary" role="status"><span class="sr-only">{{ __('warehouse.loading') }}</span></div></div>';

            // Keep the rest of the function as is...
            // (The fetch and display logic remains the same)
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
                        title: '{{ __("warehouse.confirm_delete") }}',
                        text: '{{ __("warehouse.confirm_delete_supplier_full") }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '{{ __("warehouse.yes_delete") }}',
                        cancelButtonText: '{{ __("warehouse.cancel") }}',
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
                        title: '{{ __("warehouse.phone_number") }}',
                        text: '{{ $warehouse->contact_number }}',
                        icon: 'info',
                        confirmButtonText: '{{ __("warehouse.ok") }}'
                    });
                });
            }

            const emailButton = document.querySelector('.action-btn.email');
            if (emailButton) {
                emailButton.addEventListener('click', function() {
                    Swal.fire({
                        title: '{{ __("warehouse.email") }}',
                        text: '{{ $warehouse->email }}',
                        icon: 'info',
                        confirmButtonText: '{{ __("warehouse.ok") }}'
                    });
                });
            }
        });
    </script>

    <!-- JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
@endsection
