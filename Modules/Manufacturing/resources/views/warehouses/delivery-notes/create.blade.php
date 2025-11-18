@extends('master')

@section('title', 'إنشاء أذن تسليم - بيانات مبسطة')

@section('content')

    <style>
        .info-tooltip {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            background: #3498db;
            color: white;
            border-radius: 50%;
            font-size: 12px;
            font-weight: bold;
            cursor: help;
            margin-right: 5px;
            vertical-align: middle;
        }

        .info-tooltip:hover {
            background: #2980b9;
        }

        .info-tooltip .tooltip-text {
            visibility: hidden;
            width: 300px;
            background-color: #2c3e50;
            color: #fff;
            text-align: right;
            border-radius: 6px;
            padding: 12px;
            position: absolute;
            z-index: 1000;
            bottom: 125%;
            right: 50%;
            margin-right: -150px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 13px;
            line-height: 1.6;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .info-tooltip .tooltip-text::after {
            content: "";
            position: absolute;
            top: 100%;
            right: 50%;
            margin-right: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #2c3e50 transparent transparent transparent;
        }

        .info-tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        .info-tooltip .tooltip-text ol {
            margin: 8px 0 0 0;
            padding-right: 20px;
        }

        .info-tooltip .tooltip-text ol li {
            margin-bottom: 6px;
        }
    </style>

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="12" y1="11" x2="12" y2="17"></line>
                <line x1="9" y1="14" x2="15" y2="14"></line>
            </svg>
            📝 إنشاء أذن تسليم جديدة (مبسطة)
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
            <span>إنشاء أذن</span>
        </nav>
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
                            <span style="font-size: 16px; font-weight: 500;">🔼 أذن صادرة (للإنتاج / للعملاء / لمستودع آخر)</span>
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
                                class="form-input {{ $errors->has('delivery_number') ? 'is-invalid' : '' }}" placeholder="سيتم التعبئة تلقائياً" value="{{ old('delivery_number', $autoGeneratedNumber ?? '') }}" readonly required>
                        </div>
                        <small style="color: #27ae60; display: block; margin-top: 5px;">✓ يتم إنشاء رقم الأذن تلقائياً</small>
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
                                class="form-input {{ $errors->has('delivery_date') ? 'is-invalid' : '' }}" value="{{ old('delivery_date', date('Y-m-d')) }}" required>
                        </div>
                        @if ($errors->has('delivery_date'))
                            <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('delivery_date') }}</small>
                        @endif
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
                        <h3 class="section-title">
                            بيانات الموردين
                            <span class="info-tooltip">
                                ?
                                <span class="tooltip-text">
                                    <strong>📌 نظام العمل (ثلاث مراحل):</strong>
                                    <ol>
                                        <li><strong>المرحلة 1 - إنشاء الأذن (هنا):</strong> بيانات أساسية فقط بدون أوزان</li>
                                        <li><strong>المرحلة 2 - التسجيل:</strong> تسجيل الوزن الفعلي من الميزان</li>
                                        <li><strong>المرحلة 3 - التسوية:</strong> ربط الفاتورة وحساب الفروقات</li>
                                    </ol>
                                </span>
                            </span>
                        </h3>
                        <p class="section-subtitle">معلومات المورد والتسليم</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="warehouse_id" class="form-label">
                            المستودع الوارد إليه
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
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->warehouse_name ?? $warehouse->name }} [{{ $warehouse->warehouse_code ?? '' }}]
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('warehouse_id'))
                            <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('warehouse_id') }}</small>
                        @endif
                    </div>

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
                            <select name="supplier_id" id="supplier_id" class="form-input {{ $errors->has('supplier_id') ? 'is-invalid' : '' }}" required>
                                <option value="">-- اختر المورد --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
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
                                class="form-input {{ $errors->has('driver_name') ? 'is-invalid' : '' }}" placeholder="اسم السائق" value="{{ old('driver_name') }}">
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
                                class="form-input {{ $errors->has('vehicle_number') ? 'is-invalid' : '' }}" placeholder="مثال: أ ب ت 1234" value="{{ old('vehicle_number') }}">
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
                                class="form-input {{ $errors->has('invoice_reference_number') ? 'is-invalid' : '' }}" placeholder="رقم الفاتورة من المورد (إن وجدت)" value="{{ old('invoice_reference_number') }}">
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
                        <h3 class="section-title">
                            بيانات الوجهة
                            <span class="info-tooltip">
                                ?
                                <span class="tooltip-text">
                                    <strong>📋 خطوات الإخراج:</strong>
                                    <ol>
                                        <li>اختر المستودع المصدر أولاً</li>
                                        <li>اختر المادة من المواد المتوفرة</li>
                                        <li>أدخل الكمية المراد إخراجها</li>
                                        <li>اختر الوجهة (الإنتاج / مستودع آخر)</li>
                                        <li>سيتم خصم الكمية تلقائياً</li>
                                    </ol>
                                </span>
                            </span>
                        </h3>
                        <p class="section-subtitle">إلى أين تذهب البضاعة؟</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="warehouse_from_id" class="form-label">
                            المستودع المصدر
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            </svg>
                            <select name="warehouse_from_id" id="warehouse_from_id" class="form-input {{ $errors->has('warehouse_from_id') ? 'is-invalid' : '' }}">
                                <option value="">-- اختر المستودع --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_from_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->warehouse_name }} [{{ $warehouse->warehouse_code ?? '' }}]
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('warehouse_from_id'))
                            <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('warehouse_from_id') }}</small>
                        @endif
                        <small style="color: #27ae60; display: block; margin-top: 5px;" id="warehouse_info_display"></small>
                    </div>

                    <div class="form-group" id="material_from_group" style="display: none;">
                        <label for="material_detail_id_outgoing" class="form-label">
                            المادة المراد إخراجها
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="feather icon-box" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #7f8c8d; font-size: 18px;"></i>
                            <select name="material_detail_id" id="material_detail_id_outgoing" class="form-input {{ $errors->has('material_detail_id') ? 'is-invalid' : '' }}">
                                <option value="">-- اختر المادة --</option>
                            </select>
                            <input type="hidden" name="material_id" id="material_id_hidden">
                        </div>
                        @if ($errors->has('material_detail_id'))
                            <small style="color: #e74c3c; display: block; margin-top: 5px;">
                                <i class="feather icon-alert-circle"></i> {{ $errors->first('material_detail_id') }}
                            </small>
                        @endif
                        <div style="margin-top: 10px; padding: 12px; background: #f8f9fa; border-radius: 6px; border-right: 3px solid #27ae60;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                <i class="feather icon-info" style="color: #3498db;"></i>
                                <strong style="color: #2c3e50;" id="selected_material_name">لم يتم اختيار المادة</strong>
                            </div>
                            <small style="color: #27ae60; display: block;" id="material_quantity_display">
                                <i class="feather icon-package"></i> اختر المادة لعرض الكمية المتوفرة
                            </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="delivery_quantity_outgoing" class="form-label">
                            الكمية المراد إخراجها
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            </svg>
                            <input type="number" name="delivery_quantity" id="delivery_quantity_outgoing"
                                class="form-input {{ $errors->has('delivery_quantity') ? 'is-invalid' : '' }}"
                                placeholder="أدخل الكمية"
                                value="{{ old('delivery_quantity') }}"
                                min="0.01"
                                step="0.01">
                        </div>
                        @if ($errors->has('delivery_quantity'))
                            <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('delivery_quantity') }}</small>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="destination_id" class="form-label">
                            الوجهة (مستودع / قسم الإنتاج)
                            <span class="required" id="destination_required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            </svg>
                            <select name="destination_id" id="destination_id" class="form-input {{ $errors->has('destination_id') ? 'is-invalid' : '' }}">
                                <option value="">-- اختر الوجهة --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('destination_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->warehouse_name }} [{{ $warehouse->warehouse_code ?? '' }}]
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('destination_id'))
                            <small style="color: #e74c3c; display: block; margin-top: 5px;">❌ {{ $errors->first('destination_id') }}</small>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="outgoing_notes" class="form-label">سبب الإخراج (اختياري)</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <input type="text" name="outgoing_notes" id="outgoing_notes"
                                class="form-input" placeholder="مثال: للإنتاج - طلبية رقم 123" value="{{ old('outgoing_notes') }}">
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
                        <label for="notes" class="form-label">الملاحظات (اختياري)</label>
                        <div class="input-wrapper">
                            <textarea name="notes" id="notes"
                                class="form-input {{ $errors->has('notes') ? 'is-invalid' : '' }}" rows="3" placeholder="أدخل أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
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
                    إنشاء الأذن
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
            const typeIncoming = document.getElementById('type_incoming');
            const typeOutgoing = document.getElementById('type_outgoing');
            const incomingSection = document.getElementById('incoming-section');
            const outgoingSection = document.getElementById('outgoing-section');
            const supplierId = document.getElementById('supplier_id');
            const destinationId = document.getElementById('destination_id');

            const warehouseId = document.getElementById('warehouse_id');

            function updateVisibility() {
                if (typeIncoming.checked) {
                    incomingSection.style.display = '';
                    outgoingSection.style.display = 'none';
                    supplierId.required = true;
                    warehouseId.required = true;
                    destinationId.required = false;
                    // Remove required from outgoing fields
                    if (quantityOutgoing) {
                        quantityOutgoing.required = false;
                    }
                    if (warehouseFromId) {
                        warehouseFromId.required = false;
                    }
                } else {
                    incomingSection.style.display = 'none';
                    outgoingSection.style.display = '';
                    supplierId.required = false;
                    warehouseId.required = false;
                    destinationId.required = true;
                    // Add required to outgoing fields
                    if (quantityOutgoing) {
                        quantityOutgoing.required = true;
                    }
                    if (warehouseFromId) {
                        warehouseFromId.required = true;
                    }
                }
            }

            typeIncoming.addEventListener('change', updateVisibility);
            typeOutgoing.addEventListener('change', updateVisibility);

            // بيانات المواد المتوفرة في كل مستودع
            const warehouseMaterials = {!! json_encode($materialDetails->where('quantity', '>', 0)->groupBy('warehouse_id')->map(function($details) {
                return $details->map(function($detail) {
                    // التأكد من تحميل اسم المادة من الأعمدة المتاحة
                    $materialName = 'غير محدد';
                    if ($detail->material) {
                        if (!empty($detail->material->name_ar)) {
                            $materialName = $detail->material->name_ar;
                        } elseif (!empty($detail->material->name_en)) {
                            $materialName = $detail->material->name_en;
                        } elseif (!empty($detail->material->name)) {
                            $materialName = $detail->material->name;
                        }
                    }

                    return [
                        'detail_id' => $detail->id,
                        'material_id' => $detail->material_id,
                        'material_name' => $materialName,
                        'quantity' => $detail->quantity ?? 0,
                        'unit_name' => $detail->unit?->unit_name ?? 'وحدة'
                    ];
                })->values();
            })) !!};

            // عناصر النموذج للإذن الصادر
            const warehouseFromId = document.getElementById('warehouse_from_id');
            const materialFromGroup = document.getElementById('material_from_group');
            const materialDetailIdOutgoing = document.getElementById('material_detail_id_outgoing');
            const materialIdHidden = document.getElementById('material_id_hidden');
            const quantityOutgoing = document.getElementById('delivery_quantity_outgoing');
            const warehouseInfoDisplay = document.getElementById('warehouse_info_display');
            const materialQuantityDisplay = document.getElementById('material_quantity_display');

            // عند اختيار المستودع للصادر
            if (warehouseFromId) {
                warehouseFromId.addEventListener('change', function() {
                    const warehouseId = this.value;

                    if (warehouseId && warehouseMaterials[warehouseId]) {
                        const materials = warehouseMaterials[warehouseId];

                        // عرض عدد المواد المتوفرة
                        warehouseInfoDisplay.textContent = `✅ يحتوي على ${materials.length} مادة متوفرة`;
                        warehouseInfoDisplay.style.color = '#27ae60';

                        // ملء قائمة المواد
                        materialDetailIdOutgoing.innerHTML = '<option value="">-- اختر المادة --</option>';
                        materials.forEach(m => {
                            const option = document.createElement('option');
                            option.value = m.detail_id;

                            // التأكد من وجود اسم المادة
                            const materialName = m.material_name || 'غير محدد';
                            const quantity = m.quantity || 0;
                            const unitName = m.unit_name || 'وحدة';

                            option.textContent = `${materialName} - متوفر: ${quantity} ${unitName}`;
                            option.setAttribute('data-material-id', m.material_id);
                            option.setAttribute('data-quantity', quantity);
                            option.setAttribute('data-unit', unitName);
                            option.setAttribute('data-material-name', materialName);
                            materialDetailIdOutgoing.appendChild(option);
                        });

                        materialFromGroup.style.display = '';
                        materialDetailIdOutgoing.required = true;
                    } else {
                        materialFromGroup.style.display = 'none';
                        materialDetailIdOutgoing.required = false;
                        warehouseInfoDisplay.textContent = '';
                        warehouseInfoDisplay.style.color = '#27ae60';
                    }
                });
            }

            // عند اختيار المادة
            if (materialDetailIdOutgoing) {
                materialDetailIdOutgoing.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const materialId = selectedOption.getAttribute('data-material-id');
                    const availableQty = selectedOption.getAttribute('data-quantity');
                    const unitName = selectedOption.getAttribute('data-unit');
                    const materialName = selectedOption.getAttribute('data-material-name');
                    const selectedMaterialName = document.getElementById('selected_material_name');

                    if (this.value) {
                        // حفظ material_id في حقل مخفي
                        materialIdHidden.value = materialId;

                        // عرض اسم المادة
                        if (selectedMaterialName) {
                            selectedMaterialName.innerHTML = `<i class="feather icon-check-circle" style="color: #27ae60;"></i> ${materialName}`;
                            selectedMaterialName.style.color = '#27ae60';
                        }

                        // عرض الكمية المتوفرة
                        materialQuantityDisplay.innerHTML = `<i class="feather icon-package"></i> متوفر: <strong>${availableQty} ${unitName}</strong>`;
                        materialQuantityDisplay.style.color = '#27ae60';

                        // تحديد الحد الأقصى للكمية
                        if (quantityOutgoing) {
                            quantityOutgoing.max = availableQty;
                            quantityOutgoing.setAttribute('data-max', availableQty);
                            quantityOutgoing.setAttribute('data-unit', unitName);
                            quantityOutgoing.setAttribute('data-material-name', materialName);
                        }
                    } else {
                        if (selectedMaterialName) {
                            selectedMaterialName.innerHTML = '<i class="feather icon-info"></i> لم يتم اختيار المادة';
                            selectedMaterialName.style.color = '#2c3e50';
                        }
                        materialQuantityDisplay.innerHTML = '<i class="feather icon-package"></i> اختر المادة لعرض الكمية المتوفرة';
                        materialQuantityDisplay.style.color = '#27ae60';
                    }
                });
            }

            // التحقق من الكمية عند الإدخال
            if (quantityOutgoing) {
                quantityOutgoing.addEventListener('input', function() {
                    const maxQty = parseFloat(this.getAttribute('data-max'));
                    const currentQty = parseFloat(this.value);
                    const unitName = this.getAttribute('data-unit') || 'وحدة';
                    const materialName = this.getAttribute('data-material-name') || 'المادة';

                    if (maxQty && currentQty > maxQty) {
                        materialQuantityDisplay.innerHTML = `<i class="feather icon-alert-circle"></i> الكمية المطلوبة (<strong>${currentQty}</strong>) أكبر من المتوفر (<strong>${maxQty} ${unitName}</strong>)`;
                        materialQuantityDisplay.style.color = '#e74c3c';
                        this.classList.add('is-invalid');
                    } else if (currentQty > 0) {
                        materialQuantityDisplay.innerHTML = `<i class="feather icon-check-circle"></i> سيتم خصم <strong>${currentQty} ${unitName}</strong> من ${materialName}`;
                        materialQuantityDisplay.style.color = '#27ae60';
                        this.classList.remove('is-invalid');
                    }
                });
            }

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
                let hasError = false;

                // التحقق للإذن الوارد
                if (typeIncoming.checked) {
                    // التحقق من اختيار المستودع
                    if (warehouseId && !warehouseId.value) {
                        e.preventDefault();
                        alert('❌ يجب اختيار المستودع الوارد إليه!');
                        warehouseId.focus();
                        hasError = true;
                        return false;
                    }

                    // التحقق من اختيار المورد
                    if (supplierId && !supplierId.value) {
                        e.preventDefault();
                        alert('❌ يجب اختيار المورد!');
                        supplierId.focus();
                        hasError = true;
                        return false;
                    }
                }

                // التحقق من الكمية للإذن الصادر
                if (typeOutgoing.checked) {
                    // التحقق من اختيار المستودع
                    if (warehouseFromId && !warehouseFromId.value) {
                        e.preventDefault();
                        alert('❌ يجب اختيار المستودع المصدر أولاً!');
                        warehouseFromId.focus();
                        hasError = true;
                        return false;
                    }

                    // التحقق من اختيار المادة
                    if (materialDetailIdOutgoing && !materialDetailIdOutgoing.value) {
                        e.preventDefault();
                        alert('❌ يجب اختيار المادة!');
                        materialDetailIdOutgoing.focus();
                        hasError = true;
                        return false;
                    }

                    // التحقق من الكمية فقط إذا كانت المادة محددة
                    if (materialDetailIdOutgoing && materialDetailIdOutgoing.value) {
                        if (quantityOutgoing) {
                            const maxQty = parseFloat(quantityOutgoing.getAttribute('data-max'));
                            const currentQty = parseFloat(quantityOutgoing.value);

                            if (!currentQty || currentQty <= 0) {
                                e.preventDefault();
                                alert('❌ يجب إدخال الكمية المراد إخراجها!');
                                quantityOutgoing.focus();
                                hasError = true;
                                return false;
                            }

                            if (maxQty && currentQty > maxQty) {
                                e.preventDefault();
                                alert('❌ الكمية المطلوبة أكبر من الكمية المتوفرة في المستودع!');
                                quantityOutgoing.focus();
                                hasError = true;
                                return false;
                            }
                        }
                    }
                }

                // إذا لم توجد أخطاء، اسمح بالإرسال
                if (hasError) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>

@endsection
