@extends('master')

@section('title', __('warehouse_registration.shipment_details'))

@section('content')
<style>
    .simple-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .success-banner {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(17, 153, 142, 0.3);
    }
    
    .barcode-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }
    
    .barcode-number {
        font-size: 36px;
        font-weight: bold;
        letter-spacing: 4px;
        font-family: 'Courier New', monospace;
        margin: 20px 0;
    }
    
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    
    .card-header-simple {
        font-size: 20px;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 3px solid #667eea;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .info-item-simple {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .info-label-simple {
        font-size: 14px;
        color: #7f8c8d;
        font-weight: 500;
    }
    
    .info-value-simple {
        font-size: 18px;
        color: #2c3e50;
        font-weight: bold;
    }
    
    .status-badge-simple {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: bold;
    }
    
    .status-success {
        background: #d4edda;
        color: #155724;
    }
    
    .status-warning {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-info {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .btn-group-simple {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }
    
    .btn-simple {
        padding: 12px 24px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-success {
        background: #28a745;
        color: white;
    }
    
    .btn-warning {
        background: #ffc107;
        color: #000;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-simple:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .progress-bar-container {
        background: #ecf0f1;
        border-radius: 10px;
        height: 30px;
        overflow: hidden;
        display: flex;
        border: 2px solid #bdc3c7;
    }
    
    .progress-segment {
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 13px;
        font-weight: bold;
    }
</style>

<div class="simple-container">
    <!-- رسالة النجاح -->
    @if (session('success'))
        <div class="success-banner">
            <div style="display: flex; align-items: center; gap: 20px;">
                <div style="font-size: 48px;">✅</div>
                <div style="flex: 1;">
                    <div style="font-size: 20px; font-weight: bold; margin-bottom: 10px;">{{ session('success') }}</div>
                    @if(session('batch_code'))
                        <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 10px; margin-top: 10px;">
                            <div style="font-size: 32px; font-weight: bold; letter-spacing: 3px; font-family: 'Courier New', monospace;">
                                {{ session('batch_code') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- معلومات الشحنة الأساسية -->
    <div class="info-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h1 style="font-size: 32px; color: #2c3e50; margin: 0;">📦 {{ __('warehouse_registration.delivery_note') }} #{{ $deliveryNote->note_number ?? $deliveryNote->id }}</h1>
                <div style="margin-top: 10px;">
                    <span class="status-badge-simple {{ $deliveryNote->registration_status === 'registered' ? 'status-success' : ($deliveryNote->registration_status === 'in_production' ? 'status-info' : 'status-warning') }}">
                        @switch($deliveryNote->registration_status)
                            @case('not_registered') ⏳ {{ __('warehouse_registration.pending') }} @break
                            @case('registered') ✅ {{ __('warehouse_registration.registered') }} @break
                            @case('in_production') 🏭 {{ __('warehouse_registration.in_production') }} @break
                            @case('completed') ✔️ {{ __('warehouse.completed') }} @break
                            @default {{ $deliveryNote->registration_status }}
                        @endswitch
                    </span>
                </div>
            </div>
        </div>
        
        <div class="info-grid">
            <div class="info-item-simple">
                <span class="info-label-simple">{{ __('warehouse_registration.supplier') }}</span>
                <span class="info-value-simple">{{ $deliveryNote->supplier->name ?? __('warehouse.not_specified') }}</span>
            </div>
            
            <div class="info-item-simple">
                <span class="info-label-simple">{{ __('warehouse_registration.created_date') }}</span>
                <span class="info-value-simple">{{ $deliveryNote->created_at->format('Y-m-d') }}</span>
            </div>
            
            <div class="info-item-simple">
                <span class="info-label-simple">{{ __('warehouse_registration.material') }}</span>
                <span class="info-value-simple">{{ $deliveryNote->material->name_ar ?? __('warehouse.not_specified') }}</span>
            </div>
            
            <div class="info-item-simple">
                <span class="info-label-simple">{{ __('warehouse_registration.actual_weight') }}</span>
                <span class="info-value-simple">{{ number_format($deliveryNote->actual_weight ?? 0, 2) }} {{ __('warehouse.kg') }}</span>
            </div>
        </div>
    </div>

    @php
        // البحث عن عملية التقسيم في product_tracking
        $splitTracking = \App\Models\ProductTracking::where('action', 'split')
            ->where('input_barcode', $deliveryNote->materialBatch?->batch_code ?? '')
            ->orWhere(function($q) use ($deliveryNote) {
                $q->where('output_barcode', 'like', '%' . ($deliveryNote->production_barcode ?? '') . '%');
            })
            ->first();
        
        $metadata = $splitTracking ? (is_string($splitTracking->metadata) ? json_decode($splitTracking->metadata, true) : $splitTracking->metadata) : null;
        
        // تحديد إذا كان هناك تقسيم جزئي
        $hasPartialSplit = $splitTracking && $metadata && isset($metadata['split_type']) && $metadata['split_type'] === 'partial_transfer';
    @endphp

    <!-- بطاقات الباركود بتصميم موحد (تظهر فقط إذا لم يكن هناك تقسيم جزئي) -->
    @if(!$hasPartialSplit)
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 30px;">
        
        <!-- باركود دخول المستودع -->
        @if($deliveryNote->materialBatch && $deliveryNote->materialBatch->batch_code)
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
            <div style="text-align: center; margin-bottom: 15px;">
                <div style="font-size: 15px; opacity: 0.9; margin-bottom: 5px;">{{ __('warehouse.warehouse') }}</div>
                <div style="font-size: 20px; font-weight: bold;">📦 {{ __('warehouse_registration.warehouse_barcode') }}</div>
            </div>
            <div style="background: rgba(255,255,255,0.15); padding: 12px; border-radius: 10px; text-align: center; margin-bottom: 15px;">
                <div style="font-size: 18px; font-weight: bold; font-family: 'Courier New', monospace; letter-spacing: 2px;">
                    {{ $deliveryNote->materialBatch->batch_code }}
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 13px; text-align: center;">
                <div>📊 {{ number_format($deliveryNote->materialBatch->initial_quantity, 2) }} {{ __('warehouse.kg') }}</div>
            </div>
            <button onclick="printWarehouseBarcode('{{ $deliveryNote->materialBatch->batch_code }}', '{{ $deliveryNote->note_number }}', '{{ $deliveryNote->material->name_ar ?? __('warehouse.not_specified') }}', {{ $deliveryNote->materialBatch->initial_quantity }}, '{{ $deliveryNote->supplier->name ?? __('warehouse.not_specified') }}')" 
                    style="width: 100%; background: white; color: #667eea; padding: 12px; border: none; border-radius: 8px; font-size: 15px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.2)';"
                    onmouseout="this.style.transform=''; this.style.boxShadow='';">
                <i class="feather icon-printer"></i> {{ __('warehouse_registration.print_warehouse_barcode') }}
            </button>
        </div>
        @endif

        <!-- باركود الإنتاج (يظهر فقط للنقل الكامل) -->
        @if($deliveryNote->production_barcode)
        <div style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);">
            <div style="text-align: center; margin-bottom: 15px;">
                <div style="font-size: 15px; opacity: 0.9; margin-bottom: 5px;">{{ __('warehouse_registration.production') }}</div>
                <div style="font-size: 20px; font-weight: bold;">🏭 {{ __('warehouse_registration.production_barcode') }}</div>
            </div>
            <div style="background: rgba(255,255,255,0.15); padding: 12px; border-radius: 10px; text-align: center; margin-bottom: 15px;">
                <div style="font-size: 18px; font-weight: bold; font-family: 'Courier New', monospace; letter-spacing: 2px;">
                    {{ $deliveryNote->production_barcode }}
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 13px; text-align: center;">
                <div>✅ {{ __('warehouse_registration.moved_to_production') }}</div>
            </div>
            <button onclick="printProductionBarcode('{{ $deliveryNote->production_barcode }}', '{{ $deliveryNote->note_number }}', '{{ $deliveryNote->material->name_ar ?? __('warehouse.not_specified') }}')"
                    style="width: 100%; background: white; color: #11998e; padding: 12px; border: none; border-radius: 8px; font-size: 15px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.2)';"
                    onmouseout="this.style.transform=''; this.style.boxShadow='';">
                <i class="feather icon-printer"></i> {{ __('warehouse_registration.print_production_barcode') }}
            </button>
        </div>
        @endif
        
    </div>
    @endif

    <!-- ✨ كارد تقسيم الباركود (FULL WIDTH - md-12) - يظهر فقط للنقل الجزئي -->
    @if($hasPartialSplit)

    <div class="info-card" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: white; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(245, 158, 11, 0.3);">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 18px; opacity: 0.95; margin-bottom: 8px;">🔄 {{ __('warehouse_registration.batch_split_operation') }}</div>
            <div style="font-size: 28px; font-weight: bold;">{{ __('warehouse_registration.barcode_split_to_two') }}</div>
            <div style="font-size: 14px; margin-top: 10px; opacity: 0.9;">
                <i class="feather icon-info"></i> {{ __('warehouse_registration.system_created_separate_barcodes') }}
            </div>
        </div>

        <!-- الباركود الأصلي -->
        <div style="background: rgba(0,0,0,0.15); padding: 20px; border-radius: 12px; margin-bottom: 25px; border: 2px dashed rgba(255,255,255,0.4);">
            <div style="text-align: center; margin-bottom: 12px;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">📋 {{ __('warehouse_registration.original_barcode_before_split') }}</div>
                <div style="background: rgba(255,255,255,0.2); padding: 15px 25px; border-radius: 10px; display: inline-block;">
                    <div style="font-size: 24px; font-weight: bold; font-family: 'Courier New', monospace; letter-spacing: 3px;">
                        {{ $metadata['original_barcode'] ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- السهم للأسفل -->
        <div style="text-align: center; margin: 25px 0;">
            <div style="background: rgba(255,255,255,0.2); width: 60px; height: 60px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                <i class="feather icon-arrow-down" style="font-size: 32px;"></i>
            </div>
        </div>

        <!-- البيانات المقسمة في صفين -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 20px;">
            
            @if(isset($metadata['production_barcode']))
            <!-- باركود الإنتاج -->
            <div style="background: rgba(59, 130, 246, 0.95); border: 3px solid white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
                <div style="text-align: center; margin-bottom: 20px;">
                    <div style="background: rgba(255,255,255,0.2); width: 60px; height: 60px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <i class="feather icon-box" style="font-size: 28px;"></i>
                    </div>
                    <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">🏭 {{ __('warehouse_registration.production_barcode') }}</div>
                    <div style="font-size: 13px; opacity: 0.9;">{{ __('warehouse_registration.quantity_moved_to_production') }}</div>
                </div>
                
                <div style="background: rgba(255,255,255,0.25); padding: 15px; border-radius: 10px; margin-bottom: 15px; text-align: center;">
                    <div style="font-size: 20px; font-weight: bold; font-family: 'Courier New', monospace; letter-spacing: 2px; word-break: break-all;">
                        {{ $metadata['production_barcode'] }}
                    </div>
                </div>
                
                <div style="background: rgba(255,255,255,0.2); padding: 12px; border-radius: 8px; text-align: center; margin-bottom: 15px;">
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">{{ __('warehouse.quantity') }}</div>
                    <div style="font-size: 24px; font-weight: bold;">{{ number_format($metadata['production_quantity'] ?? 0, 2) }} <span style="font-size: 16px;">{{ __('warehouse.kg') }}</span></div>
                </div>
                
                <button onclick="printProductionBarcode('{{ $metadata['production_barcode'] }}', '{{ $deliveryNote->note_number }}', '{{ $deliveryNote->material->name_ar ?? __('warehouse.not_specified') }}')"
                        style="width: 100%; background: white; color: #3b82f6; padding: 14px; border: none; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s; box-shadow: 0 3px 10px rgba(0,0,0,0.15);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.25)';"
                        onmouseout="this.style.transform=''; this.style.boxShadow='0 3px 10px rgba(0,0,0,0.15)';">
                    <i class="feather icon-printer" style="font-size: 20px;"></i>
                    <span>{{ __('warehouse_registration.print_production_barcode') }}</span>
                </button>
            </div>
            @endif

            @if(isset($metadata['remaining_barcode']))
            <!-- باركود المستودع المتبقي -->
            <div style="background: rgba(16, 185, 129, 0.95); border: 3px solid white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
                <div style="text-align: center; margin-bottom: 20px;">
                    <div style="background: rgba(255,255,255,0.2); width: 60px; height: 60px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <i class="feather icon-package" style="font-size: 28px;"></i>
                    </div>
                    <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">📦 {{ __('warehouse_registration.warehouse_barcode') }}</div>
                    <div style="font-size: 13px; opacity: 0.9;">{{ __('warehouse_registration.remaining_quantity_in_warehouse') }}</div>
                </div>
                
                <div style="background: rgba(255,255,255,0.25); padding: 15px; border-radius: 10px; margin-bottom: 15px; text-align: center;">
                    <div style="font-size: 20px; font-weight: bold; font-family: 'Courier New', monospace; letter-spacing: 2px; word-break: break-all;">
                        {{ $metadata['remaining_barcode'] }}
                    </div>
                </div>
                
                <div style="background: rgba(255,255,255,0.2); padding: 12px; border-radius: 8px; text-align: center; margin-bottom: 15px;">
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">{{ __('warehouse.quantity') }}</div>
                    <div style="font-size: 24px; font-weight: bold;">{{ number_format($metadata['remaining_quantity'] ?? 0, 2) }} <span style="font-size: 16px;">{{ __('warehouse.kg') }}</span></div>
                </div>
                
                <button onclick="printWarehouseBarcode('{{ $metadata['remaining_barcode'] }}', '{{ $deliveryNote->note_number }}', '{{ $deliveryNote->material->name_ar ?? __('warehouse.not_specified') }}', {{ $metadata['remaining_quantity'] ?? 0 }}, '{{ $deliveryNote->supplier->name ?? __('warehouse.not_specified') }}')"
                        style="width: 100%; background: white; color: #10b981; padding: 14px; border: none; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s; box-shadow: 0 3px 10px rgba(0,0,0,0.15);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.25)';"
                        onmouseout="this.style.transform=''; this.style.boxShadow='0 3px 10px rgba(0,0,0,0.15)';">
                    <i class="feather icon-printer" style="font-size: 20px;"></i>
                    <span>{{ __('warehouse_registration.print_warehouse_barcode') }}</span>
                </button>
            </div>
            @endif
        </div>

        <!-- ملاحظة توضيحية -->
        <div style="background: rgba(0,0,0,0.15); padding: 20px; border-radius: 10px; text-align: center; border: 2px solid rgba(255,255,255,0.3);">
            <div style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">
                <i class="feather icon-alert-circle"></i> {{ __('warehouse_registration.important_note') }}
            </div>
            <div style="font-size: 14px; line-height: 1.8; opacity: 0.95;">
                {{ __('warehouse_registration.barcode_split_explanation') }}:<br>
                • <strong>{{ __('warehouse_registration.blue_barcode') }}</strong> {{ __('warehouse_registration.quantity_moved_to_production_explanation') }}<br>
                • <strong>{{ __('warehouse_registration.green_barcode') }}</strong> {{ __('warehouse_registration.remaining_quantity_explanation') }}<br>
                {{ __('warehouse_registration.please_print_barcodes') }}
            </div>
        </div>
    </div>
    @endif

    <!-- الكميات والحالة -->
    @if($deliveryNote->quantity && $deliveryNote->quantity > 0)
        <div class="info-card">
            <div class="card-header-simple">{{ __('warehouse_registration.quantity_distribution') }}</div>
            
            @php
                $totalQuantity = $deliveryNote->quantity ?? 0;
                $transferredQuantity = $deliveryNote->quantity_used ?? 0;
                $remainingQuantity = $deliveryNote->quantity_remaining ?? 0;
                $transferPercentage = $totalQuantity > 0 ? ($transferredQuantity / $totalQuantity * 100) : 0;
                $remainingPercentage = $totalQuantity > 0 ? ($remainingQuantity / $totalQuantity * 100) : 0;
            @endphp
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
                <div style="text-align: center; padding: 20px; background: #e3f2fd; border-radius: 12px;">
                    <div style="font-size: 32px; font-weight: bold; color: #1976d2;">{{ number_format($totalQuantity, 2) }}</div>
                    <div style="color: #1976d2; font-weight: 600;">{{ __('warehouse_registration.total_quantity') }}</div>
                </div>
                
                <div style="text-align: center; padding: 20px; background: #e8f5e9; border-radius: 12px;">
                    <div style="font-size: 32px; font-weight: bold; color: #388e3c;">{{ number_format($transferredQuantity, 2) }}</div>
                    <div style="color: #388e3c; font-weight: 600;">{{ __('warehouse_registration.transferred') }}</div>
                </div>
                
                <div style="text-align: center; padding: 20px; background: #fff3e0; border-radius: 12px;">
                    <div style="font-size: 32px; font-weight: bold; color: #f57c00;">{{ number_format($remainingQuantity, 2) }}</div>
                    <div style="color: #f57c00; font-weight: 600;">{{ __('warehouse_registration.remaining') }}</div>
                </div>
            </div>
            
            <div class="progress-bar-container">
                @if($transferPercentage > 0)
                    <div class="progress-segment" style="width: {{ $transferPercentage }}%; background: linear-gradient(90deg, #27ae60, #2ecc71);">
                        @if($transferPercentage > 15){{ number_format($transferPercentage, 0) }}%@endif
                    </div>
                @endif
                @if($remainingPercentage > 0)
                    <div class="progress-segment" style="width: {{ $remainingPercentage }}%; background: linear-gradient(90deg, #3498db, #5dade2);">
                        @if($remainingPercentage > 15){{ number_format($remainingPercentage, 0) }}%@endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- الأزرار -->
    <div class="btn-group-simple">
        <a href="{{ route('manufacturing.warehouse.registration.pending') }}" class="btn-simple btn-secondary">
            ← {{ __('warehouse_registration.back_to_menu') }}
        </a>
        
        @if($canMoveToProduction ?? false)
            <a href="{{ route('manufacturing.warehouse.registration.transfer-form', $deliveryNote) }}" class="btn-simple btn-success">
                🚚 {{ __('warehouse_registration.transfer_to_production') }}
            </a>
        @endif
        
        @if(!$deliveryNote->is_locked && ($canEdit ?? false))
            <a href="{{ route('manufacturing.warehouse.registration.create', $deliveryNote) }}" class="btn-simple btn-warning">
                ✏️ {{ __('warehouse.edit') }}
            </a>
        @endif
    </div>
</div>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
// طباعة باركود دخول المستودع
function printWarehouseBarcode(barcode, noteNumber, materialName, quantity, supplierName) {
    const printWindow = window.open('', '', 'height=700,width=900');
    printWindow.document.write('<html dir="rtl"><head><title>{{ __('warehouse_registration.print_warehouse_barcode') }}</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
    printWindow.document.write('.print-container { background: white; padding: 60px; border-radius: 20px; box-shadow: 0 5px 30px rgba(0,0,0,0.1); text-align: center; max-width: 600px; }');
    printWindow.document.write('.header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; }');
    printWindow.document.write('.title { font-size: 32px; font-weight: bold; margin-bottom: 10px; }');
    printWindow.document.write('.subtitle { font-size: 18px; opacity: 0.95; }');
    printWindow.document.write('.barcode-wrapper { background: #f8f9fa; padding: 30px; border-radius: 15px; margin: 25px 0; border: 3px solid #667eea; }');
    printWindow.document.write('.barcode-number { font-size: 28px; font-weight: bold; color: #2c3e50; margin-top: 20px; letter-spacing: 4px; font-family: "Courier New", monospace; padding: 15px; background: white; border-radius: 10px; }');
    printWindow.document.write('.info-section { background: #f8f9fa; padding: 25px; border-radius: 12px; margin-top: 20px; text-align: right; }');
    printWindow.document.write('.info-row { margin: 15px 0; display: flex; justify-content: space-between; border-bottom: 1px solid #ddd; padding-bottom: 10px; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 16px; font-weight: 600; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 18px; }');
    printWindow.document.write('.badge { background: #667eea; color: white; padding: 12px 25px; border-radius: 25px; display: inline-block; margin: 15px 0; font-weight: bold; }');
    printWindow.document.write('@media print { body { background: white; } }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<div class="print-container">');
    printWindow.document.write('<div class="header">');
    printWindow.document.write('<div class="title">📦 {{ __('warehouse_registration.warehouse_entry') }}</div>');
    printWindow.document.write('<div class="subtitle">{{ __('warehouse_registration.delivery_note') }} ' + noteNumber + '</div>');
    printWindow.document.write('</div>');
    printWindow.document.write('<div class="badge">{{ __('warehouse_registration.warehouse') }}</div>');
    printWindow.document.write('<div class="barcode-wrapper">');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-number">' + barcode + '</div>');
    printWindow.document.write('</div>');
    printWindow.document.write('<div class="info-section">');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('warehouse_registration.material') }}:</span><span class="value">' + materialName + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('warehouse_registration.quantity') }}:</span><span class="value">' + quantity + ' {{ __('warehouse.kg') }}</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('warehouse_registration.supplier') }}:</span><span class="value">' + supplierName + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('warehouse.date') }}:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
    printWindow.document.write('</div></div>');
    printWindow.document.write('<script>');
    printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2.5, height: 100, displayValue: false, margin: 15 });');
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 600); };');
    printWindow.document.write('<\/script></body></html>');
    printWindow.document.close();
}

