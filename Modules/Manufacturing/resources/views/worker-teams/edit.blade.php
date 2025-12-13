@extends('master')

@section('title', __('shifts-workers.edit_team'))

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

        <h1 class="um-page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            {{ __('shifts-workers.edit_team') }}
        </h1>
        <nav class="um-breadcrumb-nav">
            <span><i class="feather icon-home"></i> {{ __('shifts-workers.dashboard') }}</span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('shifts-workers.worker_teams') }}</span>
            <i class="feather icon-chevron-left"></i>
            <span>{{ __('shifts-workers.edit_team') }}</span>
        </nav>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('manufacturing.worker-teams.update', $team->id) }}" id="teamForm">
            @csrf
            @method('PUT')

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
                        <p class="section-subtitle">{{ __('shifts-workers.team_updated_successfully') }}</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="team_code" class="form-label">
                            {{ __('shifts-workers.team_code_label') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-group-with-button">
                            <div class="input-wrapper" style="flex: 1;">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                </svg>
                                <input type="text" name="team_code" id="team_code"
                                    class="form-input @error('team_code') is-invalid @enderror"
                                    value="{{ old('team_code', $team->team_code) }}" placeholder="{{ __('worker-teams.team_code_label') }}" required readonly>
                            </div>
                        </div>
                        @error('team_code')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label">
                            {{ __('shifts-workers.team_name_label') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                            <input type="text" name="name" id="name"
                                class="form-input @error('name') is-invalid @enderror"
                                value="{{ old('name', $team->name) }}" placeholder="{{ __('worker-teams.team_name_label') }}" required>
                        </div>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="manager_id" class="form-label">
                            المسؤول عن المجموعة
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <select name="manager_id" id="manager_id"
                                class="form-input @error('manager_id') is-invalid @enderror" required>
                                <option value="">اختر المسؤول</option>
                                @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ old('manager_id', $team->manager_id) == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @error('manager_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label for="description" class="form-label">{{ __('shifts-workers.description_label') }}</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                            </svg>
                            <textarea name="description" id="description" rows="3"
                                class="form-input @error('description') is-invalid @enderror"
                                placeholder="{{ __('shifts-workers.description_label') }}">{{ old('description', $team->description) }}</textarea>
                        </div>
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
                        <div class="workers-selection-header">
                            <label class="form-label">{{ __('shifts-workers.workers_label') }}</label>
                            <div class="selection-actions">
                                <span class="selected-count">المختارين: <strong id="selectedCount">{{ count(old('workers', $team->worker_ids ?? [])) }}</strong></span>
                                <button type="button" class="btn-select-action" onclick="toggleAllWorkers(true)">اختر الكل</button>
                                <button type="button" class="btn-select-action cancel" onclick="toggleAllWorkers(false)">إلغاء الاختيار</button>
                            </div>
                        </div>

                        <div class="workers-table-wrapper">
                            @if($workers->count() > 0)
                            <table class="workers-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px; text-align: center;">
                                            <input type="checkbox" id="selectAllWorkers" onchange="toggleAllWorkers(this.checked)">
                                        </th>
                                        <th>اسم العامل</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الهاتف</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($workers as $worker)
                                    <tr>
                                        <td style="text-align: center;">
                                            <input type="checkbox"
                                                   id="worker_{{ $worker->id }}"
                                                   name="workers[]"
                                                   value="{{ $worker->id }}"
                                                   class="worker-checkbox"
                                                   {{ in_array($worker->id, old('workers', $team->worker_ids ?? [])) ? 'checked' : '' }}
                                                   onchange="updateWorkerCount()">
                                        </td>
                                        <td>
                                            <label for="worker_{{ $worker->id }}" style="margin: 0; cursor: pointer; font-weight: 500;">
                                                {{ $worker->name }}
                                            </label>
                                        </td>
                                        <td>{{ $worker->email ?? '-' }}</td>
                                        <td>{{ $worker->phone ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <p style="color: #999; text-align: center; padding: 20px;">لا توجد عمال متاحة</p>
                            @endif
                        </div>
                        @error('workers')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
                @if(auth()->user()->hasPermission('WORKER_TEAMS_UPDATE'))
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    {{ __('worker-teams.update') }}
                </button>
                @endif
                @if(auth()->user()->hasPermission('WORKER_TEAMS_READ'))
                <a href="{{ route('manufacturing.worker-teams.show', $team->id) }}" class="btn-cancel">
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
        .input-group-with-button {
            display: flex;
            gap: 10px;
            align-items: flex-start;
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
                transform: rotate(0deg);waste
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
    </style>

    <script>
        // Update worker count
        function updateWorkerCount() {
            const count = document.querySelectorAll('.worker-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
            updateSelectAllState();
        }

        // Toggle all workers
        function toggleAllWorkers(checked) {
            const checkboxes = document.querySelectorAll('.worker-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = checked;
            });
            updateWorkerCount();
        }

        // Update select all checkbox state
        function updateSelectAllState() {
            const selectAllCheckbox = document.getElementById('selectAllWorkers');
            const checkboxes = document.querySelectorAll('.worker-checkbox');
            const checkedCount = document.querySelectorAll('.worker-checkbox:checked').length;

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = checkedCount === checkboxes.length && checkboxes.length > 0;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateWorkerCount();
        });
    </script>
@endsection
