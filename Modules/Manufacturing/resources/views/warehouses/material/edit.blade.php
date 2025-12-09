@extends('master')

@section('title', __('warehouse.edit_material'))

@section('content')

    <style>
        .form-section {
            background: white;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .section-header {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f0f0f0;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 15px;
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .required {
            color: #e74c3c;
            font-weight: bold;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            color: #2c3e50;
            transition: all 0.3s;
            direction: rtl;
            text-align: right;
        }

        .form-input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233498db' stroke-width='2'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: left 12px center;
            background-size: 18px;
            padding-left: 40px;
        }

        select.form-input option {
            background-color: white;
            color: #2c3e50;
            padding: 8px;
            direction: rtl;
        }

        .btn-submit {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .btn-cancel {
            padding: 12px 24px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            color: #7f8c8d;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel:hover {
            border-color: #3498db;
            color: #3498db;
        }

        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: slideDown 0.3s ease-out;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .um-header-section {
            margin-bottom: 30px;
        }

        .um-page-title {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 12px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .title-icon {
            width: 32px;
            height: 32px;
            color: #3498db;
        }

        .um-breadcrumb-nav {
            font-size: 14px;
            color: #7f8c8d;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
        }

        .input-error {
            border-color: #dc3545 !important;
            background-color: #fff5f5 !important;
        }
    </style>

    <!-- Header -->
    <div class="um-header-section">
        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            {{ __('warehouse.edit_material') }}
        </h1>
        <nav class="um-breadcrumb-nav">
            <span>{{ __('warehouse.breadcrumb_dashboard') }}</span>
            <span>›</span>
            <span>{{ __('warehouse.materials') }}</span>
        </nav>
    </div>

    <!-- Messages -->
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
            <strong>{{ __('warehouse.error_in_data') }}:</strong>
            <ul style="margin: 8px 0 0 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div style="background: white; border-radius: 12px; padding: 24px; border: 1px solid #e0e0e0;">
        <form method="POST" action="{{ route('manufacturing.warehouse-products.update', $material->id) }}" id="materialForm">
            @csrf
            @method('PUT')

            <!-- Material Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <h3 class="section-title">💡 {{ __('warehouse.material_information') }} ({{ __('warehouse.only_fields') }})</h3>
                </div>

                <div class="form-grid">
                    <!-- Field 1: Material Name (Arabic) -->
                    <div class="form-group full-width">
                        <label for="name_ar" class="form-label">
                            {{ __('warehouse.material_name') }} <span class="required">*</span>
                        </label>
                        @php
                            $locale = app()->getLocale();
                            $nameAr = \App\Models\Translation::getTranslation('App\Models\Material', $material->id, 'name', 'ar') ?? $material->name_ar ?? '';
                        @endphp
                        <input type="text" name="name_ar" id="name_ar"
                               class="form-input @error('name_ar') input-error @enderror"
                               placeholder="{{ __('warehouse.search_materials') }}"
                               value="{{ old('name_ar', $nameAr) }}"
                               required>
                        @error('name_ar')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Field 1.1: Material Name (English) -->
                    <div class="form-group full-width">
                        <label for="name_en" class="form-label">
                            {{ __('warehouse.material_name_en') }}
                        </label>
                        @php
                            $nameEn = \App\Models\Translation::getTranslation('App\Models\Material', $material->id, 'name', 'en') ?? $material->name_en ?? '';
                        @endphp
                        <input type="text" name="name_en" id="name_en"
                               class="form-input @error('name_en') input-error @enderror"
                               placeholder="{{ __('warehouse.material_name_en') }}"
                               value="{{ old('name_en', $nameEn) }}">
                        @error('name_en')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Field 1.2: Material Name (Hindi) -->
                    <div class="form-group full-width">
                        <label for="name_hi" class="form-label">
                            Material Name (Hindi)
                        </label>
                        @php
                            $nameHi = \App\Models\Translation::getTranslation('App\Models\Material', $material->id, 'name', 'hi') ?? '';
                        @endphp
                        <input type="text" name="name_hi" id="name_hi"
                               class="form-input @error('name_hi') input-error @enderror"
                               placeholder="Material Name (Hindi)"
                               value="{{ old('name_hi', $nameHi) }}">
                        @error('name_hi')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Field 1.3: Material Name (Urdu) -->
                    <div class="form-group full-width">
                        <label for="name_ur" class="form-label">
                            Material Name (Urdu)
                        </label>
                        @php
                            $nameUr = \App\Models\Translation::getTranslation('App\Models\Material', $material->id, 'name', 'ur') ?? '';
                        @endphp
                        <input type="text" name="name_ur" id="name_ur"
                               class="form-input @error('name_ur') input-error @enderror"
                               placeholder="Material Name (Urdu)"
                               value="{{ old('name_ur', $nameUr) }}"
                               style="direction: rtl;">
                        @error('name_ur')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Field 2: Material Type -->
                    <div class="form-group">
                        <label for="material_type_id" class="form-label">
                            {{ __('warehouse.material_type') }} <span class="required">*</span>
                        </label>
                        <select name="material_type_id" id="material_type_id"
                                class="form-input @error('material_type_id') input-error @enderror"
                                required>
                            <option value="">{{ __('warehouse.select_material_type') }}</option>
                            @foreach ($materialTypes as $type)
                                <option value="{{ $type->id }}" {{ old('material_type_id', $material->material_type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->type_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('material_type_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Field 3: Barcode (Read-only) -->
                    <div class="form-group">
                        <label for="barcode" class="form-label">{{ __('warehouse.cannot_be_edited') }}</label>
                        <input type="text" name="barcode" id="barcode"
                               class="form-input"
                               placeholder="{{ __('warehouse.current_code') }}"
                               value="{{ $material->barcode }}"
                               readonly
                               style="background-color: #f5f5f5; cursor: not-allowed;">
                        <small style="color: #666; margin-top: 5px; display: block;">{{ __('warehouse.unique_code') }}</small>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    ✓ {{ __('warehouse.save_changes') }}
                </button>
                <a href="{{ route('manufacturing.warehouse-products.index') }}" class="btn-cancel">
                    ✕ {{ __('warehouse.cancel') }}
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('materialForm');
            const inputs = form.querySelectorAll('[required]');

            form.addEventListener('submit', function(e) {
                let isValid = true;

                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.classList.add('input-error');
                        isValid = false;
                    } else {
                        input.classList.remove('input-error');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('❌ {{ __('warehouse.please_fill_all_required_fields') }}');
                    return false;
                }
            });

            // إزالة التنبيهات بعد 5 ثواني
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.3s';
                    setTimeout(() => alert.style.display = 'none', 300);
                }, 5000);
            });
        });
    </script>

@endsection
