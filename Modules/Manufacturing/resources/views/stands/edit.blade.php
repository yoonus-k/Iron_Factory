@extends('master')

@section('title', 'تعديل الاستاند')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-edit-2"></i>
                تعديل الاستاند
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <a href="{{ route('manufacturing.stands.index') }}">الاستاندات</a>
                <i class="feather icon-chevron-left"></i>
                <span>تعديل {{ $stand->stand_number }}</span>
            </nav>
        </div>

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="um-alert-custom um-alert-danger" role="alert">
                <i class="feather icon-alert-circle"></i>
                <strong>يوجد أخطاء في البيانات المدخلة:</strong>
                <ul style="margin: 10px 0 0 20px; padding: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        <!-- Main Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-edit-3"></i>
                    تعديل بيانات الاستاند
                </h4>
                <a href="{{ route('manufacturing.stands.show', $stand->id) }}" class="um-btn um-btn-outline">
                    <i class="feather icon-arrow-right"></i>
                    رجوع
                </a>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('manufacturing.stands.update', $stand->id) }}">
                @csrf
                @method('PUT')

                <div class="um-form-section">
                    <div class="um-row">
                        <!-- رقم الاستاند -->
                        <div class="um-col-md-6">
                            <div class="um-form-group">
                                <label for="stand_number">
                                    <i class="feather icon-hash"></i>
                                    رقم الاستاند
                                    <span class="um-required">*</span>
                                </label>
                                <input type="text" 
                                       name="stand_number" 
                                       id="stand_number" 
                                       class="um-form-control @error('stand_number') is-invalid @enderror" 
                                       value="{{ old('stand_number', $stand->stand_number) }}"
                                       readonly
                                       style="background-color: #f5f5f5;">
                                @error('stand_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="um-help-text">رقم الاستاند لا يمكن تعديله</small>
                            </div>
                        </div>

                        <!-- الوزن -->
                        <div class="um-col-md-6">
                            <div class="um-form-group">
                                <label for="weight">
                                    <i class="feather icon-activity"></i>
                                    الوزن (كجم)
                                    <span class="um-required">*</span>
                                </label>
                                <input type="number" 
                                       name="weight" 
                                       id="weight" 
                                       class="um-form-control @error('weight') is-invalid @enderror" 
                                       value="{{ old('weight', $stand->weight) }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="أدخل وزن الاستاند بالكيلوجرام"
                                       required>
                                @error('weight')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="um-row">
                        <!-- المرحلة الحالية -->
                        <div class="um-col-md-6">
                            <div class="um-form-group">
                                <label for="status">
                                    <i class="feather icon-flag"></i>
                                    المرحلة الحالية
                                    <span class="um-required">*</span>
                                </label>
                                <select name="status" 
                                        id="status" 
                                        class="um-form-control @error('status') is-invalid @enderror"
                                        required>
                                    <option value="unused" {{ old('status', $stand->status) == 'unused' ? 'selected' : '' }}>
                                        غير مستخدم
                                    </option>
                                    <option value="stage1" {{ old('status', $stand->status) == 'stage1' ? 'selected' : '' }}>
                                        المرحلة الأولى
                                    </option>
                                    <option value="stage2" {{ old('status', $stand->status) == 'stage2' ? 'selected' : '' }}>
                                        المرحلة الثانية
                                    </option>
                                    <option value="stage3" {{ old('status', $stand->status) == 'stage3' ? 'selected' : '' }}>
                                        المرحلة الثالثة
                                    </option>
                                    <option value="stage4" {{ old('status', $stand->status) == 'stage4' ? 'selected' : '' }}>
                                        المرحلة الرابعة
                                    </option>
                                    <option value="completed" {{ old('status', $stand->status) == 'completed' ? 'selected' : '' }}>
                                        مكتمل
                                    </option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- الحالة -->
                        <div class="um-col-md-6">
                            <div class="um-form-group">
                                <label for="is_active">
                                    <i class="feather icon-toggle-right"></i>
                                    الحالة
                                </label>
                                <div class="um-status-toggle">
                                    <label class="um-switch">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               id="is_active" 
                                               value="1"
                                               {{ old('is_active', $stand->is_active) ? 'checked' : '' }}>
                                        <span class="um-slider"></span>
                                    </label>
                                    <span id="statusLabel" class="um-status-label">
                                        {{ old('is_active', $stand->is_active) ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- الملاحظات -->
                    <div class="um-row">
                        <div class="um-col-md-12">
                            <div class="um-form-group">
                                <label for="notes">
                                    <i class="feather icon-file-text"></i>
                                    ملاحظات
                                </label>
                                <textarea name="notes" 
                                          id="notes" 
                                          class="um-form-control @error('notes') is-invalid @enderror" 
                                          rows="4"
                                          maxlength="1000"
                                          placeholder="أضف أي ملاحظات إضافية حول الاستاند...">{{ old('notes', $stand->notes) }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="um-help-text">الحد الأقصى 1000 حرف</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="um-form-actions">
                    <button type="submit" class="um-btn um-btn-primary">
                        <i class="feather icon-save"></i>
                        حفظ التعديلات
                    </button>
                    <a href="{{ route('manufacturing.stands.show', $stand->id) }}" class="um-btn um-btn-outline">
                        <i class="feather icon-x"></i>
                        إلغاء
                    </a>
                </div>
            </form>
        </section>
    </div>

    <style>
        .um-status-toggle {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 10px;
        }

        .um-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .um-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .um-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .um-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .um-slider {
            background-color: #4CAF50;
        }

        input:checked + .um-slider:before {
            transform: translateX(26px);
        }

        .um-status-label {
            font-weight: 500;
            font-size: 16px;
            color: #333;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isActiveCheckbox = document.getElementById('is_active');
            const statusLabel = document.getElementById('statusLabel');

            // تحديث التسمية عند تغيير الحالة
            isActiveCheckbox.addEventListener('change', function() {
                statusLabel.textContent = this.checked ? 'نشط' : 'غير نشط';
            });
        });
    </script>

@endsection
