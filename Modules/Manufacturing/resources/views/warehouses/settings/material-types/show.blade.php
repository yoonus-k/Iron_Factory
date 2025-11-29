@extends('master')

@section('title', __('warehouse.material_type_details'))

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-layers"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $materialType->type_name }}</h1>
                        @if($materialType->type_name_en)
                            <p class="course-subtitle">{{ $materialType->type_name_en }}</p>
                        @endif
                        <div class="badges">
                            @if ($materialType->is_active)
                                <span class="badge badge-success">{{ __('warehouse.active') }}</span>
                            @else
                                <span class="badge badge-secondary">{{ __('warehouse.inactive') }}</span>
                            @endif
                            @php
                                $categories = [
                                    'raw_material' => 'مادة خام',
                                    'finished_product' => 'منتج نهائي',
                                    'semi_finished' => 'منتج شبه مكتمل',
                                    'additive' => 'مادة مضافة',
                                    'packing_material' => 'مادة تغليف',
                                    'component' => 'مكون'
                                ];
                            @endphp
                            <span class="badge badge-info">{{ $categories[$materialType->category] ?? $materialType->category }}</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.warehouse-settings.material-types.edit', $materialType->id) }}" class="btn btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        {{ __('warehouse.edit') }}
                    </a>
                    <form method="POST" action="{{ route('manufacturing.warehouse-settings.material-types.toggle-status', $materialType->id) }}" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn" style="background-color: {{ $materialType->is_active ? '#e74c3c' : '#27ae60' }}; color: white; border: none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            {{ $materialType->is_active ? __('warehouse.deactivate') : __('warehouse.activate') }}
                        </button>
                    </form>
                    <a href="{{ route('manufacturing.warehouse-settings.material-types.index') }}" class="btn btn-back">
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
            <!-- معلومات النوع الأساسية -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('warehouse.basic_type_info') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.type_code') }}:</div>
                        <div class="info-value">
                            <span class="badge badge-primary">{{ $materialType->type_code }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.type_name_ar') }}:</div>
                        <div class="info-value">{{ $materialType->type_name }}</div>
                    </div>

                    @if($materialType->type_name_en)
                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.type_name_en') }}:</div>
                        <div class="info-value">{{ $materialType->type_name_en }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.category') }}:</div>
                        <div class="info-value">
                            <span class="badge badge-info">{{ $categories[$materialType->category] ?? $materialType->category }}</span>
                        </div>
                    </div>

                    @if($materialType->default_unit)
                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.default_unit') }}:</div>
                        <div class="info-value">{{ $materialType->unit?->unit_name }} ({{ $materialType->unit?->unit_code }})</div>
                    </div>
                    @endif

                    @if($materialType->standard_cost)
                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.standard_cost') }}:</div>
                        <div class="info-value">{{ $materialType->standard_cost }}</div>
                    </div>
                    @endif

                    @if($materialType->shelf_life_days)
                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.shelf_life_days') }}:</div>
                        <div class="info-value">{{ $materialType->shelf_life_days }} {{ __('warehouse.days') }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.status') }}:</div>
                        <div class="info-value">
                            @if ($materialType->is_active)
                                <span class="badge badge-success">{{ __('warehouse.active') }}</span>
                            @else
                                <span class="badge badge-secondary">{{ __('warehouse.inactive') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- الوصف وشروط التخزين -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('warehouse.description_and_storage') }}</h3>
                </div>
                <div class="card-body">
                    @if($materialType->description)
                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.description_ar') }}:</div>
                        <div class="info-value">{{ $materialType->description }}</div>
                    </div>
                    @endif

                    @if($materialType->description_en)
                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.description_en') }}:</div>
                        <div class="info-value">{{ $materialType->description_en }}</div>
                    </div>
                    @endif

                    @if($materialType->storage_conditions)
                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.storage_conditions_ar') }}:</div>
                        <div class="info-value">{{ $materialType->storage_conditions }}</div>
                    </div>
                    @endif

                    @if($materialType->storage_conditions_en)
                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.storage_conditions_en') }}:</div>
                        <div class="info-value">{{ $materialType->storage_conditions_en }}</div>
                    </div>
                    @endif

                    @if(!$materialType->description && !$materialType->description_en && !$materialType->storage_conditions && !$materialType->storage_conditions_en)
                    <p class="text-muted">{{ __('warehouse.no_additional_info') }}</p>
                    @endif
                </div>
            </div>

            <!-- معلومات إضافية -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('warehouse.additional_information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.created_by') }}:</div>
                        <div class="info-value">{{ $materialType->creator->name ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.creation_date') }}:</div>
                        <div class="info-value">{{ $materialType->created_at->format('Y-m-d H:i') }}</div>
                    </div>

                    @if($materialType->updated_at != $materialType->created_at)
                    <div class="info-item">
                        <div class="info-label">{{ __('warehouse.last_modification_date') }}:</div>
                        <div class="info-value">{{ $materialType->updated_at->format('Y-m-d H:i') }}</div>
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
                <h3 class="card-title">{{ __('warehouse.operation_logs') }}</h3>
            </div>
            <div class="card-body">
                @php
                    $operationLogs = $materialType->operationLogs()->orderBy('created_at', 'desc')->get();
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


    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete confirmation with SweetAlert2
            const deleteButtons = document.querySelectorAll('.action-btn.delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: 'هل أنت متأكد من حذف هذا النوع؟ هذا الإجراء لا يمكن التراجع عنه!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
