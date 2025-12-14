@extends('master')

@section('title', __('shifts-workers.add_new_team'))

@section('content')

    <!-- Header -->
    <div class="um-header-section">
        @if(session('success'))
        <div class="um-alert-custom um-alert-success" role="alert">
            <i class="feather icon-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="um-alert-custom um-alert-danger" role="alert">
            <i class="feather icon-alert-circle"></i>
            {{ session('error') }}
            <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                <i class="feather icon-x"></i>
            </button>
        </div>
        @endif

        <!-- عرض جميع أخطاء التحقق -->
        @if($errors->any())
        <div class="error-summary-box" role="alert">
            <div class="error-summary-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <strong>يوجد {{ $errors->count() }} خطأ في البيانات المدخلة:</strong>
            </div>
            <ul class="error-list">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="error-close-btn" onclick="this.parentElement.style.display='none'">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        @endif

        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            {{ __('shifts-workers.add_new_team') }}
        </h1>
        <nav class="um-breadcrumb-nav">
            <span><i class="feather icon-home"></i> {{ __('shifts-workers.dashboard') }}</span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('shifts-workers.worker_teams') }}</span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('shifts-workers.add_new_team') }}</span>
        </nav>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.worker-teams.store') }}" id="teamForm">
            @csrf

            <!-- Team Information -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">{{ __('shifts-workers.team_code_label') }}</h3>
                        <p class="section-subtitle">{{ __('shifts-workers.team_created_successfully') }}</p>
                    </div>
                </div>

                <div class="form-grid">
                    <!-- الكود يتولد تلقائيًا - مخفي -->
                    <input type="hidden" name="team_code" id="team_code" value="{{ old('team_code') }}">

                    <div class="form-group">
                        <label for="name" class="form-label">
                            <svg style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-left: 5px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                            اسم المجموعة
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="name" id="name"
                            class="form-input-simple @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="مثال: فريق الإنتاج أ" required autofocus>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="manager_id" class="form-label">
                            <svg style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-left: 5px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            المشرف المسؤول
                            <span class="required">*</span>
                        </label>
                        <select name="manager_id" id="manager_id"
                            class="form-input-simple @error('manager_id') is-invalid @enderror" required>
                            <option value="">-- اختر المشرف --</option>
                            @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('manager_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width" style="margin-top: 10px;">
                        <label for="description" class="form-label" style="font-size: 13px; color: #666;">
                            <svg style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-left: 5px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                            </svg>
                            ملاحظات (اختياري)
                        </label>
                        <textarea name="description" id="description" rows="2"
                            class="form-input-simple @error('description') is-invalid @enderror"
                            placeholder="أي ملاحظات إضافية عن المجموعة...">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Worker Selection -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon account">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="section-title">{{ __('shifts-workers.workers_label') }}</h3>
                        <p class="section-subtitle">اختر العمال من الجدول</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <div class="simple-info-box">
                            <svg style="width: 20px; height: 20px; color: #3b82f6;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            <div>
                                <strong>اختر العمال بسهولة:</strong> حدد من القائمة أدناه
                            </div>
                        </div>

                        <div class="workers-selection-header">
                            <div class="selected-count">
                                <strong id="selectedCount">0</strong> عامل محدد
                            </div>
                            <div class="selection-actions">
                                <button type="button" class="btn-select-action" onclick="toggleAllWorkers(true)">
                                    ✓ تحديد الكل
                                </button>
                                <button type="button" class="btn-select-action cancel" onclick="toggleAllWorkers(false)">
                                    ✗ إلغاء الكل
                                </button>
                            </div>
                        </div>

                        <div class="workers-simple-list">
                            @forelse($workers as $worker)
                            <label class="worker-simple-item">
                                <input type="checkbox" name="workers[]" value="{{ $worker->id }}"
                                    class="worker-checkbox" onchange="updateWorkerCount()"
                                    {{ in_array($worker->id, old('workers', [])) ? 'checked' : '' }}>
                                <div class="worker-info">
                                    <div class="worker-name">{{ $worker->name }}</div>
                                    <div class="worker-meta">{{ $worker->worker_code }} - {{ $worker->role->role_name ?? 'عامل' }}</div>
                                </div>
                            </label>
                            @empty
                            <div class="text-muted" style="text-align: center; padding: 30px; color: #999;">
                                لا يوجد عمال متاحين
                            </div>
                            @endforelse
                        </div>
                        @error('workers')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
                @if(auth()->user()->hasPermission('WORKER_TEAMS_CREATE'))
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    {{ __('shifts-workers.save') }}
                </button>
                @endif
                @if(auth()->user()->hasPermission('WORKER_TEAMS_READ'))
                <a href="{{ route('manufacturing.shifts-workers.index') }}" class="btn-cancel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    {{ __('shifts-workers.cancel') }}
                </a>
                @endif
            </div>
        </form>
    </div>

    <style>
        /* صندوق عرض الأخطاء */
        .error-summary-box {
            position: relative;
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 2px solid #ef4444;
            border-right: 6px solid #dc2626;
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-summary-header {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #991b1b;
            margin-bottom: 14px;
            font-size: 16px;
        }

        .error-summary-header svg {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
        }

        .error-list {
            margin: 0;
            padding: 0;
            padding-right: 30px;
            list-style: none;
        }

        .error-list li {
            position: relative;
            padding: 10px 0;
            padding-right: 24px;
            color: #7f1d1d;
            font-size: 14px;
            line-height: 1.6;
            border-bottom: 1px solid rgba(239, 68, 68, 0.2);
        }

        .error-list li:last-child {
            border-bottom: none;
        }

        .error-list li:before {
            content: "✕";
            position: absolute;
            right: 0;
            top: 10px;
            width: 18px;
            height: 18px;
            background: #dc2626;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }

        .error-close-btn {
            position: absolute;
            top: 16px;
            left: 16px;
            background: rgba(220, 38, 38, 0.1);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .error-close-btn:hover {
            background: #dc2626;
            transform: rotate(90deg);
        }

        .error-close-btn svg {
            width: 16px;
            height: 16px;
            stroke: #dc2626;
        }

        .error-close-btn:hover svg {
            stroke: white;
        }

        /* تمييز الحقول التي بها أخطاء */
        .form-input-simple.is-invalid,
        .form-input-simple:invalid {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .error-message {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #dc2626;
            font-size: 13px;
            margin-top: 6px;
            font-weight: 500;
            animation: shake 0.3s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-message:before {
            content: "⚠";
            font-size: 14px;
        }

        /* تبسيط تصميم الحقول */
        .form-input-simple {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s;
            font-family: 'Cairo', sans-serif;
        }

        .form-input-simple:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
        }

        .required {
            color: #ef4444;
            margin-right: 3px;
        }

        /* صندوق معلومات بسيط */
        .simple-info-box {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: #eff6ff;
            border-right: 4px solid #3b82f6;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #1e40af;
        }

        /* قائمة العمال المبسطة */
        .workers-simple-list {
            max-height: 400px;
            overflow-y: auto;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            margin-top: 10px;
        }

        .worker-simple-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: background 0.2s;
        }

        .worker-simple-item:hover {
            background: #f9fafb;
        }

        .worker-simple-item:last-child {
            border-bottom: none;
        }

        .worker-simple-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #3b82f6;
        }

        .worker-info {
            flex: 1;
        }

        .worker-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 3px;
            font-size: 14px;
        }

        .worker-meta {
            font-size: 12px;
            color: #6b7280;
        }

        .input-group-with-button {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        /* Worker Table Styles */
        .workers-table-wrapper {
            overflow-x: auto;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-top: 10px;
        }

        .workers-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .workers-table thead {
            background: #f5f5f5;
            border-bottom: 2px solid #e0e0e0;
        }

        .workers-table thead tr th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
        }

        .workers-table tbody tr {
            border-bottom: 1px solid #e0e0e0;
            transition: background-color 0.2s;
        }

        .workers-table tbody tr:hover {
            background-color: #f9f9f9;
        }

        .workers-table tbody tr td {
            padding: 12px 15px;
            color: #555;
        }

        .workers-table input[type="checkbox"] {
            cursor: pointer;
            width: 18px;
            height: 18px;
        }

        .workers-table label {
            cursor: pointer;
            color: #333;
        }

        .workers-selection-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .selection-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .selected-count {
            font-size: 13px;
            color: #64748b;
            padding: 6px 10px;
            background: #f1f5f9;
            border-radius: 4px;
        }

        .selected-count strong {
            color: #3b82f6;
            font-size: 15px;
            font-weight: 600;
        }

        .btn-select-action {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 14px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }

        .btn-select-action:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-select-action.cancel {
            background: #ef4444;
        }

        .btn-select-action.cancel:hover {
            background: #dc2626;
        }

        .btn-generate {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.25);
            min-height: 48px;
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.35);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .btn-generate:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.25);
        }

        .btn-generate:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-generate svg {
            width: 18px;
            height: 18px;
            animation: spin 0s linear infinite;
        }

        .btn-generate.loading svg {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .btn-generate.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .btn-generate.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .workers-selection-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .selection-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-select-all, .btn-deselect-all {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: white;
            color: #475569;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-select-all:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        .btn-deselect-all:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
        }

        .selected-count {
            font-size: 13px;
            color: #64748b;
        }

        .selected-count strong {
            color: #3b82f6;
            font-size: 15px;
        }

        .worker-item {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s;
        }

        .worker-item:hover {
            background: #f8fafc;
        }

        .worker-item:last-child {
            border-bottom: none;
        }

        .worker-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .worker-item label {
            cursor: pointer;
            flex: 1;
            margin: 0;
            font-size: 14px;
            color: #1e293b;
        }

        /* Reduce icon sizes */
        .section-icon svg {
            width: 20px;
            height: 20px;
        }

        .input-icon {
            width: 18px;
            height: 18px;
        }

        .title-icon {
            width: 24px;
            height: 24px;
        }

        @media (max-width: 768px) {
            .input-group-with-button {
                flex-direction: column;
            }

            .btn-generate {
                width: 100%;
                justify-content: center;
            }

            .section-icon svg {
                width: 18px;
                height: 18px;
            }

            .input-icon {
                width: 16px;
                height: 16px;
            }
        }
    </style>

    <script>
        // التمرير التلقائي لصندوق الأخطاء عند وجود أخطاء
        document.addEventListener('DOMContentLoaded', function() {
            const errorBox = document.querySelector('.error-summary-box');
            if (errorBox) {
                // التمرير للأخطاء
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // إضافة تأثير وميض للفت الانتباه
                let blinkCount = 0;
                const blinkInterval = setInterval(() => {
                    errorBox.style.opacity = errorBox.style.opacity === '0.7' ? '1' : '0.7';
                    blinkCount++;
                    if (blinkCount >= 4) {
                        clearInterval(blinkInterval);
                        errorBox.style.opacity = '1';
                    }
                }, 300);

                // تمييز الحقول التي بها أخطاء
                const invalidInputs = document.querySelectorAll('.is-invalid');
                invalidInputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.classList.add('fixing-error');
                        const errorMsg = this.parentElement.querySelector('.error-message');
                        if (errorMsg) {
                            errorMsg.style.fontWeight = 'bold';
                        }
                    });

                    input.addEventListener('blur', function() {
                        this.classList.remove('fixing-error');
                        const errorMsg = this.parentElement.querySelector('.error-message');
                        if (errorMsg) {
                            errorMsg.style.fontWeight = 'normal';
                        }
                    });
                });
            }

            // التمرير للحقل الأول الذي به خطأ عند الضغط على خطأ معين
            const errorList = document.querySelectorAll('.error-list li');
            errorList.forEach((errorItem, index) => {
                errorItem.style.cursor = 'pointer';
                errorItem.addEventListener('click', function() {
                    const invalidInput = document.querySelectorAll('.is-invalid')[index];
                    if (invalidInput) {
                        invalidInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => {
                            invalidInput.focus();
                        }, 500);
                    }
                });
            });
        });

        // Update worker count
        function updateWorkerCount() {
            const count = document.querySelectorAll('.worker-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
        }

        // Toggle all workers
        function toggleAllWorkers(checked) {
            const checkboxes = document.querySelectorAll('.worker-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = checked;
            });
            updateWorkerCount();
        }

        // Auto-generate team code on page load
        async function autoGenerateCode() {
            const codeInput = document.getElementById('team_code');
            if (codeInput.value) return; // Already has a value

            try {
                const response = await fetch('{{ route("manufacturing.worker-teams.generate-code") }}');
                const data = await response.json();
                if (data.team_code) {
                    codeInput.value = data.team_code;
                }
            } catch (error) {
                console.error('Error generating code:', error);
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            autoGenerateCode();
            updateWorkerCount();
        });
    </script>
@endsection
