@extends('master')

@section('title', __('delivery_notes.edit_delivery_note'))

@section('content')

    <style>
        .form-section.warehouse-only { display: block; }
        .form-section.admin-only { display: none; }
        .role-badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; margin-left: 8px; background: #d4edda; color: #155724; }

        select.form-input {
            width: 100% !important;
            padding: 10px !important;
            border: 2px solid #e0e0e0 !important;
            border-radius: 8px !important;
            background-color: white !important;
            color: #2c3e50 !important;
            font-size: 16px !important;
            direction: rtl !important;
            text-align: right !important;
        }

        select.form-input:hover { border-color: #3498db !important; }
        select.form-input:focus { border-color: #3498db !important; outline: none !important; box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1) !important; }

        .btn-submit:disabled {
            background-color: #95a5a6 !important;
            cursor: not-allowed !important;
            opacity: 0.7 !important;
        }
    </style>

    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            ✏️ {{ __('delivery_notes.edit_delivery_note') }}
        </h1>
        <nav class="um-breadcrumb-nav">
            <span><i class="feather icon-home"></i> {{ __('delivery_notes.dashboard') }}</span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('delivery_notes.warehouse') }}</span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('delivery_notes.delivery_note') }}</span>
        </nav>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">
            ❌ {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <strong>{{ __('delivery_notes.data_error') }}:</strong>
            <ul style="margin: 8px 0 0 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('manufacturing.delivery-notes.update', $deliveryNote->id) }}" id="deliveryNoteForm">
        @csrf
        @method('PUT')

        <div class="form-section warehouse-only">
            <div class="section-header">
                <div class="section-icon personal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="section-title">🔄 {{ __('delivery_notes.type') }}</h3>
                    <p class="section-subtitle">{{ __('delivery_notes.incoming_from_supplier') }} {{ __('delivery_notes.or') }} {{ __('delivery_notes.outgoing_to_customer') }}</p>
                </div>
            </div>

            <div class="form-group">
                <div style="display: flex; gap: 20px; margin: 15px 0;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="radio" name="type" id="type_incoming" value="incoming" {{ $deliveryNote->type === 'incoming' ? 'checked' : '' }} disabled style="margin-right: 10px;">
                        <span style="font-size: 16px; font-weight: 500;">📥 {{ __('delivery_notes.incoming') }}</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="radio" name="type" id="type_outgoing" value="outgoing" {{ $deliveryNote->type === 'outgoing' ? 'checked' : '' }} disabled style="margin-right: 10px;">
                        <span style="font-size: 16px; font-weight: 500;">📤 {{ __('delivery_notes.outgoing') }}</span>
                    </label>
                </div>
                <input type="hidden" name="type" value="{{ $deliveryNote->type }}">
            </div>
        </div>

        <div class="form-section warehouse-only">
            <div class="section-header">
                <div class="section-icon personal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    </svg>
                </div>
                <div>
                    <h3 class="section-title">📋 {{ __('delivery_notes.basic_info') }}</h3>
                    <p class="section-subtitle">{{ __('delivery_notes.date_and_number') }}</p>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="delivery_number" class="form-label">{{ __('delivery_notes.note_number') }} <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="delivery_number" id="delivery_number" class="form-input" value="{{ old('delivery_number', $deliveryNote->note_number) }}" required>
                    </div>
                    @error('delivery_number') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="delivery_date" class="form-label">{{ __('delivery_notes.date') }} <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <input type="date" name="delivery_date" id="delivery_date" class="form-input" value="{{ old('delivery_date', $deliveryNote->delivery_date->format('Y-m-d')) }}" required>
                    </div>
                    @error('delivery_date') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>

        <div class="form-section warehouse-only" id="incoming-section" {{ $deliveryNote->type === 'outgoing' ? 'style=display:none' : '' }}>
            <div class="section-header">
                <div class="section-icon personal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <h3 class="section-title">📥 {{ __('delivery_notes.incoming_shipment') }}</h3>
                    <p class="section-subtitle">{{ __('delivery_notes.material_warehouse_weight') }}</p>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="incoming_material" class="form-label">{{ __('delivery_notes.material') }} <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select name="material_id" id="incoming_material" class="form-input">
                            <option value="">{{ __('delivery_notes.select_material') }}</option>
                        </select>
                    </div>
                    @error('material_id') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                    <div style="margin-top: 10px; padding: 12px; background: #f8f9fa; border-radius: 6px; border-right: 3px solid #27ae60;">
                        <small style="color: #27ae60;" id="incoming_material_display">
                            @if($deliveryNote->type === 'incoming' && $deliveryNote->material_id)
                                ✓ {{ __('delivery_notes.currently_selected_material') }}
                            @else
                                {{ __('delivery_notes.select_warehouse_first') }}
                            @endif
                        </small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="incoming_warehouse" class="form-label">{{ __('delivery_notes.warehouse') }} <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select name="warehouse_id" id="incoming_warehouse" class="form-input">
                            <option value="">{{ __('delivery_notes.select_warehouse') }}</option>
                            @foreach($warehouses ?? [] as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $deliveryNote->warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->warehouse_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('warehouse_id') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="quantity_incoming_edit" class="form-label">{{ __('delivery_notes.quantity') }} ({{ __('delivery_notes.unit') }}) <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="number" name="quantity" id="quantity_incoming_edit" class="form-input" placeholder="0" step="0.01" value="{{ old('quantity', $deliveryNote->quantity) }}">
                    </div>
                    @error('quantity') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>

        <div class="form-section warehouse-only" id="outgoing-section" {{ $deliveryNote->type === 'incoming' ? 'style=display:none' : '' }}>
            <div class="section-header">
                <div class="section-icon personal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 3v18M3 9h18M3 15h18"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="section-title">📤 {{ __('delivery_notes.outgoing_shipment') }}</h3>
                    <p class="section-subtitle">{{ __('delivery_notes.warehouse_material_quantity') }}</p>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="warehouse_from_id" class="form-label">{{ __('delivery_notes.source_warehouse') }} <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select name="warehouse_from_id" id="warehouse_from_id" class="form-input">
                            <option value="">{{ __('delivery_notes.select_warehouse') }}</option>
                            @foreach($warehouses ?? [] as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_from_id', $deliveryNote->warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->warehouse_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('warehouse_from_id') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="material_detail_id_outgoing" class="form-label">{{ __('delivery_notes.material') }} <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select name="material_detail_id" id="material_detail_id_outgoing" class="form-input">
                            <option value="">{{ __('delivery_notes.select_material') }}</option>
                            @if($deliveryNote->material_detail_id)
                                @php
                                    $selected = $materialDetails->first(fn($m) => $m->id == $deliveryNote->material_detail_id);
                                @endphp
                                @if($selected)
                                    <option value="{{ $selected->id }}" selected>{{ $selected->name_ar }} ({{ $selected->quantity }} {{ $selected->unit_name ?? __('delivery_notes.unit') }})</option>
                                @endif
                            @endif
                        </select>
                    </div>
                    @error('material_detail_id') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                    <div style="margin-top: 10px; padding: 12px; background: #f8f9fa; border-radius: 6px; border-right: 3px solid #27ae60;">
                        <small style="color: #27ae60;" id="material_quantity_display">
                            @if($deliveryNote->material_detail_id)
                                @php
                                    $selected = $materialDetails->first(fn($m) => $m->id == $deliveryNote->material_detail_id);
                                @endphp
                                @if($selected)
                                    ✓ {{ __('delivery_notes.available') }}: <strong>{{ $selected->quantity }} {{ $selected->unit_name ?? __('delivery_notes.unit') }}</strong>
                                @else
                                    {{ __('delivery_notes.select_material_to_show_quantity') }}
                                @endif
                            @else
                                {{ __('delivery_notes.select_material_to_show_quantity') }}
                            @endif
                        </small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="delivery_quantity_outgoing" class="form-label">{{ __('delivery_notes.quantity') }} <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="number" name="delivery_quantity" id="delivery_quantity_outgoing" class="form-input" placeholder="0" step="0.01" value="{{ old('delivery_quantity', $deliveryNote->delivery_quantity) }}">
                    </div>
                    @error('delivery_quantity') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="destination" class="form-label">{{ __('delivery_notes.destination') }} <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                            <path d="M12 5v7l6 3.5"></path>
                        </svg>
                        <select name="destination_id" id="destination" class="form-input">
                            <option value="">{{ __('delivery_notes.select_destination') }}</option>
                            <option value="client" {{ old('destination_id', $deliveryNote->destination_id) === 'client' ? 'selected' : '' }}>👤 {{ __('delivery_notes.to_client') }}</option>
                            <option value="production_transfer" {{ old('destination_id', $deliveryNote->destination_id) === 'production_transfer' ? 'selected' : '' }}>🚚 {{ __('delivery_notes.production_transfer') }}</option>
                        </select>
                    </div>
                    @error('destination_id') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit" id="submitBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>{{ __('delivery_notes.save_changes') }}</span>
            </button>
            <a href="{{ route('manufacturing.delivery-notes.index') }}" class="btn-cancel">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                {{ __('delivery_notes.cancel') }}
            </a>
        </div>
    </form>

    <script>
        const materialDetails = {!! json_encode($materialDetails ?? []) !!};
        const deliveryNoteType = '{{ $deliveryNote->type }}';

        document.addEventListener('DOMContentLoaded', function() {
            const materialIncoming = document.getElementById('incoming_material');
            const incomingMaterialDisplay = document.getElementById('incoming_material_display');
            const warehouseIncoming = document.getElementById('incoming_warehouse');

            const warehouseFromId = document.getElementById('warehouse_from_id');
            const materialSelect = document.getElementById('material_detail_id_outgoing');
            const quantityInput = document.getElementById('delivery_quantity_outgoing');
            const materialQuantityDisplay = document.getElementById('material_quantity_display');

            function updateIncomingMaterials() {
                const warehouseId = warehouseIncoming.value;
                const currentMaterialId = '{{ $deliveryNote->material_id }}';
                materialIncoming.innerHTML = '<option value="">{{ __('delivery_notes.select_material') }}</option>';
                incomingMaterialDisplay.innerHTML = '{{ __('delivery_notes.loading_materials') }}...';

                if (warehouseId && Array.isArray(materialDetails)) {
                    const filtered = materialDetails.filter(m => m.warehouse_id == warehouseId);
                    if (filtered.length > 0) {
                        filtered.forEach(material => {
                            const option = document.createElement('option');
                            option.value = material.material_id;
                            option.setAttribute('data-quantity', material.quantity);
                            option.setAttribute('data-unit', material.unit_name || '{{ __('delivery_notes.unit') }}');
                            option.text = `${material.name_ar || material.material_name} (${material.quantity} ${material.unit_name || '{{ __('delivery_notes.unit') }}'})`;
                            if (material.material_id == currentMaterialId) {
                                option.selected = true;
                                incomingMaterialDisplay.innerHTML = `✓ {{ __('delivery_notes.material') }}: ${material.name_ar || material.material_name}`;
                            }
                            materialIncoming.appendChild(option);
                        });
                    } else {
                        incomingMaterialDisplay.innerHTML = '❌ {{ __('delivery_notes.no_materials_in_warehouse') }}';
                    }
                } else {
                    incomingMaterialDisplay.innerHTML = '{{ __('delivery_notes.select_warehouse_first') }}';
                }
            }

            warehouseIncoming.addEventListener('change', updateIncomingMaterials);

            materialIncoming.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (this.value) {
                    const quantity = selectedOption.getAttribute('data-quantity');
                    const unit = selectedOption.getAttribute('data-unit');
                    incomingMaterialDisplay.innerHTML = `✓ {{ __('delivery_notes.available') }}: <strong>${quantity} ${unit}</strong>`;
                } else {
                    incomingMaterialDisplay.innerHTML = '{{ __('delivery_notes.select_material_to_show_details') }}';
                }
            });

            function updateOutgoingMaterials() {
                const warehouseId = warehouseFromId.value;
                const currentMaterialId = '{{ $deliveryNote->material_detail_id }}';
                materialSelect.innerHTML = '<option value="">{{ __('delivery_notes.select_material') }}</option>';

                if (warehouseId && Array.isArray(materialDetails)) {
                    const filtered = materialDetails.filter(m => m.warehouse_id == warehouseId && m.quantity > 0);
                    filtered.forEach(material => {
                        const option = document.createElement('option');
                        option.value = material.id;
                        option.setAttribute('data-material-id', material.material_id);
                        option.setAttribute('data-quantity', material.quantity);
                        option.setAttribute('data-unit', material.unit_name || '{{ __('delivery_notes.unit') }}');
                        option.text = `${material.name_ar || material.material_name} (${material.quantity} ${material.unit_name || '{{ __('delivery_notes.unit') }}'})`;
                        if (material.id == currentMaterialId) {
                            option.selected = true;
                        }
                        materialSelect.appendChild(option);
                    });
                }
            }

            warehouseFromId.addEventListener('change', updateOutgoingMaterials);

            materialSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (this.value) {
                    const quantity = selectedOption.getAttribute('data-quantity');
                    const unit = selectedOption.getAttribute('data-unit');
                    materialQuantityDisplay.innerHTML = `✓ {{ __('delivery_notes.available') }}: <strong>${quantity} ${unit}</strong>`;
                    if (quantityInput) quantityInput.max = quantity;
                } else {
                    materialQuantityDisplay.innerHTML = '{{ __('delivery_notes.select_material_to_show_quantity') }}';
                }
            });

            const form = document.getElementById('deliveryNoteForm');
            const submitBtn = document.getElementById('submitBtn');
            let isSubmitting = false;

            form.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return false;
                }

                if (deliveryNoteType === 'incoming') {
                    if (!materialIncoming || !materialIncoming.value) {
                        e.preventDefault();
                        if (materialIncoming) materialIncoming.focus();
                        alert('{{ __('delivery_notes.please_select_material') }}');
                        return false;
                    }
                    if (!warehouseIncoming || !warehouseIncoming.value) {
                        e.preventDefault();
                        if (warehouseIncoming) warehouseIncoming.focus();
                        alert('{{ __('delivery_notes.please_select_warehouse') }}');
                        return false;
                    }
                } else {
                    if (!warehouseFromId.value) {
                        e.preventDefault();
                        warehouseFromId.focus();
                        alert('{{ __('delivery_notes.please_select_source_warehouse') }}');
                        return false;
                    }
                    if (!materialSelect.value) {
                        e.preventDefault();
                        materialSelect.focus();
                        alert('{{ __('delivery_notes.please_select_material') }}');
                        return false;
                    }
                    if (!quantityInput.value || parseFloat(quantityInput.value) <= 0) {
                        e.preventDefault();
                        quantityInput.focus();
                        alert('{{ __('delivery_notes.please_enter_valid_quantity') }}');
                        return false;
                    }
                    if (!document.getElementById('destination').value) {
                        e.preventDefault();
                        document.getElementById('destination').focus();
                        alert('{{ __('delivery_notes.please_select_destination') }}');
                        return false;
                    }
                }

                isSubmitting = true;
                submitBtn.disabled = true;

                return true;
            });

            if (deliveryNoteType === 'outgoing' && warehouseFromId.value) {
                updateMaterials();
                if (materialSelect.value) {
                    materialSelect.dispatchEvent(new Event('change'));
                }
            }
        });
    </script>
@endsection