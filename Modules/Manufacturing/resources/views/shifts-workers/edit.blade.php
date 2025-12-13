@extends('master')

@section('title', __('shifts-workers.edit_shift'))

@section('content')

        <!-- Header -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                {{ __('shifts-workers.edit_shift') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('shifts-workers.shifts_and_workers') }}</span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('shifts-workers.edit_shift') }}</span>
            </nav>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <form method="POST" action="{{ route('manufacturing.shifts-workers.update', $shift->id) }}" id="shiftForm">
                @csrf
                @method('PUT')

                <!-- Shift Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon personal">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('shifts-workers.shift_information') }}</h3>
                            <p class="section-subtitle">{{ __('shifts-workers.update_data') }}</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="shift_code" class="form-label">
                                {{ __('shifts-workers.shift_code') }}
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <input type="text" name="shift_code" id="shift_code"
                                    class="form-input"
                                    value="{{ old('shift_code', $shift->shift_code) }}" placeholder="{{ __('shifts-workers.shift_code_placeholder') }}" required readonly>
                            </div>
                            @error('shift_code')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="shift_date" class="form-label">
                                {{ __('shifts-workers.shift_date') }}
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <input type="date" name="shift_date" id="shift_date"
                                    class="form-input"
                                    value="{{ old('shift_date', $shift->shift_date->format('Y-m-d')) }}" required {{ $shift->status != 'scheduled' ? 'readonly' : '' }}>
                            </div>
                            @error('shift_date')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="shift_type" class="form-label">
                                {{ __('shifts-workers.shift_type') }}
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <select name="shift_type" id="shift_type"
                                    class="form-input" required {{ $shift->status != 'scheduled' ? 'disabled' : '' }} onchange="updateShiftTimes()">
                                    <option value="">{{ __('shifts-workers.select_shift_type') }}</option>
                                    <option value="morning" {{ old('shift_type', $shift->shift_type) == 'morning' ? 'selected' : '' }}>{{ __('shifts-workers.morning_shift') }}</option>
                                    <option value="evening" {{ old('shift_type', $shift->shift_type) == 'evening' ? 'selected' : '' }}>{{ __('shifts-workers.evening_shift') }}</option>
                                </select>
                            </div>
                            @error('shift_type')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <input type="hidden" id="team_id" name="team_id" value="{{ $shift->team_id ?? '' }}">

                        <div class="form-group">
                            <label for="supervisor_id" class="form-label">
                                {{ __('shifts-workers.shift_supervisor') }}
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <select name="supervisor_id" id="supervisor_id"
                                    class="form-input" required>
                                    <option value="">{{ __('shifts-workers.select_supervisor') }}</option>
                                    @foreach($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $shift->supervisor_id) == $supervisor->id ? 'selected' : '' }}>
                                            {{ $supervisor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('supervisor_id')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="start_time" class="form-label">
                                {{ __('shifts-workers.start_time') }}
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <input type="time" name="start_time" id="start_time"
                                    class="form-input"
                                    value="{{ old('start_time', $shift->start_time) }}" required readonly>
                            </div>
                            @error('start_time')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="end_time" class="form-label">
                                {{ __('shifts-workers.end_time') }}
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <input type="time" name="end_time" id="end_time"
                                    class="form-input"
                                    value="{{ old('end_time', $shift->end_time) }}" required readonly>
                            </div>
                            @error('end_time')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- المرحلة -->
                        <div class="form-group">
                            <label for="stage_number" class="form-label">
                                <i class="feather icon-layers"></i>
                                {{ __('shifts-workers.stage_number') }} (اختياري)
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                </svg>
                                <select name="stage_number" id="stage_number" class="form-input" onchange="loadStageRecords()">
                                    <option value="0">-- لم تحدد المرحلة بعد --</option>
                                    <option value="1" {{ old('stage_number', $shift->stage_number) == 1 ? 'selected' : '' }}>المرحلة 1 - الأستندات</option>
                                    <option value="2" {{ old('stage_number', $shift->stage_number) == 2 ? 'selected' : '' }}>المرحلة 2 - المعالجة</option>
                                    <option value="3" {{ old('stage_number', $shift->stage_number) == 3 ? 'selected' : '' }}>المرحلة 3 - الملفات</option>
                                    <option value="4" {{ old('stage_number', $shift->stage_number) == 4 ? 'selected' : '' }}>المرحلة 4 - الصناديق</option>
                                </select>
                            </div>
                        </div>

                        <!-- باركود المرحلة -->
                        <div class="form-group">
                            <label for="stage_record_barcode" class="form-label">
                                <i class="feather icon-barcode-2"></i>
                                باركود المرحلة (اختياري)
                            </label>
                            <div class="input-wrapper position-relative">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <line x1="8" y1="4" x2="8" y2="20"></line>
                                    <line x1="16" y1="4" x2="16" y2="20"></line>
                                    <line x1="4" y1="8" x2="20" y2="8"></line>
                                    <line x1="4" y1="16" x2="20" y2="16"></line>
                                </svg>
                                <input type="text" name="stage_record_barcode" id="stage_record_barcode"
                                    class="form-input" placeholder="-- اختر المرحلة أولاً ثم ادخل أو اختر الباركود --"
                                    value="{{ $shift->stage_record_barcode ?? '' }}" autocomplete="off" list="barcodeList" oninput="filterBarcodes()">
                                <datalist id="barcodeList"></datalist>
                                <div id="barcodeDropdown" class="barcode-dropdown" style="display: none;"></div>
                            </div>
                            <small class="text-muted" id="stage_records_count"></small>
                        </div>

                        <input type="hidden" name="stage_record_id" id="stage_record_id" value="{{ $shift->stage_record_id ?? '' }}">

                        <div class="form-group full-width">
                            <label for="notes" class="form-label">{{ __('shifts-workers.notes') }}</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <line x1="8" y1="6" x2="21" y2="6"></line>
                                    <line x1="8" y1="12" x2="21" y2="12"></line>
                                    <line x1="8" y1="18" x2="21" y2="18"></line>
                                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                </svg>
                                <textarea name="notes" id="notes" rows="4"
                                    class="form-input" placeholder="{{ __('shifts-workers.enter_shift_notes') }}">{{ old('notes', $shift->notes) }}</textarea>
                            </div>
                            @error('notes')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Workers Assignment Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon account">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('shifts-workers.assign_workers') }}</h3>
                            <p class="section-subtitle">{{ __('shifts-workers.select_workers_info') }}</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <!-- اختيار المجموعة -->
                        <div class="form-group full-width">
                            <label class="form-label">
                                <i class="feather icon-users" style="margin-right: 5px;"></i>
                                مجموعات العمال المتاحة
                            </label>
                            <div class="teams-grid">
                                @forelse($teams as $team)
                                    <div class="team-card" onclick="selectTeam({{ $team['id'] }}, {{ json_encode($team['worker_ids']) }}, {{ $team['manager_id'] }}, '{{ $team['manager_name'] }}')">
                                        <div class="team-card-header">
                                            <div class="team-icon">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="9" cy="7" r="4"></circle>
                                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                                </svg>
                                            </div>
                                            <div class="team-title">{{ $team['name'] }}</div>
                                        </div>
                                        <div class="team-card-body">
                                            <div class="team-info-row">
                                                <span class="info-label">الكود:</span>
                                                <span class="info-value">{{ $team['code'] }}</span>
                                            </div>
                                            <div class="team-info-row">
                                                <span class="info-label">المسؤول:</span>
                                                <span class="info-value">{{ $team['manager_name'] }}</span>
                                            </div>
                                            <div class="team-info-row">
                                                <span class="info-label">عدد العمال:</span>
                                                <span class="info-value badge">{{ $team['workers_count'] }}</span>
                                            </div>
                                        </div>
                                        <div class="team-card-footer">
                                            <button type="button" class="btn-select-team">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                اختيار هذه المجموعة
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <p style="color: #999; padding: 20px;">لا توجد مجموعات عمال متاحة</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="form-group full-width" style="margin: 30px 0; text-align: center;">
                            <div class="divider-with-text">أو</div>
                        </div>

                        <div class="form-group full-width">
                            <div class="workers-selection-header">
                                <label class="form-label">{{ __('shifts-workers.available_workers') }}</label>
                                <div class="selection-info">
                                    <span class="selected-count">{{ __('shifts-workers.selected') }}: <strong id="selectedWorkersCount">0</strong></span>
                                    <button type="button" class="btn-clear-selection" onclick="clearAllWorkers()">
                                        <i class="feather icon-x"></i> {{ __('shifts-workers.clear_all') }}
                                    </button>
                                </div>
                            </div>
                            <div class="workers-table-wrapper">
                                @if($workers->count() > 0)
                                <table class="workers-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px; text-align: center;">
                                                <input type="checkbox" id="selectAllWorkers" onchange="toggleAllWorkers(this)">
                                            </th>
                                            <th>{{ __('shifts-workers.worker_name') }}</th>
                                            <th>{{ __('shifts-workers.worker_id') }}</th>
                                            <th>الحالة</th>
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
                                                       {{ in_array($worker->id, old('workers', $shift->worker_ids ?? [])) ? 'checked' : '' }}
                                                       onchange="updateWorkersCount()">
                                            </td>
                                            <td>
                                                <label for="worker_{{ $worker->id }}" style="margin: 0; cursor: pointer;">
                                                    {{ $worker->name }}
                                                </label>
                                            </td>
                                            <td>{{ $worker->id }}</td>
                                            <td>
                                                <span class="worker-status {{ $worker->is_active ? 'active' : 'inactive' }}">
                                                    {{ $worker->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @else
                                <p style="color: #999; text-align: center; padding: 20px;">{{ __('shifts-workers.no_workers_available') }}</p>
                                @endif
                            </div>
                            @error('workers')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon account">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('shifts-workers.additional_information') }}</h3>
                            <p class="section-subtitle">{{ __('shifts-workers.additional_info_description') }}</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="status" class="form-label">
                                {{ __('shifts-workers.shift_status') }}
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                </svg>
                                <input type="text"
                                       class="form-input"
                                       value="{{ $shift->status_name }}" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="created_at" class="form-label">
                                {{ __('shifts-workers.creation_date') }}
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2">
                                    </rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <input type="text"
                                    class="form-input"
                                    value="{{ $shift->created_at->format('Y-m-d H:i') }}" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="updated_at" class="form-label">
                                {{ __('shifts-workers.update_date') }}
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2">
                                    </rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <input type="text"
                                    class="form-input"
                                    value="{{ $shift->updated_at->format('Y-m-d H:i') }}" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            &nbsp;
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        {{ __('shifts-workers.save_changes') }}
                    </button>
                    <a href="{{ route('manufacturing.shifts-workers.show', $shift->id) }}" class="btn-cancel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        {{ __('cancel') }}
                    </a>
                </div>
            </form>
        </div>

    <script>
        // تحديث أوقات الوردية حسب النوع
        function updateShiftTimes() {
            const shiftType = document.getElementById('shift_type').value;
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');

            if (shiftType === 'morning') {
                startTimeInput.value = '06:00';
                endTimeInput.value = '18:00';
            } else if (shiftType === 'evening') {
                startTimeInput.value = '18:00';
                endTimeInput.value = '06:00';
            }
        }

        // Load team workers when a team is selected
        async function loadTeamWorkers() {
            const teamSelect = document.getElementById('team_id');
            const teamId = teamSelect.value;

            if (!teamId) {
                return;
            }

            try {
                const url = `/manufacturing/worker-teams/${teamId}/workers`;
                console.log('Fetching from:', url);

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Response data:', data);

                if (data.success && data.worker_ids && data.worker_ids.length > 0) {
                    // First, uncheck all workers
                    document.querySelectorAll('.worker-checkbox').forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Then check workers from the selected team
                    data.worker_ids.forEach(workerId => {
                        const checkbox = document.getElementById(`worker_${workerId}`);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });

                    updateWorkersCount();

                    // Show success message
                    const workersCount = data.worker_ids.length;
                    const teamName = teamSelect.options[teamSelect.selectedIndex].text;
                    alert(`{{ __('shifts-workers.team_workers_selected') }}`.replace('{count}', workersCount).replace('{team}', teamName));
                } else {
                    alert('{{ __('shifts-workers.no_workers_in_team') }}');
                }
            } catch (error) {
                console.error('Error loading team workers:', error);
                alert('{{ __('shifts-workers.error_loading_team_workers') }}'.replace('{error}', error.message));
            }
        }

        // Select a team from the card and load its workers
        function selectTeam(teamId, workerIds, managerId, managerName) {
            // Store team ID in hidden field
            document.getElementById('team_id').value = teamId;

            // تعيين المسؤول تلقائياً
            if (managerId) {
                document.getElementById('supervisor_id').value = managerId;
            }

            // First, clear all selected workers
            document.querySelectorAll('.worker-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Remove selected class from all team cards
            document.querySelectorAll('.team-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selected class to clicked card
            if (event && event.currentTarget) {
                event.currentTarget.classList.add('selected');
            }

            // Select workers from the team
            if (workerIds && workerIds.length > 0) {
                workerIds.forEach(workerId => {
                    const checkbox = document.getElementById(`worker_${workerId}`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }

            updateWorkersCount();

            // Scroll to workers table
            setTimeout(() => {
                const workersTable = document.querySelector('.workers-table-wrapper');
                if (workersTable) {
                    workersTable.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 300);
        }

        // Clear all selected workers
        function clearAllWorkers() {
            document.querySelectorAll('.worker-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('team_id').value = '';
            // Remove selected class from all team cards
            document.querySelectorAll('.team-card').forEach(card => {
                card.classList.remove('selected');
            });
            updateWorkersCount();
        }

        // Update the count of selected workers
        function updateWorkersCount() {
            const count = document.querySelectorAll('.worker-checkbox:checked').length;
            document.getElementById('selectedWorkersCount').textContent = count;
            updateSelectAllState();
        }

        // Toggle all workers
        function toggleAllWorkers(selectAllCheckbox) {
            const checkboxes = document.querySelectorAll('.worker-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateWorkersCount();
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

        // تحديث الأوقات عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            updateShiftTimes();
            updateWorkersCount();
        });
    </script>

    <style>
        /* Teams Grid Styles */
        .teams-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .team-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 0;
            transition: all 0.3s ease;
            cursor: pointer;
            overflow: hidden;
        }

        .team-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
            transform: translateY(-2px);
        }

        .team-card.selected {
            border-color: #10b981;
            background: #f0fdf4;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }

        .team-card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .team-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .team-icon svg {
            width: 24px;
            height: 24px;
        }

        .team-title {
            font-weight: 600;
            font-size: 16px;
            flex: 1;
        }

        .team-card-body {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .team-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .team-info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 600;
        }

        .info-value.badge {
            background: #e0f2fe;
            color: #0369a1;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
        }

        .team-card-footer {
            padding: 12px 15px;
            background: #f9f9f9;
        }

        .btn-select-team {
            width: 100%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-select-team:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
        }

        .btn-select-team svg {
            width: 18px;
            height: 18px;
        }

        .divider-with-text {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 14px;
            color: #999;
            font-weight: 600;
        }

        .divider-with-text::before,
        .divider-with-text::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0e0e0;
        }

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
            font-weight: 500;
            color: #333;
        }

        .worker-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .worker-status.active {
            background-color: #d4edda;
            color: #155724;
        }

        .worker-status.inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .workers-selection-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .selection-info {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .selected-count {
            font-size: 13px;
            color: #64748b;
        }

        .selected-count strong {
            color: #3b82f6;
            font-size: 15px;
            font-weight: 600;
        }

        .btn-clear-selection {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fca5a5;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-clear-selection:hover {
            background: #fee2e2;
            border-color: #f87171;
        }
    </style>

    <script>
        // تحديث أوقات الوردية حسب النوع
        function updateShiftTimes() {
            const shiftType = document.getElementById('shift_type').value;
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');

            if (shiftType === 'morning') {
                startTimeInput.value = '06:00';
                endTimeInput.value = '18:00';
            } else if (shiftType === 'evening') {
                startTimeInput.value = '18:00';
                endTimeInput.value = '06:00';
            }
        }

        // Clear all selected workers
        function clearAllWorkers() {
            document.querySelectorAll('.worker-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('team_id').value = '';
            updateWorkersCount();
        }

        // Update the count of selected workers
        function updateWorkersCount() {
            const count = document.querySelectorAll('.worker-checkbox:checked').length;
            document.getElementById('selectedWorkersCount').textContent = count;
            updateSelectAllState();
        }

        // Toggle all workers
        function toggleAllWorkers(selectAllCheckbox) {
            const checkboxes = document.querySelectorAll('.worker-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateWorkersCount();
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

        // Load team supervisor when team is selected
        // تحديث الأوقات عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            updateShiftTimes();
            updateWorkersCount();
            // Load stage records if stage is selected
            if (document.getElementById('stage_number').value) {
                loadStageRecords();
            }
        });

        // Store available barcodes globally
        let availableBarcodes = [];

        // Load stage records (barcodes) based on stage selection
        async function loadStageRecords() {
            const stageNumber = document.getElementById('stage_number').value;
            const barcodeInput = document.getElementById('stage_record_barcode');
            const countLabel = document.getElementById('stage_records_count');
            const dataList = document.getElementById('barcodeList');

            // Clear and disable if no stage selected
            if (!stageNumber || stageNumber === '0') {
                barcodeInput.disabled = true;
                barcodeInput.value = '';
                barcodeInput.placeholder = '-- اختر المرحلة أولاً ثم ادخل أو اختر الباركود --';
                countLabel.textContent = '';
                document.getElementById('stage_record_id').value = '';
                dataList.innerHTML = '';
                availableBarcodes = [];
                return;
            }

            try {
                // Show loading state
                barcodeInput.disabled = true;
                barcodeInput.placeholder = 'جاري التحميل...';
                countLabel.textContent = '';

                const response = await fetch('{{ route("manufacturing.shifts-workers.get-stage-records") }}?stage_number=' + stageNumber, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success && data.records && data.records.length > 0) {
                    // Store barcodes for filtering
                    availableBarcodes = data.records.map(r => ({
                        barcode: r.barcode,
                        id: r.id
                    }));

                    // Populate datalist
                    dataList.innerHTML = availableBarcodes.map(item =>
                        `<option value="${item.barcode}">ID: ${item.id} - ${item.barcode}</option>`
                    ).join('');

                    barcodeInput.disabled = false;
                    barcodeInput.placeholder = 'ادخل أو اختر الباركود من القائمة';
                    countLabel.textContent = `عدد السجلات: ${data.count}`;
                } else {
                    barcodeInput.disabled = true;
                    barcodeInput.placeholder = '-- لا توجد سجلات في هذه المرحلة --';
                    countLabel.textContent = 'لا توجد سجلات متاحة';
                    dataList.innerHTML = '';
                    availableBarcodes = [];
                }
            } catch (error) {
                console.error('Error loading stage records:', error);
                barcodeInput.disabled = true;
                barcodeInput.placeholder = '-- خطأ في التحميل --';
                countLabel.textContent = 'حدث خطأ: ' + error.message;
                dataList.innerHTML = '';
            }
        }

        // Filter barcodes as user types
        function filterBarcodes() {
            const input = document.getElementById('stage_record_barcode');
            const dropdown = document.getElementById('barcodeDropdown');
            const value = input.value.toLowerCase().trim();

            if (!value || value.length < 1) {
                dropdown.style.display = 'none';
                return;
            }

            const filtered = availableBarcodes.filter(item =>
                item.barcode.toLowerCase().includes(value) ||
                item.id.toString().includes(value)
            );

            if (filtered.length > 0) {
                dropdown.innerHTML = filtered.map(item =>
                    `<div class="barcode-option" onclick="selectBarcode('${item.barcode}', ${item.id})">
                        <strong>${item.barcode}</strong>
                        <span class="barcode-id">ID: ${item.id}</span>
                    </div>`
                ).join('');
                dropdown.style.display = 'block';
            } else {
                dropdown.innerHTML = '<div class="barcode-option disabled">لا توجد نتائج</div>';
                dropdown.style.display = 'block';
            }
        }

        // Select a barcode from dropdown
        function selectBarcode(barcode, recordId) {
            document.getElementById('stage_record_barcode').value = barcode;
            document.getElementById('stage_record_id').value = recordId;
            document.getElementById('barcodeDropdown').style.display = 'none';
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const input = document.getElementById('stage_record_barcode');
            const dropdown = document.getElementById('barcodeDropdown');
            if (!input || !dropdown) return;

            if (!input.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>

@endsection
