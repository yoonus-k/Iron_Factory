@extends('master')

@section('title', __('warehouse_registration.transfer_to_production'))

@section('content')
<div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="um-page-title">
                        <i class="feather icon-arrow-left"></i>
                        {{ __('warehouse_registration.transfer_to_production') }}
                    </h1>
                    <nav class="um-breadcrumb-nav">
                        <span>
                            <i class="feather icon-home"></i> {{ __('app.dashboard') }}
                        </span>
                        <i class="feather icon-chevron-left"></i>
                        <span>{{ __('warehouse.warehouse') }}</span>
                        <i class="feather icon-chevron-left"></i>
                        <span>{{ __('warehouse_registration.registration') }}</span>
                        <i class="feather icon-chevron-left"></i>
                        <span>{{ __('warehouse_registration.transfer_to_production') }}</span>
                    </nav>
                </div>
                <div class="col-auto">
                    <a href="{{ route('manufacturing.warehouse.registration.show', $deliveryNote) }}" class="um-btn um-btn-outline">
                        <i class="feather icon-arrow-right"></i> {{ __('app.back') }}
                    </a>
                </div>
            </div>
        </div>
                @if (session('error'))
            <div class="um-alert-custom um-alert-error" role="alert" id="errorMessage">
                <i class="feather icon-alert-circle"></i>
                {{ session('error') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        {{-- عرض جميع أخطاء التحقق --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-container">
                <div class="alert-header">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h4 class="alert-title">{{ __('app.input_errors') }}</h4>
                    <button type="button" class="alert-close" onclick="this.parentElement.parentElement.style.display='none'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="alert-body">
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>
                                <span>
                                    <svg style="width: 16px; height: 16px; margin-left: 8px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                    </svg>
                                    {{ $error }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif


        <!-- Alerts -->
        @if ($errors->any())
            <div class="um-alert-custom um-alert-error" role="alert">
                <i class="feather icon-alert-circle"></i>
                <div>
                    <strong>{{ __('app.data_error') }}</strong>
                    <ul style="margin: 8px 0 0 0; padding-right: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <!-- معلومات البضاعة -->
                <section class="um-main-card">
                    <div class="um-card-header" style="background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%); color: white;">
                        <h4 class="um-card-title" style="color: white;">
                            <i class="feather icon-info"></i> {{ __('warehouse_registration.goods_info') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="info-label">{{ __('warehouse_registration.note_number') }}:</label>
                                    <div class="info-value">{{ $deliveryNote->note_number ?? $deliveryNote->id }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="info-label">{{ __('warehouse_registration.material') }}:</label>
                                    <div class="info-value">{{ $deliveryNote->material?->name ?? 'غير محددة' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="info-label">{{ __('warehouse_registration.supplier') }}:</label>
                                    <div class="info-value">{{ $deliveryNote->supplier?->name ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="info-label">{{ __('warehouse_registration.reception_date') }}:</label>
                                    <div class="info-value">{{ $deliveryNote->delivery_date?->format('Y-m-d H:i') ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info" style="margin-bottom: 0;">
                            <i class="fas fa-box"></i>
                            <strong>{{ __('warehouse_registration.quantity_status') }}:</strong>
                            <div style="margin-top: 10px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <div style="font-size: 12px; color: #666; margin-bottom: 3px;">{{ __('warehouse_registration.delivered_quantity') }} ({{ __('warehouse_registration.from_note') }}):</div>
                                    <div style="font-size: 18px; font-weight: bold; color: #3498db;">
                                        {{ number_format($deliveryNote->quantity ?? $availableQuantity, 2) }} {{ __('warehouse_registration.unit') }}
                                    </div>
                                </div>
                                <div>
                                    <div style="font-size: 12px; color: #666; margin-bottom: 3px;">{{ __('warehouse_registration.remaining_quantity_to_transfer') }}:</div>
                                    <div style="font-size: 18px; font-weight: bold; color: #27ae60;">
                                        {{ number_format($deliveryNote->quantity_remaining ?? ($deliveryNote->quantity ?? $availableQuantity), 2) }} {{ __('warehouse_registration.unit') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- شريط التقدم -->
                <section class="um-main-card">
                    <div class="um-card-body">
                        <div style="margin-bottom: 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <label style="font-weight: 600; color: #2c3e50; margin-bottom: 0;">{{ __('warehouse_registration.delivered_quantity_from_note') }}:</label>
                                <span style="font-weight: 600; color: #3498db;">
                                    {{ number_format($deliveryNote->quantity ?? $availableQuantity, 2) }} {{ __('warehouse_registration.unit') }}
                                </span>
                            </div>
                            <div class="progress" style="height: 25px; border-radius: 4px; overflow: hidden;">
                                <div class="progress-bar"
                                    style="width: 100%; background: linear-gradient(90deg, #3498db 0%, #2980b9 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">
                                    {{ number_format($deliveryNote->quantity ?? $availableQuantity, 2) }} {{ __('warehouse_registration.unit') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- نموذج النقل -->
                <section class="um-main-card">
                    <div class="um-card-header" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white;">
                        <h4 class="um-card-title" style="color: white;">
                            <i class="feather icon-arrow-left"></i> {{ __('warehouse_registration.transfer_to_production') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning" style="margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-exclamation-triangle" style="font-size: 18px;"></i>
                                <div>
                                    <strong>{{ __('warehouse_registration.important_note') }}:</strong> {{ __('warehouse_registration.will_transfer_full_quantity') }} {{ __('warehouse_registration.cannot_partial_transfer') }}
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('manufacturing.warehouse.registration.transfer-to-production', $deliveryNote) }}" method="POST">
                            @csrf

                            <!-- استخدام الكمية من أذن الاستلام -->
                            <input type="hidden" name="quantity" value="{{ $deliveryNote->quantity ?? $availableQuantity }}">

                            <div class="mb-4">
                                <label class="form-label" style="font-weight: 600;">
                                    <i class="fas fa-weight"></i> {{ __('warehouse_registration.quantity_to_transfer') }} ({{ __('warehouse_registration.unit') }}):
                                </label>
                                <div style="font-size: 24px; font-weight: bold; color: #27ae60; padding: 15px; background: #f8f9fa; border-radius: 4px; border: 1px solid #ddd;">
                                    {{ number_format($deliveryNote->quantity ?? $availableQuantity, 2) }} {{ __('warehouse_registration.unit') }}
                                </div>
                                <small class="form-text" style="color: #666; margin-top: 8px;">
                                    <i class="fas fa-info-circle"></i>
                                    {{ __('warehouse_registration.original_delivery_note_quantity') }}
                                </small>
                            </div>                            <div class="mb-4">
                                <label for="notes" class="form-label" style="font-weight: 600;">
                                    <i class="fas fa-sticky-note"></i> {{ __('app.notes') }} ({{ __('app.optional') }}):
                                </label>

                                <textarea
                                    id="notes"
                                    name="notes"
                                    class="form-control"
                                    rows="3"
                                    placeholder="مثال: نقل كامل للإنتاج"
                                    style="padding: 12px; border-radius: 4px; border: 1px solid #ddd; font-size: 14px;"></textarea>

                                <small class="form-text" style="color: #666; margin-top: 5px;">
                                    {{ __('warehouse_registration.add_transfer_related_notes') }}
                                </small>
                            </div>

                            <!-- معاينة -->
                            <div class="alert alert-info">
                                <strong>{{ __('warehouse_registration.operation_preview') }}:</strong>
                                <div style="margin-top: 10px; font-size: 13px; line-height: 1.8;">
                                    <div>
                                        <i class="fas fa-arrow-down" style="color: #3498db;"></i>
                                        <strong>{{ __('warehouse_registration.before_transfer') }}:</strong>
                                    </div>
                                    <div style="margin-right: 20px; margin-bottom: 10px;">
                                        {{ __('warehouse_registration.delivered_quantity') }}: <strong>{{ number_format($deliveryNote->quantity ?? $availableQuantity, 2) }}</strong> {{ __('warehouse_registration.unit') }}
                                    </div>

                                    <div>
                                        <i class="fas fa-arrow-up" style="color: #27ae60;"></i>
                                        <strong>{{ __('warehouse_registration.after_transfer') }}:</strong>
                                    </div>
                                    <div style="margin-right: 20px;">
                                        {{ __('warehouse_registration.remaining_quantity') }}: <strong>0.00</strong> {{ __('warehouse_registration.unit') }}
                                    </div>
                                </div>
                            </div>

                            <!-- الأزرار -->
                            <div style="display: flex; gap: 12px;">
                                <a href="{{ route('manufacturing.warehouse.registration.show', $deliveryNote) }}" class="um-btn um-btn-outline" style="flex: 0 0 auto;">
                                    <i class="feather icon-x"></i> {{ __('app.cancel') }}
                                </a>
                                <button type="submit" class="um-btn um-btn-primary" style="flex: 1; background: linear-gradient(135deg, #10B981 0%, #059669 100%); border: none;">
                                    <i class="feather icon-check"></i> {{ __('warehouse_registration.confirm_full_transfer') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>


    <style>
        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .btn-link {
            color: #3498db;
            text-decoration: none;
        }

        .btn-link:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .info-group {
            margin-bottom: 15px;
        }

        .info-label {
            display: block;
            font-size: 12px;
            color: #999;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-label {
            color: #2c3e50;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-control {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px 12px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn {
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .btn-success {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            border: none;
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
        }

        .btn-outline-secondary {
            border: 2px solid #bdc3c7;
            color: #2c3e50;
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background-color: #ecf0f1;
            border-color: #95a5a6;
        }

        .progress {
            background-color: #ecf0f1;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
            margin-bottom: 20px;
        }

        .card-header {
            border-bottom: 2px solid rgba(0, 0, 0, 0.1);
        }

        .alert {
            border-radius: 4px;
            border: none;
        }
    </style>
@endsection
