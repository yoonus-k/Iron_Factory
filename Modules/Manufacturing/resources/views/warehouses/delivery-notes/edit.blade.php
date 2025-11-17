@extends('master')

@section('title', 'تعديل أذن التسليم')

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            تعديل أذن التسليم
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>
                <i class="feather icon-home"></i> لوحة التحكم
            </span>
            <i class="feather icon-chevron-left"></i>
            <span>المستودع</span>
            <i class="feather icon-chevron-left"></i>
            <span>أذون التسليم</span>
            <i class="feather icon-chevron-left"></i>
            <span>تعديل أذن</span>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <!-- Error Alert -->
        @if ($errors->any())
            <div style="margin-bottom: 20px; padding: 15px; background-color: #fee; border: 1px solid #fcc; border-radius: 6px; border-right: 4px solid #e74c3c;">
                <div style="display: flex; align-items: flex-start; gap: 10px;">
                    <svg style="width: 24px; height: 24px; color: #e74c3c; flex-shrink: 0; margin-top: 2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div>
                        <h4 style="margin: 0 0 8px 0; color: #c0392b; font-weight: 600;">⚠️ هناك أخطاء في النموذج</h4>
                        <ul style="margin: 0; padding-left: 20px; color: #c0392b;">
                            @foreach ($errors->all() as $error)
                                <li style="margin: 4px 0;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('manufacturing.delivery-notes.update', $deliveryNote->id) }}" id="deliveryNoteForm">
            @csrf
            @method('PUT')

            <!-- Type Display Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">نوع الأذن</h3>
                        <p class="section-subtitle">{{ $deliveryNote->type === 'incoming' ? '🔽 أذن واردة (من المورد)' : '🔼 أذن صادرة (للزبون)' }}</p>
                    </div>
                </div>

                <input type="hidden" name="type" value="{{ $deliveryNote->type }}">
            </div>

            <!-- Basic Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">المعلومات الأساسية</h3>
                        <p class="section-subtitle">قم بتحديث بيانات الأذن</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="delivery_number" class="form-label">
                            رقم الأذن
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="21 8 21 21 3 21 3 8"></polyline>
                                <line x1="1" y1="3" x2="23" y2="3"></line>
                                <path d="M10 12v4"></path>
                                <path d="M14 12v4"></path>
                            </svg>
                            <input type="text" name="delivery_number" id="delivery_number"
                                class="form-input {{ $errors->has('delivery_number') ? 'is-invalid' : '' }}" placeholder="مثال: DN-2024-001" value="{{ old('delivery_number', $deliveryNote->note_number) }}" required>
                        </div>
                        @if ($errors->has('delivery_number'))
                            <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('delivery_number') }}</small>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="delivery_date" class="form-label">
                            التاريخ
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <input type="date" name="delivery_date" id="delivery_date"
                                class="form-input {{ $errors->has('delivery_date') ? 'is-invalid' : '' }}" value="{{ old('delivery_date', $deliveryNote->delivery_date->format('Y-m-d')) }}" required>
                        </div>
                        @if ($errors->has('delivery_date'))
                            <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('delivery_date') }}</small>
                        @endif
                    </div>

                    <!-- ✅ جديد: اختيار المستودع (إجباري) -->
                    <div class="form-group">
                        <label for="warehouse_id" class="form-label">
                            المستودع
                            <span class="required">*</span>
                            <small style="color: #27ae60; display: block; margin-top: 5px;">👈 المستودع الرئيسي</small>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            </svg>
                            <select name="warehouse_id" id="warehouse_id" class="form-input {{ $errors->has('warehouse_id') ? 'is-invalid' : '' }}" required>
                                <option value="">-- اختر المستودع --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $deliveryNote->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->warehouse_name ?? $warehouse->name }} [{{ $warehouse->warehouse_code ?? '' }}]
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('warehouse_id'))
                            <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('warehouse_id') }}</small>
                        @endif
                    </div>

                    <!-- ✅ معدّل: اختيار المادة (اختياري الآن) -->
                    <div class="form-group">
                        <label for="material_id" class="form-label">
                            المادة
                            <small style="color: #95a5a6; display: block; margin-top: 5px;">(اختياري)</small>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            </svg>
                            <select name="material_id" id="material_id" class="form-input {{ $errors->has('material_id') ? 'is-invalid' : '' }}">
                                <option value="">-- اختر المادة (اختياري) --</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material->id }}" {{ old('material_id', $deliveryNote->material_id) == $material->id ? 'selected' : '' }}>
                                        {{ $material->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('material_id'))
                            <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('material_id') }}</small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Weight Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="5" r="3"></circle>
                            <line x1="9" y1="9" x2="9" y2="16"></line>
                            <line x1="15" y1="9" x2="15" y2="16"></line>
                            <path d="M9 16h6"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">معلومات الوزن</h3>
                        <p class="section-subtitle">سجل الأوزان من الميزان والفاتورة</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="weight_from_scale" class="form-label">
                            الوزن المسجل من الميزان (كجم)
                            <span class="required">*</span>
                            <small style="color: #27ae60; display: block; margin-top: 5px;">👈 الوزن الرئيسي من الميزان</small>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="5" r="3"></circle>
                                <line x1="9" y1="9" x2="9" y2="16"></line>
                                <line x1="15" y1="9" x2="15" y2="16"></line>
                                <path d="M9 16h6"></path>
                            </svg>
                            <input type="number" name="weight_from_scale" id="weight_from_scale"
                                class="form-input {{ $errors->has('weight_from_scale') ? 'is-invalid' : '' }}" placeholder="0.00" step="0.01" value="{{ old('weight_from_scale', $deliveryNote->weight_from_scale) }}" required>
                        </div>
                        @if ($errors->has('weight_from_scale'))
                            <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('weight_from_scale') }}</small>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="actual_weight" class="form-label">
                            الوزن الفعلي (كجم)
                            <span class="required">*</span>
                            <small style="color: #7f8c8d; display: block; margin-top: 5px;">الوزن المسجل بالنظام</small>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="5" r="3"></circle>
                                <line x1="9" y1="9" x2="9" y2="16"></line>
                                <line x1="15" y1="9" x2="15" y2="16"></line>
                                <path d="M9 16h6"></path>
                            </svg>
                            <input type="number" name="actual_weight" id="actual_weight"
                                class="form-input {{ $errors->has('actual_weight') ? 'is-invalid' : '' }}" placeholder="0.00" step="0.01" value="{{ old('actual_weight', $deliveryNote->actual_weight ?? $deliveryNote->delivered_weight) }}" required>
                        </div>
                        @if ($errors->has('actual_weight'))
                            <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('actual_weight') }}</small>
                        @endif
                    </div>

                    @if($deliveryNote->weight_discrepancy)
                        <div class="form-group">
                            <label class="form-label">
                                الفرق في الوزن
                            </label>
                            <div class="input-wrapper">
                                <div style="padding: 10px 15px; background: #ecf0f1; border-radius: 4px; font-weight: 500;">
                                    {{ $deliveryNote->weight_discrepancy >= 0 ? '+' : '' }}{{ $deliveryNote->weight_discrepancy }} كجم
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Incoming Details Section (conditional) -->
            @if($deliveryNote->isIncoming())
                <div class="form-section" id="incoming-section">
                    <div class="section-header">
                        <div class="section-icon personal">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">بيانات الموردين</h3>
                            <p class="section-subtitle">معلومات المورد والتسليم</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="supplier_id" class="form-label">
                                المورد
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <select name="supplier_id" id="supplier_id" class="form-input {{ $errors->has('supplier_id') ? 'is-invalid' : '' }}" required>
                                    <option value="">-- اختر المورد --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $deliveryNote->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($errors->has('supplier_id'))
                                <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('supplier_id') }}</small>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="driver_name" class="form-label">اسم السائق</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <input type="text" name="driver_name" id="driver_name"
                                    class="form-input {{ $errors->has('driver_name') ? 'is-invalid' : '' }}" placeholder="اسم السائق" value="{{ old('driver_name', $deliveryNote->driver_name) }}">
                            </div>
                            @if ($errors->has('driver_name'))
                                <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('driver_name') }}</small>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="vehicle_number" class="form-label">رقم المركبة</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <input type="text" name="vehicle_number" id="vehicle_number"
                                    class="form-input {{ $errors->has('vehicle_number') ? 'is-invalid' : '' }}" placeholder="مثال: أ ب ت 1234" value="{{ old('vehicle_number', $deliveryNote->vehicle_number) }}">
                            </div>
                            @if ($errors->has('vehicle_number'))
                                <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('vehicle_number') }}</small>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="invoice_reference_number" class="form-label">رقم مرجع الفاتورة</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <line x1="9" y1="11" x2="15" y2="11"></line>
                                    <line x1="9" y1="15" x2="15" y2="15"></line>
                                </svg>
                                <input type="text" name="invoice_reference_number" id="invoice_reference_number"
                                    class="form-input {{ $errors->has('invoice_reference_number') ? 'is-invalid' : '' }}" placeholder="رقم الفاتورة من المورد" value="{{ old('invoice_reference_number', $deliveryNote->invoice_reference_number) }}">
                            </div>
                            @if ($errors->has('invoice_reference_number'))
                                <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('invoice_reference_number') }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Outgoing Details Section (conditional) -->
            @if($deliveryNote->isOutgoing())
                <div class="form-section" id="outgoing-section">
                    <div class="section-header">
                        <div class="section-icon personal">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 3v18M3 9h18M3 15h18"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">بيانات الوجهة</h3>
                            <p class="section-subtitle">معلومات المستودع أو الوجهة</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="destination_id" class="form-label">
                                المستودع / الوجهة
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                </svg>
                                <select name="destination_id" id="destination_id" class="form-input {{ $errors->has('destination_id') ? 'is-invalid' : '' }}" required>
                                    <option value="">-- اختر الوجهة --</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('destination_id', $deliveryNote->destination_id) == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($errors->has('destination_id'))
                                <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('destination_id') }}</small>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="received_by" class="form-label">المستقبل</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <select name="received_by" id="received_by" class="form-input {{ $errors->has('received_by') ? 'is-invalid' : '' }}">
                                    <option value="">-- اختر المستخدم --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('received_by', $deliveryNote->received_by) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($errors->has('received_by'))
                                <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('received_by') }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    حفظ التغييرات
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('deliveryNoteForm');
            const inputs = form.querySelectorAll('.form-input');

            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.required && !this.value) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            form.addEventListener('submit', function(e) {
                const firstInvalid = form.querySelector('.is-invalid, :invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            });
        });
    </script>

@endsection
