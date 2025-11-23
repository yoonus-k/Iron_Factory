@extends('master')

@section('title', 'إنشاء أذن تسليم')

@section('content')

    <style>
        .form-section { display: none; }
        .form-section.active { display: block; }
    </style>

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

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            📝 إذن تسليم جديد
        </h1>
        <nav class="um-breadcrumb-nav">
            <span><i class="feather icon-home"></i> لوحة التحكم</span>
            <i class="feather icon-chevron-left"></i>
            <span>المستودع</span>
            <i class="feather icon-chevron-left"></i>
            <span>أذن تسليم</span>
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
            <strong>خطأ في البيانات:</strong>
            <ul style="margin: 8px 0 0 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form method="POST" action="{{ route('manufacturing.delivery-notes.store') }}" id="deliveryNoteForm">
        @csrf

        <!-- Type Selection -->
        <div class="form-section warehouse-only">
            <div class="section-header">
                <div class="section-icon personal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="section-title">🔄 نوع الأذن</h3>
                    <p class="section-subtitle">واردة من المورد أو صادرة للإنتاج</p>
                </div>
            </div>

            <div class="form-group">
                <div style="display: flex; gap: 20px; margin: 15px 0;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="radio" name="type" id="type_incoming" value="incoming" {{ old('type', 'incoming') === 'incoming' ? 'checked' : '' }} style="margin-right: 10px;">
                        <span style="font-size: 16px; font-weight: 500;">📥 واردة</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="radio" name="type" id="type_outgoing" value="outgoing" {{ old('type') === 'outgoing' ? 'checked' : '' }} style="margin-right: 10px;">
                        <span style="font-size: 16px; font-weight: 500;">📤 صادرة</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Basic Fields -->
        <div class="form-section warehouse-only">
            <div class="section-header">
                <div class="section-icon personal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    </svg>
                </div>
                <div>
                    <h3 class="section-title">📋 البيانات الأساسية</h3>
                    <p class="section-subtitle">التاريخ فقط</p>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="delivery_number" class="form-label">رقم الأذن 📌</label>
                    <div class="input-wrapper">
                        <input type="text" id="delivery_number" class="form-input" value="{{ $autoGeneratedNumber ?? 'سيتم توليده تلقائياً' }}" readonly style="background-color: #f0f0f0; cursor: not-allowed;">
                    </div>
                    <small style="color: #7f8c8d; margin-top: 5px;">يتم توليده تلقائياً</small>
                </div>

                <div class="form-group">
                    <label for="delivery_date" class="form-label">التاريخ <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <input type="date" name="delivery_date" id="delivery_date" class="form-input" value="{{ old('delivery_date', date('Y-m-d')) }}" required>
                    </div>
                    @error('delivery_date') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>

        <!-- Incoming Section -->
        <div class="form-section warehouse-only" id="incoming-section">
            <div class="section-header">
                <div class="section-icon personal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <h3 class="section-title">📥 الشحنة الواردة</h3>
                    <p class="section-subtitle">المادة والمستودع والوزن</p>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="incoming_material" class="form-label">المادة <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select name="material_id" id="incoming_material" class="form-input">
                            <option value="">اختر المادة</option>
                            @if(isset($materials) && count($materials) > 0)
                                @foreach($materials as $material)
                                    <option value="{{ $material->id }}">{{ $material->name_ar ?? 'بدون اسم' }}</option>
                                @endforeach
                            @else
                                <option disabled>لا توجد مواد متاحة</option>
                            @endif
                        </select>
                    </div>
                    @error('material_id') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="incoming_warehouse" class="form-label">المستودع <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select name="warehouse_id" id="incoming_warehouse" class="form-input">
                            <option value="">اختر المستودع</option>
                            @foreach($warehouses ?? [] as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('warehouse_id') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="invoice_weight" class="form-label">الوزن (كيلو) <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="number" name="invoice_weight" id="invoice_weight" class="form-input" placeholder="0" step="0.01">
                    </div>
                    @error('invoice_weight') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>

        <!-- Outgoing Section -->
        <div class="form-section warehouse-only" id="outgoing-section" style="display: none;">
            <div class="section-header">
                <div class="section-icon personal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 3v18M3 9h18M3 15h18"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="section-title">📤 الشحنة الصادرة</h3>
                    <p class="section-subtitle">المستودع والمادة والكمية</p>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="outgoing_warehouse" class="form-label">المستودع المصدر <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select name="warehouse_from_id" id="outgoing_warehouse" class="form-input">
                            <option value="">اختر المستودع</option>
                            @foreach($warehouses ?? [] as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('warehouse_from_id') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="outgoing_material" class="form-label">المادة <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select name="material_detail_id" id="outgoing_material" class="form-input">
                            <option value="">اختر المادة</option>
                        </select>
                    </div>
                    @error('material_detail_id') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                    <div style="margin-top: 10px; padding: 12px; background: #f8f9fa; border-radius: 6px; border-right: 3px solid #27ae60;">
                        <small style="color: #27ae60;" id="material_quantity_display">اختر المادة لعرض الكمية</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="outgoing_quantity" class="form-label">الكمية <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="number" name="delivery_quantity" id="outgoing_quantity" class="form-input" placeholder="0" step="0.01">
                    </div>
                    @error('delivery_quantity') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="destination" class="form-label">الوجهة <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                            <path d="M12 5v7l6 3.5"></path>
                        </svg>
                        <select name="destination_id" id="destination" class="form-input">
                            <option value="">-- اختر الوجهة --</option>
                            <option value="client">👤 للعميل</option>
                            <option value="production_transfer">🚚 نقل للإنتاج</option>
                        </select>
                    </div>
                    @error('destination_id') <small style="color: #e74c3c;">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn-submit" id="submitBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>حفظ الأذن</span>
            </button>
            <a href="{{ route('manufacturing.delivery-notes.index') }}" class="btn-cancel">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                إلغاء
            </a>
        </div>
    </form>

    <script>
        const materialDetails = {!! json_encode($materialDetails ?? []) !!};

        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const typeIncoming = document.getElementById('type_incoming');
            const typeOutgoing = document.getElementById('type_outgoing');
            const incomingSection = document.getElementById('incoming-section');
            const outgoingSection = document.getElementById('outgoing-section');

            const incomingMaterial = document.getElementById('incoming_material');
            const incomingWarehouse = document.getElementById('incoming_warehouse');
            const outgoingWarehouse = document.getElementById('outgoing_warehouse');
            const outgoingMaterial = document.getElementById('outgoing_material');
            const outgoingQuantity = document.getElementById('outgoing_quantity');
            const materialQuantityDisplay = document.getElementById('material_quantity_display');
            const destination = document.getElementById('destination');

            const form = document.getElementById('deliveryNoteForm');
            const submitBtn = document.getElementById('submitBtn');
            let isSubmitting = false;

            // Toggle sections based on type
            function updateSections() {
                if (typeIncoming.checked) {
                    incomingSection.style.display = '';
                    outgoingSection.style.display = 'none';
                } else {
                    incomingSection.style.display = 'none';
                    outgoingSection.style.display = '';
                }
            }

            typeIncoming.addEventListener('change', updateSections);
            typeOutgoing.addEventListener('change', updateSections);

            // Populate outgoing materials based on warehouse
            outgoingWarehouse.addEventListener('change', function() {
                const warehouseId = this.value;
                outgoingMaterial.innerHTML = '<option value="">اختر المادة</option>';
                materialQuantityDisplay.innerHTML = 'اختر المادة لعرض الكمية';

                if (warehouseId && Array.isArray(materialDetails)) {
                    const filtered = materialDetails.filter(m => m.warehouse_id == warehouseId && m.quantity > 0);
                    filtered.forEach(material => {
                        const option = document.createElement('option');
                        option.value = material.id;
                        option.setAttribute('data-material-id', material.material_id);
                        option.setAttribute('data-quantity', material.quantity);
                        option.setAttribute('data-unit', material.unit_name || 'كيلو');
                        option.text = `${material.material_name} (${material.quantity} ${material.unit_name || 'كيلو'})`;
                        outgoingMaterial.appendChild(option);
                    });
                }
            });

            // Update quantity display
            outgoingMaterial.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (this.value) {
                    const quantity = selectedOption.getAttribute('data-quantity');
                    const unit = selectedOption.getAttribute('data-unit');
                    materialQuantityDisplay.innerHTML = `✓ متوفر: <strong>${quantity} ${unit}</strong>`;
                    if (outgoingQuantity) outgoingQuantity.max = quantity;
                } else {
                    materialQuantityDisplay.innerHTML = 'اختر المادة لعرض الكمية';
                }
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return false;
                }

                const type = typeIncoming.checked ? 'incoming' : 'outgoing';

                // Validation for incoming
                if (type === 'incoming') {
                    if (!incomingMaterial.value) {
                        e.preventDefault();
                        incomingMaterial.focus();
                        alert('الرجاء اختيار المادة');
                        return false;
                    }
                    if (!incomingWarehouse.value) {
                        e.preventDefault();
                        incomingWarehouse.focus();
                        alert('الرجاء اختيار المستودع');
                        return false;
                    }
                } else {
                    // Validation for outgoing
                    if (!outgoingWarehouse.value) {
                        e.preventDefault();
                        outgoingWarehouse.focus();
                        alert('الرجاء اختيار المستودع المصدر');
                        return false;
                    }
                    if (!outgoingMaterial.value) {
                        e.preventDefault();
                        outgoingMaterial.focus();
                        alert('الرجاء اختيار المادة');
                        return false;
                    }
                    if (!outgoingQuantity.value || parseFloat(outgoingQuantity.value) <= 0) {
                        e.preventDefault();
                        outgoingQuantity.focus();
                        alert('الرجاء إدخال كمية صحيحة');
                        return false;
                    }
                    if (!destination.value) {
                        e.preventDefault();
                        destination.focus();
                        alert('الرجاء اختيار الوجهة');
                        return false;
                    }
                }

                // Prevent double submission
                isSubmitting = true;
                submitBtn.disabled = true;
            });

            updateSections();
        });
    </script>

@endsection
