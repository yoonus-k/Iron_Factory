@extends('master')

@section('title', 'إضافة أذن مخزني جديد')

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="12" y1="11" x2="12" y2="17"></line>
                <line x1="9" y1="14" x2="15" y2="14"></line>
            </svg>
            إضافة أذن تسليم جديدة
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
            <span>إضافة أذن جديدة</span>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.delivery-notes.store') }}" id="deliveryNoteForm">
            @csrf

            <!-- Type Selection Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">نوع الأذن</h3>
                        <p class="section-subtitle">حدد ما إذا كانت أذن واردة أو صادرة</p>
                    </div>
                </div>

                <div class="form-group">
                    <div style="display: flex; gap: 20px; margin: 15px 0;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="type" id="type_incoming" value="incoming"
                                class="form-input" {{ old('type', 'incoming') === 'incoming' ? 'checked' : '' }}
                                style="margin-right: 10px; cursor: pointer;">
                            <span style="font-size: 16px; font-weight: 500;">🔽 أذن واردة (من المورد)</span>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="type" id="type_outgoing" value="outgoing"
                                class="form-input" {{ old('type') === 'outgoing' ? 'checked' : '' }}
                                style="margin-right: 10px; cursor: pointer;">
                            <span style="font-size: 16px; font-weight: 500;">🔼 أذن صادرة (للزبون)</span>
                        </label>
                    </div>
                </div>
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
                        <p class="section-subtitle">أدخل بيانات الأذن الأساسية</p>
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
                                class="form-input" placeholder="سيتم التعبئة تلقائياً" value="{{ old('delivery_number', $autoGeneratedNumber ?? '') }}" readonly required>
                        </div>
                        <small style="color: #7f8c8d; display: block; margin-top: 5px;">✓ يتم إنشاء رقم الأذن تلقائياً</small>
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
                                class="form-input" value="{{ old('delivery_date') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="material_detail_id" class="form-label">
                            المادة من المستودع
                            <span class="required">*</span>
                            <small style="color: #7f8c8d; display: block; margin-top: 5px;">استخدام المنتجات الموجودة بالفعل</small>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            </svg>
                            <select name="material_detail_id" id="material_detail_id" class="form-input" required onchange="updateMaterialInfo()">
                                <option value="">-- اختر المادة من المستودع --</option>
                                @foreach($materialDetails as $detail)
                                    <option value="{{ $detail->id }}" data-quantity="{{ $detail->quantity }}" data-unit="{{ $detail->unit->name ?? '' }}" data-warehouse="{{ $detail->warehouse->warehouse_name ?? '' }}" data-actual-weight="{{ $detail->actual_weight ?? 0 }}" data-original-weight="{{ $detail->original_weight ?? 0 }}" {{ old('material_detail_id') == $detail->id ? 'selected' : '' }}>
                                        [{{ $detail->warehouse->warehouse_code }}] {{ $detail->material->name }} - {{ $detail->quantity }} {{ $detail->unit->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Material Info Display -->
                <div id="material-info" style="display: none; margin-top: 20px;">
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #3498db;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                            <div>
                                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 4px;">المستودع</div>
                                <div id="info-warehouse" style="font-weight: 600; color: #2c3e50;"></div>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 4px;">الكمية المتوفرة</div>
                                <div id="info-quantity" style="font-weight: 600; color: #2c3e50;"></div>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 4px;">الوزن الحالي</div>
                                <div id="info-weight" style="font-weight: 600; color: #2c3e50;"></div>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 4px;">الوزن الأصلي</div>
                                <div id="info-original" style="font-weight: 600; color: #2c3e50;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quantity Section -->
                <div class="form-section" style="margin-top: 20px;">
                    <div class="section-header">
                        <div class="section-icon personal">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">كمية الأذن</h3>
                            <p class="section-subtitle">حدد كمية المنتج في هذه الأذن</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="delivery_quantity" class="form-label">
                                كمية الأذن
                                <span class="required">*</span>
                                <small style="color: #7f8c8d; display: block; margin-top: 5px;">الكمية المسلمة في هذه الأذن</small>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <input type="number" name="delivery_quantity" id="delivery_quantity"
                                    class="form-input" placeholder="0.00" step="0.01" value="{{ old('delivery_quantity') }}" required>
                            </div>
                        </div>
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
                        <label for="actual_weight" class="form-label">
                            الوزن الفعلي (كجم)
                            <span class="required">*</span>
                            <small style="color: #7f8c8d; display: block; margin-top: 5px;">الوزن المسجل من الميزان</small>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="5" r="3"></circle>
                                <line x1="9" y1="9" x2="9" y2="16"></line>
                                <line x1="15" y1="9" x2="15" y2="16"></line>
                                <path d="M9 16h6"></path>
                            </svg>
                            <input type="number" name="actual_weight" id="actual_weight"
                                class="form-input" placeholder="0.00" step="0.01" value="{{ old('actual_weight') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="invoice_weight" class="form-label">
                            وزن الفاتورة (كجم)
                            <small style="color: #7f8c8d; display: block; margin-top: 5px;">الوزن في فاتورة الموردين (اختياري)</small>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="5" r="3"></circle>
                                <line x1="9" y1="9" x2="9" y2="16"></line>
                                <line x1="15" y1="9" x2="15" y2="16"></line>
                                <path d="M9 16h6"></path>
                            </svg>
                            <input type="number" name="invoice_weight" id="invoice_weight"
                                class="form-input" placeholder="0.00" step="0.01" value="{{ old('invoice_weight') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Incoming Details Section (conditional) -->
            <div class="form-section" id="incoming-section" style="{{ old('type', 'incoming') === 'incoming' ? '' : 'display: none;' }}">
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
                            <span class="required" id="supplier_required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <select name="supplier_id" id="supplier_id" class="form-input">
                                <option value="">-- اختر المورد --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="driver_name" class="form-label">اسم السائق</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <input type="text" name="driver_name" id="driver_name"
                                class="form-input" placeholder="اسم السائق" value="{{ old('driver_name') }}">
                        </div>
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
                                class="form-input" placeholder="مثال: أ ب ت 1234" value="{{ old('vehicle_number') }}">
                        </div>
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
                                class="form-input" placeholder="رقم الفاتورة من المورد" value="{{ old('invoice_reference_number') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Outgoing Details Section (conditional) -->
            <div class="form-section" id="outgoing-section" style="{{ old('type') === 'outgoing' ? '' : 'display: none;' }}">
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
                            <span class="required" id="destination_required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            </svg>
                            <select name="destination_id" id="destination_id" class="form-input">
                                <option value="">-- اختر الوجهة --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('destination_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="received_by" class="form-label">المستقبل</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <select name="received_by" id="received_by" class="form-input">
                                <option value="">-- اختر المستخدم --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('received_by') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">ملاحظات إضافية</h3>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="notes" class="form-label">الملاحظات</label>
                        <div class="input-wrapper">
                            <textarea name="notes" id="notes"
                                class="form-input" rows="4" placeholder="أدخل أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    حفظ الأذن
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
        // تحديث معلومات المادة عند الاختيار
        function updateMaterialInfo() {
            const select = document.getElementById('material_detail_id');
            const option = select.options[select.selectedIndex];
            const infoDiv = document.getElementById('material-info');

            if (select.value) {
                document.getElementById('info-warehouse').textContent = option.dataset.warehouse;
                document.getElementById('info-quantity').textContent = option.dataset.quantity + ' ' + option.dataset.unit;
                document.getElementById('info-weight').textContent = option.dataset.actualWeight + ' كجم';
                document.getElementById('info-original').textContent = option.dataset.originalWeight + ' كجم';
                infoDiv.style.display = 'block';
            } else {
                infoDiv.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const typeIncoming = document.getElementById('type_incoming');
            const typeOutgoing = document.getElementById('type_outgoing');
            const incomingSection = document.getElementById('incoming-section');
            const outgoingSection = document.getElementById('outgoing-section');
            const supplierId = document.getElementById('supplier_id');
            const destinationId = document.getElementById('destination_id');

            function updateVisibility() {
                if (typeIncoming.checked) {
                    incomingSection.style.display = '';
                    outgoingSection.style.display = 'none';
                    supplierId.required = true;
                    destinationId.required = false;
                } else {
                    incomingSection.style.display = 'none';
                    outgoingSection.style.display = '';
                    supplierId.required = false;
                    destinationId.required = true;
                }
            }

            typeIncoming.addEventListener('change', updateVisibility);
            typeOutgoing.addEventListener('change', updateVisibility);

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

            // تحديث معلومات المادة عند التحميل
            updateMaterialInfo();
        });
    </script>

@endsection