// طباعة باركود الإنتاج
function printProductionBarcode(barcode, noteNumber, materialName) {
    const printWindow = window.open('', '', 'height=650,width=850');
    printWindow.document.write('<html dir="rtl"><head><title>{{ __('warehouse_registration.print_production_barcode') }} - ' + noteNumber + '</title>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f5f5f5; }');
    printWindow.document.write('.barcode-container { background: white; padding: 50px; border-radius: 16px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); text-align: center; max-width: 550px; }');
    printWindow.document.write('.title { font-size: 28px; font-weight: bold; color: #11998e; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 4px solid #11998e; }');
    printWindow.document.write('.note-number { font-size: 24px; color: #11998e; font-weight: bold; margin: 20px 0; }');
    printWindow.document.write('.barcode-code { font-size: 22px; font-weight: bold; color: #2c3e50; margin: 25px 0; letter-spacing: 4px; font-family: "Courier New", monospace; }');
    printWindow.document.write('.info { margin-top: 30px; padding: 25px; background: #e8f5f3; border-radius: 10px; text-align: right; border: 2px solid #11998e; }');
    printWindow.document.write('.info-row { margin: 12px 0; display: flex; justify-content: space-between; }');
    printWindow.document.write('.label { color: #7f8c8d; font-size: 16px; }');
    printWindow.document.write('.value { color: #2c3e50; font-weight: bold; font-size: 18px; }');
    printWindow.document.write('.production-badge { background: #11998e; color: white; padding: 10px 20px; border-radius: 25px; display: inline-block; margin: 15px 0; font-weight: bold; }');
    printWindow.document.write('@media print { body { background: white; } }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<div class="barcode-container">');
    printWindow.document.write('<div class="title">🏭 {{ __('warehouse_registration.production_barcode') }}</div>');
    printWindow.document.write('<div class="production-badge">✓ {{ __('warehouse_registration.moved_to_production') }}</div>');
    printWindow.document.write('<div class="note-number">{{ __('warehouse_registration.delivery_note') }} ' + noteNumber + '</div>');
    printWindow.document.write('<svg id="print-barcode"></svg>');
    printWindow.document.write('<div class="barcode-code">' + barcode + '</div>');
    printWindow.document.write('<div class="info">');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('warehouse_registration.material') }}:</span><span class="value">' + materialName + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('warehouse_registration.production_barcode') }}:</span><span class="value">' + barcode + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('warehouse.date') }}:</span><span class="value">' + new Date().toLocaleDateString('ar-EG') + '</span></div>');
    printWindow.document.write('<div class="info-row"><span class="label">{{ __('warehouse.time') }}:</span><span class="value">' + new Date().toLocaleTimeString('ar-EG') + '</span></div>');
    printWindow.document.write('</div></div>');
    printWindow.document.write('<script>');
    printWindow.document.write('JsBarcode("#print-barcode", "' + barcode + '", { format: "CODE128", width: 2, height: 90, displayValue: false, margin: 12 });');
    printWindow.document.write('window.onload = function() { setTimeout(function() { window.print(); window.onafterprint = function() { window.close(); }; }, 500); };');
    printWindow.document.write('<\/script></body></html>');
    printWindow.document.close();
}
</script>

@endsection