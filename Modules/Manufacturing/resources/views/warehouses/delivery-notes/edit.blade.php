@extends('master')

@section('title', 'تعديل أذن التسليم - بيانات مبسطة')

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            ✏️ تعديل أذن التسليم
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

    <!-- Process Explanation -->
    <div class="alert alert-info mb-4" style="border-right: 4px solid #3498db;">
        <h5 class="mb-2"><strong>📌 نظام العمل الجديد (ثلاث مراحل):</strong></h5>
        <div style="display: grid; gap: 10px; margin-top: 15px;">
            <div style="background: white; padding: 12px; border-radius: 4px; border-right: 3px solid #3498db;">
                <strong>المرحلة 1 - إنشاء الأذن (هنا):</strong>
                <span style="color: #666; display: block; margin-top: 4px;">
                    📝 بيانات أساسية فقط (رقم الأذن، التاريخ، المورد، المستودع) بدون أوزان أو كميات
                </span>
            </div>
            <div style="background: white; padding: 12px; border-radius: 4px; border-right: 3px solid #27ae60;">
                <strong>المرحلة 2 - التسجيل:</strong>
                <span style="color: #666; display: block; margin-top: 4px;">
                    ⚖️ تسجيل الوزن الفعلي من الميزان + تحديد المادة + الكمية بعد الفحص
                </span>
            </div>
            <div style="background: white; padding: 12px; border-radius: 4px; border-right: 3px solid #f39c12;">
                <strong>المرحلة 3 - التسوية (عند وصول الفاتورة):</strong>
                <span style="color: #666; display: block; margin-top: 4px;">
                    📊 ربط الفاتورة + حساب الفروقات بين الوزن الفعلي ووزن الفاتورة
                </span>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="form-card">
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

        @if ($errors->any())
            <div class="alert alert-danger alert-container">
                <div class="alert-header">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h4 class="alert-title">يوجد أخطاء في البيانات المدخلة</h4>
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
                        <p class="section-subtitle">بيانات الأذن الأساسية فقط - بدون أوزان أو كميات</p>
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

                    <div class="form-group">
                        <label for="warehouse_id" class="form-label">
                            المستودع
                            <span class="required">*</span>
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
                            <label for="driver_name" class="form-label">اسم السائق (اختياري)</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <input type="text" name="driver_name" id="driver_name"
                                    class="form-input {{ $errors->has('driver_name') ? 'is-invalid' : '' }}" placeholder="اسم السائق" value="{{ old('driver_name', $deliveryNote->driver_name) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="vehicle_number" class="form-label">رقم المركبة (اختياري)</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <input type="text" name="vehicle_number" id="vehicle_number"
                                    class="form-input {{ $errors->has('vehicle_number') ? 'is-invalid' : '' }}" placeholder="مثال: أ ب ت 1234" value="{{ old('vehicle_number', $deliveryNote->vehicle_number) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="invoice_reference_number" class="form-label">رقم مرجع الفاتورة (اختياري)</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <line x1="9" y1="11" x2="15" y2="11"></line>
                                    <line x1="9" y1="15" x2="15" y2="15"></line>
                                </svg>
                                <input type="text" name="invoice_reference_number" id="invoice_reference_number"
                                    class="form-input {{ $errors->has('invoice_reference_number') ? 'is-invalid' : '' }}" placeholder="رقم الفاتورة من المورد (إن وجدت)" value="{{ old('invoice_reference_number', $deliveryNote->invoice_reference_number) }}">
                            </div>
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
                    </div>
                </div>
            @endif

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
                        <label for="notes" class="form-label">الملاحظات (اختياري)</label>
                        <div class="input-wrapper">
                            <textarea name="notes" id="notes"
                                class="form-input {{ $errors->has('notes') ? 'is-invalid' : '' }}" rows="3" placeholder="أدخل أي ملاحظات إضافية...">{{ old('notes', $deliveryNote->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Steps Info -->
            <div class="alert alert-success mb-4" style="border-right: 4px solid #27ae60;">
                <h5 class="mb-2"><strong>✅ الخطوات التالية بعد تعديل الأذن:</strong></h5>
                <ol style="margin: 0; padding-right: 20px;">
                    <li style="margin-bottom: 8px;">إذا لم يتم التسجيل بعد، ستظهر الأذن في "الشحنات المعلقة للتسجيل"</li>
                    <li style="margin-bottom: 8px;">عند وصول البضاعة، اذهب إلى صفحة التسجيل لإدخال الوزن الفعلي والكمية</li>
                    <li style="margin-bottom: 8px;">بعد وصول الفاتورة، يمكن ربطها وحساب الفروقات في صفحة التسوية</li>
                </ol>
            </div>

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
