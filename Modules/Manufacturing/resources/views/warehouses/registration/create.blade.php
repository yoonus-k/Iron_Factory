@extends('master')

@section('title', __('warehouse_registration.register_shipment'))

@section('content')
    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="um-page-title">
                        <i class="feather icon-edit-3"></i>
                        {{ __('warehouse_registration.register_new_shipment') }}
                    </h1>
                    <nav class="um-breadcrumb-nav">
                        <span>
                            <i class="feather icon-home"></i> {{ __('warehouse.dashboard') }}
                        </span>
                        <i class="feather icon-chevron-left"></i>
                        <span>{{ __('warehouse.warehouse') }}</span>
                        <i class="feather icon-chevron-left"></i>
                        <span>{{ __('warehouse.registration') }}</span>
                        <i class="feather icon-chevron-left"></i>
                        <span>#{{ $deliveryNote->note_number ?? $deliveryNote->id }}</span>
                    </nav>
                </div>
                <div class="col-auto">
                    <a href="{{ route('manufacturing.warehouse.registration.pending') }}" class="um-btn um-btn-outline">
                        <i class="feather icon-arrow-right"></i> {{ __('warehouse_registration.back') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Step Indicator -->
        <div class="um-alert-custom um-alert-info"
            style="display: flex; align-items: center; gap: 15px; margin-bottom: 24px;">
            <div
                style="background: #0066CC; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0; font-size: 18px;">
                1
            </div>
            <div>
                <strong>{{ __('warehouse_registration.step_1') }}</strong> {{ __('warehouse_registration.fill_registration_data') }}
            </div>
        </div>

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

        {{-- عرض جميع أخطاء التحقق --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-container">
                <div class="alert-header">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h4 class="alert-title">{{ __('warehouse_registration.validation_errors') }}</h4>
                    <button type="button" class="alert-close"
                        onclick="this.parentElement.parentElement.style.display='none'">
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
                                    <svg style="width: 16px; height: 16px; margin-left: 8px;" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2">
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


        <form action="{{ route('manufacturing.warehouse.registration.store', $deliveryNote) }}" method="POST"
            id="registrationForm">
            @csrf

            <div class="row">
                <!-- معلومات الشحنة -->
                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">📦 {{ __('warehouse_registration.shipment_info') }} ({{ __('warehouse_registration.reference_data') }})</h5>
                            <small class="text-muted">{{ __('warehouse_registration.read_only_data') }}</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label"><strong>{{ __('warehouse_registration.shipment_number') }}:</strong></label>
                                        <input type="text" class="form-control"
                                            value="{{ $deliveryNote->note_number ?? $deliveryNote->id }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label"><strong>{{ __('warehouse_registration.created_date') }}:</strong></label>
                                        <input type="text" class="form-control"
                                            value="{{ $deliveryNote->created_at->format('d/m/Y H:i') }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label"><strong>{{ __('warehouse_registration.supplier') }}:</strong></label>
                                <input type="text" class="form-control"
                                    value="{{ $deliveryNote->supplier->name ?? 'N/A' }}" disabled>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="form-label"><strong>{{ __('warehouse_registration.driver_name') }}:</strong></label>
                                        <input type="text" class="form-control"
                                            value="{{ $deliveryNote->driver_name ?? 'N/A' }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="form-label"><strong>{{ __('warehouse_registration.vehicle_number') }}:</strong></label>
                                        <input type="text" class="form-control"
                                            value="{{ $deliveryNote->vehicle_number ?? 'N/A' }}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- بيانات التسجيل المطلوبة -->
                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">{{ __('warehouse_registration.required_data') }}</h5>
                            <small>{{ __('warehouse_registration.all_fields_required') }} *</small>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-3" style="display: flex; gap: 10px; align-items: flex-start;">
                                <i class="fas fa-lightbulb" style="flex-shrink: 0; margin-top: 2px;"></i>
                                <div>
                                    <strong>{{ __('warehouse_registration.tip') }}</strong> {{ __('warehouse_registration.tip_message') }}
                                </div>
                            </div>

                            <!-- Hidden warehouse_id field -->
                            @if ($deliveryNote->warehouse_id)
                                <input type="hidden" name="warehouse_id" value="{{ $deliveryNote->warehouse_id }}">
                            @endif

                            <!-- المستودع والمادة في صف واحد -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="form-label"><strong>{{ __('warehouse_registration.warehouse_select') }} <span
                                                    class="text-danger">*</span></strong></label>
                                        <select name="warehouse_select" class="form-select @error('warehouse_id') is-invalid @enderror"
                                            id="warehouseSelect" required>
                                            <option value="">{{ __('warehouse_registration.select_warehouse') }}</option>
                                            @if ($deliveryNote->warehouse_id && $deliveryNote->warehouse)
                                                <option value="{{ $deliveryNote->warehouse_id }}" selected>
                                                    {{ $deliveryNote->warehouse->warehouse_name ?? __('warehouse.warehouse') }}
                                                </option>
                                            @else
                                                @foreach (\App\Models\Warehouse::where('is_active', true)->get() as $warehouse)
                                                    <option value="{{ $warehouse->id }}">
                                                        {{ $warehouse->warehouse_name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('warehouse_id')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="form-label"><strong>{{ __('warehouse_registration.material_select') }} <span
                                                    class="text-danger">*</span></strong></label>
                                        <select name="material_id" class="form-select @error('material_id') is-invalid @enderror"
                                            id="materialSelect" required>
                                            <option value="">{{ __('warehouse_registration.select_material') }}</option>
                                            @foreach ($materials as $mat)
                                                <option value="{{ $mat->id }}" @selected(old('material_id', $previousLog->material_id ?? '') == $mat->id)>
                                                    {{ $mat->name_ar }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('material_id')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label"><strong>{{ __('warehouse_registration.quantity_delivered') }} <span
                                            class="text-danger">*</span></strong></label>
                                <div class="input-group">
                                    <input type="number" name="quantity"
                                        class="form-control @error('quantity') is-invalid @enderror"
                                        placeholder="{{ __('warehouse_registration.quantity_delivered') }}" step="0.01" min="0.01"
                                        value="{{ old('quantity', $deliveryNote->quantity ?? $deliveryNote->delivered_weight ?? '') }}"
                                        readonly
                                        style="background-color: #f8f9fa; cursor: not-allowed;">
                                    <span class="input-group-text">{{ __('warehouse.unit') }}</span>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    ✅ {{ __('warehouse_registration.recorded_quantity') }}
                                    @if($deliveryNote->quantity && $deliveryNote->quantity > 0)
                                        - {{ __('warehouse_registration.registered_quantity') }}: <strong style="color: #0066CC;">{{ number_format($deliveryNote->quantity, 2) }}</strong> {{ __('warehouse.unit') }}
                                    @endif
                                </small>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label"><strong>{{ __('warehouse_registration.coil_number_input') }} <span class="text-muted">({{ __('warehouse.optional_field') }})</span></strong></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="feather icon-package"></i>
                                    </span>
                                    <input type="text" name="coil_number"
                                        class="form-control @error('coil_number') is-invalid @enderror"
                                        placeholder="{{ __('warehouse_registration.enter_coil_number') }}"
                                        value="{{ old('coil_number') }}"
                                        autocomplete="off">
                                </div>
                                <small class="text-muted d-block mt-1">
                                    ℹ️ {{ __('warehouse_registration.coil_number') }} - {{ __('warehouse_registration.use_for_all_stages') }}
                                </small>
                                @error('coil_number')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label"><strong>{{ __('warehouse_registration.actual_weight_from_scale') }} <span
                                            class="text-danger">*</span></strong></label>
                                <div class="input-group">
                                    <input type="number" name="actual_weight"
                                        class="form-control @error('actual_weight') is-invalid @enderror"
                                        placeholder="{{ __('warehouse_registration.enter_weight') }}" step="0.01" min="0.01"
                                        value="{{ old('actual_weight', $previousLog->weight_recorded ?? '') }}" required
                                        autocomplete="off">
                                    <span class="input-group-text">{{ __('warehouse.kg') }}</span>
                                </div>
                                @error('actual_weight')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label"><strong>{{ __('warehouse_registration.unit_select') }} <span
                                            class="text-danger">*</span></strong></label>
                                <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror"
                                    required>
                                    <option value="">{{ __('warehouse_registration.select_unit') }}</option>
                                    @foreach (\App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get() as $unit)
                                        <option value="{{ $unit->id }}" @selected(old('unit_id', $previousLog->unit_id ?? '') == $unit->id)>
                                            {{ $unit->unit_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-0">
                                <label class="form-label"><strong>{{ __('warehouse_registration.storage_location') }} <span
                                            class="text-danger">*</span></strong></label>
                                <input type="text" name="location"
                                    class="form-control @error('location') is-invalid @enderror"
                                    placeholder="{{ __('warehouse_registration.enter_storage_location') }}"
                                    value="{{ old('location', $previousLog->location ?? '') }}" required
                                    autocomplete="off">
                                @error('location')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ملاحظات -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('warehouse_registration.additional_notes') }} ({{ __('warehouse.optional_field') }})</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label class="form-label">{{ __('warehouse_registration.additional_notes_placeholder') }}:</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                            placeholder="{{ __('warehouse_registration.additional_notes_placeholder') }}" autocomplete="off">{{ old('notes') }}</textarea>
                        @error('notes')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- التأكيد والإرسال -->
            <div class="card border-success mb-4">
                <div class="card-body">
                    <div class="form-check mb-4">
                        <input type="checkbox" id="confirmCheck" class="form-check-input" required>
                        <label class="form-check-label" for="confirmCheck">
                            <strong>✓ {{ __('warehouse_registration.confirm_data_accuracy') }}</strong>
                        </label>
                    </div>

                    <div class="row g-2">
                        <div class="col-auto">
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
                                <i class="fas fa-check-circle"></i> {{ __('warehouse_registration.register_now') }}
                            </button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('manufacturing.warehouse.registration.pending') }}"
                                class="btn btn-secondary btn-lg">
                                <i class="fas fa-times-circle"></i> {{ __('warehouse_registration.cancel') }}
                            </a>
                        </div>
                    </div>

                    <div class="alert alert-light mt-3 mb-0" style="border-left: 3px solid #27ae60;">
                        <small style="color: #666;">
                            <strong>✓ {{ __('warehouse_registration.after_registration') }}</strong>
                        </small>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('confirmCheck').addEventListener('change', function() {
            document.getElementById('submitBtn').disabled = !this.checked;
        });
        document.getElementById('submitBtn').disabled = true;

        // تحديث حقل warehouse_id المخفي عند تغيير select المستودع
        const warehouseSelect = document.getElementById('warehouseSelect');
        if (warehouseSelect) {
            warehouseSelect.addEventListener('change', function() {
                // تحديث أو إنشاء hidden input
                let hiddenInput = document.querySelector('input[name="warehouse_id"]');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'warehouse_id';
                    document.getElementById('registrationForm').appendChild(hiddenInput);
                }
                hiddenInput.value = this.value;
            });

            // تعيين القيمة الأولية إذا كانت موجودة
            if (warehouseSelect.value) {
                let hiddenInput = document.querySelector('input[name="warehouse_id"]');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'warehouse_id';
                    document.getElementById('registrationForm').appendChild(hiddenInput);
                }
                hiddenInput.value = warehouseSelect.value;
            }
        }

        function usePreviousData() {
            document.getElementById('usePreviousBtn').style.display = 'none';
            document.getElementById('enterNewBtn').style.display = 'none';
            // البيانات مملوءة بالفعل من old() أو previousLog
            document.querySelector('.alert-warning').style.display = 'none';
        }

        function enterNewData() {
            // امسح البيانات السابقة
            document.querySelector('input[name="actual_weight"]').value = '';
            document.querySelector('select[name="material_id"]').value = '';
            document.querySelector('select[name="unit_id"]').value = '';
            document.querySelector('input[name="location"]').value = '';

            document.getElementById('usePreviousBtn').style.display = 'none';
            document.getElementById('enterNewBtn').style.display = 'none';
            document.querySelector('.alert-warning').style.display = 'none';
        }
    </script>
@endsection