@extends('master')

@section('title', __('delivery_notes.delivery_note_details'))

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
                                        ⏳ {{ __('delivery_notes.pending') }}
                                        @break
                                    @case('registered')
                                        ✅ {{ __('delivery_notes.registered') }}
                                        @break
                                    @case('in_production')
                                        🏭 {{ __('delivery_notes.in_production') }}
                                        @break
                                    @case('completed')
                                        ✔️ {{ __('delivery_notes.completed') }}
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
                        {{ __('delivery_notes.back') }}
                    </a>

                    @if($deliveryNote->type === 'incoming')
                        @if (auth()->user()->hasPermission('WAREHOUSE_REGISTRATION_TRANSFER'))
                            @php
                                // Check if delivery note has coils
                                $hasCoils = $deliveryNote->coils && $deliveryNote->coils->count() > 0;
                                $transferRoute = $hasCoils 
                                    ? route('manufacturing.coils.transfer-index') 
                                    : route('manufacturing.warehouse.registration.transfer-form', $deliveryNote);
                            @endphp
                            <a href="{{ $transferRoute }}" class="btn btn-success">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3"></polyline>
                                    <line x1="12" y1="12" x2="20" y2="7.5"></line>
                                    <line x1="12" y1="12" x2="12" y2="21"></line>
                                    <line x1="12" y1="12" x2="4" y2="7.5"></line>
                                </svg>
                                {{ __('delivery_notes.transfer_to_production') }}
                                @if($hasCoils)
                                    <span class="badge badge-light" style="margin-right: 5px;">{{ $deliveryNote->coils->count() }} كويل</span>
                                @endif
                            </a>
                        @endif

                        @if (!$deliveryNote->is_locked)
                            @if (auth()->user()->hasPermission('WAREHOUSE_REGISTRATION_LOCK'))
                                <button class="btn" type="button" data-bs-toggle="modal" data-bs-target="#lockModal" title="{{ __('delivery_notes.lock_shipment') }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                    {{ __('delivery_notes.lock') }}
                                </button>
                            @endif
                        @else
                            @if (auth()->user()->hasPermission('WAREHOUSE_REGISTRATION_UNLOCK'))
                                <form action="{{ route('manufacturing.warehouse.registration.unlock', $deliveryNote) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-info" onclick="return confirm('{{ __('delivery_notes.confirm_unlock') }}')">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 9.2-1"></path>
                                        </svg>
                                        {{ __('delivery_notes.unlock') }}
                                    </button>
                                </form>
                            @endif
                        @endif
                    @endif

                    @if (auth()->user()->hasPermission('WAREHOUSE_DELIVERY_NOTES_UPDATE'))
                        <a href="{{ route('manufacturing.delivery-notes.edit', $deliveryNote->id) }}" class="btn btn-edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            {{ __('delivery_notes.edit') }}
                        </a>
                    @endif

                    @if (auth()->user()->hasPermission('WAREHOUSE_DELIVERY_NOTES_UPDATE'))
                        <div class="dropdown">
                            <button class="btn" type="button" data-bs-toggle="dropdown" title="{{ __('delivery_notes.change_status') }}">
                                @php
                                    $statusColor = $deliveryNote->status->color();
                                    $colorCode = $statusColor === 'yellow' ? '#f39c12' : ($statusColor === 'green' ? '#27ae60' : ($statusColor === 'red' ? '#e74c3c' : '#3498db'));
                                @endphp
                                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: {{ $colorCode }}; margin-left: 6px;"></span>
                                {{ $deliveryNote->status->label() }}
                            </button>
                            <ul class="dropdown-menu">
                                @foreach(\App\Enums\DeliveryNoteStatus::cases() as $status)
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
                    @endif
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
                    <h3 class="card-title">{{ __('delivery_notes.delivery_note_info') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">{{ __('delivery_notes.note_number') }}:</div>
                        <div class="info-value">{{ $deliveryNote->note_number }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">{{ __('delivery_notes.type') }}:</div>
                        <div class="info-value">
                            @if($deliveryNote->type === 'incoming')
                                <span class="badge badge-success">🔽 {{ __('delivery_notes.incoming_from_supplier') }}</span>
                            @else
                                <span class="badge badge-warning">🔼 {{ __('delivery_notes.outgoing_to_customer') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">{{ __('delivery_notes.delivery_date') }}:</div>
                        <div class="info-value">{{ $deliveryNote->delivery_date->format('Y-m-d') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">{{ __('delivery_notes.delivery_quantity') }}:</div>
                        <div class="info-value">{{ number_format($deliveryNote->delivery_quantity ?? 0, 2) }} {{ __('delivery_notes.unit') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">{{ __('delivery_notes.status') }}:</div>
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
                            <div class="info-label">{{ __('delivery_notes.activity') }}:</div>
                            <div class="info-value">
                                <span class="badge badge-success">✓ {{ __('delivery_notes.active') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="info-item">
                            <div class="info-label">{{ __('delivery_notes.activity') }}:</div>
                            <div class="info-value">
                                <span class="badge badge-warning">⚠ {{ __('delivery_notes.inactive') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">{{ __('delivery_notes.invoice_number') }}:</div>
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
                        <h3 class="card-title">📦 {{ __('delivery_notes.warehouse_management') }}</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $registeredQuantity = $deliveryNote->quantity ?? 0;
                            $transferredQuantity = $deliveryNote->quantity_used ?? 0;
                            $remainingQuantity = $deliveryNote->quantity_remaining ?? 0;
                            $transferPercentage = $registeredQuantity > 0 ? ($transferredQuantity / $registeredQuantity * 100) : 0;

                            $statusColor = 'success';
                            $statusColorStart = '#27ae60';
                            $statusColorEnd = '#229954';
                            $statusLabel = '✅ ' . __('delivery_notes.fully_transferred');

                            if ($transferPercentage == 0) {
                                $statusColor = 'warning';
                                $statusColorStart = '#e74c3c';
                                $statusColorEnd = '#c0392b';
                                $statusLabel = '⏳ ' . __('delivery_notes.not_transferred_yet');
                            } elseif ($transferPercentage < 100) {
                                $statusColor = 'info';
                                $statusColorStart = '#3498db';
                                $statusColorEnd = '#2980b9';
                                $statusLabel = '⚡ ' . __('delivery_notes.partial_transfer');
                            }
                        @endphp

                        <div style="background: linear-gradient(135deg, {{ $statusColorStart }} 0%, {{ $statusColorEnd }} 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="font-size: 12px; opacity: 0.9; margin-bottom: 5px;">{{ __('delivery_notes.total_status') }}:</div>
                                    <div style="font-size: 20px; font-weight: bold;">{{ $statusLabel }}</div>
                                </div>
                                <div style="text-align: center;">
                                    <div style="font-size: 30px; font-weight: bold; margin-bottom: 5px;">{{ number_format($transferPercentage, 1) }}%</div>
                                    <div style="font-size: 12px; opacity: 0.9;">{{ __('delivery_notes.transfer_percentage') }}</div>
                                </div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                            <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; border-right: 4px solid #27ae60;">
                                <div style="font-size: 12px; color: #666; margin-bottom: 5px; font-weight: 600;">
                                    📥 {{ __('delivery_notes.incoming_quantity_registered') }}:
                                </div>
                                <div style="font-size: 18px; font-weight: bold; color: #27ae60;">
                                    {{ number_format($registeredQuantity, 2) }}
                                    @if($deliveryNote->material && $deliveryNote->material->materialDetails->first() && $deliveryNote->material->materialDetails->first()->unit)
                                        {{ $deliveryNote->material->materialDetails->first()->unit->name ?? __('delivery_notes.unit') }}
                                    @else
                                        {{ __('delivery_notes.unit') }}
                                    @endif
                                </div>
                            </div>

                            <div style="background: #fff3e0; padding: 15px; border-radius: 8px; border-right: 4px solid #ff9800;">
                                <div style="font-size: 12px; color: #666; margin-bottom: 5px; font-weight: 600;">
                                    🏭 {{ __('delivery_notes.transferred_to_production') }}:
                                </div>
                                <div style="font-size: 18px; font-weight: bold; color: #ff9800;">
                                    {{ number_format($transferredQuantity, 2) }}
                                    @if($deliveryNote->material && $deliveryNote->material->materialDetails->first() && $deliveryNote->material->materialDetails->first()->unit)
                                        {{ $deliveryNote->material->materialDetails->first()->unit->name ?? __('delivery_notes.unit') }}
                                    @else
                                        {{ __('delivery_notes.unit') }}
                                    @endif
                                </div>
                            </div>

                            <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; border-right: 4px solid #3498db;">
                                <div style="font-size: 12px; color: #666; margin-bottom: 5px; font-weight: 600;">
                                    📦 {{ __('delivery_notes.remaining_in_warehouse') }}:
                                </div>
                                <div style="font-size: 18px; font-weight: bold; color: #3498db;">
                                    {{ number_format($remainingQuantity, 2) }}
                                    @if($deliveryNote->material && $deliveryNote->material->materialDetails->first() && $deliveryNote->material->materialDetails->first()->unit)
                                        {{ $deliveryNote->material->materialDetails->first()->unit->name ?? __('delivery_notes.unit') }}
                                    @else
                                        {{ __('delivery_notes.unit') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <label style="font-weight: 600; color: #2c3e50;">📊 {{ __('delivery_notes.transfer_progress') }}:</label>
                                <span style="font-weight: 600; color: #3498db;">{{ number_format($transferPercentage, 1) }}%</span>
                            </div>
                            <div class="progress" style="height: 30px; border-radius: 4px;">
                                <div class="progress-bar" style="width: {{ min($transferPercentage, 100) }}%; background: linear-gradient(90deg, #27ae60 0%, #2ecc71 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    @if($transferPercentage > 10){{ number_format($transferredQuantity, 1) }}@endif
                                </div>
                            </div>
                        </div>

                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="font-weight: 600; color: #2c3e50; margin-bottom: 12px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
                                📅 {{ __('delivery_notes.movement_log') }}:
                            </div>

                            @if ($deliveryNote->registered_at)
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #e9ecef;">
                                    <div style="width: 10px; height: 10px; background: #3498db; border-radius: 50%;"></div>
                                    <div style="flex: 1;">
                                        <div style="font-size: 12px; color: #999;">📥 {{ __('delivery_notes.quantity_registration_from_crate') }}:</div>
                                        <div style="font-weight: 600; color: #2c3e50;">{{ $deliveryNote->registered_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                    <div style="font-size: 11px; color: #999;">{{ __('delivery_notes.by') }}: {{ $deliveryNote->registeredBy?->name ?? 'N/A' }}</div>
                                </div>
                            @endif

                            @if ($deliveryNote->quantity_used && $deliveryNote->quantity_used > 0)
                                <div style="display: flex; align-items: center; gap: 10px; padding-bottom: 10px;">
                                    <div style="width: 10px; height: 10px; background: #27ae60; border-radius: 50%;"></div>
                                    <div style="flex: 1;">
                                        <div style="font-size: 12px; color: #999;">🏭 {{ __('delivery_notes.start_transfer_to_production') }}:</div>
                                        <div style="font-weight: 600; color: #2c3e50;">
                                            @if($deliveryNote->registrationLogs && $deliveryNote->registrationLogs->count() > 0)
                                                {{ $deliveryNote->registrationLogs->first()->created_at?->format('d/m/Y H:i') ?? __('delivery_notes.identified') }}
                                            @else
                                                {{ __('delivery_notes.identified') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div style="font-size: 11px; color: #999;">{{ __('delivery_notes.by') }}: {{ __('delivery_notes.system') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- قسم الكويلات إذا كان الإذن مقسم إلى كويلات --}}
            @if ($deliveryNote->coils && $deliveryNote->coils->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon warning">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <circle cx="12" cy="12" r="6"></circle>
                                <circle cx="12" cy="12" r="2"></circle>
                            </svg>
                        </div>
                        <h3 class="card-title">🎲 {{ __('delivery_notes.coils_details') }} ({{ $deliveryNote->coils->count() }} {{ __('delivery_notes.coil') }})</h3>
                    </div>
                    <div class="card-body">
                        <div style="background: #fff9e6; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-right: 4px solid #ffa726;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <svg style="width: 24px; height: 24px; color: #ffa726;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <div>
                                    <strong>{{ __('delivery_notes.coils_notice') }}:</strong>
                                    {{ __('delivery_notes.this_delivery_divided_into_coils') }}
                                </div>
                            </div>
                        </div>

                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                        <th style="padding: 12px; text-align: right; font-weight: 600; color: #495057;">#</th>
                                        <th style="padding: 12px; text-align: right; font-weight: 600; color: #495057;">{{ __('delivery_notes.coil_number') }}</th>
                                        <th style="padding: 12px; text-align: right; font-weight: 600; color: #495057;">{{ __('delivery_notes.coil_barcode') }}</th>
                                        <th style="padding: 12px; text-align: right; font-weight: 600; color: #495057;">{{ __('delivery_notes.coil_weight') }}</th>
                                        <th style="padding: 12px; text-align: right; font-weight: 600; color: #495057;">{{ __('delivery_notes.remaining_weight') }}</th>
                                        <th style="padding: 12px; text-align: right; font-weight: 600; color: #495057;">{{ __('delivery_notes.status') }}</th>
                                        <th style="padding: 12px; text-align: right; font-weight: 600; color: #495057;">{{ __('delivery_notes.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deliveryNote->coils as $index => $coil)
                                        <tr style="border-bottom: 1px solid #dee2e6;">
                                            <td style="padding: 12px; text-align: right;">{{ $index + 1 }}</td>
                                            <td style="padding: 12px; text-align: right; font-weight: 600; color: #2c3e50;">{{ $coil->coil_number ?? 'N/A' }}</td>
                                            <td style="padding: 12px; text-align: right;">
                                                <code style="background: #e9ecef; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ $coil->coil_barcode }}</code>
                                            </td>
                                            <td style="padding: 12px; text-align: right; color: #27ae60; font-weight: 600;">{{ number_format($coil->coil_weight, 3) }} كجم</td>
                                            <td style="padding: 12px; text-align: right; color: #3498db; font-weight: 600;">{{ number_format($coil->remaining_weight, 3) }} كجم</td>
                                            <td style="padding: 12px; text-align: right;">
                                                @php
                                                    $statusBadge = [
                                                        'available' => ['bg' => '#d4edda', 'color' => '#155724', 'text' => 'متاح'],
                                                        'partially_used' => ['bg' => '#fff3cd', 'color' => '#856404', 'text' => 'مستخدم جزئياً'],
                                                        'fully_used' => ['bg' => '#f8d7da', 'color' => '#721c24', 'text' => 'مستخدم بالكامل'],
                                                    ];
                                                    $badge = $statusBadge[$coil->status] ?? ['bg' => '#e2e3e5', 'color' => '#383d41', 'text' => $coil->status];
                                                @endphp
                                                <span style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                                    {{ $badge['text'] }}
                                                </span>
                                            </td>
                                            <td style="padding: 12px; text-align: right; color: #6c757d; font-size: 13px;">{{ $coil->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="background: #f8f9fa; font-weight: 600;">
                                        <td colspan="3" style="padding: 12px; text-align: right;">{{ __('delivery_notes.total') }}:</td>
                                        <td style="padding: 12px; text-align: right; color: #27ae60;">{{ number_format($deliveryNote->coils->sum('coil_weight'), 3) }} كجم</td>
                                        <td style="padding: 12px; text-align: right; color: #3498db;">{{ number_format($deliveryNote->coils->sum('remaining_weight'), 3) }} كجم</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('delivery_notes.recorded_by') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">{{ __('delivery_notes.name') }}:</div>
                        <div class="info-value" style="color: #000000; font-weight: 600;">{{ $deliveryNote->recordedBy->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">{{ __('delivery_notes.creation_date') }}:</div>
                        <div class="info-value" style="color: #000000; font-weight: 600;">{{ $deliveryNote->created_at->format('d-m-Y H:i:s') }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('delivery_notes.received_by') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">{{ __('delivery_notes.name') }}:</div>
                        <div class="info-value" style="color: #000000; font-weight: 600;">{{ $deliveryNote->receiver->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">{{ __('delivery_notes.last_update') }}:</div>
                        <div class="info-value" style="color: #000000; font-weight: 600;">{{ $deliveryNote->updated_at->format('d-m-Y H:i:s') }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('delivery_notes.approved_by') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">{{ __('delivery_notes.name') }}:</div>
                        <div class="info-value" style="color: #000000; font-weight: 600;">{{ $deliveryNote->approvedBy->name ?? __('delivery_notes.not_approved') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">{{ __('delivery_notes.approval_date') }}:</div>
                        <div class="info-value" style="color: #000000; font-weight: 600;">{{ $deliveryNote->approved_at ? $deliveryNote->approved_at->format('d-m-Y H:i') : __('delivery_notes.not_set') }}</div>
                    </div>
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
                <h3 class="card-title">{{ __('delivery_notes.operation_logs') }}</h3>
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
                                                            {{ __('delivery_notes.operation_create') }}
                                                        </span>
                                                        @break
                                                    @case('update')
                                                        <span class="badge" style="background-color: #3498db; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                            <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/>
                                                            </svg>
                                                            {{ __('delivery_notes.operation_update') }}
                                                        </span>
                                                        @break
                                                    @case('delete')
                                                        <span class="badge" style="background-color: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                            <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px; display: inline-block; margin-left: 3px;">
                                                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-9l-1 1H5v2h14V4z"/>
                                                            </svg>
                                                            {{ __('delivery_notes.operation_delete') }}
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
                                                <span><strong>{{ $log->user->name ?? __('delivery_notes.deleted_user') }}</strong></span>
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
                        <p style="margin: 0; font-size: 14px;">{{ __('delivery_notes.no_operations_recorded') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="lockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🔒 {{ __('delivery_notes.lock_shipment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('manufacturing.warehouse.registration.lock', $deliveryNote) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">{{ __('delivery_notes.lock_reason') }}:</label>
                            <textarea name="lock_reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('delivery_notes.cancel') }}</button>
                        <button type="submit" class="btn btn-warning">{{ __('delivery_notes.lock') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
@endsection